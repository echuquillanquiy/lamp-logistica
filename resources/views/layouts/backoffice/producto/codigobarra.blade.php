<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Codigo de Barra 25x65 </h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      
        <div id="cont-codigobarra"></div>
    </div>
</div>
<script>
mostrarcodigobarra();
function mostrarcodigobarra(){
    var codigoadicional = $('#codigoadicional').val();
    $('#cont-codigobarra').html('<iframe src="{{ url('backoffice/producto/'.$producto->id.'/edit') }}?view=codigobarra25x65&codigoadicional='+codigoadicional+'#zoom=130" frameborder="0" height="500px" width="100%"></iframe>');
} 
</script>