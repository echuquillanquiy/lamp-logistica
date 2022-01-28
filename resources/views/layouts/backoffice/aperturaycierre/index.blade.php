@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-warning"  onclick="modal({route:'aperturaycierre/create'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Aperturas y Cierres</h4>
    </div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Tienda</th>
                <th>Caja</th>
                <th>Responsable</th>
                <th>Recepción</th>
                <th>Apertura</th>
                <th>Cierre</th>
                <th>Fecha de Apertura</th>
                <th>Fecha de Cierre</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['tiendanombre','cajanombre','usersresponsable','usersrecepcion'],
                'search_url'=> url('backoffice/aperturaycierre')
            ])
            <tbody>
                <?php 
                $iduser = Auth::user()->id;
                ?>
                @foreach($aperturacierres as $value)
                <tr>
                  <td>{{$value->tiendanombre}}</td>
                  <td>{{$value->cajanombre}}</td>
                  <td>{{$value->usersresponsablenombre}}</td>
                  <td>{{$value->usersrecepcionnombre}}</td>
                  <td>
                    {{$monedasoles->simbolo}} {{$value->montoasignarsoles}} - {{$monedadolares->simbolo}} {{$value->montoasignardolares}}</td>
                  <td>
                    @if($value->fechacierreconfirmacion!='')
                        {{$monedasoles->simbolo}} {{$value->montocierresoles}} - {{$monedadolares->simbolo}} {{$value->montocierredolares}}
                    @else
                        ---
                    @endif
                  </td>
                  <td>{{$value->fechaconfirmacion!=''?$value->fechaconfirmacion:'---'}}</td>
                  <td>{{$value->fechacierreconfirmacion!=''?$value->fechacierreconfirmacion:'---'}}</td>
                  <td>
                    @if($value->idestado==1)
                      <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Apertura En Proceso</span></div>
                    @elseif($value->idestado==2)
                      <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></div> 
                    @elseif($value->idestado==3)
                      <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Aperturado</span></div>
                    @elseif($value->idestado==4)
                      <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Cierre Pendiente</span></div>
                    @elseif($value->idestado==5)
                      <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-check"></i> Caja Cerrada</span></div>
                    @endif
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestado==1 && $value->idusersresponsable==$iduser)
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Confirmar</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                         @elseif($value->idestado==2 && $value->idusersresponsable==$iduser)
                         @if($value->idtienda==$usersmaster->idtienda)
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=confirmarrecepcion'})"><i class="fa fa-check"></i> Recepcionar</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=anularenvio'})"><i class="fa fa-ban"></i> Rechazar</a></li>
                         @endif
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                         @elseif($value->idestado==2 && $value->idusersrecepcion==$iduser)
                         @if($value->idtienda==$usersmaster->idtienda)
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=confirmarrecepcion'})"><i class="fa fa-check"></i> Recepcionar</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=anularenvio'})"><i class="fa fa-ban"></i> Rechazar</a></li>
                         @endif
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=detalleapertura'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                         @elseif($value->idestado==3 && ($value->idusersresponsable==$iduser || $value->idusersrecepcion==$iduser))
                         @if($value->idtienda==$usersmaster->idtienda)
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=confirmarcierre'})"><i class="fa fa-check"></i> Cerrar Caja</a></li>
                         @endif
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=detalleapertura'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                         @elseif($value->idestado==4 && $value->idusersresponsable==$iduser)
                         @if($value->idtienda==$usersmaster->idtienda)
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=confirmarrecepcioncierre'})"><i class="fa fa-check"></i> Recepcionar</a></li>
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=anularenviocierre'})"><i class="fa fa-ban"></i> Rechazar</a></li>
                         @endif
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=detallecierre'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                         <li>
                            <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=pdfdetalle',size:'modal-fullscreen'})">
                                <i class="fa fa-file-alt"></i> PDF Detalle
                            </a>
                         </li>
                         @elseif($value->idestado==5 && $value->idusersresponsable==$iduser)
                         <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=detallecierre'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                         <li>
                            <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=pdfdetalle',size:'modal-fullscreen'})">
                                <i class="fa fa-file-alt"></i> PDF Detalle
                            </a>
                         </li>
                         @else
                            @if(usersmaster()->id==1)
                            <li><a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=detallecierre'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                         <li>
                            <a href="javascript:;" onclick="modal({route:'aperturaycierre/{{ $value->id }}/edit?view=pdfdetalle',size:'modal-fullscreen'})">
                                <i class="fa fa-file-alt"></i> PDF Detalle
                            </a>
                         </li>
                            @else
                            <li style="padding-left: 10px;padding-right: 10px;color: #ef0103;font-weight: bold;">Verifique el Usuario y la tienda!!.</li>
                            @endif
                         
                         @endif
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
      </div>
    </div>
</div>
{{ $aperturacierres->links('app.tablepagination', ['results' => $aperturacierres]) }}
@endsection
