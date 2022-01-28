<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="8">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="7">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="7">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>          
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha Solicitud</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha Envio</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha Recepción</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda Origen (Responsable)</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda Destino (Responsable)</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Motivo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($productotransferencia as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">{{ ($value->idestadotransferencia==1 or $value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechasolicitud), 'd/m/Y - h:i A'):'---' }}</td>
      <td style="border: 1px solid black;">{{ ($value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechaenvio), 'd/m/Y - h:i A'):'---' }}</td>
      <td style="border: 1px solid black;">{{ $value->idestadotransferencia==3?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
      <td style="border: 1px solid black;">{{ $value->tienda_origen_nombre}} {{ $value->idusersorigen!=0?'('.$value->user_origen_nombre.')':'' }}</td>
      <td style="border: 1px solid black;">{{ $value->tienda_destino_nombre}} {{ $value->idusersdestino!=0?'('.$value->user_destino_nombre.')':'' }}</td>
      <td style="border: 1px solid black;">{{ $value->motivo}}</td>
      <td style="border: 1px solid black;">
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