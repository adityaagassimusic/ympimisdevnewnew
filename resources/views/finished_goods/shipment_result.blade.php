@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
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
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Finished Goods And KD Parts Achievement For Shipment <span class="text-purple">出荷用の完成品生産高</span>
		<small>Based on ETD YMPI <span class="text-purple">YMPIのETDベース</span></small>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					<div class="col-md-3 col-md-offset-3">
						<div class="form-group">
							<label>Export Date From</label>
							<div class="input-group date">
								<div class="input-group-addon" style="background-color: rgba(126,86,134,.7);">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="datefrom" nama="datefrom">
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Export Date To</label>
							<div class="input-group date">
								<div class="input-group-addon" style="background-color: rgba(126,86,134,.7);">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="dateto" nama="dateto">
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group pull-right">
								<button id="search" onClick="fillChart()" class="btn btn-primary bg-purple">Update Chart</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<center>
								<div style="background-color: rgba(248,161,63,0.9); font-size: 2vw; font-weight: bold;">Finished Goods</div>
								<div id="container" style="width:100%; height:450px;"></div>
								<div style="background-color: rgba(248,161,63,0.9); font-size: 2vw; font-weight: bold;">KD Parts</div>
								<div id="container2" style="width:100%; height:650px;"></div>
								<span class="text-red">Blank = No Export Plan</span><br>
								<span class="text-red">ブランク = 輸出計画はありません</span><br>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalResult">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalResultTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableModal">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Material</th>
								<th>Description</th>
								<th>Dest.</th>
								<th>Plan</th>
								<th>Actual</th>
								<th>Diff</th>
							</tr>
						</thead>
						<tbody id="modalResultBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th></th>
							<th id="modalResultTotal1"></th>
							<th id="modalResultTotal2"></th>
							<th id="modalResultTotal3"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
{{-- <script src="{{ url("js/highcharts.js")}}"></script> --}}
<script src="{{ url("js/highstock.js")}}"></script>
{{-- <script src="{{ url("js/annotations.js")}}"></script> --}}
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#datefrom').val("");
		$('#dateto').val("");

		fillChart();
		setInterval(function(){
			fillChart();
		}, 1000*60*5);
	});

	// var interval;
	// var statusx = "idle";

	// $(document).on('mousemove keyup keypress',function(){
	// 	clearTimeout(interval);
	// 	settimeout();
	// 	statusx = "active";
	// })

	// function settimeout(){
	// 	interval=setTimeout(function(){
	// 		statusx = "idle";
	// 		fillChart()
	// 	},60000)
	// }

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate(){
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillChart(){
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var data = {
			datefrom:datefrom,
			dateto:dateto
		};
		$.get('{{ url("fetch/fg_shipment_result") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					var data = result.shipment_results;
					var seriesData = [];
					var xCategories = [];
					var i, cat;
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					for(i = 0; i < data.length; i++){
						cat = data[i].st_date;
						if(xCategories.indexOf(cat) === -1){
							xCategories[xCategories.length] = cat;
						}
					}

					for(i = 0; i < data.length; i++){
						if(seriesData){
							var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].hpl;});
							if(currSeries.length === 0){
								seriesData[seriesData.length] = currSeries = {name: data[i].hpl, data: []};
							} else {
								currSeries = currSeries[0];
							}
							var index = currSeries.data.length;
							currSeries.data[index] = data[i].actual;
						} else {
							seriesData[0] = {name: data[i].hpl, data: [data[i].actual]}
						}
					}

					if(xCategories.length <= 5){
						var scrollMax = xCategories.length-1;
					}
					else{
						var scrollMax = 4;
					}
					var yAxisLabels = [0,25,50,75,100,145];
					Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: null
						},
						legend:{
							enabled: false
						},
						xAxis: {
							categories: xCategories,
							type: 'category',
							gridLineWidth: 5,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '30px',
									color: 'rgba(75, 30, 120)',
								}
							},
							min: 0,
							max:scrollMax,
							scrollbar: {
								enabled: true,
								barBackgroundColor: 'rgba(126,86,134,.7)',
								barBorderRadius: 7,
								barBorderWidth: 0,
								buttonBackgroundColor: 'rgba(126,86,134,.7)',
								buttonBorderWidth: 0,
								buttonBorderRadius: 7,
								trackBackgroundColor: 'none',
								trackBorderWidth: 1,
								trackBorderRadius: 8,
								trackBorderColor: 'rgba(126,86,134,.7)'
							}
						},
						yAxis: {
							min: 0,
							title: {
								enabled:false,
							},
							tickPositioner: function() {
								return yAxisLabels;
							},
							plotLines: [{
								color: '#FF0000',
								width: 2,
								value: 100,
								label: {
									align:'right',
									text: '100%',
									x:-7,
									style: {
										fontSize: '1vw',
										color: '#FF0000',
										fontWeight: 'bold'
									}
								}
							}],
							labels: {
								enabled:false
							}
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column:{
								size: '95%',
								borderWidth: 0							
							},
							series:{
								animation: false,
								minPointLength: 2,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								shadow: false,
								color: 'rgba(126,86,134,.7)',
								borderColor: '#303030',
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									rotation: -90,
									align: 'left',
									formatter: function() {
										return this.series.name +' '+ this.y +'%';
									},
									y: -5,
									style: {
										fontSize: '1vw',
										color: 'black',
										textOutline: false,
										fontWeight: null,
									}
								},
								point: {
									events: {
										click: function () {
											fillModal(this.category, this.series.name, 'FG');
										}
									}
								}
							}
						},
						tooltip: {
							formatter: function() {
								return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y +'%';
							}
						},
						series: seriesData
					});

				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

$.get('{{ url("fetch/kd_shipment_progress") }}', data, function(result, status, xhr){
	if(xhr.status == 200){
		if(result.status){
			$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
			var data = result.shipment_results;
			var seriesData = [];
			var xCategories = [];
			var i, cat;
			var intVal = function ( i ) {
				return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
				i : 0;
			};
			for(i = 0; i < data.length; i++){
				cat = data[i].st_date;
				if(xCategories.indexOf(cat) === -1){
					xCategories[xCategories.length] = cat;
				}
			}

			for(i = 0; i < data.length; i++){
				if(seriesData){
					var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].hpl;});
					if(currSeries.length === 0){
						seriesData[seriesData.length] = currSeries = {name: data[i].hpl, data: []};
					} else {
						currSeries = currSeries[0];
					}
					var index = currSeries.data.length;
					currSeries.data[index] = data[i].actual;
				} else {
					seriesData[0] = {name: data[i].hpl, data: [data[i].actual]}
				}
			}

			if(xCategories.length <= 5){
				var scrollMax = xCategories.length-1;
			}
			else{
				var scrollMax = 4;
			}
			var yAxisLabels = [0,25,50,75,100,180];
			Highcharts.chart('container2', {
				chart: {
					type: 'column'
				},
				title: {
					text: null
				},
				legend:{
					enabled: false
				},
				xAxis: {
					categories: xCategories,
					type: 'category',
					gridLineWidth: 5,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						style: {
							fontSize: '30px',
							color: 'rgba(75, 30, 120)',
						}
					},
					min: 0,
					max:scrollMax,
					scrollbar: {
						enabled: true,
						barBackgroundColor: 'rgba(126,86,134,.7)',
						barBorderRadius: 7,
						barBorderWidth: 0,
						buttonBackgroundColor: 'rgba(126,86,134,.7)',
						buttonBorderWidth: 0,
						buttonBorderRadius: 7,
						trackBackgroundColor: 'none',
						trackBorderWidth: 1,
						trackBorderRadius: 8,
						trackBorderColor: 'rgba(126,86,134,.7)'
					}
				},
				yAxis: {
					min: 0,
					title: {
						enabled:false,
					},
					tickPositioner: function() {
						return yAxisLabels;
					},
					plotLines: [{
						color: '#FF0000',
						width: 2,
						value: 100,
						label: {
							align:'right',
							text: '100%',
							x:-7,
							style: {
								fontSize: '1vw',
								color: '#FF0000',
								fontWeight: 'bold'
							}
						}
					}],
					labels: {
						enabled:false
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					column:{
						size: '95%',
						borderWidth: 0							
					},
					series:{
						animation: false,
						minPointLength: 2,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						shadow: false,
						color: 'rgba(126,86,134,.7)',
						borderColor: '#303030',
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							rotation: -90,
							align: 'left',
							formatter: function() {
								return this.series.name +' '+ this.y +'%';
							},
							y: -5,
							style: {
								fontSize: '1vw',
								color: 'black',
								textOutline: false,
								fontWeight: null,
							}
						},
						point: {
							events: {
								click: function () {
									fillModal(this.category, this.series.name, 'KD');
								}
							}
						}
					}
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.x +'</b><br/>'+
						this.series.name +': '+ this.y +'%';
					}
				},
				series: seriesData
			});

		}
		else{
			alert('Attempt to retrieve data failed');
		}
	}
	else{
		alert('Disconnected from server');
	}
});
}

function fillModal(date, hpl, prod){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableModal').hide();
	var data = {
		date:date,
		hpl:hpl
	};
	if(prod == 'FG'){
		$.get('{{ url("fetch/tb_shipment_result") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#tableModal').DataTable().destroy();
					$('#modalResultTitle').html('');
					$('#modalResultTitle').html(hpl +' Export Date: '+ date);
					$('#modalResultBody').html('');
					var resultData = '';
					var resultTotal1 = 0;
					var resultTotal2 = 0;
					var resultTotal3 = 0;
					$.each(result.shipment_results, function(key, value) {
						resultData += '<tr>';
						resultData += '<td style="width: 10%">'+ value.material_number +'</td>';
						resultData += '<td style="width: 40%">'+ value.material_description +'</td>';
						resultData += '<td style="width: 5%">'+ value.destination_shortname +'</td>';
						resultData += '<td style="width: 15%">'+ value.plan.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%">'+ value.actual.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%; font-weight: bold;">'+ value.diff.toLocaleString() +'</td>';
						resultData += '</tr>';
						resultTotal1 += value.plan;
						resultTotal2 += value.actual;
						resultTotal3 += value.diff;
					});
					$('#modalResultBody').append(resultData);
					$('#modalResultTotal1').html('');
					$('#modalResultTotal1').append(resultTotal1.toLocaleString());
					$('#modalResultTotal2').html('');
					$('#modalResultTotal2').append(resultTotal2.toLocaleString());
					$('#modalResultTotal3').html('');
					$('#modalResultTotal3').append(resultTotal3.toLocaleString());
					$('#tableModal').DataTable({
						"paging": false,
						'searching': false,
						'order':[],
						'responsive': true,
						'info': false,
						"columnDefs": [{
							"targets": 5,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData.substring(0,1) ==  "-" ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
					});
					$('#loading').hide();
					$('#modalResultTitle').show();
					$('#tableModal').show();
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}
	if(prod == 'KD'){
		$.get('{{ url("fetch/kd_shipment_progress_detail") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#tableModal').DataTable().destroy();
					$('#modalResultTitle').html('');
					$('#modalResultTitle').html(hpl +' Export Date: '+ date);
					$('#modalResultBody').html('');
					var resultData = '';
					var resultTotal1 = 0;
					var resultTotal2 = 0;
					var resultTotal3 = 0;
					$.each(result.shipment_progress, function(key, value) {
						resultData += '<tr>';
						resultData += '<td style="width: 10%">'+ value.material_number +'</td>';
						resultData += '<td style="width: 40%">'+ value.material_description +'</td>';
						resultData += '<td style="width: 5%">'+ value.destination_shortname +'</td>';
						resultData += '<td style="width: 15%">'+ value.plan.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%">'+ value.actual.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%; font-weight: bold;">'+ value.diff.toLocaleString() +'</td>';
						resultData += '</tr>';
						resultTotal1 += value.plan;
						resultTotal2 += value.actual;
						resultTotal3 += value.diff;
					});
					$('#modalResultBody').append(resultData);
					$('#modalResultTotal1').html('');
					$('#modalResultTotal1').append(resultTotal1.toLocaleString());
					$('#modalResultTotal2').html('');
					$('#modalResultTotal2').append(resultTotal2.toLocaleString());
					$('#modalResultTotal3').html('');
					$('#modalResultTotal3').append(resultTotal3.toLocaleString());
					$('#tableModal').DataTable({
						"paging": false,
						'searching': false,
						'order':[],
						'responsive': true,
						'info': false,
						"columnDefs": [{
							"targets": 5,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData.substring(0,1) ==  "-" ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
					});
					$('#loading').hide();
					$('#modalResultTitle').show();
					$('#tableModal').show();
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}

		});
	}
}
</script>
@endsection