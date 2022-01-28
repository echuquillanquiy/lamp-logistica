<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail; 
use NumeroALetras;

class NotaDevolucionController extends Controller
{ 
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $where = [];
        if($request->input('codigoventa')!=''){$where[] = ['venta.codigo',$request->input('codigoventa')];}
        if($request->input('codigo')!=''){$where[] = ['notadevolucion.codigo',$request->input('codigo')];}
        $where[] = ['notadevolucion.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where[] = ['formapagoventa.nombre','LIKE','%'.$request->input('tipoventa').'%'];
      
        if(usersmaster()->id!=1){
          $where[] = ['notadevolucion.idtienda',usersmaster()->idtienda];
        }
        
        $notadevolucions = DB::table('notadevolucion')
            ->join('users as responsable','responsable.id','notadevolucion.idusuarioresponsable')
            ->join('venta','venta.id','notadevolucion.idventa')
            ->join('formapago as formapagoventa','formapagoventa.id','venta.idformapago')
            ->join('users as cliente','cliente.id','venta.idusuariocliente')
            ->join('moneda','moneda.id','notadevolucion.idmoneda')
            ->join('formapago','formapago.id','notadevolucion.idformapago')
            ->leftJoin('aperturacierre','aperturacierre.id','notadevolucion.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->leftJoin('tienda','tienda.id','venta.idtienda')
            ->where($where)
            ->select(
                'notadevolucion.*',
                'responsable.nombre as responsablenombre',
                DB::raw('IF(cliente.idtipopersona=1,
                CONCAT(cliente.identificacion," - ",cliente.apellidos,", ",cliente.nombre),
                CONCAT(cliente.identificacion," - ",cliente.nombre)) as cliente'),
                'venta.codigo as ventacodigo',
                'caja.nombre as cajanombre',
                'tienda.nombre as tiendanombre',
                'formapago.nombre as formapagonombre',
                'formapagoventa.nombre as formapagonombreventa',
                'venta.montorecibido as ventamontorecibido',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('notadevolucion.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/notadevolucion/index',[
            'notadevolucions' => $notadevolucions,
            'idapertura' => aperturacierre(usersmaster()->idtienda,Auth::user()->id)['idapertura']
        ]);
    }
  
    public function create(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/notadevolucion/create', [
              'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'notadevoluciondetalle') {
            $ventadetalles = DB::table('ventadetalle')
                ->join('producto','producto.id','ventadetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
                ->where('ventadetalle.idventa',$request->input('idventa'))
                ->select(
                    'ventadetalle.*',
                    'producto.codigoimpresion as codigoproducto',
                    'producto.nombreproducto as nombreproducto',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidad'
                )
                ->orderBy('ventadetalle.id','asc')
                ->get();
          
            $ventdel = [];
            foreach($ventadetalles as $value){
                $cantidad_notadevolucion = DB::table('notadevoluciondetalle')
                     ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                     ->where('notadevoluciondetalle.idventadetalle',$value->id)
                     ->where('notadevolucion.idestado',2)
                     ->sum('notadevoluciondetalle.cantidad');
              
                $cantidad_actual = $value->cantidad-$cantidad_notadevolucion;
                if($cantidad_actual>0){
                    $ventdel[] = [
                        'id' => $value->id,
                        'codigoproducto' => $value->codigoproducto,
                        'nombreproducto' => $value->nombreproducto,
//                         'productomarca' => $value->productomarca,
//                         'productomotor' => $value->productomotor,
//                         'productomodelo' => $value->productomodelo,
                        'unidad' => $value->unidad,
                        'cantidad' => $cantidad_actual,
                        'preciounitario' => $value->preciounitario,
                        'preciototal' => $value->preciototal
                    ];
                }  
            }
            
            return view('layouts/backoffice/notadevolucion/notadevoluciondetalle',[
                'ventadetalles' => $ventdel,
            ]);
        }
    }
  
    public function store(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idventa' => 'required',
                'motivodevolucion' => 'required',
                'productos' => 'required',
                'productos' => 'required',
            ];
            $messages = [
                'idventa.required' => 'Hay un error, intente otra vez por favor.',
                'motivodevolucion.required' => 'El "Motivo" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];

            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idventadetalle = explode('/-/', $item[0]);
                $cantidad = explode('/-/', $item[1]);
                if($cantidad[1]<=0){
                    $rules = array_merge($rules,[
                        $cantidad[0] => 'required|numeric|min:1',
                    ]);
                    $messages = array_merge($messages,[
                        $cantidad[0].'.required' => 'La cantidad es obligatorio.',
                        $cantidad[0].'.min' => 'La cantidad minímo es 1.',
                    ]);
                    break;
                }
              
                $ventadetalle = DB::table('ventadetalle')
                    ->where('ventadetalle.id',$idventadetalle[1])
                    ->first();
                $cantidad_notadevolucion = DB::table('notadevoluciondetalle')
                     ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                     ->where('notadevoluciondetalle.idventadetalle',$idventadetalle[1])
                     ->where('notadevolucion.idestado',2)
                     ->sum('notadevoluciondetalle.cantidad');
              
                $cantidad_actual = $ventadetalle->cantidad-$cantidad_notadevolucion;
              
                if($cantidad_actual<$cantidad[1]){
                    $rules = array_merge($rules,[
                        'error' => 'required',
                    ]);
                    $messages = array_merge($messages,[
                        'error.required' => 'No hay sifucientes productos, ingrese otra cantidad!!.',
                    ]);
                }
            }
     
            //if($request->input('totaladevolver')>=0){
            if($request->input('totaladevolver')>0){
                //$formapago_validar = formapago_validar($request->input('totalventa'),$request,$rules,$messages,2);
                $formapago_validar = formapago_validar($request->input('totaladevolver'),$request,$rules,$messages,2);
                $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
                $total = $formapago_validar['total'];
                $idaperturacierre = $formapago_validar['idaperturacierre'];
            }else{
                $this->validate($request,$rules,$messages);
                $idaperturacierre = 0;
                $aperturacierre = aperturacierre(usersmaster()->idtienda,Auth::user()->id);
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
                $total = 0;
            }
          
          
            $notadevolucion = DB::table('notadevolucion')
                ->select('notadevolucion.*')
                ->orderBy('notadevolucion.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($notadevolucion!=''){
                $codigo = $notadevolucion->codigo+1;
            }
          
            $venta = DB::table('venta')->whereId($request->input('idventa'))->first();
              
            $idnotadevolucion = DB::table('notadevolucion')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'fechaconfirmacion' => Carbon::now(),
                'codigo' => $codigo,
                'total' => $total,
                'motivo' => $request->input('motivodevolucion'),
                'idformapago'=> $request->input('idformapago'),
                'idmoneda' => $venta->idmoneda,
                'idventa' => $request->input('idventa'),
                'idtienda' => usersmaster()->idtienda,
                'idusuarioresponsable' => Auth::user()->id,
                'idaperturacierre' => $idaperturacierre,
                'idestado' => 2
            ]);
          
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idventadetalle = explode('/-/', $item[0]);
                $cantidad = explode('/-/', $item[1]);
                $preciounitario = explode('/-/', $item[2]);
                $ventadetalle = DB::table('ventadetalle')
                    ->where('ventadetalle.id',$idventadetalle[1])
                    ->first();
                DB::table('notadevoluciondetalle')->insert([
                    'cantidad' => $cantidad[1],
                    'preciounitario' => $preciounitario[1],
                    'idunidadmedida' => $ventadetalle->idunidadmedida,
                    'idproducto' => $ventadetalle->idproducto,
                    'idventadetalle' => $idventadetalle[1],
                    'idnotadevolucion' => $idnotadevolucion
               ]);
            }
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idnotadevolucion',$idnotadevolucion)->delete();
            formapago_insertar(
                $request,
                'notadevolucion',
                $idnotadevolucion
            );
          
            // Actualizar Stock
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->where('notadevoluciondetalle.idnotadevolucion',$idnotadevolucion)
                ->get();

            foreach($notadevoluciondetalles as $value){
                actualizar_stock(
                    'notadevolucion',
                    $idnotadevolucion,
                    $value->idproducto,
                    $value->cantidad,
                    $value->idunidadmedida,
                    1,
                    usersmaster()->idtienda
                );     
            }
            // Fin Actualizar Stock

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }
  
    public function show(Request $request, $id) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-seleccionarventa'){
            $venta = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('ubigeo as ubigeocliente','ubigeocliente.id','usuariocliente.idubigeo')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->leftJoin('agencia','agencia.id','venta.idagencia')
            ->where('venta.codigo',$request->input('venta_codigo'))
            ->where('venta.idestado',3)
            ->select(
                'venta.*',
                'usuariovendedor.nombre as nombreusuariovendedor',
                'usuariovendedor.apellidos as apellidosusuariovendedor',
                DB::raw('IF(usuariocliente.idtipopersona=1,
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                'usuariocliente.direccion as direccionusuariocliente',
                'usuariocliente.idubigeo as idubigeousuariocliente',
                'ubigeocliente.codigo as ubigeoclientecodigo',
                'ubigeocliente.nombre as ubigeoclientenombre',
                'formapago.nombre as nombreFormapago',
                'agencia.nombrecomercial as agencianombrecomercial',
                'agencia.ruc as agenciaruc',
                'agencia.razonsocial as agenciarazonsocial',
                'moneda.nombre as monedanombre',
                'tipocomprobante.nombre as tipocomprobantenombre'
            )
            ->first();
          
            $countcobranzacredito = 0;
            $countcobranzaletra = 0;
          
            $totalpagado = 0;
            $deudatotal = 0;
            $totalnotadevoluciondevolver= 0;
            $valid_productos = 0;
          
            if($venta!=''){
                $countcobranzacredito = DB::table('cobranzacredito')
                    ->where('cobranzacredito.idventa',$venta->id)
                    ->where('cobranzacredito.idestado',2)
                    ->count();
                $countcobranzaletra = DB::table('cobranzaletra')
                    ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                    ->where('tipopagoletra.idventa',$venta->id)
                    ->where('cobranzaletra.idestado',2)
                    ->count();  
              
                /**Calculo Deuda**/
                $totalnotadevolucion = DB::table('notadevolucion')
                  //->join('tipopagodetalle','tipopagodetalle.idnotadevolucion','notadevolucion.id')
                  ->where('notadevolucion.idventa',$venta->id)
                  ->where('notadevolucion.idestado',2)
                  //->where('tipopagodetalle.idtipopago','<>',4)
                  ->sum('total');
              
                $totalapagado = DB::table('notadevoluciondetalle')
                          ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                          //->join('tipopagodetalle','tipopagodetalle.idnotadevolucion','notadevolucion.id')
                          ->where('notadevolucion.idestado',2)
                          ->where('notadevolucion.idventa',$venta->id)
                          //->where('tipopagodetalle.idtipopago','<>',4)
                          ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
                
                if($venta->idformapago==1){
                    $totalpagado = $venta->montorecibido-$totalnotadevolucion;
                }elseif($venta->idformapago==2){
                    $totalpagado = DB::table('cobranzacredito')->where('idestado',2)->where('idventa',$venta->id)->sum('monto');
                    $deudatotal = $venta->montorecibido-$totalpagado-$totalnotadevolucion;
                }elseif($venta->idformapago==3){
                    $totalpagado = DB::table('cobranzaletra')
                        ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                        ->where('cobranzaletra.idestado',2)
                        ->where('tipopagoletra.idventa',$venta->id)
                        ->sum('cobranzaletra.monto');
                    $deudatotal = $venta->montorecibido-$totalpagado-$totalnotadevolucion;
                }
                /**Fin Calculo Deuda**/
                //validar si existe productos
                $ventadetalles = DB::table('ventadetalle')
                    ->where('ventadetalle.idventa',$venta->id)
                    ->get();
                foreach($ventadetalles as $value){
                    $cantidad_notadevolucion = DB::table('notadevoluciondetalle')
                          ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                          ->where('notadevolucion.idestado',2)
                          ->where('notadevoluciondetalle.idventadetalle',$value->id)
                          ->sum('notadevoluciondetalle.cantidad');
                    $cantidad_actual = $value->cantidad-$cantidad_notadevolucion;
                    if($cantidad_actual>0){
                        $valid_productos=1;
                        break;
                    }  
                }
            }
          
            return [ 
              'venta' => $venta,
              'countcobranzacredito' => $countcobranzacredito,
              'countcobranzaletra' => $countcobranzaletra,
              'totalapagar' => number_format($totalpagado+$deudatotal-$totalapagado, 2, '.', ''),
              'totalpagado' => number_format($totalpagado, 2, '.', ''),
              'deudatotal' => number_format($deudatotal, 2, '.', ''),
              'valid_productos' => $valid_productos,
            ];
        }else if ($id == 'show-cliente') {
          $usuarios = DB::table('users')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'users.id as id',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as text')
                )
                ->get();
          
            return $usuarios;
        }
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
        $notadevolucion = DB::table('notadevolucion')
                ->join('venta','venta.id','notadevolucion.idventa')
                ->join('moneda','moneda.id','notadevolucion.idmoneda')
                ->join('users','users.id','venta.idusuariocliente')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('agencia','agencia.id','venta.idagencia')
                ->join('formapago','formapago.id','venta.idformapago')
                ->where('notadevolucion.id',$id)
                ->select(
                    'notadevolucion.*',
                    'venta.codigo as ventacodigo',
                    'moneda.nombre as monedanombre',
                    'agencia.nombrecomercial as agencianombre',
                    'formapago.nombre as formapagonombre',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariovendedor.apellidos as apellidosusuariovendedor',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as cliente'),
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                    'usuariocliente.nombre as nombreclienete',
                    'usuariocliente.apellidos as apellidosclienete',
                    'usuariocliente.identificacion as identificacioncliente',
                    'usuariocliente.direccion as direccionusuariocliente',
                    'usuariocliente.idubigeo as idubigeousuariocliente',
                    'formapago.nombre as nombreFormapago',
                    'moneda.nombre as monedanombre'
                )
                ->first();

      
        /*if($request->input('view') == 'editar') {
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->join('producto','producto.id','notadevoluciondetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
                ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
                ->select(
                    'notadevoluciondetalle.*',
                    'producto.id as idproducto',
                    'producto.codigoimpresion as producodigoimpresion',
                    'producto.compatibilidadnombre as productonombre',
                    'producto.compatibilidadmotor as productomotor',
                    'producto.compatibilidadmarca as productomarca',
                    'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidadmedidanombre'
                )
                ->orderBy('notadevoluciondetalle.id','asc')
                ->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/notadevolucion/edit',[
                'notadevolucion' => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'confirmar') {
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->join('producto','producto.id','notadevoluciondetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
                ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
                ->select(
                    'notadevoluciondetalle.*',
                    'producto.id as idproducto',
                    'producto.codigoimpresion as producodigoimpresion',
                    'producto.compatibilidadnombre as productonombre',
                    'producto.compatibilidadmotor as productomotor',
                    'producto.compatibilidadmarca as productomarca',
                    'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidadmedidanombre'
                )
                ->orderBy('notadevoluciondetalle.id','asc')
                ->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/notadevolucion/confirmar',[
                'notadevolucion' => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'monedas' => $monedas
            ]);
        }*/
          
        if($request->input('view') == 'detalle') {
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->join('producto','producto.id','notadevoluciondetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
                ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
                ->select(
                    'notadevoluciondetalle.*',
                    'producto.id as idproducto',
                    'producto.codigoimpresion as producodigoimpresion',
                    'producto.nombreproducto as nombreproducto',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidadmedidanombre'
                )
                ->orderBy('notadevoluciondetalle.id','asc')
                ->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/notadevolucion/detalle',[
                'notadevolucion' => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'anular') {
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->join('producto','producto.id','notadevoluciondetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
                ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
                ->select(
                    'notadevoluciondetalle.*',
                    'producto.id as idproducto',
                    'producto.codigoimpresion as producodigoimpresion',
                    'producto.nombreproducto as nombreproducto',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidadmedidanombre'
                )
                ->orderBy('notadevoluciondetalle.id','asc')
                ->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/notadevolucion/anular',[
                'notadevolucion' => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->join('producto','producto.id','notadevoluciondetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
                ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
                ->select(
                    'notadevoluciondetalle.*',
                    'producto.id as idproducto',
                    'producto.codigoimpresion as producodigoimpresion',
                    'producto.nombreproducto as nombreproducto',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidadmedidanombre'
                )
                ->orderBy('notadevoluciondetalle.id','asc')
                ->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/notadevolucion/delete',[
                'notadevolucion' => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'proformacliente') {
            return view('layouts/backoffice/notadevolucion/proformacliente',[
                'notadevolucion' => $notadevolucion,
            ]);
        }elseif($request->input('view') == 'proformacliente-pdf') {
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($notadevolucion->idubigeousuariocliente)->first();
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
              ->join('producto','producto.id','notadevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
              ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
              ->select(
                'notadevoluciondetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
//                 'producto.compatibilidadmotor as productomotor',
//                 'producto.compatibilidadmarca as productomarca',
//                 'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('notadevoluciondetalle.id','asc')
              ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/notadevolucion/proformacliente-pdf',[
                'formapagos' => $formapagos,
                'notadevolucion' => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
            return $pdf->stream();
          
        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/notadevolucion/ticket',[
                'notadevolucion' => $notadevolucion,
            ]);
        }
        elseif($request->input('view') == 'ticket-pdf') {
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($notadevolucion->idubigeousuariocliente)->first();
            $notadevoluciondetalles = DB::table('notadevoluciondetalle')
              ->join('producto','producto.id','notadevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','notadevoluciondetalle.idunidadmedida')
              ->where('notadevoluciondetalle.idnotadevolucion',$notadevolucion->id)
              ->select(
                'notadevoluciondetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
//                 'producto.compatibilidadmotor as productomotor',
//                 'producto.compatibilidadmarca as productomarca',
//                 'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('notadevoluciondetalle.id','asc')
              ->get();
//             $agencia = DB::table('agencia')
// //                 ->leftJoin('ubigeo','ubigeo.id','agencia.idubigeo')
//                 ->where('agencia.id',$venta->idagencia)
//                 ->select(
//                   'agencia.*'
//                 )
//                 ->first();
            $pdf = PDF::loadView('layouts/backoffice/notadevolucion/ticket-pdf',[
                'formapagos'    => $formapagos,
                'notadevolucion'         => $notadevolucion,
                'notadevoluciondetalles' => $notadevoluciondetalles,
                'ubigeocliente' => $ubigeocliente,
//                 'agencia' => $agencia,
              
            ]);
            return $pdf->stream();
          
        }
    }

    public function update(Request $request, $idnotadevolucion)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
        /*if($request->input('view') == 'editar') {
            $rules = [
                'idventa' => 'required',
                'motivodevolucion' => 'required',
                'productos' => 'required',
            ];
            $messages = [
                'idventa.required' => 'Hay un error, intente otra vez por favor.',
                'motivodevolucion.required' => 'El "Motivo" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];

            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idventadetalle = explode('/-/', $item[0]);
                $cantidad = explode('/-/', $item[1]);
                if($cantidad[1]<=0){
                    $rules = array_merge($rules,[
                        $cantidad[0] => 'required|numeric|min:1',
                    ]);
                    $messages = array_merge($messages,[
                        $cantidad[0].'.required' => 'La cantidad es obligatorio.',
                        $cantidad[0].'.min' => 'La cantidad minímo es 1.',
                    ]);
                    break;
                }
              
                $ventadetalle = DB::table('ventadetalle')
                    ->where('ventadetalle.id',$idventadetalle[1])
                    ->first();
                $cantidad_notadevolucion = DB::table('notadevoluciondetalle')
                     ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                     ->where('notadevoluciondetalle.idventadetalle',$idventadetalle[1])
                     ->where('notadevolucion.idestado',2)
                     ->sum('notadevoluciondetalle.cantidad');
              
                $cantidad_actual = $ventadetalle->cantidad-$cantidad_notadevolucion;
              
                if($cantidad_actual<$cantidad[1]){
                    $rules = array_merge($rules,[
                        'error' => 'required',
                    ]);
                    $messages = array_merge($messages,[
                        'error.required' => 'No hay sifucientes productos, ingrese otra cantidad!!.',
                    ]);
                }
            }
          
            $formapago_validar = formapago_validar($request->input('totaladevolver'),$request,$rules,$messages,2);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

            
            DB::table('notadevolucion')->whereId($idnotadevolucion)->update([
                'total' => $formapago_validar['total'],
                'motivo' => $request->input('motivodevolucion'),
                'idformapago'=> $request->input('idformapago'),
                'idventa' => $request->input('idventa'),
                'idtienda' => usersmaster()->idtienda,
                'idusuarioresponsable' => Auth::user()->id
            ]);
          
            DB::table('notadevoluciondetalle')->where('idnotadevolucion',$idnotadevolucion)->delete();
          
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idventadetalle = explode('/-/', $item[0]);
                $cantidad = explode('/-/', $item[1]);
                $preciounitario = explode('/-/', $item[2]);
                $ventadetalle = DB::table('ventadetalle')
                    ->where('ventadetalle.id',$idventadetalle[1])
                    ->first();
                DB::table('notadevoluciondetalle')->insert([
                    'cantidad' => $cantidad[1],
                    'preciounitario' => $preciounitario[1],
                    'idunidadmedida' => $ventadetalle->idunidadmedida,
                    'idproducto' => $ventadetalle->idproducto,
                    'idventadetalle' => $idventadetalle[1],
                    'idnotadevolucion' => $idnotadevolucion
               ]);
            }
          
            DB::table('tipopagodetalle')->where('idnotadevolucion',$idnotadevolucion)->delete();
          
            //forma de pago
            formapago_insertar(
                $request,
                'notadevolucion',
                $idnotadevolucion
            );
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') {
            $rules = [
                'idventa' => 'required',
                'motivodevolucion' => 'required',
                'productos' => 'required',
            ];
            $messages = [
                'idventa.required' => 'Hay un error, intente otra vez por favor.',
                'motivodevolucion.required' => 'El "Motivo" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];

            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idventadetalle = explode('/-/', $item[0]);
                $cantidad = explode('/-/', $item[1]);
                if($cantidad[1]<=0){
                    $rules = array_merge($rules,[
                        $cantidad[0] => 'required|numeric|min:1',
                    ]);
                    $messages = array_merge($messages,[
                        $cantidad[0].'.required' => 'La cantidad es obligatorio.',
                        $cantidad[0].'.min' => 'La cantidad minímo es 1.',
                    ]);
                    break;
                }
              
                $ventadetalle = DB::table('ventadetalle')
                    ->where('ventadetalle.id',$idventadetalle[1])
                    ->first();
                $cantidad_notadevolucion = DB::table('notadevoluciondetalle')
                     ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                     ->where('notadevoluciondetalle.idventadetalle',$idventadetalle[1])
                     ->where('notadevolucion.idestado',2)
                     ->sum('notadevoluciondetalle.cantidad');
              
                $cantidad_actual = $ventadetalle->cantidad-$cantidad_notadevolucion;
              
                if($cantidad_actual<$cantidad[1]){
                    $rules = array_merge($rules,[
                        'error' => 'required',
                    ]);
                    $messages = array_merge($messages,[
                        'error.required' => 'No hay sifucientes productos, ingrese otra cantidad!!.',
                    ]);
                }
            }
          
            $formapago_validar = formapago_validar($request->input('totaladevolver'),$request,$rules,$messages,2);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);

            
            DB::table('notadevolucion')->whereId($idnotadevolucion)->update([
                'fechaconfirmacion' => Carbon::now(),
                'idtienda' => usersmaster()->idtienda,
                'idusuarioresponsable' => Auth::user()->id,
                'idaperturacierre' => $formapago_validar['idaperturacierre'],
                'idestado' => 2
            ]);
          
            DB::table('tipopagodetalle')->where('idnotadevolucion',$idnotadevolucion)->delete();
          
            //forma de pago
            formapago_insertar(
                $request,
                'notadevolucion',
                $idnotadevolucion
            );
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }*/
        if($request->input('view') == 'anular') {
            DB::table('notadevolucion')->whereId($idnotadevolucion)->update([
               'fechaanulacion' => Carbon::now(),
               'idestado' => 3
            ]);
          
            // tipo pago detalle
            DB::table('tipopagodetalle')->where('idnotadevolucion',$idnotadevolucion)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 3
            ]);
          
             // Actualizar Stock
             $notadevoluciondetalles = DB::table('notadevoluciondetalle')
                ->where('notadevoluciondetalle.idnotadevolucion',$idnotadevolucion)
                ->get();

            foreach($notadevoluciondetalles as $value){
                actualizar_stock(
                    'notadevolucion',
                    $idnotadevolucion,
                    $value->idproducto,
                    $value->cantidad,
                    $value->idunidadmedida,
                    1,
                    usersmaster()->idtienda,
                    'Salida'
                );     
            }
            // Fin Actualizar Stock
       

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idnotadevolucion)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idnotadevolucion',$idnotadevolucion)->delete();
            DB::table('notadevoluciondetalle')->where('idnotadevolucion',$idnotadevolucion)->delete();
            DB::table('notadevolucion')
                ->whereId($idnotadevolucion)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
