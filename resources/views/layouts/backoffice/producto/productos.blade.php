<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Productos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <form>
            <div class="row">
            <div class="col-md-3">
                <input type="text"  id="codigoimpresion" class="form-control" placeholder="Cod. Barra"/>
                <input type="text" id="nombreproducto" class="form-control" placeholder="Nombre"/>
                <input type="text" id="productomarca" class="form-control" placeholder="Marca"/>
              </div>
               <div class="col-md-3">
                <input type="text"  id="productocategoria" class="form-control" placeholder="Categoría"/>
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
        <table id="tabla-productos" class="table table-bordered table-hover table-striped" style="width:100%;font-weight: bold;color: #000;">
            <thead class="thead-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Talla</th>
                    <th>P. Sugerido</th>
                    <th width="10px"></th>
                  </tr>
            </thead>
        </table>
    </div>
</form>  
</div>
<script>
$(document).ready(function() {
    var table = $('#tabla-productos').DataTable({
        ajax: "{{url('resources/views/layouts/backoffice/producto/clientes.json')}}",
        dom: 'rti',
        select: true,
        scrollX: true,
        mark: true,
        scrollY: 400,
        scroller: {
            loadingIndicator: true
        },
        colReorder: true,
        order: [[ 1, "asc" ]],
        language: {
          info: "Mostrando _START_ de _TOTAL_ entradas"
        },
        columns: [
            { data: "codigoimpresion"},
            { data: "nombreproducto"},
            { data: "productonombremarca"},
            { data: "productonombrecategoria"},
            { data: "productonombretalla"},
            { data: "precio" },
            { data: null, 
              defaultContent: '<a href="javascript:;" class="btn btn-warning btn-seleccionarproducto" ><i class="fa fa-check"></i> Seleccionar</span></a>', 
              orderable: false, 
              className: "with-btn"
            }
        ]
    });
  
    $('#tabla-productos tbody').on('click', 'tr', function () {
        var data = table.row(this).data();
        $('#tabla-productos tbody .selected').removeClass('selected');
        $(this).addClass('selected');
        //$(this).toggleClass('selected')
        load('#cont-almacen'); 
        $.ajax({
            url:"{{url('backoffice/producto/show-almacen')}}",
            type:'GET',
            data: {
                idproducto : data.idproducto
            },
            success: function (respuesta){
              $('#cont-almacen').html(respuesta); 
            }
        });
        load('#cont-productogaleriaimg'); 
        $.ajax({
            url:"{{url('backoffice/producto/show-productogaleriaimg')}}",
            type:'GET',
            data: {
                idproducto : data.idproducto
            },
            success: function (respuesta){
              $('#cont-productogaleriaimg').html(respuesta); 
            }
        });
    } );
  

  
    // Opción
    $('#tabla-productos tbody').on( 'click', 'a.btn-seleccionarproducto', function () {
        var data = table.row( $(this).parents('tr') ).data();
        $(this).removeClass('btn-warning').addClass('btn-success').html('<i class="fa fa-check"></i> Seleccionado').css('background-color','#0b730b').css('border-color','#0b730b');
        seleccionarproducto(data.idproducto);
    } );
 
    $('#codigoimpresion').on('keyup',function(){
        table.column(0).search(this.value).draw();
    });
    $('#nombreproducto').on('keyup',function(){
        table.column(1).search(this.value).draw();
    });
   $('#productomarca').on('keyup',function(){
        table.column(2).search(this.value).draw();
    });
    $('#productocategoria').on('keyup',function(){
        table.column(3).search(this.value).draw();
    });
    $('#productotalla').on('keyup',function(){
        table.column(4).search(this.value).draw();
    });
} );
 
</script>