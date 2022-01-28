<table style="width:100%">
  <thead class="thead-dark">
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="5">
        {{ $titulo }}
      </th>
    </tr>
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Nombre</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Soles</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Dolares</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($caja as $value)
    <tr>
        <?php $montocierresoles   = DB::table('aperturacierre')
                                    ->where('aperturacierre.idcaja',$value->id)
                                    ->where('aperturacierre.idestado',5)
                                    ->sum('montocierresoles');
      
              $montocierredolares = DB::table('aperturacierre')
                                    ->where('aperturacierre.idcaja',$value->id)
                                    ->where('aperturacierre.idestado',5)
                                    ->sum('montocierredolares');
        ?>
        <td></td>
        <td style="border: 1px solid black;">{{ $value->tiendanombre }}</td>
        <td style="border: 1px solid black;">{{ $value->nombre }}</td>
        <td style="border: 1px solid black;">{{$monedasoles->simbolo}} {{ number_format($montocierresoles, 2, '.', '') }}</td>
        <td style="border: 1px solid black;">{{$monedadolares->simbolo}} {{ number_format($montocierredolares, 2, '.', '') }}</td>
        <td style="border: 1px solid black;">{{ $value->idestado == 1 ? 'Activado' : 'Desactivado' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>