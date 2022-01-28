@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reportedevolucionnota') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Reporte de Notas de Devolución</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportedevolucionnota') }}" method="GET"> 
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
                  <input class="form-control" type="text" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}"/>
                  <label>Responsable</label>
                  <select id="idresponsable" name="idresponsable">
                      @if(isset($_GET['idresponsable']))
                        @if($_GET['idresponsable']!='')
                        <?php $users = DB::table('users')->where('id',$_GET['idresponsable'])->first();?>
                        <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
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
                  <select id="idestado" name="idestado">
                      <option></option>
                      <option value="1">Pendiente</option>
                      <option value="2">Confirmado</option>
                  </select>
                  <label>Fecha Inicio</label>
                  <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  <label>Fecha Fin</label>
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
         @include('layouts.backoffice.reportedevolucionnota.tabla')
         {{ $devolucionnota->links('app.tablepagination', ['results' => $devolucionnota]) }}
      </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reportedevolucionnota')}}?'+
      'tipo='+tipo+
      '&codigo='+$('#codigo').val()+
      '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
      '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");
  
  $("#idresponsable").select2({
      ajax: {
          url:"{{url('backoffice/reportedevolucionnota/showlistarusuario')}}",
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
  });

  $("#idestado").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
</script>
@endsection