<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Cobraza de Credito</h4>
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
                @include('app.formapago',[
                    'formapago' => 'false',
                    'modulo' => 'cobranzacredito',
                    'idmodulo' => $cobranzacredito->id,
                    'disabled' => 'true'
                ])
    </div>
</div>
<script>
$('#idventa').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$cobranzacredito->idmoneda}}).trigger("change");  
</script>
