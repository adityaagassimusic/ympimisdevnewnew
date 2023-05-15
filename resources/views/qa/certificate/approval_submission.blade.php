<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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
  <style type="text/css">
    #loading{ display: none; }
  </style>
</head>


<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <header class="main-header" >
      <nav class="navbar navbar-static-top">
        {{-- <div class="container"> --}}
          <div class="navbar-header">
            <a href="{{ url("/home") }}" class="logo">
              @if($page == "Employment Services")
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>HR-Qu</b></span>
              @else
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
              @endif
            </a>
          </div>
          <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
              <li>
                <a style="font-size: 20px; font-weight: bold;" class="text-yellow">
                  <?php if (isset($title)) {
                   echo $title;
                 } ?>
               </a>
             </li>
           </ul>
         </div>

       </nav>
     </header>
     <div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 10px;">
      <section class="content">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
          <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
          </p>
        </div>
        <div class="error" style="text-align: center;">
          <h1><i class="fa fa-file-text-o"></i> {{ $head }}</h1>
          <p id="text_success">
            <?php if (ISSET($error)): ?>
              <h3 class="text-red"><i class="fa fa-times-circle fa-lg"></i> {{$error}}</h3>
            <?php endif ?>
            <?php if (ISSET($message)): ?>
              <h3 class="text-green"><i class="fa fa-check-circle fa-lg"></i> {{$message}}</h3>
            <?php endif ?>
          </p>
          <?php if (ISSET($news) && count($news) > 0) { ?>
            <table style="border:1px solid black; border-collapse: collapse;width: 100%">
              <thead style="background-color: rgb(126,86,134);color: white">
                <tr>
                  <th style="font-size: 15px;width: 2%">Request ID</th>
                  <th style="font-size: 15px;width: 2%">Employee ID</th>
                  <th style="font-size: 15px;width: 5%">Name</th>
                  <th style="font-size: 15px;width: 2%">Certificate Name</th>
                  <th style="font-size: 15px;width: 3%">Leader QA</th>
                </tr>
              </thead>
              <tbody align="center">
                <?php for ($i=0; $i < count($news); $i++) { ?>
                <tr>
                  <td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;padding: 3px;">{{$news[$i]->request_id}}</td>
                  <td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;padding: 3px;">
                  {{$news[$i]->employee_id}}
                  </td>
                  <td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;padding: 3px;">
                  {{$news[$i]->name}}
                  </td>
                  <td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;padding: 3px;">
                  {{$news[$i]->certificate_name}}
                  </td>
                  <td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;padding: 3px;">
                  {{explode('_',$news[$i]->leader_qa)[0]}} - {{explode('_',$news[$i]->leader_qa)[1]}}
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } ?>
        </div>

      </section>
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
  <script type="text/javascript">
    jQuery(document).ready(function() {
    });
    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  </script>
  @yield('scripts')
</body>
</html>
