<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/aperturaycierre/{{ $aperturacierre->id }}',
              method: 'PUT',
              data:{
                  view: 'editar'
              }
          },
          function(resultado){
                  location.href = '{{ url('backoffice/aperturaycierre') }}';                                                            
          },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Confirmar Apertura de Caja</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Caja *</label>
            <div class="col-sm-8">
                <select id="idcaja">
                          <option></option>
                          @foreach($cajas as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Saldo anterior</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" id="saldoanterior" placeholder="---" readonly/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto a asignar S/. *</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{ $aperturacierre->montoasignarsoles }}" id="montoasignarsoles" step="0.01" min="0"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto a asignar $ *</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{ $aperturacierre->montoasignardolares }}" id="montoasignardolares" step="0.01" min="0"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Persona responsable</label>
            <div class="col-sm-8">
                <select id="idusersresponsable" class="form-control" disabled>
                          <option>{{$aperturacierre->usersresponsablenombre}}</option>
                      </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Persona a asignar</label>
            <div class="col-sm-8">
                <select id="idusers" class="form-control" >
                    <option></option>
                          @foreach($users as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
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
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/aperturaycierre/show-saldoanterior')}}",
        type:'GET',
        data: {
            idcaja : e.currentTarget.value
       },
       success: function (respuesta){
          $("#saldoanterior").val(respuesta.saldoactual);
       }
     })
}).val({{$aperturacierre->idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "--  Seleccionar --"
}).val({{$aperturacierre->idusersrecepcion}}).trigger("change");

$("#idusersresponsable").select2({
    ajax: {
        url:"{{url('backoffice/aperturaycierre/show-listarusuario')}}",
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
});
</script>