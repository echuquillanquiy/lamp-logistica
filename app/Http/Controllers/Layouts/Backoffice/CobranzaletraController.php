<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class CobranzaletraController extends Controller
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
        $where[] = ['cobranzaletra.monto','LIKE','%'.$request->input('monto').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        if($request->input('ventacodigo')!=''){$where[] = ['venta.codigo',$request->input('ventacodigo')];}
       
        $cobranzaletras  = DB::table('cobranzaletra')
            ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
            ->join('venta','venta.id','tipopagoletra.idventa')
            ->join('users as responsable','responsable.id','cobranzaletra.idusuario')
            ->join('moneda','moneda.id','cobranzaletra.idmoneda')
            ->leftJoin('aperturacierre','aperturacierre.id','cobranzaletra.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->leftJoin('tienda','tienda.id','caja.idtienda')
            ->where('tienda.id',usersmaster()->idtienda)
            ->where($where)
            ->select(
                'cobranzaletra.*',
                'tipopagoletra.numeroletra as numeroletra',
                'responsable.nombre as responsablenombre',
                'venta.codigo as ventacodigo',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('cobranzaletra.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/cobranzaletra/index',[
            'tienda' => $tienda,
            'cobranzaletras' => $cobranzaletras,
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
        return view('layouts/backoffice/cobranzaletra/create',[
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
            if($request->input('validletra') > 0){
                if($request->input('selectletras')==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El N° Unico es obligatorio!.'
                    ]);
                }
                $selectletras = explode('/&/', $request->input('selectletras'));
                for($i = 1; $i < count($selectletras); $i++){
                    $item = explode('/,/',$selectletras[$i]);
                    if($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El N° Unico esta vacio, ingrese un dato por favor!.'
                        ]);
                    }
                }  
                for($i = 1; $i < count($selectletras); $i++){
                    $item = explode('/,/',$selectletras[$i]);
                    DB::table('tipopagoletra')->whereId($item[0])->update([
                      'numerounico' => $item[1]
                    ]);
                }   
                return response()->json([
                    'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha registrado correctamente.'
                ]);
            }else{
                $rules = [
                    'idventa' => 'required',
                    'monto' => 'required',
                ];
                $messages = [
                    'idventa.required'   => 'La "Venta" es Obligatorio.',
                    'monto.required'   => 'El "Monto" es Obligatorio.',
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
                $venta = DB::table('venta')
                    ->join('moneda','moneda.id','venta.idmoneda')
                    ->where('venta.id',$request->input('idventa'))
                    ->select(
                        'venta.*',
                        'moneda.simbolo as monedasimbolo'
                    )
                    ->first();
                // fin deuda restante
              
                // Apertura de caja
                $idaperturacierre = 0;
                $aperturacierre = aperturacierre(usersmaster()->idtienda, Auth::user()->id);
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
                $ultimocobranzaletra = DB::table('cobranzaletra')
                        ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                        ->where('tipopagoletra.idventa',$request->input('idventa'))
                        ->where('cobranzaletra.idestado',2)
                        ->select('tipopagoletra.numero')
                        ->orderBy('tipopagoletra.numero','desc')
                        ->limit(1)
                        ->first();

                if($ultimocobranzaletra!=''){
                    $ultimopagoletra = DB::table('tipopagoletra')
                        ->where('tipopagoletra.idventa',$request->input('idventa'))
                        ->where('tipopagoletra.numero',$ultimocobranzaletra->numero+1)
                        ->select('tipopagoletra.*')
                        ->limit(1)
                        ->first();
                }else{
                    $ultimopagoletra = DB::table('tipopagoletra')
                        ->where('tipopagoletra.idventa',$request->input('idventa'))
                        ->select('tipopagoletra.*')
                        ->orderBy('tipopagoletra.numero','asc')
                        ->limit(1)
                        ->first();
                }

                if($ultimopagoletra==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay Letra pendiente de esta venta!!.'
                    ]);
                }
                // fin letra de pago

                if($request->input('monto')!=$ultimopagoletra->monto){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto debe ser igual a la letra a pagar!.'
                    ]);
                }
              
                $cobranzaletra = DB::table('cobranzaletra')
                    ->orderBy('cobranzaletra.codigo','desc')
                    ->limit(1)
                    ->first();
                $codigo = 1;
                if($cobranzaletra!=''){
                    $codigo = $cobranzaletra->codigo+1;
                }

                $idcobranzaletra = DB::table('cobranzaletra')->insertGetId([
                    'fecharegistro'=> Carbon::now(),         
                    'codigo'=> $codigo,                 
                    'monto'=> $request->input('monto'),      
                    'idformapago'=> $request->input('idformapago'),
                    'idmoneda'=> $venta->idmoneda,
                    'idtipopagoletra'=> $ultimopagoletra->id,
                    'idusuario'=> Auth::user()->id,
                    'idaperturacierre'=> $idaperturacierre,
                    'idestado'=> 1
                ]);

                //forma de pago
                DB::table('tipopagodetalle')->where('idcobranzaletra',$idcobranzaletra)->delete();
                formapago_insertar(
                    $request,
                    'cobranzaletra',
                    $idcobranzaletra
                );
              
                return response()->json([
                    'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha registrado correctamente.'
                ]);
            }
                
        }
    }

    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       if($id == 'show-listarventacliente'){
            $ventas = DB::table('venta')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->where('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',3)
                ->where('venta.codigo',$request->input('buscar'))
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',3)
                ->where('usuariocliente.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',3)
                ->where('usuariocliente.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',3)
                ->where('usuariocliente.identificacion','LIKE','%'.$request->input('buscar').'%')
              
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',5)
                ->where('venta.codigo',$request->input('buscar'))
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',5)
                ->where('usuariocliente.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',5)
                ->where('usuariocliente.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('venta.idformapago',3)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('venta.idestado',5)
                ->where('usuariocliente.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'venta.id as id',
                    DB::raw('CONCAT("(",LPAD(venta.codigo,6,0),") ",IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre))) as text')
                )
                ->orderBy('venta.codigo','desc')
                ->get();
          
            return $ventas;
        }elseif($id == 'show-seleccionarventacliente'){
            $venta = DB::table('venta')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('venta.id',$request->input('idventa'))
                ->select(
                    'venta.*',
                    'moneda.simbolo as monedasimbolo',
                    'moneda.nombre as monedanombre'
                )
                ->first();
   
            $montopagado = DB::table('cobranzaletra')
                ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                ->where('tipopagoletra.idventa',$venta->id)
                ->where('cobranzaletra.idestado',2)
                ->sum('cobranzaletra.monto');
            $notadevolucions = DB::table('notadevolucion')
                ->where('notadevolucion.idventa',$venta->id)
                ->sum('total');
         
            $deudarestante = $venta->montorecibido-$montopagado-$notadevolucions;
         
            // ultima letra de pago
            $ultimocobranzaletra = DB::table('cobranzaletra')
                    ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->where('cobranzaletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            $cuotaapagar = '---';
            $montoapagar = '0.00';
            if($ultimocobranzaletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->where('tipopagoletra.numero',$ultimocobranzaletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
                if($ultimopagoletra!=''){
                    $cuotaapagar = $ultimopagoletra->numeroletra;
                    $montoapagar = $ultimopagoletra->monto;
                }
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
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
          
            $tipopagoletras_vacio = DB::table('tipopagoletra')
                ->where('tipopagoletra.idventa',$request->input('idventa'))
                ->where('tipopagoletra.numerounico','')
                ->count();
         
            $tipopagoletras = DB::table('tipopagoletra')
                ->where('tipopagoletra.idventa',$request->input('idventa'))
                ->get();
          
            $cronogramaletra = '';
            foreach($tipopagoletras as $value){
              
                $cobranzaletra = DB::table('cobranzaletra')
                    ->where('cobranzaletra.idtipopagoletra',$value->id)
                    ->where('cobranzaletra.idestado',2)
                    ->select('cobranzaletra.fechaconfirmacion')
                    ->first();
              
                $fechapago = '---';
                if($cobranzaletra!=''){
                    $fechapago = $cobranzaletra->fechaconfirmacion;
                }
              
                $numerounico = '<td>'.$value->numerounico.'</td>';
                if($tipopagoletras_vacio>0){
                    $numerounico = '<td class="with-form-control"><input type="text" id="numerounico'.$value->id.'" class="form-control"/></td>';
                }
                $cronogramaletra = $cronogramaletra.'<tr idtipopagoletra="'.$value->id.'">
                  <td>'.$value->numero.'</td>
                  <td>'.$value->numeroletra.'</td>
                  '.$numerounico.'
                  <td>'.$value->fecha.'</td>
                  <td>'.$value->monto.'</td>
                  <td>'.$fechapago.'</td>
                </tr>';
            }

            return [ 
              'deudarestante' => $venta->monedasimbolo.' '.number_format($deudarestante, 2, '.', ''),
              'fechainiciopago' => date_format(date_create($venta->fp_letra_fechainicio),"d/m/Y"),
              'cuotaapagar' => $cuotaapagar,
              'moneda' => $venta->monedanombre,
              'montoapagar' => $montoapagar,
              'cronogramaletra' => $cronogramaletra,
              'validletra' => $tipopagoletras_vacio
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
    public function edit(Request $request, $idcobranzaletra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $cobranzaletra  = DB::table('cobranzaletra')
            ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
            ->join('venta','venta.id','tipopagoletra.idventa')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('users as responsable','responsable.id','cobranzaletra.idusuario')
            ->join('moneda','moneda.id','cobranzaletra.idmoneda')
            ->leftJoin('aperturacierre','aperturacierre.id','cobranzaletra.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->where('cobranzaletra.id',$idcobranzaletra)
            ->select(
                'cobranzaletra.*',
                'responsable.nombre as responsablenombre',
                'moneda.simbolo as monedasimbolo',
                'venta.id as idventa',
                 DB::raw('CONCAT("(",LPAD(venta.codigo,6,0),") ",IF(usuariocliente.idtipopersona=1,
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre))) as venta')
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            $tipopagos = DB::table('tipopago')->get();
            return view('layouts/backoffice/cobranzaletra/edit',[
                'cobranzaletra' => $cobranzaletra,
                'tipopagos' => $tipopagos,
            ]);
        }
        elseif($request->input('view') == 'confirmar') {
            $tipopagos = DB::table('tipopago')->get();
            return view('layouts/backoffice/cobranzaletra/confirmar',[
                'cobranzaletra' => $cobranzaletra,
                'tipopagos' => $tipopagos,
            ]);
        }
        elseif($request->input('view') == 'detalle') {
            $tipopagos = DB::table('tipopago')->get();
            return view('layouts/backoffice/cobranzaletra/detalle',[
                'cobranzaletra' => $cobranzaletra,
                'tipopagos' => $tipopagos,
            ]);
        } elseif($request->input('view') == 'anular') {
            $tipopagos = DB::table('tipopago')->get();
            return view('layouts/backoffice/cobranzaletra/anular',[
                'cobranzaletra' => $cobranzaletra,
                'tipopagos' => $tipopagos,
            ]);
        }
        elseif($request->input('view') == 'letra') {
            return view('layouts/backoffice/cobranzaletra/letra');
        }
        elseif($request->input('view') == 'letra-pdf') {          
            $pdf = PDF::loadView('layouts/backoffice/cobranzaletra/letra-pdf');
            return $pdf->stream();          
        }
        elseif($request->input('view') == 'eliminar') {
            $tipopagos = DB::table('tipopago')->get();
            return view('layouts/backoffice/cobranzaletra/delete',[
                'cobranzaletra' => $cobranzaletra,
                'tipopagos' => $tipopagos,
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
    public function update(Request $request, $idcobranzaletra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'editar') {
                $rules = [
                    'idventa' => 'required',
                    'monto' => 'required',
                ];
                $messages = [
                    'idventa.required'   => 'La "Venta" es Obligatorio.',
                    'monto.required'   => 'El "Monto" es Obligatorio.',
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
            $venta = DB::table('venta')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('venta.id',$request->input('idventa'))
                ->select(
                    'venta.*',
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
            $deudatotal = 0;
            $montopagado = 0;
            if($venta!=''){
                $deudatotal = DB::table('ventadetalle')
                    ->where('ventadetalle.idventa',$venta->id)
                    ->sum(DB::raw('CONCAT((ventadetalle.cantidad*ventadetalle.preciounitario)-ventadetalle.descuento)'));
              
                $montopagado = DB::table('cobranzaletra')
                    ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                    ->where('tipopagoletra.idventa',$venta->id)
                    ->where('cobranzaletra.idestado',2)
                    ->sum('cobranzaletra.monto');
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
            $ultimocobranzaletra = DB::table('cobranzaletra')
                    ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->where('cobranzaletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            if($ultimocobranzaletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->where('tipopagoletra.numero',$ultimocobranzaletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->select('tipopagoletra.*')
                    ->orderBy('tipopagoletra.numero','asc')
                    ->limit(1)
                    ->first();
            }
            
            if($ultimopagoletra==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Letra pendiente de esta venta!!.'
                ]);
            }
            // fin letra de pago
          
            if($request->input('monto')!=$ultimopagoletra->monto){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto debe ser igual al monto a pagar!.'
                ]);
            }

            DB::table('cobranzaletra')->whereId($idcobranzaletra)->update([ 
                'monto'=> $request->input('monto'),  
                'idformapago'=> $request->input('idformapago'), 
                'idmoneda'=> $venta->idmoneda,
                'idusuario'=> Auth::user()->id
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idcobranzaletra',$idcobranzaletra)->delete();
            formapago_insertar(
                $request,
                'cobranzaletra',
                $idcobranzaletra
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
            $ultimocobranzaletra = DB::table('cobranzaletra')
                    ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->where('cobranzaletra.idestado',2)
                    ->select('tipopagoletra.numero')
                    ->orderBy('tipopagoletra.numero','desc')
                    ->limit(1)
                    ->first();
         
            if($ultimocobranzaletra!=''){
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->where('tipopagoletra.numero',$ultimocobranzaletra->numero+1)
                    ->select('tipopagoletra.*')
                    ->limit(1)
                    ->first();
            }else{
                $ultimopagoletra = DB::table('tipopagoletra')
                    ->where('tipopagoletra.idventa',$request->input('idventa'))
                    ->select('tipopagoletra.*')
                    ->orderBy('tipopagoletra.numero','asc')
                    ->limit(1)
                    ->first();
            }
            if($ultimopagoletra==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Letra pendiente de esta venta!!.'
                ]);
            }
            // fin letra de pago

            DB::table('cobranzaletra')->whereId($idcobranzaletra)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                'idtipopagoletra'=> $ultimopagoletra->id,
                'idusuario'=> Auth::user()->id,
                'idaperturacierre'=> $idaperturacierre,
                'idestado'=> 2
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idcobranzaletra',$idcobranzaletra)->delete();
            formapago_insertar(
                $request,
                'cobranzaletra',
                $idcobranzaletra
            );

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        } elseif($request->input('view') == 'anular') {

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
            $cobranzaletra = DB::table('cobranzaletra')->whereId($idcobranzaletra)->first();
     
            if($cobranzaletra->idaperturacierre != $idapertura){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular.'
                ]);
            }
          
            DB::table('cobranzaletra')->whereId($idcobranzaletra)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                'idestado'=> 3
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
    public function destroy(Request $request, $idcobranzaletra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idcobranzaletra',$idcobranzaletra)->delete();
            DB::table('cobranzaletra')->whereId($idcobranzaletra)->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
