@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
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
		border:1px solid rgb(211,211,211);
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<!-- <div class="row"> -->
				<form method="GET" action="{{ action('AssemblyProcessController@indexAssemblyResumeNG') }}">
				<div id="period_title" class="col-xs-5" style="background-color: #7e5686;color: white;">
		            <center><span style="font-size: 1.6vw; font-weight: bold;" id="title_text"></span></center>
					<!-- <div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div> -->
		        </div>
		        <div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
		            <div class="input-group date">
		                <div class="input-group-addon" style="background-color: #7e5686;color: white;">
		                    <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right datepicker" id="date_from" name="date_from" placeholder="Select Date From">
		            </div>
		        </div>
		        <div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
		            <div class="input-group date">
		                <div class="input-group-addon" style="background-color: #7e5686;color: white;border-color: ">
		                    <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right datepicker" id="date_to" name="date_to" placeholder="Select Date To">
		            </div>
		        </div>
		        <input type="hidden" name="origin_group_code" value="{{$_GET['origin_group_code']}}">
		        <div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
		            <select style="width: 100%" data-placeholder="Pilih Nama NG" class="form-control select2" id="ng_name" name="ng_name">
		            	<option value=""></option>
		            </select>
		        </div>

				<div class="col-xs-1" style="padding-left: 5px;padding-right: 0px;">
					<button class="btn btn-success" type="submit" style="background-color: #7e5686;border-color: #7e5686;color: white;"><i class="fa fa-search"></i> Search</button>
				</div>
				</form>

					<!-- <div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;padding-left: 5px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_to" name="tanggal_to" placeholder="Select Date To">
						</div>
					</div> -->
					<!-- <div class="col-xs-2" style="padding-right: 0;">
						<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Locations" onchange="changeLocation()" style="width: 100%;"> 
						</select>
						<input type="text" name="location" id="location" hidden>	
					</div> -->

					

					<!-- <div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div> -->
			<!-- </div> -->
		</div>
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #52c9ed; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <span class="text-purple">検査数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <span class="text-purple">良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ok">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #ff851b; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <span class="text-purple">不良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ng">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>% <span class="text-purple">不良率</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="pctg">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div> -->
				<div class="col-xs-4">
					<table style="width: 100%;height: 85vh;">
						<tr>
							<th style="border: 3px solid white;font-size: 2vw;background-color: #61baff;text-align: center;height: 2%">Flute</th>
						</tr>
						<tr>
							<th style="border: 3px solid white;font-size: 3vw;background-color: none;text-align: center;height: 4%;color: white;" id="ng_name_fl">Kizu</th>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 15vw;padding:0px;height: 70%;background-color: none;font-weight: bold;color: white; cursor: pointer;" id="qty_fl" onclick="detail('041')">
								0
							</td>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 4vw;padding:0px;height: 6%;background-color: rgb(254, 204, 254);font-weight: bold;" id="persen_fl">
								0 %
							</td>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 4vw;padding:0px;height: 20%;background-color: none;font-weight: bold;color: white;">
								<div id="container_fl"></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-xs-4">
					<table style="width: 100%;height: 85vh;">
						<tr>
							<th style="border: 3px solid white;font-size: 2vw;background-color: #ffbd44;text-align: center;height: 2%">Clarinet</th>
						</tr>
						<tr>
							<th style="border: 3px solid white;font-size: 3vw;background-color: none;text-align: center;height: 4%;color: white;" id="ng_name_cl">Kizu</th>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 15vw;padding:0px;height: 70%;background-color: none;font-weight: bold;color: white;cursor: pointer;" id="qty_cl" onclick="detail('042')">
								0
							</td>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 4vw;padding:0px;height: 6%;background-color: rgb(254, 204, 254);font-weight: bold;" id="persen_cl">
								0 %
							</td>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 4vw;padding:0px;height: 20%;background-color: none;font-weight: bold;color: white;">
								<div id="container_cl"></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-xs-4">
					<table style="width: 100%;height: 85vh;">
						<tr>
							<th style="border: 3px solid white;font-size: 2vw;background-color: #d2ff69;text-align: center;height: 2%">Saxophone</th>
						</tr>
						<tr>
							<th style="border: 3px solid white;font-size: 3vw;background-color: none;text-align: center;height: 4%;color: white;" id="ng_name_sx">Kizu</th>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 15vw;padding:0px;height: 70%;background-color: none;font-weight: bold;color: white;cursor: pointer;" id="qty_sx" onclick="detail('043')">
								0
							</td>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 4vw;padding:0px;height: 6%;background-color: rgb(254, 204, 254);font-weight: bold;" id="persen_sx">
								0 %
							</td>
						</tr>
						<tr>
							<td style="border: 3px solid white;font-size: 4vw;padding:0px;height: 20%;background-color: none;font-weight: bold;color: white;">
								<div id="container_sx"></div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px;margin-top: 10px;">
				<!-- <center>
					<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
				</center> -->
				<div class="col-xs-12">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);font-weight: ">
							<tr>
								<th style="width: 1%;color: white">#</th>
								<th style="width: 2%;color: white">Serial Number</th>
								<th style="width: 2%;color: white">Model</th>
								<th style="width: 3%;color: white">Loc</th>
								<th style="width: 3%;color: white">NG Name</th>
								<th style="width: 3%;color: white">Onko</th>
								<th style="width: 3%;color: white">Qty</th>
								<th style="width: 3%;color: white">Value Atas</th>
								<th style="width: 3%;color: white">Value Bawan</th>
								<th style="width: 3%;color: white">NG Loc</th>
								<th style="width: 3%;color: white">Emp Kensa</th>
								<th style="width: 3%;color: white">At</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<div class="col-xs-12" style="padding-left:0px;padding-right:0px;">
					<button class="btn btn-danger pull-right" onclick="$('#modalDetail').modal('hide')"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var ng = null;
	var process_all = null;
	var emp = null;
	var ng_monthly = null;
	var date_from = null;
	var date_from_title = null;
	var date_to = null;
	var date_to_title = null;
	var ng_namee = null;

	jQuery(document).ready(function(){
		$('#date_from').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#date_to').datepicker({
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
		ng = null;
		ng_monthly = null;
		date_from = null;
		date_from_title = null;
		date_to = null;
		date_to_title = null;
		ng_namee = null;
		process_all = null;
		emp = null;

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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fetchChart(){
		// $('#loading').show();
		// var location = $('#location').val();
		var origin_group_code = '{{$_GET["origin_group_code"]}}';
		var date_from = '{{$_GET["date_from"]}}';
		var date_to = '{{$_GET["date_to"]}}';
		var ng_name = '{{$_GET["ng_name"]}}';

		var data = {
			origin_group_code:origin_group_code,
			date_from:date_from,
			date_to:date_to,
			ng_name:ng_name,
		}

		$.get('{{ url("fetch/assembly/resume_ng") }}', data, function(result, status, xhr) {
			if(result.status){


                if (result.dateTitleFirst == result.dateTitleLast) {
                	$('#title_text').text('Resume NG '+ '{{$_GET["ng_name"]}}'+' '+ result.dateTitleFirst);
                }else{
                	$('#title_text').text('Resume NG '+ '{{$_GET["ng_name"]}}'+' '+ result.dateTitleFirst+' - '+result.dateTitleLast);
                }

                $('#ng_name_fl').html(ng_name);
                $('#ng_name_cl').html(ng_name);
                $('#ng_name_sx').html(ng_name);
                
                var qty_fl = 0;
                var qty_sx = 0;
                var qty_cl = 0;

                var persen_fl = 0;
                var persen_sx = 0;
                var persen_cl = 0;

                var serial_number_fl = [];
                var serial_number_cl = [];
                var serial_number_sx = [];

                for(var i = 0; i < result.ng.length;i++){
                	if (result.ng[i].origin_group_code == '042') {
                		qty_cl++;
                		serial_number_cl.push(result.ng[i].serial_number);
                	}
                	if (result.ng[i].origin_group_code == '043') {
                		qty_sx++;
                		serial_number_sx.push(result.ng[i].serial_number);
                	}
                	if (result.ng[i].origin_group_code == '041') {
                		qty_fl++;
                		serial_number_fl.push(result.ng[i].serial_number);
                	}
                }

                $('#qty_fl').html(qty_fl);
                $('#qty_cl').html(qty_cl);
                $('#qty_sx').html(qty_sx);

                var serial_number_fl_unik = serial_number_fl.filter(onlyUnique);
                var serial_number_cl_unik = serial_number_cl.filter(onlyUnique);
                var serial_number_sx_unik = serial_number_sx.filter(onlyUnique);

                for(var i = 0; i < result.perolehan.length;i++){
                	if (result.perolehan[i].origin_group_code == '041') {
                		persen_fl = ((parseInt(serial_number_fl_unik.length) / parseInt(result.perolehan[i].perolehan))*100).toFixed(1);
                	}
                	if (result.perolehan[i].origin_group_code == '042') {
                		persen_cl = ((parseInt(serial_number_cl_unik.length) / parseInt(result.perolehan[i].perolehan))*100).toFixed(1);
                	}
                	if (result.perolehan[i].origin_group_code == '043') {
                		persen_sx = ((parseInt(serial_number_sx_unik.length) / parseInt(result.perolehan[i].perolehan))*100).toFixed(1);
                	}
                }

                $('#persen_fl').html(persen_fl+' %');
                $('#persen_cl').html(persen_cl+' %');
                $('#persen_sx').html(persen_sx+' %');

                $('#ng_name').html('');
                var ng_names = '';

                ng_names += '<option value=""></option>';
                for(var i = 0; i < result.ng_name.length;i++){
                	ng_names += '<option value="'+result.ng_name[i].ng_name+'">'+result.ng_name[i].ng_name+'</option>';
                }

                $('#ng_name').append(ng_names);

                $('#ng_name').val(ng_name).trigger('change');
                $('#date_from').val(date_from);
                $('#date_to').val(date_to);

                ng = result.ng;
                process_all = result.process_all;
                emp = result.emp;
                ng_monthly = result.ng_monthly;
                date_to = result.date_to;
                date_to = result.date_to;
                date_from_title = result.dateTitleFirst;
                date_to_title = result.dateTitleLast;
                ng_namee = ng_name;

                var date_fl = [];
                var date_cl = [];
                var date_sx = [];

                for(var i = 0; i < result.ng_monthly.length;i++){
                	if (result.ng_monthly[i].origin_group_code == '041') {
                		date_fl.push(result.ng_monthly[i].dates);
                	}
                	if (result.ng_monthly[i].origin_group_code == '042') {
                		date_cl.push(result.ng_monthly[i].dates);
                	}
                	if (result.ng_monthly[i].origin_group_code == '043') {
                		date_sx.push(result.ng_monthly[i].dates);
                	}
                }

                var date_fl_unik = date_fl.filter(onlyUnique);
                var date_cl_unik = date_cl.filter(onlyUnique);
                var date_sx_unik = date_sx.filter(onlyUnique);

                var category_fl = [];
                var series_fl = [];

                var category_cl = [];
                var series_cl = [];

                var category_sx = [];
                var series_sx = [];

                for(var i = 0; i < date_fl_unik.length;i++){
                	category_fl.push(date_fl_unik[i]);
                	var qty = 0;
                	for(var j = 0; j < result.ng_monthly.length;j++){
                		if (result.ng_monthly[j].dates == date_fl_unik[i] && result.ng_monthly[j].origin_group_code == '041') {
                			qty++;
                		}
                	}
                	series_fl.push(parseInt(qty));
                }

                Highcharts.chart('container_fl', {
                        chart: {
                            backgroundColor: null,
                            type: 'line',
                            height: '180',
                        },
                        title: {
                            text: 'Daily NG '+ng_name,
                            style: {
                                fontSize: '14px'
                            }
                        },
                        // subtitle: {
                        //     text: category_fl,
                        //     style: {
                        //         fontSize: '10px'
                        //     }
                        // },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories:category_fl,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '12px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function (event) {
											// showDetail(result.date, event.point.category);
										}
									}
								},
							}
						},
                        series: [
                        {
                            name: 'Qty NG',
                            type: 'line',
                            color: '#61baff',
                            data: series_fl
                        }]
                    });

                for(var i = 0; i < date_cl_unik.length;i++){
                	category_cl.push(date_cl_unik[i]);
                	var qty = 0;
                	for(var j = 0; j < result.ng_monthly.length;j++){
                		if (result.ng_monthly[j].dates == date_cl_unik[i] && result.ng_monthly[j].origin_group_code == '042') {
                			qty++;
                		}
                	}
                	series_cl.push(parseInt(qty));
                }

                Highcharts.chart('container_cl', {
                        chart: {
                            backgroundColor: null,
                            type: 'line',
                            height: '180',
                        },
                        title: {
                            text: 'Daily NG '+ng_name,
                            style: {
                                fontSize: '14px'
                            }
                        },
                        // subtitle: {
                        //     text: category_cl,
                        //     style: {
                        //         fontSize: '10px'
                        //     }
                        // },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories:category_cl,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '12px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function (event) {
											// showDetail(result.date, event.point.category);
										}
									}
								},
							}
						},
                        series: [
                        {
                            name: 'Qty NG',
                            type: 'line',
                            color: '#ffbd44',
                            data: series_cl
                        }]
                    });

                for(var i = 0; i < date_sx_unik.length;i++){
                	category_sx.push(date_sx_unik[i]);
                	var qty = 0;
                	for(var j = 0; j < result.ng_monthly.length;j++){
                		if (result.ng_monthly[j].dates == date_sx_unik[i] && result.ng_monthly[j].origin_group_code == '041') {
                			qty++;
                		}
                	}
                	series_sx.push(parseInt(qty));
                }

                Highcharts.chart('container_sx', {
                        chart: {
                            backgroundColor: null,
                            type: 'line',
                            height: '180',
                        },
                        title: {
                            text: 'Daily NG '+ng_name,
                            style: {
                                fontSize: '14px'
                            }
                        },
                        // subtitle: {
                        //     text: category_sx,
                        //     style: {
                        //         fontSize: '10px'
                        //     }
                        // },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories:category_sx,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '12px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function (event) {
											// showDetail(result.date, event.point.category);
										}
									}
								},
							}
						},
                        series: [
                        {
                            name: 'Qty NG',
                            type: 'line',
                            color: '#d2ff69',
                            data: series_sx
                        }]
                    });

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
}

function detail(origin_group_code) {
	$("#loading").show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var tableBody = '';
	var index = 1;
	$.each(ng, function(key, value) {
		if (value.origin_group_code == origin_group_code) {
			tableBody += '<tr>';
			tableBody += '<td>'+index+'</td>';
			tableBody += '<td>'+value.serial_number+'</td>';
			tableBody += '<td>'+value.model+'</td>';
			tableBody += '<td>'+value.location+'</td>';
			tableBody += '<td>'+value.ng_name+'</td>';
			tableBody += '<td>'+value.ongko+'</td>';
			var qty = 1;
			if (value.value_bawah == null) {
				tableBody += '<td>'+qty+'</td>';
				tableBody += '<td></td>';
				tableBody += '<td></td>';
			}else{
				tableBody += '<td>'+qty+'</td>';
				tableBody += '<td>'+value.value_bawah+'</td>';
				tableBody += '<td>'+value.value_atas+'</td>';
			}
			tableBody += '<td>'+(value.value_lokasi || "")+'</td>';
			tableBody += '<td>'+value.employee_id+'<br>'+value.name+'</td>';
			tableBody += '<td>'+value.created+'</td>';
			tableBody += '</tr>';
			index++;
		}
	});
	$('#tableDetailBody').append(tableBody);

	var table = $('#tableDetail').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
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
		// "footerCallback": function ( row, data, start, end, display ) {
  //           var api = this.api(), data;
 
  //           var intVal = function ( i ) {
  //               return typeof i === 'string' ?
  //                   i.replace(/[\$,]/g, '')*1 :
  //                   typeof i === 'number' ?
  //                       i : 0;
  //           };

  //           pageTotal = api
  //               .column( 7, { page: 'current'} )
  //               .data()
  //               .reduce( function (a, b) {
  //                   return intVal(a) + intVal(b);
  //               }, 0 );
 
  //           $( api.column( 7 ).footer() ).html(
  //               pageTotal
  //           );
  //       }
	});
	if (date_to_title == date_from_title) {
		$('#modalDetailTitle').html('Detail NG '+ng_namee+'<br>Tanggal '+date_from_title);
	}else{
		$('#modalDetailTitle').html('Detail NG '+ng_namee+'<br>Tanggal '+date_from_title+' - '+date_to_title);
	}
	$("#loading").hide();
	$('#modalDetail').modal('show');
}

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
	}

</script>
@endsection