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
       <div class="box box-solid">
        <div class="box-body">
          <div class="col-sm-8">
            <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
              <tbody align="center">
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30%;">Posisi</td>
                  <td colspan="3" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40">{{ $isimail[0]->position }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Department</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">{{ $isimail[0]->department }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40 ">Status</td>
                  <td colspan="3" style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30">{{ $isimail[0]->employment_status }}</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40 ">Jumlah</td>
                  <td colspan="3" style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30"> L ({{ $isimail[0]->quantity_male }}) / P ({{ $isimail[0]->quantity_female }})</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40 ">Alasan Penambahan</td>
                  <td colspan="3" style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30"> {{ $isimail[0]->reason}}</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40 ">Perkiraan Tanggal Masuk</td>
                  <td colspan="3" style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30">{{ $isimail[0]->start_date}}</td>
                </tr>
              </tbody>            
            </table><br><br>

            <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
              <tbody align="center">
                <tr>
                  <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40">Kualifikasi Umum</td>
                  <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40">Kualifikasi Khusus</td>
                </tr>
                <tr>
                  <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40" align="left">Personal :</td>
                  <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40" align="left">Keahlian/Ketrampilan yang diutamakan :</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Umur</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">Max {{ $isimail[0]->min_age}} Tahun <br> Min {{ $isimail[0]->max_age}} Tahun</td>
                  <td colspan="2" rowspan="4" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 50%" align="left">- Mengerti Proses Permesinan</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Status Perkawinan</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">{{ $isimail[0]->marriage_status}}</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Domisili</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">{{ $isimail[0]->domicile}}</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Pengalaman Kerja</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">{{ $isimail[0]->work_experience}} Tahun</td>
                </tr>
                <tr>
                  <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40" align="left">Pendidikan :</td>
                  <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40" align="left">Persyaratan Lainnya :</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Jenjang Pendidikan</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">{{ $isimail[0]->educational_level}}</td>
                  <td colspan="2" rowspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 50%" align="left">- Mengerti Proses Permesinan</td>
                </tr>
                <tr>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 20%">Jurusan</td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 30% ">{{ $isimail[0]->major}}</td>
                </tr>
                <tr>
                  <td colspan="4" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40" align="left">Catatan :</td>
                </tr>
                <tr>
                  <td colspan="4" rowspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; width: 50%" align="left">{{ $isimail[0]->note}}</td>
                </tr>
                <tr></tr>
              </tbody>            
            </table>
          </div>
          <div class="col-sm-4">
            <button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 20px" onclick="confirm()">Approve (承認)</button>
          </div>
        </div>
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

  function confirm(){

    $.get('{{ url("human_resource/appproval/confirm/")."/".Request::segment(3) }}', function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!',result.message);
      }else{
        openSuccessGritter('Error!',result.message);
      }
    });
  }
</script>