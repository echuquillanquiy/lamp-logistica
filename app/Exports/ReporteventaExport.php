<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteventaExport implements FromView, ShouldAutoSize
{
    public function __construct($venta, $inicio, $fin, $titulo)
    {
        $this->titulo = $titulo;
        $this->inicio = $inicio;
        $this->venta  = $venta;
        $this->fin    = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reporteventa/tablaexcel',[
          
            'titulo'  => $this->titulo,
            'inicio'  => $this->inicio,
            'venta'   => $this->venta,
            'fin'     => $this->fin
          
        ]);
    }
}