<div class="modal-content">
   <form action="javascript:;"
         onsubmit="callback({
             route: 'backoffice/notadevolucion/{{$notadevolucion->id}}',
             method: 'PUT',
             data:{
                 view: 'editar',
                       productos: selectproductos(),
                       seleccionartipopago: seleccionartipopago()
             }
         },
         function(resultado){
              location.href = '{{ url('backoffice/notadevolucion') }}';                                                  
         },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Editar Nota de Devolución</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="idventa" value="{{$notadevolucion->idventa}}" class="form-control">
        <div class="row">
            <div class="col-md-3"> 
            </div>
            <div class="col-md-6"> 
                <input class="form-control" type="text" value="{{ str_pad($notadevolucion->ventacodigo, 8, "0", STR_PAD_LEFT) }}" style="height: 40px;font-size: 16px;text-align: center;" disabled/>
            </div>
        </div>
            <div class="row">
                <div class="col-md-7"> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Agencia</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{ $notadevolucion->agencianombre }}" id="agencia" class="form-control" disabled>
                        </div>
                    </div>   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Cliente</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{ $notadevolucion->cliente }}" id="cliente" class="form-control" disabled>
                        </div>
                    </div>    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Motivo</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{ $notadevolucion->motivo }}" id="motivodevolucion" class="form-control">
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
                        <label class="col-sm-3 col-form-label">Fecha Registro</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{ $notadevolucion->fecharegistro }}" id="fechaemision" class="form-control" disabled>
                        </div>
                    </div>    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Forma de Pago</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{ $notadevolucion->formapagonombre }}" id="formapago" class="form-control" disabled>
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
                          <th rowspan="2" style="vertical-align: middle;">Motor</th>
                          <th rowspan="2" style="vertical-align: middle;">Marca</th>
                          <th rowspan="2" style="vertical-align: middle;">Modelo</th>
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
                    <tbody>
                    </tbody>
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
                            'formapago' => 'false',
                            'modulo' => 'notadevolucion',
                            'idmodulo' => $notadevolucion->id
                        ])
                    </div>
                 </div>
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
  </form>
</div>
<script>
@foreach($notadevoluciondetalles as $value)
    agregarproducto(
         '{{$value->idventadetalle}}',
         '{{str_pad($value->producodigoimpresion, 8, "0", STR_PAD_LEFT)}}',
         '{{$value->productonombre}}',
         '{{$value->productomotor}}',
         '{{$value->productomarca}}',
         '{{$value->productomodelo}}',
         '{{$value->preciounitario}}',
         '{{$value->unidadmedidanombre}}',
         '{{$value->cantidad}}'
    );
@endforeach
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$notadevolucion->idmoneda}}).trigger("change"); 
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
function agregarproducto(idventadetalle,codigo,nombre,motor,marca,modelo,preciounitario,unidad,cantidad){
      
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
          nuevaFila+='<td>'+motor+'</td>';
          nuevaFila+='<td>'+marca+'</td>';
          nuevaFila+='<td>'+modelo+'</td>';
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
        var saldorestante = parseFloat($("#totalapagar").val())-total;
        var totalpagado = parseFloat($("#totalpagado").val());
        $('#cont-opcionsaldo').css('display','none');
        if(totalpagado>=0 && totalpagado>saldorestante/* && totalpagado>totalventa*/){
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