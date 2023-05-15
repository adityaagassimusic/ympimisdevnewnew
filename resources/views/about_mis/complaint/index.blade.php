@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
 tbody>tr>td{
  text-align:center;
  vertical-align: middle;
  font-weight: bold;
}
table.table-bordered{
  border:1px solid black;
}
table.table-bordered > thead > tr > th{
  border:1px solid black;
  vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
  border:1px solid black;
  vertical-align: middle;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid black;
  vertical-align: middle;
}
#loading { display: none; }
#loading { display: none; }

/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>
@stop
@section('header')
<section class="content-header">

  <h1 style="text-align: center;">
    Wifi Complaint Monitoring<span class="text-purple"></span>
  </h1>
  <ol class="breadcrumb">
         <!--  <li>
               <button data-toggle="modal" data-target="#modalCreate" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i> New Item</button>
          </li>
        -->
      </ol>
    </section>
    @stop
    @section('content')
    <section class="content">
      <div class="row">
        <div class="col-md-12" style="padding: 1px !important;margin-top: 10px;margin-bottom: 10px;">
          <div class="col-xs-2">
            <div class="input-group date">
              <div class="input-group-addon bg-green" style="border: none;">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date" onchange="drawChart()">
            </div>
          </div>
        </div>
        <div class="xol-xs-12 col-md-2 col-lg-6" id="chart_kategori" style="height: 50vh;">
        </div>
        <div class="xol-xs-12 col-md-3 col-lg-6" id="chart_bulan" style="height: 50vh;">
        </div>
      </div>

      <div class="col-xs-12">
        <div class="nav-tabs-custom" style="margin-top: 1%;">
          <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
            <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Open</a>
            </li>
            <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Close</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
              <div class="col-xs-12" style="padding-top: 20px;">
                <div class="row" id="divTable">
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
              <div class="col-xs-12" style="padding-top: 20px;">
                <div class="row" id="divTable2">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
      <div class="modal-dialog modal-md" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail"></h4>
          </div>
          <div class="modal-body">
            <div class="row" id="divTable4">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          </div>
        </div>
      </div>
    </div> 
    <div class="modal fade" id="modalDetail5" style="color: black;z-index: 10000;">
      <div class="modal-dialog modal-md" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail5"></h4>
          </div>
          <div class="modal-body">
            <div class="row" id="divTable5">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          </div>
        </div>
      </div>
    </div> 

    @endsection
    @section('scripts')
    <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
    <script src="{{ url("js/buttons.flash.min.js")}}"></script>
    <script src="{{ url("js/jszip.min.js")}}"></script>
    <script src="{{ url("js/vfs_fonts.js")}}"></script>
    <script src="{{ url("js/buttons.html5.min.js")}}"></script>
    <script src="{{ url("js/buttons.print.min.js")}}"></script>
    <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
    <script src="{{ url("js/highcharts.js")}}"></script>
    <script src="{{ url("js/highcharts-3d.js")}}"></script>
    <script src="{{ url("js/jquery.numpad.js")}}"></script>
    <script>
     $.ajaxSetup({
      headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
   });

     var no = 1;
     var data_detail = [];

     jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
      drawChart();

          // Chart();
          // TableAtas();
        });

     $('.select2').select2({
      dropdownAutoWidth : true,
      dropdownParent: $("#modalCreate"),
      allowClear:true,
      tags: true
    });

     $('.select3').select2({
      dropdownAutoWidth : true,
      dropdownParent: $("#updateModal"),
      allowClear:true,
      tags: true
    });

     $('#rcv_date').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true
    });


     $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm",
      todayHighlight: true,
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });

     function initiateTable() {
      $('#divTable').html("");
      var tableData = "";
      tableData += "<table id='misTable' class='table table-bordered table-striped table-hover'>";
      tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
      tableData += '<tr>';
      tableData += '<th style="width: 1%">Date</th>';
      tableData += '<th style="width: 1%">Category</th>';
      tableData += '<th style="width: 2%">Pelapor</th>';
      tableData += '<th style="width: 5%">Complaint</th>';
      tableData += '<th style="width: 1%">Location</th>';
      tableData += '</tr>';
      tableData += '</thead>';
      tableData += '<tbody id="misTableBody">';
      tableData += "</tbody>";
      tableData += "<tfoot>";
      tableData += "<tr>";
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

    function initiateTable2() {
      $('#divTable2').html("");
      var tableData2 = "";
      tableData2 += "<table id='misTable2' class='table table-bordered table-striped table-hover'>";
      tableData2 += '<thead style="background-color: rgba(126,86,134,.7);">';
      tableData2 += '<tr>';
      tableData2 += '<th style="width: 1%">Date</th>';
      tableData2 += '<th style="width: 1%">Category</th>';
      tableData2 += '<th style="width: 2%">Pelapor</th>';
      tableData2 += '<th style="width: 5%">Complaint</th>';
      tableData2 += '<th style="width: 1%">Location</th>';
      tableData2 += '</tr>';
      tableData2 += '</thead>';
      tableData2 += '<tbody id="misTableBody2">';
      tableData2 += "</tbody>";
      tableData2 += "<tfoot>";
      tableData2 += "<tr>";
      tableData2 += "<th></th>";
      tableData2 += "<th></th>";
      tableData2 += "<th></th>";
      tableData2 += "<th></th>";
      tableData2 += "<th></th>";
      tableData2 += "</tr>";
      tableData2 += "</tfoot>";
      tableData2 += "</table>";
      $('#divTable2').append(tableData2);
    }


    function get_inv(){
     initiateTable();

     $('#misTable').DataTable().clear();
     $('#misTable').DataTable().destroy();
     $("#misTableBody").empty();
     var body3 = '';
     var body4 = '';

     $.each(data_detail[0], function(index, value){

      if (value.handling == null) {
        body3 += "<tr>";
        body3 += "<td>"+value.tanggal+"</td>";
        body3 += "<td>"+value.category+"</td>";
        body3 += "<td>"+value.employee_id+" - "+value.name+"</td>";
        body3 += "<td>"+value.detail_complaint+"</td>";
        body3 += "<td>"+value.location+"</td>";
        body3 += "</tr>";
        no++;
      }

    })

     $("#misTableBody").append(body3);


     $('#misTable tfoot th').each( function () {
       var title = $(this).text();
       $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
     } );


     var table = $('#misTable').DataTable({
       'dom': 'Bfrtip',
       'responsive':true,
       'lengthMenu': [
       [ 10, 25, 50, -1 ],
       [ '10 rows', '25 rows', '50 rows', 'Show all' ]
       ],
       'buttons': {
        buttons:[
        {
         extend: 'excel',
         className: 'btn btn-info',
         text: '<i class="fa fa-file-excel-o"></i> Excel',
         exportOptions: {
          columns: ':not(.notexport)'
        }
      },
      {
       extend: 'pageLength',
       className: 'btn btn-default',
     },
     ]
   },
   initComplete: function() {
    this.api()
    .columns([0,1,2,4])
    .every(function(dd) {
     var column = this;
     var theadname = $("#misTable th").eq([dd])
     .text();
     var select = $(
      '<select><option value="" style="font-size:11px;">All</option></select>'
      )
     .appendTo($(column.footer()).empty())
     .on('change', function() {
      var val = $.fn.dataTable.util
      .escapeRegex($(this)
       .val());

      column.search(val ? '^' + val + '$' :
       '', true,
       false)
      .draw();
    });
     column
     .data()
     .unique()
     .sort()
     .each(function(d, j) {
      var vals = d;
      if ($("#misTable th").eq([dd])
       .text() ==
       'Category') {
       vals = d.split(' ')[0];
   }
   select.append(
    '<option style="font-size:12px;width: 100%"  value="' +
    d + '">' + vals + '</option>');
 });
   });
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
  "processing": false,
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

     $('#misTable tfoot tr').appendTo('#misTable thead');

     initiateTable2();

     $.each(data_detail[0], function(index, value){
      if (value.handling != null) {
        body4 += "<tr>";
        body4 += "<td>"+value.tanggal+"</td>";
        body4 += "<td>"+value.category+"</td>";
        body4 += "<td>"+value.employee_id+" - "+value.name+"</td>";
        body4 += "<td>"+value.detail_complaint+"</td>";
        body4 += "<td>"+value.location+"</td>";
        body4 += "</tr>";
        no++;
      }

    })

     $("#misTableBody2").append(body4);


     $('#misTable2 tfoot th').each( function () {
       var title = $(this).text();
       $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
     } );


     var table = $('#misTable2').DataTable({
       'dom': 'Bfrtip',
       'responsive':true,
       'lengthMenu': [
       [ 10, 25, 50, -1 ],
       [ '10 rows', '25 rows', '50 rows', 'Show all' ]
       ],
       'buttons': {
        buttons:[
        {
         extend: 'excel',
         className: 'btn btn-info',
         text: '<i class="fa fa-file-excel-o"></i> Excel',
         exportOptions: {
          columns: ':not(.notexport)'
        }
      },
      {
       extend: 'pageLength',
       className: 'btn btn-default',
     },
     ]
   },
   initComplete: function() {
    this.api()
    .columns([0,1,2,4])
    .every(function(dd) {
     var column = this;
     var theadname = $("#misTable2 th").eq([dd])
     .text();
     var select = $(
      '<select><option value="" style="font-size:11px;">All</option></select>'
      )
     .appendTo($(column.footer()).empty())
     .on('change', function() {
      var val = $.fn.dataTable.util
      .escapeRegex($(this)
       .val());

      column.search(val ? '^' + val + '$' :
       '', true,
       false)
      .draw();
    });
     column
     .data()
     .unique()
     .sort()
     .each(function(d, j) {
      var vals = d;
      if ($("#misTable2 th").eq([dd])
       .text() ==
       'Category') {
       vals = d.split(' ')[0];
   }
   select.append(
    '<option style="font-size:12px;width: 100%"  value="' +
    d + '">' + vals + '</option>');
 });
   });
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
  "processing": false,
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

     $('#misTable2 tfoot tr').appendTo('#misTable2 thead');

   }



   function drawChart() {    

    var datefrom = $('#datefrom').val();

    var data = {
      datefrom: datefrom
    };

    $.get('{{ url("fetch/mis/complaint/monitoring") }}', data, function(result, status, xhr) {
      if(result.status){

        var tgl = [];

        var kategori = [];
        var belum_ditangani = [];
        var sudah_ditangani = [];

        var bulans = [];
        var tahun = [];
        var belum_ditangani_bulan = [];
        var sudah_ditangani_bulan = [];

        data_detail = [];

        data_detail.push(result.data_list);


        $.each(result.data_kategori, function(key, value) {
          kategori.push(value.location);
          belum_ditangani.push(parseInt(value.jumlah_belum));
          sudah_ditangani.push(parseInt(value.jumlah_sudah));
        });

        $.each(result.data_bulan, function(key, value) {
          bulans.push(value.bulans);
          tahun.push(value.tahun);
          belum_ditangani_bulan.push({y: parseInt(value.jumlah_belum),key:value.tahun});
          sudah_ditangani_bulan.push({y: parseInt(value.jumlah_sudah),key:value.tahun});
        });

        Highcharts.chart('chart_kategori', {
          chart: {
            backgroundColor: null,
            type: 'column',

          },
          title: {
            text: "Resume Wifi Complaint",
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
            categories: kategori,
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
                    showModalTable(this.category,this.series.name);
                  }
                }
              }
            }
          },
          series: [{
            name: 'Belum Ditangani',
            data: belum_ditangani,
            color: '#feccfe'
          }, {
            name: 'Sudah Ditangani',
            data: sudah_ditangani,
            color: 'rgb(34, 204, 125)'
          }]
        });


        $('#chart_bulan').highcharts({
          chart: {
            type: 'column',
            backgroundColor: null
          },
          title: {
            text: "Resume Wifi Complaint Per Bulan",
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
            categories: bulans,
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
          }, {
            name: 'Sudah Ditangani',
            data: sudah_ditangani_bulan,
            color: 'rgb(34, 204, 125)'
          }
          ]
        })
      } else{
        alert('Attempt to retrieve data failed');
      }
      get_inv();

    })
}


function showModalTable(kategori, status) {
  $('#divTable4').html("");
  var tableData = "";
  tableData += "<div class='col-md-12'>";
  tableData += "<table id='tableDetail' class='table table-bordered table-striped table-hover'>";
  tableData += '<thead>';
  tableData += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
  tableData += '<th style="width:1%">Date</th>';
  tableData += '<th style="width:1%">Category</th>';
  tableData += '<th style="width:4%">Pelapor</th>';
  tableData += '<th style="width:5%">Complaint</th>';
  tableData += '<th style="width:1%">Location</th>';
  tableData += '<th style="width:1%">Evidance</th>';
  tableData += '<th style="width:1%">Action</th>';
  tableData += '</tr>';
  tableData += '</thead>';
  tableData += '<tbody id="bodyTableDetail">';
  tableData += "</tbody>";
  tableData += "<tfoot>";
  tableData += "<tr>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "</tr>";
  tableData += "</tfoot>";
  tableData += "</table>";
  tableData += "</div>";
  $('#divTable4').append(tableData);
  ShowModalAll(kategori, status);

}


function showModalTable5(kategori, status) {
  $('#divTable5').html("");
  var tableData = "";
  tableData += "<div class='col-md-12'>";
  tableData += "<table id='tableDetail5' class='table table-bordered table-striped table-hover'>";
  tableData += '<thead>';
  tableData += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
  tableData += '<th style="width:1%">Date</th>';
  tableData += '<th style="width:1%">Category</th>';
  tableData += '<th style="width:4%">Pelapor</th>';
  tableData += '<th style="width:5%">Complaint</th>';
  tableData += '<th style="width:1%">Location</th>';
  tableData += '<th style="width:1%">Evidance</th>';
  tableData += '<th style="width:1%">Action</th>';
  tableData += '</tr>';
  tableData += '</thead>';
  tableData += '<tbody id="bodyTableDetail5">';
  tableData += "</tbody>";
  tableData += "<tfoot>";
  tableData += "<tr>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "<th></th>";
  tableData += "</tr>";
  tableData += "</tfoot>";
  tableData += "</table>";
  tableData += "</div>";
  $('#divTable5').append(tableData);

}



function ShowModalAll(kategori, status) { 

  $('#tableDetail').DataTable().clear();
  $('#tableDetail').DataTable().destroy();
  $('#loading').show();
  $('#bodyTableDetail').html('');
  var tableDetail = '';

  $.each(data_detail[0], function(key, value){
    console.log(value.location);

    if (value.location == kategori && value.handling == null && status == "Belum Ditangani") {

      tableDetail += '<tr>';
      tableDetail += '<td>'+value.tanggal+'</td>';
      tableDetail += '<td>'+value.category+'</td>';
      tableDetail += '<td>'+value.employee_id+'-'+value.name.split(' ').slice(0,2).join(' ')+'</td>';
      tableDetail += '<td>'+value.detail_complaint+'</td>';
      tableDetail += '<td>'+value.location+'</td>';
      if (value.evidence != null) {
        tableDetail += "<td style='border-left:1px solid yellow'><img src='"+"{{ url('data_file/mis/complaint') }}/"+value.evidence+"' width='250'></td>";  
      }else{
        tableDetail += '<td>-</td>';
      }
      tableDetail += '<td>-</td>';
      tableDetail += '</tr>';

    }else if (value.location == kategori && value.handling != null && status == "Sudah Ditangani"){
      tableDetail += '<tr>';
      tableDetail += '<td>'+value.tanggal+'</td>';
      tableDetail += '<td>'+value.category+'</td>';
      tableDetail += '<td>'+value.employee_id+'-'+value.name.split(' ').slice(0,2).join(' ')+'</td>';
      tableDetail += '<td>'+value.detail_complaint+'</td>';
      tableDetail += '<td>'+value.location+'</td>';
      if (value.evidence != null) {
        tableDetail += "<td style='border-left:1px solid black'><img src='"+"{{ url('data_file/mis/complaint') }}/"+value.evidence+"' width='250'></td>";  
      }else{
        tableDetail += '<td>-</td>';
      }
      tableDetail += '<td>-</td>';
      tableDetail += '</tr>';

    }
  });

  $('#bodyTableDetail').append(tableDetail);

  $('#tableDetail tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
  } );


  var table = $('#tableDetail').DataTable({
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
        className: 'btn btn-success',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        exportOptions: {
          columns: ':not(.notexport)'
        }
      }

      ]
    },
    initComplete: function() {
      this.api()
      .columns([0,1,2,4])
      .every(function(dd) {
        var column = this;
        var theadname = $("#tableDetail th").eq([dd])
        .text();
        var select = "";
        var select = $(
          '<select><option value="" style="font-size:11px;">All</option></select>'
          )
        .appendTo($(column.footer()).empty())
        .on('change', function() {
          var val = $.fn.dataTable.util
          .escapeRegex($(this)
            .val());

          column.search(val ? '^' + val + '$' :
            '', true,
            false)
          .draw();
        });
        column
        .data()
        .unique()
        .sort()
        .each(function(d, j) {
          var vals = d;
          if ($("#tableDetail th").eq([dd])
            .text() ==
            'Category') {
            vals = d.split(' ')[0];
        }
        select.append(
          '<option style="font-size:12px;"  value="' +
          d + '">' + vals + '</option>');
      });
      });
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

  $('#tableDetail tfoot tr').appendTo('#tableDetail thead');

  $('#judul_detail').html('Detail Wifi Complaint '+status);
  $('#modalDetail').modal('show');
  $('#loading').hide();
}


function ShowModalBulan(kategori,status) { 
  showModalTable5();


console.log('s');
  $('#tableDetail5').DataTable().clear();
  $('#tableDetail5').DataTable().destroy();
  $('#loading').show();
  $('#bodyTableDetail5').html('');
  var tableDetail = '';

  $.each(data_detail[0], function(key, value){

    if (value.bulan == kategori && value.handling == null && status == "Belum Ditangani") {

      tableDetail += '<tr>';
      tableDetail += '<td>'+value.tanggal+'</td>';
      tableDetail += '<td>'+value.category+'</td>';
      tableDetail += '<td>'+value.employee_id+'-'+value.name.split(' ').slice(0,2).join(' ')+'</td>';
      tableDetail += '<td>'+value.detail_complaint+'</td>';
      tableDetail += '<td>'+value.location+'</td>';
      if (value.evidence != null) {
        tableDetail += "<td style='border-left:1px solid yellow'><img src='"+"{{ url('data_file/mis/complaint') }}/"+value.evidence+"' width='250'></td>";  
      }else{
        tableDetail += '<td>-</td>';
      }
      tableDetail += '<td>-</td>';
      tableDetail += '</tr>';

    }else if (value.bulan == kategori && value.handling != null && status == "Sudah Ditangani"){
      tableDetail += '<tr>';
      tableDetail += '<td>'+value.tanggal+'</td>';
      tableDetail += '<td>'+value.category+'</td>';
      tableDetail += '<td>'+value.employee_id+'-'+value.name.split(' ').slice(0,2).join(' ')+'</td>';
      tableDetail += '<td>'+value.detail_complaint+'</td>';
      tableDetail += '<td>'+value.location+'</td>';
      if (value.evidence != null) {
        tableDetail += "<td style='border-left:1px solid black'><img src='"+"{{ url('data_file/mis/complaint') }}/"+value.evidence+"' width='250'></td>";  
      }else{
        tableDetail += '<td>-</td>';
      }
      tableDetail += '<td>-</td>';
      tableDetail += '</tr>';

    }
  });

  $('#bodyTableDetail5').append(tableDetail);

  $('#tableDetail5 tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
  } );


  var table = $('#tableDetail5').DataTable({
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
        className: 'btn btn-success',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        exportOptions: {
          columns: ':not(.notexport)'
        }
      }

      ]
    },
    initComplete: function() {
      this.api()
      .columns([0,1,2,4])
      .every(function(dd) {
        var column = this;
        var theadname = $("#tableDetail5 th").eq([dd])
        .text();
        var select = "";
        var select = $(
          '<select><option value="" style="font-size:11px;">All</option></select>'
          )
        .appendTo($(column.footer()).empty())
        .on('change', function() {
          var val = $.fn.dataTable.util
          .escapeRegex($(this)
            .val());

          column.search(val ? '^' + val + '$' :
            '', true,
            false)
          .draw();
        });
        column
        .data()
        .unique()
        .sort()
        .each(function(d, j) {
          var vals = d;
          if ($("#tableDetail5 th").eq([dd])
            .text() ==
            'Category') {
            vals = d.split(' ')[0];
        }
        select.append(
          '<option style="font-size:12px;"  value="' +
          d + '">' + vals + '</option>');
      });
      });
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

  $('#tableDetail5 tfoot tr').appendTo('#tableDetail5 thead');

  $('#judul_detail5').html('Detail Wifi Complaint '+status);
  $('#modalDetail5').modal('show');
  $('#loading').hide();
}

function unique(list) {
  var result = [];
  $.each(list, function(i, e) {
   if ($.inArray(e, result) == -1) result.push(e);
 });
  return result;
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
@endsection