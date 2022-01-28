<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/agencia/{{$agencia->id}}',
                method: 'PUT',
                data:{
                    view: 'editar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/agencia') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Agencia</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">RUC *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->ruc}}" id="ruc" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre Comercial *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->nombrecomercial}}" id="nombrecomercial" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Razón Social *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->razonsocial}}" id="razonsocial" class="form-control"/>
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
        <h5 style="text-align: center;
    background-color: #348fe2;
    padding: 8px;
    color: #f8f9fa;">Facturador SUNAT</h5>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Usuario Sol *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->sunat_usuario}}" id="sunat_usuario" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Clave Sol *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->sunat_clave}}" id="sunat_clave" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Ruta de Certificado *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$agencia->sunat_certificado}}" id="sunat_certificado" class="form-control"/>
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
}).val({{$agencia->idestadoempresa}}).trigger("change");
$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$agencia->idestado}}).trigger("change");
</script>