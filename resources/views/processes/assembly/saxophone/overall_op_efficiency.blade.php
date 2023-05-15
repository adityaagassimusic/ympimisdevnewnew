@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ url("js/jsQR.js")}}"></script>
<style type="text/css">
	canvas{
		text-align: center;
	}
	.morecontent span {
		display: none;
	}
	.morelink {
		display: block;
	}
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
		border:1px solid #2a2a2b;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.content{
		color: white;
		font-weight: bold;
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
	.std {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgb(255,116,116);
		display: inline-block;
	}
	.act {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgb(144,238,126);
		display: inline-block;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-1">
						<button class="btn btn-success" onclick="getData()">Update Chart</button>
					</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>	

			{{-- OP Overall Eff --}}
			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
				<div id="shifta">
					<div id="container1_shifta" style="width: 100%;"></div>					
				</div>
				<div id="shiftb">
					<div id="container1_shiftb" style="width: 100%;"></div>					
				</div>
			</div>


			{{-- Last NG --}}
			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
				<div id="shifta4">
					<div id="container4_shifta" style="width: 100%;"></div>
				</div>
				<div id="shiftb4">
					<div id="container4_shiftb" style="width: 100%;"></div>
				</div>
			</div>

		</div>
	</div>

	<!-- start modal detail  -->
	<div class="modal fade" id="myModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator Overall Efficiency Details</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						{{-- <h5 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Resume</b></h5> --}}

						<div class="col-md-12" style="margin-bottom: 20px;">
							<div class="col-md-4">
								<h5 class="modal-title">NG Rate</h5>
								<h5 class="modal-title" id="ng_rate"></h5>
							</div>
							<div class="col-md-4">
								<h5 class="modal-title">Post Rate</h5>
								<h5 class="modal-title" id="posh_rate"></h5>
							</div>
							<div class="col-md-4">
								<h5 class="modal-title">Operator Overall Efficiency</h5>
								<h5 class="modal-title" id="op_eff"></h5>
							</div>
						</div>

						<div class="col-md-7">
							<table id="welding-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="welding-ng-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">NOT GOOD</th>
									</tr>
									<tr>
										<th style="width: 20%">Finish Welding</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>Material Qty</th>
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
										<th style="width: 20%">Finish Welding</th>
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

						<div class="col-md-12">
							<table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="8" style="text-align: center;">WELDING RESULT</th>
									</tr>
									<tr>
										<th>Part Type</th>
										<th>Model</th>
										<th>Key</th>
										<th style="width: 13%">Start</th>
										<th style="width: 13%">Finsih</th>
										<th style="width: 10%">Standart time</th>
										<th style="width: 10%">Actual time</th>
										<th style="width: 10%">Material Qty</th>
									</tr>
								</thead>
								<tbody id="data-log-body">
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

	<!-- start modal detail  -->
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
						Handling Operator's Efficiency
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								
								<input type="hidden" id="m_operator">
								<input type="hidden" id="date">

								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="input_tag">
									</div>
								</div>

								<div class="form-group row" align="right" id="field-nik">
									<label class="col-sm-4">NIK</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="employee_id" readonly>
									</div>
								</div>

								<div class="form-group row" align="right" id="field-name">
									<label class="col-sm-4">Name</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="name" readonly>
									</div>
								</div>

								<div class="form-group row" align="right" id="field-key">
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
					<button class="btn btn-danger" data-dismiss="modal"><span><i class="glyphicon glyphicon-remove-sign"></i> Cancel</span></button>
					<button id="btn-check" class="btn btn-success" onclick="checkEff()"><span><i class="fa fa-check-square-o"></i> Check</span></button>
				</div>
			</div>
		</div>
	</div>
	<!-- end modal -->


</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 20000);
		
	});

	function getData() {
		$("#loading").show();
		
		location.reload(); 
	}



	function showDetail(tgl, nama) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$('#myModal').modal('show');
		$('#data-log-body').append().empty();
		$('#welding-log-body').append().empty();
		$('#welding-ng-log-body').append().empty();
		$('#welding-cek-body').append().empty();
		$('#ng_rate').append().empty();
		$('#posh_rate').append().empty();
		$('#op_eff').append().empty();
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
				for (var i = 0; i < result.ng.length; i++) {
					body += '<tr>';
					body += '<td>'+result.ng[i].welding_time+'</td>';
					body += '<td>'+result.ng[i].model+'</td>';
					body += '<td>'+result.ng[i].key+'</td>';
					body += '<td>'+result.ng[i].op_kensa+'</td>';
					body += '<td>'+result.ng[i].quantity+'</td>';
					body += '</tr>';

					total_ng += parseInt(result.ng[i].quantity);
				}
				body += '<tr>';
				body += '<td colspan="4" style="text-align: center;">Total</td>';
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


				//Data Log
				var total_perolehan = 0;
				var total_std = 0;
				var total_act = 0;

				var body = '';
				for (var i = 0; i < result.data_log.length; i++) {
					body += '<tr>';
					body += '<td>'+result.data_log[i].part_type+'</td>';
					body += '<td>'+result.data_log[i].model+'</td>';
					body += '<td>'+result.data_log[i].key+'</td>';
					body += '<td>'+result.data_log[i].start+'</td>';
					body += '<td>'+result.data_log[i].finish+'</td>';
					body += '<td>'+result.data_log[i].std+'</td>';
					body += '<td>'+result.data_log[i].act+'</td>';
					body += '<td>'+result.data_log[i].perolehan_jumlah+'</td>';
					body += '</tr>';
					total_perolehan += parseInt(result.data_log[i].perolehan_jumlah);
					total_std += parseFloat(result.data_log[i].std);
					total_act += parseFloat(result.data_log[i].act);
				}
				body += '<tr>';
				body += '<td colspan="5" style="text-align: center;">Total</td>';
				body += '<td>'+total_std.toFixed(2)+'</td>';
				body += '<td>'+total_act.toFixed(2)+'</td>';
				body += '<td>'+total_perolehan+'</td>';
				body += '</tr>';
				$('#data-log-body').append(body);


				//Resume
				var ng_rate = total_ng / total_cek * 100;
				var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Cek</sub> x 100%';
				text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_cek +'</sub> x 100%';
				text_ng_rate += '<br>= <b>'+ ng_rate.toFixed(2) +'%</b>';
				$('#ng_rate').append(text_ng_rate);

				var posh_rate = ((total_cek - total_ng) / total_cek) * 100;
				var text_posh_rate = '= <sup>(Total Cek - Total NG)</sup>/<sub>Total Cek</sub> x 100%';
				text_posh_rate += '<br>= <sup>('+ total_cek + ' - ' + total_ng +')</sup>/<sub>'+ total_cek +'</sub> x 100%';
				text_posh_rate += '<br>= <b>'+ posh_rate.toFixed(2) +'%</b>';
				$('#posh_rate').append(text_posh_rate);

				var op_eff = posh_rate * (total_std / total_act);
				var text_op_eff = '= <sup>Total Standart time</sup>/<sub>Total Actual time</sub> x Posh Rate';
				text_op_eff += '<br>= <sup>'+ total_std.toFixed(2) +'</sup>/<sub>'+ total_act.toFixed(2) +'</sub> x '+ posh_rate.toFixed(2) +'%';
				text_op_eff += '<br>= <b>'+ op_eff.toFixed(2) +'%</b>';
				$('#op_eff').append(text_op_eff);

			}

		});
}


function fillChart() {
	var tanggal = $("#tanggal").val();

	var position = $(document).scrollTop();

	var data = {
		tanggal:tanggal,
		origin: '{{ Request::segment(4) }}'
	}

	$.get('{{ url("fetch/assembly/overall_eff") }}', data, function(result, status, xhr) {
		if(result.status){
			$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

			var op_name = [];
			var eff_value = [];
			var series = [];


			var chart = Highcharts.chart('container1', {
				chart: {
					animation: false
				},
				title: {
					text: 'Operators Overall Efficiency',
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'on '+ result.date,
					style: {
						fontSize: '1vw',
						fontWeight: 'bold'
					}
				},
				yAxis: {
					title: {
						enabled: true,
						text: "Overall Efficiency (%)"
					},
					min: 0,
					max: 500,
					plotLines: [{
						color: '#FF0000',
						value: 100,
						dashStyle: 'shortdash',
						width: 2,
						zIndex: 5,
						label: {
							align:'right',
							text: 'Target '+100+'%',
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
				xAxis: {
					categories: op_name,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						rotation: -45,
						style: {
							fontSize: '13px'
						}
					},
				},
				tooltip: {
					headerFormat: '<span>{point.category}</span><br/>',
					pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
				},
				credits: {
					enabled:false
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
									showDetail(result.date, event.point.category);

								}
							}
						},
					}
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: series,
					showInLegend: false
				}]

			});


			$(document).scrollTop(position);

		}
	});


	$.get('{{ url("fetch/welding/welding_op_eff_target") }}', data, function(result, status, xhr) {
		if(result.status){
			var target = result.eff_target;

			var op_a = [];
			var name_a = [];
			var key = [];
			var eff = [];
			var data = [];
			var loop = 0;
			var plotBands = [];

			for(var i = 0; i < result.target.length; i++){
				if(result.target[i].group == 'A'){
					loop += 1;

					op_a.push(result.target[i].employee_id);
					name_a.push(result.target[i].name);
					key.push(result.target[i].key || 'Not Found');
					eff.push(result.target[i].eff * 100);


					if(eff[loop-1] > parseInt(target)){
						data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(144,238,126)'});
					}else{
						data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(255,116,116)'})
					}

					if(eff[loop-1] < parseInt(target)){
						if(result.target[i].check != null){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(25,118,210 ,.3)'});
						}else{
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});
						}
					}
				}			
			}
			var chart = Highcharts.chart('container4_shifta', {
				chart: {
					animation: false
				},
				title: {
					text: 'Last Operators Efficiency Less '+target+'%',
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Group A on '+ result.date,
					style: {
						fontSize: '1vw',
						fontWeight: 'bold'
					}
				},
				yAxis: {
					title: {
						enabled: true,
						text: "Efficiency"
					},
					labels: {
						enabled: false
					},
					max: 300
				},
				xAxis:  {
					categories: key,
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						rotation: -45,
						style: {
							fontSize: '13px'
						}
					},
					plotBands: plotBands
				},
				tooltip: {
					headerFormat: '<span>{point.category}</span><br/>',
					pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
				},
				credits: {
					enabled:false
				},
				legend : {
					enabled:false
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
									showCheck(op_a[event.point.index], name_a[event.point.index], event.point.category, result.date);
								}
							}
						},
					},
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: data,
					showInLegend: false
				}]

			});



			var op_b = [];
			var name_b = [];
			var key = [];
			var eff = [];
			var data = [];
			var loop = 0;
			var plotBands = [];
			for(var i = 0; i < result.target.length; i++){
				if(result.target[i].group == 'B'){
					loop += 1;

					op_b.push(result.target[i].employee_id);
					name_b.push(result.target[i].name);
					key.push(result.target[i].key || 'Not Found');
					eff.push(result.target[i].eff * 100);


					if(eff[loop-1] > parseInt(target)){
						data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(144,238,126)'});
					}else{
						data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(255,116,116)'})
					}

					if(eff[loop-1] < parseInt(target)){
						if(result.target[i].check != null){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(25,118,210 ,.3)'});
						}else{
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});
						}
					}
				}			
			}

			var chart = Highcharts.chart('container4_shiftb', {
				chart: {
					animation: false
				},
				title: {
					text: 'Last Operators Efficiency Less '+target+'%',
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Group B on '+ result.date,
					style: {
						fontSize: '1vw',
						fontWeight: 'bold'
					}
				},
				yAxis: {
					title: {
						enabled: true,
						text: "Efficiency"
					},
					labels: {
						enabled: false
					},
					max: 300
				},
				xAxis:  {
					categories: key,
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						rotation: -45,
						style: {
							fontSize: '13px'
						}
					},
					plotBands: plotBands
				},
				tooltip: {
					headerFormat: '<span>{point.category}</span><br/>',
					pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
				},
				credits: {
					enabled:false
				},
				legend : {
					enabled:false
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
									showCheck(op_b[event.point.index], name_b[event.point.index], event.point.category, result.date);
								}
							}
						},
					},
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: data,
					showInLegend: false
				}]
			});

			$(document).scrollTop(position);

		}
	});

}

function checkEff() {
	var m_operator = $("#m_operator").val();
	var employee_id = $("#employee_id").val();
	var name = $("#name").val();
	var key = $("#key").val();
	var date = $("#date").val();

	var data = {
		m_operator: m_operator,
		employee_id: employee_id,
		name: name,
		key: key,
		date: date,
	}

	$.post('{{ url("update/welding/welding_op_eff_check") }}', data, function(result, status, xhr) {
		if(result.status){
			openSuccessGritter('Success!', result.message);
			$('#check-modal').modal('hide');
		}else{
			openErrorGritter('Error!', result.message);
			$('#check-modal').modal('hide');
		}

	});
}

$('#check-modal').on('shown.bs.modal', function () {
	$('#input_tag').focus();
});

$('#input_tag').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($("#input_tag").val().length == 10){
			var data = {
				employee_id : $("#input_tag").val()
			}

			$.get('{{ url("scan/welding/operator/rfid") }}', data, function(result, status, xhr){
				if(result.status){
					var employee_id = $("#employee_id").val();
					if(employee_id == result.employee.operator_nik){
						$("#m_operator").val(result.employee.operator_id);
						showData();
					}else{
						audio_error.play();
						openErrorGritter('Error', 'Tag OP Wrong');
						$('#input_tag').val('');
					}
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#input_tag').val('');
				}
			});
		}
		else{
			openErrorGritter('Error!', 'Tag Invalid.');
			audio_error.play();
			$("#operator").val("");
		}			
	}
});

function showData() {
	$('#field-nik').show();
	$('#field-name').show();
	$('#field-key').show();
	$('#btn-check').show();
}


function showCheck(nik, nama, kunci, tgl) {

	document.getElementById("employee_id").value = nik;
	document.getElementById("name").value = nama;
	document.getElementById("key").value = kunci;
	document.getElementById("date").value = tgl;

	$('#field-nik').hide();
	$('#field-name').hide();
	$('#field-key').hide();
	$('#btn-check').hide();

	$('#check-modal').modal('show');
	$('#input_tag').val("");
	$('#input_tag').focus();

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