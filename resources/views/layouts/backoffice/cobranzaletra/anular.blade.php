<div class="modal-content">
  <form action="javascript:;" onsubmit="callback({
                                                      route: 'backoffice/cobranzaletra/{{$cobranzaletra->id}}',
                                                      method: 'PUT',
                                                      data:{
                                                          view: 'anular'
                                                      }
                                                  },
                                                  function(resultado){
                                                      location.href = '{{ url('backoffice/cobranzaletra') }}';                                                             
                                                  },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Anular Cobraza de Letra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Venta *</label>
            <div class="col-sm-10">
                <select class="form-control" id="idventa" disabled>
                  <option value="{{$cobranzaletra->idventa}}">{{$cobranzaletra->venta}}</option>
                </select>
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
        <i class="fa fa-info-circle"></i> ¿Esta seguro de Anular?
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Anular</button>
    </div>
  </form>
</div>
<script>
$('#idventa').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
</script>
