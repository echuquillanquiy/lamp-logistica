<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportedevolucionnotaExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportedevolucionnotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );

        $where   = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];
    
        if( $request->input('codigo') != '' ){
            $where[] = ['notadevolucion.codigo','LIKE','%'.$request->input('codigo').'%'];
        }
      
        if( $request->input('idresponsable') != '' ){
            $where[] = ['responsable.id',$request->input('idresponsable')];
        }
      
        if( $request->input('idestado') != '' ){
            $where[] = ['notadevolucion.idestado',$request->input('idestado')];
        }
      
        if( $request->input('fechainicio') != '' ){
            $where[] = ['notadevolucion.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if( $request->input('fechafin') != '' ){
            $where[] = ['notadevolucion.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo') == 'excel'){
            $devolucionnota = DB::table('notadevolucion')
                ->join( 'tienda', 'tienda.id', 'notadevolucion.idtienda')
                ->join( 'venta', 'venta.id', 'notadevolucion.idventa')
                ->join( 'users as responsable', 'responsable.id', 'notadevolucion.idusuarioresponsable')
                ->join( 'aperturacierre', 'aperturacierre.id', 'notadevolucion.idaperturacierre')
                ->join( 'caja', 'caja.id', 'aperturacierre.idcaja')
                ->join( 'moneda', 'moneda.id', 'notadevolucion.idmoneda')
                ->where($where)
                ->select(
                    'notadevolucion.*',
                    'tienda.nombre as nombretienda',
                    'venta.codigo as codigoventa',
                    'responsable.nombre as nombreresponsable',
                    'caja.nombre as nombrecaja',
                    'moneda.codigo as codigomoneda'
                )
                ->orderBy('notadevolucion.id','desc')
                ->get();
            
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio      = $request->input('fechainicio');
            $fin         = $request->input('fechafin');
            $titulo      = 'Reporte de Notas de Devolución';
            $fecha       = '';

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
                                    ReportedevolucionnotaExport($devolucionnota, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $devolucionnota = DB::table('notadevolucion')
                ->join( 'tienda', 'tienda.id', 'notadevolucion.idtienda')
                ->join( 'venta', 'venta.id', 'notadevolucion.idventa')
                ->join( 'users as responsable', 'responsable.id', 'notadevolucion.idusuarioresponsable')
                ->join( 'aperturacierre', 'aperturacierre.id', 'notadevolucion.idaperturacierre')
                ->join( 'caja', 'caja.id', 'aperturacierre.idcaja')
                ->join( 'moneda', 'moneda.id', 'notadevolucion.idmoneda')
                ->where($where)
                ->select(
                    'notadevolucion.*',
                    'tienda.nombre as nombretienda',
                    'venta.codigo as codigoventa',
                    'responsable.nombre as nombreresponsable',
                    'caja.nombre as nombrecaja',
                    'moneda.codigo as codigomoneda'
                )
                ->orderBy('notadevolucion.id','desc')
                ->paginate(10);
          
            $tipopago = DB::table('tipopago')->get();
            $tiendas  = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportedevolucionnota/index',[
                'tipopago' => $tipopago,
                'tiendas' => $tiendas,
                'devolucionnota' => $devolucionnota
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