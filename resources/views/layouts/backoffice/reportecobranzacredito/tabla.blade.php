<table class="table table-bordered table-hover table-striped" style="width:100%">
    <thead class="thead-dark">
        <tr>
            <th width="100px">Tienda - Caja</th>
            <th>Fecha de registro</th>
            <th>Fecha de confirmación</th>
            <th>Responsable</th>
            <th>Cliente</th>
            <th>Cod. Cobranza de Crédito</th>
            <th>Código de Venta</th>
            <th>Moneda</th>
            <th>Tipo de Pago - Descripción</th>
            <th>Monto</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cobranzacredito as $value)
        <tr>
            <td>{{$value->tiendanombre}} - {{$value->cajanombre}}</td>
            <td>{{$value->fecharegistro}}</td>
            <td>
                @if($value->idestado == 2 or $value->idestado == 3)
                    {{$value->fechaconfirmacion}}
                @else
                    --- 
                @endif
            </td>
            <td>{{$value->responsablenombre}}</td>
            <td>{{$value->cliente}}</td>
            <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{$value->monedacodigo}}</td>
            <td><?php echo tipopago_detalle('cobranzacredito', $value->id)[ 'detalle' ] ?></td>
            <td>{{$value->monto}}</td>
            <td>
                @if($value->idestado == 1)
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
                @elseif($value->idestado == 2)
                    <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                @elseif($value->idestado == 3)
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>