@include('layouts/backoffice/producto/productos')
<script>
function seleccionarproducto(idproducto){
    $.ajax({
        url:"{{url('backoffice/venta/show-seleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        success: function (respuesta){
          if(respuesta["datosProducto"]!=null){
            agregarproducto(
              respuesta["datosProducto"].id,
              (respuesta["datosProducto"].codigoimpresion).padStart(6,"0"),
              respuesta["datosProducto"].nombreproducto,
              respuesta["datosProducto"].precio,
              respuesta["datosProducto"].idunidadmedida,
              respuesta["datosProducto"].unidadmedidanombre,
              respuesta["stock"],
              '0',
              '0.00'
            );
          }
        }
    })
}
</script>