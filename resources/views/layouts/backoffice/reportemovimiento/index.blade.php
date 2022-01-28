@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reportemovimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Reporte de Movimientos</h4>
    </div>
    <div class="panel-body">
        <form action="{{ url('backoffice/reportemovimiento') }}" method="GET"> 
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
                      
                        <label>Código Movimiento</label>
                        <input class="form-control" type="text" name="codigo" id="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
                        
                        <label>Tipo</label>
                        <select name="tipomovimiento" id="tipomovimiento">
                            <option></option>
                            @foreach($tipomovimiento as $value)
                            <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                            @endforeach
                        </select>
                      
                        <label>Concepto</label>
                        <input class="form-control" type="text" name="concepto" id="concepto" value="{{isset($_GET['concepto'])?($_GET['concepto']!=''?$_GET['concepto']:''):''}}">
                    </div>
                    <div class="col-md-6">
                        <label>Responsable</label>
                        <select name="idusuarioresponsable" id="idusuarioresponsable">
                            <option></option>
                            @foreach($usuarios as $value)
                            <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                            @endforeach
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
            @include('layouts.backoffice.reportemovimiento.tabla')
            {{ $movimiento->links('app.tablepagination', ['results' => $movimiento]) }}
        </div>
    </div>
</div>
@endsection

@section('subscripts')
<script>
  
    function reporte(tipo){
        window.location.href = '{{url('backoffice/reportemovimiento')}}?'+
                                'tipo='+tipo+
                                '&codigo='+$('#codigo').val()+
                                '&tipomovimiento='+($('#tipomovimiento').val()!=null?$('#tipomovimiento').val():'')+
                                '&concepto='+$('#concepto').val()+
                                '&idtipopago='+($('#idtipopago').val()!=null?$('#idtipopago').val():'')+
                                '&idusuarioresponsable='+($('#idusuarioresponsable').val()!=null?$('#idusuarioresponsable').val():'')+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }

    $("#tienda").select2({
        placeholder: "--  Seleccionar --",
        allowClear: true
    }).val({{usersmaster()->idtienda}}).trigger("change");

    $("#tipomovimiento").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1,
        allowClear: true
    }).val({{isset($_GET['tipomovimiento'])?($_GET['tipomovimiento']!=''?$_GET['tipomovimiento']:'0'):'0'}}).trigger("change");

    $("#idusuarioresponsable").select2({
        placeholder: "--  Seleccionar --",
        minimumInputLength: -1,
        allowClear: true
    }).val({{isset($_GET['idusuarioresponsable'])?($_GET['idusuarioresponsable']!=''?$_GET['idusuarioresponsable']:'0'):'0'}}).trigger("change");
  
</script>
@endsection