@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte de Ventas por Producto</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reporteventaproducto') }}" method="GET" autocomplete="off"> 
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
                  <label>Vendedor</label>
                  <select name="vendedor" id="vendedor" >
                      @if(isset($_GET['vendedor']))
                          <?php $vendedor = DB::table('users')->whereId($_GET['vendedor'])->first(); ?>
                          @if($vendedor!='')
                          <option value="{{$vendedor->id}}">{{$vendedor->identificacion}} - {{$vendedor->apellidos}}, {{$vendedor->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                  <label>Cajero</label>
                  <select name="cajero" id="cajero" >
                      @if(isset($_GET['cajero']))
                          <?php $cajero = DB::table('users')->whereId($_GET['cajero'])->first(); ?>
                          @if($cajero!='')
                          <option value="{{$cajero->id}}">{{$cajero->identificacion}} - {{$cajero->apellidos}}, {{$cajero->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                  <label>Cliente</label>
                  <select name="cliente" id="cliente">
                      @if(isset($_GET['cliente']))
                          <?php $cliente = DB::table('users')->whereId($_GET['cliente'])->first(); ?>
                          @if($cliente!='')
                          <option value="{{$cliente->id}}">{{$cliente->identificacion}} - {{$cliente->apellidos}}, {{$cliente->nombre}}</option>
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
                  <select name="formapago" id="formapago" >
                      <option></option>
                      @foreach($formapago as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Moneda</label>
                  <select name="moneda" id="moneda" >
                      <option></option>
                      @foreach($moneda as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
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
           @include('layouts.backoffice.reporteventaproducto.tabla')
           {{ $ventaproducto->links('app.tablepagination', ['results' => $ventaproducto]) }}
        </div>
    </div>

</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reporteventaproducto')}}?'+
      'tipo='+tipo+
      '&vendedor='+($('#vendedor').val()!=null?$('#vendedor').val():'')+
      '&cajero='+($('#cajero').val()!=null?$('#cajero').val():'')+
      '&cliente='+($('#cliente').val()!=null?$('#cliente').val():'')+
      '&formapago='+($('#formapago').val()!=null?$('#formapago').val():'')+
      '&moneda='+($('#moneda').val()!=null?$('#moneda').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

$("#tienda").select2({
    placeholder: "--  Seleccionar --",
    allowClear: true
}).val({{usersmaster()->idtienda}}).trigger("change");
  
$("#formapago").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['formapago'])?($_GET['formapago']!=''?$_GET['formapago']:'0'):'0'}}).trigger("change");
  
$("#moneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['moneda'])?($_GET['moneda']!=''?$_GET['moneda']:'0'):'0'}}).trigger("change");
  
$('#vendedor').select2({
  ajax: {
        url:"{{url('backoffice/reporteventaproducto/show-listarcliente')}}",
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
        url:"{{url('backoffice/reporteventaproducto/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});
  
$('#cajero').select2({
  ajax: {
        url:"{{url('backoffice/reporteventaproducto/show-listarcliente')}}",
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
        url:"{{url('backoffice/reporteventaproducto/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});
  
$('#cliente').select2({
  ajax: {
        url:"{{url('backoffice/reporteventaproducto/show-listarcliente')}}",
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
        url:"{{url('backoffice/reporteventaproducto/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});
</script>
@endsection