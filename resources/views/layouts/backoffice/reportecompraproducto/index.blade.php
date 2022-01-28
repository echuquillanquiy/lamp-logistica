@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte de Compras por Productos</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportecompraproducto') }}" method="GET" autocomplete="off"> 
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
                  <label>Cajero</label>
                  <select name="responsable" id="responsable" >
                      @if(isset($_GET['responsable']))
                          <?php $responsable = DB::table('users')->whereId($_GET['responsable'])->first(); ?>
                          @if($responsable!='')
                          <option value="{{$responsable->id}}">{{$responsable->identificacion}} - {{$responsable->apellidos}}, {{$responsable->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                  <label>Proveedor</label>
                  <select name="proveedor" id="proveedor" >
                      @if(isset($_GET['proveedor']))
                          <?php $proveedor = DB::table('users')->whereId($_GET['proveedor'])->first(); ?>
                          @if($proveedor!='')
                          <option value="{{$proveedor->id}}">{{$proveedor->identificacion}} - {{$proveedor->apellidos}}, {{$proveedor->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                  <label>Forma de Pago</label>
                  <select name="formapago" id="formapago" >
                      <option></option>
                      @foreach($formapago as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
               </div>
               <div class="col-md-6">
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
           @include('layouts.backoffice.reportecompraproducto.tabla')
           {{ $compraproducto->links('app.tablepagination', ['results' => $compraproducto]) }}
        </div>
    </div>

</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reportecompraproducto')}}?'+
      'tipo='+tipo+
      '&responsable='+($('#responsable').val()!=null?$('#responsable').val():'')+
      '&proveedor='+($('#proveedor').val()!=null?$('#proveedor').val():'')+
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
  
$('#responsable').select2({
  ajax: {
        url:"{{url('backoffice/reportecompraproducto/show-listarcliente')}}",
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
        url:"{{url('backoffice/reportecompraproducto/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});
  
$('#proveedor').select2({
  ajax: {
        url:"{{url('backoffice/reportecompraproducto/show-listarcliente')}}",
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
        url:"{{url('backoffice/reportecompraproducto/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});
</script>
@endsection