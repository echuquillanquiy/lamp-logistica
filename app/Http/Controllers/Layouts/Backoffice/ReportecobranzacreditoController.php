<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportecobranzacreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportecobranzacreditoController extends Controller
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
     
        if($request->input('idcliente')!=''){
          $where[] = ['cliente.id',$request->input('idcliente')];
        }
     
        if($request->input('idusuario')!=''){
          $where[] = ['responsable.id',$request->input('idusuario')];
        }
      
        if($request->input('codigocredito')!=''){
          $where[] = ['cobranzacredito.codigo','LIKE','%'.$request->input('codigocredito').'%'];
        }
      
        if($request->input('ventacodigo')!=''){
          $where[] = ['venta.codigo','LIKE','%'.$request->input('ventacodigo').'%'];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['cobranzacredito.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['cobranzacredito.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
            $cobranzacredito  = DB::table('cobranzacredito')
                ->join('venta','venta.id','cobranzacredito.idventa')
                ->join('users as cliente','cliente.id','venta.idusuariocliente')
                ->join('users as responsable','responsable.id','cobranzacredito.idusuario')
                ->join('moneda','moneda.id','cobranzacredito.idmoneda')
                ->leftJoin('aperturacierre','aperturacierre.id','cobranzacredito.idaperturacierre')
                ->leftJoin('caja','caja.id','aperturacierre.idcaja')
                ->leftJoin('tienda','tienda.id','caja.idtienda')
                ->where($where)
                ->select(
                    'cobranzacredito.*',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre',
                    DB::raw('IF(cliente.idtipopersona=1,
                    CONCAT(cliente.apellidos,", ",cliente.nombre),
                    CONCAT(cliente.nombre)) as cliente'),
                    'responsable.nombre as responsablenombre',
                    'venta.codigo as ventacodigo',
                    'moneda.codigo as monedacodigo'
                )
                ->orderBy('cobranzacredito.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Cobranza de Créditos';
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
                                    ReportecobranzacreditoExport($cobranzacredito, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $cobranzacredito  = DB::table('cobranzacredito')
                ->join('venta','venta.id','cobranzacredito.idventa')
                ->join('users as cliente','cliente.id','venta.idusuariocliente')
                ->join('users as responsable','responsable.id','cobranzacredito.idusuario')
                ->join('moneda','moneda.id','cobranzacredito.idmoneda')
                ->leftJoin('aperturacierre','aperturacierre.id','cobranzacredito.idaperturacierre')
                ->leftJoin('caja','caja.id','aperturacierre.idcaja')
                ->leftJoin('tienda','tienda.id','caja.idtienda')
                ->where($where)
                ->select(
                    'cobranzacredito.*',
                    'caja.nombre as cajanombre',
                    'tienda.nombre as tiendanombre',
                    DB::raw('IF(cliente.idtipopersona=1,
                    CONCAT(cliente.apellidos,", ",cliente.nombre),
                    CONCAT(cliente.nombre)) as cliente'),
                    'responsable.nombre as responsablenombre',
                    'venta.codigo as ventacodigo',
                    'moneda.codigo as monedacodigo'
                )
                ->orderBy('cobranzacredito.id','desc')
                ->paginate(10);
          
            $usuarios = DB::table('users')->where('idestado',1)->get();
            $tiendas = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportecobranzacredito/index',[
             'cobranzacredito' => $cobranzacredito,
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
