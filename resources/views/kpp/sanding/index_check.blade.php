
@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
		overflow:hidden;
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:2px;
	}

	table.table-bordered > tbody > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	input[type=radio]{
		width: 18px;
		height: 18px;
	}

	table {
		margin-bottom: 10px !important;
	}

	.radio_text {
		font-weight: bold;
		font-size: 18px;
	}

	hr {
		margin: 3px;
		border-color: black;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<!-- <h1>
		<div class="col-xs-12 col-md-9 col-lg-9">
			<h3 style="margin-top: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3>
		</div>
	</h1> -->
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="margin-bottom: 3px">
			<center>
				<table style="margin-bottom: 2px; border: 1px solid black">
					<tr>
						<td style="padding: 2px 10px 2px 5px; background-color: #2e51ff; color: white; font-weight: bold">OPERATOR : </td>
						<td id="op_nik" style="padding: 2px 2px 2px 10px">&nbsp;</td>
						<td id="op_name" style="padding: 2px 10px 2px 2px">Nama OP</td>
						<td style="padding: 2px 10px 2px 5px; background-color: #2e51ff; color: white; font-weight: bold">MATERIAL NUMBER : </td>
						<td><input type="text" id="material_number" placeholder="TAP KANBAN DISINI" style="text-align: center;"></td>
					</tr>
				</table>
				<label id="judul" style="font-size: 18px">CEK VISUAL MATERIAL <span id="gmc"></span></label>
			</center>
			<span style="color: red; background-color: yellow; font-weight: bold; font-size: 14px">&nbsp; 1. Cek aktual kualitas material awal dan bandingkan dengan standart kualitas di bawah : &nbsp;</span>
		</div>
		<div class="col-xs-3" style="padding-right: 0px">
			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff; color: white">FOTO MATERIAL KESELURUHAN</th>
				</tr>
				<tr>
					<td id="material_photo"></td>
				</tr>
			</table>
			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff; color: white">LEVEL</th>
					<th style="background-color: #2e51ff; color: white">PENJELASAN KUALITAS</th>
				</tr>
				<tr>
					<td style="text-align: center;">
						&#10102;
					</td>
					<td style="text-align: left">
						Bentuk material sempurna (Tidak ada perubahan bentuk)
					</td>
				</tr>
				<tr>
					<td style="text-align: center;">
						&#10103;
					</td>
					<td style="text-align: left">
						Bentuk material cukup baik (Sedikit berubah bentuk, tidak menambah waktu proses)
					</td>
				</tr>
				<tr>
					<td style="text-align: center;">
						&#10104;
					</td>
					<td style="text-align: left">
						Bentuk material cukup jelek (Sedikit ada perubahan bentuk, sedikit menambah waktu proses)
					</td>
				</tr>
				<tr>
					<td style="text-align: center;">
						&#10105;
					</td>
					<td style="text-align: left">
						Bentuk material jelek (Ada perubahan bentuk, menambah waktu proses)
					</td>
				</tr>
				<tr>
					<td style="text-align: center;">
						&#10106;
					</td>
					<td style="text-align: left">
						Bentuk material sangat jelek	(Ada perubahan bentuk, menambah	waktu proses)
					</td>
				</tr>
			</table>
		</div>

		<div class="col-xs-9">
			<table class="table table-bordered" style="width: 100%">
				<tr>
					<th style="background-color: #2e51ff; color: white" id="head_item">DETAIL POIN CEK MATERIAL AWAL</th>
				</tr>
				<tr id="text_item">
				</tr>
				<tr id="photo_item"></tr>
				<tr id="pointer_item"></tr>
			</table>
			<button class="btn btn-success" style="width: 100%; font-weight: bold; margin-bottom: 5px; display: none" id="save" onclick="save_data()"><i class="fa fa-check"></i> REPORT KUALITAS</button>
			<!-- <button class="btn btn-primary" style="width: 100%; font-weight: bold; margin-bottom: 5px;" onclick="save_data()">GRAFIK</button> -->
			<div id="chart_container">
				<div class="col-xs-6">
					<div id="chart" style="width: 99%;"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">ID OPERATOR</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="op_tag" placeholder="Scan ID Card" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalLanjut">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label>LANJUT KE LANGKAH SELANJUTNYA</label>
							
							<a href="#" class="btn btn-success btn-lg" id="btn_next" style="width: 100%"><i class="fa fa-check"></i> OK</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var len = [];
	var op_arr = <?php echo json_encode($op); ?>;
	var qty = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#material_number').val('');

		// document.getElementById("material_number").focus();
	});	

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#op_tag').val('');
		$('#op_tag').focus();
	});

	$('#material_number').keypress(function (e) {
		if (e.which == 13) {
			fillData();
		}
	});

	$('#op_tag').keypress(function (e) {
		if (e.which == 13) {
			filterOp();
			
		}
	});

	function filterOp() {
		var stat = 0;
		$.each(op_arr, function(key, value) {
			if($("#op_tag").val() == value.tag || $("#op_tag").val().toUpperCase() == value.employee_id) {
				$("#op_nik").text(value.employee_id);
				$("#op_name").text(value.name);
				$("#modalOperator").modal('hide');
				$('#material_number').focus();

				stat = 1;
			}
		})

		if (stat == 0) {
			openErrorGritter('Error', 'Operator Tidak Terdaftar');
		}
	}

	function fillData(){
		$("#loading").show();
		$("#material_photo").empty();
		$("#gmc").empty();

		$("#chart_container").empty();
		$("#text_item").empty();

		var data = {
			material_number : $("#material_number").val(),
			category : 'Detail Point Cek Material Awal'
		}
		$.get('{{ url("fetch/material_check/sanding") }}', data, function(result, status, xhr){
			$("#loading").hide();
			if(result.status){
				$("#save").show();
				qty = result.qty;
				$("#material_number").val(result.datas[0].material_number);
				$("#material_number").attr('readonly', true);
				$("#material_number").css('background-color', '#d1d1d1');
				var bodypoin = "";
				var bodyphoto = "";
				var bodycek = "";


				$("#gmc").text(result.datas[0].material_description);

				$("#material_photo").append("<img src=\"{{ url('files/sanding/visual_check') }}/MA_"+result.datas[0].material_number+".jpg\" style='margin: 3px 0px 3px 0px; max-width: 220px'></br>");
				
				$("#pointer_item").empty();

				$("#head_item").attr('colspan', result.datas.length);

				len = result.datas;

				var series1 = [];
				var series2 = [];
				var series3 = [];
				var category = [];

				$.each(result.grafik, function(key, value) {
					if (value.check_point == '1') {
						series1.push([parseInt(value.hari), value.jml]);
					} else if (value.check_point == '2') {
						series2.push([parseInt(value.hari), value.jml]);
					} else if (value.check_point == '3') {
						series3.push([parseInt(value.hari), value.jml]);
					}
				})

				$.each(result.datas, function(key, value) {
					bodypoin += "<td style='width:1%; vertical-align: top'>";
					bodypoin += value.point+". "+value.description+"<br>"
					bodypoin += "<img src=\"{{ url('files/sanding/visual_check') }}/MA_"+value.material_number+"_P"+value.point+".jpg\" style='margin: 3px 0px 3px 0px; max-width: 220px'></td>";
					bodycek += '<td><label class="btn btn-default radio_text" style="background-color: rgb(44, 232, 47)"><input type="radio" name="check_'+value.point+'" value="1">&nbsp;1</label>&nbsp;';
					bodycek += '<label class="btn btn-default radio_text" style="background-color: rgb(95, 87, 255)"><input type="radio" name="check_'+value.point+'" value="2">&nbsp;2</label>&nbsp;<br>';
					bodycek += '<label class="btn btn-default radio_text" style="background-color: rgb(255, 241, 87)"><input type="radio" name="check_'+value.point+'" value="3">&nbsp;3</label>&nbsp;';
					bodycek += '<label class="btn btn-default radio_text" style="background-color: rgb(255, 157, 87)"><input type="radio" name="check_'+value.point+'" value="4">&nbsp;4</label>&nbsp;';
					bodycek += '<label class="btn btn-default radio_text" style="background-color: rgb(242, 67, 61)"><input type="radio" name="check_'+value.point+'" value="5">&nbsp;5</label>&nbsp;</td>';

					$("#chart_container").append('<div class="col-xs-'+(12/result.datas.length)+'" style="padding: 0px 5px 0px 5px;"><div id="chart_'+value.point+'" style="width: 100%; height: 38vh"></div></div>');

					// ------------    GRAFIK ---------
					var series_1 = [];
					var series_2 = [];
					var series_3 = [];
					var series_4 = [];
					var series_5 = [];

					
					$.each(result.grafik, function(key2, value2) {
						if (value2.point == value.point) {
							if (value2.cp == '1') {
								series_1.push([parseInt(value2.hari), parseInt(value2.cp)]);
							} else if (value2.cp == '2') {
								series_2.push([parseInt(value2.hari), parseInt(value2.cp)]);
							} else if (value2.cp == '3') {
								series_3.push([parseInt(value2.hari), parseInt(value2.cp)]);
							} else if (value2.cp == '4') {
								series_4.push([parseInt(value2.hari), parseInt(value2.cp)]);
							} else if (value2.cp == '5') {
								series_5.push([parseInt(value2.hari), parseInt(value2.cp)]);
							}
						} 
					})

					Highcharts.chart('chart_'+value.point, {
						chart: {
							type: 'scatter',
							zoomType: 'xy'
						},
						title: {
							text: 'POIN CEK '+value.point,
							style: {
								fontSize: '12px',
							}
						},
						xAxis: {
							title: {
								text: 'Tanggal'
							},
							startOnTick: true,
							endOnTick: true,
							showLastLabel: true,
							tickInterval: 1,
							gridLineWidth: 1,
							gridLineColor: '#707073',
							lineWidth:1,
							lineColor:'#707073'
						},
						yAxis: {
							title: {
								text: 'Level Check'
							},
							tickPositions: [1,2,3,4,5]
						},
						legend: {
							itemStyle:{
								color: "white",
								fontSize: "8px",
								fontWeight: "bold",
							}
						},
						plotOptions: {
							scatter: {
								marker: {
									radius: 5,
									states: {
										hover: {
											enabled: true,
											lineColor: 'rgb(100,100,100)'
										}
									}
								},
								states: {
									hover: {
										marker: {
											enabled: false
										}
									}
								},
								tooltip: {
									headerFormat: '<b>{series.name}</b><br>',
									pointFormat: 'Tgl {point.x} : {point.y} '
								}
							}
						},
						credits: {
							enabled : false
						},
						series: [{
							name: 'Lv 5',
							color: 'rgba(242, 67, 61, .8)',
							data: series_5,
							marker: {
								symbol: 'circle'
							}

						}, {
							name: 'Lv 4',
							color: 'rgba(255, 157, 87, .8)',
							data: series_4,
							marker: {
								symbol: 'circle'
							}
						},
						{
							name: 'Lv 3',
							color: 'rgba(255, 241, 87, .8)',
							data: series_3,
							marker: {
								symbol: 'circle'
							}
						},
						{
							name: 'Lv 2',
							color: 'rgba(95, 87, 255, .8)',
							data: series_2,
							marker: {
								symbol: 'circle'
							}
						},
						{
							name: 'Lv 1',
							color: 'rgba(44, 232, 47, .8)',
							data: series_1,
							marker: {
								symbol: 'circle'
							}
						}]
					});

				})

$("#text_item").append(bodypoin);
$("#pointer_item").append(bodycek);

}
else{
	openErrorGritter('Error!', result.message);
}
});
}

function save_data() {
	var stat = 1;
	$.each(len, function(key, value) {
		if ( ! $("input[name='check_"+value.point+"']").is(':checked') ) {
			
			stat = 0;
			return false;
		}		
	})

	if (stat == 0) {
		openErrorGritter('Error', 'Semua Poin Cek Harus Diisi');
		return false;
	}

	if (confirm('Anda Yakin untuk Menyimpan Data?')) {
		$("#loading").show();

		var hasil_cek = [];

		$.each(len, function(key, value) {
			hasil_cek.push({'point' : value.point, 'description' : value.description, 'value' : $("input[name='check_"+value.point+"']:checked").val()})
		})

		var data = {
			material_number : $("#material_number").val(),
			material_desc : $("#gmc").text(),
			op : $("#op_nik").text()+'/'+$("#op_name").text(),
			check : hasil_cek,
			qty : qty
		}

		$.post('{{ url("input/material_check/sanding") }}', data, function(result, status, xhr){
			if (result.status) {
				qty = 0;
				$("#loading").hide();
				openSuccessGritter('sukses', 'Data Berhasil tersimpan');
				$("#btn_next").attr('href', '{{ url("index/material_check/finish/sanding") }}/'+result.form_number);

				$('#modalLanjut').modal({
					backdrop: 'static',
				});
			}
		})
	}
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});

	audio_ok.play();
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

	audio_error.play();
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

</script>
@endsection