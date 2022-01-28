@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte Cajas</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportecaja') }}" method="GET" autocomplete="off"> 
          <div class="custom-form">
            <div class="row">
             <div class="col-sm-6">
                  <label>Tienda</label>
                  <select name="tienda" id="tienda" disabled>
                      <option></option>
                      @foreach($tiendas as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
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
           @include('layouts.backoffice.reportecaja.tabla')
           {{ $caja->links('app.tablepagination', ['results' => $caja]) }}
        </div>
    </div>
</div>

@endsection
@section('subscripts')
<script>
  function reporte(tipo){
      window.location.href = '{{url('backoffice/reportecaja')}}?'+
        'tipo='+tipo;
  }
  
  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");
</script>
@endsection
