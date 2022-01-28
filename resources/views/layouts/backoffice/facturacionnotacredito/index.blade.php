@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacionnotacredito/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Notas de Crédito</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="90px">Comprobante</th>
                <th width="100px">Fecha de Emisión</th>
                <th width="100px">Responsable</th>
                <th width="85px">Serie</th>
                <th width="85px">Correlativo</th>
                <th width="85px">DNI/RUC</th>
                <th>Cliente</th>
                <th width="85px">Moneda</th>
                <th width="80px">Base Imp.</th>
                <th width="80px">IGV</th>
                <th width="80px">Total</th>
                <th width="10px">Estado</th>
                <th width="10px">SUNAT</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>[
          'codigoventa',
          'date:fechaemision',
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
                'search_url'=> url('backoffice/facturacionnotacredito')
            ])
            <tbody>
                @foreach($facturacionnotacreditos as $value)
                <?php 
                  $facturacioncomunicacionbajadetalle = DB::table('facturacioncomunicacionbajadetalle')
                      ->where('facturacioncomunicacionbajadetalle.idfacturacionnotacredito',$value->id)
                      ->limit(1)
                      ->first(); 
                ?>
                <tr>
                  <td>{{ $value->notacredito_numerodocumentoafectado }}</td>
                  <td>{{ $value->notacredito_fechaemision }}</td>
                  <td>{{ $value->responsablenombre }}</td>
                  <td>{{ $value->notacredito_serie }}</td>
                  <td>{{ $value->notacredito_correlativo }}</td>
                  <td>{{ $value->cliente_numerodocumento }}</td>
                  <td>{{ $value->cliente_razonsocial }}</td>
                  <td>{{ $value->notacredito_tipomoneda }}</td>
                  <td>{{ $value->notacredito_valorventa }}</td>
                  <td>{{ $value->notacredito_totalimpuestos }}</td>
                  <td>{{ $value->notacredito_montoimpuestoventa }}</td>
                  <td>
                    @if($facturacioncomunicacionbajadetalle!='')
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> C. Baja (Anulado)</span></div>
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
                         <li><a href="javascript:;" onclick="modal({route:'facturacionnotacredito/{{ $value->id }}/edit?view=enviarsunat',size:'modal-fullscreen'})">
                           <i class="fa fa-paper-plane"></i> Enviar SUNAT</a></li>
                         @endif 
                         <li><a href="javascript:;" onclick="modal({route:'facturacionnotacredito/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Detalle</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'facturacionnotacredito/{{ $value->id }}/edit?view=comprobante',size:'modal-fullscreen'})">
                            <i class="fa fa-file-alt"></i> PDF Comprobante
                         </a></li>
                         <?php $archivo = $value->emisor_ruc.'-'.$value->notacredito_tipodocumento.'-'.$value->notacredito_serie.'-'.$value->notacredito_correlativo; ?>
                         <li><a href="{{url('public/sunat/produccion/notacredito/'.$archivo.'.xml')}}" target="_blank" download>
                            <i class="fa fa-file-alt"></i> XML Comprobante
                         </a></li>
                         <li><a href="{{url('public/sunat/produccion/notacredito/R-'.$archivo.'.zip')}}" target="_blank">
                            <i class="fa fa-file-alt"></i> CDR Comprobante
                         </a></li>
                         <li><a href="javascript:;" onclick="modal({route:'facturacionnotacredito/{{ $value->id }}/edit?view=correo'})">
                            <i class="fa fa-share-square"></i> Enviar Correo
                         </a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $facturacionnotacreditos->links('app.tablepagination', ['results' => $facturacionnotacreditos]) }}
        </div>
    </div>

</div>
@endsection