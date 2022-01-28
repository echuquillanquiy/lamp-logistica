<div class="modal-content">
  <div id="carga-formcotizacion">
    <div class="modal-header">
        <h4 class="modal-title">Eliminar Cotización</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" 
            id="formcotizacion"
                onsubmit="callback({
                    route: 'backoffice/cotizacion/{{$venta->id}}',
                    method: 'DELETE',
                    carga: '#carga-formcotizacion',
                    idform: 'formcotizacion',
                    data:{
                        view: 'eliminar'
                    }
                },
                function(resultado){
                    location.href = '{{ url('backoffice/cotizacion') }}';                                                  
                },this)"> 
          <div class="row">
            <div class="col-md-6"> 
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Cliente *</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="idcliente" disabled>
                          <option value="{{$venta->idusuariocliente}}">{{$venta->cliente}}</option>
                        </select>
                    </div>
                </div>    
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Forma de Pago *</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="idformapago" disabled>
                          <option></option>
                          @foreach ($formapagos as $value) 
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                        </select>
                    </div>
                </div> 
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Estado *</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="idestado" disabled>
                          <option></option>
                          <option value="1">Cotización</option>
                          <option value="2">Venta</option>
                        </select>
                    </div>
                </div>
            </div>
          </div>
        </form> 
        <div class="table-responsive">
            <table class="table" id="tabla-cotizacion" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Motor</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>U. Medida</th>
                    <th width="10px">Stock</th>
                    <th width="80px">Cantidad</th>
                    <th width="110px">P. Unitario</th>
                    <th width="110px">P. Total</th>
                  </tr>
                </thead>
                <tbody num="{{count($ventadetalles)}}">
                <?php 
                  $i=0;
                  $total=0;
                ?>
                @foreach($ventadetalles as $value)
                  <?php 
                  $subtotal=number_format($value->cantidad*$value->preciounitario, 2, '.', '');
                  $total=$total+$subtotal;
                  $stock = stock_producto(usersmaster()->idtienda,$value->idproducto)['total'];
                  
                  $ventas = DB::table('ventadetalle')
                      ->join('venta','venta.id','ventadetalle.idventa')
                      ->where('ventadetalle.idproducto',$value->idproducto)
                      ->where('venta.idestado',2)
                      ->sum('ventadetalle.cantidad');
          
                  $stock = $stock-$ventas;
                  ?>
                  <tr>
                    <td>{{ str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ $value->productonombre }}</td>
                    <td>{{ $value->productomotor }}</td>
                    <td>{{ $value->productomarca }}</td>
                    <td>{{ $value->productomodelo }}</td>
                    <td>{{ $value->unidadmedidanombre }}</select></td>
                    <td>{{$stock}}</td>
                    <td>{{ $value->cantidad }}</td>
                    <td>{{ $value->preciounitario }}</td>
                    <td>{{ $subtotal }}</td>   
                  </tr>
                @endforeach
                </tbody>

            </table>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Total</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" id="totalventa" value="{{number_format($total, 2, '.', '')}}" placeholder="0.00" disabled>
            </div>
        </div>
    </div>
    <div class="modal-footer">
       <a href="javascript:;" class="btn btn-success" onclick="$('#formcotizacion').submit();">Eliminar</a>
    </div>
  </div>
</div>
<script>
$('#idagencia').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idagencia}}).trigger('change');
  
$('#idcliente').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
  
$('#idformapago').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idformapago}}).trigger('change');

$('#idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idestado}}).trigger('change');
</script>