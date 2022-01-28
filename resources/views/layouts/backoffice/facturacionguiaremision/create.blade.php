<div class="modal-content">
  <div id="carga-facturacionguiaremision">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Guia de Remisión</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
         <div class="container">
          <div class="form-row">
            <div class="col-sm-6 mx-select2">
              <select class="form-control" id="idestadodocumento">
                <option value="venta">Venta</option>
                <option value="compra">Compra</option>
                <option value="boletafactura">Boleta / Factura</option>
<!--                 <option value="transferencia">Transferencia</option> -->
              </select>
            </div>
            <div class="col-sm-6" id="div-buscador-venta">
              <input class="form-control" type="text" value="{{isset($_GET['codigoventa'])?$_GET['codigoventa']:''}}" id="codigoventa" placeholder="Código de Venta" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
            <div class="col-sm-6 d-none" id="div-buscador-compra">
              <input class="form-control" type="text" value="{{isset($_GET['codigoventa'])?$_GET['codigoventa']:''}}" id="codigocompra" placeholder="Código de Compra" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
            <div class="col-sm-3 div-buscador-facturaboleta d-none">
              <input class="form-control" type="text" value="{{isset($_GET['codigoventa'])?$_GET['codigoventa']:''}}" id="serie" placeholder="Serie" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
            <div class="col-sm-3 div-buscador-facturaboleta d-none">
              <input class="form-control" type="text" value="{{isset($_GET['codigoventa'])?$_GET['codigoventa']:''}}" id="correlativo" placeholder="Correlativo" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
<!--             <div class="col-sm-6  d-none" id="div-buscador-transferencia">
              <input class="form-control" type="text" value="{{isset($_GET['codigotransferencia'])?$_GET['codigotransferencia']:''}}" id="codigotransferencia" placeholder="Código de Transferencia" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div> -->
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
        <div id="cont-guiaremision-carga"></div>
        <div id="cont-guiaremision" class="d-none">
          <form action="javascript:;" id="formfacturacionguiaremision" onsubmit="callback({
                                                                                      route: 'backoffice/facturacionguiaremision',
                                                                                      method: 'POST',
                                                                                      carga: '#carga-facturacionguiaremision',
                                                                                      idform: 'formfacturacionguiaremision',
                                                                                      data:{
                                                                                          view: 'registrar',
                                                                                          productos: selectproductos(),
                                                                                      }
                                                                                  },
                                                                                  function(resultado){
                                                                                      location.href = '{{ url('backoffice/facturacionguiaremision') }}';                                                  
                                                                                  },this)"> 
            <input type="hidden" id="idcompra">
            <input type="hidden" id="idventa">
            <input type="hidden" id="idfacturacion">
            <div class="row">
                <div class="col-md-7">
                    <h4>General</h4>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Remitente *</label>
                            <select class="form-control" id="emisor" disabled>
                              <option></option>
                              @foreach ($agencias as $value) 
                                <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                              @endforeach
                            </select>
                        </div>
                       <div class="col-sm-6">
                            <label>Destinatario *</label>
                            <select class="form-control" id="destinatario">
                              <option></option>
                            </select>
                       </div>
                    </div>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Punto de Partida *</label>
                            <select class="form-control" id="partidaubigeo">
                              <option value="{{usersmaster()->idubigeo}}">{{usersmaster()->ubigeonombre}}</option>
                            </select>
                        </div>
                       <div class="col-sm-6">
                            <label>Punto de Llegada *</label>
                            <select class="form-control" id="llegadaubigeo">
                              <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                       <div class="col-sm-6">
                            <label>Dirección de Partida *</label>
                            <input type="text" value="{{usersmaster()->tiendadireccion}}" id="direccionpartida" class="form-control">
                        </div>
                       <div class="col-sm-6">
                            <label>Dirección de Llegada *</label>
                            <input type="text" id="direccionllegada" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-5"> 
                    <h4>Detalle de Traslado</h4>
                    <div class="form-group row">
                       <div class="col-sm-4">
                            <label>Motivo *</label>
                            <select class="form-control" id="motivo">
                              <option></option>
                              @foreach ($motivos as $value) 
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                              @endforeach
                            </select>
                        </div>
                       <div class="col-sm-4">
                            <label>Fecha de Emisión *</label>
                            <input class="form-control" type="date" id="fechaemision" value="{{ date('Y-m-d') }}" disabled>
                        </div>
                       <div class="col-sm-4">
                            <label>Fecha de Traslado *</label>
                            <input class="form-control" type="date" id="fechatraslado">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label>Nombre del Transportista *</label>
                            <select class="form-control" id="transportista">
                              <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label>Observación *</label>
                            <input type="text" id="observacion" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionguiaremision" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>U. Medida</th>
                        <th width="10px">Stock</th>
                        <th width="80px">Cantidad</th>
                        <th width="110px">P. Unitario</th>
                        <th width="110px">P. Total</th>
                        </th>
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
          </form>
        </div>
    </div>
    <div id="cont-guiaremision-btn" style="display:none;">
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionguiaremision').submit();">Enviar a Sunat</a>
      </div> 
    </div>
  </div>
</div>
<script>
$('#motivo').select2({
  placeholder: 'Seleccionar'
}).val(1).trigger('change');  
  
$('#idestadodocumento').select2({
}).on('change', function (e) {
  let valueSelect = e.currentTarget.value;
  if (valueSelect == 'venta') {
    $('#div-buscador-compra').addClass('d-none').removeClass('d-block');
    $('#div-buscador-venta').removeClass('d-block').removeClass('d-none');
    $('#div-buscador-transferencia').addClass('d-none').removeClass('d-block');
    $('.div-buscador-facturaboleta').addClass('d-none').removeClass('d-block');
  }else if (valueSelect == 'boletafactura') {
    $('#div-buscador-compra').addClass('d-none').removeClass('d-block');
    $('.div-buscador-facturaboleta').addClass('d-block').removeClass('d-none');
    $('#div-buscador-venta').addClass('d-none').removeClass('d-block');
    $('#div-buscador-transferencia').addClass('d-none').removeClass('d-block');
  }else if (valueSelect == 'transferencia') {
    $('#div-buscador-compra').addClass('d-none').removeClass('d-block');
    $('#div-buscador-transferencia').addClass('d-block').removeClass('d-none');
    $('#div-buscador-venta').addClass('d-none').removeClass('d-block');
    $('.div-buscador-facturaboleta').addClass('d-none').removeClass('d-block');
  }else if (valueSelect == 'compra') {
    $('#div-buscador-compra').addClass('d-block').removeClass('d-none');
    $('#div-buscador-transferencia').addClass('d-none').removeClass('d-block');
    $('#div-buscador-venta').addClass('d-none').removeClass('d-block');
    $('.div-buscador-facturaboleta').addClass('d-none').removeClass('d-block');
  }
});
  
@if(isset($_GET['codigoventa']))
  cargarventa({
               codigoventa: "{{ $_GET['codigoventa'] }}", 
               idestadodocumento: $("#idestadodocumento option:selected").val() 
              });
@endif
  
$('#codigocompra,#codigoventa, #serie, #correlativo, #codigotransferencia').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code==13){
        cargarventa({
                    codigocompra: $('#codigocompra').val(),
                    codigoventa: $('#codigoventa').val(),
                    serie: $('#serie').val(),
                    correlativo: $('#correlativo').val(),
                    codigotransferencia: $('#codigotransferencia').val(),
                    idestadodocumento: $("#idestadodocumento option:selected").val()
                   })
    }     
}); 
  
function cargarventa(inputObject) {
  load('#cont-guiaremision-carga');
  $('#cont-guiaremision').addClass('d-none').removeClass('d-block');
  $('#cont-guiaremision-btn').addClass('d-none').removeClass('d-block');
  $('#tabla-facturacionguiaremision > tbody').html('');
  $('#idcompra,#idventa, #idfacturacion, #totalventa').val('');
  $.ajax({
     url: "{{ url('backoffice/facturacionguiaremision/show-seleccionarventa') }}",
     type: 'GET',
     data: {
        codigocompra: inputObject.codigocompra,
        codigoventa: inputObject.codigoventa,
        serie: inputObject.serie,
        correlativo: inputObject.correlativo,
        codigotransferencia: inputObject.codigotransferencia,
        idestadodocumento : inputObject.idestadodocumento
     },
     success: function (respuesta){
        if(respuesta["venta"] != null) {
          //respuesta['compra'].tipo == 'compra' ? $('#idcompra').val(respuesta['compra'].id) : $('#idfacturacion').val(respuesta['compra'].id);
          respuesta['venta'].tipo == 'venta' ? $('#idventa').val(respuesta['venta'].id) : $('#idfacturacion').val(respuesta['venta'].id);
          $('#cont-guiaremision').addClass('d-block').removeClass('d-none');
          $('#cont-guiaremision-btn').addClass('d-block').removeClass('d-none');
          $('#cont-guiaremision-carga').html('');
          let text = `${respuesta.venta['cliente_identificacion']} - ${respuesta.venta['cliente_nombre']} ${respuesta.venta['cliente_apellidos']}`;
          let nuevaOpcion = new Option(text, respuesta.venta['cliente_id'], true, true);
          $('#destinatario').append(nuevaOpcion).trigger('change');
          let trDetalle = '';
          let num = 0;
          var totalVenta = 0;
          respuesta['ventadetalle'].forEach((value) => {
              trDetalle += `<tr id="${num}" num="${num}" idproducto="${value.idproducto}">
                              <td id="productCodigo${num}">${value.codigo}</td>
                              <td id="productNombre${num}">${value.descripcion}</td>
                              <td id="productUnidad${num}">${value.unidadmedida}</td>
                              <td>${value.stock}</td>
                              <td id="productCant${num}">${value.cantidad}</td>
                              <td>${value.preciounitario}</td>
                              <td>${value.preciototal}</td>
                            </tr>`;
               totalVenta += parseFloat(value.preciototal);
               num++;
          });
          $('#tabla-facturacionguiaremision > tbody').append(trDetalle);
          $('#totalventa').val(totalVenta);
        }else {
           $('#cont-guiaremision-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">El Código no es valido!</div>');
        }
     }
  });
}
  
$('#emisor').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$agencia->id}}).trigger('change');
  
$('#destinatario').select2({
  ajax: {
        url:"{{url('backoffice/facturacionguiaremision/show-listarcliente')}}",
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
        url:"{{url('backoffice/facturacionguiaremision/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       },
       success: function (respuesta){
          $("#direccionllegada").val(respuesta['cliente'].direccion);
          $('#llegadaubigeo').html('<option value="'+respuesta["cliente"].idubigeo+'">'+respuesta["cliente"].ubigeonombre+'</option>');
       }
     })
});
  
$('#transportista').select2({
  ajax: {
        url:"{{url('backoffice/facturacionguiaremision/show-listartransportista')}}",
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
  
$('#partidaubigeo').select2({
  ajax: {
        url:"{{url('backoffice/facturacionguiaremision/show-ubigeo')}}",
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

$('#llegadaubigeo').select2({
  ajax: {
        url:"{{url('backoffice/facturacionguiaremision/show-ubigeo')}}",
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

function selectproductos(){
    let data = [];
    $("#tabla-facturacionguiaremision tbody tr").each(function() {
        let num = $(this).attr('id');
        data.push({
          idproducto: $(this).attr('idproducto'),
          codigo: $("#productCodigo"+num).text(),
          descripcion: $("#productNombre"+num).text(),
          cantidad: $("#productCant"+num).text(),
          unidadmedida: $("#productUnidad"+num).text(),
          productototal: $("#productTotal"+num).val(),
        });
    });
    return JSON.stringify(data);
}
  
function eliminarproducto(num){
    $("#tabla-facturacionguiaremision tbody tr#"+num).remove();
    calcularmonto();
}
</script>