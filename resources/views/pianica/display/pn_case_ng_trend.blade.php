@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
		font-size: 18px;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 16px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
					</div>
				</div>

				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" placeholder="Select Date To">
					</div>
				</div>

				<div class="col-xs-1" style="padding-left: 0px;padding-right: 0px;">
					<div class="form-group">
						<button class="btn btn-success" type="button" onclick="fillTable()">Search</button>
					</div>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
			</div>
			<div class="col-xs-2" style="padding: 0px; margin-top: 0;">
				<div class="row">
					<div class="col-lg-12 col-xs-6">
						<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
								<h5 style="font-size: 2.5vw; font-weight: bold;" id="total">0</h5>
							</div>
							<div class="icon" style="padding-top: 40px;font-size: 3vw">
								<i class="fa fa-search"></i>
							</div>
						</div>
						<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
								<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok">0</h5>
							</div>
							<div class="icon" style="padding-top: 40px;font-size: 3vw">
								<i class="fa fa-check"></i>
							</div>
						</div>
						<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;">
							<div class="inner" style="padding-bottom: 0px; cursor: pointer;" onclick="modalTampil($('#tanggal').val(), $('#tanggal2').val(), 'RETURN', 'bar')">
								<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>RETURN <small><span style="color: white">返品</span></small></b></h3>
								<h5 style="font-size: 2.5vw; font-weight: bold;" id="return">0</h5>
							</div>
							<div class="icon" style="padding-top: 40px;font-size: 3vw">
								<i class="fa fa-reply"></i>
							</div>
						</div>
						<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px; color: black">
							<div class="inner" style="padding-bottom: 0px; cursor: pointer;" onclick="modalTampil($('#tanggal').val(), $('#tanggal2').val(), 'REPAIR', 'bar')">
								<h3 style="margin-bottom: 0px;font-size: 2vw;">REPAIR <b><small><span>修正</span></small></b></h3>
								<h5 style="font-size: 2.5vw; font-weight: bold;" id="repair">0.00<sup style="font-size: 30px"> %</sup></h5>
							</div>
							<div class="icon" style="padding-top: 40px;font-size: 3vw">
								<i class="fa fa-refresh"></i>
							</div>
						</div>
						<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px; color: black">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL NG % <small></b></h3>
									<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg">0</h5>
								</div>
								<div class="icon" style="padding-top: 40px;font-size: 3vw">
									<i class="fa fa-line-chart"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-10" style="padding: 0px; margin-top: 0;">
					<div id="container" style="height: 83vh;"></div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
			<div class="modal-dialog" style="width: 1200px">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="title_detail"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
									<thead>
										<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
											<th style="padding: 5px;text-align: center;width: 1%">#</th>
											<th style="padding: 5px;text-align: center;width: 3%">Date</th>
											<th style="padding: 5px;text-align: center;width: 4%">Model</th>
											<th style="padding: 5px;text-align: center;width: 3%">Line</th>
											<th style="padding: 5px;text-align: center;width: 3%">NG 1</th>
											<th style="padding: 5px;text-align: center;width: 3%">NG 2</th>
											<th style="padding: 5px;text-align: center;width: 3%">NG 3</th>
											<th style="padding: 5px;text-align: center;width: 3%">NG 4</th>
										</tr>
									</thead>
									<tbody id="bodyTableDetail">

									</tbody>
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
	@stop

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

		jQuery(document).ready(function() {
			$('.select2').select2();
			fillTable();
			setInterval(fillTable, 600000);
		});

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		function fillTable() {
			$('#loading').show();
			var tgl1 = $('#tanggal').val();
			var tgl2 = $('#tanggal2').val();
			var data = {
				date_from:tgl1,
				date_to:tgl2
			}

			$.get('{{ url("fetch/case_pn/ng_trend") }}', data, function(result, status, xhr) {
				if (result.status) {
					$('#loading').hide();

					var cat1 = [];
					var total_cek = 0;
					var total_ok = 0;
					var total_repair = 0;
					var total_return = 0;
					var tot_cek = [];
					var tot_ng = [];
					var scater_ng = [];

					$.each(result.data_trend, function(key, value){
						total_cek += 1;

						if (value.stat == 'oke') {
							total_ok += 1;
						}

						if (value.ng_status == 'return') {
							total_return += 1;
						}

						if (value.ng_status == 'repair') {
							total_repair += 1;
						}

						if(cat1.indexOf(value.dt) === -1){
							cat1[cat1.length] = value.dt;
						}
					})

					$.each(cat1, function(key, value){
						var tmp_total_cek = 0;
						var tmp_total_ng = 0;
						$.each(result.data_trend, function(key1, value1){
							if (value == value1.dt) {
								tmp_total_cek += 1;

								if (value1.stat == 'ng') {
									tmp_total_ng += 1;
								}
							}
						})

						tot_cek.push(tmp_total_cek);
						tot_ng.push(tmp_total_ng);
						scater_ng.push(parseFloat((tmp_total_ng/tmp_total_cek * 100).toFixed(2)));
					})

					var total_ng = total_repair + total_return;

					$("#total").text(total_cek);
					$("#ok").text(total_ok);
					$("#return").text(total_return);
					$("#repair").text(total_repair);
					var run = parseFloat((total_ng / total_cek * 100).toFixed(2));
					$("#pctg").text(run+' %');


					Highcharts.chart('container', {
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Persentase NG Case Pianica',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle
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
						yAxis: [{						
							allowDecimals: false,
							title: {
								text: 'Qty Item Pc(s)',
								style: {
									color: '#eee',
									fontSize: '20px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"13px"
								}
							},
						}, {
							title: {
								text: 'NG Rate (%)',
								style: {
									color: '#eee',
									fontSize: '20px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"13px"
								}
							},
							max: 100,
							type: 'linear',
							opposite: true
						}],
						xAxis: {
							labels: {
								style: {
									fontSize: '12px',
									fontWeight: 'bold'
								}
							},
							categories: cat1
						},
						tooltip: {
							formatter: function () {
								return '<b>' + this.series.name + '</b><br/>' +
								this.point.y + ' ' + this.series.name.toLowerCase();
							}
						},
						plotOptions: {
							column: {
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalTampil(this.category, this.category, this.series.name, 'Grafik');
										}
									}
								},
								dataLabels: {
									allowOverlap: true,
									enabled: true,
									style: {
										color: 'black',
										fontSize: '13px',
										textOutline: false,
										fontWeight: 'bold',
									},
								},
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								animation: false,
								opacity: 1
							}
						},
						series: [
						{
							name: 'Total NG',
							data: tot_ng,
							color: "#d62d2d"
						}, {
							name: 'Qty Check',
							data: tot_cek,
							color: "#2064bd"
						}, 
						{
							type: 'spline',
							data: scater_ng,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#fff',
							yAxis:1,
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}%',
								style:{
									fontSize: '1vw',
									textShadow: false
								}
							}
						}
						]
					});

				} else{
					alert('Fill Data Failed');
					$('#loading').hide();
				}
			});
}

function modalTampil(dt, dt2, ctg, remark) {
	var data = {
		date_from : dt,
		date_to : dt2,
		category : remark,
		remark : ctg
	}

	$("#laoding").show();

	$.get('{{ url("fetch/case_pn/ng_trend/detail") }}', data, function(result, status, xhr) {
		if (result.status) {
			$("#bodyTableDetail").empty();
			$('#tableDetail').DataTable().clear();
			$('#tableDetail').DataTable().destroy();
			var body = '';

			$("#loading").hide();
			$("#modalDetail").modal('show');

			$("#title_detail").html(ctg+'<br>'+result.monthTitle);

			$.each(result.datas, function(key, value){
				body += '<tr>';
				body += '<td>'+(key+1)+'</td>';
				body += '<td>'+value.dt+'</td>';
				body += '<td>'+value.type+'</td>';
				body += '<td>'+value.line+'</td>';

				if (value.ngs) {
					var ng = value.ngs.split(',');
					var ng_stat = value.ng_statuses.split(',');

					$.each(ng, function(key2, value2){
						if (remark == 'Grafik') {
							body += '<td>'+value2+' - '+ng_stat[key2]+'</td>';
						} else {
							body += '<td>'+value2+'</td>';
						}
					})

					for (var i = ng.length; i < 4; i++) {
						body += '<td></td>';
					}
				} else {
					body += '<td></td>';
					body += '<td></td>';
					body += '<td></td>';
					body += '<td></td>';
				}

				body += '</tr>';
			})

			$("#bodyTableDetail").append(body);

			var table = $('#tableDetail').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 25, 50, -1 ],
				[ '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'pageLength',
						className: 'btn btn-default',
					},
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					{
						extend: 'copy',
						className: 'btn btn-success',
						text: '<i class="fa fa-copy"></i> Copy',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					]
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});
		}
	})		
}

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
@stop