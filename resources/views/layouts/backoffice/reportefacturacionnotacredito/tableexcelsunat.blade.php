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
    @foreach($facturacionnotacreditos as $value)
      <?php
        $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionnotacredito',$value->id)
                      ->limit(1)
                      ->first(); 
    
    
      $cliente_numerodocumento = $value->cliente_numerodocumento;
      $cliente_razonsocial = $value->cliente_razonsocial;
      $notacredito_valorventa = $value->notacredito_valorventa;
      $notacredito_montoigv = $value->notacredito_montoigv;
      $notacredito_montoimpuestoventa = $value->notacredito_montoimpuestoventa;
      $addstyle = '';
      if($facturacioncomunicacionbajadetalle!=''){
          $cliente_numerodocumento = '0001';
          $cliente_razonsocial = 'NOTA DE CREDITO ANULADA';
          $notacredito_valorventa = '0.00';
          $notacredito_montoigv = '0.00';
          $notacredito_montoimpuestoventa = '0.00';
          $addstyle = 'color:#ff2e29;';
      }
      ?>
      <tr>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $i }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ date_format(date_create($value->notacredito_fechaemision), 'd/m/Y') }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->notacredito_tipodocumento }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->notacredito_serie.'-'.$value->notacredito_correlativo }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?> text-align: center;">{{ $value->notacredito_tipomoneda == 'PEN' ? 'S/.' : '$' }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?> text-align: right;">{{ $cliente_numerodocumento }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $cliente_razonsocial }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $notacredito_valorventa }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $notacredito_montoigv }} </td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $notacredito_montoimpuestoventa }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>"></td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->facturacionboletafactura_tipodocumento }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->facturacionboletafactura_serie.' - '.$value->facturacionboletafactura_correlativo }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ date_format(date_create($value->facturacionboletafacturaventa_fechaemision), 'd/m/Y') }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->facturacionboletafactura_venta_montoigv }}</td>
          <td style="border: 1px solid black; <?php echo $addstyle ?>">{{ $value->facturacionboletafactura_venta_montoimpuestoventa - $value->facturacionboletafactura_venta_montoigv }}</td>
      </tr>
      <?php $i++;?>
    @endforeach
  </tbody>
</table>