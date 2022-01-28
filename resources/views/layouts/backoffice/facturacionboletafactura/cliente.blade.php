<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/usuario',
            method: 'POST',
            data:{
                view: 'registrar'
            }
        },
        function(resultado){
            removemodal('#mx-modal-carga-cliente');     
            $('#idcliente').html('<option value=\''+resultado.cliente.id+'\'>'+resultado.cliente.identificacion+' - '+resultado.cliente.apellidos+', '+resultado.cliente.nombre+'</option>'); 
            $('#clientedireccion').val(resultado.cliente.direccion);
            $('#clienteidubigeo').html('<option value=\''+resultado.ubigeo.id+'\'>'+resultado.ubigeo.nombre+'</option>');
                                                                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Usuario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Tipo de Persona *</label>
            <div class="col-sm-8">
                <select id="idtipopersona" class="form-control">
                    @foreach($tipopersonas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="cont-juridica" style="display:none;">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">RUC *</label>
            <div class="col-sm-8">
                <input type="text" id="ruc" class="form-control" onkeyup="validaruc(this.value)"/>
                <span class="text-danger" id="procesandoinfo"></span>
            </div>
            <label class="col-sm-4 col-form-label">Nombre Comercial *</label>
            <div class="col-sm-8">
                <input type="text" id="nombrecomercial" class="form-control" readonly/>
            </div>
            <label class="col-sm-4 col-form-label">Razòn Social *</label>
            <div class="col-sm-8">
                <input type="text" id="razonsocial" class="form-control" readonly/>
            </div>
        </div>
        </div>
        <div id="cont-natural" style="display:none;">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Identificación *</label>
            <div class="col-sm-8">
                <input type="text" id="dni" class="form-control"/>
            </div>
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" id="nombre" class="form-control"/>
            </div>
            <label class="col-sm-4 col-form-label">Apellidos *</label>
            <div class="col-sm-8">
                <input type="text" id="apellidos" class="form-control"/>
            </div>
        </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número de Teléfono</label>
            <div class="col-sm-8">
                <input type="text" id="numerotelefono" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Correo Electrónico</label>
            <div class="col-sm-8">
                <input type="text" id="email" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
            <div class="col-sm-8">
                <select id="idubigeo" class="form-control">
                      <option></option>
                  </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Dirección *</label>
            <div class="col-sm-8">
                <input type="text" id="direccion" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>            
<script>
function validaruc(ruc){
    $('#razonsocial').attr("readonly", true);
    $('#nombrecomercial').attr("readonly", true);
    $('#razonsocial').val('');
    $('#nombrecomercial').val('');
    $('#idubigeo').html('<option></option>');
    $('#direccion').val('');
    var cuentaruc = ruc.length;
    if (cuentaruc==11){
      $.ajax({
        url: '{{url("backoffice/usuario/show-validaruc")}}?ruc='+ruc,
        type:"GET",
        beforeSend: function() {
            $("#procesandoinfo").html('<div class="alert alert-info">Procesando información...</div>');
        },
        success:function(respuesta){
          if(respuesta['consultaruc']['success'] == true){
              $('#razonsocial').val(respuesta['consultaruc']['result'].razon_social);
              $('#nombrecomercial').val(respuesta['consultaruc']['result'].nombre_comercial);
              $('#idubigeo').html(respuesta['ubigeo']);
              $('#direccion').val(respuesta['direccion']);
              $("#procesandoinfo").html('');
              $('#razonsocial').attr("readonly", false);
              $('#nombrecomercial').attr("readonly", false);             
          }else{
              $("#procesandoinfo").html('<div class="alert alert-danger">'+respuesta['consultaruc']['message']+'</div>');
          }
        }
      });
    }else{
      $('#razonsocial').val('');
      $('#nombrecomercial').val('');
      $('#direccion').val('');
      $('#razonsocial').attr("readonly", true);
      $('#nombrecomercial').attr("readonly", true);
    }        
}
$("#idtipopersona").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(e.currentTarget.value == 1 || e.currentTarget.value == 3) {
        $('#cont-natural').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-juridica').css('display','block');
    }
}).val(1).trigger("change");

$("#idubigeo").select2({
    ajax: {
        url:"{{url('backoffice/usuario/show-ubigeo')}}",
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
    minimumInputLength: 2,
    allowClear: true
});
</script>