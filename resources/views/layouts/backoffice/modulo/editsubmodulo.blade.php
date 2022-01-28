@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<ol class="breadcrumb pull-right">
		<li class="breadcrumb-item">
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </li>
</ol>
<h1 class="page-header">EDITAR SUB-MÓDULO</h1>
<div class="panel">
<div class="panel-body">
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/modulo/{{ $modulo->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/modulo') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="editsubmodulo" id="view"/>
            <div class="row">
                <div class="col-md-6">
                  <label>Módulo *</label>
                  <select id="idmodulo" class="form-control">
                    @foreach($modulos as $value)
                    <option value="{{ $value->id }}" <?php echo $modulo->idmodulo==$value->id ? 'selected' : '' ?>>{{ $value->orden }} - {{ $value->nombre }}</option>
                    <?php
                    $submodulos = DB::table('modulo')
                      ->where('idmodulo', $value->id)
                      ->orderBy('orden','asc')
                      ->get();
                    ?>
                    @foreach($submodulos as $subvalue)
                    <option value="{{ $subvalue->id }}" <?php echo $modulo->idmodulo==$subvalue->id ? 'selected' : '' ?>>&nbsp;&nbsp;&nbsp;&nbsp;{{ $value->orden }}.{{ $subvalue->orden }} - {{ $subvalue->nombre }}</option>
                    @endforeach
                    @endforeach
                  </select>
                  <label>Nombre *</label>
                  <input type="text" value="{{ $modulo->nombre }}" id="nombre" class="form-control"/>
                  <label>Icono</label>
                  <input type="text" value="{{ $modulo->icono }}" id="icono" class="form-control"/>
                  <label>orden *</label>
                  <input type="number" id="orden" value="{{ $modulo->orden }}" class="form-control" class="form-control"/>
                </div>
                <div class="col-md-6">
                  <label>Vista</label>
                  <input type="text" value="{{ $modulo->vista }}" id="vista" class="form-control"/>
                  <label>Controlador</label>
                  <input type="text" value="{{ $modulo->controlador }}" id="controlador" class="form-control"/>
                  <label>Estado *</label>
                  <select id="idestado" class="form-control">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
                </div>
            </div>
    <button type="submit" class="btn btn-inverse">Guardar Cambios</button>
</form>  
</div> 
</div> 
@endsection
@section('subscripts')
<script>
$("#idmodulo").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$modulo->idmodulo}}).trigger("change");
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$modulo->idestado}}).trigger("change");
</script>
@endsection