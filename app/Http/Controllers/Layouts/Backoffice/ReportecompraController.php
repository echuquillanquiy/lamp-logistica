<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReportecompraExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportecompraController extends Controller
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
            $where[] = ['compra.codigo',$request->input('codigo')];
        }
      
        if($request->input('idcomprobante')!=''){
            $where[] = ['tipocomprobante.id',$request->input('idcomprobante')];
        }
      
        if($request->input('idformapago')!=''){
            $where[] = ['compra.idformapago', $request->input('idformapago')];
        }
      
        if($request->input('seriecorrelativo')!=''){
            $where[] = ['compra.seriecorrelativo',$request->input('seriecorrelativo')];
        }
      
        if($request->input('idproveedor')!=''){
            $where[] = ['proveedor.id',$request->input('idproveedor')];
        }
      
        if($request->input('idestado')!=''){
            $where[] = ['compra.idestado',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['compra.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['compra.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
          
            $compra = DB::table('compra')
                ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
                ->join('users as vendedor','vendedor.id','compra.idusuarioresponsable')
                ->join('tipocomprobante','tipocomprobante.id','compra.idcomprobante')
                ->join('formapago','formapago.id','compra.idformapago')
                ->join('tienda','tienda.id','compra.idtienda')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->where($where)
                ->select(
                    'compra.*',
                    'proveedor.idtipopersona as tipopersonaProveedor',
                    'proveedor.identificacion as identificacionProveedor',
                    'proveedor.nombre as nombreProveedor',
                    'proveedor.apellidos as apellidoProveedor',
                    'vendedor.nombre as nombrevendedor',
                    'vendedor.apellidos as apellidovendedor',
                    'tipocomprobante.nombre as nombreComprobante',
                    'formapago.nombre as formapagonombre',
                    'tienda.nombre as tiendanombre',              
                    'moneda.codigo as monedacodigo'
                )
                ->orderBy('compra.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $idcomprobante = $request->input('idcomprobante');
            if($idcomprobante != ''){
              $comprobante  = DB::table('tipocomprobante')->whereId($idcomprobante)->first();              
            }else{
              $comprobante  = 0;
            }
            $inicio       = $request->input('fechainicio');
            $fin          = $request->input('fechafin');
            $titulo       = 'Reporte de Compras';
            $fecha        = '';

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
                                    ReportecompraExport($compra, $inicio, $fin, $comprobante, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
            $compra = DB::table('compra')
                ->join('users as proveedor','proveedor.id','compra.idusuarioproveedor')
                ->join('users as vendedor','vendedor.id','compra.idusuarioresponsable')
                ->join('tipocomprobante','tipocomprobante.id','compra.idcomprobante')
                ->join('formapago','formapago.id','compra.idformapago')
                ->join('tienda','tienda.id','compra.idtienda')
                ->join('moneda','moneda.id','compra.idmoneda')
                ->where($where)
                ->select(
                    'compra.*',
                    'proveedor.idtipopersona as tipopersonaProveedor',
                    'proveedor.identificacion as identificacionProveedor',
                    'proveedor.nombre as nombreProveedor',
                    'proveedor.apellidos as apellidoProveedor',
                    'vendedor.nombre as nombrevendedor',
                    'vendedor.apellidos as apellidovendedor',
                    'tipocomprobante.nombre as nombreComprobante',
                    'formapago.nombre as formapagonombre',
                    'tienda.nombre as tiendanombre',              
                    'moneda.codigo as monedacodigo'
                )
                ->orderBy('compra.id','desc')
                ->paginate(10);
          
            $tipopersonas = DB::table('tipopersona')->get();
            $comprobante  = DB::table('tipocomprobante')->get();
            $formapago     = DB::table('formapago')->get();
            $tiendas      = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportecompra/index',[
              
                  'tipopersonas'  => $tipopersonas,
                  'comprobante'   => $comprobante,
                  'formapago'      => $formapago,
                  'tiendas'       => $tiendas,
                  'compra'        => $compra
              
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