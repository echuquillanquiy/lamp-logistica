<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionGuiaremisionExport implements FromView, ShouldAutoSize
{
    public function __construct($facturacionguiaremision, $inicio, $fin, $titulo)
    {
        $this->facturacionguiaremision  = $facturacionguiaremision;
        $this->titulo                   = $titulo;
        $this->inicio                   = $inicio;
        $this->fin                      = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportefacturacionguiaremision/tablaexcel',[
          
            'facturacionguiaremision' => $this->facturacionguiaremision,
            'titulo'                  => $this->titulo,
            'inicio'                  => $this->inicio,
            'fin'                     => $this->fin
          
        ]);
    }
}