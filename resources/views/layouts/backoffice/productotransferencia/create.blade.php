<div class="modal-content">
  <div id="carga-formproductotransferencia">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Transferencia de Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" 
              id="formproductotransferencia"
              onsubmit="callback({
                    route: 'backoffice/productotransferencia',
                    method: 'POST',
                    carga: '#carga-formproductotransferencia',
                    idform: 'formproductotransferencia',
                    data: {
                       view: 'registrar',
                       productos: selectproductos()
                    }
                },
                function(resultado){
                    location.href = '{{ url('backoffice/productotransferencia') }}';                                                                            
                },this)"> 
      <div class="row justify-content-md-center">
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Estado *</label>
              <div class="col-sm-9">
                  <select class="form-control" id="idestadotransferencia">
                      <option></option>
                      <option value="1">Solicitar Productos</option>
                      <option value="2">Enviar Productos</option>
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Motivo (Opcional)</label>
              <div class="col-sm-9">
                  <input type="text" class="form-control" id="motivo">
              </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-2 col-form-label">De *</label>
              <div class="col-sm-10">
                  <select class="form-control" id="idtiendaorigen">
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-sm-2 col-form-label">Para *</label>
              <div class="col-sm-10">
                  <select class="form-control" id="idtiendadestino">
                      <option></option>
                    @foreach($tiendas as $value)
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
                <!--input class="form-control" type="text" id="codigoproducto" placeholder="Código de Barras" style="height: 40px;font-size: 16px;text-align: center;"/-->
                <textarea class="form-control" id="codigoproducto" placeholder="Código de Barras" style="height: 40px;font-size: 16px;text-align: center;line-height: 1.6;"></textarea>
            </div>
          </div>
        <div class="table-responsive">
            <table class="table table-striped" id="tabla-productotransferencia" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>U. Medida</th>
                    <th width="10px">Stock</th>
                    <th width="80px">Cantidad</th>
                    <th width="200px">Motivo (Opcional)</th>
                    <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'productotransferencia/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th>
                  </tr>
                </thead>
                <tbody num="0">
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formproductotransferencia').submit();">Guardar Cambios</a>
    </div>
  
</div>
<script>
$('#idestadotransferencia').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    if(e.currentTarget.value==1){
        $('#idtiendaorigen').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val(null).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{usersmaster()->idtienda}}).trigger('change'); 
      
        $('#idtiendaorigen').removeAttr('disabled');
        $('#idtiendadestino').attr('disabled','true');
    }else if(e.currentTarget.value==2){
        $('#idtiendaorigen').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val({{usersmaster()->idtienda}}).trigger('change'); 
        $('#idtiendadestino').select2({
            placeholder: '--Seleccionar--',
            minimumResultsForSearch: -1
        }).val(null).trigger('change'); 
      
        $('#idtiendaorigen').attr('disabled','true');
        $('#idtiendadestino').removeAttr('disabled');
    }
}).val(2).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}); 
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
});
  
$('#codigoproducto').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        var codigoproducto = $('#codigoproducto').val();
        var pastedText = codigoproducto.split(/\r?\n/g);
        if(pastedText.length>2){
            $.each(pastedText, function( key, value ) {
                if(value!=''){
                    var dataText = value.split('	');
                    var codigoproducto = dataText[0];
                    var cantidadproducto = dataText[1];
                    $.ajax({
                        url:"{{url('backoffice/productotransferencia/show-agregarproductocodigo')}}",
                        type:'GET',
                        data: {
                            codigoimpresion : codigoproducto
                        },
                        success: function (respuesta){
                          if(respuesta["datosProducto"]!=null){
                            var validexist = 0;
                            $("#tabla-productotransferencia tbody tr").each(function() {
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
                                  (respuesta["datosProducto"].codigoimpresion).toString().padStart(6,"0"),
                                  respuesta["datosProducto"].nombreproducto,
                                  respuesta["datosProducto"].idunidadmedida,
                                  respuesta["datosProducto"].unidadmedidanombre,
                                  respuesta["stock"],
                                  cantidadproducto
                                );
                            }
                                
                          }
                        }
                    }) 
                }
            });
        }else{
            $.ajax({
                url:"{{url('backoffice/productotransferencia/show-agregarproductocodigo')}}",
                type:'GET',
                data: {
                    codigoimpresion : $('#codigoproducto').val()
                },
                success: function (respuesta){
                  if(respuesta["datosProducto"]!=null){
                    var validexist = 0;
                    $("#tabla-productotransferencia tbody tr").each(function() {
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
                          (respuesta["datosProducto"].codigoimpresion).toString().padStart(6,"0"),
                          respuesta["datosProducto"].nombreproducto,
                          respuesta["datosProducto"].idunidadmedida,
                          respuesta["datosProducto"].unidadmedidanombre,
                          respuesta["stock"],
                          '0'
                        );
                    }
                        
                  }
                }
            })  
        } 
    }     
});
function agregarproducto(idproducto,codigo,nombre,idunidadmedida,unidadmedidanombre,stock,cantidad){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style="background-color:#abfbab !important;color: #000;";
      if(stock<=0){
          var style="background-color:#ffafaf !important;color: #000;";
      }
      var num = $("#tabla-productotransferencia tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="with-form-control"><select id="idunidadmedida'+num+'" disabled><option value="'+idunidadmedida+'">'+unidadmedidanombre+'</option></select></td>';
          nuevaFila+='<td>'+stock+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productMotivo'+num+'" type="text"></td>'; 
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-productotransferencia tbody").append(nuevaFila);
      $("#tabla-productotransferencia tbody").attr('num',parseInt(num)+1);
  
      $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
}
function selectproductos(){
    var data = '';
    $("#tabla-productotransferencia tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var idunidadmedida = $("#idunidadmedida"+num).val();
        var productMotivo = $("#productMotivo"+num).val();
        data = data+'&'+idproducto+','+productCant+','+idunidadmedida+','+productMotivo;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-productotransferencia tbody tr#"+num).remove();
}
</script>