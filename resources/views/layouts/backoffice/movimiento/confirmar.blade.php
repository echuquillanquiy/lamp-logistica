<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/movimiento/{{$movimiento->id}}',
            method: 'PUT',
            data:{
                view: 'confirmar',
                seleccionartipopago: seleccionartipopago(),
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/movimiento') }}';                                                             
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Confirmar Movimiento</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Tipo de Movimiento</label>
            <div class="col-sm-8">
                <select class="form-control" id="idtipomovimiento" disabled>
                    <option></option>
                    @foreach($tipomovimientos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Concepto</label>
            <div class="col-sm-8">
                <input type="text" value="{{$movimiento->concepto}}" id="concepto" class="form-control" disabled>
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
                    'modulo' => 'movimiento',
                    'idmodulo' => $movimiento->id,
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
$("#idtipomovimiento").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$movimiento->idtipomovimiento}}).trigger("change");

  
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$movimiento->idmoneda}}).trigger("change");
</script>
