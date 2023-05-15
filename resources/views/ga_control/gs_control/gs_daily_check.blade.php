@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  table.table-bordered{
    border:1px solid white;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid rgb(54, 59, 56) !important;
    /*background-color: #212121;*/
    text-align: center;
    vertical-align: middle;
    /*color:white;*/
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(54, 59, 56);
    background-color: null;
    /*color: white;*/
    vertical-align: middle;
    padding: 2px 5px 2px 5px;
  }
  table.table-condensed > thead > tr > th{   
    color: black
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(150,150,150);
    padding:0;
  }

  #example2 {
    border:1px solid black;    
  }

  #example2 > tbody > tr > td {
    color: black;
  }

  #example3 {
    border:1px solid black;    
  }

  #example3 > tbody > tr > td {
    color: black;
  }

  #example4 {
    border:1px solid black;    
  }

  #example4 > tbody > tr > td {
    color: black;
  }

  .dataTables_length {
    color: white;
  }

  .dataTables_filter {
    color: white;
  }
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  td:hover {
    overflow: visible;
  }
  #tabelmonitor{
    font-size: 0.83vw;
  }
  .content-wrapper{
    padding-top: 0 !important;
  }

  #tabelisi > tr:hover {
    cursor: pointer;
    background-color: #212121;
  }

  .zoom{
   -webkit-user-select: none;
   -moz-user-select: none;
   -ms-user-select: none;
   -webkit-animation: zoomin 5s ease-in infinite;
   animation: zoomin 5s ease-in infinite;
   transition: all .5s ease-in-out;
   overflow: hidden;
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


    <div class="col-xs-12" style="background-color: #212121"><center><span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text">MONITORING DAILY JOB GS</span></center></div>
      <form method="GET" action="{{ url('export/gs/list') }}">
    <div class="col-md-12" style="padding-top: 20px">
      <div class="col-xs-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border: none;">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date" required="" onchange="drawChart()">

        </div>
      </div>
      <div class="col-xs-3">
        <button type="submit" class="btn btn-success form-control" style="width: 100%"><i class="fa fa-file-excel-o"></i> &nbsp;&nbsp;Download Resume Daily GS</button>
      </div>
    </div>
  </form>

    <div class="col-md-12" style="padding-top: 10px;">
      <div id="chart_bulan" style="width: 99%; height: 300px;"></div>
    </div>

    <div class="col-md-12" style="">
      <br><br>
      <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
        <thead style="background-color: #212121; color: white; font-size: 12px;font-weight: bold">
          <tr>
            <th style="width: 3%; vertical-align: middle;;font-size: 16px;">Nama</th>
            <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Lokasi</th>
            <th style="width: 6%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Pekerjaan</th>
            <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Start</th>
            <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Finish</th>
            <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Load Time</th>
          </tr>
        </thead>
        <tbody id="tabelisi">
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


<div class="modal modal-default fade" id="modalFinish">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 style="background-color: #BA55D3; text-align: center; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
          FOTO PEKERJAAN
        </h2>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6">
              <div class="form-group">
                <label>Foto Before</label>
                : <div name="img_foto_before" id="img_foto_before"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label >Foto After</label>
                : <div name="img_foto_after" id="img_foto_after"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalImage">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <div class="form-group">
          <div  name="image_show" id="image_show"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalDetail" style="z-index: 10000;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #34c3eb;" id="mod_bg">
          <h1 style="text-align: center; margin:5px; font-weight: bold; color: white" id="modalDetailTitle"></h1>
        </div>
      </div>
      <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
        <div class="col-xs-12">
          <table class="table table-bordered table-striped table-hover" id="DetailWr">
            <thead style="background-color: rgba(126,86,134,.7); color: rgb(0,0,0) !important">
              <tr>
               <th>#</th>
               <th>Nama</th>
               <th>Category</th>
               <th>Lokasi</th>
               <th>Pekerjaan</th>
             </tr>
           </thead>
           <tbody id="modalDetailBody">
           </tbody>
         </table>
       </div>
     </div>

   </div>
 </div>
</div>




</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<!-- <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script> -->
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

  var datalist = [];
  var temps = [];
  var get_daily = [];

  jQuery(document).ready(function() {
    drawChart();
    // fetchTable();
    // setInterval(fetchTable, 300000);
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
    var data = {
      date_from: date_from
    };

    $.get('{{ url("fetch/monitoring/gs/daily") }}', data, function(result, status, xhr) {
      if(result.status){

        datalist = result.gs_jobs;
        temps = result.dataworkall;
        get_daily = result.data_set;

        var name = [];
        var belum_ditangani = [];
        var sudah_ditangani = [];

        $.each(result.datas, function(key, value) {
          name.push(value.names);
          belum_ditangani.push(parseInt(value.jumlah_belum));
          sudah_ditangani.push(parseInt(value.jumlah_sudah));
        });


        $('#chart_bulan').highcharts({
          chart: {
            type: 'column',
            backgroundColor: null
          },
          title: {
            text: 'GS Joblist Daily',
          },

          xAxis: {
            type: 'category',
            categories: name,
            lineWidth:2,
            lineColor:'#9e9e9e',
            gridLineWidth: 1,
            labels: {
              style: {
                fontWeight:'Bold'
              }
            }
          },
          yAxis: {
            lineWidth:2,
            lineColor:'#fff',
            type: 'linear',
            title: {
              text: 'Total Job'
            },
            stackLabels: {
              enabled: true,
              style: {
                fontWeight: 'bold',
                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
              }
            }
          },
          legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 10,
            floating: true,
            borderWidth: 1,
            shadow: false,
            reversed: true,
            itemStyle:{
              color: "white",
              fontSize: "12px",
              fontWeight: "bold",

            }
          },
          plotOptions: {
            series: {
              cursor: 'pointer',
              point: {
                events: {
                  click: function () {
                    fillModal(this.category,this.series.name);
                  }
                }
              },
              dataLabels: {
                enabled: false,
                format: '{point.y}'
              }
            },
            column: {
              color:  Highcharts.ColorString,
              stacking: 'normal',
              pointPadding: 0.93,
              groupPadding: 0.93,
              borderWidth: 1,
              dataLabels: {
                enabled: true
              }
            }
          },
          credits: {
            enabled: false
          },

          tooltip: {
            formatter:function(){
              return this.series.name+' : ' + this.y;
            }
          },
          series: [
          {
            name: 'Open',
            data: belum_ditangani,
            color: { 
              pattern: {
                path: 'M 0 1.5 L 2.5 1.5 L 2.5 0 M 2.5 5 L 2.5 3.5 L 5 3.5',
                color: "#b22a00",
                width: 5,
                height: 5
              }
            }
          },
          {
            name: 'Close',
            data: sudah_ditangani,
            color: { 
              pattern: {
                path: 'M 0 1.5 L 2.5 1.5 L 2.5 0 M 2.5 5 L 2.5 3.5 L 5 3.5',
                color: "#357a38",
                width: 5,
                height: 5
              }
            }
          }
          ]
        })
        fetchTable();


        
      } else{
        alert('Attempt to retrieve data failed');
      }
    })
  }


  function fetchTable(){

    $('#tabelmonitor').DataTable().clear();
    $('#tabelmonitor').DataTable().destroy();

    $("#tabelisi").find("td").remove();  
    $('#tabelisi').html("");
    var table = "";

    var cnt_time2 = 0;

    $.each(datalist, function(key, value) {

      var duration_total = parseFloat(parseFloat(value.times).toFixed(2)/60);
      for(var i = 0; i < temps.length;i++){
        var dataone = temps[i].split("+");
        if(value.list_job == dataone[1] && value.id == dataone[0]){
          duration_total = duration_total - parseFloat(dataone[5]);
        }
      }
      cnt_time2 = parseFloat(parseFloat(duration_total).toFixed(2));
      table += '<tr onclick= "modalFinishImg(\''+value.img_before+'\',\''+value.img_after+'\')" style="cursor:pointer;color: white">';
      table += '<td style="text-align: center;">'+value.name_gs+'</td>';
      table += '<td style="border-left:1px solid yellow; text-align: center;">'+value.category+'</span></td>';
      table += '<td style="border-left:1px solid yellow; text-align: center;">'+value.list_job+'</td>';
      table += '<td style="border-left:1px solid yellow; text-align: center;">'+value.request_at+'</td>';
      table += '<td style="border-left:1px solid yellow; text-align: center;">'+value.finished_at+'</span></td>';
      table += '<td style="border-left:1px solid yellow; text-align:center;">'+cnt_time2+'</td>';
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

  function modalFinishImg(before,after){
    var images_gs = "";
    var images_gs_after = "";
    $("#img_foto_after").html("");
    $("#img_foto_before").html("");
    $('#img_foto_before').show();
    $('#img_foto_after').show();

    if (before.length == 4) {
      $('#img_foto_before').hide();
    }else{
      images_gs += '<img src="{{ url("images/ga/gs_control") }}/'+before+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+before+'\')">';
      $("#img_foto_before").append(images_gs);
    }

    if (after.length == 4) {
      $('#img_foto_after').hide();
    }else{
      images_gs_after += '<img src="{{ url("images/ga/gs_control") }}/'+after+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+after+'\')">';
      $("#img_foto_after").append(images_gs_after);
    }
    $('#modalFinish').modal('show');

  }

  function showImage(imgs) {
    $('#modalImage').modal('show');
    var images_show = "";
    $("#image_show").html("");
    images_show += '<img style="cursor:zoom-in" src="{{ url("images/ga/gs_control") }}/'+imgs+'" width="100%" >';
    $("#image_show").append(images_show);
  }


  function fillModal(cat, name){

    console.log(cat,name);
   // $('#loading2').show();
   $('#modalDetailBody').html('');
   $('#DetailWr').DataTable().clear();
   $('#DetailWr').DataTable().destroy();
   var resultData = '';
   var no = 1;

   var resultText = "";

   if (name == "Open") {
    resultText = 'Detail Joblist Daily GS Open';   
    $('#mod_bg').css('background-color', '#34c3eb');

  }
  else{
    resultText = 'Detail Joblist Daily GS Close';   
    $('#mod_bg').css('background-color', '#00a65a');

  }

  // $.each(get_daily, function(key, value) {
    for (var i =  0; i < get_daily.length; i++) {
      console.log(get_daily[i].names, cat);
      if (get_daily[i].names == cat && get_daily[i].status == 0 && name == "Open") {
        resultData += '<tr style="background-color: rgba(204, 255, 255); color:black; text-align: center">';
        resultData += '<td style="width: 1%">'+ no +'</td>';
        resultData += '<td style="width: 5%">'+ get_daily[i].names +'</td>';
        resultData += '<td style="width: 1%">'+ get_daily[i].category +'</td>';
        resultData += '<td style="width: 5%">'+ get_daily[i].area +'</td>';
        resultData += '<td style="width: 5%">'+ get_daily[i].list_job +'</td>';
        resultData += '</tr>';
        no++;
      }else if (get_daily[i].names == cat && get_daily[i].status == 2 && name == "Close") {
        resultData += '<tr style="background-color: rgba(204, 255, 255); color:black; text-align: center">';
        resultData += '<td style="width: 1%">'+ no +'</td>';
        resultData += '<td style="width: 5%">'+ get_daily[i].names +'</td>';
        resultData += '<td style="width: 1%">'+ get_daily[i].category +'</td>';
        resultData += '<td style="width: 1%">'+ get_daily[i].area +'</td>';
        resultData += '<td style="width: 5%">'+ get_daily[i].list_job +'</td>';
        resultData += '</tr>';
        no++;
      }
    }

    $('#modalDetailBody').append(resultData);
    $('#modalDetailTitle').html(resultText);

    $('#loading2').hide();

    var table = $('#DetailWr').DataTable({
      'dom': 'Bfrtip',
      'responsive':true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[{
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
        ]
      },
      'paging': true,
      'lengthChange': true,
      'pageLength': 5,
      'DataListing': true ,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true
    });
    $('#modalDetail').modal('show');

  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
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
        [0, '#2a2a2b']
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
        // itemStyle: {
        //   color: '#E0E0E3'
        // },
        // itemHoverStyle: {
        //   color: '#FFF'
        // },
        // itemHiddenStyle: {
        //   color: '#606063'
        // }
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