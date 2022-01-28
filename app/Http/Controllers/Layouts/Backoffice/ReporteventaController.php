<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

use App\Exports\ReporteventaExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReporteventaController extends Controller
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
            $where[] = ['venta.codigo',$request->input('codigo').'%'];
        }
      
        if($request->input('idusuariovendedor')!=''){
            $where[] = ['usuariovendedor.id',$request->input('idusuariovendedor')];
        }
      
        if($request->input('nombreCliente')!=''){
            $where[] = ['usuariocliente.id',$request->input('nombreCliente')];
        }
      
        if($request->input('nombreCajero')!=''){
            $where[] = ['usuariocajero.id','LIKE','%'.$request->input('nombreCajero').'%'];
        }
      
        if($request->input('idformapago')!=''){
            $where[] = ['venta.idformapago',$request->input('idformapago')];
        }   
      
        if($request->input('idestado')!=''){
            $where[] = ['venta.idestado',$request->input('idestado')];
        }   
      
        if($request->input('fechainicio')!=''){
            $where[] = ['venta.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['venta.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
          
           $venta = DB::table('venta')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocajero','usuariocajero.id','venta.idusuariocajero')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
                ->join('tienda','tienda.id','venta.idtienda')
                ->Where($where)
                ->select(
                    'venta.*',
                    'tienda.nombre as tiendanombre',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariocajero.nombre as nombreusuariocajero',
                    'usuariocliente.identificacion as identificacioncliente',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.nombre)) as cliente'),
                    'formapago.nombre as nombreFormapago',
                    'moneda.nombre as monedanombre',
                    'tipocomprobante.nombre as tipocomprobantenombre'
                )
                ->orderBy('venta.fechaconfirmacion','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Ventas';
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
                                    ReporteventaExport($venta, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }
        else{
          
           $venta = DB::table('venta')
                ->join('users as usuariovendedor','usuariovendedor.id','venta.idusuariovendedor')
                ->join('users as usuariocajero','usuariocajero.id','venta.idusuariocajero')
                ->join('users as usuariocliente','usuariocliente.id','venta.idusuariocliente')
                ->join('formapago','formapago.id','venta.idformapago')
                ->join('moneda','moneda.id','venta.idmoneda')
                ->join('tipocomprobante','tipocomprobante.id','venta.idtipocomprobante')
                ->join('tienda','tienda.id','venta.idtienda')
                ->Where($where)
                ->select(
                    'venta.*',
                    'tienda.nombre as tiendanombre',
                    'usuariovendedor.nombre as nombreusuariovendedor',
                    'usuariocajero.nombre as nombreusuariocajero',
                    'usuariocliente.identificacion as identificacioncliente',
                    DB::raw('IF(usuariocliente.idtipopersona=1,
                    CONCAT(usuariocliente.apellidos,", ",usuariocliente.nombre),
                    CONCAT(usuariocliente.nombre)) as cliente'),
                    'formapago.nombre as nombreFormapago',
                    'moneda.nombre as monedanombre',
                    'tipocomprobante.nombre as tipocomprobantenombre'
                )
                ->orderBy('venta.fechaconfirmacion','desc')
                ->paginate(10);
            $agencia = DB::table('agencia')->get();
            $comprobante = DB::table('tipocomprobante')->get();
            $tipopersonas = DB::table('tipopersona')->get();
            $cliente = DB::table('users')->get();
            $cajero = DB::table('users')->get();
            $formapago = DB::table('formapago')->get();
            $vendedor = DB::table('users')->where('idestado',1)->get();
            $tiendas = DB::table('tienda')->get();
            return view('layouts/backoffice/reporteventa/index',[
                    'venta' => $venta,
                    'vendedor' => $vendedor,
                    'formapago' => $formapago,
                    'cliente' => $cliente,
                    'cajero' => $cajero,
                    'agencia' => $agencia,
                    'comprobante' => $comprobante,
                    'tipopersonas' => $tipopersonas,
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
