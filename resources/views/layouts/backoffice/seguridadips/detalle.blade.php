<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Detalle Seguridad de IP</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Nombre</label>
            <div class="col-sm-8">
                <input type="text" value="{{$seguridadips->nombre}}" id="nombre" class="form-control" disabled/>
            </div>
        </div>
      <div class="form-group row">
            <label class="col-sm-4 col-form-label">IP</label>
            <div class="col-sm-8">
                <input type="text" value="{{$seguridadips->ip}}" id="ip" class="form-control" disabled/>
            </div>
        </div>
    </div>  
</div>