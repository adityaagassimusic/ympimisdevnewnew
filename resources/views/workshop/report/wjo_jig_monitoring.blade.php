@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
		border:1px solid #2a2a2b;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px; padding: 0%;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="date_from" placeholder="Select Date From">
					</div>
				</div>

				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="date_to" placeholder="Select Date To">
					</div>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>	
			
		</div>
		<div class="col-xs-12">
			<center><h2><b>Monitoring Automatic WJO Jig Order</b><span id="subtitle"></span></h2></center>
		</div>
		<div class="col-xs-6">
			<div id="container1" style="padding: 1%; padding-bottom: 0%;"></div>		
		</div>
		<div class="col-xs-6">
			<div id="container2" style="padding: 1%; padding-bottom: 0%;"></div>
		</div>
	</div>

	<div class="modal fade" id="modal-detail" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>WJO Details</b></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="detail" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="detail-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Jig Code</th>
										<th style="width: 20%">Jig Name</th>
										<th>Jig Location</th>
										<th>Max Stock</th>
										<th>Stock Balance</th>
										<th>WJO Number</th>
										<th>Created_at</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody id="detail-body">
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

	<div class="modal fade" id="modal-detail-zero" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>WJO Details</b></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="detail-zero" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="detail-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>No</th>
										<th>Process</th>
										<th style="width: 20%">Jig Code</th>
										<th>Jig Name</th>
										<th>Quantity</th>
										<th>Stock Balance</th>
									</tr>
								</thead>
								<tbody id="detail-zero-body">
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

	var detail_arr = [];
	var detail_zero_arr = [];

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
	});

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		todayHighlight: true,
		endDate: '<?php echo date("Y-m") ?>'
	});

	function fillChart() {
		var date_from = $("#date_from").val();
		var date_to = $("#date_to").val();

		if ($("#date_from").val() != '' && $("#date_to").val() == '') {
			openErrorGritter('Error', 'Please Fill All Date Filter');
			return false;
		}

		if ($("#date_from").val() == '' && $("#date_to").val() != '') {
			openErrorGritter('Error', 'Please Fill All Date Filter');
			return false;
		}

		var data = {
			date_from : date_from,
			date_to : date_to
		}

		if ($("#date_from").val() != '' && $("#date_to").val() != '') {
			$("#subtitle").html('<br> on '+$("#date_from").val()+' ~ '+$("#date_to").val());
		}

		$.get('{{ url("fetch/workshop/monitoring/jig") }}', data, function(result, status, xhr){
			if(result.status){
				var finsihed = [];
				var unfinsihed = [];
				var zero = [];
				var categories1 = [];
				var categories2 = [];

				detail_arr = result.wjo_jig_details;
				detail_zero_arr = result.wjo_zero_details;

				$.each(result.wjo_jigs, function(index, value){
					finsihed.push(parseInt(value.sum_finish));
					unfinsihed.push(parseInt(value.sum_unfinish));

					if(categories1.indexOf(value.process) === -1){
						categories1[categories1.length] = value.process;
					}
				});

				$.each(result.wjo_zero_jigs, function(index, value){
					if (value.process) {
						zero.push(parseInt(value.jml));
					}

					if(categories2.indexOf(value.process) === -1){
						categories2[categories2.length] = value.process;
					}
				});

				$('#container1').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'WJO Automatic Monitoring',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: categories1,
						lineColor:'#9e9e9e',
						tickInterval: 1,
						gridLineWidth: 1
					},
					yAxis: {
						title: {
							text: 'Number of WJO'
						},
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										showWJODetail(this.category, this.series.name);
									}
								}
							},
						},
						column: {
							color:  Highcharts.ColorString,
							stacking: 'normal',
							borderRadius: 1,
							dataLabels: {
								enabled: true
							}
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: true
					},
					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series: [{
						name: 'Finished',
						data: finsihed,
						color: '#90ee7e'
					},
					{
						name: 'Unfinished',
						data: unfinsihed,
						color: '#ff9191'
					}]
				});


				// ------------------  GRAFIK 2 ----------------


				$('#container2').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Jig Urgent Monitoring (Stok Nol (0))',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: categories2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						title: {
							text: 'Number of Jig'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (
									Highcharts.defaultOptions.title.style &&
									Highcharts.defaultOptions.title.style.color
									) || 'gray'
							}
						}
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							minPointLength: 3,
							point: {
								events: {
									click: function () {
										showWJODetailZero(this.category);
									}
								}
							},
						},
						column: {
							color:  Highcharts.ColorString,
							stacking: 'normal',
							borderRadius: 1,
							dataLabels: {
								enabled: true
							}
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: true
					},
					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series: [{
						name: 'Jig Item',
						data: zero,
					}]
				});
				
			}
		});
	}

	function showWJODetail(param, stat, stok) {
		$("#modal-detail").modal('show');


		$('#detail').DataTable().clear();
		$('#detail').DataTable().destroy();
		$('#detail-body').html("");
		
		var body = "";

		

		$.each(detail_arr, function(index, value){
			if (stat == 'Finished') {
				if (value.remark >= 4 && value.process == param) {
					body += '<tr>';
					body += '<td>'+value.item_number+'</td>';
					body += '<td>'+value.item_name+'</td>';
					body += '<td>'+value.process+'</td>';
					body += '<td>'+value.quantity+'</td>';
					body += '<td>'+value.quantity_actual+'</td>';
					body += '<td>'+value.order_no+'</td>';
					body += '<td>'+value.create_at+'</td>';
					body += '<td>'+value.remark+'</td>';
					body += '</tr>';
				}
			} else if (stat == 'Unfinished') {
				if (value.remark < 4 && value.process == param) {
					body += '<tr>';
					body += '<td>'+value.item_number+'</td>';
					body += '<td>'+value.item_name+'</td>';
					body += '<td>'+value.process+'</td>';
					body += '<td>'+value.quantity+'</td>';
					body += '<td>'+value.quantity_actual+'</td>';
					body += '<td>'+value.order_no+'</td>';
					body += '<td>'+value.create_at+'</td>';
					body += '<td>'+value.remark+'</td>';
					body += '</tr>';
				}
			} else if (stat == ''){
				if (value.quantity_actual == 0 && value.process == param) {
					body += '<tr>';
					body += '<td>'+value.item_number+'</td>';
					body += '<td>'+value.item_name+'</td>';
					body += '<td>'+value.process+'</td>';
					body += '<td>'+value.quantity+'</td>';
					body += '<td>'+value.quantity_actual+'</td>';
					body += '<td>'+value.order_no+'</td>';
					body += '<td>'+value.create_at+'</td>';
					body += '<td>'+value.remark+'</td>';
					body += '</tr>';
				}
			}

		})

		$("#detail-body").append(body);

		var table = $('#detail').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'pageLength': 25,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
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
				}
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

	function showWJODetailZero(param) {
		$("#modal-detail-zero").modal('show');

		$('#detail-zero').DataTable().clear();
		$('#detail-zero').DataTable().destroy();
		$('#detail-zero-body').html("");
		
		var body = "";
		var no = 1;

		$.each(detail_zero_arr, function(index, value){
			if (value.process == param) {
				body += '<tr>';
				body += '<td>'+no+'</td>';
				body += '<td>'+value.process+'</td>';
				body += '<td>'+value.jig_code+'</td>';
				body += '<td>'+value.jig_name+'</td>';
				body += '<td>'+value.quantity+'</td>';
				body += '<td>'+value.quantity_actual+'</td>';
				body += '</tr>';
				no++;
			}
		})

		$('#detail-zero-body').append(body);
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
			backgroundColor: null,
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