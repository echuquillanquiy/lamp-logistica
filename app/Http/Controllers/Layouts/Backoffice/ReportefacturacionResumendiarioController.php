<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Mail;
use PDF;
use DB;

use App\Exports\ReportefacturacionResumendiarioExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportefacturacionResumendiarioController extends Controller
{
  public function index(Request $request) 
  {
      $request->user()->authorizeRoles( $request->path() );
    
    $where    = [];
//     $where[]  = ['tienda.id', usersmaster()->idtienda];
    if($request->input('fechainicio') != ''){
        $where[] = ['facturacionresumen.resumen_fechageneracion', '>=', $request->input('fechainicio').'00:00:00'];
    }

    if($request->input('fechafin') != ''){
        $where[] = ['facturacionresumen.resumen_fechageneracion', '<=', $request->input('fechafin').'23:59:59'];
    }

    if($request->input('tipo') == 'excel'){
      
      $facturacionresumendiario = DB::table('facturacionresumen')
        ->where($where)
        ->select(
          'facturacionresumen.*'
        )
        ->orderBy('facturacionresumen.id', 'desc')
        ->get();

      /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
      $inicio = $request->input('fechainicio');
      $fin    = $request->input('fechafin');
      $titulo = 'Reporte de Resumen Diario';
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
                              ReportefacturacionResumendiarioExport($facturacionresumendiario, $inicio, $fin, $titulo),
                              $titulo.' '.$fecha.'.xls'
                            );
      /* FIN - Capturando los valores de filtrar para mostrar en el excel */

    }
    else{

      $facturacionresumendiario = DB::table('facturacionresumen')
        ->where($where)
        ->select(
          'facturacionresumen.*'
        )
        ->orderBy('facturacionresumen.id', 'desc')
        ->paginate(10);
      
      $tienda = DB::table('tienda')->get();
      
      return view('layouts/backoffice/reportefacturacionresumendiario/index',[
        'facturacionresumendiario' => $facturacionresumendiario,
        'tienda' => $tienda
      ]);

    }
  }
}
