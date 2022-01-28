@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <h4 class="panel-title">Reporte de Movimientos de Productos</h4>
    </div>
    <div class="panel-body">
          <div class="custom-form">
            <div class="row">
               <div class="col-md-6">
                  <label>Tienda</label>
                  <select name="idtienda" id="idtienda" disabled>
                      <option></option>
                      @foreach($tiendas as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
                  </select>
               </div>
               <div class="col-md-6">
                  <label>Producto - Motor, Modelo</label>
                  <select name="idproducto" id="idproducto">
                      @if(isset($_GET['idproducto']))
                          <?php  
                    
                            $idproducto = DB::table('producto')
                                ->join('productonombre','productonombre.id','producto.idproductonombre')
                                ->join('productomotor','productomotor.id','producto.idproductomotor')
                                ->join('productomodelo','productomodelo.id','producto.idproductomodelo')
                                ->where('producto.id',$_GET['idproducto'])
                                ->select(
                                    'producto.*',
                                    'productonombre.nombre as productonombre',
                                    'productomotor.nombre as productomotor',
                                    'productomodelo.nombre as productomodelo',
                                )
                                ->first();
                   
                    ?>
                          @if($idproducto!='')
                          <option value="{{$idproducto->id}}">{{$idproducto->codigoimpresion}} - {{$idproducto->productonombre}}, {{$idproducto->productomotor}}, {{$idproducto->productomodelo}}</option>
                          @else
                          <option></option>
                          @endif
                      @else
                      <option></option>
                      @endif
                  </select>
               </div>
               <div class="col-md-12">
                  <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                  <a href="javascript:;" onclick="reporte('excel')" class="btn  btn-primary" style="margin-bottom:10px;" ><i class="fa fa-file-excel"></i> Exportar Excel</a>
               </div>
             </div>
          </div>
        <div class="table-responsive">
         @include('layouts.backoffice.reportecardexproducto.tabla')
         {{ $producto->links('app.tablepagination', ['results' => $producto]) }}
        </div>
    </div>
</div>

@endsection

@section('subscripts')
<style>
.pagination {
    margin-top: 5px;
}
</style>
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/reportecardexproducto')}}?'+
      'tipo='+tipo+
      '&idtienda='+($('#idtienda').val()!=null?$('#idtienda').val():'')+
      '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'');
}
 
  $("#idtienda").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{usersmaster()->idtienda}}).trigger("change");;
  
  $("#idproducto").select2({
      ajax: {
          url:"{{url('backoffice/reportecardexproducto/show-listarproducto')}}",
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
  });
  

</script>
@endsection
