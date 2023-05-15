@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
table.table-bordered{
	border:2px solid rgba(150, 150, 150, 0);
}
table.table-bordered > thead > tr > th{
	border:2px solid rgb(54, 59, 56) !important;
	text-align: center;
	background-color: #f0f0ff;  
	color:black;
}
table.table-bordered > tbody > tr > td{
	border-collapse: collapse !important;
	border:2px solid rgb(54, 59, 56)!important;
	background-color: #f0f0ff;
	color: black;
	vertical-align: middle;
	text-align: center;
	padding:3px;
}
table.table-condensed > thead > tr > th{   
	color: black
}
table.table-bordered > tfoot > tr > th{
	border:2px solid rgb(150,150,150);
	padding:0;
}
table.table-bordered > tbody > tr > td > p{
	color: #abfbff;
}

table.table-striped > thead > tr > th{
	border:2px solid black !important;
	text-align: center;
	background-color: rgba(126,86,134,.7) !important;  
}
table.table-striped > tbody > tr > td{
	border: 2px solid #eeeeee !important;
	border-collapse: collapse;
	color: black;
	padding: 3px;
	vertical-align: middle;
	text-align: center;
	background-color: white;
}
thead input {
	width: 100%;
	padding: 3px;
	box-sizing: border-box;
}
thead>tr>th{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
}
td:hover {
	overflow: visible;
}
table > thead > tr > th{
	border:2px solid #f4f4f4;
	color: white;
}
#tableResume > thead > tr > th{
	border: 2px solid black;
}
#tableResume > tbody > tr > td{
	cursor: pointer;
	border: 2px solid black;
}

#tableResumeSection > thead > tr > th{
	border: 2px solid black;
}
#tableResumeSection > tbody > tr > td{
	cursor: pointer;
	border: 2px solid black;
}

#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px">
			<div class="col-xs-2" style="padding-left: 0px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border-color:black; color:white; background-color: rgba(126,86,134,.7)">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From" style="border-color: black">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border-color:black; color:white; background-color: rgba(126,86,134,.7)">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To" style="border-color: black">
				</div>
			</div>
			<div class="col-xs-3" style="padding-left: 0px;">
				<select class="form-control select2" data-placeholder="Select Cost Center Category" id="category" style="border-color:black; width: 100% height: 35px; font-size: 15px;">
					<option value=""></option>
					<option value="DIRECT">DIRECT</option>
					<option value="INDIRECT">INDIRECT</option>
					<option value="PL">PL (GA, HR, ACC)</option>
				</select>
			</div>
			<div class="col-xs-2" style="padding-left: 0px;">
				<button class="btn btn-success" onclick="drawChart()">Update Chart</button>
			</div>			
		</div>
		<div class="col-xs-12" style="margin-top: 1%;">
			<div id="container" style="width: 100%; height: 80vh;"></div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span id="title_modal" style="font-weight: bold; font-size: 1.5vw;"></span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12" style="padding-bottom: 5px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%; vertical-align: middle;">#</th>
									<th style="width: 10%; vertical-align: middle;">Employe ID</th>
									<th style="width: 20%; vertical-align: middle;">Name</th>
									<th style="width: 30%; vertical-align: middle;">Department</th>
									<th style="width: 20%; vertical-align: middle;">Section</th>
									<th style="width: 10%; vertical-align: middle;">Shift Code</th>
									<th style="width: 10%; vertical-align: middle;">Attend Code</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
						</table>
					</div>
				</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			allowClear: true
		});

		$("#loading").hide();
		
		drawChart();
		setInterval(drawChart, 15*60*1000);
	});

	function drawChart() {
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var category = $('#category').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			category:category
		}

		$.get('{{ url("fetch/report/absence_monitoring") }}',data, function(result, status, xhr) {
			if(result.status){

				var day = [];

				var mangkir = [];
				var cuti = [];
				var izin = [];
				var sakit = [];
				var isoman = [];
				var covid = [];

				var all = [];

				for (var i = 0; i < result.data.length; i++) {
					day.push(result.data[i].day + ', ' + result.data[i].date);

					mangkir.push(parseInt(result.data[i].mangkir));
					cuti.push(parseInt(result.data[i].cuti));
					izin.push(parseInt(result.data[i].izin));
					sakit.push(parseInt(result.data[i].sakit));
					isoman.push(parseInt(result.data[i].isoman));
					covid.push(parseInt(result.data[i].covid));
					all.push(parseInt(result.data[i].mangkir)+parseInt(result.data[i].cuti)+parseInt(result.data[i].izin)+parseInt(result.data[i].sakit)+parseInt(result.data[i].isoman)+parseInt(result.data[i].covid));
				}

				var regress = [];

				for(var i = 0; i < all.length; i++){
					if (all[i] != 0) {
						regress.push(all[i]);
					}
				}

				if (regress.length % 2 == 0) {
					var totalsmedian = regress;
					var indexMedianBawah = (totalsmedian.length/2)-1;
					var indexMedianAtas = (totalsmedian.length/2);
					var indexMinus = -(indexMedianAtas+indexMedianBawah);
					var indexPlus = 1;
					var xLinear = [];
					for(var i = 0; i < totalsmedian.length;i++){
						if (totalsmedian[i] != 0) {
							if (i == indexMedianBawah) {
								xLinear.push(-1);
								indexMinus = indexMinus + 2;
							}else if(i == indexMedianAtas){
								xLinear.push(indexPlus);
								indexPlus = indexPlus + 2;
							}else if(i < indexMedianBawah){
								xLinear.push(indexMinus);
								indexMinus = indexMinus + 2;
							}else if(i > indexMedianAtas){
								xLinear.push(indexPlus);
								indexPlus = indexPlus + 2;
							}
						}
					}

					for(var i = 0; i < all.length; i++){
						if (all[i] == 0) {
							xLinear.push(indexPlus);
							indexPlus = indexPlus + 2;
						}
					}
				}else{
					var totalsmedian = regress;
					var indexMedian = Math.round(totalsmedian.length/2)-1;
					var indexMinus = -indexMedian;
					var indexPlus = 1;
					var xLinear = [];
					for(var i = 0; i < totalsmedian.length;i++){
						if (totalsmedian[i] != 0) {
							if (i == indexMedian) {
								xLinear.push(0);
							}else if(i < indexMedian){
								xLinear.push(indexMinus);
								indexMinus++;
							}else if(i > indexMedian){
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					for(var i = 0; i < all.length; i++){
						if (all[i] == 0) {
							xLinear.push(indexPlus);
							indexPlus++;
						}
					}
				}

				var xy = [];
				var xkuadrat = [];

				for(var i = 0; i < all.length; i++){
					if (all[i] != 0) {
						xy.push(parseInt(xLinear[i])*parseInt(all[i]));
						xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
					}
				}

				var sumy = all.reduce((a, b) => a + b, 0);
				var sumxy = xy.reduce((a, b) => a + b, 0);
				var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

				var a = sumy/totalsmedian.length;
				var b = sumxy/sumxkuadrat;

				var regressions = [];

				for(var i = 0; i < all.length; i++){
					regressions.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
				}

				var category = ''
				if(result.category != null) {
					category = 'Category ' + result.category;
				}

				Highcharts.chart('container', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Daily Absence Monitoring '+ category,
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: result.datefrom +' ~ '+ result.dateto,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: day,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -45,
							style: {
								fontSize: '14px'
							}
						},
					},
					yAxis: {
						title: {
							text: 'Count Item'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: 'white',
								fontSize: '0.5vw'
							}
						},
					},
					legend: {
						enabled: true,
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							cursor: 'pointer',
						},
						series:{
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							borderColor: '#212121',
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(this.category, this.series.name);

									}
								}
							},
						}
					},credits: {
						enabled: false
					},
					series: [
					{
						name : 'Mangkir',
						data : mangkir,
						color : 'Grey',
					},{
						name : 'Cuti',
						data : cuti,
						color : '#00a65a',
					},{
						name : 'Izin',
						data : izin,
						color : '#24cbe5',
					},{
						name : 'Sakit',
						data : sakit,
						color : '#fff263',
					},{
						name : 'Isoman',
						data : isoman,
						color : '#ff9655',
					},{
						name : 'Positif COVID-19',
						data : covid,
						color : '#e31919',
					},{
						type: 'line',
						data: regressions,
						name: "Trend",
						colorByPoint: false,
						color: "#fff",
						animation: false,
						dashStyle:'shortdash',
						lineWidth: 2,
						marker: {
							radius: 0,
							lineColor: '#fff',
							lineWidth: 1
						},
					}
					]
				});
			}
		});
}

function showDetail(category, series) {
	$("#loading").show();

	var data = {
		date : category,
		attend_code : series
	}

	$.get('{{ url("fetch/report/absence_monitoring_detail") }}',data, function(result, status, xhr) {
		if(result.status){

			$('#tableDetailBody').html("");
			$('#title_modal').text('Absence Data  "' + series + '" on ' + category);

			var detail = '';
			var count = 0;

			$.each(result.data, function(key, value){
				detail += '<tr>';
				detail += '<td>'+ ++count +'</td>';
				detail += '<td>'+value.employee_id+'</td>';
				detail += '<td>'+value.name+'</td>';
				detail += '<td>'+(value.department || '' )+'</td>';
				detail += '<td>'+(value.section || '' )+'</td>';
				detail += '<td>'+value.shift_code+'</td>';
				detail += '<td>'+(value.attend_code || '')+'</td>';
				detail += '</tr>';
			});

			$('#tableDetailBody').append(detail);


			$('#modalDetail').modal('show');
			$("#loading").hide();

		}
	});

}

$('.datepicker').datepicker({
	<?php $tgl_max = date('Y-m-d') ?>
	autoclose: true,
	format: "yyyy-mm-dd",
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



</script>


@stop