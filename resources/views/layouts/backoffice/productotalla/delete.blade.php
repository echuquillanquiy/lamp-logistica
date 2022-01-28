<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/productotalla/{{$productotalla->id}}',
                method: 'DELETE',
                data:{
                    view: 'eliminar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/productotalla') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Talla</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre</label>
            <div class="col-sm-8">
                <input type="text" value="{{$productotalla->nombre}}" id="nombre" class="form-control" disabled/>
            </div>
        </div>
            <div class="alert alert-warning">¿Esta seguro de eliminar?</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Eliminar</button>
    </div>
</form>  
</div>