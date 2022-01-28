@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacionboletafactura/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Boletas y Facturas</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="90px">Cod. Venta</th>
                <th width="100px">Fecha de Emisión</th>
                <th width="80px">Responsable</th>
                <th width="110px">Comprobante</th>
                <th width="85px">Serie</th>
                <th width="85px">Correlativo</th>
                <th width="85px">DNI/RUC</th>
                <th>Cliente</th>
                <th width="85px">Moneda</th>
                <th width="80px">Base Imp.</th>
                <th width="80px">IGV</th>
                <th width="80px">Total</th>
                <th width="10px">N. Crédito</th>
                <th width="10px">Estado</th>
                <th width="10px">SUNAT</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>[
          '',
          'date:fechaemision',
          'responsable',
          'select:comprobante/01=FACTURA,03=BOLETA',
          'serie',
          'correlativo',
          'clienteidentificacion',
          'cliente',
          'moneda',
          '',
          '',
          '',
          '',
          'select:estado/0=Pendiente,1=Correcto,2=Observado,=Todo'
          ],
                'search_url'=> url('backoffice/facturacionboletafactura')
            ])
            <tbody>
                @foreach($facturacionboletafacturas as $value)
                <?php 
                  $ventas = DB::table('venta')
                      ->join('facturacionboletafacturadetalle','facturacionboletafacturadetalle.idventa','venta.id')
                      ->orWhere('facturacionboletafacturadetalle.idfacturacionboletafactura',$value->id)
                      ->select(
                          'venta.codigo as ventacodigo'
                      )
                      ->distinct()
                      ->get();
                  $facturacionnotacreditos = DB::table('facturacionnotacredito')
                      ->where('facturacionnotacredito.idfacturacionboletafactura',$value->id)
                      ->select(
                          'facturacionnotacredito.*'
                      )
                      ->orderBy('facturacionnotacredito.id','desc')
                      ->get();
                  $codnotacredito = '';
                  //$totalnotacredito = 0;
                  foreach($facturacionnotacreditos as $valuenotacreditos){
                      //totalnotacredito = $totalnotacredito+$valuenotacreditos->notacredito_montoimpuestoventa;
                      $codnotacredito = $codnotacredito.'<span class="badge badge-pill badge-dark">'.$valuenotacreditos->notacredito_serie.'-'.$valuenotacreditos->notacredito_correlativo.'</span>';
                  }
                ?>
                <?php 
                  $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
                  $facturacionresumendetalle = DB::table('facturacionresumendetalle')
                      ->where('facturacionresumendetalle.idfacturacionboletafactura',$value->id)
                      ->limit(1)
                      ->first(); 
                ?>
                <tr>
                  <td>
                    @foreach($ventas as $valueventas)
                    <span class="badge badge-pill badge-warning">{{ $valueventas->ventacodigo!=''?str_pad($valueventas->ventacodigo, 8, "0", STR_PAD_LEFT):'' }}</span><br>
                    @endforeach
                  </td>
                  
                  <!--td>{{ $value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):'---' }}</td-->
                  <td>{{ $value->venta_fechaemision }}</td>
                  <td>{{ $value->responsablenombre }}</td>
                  <td>
                    @if($value->venta_tipodocumento=='03')
                        BOLETA
                    @elseif($value->venta_tipodocumento=='01')
                        FACTURA
                    @endif  
                  </td>
                  <td>{{ $value->venta_serie }}</td>
                  <td>{{ $value->venta_correlativo }}</td>
                  <td>{{ $value->cliente_numerodocumento }}</td>
                  <td>{{ $value->cliente_razonsocial }}</td>
                  <td>{{ $value->venta_tipomoneda }}</td>
                  <td>{{ $value->venta_valorventa }}</td>
                  <td>{{ $value->venta_totalimpuestos }}</td>
                  <td>{{ $value->venta_montoimpuestoventa }}</td>
                  <td><?php echo $codnotacredito ?></td>
                  <td>
                    @if($facturacioncomunicacionbajadetalle!='')
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> C. Baja (Anulado)</span></div>
                    @elseif($facturacionresumendetalle!='')
                        @if($facturacionresumendetalle->estado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> R. Diario (Enviado)</span></div>
                        @elseif($facturacionresumendetalle->estado==3)
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> R. Diario (Anulado)</span></div>
                        @endif
                    @else
                    @if($value->idestadofacturacion==0)
                        <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                    @elseif($value->idestadofacturacion==1)
                        <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Correcto</span></div> 
                    @elseif($value->idestadofacturacion==2)
                        <div class="td-badge"><span class="badge badge-pill"><i class="fa fa-times"></i> Observado</span></div>
                    @endif  
                    @endif 
                  </td>
                  <td>
                    @if($value->idestadosunat==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div> 
                    @else
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Enviado</span></div>
                    @endif  
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestadofacturacion==0 or $value->idestadofacturacion==2)
                         <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=enviarsunat',size:'modal-fullscreen'})">
                           <i class="fa fa-paper-plane"></i> Reenviar a la SUNAT</a></li>
                         @endif 
                         <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Detalle</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=comprobante',size:'modal-fullscreen'})">
                            <i class="fa fa-file-alt"></i> PDF Comprobante
                         </a></li>
                         <?php $archivo = $value->emisor_ruc.'-'.$value->venta_tipodocumento.'-'.$value->venta_serie.'-'.$value->venta_correlativo; ?>
                         <li>
                            <a href="{{url('public/sunat/produccion/boletafactura/'.$archivo.'.xml')}}" target="_blank" download>
                                <i class="fa fa-file-alt"></i> XML Comprobante
                            </a>
                         </li>
                         <li><a href="{{url('public/sunat/produccion/boletafactura/R-'.$archivo.'.zip')}}" target="_blank" download>
                            <i class="fa fa-file-alt"></i> CDR Comprobante
                         </a></li>
                         <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=correo'})">
                            <i class="fa fa-share-square"></i> Enviar Correo
                         </a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $facturacionboletafacturas->links('app.tablepagination', ['results' => $facturacionboletafacturas]) }}
        </div>
    </div>

</div>
@endsection