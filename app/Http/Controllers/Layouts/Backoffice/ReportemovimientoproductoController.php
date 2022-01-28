<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

use App\Exports\ReportemovimientoproductoExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportemovimientoproductoController extends Controller
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
        $where[] = ['tienda.id',usersmaster()->idtienda];
      
        if($request->input('codigo') != ''){
          $where[] = ['productomovimiento.codigo','LIKE','%'.$request->input('codigo').'%'];
        }
      
        if($request->input('idestadomovimiento') != ''){
          $where[] = ['productomovimiento.idestadomovimiento',$request->input('idestadomovimiento')];
        }
      
        if($request->input('idusuario') != ''){
          $where[] = ['users.id',$request->input('idusuario')];
        }
      
        if($request->input('fechainicio') != '' ){
          $where[] = ['productomovimiento.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }

        if($request->input('fechafin') != '' ){
          $where[] = ['productomovimiento.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
            
            $movimientoproducto = DB::table('productomovimiento')
                ->join('tienda','tienda.id' ,'productomovimiento.idtienda')
                ->leftJoin('users','users.id' ,'productomovimiento.idusers')
                ->where('tienda.id',usersmaster()->idtienda)
                ->where($where)
                ->select(
                  'productomovimiento.*',
                  'users.nombre as users_nombre',
                  'tienda.nombre as tienda_nombre'
                )
                ->orderBy('productomovimiento.id','desc')
                ->paginate(10);
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Movimiento de Productos';
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
                                    ReportemovimientoproductoExport($movimientoproducto, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
          }
          else{
            
             $movimientoproducto = DB::table('productomovimiento')
                  ->join('tienda','tienda.id' ,'productomovimiento.idtienda')
                  ->leftJoin('users','users.id' ,'productomovimiento.idusers')
                  ->where('tienda.id',usersmaster()->idtienda)
                  ->where($where)
                  ->select(
                    'productomovimiento.*',
                    'users.nombre as users_nombre',
                    'tienda.nombre as tienda_nombre'
                  )
                  ->orderBy('productomovimiento.id','desc')
                  ->paginate(10);
            
              $usuarios = DB::table('users')->where('idestado',1)->get();
              $tiendas = DB::table('tienda')->get();
            
              return view('layouts/backoffice/reportemovimientoproducto/index',[
                 'movimientoproducto' => $movimientoproducto,
                 'usuarios' => $usuarios,
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
                    CONCAT(users.identificacion," - ",users.nombre)) as text'))
                ->get();          
            return $usuarios;
        }elseif($id == 'show-seleccionarcliente'){
            $usuario = DB::table('users')
                ->where('users.id',$request->input('idcliente'))
                ->select('users.*')
                ->first();          
            return [ 'cliente' => $usuario ];
        }
    }
}
