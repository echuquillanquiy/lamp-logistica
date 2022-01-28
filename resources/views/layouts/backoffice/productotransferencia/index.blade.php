@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
   <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'productotransferencia/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Transferencias de Productos</h4>
    </div>
    <div class="panel-body">
     <div class="table-responsive">
       <table class="table table-striped">
         <thead class="thead-dark">
            <tr>
              <th width="110px">Código</th>
              <th>Tienda Origen (Responsable)</th>
              <th>Tienda Destino (Responsable)</th>
              <th>Motivo</th>
              <th width="100px">Fecha Solicitud</th>
              <th width="100px">Fecha Envio</th>
              <th width="100px">Fecha Recepción</th>
              <th width="10px">Transferencia</th>
              <th width="10px">Estado</th>
              <th width="10px">Guia de Remision</th>
              <th width="10px"></th>
            </tr>
         </thead>
         <tbody>
            @include('app.tablesearch',[
                'searchs'=>['codigo','tiendaorigen','tiendadestino','motivo','date:fechasolicitud','date:fechaenvio','date:fecharecepcion','select:idestadotransferencia/1=Solicitud,2=Envio,3=Recepcionado,=Todo','select:idestado/1=En proceso,3=Pendiente,2=Confirmado,=Todo'],
                'search_url'=> url('backoffice/productotransferencia')
            ])
         <?php $idtienda = usersmaster()->idtienda; ?>
          @foreach($productotransferencias as $value)
           <?php
            $guia_remision = DB::table('facturacionguiaremision')->where('idtransferencia', $value->id)->first();
           ?>
            <tr>
              <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
              <td>{{ $value->tienda_origen_nombre }} {{ $value->idusersorigen!=0?'('.$value->user_origen_nombre.')':'' }}</td>
              <td>{{ $value->tienda_destino_nombre }} {{ $value->idusersdestino!=0?'('.$value->user_destino_nombre.')':'' }}</td>
              <td>{{ $value->motivo }}</td>
              <td>{{ ($value->idestadotransferencia==1 or $value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechasolicitud), 'd/m/Y - h:i A'):'---' }}</td>
              <td>{{ ($value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechaenvio), 'd/m/Y - h:i A'):'---' }}</td>
              <td>{{ $value->idestadotransferencia==3?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
              <td>
                    @if($value->idestadotransferencia==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Solicitud</span></div> 
                    @elseif($value->idestadotransferencia==2)
                        <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-share"></i> Envio</span></div>
                    @elseif($value->idestadotransferencia==3)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Recepcionado</span></div>
                    @endif 
              </td>
              <td>
                    @if($value->idestadotransferencia==1)
                        @if($value->id_tienda_destino==$idtienda)
                            @if($value->idestado==1)
                                <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> En Proceso</span></div> 
                            @elseif($value->idestado==2)
                                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                            @endif
                        @else
                            @if($value->idestado==1)
                            <div class="td-badge"><span class="badge badge-pill badge-secondary"><i class="fa fa-sync-alt"></i> En Proceso</span></div> 
                            @elseif($value->idestado==2)
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                            @endif
                        @endif
                    @elseif($value->idestadotransferencia==2)
                        @if($value->id_tienda_destino==$idtienda) 
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                        @else
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                        @endif
                    @elseif($value->idestadotransferencia==3)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                    @endif 
                        
              </td>
              <td>
                @if(!is_null($guia_remision))
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-truck" aria-hidden="true"></i> {{ $guia_remision->guiaremision_serie }} - {{ $guia_remision->guiaremision_correlativo }}</span></div> 
                @endif 
              </td>
              <td class="with-btn-group" nowrap>
                  <div class="btn-group">
                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                      Opción <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                    @if($value->idestadotransferencia==1)
                        @if($value->id_tienda_destino==$idtienda)
                            @if($value->idestado==1)  
                            @if($value->idusersdestino==Auth::user()->id)  
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=confirmar',size:'modal-fullscreen'})">
                                  <i class="fa fa-check"></i> Confirmar</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                                  <i class="fa fa-edit"></i> Editar</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                  <i class="fa fa-list-alt"></i> Detalle</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                                  <i class="fa fa-file-alt"></i> Ticket</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                                  <i class="fa fa-trash"></i> Eliminar</a></li> 
                            @else
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            @endif
                            @elseif($value->idestado==2)
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                          <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket 
                             </a></li>
                            @endif
                        @else
                            @if($value->idestado==1)
                            @if($value->idusersorigen==Auth::user()->id)  
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=confirmar',size:'modal-fullscreen'})">
                                  <i class="fa fa-check"></i> Confirmar</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                                  <i class="fa fa-edit"></i> Editar</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                  <i class="fa fa-list-alt"></i> Detalle</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                                  <i class="fa fa-file-alt"></i> Ticket</a></li>
                                <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                                  <i class="fa fa-trash"></i> Eliminar</a></li> 
                            @else
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle11</a></li>
                            @endif
                            @elseif($value->idestado==2)
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                              <i class="fa fa-share"></i> Enviar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=rechazar',size:'modal-fullscreen'})">
                              <i class="fa fa-ban"></i> Rechazar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                              <i class="fa fa-file-alt"></i> Ticket</a></li>
                            @endif
                        @endif
                    @elseif($value->idestadotransferencia==2)
                        @if($value->id_tienda_destino==$idtienda)
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                              <i class="fa fa-check"></i> Recepcionar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=rechazar',size:'modal-fullscreen'})">
                              <i class="fa fa-ban"></i> Rechazar</a></li>
<!--                             @if(is_null($guia_remision))
                              <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=guiaremision',size:'modal-fullscreen'})">
                              <i class="fa fa-truck"></i> Guia Remision</a></li> 
                            @endif -->
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                              <i class="fa fa-file-alt"></i> Ticket</a></li>
                        @else
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                              <i class="fa fa-file-alt"></i> Ticket</a></li>
                            
                        @endif
                    @elseif($value->idestadotransferencia==3)
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=ticket'})">
                              <i class="fa fa-file-alt"></i> Ticket</a></li>
<!--                             @if(is_null($guia_remision))
                              <li><a href="javascript:;" onclick="modal({route:'productotransferencia/{{ $value->id }}/edit?view=guiaremision',size:'modal-fullscreen'})">
                              <i class="fa fa-truck"></i> Guia Remision</a></li> 
                            @endif -->
                    @endif 
                         
                    </ul>
                  </div>
              </td>
            </tr>
          @endforeach
         </tbody>
       </table>
        {{ $productotransferencias->links('app.tablepagination', ['results' => $productotransferencias]) }}
     </div>
    </div>
</div>
@endsection