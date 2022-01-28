<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecajaExport implements FromView, ShouldAutoSize
{
    public function __construct($caja, $monedasoles, $monedadolares, $titulo)
    {
        $this->monedadolares    = $monedadolares;
        $this->monedasoles      = $monedasoles;
        $this->titulo           = $titulo;
        $this->caja             = $caja;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportecaja/tablaexcel',[
          
            'monedadolares'   => $this->monedadolares,
            'monedasoles'     => $this->monedasoles,
            'titulo'          => $this->titulo,
            'caja'            => $this->caja
          
        ]);
    }
}