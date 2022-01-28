@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
   <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'productomovimiento/create?view=registrar',size:'modal-fullscreen'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Movimiento de Productos</h4>
    </div>
    <div class="panel-body">
     <div class="table-responsive">
       <table class="table table-striped">
         <thead class="thead-dark">
            <tr>
              <th width="110px">Código</th>
              <th>Movimiento</th>
              <th>Motivo</th>
              <th>Tienda</th>
              <th>Responsable</th>
              <th>Fecha Registro</th>
              <th>Fecha Confirmación</th>
              <th width="10px">Estado</th>
              <th width="10px"></th>
            </tr>
         </thead>
            @include('app.tablesearch',[
                'searchs'=>[
          'codigo',
          'select:estadomovimiento/1=Ingreso,2=Salida,=Todo',
          'motivo',
          'tiendanombre',
          'responsable',
          'date:fecharegistro',
          'date:fecharecepcion',
         'select:estado/1=Pendiente,2=Recepcionado,=Todo'
          ],
                'search_url'=> url('backoffice/productomovimiento')
            ])
         <tbody>
         <?php $idtienda = usersmaster()->idtienda; ?>
          @foreach($productomovimientos as $value)
            <tr>
              <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
              <td>
                    @if($value->idestadomovimiento==1)
                        INGRESO
                    @elseif($value->idestadomovimiento==2)
                        SALIDA
                    @endif 
              </td>
              <td>{{ $value->motivo }}</td>
              <td>{{ $value->tienda_nombre }}</td>
              <td>{{ $value->users_nombre }}</td>
              <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A') }}</td>
              <td>{{ $value->idestado==2?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
              <td>
                    @if($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                    @elseif($value->idestado==2)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Recepcionado</span></div>
                    @endif 
              </td>
              <td class="with-btn-group" nowrap>
                  <div class="btn-group">
                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                      Opción <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                    @if($value->idestado==1)
                            <li><a href="javascript:;" onclick="modal({route:'productomovimiento/{{ $value->id }}/edit?view=editar',size:'modal-fullscreen'})">
                              <i class="fa fa-edit"></i> Confirmar</a></li>
                            <li><a href="javascript:;" onclick="modal({route:'productomovimiento/{{ $value->id }}/edit?view=eliminar',size:'modal-fullscreen'})">
                              <i class="fa fa-trash"></i> Eliminar</a></li> 
                    @elseif($value->idestado==2)
                            <li><a href="javascript:;" onclick="modal({route:'productomovimiento/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                      
                    @endif 
                    </ul>
                  </div>
              </td>
            </tr>
          @endforeach
         </tbody>
       </table>
        {{ $productomovimientos->links('app.tablepagination', ['results' => $productomovimientos]) }}
     </div>
    </div>
</div>
@endsection