<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Editar Configuración de Sistema</h4>
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
            <a href="#nav-pills-tab-2-imagenlogin" data-toggle="tab" class="nav-link">
              <span class="d-sm-none">Pills 2</span>
              <span class="d-sm-block d-none">Imágenes de Login</span>
            </a>
          </li>
        </ul>
        <!-- end nav-pills -->
        <!-- begin content -->
        <div class="tab-content">
          <div class="tab-pane fade active show" id="nav-pills-tab-1-general">
            <form action="javascript:;" 
                      onsubmit="callback({
                            route: 'backoffice/sistema/{{ $sistema->id }}',
                            method: 'PUT',
                            data:{
                                view: 'editar'
                            }
                        },
                        function(resultado){
                            location.href = '{{ url('backoffice/sistema') }}';
                        },this)" enctype="multipart/form-data"> 
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Nombre *</label>
                  <div class="col-sm-8">
                    <input type="text" value="{{ $sistema->nombre }}" id="nombre" class="form-control"/>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Descripción</label>
                  <div class="col-sm-8">
                    <textarea id="descripcion" cols="30" rows="5" class="form-control">{{ $sistema->descripcion }}</textarea>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Slogan</label>
                  <div class="col-sm-8">
                    <input type="text" value="{{ $sistema->slogan }}" id="slogan" class="form-control"/>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Numero de Teléfono</label>
                  <div class="col-sm-8">
                    <input type="text" value="{{ $sistema->numerotelefono }}" id="numerotelefono" class="form-control"/>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Correo Electrónico</label>
                  <div class="col-sm-8">
                    <input type="text" value="{{ $sistema->correo }}" id="correo" class="form-control"/>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Página Web</label>
                  <div class="col-sm-8">
                    <input type="text" value="{{ $sistema->paginaweb }}" id="paginaweb" class="form-control"/>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Icono (png)</label>
                  <div class="col-sm-8">
                      <div class="fuzone" id="cont-fileupload">
                        <div class="fu-text"><span><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</span></div>
                        <input type="file" class="upload" id="imagenicono">
                        <div id="resultado-fileupload"></div>
                      </div>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Logo (png)</label>
                  <div class="col-sm-8">
                      <div class="fuzone" id="cont-fileuploadlogo">
                        <div class="fu-text"><span><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</span></div>
                        <input type="file" class="upload" id="imagenlogo">
                        <div id="resultado-fileuploadlogo"></div>
                      </div>
                  </div>
              </div>
              <div style="text-align: right;">
                <button type="submit" class="btn btn-success">Actualizar</button>
              </div>
              
          </form> 
          </div>
          <div class="tab-pane fade" id="nav-pills-tab-2-imagenlogin">            
              <form action="javascript:;" 
                id="form-imagenlogin"
                onsubmit="callback({
                      route: 'backoffice/sistema/{{$sistema->id}}',
                      method: 'PUT',
                      data:{
                          view: 'registrarimagen'
                      }
                  },
                  function(resultado){
                      selectimagenlogin();                                                                        
                  },this)" enctype="multipart/form-data">
              <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Imágenes de login (1000*800) *</label>
                  <div class="col-sm-12">
                      <div class="fuzone" id="cont-fileupload2">
                            <div class="fu-text"><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</div>
                            <input type="file" class="upload" id="imagenlogin">
                            <div id="resultado-fileupload2"></div>
                        </div>
                  </div>
              </div> 
              <div style="text-align: center; margin-bottom: 5px;"> <button type="submit" class="btn btn-success">Subir Imagen</button></div>
              </form>  
              <div id="cont-imagenlogin"></div>              
          </div>
        </div>
        <!-- end tab-content -->
    </div>
</div>
<script>
uploadfile({
  input:"#imagenicono",
  cont:"#cont-fileupload",
  result:"#resultado-fileupload",
  ruta: "{{ url('public/admin/sistema/') }}",
  image: "{{ $sistema->imagenicono }}"
});

uploadfile({
  input:"#imagenlogo",
  cont:"#cont-fileuploadlogo",
  result:"#resultado-fileuploadlogo",
  ruta: "{{ url('public/admin/sistema/') }}",
  image: "{{ $sistema->imagenlogo }}"
});

uploadfile({
  input: "#imagenlogin",
  cont: "#cont-fileupload2",
  result: "#resultado-fileupload2",
  ruta: "{{ url('/public/admin/sistema/') }}"
});
  
selectimagenlogin();
function selectimagenlogin(){
    load('#cont-imagenlogin');
    $.ajax({
        url:"{{url('backoffice/sistema/show-seleccionarimagenlogin')}}",
        type:'GET',
        data: {
            idsistema : '{{$sistema->id}}'
        },
        success: function (respuesta){
            $('#cont-imagenlogin').html(respuesta);
            $('#resultado-fileupload2').html('');
        }
    });
}
function removeimagensistema(idimagenlogin){
    load('#cont-imagenlogin');
    callback({
        route: 'backoffice/sistema/'+idimagenlogin,
        method: 'DELETE',
        data:{
            view: 'eliminarimagen'
        }
    },
    function(resultado){
        selectimagenlogin();                                                                          
    });
}
</script>