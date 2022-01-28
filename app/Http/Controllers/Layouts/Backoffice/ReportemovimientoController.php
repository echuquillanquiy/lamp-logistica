<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

use App\Exports\ReportemovimientoExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportemovimientoController extends Controller
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
            $where[] = ['movimiento.codigo','LIKE','%'.$request->input('codigo').'%'];
        }
      
        if($request->input('tipomovimiento') != ''){
            $where[] = ['tipomovimiento.id',$request->input('tipomovimiento')];
        }
      
        if($request->input('concepto') != ''){
            $where[] = ['movimiento.concepto','LIKE','%'.$request->input('concepto').'%'];
        }
      
        if($request->input('idusuarioresponsable') != ''){
            $where[] = ['responsable.id',$request->input('idusuarioresponsable')];
        }
      
        if($request->input('fechainicio') != ''){
            $where[] = ['movimiento.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin') != ''){
            $where[] = ['movimiento.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
          
            $movimiento  = DB::table('movimiento')
                ->join('tipomovimiento','tipomovimiento.id','movimiento.idtipomovimiento')
                ->join('moneda','moneda.id','movimiento.idmoneda')
                ->join('tienda','tienda.id','movimiento.idtienda')
                ->join('users as responsable','responsable.id','movimiento.idusuario')
                ->where($where)
                ->select(
                    'movimiento.*',
                    'tienda.nombre as tiendanombre',
                    'tipomovimiento.nombre as tipomovimientonombre',
                    'responsable.nombre as responsablenombre',
                    'responsable.apellidos as responsableapellidos',
                    'moneda.nombre as monedanombre'
                )
                ->orderBy('movimiento.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Movimientos';
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
                                    ReportemovimientoExport($movimiento, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }
        else{
          
            $movimiento  = DB::table('movimiento')
                ->join('tipomovimiento','tipomovimiento.id','movimiento.idtipomovimiento')
                ->join('moneda','moneda.id','movimiento.idmoneda')
                ->join('tienda','tienda.id','movimiento.idtienda')
                ->join('users as responsable','responsable.id','movimiento.idusuario')
                ->where($where)
                ->select(
                    'movimiento.*',
                    'tienda.nombre as tiendanombre',
                    'tipomovimiento.nombre as tipomovimientonombre',
                    'responsable.nombre as responsablenombre',
                    'responsable.apellidos as responsableapellidos',
                    'moneda.nombre as monedanombre'
                )
                ->orderBy('movimiento.id','desc')
                ->paginate(10);
          
            $usuarios = DB::table('users')
                        ->where('users.idestado',1)
                        ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                        ->get();
          
            $tipomovimiento = DB::table('tipomovimiento')->get();
            $tipopago       = DB::table('tipopago')->get();
            $tiendas        = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportemovimiento/index',[
                'tipomovimiento'  => $tipomovimiento,
                'movimiento'      => $movimiento,
                'usuarios'        => $usuarios,
                'tipopago'        => $tipopago,
                'tiendas'         => $tiendas
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
        $request->user()->authorizeRoles( $request->path() );
      
        if($id=='showlistarusuario'){
          
            $usuarios = DB::table('users')
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'users.id as id',
                     DB::raw('CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre) as text')
                )
                ->get();
          
            return $usuarios;
          
        }
    }
}
