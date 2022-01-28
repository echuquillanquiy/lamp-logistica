@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-warning btn-xs" href="{{ url('backoffice/productoingresosalida/create') }}"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Ingreso y Salida de Productos</h4>
    </div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Tienda</th>
              <th>Fecha Registro</th>
              <th>Producto</th>
              <th>Tipo Movimiento</th>
              <th>Cantidad Ingreso</th>
              <th>Cantidad Salida</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = $ingresosalidaproducto->firstItem(); ?>
            @foreach($ingresosalidaproducto as $value)
              <tr>
                  <td width="10px">{{ $i }}</td>
                  <td scope="col">{{ $value->tienda_nombre }}</td>
                  <td scope="col">{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A') }}</td>
                  <td scope="col">{{ $value->producto_nombre }}</td>
                  <td scope="col">
                      @if($value->cantidad_ingreso == 0)
                          Salida
                      @else
                          Ingreso
                      @endif
                  </td>
                  <td scope="col" class="{{ $value->cantidad_ingreso != 0 ? 'bg-success-bright' : '' }}">{{ $value->cantidad_ingreso }}</td>
                  <td scope="col" class="{{ $value->cantidad_salida != 0 ? 'bg-danger-bright' : '' }}">{{ $value->cantidad_salida }}</td>
                  <td width="10px"></td>
              </tr>
              <?php $i++; ?>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
</div>
{{ $ingresosalidaproducto->links('app.tablepagination', ['results' => $ingresosalidaproducto]) }}
@endsection
