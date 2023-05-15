@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  input {
    line-height: 22px;
  }
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:center;
    color: black;
  }
  tfoot>tr>th{
    text-align:center;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  .content-wrapper{
    color: white;
    font-weight: bold;
    background-color: #313132 !important;
  }
  #loading, #error { display: none; }

  .loading {
    margin-top: 8%;
    position: absolute;
    left: 50%;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
  }
</style>
@endsection

@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@stop

@section('content')
<section class="content" style="padding-top:0">
  <div class="row">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
      </p>
    </div>


    <div class="col-xs-10">
      <div class="col-xs-12" style="padding-left: 0; padding-bottom: 4px;">
        @if(isset($detail_item))
        <h2 style="font-weight: bold;margin-top: 10px">&nbsp;&nbsp;&nbsp;TRENDLINE : <span style="color:#ff8f00;" id="judul"> {{ $detail_item[0]->gmc }} - {{ $detail_item[0]->deskripsi }} ( {{ $detail_item[0]->proses }} )</span></h2>
        @else
        <h2 style="font-weight: bold;margin-top: 10px" >&nbsp;&nbsp;&nbsp;TRENDLINE : <span style="color:#ff8f00;" id="judul"></span></h2>
        @endif
      </div>
    </div>
    <div class="col-xs-2">
      <a class="btn btn-success" href="{{ url('winds') }}" aria-label="Close" style="font-weight: bold; font-size: 15px; width: 100%;"><i class="fa fa-chevron-left"></i> Back to Dashboard <br><i class="fa fa-chevron-left"></i>  ダッシュボードに戻る</a>
    </div>
    <div class="col-xs-12">
      <div class="col-xs-12" style="padding-left: 0; padding-bottom: 4px;">
        <div class="col-xs-2">
          <input type="text" class="form-control datepicker" id="dateFrom" placeholder="Select Date From">
        </div>
        <div class="col-xs-2">
          <input type="text" class="form-control datepicker" id="dateTo" placeholder="Select Date To">
        </div>
        <div class="col-xs-2">
          @if(Request::segment(4))
          <input type="text" class="form-control" id="gmc" placeholder="GMC" value="{{ Request::segment(4) }}">
          @else
          <input type="text" class="form-control" id="gmc" placeholder="GMC">
          @endif
        </div>
        <div class="col-xs-2">
          @if(Request::segment(5))
          <input type="text" class="form-control" id="proses" placeholder="PROSES" value="{{ Request::segment(5) }}">
          @else
          <input type="text" class="form-control" id="proses" placeholder="PROSES">
          @endif
        </div>
        <div class="col-xs-2">
          <button class="btn btn-primary" id="btn_search" onclick="drawGrafik()"><i class="fa fa-search"></i>&nbsp;Search</button>
        </div>
      </div>
    </div>
    <div class="col-xs-12" style="padding-top: 20px" id="chart_div">
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    drawGrafik();

    $('.datepicker').datepicker({
      autoclose: true,
      format: "dd-mm-yyyy",
    });
  });

  function drawGrafik() {
    var gmc = $("#gmc").val();
    var proses = $("#proses").val();

    if (gmc == '' || proses == '') {
      return false;
    }

    $("#loading").show();

    $.get('{{ url("winds/fetch/grafik_trendline") }}/'+gmc+'/'+proses, function(result) {
      $("#loading").hide();
      $("#judul").text(result.item_detail[0].gmc+" - "+result.item_detail[0].deskripsi+" ( "+result.item_detail[0].proses+ " ) ");

      var awal_obj = {};
      var dt = [];
      $.each(result.datas, function(key, value) {
        dt.push(value.tanggal);
        var name = value.poin_cek, val = value.awal;
        if (awal_obj[name]) {
          awal_obj[name].push(parseFloat(val));
        } else {
          awal_obj[name] = [parseFloat(val)];
        }
      })

      var tengah_obj = {};
      $.each(result.datas, function(key, value) {
        var name = value.poin_cek, val = value.tengah;
        if (tengah_obj[name]) {
          tengah_obj[name].push(parseFloat(val));
        } else {
          tengah_obj[name] = [parseFloat(val)];
        }
      })

      var akhir_obj = {};
      $.each(result.datas, function(key, value) {
        var name = value.poin_cek, val = value.akhir;
        if (akhir_obj[name]) {
          akhir_obj[name].push(parseFloat(val));
        } else {
          akhir_obj[name] = [parseFloat(val)];
        }
      })

      var min_obj = {};
      $.each(result.datas, function(key, value) {
        var name = value.poin_cek, val = value.min;
        if (min_obj[name]) {
          min_obj[name].push(parseFloat(val));
        } else {
          min_obj[name] = [parseFloat(val)];
        }
      })

      var max_obj = {};
      $.each(result.datas, function(key, value) {
        var name = value.poin_cek, val = value.max;
        if (max_obj[name]) {
          max_obj[name].push(parseFloat(val));
        } else {
          max_obj[name] = [parseFloat(val)];
        }
      })

      // --------------------------------- //

      var master_arr = [];

      $.each(awal_obj, function(key, value) {
        temp_arr = [];

        temp_arr.push(awal_obj[key]);
        temp_arr.push(tengah_obj[key]);
        temp_arr.push(akhir_obj[key]);
        temp_arr.push(min_obj[key]);
        temp_arr.push(max_obj[key]);

        master_arr.push(temp_arr);
      })

      var cat = [];
      for ( var property in awal_obj ) {
        cat.push(property ); 
      }

      $("#chart_div").empty();

      $.each(master_arr, function(key, value) {
        $("#chart_div").append('<div id="chart'+key+'"></div>');

        Highcharts.chart('chart'+key, {

          title: {
            text: 'Trendline '+cat[key]
          },

          yAxis: {
            title: {
              text: 'Trend (mm)'
            }
          },

          xAxis: {
            categories: dt
          },

          legend: {

          },

          plotOptions: {
            series: {

            }
          },

          series: [{
            name: 'Awal',
            data: master_arr[key][0],
            color : '#7bdaed'
          }, {
            name: 'Tengah',
            data: master_arr[key][1],
            color : '#8a7bed'
          }, {
            name: 'Akhir',
            data: master_arr[key][2],
            color : '#cb7bed'
          }, {
            name: 'Min',
            data: master_arr[key][3],
            color: '#f54747',
            dashStyle: 'longdash',
            marker:{
              enabled:false
            },
            dataLabels:{ 
              enabled: true,
              format: 'Min'
            }
          }, {
            name: 'Max',
            data: master_arr[key][4],
            color: '#f54747',
            dashStyle: 'longdash',
            marker:{
              enabled:false
            },
            dataLabels:{ 
              enabled: true,
              format: 'Max'
            }
          }],

          responsive: {
            rules: [{
              condition: {
                maxWidth: 500
              }
            }]
          },

          credits: {enabled : false}

        });
      })
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

</script>

@endsection