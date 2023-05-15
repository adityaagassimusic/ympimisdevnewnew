@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

table.table-bordered{
	border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
	border:1px solid rgb(54, 59, 56) !important;
	text-align: center;
	background-color: #212121;  
	color:white;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(54, 59, 56);
	background-color: #212121;
	color: white;
	vertical-align: middle;
	text-align: center;
	padding:3px;
}
table.table-condensed > thead > tr > th{   
	color: black;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(150,150,150);
	padding:0;
}
table.table-bordered > tbody > tr > td > p{
	color: #abfbff;
}

table.table-striped > thead > tr > th{
	border:1px solid black !important;
	text-align: center;
	background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
	border: 1px solid #eeeeee !important;
	border-collapse: collapse;
	color: black;
	padding: 3px;
	vertical-align: middle;
	text-align: center;
	background-color: white;
}

thead input {
	width: 100%;
	padding: 3px;
	box-sizing: border-box;
}
thead>tr>th{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
}
td:hover {
	overflow: visible;
}
table > thead > tr > th{
	border:2px solid #f4f4f4;
	color: white;
}
.content-wrapper{
	color: white;
	font-weight: bold;
	background-color: #313132 !important;
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

.dataTables_info,
.dataTables_length {
	color: white;
}

div.dataTables_filter label, 
div.dataTables_wrapper div.dataTables_info {
	color: white;
}

div#tableDetail_info.dataTables_info,
div#tableDetail_filter.dataTables_filter label,
div#tableDetail_wrapper.dataTables_wrapper{
	color: black;
}

#tableDetail_info.dataTables_info,
#tableDetail_info.dataTables_length {
	color: black;
}

div#tableDetailPie_info.dataTables_info,
div#tableDetailPie_filter.dataTables_filter label,
div#tableDetailPie_wrapper.dataTables_wrapper{
	color: black;
}

#tableDetailPie_info.dataTables_info,
#tableDetailPie_info.dataTables_length {
	color: black;
}

div#tableDetailCategory_info.dataTables_info,
div#tableDetailCategory_filter.dataTables_filter label,
div#tableDetailCategory_wrapper.dataTables_wrapper{
	color: black;
}

#tableDetailCategory_info.dataTables_info,
#tableDetailCategory_info.dataTables_length {
	color: black;
}


</style>
@endsection
@section('header')
@endsection
@section('content')
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	<p style="position: absolute; color: White; top: 45%; left: 27%;">
		<span style="font-size: 40px">Loading, please wait a moment . . . <i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #00af50; height: 68vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah Vaksin')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 23vh;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>SUDAH VAKSIN 1</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.7vw;color: #0d47a1;"><b>ワクチン1回目</b></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_sudah_vaksin">0</h5>
							<h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_sudah_vaksin">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 30vh;font-size:10vh">
							<i class="fa fa-check" ></i>
						</div>
					</div>

					<div class="small-box" style="background: #fe0000; height: 25vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum Vaksin All')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: white;"><b>Belum Vaksin</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: #0d47a1;"><b>ワクチン未接種</b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_belum_vaksin">0</h5>
							<h4 style="font-size: 2vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white;" id="persen_belum_vaksin">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 10vh;font-size:10vh;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div class="small-box" style="background: #536dfe; height: 100px; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Sudah Vaksin Pertama')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>Hanya Vaksin 1</b></h3>
							<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_sudah_vaksin_pertama">0</h5>
							<h4 style="font-size: 1.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_sudah_vaksin_pertama">0 %</h4>
						</div>
						<div class="icon" style="padding-top: 10px;">
							<i class="fa fa-check"></i>
						</div>
					</div> -->
					<div class="small-box" style="background: #00ff73; height: 13vh; margin-bottom: 5px;cursor: pointer;color:black" onclick="ShowModalAll('Sudah Vaksin Kedua')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>VAKSIN 2</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"><b>ワクチン2回目</b></h3>
							<span style="font-size: 1.8vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="total_sudah_vaksin_kedua">0</span> <span style="font-size: 1.8vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="persen_sudah_vaksin_kedua">0 %</span>
						</div>
						<div class="icon" style="padding-top: 0;font-size:8vh;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #5bffa6; height: 12vh; margin-bottom: 5px;cursor: pointer;color:black" onclick="ShowModalAll('Belum Vaksin Kedua')">
						<div class="inner" style="padding-bottom: 0px;;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>BELUM VAKSIN 2</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"><b>2回目未接種</b></h3>
							<span style="font-size: 1.8vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="total_belum_vaksin_kedua">0</span> <span style="font-size: 1.8vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="persen_belum_vaksin_kedua">0 %</span>
						</div>
						<div class="icon" style="padding-top: 0;font-size:8vh;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #42a5f5; height: 13vh; margin-bottom: 5px;cursor: pointer;color:black" onclick="ShowModalAll('Sudah Vaksin Ketiga')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>VAKSIN 3</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"><b>ワクチン3回目</b></h3>
							<span style="font-size: 1.8vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="total_sudah_vaksin_ketiga">0</span> <span style="font-size: 1.8vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="persen_sudah_vaksin_ketiga">0 %</span>
						</div>
						<div class="icon" style="padding-top: 0;font-size:8vh;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #90caf9; height: 12vh; margin-bottom: 5px;cursor: pointer;color:black" onclick="ShowModalAll('Belum Vaksin Ketiga')">
						<div class="inner" style="padding-bottom: 0px;;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1.4vw;"><b>BELUM VAKSIN 3</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.1vw;color: #0d47a1;"><b>3回目未接種</b></h3>
							<span style="font-size: 1.8vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="total_belum_vaksin_ketiga">0</span> <span style="font-size: 1.8vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="persen_belum_vaksin_ketiga">0 %</span>
						</div>
						<div class="icon" style="padding-top: 0;font-size:8vh;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #ffff01; height: 15vh; margin-bottom: 5px;cursor: pointer;color:black" onclick="ShowModalAll('Sudah Daftar')">
						<div class="inner" style="padding-bottom: 0px;;padding-top: 5px;">
							<h3 style="margin-bottom: 0px;font-size: 1vw;"><b>SUDAH DAFTAR VAKSIN 3</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.2vw;color: #0d47a1;"><b>登録ずみ</b></h3>
							<span style="font-size: 2.2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="total_sudah_daftar">0</span> 
							<!-- <span style="font-size: 2vw; font-weight: bold;padding-top: 0px;margin-top: 0px;float: right;" id="persen_sudah_daftar">0 %</span> -->
						</div>
						<div class="icon" style="padding-top: 15px;font-size:8vh;">
							<i class="fa fa-check"></i>
						</div>
					</div>

					<div class="small-box" style="background: #795548; height: 25vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('Belum')">
						<div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>BELUM DAFTAR</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.4vw;color: #0d47a1;"><b>登録しない</b></h3>
							<span style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="total_belum">0</span> <br><span style="font-size: 1.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum">0 %</span>
						</div>
						<div class="icon" style="padding-top: 15px;font-size:8vh;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					
				</div>
				<div class="col-xs-8">
					<div id="container1" class="container1" style="width: 100%; height: 95vh;"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div style="margin-left: 20px;">*) Jumlah Manpower tidak termasuk Japanese Expatriat</div> 
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetailAll">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitleAll"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetailAll" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetailAll">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 6%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
							</tr>
						</thead>
						<tbody id="tableDetailBodyAll">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 6%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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
	var intervalChart;

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		intervalChart = setInterval(fillChart, 60000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	

	function fillChart() {
		$("#loading").show();

		// var tanggal = $('#tanggal').val();
		
		// var data = {
		// 	tanggal:tanggal
		// }

		// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		$.get('{{ url("fetch/vaksin/monitoring") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){


					$("#loading").hide();
					//Chart Machine Report
					var dept = [];

					var sudah_vaksin = 0;
					var sudah_daftar = 0;
					var belum = 0;


					var jml_sudah_vaksin_all = [];
					var jml_sudah_vaksin_pertama = [];
					var jml_sudah_vaksin_kedua = [];
					var jml_sudah_vaksin_ketiga = [];
					var jml_sudah_daftar = [];
					var jml_belum = [];
					var jml_belum_vaksin = [];

					var series = [];
					var series2 = [];
					var series3 = [];

					var total_sudah_vaksin_all = 0;
					var total_sudah_vaksin_pertama = 0;
					var total_sudah_vaksin_kedua = 0;
					var total_belum_vaksin_kedua = 0;
					var total_sudah_vaksin_ketiga = 0;
					var total_belum_vaksin_ketiga = 0;
					var total_sudah_daftar = 0;

					var total_belum = 0;
					var total_belum_all = 0;
					var total_all = 0;


					for (var i = 0; i < result.vaksin.length; i++) {
						dept.push(result.vaksin[i].department_shortname);

						// sudah_vaksin = sudah_vaksin+parseInt(result.vaksin[i].sudah_vaksin);
						// sudah_daftar = sudah_daftar+parseInt(result.vaksin[i].sudah_daftar);
						// belum = belum+parseInt(result.vaksin[i].belum);

						jml_sudah_vaksin_all.push(parseInt(result.vaksin[i].sudah_vaksin_pertama) + parseInt(result.vaksin[i].sudah_vaksin_kedua));
						jml_sudah_vaksin_pertama.push(parseInt(result.vaksin[i].sudah_vaksin_pertama));
						jml_sudah_vaksin_kedua.push(parseInt(result.vaksin[i].sudah_vaksin_kedua));
						jml_sudah_vaksin_ketiga.push(parseInt(result.vaksin[i].sudah_vaksin_ketiga));
						jml_sudah_daftar.push(parseInt(result.vaksin[i].sudah_daftar));
						jml_belum.push(parseInt(result.vaksin[i].belum));
						jml_belum_vaksin.push(parseInt(result.vaksin[i].belum));
						 // + parseInt(result.vaksin[i].sudah_daftar)

						total_sudah_vaksin_all += parseInt(result.vaksin[i].sudah_vaksin_pertama);
						total_sudah_vaksin_all += parseInt(result.vaksin[i].sudah_vaksin_kedua);
						// total_sudah_vaksin_all += parseInt(result.vaksin[i].sudah_vaksin_ketiga);
						total_sudah_vaksin_pertama += parseInt(result.vaksin[i].sudah_vaksin_pertama);
						total_sudah_vaksin_kedua += parseInt(result.vaksin[i].sudah_vaksin_kedua);
						total_sudah_vaksin_ketiga += parseInt(result.vaksin[i].sudah_vaksin_ketiga);
						total_sudah_daftar += parseInt(result.vaksin[i].sudah_daftar);
						total_belum += parseInt(result.vaksin[i].belum);
						total_belum_all += parseInt(result.vaksin[i].belum);
						// total_belum_all += parseInt(result.vaksin[i].sudah_daftar);

						series.push([dept[i], jml_sudah_vaksin_all[i]]);
						series2.push([dept[i], jml_sudah_daftar[i]]);
						series3.push([dept[i], jml_belum[i]]);

					}

					total_all = total_sudah_vaksin_pertama+total_sudah_vaksin_kedua+total_belum; //+total_sudah_daftar
					total_belum_vaksin_kedua = total_sudah_vaksin_all - total_sudah_vaksin_kedua;
					total_belum_vaksin_ketiga = total_sudah_vaksin_all - total_sudah_vaksin_ketiga;

					$('#total_sudah_vaksin').html(total_sudah_vaksin_all+" <span style='font-size:2.4vw'> 人</span>");
					$('#total_sudah_vaksin_pertama').html(total_sudah_vaksin_pertama+"<span style='font-size:1.8vw'> 人</span>");
					$('#total_sudah_vaksin_kedua').html(total_sudah_vaksin_kedua+"<span style='font-size:1.8vw'> 人</span>");
					$('#total_belum_vaksin_kedua').html(total_belum_vaksin_kedua+"<span style='font-size:1.8vw'> 人</span>");
					$('#total_sudah_vaksin_ketiga').html(total_sudah_vaksin_ketiga+"<span style='font-size:1.8vw'> 人</span>");
					$('#total_belum_vaksin_ketiga').html(total_belum_vaksin_ketiga+"<span style='font-size:1.8vw'> 人</span>");
					$('#total_sudah_daftar').html(total_sudah_daftar+"<span style='font-size:1.8vw'> 人</span>");
					$('#total_belum').html(total_belum +"<span style='font-size:1.8vw'> 人</span>");
					$('#total_belum_vaksin').html(total_belum_all +"<span style='font-size:2.4vw'> 人</span>");

					// $('#persen_sudah_vaksin').text((((total_sudah_vaksin_pertama+total_sudah_vaksin_kedua+total_sudah_vaksin_ketiga)/total_all)*100).toFixed(1)+'%');
					$('#persen_sudah_vaksin').text((((total_sudah_vaksin_pertama+total_sudah_vaksin_kedua)/total_all)*100).toFixed(1)+'%');
					$('#persen_sudah_vaksin_pertama').text(((total_sudah_vaksin_pertama/total_all)*100).toFixed(1)+'%');
					$('#persen_sudah_vaksin_kedua').text(((total_sudah_vaksin_kedua/total_all)*100).toFixed(1)+'%');
					$('#persen_belum_vaksin_kedua').text(((total_belum_vaksin_kedua/total_all)*100).toFixed(1)+'%');
					$('#persen_sudah_vaksin_ketiga').text(((total_sudah_vaksin_ketiga/total_all)*100).toFixed(1)+'%');
					$('#persen_belum_vaksin_ketiga').text(((total_belum_vaksin_ketiga/total_all)*100).toFixed(1)+'%');
					// $('#persen_sudah_daftar').text(((total_sudah_daftar/total_all)*100).toFixed(1)+'%');
					$('#persen_belum').text(((total_belum/total_all)*100).toFixed(1)+'%');
					$('#persen_belum_vaksin').text(((total_belum_all/total_all)*100).toFixed(1)+'%');

					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Resume Data Vaksinasi Karyawan YMPI',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: dept,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Data',
								style: {
									color: '#eee',
									fontSize: '15px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"15px"
								}
							},
							type: 'linear',
							opposite: true
						}
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
								fontSize:'12px',
							},
							reversed : true
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category,this.series.name);
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
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
							},
						},credits: {
							enabled: false
						},
						series: [{
							type: 'column',
							data: series3,
							name: 'Belum',
							colorByPoint: false,
							color:'#f44336'
						},{
							type: 'column',
							data: series2,
							name: 'Sudah Daftar',
							colorByPoint: false,
							color:'#32a852'
						},{
							type: 'column',
							data: series,
							name: 'Sudah Vaksin',
							colorByPoint: false,
							color: "#304ffe"
						}
						]
					});
				}
			}
		});

	}

	function ShowModal(dept,answer) {
		clearInterval(intervalChart);
		$('#modalDetail').modal('show');
		$('#loadingDetail').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();

		$("#loading").show();

		var data = {
			dept:dept,
			answer:answer,
		}

		$.get('{{ url("fetch/vaksin/monitoring/detail") }}', data, function(result, status, xhr) {
			if(result.status){

				$("#loading").hide();
				$('#tableDetailBody').html('');

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				var keterangan = "covid";

				$.each(result.vaksin, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees With Answer '"+answer+"'</span></center>");

				$('#loadingDetail').hide();
				$('#tableDetail').show();
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
				intervalChart = setInterval(fillChart,60000);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function ShowModalAll(answer) {
		clearInterval(intervalChart);
		$('#modalDetailAll').modal('show');
		$('#loadingDetailAll').show();
		$('#modalDetailTitleAll').html("");
		$('#tableDetailAll').hide();

		$("#loading").show();

		var data = {
			answer:answer,
		}

		$.get('{{ url("fetch/vaksin/monitoring/detailAll") }}', data, function(result, status, xhr) {
			if(result.status){

				$("#loading").hide();
				$('#tableDetailBodyAll').html('');

				$('#tableDetailAll').DataTable().clear();
				$('#tableDetailAll').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				var keterangan = "covid";

				$.each(result.vaksin, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBodyAll').append(resultData);
				$('#modalDetailTitleAll').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees '"+answer+"'</span></center>");

				$('#loadingDetailAll').hide();
				$('#tableDetailAll').show();
				var table = $('#tableDetailAll').DataTable({
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
				intervalChart = setInterval(fillChart,60000);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}


</script>
@endsection