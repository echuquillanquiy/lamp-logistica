<?php $idtienda = usersmaster()->idtienda; ?>
<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="7">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="6">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="6">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha Registro</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha Confirmación</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Movimiento</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($movimientoproducto as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->tienda_nombre }}</td>
      <td style="border: 1px solid black;">{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A') }}</td>
      <td style="border: 1px solid black;">{{ $value->idestado==2?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
      <td style="border: 1px solid black;">{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">
      @if($value->idestadomovimiento==1)
          INGRESO
      @elseif($value->idestadomovimiento==2)
          SALIDA
      @endif 
      </td>
      <td style="border: 1px solid black;">{{ $value->users_nombre }}</td>
      <td style="border: 1px solid black;">
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