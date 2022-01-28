<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionNotacreditoExport implements FromView, ShouldAutoSize
{
    public function __construct($facturacionnotacreditos, $inicio, $fin, $tipocomprobante, $titulo, $tipoexcel = 'excel')
    {
        $this->facturacionnotacreditos = $facturacionnotacreditos;
        $this->tipocomprobante = $tipocomprobante;
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->titulo = $titulo;
        $this->tipoexcel = $tipoexcel;
    }
  
    public function view(): View
    {
      if ($this->tipoexcel == 'excel') {
        return view('layouts/backoffice/reportefacturacionnotacredito/tablaexcel',[
            'facturacionnotacreditos' => $this->facturacionnotacreditos,
            'tipocomprobante' => $this->tipocomprobante,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
          'titulo' => $this->titulo
        ]);
      }else if ($this->tipoexcel == 'excelsunat') {
        return view('layouts/backoffice/reportefacturacionnotacredito/tableexcelsunat',[
            'facturacionnotacreditos' => $this->facturacionnotacreditos,
            'tipocomprobante' => $this->tipocomprobante,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
          'titulo' => $this->titulo
        ]);
      }
        
    }
}

?>