<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

class CompraDevolucionController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $where = [];
        $where1 = [];
        $where2 = [];
      
        $where[] = ['compradevolucion.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where[] = ['compra.codigo','LIKE','%'.$request->input('codigocompra').'%'];
        $where[] = ['compradevolucion.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        $where[] = ['proveedor.identificacion','LIKE','%'.$request->input('cliente').'%'];
      
        $where1[] = ['compradevolucion.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where1[] = ['compra.codigo','LIKE','%'.$request->input('codigocompra').'%'];
        $where1[] = ['compradevolucion.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where1[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        $where1[] = ['proveedor.nombre','LIKE','%'.$request->input('cliente').'%'];
      
        $where2[] = ['compradevolucion.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where2[] = ['compra.codigo','LIKE','%'.$request->input('codigocompra').'%'];
        $where2[] = ['compradevolucion.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where2[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        $where2[] = ['proveedor.apellidos','LIKE','%'.$request->input('cliente').'%'];
        
        $compradevolucions = DB::table('compradevolucion')
            ->join('users as responsable','responsable.id','compradevolucion.idusers')
            ->join('compra','compra.id','compradevolucion.idcompra')
            ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
            ->join('moneda','moneda.id','compra.idmoneda')
            ->where('compradevolucion.idtienda',usersmaster()->idtienda)
            ->where($where)
            ->orWhere($where1)
            ->orWhere($where2)
            ->select(
                'compradevolucion.*',
                'compra.codigo as compracodigo',
                'responsable.nombre as responsable',
                DB::raw('IF(proveedor.idtipopersona=1,
                CONCAT(proveedor.identificacion," - ",proveedor.apellidos,", ",proveedor.nombre),
                CONCAT(proveedor.identificacion," - ",proveedor.apellidos)) as proveedor'),
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('compradevolucion.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/compradevolucion/index',[
            'compradevolucions' => $compradevolucions,
            'idapertura' => aperturacierre(usersmaster()->idtienda,Auth::user()->id)['idapertura']
        ]);
    }
  
    public function create(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $tipocomprobantes = DB::table('tipocomprobante')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/compradevolucion/create', [
              'tipocomprobantes' => $tipocomprobantes,
              'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'compradevoluciondetalle') {
            $compradetalles = DB::table('compradetalle')
                ->join('producto','producto.id','compradetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
                ->where('compradetalle.idcompra',$request->input('idcompra'))
                ->select(
                    'compradetalle.*',
                    'producto.codigoimpresion as codigoproducto',
                     'producto.nombreproducto as nombreproducto',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as unidad'
                )
                ->orderBy('compradetalle.id','asc')
                ->get();
          
            $compradel = [];
            foreach($compradetalles as $value){
              
                $cantidad_compradevolucion = DB::table('compradevoluciondetalle')
                     ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                     ->where('compradevoluciondetalle.idcompradetalle',$value->id)
                     ->where('compradevolucion.idestado',1)
                     ->orWhere('compradevoluciondetalle.idcompradetalle',$value->id)
                     ->where('compradevolucion.idestado',2)
                     ->sum('compradevoluciondetalle.cantidad');
                
                $cantidad_actual = $value->cantidad-$cantidad_compradevolucion;
                if($cantidad_actual>0){
                    $compradel[] = [
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
            
            return view('layouts/backoffice/compradevolucion/compradevoluciondetalle',[
                'compradetalles' => $compradel,
            ]);
        }
    }
  
    public function store(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
          
            $rules = [
                'idcompra' => 'required',
                'motivodevolucioncompra' => 'required',
                'productos' => 'required',
            ];
            $messages = [
                'idcompra.required' => 'La "Compra" son Obligatorio.',
                'motivodevolucioncompra.required' => 'El "Motivo de Devolución de Compra" son Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
            
            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idcompradetalle = explode('/-/', $item[0]);
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
              
                $compradetalle = DB::table('compradetalle')
                    ->where('compradetalle.id',$idcompradetalle[1])
                    ->first();
                $cantidad_compradevolucion = DB::table('compradevoluciondetalle')
                     ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                     ->where('compradevoluciondetalle.idcompradetalle',$idcompradetalle[1])
                     ->where('compradevolucion.idestado',2)
                     ->sum('compradevoluciondetalle.cantidad');
              
                $cantidad_actual = $compradetalle->cantidad-$cantidad_compradevolucion;
              
                if($cantidad_actual<$cantidad[1]){
                    $rules = array_merge($rules,[
                        'error' => 'required',
                    ]);
                    $messages = array_merge($messages,[
                        'error.required' => 'No hay sifucientes productos, ingrese otra cantidad!!.',
                    ]);
                }
            }
          
            if($request->input('totaladevolver')>0){
                $formapago_validar = formapago_validar($request->input('totaladevolver'),$request,$rules,$messages,1);
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
            
          
            $compradevolucion = DB::table('compradevolucion')
                ->select('compradevolucion.*')
                ->orderBy('compradevolucion.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($compradevolucion!=''){
                $codigo = $compradevolucion->codigo+1;
            }

            // DEVOLUCION COMPRA
            $idcompradevolucion = DB::table('compradevolucion')->insertGetId([
               'fecharegistro' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'codigo' => $codigo,
               'montorecibido' => $total,
               'motivo' => $request->input('motivodevolucioncompra'),
               'idformapago'=> $request->input('idformapago'),
               'idmoneda' => $request->input('idmoneda'),
               'idcompra' => $request->input('idcompra'),
               'idusers' => Auth::user()->id,
               'idaperturacierre' => $idaperturacierre,
               'idtienda' =>  usersmaster()->idtienda,
               'idestado' => 2
            ]);
          
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idcompradetalle = explode('/-/', $item[0]);
                $cantidad = explode('/-/', $item[1]);
                $preciounitario = explode('/-/', $item[2]);
                $compradetalle = DB::table('compradetalle')
                    ->where('compradetalle.id',$idcompradetalle[1])
                    ->first();
                DB::table('compradevoluciondetalle')->insert([
                    'cantidad' => $cantidad[1],
                    'preciounitario' => $preciounitario[1],
                    'idunidadmedida' => $compradetalle->idunidadmedida,
                    'idproducto' => $compradetalle->idproducto,
                    'idcompradetalle' => $idcompradetalle[1],
                    'idcompradevolucion' => $idcompradevolucion
               ]);
            }
          
            //forma de pago            
            DB::table('tipopagodetalle')->where('idcompradevolucion',$idcompradevolucion)->delete();
            formapago_insertar(
                $request,
                'compradevolucion',
                $idcompradevolucion
            );
          
            // Actualizar Stock
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
               ->where('compradevoluciondetalle.idcompradevolucion',$idcompradevolucion)
               ->get();

            foreach($compradevoluciondetalles as $value){
                actualizar_stock(
                    'compradevolucion',
                    $idcompradevolucion,
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
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }
  
    public function show(Request $request, $id) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-seleccionarcompra'){
          
            $compra = DB::table('compra')
                ->join('users','users.id','compra.idusuarioproveedor')
                ->join('formapago','formapago.id','compra.idformapago')
                ->leftJoin('aperturacierre','aperturacierre.id','compra.idaperturacierre')
                ->leftJoin('caja','caja.id','aperturacierre.idcaja')
                ->leftJoin('tienda','tienda.id','caja.idtienda')
                ->where('compra.codigo',$request->input('compra_codigo'))
                ->select(
                    'compra.*',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre',
                    'users.idubigeo as idubigeo',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as proveedor'),
                    'formapago.nombre as nombreFormapago',
                    'tienda.nombre as tiendanombre'
                )
                ->first();
          
            $countpagocredito = 0;
            $countpagoletra = 0;
          
            $totalpagado = 0;
            $deudatotal = 0;
            $totalcompradevoluciondevolver= 0;
            $valid_productos = 0;
          
            $compra_monto = 0;
            $totalapagar = 0;
            $totalcompradevolucion = 0;
          
            if($compra!=''){
                $compra_monto = $compra->monto;
                $countpagocredito = DB::table('pagocredito')
                    ->where('pagocredito.idcompra',$compra->id)
                    ->where('pagocredito.idestado',2)
                    ->count();
                $countpagoletra = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('tipopagoletra.idcompra',$compra->id)
                    ->where('pagoletra.idestado',2)
                    ->count();  
              
                /**Calculo Deuda**/
                $totalcompradevolucion = DB::table('compradevolucion')
                  ->where('compradevolucion.idcompra',$compra->id)
                  ->where('compradevolucion.idestado',2)
                  ->sum('montorecibido');
              
                $totalapagar = DB::table('compradevoluciondetalle')
                          ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                          ->where('compradevolucion.idestado',2)
                          ->where('compradevolucion.idcompra',$compra->id)
                          ->sum(DB::raw('CONCAT(compradevoluciondetalle.cantidad*compradevoluciondetalle.preciounitario)'));
                
                if($compra->idformapago==1){
                    $totalpagado = $compra_monto-$totalcompradevolucion;
                }elseif($compra->idformapago==2){
                    $totalpagado = DB::table('pagocredito')->where('idestado',2)->where('idcompra',$compra->id)->sum('monto');
                    $deudatotal = $compra_monto-$totalpagado-$totalcompradevolucion;
                }elseif($compra->idformapago==3){
                    $totalpagado = DB::table('pagoletra')
                        ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                        ->where('pagoletra.idestado',2)
                        ->where('tipopagoletra.idcompra',$compra->id)
                        ->sum('pagoletra.monto');
                    $deudatotal = $compra_monto-$totalpagado-$totalcompradevolucion;
                }
                /**Fin Calculo Deuda**/
                //validar si existe productos
                $compradetalles = DB::table('compradetalle')
                    ->where('compradetalle.idcompra',$compra->id)
                    ->get();
                foreach($compradetalles as $value){
                    $cantidad_compradevolucion = DB::table('compradevoluciondetalle')
                          ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                          ->where('compradevolucion.idestado',2)
                          ->where('compradevoluciondetalle.idcompradetalle',$value->id)
                          ->sum('compradevoluciondetalle.cantidad');
                    $cantidad_actual = $value->cantidad-$cantidad_compradevolucion;
                    if($cantidad_actual>0){
                        $valid_productos=1;
                        break;
                    }  
                }
            }
          
            return [ 
              'compra' => $compra,
              'countpagocredito' => $countpagocredito,
              'countpagoletra' => $countpagoletra,
              'totalapagar' => number_format($compra_monto-$totalapagar, 2, '.', ''),
              'totalpagado' => number_format($totalpagado-$totalcompradevolucion, 2, '.', ''),
              'deudatotal' => number_format($deudatotal, 2, '.', ''),
              'valid_productos' => $valid_productos,
            ];
        }
        /*if($id == 'show-seleccionarcompra'){
            $compra = DB::table('compra')
                ->join('users','users.id','compra.idusuarioproveedor')
                ->leftJoin('aperturacierre','aperturacierre.id','compra.idaperturacierre')
                ->leftJoin('caja','caja.id','aperturacierre.idcaja')
                ->leftJoin('tienda','tienda.id','caja.idtienda')
                ->where('compra.codigo',$request->input('codigocompra'))
                ->select(
                    'compra.*',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre',
                    'users.idubigeo as idubigeo',
                    'users.identificacion as proveedoridentificacion',
                    'users.nombre as proveedornombre',
                    'tienda.nombre as tiendanombre'
                )
                ->first(); 
            $idcompra = 0;
            if($compra!=''){
                $idcompra = $compra->id;
            }
            $compradetalles = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$idcompra)
              ->select(
                'compradetalle.*',
                'producto.id as idproducto',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
        
            $compradetal = [];
            $idtienda = usersmaster()->idtienda;
            foreach($compradetalles as $value){
              
                $compradevoluciondetalle_cantidad = DB::table('compradevoluciondetalle')
                     ->join('compradevolucion','compradevolucion.id','compradevoluciondetalle.idcompradevolucion')
                     ->where('compradevoluciondetalle.idcompradetalle',$value->id)
                     ->where('compradevolucion.idestado',1)
                     ->orWhere('compradevoluciondetalle.idcompradetalle',$value->id)
                     ->where('compradevolucion.idestado',2)
                     ->sum('compradevoluciondetalle.cantidad');
                
                  $resta = $value->cantidad-$compradevoluciondetalle_cantidad;
        
                    $compradetal[] = [
                         "idcompradetalle" => $value->id,
                         "idproducto" => $value->idproducto,
                         "producodigoimpresion" => $value->producodigoimpresion,
                         "productonombre" => $value->productonombre,
                         "productomotor" => $value->productomotor,
                         "productomarca" => $value->productomarca,
                         "productomodelo" => $value->productomodelo,
                         "preciounitario" => $value->preciounitario,
                         "idunidadmedida" => $value->idunidadmedida,
                         "unidadmedidanombre" => $value->unidadmedidanombre,
                         "stock" => stock_producto($idtienda,$value->idproducto)['total'],
                         "cantidad" => $resta,
                         "preciototal" => $value->preciototal
                    ];
            }
          
            return [ 
              'compra' => $compra,
              'compradetalles' => (object)$compradetal 
            ];
        }*/
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $compradevolucion = DB::table('compradevolucion')
                ->join('compra','compra.id','compradevolucion.idcompra')
                ->join('users','users.id','compra.idusuarioproveedor')
                ->join('users as usuariocliente','usuariocliente.id','compra.idusuarioproveedor')
                ->join('users as usuariovendedor','usuariovendedor.id','compra.idusuarioresponsable')
                ->join('formapago','formapago.id','compra.idformapago')
                ->join('moneda','moneda.id','compradevolucion.idmoneda')
                ->where('compradevolucion.id',$id)
                ->select(
                    'compradevolucion.*',
                    'compra.codigo as compracodigo',
                    'compra.idusuarioproveedor as idusuarioproveedor',
                    'compra.seriecorrelativo as seriecorrelativo',
                    'compra.fechaemision as fechaemision',
                    'compra.idcomprobante as idcomprobante',
                    'compra.idestado as idestado',
                    'compra.idmoneda as idmoneda',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariovendedor.apellidos as apellidosusuariovendedor',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.apellidos)) as proveedor'),
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
           'usuariocliente.nombre as nombrecliente',
                    'usuariocliente.identificacion as identificacioncliente',
                    'usuariocliente.direccion as direccionusuariocliente',
                    'usuariocliente.idubigeo as idubigeousuariocliente',
                    'formapago.nombre as nombreFormapago',
                    'moneda.nombre as monedanombre'
                )
                ->first();
      
        if($request->input('view') == 'editar') {
            $tipocomprobantes = DB::table('tipocomprobante')->get();
            $monedas = DB::table('moneda')->get();
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
              ->join('producto','producto.id','compradevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradevoluciondetalle.idunidadmedida')
              ->where('compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                  'compradevoluciondetalle.*',
                  'producto.id as idproducto',
                  'producto.codigoimpresion as producodigoimpresion',
                  'producto.compatibilidadnombre as productonombre',
                  'producto.compatibilidadmotor as productomotor',
                  'producto.compatibilidadmarca as productomarca',
                  'producto.compatibilidadmodelo as productomodelo',
                  'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradevoluciondetalle.id','asc')
              ->get();
            return view('layouts/backoffice/compradevolucion/edit',[
                'compradevolucion' => $compradevolucion,
                'compradevoluciondetalles' => $compradevoluciondetalles,
                'tipocomprobantes' => $tipocomprobantes,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'detalle') {
            $tipocomprobantes = DB::table('tipocomprobante')->get();
            $monedas = DB::table('moneda')->get();
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
              ->join('producto','producto.id','compradevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradevoluciondetalle.idunidadmedida')
              ->where('compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                  'compradevoluciondetalle.*',
                  'producto.id as idproducto',
                  'producto.codigoimpresion as producodigoimpresion',
                  'producto.nombreproducto as nombreproducto',
//                   'producto.compatibilidadmotor as productomotor',
//                   'producto.compatibilidadmarca as productomarca',
//                   'producto.compatibilidadmodelo as productomodelo',
                  'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradevoluciondetalle.id','asc')
              ->get();
            return view('layouts/backoffice/compradevolucion/detalle',[
                'compradevolucion' => $compradevolucion,
                'compradevoluciondetalles' => $compradevoluciondetalles,
                'tipocomprobantes' => $tipocomprobantes,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'anular') {
            $tipocomprobantes = DB::table('tipocomprobante')->get();
            $monedas = DB::table('moneda')->get();
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
              ->join('producto','producto.id','compradevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradevoluciondetalle.idunidadmedida')
              ->where('compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                  'compradevoluciondetalle.*',
                  'producto.id as idproducto',
                  'producto.codigoimpresion as producodigoimpresion',
                  'producto.nombreproducto as nombreproducto',
//                   'producto.compatibilidadmotor as productomotor',
//                   'producto.compatibilidadmarca as productomarca',
//                   'producto.compatibilidadmodelo as productomodelo',
                  'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradevoluciondetalle.id','asc')
              ->get();
            return view('layouts/backoffice/compradevolucion/anular',[
                'compradevolucion' => $compradevolucion,
                'compradevoluciondetalles' => $compradevoluciondetalles,
                'tipocomprobantes' => $tipocomprobantes,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $tipocomprobantes = DB::table('tipocomprobante')->get();
            $monedas = DB::table('moneda')->get();
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
              ->join('producto','producto.id','compradevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradevoluciondetalle.idunidadmedida')
              ->where('compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                  'compradevoluciondetalle.*',
                  'producto.id as idproducto',
                  'producto.codigoimpresion as producodigoimpresion',
                  'producto.compatibilidadnombre as productonombre',
                  'producto.compatibilidadmotor as productomotor',
                  'producto.compatibilidadmarca as productomarca',
                  'producto.compatibilidadmodelo as productomodelo',
                  'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradevoluciondetalle.id','asc')
              ->get();
            return view('layouts/backoffice/compradevolucion/delete',[
                'compradevolucion' => $compradevolucion,
                'compradevoluciondetalles' => $compradevoluciondetalles,
                'tipocomprobantes' => $tipocomprobantes,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'proformacliente') {
            return view('layouts/backoffice/compradevolucion/proformacliente',[
                'compradevolucion' => $compradevolucion,
            ]);
        }elseif($request->input('view') == 'proformacliente-pdf') {
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($compradevolucion->idubigeousuariocliente)->first();
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
              ->join('producto','producto.id','compradevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradevoluciondetalle.idunidadmedida')
              ->where('compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                'compradevoluciondetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradevoluciondetalle.id','asc')
              ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/compradevolucion/proformacliente-pdf',[
                'formapagos' => $formapagos,
                'compradevolucion' => $compradevolucion,
                'compradevoluciondetalles' => $compradevoluciondetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
            return $pdf->stream();
          
        }
      elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/compradevolucion/ticket',[
                'compradevolucion' => $compradevolucion,
            ]);
        }
        elseif($request->input('view') == 'ticket-pdf') {
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($compradevolucion->idubigeousuariocliente)->first();
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
              ->join('producto','producto.id','compradevoluciondetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradevoluciondetalle.idunidadmedida')
              ->where('compradevoluciondetalle.idcompradevolucion',$compradevolucion->id)
              ->select(
                'compradevoluciondetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
          
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('compradevoluciondetalle.id','asc')
              ->get();
//             $agencia = DB::table('agencia')
// //                 ->leftJoin('ubigeo','ubigeo.id','agencia.idubigeo')
//                 ->where('agencia.id',$compradevolucion->idagencia)
//                 ->select(
//                   'agencia.*'
//                 )
//                 ->first();
            $pdf = PDF::loadView('layouts/backoffice/compradevolucion/ticket-pdf',[
                'formapagos'    => $formapagos,
                'compradevolucion'         => $compradevolucion,
                'compradevoluciondetalles' => $compradevoluciondetalles,
                'ubigeocliente' => $ubigeocliente,
//                 'agencia' => $agencia,
              
            ]);
            return $pdf->stream();
          
        }
    }

    public function update(Request $request, $idcompradevolucion)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'anular') {

            // CAJA
            // Apertura de caja
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
            // Fin Apertura de caja
          
            DB::table('compradevolucion')->whereId($idcompradevolucion)->update([
               'fechaanulacion' => Carbon::now(),
               'idestado' => 3
            ]);
          
            // tipo pago detalle
            DB::table('tipopagodetalle')->where('idcompradevolucion',$idcompradevolucion)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 3
            ]);

               // Actualizar Stock
            $compradevoluciondetalles = DB::table('compradevoluciondetalle')
               ->where('compradevoluciondetalle.idcompradevolucion',$idcompradevolucion)
               ->get();

            foreach($compradevoluciondetalles as $value){
                actualizar_stock(
                    'compradevolucion',
                    $idcompradevolucion,
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
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
          
        }
    }

    public function destroy(Request $request, $idcompradevolucion)
    {
        if($request->input('view') == 'eliminar') {
            DB::table('tipopagodetalle')->where('idcompradevolucion',$idcompradevolucion)->delete();
            DB::table('compradevoluciondetalle')->where('idcompradevolucion',$idcompradevolucion)->delete();
            DB::table('compradevolucion')->whereId($idcompradevolucion)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
