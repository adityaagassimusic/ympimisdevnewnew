@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    padding:  4px;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
  }
  #loading, #error { display: none; }

  #container1 {
    height: 400px;
  }

  .highcharts-figure,
  .highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
  }

  .highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
  }

  .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
  }

  .highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
  }

  .highcharts-data-table td,
  .highcharts-data-table th,
  .highcharts-data-table caption {
    padding: 0.5em;
  }

  .highcharts-data-table thead tr,
  .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
  }

  .highcharts-data-table tr:hover {
    background: #f1f7ff;
  }
</style>
@endsection

@section('header')
<section class="content-header" style="padding-bottom: 40px">
  <h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h1>
  <a href="{{ url('list/kategori/approval') }}" class="btn pull-right" style="margin-left: 5px; width: 15%; background-color: #34675C; color: white;"><i class="fa fa-file-text-o"></i> Edit Kategori<br>カテゴリを編集</a>
  <button class="btn pull-right" style="margin-left: 5px; width: 15%; background-color: rgb(126,86,134); color: white;" onclick="BuatFlow();"><i class="fa fa-list"></i> Buat Kategori<br>カテゴリーを作成する</button>
  <a href="{{ url('buat/dokumen/approval') }}" class="btn pull-right" style="margin-left: 5px; width: 15%; background-color: #3498db; color: white;"><i class="fa fa-file-text-o"></i> Form Pengajuan Dokumen<br>資料申請書</a>
  <!-- <button class="btn pull-left" style="width: 10%; background-color: #ccad00; color: white;" onclick="TestSendEmail()"><i class="fa fa-list"></i> Test Sent Email</button> -->
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 40%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <div class="row">
    <div class="col-xs-12" style="padding-top: 20px">
      <div style="background-color: #3f51b5;color: white;padding: 5px;text-align: center;">
        <span style="font-weight: bold; font-size: 30px">IN PROGRESS</span>
      </div>
    </div>

    <div class="col-xs-12" style="padding-top: 20px">
      <div class="box box-primary">
        <div class="box-header with-border">
          @if($role->role_code == 'S-MIS')
          <div class="col-xs-2" style="padding-top: 10px">
            <button class="btn pull-left" style="width: 100%; background-color: green; color: white;" id="show_all" onclick="ShowAll(this.id);"><i class="fa fa-list"></i> Lihat Semua</button>
            <!-- <button class="btn pull-left" style="margin-left: 5px; width: 10%; background-color: #FFC300; color: black;" onclick="Reload();"><i class="fa fa-list"></i> On Progress</button> -->
          </div>
          <div class="col-xs-2" style="padding-left: 0; padding-bottom: 10px; padding-top: 10px">
            <select class="form-control select4" id="pic_progress" name="pic_progress" data-placeholder="PIC On Progress" onchange="AllResume()">
              <option value="">&nbsp;</option>
              @foreach($pic as $pic)
              <option value="{{$pic->tanggungan}}">{{$pic->tanggungan}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xs-2" style="padding-top: 10px" align="center">
            <button class="btn btn-warning" style="width: 100%" onclick="Clear()"><i class="fa fa-eraser" aria-hidden="true"> Clear</i></button>
          </div>
          @endif
          <div class="col-xs-12" style="padding-top: 10px">
            <div class="box-body" style="padding-top: 0;" id="divTable"></div>
              <!-- <thead style="background-color: rgb(126,86,134); color: white" id="headMiraiApproval"> -->
            <!-- <table class="table table-hover table-striped table-bordered" id="tableResume">
                <thead style="background-color: rgb(126,86,134); color: white">
                  <tr>
                    <th style="text-align: center; width: 10%">Tanggal Pengajuan</th>
                    <th style="text-align: center; width: 20%">Nomor</th>
                    <th style="text-align: center; width: 10%">Dept.</th>
                    <th style="text-align: center; width: 20%">Judul & Pembuat Dokumen</th>
                    <th style="text-align: center; width: 30%">Progress Approval</th>
                    <th style="text-align: center; width: 10%">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tableBodyResume">
                </tbody>
                <tfoot>
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table> -->
            </div>
          </div>
        </div>
      </div>

    <div class="col-xs-12">
      <div style="background-color: #32a852;color: white;padding: 5px;text-align: center;">
        <span style="font-weight: bold; font-size: 30px">COMPLETED</span>
      </div>
    </div>

    <div class="col-xs-12" style="padding-top: 20px">
      <div class="box box-success">
        <div class="box-header with-border">
          <div class="col-xs-4" style="padding-bottom: 10px" align="center">
            <label>Dari Bulan</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="dari_bulan" data-placeholder="Select Month">
            </div>
          </div>
          <div class="col-xs-4" style="padding-bottom: 10px" align="center">
            <label>Sampai Bulan</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="sampai_bulan" data-placeholder="Select Month">
            </div>
          </div>
          <div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
            <button class="btn btn-primary" style="width: 100%" onclick="Completed()"><i class="fa fa-search" aria-hidden="true"> Search</i></button>
          </div>
          <div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
            <button class="btn btn-warning" style="width: 100%" onclick="Clear()"><i class="fa fa-eraser" aria-hidden="true"> Clear</i></button>
          </div>

          <div class="col-xs-12" style="padding-top: 10px">
            <table class="table table-hover table-striped table-bordered" id="tableResumeClose">
              <!-- <thead style="background-color: rgb(126,86,134); color: white" id="headMiraiApprovalClose"> -->
              <thead style="background-color: rgb(126,86,134); color: white">
                <tr>
                  <th style="text-align: center; width: 10%">Tanggal Pengajuan</th>
                  <th style="text-align: center; width: 20%">Nomor</th>
                  <th style="text-align: center; width: 10%">Dept.</th>
                  <th style="text-align: center; width: 20%">Judul & Pembuat Dokumen</th>
                  <th style="text-align: center; width: 30%">Progress Approval</th>
                  <th style="text-align: center; width: 10%">Aksi</th>
                </tr>
              </thead>
              <tbody id="tableBodyResumeClose">
              </tbody>
              <tfoot>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="modalCreate" data-keyboard="false">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <center>
          <h3 style="background-color: rgb(126,86,134); font-weight: bold; padding: 3px; margin-top: 0; color: white;">
            Buat Kategori<br>
          </h3>
        </center>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
          <form id ="importForm" name="importForm" method="post" action="{{ url('adagio/home/create') }}" >
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="col-md-12" style="margin-bottom : 5px">
              <span>Departemen (課)</span>
              <select class="form-control select6" id="app_dpt" name="app_dpt" data-placeholder='Pilih Category' style="width: 100%">
                <option value="">&nbsp;</option>
                @foreach($dept as $row)
                <option value="{{$row->department_name}}">{{$row->department_name}}</option>
                @endforeach
              </select>                      
            </div>  
            <div class="col-md-12" style="margin-bottom : 5px">
              <span>Judul Dokumen (資料名)</span>
              <!-- <textarea rows="2" class="form-control" id="judul" name="judul" required></textarea>               -->
              <input type="text" class="form-control" id="judul" name="judul" required>              
            </div>
            <div class="col-md-12" style="margin-bottom : 5px">
              <span>Judul Dokumen (Japanese) (資料名（日本語))<span style="color: red; font-size: 13px">*Wajib diisi, translate kategori dokumen ke interpreter.</span></span>
              <!-- <textarea rows="2" class="form-control" id="jd_japan" name="jd_japan" required></textarea>                -->
              <input type="text" class="form-control" id="jd_japan" name="jd_japan" required>
            </div>
            <div class="col-xs-12" style="margin-bottom : 5px">
              <div class="col-xs-7" style="padding-left: 0">
                <span>Urutan Approval (承認順番)</span>
              </div>
              <div class="col-xs-3" style="padding-left: 0">
                <span>Ket. (備考)</span>
              </div>
            </div>
            <div class="col-xs-12" style="margin-bottom : 5px">
              <input type="text" name="lop" id="lop" value="1" hidden>
              <div class="col-xs-7" style="padding-left: 0">
                <select class="form-control select2" id="description1" name="description1" data-placeholder='Pilih Nama' style="width: 100%" required>
                  <option value="">&nbsp;</option>
                  @foreach($user as $row)
                  <option value="{{$row->employee_id}}/{{$row->name}}/{{$row->position}}">{{$row->employee_id}} - {{$row->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-3" style="padding-left: 0">
                <select class="form-control select2" id="header1" name="header1" data-placeholder='Pilih Header' style="width: 100%" required>
                  <option value="">&nbsp;</option>
                  <option value="Created by/(作られた)">Created by</option>
                  <option value="Checked by/(チェック済み)">Checked by</option>
                  <!-- <option value="Accept by/(承認)">Accept by</option> -->
                  <option value="Approved by/(承認)">Approved by</option>
                  <option value="Known by/(承知)">Known by</option>
                  <option value="Prepared by/(準備)">Prepared by</option>
                  <option value="Received by/(が受信した)">Received by</option>
                </select>
              </div>
              <div class="col-xs-2" style="padding-left: 0">
                <button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
              </div>  
            </div>
            <div id="tambah"></div>
            <div class="col-md-12">
              <br>
              <button class="btn btn-success pull-right" type="submit">Simpan (保存)</button>
            </div>
          </form>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
  var no = 2;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('#datefrom').datepicker({
      autoclose: true,
      todayHighlight: true
    });
    $('.select2').select2({
      dropdownParent: $('#modalCreate'),
      allowClear : true,
    });

    $('.select3').select2({
      allowClear : true,
    });
    $('.select4').select2({
      allowClear : true,
    });
    $('.select5').select2({
      allowClear : true,
    });
    $('.select6').select2({
      allowClear : true,
    });
    $('.select7').select2({
      allowClear : true,
    });

    $('#app_dpt').val('{{$employee->department}}').trigger('change');
    fillChart();
    // OutStanding();
    // Completed();
    AllResume();
    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
      autoclose: true,
    });

    $('#dari_bulan').datepicker({
      format: 'yyyy-mm',
      viewMode: "months",
      minViewMode: "months",
      todayHighlight: true,
      autoclose: true,
    });
    $('#sampai_bulan').datepicker({
      format: 'yyyy-mm',
      viewMode: "months",
      minViewMode: "months",
      todayHighlight: true,
      autoclose: true,
    });
  });

  $("form#importForm").submit(function(e) {
    $("#loading").show();
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

  function Clear(){
    location.reload();
  }

  function OutStanding(no_transaction){
    var select = 'OutStanding';

    var data = {
      status : 2,
      no_transaction:no_transaction,
      pic_progress:$('#pic_progress').val(),
      select:select
    }

    $.get('<?php echo e(url("adagio/data/resume")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableResume').DataTable().clear();
        $('#tableResume').DataTable().destroy();
        $('#tableBodyResume').html("");
        // $('#headMiraiApproval').html("");
        $('#footMiraiApproval').html("");
        var tableData = '';
        // var headMiraiApproval = '';
        var footMiraiApproval = '';
// $('#tableBodyResume').empty();
// fillChart();
var count = 1;

// headMiraiApproval += '<tr>';
// headMiraiApproval += '<th style=" text-align: center">Tanggal Pengajuan</th>';
// headMiraiApproval += '<th style=" text-align: center">Nomor</th>';
// headMiraiApproval += '<th style=" text-align: center">Dept.</th>';
// headMiraiApproval += '<th style=" text-align: center">Judul & Pembuat Dokumen</th>';
// headMiraiApproval += '<th style=" text-align: center">Progress Approval</th>';
// headMiraiApproval += '<th style=" text-align: center">Aksi</th>';
// headMiraiApproval += '</tr>';

footMiraiApproval += '<tr>';
footMiraiApproval += '<th style="text-align:center"></th>';
footMiraiApproval += '<th style="text-align:center"></th>';
footMiraiApproval += '<th style="text-align:center"></th>';
footMiraiApproval += '<th style="text-align:center"></th>';
footMiraiApproval += '<th style="text-align:center"></th>';
footMiraiApproval += '<th style="text-align:center"></th>';
footMiraiApproval += '</tr>';


$.each(result.resumes, function(key, value) {

  var identitas = value.nik.split("/");
  var sendmail = '{{ url("adagio/sendmail/") }}';
  var report = '{{ url("adagio/done/report")}}';
  var urldelete = '{{ url("adagio/data/delete/") }}';

  var name = value.name.split(',');
  var status = value.status.split(',');
  var approved_at = value.approved_at.split(',');
  var approver_id = value.approver_id.split(',');
  var time = getFormattedTime(new Date(value.created_at));


  tableData += '<tr>';
  tableData += '<td style=" text-align: center">'+ value.date +'<br>'+ time +'</td>';
  tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'</td>';
  tableData += '<td style=" text-align: center">'+ value.department_shortname +'</td>';
  tableData += '<td style=" text-align: left">'+ value.description +'<br>'+ identitas[1] +'</td>';
  tableData += '<td style=" text-align: left">';
  tableData += '<ol>';
  for(var i = 0; i < name.length; i++){
    if(status[i] == ''){
      tableData += '<li style="color: #e53935;">';
      tableData += '<a target="_blank" style="color: red;" href="{{ url('adagio/view/sign') }}/'+value.no_transaction+'/'+approver_id[i]+'">';
      tableData += name[i]+' (Waiting)';
      tableData += '</a>';
      tableData += '</li>';
    }
    else if(status[i] == 'Approved'){
      tableData += '<li style="color: green;">';
      tableData += name[i]+' (Approved '+approved_at[i]+')';
      tableData += '</li>';
    }
    else{
      tableData += '<li style="color: green;">';
      tableData += name[i]+' ('+status[i]+' '+approved_at[i]+')';
      tableData += '</li>';
    }

  }
  tableData += '</ol>';
  tableData += '</td>';
  tableData += '<td style=" text-align: center;">';
  if ((value.created_by == result.user.id) || (result.role == 'MIS')) {
    if (value.remark == 'Belum Kirim Email') {
      tableData += '<a onclick="sendEmail(\''+value.no_transaction+'\')" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Mail</a><br>';
      tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br>';
      tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><br>';
    }else if (value.remark == 'Open'){
      tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br>';
      tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br>';
    }
  }
  else if ((result.role == 'MIS') && (value.remark == 'Open')) {
    tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br>';
    tableData += '<a href="'+report+'/'+value.no_transaction+'" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><br>';
    tableData += '<a onclick="CreatePdfUlang(\''+value.no_transaction+'\')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Create Ulang"><i class="fa fa-file-pdf-o"></i> Create Ulang PDF</a>';
  }
  else{
    tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><br>';
  }
  tableData += '</td>';
  tableData += '</tr>';

  count += 1;
});

$('#tableBodyResume').append(tableData);
// $('#headMiraiApproval').append(headMiraiApproval);
$('#footMiraiApproval').append(footMiraiApproval);

$('#tableResume tfoot th').each( function () {
  var title = $(this).text();
  $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
} );

var table = $('#tableResume').DataTable({
  'dom': 'Bfrtip',
  'responsive':true,
  'lengthMenu': [
  [ 5, 10, 25, -1 ],
  [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
  'pageLength': 15,
  'searching': true,
  'ordering': true,
  'order': [],
  'info': true,
  'autoWidth': true,
  "sPaginationType": "full_numbers",
  "bJQueryUI": true,
  "bAutoWidth": false,
  "processing": true
});

table.columns().every( function () {
  var that = this;
  $( '#search', this.footer() ).on( 'keyup change', function () {
    if ( that.search() !== this.value ) {
      that
      .search( this.value )
      .draw();
    }
  } );
} );

$('#tableResume tfoot tr').appendTo('#tableResume thead');
// $('#modalLocation').modal('hide');
}
else{
  openErrorGritter('Error!', result.message);
}
});
}

function getFormattedTime(date) {
  var year = date.getFullYear();

  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
  ];

  var month = date.getMonth();

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;

  var hour = date.getHours();
  if (hour < 10) {
    hour = "0" + hour;
  }



  var minute = date.getMinutes();
  if (minute < 10) {
    minute = "0" + minute;
  }
  var second = date.getSeconds();

  // return day + '-' + monthNames[month] + '-' + year +' '+ hour +':'+ minute +':'+ second;
  return hour +':'+ minute +':'+ second;
}

function ShowAll(id){
  $("#loading").show();
  var data = {
    status : 2,
    select:id
  }

  $.get('<?php echo e(url("adagio/data/resume")); ?>', data, function(result, status, xhr){
    if(result.status){
      $("#loading").hide();
      $('#tableResume').DataTable().clear();
      $('#tableResume').DataTable().destroy();
      $('#tableBodyResume').html("");
      // $('#headMiraiApproval').html("");
      // $('#footMiraiApproval').html("");
      // var headMiraiApproval = '';
      // var footMiraiApproval = '';
      // fillChart();
      var count = 1;
      var tableData = '';

      // headMiraiApproval += '<tr>';
      // headMiraiApproval += '<th style=" text-align: center">Tanggal Pengajuan</th>';
      // headMiraiApproval += '<th style=" text-align: center">Nomor</th>';
      // headMiraiApproval += '<th style=" text-align: center">Dept.</th>';
      // headMiraiApproval += '<th style=" text-align: center">Judul & Pembuat Dokumen</th>';
      // headMiraiApproval += '<th style=" text-align: center">Progress Approval</th>';
      // headMiraiApproval += '<th style=" text-align: center">Aksi</th>';
      // headMiraiApproval += '</tr>';

      // footMiraiApproval += '<tr>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '</tr>';
      initiateTable();

      $.each(result.resumes, function(key, value) {
        // console.log(value.remark, result.user.username);
        var identitas = value.nik.split("/");
        var sendmail = '{{ url("adagio/sendmail/") }}';
        var report = '{{ url("adagio/done/report")}}';
        var urldelete = '{{ url("adagio/data/delete/") }}';

        var name = value.name.split(',');
        var status = value.status.split(',');
        var approved_at = value.approved_at.split(',');
        var approver_id = value.approver_id.split(',');
        var time = getFormattedTime(new Date(value.created_at));
        var tanggapan = '{{ url("adagio/tanggapan/hold/") }}';
      



        tableData += '<tr>';
        tableData += '<td style=" text-align: center">'+ value.date +'<br>'+ time +'</td>';
        tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'<br><span class="label label-info" style="color: white">'+value.remark+'</span></td>';
        tableData += '<td style=" text-align: center">'+ value.department_shortname +'</td>';
        tableData += '<td style=" text-align: left">'+ value.description +'<br>'+ identitas[1] +'</td>';
        tableData += '<td style=" text-align: left">';
        tableData += '<ol>';
        for(var i = 0; i < name.length; i++){
          if(status[i] == ''){
            tableData += '<li style="color: #e53935;">';
            tableData += '<a target="_blank" style="color: red;" href="{{ url('adagio/view/sign') }}/'+value.no_transaction+'/'+approver_id[i]+'">';
            tableData += name[i]+' (Waiting)';
            tableData += '</a>';
            tableData += '</li>';
          }
          else if(status[i] == 'Approved'){
            tableData += '<li style="color: green;">';
            tableData += name[i]+' (Approved '+approved_at[i]+')';
            tableData += '</li>';
          }
          else{
            tableData += '<li style="color: green;">';
            tableData += name[i]+' ('+status[i]+' '+approved_at[i]+')';
            tableData += '</li>';
          }

        }
        tableData += '</ol>';
        tableData += '</td>';
        tableData += '<td style=" text-align: center;">';
        if (value.created_by == result.user.id) {
          if (value.remark == 'Belum Kirim Email') {
            tableData += '<a onclick="sendEmail(\''+value.no_transaction+'\')" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Mail</a><br><br>';
            tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br><br>';
          }else if (value.remark == 'Open'){
            if (result.user.role_code == 'S-MIS') {
              tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-warning btn-md"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br><br>';
              tableData += '<a onclick="CreatePdfUlang(\''+value.no_transaction+'\')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Create Ulang"><i class="fa fa-file-pdf-o"></i> Create Ulang PDF</a><br><br>';
              tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br><br>';
            }else{
              tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-warning btn-md"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br><br>';
            }
          }else if (value.remark == 'Send Aplicant Hold & Comment') {
            tableData += '<a href="'+tanggapan+'/'+value.no_transaction+'" target="_blank" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-clipboard"></i> Klik Untuk Menanggapi</a><br><br>';
          }
          tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-info btn-md"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Dokumen</a><br><br>';
        }
        else if (result.user.role_code == 'S-MIS') {
          tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-warning btn-md"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br><br>';
          tableData += '<a href="'+report+'/'+value.no_transaction+'" class="btn btn-info btn-md"  data-toggle="tooltip" title="Dokumen"><i class="fa fa-file-pdf-o"></i> Dokumen</a><br><br>';
          tableData += '<a onclick="CreatePdfUlang(\''+value.no_transaction+'\')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Create Ulang"><i class="fa fa-file-pdf-o"></i> Create Ulang PDF</a><br><br>';
          tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br><br>';
        }
        else{
          tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-info btn-md"  data-toggle="tooltip" title="Dokumen"><i class="fa fa-file-pdf-o"></i> Dokumen</a>';
        }
        tableData += '</td>';
        tableData += '</tr>';

        count += 1;
      });

      $('#tableBodyResume').append(tableData);

      $('#tableResume tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
        } );

      var table = $('#tableResume').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 5, 10, 25, -1 ],
        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          ]
        },
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        'ordering' :false,
        initComplete: function() {
          this.api()
          .columns([2])
          .every(function(dd) {
            var column = this;
            var theadname = $("#tableResume th").eq([dd]).text();
            var select = $(
              '<select style="width: 100%; color: black;"><option value="" style="font-size:11px;">All</option></select>'
              )
            .appendTo($(column.footer()).empty())
            .on('change', function() {
              var val = $.fn.dataTable.util.escapeRegex($(this).val());

              column.search(val ? '^' + val + '$' : '', true, false)
              .draw();
            });
            column
            .data()
            .unique()
            .sort()
            .each(function(d, j) {
              var vals = d;
              if ($("#tableResume th").eq([dd]).text() == 'Category') {
                vals = d.split(' ')[0];
              }
              select.append('<option style="font-size:11px;" value="' +
                d + '">' + vals + '</option>');
            });
          });
        },
      });

      table.columns().every( function () {
          var that = this;
          $( '#search', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          } );
        } );

        $('#tableResume tfoot tr').appendTo('#tableResume thead')
      // $('#modalLocation').modal('hide');
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });
}

function AllResume(no_transaction){
  $("#loading").show();
  var select = 'AllResume';

  var data = {
    status : 2,
    no_transaction:no_transaction,
    pic_progress:$('#pic_progress').val(),
    select:select
  }

  $.get('<?php echo e(url("adagio/data/resume")); ?>', data, function(result, status, xhr){
    if(result.status){
      $("#loading").hide();
      $('#tableResume').DataTable().clear();
      $('#tableResume').DataTable().destroy();
      $('#tableBodyResume').html("");
      // $('#headMiraiApproval').html("");
      // $('#footMiraiApproval').html("");
      // var headMiraiApproval = '';
      // var footMiraiApproval = '';
      // fillChart();
      var tableData = '';
      var count = 1;

      // headMiraiApproval += '<tr>';
      // headMiraiApproval += '<th style=" text-align: center">Tanggal Pengajuan</th>';
      // headMiraiApproval += '<th style=" text-align: center">Nomor</th>';
      // headMiraiApproval += '<th style=" text-align: center">Dept.</th>';
      // headMiraiApproval += '<th style=" text-align: center">Judul & Pembuat Dokumen</th>';
      // headMiraiApproval += '<th style=" text-align: center">Progress Approval</th>';
      // headMiraiApproval += '<th style=" text-align: center">Aksi</th>';
      // headMiraiApproval += '</tr>';

      // footMiraiApproval += '<tr>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '<th style="text-align:center"></th>';
      // footMiraiApproval += '</tr>';

      initiateTable();

      $.each(result.resumes, function(key, value) {
        // console.log(value.remark, result.user.username);
        var identitas = value.nik.split("/");
        var sendmail = '{{ url("adagio/sendmail/") }}';
        var report = '{{ url("adagio/done/report")}}';
        var urldelete = '{{ url("adagio/data/delete/") }}';

        var name = value.name.split(',');
        var status = value.status.split(',');
        var approved_at = value.approved_at.split(',');
        var approver_id = value.approver_id.split(',');
        var time = getFormattedTime(new Date(value.created_at));
        var tanggapan = '{{ url("adagio/tanggapan/hold") }}';
      



        tableData += '<tr>';
        tableData += '<td style=" text-align: center">'+ value.date +'<br>'+ time +'</td>';
        tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'<br><span class="label label-info" style="color: white">'+value.remark+'</span></td>';
        tableData += '<td style=" text-align: center">'+ value.department_shortname +'</td>';
        tableData += '<td style=" text-align: left">'+ value.description +'<br>'+ identitas[1] +'</td>';
        tableData += '<td style=" text-align: left">';
        tableData += '<ol>';
        for(var i = 0; i < name.length; i++){
          if(status[i] == ''){
            tableData += '<li style="color: #e53935;">';
            tableData += '<a target="_blank" style="color: red;" href="{{ url('adagio/view/sign') }}/'+value.no_transaction+'/'+approver_id[i]+'">';
            tableData += name[i]+' (Waiting)';
            tableData += '</a>';
            tableData += '</li>';
          }
          else if(status[i] == 'Approved'){
            tableData += '<li style="color: green;">';
            tableData += name[i]+' (Approved '+approved_at[i]+')';
            tableData += '</li>';
          }
          else{
            tableData += '<li style="color: green;">';
            tableData += name[i]+' ('+status[i]+' '+approved_at[i]+')';
            tableData += '</li>';
          }

        }
        tableData += '</ol>';
        tableData += '</td>';
        tableData += '<td style=" text-align: center;">';
        if (value.created_by == result.user.id) {
          if (value.remark == 'Belum Kirim Email') {
            tableData += '<a onclick="sendEmail(\''+value.no_transaction+'\')" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Mail</a><br><br>';
            tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br><br>';
          }else if (value.remark == 'Open'){
            tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br><br>';
            tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br><br>';
          }else if (value.remark == 'Send Aplicant Hold & Comment') {
            tableData += '<a href="'+tanggapan+'/'+value.no_transaction+'" target="_blank" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-clipboard"></i> Klik Untuk Menanggapi</a><br><br>';
          }
          tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Dokumen</a><br><br>';
        }
        else if (result.user.role_code == 'S-MIS') {
          tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br><br>';
          tableData += '<a href="'+report+'/'+value.no_transaction+'" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Dokumen"><i class="fa fa-file-pdf-o"></i> Dokumen</a><br><br>';
          tableData += '<a onclick="CreatePdfUlang(\''+value.no_transaction+'\')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Create Ulang"><i class="fa fa-file-pdf-o"></i> Create Ulang PDF</a><br>';
          tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br><br>';
        }
        else{
          tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Dokumen"><i class="fa fa-file-pdf-o"></i> Dokumen</a><br><br>';
        }
        tableData += '</td>';
        tableData += '</tr>';

        count += 1;
      });

      $('#tableResume').append(tableData);

      $('#tableResume tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
      } );

      var table = $('#tableResume').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
          [ 5, 10, 25, -1 ],
          [ '5 rows', '10 rows', '25 rows', 'Show all' ]
          ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          ]
        },
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        'ordering' :false,
        initComplete: function() {
          this.api()
          .columns([2])
          .every(function(dd) {
            var column = this;
            var theadname = $("#tableResume th").eq([dd]).text();
            var select = $(
              '<select style="width: 100%; color: black;"><option value="" style="font-size:11px;">All</option></select>'
              )
            .appendTo($(column.footer()).empty())
            .on('change', function() {
              var val = $.fn.dataTable.util.escapeRegex($(this).val());

              column.search(val ? '^' + val + '$' : '', true, false)
              .draw();
            });
            column
            .data()
            .unique()
            .sort()
            .each(function(d, j) {
              var vals = d;
              if ($("#tableResume th").eq([dd]).text() == 'Category') {
                vals = d.split(' ')[0];
              }
              select.append('<option style="font-size:11px;" value="' +
                d + '">' + vals + '</option>');
            });
          });
        },
      });

      table.columns().every( function () {
        var that = this;
        $( '#search', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#tableResume tfoot tr').appendTo('#tableResume thead');

      // $('#modalLocation').modal('hide');
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });
}

function Completed(no_transaction){
  $("#loading").show();
  var dari_bulan = $("#dari_bulan").val();
  var sampai_bulan = $("#sampai_bulan").val();

  var select = 'Completed';

  var data = {
    status : 2,
    no_transaction:no_transaction,
    pic_progress:$('#pic_progress').val(),
    select:select,
    dari_bulan : dari_bulan,
    sampai_bulan : sampai_bulan
  }

  $.get('<?php echo e(url("adagio/data/resume")); ?>', data, function(result, status, xhr){
    if(result.status){
      $("#loading").hide();

      $('#tableResumeClose').DataTable().clear();
      $('#tableResumeClose').DataTable().destroy();

      $('#tableBodyResumeClose').html("");
      // $('#footMiraiApprovalClose').html("");

      var tableData = '';
      // var ftMiraiApprovalClose = '';
      var count = 1;

      // $('#headMiraiApprovalClose').html("");
      // var headMiraiApprovalClose = '';

      // headMiraiApprovalClose += '<tr>';
      // headMiraiApprovalClose += '<th style=" text-align: center">Tanggal Pengajuan</th>';
      // headMiraiApprovalClose += '<th style=" text-align: center">Nomor</th>';
      // headMiraiApprovalClose += '<th style=" text-align: center">Dept.</th>';
      // headMiraiApprovalClose += '<th style=" text-align: center">Judul & Pembuat Dokumen</th>';
      // headMiraiApprovalClose += '<th style=" text-align: center">Progress Approval</th>';
      // headMiraiApprovalClose += '<th style=" text-align: center">Aksi</th>';
      // headMiraiApprovalClose += '</tr>';

      // ftMiraiApprovalClose += '<tr>';
      // ftMiraiApprovalClose += '<th style="text-align:center"></th>';
      // ftMiraiApprovalClose += '<th style="text-align:center"></th>';
      // ftMiraiApprovalClose += '<th style="text-align:center"></th>';
      // ftMiraiApprovalClose += '<th style="text-align:center"></th>';
      // ftMiraiApprovalClose += '<th style="text-align:center"></th>';
      // ftMiraiApprovalClose += '<th style="text-align:center"></th>';
      // ftMiraiApprovalClose += '</tr>';


      $.each(result.resumes, function(key, value) {

        var identitas = value.nik.split("/");
        var sendmail = '{{ url("adagio/sendmail/") }}';
        var report = '{{ url("adagio/done/report")}}';
        var urldelete = '{{ url("adagio/data/delete/") }}';

        var name = value.name.split(',');
        var status = value.status.split(',');
        var approved_at = value.approved_at.split(',');
        var approver_id = value.approver_id.split(',');
        var time = getFormattedTime(new Date(value.created_at));
      

        tableData += '<tr>';
        tableData += '<td style=" text-align: center">'+ value.date +'<br>'+ time +'</td>';
        if (value.remark == 'Open') {
        tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'<br><span class="label label-danger" style="color: white">IN PROGRESS</span></td>';
        }else if (value.remark == 'Close') {
        tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'<br><span class="label label-success" style="color: white">COMPLETED</span></td>';
        }else if (value.remark == 'Rejected') {
        tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'<br><span class="label label-danger" style="color: white">REJECTED</span></td>';
        }

        tableData += '<td style=" text-align: center">'+ value.department_shortname +'</td>';
        tableData += '<td style=" text-align: left">'+ value.description +'<br>'+ identitas[1] +'</td>';
        tableData += '<td style=" text-align: left">';
        tableData += '<ol>';
        for(var i = 0; i < name.length; i++){
          if(status[i] == ''){
            tableData += '<li style="color: #e53935;">';
            tableData += '<a target="_blank" style="color: red;" href="{{ url('adagio/view/sign') }}/'+value.no_transaction+'/'+approver_id[i]+'">';
            tableData += name[i]+' (Waiting)';
            tableData += '</a>';
            tableData += '</li>';
          }
          else if(status[i] == 'Approved'){
            tableData += '<li style="color: green;">';
            tableData += name[i]+' (Approved '+approved_at[i]+')';
            tableData += '</li>';
          }
          else if(status[i] == 'Rejected'){
            tableData += '<li style="color: red;">';
            tableData += name[i]+' ('+status[i]+' '+approved_at[i]+')';
            tableData += '</li>';
          }
          else{
            tableData += '<li style="color: green;">';
            tableData += name[i]+' ('+status[i]+' '+approved_at[i]+')';
            tableData += '</li>';
          }

        }
        tableData += '</ol>';
        tableData += '</td>';
        tableData += '<td style=" text-align: center;">';
        tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-info btn-md"  data-toggle="tooltip" title="Dokumen"><i class="fa fa-file-pdf-o"></i> Dokumen</a>';
        tableData += '</td>';
        tableData += '</tr>';

        count += 1;
      });

      $('#tableBodyResumeClose').append(tableData);

      $('#tableResumeClose tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
        } );
      // $('#headMiraiApprovalClose').append(headMiraiApprovalClose);
      // $('#footMiraiApprovalClose').append(ftMiraiApprovalClose);

      var table = $('#tableResumeClose').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 5, 10, 25, -1 ],
        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          ]
        },
        // 'paging': true,
        // 'lengthChange': true,
        // 'pageLength': 10,
        // 'searching': true,
        // 'ordering': true,
        // 'order': [],
        // 'info': true,
        // 'autoWidth': true,
        // "sPaginationType": "full_numbers",
        // "bJQueryUI": true,
        // "bAutoWidth": false,
        // "processing": true
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        'ordering' :true,
        initComplete: function() {
          this.api()
          .columns([2])
          .every(function(dd) {
            var column = this;
            var theadname = $("#tableResumeClose th").eq([dd]).text();
            var select = $(
              '<select style="width: 100%; color: black;"><option value="" style="font-size:11px;">All</option></select>'
              )
            .appendTo($(column.footer()).empty())
            .on('change', function() {
              var val = $.fn.dataTable.util.escapeRegex($(this).val());

              column.search(val ? '^' + val + '$' : '', true, false)
              .draw();
            });
            column
            .data()
            .unique()
            .sort()
            .each(function(d, j) {
              var vals = d;
              if ($("#tableResumeClose th").eq([dd]).text() == 'Category') {
                vals = d.split(' ')[0];
              }
              select.append('<option style="font-size:11px;" value="' +
                d + '">' + vals + '</option>');
            });
          });
        },
      });

      table.columns().every( function () {
          var that = this;
          $( '#search', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          } );
        } );

        $('#tableResumeClose tfoot tr').appendTo('#tableResumeClose thead');

      // table.columns().every( function () {
      //   var that = this;
      //   $( '.search', this.footer() ).on( 'keyup change', function () {
      //     if ( that.search() !== this.value ) {
      //       that
      //       .search( this.value )
      //       .draw();
      //     }
      //   } );
      // } );

      // $('#tableResumeClose tfoot tr').appendTo('#tableResumeClose thead');
      // $('#modalLocation').modal('hide');
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });
}

function CreatePdfUlang(no_transaction){
  if(confirm("Apakah anda yakin akan create PDF Ulang?")){
    var data = {
      no_transaction:no_transaction
    }
    $("#loading").show();

    $.get('{{ url("adagio/create/ulang") }}', data, function(result, status, xhr){
      openSuccessGritter('Success!', result.message);
      $("#loading").hide();
      audio_ok.play();
    });
  }
  else{
    return false;
  }
}

function Confirm(){
  if (confirm()) {

  }
  else{
    return false;
  }
}

function openSuccessGritter(title, message){
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-success',
    image: '{{ url("images/image-screen.png") }}',
    sticky: false,
    time: '4000'
  });
}

function openErrorGritter(title, message) {
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-danger',
    image: '{{ url("images/image-stop.png") }}',
    sticky: false,
    time: '4000'
  });
}

function initiateTable() {
    $('#divTable').html("");
    var tableData = "";
    tableData += '<table class="table table-hover table-striped table-bordered" id="tableResume">';
    tableData += '<thead style="background-color: rgb(126,86,134); color: white">';
    tableData += '<tr>';
    tableData += '<th style="text-align: center; width: 10%">Tanggal Pengajuan</th>';
    tableData += '<th style="text-align: center; width: 20%">Nomor</th>';
    tableData += '<th style="text-align: center; width: 10%">Dept.</th>';
    tableData += '<th style="text-align: center; width: 20%">Judul & Pembuat Dokumen</th>';
    tableData += '<th style="text-align: center; width: 30%">Progress Approval</th>';
    tableData += '<th style="text-align: center; width: 10%">Aksi</th>';
    tableData += '</tr>';
    tableData += '</thead>';
    tableData += '<tbody id="tableBodyResume">';
    tableData += "</tbody>";
    tableData += "<tfoot>";
    tableData += "<tr>";
    tableData += "<th></th>";
    tableData += "<th></th>";
    tableData += "<th></th>";
    tableData += "<th></th>";
    tableData += "<th></th>";
    tableData += "<th></th>";
    tableData += "</tr>";
    tableData += "</tfoot>";
    tableData += "</table>";
    $('#divTable').append(tableData);
  }


function Home(){
  $("#buat_approval").hide();
  $("#form_input").hide();
  $("#tunjangan_keluarga").hide();
}

function BuatApproval(){
  $("#buat_approval").show();
  $("#form_input").hide();
  $("#tunjangan_keluarga").hide();
}

function BuatDokumen(){
  $("#form_input").show();
  $("#buat_approval").hide();
  $("#tunjangan_keluarga").hide();
}

function add_item() {
  var bodi = "";
  var loop_tp = "";
  var employee_id_tp = "";
  var in_out_tp = "";
  var tanggal_tp = "";
  var keterangan_tp = "";

  employee_id_tp += "<option value=''></option>";
  in_out_tp += "<option value=''></option>";
  keterangan_tp += "<option value=''></option>";


  bodi += '<tr id="'+no+'" class="item">';

  bodi += '<td>';
  bodi += '<input type="text" name="loop_tp_'+no+'" id="loop_tp_'+no+'" value="coba'+no+'" hidden>';
  bodi += '<select class="form-control select2" id="employee_id_tp_'+no+'" name="employee_id_tp_'+no+'" data-placeholder="Pilih NIK Atau Nama" style="width: 100%"><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>';
  bodi += '</td>';

  bodi += '<td>';
  bodi += '<select class="form-control select2" id="in_out_tp_'+no+'" name="employee_id_tk_'+no+'" data-placeholder="IN / OUT" style="width: 100%"><option value="">&nbsp;</option><option value="IN">IN</option><option value="OUT">OUT</option></select>';
  bodi += '</td>';

  bodi += '<td>';
  bodi += '<div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker" id="tanggal_'+no+'" name="tanggal_'+no+'" placeholder="Received Date"></div>';
  bodi += '</td>';

  bodi += '<td>';
  bodi += '<input type="text" class="form-control" id="keterangan_'+no+'" name="employee_id_tk_'+no+'">'
  bodi += '</td>';

  bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';

  bodi += '</tr>';

  $("#body_add").append(bodi);

  $.each(arr_loop_tp, function(index, value){
    loop_tp += "<option value='"+value+"'>"+value+"</option>";
  })

  $.each(arr_employee_id_tp, function(index, value){
    employee_id_tp += "<option value='"+value+"'>"+value+"</option>";
  })

  $.each(arr_in_out_tp, function(index, value){
    in_out_tp += "<option value='"+value+"'>"+value+"</option>";
  })

  $.each(arr_tanggal_tp, function(index, value){
    tanggal_tp += "<option value='"+value+"'>"+value+"</option>";
  })

  $.each(arr_keterangan_tp, function(index, value){
    keterangan_tp += "<option value='"+value+"'>"+value+"</option>";
  })

  no++;
  $('.select2').select2({
    allowClear : true
  });

  $(".datepicker").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true,
  });
}

function save_item() {
  arr_params = [];

  $('.item').each(function(index, value) {
    var ido = $(this).attr('id');
    arr_params.push({'employee_id_tp' : $("#employee_id_tp_"+ido).val(), 'in_out_tp' : $("#in_out_tp_"+ido).val(), 'tanggal_tp' : $("#tanggal_"+ido).val(), 'keterangan_tp' : $("#keterangan_"+ido).val(), 'loop_tp' : $("#loop_tp_"+ido).val()});
  });

  var data = {
    item : arr_params,
    department_tp : $('#department_tp').val(),
    section_tp : $('#section_tp').val().split('_')[0],
    bulan_tp : $('#bulan_tp').val()
  }

  $.post('{{ url("human_resource/add/uang_pekerjaan") }}', data, function(result, status, xhr) {
// openSuccessGritter('Success','Pengajuan Disimpan');
location.reload(true);
})
}

function delete_item(no) {
  $("#"+no).remove();
}

function Reload() {
  AllResume();
}

function BuatFlow(){
  $('#modalCreate').modal('show');
}

function tambah(id,lop) {
  var id = id;
  var lop = "";
  if (id == "tambah"){
    lop = "lop";
  }else{
    lop = "lop2";
  }
  var divdata = "";
  divdata += "<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'>";
  divdata += "<div class='col-xs-7' style='padding-left:0;'>";
  divdata += "<select class='form-control select7' id='description"+no+"' name='description"+no+"' data-placeholder='Pilih Nama' style='width: 100%'>";
  divdata += "<option value=''>&nbsp;</option>@foreach($user as $row)<option value='{{$row->employee_id}}/{{$row->name}}/{{$row->position}}'>{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>";
  divdata += "</div>";

  divdata += "<div id='divheader_"+no+"' class='col-xs-3' style='padding-left:0;'>";
  divdata += "<select class='form-control select7' id='header"+no+"' name='header"+no+"' data-placeholder='Pilih Header' style='width: 100%'>";
  divdata += "<option value=''>&nbsp;</option>";
  divdata += "<option value='Created by/(作られた)'>Created by</option>";
  divdata += "<option value='Checked by/(チェック済み)'>Checked by</option>";
  divdata += "<option value='Accept by/(承認)'>Accept by</option>";
  divdata += "<option value='Approved by/(承認)''>Approved by</option>";
  divdata += "<option value='Known by/(承知)'>Known by</option>";
  divdata += "<option value='Prepared by/(準備)'>Prepared by</option>";
  divdata += "<option value='Received by/(が受信した)'>Received by</option>";
  divdata += "</select>";
  divdata += "</div>";


  divdata += "<div class='col-xs-2' style='padding:0;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button>";
  divdata += "<button type='button' onclick='tambah(\""+id+"\",\""+lop+"\");' class='btn btn-success'><i class='fa fa-plus' ></i></button>";
  divdata += "</div>";
  divdata += "</div>";

  $("#"+id).append(divdata);
  document.getElementById(lop).value = no;
  no+=1;
  $('.select7').select2({
    dropdownParent : $("#modalCreate"),
    tags : true
  });

}

function kurang(elem,lop) {
  var lop = lop;
  var ids = $(elem).parent('div').parent('div').attr('id');
  var oldid = ids;
  $(elem).parent('div').parent('div').remove();
  var newid = parseInt(ids) + 1;
  jQuery("#"+newid).attr("id",oldid);
  jQuery("#divheader_"+newid).attr("id",oldid);

  jQuery("#description"+newid).attr("name","description"+oldid);
  jQuery("#header"+newid).attr("name","header"+oldid);

  jQuery("#description"+newid).attr("id","description"+oldid);
  jQuery("#header"+newid).attr("id","header"+oldid);

  no-=1;
  var a = no -1;

  for (var i =  ids; i <= a; i++) { 
    var newid = parseInt(i) + 1;
    var oldid = newid - 1;
    jQuery("#"+newid).attr("id",oldid);
    jQuery("#description"+newid).attr("name","description"+oldid);
    jQuery("#header"+newid).attr("name","header"+oldid);

    jQuery("#description"+newid).attr("id","description"+oldid);
    jQuery("#header"+newid).attr("id","header"+oldid);
  }

  document.getElementById(lop).value = a;
}

function selectType(type){
  var tipe = type;
  var data = {
    cat:tipe
  }

  $.get('<?php echo e(url("adagio/data/approval")); ?>', data, function(result, status, xhr){
    if(result.status){
      $('#tableList').DataTable().clear();
      $('#tableList').DataTable().destroy();
      $('#tableBodyList').html("");
      $('#tableBodyList').empty();

      var tableData = '';
      var count = 1;
      $.each(result.lists, function(key, value) {

        var identitas = value.user.split("/");

        tableData += '<tr>';
        tableData += '<td>'+ count +'</td>';
        tableData += '<td>'+ value.category +'</td>';
        tableData += '<td>'+ identitas[1] +'</td>';
        tableData += '<td>'+ identitas[2] +'</td>';
        tableData += '</tr>';

        count += 1;
      });

      $('#tableBodyList').append(tableData);

      openSuccessGritter('Success!', result.message);
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });

}

function selectResume(no_transaction){
  var select = 'OutStanding';

  var data = {
    status : 2,
    no_transaction:no_transaction,
    dpt:$('#dpt').val(),
    stt:$('#stt').val(),
    nm:$('#nm').val(),
    pic_progress:$('#pic_progress').val(),
    date_to:$('#date_to').val(),
    select:select
  }

  $.get('<?php echo e(url("adagio/data/resume")); ?>', data, function(result, status, xhr){
    if(result.status){
      $('#tableResume').DataTable().clear();
      $('#tableResume').DataTable().destroy();
      var tableData = '';
      $('#tableBodyResume').html("");
      $('#tableBodyResume').empty();
      fillChart();

      var count = 1;

$.each(result.resumes, function(key, value) {

  var identitas = value.nik.split("/");
  var sendmail = '{{ url("adagio/sendmail/") }}';
// var report = '{{ url("adagio/data/report/") }}';
var report = '{{ url("adagio/done/report")}}';
var urldelete = '{{ url("adagio/data/delete/") }}';

var name = value.name.split(',');
var status = value.status.split(',');
var approved_at = value.approved_at.split(',');
var approver_id = value.approver_id.split(',');
var time = getFormattedTime(new Date(value.created_at));

tableData += '<tr>';
tableData += '<td style=" text-align: left">'+ value.department_shortname +'</td>';
tableData += '<td style=" text-align: left">No. Approval '+ value.no_transaction +'<br>No. Dokumen '+ value.no_dokumen +'</td>';
tableData += '<td style=" text-align: left">'+ value.department_shortname +'</td>';
// tableData += '<td style=" text-align: left; width: 0.1%;">'+ value.no_dokumen +'</td>';
// tableData += '<td style=" text-align: left; width: 2%;">'+ value.judul +'</td>';
tableData += '<td style=" text-align: left">'+ value.description +'<br>'+ identitas[1] +'</td>';
// tableData += '<td>'+ value.remark +'</td>';
// tableData += '<td style=" text-align: left; width: 1%;">'+ identitas[1] +'</td>';
// tableData += '<td>'+ value.description +'</td>';
// tableData += '<td>'+ value.date +'</td>';
tableData += '<td style=" text-align: left">';
tableData += '<ol>';
for(var i = 0; i < name.length; i++){
  if(status[i] == ''){
    tableData += '<li style="color: #e53935;">';
    tableData += '<a target="_blank" style="color: red;" href="{{ url('adagio/view/sign') }}/'+value.no_transaction+'/'+approver_id[i]+'">';
    tableData += name[i]+' (Waiting)';
    tableData += '</a>';
    tableData += '</li>';
  }
  else if(status[i] == 'Approved'){
    tableData += '<li style="color: green;">';
    tableData += name[i]+' (Approved '+approved_at[i]+')';
    tableData += '</li>';
  }
  else{
    tableData += '<li style="color: green;">';
    tableData += name[i]+' ('+status[i]+' '+approved_at[i]+')';
    tableData += '</li>';
  }

}
tableData += '</ol>';
tableData += '</td>';
tableData += '<td style=" text-align: center;">';
if (value.created_by == result.user.id) {
  if (value.remark == 'Belum Kirim Email') {
    tableData += '<a onclick="sendEmail(\''+value.no_transaction+'\')" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Mail</a><br>';
    tableData += '<a onclick="deleteFile('+value.id+')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Hapus</a><br>';
  }else if (value.remark == 'Open'){
    tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br>';
  }
  tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><br>';
}
else if ((result.role == 'MIS') && (value.remark == 'Open')) {
  tableData += '<a onclick="resendMail(\''+value.no_transaction+'\')" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Kirim Ulang Email</a><br>';
  tableData += '<a href="'+report+'/'+value.no_transaction+'" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><br>';
}
else{
  tableData += '<a href="'+report+'/'+value.no_transaction+'" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><br>';
}
tableData += '</td>';
tableData += '</tr>';

count += 1;
});
$('#tableResume tfoot th').each( function () {
  var title = $(this).text();
  $(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
} );

$('#tableBodyResume').append(tableData);
var tableResume = $('#tableResume').DataTable({
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
    }
    ]
  },
  'paging': true,
  'lengthChange': true,
  'pageLength': 10,
  'searching': true,
  'ordering': true,
  'order': [],
  'info': true,
  'autoWidth': true,
  "sPaginationType": "full_numbers",
  "bJQueryUI": true,
  "bAutoWidth": false,
  "processing": true
});
tableResume.columns().every( function () {
  var that = this;
  $( '#search', this.footer() ).on( 'keyup change', function () {
    if ( that.search() !== this.value ) {
      that
      .search( this.value )
      .draw();
    }
  } );
} );

$('#tableResume tfoot tr').appendTo('#tableResume thead');

// openSuccessGritter('Success!', result.message);
// $('#modalLocation').modal('hide');
}
else{
  openErrorGritter('Error!', result.message);
}
});
}

function resendMail(no_transaction){
  if(confirm("Apakah anda yakin akan mengirim ulang email?")){
    $("#loading").show();
    $.get('{{ url("adagio/sendmail") }}/'+no_transaction, function(result, status, xhr){
      openSuccessGritter('Success!', result.message);
      $("#loading").hide();
      audio_ok.play();
    });
  }
  else{
    return false;
  }
}

function sendEmail(no_transaction){
  if(confirm("Apakah anda yakin akan mengirim email?")){
    $("#loading").show();
    $.get('{{ url("adagio/sendmail") }}/'+no_transaction, function(result, status, xhr){
      openSuccessGritter('Success!', result.message);
      $("#loading").hide();
      audio_ok.play();
    });
  }
  else{
    return false;
  }
}

function clearConfirmation(){
  location.reload(true);    
}

function deleteFile(id){

  if(confirm("Apa anda yakin akan menghapus file pengajuan ini?")){
    var data = {
      id:id
    }
// console.log(no_transaction)
$.post('{{ url("delete/file/approval") }}', data, function(result, status, xhr){
  if(result.status){
// var loc = $('#location').val(); 

// fetchResume(loc);
openSuccessGritter('Success!', result.message);
// Completed();
    AllResume();
// selectResume();
// console.log(result);
}
else{
  openErrorGritter('Error!', result.message);
}

});
}
else{
  return false;
}
}



function fillChart() {
  $("#loading").show();

  var data = {
    status : 2,
    dpt:$('#dpt').val(),
    stt:$('#stt').val(),
    nm:$('#nm').val(),
    date_to:$('#date_to').val()
  }

  $.get('{{ url("adagio/data/resume") }}',data, function(result, status, xhr) {
    if(xhr.status == 200){
      if(result.status){


        $("#loading").hide();
        var dept = [];

        var jml_sudah = [];
        var jml_belum = [];

        var sudah = 0;
        var belum = 0;

        var series = []
        var series2 = [];

        var jml_rendah = 0;
        var jml_sedang = 0;
        var jml_tinggi = 0;

        for (var i = 0; i < result.survey.length; i++) {
          dept.push(result.survey[i].department_shortname);

          sudah = sudah+parseInt(result.survey[i].sudah);
          belum = belum+parseInt(result.survey[i].belum);

          jml_sudah.push(parseInt(result.survey[i].sudah));
          jml_belum.push(parseInt(result.survey[i].belum));

          series.push([dept[i], jml_sudah[i]]);
          series2.push([dept[i], jml_belum[i]]);
        }

        var colors = ['#32a852', '#a83232'];

        // Highcharts.chart('container1', {
        //   chart: {
        //     type: 'column',
        //     options3d: {
        //       enabled: true,
        //       alpha: 15,
        //       beta: 0,
        //       depth: 50,
        //       viewDistance: 50
        //     }
        //   },
        //   title: {
        //     text: 'Mirai Sign'
        //   },
        //   xAxis: {
        //     categories: dept,
        //     type: 'category',
        //     gridLineWidth: 1,
        //     gridLineColor: 'RGB(204,255,255)',
        //     lineWidth:2,
        //     lineColor:'#9e9e9e',

        //     labels: {
        //       style: {
        //         fontSize: '13px'
        //       }
        //     },
        //   },yAxis: [{
        //     title: {
        //       text: 'Total',
        //       style: {
        //         color: '#eee',
        //         fontSize: '15px',
        //         fontWeight: 'bold',
        //         fill: '#6d869f'
        //       }
        //     },
        //     labels:{
        //       style:{
        //         fontSize:"15px"
        //       }
        //     },
        //     type: 'linear',
        //     opposite: true
        //   },
        //   ],
        //   tooltip: {
        //     headerFormat: '<span>{series.name}</span><br/>',
        //     pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
        //   },
        //   legend: {
        //     layout: 'horizontal',
        //     backgroundColor:
        //     Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
        //     itemStyle: {
        //       fontSize:'10px',
        //     },
        //     enabled: true,
        //     reversed: true
        //   },  
        //   plotOptions: {
        //     series:{
        //       cursor: 'pointer',
        //       point: {
        //         events: {
        //           click: function () {
        //             ShowModal(this.category,this.series.name);
        //           }
        //         }
        //       },
        //       animation: false,
        //       dataLabels: {
        //         enabled: true,
        //         format: '{point.y}',
        //         style:{
        //           fontSize: '1vw'
        //         }
        //       },
        //       animation: false,
        //       pointPadding: 0.93,
        //       groupPadding: 0.93,
        //       borderWidth: 0.93,
        //       cursor: 'pointer'
        //     },
        //   },credits: {
        //     enabled: false
        //   },
        //   colors:colors,
        //   series: [{
        //     data: series2,
        //     name: 'Open',
        //     showInLegend: false
        //   }]
        // });
      }
    }
  });
}


function TestSendEmail(){
  $.get('<?php echo e(url("adagio/cek/kirim_email")); ?>', function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
}

</script>

@endsection