@extends('layouts.display')
@section('stylesheets')
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<form method="GET" action="{{ action('AssemblyProcessController@indexOpRate') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Locations" onchange="changeLocation()" style="width: 100%;"> 	
							@foreach($location as $location)
							<option value="{{$location}}">{{ trim($location, "'")}}</option>
							@endforeach
						</select>
						<input type="text" name="location" id="location" hidden>	
					</div>
					<div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div>
				</form>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 1%;">
			<div id="container" class="container" style="width: 100%;height: 500px"></div>
		</div>
	</div>
</section>

<!-- start modal -->
<div class="modal fade" id="myModal" style="color: black;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>NG Rate Operator Details</b></h4>
				<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12" style="margin-bottom: 20px;">
						<div class="col-md-6">
							<h5 class="modal-title">NG Rate</h5><br>
							<h5 class="modal-title" id="ng_rate"></h5>
						</div>
						<div class="col-md-6">
							<div id="modal_ng" style="height: 200px"></div>
						</div>
					</div>

					<div class="col-md-8">
						<table id="welding-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead id="welding-ng-log-head" style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th colspan="6" style="text-align: center;">NOT GOOD</th>
								</tr>
								<tr>
									<th style="width: 15%;">Finish Welding</th>
									<th>Model</th>
									<th>Key</th>
									<th>OP Kensa</th>
									<th>NG Name</th>
									<th style="width: 5%;">Material Qty</th>
								</tr>
							</thead>
							<tbody id="welding-ng-log-body">
							</tbody>
						</table>
					</div>

					<div class="col-md-6">
						<table id="welding-log" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead id="welding-log-head" style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th colspan="5" style="text-align: center;">GOOD</th>
								</tr>
								<tr>
									<th>Finish Welding</th>
									<th>Model</th>
									<th>Key</th>
									<th>OP Kensa</th>
									<th>Material Qty</th>
								</tr>
							</thead>
							<tbody id="welding-log-body">
							</tbody>
						</table>
					</div>
					<div class="col-md-6">
						<table id="welding-cek" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead id="welding-cek-head" style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th colspan="5" style="text-align: center;">TOTAL CEK</th>
								</tr>
								<tr>
									<th>Finish Welding</th>
									<th>Model</th>
									<th>Key</th>
									<th>OP Kensa</th>
									<th>Material Qty</th>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#tanggal').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
		fetchChart();
		setInterval(fetchChart, 30000);
	});

	function fetchChart(){

		var location = "{{$_GET['location']}}";
		var tanggal = "{{$_GET['tanggal']}}";
		var data = {
			tanggal:tanggal,
			location:location
		}

		$.get('{{ url("fetch/assembly/op_ng") }}', data, function(result, status, xhr) {
			if(result.status){

				var total = 0;
				var title = result.title;
				$('#loc').html('<b style="color:white">'+ title +'</b>');

				var target = result.ng_target;

				// GROUP A
				var op_name = [];
				var rate = [];
				var ng = [];
				var data = [];
				var data2 = [];
				var loop = 0;

				for(var i = 0; i < result.ng_rate.length; i++){
					op_name.push(result.ng_rate[i].employee_id+'-'+result.ng_rate[i].name.replace(/(.{13})..+/, "$1&hellip;"));

					var check = parseInt(result.ng_rate[i].check) + parseInt(result.ng_rate[i].check2);
					var ng = parseInt(result.ng_rate[i].ng) + parseInt(result.ng_rate[i].ng2);
					var rates = (ng/check)*100;

					if(rates > 100){
						rate.push(100);						
					}else{
						rate.push(rates);						
					}
					if(rate[i] > parseInt(target)){
						data2.push({y: rate[i], color: 'rgb(255,116,116)'})
					} else{
						data2.push({y: rate[i], color: 'rgb(144,238,126)'});
					}
				}

				Highcharts.chart('container', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operator',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitle,
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
					yAxis: {
						title: {
							enabled: true,
							text: "NG Rate (%)"
						},
						min: 0,
						plotLines: [{
							color: '#FF0000',
							value: parseInt(target),
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target '+parseInt(target)+'%',
								x:-7,
								style: {
									fontSize: '12px',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}],
						labels: {
							enabled: false
						}
					},
					tooltip: {
						headerFormat: '<span>{series.name}</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.category} </span>: <b>{point.y}%</b><br/>',
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
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								rotation: -90,
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
									click: function (event) {
										showDetail(result.dateTitle, event.point.category);

									}
								}
							},
						}
					},
					series: [{
						type: 'column',
						data: data2,
						name: 'NG Rate',
						showInLegend: false
					}]
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	
	function showDetail(tgl, nama) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$('#myModal').modal('show');
		$('#welding-log-body').append().empty();
		$('#welding-ng-log-body').append().empty();
		$('#welding-cek-body').append().empty();
		$('#ng_rate').append().empty();
		$('#posh_rate').append().empty();
		$('#judul').append().empty();


		$.get('{{ url("fetch/welding/op_ng_detail") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#judul').append('<b>'+result.nik+' - '+result.nama+' on '+tgl+'</b>');

				//Welding log
				var total_good = 0;
				var body = '';
				for (var i = 0; i < result.good.length; i++) {
					body += '<tr>';
					body += '<td>'+result.good[i].welding_time+'</td>';
					body += '<td>'+result.good[i].model+'</td>';
					body += '<td>'+result.good[i].key+'</td>';
					body += '<td>'+result.good[i].op_kensa+'</td>';
					body += '<td>'+result.good[i].quantity+'</td>';
					body += '</tr>';

					total_good += parseInt(result.good[i].quantity);
				}
				body += '<tr>';
				body += '<td  colspan="4" style="text-align: center;">Total</td>';
				body += '<td>'+total_good+'</td>';
				body += '</tr>';
				$('#welding-log-body').append(body);


				//Welding NG log
				var total_ng = 0;
				var body = '';
				for (var i = 0; i < result.ng_ng.length; i++) {
					body += '<tr>';
					body += '<td>'+result.ng_ng[i].welding_time+'</td>';
					body += '<td>'+result.ng_ng[i].model+'</td>';
					body += '<td>'+result.ng_ng[i].key+'</td>';
					body += '<td>'+result.ng_ng[i].op_kensa+'</td>';
					body += '<td>'+result.ng_ng[i].ng_name+'</td>';
					body += '<td>'+result.ng_ng[i].quantity+'</td>';
					body += '</tr>';

					total_ng += parseInt(result.ng_ng[i].quantity);
				}
				body += '<tr>';
				body += '<td colspan="5" style="text-align: center;">Total</td>';
				body += '<td>'+total_ng+'</td>';
				body += '</tr>';
				$('#welding-ng-log-body').append(body);

				//Welding cek
				var total_cek = 0;
				var body = '';
				for (var i = 0; i < result.cek.length; i++) {
					body += '<tr>';
					body += '<td>'+result.cek[i].welding_time+'</td>';
					body += '<td>'+result.cek[i].model+'</td>';
					body += '<td>'+result.cek[i].key+'</td>';
					body += '<td>'+result.cek[i].op_kensa+'</td>';
					body += '<td>'+result.cek[i].quantity+'</td>';
					body += '</tr>';

					total_cek += parseInt(result.cek[i].quantity);
				}
				body += '<tr>';
				body += '<td colspan="4" style="text-align: center;">Total</td>';
				body += '<td>'+total_cek+'</td>';
				body += '</tr>';
				$('#welding-cek-body').append(body);


				//Resume
				var ng_rate = total_ng / total_cek * 100;
				var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Cek</sub> x 100%';
				text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_cek +'</sub> x 100%';
				text_ng_rate += '<br>= <b>'+ ng_rate.toFixed(2) +'%</b>';
				$('#ng_rate').append(text_ng_rate);


				//Chart NG
				var data = [];
				var ng_name = [];
				var qty = [];
				for (var i = 0; i < result.ng_qty.length; i++) {

					ng_name.push(result.ng_qty[i].ng_name);
					qty.push(result.ng_qty[i].qty);

					if(i == 0){
						data.push([ng_name[i], qty[i], true, false]);
					}else{
						data.push([ng_name[i], qty[i], false, false]);
					}

				}

				Highcharts.chart('modal_ng', {
					chart: {
						styledMode: true,
						backgroundColor: null,
						borderWidth: null,
						plotBackgroundColor: null,
						plotShadow: null,
						plotBorderWidth: null,
						plotBackgroundImage: null
					},
					title: {
						text: '',
						style: {
							display: 'none'
						}
					},
					exporting: {
						enabled: false 
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						pie: {
							animation: false,
							dataLabels: {
								useHTML: true,
								enabled: true,
								format: '<span style="color:#121212"><b>{point.name}</b>:</span><br><span style="color:#121212">total = {point.y} PC(s)</span>',
								style:{
									textOutline: true,
								}
							}
						}
					},
					credits: {
						enabled:false
					},
					series: [{
						type: 'pie',
						allowPointSelect: true,
						keys: ['name', 'y', 'selected', 'sliced'],
						data: data,
					}]
				});

			}

		});
	}


	

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


</script>
@endsection