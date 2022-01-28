@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<ol class="breadcrumb pull-right">
		<li class="breadcrumb-item">
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </li>
</ol>
<h1 class="page-header">REGISTRAR SUB-MÓDULO</h1>
<div class="panel">
<div class="panel-body">
<form class="js-validation-signin px-30" action="javascript:;" 
                                         onsubmit="callback({
                                                    route: 'backoffice/modulo',
                                                    method: 'POST',
                                                    data:{
                                                    	'idmodulo' : {{ $modulo->id }}
                                                    }
                                                },
                                                function(resultado){
                                                  if (resultado.resultado == 'CORRECTO') {
                                                    location.href = '{{ url('backoffice/modulo') }}';                                                                            
                                                  }
                                                },this)">
 
  <input type="hidden" value="createsubsubmodulo" id="view"/>
            <div class="row">
                <div class="col-md-6">
                  <label>Nombre *</label>
                  <input type="text" id="nombre" class="form-control"/>
                  <label>Icono</label>
                  <input type="text" id="icono" class="form-control"/>
                  <label>orden *</label>
                  <input type="number" id="orden" class="form-control"/>
                </div>
                <div class="col-md-6">
                  <label>Vista</label>
                  <input type="text" id="vista"/>
                  <label>Controlador</label>
                  <input type="text" id="controlador"/>
                  <label>Estado *</label>
                  <select id="idestado" class="form-control">
                      <option value="1" selected>Activado</option>
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
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
});
</script>
@endsection