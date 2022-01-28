<div class="modal-content">
  <div id="carga-formproductotransferencia">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Transferencia de Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <div class="row justify-content-md-center">
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Código</label>
              <div class="col-sm-9">
                  <input type="text" class="form-control" value="{{$productotransferencia->codigo}}" disabled>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Estado</label>
              <div class="col-sm-9">
                  <select class="form-control" id="idestado" disabled>
                      <option value="1">Solicitar Productos</option>
                      <option value="2">Enviar Productos</option>
                      <option value="3">Recepcionar Productos</option>
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Motivo</label>
              <div class="col-sm-9">
                  <input type="text" class="form-control" value="{{$productotransferencia->motivo}}" id="motivo" placeholder="Opcional" disabled>
              </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-2 col-form-label">De</label>
              <div class="col-sm-10">
                  <select class="form-control" id="idtiendaorigen" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-2 col-form-label">Para</label>
              <div class="col-sm-10">
                  <select class="form-control" id="idtiendadestino" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
          </div>
        </div>
      </div>
      </form>
        <div class="table-responsive">
            <table class="table" id="tabla-productotransferencia" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>U. Medida</th>
                    <th width="80px">Cantidad</th>
                    <th width="50px">Enviado</th>
                    <th width="50px">Recepcionado</th>
                    <th width="200px">Motivo (Opcional)</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($detalletransferencia as $value)
                    <tr>
                      <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                      <td>{{$value->nombreproducto}}</td>
                      <td>{{$value->unidadmedidanombre}}</td>
                      <td>{{$value->cantidad}}</td>
                      <td>{{$value->cantidadenviado}}</td>
                      <td>{{$value->cantidadrecepcion}}</td>
                      <td>{{$value->motivo}}</td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$('#idestado').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idestado}}).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendaorigen}}).trigger('change');  
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
</script>