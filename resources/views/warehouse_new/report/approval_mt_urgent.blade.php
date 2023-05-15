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
                   {{ $title }}
               </a>
             </li>
           </ul>
         </div>

       </nav>
     </header>
     <div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 10px;">
      <section class="content">

        <div class="error" style="text-align: center;">
          <p>
            <h2>
              {{ $message }}<br>
            </h2>
            @if ($status == "APPROVAL")
            <h1 class="text-green"><i class="fa fa-check-circle fa-lg"></i>&nbsp;{{ $message2 }}</h1>
            <br>
            <h1><i class="fa fa-file-text-o"></i>Kode Request : URGENT-{{$kode_request->kode_request}}</h1>
            @elseif ($status2 == 'reject')
            <h1 class="text-red"><i class="fa fa-times-circle"></i>&nbsp;{{ $message2 }}</h1>
            @elseif ($status2 == 'finish')
             <h2 class="text-green">
                <i class="fa fa-check-circle fa-lg"></i> {{ $no }}<br>
              </h2>
            @endif
          </p>
          <br>
        </div>


        @if(!$status)
        <div style="text-align: center;">
          <center>
            <h1><i class="fa fa-file-text-o"></i> {{$kode}}</h1>
            <input type="hidden" id="kode_request" value="{{ $kode }}">
            <input type="hidden" id="stat2" value="{{ $status2 }}">
            <center><h3 id="give">Give Reason to Applicant : </h3></center>
            <textarea id="comment" class="form-control" placeholder="Fill your comment here" style="width: 80%"></textarea>
            <br>
            <button class="btn btn-success" onclick="postComment()" id="btn_send"><i class="fa fa-send"></i> Send Reason</button>
          </center>
        </div>
        @endif

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

    $("form#upload_form").submit(function(e) {
      e.preventDefault();    
      var formData = new FormData();
      formData.append('form_number', $("#form_number").val());
      formData.append('file_name', $("#file_name").val());
      formData.append('sap_file', $('#sap_file').prop('files')[0]);

      $.ajax({
        url: '{{ url("upload/approval/fixed_asset") }}',
        type: 'POST',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {

        }
      })
    })

    function postComment() {
      var formData = new FormData();
      formData.append('kode_request', $("#kode_request").val());
      formData.append('stat2', $("#stat2").val());
      formData.append('comment', $("#comment").val());

      $.ajax({
        url: '{{ url("post/approval/reject") }}',
        type: 'POST',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          $("#comment").hide();
          $("#btn_send").hide();
          $("#give").hide();


          openSuccessGritter('success', 'Successfully '+$("#stat2").val())
        }
      })
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
