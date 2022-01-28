<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="10">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="9">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="9">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de registro</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Compra</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Proveedor</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo de Pago - Descripción</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php $sumtotal = 0; ?>
    @foreach($devolucioncompra as $value)
    <?php 
      $total = DB::table('compradevoluciondetalle')
              ->where('idcompradevolucion',$value->id)
              ->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
      $sumtotal += $total;
    ?>
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->tiendanombre}}</td>
      <td style="border: 1px solid black;">{{ $value->fecharegistro }}</td>
      <td style="border: 1px solid black;">{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">{{ $value->responsable }}</td>
      <td style="border: 1px solid black;">{{ $value->proveedor }}</td>
      <td style="border: 1px solid black;"><?php echo tipopago_detalle('compradevolucion', $value->id)[ 'detalle' ] ?></td>
      <td style="border: 1px solid black;">{{ $value->monedacodigo }}</td>
      <td style="border: 1px solid black;">{{ number_format($total, 2, '.', '') }}</td>
      <td style="border: 1px solid black;">
        @if($value->idestado==1)
            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pedido (Orden de compra)</span></div>
        @elseif($value->idestado==2)
            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Compra</span></div>
        @elseif($value->idestado==3)
            <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>   
        @endif
      </td>
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="8">
        Total:
      </td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $sumtotal }}</td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;"></td>
    </tr>
  </tbody>
</table>