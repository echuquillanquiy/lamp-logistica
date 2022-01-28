<table>
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="6">
        {{ $titulo }}
      </th>
    </tr>
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo de Pago</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Módulo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Detalle</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Monto</th>
    </tr>
  </thead>
  <tbody>
    <?php $total = 0; ?>
    @foreach($tipopagodetalle as $value)
    <?php $total += $value->monto; ?>
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->tipopagonombre }}</td>
      <td style="border: 1px solid black;">
        @if($value->idventa != 0)
          VENTA
        @elseif($value->idnotadevolucion != 0)
          NOTA DEVOLUCIÓN
        @endif
      </td>
      <td style="border: 1px solid black;">
        @if($value->idventa != 0)
          {{ $value->ventacliente }}
        @elseif($value->idnotadevolucion != 0)
          {{ $value->notadevolucionresponsable }}
        @endif
      </td>
      <td style="border: 1px solid black;">
      @if($value->idtipopago == 1)
      @elseif($value->idtipopago == 2)
        
        Banco: {{ $value->bancodeposito }} <br>
        Nro. Cuenta: {{ $value->deposito_numerocuenta }} <br>
        Fecha de Depósito: {{ $value->deposito_fecha }} <br>
        Hora de Depósito: {{ $value->deposito_hora }} <br>
        Nro. Operación: {{ $value->deposito_numerooperacion }}
        
      @elseif($value->idtipopago == 3)
        
        Banco: {{ $value->bancocheque }} <br>
        Fecha de Emisión: {{ $value->cheque_emision }} <br>
        Fecha de Vencimiento: {{ $value->cheque_vencimiento }} <br>
        Nro. Cheque: {{ $value->cheque_numero }}

      @elseif($value->idtipopago == 4)
        
        @if($value->iduserssaldo != null)
          Saldo de Cliente: {{ $value->clienteapellidos }}, {{ $value->clientenombre }}
        @else
          Saldo de Cliente: ---
        @endif
        
      @endif
      </td>
      <td style="border: 1px solid black;">
        @if($value->idventa != 0)
          {{ $value->monedanombreventa }}
        @elseif($value->idnotadevolucion != 0)
          {{ $value->monedanombrenota }}
        @endif
      </td>
      <td style="border: 1px solid black;">{{ $value->monto }}</td>
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="5">
        Total:
      </td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $total }}</td>
    </tr>
  </tbody>
</table>