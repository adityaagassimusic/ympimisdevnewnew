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
          <div class="col-xs-2">
               <div class="input-group date">
                    <div class="input-group-addon bg-purple" style="border: none;">
                         <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datepicker" id="month_from" placeholder="Pilih Bulan" onchange="fetchChart(value)">
               </div>
          </div>
          <br><br>
          <div class="col-xs-12">
               <div id="chartkasus" style="width: 100%; height: 60vh;"></div>
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
               <table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
                    <thead>
                         <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                            <th style="padding: 5px;text-align: center;width: 1%">Location</th>
                            <th style="padding: 5px;text-align: center;width: 1%">Area</th>
                            <th style="padding: 5px;text-align: center;width: 1%">Date</th>
                            <th style="padding: 5px;text-align: center;width: 6%">Detail Accident</th>
                            <th style="padding: 5px;text-align: center;width: 1%">Condition</th>
                         </tr>
                    </thead>
                    <tbody id="bodyTableDetail">
                    
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
          // $('#modalPeriod').modal({
          //      backdrop: 'static',
          //      keyboard: false
          // });
          // fetchChart();
          fetchChart();
     });

     // $('.select3').select2({
     //      dropdownAutoWidth : true,
     //      allowClear: true,
     //      dropdownParent: $("#modalPeriod")
     // });

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

     function fetchChart(month){
          $('#loading').show();

          var data = {
               month:month,
          }
          $.get('{{ url("fetch/monitoring/kecelakaan_kerja") }}', data, function(result, status, xhr) {
               if(result.status){
                    loc = [];
                    jumlah = [];
                    nama_bulan = [];

                    modal_detail = [];

                    $.each(result.accidents, function(key, value) {
                         loc.push(value.location);
                         jumlah.push(parseInt(value.jumlah));
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
                              text: 'Resume Kasus Kecelakaan Kerja Pada Periode '+result.period,
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
                                     ShowModal(this.category,result.period);
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
                              categories: loc
                         },
                         credits:{
                              enabled:false
                         },
                         series: [
                         {
                              type: 'column',
                              name: 'Jumlah',
                              data: jumlah,
                              colorByPoint: true,
                              dataLabels: {
                                   enabled: true,
                                   style: {
                                        textOutline: false,
                                        fontWeight: 'bold',
                                        fontSize: '20px'
                                   }
                              }
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

function ShowModal(location,period) {
     $('#tableDetail').DataTable().clear();
     $('#tableDetail').DataTable().destroy();

     $('#loading').show();
     $('#bodyTableDetail').html('');

     var data = {
          location:location,
          period:period
     }

     $.get('{{ url("fetch/monitoring/kecelakaan_kerja/detail") }}', data, function(result){
          if(result.status){
               tableDetail = "";
               $.each(result.detail, function(key, value){
                    tableDetail += '<tr>';
                    tableDetail += '<td style="padding: 5px;width:1%;text-align:left !important">&nbsp;'+value.location+'</td>';
                    tableDetail += '<td style="padding: 5px;width:1%;text-align:left !important">'+value.area+'</td>';
                    tableDetail += '<td style="padding: 5px;width:1%;text-align:left !important">'+value.date_incident+' '+value.time_incident+'</td>';
                    tableDetail += '<td style="padding: 5px;width:6%;text-align:left !important">'+value.detail_incident+'</td>';
                    tableDetail += '<td style="padding: 5px;width:1%;text-align:left !important">'+value.condition+'</td>';
                    tableDetail += '</tr>';
               });
               $('#bodyTableDetail').append(tableDetail);

               $('#tableDetail').DataTable({
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

                    $('#judul_detail_dept').html('Detail Kecelakaan Lokasi '+location+' Periode '+period);
                    $('#modalDetailDept').modal('show');
                    $('#loading').hide();
          }
          else{
               alert(result.message);
          }
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
          enabled:false,
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