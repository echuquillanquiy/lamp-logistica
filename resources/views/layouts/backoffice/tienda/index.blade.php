@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'tienda/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Tiendas</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Serie</th>
                <th width="100px">Estado</th>
                <th width="10px">Imagen</th>
                <th width="10px"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($tiendas as $value)
                <tr>
                  <td>{{$value->nombre}}</td>
                  <td>{{$value->direccion}}</td>
                  <td>{{ str_pad($value->facturador_serie, 3, "0", STR_PAD_LEFT) }}</td>
                  <td>
                    @if($value->facturador_idestado==1)
                    BETA
                    @ELSE
                    PRODUCCIÓN
                    @endif
                  </td>
                  <td class="with-img">
                    @if($value->imagen!='')
                    <img src="{{url('public/admin/tienda/'.$value->imagen)}}" height="30px">
                    @endif
                  </td>
                      <td class="with-btn-group" nowrap>
                        <div class="btn-group">
                          <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                            Opción <span class="caret"></span>
                          </a>
                          <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:;" onclick="modal({route:'tienda/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                            <!--li><a href="javascript:;" onclick="modal({route:'tienda/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li-->
                          </ul>
                        </div>
                      </td>
                </tr>
              @endforeach 
            </tbody>
        </table>
        {{ $tiendas->links('app.tablepagination', ['results' => $tiendas]) }}  
        </div>
    </div>
</div>
@endsection
 
