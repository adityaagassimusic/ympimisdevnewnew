@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
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
td {
  overflow:hidden;
  text-overflow: ellipsis;
}
#tableBodyResult > tr:hover {
  cursor: pointer;
  background-color: #7dfa8c;
}

#tableBodyHasil > tr:hover {
  cursor: pointer;
  background-color: #7dfa8c;
}
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    <b>Checklist Point Check & Hasil Audit AEO</b>
  </h1>
  <ol class="breadcrumb">
    <?php 
      if (strpos(strtolower($employee->position), 'operator') !== false) {

      } else { ?>
      
        <a class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create-modal">
          <i class="fa fa-plus"></i>&nbsp;&nbsp;Tambah Point Check
        </a>


        <a href="{{ url("index/audit_aeo")}}" class="btn btn-warning btn-sm" style="color:white;float: right;margin-right: 5px"><i class="fa fa-plus"></i> Lakukan {{ $page }} </a>

      
      <?php 
      }
     ?>
  </ol>
</section>
@endsection

@section('content')
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>
  @endif
  <div class="row">
    <div class="col-xs-4">
      <div class="box">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
           <div class="box-header" style="padding-right:0;padding-left: 0;padding-top: 0;">
              <span style="font-size:20px;font-weight: bold;">Point Check Audit AEO</span>
           </div>
          <!-- <div class="box-body" style="overflow-x: scroll;"> -->
            <table id="tableResult" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>Nomor</th>
                  <th>Kategori</th>
                  <th>Lokasi</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tableBodyResult">
              </tbody>
              <tfoot>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>       
    
          <!-- </div> -->
        </div>
      </div>
    </div>
    <div class="col-xs-8" style="padding: 0;">
      <div class="box">
        <div class="box-body">
           <div class="box-header" style="padding-right:0;padding-left: 0;padding-top: 0;">
              <span style="font-size:20px;font-weight: bold;">List Hasil Audit AEO</span>
            </div>
            <table id="tableHasil" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>ID</th>
                  <th>Tanggal</th>
                  <th>Auditor</th>
                  <!-- <th>Kategori</th> -->
                  <th>Auditee</th>
                  <th>Lokasi</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tableBodyHasil">
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
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body">
        Are you sure want to delete this?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
          <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Point Question</h1>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="box-body">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="form-group row" align="right">
                <label class="col-sm-2">Kategori<span class="text-red">*</span></label>
                <div class="col-sm-9" align="left">
                  <select class="form-control select2" id="inputkategori" data-placeholder="Pilih Kategori..." style="width: 100%; font-size: 20px;" required>
                    <option value="AEO">AEO</option>
                  </select>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">PIC / User<span class="text-red">*</span></label>
                <div class="col-sm-9" align="left">
                  <select class="form-control select2" id="inputlokasi" data-placeholder="PIC / User" style="width: 100%; font-size: 20px;" required>
                    <option></option>
                    <option value="Human Resources">Human Resources</option>
                    <option value="General Affairs">General Affairs</option>
                    <option value="Accounting">Accounting</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="MIS">MIS</option>
                    <option value="Logistic">Logistic</option>
                    <option value="Standardization">Standardization</option>
                    <option value="Production Control">Production Control</option>
                    <option value="Purchasing">Purchasing</option>
                    <option value="Production">Production</option>
                    <option value="Production Engineering">Production Engineering</option>
                    <option value="QA">QA</option>
                    <option value="QA Production">QA Production</option>
                  </select>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Nomor<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="inputklausul" class="form-control" id="inputklausul" placeholder="Input Nomor Urutan Audit (Contoh : 1)" required>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Judul<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="inputpoint_judul" class="form-control" id="inputpoint_judul" placeholder="Input Judul" required>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Point Question<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <textarea id="inputpoint_question" class="form-control" style="height: 200px;" name="inputpoint_question"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
        <button class="btn btn-success" onclick="addPointQuestion()"><i class="fa fa-plus"></i> Add Point Question</button>
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
<script>

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    fillTable();
    fillTableResult();
    $("#navbar-collapse").text('');
    $('.select2').select2({
      language : {
        noResults : function(params) {
          return "There is no list";
      }
    }
    });

    CKEDITOR.replace('inputpoint_question' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

  });

  function clearConfirmation(){
    location.reload(true);
  }

  function fillTable(tanggal) {

    var category = "AEO";

    var data = {
      category:category,
      tanggal:tanggal
    }

    $.get('{{ url("index/audit/fetch_kategori_lokasi") }}', data, function(result, status, xhr) {

    if(result.status){
      $('#tableResult').DataTable().clear();
      $('#tableResult').DataTable().destroy();
      $('#tableBodyResult').html("");
      var tableData = "";
      var count = 1;
      
      $.each(result.lists, function(key, value) {
        // var d = new Date(value.auditor_date);
        // var day = d.getDate();
        // var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        // var month = months[d.getMonth()];
        // var year = d.getFullYear();

        // var e = new Date(value.auditee_due_date);
        // var day2 = e.getDate();
        // var month2 = months[e.getMonth()];
        // var year2 = e.getFullYear();

        tableData += '<tr>';
        tableData += '<td width="5%">'+ count +'</td>';
        tableData += '<td width="10%">'+ value.kategori +'</td>';
        tableData += '<td>'+ value.lokasi +'</td>';
        tableData += '<td><a class="btn btn-primary btn-xs" href="{{ url("index/audit_iso/point_check") }}/'+value.kategori+'/'+ value.lokasi +'"><i class="fa fa-eye"></i> Detail Question</a></td>';

        tableData += '</tr>';
        count += 1;
      });

      $('#tableBodyResult').append(tableData);

      $('#tableResult tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );
      var table = $('#tableResult').DataTable({
        'responsive':true,
        'paging': true,
        'lengthChange': true,
        'pageLength': 15,
        'searching': false,
        'ordering': true,
        'order': [],
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true
      });
    }
    else{
      alert('Attempt to retrieve data failed');
    }
       table.columns().every( function () {
      var that = this;

      $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
          that
          .search( this.value )
          .draw();
        }
      } );
    } );

    $('#tableResult tfoot tr').appendTo('#tableResult thead');

  });

    // $('#judul_table').append().empty();
    // $('#judul_table').append('<center>Pengecekan Tanggal <b>'+tanggal+'</b> dengan Judgement <b>'+jdgm+'</b> (<b>'+remark+'</b>)</center>');
    
  }


  function fillTableResult() {

    var category = "";

    var data = {
      category:category,
    }

    $.get('{{ url("index/audit_aeo/hasil_audit") }}', data, function(result, status, xhr) {

    if(result.status){

      $('#tableHasil').DataTable().clear();
      $('#tableHasil').DataTable().destroy();
      $('#tableBodyHasil').html("");
      var tableIsi = "";
      var count = 1;
      
      $.each(result.lists, function(key, value) {
        tableIsi += '<tr>';
        tableIsi += '<td width="1%">'+ count +'</td>';
        tableIsi += '<td width="10%">'+ getFormattedDate(new Date(value.tanggal)) +'</td>';
        tableIsi += '<td>'+ value.auditor_name +'</td>';
        tableIsi += '<td>'+ value.auditee_name +'</td>';
        // tableIsi += '<td>'+ value.kategori +'</td>';
        tableIsi += '<td>'+ value.lokasi +'</td>';
        tableIsi += '<td><a class="btn btn-danger btn-sm" href="{{ url("index/audit_aeo/report") }}/'+value.kategori+'/'+value.lokasi+'/'+value.auditor_name+'/'+value.tanggal+'"><i class="fa fa-file-pdf-o"></i> Report Hasil Audit</a></td>';

        tableIsi += '</tr>';
        count += 1;
      });

      $('#tableBodyHasil').append(tableIsi);

      $('#tableHasil tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );

      var table2 = $('#tableHasil').DataTable({
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
            {
            extend: 'copy',
            className: 'btn btn-success',
            text: '<i class="fa fa-copy"></i> Copy',
            exportOptions: {
              columns: ':not(.notexport)'
            }
            },
            {
              extend: 'excel',
              className: 'btn btn-info',
              text: '<i class="fa fa-file-excel-o"></i> Excel',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'print',
              className: 'btn btn-warning',
              text: '<i class="fa fa-print"></i> Print',
              exportOptions: {
                columns: ':not(.notexport)'
              }
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
      }
      else{
        alert('Attempt to retrieve data failed');
      }
      table2.columns().every( function () {
      var that = this;

      $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
          that
          .search( this.value )
          .draw();
        }
      } );
    } );

    $('#tableHasil tfoot tr').appendTo('#tableHasil thead');

  });

  }


  function getFormattedDate(date) {
    var year = date.getFullYear();

    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    var month = date.getMonth();

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    
    return day + '-' + monthNames[month] + '-' + year;
  }

  function deleteConfirmation(id) {
      jQuery('#modalDeleteButton').attr("href", '{{ url("index/audit_iso/delete") }}'+'/'+id);
  }

  function addPointQuestion() {
    var data = {
      kategori:$('#inputkategori').val(),
      lokasi:$('#inputlokasi').val(),
      klausul:$('#inputklausul').val(),
      point_judul:$('#inputpoint_judul').val(),
      point_question:CKEDITOR.instances.inputpoint_question.getData()
    }

    $.post('{{ url("input/audit_iso/point_check") }}', data, function(result, status, xhr){
      if(result.status){
        $("#create-modal").modal('hide');
        emptyAll();
        fillTable();
        openSuccessGritter('Success','Success Add Point Question');
      } else {
        audio_error.play();
        openErrorGritter('Error','Add Point Question Failed');
      }
    });
  }

  function emptyAll() {
    $('#inputklausul').val('');
    $('#inputlokasi').val('');
    $('#inputkategori').val('');
    $('#inputpoint_judul').val('');
    $("#inputpoint_question").html(CKEDITOR.instances.inputpoint_question.setData(''));
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

@stop