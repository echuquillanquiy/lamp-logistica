<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/inicio/{{ $usuario->id }}',
            method: 'PUT',
            data:{
                view: 'editpassword'
            } 
        },
        function(resultado){    
            location.reload(); 
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Cambiar Contraseña</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-5 col-form-label">Contraseña Actual *</label>
            <div class="col-sm-7">
                <input type="password" value="" id="antpassword" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-5 col-form-label">Nueva Contraseña *</label>
            <div class="col-sm-7">
                <input type="password" value="" id="password" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-5 col-form-label">Confirmar Nueva Contraseña *</label>
            <div class="col-sm-7">
                <input type="password" value="" id="password_confirmation" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>