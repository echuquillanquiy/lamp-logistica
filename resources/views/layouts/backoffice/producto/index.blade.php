@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="panel panel-danger" data-sortable-id="ui-widget-3">
    <div class="panel-heading ui-sortable-handle">
        <div class="btn-group btn-group-toggle pull-right">
            <a href="javascript:;" class="btn btn-warning" onclick="modal({route:'producto/create?view=registrar'})"><i class="fa fa-plus"></i> Registrar</a>
        </div>
        <h4 class="panel-title">Productos</h4>
    </div>
    <div class="panel-body">
        <form>
            <div class="row">
            <div class="col-md-3">
                <input type="text"  id="codigoimpresion" class="form-control" placeholder="Código "/>
                <input type="text" id="nombreproducto" class="form-control" placeholder="Nombre del Producto"/>
                <input type="text"  id="productocategoria" class="form-control" placeholder="Categoría"/>
              </div>
               <div class="col-md-3">
                <input type="text" id="productomarca" class="form-control" placeholder="Marca"/>
                <input type="text" id="productotalla" class="form-control" placeholder="Talla"/>
              </div>
            <div class="col-md-3">
                <div style="background-color: #dee2e6;width: 100%;height: 111px;" id="cont-productogaleriaimg"></div>
            </div>
            <div class="col-md-3">
              <div style="height: 111px;background-color: #dee2e6;" id="cont-almacen"></div>
            </div>
            </div>
        </form>
        <table id="myTable" class="table table-bordered table-hover table-striped" style="width:100%;">
            <thead class="thead-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Talla</th>
                    <th>P. Mínimo</th>
                    <th>P. Sugerido</th>
                    <th width="10px"></th>
                  </tr>
            </thead>
        </table>
    </div>
</div>
<style>
  .dataTables_scrollFootInner .dataTable {
      margin: 0 !important;
  }
  mark {
  padding: 0;
  background: #f1c40f;
}
div.dataTables_wrapper div.dataTables_processing {
    display: none !important;
}
</style>

@endsection
@section('subscripts')
<script>
$(document).ready(function() {
    var selected = [];
    var table = $('#myTable').DataTable({
        ajax: "{{url('resources/views/layouts/backoffice/producto/clientes.json')}}",
        dom: 'rti',
        //dom: 'Rlfrtip',
        scrollX: true,
        mark: true,
        scrollY: 400,
        scroller: {
            loadingIndicator: true
        },
        colReorder: false,
        /*colReorder: {
            allowReorder: false
        },*/
        order: [[ 1, "asc" ]],
        language: {
          info: "Mostrando _START_ de _TOTAL_ entradas"
        },
        columns: [
            { data: "codigoimpresion"},
            { data: "nombreproducto"},
            { data: "productonombrecategoria"},
            { data: "productonombremarca"},
            { data: "productonombretalla"},
            { data: "preciotienda" },
            { data: "precio" },
            { data: null, 
              defaultContent: '<div class="btn-group" style="width: 50px;">'+
                      '<a href="javascript:;" class="btn btn-white btn-sm dropdown-toggle width-80 no-caret mx-btn-opcion" data-toggle="dropdown">'+
                        '<i class="fa fa-cogs" aria-hidden="true"></i> <span class="caret"></span>'+
                      '</a>'+
                      '<ul class="dropdown-menu pull-right">'+
                      '</ul>'+
                    '</div>', 
              orderable: false, 
              className: "with-btn-group"
            }
        ]
    });
  
  
    $('#myTable tbody').on('click', 'tr', function () {
        var data = table.row(this).data();
        $('#myTable tbody .selected').removeClass('selected');
        $(this).addClass('selected');
        load('#cont-almacen'); 
        $.ajax({
            url:"{{url('backoffice/producto/show-almacen')}}",
            type:'GET',
            data: {
                idproducto : data.id
            },
            success: function (respuesta){
              $('#cont-almacen').html(respuesta); 
            }
        });
        load('#cont-productogaleriaimg'); 
      
        $('#cont-productogaleriaimg').attr('onclick','modal({route:\'producto/'+data.id+'/edit?view=imagendetalle\'})'); 
        $.ajax({
            url:"{{url('backoffice/producto/show-productogaleriaimg')}}",
            type:'GET',
            data: {
                idproducto : data.id
            },
            success: function (respuesta){
                $('#cont-productogaleriaimg').html(respuesta); 
            }
        });
    } );

    // Opción
    $('#myTable tbody').on( 'click', 'a.mx-btn-opcion', function () {
        var data = table.row( $(this).parents('tr') ).data();
        var tr = $(this).parents('td');
        var row = tr.find('ul');
        <?php $idpermiso = usersmaster()->idpermiso ?>
        @if($idpermiso==1 || $idpermiso==4)
        $(row).html('<li><a href="javascript:;" onclick="modal({route:\'producto/'+data.id+'/edit?view=editar\'})"><i class="fa fa-edit"></i> Editar</a></li>'+
                       '<li><a href="javascript:;" onclick="modal({route:\'producto/'+data.id+'/edit?view=codigobarra\'})"><i class="fa fa-barcode"></i> Código de Barra 25x65</a></li>'+
                        '<li><a href="javascript:;" onclick="modal({route:\'producto/'+data.id+'/edit?view=registrarimagen\'})"><i class="fa fa-images"></i> Subir Imagenes</a></li>'+
                        '<li><a href="javascript:;" onclick="modal({route:\'producto/'+data.id+'/edit?view=eliminar\'})"><i class="fa fa-trash"></i> Eliminar</a></li>');
        @else
        $(row).html('<li><a href="javascript:;" onclick="modal({route:\'producto/'+data.id+'/edit?view=registrarimagen\'})"><i class="fa fa-images"></i> Subir Imagenes</a></li>'
                        '<li><a href="javascript:;" onclick="modal({route:\'producto/'+data.id+'/edit?view=codigobarra\'})"><i class="fa fa-barcode"></i> Código barra 25x65</a></li>'
                   );
        @endif 
    } );

   
    $('#codigoimpresion').on('keyup',function(){
        table.column(0).search(this.value).draw();
    });
    $('#nombreproducto').on('keyup',function(){
        table.column(1).search(this.value).draw();
    });
    $('#productocategoria').on('keyup',function(){
        table.column(2).search(this.value).draw();
    });
   $('#productomarca').on('keyup',function(){
        table.column(3).search(this.value).draw();
    });
   $('#productotalla').on('keyup',function(){
        table.column(4).search(this.value).draw();
    });
  
} );
</script>
@endsection