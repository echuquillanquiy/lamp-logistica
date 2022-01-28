@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacionresumen/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Resumenes Diarios</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="150px">Fecha de Registro</th>
                <th width="150px">Tienda</th>
                <th>Responsable</th>
                <th width="170px">Resumen Correlativo</th>
                <th width="100px">Estado</th>
                <th width="100px">SUNAT</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['date:fecharegistro','','responsable','',
          'select:estado/0=Pendiente,1=Correcto,2=Observado,=Todo',''],
                'search_url'=> url('backoffice/facturacionresumen')
            ])
            <tbody>
              @foreach($facturacionresumen as $value)
                <?php
                  $tienda = DB::table('tienda')->where('id', $value->idtienda)->first();
                  $responsable = DB::table('users')->where('id', $value->idusuarioresponsable)->first();
          
                  $tiendanombre = '';
                  if($tienda!=''){
                      $tiendanombre = $tienda->nombre;
                  }
                  $responsablenombre = '';
                  if($responsable!=''){
                      $responsablenombre = $responsable->nombre;
                  }
                ?>
                <tr>
                  <td>{{ date_format(date_create($value->resumen_fechageneracion), 'd/m/Y - h:i A') }}</td>
                  <td>{{ $tiendanombre }}</td>
                  <td>{{ $responsablenombre }}</td>
                  <td>{{ $value->resumen_correlativo }}</td>
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
                         @if($value->idestadofacturacion == 2)
                            <li>
                              <a href="javascript:;" onclick="modal({route:'facturacionresumen/{{ $value->id }}/edit?view=reenviarsunat', size:'modal-fullscreen'})">
                              <i class="fa fa-retweet"></i> Reenviar Sunat</a>
                            </li>
                         @endif
                         <li>
                            <a href="javascript:;" onclick="modal({route:'facturacionresumen/{{ $value->id }}/edit?view=detalle', size:'modal-fullscreen'})">
                            <i class="fa fa-info-circle"></i> Detalle</a>
                          </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
        </table>
        {{ $facturacionresumen->links('app.tablepagination', ['results' => $facturacionresumen]) }}
        </div>
    </div>

</div>
@endsection