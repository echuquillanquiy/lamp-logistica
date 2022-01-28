<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportedevolucioncompraExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportedevolucioncompraController extends Controller
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
    
        if($request->input('codigo')!=''){
            $where[] = ['compradevolucion.codigo','LIKE','%'.$request->input('codigo').'%'];
        }
      
        if($request->input('idresponsable')!=''){
            $where[] = ['responsable.id',$request->input('idresponsable')];
        }
      
        if($request->input('idproveedor')!=''){
            $where[] = ['proveedor.id',$request->input('idproveedor')];
        }
      
        if($request->input('idestado')!=''){
            $where[] = ['compradevolucion.idestado',$request->input('idestado')];
        }      
      
        if($request->input('fechainicio')!=''){
            $where[] = ['compradevolucion.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['compradevolucion.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
          
            $devolucioncompra = DB::table('compradevolucion')
                ->join('compra','compra.id','compradevolucion.idcompra')
                ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->join('users as responsable','responsable.id','compradevolucion.idusers')
                ->join('tienda','tienda.id','compradevolucion.idtienda')
                ->where($where)
                ->select(
                    'compradevolucion.*',
                    'compra.codigo as compracodigo',
                    'tienda.nombre as tiendanombre',
                    'responsable.nombre as responsable',
                    'moneda.codigo as monedacodigo',
                    DB::raw('IF(proveedor.idtipopersona=1,
                    CONCAT(proveedor.identificacion," - ",proveedor.apellidos,", ",proveedor.nombre),
                    CONCAT(proveedor.identificacion," - ",proveedor.nombre)) as proveedor')                
                )
                ->orderBy('compradevolucion.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Devolución de Compras';
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
                                    ReportedevolucioncompraExport($devolucioncompra, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $devolucioncompra = DB::table('compradevolucion')
                ->join('compra','compra.id','compradevolucion.idcompra')
                ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->join('users as responsable','responsable.id','compradevolucion.idusers')
                ->join('tienda','tienda.id','compradevolucion.idtienda')
                ->where($where)
                ->select(
                    'compradevolucion.*',
                    'compra.codigo as compracodigo',
                    'tienda.nombre as tiendanombre',
                    'responsable.nombre as responsable',
                    'moneda.codigo as monedacodigo',
                    DB::raw('IF(proveedor.idtipopersona=1,
                    CONCAT(proveedor.identificacion," - ",proveedor.apellidos,", ",proveedor.nombre),
                    CONCAT(proveedor.identificacion," - ",proveedor.nombre)) as proveedor')                
                )
                ->orderBy('compradevolucion.id','desc')
                ->paginate(10);
          
            $tipopago = DB::table('tipopago')->get();
            $tiendas  = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportedevolucioncompra/index',[
                'tipopago' => $tipopago,
                'tiendas' => $tiendas,
                'devolucioncompra' => $devolucioncompra
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