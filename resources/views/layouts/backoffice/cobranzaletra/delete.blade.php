<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/cobranzaletra/{{$cobranzaletra->id}}',
            method: 'DELETE',
            data:{
                view: 'eliminar'
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/cobranzaletra') }}';                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Cobraza de Letra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Venta</label>
            <div class="col-sm-8">
                <select class="form-control" id="idventa" disabled>
                  <option value="{{$cobranzaletra->idventa}}">{{$cobranzaletra->venta}}</option>
                </select>
            </div>
        </div> 
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Deuda restante</label>
            <div class="col-sm-8">
                <input type="text" id="cliente_deudarestante" class="form-control" disabled>
            </div>
        </div> 
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{$cobranzaletra->monto}}" id="monto" placeholder="0.00" step="0.01" min="0" disabled/>
            </div>
        </div>
                @include('app.formapago',[
                    'formapago' => 'false',
                    'modulo' => 'cobranzaletra',
                    'idmodulo' => $cobranzaletra->id,
                    'disabled' => 'true'
                ])
    </div>
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de eliminar?
				</div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Eliminar</button>
    </div>
</form>  
</div>
<script>
$('#idventa').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
 
selectventa({{$cobranzaletra->idventa}});
function selectventa(idventa){
  $.ajax({
        url:"{{url('backoffice/cobranzaletra/show-seleccionarventacliente')}}",
        type:'GET',
        data: {
            idventa : idventa
       },
       success: function (respuesta){
          $("#cliente_deudarestante").val(respuesta['deudarestante']);
          $("#cliente_ultimafechapago").val(respuesta['ultimafechapago']);
       }
     })
}
</script>
