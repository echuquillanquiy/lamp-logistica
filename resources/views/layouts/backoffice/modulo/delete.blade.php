@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<ol class="breadcrumb pull-right">
		<li class="breadcrumb-item">
      <a class="btn btn-success" href="{{ url('backoffice/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </li>
</ol>
<h1 class="page-header">ELIMINAR MÓDULO</h1>
<div class="panel">
<div class="panel-body">
    <form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/modulo/{{ $modulo->id }}',
                                            method: 'DELETE'
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/modulo') }}';                                                                            
                                            }                                                                                                                    
                                        },this)" enctype="multipart/form-data">
      <input type="hidden" value="deletemodulo" id="view"/>
      <div class="alert alert-warning">¿Esta Seguro de Eliminar EL modulo <b>"{{ $modulo->nombre }}'</b>?</div>
      <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>
    </form> 
</div>
</div>
@endsection