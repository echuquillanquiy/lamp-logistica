<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PagocreditoController extends Controller
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
        if($request->input('fechaconfirmacion')!=''){
            $where[] = ['pagocredito.fechaconfirmacion','LIKE','%'.$request->input('fechaconfirmacion').'%'];
        }
        $where[] = ['pagocredito.monto','LIKE','%'.$request->input('monto').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        if($request->input('compracodigo')!=''){$where[] = ['compra.codigo',$request->input('compracodigo')];}
       
        $pagocreditos  = DB::table('pagocredito')
            ->join('compra','compra.id','pagocredito.idcompra')
            ->join('users as responsable','responsable.id','pagocredito.idusuario')
            ->join('moneda','moneda.id','pagocredito.idmoneda')
            ->where('pagocredito.idtienda',usersmaster()->idtienda)
            ->where($where)
            ->select(
                'pagocredito.*',
                'responsable.nombre as responsablenombre',
                'compra.codigo as compracodigo',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('pagocredito.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/pagocredito/index',[
            'tienda' => $tienda,
            'pagocreditos' => $pagocreditos,          
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
      
        $monedas = DB::table('moneda')->get();
        return view('layouts/backoffice/pagocredito/create',[
            'monedas' => $monedas,
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
          
            $formapago_validar = formapago_validar('',$request,$rules,$messages);
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
       
            $totalcompradevolucion = DB::table('compradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum('montorecibido');
              
             $totalapagado = DB::table('compradevoluciondetalle')
                 ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum(DB::raw('CONCAT(compradevoluciondetalle.cantidad*compradevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('pagocredito')
                 ->where('idestado',2)
                 ->where('idcompra',$compra->id)
                 ->sum('monto');
             $deudarestante = $compra->monto-$totalpagado-$totalapagado+$totalcompradevolucion;

            if($formapago_validar['total']>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante
          
            $pagocredito = DB::table('pagocredito')
                ->orderBy('pagocredito.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($pagocredito!=''){
                $codigo = $pagocredito->codigo+1;
            }

            $idpagocredito = DB::table('pagocredito')->insertGetId([
                'fecharegistro'=> Carbon::now(),           
                'codigo'=> $codigo,          
                'monto'=> $formapago_validar['total'],     
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $compra->idmoneda,
                'idcompra'=> $request->input('idcompra'),
                'idusuario'=> Auth::user()->id,
                'idaperturacierre'=> 0,
                'idtienda' =>  usersmaster()->idtienda,
                'idestado'=> 1
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idpagocredito',$idpagocredito)->delete();
            formapago_insertar(
                $request,
                'pagocredito',
                $idpagocredito
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
                ->orWhere('compra.idformapago',2)
                ->where('compra.idestado',2)
                ->where('compra.codigo',$request->input('buscar'))
              
                ->orWhere('compra.idformapago',2)
                ->where('compra.idestado',2)
                ->where('usuariocliente.nombre','LIKE','%'.$request->input('buscar').'%')
              
                ->orWhere('compra.idformapago',2)
                ->where('compra.idestado',2)
                ->where('usuariocliente.apellidos','LIKE','%'.$request->input('buscar').'%')
              
                ->orWhere('compra.idformapago',2)
                ->where('compra.idestado',2)
                ->where('usuariocliente.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'compra.id as id',
                    DB::raw('CONCAT("(",LPAD(compra.codigo,8,0),") ",IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos))) as text')
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
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
       
            $totalcompradevolucion = DB::table('compradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum('montorecibido');
              
             $totalapagado = DB::table('compradevoluciondetalle')
                 ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum(DB::raw('CONCAT(compradevoluciondetalle.cantidad*compradevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('pagocredito')
                 ->where('idestado',2)
                 ->where('idcompra',$compra->id)
                 ->sum('monto');
             $deudarestante = $compra->monto-$totalpagado-$totalapagado+$totalcompradevolucion;


            return [ 
              'idmoneda' => $compra->idmoneda,
              'deudarestante' => $compra->monedasimbolo.' '.number_format($deudarestante, 2, '.', ''),
              'ultimafechapago' => date_format(date_create($compra->fp_credito_ultimafecha),"d/m/Y")
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
    public function edit(Request $request, $idpagocredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $pagocredito  = DB::table('pagocredito')
            ->join('compra','compra.id','pagocredito.idcompra')
            ->join('users as usuariocliente','usuariocliente.id','compra.idusuarioproveedor')
            ->join('users as responsable','responsable.id','pagocredito.idusuario')
            ->join('moneda','moneda.id','pagocredito.idmoneda')
            ->leftJoin('aperturacierre','aperturacierre.id','pagocredito.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->where('pagocredito.id',$idpagocredito)
            ->select(
                'pagocredito.*',
                'responsable.nombre as responsablenombre',
                'moneda.simbolo as monedasimbolo',
                 DB::raw('CONCAT("(",LPAD(compra.codigo,6,0),") ",IF(usuariocliente.idtipopersona=1,
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre))) as compra')
            )
            ->first();
      
        $monedas = DB::table('moneda')->get();
      
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/pagocredito/edit',[
                'pagocredito' => $pagocredito,
                'monedas'     => $monedas,
            ]);
        }elseif($request->input('view') == 'confirmar') {
            return view('layouts/backoffice/pagocredito/confirmar',[
                'pagocredito' => $pagocredito,
                'monedas'     => $monedas,
            ]);
        }elseif($request->input('view') == 'anular') {
            return view('layouts/backoffice/pagocredito/anular',[
                'pagocredito' => $pagocredito,
                'monedas'     => $monedas,
            ]);
        }elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/pagocredito/detalle',[
                'pagocredito' => $pagocredito,
                'monedas'     => $monedas,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/pagocredito/delete',[
                'pagocredito' => $pagocredito,
                'monedas'     => $monedas,
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
    public function update(Request $request, $idpagocredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idcompra' => 'required',
            ];
            
            $messages = [
                'idcompra.required'   => 'La "Venta" es Obligatorio.',
            ];
          
            
            $formapago_validar = formapago_validar('',$request,$rules,$messages);
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
       
            $totalcompradevolucion = DB::table('compradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum('montorecibido');
              
             $totalapagado = DB::table('compradevoluciondetalle')
                 ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum(DB::raw('CONCAT(compradevoluciondetalle.cantidad*compradevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('pagocredito')
                 ->where('idestado',2)
                 ->where('idcompra',$compra->id)
                 ->sum('monto');
             $deudarestante = $compra->monto-$totalpagado-$totalapagado+$totalcompradevolucion;

            if($formapago_validar['total']>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante
          
            DB::table('pagocredito')->whereId($idpagocredito)->update([ 
                'monto'=> $formapago_validar['total'],     
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $compra->idmoneda,
                'idcompra'=> $request->input('idcompra'),
                'idusuario'=> Auth::user()->id,
                'idtienda' =>  usersmaster()->idtienda
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idpagocredito',$idpagocredito)->delete();
            formapago_insertar(
                $request,
                'pagocredito',
                $idpagocredito
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') {
          
            $formapago_validar = formapago_validar('',$request,[],[],2);
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
       
            $totalcompradevolucion = DB::table('compradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum('montorecibido');
              
             $totalapagado = DB::table('compradevoluciondetalle')
                 ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                 ->where('compradevolucion.idestado',2)
                 ->where('compradevolucion.idcompra',$compra->id)
                 ->sum(DB::raw('CONCAT(compradevoluciondetalle.cantidad*compradevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('pagocredito')
                 ->where('idestado',2)
                 ->where('idcompra',$compra->id)
                 ->sum('monto');
             $deudarestante = $compra->monto-$totalpagado-$totalapagado+$totalcompradevolucion;

            if($formapago_validar['total']>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante

            DB::table('pagocredito')->whereId($idpagocredito)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                'idaperturacierre'=> $formapago_validar['idaperturacierre'],
                'idestado'=> 2
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idpagocredito',$idpagocredito)->delete();
            formapago_insertar(
                $request,
                'pagocredito',
                $idpagocredito
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'anular') {
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
          
            $pagocredito = DB::table('pagocredito')->whereId($idpagocredito)->first();
          
            if($pagocredito->idaperturacierre != $idapertura){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular.'
                ]);
            }

            DB::table('pagocredito')->whereId($idpagocredito)->update([     
                'fechaanulacion'   => Carbon::now(),
                'idestado'         => 3
            ]);
          
            // tipo pago detalle
            DB::table('tipopagodetalle')->where('idpagocredito',$idpagocredito)->update([
                'fechaanulacion'  => Carbon::now(),
                'idestado'        => 3
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
    public function destroy(Request $request, $idpagocredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idpagocredito',$idpagocredito)->delete();
            DB::table('pagocredito')->whereId($idpagocredito)->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
