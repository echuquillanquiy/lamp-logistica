<div class="modal-content">
  <div id="carga-compradevolucion">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Devolución de Compra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4"> 
            </div>
            <div class="col-md-4"> 
                <input class="form-control" type="text" value="{{isset($_GET['codigocompra'])?$_GET['codigocompra']:''}}" id="codigocompra" placeholder="Código de Compra" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
        </div>
        <div id="cont-compradevolucion-carga"></div>
        <form action="javascript:;"
                  id="formcotizacioncompradevolucion"
                      onsubmit="callback({
                          route: 'backoffice/compradevolucion',
                          method: 'POST',
                          carga: '#carga-compradevolucion',
                          idform: 'formcotizacioncompradevolucion',
                          data:{
                              view: 'registrar',
                              productos: selectproductos(),
                              seleccionartipopago: seleccionartipopago(),
                              listarcuotasletra: listarcuotasletra(),
                          }
                      },
                      function(resultado){
                           location.href = '{{ url('backoffice/compradevolucion') }}';                                                  
                      },this)"> 
            <div id="cont-compradevolucion" style="display:none;">
            <input type="hidden" id="idcompra" class="form-control">
              
            <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Proveedor</label>
                    <div class="col-sm-9">
                        <select id="idproveedor" disabled>
                            <option></option>
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
                        <label class="col-sm-3 col-form-label">Forma de Pago</label>
                        <div class="col-sm-9">
                            <input type="text" id="formapago" class="form-control" disabled>
                        </div>
                    </div>  
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Motivo *</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" id="motivodevolucioncompra"/>
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
                        <input class="form-control" type="text" id="seriecorrelativo" disabled/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fecha de Emisión</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" id="fechaemision" disabled/>
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
                          <th rowspan="2" style="vertical-align: middle;">Descripción</th>
                          <th rowspan="2" style="vertical-align: middle;">U. Medida</th>
                          <th colspan="2">COMPRA</th>
                          <th colspan="3">DEVOLUCIÓN</th>
                          <th width="10px" class="with-btn" rowspan="2">
                            <a href="javascript:;" class="btn btn-warning" 
                               onclick="modal({route:'compradevolucion/create?view=compradevoluciondetalle&idcompra='+$('#idcompra').val(),size:'modal-fullscreen',carga:'#carga-compradevoluciondetalle'})">
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
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total Devolución</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" id="totalcompra" placeholder="0.00" value="0.00" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total Compra</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" id="totalapagar" placeholder="0.00" value="0.00" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total Pagado</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" id="totalpagado" placeholder="0.00" value="0.00" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="cont-opcionsaldo" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Total a Devolver</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" id="totaladevolver" placeholder="0.00" value="0.00" disabled>
                            </div>
                        </div>
                        @include('app.formapago',[
                            'formapago' => 'false'
                        ])
                    </div>
                 </div>
              </div>
        </form> 
        </div>
    </div>
    <div id="cont-compradevolucion-btn" style="display:none;">
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formcotizacioncompradevolucion').submit();">Guardar Cambios</a>
    </div> 
    </div> 
  </div>
</div>
<script>
$('#codigocompra').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarcompra_compra($('#codigocompra').val())
    }     
});
  
function cargarcompra_compra(compra_codigo){
        load('#cont-compradevolucion-carga');
        $('#cont-compradevolucion').css('display','none');
        $('#cont-compradevolucion-btn').css('display','none');
        $('#tabla-compradevolucion > tbody').html('');
        calcularmonto();
        $.ajax({
            url:"{{url('backoffice/compradevolucion/show-seleccionarcompra')}}",
            type:'GET',
            data: {
                compra_codigo : compra_codigo
            },
            success: function (respuesta){
                 if(respuesta["compra"] != undefined){
                  if(respuesta["valid_productos"]>0){
                    if(respuesta["compra"].idformapago==3){
                        if(respuesta["countcobranzaletra"] > 0){
                            $('#cont-compradevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡Esta Compra a Letra, tiene cobranzas realizadas!</div>');
                        }else{
                            $('#cont-compradevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡No estan permitidas emitir devolución de Compra a Letras por este medio!</div>');     
                        }
                    }else if(respuesta["compra"].idformapago==1 || respuesta["compra"].idformapago==2){
                        $('#idcompra').val(respuesta["compra"].id);
                        $('#cont-compradevolucion-carga').html('');
                        $('#cont-compradevolucion').css('display','block');
                        $('#cont-compradevolucion-btn').css('display','block');
                        $('#idproveedor').html('<option value="'+respuesta["compra"].idusuarioproveedor+'">'+respuesta["compra"].proveedor+'</option>');
                        $("#idestado").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["compra"].idestado).trigger("change");
                        $("#idmoneda").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["compra"].idmoneda).trigger("change");
                        $("#idcomprobante").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["compra"].idcomprobante).trigger("change");
                        $('#seriecorrelativo').val(respuesta["compra"].seriecorrelativo);
                        $('#fechaemision').val(respuesta["compra"].fechaemision);
                      
                        $('#formapago').val(respuesta["compra"].nombreFormapago);
                        $('#totalpagado').val(respuesta["totalpagado"]);
                        $('#totalapagar').val(respuesta["totalapagar"]);
                    }
                  }else{
                      $('#cont-compradevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡La compra no tiene productos a devolver!</div>');
                  }
                }else{
                    $('#cont-compradevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡No existe la Compra!</div>');
                } 
         }
       })
}
  
  
$("#idproveedor").select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});

$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val(1).trigger("change");
  
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});

function agregarproducto(idcompradetalle,codigo,descripcion,preciocompra,unidad,cantidad,idmotivonotacredito){
      
      var validexist = 0;
      $("#tabla-compradevolucion tbody tr").each(function() {      
          var idcompradetalle_ant = $(this).attr('idcompradetalle');
          if(idcompradetalle_ant==idcompradetalle){
              validexist = 1;
              alert('Ya existe en la lista!');
          }
      });
  
      if(validexist==1){
          return false;
      }
      $('#btnseleccionar'+idcompradetalle)
        .css('background-color','rgb(11, 115, 11)')
        .css('border-color','rgb(11, 115, 11)')
        .html('<i class="fas fa-plus"></i> Seleccionado</a>');
  
      var num = $("#tabla-compradevolucion tbody").attr('num');
  
      var nuevaFila='<tr id="'+num+'" idcompradetalle="'+idcompradetalle+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+descripcion+'</td>';
          nuevaFila+='<td>'+unidad+'</td>';
          nuevaFila+='<td>'+cantidad+'</td>';
          nuevaFila+='<td>'+preciocompra+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+preciocompra+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0" disabled></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" step="0.01" min="0" disabled></td>';  
          nuevaFila+='<td class="with-btn" width="10px"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
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
  
        // saldo
        var totalcompra = parseFloat($("#totalcompra").val());
        var saldorestante = parseFloat($("#totalapagar").val())-totalcompra;
        var totalpagado = parseFloat($("#totalpagado").val());
        $('#cont-opcionsaldo').css('display','none');
        $('#totaladevolver').val('0.00');
        if(totalpagado>=0 && totalpagado>saldorestante){
            $('#totaladevolver').val((totalpagado-saldorestante).toFixed(2));
            $('#cont-opcionsaldo').css('display','block');
        }
}
function selectproductos(){
    var data = '';
    $("#tabla-compradevolucion tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idcompradetalle = '/-/'+$(this).attr('idcompradetalle');
        var productCant = 'productCant'+num+'/-/'+$("#productCant"+num).val();
        var productUnidad = 'productUnidad'+num+'/-/'+$("#productUnidad"+num).val();
        var productTotal = 'productTotal'+num+'/-/'+$("#productTotal"+num).val();
        data = data+'/&/'+idcompradetalle+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productTotal;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-compradevolucion tbody tr#"+num).remove();
    calcularmonto();
}
</script>