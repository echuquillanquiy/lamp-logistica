<table class="table table-bordered table-hover table-striped" style="width:100%">
  <thead class="thead-dark">
    <tr>
      <th width="50px">Tipo de Pago</th>
      <th width="100px">Módulo</th>
      <th width="200px">Cliente</th>
      <th>Detalle</th>
      <th width="10px">Moneda</th>
      <th width="10px">Monto</th>
    </tr>
  </thead>
  <tbody>
    <!-- 
    Forma de pago:
      1-Efectivo
      2-Deposito
      3-Cheque
      4-Saldo
    -->
    @foreach($tipopagodetalle as $value)
    <tr>
      <td>{{ $value->tipopagonombre }}</td>      
      <td>
        @if($value->idventa != 0)
          VENTA
        @elseif($value->idnotadevolucion != 0)
          NOTA DEVOLUCIÓN
        @endif
      </td>
      <td>
        @if($value->idventa != 0)
          {{ $value->ventacliente }}
        @elseif($value->idnotadevolucion != 0)
          {{ $value->notadevolucionresponsable }}
        @endif
      </td>
      <td>
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
          Saldo de Cliente: {{ $value->saldoapellidos }}, {{ $value->saldonombre }}
        @else
          Saldo de Cliente: ---
        @endif
        
      @endif
      </td>
      <td>
        @if($value->idventa != 0)
          {{ $value->monedanombreventa }}
        @elseif($value->idnotadevolucion != 0)
          {{ $value->monedanombrenota }}
        @endif
      </td>
      <td>{{ $value->monto }}</td>
    </tr>
    @endforeach
  </tbody>
</table>