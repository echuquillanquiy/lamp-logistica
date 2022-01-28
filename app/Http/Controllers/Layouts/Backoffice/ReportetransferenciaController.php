<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportetransferenciaExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportetransferenciaController extends Controller
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
        $where[] = ['productotransferencia.idtiendaorigen',usersmaster()->idtienda];
      
        if($request->input('codigo')!=''){
            $where[] = ['productotransferencia.codigo',$request->input('codigo')];
        }
      
        if($request->input('motivo')!=''){
            $where[] = ['productotransferencia.motivo','LIKE','%'.$request->input('motivo').'%'];
        }
      
        if($request->input('idestado') != ''){
            $where[] = ['productotransferencia.idestadotransferencia','=',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['productotransferencia.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['productotransferencia.fecharegistro','<=',$request->input('fechafin').' 24:00:00'];
        }
      
        $where1 = [];
        $where1[] = ['productotransferencia.idtiendadestino',usersmaster()->idtienda];
      
        if($request->input('codigo')!=''){
            $where1[] = ['productotransferencia.codigo',$request->input('codigo')];
        }
      
        if($request->input('motivo')!=''){
            $where1[] = ['productotransferencia.motivo','LIKE','%'.$request->input('motivo').'%'];
        }
      
        if($request->input('idestado') != ''){
            $where1[] = ['productotransferencia.idestadotransferencia','=',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where1[] = ['productotransferencia.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where1[] = ['productotransferencia.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
        
        if($request->input('tipo')=='excel'){
          
            $productotransferencia = DB::table('productotransferencia')
                ->join('tienda as tienda_origen','tienda_origen.id' ,'productotransferencia.idtiendaorigen')
                ->join('tienda as tienda_destino','tienda_destino.id' ,'productotransferencia.idtiendadestino')
                ->leftJoin('users as user_origen','user_origen.id' ,'productotransferencia.idusersorigen')
                ->leftJoin('users as user_destino','user_destino.id' ,'productotransferencia.idusersdestino')
                ->where($where)
                ->orWhere($where1)
                ->select(
                  'productotransferencia.*',
                  'user_origen.nombre as user_origen_nombre',
                  'user_destino.nombre as user_destino_nombre',
                  'tienda_origen.nombre as tienda_origen_nombre',
                  'tienda_destino.id as id_tienda_destino',
                  'tienda_destino.nombre as tienda_destino_nombre'
                )
                ->orderBy('productotransferencia.id','desc')
                ->get(); 
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Transferencias';
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
                                    ReportetransferenciaExport($productotransferencia, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $productotransferencia = DB::table('productotransferencia')
                ->join('tienda as tienda_origen','tienda_origen.id' ,'productotransferencia.idtiendaorigen')
                ->join('tienda as tienda_destino','tienda_destino.id' ,'productotransferencia.idtiendadestino')
                ->leftJoin('users as user_origen','user_origen.id' ,'productotransferencia.idusersorigen')
                ->leftJoin('users as user_destino','user_destino.id' ,'productotransferencia.idusersdestino')
                ->where($where)
                ->orWhere($where1)
                ->select(
                  'productotransferencia.*',
                  'user_origen.nombre as user_origen_nombre',
                  'user_destino.nombre as user_destino_nombre',
                  'tienda_origen.nombre as tienda_origen_nombre',
                  'tienda_destino.id as id_tienda_destino',
                  'tienda_destino.nombre as tienda_destino_nombre'
                )
                ->orderBy('productotransferencia.id','desc')
                ->paginate(10);       
          
            $tiendas= DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportetransferencia/index',[
               'productotransferencia' => $productotransferencia,
               'tiendas' => $tiendas
            ]);
          
        }
    }    
}
