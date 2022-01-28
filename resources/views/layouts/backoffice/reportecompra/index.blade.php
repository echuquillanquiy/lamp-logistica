@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/reportecompra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Reporte de Compras</h4>
    </div>
    <div class="panel-body">
        <form action="{{ url('backoffice/reportecompra') }}" method="GET"> 
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
                      
                        <label>Codigo Compra</label>
                        <input class="form-control" type="text" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}"/>
                       
                        <label>Comprobante</label>
                        <select id="idcomprobante" name="idcomprobante">
                            <option></option>
                            @foreach($comprobante as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                      
                        <label>Número de Comprobante</label>
                        <input class="form-control" type="text" id="seriecorrelativo" name="seriecorrelativo" value="{{isset($_GET['seriecorrelativo'])?($_GET['seriecorrelativo']!=''?$_GET['seriecorrelativo']:''):''}}"/>
                        
                        <label>Proveedor</label>
                        <select id="idproveedor" name="idproveedor">
                            @if(isset($_GET['idproveedor']))
                                @if($_GET['idproveedor']!='')
                                <?php $users = DB::table('users')->where('id',$_GET['idproveedor'])->first();?>
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
                        <label>Forma de Pago</label>
                        <select id="idformapago">
                            <option></option>
                            @foreach($formapago as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                      
                        <label>Estado</label>
                        <select id="idestado" name="idestado">
                            <option></option>
                            <option value="1">Pendiente</option>
                            <option value="2">Comprado</option>
                        </select>
                      
                        <label>Fecha de Inicio</label>
                        <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                        
                        <label>Fecha de Fin</label>
                        <input class="form-control" type="date" name="fechafin"  id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
                    </div>
                    <div class="col-md-12">
                        <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                        <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
        @include('layouts.backoffice.reportecompra.tabla')
        {{ $compra->links('app.tablepagination', ['results' => $compra]) }}
        </div>
    </div>
</div>
@endsection

@section('subscripts')
<script>
  
    function reporte(tipo){
        window.location.href = '{{url('backoffice/reportecompra')}}?'+
                                'tipo='+tipo+
                                '&codigo='+$('#codigo').val()+
                                '&idcomprobante='+($('#idcomprobante').val()!=null?$('#idcomprobante').val():'')+
                                '&idformapago='+($('#idformapago').val()!=null?$('#idformapago').val():'')+
                                '&seriecorrelativo='+$('#seriecorrelativo').val()+
                                '&idproveedor='+($('#idproveedor').val()!=null?$('#idproveedor').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }

    $("#tienda").select2({
        placeholder: "--  Seleccionar --",
        allowClear: true
    }).val({{usersmaster()->idtienda}}).trigger("change");

    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1,
        allowClear: true
    }).val({{isset($_GET['idcomprobante'])?($_GET['idcomprobante']!=''?$_GET['idcomprobante']:'0'):'0'}}).trigger("change");

    $('#idformapago').select2({
        placeholder: "---Seleccionar---",
        minimumResultsForSearch: -1,
        allowClear: true
    }).val( {{ isset($_GET['idformapago']) ? ($_GET['idformapago'] != '' ? $_GET['idformapago']: '0') : '0' }} ).trigger("change");

    $('#idproveedor').select2({
      ajax: {
            url:"{{url('backoffice/reportecompra/show-listarcliente')}}",
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
            url:"{{url('backoffice/reportecompra/show-seleccionarcliente')}}",
            type:'GET',
            data: {
                idcliente : e.currentTarget.value
           }
         })
    });

    $("#idestado").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1,
        allowClear: true
    }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
  
</script>
@endsection