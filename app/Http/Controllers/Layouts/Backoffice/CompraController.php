<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $where = [];
        $where[] = ['tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
      
        if($request->input('codigo')!=''){
            $where[] = ['compra.codigo',$request->input('codigo')];
        }
        if($request->input('seriecorrelativo')!=''){
            $where[] = ['compra.seriecorrelativo',$request->input('seriecorrelativo')];
        }
        if($request->input('proveedor')!=''){
            $where[] = ['proveedor.apellidos','LIKE','%'.$request->input('proveedor').'%'];
        }
        $where1 = [];
        $where1[] = ['tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where1[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
      
        if($request->input('codigo')!=''){
            $where1[] = ['compra.codigo',$request->input('codigo')];
        }
        if($request->input('seriecorrelativo')!=''){
            $where1[] = ['compra.seriecorrelativo',$request->input('seriecorrelativo')];
        }
        if($request->input('proveedor')!=''){
            $where1[] = ['proveedor.identificacion','LIKE','%'.$request->input('proveedor').'%'];
        }
      
        $compra = DB::table('compra')
            ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
            ->join('users as responsable','responsable.id','compra.idusuarioresponsable')
            ->join('tipocomprobante','tipocomprobante.id','compra.idcomprobante')
            ->join('formapago','formapago.id','compra.idformapago')
            ->join('moneda','moneda.id','compra.idmoneda')
            ->where('compra.idtienda',usersmaster()->idtienda)
            ->where($where)
            ->orWhere('compra.idtienda',usersmaster()->idtienda)
            ->where($where1)
            ->select(
                'compra.*',
                DB::raw('IF(proveedor.idtipopersona=1,
                CONCAT(proveedor.identificacion," - ",proveedor.apellidos,", ",proveedor.nombre),
                CONCAT(proveedor.identificacion," - ",proveedor.apellidos)) as proveedor'),
                'responsable.nombre as responsablenombre',
                'formapago.nombre as nombreFormapago',
                'tipocomprobante.nombre as nombreComprobante',
                'moneda.simbolo as monedasimbolo'
            )
            ->orderBy('compra.id','desc')
            ->paginate(10);
      
        
        return view('layouts/backoffice/compra/index',[
            'compra' => $compra,
            'idapertura' => aperturacierre(usersmaster()->idtienda,Auth::user()->id)['idapertura']
        ]);
    }

    public function create(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $monedas = DB::table('moneda')->get();
            return view('layouts/backoffice/compra/create',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'monedas' => $monedas,
            ]);
        }elseif($request->input('view') == 'registrar-proveedor') {
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/compra/proveedor',[
                'tipopersonas' => $tipopersonas
            ]);
        }elseif($request->input('view') == 'productos') {
            return view('layouts/backoffice/compra/productos');
        }
    }


    public function store(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idproveedor' => 'required',
                'idestado' => 'required',
                'idmoneda' => 'required',
                'idcomprobante' => 'required',
                'seriecorrelativo' => 'required',
                'fechaemision' => 'required',
                'productos' => 'required',
            ];
            $messages = [
                'idproveedor.required' => 'El "Proveedor" es Obligatorio.',
                'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
                'seriecorrelativo.required' => 'La "Serie - Correlativo" es Obligatorio.',
                'fechaemision.required' => 'La "Fecha emisión" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
          
            $formapago_validar = formapago_validar($request->input('totalcompra'),$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
       
            $cliente = DB::table('users')->whereId($request->input('idproveedor'))->first();
            if($cliente->idtipopersona==2){
                if($cliente->apellidos==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Razón Social del Cliente!!.'
                    ]);
                }
            }
          
            if($request->input('idformapago')=='null'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La "Forma de Pago" es Obligatorio.'
                ]);
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
                        'mensaje'   => 'La Precio Unitario es 0.00.'
                    ]);
                    break;
                }elseif($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            }
          
            $compra = DB::table('compra')
                ->orderBy('compra.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($compra!=''){
                $codigo = $compra->codigo+1;
            }
          
            $creditoiniciopago = '';
            $creditofrecuencia = '';
            $creditodias = '';
            $creditoultimopago = '';
            $letraidgarante = 0;
            $letrafechainicio = '';
            $letrafrecuencia = '';
            $letracuota = '';
          
            if($request->input('idformapago')==1){
            }elseif($request->input('idformapago')==2){
                $creditoiniciopago = $request->input('creditoiniciopago');
                $creditofrecuencia = $request->input('creditofrecuencia');
                $creditodias = $request->input('creditodias');
                $creditoultimopago = $request->input('creditoultimopago');
            }elseif($request->input('idformapago')==3){
                $letraidgarante = $request->input('letraidgarante');
                $letrafechainicio = $request->input('letrafechainicio');
                $letrafrecuencia = $request->input('letrafrecuencia');
                $letracuota = $request->input('letracuota');
            }
          
            $idcompra = DB::table('compra')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'seriecorrelativo' => $request->input('seriecorrelativo'),
               'fechaemision' => $request->input('fechaemision'),
               'monto' => $request->input('totalcompra'),
               'fp_credito_fechainicio' => $creditoiniciopago,
               'fp_credito_frecuencia' => $creditofrecuencia,
               'fp_credito_dias' => $creditodias,
               'fp_credito_ultimafecha' => $creditoultimopago,
               'fp_letra_garante' => $letraidgarante,
               'fp_letra_fechainicio' => $letrafechainicio,
               'fp_letra_frecuencia' => $letrafrecuencia,
               'fp_letra_cuotas' => $letracuota,
               'idmoneda' => $request->input('idmoneda'),
               'idformapago' => $request->input('idformapago'),
               'idaperturacierre' => 0,
               'idusuarioresponsable' => Auth::user()->id,
               'idusuarioproveedor' => $request->input('idproveedor'),
               'idcomprobante' =>  $request->input('idcomprobante'),
               'idtienda' =>  usersmaster()->idtienda,
               'idestado' => $request->input('idestado'),
            ]);
            
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('compradetalle')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[4],
                  'idunidadmedida' => $item[3],
                  'idproducto' => $item[0],
                  'idcompra' => $idcompra,
                ]);
            }     
          
            DB::table('tipopagodetalle')->where('idcompra',$idcompra)->delete();
          
            //forma de pago
            formapago_insertar(
                $request,
                'compra',
                $idcompra
            );
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
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
              ->where('producto.codigoimpresion',$request->input('codigoimpresion'))
              ->first();
            return [ 'datosProducto' => $producto ];
        }elseif($id == 'show-seleccionarproducto'){
            $producto = DB::table('producto')
              ->where('producto.id',$request->input('idproducto'))
              ->first();
            return [ 'datosProducto' => $producto ];
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
        }
            
    }

    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
        $compra = DB::table('compra')
            ->join('users','users.id','compra.idusuarioproveedor')
            ->join('tipocomprobante as comprobante','comprobante.id','compra.idcomprobante')
            ->leftJoin('aperturacierre','aperturacierre.id','compra.idaperturacierre')
            ->leftJoin('caja','caja.id','aperturacierre.idcaja')
            ->leftJoin('tienda','tienda.id','caja.idtienda')
            ->where('compra.id',$id)
            ->select(
                'compra.*',
                'caja.nombre as cajanombre',
                'tienda.nombre as tiendanombre',
                'users.idubigeo as idubigeo',
                'users.identificacion as proveedoridentificacion',
                'users.nombre as proveedornombre',
                'tienda.nombre as tiendanombre',
                'comprobante.nombre as nombreComprobante'
            )
            ->first();     
        if($request->input('view') == 'editar') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $monedas = DB::table('moneda')->get();
              $compradetalles = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
            return view('layouts/backoffice/compra/edit',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'compra' => $compra,
                'monedas' => $monedas,
                'compradetalles' => $compradetalles,
            ]);
        }
      elseif($request->input('view') == 'detalle') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $monedas = DB::table('moneda')->get();
            $compradetalles = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
          
            return view('layouts/backoffice/compra/detalle',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'compra' => $compra,
                'monedas' => $monedas,
                'compradetalles' => $compradetalles,
            ]);
        }
      elseif($request->input('view') == 'confirmar') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $monedas = DB::table('moneda')->get();
          $compradetalles = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
           
          
            return view('layouts/backoffice/compra/confirmar',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'compra' => $compra,
                'monedas' => $monedas,
                'compradetalles' => $compradetalles,
            ]);
        }
      elseif($request->input('view') == 'anular') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $monedas = DB::table('moneda')->get();
            $compradetalles = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
          
            return view('layouts/backoffice/compra/anular',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'compra' => $compra,
                'monedas' => $monedas,
                'compradetalles' => $compradetalles,
            ]);
        }
      elseif($request->input('view') == 'eliminar') {
            $comprobantes = DB::table('tipocomprobante')->get();
            $formapagos = DB::table('formapago')->get();
            $monedas = DB::table('moneda')->get();
            $compradetalles = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
            return view('layouts/backoffice/compra/delete',[
                'comprobantes' => $comprobantes,
                'formapagos' => $formapagos,
                'compra' => $compra,
                'monedas' => $monedas,
                'compradetalles' => $compradetalles,
            ]);
        }
      elseif($request->input('view') == 'proforma') {
            return view('layouts/backoffice/compra/proforma',[
                'compra' => $compra,
            ]);
        }
      elseif($request->input('view') == 'proforma-pdf') {
            $formapago      = DB::table('formapago')->whereId($compra->idformapago)->first();
            $ubigeocliente  = DB::table('ubigeo')->whereid($compra->idubigeo)->first();
            $moneda         = DB::table('moneda')->whereId($compra->idmoneda)->first();
            $responsable    = DB::table('users')->whereId($compra->idusuarioresponsable)->first();
         $compraDetalle = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
            
            $pdf = PDF::loadView('layouts/backoffice/compra/proforma-pdf',[
                'compra'        => $compra,
                'formapago'     => $formapago,
                'moneda'        => $moneda,
                'responsable'   => $responsable,
                'ubigeocliente' => $ubigeocliente,
                'compradetalle' => $compraDetalle
            ]);
            return $pdf->stream();
          
        }elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/compra/ticket',[
                'compra' => $compra,
            ]);
        }
        elseif($request->input('view') == 'ticket-pdf') {
            $formapago      = DB::table('formapago')->whereId($compra->idformapago)->first();
            $ubigeocliente  = DB::table('ubigeo')->whereid($compra->idubigeo)->first();
            $moneda         = DB::table('moneda')->whereId($compra->idmoneda)->first();
            $responsable    = DB::table('users')->whereId($compra->idusuarioresponsable)->first();
           $compradetalle = DB::table('compradetalle')
              ->join('producto','producto.id','compradetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','compradetalle.idunidadmedida')
              ->where('compradetalle.idcompra',$compra->id)
              ->select(
                'compradetalle.*',
              'producto.codigoimpresion as codigoimpresion',
              'producto.nombreproducto as nombreproducto'
            
              )
              ->orderBy('compradetalle.id','asc')
              ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/compra/ticket-pdf',[
                'compra'        => $compra,
                'formapago'     => $formapago,
                'moneda'        => $moneda,
                'responsable'   => $responsable,
                'ubigeocliente' => $ubigeocliente,
                'compradetalle' => $compradetalle
            ]);
            return $pdf->stream();
          
        }
    }

    public function update(Request $request, $idcompra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idproveedor' => 'required',
                'idestado' => 'required',
                'idmoneda' => 'required',
                'idcomprobante' => 'required',
                'seriecorrelativo' => 'required',
                'fechaemision' => 'required',
                'productos' => 'required',
            ];
            $messages = [
                'idproveedor.required' => 'El "Proveedor" es Obligatorio.',
                'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
                'seriecorrelativo.required' => 'La "Serie - Correlativo" es Obligatorio.',
                'fechaemision.required' => 'La "Fecha emisión" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
          
            $formapago_validar = formapago_validar($request->input('totalcompra'),$request,$rules,$messages);
            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
      
            $cliente = DB::table('users')->whereId($request->input('idproveedor'))->first();
            if($cliente->idtipopersona==2){
                if($cliente->apellidos==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Razón Social del Cliente!!.'
                    ]);
                }
            }
          
            if($request->input('idformapago')=='null'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La "Forma de Pago" es Obligatorio.'
                ]);
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
                        'mensaje'   => 'La Precio Unitario es 0.00.'
                    ]);
                    break;
                }elseif($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            } 
          
            $creditoiniciopago = '';
            $creditofrecuencia = '';
            $creditodias = '';
            $creditoultimopago = '';
            $letraidgarante = 0;
            $letrafechainicio = '';
            $letrafrecuencia = '';
            $letracuota = '';
          
            if($request->input('idformapago')==1){
            }elseif($request->input('idformapago')==2){
                $creditoiniciopago = $request->input('creditoiniciopago');
                $creditofrecuencia = $request->input('creditofrecuencia');
                $creditodias = $request->input('creditodias');
                $creditoultimopago = $request->input('creditoultimopago');
            }elseif($request->input('idformapago')==3){
                $letraidgarante = $request->input('letraidgarante');
                $letrafechainicio = $request->input('letrafechainicio');
                $letrafrecuencia = $request->input('letrafrecuencia');
                $letracuota = $request->input('letracuota');
            }
          
            DB::table('compra')->whereId($idcompra)->update([
               'seriecorrelativo' => $request->input('seriecorrelativo'),
               'fechaemision' => $request->input('fechaemision'),
               'monto' => $request->input('totalcompra'),
               'fp_credito_fechainicio' => $creditoiniciopago,
               'fp_credito_frecuencia' => $creditofrecuencia,
               'fp_credito_dias' => $creditodias,
               'fp_credito_ultimafecha' => $creditoultimopago,
               'fp_letra_garante' => $letraidgarante,
               'fp_letra_fechainicio' => $letrafechainicio,
               'fp_letra_frecuencia' => $letrafrecuencia,
               'fp_letra_cuotas' => $letracuota,
               'idmoneda' => $request->input('idmoneda'),
               'idformapago' => $request->input('idformapago'),
               'idaperturacierre' => 0,
               'idusuarioresponsable' => Auth::user()->id,
               'idusuarioproveedor' => $request->input('idproveedor'),
               'idcomprobante' =>  $request->input('idcomprobante'),
               'idtienda' =>  usersmaster()->idtienda,
               'idestado' => $request->input('idestado')
            ]);
            
            DB::table('compradetalle')->where('idcompra',$idcompra)->delete();
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('compradetalle')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[4],
                  'idunidadmedida' => $item[3],
                  'idproducto' => $item[0],
                  'idcompra' => $idcompra,
                ]);
            }  
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idcompra',$idcompra)->delete();
            formapago_insertar(
                $request,
                'compra',
                $idcompra
            );

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') {

            $compra = DB::table('compra')->whereId($idcompra)->first();  

            $formapago_validar = formapago_validar('',$request,[],[],2);

            $this->validate($request,$formapago_validar['rules'],$formapago_validar['messages']);
          
            DB::table('compra')->whereId($idcompra)->update([
               'fechaconfirmacion' => Carbon::now(),
               'idaperturacierre'  => $formapago_validar['idaperturacierre'],
               'idtienda'          =>  usersmaster()->idtienda,
               'idestado'          => 2
            ]);
          
            //forma de pago
            DB::table('tipopagodetalle')->where('idcompra',$idcompra)->delete();

            formapago_insertar(
                $request,
                'compra',
                $idcompra
            );
          
            
            /**Actualizar Stock */
            $compradetalle = DB::table('compradetalle')
                ->where('compradetalle.idcompra',$idcompra)
                ->get();    
  
            foreach($compradetalle as $value){
                actualizar_stock(
                    'compra',
                    $idcompra,
                    $value->idproducto,
                    $value->cantidad,
                    $value->idunidadmedida,
                   1, //por
                    usersmaster()->idtienda,
                    'Ingreso'
                );     
            }
            /*Fin actualizar Stock */
            
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'anular') {

            $countcompradevolucions = DB::table('compradevolucion')
                ->where('compradevolucion.idestado',2)
                ->where('compradevolucion.idcompra',$idcompra)
                ->count();
            if($countcompradevolucions>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede anular, ya que existe productos devueltos!.'
                ]);
            }

            // Apertura de caja
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

            
            DB::table('compra')->whereId($idcompra)->update([
               'fechaanulacion' => Carbon::now(),
               'idestado' => 3
            ]);
          
            // tipo pago detalle
            DB::table('tipopagodetalle')->where('idcompra',$idcompra)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 3
            ]);

              /**Actualizar Stock */
            $compradetalle = DB::table('compradetalle')
              ->where('compradetalle.idcompra',$idcompra)
              ->get();    

            foreach($compradetalle as $value){
                actualizar_stock(
                    'compra',
                    $idcompra,
                    $value->idproducto,
                    $value->cantidad,
                    $value->idunidadmedida,
                    1, //por
                    usersmaster()->idtienda,
                    'Salida'
                );     
            }
            /*Fin actualizar Stock */
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idcompra)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($request->input('view') == 'eliminar') {
            /*DB::table('pagocredito')->where('idcompra',$idcompra)->delete();
            DB::table('pagoletra')->where('idcompra',$idcompra)->delete();*/
            DB::table('tipopagodetalle')->where('idcompra',$idcompra)->delete();
            DB::table('compradetalle')->where('idcompra',$idcompra)->delete();
            DB::table('compra')
                ->whereId($idcompra)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
