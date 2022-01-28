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

class FacturacionBoletafacturaController extends Controller
{
    
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if (usersmaster()->idestadosunat == 2) { 
          $where = [];
          /*if($request->input('codigoventa')!=''){
              $where[] = ['venta.codigo','LIKE','%'.$request->input('codigoventa').'%'];
          }*/
          $where[] = ['facturacionboletafactura.venta_fechaemision','LIKE','%'.$request->input('fechaemision').'%'];
          $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
          $where[] = ['facturacionboletafactura.venta_tipodocumento','LIKE','%'.$request->input('comprobante').'%'];
          $where[] = ['facturacionboletafactura.venta_serie','LIKE','%'.$request->input('serie').'%'];
          $where[] = ['facturacionboletafactura.venta_correlativo','LIKE','%'.$request->input('correlativo').'%'];
          $where[] = ['facturacionboletafactura.cliente_numerodocumento','LIKE','%'.$request->input('clienteidentificacion').'%'];
          $where[] = ['facturacionboletafactura.cliente_razonsocial','LIKE','%'.$request->input('cliente').'%'];
          $where[] = ['facturacionboletafactura.venta_tipomoneda','LIKE','%'.$request->input('moneda').'%'];
          if($request->input('estado')!=''){
          $where[] = ['facturacionboletafactura.idestadofacturacion',$request->input('estado')];
          }
          if(usersmaster()->id!=1){
          $where[] = ['facturacionboletafactura.idtienda',usersmaster()->idtienda];  
          }


          $facturacionboletafacturas = DB::table('facturacionboletafactura')
              ->join('users as responsable','responsable.id','facturacionboletafactura.idusuarioresponsable')
              ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
              ->where($where)
              ->select(
                  'facturacionboletafactura.*',
                  'venta.codigo as ventacodigo',
                  'responsable.nombre as responsablenombre'
              )
              ->orderBy('facturacionboletafactura.id','desc')
              ->paginate(10);
                  
          return view('layouts/backoffice/facturacionboletafactura/index',[
              'facturacionboletafacturas' => $facturacionboletafacturas
          ]);
       }else { 
         return view('errors.error-facturacion');
       }
    }
  
    public function create(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        if($request->input('view') == 'registrar') {
            $agencias = DB::table('agencia')->get();
            $agencia = DB::table('agencia')->where('idestado',1)->first();
            $tipocomprobantes = DB::table('tipocomprobante')->where('id',1)->orWhere('id',2)->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/facturacionboletafactura/create', [
              'agencias' => $agencias,
              'agencia' => $agencia,
              'tipocomprobantes' => $tipocomprobantes,
              'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'registrar-cliente') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/facturacionboletafactura/cliente',[
                'tipopersonas' => $tipopersonas
            ]);
        }elseif($request->input('view') == 'productos') {
            return view('layouts/backoffice/facturacionboletafactura/productos');
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
          
            $rules = [
                'idcliente' => 'required',
                'clientedireccion' => 'required',
                'clienteidubigeo' => 'required',
                'idagencia' => 'required',
                'idtipocomprobante' => 'required',
                'idmoneda' => 'required',
                'productos' => 'required',
            ];
            
            $messages = [
                'idventa.required' => 'La "Venta" es Obligatorio.',
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'clientedireccion.required' => 'La "Dirección" es Obligatorio.',
                'clienteidubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'idagencia.required' => 'La "Agencia" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'idtipocomprobante.required' => 'El "Tipo de comprobante" es Obligatorio.',
                'productos.required' => 'Los "Porductos" son Obligatorio.',
            ];
          
            $this->validate($request,$rules,$messages);

            if($request->input('idtipocomprobante')==2){ // Factura
                $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
                if($cliente->idtipopersona<>2){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Cliente debe tener un RUC!!.'
                    ]);
                }
            }elseif($request->input('idtipocomprobante')==1){ // Boleta
                $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
                if($cliente->idtipopersona<>1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Cliente debe tener un DNI!!.'
                    ]);
                }
            }
          
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
            }
          
            $idtienda = usersmaster()->idtienda;
            // CAJA
            $idaperturacierre = 0;
            if($request->input('idestado')==2){
                $caja = caja($idtienda,Auth::user()->id);
                if($caja['apertura']==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Caja debe estar Aperturada.'
                    ]);
                }
                $idaperturacierre = $caja['apertura']->id;
            }
            
            // CLIENTE
            DB::table('users')->whereId($request->input('idcliente'))->update([
               'direccion' => $request->input('clientedireccion'),
               'idubigeo' => $request->input('clienteidubigeo')
            ]);

            // FACTURACION
                $agencia = DB::table('agencia')
                    ->where('agencia.id',$request->input('idagencia'))
                    ->first();
              
                $cliente = DB::table('users')
                    ->where('users.id',$request->input('idcliente'))
                    ->first();
          
                $clienteubigeo = DB::table('ubigeo')
                    ->where('ubigeo.id',$request->input('clienteidubigeo'))
                    ->first();
              
                $tienda = DB::table('tienda')
                    ->join('ubigeo','ubigeo.id','tienda.idubigeo')
                    ->where('tienda.id',$idtienda)
                    ->select(
                        'tienda.direccion as tiendadireccion',
                        'tienda.facturador_serie as tiendaserie',
                        'ubigeo.codigo as tiendaubigeocodigo',
                        'ubigeo.distrito as tiendaubigeodistrito',
                        'ubigeo.provincia as tiendaubigeoprovincia',
                        'ubigeo.departamento as tiendaubigeodepartamento'
                    )
                    ->first();
              
                if($cliente->idtipopersona==1) {
                    $cliente_tipodocumento = '1';
                    $cliente_razonsocial = $cliente->apellidos.', '.$cliente->nombre;
                }elseif($cliente->idtipopersona==2) {
                    $cliente_tipodocumento = '6';
                    $cliente_razonsocial = $cliente->apellidos;
                }
              
                if($request->input('idtipocomprobante')==1) {
                    $venta_tipodocumento = '03';
                    $venta_serie = 'B'.str_pad($tienda->tiendaserie, 3, "0", STR_PAD_LEFT);
                }elseif($request->input('idtipocomprobante')==2) {
                    $venta_tipodocumento = '01';
                    $venta_serie = 'F'.str_pad($tienda->tiendaserie, 3, "0", STR_PAD_LEFT);
                }
          
                $correlativo = DB::table('facturacionboletafactura')
                    ->where('venta_tipodocumento',$venta_tipodocumento)
                    ->where('emisor_ruc',$agencia->ruc)
                    ->where('venta_serie',$venta_serie)
                    ->orderBy('venta_correlativo','desc')
                    ->limit(1)
                    ->first();
              
                if($correlativo!=''){
                    $venta_correlativo = $correlativo->venta_correlativo+1;
                }else{
                    $venta_correlativo = 1;
                }
           
                $moneda = DB::table('moneda')->whereId($request->input('idmoneda'))->first();
          
                $productos = explode('&', $request->input('productos'));
                $total_preciounitario = 0;
                $total_precioventa = 0;
                $total_valorunitario = 0;
                $total_valorventa = 0;
                $total_impuesto = 0;
                for($i = 1; $i < count($productos); $i++){
                    $item = explode(',',$productos[$i]);
                  
                    $cantidad = $item[1];
                    $preciounitario = number_format($item[2],2, '.', '');
                    $precioventa = number_format($preciounitario*$cantidad,2, '.', '');
                    $valorunitario = number_format(($preciounitario/1.18),2, '.', '');
                    $valorventa = number_format($valorunitario*$cantidad,2, '.', '');
                    $igv = number_format($precioventa-$valorventa,2, '.', '');
                  
                    $total_preciounitario = $total_preciounitario+$preciounitario;
                    $total_precioventa = $total_precioventa+$precioventa;
                    $total_valorunitario = $total_valorunitario+$valorunitario;
                    $total_valorventa = $total_valorventa+$valorventa;
                    $total_impuesto = $total_impuesto+$igv;
                } 
              
                $idfacturacionboletafactura = DB::table('facturacionboletafactura')->insertGetId([
                    'emisor_ruc' => $agencia->ruc,
                    'emisor_razonsocial' => $agencia->razonsocial,
                    'emisor_nombrecomercial' => $agencia->nombrecomercial,
                    'emisor_ubigeo' => $tienda->tiendaubigeocodigo,
                    'emisor_departamento' => $tienda->tiendaubigeodepartamento,
                    'emisor_provincia' => $tienda->tiendaubigeoprovincia,
                    'emisor_distrito' => $tienda->tiendaubigeodistrito,
                    'emisor_urbanizacion' => '',
                    'emisor_direccion' => $tienda->tiendadireccion,
                    'cliente_tipodocumento' => $cliente_tipodocumento,
                    'cliente_numerodocumento' => $cliente->identificacion,
                    'cliente_razonsocial' => $cliente_razonsocial,
                    'cliente_ubigeo' => $clienteubigeo->codigo,
                    'cliente_departamento' => $clienteubigeo->departamento,
                    'cliente_provincia' => $clienteubigeo->provincia,
                    'cliente_distrito' => $clienteubigeo->distrito,
                    'cliente_urbanizacion' => '',
                    'cliente_direccion' => $cliente->direccion,
                    'venta_ublversion' => '',
                    'venta_tipooperacion' => '0101',
                    'venta_tipodocumento' => $venta_tipodocumento,
                    'venta_serie' => $venta_serie,
                    'venta_correlativo' => ($venta_serie == 'F009' && $venta_correlativo == 7) ? 8 : $venta_correlativo,
//                     'venta_correlativo' => $venta_correlativo,
                    'venta_fechaemision' => Carbon::now(),
                    'venta_tipomoneda' => $moneda->codigo,
                    'venta_montooperaciongravada' => number_format($total_valorventa,2, '.', ''),
                    'venta_montoigv' => number_format($total_impuesto,2, '.', ''),
                    'venta_totalimpuestos' => number_format($total_impuesto,2, '.', ''),
                    'venta_valorventa' => number_format($total_valorventa,2, '.', ''),
                    'venta_montoimpuestoventa' => number_format($total_precioventa,2, '.', ''),
                    'venta_qr' => '',
                    'leyenda_codigo' => '1000',
                    'leyenda_value' => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$moneda->nombre,
                    'idestadofacturacion' => 0,
                    'idventa' => 0,
                    'idagencia' => $request->input('idagencia'),
                    'idtienda' => usersmaster()->idtienda,
                    'idusuarioresponsable' => Auth::user()->id,
                    'idusuariocliente' => $request->input('idcliente'),
                    'idestadosunat' => 1 // pendiente
                ]);
          
                $productos = explode('&', $request->input('productos'));
                for($i = 1; $i < count($productos); $i++){
                    $item = explode(',',$productos[$i]);
                  
                    $producto = DB::table('producto')->whereId($item[0])->first();
                    $productounidadmedida = DB::table('productounidadmedida')->whereId($item[3])->first();
                    
                    $cantidad = $item[1];
                    $preciounitario = number_format($item[2],2, '.', '');
                    $precioventa = number_format($preciounitario*$cantidad,2, '.', '');
                    $valorunitario = number_format(($preciounitario/1.18),2, '.', '');
                    $valorventa = number_format($valorunitario*$cantidad,2, '.', '');
                    $igv = number_format($precioventa-$valorventa,2, '.', '');
                  
             
                    DB::table('facturacionboletafacturadetalle')->insert([
                        'codigoproducto' => str_pad($producto->codigoimpresion, 6, "0", STR_PAD_LEFT),
                        'unidad' => $productounidadmedida->codigo,
                        'cantidad' => $item[1],
                        'descripcion' => $producto->compatibilidadnombre,
                        'montobaseigv' => $valorventa,
                        'porcentajeigv' => 18.00,
                        'igv' => $igv,
                        'tipoafectacionigv' => '10',
                        'totalimpuestos' => $igv,
                        'montovalorventa' => $valorventa,
                        'montovalorunitario' => $valorunitario,
                        'montopreciounitario' => $preciounitario,
                        'idproducto' => $producto->id,
                        'idventa' => $item[5],
                        'idfacturacionboletafactura' => $idfacturacionboletafactura
                   ]);
                } 
             
                //Enivar a SUNAT
                $resultado = facturador_facturaboleta($idfacturacionboletafactura);
                /*return response()->json([
                  'resultado' => $resultado['resultado'],
                  'mensaje'   => $resultado['mensaje']
                ]);*/
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
      }else {
        return view('errors.error-facturacion');
      }
    }
  
    public function show(Request $request, $id) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
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
              ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
              ->where('producto.codigoimpresion',$request->input('codigoimpresion'))
              ->select(
                  'producto.*',
                  'productounidadmedida.id as idunidadmedida',
                  'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->first();
            return [ 
              'datosProducto' => $producto,
              'stock' => stock_producto(usersmaster()->idtienda,$producto->id)['total']
            ];
        }elseif($id == 'show-agregarventacodigo'){
        
            /*$venta = DB::table('venta')
              ->whereId($request->input('codigoventa'))
              ->first();
        
            $resultado = '';
        
            if($venta!=''){
                $venta = DB::table('venta')
                    ->where('venta.id',$request->input('codigoventa'))
                    ->where('venta.idestado','3')
                    ->first();
                if
                $resultado = 'NOEXISTE';
            }else{
                $resultado = 'NOEXISTE';
            }*/
        
            $ventadetalles = DB::table('ventadetalle')
              ->join('venta','venta.id','ventadetalle.idventa')
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
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('ventadetalle.id','asc')
              ->get();
            
            $ventadetal = [];
            $idtienda = usersmaster()->idtienda;
            foreach($ventadetalles as $value){
                $ventadetal[] = [
                     "id" => $value->idproducto,
                     "idventa" => $value->idventa,
                     "codigoventa" => str_pad($request->input('codigoventa'), 8, "0", STR_PAD_LEFT),
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
                     "preciototal" => $value->preciototal
                ];
            }
            return [ 
              'ventadetalles' => (object)$ventadetal,
              //'resultado' => $resultado,
            ];
        }elseif($id == 'show-seleccionarproducto'){
            $producto = DB::table('producto')
              ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
              ->where('producto.id',$request->input('idproducto'))
              ->select(
                  'producto.*',
                  'productounidadmedida.id as idunidadmedida',
                  'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->first();
            return [ 
              'datosProducto' => $producto,
              'stock' => stock_producto(usersmaster()->idtienda,$request->input('idproducto'))['total']
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
        }elseif($id == 'show-seleccionarventa'){
            $venta = DB::table('venta')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->leftJoin('agencia','agencia.id','venta.idagencia')
                ->leftJoin('ubigeo as ubigeocliente','ubigeocliente.id','usuariocliente.idubigeo')
                ->where('venta.codigo',$request->input('codigoventa'))
                ->select(
                    'venta.*',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariovendedor.apellidos as apellidosusuariovendedor',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                    'usuariocliente.direccion as direccionusuariocliente',
                    'usuariocliente.idubigeo as idubigeousuariocliente',
                    'ubigeocliente.nombre as ubigeoclientenombre',
                    'formapago.nombre as nombreFormapago',
                    'agencia.nombrecomercial as agencianombrecomercial',
                    'agencia.ruc as agenciaruc',
                    'agencia.razonsocial as agenciarazonsocial',
                    'agencia.direccion as agenciadireccion',
                    'agencia.descripcion as agenciadescripcion',
                    'agencia.telefono as agenciatelefono',
                    'agencia.celular as agenciacelular',
                    'agencia.correo as agenciacorreo',
                    'agencia.logo as agencialogo',
                    'agencia.terminoycondicion as agenciaterminoycondicion',
                    'moneda.nombre as monedanombre'
                )
                ->first();
            $idventa = 0;
            if($venta!=''){
                $idventa = $venta->id;
            }
            $ventadetalles = DB::table('ventadetalle')
              ->join('producto','producto.id','ventadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
              ->where('ventadetalle.idventa',$idventa)
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
        
            $ventadetal = [];
            foreach($ventadetalles as $value){
                $ventadetal[] = [
                     "id" => $value->idproducto,
                     "producodigoimpresion" => $value->producodigoimpresion,
                     "productonombre" => $value->productonombre,
                     "productomotor" => $value->productomotor,
                     "productomarca" => $value->productomarca,
                     "productomodelo" => $value->productomodelo,
                     "preciounitario" => $value->preciounitario,
                     "unidadmedidanombre" => $value->unidadmedidanombre,
                     "stock" => stock_producto($venta->idtienda,$value->idproducto)['total'],
                     "cantidad" => $value->cantidad,
                     "preciototal" => $value->preciototal
                ];
            }
          
            return [ 
              'venta' => $venta,
              'ventadetalles' => (object)$ventadetal 
            ];
        }elseif($id == 'show-ubigeo'){
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
        }elseif($id == 'show-seleccionarcliente'){
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
        }elseif($id == 'show-listarformapago'){
            $formapagos = DB::table('formapago')
                ->orWhere('formapago.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'formapago.id as id',
                  'formapago.nombre as text'
                )
                ->get();
            return $formapagos;
        }
    
  }
  
    public function edit(Request $request, $id)
    {
       //$request->user()->authorizeRoles( $request->path() );
      
       if (usersmaster()->idestadosunat == 2) { 
        $facturacionboletafactura = DB::table('facturacionboletafactura')
            ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
            ->where('facturacionboletafactura.id',$id)
            ->select(
                'facturacionboletafactura.*',
                'venta.codigo as ventacodigo',
                'venta.numeroplaca as ventanumeroplaca'
            )
            ->first();
      
        if($request->input('view') == 'enviarsunat') {
            $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                ->leftJoin('venta','venta.id','facturacionboletafacturadetalle.idventa')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    'facturacionboletafacturadetalle.*',
                    'venta.codigo as ventacodigo'
                )
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();
            return view('layouts/backoffice/facturacionboletafactura/enviarsunat',[
                'facturacionboletafactura' => $facturacionboletafactura,
                'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles,
            ]);
        }elseif($request->input('view') == 'comprobante') {
            return view('layouts/backoffice/facturacionboletafactura/comprobante',[
                'facturacionboletafactura' => $facturacionboletafactura,
            ]);
        }elseif($request->input('view') == 'comprobante-pdf') {
            $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                ->leftJoin('venta','venta.id','facturacionboletafacturadetalle.idventa')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    'facturacionboletafacturadetalle.*',
                    'venta.codigo as ventacodigo'
                )
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();
            $pdf = PDF::loadView('layouts/backoffice/facturacionboletafactura/comprobante-pdf',[
                'facturacionboletafactura' => $facturacionboletafactura,
                'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles,
            ]);
            return $pdf->stream();
        }elseif($request->input('view') == 'correo') {
            return view('layouts/backoffice/facturacionboletafactura/correo',[
                'facturacionboletafactura' => $facturacionboletafactura,
            ]);
        }elseif($request->input('view') == 'detalle') {
            $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                ->leftJoin('venta','venta.id','facturacionboletafacturadetalle.idventa')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    'facturacionboletafacturadetalle.*',
                    'venta.codigo as ventacodigo'
                )
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();
            return view('layouts/backoffice/facturacionboletafactura/detalle',[
                'facturacionboletafactura' => $facturacionboletafactura,
                'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/facturacionboletafactura/delete',[
              'facturacionboletafactura' => $facturacionboletafactura
            ]);
        }
      }else {
         return view('errors.error-facturacion');
      }
    }

    public function update(Request $request, $idfacturacionboletafactura)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       if (usersmaster()->idestadosunat == 2) { 
        if($request->input('view') == 'enviarsunat') {
            $resultado = facturador_facturaboleta($idfacturacionboletafactura);
            return response()->json([
              'resultado' => $resultado['resultado'],
              'mensaje'   => $resultado['mensaje']
            ]);
        }elseif($request->input('view') == 'correo') {
            $rules = [
              'correo' => 'required',
            ];
            $messages = [
              'correo.required' => 'El "Correo Electrónico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
          
            $facturacionboletafactura = DB::table('facturacionboletafactura')
                ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
                ->where('facturacionboletafactura.id',$idfacturacionboletafactura)
                ->select(
                    'facturacionboletafactura.*',
                    'venta.codigo as ventacodigo'
                )
                ->first();
     
          
            $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/facturacionboletafactura/comprobante-pdf',[
                'facturacionboletafactura' => $facturacionboletafactura,
                'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles,
            ]);
          
            $output = $pdf->output();
          
            $comprobante = '';
            if($facturacionboletafactura->venta_tipodocumento=='03'){
                $comprobante = 'BOLETA';
            }elseif($facturacionboletafactura->venta_tipodocumento=='01'){
                $comprobante = 'FACTURA';
            } 
                
          
            $user = array (
               'name' => $comprobante.' - '.$facturacionboletafactura->venta_serie.'-'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT),
               'correo' => $request->input('correo'),
               'pdf' => $output,
               'nombrepdf'=>$comprobante.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
               'xml' => 'public/sunat/produccion/boletafactura/'.$facturacionboletafactura->emisor_ruc.'-'.$facturacionboletafactura->venta_tipodocumento.'-'.$facturacionboletafactura->venta_serie.'-'.$facturacionboletafactura->venta_correlativo.'.xml',
            );
          
            Mail::send('app/templateemail',  ['user' => $user], function ($message) use ($user) {
                $message->from(usersmaster()->tiendacorreo,  'DICOWE S.A.C.');
                $message->to($user['correo'])->subject($user['name']);
                $message->attach($user['xml']);
                $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
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

    public function destroy(Request $request, $idfacturacionboletafactura)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        if($request->input('view') == 'eliminar') {
            DB::table('ventadetalle')->where('idventa',$idfacturacionboletafactura)->delete();
            DB::table('venta')
                ->whereId($idfacturacionboletafactura)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
      }else {
       return view('errors.error-facturacion');
      }
    }

}

