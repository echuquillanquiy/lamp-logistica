<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="9">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="8">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="8">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda - Caja</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de registro</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Venta</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Código</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo de Pago - Descripción</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php $total = 0; ?>
    @foreach($devolucionnota as $value)
    <?php $total += $value->total; ?>
    <?php
         $detalle_pago = tipopago_detalle('venta', $value->id)['detalle_array'];
    ?>
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->nombretienda}} - {{ $value->nombrecaja }}</td>
      <td style="border: 1px solid black;">{{ $value->fecharegistro }}</td>
      <td style="border: 1px solid black;">{{ str_pad( $value->codigoventa, 8, "0", STR_PAD_LEFT ) }}</td>
      <td style="border: 1px solid black;">{{ str_pad( $value->codigo, 8, "0", STR_PAD_LEFT ) }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreresponsable }}</td>
      <td style="border: 1px solid black;">
        @if(!empty($detalle_pago))
           Tipo Pago: {{ $detalle_pago['tipopagonombre'] }}
           Banco: {{ $detalle_pago['banco'] }}
           Nro Operacion: {{ $detalle_pago['nrooperacion'] }}
           Numero: {{ $detalle_pago['numero'] }}
           Emision: {{ $detalle_pago['emision'] }}
           Fecha Vencimiento: {{ $detalle_pago['vcto'] }}
        @endif
      </td>
      <td style="border: 1px solid black;">{{ $value->codigomoneda }}</td>
      <td style="border: 1px solid black;">{{ number_format($value->total, 2, '.', '') }}</td>
      <td style="border: 1px solid black;">
        @if( $value->idestado == 1 )
            <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
        @else
            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>  
        @endif
      </td>
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="7">
        Total:
      </td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $total }}</td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;"></td>
    </tr>
  </tbody>
</table>