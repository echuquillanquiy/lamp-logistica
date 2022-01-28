<div class="modal-content">
  <div id="carga-facturacionresumen">
    <div class="modal-header">
        <h4 class="modal-title">Registrar Resumen Diario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-3"> 
            </div>
            <div class="col-md-6 mx-select2"> 
                <select class="form-control" id="reenviaranular">
                  <option value="anular">Para Anular</option>
                  <option value="reenviar">Para Reenviar</option>
                </select>
            </div>
            <div class="col-md-3"> 
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"> 
            </div>
            <div class="col-md-3 mx-select2"> 
                <select class="form-control" id="facturador_serie">
                  <option></option>
                  <option tipo="boleta" value="B{{ str_pad($serie->facturador_serie, 3, "0", STR_PAD_LEFT) }}">B{{ str_pad($serie->facturador_serie, 3, "0", STR_PAD_LEFT) }} ( Boleta)</option>
                  <option tipo="notacredito_boletafactura" value="FF0{{ $serie->facturador_serie }}">FF0{{ $serie->facturador_serie }} (Nota de Credito - Factura)</option>
                  <option tipo="notacredito_boletafactura" value="BB0{{ $serie->facturador_serie }}">BB0{{ $serie->facturador_serie }} (Nota de Credito - Boleta)</option>
                </select>
            </div>
            <div class="col-md-3"> 
                <input class="form-control" type="text" id="facturador_correlativo" placeholder="Correlativo" style="height: 40px;font-size: 16px;text-align: center;"/>
            </div>
        </div>
        <div id="cont-tabla-facturacionresumen" style="display:none;">
        <form action="javascript:;"  id="formfacturacionresumen" onsubmit="callback({
                                                                                          route: 'backoffice/facturacionresumen',
                                                                                          method: 'POST',
                                                                                          carga: '#carga-facturacionresumen',
                                                                                          idform: 'formfacturacionresumen',
                                                                                          data:{
                                                                                              view: 'registrar',
                                                                                              boletas: selectproductos(),
                                                                                             
                                                                                          }
                                                                                      },
                                                                                      function(resultado){
                                                                                           location.href = '{{ url('backoffice/facturacionresumen') }}';                                                  
                                                                                      },this)"> 
                <div class="row">
                    <div class="col-md-3"> 
                    </div>
                    <div class="col-md-6"> 
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Agencia *</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="idagencia" disabled>
                                  <option></option>
                                  <option value="{{ $agencia->id }}">{{ $agencia->ruc }} - {{ $agencia->nombrecomercial }}</option>
                                </select>
                            </div>
                        </div>     
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table" id="tabla-facturacionresumen" style="margin-bottom: 5px;">
                     <thead class="thead-dark">
                        <tr>
                          <th>Tipo</th>
                          <th>Código</th>
                          <th>Cliente</th>
                          <th>Moneda</th>
                          <th>OP. Gravadas</th>
                          <th>IGV</th>
                          <th>Total</th>
                          <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody num="0"></tbody>
                </table>
            </div>
        </div>
        <div id="cont-facturacionresumen-carga"></div>
    </div>
    <div id="cont-facturacionresumen-btn" style="display:none;">
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formfacturacionresumen').submit();">Enviar a Sunat</a>
    </div> 
    </div> 
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
<script>
  $('#reenviaranular').select2({
      placeholder: '-- Seleccionar --',
  }); 
  
  $('#facturador_serie').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  });
  
  $('#idagencia').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  }).val({{$agencia->id}}).trigger('change');
  
  
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
  
  var arrayRepetidos = [];
  
  function cargarventa_boletafactura(facturador_serie,facturador_correlativo){
    load('#cont-facturacionresumen-carga');
    $('#cont-facturacionresumen').css('display','none');
    $('#cont-facturacionresumen-btn').css('display','none');
    $.ajax({
        url:"{{url('backoffice/facturacionresumen/show-seleccionarboletafactura')}}",
        type:'GET',
        data: {
            facturador_serie       : facturador_serie,
            facturador_correlativo : facturador_correlativo,
            tipodocumento          : $('#facturador_serie option:selected').attr('tipo'),
        },
        success: function (respuesta){
            if(respuesta.length) {
                arrayRepetidos.push({tipo: respuesta[0].tipo, id: respuesta[0].id});
                let contadorRepetidos = 0;  
                arrayRepetidos.forEach( (value) => {
                  if (value.tipo == respuesta[0]["tipo"] && value.id == respuesta[0]["id"]) {contadorRepetidos++; }
                });
                
                $('#cont-tabla-facturacionresumen').css('display','block');
                $('#idboletafacturaventa').val(respuesta[0]["id"]);
                $('#cont-facturacionresumen-carga').html('');
                $('#cont-facturacionresumen').css('display','block');
                $('#cont-facturacionresumen-btn').css('display','block');
  
                if (contadorRepetidos > 1) {
                    alert('No se puede agregar el mismo documento 2 veces.');
                }else {
                  agregarproducto({
                    tipo:              respuesta[0]["tipo"],
                    id:                respuesta[0]["id"],
                    serie_correlativo: respuesta[0]["serie_correlativo"],
                    cliente:           respuesta[0]["cliente"],
                    monenda:           respuesta[0]["moneda"],
                    opgravada:         respuesta[0]["venta_montooperaciongravada"],
                    igv:               respuesta[0]["venta_montoigv"],
                    total:             respuesta[0]["venta_montoimpuestoventa"],
                  });
                }

            }else{
                $('#cont-facturacionresumen-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 20px;padding-bottom: 20px;">No existe el Comprobante!</div>');
            }
        }
    })
}

$('#tipoemisionresumen').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-tipoemisionresumen-facturaboleta').css('display','none');
    $('#cont-tipoemisionresumen-venta').css('display','none');
    $('#cont-facturacionresumen').css('display','none');
    $('#cont-facturacionresumen-btn').css('display','none');
    $('#cont-facturacionresumen-carga').html('');
    if(e.currentTarget.value==1){
        $('#cont-tipoemisionresumen-facturaboleta').css('display','block');
    }else if(e.currentTarget.value==2){
        $('#cont-tipoemisionresumen-venta').css('display','block');
    }
}).val(1).trigger('change');
  
$('#facturador_serie').select2({
    placeholder: '-- Serie --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-facturacionresumen').css('display','none');
    $('#cont-facturacionresumen-btn').css('display','none');
});
  
$('#idmotivoresumen').select2({
    placeholder: '-- Seleccionar Motivo --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-tabla-facturacionresumen').css('display','none');
    if($('#tipoemisionresumen').val()==1){
        cargarboletafacturaitem($('#idboletafacturaventa').val(),e.currentTarget.value);
    }else if($('#tipoemisionresumen').val()==2){
        cargarventaitem($('#idboletafacturaventa').val(),e.currentTarget.value);
    }
});

function agregarproducto(data){
    let tipo = '';
    if (data.tipo == 'boleta') {
      tipo = 'Boleta';
    }else if (data.tipo == 'nota_credito') {
      tipo = 'Nota de Credito';      
    }
        
    let num = $("#tabla-facturacionresumen tbody").attr('num');

    let nuevaFila = `<tr id="${num}" idventa_boletafactura="${data.id}" tipo="${data.tipo}">
                        <td>${tipo}</td>
                        <td>${data.serie_correlativo}</td>
                        <td>${data.cliente}</td>
                        <td>${data.monenda}</td>
                        <td>${parseFloat(data.opgravada).toFixed(2)}</td>
                        <td>${parseFloat(data.igv).toFixed(2)}</td>
                        <td>${parseFloat(data.total).toFixed(2)}</td>
                        <td class="with-btn" width="10px"><a id="del${num}" href="javascript:;" onclick="eliminarproducto(${num})" class="btn btn-danger big-btn">
                          <i class="fas fa-trash-alt"></i> Quitar</a>
                        </td>
                     </tr>`;

    $("#tabla-facturacionresumen tbody").append(nuevaFila);
    $("#tabla-facturacionresumen tbody").attr('num',parseInt(num)+1);
}
  
function selectproductos(){
    var data = [];
    $("#tabla-facturacionresumen tbody tr").each(function() {
        data.push({
          idventa_boletafactura : $(this).attr('idventa_boletafactura'),
          tipo : $(this).attr('tipo'),
        });
    });
    return JSON.stringify(data);
}
  
function eliminarproducto(num){
    $("#tabla-facturacionresumen tbody tr#"+num).remove();
}
</script>