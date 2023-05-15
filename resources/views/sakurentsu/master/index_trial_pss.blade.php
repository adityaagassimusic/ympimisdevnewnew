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
  <link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">


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
        <div style="text-align: center;" id="inputan_interpreter_tiga_em">
          <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
          <p>
            <h2>
              {{ $sk->title }}<br>
            </h2>
            <h3>Sakurentsu {{ $sk->category }}</h3><br>
          </p>
          <p>Trial Request File</p>
          <div class="btn-toolbar" role="toolbar">
            <?php 
            $trial_file = json_decode($sk->trial_file);
            foreach ($trial_file as $tfile) { 
              ?>
              <div class="btn-group" role="group">
                <a href="{{ url('uploads/sakurentsu/trial_req/'.$tfile)}}" style="margin-right: 2px" target="_blank"><button class="btn btn-secondary btn-default"><i class="fa fa-file-pdf-o fa-2x"></i><br><?php echo $tfile; ?></button></a>
              </div>
            <?php } ?>
          </div>
          <div>

          </div>
          <p>Please specify the PSS for this trial request</p>

          <textarea class="form-control tags" id="pss_desc" placeholder="type requirement PSS">
          </textarea>

          <button class="btn btn-success" onclick="save_pss()"><i class="fa fa-check"></i>&nbsp; Save PSS Requirement</button>
        </div>

        <div id="pesan_interpreter_tiga_em" style="display: none; text-align: center;">
          <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
          <p>
            <h2 class="text-green">
              <i class="fa fa-check-circle fa-lg"></i> Sakurentsu successfully assigned<br>
            </h2>
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
  <script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>

  @yield('scripts')
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.select2').select2();
    jQuery('.tags').tagsInput({ width: 'auto' });

    function save_pss() {
      if ($("#pss_desc").val() == "") {
        openErrorGritter('Error', 'Please fill PSS first');
        return false;
      }

      var data = {
        sakurentsu_number : "{{ $sk->sakurentsu_number }}",
        pss_desc : $("#pss_desc").val()
      }

      $.get('{{ url("post/sakurentsu/pss") }}', data, function(result, status, xhr){
        if (result.status) {
          openSuccessGritter('Success', 'PSS Requirement has been saved');
        }
      })
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
