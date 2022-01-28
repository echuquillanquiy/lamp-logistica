<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;
use DateTime;

class FacturacioncomunicacionbajaController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        $where = [];
      
        is_null($request->motivo)        || $where[] = ['facturacioncomunicacionbaja.descripcionmotivobaja', 'LIKE', '%'.$request->motivo.'%'];
        is_null($request->fecharegistro) || $where[] = ['facturacioncomunicacionbaja.comunicacionbaja_fechageneracion', 'LIKE', '%'.$request->fecharegistro.'%'];
        
          if($request->input('estado')!=''){
          $where[] = ['facturacioncomunicacionbaja.idestadofacturacion',$request->input('estado')];
          }
          if(usersmaster()->id!=1){
          $where[] = ['facturacioncomunicacionbaja.idtienda',usersmaster()->idtienda];  
          }
      
        $facturacioncomunicacionbaja = DB::table('facturacioncomunicacionbaja')
          ->join('users as responsable', 'responsable.id', 'facturacioncomunicacionbaja.idusuarioresponsable')
          ->join('facturacioncomunicacionbajadetalle as detalle', 'detalle.idfacturacioncomunicacionbaja', 'facturacioncomunicacionbaja.id')
          ->leftJoin('facturacionboletafactura', 'facturacionboletafactura.id', 'detalle.idfacturacionboletafactura')
          ->leftJoin('facturacionnotacredito', 'facturacionnotacredito.id', 'detalle.idfacturacionnotacredito')
          ->where($where)
          ->select(
                  'facturacioncomunicacionbaja.*',
                  'responsable.nombre as nombreresponsable',
                  'detalle.tipodocumento as tipodocumento',
                  'detalle.serie as serie',
                  'detalle.correlativo as correlativo',
                  'detalle.descripcionmotivobaja as motivo',
                  'facturacionboletafactura.cliente_numerodocumento as factbol_cliente_numerodocumento',
                  'facturacionboletafactura.cliente_razonsocial as factbol_cliente_razonsocial',
                  'facturacionnotacredito.cliente_numerodocumento as notacred_cliente_numerodocumento',
                  'facturacionnotacredito.cliente_razonsocial as notacred_cliente_razonsocial'
        
          )
          ->orderBy('facturacioncomunicacionbaja.id','desc')
          ->paginate(10);
      
        return view('layouts/backoffice/facturacioncomunicacionbaja/index',[
            'facturacioncomunicacionbaja' => $facturacioncomunicacionbaja
        ]);
        
      }else {
        return view('errors.error-facturacion');
      }
    }
  
    public function create(Request $request) 
    {    
        $request->user()->authorizeRoles( $request->path() );
      
       if (usersmaster()->idestadosunat == 2) { 
          $serie    = DB::table('tienda')->where('tienda.id',usersmaster()->idtienda)->where('facturador_serie','<>','')->first();

          if( $request->input('view') == 'registrar' ) {
              return view('layouts/backoffice/facturacioncomunicacionbaja/create',[
                'serie'    => $serie
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
            $rules = [
                'motivo' => 'required',
            ];
            $messages = [
                'motivo.required' => 'El "Motivo" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            
            $idtienda    = usersmaster()->idtienda;
            $tienda      = DB::table('tienda')
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
            $correlativo = DB::table('facturacioncomunicacionbaja')
                            ->orderBy('facturacioncomunicacionbaja.comunicacionbaja_correlativo','desc')
                            ->first();
            $correlativo = ($correlativo == '') ? 0 : $correlativo->comunicacionbaja_correlativo;
            $comunicacionbaja_correlativo = $correlativo + 1;

            if ($request->input('tipo') == 'boletafactura') {
              $facturacion = DB::table('facturacionboletafactura')->whereId($request->input('idfacturacion'))->first();
              $fecha_emision = explode(' ', $facturacion->venta_fechaemision);
              $fecha_emision = new DateTime($fecha_emision[0]);            
              $fecha_actual  = date("d-m-Y");
              $fecha_actual  = new DateTime($fecha_actual);
              $diff_fecha    = $fecha_emision->diff($fecha_actual);    

              if ($diff_fecha->days >= 7) {
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Solo se admiten Facturas con un plazo maximo de 7 dias de emisión!!.'
                  ]);
              }
              
              //$interval = date_diff(date_create($facturacion->venta_fechaemision), date_create(Carbon::now()));
              //$dias = $interval->format('%R%a');
              //$fecha = new \DateTime('-' . $dias . 'days');

              $facturacioncomunicacionbaja = DB::table('facturacioncomunicacionbaja')->insertGetId([
                  'emisor_ruc'                         => $facturacion->emisor_ruc,
                  'emisor_razonsocial'                 => $facturacion->emisor_razonsocial,
                  'emisor_nombrecomercial'             => $facturacion->emisor_nombrecomercial,
                  'emisor_ubigeo'                      => $facturacion->emisor_ubigeo,
                  'emisor_departamento'                => $facturacion->emisor_departamento,
                  'emisor_provincia'                   => $facturacion->emisor_provincia,
                  'emisor_distrito'                    => $facturacion->emisor_distrito,
                  'emisor_direccion'                   => $facturacion->emisor_direccion,
                  'emisor_urbanizacion'                => $facturacion->emisor_urbanizacion,
                  'comunicacionbaja_correlativo'       => $comunicacionbaja_correlativo,
                  'comunicacionbaja_fechageneracion'   => $facturacion->venta_fechaemision,
                  'comunicacionbaja_fechacomunicacion' => Carbon::now(),
                  'estadofacturacion'                  => '',
                  'idestadofacturacion'                => 0,
                  'idagencia'                          => $facturacion->idagencia,
                  'idtienda'                           => $idtienda,
                  'idusuarioresponsable'               => Auth::user()->id,
                  'idestadosunat'                      => 0,
              ]);

              DB::table('facturacioncomunicacionbajadetalle')->insert([
                  'tipodocumento'                 => $facturacion->venta_tipodocumento,
                  'serie'                         => $facturacion->venta_serie,
                  'correlativo'                   => $facturacion->venta_correlativo,
                  'descripcionmotivobaja'         => $request->input('motivo'),
                  'idfacturacioncomunicacionbaja' => $facturacioncomunicacionbaja,
                  'idventa'                       => $facturacion->idventa,
                  'idfacturacionboletafactura'    => $facturacion->id,
                  'idfacturacionnotacredito'      => 0
              ]);

              $resultado = facturador_comunicacionbaja( $facturacioncomunicacionbaja );
              return response()->json([
                'resultado' => $resultado['resultado'],
                'mensaje'   => $resultado['mensaje']
              ]);
            }else if ($request->input('tipo') == 'notacredito') {
                $notacredito = DB::table('facturacionnotacredito')->whereId($request->input('idfacturacion'))->first();
                $fecha_emision = explode(' ', $notacredito->notacredito_fechaemision);
                $fecha_emision = new DateTime($fecha_emision[0]);            
                $fecha_actual  = date("d-m-Y");
                $fecha_actual  = new DateTime($fecha_actual);
                $diff_fecha    = $fecha_emision->diff($fecha_actual);    

                 if ($diff_fecha->days >= 7) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Solo se admiten Facturas con un plazo maximo de 7 dias de emisión!!.'
                    ]);
                }

                //$interval = date_diff(date_create($notacredito->notacredito_fechaemision), date_create(Carbon::now()));
                //$dias = $interval->format('%R%a');
                //$fecha = new \DateTime('-' . $dias . 'days');
                
                $facturacioncomunicacionbaja = DB::table('facturacioncomunicacionbaja')->insertGetId([
                  'emisor_ruc'                         => $notacredito->emisor_ruc,
                  'emisor_razonsocial'                 => $notacredito->emisor_razonsocial,
                  'emisor_nombrecomercial'             => $notacredito->emisor_nombrecomercial,
                  'emisor_ubigeo'                      => $notacredito->emisor_ubigeo,
                  'emisor_departamento'                => $notacredito->emisor_departamento,
                  'emisor_provincia'                   => $notacredito->emisor_provincia,
                  'emisor_distrito'                    => $notacredito->emisor_distrito,
                  'emisor_direccion'                   => $notacredito->emisor_direccion,
                  'emisor_urbanizacion'                => $notacredito->emisor_urbanizacion,
                  'comunicacionbaja_correlativo'       => $comunicacionbaja_correlativo,
                  'comunicacionbaja_fechageneracion'   => $notacredito->notacredito_fechaemision,
                  'comunicacionbaja_fechacomunicacion' => Carbon::now(),
                  'estadofacturacion'                  => '',
                  'idestadofacturacion'                => 0,
                  'idagencia'                          => $notacredito->idagencia,
                  'idtienda'                           => $idtienda,
                  'idusuarioresponsable'               => Auth::user()->id,
                  'idestadosunat'                      => 0,
              ]);

              DB::table('facturacioncomunicacionbajadetalle')->insert([
                  'tipodocumento'                 => $notacredito->notacredito_tipodocumento,
                  'serie'                         => $notacredito->notacredito_serie,
                  'correlativo'                   => $notacredito->notacredito_correlativo,
                  'descripcionmotivobaja'         => $request->input('motivo'),
                  'idfacturacioncomunicacionbaja' => $facturacioncomunicacionbaja,
                  'idventa'                       => 0,
                  'idfacturacionboletafactura'    => 0,
                  'idfacturacionnotacredito'      => $notacredito->id,
              ]);

              $resultado = facturador_comunicacionbaja( $facturacioncomunicacionbaja );
              
              return response()->json([
                'resultado' => $resultado['resultado'],
                'mensaje'   => $resultado['mensaje']
              ]);
            }
          
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
          
          if ($request->input('tipo') == 'boletafactura') {
            $query_facturacion = DB::table('facturacionboletafactura')
            ->join('users as cliente', 'cliente.id', 'facturacionboletafactura.idusuariocliente')
            ->join('agencia', 'agencia.id', 'facturacionboletafactura.idagencia')
            ->where('facturacionboletafactura.idtienda', usersmaster()->idtienda)
            ->where([
              ['facturacionboletafactura.venta_serie', '=', $request->input('serie')],
              ['facturacionboletafactura.venta_correlativo', '=', $request->input('correlativo')]
            ])
             ->select(
                      'facturacionboletafactura.*', 
                      'cliente.identificacion as cliente_identificacion', 
                      'cliente.nombre as cliente_nombre',
                      'cliente.apellidos as cliente_apellidos',
                      'agencia.id as agencia_id',
                      'agencia.nombrecomercial as agencia_nombrecomercial',
                      'agencia.ruc as agencia_ruc')
            ->first();
            
            if ( !is_null($query_facturacion) ) {
              $where_exist[] = ['facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $query_facturacion->id];
              $query_facturaciondetalle = DB::table('facturacionboletafacturadetalle')->where('facturacionboletafacturadetalle.idfacturacionboletafactura', $query_facturacion->id)->get();
              foreach ( $query_facturaciondetalle as $item ) {
                  $ventadetalle[] = [
                    'id'             => $item->id,
                    'codigo'         => $item->codigoproducto,
                    'descripcion'    => $item->descripcion,
                    'unidadmedida'   => $item->unidad,
                    'cantidad'       => $item->cantidad,
                    'preciounitario' => $item->montopreciounitario,
                    'preciototal'    => number_format($item->montopreciounitario * $item->cantidad, 2, '.', ''),
                    'idproducto'     => $item->idproducto,
                  ];
              }

              $venta  = [
                'tipo'                    => 'facturaboleta',
                'id'                      => $query_facturacion->id,
                'cliente'                 => $query_facturacion->cliente_numerodocumento.' - '.$query_facturacion->cliente_razonsocial,
                'venta_tipomoneda'        => $query_facturacion->venta_tipomoneda,
                'venta_fechaemision'      => $query_facturacion->venta_fechaemision,
                'venta_tipodocumento'     => $query_facturacion->venta_tipodocumento,
                'venta_serie'             => $query_facturacion->venta_serie,
                'venta_correlativo'       => $query_facturacion->venta_correlativo,
                'agencia_id'              => $query_facturacion->agencia_id,
                'agencia_nombrecomercial' => $query_facturacion->agencia_nombrecomercial,
                'agencia_ruc'             => $query_facturacion->agencia_ruc,
                'idventa'                 => $query_facturacion->idventa,
              ];
            }
            

            $exist_facturacion = DB::table('facturacioncomunicacionbajadetalle')->where($where_exist)->exists();

            if (!$exist_facturacion) {
              return [
                'venta' => $venta,
                'ventadetalle' => $ventadetalle,
              ];
            }
          }else if  ($request->input('tipo') == 'notacredito') {
            $query_notacredito = DB::table('facturacionnotacredito')
              ->join('agencia', 'agencia.id', 'facturacionnotacredito.idagencia')
              ->where('facturacionnotacredito.idtienda', usersmaster()->idtienda)
              ->where([
                ['facturacionnotacredito.notacredito_serie', '=', $request->input('serie')],
                ['facturacionnotacredito.notacredito_correlativo', '=', $request->input('correlativo')]
              ])
               ->select(
                        'facturacionnotacredito.*', 
                        'agencia.id as agencia_id',
                        'agencia.nombrecomercial as agencia_nombrecomercial',
                        'agencia.ruc as agencia_ruc')
              ->first();
            
             if ( !is_null($query_notacredito) )  {
                 $where_exist[] = ['facturacioncomunicacionbajadetalle.idfacturacionnotacredito', $query_notacredito->id];
                 $query_notacreditodetalle = DB::table('facturacionnotacreditodetalle')->where('facturacionnotacreditodetalle.idfacturacionnotacredito', $query_notacredito->id)->get();

                 foreach ( $query_notacreditodetalle as $item ) {
                      $ventadetalle[] = [
                        'id'             => $item->id,
                        'codigo'         => $item->codigoproducto,
                        'descripcion'    => $item->descripcion,
                        'unidadmedida'   => $item->unidad,
                        'cantidad'       => $item->cantidad,
                        'preciounitario' => $item->montopreciounitario,
                        'preciototal'    => number_format($item->montopreciounitario * $item->cantidad, 2, '.', ''),
                        'idproducto'     => $item->idproducto,
                      ];
                 }

                 $venta  = [
                    'tipo'                    => 'facturaboleta',
                    'id'                      => $query_notacredito->id,
                    'cliente'                 => $query_notacredito->cliente_numerodocumento.' - '.$query_notacredito->cliente_razonsocial,
                    'venta_tipomoneda'        => $query_notacredito->notacredito_tipomoneda,
                    'venta_fechaemision'      => $query_notacredito->notacredito_fechaemision,
                    'venta_tipodocumento'     => $query_notacredito->notacredito_tipodocumento,
                    'venta_serie'             => $query_notacredito->notacredito_serie,
                    'venta_correlativo'       => $query_notacredito->notacredito_correlativo,
                    'agencia_id'              => $query_notacredito->agencia_id,
                    'agencia_nombrecomercial' => $query_notacredito->agencia_nombrecomercial,
                    'agencia_ruc'             => $query_notacredito->agencia_ruc,
                    'idventa'                 => 0,
                 ];
             }
            
              
            $exist_facturacion = DB::table('facturacioncomunicacionbajadetalle')->where($where_exist)->exists();

            if (!$exist_facturacion) {
              return [
                'venta' => $venta,
                'ventadetalle' => $ventadetalle,
              ];
            }
            
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
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        $facturacioncomunicacionbaja = DB::table('facturacioncomunicacionbaja')
            ->join('facturacioncomunicacionbajadetalle', 'facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja', 'facturacioncomunicacionbaja.id')
            ->join('facturacionboletafactura', 'facturacionboletafactura.id', 'facturacioncomunicacionbajadetalle.idfacturacionboletafactura')
            ->where('facturacioncomunicacionbaja.id',$id)
            ->select(
              'facturacioncomunicacionbaja.*',
              'facturacioncomunicacionbajadetalle.descripcionmotivobaja',
              'facturacioncomunicacionbajadetalle.serie',
              'facturacioncomunicacionbajadetalle.correlativo',
              'facturacioncomunicacionbajadetalle.idfacturacionboletafactura',
              'facturacionboletafactura.cliente_numerodocumento',
              'facturacionboletafactura.cliente_razonsocial',
              'facturacionboletafactura.venta_tipomoneda',
              'facturacionboletafactura.venta_fechaemision'
            )
            ->first(); 
      
        $facturacionboletafacturadetalles = DB::table('facturacionboletafacturadetalle')
          ->where('facturacionboletafacturadetalle.idfacturacionboletafactura', $facturacioncomunicacionbaja->idfacturacionboletafactura)
          ->get();
      
        if($request->input('view') == 'reenviarsunat'){
            return view('layouts/backoffice/facturacioncomunicacionbaja/reenviarsunat',[
                'facturacioncomunicacionbaja' => $facturacioncomunicacionbaja,
                'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles,
            ]);
        }elseif($request->input('view') == 'detalle') {   
            return view('layouts/backoffice/facturacioncomunicacionbaja/detalle',[
                'facturacioncomunicacionbaja' => $facturacioncomunicacionbaja,
                'facturacionboletafacturadetalles' => $facturacionboletafacturadetalles,
            ]);
        }
      }else {
        return view('errors.error-facturacion');
      }
    }

    public function update(Request $request, $idfacturacioncomunicacionbaja)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2) { 
        if ($request->input('view') == 'reenviarsunat') {
            $resultado = facturador_comunicacionbaja( $idfacturacioncomunicacionbaja );

           return response()->json([
             'resultado' => $resultado['resultado'],
             'mensaje'   => $resultado['mensaje']
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
