@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/aperturaycierre') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Confirmar Apertura de Caja</h4>
    </div>
    <div class="panel-body">
        <form class="js-validation-signin px-30" 
            action="javascript:;" 
            onsubmit="callback({
              route: 'backoffice/aperturaycierre/{{ $aperturacierre->id }}',
              method: 'PUT',
              data:{
                  view: 'confirmarenvio'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/aperturaycierre') }}';                                                                            
          },this)">
          <div class="profile-edit-container">
              <div class="custom-form">
                <div class="row">
                   <div class="col-md-6">
                      <label>Caja</label>
                      <select class="form-control" id="idcaja" disabled>
                          <option></option>
                          @foreach($cajas as $value)
                          <option value="{{ $value->id }}">{{ $value->tiendanombre }} - {{ $value->nombre }}</option>
                          @endforeach
                      </select>
                      <label>Monto asignado</label>
                      <input class="form-control" type="number" value="{{ $aperturacierre->montoasignar }}" id="montoasignar" disabled/>
                   </div>
                   <div class="col-md-6">
                      <label>Persona responsable *</label>
                      <select class="form-control" id="idusersresponsable" disabled>
                          <option></option>
                          @foreach($users as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
                      <label>Persona asignado</label>
                      <select class="form-control" id="idusers" disabled>
                          <option></option>
                          @foreach($users as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
                   </div>
                 </div>
              </div>
          </div>
          <div class="alert alert-warning">¡Esta seguro de Confirmar!</div>
          <div class="profile-edit-container">
              <div class="custom-form">
                  <button type="submit" class="btn  big-btn btn-danger  color-bg flat-btn">Confirmar</button>
              </div>
          </div> 
      </form> 
    </div>
</div>

                            
@endsection

@section('subscripts')
<script>
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$aperturacierre->idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$aperturacierre->idusersrecepcion}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
}).val({{$aperturacierre->idusersresponsable}}).trigger("change");

</script>
@endsection