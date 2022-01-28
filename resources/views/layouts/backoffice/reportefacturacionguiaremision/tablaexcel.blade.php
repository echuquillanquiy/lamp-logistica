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
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Emisión</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Traslado</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Emisor</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Serie</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Correlativo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente Destinatario</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Motivo</th>
    </tr>
  </thead>
  <tbody>
    @foreach($facturacionguiaremision as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->tiendanombre }}</td>
      <td style="border: 1px solid black;">{{ $value->despacho_fechaemision }}</td>
      <td style="border: 1px solid black;">{{ $value->envio_fechatraslado }}</td>
      <td style="border: 1px solid black;">{{ $value->emisor_nombrecomercial }}</td>
      <td style="border: 1px solid black;">{{ $value->guiaremision_serie }}</td>
      <td style="border: 1px solid black;">{{ $value->guiaremision_correlativo }}</td>
      <td style="border: 1px solid black;">{{ $value->despacho_destinatario_razonsocial }}</td>
      <td style="border: 1px solid black;">{{ $value->envio_descripciontraslado }}</td>
    </tr>
    @endforeach
  </tbody>
</table>