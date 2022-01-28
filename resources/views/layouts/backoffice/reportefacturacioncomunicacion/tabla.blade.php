<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th>Fecha de Generación</th>
      <th>Responsable</th>
      <th>Tipo Documento</th>
      <th>Doc. Afectado</th>
      <th>DNI/RUC</th>
      <th>Cliente</th>
      <th>Motivo de Anulación</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($comunicacionbaja as $value)
    <tr>
      <td>{{ $value->comunicacionbaja_fechageneracion }}</td>
      <td>{{ $value->responsableapellidos }}, {{ $value->responsablenombre }}</td>
      <td>
        @if($value->tipodocumento == '01')
          FACTURA
        @elseif($value->tipodocumento == '03')
          BOLETA DE VENTA
        @elseif($value->tipodocumento == '07')
          NOTA DE CREDITO
        @else          
          {{ $value->tipodocumento }}
        @endif
      </td>
      <td>{{ $value->serie }} - {{ $value->correlativo }}</td>
      <td>{{ $value->factbol_cliente_numerodocumento }} {{ $value->notacred_cliente_numerodocumento }}</td>
      <td>{{ $value->factbol_cliente_razonsocial }} {{ $value->notacred_cliente_razonsocial }}</td>      
      <td>{{ $value->motivo }}</td>
      <td>
        @if($value->idestadofacturacion == 0)
          <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
        @elseif($value->idestadofacturacion == 1)
          <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Correcto</span></div> 
        @elseif($value->idestadofacturacion == 2)
          <div class="td-badge"><span class="badge badge-pill"><i class="fa fa-times"></i> Observado</span></div>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>