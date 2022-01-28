<?php
if(isset($modulo)){
  
    if(!isset($formapago)){
        if($modulo=='movimiento'){
            $tablemodulo = DB::table('movimiento')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='compra'){
            $tablemodulo = DB::table('compra')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='compradevolucion'){
            $tablemodulo = DB::table('compradevolucion')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='pagocredito'){
            $tablemodulo = DB::table('pagocredito')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='pagoletra'){
            $tablemodulo = DB::table('pagoletra')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='venta'){
            $tablemodulo = DB::table('venta')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='notadevolucion'){
            $tablemodulo = DB::table('notadevolucion')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='cobranzacredito'){
            $tablemodulo = DB::table('cobranzacredito')
                ->whereId($idmodulo)
                ->first();
        }elseif($modulo=='cobranzaletra'){
            $tablemodulo = DB::table('cobranzaletra')
                ->whereId($idmodulo)
                ->first();
        }
        $idformapago = isset($tablemodulo->idformapago)?$tablemodulo->idformapago:'';
        $creditoiniciopago = isset($tablemodulo->fp_credito_fechainicio)?$tablemodulo->fp_credito_fechainicio:'';
        $creditofrecuencia = isset($tablemodulo->fp_credito_frecuencia)?$tablemodulo->fp_credito_frecuencia:'';
        $creditodias = isset($tablemodulo->fp_credito_dias)?$tablemodulo->fp_credito_dias:'';
        $creditoultimopago = isset($tablemodulo->fp_credito_ultimafecha)?$tablemodulo->fp_credito_ultimafecha:'';
        $letraidgarante = isset($tablemodulo->fp_letra_garante)?$tablemodulo->fp_letra_garante:'';
        $letrafechainicio = isset($tablemodulo->fp_letra_fechainicio)?$tablemodulo->fp_letra_fechainicio:'';
        $letrafrecuencia = isset($tablemodulo->fp_letra_frecuencia)?$tablemodulo->fp_letra_frecuencia:'';
        $letracuota = isset($tablemodulo->fp_letra_cuotas)?$tablemodulo->fp_letra_cuotas:'';

        $letras = DB::table('tipopagoletra')
            ->where('tipopagoletra.id'.$modulo,$idmodulo)
            ->orderBy('tipopagoletra.numero','asc')
            ->get();
    }
        
  
    $tipopagodetalles = DB::table('tipopagodetalle')
        ->leftJoin('users','users.id','tipopagodetalle.saldo_cliente')
        ->where('tipopagodetalle.id'.$modulo,$idmodulo)
        ->select(
            'tipopagodetalle.*',
            DB::raw('IF(users.idtipopersona=1,
            CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
            CONCAT(users.identificacion," - ",users.apellidos)) as saldo_clientenombre')
        )
        ->orderBy('tipopagodetalle.id','asc')
        ->get(); 
}

?>
                <div <?php echo isset($formapago)?'style="display:none;"':''?>>
                <div class="form-group row" style="display:none;">
                    <label class="col-sm-4 col-form-label">Forma de Pago {{isset($disabled)?'':'*'}}</label>
                    <div class="col-sm-8">
                        <?php $formapagos = DB::table('formapago')->get(); ?>
                        <select class="form-control" id="idformapago" {{isset($disabled)?'disabled':''}}>
                          <option></option>
                          @foreach($formapagos as $value)
                              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                           @endforeach
                        </select>
                    </div>
                </div>  
                </div>  
                @if(!isset($formapago))
                <div id="cont-pagocredito" style="display:none;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de Inicio {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{isset($creditoiniciopago)?($creditoiniciopago!=''?$creditoiniciopago:date('Y-m-d')):date('Y-m-d')}}" onclick="calcularFecha()" onchange="calcularFecha()" onkeyup="calcularFecha()" id="creditoiniciopago" class="form-control" {{isset($disabled)?'disabled':''}}>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Frecuencia {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="creditofrecuencia" {{isset($disabled)?'disabled':''}}>
                              <option></option>
                              <option value="1">Días</option>
                              <option value="2">1 Semana</option>
                              <option value="3">1 Quincena</option>
                              <option value="4">1 Mes</option>
                            </select>
                        </div>
                        <label class="col-sm-1 col-form-label">Días {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-3">
                            <input type="number" value="{{isset($creditodias)?$creditodias:1}}" onclick="calcularFecha()" onkeyup="calcularFecha()" id="creditodias" class="form-control" {{isset($disabled)?'disabled':''}}>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Ultima Fecha {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{isset($creditoultimopago)?$creditoultimopago:''}}" id="creditoultimopago" class="form-control" {{isset($disabled)?'disabled':''}}>
                        </div>
                    </div>  
                </div>
                <div id="cont-pagoletra" style="display:none;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Aval ó Garante {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-{{isset($disabled)?'8':'7'}}">
                            <select class="form-control" id="letraidgarante" {{isset($disabled)?'disabled':''}}>
                              @if(isset($letraidgarante))
                              <?php 
                              $clientegarante = DB::table('users')
                                  ->whereId($letraidgarante)
                                  ->select(
                                      DB::raw('IF(users.idtipopersona=1,
                                      CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre),
                                      CONCAT(users.identificacion," - ",users.nombre)) as cliente')
                                  )
                                  ->first();
                              ?>
                              @if($clientegarante!='')
                              <option value="{{$letraidgarante}}">{{$clientegarante->cliente}}</option>
                              @else
                              <option></option>
                              @endif
                              @else
                              <option></option>
                              @endif
                            </select>
                        </div>
                        @if(isset($disabled))
                        @else
                        <div class="col-sm-1">
                            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'venta/create?view=registrar-cliente',carga:'#mx-modal-carga-cliente'})" style="width: 100%;"><i class="fas fa-plus"></i></a>
                        </div>
                        @endif
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha de Inicio {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-8">
                            <input type="date" value="{{isset($letrafechainicio)?$letrafechainicio:date('Y-m-d')}}" onclick="agregarcuotas()" onkeyup="agregarcuotas()" onchange="agregarcuotas()" id="letrafechainicio" class="form-control" {{isset($disabled)?'disabled':''}}>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Frecuencia {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="letrafrecuencia" {{isset($disabled)?'disabled':''}}>
                              <option value="1">Diario</option>
                              <option value="2">Semanal</option>
                              <option value="3">Quincenal</option>
                              <option value="4">Mensual</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Cuotas {{isset($disabled)?'':'*'}}</label>
                        <div class="col-sm-8">
                            <input type="number" value="{{isset($letracuota)?$letracuota:'0'}}" id="letracuota" onkeyup="agregarcuotas()" class="form-control" {{isset($disabled)?'disabled':''}}>
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
                        @if(isset($letras))  
                        <?php $totalmontoletra=0 ?>
                        <?php $i=0 ?>
                        @foreach($letras as $value)
                        <tr id="{{$i}}" num="{{$i}}"> 
                          <td>{{$value->numero}}</td>
                          @if(isset($disabled))
                          <td>{{$value->numeroletra}}</td>
                          <td>{{$value->fecha}}</td>
                          <td>{{$value->monto}}</td>
                          @else
                          <td class="mx-td-text"><input type="text" class="form-control" value="{{$value->numeroletra}}" id="ventaletranumerounico{{$i}}" {{isset($disabled)?'disabled':''}}></td>
                          <td class="mx-td-text"><input type="date" value="{{$value->fecha}}" class="form-control" id="ventaletrafecha{{$i}}" {{isset($disabled)?'disabled':''}}></td>
                          <td class="mx-td-text"><input type="number" value="{{$value->monto}}" onclick="sumaletratotal()" onkeyup="sumaletratotal()" onchange="sumaletratotal()" class="form-control" id="ventaletramonto{{$i}}" step="0.01" {{isset($disabled)?'disabled':''}}></td>
                          @endif
                        </tr>
                        <?php $i++ ?>
                        <?php $totalmontoletra=$totalmontoletra+$value->monto ?>
                        @endforeach
                        @endif
                      </tbody> 
                      <tfooter>
                      </tfooter> 
                    </table>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Total Letra</label>
                  <div class="col-sm-8">
                      <input type="number" value="{{isset($letras)?number_format($totalmontoletra, 2, '.', ''):'0'}}" id="letratotal" class="form-control" disabled>
                  </div>
                </div> 
                </div>
                @endif
                <div id="cont-tipopago" style="display:none;">                    
                <table class="table table-striped table-bordered table-hover" id="table-tipopago" style="margin-bottom: 5px;"> 
                  <thead class="thead-dark"> 
                    <tr>  
                      <th style="font-size: 15px;">Tipo de Pago</th>
                       @if(!isset($disabled))
                      <?php
                $bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'bancocuentabancaria.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get();
                $bancoshtml = '';
                              foreach($bancos as $valuebanco){
                                  $bancoshtml = $bancoshtml.'<option value="'.$valuebanco->id.'" numerocuenta="'.$valuebanco->numerocuenta.'">'.$valuebanco->nombre.'</option>';
                              }
            ?>
                      
                          <th width="20px" class="mx-td-text"><a href="javascript:;" onclick="agregartipopago(
                                                                                                              '',
                                                                                                              1,
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '',
                                                                                                              '{{$bancoshtml}}'
                                                                                                          );" class="btn btn-success" ><i class="fa fa-plus" aria-hidden="true"></i></a></th>
                      @endif
                    </tr> 
                  </thead> 
                  <tbody num="0">
                  </tbody> 
                </table>
                  <div style="background-color: #065f65;padding: 8px;font-size: 23px;color: white;height: 45px;margin-bottom: 5px;">
                      <div style="text-align: right;width: 50%;float: left;">
                        @if(isset($disabled))
                        Total Pagado:
                        @else
                        Total a Pagar:
                        @endif
                        &nbsp;&nbsp;</div><div style="width: 50%;float: left;" id="totalformapago">0.00</div>
                  </div> 
                </div>
<style>
table .select2-container {
    margin-top: -9px !important;
    margin-bottom: -8px !important;
}
  .mx-td-text{
  padding: 5px !important;
    padding-left: 6px !important;
  }
</style>
<script>
/* tipo de pago */
$('#idformapago').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#table-tipopago tbody tr').each(function() {
        var num = $(this).attr('num');
        scripttipopago($('#idtipopago'+num).val(),num);
    });
    if(e.currentTarget.value==1) {
        $('#cont-pagocredito').css('display','none');
        $('#cont-pagoletra').css('display','none');
        $('#table-tipopago > tbody').html('');
        $('#cont-tipopago').css('display','block');

        @if(isset($tipopagodetalles))
        @if(count($tipopagodetalles)>0)
        @foreach($tipopagodetalles as $value)
        <?php
                          $bancoshtml = '';
                          if($value->id < 4601){
                            
                              $bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'banco.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get();
                              foreach($bancos as $valuebanco){
                                  $bancoshtml = $bancoshtml.'<option value="'.$valuebanco->id.'" numerocuenta="'.$valuebanco->numerocuenta.'">'.$valuebanco->nombre.'</option>';
                              }
                          }else{
                              $bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'bancocuentabancaria.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get();
                              foreach($bancos as $valuebanco){
                                  $bancoshtml = $bancoshtml.'<option value="'.$valuebanco->id.'" numerocuenta="'.$valuebanco->numerocuenta.'">'.$valuebanco->nombre.'</option>';
                              }
                          }
      ?>
        agregartipopago(
            '{{$value->monto}}',
            '{{$value->idtipopago}}',
            '{{$value->deposito_banco}}',
            '{{$value->deposito_numerocuenta}}',
            '{{$value->deposito_fecha}}',
            '{{$value->deposito_hora}}',
            '{{$value->deposito_numerooperacion}}',
            '{{$value->cheque_banco}}',
            '{{$value->cheque_emision}}',
            '{{$value->cheque_vencimiento}}',
            '{{$value->cheque_numero}}',
            '{{$value->saldo_cliente}}',
            '{{$value->saldo_clientenombre}}',
            '{{$bancoshtml}}'
        );
        @endforeach
        @else
            <?php
                $bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'bancocuentabancaria.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get();
                $bancoshtml = '';
                              foreach($bancos as $valuebanco){
                                  $bancoshtml = $bancoshtml.'<option value="'.$valuebanco->id.'" numerocuenta="'.$valuebanco->numerocuenta.'">'.$valuebanco->nombre.'</option>';
                              }
            ?>
            agregartipopago(
                '',
                1,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '{{$bancoshtml}}'
            );
        @endif
        @else
            <?php
                $bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'bancocuentabancaria.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get();
                $bancoshtml = '';
                              foreach($bancos as $valuebanco){
                                  $bancoshtml = $bancoshtml.'<option value="'.$valuebanco->id.'" numerocuenta="'.$valuebanco->numerocuenta.'">'.$valuebanco->nombre.'</option>';
                              }
            ?>
            agregartipopago(
                '',
                1,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '{{$bancoshtml}}'
            );
        @endif
    }else if(e.currentTarget.value==2) {
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
}).val('{{isset($idformapago)?$idformapago:1}}').trigger("change");
$('#creditofrecuencia').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).on("change", function(e) {
   
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
        @if(!isset($creditodias))
        $('#creditodias').val('1');
        $('#creditodias').attr('dias',1);
        $('#creditodias').removeAttr('disabled');
        @endif
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
    
}).val({{isset($creditofrecuencia)?($creditofrecuencia!=''?$creditofrecuencia:'1'):'1'}}).trigger('change');
  
$('#letraidgarante').select2({
    ajax: {
        url:"{{url('backoffice/inicio/show-listarclientes')}}",
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
}).val({{isset($letrafrecuencia)?($letrafrecuencia!=''?$letrafrecuencia:'null'):'null'}}).trigger('change');
  
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
            '<td class="mx-td-text"><input type="text" class="form-control" id="ventaletranumerounico'+i+'" {{isset($disabled)?'disabled':''}}></td>'+
            '<td class="mx-td-text"><input type="date" value="'+sumaFecha(fecha,d)+'" class="form-control" id="ventaletrafecha'+i+'" {{isset($disabled)?'disabled':''}}></td>'+
            '<td class="mx-td-text"><input type="number" value="'+montocuota.toFixed(2)+'" onclick="sumaletratotal()" onkeyup="sumaletratotal()" onchange="sumaletratotal()" class="form-control" id="ventaletramonto'+i+'" step="0.01" {{isset($disabled)?'disabled':''}}></td>'+
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
        var num = $(this).attr('num');
        listacutotas = listacutotas+'/&/id'+num+'/-/'+$(this).attr('id')+
            '/,/ventaletranumerounico'+num+'/-/'+$('#ventaletranumerounico'+num).val()+
            '/,/ventaletrafecha'+num+'/-/'+$('#ventaletrafecha'+num).val()+
            '/,/ventaletramonto'+num+'/-/'+$('#ventaletramonto'+num).val();
    });
    return listacutotas;
}
/* fin tipo de pago */  


function agregartipopago(
    mx_monto='',
    mx_tipopago=1,
    deposito_banco='',
    deposito_numerocuenta='',
    deposito_fecha='',
    deposito_hora='',
    deposito_numerooperacion='',
    cheque_banco='',
    cheque_emision='',
    cheque_vencimiento='',
    cheque_numero='',
    saldo_cliente='',
    saldo_clientenombre='',
    bancos='') {
     var num = $('#table-tipopago tbody').attr('num');
     var btneliminar = '';
     @if(!isset($disabled))
        var canttable = $('#table-tipopago tbody tr').length;
        btneliminar = '<td class="mx-td-text">';
        if(canttable>0){
            btneliminar = btneliminar+'<div class="mx-br"></div>'+
                    '<a href="javascript:;" id = remover onclick="eliminarservicio('+num+')"class="btn btn-danger"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>';
        }
        btneliminar = btneliminar+'</td>';
    @endif
      
    
    var bancos = bancos.replace(/&lt;/gi, "<");
    var bancos = bancos.replace(/&gt;/gi, ">");
    var bancos = bancos.replace(/&quot;/gi, '"');
      
     $('#table-tipopago tbody').append(
            '<tr id="'+num+'"  num="'+num+'" >'+
              '<td class="with-form-control-select" style="padding-top: 15px;padding-bottom: 0px;">'+
                 '<select id="idtipopago'+num+'" class="form-control" {{isset($disabled)?'disabled':''}} disabled>'+
                    '<option></option>'+
                   <?php $tipopagos = DB::table('tipopago')->get(); ?>
                   @foreach($tipopagos as $value)
                    '<option value="{{ $value->id }}">{{ $value->nombre }}</option>'+
                   @endforeach
                 '</select>'+
                 '<div id="divefectivo'+num+'" style="display:none;margin-top: 14px">'+
                       '<div class="form-group row">'+
                          '<label class="col-sm-4 col-form-label">Monto {{isset($disabled)?'':'*'}}</label>'+
                          '<div class="col-sm-8">'+
                             '<input type="number" id="efectivo_total'+num+'" value="'+mx_monto+'" onclick="totalformapago()" onkeyup="totalformapago()" placeholder="0.00" class="form-control" step="0.01" min="0" style="font-size: 20px;" {{isset($disabled)?'disabled':''}}>'+
                          '</div>'+
                       '</div>'+
                 '</div>'+
                 '<div id="divdeposito'+num+'" style="display:none;margin-top: 20px">'+
                    '<div class="form-group row">'+
                      '<label class="col-sm-4 col-form-label">Banco {{isset($disabled)?'':'*'}}</label>'+
                      '<div class="col-sm-8">'+
                        '<select id="deposito_banco'+num+'" class="form-control" style="width:100%;" {{isset($disabled)?'disabled':''}}>'+
                          '<option></option>'+bancos+
                          <?php 
                          
                          
                          /*$bancos = DB::table('bancocuentabancaria')
                              ->join('banco','banco.id','bancocuentabancaria.idbanco')
                              ->select(
                                  'bancocuentabancaria.id as id', 
                                  'bancocuentabancaria.numerocuenta as numerocuenta', 
                                  DB::raw('CONCAT(banco.nombre," - ",bancocuentabancaria.nombre) as nombre')
                              )
                              ->orderBy('bancocuentabancaria.id','desc')
                              ->get();*/
                          ?>
                        '</select>'+
                      '</div>'+
                    '</div>'+
                     '<div class="form-group row">'+
                        '<label class="col-sm-4 col-form-label">Número de Cuenta {{isset($disabled)?'':'*'}}</label>'+
                        '<div class="col-sm-8">'+
                           '<input type="text" id="deposito_numerocuenta'+num+'" value="'+deposito_numerocuenta+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                        '</div>'+
                     '</div>'+
                     '<div id="depositocreditoletra'+num+'" style="display:block;">'+     
                       '<div class="form-group row">'+
                          '<label class="col-sm-4 col-form-label">Fecha de Deposito {{isset($disabled)?'':'*'}}</label>'+
                          '<div class="col-sm-8">'+
                             '<input type="date" id="deposito_fechadeposito'+num+'" value="'+deposito_fecha+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                          '</div>'+
                       '</div>'+
                       '<div class="form-group row">'+
                        '<label class="col-sm-4 col-form-label">Hora de Deposito {{isset($disabled)?'':'*'}}</label>'+
                        '<div class="col-sm-8">'+
                          '<input type="time" id="deposito_horadeposito'+num+'" value="'+deposito_hora+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                        '</div>'+
                      '</div>'+
                       '<div class="form-group row">'+
                          '<label class="col-sm-4 col-form-label">Número de Operación {{isset($disabled)?'':'*'}}</label>'+
                          '<div class="col-sm-8">'+
                             '<input type="number" id="deposito_numerooperacion'+num+'" value="'+deposito_numerooperacion+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                          '</div>'+
                       '</div>'+
                       '<div class="form-group row">'+
                          '<label class="col-sm-4 col-form-label">Monto {{isset($disabled)?'':'*'}}</label>'+
                          '<div class="col-sm-8">'+
                             '<input type="number" id="deposito_total'+num+'" value="'+mx_monto+'" onclick="totalformapago()" onkeyup="totalformapago()" placeholder="0.00" class="form-control" step="0.01" min="0" style="font-size: 20px;" {{isset($disabled)?'disabled':''}}>'+
                          '</div>'+
                       '</div>'+
                     '</div>'+
                 '</div>'+
                 '<div id="divcheque'+num+'" style="display:none;margin-top: 20px">'+
                    '<div class="form-group row">'+
                      '<label class="col-sm-4 col-form-label">Banco {{isset($disabled)?'':'*'}}</label>'+
                      '<div class="col-sm-8">'+
                        '<select id="cheque_banco'+num+'" class="form-control" style="width:100%;" {{isset($disabled)?'disabled':''}}>'+
                          '<option></option>'+
                          <?php $bancos = DB::table('banco')->get(); ?>
                          @foreach($bancos as $value)
                          '<option value="{{ $value->id }}">{{ $value->nombre }}</option>'+
                          @endforeach
                        '</select>'+
                      '</div>'+
                    '</div>'+
                    '<div id="chequecreditoletra'+num+'" style="display:block;">'+
                      '<div class="form-group row">'+
                        '<label class="col-sm-4 col-form-label">Fecha de Emisión {{isset($disabled)?'':'*'}}</label>'+
                        '<div class="col-sm-8">'+
                           '<input type="date" id="cheque_fechaemision'+num+'" value="'+cheque_emision+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                        '</div>'+
                      '</div>'+
                      '<div class="form-group row">'+
                        '<label class="col-sm-4 col-form-label">Fecha de Vencimiento {{isset($disabled)?'':'*'}}</label>'+
                        '<div class="col-sm-8">'+
                           '<input type="date" id="cheque_fechavencimiento'+num+'" value="'+cheque_vencimiento+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                        '</div>'+
                      '</div>'+
                      '<div class="form-group row">'+
                        '<label class="col-sm-4 col-form-label">Número de Cheque {{isset($disabled)?'':'*'}}</label>'+
                        '<div class="col-sm-8">'+
                           '<input type="number" id="cheque_numero'+num+'" value="'+cheque_numero+'" class="form-control" {{isset($disabled)?'disabled':''}}>'+
                        '</div>'+
                      '</div>'+
                      '<div class="form-group row">'+
                        '<label class="col-sm-4 col-form-label">Monto {{isset($disabled)?'':'*'}}</label>'+
                        '<div class="col-sm-8">'+
                           '<input type="number" id="cheque_total'+num+'" value="'+mx_monto+'" onclick="totalformapago()" onkeyup="totalformapago()" placeholder="0.00"class="form-control" step="0.01" min="0" style="font-size: 20px;" {{isset($disabled)?'disabled':''}}>'+
                        '</div>'+
                      '</div>'+
                     '</div>'+
                 '</div>'+
                 '<div id="divsaldo'+num+'" style="display:none;margin-top: 20px">'+
                       '<div class="form-group row">'+
                         '<label class="col-sm-4 col-form-label">Cliente *</label>'+
                         '<div class="col-sm-8">'+
                           '<select class="form-control" id="saldo_cliente'+num+'" {{isset($disabled)?'disabled':''}}>'+
                               '<option value="'+saldo_cliente+'">'+saldo_clientenombre+'</option>'+
                           '</select>'+
                         '</div>'+
                       '</div>'+
                       '<div class="form-group row">'+
                          '<label class="col-sm-4 col-form-label">Saldo de Cliente</label>'+
                          '<div class="col-sm-8">'+
                             '<input type="text" id="saldo_totalefectivo'+num+'" value="---" class="form-control" disabled>'+
                          '</div>'+
                       '</div>'+
                       '<div class="form-group row">'+
                          '<label class="col-sm-4 col-form-label">Monto {{isset($disabled)?'':'*'}}</label>'+
                          '<div class="col-sm-8">'+
                             '<input type="number" id="saldo_total'+num+'" value="'+mx_monto+'" onclick="totalformapago()" onkeyup="totalformapago()" placeholder="0.00" class="form-control" step="0.01" min="0" style="font-size: 20px;" {{isset($disabled)?'disabled':''}}>'+
                          '</div>'+
                       '</div>'+
                 '</div>'+
              '</td>'+btneliminar+
            '</tr>'
     );
    
     $('#table-tipopago tbody').attr('num',parseInt(num)+1);
  
     $("#idtipopago"+num).select2({
        placeholder: "-- Seleccionar --",
        minimumResultsForSearch: -1
     }).on("change", function(e) {
        scripttipopago(e.currentTarget.value,num);
    }).val(mx_tipopago).trigger('change');
    
    $("#deposito_banco"+num).select2({
        placeholder: "-- Seleccionar --",
        minimumResultsForSearch: -1
    }).on("change", function(e){
        $('#deposito_numerocuenta'+num).val($('#'+e.currentTarget.id+' option:selected').attr('numerocuenta'));
    }).val(deposito_banco).trigger('change');
  
    $("#cheque_banco"+num).select2({
        placeholder: "-- Seleccionar --",
        minimumResultsForSearch: -1
    }).val(cheque_banco).trigger('change');
      
        $.ajax({
            url:"{{url('backoffice/inicio/show-mostrarsaldousuario')}}",
            type:'GET',
            data: {
                idcliente : saldo_cliente
            },
            success: function (respuesta){
                $('#saldo_totalefectivo'+num).val(respuesta["saldototal"]);
            }
        })
  
    $("#saldo_cliente"+num).select2({
        ajax: {
            url:"{{url('backoffice/inicio/show-listarclientes')}}",
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
    }).on("change", function(e){
        $.ajax({
            url:"{{url('backoffice/inicio/show-mostrarsaldousuario')}}",
            type:'GET',
            data: {
                idcliente : $('#saldo_cliente'+num+' option:selected').val()
            },
            success: function (respuesta){
                $('#saldo_totalefectivo'+num).val(respuesta["saldototal"]);
            }
        })
      
    });
      
     totalformapago();
}

function scripttipopago(idtipopago,num){
        if(idtipopago==1) {
            $('#divefectivo'+num).css('display','block');
            $('#divdeposito'+num).css('display','none');
            $('#divcheque'+num).css('display','none');
            $('#divsaldo'+num).css('display','none');
            if($("#idformapago").val()==2){
              $('#divefectivo'+num).css('display','none');
            }else if($("#idformapago").val()==3){
              $('#divefectivo'+num).css('display','none');
            }
        }else if(idtipopago==2) {
            $('#divefectivo'+num).css('display','none');
            $('#divdeposito'+num).css('display','block');
            $('#divcheque'+num).css('display','none');
            $('#divsaldo'+num).css('display','none');
            if($("#idformapago").val()==1){
              $('#divefectivo'+num).css('display','none');
              $("#depositocreditoletra"+num).css('display','block');
              $("#chequecreditoletra"+num).css('display','block');
            }else if($("#idformapago").val()==2){
              $("#depositocreditoletra"+num).css('display','none');
              $("#chequecreditoletra"+num).css('display','none');
            }else if($("#idformapago").val()==3){
              $("#depositocreditoletra"+num).css('display','none');
              $("#chequecreditoletra"+num).css('display','none');
            }
        }else if(idtipopago==3) {
            $('#divefectivo'+num).css('display','none');
            $('#divdeposito'+num).css('display','none');
            $('#divcheque'+num).css('display','block');
            $('#divsaldo'+num).css('display','none');
            if($("#idformapago").val()==1){
              $("#depositocreditoletra"+num).css('display','block');
              $("#chequecreditoletra"+num).css('display','block');
            }else if($("#idformapago").val()==2){
              $("#depositocreditoletra"+num).css('display','none');
              $("#chequecreditoletra"+num).css('display','none');
            }else if($("#idformapago").val()==3){
              $("#depositocreditoletra"+num).css('display','none');
              $("#chequecreditoletra"+num).css('display','none');
            }
        }else if(idtipopago==4) {
            $('#divefectivo'+num).css('display','none');
            $('#divdeposito'+num).css('display','none');
            $('#divcheque'+num).css('display','none');
            $('#divsaldo'+num).css('display','block');
            if($("#idformapago").val()==1){
              $("#depositocreditoletra"+num).css('display','block');
              $("#chequecreditoletra"+num).css('display','block');
            }else if($("#idformapago").val()==2){
              $("#depositocreditoletra"+num).css('display','none');
              $("#chequecreditoletra"+num).css('display','none');
            }else if($("#idformapago").val()==3){
              $("#depositocreditoletra"+num).css('display','none');
              $("#chequecreditoletra"+num).css('display','none');
            }
        }
}
function totalformapago(){
    var total = 0;
    $('#table-tipopago tbody tr').each(function() {
        var num = $(this).attr('num');
        var efectivo_total = $('#efectivo_total'+num).val();
        var deposito_total = $('#deposito_total'+num).val();
        var cheque_total = $('#cheque_total'+num).val();
        var saldo_total = $('#saldo_total'+num).val();
        if(efectivo_total==''){ efectivo_total = 0; }
        if(deposito_total==''){ deposito_total = 0; }
        if(cheque_total==''){ cheque_total = 0; }
        if(saldo_total==''){ saldo_total = 0; }
        if($('#idtipopago'+num).val()==1){
            total = total+parseFloat(efectivo_total);
        }else if($('#idtipopago'+num).val()==2){
            total = total+parseFloat(deposito_total);
        }else if($('#idtipopago'+num).val()==3){
            total = total+parseFloat(cheque_total);
        }else if($('#idtipopago'+num).val()==4){
            total = total+parseFloat(saldo_total);
        }
    });
    $('#totalformapago').html(total.toFixed(2));
}
function efectivocredito(num){
    if($('idtipopago'+num).val()==1){
      $('#divefectivo'+num).css('display','none');
    }  
}

function eliminarservicio(num) {
  $('#table-tipopago tbody tr#'+num).remove();
}
  
function seleccionartipopago(){
    var servicios = '';
    $('#table-tipopago tbody tr').each(function() {
        var num = $(this).attr('num');
        servicios = servicios+'/&/'+
            'efectivo_total'+num+'/-/'+$('#efectivo_total'+num).val()+'/,/'+
            'deposito_banco'+num+'/-/'+$('#deposito_banco'+num).val()+'/,/'+
            'deposito_numerocuenta'+num+'/-/'+$('#deposito_numerocuenta'+num).val()+'/,/'+
            'deposito_fechadeposito'+num+'/-/'+$('#deposito_fechadeposito'+num).val()+'/,/'+
            'deposito_numerooperacion'+num+'/-/'+$('#deposito_numerooperacion'+num).val()+'/,/'+
            'deposito_total'+num+'/-/'+$('#deposito_total'+num).val()+'/,/'+
            'cheque_banco'+num+'/-/'+$('#cheque_banco'+num).val()+'/,/'+
            'cheque_fechaemision'+num+'/-/'+$('#cheque_fechaemision'+num).val()+'/,/'+
            'cheque_fechavencimiento'+num+'/-/'+$('#cheque_fechavencimiento'+num).val()+'/,/'+
            'cheque_numero'+num+'/-/'+$('#cheque_numero'+num).val()+'/,/'+
            'cheque_total'+num+'/-/'+$('#cheque_total'+num).val()+'/,/'+
            'idtipopago'+num+'/-/'+$('#idtipopago'+num).val()+'/,/'+
            'deposito_horadeposito'+num+'/-/'+$('#deposito_horadeposito'+num).val()+'/,/'+
            'saldo_cliente'+num+'/-/'+$('#saldo_cliente'+num).val()+'/,/'+
            'saldo_total'+num+'/-/'+$('#saldo_total'+num).val();
    });
    return servicios;
}
</script>