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
			<div class="col-xs-3" style="padding-left: 5px;padding-right: 5px">
				<select class="form-control select2" name="code" id="code" data-placeholder="Pilih Nama Sertifikat 認定証選択" style="width: 100%;">
					<option></option>
					@foreach($code_number as $code_number)
					<option value="{{$code_number->description}}_{{$code_number->product}}">{{$code_number->description}} - {{$code_number->product}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fillList()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)" id="btnSearch">
					Search <small>検索</small>
				</button>
			</div>
			<div class="col-xs-3 pull-right" style="padding-left: 5px;padding-right: 5px;">
			<?php if ($role == 'L-QA' || $role == 'S-MIS'): ?>
				<a class="btn btn-success pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134)" href="{{url('approval/qa/certificate/inprocess/Leader QA')}}">
					Approval Leader <small>リーダーの承認</small>
				</a>
			<?php endif ?>
			<?php if ($role == 'L-QA' || $role == 'S-QA' || $role == 'S-MIS'): ?>
				<a class="btn btn-primary pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134)" href="{{url('index/qa/qr_code/certificate/inprocess')}}">
					Print QR Code
				</a>
			<?php endif ?>
			</div>
		</div>
		<div class="col-xs-10" style="padding-right: 5px;">
			<div id="container" style="height: 35vh;">
			</div>
		</div>
		<div class="col-xs-2" style="margin-bottom: 0px;padding-bottom: 10px;padding-left: 5px" id="div_detail">
			<div class="box box-solid" style="height: 35vh;margin-bottom: 0px;padding-bottom: 0px">
				<div class="box-body">
					<center style="background-color: #605ca8;color: white"><h4 style="font-weight: bold;padding: 5px;margin-top: 0px">Resume まとめ</h4></center>
					<table class="table table-bordered" id="tableResume" style="height: 26vh">
						<thead>
							<tr>
								<th style="text-align:center;width: 50%;color: green">Active<br>有効</th>
								<th style="text-align:center;color: green;font-size: 25px" id="resumeActive">0</th>
							</tr>
							<tr>
								<th style="text-align:center;width: 50%;color: orange">Renewal<br>更新必要</th>
								<th style="text-align:center;color: orange;font-size: 25px" id="resumeRenewal">0</th>
							</tr>
							<tr>
								<th style="text-align:center;width: 50%;color: red">Expired<br>無効</th>
								<th style="text-align:center;color: red;font-size: 25px" id="resumeExpired">0</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 0px;padding-top: 0px">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableCode" class="table table-bordered table-hover" style="margin-bottom: 0;">
								<thead style="background-color: #605ca8;color: white">
									<tr>
										<th width="1%">#</th>
										<th width="5%">Certificate No.<br><small>認定番号</small></th>
										<th width="5%">Desc<br><small>内容</small></th>
										<th width="5%">From<br><small>有効日付</small></th>
										<th width="5%">To<br><small>無効日付</small></th>
										<th width="5%">Emp<br><small>従業員</small></th>
										<th width="1%">Status<br><small>ステイタス</small></th>
										<th width="1%">Leader QA<br><small>品質保証リーダー</small></th>
										<th width="1%">Staff QA<br><small>品質保証スタッフ</small></th>
										<th width="1%">Foreman Produksi<br><small>組立工長</small></th>
										<th width="1%">Foreman QA<br><small>品質保証工長</small></th>
										<th width="1%">Chief QA<br><small>品質保証課長</small></th>
										<th width="5%">Action<br><small>アクション</small></th>
										<!-- <th width="5%">Approval<br><small>加工承認</small></th> -->
									</tr>
								</thead>
								<tbody id="bodyTableCode">
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		setInterval(fillList,600000);

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
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
		$('#loading').show();
		var data = {
			// tanggal_from:$('#tanggal_from').val(),
			// tanggal_to:$('#tanggal_to').val(),
			// status:$('#status').val(),
			code:$('#code').val().split('_')[1],
		}
		$.get('{{ url("fetch/qa/certificate/code/inprocess") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableCode').DataTable().clear();
				$('#tableCode').DataTable().destroy();
				$('#bodyTableCode').html("");
				var tableData = "";
				var index = 1;
				var active = 0, renewal = 0, non_acitve = 0, expired = 0;;

				$.each(result.datas, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="padding:5px !important;text-align:right">'+ index +'</td>';
					tableData += '<td style="padding:5px !important">'+ (value.certificate_code || 'YMPI-QA-'+value.code+'-'+value.code_number+'-'+value.number) +'</td>';
					tableData += '<td style="padding:5px !important">'+ value.certificate_desc +' - '+value.names+'</td>';
					tableData += '<td style="padding:5px !important;text-align:right">'+ (value.periode_from || '') +'</td>';
					tableData += '<td style="padding:5px !important;text-align:right">'+ (value.periode_to || '') +'</td>';
					tableData += '<td style="padding:5px !important">'+ (value.employee_id || "") +'<br>'+(value.name || "")+'</td>';
					if (value.status == '1') {
						var active_label = 'font-weight:bold;background-color:RGB(204,255,255)';
						var active_status = 'Active<br><small>有効</small>';
						active++;
					}else if(value.status == '0'){
						var active_label = 'font-weight:bold;background-color:#6b6b6b;color:white';
						var active_status = 'Inactive';
						non_acitve++;
					}else if(value.status == '3'){
						var active_label = 'font-weight:bold;background-color:RGB(255,204,255)';
						var active_status = 'Expired<br><small>無効</small>';
						expired++;
					}else if(value.status == '2'){
						var active_label = 'font-weight:bold;background-color:#ffebcc';
						var active_status = 'Renewal<br></small>更新必要</small>';
						renewal++;
					}
					tableData += '<td style="padding:5px !important;'+active_label+';text-align:center">'+ active_status +'</td>';

					var approval_list = [];
					for (var i = 0; i < result.approval.length; i++) {
						if (result.approval[i][0].certificate_id == value.certificate_id) {
							for (var j = 0; j < result.approval[i].length;j++) {
								approval_list.push(result.approval[i][j].remark);
								// if (result.approval[i][j].certificate_approval_id != certificate_approval_id) {
								// 	certificate_approval_id = result.approval[i][j].certificate_approval_id;
								// }
							}
						}
					}

					if (approval_list.indexOf('Leader QA') != -1) {
						for (var i = 0; i < result.approval.length; i++) {
							if (result.approval[i][0].certificate_id == value.certificate_id) {
								for (var j = 0; j < result.approval[i].length;j++) {
									if (result.approval[i][j].remark == 'Leader QA') {
										if (result.approval[i][j].approver_status == null) {
											if (result.approval[i][j].keutamaan == 'utama') {
												var certificate_id_approve = '';
												for(var u = 0; u < result.utamas.length;u++){
													if (result.utamas[u].certificate_approval_id == result.approval[i][j].certificate_approval_id) {
														certificate_id_approve = result.utamas[u].certificate_id;
													}
												}
												var urls = '{{url("approval/qa/certificate/inprocess/Leader QA")}}';
												tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#dd4b39;text-align:center;"><a style="text-decoration:none;color:white" target="_blank" href="'+urls+'">'+result.approval[i][j].approver_names+'<br>Waiting<br><small>承認待ち</small></a></td>';
											}else{
												tableData += '<td style="padding:5px !important;background-color:white;"></td>';
											}
										}else{
											tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#00a65a;text-align:center">'+result.approval[i][j].approver_names+'<br>'+result.approval[i][j].approved_ats+'</td>';
										}
									}
								}
							}
						}
					}else{
						if (value.status == 0) {
							tableData += '<td style="padding:5px !important;background-color:white;"></td>';
						}else{
							tableData += '<td style="padding:5px !important;background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;text-align:center">None<br><small>無し</small></td>';
						}
					}

					if (approval_list.indexOf('Staff QA') != -1) {
						for (var i = 0; i < result.approval.length; i++) {
							if (result.approval[i][0].certificate_id == value.certificate_id) {
								for (var j = 0; j < result.approval[i].length;j++) {
									if (result.approval[i][j].remark == 'Staff QA') {
										if (result.approval[i][j].approver_status == null) {
											if (result.approval[i][j].keutamaan == 'utama') {
												var urls = '{{url("approval/qa/certificate/inprocess/Staff QA")}}';
												tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#dd4b39;text-align:center;"><a style="text-decoration:none;color:white" target="_blank" href="'+urls+'">'+result.approval[i][j].approver_names+'<br>Waiting<br><small>承認待ち</small></a></td>';
											}else{
												tableData += '<td style="padding:5px !important;background-color:white;"></td>';
											}
										}else{
											tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#00a65a;text-align:center">'+result.approval[i][j].approver_names+'<br>'+result.approval[i][j].approved_ats+'</td>';
										}
									}
								}
							}
						}
					}else{
						if (value.status == 0) {
							tableData += '<td style="padding:5px !important;background-color:white;"></td>';
						}else{
							tableData += '<td style="padding:5px !important;background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;text-align:center">None<br><small>無し</small></td>';
						}
					}

					if (approval_list.indexOf('Foreman Produksi') != -1 || approval_list.indexOf('Chief Produksi') != -1) {
						for (var i = 0; i < result.approval.length; i++) {
							if (result.approval[i][0].certificate_id == value.certificate_id) {
								for (var j = 0; j < result.approval[i].length;j++) {
									if (result.approval[i][j].remark == 'Foreman Produksi' || result.approval[i][j].remark == 'Chief Produksi') {
										if (result.approval[i][j].approver_status == null) {
											if (result.approval[i][j].keutamaan == 'utama') {
												var certificate_id_approve = '';
												for(var u = 0; u < result.utamas.length;u++){
													if (result.utamas[u].certificate_approval_id == result.approval[i][j].certificate_approval_id) {
														certificate_id_approve = result.utamas[u].certificate_id;
													}
												}
												var urls = '{{url("approval_all/qa/certificate/inprocess")}}'+'/'+result.approval[i][j].remark+'/'+certificate_id_approve;
												tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#dd4b39;text-align:center;"><a style="text-decoration:none;color:white" target="_blank" href="'+urls+'">'+result.approval[i][j].approver_names+'<br>Waiting<br><small>承認待ち</small></a></td>';
											}else{
												tableData += '<td style="padding:5px !important;background-color:white;"></td>';
											}
										}else{
											tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#00a65a;text-align:center">'+result.approval[i][j].approver_names+'<br>'+result.approval[i][j].approved_ats+'</td>';
										}
									}
								}
							}
						}
					}else{
						if (value.status == 0) {
							tableData += '<td style="padding:5px !important;background-color:white;"></td>';
						}else{
							tableData += '<td style="padding:5px !important;background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;text-align:center">None<br><small>無し</small></td>';
						}
					}

					if (approval_list.indexOf('Foreman QA') != -1) {
						for (var i = 0; i < result.approval.length; i++) {
							if (result.approval[i][0].certificate_id == value.certificate_id) {
								for (var j = 0; j < result.approval[i].length;j++) {
									if (result.approval[i][j].remark == 'Foreman QA') {
										if (result.approval[i][j].approver_status == null) {
											if (result.approval[i][j].keutamaan == 'utama') {
												var certificate_id_approve = '';
												for(var u = 0; u < result.utamas.length;u++){
													if (result.utamas[u].certificate_approval_id == result.approval[i][j].certificate_approval_id) {
														certificate_id_approve = result.utamas[u].certificate_id;
													}
												}
												var urls = '{{url("approval_all/qa/certificate/inprocess")}}'+'/'+result.approval[i][j].remark+'/'+certificate_id_approve;
												tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#dd4b39;text-align:center;"><a style="text-decoration:none;color:white" target="_blank" href="'+urls+'">'+result.approval[i][j].approver_names+'<br>Waiting<br><small>承認待ち</small></a></td>';
											}else{
												tableData += '<td style="padding:5px !important;background-color:white;"></td>';
											}
										}else{
											tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#00a65a;text-align:center">'+result.approval[i][j].approver_names+'<br>'+result.approval[i][j].approved_ats+'</td>';
										}
									}
								}
							}
						}
					}else{
						if (value.status == 0) {
							tableData += '<td style="padding:5px !important;background-color:white;"></td>';
						}else{
							tableData += '<td style="padding:5px !important;background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;text-align:center">None<br><small>無し</small></td>';
						}
					}

					if (approval_list.indexOf('Chief QA') != -1) {
						for (var i = 0; i < result.approval.length; i++) {
							if (result.approval[i][0].certificate_id == value.certificate_id) {
								for (var j = 0; j < result.approval[i].length;j++) {
									if (result.approval[i][j].remark == 'Chief QA') {
										if (result.approval[i][j].approver_status == null) {
											if (result.approval[i][j].keutamaan == 'utama') {
												var certificate_id_approve = '';
												for(var u = 0; u < result.utamas.length;u++){
													if (result.utamas[u].certificate_approval_id == result.approval[i][j].certificate_approval_id) {
														certificate_id_approve = result.utamas[u].certificate_id;
													}
												}
												var urls = '{{url("approval_all/qa/certificate/inprocess/Chief QA")}}'+'/'+certificate_id_approve;
												tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#dd4b39;text-align:center;"><a style="text-decoration:none;color:white" target="_blank" href="'+urls+'">'+result.approval[i][j].approver_names+'<br>Waiting<br><small>承認待ち</small></a></td>';
											}else{
												tableData += '<td style="padding:5px !important;background-color:white;"></td>';
											}
										}else{
											tableData += '<td style="padding:5px !important;color:white;font-weight:bold;font-size:11px;background-color:#00a65a;text-align:center">'+result.approval[i][j].approver_names+'<br>'+result.approval[i][j].approved_ats+'</td>';
										}
									}
								}
							}
						}
					}else{
						if (value.status == 0) {
							tableData += '<td style="padding:5px !important;background-color:white;"></td>';
						}else{
							tableData += '<td style="padding:5px !important;background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;text-align:center">None<br><small>無し</small></td>';
						}
					}


					if (value.status == '0') {
						tableData += '<td style="padding:5px !important"></td>';
					}else{
						if (value.status == '1') {
							tableData += '<td style="padding:5px !important;text-align:center">';
							tableData += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/inprocess/")}}/'+value.certificate_id+'">Detail <small>詳細</small></a>';
							var certificate_id_approve_resend = '';
							var remark_resend = '';
							var fully_approved = '';
							for (var i = 0; i < result.approval.length; i++) {
								if (result.approval[i][0].certificate_id == value.certificate_id) {
									for (var j = 0; j < result.approval[i].length;j++) {
										if (result.approval[i][j].remark == 'Leader QA') {
											if (result.approval[i][j].approver_status == null) {
												if ('{{$role}}'.match(/QA/gi) || '{{$role}}'.match(/MIS/gi)) {
													tableData += '<a class="btn btn-warning btn-xs" target="_blank" href="{{url("review/qa/certificate/inprocess")}}/'+value.certificate_id+'/Leader QA">Edit <small>更新</small></a>';
												}
											}else{
												
											}
										}
										certificate_id_approve_resend = result.approval[i][j].certificate_approval_id;
										if (result.approval[i][j].keutamaan == 'utama') {
											remark_resend = result.approval[i][j].remark;
										}
										if (result.approval[i][j].remark == 'Chief QA' && result.approval[i][j].approver_status != null) {
											fully_approved = 'Full';
										}
										if (result.approval[i][j].remark == 'Leader QA' && result.approval[i][j].approver_status == null) {
											fully_approved = 'Full';
										}
										// for(var u = 0; u < result.utamas.length;u++){
										// 	if (result.utamas[u].certificate_approval_id == result.approval[i][j].certificate_approval_id) {
										// 		// certificate_id_approve = result.utamas[u].certificate_id;
										// 		remark_resend = result.utamas[u].rema;
										// 	}
										// }
									}
								}
							}
							if ('{{$role}}'.match(/QA/gi) || '{{$role}}'.match(/MIS/gi)) {
								tableData += '<a class="btn btn-info btn-xs" target="_blank" href="{{url("renew/qa/certificate/inprocess/")}}/'+value.certificate_id+'">Renew <small>更新必要</small></a>';
							}
							if ('{{$role}}'.match(/QA/gi) || '{{$role}}'.match(/MIS/gi)) {
								// tableData += '<button class="btn btn-danger btn-xs" onclick="deactivate(\''+value.certificate_id+'\')">Deactivate<br><small>非アクティブ化</small></button>';
							}
							if (fully_approved == '') {
								// tableData += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-success"  onclick="resendEmail(\''+certificate_id_approve_resend+'\',\''+remark_resend+'\');">Resend Email</a>';
							}
							tableData += '</td>';
						}else if(value.status == '2' || value.status == '3'){
							tableData += '<td style="padding:5px !important;text-align:center">';
							tableData += '<a class="btn btn-primary btn-xs" target="_blank" href="{{url("print/qa/certificate/inprocess/")}}/'+value.certificate_id+'">Detail <small>詳細</small></a>&nbsp;';
							if ('{{$role}}'.match(/QA/gi) || '{{$role}}'.match(/MIS/gi)) {
								tableData += '<a class="btn btn-info btn-xs" target="_blank" href="{{url("renew/qa/certificate/inprocess/")}}/'+value.certificate_id+'">Renew <small>更新必要</small></a>';
							}
							if ('{{$role}}'.match(/QA/gi) || '{{$role}}'.match(/MIS/gi)) {
								tableData += '<button class="btn btn-danger btn-xs" onclick="deactivate(\''+value.certificate_id+'\')">Deactivate<br><small>非アクティブ化</small></button>';
							}
							tableData += '</td>';
						}
					}
					// for (var i = 0; i < result.approval.length; i++) {
					// 	if (result.approval[i][0].certificate_id == value.certificate_id) {
					// 		for (var j = 0; j < result.approval[i].length;j++) {
					// 			approval_list.push(result.approval[i][j].remark);
					// 			if (result.approval[i][j].certificate_approval_id != certificate_approval_id) {
					// 				certificate_approval_id = result.approval[i][j].certificate_approval_id;
					// 			}
					// 		}
					// 	}
					// }
					// tableData += '<td style="padding:5px !important" rowspan="'+count[certificate_approval_id]+'">a</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableCode').append(tableData);

				$('#resumeActive').html(active);
				$('#resumeExpired').html(expired);
				$('#resumeRenewal').html(renewal);

				var table = $('#tableCode').DataTable({
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
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				$('#loading').hide();

				var category = [];
				var active = [];
				var renewal = [];
				var expired = [];
				for (var i = 0; i < result.charts.length;i++) {
						category.push(result.charts[i].description+' - '+result.charts[i].product);
						active.push({y:parseInt(result.charts[i].active),key:result.charts[i].description+'_'+result.charts[i].product});
						renewal.push({y:parseInt(result.charts[i].renewal),key:result.charts[i].description+'_'+result.charts[i].product});
						expired.push({y:parseInt(result.charts[i].expired),key:result.charts[i].description+'_'+result.charts[i].product});
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
						Highcharts.defaultOptions.legend.backgroundColor || '#1c1c1c',
						itemStyle: {
							fontSize:'12px',
						},
						reversed : true
					},	
				    title: {
				        text: '<b>CERTIFICATE INPROCESS</b>',
						style:{
							fontSize:"12px"
						}
				    },
				    subtitle: {
				        text: ''
				    },
				    plotOptions: {
				        series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,this.series.name,this.options.key);
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
				    series: [{
						type: 'column',
						data: expired,
						name: 'Expired',
						colorByPoint: false,
						color:'#f44336'
					},{
						type: 'column',
						data: renewal,
						name: 'Renewal',
						colorByPoint: false,
						color:'#f4c536'
					},{
						type: 'column',
						data: active,
						name: 'Active',
						colorByPoint: false,
						color:'#32a852'
					}
					]
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!','Attempt to retrieve data failed');
			}
		});
	}

	function ShowModal(category,series,code) {
		console.log(code);
		location.href='#div_detail'
		$('#code').val(code).trigger('change');
		document.getElementById("btnSearch").click();
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