<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

class FacturacionguiaremisionController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        $where = [];
        is_null($request->responsable)   || $where[] = ['responsable.nombre', $request->responsable];
        is_null($request->motivo)        || $where[] = ['facturacionguiaremision.envio_descripciontraslado', 'LIKE', '%'.$request->motivo.'%'];
        is_null($request->fecharegistro) || $where[] = ['facturacionguiaremision.despacho_fechaemision', $request->fecharegistro];
        is_null($request->codigo)        || $where[] = ['venta.codigo', $request->codigo];
      
          if($request->input('estado')!=''){
          $where[] = ['facturacionguiaremision.idestadofacturacion',$request->input('estado')];
          }
          if(usersmaster()->id!=1){
          $where[] = ['facturacionguiaremision.idtienda',usersmaster()->idtienda];  
          }
        
        $facturacionguiaremisiones = DB::table('facturacionguiaremision')
          ->join('users as responsable', 'responsable.id', 'facturacionguiaremision.idusuarioresponsable')
          ->leftJoin('venta', 'venta.id', 'facturacionguiaremision.idventa')
          ->where($where)
          ->whereRaw("CONCAT(facturacionguiaremision.despacho_destinatario_numerodocumento,' - ',facturacionguiaremision.despacho_destinatario_razonsocial) LIKE '%".$request->cliente."%'")
          ->select(
              'facturacionguiaremision.*',
              'responsable.nombre as responsable_nombre'
          )
          ->orderBy('facturacionguiaremision.id','desc')
          ->paginate(10);
      
        return view('layouts/backoffice/facturacionguiaremision/index',[
            'facturacionguiaremisiones' => $facturacionguiaremisiones
        ]);
      }else {
        return view('errors.error-facturacion');
      }
    }
  
    public function create(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if (usersmaster()->idestadosunat == 2) { 
            $series = DB::table('tienda')->where('tienda.id',usersmaster()->idtienda)->where('facturador_serie','<>','')->get();
            $motivos = DB::table('motivoguiaremision')->get();
            $agencias = DB::table('agencia')->get();
            $agencia = DB::table('agencia')
              ->where('idestado',1)
              ->first();
            if($request->input('view') == 'registrar') {
                return view('layouts/backoffice/facturacionguiaremision/create',[
                  'agencias' => $agencias,
                  'agencia' => $agencia,
                  'motivos' => $motivos
                ]);
            }
       }else {
          return view('errors.error-facturacion');
       }
    }
  
    public function store(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        if($request->input('view') == 'registrar') {
            $post_productos = json_decode($request->productos);
        
            $rules = [
                'emisor'           => 'required',
                'destinatario'     => 'required',
                'partidaubigeo'    => 'required',
                'llegadaubigeo'    => 'required',
                'direccionpartida' => 'required',
                'direccionllegada' => 'required',
                'motivo'           => 'required',
                'fechaemision'     => 'required',
                'fechatraslado'    => 'required',
                'transportista'    => 'required',
//                 'observacion'      => 'required',
                'productos'        => 'required',
            ];          
            $messages = [
                'emisor.required'           => 'El "Remitente" es Obligatorio.',
                'destinatario.required'     => 'El "Destinatario" es Obligatorio.',
                'partidaubigeo.required'    => 'El "Punto de Partida" es Obligatorio.',
                'llegadaubigeo.required'    => 'El "Punto de Llegada" es Obligatorio.',
                'direccionpartida.required' => 'La "Dirección de Partida" es Obligatorio.',
                'direccionllegada.required' => 'La "Dirección de Llegada" es Obligatorio.',
                'motivo.required'           => 'El "Motivo" es Obligatorio.',
                'fechaemision.required'     => 'La "Fecha de Emisión" es Obligatorio.',
                'fechatraslado.required'    => 'La "Fecha de Traslado" es Obligatorio.',
                'transportista.required'    => 'El "Transportista" es Obligatorio.',
//                 'observacion.required'      => 'La "Observación" es Obligatorio.',
                'productos.required'        => 'Los "Productos" son Obligatorios.',
            ];
            $this->validate($request,$rules,$messages);
          
            $usersmaster =usersmaster();
            
            $facturacion              = DB::table('facturacionguiaremision')->where('idventa', $request->input('idventa'))->first();
            $agencia                  = DB::table('agencia')->whereId($request->input('emisor'))->first();
            $ubigeoagencia            = DB::table('ubigeo')->whereId($usersmaster->idubigeo)->first();
            $destinatario             = DB::table('users')->whereId($request->input('destinatario'))->first();
            $tipopersonadestinatario  = DB::table('tipopersona')->whereId($destinatario->idtipopersona)->first();
            $transportista            = DB::table('users')->whereId($request->input('transportista'))->first();
            $tipopersonatransportista = DB::table('tipopersona')->whereId($transportista->idtipopersona)->first();
            $motivo                   = DB::table('motivoguiaremision')->whereId($request->input('motivo'))->first();
            $ubigeopartida            = DB::table('ubigeo')->whereId($request->input('partidaubigeo'))->first();
            $ubigeollegada            = DB::table('ubigeo')->whereId($request->input('llegadaubigeo'))->first();
            $idtienda                 = $usersmaster->idtienda;
    
            $tienda = DB::table('tienda')
                    ->join('ubigeo','ubigeo.id','tienda.idubigeo')
                    ->where('tienda.id',$idtienda)
                    ->select(
                        'tienda.*',
                        'tienda.direccion as tiendadireccion',
                        'tienda.facturador_serie as tiendaserie',
                        'ubigeo.codigo as tiendaubigeocodigo',
                        'ubigeo.distrito as tiendaubigeodistrito',
                        'ubigeo.provincia as tiendaubigeoprovincia',
                        'ubigeo.departamento as tiendaubigeodepartamento'
                    )
                    ->first();
     
            $guiaremision_serie = 'T'.str_pad($tienda->tiendaserie, 3, "0", STR_PAD_LEFT);
           
            $correlativo = DB::table('facturacionguiaremision')
                ->where('facturacionguiaremision.idtienda',$idtienda)
                ->orderBy('facturacionguiaremision.guiaremision_correlativo','desc')
                ->first();
 
            $correlativo = ($correlativo == '') ? 0 : $correlativo->guiaremision_correlativo;
                    
            $guiaremision_correlativo = $correlativo + 1;

            if(empty($post_productos)){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La cantidad minímo es 1.'
                ]);
            }
          
            if($destinatario->idtipopersona==2){
                if($destinatario->apellidos==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Razón Social del Destinatario!!.'
                    ]);
                }
            }
          
            $destinatario_nombre = ($destinatario->idtipopersona == 1) ? $destinatario->apellidos.', '.$destinatario->nombre : $destinatario->apellidos;
    
            $idfacturacionguiaremision = DB::table('facturacionguiaremision')->insertGetId([
                'emisor_ruc'                            => $agencia->ruc,
                'emisor_razonsocial'                    => $agencia->razonsocial,
                'emisor_nombrecomercial'                => $agencia->nombrecomercial,
                'emisor_ubigeo'                         => $usersmaster->idubigeo,
                'emisor_departamento'                   => $usersmaster->ubigeodepartamento,
                'emisor_provincia'                      => $usersmaster->ubigeoprovincia,
                'emisor_distrito'                       => $usersmaster->ubigeodistrito,
                'emisor_urbanizacion'                   => '',
                'emisor_direccion'                      => $usersmaster->tiendadireccion,
                'despacho_tipodumento'                  => '',
                'despacho_serie'                        => '',
                'despacho_correlativo'                  => '',
                'despacho_fechaemision'                 => $request->input('fechaemision'),
                'despacho_destinatario_tipodocumento'   => ($destinatario->idtipopersona == 1) ? 1 : 6,
                'despacho_destinatario_numerodocumento' => $destinatario->identificacion,
                'despacho_destinatario_razonsocial'     => $destinatario_nombre,
                'despacho_tercero_tipodocumento'        => '',
                'despacho_tercero_numerodocumento'      => '',
                'despacho_tercero_razonsocial'          => '',
                'despacho_observacion'                  => !is_null($request->input('observacion')) ? $request->input('observacion') : '',
                'transporte_tipodocumento'              => 6,
                'transporte_numerodocumento'            => $agencia->ruc,
                'transporte_razonsocial'                => $agencia->razonsocial,
                'transporte_placa'                      => '',
                'transporte_chofertipodocumento'        => 1,
                'transporte_choferdocumento'            => $transportista->identificacion,
                'envio_codigotraslado'                  => $motivo->codigo,
                'envio_descripciontraslado'             => $motivo->nombre,
                'envio_modtraslado'                     => '01',
                'envio_fechatraslado'                   => $request->input('fechatraslado'),
                'envio_codigopuerto'                    => '',
                'envio_indtransbordo'                   => '',
                'envio_pesototal'                       => 0,
                'envio_unidadpesototal'                 => 'KGM',
                'envio_numerocontenedor'                => '',
                'envio_direccionllegadacodigoubigeo'    => $ubigeollegada->codigo,
                'envio_direccionllegada'                => $request->input('direccionllegada'),
                'envio_direccionpartidacodigoubigeo'    => $ubigeopartida->codigo,
                'envio_direccionpartida'                => $request->input('direccionpartida'),
                'guiaremision_tipodocumento'            => '09',
                'guiaremision_serie'                    => $guiaremision_serie,
                'guiaremision_correlativo'              => $guiaremision_correlativo,
                'guiaremision_fechaemision'             => '',
                'estadofacturacion'                     => '',
                'idestadofacturacion'                   => 0,
                'idfacturacionboletafactura'            => !is_null($request->input('idfacturacion')) ? $request->input('idfacturacion') : 0,
                'idventa'                               => !is_null($request->input('idventa')) ? $request->input('idventa') : 0,
                'idtransferencia'                       => 0,
                'idagencia'                             => $agencia->id,
                'idtienda'                              => $idtienda,
                'idusuarioresponsable'                  => Auth::user()->id,
                'idestadosunat'                         => 0, 
            ]);            
            
            foreach ($post_productos as $item) {
               if($item->cantidad <= 0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                }
              
               DB::table('facturacionguiaremisiondetalle')->insert([
                  'cantidad'                  => $item->cantidad,
                  'unidad'                    => $item->unidadmedida,
                  'descripcion'               => $item->descripcion,
                  'codigo'                    => $item->codigo,
                  'codprodsunat'              => '',
                  'idproducto'                => $item->idproducto,
                  'idfacturacionguiaremision' => $idfacturacionguiaremision
               ]);
            }
            
           $resultado = facturador_guiaremision($idfacturacionguiaremision);
          
           return response()->json([
             'resultado' => $resultado['resultado'],
             'mensaje'   => $resultado['mensaje']
           ]);
        }
      }else {
          return view('errors.error-facturacion');
      }
    }

    public function show(Request $request, $id) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-seleccionarventa') {
          $venta        = [];
          $ventadetalle = [];
          $where_exist  = [];
          if ($request->input('idestadodocumento') == 'venta') {
            $query_venta = DB::table('venta')
              ->join('users as cliente', 'cliente.id', 'venta.idusuariocliente')
              ->where('venta.codigo', $request->input('codigoventa'))
              ->where('venta.idtienda', usersmaster()->idtienda)
              ->where('venta.idestado', 3)
              ->select('venta.*', 'cliente.identificacion as cliente_identificacion', 'cliente.nombre as cliente_nombre', 'cliente.apellidos as cliente_apellidos')
              ->first();
   
            if (!is_null($query_venta)) {
              $where_exist[] = ['facturacionguiaremision.idventa', $query_venta->id];
              $query_ventadetalle = DB::table('ventadetalle')
                ->join('producto', 'producto.id', 'ventadetalle.idproducto')
                ->join('productounidadmedida', 'productounidadmedida.id', 'ventadetalle.idunidadmedida')
                ->where('ventadetalle.idventa', $query_venta->id)
                ->select('ventadetalle.*', 
                         'productounidadmedida.nombre as unidadmedida_nombre', 
                         'productounidadmedida.codigo as unidadmedida_codigo', 
                         'producto.compatibilidadnombre as producto_compatibilidadnombre', 
                         'producto.codigoimpresion as producto_codigoimpresion')
                ->get();
              
              foreach ($query_ventadetalle as $item) {
                $stock = stock_producto($query_venta->idtienda, $item->idproducto);
                $ventadetalle[] = [
                  'id'             => $item->id,
                  'codigo'         => $item->producto_codigoimpresion,
                  'descripcion'    => $item->producto_compatibilidadnombre,
                  'unidadmedida'   => $item->unidadmedida_codigo,
                  'stock'          => $stock['total'],
                  'cantidad'       => $item->cantidad,
                  'preciounitario' => $item->preciounitario,
                  'preciototal'    => $item->preciototal,
                  'idproducto'     => $item->idproducto
                ];
              }
              
              $venta = [
                'tipo'                   => 'venta',
                'id'                     => $query_venta->id,
                'cliente_id'             => $query_venta->idusuariocliente,
                'cliente_identificacion' => $query_venta->cliente_identificacion,
                'cliente_nombre'         => $query_venta->cliente_nombre,
                'cliente_apellidos'      => $query_venta->cliente_apellidos,
              ];
            } 
              
          }else if ($request->input('idestadodocumento') == 'compra') {
            $query_compra = DB::table('compra')
              ->join('users as cliente', 'cliente.id', 'compra.idusuarioproveedor')
              ->where('compra.codigo', $request->input('codigocompra'))
              ->where('compra.idtienda', usersmaster()->idtienda)
              ->where('compra.idestado', 2)
              ->select('compra.*', 'cliente.identificacion as cliente_identificacion', 'cliente.nombre as cliente_nombre', 'cliente.apellidos as cliente_apellidos')
              ->first();
            
   
            if (!is_null($query_compra)) {
              $where_exist[] = ['facturacionguiaremision.idcompra', $query_compra->id];
              $query_compradetalle = DB::table('compradetalle')
                ->join('producto', 'producto.id', 'compradetalle.idproducto')
                ->join('productounidadmedida', 'productounidadmedida.id', 'compradetalle.idunidadmedida')
                ->where('compradetalle.idcompra', $query_compra->id)
                ->select('compradetalle.*', 
                         'productounidadmedida.nombre as unidadmedida_nombre', 
                         'productounidadmedida.codigo as unidadmedida_codigo', 
                         'producto.compatibilidadnombre as producto_compatibilidadnombre', 
                         'producto.codigoimpresion as producto_codigoimpresion')
                ->get();
              
              foreach ($query_compradetalle as $item) {
                $stock = stock_producto($query_compra->idtienda, $item->idproducto);
                $ventadetalle[] = [
                  'id'             => $item->id,
                  'codigo'         => $item->producto_codigoimpresion,
                  'descripcion'    => $item->producto_compatibilidadnombre,
                  'unidadmedida'   => $item->unidadmedida_codigo,
                  'stock'          => $stock['total'],
                  'cantidad'       => $item->cantidad,
                  'preciounitario' => $item->preciounitario,
                  'preciototal'    => $item->preciototal,
                  'idproducto'     => $item->idproducto
                ];
              }
              
              $venta = [
                'tipo'                   => 'compra',
                'id'                     => $query_compra->id,
                'cliente_id'             => $query_compra->idusuarioproveedor,
                'cliente_identificacion' => $query_compra->cliente_identificacion,
                'cliente_nombre'         => $query_compra->cliente_nombre,
                'cliente_apellidos'      => $query_compra->cliente_apellidos,
              ];
            } 
              
          }else if ($request->input('idestadodocumento') == 'boletafactura') {
            $query_facturacion = DB::table('facturacionboletafactura')
              ->join('users as cliente', 'cliente.id', 'facturacionboletafactura.idusuariocliente')
              ->where('facturacionboletafactura.idtienda', usersmaster()->idtienda)
              ->where([
                ['facturacionboletafactura.venta_serie', '=', $request->input('serie')],
                ['facturacionboletafactura.venta_correlativo', '=', $request->input('correlativo')]
              ])
               ->select('facturacionboletafactura.*', 'cliente.identificacion as cliente_identificacion', 'cliente.nombre as cliente_nombre', 'cliente.apellidos as cliente_apellidos')
              ->first();
            
            if (!is_null($query_facturacion)) {
              $where_exist[] = ['facturacionguiaremision.idfacturacionboletafactura', $query_facturacion->id];
              $query_facturaciondetalle = DB::table('facturacionboletafacturadetalle')->where('facturacionboletafacturadetalle.idfacturacionboletafactura', $query_facturacion->id)->get();
              foreach ($query_facturaciondetalle as $item) {
                  $stock = stock_producto($query_facturacion->idtienda, $item->idproducto);
                  $ventadetalle[] = [
                    'id'             => $item->id,
                    'codigo'         => $item->codigoproducto,
                    'descripcion'    => $item->descripcion,
                    'unidadmedida'   => $item->unidad,
                    'stock'          => $stock['total'],
                    'cantidad'       => $item->cantidad,
                    'preciounitario' => $item->montopreciounitario,
                    'preciototal'    => number_format($item->montopreciounitario * $item->cantidad, 2, '.', ''),
                    'idproducto'     => $item->idproducto,
                  ];
              }
              
              $venta  = [
                'tipo'                   => 'facturaboleta',
                'id'                     => $query_facturacion->id,
                'idusuariocliente'       => $query_facturacion->idusuariocliente,
                'cliente_id'             => $query_facturacion->idusuariocliente,
                'cliente_identificacion' => $query_facturacion->cliente_identificacion,
                'cliente_nombre'         => $query_facturacion->cliente_nombre,
                'cliente_apellidos'      => $query_facturacion->cliente_apellidos,
              ];
            }

          }
//           else if ($request->input('idestadodocumento') == 'transferencia') {
//             $query_transferencia = DB::table('productotransferencia')
//               ->where('productotransferencia.codigo', $request->input('codigotransferencia'))
//               ->select(
//                 'productotransferencia.*'
//               )
//               ->first();
            
//             $agencia_destino = DB::table()->first();
//             if (!is_null($query_transferencia)) {
//               $where_exist[] = ['facturacionguiaremision.idtransferencia', $query_transferencia->id];
//               $query_transferenciadetalle = DB::table('productotransferenciadetalle')
//                 ->join('producto', 'producto.id', 'productotransferenciadetalle.idproducto')
//                 ->join('productounidadmedida', 'productounidadmedida.id', 'producto.idproductounidadmedida')
//                 ->where('productotransferenciadetalle.idproductotransferencia', $query_transferencia->id)
//                 ->select(
//                   'productotransferenciadetalle.*',
//                   'producto.codigoimpresion',
//                   'producto.compatibilidadnombre',
//                   'productounidadmedida.codigo'
//                 )
//                 ->get();
              
//               foreach ($query_transferenciadetalle as $item) {
// //                   $stock = stock_producto($query_transferenciadetalle->idtienda, $item->idproducto);
//                   $ventadetalle[] = [
//                     'codigo'         => $item->codigoimpresion,
//                     'descripcion'    => $item->compatibilidadnombre,
//                     'unidadmedida'   => $item->codigo,
//                     'stock'          => '',
//                     'cantidad'       => $item->cantidadenviado,
//                     'preciounitario' => '',
//                     'preciototal'    => '',
//                     'idproducto'     => $item->idproducto,
//                   ];
//               }
              
//               $venta  = [
//                   'tipo'                   => 'facturaboleta',
//                   'id'                     => $query_transferencia->id,
//                   'cliente_id'             => $query_transferencia->respdestino_id,
//                   'cliente_identificacion' => $query_transferencia->respdestino_identificacion,
//                   'cliente_nombre'         => $query_transferencia->respdestino_nombre,
//                   'cliente_apellidos'      => $query_transferencia->respdestino_apellido,
//                ];
//             }
//           }
      
          $exist_facturacion = DB::table('facturacionguiaremision')->where($where_exist)->exists();
      
          if (!$exist_facturacion  && !empty($venta)) {
            return [
              'venta' => $venta,
              'ventadetalle' => $ventadetalle,
            ];
          }
      
        }
        elseif($id == 'show-seleccionarventadetalle'){
            $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$request->input('idfacturacionboletafactura'))
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();          
            return [ 
              'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles
            ];
        }
        elseif($id == 'show-ubigeo'){
            $ubigeos = DB::table('ubigeo')
                ->where('ubigeo.departamento','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.provincia','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.distrito','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'ubigeo.id as id',
                   DB::raw('CONCAT(ubigeo.nombre) as text')
                )
                ->get();
            return $ubigeos;
        }
        elseif($id == 'show-listarcliente'){
            $usuarios = DB::table('users')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'users.id as id',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.nombre)) as text')
                )
                ->get();
          
            return $usuarios;
        }
        elseif($id == 'show-listartransportista'){
            $usuarios = DB::table('users')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->where('users.idtipopersona', 1) 
                ->orWhere('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('users.idtipopersona', 1) 
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->where('users.idtipopersona', 1) 
                ->select(
                    'users.id as id',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.nombre)) as text')
                )
                ->get();
          
            return $usuarios;
        }
        elseif($id == 'show-seleccionarcliente'){
            $usuario = DB::table('users')
                ->join('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.id',$request->input('idcliente'))
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
          
            return [ 
              'cliente' => $usuario
            ];
        }
        elseif($id == 'show-agregarventacodigo'){        
            $ventadetalles = DB::table('ventadetalle')
              ->join('venta','venta.id','ventadetalle.idventa')
              ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
              ->where('venta.codigo',$request->input('codigoventa'))
              ->select(
                'ventadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre',
                'venta.idtipocomprobante as tipocomprobante',
                'tipocomprobante.nombre as tipocomprobantenombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();            
          
            $ventadetal = [];
            $idtienda = usersmaster()->idtienda;
            foreach($ventadetalles as $value){
                $ventadetal[] = [
                     "id" => $value->idproducto,
                     "producodigoimpresion" => $value->producodigoimpresion,
                     "productonombre" => $value->productonombre,
                     "productomotor" => $value->productomotor,
                     "productomarca" => $value->productomarca,
                     "productomodelo" => $value->productomodelo,
                     "preciounitario" => $value->preciounitario,
                     "idunidadmedida" => $value->idunidadmedida,
                     "unidadmedidanombre" => $value->unidadmedidanombre,
                     "stock" => stock_producto($idtienda,$value->idproducto)['total'],
                     "cantidad" => $value->cantidad,
                     "preciototal" => $value->preciototal,
                     "tipocomprobante" => $value->tipocomprobante,
                     "tipocomprobantenombre" => $value->tipocomprobantenombre
                ];
            }
            return [ 
              'ventadetalles' => (object)$ventadetal ,
            ];
        }
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        $facturacionguiaremision = DB::table('facturacionguiaremision')
            ->leftJoin('venta','venta.id','facturacionguiaremision.idventa')
            ->where('facturacionguiaremision.id',$id)
            ->select(
                'facturacionguiaremision.*',
                'venta.numeroplaca as ventanumeroplaca'
            )
            ->first();
      
        $facturacionguiaremisiondetalles = DB::table('facturacionguiaremisiondetalle')
                ->where('facturacionguiaremisiondetalle.idfacturacionguiaremision',$facturacionguiaremision->id)
                ->orderBy('facturacionguiaremisiondetalle.id','asc')
                ->get();
      
        if( $request->input('view') == 'comprobante' ){
            return view('layouts/backoffice/facturacionguiaremision/comprobante',[
                'facturacionguiaremision' => $facturacionguiaremision,
            ]);
        }
        elseif( $request->input('view') == 'comprobante-pdf' ) {
            $pdf = PDF::loadView('layouts/backoffice/facturacionguiaremision/comprobante-pdf',[
                'facturacionguiaremision' => $facturacionguiaremision,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles
            ]);
            return $pdf->stream();
        }
        elseif ($request->input('view') == 'reenviarsunat') {
           $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
     
           $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
           return view('layouts/backoffice/facturacionguiaremision/reenviarsunat',[
                'facturacionguiaremision' => $facturacionguiaremision,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'ubigeo_partida' => $ubigeo_partida,
                'ubigeo_llegada' => $ubigeo_llegada,
           ]);
        }
        elseif ($request->input('view') == 'detalle') {
           $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
     
           $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
          
           return view('layouts/backoffice/facturacionguiaremision/detalle',[
               'facturacionguiaremision' => $facturacionguiaremision,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'ubigeo_partida' => $ubigeo_partida,
                'ubigeo_llegada' => $ubigeo_llegada,
           ]);
        }
      }else {
          return view('errors.error-facturacion');
      }
    }

    public function update(Request $request, $idfacturacionguiaremision)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
          if ( $request->input('view') == 'reenviarsunat' ) {

             $resultado = facturador_guiaremision($idfacturacionguiaremision);      

             return response()->json([
               'resultado' => $resultado['resultado'],
               'mensaje'   => $resultado['mensaje']
             ]);

          }
          elseif( $request->input('view') == 'correo' ) {

                $rules    = [ 'correo'          => 'required' ];
                $messages = [ 'correo.required' => 'El "Correo Electrónico" es Obligatorio.' ];

                $this->validate( $request, $rules, $messages );

                $facturacionguiaremision = DB::table('facturacionguiaremision')
                    ->leftJoin('venta', 'venta.id', 'facturacionguiaremision.idventa')
                    ->where('facturacionguiaremision.id', $idfacturacionguiaremision)
                    ->select( 'facturacionguiaremision.*', 'venta.codigo as ventacodigo')
                    ->first();

                $facturacionguiaremisiondetalles = DB::table('facturacionguiaremisiondetalle')
                    ->orderBy('facturacionguiaremisiondetalle.id','asc')
                    ->get();

                $pdf = PDF::loadView( 'layouts/backoffice/facturacionguiaremision/comprobante-pdf', [
                    'facturacionguiaremision'         => $facturacionguiaremision,
                    'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                ]);

                $output = $pdf->output();

                $comprobante = '';

                if( $facturacionguiaremision->guiaremision_tipodocumento == '09' ){
                    $comprobante = 'GUIA DE REMISIÓN';
                }
                else{
                    $comprobante = 'GUIA DE REMISIÓN';
                } 

                $user = array (
                   'name'      => $comprobante.' - '.$facturacionguiaremision->guiaremision_serie.'-'.str_pad($facturacionguiaremision->guiaremision_correlativo, 6, "0", STR_PAD_LEFT),
                   'correo'    => $request->input('correo'),
                   'pdf'       => $output,
                   'nombrepdf' => $comprobante.'_'.$facturacionguiaremision->guiaremision_serie.'_'.str_pad($facturacionguiaremision->guiaremision_correlativo, 6, "0", STR_PAD_LEFT).'.pdf'
                );

                Mail::send( 'app/templateemail',  [ 'user' => $user ], function ( $message ) use ( $user ) {
                    $message->from('ventas@dicowe.com.pe', 'DICOWE S.A.C.');
                    $message->to( $user[ 'correo' ] )->subject( $user[ 'name' ] );
                    $message->attachData( $user['pdf' ], $user[ 'nombrepdf' ], [ 'mime' => 'application/pdf' ] );
                });          

                return response()->json([
                  'resultado' => 'CORRECTO',
                  'mensaje'   => 'Se ha enviado correctamente.'
                ]);
            }
      }else {
          return view('errors.error-facturacion');
      }
    }

    public function destroy(Request $request, $idfacturacionnotacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
    }
}
