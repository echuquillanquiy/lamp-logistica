<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ProductoTransferenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {    
       $where = [];
        if($request->input('codigo')!=''){
            $where[] = ['productotransferencia.codigo','LIKE',$request->input('codigo')];
        }  
        $where[] = ['tienda_origen.nombre','LIKE','%'.$request->input('tiendaorigen').'%'];
        $where[] = ['tienda_destino.nombre','LIKE','%'.$request->input('tiendadestino').'%'];
        $where[] = ['productotransferencia.motivo','LIKE','%'.$request->input('motivo').'%'];
      
        if($request->input('fechasolicitud')!=''){
            $where[] = ['productotransferencia.fechasolicitud','>=',$request->input('fechasolicitud').' 00:00:00'];
            $where[] = ['productotransferencia.fechasolicitud','<=',$request->input('fechasolicitud').' 23:59:59'];
        }  
        if($request->input('fechaenvio')!=''){
            $where[] = ['productotransferencia.fechaenvio','>=',$request->input('fechaenvio').' 00:00:00'];
            $where[] = ['productotransferencia.fechaenvio','<=',$request->input('fechaenvio').' 23:59:59'];
        }  
        if($request->input('fecharecepcion')!=''){
            $where[] = ['productotransferencia.fecharecepcion','>=',$request->input('fecharecepcion').' 00:00:00'];
            $where[] = ['productotransferencia.fecharecepcion','<=',$request->input('fecharecepcion').' 23:59:59'];

        }  
        
        if($request->input('idestadotransferencia')!=''){
           $where[] = ['productotransferencia.idestadotransferencia',$request->input('idestadotransferencia')];
        }
       if($request->input('idestado')!=''){
            $where[] = ['productotransferencia.idestado',$request->input('idestado')];
        }
      
         $productotransferencias = DB::table('productotransferencia')
            ->join('tienda as tienda_origen','tienda_origen.id' ,'productotransferencia.idtiendaorigen')
            ->join('tienda as tienda_destino','tienda_destino.id' ,'productotransferencia.idtiendadestino')
            ->leftJoin('users as user_origen','user_origen.id' ,'productotransferencia.idusersorigen')
            ->leftJoin('users as user_destino','user_destino.id' ,'productotransferencia.idusersdestino')
            ->orWhere('tienda_origen.id',usersmaster()->idtienda)
            ->where('productotransferencia.idestado',1)
           ->where($where)
            ->orWhere('tienda_origen.id',usersmaster()->idtienda)
            ->where('productotransferencia.idestado',2)
           ->where($where)
            ->orWhere('tienda_destino.id',usersmaster()->idtienda)
            ->where('productotransferencia.idestado',1)
             ->where($where)
            ->orWhere('tienda_destino.id',usersmaster()->idtienda)
            ->where('productotransferencia.idestado',2)
           ->where($where)
            ->select(
              'productotransferencia.*',
              'user_origen.nombre as user_origen_nombre',
              'user_destino.nombre as user_destino_nombre',
              'tienda_origen.nombre as tienda_origen_nombre',
              'tienda_destino.id as id_tienda_destino',
              'tienda_destino.nombre as tienda_destino_nombre'
            )
            ->orderBy('productotransferencia.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/productotransferencia/index', [
          'productotransferencias' => $productotransferencias
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        if($request->input('view') == 'registrar') {
            $tiendas = DB::table('tienda')->get();
            return view('layouts/backoffice/productotransferencia/create', [
              'tiendas' => $tiendas
            ]);
        }elseif($request->input('view') == 'productos') {
            return view('layouts/backoffice/productotransferencia/productos');
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->input('view') == 'registrar') {
            $rules = [
                'idtiendaorigen' => 'required',
                'idtiendadestino' => 'required',
                'idestadotransferencia' => 'required',
                'productos' => 'required',
            ];

            $messages = [
                'idtiendaorigen.required'   => 'El campo "De" es Obligatorio.',
                'idtiendadestino.required'   => 'El campo "Para" es Obligatorio.',
                'idestadotransferencia.required'   => 'El "Estado" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
          
            if($request->input('idtiendaorigen')==$request->input('idtiendadestino')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No puedes transferir a la misma tienda!.'
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
                        'mensaje'   => 'La Unidad de Medida es obligatorio.'
                    ]);
                    break;
                }elseif($request->input('idestadotransferencia')==2){
                    $stock = stock_producto(usersmaster()->idtienda,$item[0])['total'];
                    if($item[1]>$stock){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay suficiente stock, ingrese otra cantidad.'
                        ]);
                        break;
                    }
                }
            } 
        
            $productotransferencia = DB::table('productotransferencia')
                ->orderBy('productotransferencia.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($productotransferencia!=''){
                $codigo = $productotransferencia->codigo+1;
            }
          
            $fechaenvio = null;
            $idusersorigen = 0;
            $idusersdestino = 0;
            //$idestado = 1;
            if($request->input('idestadotransferencia')==1){
                $idusersdestino = Auth::user()->id;
            }elseif($request->input('idestadotransferencia')==2){
                $fechaenvio = Carbon::now();
                $idusersorigen = Auth::user()->id;
                //$idestado = 2;
            }

            $idtransferencia = DB::table('productotransferencia')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'fechasolicitud' => Carbon::now(),
              'fechaenvio' => $fechaenvio,
              'codigo' => $codigo,
              'motivo' => $request->input('motivo')!=''?$request->input('motivo'):'',
              'idtiendaorigen' => $request->input('idtiendaorigen'),
              'idtiendadestino' => $request->input('idtiendadestino'),
              'idusersorigen' => $idusersorigen,
              'idusersdestino' => $idusersdestino,
              'idestadotransferencia' => $request->input('idestadotransferencia'),
              'idestado' => 2,
            ]);

            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('productotransferenciadetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'cantidadenviado' => $item[1],
                  'cantidadrecepcion' => 0,
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductotransferencia' => $idtransferencia,
                ]);
            }       

            if($request->input('idestadotransferencia')==2){ 
                /**Actualizar Stock */
                $productotransferenciadetalle_stock = DB::table('productotransferenciadetalle')
                    ->where('productotransferenciadetalle.idproductotransferencia', $idtransferencia)
                    ->get();    

                foreach($productotransferenciadetalle_stock as $value){
                    actualizar_stock(
                        'productotransferencia',
                        $idtransferencia,
                        $value->idproducto,
                        $value->cantidad,
                        $value->idunidadmedida,
                        1, //por
                        usersmaster()->idtienda,
                        'Salida'
                    );     
                }
                /*Fin actualizar Stock */
            } 

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($id == 'show-agregarproductocodigo'){
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
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $productotransferencia = DB::table('productotransferencia')
          ->join('tienda as tienda_origen','tienda_origen.id' ,'productotransferencia.idtiendaorigen')
          ->join('ubigeo as ubigeo_origen','ubigeo_origen.id' ,'tienda_origen.idubigeo')
          ->join('tienda as tienda_destino','tienda_destino.id' ,'productotransferencia.idtiendadestino')
          ->join('ubigeo as ubigeo_destino','ubigeo_destino.id' ,'tienda_destino.idubigeo')
          ->where('productotransferencia.id', $id)
          ->select(
            'productotransferencia.*',
            'tienda_origen.idubigeo as tienda_origen_idubigeo',
            'tienda_origen.nombre as tienda_origen_nombre',
            'tienda_origen.direccion as tienda_origen_direccion',
            'ubigeo_origen.nombre as ubigeo_origen_nombre',
            'tienda_destino.idubigeo as tienda_destino_idubigeo',
            'tienda_destino.nombre as tienda_destino_nombre',
            'tienda_destino.direccion as tienda_destino_direccion',
            'ubigeo_destino.nombre as ubigeo_destino_nombre'
          )
          ->first();
      
        if($request->input('view') == 'editar') { 
          
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productotransferencia/edit', [
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
            ]);
          
        }elseif($request->input('view') == 'confirmar') { 
          
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productotransferencia/confirmar', [
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
            ]);
          
        }elseif($request->input('view') == 'rechazar') { 
          
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productotransferencia/rechazar', [
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
            ]);
          
        }elseif($request->input('view') == 'detalle') { 
          
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productotransferencia/detalle', [
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
            ]);
          
        }elseif($request->input('view') == 'documento') { 
            return view('layouts/backoffice/productotransferencia/documento', [
              'productotransferencia' => $productotransferencia
            ]);
          
        }elseif($request->input('view') == 'documento-pdf') { 
          
            $tienda = DB::table('tienda')->whereId(usersmaster()->idtienda)->first();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.compatibilidadnombre as productonombre',
                'producto.compatibilidadmotor as productomotor',
                'producto.compatibilidadmarca as productomarca',
                'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();

            $pdf = PDF::loadView('layouts/backoffice/productotransferencia/documento-pdf',[
                'tienda' => $tienda,
                'productotransferencia' => $productotransferencia,
                'detalletransferencia' => $detalletransferencia
            ]);
            //return $pdf->download('invoice.pdf')
            return $pdf->stream();
          
        }elseif($request->input('view') == 'eliminar') {
         
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as productonombre',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productotransferencia/delete', [
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
            ]);
        }elseif ($request->input('view') == 'guiaremision') {
            $agencia              = DB::table('agencia')->whereId(5)->first();
            $motivoguiaremision   = DB::table('motivoguiaremision')->get();
            $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as productonombre',
                'productounidadmedida.nombre as unidadmedidanombre',
                'productounidadmedida.codigo as unidadmedidacodigo'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();
          
            return view('layouts/backoffice/productotransferencia/guiaremision', [
              'agencia'               => $agencia,
              'motivoguiaremision'    => $motivoguiaremision,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia'  => $detalletransferencia
            ]);
        }elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/productotransferencia/ticket',[
                'productotransferencia' => $productotransferencia,
            ]);
        }
        elseif($request->input('view') == 'ticket-pdf') {
                       $tienda = DB::table('tienda')->whereId(usersmaster()->idtienda)->first();

             $detalletransferencia = DB::table('productotransferenciadetalle')
              ->join('producto','producto.id','productotransferenciadetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productotransferenciadetalle.idunidadmedida')
              ->where('productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                'productotransferenciadetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as productonombre',
                'productounidadmedida.nombre as unidadmedidanombre',
                'productounidadmedida.codigo as unidadmedidacodigo'
              )
              ->orderBy('productotransferenciadetalle.id','asc')
              ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/productotransferencia/ticket-pdf',[
                'productotransferencia'        => $productotransferencia,
                'tienda'     => $tienda,
                'detalletransferencia' => $detalletransferencia
            ]);
            return $pdf->stream();
          
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtransferencia)
    {
        if($request->input('view') == 'editar') {
            $rules = [
                'idtiendaorigen' => 'required',
                'idtiendadestino' => 'required',
                'idestadotransferencia' => 'required',
                'productos' => 'required',
            ];

            $messages = [
                'idtiendaorigen.required'   => 'El campo "De" es Obligatorio.',
                'idtiendadestino.required'   => 'El campo "Para" es Obligatorio.',
                'idestadotransferencia.required'   => 'El "Estado" es Obligatorio.',
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
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            } 
          
            DB::table('productotransferencia')->whereId($idtransferencia)->update([
                'motivo' => $request->input('motivo')!=''?$request->input('motivo'):'',
                'idtiendaorigen' => $request->input('idtiendaorigen'),
                'idtiendadestino' => $request->input('idtiendadestino'),
                'idusersdestino' => Auth::user()->id,
                'idestadotransferencia' => $request->input('idestadotransferencia'),
            ]);

            DB::table('productotransferenciadetalle')->where('idproductotransferencia', $idtransferencia)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('productotransferenciadetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'cantidadenviado' => 0,
                  'cantidadrecepcion' => 0,
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductotransferencia' => $idtransferencia,
                ]);
            }       

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') { 
          
            $productotransferencias = DB::table('productotransferenciadetalle')->where('idproductotransferencia',$idtransferencia)->get();
            
            if($request->input('idestadotransferencia')==2){
                foreach($productotransferencias as $value){
                    $stock = stock_producto(usersmaster()->idtienda,$value->idproducto)['total'];
                    if($value->cantidad>$stock){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay suficiente stock, ingrese otra cantidad.'
                        ]);
                        break;
                    }
                }
            }
            
            DB::table('productotransferencia')->whereId($idtransferencia)->update([
                'fechasolicitud' => Carbon::now(),
                'idestado' => 2,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha confirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'rechazar') { 
            
            $productotransferencia = DB::table('productotransferencia')->whereId($idtransferencia)->first();
          
            if($productotransferencia->idestadotransferencia==1){
                DB::table('productotransferencia')->whereId($idtransferencia)->update([
                    'idestadotransferencia' => 1,
                    'idestado' => 1,
                ]);
            }elseif($productotransferencia->idestadotransferencia==2){
                DB::table('productotransferencia')->whereId($idtransferencia)->update([
                    'idestadotransferencia' => 1,
                    'idestado' => 1,
                ]);
            }  

            // Actualizar Stock
            $productotransferenciadetalles = DB::table('productotransferenciadetalle')
                ->join('productotransferencia', 'productotransferencia.id', 'productotransferenciadetalle.idproductotransferencia')
                ->where('productotransferenciadetalle.idproductotransferencia',$idtransferencia)
                ->select(
                    'productotransferenciadetalle.*', 
                    'productotransferencia.idtiendaorigen as idtiendaorigen',  
                    'productotransferencia.idtiendadestino as idtiendadestino' 
                )
                ->get();
            foreach($productotransferenciadetalles as $value){
                actualizar_stock(
                    'productotransferencia',
                    $idtransferencia,
                    $value->idproducto,
                    $value->cantidadenviado,
                    $value->idunidadmedida,
                    1,
                    $value->idtiendaorigen
                );     
            }
            // Fin Actualizar Stock

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha rechazado correctamente.'
            ]);
        }elseif($request->input('view') == 'recepcionar') {
            $rules = [
                'idtiendaorigen' => 'required',
                'idtiendadestino' => 'required',
                'idestadotransferencia' => 'required',
                'productos' => 'required',
            ];

            $messages = [
                'idtiendaorigen.required'   => 'El campo "De" es Obligatorio.',
                'idtiendadestino.required'   => 'El campo "Para" es Obligatorio.',
                'idtiendadestino.required'   => 'El "Estado" es Obligatorio.',
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
      
            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                if($item[5]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            } 
          
            DB::table('productotransferencia')->whereId($idtransferencia)->update([
              'fecharecepcion' => Carbon::now(),
              'idusersdestino' => Auth::user()->id,
              'idestadotransferencia' => 3,
              'idestado' => 2
            ]);
          
            DB::table('productotransferenciadetalle')->where('idproductotransferencia', $idtransferencia)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('productotransferenciadetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'cantidadenviado' => $item[4],
                  'cantidadrecepcion' => $item[5],
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductotransferencia' => $idtransferencia,
                ]);
            }
          
             // Actualizar Stock
             $productotransferenciadetalles = DB::table('productotransferenciadetalle')
                ->join('productotransferencia', 'productotransferencia.id', 'productotransferenciadetalle.idproductotransferencia')
                ->where('productotransferenciadetalle.idproductotransferencia',$idtransferencia)
                ->select(
                    'productotransferenciadetalle.*', 
                    'productotransferencia.idtiendaorigen as idtiendaorigen',  
                    'productotransferencia.idtiendadestino as idtiendadestino' 
                )
                ->get();
            foreach($productotransferenciadetalles as $value){
                actualizar_stock(
                    'productotransferencia',
                    $idtransferencia,
                    $value->idproducto,
                    $value->cantidadrecepcion,
                    $value->idunidadmedida,
                    1,
                    $value->idtiendadestino
                );  
            
                $devuelto = $value->cantidadenviado-$value->cantidadrecepcion;
                if($devuelto>0){
                    actualizar_stock(
                        'productotransferencia',
                        $idtransferencia,
                        $value->idproducto,
                        $devuelto,
                        $value->idunidadmedida,
                        1,
                        $value->idtiendaorigen
                    );     
                }
            }
            // Fin Actualizar Stock

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha enviado correctamente.'
            ]);
        }elseif($request->input('view') == 'enviar') {
            $rules = [
                'productos' => 'required',
            ];

            $messages = [
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
      
            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                $stock = stock_producto(usersmaster()->idtienda,$item[0])['total'];
                if($item[4]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }if($item[4]>$stock){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay suficiente stock, ingrese otra cantidad.'
                    ]);
                    break;
                }
            } 
          

          
            DB::table('productotransferencia')->whereId($idtransferencia)->update([
                'fechaenvio' => $fechaenvio = Carbon::now(),
                'idusersorigen' => Auth::user()->id,
                'idestadotransferencia' => $request->input('idestadotransferencia')
            ]);

            DB::table('productotransferenciadetalle')->where('idproductotransferencia', $idtransferencia)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('productotransferenciadetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'cantidadenviado' => $item[4],
                  'cantidadrecepcion' => 0,
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductotransferencia' => $idtransferencia,
                ]);
            }       

              // Actualizar Stock
            $productotransferenciadetalles = DB::table('productotransferenciadetalle')
              ->where('productotransferenciadetalle.idproductotransferencia',$idtransferencia)
              ->get();

            foreach($productotransferenciadetalles as $value){
                actualizar_stock(
                    'productotransferencia',
                    $idtransferencia,
                    $value->idproducto,
                    $value->cantidadenviado,
                    $value->idunidadmedida,
                    1,
                    usersmaster()->idtienda,
                    'Salida'
                );     
            }
            // Fin Actualizar Stock

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif ($request->input('view') == 'guiaremision') {
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
//               'observacion'      => 'required',
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
//               'observacion.required'      => 'La "Observación" es Obligatorio.',
              'productos.required'        => 'Los "Productos" son Obligatorios.',
           ];
          
           $this->validate($request,$rules,$messages);
            
           $agencia       = DB::table('agencia')->whereId($request->input('emisor'))->first();
           $destinatario  = DB::table('agencia')->whereId($request->input('destinatario'))->first();
           $ubigeoagencia = DB::table('ubigeo')->whereId($agencia->idubigeo)->first();
           $transportista = DB::table('users')->whereId($request->input('transportista'))->first();
           $motivo        = DB::table('motivoguiaremision')->whereId($request->input('motivo'))->first();
           $ubigeopartida = DB::table('ubigeo')->whereId($request->input('partidaubigeo'))->first();
           $ubigeollegada = DB::table('ubigeo')->whereId($request->input('llegadaubigeo'))->first();
           $idtienda      = usersmaster()->idtienda;

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

           $guiaremision_serie = 'T'.str_pad($tienda->tiendaserie, 3, "0", STR_PAD_LEFT);

           $correlativo = DB::table('facturacionguiaremision')
                  ->where('facturacionguiaremision.idtienda',$idtienda)
                  ->orderBy('facturacionguiaremision.guiaremision_correlativo','desc')
                  ->first();

           $correlativo = ($correlativo == '') ? 0 : $correlativo->guiaremision_correlativo;

           $guiaremision_correlativo = $correlativo + 1;
        
           $idfacturacionguiaremision = DB::table('facturacionguiaremision')->insertGetId([
                'emisor_ruc'                            => $agencia->ruc,
                'emisor_razonsocial'                    => $agencia->razonsocial,
                'emisor_nombrecomercial'                => $agencia->nombrecomercial,
                'emisor_ubigeo'                         => $agencia->idubigeo, 
                'emisor_departamento'                   => $ubigeoagencia->departamento,
                'emisor_provincia'                      => $ubigeoagencia->provincia,
                'emisor_distrito'                       => $ubigeoagencia->distrito,
                'emisor_urbanizacion'                   => '',
                'emisor_direccion'                      => $agencia->direccion,
             
                'despacho_tipodumento'                  => '',
                'despacho_serie'                        => '',
                'despacho_correlativo'                  => '',
                'despacho_fechaemision'                 => $request->input('fechaemision'),
                'despacho_destinatario_tipodocumento'   => 6,
                'despacho_destinatario_numerodocumento' => $agencia->ruc,
                'despacho_destinatario_razonsocial'     => $agencia->razonsocial,
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
                'envio_direccionllegadacodigoubigeo'    => $ubigeopartida->codigo,
                'envio_direccionllegada'                => $request->input('direccionllegada'),
                'envio_direccionpartidacodigoubigeo'    => $ubigeollegada->codigo,
                'envio_direccionpartida'                => $request->input('direccionpartida'),
             
                'guiaremision_tipodocumento'            => '09',
                'guiaremision_serie'                    => $guiaremision_serie,
                'guiaremision_correlativo'              => $guiaremision_correlativo,
                'guiaremision_fechaemision'             => '',
             
                'estadofacturacion'                     => '',
                'idestadofacturacion'                   => 0,
                'idfacturacionboletafactura'            => 0,
                'idventa'                               => 0,
                'idtransferencia'                       => $idtransferencia,
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
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtransferencia)
    {
        if($request->input('view') == 'eliminar') {
          
            DB::table('productotransferencia')->whereId($idtransferencia)->update([
                'fechaeliminado' => Carbon::now(),
                'idestado' => 3,
            ]);
          
            /*DB::table('productotransferenciadetalle')->where('idproductotransferencia', $idtransferencia)->delete();
            DB::table('productotransferencia')->whereId($idtransferencia)->delete();*/

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
