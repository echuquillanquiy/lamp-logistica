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

class FacturacionNotacreditoController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
          $where = [];
          $where[] = ['facturacionnotacredito.notacredito_numerodocumentoafectado','LIKE','%'.$request->input('codigoventa').'%'];
          $where[] = ['facturacionnotacredito.notacredito_fechaemision','LIKE','%'.$request->input('fechaemision').'%'];
          $where[] = ['facturacionnotacredito.notacredito_tipodocumento','LIKE','%'.$request->input('comprobante').'%'];
          $where[] = ['facturacionnotacredito.notacredito_serie','LIKE','%'.$request->input('serie').'%'];
          $where[] = ['facturacionnotacredito.notacredito_correlativo','LIKE','%'.$request->input('correlativo').'%'];
          $where[] = ['facturacionnotacredito.cliente_numerodocumento','LIKE','%'.$request->input('clienteidentificacion').'%'];
          $where[] = ['facturacionnotacredito.cliente_razonsocial','LIKE','%'.$request->input('cliente').'%'];
          $where[] = ['facturacionnotacredito.notacredito_tipomoneda','LIKE','%'.$request->input('moneda').'%'];
        
          if($request->input('estado')!=''){
          $where[] = ['facturacionnotacredito.idestadofacturacion',$request->input('estado')];
          }
          if(usersmaster()->id!=1){
          $where[] = ['facturacionnotacredito.idtienda',usersmaster()->idtienda];  
          }

          $facturacionnotacreditos = DB::table('facturacionnotacredito')
              ->join('users as responsable','responsable.id','facturacionnotacredito.idusuarioresponsable')
              ->where('facturacionnotacredito.idtienda',usersmaster()->idtienda)
              ->where($where)
              ->select(
                  'facturacionnotacredito.*',
                  'responsable.nombre as responsablenombre'
              )
              ->orderBy('facturacionnotacredito.id','desc')
              ->paginate(10);

          return view('layouts/backoffice/facturacionnotacredito/index',[
              'facturacionnotacreditos' => $facturacionnotacreditos
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
              $series = DB::table('tienda')->where('tienda.id',usersmaster()->idtienda)->where('facturador_serie','<>','')->get();
              $motivonotacreditos = DB::table('motivonotacredito')
                  ->orWhere('id',5)
                  ->orWhere('id',6)
                  ->orWhere('id',7)
                  ->get();
              return view('layouts/backoffice/facturacionnotacredito/create', [
                'series' => $series,
                'motivonotacreditos' => $motivonotacreditos,
              ]);
          }elseif($request->input('view') == 'registrar-cliente') {
              $tipopersonas = DB::table('tipopersona')->get();
              return view('layouts/backoffice/facturacionnotacredito/cliente',[
                  'tipopersonas' => $tipopersonas
              ]);
          }elseif($request->input('view') == 'productos') {
              return view('layouts/backoffice/facturacionnotacredito/productos');
          }elseif($request->input('view') == 'facturacionnotacreditodetalle') {

              $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                  ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$request->input('idboletafacturaventa'))
                  ->orderBy('facturacionboletafacturadetalle.id','asc')
                  ->get();

              $ventdel = [];
              foreach($facturacionboletafacturadetalles as $value){

                  $cantidad_notacredito = DB::table('facturacionnotacreditodetalle')
                        ->where('facturacionnotacreditodetalle.idfacturacionboletafacturadetalle',$value->id)
                        ->sum('facturacionnotacreditodetalle.cantidad');

                  $cantidad_actual = $value->cantidad - $cantidad_notacredito;
                  if($cantidad_actual > 0){
                      $ventdel[] = [
                          'id'               => $value->id,
                          'codigoproducto'   => $value->codigoproducto,
                          'productonombre'   => $value->descripcion,
                          'unidad'           => $value->unidad,
                          'cantidad'         => $cantidad_actual,
                          'preciounitario'   => $value->montopreciounitario,
                          'preciototal'      => $value->montopreciounitario * $value->cantidad,
                      ];
                  }  
              }

              return view('layouts/backoffice/facturacionnotacredito/facturacionnotacreditodetalle',[
                  'ventadetalles' => $ventdel,
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
           $post_produtos = json_decode($request->input('productos'));
         
            $rules = [
                'idmotivonotacredito'           => 'required',
                'motivonotacredito_descripcion' => 'required',
                'productos'                     => 'required',
            ];
            
            $messages = [
                'idmotivonotacredito.required'           => 'El "Motivo" es Obligatorio.',
                'motivonotacredito_descripcion.required' => 'El "Motivo" es Obligatorio.',
                'productos.required'                     => 'Los "Productos" son Obligatorio.',
            ];
          
            $this->validate($request,$rules,$messages);

            foreach ($post_produtos as $item_producto) {
              if ($item_producto->productCant <= 0) {
                 return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                  ]);
                  break;
              }elseif ($item_producto->productUnidad < 0) {
                 return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'La Precio Unitario minímo es 0.00.'
                  ]);
                    break;
              }
            }
          
            $idtienda = usersmaster()->idtienda;

            $facturacionboletafactura = DB::table('facturacionboletafactura')->whereId($request->input('idboletafacturaventa'))->first();
            $idfacturaboleta         = $facturacionboletafactura->id;
            $emisor_ruc              = $facturacionboletafactura->emisor_ruc;
            $emisor_razonsocial      = $facturacionboletafactura->emisor_razonsocial;
            $emisor_nombrecomercial  = $facturacionboletafactura->emisor_nombrecomercial;
            $emisor_ubigeo           = $facturacionboletafactura->emisor_ubigeo;
            $emisor_departamento     = $facturacionboletafactura->emisor_departamento;
            $emisor_provincia        = $facturacionboletafactura->emisor_provincia;
            $emisor_distrito         = $facturacionboletafactura->emisor_distrito;
            $emisor_urbanizacion     = $facturacionboletafactura->emisor_urbanizacion;
            $emisor_direccion        = $facturacionboletafactura->emisor_direccion;
            $cliente_tipodocumento   = $facturacionboletafactura->cliente_tipodocumento;
            $cliente_numerodocumento = $facturacionboletafactura->cliente_numerodocumento;
            $cliente_razonsocial     = $facturacionboletafactura->cliente_razonsocial;
            $cliente_ubigeo          = $facturacionboletafactura->cliente_ubigeo; // opcional
            $cliente_departamento    = $facturacionboletafactura->cliente_departamento; // opcional
            $cliente_provincia       = $facturacionboletafactura->cliente_provincia; // opcional
            $cliente_distrito        = $facturacionboletafactura->cliente_distrito; // opcional
            $cliente_urbanizacion    = $facturacionboletafactura->cliente_urbanizacion; // opcional
            $cliente_direccion       = $facturacionboletafactura->cliente_direccion; // opcional
            $venta_serie             = $facturacionboletafactura->venta_serie;
            $venta_correlativo       = $facturacionboletafactura->venta_correlativo;
            $venta_tipodocumento     = $facturacionboletafactura->venta_tipodocumento;
            $venta_tipomoneda        = $facturacionboletafactura->venta_tipomoneda;
            $idagencia               = $facturacionboletafactura->idagencia;
            $idtienda                = $facturacionboletafactura->idtienda;

            if($venta_tipomoneda=='PEN'){
                $monedanombre = 'SOLES';
            }elseif($venta_tipomoneda=='USD'){
                $monedanombre = 'DOLARES';
            }
          
            $motivonotacredito = DB::table('motivonotacredito')->whereId($request->input('idmotivonotacredito'))->first();

            if($venta_tipodocumento=='03') {
                $list = explode('B',$venta_serie);
                $notacredito_serie = 'BB'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }elseif($venta_tipodocumento=='01') {
                $list = explode('F',$venta_serie);
                $notacredito_serie = 'FF'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }elseif($venta_tipodocumento=='00') {
                $list = explode('V',$venta_serie);
                $notacredito_serie = 'VV'.str_pad(intval($list[1]), 2, "0", STR_PAD_LEFT);
            }
          
            $correlativo = DB::table('facturacionnotacredito')
                ->where('notacredito_tipodocumento','07')
                ->where('emisor_ruc',$emisor_ruc)
                ->where('notacredito_serie',$notacredito_serie)
                ->orderBy('notacredito_correlativo','desc')
                ->limit(1)
                ->first();
          
            if($correlativo!=''){
                $notacredito_correlativo = $correlativo->notacredito_correlativo+1;
            }else{
                $notacredito_correlativo = 1;
            }
          
            $total_preciounitario = 0;
            $total_precioventa    = 0;
            $total_valorunitario  = 0;
            $total_valorventa     = 0;
            $total_impuesto       = 0;
          
            foreach ($post_produtos as $item_producto2) {
                $cantidad       = $item_producto2->productCant;
                $preciounitario = number_format($item_producto2->productUnidad, 2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/1.18),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                $total_preciounitario = $total_preciounitario+$preciounitario;
                $total_precioventa    = $total_precioventa+$precioventa;
                $total_valorunitario  = $total_valorunitario+$valorunitario;
                $total_valorventa     = $total_valorventa+$valorventa;
                $total_impuesto       = $total_impuesto+$impuesto;
            }
              
            $idfacturacionnotacredito = DB::table('facturacionnotacredito')->insertGetId([
                'emisor_ruc'                          => $emisor_ruc,
                'emisor_razonsocial'                  => $emisor_razonsocial,
                'emisor_nombrecomercial'              => $emisor_nombrecomercial,
                'emisor_ubigeo'                       => $emisor_ubigeo,
                'emisor_departamento'                 => $emisor_departamento,
                'emisor_provincia'                    => $emisor_provincia,
                'emisor_distrito'                     => $emisor_distrito,
                'emisor_urbanizacion'                 => $emisor_urbanizacion,
                'emisor_direccion'                    => $emisor_direccion,
                'cliente_tipodocumento'               => $cliente_tipodocumento,
                'cliente_numerodocumento'             => $cliente_numerodocumento,
                'cliente_razonsocial'                 => $cliente_razonsocial,
                'cliente_ubigeo'                      => $cliente_ubigeo, // opcional
                'cliente_departamento'                => $cliente_departamento, // opcional
                'cliente_provincia'                   => $cliente_provincia, // opcional
                'cliente_distrito'                    => $cliente_distrito, // opcional
                'cliente_urbanizacion'                => $cliente_urbanizacion, // opcional
                'cliente_direccion'                   => $cliente_direccion, // opcional
                'notacredito_ublversion'              => '2.1',
                'notacredito_numerodocumentoafectado' => $venta_serie.'-'.$venta_correlativo,
                'notacredito_tipodocafectado'         => $venta_tipodocumento,
                'notacredito_codigomotivo'            => $motivonotacredito->codigo,
                'notacredito_descripcionmotivo'       => $request->input('motivonotacredito_descripcion'),
                'notacredito_tipodocumento'           => '07',
                'notacredito_serie'                   => $notacredito_serie,
                'notacredito_correlativo'             => $notacredito_correlativo,
                'notacredito_fechaemision'            => Carbon::now(),
                'notacredito_tipomoneda'              => $venta_tipomoneda,
                'notacredito_montooperaciongravada'   => number_format($total_precioventa-$total_impuesto,2, '.', ''),
                'notacredito_montoigv'                => number_format($total_impuesto,2, '.', ''),
                'notacredito_totalimpuestos'          => number_format($total_impuesto,2, '.', ''),
                'notacredito_valorventa'              => number_format($total_valorventa,2, '.', ''),
                'notacredito_montoimpuestoventa'      => number_format($total_precioventa,2, '.', ''),
                'notacredito_qr'                      => '',
                'leyenda_codigo'                      => '1000',
                'leyenda_value'                       => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$monedanombre,
                'idestadofacturacion'                 => 0,
                'idfacturacionboletafactura'          => $idfacturaboleta,
                'idagencia'                           => $idagencia,
                'idtienda'                            => $idtienda,
                'idusuarioresponsable'                => Auth::user()->id,
                'idestadosunat'                       => 1
            ]);
          
            foreach ($post_produtos as $item_producto3) {
                $cantidad       = $item_producto3->productCant;
                $preciounitario = number_format($item_producto3->productUnidad,2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/1.18),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto       = number_format($precioventa-$valorventa,2, '.', '');

                $facturacionboletafacturadetalle = DB::table('facturacionboletafacturadetalle')->whereId($item_producto3->idfacturacionboletafacturadetalle)->first();
                $codigoproducto                  = $facturacionboletafacturadetalle->codigoproducto;
                $unidad                          = $facturacionboletafacturadetalle->unidad;
                $descripcion                     = $facturacionboletafacturadetalle->descripcion;
                $idproducto                      = $facturacionboletafacturadetalle->idproducto;

                DB::table('facturacionnotacreditodetalle')->insert([
                   'codigoproducto'                    => $codigoproducto,
                   'unidad'                            => $unidad,
                   'cantidad'                          => $cantidad,
                   'descripcion'                       => $descripcion,
                   'montobaseigv'                      => $valorventa,
                   'porcentajeigv'                     => 18.00,
                   'igv'                               => $impuesto,
                   'tipoafectacionigv'                 => '10',
                   'totalimpuestos'                    => $impuesto,
                   'montovalorventa'                   => $valorventa,
                   'montovalorunitario'                => $valorunitario,
                   'montopreciounitario'               => $preciounitario,
                   'idproducto'                        => $idproducto,
                   'idfacturacionboletafacturadetalle' => $item_producto3->idfacturacionboletafacturadetalle,
                   'idfacturacionnotacredito'          => $idfacturacionnotacredito
               ]);
            }
                    
       
            $resultado = facturador_notacredito($idfacturacionnotacredito);
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
      
        if($id == 'show-seleccionarboletafactura'){
            $facturacionboletafactura = DB::table('facturacionboletafactura')
                ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
                ->where('facturacionboletafactura.venta_serie',$request->input('facturador_serie'))
                ->where('facturacionboletafactura.venta_correlativo',$request->input('facturador_correlativo'))
                ->select(
                    'facturacionboletafactura.*',
                    'venta.codigo as ventacodigo'
                )
                ->first();
          
            return [ 
              'facturacionboletafactura' => $facturacionboletafactura
            ];
        }elseif($id == 'show-seleccionarboletafacturadetalle'){
            $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
                ->where('facturacionboletafacturadetalle.idfacturacionboletafactura',$request->input('idfacturacionboletafactura'))
                ->orderBy('facturacionboletafacturadetalle.id','asc')
                ->get();
            return [ 
              'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles
            ];
        }
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
          $facturacionnotacredito = DB::table('facturacionnotacredito')
              ->leftJoin('motivonotacredito','motivonotacredito.codigo','facturacionnotacredito.notacredito_codigomotivo')
              ->leftJoin('facturacionboletafactura','facturacionboletafactura.id','facturacionnotacredito.idfacturacionboletafactura')
              ->where('facturacionnotacredito.id',$id)
              ->select(
                  'facturacionnotacredito.*',
                  'motivonotacredito.nombre as motivonotacreditonombre',
                  'facturacionboletafactura.venta_fechaemision as venta_fechaemision'
              )
              ->first();

          if($request->input('view') == 'enviarsunat') {
              $facturacionnotacreditodetalles = DB::table('facturacionnotacreditodetalle')
                  ->where('facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                  ->orderBy('facturacionnotacreditodetalle.id','asc')
                  ->get();
              return view('layouts/backoffice/facturacionnotacredito/enviarsunat',[
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'facturacionnotacreditodetalles' => $facturacionnotacreditodetalles,
              ]);
          }elseif($request->input('view') == 'comprobante') {
              return view('layouts/backoffice/facturacionnotacredito/comprobante',[
                  'facturacionnotacredito' => $facturacionnotacredito,
              ]);
          }elseif($request->input('view') == 'comprobante-pdf') {
              $facturacionnotacreditodetalles = DB::table('facturacionnotacreditodetalle')
                  ->where('facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                  ->orderBy('facturacionnotacreditodetalle.id','asc')
                  ->get();
              $pdf = PDF::loadView('layouts/backoffice/facturacionnotacredito/comprobante-pdf',[
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'facturacionnotacreditodetalles' => $facturacionnotacreditodetalles,
              ]);
              return $pdf->stream();
          }elseif($request->input('view') == 'correo') {
              return view('layouts/backoffice/facturacionnotacredito/correo',[
                  'facturacionnotacredito' => $facturacionnotacredito,
              ]);
          }elseif($request->input('view') == 'detalle') {
              $facturacionnotacreditodetalles = DB::table('facturacionnotacreditodetalle')
                  ->where('facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                  ->orderBy('facturacionnotacreditodetalle.id','asc')
                  ->get();
              return view('layouts/backoffice/facturacionnotacredito/detalle',[
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'facturacionnotacreditodetalles' => $facturacionnotacreditodetalles,
              ]);
          }elseif($request->input('view') == 'eliminar') {
              return view('layouts/backoffice/facturacionnotacredito/delete',[
                'facturacionnotacredito' => $facturacionnotacredito
              ]);
          }
       }else {
          return view('errors.error-facturacion');
       }
    }

    public function update(Request $request, $idfacturacionnotacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        if($request->input('view') == 'enviarsunat') {
            /*$countcobranzacredito = 0;
            $countcobranzaletra = 0;
            if($venta!=''){
                $countcobranzacredito = DB::table('cobranzacredito')
                    ->where('cobranzacredito.idventa',$venta->id)
                    ->where('cobranzacredito.idestado',2)
                    ->count();
                $countcobranzaletra = DB::table('cobranzaletra')
                    ->join('ventaletra','ventaletra.id','cobranzaletra.idventaletra')
                    ->where('ventaletra.idventa',$venta->id)
                    ->where('cobranzaletra.idestado',2)
                    ->count();  
            }
            if($countcobranzacredito>0){
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Esta Venta a Crédito, tiene cobranzas realizadas!'
                ]);
            }elseif($countcobranzaletra>0){
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Esta Venta a Letra, tiene cobranzas realizadas!'
                ]);
            }*/
            $resultado = facturador_notacredito($idfacturacionnotacredito);
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
          
            $facturacionnotacredito = DB::table('facturacionnotacredito')
                ->where('facturacionnotacredito.id',$idfacturacionnotacredito)
                ->select(
                    'facturacionnotacredito.*'
                )
                ->first();
          
            $facturacionnotacreditodetalles = DB::table('facturacionnotacreditodetalle')
                ->orderBy('facturacionnotacreditodetalle.id','asc')
                ->get();
            $pdf = PDF::loadView('layouts/backoffice/facturacionnotacredito/comprobante-pdf',[
                'facturacionnotacredito' => $facturacionnotacredito,
                'facturacionnotacreditodetalles' => $facturacionnotacreditodetalles,
            ]);
          
            $output = $pdf->output();
          
            $comprobante = 'NOTA DE CREDITO';
         
            $user = array (
               'name' => $comprobante.' - '.$facturacionnotacredito->notacredito_serie.'-'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT),
               'correo' => $request->input('correo'),
               'pdf' => $output,
               'nombrepdf'=>$comprobante.'_'.$facturacionnotacredito->notacredito_serie.'_'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
               'xml' => 'public/sunat/produccion/notacredito/'.$facturacionboletafactura->emisor_ruc.'-'.$facturacionboletafactura->notacredito_tipodocumento.'-'.$facturacionboletafactura->notacredito_serie.'-'.$facturacionboletafactura->notacredito_correlativo.'.xml',
            );
          
            Mail::send('app/templateemail',  ['user' => $user], function ($message) use ($user) {
                $message->from('ventas@dicowe.com.pe',  'DICOWE S.A.C.');
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

    public function destroy(Request $request, $idfacturacionnotacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        if($request->input('view') == 'eliminar') {
            DB::table('ventadetalle')->where('idventa',$idfacturacionnotacredito)->delete();
            DB::table('venta')
                ->whereId($idfacturacionnotacredito)
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
