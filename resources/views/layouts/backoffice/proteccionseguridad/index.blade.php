@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Proteccion y Seguridad</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Tienda</th>
                <th>Usuario</th>
                <th>Detalle</th>
                <th>Fecha de Registro</th>
                <th>IP Address</th>
                <th>Navegador</th>
                <th>Ubicación</th>
                <!--th>url</th-->
              </tr>
            </thead>
            <tbody>
             @foreach($proteccionseguridad as $value)
                <tr>
                  <td>{{ $value->tienda }}</td>
                  <td>{{ $value->usuario }}</td>
                  <td>{{ $value->motivo }}</td>
                  <td>{{ $value->fecharegistro }}</td>
                  <td>{{ $value->ipaddress }}</td>
                  <td>{{ $value->navegador }}</td>
                  <td>{{ $value->ubicacion }}</td>
                  <!--td>{{ $value->url }}</td-->
                </tr>
              @endforeach
            </tbody>
        </table>
         {{ $proteccionseguridad->links('app.tablepagination', ['results' => $proteccionseguridad]) }}
        </div>
    </div>
</div>
@endsection
