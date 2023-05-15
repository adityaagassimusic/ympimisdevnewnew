@extends('layouts.display')
@section('stylesheets')
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
     <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;display: none;">
          <p style="position: absolute; color: White; top: 45%; left: 35%;">
               <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-spinner"></i></span>
          </p>
     </div>
     <div class="row">
          <div class="col-xs-12">
               <div id="chartkasus" style="width: 100%; height: 60vh;"></div>
          </div>
           <div class="col-xs-12" style="margin-top: 20px;">
               <div id="container_sosialisasi" style="width: 100%; height: 60vh;"></div>
          </div>
     </div>
</section>

<div class="modal fade" id="modalKasus">
     <div class="modal-dialog modal-md">
          <div class="modal-content">
               <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                         <div class="form-group">
                              <label for="exampleInputEmail1">Pilih Kasus Kecelakaan</label>
                              <select class="form-control select3" id="case" name="case" data-placeholder='Detail kecelakaan' style="width: 100%" onchange="fetchChart(value)">
                                   <option value="">&nbsp;</option>
                                   @foreach($accident as $acc)
                                   <option value="{{$acc->id}}">{{$acc->location}} - {{$acc->condition}}</option>
                                   @endforeach
                              </select>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

<div class="modal fade" id="modalDetailDept" style="color: black;z-index: 10000;">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail_dept"></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12" id="data-activity">
               <table id="tableDetailDept" class="table table-striped table-bordered" style="width: 100%;">
                    <thead>
                         <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                            <th style="padding: 5px;text-align: center;width: 1%">GROUP</th>
                            <th style="padding: 5px;text-align: center;width: 1%">STATUS</th>
                         </tr>
                    </thead>
                    <tbody id="bodyTableDetailDept">
                    
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

<div class="modal fade" id="modalSosialisasi" style="color: black;z-index: 10000;">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_sosialisasi"></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12" id="data-activity">
          <table id="tableDetailSosialisasi" class="table table-striped table-bordered" style="width: 100%;">
                  <thead>
                       <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                            <th style="padding: 5px;text-align: center;width: 1%">EMPLOYEE ID</th>
                            <th style="padding: 5px;text-align: center;width: 1%">EMPLOYEE NAME</th>
                            <th style="padding: 5px;text-align: center;width: 1%">DEPARTMENT</th>
                            <th style="padding: 5px;text-align: center;width: 1%">GROUP</th>
                            <th style="padding: 5px;text-align: center;width: 1%">STATUS</th>
                       </tr>
                  </thead>
                  <tbody id="bodyTableDetailSosialisasi">
                    
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

<div class="modal fade" id="modalDetail">
     <div class="modal-dialog modal-lg">
          <div class="modal-content">
               <div class="modal-header">
                    <h3 style="margin-top: 0;" id="titleDetail"></h3>
                    <table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="example2">
                         <thead style="background-color: rgba(126,86,134,.7);">
                              <tr>
                                   <th>Period</th>
                                   <th>ID</th>
                                   <th>Name</th>
                                   <th>Section</th>
                                   <th>OT</th>
                              </tr>
                         </thead>
                         <tbody id="tableDetailBody"></tbody>
                         <tfoot>
                         </tfoot>
                    </table>
                    <div id="progressbar2">
                         <center>
                              <i class="fa fa-spinner fa-spin" style="font-size: 6em;"></i> 
                              <br><h4>Loading ...</h4>
                         </center>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     var detail_att = [];


     jQuery(document).ready(function(){
          $('#modalKasus').modal({
               backdrop: 'static',
               keyboard: false
          });
          // fetchChart();
     });

     $('.select3').select2({
          dropdownAutoWidth : true,
          allowClear: true,
          dropdownParent: $("#modalKasus")
     });

     function addZero(i) {
          if (i < 10) {
               i = "0" + i;
          }
          return i;
     }

     function getActualFullDate() {
          var d = new Date();
          var day = addZero(d.getDate());
          var month = addZero(d.getMonth()+1);
          var year = addZero(d.getFullYear());
          var h = addZero(d.getHours());
          var m = addZero(d.getMinutes());
          var s = addZero(d.getSeconds());
          return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
     }

     $('.datepicker').datepicker({
          autoclose: true,
          format: "yyyy-mm",
          startView: "months", 
          minViewMode: "months",
          autoclose: true,
     });


     var modal_detail = [];

     function fetchChart(id){
          $('#loading').show();
          $('#modalKasus').modal('hide');
          var data = {
               id:id,
          }
          $.get('{{ url("fetch/monitoring/yokotenkai") }}', data, function(result, status, xhr) {
               if(result.status){
                    dept = [];
                    jumlah_group = [];
                    count_sudah = [];
                    count_belum = [];

                    belum = [];
                    sudah = [];
                    modal_detail = [];


                    $.each(result.accidents, function(key, value) {
                         dept.push(value.department_shortname);
                         count_sudah.push(parseInt(value.count_sudah));
                         count_belum.push(parseInt(value.count_belum));
                         jumlah_group.push(parseInt(value.jumlah_group));

                         var jumlah_sudah = 0;
                         var jumlah_belum = 0;
                         var sosil = [];

                         for(var j = 0; j < result.sosialisasi.length; j++){
                             sosil.push(result.sosialisasi[j].employee_id);
                         }

                         for(var k = 0; k < result.employees.length;k++){
                              if (sosil.includes(result.employees[k].employee_id) && result.employees[k].department == value.department_name) {
                                   jumlah_sudah++;
                                   modal_detail.push({
                                     employee_id:result.employees[k].employee_id,
                                     name:result.employees[k].name,
                                     department_shortname:value.department_shortname,
                                     department:value.department_name,
                                     section:result.employees[k].section,
                                     group:result.employees[k].group,
                                     sub_group:result.employees[k].sub_group,
                                     status_cek:'Sudah'
                                   });
                               }else if(!sosil.includes(result.employees[k].employee_id) && result.employees[k].department == value.department_name){
                                 jumlah_belum++;
                                 modal_detail.push({
                                   employee_id:result.employees[k].employee_id,
                                   name:result.employees[k].name,
                                   department_shortname:value.department_shortname,
                                   department:value.department_name,
                                   section:result.employees[k].section,
                                   group:result.employees[k].group,
                                   sub_group:result.employees[k].sub_group,
                                   status_cek:'Belum'
                                 });
                              }
                         }
                         sudah.push({y:parseInt(jumlah_sudah),key:value.department_name});
                         belum.push({y:parseInt(jumlah_belum),key:value.department_name});

                    });

                    const chart = new Highcharts.Chart({
                         chart: {
                            renderTo: 'chartkasus',
                            type: 'column',
                            options3d: {
                                enabled: true,
                                alpha: 0,
                                beta: 0,
                                depth: 50,
                                viewDistance: 25
                            }
                        },
                         title: {
                              text: 'Resume Pengisian Form Yokotenkai Per Departemen',
                              style: {
                                   fontSize: '3vh',
                                   fontWeight: 'bold'
                              }
                         },
                         plotOptions: {
                            series:{
                               cursor: 'pointer',
                               point: {
                                 events: {
                                   click: function () {
                                     ShowModalDept(this.category,this.series.name);
                                   }
                                 }
                               },
                               animation: false,
                               dataLabels: {
                                 enabled: true,
                                 format: '{point.y}',
                                 style:{
                                   fontSize: '1vw'
                                 }
                               },
                               animation: false,
                               cursor: 'pointer',
                               depth:25
                             },
                        },
                         yAxis:{
                              title:{
                                   text: null
                              }
                         },
                         xAxis: {
                              categories: dept
                         },
                         credits:{
                              enabled:false
                         },
                         series: [
                         {
                              type: 'column',
                              name: 'Belum Mengisi',
                              data: count_belum,
                              color: '#f44336',
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         },
                         {
                              type: 'column',
                              name: 'Sudah Mengisi',
                              data: count_sudah,
                              color: '#90ee7e',
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
                         }
                         // , {
                         //      type: 'spline',
                         //      name: 'Total Grup',
                         //      data: jumlah_group,
                         //      dataLabels: {
                         //           enabled: true,
                         //           style: {
                         //                textOutline: false,
                         //                fontWeight: 'bold',
                         //                fontSize: '20px'
                         //           }
                         //      }
                         // }
                         ]
                    });

                    const chart2 = new Highcharts.Chart({
                    
                    chart: {
                       renderTo: 'container_sosialisasi',
                       type: 'column',
                       options3d: {
                           enabled: true,
                           alpha: 0,
                           beta: 0,
                           depth: 50,
                           viewDistance: 25
                       }
                    },
                    title: {
                         text: 'Resume Hasil Sosialiasi Kasus Kecelakaan Kerja',
                         style: {
                              fontSize: '3vh',
                              fontWeight: 'bold'
                         }
                    },
                   xAxis: {
                        categories: dept,
                        type: 'category',
                        gridLineWidth: 0,
                        gridLineColor: 'RGB(204,255,255)',
                        lineWidth:1,
                        lineColor:'#9e9e9e',
                        labels: {
                          style: {
                            fontSize: '13px'
                          }
                        },
                      },
                      yAxis: [{
                        title: {
                          text: 'Total Data',
                          style: {
                            color: '#eee',
                            fontSize: '15px',
                            fontWeight: 'bold',
                            fill: '#6d869f'
                          }
                        },
                        labels:{
                          style:{
                            fontSize:"15px"
                          }
                        },
                        type: 'linear',
                        opposite: true
                      }
                      ],
                      tooltip: {
                        headerFormat: '<span>{series.name}</span><br/>',
                        pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
                      },
                      legend: {
                        layout: 'horizontal',
                        itemStyle: {
                          fontSize:'12px',
                        }
                      },  
                        plotOptions: {
                            series:{
                          cursor: 'pointer',
                          point: {
                            events: {
                              click: function () {
                                ShowModal(this.category,this.series.name);
                              }
                            }
                          },
                          animation: false,
                          dataLabels: {
                            enabled: true,
                            format: '{point.y}',
                            style:{
                              fontSize: '1vw'
                            }
                          },
                          animation: false,
                          cursor: 'pointer',
                          depth:25
                        },
                        },
                        credits:{
                          enabled:false
                        },
                        series: [{
                        type: 'column',
                        data: belum,
                        name: 'Belum Sosialisasi',
                        color:'#ffeb3b'
                      },{
                        type: 'column',
                        data: sudah,
                        name: 'Sudah Sosialisasi',
                        color:'#3f51b5'
                      }
                      ]
                    });
             }
             else{
               alert(result.message);
          }
          $('#loading').hide();
     });
}

function ShowModalDept(dept,status) {
     $('#tableDetailDept').DataTable().clear();
     $('#tableDetailDept').DataTable().destroy();

     $('#loading').show();
     $('#bodyTableDetailDept').html('');

      if (status == "Belum Mengisi") {
          stat = 'Belum';
     }else if(status == "Sudah Mengisi") {
          stat = 'sudah';
     }

     var id = $("#case").val();

     var data = {
          dept:dept,
          stat:stat,
          id:id
     }

     $.get('{{ url("fetch/monitoring/yokotenkai/detail") }}', data, function(result){
          if(result.status){
               tableDetailDept = "";
               $.each(result.detail_yokotenkai, function(key, value){
                    tableDetailDept += '<tr>';
                    tableDetailDept += '<td style="width:1%;text-align:left !important">&nbsp;'+value.group+'</td>';
                    tableDetailDept += '<td style="width:1%;text-align:left !important">&nbsp;'+value.status+'</td>';
                    tableDetailDept += '</tr>';
               });
               $('#bodyTableDetailDept').append(tableDetailDept);

               $('#tableDetailDept').DataTable({
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
                     }
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
                   "processing": true
                 });

                    $('#judul_detail_dept').html('Detail '+dept+' '+status);
                    $('#modalDetailDept').modal('show');
                    $('#loading').hide();
          }
          else{
               alert(result.message);
          }
     });

     
}

function ShowModal(dept,status) {
     $('#tableDetailSosialisasi').DataTable().clear();
     $('#tableDetailSosialisasi').DataTable().destroy();

     $('#loading').show();
     $('#bodyTableDetailSosialisasi').html('');
     var tableDetailSosialisasi = '';

     for(var i = 0; i < modal_detail.length;i++){
          var stat = "";
          if (status == "Belum Sosialisasi") {
               stat = 'Belum';
          }else if(status == "Sudah Sosialisasi") {
               stat = 'Sudah';
          }

          if (modal_detail[i].department_shortname === dept) {
               if (modal_detail[i].status_cek === stat) {
                    tableDetailSosialisasi += '<tr>';
                    tableDetailSosialisasi += '<td style="width:1%;text-align:left !important">&nbsp;'+modal_detail[i].employee_id+'</td>';
                    tableDetailSosialisasi += '<td style="width:5%;text-align:left !important">&nbsp;'+modal_detail[i].name+'</td>';
                    tableDetailSosialisasi += '<td style="width:1%;text-align:left !important">&nbsp;'+modal_detail[i].department_shortname+'</td>';
                    tableDetailSosialisasi += '<td style="width:3%;text-align:left !important">&nbsp;'+modal_detail[i].group+'</td>';
                    tableDetailSosialisasi += '<td style="width:1%;text-align:left !important">&nbsp;'+modal_detail[i].status_cek+'</td>';
                    tableDetailSosialisasi += '</tr>';
               }
          }
     }
     $('#bodyTableDetailSosialisasi').append(tableDetailSosialisasi);
     $('#tableDetailSosialisasi').DataTable({
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
      }
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
    "processing": true
  });

     $('#judul_sosialisasi').html('Detail Operator '+status);
     $('#modalSosialisasi').modal('show');
     $('#loading').hide();
}

function details(date, cat, name){
     $('#tableDetailBody').html("");
     $('#titleDetail').text("");
     $('#modalDetail').modal('show');
     $('#progressbar2').show();
     var data = {
          period:date,
          department:cat,
          category:name
     }
     $.get('{{ url("fetch/overtime_report_detail") }}', data, function(result){
          if(result.status){
               tableData = "";

               $.each(result.violations, function(key, value){
                    tableData += '<tr>';
                    tableData += '<td>'+value.period+'</td>';
                    tableData += '<td>'+value.Emp_no+'</td>';
                    tableData += '<td>'+value.Full_name+'</td>';
                    tableData += '<td>'+value.Section+'</td>';
                    tableData += '<td>'+value.ot+'</td>';
                    tableData += '</tr>';
               });
               $('#titleDetail').text(cat+" More than "+name);
               $('#tableDetailBody').append(tableData);
          }
          else{
               alert(result.message);
          }
          $('#progressbar2').hide();
     });
}

Highcharts.createElement('link', {
     href: '{{ url("fonts/UnicaOne.css")}}',
     rel: 'stylesheet',
     type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
     colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
     '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
     chart: {
          backgroundColor: {
               linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
               stops: [
               [0, '#2a2a2b'],
               [1, '#3e3e40']
               ]
          },
          style: {
               fontFamily: 'sans-serif'
          },
          plotBorderColor: '#606063'
     },
     title: {
          style: {
               color: '#E0E0E3',
               textTransform: 'uppercase',
               fontSize: '20px'
          }
     },
     subtitle: {
          style: {
               color: '#E0E0E3',
               textTransform: 'uppercase'
          }
     },
     xAxis: {
          gridLineColor: '#707073',
          labels: {
               style: {
                    color: '#E0E0E3'
               }
          },
          lineColor: '#707073',
          minorGridLineColor: '#505053',
          tickColor: '#707073',
          title: {
               style: {
                    color: '#A0A0A3'

               }
          }
     },
     yAxis: {
          gridLineColor: '#707073',
          labels: {
               style: {
                    color: '#E0E0E3'
               }
          },
          lineColor: '#707073',
          minorGridLineColor: '#505053',
          tickColor: '#707073',
          tickWidth: 1,
          title: {
               style: {
                    color: '#A0A0A3'
               }
          }
     },
     tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.85)',
          style: {
               color: '#F0F0F0'
          }
     },
     plotOptions: {
          series: {
               dataLabels: {
                    color: 'white'
               },
               marker: {
                    lineColor: '#333'
               }
          },
          boxplot: {
               fillColor: '#505053'
          },
          candlestick: {
               lineColor: 'white'
          },
          errorbar: {
               color: 'white'
          }
     },
     legend: {
          itemStyle: {
               color: '#E0E0E3'
          },
          itemHoverStyle: {
               color: '#FFF'
          },
          itemHiddenStyle: {
               color: '#606063'
          }
     },
     credits: {
          style: {
               color: '#666'
          }
     },
     labels: {
          style: {
               color: '#707073'
          }
     },

     drilldown: {
          activeAxisLabelStyle: {
               color: '#F0F0F3'
          },
          activeDataLabelStyle: {
               color: '#F0F0F3'
          }
     },

     navigation: {
          buttonOptions: {
               symbolStroke: '#DDDDDD',
               theme: {
                    fill: '#505053'
               }
          }
     },

     rangeSelector: {
          buttonTheme: {
               fill: '#505053',
               stroke: '#000000',
               style: {
                    color: '#CCC'
               },
               states: {
                    hover: {
                         fill: '#707073',
                         stroke: '#000000',
                         style: {
                              color: 'white'
                         }
                    },
                    select: {
                         fill: '#000003',
                         stroke: '#000000',
                         style: {
                              color: 'white'
                         }
                    }
               }
          },
          inputBoxBorderColor: '#505053',
          inputStyle: {
               backgroundColor: '#333',
               color: 'silver'
          },
          labelStyle: {
               color: 'silver'
          }
     },

     navigator: {
          handles: {
               backgroundColor: '#666',
               borderColor: '#AAA'
          },
          outlineColor: '#CCC',
          maskFill: 'rgba(255,255,255,0.1)',
          series: {
               color: '#7798BF',
               lineColor: '#A6C7ED'
          },
          xAxis: {
               gridLineColor: '#505053'
          }
     },

     scrollbar: {
          barBackgroundColor: '#808083',
          barBorderColor: '#808083',
          buttonArrowColor: '#CCC',
          buttonBackgroundColor: '#606063',
          buttonBorderColor: '#606063',
          rifleColor: '#FFF',
          trackBackgroundColor: '#404043',
          trackBorderColor: '#404043'
     },

     legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
     background2: '#505053',
     dataLabelsColor: '#B0B0B3',
     textColor: '#C0C0C0',
     contrastTextColor: '#F0F0F3',
     maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);
</script>
@endsection