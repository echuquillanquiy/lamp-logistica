<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="15">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="14">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="14">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cod. Venta</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Vendedor</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cajero</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cod. Producto</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Producto</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Und. Medida</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Forma de pago</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">P. Unitario</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cant.</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php $total = 0; ?>
    @foreach($ventaproducto as $value)
    <?php $total += $value->preciototal; ?>
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->nombretienda }}</td>
      <td style="border: 1px solid black;">{{ $value->fechaventa }}</td>
      <td style="border: 1px solid black;">{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">{{ $value->nombrevendedor }}</td>
      <td style="border: 1px solid black;">{{ $value->nombrecajero }}</td>
      <td style="border: 1px solid black;">{{ $value->identificacioncliente }} - {{ $value->nombrecliente }}</td>
      <td style="border: 1px solid black;">{{ str_pad($value->codigoimpresionproducto, 6, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreproducto }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreunidadmedida }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreformapago }}</td>
      <td style="border: 1px solid black;">{{ $value->monedanombre }}</td>
      <td style="border: 1px solid black;">{{ $value->preciounitario }}</td>
      <td style="border: 1px solid black;">{{ $value->cantidad }}</td>
      <td style="border: 1px solid black;">{{ $value->preciototal }}</td>
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="14">
        Total:
      </td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $total }}</td>
    </tr>
  </tbody>
</table>