@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'pagoletra/create'})"><i class="fa fa-angle-right"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Reporte Pago de Letras</h4>
    </div>
    <div class="panel-body">
        <form action="{{ url('backoffice/reportepagoletra') }}" 
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
                 
                  <label>Responsable</label>
                  <select name="idusuarioresponsable" id="idusuarioresponsable">
                      @if(isset($_GET['idusuarioresponsable']))
                          <?php $idusuarioresponsable = DB::table('users')->whereId($_GET['idusuarioresponsable'])->first(); ?>
                          @if($idusuarioresponsable!='')
                          <option value="{{$idusuarioresponsable->id}}">{{$idusuarioresponsable->identificacion}} - {{$idusuarioresponsable->apellidos}}, {{$idusuarioresponsable->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                 
                  <label>Cod. Pago de Letra</label>
                  <input class="form-control" type="text" name="codigoletra" 
                         id="codigoletra" value="{{isset($_GET['codigoletra'])?($_GET['codigoletra']!=''?$_GET['codigoletra']:''):''}}"
                         placeholder="Código Pago de Letra">
               </div>
               <div class="col-md-6">                  
                  <label>Venta</label>
                  <input class="form-control" type="text" name="venta" 
                         id="venta" value="{{isset($_GET['venta'])?($_GET['venta']!=''?$_GET['venta']:''):''}}"
                         placeholder="Código de Venta">
                 
                  <label>Fecha de Inicio</label>
                  <input class="form-control" type="date" name="fechainicio" 
                         id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                 
                  <label>Fecha de Fin</label>
                  <input class="form-control" type="date" name="fechafin" 
                         id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
               </div>
               <div class="col-md-12">
                  <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                  <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
               </div>
             </div>
          </div>
      </form>
        <div class="table-responsive">
         @include('layouts.backoffice.reportepagoletra.tabla')
         {{ $pagoletra->links('app.tablepagination', ['results' => $pagoletra]) }}
        </div>
</div>
</div>
@endsection

@section('subscripts')
<script>
    function reporte(tipo){
        window.location.href = '{{url('backoffice/reportepagoletra')}}?'+
          'tipo='+tipo+
          '&idusuarioresponsable='+($('#idusuarioresponsable').val()!=null?$('#idusuarioresponsable').val():'')+
          '&codigoletra='+$('#codigoletra').val()+
          '&venta='+$('#venta').val()+
          '&fechainicio='+$('#fechainicio').val()+
          '&fechafin='+$('#fechafin').val();
    }

      $("#tienda").select2({
          placeholder: "--  Seleccionar --",
          allowClear: true
      }).val({{usersmaster()->idtienda}}).trigger("change");

    $('#idusuarioresponsable').select2({
        ajax: {
              url:"{{url('backoffice/reportepagoletra/show-listarcliente')}}",
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
            url:"{{url('backoffice/reportepagoletra/show-seleccionarcliente')}}",
            type:'GET',
            data: {
                idcliente : e.currentTarget.value
           }
         })
    }); 
  
</script>
@endsection
