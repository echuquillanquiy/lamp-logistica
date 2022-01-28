@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div id="page-container" class="fade show">
	    <!-- begin error -->
        <div class="error">
            <div class="error-code m-b-10">ERROR</div>
            <div class="error-content">
                <div class="error-message">La facturacion no esta habilitada para esta tienda.</div>
                <div class="error-desc m-b-30">
                   Contactese con el administrador.
                </div>
            </div>
        </div>
        <!-- end error -->
		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
@endsection