<div class="modal-content">  
  <div class="cont-confirm" style="margin-top: 15px;">
      <div class="confirm" style="text-align: center; color: green;font-size: 30px;"><i class="fa fa-check"></i></div>
      <div class="confirm-texto" style=" text-align: center; color: green;font-size: 20px; ">¡Correcto!</div>
      <div class="confirm-subtexto" style=" font-size: 12px;text-align: center;color: black; font-weight: 700;">Se ha registrado correctamente.</div>
  </div>
  <br>
  <div class='custom-form' style='text-align: center;margin-bottom: 5px;'>
     <button type='button' class='btn big-btn color-bg flat-btn mx-realizar-pago btn btn-warning' style='margin: auto;float: none;' onclick='realizar_nueva_venta()'>
     <i class='fa fa-check'></i> Realizar Nueva Venta</button>
  </div>
  <div class='custom-form'style='text-align: center;margin-bottom: 5px;'>
     <button type='button' class='btn big-btn color-bg flat-btn btn-warning' style='margin: auto;float: none;' onclick='iraventas()'>
     <i class='fa fa-check'></i> Ir a las Ventas</button>
  </div>
   <div class="modal-body">
          <iframe src="{{url('backoffice/venta/'.$venta->id.'/edit?view=ticket-pdf')}}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
   </div>
</div>
<script>
function realizar_nueva_venta(){
    $('#mx-modal').modal('hide');
    $('#mx-modal').remove();
    $('.modal-backdrop').remove();
    modal({route:'venta/create?view=registrarventarapida',size:'modal-fullscreen'})
}
  
function iraventas(){
    location.href = '{{ url('backoffice/venta') }}';
}
</script>