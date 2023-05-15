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
              @if ($status)
              @if ($status2 == 'new_pic')
              <center id="text_loc"><h3>Please Select Location Asset : </h3></center>
              @elseif ($status2 == 'payment')
              <center id="text_loc"><h3>Please Upload Payment Receipt : </h3></center>
              @elseif ($status2 == 'disposal_date')
              <center id="text_loc"><h3>Please Select Retired Date : </h3></center>
              @elseif ($status2 == 'manager_disposal')
              <center id="text_loc"><h3>Please Select Disposal Date (if urgent) : </h3><br></center>
              @elseif($status2 == 'upload_doc_missing')
              <center id="text_loc"><h3>Please Upload SAP File : </h3><br></center>
              @elseif($status2 == 'upload_doc_missing_2')
              <center id="text_loc"><h3>Please Upload Approval SAP File : </h3><br></center>
              @else
              <h1 class="text-green"><i class="fa fa-check-circle"></i>&nbsp;{{ $message2 }}</h1>
              @endif
              @else
              @if ($status2 == 'reject')
              <h1 class="text-red"><i class="fa fa-times-circle"></i>&nbsp;{{ $message2 }}</h1>
              @elseif ($status2 == 'hold')
              <h1 class="text-blue"><i class="fa fa-exclamation-circle "></i>&nbsp;{{ $message2 }}</h1>
              @endif
              @endif
            </p>
            <br>
          </div>

          @if(isset($reg_form))
          <div style="text-align: center;">
            <form id="upload_form" method="post" enctype="multipart/form-data" autocomplete="off">
              <center>
                <input type="hidden" name="form_number" id="form_number" value="{{ $reg_form->id }}">
                <input type="hidden" name="file_name" id="file_name" value="{{ $reg_form->sap_file }}">
                <input type="file" name="sap_file" id="sap_file">
                <br>
                <button class="btn btn-success" onclick="loading()"><i class="fa fa-upload"></i> Upload</button>
              </center>
            </form>
          </div>
          @endif

          @if(!$status)
          <div style="text-align: center;">
            <center>
              <h1><i class="fa fa-file-text-o"></i> {{ $message }} </h1>
              @if($message == "Request Label Asset Form")
              <input type="hidden" id="id_form" value="{{ $asset->ids }}">
              @else
              <input type="hidden" id="id_form" value="{{ $asset->id }}">
              @endif
              <input type="hidden" id="stat2" value="{{ $status2 }}">
              <center id="text_comment"><h3>Give Comment to Applicant : </h3></center>
              <textarea id="comment" class="form-control" placeholder="Fill your comment here" style="width: 80%"></textarea>
              <br>
              <button class="btn btn-success" onclick="postComment()" id="btn_send"><i class="fa fa-send"></i> Send Comment</button>
            </center>
          </div>
          @endif

          @if(isset($loc_list) && $status2 == 'new_pic')
          <div style="text-align: center;">
            <form id="location_form" method="post" autocomplete="off" action="{{ url("approval/fixed_asset/disposal/new_pic") }}">
              <center>
                <input type="hidden" value="{{csrf_token()}}" name="_token">
                <input type="hidden" name="id" id="id" value="{{ $asset->id }}">
                <input type="hidden" name="form_number_loc" id="form_number_loc" value="{{ $asset->form_number }}">
                <input type="hidden" name="nama" id="nama" value="{{ $nama }}">
                <select class="form-control select2" name="location" id="location" data-placeholder='Select new Asset Location' style="width: 70%" required>
                  <option value=""></option>
                  @foreach($loc_list as $loc)
                  <option value="{{ $loc->location }}">{{ $loc->location }}</option>
                  @endforeach
                </select>
                <br>
                <button type="submit" class="btn btn-success" id="send_location" onclick="loading()"><i class="fa fa-check"></i> Approve</button>
              </center>
            </form>
          </div>
          @elseif(isset($loc_list) && $status2 == 'payment')
          <div style="text-align: center;">
            <form id="location_form" method="post" autocomplete="off" action="{{ url("approval/fixed_asset/disposal/payment") }}" enctype="multipart/form-data">
              <center>
                <input type="hidden" value="{{csrf_token()}}" name="_token">
                <input type="hidden" name="id" id="id" value="{{ $asset->id }}">
                <input type="hidden" name="form_number_payment" id="form_number_payment" value="{{ $asset->form_number }}">
                <input type="hidden" name="nama" id="nama" value="{{ $nama }}">
                <input type="file" name="payment_file" id="payment_file" required><br>
                <input type="text" name="retired_date2" id="retired_date2" class="form-control" placeholder="Select Retire Date" style="width: 30%" required>
                <br>
                <button type="submit" class="btn btn-success" id="payment_upload" onclick="loading()"><i class="fa fa-check"></i> Approve</button>
              </center>
            </form>
          </div>
          @elseif(isset($loc_list) && $status2 == 'disposal_date')
          <div style="text-align: center;">
            <form id="location_form" method="post" autocomplete="off" action="{{ url("approval/fixed_asset/disposal_scrap/retire_date") }}" enctype="multipart/form-data">
              <center>
                <input type="hidden" value="{{csrf_token()}}" name="_token">
                <input type="hidden" name="id" id="id" value="{{ $asset->id }}">
                <input type="hidden" name="form_number_dispo" id="form_number_dispo" value="{{ $asset->form_number }}">
                <input type="hidden" name="nama" id="nama" value="{{ $nama }}">
                <div class="input-group date" style="padding-left: 35%;">
                  <div class="input-group-addon bg-purple" style="border: none;">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" name="retired_date" id="retired_date" class="form-control" placeholder="Select Date" style="width: 40%" required>
                </div>
                <br>
                <button type="submit" class="btn btn-success" id="retire" onclick="loading()"><i class="fa fa-check"></i> Approve</button>
              </center>
            </form>
          </div>
          @elseif($status2 == 'manager_disposal')
          <div style="text-align: center;">
            <form id="disposal_form" method="post" autocomplete="off" action="{{ url("approval/fixed_asset/disposal/disposal_date") }}" enctype="multipart/form-data">
              <center>
                <input type="hidden" value="{{csrf_token()}}" name="_token">
                <input type="hidden" name="id" id="id" value="{{ $asset->id }}">
                <input type="hidden" name="form_number_disposal" id="form_number_disposal" value="{{ $asset->form_number }}">
                <input type="hidden" name="nama" id="nama" value="{{ $nama }}">
                <div class="input-group date" style="padding-left: 35%;">
                  <div class="input-group-addon bg-purple" style="border: none;">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" name="disposal_date" id="disposal_date" class="form-control" placeholder="Select Disposal Date (Optional)" style="width: 40%">
                </div>
                <br>
                <button type="submit" class="btn btn-success" id="disposal" onclick="loading()"><i class="fa fa-check"></i> Approve</button>
              </center>
            </form>
          </div>
          @elseif($status2 == 'upload_doc_missing')
          <div style="text-align: center;">
            <form id="location_form" method="post" autocomplete="off" action="{{ url("approval/fixed_asset/missing/document") }}" enctype="multipart/form-data">
              <center>
                <input type="hidden" value="{{csrf_token()}}" name="_token">
                <input type="hidden" name="id" id="id" value="{{ $asset->id }}">
                <input type="hidden" name="form_number_missing" id="form_number_missing" value="{{ $asset->form_number }}">
                <input type="hidden" name="nama" id="nama" value="{{ $nama }}">
                <input type="file" name="file" id="file" required><br>
                <input type="text" name="retired_date3" id="retired_date3" class="form-control" placeholder="Select Retire Date" style="width: 30%" required>
                <br>
                <button type="submit" class="btn btn-success" id="missing_upload" onclick="loading()"><i class="fa fa-check"></i> Approve</button>
              </center>
            </form>
          </div>
          @elseif($status2 == 'upload_doc_missing_2')
          <div style="text-align: center;">
            <form id="location_form" method="post" autocomplete="off" action="{{ url("approval/fixed_asset/missing/manager") }}" enctype="multipart/form-data">
              <center>
                <input type="hidden" value="{{csrf_token()}}" name="_token">
                <input type="hidden" name="id" id="id" value="{{ $asset->id }}">
                <input type="hidden" name="form_number_missing2" id="form_number_missing2" value="{{ $asset->form_number }}">
                <input type="hidden" name="nama" id="nama" value="{{ $nama }}">
                <input type="file" name="file" id="file" required><br>
                <br>
                <button type="submit" class="btn btn-success" id="missing_upload2" onclick="loading()"><i class="fa fa-check"></i> Approve</button>
              </center>
            </form>
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

      function loading() {
        $("#loading").show();
      }

      $('.select2').select2();

      $('#retired_date').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true
      });

      $('#retired_date2').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true
      });

      $('#retired_date3').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true
      });

      $('#disposal_date').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true
      });

      $("form#upload_form").submit(function(e) {
        e.preventDefault();    
        var formData = new FormData();
        formData.append('id_form', $("#form_number").val());
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
            openSuccessGritter('success', 'SAP File Successfully Uploaded');
            $("#upload_form").hide();
            $("#loading").hide();
          }
        })
      })

      function postComment() {
        $("#loading").show();
        var formData = new FormData();
        formData.append('id_form', $("#id_form").val());
        formData.append('stat2', $("#stat2").val());
        formData.append('position', "{{ $message }}");
        formData.append('nama', "{{ $nama }}");
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
            $("#loading").hide();

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
