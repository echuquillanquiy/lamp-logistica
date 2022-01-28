<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportepagocreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportepagocreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $tienda = DB::table('tienda')->whereId(1)->first();

        $where = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];

        if($request->input('idusuarioresponsable')!=''){
            $where[] = ['responsable.id', $request->input('idusuarioresponsable')];
        }

        if($request->input('idcliente')!=''){
            $where[] = ['cliente.id', $request->input('idcliente')];
        }
      
        if($request->input('codigocredito')!=''){
            $where[] = ['pagocredito.codigo','LIKE','%'.$request->input('codigocredito').'%'];
        }
      
        if($request->input('venta')!=''){
            $where[] = ['compra.codigo','LIKE','%'.$request->input('venta').'%'];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['pagocredito.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['pagocredito.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
            $pagocredito  = DB::table('pagocredito')
                ->join('compra','compra.id','pagocredito.idcompra')
                ->join('users as cliente', 'cliente.id', 'compra.idusuarioproveedor')
                ->join('users as responsable','responsable.id','pagocredito.idusuario')
                ->join('moneda','moneda.id','pagocredito.idmoneda')
                ->join('tienda','tienda.id','pagocredito.idtienda')
                ->where($where)
                ->select(
                    'pagocredito.*',
                    'tienda.nombre as tiendanombre',
                    'responsable.nombre as responsablenombre',
                    DB::raw('IF(cliente.idtipopersona=1,
                    CONCAT(cliente.apellidos,", ",cliente.nombre),
                    CONCAT(cliente.nombre)) as cliente'),
                    'compra.codigo as compracodigo',
                    'moneda.codigo as monedacodigo'
                )
                ->orderBy('pagocredito.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Pagos de Crédito';
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
                                    ReportepagocreditoExport($pagocredito, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $pagocredito  = DB::table('pagocredito')
                ->join('compra','compra.id','pagocredito.idcompra')
                ->join('users as cliente', 'cliente.id', 'compra.idusuarioproveedor')
                ->join('users as responsable','responsable.id','pagocredito.idusuario')
                ->join('moneda','moneda.id','pagocredito.idmoneda')
                ->join('tienda','tienda.id','pagocredito.idtienda')
                ->where($where)
                ->select(
                    'pagocredito.*',
                    'tienda.nombre as tiendanombre',
                    'responsable.nombre as responsablenombre',
                    DB::raw('IF(cliente.idtipopersona=1,
                    CONCAT(cliente.apellidos,", ",cliente.nombre),
                    CONCAT(cliente.nombre)) as cliente'),
                    'compra.codigo as compracodigo',
                    'moneda.codigo as monedacodigo'
                )
                ->orderBy('pagocredito.id','desc')
                ->paginate(10);
          
              $usuarios = DB::table('users')
                ->where('users.idestado',1)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->get();
          
              $tipopago = DB::table('tipopago')->get();
              $tiendas = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportepagocredito/index',[
                  'pagocredito' => $pagocredito,
                  'usuarios' => $usuarios,
                  'tiendas' => $tiendas,
                  'tipopago' => $tipopago,
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
