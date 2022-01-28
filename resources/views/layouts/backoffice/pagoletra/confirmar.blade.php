<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/pagoletra/{{$pagoletra->id}}',
            method: 'PUT',
            data:{
                view: 'confirmar',
                seleccionartipopago: seleccionartipopago()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/pagoletra') }}';                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Confirmar Pago de Letra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Compra *</label>
            <div class="col-sm-10">
                <select class="form-control" id="idcompra" disabled>
                  <option value="{{$pagoletra->idcompra}}">{{$pagoletra->compra}}</option>
                </select>
            </div>
        </div> 
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
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto *</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{$pagoletra->monto}}" id="monto" placeholder="0.00" step="0.01" min="0" disabled/>
            </div>
        </div>
                @include('app.formapago',[
                    'formapago' => 'false',
                    'modulo' => 'pagoletra',
                    'idmodulo' => $pagoletra->id,
                    'disabled' => 'true'
                ])
    </div>
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de confirmar?
				</div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Confirmar</button>
    </div>
</form>  
</div>
<script>
$('#idcompra').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
 
selectcompra({{$pagoletra->idcompra}});
function selectcompra(idcompra){
  $.ajax({
        url:"{{url('backoffice/pagoletra/show-seleccionarcompracliente')}}",
        type:'GET',
        data: {
            idcompra : idcompra
       },
       success: function (respuesta){
          $("#cliente_deudarestante").val(respuesta['deudarestante']);
          $("#cliente_fechainiciopago").val(respuesta['fechainiciopago']);
          $("#cliente_cuotaapagar").val(respuesta['cuotaapagar']);
          $("#cliente_moneda").val(respuesta['moneda']);
          $("#cliente_montoapagar").val(respuesta['montoapagar']);
          $("#tabla-cronogramaletra > tbody").html(respuesta['cronogramaletra']);
       }
     })
}
</script>
