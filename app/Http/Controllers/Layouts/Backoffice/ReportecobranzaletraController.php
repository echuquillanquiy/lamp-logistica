<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;


use App\Exports\ReportecobranzaletraExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportecobranzaletraController extends Controller
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
        
        if($request->input('idusuario') != '' ){
            $where[] = ['responsable.id',$request->input('idusuario')];
        }
      
        if($request->input('codigoletra') != '' ){
            $where[] = ['cobranzaletra.codigo','=',$request->input('codigoletra')];
        }
      
        if($request->input('ventacodigo') != '' ){
            $where[] = ['venta.codigo','=',$request->input('ventacodigo')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['cobranzaletra.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }   
      
        if($request->input('fechafin')!=''){
            $where[] = ['cobranzaletra.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
            $cobranzaletra  = DB::table('cobranzaletra')
                ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                ->join('venta','venta.id','tipopagoletra.idventa')
                ->join('users as responsable','responsable.id','cobranzaletra.idusuario')
                ->join('moneda','moneda.id','cobranzaletra.idmoneda')
                ->leftJoin('aperturacierre','aperturacierre.id','cobranzaletra.idaperturacierre')
                ->leftJoin('caja','caja.id','aperturacierre.idcaja')
                ->leftJoin('tienda','tienda.id','caja.idtienda')
                ->where($where)
                ->select(
                    'cobranzaletra.*',
                    'tipopagoletra.numeroletra as numeroletra',
                    'responsable.nombre as responsablenombre',
                    'venta.codigo as ventacodigo',
                    'moneda.codigo as monedacodigo',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('cobranzaletra.id','desc')
                ->get(); 
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Cobranza de Letras';
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
                                    ReportecobranzaletraExport($cobranzaletra, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
            $cobranzaletra  = DB::table('cobranzaletra')
                ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                ->join('venta','venta.id','tipopagoletra.idventa')
                ->join('users as responsable','responsable.id','cobranzaletra.idusuario')
                ->join('moneda','moneda.id','cobranzaletra.idmoneda')
                ->leftJoin('aperturacierre','aperturacierre.id','cobranzaletra.idaperturacierre')
                ->leftJoin('caja','caja.id','aperturacierre.idcaja')
                ->leftJoin('tienda','tienda.id','caja.idtienda')
                ->where($where)
                ->select(
                    'cobranzaletra.*',
                    'tipopagoletra.numeroletra as numeroletra',
                    'responsable.nombre as responsablenombre',
                    'venta.codigo as ventacodigo',
                    'moneda.codigo as monedacodigo',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('cobranzaletra.id','desc')
                ->paginate(10);
          
            $tipopago = DB::table('tipopago')->get();
            $usuarios = DB::table('users')->where('idestado',1)->get();
            $tiendas = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportecobranzaletra/index',[
              'cobranzaletra' => $cobranzaletra,
              'tipopago' => $tipopago,
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
