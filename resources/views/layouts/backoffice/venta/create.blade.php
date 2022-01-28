<div class="modal-content">
  <div id="carga-cotizacionventa">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Venta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4"> 
            </div>
            <div class="col-md-4"> 
                <input class="form-control" type="text" value="{{isset($_GET['codigoventa'])?$_GET['codigoventa']:''}}" id="codigoventa" placeholder="Código de Cotización" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
        </div>
        <div id="cont-cotizacionventa-carga"></div>
        <form action="javascript:;"
                  id="formcotizacionventa"
                      onsubmit="callback({
                          route: 'backoffice/venta',
                          method: 'POST',
                          carga: '#carga-cotizacionventa',
                          idform: 'formcotizacionventa',
                          data:{
                              view: 'registrar',
                              productos: selectproductos(),
                              seleccionartipopago: seleccionartipopago(),
                              listarcuotasletra: listarcuotasletra()
                          }
                      },
                      function(resultado){
                          location.href = '{{ url('backoffice/venta') }}';                                                  
                      },this)"> 
            <div id="cont-cotizacionventa" style="display:none;">
            <input type="hidden" id="idventa" class="form-control">
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
                            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'venta/create?view=registrar-cliente',carga:'#mx-modal-carga-cliente'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
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
                        <label class="col-sm-4 col-form-label">Agencia</label>
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
            <div class="table-responsive">
                <table class="table" id="tabla-ventacotizacion" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      
                  <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th width="10px">Stock</th>
                    <th width="50px">Cant.</th>
                    <th width="100px">P. Unitario</th>
                    <th width="100px">P. Total</th>
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
        </form> 
        </div>
    </div>
    <div id="cont-cotizacionventa-btn" style="display:none;">
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formcotizacionventa').submit();">Guardar Cambios</a>
    </div> 
    </div> 
  </div>
</div>
<script>
@if(isset($_GET['codigoventa']))
  cargarventa('{{$_GET['codigoventa']}}');
@endif
$('#codigoventa').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarventa($('#codigoventa').val())
    }     
});

function cargarventa(codigoventa){
        load('#cont-cotizacionventa-carga');
        $('#cont-cotizacionventa').css('display','none');
        $('#cont-cotizacionventa-btn').css('display','none');
        $("#idtipocomprobante").select2({
            placeholder: '-- Seleccionar --',
            minimumResultsForSearch: -1
        }).val(null).trigger("change");
        $.ajax({
            url:"{{url('backoffice/venta/show-seleccionarventa')}}",
            type:'GET',
            data: {
                codigoventa : codigoventa
            },
            success: function (respuesta){
                if(respuesta["venta"]!=undefined){
                    /*if(respuesta["venta"].idestado==1){
                        $('#cont-cotizacionventa-carga').html('<div class="alert alert-warning" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡La venta, aún sigue en cotización!</div>');
                    }else */
                    if(respuesta["venta"].idestado==2){
                        $('#idventa').val(respuesta["venta"].id);
                        $('#cont-cotizacionventa-carga').html('');
                        $('#cont-cotizacionventa').css('display','block');
                        $('#cont-cotizacionventa-btn').css('display','block');
                        $('#idcliente').html('<option value="'+respuesta["venta"].idusuariocliente+'">'+respuesta["venta"].cliente+'</option>');
                        $('#clientedireccion').val(respuesta["venta"].direccionusuariocliente);
                        $('#clienteidubigeo').html('<option value="'+respuesta["venta"].idubigeousuariocliente+'">'+respuesta["venta"].ubigeoclientenombre+'</option>');
                        $("#idagencia").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["agencia"].id).trigger("change");
                        $("#idmoneda").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["venta"].idmoneda).trigger("change");
                        $("#idformapago").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["venta"].idformapago).trigger("change");
                        $('#tabla-ventacotizacion tbody').html('');
                        $.each( respuesta["ventadetalles"], function( key, value ) {
                            agregarproducto(
                                value.id,
                                value.codigoimpresion,
                                value.nombreproducto,
                                value.stock,
                                value.cantidad,
                                value.precio,
                                value.preciototal
                            );
                        });
                    }else if(respuesta["venta"].idestado==3){
                        $('#cont-cotizacionventa-carga').html('<div class="alert alert-success" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡La venta, ya fue realizada!</div>');
                    }
                }else{
                    $('#cont-cotizacionventa-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">El Código no es valido!</div>');
                } 
            }
        })
}
  
$('#idagencia').select2({
    placeholder: '-- Seleccionar --'
});
  
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
  
$('#idmoneda').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});
  
$('#idtipocomprobante').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});

function agregarproducto(idproducto,codigoimpresion,nombreproducto,stock,cantidad,precio,preciototal){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style="background-color:#abfbab;color: #000;";
      if(stock<=0){
          var style="background-color:#ffcbcb;color: #000;";
      }
      var num = $("#tabla-ventacotizacion tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'">';
          nuevaFila+='<td>'+codigoimpresion+'</td>';
          nuevaFila+='<td>'+nombreproducto+'</td>';
          nuevaFila+='<td class="with-form-control">'+stock+'</td>';
          nuevaFila+='<td class="with-form-control">'+cantidad+'</td>';
          nuevaFila+='<td class="with-form-control">'+precio+'</td>';
          nuevaFila+='<td class="with-form-control" id="productTotal'+num+'">'+preciototal+'</td>';   
          nuevaFila+='</tr>';
      $("#tabla-ventacotizacion tbody").append(nuevaFila);
      $("#tabla-ventacotizacion tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}
function calcularmonto(){
        var total = 0;
        $("#tabla-ventacotizacion tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productTotal = parseFloat($("#productTotal"+num).html());
            total = total+parseFloat(productTotal);
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
    $("#tabla-ventacotizacion tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        data = data+'&'+idproducto;
    });
    return data;
}
</script>