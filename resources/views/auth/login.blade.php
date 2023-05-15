<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>YMPI Information System</title>
  <style rel='stylesheet' type='text/css'>
    div#bottom-right {
      position: absolute;
      bottom: 5px;
      right: 5px;
    }
    div#bottom-left {
      position: absolute;
      bottom: 5px;
      left: 5px;
    }
  </style>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ asset("dist/css/AdminLTE.min.css")}}">
  <link rel="stylesheet" href="{{ asset("plugins/iCheck/square/blue.css")}}">
  <link rel="stylesheet" href="{{ asset("fonts/SourceSansPro.css")}}">
</head>
<body class="hold-transition login-page">
  <div class="login-logo" style="padding-top: 100px; margin-bottom: 0px">
    <img src="{{ url("images/logo_mirai.png")}}" height="200px">
  </div>
  <div class="login-box">
    <div class="login-box-body">
      {{-- <p class="login-box-msg" style="font-weight: bold; color: rgb(200,0,0);">Mulai 20 Januari 2020 Username (NIK) MIRAI menggunakan NIK baru.</p> --}}
      <form method="post" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
          <input autocomplete="off" type="text" class="form-control" placeholder="Username (NIK)" id="username" name="username" value="{{ old('username') }}" required autofocus>
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
          <input autocomplete="off" type="password" class="form-control" placeholder="Password" id="password" name="password" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          @if($errors->has('username'))
          <span class="help-block"></span>
          @endif
        </div>
        <div class="row">
          <div class="col-xs-12">
          </div>
          <div class="col-xs-8">
            {{-- <a href="{{ url("register") }}" class="pull-left">Register a new account</a> --}}
            <!-- <a href="{{ url('forgot') }}" class="pull-left">Lupa Password</a> -->
          </div>
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div id='bottom-left'>
    <a class="btn btn-default btn-sm pull-right" href="{{ url("index/completion_only") }}">Completion Only</a>
  </div>
  <div id='bottom-right'>
    <a class="btn btn-default btn-sm pull-right" href="{{ url("calon/karyawan") }}">Calon Karyawan <i class="fa fa-angle-double-right "></i></a>
  </div>
  <script src="{{ asset("bower_components/jquery/dist/jquery.min.js")}}"></script>
  <script src="{{ asset("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
  <script src="{{ asset("plugins/iCheck/icheck.min.js")}}"></script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
      });
    });

    jQuery(document).ready(function() {
      $('#username').val('');
      $('#password').val('');
    });
  </script>
</body>
</html>
