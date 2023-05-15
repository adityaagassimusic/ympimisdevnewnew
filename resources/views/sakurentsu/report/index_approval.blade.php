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
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">

  <link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
  
  @yield('stylesheets')
</head>


<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <header class="main-header" >
      <nav class="navbar navbar-static-top">
        {{-- <div class="container"> --}}
          <div class="navbar-header">
            <a href="{{ url("/home") }}" class="logo">
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
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
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
          <p style="position: absolute; color: white; top: 45%; left: 35%;">
            <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
          </p>
        </div>
        <div>
          <div style="text-align: center;">
            <h1><b><i class="fa fa-file-text-o"></i> 3M Request Approval</b></h1>
            <p>
              <h2>
                {{ $data->title }}<br>
              </h2>
              <h3>{{ $data->title_jp }}</h3><br>
            </p>
          </div>
        </div>

        <div style="text-align: center;">
          <p>
            <?php 
            if ($status == 'Sorry, You Don`t Have Access To Approval') { ?>
              <h2 class="text-red">
                <i class="fa fa-close fa-lg"></i> {{ $status }}<br>
              </h2>
            <?php } else if ($status == 'Add New Reminder 3M') { ?>
              <h2 class="text-blue">
                <i class="fa fa-calendar-check-o fa-lg"></i> {{ $status }}<br>
              </h2><br>
              <center id="inputan">
                <div class="input-group" style="width: 20%">

                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <input type="hidden" value="{{csrf_token()}}" name="_token">
                  <input type="text" class="form-control" placeholder="Select a date" id="reminder_date">
                </div>
                <button class="btn btn-success" style="margin-top: 3px" onclick="reminder()"><i class="fa fa-check"></i> Add Reminder</button>
              </center>
              <h3 class="text-green" style="display: none" id="msg">
                <i class="fa fa-check-circle-o fa-lg"></i> Reminder Successfully Added<br>
              </h2><br>
            <?php } else { ?>
              <h2 class="text-green">
                <i class="fa fa-check-circle fa-lg"></i> {{ $status }}<br>
              </h2>
            <?php } ?>
          </p>
        </div>
      </section>
    </div>
    @include('layouts.footer')
  </div>
  <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
  <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
  <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
  <script src="{{ url("dist/js/demo.js")}}"></script>
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
  <script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>

  @yield('scripts')
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    $('#reminder_date').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
      startDate: new Date()
    });

    function reminder() {
      $("#loading").show();
      var formData = new FormData();
      formData.append('form_number', '{{ Request::segment(4) }}');
      formData.append('reminder_date', $("#reminder_date").val());

      $.ajax({
        url: '{{ url("post/reminder/sakurentsu/3m") }}',
        type: 'POST',
        data: formData,
        success: function (result, status, xhr) {
          $("#loading").hide();

          openSuccessGritter('Success', result.message);
          $("#inputan").hide();
          $("#msg").show();
        },
        error: function(result, status, xhr){
          $("#loading").hide();

          openErrorGritter('Error!', result.message);
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }

    function openErrorGritter(title, message) {
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-danger',
        image: '{{ url("images/image-stop.png") }}',
        sticky: false,
        time: '2000'
      });
      audio_error.play();
    }

    function openSuccessGritter(title, message){
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '2000'
      });
    }
  </script>
</body>
</html>
