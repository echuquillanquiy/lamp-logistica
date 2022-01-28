<table class="table table-bordered table-hover table-striped" style="width:100%">
    <thead class="thead-dark">
        <tr>
            <th width="200px">Tienda</th>
            <th>Fecha de Emisión</th>
            <th>Responsable</th>
            <th>Afectado</th>
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
      @foreach($facturacionnotacreditos as $value)
          <?php $montototal = DB::table('ventadetalle')->where('idventa',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)')); ?>
                <?php 
                  $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionnotacredito',$value->id)
                      ->limit(1)
                      ->first(); 
                ?>
          <tr>
            <td>{{ $value->tiendanombre }}</td>
            <td>{{ $value->notacredito_fechaemision }}</td>
            <td>{{ $value->nombreresponsable }}</td>
            <td>{{ $value->facturacionboletafactura_serie }}-{{ $value->facturacionboletafactura_correlativo }}</td>
            <td>{{ $value->notacredito_serie }}</td>
            <td>{{ $value->notacredito_correlativo }}</td>
            <td>{{ $value->cliente_numerodocumento }}</td>
            <td>{{ $value->cliente_razonsocial }}</td>
            <td>{{ $value->monedanombre }}</td>
            <td>{{ $value->notacredito_valorventa }}</td>
            <td>{{ $value->notacredito_totalimpuestos }}</td>
            <td>{{ $value->notacredito_montoimpuestoventa }}</td>
            <td>
             
                    @if($facturacioncomunicacionbajadetalle!='')
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> C. Baja (Anulado)</span></div>
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