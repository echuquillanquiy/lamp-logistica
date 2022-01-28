<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Mail;
use PDF;
use DB;
use Peru\Http\ContextClient;
use Peru\Sunat\{HtmlParser, Ruc, RucParser};
use Peru\Jne\{Dni, DniParser};


class CotizacionController extends Controller
{
    public function index(Request $request) 
    {
        //$request->user()->authorizeRoles( $request->path() );
      
        $where = [];
      
        if(usersmaster()->idpermiso!=6){
            $where[] = ['venta.idusuariovendedor',Auth::user()->id];
            $where1[] = ['venta.idusuariovendedor',Auth::user()->id];
            $where2[] = ['venta.idusuariovendedor',Auth::user()->id];
        }
      
        
        $where[] = ['venta.idestado','<>',3];
        $where[] = ['venta.idtienda',usersmaster()->idtienda];
        $where[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where[] = ['usuariocliente.identificacion','LIKE','%'.$request->input('cliente').'%'];
      
        $where1[] = ['venta.idestado','<>',3];
        $where1[] = ['venta.idtienda',usersmaster()->idtienda];
        $where1[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where1[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where1[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where1[] = ['usuariocliente.nombre','LIKE','%'.$request->input('cliente').'%'];
      
        $where2[] = ['venta.idestado','<>',3];
        $where2[] = ['venta.idtienda',usersmaster()->idtienda];
        $where2[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where2[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where2[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where2[] = ['usuariocliente.apellidos','LIKE','%'.$request->input('cliente').'%'];
        
        $ventas = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->where($where)
            ->orWhere($where1)
            ->orWhere($where2)
            ->select(
                'venta.*',
                'usuariovendedor.nombre as nombreusuariovendedor',
                DB::raw('IF(usuariocliente.idtipopersona=1,
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                'formapago.nombre as nombreFormapago',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('venta.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/cotizacion/index',[
            'ventas' => $ventas
        ]);
    }
  
    public function create(Request $request) 
    {
        //$request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $formapagos = DB::table('formapago')->get();
            return view('layouts/backoffice/cotizacion/create', [
              'formapagos' => $formapagos
            ]);
        }elseif($request->input('view') == 'registrar-cliente') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/cotizacion/cliente',[
                'tipopersonas' => $tipopersonas
            ]);
        }elseif($request->input('view') == 'productos') {
            return view('layouts/backoffice/cotizacion/productos');
        }elseif($request->input('view') == 'producto-cotizado') {
            $tienda_vendedores = DB::table('users')
                ->join('venta','venta.idusuariovendedor','users.id')
                ->join('ventadetalle','ventadetalle.idventa','venta.id')
                ->join('tienda','tienda.id','venta.idtienda')
                ->where('ventadetalle.idproducto',$request->input('idproducto'))
                ->where('venta.idestado',2)
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->select(
                    'users.*',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('ventadetalle.id','asc')
                ->distinct()
                ->get();
            $todatienda_vendedores = DB::table('users')
                ->join('venta','venta.idusuariovendedor','users.id')
                ->join('ventadetalle','ventadetalle.idventa','venta.id')
                ->join('tienda','tienda.id','venta.idtienda')
                ->where('ventadetalle.idproducto',$request->input('idproducto'))
                ->where('venta.idestado',2)
                ->select(
                    'users.*',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('ventadetalle.id','asc')
                ->distinct()
                ->get();
          
            $producto = DB::table('producto')
                ->whereId($request->input('idproducto'))
                ->first();
          
          
            return view('layouts/backoffice/cotizacion/producto-cotizado',[
                'producto' => $producto,
                'tienda_vendedores' => $tienda_vendedores,
                'todatienda_vendedores' => $todatienda_vendedores
            ]);
        }
    }
  
    public function store(Request $request)
    {
        //$request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $rules = [
              'idcliente' => 'required',
              'idformapago' => 'required',
              'idestado' => 'required',
              'productos' => 'required',
            ];
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'idformapago.required' => 'La "Formad de Pago" es Obligatorio.',
              'idestado.required' => 'El "Estado" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Precio Unitario minímo es 0.00.'
                    ]);
                    break;
                }elseif($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligatorio.'
                    ]);
                    break;
                }
              
                /*if($request->input('idestado')==2){
                    $ventas = DB::table('ventadetalle')
                      ->join('venta','venta.id','ventadetalle.idventa')
                      ->where('venta.idtienda',usersmaster()->idtienda)
                      ->where('ventadetalle.idproducto',$item[0])
                      ->where('venta.idestado',2)
                      ->sum('ventadetalle.cantidad');
                    $stocktotal = stock_producto(usersmaster()->idtienda,$item[0])['total'];
                    $stocktotal = ($stocktotal-$ventas);
                    if($stocktotal<$item[1]){
                        $producto = DB::table('producto')->whereId($item[0])->first();
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Suficiente Stock del producto "'.$producto->codigoimpresion.' - '.$producto->compatibilidadnombre.'"!!.'
                        ]);
                        break;
                    }
                }*/
            } 
          
            $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
            if($cliente->idtipopersona==2){
                if($cliente->apellidos==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Razón Social del Cliente!!.'
                    ]);
                }
            }

            $venta = DB::table('venta')
                ->orderBy('venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($venta!=''){
                $codigo = $venta->codigo+1;
            }

            $idventa = DB::table('venta')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'montorecibido' => '0.00',
               'vuelto' => '0.00',
               'fp_credito_fechainicio' => '',
               'fp_credito_frecuencia' => '',
               'fp_credito_dias' => '',
               'fp_credito_ultimafecha' => '',
               'fp_letra_garante' => 0,
               'fp_letra_fechainicio' => '',
               'fp_letra_frecuencia' => '',
               'fp_letra_cuotas' => '',
               'idmoneda' => 1,
               'idaperturacierre' => 0,
               'idusuariocliente' => $request->input('idcliente'),
               'idusuariovendedor' => Auth::user()->id,
               'idusuariocajero' => 0,
               'idagencia' =>  0,
               'idtipocomprobante' =>  0,
               'idformapago' =>  $request->input('idformapago'),
               'idtienda' =>  usersmaster()->idtienda,
               'idestado' => $request->input('idestado'),
            ]);
            
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('ventadetalle')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'descuento' => '0.00',
                  'preciototal' => $item[4],
                  'idunidadmedida' => $item[3],
                  'idproducto' => $item[0],
                  'idventa' => $idventa,
                ]);
            }          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }
  
    public function show(Request $request, $id) 
    {
        //$request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-listarproducto'){
            $productos = DB::table('producto')
                ->where('producto.codigoimpresion','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('producto.compatibilidadnombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'producto.id as id',
                   DB::raw('CONCAT(producto.codigoimpresion," - ",producto.compatibilidadnombre) as text')
                )
                ->get();
            return $productos;
        }elseif($id == 'show-agregarproductocodigo'){
            $producto = DB::table('producto')
              ->where('producto.codigoimpresion',$request->input('codigoimpresion'))
              ->first();
            $ventas = DB::table('ventadetalle')
                ->join('venta','venta.id','ventadetalle.idventa')
                ->where('venta.idtienda',usersmaster()->idtienda)
                ->where('ventadetalle.idproducto',$producto->id)
                ->where('venta.idestado',2)
                ->sum('ventadetalle.cantidad');
          
            $stocktotal = stock_producto(usersmaster()->idtienda,$producto->id)['total'];
          
            return [ 
              'datosProducto' => $producto,
              'stock' => ($stocktotal-$ventas)
            ];
        }elseif($id == 'show-seleccionarproducto'){
          $producto = DB::table('producto')
              ->where('producto.id',$request->input('idproducto'))
              ->first();
           
            $ventas = DB::table('ventadetalle')
                ->join('venta','venta.id','ventadetalle.idventa')
                ->where('ventadetalle.idproducto',$producto->id)
                ->where('venta.idestado',2)
                ->sum('ventadetalle.cantidad');

            $stocktotal = stock_producto(usersmaster()->idtienda,$request->input('idproducto'))['total'];
          
            return [ 
              'datosProducto' => $producto,
              'stock' => ($stocktotal-$ventas)
            ];
        }elseif($id == 'show-listarcliente'){
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
        }elseif($id == 'show-unidadmedida'){
            $productounidadmedidas = DB::table('productounidadmedida')
                ->orWhere('productounidadmedida.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'productounidadmedida.id as id',
                  'productounidadmedida.nombre as text'
                )
                ->get();
          
            return $productounidadmedidas;
        }elseif($id == 'show-dniruc') {
            return consultaDniRuc($request->numeroidentificacion, $request->idtipopersona);            
        }
    
    }
  
    public function edit(Request $request, $id)
    {
        //$request->user()->authorizeRoles( $request->path() );
      
        $venta = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->where('venta.id',$id)
            ->select(
                'venta.*',
                'usuariovendedor.nombre as nombreusuariovendedor',
                'usuariovendedor.apellidos as apellidosusuariovendedor',
                DB::raw('IF(usuariocliente.idtipopersona=1,
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                'usuariocliente.direccion as direccionusuariocliente',
                'usuariocliente.idubigeo as idubigeousuariocliente',
                'formapago.nombre as nombreFormapago',
                'moneda.nombre as monedanombre'
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            $agencias = DB::table('agencia')->get();
            $formapagos = DB::table('formapago')->get();
            $ventadetalles = DB::table('ventadetalle')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->where('ventadetalle.idventa',$venta->id)
              ->select(
                'ventadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as productonombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();
            return view('layouts/backoffice/cotizacion/edit',[
                'agencias' => $agencias,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
            ]);
        }elseif($request->input('view') == 'proforma') {
            return view('layouts/backoffice/cotizacion/proforma',[
                'venta' => $venta,
            ]);
        }elseif($request->input('view') == 'proforma-pdf') {
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($venta->idubigeousuariocliente)->first();
            $ventadetalles = DB::table('ventadetalle')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
              ->where('ventadetalle.idventa',$venta->id)
              ->select(
                'ventadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/cotizacion/proforma-pdf',[
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
            return $pdf->stream();
          
        }elseif($request->input('view') == 'proformacliente') {
            return view('layouts/backoffice/cotizacion/proformacliente',[
                'venta' => $venta,
            ]);
        }elseif($request->input('view') == 'proformacliente-pdf') {
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($venta->idubigeousuariocliente)->first();
            $ventadetalles = DB::table('ventadetalle')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
              ->where('ventadetalle.idventa',$venta->id)
              ->select(
                'ventadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/cotizacion/proformacliente-pdf',[
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
            return $pdf->stream();
          
        }elseif($request->input('view') == 'correo') {
            return view('layouts/backoffice/cotizacion/correo',[
                'venta' => $venta,
            ]);
        }elseif($request->input('view') == 'detalle') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $ventadetalles = DB::table('ventadetalle')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
              ->where('ventadetalle.idventa',$venta->id)
              ->select(
                'ventadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();
            return view('layouts/backoffice/cotizacion/detalle',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $agencias = DB::table('agencia')->get();
            $formapagos = DB::table('formapago')->get();
            $ventadetalles = DB::table('ventadetalle')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
              ->where('ventadetalle.idventa',$venta->id)
              ->select(
                'ventadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();
            return view('layouts/backoffice/cotizacion/delete',[
                'agencias' => $agencias,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
            ]);
        }
    }

    public function update(Request $request, $idventa)
    {
        //$request->user()->authorizeRoles( $request->path() );
      
      
        if($request->input('view') == 'editar') {
            $rules = [
              'idcliente' => 'required',
              'idformapago' => 'required',
              'idestado' => 'required',
              'productos' => 'required',
            ];
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'idformapago.required' => 'La "Formad de Pago" es Obligatorio.',
              'idestado.required' => 'El "Estado" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Precio Unitario es 0.00.'
                    ]);
                    break;
                }elseif($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligatorio.'
                    ]);
                    break;
                }
              
                /*if($request->input('idestado')==2){
                    $ventas = DB::table('ventadetalle')
                      ->join('venta','venta.id','ventadetalle.idventa')
                      ->where('venta.idtienda',usersmaster()->idtienda)
                      ->where('ventadetalle.idproducto',$item[0])
                      ->where('venta.id','<>',$idventa)
                      ->where('venta.idestado',2)
                      ->sum('ventadetalle.cantidad');
                    $stocktotal = stock_producto(usersmaster()->idtienda,$item[0])['total'];
                    $stocktotal = ($stocktotal-$ventas);
                    if($stocktotal<$item[1]){
                        $producto = DB::table('producto')->whereId($item[0])->first();
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Suficiente Stock del producto "'.$producto->codigoimpresion.' - '.$producto->compatibilidadnombre.'"!!.'
                        ]);
                        break;
                    }
                }*/
            } 
          
            $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
            if($cliente->idtipopersona==2){
                if($cliente->apellidos==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Razón Social del Cliente!!.'
                    ]);
                }
            }
            
            DB::table('venta')->whereId($idventa)->update([
               'idusuariocliente' => $request->input('idcliente'),
               'idusuariovendedor' => Auth::user()->id,
               'idformapago' =>  $request->input('idformapago'),
               'idtienda' =>  usersmaster()->idtienda,
               'idestado' => $request->input('idestado'),
            ]);
            
            DB::table('ventadetalle')->where('idventa',$idventa)->delete();
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('ventadetalle')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'descuento' => '0.00',
                  'preciototal' => $item[4],
                  'idunidadmedida' => $item[3],
                  'idproducto' => $item[0],
                  'idventa' => $idventa,
                ]);
            }  

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'correo') {
            
            $rules = [
              'correo' => 'required',
            ];
            $messages = [
              'correo.required' => 'El "Correo Electrónico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            
            $venta = DB::table('venta')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('venta.id',$idventa)
                ->select(
                    'venta.*',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariovendedor.apellidos as apellidosusuariovendedor',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                    'usuariocliente.direccion as direccionusuariocliente',
                    'usuariocliente.idubigeo as idubigeousuariocliente',
                    'formapago.nombre as nombreFormapago',
                    'moneda.nombre as monedanombre'
                )
                ->first();
          
            $formapagos = DB::table('formapago')->get();
            $ubigeocliente = DB::table('ubigeo')->whereid($venta->idubigeousuariocliente)->first();
            $ventadetalles = DB::table('ventadetalle')
                ->join('producto','producto.id','ventadetalle.idproducto')
                ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
                ->where('ventadetalle.idventa',$venta->id)
                ->select(
                  'ventadetalle.*',
                  'producto.codigoimpresion as producodigoimpresion',
                  'producto.compatibilidadnombre as productonombre',
                  'producto.compatibilidadmotor as productomotor',
                  'producto.compatibilidadmarca as productomarca',
                  'producto.compatibilidadmodelo as productomodelo',
                  'productounidadmedida.nombre as unidadmedidanombre'
                )
                ->orderBy('ventadetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/cotizacion/proforma-pdf',[
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
          
            $output = $pdf->output();
          
            $ventacodigo = str_pad($venta->codigo, 8, "0", STR_PAD_LEFT);
         
            $user = array (
             'name' => 'COTIZACIÓN - '.$ventacodigo,
             'correo' => $request->input('correo'),
             'pdf' => $output,
             'nombrepdf'=>'COTIZACION_'.$ventacodigo.'.pdf'
            );
          
            Mail::send('app/templateemail',  ['user' => $user], function ($message) use ($user) {
              $message->from(usersmaster()->tiendacorreo,  'DICOWE S.A.C.');
              $message->to($user['correo'])->subject($user['name']);
              $message->attachData($user['pdf'], $user['nombrepdf'], [
                      'mime' => 'application/pdf',
                  ]);
            });
          
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha enviado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idventa)
    {
        //$request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            $cotizacion = DB::table('venta')->whereId($idventa)->first();

            if ($cotizacion->idestado == 3) {
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'No se puede eliminar cotizacion en estado venta.'
              ]);
            }
          
            DB::table('ventadetalle')->where('idventa',$idventa)->delete();
            DB::table('venta')
                ->whereId($idventa)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
