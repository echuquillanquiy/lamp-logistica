<div class="modal-content">
  <div id="carga-formcotizacion">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Venta rapida</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>

    <div class="modal-body">
        <form action="javascript:;"
              id="formcotizacion"
                  onsubmit="callback({
                      route: 'backoffice/venta',
                      method: 'POST',
                      carga: '#carga-formcotizacion',
                      idform: 'formcotizacion',
                      data:{
                          view: 'registrarventarapida',
                          productos: selectproductos(),
                              seleccionartipopago: seleccionartipopago(),
                              listarcuotasletra: listarcuotasletra(),
                              idformapago: $('#idformapago').val(),
                              totalventa: $('#totalventa').val()
                      }
                  },
                  function(resultado){
                             $('#mx-modal').modal('hide');
                            $('#mx-modal').remove();
                            $('.modal-backdrop').remove();
                            modal({route:'venta/'+resultado['idventa']+'/edit?view=ticketventa'})

                  },this)"> 
          <div class="row">
            <div class="col-md-7"> 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cliente *</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idcliente">
                          <option></option>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cotizacion/create?view=registrar-cliente',carga:'#mx-modal-carga-cliente'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
                    </div>
                        <label class="col-sm-2 col-form-label">Dirección *</label>
                        <div class="col-sm-10">
                            <input type="text" id="clientedireccion" class="form-control">
                        </div>
                        <label class="col-sm-2 col-form-label">Ubigeo *</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="clienteidubigeo">
                              <option></option>
                            </select>
                        </div>
                </div>   
            </div>
                <div class="col-md-5"> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Agencia</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="idagencia" disabled>
                              <option></option>
                              @foreach ($agencias as $value) 
                                <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Moneda</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="idmoneda" disabled>
                              <option></option>
                              @foreach ($monedas as $value) 
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Comprobante *</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="idtipocomprobante" disabled>
                              <option></option>
                              @foreach ($tipocomprobantes as $value) 
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                              @endforeach
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
                    <th width="100px">Cantidad </th>
                    <th width="100px">P. Unitario</th>
                    <th width="100px">P. Total</th>
                    <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'cotizacion/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th>
                  </tr>
                </thead>
                <tbody num="0"></tbody>
            </table>
        </div>
            <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Sub Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" id="subtotalventa" placeholder="0.00" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">IGV (18%)</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" id="igvventa" placeholder="0.00" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                @include('app.formapago')
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formcotizacion').submit();">Guardar Cambios</a>
    </div> 
  </div> 
</div>
<script>
$('#idcliente').select2({
    ajax: {
        url:"{{url('backoffice/venta/show-listarcliente')}}",
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
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/venta/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       },
       success: function (respuesta){
          $("#clientedireccion").val(respuesta['cliente'].direccion);
          $('#clienteidubigeo').html('<option value="'+respuesta["cliente"].idubigeo+'">'+respuesta["cliente"].ubigeonombre+'</option>');
       }
     })
}); 
 $('#clienteidubigeo').select2({
  ajax: {
        url:"{{url('backoffice/venta/show-ubigeo')}}",
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
 
$('#idagencia').select2({
    placeholder: '-- Seleccionar --'
}).val(5).trigger('change');
  
$('#idmoneda').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val(1).trigger('change');
  
$('#idtipocomprobante').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val(1).trigger('change');
  
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
function agregarproducto(idproducto,codigoimpresion,nombreproducto,precio,preciototal,stock,cantidad){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style="";
      if(stock<=0){
          var style="background-color:#ffcbcb;color: #000;";
      }
      var num = $("#tabla-cotizacion tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'">';
          nuevaFila+='<td>'+codigoimpresion+'</td>';
          nuevaFila+='<td>'+nombreproducto+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="1" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precio+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" step="0.01" min="0" disabled></td>';   
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-cotizacion tbody").append(nuevaFila);
      $("#tabla-cotizacion tbody").attr('num',parseInt(num)+1);
      calcularmonto();
  
      $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
}

function calcularmonto(){
        var total = 0;
        $("#tabla-cotizacion tbody tr").each(function() {
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
    
        $("#subtotalventa").val(subtotal); 
        $("#igvventa").val(igv); 
        $("#totalventa").val(total); 
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