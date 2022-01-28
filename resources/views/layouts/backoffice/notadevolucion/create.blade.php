<div class="modal-content">
  <div id="carga-notadevolucion">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Nota de Devolución</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-3"> 
            </div>
            <div class="col-md-6"> 
                <input class="form-control" type="text" id="venta_codigo" placeholder="Codigo de Venta" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
        </div>
        <form action="javascript:;" 
              id="formnotadevolucion" 
              onsubmit="callback({
                   route: 'backoffice/notadevolucion',
                   method: 'POST',
                   carga: '#carga-notadevolucion',
                   idform: 'formnotadevolucion',
                   data:{
                       view: 'registrar',
                       productos: selectproductos(),
                       seleccionartipopago: seleccionartipopago()
                   }
               },
               function(resultado){
                   location.href = '{{ url('backoffice/notadevolucion') }}';                                                  
               },this)"> 
            <div id="cont-notadevolucion" style="display:none;">
            <input type="hidden" id="idventa" class="form-control">
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
                        <label class="col-sm-2 col-form-label">Motivo *</label>
                        <div class="col-sm-10">
                            <input type="text" id="motivodevolucion" class="form-control">
                        </div>
                    </div>  
                </div>
                <div class="col-md-5"> 
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
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Forma de Pago</label>
                        <div class="col-sm-9">
                            <input type="text" id="formapago" class="form-control" disabled>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tabla-notadevolucion" style="margin-bottom: 5px;">
                     <thead class="thead-dark">
                        <tr>
                          <th rowspan="2" style="vertical-align: middle;">Código</th>
                          <th rowspan="2" style="vertical-align: middle;">Nombre</th>
                          <th rowspan="2" style="vertical-align: middle;">U. Medida</th>
                          <th colspan="2">VENTA</th>
                          <th colspan="3">DEVOLUCIÓN</th>
                          <th width="10px" class="with-btn" rowspan="2">
                            <a href="javascript:;" class="btn btn-warning" 
                               onclick="modal({route:'notadevolucion/create?view=notadevoluciondetalle&idventa='+$('#idventa').val(),size:'modal-fullscreen',carga:'#carga-notadevoluciondetalle'})">
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
                            <input class="form-control" type="text" id="totalventa" placeholder="0.00" value="0.00" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total Venta</label>
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
            </div>
        </form> 
        <div id="cont-notadevolucion-carga"></div>
    </div>
    <div id="cont-notadevolucion-btn" style="display:none;">
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formnotadevolucion').submit();">Guardar Cambios</a>
    </div> 
    </div> 
  </div>
</div>
<script>
$('#venta_codigo').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarventa_venta($('#venta_codigo').val())
    }     
}); 
 
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});
  
function cargarventa_venta(venta_codigo){
        load('#cont-notadevolucion-carga');
        $('#cont-notadevolucion').css('display','none');
        $('#cont-notadevolucion-btn').css('display','none');
        $('#tabla-notadevolucion > tbody').html('');
        calcularmonto();
        $.ajax({
            url:"{{url('backoffice/notadevolucion/show-seleccionarventa')}}",
            type:'GET',
            data: {
                venta_codigo : venta_codigo
            },
            success: function (respuesta){
                 if(respuesta["venta"] != undefined){
                  if(respuesta["valid_productos"]>0){
                    if(respuesta["venta"].idformapago==3){
                        if(respuesta["countcobranzaletra"] > 0){
                            $('#cont-notadevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡Esta Venta a Letra, tiene cobranzas realizadas!</div>');
                        }else{
                            $('#cont-notadevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡No estan permitidas emitir devolución de Venta a Letras por este medio!</div>');     
                        }
                    }else if(respuesta["venta"].idformapago==1 || respuesta["venta"].idformapago==2){
                        $('#idventa').val(respuesta["venta"].id);
                        $('#cont-notadevolucion-carga').html('');
                        $('#cont-notadevolucion').css('display','block');
                        $('#cont-notadevolucion-btn').css('display','block');
                  
                        $('#agencia').val(respuesta["venta"].agenciaruc+' - '+respuesta["venta"].agenciarazonsocial);
                        $('#cliente').val(respuesta["venta"].cliente);
                        $("#idmoneda").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["venta"].idmoneda).trigger("change");
                        $('#formapago').val(respuesta["venta"].nombreFormapago);
                        $('#totalpagado').val(respuesta["totalpagado"]);
                        $('#totalapagar').val(respuesta["totalapagar"]);
                    }
                  }else{
                      $('#cont-notadevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡La venta no tiene productos a devolver!</div>');
                  }
                }else{
                    $('#cont-notadevolucion-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">¡No existe la Venta!</div>');
                } 
         }
       })
}
function agregarproducto(idventadetalle,codigo,nombre,preciounitario,unidad,cantidad){
      
      var validexist = 0;
      $("#tabla-notadevolucion tbody tr").each(function() {      
          var idventadetalle_ant = $(this).attr('idventadetalle');
          if(idventadetalle_ant==idventadetalle){
              validexist = 1;
              alert('Ya existe en la lista!');
          }
      });
  
      if(validexist==1){
          return false;
      }
      $('#btnseleccionar'+idventadetalle)
        .css('background-color','rgb(11, 115, 11)')
        .css('border-color','rgb(11, 115, 11)')
        .html('<i class="fas fa-plus"></i> Seleccionado</a>');
  
      var num = $("#tabla-notadevolucion tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idventadetalle="'+idventadetalle+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
      
          nuevaFila+='<td>'+unidad+'</td>';
          nuevaFila+='<td>'+cantidad+'</td>';
          nuevaFila+='<td>'+preciounitario+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+preciounitario+'" step="0.01" min="0" disabled></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="0.00" step="0.01" min="0" disabled></td>';  
          nuevaFila+='<td class="with-btn" width="10px"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-notadevolucion tbody").append(nuevaFila);
      $("#tabla-notadevolucion tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}
function calcularmonto(){
        var total = 0;
        $("#tabla-notadevolucion tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productUnidad = parseFloat($("#productUnidad"+num).val());
            var subtotal = (productCant*productUnidad).toFixed(2);
            $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(subtotal);
        });
          
        var total = parseFloat(total).toFixed(2);

        $("#totalventa").val(total); 
  
        // saldo
        var totalventa = parseFloat($("#totalventa").val());
        var saldorestante = parseFloat($("#totalapagar").val())-totalventa;
        var totalpagado = parseFloat($("#totalpagado").val());
        /*console.log(parseFloat($("#totalapagar").val()))
        console.log(totalventa)
        console.log(parseFloat($("#totalpagado").val()))*/
        $('#cont-opcionsaldo').css('display','none');
        $('#totaladevolver').val('0.00');
        if(totalpagado>=0 && totalpagado>saldorestante && totalpagado>totalventa){
            $('#totaladevolver').val((totalpagado-saldorestante).toFixed(2));
            $('#cont-opcionsaldo').css('display','block');
        }
}
function selectproductos(){
    var data = '';
    $("#tabla-notadevolucion tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idventadetalle = '/-/'+$(this).attr('idventadetalle');
        var productCant = 'productCant'+num+'/-/'+$("#productCant"+num).val();
        var productUnidad = 'productUnidad'+num+'/-/'+$("#productUnidad"+num).val();
        var productTotal = 'productTotal'+num+'/-/'+$("#productTotal"+num).val();
        data = data+'/&/'+idventadetalle+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productTotal;
    });
    return data;
}
function eliminarproducto(num){
    $("#tabla-notadevolucion tbody tr#"+num).remove();
    calcularmonto();
}
</script>