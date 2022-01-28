<div class="modal-content">
  <div id="carga-compradevolucion">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Devolución de Compra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4"> 
            </div>
            <div class="col-md-4"> 
                <input class="form-control" type="text" value="{{str_pad($compradevolucion->compracodigo, 8, "0", STR_PAD_LEFT)}}" id="codigocompra" placeholder="Código de Compra" style="height: 40px;font-size: 16px;text-align: center;" disabled/>
            </div>
        </div>
            <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Proveedor</label>
                    <div class="col-sm-9">
                        <select id="idproveedor" disabled>
                            <option value="{{ $compradevolucion->idusuarioproveedor }}">{{ $compradevolucion->proveedor }}</option>
                        </select>
                    </div>
                </div> 
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Estado</label>
                    <div class="col-sm-9">
                        <select id="idestado" disabled>
                                  <option></option>
                                  <option value="1">Pedido (Orden de compra)</option>
                                  <option value="2">Compra</option>
                              </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Motivo</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" id="motivodevolucioncompra" value="{{ $compradevolucion->motivo }}" disabled/>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Comprobante</label>
                    <div class="col-sm-9">
                        <select id="idcomprobante" disabled>
                                      <option></option>
                                      @foreach($tipocomprobantes as $value)
                                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                      @endforeach
                                  </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Serie - Correlativo</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $compradevolucion->seriecorrelativo }}" id="seriecorrelativo" disabled/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fecha de Emisión</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" value="{{ $compradevolucion->fechaemision }}" id="fechaemision" disabled/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Moneda</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idmoneda" disabled>
                            <option></option>
                            @foreach($monedas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
            <div class="table-responsive">
                <table class="table" id="tabla-compradevolucion" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                        <tr>
                          <th rowspan="2" style="vertical-align: middle;">Código</th>
                          <th rowspan="2" style="vertical-align: middle;">Nombre</th>
                          <th rowspan="2" style="vertical-align: middle;">U. Medida</th>
                          <th colspan="2">COMPRA</th>
                          <th colspan="3">DEVOLUCIÓN DE COMPRA</th>
                        </tr>
                        <tr>
                          <th width="40px">Cant.</th>
                          <th width="80px">P. Unitario</th>
                          <th width="90px">Cant.</th>
                          <th width="110px">P. Unitario</th>
                          <th width="80px">P. Total</th>
                        </tr>
                    </thead>
                    <tbody num="0"></tbody>

                </table>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" id="totalcompra" placeholder="0.00" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                @include('app.formapago',[
                    'formapago' => 'false',
                    'modulo' => 'compradevolucion',
                    'idmodulo' => $compradevolucion->id,
                    'disabled' => 'true'
                ])
               </div>
            </div>
    </div>
  </div>
</div>
<script>
@foreach($compradevoluciondetalles as $value)
    agregarproducto(
      '{{$value->idproducto}}',
        '{{str_pad($value->producodigoimpresion, 8, "0", STR_PAD_LEFT)}}',
         '{{$value->nombreproducto}}',
         '{{$value->preciounitario}}',
         '{{$value->idunidadmedida}}',
         '{{$value->unidadmedidanombre}}',
         '{{$value->cantidad}}'
    );
@endforeach
  
$("#idproveedor").select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compradevolucion->idcomprobante}}).trigger("change");

$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compradevolucion->idestado}}).trigger("change");
  
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compradevolucion->idmoneda}}).trigger("change");

  
function agregarproducto(idproducto,codigo,nombre,precioventa,idunidadmedida,unidadmedida,cantidad){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-compradevolucion tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'"  idunidadmedida="'+idunidadmedida+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="with-form-control">'+unidadmedida+'</td>';
          nuevaFila+='<td class="with-form-control">'+cantidad+'</td>';
          nuevaFila+='<td class="with-form-control">'+precioventa+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()" disabled></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precioventa+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0" disabled></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" step="0.01" min="0" disabled></td>';  
          nuevaFila+='</tr>';
      $("#tabla-compradevolucion tbody").append(nuevaFila);
      $("#tabla-compradevolucion tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}
function calcularmonto(){
        var total = 0;
        $("#tabla-compradevolucion tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productUnidad = parseFloat($("#productUnidad"+num).val());
            var subtotal = (productCant*productUnidad).toFixed(2);
            $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(subtotal);
        });
          
        var total = parseFloat(total).toFixed(2);
        var subtotal = parseFloat(total/1.18).toFixed(2);
        var igv = parseFloat(total-subtotal).toFixed(2);
        $("#totalcompra").val(total); 
}
</script>