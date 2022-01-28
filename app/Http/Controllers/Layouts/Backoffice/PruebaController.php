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

class PruebaController extends Controller
{
    public function index(Request $request) 
    {
        $idtienda = usersmaster()->idtienda;
      
        $where = [];
        $where1 = [];
        $where2 = [];
        //if(Auth::user()->id!=1){
            /*$where[] = ['venta.idusuariocajero',Auth::user()->id];
            $where1[] = ['venta.idusuariocajero',Auth::user()->id];
            $where2[] = ['venta.idusuariocajero',Auth::user()->id];*/
            $where[] = ['venta.idtienda',$idtienda];
            $where1[] = ['venta.idtienda',$idtienda];
            $where2[] = ['venta.idtienda',$idtienda];
        //}
        $where[] = ['venta.idestado','<>',1];
        $where[] = ['venta.idestado','<>',2];
        $where[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where[] = ['usuariocliente.identificacion','LIKE','%'.$request->input('cliente').'%'];
      
        $where1[] = ['venta.idestado','<>',1];
        $where1[] = ['venta.idestado','<>',2];
        $where1[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where1[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where1[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where1[] = ['usuariocliente.nombre','LIKE','%'.$request->input('cliente').'%'];
      
        $where2[] = ['venta.idestado','<>',1];
        $where2[] = ['venta.idestado','<>',2];
        $where2[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where2[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where2[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where2[] = ['usuariocliente.apellidos','LIKE','%'.$request->input('cliente').'%'];
        
        $ventas = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
            ->join('aperturacierre','aperturacierre.id','venta.idaperturacierre')
            ->join('caja','caja.id','aperturacierre.idcaja')
            ->join('tienda','tienda.id','caja.idtienda')
            ->where($where)
            ->orWhere($where1)
            ->orWhere($where2)
            ->select(
                'venta.*',
                'caja.nombre as cajanombre',
                'tienda.nombre as tiendanombre',
                'usuariovendedor.nombre as nombreusuariovendedor',
                DB::raw('IF(usuariocliente.idtipopersona=1,
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                'formapago.nombre as nombreFormapago',
                'moneda.simbolo as monedasimbolo',
                'tipocomprobante.nombre as tipocomprobantenombre'
            )
            ->orderBy('venta.fechaconfirmacion','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/prueba/index',[
            'ventas' => $ventas
        ]);
    }
  
    public function create(Request $request) 
    {
        if($request->input('view') == 'registrar') {
            $agencias = DB::table('agencia')->get();
            $agencia = DB::table('agencia')->where('idestado',1)->first();
            $tipocomprobantes = DB::table('tipocomprobante')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/prueba/create', [
              'agencias' => $agencias,
              'agencia' => $agencia,
              'tipocomprobantes' => $tipocomprobantes,
              'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'registrar-cliente') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/prueba/cliente',[
                'tipopersonas' => $tipopersonas
            ]);
        }elseif($request->input('view') == 'productos') {
            return view('layouts/backoffice/prueba/productos');
        } 
    }
  
    public function store(Request $request)
    {
        if($request->input('view') == 'registrar') {
            $rules = [
                'idcliente' => 'required',
                'clientedireccion' => 'required',
                'clienteidubigeo' => 'required',
                'idagencia' => 'required',
                'idtipocomprobante' => 'required',
                'idformapago' => 'required',
            ];
          
            if($request->input('idformapago')==1){
                if($request->input('ttipopagos')==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje' => 'Agregar un "Tipo de Pago".'
                    ]);
                }
                 $montototal1 = 0;
                 $servicio = explode('&',$request->input('ttipopagos'));
              
                 for($y=1; $y < count($servicio); $y++) {
                    $servic = explode('/',$servicio[$y]);
                    if($servic[11]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje' => 'El "Tipo de Pago" es obligatorio.'
                        ]);
                    }elseif($servic[11]==1){
                        if($servic[0]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Ingrese un Monto a Pagar.'
                            ]);
                        }

                        $montototal1 = $montototal1+$servic[0];
                    }elseif($servic[11]==2){
                        if($servic[1]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Seleccione un Banco.'
                            ]);
                        }elseif($servic[2]==''){
                             return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Registre un numero de cuenta valido.'
                            ]);
                        }elseif($servic[3]==''){
                             return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Seleccione una Fecha de Deposito.'
                            ]);
                        }elseif($servic[4]==''){
                             return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'El campo Numero de Operación es obligatorio.'
                            ]);
                        }elseif($servic[5]==''){
                             return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Ingrese un Monto a Pagar.'
                            ]);
                        }
                        $montototal1 = $montototal1+$servic[5];
                    }elseif($servic[11]==3){
                        if($servic[6]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Seleccione un Banco.'
                            ]);
                        }elseif($servic[7]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Seleccione una Fecha de Emisión.'
                            ]);
                        }elseif($servic[8]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Seleccione una Fecha de Vencimiento.'
                            ]);
                        }elseif($servic[9]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'El campo Numero de Cheque es obligatorio.'
                            ]);
                        }elseif($servic[10]==''){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje' => 'Ingrese un Monto a Pagar.'
                            ]);
                        }

                        $montototal1 = $montototal1+$servic[10];
                    }              
                }

                $totalinput1 = (string)$request->input('totalventa');
                $montototal1 = (string)$montototal1;
                if($montototal1 > $totalinput1){
                      return response()->json([
                          'resultado' => 'ERROR',
                          'mensaje' => 'El "Monto a pagar" es mayor al Total de la Venta.'
                      ]);
                }elseif($montototal1 < $totalinput1){
                      return response()->json([
                          'resultado' => 'ERROR',
                          'mensaje' => 'El "Monto a pagar" es menor al Total de la Venta.'
                      ]);
                }
            }elseif($request->input('idformapago')==2){
                $rules = [
                    'idcliente' => 'required',
                    'clientedireccion' => 'required',
                    'clienteidubigeo' => 'required',
                    'idagencia' => 'required',
                    'idtipocomprobante' => 'required',
                    'idformapago' => 'required',
                    'creditoiniciopago' => 'required',
                    'creditofrecuencia' => 'required',
                    'creditodias' => 'required',
                    'creditoultimopago' => 'required',
                ];
            }elseif($request->input('idformapago')==3){
                $rules = [
                    'idcliente' => 'required',
                    'clientedireccion' => 'required',
                    'clienteidubigeo' => 'required',
                    'idagencia' => 'required',
                    'idtipocomprobante' => 'required',
                    'idformapago' => 'required',
                    'idgarante' => 'required',
                    'letrafechainicio' => 'required',
                    'letrafrecuencia' => 'required',
                    'letracuota' => 'required',
                ];
              
                if($request->input('letratablacuotas')==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje' => 'La "Cuota" mínima es 1.'
                    ]);
                }
              
                $cuotas = explode(',',$request->input('letratablacuotas'));
                $montototal1 = 0;
                $valid_numletra = [];
                for($i=1; $i < count($cuotas); $i++) {
                    $cuot = explode('/val/',$cuotas[$i]);
                    if($cuot[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje' => 'Hay campos "N° de Letra" estan vacios!!.'
                        ]);
                    }
                    if($cuot[2]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje' => 'Hay campos "Ultima Fecha" estan vacios!!.'
                        ]);
                    }
                    if($cuot[3]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje' => 'Hay campos "Importe" estan vacios!!.'
                        ]);
                    }
                    $montototal1 = $montototal1+$cuot[3];
                  
                    if (in_array($cuot[1], $valid_numletra)) {
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje' => 'Hay duplicados en el campo "N° de Letra"!!.'
                        ]);
                    }
                  
                    $valid_numletra[] = $cuot[1];
                } 
              
                $totalinput1 = (string)$request->input('totalventa');
                $montototal1 = (string)$montototal1;
                if($montototal1 > $totalinput1){
                      return response()->json([
                          'resultado' => 'ERROR',
                          'mensaje' => 'El "Monto a pagar" es mayor al Total de la Venta.'
                      ]);
                }elseif($montototal1 < $totalinput1){
                      return response()->json([
                          'resultado' => 'ERROR',
                          'mensaje' => 'El "Monto a pagar" es menor al Total de la Venta.'
                      ]);
                }
            }
          
            
            $messages = [
                'idventa.required' => 'La "Venta" es Obligatorio.',
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'clientedireccion.required' => 'La "Dirección" es Obligatorio.',
                'clienteidubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'idagencia.required' => 'La "Agencia" es Obligatorio.',
                'idtipocomprobante.required' => 'El "Tipo de comprobante" es Obligatorio.',
                'idformapago.required' => 'La "Forma de pago" es Obligatorio.',
                'creditoiniciopago.required' => 'La "Fecha de inicio" es Obligatorio.',
                'creditofrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
                'creditodias.required' => 'Los "Días" son Obligatorio.',
                'creditoultimopago.required' => 'La "Ultima fecha" es Obligatorio.',
                'idgarante.required' => 'El "Aval ó Garante" es Obligatorio.',
                'letrafechainicio.required' => 'La "Fecha de inicio" es Obligatorio.',
                'letrafrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
                'letracuota.required' => 'Las "Cuotas" son Obligatorio.',
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
          
            $ventadetalles = DB::table('ventadetalle')
                    ->join('producto','producto.id','ventadetalle.idproducto')
                    ->where('ventadetalle.idventa',$request->input('idventa'))
                    ->select(
                        'ventadetalle.*',
                        'producto.codigoimpresion as productocodigoimpresion',
                        'producto.compatibilidadnombre as productonombre'
                    )
                    ->get();
          
            foreach($ventadetalles as $value){
                $stocktotal = stock_producto(usersmaster()->idtienda,$value->idproducto)['total'];
                if($stocktotal<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay Suficiente Stock del producto "'.$value->productocodigoimpresion.' - '.$value->productonombre.'"!!.'
                    ]);
                    break;
                }
            }
  
            $idtienda = usersmaster()->idtienda;

            // CAJA
            // Apertura de caja
            $idaperturacierre = 0;
            $aperturacierre = aperturacierre($idtienda,Auth::user()->id);
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

                $totalventa = DB::table('ventadetalle')
                    ->where('idventa',$request->input('idventa'))
                    ->sum(DB::raw('CONCAT((ventadetalle.cantidad*ventadetalle.preciounitario)-ventadetalle.descuento)'));
              
                if($totalventa<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'la venta no puede ser menor a 0.00!.'
                    ]);
                }
        
            
            // CLIENTE
            DB::table('users')->whereId($request->input('idcliente'))->update([
                'direccion' => $request->input('clientedireccion'),
                'idubigeo' => $request->input('clienteidubigeo')
            ]);
          
           /* $venta = DB::table('venta')
                //->join('aperturacierre','aperturacierre.id','venta.idaperturacierre')
                //->join('caja','caja.id','aperturacierre.idcaja')
                //->where('caja.idtienda',usersmaster()->idtienda)
                ->select('venta.*')
                ->orderBy('venta.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($venta!=''){
                $codigo = $venta->codigo+1;
            }*/

            // VENTA
            DB::table('venta')->whereId($request->input('idventa'))->update([
               //'codigo' => $codigo,
               'fechaconfirmacion' => Carbon::now(),
               'montorecibido' => $request->input('totalventa'),
               'vuelto' => '0.00',
               'fp_credito_fechainicio' => $request->input('creditoiniciopago')!=''?$request->input('creditoiniciopago'):'',
               'fp_credito_frecuencia' => $request->input('creditofrecuencia')!=''?$request->input('creditofrecuencia'):'',
               'fp_credito_dias' => $request->input('creditodias')!=''?$request->input('creditodias'):'',
               'fp_credito_ultimafecha' => $request->input('creditoultimopago')!=''?$request->input('creditoultimopago'):'',
               'fp_letra_garante' => $request->input('idgarante')!=''?$request->input('idgarante'):0,
               'fp_letra_fechainicio' => $request->input('letrafechainicio')!=''?$request->input('letrafechainicio'):'',
               'fp_letra_frecuencia' => $request->input('letrafrecuencia')!=''?$request->input('letrafrecuencia'):'',
               'fp_letra_cuotas' => $request->input('letracuota')!=''?$request->input('letracuota'):'',
               'idaperturacierre' => $idaperturacierre,
               'idusuariocliente' => $request->input('idcliente'),
               'idusuariocajero' => Auth::user()->id,
               'idagencia' =>  $request->input('idagencia'),
               'idtipocomprobante' =>  $request->input('idtipocomprobante'),
               'idformapago' =>  $request->input('idformapago'),
               'idestado' => 3,
            ]);
            
            //forma de pago
            if($request->input('idformapago')==1){
                  $servicio1 = explode('&',$request->input('ttipopagos'));
                  for($i=1; $i < count($servicio1); $i++) {
                      $servic1 = explode('/',$servicio1[$i]);
                      //----------> Contado Efectivo
                      if($servic1[11]==1){
                          DB::table('ventatipopago')->insertGetId([
                              'monto' => $servic1[0],
                              'deposito_numerocuenta' => '',
                              'deposito_fecha' => '',
                              'deposito_hora' => '',
                              'deposito_numerooperacion' => '',
                              'cheque_emision' => '',
                              'cheque_vencimiento' => '',
                              'cheque_numero' => '',
                              'banco' => '',
                              'idtipopago' => $servic1[11],
                              'idventa' => $request->input('idventa'),
                              'idestado' => 1
                          ]);
                      //-----------> Contado Deposito
                      }elseif($servic1[11]==2){
                          DB::table('ventatipopago')->insertGetId([
                              'monto' => $servic1[5],
                              'deposito_numerocuenta' => $servic1[2],
                              'deposito_fecha' => $servic1[3],
                              'deposito_hora' => $servic1[12],
                              'deposito_numerooperacion' => $servic1[4],
                              'cheque_emision' => '',
                              'cheque_vencimiento' => '',
                              'cheque_numero' => '',
                              'banco' => $servic1[1],
                              'idtipopago' => $servic1[11],
                              'idventa' => $request->input('idventa'),
                              'idestado' => 1
                          ]);
                      //-----------> Contado Cheque
                      }elseif($servic1[11]==3){
                            DB::table('ventatipopago')->insertGetId([
                              'monto' => $servic1[10],
                              'deposito_numerocuenta' => '',
                              'deposito_fecha' => '',
                              'deposito_hora' => '',
                              'deposito_numerooperacion' => '',
                              'cheque_emision' => $servic1[7],
                              'cheque_vencimiento' => $servic1[8],
                              'cheque_numero' => $servic1[9],
                              'banco' => $servic1[6],
                              'idtipopago' => $servic1[11],
                              'idventa' => $request->input('idventa'),
                              'idestado' => 1
                          ]);
                      }
                  }
            }elseif($request->input('idformapago')==2){
            }elseif($request->input('idformapago')==3){
                $cuotas = explode(',',$request->input('letratablacuotas'));
                for($i=1; $i < count($cuotas); $i++) {
                    $cuot = explode('/val/',$cuotas[$i]);
                    DB::table('ventaletra')->insert([
                        'numero' => $cuot[0],
                        'numeroletra' => $cuot[1],
                        'numerounico' => '',
                        'fechainicio' => $request->input('letrafechainicio'),
                        'fechafin' => $cuot[2],
                        'monto' => $cuot[3],
                        'idventa' => $request->input('idventa'),
                        'idestado' => 1
                    ]);
                }
            }
          
          
            // FACTURACIÓN
            if($request->input('idtipocomprobante')==1 or $request->input('idtipocomprobante')==2){
                
                $agencia = DB::table('agencia')
                    ->where('agencia.id',$request->input('idagencia'))
                    ->first();
              
                $cliente = DB::table('users')
                    ->join('ubigeo','ubigeo.id','users.idubigeo')
                    ->where('users.id',$request->input('idcliente'))
                    ->select(
                        'users.*',
                        'ubigeo.codigo as ubigeocodigo',
                        'ubigeo.distrito as ubigeodistrito',
                        'ubigeo.provincia as ubigeoprovincia',
                        'ubigeo.departamento as ubigeodepartamento'
                    )
                    ->first();
              
                $venta = DB::table('venta')
                    ->join('moneda','moneda.id','venta.idmoneda')
                    ->where('venta.id',$request->input('idventa'))
                    ->select(
                        'venta.*',
                        'moneda.codigo as monedacodigo',
                        'moneda.nombre as monedanombre'
                    )
                    ->first();
              
                if($cliente->idtipopersona==1) {
                    $cliente_tipodocumento = '1';
                    $cliente_razonsocial = $cliente->apellidos.', '.$cliente->nombre;
                }elseif($cliente->idtipopersona==2) {
                    $cliente_tipodocumento = '6';
                    $cliente_razonsocial = $cliente->nombre;
                }
              
                $usersmaster = usersmaster();
                if($venta->idtipocomprobante==1) {
                    $venta_tipodocumento = '03';
                    $venta_serie = 'B'.str_pad($usersmaster->tiendaserie, 3, "0", STR_PAD_LEFT);
                }elseif($venta->idtipocomprobante==2) {
                    $venta_tipodocumento = '01';
                    $venta_serie = 'F'.str_pad($usersmaster->tiendaserie, 3, "0", STR_PAD_LEFT);
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
          
                $ventadetalles = DB::table('ventadetalle')
                    ->join('producto','producto.id','ventadetalle.idproducto')
                    ->join('productounidadmedida','productounidadmedida.id','ventadetalle.idunidadmedida')
                    ->where('ventadetalle.idventa',$venta->id)
                    ->select(
                        'ventadetalle.*',
                        'producto.codigoimpresion as productocodigoimpresion',
                        'producto.compatibilidadnombre as productonombre',
                        'productounidadmedida.codigo as unidadmedidacodigo'
                    )
                    ->get();
          
                $total_preciounitario = 0;
                $total_precioventa = 0;
                $total_valorunitario = 0;
                $total_valorventa = 0;
                $total_impuesto = 0;
          
                foreach($ventadetalles as $value){
                    $preciounitario = number_format($value->preciounitario,2, '.', '');
                    $precioventa = number_format($value->preciounitario*$value->cantidad,2, '.', '');
                    $valorunitario = number_format(($preciounitario/1.18),2, '.', '');
                    $valorventa = number_format($valorunitario*$value->cantidad,2, '.', '');
                    $igv = number_format($precioventa-$valorventa,2, '.', '');

                    $total_preciounitario = $total_preciounitario+$preciounitario;
                    $total_precioventa = $total_precioventa+$precioventa;
                    $total_valorunitario = $total_valorunitario+$valorunitario;
                    $total_valorventa = $total_valorventa+$valorventa;
                    $total_impuesto = $total_impuesto+$igv;
                }
              
                $idfacturacion = DB::table('facturacionboletafactura')->insertGetId([
                    'emisor_ruc' => $agencia->ruc,
                    'emisor_razonsocial' => $agencia->razonsocial,
                    'emisor_nombrecomercial' => $agencia->nombrecomercial,
                    'emisor_ubigeo' => $usersmaster->ubigeocodigo,
                    'emisor_departamento' => $usersmaster->ubigeodepartamento,
                    'emisor_provincia' => $usersmaster->ubigeoprovincia,
                    'emisor_distrito' => $usersmaster->ubigeodistrito,
                    'emisor_urbanizacion' => '',
                    'emisor_direccion' => $usersmaster->tiendadireccion,
                    'cliente_tipodocumento' => $cliente_tipodocumento,
                    'cliente_numerodocumento' => $cliente->identificacion,
                    'cliente_razonsocial' => $cliente_razonsocial,
                    'cliente_ubigeo' => $cliente->ubigeocodigo,
                    'cliente_departamento' => $cliente->ubigeodepartamento,
                    'cliente_provincia' => $cliente->ubigeoprovincia,
                    'cliente_distrito' => $cliente->ubigeodistrito,
                    'cliente_urbanizacion' => '',
                    'cliente_direccion' => $cliente->direccion,
                    'venta_ublversion' => '',
                    'venta_tipooperacion' => '0101',
                    'venta_tipodocumento' => $venta_tipodocumento,
                    'venta_serie' => $venta_serie,
                    'venta_correlativo' => $venta_correlativo,
                    'venta_fechaemision' => $venta->fechaconfirmacion,
                    'venta_tipomoneda' => $venta->monedacodigo,
                    'venta_montooperaciongravada' => number_format($total_valorventa,2, '.', ''),
                    'venta_montoigv' => number_format($total_impuesto,2, '.', ''),
                    'venta_totalimpuestos' => number_format($total_impuesto,2, '.', ''),
                    'venta_valorventa' => number_format($total_valorventa,2, '.', ''),
                    'venta_montoimpuestoventa' => number_format($total_precioventa,2, '.', ''),
                    'venta_qr' => '',
                    'leyenda_codigo' => '1000',
                    'leyenda_value' => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$venta->monedanombre,
                    'idestadofacturacion' => 0,
                    'idventa' => $venta->id,
                    'idagencia' => $request->input('idagencia'),
                    'idtienda' => usersmaster()->idtienda,
                    'idusuarioresponsable' => Auth::user()->id,
                    'idusuariocliente' => $request->input('idcliente'),
                    'idestadosunat' => 1 // pendiente
                ]);
          
                foreach($ventadetalles as $value){
                    $preciounitario = number_format($value->preciounitario,2, '.', '');
                    $precioventa = number_format($value->preciounitario*$value->cantidad,2, '.', '');
                    $valorunitario = number_format(($preciounitario/1.18),2, '.', '');
                    $valorventa = number_format($valorunitario*$value->cantidad,2, '.', '');
                    $igv = number_format($precioventa-$valorventa,2, '.', '');
                  
                    DB::table('facturacionboletafacturadetalle')->insert([
                       'codigoproducto' => str_pad($value->productocodigoimpresion, 6, "0", STR_PAD_LEFT),
                       'unidad' => $value->unidadmedidacodigo,
                       'cantidad' => $value->cantidad,
                       'descripcion' => $value->productonombre,
                       'montobaseigv' => $valorventa,
                       'porcentajeigv' => 18.00,
                       'igv' => $igv,
                       'tipoafectacionigv' => '10',
                       'totalimpuestos' => $igv,
                       'montovalorventa' => $valorventa,
                       'montovalorunitario' => $valorunitario,
                       'montopreciounitario' => $preciounitario,
                       'idproducto' => $value->idproducto,
                       'idventa' => $venta->id,
                       'idfacturacionboletafactura' => $idfacturacion
                   ]);  
                }
              
                //Enivar a SUNAT
                $resultado = facturador_facturaboleta($idfacturacion);
                /*return response()->json([
                  'resultado' => $resultado['resultado'],
                  'mensaje'   => $resultado['mensaje']
                ]);*/
            }  
            // FIN FACTURACIÓN*/
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view') == 'facturarventa') {
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
                    $cliente_razonsocial = $cliente->nombre;
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
                    'emisor_urbanizacion' => 'NONE',
                    'emisor_direccion' => $tienda->tiendadireccion,
                    'cliente_tipodocumento' => $cliente_tipodocumento,
                    'cliente_numerodocumento' => $cliente->identificacion,
                    'cliente_razonsocial' => $cliente_razonsocial,
                    'cliente_ubigeo' => $clienteubigeo->codigo,
                    'cliente_departamento' => $clienteubigeo->departamento,
                    'cliente_provincia' => $clienteubigeo->provincia,
                    'cliente_distrito' => $clienteubigeo->distrito,
                    'cliente_urbanizacion' => 'NONE',
                    'cliente_direccion' => $cliente->direccion,
                    'venta_ublversion' => '2.1',
                    'venta_tipooperacion' => '0101',
                    'venta_tipodocumento' => $venta_tipodocumento,
                    'venta_serie' => $venta_serie,
                    'venta_correlativo' => $venta_correlativo,
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
                    'idventa' => $request->input('idventa'),
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
                        'idventa' => $request->input('idventa'),
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
    }
  
    public function show(Request $request, $id) 
    {
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
                    CONCAT(users.identificacion," - ",users.nombre)) as text')
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
                ->where('venta.idestado',2)
                ->select(
                    'venta.*',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariovendedor.apellidos as apellidosusuariovendedor',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
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
                     "preciototal" => $value->preciototal
                ];
            }
          
            return [ 
              'venta' => $venta,
              'ventadetalles' => (object)$ventadetal 
            ];
        }elseif($id == 'show-seleccionarventa-facturar'){
            $venta = DB::table('venta')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->leftJoin('agencia','agencia.id','venta.idagencia')
                ->leftJoin('ubigeo as ubigeocliente','ubigeocliente.id','usuariocliente.idubigeo')
                ->where('venta.id',$request->input('idventa'))
                ->select(
                    'venta.*',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariovendedor.apellidos as apellidosusuariovendedor',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
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
                $facturacionboletafacturas = DB::table('facturacionboletafactura')
                      ->join('users as responsable','responsable.id','facturacionboletafactura.idusuarioresponsable')
                      ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idfacturacionboletafactura','facturacionboletafactura.id')
                      ->leftJoin('venta','venta.id','facturacionboletafacturadetalle.idventa')
                      ->where('facturacionboletafactura.idventa',$venta->id)
                      ->orWhere('facturacionboletafacturadetalle.idventa',$venta->id)
                      ->select(
                          'facturacionboletafacturadetalle.*'
                      )
                      ->get();
                $valid = 0;
                foreach($facturacionboletafacturas as $valuefact){
                    if($valuefact->idproducto==$value->idproducto){
                        $valid = 1;
                    }
                }
                if($valid==0){
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
                         "stock" => stock_producto(usersmaster()->idtienda,$value->idproducto)['total'],
                         "cantidad" => $value->cantidad,
                         "preciototal" => $value->preciototal
                    ];
                }
                    
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
        $venta = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('ubigeo as ubigeocliente','ubigeocliente.id','usuariocliente.idubigeo')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->leftJoin('agencia','agencia.id','venta.idagencia')
            ->where('venta.id',$id)
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
                'agencia.direccion as agenciadireccion',
                'agencia.descripcion as agenciadescripcion',
                'agencia.telefono as agenciatelefono',
                'agencia.celular as agenciacelular',
                'agencia.correo as agenciacorreo',
                'agencia.logo as agencialogo',
                'agencia.terminoycondicion as agenciaterminoycondicion',
                'moneda.nombre as monedanombre',
                'tipocomprobante.nombre as tipocomprobantenombre'
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            $agencias = DB::table('agencia')->get();
            $formapagos = DB::table('formapago')->get();
            $tipocomprobantes = DB::table('tipocomprobante')->where('id',1)->orWhere('id',2)->get();
            $monedas = DB::table('moneda')->get();
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
          
            $facturacionboletafacturas = DB::table('facturacionboletafactura')
                      ->join('users as responsable','responsable.id','facturacionboletafactura.idusuarioresponsable')
                      ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idfacturacionboletafactura','facturacionboletafactura.id')
                      ->leftJoin('venta','venta.id','facturacionboletafacturadetalle.idventa')
                      ->where('facturacionboletafactura.idventa',$venta->id)
                      ->orWhere('facturacionboletafacturadetalle.idventa',$venta->id)
                      ->select(
                          'facturacionboletafactura.*',
                          'venta.codigo as ventacodigo',
                          'responsable.nombre as responsablenombre'
                      )
                      ->distinct()
                      ->orderBy('facturacionboletafactura.id','desc')
                      ->get();
          
            
            return view('layouts/backoffice/prueba/edit',[
                'agencias' => $agencias,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
                'tipocomprobantes' => $tipocomprobantes,
                'facturacionboletafacturas' => $facturacionboletafacturas,
                'monedas' => $monedas
            ]);
        }elseif($request->input('view') == 'proforma') {
            return view('layouts/backoffice/prueba/proforma',[
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
          
            $pdf = PDF::loadView('layouts/backoffice/prueba/proforma-pdf',[
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
            return $pdf->stream();
          
        }elseif($request->input('view') == 'correo') {
            return view('layouts/backoffice/prueba/correo',[
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
            return view('layouts/backoffice/prueba/detalle',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
            ]);
        }elseif($request->input('view') == 'anular') {
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
            return view('layouts/backoffice/prueba/anular',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
            ]);
        }elseif($request->input('view') == 'eliminar') {
          return view('layouts/backoffice/prueba/delete',[
            'tienda' => $tienda,
            'venta' => $venta
          ]);
        }elseif($request->input('view') == 'reducirmonto') {
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
            return view('layouts/backoffice/prueba/reducirmonto',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
            ]);
        }
    }

    public function update(Request $request, $idventa)
    {
      
        if($request->input('view') == 'editar') {
            //
        }elseif($request->input('view') == 'correo') {
          
            $venta = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->leftJoin('agencia','agencia.id','venta.idagencia')
            ->where('venta.id',$idventa)
            ->select(
                'venta.*',
                'usuariovendedor.nombre as nombreusuariovendedor',
                'usuariovendedor.apellidos as apellidosusuariovendedor',
                DB::raw('IF(usuariocliente.idtipopersona=1,
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                'usuariocliente.direccion as direccionusuariocliente',
                'usuariocliente.idubigeo as idubigeousuariocliente',
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
                'moneda.nombre as monedanombre',
                'tipocomprobante.nombre as tipocomprobantenombre'
            )
            ->first();
            
            $rules = [
              'correo' => 'required',
            ];
            $messages = [
              'correo.required' => 'El "Correo Electrónico" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
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
          
            $pdf = PDF::loadView('layouts/backoffice/prueba/proforma-pdf',[
                'formapagos' => $formapagos,
                'venta' => $venta,
                'ventadetalles' => $ventadetalles,
                'ubigeocliente' => $ubigeocliente,
            ]);
          
            $output = $pdf->output();
          
            $ventacodigo = str_pad($venta->codigo, 8, "0", STR_PAD_LEFT);
         
            $user = array (
             'name' => strtoupper($venta->tipocomprobantenombre).' - '.$ventacodigo,
             'correo' => $request->input('correo'),
             'pdf' => $output,
             'nombrepdf'=>strtoupper($venta->tipocomprobantenombre).'_'.$ventacodigo.'.pdf'
            );
          
            Mail::send('app/templateemail',  ['user' => $user], function ($message) use ($user) {
              $message->from('ventas@dicowe.com.pe',  'DICOWE S.A.C.');
              $message->to($user['correo'])->subject($user['name']);
              $message->attachData($user['pdf'], $user['nombrepdf'], [
                      'mime' => 'application/pdf',
                  ]);
            });
          
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha enviado correctamente.'
            ]);
        }elseif($request->input('view') == 'anular') {
            
            DB::table('venta')->whereId($idventa)->update([
               'fechaanulacion' => Carbon::now(),
               'idestado' => 4,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idventa)
    {
        if($request->input('view') == 'eliminar') {
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
