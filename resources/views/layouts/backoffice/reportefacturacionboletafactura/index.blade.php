@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger"  data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte Boletas y Facturas</h4>
    </div>
    <div class="panel-body">
      <form action="{{ url('backoffice/reportefacturacionboletafactura') }}" method="GET" autocomplete="off"> 
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
                  <label>Código Venta</label>
                  <input class="form-control" type="text"  name="venta" id="venta"  value="{{isset($_GET['venta'])?($_GET['venta']!=''?$_GET['venta']:''):''}}">
                  <label>Tipo de Comprobante</label>
                  <select id="tipoCompbrobante" name="tipoCompbrobante">
                      <option></option>
                      <option value="03">Boleta</option>
                      <option value="01">Factura</option>
                  </select>
                  <label>Serie</label>
                  <input class="form-control" type="text"  name="serie" id="serie" value="{{isset($_GET['serie'])?($_GET['serie']!=''?$_GET['serie']:''):''}}">
                  <label>Correlativo</label>
                  <input class="form-control" type="text"  name="correlativo" id="correlativo" value="{{isset($_GET['correlativo'])?($_GET['correlativo']!=''?$_GET['correlativo']:''):''}}">
               </div>
               <div class="col-md-6">
                 
                  <label>Cliente</label>
                  <select name="cliente" id="cliente">
                      @if(isset($_GET['cliente']))
                          <?php $cliente = DB::table('users')->whereId($_GET['cliente'])->first(); ?>
                          @if($cliente!='')
                          <option value="{{$cliente->id}}">
                            @if( $cliente->idtipopersona == 1 )
                            {{ $cliente->identificacion }} - {{ $cliente->apellidos }}, {{ $cliente->nombre }}
                            @else
                            {{ $cliente->identificacion }} - {{ $cliente->nombre }}
                            @endif
                          </option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
                 
                  <label>Moneda</label>
                  <select name="moneda" id="moneda">
                      <option></option>
                      @foreach($monedas as $value)
                      <option value="{{ $value->nombre }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
                 
                  <label>Fecha de Inicio</label>
                  <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  <label>Fecha de Fin</label>
                  <input class="form-control" type="date" name="fechafin"  id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
               </div>
               <div class="col-md-12">
                  <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                  <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
                  <a href="javascript:;" onclick="reporte('excelsunat')" class="btn  btn-success" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel Sunat</a>
               </div>
             </div>
          </div>
        </form>
        <div class="table-responsive">            
           @include('layouts.backoffice.reportefacturacionboletafactura.tabla')
           {{ $facturacionboletafacturas->links('app.tablepagination', ['results' => $facturacionboletafacturas]) }}
        </div>
    </div>

</div>
@endsection
@section('subscripts')
<script>
  function reporte(tipo){
    window.location.href = '{{url('backoffice/reportefacturacionboletafactura')}}?'+
      'tipo='+tipo+
      '&venta='+$('#venta').val()+
      '&tipoCompbrobante='+($('#tipoCompbrobante').val()!=null?$('#tipoCompbrobante').val():'')+
      '&serie='+$('#serie').val()+
      '&correlativo='+$('#correlativo').val()+
      '&cliente='+($('#cliente').val()!=null?$('#cliente').val():'')+
      '&moneda='+($('#moneda').val()!=null?$('#moneda').val():'')+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
  }

  $("#tienda").select2({
    placeholder: "--  Seleccionar --",
    allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");

  $("#tipoCompbrobante").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val('{{isset($_GET['tipoCompbrobante'])?($_GET['tipoCompbrobante']!=''?$_GET['tipoCompbrobante']:'0'):'0'}}').trigger("change");
  
  $("#moneda").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val('{{isset($_GET['moneda'])?($_GET['moneda']!=''?$_GET['moneda']:'0'):'0'}}').trigger("change");
  
  

$('#cliente').select2({
  ajax: {
          url:      "{{url('backoffice/reportefacturacionboletafactura/show-listarcliente')}}",
          dataType: 'json',
          delay:    250,
          data:     function (params) {
                      return { buscar: params.term };
                    },
          processResults: function (data) {
                            return { results: data };
                          },
          cache: true
        },
    placeholder:  "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear:   true
}).on("change", function(e) {
  $.ajax({
          url:"{{url('backoffice/reportefacturacionboletafactura/show-seleccionarcliente')}}",
          type:'GET',
          data: { idcliente : e.currentTarget.value }
        })
});
</script>
@endsection