<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

use App\Exports\ReporteventaproductoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteventaproductoController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );

        $where = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];
      
        if( $request->input('vendedor') != '' ){
          $where[] = ['vendedor.id',$request->input('vendedor')];
        }
      
        if( $request->input('cajero') != '' ){
          $where[] = ['cajero.id',$request->input('cajero')];
        }
      
        if( $request->input('cliente') != '' ){
          $where[] = ['cliente.id',$request->input('cliente')];
        }
      
        if( $request->input('formapago') != '' ){
          $where[] = ['formapago.id',$request->input('formapago')];
        }
      
        if( $request->input('moneda') != '' ){
          $where[] = ['moneda.id',$request->input('moneda')];
        }
      
        if( $request->input('fechainicio') != '' ){
          $where[] = ['venta.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if( $request->input('fechafin') != '' ){
          $where[] = ['venta.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
        }

        if( $request->input('tipo') == 'excel' ){
            $ventaproducto = DB::table('ventadetalle')
                ->join('venta','venta.id','ventadetalle.idventa')
                ->join('users as vendedor','vendedor.id','venta.idusuariovendedor')
                ->join('users as cajero','cajero.id','venta.idusuariocajero')
                ->join('users as cliente','cliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->join('tienda','tienda.id','venta.idtienda')
                ->join('producto','producto.id','ventadetalle.idproducto')
                ->join('productounidadmedida as unidadmedida','unidadmedida.id','producto.idproductounidadmedida')
                ->Where($where)
                ->select(
                  'ventadetalle.*',
                  'venta.codigo as codigoventa',
                  'venta.fechaconfirmacion as fechaventa',
                  'vendedor.nombre as nombrevendedor',
                  'cajero.nombre as nombrecajero',
                  'cliente.nombre as nombrecliente',
                  'cliente.identificacion as identificacioncliente',
                  'formapago.nombre as nombreformapago',
                  'moneda.nombre as monedanombre',
                  'tienda.nombre as nombretienda',
                  'producto.nombreproducto as nombreproducto',
                  'producto.codigoimpresion as codigoimpresionproducto',
             
                  'unidadmedida.nombre as nombreunidadmedida')
                ->orderBy('ventadetalle.id','desc')
                ->get();
              
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Ventas por Producto';
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
                                    ReporteventaproductoExport($ventaproducto, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
        }
        else{
          
            $ventaproducto = DB::table('ventadetalle')
                ->join('venta','venta.id','ventadetalle.idventa')
                ->join('users as vendedor','vendedor.id','venta.idusuariovendedor')
                ->join('users as cajero','cajero.id','venta.idusuariocajero')
                ->join('users as cliente','cliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->join('tienda','tienda.id','venta.idtienda')
                ->join('producto','producto.id','ventadetalle.idproducto')
                ->join('productounidadmedida as unidadmedida','unidadmedida.id','producto.idproductounidadmedida')
                ->Where($where)
                ->select(
                  'ventadetalle.*',
                  'venta.codigo as codigoventa',
                  'venta.fechaconfirmacion as fechaventa',
                  'vendedor.nombre as nombrevendedor',
                  'cajero.nombre as nombrecajero',
                  'cliente.nombre as nombrecliente',
                  'cliente.identificacion as identificacioncliente',
                  'formapago.nombre as nombreformapago',
                  'moneda.nombre as monedanombre',
                  'tienda.nombre as nombretienda',
                  'producto.nombreproducto as nombreproducto',
                  'producto.codigoimpresion as codigoimpresionproducto',
                  
                  'unidadmedida.nombre as nombreunidadmedida')
                ->orderBy('ventadetalle.id','desc')
                ->paginate(10);
          
            $formapago = DB::table('formapago')->get();
            $moneda    = DB::table('moneda')->get();
            $tiendas   = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reporteventaproducto/index',[
               'ventaproducto' => $ventaproducto,
               'formapago' => $formapago,
               'moneda' => $moneda,
               'tiendas' => $tiendas
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
