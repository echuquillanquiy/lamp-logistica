<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/productounidadmedida',
                method: 'POST',
                data:{
                    view: 'registrar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/productounidadmedida') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Unidad de medida</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" id="nombre" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número *</label>
            <div class="col-sm-8">
                <input type="text" id="numero" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Código *</label>
            <div class="col-sm-8">
                <input type="text" id="codigo" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>