<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PagoletraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $tienda = DB::table('tienda')->whereId(1)->first();

        $where = [];
        $where[] = ['pagoletra.monto','LIKE','%'.$request->input('monto').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        if($request->input('compracodigo')!=''){$where[] = ['compra.codigo',$request->input('compracodigo')];}
       
        $pagoletras  = DB::table('pagoletra')
            ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
            ->join('compra','compra.id','tipopagoletra.idcompra')
            ->join('users as responsable','responsable.id','pagoletra.idusuario')
            ->join('moneda','moneda.id','pagoletra.idmoneda')
            ->where('pagoletra.idtienda',usersmaster()->idtienda)
            ->where($where)
            ->select(
                'pagoletra.*',
                'tipopagoletra.numeroletra as numeroletra',
                'responsable.nombre as responsablenombre',
                'compra.codigo as compracodigo',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('pagoletra.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/pagoletra/index',[
            'tienda' => $tienda,
            'pagoletras' => $pagoletras,
            'idapertura' => aperturacierre(usersmaster()->idtienda,Auth::user()->id)['idapertura']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $tipopagos = DB::table('tipopago')->get();
        return view('layouts/backoffice/pagoletra/create',[
            'tipopagos' => $tipopagos,
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
   
        if($request->input('view') == 'registrar') {
            $rules = [
                'idcompra' => 'required',
            ];
          
            $messages = [
                'idcompra.required'   => 'La "Venta" es Obligatorio.',
            ];

            $formapago_validar = formapago_validar($request->input('cliente_montoapagar'),$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
          
            // deuda restante
            $compra = DB::table('compra')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->where('compra.id',$request->input('idcompra'))
                ->select(
                    'compra.*',
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
            $montopagado = DB::table('pagoletra')
                ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                ->where('tipopagoletra.idcompra',$compra->id)
                ->where('pagoletra.idestado',2)
                ->sum('pagoletra.monto');
         
            $deudarestante = $compra->monto-$montopagado;
            if($request->input('monto')>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante
          
            // ultima letra de pago
            $ultimopagoletra = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('pagoletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            if($ultimopagoletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('tipopagoletra.numero',$ultimopagoletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->select('tipopagoletra.*')
                    ->orderBy('tipopagoletra.numero','asc')
                    ->limit(1)
                    ->first();
            }
            
            if($ultimopagoletra==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Letra pendiente de esta compra!!.'
                ]);
            }
            // fin letra de pago
          
            if($formapago_validar['total']!=$ultimopagoletra->monto){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto debe ser igual al monto a pagar!.'
                ]);
            }
          
            $pagocredito = DB::table('pagocredito')
                ->orderBy('pagocredito.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($pagocredito!=''){
                $codigo = $pagocredito->codigo+1;
            }

            $idpagoletra = DB::table('pagoletra')->insertGetId([
                'fecharegistro'=> Carbon::now(),             
                'codigo'=> $codigo,                
                'monto'=> $formapago_validar['total'], 
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $compra->idmoneda,
                'idtipopagoletra'=> $ultimopagoletra->id,
                'idusuario'=> Auth::user()->id,
                'idaperturacierre'=> 0,
                'idtienda' =>  usersmaster()->idtienda,
                'idestado'=> 1
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idpagoletra',$idpagoletra)->delete();
            formapago_insertar(
                $request,
                'pagoletra',
                $idpagoletra
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       if($id == 'show-listarcompracliente'){
            $compras = DB::table('compra')
                ->join('users as usuariocliente','usuariocliente.id','compra.idusuarioproveedor')
                ->where('compra.idformapago',3)
                ->where('compra.idestado',2)
                ->where('compra.codigo',$request->input('buscar'))
                ->orWhere('compra.idformapago',3)
                ->where('compra.idestado',2)
                ->where('usuariocliente.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('compra.idformapago',3)
                ->where('compra.idestado',2)
                ->where('usuariocliente.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('compra.idformapago',3)
                ->where('compra.idestado',2)
                ->where('usuariocliente.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'compra.id as id',
                    DB::raw('CONCAT("(",LPAD(compra.codigo,8,0),") ",IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre))) as text')
                )
                ->orderBy('compra.codigo','desc')
                ->get();
          
            return $compras;
        }elseif($id == 'show-seleccionarcompracliente'){
            $compra = DB::table('compra')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->where('compra.id',$request->input('idcompra'))
                ->select(
                    'compra.*',
                    'moneda.simbolo as monedasimbolo',
                    'moneda.nombre as monedanombre'
                )
                ->first();
          
            $montopagado = DB::table('pagoletra')
                ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                ->where('tipopagoletra.idcompra',$compra->id)
                ->where('pagoletra.idestado',2)
                ->sum('pagoletra.monto');

            $deudarestante = $compra->monto-$montopagado;
         
            // ultima letra de pago
            $ultimopagoletra = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('pagoletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            $cuotaapagar = '---';
            $montoapagar = '0.00';
            if($ultimopagoletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('tipopagoletra.numero',$ultimopagoletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
                if($ultimopagoletra!=''){
                    $cuotaapagar = $ultimopagoletra->numeroletra;
                    $montoapagar = $ultimopagoletra->monto;
                }
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->select('tipopagoletra.*')
                    ->orderBy('tipopagoletra.numero','asc')
                    ->limit(1)
                    ->first();
                if($ultimopagoletra!=''){
                    $cuotaapagar = $ultimopagoletra->numeroletra;
                    $montoapagar = $ultimopagoletra->monto;
                }
            }
            // fin letra de pago
          
            $tipopagoletras = DB::table('tipopagoletra')
                ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                ->get();
          
            $cronogramaletra = '';
            foreach($tipopagoletras as $value){
              
                $pagoletra = DB::table('pagoletra')
                    ->where('pagoletra.idtipopagoletra',$value->id)
                    ->where('pagoletra.idestado',2)
                    ->select('pagoletra.fechaconfirmacion')
                    ->first();
              
                $fechapago = '---';
                if($pagoletra!=''){
                    $fechapago = $pagoletra->fechaconfirmacion;
                }
                $cronogramaletra = $cronogramaletra.'<tr>
                  <td>'.$value->numero.'</td>
                  <td>'.$value->numeroletra.'</td>
                  <td>'.$value->numerounico.'</td>
                  <td>'.$value->fecha.'</td>
                  <td>'.$value->monto.'</td>
                  <td>'.$fechapago.'</td>
                </tr>';
            }

            return [ 
              'deudarestante' => $compra->monedasimbolo.' '.number_format($deudarestante, 2, '.', ''),
              'fechainiciopago' => date_format(date_create($compra->fp_letra_fechainicio),"d/m/Y"),
              'cuotaapagar' => $cuotaapagar,
              'moneda' => $compra->monedanombre,
              'montoapagar' => $montoapagar,
              'cronogramaletra' => $cronogramaletra
            ];
        }elseif($id == 'show-cuentabancaria'){
            $bancocuentabancaria = DB::table('bancocuentabancaria')
                ->where('bancocuentabancaria.idbanco',$request->input('idbanco'))
                ->first();
            $numerocuenta = '';
            if($bancocuentabancaria!=''){
                $numerocuenta = $bancocuentabancaria->numerocuenta;
            }
            return [ 
              'numerocuenta' => $numerocuenta
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idpagoletra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $pagoletra  = DB::table('pagoletra')
            ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
            ->join('compra','compra.id','tipopagoletra.idcompra')
            ->join('users as usuariocliente','usuariocliente.id','compra.idusuarioproveedor')
            ->join('users as responsable','responsable.id','pagoletra.idusuario')
            ->join('moneda','moneda.id','pagoletra.idmoneda')
            ->leftJoin('aperturacierre','aperturacierre.id','pagoletra.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->where('pagoletra.id',$idpagoletra)
            ->select(
                'pagoletra.*',
                'responsable.nombre as responsablenombre',
                'moneda.simbolo as monedasimbolo',
                'compra.id as idcompra',
                 DB::raw('CONCAT("(",LPAD(compra.codigo,6,0),") ",IF(usuariocliente.idtipopersona=1,
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos))) as compra')
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/pagoletra/edit',[
                'pagoletra' => $pagoletra,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'confirmar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/pagoletra/confirmar',[
                'pagoletra' => $pagoletra,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'anular') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/pagoletra/anular',[
                'pagoletra' => $pagoletra,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'detalle') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/pagoletra/detalle',[
                'pagoletra' => $pagoletra,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/pagoletra/delete',[
                'pagoletra' => $pagoletra,
                'monedas' => $monedas,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idpagoletra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idcompra' => 'required',
                'monto' => 'required',
            ];
          
            $messages = [
                'idcompra.required'   => 'La "Venta" es Obligatorio.',
                'monto.required'   => 'El "Monto" es Obligatorio.'
            ];
          
            $formapago_validar = formapago_validar($request->input('monto'),$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

            if($request->input('monto')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe ingresar un monto!.'
                ]);
            }
          
            // deuda restante
            $compra = DB::table('compra')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->where('compra.id',$request->input('idcompra'))
                ->select(
                    'compra.*',
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
            $deudatotal = 0;
            $montopagado = 0;
            if($compra!=''){
                $deudatotal = DB::table('compradetalle')
                    ->where('compradetalle.idcompra',$compra->id)
                    ->sum(DB::raw('CONCAT(compradetalle.cantidad*compradetalle.preciounitario)'));
              
                $montopagado = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('tipopagoletra.idcompra',$compra->id)
                    ->where('pagoletra.idestado',2)
                    ->sum('pagoletra.monto');
            }
         
            $deudarestante = $deudatotal-$montopagado;
            if($request->input('monto')>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante
          
            // ultima letra de pago
            $ultimopagoletra = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('pagoletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            if($ultimopagoletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('tipopagoletra.numero',$ultimopagoletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->select('tipopagoletra.*')
                    ->orderBy('tipopagoletra.numero','asc')
                    ->limit(1)
                    ->first();
            }
            
            if($ultimopagoletra==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Letra pendiente de esta compra!!.'
                ]);
            }
            // fin letra de pago
          
            if($request->input('monto')!=$ultimopagoletra->monto){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto debe ser igual al monto a pagar!.'
                ]);
            }
          

            DB::table('pagoletra')->whereId($idpagoletra)->update([ 
                'monto'=> $request->input('monto'),       
                'idmoneda'=> $compra->idmoneda,
                'idusuario'=> Auth::user()->id
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idpagoletra',$idpagoletra)->delete();
            formapago_insertar(
                $request,
                'pagoletra',
                $idpagoletra
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') {

            $usuario = usersmaster();
          
            // Apertura de caja
            $idaperturacierre = 0;
            $aperturacierre = aperturacierre($usuario->idtienda, Auth::user()->id);
            if($aperturacierre['apertura']!=''){
                if($aperturacierre['apertura']->idestado==3 && $aperturacierre['apertura']->idusersrecepcion==Auth::user()->id){
                }else{
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Caja debe estar Aperturada.'
                    ]);
                }
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay ninguna Caja Aperturada.'
                ]);
            }
            $idaperturacierre = $aperturacierre['apertura']->id;
            // Fin Apertura de caja
          
            // ultima letra de pago
            $ultimopagoletra = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('pagoletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            if($ultimopagoletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->where('tipopagoletra.numero',$ultimopagoletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idcompra',$request->input('idcompra'))
                    ->select('tipopagoletra.*')
                    ->orderBy('tipopagoletra.numero','asc')
                    ->limit(1)
                    ->first();
            }
            if($ultimopagoletra==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Letra pendiente de esta compra!!.'
                ]);
            }
            // fin letra de pago

            DB::table('pagoletra')->whereId($idpagoletra)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                'idtipopagoletra'=> $ultimopagoletra->id,
                'idusuario'=> Auth::user()->id,
                'idaperturacierre'=> $idaperturacierre,
                'idestado'=> 2
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idpagoletra',$idpagoletra)->delete();
            formapago_insertar(
                $request,
                'pagoletra',
                $idpagoletra
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }else if ($request->input('view') == 'anular') {
          // Apertura de caja
            $aperturacierre = aperturacierre(usersmaster()->idtienda,Auth::user()->id);
            if($aperturacierre['resultado']=='ERROR'){
                return response()->json([
                    'resultado' => $aperturacierre['resultado'],
                    'mensaje'   => $aperturacierre['mensaje']
                ]);
            }
            $idapertura = $aperturacierre['idapertura'];
            // Fin Apertura de caja
          
            $pagoletra = DB::table('pagoletra')->whereId($idpagoletra)->first();
          
            if($pagoletra->idaperturacierre != $idapertura){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular.'
                ]);
            }
        
            DB::table('pagoletra')->whereId($idpagoletra)->update([     
                'fechaanulacion'   => Carbon::now(),
                'idestado'         => 3
            ]);
          
            // tipo pago detalle
            DB::table('tipopagodetalle')->where('idpagoletra',$idpagoletra)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idpagoletra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idpagoletra',$idpagoletra)->delete();
            DB::table('pagoletra')->whereId($idpagoletra)->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
