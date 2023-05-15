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
                   echo $title.' '.$title_jp;
                 } ?>
               </a>
             </li>
           </ul>
         </div>

       </nav>
     </header>
     <div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 10px;">
      <section class="content" style="min-height: 120vh !important">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
          <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
          </p>
        </div>
        <div style="text-align: center;">
          <h3><i class="fa fa-file-text-o"></i> {{ $head }}<br><small>{{$title_jp}}</small></h3>
          <p id="text_success">
            <?php if (ISSET($message)): ?>
              <h3 class="text-green"><i class="fa fa-check-circle fa-lg"></i> {{$message}}</h3>
            <?php endif ?>
              <h3 class="text-red" id="error_message" style="display: none"><i class="fa fa-times-circle fa-lg"></i></h3>
              <h3 class="text-green" id="success_message" style="display: none"><i class="fa fa-check-circle fa-lg"></i></h3>
          </p>
          <?php if (!ISSET($message)): ?>
            <div class="col-xs-8" id="div_detail">
              <table class="table table-responsive" style="border:1px solid black; border-collapse: collapse;">
                <thead style="background-color: rgb(126,86,134);color: white">
                  <tr>
                    <th style="padding:3px;width: 1%;border: 1px solid black">#</th>
                    <th style="padding:3px;width: 2%;border: 1px solid black">Team No.</th>
                    <th style="padding:3px;width: 4%;border: 1px solid black">Team Name</th>
                    <th style="padding:3px;width: 4%;border: 1px solid black">Team Title</th>
                    <?php $asesor_id = []; ?>
                    <?php for($i = 0; $i < count($data['sga_asesor']);$i++){ ?>
                    <th style="padding:3px;width: 3%;border: 1px solid black">{{explode(' ',$data['sga_asesor'][$i]->asesor_name)[0]}} {{explode(' ',$data['sga_asesor'][$i]->asesor_name)[1]}}</th>
                    <?php array_push($asesor_id,$data['sga_asesor'][$i]->asesor_id) ?>
                    <?php } ?>
                    <th style="padding:3px;width: 1%;border: 1px solid black">Total Nilai</th>
                    <th style="font-size: 15px;width: 1%;border: 1px solid black">File PDF</th>
                  </tr>
                </thead>
                <tbody align="center">
                  <?php $index = 1 ?>
                  <?php for($i = 0; $i < count($data['teams']);$i++){ ?>
                  <?php if ($index < 6) {
                    $bgcolor = '#c3e157';
                  }else{
                    $bgcolor = 'none';
                  } ?>
                  <tr style="background-color: {{$bgcolor}}">
                    <td style="border:1px solid black; padding:3px; height: 20;text-align: right;">{{$index}}</td>
                    <td style="border:1px solid black; padding:3px; height: 20;text-align: left;">{{$data['teams'][$i]->team_no}}</td>
                    <td style="border:1px solid black; padding:3px; height: 20;text-align: left;">{{$data['teams'][$i]->team_name}}</td>
                    <td style="border:1px solid black; padding:3px; height: 20;text-align: left;">{{$data['teams'][$i]->team_title}}</td>
                    <?php
                    $total = 0;
                    for($k = 0; $k < count($asesor_id);$k++){
                      for($j = 0; $j < count($data['sga_result']);$j++){
                        if ($data['sga_result'][$j]->asesor_id == $asesor_id[$k] && $data['sga_result'][$j]->team_no == $data['teams'][$i]->team_no) { ?>
                          <td style="border:1px solid black; padding:3px; height: 20;text-align: left;">{{$data['sga_result'][$j]->total_nilai}}</td>
                          
                        <?php $total = $total + $data['sga_result'][$j]->total_nilai;
                        }
                      }
                    }
                     ?>
                     <td style="border:1px solid black; padding:3px; height: 20;text-align: right;">{{$total}}</td>
                     <td style="border:1px solid black; padding:3px; height: 20;text-align: center;">
                      @if($data['teams'][$i]->file_pdf != null)
                      <a target="_blank" style="text-decoration: none;" href="{{url('data_file/sga/pdf/'.$data['teams'][$i]->file_pdf)}}">File Presentation</a>
                      @endif
                     </td>
                  </tr>
                  <?php $index++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <div class="col-xs-4" style="font-size: 18px;text-align: left;" id="div_reason">
              <span style="font-weight: bold;">Reason</span>
              <br>
              <textarea id="reason" style="width: 100%" placeholder="Masukkan Reason"></textarea>
              <br>
              <button class="btn btn-success" onclick="inputReason()" style="width: 100%;font-weight: bold;">Submit</button>
              <br>
            </div>
          <?php endif ?>
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

    function inputReason() {
      $('#loading').show();
      var data = {
        periode:'{{$periode}}',
        reason:$('#reason').val()
      }
      $.get('{{ url("reject/sga/report/reason") }}', data, function(result, status, xhr){
        if(result.status){
          $('#loading').hide();
          $('#div_detail').hide();
          $('#div_reason').hide();
          $("#success_message").show();
          $("#success_message").html('<i class="fa fa-check-circle fa-lg"></i> SGA Rejected Successfully SGA却下完了');
        }else{
          audio_error.play();
          $('#loading').hide();
          $("#error_message").show();
          $("#error_message").html('<i class="fa fa-times-circle fa-lg"></i> '+result.message);
          return false;
        }
      });
    }
  </script>
  @yield('scripts')
</body>
</html>
