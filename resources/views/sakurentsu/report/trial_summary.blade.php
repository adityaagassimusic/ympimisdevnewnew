@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
  #loading { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }}</span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@endsection

@section('content')
<section class="content">
  @if (session('success'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('success') }}
  </div>
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>
  @endif

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-header">
          <h3 class="box-title"><span class="text-purple">Filter Data</span></h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Issue. Date From</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right date_picker" id="datefrom" placeholder="Select Date From">
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label>Issue. Date To</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right date_picker" id="dateto" placeholder="Select Date To">
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label>Sakurentsu Number</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <input type="text" class="form-control" id="sakurentsu_number" placeholder="Input Sakurentsu Number">
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label>Trial Request Number</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <input type="text" class="form-control" id="trial_number" placeholder="Input Trial Request Number">
                </div>
              </div>
            </div>

             <div class="col-md-3">
              <div class="form-group">
                <label>Department to</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <select class="form-control pull-right select2" id="department" data-placeholder='Select Department Trial'>
                    <option value=""></option>
                    @foreach($department as $dpt)
                    <option value="{{ $dpt->department }}">{{ $dpt->department }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">  
                <label>Trial Subject</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <input type="text" class="form-control" id="subject_trial" placeholder="Input Subject Trial">
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Created By</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <select class="form-control pull-right select2" id="pic" data-placeholder='Select PIC' >
                    <option value=""></option>
                    @foreach($employee as $emp)
                    <option value="{{ $emp->employee_id }}">{{ $emp->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <button class="btn btn-primary pull-right" onclick="search()" style="margin-bottom: 5px"><i class="fa fa-search"></i> Filter</button>
            </div>

            <div class="col-xs-12">
              <table id="master_table" class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 1%">ID</th>
                    <th style="width: 1%">Form Number</th>
                    <th style="width: 1%">Sakurentsu Number</th>
                    <th style="width: 1%">Issue Date</th>
                    <th style="width: 7%">Department to</th>
                    <th style="width: 2%">Created By</th>
                    <th>Subject</th>
                    <th style="width: 1%">Status</th>
                    <th style="width: 1%">Report</th>
                  </tr>
                </thead>
                <tbody id="master_body">
                </tbody>
              </table>
            </div>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('.date_picker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true
    });
  });

  $(function () {
    $('.select2').select2({
      allowClear: true
    });
  })

  function search() {
    $("#loading").show();

    var data = {
      datefrom : $("#datefrom").val(),
      dateto : $("#dateto").val(),
      sakurentsu_number : $("#sakurentsu_number").val(),
      trial_number : $("#trial_number").val(),
      subject_trial : $("#subject_trial").val(),
      department : $("#department").val(),
      pic : $("#pic").val(),
    };


    $.get('{{ url("fetch/sakurentsu/summary/trial") }}', data, function(result, status, xhr){
      if (result.status) {
        $("#loading").hide();

        $('#master_table').DataTable().clear();
        $('#master_table').DataTable().destroy();
        $("#master_body").empty();
        body = "";


        $.each(result.datas, function(key, value) {   
         body += "<tr>";
         body += "<td>"+value.id+"</td>";
         body += "<td>"+value.form_number+"</td>";
         body += "<td>"+(value.sakurentsu_number || '')+"</td>";
         body += "<td>"+value.issue_date+"</td>";
         body += "<td>"+value.department+"</td>";
         body += "<td>"+value.name+"</td>";
         body += "<td>"+value.subject+"</td>";
         body += "<td>"+value.status+"</td>";

         url = "{{ url('uploads/sakurentsu/trial_req/report') }}/Report_"+value.form_number+".pdf";

         body += "<td><a href='"+url+"' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Report</a></td>";
         body += "</tr>";

       });

        $("#master_body").append(body);

        var table = $('#master_table').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 25, 50, -1 ],
          [ '25 rows', '50 rows', 'Show all' ]
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
          "order": [[ 0, 'asc' ]]
        });     
      } else {
        $("#loading").hide();
        openErrorGritter('Error', result.message);
      }
    });
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
    audio_error.play();
  }

</script>

@stop