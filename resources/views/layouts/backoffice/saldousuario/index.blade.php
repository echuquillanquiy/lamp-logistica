@extends('layouts.backoffice.master')

@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <!--div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'saldousuario/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div-->
        <h4 class="panel-title">Saldo de Usuarios</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Usuario</th>
                <th>Fecha Registro</th>
                <th>Fecha Confirmacion</th>
                <th>Monto</th>
                <th>Motivo</th>
                <th width="150px">Estado</th>
                <!--th width="10px"></th-->
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['tiendanombre','nombre'],
                'search_url'=> url('backoffice/saldousuario')
            ])
            <tbody>
              @foreach($saldousuarios as $value)
                <tr>
                  <td>{{ $value->nombre }}</td>
                  <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A') }}</td>
                  <td>{{ date_format(date_create($value->fechaconfirmacion), 'd/m/Y - h:i A') }}</td>
                  <td>{{ $value->monedasimbolo }} {{ number_format($value->monto, 2, '.', '') }}</td>
                  <td>{{ $value->motivo }}</td>
                  <td>  
                      @if($value->idestado == 1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                      @elseif($value->idestado == 2)
                          <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                      @elseif($value->idestado == 3)
                          <div class="td-badge"><span class="badge badge-pill badge-danger"><i class="fa fa-ban"></i> Anulado</span></div>
                      @endif 
                  </td>
                  <!--td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                        @if($value->idestado == 1)
                           <li><a href="javascript:;" onclick="modal({route:'saldousuario/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                          <li><a href="javascript:;" onclick="modal({route:'saldousuario/{{ $value->id }}/edit?view=confirmar'})"><i class="fa fa-check-circle" aria-hidden="true"></i> Confirmar</a></li>
                          <li><a href="javascript:;" onclick="modal({route:'saldousuario/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                        @elseif($value->idestado == 2)
                          <li><a href="javascript:;" onclick="modal({route:'saldousuario/{{ $value->id }}/edit?view=detalle'})"><i class="fa fa-list-alt"></i> Detalle</a></li>  
                          <!--li><a href="javascript:;" onclick="modal({route:'saldousuario/{{ $value->id }}/edit?view=anular'})"><i class="fa fa-ban"></i> Anular</a></li-->  
                        @endif
                      </ul>
                    </div>
                  </td-->
                </tr>
              @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
 
