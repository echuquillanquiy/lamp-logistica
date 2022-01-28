@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte Cobranza de Letras</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportecobranzaletra') }}" method="GET" autocomplete="off"> 
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
                 
                  <label>Cod. Cobranza de Letra</label>
                  <input class="form-control" type="text"  name="codigoletra" 
                         id="codigoletra" value="{{isset($_GET['codigoletra'])?($_GET['codigoletra']!=''?$_GET['codigoletra']:''):''}}"
                         placeholder="Código Cobranza de Letra">
               </div>
               <div class="col-md-6">
                  <label>Código de Venta</label>
                  <input class="form-control" type="text"  name="ventacodigo" 
                         id="ventacodigo" value="{{isset($_GET['ventacodigo'])?($_GET['ventacodigo']!=''?$_GET['ventacodigo']:''):''}}"
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
           @include('layouts.backoffice.reportecobranzaletra.tabla')
           {{ $cobranzaletra->links('app.tablepagination', ['results' => $cobranzaletra]) }}
        </div>
</div>
</div>
@endsection
@section('subscripts')
<script>
    function reporte(tipo){
        window.location.href = '{{url('backoffice/reportecobranzaletra')}}?'+
          'tipo='+tipo+
          '&idusuario='+($('#idusuario').val()!=null?$('#idusuario').val():'')+
          '&codigoletra='+$('#codigoletra').val()+
          '&centacodigo='+$('#centacodigo').val()+
          '&fechainicio='+$('#fechainicio').val()+
          '&fechafin='+$('#fechafin').val();
    }

    $("#tienda").select2({
        placeholder: "--  Seleccionar --",
        allowClear: true
    }).val({{usersmaster()->idtienda}}).trigger("change");

    $('#idusuario').select2({
      ajax: {
            url:"{{url('backoffice/reportecobranzaletra/show-listarcliente')}}",
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
            url:"{{url('backoffice/reportecobranzaletra/show-seleccionarcliente')}}",
            type:'GET',
            data: {
                idcliente : e.currentTarget.value
           }
         })
    });
  
</script>
@endsection
