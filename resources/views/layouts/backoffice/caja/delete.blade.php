<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/caja/{{$caja->id}}',
                method: 'DELETE',
                data:{
                    view: 'eliminar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/caja') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Caja</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Tienda *</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" value="{{ $caja->tiendanombre }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $caja->nombre }}" id="nombre" class="form-control"/>
            </div>
        </div>
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de eliminar?
				</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Eliminar</button>
    </div>
</form>  
</div>