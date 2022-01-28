<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportepagocreditoExport implements FromView, ShouldAutoSize
{
    public function __construct($pagocredito, $inicio, $fin, $titulo)
    {
        $this->pagocredito  = $pagocredito;
        $this->titulo       = $titulo;
        $this->inicio       = $inicio;
        $this->fin          = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportepagocredito/tablaexcel',[
          
            'pagocredito' => $this->pagocredito,
            'titulo'      => $this->titulo,
            'inicio'      => $this->inicio,
            'fin'         => $this->fin
          
        ]);
    }
}