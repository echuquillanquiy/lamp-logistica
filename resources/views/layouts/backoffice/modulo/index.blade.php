@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<ol class="breadcrumb pull-right">
		<li class="breadcrumb-item">
      <a class="btn btn-warning" href="{{ url('backoffice/modulo/create?view=registrar') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </li>
</ol>
<h1 class="page-header">Módulos</h1>
<div class="panel">
<div class="panel-body">
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="10px"></th>
            <th colspan="5">Nombre</th>
            <th>Vista/Controlador</th>
            <th>Roles</th>
            <th>Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>  
        <tbody>
          @foreach($modulos as $value)
          <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$value->id)->count();?>
          <?php $countmodulos = DB::table('modulo')->where('idmodulo',$value->id)->count();?>
            <tr>
              <td>{{ $value->orden }}</td>
              <td><i class="{{ $value->icono }}"></i></td>
              <td colspan="4">{{ $value->nombre }}</td>
              <td>{{ $value->vista }}<br>{{ $value->controlador }}</td>
              <td>{{ $countrolesmodulos }}</td>
              <td>
                @if($value->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td class="with-btn-group" nowrap>
                <div class="btn-group">
                  <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                    Opción <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="{{ url('backoffice/modulo/'.$value->id.'/edit?view=registrarsubmodulo') }}"><i class="fa fa-plus"></i> Registrar</a></li>
                    <li><a href="{{ url('backoffice/modulo/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    @if($countmodulos==0 && $countmodulos==0)
                    <li><a href="{{ url('backoffice/modulo/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
            <?php
            $submodulos = DB::table('modulo')
              ->where('idmodulo',$value->id)
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($submodulos as $subvalue)
            <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$subvalue->id)->count();?>
            <?php $countmodulos = DB::table('modulo')->where('idmodulo',$subvalue->id)->count();?>
            <tr>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}</td>
              <td width="10px"><i class="{{ $subvalue->icono }}"></i></td>
              <td colspan="3">{{ $subvalue->nombre }}</td>
              <td>{{ $subvalue->vista }}<br>{{ $subvalue->controlador }}</td>
              <td>{{ $countrolesmodulos }}</td>
              <td>
                @if($subvalue->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td class="with-btn-group" nowrap>
                <div class="btn-group">
                  <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                    Opción <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="{{ url('backoffice/modulo/'.$subvalue->id.'/edit?view=registrarsubsubmodulo') }}"><i class="fa fa-plus"></i> Registrar</a></li>
                    <li><a href="{{ url('backoffice/modulo/'.$subvalue->id.'/edit?view=editarsubmodulo') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    @if($countrolesmodulos==0 && $countmodulos==0)
                    <li><a href="{{ url('backoffice/modulo/'.$subvalue->id.'/edit?view=eliminarsubmodulo') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
            <?php
            $subsubmodulos = DB::table('modulo')
              ->where('idmodulo',$subvalue->id)
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($subsubmodulos as $subsubvalue)
            <?php $subcountrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$subsubvalue->id)->count();?>
            <?php $subcountmodulos = DB::table('modulo')->where('idmodulo',$subsubvalue->id)->count();?>
            <tr>
              <td></td>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}</td>
              <td width="10px"><i class="{{ $subsubvalue->icono }}"></i></td>
              <td colspan="2">{{ $subsubvalue->nombre }}</td>
              <td>{{ $subsubvalue->vista }}<br>{{ $subsubvalue->controlador }}</td>
              <td>{{ $countrolesmodulos }}</td>
              <td>
                @if($subsubvalue->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td class="with-btn-group" nowrap>
                <div class="btn-group">
                  <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                    Opción <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu pull-right">
                    @if($subsubvalue->vista=='' && $subsubvalue->controlador=='')
                    <li><a href="{{ url('backoffice/modulo/'.$subsubvalue->id.'/edit?view=registrarsistemamodulo') }}"><i class="fa fa-plus"></i> Registrar</a></li>
                    @endif
                    <li><a href="{{ url('backoffice/modulo/'.$subsubvalue->id.'/edit?view=editarsubsubmodulo') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    @if($subcountrolesmodulos==0 && $subcountmodulos==0)
                    <li><a href="{{ url('backoffice/modulo/'.$subsubvalue->id.'/edit?view=eliminarsubsubmodulo') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
            <?php
            $sistemamodulos = DB::table('modulo')
              ->where('idmodulo',$subsubvalue->id)
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($sistemamodulos as $sistemavalue)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}.{{ $sistemavalue->orden }}</td>
              <td width="10px"><i class="{{ $sistemavalue->icono }}"></i></td>
              <td>{{ $sistemavalue->nombre }}</td>
              <td>{{ $sistemavalue->vista }}<br>{{ $sistemavalue->controlador }}</td>
              <td>{{ $countrolesmodulos }}</td>
              <td>
                @if($sistemavalue->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td class="with-btn-group" nowrap>
                <div class="btn-group">
                  <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                    Opción <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="{{ url('backoffice/modulo/'.$sistemavalue->id.'/edit?view=editarsistemamodulo') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="{{ url('backoffice/modulo/'.$sistemavalue->id.'/edit?view=eliminarsistemamodulo') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                  </ul>
                </div>
              </td>
            </tr>
            @endforeach
            @endforeach
            @endforeach
          @endforeach
        </tbody>
    </table>
</div>  
</div>
</div>
@endsection