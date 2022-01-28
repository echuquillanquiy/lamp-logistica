<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecobranzacreditoExport implements FromView, ShouldAutoSize
{
    public function __construct($cobranzacredito, $inicio, $fin, $titulo)
    {
        $this->cobranzacredito  = $cobranzacredito;
        $this->titulo           = $titulo;
        $this->inicio           = $inicio;
        $this->fin              = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportecobranzacredito/tablaexcel',[
          
            'cobranzacredito' => $this->cobranzacredito,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin
          
        ]);
    }
}