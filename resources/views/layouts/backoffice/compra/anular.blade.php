<div class="modal-content"> 
  <div id="carga-formcompra">
    <div class="modal-header">
        <h4 class="modal-title">Anular Compra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" 
             id="formcompra"
                onsubmit="callback({
                    route: 'backoffice/compra/{{ $compra->id }}',
                    method: 'PUT',
                    carga: '#carga-formcompra',
                    idform: 'formcompra',
                    data:{
                        view: 'anular'
                    }
                },
                function(resultado){
                    location.href = '{{ url('backoffice/compra') }}';                                                  
                },this)">
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Proveedor</label>
                    <div class="col-sm-9">
                        <select id="idproveedor" disabled>
                                  <option value="{{ $compra->idusuarioproveedor }}">{{ $compra->proveedoridentificacion }} - {{ $compra->proveedornombre }}</option>
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
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Comprobante</label>
                    <div class="col-sm-9">
                        <select id="idcomprobante" disabled>
                                      <option></option>
                                      @foreach($comprobantes as $value)
                                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                      @endforeach
                                  </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Serie - Correlativo</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $compra->seriecorrelativo }}" id="seriecorrelativo" disabled/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fecha de Emisión</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" value="{{ $compra->fechaemision }}" id="fechaemision" disabled/>
                    </div>
                </div>
            </div>
        </div>
        </form> 
        <div class="table-responsive">
            <table class="table table-striped" id="tabla-compra" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th width="110px">P. Unitario</th>
                    <th width="110px">P. Total</th>
<!--                     <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'compra/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th> -->
                  </tr>
                </thead>
                <tbody num="0"></tbody>

            </table>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="totalcompra" placeholder="0.00" disabled>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                @include('app.formapago',[
                    'modulo' => 'compra',
                    'idmodulo' => $compra->id,
                    'disabled' => 'true'
                ])
            </div>
        </div> 
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de Anular?
				</div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formcompra').submit();">Anular</a>
    </div>
</div>  
</div>
<script>
$("#idproveedor").select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->idcomprobante}}).trigger("change");

$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->idestado}}).trigger("change"); 
 
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->idmoneda}}).trigger("change");
  
@foreach($compradetalles as $value)
agregarproducto(
  '{{ $value->idproducto }}',
  '{{ str_pad($value->codigoimpresion, 6, "0", STR_PAD_LEFT) }}',
  '{{ $value->nombreproducto }}',
'{{ $value->cantidad }}',
'{{ $value->preciounitario }}',
'{{ $value->preciototal }}');
@endforeach
  
function agregarproducto(idproducto,codigoimpresion,nombreproducto,cantidad,preciounitario,preciototal){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-compra tbody").attr('num');
       var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" >';
          nuevaFila+='<td>'+codigoimpresion+'</td>';
          nuevaFila+='<td>'+nombreproducto+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" disabled></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+preciounitario+'" step="0.01" min="0" disabled></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="'+preciototal+'" step="0.01" min="0" disabled></td>';   
          nuevaFila+='</tr>';
      $("#tabla-compra tbody").append(nuevaFila);
      $("#tabla-compra tbody").attr('num',parseInt(num)+1);
  
      $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
}
</script>