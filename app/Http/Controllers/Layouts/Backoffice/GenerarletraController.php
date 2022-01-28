<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

class GenerarletraController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
         $where = [];
        $where1 = [];
        $where2 = [];
        //if(Auth::user()->id!=1){
            $where[] = ['venta.idusuariocajero',Auth::user()->id];
            $where1[] = ['venta.idusuariocajero',Auth::user()->id];
            $where2[] = ['venta.idusuariocajero',Auth::user()->id];
        //}
        $where[] = ['venta.idestado',5]; // generar letra
        $where[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where[] = ['usuariocliente.identificacion','LIKE','%'.$request->input('cliente').'%'];
      
        $where1[] = ['venta.idestado',5];
        $where1[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where1[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where1[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where1[] = ['usuariocliente.nombre','LIKE','%'.$request->input('cliente').'%'];
      
        $where2[] = ['venta.idestado',5];
        $where2[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where2[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where2[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where2[] = ['usuariocliente.apellidos','LIKE','%'.$request->input('cliente').'%'];
      
      
        $where3[] = ['venta.idestado',4]; // generar letra
        $where3[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where3[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where3[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where3[] = ['usuariocliente.identificacion','LIKE','%'.$request->input('cliente').'%'];
      
        $where4[] = ['venta.idestado',4];
        $where4[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where4[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where4[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where4[] = ['usuariocliente.nombre','LIKE','%'.$request->input('cliente').'%'];
      
        $where5[] = ['venta.idestado',4];
        $where5[] = ['venta.codigo','LIKE','%'.$request->input('codigo').'%'];
        $where5[] = ['venta.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
        $where5[] = ['usuariovendedor.nombre','LIKE','%'.$request->input('vendedor').'%'];
        $where5[] = ['usuariocliente.apellidos','LIKE','%'.$request->input('cliente').'%'];
        
        $ventas = DB::table('venta')
            ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('formapago','formapago.id','venta.idformapago')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
            ->where($where)
            ->orWhere($where1)
            ->orWhere($where2)
            ->orWhere($where3)
            ->orWhere($where4)
            ->orWhere($where5)
            ->select(
                'venta.*',
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
      
        return view('layouts/backoffice/generarletra/index',[
            'ventas' => $ventas,
            'idapertura' => aperturacierre(usersmaster()->idtienda,Auth::user()->id)['idapertura']
        ]);
    }
  
    public function create(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/generarletra/create', [
              'monedas' => $monedas
            ]);
        }
    }
  
    public function store(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $rules = [
              'idcliente' => 'required',
              'idformapago' => 'required',
              'productos' => 'required',
            ];
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'idformapago.required' => 'La "Formad de Pago" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
          
            if($request->input('idformapago')==2){
                $rules = [
                    'idcliente' => 'required',
                    'idformapago' => 'required',
                    'creditoiniciopago' => 'required',
                    'creditofrecuencia' => 'required',
                    'creditodias' => 'required',
                    'creditoultimopago' => 'required',
                ];
            }elseif($request->input('idformapago')==3){
                $rules = [
                    'idcliente' => 'required',
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
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
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
               'idmoneda' => $request->input('idmoneda'),
               'idaperturacierre' => 0,
               'idusuariocliente' => $request->input('idcliente'),
               'idusuariovendedor' => Auth::user()->id,
               'idusuariocajero' => Auth::user()->id,
               'idagencia' =>  0,
               'idtipocomprobante' =>  1,
               'idformapago' =>  $request->input('idformapago'),
               'idtienda' =>  usersmaster()->idtienda,
               'idestado' => 5
            ]);
          
            if($request->input('idformapago')==2){
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
                        'idventa' => $idventa,
                        'idestado' => 1
                    ]);
                }
            }
            
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('ventagenerar')->insert([
                  'deudatotal' => $item[1],
                  'idventagenerado' => $item[0],
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
        $request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-seleccionarventas'){
          
            $ventas = DB::table('venta')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('users as usuariocajero','usuariocajero.id','venta.idusuariocajero')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
                ->join('aperturacierre','aperturacierre.id','venta.idaperturacierre')
                ->join('caja','caja.id','aperturacierre.idcaja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->where('venta.idusuariocliente',$request->input('idcliente'))
                ->where('venta.idformapago',2)
                ->orWhere('venta.idusuariocliente',$request->input('idcliente'))
                ->where('venta.idformapago',3)
                ->select(
                    'venta.*',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariocajero.nombre as nombreusuariocajero',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                    'formapago.nombre as formapago',
                    'moneda.simbolo as monedasimbolo',
                    'tipocomprobante.nombre as tipocomprobantenombre'
                )
                ->orderBy('venta.fechaconfirmacion','desc')
                ->get();
          
            $ventadetal = [];
            $idtienda = usersmaster()->idtienda;
            foreach($ventas as $value){
                  $totalnotadevolucion = DB::table('notadevolucion')
                      ->where('notadevolucion.idventa',$value->id)
                      ->sum('total');
                  $totalpagado = 0;
                  $deudatotal = 0;
                  if($value->idformapago==1){
                      $totalpagado = $value->montorecibido-$totalnotadevolucion;
                  }elseif($value->idformapago==2){
                      $totalpagado = DB::table('cobranzacredito')
                          ->where('idestado',2)
                          ->where('idventa',$value->id)
                          ->sum('monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalnotadevolucion;
                  }elseif($value->idformapago==3){
                      $totalpagado = DB::table('cobranzaletra')
                          ->join('ventaletra','ventaletra.id','cobranzaletra.idventaletra')
                          ->where('cobranzaletra.idestado',2)
                          ->where('ventaletra.idventa',$value->id)
                          ->sum('cobranzaletra.monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalnotadevolucion;
                  }
                $ventadetal[] = [
                     "id" => $value->id,
                     "codigo" => $value->codigo,
                     "fecharegistro" => $value->fechaconfirmacion,
                     "nombreusuariovendedor" => $value->nombreusuariovendedor,
                     "nombreusuariocajero" => $value->nombreusuariocajero,
                     "cliente" => $value->cliente,
                     "formapago" => $value->formapago,
                     "totalpagado" =>  number_format($totalpagado, 2, '.', ''),
                     "deudatotal" =>  number_format($deudatotal, 2, '.', ''),
                ];
            }
          
            return [ 
              'ventas' => $ventadetal
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
        }
    
  }
  
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $venta = DB::table('venta')
            ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
            ->join('moneda','moneda.id','venta.idmoneda')
            ->where('venta.id',$id)
            ->select(
                'venta.*',
                DB::raw('IF(usuariocliente.idtipopersona=1,
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                'moneda.nombre as monedanombre'
            )
            ->first();
      
        if($request->input('view') == 'detalle') {
            $monedas = DB::table('moneda')->get();
            $ventagenerars = DB::table('ventagenerar')
                ->join('venta','venta.id','ventagenerar.idventagenerado')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('users as usuariocajero','usuariocajero.id','venta.idusuariocajero')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('ventagenerar.idventa',$id)
                ->select(
                    'venta.*',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariocajero.nombre as nombreusuariocajero',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                    'ventagenerar.id as idventagenerar',
                    'ventagenerar.deudatotal as deudatotal',
                    'formapago.nombre as formapago',
                    'moneda.simbolo as monedasimbolo'
                )
                ->orderBy('ventagenerar.id','desc')
                ->get();
          
          
            $ventadetal = [];
            $idtienda = usersmaster()->idtienda;
            foreach($ventagenerars as $value){
                $ventadetal[] = [
                     "id" => $value->idventagenerar,
                     "codigo" => $value->codigo,
                     "fecharegistro" => $value->fechaconfirmacion,
                     "nombreusuariovendedor" => $value->nombreusuariovendedor,
                     "nombreusuariocajero" => $value->nombreusuariocajero,
                     "cliente" => $value->cliente,
                     "formapago" => $value->formapago,
                     "totalpagado" =>  0,
                     "deudatotal" =>  $value->deudatotal,
                ];
            }
            return view('layouts/backoffice/generarletra/detalle',[
                'monedas' => $monedas,
                'venta' => $venta,
                'ventagenerars' => $ventadetal,
            ]);
        }elseif($request->input('view') == 'anular') {
            $monedas = DB::table('moneda')->get();
            $ventagenerars = DB::table('ventagenerar')
                ->join('venta','venta.id','ventagenerar.idventagenerado')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('users as usuariocajero','usuariocajero.id','venta.idusuariocajero')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->where('ventagenerar.idventa',$id)
                ->select(
                    'venta.*',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariocajero.nombre as nombreusuariocajero',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombre)) as cliente'),
                    'ventagenerar.id as idventagenerar',
                    'ventagenerar.deudatotal as deudatotal',
                    'formapago.nombre as formapago',
                    'moneda.simbolo as monedasimbolo'
                )
                ->orderBy('ventagenerar.id','desc')
                ->get();
          
          
            $ventadetal = [];
            $idtienda = usersmaster()->idtienda;
            foreach($ventagenerars as $value){
                $ventadetal[] = [
                     "id" => $value->idventagenerar,
                     "codigo" => $value->codigo,
                     "fecharegistro" => $value->fechaconfirmacion,
                     "nombreusuariovendedor" => $value->nombreusuariovendedor,
                     "nombreusuariocajero" => $value->nombreusuariocajero,
                     "cliente" => $value->cliente,
                     "formapago" => $value->formapago,
                     "totalpagado" =>  0,
                     "deudatotal" =>  $value->deudatotal,
                ];
            }
            return view('layouts/backoffice/generarletra/anular',[
                'monedas' => $monedas,
                'venta' => $venta,
                'ventagenerars' => $ventadetal,
            ]);
        }
    }

    public function update(Request $request, $idventa)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
        if($request->input('view') == 'anular') {

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
        $request->user()->authorizeRoles( $request->path() );
      
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
