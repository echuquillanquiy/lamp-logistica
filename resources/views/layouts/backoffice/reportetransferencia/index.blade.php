@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php
$get_idtiendaorigen = isset($_GET['idtiendaorigen']) ? $_GET['idtiendaorigen'] : 1;
$get_idusersorigen = isset($_GET['idusersorigen']) ? $_GET['idusersorigen'] : '';

?>
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reportetransferencia') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Reporte Transferencia de Productos</h4>
    </div>
    <div class="panel-body">
        <form action="{{ url('backoffice/reportetransferencia') }}" autocomplete="off"
            method="GET"> 
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
                  <label>Código de Transferencia</label>
                  <input class="form-control" type="number" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
                  <label>Estado</label>
                  <select id="idestado" name="idestado">
                      <option></option>
                      <option value="1">Solicitud </option>
                      <option value="2">Envio</option>
                      <option value="3">Recepcionado</option>
                  </select>
               </div>
               <div class="col-md-6">
                  <label>Motivo</label>
                  <input class="form-control" type="text" id="motivo" name="motivo" value="{{isset($_GET['motivo'])?($_GET['motivo']!=''?$_GET['motivo']:''):''}}">
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
         @include('layouts.backoffice.reportetransferencia.tabla')
         {{ $productotransferencia->links('app.tablepagination', ['results' => $productotransferencia]) }}
      </div>
      
    </div>
</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reportetransferencia')}}?'+
      'tipo='+tipo+
      '&codigo='+$('#codigo').val()+
      '&motivo='+$('#motivo').val()+
      '&idtiendadestino='+($('#idtiendadestino').val()!=null?$('#idtiendadestino').val():'')+
      '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");
  
  $("#idtiendaorigen").select2({
      placeholder: "--  Seleccionar --",
      /*minimumResultsForSearch: -1,*/
      allowClear: true
  }).val({{isset($_GET['idtiendaorigen'])?($_GET['idtiendaorigen']!=''?$_GET['idtiendaorigen']:'0'):'0'}}).trigger("change");

  $("#idtiendadestino").select2({
      placeholder: "--  Seleccionar --",
      /*minimumResultsForSearch: -1,*/
      allowClear: true
  }).val({{isset($_GET['idtiendadestino'])?($_GET['idtiendadestino']!=''?$_GET['idtiendadestino']:'0'):'0'}}).trigger("change");

  $("#idestado").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
  

</script>
@endsection