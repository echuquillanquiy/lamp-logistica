<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CobranzacreditoController extends Controller
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
            $where[] = ['cobranzacredito.fechaconfirmacion','LIKE','%'.$request->input('fechaconfirmacion').'%'];
        }
        $where[] = ['cobranzacredito.monto','LIKE','%'.$request->input('monto').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        if($request->input('ventacodigo')!=''){$where[] = ['venta.codigo',$request->input('ventacodigo')];}
       
        $cobranzacreditos  = DB::table('cobranzacredito')
            ->join('venta','venta.id','cobranzacredito.idventa')
            ->join('users as responsable','responsable.id','cobranzacredito.idusuario')
            ->join('moneda','moneda.id','cobranzacredito.idmoneda')
            ->where('cobranzacredito.idtienda',usersmaster()->idtienda)
            ->where($where)
            ->select(
                'cobranzacredito.*',
                'responsable.nombre as responsablenombre',
                'venta.codigo as ventacodigo',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('cobranzacredito.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/cobranzacredito/index',[
            'tienda' => $tienda,
            'cobranzacreditos' => $cobranzacreditos,
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
        return view('layouts/backoffice/cobranzacredito/create',[
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
                'idventa' => 'required',
            ];
          
            $messages = [
                'idventa.required'   => 'La "Venta" es Obligatorio.',
            ];
          
            $formapago_validar = formapago_validar('',$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
          
            // deuda restante
          
            $venta = DB::table('venta')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('venta.id',$request->input('idventa'))
                ->select(
                    'venta.*',
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
            $totalnotadevolucion = DB::table('notadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$request->input('idventa'))
                 ->sum('total');
              
             $totalapagado = DB::table('notadevoluciondetalle')
                 ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$request->input('idventa'))
                 ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('cobranzacredito')
                 ->where('idestado',2)
                 ->where('idventa',$request->input('idventa'))
                 ->sum('monto');
             $deudarestante = $venta->montorecibido-$totalpagado-$totalapagado+$totalnotadevolucion;
          
            if($formapago_validar['total']>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante
         
            $cobranzacredito = DB::table('cobranzacredito')
                ->orderBy('cobranzacredito.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($cobranzacredito!=''){
                $codigo = $cobranzacredito->codigo+1;
            }

            $idcobranzacredito = DB::table('cobranzacredito')->insertGetId([
                'fecharegistro'=> Carbon::now(),        
                'codigo'=> $codigo,              
                'monto'=> $formapago_validar['total'],     
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $venta->idmoneda,
                'idventa'=> $request->input('idventa'),
                'idusuario'=> Auth::user()->id,
                'idaperturacierre'=> 0,
                'idtienda'=> usersmaster()->idtienda,
                'idestado'=> 1
            ]);
          
            //forma de pago
            formapago_insertar(
                $request,
                'cobranzacredito',
                $idcobranzacredito
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
      
       if($id == 'show-listarventacliente'){
            $ventas = DB::table('venta')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->where('venta.idformapago',2)
                ->where('venta.idestado',3)
                ->where('venta.codigo',$request->input('buscar'))
                ->orWhere('venta.idformapago',2)
                ->where('venta.idestado',3)
                ->where('usuariocliente.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('venta.idformapago',2)
                ->where('venta.idestado',3)
                ->where('usuariocliente.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('venta.idformapago',2)
                ->where('venta.idestado',3)
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
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
         
            $totalnotadevolucion = DB::table('notadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$venta->id)
                 ->sum('total');
              
             $totalapagado = DB::table('notadevoluciondetalle')
                 ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$venta->id)
                 ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('cobranzacredito')
                 ->where('idestado',2)
                 ->where('idventa',$venta->id)
                 ->sum('monto');
             $deudarestante = $venta->montorecibido-$totalpagado-$totalapagado+$totalnotadevolucion;
         

            return [ 
              'idmoneda' => $venta->idmoneda,
              'deudarestante' => $venta->monedasimbolo.' '.number_format($deudarestante, 2, '.', ''),
              'ultimafechapago' => date_format(date_create($venta->fp_credito_ultimafecha),"d/m/Y")
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
    public function edit(Request $request, $idcobranzacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $cobranzacredito  = DB::table('cobranzacredito')
            ->join('venta','venta.id','cobranzacredito.idventa')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('users as responsable','responsable.id','cobranzacredito.idusuario')
            ->join('moneda','moneda.id','cobranzacredito.idmoneda')
            ->leftJoin('aperturacierre','aperturacierre.id','cobranzacredito.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->where('cobranzacredito.id',$idcobranzacredito)
            ->select(
                'cobranzacredito.*',
                'responsable.nombre as responsablenombre',
                'moneda.simbolo as monedasimbolo',
                 DB::raw('CONCAT("(",LPAD(venta.codigo,6,0),") ",IF(usuariocliente.idtipopersona=1,
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                 CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos))) as venta')
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/cobranzacredito/edit',[
                'cobranzacredito' => $cobranzacredito,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'confirmar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/cobranzacredito/confirmar',[
                'cobranzacredito' => $cobranzacredito,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'detalle') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/cobranzacredito/detalle',[
                'cobranzacredito' => $cobranzacredito,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'anular') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/cobranzacredito/anular',[
                'cobranzacredito' => $cobranzacredito,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/cobranzacredito/delete',[
                'cobranzacredito' => $cobranzacredito,
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
    public function update(Request $request, $idcobranzacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idventa' => 'required',
            ];
          
            $messages = [
                'idventa.required'   => 'La "Venta" es Obligatorio.',
            ];
          
            $formapago_validar = formapago_validar('',$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

          
            // deuda restante
          
            $venta = DB::table('venta')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('venta.id',$request->input('idventa'))
                ->select(
                    'venta.*',
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
            $totalnotadevolucion = DB::table('notadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$request->input('idventa'))
                 ->sum('total');
              
             $totalapagado = DB::table('notadevoluciondetalle')
                 ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$request->input('idventa'))
                 ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('cobranzacredito')
                 ->where('idestado',2)
                 ->where('idventa',$request->input('idventa'))
                 ->sum('monto');
             $deudarestante = $venta->montorecibido-$totalpagado-$totalapagado+$totalnotadevolucion;
          
            if($formapago_validar['total']>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante

            DB::table('cobranzacredito')->whereId($idcobranzacredito)->update([ 
                'monto'=> $formapago_validar['total'],     
                'idformapago'=> $request->input('idformapago'),
                'idmoneda'=> $venta->idmoneda,
                'idtienda'=> usersmaster()->idtienda,
                'idusuario'=> Auth::user()->id
            ]);

            //forma de pago
            DB::table('tipopagodetalle')->where('idcobranzacredito',$idcobranzacredito)->delete();
            formapago_insertar(
                $request,
                'cobranzacredito',
                $idcobranzacredito
            );
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') {

            $formapago_validar = formapago_validar('',$request,[],[],1);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
          
            // deuda restante
          
            $venta = DB::table('venta')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('venta.id',$request->input('idventa'))
                ->select(
                    'venta.*',
                    'moneda.simbolo as monedasimbolo'
                )
                ->first();
          
            $totalnotadevolucion = DB::table('notadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$request->input('idventa'))
                 ->sum('total');
              
             $totalapagado = DB::table('notadevoluciondetalle')
                 ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                 ->where('notadevolucion.idestado',2)
                 ->where('notadevolucion.idventa',$request->input('idventa'))
                 ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
              
             $totalpagado = DB::table('cobranzacredito')
                 ->where('idestado',2)
                 ->where('idventa',$request->input('idventa'))
                 ->sum('monto');
             $deudarestante = $venta->montorecibido-$totalpagado-$totalapagado+$totalnotadevolucion;
          
            if($formapago_validar['total']>$deudarestante){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El monto excede a la deuda!.'
                ]);
            }
            // fin deuda restante
          
            DB::table('cobranzacredito')->whereId($idcobranzacredito)->update([     
                'fechaconfirmacion'=> Carbon::now(),
                'idaperturacierre'=> $formapago_validar['idaperturacierre'],
                'idtienda'=> usersmaster()->idtienda,
                'idestado'=> 2
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idcobranzacredito',$idcobranzacredito)->delete();
            formapago_insertar(
                $request,
                'cobranzacredito',
                $idcobranzacredito
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
          
            $cobranzacredito = DB::table('cobranzacredito')->whereId($idcobranzacredito)->first();
          
            if($cobranzacredito->idaperturacierre != $idapertura){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular.'
                ]);
            }

            DB::table('cobranzacredito')->whereId($idcobranzacredito)->update([     
                'fechaanulacion'   => Carbon::now(),
                'idestado'         => 3
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
    public function destroy(Request $request, $idcobranzacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idcobranzacredito',$idcobranzacredito)->delete();
            DB::table('cobranzacredito')->whereId($idcobranzacredito)->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
