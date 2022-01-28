<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportemovimientoExport implements FromView, ShouldAutoSize
{
    public function __construct($movimiento, $inicio, $fin, $titulo)
    {
        $this->movimiento       = $movimiento;
        $this->titulo           = $titulo;
        $this->inicio           = $inicio;
        $this->fin              = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportemovimiento/tablaexcel',[
          
            'movimiento'      => $this->movimiento,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin
          
        ]);
    }
}