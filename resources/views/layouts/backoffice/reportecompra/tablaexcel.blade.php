<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
          <th></th>
          <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="14">
            {{ $titulo }}
          </th>
        </tr>
        @if($comprobante != '')
        <tr>
          <th></th>
          <th style="font-weight: 900;">Comprobante:</th>
          <th style="font-weight: 900;" colspan="13">
            {{ $comprobante->nombre }}
          </th>
        </tr>
        @endif
        @if($inicio != '')
        <tr>
          <th></th>
          <th style="font-weight: 900;">Fecha de Inicio:</th>
          <th style="font-weight: 900;" colspan="13">{{$inicio}}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
          <th></th>
          <th style="font-weight: 900;">Fecha de Fin:</th>
          <th style="font-weight: 900;" colspan="13">{{$fin}}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de registro</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">fecha de compra</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Proveedor</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Comprobante</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Número</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cajero</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Forma de Pago</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo de Pago - Descripción</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total Pagado</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total Deuda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php
          $tpagado = 0;
          $tdeuda  = 0;
        ?>
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
              $tdeuda += $deudatotal;
          }
          elseif($value->idformapago==3){
              $totalpagado = DB::table('pagoletra')
                  ->join('tipopagoletra','tipopagoletra.id','pagoletra.idtipopagoletra')
                  ->where('pagoletra.idestado',2)
                  ->where('tipopagoletra.idcompra',$value->id)
                  ->sum('pagoletra.monto');
              $deudatotal = $totaldetalle-$totalpagado;
              $tpagado  += $deudatotal;
          }
      
        ?>
        <tr>
            <?php $montototal = DB::table('compradetalle')->where('idcompra',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)')); ?>
            <td></td>
            <td style="border: 1px solid black;">{{ $value->tiendanombre }}</td>
            <td style="border: 1px solid black;">{{ $value->fecharegistro }}</td>
            <td style="border: 1px solid black;">{{ $value->idestado==2 ? $value->fechaconfirmacion : '---' }}</td>
            <td style="border: 1px solid black;">
              @if( $value->tipopersonaProveedor == 1 )
                {{ $value->identificacionProveedor }} - {{ $value->apellidoProveedor }}, {{ $value->nombreProveedor }}
              @else
                {{ $value->identificacionProveedor }} - {{ $value->apellidoProveedor }}
              @endif
            </td>
            <td style="border: 1px solid black;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td style="border: 1px solid black;">{{ $value->nombreComprobante }}</td>
            <td style="border: 1px solid black;">{{ $value->seriecorrelativo }}</td>
            <td style="border: 1px solid black;">{{ $value->nombrevendedor }}</td>
            <td style="border: 1px solid black;">{{ $value->monedacodigo }}</td>
            <td style="border: 1px solid black;">{{ $value->formapagonombre }}</td>
            <td style="border: 1px solid black;"><?php echo tipopago_detalle('compra', $value->id)[ 'detalle' ] ?></td>
            <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828; border: 1px solid black;"'?>>
              {{ number_format($totalpagado, 2, '.', '') }}
            </td>
            <td <?php echo $deudatotal>0?'style="background-color: #ef7d88;color: #352828;"':'style="background-color: #76e08f;color: #352828; border: 1px solid black;"'?>>
              {{ number_format($deudatotal, 2, '.', '') }}
            </td>
            <td style="border: 1px solid black;">
              @if($value->idestado==1)
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
              @else
                <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Comprado</span></div>
              @endif
            </td>
        </tr>
        @endforeach
        <tr>
          <td></td>
          <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="11">
            Total:
          </td>
          <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $tpagado }}</td>
          <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $tdeuda }}</td>
          <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;"></td>
        </tr>
    </tbody>
</table>