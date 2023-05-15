@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		text-align: center;
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
	#loading, #error { display: none; }
</style>
@endsection
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
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="date" name="date" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" id="location" data-placeholder="Select Locations" style="width: 100%;"> 	
						<option value=""></option>
						<option value="phs-{{$location}}">PHS</option>
						<option value="hsa-{{$location}}">HSA</option>
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
				</div>
				<div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 1%;" id="shifta">
			<div id="container1" class="container1" style="width: 100%;"></div>
		</div>
		<div class="col-xs-12" id="shiftb">
			<div id="container2" class="container2" style="width: 100%;"></div>
		</div>
		<div class="col-xs-12" id="shiftc">
			<div id="container3" class="container3" style="width: 100%;"></div>
		</div>
		<!-- <div class="col-xs-12" style="margin-top: 1%;">
			<div id="shifta2">
				<div id="container1_last" style="width: 100%;"></div>					
			</div>
			<div id="shiftb2">
				<div id="container2_last" style="width: 100%;"></div>					
			</div>
			<div id="shiftc2">
				<div id="container3_last" style="width: 100%;"></div>					
			</div>
		</div> -->
	</div>
</section>

<div class="modal fade" id="check-modal">
	<div class="modal-dialog modal-md">
		<div class="modal-content" style="color: black;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
				<h4 class="modal-title" style="text-align: center;">
					Handling Operator's NG Rate
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />						

							<input type="hidden" id="date">

							<div class="form-group row" align="right">
								<label class="col-sm-4">Tag</label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="input_tag">
								</div>
							</div>

							<div class="form-group row" align="right" id="field-nik" >
								<label class="col-sm-4">NIK</label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="employee_id" readonly>
								</div>
							</div>

							<div class="form-group row" align="right" id="field-name" >
								<label class="col-sm-4">Name</label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="name" readonly>
								</div>
							</div>

							<div class="form-group row" align="right" id="field-key" >
								<label class="col-sm-4">Key</label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="key" readonly>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" onclick="stopScan()"><span><i class="glyphicon glyphicon-remove-sign"></i> Cancel</span></button>
				<button id="btn-check" class="btn btn-success" onclick="checkNg()"><span><i class="fa fa-check-square-o"></i> Check</span></button>
			</div>
		</div>
	</div>
</div>

<!-- start modal -->
<div class="modal fade" id="myModal" style="color: black;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background-color: lightskyblue">
				<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Welding Productivity Detail</b></h4>
				<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
			</div>
			<div class="modal-body">
				<div class="row">
					<!-- <div class="col-md-12" style="margin-bottom: 20px;">
						<div class="col-md-6">
							<h5 class="modal-title">NG Rate</h5><br>
							<h5 class="modal-title" id="ng_rate"></h5>
						</div> -->
						<!-- <div class="col-md-6">
							<div id="modal_ng" style="height: 200px"></div>
						</div> -->
						<!-- </div> -->
						<div class="col-md-12">
							<div style="width: 100%;background-color: rgba(126,86,134,.7);text-align: center;margin-bottom: 10px;">
								<span style="width: 100%;font-size: 17px;font-weight: bold;padding: 10px;">
									TOTAL = <span id="total_cek"></span>
								</span>
							</div>
							<table id="welding-cek" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="welding-cek-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 10%">Material</th>
										<th style="width: 1%">Model</th>
										<th style="width: 1%">Key</th>
										<th style="width: 5%">Start</th>
										<th style="width: 5%">Finish</th>
										<th style="width: 1%">Act Time</th>
										<th style="width: 1%">Std Time</th>
										<th style="width: 1%">Qty</th>
									</tr>
								</thead>
								<tbody id="welding-cek-body">
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
	<!-- end modal -->

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

	var details = null;

	jQuery(document).ready(function(){
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart, 300000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#1976D2', '#aaeeee', '#ff0066',
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


function showDetail(date, nama,employee_id) {
	$('#loading').show();
	$('#judul').html('');

	$('#judul').append('<b>'+employee_id+' - '+nama+' Tanggal '+date+'</b>');

	$('#welding-cek').DataTable().clear();
	$('#welding-cek').DataTable().destroy();

	$('#welding-cek-body').html('');
	var total_cek = 0;
	var body = '';
	for (var i = 0; i < details.length; i++) {
		if (details[i].last_check == employee_id) {
			body += '<tr>';
			body += '<td style="text-align:left;padding-left:5px;">'+details[i].material_number+' - '+details[i].material_description+'</td>';
			body += '<td style="text-align:left;padding-left:5px;">'+details[i].model+'</td>';
			body += '<td style="text-align:left;padding-left:5px;">'+details[i].key+'</td>';
			body += '<td style="text-align:right;padding-right:5px;">'+details[i].started_at+'</td>';
			body += '<td style="text-align:right;padding-right:5px;">'+details[i].finished_at+'</td>';
			body += '<td style="text-align:right;padding-right:5px;">'+details[i].diff+'</td>';
			body += '<td style="text-align:right;padding-right:5px;">'+details[i].standard_time+'</td>';
			body += '<td style="text-align:right;padding-right:5px;">'+details[i].quantity+'</td>';
			body += '</tr>';

			total_cek += parseInt(details[i].quantity);
		}

	}
	$('#total_cek').html(total_cek);
	// body += '<tr>';
	// body += '<td colspan="7" style="text-align: center;">Total</td>';
	// body += '<td>'+total_cek+'</td>';
	// body += '</tr>';
	$('#welding-cek-body').append(body);

	$('#welding-cek').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ 10, 25, 100, -1 ],
		[ '10 rows', '25 rows', '100 rows', 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			{
				extend: 'copy',
				className: 'btn btn-success',
				text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
				}
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
				extend: 'print',
				className: 'btn btn-warning',
				text: '<i class="fa fa-print"></i> Print',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}
			]
		},
		'paging': true,
		'lengthChange': true,
		'pageLength': 10,
		'searching': true	,
		'ordering': true,
		'order': [],
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true
	});
	$('#myModal').modal('show');
	$('#loading').hide();
}

function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}


function fetchChart(){
	$('#loading').show();
	var location = "{{$location}}";
	var location_select = $('#location').val();
	var date = $("#date").val();
	var data = {
		location:location,
		location_select:location_select,
		date:date
	}

	$.get('{{ url("fetch/welding/productivity") }}', data, function(result, status, xhr) {
		if(result.status){

			$('#loc').html('<b style="color:white">'+ $('#location').val() +'</b>');

			//SHIFT A
			var operators = [];
			for(var i = 0; i < result.productivity.length;i++){
				if (result.productivity[i].shift == 'A') {
					operators.push(result.productivity[i].last_check);
				}
			}

			var operator = operators.filter(onlyUnique);

			var op_name = [];
			var actual = [];
			var standard = [];
			var productivity = [];

			for(var i = 0; i < operator.length;i++){
				var act = 0;
				var std = 0;
				var names = '';
				for(var j = 0; j < result.productivity.length;j++){
					if (result.productivity[j].last_check == operator[i]) {
						act = act + parseFloat(result.productivity[j].diff);
						std = std + result.productivity[j].standard_time;
						names = result.productivity[j].name;
					}
				}
				actual.push({y:parseFloat(act.toFixed(2)),key:operator[i]});
				if (std > 460) {
					standard.push({y:460,key:operator[i]});
				}else{
					standard.push({y:parseFloat(std.toFixed(2)),key:operator[i]});
				}
				if (names.split(' ').length == 1) {
					op_name.push(names);
				}else{
					op_name.push(names.split(' ')[0]+' '+names.split(' ')[1]);
				}

				productivity.push({y:parseFloat(((actual[i].y / standard[i].y)*100).toFixed(2)),key:operator[i]});
			}

			Highcharts.chart('container1', {
				chart: {
					animation: false
				},
				title: {
					text: 'OPERATOR PRODUCTIVITY',
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Group A on '+result.dateTitle,
					style: {
						fontSize: '1vw',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					categories: op_name,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						rotation: -45,
						style: {
							fontSize: '12px',
							fontWeight: 'bold'
						}
					},
				},
				yAxis: [{ 
				        labels: {
				            format: '{value}',
				            style: {
				                color: '#fff'
				            }
				        },
				        title: {
				            text: 'Actual Time VS Standard Time',
				            style: {
				                color: '#fff'
				            }
				        }
				    },
				    {
					title: {
						enabled: true,
						text: "Productivity (%)"
					},
					min: 0,
					labels: {
						enabled: true
					},
					opposite:true
				}],
				tooltip: {
					shared:true
					// headerFormat: '<span>{series.name}</span><br/>',
					// pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.category} </span>: <b>{point.y} %</b><br/>',
				},
				legend: {
					layout: 'horizontal',
					align: 'right',
					verticalAlign: 'top',
					x: 0,
					y: 30,
					floating: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
					shadow: true,
					itemStyle: {
						fontSize:'px',
					},
					enabled:false
				},	
				credits: {
					enabled: false
				},
				plotOptions: {
					series:{
						cursor: 'pointer',
		                point: {
		                  events: {
		                    click: function () {
		                    	showDetail(result.date, event.point.category,this.options.key);
		                    }
		                  }
		                },
						dataLabels: {
							enabled: true,
							format: '{point.y}',
							style:{
								fontSize: '11px'
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						// cursor: 'pointer',
					},
					spline:{
						// dataLabels: {
						// 	enabled: true,
						// 	rotation: -90,
						// 	style:{
						// 		fontSize: '15px'
						// 	}
						// },
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer',
						point: {	
							events: {
								click: function (event) {
									showDetail(result.date, event.point.category,this.options.key);

								}
							}
						},
					}
				},
				series: [{
			        name: 'Actual Time',
			        type: 'column',
			        data: actual,
			        color: '#73ff83'
				},{
			        name: 'Standard Time',
			        type: 'column',
			        data: standard,
			        color: '#ff7569'
				},{
					type: 'spline',
					data: productivity,
					name: 'Productivity',
					yAxis: 1,
					color:'#fff',
					dataLabels: {
						enabled: true,
						format: '{point.y} %' ,
					},
					showInLegend: false,
					tooltip: {
			            valueSuffix: '%'
			        }
				},]
			});

			//SHIFT B
			var operators = [];
			for(var i = 0; i < result.productivity.length;i++){
				if (result.productivity[i].shift == 'B') {
					operators.push(result.productivity[i].last_check);
				}
			}

			var operator = operators.filter(onlyUnique);

			var op_name = [];
			var actual = [];
			var standard = [];
			var productivity = [];

			for(var i = 0; i < operator.length;i++){
				var act = 0;
				var std = 0;
				var names = '';
				for(var j = 0; j < result.productivity.length;j++){
					if (result.productivity[j].last_check == operator[i]) {
						act = act + parseFloat(result.productivity[j].diff);
						std = std + result.productivity[j].standard_time;
						names = result.productivity[j].name;
					}
				}
				actual.push({y:parseFloat(act.toFixed(2)),key:operator[i]});
				if (std > 460) {
					standard.push({y:460,key:operator[i]});
				}else{
					standard.push({y:parseFloat(std.toFixed(2)),key:operator[i]});
				}
				if (names.split(' ').length == 1) {
					op_name.push(names);
				}else{
					op_name.push(names.split(' ')[0]+' '+names.split(' ')[1]);
				}

				productivity.push({y:parseFloat(((actual[i].y / standard[i].y)*100).toFixed(2)),key:operator[i]});
			}

			Highcharts.chart('container2', {
				chart: {
					animation: false
				},
				title: {
					text: 'OPERATOR PRODUCTIVITY',
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Group B on '+result.dateTitle,
					style: {
						fontSize: '1vw',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					categories: op_name,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						rotation: -45,
						style: {
							fontSize: '12px',
							fontWeight: 'bold'
						}
					},
				},
				yAxis: [{ 
				        labels: {
				            format: '{value}',
				            style: {
				                color: '#fff'
				            }
				        },
				        title: {
				            text: 'Actual Time VS Standard Time',
				            style: {
				                color: '#fff'
				            }
				        }
				    },
				    {
					title: {
						enabled: true,
						text: "Productivity (%)"
					},
					min: 0,
					labels: {
						enabled: true
					},
					opposite:true
				}],
				tooltip: {
					shared:true
					// headerFormat: '<span>{series.name}</span><br/>',
					// pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.category} </span>: <b>{point.y} %</b><br/>',
				},
				legend: {
					layout: 'horizontal',
					align: 'right',
					verticalAlign: 'top',
					x: 0,
					y: 30,
					floating: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
					shadow: true,
					itemStyle: {
						fontSize:'px',
					},
					enabled:false
				},	
				credits: {
					enabled: false
				},
				plotOptions: {
					series:{
						cursor: 'pointer',
		                point: {
		                  events: {
		                    click: function () {
		                    	showDetail(result.date, event.point.category,this.options.key);
		                    }
		                  }
		                },
						dataLabels: {
							enabled: true,
							format: '{point.y}',
							style:{
								fontSize: '11px'
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						// cursor: 'pointer',
					},
					spline:{
						// dataLabels: {
						// 	enabled: true,
						// 	rotation: -90,
						// 	style:{
						// 		fontSize: '15px'
						// 	}
						// },
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer',
						point: {	
							events: {
								click: function (event) {
									showDetail(result.date, event.point.category,this.options.key);

								}
							}
						},
					}
				},
				series: [{
			        name: 'Actual Time',
			        type: 'column',
			        data: actual,
			        color: '#73ff83'
				},{
			        name: 'Standard Time',
			        type: 'column',
			        data: standard,
			        color: '#ff7569'
				},{
					type: 'spline',
					data: productivity,
					name: 'Productivity',
					yAxis: 1,
					color:'#fff',
					dataLabels: {
						enabled: true,
						format: '{point.y} %' ,
					},
					showInLegend: false,
					tooltip: {
			            valueSuffix: '%'
			        }
				},]
			});

			//SHIFT C
			var operators = [];
			for(var i = 0; i < result.productivity.length;i++){
				if (result.productivity[i].shift == 'C') {
					operators.push(result.productivity[i].last_check);
				}
			}

			var operator = operators.filter(onlyUnique);

			var op_name = [];
			var actual = [];
			var standard = [];
			var productivity = [];

			for(var i = 0; i < operator.length;i++){
				var act = 0;
				var std = 0;
				var names = '';
				for(var j = 0; j < result.productivity.length;j++){
					if (result.productivity[j].last_check == operator[i]) {
						act = act + parseFloat(result.productivity[j].diff);
						std = std + result.productivity[j].standard_time;
						names = result.productivity[j].name;
					}
				}
				actual.push({y:parseFloat(act.toFixed(2)),key:operator[i]});
				if (std > 460) {
					standard.push({y:460,key:operator[i]});
				}else{
					standard.push({y:parseFloat(std.toFixed(2)),key:operator[i]});
				}
				if (names.split(' ').length == 1) {
					op_name.push(names);
				}else{
					op_name.push(names.split(' ')[0]+' '+names.split(' ')[1]);
				}

				productivity.push({y:parseFloat(((actual[i].y / standard[i].y)*100).toFixed(2)),key:operator[i]});
			}

			Highcharts.chart('container3', {
				chart: {
					animation: false
				},
				title: {
					text: 'OPERATOR PRODUCTIVITY',
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Group C on '+result.dateTitle,
					style: {
						fontSize: '1vw',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					categories: op_name,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						rotation: -45,
						style: {
							fontSize: '12px',
							fontWeight: 'bold'
						}
					},
				},
				yAxis: [{ 
				        labels: {
				            format: '{value}',
				            style: {
				                color: '#fff'
				            }
				        },
				        title: {
				            text: 'Actual Time VS Standard Time',
				            style: {
				                color: '#fff'
				            }
				        }
				    },
				    {
					title: {
						enabled: true,
						text: "Productivity (%)"
					},
					min: 0,
					labels: {
						enabled: true
					},
					opposite:true
				}],
				tooltip: {
					shared:true
					// headerFormat: '<span>{series.name}</span><br/>',
					// pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.category} </span>: <b>{point.y} %</b><br/>',
				},
				legend: {
					layout: 'horizontal',
					align: 'right',
					verticalAlign: 'top',
					x: 0,
					y: 30,
					floating: true,
					borderWidth: 1,
					backgroundColor:
					Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
					shadow: true,
					itemStyle: {
						fontSize:'px',
					},
					enabled:false
				},	
				credits: {
					enabled: false
				},
				plotOptions: {
					series:{
						cursor: 'pointer',
		                point: {
		                  events: {
		                    click: function () {
		                    	showDetail(result.date, event.point.category,this.options.key);
		                    }
		                  }
		                },
						dataLabels: {
							enabled: true,
							format: '{point.y}',
							style:{
								fontSize: '11px'
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						// cursor: 'pointer',
					},
					spline:{
						// dataLabels: {
						// 	enabled: true,
						// 	rotation: -90,
						// 	style:{
						// 		fontSize: '15px'
						// 	}
						// },
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer',
						point: {	
							events: {
								click: function (event) {
									showDetail(result.date, event.point.category,this.options.key);

								}
							}
						},
					}
				},
				series: [{
			        name: 'Actual Time',
			        type: 'column',
			        data: actual,
			        color: '#73ff83'
				},{
			        name: 'Standard Time',
			        type: 'column',
			        data: standard,
			        color: '#ff7569'
				},{
					type: 'spline',
					data: productivity,
					name: 'Productivity',
					yAxis: 1,
					color:'#fff',
					dataLabels: {
						enabled: true,
						format: '{point.y} %' ,
					},
					showInLegend: false,
					tooltip: {
			            valueSuffix: '%'
			        }
				},]
			});

			details = result.productivity;

			$('#loading').hide();
		}
		else{
			$('#loading').hide();
			openErrorGritter('Error!',result.message);
		}
	});
}

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '2000'
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

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

function changeLocation(){
	$("#location").val($("#locationSelect").val());
}

function changeGroup() {
	$("#group").val($("#groupSelect").val());
}


</script>
@endsection