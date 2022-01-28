<table>
  <thead>
    <tr></tr>
    <tr>
      <th></th>
      <th style="font-weight: 900; background-color: #065f65; color: #ffffff; text-align: center; font-size: 12px; " colspan="8">
        {{ $titulo }}
      </th>
    </tr>
    @if($comprobante != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Tipo de Documento:</th>
      <th style="font-weight: 900;" colspan="12">
        @if($comprobante == '01')
          FACTURA
        @elseif($comprobante == '03')
          BOLETA
        @elseif($comprobante == '07')
          NOTA DE CREDITO
        @else
          {{ $comprobante }}
        @endif
      </th>
    </tr>
    @endif
    @if($inicio != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Inicio:</th>
      <th style="font-weight: 900;" colspan="7">{{$inicio}}</th>
    </tr>
    @endif
    @if($fin != '')
    <tr>
      <th></th>
      <th style="font-weight: 900;">Fecha de Fin:</th>
      <th style="font-weight: 900;" colspan="7">{{$fin}}</th>
    </tr>
    @endif
    <tr></tr>
    <tr>
      <th></th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Fecha de Generación</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Responsable</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Tipo Documento</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Doc. Afectado</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">DNI/RUC</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Cliente</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Motivo de Anulación</th>
      <th style="border: 1px solid black; font-weight: 900; background-color: #065f65; color: #ffffff;">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($comunicacionbaja as $value)
    <tr>
      <td></td>
      <td style="border: 1px solid black;">{{ $value->comunicacionbaja_fechageneracion }}</td>
      <td style="border: 1px solid black;">{{ $value->responsableapellidos }}, {{ $value->responsablenombre }}</td>
      <td style="border: 1px solid black;">
        @if($value->tipodocumento == '01')
          FACTURA
        @elseif($value->tipodocumento == '03')
          BOLETA
        @elseif($value->tipodocumento == '07')
          NOTA DE CREDITO
        @else          
          {{ $value->tipodocumento }}
        @endif
      </td>
      <td style="border: 1px solid black;">{{ $value->serie }} - {{ $value->correlativo }}</td>
      <td style="border: 1px solid black;">{{ $value->factbol_cliente_numerodocumento }} {{ $value->notacred_cliente_numerodocumento }}</td>
      <td style="border: 1px solid black;">{{ $value->factbol_cliente_razonsocial }} {{ $value->notacred_cliente_razonsocial }}</td>
      <td style="border: 1px solid black;">{{ $value->motivo }}</td>
      <td style="border: 1px solid black;">
        @if($value->idestadofacturacion == 0)
          <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
        @elseif($value->idestadofacturacion == 1)
          <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Correcto</span></div> 
        @elseif($value->idestadofacturacion == 2)
          <div class="td-badge"><span class="badge badge-pill"><i class="fa fa-times"></i> Observado</span></div>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>