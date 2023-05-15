@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
  thead>tr>th{
    text-align:center;
    overflow:hidden;
    padding: 3px;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    text-align: center;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }

  .content-wrapper {
    padding-top: 0px !important;
  }

  #loading {
    display: none
  }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    {{ $title }}
    <small><span class="text-purple"> {{ $title_jp }}</span></small>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: white; top: 45%; left: 50%;">
      <span style="font-size: 40px">Loading... <i class="fa fa-spinner fa-spin" style="font-size: 80px;"></i></span>
    </p>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">

          <div class="col-xs-8">
            <div style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 100%;" id="attach_pdf">
            </div>
          </div>


          <div class="col-xs-4" style="padding-right: 0; padding-left: 0; margin-bottom: 2%; border-radius: 5px" id="this_area">
            <div class="input-group input-group-lg" style="border: 1px solid black;">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
                <i class="fa fa-user"></i>
              </div>
              <input type="text" class="form-control" placeholder="TAP ID CARD HERE" id="item_scan">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
                <i class="fa fa-user"></i>TAP
              </div>
            </div>
            <br>
            <input type="hidden" id="code">

            <table class="table" width="100%">
              <tr>
                <td style="padding:0"><input type="text" id="op_id" class="form-control" placeholder="Employee ID" readonly></td>
                <td style="padding:0"><input type="text" id="op_name" class="form-control" placeholder="Employee Name" readonly></td>
                <input type="hidden" id="id_sosialisasi" class="form-control" value="{{$accident->id}}">
              </tr>
            </table>

            <table class="table table-bordered" id="tableList">
              <thead style="background-color: rgba(126,86,134); color: white">
                <tr>
                  <th>EMPLOYEE ID</th>
                  <th>EMPLOYEE NAME</th>
                  <th>SCAN TIME</th>
                </tr>
              </thead>
              <tbody id="body_history">
              </tbody>
            </table>

          </div>

        </div>
      </div>
    </div>
  </div>
</section>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('.datepicker').datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true
    });

    $("#op_name").val("");
    $("#op_id").val("");
    $("#item_scan").focus();
    getHistory();

    var path = "{{$file_path}}";
    $('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
    
  });


$('#item_scan').keydown(function(event) {
  if (event.keyCode == 13 || event.keyCode == 9) {
    if ($("#item_scan").val() != "") {
      checkCode($("#item_scan").val());
    } else {
      $("#op_name").val("");
      $("#op_id").val("");
      audio_error.play();
      openErrorGritter('Error', 'Invalid CARD');
    }
  }
});

function checkCode(param) {
  var data = {
    tag : param
  }

  $.get('{{ url("fetch/general/oxymeter/employee") }}', data, function(result, status, xhr){
    if (result.status) {
      $("#op_name").val(result.datas.name);
      $("#op_id").val(result.datas.employee_id);
      audio_ok.play();
      save();
      // openSuccessGritter('Success', '');
    } else {
      audio_error.play();
      openErrorGritter('Error', result.message);
    }

    $('#item_scan').val("");
  })
}

function save() {
  data = {
    id : $("#id_sosialisasi").val(),
    employee_tag : $("#item_scan").val(),
    employee_id : $("#op_id").val(),
    name : $("#op_name").val(),
  }

  $.post('{{ url("post/kecelakaan/sosialisasi") }}', data, function(result, status, xhr){
    if (result.status) {
      $("#loading").hide();

      // $("#op_name").val("");
      // $("#op_id").val("");
      audio_ok.play();
      openSuccessGritter('Success', 'Data Berhasil Disimpan');
      $("#item_scan").focus();

      getHistory();
    } else {
      audio_error.play();
      openErrorGritter('Error', result.message);
    }
  })
}

function getHistory() {
  var data = {
    username : "{{ Auth::user()->username }}",
    id : $("#id_sosialisasi").val(),
  }

  $.get('{{ url("fetch/kecelakaan_history") }}', data, function(result, status, xhr){
    $("#body_history").empty();
    $('#tableList').DataTable().clear();
    $('#tableList').DataTable().destroy();
    var body = "";

    $.each(result.datas, function(index, value){
      body += "<tr>";
      body += "<td>"+value.employee_id+"</td>";
      body += "<td>"+value.name+"</td>";
      body += "<td>"+value.updated_at+"</td>";
      body += "</tr>";
    })

    $("#body_history").append(body);

    var table = $('#tableList').DataTable({
      'dom': 'Bfrtip',
      'responsive':true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "aaSorting": [[ 0, "desc" ]]
    });
  })
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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
@endsection