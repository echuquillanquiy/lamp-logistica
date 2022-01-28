@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
  <div class="panel-heading ui-sortable-handle">
      <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reporteaperturacierre') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
      </div>
      <h4 class="panel-title">Reporte de Aperturas y Cierres</h4>
  </div>
  <div class="panel-body">
    <form action="{{ url('backoffice/reporteaperturacierre') }}"  id="sendForm"
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
                <label>Persona responsable</label>
                <select name="responsable" id="responsable">
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
                <label>Persona de recepción</label>
                <select name="recepcion" id="recepcion">
                      @if(isset($_GET['recepcion']))
                          <?php $recepcion = DB::table('users')->whereId($_GET['recepcion'])->first(); ?>
                          @if($recepcion!='')
                          <option value="{{$recepcion->id}}">{{$recepcion->identificacion}} - {{$recepcion->apellidos}}, {{$recepcion->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                </select>
             </div>
             <div class="col-md-6">
               <label>Estado</label>
                <select name="estado" id="estado">
                    <option value="1">Apertura En Proceso</option>
                    <option value="2">Apertura Pendiente</option>
                    <option value="3">Aperturado</option>
                    <option value="4">Cierre Pendiente</option>
                    <option value="5">Caja Cerrada</option>
                </select>
                <label>Fecha inicio</label>
                <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                <label>Fecha fin</label>
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
         @include('layouts.backoffice.reporteaperturacierre.tabla')
         {{ $aperturacierre->links('app.tablepagination', ['results' => $aperturacierre]) }}      
    </div>
  </div>
</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reporteaperturacierre')}}?'+
      'tipo='+tipo+
      '&responsable='+($('#responsable').val()!=null?$('#responsable').val():'')+
      '&recepcion='+($('#recepcion').val()!=null?$('#recepcion').val():'')+
      '&estado='+($('#estado').val()!=null?$('#estado').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");

$('#responsable').select2({
  ajax: {
        url:"{{url('backoffice/reporteaperturacierre/show-listarcliente')}}",
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
        url:"{{url('backoffice/reporteaperturacierre/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});

$('#recepcion').select2({
  ajax: {
        url:"{{url('backoffice/reporteaperturacierre/show-listarcliente')}}",
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
        url:"{{url('backoffice/reporteaperturacierre/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});

  $("#estado").select2({
      placeholder: "-- Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{isset($_GET['estado'])?($_GET['estado']!=''?$_GET['estado']:'0'):'0'}}).trigger("change");
</script>
@endsection