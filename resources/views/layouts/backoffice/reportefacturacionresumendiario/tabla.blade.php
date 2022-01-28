<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th>Fecha de Generación</th>
      <th>Fecha de Resumen</th>
      <th>Correlativo</th>        
    </tr>
  </thead>
  <tbody>
    @foreach($facturacionresumendiario as $value)
    <tr>
      <td>{{ $value->resumen_fechageneracion }}</td>
      <td>{{ $value->resumen_fecharesumen }}</td>
      <td>{{ $value->resumen_correlativo }}</td>
    </tr>
    @endforeach
  </tbody>
</table>