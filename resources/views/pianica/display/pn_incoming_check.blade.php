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
						<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
					</div>
				</div>

				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-7">
					<div id="container1" class="container1" style="width: 100%;"></div> <br>
					<div id="container2" class="container2" style="width: 100%;"></div>
				</div>
				<div class="col-xs-5" style="padding-left: 0px">
					<div class="col-xs-6" style="padding-left: 0px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE DAY</td>
								</tr>
								<tr>
									<td id="lowest_avatar_daily" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_name_daily" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_ng_daily" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE WEEK</td>
								</tr>
								<tr>
									<td id="lowest_avatar" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_name" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_ng" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" id="highest_title_daily">BAD QUALITY EMPLOYEE<br>OF THE DAY</td>
								</tr>
								<tr id="not_counceled">
									<td id="not_counceled_td_daily" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
								<tr>
									<td id="highest_avatar_daily" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;cursor: pointer;"></td>
								</tr>
								<tr>
									<td id="highest_name_daily" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
								<tr>
									<td id="highest_ng_daily" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" onclick="councelingModal()" class="sedang" id="highest_title">BAD QUALITY EMPLOYEE<br>OF THE WEEK</td>
								</tr>
								<tr id="not_counceled">
									<td onclick="councelingModal()" id="not_counceled_td" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" class="sedang">BELUM DILAKUKAN <br> TRAINING & KONSELING</td>
								</tr>
								<tr>
									<td id="highest_avatar" onclick="councelingModal()" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;cursor: pointer;"></td>
								</tr>
								<tr>
									<td id="highest_name" onclick="councelingModal()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" class="sedang"></td>
								</tr>
								<tr>
									<td id="highest_ng" onclick="councelingModal()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" class="sedang"></td>
								</tr>
							</table>
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
								<th style="width: 2%;">Check Date</th>
								<th style="width: 3%;">Model</th>
								<th style="width: 4%;">Nomor Reed</th>
								<th style="width: 2%;">NG</th>
								<th style="width: 4%;">Quantity</th>
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
							<button class="btn btn-danger" onclick="cancelScan('tag_employee')"><i class="fa fa-close"></i> Cancel</button>
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
							<button class="btn btn-danger" onclick="cancelScan('tag_leader')"><i class="fa fa-close"></i> Cancel</button>
						</div>
					</div>

					<div class="form-group">
						<div class="col-xs-12" style="padding-left: 0px;">
							<label for="">Document Training</label>
						</div>
						<div class="col-xs-10" style="padding-left: 0px;">
							<!-- <input type="file" name="counceled_image" id="counceled_image" class="form-control" placeholder="Scan ID Card Sub Leader / Leader"> -->
							<a href="{{url('input/injection/training_document')}}" target="_blank" class="btn btn-primary"><i class="fa fa-pencil"></i>&nbsp; Input Document Training</a>
						</div>
					</div>
				</div>
				<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
					<button class="btn btn-success" onclick="submitCouncel()"><i class="fa fa-check"></i> Submit</button>
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
		$('.select2').select2();
		fetchChart();
		$('#modalDetail').on('hidden.bs.modal', function () {
			$('#tableDetail').DataTable().clear();
		});
		setInterval(fetchChart, 300000);
	});

	var detail_all_injeksi = [];
	var detail_all_assy = [];

	function fetchChart(){
		$("#loading").show();
		var tanggal = $("#tanggal").val();

		var data = {
			tanggal:tanggal,
		}

		var data_ng = ['Lepas','Longgar','Pangkal Menempel','Panjang','Melekat','Ujung Menempel','Lengkung','Terbalik','Celah Lebar','Salah Posisi','Kepala Rusak','Patah','Lekukan','Kotor','Celah Sempit','Double'];

		$.get('{{ url("fetch/pianica/monitoring/pn_part") }}', data, function(result, status, xhr) {
			$("#loading").hide();
			if(result.status){
				var series = [];
				var series_ng = [];
				var operator = [];

				var op_low;
				var op_high;

				var op_low_week;
				var op_high_week;
				$.each(result.operator, function(index, value){
					var stat = 0;
					operator.push(value.nama);

					$.each(result.datas, function(index2, value2){
						if (value2.nik_op_plate == value.nik) {
							series.push(parseInt(value2.jml_ng));
							stat = 1;
						}
					})

					if (stat == 0) {
						series.push(0);
					}
				})

				// ------ NG ------------
				$.each(data_ng, function(index, value){
					var stat2 = 0;
					$.each(result.datas_ng, function(index2, value2){
						if (value == value2.ng) {
							stat2 = 1;
							series_ng.push(parseInt(value2.jml_ng));
						}
					})

					if (stat2 == 0) {
						series_ng.push(0);
					}
				})

				//HIGHEST LOWEST TODAY

				$.each(result.operator, function(index, value){
					var stat = 0;

					$.each(result.harian, function(index2, value2){
						if (value2.nik_op_plate == value.nik) {
							stat = 1;
						}
					})

					if (stat == 0) {
						op_low = {'nik' : value.nik, 'nama' : value.nik+" - "+value.nama, 'ng' : 0};
					}
				})

				if (op_low.length == 0) {
					op_low = {'nik' : result.harian[0].nik_op_plate, 'nama' : result.harian[0].nik_op_plate+" - "+result.harian[0].name, 'ng' : parseInt(result.harian[0].jml_ng)};
				}

				op_high = {'nik' : result.harian[result.harian.length-1].nik_op_plate, 'nama' : result.harian[result.harian.length-1].nik_op_plate+" - "+result.harian[result.harian.length-1].name, 'ng' : parseInt(result.harian[result.harian.length-1].jml_ng)};
				
				var thumbs_up = '{{ url("data_file/injection/ok.png") }}';
				var thumbs_down = '{{ url("data_file/injection/not_ok.png") }}';

				var url_lowest = '{{ url("images/avatar/") }}/'+op_low.nik+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+op_high.nik+'.jpg';

				$("#lowest_avatar_daily").html("<img src='"+url_lowest+"' style='width: 80px' alt='user image'> <img style='width:80px' src='"+thumbs_up+"' >");
				$("#lowest_name_daily").html(op_low.nama);
				$("#lowest_ng_daily").html("Jumlah NG = "+ op_low.ng);

				$("#highest_avatar_daily").html("<img src='"+url_highest+"' style='width: 80px' alt='user image'> <img style='width:80px' src='"+thumbs_down+"' >");
				$("#not_counceled_td_daily").html("NG Terbanyak <br> Reed "+result.ng_harian[0].kode_reed+" "+result.ng_harian[0].ng+" = "+result.ng_harian[0].jml_ng);
				$("#highest_name_daily").html(op_high.nama);
				$("#highest_ng_daily").html("Jumlah NG = "+ op_high.ng);

				//HIGHEST LOWEST WEEKLY

				var counceling = "";

				$.each(result.operator, function(index, value){
					var stat = 0;

					$.each(result.mingguan, function(index2, value2){
						if (value2.nik_op_plate == value.nik) {
							stat = 1;
						}
					})

					if (stat == 0) {
						op_low_week = {'nik' : value.nik, 'nama' : value.nik+" - "+value.nama, 'ng' : 0};
					}
				})

				if (op_low_week.length == 0) {
					op_low_week = {'nik' : result.mingguan[0].nik_op_plate, 'nama' : result.mingguan[0].nik_op_plate+" - "+result.mingguan[0].name, 'ng' : parseInt(result.mingguan[0].jml_ng)};
				}

				op_high_week = {'nik' : result.mingguan[result.mingguan.length-1].nik_op_plate, 'nama' : result.mingguan[result.mingguan.length-1].nik_op_plate+" - "+result.mingguan[result.mingguan.length-1].name, 'ng' : parseInt(result.mingguan[result.mingguan.length-1].jml_ng), 'from' : result.mingguan[result.mingguan.length-1].from, 'to' : result.mingguan[result.mingguan.length-1].to};

				var url_lowest = '{{ url("images/avatar/") }}/'+op_low_week.nik+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+op_high_week.nik+'.jpg';

				$("#lowest_avatar").html("<img src='"+url_lowest+"' style='width: 80px' alt='user image'> <img style='width:80px' src='"+thumbs_up+"' >");
				$("#lowest_name").html(op_low_week.nama);
				$("#lowest_ng").html("Jumlah NG = "+ op_low_week.ng);

				$("#highest_avatar").html("<img src='"+url_highest+"' style='width: 80px' alt='user image'> <img style='width:80px' src='"+thumbs_down+"' >");
				$("#highest_name").html(op_high_week.nama);
				$("#highest_ng").html("Jumlah NG = "+ op_high_week.ng);
				$("#firstDate").val(op_high_week.from);
				$("#lastDate").val(op_high_week.to);

				// training
				$.each(result.training, function(index3, value3){
					if (value3.employee_id == op_high_week.nik && value3.period_from == op_high_week.from && value3.period_to == op_high_week.to) {
						$("#highest_title").removeClass("sedang");
						$("#not_counceled_td").removeClass("sedang");
						$("#not_counceled_td").html("SUDAH DILAKUKAN <br> TRAINING & KONSELING");
						$("#not_counceled_td").css('background-color', '#82ff80');
						$("#highest_name").removeClass("sedang");
						$("#highest_ng").removeClass("sedang");
					}
				})

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						height: '250',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL INCOMING CHECK BENSUKI",
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.tgl,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: operator,
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
							text: 'Qty NG Pc(s)',
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
						max:100
					}
					],
					tooltip: {
						headerFormat: '<span>Total NG Assy</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{this.category} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
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
										ShowModal(this.category, 'Operator');
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
						data: series,
						name: "Total NG",
						colorByPoint: false,
						color: "#fc5d5d",
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


				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						height: '250',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: " ",
						style: {
							fontSize: '10px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: ' ',
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: data_ng,
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
							text: 'Qty NG Pc(s)',
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
						max:100
					}
					],
					tooltip: {
						headerFormat: '<span>Total NG Assy</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{this.category} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						
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
										ShowModal(this.category, 'NG');
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
						column : {
							dataLabels: {
								allowOverlap: true,
								enabled: true,
							}
						}
					},
					series: [
					{
						zoneAxis: 'x',
						type: 'column',
						data: series_ng,
						name: "Total NG",
						colorByPoint: false,
						color: "#fc5d5d",
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

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

function ShowModal(category, stat) {
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');

	var bodyDetail = '';
	var total_ng = 0;

	$('#modalDetailTitle').html('Detail NG From '+category);
	if (stat === 'injeksi') {
		var index = 1;
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
	if (stat === 'assy') {
		var index = 1;
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

	$('#modalDetail').modal('show');
}

function councelingModal() {
	if ($('#not_counceled_td').text() == 'BELUM DILAKUKAN  TRAINING & KONSELING') {
		$('#modalCounceling').modal('show');
		$('#employee_id').html($('#highest_name').text().split(' - ')[0]);
		$('#name').html($('#highest_name').text().split(' - ')[1]);
		$('#ng_qty').html($('#highest_ng').text().split(' = ')[1]);

		$('#modalDetailTitleCounceling').html('TRAINING DAN KONSELING');

		$('#tag_employee').val('');
		$('#tag_leader').val('');
		// document.getElementById("counceled_image").value = "";
		$('#tag_employee').removeAttr('disabled');
		$('#tag_leader').removeAttr('disabled');
		$('#tag_employee').focus();
	}
}

function submitCouncel() {
	$('#loading').show();
	if ($('#tag_employee').val() == "" || $('#tag_leader').val() == "") {
		$('#loading').hide();
		openErrorGritter('Error!','Semua Data Harus Diisi');
		return false;
	}
	var counceled_employee = $("#tag_employee").val();
	var counceled_by = $("#tag_leader").val();
	var first_date = $("#firstDate").val();
	var last_date = $("#lastDate").val();

	var formData = new FormData();
	formData.append('counceled_employee', counceled_employee);
	formData.append('counceled_by', counceled_by);
	formData.append('first_date', first_date);
	formData.append('last_date', last_date);
	formData.append('total_ng', $("#ng_qty").text());

	$.ajax({		
		url:"{{ url('input/pianica/counceling/pn_part') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			$('#loading').hide();
			fetchChart();
			$('#modalCounceling').modal('hide');
			openSuccessGritter('Success','Input Konseling Berhasil');
		},
		error: function (err) {
			openErrorGritter('Error!',err);
		}
	})
}

$('#tag_employee').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($('#tag_employee').val().length > 9 ){
			var data = {
				employee_id : $("#tag_employee").val()
			}

			$.get('{{ url("scan/injection/counceled_employee") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.employee.employee_id != $('#employee_id').text()) {
						audio_error.play();
						openErrorGritter('Error!', 'Operator Tidak Sama');
						$('#tag_employee').val('');
					}else{
						$('#tag_employee').val(result.employee.employee_id+'-'+result.employee.name);
						$('#tag_employee').prop('disabled',true);
						openSuccessGritter('Success!', result.message);
					}
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#tag_employee').val('');
				}
			});
		}else{
			openErrorGritter('Error!', 'Tag Tidak Ditemukan');
			$('#tag_employee').val('');
		}
	}
});

$('#tag_leader').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($('#tag_leader').val().length > 9 ){
			var data = {
				employee_id : $("#tag_leader").val()
			}

			$.get('{{ url("scan/injection/counceled_by") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tag_leader').val(result.employee.employee_id+'-'+result.employee.name);
					$('#tag_leader').prop('disabled',true);
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_leader').val('');
				}
			});
		}else{
			openErrorGritter('Error', 'Tag Tidak Ditemukan');
			$('#tag_leader').val('');
		}
	}
});

function cancelScan(btn) {
	$('#'+btn).val('');
	$('#'+btn).removeAttr('disabled');
	$('#'+btn).focus();
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

</script>
@endsection