<div class="modal-content">
<form action="javascript:;" 
          onsubmit="callback({
            route: 'backoffice/aperturaycierre/{{ $aperturacierre->id }}',
            method: 'PUT',
            data:{
                view: 'confirmarcierre'
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/aperturaycierre') }}';                                                                            
        },this)"> 
    <div class="modal-header">
        <h4 class="modal-title">Confirmar Cierre de Caja</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
			  @include('app.efectivodetalle',['idaperturacierre'=>$aperturacierre->id])  	
        <div class="alert alert-warning"> ¡Esta seguro de Cerrar la Caja!</div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Cerrar Caja</button>
    </div>
</form>  
</div>