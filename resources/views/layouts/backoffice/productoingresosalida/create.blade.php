@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a class="btn btn-dark btn-xs" href="{{ url('backoffice/productoingresosalida') }}"><i class="fa fa-angle-left"></i> Ir Atras</a>
        </div>
        <h4 class="panel-title">Registrar Ingreso/Salida de Productos</h4>
    </div>
    <div class="panel-body">
        <form class="js-validation-signin px-30" 
              action="javascript:;" 
              onsubmit="callback({
                route: 'backoffice/productoingresosalida',
                method: 'POST',
                data:{
                    view: 'registrar',
                    productos: seleccionarTablaProducto()
                }
            },
            function(resultado){
                location.href = '{{ url('backoffice/productoingresosalida') }}';                                                                            
            },this)">
          <div class="row">
            <div class="col-md-12">
              <label>Nombre *</label>
              <select name="" id="idtienda">
                <option value=""></option>
                @foreach($tiendas as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-3">
              <label>Productos *</label>
              <input type="text" class="form-control" id="codigoproducto" placeholder="Codigo de Barras">
            </div> 
            <div class="form-group col-md-9">
              <label>&nbsp;</label>
              <select name="" id="listaproducto">
              </select>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="table-producto">
              <thead class="thead-dark">
                <tr> 
                  <th>Productos</th> 
                  <th width="100px">Stock</th> 
                  <th width="100px">Cant. Ingreso</th> 
                  <th width="100px">Cant. Egreso</th> 
                  <th>Observación</th> 
                  <th width="10px"></th> 
                </tr>
              </thead>
              <tbody num="0">
              </tbody>
            </table>
          </div>
          <button type="submit" class="btn btn-danger">Guardar Cambios</button>
        </form>  
    </div>
</div>
@endsection
@section('subscripts')
<script>
$('#idtienda').select2({
  placeholder: '-- Seleccionar --'
});
  
$('#listaproducto').select2({
  placeholder: '-- Seleccionar Producto --',
  ajax: {
        url:"{{url('backoffice/venta/show')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term,
                  view: 'buscarproducto'
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
  },
  placeholder: "--  Seleccionar Producto --",
  allowClear: true,
  minimumInputLength: 2
}).on('change', function (e) {
  let idProducto = $('#listaproducto option:selected').val();
  let nombreProducto = $('#listaproducto option:selected').text();
  agregarProducto(idProducto, nombreProducto);
});
  
const agregarProducto = (idProducto, nombreProducto) => {
  let num = $('#table-producto tbody').attr('num');
  let tbodyHtml = ` <tr id="${num}" num="${num}" idproducto="${idProducto}"> 
                        <td>${nombreProducto}</td> 
                        <td>0</td> 
                        <td class="mx-td-text">
                          <input id="ingreso${num}" onkeyup="disabledInputCantidad()" type="text" class="form-control">
                        </td> 
                        <td class="mx-td-text">
                          <input id="salida${num}" onkeyup="disabledInputCantidad()" type="text" class="form-control">
                        </td> 
                        <td class="mx-td-text">
                          <input id="observacion${num}" type="text" class="form-control">
                        </td> 
                        <td class="mx-td-text">
                          <a onclick="eliminarProducto(${num})" title="Eliminar" 
                            class="btn btn-danger btn-square" href="javascript:;" role="button">
                              <i class="fa fa-trash-o" style="color: #fff"></i> 
                          </a>
                        </td> 
                      </tr>`;
  
  $('#table-producto tbody').attr('num',parseFloat(num)+1);
  $('#table-producto tbody').append(tbodyHtml);
}

const eliminarProducto = (num) =>  $('#table-producto tbody tr#'+num).remove();
 
const disabledInputCantidad = () => {
   $('#table-producto tbody > tr').each(function(e) {
      let num = $(this).attr('num');
      let ingreso = $('#ingreso'+num).val();
      let salida = $('#salida'+num).val();
      if (ingreso != '') {
          $("#salida"+num).prop( "disabled", true );
      }else if (ingreso == '') {
          $("#salida"+num).prop( "disabled", false );
      }
      if (salida != '') {
          $("#ingreso"+num).prop( "disabled", true );
      }else if (salida == '') {
          $("#ingreso"+num).prop( "disabled", false );

      }
   });
}

 const seleccionarTablaProducto = () => {
    let productos = '';
    $('#table-producto tbody tr').each(function() {
        let num = $(this).attr('num');
        productos = productos+'&'+
        $(this).attr('idproducto')+'/,/'+
        $('#ingreso'+num).val()+'/,/'+
        $('#salida'+num).val()+'/,/'+
        $('#observacion'+num).val() ;
    });
    console.log(productos);
    return productos;
} 
  
</script>
@endsection