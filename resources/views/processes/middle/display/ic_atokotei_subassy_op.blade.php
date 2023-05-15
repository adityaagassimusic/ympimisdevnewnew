@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
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
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2" style="padding-right: 0; color:black;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			
			<div class="col-xs-12" style="margin-top: 5px; padding-left: 0px;">
				<div id="container1" style="width: 100%; height: 560px;"></div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		$('.select3').select2({
			allowClear: true
		});
		fillChart();
		setInterval(fillChart, 47000);
		
	});

	function fillChart() {
		var key = $('#key').val();
		var tanggal = $('#tanggal').val();
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		
		var data = {
			tanggal : tanggal,
			key : key
		}

		$.get('{{ url("fetch/middle/ic_atokotei_subassy_op") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var operator = [];
					var kizu_before = [];
					var hokori_debu = [];
					var hokori_benang = [];
					var kizu_after = [];
					var scrath = [];
					var buff_tarinai = [];
					var toso_usui = [];
					var tare = [];
					var yogore = [];
					var enthol = [];
					var black_shimi = [];
					var buff_tidak_rata = [];
					var other = [];

					for (var i = 0; i < result.resume.length; i++) {
						kizu_before.push(0);
						hokori_debu.push(0);
						hokori_benang.push(0);
						kizu_after.push(0);
						scrath.push(0);
						buff_tarinai.push(0);
						toso_usui.push(0);
						tare.push(0);
						yogore.push(0);
						enthol.push(0);
						black_shimi.push(0);
						buff_tidak_rata.push(0);
						other.push(0);
					}

					for (var i = 0; i < result.resume.length; i++) {
						operator.push(result.resume[i].name);
						for (var j = 0; j < result.detail.length; j++) {
							if(result.resume[i].operator_id == result.detail[j].operator_id){
								if(result.detail[j].ng_name == 'Kizu before'){
									kizu_before[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Hokori debu'){
									hokori_debu[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Hokori benang'){
									hokori_benang[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Kizu after'){
									kizu_after[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Scrath'){
									scrath[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Buff tarinai'){
									buff_tarinai[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Toso usui'){
									toso_usui[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Tare'){
									tare[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Yogore'){
									yogore[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Enthol'){
									enthol[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Black shimi'){
									black_shimi[i] = result.detail[j].quantity;									
								}else if(result.detail[j].ng_name == 'Buff tidak rata'){
									buff_tidak_rata[i] = result.detail[j].quantity;
								}else{
									if(typeof other[i] == 'undefined'){
										other.push(result.detail[j].quantity);
									}else{
										other[i] += result.detail[j].quantity;
									}
								}
							}
						}
					}

					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'I.C. SubAssy by Operator',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'on '+result.date,
							style: {
								fontSize: '1vw',
								fontWeight: 'bold'
							},
						},
						xAxis: {
							categories: operator,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								rotation: -25,
								style: {
									fontSize: '16px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total Not Good'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'top',
							x: 1,
							y: 0,
							floating: true,
							borderWidth: 1,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name : 'Kizu before',
							data : kizu_before,
							color : '#2b908f'
						},{
							name : 'Hokori debu',
							data : hokori_debu,
							color : '#9c4dcc',
						},{
							name : 'Hokori benang',
							data : hokori_benang,
							color : '#90ee7e',
						},{
							name : 'Kizu after',
							data : kizu_after,
							color : '#f45b5b',
						},{
							name : 'Scrath',
							data : scrath,
							color : '#7798BF',
						},{
							name : 'Buff tarinai',
							data : buff_tarinai,
							color : '#aaeeee',
						},{
							name : 'Toso usui',
							data : toso_usui,
							color : '#ff0066',
						},{
							name : 'Tare',
							data : tare,
							color : '#FF8F00',

						},{
							name : 'Yogore',
							data : yogore,
							color : '#cfd8dc'

						},{
							name : 'Enthol',
							data : enthol,
							color : '#a1887f'

						},{
							name : 'Black shimi',
							data : black_shimi,
							color : '#212121'

						},{
							name : 'Buff tidak rata',
							data : buff_tidak_rata,
							color : '#FFEB3B'
						},{
							name : 'Others',
							data : other,
							color : '#ffffff'
						}
						]
					});


				}
			}
		});

}

$('.datepicker').datepicker({
	<?php $tgl_max = date('d-m-Y') ?>
	autoclose: true,
	format: "dd-mm-yyyy",
	todayHighlight: true,	
	endDate: '<?php echo $tgl_max ?>'
});

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

</script>
@endsection