<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/bancocuentabancaria',
              method: 'POST',
              data:{
                  view: 'registrar'
              }
          },
          function(resultado){
                 location.href = '{{ url('backoffice/bancocuentabancaria') }}';                                                             
          },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Número de cuenta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Banco *</label>
            <div class="col-sm-8">
                <select id="idbanco">
                          <option></option>
                          @foreach($bancos as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre de cuenta *</label>
            <div class="col-sm-8">
                <input type="text" id="nombre" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número de cuenta *</label>
            <div class="col-sm-8">
                <input type="text" id="numerocuenta" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>
<script>
$("#idbanco").select2({
    placeholder: "-- Seleccionar --",
    minimumResultsForSearch: -1
});
</script>