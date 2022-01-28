<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecompraproductoExport implements FromView, ShouldAutoSize
{
    public function __construct($compraproducto, $inicio, $fin, $titulo)
    {
        $this->compraproducto = $compraproducto;
        $this->titulo         = $titulo;
        $this->inicio         = $inicio;
        $this->fin            = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportecompraproducto/tablaexcel',[
          
            'compraproducto'  => $this->compraproducto,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin
          
        ]);
    }
}