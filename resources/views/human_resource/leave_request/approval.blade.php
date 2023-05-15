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
  <link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
  {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
  @yield('stylesheets')
  <style type="text/css">
    #loading, #error { display: none; }
  </style>
</head>


<body class="hold-transition skin-purple layout-top-nav">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
      <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>

  </div>
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

        <div class="error" style="text-align: center;">
          <h1><i class="fa fa-file-text-o"></i> {{ $head }}</h1>
          <p>
            <h2>
            {{ $message }}<br>
            </h2>
          </p>
        </div>
        <?php if ($remark == 'GA'): ?>
          <div class="box box-solid" id="div_driver">
            <div class="box-body">
              <div class="form-group row" align="right">
                <label class="col-sm-2">Pilih Driver<span class="text-red">*</span></label>
                <div class="col-sm-5" align="left">
                  <select class="form-control selectPur" data-placeholder="Pilih Driver" name="driver_id" id="driver_id" style="width: 100%">
                    <option value=""></option>
                    @foreach($driver as $driver)
                    <option value="{{$driver->driver_id}}">{{$driver->remark}} - {{$driver->driver_id}} - {{$driver->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Pilih Kendaraan<span class="text-red">*</span></label>
                <div class="col-sm-5" align="left">
                  <select class="form-control selectPur" data-placeholder="Pilih Kendaraan" name="car" id="car" style="width: 100%">
                    <option value=""></option>
                    @foreach($cars as $car)
                    <option value="{{$car->plat_no}}_{{$car->car}}">{{$car->plat_no}} - {{$car->car}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-sm-7">
                <button class="btn btn-success pull-right" onclick="confirmDriver()">
                  CONFIRM
                </button>
              </div>
            </div>
          </div>
        <?php endif ?>

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
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  {{-- <script>$(document).ajaxStart(function() { Pace.restart(); });</script> --}}
  @yield('scripts')
</body>
</html>
<script type="text/javascript">
  jQuery(document).ready(function() {
    $('.selectPur').select2({
      allowClear:true
    });

    if ('{{$driver_ids}}' == 0) {
      $('#div_driver').hide();
    }else{
      $('#div_driver').show();
    }

  });

  function confirmDriver() {
    $('#loading').show();
    if ($('#driver_id').val() == '' || $('#car').val() == '') {
      $('#loading').hide();
      openSuccessGritter('Error!','Pilih Driver');
      return false;
    }

    var data = {
      id:'{{$driver_ids}}',
      driver_id:$('#driver_id').val(),
      car:$('#car').val(),
    }

    $.get('{{ url("confirm/human_resource/leave_request/driver") }}',data, function(result, status, xhr){
      if(result.status){
        $('#div_driver').hide();
        $('#loading').hide();
        openSuccessGritter('Success!',result.message);
      }else{
        $('#loading').hide();
        $('#div_driver').show();
        openSuccessGritter('Error!',result.message);
      }
    });
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '3000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '3000'
    });
  }
</script>
