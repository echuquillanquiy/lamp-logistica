@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cotizacion/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Cotizaciónes</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="110px">Código</th>
                <th width="120px">Fecha de registro</th>
                <th width="100px">Vendedor</th>
                <th>Cliente</th>
                <th>Total</th>
                <th width="10px">Pago</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['codigo','date:fecharegistro','vendedor','cliente'],
                'search_url'=> url('backoffice/cotizacion')
            ])
            <tbody>
                @foreach($ventas as $value)
                <tr>
                  <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                  <td>{{ $value->fecharegistro }}</td>
                  <td>{{ $value->nombreusuariovendedor }}</td>
                  <td>{{ $value->cliente }}</td>
                  <td>
                    <?php $montototal = DB::table('ventadetalle')->where('idventa',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)')); ?>
                    {{ $value->monedasimbolo }} {{ number_format($montototal, 2, '.', '') }}
                  </td>
                  <td>{{ $value->nombreFormapago }}</td>
                  <td>
                    @if($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                    @else
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                    @endif  
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                         @if($value->idestado==1)
                         <li><a href="javascript:;" onclick="modal({route:'cotizacion/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                            <i class="fa fa-edit"></i> Editar
                         </a></li>
                         <li><a href="javascript:;" onclick="modal({route:'cotizacion/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                            <i class="fa fa-trash"></i> Eliminar
                         </a></li>
                         @else
                         <li><a href="javascript:;" onclick="modal({route:'cotizacion/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                            <i class="fa fa-edit"></i> Editar
                         </a></li>
                         <li><a href="javascript:;" onclick="modal({route:'cotizacion/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                            <i class="fa fa-trash"></i> Eliminar
                         </a></li>
                         @endif 
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $ventas->links('app.tablepagination', ['results' => $ventas]) }}
        </div>
    </div>

</div>
@endsection