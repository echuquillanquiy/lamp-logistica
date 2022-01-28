<div class="modal-content">
  <div id="carga-formcotizacion">
    <div class="modal-header">
        <h4 class="modal-title">Editar Cotización</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" 
            id="formcotizacion"
                onsubmit="callback({
                    route: 'backoffice/cotizacion/{{$venta->id}}',
                    method: 'PUT',
                    carga: '#carga-formcotizacion',
                    idform: 'formcotizacion',
                    data:{
                        view: 'editar',
                        productos: selectproductos()
                    }
                },
                function(resultado){
                     location.href = '{{ url('backoffice/cotizacion') }}';                                                  
                },this)"> 
          <div class="row">
            <div class="col-md-7"> 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cliente *</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idcliente">
                          <option value="{{$venta->idusuariocliente}}">{{$venta->cliente}}</option>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cotizacion/create?view=registrar-cliente',carga:'#mx-modal-carga-cliente'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
                    </div>
                </div>    
            </div>
            <div class="col-md-5">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Forma de Pago *</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idformapago">
                          <option></option>
                          @foreach ($formapagos as $value) 
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                        </select>
                    </div>
                </div> 
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Estado *</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idestado">
                          <option></option>
                          <option value="1">Cotización</option>
                          <option value="2">Venta</option>
                        </select>
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
            <table class="table" id="tabla-cotizacion" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th width="10px">Stock</th>
                    <th width="80px">Cantidad</th>
                    <th width="110px">P. Unitario</th>
                    <th width="110px">P. Total</th>
                    <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cotizacion/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th>
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
                      ->where('venta.idtienda',usersmaster()->idtienda)
                      ->where('ventadetalle.idproducto',$value->idproducto)
                      ->where('venta.id','<>',$venta->id)
                      ->where('venta.idestado',2)
                      ->sum('ventadetalle.cantidad');
          
                  $stock = $stock-$ventas;
                  
                  $style="";
                  if($stock<=0){
                      $style="background-color:#ffcbcb;color: #000;";
                  }
                  ?>
                  <tr id="{{$i}}" idproducto="{{ $value->idproducto }}" style="{{$style}}">
                    <td>{{ str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ $value->productonombre }}</td>
                    <td class="with-btn"><a href="javascript:;" onclick="modal({route:'cotizacion/create?idproducto={{ $value->idproducto }}&view=producto-cotizado',carga:'#mx-modal-carga-productocotizado'})" 
                                            class="btn btn-default big-btn">{{$stock}}</a></td>
                    <td class="with-form-control"><input class="form-control" id="productCant{{$i}}" type="number" value="{{ $value->cantidad }}" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>
                    <td class="with-form-control"><input class="form-control" id="productUnidad{{$i}}" type="number" value="{{ $value->preciounitario }}" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0"></td>
                    <td class="with-form-control"><input class="form-control" id="productTotal{{$i}}" type="text" value="{{ $subtotal }}" step="0.01" min="0" disabled></td>   
                    <td class="with-btn"><a id="del{{$i}}" href="javascript:;" onclick="eliminarproducto({{$i}})" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>
                  </tr>
                  <script>
                  $("select#idunidadmedida{{$i}}").select2({
                      placeholder: "--  Seleccionar --",
                      minimumResultsForSearch: -1
                  });
                  </script>
                <?php $i++ ?>
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
       <a href="javascript:;" class="btn btn-success" onclick="$('#formcotizacion').submit();">Guardar Cambios</a>
    </div>
  </div>
</div>
<script>
$('#idagencia').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idagencia}}).trigger('change');
  
$('#idcliente').select2({
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
});
  
$('#idformapago').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idformapago}}).trigger('change');

$('#idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idestado}}).trigger('change');
  
$('#codigoproducto').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        $.ajax({
            url:"{{url('backoffice/cotizacion/show-agregarproductocodigo')}}",
            type:'GET',
            data: {
                codigoimpresion : $('#codigoproducto').val()
            },
            success: function (respuesta){
              if(respuesta["datosProducto"]!=null){
                var validexist = 0;
                $("#tabla-cotizacion tbody tr").each(function() {
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
                      respuesta["datosProducto"].precio,
                      '0.00',
                      respuesta["stock"],
                      1
                    );
                }
                    
              }
            }
        })
    }     
});

function agregarproducto(idproducto,codigo,nombre,precio,preciototal,stock,cantidad){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style="";
      if(stock<=0){
          var style="background-color:#ffcbcb;color: #000;";
      }
      var num = $("#tabla-cotizacion tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="with-btn"><a href="javascript:;" onclick="modal({route:\'cotizacion/create?idproducto='+idproducto+'&view=producto-cotizado\',carga:\'#mx-modal-carga-productocotizado\'})" class="btn btn-default big-btn">'+stock+'</a></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precio+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" step="0.01" min="0" disabled></td>';   
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-cotizacion tbody").append(nuevaFila);
      $("#tabla-cotizacion tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}

function calcularmonto(val=false){
    if(val==true){
        /*var total = 0;
        $("#tabla-cotizacion tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productTotal = parseFloat($("#productTotal"+num).val());
            var subtotal = (productTotal/productCant).toFixed(2);
            $("#productUnidad"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(productTotal);
        });
        $("#totalventa").val((parseFloat(total)).toFixed(2));  */
    }else{
        var total = 0;
        $("#tabla-cotizacion tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productUnidad = parseFloat($("#productUnidad"+num).val());
            var subtotal = (productCant*productUnidad).toFixed(2);
            $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(subtotal);
        });
        $("#totalventa").val((parseFloat(total)).toFixed(2)); 
    } 
}

function selectproductos(){
    var data = '';
    $("#tabla-cotizacion tbody tr").each(function() {
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
    $("#tabla-cotizacion tbody tr#"+num).remove();
    calcularmonto();
}
</script>