<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="100px">Tienda - Caja</th>
      <th>Fecha de registro</th>
      <th>Venta</th>
      <th>Código</th>
      <th>Responsable</th>
      <th>Tipo de Pago - Descripción</th>
      <th>Moneda</th>
      <th>Total</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($devolucionnota as $value)
    <tr>
      <td>{{ $value->nombretienda}} - {{ $value->nombrecaja }}</td>
      <td>{{ $value->fecharegistro }}</td>
      <td>{{ str_pad( $value->codigoventa, 8, "0", STR_PAD_LEFT ) }}</td>
      <td>{{ str_pad( $value->codigo, 8, "0", STR_PAD_LEFT ) }}</td>
      <td>{{ $value->nombreresponsable }}</td>
      <td><?php echo tipopago_detalle('notadevolucion',$value->id)['detalle'] ?></td>
      <td>{{ $value->codigomoneda }}</td>
      <td>{{ number_format($value->total, 2, '.', '') }}</td>
      <td>
        @if( $value->idestado == 1 )
            <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
        @else
            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>  
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>