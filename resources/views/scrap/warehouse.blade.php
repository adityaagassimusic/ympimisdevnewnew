@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
  overflow:hidden;
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
td{
  overflow:hidden;
  text-overflow: ellipsis;
}
.kedip {
  width: 50px;
  height: 50px;
  -webkit-animation: kedip 1s infinite;  /* Safari 4+ */
  -moz-animation: kedip 1s infinite;  /* Fx 5+ */
  -o-animation: kedip 1s infinite;  /* Opera 12+ */
  animation: kedip 1s infinite;  /* IE 10+, Fx 29+ */
}

@-webkit-keyframes kedip {
  0%, 49% {
    /*visibility: hidden;*/
    color: #ffff00;
    font-size: 15px;
    font-weight: bold;
  }
  50%, 100% {
    color: rgba(0,0,0,1);
    font-size: 15px;
    font-weight: bold;
    /*visibility: visible;*/
  }
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
  background-color: #ffd8b7;
}

.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
  background-color: #FFD700;
}
#loading, #error { display: none; }
</style>
@stop

@section('content')
<section class="content">
  <div class="row">
    <!-- ========== -->
    @if(($emp_dept->role_code == 'S-MIS') || ($emp_dept->role_code == 'OP-LOG') || ($emp_dept->username == 'PI9901011') || ($emp_dept->role_code == 'OP-QA'))
    <div class="col-xs-12">
      <div class="box" style="background-color: #ffffff">
       <div class="box-header">
        <h3 class="box-title">Scrap Warehouse</h3>
      </div>
      <div class="box-body" style="padding-bottom: 30px;">
        <div class="row">
          <div class="col-md-12">
            <div class="input-group col-md-8 col-md-offset-2">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                <i class="glyphicon glyphicon-barcode" style="size: 34px"></i>
              </div>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <input type="text" style="text-align: center; font-size: 30px; height: 75px" class="form-control" id="slip_scrap_number" placeholder="Scan Scrap Slip Here..." required>
              <div class="input-group-addon" id="icon-serial">
                <i class="glyphicon glyphicon-ok"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <!-- ====================== -->
</div>
<div class="col-md-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
  <div class="col-xs-2" style="padding-left: 0;">
    <div class="input-group date">
      <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
        <i class="fa fa-calendar"></i>
      </div>
      <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
    </div>
  </div>
  <div class="col-xs-2" style="padding-left: 0;">
    <div class="input-group date">
      <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
        <i class="fa fa-calendar"></i>
      </div>
      <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" onchange="fetchScrapDetail()">
    </div>
  </div> 
</div>

<div class="row">
  <div class="col-xs-12" style="padding-top: 1%;">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Scrap List Detail</a></li>
      </ul>
      <div class="tab-content" style="background-color: #ffffff">
        <div class="tab-pane active" id="tab_1">
          <table class="table table-hover table-striped table-bordered" id="tableResume">
            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
              <tr>
                <th style="width: 1%;">Slip</th>
                <th style="width: 4%;">Desc</th>
                <th style="width: 1%;">Category</th>
                <th style="width: 1%;">From Loc</th>
                <th style="width: 1%;">To Loc</th>
                <th style="width: 1%;">Qty</th>
                <th style="width: 1%;">Received At</th>
                <th style="width: 1%;">Info</th>
                <th style="width: 1%;">Reprint</th>
              </tr>
            </thead>
            <tbody id="tableBodyResume">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- <form id ="invoice" name="invoice" method="post" action="{{ url('invoice/scrap') }}"> -->
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="nav-tabs-custom tab-danger" align="center">
            <ul class="nav nav-tabs">
              <span>Input Invoice</span>
            </ul>
          </div>
          <div class="tab-content" align="center">
            <div class="tab-pane active">
              <div class="row" align="center">
                <div class="col-md-12" style="margin-bottom : 5px" align="center">
                  <input type="hidden" name="slip" id="slip">
                  <input type="text" class="form-control" id="category" name="category" required style="text-align: center;" placeholder="Masukkan No Invoice">                      
                </div>
                <div class="col-md-12" style="margin-bottom : 5px" align="center">
                  <button class="btn btn-success" onclick="InsertInvoice($('#slip').val())">Confirm</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- </form> -->
</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $("#slip_scrap_number").focus();
    fetchScrapDetail();
    $("#resume_closure").hide();
    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
      autoclose: true,
    });
    $('#tanggal').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
  })

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


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

  $('#slip_scrap_number').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {

      var number = $("#slip_scrap_number").val().replace(/[^A-Za-z0-9]/g, ' ');

      if(number.length >= 7){
        scanScrap();
        return false;
      }
      else{
        openErrorGritter('Error!', 'Nomor Slip Tidak Sesuai.');
        $("#slip_scrap_number").val('');
        audio_error.play();
      }
    }
  });

  function refreshTable(){;
    fetchScrapDetail();
  }

  function reset(){
    $('#slip_scrap_number').val("");
  }

  function reprintScrap(id){
    if(confirm("Apakah anda yakin akan mencetak ulang slip scrap ini?")){
      var data = {
        id:id,
        cat:'received'
      }
      $.get('<?php echo e(url("reprint/scrap")); ?>', data, function(result, status, xhr){
        if(result.status){
          openSuccessGritter('Success!', result.message);
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

  function scanScrap(){
    var number = $("#slip_scrap_number").val().replace(/[^A-Za-z0-9]/g, ' ');
    var data = {
      number : number
    }

    $.get('{{ url("scan/scrap_warehouse") }}', data,  function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
        audio_ok.play();
        $("#slip_scrap_number").val("");
        $("#slip_scrap_number").focus();
        fetchScrapDetail();
      }else{
        openErrorGritter('Error!', result.message);
        audio_error.play();
        $("#slip_scrap_number").val("");
        $("#slip_scrap_number").focus();
        fetchScrapDetail();
      }
    });
  }

  function fetchScrapDetail(){
    var data = {
      status : 2,
      date_from:$('#date_from').val(),
      date_to:$('#date_to').val()
    }

    $.get('<?php echo e(url("fetch/scrap_detail")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableResume').DataTable().clear();
        $('#tableResume').DataTable().destroy();
        var tableData = '';
        $('#tableBodyResume').html("");
        $('#tableBodyResume').empty();
        
        var count = 1;
        $.each(result.resumes, function(key, value) {
          if (value.remark == 'canceled') {
            tableData += '<tr class="kedip">';
            tableData += '<td  style="background-color: red">'+ value.order_no +'</td>';
            tableData += '<td  style="background-color: red">'+ value.material_description +'</td>';
            tableData += '<td  style="background-color: red">'+ value.category +'</td>';
            tableData += '<td  style="background-color: red">'+ value.issue_location +'</td>';
            tableData += '<td  style="background-color: red">'+ value.receive_location +'</td>';
            tableData += '<td  style="background-color: red">'+ value.quantity+' '+value.uom+'</td>';
            tableData += '<td  style="background-color: red">'+ value.created_at +'</td>';
            tableData += '<td  style="background-color: red">'+ value.remark +'</td>';
            tableData += '<td><center><button class="btn btn-primary btn-sm" onclick="reprintScrap('+value.id+')"><i class="fa fa-print"></i></button></center></td>';
            tableData += '</tr>';
          }else{
            tableData += '<tr>';
            tableData += '<td>'+ value.order_no +'</td>';
            tableData += '<td>'+ value.material_description +'</td>';
            tableData += '<td>'+ value.category +'</td>';
            tableData += '<td>'+ value.issue_location +'</td>';
            tableData += '<td>'+ value.receive_location +'</td>';
            tableData += '<td>'+ value.quantity+' '+value.uom+'</td>';
            tableData += '<td>'+ value.created_at +'</td>';
            tableData += '<td>'+ value.remark +'</td>';
            tableData += '<td><center><button class="btn btn-primary btn-sm" onclick="reprintScrap('+value.id+')"><i class="fa fa-print"></i></button></center></td>';    
            tableData += '</tr>';
          }
          count += 1;
        });

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

        // openSuccessGritter('Success!', result.message);
        $('#modalLocation').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function openModalCreate(slip){
    $('#slip').val(slip);
    $('#modalCreate').modal('show');
  }

  function InsertInvoice(slip){
    var data = {
      slip:slip,
      invoice:$('#category').val()
    }

    $.post('<?php echo e(url("invoice/scrap")); ?>', data, function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
        $('#modalCreate').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }
</script>

@stop