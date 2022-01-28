<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Pago de Letra</h4>
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
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto</label>
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
</div>
<script>
$('#idcompra').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
</script>
