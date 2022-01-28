@include('layouts/backoffice/producto/productos')
<script>
function seleccionarproducto(idproducto){
    $.ajax({
        url:"{{url('backoffice/compra/show-seleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        success: function (respuesta){
          if(respuesta["datosProducto"]!=null){
            var validexist = 0;
            $("#tabla-compra tbody tr").each(function() {
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
                      respuesta["datosProducto"].codigoimpresion,
                      respuesta["datosProducto"].nombreproducto,
                      1,
                       '0.00',
                       '0.00'
                    );
            } 
                
          }
        }
    })
}
</script>