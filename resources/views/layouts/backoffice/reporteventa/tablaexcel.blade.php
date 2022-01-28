<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="15">
                {{ $titulo }}
            </th>
        </tr>
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="14">{{$inicio}}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="14">{{$fin}}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de confirmación</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cajero</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Vendedor</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">RUC/DNI</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Forma de Pago</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo de Pago - Descripción</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total Pagado</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total Deuda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php
          $tpagar   = 0;
          $tpagado  = 0;
          $tdeuda   = 0;
        ?>
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
              
//                   $facturacionboletafacturas = DB::table('facturacionboletafactura')
//                       ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idfacturacionboletafactura','facturacionboletafactura.id')
//                       ->where('facturacionboletafactura.idventa',$value->id)
//                       ->orWhere('facturacionboletafacturadetalle.idventa',$value->id)
//                       ->select(
//                           'facturacionboletafactura.venta_serie as venta_serie',
//                           'facturacionboletafactura.venta_correlativo as venta_correlativo'
//                       )
//                       ->orderBy('facturacionboletafactura.id','desc')
//                       ->distinct()
//                       ->get();
        ?>
        <tr>
            <td></td>
            <td style="border: 1px solid black;">{{ $value->tiendanombre}}</td>
            <td style="border: 1px solid black;">                
                @if($value->idestado == 2 or $value->idestado == 3)
                    {{$value->fechaconfirmacion}}
                @else
                    --- 
                @endif
            </td>
            <td style="border: 1px solid black;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td style="border: 1px solid black;">{{ $value->nombreusuariocajero }}</td>
            <td style="border: 1px solid black;">{{ $value->nombreusuariovendedor }}</td>
            <td style="border: 1px solid black;">{{ $value->identificacioncliente }}</td>
            <td style="border: 1px solid black;">{{ $value->cliente }}</td>
            <td style="border: 1px solid black;">{{ $value->monedanombre }}</td>
            <td style="border: 1px solid black;">{{ $value->nombreFormapago }}</td>
            <td style="border: 1px solid black;">
              <?php
               $detalle_pago = tipopago_detalle('venta', $value->id)['detalle_array'];
              ?>
              @if(!empty($detalle_pago))
                 Tipo Pago: {{ $detalle_pago['tipopagonombre'] }}
                 Banco: {{ $detalle_pago['banco'] }}
                 Nro Operacion: {{ $detalle_pago['nrooperacion'] }}
                 Numero: {{ $detalle_pago['numero'] }}
                 Emision: {{ $detalle_pago['emision'] }}
                 Fecha Vencimiento: {{ $detalle_pago['vcto'] }}
              @endif
            </td>

            <td>{{ $value->montorecibido }}</td>
            <td <?php echo $deudatotal > 0 ? 'style="background-color: #ef7d88;color: #352828;width:15px;"':'style="background-color: #76e08f;color: #352828;width:15px; border: 1px solid black;"'?>>
              {{ number_format($totalpagado, 2, '.', '') }}
            </td>
            <td <?php echo $deudatotal > 0 ? 'style="background-color: #ef7d88;color: #352828;width:15px;"':'style="background-color: #76e08f;color: #352828;color: #352828;width:15px; border: 1px solid black;"'?>>
              {{ number_format($deudatotal, 2, '.', '') }}
            </td>
        
            <td style="border: 1px solid black;">
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
        <tr>
            <td></td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="11">
                Total:
            </td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $tpagar }}</td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $tpagado }}</td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $tdeuda }}</td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;"></td>
        </tr>
    </tbody>
</table>