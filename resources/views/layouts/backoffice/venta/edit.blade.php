<div class="modal-content">
  <div id="carga-cotizacionventa">
    <div class="modal-header">
        <h4 class="modal-title">Facturar Venta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true" style="font-weight: bold;">Comprobantes Emitidos</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false" style="font-weight: bold;">Emitir Comprobante</a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            @if(count($facturacionboletafacturas)>0)
            <table class="table table-bordered table-hover table-striped" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="100px">Fecha de Emisión</th>
                    <th width="80px">Responsable</th>
                    <th width="110px">Comprobante</th>
                    <th width="85px">Serie</th>
                    <th width="85px">Correlativo</th>
                    <th width="85px">DNI/RUC</th>
                    <th>Cliente</th>
                    <th width="85px">Moneda</th>
                    <th width="80px">Base Imp.</th>
                    <th width="80px">IGV</th>
                    <th width="80px">Total</th>
                    <th width="10px">Estado</th>
                    <th width="10px"></th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($facturacionboletafacturas as $value)
                    <?php 
                      $montototal = DB::table('ventadetalle')->where('idventa',$value->id)->sum(DB::raw('CONCAT(preciounitario*cantidad)'));
                    ?>
                    <tr>
                      <td>{{ $value->venta_fechaemision }}</td>
                      <td>{{ $value->responsablenombre }}</td>
                      <td>
                        @if($value->venta_tipodocumento=='03')
                            BOLETA
                        @elseif($value->venta_tipodocumento=='01')
                            FACTURA
                        @endif  
                      </td>
                      <td>{{ $value->venta_serie }}</td>
                      <td>{{ $value->venta_correlativo }}</td>
                      <td>{{ $value->cliente_numerodocumento }}</td>
                      <td>{{ $value->cliente_razonsocial }}</td>
                      <td>{{ $value->venta_tipomoneda }}</td>
                      <td>{{ $value->venta_valorventa }}</td>
                      <td>{{ $value->venta_totalimpuestos }}</td>
                      <td>{{ $value->venta_montoimpuestoventa }}</td>
                      <td>
                        @if($value->idestadosunat==1)
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No Enviado</span></div> 
                        @else
                            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Enviado</span></div>
                        @endif  
                      </td>
                      <td class="with-btn-group" nowrap>
                        <div class="btn-group">
                          <a href="#" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret" data-toggle="dropdown">
                            Opción <span class="caret"></span>
                          </a>
                          <ul class="dropdown-menu pull-right">
                             <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=detalle',size:'modal-fullscreen'})">
                               <i class="fa fa-list-alt"></i> Detalle</a></li>
                             <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=comprobante',size:'modal-fullscreen'})">
                                <i class="fa fa-file-alt"></i> PDF Comprobante
                             </a></li>
                             <?php $archivo = $value->emisor_ruc.'-'.$value->venta_tipodocumento.'-'.$value->venta_serie.'-'.$value->venta_correlativo; ?>
                             <li>
                                <a href="{{url('public/sunat/produccion/boletafactura/'.$archivo.'.xml')}}" target="_blank" download>
                                    <i class="fa fa-file-alt"></i> XML Comprobante
                                </a>
                             </li>
                             <li><a href="{{url('public/sunat/produccion/boletafactura/R-'.$archivo.'.zip')}}" target="_blank" download>
                                <i class="fa fa-file-alt"></i> CDR Comprobante
                             </a></li>
                             <li><a href="javascript:;" onclick="modal({route:'facturacionboletafactura/{{ $value->id }}/edit?view=correo'})">
                                <i class="fa fa-share-square"></i> Enviar Correo (XML y CDR)
                             </a></li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-warning">Esta venta no tiene ningún comprobante.</div>
            @endif
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div id="cont-emitircomprobante">
            <div class="row">
                <div class="col-md-4"> 
                </div>
                <div class="col-md-4"> 
                    <input class="form-control" type="text" id="codigoventa" value="{{str_pad($venta->codigo, 8, "0", STR_PAD_LEFT)}}" placeholder="Código de Cotización" style="height: 40px;font-size: 16px;text-align: center;" disabled/>
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
                                  view: 'facturarventa',
                                  productos: selectproductos(),
                                  idventa: '{{$venta->id}}'
                              }
                          },
                          function(resultado){
                               location.href = '{{ url('backoffice/venta') }}';                                                  
                          },this)"> 
                <div id="cont-cotizacionventa" style="display:none;">
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
                            <th>Nombre</th>
                            <th>Motor</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>U. Medida</th>
                            <th width="80px">Cantidad</th>
                            <th width="110px">P. Unitario</th>
                            <th width="110px">P. Total</th>
                            <th width="10px"></th>
                          </tr>
                        </thead>
                        <tbody num="0"></tbody>
                    </table>
                </div>
                <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Sub Total</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="subtotalventa" placeholder="0.00" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">IGV (18%)</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="igvventa" placeholder="0.00" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Total</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
                        </div>
                    </div>
                </div>
                </div> 
                <div id="cont-cotizacionventa-btn" style="display:none;text-align: right;">
                <a href="javascript:;" class="btn btn-success" onclick="enviarsunat()">Enviar a SUNAT</a>
                </div>
                </div> 
            </form> 
            </div>
        </div>
      </div>
            
    </div>
  </div>
</div>
<script>
  
function enviarsunat(){
    $('#cont-cotizacionventa-btn').css('display','none');
    $('#formcotizacionventa').submit();
}
        load('#cont-cotizacionventa-carga');
        $('#cont-cotizacionventa').css('display','none');
        $('#cont-cotizacionventa-btn').css('display','none');
        $("#idtipocomprobante").select2({
            placeholder: '-- Seleccionar --',
            minimumResultsForSearch: -1
        }).val(null).trigger("change");
        $.ajax({
            url:"{{url('backoffice/venta/show-seleccionarventa-facturar')}}",
            type:'GET',
            data: {
                idventa : '{{$venta->id}}'
            },
            success: function (respuesta){
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
                        $("#idformapago").select2({
                            placeholder: '-- Seleccionar --',
                            minimumResultsForSearch: -1
                        }).val(respuesta["venta"].idformapago).trigger("change");
                        $('#tabla-ventacotizacion tbody').html('');
                        var valid=0;
                        $.each( respuesta["ventadetalles"], function( key, value ) {
                            agregarproducto(
                                value.id,
                                (value.producodigoimpresion).toString().padStart(6,"0"),
                                value.productonombre,
                                value.productomotor,
                                value.productomarca,
                                value.productomodelo,
                                value.preciounitario,
                                value.idunidadmedida,
                                value.unidadmedidanombre,
                                value.cantidad,
                                value.preciototal
                            );
                            valid=1;
                        });
                        if(valid==0){
                            $('#cont-emitircomprobante').html('<div class="alert alert-warning">Esta venta ya no tiene ningún producto pendiente, para poder emitir un comprobante.</div>');      
                        }
            }
        })

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
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});

$('#idmoneda').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->idmoneda}}).trigger('change');
  
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
  
$('#idtipocomprobante').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});

function agregarproducto(idproducto,codigo,nombre,motor,marca,modelo,precioventa,idunidadmedida,unidadmedidanombre,cantidad,preciototal){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-ventacotizacion tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td>'+motor+'</td>';
          nuevaFila+='<td>'+marca+'</td>';
          nuevaFila+='<td>'+modelo+'</td>';
          nuevaFila+='<td class="with-form-control"><select id="idunidadmedida'+num+'" disabled><option value="'+idunidadmedida+'">'+unidadmedidanombre+'</option></select></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto()" onclick="calcularmonto()"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productUnidad'+num+'" type="number" value="'+precioventa+'" onkeyup="calcularmonto()" onclick="calcularmonto()" step="0.01" min="0"></td>';
          nuevaFila+='<td class="with-form-control"><input class="form-control" id="productTotal'+num+'" type="text" value="'+preciototal+'" step="0.01" min="0" disabled></td>';  
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-ventacotizacion tbody").append(nuevaFila);
      $("#tabla-ventacotizacion tbody").attr('num',parseInt(num)+1);
      calcularmonto();
  
      $("select#idunidadmedida"+num).select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
}
function calcularmonto(){
        var total = 0;
        $("#tabla-ventacotizacion tbody tr").each(function() {
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
    $("#tabla-ventacotizacion tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var idunidadmedida = $("#idunidadmedida"+num).val();
        var productTotal = $("#productTotal"+num).val();
        data = data+'&'+idproducto+','+productCant+','+productUnidad+','+idunidadmedida+','+productTotal;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-ventacotizacion tbody tr#"+num).remove();
    calcularmonto();
}
</script>