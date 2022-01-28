@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte Guías de Remisión</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportefacturacionguiaremision') }}" method="GET" autocomplete="off"> 
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
                  <label>Cliente Destinatario</label>
                  <select name="destinatario" id="destinatario" >
                      <option></option>
                  </select>
                  <label>Serie</label>
                  <input class="form-control" type="text"  name="serie" id="serie" value="{{isset($_GET['serie'])?($_GET['serie']!=''?$_GET['serie']:''):''}}">
               </div>
               <div class="col-md-6">
                  <label>Correlativo</label>
                  <input class="form-control" type="text"  name="correlativo" id="correlativo" value="{{isset($_GET['correlativo'])?($_GET['correlativo']!=''?$_GET['correlativo']:''):''}}">
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
           @include('layouts.backoffice.reportefacturacionguiaremision.tabla')
           {{ $facturacionguiaremision->links('app.tablepagination', ['results' => $facturacionguiaremision]) }}
        </div>
    </div>

</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reportefacturacionguiaremision')}}?'+
      'tipo='+tipo+
      '&destinatario='+($('#destinatario').val()!=null?$('#destinatario').val():'')+
      '&serie='+$('#serie').val()+
      '&correlativo='+$('#correlativo').val()+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

$("#tienda").select2({
    placeholder: "--  Seleccionar --",
    allowClear: true
}).val({{usersmaster()->idtienda}}).trigger("change");

$('#destinatario').select2({
  ajax: {
        url:"{{url('backoffice/reportefacturacionguiaremision/show-listarcliente')}}",
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
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/reportefacturacionguiaremision/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
}).val({{isset($_GET['destinatario'])?($_GET['destinatario']!=''?$_GET['destinatario']:'0'):'0'}}).trigger("change");
</script>
@endsection