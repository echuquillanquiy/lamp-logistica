<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="100px">Tienda</th>
      <th>Fecha de registro</th>
      <th>Compra</th>
      <th>Código</th>
      <th>Responsable</th>
      <th>Proveedor</th>
      <th>Tipo de Pago - Descripción</th>
      <th>Moneda</th>
      <th>Total</th>
      <th>Estado</th>
      </tr>
  </thead>
  <tbody>
    @foreach($devolucioncompra as $value)
    <?php $total = DB::table('compradevoluciondetalle')
              ->where('idcompradevolucion',$value->id)
              ->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
    ?>
    <tr>
      <td>{{ $value->tiendanombre}}</td>
      <td>{{ $value->fecharegistro }}</td>
      <td>{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
      <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
      <td>{{ $value->responsable }}</td>
      <td>{{ $value->proveedor }}</td>
      <td><?php echo tipopago_detalle('compradevolucion', $value->id)[ 'detalle' ] ?></td>
      <td>{{ $value->monedacodigo }}</td>
      <td>{{ number_format($total, 2, '.', '') }}</td>
      <td>
        @if($value->idestado==1)
            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pedido (Orden de compra)</span></div>
        @elseif($value->idestado==2)
            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Compra</span></div>
        @elseif($value->idestado==3)
            <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>   
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>