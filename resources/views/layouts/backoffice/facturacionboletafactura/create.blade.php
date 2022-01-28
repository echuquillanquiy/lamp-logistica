<div class="modal-content">
  <div id="carga-facturacionboletafactura">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Comprobante</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <form action="javascript:;"
                  id="formfacturacionboletafactura"
                      onsubmit="callback({
                          route: 'backoffice/facturacionboletafactura',
                          method: 'POST',
                          carga: '#carga-facturacionboletafactura',
                          idform: 'formfacturacionboletafactura',
                          data:{
                              view: 'registrar',
                              productos: selectproductos(),
                          }
                      },
                      function(resultado){
                       //    location.href = '{{ url('backoffice/facturacionboletafactura') }}';                                                  
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
                            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacionboletafactura/create?view=registrar-cliente',carga:'#mx-modal-carga-cliente'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Dirección *</label>
                        <div class="col-sm-10">
                            <input type="text" id="clientedireccion" class="form-control">
                        </div>
                    </div>  
                    <div class="form-group row">
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
                        <label class="col-sm-4 col-form-label">Agencia *</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="idagencia">
                              <option></option>
                              @foreach ($agencias as $value) 
                                <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Moneda *</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="idmoneda">
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
                            <select class="form-control" id="idtipocomprobante">
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
              <div class="col-md-2"> 
              </div>
              <div class="col-md-4"> 
                  <input class="form-control" type="text" id="codigoventa" placeholder="Código de Venta" style="height: 40px;font-size: 16px;text-align: center;"/>
              </div>
              <div class="col-md-4"> 
                  <input class="form-control" type="text" id="codigoproducto" placeholder="Código de Producto" style="height: 40px;font-size: 16px;text-align: center;"/>
              </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionboletafactura" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th width="1px">Cod. Venta</th>
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
                        <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'facturacionboletafactura/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th>
                      </tr>
                    </thead>
                    <tbody num="0"></tbody>
                </table>
            </div>
            <div class="row">
            <div class="col-md-3">
            </div>
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
            </div> 
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionboletafactura').submit();">Enviar a SUNAT</a>
    </div> 
  </div>
</div>
<script> 
    //Seleccionar agencia
    $('#idagencia').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    }).val({{$agencia->id}}).trigger('change');
  
    //Tipo de moneda
    $('#idmoneda').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
  
   //Tipo de Comprobante
   $('#idtipocomprobante').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    });
  
    //Busqueda de clientes
    $('#idcliente').select2({
      ajax: {
            url:"{{url('backoffice/facturacionboletafactura/show-listarcliente')}}",
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
            url:"{{url('backoffice/facturacionboletafactura/show-seleccionarcliente')}}",
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

    //Busqueda de Ubigeo
    $('#clienteidubigeo').select2({
      ajax: {
            url:"{{url('backoffice/facturacionboletafactura/show-ubigeo')}}",
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
   
    //Codigo de ventas
    $('#codigoventa').keyup(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        var codventa = $('#codigoventa').val();
        if(code==13){
            $.ajax({
                url:"{{url('backoffice/facturacionboletafactura/show-agregarventacodigo')}}",
                type:'GET',
                data: {
                    codigoventa : codventa
                },
                success: function (respuesta){

                    var validexist = 0;
                    $("#tabla-facturacionboletafactura tbody tr").each(function() {   
                        var codigoventa = $(this).attr('codigoventa');
                        if(parseInt(codigoventa)==parseInt(codventa)){
                            validexist = 1;
                            alert('La venta, ya existe en la lista!');
                        }
                    });
                    if(validexist==0){
                        $.each( respuesta["ventadetalles"], function( key, value ) {
                            agregarproducto(
                                value.id,
                                value.idventa,
                                value.codigoventa,
                                (value.producodigoimpresion).toString().padStart(6,"0"),
                                value.productonombre,
                                value.productomotor,
                                value.productomarca,
                                value.productomodelo,
                                value.preciounitario,
                                value.idunidadmedida,
                                value.unidadmedidanombre,
                                value.stock,
                                value.cantidad,
                                0
                            );
                        });
                    }
                    $("#codigoventa").val('');  
                  }
            })
        }     
    });
  
    //Codigo de producto
    $('#codigoproducto').keyup(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            $.ajax({
                url:"{{url('backoffice/facturacionboletafactura/show-agregarproductocodigo')}}",
                type:'GET',
                data: {
                    codigoimpresion : $('#codigoproducto').val()
                },
                success: function (respuesta){
                  if(respuesta["datosProducto"]!=null){
                    var validexist = 0;
                    $("#tabla-facturacionboletafactura tbody tr").each(function() {    
                        var idproducto = $(this).attr('idproducto');
                        if(idproducto==respuesta["datosProducto"].id){
                            validexist = 1;
                            alert('El producto ya existe en la lista!');
                        }
                    });
                    if(validexist==0){
                        agregarproducto(
                          respuesta["datosProducto"].id,
                          0,
                          '',
                          (respuesta["datosProducto"].codigoimpresion).toString().padStart(6,"0"),
                          respuesta["datosProducto"].compatibilidadnombre,
                          respuesta["datosProducto"].compatibilidadmotor,
                          respuesta["datosProducto"].compatibilidadmarca,
                          respuesta["datosProducto"].compatibilidadmodelo,
                          respuesta["datosProducto"].precio,
                          respuesta["datosProducto"].idunidadmedida,
                          respuesta["datosProducto"].unidadmedidanombre,
                          respuesta["stock"],
                          '0'
                        );
                    }
                    $("#codigoproducto").val('');  
                  }
                }
            })
        }     
    });
  
    function agregarproducto(idproducto,idventa,codigoventa,codigo,nombre,motor,marca,modelo,precioventa,idunidadmedida,unidadmedidanombre,stock,cantidad,preciototal){
       $("#idproducto").html('');
       var num = $("#tabla-facturacionboletafactura tbody").attr('num');
       var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" idventa="'+idventa+'">';
           nuevaFila+='<td>'+codigoventa+'</td>';
           nuevaFila+='<td>'+codigo+'</td>';
           nuevaFila+='<td>'+nombre+'</td>';
           nuevaFila+='<td>'+motor+'</td>';
           nuevaFila+='<td>'+marca+'</td>';
           nuevaFila+='<td>'+modelo+'</td>';
           nuevaFila+='<td class="with-form-control"><select id="idunidadmedida'+num+'" disabled><option value="'+idunidadmedida+'">'+unidadmedidanombre+'</option></select></td>';
           nuevaFila+='<td class="with-form-control">'+stock+'</td>';
           nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
           nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precioventa+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0"></td>';
           nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="'+preciototal+'" step="0.01" min="0" disabled></td>';  
           nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
           nuevaFila+='</tr>';
       $("#tabla-facturacionboletafactura tbody").append(nuevaFila);
       $("#tabla-facturacionboletafactura tbody").attr('num',parseInt(num)+1);
       calcularmonto();
       $("select#idunidadmedida"+num).select2({
           placeholder: "--  Seleccionar --",
           minimumResultsForSearch: -1
       });
    }
  
    function calcularmonto(){
            var total = 0;
            $("#tabla-facturacionboletafactura tbody tr").each(function() {
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
        $("#tabla-facturacionboletafactura tbody tr").each(function() {
            var num = $(this).attr('id');        
            var idproducto = $(this).attr('idproducto');
            var idventa = $(this).attr('idventa');
            var productCant = $("#productCant"+num).val();
            var productUnidad = $("#productUnidad"+num).val();
            var idunidadmedida = $("#idunidadmedida"+num).val();
            var productTotal = $("#productTotal"+num).val();
            data = data+'&'+idproducto+','+productCant+','+productUnidad+','+idunidadmedida+','+productTotal+','+idventa;
        });
        return data;
    }

    function eliminarproducto(num){
        $("#tabla-facturacionboletafactura tbody tr#"+num).remove();
        calcularmonto();
    }
</script>