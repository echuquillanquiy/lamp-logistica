<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

use App\Exports\ReportefacturacionBoletafacturaExport;
use App\Exports\ReportefacturacionBoletafacturaExport2;
use Maatwebsite\Excel\Facades\Excel;

class ReportefacturacionBoletafacturaController extends Controller
{
    public function index(Request $request) 
    {
//       dump($request->input());
        $request->user()->authorizeRoles( $request->path() );

        $where  = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];
      
        if($request->input('venta')!= ''){
          $where[] = ['venta.codigo',$request->input('venta')];
        }
      
        if($request->input('tipoCompbrobante')!= ''){
          $where[] = ['facturacionboletafactura.venta_tipodocumento', $request->input('tipoCompbrobante')];
        }
      
        if($request->input('serie')!= ''){
           $where[] = ['facturacionboletafactura.venta_serie','LIKE','%'.$request->input('serie').'%'];
        }
      
        if($request->input('correlativo')!= ''){
          $where[] = ['facturacionboletafactura.venta_correlativo',$request->input('correlativo')];
        }
      
        if($request->input('cliente')!=''){
            $where[] = ['cliente.id',$request->input('cliente')];
        }
      
        if($request->input('moneda')!= ''){
          $where[] = ['moneda.nombre', $request->input('moneda')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['facturacionboletafactura.venta_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['facturacionboletafactura.venta_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }
      
      $facturacionboletafacturas  = DB::table('facturacionboletafactura')
                ->join('moneda','moneda.codigo','facturacionboletafactura.venta_tipomoneda')
                ->join('users as cliente','cliente.identificacion','facturacionboletafactura.cliente_numerodocumento')
                ->join('tienda','tienda.id','facturacionboletafactura.idtienda')
                ->leftJoin('venta','venta.id','facturacionboletafactura.idventa')
                ->where($where)
                ->select(
                    'facturacionboletafactura.*',
                    'venta.codigo as ventacodigo',
                    'tienda.nombre as tiendanombre',
                    'moneda.nombre as monedanombre'
                )
                ->distinct()
                ->orderBy('facturacionboletafactura.id','desc');
      
        if($request->input('tipo')=='excel'){
            $facturacionboletafacturas  = $facturacionboletafacturas->get();

            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio           = $request->input('fechainicio');
            $fin              = $request->input('fechafin');
            $tipocomprobante  = $request->input('tipoCompbrobante');
            $titulo           = 'Reporte de Boletas y Facturas';
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

            return Excel::download(new ReportefacturacionBoletafacturaExport($facturacionboletafacturas, $inicio, $fin, $tipocomprobante, $titulo, 'excel'),
                                   $titulo.' '.$fecha.'.xls');
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
        }else if ($request->input('tipo') == 'excelsunat'){
          
            $facturacionboletafacturas  = $facturacionboletafacturas->get();
            $inicio           = $request->input('fechainicio');
            $fin              = $request->input('fechafin');
            $tipocomprobante  = $request->input('tipoCompbrobante');
            $titulo           = 'Reporte de Boletas y Facturas';
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
          
           return Excel::download(new ReportefacturacionBoletafacturaExport($facturacionboletafacturas, $inicio, $fin, $tipocomprobante, $titulo, 'excelsunat'),
                                   $titulo.' '.$fecha.'.xls');
        }else {            
            $facturacionboletafacturas = $facturacionboletafacturas->paginate(10);
          
            $tiendas= DB::table('tienda')->get();
            $monedas= DB::table('moneda')->get();
          
            return view('layouts/backoffice/reportefacturacionboletafactura/index',[
               'facturacionboletafacturas'  => $facturacionboletafacturas,
               'tiendas'                    => $tiendas,
               'monedas'                    => $monedas
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
