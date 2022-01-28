@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
   <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
          <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reportemovimientoproducto') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Reporte Movimiento de Productos</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportemovimientoproducto') }}" autocomplete="off"
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
                  <label>Código de Movimiento</label>
                  <input class="form-control" type="number" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
                  <label>Estado Movimiento</label>
                  <select name="idestadomovimiento" id="idestadomovimiento">
                    <option value="1">Ingreso</option>
                    <option value="2">Salída</option>
                 </select>
               </div>
               <div class="col-md-6">
                  <label>Usuarios</label>
                  <select id="idusuario" name="idusuario">
                      @if(isset($_GET['idusuario']))
                          <?php $idusuario = DB::table('users')->whereId($_GET['idusuario'])->first(); ?>
                          @if($idusuario!='')
                          <option value="{{$idusuario->id}}">{{$idusuario->identificacion}} - {{$idusuario->apellidos}}, {{$idusuario->nombre}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
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
         @include('layouts.backoffice.reportemovimientoproducto.tabla')
         {{ $movimientoproducto->links('app.tablepagination', ['results' => $movimientoproducto]) }}
     </div>
    </div>
</div>
@endsection

@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reportemovimientoproducto')}}?'+
      'tipo='+tipo+
      '&codigo='+$('#codigo').val()+
      '&idestadomovimiento='+($('#idestadomovimiento').val()!=null?$('#idestadomovimiento').val():'')+
      '&idusuario='+($('#idusuario').val()!=null?$('#idusuario').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
}

  $("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");
  
  $("#idestadomovimiento").select2({
      placeholder: "--  Seleccionar --",
      /*minimumResultsForSearch: -1,*/
      allowClear: true
  }).val({{isset($_GET['idestadomovimiento'])?($_GET['idestadomovimiento']!=''?$_GET['idestadomovimiento']:'0'):'0'}}).trigger("change");

$('#idusuario').select2({
  ajax: {
        url:"{{url('backoffice/reportemovimientoproducto/show-listarcliente')}}",
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
        url:"{{url('backoffice/reportemovimientoproducto/show-seleccionarcliente')}}",
        type:'GET',
        data: {
            idcliente : e.currentTarget.value
       }
     })
});
</script>
@endsection