@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'compradevolucion/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Devolución de Compras</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="110px">Cod. Compra</th>
                <th width="110px">Código</th>
                <th width="100px">Responsable</th>
                <th>Proveedor</th>
                <th>Tipo de Pago - Descripción</th>
                <th width="100px">Devuelto</th>
                <th width="100px">Descontado</th>
                <th width="120px">Fecha de registro</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['codigocompra','codigo','responsable','proveedor','','','','','',''],
                'search_url'=> url('backoffice/compradevolucion')
            ])
            <tbody>
                @foreach($compradevolucions as $value)
                <?php 
              
                  $total = DB::table('compradevoluciondetalle')
                          ->where('idcompradevolucion',$value->id)
                          ->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
                ?>
                <tr>
                  <td>{{ str_pad($value->compracodigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->responsable }}</td>
                  <td>{{ $value->proveedor }}</td>
                    <td>
                      <?php echo tipopago_detalle('compradevolucion',$value->id)['detalle'] ?>
                    </td>
                  <td>
                    {{ $value->monedasimbolo }} {{ $value->montorecibido }}
                  </td>
                  <td>
                    {{ $value->monedasimbolo }} {{ number_format($total, 2, '.', '') }}
                  </td>
                  <td>{{ $value->fecharegistro }}</td>
                  <td>
                    @if($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                    @elseif($value->idestado==2)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                    @elseif($value->idestado==3)
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>   
                    @endif
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestado==1)
                              <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                                <i class="fa fa-edit"></i> Confirmar</a></li>
                              <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                                <i class="fa fa-trash"></i> Eliminar</a></li> 
                         @elseif($value->idestado==2)
                              <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                <i class="fa fa-list-alt"></i> Detalle</a></li>
<!--                              <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=proformacliente',size:'modal-fullscreen'})">
                                <i class="fa fa-file-alt"></i> PDF Cliente
                             </a></li> -->
                             <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=ticket'})">
                                <i class="fa fa-file-alt"></i> Ticket de Cliente
                             </a></li>
                            @if($value->idaperturacierre==$idapertura)
                               <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=anular',size:'modal-fullscreen'})">
                                  <i class="fa fa-ban"></i> Anular
                               </a></li>
                            @endif
                         @elseif($value->idestado==3)
                              <li><a href="javascript:;" onclick="modal({route:'compradevolucion/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                                <i class="fa fa-list-alt"></i> Detalle</a></li>
                         @endif
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $compradevolucions->links('app.tablepagination', ['results' => $compradevolucions]) }}
        </div>
    </div>

</div>
@endsection
@section('subscripts')
@endsection