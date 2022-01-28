<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Detalle</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <iframe src="{{url('backoffice/aperturaycierre/'.$aperturacierre->id.'/edit?view=pdfdetalle-pdf')}}" frameborder="0" width="100%" height="600px"></iframe>
    </div>
</div>