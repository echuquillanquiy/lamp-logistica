<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Mail;
use PDF;
use DB;

use App\Exports\ReportetipopagoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportetipopagoController extends Controller
{
  public function index(Request $request) 
  {
      $request->user()->authorizeRoles( $request->path() );
    
    $where    = [];
//     $where[]  = ['venta.idtienda', usersmaster()->idtienda];
    
    if($request->input('modulo') == '1'){
        $where[] = ['tipopagodetalle.idventa', '<>', 0];
    }
    
    if($request->input('modulo') == '2'){
        $where[] = ['tipopagodetalle.idnotadevolucion', '<>', 0];      
    }
    
    if($request->input('tipopago') != ''){
        $where[] = ['tipopagodetalle.idtipopago', $request->input('tipopago')];
    }

    if($request->input('tipo') == 'excel'){

      $tipopagodetalle = DB::table('tipopagodetalle')
        ->join('tipopago', 'tipopago.id', 'tipopagodetalle.idtipopago')
        ->leftjoin('venta', 'venta.id', 'tipopagodetalle.idventa')
        ->leftjoin('users as cliente', 'cliente.id', 'venta.idusuariocliente')
        ->leftjoin('moneda as ventamoneda', 'ventamoneda.id', 'venta.idmoneda')
        ->leftjoin('notadevolucion', 'notadevolucion.id', 'tipopagodetalle.idnotadevolucion')
        ->leftjoin('users as responsable', 'responsable.id', 'notadevolucion.idusuarioresponsable')
        ->leftjoin('moneda as notamoneda', 'notamoneda.id', 'notadevolucion.idmoneda')
        ->leftjoin('banco as deposito', 'deposito.id', 'tipopagodetalle.deposito_banco')
        ->leftjoin('banco as cheque', 'cheque.id', 'tipopagodetalle.cheque_banco')
        ->leftjoin('users as saldo', 'saldo.id', 'tipopagodetalle.iduserssaldo')
        ->where($where)
        ->select(
          'tipopagodetalle.*',
          'tipopago.nombre as tipopagonombre',
          'deposito.nombre as bancodeposito',
          'cheque.nombre as bancocheque',
          'ventamoneda.nombre as monedanombreventa',
          'notamoneda.nombre as monedanombrenota',
          DB::raw(
            'IF(cliente.idtipopersona = 1,
            CONCAT(cliente.identificacion," - ",cliente.apellidos,", ",cliente.nombre),
            CONCAT(cliente.identificacion," - ",cliente.nombre)) as ventacliente'
          ),
          DB::raw(
            'IF(responsable.idtipopersona = 1,
            CONCAT(responsable.identificacion," - ",responsable.apellidos,", ",responsable.nombre),
            CONCAT(responsable.identificacion," - ",responsable.nombre)) as notadevolucionresponsable'
          ),
          'saldo.nombre as saldonombre',
          'saldo.apellidos as saldoapellidos'
        )
        ->orderBy('tipopagodetalle.id', 'desc')
        ->get();

      /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
      $titulo = 'Reporte por Tipo de Pago';

      return Excel::download(new 
                              ReportetipopagoExport($tipopagodetalle, $titulo),
                              $titulo.'.xls'
                            );
      /* FIN - Capturando los valores de filtrar para mostrar en el excel */

    }
    else{

      $tipopagodetalle = DB::table('tipopagodetalle')
        ->join('tipopago', 'tipopago.id', 'tipopagodetalle.idtipopago')
        ->leftjoin('venta', 'venta.id', 'tipopagodetalle.idventa')
        ->leftjoin('users as cliente', 'cliente.id', 'venta.idusuariocliente')
        ->leftjoin('moneda as ventamoneda', 'ventamoneda.id', 'venta.idmoneda')
        ->leftjoin('notadevolucion', 'notadevolucion.id', 'tipopagodetalle.idnotadevolucion')
        ->leftjoin('users as responsable', 'responsable.id', 'notadevolucion.idusuarioresponsable')
        ->leftjoin('moneda as notamoneda', 'notamoneda.id', 'notadevolucion.idmoneda')
        ->leftjoin('banco as deposito', 'deposito.id', 'tipopagodetalle.deposito_banco')
        ->leftjoin('banco as cheque', 'cheque.id', 'tipopagodetalle.cheque_banco')
        ->leftjoin('users as saldo', 'saldo.id', 'tipopagodetalle.iduserssaldo')
        ->where($where)
        ->select(
          'tipopagodetalle.*',
          'tipopago.nombre as tipopagonombre',
          'deposito.nombre as bancodeposito',
          'cheque.nombre as bancocheque',
          'ventamoneda.nombre as monedanombreventa',
          'notamoneda.nombre as monedanombrenota',
          DB::raw(
            'IF(cliente.idtipopersona = 1,
            CONCAT(cliente.identificacion," - ",cliente.apellidos,", ",cliente.nombre),
            CONCAT(cliente.identificacion," - ",cliente.nombre)) as ventacliente'
          ),
          DB::raw(
            'IF(responsable.idtipopersona = 1,
            CONCAT(responsable.identificacion," - ",responsable.apellidos,", ",responsable.nombre),
            CONCAT(responsable.identificacion," - ",responsable.nombre)) as notadevolucionresponsable'
          ),
          'saldo.nombre as saldonombre',
          'saldo.apellidos as saldoapellidos'
        )
        ->orderBy('tipopagodetalle.id', 'desc')
        ->paginate(10);
      
      $tipopago = DB::table('tipopago')->get();
      $tienda   = DB::table('tienda')->get();
      
      return view('layouts/backoffice/reportetipopago/index',[
        'tipopagodetalle' => $tipopagodetalle,
        'tipopago'        => $tipopago,
        'tienda'          => $tienda
      ]);

    }
  }
}