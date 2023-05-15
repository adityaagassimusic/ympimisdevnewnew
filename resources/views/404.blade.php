<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
  <title>YMPI 情報システム</title>
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <link rel="stylesheet" href="{{ url("plugins/iCheck/all.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/skins/skin-purple.css")}}">
  <link rel="stylesheet" href="{{ url("fonts/SourceSansPro.css")}}">
  <link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
  {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
  @yield('stylesheets')
  <style>
    .crop {
      overflow: hidden;
    }
    .crop img {
      margin: -10% 0 -10% 0;
    }
  </style>
  <style>
    aside{
      font-size: 12px;
    }
    .sidebar-menu > li > a {
      padding: 7px 5px 7px 15px;
      display: block;
    }
    .treeview-menu > li > a {
      padding: 3px 5px 3px 15px;
      display: block;
      font-size: 12px;
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    @include('layouts.leftbar')
    <div class="content-wrapper">
      @yield('header')
      <section class="content">
        <div class="col-xs-4">
          <span style="font-weight: bold; font-size: 6vw;" class="text-yellow pull-right">404</span>
        </div>
        <div class="col-xs-8">
          <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found. <span class="text-purple">ページが見つかりません。</span></h3>
          <p>
            Halaman yang anda kunjungi sedang dalam perbaikan.<br>
            Atau anda tidak memiliki hak akses ke halaman ini.<br>
            <span class="text-purple">アクセスするウェブページが修理中又は改良中です。<br>
            若しくは、あなたにこのウェブページにアクセスする権限がありません。</span><br><br>
            Tekan link di bawah ini untuk kembali ke halaman sebelumnya.<br>
            <span class="text-purple">以下のURLをクリックして前のウェブページに戻ってください。</span><br><br>
            <a href="javascript:history.back()" type="button" class="btn btn-warning" style="font-weight: bold;"><i class="fa fa-angle-double-left "></i> Kembali 戻る</a>
          </p>
          <p style="font-weight: bold; font-size:20px; color: red;">
            {{ isset($message) == 1 ? $message : '' }}
          </p>
        </div>
      </div>
      @include('layouts.footer')
    </div>
    <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
    <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
    <script src="{{ url("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
    <script src="{{ url("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
    <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
    <script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{ url("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
    <script src="{{ url("plugins/iCheck/icheck.min.js")}}"></script>
    <script src="{{ url("bower_components/fastclick/lib/fastclick.js")}}"></script>
    {{-- <script src="{{ url("bower_components/PACE/pace.min.js")}}"></script> --}}
    <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
    <script src="{{ url("dist/js/demo.js")}}"></script>
    {{-- <script>$(document).ajaxStart(function() { Pace.restart(); });</script> --}}
    @yield('scripts')
  </body>
  </html>
