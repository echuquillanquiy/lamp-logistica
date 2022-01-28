<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Imagenes</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <form action="javascript:;" 
          id="form-productoimagen"
          onsubmit="callback({
                route: 'backoffice/producto/{{$producto->id}}',
                method: 'PUT',
                data:{
                    view: 'registrarimagen'
                }
            },
            function(resultado){
                selectproductoimagen();                                                                          
            },this)" enctype="multipart/form-data"> 
        <div class="form-group row">
            <div class="col-sm-12">
                <div class="fuzone" id="cont-fileupload">
                      <div class="fu-text"><i class="fa fa-image"></i> Haga clic aquí o suelte para cargar</div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-fileupload"></div>
                  </div>
            </div>
        </div>
        <div style="text-align: center; margin-bottom: 5px;"> <button type="submit" class="btn btn-success">Subir Imagen</button></div>
        </form>  
        <div id="cont-productoimagen"></div>
    </div>
</div>

<script>
uploadfile({
  input: "#imagen",
  cont: "#cont-fileupload",
  result: "#resultado-fileupload",
  ruta: "{{ url('/public/admin/perfil/') }}"
});
  
selectproductoimagen();
function selectproductoimagen(){
    load('#cont-productoimagen');
    $.ajax({
        url:"{{url('backoffice/producto/show-seleccionarproductoimagen')}}",
        type:'GET',
        data: {
            idproducto : '{{$producto->id}}'
        },
        success: function (respuesta){
            $('#cont-productoimagen').html(respuesta);
            $('#resultado-fileupload').html('');
        }
    });
}
function removeimagenproducto(idproductoimagen){
    load('#cont-productoimagen');
    callback({
                route: 'backoffice/producto/'+idproductoimagen,
                method: 'DELETE',
                data:{
                    view: 'eliminarimagen'
                }
            },
            function(resultado){
                selectproductoimagen();                                                                          
            });
}
</script>