@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacionguiaremision/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Guia de Remisión</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="90px">Origen</th>
                <th width="90px">Código</th>
                <th width="100px">Fecha de Registro</th>
                <th width="100px">Responsable</th>
                <th>Cliente Destinatario</th>
                <th width="85px">Motivo</th>
                <th width="85px">Serie-Correlativo</th>
                <th width="10px">Estado</th>
                <th width="10px">SUNAT</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>[
          '',
          'codigo',
          'date:fecharegistro',
          'responsable',
          'cliente',
          'motivo',
          '',
          'select:estado/0=Pendiente,1=Correcto,2=Observado,=Todo',
          '',
          ''],
                'search_url'=> url('backoffice/facturacionguiaremision')
            ])
            <tbody>
                @foreach($facturacionguiaremisiones as $value)
              <?php
                $codigo_modulo = '';
                $origen = '';
                $venta = DB::table('venta')->whereId($value->idventa)->limit(1)->first();
                if ($value->idventa != 0 && isset($venta)) {
                  $codigo_modulo = str_pad($venta->codigo, 8, "0", STR_PAD_LEFT);
                  $origen = 'Venta';
                }else if ($value->idfacturacionboletafactura != 0) {
                  $facturacion = DB::table('facturacionboletafactura')->whereId($value->idfacturacionboletafactura)->first();
                  $codigo_modulo =  $facturacion->venta_serie.' - '.$facturacion->venta_correlativo;
                  $origen = 'Facturación';
                }else {
                  $origen = 'Transferencia';
                  $transferencia = DB::table('facturacionboletafactura')->whereId($value->idtransferencia)->first();
                  $codigo_modulo = '';
                }
              ?>
                <tr>
                  <td>{{ $origen }}</td>
                  <td>{{ $codigo_modulo }}</td>
                  <td>{{ date_format(date_create($value->despacho_fechaemision), 'd/m/Y - h:i A') }}</td>
                  <td>{{ $value->responsable_nombre }}</td>
                  <td>{{ $value->despacho_destinatario_numerodocumento }} - {{ $value->despacho_destinatario_razonsocial }}</td>
                  <td>{{ $value->envio_descripciontraslado }}</td>
                  <td>{{ $value->guiaremision_serie }} - {{ $value->guiaremision_correlativo }}</td>
                  <td>
                    @if($value->idestadofacturacion==0)
                        <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                    @elseif($value->idestadofacturacion==1)
                        <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Correcto</span></div> 
                    @elseif($value->idestadofacturacion==2)
                        <div class="td-badge"><span class="badge badge-pill"><i class="fa fa-times"></i> Observado</span></div>
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
                          @if($value->idestadofacturacion==2 or $value->idestadofacturacion == 0)
                          <li><a href="javascript:;" onclick="modal({route:'facturacionguiaremision/{{ $value->id }}/edit?view=reenviarsunat',size:'modal-fullscreen'})">
                           <i class="fa fa-paper-plane"></i> Reenviar a la SUNAT
                         </a></li>
                          @endif
                          <li>
                            <a href="javascript:;" onclick="modal({route:'facturacionguiaremision/{{ $value->id }}/edit?view=comprobante',size:'modal-fullscreen'})">
                            <i class="fa fa-file-alt"></i> PDF Guia de Remisión </a>
                          </li>
                          <li>
                            <a href="javascript:;" onclick="modal({route:'facturacionguiaremision/{{ $value->id }}/edit?view=correo'})">
                            <i class="fa fa-share-square"></i> Enviar Correo</a>
                          </li>   
                           <li>
                            <a href="javascript:;" onclick="modal({route:'facturacionguiaremision/{{ $value->id }}/edit?view=detalle', size:'modal-fullscreen'})">
                            <i class="fa fa-info-circle"></i> Detalle</a>
                           </li>
    <!--                     <li><a href="javascript:;" onclick="modal({route:'facturacionguiaremision/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Editar
                         </a></li>
                         <li><a href="javascript:;" onclick="modal({route:'facturacionguiaremision/{{ $value->id }}/edit?view=comprobante',size:'modal-fullscreen'})">
                            <i class="fa fa-file-alt"></i> PDF Comprobante
                         </a></li>
                         
                          -->
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $facturacionguiaremisiones->links('app.tablepagination', ['results' => $facturacionguiaremisiones]) }}
        </div>
    </div>

</div>
@endsection