@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div id="chart_title" class="col-xs-10" style="background-color: #673ab7;">
			<center>
				<span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text"></span>
			</center>
		</div>
		<div class="col-xs-2" style="padding-top: 0.25%;">
			<div class="input-group date">
				<div class="input-group-addon bg-purple">
					<i class="fa fa-calendar-o" style="color:white"></i>
				</div>
				<select class="form-control select2" onchange="fetchChart()" name="fy" id='fy' data-placeholder="Select Fiscal Year" style="width: 100%;">
					<!-- <option value="">Select Fiscal Year</option> -->
					<option value="FY199">FY199</option>
					<option value="FY198">FY198</option>
					<option value="FY197">FY197</option>
				</select>
			</div>
		</div>
		
		<div class="col-xs-12" id="container_bulan" style="margin-top: 1%; height: 40vh;"></div>
		<div class="col-xs-4" style="padding-right: 0">
			<div id="container_defect" style="width: 100%;"></div>
		</div>
		<div class="col-xs-8" style="padding-left: 0;">
			<div id="container_hpl_defect" style="width: 100%;"></div>
		</div>

		@if($id == "wi")
		<div class="col-xs-3">
			<div id="container_fl_defect_fungsi" style="width: 100%;margin-top: 20px;"></div>
		</div>
		<div class="col-xs-3">
			<div id="container_fl_defect_visual" style="width: 100%;margin-top: 20px;"></div>
		</div>
		<div class="col-xs-3">
			<div id="container_sax_defect_fungsi" style="width: 100%;margin-top: 20px;"></div>
		</div>
		<div class="col-xs-3">
			<div id="container_sax_defect_visual" style="width: 100%;margin-top: 20px;"></div>
		</div>
		@elseif($id == "edin")
		<div class="col-xs-4">
			<div id="container_edin_defect_fungsi" style="width: 100%;margin-top: 20px;"></div>
		</div>
		<div class="col-xs-4">
			<div id="container_edin_defect_visual" style="width: 100%;margin-top: 20px;"></div>
		</div>
		<div class="col-xs-4">
			<div id="container_edin_defect_ng_jelas" style="width: 100%;margin-top: 20px;"></div>
		</div>
		@endif
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/drilldown.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchChart();

		$('.select2').select2({
			allowClear : true,
		});
	});

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};

	function fetchChart(){
		var fy = $('#fy').val();
		var id = "{{$id}}";

		var data = {
			fy:fy,
			id:id
		}

		$('#loading').show();

		$.get('{{ url("fetch/qc_report/market_claim") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#loading').hide();

				$('#title_text').text('MARKET CLAIM '+id.toUpperCase()+' ON ' + result.fy);
				var h = $('#chart_title').height();
				$('.select').css('height', h);

				var month = [], 
				tahun = [],
				jumlah = [];

        $.each(result.data_bulan, function(key, value) {
          // jumlah.push(value.jumlah);
          month.push(value.bulan);
          tahun.push(value.tahun);
          
          jumlah.push({y: parseInt(value.jumlah),key:value.tahun});
        })


				var total_defect_visual = 0;
				var total_defect_fungsi = 0;
				var total_defect_jelas = 0;
				var total_defect_product_visual = [];
				var total_defect_product_fungsi = [];
				var total_defect_product_jelas = [];
				var hpl = [];

        $.each(result.data_defect, function(key, value) {
        	if(value.category == "Visual"){
        		total_defect_visual += parseInt(value.jumlah);
        		total_defect_product_visual.push(parseInt(value.jumlah));
        	}else if(value.category == "Fungsi"){
        		total_defect_fungsi += parseInt(value.jumlah);
        		total_defect_product_fungsi.push(parseInt(value.jumlah));
        	}else if(value.category == "NG Jelas"){
        		total_defect_jelas += parseInt(value.jumlah);
        		total_defect_product_jelas.push(parseInt(value.jumlah));
        	}
        	if (!hpl.includes(value.hpl)) {
					  hpl.push(value.hpl);
					}
				});

				var total_ng_fl_visual = []
				var defect_fl_visual = [];
				var total_ng_fl_fungsi = []
				var defect_fl_fungsi = [];

				var total_ng_sax_visual = []
				var defect_sax_visual = [];
				var total_ng_sax_fungsi = []
				var defect_sax_fungsi = [];

				var total_ng_edin_visual = []
				var defect_edin_visual = [];
				var total_ng_edin_fungsi = []
				var defect_edin_fungsi = [];
				var total_ng_edin_ng_jelas = []
				var defect_edin_ng_jelas = [];

				$.each(result.data_detail_defect, function(key, value) {
					if (id == "wi") {
						if(value.hpl == "FL"){
							if(value.category == "Visual"){
			       		total_ng_fl_visual.push(parseInt(value.jumlah));
			       		defect_fl_visual.push(value.defect);
			       	}else if(value.category == "Fungsi"){
			       		total_ng_fl_fungsi.push(parseInt(value.jumlah));
			       		defect_fl_fungsi.push(value.defect);
			       	}
						}else if(value.hpl == "SAX"){
							if(value.category == "Visual"){
			       		total_ng_sax_visual.push(parseInt(value.jumlah));
			       		defect_sax_visual.push(value.defect);
			       	}else if(value.category == "Fungsi"){
			       		total_ng_sax_fungsi.push(parseInt(value.jumlah));
			       		defect_sax_fungsi.push(value.defect);
			       	}
						}
					}else if(id == "edin"){
						if(value.category == "Visual"){
		       		total_ng_edin_visual.push(parseInt(value.jumlah));
		       		defect_edin_visual.push(value.defect);
						}else if(value.category == "Fungsi"){
		       		total_ng_edin_fungsi.push(parseInt(value.jumlah));
		       		defect_edin_fungsi.push(value.defect);
						}else if(value.category == "NG Jelas"){
		       		total_ng_edin_ng_jelas.push(parseInt(value.jumlah));
		       		defect_edin_ng_jelas.push(value.defect);
						}
					}
				});


				Highcharts.chart('container_bulan', {
					chart: {
						type: 'spline',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Claim By Month'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: true
					},
					xAxis: {
						type: 'category',
            categories: month,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +' '+tahun[(this.pos)];
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim'
						},
          	tickInterval: 10,  
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: jumlah,
            color : '#448aff' //f5f500
          },]
				});

					Highcharts.chart('container_defect', {
						chart: {
							backgroundColor: null,
							type: 'pie',
							options3d: {
								enabled: true,
								alpha: 45,
								beta: 0
							}
						},
						title: {
							text: 'Claim By Category'
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.y}</b>'
						},
						accessibility: {
							point: {
								valueSuffix: '%'
							}
						},
						legend: {
							enabled: true,
							symbolRadius: 1,
							borderWidth: 1,
							reversed:true
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								edgeWidth: 1,
								edgeColor: 'rgb(126,86,134)',
								depth: 35,
								dataLabels: {
									enabled: true,
									format: '<b>{point.y}</b>',
									distance: -40,
									style:{
										fontSize:'0.8vw',
										textOutline:0
									},
									color:'white'
								},
								showInLegend: true,
							}
						},
						credits: {
							enabled: false
						},
						exporting: {
							enabled: false
						},
						series: [{
							name: 'Defect',
							data: [{
								name: 'Visual',
								y: total_defect_visual,
								color: "#C1666B"
							}, {
								name: 'Fungsi',
								y: total_defect_fungsi,
								color:'#D10000'
							}, {
								name: 'NG Jelas',
								y: total_defect_jelas,
								color:'#000000'
							}]
						}]
					});

					Highcharts.chart('container_hpl_defect', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor: null
					},
					title: {
						text: 'Defect By Product'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: true
					},
					xAxis: {
						type: 'category',
            categories: hpl,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim'
						},

          tickInterval: 10,  
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
              stacking: 'normal',
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Total Defect Fungsi',
            data: total_defect_product_fungsi,
            color : '#D10000' 
          },{
            name: 'Total Defect Visual',
            data: total_defect_product_visual,
            color : '#C1666B' 
          },{
            name: 'Total Defect NG Jelas',
            data: total_defect_product_jelas,
            color : '#000000' 
          },]
				});

					if ("{{$id}}" == "wi") {

					Highcharts.chart('container_fl_defect_fungsi', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect FL Fungsi'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_fl_fungsi,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          	tickInterval: 1, 
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_fl_fungsi,
            color : '#D10000' //f5f500
          },]
				});

					Highcharts.chart('container_fl_defect_visual', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect FL Visual'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_fl_visual,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          	tickInterval: 1, 
					}, 
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_fl_visual,
            color : '#C1666B' //f5f500
          },]
				});

					Highcharts.chart('container_sax_defect_fungsi', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect Sax Fungsi'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_sax_fungsi,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          	tickInterval: 1, 
					}, 
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_sax_fungsi,
            color : '#D10000' //f5f500
          },]
				});

					Highcharts.chart('container_sax_defect_visual', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect Sax Visual'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_sax_visual,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          tickInterval: 1,  
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_sax_visual,
            color : '#C1666B' //f5f500
          },]
				});


					} else if ("{{$id}}" == "edin"){

					Highcharts.chart('container_edin_defect_fungsi', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect EDIN Fungsi'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_edin_fungsi,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          tickInterval: 1,  
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_edin_fungsi,
            color : '#D10000' //f5f500
          }]
				});

				Highcharts.chart('container_edin_defect_visual', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect EDIN Visual'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_edin_visual,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          tickInterval: 1,  
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_edin_visual,
            color : '#C1666B' //f5f500
          }]
				});

				Highcharts.chart('container_edin_defect_ng_jelas', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Defect EDIN NG Jelas'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						type: 'category',
            categories: defect_edin_ng_jelas,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total Market Claim By Defect'
						},
          tickInterval: 1,  
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Jumlah',
            data: total_ng_edin_ng_jelas,
            color : '#000000' //f5f500
          }]
				});
			}


				$('#loading').hide();
			}else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');				
			}
		});		
}


Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#D10000', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
    time: '2000'
  });
}

</script>
@endsection