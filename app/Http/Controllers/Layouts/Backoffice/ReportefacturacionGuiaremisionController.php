<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;

use App\Exports\ReportefacturacionGuiaremisionExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportefacturacionGuiaremisionController extends Controller
{
    public function index(Request $request) 
    {
        $request->user()->authorizeRoles( $request->path() );

        $where = [];
        $where[] = ['tienda.id',usersmaster()->idtienda];
      
        if($request->input('venta')!= ''){
          $where[] = ['venta.codigo','LIKE','%'.$request->input('venta').'%'];
        }
      
        if($request->input('destinatario')!= ''){
          $where[] = ['destinatario.id','LIKE','%'.$request->input('destinatario').'%'];
        }
      
        if($request->input('serie')!= ''){
           $where[] = ['facturacionguiaremision.guiaremision_serie','LIKE','%'.$request->input('serie').'%'];
        }
      
        if($request->input('correlativo')!= ''){
          $where[] = ['facturacionguiaremision.guiaremision_correlativo','LIKE','%'.$request->input('correlativo').'%'];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['facturacionguiaremision.envio_fechatraslado','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['facturacionguiaremision.envio_fechatraslado','<=',$request->input('fechafin').' 23:59:59'];
        }

        if($request->input('tipo')=='excel'){
          
              $facturacionguiaremision = DB::table('facturacionguiaremision')
                  ->join('tienda','tienda.id','facturacionguiaremision.idtienda')
                  ->where($where)
                  ->select(
                      'facturacionguiaremision.*',
                      'tienda.nombre as tiendanombre'
                  )
                  ->orderBy('facturacionguiaremision.id','desc')
                  ->get();
          
              /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
              $inicio = $request->input('fechainicio');
              $fin    = $request->input('fechafin');
              $titulo = 'Reporte de Guías de Remisión';
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
                                      ReportefacturacionGuiaremisionExport($facturacionguiaremision, $inicio, $fin, $titulo),
                                      $titulo.' '.$fecha.'.xls'
                                    );
              /* FIN - Capturando los valores de filtrar para mostrar en el excel */
          
          }
          else{   
            
              $facturacionguiaremision = DB::table('facturacionguiaremision')
                  ->join('tienda','tienda.id','facturacionguiaremision.idtienda')
                  ->join('users','users.identificacion','facturacionguiaremision.despacho_destinatario_numerodocumento')
                  ->join('users as destinatario','destinatario.id','users.id')
                  ->where($where)
                  ->select(
                      'facturacionguiaremision.*',
                      'tienda.nombre as tiendanombre'
                  )
                  ->orderBy('facturacionguiaremision.id','desc')
                  ->paginate(10);
            
              $tiendas= DB::table('tienda')->get();
            
              return view('layouts/backoffice/reportefacturacionguiaremision/index',[
                 'facturacionguiaremision' => $facturacionguiaremision,
                 'tiendas' => $tiendas
              ]);  
            
          }
    }
  
    public function show(Request $request, $id) 
    {
        if($id == 'show-listarcliente'){
            $usuarios = DB::table('users')
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->select(
                    'users.id as id',
                    DB::raw('IF(users.idtipopersona=1,
                    CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                    CONCAT(users.identificacion," - ",users.nombre)) as text')
                )
                ->get();          
            return $usuarios;
        }elseif($id == 'show-seleccionarcliente'){
            $usuario = DB::table('users')
                ->where('users.id',$request->input('idcliente'))
                ->select(
                    'users.*'
                )
                ->first();
          
            return [ 
              'cliente' => $usuario
            ];
        }
    }
}
