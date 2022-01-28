<div class="modal-content">
    <form action="javascript:;"  onsubmit="callback({
                                                        route: 'backoffice/pagocredito/{{$pagocredito->id}}',
                                                        method: 'PUT',
                                                        data:{
                                                            view: 'anular'
                                                        }
                                                    },
                                                    function(resultado){
                                                        location.href = '{{ url('backoffice/pagocredito') }}';                                                             
                                                    },this)"> 
        <div class="modal-header">
            <h4 class="modal-title">Detalle de Pago de Crédito</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Compra</label>
                <div class="col-sm-10">
                    <select class="form-control" id="idcompra" disabled>
                      <option value="{{$pagocredito->idcompra}}">{{$pagocredito->compra}}</option>
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
                        'modulo' => 'pagocredito',
                        'idmodulo' => $pagocredito->id,
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
$('#idcompra').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
$("#idmoneda").select2({
    placeholder: "-- Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$pagocredito->idmoneda}}).trigger("change");  
</script>
