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
	.gambar {
	    width: 100%;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 0px;
	    margin-top: 10px;
	    display: inline-block;
	    border: 2px solid white;
	  }

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ff8080;
		}
		50%, 100% {
			background-color: #ffe8e8;
		}
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
			<div class="row">
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
					</div>
				</div>
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
					</div>
				</div>
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="input-group" style="width: 100%"> 
						<select class="form-control select2"  id="select_area" data-placeholder="Select Area">
							<option value=""></option>
							@foreach($area as $area)
							<option value="{{$area->area_code}}">{{$area->area}}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<div id="container1" class="container1" style="width: 100%;"></div>
						</div>
						<div class="col-xs-12">
							<div class="box box-solid">
								<div class="box-body">
									<table style="width: 100%;">
							          <tr>
							            <td width="1%">
							              <div class="description-block border-right" style="color: #2fe134">
							                <h5 class="description-header" style="font-size: 48px;">
							                  <span class="description-percentage" id="tot_plan"></span>
							                </h5>      
							                <span class="description-text" style="font-size: 32px;">Total Plan</span><br><span class="description-text" style="font-size: 32px;">単月見込み</span>
							              </div>
							            </td>
							            <td width="1%">
							              <div class="description-block border-right" style="color: #7300ab" >
							                <h5 class="description-header" style="font-size: 48px; ">
							                  <span class="description-percentage" id="tot_act"></span>
							                </h5>      
							                <span class="description-text" style="font-size: 32px;">Total Actual</span>
							                <br><span class="description-text" style="font-size: 32px;">単月実績</span>
							              </div>
							            </td>
							            <td width="1%">
							              <div class="description-block border-right text-green" id="diff_text">
							                <h5 class="description-header" style="font-size: 48px;">
							                  <span class="description-percentage" id="tot_diff"></span>
							                </h5>      
							                <span class="description-text" style="font-size: 32px;">Diff(Act-Plan)</span>
							                <br><span class="description-text" style="font-size: 32px;">差異</span>
							              </div>
							            </td>
							            <td width="1%">
							              <div class="description-block border-right text-yellow">
							                <h5 class="description-header" style="font-size: 48px;">
							                  <span class="description-percentage" id="percent"></span>
							                </h5>      
							                <span class="description-text" style="font-size: 32px;">Percent<br><span>パーセント</span></span>
							              </div>
							            </td>
							       	  </tr>
							      </table>
								</div>
							</div>
						</div>
					</div>
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
				<div class="modal-body table-responsive no-padding" style="min-height: 100px;margin-top: 10px">
					<!-- <center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center> -->
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Product</th>
								<th style="width: 4%;">Material</th>
								<th style="width: 2%;">Cav</th>
								<th style="width: 4%;">Employee</th>
								<th style="width: 2%;">NG Name</th>
								<th style="width: 2%;">Qty</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot>
				            <tr style="background-color:rgba(126,86,134,.7);font-size:15px;font-weight:bold">
								<th colspan="6" style="border-top:1px solid black;border-bottom:1px solid black">TOTAL</th>
								<th style="border-top:1px solid black;border-bottom:1px solid black" id="total_ng"></th>
							</tr>
				        </tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCounceling">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #03adfc;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
					<table class="table table-hover table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">ID</th>
								<th style="width: 2%;">Name</th>
								<th style="width: 2%;">NG Qty</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td id="employee_id"></td>
								<td id="name"></td>
								<td id="ng_qty"></td>
							</tr>
						</tbody>
					</table>

					<div class="form-group">
					  <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px">
		              	<label for="">Trainee Employee</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px">
					  	<input type="text" name="tag_employee" id="tag_employee" class="form-control" placeholder="Scan ID Card Employee">
					  </div>
					  <div class="col-xs-2" style="padding-right: 0px">
					  	<button class="btn btn-danger" onclick="cancelScan('tag_employee')">Cancel</button>
					  </div>
					  <input type="hidden" name="firstDate" id="firstDate" class="form-control" placeholder="">
					  <input type="hidden" name="lastDate" id="lastDate" class="form-control" placeholder="">
		            </div>

		            <div class="form-group">
		              <div class="col-xs-12" style="padding-left: 0px">
		              	<label for="">Trained By</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px">
					  	<input type="text" name="tag_leader" id="tag_leader" class="form-control" placeholder="Scan ID Card Sub Leader / Leader">
					  </div>
					  <div class="col-xs-2" style="padding-right: 0px">
					  	<button class="btn btn-danger" onclick="cancelScan('tag_leader')">Cancel</button>
					  </div>
		            </div>

		            <div class="form-group">
		              <div class="col-xs-12" style="padding-left: 0px;">
		              	<label for="">Document Training</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px;">
					  	<!-- <input type="file" name="counceled_image" id="counceled_image" class="form-control" placeholder="Scan ID Card Sub Leader / Leader"> -->
					  	<a href="{{url('input/injection/training_document')}}" target="_blank" class="btn btn-primary">Input Document Training</a>
					  </div>
		            </div>
				</div>
				<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
					<button class="btn btn-success" onclick="submitCouncel()">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
		$('#modalDetail').on('hidden.bs.modal', function () {
			$('#tableDetail').DataTable().clear();
		});
		// setInterval(fetchChart, 20000);
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

	var detail_all_injeksi = [];
	var detail_all_assy = [];

	function fetchChart(){
		var area_code = "{{$_GET['area']}}";
		if ($('#select_area').val() == '') {
			area_code = "{{$_GET['area']}}";
		}else{
			area_code = $('#select_area').val();
		}

		var data = {
			area_code:area_code,
		}

		$.get('{{ url("fetch/maintenance/display/jishu_hozen") }}', data, function(result, status, xhr) {
			if(result.status){
				var categories = [];
				var plan = [];
				var actual = [];
				var all_plan = 0;
				var all_actual = 0;
				for(var i = 0; i < result.jishu_hozen.length;i++){
					categories.push(result.jishu_hozen[i].week_date);
					var plans = [];
					var actuals = [];
					var total_plan = 0;
					var total_actual = 0;
					if (result.jishu_hozen[i].plan != null) {
						plans = result.jishu_hozen[i].plan.split(',');
					}
					total_plan = plans.length;
					if (result.jishu_hozen[i].actual != null) {
						actuals = result.jishu_hozen[i].actual.split(',');
						for(var j = 0; j < actuals.length;j++){
							if (plans.includes(actuals[j])) {
								total_actual++;
								all_actual++;
							}
						}
					}
					plan.push(total_plan-total_actual);
					all_plan++;
					actual.push(total_actual);
				}

				$('#tot_plan').html(all_plan);
				$('#tot_act').html(all_actual);
				$('#tot_diff').html(all_actual-all_plan);
				$('#percent').html(((all_actual/all_plan)*100).toFixed(1));
				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						height: '450',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: result.area_name+" JISHU HOZEN MONITORING",
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'QTY JISHU HOZEN',
							style: {
								color: '#eee',
								fontSize: '15px',
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
						// max: 400
					}
					],
					tooltip: {
						headerFormat: '<span>TOTAL JISHU HOZEN</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -90,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
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
										ShowModal(this.category,'injeksi');
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
						zoneAxis: 'x',
						type: 'column',
						data: actual,
						name: "Total Actual",
						colorByPoint: false,
						color: "#23c44e",
						animation: false,
						stacking:true,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: plan,
						name: "Total Plan",
						colorByPoint: false,
						color: "#9e1313",
						animation: false,
						stacking:true,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					
					]
				})
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

function ShowModal(operator_injection,type) {
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var bodyDetail = '';
	var total_ng = 0;
	if (type === 'injeksi') {
		var index = 1;
		$('#modalDetailTitle').html('Detail NG From Injection');
		for (var i = 0; i < detail_all_injeksi.length; i++) {
			if (detail_all_injeksi[i].name_injection === operator_injection) {
				bodyDetail += '<tr>';
				bodyDetail += '<td>'+index+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].product+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].material_number+'<br>'+detail_all_injeksi[i].part_name+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].cavity+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].operator_injection+'<br>'+detail_all_injeksi[i].name_injection+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].ng_name+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].ng_count+'</td>';
				total_ng = total_ng + parseInt(detail_all_injeksi[i].ng_count);
				bodyDetail += '</tr>';
				index++;
			}
		}
	}
	if (type === 'assy') {
		var index = 1;
		$('#modalDetailTitle').html('Detail NG From Assy');
		for (var i = 0; i < detail_all_assy.length; i++) {
			if (detail_all_assy[i].name_injection === operator_injection) {
				bodyDetail += '<tr>';
				bodyDetail += '<td>'+index+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].product+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].material_number+'<br>'+detail_all_assy[i].part_name+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].cavity+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].operator_injection+'<br>'+detail_all_assy[i].name_injection+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].ng_name+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].ng_count+'</td>';
				total_ng = total_ng + parseInt(detail_all_assy[i].ng_count);
				bodyDetail += '</tr>';
				index++;
			}
		}
	}
	
	$('#tableDetailBody').append(bodyDetail);

	$('#total_ng').html(total_ng);

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

	$('#modalDetail').modal('show');
}


function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

	function perbandingan(a,b){
		return a-b;
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
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

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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


</script>
@endsection