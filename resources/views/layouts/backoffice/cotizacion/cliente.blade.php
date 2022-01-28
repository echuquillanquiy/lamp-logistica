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
                <select id="idtipopersona">
                    @foreach($tipopersonas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
               </div>
      </div>
      <div id="cont-juridica" style="display:none;">
          <div class="form-group row" >
              <label class="col-sm-4 col-form-label">RUC *</label>
                  <div class="col-sm-8">
                       <input type="text" id="ruc"  class="form-control" onkeyup="buscar_ruc()">
                    <div id="resultado-ruc" style="float: right;margin-top: -48px;text-align: right;"></div>
                   </div>
          </div>
          
          <div class="form-group row">
              <label class="col-sm-4 col-form-label">Nombre Comercial *</label>
                  <div class="col-sm-8">
                      <input type="text" id="nombrecomercial"  class="form-control" disabled/>
                   </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-4 col-form-label">Razòn Social *</label>
                  <div class="col-sm-8">
                       <input type="text" id="razonsocial" class="form-control"  disabled/>
                   </div>
          </div>
        </div>
      <div id="cont-natural" style="display:none;">
        <div class="form-group row" >
              <label class="col-sm-4 col-form-label">DNI (8 Digitos)  *</label>
                  <div class="col-sm-8">
                         <input type="text" id="dni" class="form-control" onkeyup="buscar_dni()">
                      <div id="resultado-dni" style="float: right;margin-top: -48px;text-align: right;"></div>
                   </div>
          </div>
      
         <div class="form-group row">
              <label class="col-sm-4 col-form-label">Nombre *</label>
                  <div class="col-sm-8">
                       <input type="text" id="nombre" class="form-control" disabled>
                   </div>
          </div>
         <div class="form-group row">
              <label class="col-sm-4 col-form-label">Apellidos *</label>
                  <div class="col-sm-8">
                       <input type="text" id="apellidos"class="form-control" disabled>
                   </div>
          </div>
      </div>
        <div class="form-group row">
              <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
                  <div class="col-sm-8">
                      <select id="idubigeo" disabled>
                      <option></option>
                  </select>
                   </div>
        </div>
        <div class="form-group row">
              <label class="col-sm-4 col-form-label">Dirección *</label>
                  <div class="col-sm-8">
                      <input type="text" id="direccion" class="form-control" disabled/>
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
      
      </div>
        <div class="modal-footer">
            <a href="javascript:;" id="botonenviar" class="btn btn-success" onclick="$('#form-usuario').submit();">Guardar Cambios</a>
        </div>
    </form>  
    </div>            
    <script>
   function buscar_dni(){
    limpiarcampos();
    $('#resultado-dni').html('');
    var identificacion = $('#dni').val();
    if(identificacion.length==8){
        load('#resultado-dni');
        $.ajax({
            url:"{{url('backoffice/usuario/showbuscaridentificacion')}}",
            type:'GET',
            data: {
                buscar_identificacion : identificacion,
                tipo_persona : 1
            },
            success: function (respuesta){
                $('#resultado-dni').html('');
                $('#nombre').removeAttr('disabled');
                $('#apellidos').removeAttr('disabled');
                $('#idubigeo').removeAttr('disabled');
                $('#direccion').removeAttr('disabled');
                if(respuesta.resultado=='ERROR'){
                    $('#nombre').val('');
                    $('#apellidos').val('');
                    $('#idubigeo').val('');
                    $('#direccion').val('');
                }else{
                    $('#nombre').val(respuesta.nombres);
                    $('#apellidos').val(respuesta.apellidoPaterno+' '+respuesta.apellidoMaterno);
                }  
            }
        })
    }  
}
function buscar_ruc(){
    $('#resultado-ruc').html('');
    var identificacion = $('#ruc').val();
    if(identificacion.length==11){
        load('#resultado-ruc');
        $.ajax({
            url:"{{url('backoffice/usuario/showbuscaridentificacion')}}",
            type:'GET',
            data: {
                buscar_identificacion : identificacion,
                tipo_persona : 2
            },
            success: function (respuesta){
                $('#resultado-ruc').html('');
                $('#nombrecomercial').removeAttr('disabled');
                $('#razonsocial').removeAttr('disabled');
                $('#idubigeo').removeAttr('disabled');
                $('#direccion').removeAttr('disabled');
                if(respuesta.resultado=='ERROR'){
                    $('#nombrecomercial').val('');
                    $('#razonsocial').val('');
                    $('#idubigeo').val('');
                    $('#direccion').val('');
                }else{
                    $('#nombrecomercial').val(respuesta.nombreComercial);
                    $('#razonsocial').val(respuesta.razonSocial);
                    $('#idubigeo').html('<option value="'+respuesta.idubigeo+'">'+respuesta.ubigeo+'</option>');
                    $('#direccion').val(respuesta.direccion);
                }  
            }
        })
    }  
}
function limpiarcampos(){
    $('#nombre').attr('disabled','true');
    $('#apellidos').attr('disabled','true');
    $('#nombrecomercial').attr('disabled','true');
    $('#razonsocial').attr('disabled','true');
    $('#idubigeo').attr('disabled','true');
    $('#direccion').attr('disabled','true');
  

    $('#nombre').val('');
    $('#apellidos').val('');
    $('#nombrecomercial').val('');
    $('#razonsocial').val('');
    $('#idubigeo').html('');
    $('#direccion').val('');
}
$("#idtipopersona").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(e.currentTarget.value == 1) {
        $('#cont-natural').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-juridica').css('display','block');
    }    
    $('#dni').val('');
    $('#ruc').val('');
    limpiarcampos();
}).val(1).trigger("change");

$("#idubigeo").select2({
    ajax: {
        url:"{{url('backoffice/usuario/show-ubigeo')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term,
                  view: 'listarubigeo'
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