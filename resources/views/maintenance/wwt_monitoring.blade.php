@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #dcdcdc;
		border: 1px solid black !important;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		color: black;
		/*background-color: white;*/
	}
	thead {
		/*background-color: rgb(126,86,134);*/
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
		font-weight: bold;
		font-size: 20px;
	}

	#loading, #error { display: none; }

	.blink_me {
		animation: blinker 1s linear infinite;
	}

	@keyframes blinker {
		50% {
			opacity: 0;
			background-color: yellow;
		}
	}
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="col-xs-12">
		<div class="col-xs-2">
			<span style="color: white;">Dari Tanggal :</span>
			<div class="input-group date">
				<div class="input-group-addon bg-purple" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right"id="dari_tanggal" name="dari_tanggal" value="{{ $dari }}">
			</div>
		</div>
		<div class="col-xs-2">
			<span style="color: white;">Sampai Tanggal :</span>
			<div class="input-group date">
				<div class="input-group-addon bg-purple" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right" id="sampai_tanggal" name="sampai_tanggal" value="{{ $sampai }}" onchange="Chart()">
			</div>
		</div>
		<div class="col-xs-8" style="padding-top: 20px">
			<a href="{{ url('index/maintenance/wwt/waste_control/update') }}" class="btn btn-success pull-right"><i class="fa fa-list"></i> Input Limbah</a>
		</div>
	</div>

	<div class="col-xs-12">
		<div class="col-xs-3" style="margin-top: 20px" align="center">
			<div class="small-box" style="background: #00ff73; height: 100%;cursor: pointer;color:black" onclick="DetailMasaSimpan()">
				<div id="blink">
					<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1.4vw; text-align: left;"><b>Masa Simpan Terlama</b></h3>
					<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1.1vw;color: #0d47a1; text-align: left;"><b>( 入力< )</b></h3>
					<span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="masa_simpan">0</span><span style="font-size: 2vw; margin-bottom: 0px;margin-top: 0px"> Hari</span> 
					<div class="icon" style="padding-top: 20px;font-size:8vh;">
						<i class="fa fa-recycle"></i>
					</div>
				</div>
			</div>
			<div class="small-box" style="background: #ff9800; height: 100%;cursor: pointer;color:black">
				<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1.4vw; text-align: left;"><b>JUMLAH LIMBAH WWT</b></h3>
				<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1.1vw;color: #0d47a1; text-align: left; "><b>( 入力< )</b></h3>
				<div class="icon" style="padding-top: 20px;font-size:8vh;">
					<i class="fa fa-recycle"></i>
				</div>
				<table style="border-collapse: collapse; width: 100%">
					<tr>
						<td style="width: 1%; height: 50px; font-size: 1.4vw">JUMBO BAG</td>
						<td style="width: 1%; font-size: 1.4vw">PAIL</td>
						<td style="width: 1%; font-size: 1.4vw">DRUM</td>
					</tr>
					<tr>
						<td id="modal_wwt" onclick="DetailCategory(this.id)"><span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="wwt">0</span></td>
						<td id="modal_wwt_pail" onclick="DetailCategory(this.id)"><span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="wwt_pail">0</span></td>
						<td id="modal_wwt_drum" onclick="DetailCategory(this.id)"><span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="wwt_drum">0</span></td>
					</tr>
				</table>
			</div>
			<div class="small-box" style="background: #ff8983; height: 100%;cursor: pointer;color:black">
				<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1.4vw; text-align: left;"><b>JUMLAH LIMBAH DISPOSAL</b></h3>
				<h3 style="margin-bottom: 0px; padding-left: 15px; font-size: 1.1vw;color: #0d47a1; text-align: left; "><b>( 入力< )</b></h3>
				<div class="icon" style="padding-top: 20px;font-size:8vh;">
					<i class="fa fa-recycle"></i>
				</div>
				<table style="border-collapse: collapse; width: 100%">
					<tr>
						<td style="width: 1%; height: 50px; font-size: 1.4vw">JUMBO BAG</td>
						<td style="width: 1%; font-size: 1.4vw">PAIL</td>
						<td style="width: 1%; font-size: 1.4vw">DRUM</td>
					</tr>
					<tr>
						<td id="modal_disposal" onclick="DetailCategory(this.id)"><span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="disposal">0</span></td>
						<td id="modal_disposal_pail" onclick="DetailCategory(this.id)"><span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="disposal_pail">0</span></td>
						<td id="modal_disposal_drum" onclick="DetailCategory(this.id)"><span style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px" id="disposal_drum">0</span></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="col-xs-9" style="margin-top: 20px">
			<div id="chart" style="height: 35vh"></div><br>
			<div id="chart2" style="height: 35vh"></div>
		</div>
	</div>
	
	<div class="modal fade" id="modalDetail" style="z-index: 10000;">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="nav-tabs-custom tab-danger" align="center">
						<ul class="nav nav-tabs">
							<h2 id="detailJudul"></h2>
						</ul>
					</div>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="detailtable">
						<thead style="background-color: rgb(126,86,134)">
							<tr>
								<th>No</th>
								<th>Slip</th>
								<th>Jenis Limbah</th>
								<th>Jumlah</th>
								<th>Tanggal Masuk</th>
								<th>Tanggal Keluar</th>
								<th>Masa Simpan</th>
							</tr>
						</thead>
						<tbody id="detailbodytable">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.select2').select2();

		$('#dari_tanggal').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true,
			autoclose: true
		});

		$('#sampai_tanggal').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true,
			autoclose: true
		});

		Chart();
		setInterval(function() {
			Chart();
		}, 30000);
	})

	var alarm = new Audio('{{ url("sounds/alarm_error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function alarm_error() {
		alarm.play();
	}

	var intervals;

	function blink(hasil) {
		if (hasil >= 80) {
			$("#blink").addClass("blink_me");
			// intervals = setInterval(alarm_error,1000);
			SendEmail(hasil);
		}else{
			$("#blink").removeClass("blink_me");
			clearInterval(intervals);
		}
	}

	function SendEmail(hasil){
		var data = {
			hasil : hasil
		}
		$.post('{{ url("notifikasi/email") }}', data, function(result, status, xhr) {})
	}

	function FetchFy(){
		var fy = $("#fiscal_year").val();
		Chart(fy);
	}

	function Chart(fy) {
		$("#loading").show();
		// console.log(fy);
		var data = {
			dari_tanggal:$('#dari_tanggal').val(),
			sampai_tanggal:$('#sampai_tanggal').val(),
			fy:fy
		}

		$.get('{{ url("fetch/maintenance/wwt/monitoring") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					$("#loading").hide();
					var waste_category = [];
					var jumlah = [];
					var disposal = [];
					var masa_simpan = [];
					var intervals = [];

					for (var i = 0; i < result.grafik.length; i++) {
						waste_category.push(result.grafik[i].waste_category);
						jumlah.push(parseInt(result.grafik[i].jml_wwt));
						disposal.push(parseInt(result.grafik[i].jml_disposal));
					}

					$("#wwt").html(result.wwt[0].wwt);
					console.log(result.wwt[0].wwt);
					$("#wwt_pail").html(result.wwt_pail[0].wwt);
					$("#wwt_drum").html(result.wwt_drum[0].wwt);
					$("#disposal").html(result.disposal[0].disposal);
					$("#disposal_pail").html(result.disposal_pail[0].disposal);
					$("#disposal_drum").html(result.disposal_drum[0].disposal);
					$("#masa_simpan").html(result.hasil);
					blink(result.hasil);

					var colors = ['#f45b5b', '#ff9800'];

					Highcharts.chart('chart', {
						chart: {
							type: 'column',
							options3d: {
								enabled: true,
								alpha: 15,
								beta: 0,
								depth: 50,
								viewDistance: 50
							}
						},
						title: {
							text: 'JUMLAH LIMBAH WWT'
						},
						xAxis: {
							categories: waste_category,
							type: 'category',
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
						},yAxis: [{
							title: {
								text: 'Total',
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
							opposite: false
						},
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
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
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											// ShowModal(this.category,this.series.name);
											detailLimbah(this.category, this.series.name);
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
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						colors:colors,
						series: [{
							data: jumlah,
							color: 'orange',
							name: 'WWT',
							index : 1,
							legendIndex : 0
						}]
					});

					var categori = [];
					var series = [];

					$.each(result.series_disposal, function(key, value){
						var isi = 0;
						categori.push(value.bulan);
						$.each(result.data_disposal, function(key2, value2){
							if (value.bulan == value2.bulan2) {
								series.push(value2.jumlah);
								isi = 1;
							}
						});
						if (isi == 0) {
							series.push(0);
						}
					});

					Highcharts.chart('chart2', {
						chart: {
							type: 'column',
							options3d: {
								enabled: true,
								alpha: 15,
								beta: 0,
								depth: 50,
								viewDistance: 50,
							}
						},
						title: {
							text: 'JUMLAH LIMBAH DISPOSAL'
						},
						xAxis: {
							categories: categori,
							type: 'category',
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
						},yAxis: [{
							title: {
								text: 'Total',
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
							opposite: false
						},
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
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
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										// click: function () {
										// 	detailLimbah(this.category, this.series.name);
										// }
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
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							data: series,
							name: 'Disposal',
							zIndex: 0,
							color: 'red'
						}, {
							name: ' ',
							type: 'line',
							zIndex: 1,
							data: series
						}]
					});
				}
			}
		});
	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
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
		}
	};
	Highcharts.setOptions(Highcharts.theme);

	function DetailMasaSimpan(){
		$('#modalDetail').modal('show');
		$('#detailJudul').html('Detail Masa Simpan Limbah WWT');
		var data = {
			terbanyak : $('#masa_simpan').html(),

		}
		$.get('{{ url("fetch/all/monitoring/detail") }}', data, function(result, status, xhr) {

			$('#detailtable').DataTable().clear();
			$('#detailtable').DataTable().destroy();
			$("#detailbodytable").empty();
			var body = '';

			$.each(result.masa_simpan, function(index, value){
				body += '<tr>';
				body += '<td>'+(index+1)+'</td>';
				body += '<td>'+value.slip+'</td>';
				body += '<td>'+value.waste_category+'</td>';
				if (value.quantity == null) {
					body += '<td><span class="label label-danger">Qty Belum Input</span></td>';
				}else{
					body += '<td>'+value.quantity+' KG</td>';
				}
				body += '<td>'+value.date_in+'</td>';
				body += '<td>-</td>';
				body += '<td>'+value.jml+' Hari</td>';
				body += '</tr>';
			})

			$("#detailbodytable").append(body);

			var table = $('#detailtable').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
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
				"processing": false,
			});
		})
	}

	function DetailCategory(id){
		// console.log(id);
		$('#modalDetail').modal('show');
		if (id == 'modal_wwt') {
			$('#detailJudul').html('Detail Limbah WWT Jumbo Bag');
		}else if(id == 'modal_wwt_pail'){
			$('#detailJudul').html('Detail Limbah WWT Pail');
		}else if(id == 'modal_wwt_drum'){
			$('#detailJudul').html('Detail Limbah WWT Drum');
		}else if(id == 'modal_disposal'){
			$('#detailJudul').html('Detail Limbah Disposal Jumbo Bag');
		}else if(id == 'modal_disposal_pail'){
			$('#detailJudul').html('Detail Limbah Disposal Pail');
		}else if(id == 'modal_disposal_drum'){
			$('#detailJudul').html('Detail Limbah Disposal Drum');
		}
		var data = {
			category : id,
			dari_tanggal : $('#dari_tanggal').val(),
			sampai_tanggal : $('#sampai_tanggal').val() 
		}
		$.get('{{ url("fetch/all/monitoring/detail") }}', data, function(result, status, xhr) {

			$('#detailtable').DataTable().clear();
			$('#detailtable').DataTable().destroy();
			$("#detailbodytable").empty();
			var body = '';

			$.each(result.detail_modal, function(index, value){
				body += '<tr>';
				body += '<td>'+(index+1)+'</td>';
				body += '<td>'+value.slip+'</td>';
				body += '<td>'+value.waste_category+'</td>';
				if (value.quantity == null) {
					body += '<td><span class="label label-danger">Qty Belum Input</span></td>';
				}else{
					body += '<td>'+value.quantity+' KG</td>';
				}
				body += '<td>'+value.date_in+'</td>';
				if (id == 'modal_wwt') {
					body += '<td>-</td>';
				}else{
					body += '<td>'+value.date_disposal+'</td>';
				}
				body += '<td>'+value.jml+' Hari</td>';
				body += '</tr>';
			})

			$("#detailbodytable").append(body);

			var table = $('#detailtable').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
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
				"processing": false,
			});
		})

	}

	function detailLimbah(category, name){
		$('#modalDetail').modal('show');
		$('#detailJudul').html('Limbah '+ name+ ' ' + category);
		var data = {
			category : category,
			jenis : name,
			dari_tanggal : $('#dari_tanggal').val(),
			sampai_tanggal : $('#sampai_tanggal').val() 
		}
		// console.log(category, name);
		$.get('{{ url("fetch/wwt/monitoring/detail") }}', data, function(result, status, xhr) {

			$('#detailtable').DataTable().clear();
			$('#detailtable').DataTable().destroy();
			$("#detailbodytable").empty();
			var body = '';

			$.each(result.detail_modal, function(index, value){
				body += '<tr>';
				body += '<td>'+(index+1)+'</td>';
				body += '<td>'+value.slip+'</td>';
				body += '<td>'+value.waste_category+'</td>';
				if (value.quantity == null) {
					body += '<td><span class="label label-danger">Qty Belum Input</span></td>';
				}else{
					body += '<td>'+value.quantity+' KG</td>';
				}
				body += '<td>'+value.date_in+'</td>';
				if (name == 'WWT') {
					body += '<td>-</td>';
				}else{
					body += '<td>'+value.date_disposal+'</td>';
				}
				body += '<td>'+value.jml+' Hari</td>';
				body += '</tr>';
			})

			$("#detailbodytable").append(body);

			var table = $('#detailtable').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
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
				"processing": false,
			});
		})
	}

	function Refresh(){
		location.reload(true);
	}

</script>
@endsection