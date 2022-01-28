<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="8">
        {{ $titulo }}
      </th>
    </tr>
    @if($estado != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Estado:</th>
      <th style="font-weight: 900;" colspan="7">
        @if($estado == 1)
          Apertura En Proceso
        @elseif($estado == 2)
          Apertura Pendiente
        @elseif($estado == 3)
          Aperturado
        @elseif($estado == 4)
          Cierre Pendiente
        @elseif($estado == 5)
          Caja Cerrada
        @endif
      </th>
    </tr>
    @endif
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
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda - Caja</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Apertura</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Cierre</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Recepción</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Apertura</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cierre</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($aperturacierre as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{$value->tiendanombre}} - {{$value->cajanombre}}</td>
      <td style="border: 1px solid black;">{{$value->fechaconfirmacion!=''?$value->fechaconfirmacion:'---'}}</td>
      <td style="border: 1px solid black;">{{$value->fechacierreconfirmacion!=''?$value->fechacierreconfirmacion:'---'}}</td>
      <td style="border: 1px solid black;">{{$value->responsableapellidos}}, {{$value->responsablenombre}}</td>
      <td style="border: 1px solid black;">{{$value->recepcionapellidos}}, {{$value->recepcionnombre}}</td>
      <td style="border: 1px solid black;">
        {{$monedasoles->simbolo}} {{$value->montoasignarsoles}} - {{$monedadolares->simbolo}} {{$value->montoasignardolares}}
      </td>
      <td style="border: 1px solid black;">
        @if($value->fechacierreconfirmacion!='')
            {{$monedasoles->simbolo}} {{$value->montocierresoles}} - {{$monedadolares->simbolo}} {{$value->montocierredolares}}
        @else
            ---
        @endif
      </td>
      <td style="border: 1px solid black;">
        @if($value->idestado==1)
          Apertura En Proceso
        @elseif($value->idestado==2)
          Apertura Pendiente 
        @elseif($value->idestado==3)
          Aperturado
        @elseif($value->idestado==4)
          Cierre Pendiente
        @elseif($value->idestado==5)
          Caja Cerrada
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>