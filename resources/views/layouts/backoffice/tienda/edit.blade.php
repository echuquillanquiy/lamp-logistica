<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/tienda/{{ $tienda->id }}',
                method: 'PUT',
                data:{
                    view: 'editar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/tienda') }}';                                                                            
            },this)" enctype="multipart/form-data"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Tienda</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
              <!-- begin nav-pills -->
              <ul class="nav nav-pills">
                <li class="nav-items">
                  <a href="#nav-pills-tab-1-general" data-toggle="tab" class="nav-link active">
                    <span class="d-sm-none">Pills 1</span>
                    <span class="d-sm-block d-none">General</span>
                  </a>
                </li>
                <li class="nav-items">
                  <a href="#nav-pills-tab-2-configuracion" data-toggle="tab" class="nav-link">
                    <span class="d-sm-none">Pills 2</span>
                    <span class="d-sm-block d-none">Configuración</span>
                  </a>
                </li>
              </ul>
              <!-- end nav-pills -->
              <!-- begin content -->
              <div class="tab-content">
                <div class="tab-pane fade active show" id="nav-pills-tab-1-general">
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
                            <input type="text" value="{{ $tienda->nombre }}" id="nombre" class="form-control"/>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Descripción</label>
                          <div class="col-sm-8">
                              <textarea id="descripcion" cols="30" rows="5" class="form-control">{{ $tienda->descripcion }}</textarea>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Número de Teléfono *</label>
                          <div class="col-sm-8">
                            <input type="text" value="{{ $tienda->numerotelefono }}" id="numerotelefono" class="form-control"/>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Correo Electrónico *</label>
                          <div class="col-sm-8">
                            <input type="text" value="{{ $tienda->correo }}" id="correo" class="form-control"/>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Pagina Web</label>
                          <div class="col-sm-8">
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">http://</span>
                              </div>
                              <input type="text" value="{{ $tienda->paginaweb }}" id="paginaweb" class="form-control">
                            </div>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
                          <div class="col-sm-8">
                            <select id="idubigeo" class="form-control">
                                <option value="{{ $tienda->idubigeo }}">{{ $tienda->ubigeonombre }}</option>
                            </select>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Dirección *</label>
                          <div class="col-sm-8">
                            <input type="text" value="{{ $tienda->direccion }}" id="direccion" class="form-control"/>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Referencia</label>
                          <div class="col-sm-8">
                            <input type="text" value="{{ $tienda->referencia }}" id="referencia" class="form-control"/>
                          </div>
                      </div>
                      <h5 style="text-align: center;
                  background-color: #348fe2;
                  padding: 8px;
                  color: #f8f9fa;">Facturador SUNAT</h5>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Serie *</label>
                          <div class="col-sm-8">
                            <input type="number" value="{{ $tienda->facturador_serie }}" id="facturador_serie" class="form-control" disabled/>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Estado *</label>
                          <div class="col-sm-8">
                              <select id="facturador_idestado" class="form-control">
                                  <option></option>
                                  <option value="1">BETA</option>
                                  <option value="2">PRODUCCIÓN</option>
                              </select>
                          </div>
                      </div>
                </div>
                <div class="tab-pane fade" id="nav-pills-tab-2-configuracion">
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Icono (png) *</label>
                          <div class="col-sm-8">
                              <div class="fuzone" id="cont-fileupload2">
                                <div class="fu-text"><span><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</span></div>
                                <input type="file" class="upload" id="imagenicono">
                                <div id="resultado-fileupload2"></div>
                              </div>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Imagen de fondo (1000*800) *</label>
                          <div class="col-sm-8">
                              <div class="fuzone" id="cont-fileupload3">
                                <div class="fu-text"><span><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</span></div>
                                <input type="file" class="upload" id="imagenfondo">
                                <div id="resultado-fileupload3"></div>
                              </div>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Contraseña Interna </label>
                          <div class="col-sm-8">
                            <input type="text" value="{{ $tienda->claveinterna }}" id="claveinterna" class="form-control"/>
                          </div>
                      </div>
                </div>
              </div>
              <!-- end tab-content -->
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
$("#facturador_idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$tienda->facturador_idestado}}).trigger("change");
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-fileupload",
  ruta: "{{ url('public/admin/tienda/') }}",
  image: "{{ $tienda->imagen }}"
});
uploadfile({
  input:"#imagenicono",
  cont:"#cont-fileupload2",
  result:"#resultado-fileupload2",
  ruta: "{{ url('public/admin/tienda/') }}",
  image: "{{ $tienda->imagenicono }}"
});
uploadfile({
  input:"#imagenfondo",
  cont:"#cont-fileupload3",
  result:"#resultado-fileupload3",
  ruta: "{{ url('public/admin/tienda/') }}",
  image: "{{ $tienda->imagenfondo }}"
});
</script>