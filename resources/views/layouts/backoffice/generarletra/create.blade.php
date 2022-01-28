<div class="modal-content">
  <form action="javascript:;"
                  onsubmit="callback({
                      route: 'backoffice/generarletra',
                      method: 'POST',
                      data:{
                          view: 'registrar',
                          productos: selectproductos(),
                          ttipopagos:serviciost(),
                          letratablacuotas:listarcuotasletra()
                      }
                  },
                  function(resultado){
                       location.href = '{{ url('backoffice/generarletra') }}';                                                  
                  },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Registrar Generar Letra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
          <div class="row">
            <div class="col-md-8"> 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cliente *</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="idcliente">
                          <option></option>
                        </select>
                    </div>
                </div>   
            </div>
            <div class="col-md-4"> 
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Moneda *</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="idmoneda">
                          <option></option>
                          @foreach ($monedas as $value) 
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                        </select>
                    </div>
                </div> 
            </div>
          </div>
        <div class="table-responsive">
            <table class="table" id="tabla-generarletra" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Fecha de registro</th>
                    <th>Vendedor</th>
                    <th>Cajero</th>
                    <th>Forma de Pago</th>
                    <th width="110px">Total</th>
                    <th width="10px"></th>
                  </tr>
                </thead>
                <tbody num="0"></tbody>
            </table>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Deuda Total</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    
                  
                    <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Forma de Pago *</label>
                    <div class="col-sm-8">
                        <?php $formapagos = DB::table('formapago')->where('id',2)->orWhere('id',3)->get(); ?>
                        <select class="form-control" id="idformapago">
                          <option></option>
                          @foreach($formapagos as $value)
                              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                           @endforeach
                        </select>
                    </div>
                </div>  
                <div id="cont-pagocredito" style="display:none;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de Inicio *</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{date('Y-m-d')}}" onclick="calcularFecha()" onchange="calcularFecha()" onkeyup="calcularFecha()" id="creditoiniciopago" class="form-control">
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Frecuencia *</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="creditofrecuencia">
                              <option></option>
                              <option value="1">Días</option>
                              <option value="2">1 Semana</option>
                              <option value="3">1 Quincena</option>
                              <option value="4">1 Mes</option>
                            </select>
                        </div>
                        <label class="col-sm-1 col-form-label">Días *</label>
                        <div class="col-sm-3">
                            <input type="number" value="1" onclick="calcularFecha()" onkeyup="calcularFecha()" id="creditodias" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Ultima Fecha *</label>
                        <div class="col-sm-8">
                            <input type="date" id="creditoultimopago" class="form-control">
                        </div>
                    </div>  
                </div>
                <div id="cont-pagoletra" style="display:none;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Aval ó Garante *</label>
                        <div class="col-sm-7">
                            <select class="form-control" id="idgarante">
                              <option></option>
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'venta/create?view=registrar-cliente',carga:'#mx-modal-carga-cliente'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de Inicio *</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{date('Y-m-d')}}" onclick="agregarcuotas()" onkeyup="agregarcuotas()" onchange="agregarcuotas()" id="letrafechainicio" class="form-control">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Frecuencia *</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="letrafrecuencia">
                              <option value="1">Diario</option>
                              <option value="2">Semanal</option>
                              <option value="3">Quincenal</option>
                              <option value="4">Mensual</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Cuotas *</label>
                        <div class="col-sm-8">
                            <input type="number" value="0" id="letracuota" onkeyup="agregarcuotas()" class="form-control">
                        </div>
                    </div> 
                <div class="form-group row">
                  <div class="col-sm-12">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-ventaproducto-letracuotas" style="margin-bottom: 5px;"> 
                      <thead class="thead-inverse"> 
                        <tr>  
                          <th width="20px">N°</th>
                          <th>N° de Letra</th>
                          <th width="90px">Ultima Fecha</th>
                          <th width="120px">Importe</th>
                        </tr> 
                      </thead> 
                      <tbody num="0">
                      </tbody> 
                      <tfooter>
                      </tfooter> 
                    </table>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Monto a pagar</label>
                  <div class="col-sm-8">
                      <input type="number" value="0.00" id="letratotal" class="form-control" disabled>
                  </div>
                </div> 
                </div>
                  
                </div>
            </div> 
    </div>
    <div class="modal-footer">
      <button type="submit"  class="btn btn-success">Guardar Cambios</button>
    </div> 
  </div> 
  </form> 
</div>
<script>
/* tipo de pago */
$('#idformapago').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#table-tipopago tbody tr').each(function() {
        var num = $(this).attr('num');
        scripttipopago($('#idcreditotipopagot'+num).val(),num);
    });
    if(e.currentTarget.value==2) {
        $('#cont-pagocredito').css('display','block');
        $('#cont-pagoletra').css('display','none');
        $('#table-tipopago > tbody').html('');
        $('#cont-tipopago').css('display','none');
    }else if(e.currentTarget.value==3) {
        $('#cont-pagocredito').css('display','none');
        $('#cont-pagoletra').css('display','block');
        $('#table-tipopago > tbody').html('');
        $('#cont-tipopago').css('display','none');
    }
}).val({{isset($idformapago)?$idformapago:'0'}}).trigger("change");
$('#creditofrecuencia').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    @if(isset($creditodias))
    @else
    var frecuencia = $('#creditofrecuencia').val();
    if(frecuencia == 2) {
        $('#creditodias').val('7');
        $('#creditodias').attr('dias',7);
        $('#creditodias').attr('disabled',true);
    }else if(frecuencia == 3) {
        $('#creditodias').val('15');
        $('#creditodias').attr('dias',15);
        $('#creditodias').attr('disabled',true);
    }else if(frecuencia == 4) {
        $('#creditodias').val('30');
        $('#creditodias').attr('dias',30);
        $('#creditodias').attr('disabled',true);
    }else if(frecuencia == 1) {
        $('#creditodias').val('1');
        $('#creditodias').attr('dias',1);
        $('#creditodias').removeAttr('disabled');
    }
    // calcular fecha
    var fecha = $('#creditoiniciopago').val();
    if($('#creditofrecuencia').val()==1) {
        var d = $('#creditodias').val();
    }else{
        var d = $('#creditodias').attr('dias');
    }
    var Fecha = new Date();
    var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
    var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
    var aFecha = sFecha.split(sep);
    var fecha = aFecha[0]+'/'+aFecha[1]+'/'+aFecha[2];
    fecha= new Date(fecha);
    fecha.setDate(fecha.getDate()+parseInt(d));
    var anno=fecha.getFullYear();
    var mes= fecha.getMonth()+1;
    var dia= fecha.getDate();
    mes = (mes < 10) ? ("0" + mes) : mes;
    dia = (dia < 10) ? ("0" + dia) : dia;
    var fechaFinal = anno+sep+mes+sep+dia;
    $('#creditoultimopago').val(fechaFinal);
    @endif
}).val(1).trigger('change');
  
$('#idgarante').select2({
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
});
$("#letrafrecuencia").select2({
    placeholder: "-- Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    {{isset($letras)?'':'agregarcuotas();'}}
});
  
function calcularFecha(){
   var fecha = $('#creditoiniciopago').val();
   if($('#creditofrecuencia').val()==1) {
       var d = $('#creditodias').val();
   }else{
       var d = $('#creditodias').attr('dias');
   }
   $('#creditoultimopago').val(sumaFecha(fecha,d));
}
function agregarcuotas() {
    var fecha = $('#letrafechainicio').val();
    var frecuencia = $('#letrafrecuencia').val();
    var letracuota = $('#letracuota').val();
    $('#table-ventaproducto-letracuotas > tbody').html('');
    var c = 0;
    var total = 0;
    if($('#totalventa').val()!=null){
        total = parseFloat($('#totalventa').val());
    }else if($('#totalcompra').val()!=null){
        total = parseFloat($('#totalcompra').val());
    }
    var montocuota = total/parseInt(letracuota);
    var tablehtml = '';
    for(var i=1; i < parseInt(letracuota)+1; i++) {
        if(frecuencia == 1) {
            var d = c+1;
        }else if(frecuencia == 2) {
            var d = c + 7;
        }else if(frecuencia == 3) {
            var d = c + 15;
        }else if(frecuencia == 4) {
            var d = c + 30;
        }
        tablehtml = tablehtml+'<tr id="'+i+'" num="'+i+'">'+ 
            '<td>'+i+'</td>'+
            '<td class="mx-td-text"><input type="text" class="form-control" id="ventaletranumerounico'+i+'"></td>'+
            '<td class="mx-td-text"><input type="date" value="'+sumaFecha(fecha,d)+'" class="form-control" id="ventaletrafecha'+i+'"></td>'+
            '<td class="mx-td-text"><input type="number" value="'+montocuota.toFixed(2)+'" onclick="sumaletratotal()" onkeyup="sumaletratotal()" onchange="sumaletratotal()" class="form-control" id="ventaletramonto'+i+'" step="0.01"></td>'+
        '</tr>';
        fecha = sumaFecha(fecha,d);
    }
    $('#table-ventaproducto-letracuotas > tbody').html(tablehtml);
    sumaletratotal();
}
function sumaletratotal(){
    var totalletra = 0;
    $('#table-ventaproducto-letracuotas>tbody>tr').each(function() {
        totalletra = totalletra+parseFloat($('#ventaletramonto'+$(this).attr('num')).val());
    });
    $('#letratotal').val(totalletra.toFixed(2));
}
function sumaFecha(fecha,d){
    var Fecha = new Date();
    var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
    var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
    var aFecha = sFecha.split(sep);
    var fecha = aFecha[0]+'/'+aFecha[1]+'/'+aFecha[2];
    fecha= new Date(fecha);
    fecha.setDate(fecha.getDate()+parseInt(d));
    var anno=fecha.getFullYear();
    var mes= fecha.getMonth()+1;
    var dia= fecha.getDate();
    mes = (mes < 10) ? ("0" + mes) : mes;
    dia = (dia < 10) ? ("0" + dia) : dia;
    var fechaFinal = anno+sep+mes+sep+dia;
    return fechaFinal;
}  
function listarcuotasletra() {
    var listacutotas = '';
    $('#table-ventaproducto-letracuotas>tbody>tr').each(function() {
        listacutotas = listacutotas+','+$(this).attr('id')+
            '/val/'+$('#ventaletranumerounico'+$(this).attr('num')).val()+
            '/val/'+$('#ventaletrafecha'+$(this).attr('num')).val()+
            '/val/'+$('#ventaletramonto'+$(this).attr('num')).val();
    });
    return listacutotas;
}
function serviciost(){
    var servicios = '';
    $('#table-tipopago tbody tr').each(function() {
        var num = $(this).attr('num');
        servicios = servicios+'&'+
            $('#totalefectivo'+num).val()+'/'+
            $('#idcreditodepositobanco'+num).val()+'/'+
            $('#pagocreditodepositonumerocuenta'+num).val()+'/'+
            $('#pagocreditodepositofechadeposito'+num).val()+'/'+
            $('#pagocreditodepositonumerooperacion'+num).val()+'/'+
            $('#totaldeposito'+num).val()+'/'+
            $('#idchequebanco'+num).val()+'/'+
            $('#pagocreditochequefechaemision'+num).val()+'/'+
            $('#pagocreditochequefechavencimiento'+num).val()+'/'+
            $('#pagocreditochequenumero'+num).val()+'/'+
            $('#totalcheque'+num).val()+'/'+
            $('#idcreditotipopagot'+num).val()+'/'+
            $('#pagocreditodepositohoradeposito'+num).val();
    });
    return servicios;
}
/* fin tipo de pago */    
$('#idcliente').select2({
    ajax: {
        url:"{{url('backoffice/generarletra/show-listarcliente')}}",
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
    $("#tabla-generarletra tbody").html('');
    $.ajax({
        url:"{{url('backoffice/generarletra/show-seleccionarventas')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       },
       success: function (respuesta){
          $.each(respuesta['ventas'], function( key, value ) {
              agregarproducto(
                  value['id'],
                  (value['codigo']).padStart(6,"0"),
                  value['fecharegistro'],
                  value['nombreusuariovendedor'],
                  value['nombreusuariocajero'],
                  value['formapago'],
                  value['totalpagado'],
                  value['deudatotal']
              );
          });
       }
     })
});
  
$('#idmoneda').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val(1).trigger('change');
  
$('#idformapago').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val(1).trigger('change');

$('#idestado').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val(2).trigger('change');
  
function agregarproducto(idventa,codigo,fecharegistro,vendedor,cajero,formapago,totalpagado,deudatotal){
      var num = $("#tabla-generarletra tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idventa="'+idventa+'" totalpagado="'+totalpagado+'" deudatotal="'+deudatotal+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+fecharegistro+'</td>';
          nuevaFila+='<td>'+vendedor+'</td>';
          nuevaFila+='<td>'+cajero+'</td>';
          nuevaFila+='<td>'+formapago+'</td>';
          nuevaFila+='<td>'+deudatotal+'</td>';   
          nuevaFila+='<td class="with-btn"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fas fa-trash-alt"></i> Quitar</a></td>'
          nuevaFila+='</tr>';
      $("#tabla-generarletra tbody").append(nuevaFila);
      $("#tabla-generarletra tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}

function calcularmonto(){
        var total = 0;
        $("#tabla-generarletra tbody tr").each(function() {
            var num = $(this).attr('id');        
            var deudatotal = $(this).attr('deudatotal');   
            total = total+parseFloat(deudatotal);
        });
        $("#totalventa").val((parseFloat(total)).toFixed(2)); 
}

function selectproductos(){
    var data = '';
    $("#tabla-generarletra tbody tr").each(function() {  
        var idventa = $(this).attr('idventa');
        var deudatotal = $(this).attr('deudatotal');
        data = data+'&'+idventa+','+deudatotal;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-generarletra tbody tr#"+num).remove();
    calcularmonto();
}
</script>