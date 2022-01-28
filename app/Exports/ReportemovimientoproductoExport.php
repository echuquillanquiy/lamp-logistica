<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportemovimientoproductoExport implements FromView, ShouldAutoSize
{
    public function __construct($movimientoproducto, $inicio, $fin, $titulo)
    {
        $this->movimientoproducto = $movimientoproducto;
        $this->titulo             = $titulo;
        $this->inicio             = $inicio;
        $this->fin                = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportemovimientoproducto/tablaexcel',[
          
            'movimientoproducto'  => $this->movimientoproducto,
            'titulo'              => $this->titulo,
            'inicio'              => $this->inicio,
            'fin'                 => $this->fin
          
        ]);
    }
}