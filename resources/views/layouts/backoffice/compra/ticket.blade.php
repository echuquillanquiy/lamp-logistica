<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Ticket</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="cargarventaclose()">×</button>
    </div>
    <div class="modal-body">
        <iframe src="{{url('backoffice/compra/'.$compra->id.'/edit?view=ticket-pdf')}}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
    </div>
</div>