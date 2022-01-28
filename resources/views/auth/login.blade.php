<?php 
  $sistema = DB::table('sistema')->where('sistema.id',1)->first();
  $sistemaimagenlogin = DB::table('sistemaimagenlogin')->where('sistemaimagenlogin.idsistema',1)->get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>
      @if(isset($sistema))
        {{ $sistema->nombre }}
      @else            
      {{ config('app.name', 'Sistema') }}
      @endif
  </title>
  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
  <meta content="" name="description" />
  <meta content="" name="author" />
  
  @if(isset($sistema))
  <link rel="icon" type="image/png" href="{{ url('public/admin/sistema/'.$sistema->imagenicono ) }}">
  @else
  <link rel="icon" type="image/png" href="{{ url('public/admin/img/general/icono.png') }}">
  @endif
  <!-- ================== BEGIN BASE CSS STYLE ================== -->
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/bootstrap/4.0.0/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/font-awesome/5.0/css/fontawesome-all.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/plugins/animate/animate.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/css/default/style.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/css/default/style-responsive.min.css') }}" rel="stylesheet" />
  <link href="{{ url('public/assets/css/default/theme/red.css') }}" rel="stylesheet" id="theme" />
  <!-- ================== END BASE CSS STYLE ================== -->

  <!-- ================== BEGIN BASE JS ================== -->
  <script src="{{ url('public/assets/plugins/pace/pace.min.js') }}"></script>
  <!-- ================== END BASE JS ================== -->
</head>

<body class="pace-top">
  <!-- begin #page-loader -->
  <div id="page-loader" class="fade show"><span class="spinner"></span></div>
  <!-- end #page-loader -->

  <div class="login-cover">
    <div class="login-cover-image" style="background-image: url({{ url('public/assets/img/login-bg/login-bg-17.jpg') }})" data-id="login-cover-image"></div>
    <div class="login-cover-bg"></div>
  </div>
  <!-- begin #page-container -->
  <div id="page-container" class="fade">
    <!-- begin login -->
    <div class="login login-v2" data-pageload-addclass="animated fadeIn">
      <!-- begin brand -->
      <div class="login-header">
        <div class="brand">
          @if(isset($sistema))
          <img src="{{url('public/admin/sistema/'.$sistema->imagenicono)}}" height="50px">
          @else
          <span class="logo"></span>            
          @endif
          <b>          
              @if(isset($sistema))
                {{ $sistema->nombre }}
              @else            
              {{ config('app.name', 'Sistema') }}
              @endif
          </b>
          <small>
              @if(isset($sistema))
                {{ $sistema->slogan }}
              @else            
              Sistema de Gestión
              @endif</small>
        </div>
        <div class="icon">
          <i class="fa fa-lock"></i>
        </div>
      </div>
      <!-- end brand -->
      <!-- begin login-content -->
      <div class="login-content">
        <form method="POST" action="{{ route('login') }}" class="margin-bottom-0">
          @csrf
          <div class="form-group m-b-20">
            <input id="email" type="text" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Usuario"> 
            @error('email')
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span> @enderror
          </div>
          <div class="form-group m-b-20">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password" placeholder="Contraseña"> 
            @error('password')
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span> @enderror
          </div>
          <div class="checkbox checkbox-css m-b-20">
            <input type="checkbox" id="remember_checkbox" />
            <label for="remember_checkbox">
                            Recuérdame
                        </label>
          </div>
          <div class="login-buttons">
            <button type="submit" class="btn btn-success btn-block btn-lg">Iniciar sesión</button>
          </div>
          @if (Route::has('password.request'))
          <div class="m-t-20">
            ¿Olvidó su contraseña? Click <a href="{{ route('password.request') }}">Aqui</a> para recuperar.
          </div>
          @endif
        </form>
      </div>
      <!-- end login-content -->
    </div>
    <!-- end login -->

    <ul class="login-bg-list clearfix">
      @foreach( $sistemaimagenlogin as $value )
      <li><a href="javascript:;" data-click="change-bg" data-img="{{ url('public/admin/sistema/'.$value->imagen) }}" style="background-image: url({{ url('public/admin/sistema/'.$value->imagen) }})"></a></li>
      @endforeach
    </ul>

  </div>
  <!-- end page container -->

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
  <script src="{{ url('public/assets/js/demo/login-v2.demo.min.js') }}"></script>
  <!-- ================== END PAGE LEVEL JS ================== -->

  <script>
    $(document).ready(function() {
      App.init();
      LoginV2.init();
    });
  </script>
</body>

</html>