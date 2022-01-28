<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Mail;
use PDF;
use DB;

use App\Exports\ReportefacturacionComunicacionExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportefacturacionComunicacionController extends Controller
{
  public function index(Request $request)
  {
      $request->user()->authorizeRoles( $request->path() );
    
    $where    = [];
//     $where[]  = ['tienda.id', usersmaster()->idtienda];

    if($request->input('comprobante') != ''){
        $where[] = ['detalle.tipodocumento', $request->input('comprobante')];
    }
    
    if($request->input('fechainicio') != ''){
        $where[] = ['facturacioncomunicacionbaja.comunicacionbaja_fechageneracion', '>=', $request->input('fechainicio').'00:00:00'];
    }

    if($request->input('fechafin') != ''){
        $where[] = ['facturacioncomunicacionbaja.comunicacionbaja_fechageneracion', '<=', $request->input('fechafin').'23:59:59'];
    }

    if($request->input('tipo') == 'excel'){
      
      $comunicacionbaja = DB::table('facturacioncomunicacionbaja')
        ->join('users as responsable', 'responsable.id', 'facturacioncomunicacionbaja.idusuarioresponsable')
        ->join('facturacioncomunicacionbajadetalle as detalle', 'detalle.idfacturacioncomunicacionbaja', 'facturacioncomunicacionbaja.id')
        ->leftJoin('facturacionboletafactura', 'facturacionboletafactura.id', 'detalle.idfacturacionboletafactura')
        ->leftJoin('facturacionnotacredito', 'facturacionnotacredito.id', 'detalle.idfacturacionnotacredito')
        ->where($where)
        ->select(
          'facturacioncomunicacionbaja.*',
          'responsable.apellidos as responsableapellidos',
          'responsable.nombre as responsablenombre',
          'detalle.descripcionmotivobaja as motivo',
          'detalle.tipodocumento as tipodocumento',
          'detalle.correlativo as correlativo',
          'detalle.serie as serie',
          'facturacionboletafactura.cliente_numerodocumento as factbol_cliente_numerodocumento',
          'facturacionboletafactura.cliente_razonsocial as factbol_cliente_razonsocial',
          'facturacionnotacredito.cliente_numerodocumento as notacred_cliente_numerodocumento',
          'facturacionnotacredito.cliente_razonsocial as notacred_cliente_razonsocial'
        )
        ->orderBy('facturacioncomunicacionbaja.id', 'desc')
        ->get();

      /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
      $comprobante  = $request->input('comprobante');
      $inicio       = $request->input('fechainicio');
      $fin          = $request->input('fechafin');
      $titulo       = 'Reporte de Comunicación de Baja';
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
                              ReportefacturacionComunicacionExport($comunicacionbaja, $inicio, $fin, $comprobante, $titulo),
                              $titulo.' '.$fecha.'.xls'
                            );
      /* FIN - Capturando los valores de filtrar para mostrar en el excel */

    }
    else{

      $comunicacionbaja = DB::table('facturacioncomunicacionbaja')
        ->join('users as responsable', 'responsable.id', 'facturacioncomunicacionbaja.idusuarioresponsable')
        ->join('facturacioncomunicacionbajadetalle as detalle', 'detalle.idfacturacioncomunicacionbaja', 'facturacioncomunicacionbaja.id')
        ->leftJoin('facturacionboletafactura', 'facturacionboletafactura.id', 'detalle.idfacturacionboletafactura')
        ->leftJoin('facturacionnotacredito', 'facturacionnotacredito.id', 'detalle.idfacturacionnotacredito')
        ->where($where)
        ->select(
          'facturacioncomunicacionbaja.*',
          'responsable.apellidos as responsableapellidos',
          'responsable.nombre as responsablenombre',
          'detalle.descripcionmotivobaja as motivo',
          'detalle.tipodocumento as tipodocumento',
          'detalle.correlativo as correlativo',
          'detalle.serie as serie',
          'facturacionboletafactura.cliente_numerodocumento as factbol_cliente_numerodocumento',
          'facturacionboletafactura.cliente_razonsocial as factbol_cliente_razonsocial',
          'facturacionnotacredito.cliente_numerodocumento as notacred_cliente_numerodocumento',
          'facturacionnotacredito.cliente_razonsocial as notacred_cliente_razonsocial'
        )
        ->orderBy('facturacioncomunicacionbaja.id', 'desc')
        ->paginate(10);
      
      $tienda = DB::table('tienda')->get();
//       dd($facturacioncomunicacionbaja);
      return view('layouts/backoffice/reportefacturacioncomunicacion/index',[
        'comunicacionbaja' => $comunicacionbaja,
        'tienda' => $tienda
      ]);

    }
  }
}