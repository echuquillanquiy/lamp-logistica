@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-warning"  onclick="modal({route:'bancocuentabancaria/create'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Cuentas Bancarias</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Banco</th>
                <th>Nombre de cuenta</th>
                <th>Número de cuenta</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['banco','nombre','numerocuenta'],
                'search_url'=> url('backoffice/bancocuentabancaria')
            ])
            <tbody>
              @foreach($bancocuentabancarias as $value)
                <tr>
                  <td>{{ $value->banco }}</td>
                  <td>{{ $value->nombre }}</td>
                  <td>{{ $value->numerocuenta }}</td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" onclick="modal({route:'bancocuentabancaria/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                        <li><a href="javascript:;" onclick="modal({route:'bancocuentabancaria/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
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
{{ $bancocuentabancarias->links('app.tablepagination', ['results' => $bancocuentabancarias]) }}
@endsection
