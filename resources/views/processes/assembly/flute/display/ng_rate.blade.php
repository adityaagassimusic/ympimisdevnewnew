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
				<!-- <div id="period_title" class="col-xs-3" style="background-color: rgba(138, 63, 181,0.9);">
		            <center><span style="color: white; font-size: 24px; font-weight: bold;" id="title_text"></span></center>
		        </div> -->
		        <div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
		            <div class="input-group date">
		                <div class="input-group-addon" style="background-color: rgba(138, 63, 181);color: white;">
		                    <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From">
		            </div>
		        </div>
		        <div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
		            <div class="input-group date">
		                <div class="input-group-addon" style="background-color: rgba(138, 63, 181);color: white;">
		                    <i class="fa fa-calendar"></i>
		                </div>
		                <input type="text" class="form-control pull-right datepicker" id="tanggal_to" name="tanggal_to" placeholder="Select Date To">
		            </div>
		        </div>

		        <div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;" id="div_origin">
					<select class="form-control select2" id="origin" data-placeholder="Select Origin" style="width: 100%;">
						<option value=""></option>
						<option value="Production">Production</option>
						<option value="QA">QA</option>
					</select>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
					<select class="form-control select2" id="model" data-placeholder="Select Model" style="width: 100%;">
						<option value=""></option>
						@foreach($models as $model)
						<option value="{{$model->model}}">{{$model->model}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-1" style="padding-left: 5px;padding-right: 0px;">
					<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
				</div>

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
							@foreach($locations as $location)
							<option value="{{$location->location}}">{{ $location->location}}</option>
							@endforeach
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
				<!-- <div class="col-xs-12" style="background-color: #735CDD">
		            <center>
		            	<span style="color: white; font-size: 2vw; font-weight: bold;">All Line</span>
		            </center>
				</div> -->
				<div class="col-xs-2" style="padding-right: 0;margin-top: 10px">
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
				</div>
				<div class="col-xs-10">
					<div id="container" class="container" style="width: 100%;"></div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
						<div id="container_model" class="container_model" style="width: 100%;"></div>
					</div>
					<div class="col-xs-6" style="padding-left: 5px;padding-right: 0px;">
						<div id="container_key" class="container_key" style="width: 100%;"></div>
					</div>
				</div>

				<!-- <div class="col-xs-12" style="background-color: #735CDD">
		            <center>
		            	<span style="color: white; font-size: 2vw; font-weight: bold;">Line 1</span>
		            </center>
				</div>
				<div class="col-xs-2" style="padding-right: 0;margin-top: 10px">
					<div class="small-box" style="background: #52c9ed; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <span class="text-purple">検査数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="total_line1">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <span class="text-purple">良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ok_line1">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #ff851b; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <span class="text-purple">不良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ng_line1">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>% <span class="text-purple">不良率</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="pctg_line1">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container1" class="container1" style="width: 100%;"></div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
						<div id="container1_model" class="container1_model" style="width: 100%;"></div>
					</div>
					<div class="col-xs-6" style="padding-left: 5px;padding-right: 0px;">
						<div id="container1_key" class="container1_key" style="width: 100%;"></div>
					</div>
				</div> -->
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
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<!-- <center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center> -->
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
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
						<tfoot style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th colspan="6" style="color: white">TOTAL</th>
								<th colspan="6" style="text-align: right;color: white" id="total_all"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#div_origin').hide();
		$('#origin').val('Production').trigger('change');
		if ('{{$emp->department}}'.match(/Quality Assurance/gi) || '{{$emp->department}}'.match(/Management Information System/gi) || '{{$emp->position}}'.match(/Manager/gi)) {
			$('#div_origin').show();
			$('#origin').val('').trigger('change');
		}
		$('#tanggal_from').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#tanggal_to').datepicker({
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
		// setInterval(fetchChart, 300000);
		// fetchLineChart();
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

	

	function fetchChart(){
		$('#loading').show();
		var tanggal_from = $('#tanggal_from').val();
		var tanggal_to = $('#tanggal_to').val();
		var origin = $('#origin').val();
		var model = $('#model').val();

		var data = {
			tanggal_from:tanggal_from,
			tanggal_to:tanggal_to,
			model:model,
			origin:origin
		}

		if ($('#origin').val() == 'QA') {
			var colorsng = '#3f51b5';
		}else if($('#origin').val() == 'Production'){
			var colorsng = '#299123';
		}else{
			var colorsng = '#ff7474';
		}

		$.get('{{ url("fetch/assembly/ng_rate") }}', data, function(result, status, xhr) {
			if(result.status){

				var total = 0;
				var title = result.title;
				var title = $('#origin').val();
				$('#loc').html('<b style="color:white">'+ title +'</b>');


                // $('#title_text').text('NG Rate ' + result.dateTitleFirst+' - '+result.dateTitleLast);
                // var h = $('#period_title').height();

				for(var i = 0; i < result.data.length; i++){
					var Rate = parseFloat(result.data[i].ng_rate);

					$('#total').append().empty();
					$('#total').html(result.data[i].total_check+ '');

					$('#ok').append().empty();
					$('#ok').html(result.data[i].total_ok + '');

					$('#ng').append().empty();
					$('#ng').html(result.data[i].total_ng + '');

					$('#pctg').append().empty();
					$('#pctg').html(Rate.toFixed(1) + '<sup style="font-size: 40px"> %</sup>');
				}


				var categories1 = [];
				var seriesCount1 = [];

				var ng = [], ng2 = [], jml = [], ng_rate = [], series = [], series2 = [];
				var newArr = [];

				$.each(result.ng, function(key, value){
					ctg1 = value.ng_name.toUpperCase();
					if(categories1.indexOf(ctg1) === -1){
						categories1[categories1.length] = ctg1;
					}
					ng.push(value.ng_name);
					jml.push(parseInt(value.jumlah));
					series.push({y:jml[key],key:ng[key]});
				});

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories1,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
					}
					],
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: 0,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'16px',
						},
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
										ShowModal(this.options.key,result.date,'ng_name');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					{
						type: 'column',
						data: series,
						name: 'Total NG',
						colorByPoint: false,
						color: colorsng,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					}
					]
				});


				var catkey = [], kunci = [], kunci2 = [], ng_rate = [], jml = [], series = [], series2 = [];

				$.each(result.ngbody, function(key, value){
					catkey.push(value.model.replace(/_/g, "-"));

					kunci.push(value.model);
					jml.push(parseInt(value.ng));
					series.push({key:kunci[key],y:jml[key]});
				});


				Highcharts.chart('container_model', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG By NG Name & Key',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: catkey,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						
					}
					],
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: 0,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'16px',
						},
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.options.key,result.date,'ngname_key');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					credits: {
						enabled: false
					},
					series :  [
					{
						type: 'column',
						data: series,
						name: 'Total NG',
						colorByPoint: false,
						color: colorsng,
						animation: false
					},
					]
				});

				var catkey = [], kunci = [], kunci2 = [], ng_rate = [], jml = [], series = [], series2 = [];

				$.each(result.ngkey, function(key, value){

					catkey.push(value.ongko);

					kunci.push(value.ongko);
					jml.push(parseInt(value.ng));
					series.push({key:kunci[key],y:jml[key]});

				});


				Highcharts.chart('container_key', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG By Key',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: catkey,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						
					}
					],
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: 0,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'16px',
						},
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.options.key,result.date,'key');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					credits: {
						enabled: false
					},
					series :  [
					{
						type: 'column',
						data: series,
						name: 'Total NG',
						colorByPoint: false,
						color: colorsng,
						animation: false
					},
					]
				});

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
}

// function fetchLineChart(){
// 		// $('#loading').show();
// 		// var location = $('#location').val();
// 		var tanggal_from = $('#tanggal_from').val();
// 		var tanggal_to = $('#tanggal_to').val();
// 		var origin = $('#origin').val();

// 		var data = {
// 			tanggal_from:tanggal_from,
// 			tanggal_to:tanggal_to,
// 			// location:location,
// 			origin:origin
// 		}

// 		if ($('#origin').val() == 'QA') {
// 			var colorsng = '#3f51b5';
// 		}else if($('#origin').val() == 'Production'){
// 			var colorsng = '#299123';
// 		}else{
// 			var colorsng = '#ff7474';
// 		}

// 		$.get('{{ url("fetch/assembly/ng_rate/line") }}', data, function(result, status, xhr) {
// 			if(result.status){

// 				var total = 0;
// 				// var title = result.title;
// 				// var title = $('#origin').val();

// 				for(var i = 0; i < result.datastatline.length; i++){
// 					var Rate = parseFloat(result.datastatline[i].ng_rate);

// 					$('#total_line1').append().empty();
// 					$('#total_line1').html(result.datastatline[i].total_check+ '');

// 					$('#ok_line1').append().empty();
// 					$('#ok_line1').html(result.datastatline[i].total_ok + '');

// 					$('#ng_line1').append().empty();
// 					$('#ng_line1').html(result.datastatline[i].total_ng + '');

// 					$('#pctg_line1').append().empty();
// 					$('#pctg_line1').html(Rate.toFixed(1) + '<sup style="font-size: 40px"> %</sup>');
// 				}

// 				var categories1 = [];
// 				var seriesCount1 = [];

// 				var ng = [], ng2 = [], jml = [], ng_rate = [], series = [], series2 = [];
// 				var newArr = [];

// 				$.each(result.ngline, function(key, value){
// 					ctg1 = value.ng_name.toUpperCase();
// 					if(categories1.indexOf(ctg1) === -1){
// 						categories1[categories1.length] = ctg1;
// 					}
					
// 					// if(newArr[value.ng_name] == undefined){
// 					// 	newArr[value.ng_name] =0;
// 					// }

// 					// newArr[value.ng_name] += value.quantity;

// 					ng.push(value.ng_name);
// 					jml.push(parseInt(value.jumlah));
// 					series.push([ng[key], jml[key]]);

// 					// ng2.push(value.ng_name);
// 					// ng_rate.push(parseFloat(value.rate));
// 					// series2.push([ng2[key], ng_rate[key]]);


// 				});

// 				Highcharts.chart('container1', {
// 					chart: {
// 						type: 'column',
// 						height: '330',
// 						backgroundColor: "rgba(0,0,0,0)"
// 					},
// 					title: {
// 						text: 'NG Line 1',
// 						style: {
// 							fontSize: '25px',
// 							fontWeight: 'bold'
// 						}
// 					},
// 					subtitle: {
// 						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
// 						style: {
// 							fontSize: '1vw',
// 							fontWeight: 'bold'
// 						}
// 					},
// 					xAxis: {
// 						categories: categories1,
// 						type: 'category',
// 						gridLineWidth: 1,
// 						gridLineColor: 'RGB(204,255,255)',
// 						lineWidth:2,
// 						lineColor:'#9e9e9e',
// 						labels: {
// 							style: {
// 								fontSize: '14px',
// 								fontWeight: 'bold'
// 							}
// 						},
// 					},
// 					yAxis: [{
// 						title: {
// 							text: 'Qty NG Pc(s)',
// 							style: {
// 								color: '#eee',
// 								fontSize: '18px',
// 								fontWeight: 'bold',
// 								fill: '#6d869f'
// 							}
// 						},
// 						labels:{
// 							style:{
// 								fontSize:"14px"
// 							}
// 						},
// 						type: 'linear',
						
// 					}
// 					// , { // Secondary yAxis
// 					// 	title: {
// 					// 		text: 'NG Rate (%)',
// 					// 		style: {
// 					// 			color: '#eee',
// 					// 			fontSize: '20px',
// 					// 			fontWeight: 'bold',
// 					// 			fill: '#6d869f'
// 					// 		}
// 					// 	},
// 					// 	labels:{
// 					// 		style:{
// 					// 			fontSize:"20px"
// 					// 		}
// 					// 	},
// 					// 	type: 'linear',
// 					// 	opposite: true

// 					// }
// 					],
// 					tooltip: {
// 						headerFormat: '<span>NG Name</span><br/>',
// 						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
// 					},
// 					legend: {
// 						layout: 'horizontal',
// 						align: 'right',
// 						verticalAlign: 'top',
// 						x: 0,
// 						y: 20,
// 						floating: true,
// 						borderWidth: 1,
// 						backgroundColor:
// 						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
// 						shadow: true,
// 						itemStyle: {
// 							fontSize:'16px',
// 						},
// 					},	
// 					credits: {
// 						enabled: false
// 					},
// 					plotOptions: {
// 						series:{
// 							cursor: 'pointer',
// 							point: {
// 								events: {
// 									click: function () {
// 										ShowModal(this.category,result.date,'ng_name');
// 									}
// 								}
// 							},
// 							dataLabels: {
// 								enabled: true,
// 								format: '{point.y}',
// 								style:{
// 									fontSize: '1vw'
// 								}
// 							},
// 							animation: {
// 								enabled: true,
// 								duration: 800
// 							},
// 							pointPadding: 0.93,
// 							groupPadding: 0.93,
// 							borderWidth: 0.93,
// 							cursor: 'pointer'
// 						},
// 					},
// 					series: [
// 					// {
// 					// 	type: 'column',
// 					// 	data: series2,
// 					// 	name: 'NG Rate',
// 					// 	yAxis:1,
// 					// 	colorByPoint: false,
// 					// 	color:'#ff9800',
// 					// 	animation: false,
// 					// 	dataLabels: {
// 					// 		enabled: true,
// 					// 		format: '{point.y}%' ,
// 					// 		style:{
// 					// 			fontSize: '1vw',
// 					// 			textShadow: false
// 					// 		},
// 					// 	},
						
// 					// },
// 					{
// 						type: 'column',
// 						data: series,
// 						name: 'Total NG',
// 						colorByPoint: false,
// 						color: colorsng,
// 						animation: false,
// 						dataLabels: {
// 							enabled: true,
// 							format: '{point.y}' ,
// 							style:{
// 								fontSize: '1vw',
// 								textShadow: false
// 							},
// 						},
// 					}
// 					]
// 				});


// 				var catkey = [], kunci = [], kunci2 = [], ng_rate = [], jml = [], series = [], series2 = [];

// 				$.each(result.ngbodyline, function(key, value){
// 					catkey.push(value.model);

// 					kunci.push(value.model);
// 					jml.push(parseInt(value.ng));
// 					series.push([kunci[key], jml[key]]);

// 					kunci2.push(value.model);
// 					ng_rate.push(parseFloat(value.rate.toFixed(1)));
// 					series2.push([kunci2[key], ng_rate[key]]);
// 				});


// 				Highcharts.chart('container1_model', {
// 					chart: {
// 						type: 'column',
// 						height: '330',
// 						backgroundColor: "rgba(0,0,0,0)"
// 					},
// 					title: {
// 						text: 'NG By NG Name & Key Line 1',
// 						style: {
// 							fontSize: '25px',
// 							fontWeight: 'bold'
// 						}
// 					},
// 					subtitle: {
// 						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
// 						style: {
// 							fontSize: '1vw',
// 							fontWeight: 'bold'
// 						}
// 					},
// 					xAxis: {
// 						categories: catkey,
// 						type: 'category',
// 						gridLineWidth: 1,
// 						gridLineColor: 'RGB(204,255,255)',
// 						lineWidth:2,
// 						lineColor:'#9e9e9e',
// 						labels: {
// 							style: {
// 								fontSize: '14px',
// 								fontWeight: 'bold'
// 							}
// 						},
// 					},
// 					yAxis: [{
// 						title: {
// 							text: 'Qty NG Pc(s)',
// 							style: {
// 								color: '#eee',
// 								fontSize: '18px',
// 								fontWeight: 'bold',
// 								fill: '#6d869f'
// 							}
// 						},
// 						labels:{
// 							style:{
// 								fontSize:"14px"
// 							}
// 						},
// 						type: 'linear',
						
// 					}
// 					// , { // Secondary yAxis
// 					// 	title: {
// 					// 		text: 'NG Rate (%)',
// 					// 		style: {
// 					// 			color: '#eee',
// 					// 			fontSize: '20px',
// 					// 			fontWeight: 'bold',
// 					// 			fill: '#6d869f'
// 					// 		}
// 					// 	},
// 					// 	labels:{
// 					// 		style:{
// 					// 			fontSize:"20px"
// 					// 		}
// 					// 	},
// 					// 	type: 'linear',
// 					// 	opposite: true

// 					// }
// 					],
// 					legend: {
// 						layout: 'horizontal',
// 						align: 'right',
// 						verticalAlign: 'top',
// 						x: 0,
// 						y: 20,
// 						floating: true,
// 						borderWidth: 1,
// 						backgroundColor:
// 						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
// 						shadow: true,
// 						itemStyle: {
// 							fontSize:'16px',
// 						},
// 					},
					
// 					tooltip: {
// 						headerFormat: '<span>Model</span><br/>',
// 						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name}</span>: <b>{point.y}</b><br/>',
// 					},
// 					plotOptions: {
// 						series:{
// 							cursor: 'pointer',
// 							point: {
// 								events: {
// 									click: function () {
// 										ShowModal(this.category,result.date,'model');
// 									}
// 								}
// 							},
// 							dataLabels: {
// 								enabled: true,
// 								format: '{point.y}',
// 								style:{
// 									fontSize: '1vw'
// 								}
// 							},
// 							animation: {
// 								enabled: true,
// 								duration: 800
// 							},
// 							pointPadding: 0.93,
// 							groupPadding: 0.93,
// 							borderWidth: 0.93,
// 							cursor: 'pointer'
// 						},
// 					},
// 					credits: {
// 						enabled: false
// 					},
// 					series :  [
// 					// {
// 					// 	type: 'column',
// 					// 	data: series2,
// 					// 	name: 'NG Rate',
// 					// 	yAxis:1,
// 					// 	colorByPoint: false,
// 					// 	color:'#ff9800',
// 					// 	animation: false,
// 					// 	dataLabels: {
// 					// 		enabled: true,
// 					// 		format: '{point.y}%',
// 					// 		style:{
// 					// 			fontSize: '0.9vw',
// 					// 			textShadow : false
// 					// 		},
// 					// 	},
// 					// },
// 					{
// 						type: 'column',
// 						data: series,
// 						name: 'Total NG',
// 						colorByPoint: false,
// 						color: colorsng,
// 						animation: false
// 					},
// 					]
// 				});

// 				var catkey = [], kunci = [], kunci2 = [], ng_rate = [], jml = [], series = [], series2 = [];

// 				$.each(result.ngkeyline, function(key, value){

// 					catkey.push(value.ongko);

// 					kunci.push(value.ongko);
// 					jml.push(parseInt(value.ng));
// 					series.push([kunci[key], jml[key]]);

// 					kunci2.push(value.ongko);
// 					ng_rate.push(parseFloat(value.rate.toFixed(1)));
// 					series2.push([kunci2[key], ng_rate[key]]);

// 				});


// 				Highcharts.chart('container1_key', {
// 					chart: {
// 						type: 'column',
// 						height: '330',
// 						backgroundColor: "rgba(0,0,0,0)"
// 					},
// 					title: {
// 						text: 'NG By Key Line 1',
// 						style: {
// 							fontSize: '25px',
// 							fontWeight: 'bold'
// 						}
// 					},
// 					subtitle: {
// 						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
// 						style: {
// 							fontSize: '1vw',
// 							fontWeight: 'bold'
// 						}
// 					},
// 					xAxis: {
// 						categories: catkey,
// 						type: 'category',
// 						gridLineWidth: 1,
// 						gridLineColor: 'RGB(204,255,255)',
// 						lineWidth:2,
// 						lineColor:'#9e9e9e',
// 						labels: {
// 							style: {
// 								fontSize: '14px',
// 								fontWeight: 'bold'
// 							}
// 						},
// 					},
// 					yAxis: [{
// 						title: {
// 							text: 'Qty NG Pc(s)',
// 							style: {
// 								color: '#eee',
// 								fontSize: '18px',
// 								fontWeight: 'bold',
// 								fill: '#6d869f'
// 							}
// 						},
// 						labels:{
// 							style:{
// 								fontSize:"14px"
// 							}
// 						},
// 						type: 'linear',
						
// 					}
// 					// , { // Secondary yAxis
// 					// 	title: {
// 					// 		text: 'NG Rate (%)',
// 					// 		style: {
// 					// 			color: '#eee',
// 					// 			fontSize: '20px',
// 					// 			fontWeight: 'bold',
// 					// 			fill: '#6d869f'
// 					// 		}
// 					// 	},
// 					// 	labels:{
// 					// 		style:{
// 					// 			fontSize:"20px"
// 					// 		}
// 					// 	},
// 					// 	type: 'linear',
// 					// 	opposite: true

// 					// }
// 					],
// 					legend: {
// 						layout: 'horizontal',
// 						align: 'right',
// 						verticalAlign: 'top',
// 						x: 0,
// 						y: 20,
// 						floating: true,
// 						borderWidth: 1,
// 						backgroundColor:
// 						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
// 						shadow: true,
// 						itemStyle: {
// 							fontSize:'16px',
// 						},
// 					},
					
// 					tooltip: {
// 						headerFormat: '<span>Model</span><br/>',
// 						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name}</span>: <b>{point.y}</b><br/>',
// 					},
// 					plotOptions: {
// 						series:{
// 							cursor: 'pointer',
// 							point: {
// 								events: {
// 									click: function () {
// 										ShowModal(this.category,result.date,'model');
// 									}
// 								}
// 							},
// 							dataLabels: {
// 								enabled: true,
// 								format: '{point.y}',
// 								style:{
// 									fontSize: '1vw'
// 								}
// 							},
// 							animation: {
// 								enabled: true,
// 								duration: 800
// 							},
// 							pointPadding: 0.93,
// 							groupPadding: 0.93,
// 							borderWidth: 0.93,
// 							cursor: 'pointer'
// 						},
// 					},
// 					credits: {
// 						enabled: false
// 					},
// 					series :  [
// 					// {
// 					// 	type: 'column',
// 					// 	data: series2,
// 					// 	name: 'NG Rate',
// 					// 	yAxis:1,
// 					// 	colorByPoint: false,
// 					// 	color:'#ff9800',
// 					// 	animation: false,
// 					// 	dataLabels: {
// 					// 		enabled: true,
// 					// 		format: '{point.y}%',
// 					// 		style:{
// 					// 			fontSize: '0.9vw',
// 					// 			textShadow : false
// 					// 		},
// 					// 	},
// 					// },
// 					{
// 						type: 'column',
// 						data: series,
// 						name: 'Total NG',
// 						colorByPoint: false,
// 						color: colorsng,
// 						animation: false
// 					},
// 					]
// 				});

// 				$('#loading').hide();
// 			}
// 			else{
// 				$('#loading').hide();
// 				openErrorGritter('Error!',result.message);
// 			}
// 		});
// }

function ShowModal(cat,date,type) {
	$("#loading").show();
	var location = $('#location').val();
	var origin = $('#origin').val();
	var data = {
		cat:cat,
		tanggal_from: $('#tanggal_from').val(),
		tanggal_to: $('#tanggal_to').val(),
		model: $('#model').val(),
		type:type,
		location:location,
		origin:origin,
	}

	$.get('{{ url("fetch/assembly/ng_rate_detail") }}', data, function(result, status, xhr) {
		if(result.status){
			$('#tableDetail').DataTable().clear();
			$('#tableDetail').DataTable().destroy();
			$('#tableDetailBody').html('');
			var tableBody = '';
			var index = 1;
			var total_qty = 0;
			$.each(result.detail, function(key, value) {
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
				total_qty++;
				index++;
			});
			$('#tableDetailBody').append(tableBody);
			$('#total_all').html(total_qty);

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
			});
			if (type === 'ng_name') {
				$('#modalDetailTitle').html('Detail NG '+cat+'<br>Tanggal '+result.dateTitleFirst+' - '+result.dateTitleLast);
			}else if (type === 'ngname_key') {
				$('#modalDetailTitle').html('Detail NG '+cat.split('_')[0]+' Kunci '+cat.split('_')[1]+'<br>Tanggal '+result.dateTitleFirst+' - '+result.dateTitleLast);
			}else if (type === 'key') {
				$('#modalDetailTitle').html('Detail NG Kunci '+cat+'<br>Tanggal '+result.dateTitleFirst+' - '+result.dateTitleLast);
			}
			$("#loading").hide();
			$('#modalDetail').modal('show');
		}else{
			$("#loading").hide();
			openErrorGritter('Error!',result.message);
		}
	});
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