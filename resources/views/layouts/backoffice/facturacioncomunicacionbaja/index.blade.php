@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacioncomunicacionbaja/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Comunicación de Baja</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Fecha de Generación</th>
                <th>Tipo Documento</th>
                <th>Doc. Afectado</th>
                <th>DNI/RUC</th>
                <th>Cliente</th>
                <th>C. Baja Correlativo</th>
                <th>Motivo Anulación</th>
                <th width="100px">Estado</th>
                <th width="100px">SUNAT</th>
                <th width="10px"></th>
              </tr>
            </thead>
             @include('app.tablesearch',[
                'searchs'=>['date:fecharegistro','','','','','','motivo',
          'select:estado/0=Pendiente,1=Correcto,2=Observado,=Todo'],
                'search_url'=> url('backoffice/facturacioncomunicacionbaja')
            ])
            <tbody>
                @foreach( $facturacioncomunicacionbaja as $value )
                <tr>
                  <td>{{ $value->comunicacionbaja_fechageneracion }}</td>
                  <td>                    
                    @if($value->tipodocumento == '01')
                      FACTURA
                    @elseif($value->tipodocumento == '03')
                      BOLETA DE VENTA
                    @elseif($value->tipodocumento == '07')
                      NOTA DE CREDITO
                    @else          
                      {{ $value->tipodocumento }}
                    @endif
                  </td>
                  <td>{{ $value->serie }} - {{ $value->correlativo }}</td>
                  <td>{{ $value->factbol_cliente_numerodocumento }} {{ $value->notacred_cliente_numerodocumento }}</td>
                  <td>{{ $value->factbol_cliente_razonsocial }} {{ $value->notacred_cliente_razonsocial }}</td>
                  <td>{{ $value->comunicacionbaja_correlativo }}</td>
                  <td>{{ $value->motivo }}</td>
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
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Beta</span></div> 
                    @else
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Producción</span></div>
                    @endif  
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestadofacturacion == 2 or $value->idestadofacturacion == 0)
                          <li>
                            <a href="javascript:;" onclick="modal({route:'facturacioncomunicacionbaja/{{ $value->id }}/edit?view=reenviarsunat', size:'modal-fullscreen'})">
                            <i class="fa fa-paper-plane"></i> Reenviar a la SUNAT
                          </li>
                         @endif
                         <li><a href="javascript:;" onclick="modal({route:'facturacioncomunicacionbaja/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                           <i class="fa fa-list-alt"></i> Detalle</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $facturacioncomunicacionbaja->links('app.tablepagination', ['results' => $facturacioncomunicacionbaja]) }}
        </div>
    </div>
</div>
@endsection