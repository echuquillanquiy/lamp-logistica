<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteventaproductoExport implements FromView, ShouldAutoSize
{
    public function __construct($ventaproducto, $inicio, $fin, $titulo)
    {
        $this->ventaproducto  = $ventaproducto;
        $this->titulo         = $titulo;
        $this->inicio         = $inicio;
        $this->fin            = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reporteventaproducto/tablaexcel',[
          
            'ventaproducto' => $this->ventaproducto,
            'titulo'        => $this->titulo,
            'inicio'        => $this->inicio,
            'fin'           => $this->fin
          
        ]);
    }
}