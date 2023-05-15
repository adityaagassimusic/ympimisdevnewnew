@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
	 <div class="col-xs-12">
	    <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
	      <div class="box-body">
	        <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
	          <div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
	            <span style="font-size: 25px;color: black;width: 25%;">HUMAN RESOURCE DEPARTMENT</span>
	            <span style="font-size: 25px;color: black;width: 25%;">人事部</span>
	          </div>
	        </div>
	        <div class="col-xs-2" style="padding-top: 5px; padding-bottom: 5px">
	          <div class="input-group date">
	            <div class="input-group-addon bg-green" style="border: none;">
	              <i class="fa fa-calendar"></i>
	            </div>
	            <input type="text" class="form-control datepicker" id="dateto" placeholder="Pilih Bulan" onchange="drawChart()">
	          </div>
	        </div> 
	        <div class="col-md-12">
		        <div class="col-md-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
		            <div id="chart" style="width: 100%"></div>
		        </div>
		      </div>
          <div class="col-md-12">
            <div class="col-md-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
                <div id="chart2" style="width: 100%"></div>
            </div>
          </div>
	      </div>
	    </div>
	  </div>   
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


	jQuery(document).ready(function() {		
	$('body').toggleClass("sidebar-collapse");
	$('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
   });
	drawChart();
  drawChart2();
	});	

	function drawChart() {
    var dateto = $('#dateto').val();

    var data = {
      dateto: dateto
    };

    $.get('{{ url("fetch/man_power") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var department = [], outsource = [], permanent = [];

          $.each(result.datas, function(key, value) {
            department.push(value.department);
            outsource.push(parseInt(value.outsource));
            permanent.push(parseInt(value.permanent));
          });

          var date = new Date();
          
          $('#chart').highcharts({
            chart: {
            type: 'column'
    		    },
    		    title: {
    		        text: 'Data Karyawan Outsource & Permanent'
    		    },
    		    xAxis: {
    		        type: 'category',
                categories: department
    		    },
    		    yAxis: {
    		        min: 0,
    		        title: {
    		            text: 'Total Man Power'
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
    		        align: 'right',
    		        x: -30,
    		        verticalAlign: 'top',
    		        y: 25,
    		        floating: true,
    		        backgroundColor:
    		            Highcharts.defaultOptions.legend.backgroundColor || 'white',
    		        borderColor: '#CCC',
    		        borderWidth: 1,
    		        shadow: false
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
    		            }
    		        }
    		    },
    		    series: [{
    		        name: 'OUTSOURCING',
                data: outsource
    		    }, {
    		        name: 'PERMANENT',
                data: permanent
    		    }]
              })
            } else{
              alert('Attempt to retrieve data failed');
            }
          }
        })
      }

  function drawChart2() {
    var dateto = $('#dateto').val();

    var data = {
      dateto: dateto
    };

    $.get('{{ url("fetch/man_power") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var department = [], contract1 = [], contract2 = [];

          $.each(result.datas, function(key, value) {
            department.push(value.department);
            contract1.push(parseInt(value.contract1));
            contract2.push(parseInt(value.contract2));
          });

          var date = new Date();
          
          $('#chart2').highcharts({
            chart: {
            type: 'column'
            },
            title: {
                text: 'Data Karyawan Contract'
            },
            xAxis: {
                type: 'category',
                categories: department
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total Man Power'
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
                align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
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
                    }
                }
            },
            series: [{
                name: 'CONTRACT 1',
                data: contract1
            }, {
                name: 'CONTRACT 2',
                data: contract2
            }]
              })
            } else{
              alert('Attempt to retrieve data failed');
            }
          }
        })
      }



        Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#f5b041', '#82e0aa'],
        };
        Highcharts.setOptions(Highcharts.theme);

</script>

@endsection