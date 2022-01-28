<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/agencia/{{$agencia->id}}',
                method: 'DELETE',
                data:{
                    view: 'eliminar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/agencia') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Agencia</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">RUC *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->ruc}}" id="ruc" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre Comercial *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->nombrecomercial}}" id="nombrecomercial" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Razón Social *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->razonsocial}}" id="razonsocial" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Estado de Empresa *</label>
            <div class="col-sm-8">
                <select id="idestado" class="form-control" disabled>
                    <option></option>
                    <option value="1">Activo (Principal)</option>
                    <option value="2">Inactivo</option>
                </select>
            </div>
        </div>
          <div class="alert alert-warning">¿Esta seguro de eliminar?</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Eliminar</button>
    </div>
</form>  
</div>
<script>
$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$agencia->idestado}}).trigger("change");
</script>