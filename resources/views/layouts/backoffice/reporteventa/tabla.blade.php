<table class="table table-bordered table-hover table-striped" style="width:100%">
    <thead class="thead-dark">
        <tr>
            <th width="100px">Tienda</th>
            <th>Fecha de confirmación</th>
            <th>Código</th>
            <th>Cajero</th>
            <th>Vendedor</th>
            <th>RUC/DNI</th>
            <th>Cliente</th>
            <th width="100px">Moneda</th>
            <th>Forma de Pago</th>
            <th>Tipo de Pago - Descripción</th>
            <th width="100px">Total</th>
            <th width="100px">Total Pagado</th>
            <th width="100px">Total Deuda</th>
            <th width="10px">Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($venta as $value)
        <?php 
                  $totalnotadevolucion = DB::table('notadevolucion')
                    ->where('notadevolucion.idestado',2)
                    ->where('notadevolucion.idventa',$value->id)
                    ->sum('total');
              
                  $totalapagado = DB::table('notadevoluciondetalle')
                          ->join('notadevolucion','notadevolucion.id','notadevoluciondetalle.idnotadevolucion')
                          ->where('notadevolucion.idestado',2)
                          ->where('notadevolucion.idventa',$value->id)
                          ->sum(DB::raw('CONCAT(notadevoluciondetalle.cantidad*notadevoluciondetalle.preciounitario)'));
              
                  $totalpagado = $value->montorecibido;
                  $deudatotal = 0;
                  if($value->idformapago==2){
                      $totalpagado = DB::table('cobranzacredito')
                          ->where('idestado',2)
                          ->where('idventa',$value->id)
                          ->sum('monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalapagado;
                  }elseif($value->idformapago==3){
                      $totalpagado = DB::table('cobranzaletra')
                          ->join('tipopagoletra','tipopagoletra.id','cobranzaletra.idtipopagoletra')
                          ->where('cobranzaletra.idestado',2)
                          ->where('tipopagoletra.idventa',$value->id)
                          ->sum('cobranzaletra.monto');
                      $deudatotal = $value->montorecibido-$totalpagado-$totalnotadevolucion;
                  }
                  // fin Total pagado
        ?>
        <tr>
            <td style="width: 25px">{{ $value->tiendanombre}}</td>
            <td style="width: 25px">                
                @if($value->idestado == 2 or $value->idestado == 3)
                    {{$value->fechaconfirmacion}}
                @else
                    --- 
                @endif
            </td>
            <td style="width: 15px">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td style="width: 20px">{{ $value->nombreusuariocajero }}</td>
            <td style="width: 20px">{{ $value->nombreusuariovendedor }}</td>
            <td style="width: 20px">{{ $value->identificacioncliente }}</td>
            <td style="width: 40px">{{ $value->cliente }}</td>
            <td style="width: 15px">{{ $value->monedanombre }}</td>
            <td>{{ $value->nombreFormapago }}</td>
            <td><?php echo tipopago_detalle('venta', $value->id)[ 'detalle' ] ?></td>
            <td>{{ $value->montorecibido }}</td>
            <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;width:15px;"':'style="background-color: #76e08f;color: #352828;width:15px;"'?>>
                {{ number_format($totalpagado, 2, '.', '') }}
            </td>
            <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;width:15px;"':'style="background-color: #76e08f;color: #352828;color: #352828;width:15px;"'?>>
                {{ number_format($deudatotal, 2, '.', '') }}
            </td>
            <td>
                @if($value->idestado == 1)
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Cotización Pendiente</span></div> 
                @elseif($value->idestado == 2)
                    <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Cotización Confirmado</span></div>
                @elseif($value->idestado == 3)
                    <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Venta Correcta</span></div>
                @elseif($value->idestado == 4)
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Venta Anulada</span></div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>