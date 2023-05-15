@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 1.5vw;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
		background-color: RGB(252, 248, 227);
		font-weight: bold;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="col-lg-2" style="padding-left: 0px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border-color:black; color:white; background-color: #ffa500">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="record_from" placeholder="Select Date From" style="border-color: black;">
				</div>
			</div>
			<div class="col-lg-2" style="padding-left: 0px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border-color:black; color:white; background-color: #ffa500">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="record_to" placeholder="Select Date To" style="border-color: black;">
				</div>
			</div>
			<div class="col-lg-2" style="padding-left: 0px;">
				<button class="btn btn-primary" onclick="fetchData()">Update Chart</button>
			</div>	
		</div>
		<div class="col-lg-12">
			<div id="container" style="height: 40vh;"></div>
		</div>
		<div id="detail">
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail" data-keyboard="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<span style="font-weight: bold;" id="detailID"></span>
					<br>
					<span style="font-weight: bold;" id="detailNama"></span>
					<br>
					<span style="font-weight: bold;" id="detailDepartemen"></span>
					<br>
					<span style="font-weight: bold;" id="detailSection"></span>
					<br>
					<span style="font-weight: bold;" id="detailGroup"></span>
					<br>
					<br>
					<table id="tableDetail" style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black;">
						<thead style="background-color: #2a2628; height: 40px;">
							<tr style="color: #f39c12;">
								<th style="width: 0.1%; border:1px solid black; text-align: center;">#</th>
								<th style="width: 0.1%; border:1px solid black;">Reason</th>
								<th style="width: 0.1%; border:1px solid black;">Tanggal</th>
								<th style="width: 0.1%; border:1px solid black;">Durasi (Menit)</th>
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
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
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
		$('#record_from').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('#record_to').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('#record_from').val("");
		$('#record_to').val("");
		fetchData();
	});

	var loss_time_logs = [];

	function modalDetail(a){
		var count = 0;
		var tableDetailBody = "";
		$('#tableDetailBody').html("");

		$.each(loss_time_logs, function(key, value){
			if(value.employee_id == a.split(' ')[0]){
				$('#detailID').text('NIK: '+value.employee_id);
				$('#detailNama').text('Nama: '+value.employee_name);
				$('#detailDepartemen').text('Departemen: '+value.department);
				$('#detailSection').text('Section: '+value.section);
				$('#detailGroup').text('Group: '+value.group);
				count += 1;
				tableDetailBody += '<tr>';
				tableDetailBody += '<td style="width: 0.1%; border:1px solid black; text-align: center;">'+count+'</td>';
				tableDetailBody += '<td style="width: 0.1%; border:1px solid black;">'+value.reason+'</td>';
				tableDetailBody += '<td style="width: 0.1%; border:1px solid black;">'+value.tanggal+'</td>';
				tableDetailBody += '<td style="width: 0.1%; border:1px solid black;">'+parseFloat(value.duration).toFixed(1)+'</td>';
				tableDetailBody += '</tr>';
			}
		});

		$('#tableDetailBody').append(tableDetailBody);
		$('#modalDetail').modal('show');
	}

	function fetchData(){
		$('#loading').show();
		var record_from = $('#record_from').val();
		var record_to = $('#record_to').val();

		var data = {
			record_from:record_from,
			record_to:record_to
		};
		$.get('{{ url("fetch/efficiency/operator_loss_time_log") }}', data, function(result, status, xhr){
			if(result.status){
				loss_time_logs = result.operator_loss_time_logs;
				var departments = result.departments;

				var all = [];

				loss_time_logs.reduce(function(res, value) {
					if (!res[value.employee_id]) {
						res[value.employee_id] = { employee_id: value.employee_id, employee_name: value.employee_name, department:value.department_shortname, total: 0 };
						all.push(res[value.employee_id]);
					}

					res[value.employee_id].total += parseFloat(value.duration);

					return res;
				}, {});

				all.sort(function(a, b){return b['total'] - a['total']});

				var categories_all = [];
				var series_all = [];
				var length = 20;

				if(all.length < length){
					length = all.length;
				}

				for (var i = 0; i < length; i++) {
					categories_all.push(all[i].employee_id+' ('+all[i].department+')'+'<br>'+all[i].employee_name);
					series_all.push(all[i].total);
				}

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						backgroundColor: null
					},
					title: {
						text: 'Top 20 ('+result.record_from+' ~ '+result.record_to+')'
					},
					subtitle: {
						text: 'YMPI'
					},
					xAxis: {
						categories: categories_all,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)'
					},
					yAxis: {
						title: {
							text: 'Minute(s)'
						}
					},
					legend: {
						enabled: false,
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y} Minute(s)</b> <br/>',
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f} Min',
								style:{
									textOutline: false,
									color: 'black'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 1,
							borderColor: '#212121',
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										modalDetail(this.category);
									}
								}
							},
						}
					},
					credits: {
						enabled: false
					},
					series: [
					{
						name : 'Loss Time',
						data : series_all,
						color : '#ffa500'
					}]
				});

				$.each(departments, function(key, value){
					div_detail = '<div style="height: 40vh;" class="col-lg-6" id="'+value.department_shortname+'"></div>';
					$('#detail').append(div_detail);

					var categories = [];
					var series = [];

					for (var i = 0; i < all.length; i++) {
						if(all[i].department == value.department_shortname){
							categories.push(all[i].employee_id+' ('+all[i].department+')'+'<br>'+all[i].employee_name);
							series.push(all[i].total);
						}
					}

					categories = categories.slice(0, 20);
					series = series.slice(0, 20);

					Highcharts.chart(value.department_shortname, {
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Top 20 ('+result.record_from+' ~ '+result.record_to+')'
						},
						subtitle: {
							text: value.department_name
						},
						xAxis: {
							categories: categories,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)'
						},
						yAxis: {
							title: {
								text: 'Minute(s)'
							}
						},
						legend: {
							enabled: false,
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y} Minute(s)</b> <br/>',
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y:.0f} Min',
									style:{
										textOutline: false,
										color: 'black'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 1,
								borderColor: '#212121',
								cursor: 'pointer',
								point: {
									events: {
										click: function (event) {
											modalDetail(this.category);

										}
									}
								},
							}
						},
						credits: {
							enabled: false
						},
						series: [
						{
							name : 'Loss Time',
							data : series,
							color : '#ffa500'
						}]
					});
				});

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed.');
			}
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
@endsection

