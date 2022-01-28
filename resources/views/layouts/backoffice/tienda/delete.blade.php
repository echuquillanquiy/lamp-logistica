<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
                route: 'backoffice/tienda/{{ $tienda->id }}',
                method: 'DELETE',
                data:{
                    view: 'eliminar'
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/tienda') }}';                                                                            
            },this)" enctype="multipart/form-data"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Tienda</h4>
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
              <input type="text" value="{{ $tienda->nombre }}" id="nombre" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número de Teléfono *</label>
            <div class="col-sm-8">
              <input type="text" value="{{ $tienda->numerotelefono }}" id="numerotelefono" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Correo Electrónico</label>
            <div class="col-sm-8">
              <input type="text" value="{{ $tienda->correo }}" id="correo" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Pagina Web</label>
            <div class="col-sm-8">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon3">http://</span>
                </div>
                <input type="text" value="{{ $tienda->paginaweb }}" id="paginaweb" class="form-control" disabled>
              </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
            <div class="col-sm-8">
              <select id="idubigeo" class="form-control" disabled>
                  <option value="{{ $tienda->idubigeo }}">{{ $tienda->ubigeonombre }}</option>
              </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Dirección *</label>
            <div class="col-sm-8">
              <input type="text" value="{{ $tienda->direccion }}" id="direccion" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Referencia</label>
            <div class="col-sm-8">
              <input type="text" value="{{ $tienda->referencia }}" id="referencia" class="form-control" disabled/>
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
  result:"#resultado-fileupload",
  ruta: "{{ url('public/admin/tienda/') }}",
  image: "{{ $tienda->imagen }}"
});
</script>