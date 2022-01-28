<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportecardexproductoExport implements FromView, ShouldAutoSize
{
    public function __construct($producto, $titulo)
    {
        $this->titulo   = $titulo;
        $this->producto = $producto;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportecardexproducto/tablaexcel',[
          
            'producto'  => $this->producto,
            'titulo'    => $this->titulo
          
        ]);
    }
}