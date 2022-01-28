<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="100px">Tienda</th>
      <th>Fecha de Emisión</th>
      <th>Serie Correlativo</th>
      <th>Cajero</th>
      <th>Proveedor</th>
      <th>Cod. Producto</th>
      <th>Producto</th>
      <th>Forma de pago</th>
      <th>Moneda</th>
      <th>P. Unitario</th>
      <th>Cantidad</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    @foreach($compraproducto as $value)
    <tr>
      <td>{{ $value->nombretienda }}</td>
      <td>{{ $value->fechaemisioncompra }}</td>
      <td>{{ $value->seriecorrelativocompra }}</td>
      <td>{{ $value->nombreresponsable }}</td>
      <td>{{ $value->identificacionproveedor }} - {{ $value->nombreproveedor }}</td>
      <td>{{ str_pad($value->codigoproducto, 6, "0", STR_PAD_LEFT) }}</td>
      <td>{{ $value->nombreproducto }}</td>
      <td>{{ $value->nombreformapago }}</td>        
      <td>{{ $value->codigomoneda }}</td>        
      <td>{{ $value->preciounitario }}</td>
      <td>{{ $value->cantidad }}</td>
      <td>{{ $value->preciototal }}</td>
    </tr>
    @endforeach
  </tbody>
</table>