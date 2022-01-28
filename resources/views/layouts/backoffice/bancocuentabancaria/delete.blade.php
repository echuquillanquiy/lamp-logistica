<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/bancocuentabancaria/{{$bancocuentabancaria->id}}',
              method: 'DELETE',
              data:{
                  view: 'eliminar'
              }
          },
          function(resultado){
                 location.href = '{{ url('backoffice/bancocuentabancaria') }}';                                                             
          },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Número de cuenta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Banco</label>
            <div class="col-sm-8">
                <select id="idbanco" disabled>
                          <option></option>
                          @foreach($bancos as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre de cuenta</label>
            <div class="col-sm-8">
                <input type="text" value="{{$bancocuentabancaria->nombre}}" id="nombre" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número de cuenta</label>
            <div class="col-sm-8">
                <input type="text" value="{{$bancocuentabancaria->numerocuenta}}" id="numerocuenta" class="form-control" disabled/>
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
<script>
$("#idbanco").select2({
    placeholder: "-- Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$bancocuentabancaria->idbanco}}).trigger("change");
</script>