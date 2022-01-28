<div class="modal-content">
<form action="javascript:;"    onsubmit="callback({
                                                      route: 'backoffice/saldousuario/{{ $saldousuario->id }}',
                                                      method: 'DELETE',
                                                  },
                                                  function(resultado){
                                                      location.href = '{{ url('backoffice/saldousuario') }}';                                                                            
                                                  },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Saldo de Usuario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Cliente *</label>
            <div class="col-sm-8">
                <select id="idcliente" disabled>
                  <option value="{{ $saldousuario->idusuario }}">{{ $saldousuario->nombre }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Saldo Actual</label>
            <div class="col-sm-8">
                <input type="number" id="saldoactual"  class="form-control"  value="0.00" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Agregar Saldo *</label>
            <div class="col-sm-8">
                <input type="number" id="saldoagregar"  class="form-control" value="{{ $saldousuario->monto }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Motivo *</label>
            <div class="col-sm-8">
                <input type="text" id="motivo" value="{{ $saldousuario->motivo }}" class="form-control" disabled/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Eliminar</button>
    </div>
</form>  
</div>
<script>
$("#idcliente").select2({
      ajax: {
        url:"{{url('backoffice/saldousuario/show-listarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
}).on('change', function (e) {
  $('#saldoagregar').prop("disabled", false);
});
</script>