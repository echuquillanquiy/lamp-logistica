@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'agencia/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Agencias</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="10px">RUC</th>
                <th>Nombre Comercial</th>
                <th>Razón Social</th>
                <th>Usuario SOL</th>
                <th>Clave SOL</th>
                <th>Certificado</th>
                <th width="10px">Agencia</th>
                <th width="10px">Estado</th>
                <th width="10px"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($agencias as $value)
                <tr>
                  <td>{{$value->ruc}}</td>
                  <td>{{$value->nombrecomercial}}</td>
                  <td>{{$value->razonsocial}}</td>
                  <td>{{$value->sunat_usuario}}</td>
                  <td>{{$value->sunat_clave}}</td>
                  <td>{{$value->sunat_certificado}}</td>
                  <td>
                    @if($value->idestadoempresa==1)
                    Activo (Principal)
                    @else
                    Inactivo
                    @endif
                  </td>
                  <td>
                    @if($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span></div>
                    @else
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-times"></i> Desactivado</span></div>
                    @endif 
                  </td>
                      <td class="with-btn-group" nowrap>
                        <div class="btn-group">
                          <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                            Opción <span class="caret"></span>
                          </a>
                          <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:;" onclick="modal({route:'agencia/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'agencia/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                          </ul>
                        </div>
                      </td>
                </tr>
              @endforeach 
            </tbody>
        </table>
        {{ $agencias->links('app.tablepagination', ['results' => $agencias]) }}
        </div>
    </div>
</div>
@endsection
