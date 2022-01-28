<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecompraExport implements FromView, ShouldAutoSize
{
    public function __construct($compra, $inicio, $fin, $comprobante, $titulo)
    {
        $this->comprobante  = $comprobante;
        $this->titulo       = $titulo;
        $this->inicio       = $inicio;
        $this->compra       = $compra;
        $this->fin          = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportecompra/tablaexcel',[
          
            'comprobante' => $this->comprobante,
            'titulo'      => $this->titulo,
            'inicio'      => $this->inicio,
            'compra'      => $this->compra,
            'fin'         => $this->fin
          
        ]);
    }
}