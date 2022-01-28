<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecobranzaletraExport implements FromView, ShouldAutoSize
{
    public function __construct($cobranzaletra, $inicio, $fin, $titulo)
    {
        $this->cobranzaletra  = $cobranzaletra;
        $this->titulo         = $titulo;
        $this->inicio         = $inicio;
        $this->fin            = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportecobranzaletra/tablaexcel',[
          
            'cobranzaletra' => $this->cobranzaletra,
            'titulo'        => $this->titulo,
            'inicio'        => $this->inicio,
            'fin'           => $this->fin
          
        ]);
    }
}