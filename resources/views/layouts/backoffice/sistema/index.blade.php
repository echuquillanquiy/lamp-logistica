@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Configuración de Sistema</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th width="10px">Icono</th>
                <th width="10px"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($sistema as $value)
                <tr>
                  <td>{{$value->nombre}}</td>
                  <td>{{$value->descripcion}}</td>
                  <td class="with-img">
                    @if($value->imagenicono!='')
                    <img src="{{url('public/admin/sistema/'.$value->imagenicono)}}" height="30px">
                    @endif
                  </td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" onclick="modal({route:'sistema/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
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
@endsection
 
