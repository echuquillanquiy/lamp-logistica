<div class="modal-content">
  <div id="carga-facturacionresumen">
    <div class="modal-header">
        <h4 class="modal-title">Detalle Resumen Diario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body"><br>
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
                    <div class="col-md-6"> 
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Agencia *</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="idagencia" disabled>
                                  <option value="{{ $agencia->id }}">{{ $agencia->ruc }} - {{ $agencia->nombrecomercial }}</option>
                                </select>
                            </div>
                        </div>     
                    </div>
                    <div class="col-md-6"> 
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Correlativo *</label>
                            <div class="col-sm-10">
                              <input class="form-control" type="text" id="facturador_correlativo" value="{{ $facturacionresumen->resumen_correlativo }}" placeholder="Correlativo" disabled/>
                            </div>
                        </div>     
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table" id="tabla-facturacionresumen" style="margin-bottom: 5px;">
                         <thead class="thead-dark">
                            <tr>
                              <th>Código</th>
                              <th>Cliente</th>
                              <th>OP. Gravadas</th>
                              <th>IGV</th>
                              <th>Total</th>
                            </tr>
                        </thead>
                        <tbody num="0">
                        @foreach($facturacionresumendetalles as $value)
                          <?php
                            $facturacion = DB::table('facturacionboletafactura')->where('facturacionboletafactura.id', $value->idfacturacionboletafactura)->first();
                          ?>
                          <tr>
                            <td>{{ $value->serienumero }}</td>
                            <td>{{ $facturacion->cliente_numerodocumento }} - {{ $facturacion->cliente_razonsocial }}</td>
                            <td>{{ $value->operacionesgravadas }}</td>
                            <td>{{ $value->montoigv }}</td>
                            <td>{{ $value->total }}</td>
                          </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
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
  

  function cargarventa_boletafactura(facturador_serie,facturador_correlativo){
    load('#cont-facturacionresumen-carga');
    $('#cont-facturacionresumen').css('display','none');
    $('#cont-facturacionresumen-btn').css('display','none');
    $.ajax({
        url:"{{url('backoffice/facturacionresumen/show-seleccionarboletafactura')}}",
        type:'GET',
        data: {
            facturador_serie       : facturador_serie,
            facturador_correlativo : facturador_correlativo
        },
        success: function (respuesta){
          console.log(respuesta["facturacionboletafactura"].venta_montooperaciongravada);
            if(respuesta["facturacionboletafactura"]!=undefined){    
              console.log(respuesta["facturacionboletafactura"]);
                $('#cont-tabla-facturacionresumen').css('display','block');
                $('#idboletafacturaventa').val(respuesta["facturacionboletafactura"].id);
                $('#cont-facturacionresumen-carga').html('');
                $('#cont-facturacionresumen').css('display','block');
                $('#cont-facturacionresumen-btn').css('display','block');

                agregarproducto(
                   1,
                   respuesta["facturacionboletafactura"].id,
                   respuesta["facturacionboletafactura"].venta_serie+' - '+respuesta["facturacionboletafactura"].venta_correlativo,
                   respuesta["facturacionboletafactura"].cliente,
                   respuesta["facturacionboletafactura"].monedanombre,
                   respuesta["facturacionboletafactura"].venta_montooperaciongravada,
                   respuesta["facturacionboletafactura"].venta_montoigv,
                   respuesta["facturacionboletafactura"].venta_montoimpuestoventa,
                );

            }
            else{
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

function agregarproducto(idtipo,id,codigo,cliente,monenda,opgravada,igv,total){
      var tipo = '';
      if(idtipo==2){
          tipo = 'Venta';
      }else if(idtipo==1){
          tipo = 'Comprobante';
      }
      let num = $("#tabla-facturacionresumen tbody").attr('num');
  
      let nuevaFila = `<tr id="${num}" idventa_boletafactura="${id}" idtipo="${idtipo}">
                          <td>${tipo}</td>
                          <td>${codigo}</td>
                          <td>${cliente}</td>
                          <td>${monenda}</td>
                          <td>${parseFloat(opgravada).toFixed(2)}</td>
                          <td>${parseFloat(igv).toFixed(2)}</td>
                          <td>${parseFloat(total).toFixed(2)}</td>
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
          idtipo :  $(this).attr('idtipo'),
        });
    });
    return JSON.stringify(data);
}
  
function eliminarproducto(num){
    $("#tabla-facturacionresumen tbody tr#"+num).remove();
}
</script>