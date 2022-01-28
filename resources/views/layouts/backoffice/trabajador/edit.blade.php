<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/trabajador/{{ $usuario->id }}',
            method: 'PUT',
            data:{
                view: 'editar'
            }
        },
        function(resultado){
            location.reload();                                                          
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Usuario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Tipo de Persona *</label>
            <div class="col-sm-8">
                <select id="idtipopersona" class="form-control" disabled>
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
                <input type="text" value="{{ $usuario->identificacion }}" id="ruc" class="form-control" disabled/>
            </div>
            <label class="col-sm-4 col-form-label">Nombre Comercial *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->nombre }}" id="nombrecomercial" class="form-control"/>
            </div>
            <label class="col-sm-4 col-form-label">Razòn Social *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->apellidos }}" id="razonsocial" class="form-control"/>
            </div>
        </div>
        </div>
        <div id="cont-natural" style="display:none;">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Identificación *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->identificacion }}" id="dni" class="form-control" disabled/>
            </div>
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->nombre }}" id="nombre" class="form-control"/>
            </div>
            <label class="col-sm-4 col-form-label">Apellidos *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->apellidos }}" id="apellidos" class="form-control"/>
            </div>
        </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Imagen de Perfil</label>
            <div class="col-sm-8">
                <div class="fuzone" id="cont-fileupload">
                      <div class="fu-text"><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-fileupload"></div>
                  </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Número de Teléfono</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->numerotelefono }}" id="numerotelefono" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Correo Electrónico</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->email }}" id="email" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
            <div class="col-sm-8">
                <select id="idubigeo" class="form-control">
                      <option value="{{ $usuario->idubigeo }}">{{ $usuario->ubigeonombre }}</option>
                  </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Dirección *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->direccion }}" id="direccion" class="form-control"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
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
}).val({{$usuario->idtipopersona}}).trigger("change");

$("#idubigeo").select2({
    ajax: {
        url:"{{url('backoffice/trabajador/show-ubigeo')}}",
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
  input: "#imagen",
  cont: "#cont-fileupload",
  result: "#resultado-fileupload",
  ruta: "{{ url('/public/admin/perfil/') }}",
  image: "{{ $usuario->imagen }}"
});
</script>