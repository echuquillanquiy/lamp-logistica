@include('layouts/backoffice/producto/productos')
<script>
function seleccionarproducto(idproducto){
    $.ajax({
        url:"{{url('backoffice/cotizacion/show-seleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        success: function (respuesta){
          if(respuesta["datosProducto"]!=null){
            var validexist = 0;
            $("#tabla-cotizacion tbody tr").each(function() {
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
                      0,
                      '',
                      (respuesta["datosProducto"].codigoimpresion).toString().padStart(6,"0"),
                      respuesta["datosProducto"].compatibilidadnombre,
                      respuesta["datosProducto"].compatibilidadmotor,
                      respuesta["datosProducto"].compatibilidadmarca,
                      respuesta["datosProducto"].compatibilidadmodelo,
                      respuesta["datosProducto"].precio,
                      respuesta["datosProducto"].idunidadmedida,
                      respuesta["datosProducto"].unidadmedidanombre,
                      respuesta["stock"],
                      '0'
                );

            } 
          }
        }
    })
}
</script>