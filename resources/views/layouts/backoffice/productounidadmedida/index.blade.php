@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'productounidadmedida/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Unidades de medida</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Nombre</th>
                <th>Código</th>
                <th>Uso</th>
                <th width="10px"></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['nombre','codigo'],
                'search_url'=> url('backoffice/productounidadmedida')
            ])
            <tbody>
              @foreach($productounidadmedidas as $value)
              <?php $countproductounidadmedidas = DB::table('producto')->where('idproductounidadmedida',$value->id)->count();?>
                <tr>
                  <td>{{ $value->nombre }}</td>
                  <td>{{ $value->codigo }}</td>
                  <td>{{ $countproductounidadmedidas }}</td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" onclick="modal({route:'productounidadmedida/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                        @if($countproductounidadmedidas==0)
                        <li><a href="javascript:;" onclick="modal({route:'productounidadmedida/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                        @endif
                      </ul>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
        </table>
          {{ $productounidadmedidas->links('app.tablepagination', ['results' => $productounidadmedidas]) }}
        </div>
    </div>
</div>

@endsection
