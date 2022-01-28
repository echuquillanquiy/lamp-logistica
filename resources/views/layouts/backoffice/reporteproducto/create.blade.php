<div class="modal-content">

   <div class="modal-header">
        <h4 class="modal-title">Descargar Reporte de Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
   </div>
   
    <div class="modal-body"  id="cont-reporteproducto">
        <div class="form-group row">
            <label class="col-sm-5 col-form-label">Contraseña Interna</label>
            <div class="col-sm-7">
                <input type="text" value="{{isset($_GET['claveinterna'])?$_GET['claveinterna']:''}}" id="claveinterna" class="form-control"/>
            </div>
        </div>
    </div>
    <div id="cont-reporteproducto-carga"></div>
    <a href="javascript:;" id="cont-reporteproducto-btn" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px; display:none; margin:15px;" ><i class="fa fa-file-excel"></i> Descargar Excel</a>
</div>
<script>  
$('#claveinterna').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarclaveinterna($('#claveinterna').val())
    }     
});
  
function cargarclaveinterna(claveinterna){
        load('#cont-reporteproducto-carga');
        $('#cont-reporteproducto-btn').css('display','none');
        $.ajax({
            url:"{{url('backoffice/reporteproducto/show-seleccionarclaveinterna')}}",
            type:'GET',
            data: {
                claveinterna : claveinterna
            },
            success: function (respuesta){
                 if(respuesta["respuesta"] == 'CORRECTO'){
                  $('#cont-reporteproducto-btn').css('display','block');
                  $('#cont-reporteproducto').css('display','none');
                  $('#cont-reporteproducto-carga').html('<div class="alert alert-info" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡DESCARGAR!</div>');
                }else{
                  $('#cont-reporteproducto-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡NO EXISTE!</div>');

                } 
         }
       })
}


</script>

 
  