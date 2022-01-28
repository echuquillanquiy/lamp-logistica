<?php $usuario = usersmaster(); ?>
<?php $sistema = DB::table('sistema')->where('sistema.id',1)->first() ?>
<?php $sistemaimagenlogin = DB::table('sistemaimagenlogin')->where('sistemaimagenlogin.idsistema',1)->get() ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>
      @if( $sistema->nombre!='' )
        {{ $sistema->nombre }}
      @else            
      {{ config('app.name', 'Sistema') }}
      @endif
  </title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
  
  @if( $sistema->imagenicono!='' )
  <link rel="icon" type="image/png" href="{{ url('public/admin/sistema/'.$sistema->imagenicono ) }}">
  @else
  <link rel="icon" type="image/png" href="{{url('public/admin/img/general/icono.png')}}">
  @endif	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/bootstrap/4.0.0/css/bootstrap.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/font-awesome/5.0/css/fontawesome-all.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/animate/animate.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/css/default/style.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/css/default/style-responsive.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/css/default/theme/red.css') }}" rel="stylesheet" id="theme" />
	<!-- ================== END BASE CSS STYLE ================== -->
	
	<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
	<link href="{{ url('public/assets/plugins/jquery-jvectormap/jquery-jvectormap.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" />
    <link href="{{ url('public/assets/plugins/gritter/css/jquery.gritter.css') }}" rel="stylesheet" />
	<!-- ================== END PAGE LEVEL STYLE ================== -->
  
	<link href="{{ url('public/assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/DataTables/extensions/AutoFill/css/autoFill.bootstrap.min.css') }}" rel="stylesheet" />
	<link href="{{ url('public/assets/plugins/DataTables/extensions/Responsive/css/responsive.bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/isotope/isotope.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/lightbox/css/lightbox.css') }}" rel="stylesheet" />
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="{{ url('public/assets/plugins/pace/pace.min.js') }}"></script>
	<!-- ================== END BASE JS ================== -->
  
  <link href="{{ url('public/assets/plugins/app/css/carga.css') }}" rel="stylesheet">
  <style>
    body {
        background: #0a9fa7;
    }
    .panel-danger > .panel-heading {
        background: #aedaff;
    }
    .panel-title {
        color: #272727;
    }
    .badge.badge-success{
        background: #0a9fa7;
    }
    .btn.btn-warning,
    .btn.btn-success {
        color: #fff;
        background: #424141;
        border-color: #3e3d3d;
    }
    table {
        font-weight: bold;
        color: #000;
    }
    .table .thead-dark th {
        color: #fff;
        background-color: #065f65;
        border-color: #077279;
    }
    .table.table-bordered > thead:first-child > tr:first-child > th {
        border-top: 1px solid #065f65;
    }
    .table-striped > tbody > tr:nth-child(odd) > td {
        background: #d7ebfb;
    }
    .table > tbody > tr > td, .table > tfoot > tr > td {
        border-color: #7ec1c5;
        padding: 10px 5px;
        white-space: nowrap;
    }
    @media (max-width: 767px){
        .page-header-fixed {
            padding-top: 168px !important;
        }
        .modal-message .modal-header,
        .modal-message .modal-body,
        .modal-message .modal-footer {
            width: 100% !important;
        }
    }
    @media (max-width: 480px){
          .error-code {
            line-height: 200px;
        }  
    }

  </style>
</head>
<style>
  .mx-td-text {
    padding: 0px !important;
    padding-left: 1px !important;
    padding-right: 1px !important;
    margin: 0 !important;
  }
</style>
<body url="{{ url('/') }}" >

 

	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
		<!-- begin #header -->
		<div id="header" class="header navbar-default">
      <!-- begin navbar-header -->
			<div class="navbar-header">
				<a href="{{url('backoffice/inicio')}}" class="navbar-brand">
          @if($sistema->imagenicono!='')
          <img src="{{url('public/admin/sistema/'.$sistema->imagenicono)}}" width="25px">
          @else
          <img src="{{url('public/admin/img/general/icono.png')}}" width="25px">        
          @endif 
          <b>
              @if( $sistema->nombre!='' )
                {{ $sistema->nombre }}
              @else            
              {{ config('app.name', 'Sistema') }}
              @endif
          </b>
        </a>
				<button type="button" class="navbar-toggle" data-click="top-menu-toggled">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<!-- end navbar-header -->
			<!-- begin header-nav -->
      <ul class="navbar-nav navbar-left">
        <li style="margin-top: 15px;margin-right: 5px;">
					<span class="badge badge-purple">Tienda: {{ $usuario->tiendanombre }}</span>
				</li>
        <li style="margin-top: 15px;margin-right: 5px;">
					<span class="badge badge-warning">Permiso: {{ $usuario->permiso }}</span>
				</li>
        <li style="margin-top: 15px;margin-right: 5px;">
          <?php $aperturacierre = aperturacierre($usuario->idtienda,Auth::user()->id); ?>
          @if(isset($aperturacierre['apertura']))    
              @if($aperturacierre['apertura']->idestado==1)
                  <span class="badge badge-info"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span>
              @elseif($aperturacierre['apertura']->idestado==2)
                  <span class="badge badge-info"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span>
              @elseif($aperturacierre['apertura']->idestado==3)
                  <?php 
                  $monedasoles = DB::table('moneda')->whereId(1)->first();
                  $monedadolares = DB::table('moneda')->whereId(2)->first();
                  $efectivosoles = efectivo($aperturacierre['apertura']->id,1);
                  $efectivodolares = efectivo($aperturacierre['apertura']->id,2); 
                  ?>
                  <span class="badge badge-success">
                    <a href="javascript:;" style="color: white;" onclick="modal({route:'aperturaycierre/{{ $aperturacierre['apertura']->id }}/edit?view=detallecierre'})">
                    <i class="fa fa-tags"></i> 
                    {{ $aperturacierre['apertura']->cajanombre }}: ({{ $monedasoles->simbolo }} {{ $efectivosoles['total'] }}) ({{ $monedadolares->simbolo }} {{ $efectivodolares['total'] }})
                    </a>
                  </span>
              @elseif($aperturacierre['apertura']->idestado==4)
                  <span class="badge badge-info"><i class="fa fa-sync-alt"></i> Cierre Pendiente</span>
              @elseif($aperturacierre['apertura']->idestado==5)
                  <span class="badge badge-dark"><i class="fa fa-check"></i> Caja Cerrada</span>
              @else
                  <span class="badge badge-info"><i class="fa fa-tags"></i> Caja Inactivo</span>
              @endif
          @else
              <span class="badge badge-info"><i class="fa fa-tags"></i> Caja Inactivo</span>
          @endif 
        </li>
        @if($usuario->idestadosunat==2)
        <li style="margin-top: 15px;margin-right: 5px;">
					<span class="badge badge-success">Facturación en Producción</span>
				</li>
        @else
        <li style="margin-top: 15px;margin-right: 5px;">
					<span class="badge badge-dark">Facturación Inactivo</span>
				</li>
        @endif
      </ul>
			<ul class="navbar-nav navbar-right">
				<li class="dropdown navbar-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
            @if(Auth::user()->imagen!='')
                <img src="{{ url('public/admin/perfil/'.Auth::user()->imagen) }}"/>
            @else
								<img src="{{ url('public/assets/img/user/user-13.jpg') }}"/>
            @endif
						<span class="d-none d-md-inline">{{ Auth::user()->nombre }}</span> <b class="caret"></b>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a href="{{url('backoffice/inicio')}}" class="dropdown-item">Ir a Inicio</a>
						<a href="javascript:;" onclick="modal({route:'inicio/1/edit?view=editperfil'})" class="dropdown-item">Editar Perfil</a>
						<a href="javascript:;" onclick="modal({route:'inicio/1/edit?view=editcambiarclave'})" class="dropdown-item">Cambiar Contraseña</a>
						<div class="dropdown-divider"></div>
            <?php 
            $role_users = DB::table('role_user')
                ->join('roles','roles.id','role_user.role_id')
                ->join('tienda','tienda.id','role_user.idtienda')
                ->where('role_user.user_id',$usuario->id)
                ->select(
                    'role_user.sesion as sesion',
                    'role_user.id as id',
                    'roles.description as rolenombre',
                    'tienda.nombre as tiendanombre'
                )
                ->orderBy('tienda.nombre','asc')
                ->get(); 
            ?>
            @foreach($role_users as $value)
            <a href="{{url('backoffice/inicio/cambiar_permiso?idrole_user='.$value->id)}}" class="dropdown-item" <?php echo $value->sesion==1?'style="background-color: #f59c1a;color: #f8f9fa;"':'' ?>>{{$value->tiendanombre}} - {{$value->rolenombre}}</a>
            @endforeach
						<div class="dropdown-divider"></div>
						<a href="javascript:;" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item">Cerrar Sesión</a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
					</div>
				</li>
			</ul>
			<!-- end header Navegación right -->
		</div>
		<!-- end #header -->
		
    <!-- begin #top-menu -->
		<div id="top-menu" class="top-menu">
            <!-- begin top-menu nav -->
			<ul class="nav">
        <?php
          $modulos = DB::table('modulo')
            ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
            ->join('roles','roles.id','rolesmodulo.idroles')
            ->join('role_user','role_user.role_id','roles.id')
            ->where('role_user.user_id',Auth::user()->id)
            ->where('role_user.sesion',1)
            ->where('modulo.idmodulo',0)
            ->where('modulo.idestado',1)
            ->select('modulo.*')
            ->orderBy('modulo.orden','asc')
            ->get();
          ?>
          @foreach($modulos as $value)
          <li class="has-sub">
						<a href="javascript:;">
					        <b class="caret"></b>
						    <i class="{{ $value->icono }}"></i>
						    <span>{{ $value->nombre }}</span>
					    </a>
						<ul class="sub-menu">
                 <?php
             
                  $submodulos = DB::table('modulo')
                    ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                    ->join('roles','roles.id','rolesmodulo.idroles')
                    ->join('role_user','role_user.role_id','roles.id')
                    ->where('role_user.user_id',Auth::user()->id)
                    ->where('role_user.sesion',1)
                    ->where('modulo.idmodulo',$value->id)
                    ->where('modulo.idestado',1)
                    ->select('modulo.*')
                    ->orderBy('modulo.orden','asc')
                    ->get();
                  ?>
                  @foreach($submodulos as $subvalue)
						      <li><a href="{{ url($subvalue->vista) }}">{{ $subvalue->nombre }}</a></li>
                  @endforeach
						</ul>
					</li>
          @endforeach
        
               
            </ul>
            <!-- end top-menu nav -->
        </div>
		<!-- end #top-menu -->
		  
		<!-- begin #content -->
		<div id="content" class="content">
      @yield('cuerpobackoffice')   
		</div>
		<!-- end #content -->
		
		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>

	<!-- ================== BEGIN BASE JS ================== -->
	<script src="{{ url('public/assets/plugins/jquery/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/bootstrap/4.0.0/js/bootstrap.bundle.min.js') }}"></script>
	<!--[if lt IE 9]>
		<script src="{{ url('public/assets/crossbrowserjs/html5shiv.js') }}"></script>
		<script src="{{ url('public/assets/crossbrowserjs/respond.min.js') }}"></script>
		<script src="{{ url('public/assets/crossbrowserjs/excanvas.min.js') }}"></script>
	<![endif]-->
	<script src="{{ url('public/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/js-cookie/js.cookie.js') }}"></script>
	<script src="{{ url('public/assets/js/theme/default.min.js') }}"></script>
	<script src="{{ url('public/assets/js/apps.min.js') }}"></script>
	<!-- ================== END BASE JS ================== -->
	
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="{{ url('public/assets/plugins/gritter/js/jquery.gritter.js') }}"></script>
	<script src="{{ url('public/assets/plugins/flot/jquery.flot.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/flot/jquery.flot.time.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/flot/jquery.flot.resize.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/flot/jquery.flot.pie.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/sparkline/jquery.sparkline.js') }}"></script>
	<script src="{{ url('public/assets/plugins/jquery-jvectormap/jquery-jvectormap.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/jquery-jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
	<script src="{{ url('public/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
	<script src="{{ url('public/assets/js/demo/dashboard.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/select2/dist/js/select2.min.js') }}"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
  
  <!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="{{ url('public/assets/plugins/DataTables/media/js/jquery.dataTables.js') }}"></script>
	<script src="{{ url('public/assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/DataTables/extensions/AutoFill/js/dataTables.autoFill.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/DataTables/extensions/AutoFill/js/autoFill.bootstrap.min.js') }}"></script>
	<script src="{{ url('public/assets/plugins/DataTables/extensions/Responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ url('public/assets/plugins/DataTables/extensions/Select/js/dataTables.select.min.js') }}"></script>
  <script src="{{ url('public/assets/plugins/DataTables/extensions/ColReorder/js/dataTables.colReorder.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/gh/jeffreydwalter/ColReorderWithResize@9ce30c640e394282c9e0df5787d54e5887bc8ecc/ColReorderWithResize.js"></script>
  <script src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
  <script src="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
  <script src="{{ url('public/assets/plugins/DataTables/extensions/Scroller/js/dataTables.scroller.min.js') }}"></script>
	<script src="{{ url('public/assets/js/demo/table-manage-autofill.demo.min.js') }}"></script>


	<script src="{{ url('public/assets/plugins/isotope/jquery.isotope.min.js') }}"></script>
  <script src="{{ url('public/assets/plugins/lightbox/js/lightbox.min.js') }}"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
  <!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="{{url('public/assets/plugins/chart-js/Chart.min.js')}}"></script>
<!-- 	<script src="{{url('public/assets/js/demo/chart-js.demo.js')}}"></script> -->
	<!-- ================== END PAGE LEVEL JS ================== -->
  
	<script src="{{ url('public/assets/plugins/app/js/app.js') }}"></script>
  @section('subscripts')
  @show  
	<script>
		$(document).ready(function() {
			App.init();
			//Dashboard.init();
		});
	</script>
</body>
</html>
