<table class="table table-bordered table-hover table-striped" style="width:100%">
    <thead class="thead-dark">
        <tr>
            <th width="100px">Tienda</th>
            <th>Fecha de registro</th>
            <th>fecha de compra</th>
            <th>Proveedor</th>
            <th>Código</th>
            <th>Comprobante</th>
            <th>Número</th>
            <th>Cajero</th>
            <th>Moneda</th>
            <th>Forma de Pago</th>
            <th>Tipo de Pago - Descripción</th>
            <th>Total Pagado</th>
            <th>Total Deuda</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($compra as $value)
        <?php        
            $totaldetalle = DB::table('compradetalle')
                    ->where('idcompra',$value->id)
                    ->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
            $totalpagado = 0;
            $deudatotal  = 0;
            if($value->idformapago==1){
                $totalpagado = $totaldetalle;
            }
            elseif($value->idformapago==2){
                $totalpagado = DB::table('pagocredito')
                    ->where('idestado',2)
                    ->where('idcompra',$value->id)
                    ->sum('monto');
                $deudatotal = $totaldetalle-$totalpagado;
            }
            elseif($value->idformapago==3){
                $totalpagado = DB::table('pagoletra')
                    ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                    ->where('pagoletra.idestado',2)
                    ->where('tipopagoletra.idcompra',$value->id)
                    ->sum('pagoletra.monto');
                $deudatotal = $totaldetalle-$totalpagado;
            }
        ?>
        <tr>
            <?php $montototal = DB::table('compradetalle')->where('idcompra',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)')); ?>
            <td>{{ $value->tiendanombre }}</td>
            <td>{{ $value->fecharegistro }}</td>
            <td>{{ $value->idestado==2 ? $value->fechaconfirmacion : '---' }}</td>
            <td>
                @if( $value->tipopersonaProveedor == 1 )
                    {{ $value->identificacionProveedor }} - {{ $value->apellidoProveedor }}, {{ $value->nombreProveedor }}
                @else
                    {{ $value->identificacionProveedor }} - {{ $value->apellidoProveedor }}
                @endif
            </td>
            <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ $value->nombreComprobante }}</td>
            <td>{{ $value->seriecorrelativo }}</td>
            <td>{{ $value->nombrevendedor }}</td>
            <td>{{ $value->monedacodigo }}</td>
            <td>{{ $value->formapagonombre }}</td>
            <td><?php echo tipopago_detalle('compra', $value->id)[ 'detalle' ] ?></td>
            <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                {{ number_format($totalpagado, 2, '.', '') }}
            </td>
            <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828;"'?>>
                {{ number_format($deudatotal, 2, '.', '') }}
            </td>
            <td>
                @if($value->idestado==1)
                  <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
                @else
                  <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Comprado</span></div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>