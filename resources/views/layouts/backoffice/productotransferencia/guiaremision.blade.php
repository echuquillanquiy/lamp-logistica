<div class="modal-content">
  <div id="carga-formmodalguiaremision">
    <div class="modal-header">
        <h4 class="modal-title">Generar Guia de Remisión</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <form action="javascript:;" id="formfacturacionguiaremision" onsubmit="callback({
                                                                                      route: 'backoffice/productotransferencia/{{ $productotransferencia->id }}',
                                                                                      method: 'PUT',
                                                                                      // carga: '#carga-facturacionguiaremision',
                                                                                      idform: 'formfacturacionguiaremision',
                                                                                      data:{
                                                                                        view: 'guiaremision',
                                                                                        emisor: $('#emisor').attr('idagencia'),
                                                                                        destinatario: $('#destinatario').attr('idagencia'),
                                                                                        productos: selectproductos(),
                                                                                      }
                                                                                },
                                                                                function(resultado){
                                                                                     location.href = '{{ url('backoffice/productotransferencia') }}';                                                  
                                                                                },this)"> 
          <div class="row">
              <div class="col-md-7">
                  <h4>General</h4>
                  <div class="form-group row">
                     <div class="col-sm-6">
                          <label>Remitente (Tienda Origen)*</label>
                          <input id="emisor" type="text" class="form-control" idagencia="{{ $agencia->id }}" value="{{ $agencia->ruc }} - {{ $agencia->nombrecomercial }} ({{ $productotransferencia->tienda_origen_nombre }})" disabled>
                      </div>
                     <div class="col-sm-6">
                          <label>Destinatario (Tienda Destino)*</label>
                          <input id="destinatario" type="text" class="form-control" idagencia="{{ $agencia->id }}" value="{{ $agencia->ruc }} - {{ $agencia->nombrecomercial }} ({{ $productotransferencia->tienda_destino_nombre }})" disabled>
                     </div>
                  </div>
                  <div class="form-group row">
                     <div class="col-sm-6">
                          <label>Punto de Partida *</label>
                          <select class="form-control" id="partidaubigeo">
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
                          <input type="text" id="direccionpartida" class="form-control">
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
                          <select class="form-control" id="motivo" disabled>
                            <option></option>
                            @foreach ($motivoguiaremision as $value) 
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
                      <th width="10px" >Stock</th>
                      <th width="150px" >Cantidad Enviando</th>
                      </th>
                    </tr>
                  </thead>
                  <tbody num="0">
                     @php
                        $i = 0;
                     @endphp
                     @foreach($detalletransferencia as $value)
                      @php
                        $stock = stock_producto($productotransferencia->idtiendaorigen, $value->idproducto);
                      @endphp
                      <tr id="{{ $i }}" idproducto="{{ $value->idproducto }}" codigounidadmedida="{{ $value->unidadmedidacodigo }}">
                        <td id="productCodigo{{ $i }}">{{ $value->producodigoimpresion }}</td>
                        <td id="productNombre{{ $i }}">{{ $value->productonombre }}</td>
                        <td id="productUnidad{{ $i }}">{{ $value->unidadmedidanombre }}</td>
                        <td class="text-center">{{ $stock['total'] }}</td>
                        <td id="productCant{{ $i }}" class="text-center">{{ $value->cantidadenviado }}</td>                                                                
                      </tr>
                      @php $i++; @endphp
                     @endforeach
                  </tbody>
              </table>
          </div>
        </form>
    </div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionguiaremision').submit();">Enviar a Sunat</a>
    </div>
  </div>
</div>
<script>
var productoTransferencia = @json($productotransferencia);  

$('#partidaubigeo').append(new Option(productoTransferencia.ubigeo_origen_nombre, productoTransferencia.tienda_origen_idubigeo, true, true)).trigger('change');
$('#llegadaubigeo').append(new Option(productoTransferencia.ubigeo_destino_nombre, productoTransferencia.tienda_destino_idubigeo, true, true)).trigger('change');
  
$('#direccionpartida').val(productoTransferencia.tienda_origen_direccion);
$('#direccionllegada').val(productoTransferencia.tienda_destino_direccion);

$('#motivo').select2({
  placeholder: 'Seleccionar'
}).val(4).trigger('change');  
  
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
  
$('#transportista').select2({
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
});
  
function selectproductos(){
    let data = [];
    $("#tabla-facturacionguiaremision tbody tr").each(function() {
        let num = $(this).attr('id');
        data.push({
          idproducto:   $(this).attr('idproducto'),
          codigo:       $("#productCodigo"+num).text(),
          descripcion:  $("#productNombre"+num).text(),
          cantidad:     $("#productCant"+num).text(),
          unidadmedida: $(this).attr('codigounidadmedida'),
        });
    });
    return JSON.stringify(data);
}  
</script>