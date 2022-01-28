@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'trabajador/create?view=registrar'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Trabajadores</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <?php $idpermiso = usersmaster()->idpermiso ?>
        <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="110px">Persona</th>
                <th width="100px">RUC/DNI</th>
                <th>Cliente</th>
                @if($idpermiso==1 || $idpermiso==4)
                <th width="110px">Acceso</th>
                <th width="10px">Permisos</th>
                @endif 
                <th width="10px"></th>
              </tr>
            </thead>
            @if($idpermiso==1 || $idpermiso==4)
                @include('app.tablesearch',[
                    'searchs'=>['tipo','identificacion','cliente','','',''],
                    'search_url'=> url('backoffice/trabajador')
                ])
            @else
                @include('app.tablesearch',[
                    'searchs'=>['tipo','identificacion','cliente',''],
                    'search_url'=> url('backoffice/trabajador')
                ])
            @endif 
            <tbody>
                @foreach($usuarios as $value)
                <tr>
                  <td>{{ $value->tipopersonanombre }}</td>
                  <td>{{ $value->identificacion }}</td>
                  <td>
                    @if($value->idtipopersona==1)
                      {{ $value->apellidos }}, {{ $value->nombre }}
                    @elseif($value->idtipopersona==3)
                      {{ $value->apellidos }}, {{ $value->nombre }}
                    @else
                      {{ $value->nombre }}
                    @endif  
                  </td>
                  @if($idpermiso==1 || $idpermiso==4)
                  <td>
                    @if($value->idestado==1)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span></div>
                    @else
                        <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-times"></i> Desactivado</span></div>
                    @endif 
                  </td>
                  <td>
                            <?php 
                            $role_users = DB::table('role_user')
                                ->join('tienda','tienda.id','role_user.idtienda')
                                ->join('roles','roles.id','role_user.role_id')
                                ->join('users','users.id','role_user.user_id')
                                ->where('role_user.user_id',$value->id)
                                ->select(
                                  'role_user.*',
                                  'tienda.nombre as tiendanombre',
                                  'users.usuario as usersusuario',
                                  'users.clave as usersclave',
                                  'roles.description as rolesnombre'
                                )
                                ->orderBy('id','asc')->get(); 
                            ?>
                            @foreach($role_users as $valueusuario)
                                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> {{$valueusuario->tiendanombre}} - {{$valueusuario->rolesnombre}} ({{$valueusuario->usersusuario.' - '.$valueusuario->usersclave}})</span></div> 
                            @endforeach
                  </td> 
                  @endif
                  <td class="with-btn-group" nowrap>
                    <div class="btn-group">
                      <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                        Opción <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu pull-right">
<!--                           <li><a href="javascript:;" onclick="modal({route:'trabajador/{{ $value->id }}/edit?view=editar'})"><i class="fa fa-edit"></i> Editar</a></li> -->
                          @if($idpermiso==1 || $idpermiso==4)
                              <li><a href="javascript:;" onclick="modal({route:'trabajador/{{ $value->id }}/edit?view=permiso'})"><i class="fa fa-users"></i> Acceso</a></li>
<!--                               <li><a href="javascript:;" onclick="modal({route:'trabajador/{{ $value->id }}/edit?view=eliminar'})"><i class="fa fa-trash"></i> Eliminar</a></li> -->
                          @endif
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
{{ $usuarios->links('app.tablepagination', ['results' => $usuarios]) }}
@endsection