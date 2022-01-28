<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/seguridadips/{{$seguridadips->id}}',
                method: 'PUT',
                data:{
                    view: 'editar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/seguridadips') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Seguridad de IP</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" value="{{$seguridadips->nombre}}" id="nombre" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
              <label class="col-sm-4 col-form-label">IP *</label>
              <div class="col-sm-8">
                  <input type="text" value="{{$seguridadips->ip}}" id="ip" class="form-control"/>
              </div>
          </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>