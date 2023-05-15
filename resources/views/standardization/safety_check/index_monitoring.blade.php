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
    padding:3px;
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
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i> Please wait a moment...</span>
    </p>
  </div>
  <div class="col-xs-3" style="padding-top: 5px;">
   <div class="small-box" style="background: #00ff73; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="modalTampil('Production','','Sudah')">
    <div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
      <h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>PRODUCTION - SUDAH MENGISI</b></h3>
      <h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"></h3>
      <span style="font-size: 3.5vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="prd_sudah_isi">0</span> <span style="font-size: 3.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="prd_persen_sudah_isi">0 %</span>
    </div>
    <div class="icon" style="padding-top: 0;font-size:8vh;">
      <i class="fa fa-check"></i>
    </div>
  </div>

  <div class="small-box" style="background: #ff4545; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="modalTampil('Production','','Belum')">
    <div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
      <h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>PRODUCTION - BELUM MENGISI</b></h3>
      <h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"></h3>
      <span style="font-size: 3.5vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="prd_belum_isi">0</span> <span style="font-size: 3.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="prd_persen_belum_isi">0 %</span>
    </div>
    <div class="icon" style="padding-top: 0;font-size:8vh;">
      <i class="fa fa-close"></i>
    </div>
  </div>
  <hr>
  <div class="small-box" style="background: #00ff73; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="modalTampil('Office','','Sudah')">
    <div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
      <h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>OFFICE - SUDAH MENGISI</b></h3>
      <h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"></h3>
      <span style="font-size: 3.5vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="ofc_sudah_isi">0</span> <span style="font-size: 3.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="ofc_persen_sudah_isi">0 %</span>
    </div>
    <div class="icon" style="padding-top: 0;font-size:8vh;">
      <i class="fa fa-check"></i>
    </div>
  </div>

  <div class="small-box" style="background: #ff4545; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="modalTampil('Office','','Belum')">
    <div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
      <h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>OFFICE - BELUM MENGISI</b></h3>
      <h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"></h3>
      <span style="font-size: 3.5vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="ofc_belum_isi">0</span> <span style="font-size: 3.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="ofc_persen_belum_isi">0 %</span>
    </div>
    <div class="icon" style="padding-top: 0;font-size:8vh;">
      <i class="fa fa-close"></i>
    </div>
  </div>
</div>

<div class="col-xs-2" style="padding-top: 5px;">
  <div class="input-group date">
    <div class="input-group-addon bg-purple" style="border: none;">
      <i class="fa fa-calendar"></i>
    </div>
    <input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Pilih Bulan">
  </div>
</div>

<div class="col-xs-7" style="padding-top: 5px;">
  <a href="{{ url('index/safety_check/form') }}" class="btn btn-success pull-right">Holiday Safety Check Form</a>
</div>
<div class="col-xs-9" style="padding-top: 5px;">
  <div id="contain2" style="width: 100%; height: 44vh;"></div>
  <div id="contain" style="width: 100%; height: 44vh;"></div>
</div>
</div>

<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b id="modal_title">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered table-stripped table-responsive" style="width: 100%; display: none;" id="tableDetail">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th style="width: 2%">No</th>
                  <th style="width: 5%">NIK</th>
                  <th style="width: 20%">Nama</th>
                  <th style="width: 30%">Bagian</th>
                  <th style="width: 10%">Status</th>
                  <th style="width: 5%">#</th>
                </tr>
              </thead>
              <tbody id="bodyDetail"></tbody>
            </table>

            <table class="table table-bordered table-stripped table-responsive" style="width: 100%; display: none;" id="tablePrdDetail">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th style="width: 2%">No</th>
                  <th style="width: 20%">Departemen</th>
                  <th style="width: 15%">Group</th>
                  <th style="width: 5%">NIK</th>
                  <th style="width: 30%">Nama</th>
                  <th style="width: 10%">Status</th>
                  <th style="width: 5%">#</th>
                </tr>
              </thead>
              <tbody id="bodyPrdDetail"></tbody>
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

  var arr_ot = [];
  var ofc_detail = [];
  var prd_detail = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    drawChart();
  });

  $('#tgl').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });

  function drawChart() {
    $("#loading").show();
    var data = {
      tgl : $("#tgl").val()
    }

    $.get('{{ url("fetch/safety_check/monitoring") }}', data, function(result) {
      $("#loading").hide();

      var cat_ofc = [];
      var ofc_sudah = [];
      var ofc_belum = [];

      var tot_ofc_sudah = 0;
      var tot_ofc_belum = 0;

      var cat_prd = [];
      var prd_sudah = [];
      var prd_belum = [];

      var tot_prd_sudah = 0;
      var tot_prd_belum = 0;

      ofc_detail = result.office_detail;
      prd_detail = result.production_detail;

      $.each(result.office, function(index, value){
        cat_ofc.push(value.bagian2);
        ofc_sudah.push(parseInt(value.sudah));
        ofc_belum.push(parseInt(value.tot));

        tot_ofc_sudah += parseInt(value.sudah);
        tot_ofc_belum += parseInt(value.tot);
      })

      $.each(result.production, function(index2, value2){
        cat_prd.push(value2.bagian);
        prd_sudah.push(parseInt(value2.sudah));
        prd_belum.push(value2.jml_grp);

        tot_prd_sudah += parseInt(value2.sudah);
        tot_prd_belum += parseInt(value2.jml_grp);
      })

      var tot_sudah = tot_ofc_sudah + tot_prd_sudah;
      var tot_belum = (tot_ofc_belum - tot_ofc_sudah) + (tot_prd_belum - tot_prd_sudah);

      // $("#total_sudah_isi").text(tot_sudah);
      // $("#total_belum_isi").text(tot_belum);
      // $("#total_approval_isi").text(tot_approval);

      // $("#persen_sudah_isi").text((tot_sudah / (tot_ofc_belum + tot_prd_belum) * 100).toFixed(2) +" %");
      // $("#persen_belum_isi").text((tot_belum / (tot_ofc_belum + tot_prd_belum) * 100).toFixed(2) +" %");
      // $("#persen_approval_isi").text((tot_approval / (tot_ofc_belum + tot_prd_belum) * 100).toFixed(2) +" %");

      $("#prd_sudah_isi").text(tot_prd_sudah);
      $("#prd_persen_sudah_isi").text((tot_prd_sudah / tot_prd_belum * 100).toFixed(1)+' %');
      $("#prd_belum_isi").text(tot_prd_belum - tot_prd_sudah);
      $("#prd_persen_belum_isi").text(((tot_prd_belum - tot_prd_sudah) / tot_prd_belum * 100).toFixed(1)+' %');
      $("#ofc_sudah_isi").text(tot_ofc_sudah);
      $("#ofc_persen_sudah_isi").text((tot_ofc_sudah / tot_ofc_belum * 100).toFixed(1)+' %');
      $("#ofc_belum_isi").text(tot_ofc_belum - tot_ofc_sudah);
      $("#ofc_persen_belum_isi").text(((tot_ofc_belum - tot_ofc_sudah) / tot_ofc_belum * 100).toFixed(1)+' %');

      Highcharts.chart('contain', {
        chart: {
          type: 'column'
        },

        title: {
          text: 'Office',
          style: {
            fontSize: '20px',
            fontWeight: 'bold'
          }
        },

        xAxis: {
          categories: cat_ofc,
          gridLineWidth: 1,
          gridLineColor: 'RGB(204,255,255)',
          lineWidth:1,
          lineColor:'#9e9e9e'
        },

        yAxis: {
          allowDecimals: false,
          min: 0,
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
              fontSize:"12px"
            },
            enabled: true
          },
          opposite: true,
          type: 'linear'

        },

        tooltip: {
          formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
            this.series.name + ': ' + this.y
          }
        },

        plotOptions: {
          column: {
            cursor: 'pointer',
            point: {
              events: {
                click: function () {
                  modalTampil('Office',this.category, '');
                }
              }
            }
          },
          series: {
            borderWidth: 0,
            dataLabels: {
              enabled: true,
              format: '{point.y}'
            }
          }
        },

        series: [{
          name: 'Sudah Mengisi',
          data: ofc_sudah,
          color: "#00ff73"
        }, {
          name: 'Yang Harus Mengisi',
          data: ofc_belum,
          color: "#42a5f5"
        }],
        credits: {
          enabled: false
        }
      });


      Highcharts.chart('contain2', {
        chart: {
          type: 'column'
        },

        title: {
          text: 'Production',
          style: {
            fontSize: '20px',
            fontWeight: 'bold'
          }
        },

        xAxis: {
          categories: cat_prd,
          gridLineWidth: 1,
          gridLineColor: 'RGB(204,255,255)',
          lineWidth:1,
          lineColor:'#9e9e9e'
        },

        yAxis: {
          allowDecimals: false,
          min: 0,
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
              fontSize:"12px"
            }
          },
          opposite: true,
          type: 'linear'

        },

        tooltip: {
          formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
            this.series.name + ': ' + this.y
          }
        },

        plotOptions: {
          column: {
            cursor: 'pointer',
            point: {
              events: {
                click: function () {
                  modalTampil('Production',this.category, '');
                }
              }
            }
          },
          series: {
            borderWidth: 0,
            dataLabels: {
              enabled: true,
              format: '{point.y}'
            }
          }
        },

        series: [{
          name: 'Sudah Mengisi',
          data: prd_sudah,
          color: "#00ff73"
        }, {
          name: 'Yang Harus Mengisi',
          data: prd_belum,
          color: "#42a5f5"
        }],
        credits: {
          enabled: false
        }
      });
    });
}

function modalTampil(lokasi, kategori, stat) {
  console.log(lokasi+' '+kategori+' '+stat);
  $("#bodyPrdDetail").empty();
  $("#bodyDetail").empty();
  $("#myModal").modal('show');
  var body = '';
  var no = 1;
  var tgls = '';

  if($("#tgl").val() != '') {
    tgls = $("#tgl").val();
  } else {
    tgls = "{{ date('Y-m') }}";
  }

  if (lokasi == 'Production') {
    $('#tablePrdDetail').DataTable().clear();
    $('#tablePrdDetail').DataTable().destroy();

    $("#tableDetail").hide();
    $("#tablePrdDetail").show();
    $.each(prd_detail, function(index, value){
      if (value.bagian2 == kategori) {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+value.bagian+'</td>';
        body += '<td>'+value.group+'</td>';
        if (value.pic) {
          body += '<td>'+value.pic.split("/")[0]+'</td>';
          body += '<td>'+value.pic.split("/")[1]+'</td>';
        } else {
          body += '<td></td>';
          body += '<td></td>';
        }

        if (value.stat == 'belum') {
          body += '<td style="background-color:#ffccff">'+value.stat+'</td>';
          body += '<td></td>';
        } else {
          body += '<td style="background-color:#ccffff">'+value.stat+'</td>';
          body += '<td><a class="btn btn-primary btn-xs" href="{{ url("index/safety_check/detail") }}/'+lokasi+'/'+value.group+'/'+tgls+'" target="_blank"> Detail</a></td>';
        }

        body += '</tr>';
        no++;
      } else if (stat == 'Sudah' && value.stat == 'sudah') {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+value.bagian+'</td>';
        body += '<td>'+value.group+'</td>';
        if (value.pic) {
          body += '<td>'+value.pic.split("/")[0]+'</td>';
          body += '<td>'+value.pic.split("/")[1]+'</td>';
        } else {
          body += '<td></td>';
          body += '<td></td>';
        }

        body += '<td style="background-color:#ccffff">'+value.stat+'</td>';
        body += '<td><a class="btn btn-primary btn-xs" href="{{ url("index/safety_check/detail") }}/'+lokasi+'/'+value.group+'/'+tgls+'" target="_blank"> Detail</a></td>';

        body += '</tr>';
        no++;
      } else if (stat == 'Belum' && value.stat == 'belum') {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+value.bagian+'</td>';
        body += '<td>'+value.group+'</td>';
        if (value.pic) {
          body += '<td>'+value.pic.split("/")[0]+'</td>';
          body += '<td>'+value.pic.split("/")[1]+'</td>';
        } else {
          body += '<td></td>';
          body += '<td></td>';
        }

        body += '<td style="background-color:#ffccff">'+value.stat+'</td>';
        body += '<td></td>';

        body += '</tr>';
        no++;
      }
    })
    $("#bodyPrdDetail").append(body);

    $('#tablePrdDetail').DataTable({
      'dom': 'Bfrtip',
      'responsive':true,
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        ]
      },
      'paging': false,
      'lengthChange': false,
      'pageLength': 10,
      'searching': true,
      'ordering': false,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true
    });
  } else if (lokasi == 'Office') {
    $('#tableDetail').DataTable().clear();
    $('#tableDetail').DataTable().destroy();

    $("#tablePrdDetail").hide();
    $("#tableDetail").show();
    $.each(ofc_detail, function(index, value){
      if (value.bagian2 == kategori) {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+value.employee_id+'</td>';
        body += '<td>'+value.name+'</td>';
        body += '<td>'+value.bagian+'</td>';

        if (value.stat == 'belum') {
          body += '<td style="background-color:#ffccff">'+value.stat+'</td>';
          body += '<td></td>';
        } else {
          body += '<td style="background-color:#ccffff">'+value.stat+'</td>';
          body += '<td><a class="btn btn-primary btn-xs" href="{{ url("index/safety_check/detail") }}/'+lokasi+'/'+value.employee_id+'/'+tgls+'" target="_blank"> Detail</a></td>';
        }

        body += '</tr>';
        no++;
      } else if (stat == 'Sudah' && value.stat == 'sudah') {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+value.employee_id+'</td>';
        body += '<td>'+value.name+'</td>';
        body += '<td>'+value.bagian+'</td>';
        body += '<td style="background-color:#ccffff">'+value.stat+'</td>';
        body += '<td><a class="btn btn-primary btn-xs" href="{{ url("index/safety_check/detail") }}/'+lokasi+'/'+value.employee_id+'/'+tgls+'" target="_blank"> Detail</a></td>';

        body += '</tr>';
        no++;
      } else if (stat == 'Belum' && value.stat == 'belum') {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+value.employee_id+'</td>';
        body += '<td>'+value.name+'</td>';
        body += '<td>'+value.bagian+'</td>';
        body += '<td style="background-color:#ffccff">'+value.stat+'</td>';
        body += '<td></td>';

        body += '</tr>';
        no++;
      }
    })
    $("#bodyDetail").append(body);

    $('#tableDetail').DataTable({
      'dom': 'Bfrtip',
      'responsive':true,
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        ]
      },
      'paging': false,
      'lengthChange': false,
      'pageLength': 200,
      'searching': true,
      'ordering': false,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true
    });
  }

}

Highcharts.createElement('link', {
  href: '{{ url("fonts/UnicaOne.css")}}',
  rel: 'stylesheet',
  type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
  colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
    '#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
  chart: {
    backgroundColor: {
      linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
      stops: [
        [0, '#2a2a2b'],
        [1, '#2a2a2b']
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
  audio_error.play();
}	
</script>
@endsection