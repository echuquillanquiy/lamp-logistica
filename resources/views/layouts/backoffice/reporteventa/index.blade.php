@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reporteventa') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Reporte de Ventas</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reporteventa') }}" method="GET"> 
          <div class="custom-form">
            <div class="row">
               <div class="col-md-6">
                  <label>Tienda</label>
                  <select name="tienda" id="tienda" disabled>
                      <option></option>
                      @foreach($tiendas as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Código</label>
                  <input class="form-control" type="text"  name="codigo" id="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
                  <label>Vendedor</label>
                  <select name="idusuariovendedor" id="idusuariovendedor">
                      @if(isset($_GET['idusuariovendedor']))
                          <?php $idusuariovendedor = DB::table('users')->whereId($_GET['idusuariovendedor'])->first(); ?>
                          @if($idusuariovendedor!='')
                          <option value="{{$idusuariovendedor->id}}">{{$idusuariovendedor->identificacion}} - {{$idusuariovendedor->apellidos}}, {{$idusuariovendedor->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                  <label>Cliente</label>
                  <select name="nombreCliente" id="nombreCliente">
                      @if(isset($_GET['nombreCliente']))
                          <?php $nombreCliente = DB::table('users')->whereId($_GET['nombreCliente'])->first(); ?>
                          @if($nombreCliente!='')
                          <option value="{{$nombreCliente->id}}">{{$nombreCliente->identificacion}} - {{$nombreCliente->apellidos}}, {{$nombreCliente->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                  <label>Cajero</label>
                  <select name="nombreCajero" id="nombreCajero">
                      @if(isset($_GET['nombreCajero']))
                          <?php $nombreCajero = DB::table('users')->whereId($_GET['nombreCajero'])->first(); ?>
                          @if($nombreCajero!='')
                          <option value="{{$nombreCajero->id}}">{{$nombreCajero->identificacion}} - {{$nombreCajero->apellidos}}, {{$nombreCajero->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
               </div>
               <div class="col-md-6">
                 <label>Forma de Pago</label>
                  <select name="idformapago" id="idformapago">
                      <option></option>
                      @foreach($formapago as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
                 <label>Estado</label>
                  <select id="idestado" name="idestado">
                      <option></option>
                      <option value="2">Pendiente</option>
                      <option value="3">Confirmado</option>
                  </select>
                  <label>Fecha de Inicio</label>
                  <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  <label>Fecha de Fin</label>
                  <input class="form-control" type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
               </div>
               <div class="col-md-12">
                  <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                  <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
               </div>
             </div>
          </div>
        </form>
      <div class="table-responsive">
         @include('layouts.backoffice.reporteventa.tabla')
         {{ $venta->links('app.tablepagination', ['results' => $venta]) }}
    </div>
</dvi>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reporteventa')}}?'+
      'tipo='+tipo+
      '&codigo='+$('#codigo').val()+
      '&idusuariovendedor='+($('#idusuariovendedor').val()!=null?$('#idusuariovendedor').val():'')+
      '&nombreCliente='+($('#nombreCliente').val()!=null?$('#nombreCliente').val():'')+
      '&nombreCajero='+($('#nombreCajero').val()!=null?$('#nombreCajero').val():'')+
      '&idformapago='+($('#idformapago').val()!=null?$('#idformapago').val():'')+
      '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");

$('#idusuariovendedor').select2({
  ajax: {
        url:"{{url('backoffice/reporteventa/show-listarcliente')}}",
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
    minimumInputLength: 2,
    allowClear: true
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/reporteventa/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});

$('#nombreCliente').select2({
  ajax: {
        url:"{{url('backoffice/reporteventa/show-listarcliente')}}",
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
    minimumInputLength: 2,
    allowClear: true
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/reporteventa/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});

$('#nombreCajero').select2({
  ajax: {
        url:"{{url('backoffice/reporteventa/show-listarcliente')}}",
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
    minimumInputLength: 2,
    allowClear: true
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/reporteventa/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});

  $("#idformapago").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{isset($_GET['idformapago'])?($_GET['idformapago']!=''?$_GET['idformapago']:'0'):'0'}}).trigger("change");

  $("#idestado").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
</script>
@endsection