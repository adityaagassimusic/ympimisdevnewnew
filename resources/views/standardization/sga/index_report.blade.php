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
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	th:hover {
		overflow: visible;
	}
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		/*background-color: #FFD700;*/
	}
	#loading, #error { display: none; }
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
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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
	<div class="alert alert-danger alert-dismissible" id="div_check" style="display: none;background-color: rgb(21, 115, 53) !important;border-color: rgb(21, 115, 53) !important">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		<span style="font-size: 20px;font-weight: bold;margin-bottom: 5px">Ada Juri yang belum mengisi penilaian.</span>
		<table class="table table-responsive" id="tableCheck">
			<thead>
				<tr style="background-color: #417dca">
					<th>Employee ID</th>
					<th>Name</th>
					<th>Team No.</th>
					<th>Team Name</th>
				</tr>
			</thead>
			<tbody id="bodyCheck">
				
			</tbody>
		</table>
	</div>					
	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Periode</span>
							<div class="form-group">
								<select class="form-control select2" name="periode" id="periode" data-placeholder="Pilih Periode" style="width: 100%;">
									<option></option>
									@foreach($periode as $periode)
										<option value="{{$periode->periode}}">{{$periode->periode}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/sga') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/sga/report') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
									<button class="btn btn-success col-sm-14" onclick="saveSelection()">Simpan Juara</button>
									<button class="btn btn-success col-sm-14" id="btn_approve" onclick="approve()"><i class="fa fa-check"></i> Approve</button>
									<a class="btn btn-warning col-sm-14" href="" id="print_pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> Print PDF</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableSga" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="" id="headTableSga">
									
								</thead>
								<tbody id="bodyTableSga">
								</tbody>
								<tfoot>
								</tfoot>
							</table>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var teams_all = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		// fillList();
		$('#bodyTableSga').html("");
		$('#headTableSga').html("");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		teams_all = [];
		$('#btn_approve').hide();
		$('#print_pdf').hide();
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
	function fillList(){
		$('#div_check').hide();
		$('#print_pdf').hide();
		$('#loading').show();
		if ($('#periode').val() == '') {
			audio_error.play();
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Periode.');
			return false;
		}
		$('#btn_approve').hide();
		var data = {
			periode:$('#periode').val(),
		}
		$.get('{{ url("fetch/sga/report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableSga').html("");
				$('#headTableSga').html("");

				var headTableSga = '';

				var column = parseInt(result.sga_asesor.length)+2;

				var tableDataBody = "";
				var index = 1;

				if (result.teams.length > 0) {

					if ($('#periode').val().match(/Final/gi)) {
						headTableSga += '<tr>';
						headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">#</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Team No.</th>';
						headTableSga += '<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Nama Team</th>';
						headTableSga += '<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Judul</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Nilai Seleksi</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Nilai Final</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">40% Seleksi</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">60% Final</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Total Penilaian</th>';
						headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Juara</th>';
						headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Hadiah</th>';
						headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">File PDF</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">Secretariat</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">Manager QA</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">DGM</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">GM</th>';
						// headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">Vice President</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">President Director</th>';
						headTableSga += '</tr>';

						$.each(result.teams, function(key, value) {
							if (!value.periode.match(/Final/gi)) {
								if (index < 6) {
									var bgcolor = '#c3e157';
								}else{
									var bgcolor = 'none';
								}
							}
							tableDataBody += '<tr style="background-color:'+bgcolor+'">';
							tableDataBody += '<td style="padding:10px;text-align:right">'+ index +'</td>';
							tableDataBody += '<td style="padding:10px">'+ value.team_no +'</td>';
							tableDataBody += '<td style="padding:10px">'+ value.team_name +'</td>';
							tableDataBody += '<td style="padding:10px">'+ value.team_title +'</td>';
							var total = 0;
							tableDataBody += '<td style="padding:10px;text-align:right">'+ value.total_nilai_seleksi +'</td>';
							tableDataBody += '<td style="padding:10px;text-align:right">'+ value.total_nilai_final +'</td>';
							var seleksi = (0.4*parseInt(value.total_nilai_seleksi)).toFixed(1);
							tableDataBody += '<td style="padding:10px;text-align:right">'+ value.persen_seleksi +'</td>';
							var final = (0.6*parseInt(value.total_nilai_final)).toFixed(1);
							tableDataBody += '<td style="padding:10px;text-align:right">'+ value.persen_final +'</td>';
							tableDataBody += '<td style="padding:10px;text-align:right">'+ value.totals +'</td>';
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+index+'" id="selection_'+result.teams_all[u].id+'"></td>';
										}else{
											if (result.teams_all[u].presdir_approver_status == null) {
												tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+result.teams_all[u].selection_result+'" id="selection_'+result.teams_all[u].id+'"></td>';
											}else{
												tableDataBody += '<td style="padding:10px;text-align:right">'+result.teams_all[u].selection_result+'</td>';
											}
											if (result.teams_all[u].secretariat_approver_status ==  null) {
												$('#btn_approve').show();
											}
										}
									}else{
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+index+'" id="selection_'+result.teams_all[u].id+'"></td>';
										}else{
											if (result.teams_all[u].dgm_approver_status == null) {
												tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+result.teams_all[u].selection_result+'" id="selection_'+result.teams_all[u].id+'"></td>';
											}else{
												tableDataBody += '<td style="padding:10px;text-align:right">'+result.teams_all[u].selection_result+'</td>';
											}
											if (result.teams_all[u].secretariat_approver_status ==  null) {
												$('#btn_approve').show();
											}
										}
									}
								}
							}
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].hadiah == null) {
											tableDataBody += '<td style="padding:10px;text-align:right"><input type="text" style="width:100px;text-align:right" value="0,0" id="hadiah_'+result.teams_all[u].id+'"></td>';
										}else{
											if (result.teams_all[u].presdir_approver_status == null) {
												tableDataBody += '<td style="padding:10px;text-align:right"><input type="text" style="width:100px;text-align:right" value="'+result.teams_all[u].hadiah+'" id="hadiah_'+result.teams_all[u].id+'"></td>';
											}else{
												tableDataBody += '<td style="padding:10px;text-align:right">'+result.teams_all[u].hadiah+'</td>';
											}
										}
									}else{
										tableDataBody += '<td style="padding:10px;text-align:right"></td>';
									}
								}
							}
							tableDataBody += '<td style="padding:10px;">';
							if (value.file_pdf != null) {
								var url_pdf = "{{ url('data_file/sga/pdf/') }}"+'/'+value.file_pdf;
								tableDataBody += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a><br><br>';
								tableDataBody += '<input type="file" name="file_pdf_'+value.id+'" id="file_pdf_'+value.id+'"><br><button class="btn btn-success btn-xs pull-left" onclick="uploadPdf(\''+value.id+'\')"><i class="fa fa-check"></i> Submit</button>';
							}else{
								tableDataBody += '<input type="file" name="file_pdf_'+value.id+'" id="file_pdf_'+value.id+'"><br><button class="btn btn-success btn-xs pull-left" onclick="uploadPdf(\''+value.id+'\')"><i class="fa fa-check"></i> Submit</button>';
							}
							tableDataBody += '</td>';
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].secretariat_approver_status != null) {
												tableDataBody += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].secretariat_approver_status.split('_')[1]))+'</td>';
												$('#print_pdf').show();
											}else{
												if (result.teams_all[u].secretariat_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
													$('#print_pdf').show();
												}
											}
										}
									}else{
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].secretariat_approver_status != null) {
												tableDataBody += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].secretariat_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].secretariat_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].manager_qa_approver_status != null) {
												if (result.teams_all[u].manager_qa_approver_status.split('_')[0] == 'Approved') {
													var bgcolorapprove = '#00a65a';
												}else{
													var bgcolorapprove = '#d1a513';
												}
												tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].manager_qa_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].manager_qa_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].manager_qa_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].manager_qa_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}else{
										tableDataBody += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;padding:10px">None</td>';
									}
								}
							}
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].dgm_approver_status != null) {
												if (result.teams_all[u].dgm_approver_status.split('_')[0] == 'Approved') {
													var bgcolorapprove = '#00a65a';
												}else{
													var bgcolorapprove = '#d1a513';
												}
												tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].dgm_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].dgm_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}else{
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].dgm_approver_status != null) {
												tableDataBody += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].dgm_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].dgm_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}

							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].gm_approver_status != null) {
												if (result.teams_all[u].gm_approver_status.split('_')[0] == 'Approved') {
													var bgcolorapprove = '#00a65a';
												}else{
													var bgcolorapprove = '#d1a513';
												}
												tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].gm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].gm_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].gm_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].gm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}else{
										tableDataBody += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;padding:10px">None</td>';
									}
								}
							}

							// for(var u = 0; u < result.teams_all.length;u++){
							// 	if (result.teams_all[u].team_no == value.team_no) {
							// 		if (value.periode.match(/Final/gi)) {
							// 			if (result.teams_all[u].selection_result == null) {
							// 				tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
							// 			}else{
							// 				if (result.teams_all[u].vice_approver_status != null) {
							// 					if (result.teams_all[u].vice_approver_status.split('_')[0] == 'Approved') {
							// 						var bgcolorapprove = '#00a65a';
							// 					}else{
							// 						var bgcolorapprove = '#d1a513';
							// 					}
							// 					tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].vice_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].vice_approver_status.split('_')[1]))+'</td>';
							// 				}else{
							// 					if (result.teams_all[u].vice_approver_name == null) {
							// 						tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
							// 					}else{
							// 						tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].vice_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
							// 					}
							// 				}
							// 			}
							// 		}else{
							// 			tableDataBody += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;padding:10px">None</td>';
							// 		}
							// 	}
							// }

							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].presdir_approver_status != null) {
												if (result.teams_all[u].presdir_approver_status.split('_')[0] == 'Approved') {
													var bgcolorapprove = '#00a65a';
												}else{
													var bgcolorapprove = '#d1a513';
												}
												tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].presdir_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].presdir_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].presdir_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].presdir_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}else{
										tableDataBody += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;padding:10px">None</td>';
									}
								}
							}
							tableDataBody += '</tr>';
							index++;
						});
					}else{
						headTableSga += '<tr>';
						headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">#</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Team No.</th>';
						headTableSga += '<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Nama Team</th>';
						headTableSga += '<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Judul</th>';
						var asesor_id = [];
						for(var i = 0; i < result.sga_asesor.length;i++){
							headTableSga += '<th width="4%" style="background-color: rgb(126,86,134); color: #fff;">'+result.sga_asesor[i].asesor_name+'</th>';
							asesor_id.push(result.sga_asesor[i].asesor_id);
						}
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Total Penilaian</th>';
						headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Juara</th>';
						headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">File PDF</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">Secretariat</th>';
						headTableSga += '<th width="2%" style="background-color: #537cf5; color: #fff;">DGM</th>';
						headTableSga += '</tr>';

						$.each(result.teams, function(key, value) {
							if (!value.periode.match(/Final/gi)) {
								if (index < 6) {
									var bgcolor = '#c3e157';
								}else{
									var bgcolor = 'none';
								}
							}
							tableDataBody += '<tr style="background-color:'+bgcolor+'">';
							tableDataBody += '<td style="padding:10px;text-align:right">'+ index +'</td>';
							tableDataBody += '<td style="padding:10px">'+ value.team_no +'</td>';
							tableDataBody += '<td style="padding:10px">'+ value.team_name +'</td>';
							tableDataBody += '<td style="padding:10px">'+ value.team_title +'</td>';
							var total = 0;
							for(var k = 0; k < asesor_id.length;k++){
								for(var j = 0; j < result.sga_result.length;j++){
									if (result.sga_result[j].asesor_id == asesor_id[k] && result.sga_result[j].team_no == value.team_no) {
										tableDataBody += '<td style="padding:10px;text-align:right">'+ (result.sga_result[j].total_nilai || 0) +'</td>';
										total = total + parseInt((result.sga_result[j].total_nilai || 0));
									}
								}
							}
							tableDataBody += '<td style="padding:10px;text-align:right">'+ total +'</td>';
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+index+'" id="selection_'+result.teams_all[u].id+'"></td>';
										}else{
											if (result.teams_all[u].presdir_approver_status == null) {
												tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+result.teams_all[u].selection_result+'" id="selection_'+result.teams_all[u].id+'"></td>';
											}else{
												tableDataBody += '<td style="padding:10px;text-align:right">'+result.teams_all[u].selection_result+'</td>';
											}
											if (result.teams_all[u].secretariat_approver_status ==  null) {
												$('#btn_approve').show();
											}
										}
									}else{
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+index+'" id="selection_'+result.teams_all[u].id+'"></td>';
										}else{
											if (result.teams_all[u].dgm_approver_status == null) {
												tableDataBody += '<td style="padding:10px;text-align:right"><input type="number" style="width:50px;text-align:right" value="'+result.teams_all[u].selection_result+'" id="selection_'+result.teams_all[u].id+'"></td>';
											}else{
												tableDataBody += '<td style="padding:10px;text-align:right">'+result.teams_all[u].selection_result+'</td>';
											}
											if (result.teams_all[u].secretariat_approver_status ==  null) {
												$('#btn_approve').show();
											}
										}
									}
								}
							}
							tableDataBody += '<td style="padding:10px;">';
							if (value.file_pdf != null) {
								var url_pdf = "{{ url('data_file/sga/pdf/') }}"+'/'+value.file_pdf;
								tableDataBody += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a><br><br>';
								tableDataBody += '<input type="file" name="file_pdf_'+value.id+'" id="file_pdf_'+value.id+'"><br><button class="btn btn-success btn-xs pull-left" onclick="uploadPdf(\''+value.id+'\')"><i class="fa fa-check"></i> Submit</button>';
							}else{
								tableDataBody += '<input type="file" name="file_pdf_'+value.id+'" id="file_pdf_'+value.id+'"><br><button class="btn btn-success btn-xs pull-left" onclick="uploadPdf(\''+value.id+'\')"><i class="fa fa-check"></i> Submit</button>';
							}
							tableDataBody += '</td>';
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].secretariat_approver_status != null) {
												tableDataBody += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].secretariat_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].secretariat_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}else{
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].secretariat_approver_status != null) {
												tableDataBody += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].secretariat_approver_status.split('_')[1]))+'</td>';
												$('#print_pdf').show();
											}else{
												if (result.teams_all[u].secretariat_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].secretariat_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
													$('#print_pdf').show();
												}
											}
										}
									}
								}
							}
							for(var u = 0; u < result.teams_all.length;u++){
								if (result.teams_all[u].team_no == value.team_no) {
									if (value.periode.match(/Final/gi)) {
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].dgm_approver_status != null) {
												if (result.teams_all[u].dgm_approver_status.split('_')[0] == 'Approved') {
													var bgcolorapprove = '#00a65a';
												}else{
													var bgcolorapprove = '#d1a513';
												}
												tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].dgm_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].dgm_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}else{
										if (result.teams_all[u].selection_result == null) {
											tableDataBody += '<td style="font-weight:bold;font-size:11px;padding:10px"></td>';
										}else{
											if (result.teams_all[u].dgm_approver_status != null) {
												if (result.teams_all[u].dgm_approver_status.split('_')[0] == 'Approved') {
													var bgcolorapprove = '#00a65a';
												}else{
													var bgcolorapprove = '#d1a513';
												}
												tableDataBody += '<td style="background-color:'+bgcolorapprove+';color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>'+getFormattedDateTime(new Date(result.teams_all[u].dgm_approver_status.split('_')[1]))+'</td>';
											}else{
												if (result.teams_all[u].dgm_approver_name == null) {
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">Waiting</td>';
												}else{
													tableDataBody += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;padding:10px">'+ result.teams_all[u].dgm_approver_name.split(' ').slice(0,2).join(' ') +'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}
							tableDataBody += '</tr>';
							index++;
						});
					}
				}else{
					headTableSga += '<tr>';
					headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">#</th>';
					headTableSga += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Team No.</th>';
					headTableSga += '<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Nama Team</th>';
					headTableSga += '<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Judul</th>';
					headTableSga += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">File PDF</th>';
					headTableSga += '</tr>';

					for(var u = 0; u < result.teams_all.length;u++){
						tableDataBody += '<tr>';
						tableDataBody += '<td style="padding:10px;text-align:right">'+ index +'</td>';
						tableDataBody += '<td style="padding:10px">'+ result.teams_all[u].team_no +'</td>';
						tableDataBody += '<td style="padding:10px">'+ result.teams_all[u].team_name +'</td>';
						tableDataBody += '<td style="padding:10px">'+ result.teams_all[u].team_title +'</td>';
						tableDataBody += '<td style="padding:10px;">';
						if (result.teams_all[u].file_pdf != null) {
							var url_pdf = "{{ url('data_file/sga/pdf/') }}"+'/'+result.teams_all[u].file_pdf;
							tableDataBody += '<a target="_blank" href="'+url_pdf+'" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> File PDF</a><br><br>';
							tableDataBody += '<input type="file" name="file_pdf_'+result.teams_all[u].id+'" id="file_pdf_'+result.teams_all[u].id+'"><br><button class="btn btn-success btn-xs pull-left" onclick="uploadPdf(\''+result.teams_all[u].id+'\')"><i class="fa fa-check"></i> Submit</button>';
						}else{
							tableDataBody += '<input type="file" name="file_pdf_'+result.teams_all[u].id+'" id="file_pdf_'+result.teams_all[u].id+'"><br><button class="btn btn-success btn-xs pull-left" onclick="uploadPdf(\''+result.teams_all[u].id+'\')"><i class="fa fa-check"></i> Submit</button>';
						}
						tableDataBody += '</td>';
						tableDataBody += '</tr>';
						index++;
					}
				}
				
				$('#headTableSga').append(headTableSga);
				$('#tableSga').DataTable().clear();
				$('#tableSga').DataTable().destroy();
				$('#bodyTableSga').append(tableDataBody);

				var table = $('#tableSga').DataTable({
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
					'pageLength': result.teams.length,
					'searching': true	,
					'ordering': true,
					"order": [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				teams_all = [];

				teams_all = result.teams_all;
				var url = '{{url("pdf/sga/report/")}}'+'/'+$('#periode').val();
				jQuery('#print_pdf').attr("href", url);
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function uploadPdf(id) {
		$('#loading').show();
		if ($('#file_pdf_'+id).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Masukkan File');
			audio_error.play();
			return false;
		}

		var formData = new FormData();
		var newAttachment  = $('#file_pdf_'+id).prop('files')[0];
		var file = $('#file_pdf_'+id).val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('id', id);
		formData.append('periode', $('#periode').val());

		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/sga/pdf') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					$('#file_pdf_'+id).val("");
					$('#loading').hide();
					fillList();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function saveSelection() {
		if (teams_all.length > 0) {
			var selection_result = [];
			var hadiah = [];
			for(var i = 0; i < teams_all.length;i++){
				if ($('#periode').val().match(/Final/gi)) {
					var results = $('#selection_'+teams_all[i].id).val();
					var hadiah = $('#hadiah_'+teams_all[i].id).val();
					if (hadiah == '0,0') {
						openErrorGritter('Error!','Hadiah harus diisi');
						return false;
					}
					selection_result.push({id:teams_all[i].id,selection:results,hadiah:hadiah});
				}else{
					var results = $('#selection_'+teams_all[i].id).val();
					selection_result.push({id:teams_all[i].id,selection:results});
				}
			}

			var data = {
				selection_result:selection_result,
				periode:$('#periode').val()
			}

			$.post('{{ url("selection/sga/report") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!',result.message);
					fillList();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					if (result.check.length > 0) {

						$('#tableCheck').DataTable().clear();
						$('#tableCheck').DataTable().destroy();

						$('#bodyCheck').html('');
						var bodyCheck = '';

						$.each(result.check, function(key, value) {
							bodyCheck += '<tr style="background-color:white;color:black">';
							bodyCheck += '<td>'+value.asesor_id+'</td>';
							bodyCheck += '<td>'+value.asesor_name+'</td>';
							bodyCheck += '<td>'+value.team_no+'</td>';
							bodyCheck += '<td>'+value.team_name+'</td>';
							bodyCheck += '</tr>';
						});
						$('#bodyCheck').append(bodyCheck);
						$('#div_check').show();

						var table = $('#tableCheck').DataTable({
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
							'searching': true	,
							'ordering': true,
							"order": [],
							'info': true,
							'autoWidth': true,
							"sPaginationType": "full_numbers",
							"bJQueryUI": true,
							"bAutoWidth": false,
							"processing": true
						});
					}
				}
			});
		}
	}

	function approve() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			$.get('{{ url("approval/sga/report/") }}/'+$('#periode').val()+'/secretariat', function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!',result.message);
					fillList();
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
				}
			});
		}
	}

	function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
    }



</script>
@endsection