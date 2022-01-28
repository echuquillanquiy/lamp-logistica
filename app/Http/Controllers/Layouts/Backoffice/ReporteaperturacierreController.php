<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use PDF;

use App\Exports\ReporteaperturacierreExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReporteaperturacierreController extends Controller
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
      
        if($request->input('responsable') != ''){
          $where[] = ['responsable.id',$request->input('responsable')];
        }
      
        if($request->input('recepcion') != ''){
          $where[] = ['recepcion.id',$request->input('recepcion')];
        }
      
        //Inicio- Busqueda por estado y fecha
        if($request->input('estado') != ''){
            $where[] = ['aperturacierre.idestado',$request->input('estado')];

            if($request->input('fechainicio') != '' || $request->input('fechafin')!=''){
              
                if($request->input('estado') == 1 || $request->input('estado') == 2){
                  
                  //Apertura En Proceso - Apertura Pendiente
                  if($request->input('fechainicio') != ''){
                    $where[] = ['aperturacierre.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
                  }      
                  if($request->input('fechafin') != ''){
                    $where[] = ['aperturacierre.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
                  }
                  
                }
                elseif($request->input('estado') == 3){
                  
                  //Aperturado
                  if($request->input('fechainicio') != ''){
                    $where[] = ['aperturacierre.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                  }
                  if($request->input('fechafin') != ''){
                    $where[] = ['aperturacierre.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                  }
                  
                }
                elseif($request->input('estado') == 4){
                  
                  //Cierre Pendiente
                  if($request->input('fechainicio') != ''){
                    $where[] = ['aperturacierre.fechacierre','>=',$request->input('fechainicio').' 00:00:00'];
                  }      
                  if($request->input('fechafin') != ''){
                    $where[] = ['aperturacierre.fechacierre','<=',$request->input('fechafin').' 23:59:59'];
                  }
                  
                }
                elseif($request->input('estado') == 5){
                  
                  //Caja Cerrada
                  if($request->input('fechainicio') != ''){
                      $where[] = ['aperturacierre.fechacierreconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
                  }      
                  if($request->input('fechafin') != ''){
                      $where[] = ['aperturacierre.fechacierreconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
                  }
                  
                }
              
            } 
        }
        else{
          
            if($request->input('estado') == '' && $request->input('fechainicio') != '' && $request->input('fechafin') != ''){
              $where[] = ['aperturacierre.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
              $where[] = ['aperturacierre.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
            }
            elseif($request->input('estado') == '' && $request->input('fechainicio') != ''){
              $where[] = ['aperturacierre.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
            }
            elseif($request->input('estado') == '' && $request->input('fechafin') != ''){
              $where[] = ['aperturacierre.fechacierreconfirmacion','<=',$request->input('fechafin').' 23:59:59'];          
            }
          
        }
        //Fin- Busqueda por estado y fecha
        
        if($request->input('tipo')=='excel'){
            $aperturacierre = DB::table('aperturacierre')
                ->join('users as responsable','responsable.id','aperturacierre.idusersresponsable')
                ->join('users as recepcion','recepcion.id','aperturacierre.idusersrecepcion')
                ->join('caja','caja.id','aperturacierre.idcaja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->where($where)
                ->select(
                    'aperturacierre.*',
                    'responsable.nombre as responsablenombre',
                    'responsable.apellidos as responsableapellidos',
                    'recepcion.nombre as recepcionnombre',
                    'recepcion.apellidos as recepcionapellidos',
                    'caja.nombre as cajanombre',
                    'tienda.id as idtienda',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('aperturacierre.id','desc')
                ->get();
            $usuarios = DB::table('users')
                ->where('users.idestado',1)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.nombre) as text')
                )
                ->get();
          
            $monedasoles = DB::table('moneda')->whereId(1)->first();
            $monedadolares = DB::table('moneda')->whereId(2)->first();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $estado = $request->input('estado');
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Apertura y Cierres';
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
                                    ReporteaperturacierreExport($aperturacierre, $usuarios, $monedasoles, $monedadolares, $estado, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
        }
        else{
            $aperturacierre = DB::table('aperturacierre')
                ->join('users as responsable','responsable.id','aperturacierre.idusersresponsable')
                ->join('users as recepcion','recepcion.id','aperturacierre.idusersrecepcion')
                ->join('caja','caja.id','aperturacierre.idcaja')
                ->join('tienda','tienda.id','caja.idtienda')
                ->where($where)
                ->select(
                    'aperturacierre.*',
                    'responsable.nombre as responsablenombre',
                    'responsable.apellidos as responsableapellidos',
                    'recepcion.nombre as recepcionnombre',
                    'recepcion.apellidos as recepcionapellidos',
                    'caja.nombre as cajanombre',
                    'tienda.id as idtienda',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('aperturacierre.id','desc')
                ->paginate(10);
          
            $tiendas= DB::table('tienda')->get();            
            $usuarios = DB::table('users')
                ->where('users.idestado',1)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.nombre) as text')
                )
                ->get();
            $monedasoles = DB::table('moneda')->whereId(1)->first();
            $monedadolares = DB::table('moneda')->whereId(2)->first();
            return view('layouts/backoffice/reporteaperturacierre/index',[
               'aperturacierre' => $aperturacierre,
               'monedasoles' => $monedasoles,
               'monedadolares' => $monedadolares,
               'tiendas' => $tiendas,
               'usuarios' => $usuarios
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
  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles( $request->path() );
      
        $aperturacierre = DB::table('aperturacierre')
            ->join('users as usersresponsable','usersresponsable.id','aperturacierre.idusersresponsable')
            ->join('users as usersrecepcion','usersrecepcion.id','aperturacierre.idusersrecepcion')
            ->join('caja','caja.id','aperturacierre.idcaja')
            ->join('tienda','tienda.id','caja.idtienda')
            ->where('aperturacierre.id',$id)
            ->select(
                'aperturacierre.*',
                'usersresponsable.nombre as responsablenombre',
                'usersresponsable.apellidos as responsableapellidos',
                'usersrecepcion.identificacion as usersrecepcionidentificacion',
                'usersrecepcion.nombre as recepcionnombre',
                'usersrecepcion.apellidos as recepcionapellidos',
                'caja.nombre as cajanombre',
                'tienda.id as idtienda',
                'tienda.nombre as tiendanombre'
            )
            ->first();

        if($request->input('view') == 'pdfdetalle') {
            return view('layouts/backoffice/reporteaperturacierre/pdfdetalle',[
                'aperturacierre' => $aperturacierre,
            ]);
        }elseif($request->input('view') == 'pdfdetalle-pdf') {
            $pdf = PDF::loadView('layouts/backoffice/reporteaperturacierre/pdfdetalle-pdf',[
                'aperturacierre' => $aperturacierre
            ]);
            return $pdf->stream();
        }
        
    }

}
