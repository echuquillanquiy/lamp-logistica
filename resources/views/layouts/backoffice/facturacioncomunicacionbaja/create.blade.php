<div class="modal-content">
  <div id="carga-facturacioncomunicacionbaja">
    <div class="modal-header">
      <h4 class="modal-title">Registrar Comunicación de Baja</h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
          <select id="serie" class="form-control"  style="height: 40px;font-size: 16px;text-align: center;" >
            <option value="" >Serie</option>
            <option tipo="boletafactura" value="F{{ str_pad($serie->facturador_serie, 3, "0", STR_PAD_LEFT) }}" >F{{ str_pad($serie->facturador_serie, 3, "0", STR_PAD_LEFT) }} (Factura)</option>
            <option tipo="notacredito" value="FF0{{ $serie->facturador_serie }}">FF0{{ $serie->facturador_serie }} (Nota de Credito - Factura)</option>
          </select>
        </div>
        <div class="col-md-3">
            <input class="form-control" type="text" id="correlativo" placeholder="Correlativo" style="height: 40px;font-size: 16px;text-align: center;"/>
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
        <div id="cont-comunicacionbaja-carga"></div>
        <div id="cont-comunicacionbaja" class="d-none">
          <form action="javascript:;" id="formfacturacioncomunicacionbaja" onsubmit="callback({
                                                                                      route: 'backoffice/facturacioncomunicacionbaja',
                                                                                      method: 'POST',
                                                                                      carga: '#carga-facturacioncomunicacionbaja',
                                                                                      idform: 'formfacturacioncomunicacionbaja',
                                                                                      data:{
                                                                                          view: 'registrar',
                                                                                          productos: selectproductos(),
                                                                                          tipo: $('#serie option:selected').attr('tipo'),
                                                                                      }
                                                                                  },
                                                                                  function(resultado){
                                                                                       location.href = '{{ url('backoffice/facturacioncomunicacionbaja') }}';                                                  
                                                                                  },this)"> 
            <input type="hidden" id="idfacturacion">
            <input type="hidden" id="factura_serie">
            <input type="hidden" id="factura_correlativo">
            <input type="hidden" id="idagencia">
            <input type="hidden" id="idventa">
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
                   <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Descripcion de Motivo</label>
                        <div class="col-sm-10">
                            <input type="text" id="motivo" class="form-control" >
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
                            <input type="text" id="tipodocumento" class="form-control" disabled>
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
    
            <div class="table-responsive">
                <table class="table" id="tabla-facturacioncomunicacionbaja" style="margin-bottom: 5px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>U. Medida</th>
                        <th width="80px">Cantidad</th>
                        <th width="110px">P. Unitario</th>
                        <th width="110px">P. Total</th>
                        <th></th>
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
    <div id="cont-comunicacionbaja-btn" style="display:none;">
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacioncomunicacionbaja').submit();">Registrar</a>
      </div> 
    </div>
  </div>
</div>
<script>
$( '#serie, #correlativo' ).keyup( function(e) {
  var code = ( e.keyCode ? e.keyCode : e.which );
  if( code == 13 ){
    cargarventa({
                serie: $('#serie').val(),
                correlativo: $('#correlativo').val()
               })
  }
});

$('#serie').select2({
   minimumResultsForSearch: -1
});  
  
function cargarventa(inputObject) {
  load('#cont-comunicacionbaja-carga');
  $('#cont-comunicacionbaja').addClass('d-none').removeClass('d-block');
  $('#cont-comunicacionbaja-btn').addClass('d-none').removeClass('d-block');
  $('#tabla-facturacioncomunicacionbaja > tbody').html('');
  $('#idfacturacion, #totalventa').val('');
  $.ajax({
     url: "{{ url('backoffice/facturacioncomunicacionbaja/show-seleccionarventa') }}",
     type: 'GET',
     data: {
        serie: inputObject.serie,
        correlativo: inputObject.correlativo,
        tipo: $('#serie option:selected').attr('tipo'),
     },
     success: function (respuesta){
        if(respuesta["venta"] != null) {
          $('#idfacturacion').val(respuesta['venta'].id);
          $('#cont-comunicacionbaja').addClass('d-block').removeClass('d-none');
          $('#cont-comunicacionbaja-btn').addClass('d-block').removeClass('d-none');
          $('#cont-comunicacionbaja-carga').html('');
          let agencia          = `${ respuesta.venta['agencia_ruc'] } - ${ respuesta.venta['agencia_nombrecomercial'] }`;
          let cliente          = `${ respuesta.venta['cliente'] }`;
          let moneda           = respuesta.venta['venta_tipomoneda'];
          let fechaemision     = respuesta.venta['venta_fechaemision'];
          let tipodocumento    = respuesta.venta['venta_tipodocumento'];
          let seriecorrelativo = `${ respuesta.venta['venta_serie'] } - ${ respuesta.venta['venta_correlativo'] }`;
          $('#agencia').val(agencia);
          $('#cliente').val(cliente);
          $('#moneda').val(moneda); 
          $('#fechaemision').val(fechaemision);
          $('#tipodocumento').val(tipodocumento);
          $('#seriecorrelativo').val(seriecorrelativo);
          $('#factura_serie').val(respuesta.venta['venta_serie']);
          $('#factura_correlativo').val(respuesta.venta['venta_correlativo']);
          $('#idagencia').val(respuesta.venta['agencia_id']);
          $('#idventa').val(respuesta.venta['idventa']);
          let trDetalle = '';
          let num = 0;
          var totalVenta = 0;
          respuesta['ventadetalle'].forEach((value) => {
              trDetalle += `<tr id="${num}" num="${num}" idproducto="${value.idproducto}">
                              <td id="productCodigo${num}">${value.codigo}</td>
                              <td id="productNombre${num}">${value.descripcion}</td>
                              <td id="productUnidad${num}">${value.unidadmedida}</td>
                              <td id="productCant${num}">${value.cantidad}</td>
                              <td>${value.preciounitario}</td>
                              <td>${value.preciototal}</td>
                            </tr>`;
               totalVenta += parseFloat(value.preciototal);
               num++;
          });
          $('#tabla-facturacioncomunicacionbaja > tbody').append(trDetalle);
          $('#totalventa').val(totalVenta);
        }else {
          $('#cont-comunicacionbaja-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">El Código no es valido!</div>');
        }
     }
  });
}

function selectproductos(){
    let data = [];
    $("#tabla-facturacioncomunicacionbaja tbody tr").each(function() {
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
    $("#tabla-facturacioncomunicacionbaja tbody tr#"+num).remove();
    calcularmonto();
}
</script>