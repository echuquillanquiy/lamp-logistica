<table>
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="3">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="2">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="2">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Generación</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Resumen</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Correlativo</th>
    </tr>
  </thead>
  <tbody>
    @foreach($facturacionresumendiario as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->resumen_fechageneracion }}</td>
      <td style="border: 1px solid black;">{{ $value->resumen_fecharesumen }}</td>
      <td style="border: 1px solid black;">{{ $value->resumen_correlativo }}</td>
    </tr>
    @endforeach
  </tbody>
</table>