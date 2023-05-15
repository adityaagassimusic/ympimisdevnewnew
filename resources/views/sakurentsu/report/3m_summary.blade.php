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
    font-size: 12px;
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
        <!-- <div class="box-header">
          <h3 class="box-title"><span class="text-purple">Filter Data</span></h3>
        </div> -->
        <div class="box-body">
          <div class="row">
           <!--  <div class="col-md-2">
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
                <label>3M Category</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <select class="form-control pull-right select2" id="category" data-placeholder='Select 3M Category' >
                    <option value=""></option>
                    <option value="Metode">Metode</option>
                    <option value="Material">Material</option>
                    <option value="Mesin">Mesin</option>
                  </select>
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
                <label>3M Number</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <input type="text" class="form-control" id="tiga_em_number" placeholder="Input 3M Number">
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>3M Title</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <input type="text" class="form-control" id="tiga_em_title" placeholder="Input 3M Title">
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Department</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-cubes"></i>
                  </div>
                  <select class="form-control pull-right select2" id="department" data-placeholder='Select Department' >
                    <option value=""></option>
                    @foreach($department as $dpt)
                    <option value="{{ $dpt }}">{{ $dpt }}</option>
                    @endforeach
                  </select>
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
            </div> -->

            <div class="col-xs-12">
              <table id="master_table" class="table table-bordered" style="width: 100%;">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 1%">ID</th>
                    <th style="width: 1%">Form Number</th>
                    <th style="width: 1%">Sakurentsu Number</th>
                    <th style="width: 1%">3M Number</th>
                    <th style="width: 1%">Category</th>
                    <th style="width: 1%">Issue Date</th>
                    <th style="width: 2%">Department</th>
                    <th style="width: 2%">Created By</th>
                    <th>Title</th>
                    <th style="width: 2%">Actual Date</th>
                    <th style="width: 1%">Status</th>
                    <th style="width: 1%">Report</th>
                  </tr>
                </thead>
                <tbody id="master_body">
                </tbody>
                <tfoot>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tfoot>
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

    search();
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
      category : $("#category").val(),
      sakurentsu_number : $("#sakurentsu_number").val(),
      tiga_em_number : $("#tiga_em_number").val(),
      tiga_em_title : $("#tiga_em_title").val(),
      department : $("#department").val(),
      pic : $("#pic").val(),
    };


    $.get('{{ url("fetch/sakurentsu/summary/3m") }}', data, function(result, status, xhr){
      if (result.status) {
        $("#loading").hide();

        $('#master_table').DataTable().clear();
        $('#master_table').DataTable().destroy();
        $("#master_body").empty();
        body = "";
        var num = 1;

        $.each(result.datas.old_datas, function(key, value) {   
          body += "<tr>";
          body += "<td>"+num+"</td>";
          body += "<td>"+value.form_number+"</td>";
          body += "<td>"+(value.sakurentsu_number || '')+"</td>";
          body += "<td>"+value.form_identity_number+"</td>";
          body += "<td>"+value.category+"</td>";
          body += "<td>"+value.issue_date+"</td>";
          body += "<td>"+value.department+"</td>";
          body += "<td>"+value.applicant+"</td>";
          body += "<td>"+value.title+"</td>";
          body += "<td>"+(value.actual_date || '')+"</td>";
          body += "<td>"+value.status+"</td>";

          if (value.document_name) {
            var doc = value.document_name.split('| ');

            body += '<td>';
            $.each(doc, function(key2, value2) {   

              url = "{{ url('/uploads/sakurentsu/three_m/summaries_old/') }}/"+value2;
              body += "<a href='"+url+"' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Pdf Report ("+(key2+1)+")</a><br>";
            });
            body += '</td>';
          } else {
            body += '<td></td>';
          }

          body += "</tr>";

          num++;
        });

        $.each(result.datas.datas, function(key, value) {   
          body += "<tr>";
          body += "<td>"+num+"</td>";
          body += "<td>"+value.form_identity_number+"</td>";
          body += "<td>"+(value.sakurentsu_number || '')+"</td>";
          body += "<td>"+(value.form_number || '')+"</td>";
          body += "<td>"+value.category+"</td>";
          body += "<td>"+value.issue_date+"</td>";
          body += "<td>"+value.department+"</td>";
          body += "<td>"+value.name+"</td>";
          body += "<td>"+value.title+"</td>";
          body += "<td>"+(value.act_date || '')+"</td>";
          body += "<td>"+value.process_name+"</td>";

          url = "{{ url('/detail/sakurentsu/3m/') }}/"+value.id+"/view";
          url2 = "{{ url('/pdf/sakurentsu/3m/') }}/"+value.id;

          body += "<td>";
          body += "<a href='"+url+"' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> MIRAI Report</a><br>"
          body += "<a href='"+url2+"' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Pdf Report</a>"
          body += "</td>";
          body += "</tr>";
          num++;
        });

        $("#master_body").append(body);

        $('#master_table tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="text-align: center; width: 100%" type="text" placeholder="Search '+title+'"/>' );
        } );

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
          "order": [[ 0, 'asc' ]],
          initComplete: function () {
            this.api()
            .columns([4, 6, 7, 10])
            .every(function (dd) {
              var column = this;
              var theadname = $("#master_table th").eq([dd]).text();
              var select = $('<select style="max-width:100px"><option value="" style="font-size:8px;">All</option></select>')
              .appendTo($(column.footer()).empty())
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });
              column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                var vals = d;
                if ($("#master_table th").eq([dd]).text() == 'Category') {
                  vals = d.split(' ')[0];
                }
                select.append('<option style="font-size:11px;" value="' + d + '">' + vals + '</option>');
              });
            });
          },
        });

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

        $('#master_table tfoot tr').appendTo('#master_table thead');  
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