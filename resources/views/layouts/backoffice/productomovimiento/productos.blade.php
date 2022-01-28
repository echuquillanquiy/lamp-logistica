@include('layouts/backoffice/producto/productos')
<script>
function seleccionarproducto(idproducto){
    $.ajax({
        url:"{{url('backoffice/productomovimiento/show-seleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        success: function (respuesta){
          if(respuesta["datosProducto"]!=null){
            var validexist = 0;
            $("#tabla-productomovimiento tbody tr").each(function() {
                var num = $(this).attr('id');        
                var idproducto = $(this).attr('idproducto');
                if(idproducto==respuesta["datosProducto"].id){
                    validexist = 1;
                    alert('Ya existe en la lista!');
                }
            });
            if(validexist==0){
                agregarproducto(
                  respuesta["datosProducto"].id,
                  (respuesta["datosProducto"].codigoimpresion).padStart(6,"0"),
                  respuesta["datosProducto"].nombreproducto,
                  respuesta["stock"],
                  '0',
                  ''
                );
            } 
                
          }
        }
    })
}
</script>