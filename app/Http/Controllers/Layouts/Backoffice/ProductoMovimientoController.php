<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ProductoMovimientoController extends Controller
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
              $where[] = ['productomovimiento.codigo','LIKE',$request->input('codigo')];
          }  
          if($request->input('estadomovimiento')!=''){
             $where[] = ['productomovimiento.idestadomovimiento',$request->input('estadomovimiento')];
          }
          $where[] = ['productomovimiento.motivo','LIKE','%'.$request->input('motivo').'%'];
          $where[] = ['tienda.nombre','LIKE','%'.$request->input('tiendanombre').'%'];
          $where[] = ['users.nombre','LIKE','%'.$request->input('responsable').'%'];

          if($request->input('fecharecepcion')!=''){
                $where[] = ['productomovimiento.fecharecepcion','>=',$request->input('fecharecepcion').' 00:00:00'];
                $where[] = ['productomovimiento.fecharecepcion','<=',$request->input('fecharecepcion').' 23:59:59'];
            }  
          $where[] = ['productomovimiento.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
          if($request->input('estado')!=''){
             $where[] = ['productomovimiento.idestado',$request->input('estado')];
          }
         $productomovimientos = DB::table('productomovimiento')
            ->join('tienda','tienda.id' ,'productomovimiento.idtienda')
            ->leftJoin('users','users.id' ,'productomovimiento.idusers')
            ->where('tienda.id',usersmaster()->idtienda)
            ->where('productomovimiento.idestado',1)
            ->where($where)
            ->orWhere('tienda.id',usersmaster()->idtienda)
            ->where('productomovimiento.idestado',2)
            ->where($where)
            ->select(
              'productomovimiento.*',
              'users.nombre as users_nombre',
              'tienda.nombre as tienda_nombre'
            )
            ->orderBy('productomovimiento.id','desc')
            ->paginate(10);
      
        /*$arr_productos = DB::table('producto')
                ->where('producto.stockminimo','>',0)
                ->orderBy('producto.id','asc')
                ->get();
      
        foreach($arr_productos as $value){
                DB::table('productomovimientodetalle')->insert([
                  'motivo' => '',
                  'cantidad' => $value->stockminimo,
                  'idunidadmedida' => $value->idproductounidadmedida,
                  'idproducto' => $value->id,
                  'idproductomovimiento' => 1,
                ]);
        }*/
      
        return view('layouts/backoffice/productomovimiento/index', [
          'productomovimientos' => $productomovimientos
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
            return view('layouts/backoffice/productomovimiento/create', [
              'tiendas' => $tiendas
            ]);
        }elseif($request->input('view') == 'productos') {
            return view('layouts/backoffice/productomovimiento/productos');
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
                'idtienda' => 'required',
                'idestadomovimiento' => 'required',
                'productos' => 'required',
            ];

            $messages = [
                'idtienda.required'   => 'La "Tienda" es Obligatorio.',
                'idestadomovimiento.required'   => 'El "Estado" es Obligatorio.',
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
                        'mensaje'   => 'La Unidad de Medida es obligatorio.'
                    ]);
                    break;
                }
            } 
        
            $productomovimiento = DB::table('productomovimiento')
                ->orderBy('productomovimiento.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($productomovimiento!=''){
                $codigo = $productomovimiento->codigo+1;
            }

            $idproductomovimiento = DB::table('productomovimiento')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'codigo' => $codigo,
              'motivo' => $request->input('motivo')!=''?$request->input('motivo'):'',
              'idtienda' => $request->input('idtienda'),
              'idusers' => Auth::user()->id,
              'idestadomovimiento' => $request->input('idestadomovimiento'),
              'idestado' => 1,
            ]);

            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('productomovimientodetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductomovimiento' => $idproductomovimiento,
                ]);
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
   
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $productomovimiento = DB::table('productomovimiento')
          ->join('tienda','tienda.id' ,'productomovimiento.idtienda')
          ->where('productomovimiento.id', $id)
          ->select(
            'productomovimiento.*',
            'tienda.nombre as tienda_nombre'
          )
          ->first();
      
        if($request->input('view') == 'editar') { 
          
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productomovimientodetalle')
              ->join('producto','producto.id','productomovimientodetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productomovimientodetalle.idunidadmedida')
              ->where('productomovimientodetalle.idproductomovimiento', $productomovimiento->id)
              ->select(
                'productomovimientodetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
//                 'producto.compatibilidadmotor as productomotor',
//                 'producto.compatibilidadmarca as productomarca',
//                 'producto.compatibilidadmodelo as productomodelo',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productomovimientodetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productomovimiento/edit', [
              'tiendas' => $tiendas,
              'productomovimiento' => $productomovimiento,
              'detalletransferencia' => $detalletransferencia
            ]);
          
        }elseif($request->input('view') == 'detalle') { 
          
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productomovimientodetalle')
              ->join('producto','producto.id','productomovimientodetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productomovimientodetalle.idunidadmedida')
              ->where('productomovimientodetalle.idproductomovimiento', $productomovimiento->id)
              ->select(
                'productomovimientodetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productomovimientodetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productomovimiento/detalle', [
              'tiendas' => $tiendas,
              'productomovimiento' => $productomovimiento,
              'detalletransferencia' => $detalletransferencia
            ]);
          
        }elseif($request->input('view') == 'eliminar') {
         
            $tiendas = DB::table('tienda')->get();
            $detalletransferencia = DB::table('productomovimientodetalle')
              ->join('producto','producto.id','productomovimientodetalle.idproducto')
              ->join('productounidadmedida','productounidadmedida.id','productomovimientodetalle.idunidadmedida')
              ->where('productomovimientodetalle.idproductomovimiento', $productomovimiento->id)
              ->select(
                'productomovimientodetalle.*',
                'producto.codigoimpresion as producodigoimpresion',
                'producto.nombreproducto as nombreproducto',
                'productounidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('productomovimientodetalle.id','asc')
              ->get();

            return view('layouts/backoffice/productomovimiento/delete', [
              'tiendas' => $tiendas,
              'productomovimiento' => $productomovimiento,
              'detalletransferencia' => $detalletransferencia
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idproductomovimiento)
    {
        if($request->input('view') == 'editar') {
            $rules = [
                'idtienda' => 'required',
                'idestadomovimiento' => 'required',
                'productos' => 'required',
            ];

            $messages = [
                'idtienda.required'   => 'La "Tienda" es Obligatorio.',
                'idestadomovimiento.required'   => 'El "Estado" es Obligatorio.',
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
          
            DB::table('productomovimiento')->whereId($idproductomovimiento)->update([
              'fecharecepcion' => Carbon::now(),
              'motivo' => $request->input('motivo')!=''?$request->input('motivo'):'',
              'idtienda' => $request->input('idtienda'),
              'idusers' => Auth::user()->id,
              'idestadomovimiento' => $request->input('idestadomovimiento'),
              'idestado' => 2
            ]);

            DB::table('productomovimientodetalle')->where('idproductomovimiento', $idproductomovimiento)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('productomovimientodetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductomovimiento' => $idproductomovimiento,
                ]);
            }       


             /**Actualizar Stock */
            $productomovimientodetalle_stock = DB::table('productomovimientodetalle')
                ->where('productomovimientodetalle.idproductomovimiento', $idproductomovimiento)
                ->get();    
          
            foreach($productomovimientodetalle_stock as $value){
                actualizar_stock(
                    'productomovimiento',
                    $idproductomovimiento,
                    $value->idproducto,
                    $value->cantidad,
                    $value->idunidadmedida,
                    1, //por
                    usersmaster()->idtienda,
                    $request->input('idestadomovimiento') == 1 ? 'Ingreso' : 'Salida'
                );     
            }
            /*Fin actualizar Stock */

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha confirmado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idproductomovimiento)
    {
        if($request->input('view') == 'eliminar') {
          
            DB::table('productomovimiento')->whereId($idproductomovimiento)->update([
              'fechaeliminado' => Carbon::now(),
              'idestado' => 3
            ]);
          
            /*DB::table('productomovimientodetalle')->where('idproductomovimiento', $idproductomovimiento)->delete();
            DB::table('productomovimiento')->whereId($idproductomovimiento)->delete();*/

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
