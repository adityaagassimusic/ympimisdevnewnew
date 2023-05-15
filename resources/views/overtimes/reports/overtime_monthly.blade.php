@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .morecontent span {
    display: none;
  }
  .morelink {
    display: block;
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
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }
  #queueTable.dataTable {
    margin-top: 0px!important;
  }
  #loading, #error { display: none; }
  .description-block {
    margin-top: 0px
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
    <div class="col-xs-2 pull-right">
      <div class="input-group date">
        <div class="input-group-addon bg-purple" style="border: none;">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Pilih Tanggal">
      </div>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;">
      <div id="over_control" style="width: 100%; height: 600px;"></div>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;">
      <div class="box box-solid" style="background-color: rgb(240,240,240);">
        <table style="width: 100%;">
          <tr>
            <td width="1%">
              <div class="description-block border-right" style="color: #2fe134">
                <h5 class="description-header" style="font-size: 48px;">
                  <span class="description-percentage" id="tot_budget"></span>
                </h5>      
                <span class="description-text" style="font-size: 32px;">Total Forecast<br><span>単月見込み</span></span>   
              </div>
            </td>
            <td width="1%">
              <div class="description-block border-right" style="color: #7300ab" >
                <h5 class="description-header" style="font-size: 48px; ">
                  <span class="description-percentage" id="tot_act"></span>
                </h5>      
                <span class="description-text" style="font-size: 32px;">Total Actual<br><span >単月実績</span></span>   
              </div>
            </td>
            <td width="1%">
              <div class="description-block border-right text-green" id="diff_text">
                <h5 class="description-header" style="font-size: 48px;">
                  <span class="description-percentage" id="tot_diff"></span>
                </h5>      
                <span class="description-text" style="font-size: 32px;">Diff(Act-Fq)</span>
                <br><span class="description-text" style="font-size: 32px;">差異</span>   
              </div>
            </td>
            <td width="1%">
              <div class="description-block border-right" style="color: #2fe134">
                <h5 class="description-header" style="font-size: 48px;">
                  <span class="description-percentage" id="avg_fc"></span>
                </h5>      
                <span class="description-text" style="font-size: 32px;">Forecast Average<br><span >見込みの平均値</span></span>   
              </div>
            </td>
            <td width="1%">
              <div class="description-block border-right text-yellow">
               <h5 class="description-header" style="font-size: 48px;">
                <span class="description-percentage" id="avg"></span>
              </h5>      
              <span class="description-text" style="font-size: 32px;">Average<br><span >平均</span></span>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>
</section>


<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 style="float: right; " id="modal-title"></h4> 
        <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="example2">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>No</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Total Lembur (jam)</th>
                  <th>Keperluan</th>
                </tr>
              </thead>
              <tbody id="tabelDetail"></tbody>
              <tfoot>

                <th colspan="3" style="font-weight: bold; size: 25px; text-align: center;">TOTAL </th>
                <th id="tot" style="font-weight: bold; size: 25px"></th>
                <th  style="font-weight: bold; size: 25px"></th>

              </tfoot>
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

  var arr_ot = [];
  var arr_cc = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    drawChart();

    setInterval(drawChart, 1000*60*120);
  });



  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function drawChart() {

    var tanggal = $('#tgl').val();

    var data = {
      tgl:tanggal
    }

    $.get('{{ url("fetch/report/overtime_report_control") }}', data, function(result) {
      var xCategories2 = [];
      var seriesDataBudget = [];
      var seriesDataAktual = [];
      var budgetHarian = [];
      var ctg, tot_act = 0, avg = 0, tot_budget = 0, avg_fc = 0;
      var tot_day_budget = 0, tot_diff;

      var new_arr = [];

      arr_ot = result.ot_detail;

      var arr_temp = [];

      arr_cc = result.tmp_cc;
      $.each(result.ot_detail, function(key, value) {
        if (!value.total_ot) {
          var ot = parseInt(value.TOTAL_OVT_PLAN) / 60;
        } else {
          var ot = parseInt(value.total_ot) / 60;
        }

        var temp = 0;
        $.each(result.tmp_cc, function(key2, value2) {
          if (value.cost_center == value2.cost_center_name && temp == 0) {
            new_arr.push({'cost_center' : value2.cost_center_department, 'total_ot' : ot});
            temp = 1;
          }
        })

        // if (temp == 0) {
        //   arr_temp.push({'cost_center' : value.cost_center, 'cc2' : value.Emp_no, 'total_ot' : ot});
        // }

      })

      var totals = [];
      new_arr.reduce(function(res, value) {
        if (!res[value.cost_center]) {
          res[value.cost_center] = { cost_center: value.cost_center, ot: 0 };
          totals.push(res[value.cost_center])
        }
        res[value.cost_center].ot += value.total_ot;
        return res;
      }, {});

      $.each(result.cc, function(key, value) {
        var stat = 0;
        budgetHarian.push(Math.round(value.fq * 100) / 100);
        seriesDataBudget.push(Math.round(value.bdg * 100) / 100);

        if(xCategories2.indexOf(value.cost_center_department) === -1){
          xCategories2[xCategories2.length] = value.cost_center_department;
        }

        $.each(totals, function(key2, value2) {
          if (value.cost_center_department == value2.cost_center) {
            stat = 1;
            seriesDataAktual.push(Math.round(value2.ot * 100) / 100);
            tot_act += value2.ot;
          }
        })

        if (stat == 0) {
          seriesDataAktual.push(Math.round(0 * 100) / 100);
        }

        tot_budget += value.bdg;
        tot_day_budget += value.fq;

      })

      // for(var i = 0; i < result.semua.length; i++){
      //   ctg = result.semua[i].cost_center_name;
      //   tot_act += result.semua[i].actual;
      //   tot_budget += result.semua[i].budget;
      //   tot_day_budget += result.semua[i].forecast;

      //   seriesDataBudget.push(Math.round(result.semua[i].budget * 100) / 100);
      //   seriesDataAktual.push(Math.round(result.semua[i].actual * 100) / 100);
      //   budgetHarian.push(Math.round(result.semua[i].forecast * 100) / 100);
      //   if(xCategories2.indexOf(ctg) === -1){
      //     xCategories2[xCategories2.length] = ctg.toUpperCase();
      //   }
      // }

      tot_diff = tot_act - tot_day_budget;

      tot_budget = Math.round(tot_budget * 100) / 100;
      tot_day_budget = Math.round(tot_day_budget * 100) / 100;
      tot_act = Math.round(tot_act * 100) / 100;
      tot_diff = Math.round(tot_diff * 100) / 100;

      var tot_budget2 = tot_budget.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
      var tot_day_budget2 = tot_day_budget.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
      var tot_act2 = tot_act.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
      var tot_diff2 = tot_diff.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

      $("#tot_budget").text(tot_day_budget2);
      $("#tot_day_budget").text(tot_budget2);
      $("#tot_act").text(tot_act2);

      if (tot_diff > 0) {
        $('#diff_text').removeClass('text-green').addClass('text-red');
        $("#tot_diff").html("+ "+tot_diff2);
      }
      else {
        $('#diff_text').removeClass('text-red').addClass('text-green');
        $("#tot_diff").html(tot_diff2);
      }

      avg = tot_act / result.emp_total.jml;
      avg = Math.round(avg * 100) / 100;

      avg_fc = tot_day_budget / result.emp_fc.jml_fc;
      avg_fc = Math.round(avg_fc * 100) / 100;
      $("#avg").html(avg);
      $("#avg_fc").html(avg_fc);

      Highcharts.chart('over_control', {
        chart: {
          type: 'column',
          backgroundColor: null
        },
        title: {
          text: 'Overtime Control - Forecast<br><center style="font-size: 24px;">'+ result.dt +'</center>',
          style: {
            fontSize: '30px',
            fontWeight: 'bold'
          }
        },
        credits:{
          enabled:false
        },
        legend: {
          itemStyle: {
            fontWeight: 'bold',
            fontSize: '20px'
          }
        },
        yAxis: {
          min:0,
          tickPositioner: function () {
            var count = 0;
            var arr = [];
            var maxDeviation = Math.ceil((this.dataMax*1.3)/500)*500;
            for (var i = 0; i <= maxDeviation/500; i++) {
              arr.push(count);
              count += 500;
            }

            return arr;
          },
          allowDecimals: false,
          title: {
            text: 'Hour(s)'
          }
        },
        xAxis: {
          labels: {
            style: {
              fontSize: '12px',
              fontWeight: 'bold'
            }
          },
          categories: xCategories2
        },
        tooltip: {
          formatter: function () {
            return '<b>' + this.series.name + '</b><br/>' +
            this.point.y + ' ' + this.series.name.toLowerCase();
          }
        },
        plotOptions: {
          column: {
            pointPadding: 0.93,
            cursor: 'pointer',
            point: {
              events: {
                click: function () {
                  modalTampil(this.category, result.ot_detail[0].dt);
                }
              }
            },
            minPointLength: 3,
            dataLabels: {
              allowOverlap: true,
              enabled: true,
              y: -25,
              style: {
                color: 'black',
                fontSize: '13px',
                textOutline: false,
                fontWeight: 'bold',
              },
              rotation: -90
            },
            pointWidth: 15,
            pointPadding: 0.97,
            groupPadding: 0.97,
            borderWidth: 0.97,
            groupPadding: 0.1,
            animation: false,
            opacity: 1
          },
          scatter : {
            dataLabels: {
              enabled: false
            },
            animation: false
          }
        },
        series: [{
          name: 'Forecast Accumulative',
          data: budgetHarian,
          color: "#02ff17"
        }, {
          name: 'Actual Accumulative',
          data: seriesDataAktual,
          color: "#ae05f7"
        },
        ]
      });
    });
}

function total_budget(costCenter, date) {
  $.ajax({
    type: "GET",
    url: '{{url("fetch/cc/budget")}}',
    data: {
      cc : costCenter,
      tgl : date
    },
    dataType: 'json',
    success: function(data) {
      $("#modal-title").html(costCenter+" ( &Sigma; Budget "+data.datas[0].budget+" )");
    }
  })

}

function modalTampil(costCenter, date) {
  $("#myModal").modal('show');
  var showChar = 100;
  var ellipsestext = "...";
  var moretext = "Show more >";
  var lesstext = "< Show less";

  total_budget(costCenter, date);

  $("#tabelDetail").empty();
  var no = 1;
  var jml = 0;

  var dataT = '';
  var no = 1;

  arr_detail = [];

  $.each(arr_ot, function(key, value) {
    var tmp = 0;
    $.each(arr_cc, function(key2, value2) {
      if (value.cost_center == value2.cost_center_name) {
        if (value2.cost_center_department == costCenter) {

          if (!value.total_ot) {
            var ot = parseInt(value.TOTAL_OVT_PLAN) / 60;
          } else {
            var ot = parseInt(value.total_ot) / 60;
          }
          if (tmp == 0) {
            tmp = 1;
            arr_detail.push({'nik' : value.Emp_no, 'name' : value.Full_name, 'jam' : ot, 'kep' : value.keperluan});
          }
        }
      }

    })
  })  

  for (var i = 0; i < arr_detail.length; i++) {
    var jam = arr_detail[i].jam;

    dataT += '<tr>';
    dataT += '<td>'+ no++; +'</td>';
    dataT += '<td>'+ arr_detail[i].nik +'</td>';
    dataT += '<td>'+ arr_detail[i].name +'</td>';           
    dataT += '<td>'+ jam.toFixed(2) +'</td>';
    dataT += '<td style="text-align:left"> <span class="more">'+ arr_detail[i].kep +'</span></td>';
    dataT += '</tr>';
    jml += parseFloat(arr_detail[i].jam);
  }
  $("#tabelDetail").append(dataT);

  $('.more').each(function() {
    var content = $(this).html();

    if(content.length > showChar) {

      var c = content.substr(0, showChar);
      var h = content.substr(showChar, content.length - showChar);

      var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

      $(this).html(html);
    }

  });

  $(".morelink").click(function(){
    if($(this).hasClass("less")) {
      $(this).removeClass("less");
      $(this).html(moretext);
    } else {
      $(this).addClass("less");
      $(this).html(lesstext);
    }
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    return false;
  });

  $("#tot").text(jml);
}

$('#tgl').datepicker({
  autoclose: true,
  format: "dd-mm-yyyy",
});

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