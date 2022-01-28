<table class="table table-bordered table-hover table-striped" style="width:100%">
    <thead class="thead-dark">
      <tr>
          <th width="100px">Tienda</th>
          <th>Nombre</th>
          <th>Soles</th>
          <th>Dolares</th>
          <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($caja as $value)
      <tr>
          <?php $montocierresoles = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$value->id)
                ->where('aperturacierre.idestado',5)
                ->sum('montocierresoles');
                $montocierredolares = DB::table('aperturacierre')
                ->where('aperturacierre.idcaja',$value->id)
                ->where('aperturacierre.idestado',5)
                ->sum('montocierredolares');
          ?>
          <td>{{ $value->tiendanombre }}</td>
          <td>{{ $value->nombre }}</td>
          <td>{{$monedasoles->simbolo}} {{ number_format($montocierresoles, 2, '.', '') }}</td>
          <td>{{$monedadolares->simbolo}} {{ number_format($montocierredolares, 2, '.', '') }}</td>
          <td>{{ $value->idestado == 1 ? 'Activado' : 'Desactivado' }}</td>
      </tr>
      @endforeach
    </tbody>
</table>