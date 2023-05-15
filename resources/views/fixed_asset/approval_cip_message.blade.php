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
                  {{ $title }}
                </a>
              </li>
            </ul>
          </div>

        </nav>
      </header>
      <div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 10px;">
        <section class="content">
          <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
              <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
          </div>
          <div class="error" style="text-align: center;">
            <p>
              <h2>
                <i class="fa fa-book"></i>
                {{ $message }} <br>
              </h2>
              @if($message != 'Failed')
              @if ($status)
              <h1 class="text-green"><i class="fa fa-check-circle"></i>&nbsp;{{ $message2 }}</h1>
              @else
              @if ($status2 == 'Reject')
              <h1 class="text-red"><i class="fa fa-times-circle"></i>&nbsp;{{ $message2 }}</h1>
              @elseif ($status2 == 'Hold')
              <h1 class="text-blue"><i class="fa fa-exclamation-circle "></i>&nbsp;{{ $message2 }}</h1>
              @endif
              @endif
              @else
              <h1 class="text-red"><i class="fa fa-times-circle"></i>&nbsp;{{ $message2 }}</h1>
              @endif
            </p>
            <br>
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
    <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
    <script src="{{ url("dist/js/demo.js")}}"></script>
    <script src="{{ url("js/jquery.gritter.min.js") }}"></script>


    @section('scripts')
    <script>
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      jQuery(document).ready(function() {
        if ("{{ $position }}" == 'fa_control') {
          // console.log("{{ url('fixed_asset/transfer_cip/form/fa_control/'.$asset->form_number) }}");
          window.setTimeout(function(){window.location.href = "{{ url('index/fixed_asset/transfer_cip/form/fa_control/'.$asset->form_number) }}";}, 3000);
        }
      })

      function loading() {
        if ($("form").valid()) {
          $("#loading").show();
        }
      }

      function postComment() {
        var formData = new FormData();
        formData.append('id_form', $("#id_form").val());
        formData.append('position', "{{ $message }}");
        
        formData.append('comment', $("#comment").val());

        $.ajax({
          url: '{{ url("post/approval/fixed_asset") }}',
          type: 'POST',
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          success: function (response) {
            $("#comment").hide();
            $("#btn_send").hide();
            $("#text_comment").hide();

            openSuccessGritter('success', 'Fixed {{$message}} Successfully '+$("#stat2").val())
          }
        })
      }

      $("form#location_form").submit(function(e) {
        if ($("#location").val() == '') {
          openErrorGritter('Error', 'Harap mengisi lokasi');
          return false;
        }

        // $("#load")
      })

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

      function openErrorGritter(title, message) {
        jQuery.gritter.add({
          title: title,
          text: message,
          class_name: 'growl-danger',
          image: '{{ url("images/image-stop.png") }}',
          sticky: false,
          time: '2000'
        });
      }
    </script>

    @endsection

    @yield('scripts')
  </body>
  </html>
