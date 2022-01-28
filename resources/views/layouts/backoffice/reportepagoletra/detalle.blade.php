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
            <label class="col-sm-4 col-form-label">Tipo de Pago</label>
            <div class="col-sm-8">
                <select class="form-control" id="idtipopago" disabled>
                    <option></option>
                    @foreach($tipopagos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="cont-tipopagodeposito" style="display:none;">
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Banco</label>
              <div class="col-sm-8">
                <select id="deposito_banco" class="form-control" style="width:100%;" disabled>
                  <option></option>
                  <?php $bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'banco.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get(); ?>
                  @foreach($bancos as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  @endforeach
                </select>
                <script>
                  $("#deposito_banco").select2({
                      placeholder: "-- Seleccionar --",
                      minimumResultsForSearch: -1
                  }).val({{$pagoletra->idbanco}}).trigger('change');
                </script>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Número de Cuenta</label>
              <div class="col-sm-8">
                 <input type="text" value="{{$pagoletra->deposito_numerocuenta}}" id="deposito_numerocuenta" class="form-control" disabled>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Fecha de Deposito</label>
              <div class="col-sm-8">
                 <input type="date" value="{{$pagoletra->deposito_fecha}}" id="deposito_fecha" class="form-control" disabled>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Hora de Deposito</label>
              <div class="col-sm-8">
                 <input type="time" value="{{$pagoletra->deposito_hora}}" id="deposito_hora" class="form-control" disabled>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Número de Operación</label>
              <div class="col-sm-8">
                 <input type="number" value="{{$pagoletra->deposito_numerooperacion}}" id="deposito_numerooperacion" class="form-control" disabled>
              </div>
            </div>
        </div>
        <div id="cont-tipopagocheque" style="display:none;">
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Banco</label>
              <div class="col-sm-8">
                <select id="cheque_banco" class="form-control" style="width:100%;" disabled>
                  <option></option>
                  <?php $bancos = DB::table('banco')->get(); ?>
                  @foreach($bancos as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  @endforeach
                </select>
                <script>
                  $("#cheque_banco").select2({
                      placeholder: "-- Seleccionar --",
                      minimumResultsForSearch: -1
                  }).val({{$pagoletra->idbanco}}).trigger('change');
                </script>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Fecha de Emisión</label>
              <div class="col-sm-8">
                 <input type="date" value="{{$pagoletra->cheque_emision}}" id="cheque_emision" class="form-control" disabled>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Fecha de Vencimiento</label>
              <div class="col-sm-8">
                 <input type="date" value="{{$pagoletra->cheque_vencimiento}}" id="cheque_vencimiento" class="form-control" disabled>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Número de Cheque</label>
              <div class="col-sm-8">
                 <input type="number" value="{{$pagoletra->cheque_numero}}" id="cheque_numero" class="form-control" disabled>
              </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{$pagoletra->monto}}" id="monto" placeholder="0.00" step="0.01" min="0" disabled/>
            </div>
        </div>
    </div>
</div>
<script>
$('#idcompra').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
  
$("#idtipopago").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    if(e.currentTarget.value==1) {
        $('#cont-tipopagodeposito').css('display','none');
        $('#cont-tipopagocheque').css('display','none');
    }else if(e.currentTarget.value==2) {
        $('#cont-tipopagodeposito').css('display','block');
        $('#cont-tipopagocheque').css('display','none');
    }else if(e.currentTarget.value==3) {
        $('#cont-tipopagodeposito').css('display','none');
        $('#cont-tipopagocheque').css('display','block');
    }
}).val({{$pagoletra->idtipopago}}).trigger('change');
</script>
