<table>
  <thead>
    <tr>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">ITEM</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">FECHA</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">TIPO</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">NUMERO</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">MONEDA</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">CODIGO|RUC|DNI</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">CLIENTE</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">VALOR</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">VENTA BOLSA</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">EXONERADO</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">IGV</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">ICBPER</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">PERCEPCIÓN</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">TOTAL</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">SUB</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">COSTO</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">CTACBLE</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">GLOSA</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">TDOC REF</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">NUMERO REF</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">FECHA REF</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">IGV REF</th>
     <th style="background-color: #E6E5E5; font-weight: bold; border: 1px solid black; text-align: center; font-size: 11px;">BASE IMP REF</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $i = 1;
      
    
    ?>
    @foreach($facturacionboletafacturas as $value)
      <?php
        $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
                  $facturacionresumendetalle = DB::table('facturacionresumendetalle')
                      ->where('facturacionresumendetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
      $cliente_numerodocumento = $value->cliente_numerodocumento;
      $cliente_razonsocial = $value->cliente_razonsocial;
      $venta_valorventa = $value->venta_valorventa;
      $venta_montoigv = $value->venta_montoigv;
      $venta_montoimpuestoventa = $value->venta_montoimpuestoventa;
      $addstyle = '';
      if($facturacioncomunicacionbajadetalle!=''){
          $cliente_numerodocumento = '0001';
          $cliente_razonsocial = 'FACTURA ANULADA';
          $venta_valorventa = '0.00';
          $venta_montoigv = '0.00';
          $venta_montoimpuestoventa = '0.00';
          $addstyle = 'color:#ff2e29;';
      }
      elseif($facturacionresumendetalle!=''){
          $cliente_numerodocumento = '0001';
          $cliente_razonsocial = 'BOLETA ANULADA';
          $venta_valorventa = '0.00';
          $venta_montoigv = '0.00';
          $venta_montoimpuestoventa = '0.00';
          $addstyle = 'color:#ff2e29;';
      }
      ?>
      <tr>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $i }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ date_format(date_create($value->venta_fechaemision), 'd/m/Y') }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->venta_tipodocumento }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->venta_serie.'-'.$value->venta_correlativo }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?> text-align: center;">{{ $value->venta_tipomoneda == 'PEN' ? 'S/.' : '$' }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?> text-align: right;">{{ $cliente_numerodocumento }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $cliente_razonsocial }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $venta_valorventa }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $venta_montoigv }} </td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $venta_montoimpuestoventa }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
      </tr>
      <?php $i++;?>
    @endforeach
  </tbody>
</table>