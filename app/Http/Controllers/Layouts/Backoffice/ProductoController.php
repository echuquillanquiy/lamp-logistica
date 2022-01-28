<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      
        /*$arr_clientes = DB::table('producto')
                ->join('productonombre','productonombre.id','producto.idproductonombre')
                ->join('productocalidad','productocalidad.id','producto.idproductocalidad')
                ->join('productomotor','productomotor.id','producto.idproductomotor')
                ->join('productomarcavehiculo','productomarcavehiculo.id','producto.idproductomarcavehiculo')
                ->join('productoserie','productoserie.id','producto.idproductoserie')
                ->join('productomarca','productomarca.id','producto.idproductomarca')
                ->join('productomodelo','productomodelo.id','producto.idproductomodelo')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->select(
                    'producto.codigocompra as codigocompra',
                    'producto.codigoproducto as codigoproducto',
                    'productonombre.nombre as productonombre',
                    'productomotor.nombre as productomotor',
                    'productoserie.nombre as productoserie',
                    'productomodelo.nombre as productomodelo',
                    'productomarca.nombre as productomarca',
                    'productounidadmedida.nombre as productounidadmedida',
                    'producto.caracteristica as caracteristica',
                    'productocalidad.nombre as productocalidad',
                    'producto.id as idproducto'
                )
                ->orderBy('producto.orden','asc')
                ->get();
        $data = [];
        foreach($arr_clientes as $value){
            $compatibilidadcodigocompras = DB::table('compatibilidadcodigocompra')
                //->where('compatibilidadcodigocompra.nombre','<>',$value->codigocompra)
                ->where('compatibilidadcodigocompra.idproducto',$value->idproducto)
                ->select(
                    'compatibilidadcodigocompra.id as id',
                    'compatibilidadcodigocompra.nombre as nombre'
                )
                ->orderBy('compatibilidadcodigocompra.id','asc')
                ->get(); 
                
            $subdata = [];
            foreach($compatibilidadcodigocompras as $subvalue){
                $subdata[] = $subvalue->nombre;
            }
                
            $data[] = [
                'codigocompra' => $value->codigocompra,
                'compatibilidadcodigocompra' => $subdata,
                'codigoproducto' => $value->codigoproducto,
                'productonombre' => $value->productonombre,
                'productomotor' => $value->productomotor,
                'productoserie' => $value->productoserie,
                'productomodelo' => $value->productomodelo,
                'productomarca' => $value->productomarca,
                'productounidadmedida' => $value->productounidadmedida,
                'stock' => 0,
                'caracteristica' => $value->caracteristica,
                'productocalidad' => $value->productocalidad,
                'idproducto' => $value->idproducto,
            ];
        }
        $json_string = json_encode(array('data' => $data));
        $file = getcwd().'/resources/views/layouts/backoffice/producto/clientes.json';
        file_put_contents($file, $json_string);*/
        return view('layouts/backoffice/producto/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
        if($request->input('view') == 'registrar'){
            $categorias = DB::table('productocategoria')->get();
            $marcas = DB::table('productomarca')->get();
            $tallas = DB::table('productotalla')->get();

            return view('layouts/backoffice/producto/create',[
              'categorias' => $categorias,
              'marcas' => $marcas,
              'tallas' => $tallas,
            ]);
          
        }else if($request->input('view') == 'buscarbarra'){
          
            return view('layouts/backoffice/producto/buscarbarra');
          
        }elseif($request->input('view') == 'buscarbarra25x65'){
          
            if( !is_null($request->input('buscarcodigobarra')) ){
                $productoBarra = DB::table('producto')
                    ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                    ->where('codigoimpresion', $request->input('buscarcodigobarra'))
                    ->select(
                        'producto.*',
                        'productounidadmedida.nombre as productounidadmedida'
                    )
                    ->first();

                $pdf = PDF::loadView('layouts/backoffice/producto/buscarbarra25x65',[
                    'producto' => $productoBarra
                ]);
                return $pdf->stream();
            }else {
                $pdf = PDF::loadView('layouts/backoffice/producto/buscarbarra25x65',[
                    'producto' => null
                ]);
                return $pdf->stream();
            }
          
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
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombreproducto' => 'required',
                'idproductocategoria' => 'required',
                'idproductomarca' => 'required',
                'idproductotalla' => 'required',
                'preciotienda' => 'required',
                'precio' => 'required',
            ];
            $messages = [
                'nombreproducto.required' => 'El "Nombre del Producto" es Obligatorio.',
                'idproductocategoria.required' => 'La "Categoria" es Obligatorio.',
                'idproductomarca.required' => 'La "Marca" es Obligatorio.',
                'idproductotalla.required' => 'El "Modelo" es Obligatorio.',
                'preciotienda.required' => 'El "Precio Minimo" es Obligatorio.',
                'precio.required' => 'El "Precio" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
  
            $id =  DB::table('producto')->insert([
                'codigoimpresion'     =>  $request->input('codigoimpresion')!=''?$request->input('codigoimpresion'):'',
                'nombreproducto'      =>  $request->input('nombreproducto')!=''?$request->input('nombreproducto'):'',
                'preciotienda'        => $request->input('preciotienda')!=''?$request->input('preciotienda'):'',
                'precio'              => $request->input('precio')!=''?$request->input('precio'):'',
                'stockminimo'         => 10,
                'idproductocategoria'     => $request->input('idproductocategoria'),
                'idproductomarca'     => $request->input('idproductomarca'),
                'idproductotalla'    => $request->input('idproductotalla'),
                'idproductounidadmedida'  => 1,
                'idestado'            => 1
            ]);

          
            load_json_productos();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
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
    
        if($id == 'showcompatibilidad'){
            $where = [];
            if($request->input('view')=='codigocompra'){
                $where[] = ['compatibilidadcodigocompra.idproducto',$request->input('idproducto')];
            }elseif($request->input('view')=='motor'){
                $where[] = ['compatibilidadmotor.idproducto',$request->input('idproducto')];
            }elseif($request->input('view')=='serie'){
                $where[] = ['compatibilidadserie.idproducto',$request->input('idproducto')];
            }elseif($request->input('view')=='modelo'){
                $where[] = ['compatibilidadmodelo.idproducto',$request->input('idproducto')];
            }elseif($request->input('view')=='marcaproducto'){
                $where[] = ['compatibilidadmarca.idproducto',$request->input('idproducto')];
            }
            $productos = DB::table('producto')
                ->leftJoin('compatibilidadcodigocompra','compatibilidadcodigocompra.idproductocompatibilidad','producto.id')
                ->leftJoin('compatibilidadmotor','compatibilidadmotor.idproductocompatibilidad','producto.id')
                ->leftJoin('compatibilidadserie','compatibilidadserie.idproductocompatibilidad','producto.id')
                ->leftJoin('compatibilidadmodelo','compatibilidadmodelo.idproductocompatibilidad','producto.id')
                ->leftJoin('compatibilidadmarca','compatibilidadmarca.idproductocompatibilidad','producto.id')
                ->join('productonombre','productonombre.id','producto.idproductonombre')
                ->join('productocalidad','productocalidad.id','producto.idproductocalidad')
                ->join('productomotor','productomotor.id','producto.idproductomotor')
                ->join('productomarcavehiculo','productomarcavehiculo.id','producto.idproductomarcavehiculo')
                ->join('productoserie','productoserie.id','producto.idproductoserie')
                ->join('productomarca','productomarca.id','producto.idproductomarca')
                ->join('productomodelo','productomodelo.id','producto.idproductomodelo')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->where($where)
                ->select(
                    'producto.*',
                    'productonombre.nombre as productonombre',
                    'productocalidad.nombre as productocalidad',
                    'productomotor.nombre as productomotor',
                    'productomarcavehiculo.nombre as productomarcavehiculo',
                    'productoserie.nombre as productoserie',
                    'productomarca.nombre as productomarca',
                    'productomodelo.nombre as productomodelo',
                    'productounidadmedida.nombre as productounidadmedida'
                )
                ->orderBy('productonombre.nombre','asc')
                ->get(); 
            return $productos;
        }elseif($id == 'listar_users'){
            $usuarios = DB::table('users')
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre) as text')
                )
                ->get();
            return $usuarios;
        }elseif($id == 'listar_ubigeo'){
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
        }elseif($id == 'listar_productomarca'){
            $productos = DB::table('productomarca')
                ->where('productomarca.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'productomarca.id as id',
                   DB::raw('CONCAT(productomarca.nombre) as text')
                )
                ->orderBy('productomarca.nombre','asc')
                ->get();
            return $productos;
        }elseif($id == 'listar_productomodelo'){
            $productos = DB::table('productomodelo')
                ->where('productomodelo.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'productomodelo.id as id',
                   DB::raw('CONCAT(productomodelo.nombre) as text')
                )
                ->orderBy('productomodelo.nombre','asc')
                ->get();
            return $productos;
        }elseif($id == 'listar_productocaracteristica'){
            $productos = DB::table('productocaracteristica')
                ->where('productocaracteristica.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'productocaracteristica.nombre as id',
                   DB::raw('CONCAT(productocaracteristica.nombre) as text')
                )
                ->orderBy('productocaracteristica.nombre','asc')
                ->get();
            return $productos;
        }elseif($id == 'listar_productounidadmedida'){
            $productounidadmedidas = DB::table('productounidadmedida')
                ->where('productounidadmedida.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'productounidadmedida.id as id',
                   DB::raw('CONCAT(productounidadmedida.nombre) as text')
                )
                ->orderBy('productounidadmedida.nombre','asc')
                ->get();
            return $productounidadmedidas;
        }elseif($id == 'show-almacen'){
         
            //$th_nuevo_stock_b = '/ Stock B.';
//             $th_nuevo_stock_b = usersmaster()->idpermiso == 1 ? '/ Stock B.' : '';
           
          
            $tiendas = DB::table('tienda')
                ->orderBy('tienda.nombre','asc')
                ->get();
            
            $html = '<table id="myTableAlmacen" class="table table-bordered table-hover table-striped" style="width:100%">
                    <thead class="thead-dark">
                      <tr>
                          <th style="padding: 4px 4px;">Tienda (Almacén)</th>
                          <th style="padding: 4px 4px;">Stock</th>

                        </tr>
                  </thead>
                  <tbody>';
            $i=1;
            $uno_th = '';
            $dos_th = '';
            foreach($tiendas as $value){
                $stock_producto = stock_producto($value->id,$request->input('idproducto'));
//                 $tr_nuevo_stock_b = usersmaster()->idpermiso == 1 ? '|'.$stock_producto['total_registro'] : '';
                //$tr_nuevo_stock_b = '|'.$stock_producto['total_registro'];

                if($i<=3){
                    $uno_th = $uno_th.'/&/'.$value->nombre.'/,/'.$stock_producto['total'];
                }else{
                    $dos_th = $dos_th.'/&/'.$value->nombre.'/,/'.$stock_producto['total'];
                }
                $i++;
            }
          
            $list_uno = explode('/&/',$uno_th);
            $list_dos = explode('/&/',$dos_th);
          
            for($i=1;$i<count($list_uno);$i++){
                /*if(count($list_uno)>=$i){
                  
                }elseif(count($list_uno)>=$i){
                  
                }*/
                $tienda_uno = '';
                $tiendastock_uno = '';
                $tienda_dos = '';
                $tiendastock_dos = '';
                if(isset($list_uno[$i])){
                    $list_uno_data = explode('/,/',$list_uno[$i]);
                    $tienda_uno = $list_uno_data[0];
                    $tiendastock_uno = $list_uno_data[1];
                }
                
                if(isset($list_dos[$i])){
                    $list_dos_data = explode('/,/',$list_dos[$i]);
                    $tienda_dos = $list_dos_data[0];
                    $tiendastock_dos = $list_dos_data[1];
                }
                
                $html = $html.'<tr>
                          <th style="padding: 5px 5px;">'.$tienda_uno.'</th>
                          <th style="padding: 5px 5px;">'.$tiendastock_uno.'</th>
                       
                        </tr>';
            }
            /*$i=1;
            foreach($tiendas as $value){
                if($i>3){
                    $html = $html.'<tr>
                          <th style="padding: 5px 5px;">'.$value->nombre.'</th>
                          <th style="padding: 5px 5px;">'.stock_producto($value->id,$request->input('idproducto'))['total'].'</th>
                          <th style="padding: 5px 5px;">'.$value->nombre.'</th>
                          <th style="padding: 5px 5px;">'.stock_producto($value->id,$request->input('idproducto'))['total'].'</th>
                        </tr>';
                }
                $i++;
            }*/
            $html = $html.'</tbody></table>';
            
            return $html;
        }elseif($id == 'show-seleccionarproductoimagen'){
            $productoimagens = DB::table('productoimagen')
                ->where('idproducto',$request->input('idproducto'))
                ->orderBy('productoimagen.orden','desc')
                ->get();
            
            $html = '<div class="row">';
            foreach($productoimagens as $value){
                $html = $html.'<div class="col-sm-4" id="cont-imgproductogalery'.$value->id.'">
                <div style="
                    width: 100%;
                    height: 195px;
                    background-image: url('.url('public/admin/productos/'.$value->imagen).');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-position: center;
                    background-color: #31353d;
                    margin-bottom: 5px;">
                    <a href="javascript:;" onclick="removeimagenproducto('.$value->id.')" style="
                            left: 10px;
                            top: 10px;
                            font-size: 18px;
                            background-color: #c12e2e;
                            padding: 2px;
                            padding-left: 9px;
                            padding-right: 9px;
                            border-radius: 15px;
                            color: #fff;
                            font-weight: bold;
                            cursor: pointer;
                            position: absolute;
                            z-index: 10;
                        }">x</a>
                    </div></div>';
            }
            $html = $html.'</div>';
            return $html;
        }elseif($id == 'show-seleccionarproductoimagendetalle'){
            $productoimagens = DB::table('productoimagen')
                ->where('idproducto',$request->input('idproducto'))
                ->orderBy('productoimagen.orden','desc')
                ->get();
            
            $html = '<div class="row">';
            foreach($productoimagens as $value){
                $html = $html.'
                <div class="col-sm-4">
                <a href="'.url('public/admin/productos/'.$value->imagen).'" data-lightbox="gallery-group-1">
                <div 
                    style="
                    width: 100%;
                    height: 195px;
                    background-image: url('.url('public/admin/productos/'.$value->imagen).');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-position: center;
                    background-color: #31353d;
                    margin-bottom: 5px;">
                    
                    </div></a><a href="'.url('public/admin/productos/'.$value->imagen).'" download style="
                            right: 10px;
                            bottom: 10px;
                            font-size: 14px;
                            background-color: #007bff;
                            padding: 5px;
                            padding-left: 9px;
                            padding-right: 9px;
                            border-radius: 15px;
                            color: #fff;
                            font-weight: bold;
                            cursor: pointer;
                            position: absolute;
                            z-index: 10;
                        }"><i class="fas fa-lg fa-fw fa-download"></i></a></div>';
            }
            $html = $html.'</div>';
            return $html;
        }elseif($id == 'show-productogaleriaimg'){
            $productoimagen = DB::table('productoimagen')
                ->where('idproducto',$request->input('idproducto'))
                ->orderBy('productoimagen.orden','desc')
                ->limit(1)
                ->first();
            $html = '';
            if($productoimagen!=''){
                $html = '<div style="
                        width: 100%;
                        height: 112px;
                        background-image: url('.url('public/admin/productos/'.$productoimagen->imagen).');
                        background-repeat: no-repeat;
                        background-size: cover;
                        background-position: center;
                        background-color: #31353d;
                        margin-bottom: 5px;
                        cursor: pointer;"></div>';
            }
            return $html;
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
        $request->user()->authorizeRoles($request->path());
        $producto = DB::table('producto')
                ->join('productocategoria','productocategoria.id','producto.idproductocategoria')
                ->join('productomarca','productomarca.id','producto.idproductomarca')
                ->join('productotalla','productotalla.id','producto.idproductotalla')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                 ->where('producto.id',$id)
                ->select(
                    'producto.*',
                    'productounidadmedida.nombre as productounidadmedida',
                    'productocategoria.nombre as productonombrecategoria',
                    'productomarca.nombre as productonombremarca',
                    'productotalla.nombre as productonombretalla',
                )
                ->orderBy('producto.id','asc')
                ->first();
        
        if($request->input('view') == 'editar') {
            $categorias = DB::table('productocategoria')->get();
            $marcas = DB::table('productomarca')->get();
            $tallas = DB::table('productotalla')->get();
            return view('layouts/backoffice/producto/edit',[
                'producto' => $producto,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'tallas' => $tallas,
            ]);
        }elseif($request->input('view') == 'codigobarra') {
            return view('layouts/backoffice/producto/codigobarra',[
                'producto' => $producto,
            ]);
        }elseif($request->input('view') == 'codigobarra25x65') {
            $pdf = PDF::loadView('layouts/backoffice/producto/codigobarra25x65',[
                'producto' => $producto,
            ]);
            return $pdf->stream();
        }elseif($request->input('view') == 'registrarimagen') {
            return view('layouts/backoffice/producto/imagen',[
                'producto' => $producto,
            ]);
        }elseif($request->input('view') == 'imagendetalle') {
            return view('layouts/backoffice/producto/imagendetalle',[
                'producto' => $producto,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $categorias = DB::table('productocategoria')->get();
            $marcas = DB::table('productomarca')->get();
            $tallas = DB::table('productotalla')->get();
            return view('layouts/backoffice/producto/delete',[
                'producto' => $producto,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'tallas' => $tallas,
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
    public function update(Request $request, $idproducto)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view') == 'editar') {
            $rules = [
                'nombreproducto' => 'required',
                'idproductocategoria' => 'required',
                'idproductomarca' => 'required',
                'idproductotalla' => 'required',
                'preciotienda' => 'required',
                'precio' => 'required',
            ];
            $messages = [
                'nombreproducto.required' => 'El "Nombre del Producto" es Obligatorio.',
                'idproductocategoria.required' => 'La "Categoria" es Obligatorio.',
                'idproductomarca.required' => 'La "Marca" es Obligatorio.',
                'idproductotalla.required' => 'El "Modelo" es Obligatorio.',
                'preciotienda.required' => 'El "Precio Minimo" es Obligatorio.',
                'precio.required' => 'El "Precio" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $producto = DB::table('producto')
                ->where('id','<>',$idproducto)
                ->where('codigoimpresion',$request->input('codigoimpresion'))
                ->first();
            if($producto!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Código" ya existe, Ingrese Otro por favor.'
                ]);
            }
          
            DB::table('producto')->whereId($idproducto)->update([
                'codigoimpresion'     =>  $request->input('codigoimpresion')!=''?$request->input('codigoimpresion'):'',
                'nombreproducto'      =>  $request->input('nombreproducto')!=''?$request->input('nombreproducto'):'',
                'preciotienda'        => $request->input('preciotienda')!=''?$request->input('preciotienda'):'',
                'precio'              => $request->input('precio')!=''?$request->input('precio'):'',
                'idproductocategoria'     => $request->input('idproductocategoria'),
                'idproductomarca'     => $request->input('idproductomarca'),
                'idproductotalla'    => $request->input('idproductotalla'),
            ]);
            load_json_productos();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'registrarimagen'){

            if($request->file('imagen')==null){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe seleccionar una imagen!!.'
                ]);
            }
          
            $imagen = uploadfile('','',$request->file('imagen'),'/public/admin/productos/');
          
            $productoimagen = DB::table('productoimagen')
                ->where('productoimagen.idproducto',$idproducto)
                ->orderBy('productoimagen.orden','desc')
                ->limit(1)
                ->first();
            $orden = 1;
            if($productoimagen!=''){
                $orden = $productoimagen->orden+1;
            }
          
            DB::table('productoimagen')->insert([
                'orden' => $orden,
                'imagen' => $imagen,
                'idproducto' => $idproducto
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrardo correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
      
        if($request->input('view') == 'eliminar') {
     
            DB::table('producto')->whereId($id)->delete();
          
            load_json_productos();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view') == 'eliminarimagen') {
            $productoimagen = DB::table('productoimagen')->whereId($id)->first();
            if($productoimagen!=''){
                uploadfile_eliminar($productoimagen->imagen,'/public/admin/productos/');
            }
            DB::table('productoimagen')->whereId($id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
