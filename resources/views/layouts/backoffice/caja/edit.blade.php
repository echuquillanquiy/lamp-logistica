<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/caja/{{ $caja->id }}',
                method: 'PUT'
            },
            function(resultado){
                location.href = '{{ url('backoffice/caja') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Caja</h4>
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
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Estado *</label>
            <div class="col-sm-8">
                <select id="idestado" class="form-control">
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
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{ $caja->idestado }}).trigger('change');
</script>