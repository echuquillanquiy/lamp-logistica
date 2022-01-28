<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportepagoletraExport implements FromView, ShouldAutoSize
{
    public function __construct($pagoletra, $inicio, $fin, $titulo)
    {
        $this->pagoletra  = $pagoletra;
        $this->titulo     = $titulo;
        $this->inicio     = $inicio;
        $this->fin        = $fin;
    }

    public function view(): View
    {
        return view('layouts/backoffice/reportepagoletra/tablaexcel',[
          
            'pagoletra' => $this->pagoletra,
            'titulo'    => $this->titulo,
            'inicio'    => $this->inicio,
            'fin'       => $this->fin
          
        ]);
    }
}