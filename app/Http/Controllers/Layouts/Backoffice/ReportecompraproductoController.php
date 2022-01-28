<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

use App\Exports\ReportecompraproductoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportecompraproductoController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );

        $where = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];
      
        if( $request->input('responsable') != '' ){
          $where[] = ['responsable.id',$request->input('responsable')];
        }
      
        if( $request->input('proveedor') != '' ){
          $where[] = ['proveedor.id',$request->input('proveedor')];
        }
      
        if( $request->input('formapago') != '' ){
          $where[] = ['formapago.id',$request->input('formapago')];
        }
      
        if( $request->input('moneda') != '' ){
          $where[] = ['moneda.id',$request->input('moneda')];
        }
      
        if( $request->input('fechainicio') != '' ){
          $where[] = ['compra.fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if( $request->input('fechafin') != '' ){
          $where[] = ['compra.fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }

        if( $request->input('tipo') == 'excel' ){
            $compraproducto = DB::table('compradetalle')
                ->join('compra','compra.id','compradetalle.idcompra')
                ->join('users as responsable','responsable.id','compra.idusuarioresponsable')
                ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->join('formapago','formapago.id','compra.idformapago')
                ->join('tienda','tienda.id','compra.idtienda')
                ->join('productounidadmedida as unidadmedida','unidadmedida.id','compradetalle.idunidadmedida')
                ->join('producto','producto.id','compradetalle.idproducto')
                ->Where($where)
                ->select(
                          'compradetalle.*',
                          'compra.seriecorrelativo as seriecorrelativocompra',
                          'compra.fechaemision as fechaemisioncompra',
                          'responsable.nombre as nombreresponsable',
                          'proveedor.identificacion as identificacionproveedor',
                          'proveedor.nombre as nombreproveedor',
                          'formapago.nombre as nombreformapago',
                          'tienda.nombre as nombretienda',
                          'moneda.codigo as codigomoneda',
                          'producto.codigoimpresion as codigoproducto',
                           'producto.nombreproducto as nombreproducto',
//                           'producto.codigocompra as codigocompraproducto',
                          'unidadmedida.nombre as nombreunidadmedida'
                        )
                ->orderBy('compradetalle.id','desc')
                ->get();
          
              /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
              $inicio = $request->input('fechainicio');
              $fin    = $request->input('fechafin');
              $titulo = 'Reporte de Compras por Productos';
              $fecha  = '';

              if($inicio != '' && $fin != ''){
                $fecha = '('.$inicio.' hasta '.$fin.')';
              }
              elseif($inicio != ''){                
                $fecha = '('.$inicio.')';
              }
              elseif($fin != ''){
                $fecha = '('.$fin.')';
              }
              else{
                $fecha = '';
              }

              return Excel::download(new 
                                      ReportecompraproductoExport($compraproducto, $inicio, $fin, $titulo),
                                      $titulo.' '.$fecha.'.xls'
                                    );
              /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $compraproducto = DB::table('compradetalle')
                ->join('compra','compra.id','compradetalle.idcompra')
                ->join('users as responsable','responsable.id','compra.idusuarioresponsable')
                ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->join('formapago','formapago.id','compra.idformapago')
                ->join('tienda','tienda.id','compra.idtienda')
                ->join('productounidadmedida as unidadmedida','unidadmedida.id','compradetalle.idunidadmedida')
                ->join('producto','producto.id','compradetalle.idproducto')
                ->Where($where)
                ->select(
                          'compradetalle.*',
                          'compra.seriecorrelativo as seriecorrelativocompra',
                          'compra.fechaemision as fechaemisioncompra',
                          'responsable.nombre as nombreresponsable',
                          'proveedor.identificacion as identificacionproveedor',
                          'proveedor.nombre as nombreproveedor',
                          'formapago.nombre as nombreformapago',
                          'tienda.nombre as nombretienda',
                          'moneda.codigo as codigomoneda',
                          'producto.codigoimpresion as codigoproducto',
                          'producto.nombreproducto as nombreproducto',
//                           'producto.codigocompra as codigocompraproducto',
                          'unidadmedida.nombre as nombreunidadmedida'
                        )
                ->orderBy('compradetalle.id','desc')
                ->paginate(10);
          
            $formapago = DB::table('formapago')->get();
            $moneda    = DB::table('moneda')->get();
            $tiendas   = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportecompraproducto/index',[
              
               'compraproducto' => $compraproducto,
               'formapago'      => $formapago,
               'moneda'         => $moneda,
               'tiendas'        => $tiendas
              
            ]);          
        }
      
    }
  
    public function show(Request $request, $id) 
    {
        $request->user()->authorizeRoles( $request->path() );
      
        if($id == 'show-listarcliente'){
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
        }elseif($id == 'show-seleccionarcliente'){
            $usuario = DB::table('users')
                ->where('users.id',$request->input('idcliente'))
                ->select(
                    'users.*'
                )
                ->first();
          
            return [ 
              'cliente' => $usuario
            ];
        }
    }
}
