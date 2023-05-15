<!DOCTYPE html>
<html>
<style type="text/css">
  input[type="radio"] {
  }



  .radio {
    display: inline-block !important;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 16px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  /* Hide the browser's default radio button */
  .radio input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
  }

  /* Create a custom radio button */
  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #ccc;
    border-radius: 50%;
  }

  /* On mouse-over, add a grey background color */
  .radio:hover input ~ .checkmark {
    background-color: #ccc;
  }

  /* When the radio button is checked, add a blue background */
  .radio input:checked ~ .checkmark {
    background-color: #2196F3;
  }

  /* Create the indicator (the dot/circle - hidden when not checked) */
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }

  /* Show the indicator (dot/circle) when checked */
  .radio input:checked ~ .checkmark:after {
    display: block;
  }

  /* Style the indicator (dot/circle) */
  .radio .checkmark:after {
    top: 9px;
    left: 9px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
  }

#loading, #error { display: none; }
  
</style>

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
</head>


<body class="hold-transition skin-purple layout-top-nav">
   <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 40%;">
          <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
        </p>
      </div>
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
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
        <div class="box box-solid" id="div_reason">
          <div class="box-body">
           <div style="text-align: center;">
            <h1><b><i class="fa fa-file-text-o"></i> Pelaporan Kanagata Retak Approval & Decision</b></h1>
            <h3><i class="fa fa-file-text-o"></i> 金型割れ報告の承認と判定</h3>
            <p>
              <h3 class="text-green">
                <i class="fa fa-file"></i> {{$request_id}}<br>
              </h3>
            </p>
            <h3> Decision Pelaporan Kanagata Retak 金型割れ報告の判定</h3>
          </div>
          <div class="form-group row" align="right">
            <label class="col-md-2"></label>
            <div class="col-md-2">
              <label class="radio" >Lanjut 継続
                <input type="radio" id="information" name="information" value="Lanjut" checked>
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="col-sm-3">
              <label class="radio" >Ganti Cavity キャビティ交換
                <input type="radio" id="information" name="information" value="Ganti Cavity">
                <span class="checkmark"></span>
              </label>
            </div>
             <div class="col-sm-2">
              <label class="radio" >Ganti Die ダイ交換
                <input type="radio" id="information" name="information" value="Ganti Die">
                <span class="checkmark"></span>
              </label>
            </div>
          </div>
          <div class="box box-solid" id="div_reason">
            <div class="box-body">
              <div class="form-group row" align="right">
                <label class="col-sm-3">Comment<span class="text-red">*</span><br>コメント</label>
                <div class="col-sm-6" align="left">
                  <textarea class="form-control" id="comment" name="comment" placeholder="Enter Comment" style="width: 100%"> </textarea>
                </div>
              </div>
            </div>
          </div>
          <div style="text-align: center; padding-top: 10px; padding-bottom: : 10px;">
            <button class="btn btn-success" onclick="confirmReason()">
              CONFIRM (確認)
            </button>
          </div>
        </div>
      </div>
      <div class="error" style="text-align: center;">
        <p>
        </p>
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
  });

  function confirmReason() {
    if ($('input[id="information"]:checked').val() == '') {
      openSuccessGritter('Error!','Masukkan Decision');
      return false;
    }
    var comments = $('#comment').val();

    var data = {
      request_id:'{{$request_id}}',
      decision:$('input[id="information"]:checked').val(),
      comment:comments
    }
    $("#loading").show();


    $.get('{{ url("decision/pelaporan/kanagata") }}',data, function(result, status, xhr){
      if(result.status){
        $("#loading").hide();

        $('#div_reason').hide();
        openSuccessGritter('Success!',result.message);
      }else{
        $("#loading").hide();
        $('#div_reason').show();
        openErrorGritter('Error!',result.message);

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
