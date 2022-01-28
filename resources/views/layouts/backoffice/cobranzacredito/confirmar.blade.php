<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/cobranzacredito/{{$cobranzacredito->id}}',
            method: 'PUT',
            data:{
                view: 'confirmar',
                seleccionartipopago: seleccionartipopago()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/cobranzacredito') }}';                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Confirmar Cobraza de Credito</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Venta</label>
            <div class="col-sm-10">
                <select class="form-control" id="idventa" disabled>
                  <option value="{{$cobranzacredito->idventa}}">{{$cobranzacredito->venta}}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Moneda</label>
            <div class="col-sm-8">
                <select class="form-control" id="idmoneda" disabled>
                    <option></option>
                    @foreach($monedas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
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
            <label class="col-sm-4 col-form-label">Ultima Fecha de Pago</label>
            <div class="col-sm-8">
                <input type="text" id="cliente_ultimafechapago" class="form-control" disabled>
            </div>
        </div> 
                @include('app.formapago',[
                    'formapago' => 'false',
                    'modulo' => 'cobranzacredito',
                    'idmodulo' => $cobranzacredito->id,
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
$('#idventa').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}); 
selectventa({{$cobranzacredito->idventa}});
function selectventa(idventa){
  $.ajax({
        url:"{{url('backoffice/cobranzacredito/show-seleccionarventacliente')}}",
        type:'GET',
        data: {
            idventa : idventa
       },
       success: function (respuesta){
          $("#cliente_deudarestante").val(respuesta['deudarestante']);
          $("#cliente_ultimafechapago").val(respuesta['ultimafechapago']);
          $("#idmoneda").select2({
              placeholder: "--  Seleccionar --",
              minimumResultsForSearch: -1
          }).val(respuesta['idmoneda']).trigger("change"); 
       }
     })
}
</script>
