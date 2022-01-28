<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionResumendiarioExport implements FromView, ShouldAutoSize
{
    public function __construct($facturacionresumendiario, $inicio, $fin, $titulo)
    {
        $this->facturacionresumendiario = $facturacionresumendiario;
        $this->titulo                   = $titulo;
        $this->inicio                   = $inicio;
        $this->fin                      = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportefacturacionresumendiario/tablaexcel',[
          
            'facturacionresumendiario'  => $this->facturacionresumendiario,
            'titulo'                    => $this->titulo,
            'inicio'                    => $this->inicio,
            'fin'                       => $this->fin
          
        ]);
    }
}