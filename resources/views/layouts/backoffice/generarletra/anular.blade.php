<div class="modal-content">
  <form action="javascript:;"
                  onsubmit="callback({
                      route: 'backoffice/generarletra/{{ $venta->id }}',
                      method: 'PUT',
                      data:{
                          view: 'anular'
                      }
                  },
                  function(resultado){
                       location.href = '{{ url('backoffice/generarletra') }}';                                                  
                  },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Anular Generar Letra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
          <div class="row">
            <div class="col-md-8"> 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cliente</label>
                    <div class="col-sm-10">
                        <input type="text" value="{{$venta->cliente}}" class="form-control" disabled>
                    </div>
                </div>   
            </div>
            <div class="col-md-4"> 
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Moneda</label>
                    <div class="col-sm-9">
                        <input type="text" value="{{$venta->monedanombre}}" class="form-control" disabled>
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
                    <label class="col-sm-4 col-form-label">Forma de Pago</label>
                    <div class="col-sm-8">
                        <?php $formapagos = DB::table('formapago')->where('id',2)->orWhere('id',3)->get(); ?>
                        <select class="form-control" id="idformapago" disabled>
                          <option></option>
                          @foreach($formapagos as $value)
                              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                           @endforeach
                        </select>
                    </div>
                </div>  
                <div id="cont-pagocredito" style="display:none;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de Inicio</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{$venta->fp_credito_fechainicio}}" id="creditoiniciopago" class="form-control" disabled>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Frecuencia</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="creditofrecuencia" disabled>
                              <option></option>
                              <option value="1">Días</option>
                              <option value="2">1 Semana</option>
                              <option value="3">1 Quincena</option>
                              <option value="4">1 Mes</option>
                            </select>
                        </div>
                        <label class="col-sm-1 col-form-label">Días</label>
                        <div class="col-sm-3">
                            <input type="number" value="{{$venta->fp_credito_dias}}" id="creditodias" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Ultima Fecha</label>
                        <div class="col-sm-8">
                            <input type="date" id="creditoultimopago" value="{{$venta->fp_credito_ultimafecha}}" class="form-control" disabled>
                        </div>
                    </div>  
                </div>
                <div id="cont-pagoletra" style="display:none;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Aval ó Garante</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="idgarante" disabled>
                              <option value="{{$venta->idusuariocliente}}">{{$venta->cliente}}</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de Inicio</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{$venta->fp_letra_fechainicio}}" id="letrafechainicio" class="form-control" disabled>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Frecuencia</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="letrafrecuencia" disabled>
                              <option value="1">Diario</option>
                              <option value="2">Semanal</option>
                              <option value="3">Quincenal</option>
                              <option value="4">Mensual</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Cuotas</label>
                        <div class="col-sm-8">
                            <input type="number" value="{{$venta->fp_letra_cuotas}}" id="letracuota" class="form-control" disabled>
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
                        <?php
                        $letras = DB::table('ventaletra')
                            ->where('idventa',$venta->id)
                            ->orderBy('ventaletra.numero','asc')
                            ->get();
                        ?>
                        <?php $totalmontoletra=0 ?>
                        @foreach($letras as $value)
                        <tr> 
                          <td>{{$value->numero}}</td>
                          <td>{{$value->numeroletra}}</td>
                          <td>{{$value->fechafin}}</td>
                          <td>{{$value->monto}}</td>
                       </tr>
                        <?php $totalmontoletra=$totalmontoletra+$value->monto ?>
                        @endforeach
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
                      <input type="number" value="{{number_format($totalmontoletra, 2, '.', '')}}" id="letratotal" class="form-control" disabled>
                  </div>
                </div> 
                </div>
                  
                </div>
            </div> 
        <div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de anular?
				</div>
    </div>
    <div class="modal-footer">
      <button type="submit"  class="btn btn-success">Anular</button>
    </div> 
  </div> 
  </form> 
</div>
<script>
/* tipo de pago*/
$('#idformapago').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
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
}).val({{$venta->idformapago}}).trigger("change");
$('#creditofrecuencia').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$venta->fp_credito_frecuencia}}).trigger('change');
  
$('#idgarante').select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
$("#letrafrecuencia").select2({
    placeholder: "-- Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$venta->fp_letra_frecuencia}}).trigger('change');

/* fin tipo de pago*/    
@foreach($ventagenerars as $value)
    agregarproducto(
      '{{$value['id']}}',
                  ('{{$value['codigo']}}').padStart(6,"0"),
                   '{{$value['fecharegistro']}}',
                   '{{$value['nombreusuariovendedor']}}',
                   '{{$value['nombreusuariocajero']}}',
                   '{{$value['formapago']}}',
                   '{{$value['totalpagado']}}',
                   '{{$value['deudatotal']}}'
    );
@endforeach  
$('#idmoneda').select2({
   placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val(1).trigger('change');

  
function agregarproducto(idventa,codigo,fecharegistro,vendedor,cajero,formapago,totalpagado,deudatotal){
      var num = $("#tabla-generarletra tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idventa="'+idventa+'" totalpagado="'+totalpagado+'" deudatotal="'+deudatotal+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+fecharegistro+'</td>';
          nuevaFila+='<td>'+vendedor+'</td>';
          nuevaFila+='<td>'+cajero+'</td>';
          nuevaFila+='<td>'+formapago+'</td>';
          nuevaFila+='<td>'+deudatotal+'</td>';   
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