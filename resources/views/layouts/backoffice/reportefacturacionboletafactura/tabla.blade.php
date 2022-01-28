<?php $idtienda = usersmaster()->idtienda ?>
<table class="table table-bordered table-hover table-striped"  style="width:100%;">
    <thead class="thead-dark">
        <tr>
            <th>Tienda</th>
            <th>Fecha de Emisión</th>
            <th>Código Venta</th>
            <th>Comprobante</th>
            <th>Serie</th>
            <th>Correlativo</th>
            <th>DNI/RUC</th>
            <th>Cliente</th>
            <th>Moneda</th>
            <th>Base Imp.</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Estado</th>          
          </tr>
    </thead>
    <tbody>
      @foreach($facturacionboletafacturas as $value)
      <?php $montototal = DB::table('ventadetalle')->where('idventa',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)')); ?>
                <?php 
                  $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
                  $facturacionresumendetalle = DB::table('facturacionresumendetalle')
                      ->where('facturacionresumendetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
                ?>
      <tr>
        <td>{{ $value->tiendanombre }}</td>
        <td>{{ $value->venta_fechaemision }}</td>
        <td>{{ $value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):'---' }}</td>
        <td>
          @if($value->venta_tipodocumento == '03')
              BOLETA
          @elseif($value->venta_tipodocumento == '01')
              FACTURA
          @endif  
        </td>
        <td>{{ $value->venta_serie }}</td>  
        <td>{{ $value->venta_correlativo }}</td>
        <td>{{ $value->cliente_numerodocumento }}</td>
        <td>{{ $value->cliente_razonsocial }}</td>
        <td>{{ $value->monedanombre }}</td>
        <td>{{ $value->venta_valorventa }}</td>
        <td>{{ $value->venta_totalimpuestos }}</td>
        <td>{{ $value->venta_montoimpuestoventa }}</td>
        <td>
                    @if($facturacioncomunicacionbajadetalle!='')
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> C. Baja (Anulado)</span></div>
                    @elseif($facturacionresumendetalle!='')
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> R. Diario (Anulado)</span></div>
                    @else
                    @if($value->idestadofacturacion==0)
                        <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                    @elseif($value->idestadofacturacion==1)
                        <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Correcto</span></div> 
                    @elseif($value->idestadofacturacion==2)
                        <div class="td-badge"><span class="badge badge-pill"><i class="fa fa-times"></i> Observado</span></div>
                    @endif  
                    @endif 
        </td>
      </tr>
      @endforeach
    </tbody>
</table>