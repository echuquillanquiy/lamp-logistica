<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportedevolucionnotaExport implements FromView, ShouldAutoSize
{
    public function __construct($devolucionnota, $inicio, $fin, $titulo)
    {
        $this->devolucionnota = $devolucionnota;
        $this->titulo         = $titulo;
        $this->inicio         = $inicio;
        $this->fin            = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportedevolucionnota/tablaexcel',[
          
            'devolucionnota'  => $this->devolucionnota,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin
          
        ]);
    }
}