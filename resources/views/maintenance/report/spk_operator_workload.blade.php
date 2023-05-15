@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
  tfoot>tr>td{
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
    background-color: #605ca8;
    color: white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    background-color: #fffcb7; 
  }

  table.table-bordered > tbody > tr > th{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    color: white;
    text-align: center;
    background-color: #605ca8;
  }

  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }

  .inprogress {
    background-color: #6ddb5e !important;
  }

  .pending {
    background-color: #e83e27 !important;
  }

</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">
    <div class="col-xs-12">
      <div id="main_contain" style="height: 450px"></div>
    </div>
    <div class="col-xs-6">
      <table class="table" style="color: white;" border="1">
        <thead>
          <tr><th colspan="2" style="background-color: #bb90d4; color: black">SPK Pending Vendor</th></tr>
          <tr>
            <th style="background-color: #bb90d4; color: black">Nomor SPK</th>
            <th style="background-color: #bb90d4; color: black">Pending Mulai</th>
          </tr>
        </thead>
        <tbody id="pending_vendor"></tbody>
      </table>
    </div>
    <div class="col-xs-6">
      <table class="table" style="color: white;" border="1">
        <thead>
          <tr><th colspan="2" style="background-color: #bb90d4; color: black">SPK Pending Spare Part</th></tr>
          <tr>
            <th style="background-color: #bb90d4; color: black">Nomor SPK</th>
            <th style="background-color: #bb90d4; color: black">Pending Mulai</th>
          </tr>
        </thead>
        <tbody id="pending_part"></tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div class="col-xs-12">
            <h1 style="text-align: center; margin:5px; font-weight: bold;" id="judulDetail"></h1>
            <table class="table table-bordered" style="width: 100%" id="tableDetail">
              <thead>
                <tr>
                  <th>Nomor SPK</th>
                  <th>Mulai Mengerjakan</th>
                </tr>
              </thead>
              <tbody id="bodyDetail"></tbody>
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

  var data_detail = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    getData();
  });

  function getData() {
    $.get('{{ url("fetch/maintenance/spk/workload") }}', function(result, status, xhr){
      var categories = [];
      var temp_arr = [];
      var listed_spk = [];
      var inprogress_spk = [];

      var pending_part = [];
      var pending_vendor = [];

      $.each(result.operator, function(index, value){
        categories.push(value.name);
      });

      $.each(result.datas, function(index2, value2){
        if (value2.remark == '5') {
          if (value2.status == 'Vendor') {
            pending_vendor.push({'order_no' : value2.order_no, 'pending': value2.status, 'time': value2.start_actual });
          } else if (value2.status == 'Part Tidak Ada') {
            pending_part.push({'order_no' : value2.order_no, 'pending': value2.status, 'time': value2.start_actual });
          }
        } else if (value2.remark == '4' || value2.remark == '3') {
          temp_arr.push({'name' : value2.name+'_'+value2.remark, jml : 1});
          data_detail.push(value2);
        } 
      })


      totals = temp_arr.reduce(function (item, value) {
        (item[value.name])? item[value.name] += value.jml : item[value.name] = value.jml;
        return item;
      }, {});


      $.each(categories, function(index3, value3){
        stat_3 = '';
        stat_4 = '';

        for(var i in totals){
          var nama = i.split('_')[0];
          var spk = i.split('_')[1];

          if (value3 == nama) {
            if (spk == '3') {
              stat_3 = 'ok';
              listed_spk.push(totals[i]);
            } else if(spk == '4'){
              stat_4 = 'ok';
              inprogress_spk.push(totals[i]);
            }
          }
        }

        if (stat_3 == '') {
          listed_spk.push(0);
        }

        if (stat_4 == '') {
          inprogress_spk.push(0);
        }

      })

      console.log(inprogress_spk);

      $("#pending_vendor").empty();
      var vendor = "";
      $.each(pending_vendor, function(index4, value4){
        vendor += '<tr><td>'+value4.order_no+'</td><td>'+value4.time+'</td></tr>';
      });
      $("#pending_vendor").append(vendor);


      $("#pending_part").empty();
      var part = "";
      $.each(pending_part, function(index5, value5){
        part += '<tr><td>'+value5.order_no+'</td><td>'+value5.time+'</td></tr>';
      });
      $("#pending_part").append(part);

      Highcharts.chart('main_contain', {
        chart: {
          type: 'column'
        },
        title: {
          text: 'SPK Operator Workload'
        },
        xAxis: {
          categories: categories
        },
        yAxis: {
          min: 0,
          title: {
            text: 'Total SPK'
          },
          stackLabels: {
            enabled: true,
            style: {
              fontWeight: 'bold',
                color: ( // theme
                  Highcharts.defaultOptions.title.style &&
                  Highcharts.defaultOptions.title.style.color
                  ) || 'gray'
              }
            }
          },
          legend: {
            enabled: true
          },
          tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
          },
          plotOptions: {
            column: {
              stacking: 'normal',
              dataLabels: {
                enabled: true
              },
              cursor: 'pointer',
              point: {
                events: {
                  click: function () {
                    modalTampil(this.category, this.series.name);
                  }
                }
              },
            }
          },
          series: [
          {
            name: 'InProgress',
            data: inprogress_spk,
            color: '#6ddb5e'
          }, {
            name: 'Listed',
            data: listed_spk,
            color: '#adb3af'
          }],
          credits: false
        });
    })
  }

  function modalTampil(nama, progress) {
    $("#detailModal").modal('show');

    $("#judulDetail").html(nama+"<br>"+progress);
    body2 = '';
    $("#bodyDetail").empty();

    $.each(data_detail, function(index, value){
      if (value.name == nama && progress == 'InProgress' && value.remark == '4') {
        console.log(value.order_no);
        body2 += '<tr><td>'+value.order_no+'</td><td>'+value.start_actual+'</td></tr>';
      } else if (value.name == nama && progress == 'Listed' && value.remark == '3') {
        body2 += '<tr><td>'+value.order_no+'</td><td>'+(value.start_actual || '-')+'</td></tr>';
      }
    });

    $("#bodyDetail").append(body2);
    
  }

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

  Highcharts.createElement('link', {
    href: '{{ url("fonts/UnicaOne.css")}}',
    rel: 'stylesheet',
    type: 'text/css'
  }, null, document.getElementsByTagName('head')[0]);

  Highcharts.theme = {
    colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
    '#eeaaee', '#55BF3B', '#DF5353', '#c39bd3', '#fdfefe', '#ba4a00', '#ffeb3b', '#b0bec5', '#0288d1', '#ec407a', '#a1887f'],
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