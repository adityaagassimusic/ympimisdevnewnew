@extends('layouts.display')
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
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
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


	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}

	#tableCode > tbody > tr > td:hover{
		background-color: #7dfa8c !important;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif						
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px">
			<!-- <div class="col-xs-2" style="padding-left: 0px;padding-right: 5px">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Expired Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Expired Date To">
				</div>
			</div> -->
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
				<select class="form-control select2" name="fiscal_year" id="fiscal_year" data-placeholder="Pilih Fiscal Year" style="width: 100%;">
					<option></option>
					@foreach($fy_all as $fy_all)
					<option value="{{$fy_all->fiscal_year}}">{{$fy_all->fiscal_year}}</option>
					@endforeach
				</select>
			</div>
			<!-- <div class="col-md-2" style="padding-left: 5px;padding-right: 5px">
				<div class="form-group">
					<select class="form-control select2" name="status" id="status" data-placeholder="Pilih Status" style="width: 100%;">
						<option></option>
						<option value="1">Active</option>
						<option value="3">Expired</option>
						<option value="2">Renewal</option>
					</select>
				</div>
			</div> -->
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)">
					Search <small>検索</small>
				</button>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">
			<!-- <div class="box box-solid" style="height: 35vh">
				<div class="box-body">
					<center style="background-color: #605ca8;color: white"><h4 style="font-weight: bold;padding: 5px;margin-top: 0px">Filter</h4></center>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Kode Sertifikat</span>
							<div class="form-group">
								
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Status</span>
							<div class="form-group">
								<select class="form-control select2" name="status" id="status" data-placeholder="Pilih Status" style="width: 100%;">
									<option></option>
									<option value="1">Active</option>
									<option value="0">Inactive</option>
									<option value="3">Expired</option>
									<option value="2">Renewal</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/certificate') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/certificate/code') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> -->
			<div id="container" style="height: 50vh;">
				
			</div>

		</div>
		<div class="col-xs-12" style="margin-top: 0px;padding-top: 0px">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableCode" class="table table-bordered table-hover" style="margin-bottom: 0;">
								<thead style="background-color: #605ca8;color: white" id="headTableCode">
									<!-- <tr> -->
										<!-- <th width="1%">#</th>
										<th width="5%">Certificate No.<br><small>認定番号</small></th>
										<th width="5%">Desc<br><small>内容</small></th>
										<th width="5%">From<br><small>有効日付</small></th>
										<th width="5%">To<br><small>無効日付</small></th>
										<th width="5%">Emp<br><small>従業員</small></th>
										<th width="1%">Status<br><small>ステイタス</small></th>
										<th width="1%">Leader QA<br><small>品質保証リーダー</small></th>
										<th width="1%">Staff QA<br><small>品質保証スタッフ</small></th>
										<th width="1%">Foreman QA<br><small>品質保証工長</small></th>
										<th width="1%">Chief QA<br><small>組立工長</small></th>
										<th width="1%">Foreman Assy<br><small>組立工長</small></th>
										<th width="1%">Manager Assy<br><small>組立課長</small></th>
										<th width="1%">Manager QA<br><small>品質保証課長</small></th>
										<th width="1%">DGM<br><small>副部長</small></th>
										<th width="1%">GM<br><small>部長</small></th>
										<th width="1%">President Director<br><small>社長</small></th>
										<th width="5%">Action<br><small>アクション</small></th> -->
									<!-- </tr> -->
								</thead>
								<tbody id="bodyTableCode">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="background-color: skyblue">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul"></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12" id="div_resume" style="overflow-x: scroll;">
              	<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
	              <thead>
	              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
	                <th style="width:1%;text-align: center;">#</th>
					<th style="width:1%">Certificate ID</th>
					<th style="width:2%">Code</th>
					<th style="width:2%">Desc</th>
					<th style="width:3%">Emp</th>
					<th style="width:1%">From</th>
					<th style="width:1%">To</th>
					<th style="width:3%">Auditor</th>
					<th style="width:3%">Staff</th>
					<th style="width:3%">PDF</th>
	              </tr>
	              </thead>
	              <tbody id="body-detail">
	                
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



</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	var certificate_all = null;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		certificate_all = null;
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fillList(){
		$('#loading').show();
		var data = {
			// tanggal_from:$('#tanggal_from').val(),
			// tanggal_to:$('#tanggal_to').val(),
			// status:$('#status').val(),
			fiscal_year:$('#fiscal_year').val(),
		}
		$.get('{{ url("fetch/qa/certificate/schedule") }}',data, function(result, status, xhr){
			if(result.status){
				
				var month_from = [];
				var month_to = [];
				var certificate_name = [];
				var month_all = [];
				certificate_all = result.schedules;
				for(var i = 0; i < result.schedules.length;i++){
					month_from.push(result.schedules[i].periode_from);
					month_to.push(result.schedules[i].periode_to);
					certificate_name.push(result.schedules[i].certificate_name);
					month_all.push(result.schedules[i].periode_from);
					month_all.push(result.schedules[i].periode_to);
				}

				var month_from_unik = month_from.filter(onlyUnique);

				var certificate_name_unik = certificate_name.filter(onlyUnique);
				var month_all_unik = month_all.filter(onlyUnique);

				var qty_from = [];
				var qty_from_all = [];

				for(var i = 0; i < month_from_unik.length;i++){
					var qty = 0;
					for(var j = 0; j < result.schedules.length;j++){
						if (result.schedules[j].periode_from == month_from_unik[i]) {
							qty++;
						}
					}
					qty_from.push({month:month_from_unik[i],qty:qty});
				}
				for(var i = 0; i < month_from_unik.length;i++){
					for(var k = 0; k < certificate_name_unik.length;k++){
						var qty_all = 0;
						for(var j = 0; j < result.schedules.length;j++){
							if (result.schedules[j].periode_from == month_from_unik[i] && result.schedules[j].certificate_name == certificate_name_unik[k]) {
								qty_all++;
							}
						}
						qty_from_all.push({certificate_name:certificate_name_unik[k],month:month_from_unik[i],qty:qty_all});
					}
				}

				var month_to_unik = month_to.filter(onlyUnique);

				var qty_to = [];
				var qty_to_all = [];

				for(var i = 0; i < month_to_unik.length;i++){
					var qty = 0;
					for(var j = 0; j < result.schedules.length;j++){
						if (result.schedules[j].periode_to == month_to_unik[i]) {
							qty++;
						}
					}
					qty_to.push({month:month_to_unik[i],qty:qty});
				}
				for(var i = 0; i < month_to_unik.length;i++){
					for(var k = 0; k < certificate_name_unik.length;k++){
						var qty_all = 0;
						for(var j = 0; j < result.schedules.length;j++){
							if (result.schedules[j].periode_to == month_to_unik[i] && result.schedules[j].certificate_name == certificate_name_unik[k]) {
								qty_all++;
							}
						}
						qty_to_all.push({certificate_name:certificate_name_unik[k],month:month_to_unik[i],qty:qty_all});
					}
				}

				var category = [];
				var renewals = [];
				var news = [];
				for (var i = 0; i < result.fy.length;i++) {
					category.push(result.fy[i].month_name);
					var newes = 0;
					for(var j = 0; j < qty_from.length;j++){
						if (qty_from[j].month == result.fy[i].months) {
							newes = qty_from[j].qty;
						}
					}
					// renewals.push(parseInt(result.renewals[i].length));
					news.push({y:parseInt(newes),key:result.fy[i].months});

					var renewal = 0;
					for(var j = 0; j < qty_to.length;j++){
						if (qty_to[j].month == result.fy[i].months) {
							renewal = qty_to[j].qty;
						}
					}
					renewals.push({y:parseInt(renewal),key:result.fy[i].months});
				}

				const chart = new Highcharts.Chart({
				    chart: {
				        renderTo: 'container',
				        type: 'column',
				        backgroundColor:'none',
				        options3d: {
				            enabled: true,
				            alpha: 0,
				            beta: 0,
				            depth: 50,
				            viewDistance: 25
				        }
				    },
				    xAxis: {
						categories: category,
						type: 'category',
						gridLineWidth: 0,
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
							text: 'Total Data <small>トータルデータ</small>',
							style: {
								color: '#eee',
								fontSize: '12px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						allowDecimals: false,
						labels:{
							style:{
								fontSize:"12px"
							}
						},
						type: 'linear',
						// opposite: true
					}
					],
					// tooltip: {
					// 	headerFormat: '<span>{series.name}</span><br/>',
					// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					// },
					legend: {
						layout: 'horizontal',
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#1c1c1c',
						itemStyle: {
							fontSize:'12px',
						},
						reversed : true
					},	
				    title: {
				        text: '<b>QA CERTIFICATE SCHEDULE</b>',
						// style:{
						// 	fontSize:"12px"
						// }
				    },
				    subtitle: {
				        text: 'QA検査認定証スケジュール監視'
				    },
				    plotOptions: {
				        series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										showModal(this.category,this.series.name,this.options.key);
									}
								}
							},
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '0.9vw'
								}
							},
							animation: false,
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
						data: renewals,
						name: 'Schedule',
						colorByPoint: false,
						color:'#f44336'
					}
					,
					{
						type: 'column',
						data: news,
						name: 'Done',
						colorByPoint: false,
						color:'#32a852'
					}
					]
				});

				// var certificate_all = [];

				// for(var i = 0; i < certificate_name_unik.length;i++){
				// 	for(var k = 0; k < month_all_unik.length;k++){
				// 		var plan = 0;
				// 		var actual = 0;
				// 		// for(var j = 0; j < qty_from.length;j++){
				// 		// 	if (qty_from[j].month == month_all_unik[k]) {
				// 		// 		actual = qty_from[j].qty;
				// 		// 	}
				// 		// }
				// 		// for(var j = 0; j < qty_to.length;j++){
				// 		// 	if (qty_to[j].month == month_all_unik[k]) {
				// 		// 		renewal = qty_to[j].qty;
				// 		// 	}
				// 		// }
				// 		for(var j = 0; j < result.schedules.length;j++){
				// 			if (result.schedules[j].month == month_all_unik[k] && result.schedules[j].certificate_name == certificate_name_unik[i]) {
				// 				plan++;
				// 			}
				// 		}
				// 		certificate_all.push({certificate_name:certificate_name_unik[i],month:month_all_unik[k],plan:plan});
				// 	}
				// }

				// console.table(certificate_all);

				$('#headTableCode').html("");
				var headTableData = '';

				headTableData += '<tr>';
				headTableData += '<th>Bagian</th>';
				headTableData += '<th>Ket</th>';
				for(var i = 0; i < result.fy.length;i++){
					headTableData += '<th>'+result.fy[i].month_name+'</th>';
				}

				headTableData += '</tr>';

				$("#headTableCode").append(headTableData);

				$('#bodyTableCode').html("");
				var tableData = "";

				for(var i = 0; i < certificate_name_unik.length;i++){
					tableData += '<tr>';
					tableData += '<td rowspan="2" style="padding:5px !important;text-align:left">'+certificate_name_unik[i]+'</td>';
					tableData += '<td style="padding:5px !important;text-align:left">Plan</td>';
					for(var j = 0; j < result.fy.length;j++){
						var actual = 0;
						var plan = 0;
						for(var k = 0; k < qty_from_all.length;k++){
							if (qty_from_all[k].month == result.fy[j].months && qty_from_all[k].certificate_name == certificate_name_unik[i]) {
								actual = qty_from_all[k].qty;
							}
						}
						for(var k = 0; k < qty_to_all.length;k++){
							if (qty_to_all[k].month == result.fy[j].months && qty_to_all[k].certificate_name == certificate_name_unik[i]) {
								plan = qty_to_all[k].qty;
							}
						}
						var plans = plan+actual;
						if (plans == 0) {
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right"></td>';
						}else{
							if (actual == 0) {
								var statuses = 'Schedule';
							}else{
								var statuses = 'Done';
							}
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right;background-color:rgb(255, 204, 255);cursor:pointer;" onclick="showModalTable(\''+result.fy[j].month_name+'\',\''+statuses+'\',\''+result.fy[j].months+'\',\''+certificate_name_unik[i]+'\')">'+plans+'</td>';
						}
					}
					tableData += '</tr>';

					tableData += '<tr>';
					tableData += '<td style="padding:5px !important;text-align:left">Actual</td>';
					for(var j = 0; j < result.fy.length;j++){
						var actual = 0;
						for(var k = 0; k < qty_from_all.length;k++){
							if (qty_from_all[k].month == result.fy[j].months && qty_from_all[k].certificate_name == certificate_name_unik[i]) {
								actual = qty_from_all[k].qty;
							}
						}
						if (actual == 0) {
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right"></td>';
						}else{
							var statuses = 'Done';
							tableData += '<td class="tdhover" style="padding:5px !important;text-align:right;background-color:rgb(204, 255, 215);cursor:pointer;" onclick="showModalTable(\''+result.fy[j].month_name+'\',\''+statuses+'\',\''+result.fy[j].months+'\',\''+certificate_name_unik[i]+'\')">'+actual+'</td>';
						}
					}
					tableData += '</tr>';
				}

				// for(var i = 0; i < result.periode.length;i++){
				// 	tableData += '<tr>';
				// 	tableData += '<td rowspan="2" style="padding:5px !important;text-align:left">'+result.periode[i].description+'</td>';
				// 	tableData += '<td style="padding:5px !important;text-align:left">Plan</td>';
				// 	for (var j = 0; j < result.fy.length;j++) {
				// 		var plans = 0;
				// 		// if (result.periode_all[j].months.split('-')[1] == result.periode[i].periode) {
				// 		// 	tableData += '<td style="padding:5px !important;text-align:left">'+result.renewals[j].length+'</td>';
				// 		// }else{
				// 		// 	tableData += '<td style="padding:5px !important;text-align:left"></td>';
				// 		// }
				// 		for(var k = 0; k < result.renewals.length;k++){
				// 			if (result.renewals[k].length > 0) {
				// 				for(var l = 0; l < result.renewals[k].length;l++){
				// 					if (result.renewals[k][l].months == result.fy[j].months && result.periode[i].description == result.renewals[k][l].description) {
				// 						// tableData += '<td style="padding:5px !important;text-align:left">'+parseInt(result.renewals[k].length)+'</td>';
				// 						// plans = result.renewals[k].length;
				// 						plans++;
				// 					}
				// 				}
				// 			}
				// 		}
				// 		if (plans == 0) {
				// 			tableData += '<td class="tdhover" style="padding:5px !important;text-align:right"></td>';
				// 		}else{
				// 			tableData += '<td class="tdhover" style="padding:5px !important;text-align:right;background-color:rgb(255, 204, 255)">'+plans+'</td>';
				// 		}
				// 	}
				// 	tableData += '</tr>';
				// 	tableData += '<tr>';
				// 	tableData += '<td style="padding:5px !important;text-align:left">Actual</td>';
				// 	for (var j = 0; j < result.fy.length;j++) {
				// 		var actuals = 0;
				// 		for(var k = 0; k < result.news.length;k++){
				// 			for(var l = 0; l < result.news[k].length;l++){
				// 					if (result.news[k][l].months == result.fy[j].months && result.periode[i].description == result.news[k][l].description) {
				// 						// tableData += '<td style="padding:5px !important;text-align:left">'+parseInt(result.renewals[k].length)+'</td>';
				// 						// plans = result.renewals[k].length;
				// 						actuals++;
				// 					}
				// 				}
				// 		}
				// 		if (actuals == 0) {
				// 			tableData += '<td class="tdhover" style="padding:5px !important;text-align:right"></td>';
				// 		}else{
				// 			tableData += '<td class="tdhover" style="padding:5px !important;text-align:right;background-color:rgb(204, 255, 215)">'+actuals+'</td>';
				// 		}
				// 	}
				// 	tableData += '</tr>';
				// }

				$('#bodyTableCode').append(tableData);

				$('#loading').hide();

			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!','Attempt to retrieve data failed');
			}
		});
	}

	function showModal(month_name,statuses,month) {

		$("#data-log").DataTable().clear();
		$("#data-log").DataTable().destroy();
		$('#body-detail').html('');
		var bodyDetail = '';

		var index = 1;
		for(var i = 0; i < certificate_all.length;i++){
			if (statuses == 'Done') {
				if (certificate_all[i].periode_from == month) {
					bodyDetail += '<tr>';
					bodyDetail += '<td style="background-color: #f0f0ff;text-align:center">'+index+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_id+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_code+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].employee_id+' - '+certificate_all[i].name+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_from+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_to+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].auditor_id+' - '+certificate_all[i].auditor_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].staff_id+' - '+certificate_all[i].staff_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">';
					if (certificate_all[i].certificate_code.split('-')[2] == 'I') {
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}else{
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/inprocess/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}
					bodyDetail += '</td>';
					bodyDetail += '</tr>';
					index++;
				}
			}

			if (statuses == 'Schedule') {
				if (certificate_all[i].periode_to == month) {
					bodyDetail += '<tr>';
					bodyDetail += '<td style="background-color: #f0f0ff;text-align:center">'+index+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_id+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_code+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].employee_id+' - '+certificate_all[i].name+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_to+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_to+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].auditor_id+' - '+certificate_all[i].auditor_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].staff_id+' - '+certificate_all[i].staff_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">';
					if (certificate_all[i].certificate_code.split('-')[2] == 'I') {
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}else{
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/inprocess/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}
					bodyDetail += '</td>';
					bodyDetail += '</tr>';
					index++;
				}
			}
		}
		$('#body-detail').append(bodyDetail);

		var table = $('#data-log').DataTable({
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
                extend: 'copy',
                className: 'btn btn-success',
                text: '<i class="fa fa-copy"></i> Copy',
                  exportOptions: {
                    columns: ':not(.notexport)'
                }
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
            'searching': true ,
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
          });

		if (statuses == 'Done') {
			$('#judul').html('Sertifikat yang Sudah Diterbitkan pada '+month_name);
		}else{
			$('#judul').html('Sertifikat yang Belum Diterbitkan pada '+month_name);
		}
		$('#modalDetail').modal('show');
	}

	function showModalTable(month_name,statuses,month,certificate_name) {

		$("#data-log").DataTable().clear();
		$("#data-log").DataTable().destroy();
		$('#body-detail').html('');
		var bodyDetail = '';

		var index = 1;
		for(var i = 0; i < certificate_all.length;i++){
			if (statuses == 'Done') {
				if (certificate_all[i].periode_from == month && certificate_all[i].certificate_name == certificate_name) {
					bodyDetail += '<tr>';
					bodyDetail += '<td style="background-color: #f0f0ff;text-align:center">'+index+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_id+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_code+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].employee_id+' - '+certificate_all[i].name+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_from+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_to+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].auditor_id+' - '+certificate_all[i].auditor_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].staff_id+' - '+certificate_all[i].staff_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">';
					if (certificate_all[i].certificate_code.split('-')[2] == 'I') {
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}else{
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/inprocess/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}
					bodyDetail += '</td>';
					bodyDetail += '</tr>';
					index++;
				}
			}

			if (statuses == 'Schedule') {
				if (certificate_all[i].periode_to == month && certificate_all[i].certificate_name == certificate_name) {
					bodyDetail += '<tr>';
					bodyDetail += '<td style="background-color: #f0f0ff;text-align:center">'+index+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_id+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_code+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].certificate_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].employee_id+' - '+certificate_all[i].name+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_to+'</td>';
					bodyDetail += '<td style="text-align:right;padding-right:10px !important;background-color: #f0f0ff">'+certificate_all[i].periode_to+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].auditor_id+' - '+certificate_all[i].auditor_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">'+certificate_all[i].staff_id+' - '+certificate_all[i].staff_name+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:10px !important;background-color: #f0f0ff">';
					if (certificate_all[i].certificate_code.split('-')[2] == 'I') {
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}else{
						bodyDetail += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/inprocess/")}}/'+certificate_all[i].certificate_id+'">Detail <small>詳細</small></a>';
					}
					bodyDetail += '</td>';
					bodyDetail += '</tr>';
					index++;
				}
			}
		}
		$('#body-detail').append(bodyDetail);

		var table = $('#data-log').DataTable({
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
                extend: 'copy',
                className: 'btn btn-success',
                text: '<i class="fa fa-copy"></i> Copy',
                  exportOptions: {
                    columns: ':not(.notexport)'
                }
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
            'searching': true ,
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
          });

		if (statuses == 'Done') {
			$('#judul').html('Sertifikat '+certificate_name+' yang Sudah Diterbitkan pada '+month_name);
		}else{
			$('#judul').html('Sertifikat '+certificate_name+' yang Belum Diterbitkan pada '+month_name);
		}
		$('#modalDetail').modal('show');
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