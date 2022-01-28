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

class FacturacionResumenController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2 || usersmaster()->idpermiso == 1) { 
        $where = [];
      
        is_null($request->fecharegistro) || $where[] = ['facturacionresumen.resumen_fechageneracion', 'LIKE', '%'.$request->fecharegistro.'%'];
        is_null($request->responsable) || $where[] = ['responsable.nombre', 'LIKE', '%'.$request->responsable.'%'];
        
          if($request->input('estado')!=''){
          $where[] = ['facturacionresumen.idestadofacturacion',$request->input('estado')];
          }
          if(usersmaster()->id!=1){
          $where[] = ['facturacionresumen.idtienda',usersmaster()->idtienda];  
          }
      
        $facturacionresumen = DB::table('facturacionresumen')
            ->join('users as responsable','responsable.id','facturacionresumen.idusuarioresponsable')
            ->where($where)
            ->select(
                'facturacionresumen.*',
                'responsable.nombre as responsablenombre'
            )
            ->orderBy('facturacionresumen.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/facturacionresumen/index',[
            'facturacionresumen' => $facturacionresumen
        ]);
      }else {
          return view('errors.error-facturacion');
      }
    }
  
    public function create(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
      if (usersmaster()->idestadosunat == 2 || usersmaster()->idpermiso == 1) { 
          $serie    = DB::table('tienda')->where('tienda.id',usersmaster()->idtienda)->where('facturador_serie','<>','')->first();
          if( $request->input('view') == 'registrar' ) {
              $series   = DB::table('tienda')->where('tienda.id',usersmaster()->idtienda)->where('facturador_serie','<>','')->get();
              $agencia  = DB::table('agencia')->where('idestado',1)->first();

              return view('layouts/backoffice/facturacionresumen/create', [
                'agencia'  => $agencia,
                'series'   => $series,
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
      
      if (usersmaster()->idestadosunat == 2 || usersmaster()->idpermiso == 1) { 
        if($request->input('view') == 'registrar') {
            $post_boletas = json_decode($request->input('boletas'));
   
            if (empty($post_boletas)) {
              return response()->json([
                 'resultado' => 'ERROR',
                 'mensaje'   => 'No hay boletas para su envio'
              ]);
            }
          
            $agencia       = DB::table('agencia')->whereId($request->input('idagencia'))->first();
            //$ubigeoagencia = DB::table('ubigeo')->whereId($agencia->idubigeo)->first();
            $idtienda      = usersmaster()->idtienda;

            $tienda  = DB::table('tienda')
                      ->join('ubigeo','ubigeo.id','tienda.idubigeo')
                      ->where('tienda.id',usersmaster()->idtienda)
                      ->select(
                          'tienda.direccion as tiendadireccion',
                          'tienda.facturador_serie as tiendaserie',
                          'ubigeo.codigo as tiendaubigeocodigo',
                          'ubigeo.distrito as tiendaubigeodistrito',
                          'ubigeo.provincia as tiendaubigeoprovincia',
                          'ubigeo.departamento as tiendaubigeodepartamento'
                      )
                      ->first();

            // CAJA
//             $idaperturacierre = 0;  
            // Apertura de caja
//             $aperturacierre = aperturacierre(usersmaster()->idtienda, Auth::user()->id);
          
//             if( $aperturacierre['apertura'] != '' ){
//                 if( $aperturacierre['apertura']->idestado == 3 && $aperturacierre['apertura']->idusersrecepcion == Auth::user()->id ){
//                 }
//                 else{
//                     return response()->json([
//                         'resultado' => 'ERROR',
//                         'mensaje'   => 'La Caja debe estar Aperturada.'
//                     ]);
//                 }
//             }
//             else{
//                 return response()->json([
//                     'resultado' => 'ERROR',
//                     'mensaje'   => 'No hay ninguna Caja Aperturada.'
//                 ]);
//             }
          
//             $idaperturacierre = $aperturacierre['apertura']->id;
            // Fin Apertura de caja
            
            // RESUMEN DIARIO
      
            $correlativo = DB::table('facturacionresumen')
                          //->where('facturacionresumen.idtienda', $idtienda)
                          ->orderBy('facturacionresumen.resumen_correlativo', 'desc')
                          ->first();
      
            $correlativo = ($correlativo == '') ? 0 : $correlativo->resumen_correlativo;
          
            $resumen_correlativo = $correlativo + 1;
            
            $fechaemision = '';
            foreach ($post_boletas as $item) {
               if ($item->tipo == 'boleta') {
                  $factura      = DB::table('facturacionboletafactura')->whereId($item->idventa_boletafactura)->first();
                  $fechaemision = $factura->venta_fechaemision;
               }else if ($item->tipo == 'nota_credito') {
                  $notacredito  = DB::table('facturacionnotacredito')->whereId($item->idventa_boletafactura)->first();
                  $fechaemision = $notacredito->notacredito_fechaemision;
               }
            }
          
            $interval = date_diff(date_create($fechaemision), date_create(Carbon::now()));
            $dias     = $interval->format('%R%a');
            $fecha    = new \DateTime('-' . $dias . 'days');
       
            $idfacturacionresumen = DB::table('facturacionresumen')->insertGetId([
                'emisor_ruc'              => $agencia->ruc,
                'emisor_razonsocial'      => $agencia->razonsocial,
                'emisor_nombrecomercial'  => $agencia->nombrecomercial,
                'emisor_ubigeo'           => $tienda->tiendaubigeocodigo,
                'emisor_departamento'     => $tienda->tiendaubigeodepartamento,
                'emisor_provincia'        => $tienda->tiendaubigeoprovincia,
                'emisor_distrito'         => $tienda->tiendaubigeodistrito,
                'emisor_urbanizacion'     => '',
                'emisor_direccion'        => $tienda->tiendadireccion,
                'resumen_correlativo'     => $resumen_correlativo,
                'resumen_fechageneracion' => $fecha,
                'resumen_fecharesumen'    => $fecha,
                'ticket'                  => '',
                'estadofacturacion'       => '',
                'idestadofacturacion'     => 0,
                'idagencia'               => $request->input('idagencia'),
                'idtienda'                => usersmaster()->idtienda,
                'idusuarioresponsable'    => Auth::user()->id,
                'idestadosunat'           => 1
            ]);
            
            foreach ($post_boletas as $item2) {
               if ($item2->tipo == 'boleta') {
                  $factura      = DB::table('facturacionboletafactura')->whereId($item2->idventa_boletafactura)->first();
                 
                  DB::table('facturacionresumendetalle')->insert([
                     'tipodocumento'              => $factura->venta_tipodocumento,
                     'serienumero'                => $factura->venta_serie.'-'.$factura->venta_correlativo,
                     'estado'                     => ($request->reenviaranular == 'anular') ? 03 : 01, // 1=adicionar, 2=modificar,3=anular
                     'clientetipo'                => $factura->cliente_tipodocumento,
                     'clientenumero'              => $factura->cliente_numerodocumento,
                     'total'                      => $factura->venta_montoimpuestoventa,
                     'operacionesgravadas'        => $factura->venta_montooperaciongravada,
                     'montoigv'                   => $factura->venta_montoigv,
                     'idfacturacionboletafactura' => $factura->id,
                     'idfacturacionnotacredito'   => 0,
                     'idventa'                    => $factura->idventa,
                     'idagencia'                  => $factura->idagencia,
                     'idtienda'                   => $factura->idtienda,                 
                     'idfacturacionresumen'       => $idfacturacionresumen,
                 ]);
               }else if ($item2->tipo == 'nota_credito') {
                  $notacredito  = DB::table('facturacionnotacredito')->whereId($item2->idventa_boletafactura)->first();
                 
                  DB::table('facturacionresumendetalle')->insert([
                     'tipodocumento'              => $notacredito->notacredito_tipodocumento,
                     'serienumero'                => $notacredito->notacredito_serie.'-'.$notacredito->notacredito_correlativo,
                     'estado'                     => 03, // 1=adicionar, 2=modificar,3=anular
                     'clientetipo'                => $notacredito->cliente_tipodocumento,
                     'clientenumero'              => $notacredito->cliente_numerodocumento,
                     'total'                      => $notacredito->notacredito_montoimpuestoventa,
                     'operacionesgravadas'        => $notacredito->notacredito_montooperaciongravada,
                     'montoigv'                   => $notacredito->notacredito_montoigv,
                     'idfacturacionboletafactura' => $notacredito->idfacturacionboletafactura,
                     'idfacturacionnotacredito'   => $notacredito->id,
                     'idventa'                    => 0,
                     'idagencia'                  => $notacredito->idagencia,
                     'idtienda'                   => $notacredito->idtienda,                 
                     'idfacturacionresumen'       => $idfacturacionresumen,
                 ]);
               }
//                DB::table('facturacionresumendetalle')->insert([
//                    'tipodocumento'              => $factura->venta_tipodocumento,
//                    'serienumero'                => $factura->venta_serie.'-'.$factura->venta_correlativo,
//                    'estado'                     => 03, // 1=adicionar, 2=modificar,3=anular
//                    'clientetipo'                => $factura->cliente_tipodocumento,
//                    'clientenumero'              => $factura->cliente_numerodocumento,
//                    'total'                      => $factura->venta_montoimpuestoventa,
//                    'operacionesgravadas'        => $factura->venta_montooperaciongravada,
//                    'montoigv'                   => $factura->venta_montoigv,
//                    'idfacturacionboletafactura' => $factura->id,
//                    'idventa'                    => $factura->idventa,
//                    'idagencia'                  => $factura->idagencia,
//                    'idtienda'                   => $factura->idtienda,                 
//                    'idfacturacionresumen'       => $idfacturacionresumen,
//                ]);
            }
          
           $resultado = facturador_resumendiario($idfacturacionresumen);
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
      
        if($id == 'show-seleccionarboletafactura') {
            $data = [];
            
            if ($request->input('tipodocumento') == 'boleta') {
                $wherefac = [];
                if ($request->reenviaranular == 'reenviar') {  $wherefac[] = ['facturacionboletafactura.idestadofacturacion', 2]; }
              
                $facturacionboletafactura = DB::table('facturacionboletafactura')
                  ->join('users as usuariocliente','usuariocliente.id','facturacionboletafactura.idusuariocliente')
                  ->join('moneda','moneda.codigo','facturacionboletafactura.venta_tipomoneda')
                  ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
                  ->where('facturacionboletafactura.venta_serie',$request->input('facturador_serie'))
                  ->where('facturacionboletafactura.venta_correlativo',$request->input('facturador_correlativo'))
                  ->where($wherefac)
                  ->select(
                      'facturacionboletafactura.*',
                      'venta.codigo as ventacodigo',
                      'moneda.nombre as monedanombre',
                      DB::raw('IF(usuariocliente.idtipopersona=1,
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                      DB::raw('(SELECT SUM(montovalorventa) FROM facturacionboletafacturadetalle WHERE idfacturacionboletafactura=facturacionboletafactura.id LIMIT 1) as total')
                  )
                  ->first();   

                if (!is_null($facturacionboletafactura)) {
                  $data[] =  [
                    'tipo'                        => 'boleta',
                    'id'                          => $facturacionboletafactura->id,
                    'serie_correlativo'           => $facturacionboletafactura->venta_serie.' - '.$facturacionboletafactura->venta_correlativo,
                    'cliente'                     => $facturacionboletafactura->cliente,
                    'moneda'                      => $facturacionboletafactura->monedanombre,
                    'venta_montooperaciongravada' => $facturacionboletafactura->venta_montooperaciongravada,
                    'venta_montoigv'              => $facturacionboletafactura->venta_montoigv,
                    'venta_montoimpuestoventa'    => $facturacionboletafactura->venta_montoimpuestoventa,
                  ];
                }
              
            }else if ($request->input('tipodocumento') == 'notacredito_boletafactura') {
                $wherefac = [];
                if ($request->reenviaranular == 'reenviar') {  $wherefac[] = ['facturacionnotacredito.idestadofacturacion', 2]; }
              
                $notacredito = DB::table('facturacionnotacredito')
                  ->where('facturacionnotacredito.notacredito_serie',$request->input('facturador_serie'))
                  ->where('facturacionnotacredito.notacredito_correlativo',$request->input('facturador_correlativo'))
                  ->first();

                if (!is_null($notacredito)) {
                  $data[] = [
                    'tipo'                        => 'nota_credito',
                    'id'                          => $notacredito->id,
                    'serie_correlativo'           => $notacredito->notacredito_serie.' - '.$notacredito->notacredito_correlativo,
                    'cliente'                     => $notacredito->cliente_numerodocumento.' - '.$notacredito->cliente_razonsocial,
                    'moneda'                      => $notacredito->notacredito_tipomoneda,
                    'venta_montooperaciongravada' => $notacredito->notacredito_montooperaciongravada,
                    'venta_montoigv'              => $notacredito->notacredito_montoigv,
                    'venta_montoimpuestoventa'    => $notacredito->notacredito_montoimpuestoventa,
                  ];
                }
              
            }
    
            return $data;        
        }
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
      if (usersmaster()->idestadosunat == 2 || usersmaster()->idpermiso == 1) { 
        $facturacionresumen = DB::table('facturacionresumen')
            ->where('facturacionresumen.id',$id)
            ->select(
                'facturacionresumen.*'
            )
            ->first();
     
        if($request->input('view') == 'reenviarsunat') {
          
            $facturacionresumendetalle = DB::table('facturacionresumendetalle')
                ->where('facturacionresumendetalle.idfacturacionresumen',$facturacionresumen->id)
                ->orderBy('facturacionresumendetalle.id','asc')
                ->get();
          
            $agencia  = DB::table('agencia')->where('idestado',1)->first();
          
            return view('layouts/backoffice/facturacionresumen/reenviarsunat',[
                'facturacionresumen' => $facturacionresumen,
                'facturacionresumendetalles' => $facturacionresumendetalle,
                'agencia' => $agencia
            ]);
          
        }elseif($request->input('view') == 'detalle') {
            $facturacionresumendetalle = DB::table('facturacionresumendetalle')
                ->where('facturacionresumendetalle.idfacturacionresumen',$facturacionresumen->id)
                ->orderBy('facturacionresumendetalle.id','asc')
                ->get();
          
            $agencia  = DB::table('agencia')->where('idestado',1)->first();
          
            return view('layouts/backoffice/facturacionresumen/detalle',[
                'facturacionresumen' => $facturacionresumen,
                'facturacionresumendetalles' => $facturacionresumendetalle,
                'agencia' => $agencia
            ]);
        }
      }else {
        return view('errors.error-facturacion');
      }
    }
  
    public function update(Request $request, $idfacturacionnotacredito)
    {
        $request->user()->authorizeRoles( $request->path() );
      
       if (usersmaster()->idestadosunat == 2 || usersmaster()->idpermiso == 1) { 
          if($request->input('view') == 'reenviarsunat') {
              $resultado = facturador_resumendiario($idfacturacionnotacredito);
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
                    ->leftJoin('venta','venta.id','facturacionnotacredito.idventa')
                    ->where('facturacionnotacredito.id',$idfacturacionnotacredito)
                    ->select(
                        'facturacionnotacredito.*',
                        'venta.codigo as ventacodigo'
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

                $ventacodigo = str_pad($venta->codigo, 8, "0", STR_PAD_LEFT);

                $user = array (
                   'name' => $comprobante.' - '.$value->venta_serie.'-'.str_pad($value->venta_correlativo, 6, "0", STR_PAD_LEFT),
                   'correo' => $request->input('correo'),
                   'pdf' => $output,
                   'nombrepdf'=>$comprobante.'_'.$value->venta_serie.'_'.str_pad($value->venta_correlativo, 6, "0", STR_PAD_LEFT).'.pdf'
                );

                Mail::send('app/templateemail',  ['user' => $user], function ($message) use ($user) {
                    $message->from('ventas@dicowe.com.pe',  'DICOWE S.A.C.');
                    $message->to($user['correo'])->subject($user['name']);
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
      
      if (usersmaster()->idestadosunat == 2 || usersmaster()->idpermiso == 1) { 
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
