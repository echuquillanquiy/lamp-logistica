<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Imagenes</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <!--div class="form-group row">
            <label class="col-sm-4 col-form-label">Número / Correo *</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="numerocorreo">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label"></label>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">Enviar Correo</button>
                <a href="javascript:;" class="btn btn-success" style="margin-bottom: 5px;" onclick="enviar_whatsapp()">Enviar WhatsApp</a>
            </div>
        </div-->
        <div id="cont-productoimagen"></div>
    </div>
</div>

<script>
  
selectproductoimagen();
function selectproductoimagen(){
    load('#cont-productoimagen');
    $.ajax({
        url:"{{url('backoffice/producto/show-seleccionarproductoimagendetalle')}}",
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
function enviar_whatsapp(){
    var numero = $('#numerocorreo').val();
    var texto = '%C2%A1Hola!%20Tengo%20problemas%20con%20mi%20matricula,%20mi%20c%C3%B3digo%20es%2046608107';
    window.open("https://api.whatsapp.com/send?phone="+numero+"&text="+texto, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400");
}
</script>