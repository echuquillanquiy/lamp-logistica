<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

use App\Exports\ReportecajaExport;
use Maatwebsite\Excel\Facades\Excel;



class ReportecajaController extends Controller
{
  
    public function index(Request $request)
    {
        $request->user()->authorizeRoles( $request->path() );

        $where    = [];
        $where[]  = ['tienda.id',usersmaster()->idtienda];

        if($request->input('tipo') == 'excel'){
          
            $caja = DB::table('caja')
                    ->join('tienda', 'tienda.id', 'caja.idtienda')
                    ->where($where)
                    ->select(
                              'caja.*',
                              'tienda.nombre as tiendanombre'
                            )
                    ->orderBy('caja.nombre', 'desc')
                    ->get();
          
            $monedasoles    = DB::table('moneda')->whereId(1)->first();
            $monedadolares  = DB::table('moneda')->whereId(2)->first();
            $titulo         = 'Reporte de Cajas';

            return Excel::download(new 
                                    ReportecajaExport($caja, $monedasoles, $monedadolares, $titulo),
                                    $titulo.'.xls'
                                  );
          
        }
        else{
          
            $caja = DB::table('caja')
                    ->join('tienda', 'tienda.id', 'caja.idtienda')
                    ->where($where)
                    ->select(
                              'caja.*',
                              'tienda.nombre as tiendanombre'
                            )
                    ->orderBy('caja.nombre', 'desc')
                    ->paginate(10);
          
            $monedasoles   = DB::table('moneda')->whereId(1)->first();
            $monedadolares = DB::table('moneda')->whereId(2)->first();
            $tiendas       = DB::table('tienda')->get();
          
            return view('layouts/backoffice/reportecaja/index',[
              
                'monedadolares' => $monedadolares,
                'monedasoles'   => $monedasoles,
                'tiendas'       => $tiendas,
                'caja'          => $caja
              
            ]);
          
        }
    }

}
