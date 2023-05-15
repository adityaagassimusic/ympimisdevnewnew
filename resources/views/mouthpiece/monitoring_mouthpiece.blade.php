@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	.table > tbody > tr:hover {
/*		background-color: #7dfa8c !important;*/
}
table.table-bordered{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
	vertical-align: middle;
	height: 30px;
	padding:  2px 5px 2px 5px;
}
.content-wrapper{
	color: white;
	font-weight: bold;
/*		background-color: #313132 !important;*/
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

#table_11 > thead > tr > th {
	border: 1px solid black;
	color: black
}

#table_91 > thead > tr > th {
	border: 1px solid black;
	color: black
}

#tableResume > thead > tr > th {
	font-size: 11px;
	font-weight: bold;
	vertical-align: middle;
}

#tableResume2 > thead > tr > th {
	font-size: 11px;
	font-weight: bold;
	vertical-align: middle;
}


</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-left: 5px;padding-right: 5px; background-color: #fffce6; color: black;text-align: center">
			<span style="padding: 15px;font-weight: bold;font-size: 25px;">STOCK MOUTHPIECE STORAGE</span>
		</div>
		<div class="col-xs-12" style="padding-right: 5px; padding-left: 5px" align="center">
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 30px;"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px; padding-left: 5px" align="center">
			<div id="container" style="width: 100%;height: 350px;"></div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px; padding-left: 5px" align="center">
			<div id="divTable" style="width: 100%;"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px; padding-left: 5px" align="center">
			<div id="container1" style="width: 100%;height: 350px;"></div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px; padding-left: 5px" align="center">
			<div id="divTable1" style="width: 100%;"></div>
		</div>
	</div>
</section>

<div class="modal" tabindex="-1" role="dialog" id="modalDetailStock" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;background-color: orange;text-align: center; padding-bottom: 10px">
					<span style="padding: 15px;font-weight: bold;font-size: 25px;" id="judulResume"></span>
				</div>
				<center>
					<span style="padding: 15px;font-weight: bold;font-size: 25px; color: black; text-align: center;">Ideal Stock</span>
				</center>

				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 1px solid black; padding-top: 20px">
					<thead>
						<tr>
							<th colspan="3" style="width:15%; background-color: #f99f8a; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="material"></th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Aktual</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Target Bulan Depan</th>
						</tr>
					</thead>
					<tbody id="bodyResumeDetail">
					</tbody>
				</table><br>
				<p style="color: black;">
					<span class="text-red">*</span>Stock Mouthpiece = Actual Stock / Ideal Stock
				</p>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(function() {
			fillChart();
		}, 10000);
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '<?php echo e(url("images/image-screen.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '<?php echo e(url("images/image-stop.png")); ?>',
			sticky: false,
			time: '2000'
		});
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

	function ShowModal (gmc, category){
		$('#modalDetailStock').modal('show');
		$('#judulResume').html('PRODUCTION RESULT - '+category);

		var data = {
			gmc:gmc
		}
		$.get('<?php echo e(url("fetch/mouthpiece/stock/detail")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#material').html(''+result.resumes.gmc+'<br>'+result.resumes.desc+'');
				$('#bodyResumeDetail').html('');
				var tableData = '';

				var jml = 0;

				var count = 1;
				tableData += '<tr>';
				tableData += '<td style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">'+result.resumes.qty+' (Pcs)</td>';
				tableData += '<td style="width:15%; background-color: #f9e88a; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">'+result.resumes.ideal_stock_1+' (Pcs)</td>';
				tableData += '</tr>';

				$('#bodyResumeDetail').append(tableData);

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();
				var tableData = '';
				$('#tableBodyResume').html("");
				$('#tableBodyResume').empty();

				var count = 1;

				$.each(result.datas, function(key, value) {

					tableData += '<tr>';
					tableData += '<td style=" text-align: center">'+ value.ideal_stock_1 +' (Pcs)</td>';	
					tableData += '<td style=" text-align: center">'+ value.gmc +'</td>';	
					tableData += '<td style=" text-align: center">'+ value.gmc +'</td>';	
					tableData += '<td style=" text-align: center">'+ value.gmc +'</td>';	
					tableData += '<td style=" text-align: center">'+ value.gmc +'</td>';	
					tableData += '<td style=" text-align: center">'+ value.gmc +'</td>';	
					tableData += '</tr>';

					count += 1;

				});

				$('#tableBodyResume').append(tableData);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fillChart() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		$.get('{{ url("fetch/monitoring/mouthpiece") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var categori = [];
					var categori1 = [];
					var series = [];
					var stock = [];
					var series1 = [];
					var stock1 = [];
					var plan = [];
					var qty = [];
					var avail = [];
					var plan1 = [];
					var qty1 = [];
					var avail1 = [];

					$.each(result.stock_ideals, function(key, value){
						categori.push(value.desc);
						series.push({y:parseInt('2')});
						stock.push({y:parseFloat(value.jumlah), key: value.gmc});
						plan.push(value.ideal_stock_1);
						qty.push(value.qty);
						avail.push(value.jumlah);
					});

					var pp = String(categori).split(",");
					var pq = String(plan).split(",")
					var qq = String(qty).split(",");
					var aa = String(avail).split(",");

					$('#divTable').html("");
					var tableData1 = "";
					tableData1 += '<table class="table table-bordered table-stripped" style="width: 100%; text-align: center" >';
					tableData1 += '<thead style="background-color: #605ca8; color: #FFD700; text-align: center">';
					tableData1 += '<tr>';

					tableData1 += '<th style="width: 4%; text-align: center">Item</th>';

					for (var i = 0; i < pp.length; i++) {
						tableData1 += '<th style="width: 4%; text-align: center">'+pp[i]+'</th>';						
					}

					tableData1 += '</tr>';
					tableData1 += '</thead>';
					tableData1 += '<tbody style="background-color: #fffce6; color: black">';
					tableData1 += '<tr>';

					tableData1 += '<td style="width: 4%;">Plan</td>';

					for (var i = 0; i < pq.length; i++) {
						tableData1 += '<td style="width: 4%;">'+pq[i]+'</td>';
					}

					tableData1 += '</tr>';
					tableData1 += '<tr>';

					tableData1 += '<td style="width: 4%;">Stock MP</td>';

					for (var i = 0; i < qq.length; i++) {
						tableData1 += '<td style="width: 4%;">'+qq[i]+'</td>';
					}

					tableData1 += '</tr>';
					tableData1 += '<tr>';

					tableData1 += '<td style="width: 4%;">Avail (Month)</td>';

					for (var i = 0; i < aa.length; i++) {
						tableData1 += '<td style="width: 4%;">'+aa[i]+' (M)</td>';
					}

					tableData1 += '</tr>';
					tableData1 += '</tbody>';
					tableData1 += '</table>';
					$('#divTable').append(tableData1);

					$.each(result.stock_ideals1, function(key, value){
						categori1.push(value.desc);
						series1.push({y:parseInt('2')});
						stock1.push({y:parseFloat(value.jumlah), key: value.gmc});
						plan1.push(value.ideal_stock_1);
						qty1.push(value.qty);
						avail1.push(value.jumlah);
					});

					var pp1 = String(categori1).split(",");
					var pq1 = String(plan1).split(",")
					var qq1 = String(qty1).split(",");
					var aa1 = String(avail1).split(",");

					$('#divTable1').html("");
					var tableData2 = "";
					tableData2 += '<table class="table table-bordered table-stripped" style="width: 100%; text-align: center" >';
					tableData2 += '<thead style="background-color: #605ca8; color: #FFD700; text-align: center">';
					tableData2 += '<tr>';

					tableData2 += '<th style="width: 4%; text-align: center">Item</th>';

					for (var i = 0; i < pp1.length; i++) {
						tableData2 += '<th style="width: 4%; text-align: center">'+pp1[i]+'</th>';						
					}

					tableData2 += '</tr>';
					tableData2 += '</thead>';
					tableData2 += '<tbody style="background-color: #fffce6; color: black">';
					tableData2 += '<tr>';

					tableData2 += '<td style="width: 4%;">Plan</td>';

					for (var i = 0; i < pq1.length; i++) {
						tableData2 += '<td style="width: 4%;">'+pq1[i]+'</td>';
					}

					tableData2 += '</tr>';
					tableData2 += '<tr>';

					tableData2 += '<td style="width: 4%;">Stock MP</td>';

					for (var i = 0; i < qq1.length; i++) {
						tableData2 += '<td style="width: 4%;">'+qq1[i]+'</td>';
					}

					tableData2 += '</tr>';
					tableData2 += '<tr>';

					tableData2 += '<td style="width: 4%;">Avail (Month)</td>';

					for (var i = 0; i < aa1.length; i++) {
						tableData2 += '<td style="width: 4%;">'+aa1[i]+' (M)</td>';
					}

					tableData2 += '</tr>';
					tableData2 += '</tbody>';
					tableData2 += '</table>';
					$('#divTable1').append(tableData2);

					Highcharts.chart('container', {
						title: {
							text: null,
							align: 'center'
						},
						xAxis: {
							categories: categori
						},
						yAxis: {
							title: {
								text: 'ACTUAL STOCK (BULAN)'
							},
							tickPixelInterval: 40
						},
						tooltip: {
							valueSuffix: ' Bulan'
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								depth:25
							},
						},
						credits:{
							enabled:false
						},
						series: [
						{
							type: 'column',
							name: 'Actual Stock (Bulan)',
							data: stock,
							color:'#f57f17',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.options.key, this.category);
									}
								}
							},
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: false,
							cursor: 'pointer',
							depth:25
						}, {
							type: 'line',
							name: 'Ideal Stock (Bulan)',
							data: series,
							color: '#179cf5',
							lineWidth: 6
						}]
					});

					Highcharts.chart('container1', {
						title: {
							text: null,
							align: 'center'
						},
						xAxis: {
							categories: categori1
						},
						yAxis: {
							title: {
								text: 'ACTUAL STOCK (BULAN)'
							},
							tickPixelInterval: 40
						},
						tooltip: {
							valueSuffix: ' Bulan'
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								depth:25
							},
						},
						credits:{
							enabled:false
						},
						series: [
						{
							type: 'column',
							name: 'Actual Stock (Bulan)',
							data: stock1,
							color:'#f57f17',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.options.key, this.category);
									}
								}
							},
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: false,
							cursor: 'pointer',
							depth:25
						}, {
							type: 'line',
							name: 'Ideal Stock (Bulan)',
							data: series1,
							color: '#179cf5',
							lineWidth: 6
						}]
					});
				}
			}
		});
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
		// itemStyle: {
		// 	color: '#E0E0E3'
		// },
		// itemHoverStyle: {
		// 	color: '#FFF'
		// },
		// itemHiddenStyle: {
		// 	color: '#606063'
		// }
		enabled:false
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
@endsection