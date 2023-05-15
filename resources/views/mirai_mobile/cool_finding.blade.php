@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  table.table-bordered {
      border: 1px solid black;
  }

  table.table-bordered>thead>tr>th {
      border: 1px solid black;
      vertical-align: middle;
      text-align: center;
      padding-top: 5px;
      padding-bottom: 5px;
  }

  table.table-bordered>tbody>tr>td {
      border: 1px solid rgb(211, 211, 211);
     /* padding-top: 3px;
      padding-bottom: 3px;
      padding-left: 2px;
      padding-right: 2px;*/
      vertical-align: middle;
  }

  table.table-bordered>tfoot>tr>th {
      border: 1px solid rgb(211, 211, 211);
      vertical-align: middle;
  }

  #tabelmonitor>tr:hover {
      background-color: #7dfa8c;
  }

 p > img{
  max-width: 300px;
  height: auto !important;
}

@-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
  } /*End of Zoom in Keyframes */

  /* Zoom out Keyframes */
  @-webkit-keyframes zoomout {
    0% {transform: scale(0);}
    50% {transform: scale(0.5);}
    100% {transform: scale(0);}
  }
  @keyframes zoomout {
    0% {transform: scale(0);}
    50% {transform: scale(0.5);}
    100% {transform: scale(0);}
    }/*End of Zoom out Keyframes */


    #loading, #error { display: none; }

  </style>
  @endsection
  @section('header')
  <section class="content-header">
    <ol class="breadcrumb" id="last_update">
    </ol>
  </section>
  @endsection

  @section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <section class="content" style="padding-top: 0; padding-bottom: 0">
    <div class="row">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />


      <div class="col-xs-12"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text">{{ $title }}</span></center></div>

      <form method="GET" action="{{ url("export/patrol_all/list") }}">
        <div class="col-md-12" style="padding-top: 20px">
          <div class="col-xs-2">
            <div class="input-group date">
              <div class="input-group-addon bg-green" style="border: none;">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" required="" onchange="drawChart()">
            </div>
          </div>

          <div class="col-xs-2">
            <div class="input-group date">
              <div class="input-group-addon bg-green" style="border: none;">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" onchange="drawChart()">
            </div>
          </div>

          <!-- <div class="col-xs-3">
            <button type="submit" class="btn btn-success form-control" style="width: 100%"><i class="fa fa-file-excel-o"></i> &nbsp;&nbsp;Download Resume Hasil Patrol</button>
          </div> -->
        </div>
      </form>

      <div class="col-md-12" style="padding-top: 10px;">
        <div id="chart_bulan" style="width: 99%; height: 300px;"></div>
      </div>
      
      <div class="col-md-12" style="background-color: white;">
        <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
          <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
            <tr>
              <th style="width: 2%; vertical-align: middle;font-size: 16px;">Pelapor</th>
              <th style="width: 2%; vertical-align: middle;font-size: 16px;">Lokasi</th>
              <th style="width: 3%; vertical-align: middle;font-size: 16px;">Kategori</th>
              <th style="width: 10%; vertical-align: middle;font-size: 16px;">Note</th>
              <th style="width: 2%; vertical-align: middle;font-size: 16px;">Status</th>
              <th style="width: 4%; vertical-align: middle;font-size: 16px;">Penanganan</th>
            </tr>
          </thead>
          <tbody id="tabelisi">
          </tbody>
          <!-- <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot> -->
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModalBulan">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_bulan"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example4" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Auditor</th>
                    <th>Auditee</th>
                    <th>Foto</th>
                    <th>Penanganan</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEdit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Edit Temuan Audit</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="row">
              <div class="col-md-6">
                <div class="col-md-12">
                  <label for="tanggal_edit">Tanggal</label>
                  : <input type="text" name="tanggal_edit" id="tanggal_edit" class="form-control datepickertanggal">
                </div>
                <div class="col-md-12">
                  <label for="poin_edit">Kategori Patrol</label>
                  : <select class="form-control select3" id="poin_edit" name="poin_edit" data-placeholder="Poin Category" style="width: 100%;"></select>
                </div>
                <div class="col-md-12">
                  <label for="pic_edit">PIC</label>
                  : 
                  <!-- <input type="text" name="pic_edit" id="pic_edit" class="form-control"> -->
                  <select class="form-control select3" id="pic_edit" name="pic_edit" data-placeholder="Select PIC" style="width: 100%">
                  </select>
                </div>
                <div class="col-md-12">
                  <label for="lokasi_edit">Lokasi</label>
                  : 
                  <!-- <input type="text" class="form-control" name="lokasi_edit" id="lokasi_edit"> </span> -->
                  <select class="form-control select3" id="lokasi_edit" name="lokasi_edit" data-placeholder="Lokasi" style="width: 100%;"></select>
                </div>
                <div class="col-md-12">
                  <label for="note_edit">Note</label>
                  <textarea class="form-control" id="note_edit" name="note_edit"></textarea>
                </div>


              </div>
              <div class="col-md-6">
                <div class="col-md-12">
                  <label for="image_edit">Temuan</label>
                  : <div name="image_edit" id="image_edit"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_penanganan_edit">
          <button type="button" onclick="post_edit()" class="btn btn-success" data-dismiss="modal"><i class="fa fa-pencil"></i> Update Audit</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalPenanganan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Detail Temuan Audit</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="row">
              <div class="col-md-5">
                <div class="col-md-12">
                  <label for="lokasi">Lokasi</label>
                  : <span name="lokasi" id="lokasi"> </span>
                </div>
                <div class="col-md-12">
                  <label for="tanggal">Tanggal</label>
                  : <span name="tanggal" id="tanggal"> </span>
                </div>
                <div class="col-md-12">
                  <label for="note">Note</label>
                  : <span name="note" id="note"> </span>
                </div>
                <div class="col-md-12">
                  <label for="image">Temuan</label>
                  : <div name="image" id="image"></div>
                </div>
              </div>
              <div class="col-md-7">
                <h4>Catatan Penanganan</h4>
                <textarea class="form-control" required="" name="penanganan" id="penanganan" style="height: 100px;"></textarea> 
                <h4>Bukti Penanganan</h4>
                <input type="file" required="" id="bukti_penanganan" name="bukti_penanganan" accept="image/*" capture="environment">
                <h4>Status Penanganan</h4>
                <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_progress" onclick="btnAction('progress')" class="btn btn-md">Progress</a>
                <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_close" onclick="btnAction('close')" class="btn btn-md">Close Temuan</a>
                <input type="hidden" id="btn_status" name="btn_status" required="">
              </div>

              <div class="col-md-12" id="status_progress" style="display: none;">
                 <div class="col-xs-12">
                  <div class="row">
                    <hr style="border: 1px solid red;background-color: red">
                  </div>
                </div>
                <div class="col-md-12">
                  <label for="note_progress">Penanganan Progress</label>
                  : <span name="note_progress" id="note_progress"> </span>
                </div>
                <div class="col-md-12">
                  <label for="images_progress">Foto Progress</label>
                  : <div name="images_progress" id="images_progress"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_penanganan">
          <button type="button" onclick="update_penanganan()" class="btn btn-success"><i class="fa fa-pencil"></i> Submit Penanganan</button>
        </div>
      </div>
    </div>
  </div>

</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script>

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
    drawChart();
    fetchTable();
    setInterval(fetchTable, 300000);
    $('body').toggleClass("sidebar-collapse");
  });


  $('.select2').select2({
      dropdownAutoWidth : true,
      allowClear: true
    });

  $('.select3').select2({
    dropdownAutoWidth : true,
    allowClear: true,
    dropdownParent: $("#modalEdit")
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });

  $('.datepickertanggal').datepicker({
    autoclose: true,
    format: "yyyy-mm-dd",
    todayHighlight: true,
  });

  function drawChart() {    
    var date_from = $('#date_from').val();
    var date_to = $('#date_to').val();

    var data = {
      date_from: date_from,
      date_to: date_to
    };

    $.get('{{ url("fetch/cool_finding_monitoring") }}', data, function(result, status, xhr) {
      if(result.status){
        var bulan = [];
        var tahun = [];
        var belum_ditangani_bulan = [];
        var progress_ditangani_bulan = [];
        var sudah_ditangani_bulan = [];

        $.each(result.data_bulan, function(key, value) {
          bulan.push(value.bulan);
          tahun.push(value.tahun);
          belum_ditangani_bulan.push({y: parseInt(value.jumlah_belum),key:value.tahun});
          progress_ditangani_bulan.push({y: parseInt(value.jumlah_progress),key:value.tahun});
          sudah_ditangani_bulan.push({y: parseInt(value.jumlah_sudah),key:value.tahun});
        });

        $('#chart_bulan').highcharts({
          chart: {
            type: 'column',
            backgroundColor: null
          },
          title: {
            text: "Resume Per Bulan",
            style: {
                fontWeight: 'bold',
                color: 'Black'
            }
          },
          credits: {
              enabled: false
          },
          xAxis: {
              tickInterval: 1,
              gridLineWidth: 1,
              categories: bulan,
              crosshair: true
          },
          yAxis: [{
              title: {
                  text: 'Jumlah',
                  style: {
                      fontWeight: 'bold',
                  },
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      fontSize: '0.8vw'
                  }
              },
          }],
          exporting: {
              enabled: false
          },
          legend: {
              enabled: true,
              borderWidth: 1
          },
          tooltip: {
              enabled: true
          },
          plotOptions: {
              column: {
                  stacking: 'normal',
                  pointPadding: 0.93,
                  groupPadding: 0.93,
                  borderWidth: 0.8,
                  borderColor: 'black'
              },
              series: {
                  dataLabels: {
                      enabled: true,
                      formatter: function() {
                          return (this.y != 0) ? this.y : "";
                      },
                      style: {
                          textOutline: false
                      }
                  },
                  cursor: 'pointer',
                  point: {
                      events: {
                          click: function() {
                            ShowModalBulan(this.category,this.series.name);
                          }
                      }
                  }
              }
          },

          tooltip: {
            formatter:function(){
              return this.series.name+' : ' + this.y;
            }
          },
          series: [{
              name: 'Belum Ditangani',
              data: belum_ditangani_bulan,
              color: '#feccfe'
          },{
              name: 'Sudah Ditangani',
              data: sudah_ditangani_bulan,
              color: 'rgb(34, 204, 125)'
          }
          ]
        })

      } else{
        alert('Attempt to retrieve data failed');
      }
    })
  }

  function ShowModal(tgl, status, category, remark) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
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
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url('index/audit_patrol_monitoring_detail') }}",
        "data" : {
          tgl : tgl,
          status : status,
          category : category
        }
      },
      "columns": [
      {"data": "auditor_name", "width": "20%"},
      {"data": "auditee_name" , "width": "20%"},
      {"data": "foto", "width": "30%"},
      {"data": "penanganan", "width": "30%"}
      ]    
    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>Temuan Patrol '+tgl+'</b></center>'); 
  }


  function ShowModalBulan(bulan, status, category, remark, tahun) {
    tabel = $('#example4').DataTable();
    tabel.destroy();

    $("#myModalBulan").modal("show");

    var table = $('#example4').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
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
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url('index/audit_patrol_monitoring_detail_bulan') }}",
        "data" : {
          bulan : bulan,
          status : status,
          category : category,
          remark : remark,
          tahun : tahun
        }
      },
      "columns": [
      {"data": "auditor_name", "width": "20%"},
      {"data": "auditee_name" , "width": "20%"},
      {"data": "foto", "width": "30%"},
      {"data": "penanganan", "width": "30%"}
      ]    
    });

    $('#judul_table_bulan').append().empty();
    $('#judul_table_bulan').append('<center><b>Patrol Bulan '+bulan+' '+status+' '+remark+'</b></center>'); 
  }



  function fetchTable(){

    var date_from = $('#date_from').val();
    var date_to = $('#date_to').val();

    var data = {
      date_from: date_from,
      date_to: date_to
    };

    $.get('{{ url("index/cool_finding_monitoring_table") }}', data, function(result, status, xhr){
      if(result.status){

        $('#tabelmonitor').DataTable().clear();
        $('#tabelmonitor').DataTable().destroy();


        $("#tabelisi").find("td").remove();  
        $('#tabelisi').html("");
        var table = "";

        $.each(result.datas, function(key, value) {

          table += '<tr>';
          table += '<td style="text-align: center;">'+value.employee_id+ ' - '+value.name+'</span></td>';
          table += '<td style="">'+value.location+'</td>';
          table += '<td style="">'+value.category+'</td>';
          table += '<td style="">'+value.note+'</td>';
          // table += '<td style="">'+value.note+'</td>';

            if (value.status_ditangani == null) {
              table += '<td style="text-align:center;font-size:16px"><span class="label label-danger">Open</span></td>';
            }
            else if(value.status_ditangani == "progress"){
               table += '<td style="text-align:center;font-size:16px"><span class="label label-warning">Progress</span></td>';
            }

            table += '<td style=" text-align: center;">';
            
            if ("{{ Auth::user()->username }}".toUpperCase() == value.employee_id || "{{ Auth::user()->role_code }}" == "S-MIS") {
              table += '<button style="height: 100%; margin-right: 5px;" onclick="edit(\''+value.id+'\')" class="btn btn-md btn-primary form-control"><i class="fa fa-pencil-square-o"></i> Edit</button>';
            }

            table += '<button style="height: 100%;" onclick="penanganan(\''+value.id+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-thumbs-o-up"></i> Penanganan</button>';
            table += '</td>';
            table += '</tr>';
          })

        $('#tabelisi').append(table);

        $('#tabelmonitor').DataTable({
          'responsive':true,
          'paging': true,
          'lengthChange': false,
          'pageLength': 25,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': false,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });
      }
    })
  }

  function penanganan(id) {

    $('#modalPenanganan').modal("show");
    $("#penanganan").val("");
    $("#bukti_penanganan").val("");
    $("#btn_status").css('background-color', '');
    $("#btn_progress").css('background-color', '');
    $("#btn_status").val("");

    var data = {
      id : id
    }

    $.get('{{ url("index/audit_patrol/detail_penanganan") }}', data, function(result, status, xhr){

      var images = "";
      $("#image").html("");
      var images_progress = "";
      $("#images_progress").html("");

      if (result.status) {
        $("#id_penanganan").val(id);
        $("#lokasi").text(result.audit[0].lokasi);
        $("#tanggal").text(result.audit[0].tanggal);
        $("#note").text(result.audit[0].note);
        images += '<img src="{{ url("files/patrol") }}/'+result.audit[0].foto+'" width="300">';
        $("#image").append(images);

        if (result.audit[0].status_ditangani == "progress") {
          $("#status_progress").show();

          images_progress += '<img src="{{ url("files/patrol") }}/'+result.audit[0].bukti_penanganan+'" width="300">';
          $("#note_progress").text(result.audit[0].note);
          $("#images_progress").append(images_progress);
        }else{
          $("#status_progress").hide();
        }

      } else {
        openErrorGritter('Error');
      }

    }); 
  }

  function update_penanganan() {

    if ($("#penanganan").val() == "") {
      openErrorGritter("Error","Catatan Penanganan Harus Diisi");
      return false;
    }

    if ($("#bukti_penanganan").val() == "") {
      openErrorGritter("Error","Bukti Penanganan Harus Diisi");
      return false;
    }

    if ($("#btn_status").val() == "") {
      openErrorGritter("Error","Status Penanganan Harus Diisi");
      return false;
    }


    var formData = new FormData();
    formData.append('id', $("#id_penanganan").val());
    formData.append('penanganan', $("#penanganan").val());
    formData.append('bukti_penanganan', $('#bukti_penanganan').prop('files')[0]);
    formData.append('btn_status', $("#btn_status").val());


    $.ajax({
      url:"{{ url('post/audit_patrol/penanganan_new') }}",
      method:"POST",
      data:formData,
      dataType:'JSON',
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        openSuccessGritter("Success","Audit Berhasil Ditangani");
        $('#modalPenanganan').modal("hide");
        fetchTable();
        drawChart();
      },
      error: function (response) {
        openErrorGritter("Error",result.datas);
        $('#modalPenanganan').modal("hide");
      },
    });

  }

  function btnAction(cat){
    
    $('#btn_progress').css('background-color', 'white');
    $('#btn_close').css('background-color', 'white');

    if (cat == "close") {
      $('#btn_'+cat).css('background-color', '#90ed7d');
    }else{
      $('#btn_'+cat).css('background-color', '#f39c12');
    }
    $('#btn_status').val(cat);
  }

  function edit(id) {

    $('#modalEdit').modal("show");

    var data = {
      id : id
    }

    $.get('{{ url("index/audit_patrol/detail_penanganan") }}', data, function(result, status, xhr){

      var images_edit = "";
      $("#image_edit").html("");


      list = "";
      list += "<option></option> ";

      if (result.status) {

        // console.log(result.audit[0].kategori);

        $("#id_penanganan_edit").val(id);
        $("#tanggal_edit").val(result.audit[0].tanggal);

        if (result.audit[0].kategori == "Patrol Daily") { 
          
          list += "<option value='5S'>5S</option>";
          list += "<option value='Safety'>Safety</option>";
          list += "<option value='Penghematan Energi'>Penghematan Energi</option>";
          list += "<option value='Penanganan Covid'>Penanganan Covid</option>";

          $("#poin_edit").html(list);

          $("#poin_edit").val(result.audit[0].point_judul).trigger('change.select2');
        }else if(result.audit[0].kategori == "EHS & 5S Patrol"){

          list += "<option value='S-Up and 5S'>S-Up and 5S</option>";
          list += "<option value='Environment'>Environment</option>";
          list += "<option value='Health'>Health</option>";
          list += "<option value='Safety'>Safety</option>";

          $("#poin_edit").html(list);

          $("#poin_edit").val(result.audit[0].point_judul).trigger('change.select2');
        
        }else if(result.audit[0].kategori == "Patrol Covid"){
          list += "<option value='Covid'>Covid</option>";
          
          $("#poin_edit").html(list);
          $("#poin_edit").val(result.audit[0].point_judul).trigger('change.select2');
        }
        else if(result.audit[0].kategori == "Patrol Energy"){
          list += "<option value='Penghematan Energi'>Penghematan Energi</option>";
          
          $("#poin_edit").html(list);
          $("#poin_edit").val(result.audit[0].point_judul).trigger('change.select2');
        }
        else if(result.audit[0].kategori == "Patrol Bangunan"){
          list += "<option value='Patrol Bangunan' selected>Patrol Bangunan</option>";
          
          $("#poin_edit").html(list);
          $("#poin_edit").val('Patrol Bangunan').trigger('change.select2');
        }
        else{
          $("#poin_edit").val(result.audit[0].point_judul).trigger('change.select2');
        }

        $('#lokasi_edit').html('');
        var lokasi_edit = '';

        for(var i = 0; i < result.location.length;i++){
          lokasi_edit += '<option value="'+result.location[i]+'">'+result.location[i]+'</option;>';
        }
        $('#lokasi_edit').append(lokasi_edit);
        $("#lokasi_edit").val(result.audit[0].lokasi);


         $('#pic_edit').html('');
        var pic_edit = '';

        for(var i = 0; i < result.auditee.length;i++){
          pic_edit += '<option value="'+result.auditee[i].name+'">'+result.auditee[i].name+'</option;>';
        }
        $('#pic_edit').append(pic_edit);
        $("#pic_edit").val(result.audit[0].auditee_name);


        $("#note_edit").val(result.audit[0].note);


        $('#remark_edit').html('');
        var remark_edit = '';
        
        remark_edit += '<option value="Positive Finding">Positive Finding</option>';
        remark_edit += '<option value="Negative Finding">Negative Finding</option>';

        $('#remark_edit').append(remark_edit);
        $("#remark_edit").val(result.audit[0].remark);

        images_edit += '<img src="{{ url("files/patrol") }}/'+result.audit[0].foto+'" width="300">';
        $("#image_edit").append(images_edit);

      } else {
        openErrorGritter('Error');
      }

    }); 
  }


  function post_edit() {

    var data = {
      id: $("#id_penanganan_edit").val(),
      tanggal: $("#tanggal_edit").val(),
      poin: $("#poin_edit").val(),
      pic: $("#pic_edit").val(),
      lokasi : $("#lokasi_edit").val(),
      note : $("#note_edit").val(),
      remark : $("#remark_edit").val()
    };

    $.post('{{ url("post/audit_patrol/edit") }}', data, function(result, status, xhr){
      if (result.status == true) {
        openSuccessGritter("Success","Audit Berhasil Diedit");
        fetchTable();
      } else {
        openErrorGritter("Error",result.datas);
      }
    })
  }


    function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
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
  @stop