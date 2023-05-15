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
      @if(count($datas) > 0)

      <div class="box box-solid" id="div_reason">
        <div class="box-body" style="text-align:center;">


          <h2><b>SDS Update Expaired</b></h2>

          <table id="tableDocument" class="table table-bordered table-striped table-hover" >
           <input type="hidden" name="_token" value="{{ csrf_token() }}">

           <thead style="background-color: rgb(126,86,134); color: #fff;">
            <?php $no = 1 ?>
            <tr>
              <th style="width: 1%; text-align: center;" rowspan="2">No</th>
              <th style="width: 1%; text-align: center;" rowspan="2">Document ID</th>
              <th style="width: 1%; text-align: center;" rowspan="2">Item Code</th>
              <th style="width: 3%; text-align: center;" rowspan="2">Judul SDS</th>
              <th style="width: 3%; text-align: center;" rowspan="2">Valid From</th>
              <th style="width: 3%; text-align: center;" rowspan="2">Valid To</th>
              <th style="width: 3%; text-align: center;" rowspan="2">Action</th>
            </tr>
          </thead>

          <?php for ($i=0; $i < count($datas); $i++) { ?>
            <tbody id="tableDocumentBody">
              <tr align="center"> 
                <td style="width: 1%; text-align: center;" rowspan="2">{{ $no++ }}</td>
                <td style="width: 1%; text-align: center;" rowspan="2">{{$datas[$i]->document_id}}</td>
                <td style="width: 1%; text-align: center;" rowspan="2">{{$datas[$i]->gmc_material}}</td>
                <td style="width: 1%; text-align: center;" rowspan="2">{{$datas[$i]->title}}</td>
                <td style="width: 1%; text-align: center;" rowspan="2">{{$datas[$i]->version_date}}</td>
                <td style="width: 1%; text-align: center;" rowspan="2">{{$datas[$i]->last_date}}</td>
                <td style="width: 1%; text-align: center;" rowspan="2">
                  <center>
                    <?php $ver = explode("_", $datas[$i]->file_name_asli) ?>
                    <button onclick="modalUpdatePch('{{$datas[$i]->document_id}}','{{$datas[$i]->gmc_material}}','{{$datas[$i]->title}}')" class="btn btn-xs btn-primary " style="margin-right:5px;">Upload</button>
                    @if($datas[$i]->version != $ver[2])
                    <input type="checkbox" name="check"  id="check_{{$datas[$i]->document_id}}" <?php if($datas[$i]->version != $ver[2]) { echo "checked"; }?>> Kirim Email
                    @else
                    <input type="checkbox" name="check" id="check_{{$datas[$i]->document_id}}" hidden>
                    @endif
                  </center>
                </td>
              </tr>
            </tbody>
          <?php } ?>
        </table>
        
        <div style="text-align: center; padding-top: 10px; padding-bottom: : 10px;">
          <button class="btn btn-success" onclick="confirmReason()">
            CONFIRM (確認)
          </button>
        </div>
      </div>
    </div>

    @else
    <div class="ok" style="text-align: center;">
      <p>
        <h2 class="text-green">
          <i class="fa fa-check-circle fa-lg"></i> SDS Document ID : {{ Request::segment(4) }} Updated Successfully<br>
        </h2>
      </p>
    </div>       
    @endif 

    <div class="modal fade" id="modalUpdatePch">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="doc_desc"></h4>

            <div class="modal-body table-responsive no-padding" style="min-height: 100px">
              <table class="table table-hover table-bordered table-striped" id="tableFile">
                <tbody id='bodyFile'></tbody>
              </table>
              <iframe id="my_iframe" name="my_iframe" height="0" width="0" frameborder="0" scrolling="yes"></iframe>

              <form method="post" enctype="multipart/form-data" action="{{ url("upload/sds/document") }}" id="form_upload">
                <label>Upload file(s) <i class="fa fa-upload"></i></label>
                <input id="id_pch" name="id_pch" hidden>
                <input id="gmc_materials" name="gmc_materials" hidden>

                <input type="file" name="doc_upload" id="doc_upload">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <br>
                <center><i class="fa fa-spin fa-refresh" style="display: none" id="loading-upload"></i></center>
                <button class="btn btn-success btn-sm" style="width: 100%" type="button" onclick="do_upload(this)" id="btn-upload-file"><i class="fa fa-plus"></i>&nbsp; Upload</button>
              </form>
              <button class="btn btn-default btn-sm" style="width: 100%" type="button" data-dismiss="modal"><i class="fa fa-close"></i>&nbsp; Close</button>
            </div>
          </div>
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

  function confirmReason() {
    var tag = [];
    if (!confirm("Apakah Anda Yakin Ingin Update Data SDS ??")) {
      return false;
    } else {
      $("input[type=checkbox]:checked").each(function() {
        if (this.id.indexOf("All") >= 0) {

        } else {
          var id_print = this.id.split("_");
          tag.push(id_print[1]);
        }
      });

      var data = {
        document_id : tag
      }

      $.get('{{ url("update/sds/expaired") }}',data, function(result, status, xhr){
        if(result.status){    
          openSuccessGritter('Success', result.message);
          location.reload();
        }
        else{
          openErrorGritter('Error!', result.message);
        }

      });
    }
  }

  function modalUpdatePch(document_id,gmc,title){
    $('#modalUpdatePch').modal('show');
    $('#gmc_materials').val(gmc);
    $('#id_pch').val(document_id);
    $("#doc_upload").val('');

    $("#btn-upload-file").removeAttr("disabled");
    $("#loading-upload").hide();

    var data = {
      id : "{{ Request::segment(4) }}"
    }

    $('#bodyFile').empty();

    $.get('{{ url("fetch/sds/upload/document") }}', data, function(result, status, xhr){
      $("#bodyFile").empty();
      if (result.status) {

        body_file = "";

        $.each(result.data, function(key, value) {
          var no_ver = value.file_name_asli.split('_')[2];
          if (value.document_id == document_id && no_ver != value.version) {
            body_file += "<tr>";
            body_file += "<td>";
            body_file += "<a href='"+"{{ url('files/chemical/documents/') }}/"+value.file_name_asli+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value.file_name_asli+"</a>";
            body_file += "</td>";
            body_file += "</tr>";
          }
        });

        $("#bodyFile").append(body_file);
      }
    })
  }

  function getdata(cob){
    $("#btn-upload-file").removeAttr("disabled");
    $("#loading-upload").hide();
    $('#modalUpdatePch').modal('hide');
    $("#doc_upload").val('');


    var data = {
      id : "{{ Request::segment(4) }}"
    }

    $('#bodyFile').empty();

    $.get('{{ url("fetch/sds/upload/document") }}', data, function(result, status, xhr){
      $("#bodyFile").empty();
      if (result.status) {

        body_file = "";

        $.each(result.data, function(key, value) {
          var no_ver = value.file_name_asli.split('_')[2];
          body_file += "<tr>";
          body_file += "<td>";
          body_file += "<a href='"+"{{ url('files/chemical/documents/') }}/"+value.file_name_asli+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value.file_name_asli+"</a>";
          body_file += "</td>";
          body_file += "</tr>";
        });

        $("#bodyFile").append(body_file);
      }
    })
  }

  function do_upload(elem)
  {
    if (!confirm("Apakah Anda Yakin Ingin Upload Data SDS ??")) {
      return false;
    } else {
      $(elem).attr('disabled','disabled');
      $("#loading-upload").show();
      document.getElementById('form_upload').target = 'my_iframe';
      document.getElementById('form_upload').submit();
      location.reload();
    }
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

<script>
  var msg = '{{Session::get('alert')}}';
  var exist = '{{Session::has('alert')}}';
  if(exist){
   window.parent.getdata("cob");
 }
</script>
