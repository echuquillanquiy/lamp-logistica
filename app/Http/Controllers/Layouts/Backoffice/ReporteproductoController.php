<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\User;

use App\Exports\InvoicesExport;
use App\Exports\ReporteproductoExport;
use Maatwebsite\Excel\Facades\Excel;


class ReporteproductoController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $where = [];
      
        if($request->input('codigo') != ''){
          $where[] =  ['producto.codigoimpresion', $request->input('codigo')];
        }

        if($request->input('nombre') != ''){
          $where[] =  ['producto.nombreproducto', 'LIKE', '%'.$request->input('nombre').'%'];
        }

        if($request->input('categoria') != ''){
          $where[] =  ['productocategoria.nombre', 'LIKE', '%'.$request->input('categoria').'%'];
        }

        if($request->input('talla') != ''){
          $where[] =  ['productotalla.nombre', 'LIKE', '%'.$request->input('talla').'%'];
        }

        if($request->input('marca') != ''){
          $where[] =  ['productomarca.nombre', 'LIKE', '%'.$request->input('marca').'%'];
        }
        if($request->input('tipo')=='excel'){
          
            $producto = DB::table('producto')
              ->join('productocategoria','productocategoria.id','producto.idproductocategoria')
                ->join('productomarca','productomarca.id','producto.idproductomarca')
                ->join('productotalla','productotalla.id','producto.idproductotalla')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->where($where)
                ->select(
                    'producto.*',
                    'producto.nombreproducto as nombreproducto',
              'producto.codigoimpresion as codigoimpresion',
                      'productocategoria.nombre as productonombrecategoria',
                    'productomarca.nombre as productonombremarca',
                    'productotalla.nombre as productonombretalla',
//                     'producto.compatibilidadcalidad as productocalidad',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadserie as productoserie',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as productounidadmedida'
                )
                ->orderBy('producto.nombreproducto','ASC')
                ->get(); 
       
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
           $titulo      = 'Reporte de Productos';

            return Excel::download(new 
                                    ReporteproductoExport($producto, $titulo),
                                    $titulo.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }else {
          
            $producto = DB::table('producto')
              ->join('productocategoria','productocategoria.id','producto.idproductocategoria')
                ->join('productomarca','productomarca.id','producto.idproductomarca')
                ->join('productotalla','productotalla.id','producto.idproductotalla')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->where($where)
                ->select(
                    'producto.*',
                    'producto.nombreproducto as nombreproducto',
                            'producto.codigoimpresion as codigoimpresion',
                      'productocategoria.nombre as productonombrecategoria',
                    'productomarca.nombre as productonombremarca',
                    'productotalla.nombre as productonombretalla',
//                     'producto.compatibilidadcalidad as productocalidad',
//                     'producto.compatibilidadmotor as productomotor',
//                     'producto.compatibilidadserie as productoserie',
//                     'producto.compatibilidadmarca as productomarca',
//                     'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as productounidadmedida'
                )
                ->orderBy('producto.nombreproducto','ASC')
                ->paginate(10); 
          
            $tiendas= DB::table('tienda')->get();
          
            return view('layouts/backoffice/reporteproducto/index',[
               'producto' => $producto,
               'tiendas' => $tiendas
            ]);      
          
        }      
    }
  
    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
      
        return view('layouts/backoffice/reporteproducto/create');
    }
  
    public function show(Request $request, $id) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-seleccionarclaveinterna'){
            $tienda = DB::table('tienda')
                ->where('tienda.id',usersmaster()->idtienda)
                ->where('tienda.claveinterna',$request->input('claveinterna'))
                ->select(
                    'tienda.*'
                )
                ->first();
          
            $respuesta  =  'ERROR';
                if(!is_null($tienda)){
                   $respuesta   = 'CORRECTO';
                }
            return [ 
                    'respuesta' => $respuesta,
                   ];
        }
    }
  
}
