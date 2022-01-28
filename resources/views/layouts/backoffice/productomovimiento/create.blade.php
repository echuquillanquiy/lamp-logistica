<div class="modal-content">
  <div id="carga-formproductomovimiento">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Movimiento de Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" 
              id="formproductomovimiento"
              onsubmit="callback({
                    route: 'backoffice/productomovimiento',
                    method: 'POST',
                    carga: '#carga-formproductomovimiento',
                    idform: 'formproductomovimiento',
                    data: {
                       view: 'registrar',
                       productos: selectproductos()
                    }
                },
                function(resultado){
                    location.href = '{{ url('backoffice/productomovimiento') }}';                                                                            
                },this)"> 
      <div class="row justify-content-md-center">
        <div class="col-md-6">
          <div class="form-group row">
              <label class="col-sm-3 col-form-label">Tipo de Movimiento *</label>
              <div class="col-sm-9">
                  <select class="form-control" id="idestadomovimiento">
                      <option></option>
                      <option value="1">Ingreso</option>
                      <option value="2">Salida</option>
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
              <label class="col-sm-2 col-form-label">Tienda *</label>
              <div class="col-sm-10">
                  <select class="form-control" id="idtienda" disabled>
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
            <table class="table table-hover" id="tabla-productomovimiento" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Porducto</th>
                    <th width="10px">Stock</th>
                    <th width="80px">Cantidad</th>
                    <th width="200px">Motivo (Opcional)</th>
                    <th width="10px" class="with-btn"><a href="javascript:;" class="btn btn-warning" onclick="modal({route:'productomovimiento/create?view=productos',size:'modal-fullscreen'})"><i class="fas fa-plus"></i> Agregar</a></th>
                  </tr>
                </thead>
                <tbody num="0">
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formproductomovimiento').submit();">Guardar Cambios</a>
    </div>
  
</div>
<script>
$('#idestadomovimiento').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}); 
  
$('#idtienda').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{usersmaster()->idtienda}}).trigger('change');  
  
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
                        url:"{{url('backoffice/productomovimiento/show-agregarproductocodigo')}}",
                        type:'GET',
                        data: {
                            codigoimpresion : codigoproducto
                        },
                        success: function (respuesta){
                          if(respuesta["datosProducto"]!=null){
                            var validexist = 0;
                            $("#tabla-productomovimiento tbody tr").each(function() {
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
                url:"{{url('backoffice/productomovimiento/show-agregarproductocodigo')}}",
                type:'GET',
                data: {
                    codigoimpresion : $('#codigoproducto').val()
                },
                success: function (respuesta){
                  if(respuesta["datosProducto"]!=null){
                    var validexist = 0;
                    $("#tabla-productomovimiento tbody tr").each(function() {
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
function agregarproducto(idproducto,codigo,nombre,stock,cantidad){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style="background-color:#abfbab ;color: #000;";
      if(stock<=0){
          var style="background-color:#ffafaf;color: #000;";
      }
      var num = $("#tabla-productomovimiento tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td>'+stock+'</td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productMotivo'+num+'" type="text"></td>'; 
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-productomovimiento tbody").append(nuevaFila);
      $("#tabla-productomovimiento tbody").attr('num',parseInt(num)+1);
  
      $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
}
function selectproductos(){
    var data = '';
    $("#tabla-productomovimiento tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var idunidadmedida = 1;
        var productMotivo = $("#productMotivo"+num).val();
        data = data+'&'+idproducto+','+productCant+','+idunidadmedida+','+productMotivo;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-productomovimiento tbody tr#"+num).remove();
}
</script>