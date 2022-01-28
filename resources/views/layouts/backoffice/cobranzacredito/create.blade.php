<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/cobranzacredito',
            method: 'POST',
            data:{
                view: 'registrar',
                seleccionartipopago: seleccionartipopago()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/cobranzacredito') }}';                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Cobranza de Credito</h4>
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
                    'formapago' => 'false'
                ])
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>  
</div>
<script>
$('#idventa').select2({
  ajax: {
        url:"{{url('backoffice/cobranzacredito/show-listarventacliente')}}",
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
    $.ajax({
        url:"{{url('backoffice/cobranzacredito/show-seleccionarventacliente')}}",
        type:'GET',
        data: {
            idventa : e.currentTarget.value
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
});
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}); 

</script>
