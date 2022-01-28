<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/agencia',
                method: 'POST',
                data:{
                    view: 'registrar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/agencia') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Agencia</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">RUC *</label>
            <div class="col-sm-8">
                <input type="text" id="ruc" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre Comercial *</label>
            <div class="col-sm-8">
                <input type="text" id="nombrecomercial" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Razón Social *</label>
            <div class="col-sm-8">
                <input type="text" id="razonsocial" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Estado de Empresa *</label>
            <div class="col-sm-8">
                <select id="idestadoempresa" class="form-control">
                    <option></option>
                    <option value="1">Activo (Principal)</option>
                    <option value="2">Inactivo</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Estado *</label>
            <div class="col-sm-8">
                <select id="idestado" class="form-control">
                    <option></option>
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>
<script>
$("#idestadoempresa").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});
$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});
</script>