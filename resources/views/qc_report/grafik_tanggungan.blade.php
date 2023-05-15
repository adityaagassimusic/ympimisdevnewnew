@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-header {
    background-color: #61258e !important;
    padding: 10px;
    color: white;
  }

  .content-header > h1 {
    margin: 0;
    font-size: 65px;
    text-align: center;
    font-weight: bold;
  }

  .content-header > h2 {
    margin: 0;
    font-size: 30px;
    text-align: center;
    font-weight: bold;
  }

  .content-header .isi {
    margin: 0;
    font-size: 60px;
    text-align: center;
    vertical-align: middle;
    font-weight: bold;
  }

  .content-wrapper{
    padding: 0 !important;
  }

  .box-header{
    text-transform: uppercase;
  }

  .text-yellow{
    font-size: 20px !important;
    font-weight: bold;
  }


</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
  <div class="row" style="padding: 0">
    <div class="col-xs-12">
      <div class="row">
              <div class="col-xs-12" style="padding-top: 0px;margin-top: 10px">
                <div class="col-xs-12" style="">
                  <div id="container_car" style="width: 100%;"></div>
                </div>
                <div class="col-xs-6" style="padding: 0;padding-left: 10px;margin-top: 20px;">
                  <div id="container_cpar" style="width: 100%;"></div>
                </div>
                <div class="col-xs-6" style="">
                  <div id="container_verifikasi" style="margin-top: 20px;"></div>
                </div>
              </div>
            </div>
        </div>
    </div>
  </section>
  @endsection
  @section('scripts')
  <script src="{{ url("js/highstock.js")}}"></script>
  <script src="{{ url("js/exporting.js")}}"></script>
  <script src="{{ url("js/export-data.js")}}"></script>
  <script src="{{ url("js/accessibility.js")}}"></script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    jQuery(document).ready(function(){
      getData();

      setInterval(getData, 60000);

    });

    function getData() {
      $.get('{{ url("fetch/qc_report/grafik_tanggungan") }}', function(result, status, xhr){
        if (result.status) {

          var tanggungan_cpar = [];
          var tanggungan_car = [];
          var tanggungan_qa = [];

          $.each(result.emp, function(keyemp, valueemp){
            $.each(result.cpar, function(key, value){
            
              if (value.posisi == "staff") {
                if (value.staff == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }else if (value.posisi == "leader") {
                if (value.leader == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }else if (value.posisi == "chief") {
                if (value.chief == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }else if (value.posisi == "foreman") {
                if (value.foreman == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }else if (value.posisi == "manager") {
                if (value.manager == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }else if (value.posisi == "dgm") {
                if (value.dgm == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }else if (value.posisi == "gm") {
                if (value.gm == valueemp.employee_id) {
                   tanggungan_cpar.push(valueemp.name);                    
                }
              }
            });


            $.each(result.verifikasi_qa, function(key, value){
                if (value.posisi == "QA") {
                  if (value.staff != null) {
                    if (value.staff == valueemp.employee_id) {
                     tanggungan_qa.push(valueemp.name);                    
                    }
                  }else{
                    if (value.leader == valueemp.employee_id) {
                     tanggungan_qa.push(valueemp.name);                    
                    }
                  }
                }else if (value.posisi == "QA2"){
                  if (value.chief != null) {
                    if (value.chief == valueemp.employee_id) {
                     tanggungan_qa.push(valueemp.name);                    
                    }
                  }else{
                    if (value.foreman == valueemp.employee_id) {
                     tanggungan_qa.push(valueemp.name);                    
                    }
                  }
                }else if (value.posisi == "QAmanager"){
                  if (value.manager == valueemp.employee_id) {
                   tanggungan_qa.push(valueemp.name);                    
                  }
                }
              
            });

            $.each(result.car, function(key, value){
              if (value.kategori_meeting == "CloseRevised") {
                if (value.posisi == 'bagian') {
                  if (value.employee_id == valueemp.employee_id) {
                   tanggungan_car.push(valueemp.name);                    
                  }
                }else{
                  if (value.pic == valueemp.employee_id) {
                   tanggungan_car.push(valueemp.name);                    
                  }
                }
                
              } else {
                if (value.kategori_meeting != "Close") {
                  if (value.posisi == 'qa' || value.posisi == 'staff' || value.posisi == 'foreman') {
                    if (value.pic == valueemp.employee_id) {
                     tanggungan_car.push(valueemp.name);                    
                    }
                  } 
                  else if (value.posisi == 'manager' || value.posisi == 'bagian') {
                    if (value.employee_id == valueemp.employee_id) {
                     tanggungan_car.push(valueemp.name);                    
                    }
                  }
                  else if (value.posisi == 'coordinator' || value.posisi == 'foreman2') {
                    if (value.verifikatorforeman == valueemp.employee_id || value.verifikatorcoordinator == valueemp.employee_id) {
                     tanggungan_car.push(valueemp.name);                    
                    }
                  }
                }
              }
            });

          });

          result_cpar = tanggungan_cpar.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), Object.create(null));
          result_car = tanggungan_car.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), Object.create(null));
          result_verifikasi = tanggungan_qa.reduce((a, c) => (a[c] = (a[c] || 0) + 1, a), Object.create(null));

          var name_cpar = [];
          var data_cpar = [];
          var data_pie_cpar = [];

          var name_car = [];
          var data_car = [];
          var data_pie_car = [];

          var name_verifikasi = [];
          var data_verifikasi = [];
          var data_pie_verifikasi = [];

          $.each(result_cpar, function(key, value) {
            name_cpar.push(key);
            data_cpar.push(value);
            data_pie_cpar.push({
              "name" : key,
              "y" : value
            });

          });

          sortByProperty(data_pie_cpar,"y","DESC");
          // sortByProperty(data_cpar,"y","DESC");

           $.each(result_car, function(key, value) {
            name_car.push(key);
            data_car.push(value);
            data_pie_car.push({
              "name" : key,
              "y" : value
            });
          });
          sortByProperty(data_pie_car,"y","DESC");

          $.each(result_verifikasi, function(key, value) {
            name_verifikasi.push(key);
            data_verifikasi.push(value);
            data_pie_verifikasi.push({
              "name" : key,
              "y" : value
            });
          });
          
          sortByProperty(data_pie_verifikasi,"y","DESC");

          var data_pie_cpar_sort = [];
          name_cpar = [];

          for(var i = 0; i < data_pie_cpar.length;i++){
            data_pie_cpar_sort.push({
              y:data_pie_cpar[i].y,
              name:data_pie_cpar[i].name,
            });
            name_cpar.push(data_pie_cpar[i].name);
          }

          var data_pie_car_sort = [];
          name_car = [];

          for(var i = 0; i < data_pie_car.length;i++){
            data_pie_car_sort.push({
              y:data_pie_car[i].y,
              name:data_pie_car[i].name,
            });
            name_car.push(data_pie_car[i].name);
          }

          var data_pie_verifikasi_sort = [];
          name_verifikasi = [];

          for(var i = 0; i < data_pie_verifikasi.length;i++){
            data_pie_verifikasi_sort.push({
              y:data_pie_verifikasi[i].y,
              name:data_pie_verifikasi[i].name,
            });
            name_verifikasi.push(data_pie_verifikasi[i].name);
          }

          // $.each(result, function(key2,value2){
          //   name_cpar.push(key2);
          //   data_cpar.push(parseInt(value2));
          // });

          Highcharts.chart('container_cpar', {
            chart: {
              backgroundColor: 'rgb(80,80,80)',
              type: 'column',
              // options3d: {
              //   enabled: true,
              //   alpha: 45,
              //   beta: 0
              // }
            },
            title: {
              text: 'PIC Tanggungan CPAR'
            },
            xAxis: {
              type: 'category',
              categories: name_cpar,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            tooltip: {
              pointFormat: '{series.name}: <b>{point.y} CPAR</b>'
            },
            accessibility: {
              point: {
                valueSuffix: '%'
              }
            },
            legend: {
              enabled: false,
              symbolRadius: 1,
              borderWidth: 1
            },
            plotOptions: {
              series: {
                allowPointSelect: true,
                cursor: 'pointer',
                edgeWidth: 1,
                edgeColor: 'rgb(126,86,134)',
                depth: 35,
                dataLabels: {
                  enabled: true,
                  format: '<b>{point.y}</b>',
                  style:{
                    fontSize:'0.8vw',
                    textOutline:0
                  },
                  color:'white'
                },
                showInLegend: true
              }
            },
            credits: {
              enabled: false
            },
            exporting: {
              enabled: true
            },
            series: [
              {
                name: 'Total CPAR',
                colorByPoint: true,
                data: data_pie_cpar_sort
              }
            ]
          });


          Highcharts.chart('container_car', {
            chart: {
              backgroundColor: 'rgb(80,80,80)',
              type: 'column',
              // options3d: {
              //   enabled: true,
              //   alpha: 45,
              //   beta: 0
              // }
            },
            title: {
              text: 'PIC Tanggungan CAR'
            },
            xAxis: {
              type: 'category',
              categories: name_car,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            tooltip: {
              pointFormat: '{series.name}: <b>{point.y} CAR</b>'
            },
            accessibility: {
              point: {
                valueSuffix: '%'
              }
            },
            legend: {
              enabled: false,
              symbolRadius: 1,
              borderWidth: 1
            },
            plotOptions: {
              column: {
                allowPointSelect: true,
                cursor: 'pointer',
                edgeWidth: 1,
                edgeColor: 'rgb(126,86,134)',
                depth: 35,
                dataLabels: {
                  enabled: true,
                  format: '<b>{point.y}</b>',
                  style:{
                    fontSize:'0.8vw',
                    textOutline:0
                  },
                  color:'white'
                },
                showInLegend: true
              }
            },
            credits: {
              enabled: false
            },
            exporting: {
              enabled: true
            },
            series: [
              {
                name: 'Total CAR',
                colorByPoint: true,
                data: data_pie_car_sort
              }
            ]
          });

          Highcharts.chart('container_verifikasi', {
            chart: {
              backgroundColor: 'rgb(80,80,80)',
              type: 'column',
              // options3d: {
              //   enabled: true,
              //   alpha: 45,
              //   beta: 0
              // }
            },
            title: {
              text: 'PIC Verifikasi QA'
            },
            xAxis: {
              type: 'category',
              categories: name_verifikasi,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            tooltip: {
              pointFormat: '{series.name}</b>'
            },
            accessibility: {
              point: {
                valueSuffix: '%'
              }
            },
            legend: {
              enabled: false,
              symbolRadius: 1,
              borderWidth: 1
            },
            plotOptions: {
              column: {
                allowPointSelect: true,
                cursor: 'pointer',
                edgeWidth: 1,
                edgeColor: 'rgb(126,86,134)',
                depth: 35,
                dataLabels: {
                  enabled: true,
                  format: '<b>{point.y} </b>',
                  style:{
                    fontSize:'0.8vw',
                    textOutline:0
                  },
                  color:'white'
                },
                showInLegend: true
              }
            },
            credits: {
              enabled: false
            },
            exporting: {
              enabled: true
            },
            series: [
              {
                name: 'Total Verifikasi QA',
                colorByPoint: true,
                data: data_pie_verifikasi_sort
              }
            ]
          });
        }
      });
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



function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

function sortByProperty(arr, property, order="ASC") {
  arr.forEach((item) => item.tempProp = sanitizeToSort(item[property]));
  arr.sort((a,b) => order === "ASC" ?
      a.tempProp > b.tempProp ?  1 : a.tempProp < b.tempProp ? -1 : 0
    : a.tempProp > b.tempProp ? -1 : a.tempProp < b.tempProp ?  1 : 0
  );
  arr.forEach((item) => delete item.tempProp);
  return arr;
}

function sanitizeToSort(str) {
  return str;
}

  </script>
  @endsection