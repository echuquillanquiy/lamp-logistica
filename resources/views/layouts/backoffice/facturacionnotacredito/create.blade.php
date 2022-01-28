<div class="modal-content">
  <div id="carga-facturacionnotacredito">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Nota de Crédito</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-3"> 
            </div>
            <div class="col-md-3 mx-select2"> 
                <select class="form-control" id="facturador_serie">
                    <option></option>
                    @foreach($series as $value) 
                        <option value="B{{ str_pad($value->facturador_serie, 3, "0", STR_PAD_LEFT) }}">B{{ str_pad($value->facturador_serie, 3, "0", STR_PAD_LEFT) }}</option>
                    @endforeach
                    @foreach($series as $value) 
                        <option value="F{{ str_pad($value->facturador_serie, 3, "0", STR_PAD_LEFT) }}">F{{ str_pad($value->facturador_serie, 3, "0", STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"> 
                <input class="form-control" type="text" id="facturador_correlativo" placeholder="Correlativo" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
        </div>
            <style>
              .mx-select2 .select2-container {
                  text-align: center;
              }
              .mx-select2 .select2-container .select2-selection--single, .select2-container--default .select2-selection--multiple {
                  height: 40px;
              }
              .mx-select2 .select2-container--default .select2-selection--single .select2-selection__rendered {
                  line-height: 38px !important;
                  font-size: 16px;
              }
            </style>
        <form action="javascript:;"
                  id="formfacturacionnotacredito"
                      onsubmit="callback({
                          route: 'backoffice/facturacionnotacredito',
                          method: 'POST',
                          carga: '#carga-facturacionnotacredito',
                          idform: 'formfacturacionnotacredito',
                          data:{
                              view: 'registrar',
                              tipoemisionnotacredito: $('#tipoemisionnotacredito').val(),
                              productos: selectproductos(),
                          }
                      },
                      function(resultado){
                           location.href = '{{ url('backoffice/facturacionnotacredito') }}';                                                  
                      },this)"> 
            <div id="cont-facturacionnotacredito" style="display:none;">
            <input type="hidden" id="idboletafacturaventa" class="form-control">
            <div class="row">
                <div class="col-md-7"> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Agencia</label>
                        <div class="col-sm-10">
                            <input type="text" id="agencia" class="form-control" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Cliente</label>
                        <div class="col-sm-10">
                            <input type="text" id="cliente" class="form-control" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Moneda</label>
                        <div class="col-sm-10">
                            <input type="text" id="moneda" class="form-control" disabled>
                        </div>
                    </div>   
                </div>
                <div class="col-md-5">  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha Emisión</label>
                        <div class="col-sm-8">
                            <input type="text" id="fechaemision" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Comprobante</label>
                        <div class="col-sm-8">
                            <input type="text" id="comprobante" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Serie - Correlativo</label>
                        <div class="col-sm-8">
                            <input type="text" id="seriecorrelativo" class="form-control" disabled>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-sm-12 mx-select2">
                            <select class="form-control" id="idmotivonotacredito" disabled>
                              <option></option>
                              @foreach ($motivonotacreditos as $value) 
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <input class="form-control" type="text" id="motivonotacredito_descripcion" placeholder="Motivo *" style="height: 40px;font-size: 16px;text-align: center;"/>
                </div>
            </div> 
            <div id="cont-tabla-facturacionnotacredito" style="display:none;">
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionnotacredito" style="margin-bottom: 5px;">
                     <thead class="thead-dark">
                        <tr>
                          <th rowspan="2" style="vertical-align: middle;">Código</th>
                          <th rowspan="2" style="vertical-align: middle;">Descripción</th>
                          <th rowspan="2" style="vertical-align: middle;">U. Medida</th>
                          <th colspan="2">VENTA</th>
                          <th colspan="3">NOTA DE CREDITO</th>
                          <th width="10px" class="with-btn" rowspan="2">
                            <a href="javascript:;" class="btn btn-warning" 
                               onclick="modal({route:'facturacionnotacredito/create?view=facturacionnotacreditodetalle&idboletafacturaventa='+$('#idboletafacturaventa').val(),size:'modal-fullscreen',carga:'#carga-facturacionnotacreditodetalle'})">
                              <i class="fas fa-plus"></i> Agregar</a>
                          </th>
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
                <div class="col-md-3">
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
                        </div>
                    </div>
                </div>
            </div> 
            </div>
            </div>
        </form> 
        <div id="cont-facturacionnotacredito-carga"></div>
    </div>
    <div id="cont-facturacionnotacredito-btn" style="display:none;">
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionnotacredito').submit();">Enviar a SUNAT</a>
    </div> 
    </div> 
  </div>
</div>
<script>
$('#facturador_correlativo').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarventa_boletafactura($('#facturador_serie').val(),$('#facturador_correlativo').val())
    }     
});
$('#venta_codigo').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarventa_venta($('#venta_codigo').val())
    }     
});

function cargarventa_boletafactura(facturador_serie,facturador_correlativo){
        $('#idventa').val('');
        $('#cont-tipoemisionnotacredito-facturaboleta-advertencia').css('display','none');
        $('#cont-tipoemisionnotacredito-venta-advertencia').css('display','none');
        load('#cont-facturacionnotacredito-carga');
        $('#cont-facturacionnotacredito').css('display','none');
        $('#cont-facturacionnotacredito-btn').css('display','none');
        $("#idmotivonotacredito").select2({
            placeholder: '-- Seleccionar --',
            minimumResultsForSearch: -1
        }).val(7).trigger("change");
        $.ajax({
            url:"{{url('backoffice/facturacionnotacredito/show-seleccionarboletafactura')}}",
            type:'GET',
            data: {
                facturador_serie : facturador_serie,
                facturador_correlativo : facturador_correlativo
            },
            success: function (respuesta){
                if(respuesta["facturacionboletafactura"]!=undefined){
                      $('#idventa').val(respuesta['facturacionboletafactura'].idventa);
                    /*if(respuesta["facturacionboletafactura"].idventa!=0){
                        $('#cont-facturacionnotacredito-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">La Boleta/Factura, tiene una Venta ('+(respuesta["facturacionboletafactura"].ventacodigo).padStart(8,"0")+')!</div>');
                    }else{*/
                        $('#idboletafacturaventa').val(respuesta["facturacionboletafactura"].id);
                        $('#cont-facturacionnotacredito-carga').html('');
                        $('#cont-facturacionnotacredito').css('display','block');
                        $('#cont-facturacionnotacredito-btn').css('display','block');
                        $('#cont-tabla-facturacionnotacredito').css('display','block');
                  
                        $('#agencia').val(respuesta["facturacionboletafactura"].emisor_ruc+' - '+respuesta["facturacionboletafactura"].emisor_razonsocial);
                        $('#cliente').val(respuesta["facturacionboletafactura"].cliente_numerodocumento+' - '+respuesta["facturacionboletafactura"].cliente_razonsocial);
                        $('#moneda').val(respuesta["facturacionboletafactura"].venta_tipomoneda);
                        $('#fechaemision').val(respuesta["facturacionboletafactura"].venta_fechaemision);
                        $('#comprobante').val(respuesta["facturacionboletafactura"].venta_tipodocumento);
                        $('#seriecorrelativo').val(respuesta["facturacionboletafactura"].venta_serie+' - '+respuesta["facturacionboletafactura"].venta_correlativo);
                    //}
                }else{
                    $('#cont-facturacionnotacredito-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">No existe la Boleta/Factura!</div>');
                } 
            }
        })
}

$('#tipoemisionnotacredito').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-tipoemisionnotacredito-facturaboleta').css('display','none');
    $('#cont-facturacionnotacredito').css('display','none');
    $('#cont-facturacionnotacredito-btn').css('display','none');
    $('#cont-facturacionnotacredito-carga').html('');
    $('#cont-tipoemisionnotacredito-facturaboleta').css('display','block');
}).val(1).trigger('change');
  
$('#facturador_serie').select2({
    placeholder: '-- Serie --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-facturacionnotacredito').css('display','none');
    $('#cont-facturacionnotacredito-btn').css('display','none');
});
$('#idmotivonotacredito').select2({
    placeholder: '-- Seleccionar Motivo --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-tabla-facturacionnotacredito').css('display','none');
}).val(7).trigger('change');

function agregarproducto(idfacturacionboletafacturadetalle,codigo,descripcion,precioventa,unidad,cantidad,idmotivonotacredito){
      var validexist = 0;
      $("#tabla-facturacionnotacredito tbody tr").each(function() {      
          var idfacturacionboletafacturadetalle_ant =  $(this).attr('idfacturacionboletafacturadetalle');
          if(idfacturacionboletafacturadetalle_ant == idfacturacionboletafacturadetalle){
              validexist = 1;
              alert('Ya existe en la lista!');
          }
      });
  
      if(validexist==1){
          return false;
      }
      $('#btnseleccionar'+idfacturacionboletafacturadetalle)
        .css('background-color','rgb(11, 115, 11)')
        .css('border-color','rgb(11, 115, 11)')
        .html('<i class="fas fa-plus"></i> Seleccionado</a>');
  
      var num = $("#tabla-facturacionnotacredito tbody").attr('num');
      var disabledcantidad = '';
      var disabledprecio = '';
      if(idmotivonotacredito==5){
      }else if(idmotivonotacredito==7){
          var disabledprecio = 'disabled';
      }else{
          var disabledcantidad = 'disabled';
          var disabledprecio = 'disabled';
      }
      var btneliminar = '';
      if(idmotivonotacredito==5 || idmotivonotacredito==7){
          btneliminar = '<a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a>';
      }
  
      var nuevaFila='<tr id="'+num+'" idfacturacionboletafacturadetalle="'+idfacturacionboletafacturadetalle+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+descripcion+'</td>';
          nuevaFila+='<td>'+unidad+'</td>';
          nuevaFila+='<td>'+cantidad+'</td>';
          nuevaFila+='<td>'+precioventa+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()" '+disabledcantidad+'></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precioventa+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0" '+disabledprecio+'></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" step="0.01" min="0" disabled></td>';  
          nuevaFila+='<td class="with-btn" width="10px">'+btneliminar+'</td>'
          nuevaFila+='</tr>';
      $("#tabla-facturacionnotacredito tbody").append(nuevaFila);
      $("#tabla-facturacionnotacredito tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}
function calcularmonto(){
        var total = 0;
        $("#tabla-facturacionnotacredito tbody tr").each(function() {
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
    var data = [];
    $("#tabla-facturacionnotacredito tbody tr").each(function() {
        var num = $(this).attr('id');  
        data.push({
          idfacturacionboletafacturadetalle: $(this).attr('idfacturacionboletafacturadetalle'),
          productCant:                       $("#productCant"+num).val(),
          productUnidad:                     $("#productUnidad"+num).val(),
          productTotal:                      $("#productTotal"+num).val(),
        });
    });
    return JSON.stringify(data);
}
  
function eliminarproducto(num){
    $("#tabla-facturacionnotacredito tbody tr#"+num).remove();
    calcularmonto();
}
</script>