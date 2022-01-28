<table style="width:100%">
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="12">
        {{ $titulo }}
      </th>
    </tr>
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="11">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="11">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tienda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Emisión</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Serie Correlativo</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cajero</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Proveedor</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cod. Producto</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Producto</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Forma de pago</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Moneda</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">P. Unitario</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cantidad</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php $total = 0; ?>
    @foreach($compraproducto as $value)
    <?php $total += $value->preciototal ?>
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->nombretienda }}</td>
      <td style="border: 1px solid black;">{{ $value->fechaemisioncompra }}</td>
      <td style="border: 1px solid black;">{{ $value->seriecorrelativocompra }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreresponsable }}</td>
      <td style="border: 1px solid black;">{{ $value->identificacionproveedor }} - {{ $value->nombreproveedor }}</td>
      <td style="border: 1px solid black;">{{ str_pad($value->codigoproducto, 6, "0", STR_PAD_LEFT) }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreproducto }}</td>
      <td style="border: 1px solid black;">{{ $value->nombreformapago }}</td>        
      <td style="border: 1px solid black;">{{ $value->codigomoneda }}</td>        
      <td style="border: 1px solid black;">{{ $value->preciounitario }}</td>
      <td style="border: 1px solid black;">{{ $value->cantidad }}</td>
      <td style="border: 1px solid black;">{{ $value->preciototal }}</td>
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: right;" colspan="10">
        Total:
      </td>
      <td style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center;">{{ $total }}</td>
    </tr>
  </tbody>
</table>