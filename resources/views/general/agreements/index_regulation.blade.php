@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
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
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		Law and Agreement System
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<!-- <button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%; background-color: #C3E991; border-color: black;color:black" onclick="newData('new','agreement');"> Perjanjian <i class="fa fa-users"></i></button> -->
		<a class="btn btn-success pull-right" style="margin-left: 5px; width: 15%; background-color: #BDD5EA; border-color: black;color:black" href="{{url('index/general/agreement')}}"> Data Peraturan <i class="fa fa-file-text"></i></a>

		<a class="btn btn-success pull-right" style="margin-left: 5px; width: 20%; background-color: #D4B483; border-color: black;color:black" href="{{url('index/general/regulation')}}"> Log Peraturan <i class="fa fa-book"></i></a>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		
		<div class="col-xs-12">
			<table id="tableRegulation" class="table table-bordered table-striped table-hover">
				<thead style="background-color: #BDD5EA; color: black;">
					<tr>
						<th style="width: 1%; text-align: center;">#</th>
						<th style="width: 1%; text-align: left;">Tanggal Terbit</th>
						<th style="width: 1%; text-align: left;">Peraturan</th>
						<th style="width: 1%; text-align: left;">Judul</th>
						<th style="width: 1%; text-align: center;">Att</th>
						<th style="width: 1%; text-align: left;">PIC Dept</th>
						<th style="width: 1%; text-align: left;">Aturan</th>
						<th style="width: 1%; text-align: left;">Status Peraturan</th>
						<!-- <th style="width: 1%; text-align: right;">Last Update</th> -->
					</tr>
				</thead>
				<tbody id="tableRegulationBody">
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal fade" id="modalDownloadRegulation">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Download Or Preview Attachment</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="downloadId">
					<center>
							<div class="col-md-10">
								<div class="form-group">
									<label>Select File(s) to Download</label>
									<select multiple class="form-control" style="height: 180px;" id="selectDownloadRegulation">
									</select>
								</div>
							</div>
							<div class="col-md-2" style="padding:0">
								<div class="form-group">
									<label>Preview</label>
									<div id="selectPreviewRegulation"></div>
								</div>
							</div>
					</center>
				</div>
				<div class="modal-footer" style="margin-top: 20px;">
					<div class="col-md-12" style="padding:0">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" onclick="downloadAttRegulation()">Download</button>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script> -->
<!-- <script src="{{ url("js/export-data.js")}}"></script> -->
<!-- <script src="{{ url("js/accessibility.js")}}"></script> -->
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%; z-index: 9999;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('.select2').select2();

		fetchData();
		// fetchTable();
	});

	$(function () {

	});

	function activaTab(tab){
	    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
	};

	function getFormattedDate(date) {
	  var year = date.getFullYear().toString().substr(-2);

	  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
		  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
		];

	  var month = date.getMonth();

	  var day = date.getDate().toString();
	  day = day.length > 1 ? day : '0' + day;
	  
	  return day + '-' + monthNames[month] + '-' + year;
}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var departments = [];
	var agreements = [];
	var documents = [];
	var regulations = [];

	function fetchData(){
		// $('#loading').show();
		var data = {

		}
		$.get('{{ url("fetch/general/agreement") }}', data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var categories_regulation = [];
				var tableAgreementBody = "";
				$('#tableAgreement').DataTable().clear();
				$('#tableAgreement').DataTable().destroy();	
				$('#tableAgreementBody').html();
				var series_agreement_safe = [];
				var series_agreement_unsafe = [];
				var series_agreement_expired = [];
				var series_agreement_terminated = [];
				var count_agreement = 0;
				var count_agreement_about = 0;
				var count_agreement_expired = 0;
				agreements = result.agreements;
				departments = result.departments;

				var result_agreements = [];

				agreements.reduce(function(res, value) {
					if (!res[value.department_shortname]) {
						res[value.department_shortname] = { department_shortname: value.department_shortname, count_safe: 0, count_unsafe: 0, count_expired: 0, count_terminated: 0};
						result_agreements.push(res[value.department_shortname]);
					}
					if(value.status == 'Terminated'){
						res[value.department_shortname].count_terminated += 1;
					}
					else if(value.status == 'In Use' && value.validity <= 0){
						res[value.department_shortname].count_expired += 1;
					}
					else if(value.status == 'In Use' && value.validity <= 90){
						res[value.department_shortname].count_unsafe += 1;
					}
					else{
						res[value.department_shortname].count_safe += 1;
					}
					return res;
				}, {});

				for (var i = 0; i < result_agreements.length; i++){
					categories.push(result_agreements[i].department_shortname);
					for (var h = 0; h < departments.length; h++){
							if(result_agreements[i].department_shortname == departments[h].department_shortname){
								series_agreement_safe.push(result_agreements[i].count_safe);
								series_agreement_unsafe.push(result_agreements[i].count_unsafe);
								series_agreement_expired.push(result_agreements[i].count_expired);
								series_agreement_terminated.push(result_agreements[i].count_terminated);
							}
							// else{
							// 	series_agreement_safe.push(0);
							// 	series_agreement_unsafe.push(0);
							// 	series_agreement_expired.push(0);
							// 	series_agreement_terminated.push(0);
							// }
						}
				};

				$.each(result.agreements, function(key, value){
					tableAgreementBody += '<tr>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: center;">'+parseInt(key+1)+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: left;">'+value.vendor+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 8%; text-align: left;">'+value.description+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_from))+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_to))+'</td>';
					if(value.status == 'In Use'){
						count_agreement += 1;
						tableAgreementBody += '<td style="width: 0.1%; text-align: left;">'+value.status+'</td>';
					}
					if(value.status == 'Terminated'){
						tableAgreementBody += '<td style="width: 0.1%; text-align: left; color: white; background-color: black;">Not Used</td>';
					}
					if(value.validity <= 0){
						if(value.status == 'In Use'){
							count_agreement_expired += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #e53935; text-align: right;">'+value.validity+' Day(s)</td>';
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}	
					}
					else if(value.validity <= 30){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #f9a825; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else if(value.validity <= 90){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #ffeb3b; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else{
						tableAgreementBody += '<td style="width: 0.1%; background-color: #aee571; text-align: right;">'+value.validity+' Day(s)</td>';			
					}
					tableAgreementBody += '<td style="width: 3%; text-align: left;">'+(value.remark || "")+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalDownloadAgreement(\''+value.id+'\')">Att('+value.att+')</a></td>';
					// tableAgreementBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href=""><i class="fa fa-paperclip"></i> </a></td>';
					
					tableAgreementBody += '<td style="width: 2%; text-align: left;">'+value.name+'</td>';
					// tableAgreementBody += '<td style="width: 0.1%; text-align: right;">'+value.updated_at+'</td>';
					tableAgreementBody += '</tr>';
				});
				$('#tableAgreementBody').append(tableAgreementBody);
				$('#count_agreement').text(count_agreement);
				$('#count_agreement_about').text(count_agreement_about);
				$('#count_agreement_expired').text(count_agreement_expired);




				$('#tableRegulation').DataTable().clear();
				$('#tableRegulation').DataTable().destroy();	
				$('#tableRegulationBody').html();
				
				var tableRegulationBody = "";
				var series_regulation_implemented = [];
				var series_regulation_not_implemented = [];
				var series_regulation_not_used = [];
				var count_regulation_all = 0;
				var count_regulation_operasional = 0;
				var count_regulation_implemented = 0;
				var count_regulation_not_implemented = 0;

				regulations = result.regulations;

				var result_regulations = [];

				regulations.reduce(function(res, value) {
					if (!res[value.department_shortname]) {
						res[value.department_shortname] = { 
							department_shortname: value.department_shortname, 
							count_implemented: 0, 
							count_not_implemented: 0, 
							count_not_used: 0, 
						};
						result_regulations.push(res[value.department_shortname]);
					}
					if(value.status == 'Sudah Implementasi'){
						res[value.department_shortname].count_implemented += 1;
					}
					else if(value.status == 'Belum Implementasi'){
						res[value.department_shortname].count_not_implemented += 1;
					}
					else if(value.status == null){
						res[value.department_shortname].count_not_used += 1;
					}
					return res;
				}, {});

				for (var i = 0; i < result_regulations.length; i++){
					categories_regulation.push(result_regulations[i].department_shortname);
					for (var h = 0; h < departments.length; h++){
							if(result_regulations[i].department_shortname == departments[h].department_shortname){
								series_regulation_implemented.push(result_regulations[i].count_implemented);
								series_regulation_not_implemented.push(result_regulations[i].count_not_implemented);
								series_regulation_not_used.push(result_regulations[i].count_not_used);
							}
						}
				};

				$.each(result.regulations, function(key, value){
                	tableRegulationBody += '<tr>';
					tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 0.1%; text-align: center;">'+parseInt(key+1)+'</td>';
					tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 1%; text-align: left;">'+(getFormattedDate(new Date(value.valid_from)) || '')+'</td>';
					tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 5%; text-align: left;">'+(value.vendor || '')+'</td>';
					tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 6%; text-align: left;">'+(value.description || '')+'</td>';


					// tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 4%; text-align: left;">'+(value.analisis || '')+'</td>';

					// tableRegulationBody += '<td style="width: 2%; text-align: left;">'+(value.status || '')+'</td>';
					// 
					tableRegulationBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalDownloadRegulation(\''+value.id+'\')">Att('+value.att+')</a></td>';
					tableRegulationBody += '<td style="width: 1%; text-align: left;">'+(value.department_shortname || "")+'</td>';

					if(value.implementation == 'Sudah Dicabut') {
						tableRegulationBody += '<td style="width: 1%; text-align: left;background-color:#e53935;">'+(value.implementation || '')+'</td>';
					}else if (value.implementation == 'Masih Berlaku'){
						tableRegulationBody += '<td style="width: 1%; text-align: left;background-color:#aee571;">'+(value.implementation || '')+'</td>';
					}else{
						tableRegulationBody += '<td style="width: 1%; text-align: left;"></td>';
					}

					if (value.status == 'Belum Implementasi') {
						tableRegulationBody += '<td style="width: 2%; text-align: left;background-color:#e53935">'+(value.status || '')+'</td>';
					}else if(value.status == 'Sudah Implementasi') {
						tableRegulationBody += '<td style="width: 2%; text-align: left;background-color:#aee571">'+(value.status || '')+'</td>';
					}else {
						tableRegulationBody += '<td style="width: 2%; text-align: left;background-color:black;color:white">Not Used</td>';
					}

					// tableRegulationBody += '<td style="width: 0.1%; text-align: right;">'+value.updated_at+'</td>';
					tableRegulationBody += '</tr>';
					
					if(value.status == 'Sudah Implementasi'){
						count_regulation_implemented += 1;
					}else if(value.status == 'Belum Implementasi'){
						count_regulation_not_implemented += 1;
					}

					if(value.remark == 'Peraturan Terkait Operasional'){
						count_regulation_operasional += 1;
					}

					count_regulation_all += 1;

				});
				
				$('#tableRegulationBody').append(tableRegulationBody);

				$('#count_regulation_all').text(count_regulation_all);
				$('#count_regulation_implemented').text(count_regulation_implemented);
				$('#count_regulation_operasional').text(count_regulation_operasional);
				$('#count_regulation_not_implemented').text(count_regulation_not_implemented);

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						// borderRadius: 10,
						// borderColor: 'black',
						// borderWidth: 1
						// options3d: {
				  //           enabled: true,
				  //           alpha: -1,
				  //           beta: 20,
				  //           depth: 50,
				  //           viewDistance: 25
				  //       }
					},
					title: {
						text: 'Perjanjian'
					},
					tooltip: {
						shared: true
					},
					xAxis: {
						categories: categories,
						labels: {
							style: {
								color: 'black',
								fontSize: '10px'
							}
						}
					},
					yAxis: {
						title: {
							text: null
						},
						labels: {
							enabled: false,
							style: {
								color: 'black'
							}
						}
					},
					plotOptions: {
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style: {
									textOutline: 0
								}
							}
						},
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchFilter(this.category, this.series.name);
									}
								}
							}
						}
					},
					exporting: {
						enabled: false
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: true,
						itemStyle: {
							color: 'black'
						}
					},
					series: [{
						name: 'Safe',
						data: series_agreement_safe,
						color: '#aee571'
					},
					{
						name: 'Unsafe',
						data: series_agreement_unsafe,
						color: '#f9a825'
					},
					{
						name: 'Expired',
						data: series_agreement_expired,
						color: '#e53935'
					},
					{
						name: 'Not Used',
						data: series_agreement_terminated,
						color: 'black'
					}]
				});

				Highcharts.chart('container3', {
					chart: {
						type: 'column',
						// options3d: {
				  //           enabled: true,
				  //           alpha: -1,
				  //           beta: 20,
				  //           depth: 50,
				  //           viewDistance: 25
				  //       }
						// borderRadius: 10,
						// borderColor: 'black',
						// borderWidth: 1
					},
					title: {
						text: 'Peraturan'
					},
					tooltip: {
						shared: true
					},
					xAxis: {
						categories: categories_regulation,
						labels: {
							style: {
								color: 'black',
								fontSize: '10px'
							}
						}
					},
					yAxis: {
						title: {
							text: null
						},
						labels: {
							enabled: false,
							style: {
								color: 'black'
							}
						}
					},
					plotOptions: {
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style: {
									textOutline: 0
								}
							}
						},
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchFilterRegulation(this.category, this.series.name);
									}
								}
							}
						}
					},
					exporting: {
						enabled: false
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: true,
						itemStyle: {
							color: 'black'
						}
					},
					series: [{
						name: 'Sudah Implementasi',
						data: series_regulation_implemented,
						color: '#aee571'
					},
					{
						name: 'Belum Implementasi',
						data: series_regulation_not_implemented,
						color: '#e53935'
					},
					{
						name: 'Peraturan Tidak Terkait',
						data: series_regulation_not_used,
						color: 'black'
					}]
				});

				$('#tableAgreement').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 15, 25, 50, -1 ],
					[ '15 rows', '25 rows', '50 rows', 'Show all' ]
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#tableRegulation').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 15, 25, 50, -1 ],
					[ '15 rows', '25 rows', '50 rows', 'Show all' ]
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
}

function newData(id,cat){
		if(id == 'new'){
			if (cat == "agreement") {
				$('#modalNewTitleAgreement').text('Buat Perjanjian Baru');
				$('#newButtonAgreement').show();
				$('#updateButtonAgreement').hide();
				clearNew();
				$('#modalNewAgreement').modal('show');
			}
			else if (cat == "regulation") {
				$('#modalNewTitleRegulation').text('Buat Peraturan Baru');
				$('#newButtonRegulation').show();
				$('#updateButtonRegulation').hide();
				// clearNew();
				clearRegulation();
				$('#modalNewRegulation').modal('show');
			}
		}
		else{
			var data = {
				id:id
			}

			if (cat == "agreement") {
				$('#newAttachmentAgreement').val('');
				$('#newButtonAgreement').hide();
				$('#updateButtonAgreement').show();
				
				$.get('{{ url("fetch/general/agreement_detail") }}', data, function(result, status, xhr){
					if(result.status){

						$('#newDepartment').html('');
						$('#newStatus').html('');

						var newDepartment = "";
						var newStatus = "";

						$.each(result.employees, function(key, value){
							if(value.department == result.agreement.department){
								newDepartment += '<option value="'+value.department+'" selected>'+value.department+'</option>';
							}
							else{
								newDepartment += '<option value="'+value.department+'">'+value.department+'</option>';
							}
						});
						$('#newDepartment').append(newDepartment);
						$('#newVendor').val(result.agreement.vendor);
						$('#newDescription').val(result.agreement.description);
						$('#newValidFrom').val(result.agreement.valid_from);
						$('#newValidTo').val(result.agreement.valid_to);

						$.each(result.agreement_statuses, function(key, value){
							if(value == result.agreement.status){
								newStatus += '<option value="'+value+'" selected>'+value+'</option>';
							}
							else{
								newStatus += '<option value="'+value+'">'+value+'</option>';
							}
						});
						$('#newStatus').append(newStatus);
						
						$('#newRemark').val(result.agreement.remark);
						$('#newId').val(result.agreement.id);

						$('#modalNewTitleAgreement').text('Edit Perjanjian');
						$('#loading').hide();
						$('#modalNewAgreement').modal('show');
					}
					else{
						openErrorGritter('Error', result.message);
						$('#loading').hide();
						audio_error.play();
					}
				});
			} else if (cat == "regulation"){
				$('#newAttachmentRegulation').val('');
				$('#newButtonRegulation').hide();
				$('#updateButtonRegulation').show();
				
				$.get('{{ url("fetch/general/agreement_detail") }}', data, function(result, status, xhr){
					if(result.status){

						$('#newDepartment').html('');
						$('#newStatus').html('');
						$('#newJenisPeraturan').html('');
						$('#newImplementation').html('');
						$('#newPenalty').html('');

						var newDepartment = "";
						var newStatus = "";
						var newJenisPeraturan = "";
						var newImplementation = "";
						var newPenalty = "";

						$('#newTanggalTerbit').val(result.agreement.valid_from);
						$('#newNomorRegulasi').val(result.agreement.vendor);
						$('#newJudulRegulasi').val(result.agreement.description);
						$('#newAnalisa').val(result.agreement.analisis);
						$('#newImplementation').val(result.agreement.implementation);
						$('#newAction').val(result.agreement.action);

						$.each(result.employees, function(key, value){
							if(value.department == result.agreement.department){
								newDepartment += '<option value="'+value.department+'" selected>'+value.department+'</option>';
							}
							else{
								newDepartment += '<option value="'+value.department+'">'+value.department+'</option>';
							}
						});


						$('#newDepartmentReg').append(newDepartment);


						$.each(result.regulation_statuses, function(key, value){
							if(value == result.agreement.status){
								newStatus += '<option value="'+value+'" selected>'+value+'</option>';
							}
							else{
								newStatus += '<option value="'+value+'">'+value+'</option>';
							}
						});
						$('#newStatusRegulasi').append(newStatus);


						if (result.agreement.remark == "Peraturan Terkait Operasional") {
							newJenisPeraturan += '<option value="Peraturan Terkait Operasional" selected>Peraturan Terkait Operasional</option>';
							newJenisPeraturan += '<option value="Peraturan Tidak Terkait Operasional">Peraturan Tidak Terkait Operasional</option>';
						}else if (result.agreement.remark == "Peraturan Tidak Terkait Operasional"){
							newJenisPeraturan += '<option value="Peraturan Terkait Operasional">Peraturan Terkait Operasional</option>';
							newJenisPeraturan += '<option value="Peraturan Tidak Terkait Operasional" selected>Peraturan Tidak Terkait Operasional</option>';
						}

						$('#newJenisPeraturan').append(newJenisPeraturan);

						if (result.agreement.implementation == "Masih") {
							newImplementation += '<option value="Masih Berlaku" selected>Masih Berlaku</option>';
							newImplementation += '<option value="Sudah Dicabut">Sudah Dicabut</option>';
						}else if (result.agreement.implementation == "Sudah Dicabut"){
							newImplementation += '<option value="Masih Berlaku">Masih Berlaku</option>';
							newImplementation += '<option value="Sudah Dicabut" selected>Sudah Dicabut</option>';
						}

						$('#newImplementation').append(newImplementation);

						if (result.agreement.penalty == "Administrasi") {
							newPenalty += '<option value="Administrasi" selected>Administrasi</option>';
							newPenalty += '<option value="Denda">Denda</option>';
							newPenalty += '<option value="Pidana">Pidana</option>';
						}else if (result.agreement.penalty == "Denda"){
							newPenalty += '<option value="Administrasi" >Administrasi</option>';
							newPenalty += '<option value="Denda" selected>Denda</option>';
							newPenalty += '<option value="Pidana">Pidana</option>';
						}else if (result.agreement.penalty == "Pidana"){
							newPenalty += '<option value="Administrasi" >Administrasi</option>';
							newPenalty += '<option value="Denda" >Denda</option>';
							newPenalty += '<option value="Pidana" selected>Pidana</option>';
						}

						$('#newPenalty').append(newPenalty);
						$('#newIdReg').val(result.agreement.id);

						$('#modalNewTitleRegulation').text('Edit Peraturan');
						$('#loading').hide();
						$('#modalNewRegulation').modal('show');
					}
					else{
						openErrorGritter('Error', result.message);
						$('#loading').hide();
						audio_error.play();
					}
				});
			}

			
		}
	}

	function newAgreement(id){
		$('#loading').show();
		if(id == 'new'){
			if($("#newDepartment").val() == "" || $('#newVendor').val() == "" || $('#newDescription').val() == "" || $('#newStatus').val() == "" || $('#validFrom').val() == "" || $('#validTo').val() == "")
			{
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachmentAgreement  = $('#newAttachmentAgreement').prop('files')[0];
			var file = $('#newAttachmentAgreement').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment', newAttachmentAgreement);
			formData.append('newDepartment', $("#newDepartment").val());
			formData.append('newVendor', $("#newVendor").val());
			formData.append('newDescription', $("#newDescription").val());
			formData.append('newValidFrom', $("#newValidFrom").val());
			formData.append('newValidTo', $("#newValidTo").val());
			formData.append('newStatus', $("#newStatus").val());
			formData.append('newRemark', $("#newRemark").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('create/general/agreement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNewAgreement').modal('hide');
						clearNew();
						fetchData();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#newDepartment").val() == "" || $('#newVendor').val() == "" || $('#newDescription').val() == "" || $('#newStatus').val() == "" || $('#validFrom').val() == "" || $('#validTo').val() == ""){
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachmentAgreement  = $('#newAttachmentAgreement').prop('files')[0];
			var file = $('#newAttachmentAgreement').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newId', $("#newIdReg").val());
			formData.append('newAttachment', newAttachmentAgreement);
			formData.append('newDepartment', $("#newDepartment").val());
			formData.append('newVendor', $("#newVendor").val());
			formData.append('newDescription', $("#newDescription").val());
			formData.append('newValidFrom', $("#newValidFrom").val());
			formData.append('newValidTo', $("#newValidTo").val());
			formData.append('newStatus', $("#newStatus").val());
			formData.append('newRemark', $("#newRemark").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('edit/general/agreement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNewAgreement').modal('hide');
						clearNew();
						fetchData();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
	}

	function newRegulation(id){
		$('#loading').show();

		if(id == 'new'){
			console.log($("#newJenisPeraturan").val());
			console.log($("#newTanggalTerbit").val());
			console.log($("#newNomorRegulasi").val());
			console.log($("#newJudulRegulasi").val());
			console.log($("#newAnalisa").val());
			console.log($("#newImplementation").val());
			console.log($("#newAction").val());
			console.log($("#newPenalty").val());
			console.log($("#newDepartmentReg").val());

			if($('#newJenisPeraturan').val() == "")
			{
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}
			else if ($('#newJenisPeraturan').val() == "Peraturan Terkait Operasional") {
				if($("#newTanggalTerbit").val() == "" || $('#newNomorRegulasi').val() == "" || $('#newJudulRegulasi').val() == "" || $('#newImplementation').val() == "" || $('#newAction').val() == "" || $('#newPenalty').val() == "" || $('#newStatusRegulasi').val() == "" || $('#newDepartmentReg').val() == "")
				{

					$('#loading').hide();
					openErrorGritter('Error', "Please fill field with (*) sign.");
					return false;
				}

				var formData = new FormData();
				var newAttachmentRegulation  = $('#newAttachmentRegulation').prop('files')[0];
				var file = $('#newAttachmentRegulation').val().replace(/C:\\fakepath\\/i, '').split(".");

				formData.append('newAttachment', newAttachmentRegulation);
				formData.append('newJenisPeraturan', $("#newJenisPeraturan").val());
				formData.append('newTanggalTerbit', $("#newTanggalTerbit").val());
				formData.append('newNomorRegulasi', $("#newNomorRegulasi").val());
				formData.append('newJudulRegulasi', $("#newJudulRegulasi").val());
				formData.append('newAnalisa', $("#newAnalisa").val());
				formData.append('newImplementation', $("#newImplementation").val());
				formData.append('newAction', $("#newAction").val());
				formData.append('newPenalty', $("#newPenalty").val());
				formData.append('newStatus', $("#newStatusRegulasi").val());
				formData.append('newDepartment', $("#newDepartmentReg").val());

				formData.append('extension', file[1]);
				formData.append('file_name', file[0]);

			}
			else if ($('#newJenisPeraturan').val() == "Peraturan Tidak Terkait Operasional") {
				console.log($("#newJenisPeraturan").val());
				console.log($("#newTanggalTerbitnot").val());
				console.log($("#newNomorRegulasinot").val());
				console.log($("#newJudulRegulasinot").val());
				console.log($("#newAnalisanot").val());

				if($("#newTanggalTerbitnot").val() == "" || $('#newNomorRegulasinot').val() == "" || $('#newJudulRegulasinot').val() == ""  || $('#newAnalisanot').val() == "")
				{

					$('#loading').hide();
					openErrorGritter('Error', "Please fill field with (*) sign.");
					return false;
				}

				var formData = new FormData();

				formData.append('newJenisPeraturan', $("#newJenisPeraturan").val());
				formData.append('newTanggalTerbit', $("#newTanggalTerbitnot").val());
				formData.append('newNomorRegulasi', $("#newNomorRegulasinot").val());
				formData.append('newJudulRegulasi', $("#newJudulRegulasinot").val());
				formData.append('newAnalisa', $("#newAnalisanot").val());
			}

			$.ajax({
				url:"{{ url('create/general/regulation') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNewRegulation').modal('hide');
						clearRegulation();
						fetchData();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#newTanggalTerbit").val() == "" || $('#newNomorRegulasi').val() == "" || $('#newJudulRegulasi').val() == "" || $('#newJenisPeraturan').val() == "" || $('#newImplementation').val() == "" || $('#newAction').val() == "" || $('#newPenalty').val() == "" || $('#newStatusRegulasi').val() == "" || $('#newDepartmentReg').val() == "")
			{
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachmentRegulation = $('#newAttachmentRegulation').prop('files')[0];
			var file = $('#newAttachmentRegulation').val().replace(/C:\\fakepath\\/i, '').split(".");
			
			formData.append('newId', $("#newIdReg").val());
			formData.append('newAttachment', newAttachmentRegulation);
			formData.append('newJenisPeraturan', $("#newJenisPeraturan").val());
			formData.append('newTanggalTerbit', $("#newTanggalTerbit").val());
			formData.append('newNomorRegulasi', $("#newNomorRegulasi").val());
			formData.append('newJudulRegulasi', $("#newJudulRegulasi").val());
			formData.append('newAnalisa', $("#newAnalisa").val());
			formData.append('newImplementation', $("#newImplementation").val());
			formData.append('newAction', $("#newAction").val());
			formData.append('newPenalty', $("#newPenalty").val());
			formData.append('newStatus', $("#newStatusRegulasi").val());
			formData.append('newDepartment', $("#newDepartmentReg").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('edit/general/regulation') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNewRegulation').modal('hide');
						clearNew();
						fetchData();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
	}

	function clearNew(){
		$("#newDepartment").prop('selectedIndex', 0).change();
		$('#newVendor').val('');
		$('#newDescription').val('');
		$('#newValidFrom').val("");
		$('#newValidTo').val("");
		$('#newAttachmentAgreement').val('');
		$("#newStatus").prop('selectedIndex', 0).change();
		$('#newRemark').val('');
		$('#newId').val('');
		$('#downloadId').val('');
	}

	function clearRegulation(){
		$("#newDepartmentReg").prop('selectedIndex', 0).change();
		$('#newNomorRegulasi').val('');
		$('#newJudulRegulasi').val('');
		$('#newTanggalTerbit').val("");
		$('#newAnalisa').val('');
		$("#newImplementation").prop('selectedIndex', 0).change();
		$('#newAction').val('');
		$('#newPenalty').val('');
		$('#newStatusRegulasi').val('');
		$('#newNomorRegulasinot').val('');
		$('#newJudulRegulasinot').val('');
		$('#newTanggalTerbitnot').val("");
		$('#newAnalisanot').val('');

	}

	function downloadAtt(){
		var file_name = $('#selectDownload').val();
		var data = {
			file_name:file_name
		}

		$.get('{{ url("download/general/agreement") }}', data, function(result, status, xhr){
			if(result.status){
				download_files(result.file_paths);
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function downloadAttRegulation(){
		var file_name = $('#selectDownload').val();
		var data = {
			file_name:file_name
		}

		$.get('{{ url("download/general/regulation") }}', data, function(result, status, xhr){
			if(result.status){
				download_files(result.file_paths);
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function download_files(files) {
		function download_next(i) {
			if (i >= files.length) {
				return;
			}
			var a = document.createElement('a');
			a.href = files[i].download;
			a.target = '_parent';
			if ('download' in a) {
				a.download = files[i].filename;
			}
			(document.body || document.documentElement).appendChild(a);
			if (a.click) {
				a.click();
			} else {
				$(a).click();
			}
			a.parentNode.removeChild(a);
			setTimeout(function() {
				download_next(i + 1);
			}, 500);
		}
		download_next(0);
	}

	function modalDownloadAgreement(id){
		var data = {
			id:id
		}
		$.get('{{ url("fetch/general/agreement_download") }}', data, function(result, status, xhr){
			if(result.status){

				$('#selectDownload').html('');
				$('#selectPreview').html('');
				var optionData = '';
				var previewData = '';
				$.each(result.files, function(key, value) {
					optionData += '<option value="' + value.file_name + '">' + value.file_name + '</option>';
					previewData += '<a target="_blank" type="button" class="btn btn-warning btn-sm" href="../../files/agreements/'+value.file_name+'"><i class="fa fa-eye"></i></a><br>';
				});
				$('#selectDownload').append(optionData);
				$('#selectPreview').append(previewData);
				$('#modalDownloadAgreement').modal('show');

			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function modalDownloadRegulation(id){
		var data = {
			id:id
		}
		$.get('{{ url("fetch/general/agreement_download") }}', data, function(result, status, xhr){
			if(result.status){

				$('#selectDownloadRegulation').html('');
				$('#selectPreviewRegulation').html('');
				var optionData = '';
				var previewData = '';
				$.each(result.files, function(key, value) {
					optionData += '<option value="' + value.file_name + '">' + value.file_name + '</option>';
					previewData += '<a target="_blank" type="button" class="btn btn-warning btn-sm" href="../../files/agreements/regulation/'+value.file_name+'"><i class="fa fa-eye"></i></a><br>';
				});
				$('#selectDownloadRegulation').append(optionData);
				$('#selectPreviewRegulation').append(previewData);
				$('#modalDownloadRegulation').modal('show');

			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}


	function fetchFilter(dep, cat){

		// console.log(dep);
		// console.log(cat);


		 // $('.vendor-tab-regulation').removeClass('active');
		 // var tab2 = $('#tab_2').attr('class').split(' ');
		 // $('.'+tab2[0]+' .'+tab2[1]).removeClass('active');

		 // $('.vendor-tab-agreement').addClass('active');
		 // var tab = $('#tab_1').attr('class').split(' ');
		 // $('.'+tab[0]+' .'+tab[1]).removeClass('active');

		 activaTab('tab_1');

		$('#tableAgreement').DataTable().clear();
		$('#tableAgreement').DataTable().destroy();
		$('#tableAgreementBody').html("");
		var tableAgreementBody = "";
		var cat2 = cat;
		if(cat == 'Not Used'){
			cat = 'Terminated';
		}
		else if(cat == "Expired"){
			cat = 'In Use';
		}
		else if(cat == "Unsafe"){
			cat = 'In Use';
		}
		else{
			cat = 'In Use';
		}
		$.each(agreements, function(key, value){

			// console.log(value.department_shortname);
			// console.log(value.status);

			if(value.department_shortname == dep && value.status == cat){
				if (cat2 == "Expired" && value.validity <= 0) {
					tableAgreementBody += '<tr>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: center;">'+parseInt(key+1)+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: left;">'+value.vendor+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 8%; text-align: left;">'+value.description+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_from))+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_to))+'</td>';
					if(value.status == 'In Use'){
						count_agreement += 1;
						tableAgreementBody += '<td style="width: 0.1%; text-align: left;">'+value.status+'</td>';
					}
					if(value.status == 'Terminated'){
						tableAgreementBody += '<td style="width: 0.1%; text-align: left; color: white; background-color: black;">Not Used</td>';
					}
					if(value.validity <= 0){
						if(value.status == 'In Use'){
							count_agreement_expired += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #e53935; text-align: right;">'+value.validity+' Day(s)</td>';
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}	
					}
					else if(value.validity <= 30){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #f9a825; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else if(value.validity <= 90){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #ffeb3b; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else{
						tableAgreementBody += '<td style="width: 0.1%; background-color: #aee571; text-align: right;">'+value.validity+' Day(s)</td>';			
					}
					tableAgreementBody += '<td style="width: 3%; text-align: left;">'+(value.remark || "")+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalDownloadAgreement(\''+value.id+'\')">Att('+value.att+')</a></td>';
					tableAgreementBody += '<td style="width: 2%; text-align: left;">'+value.name+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; text-align: right;">'+value.updated_at+'</td>';
					tableAgreementBody += '</tr>';
				}
				else if(cat2 == "Unsafe" && value.validity <= 90){
					tableAgreementBody += '<tr>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: center;">'+parseInt(key+1)+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: left;">'+value.vendor+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 8%; text-align: left;">'+value.description+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_from))+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_to))+'</td>';
					if(value.status == 'In Use'){
						count_agreement += 1;
						tableAgreementBody += '<td style="width: 0.1%; text-align: left;">'+value.status+'</td>';
					}
					if(value.status == 'Terminated'){
						tableAgreementBody += '<td style="width: 0.1%; text-align: left; color: white; background-color: black;">Not Used</td>';
					}
					if(value.validity <= 0){
						if(value.status == 'In Use'){
							count_agreement_expired += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #e53935; text-align: right;">'+value.validity+' Day(s)</td>';
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}	
					}
					else if(value.validity <= 30){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #f9a825; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else if(value.validity <= 90){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #ffeb3b; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else{
						tableAgreementBody += '<td style="width: 0.1%; background-color: #aee571; text-align: right;">'+value.validity+' Day(s)</td>';			
					}
					tableAgreementBody += '<td style="width: 3%; text-align: left;">'+(value.remark || "")+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalDownloadAgreement(\''+value.id+'\')">Att('+value.att+')</a></td>';
					tableAgreementBody += '<td style="width: 2%; text-align: left;">'+value.name+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; text-align: right;">'+value.updated_at+'</td>';
					tableAgreementBody += '</tr>';
				}
				else{
					tableAgreementBody += '<tr>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: center;">'+parseInt(key+1)+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: left;">'+value.vendor+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 8%; text-align: left;">'+value.description+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_from))+'</td>';
					tableAgreementBody += '<td onclick="newData(\''+value.id+'\',\'agreement\')" style="width: 1%; text-align: right;">'+getFormattedDate(new Date(value.valid_to))+'</td>';
					if(value.status == 'In Use'){
						count_agreement += 1;
						tableAgreementBody += '<td style="width: 0.1%; text-align: left;">'+value.status+'</td>';
					}
					if(value.status == 'Terminated'){
						tableAgreementBody += '<td style="width: 0.1%; text-align: left; color: white; background-color: black;">Not Used</td>';
					}
					if(value.validity <= 0){
						if(value.status == 'In Use'){
							count_agreement_expired += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #e53935; text-align: right;">'+value.validity+' Day(s)</td>';
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}	
					}
					else if(value.validity <= 30){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #f9a825; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else if(value.validity <= 90){
						if(value.status == 'In Use'){
							count_agreement_about += 1;
							tableAgreementBody += '<td style="width: 0.1%; background-color: #ffeb3b; text-align: right;">'+value.validity+' Day(s)</td>';	
						}
						else{
							tableAgreementBody += '<td style="width: 0.1%; text-align: right; color: white; background-color: black;">Not Used</td>';
						}		
					}
					else{
						tableAgreementBody += '<td style="width: 0.1%; background-color: #aee571; text-align: right;">'+value.validity+' Day(s)</td>';			
					}
					tableAgreementBody += '<td style="width: 3%; text-align: left;">'+(value.remark || "")+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalDownloadAgreement(\''+value.id+'\')">Att('+value.att+')</a></td>';
					tableAgreementBody += '<td style="width: 2%; text-align: left;">'+value.name+'</td>';
					tableAgreementBody += '<td style="width: 0.1%; text-align: right;">'+value.updated_at+'</td>';
					tableAgreementBody += '</tr>';
				}

				
			}
		});

		$('#tableAgreementBody').append(tableAgreementBody);

		$('#tableAgreement').DataTable({
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
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});
		
	}

	function fetchFilterRegulation(dep, cat){

		 // $('.vendor-tab-agreement').removeClass('active');
		 // var tab = $('#tab_1').attr('class').split(" ");
		 // console.log(tab);
		 // $('.'+tab[0]+' .'+tab[1]).removeClass('active');

		 // $('.vendor-tab-regulation').addClass('active');
		 // var tab2 = $('#tab_2').attr('class').split(" ");
		 // $('.'+tab2[0]+' .'+tab2[1]).removeClass('active');

		 activaTab('tab_2');


		$('#tableRegulation').DataTable().clear();
		$('#tableRegulation').DataTable().destroy();
		$('#tableRegulationBody').html("");
		var tableRegulationBody = "";
		$.each(regulations, function(key, value){
			if(value.department_shortname == dep && value.status == cat){
				tableRegulationBody += '<tr>';
				tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 0.1%; text-align: center;">'+parseInt(key+1)+'</td>';
				tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 0.1%; text-align: left;">'+(getFormattedDate(new Date(value.valid_from)) || '')+'</td>';
				tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 1%; text-align: left;">'+(value.vendor || '')+'</td>';
				tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 3%; text-align: left;">'+(value.description || '')+'</td>';
				tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 4%; text-align: left;">'+(value.analisis || '')+'</td>';
				tableRegulationBody += '<td onclick="newData(\''+value.id+'\',\'regulation\')" style="width: 4%; text-align: left;">'+(value.implementation || '')+' <br> Action : '+(value.action || '')+'</td>';
				tableRegulationBody += '<td style="width: 3%; text-align: left;">'+(value.penalty || '')+'</td>';

				if (value.status == 'Belum Implementasi') {
					tableRegulationBody += '<td style="width: 2%; text-align: left;background-color:#e53935">'+(value.status || '')+'</td>';
				}else if(value.status == 'Sudah Implementasi') {
					tableRegulationBody += '<td style="width: 2%; text-align: left;background-color:#aee571">'+(value.status || '')+'</td>';
				}else if(value.status == null){
					tableRegulationBody += '<td style="width: 2%; text-align: left;background-color:black;color:white">'+(value.status || '')+'</td>';
				}
				// 
				tableRegulationBody += '<td style="width: 0.1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalDownloadRegulation(\''+value.id+'\')">Att('+value.att+')</a></td>';
				tableRegulationBody += '<td style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
				tableRegulationBody += '<td style="width: 0.1%; text-align: right;">'+value.updated_at+'</td>';
				tableRegulationBody += '</tr>';
			}
		});

		$('#tableRegulationBody').append(tableRegulationBody);

		$('#tableRegulation').DataTable({
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
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});
		
	}

	function ChooseJenisPeraturan(elem){
		clearRegulation();
		if (elem == "Peraturan Terkait Operasional") {
			$("#operasional").show();
			$("#not_operasional").hide();
			$("#tombol").show();
		} else if (elem == "Peraturan Tidak Terkait Operasional"){
			$("#operasional").hide();
			$("#not_operasional").show();
			$("#tombol").show();
		} else{
			$("#operasional").hide();
			$("#not_operasional").hide();
			$("#tombol").hide();
		}
	}


function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});
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
}

</script>

@endsection
