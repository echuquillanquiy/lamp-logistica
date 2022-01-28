<div class="modal-content">
<form action="javascript:;"   id="form-usuario"
          onsubmit="callback({
            route: 'backoffice/usuario',
            method: 'POST',
            idform: 'form-usuario',
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
                <input type="text" id="ruc" class="form-control" />
                <span class="text-danger" id="procesandoinfo2"></span>
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
                <span class="text-danger" id="procesandoinfo1"></span>
            </div>
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" id="nombre" class="form-control" readonly/>
            </div>
            <label class="col-sm-4 col-form-label">Apellidos *</label>
            <div class="col-sm-8">
                <input type="text" id="apellidos" class="form-control" readonly/>
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
                <select id="idubigeo" class="form-control" disabled>
                  </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Dirección *</label>
            <div class="col-sm-8">
                <input type="text" id="direccion" class="form-control" readonly/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" id="botonenviar" class="btn btn-success" onclick="$('#form-usuario').submit();">Guardar Cambios</a>
    </div>
</form>  
</div>            
<script>

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
  
    $("#nombrecomercial, #razonsocial, #apellidos, #nombre, #direccion, #dni, #ruc").val('');
    $("#nombrecomercial, #razonsocial, #apellidos, #nombre, #direccion").prop('readonly', true);
    $("#idubigeo").prop('disabled', true);
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

function ajaxIdentificacion(numeroIdentificacion, idTipoPersona) {
    $.ajax({
        url: "{{ url('backoffice/usuario/show-dniruc') }}",
        data: {
            numeroidentificacion: numeroIdentificacion,
            idtipopersona: idTipoPersona,
        },
        dataType: 'json',
        beforeSend: function() {
            if (idTipoPersona == 1) {
                $("#procesandoinfo1").html('<div class="alert alert-info">Procesando información...</div>');
            }else {
                $("#procesandoinfo2").html('<div class="alert alert-info">Procesando información...</div>');
            }
        },
        success: (response) => {
            if (idTipoPersona == 1) {
                if (response.resultado == 'ERROR') {
                    $("#procesandoinfo1").html(`<div class="alert alert-danger">${response.mensaje}</div>`);
                    $("#nombre, #apellidos").val('');
                }else {
                    $("#nombre").val(response.nombres);
                    $("#apellidos").val(`${response.apellidoPaterno} ${response.apellidoMaterno}`);
                    $("#procesandoinfo1").html('');
                  
                    $("#nombre, #apellidos, #direccion").prop('readonly', false);
                    $("#idubigeo").prop('disabled', false);
                }
            }else {
                if (response.resultado == 'ERROR') {
                    $("#procesandoinfo2").html(`<div class="alert alert-danger">${response.mensaje}</div>`);
                    $("#nombrecomercial, #razonsocial, #direccion").val('');
                    $("#idubigeo").html('');
                        $('#nombrecomercial').removeAttr('readonly');
                        $('#razonsocial').removeAttr('readonly');
                        $('#direccion').removeAttr('readonly');
                        $('#idubigeo').removeAttr('disabled');
                }else {
                    $("#nombrecomercial, #razonsocial, #direccion").prop('readonly', false);
                    $("#idubigeo").prop('disabled', false);
                  
                    $("#nombrecomercial").val(response.nombreComercial == '-' ? '' : response.nombreComercial);
                    $("#razonsocial").val(response.razonSocial);
                    $("#direccion").val(response.direccion);
                    $("#idubigeo").append(`<option value="${response.idubigeo}">${response.ubigeo}</option>`);
                    $("#procesandoinfo2").html('');
                }
            }
        }
    });
}

$('#ruc').keyup(function (e) {
    let code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13) {
        ajaxIdentificacion(e.currentTarget.value, 2);
    }
})

$('#dni').keyup(function (e) {
    let code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13) {
        ajaxIdentificacion(e.currentTarget.value, 1);
    }
})
</script>