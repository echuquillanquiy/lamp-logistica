<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportetipopagoExport implements FromView, ShouldAutoSize
{
    public function __construct($tipopagodetalle, $titulo)
    {
        $this->tipopagodetalle  = $tipopagodetalle;
        $this->titulo           = $titulo;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportetipopago/tablaexcel',[
          
            'tipopagodetalle' => $this->tipopagodetalle,
            'titulo'          => $this->titulo
          
        ]);
    }
}