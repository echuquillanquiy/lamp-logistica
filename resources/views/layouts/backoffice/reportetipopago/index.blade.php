@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
  
  <div class="panel-heading ui-sortable-handle">
    <h4 class="panel-title">Reporte por Tipo de Pago</h4>
  </div>
  
  <div class="panel-body">
    
    <form action="{{ url('backoffice/reportetipotipopago') }}" method="GET" autocomplete="off"> 
      <div class="custom-form">
        <div class="row">
          <div class="col-md-6">
            
            <label>Tienda</label>
            <select name="tienda" id="tienda" disabled>
              <option></option>
              @foreach($tienda as $value)
              <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
              @endforeach
            </select>
            <label>Tipo de Pago</label>
            <select name="tipopago" id="tipopago">
              <option></option>
              @foreach($tipopago as $value)
              <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
              @endforeach
            </select>
            
          </div>
          <div class="col-md-6">
            
            <label>Módulo</label>
            <select name="modulo" id="modulo" class="form-control">
              <option></option>
              <option value="1">VENTA</option>
              <option value="2">NOTA DEVOLUCIÓN</option>
            </select>
            
          </div>          
          <div class="col-md-12">
            <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
          </div>
        </div>
      </div>
    </form>
    
    <div class="table-responsive">
      @include('layouts.backoffice.reportetipopago.tabla')
      {{ $tipopagodetalle->links('app.tablepagination', ['results' => $tipopagodetalle]) }}
    </div>
    
  </div>
</div>
@endsection

@section('subscripts')
<script>
    
  function reporte(tipo){
    window.location.href = '{{url('backoffice/reportetipopago')}}?'+
      'tipo='+tipo+
      '&modulo='+($('#modulo').val()!=null?$('#modulo').val():'')+
      '&tipopago='+($('#tipopago').val()!=null?$('#tipopago').val():'');
  }
  
  $('#tienda').select2({
    placeholder: '---Seleccionar---',
    minimumResultsForSearch: -1,
    allowClear: true
  }).val({{ usersmaster()->idtienda }}).trigger("change");
  
  $('#tipopago').select2({
    placeholder: '---Seleccionar---',
    minimumResultsForSearch: -1,
    allowClear: true
  }).val( "{{ isset($_GET['tipopago']) ? ($_GET['tipopago'] != '' ? $_GET['tipopago'] : '') : '' }}" ).trigger("change");
  
  $('#modulo').select2({
    placeholder: '---Seleccionar---',
    minimumResultsForSearch: -1,
    allowClear: true
  }).val( "{{ isset($_GET['modulo']) ? ($_GET['modulo'] != '' ? $_GET['modulo'] : '') : '' }}" ).trigger("change");
  
</script>
@endsection