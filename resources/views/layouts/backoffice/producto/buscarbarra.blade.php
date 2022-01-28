<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Codigo de Barra 25x65 </h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div style="max-width: 300px;margin: auto;;margin-bottom: 5px;">
            <input type="text" id="buscarcodigobarra" class="form-control" 
                   placeholder="Código de Barra *" style="font-weight: bold;font-size: 20px;text-align: center;"/>
        </div>
        <div style="max-width: 300px;margin: auto;;margin-bottom: 5px;">
            <input type="text" id="codigoadicional" class="form-control" 
                   placeholder="Código Adicional" style="font-weight: bold;font-size: 20px;text-align: center;"/>
        </div>
        <div style="text-align: center;margin-bottom: 5px;">
            <a href="javascript:;" onclick="mostrarcodigobarra()" class="btn btn-warning">Filtrar</a>
        </div>
        <div id="cont-codigobarra"></div>
    </div>
</div>
<script>
mostrarcodigobarra();
function mostrarcodigobarra(){
    var codigoadicional = $('#codigoadicional').val();
    var buscarcodigobarra = $('#buscarcodigobarra').val();
    $('#cont-codigobarra').html('<iframe src="{{ url('backoffice/producto/create') }}?view=buscarbarra25x65&buscarcodigobarra='+buscarcodigobarra+'&codigoadicional='+codigoadicional+'#zoom=130" frameborder="0" height="500px" width="100%"></iframe>');
} 
</script>