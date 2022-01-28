@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'seguridadips/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Seguridad de IPS</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Nombre</th>
                <th>IP</th>
                <th width="10px"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($seguridadips as $value)
                <tr>
                  <td>{{ $value->nombre }}</td>
                  <td>{{ $value->ip  }}</td>
                   <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" onclick="modal({route:'seguridadips/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                        <li><a href="javascript:;" onclick="modal({route:'seguridadips/{{ $value->id }}/edit?view=detalle'})"><i class="fa fa-list-alt"></i> Detalle</a></li>
                        <li><a href="javascript:;" onclick="modal({route:'seguridadips/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
        </table>
        {{ $seguridadips->links('app.tablepagination', ['results' => $seguridadips]) }}
        </div>
    </div>
</div>
@endsection
