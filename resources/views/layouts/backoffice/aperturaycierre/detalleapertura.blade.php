<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Detalle de Apertura de Caja</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Caja</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" value="{{ $aperturacierre->cajanombre }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto asignado S/.</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{ $aperturacierre->montoasignarsoles }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Monto asignado $</label>
            <div class="col-sm-8">
                <input class="form-control" type="number" value="{{ $aperturacierre->montoasignardolares }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Persona responsable</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" value="{{ $aperturacierre->usersresponsableapellidos }}, {{ $aperturacierre->usersresponsablenombre }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Persona asignado</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" value="{{ $aperturacierre->usersrecepcionapellidos }}, {{ $aperturacierre->usersrecepcionnombre }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Fecha de Registro</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" value="{{ $aperturacierre->fecharegistro }}" disabled/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Fecha de Confirmación</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" value="{{ $aperturacierre->fechaconfirmacion }}" disabled/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Rechazar</button>
    </div>
</div>