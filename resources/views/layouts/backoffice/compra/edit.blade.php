<div class="modal-content"> 
  <div id="carga-formcompra">
    <div class="modal-header">
        <h4 class="modal-title">Editar Compra</h4>
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
                         view: 'editar',
                         productos: selectproductos(),
                         seleccionartipopago: seleccionartipopago(),
                         listarcuotasletra: listarcuotasletra(),
                         idformapago:$('#idformapago').val(),
                         totalcompra:$('#totalcompra').val(),
                         creditodias:$('#creditodias').val(),
                         creditofrecuencia:$('#creditofrecuencia').val(),
                         creditoiniciopago:$('#creditoiniciopago').val(),
                         creditoultimopago:$('#creditoultimopago').val(),
                         letraidgarante:$('#letraidgarante').val(),
                         letracuota:$('#letracuota').val(),
                         letrafechainicio:$('#letrafechainicio').val(),
                         letrafrecuencia:$('#letrafrecuencia').val(),
                         totalcompra:$('#totalcompra').val()
                    }
                },
                function(resultado){
                    location.href = '{{ url('backoffice/compra') }}';                                                  
                },this)">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Proveedor *</label>
                    <div class="col-sm-8">
                        <select id="idproveedor">
                            <option value="{{ $compra->idusuarioproveedor }}">{{ $compra->proveedoridentificacion }} - {{ $compra->proveedornombre }}</option>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'compra/create?view=registrar-proveedor',carga:'#mx-modal-carga-proveedor'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
                    </div>
                </div> 
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Estado *</label>
                    <div class="col-sm-9">
                        <select id="idestado" disabled>
                                  <option></option>
                                  <option value="1">Pedido (Orden de compra)</option>
                                  <option value="2">Compra</option>
                              </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Moneda *</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idmoneda">
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
                    <label class="col-sm-3 col-form-label">Comprobante *</label>
                    <div class="col-sm-9">
                        <select id="idcomprobante">
                                      <option></option>
                                      @foreach($comprobantes as $value)
                                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                      @endforeach
                                  </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Serie - Correlativo *</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $compra->seriecorrelativo }}" id="seriecorrelativo"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fecha de Emisión *</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" value="{{ $compra->fechaemision }}" id="fechaemision"/>
                    </div>
                </div>
        </div>
        </div>
        </form> 
        <div class="row">
            <div class="col-md-4"> 
            </div>
            <div class="col-md-4"> 
                <input class="form-control" type="text" id="codigoproducto" placeholder="Código de Barras" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped" id="tabla-compra" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Porducto</th>
                    <th width="80px">Cantidad</th>
                    <th width="110px">P. Unitario</th>
                    <th width="110px">P. Total</th>
                    <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'compra/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th>
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
                    'idmodulo' => $compra->id
                ])
            </div>
        </div> 
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formcompra').submit();">Guardar Cambios</a>
    </div>
</div>  
</div>
<script>
$("#idproveedor").select2({
    ajax: {
        url:"{{url('backoffice/compra/show-listarcliente')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
}).val({{$compra->idusuarioproveedor}}).trigger("change");
  
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
  
$('#codigoproducto').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        $.ajax({
            url:"{{url('backoffice/compra/show-agregarproductocodigo')}}",
            type:'GET',
            data: {
                codigoimpresion : $('#codigoproducto').val()
            },
            success: function (respuesta){
              if(respuesta["datosProducto"]!=null){
                var validexist = 0;
                $("#tabla-compra tbody tr").each(function() {
                    var num = $(this).attr('id');        
                    var idproducto = $(this).attr('idproducto');
                    if(idproducto==respuesta["datosProducto"].id){
                        validexist = 1;
                        alert('Ya existe en la lista!');
                    }
                });
                if(validexist==0){
                    agregarproducto(
                      respuesta["datosProducto"].id,
                      respuesta["datosProducto"].codigoimpresion,
                      respuesta["datosProducto"].nombreproducto,
                      1,
                       '0.00',
                       '0.00'
                    );
                } 
              }
            }
        })
    }     
});
  
@foreach($compradetalles as $value)
agregarproducto(
    '{{ $value->idproducto }}',
    '{{$value->codigoimpresion}}',
    '{{ $value->nombreproducto }}',
    '{{ $value->cantidad }}',
    '{{ $value->preciounitario }}',
    '{{ $value->preciototal }}');
@endforeach
  
function agregarproducto(idproducto,codigoimpresion,nombreproducto,cantidad,precio,preciototal){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-compra tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" >';
          nuevaFila+='<td>'+codigoimpresion+'</td>';
          nuevaFila+='<td>'+nombreproducto+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precio+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" onkeyup="calcularmonto(true)" onclick="calcularmonto(true)" step="0.01" min="0"></td>';   
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-compra tbody").append(nuevaFila);
      $("#tabla-compra tbody").attr('num',parseInt(num)+1);
      calcularmonto();

}

function calcularmonto(val=false){
    if(val==true){
        var total = 0;
        $("#tabla-compra tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productTotal = parseFloat($("#productTotal"+num).val());
            var subtotal = (productTotal/productCant).toFixed(2);
            $("#productUnidad"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(productTotal);
        });
        $("#totalcompra").val((parseFloat(total)).toFixed(2));  
    }else{
        var total = 0;
        $("#tabla-compra tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productUnidad = parseFloat($("#productUnidad"+num).val());
            var subtotal = (productCant*productUnidad).toFixed(2);
            $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(subtotal);
        });
        $("#totalcompra").val((parseFloat(total)).toFixed(2));  
    } 
}

function selectproductos(){
    var data = '';
    $("#tabla-compra tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var idunidadmedida = 1;
        var productTotal = $("#productTotal"+num).val();
        data = data+'&'+idproducto+','+productCant+','+productUnidad+','+idunidadmedida+','+productTotal;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-compra tbody tr#"+num).remove();
    calcularmonto();
}
</script>