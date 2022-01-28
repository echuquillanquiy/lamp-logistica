<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th>Tienda</th>
      <th>Fecha de Emisión</th>
      <th>Fecha de Traslado</th>
      <th>Emisor</th>
      <th>Serie</th>
      <th>Correlativo</th>
      <th>Cliente Destinatario</th>
      <th>Motivo</th>         
    </tr>
  </thead>
  <tbody>
    @foreach($facturacionguiaremision as $value)
    <tr>
      <td>{{ $value->tiendanombre }}</td>
      <td>{{ $value->despacho_fechaemision }}</td>
      <td>{{ $value->envio_fechatraslado }}</td>
      <td>{{ $value->emisor_nombrecomercial }}</td>
      <td>{{ $value->guiaremision_serie }}</td>
      <td>{{ $value->guiaremision_correlativo }}</td>
      <td>{{ $value->despacho_destinatario_razonsocial }}</td>
      <td>{{ $value->envio_descripciontraslado }}</td>
    </tr>
    @endforeach
  </tbody>
</table>