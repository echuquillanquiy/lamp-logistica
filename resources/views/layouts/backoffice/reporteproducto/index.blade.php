@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte de Productos</h4>
    </div>
    <div class="panel-body">
          <div class="custom-form">
            <div class="row">
               <div class="col-md-6">
                  <label>Tienda</label>
                  <select name="tienda" id="tienda">
                      <option></option>
                      @foreach($tiendas as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
                  <label>Código de Venta</label>
                  <input class="form-control" type="text"  name="codigo" id="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
                  <label>Nombre </label>
                  <input class="form-control" type="text"  name="nombre" id="nombre" value="{{isset($_GET['nombre'])?($_GET['nombre']!=''?$_GET['nombre']:''):''}}">
               </div>
               <div class="col-md-6">
                  <label>Marca</label>
                  <input class="form-control" type="text"  name="marca" id="marca" value="{{isset($_GET['marca'])?($_GET['marca']!=''?$_GET['marca']:''):''}}">
                 <label>Categoria</label>
                  <input class="form-control" type="text"  name="categoria" id="categoria" value="{{isset($_GET['categoria'])?($_GET['categoria']!=''?$_GET['categoria']:''):''}}">
                 <label>Talla</label>
                  <input class="form-control" type="text"  name="talla" id="talla" value="{{isset($_GET['talla'])?($_GET['talla']!=''?$_GET['talla']:''):''}}">
               </div>
               <div class="col-md-12">
                 <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                 <a href="javascript:;" class="btn btn-primary"   onclick="modal({route:'reporteproducto/create'})" style="margin-bottom:10px;"><i class="fa fa-download"></i> Exportar Excel</a>
             <!--    <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>-->
               </div>
             </div>
          </div>
        <div class="table-responsive">
         @include('layouts.backoffice.reporteproducto.tabla')
         {{ $producto->links('app.tablepagination', ['results' => $producto]) }}
        </div>
    </div>
</div>

@endsection

@section('subscripts')
<style>
.pagination {
    margin-top: 5px;
}
</style>
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reporteproducto')}}?'+
      'tipo='+tipo+
      '&tienda='+($('#tienda').val()!=null?$('#tienda').val():'')+
      '&codigo='+$('#codigo').val()+
      '&nombre='+$('#nombre').val()+
      '&categoria='+($('#categoria').val()!=null?$('#categoria').val():'')+
      '&talla='+($('#talla').val()!=null?$('#talla').val():'')+
      '&marca='+($('#marca').val()!=null?$('#marca').val():'');
}
 
  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{ isset($_GET['tienda']) ? (($_GET['tienda']) !='' ? $_GET['tienda'] :'0' ):'0'}}).trigger("change");
  
  $("#codFabricante").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['codFabricante'])?($_GET['codFabricante']!=''?$_GET['codFabricante']:'0'):'0'}}).trigger("change");
  
  $("#motor").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['motor'])?($_GET['motor']!=''?$_GET['motor']:'0'):'0'}}).trigger("change");
  
  $("#serie").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['serie'])?($_GET['serie']!=''?$_GET['serie']:'0'):'0'}}).trigger("change");
  
  $("#modelo").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['modelo'])?($_GET['modelo']!=''?$_GET['modelo']:'0'):'0'}}).trigger("change");
  
  $("#marca").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['marca'])?($_GET['marca']!=''?$_GET['marca']:'0'):'0'}}).trigger("change");
  

</script>
@endsection
