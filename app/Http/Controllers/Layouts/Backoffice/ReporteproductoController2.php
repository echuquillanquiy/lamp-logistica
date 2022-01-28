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
      
        if($request->input('codFabricante') != ''){
          $where[] =  ['producto.codigocompra', 'LIKE', '%'.$request->input('codFabricante').'%'];
        }

        if($request->input('codigo') != ''){
          $where[] =  ['producto.codigoimpresion', $request->input('codigo')];
        }

        if($request->input('nombre') != ''){
          $where[] =  ['producto.compatibilidadnombre', 'LIKE', '%'.$request->input('nombre').'%'];
        }

        if($request->input('nombreIngles') != ''){
          $where[] =  ['producto.compatibilidadnombreingles', 'LIKE', '%'.$request->input('nombreIngles').'%'];
        }

        if($request->input('motor') != ''){
          $where[] =  ['productomotor.nombre', 'LIKE', '%'.$request->input('motor').'%'];
        }

        if($request->input('serie') != ''){
          $where[] =  ['productoserie.nombre', 'LIKE', '%'.$request->input('serie').'%'];
        }

        if($request->input('modelo') != ''){
          $where[] =  ['productomodelo.nombre', 'LIKE', '%'.$request->input('modelo').'%'];
        }

        if($request->input('marca') != ''){
          $where[] =  ['productomarca.nombre', 'LIKE', '%'.$request->input('marca').'%'];
        }
        if($request->input('tipo')=='excel'){
          
            $producto = DB::table('producto')
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->where($where)
                ->select(
                    'producto.*',
                    'producto.compatibilidadnombre as productonombre',
                    'producto.compatibilidadcalidad as productocalidad',
                    'producto.compatibilidadmotor as productomotor',
                    'producto.compatibilidadserie as productoserie',
                    'producto.compatibilidadmarca as productomarca',
                    'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as productounidadmedida'
                )
                ->orderBy('producto.compatibilidadnombre','ASC')
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
                ->join('productounidadmedida','productounidadmedida.id','producto.idproductounidadmedida')
                ->where($where)
                ->select(
                    'producto.*',
                    'producto.compatibilidadnombre as productonombre',
                    'producto.compatibilidadcalidad as productocalidad',
                    'producto.compatibilidadmotor as productomotor',
                    'producto.compatibilidadserie as productoserie',
                    'producto.compatibilidadmarca as productomarca',
                    'producto.compatibilidadmodelo as productomodelo',
                    'productounidadmedida.nombre as productounidadmedida'
                )
                ->orderBy('producto.compatibilidadnombre','ASC')
                ->paginate(10); 
          
            $tiendas= DB::table('tienda')->get();
          
            return view('layouts/backoffice/reporteproducto/index',[
               'producto' => $producto,
               'tiendas' => $tiendas
            ]);      
          
        }      
    }
}
