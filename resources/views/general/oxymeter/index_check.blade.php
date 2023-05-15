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
    <li>
      <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success" style="color:white"><i class="fa fa-upload"></i>Upload Oxymeter</a>
    </li>
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
          <div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%; border: 1px solid #222f3e; border-radius: 5px" id="this_area">
            <div class="input-group input-group-lg">
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
                <td><input type="text" id="op_id" class="form-control" placeholder="Employee ID" readonly></td>
                <td><input type="text" id="op_name" class="form-control" placeholder="Employee Name" readonly></td>
                <td><input type="text" id="oxy_val" class="form-control" placeholder="Oxygen Rate" readonly></td>
                <td><input type="text" id="heart_val" class="form-control" placeholder="Heart Rate" readonly></td>
              </tr>
              <tr>
                <th colspan="4" style="background-color: #7e5686; color: white"><center>Oxygen Meter :</center></th>
              </tr>
              <tr>
                <td colspan="4">
                  <table id="tombol" style="display: none; width: 100%">                    
                  </table>
                </td>
              </tr>
              <tr>
                <th colspan="4" style="background-color: #7e5686; color: white"><center>Pulse Rate :</center></th>
              </tr>
              <tr>
                <td colspan="4" id="tombol_group" style="display: none">
                  <button class='btn btn-apps btn-primary' style='width: 19%; font-size: 20pt; font-weight: bold' onclick="tampilbutton(71,80)">71 - 80</button>
                  <button class='btn btn-apps btn-primary' style='width: 19%; font-size: 20pt; font-weight: bold' onclick="tampilbutton(81,90)">81 - 90</button>
                  <button class='btn btn-apps btn-primary' style='width: 19%; font-size: 20pt; font-weight: bold' onclick="tampilbutton(91,100)">91 - 100</button>
                  <button class='btn btn-apps btn-primary' style='width: 19%; font-size: 20pt; font-weight: bold' onclick="tampilbutton(101,110)">101 - 110</button>
                  <button class='btn btn-apps btn-primary' style='width: 19%; font-size: 20pt; font-weight: bold' onclick="tampilbutton(111,120)">111 - 120</button>
                </td>
              </tr>
              <tr>
                <td colspan="4">
                  <table width="100%" id="tombol2" style="display: none; width: 100%">                    
                  </table>
                </td>
              </tr>
            </table>
          </div>

          <div class="col-xs-12">
            <table class="table table-bordered" id="tableList">
              <thead style="background-color: rgba(126,86,134); color: white">
                <tr>
                  <th>SCAN TIME</th>
                  <th>EMPLOYEE ID</th>
                  <th>EMPLOYEE NAME</th>
                  <th>OXYGEN RATE</th>
                  <th>PULSE RATE</th>
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

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Upload Cek Oximeter</h4>
        Format : [<b><i>NIK Karyawan</i></b>][<b><i>Kadar Oksigen</i></b>][<b><i>Denyut Nadi</i></b>]
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-10 col-xs-offset-1">
            <div class="col-xs-12">
              <label>Tanggal Cek :<span class="text-red">*</span></label>
            </div>
            <div class="col-xs-6" style="padding-right: 0px;">
              <input type="text" class="form-control" name="date" id="date" placeholder="Enter Tanggal Pengecekan" value="{{ date('Y-m-d') }}" readonly>  
            </div>
            
            <div class="col-xs-12" style="margin-top: 2%;">
              <label>Oximeter Data :<span class="text-red">*</span></label>
            </div>
            <div class="col-xs-12">
              <textarea id="upload" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
            </div>
          </div>
        </div>    
      </div>
      <div class="modal-footer">
        <div class="row" style="margin-top: 7%; margin-right: 2%;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button onclick="uploadOximeter()" class="btn btn-success">Upload </button>
        </div>
      </div>
    </div>
  </div>
</div>


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

    $("#item_scan").focus();
    $("#op_name").val("");
    $("#op_id").val("");
    $("#oxy_val").val("");
    $("#heart_val").val("");
    getHistory();

    
  });

  function writeTombol() {
    $("#tombol").show();
    $("#tombol").empty();
    tombol = "";

    var z = 1;
    for (var i = 80; i <= 100; i++) {
     if (i == 80) {
      tombol += "<tr>";
    } 

    if (z == 12) {
      tombol += "</tr>";
      tombol += "<tr>";

      tombol += "<td style='padding: 1vw;'><button onclick='save("+i+", \"oxygen\")' class='btn btn-apps btn-primary' style='width: 100%; font-size: 20pt; font-weight: bold'>"+i+"</button></td>";
      z = 1;
    } else {
      tombol += "<td style='padding: 1vw;'><button onclick='save("+i+", \"oxygen\")' class='btn btn-apps btn-primary' style='width: 100%; font-size: 20pt; font-weight: bold'>"+i+"</button></td>";
    }


    z++;
  }

  $("#tombol").append(tombol);
  $("#tombol_group").show();
}

function tampilbutton(min, max) {
  $("#tombol2").show();
  $("#tombol2").empty();
  tombol2 = "";

  var x = 1;
  for (var i = min; i <= max; i++) {
   if (i == min) {
    tombol2 += "<tr>";
  } 
  tombol2 += "<td style='padding: 1vw;'><button onclick='save("+i+", \"heart\")' class='btn btn-apps btn-primary' style='width: 100%; font-size: 20pt; font-weight: bold'>"+i+"</button></td>";

  if (i == max) {
    tombol2 += "</tr>";
  } 

  // if (x == 11) {
  //   tombol2 += "</tr>";
  //   tombol2 += "<tr>";

  //   tombol2 += "<td style='padding: 1vw;'><button onclick='save("+i+", \"heart\")' class='btn btn-apps btn-primary' style='width: 100%; font-size: 20pt; font-weight: bold'>"+i+"</button></td>";
  //   x = 1;
  // } else {
  //   tombol2 += "<td style='padding: 1vw;'><button onclick='save("+i+", \"heart\")' class='btn btn-apps btn-primary' style='width: 100%; font-size: 20pt; font-weight: bold'>"+i+"</button></td>";
  // }


  x++;
}

$("#tombol2").append(tombol2);
}

$('#item_scan').keydown(function(event) {
  if (event.keyCode == 13 || event.keyCode == 9) {
    if ($("#item_scan").val() != "") {
      checkCode($("#item_scan").val());
      $("#oxy_val").val("");
      $("#heart_val").val("");
    } else {
      $("#tombol").hide();
      $("#op_name").val("");
      $("#op_id").val("");
      $("#oxy_val").val("");
      $("#heart_val").val("");
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
      openSuccessGritter('Success', '');
      writeTombol();
    } else {
      audio_error.play();
      openErrorGritter('Error', result.message);
    }

    $('#item_scan').val("");
  })
}

function save(val, ctg) {
  var emp = $("#op_id").val();

  if (ctg == 'heart') {
    $("#loading").show();
  }

  data = {
    employee_id : $("#op_id").val(),
    name : $("#op_name").val(),
    value : val,
    ctg : ctg
  }

  $.post('{{ url("post/general/oxymeter") }}', data, function(result, status, xhr){
    if (result.status) {
      $("#loading").hide();
      $("#tombol2").empty();
      $("#tombol2").hide();

      // $("#op_name").val("");
      // $("#op_id").val("");
      if (ctg == 'oxygen') {
        if (val < 95) {
          audio_error.play();
          openErrorGritter('Warning', 'Kadar Oksigen Dibawah 95');
        } else{
          audio_ok.play();
          openSuccessGritter('Success', 'Data has been Saved');
        }
        $("#oxy_val").val(val);
      } else {
        $("#heart_val").val(val);
        $("#tombol_group").hide();
        $("#tombol").hide();
        audio_ok.play();
        openSuccessGritter('Success', 'Data has been Saved');
      }

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
    // limit : 5,
    dt : "{{ date('Y-m-d') }}"
  }

  $.get('{{ url("fetch/general/oxymeter/history") }}', data, function(result, status, xhr){
    $("#body_history").empty();
    $('#tableList').DataTable().clear();
    $('#tableList').DataTable().destroy();
    var body = "";

    $.each(result.datas, function(index, value){
      body += "<tr>";
      body += "<td>"+value.updated_at+"</td>";
      body += "<td>"+value.employee_id+"</td>";
      body += "<td>"+value.name+"</td>";
      body += "<td>"+value.remark+"</td>";
      body += "<td>"+(value.remark2 || '')+"</td>";
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

function uploadOximeter() {
  var date = $('#date').val();
  var upload = $('#upload').val();

  var data = {
    date : date,
    upload : upload,
    username : "{{ Auth::user()->username }}",
  }

  $('#loading').show();
  $.post('{{ url("upload/general/oxymeter") }}', data, function(result, status, xhr){
    if(result.status){
      $('#upload').val('');

      $('#uploadModal').modal('hide');

      $('#loading').hide();
      openSuccessGritter('Success', 'Upload Oximeter Success');
      getHistory();
    }else {
      $('#loading').hide();
      openErrorGritter('Error', result.message);
    }

  });
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