<div class="modal-content">
  <div id="carga-formproductomovimiento">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Movimiento de Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <div class="row justify-content-md-center">
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Tipo de Movimiento</label>
              <div class="col-sm-9">
                  <select class="form-control" id="idestado" disabled>
                      <option value="1">Ingreso</option>
                      <option value="2">Salida</option>
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Motivo</label>
              <div class="col-sm-9">
                  <input type="text" class="form-control" value="{{$productomovimiento->motivo}}" id="motivo" placeholder="Opcional" disabled>
              </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-2 col-form-label">Tienda</label>
              <div class="col-sm-10">
                  <select class="form-control" id="idtienda" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
          </div>
          </div>
        </div>
      </div>
      </form>
        <div class="table-responsive">
            <table class="table" id="tabla-productomovimiento" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Motor</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>U. Medida</th>
                    <th width="80px">Cantidad</th>
                    <th width="200px">Motivo (Opcional)</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($detalletransferencia as $value)
                    <tr>
                      <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                      <td>{{$value->productonombre}}</td>
                      <td>{{$value->productomotor}}</td>
                      <td>{{$value->productomarca}}</td>
                      <td>{{$value->productomodelo}}</td>
                      <td>{{$value->unidadmedidanombre}}</td>
                      <td>{{$value->cantidad}}</td>
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
}).val({{$productomovimiento->idestado}}).trigger('change'); 
  
$('#idtienda').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productomovimiento->idtienda}}).trigger('change'); 
</script>