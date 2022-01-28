<?php
$idestadotransferencia = 'null';
if($productotransferencia->idestadotransferencia==1){
    if($productotransferencia->idtiendadestino==usersmaster()->idtienda){
        $idestadotransferencia = 1;
    }else{
        $idestadotransferencia = 2;
    }
}elseif($productotransferencia->idestadotransferencia==2){
    if($productotransferencia->idtiendadestino==usersmaster()->idtienda){
        $idestadotransferencia = 3;
    }else{
        $idestadotransferencia = 2;
    }
}elseif($productotransferencia->idestadotransferencia==3){
   
}  
?>
<div class="modal-content">
  <div id="carga-formproductotransferencia">
    <div class="modal-header">
        <h4 class="modal-title">Rechazar Transferencia de Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" 
              id="formproductotransferencia"
              onsubmit="callback({
                    route: 'backoffice/productotransferencia/{{$productotransferencia->id}}',
                    method: 'PUT',
                    carga: '#carga-formproductotransferencia',
                    idform: 'formproductotransferencia',
                    data: {
                       view: 'rechazar'
                    }
                },
                function(resultado){
                    location.href = '{{ url('backoffice/productotransferencia') }}';                                                                            
                },this)"> 
      <div class="row justify-content-md-center">
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Estado</label>
              <div class="col-sm-9">
                  <select class="form-control" id="idestadotransferencia" disabled>
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
                    <th width="10px">Stock</th>
                    <th width="80px">Cantidad</th>
                    <th width="200px">Motivo (Opcional)</th>
                  </tr>
                </thead>
                <tbody num="{{count($detalletransferencia)}}">
                    @foreach($detalletransferencia as $value)
                    <?php
                    $stock = stock_producto(usersmaster()->idtienda,$value->idproducto)['total'];
                    $style="background-color:#abfbab;color: #000;";
                    if($stock<=0){
                        $style="background-color:#ffafaf;color: #000;";
                    }
                    ?>
                    <tr style="{{$style}}">
                      <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                      <td>{{$value->nombreproducto}}</td>
                      <td>{{$value->unidadmedidanombre}}</td>
                      <td>{{$stock}}</td>
                      <td>{{$value->cantidad}}</td>
                      <td>{{$value->motivo}}</td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formproductotransferencia').submit();">
          Rechazar
        </a>
    </div>
  
</div>
<script>
$('#idestadotransferencia').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$idestadotransferencia}}).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendaorigen}}).trigger('change');  
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
  
</script>