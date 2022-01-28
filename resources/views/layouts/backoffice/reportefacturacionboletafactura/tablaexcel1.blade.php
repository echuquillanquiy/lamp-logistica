<?php $idtienda = usersmaster()->idtienda ?>
<table>
    <thead>
      <tr></tr>
      <tr>
        <th></th>
        <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="13">
          {{ $titulo }}
        </th>
      </tr>
      @if($tipocomprobante != '')
      <tr>
        <th></th>
        <th style="font-weight: 900;">Afectado:</th>
        <th style="font-weight: 900;" colspan="12">
          @if($tipocomprobante == '01')
            FACTURA
          @elseif($tipocomprobante == '03')
            BOLETA
          @endif
        </th>
      </tr>
      @endif
      @if($inicio != '')
      <tr>
        <th></th>
        <th style="font-weight: 900;">Fecha de Inicio:</th>
        <th style="font-weight: 900;" colspan="12">{{$inicio}}</th>
      </tr>
      @endif
      @if($fin != '')
      <tr>
        <th></th>
        <th style="font-weight: 900;">Fecha de Fin:</th>
        <th style="font-weight: 900;" colspan="12">{{$fin}}</th>
      </tr>
      @endif
      <tr></tr>
      <tr>            
        <th></th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Emisión</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código Venta</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Comprobante</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Serie</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Correlativo</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">DNI/RUC</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Base Imp.</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">IGV</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total</th>
        <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>          
      </tr>
    </thead>
    <tbody>
      <?php $totalBase = 0; $totalIGV = 0; $total = 0;?>
      @foreach($facturacionboletafacturas as $value)
      <?php 
        $montototal = DB::table('ventadetalle')->where('idventa',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
        $totalBase  += $value->venta_valorventa;
        $totalIGV   += $value->venta_totalimpuestos;
        $total      += $value->venta_montoimpuestoventa;
      ?>
                <?php 
                  $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
                ?>
      <tr>
        <td></td>
        <td style="border: 1px solid black;">{{ $value->tiendanombre }}</td>
        <td style="border: 1px solid black;">{{ $value->venta_fechaemision }}</td>
        <td style="border: 1px solid black;">{{ $value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):'---' }}</td>
        <td style="border: 1px solid black;">
          @if($value->venta_tipodocumento == '03')
              BOLETA
          @elseif($value->venta_tipodocumento == '01')
              FACTURA
          @endif  
        </td>
        <td style="border: 1px solid black;">{{ $value->venta_serie }}</td>  
        <td style="border: 1px solid black;">{{ $value->venta_correlativo }}</td>
        <td style="border: 1px solid black;">{{ $value->cliente_numerodocumento }}</td>
        <td style="border: 1px solid black;">{{ $value->cliente_razonsocial }}</td>
        <td style="border: 1px solid black;">{{ $value->monedanombre }}</td>
        <td style="border: 1px solid black;">{{ $value->venta_valorventa }}</td>
        <td style="border: 1px solid black;">{{ $value->venta_totalimpuestos }}</td>
        <td style="border: 1px solid black;">{{ $value->venta_montoimpuestoventa }}</td>
        <td style="border: 1px solid black;">
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
      <tr>
        <td></td>
        <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="9">
          Total:
        </td>
        <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $totalBase }}</td>
        <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $totalIGV }}</td>
        <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $total }}</td>
        <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;"></td>
      </tr>
    </tbody>
</table>