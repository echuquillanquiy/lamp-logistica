<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Nota de Crédito</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <iframe src="{{url('backoffice/facturacionnotacredito/'.$facturacionnotacredito->id.'/edit?view=comprobante-pdf')}}" frameborder="0" width="100%" height="600px"></iframe>
    </div>
</div>