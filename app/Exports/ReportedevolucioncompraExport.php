<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportedevolucioncompraExport implements FromView, ShouldAutoSize
{
    public function __construct($devolucioncompra, $inicio, $fin, $titulo)
    {
        $this->devolucioncompra = $devolucioncompra;
        $this->titulo           = $titulo;
        $this->inicio           = $inicio;
        $this->fin              = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportedevolucioncompra/tablaexcel',[
          
            'devolucioncompra'  => $this->devolucioncompra,
            'titulo'            => $this->titulo,
            'inicio'            => $this->inicio,
            'fin'               => $this->fin
          
        ]);
    }
}