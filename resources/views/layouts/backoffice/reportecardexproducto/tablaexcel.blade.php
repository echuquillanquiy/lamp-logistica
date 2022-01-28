<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="7">
        {{ $titulo }}
      </th>
    </tr>
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Motivo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Detalle</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cant.</th>
    </tr>
  </thead>
  <tbody>
    @foreach($producto as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;"> {{$value->fechaconfirmacion}} </td>
      <td style="border: 1px solid black;"> {{$value->usuario}}</td>
      <td style="border: 1px solid black;"> {{$value->tipo}}</td>
      <td style="border: 1px solid black;"> {{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }} </td>
      <td style="border: 1px solid black;"> {{$value->motivo}}</td>
      <td style="border: 1px solid black;"> {{$value->detalle}}</td>
      <td style="border: 1px solid black;"> {{$value->cantidad}} </td>
    </tr>
    @endforeach
  </tbody>
</table>