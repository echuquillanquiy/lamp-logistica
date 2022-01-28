<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/venta/{{$venta->id}}',
              method: 'PUT',
              data:{
                  view: 'correo'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/venta') }}';                                                  
          },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Enviar a Correo Electrónico</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Correo Electrónico *</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="correo">
                    </div>
                </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Enviar</button>
    </div>
</form>
</div>