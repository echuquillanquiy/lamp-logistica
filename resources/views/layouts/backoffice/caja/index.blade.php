@extends('layouts.backoffice.master')

@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'caja/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Cajas</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th>Tienda</th>
                <th>Nombre</th>
                <th>Soles</th>
                <th>Dolares</th>
                <th width="150px" >Estado</th>
                <th width="100px" ></th>
              </tr>
            </thead>
            @include('app.tablesearch',[
                'searchs'=>['tiendanombre','nombre'],
                'search_url'=> url('backoffice/caja')
            ])
            <tbody>
              @forelse ($cajas as $value)
                <?php
                $montocierresoles = DB::table('aperturacierre')
                    ->where('aperturacierre.idcaja',$value->id)
                    ->where('aperturacierre.idestado',5)
                    ->sum('montocierresoles');
                $montocierredolares = DB::table('aperturacierre')
                    ->where('aperturacierre.idcaja',$value->id)
                    ->where('aperturacierre.idestado',5)
                    ->sum('montocierredolares');
                $countaperturacierre = DB::table('aperturacierre')
                    ->where('aperturacierre.idcaja',$value->id)
                    ->count();
                ?>
                <tr>
                  <td>{{ $value->tiendanombre }}</td>
                  <td>{{ $value->nombre }}</td>
                  <td>{{$monedasoles->simbolo}} {{ number_format($montocierresoles, 2, '.', '') }}</td>
                  <td>{{$monedadolares->simbolo}} {{ number_format($montocierredolares, 2, '.', '') }}</td>
                  <td>{{ $value->idestado == 1 ? 'Activado' : 'Desactivado' }}</td>
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" onclick="modal({route:'caja/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li>
                        @if($countaperturacierre==0)
                        <li><a href="javascript:;" onclick="modal({route:'caja/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li>
                        @endif
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center">Nada que mostrar.</td>
                </tr>         
              @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
{{ $cajas->links('app.tablepagination', ['results' => $cajas]) }}
@endsection
 
