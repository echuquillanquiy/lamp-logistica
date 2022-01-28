<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="100px">Tienda</th>
      <th>Fecha</th>
      <th>Cod. Venta</th>
      <th>Vendedor</th>
      <th>Cajero</th>
      <th>Cliente</th>
      <th>Cod. Producto</th>
      <th>Producto</th>
      <th>Und. Medida</th>
      <th>Forma de pago</th>
      <th>Moneda</th>
      <th>P. Unitario</th>
      <th>Cant.</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ventaproducto as $value)
    <tr>
      <td>{{ $value->nombretienda }}</td>
      <td>{{ $value->fechaventa }}</td>
      <td>{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
      <td>{{ $value->nombrevendedor }}</td>
      <td>{{ $value->nombrecajero }}</td>
      <td>{{ $value->identificacioncliente }} - {{ $value->nombrecliente }}</td>
      <td>{{ str_pad($value->codigoimpresionproducto, 6, "0", STR_PAD_LEFT) }}</td>
      <td>{{ $value->nombreproducto }}</td>
      <td>{{ $value->nombreunidadmedida }}</td>
      <td>{{ $value->nombreformapago }}</td>
      <td>{{ $value->monedanombre }}</td>
      <td>{{ $value->preciounitario }}</td>
      <td>{{ $value->cantidad }}</td>
      <td>{{ $value->preciototal }}</td>
    </tr>
    @endforeach
  </tbody>
</table>