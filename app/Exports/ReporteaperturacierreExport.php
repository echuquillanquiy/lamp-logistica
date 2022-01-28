<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteaperturacierreExport implements FromView, ShouldAutoSize
{
    public function __construct($aperturacierre, $usuarios, $monedasoles, $monedadolares, $estado, $inicio, $fin, $titulo)
    {
        $this->aperturacierre = $aperturacierre;
        $this->monedadolares  = $monedadolares;
        $this->monedasoles    = $monedasoles;
        $this->usuarios       = $usuarios;
        $this->estado         = $estado;
        $this->titulo         = $titulo;
        $this->inicio         = $inicio;
        $this->fin            = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reporteaperturacierre/tablaexcel',[
          
            'aperturacierre'  => $this->aperturacierre,
            'monedadolares'   => $this->monedadolares,
            'monedasoles'     => $this->monedasoles,
            'usuarios'        => $this->usuarios,
            'estado'          => $this->estado,
            'titulo'          => $this->titulo,
            'inicio'          => $this->inicio,
            'fin'             => $this->fin
          
        ]);
    }
}