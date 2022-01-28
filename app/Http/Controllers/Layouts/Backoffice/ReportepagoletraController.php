<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportepagoletraExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportepagoletraController extends Controller
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
      
        if($request->input('idusuarioresponsable')!=''){
            $where[] = ['responsable.id',$request->input('idusuarioresponsable')];
        }
      
        if($request->input('codigoletra')!=''){
            $where[] = ['pagoletra.codigo','LIKE','%'.$request->input('codigoletra').'%'];
        }
      
        if($request->input('venta')!=''){
            $where[] = ['compra.codigo','LIKE','%'.$request->input('venta').'%'];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['pagoletra.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['pagoletra.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
        
        if($request->input('tipo')=='excel'){
            $pagoletra  = DB::table('pagoletra')
              ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
              ->join('compra','compra.id','tipopagoletra.idcompra')
              ->join('users as responsable','responsable.id','pagoletra.idusuario')
              ->join('moneda','moneda.id','pagoletra.idmoneda')            
              ->leftJoin('aperturacierre','aperturacierre.id','pagoletra.idaperturacierre')
              ->leftJoin('caja','caja.id','aperturacierre.idcaja')
              ->join('tienda','tienda.id','caja.idtienda')
              ->where($where)
              ->select(
                  'pagoletra.*',
                  'tipopagoletra.numeroletra as numeroletra',
                  'responsable.nombre as responsablenombre',
                  'compra.codigo as compracodigo',
                  'moneda.codigo as monedacodigo',
                  'caja.nombre as cajanombre',
                  'tienda.nombre as tiendanombre'
              )
              ->orderBy('pagoletra.id','desc')
              ->paginate(10);
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Pago de Letras';
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
                                    ReportepagoletraExport($pagoletra, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $pagoletra  = DB::table('pagoletra')
              ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
              ->join('compra','compra.id','tipopagoletra.idcompra')
              ->join('users as responsable','responsable.id','pagoletra.idusuario')
              ->join('moneda','moneda.id','pagoletra.idmoneda')            
              ->leftJoin('aperturacierre','aperturacierre.id','pagoletra.idaperturacierre')
              ->leftJoin('caja','caja.id','aperturacierre.idcaja')
              ->join('tienda','tienda.id','caja.idtienda')
              ->where($where)
              ->select(
                  'pagoletra.*',
                  'tipopagoletra.numeroletra as numeroletra',
                  'responsable.nombre as responsablenombre',
                  'compra.codigo as compracodigo',
                  'moneda.codigo as monedacodigo',
                  'caja.nombre as cajanombre',
                  'tienda.nombre as tiendanombre'
              )
              ->orderBy('pagoletra.id','desc')
              ->paginate(10);
          
            $usuarios = DB::table('users')->where('users.idestado',1)->where('users.nombre','LIKE','%'.$request->input('buscar').'%')->get();
            $formapago = DB::table('formapago')->get();
            $tiendas = DB::table('tienda')->get();
            
            return view('layouts/backoffice/reportepagoletra/index',[
                  'pagoletra' => $pagoletra,
                  'usuarios'  => $usuarios,
                  'formapago' => $formapago,
                  'tiendas'   => $tiendas,
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
