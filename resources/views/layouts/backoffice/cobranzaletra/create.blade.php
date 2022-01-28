<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/cobranzaletra',
            method: 'POST',
            data:{
                view: 'registrar',
                selectletras : selectletras(),
                seleccionartipopago: seleccionartipopago()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/cobranzaletra') }}';                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Cobranza de Letra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Venta *</label>
            <div class="col-sm-10">
                <select class="form-control" id="idventa">
                  <option></option>
                </select>
            </div>
        </div> 
        <input type="hidden" id="validletra">
        <div id="cont-cobranzaletras-carga"></div>
        <div id="cont-cobranzaletras-primeravez" style="display:none;">
            <table class="table" id="tabla-cronogramaletra-primeravez" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>N°</th>
                    <th>N° Letra</th>
                    <th>N° Único</th>
                    <th>F. Venc.</th>
                    <th>Importe</th>
                    <th>F. de Pago</th>
                  </tr>
                <tbody>
                </tbody>
            </table>
              <div class="note note-warning note-with-right-icon m-b-5">
								<div class="note-icon"><i class="fa fa-lightbulb"></i></div>
								<div class="note-content text-right">
									<h4><b>Advertencia!</b></h4>
									<p>
                    Esta Letra no tiene registro de N° Unico, registre correctamente.
									</p>
								</div>
							</div>
        </div>
        <div id="cont-cobranzaletras-otrasveces" style="display:none;">
            <table class="table" id="tabla-cronogramaletra" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>N°</th>
                    <th>N° Letra</th>
                    <th>N° Único</th>
                    <th>F. Venc.</th>
                    <th>Importe</th>
                    <th>F. de Pago</th>
                  </tr>
                <tbody>
                </tbody>
            </table>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Deuda restante</label>
                <div class="col-sm-8">
                    <input type="text" id="cliente_deudarestante" class="form-control" disabled>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Fecha de Inicio de Pago</label>
                <div class="col-sm-8">
                    <input type="text" id="cliente_fechainiciopago" class="form-control" disabled>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">N° de letra a Pagar</label>
                <div class="col-sm-8">
                    <input type="text" id="cliente_cuotaapagar" class="form-control" disabled>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Moneda</label>
                <div class="col-sm-8">
                    <input type="text" id="cliente_moneda" class="form-control" disabled>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Monto a Pagar</label>
                <div class="col-sm-8">
                    <input type="text" id="cliente_montoapagar" class="form-control" disabled>
                </div>
            </div> 
                @include('app.formapago',[
                    'formapago' => 'false'
                ])
        </div>
            
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>
<script>
$('#idventa').select2({
  ajax: {
        url:"{{url('backoffice/cobranzaletra/show-listarventacliente')}}",
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
    minimumInputLength: 2
}).on("change", function(e) {
    load('#cont-cobranzaletras-carga');
    $("#cont-cobranzaletras-otrasveces").css('display','none');
    $("#cont-cobranzaletras-primeravez").css('display','none');
    $.ajax({
        url:"{{url('backoffice/cobranzaletra/show-seleccionarventacliente')}}",
        type:'GET',
        data: {
            idventa : e.currentTarget.value
       },
       success: function (respuesta){
          $('#cont-cobranzaletras-carga').html('');
          $("#validletra").val(respuesta['validletra']);
          if(respuesta['validletra']>0){
              $("#cont-cobranzaletras-primeravez").css('display','block');
              $("#tabla-cronogramaletra-primeravez > tbody").html(respuesta['cronogramaletra']);
          }else{
              $("#cont-cobranzaletras-otrasveces").css('display','block');
              $("#cliente_deudarestante").val(respuesta['deudarestante']);
              $("#cliente_fechainiciopago").val(respuesta['fechainiciopago']);
              $("#cliente_cuotaapagar").val(respuesta['cuotaapagar']);
              $("#cliente_moneda").val(respuesta['moneda']);
              $("#cliente_montoapagar").val(respuesta['montoapagar']);
              $("#tabla-cronogramaletra > tbody").html(respuesta['cronogramaletra']);
          }  
       }
     })
});
function selectletras(){
    var data = '';
    $("#tabla-cronogramaletra-primeravez tbody tr").each(function() {
        var idtipopagoletra = $(this).attr('idtipopagoletra');
        var numerounico = $("#numerounico"+idtipopagoletra).val();
        data = data+'/&/'+idtipopagoletra+'/,/'+numerounico;
    });
    return data;
}
</script>
