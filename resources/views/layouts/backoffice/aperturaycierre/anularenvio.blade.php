<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/aperturaycierre/{{ $aperturacierre->id }}',
                method: 'PUT',
                data:{
                    view: 'anularenvio'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/aperturaycierre') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Rechazar Apertura de Caja</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Caja</label>
            <div class="col-sm-8">
                <select id="idcaja" disabled>
                          <option></option>
                          @foreach($cajas as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto a asignar S/.</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{ $aperturacierre->montoasignarsoles }}" id="montoasignarsoles" step="0.01" min="0" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto a asignar $</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{ $aperturacierre->montoasignardolares }}" id="montoasignardolares" step="0.01" min="0" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Persona responsable</label>
            <div class="col-sm-8">
                <select id="idusersresponsable" class="form-control" disabled>
                          <option>{{$aperturacierre->usersresponsablenombre}}</option>
                      </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Persona a asignar</label>
            <div class="col-sm-8">
                <select id="idusers" class="form-control" disabled>
                    <option>{{$aperturacierre->usersrecepcionnombre}}</option>
                </select>
            </div>
        </div>
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de rechazar?
				</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Rechazar</button>
    </div>
</form>  
</div>
<script>
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$aperturacierre->idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
});

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
});
</script>