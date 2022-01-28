<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Caja</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
			  @include('app.efectivodetalle',['idaperturacierre'=>$aperturacierre->id])  	
        <div style="width:100%;height:5px;"></div>
    </div>
</div>