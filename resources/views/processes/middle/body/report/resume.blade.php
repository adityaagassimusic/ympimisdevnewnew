@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		background-color: rgba(126,86,134,.7);
		text-align: center;
		vertical-align: middle;
		color: black;
		font-size: 1vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 1vw;
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
		color: black;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
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
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="bulan" placeholder="Select Month">
						</div>
					</div>
					<div class="col-xs-2" style="color:black;">
						<div class="form-group">
							<select class="form-control select2" id="fy" data-placeholder="Select Fiscal Year" >
								<option value=""></option>
								@foreach($fys as $fy)
								<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" onclick="drawChart()">Search</button>
						</div>
					</div>
			</div>
			
			<div class="col-xs-12" style="padding: 0px">
				<div class="col-xs-12" style="padding: 0px">
					<div class="nav-tabs-custom" id="tab_1">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly_ic" class="table table-bordered" style="margin:0">
												<thead id="head_monthly_ic">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_monthly_ic">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_ic_1" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_ic_weekly" class="table table-bordered" style="margin:0">
												<thead id="head_ic_weekly">
													<tr style="background-color: rgba(126,86,134,.7);">
														<th style="padding: 0px;">Week</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">%NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_ic_weekly">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_ic_2" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_3_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_3_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_4_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_ic_4_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div id="chart_ic_5" style="width: 99%;"></div>
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_monthly_kensa" class="table table-bordered" style="margin:0">
												<thead id="head_monthly_kensa">
													<tr>
														<th style="padding: 0px;">Month</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Target</th>
														<th style="padding: 0px;">NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_monthly_kensa">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_kensa_1" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="row">
									<div class="col-xs-12">
										<div class="col-xs-3">
											<table id="table_kensa_weekly" class="table table-bordered" style="margin:0">
												<thead id="head_kensa_weekly">
													<tr style="background-color: rgba(126,86,134,.7);">
														<th style="padding: 0px;">Week</th>
														<th style="padding: 0px;">Total Check</th>
														<th style="padding: 0px;">Total NG</th>
														<th style="padding: 0px;">%NG Rate</th>
													</tr>
												</thead>
												<tbody id="body_kensa_weekly">
												</tbody>
											</table>
										</div>
										<div class="col-xs-9">
											<div id="chart_kensa_2" style="width: 99%;"></div>			
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_2_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_2_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12" style="padding:0px;">
							<div class="col-xs-6" style="padding-right: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_3_alto" style="width: 99%;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0.5%;">
								<div class="nav-tabs-custom">
									<div class="tab-content">
										<div class="tab-pane active">
											<div id="chart_kensa_3_tenor" style="width: 99%;"></div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>

					<div class="nav-tabs-custom">
						<div class="tab-content">
							<div class="tab-pane active">
								<div id="chart_kensa_4" style="width: 99%;"></div>
							</div>
						</div>
					</div>


				</div>
			</div>		
		</div>
	</div>

</section>


@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			allowClear:true
		});

		drawChart();
		setInterval(drawChart, 60*60*1000);
	});

	function change() {
		$("#fy").val($("#fySelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('m-Y') ?>
		format: "mm-yyyy",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		endDate: '<?php echo $tgl_max ?>'

	});

	function bulanText(param){
		var bulan = parseInt(param.slice(0, 2));
		var tahun = param.slice(3, 8);
		var bulanText = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

		return bulanText[bulan-1]+" "+tahun;
	}


	function drawChart(){
		var data = {
			bulan:$('#bulan').val(),
			fy:$('#fy').val(),
			location:'{{$location}}'
		}
		$.get('{{ url("fetch/body/resume") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#body_monthly_ic').append().empty();
					var fy = result.fy;

					var month =  [];
					var target = [];
					var ng = [];
					var ng_rate_monthly = [];

					for (var i = 0; i < result.resume_monthly.length; i++) {
						target.push(13.5);						
						month.push(result.resume_monthly[i].months);
						ng.push(result.resume_monthly[i].ng_rate);
						ng[i] = ng[i] || 0;
						ng_rate_monthly.push(ng[i] * 100);
					}

					var body = "";
					for (var i = 0; i < result.resume_monthly.length; i++) {
						body += "<tr>";
						body += "<td>"+month[i]+"</td>";
						body += "<td>"+result.resume_monthly[i].ng+"</td>";
						body += "<td>"+result.resume_monthly[i].check+"</td>";
						body += "<td>"+target[i]+"%</td>";
						body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_monthly_ic').append(body);

					Highcharts.chart('chart_ic_1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">NG Rate I.C. Lacquering Sax Body on '+fy+'</span>',
							useHTML: true
						},
						xAxis: {
							categories: month
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							min: 0
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',

						},
						plotOptions: {
							column: {
								cursor: 'pointer',
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									formatter: function () {
										return Highcharts.numberFormat(this.y,2)+'%';
									}
								}
							},
							line: {
								marker: {
									enabled: false
								},
								dashStyle: 'ShortDash'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'NG Rate',
							data: ng_rate_monthly
						},
						{
							name: 'Target',
							type: 'line',
							data: target,
							color: '#FF0000',
						}
						]
					});


					// $('#body_monthly_kensa').append().empty();
					// var month =  [];
					// var target = [];
					// var ng = [];
					// var ng_rate_monthly = [];

					// for (var i = 0; i < result.monthly_kensa.length; i++) {
					// 	target.push(5.9);
					// 	month.push(result.monthly_kensa[i].tgl);
					// 	ng.push(result.monthly_kensa[i].ng_rate);
					// 	ng[i] = ng[i] || 0;
					// 	ng_rate_monthly.push(ng[i] * 100);
					// }

					// var body = "";
					// for (var i = 0; i < result.monthly_kensa.length; i++) {
					// 	body += "<tr>";
					// 	body += "<td>"+month[i]+"</td>";
					// 	body += "<td>"+result.monthly_kensa[i].ng+"</td>";
					// 	body += "<td>"+result.monthly_kensa[i].g+"</td>";
					// 	body += "<td>"+target[i]+"%</td>";
					// 	body += "<td>"+ng_rate_monthly[i].toFixed(2)+"%</td>";
					// 	body += "</tr>";
					// }
					// $('#body_monthly_kensa').append(body);

					// Highcharts.chart('chart_kensa_1', {
					// 	chart: {
					// 		type: 'column'
					// 	},
					// 	title: {
					// 		text: '<span style="font-size: 18pt;">NG Rate Kensa Lacquering Sax Key on '+fy+'</span>',
					// 		useHTML: true
					// 	},
					// 	xAxis: {
					// 		categories: month
					// 	},
					// 	yAxis: {
					// 		title: {
					// 			text: 'NG Rate (%)'
					// 		},
					// 		min: 0
					// 	},
					// 	legend : {
					// 		enabled: false
					// 	},
					// 	tooltip: {
					// 		headerFormat: '<span>{point.category}</span><br/>',
					// 		pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',

					// 	},
					// 	plotOptions: {
					// 		column: {
					// 			cursor: 'pointer',
					// 			borderWidth: 0,
					// 			dataLabels: {
					// 				enabled: true,
					// 				formatter: function () {
					// 					return Highcharts.numberFormat(this.y,2)+'%';
					// 				}
					// 			}
					// 		},
					// 		line: {
					// 			marker: {
					// 				enabled: false
					// 			},
					// 			dashStyle: 'ShortDash'
					// 		}
					// 	},credits: {
					// 		enabled: false
					// 	},
					// 	series: [
					// 	{
					// 		name: 'NG Rate',
					// 		data: ng_rate_monthly
					// 	},
					// 	{
					// 		name: 'Target',
					// 		type: 'line',
					// 		data: target,
					// 		color: '#FF0000',
					// 	}
					// 	]
					// });

				}
			}
		});

}

</script>


@stop