@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
  
  <div class="panel-heading ui-sortable-handle">
    <h4 class="panel-title">Reporte de Comunicación de Baja </h4>
  </div>
  
  <div class="panel-body">
    
    <form action="{{ url('backoffice/reportefacturacioncomunicacion') }}" method="GET" autocomplete="off"> 
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
            <label>Comprobante</label>
            <select name="comprobante" id="comprobante">
              <option></option>
              <option value="01">FACTURA</option>
              <option value="03">BOLETA</option>
              <option value="07">NOTA DE CREDITO</option>
            </select>
            
          </div>
          <div class="col-md-6">
            
            <label>Fecha de Inicio</label>
            <input class="form-control" type="date" name="fechainicio" id="fechainicio" 
                   value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
            <label>Fecha de Fin</label>
            <input class="form-control" type="date" name="fechafin" id="fechafin" 
                   value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">            
            
          </div>          
          <div class="col-md-12">
            <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
          </div>
        </div>
      </div>
    </form>
    
    <div class="table-responsive">            
      @include('layouts.backoffice.reportefacturacioncomunicacion.tabla')
      {{ $comunicacionbaja->links('app.tablepagination', ['results' => $comunicacionbaja]) }}
    </div>
    
  </div>
</div>
@endsection

@section('subscripts')
<script>
    
  function reporte(tipo){
    window.location.href = '{{url('backoffice/reportefacturacioncomunicacion')}}?'+
      'tipo='+tipo+
      '&comprobante='+($('#comprobante').val()!=null?$('#comprobante').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
  }
  
  $('#tienda').select2({
    placeholder: '---Seleccionar---',
    minimumResultsForSearch: -1,
    allowClear: true
  }).val({{ usersmaster()->idtienda }}).trigger("change");
  
  $('#comprobante').select2({
    placeholder: '---Seleccionar---',
    minimumResultsForSearch: -1,
    allowClear: true
  }).val('{{isset($_GET['comprobante'])?($_GET['comprobante']!=''?$_GET['comprobante']:'0'):'0'}}').trigger("change");
//   }).val('{{isset($_GET['tipoCompbrobante'])?($_GET['tipoCompbrobante']!=''?$_GET['tipoCompbrobante']:'0'):'0'}}').trigger("change");
</script>
@endsection