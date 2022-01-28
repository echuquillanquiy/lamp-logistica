<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th>Código</th>          
      <th width="100px">Fecha Solicitud</th>
      <th width="100px">Fecha Envio</th>
      <th width="100px">Fecha Recepción</th>
      <th>Tienda Origen (Responsable)</th>
      <th>Tienda Destino (Responsable)</th>
      <th>Motivo</th>
      <th width="10px">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($productotransferencia as $value)
    <tr>
      <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
      <td>{{ ($value->idestadotransferencia==1 or $value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechasolicitud), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ ($value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechaenvio), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ $value->idestadotransferencia==3?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ $value->tienda_origen_nombre}} {{ $value->idusersorigen!=0?'('.$value->user_origen_nombre.')':'' }}</td>
      <td>{{ $value->tienda_destino_nombre}} {{ $value->idusersdestino!=0?'('.$value->user_destino_nombre.')':'' }}</td>
      <td>{{ $value->motivo}}</td>
      <td>
      @if($value->idestadotransferencia==1)
          <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Solicitud</span></div> 
      @elseif($value->idestadotransferencia==2)
          <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-share"></i> Envio</span></div>
      @elseif($value->idestadotransferencia==3)
          <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Recepcionado</span></div>
      @endif 
      </td>
    </tr>
    @endforeach
  </tbody>
</table>