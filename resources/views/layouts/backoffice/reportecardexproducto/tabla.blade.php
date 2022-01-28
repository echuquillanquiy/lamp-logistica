
<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th>Fecha</th>
      <th>Responsable</th>
      <th>Tipo</th>
      <th>Código</th>
      <th>Motivo</th>
      <th>Detalle</th>
      <th>Cant.</th>
    </tr>
  </thead>
  <tbody>
    @foreach($producto as $value)
    <tr>
      <td> {{$value->fechaconfirmacion}} </td>
      <td> {{$value->usuario}}</td>
      <td> {{$value->tipo}}</td>
      <td> {{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }} </td>
      <td> {{$value->motivo}}</td>
      <td> {{$value->detalle}}</td>
      <td> {{$value->cantidad}} </td>
    </tr>
    @endforeach
  </tbody>
</table>