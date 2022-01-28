@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cobranzacredito/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Cobranza de Creditos</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped" id="tabla-cobranzacredito">
              <thead class="thead-dark">
                <tr>
                  <th>Codigo</th>
                  <th>Responsable</th>
                  <th width="100px">Cod. Venta</th>
                  <th>Tipo de pago - Descripción</th>
                  <th width="80px">Total</th>
                  <th width="100px">Fecha de confirmación</th>
                  <th width="10px">Estado</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              @include('app.tablesearch',[
                  'searchs'=>['','responsable','ventacodigo','','','date:fechaconfirmacion','',''],
                  'search_url'=> url('backoffice/cobranzacredito')
              ])
              <tbody>
                  @foreach($cobranzacreditos as $value)
                  <tr>
                    <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{$value->responsablenombre}}</td>
                    <td>{{ str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>
                      <?php echo tipopago_detalle('cobranzacredito',$value->id)['detalle'] ?>
                    </td>
                    <td>{{$value->monedasimbolo}} {{$value->monto}}</td>
                    <td>{{$value->fechaconfirmacion!=''?$value->fechaconfirmacion:'---'}}</td>
                    <td>
                      @if($value->idestado==2)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                      @elseif($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div>   
                      @elseif($value->idestado==3)
                        <div class="td-badge"><span class="badge badge-pill badge-danger"><i class="fas fa-ban"></i> Anulado</span></div> 
                      @endif
                    </td>
                    <td class="with-btn-group" nowrap>
                      <div class="btn-group">
                        <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                          Opción <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                          @if($value->idestado==3)
                            <li><a href="javascript:;" onclick="modal({route:'cobranzacredito/{{ $value->id }}/edit?view=detalle'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                          @elseif($value->idestado==2)
                            <li><a href="javascript:;" onclick="modal({route:'cobranzacredito/{{ $value->id }}/edit?view=detalle'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                            @if($value->idaperturacierre==$idapertura)
                              <li><a href="javascript:;" onclick="modal({route:'cobranzacredito/{{ $value->id }}/edit?view=anular'})"><i class="fas fa-ban"></i> Anular </a></li>
                            @endif
                          @elseif($value->idestado==1)
                            <li><a href="javascript:;" onclick="modal({route:'cobranzacredito/{{ $value->id }}/edit?view=confirmar'})"><i class="fa fa-check"></i> Confirmar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'cobranzacredito/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'cobranzacredito/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                          @endif
                        </ul>
                      </div>
                    </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>
          {{ $cobranzacreditos->links('app.tablepagination', ['results' => $cobranzacreditos]) }}
    </div>
</div>
</div>
@endsection
