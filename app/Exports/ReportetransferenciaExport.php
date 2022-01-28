<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportetransferenciaExport implements FromView, ShouldAutoSize
{
    public function __construct($productotransferencia, $inicio, $fin, $titulo)
    {
        $this->titulo                 = $titulo;
        $this->inicio                 = $inicio;
        $this->productotransferencia  = $productotransferencia;
        $this->fin                    = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportetransferencia/tablaexcel',[
          
            'titulo'                => $this->titulo,
            'inicio'                => $this->inicio,
            'productotransferencia' => $this->productotransferencia,
            'fin'                   => $this->fin
          
        ]);
    }
}