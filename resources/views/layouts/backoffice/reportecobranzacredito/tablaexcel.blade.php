<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="11">
              {{ $titulo }}
            </th>
        </tr>
        @if($inicio != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Inicio:</th>
            <th style="font-weight: 900;" colspan="10">{{$inicio}}</th>
        </tr>
        @endif
        @if($fin != '')
        <tr>
            <th></th>
            <th style="font-weight: 900;">Fecha de Fin:</th>
            <th style="font-weight: 900;" colspan="10">{{$fin}}</th>
        </tr>
        @endif
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda - Caja</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de registro</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de confirmación</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cod. Cobranza de Crédito</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código de Venta</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo de Pago - Descripción</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Monto</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        @foreach($cobranzacredito as $value)
        <?php $total += $value->monto; ?>
        <tr>
            <td></td>
            <td style="border: 1px solid black;">{{$value->tiendanombre}} - {{$value->cajanombre}}</td>
            <td style="border: 1px solid black;">{{$value->fecharegistro}}</td>
            <td style="border: 1px solid black;">
                @if($value->idestado == 2 or $value->idestado == 3)
                    {{$value->fechaconfirmacion}}
                @else
                    --- 
                @endif
            </td>
            <td style="border: 1px solid black;">{{$value->responsablenombre}}</td>
            <td style="border: 1px solid black;">{{$value->cliente}}</td>
            <td style="border: 1px solid black;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td style="border: 1px solid black;">{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td style="border: 1px solid black;">{{$value->monedacodigo}}</td>
            <td style="border: 1px solid black;"><?php echo tipopago_detalle('cobranzacredito', $value->id)[ 'detalle' ] ?></td>
            <td style="border: 1px solid black;">{{$value->monto}}</td>
            <td style="border: 1px solid black;">
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
        <tr>
            <td></td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="9">
              Total:
            </td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $total }}</td>
            <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;"></td>
        </tr>
    </tbody>
</table>