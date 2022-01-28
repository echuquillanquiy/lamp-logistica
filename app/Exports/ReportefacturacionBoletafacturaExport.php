<?php 

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportefacturacionBoletafacturaExport implements FromView, ShouldAutoSize
{
    public function __construct($facturacionboletafacturas, $inicio, $fin, $tipocomprobante, $titulo, $tipoexcel)
    {
        $this->facturacionboletafacturas = $facturacionboletafacturas;
        $this->tipocomprobante = $tipocomprobante;
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->titulo = $titulo;
        $this->tipoexcel = $tipoexcel;
    }
  
    public function view(): View
    {
      if ($this->tipoexcel == 'excel') {
        return view('layouts/backoffice/reportefacturacionboletafactura/tablaexcel',[
            'facturacionboletafacturas' => $this->facturacionboletafacturas,
            'tipocomprobante' => $this->tipocomprobante,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'titulo' => $this->titulo
        ]);
      }else if ($this->tipoexcel == 'excelsunat') {
        return view('layouts/backoffice/reportefacturacionboletafactura/tableexcelsunat',[
            'facturacionboletafacturas' => $this->facturacionboletafacturas,
            'tipocomprobante' => $this->tipocomprobante,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'titulo' => $this->titulo
        ]);
      }
        
    }
}

?>