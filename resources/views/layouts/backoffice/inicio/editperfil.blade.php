<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/inicio/{{ $usuario->id }}',
            method: 'PUT',
            data:{
                view: 'editperfil'
            }     
        },
        function(resultado){
            location.reload();           
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Perfil</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Indentificación (DNI) *</label>
            <div class="col-sm-8">
                  <input type="text" value="{{ $usuario->identificacion }}" id="identificacion" class="form-control" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->nombre }}" id="nombre" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Apellidos *</label>
            <div class="col-sm-8">
                <input type="text" value="{{ $usuario->apellidos }}" id="apellidos" class="form-control"/>
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
                  <input type="text" value="{{ $usuario->email }}" id="email" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Imagen</label>
            <div class="col-sm-8">
                <div class="fuzone" id="cont-fileupload">
                  <div class="fu-text"><span><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-fileupload"></div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Ubicación (Ubigeo) *</label>
            <div class="col-sm-8">
                  <select id="idubigeo" class="form-control">
                      <option></option>
                      @foreach($ubigeos as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
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
$("#idubigeo").select2({
    placeholder: "---  Seleccionar ---"
}).val({{ $usuario->idubigeo==0 ? $value->id==1026 : $usuario->idubigeo }}).trigger("change");

uploadfile({
  input: "#imagen",
  cont: "#cont-fileupload",
  result: "#resultado-fileupload",
  ruta: "{{ url('/public/admin/perfil') }}",
  image: "{{ $usuario->imagen }}"
});
</script>