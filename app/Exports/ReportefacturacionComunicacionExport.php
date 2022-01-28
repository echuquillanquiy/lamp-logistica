<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionComunicacionExport implements FromView, ShouldAutoSize
{
    public function __construct($comunicacionbaja, $inicio, $fin, $comprobante, $titulo)
    {
        $this->comunicacionbaja = $comunicacionbaja;
        $this->comprobante      = $comprobante;
        $this->titulo           = $titulo;
        $this->inicio           = $inicio;
        $this->fin              = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportefacturacioncomunicacion/tablaexcel',[
          
            'comunicacionbaja'  => $this->comunicacionbaja,
            'comprobante'       => $this->comprobante,
            'titulo'            => $this->titulo,
            'inicio'            => $this->inicio,
            'fin'               => $this->fin
          
        ]);
    }
}