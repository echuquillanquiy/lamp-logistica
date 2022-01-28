<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/tienda',
                method: 'POST'
            },
            function(resultado){
                location.href = '{{ url('backoffice/tienda') }}';                                                                            
            },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Tienda</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Logo (300x300) *</label>
            <div class="col-sm-8">
                <div class="fuzone" id="cont-fileupload">
                  <div class="fu-text"><span><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</span></div>
                  <input type="file" class="upload" id="imagen">
                  <div id="resultado-fileupload"></div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre de Tienda *</label>
            <div class="col-sm-8">
              <input type="text" id="nombre" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Descripción</label>
            <div class="col-sm-8">
                <textarea id="descripcion" cols="30" rows="5" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número de Teléfono *</label>
            <div class="col-sm-8">
              <input type="text" id="numerotelefono" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Correo Electrónico *</label>
            <div class="col-sm-8">
              <input type="text" id="correo" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Pagina Web</label>
            <div class="col-sm-8">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon3">http://</span>
                </div>
                <input type="text" id="paginaweb" class="form-control">
              </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
            <div class="col-sm-8">
              <select id="idubigeo" class="form-control">
                  <option ></option>
              </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Dirección *</label>
            <div class="col-sm-8">
              <input type="text" id="direccion" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Referencia</label>
            <div class="col-sm-8">
              <input type="text" id="referencia" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>
<script>
$("#idubigeo").select2({
    ajax: {
        url:"{{url('backoffice/tienda/show-ubigeo')}}",
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
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-fileupload"
});
</script>