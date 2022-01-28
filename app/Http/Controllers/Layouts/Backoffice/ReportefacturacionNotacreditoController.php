<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

use App\Exports\ReportefacturacionNotacreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportefacturacionNotacreditoController extends Controller
{
    public function index(Request $request) 
    { 
        $request->user()->authorizeRoles( $request->path() );

        $where = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];
      
        if($request->input('tipoCompbrobante')!= ''){
          $where[] = ['facturacionnotacredito.notacredito_tipodocafectado', $request->input('tipoCompbrobante')];
        }
      
        if($request->input('notacredito_numerodocumentoafectado')!= ''){
          $where[] = ['facturacionnotacredito.notacredito_numerodocumentoafectado',$request->input('notacredito_numerodocumentoafectado')];
        }
      
        if($request->input('notacredito_serie')!= ''){
           $where[] = ['facturacionnotacredito.notacredito_serie',$request->input('notacredito_serie')];
        }
        if($request->input('notacredito_correlativo')!= ''){
          $where[] = ['facturacionnotacredito.notacredito_correlativo',$request->input('notacredito_correlativo')];          
        }
      
        if($request->input('cliente_numerodocumento')!= ''){
          $where[] = ['facturacionnotacredito.cliente_numerodocumento',$request->input('cliente_numerodocumento')];
        }
      
        if($request->input('cliente_razonsocial')!= ''){
          $where[] = ['facturacionnotacredito.cliente_razonsocial','LIKE','%'.$request->input('cliente_razonsocial').'%'];
        }
      
        if($request->input('cliente') != ''){
          $where[] = ['cliente.id',$request->input('cliente')];
        }
      
        if($request->input('moneda') != ''){
          $where[] = ['moneda.nombre', $request->input('moneda')];
        }
      
        if($request->input('fechainicio')!=''){
          $where[] = ['facturacionnotacredito.notacredito_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
          $where[] = ['facturacionnotacredito.notacredito_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('tipo')=='excel'){
            $facturacionnotacreditos = DB::table('facturacionnotacredito')
                ->join('tienda','tienda.id','facturacionnotacredito.idtienda')
                ->join('moneda','moneda.codigo','facturacionnotacredito.notacredito_tipomoneda')
                ->join('users as cliente','cliente.identificacion','facturacionnotacredito.cliente_numerodocumento')
                ->join('users','users.id','facturacionnotacredito.idusuarioresponsable')
                ->leftJoin('facturacionboletafactura','facturacionboletafactura.id','facturacionnotacredito.idfacturacionboletafactura')
                ->where($where)
                ->select(
                    'facturacionnotacredito.*',
                    'users.nombre as nombreresponsable',
                    'facturacionboletafactura.venta_serie as facturacionboletafactura_serie',
                    'facturacionboletafactura.venta_correlativo as facturacionboletafactura_correlativo',
                    'tienda.nombre as tiendanombre',
                    'moneda.nombre as monedanombre'
                )
                ->orderBy('facturacionnotacredito.id','desc')
                ->get();
            
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio           = $request->input('fechainicio');
            $fin              = $request->input('fechafin');
            $tipocomprobante  = $request->input('tipoCompbrobante');
            $titulo           = 'Reporte de Notas de Crédito';
            $fecha            = '';

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

            return Excel::download(new ReportefacturacionNotacreditoExport($facturacionnotacreditos, $inicio, $fin, $tipocomprobante, $titulo),
                                   $titulo.' '.$fecha.'.xls');
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
//             return Excel::download(new ReportefacturacionNotacreditoExport($facturacionnotacreditos),'Reporte de Notas de Crédito.xlsx');
        }else if ($request->input('tipo') == 'excelsunat'){
          $facturacionnotacreditos = DB::table('facturacionnotacredito')
                ->join('tienda','tienda.id','facturacionnotacredito.idtienda')
                ->join('moneda','moneda.codigo','facturacionnotacredito.notacredito_tipomoneda')
                ->join('users as cliente','cliente.identificacion','facturacionnotacredito.cliente_numerodocumento')
                ->join('users','users.id','facturacionnotacredito.idusuarioresponsable')
                ->leftJoin('facturacionboletafactura','facturacionboletafactura.id','facturacionnotacredito.idfacturacionboletafactura')
                ->where($where)
                ->select(
                    'facturacionnotacredito.*',
                    'users.nombre as nombreresponsable',
                    'facturacionboletafactura.venta_fechaemision as facturacionboletafacturaventa_fechaemision',
                    'facturacionboletafactura.venta_serie as facturacionboletafactura_serie',
                    'facturacionboletafactura.venta_tipodocumento as facturacionboletafactura_tipodocumento',
                    'facturacionboletafactura.venta_correlativo as facturacionboletafactura_correlativo',
                    'facturacionboletafactura.venta_montoigv as facturacionboletafactura_venta_montoigv',
                    'facturacionboletafactura.venta_montoimpuestoventa as facturacionboletafactura_venta_montoimpuestoventa',
                    'tienda.nombre as tiendanombre',
                    'moneda.nombre as monedanombre'
                )
                ->orderBy('facturacionnotacredito.id','desc')
                ->get();
          
           /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio           = $request->input('fechainicio');
            $fin              = $request->input('fechafin');
            $tipocomprobante  = $request->input('tipoCompbrobante');
            $titulo           = 'Reporte de Notas de Crédito';
            $fecha            = '';

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

            return Excel::download(new ReportefacturacionNotacreditoExport($facturacionnotacreditos, $inicio, $fin, $tipocomprobante, $titulo, 'excelsunat'),
                                   $titulo.' '.$fecha.'.xls');
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
        }else{
            $facturacionnotacreditos = DB::table('facturacionnotacredito')
                ->join('tienda','tienda.id','facturacionnotacredito.idtienda')
                ->join('moneda','moneda.codigo','facturacionnotacredito.notacredito_tipomoneda')
                ->join('users as cliente','cliente.identificacion','facturacionnotacredito.cliente_numerodocumento')
                ->join('users','users.id','facturacionnotacredito.idusuarioresponsable')
                ->leftJoin('facturacionboletafactura','facturacionboletafactura.id','facturacionnotacredito.idfacturacionboletafactura')
                ->where($where)
                ->select(
                    'facturacionnotacredito.*',
                    'users.nombre as nombreresponsable',
                    'facturacionboletafactura.venta_serie as facturacionboletafactura_serie',
                    'facturacionboletafactura.venta_correlativo as facturacionboletafactura_correlativo',
                    'tienda.nombre as tiendanombre',
                    'moneda.nombre as monedanombre'
                )
                ->orderBy('facturacionnotacredito.id','desc')
                ->paginate(10);
          
            $tiendas = DB::table('tienda')->get();
            $monedas = DB::table('moneda')->get();
//             dd($facturacionnotacreditos);
            return view('layouts/backoffice/reportefacturacionnotacredito/index',[
               'facturacionnotacreditos' => $facturacionnotacreditos,
               'tiendas' => $tiendas,
               'monedas' => $monedas
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
                        DB::raw('IF(users.idtipopersona = 1,
                        CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                        CONCAT(users.identificacion," - ",users.nombre)) as text')
                        )
                ->get();
            return $usuarios;
        }
        elseif($id == 'show-seleccionarcliente'){
            $usuario = DB::table('users')
                ->where('users.id',$request->input('idcliente'))
                ->select(
                        'users.*',
                        DB::raw('IF(users.idtipopersona = 1,
                        CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                        CONCAT(users.identificacion," - ",users.nombre)) as text')
                        )
                ->first();          
            return [ 'cliente' => $usuario ];
        }
    }
}
