<?php $idtienda = usersmaster()->idtienda; ?>
<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="100px">Tienda</th>
      <th>Fecha Registro</th>
      <th>Fecha Confirmación</th>
      <th>Código</th>
      <th>Movimiento</th>
      <th>Responsable</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($movimientoproducto as $value)
    <tr>
      <td>{{ $value->tienda_nombre }}</td>
      <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A') }}</td>
      <td>{{ $value->idestado==2?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
      <td>
      @if($value->idestadomovimiento==1)
          INGRESO
      @elseif($value->idestadomovimiento==2)
          SALIDA
      @endif 
      </td>
      <td>{{ $value->users_nombre }}</td>
      <td>
      @if($value->idestado==1)
          <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
      @elseif($value->idestado==2)
          <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Recepcionado</span></div>
      @endif 
      </td>
    </tr>
    @endforeach
  </tbody>
</table>