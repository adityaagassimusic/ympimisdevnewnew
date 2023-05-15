@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

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
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">

							<div class="col-xs-6">
								<div class="col-xs-12" style="background-color: #BDD5EA; text-align: center; margin-bottom: 5px; color: black">
									<span style="font-weight: bold; font-size: 1.6vw;">List Approval</span>
								</div>
								<table id="TableListDisposal" class="table table-bordered table-striped table-hover">
									<thead style="background-color: #BDD5EA; color: black;">
										<tr>
											<th width="10%" style="text-align: center;">No</th>
											<th width="20%">Slip Disposal</th>
											<th width="40%">Vendor</th>
											<th width="30%">Tanggal Request</th>
										</tr>
									</thead>
									<tbody id="bodyTableListDisposal">
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>

							<div class="col-xs-6">
								<div class="col-xs-12" style="background-color: #FF9999; text-align: center; margin-bottom: 5px; color: black">
									<span style="font-weight: bold; font-size: 1.6vw;">List Disposal</span>
								</div>
								<table id="TableListDisposalLog" class="table table-bordered table-striped table-hover">
									<thead style="background-color: #FF9999; color: black;">
										<tr>
											<th width="10%" style="text-align: center;">No</th>
											<th width="20%">Slip Disposal</th>
											<th width="40%">Vendor</th>
											<th width="30%">Tanggal Disposal</th>
										</tr>
									</thead>
									<tbody id="bodyTableListDisposalLog">
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
	</div>
</section>

<div class="modal fade" id="ModalDetailDisposal" data-keyboard="false">
	<div class="modal-lg modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<div class="col-xs-12" style="background-color: #BDD5EA; text-align: center; margin-bottom: 5px; color: black">
						<span style="font-weight: bold; font-size: 1.6vw;" id="modal_slip_disposal"></span>
					</div>
					<div id="detail_data" style="padding-top: 30px"></div>
					<div id="detail_rincian"></div>
					<div id="detail_approval" style="padding-top: 30px"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalDetailDisposalLog" data-keyboard="false">
	<div class="modal-lg modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<ul class="nav nav-tabs">
						<div class="col-xs-12" style="background-color: #FF9999; text-align: center; margin-bottom: 5px; color: black">
							<span style="font-weight: bold; font-size: 1.6vw;" id="modal_slip_disposal_log"></span>
						</div>
					</ul>
					<div>
						<table id="TableDetailApproveDisposal" class="table table-bordered table-striped table-hover">
							<thead style="background-color: #FF9999; color: black;">
								<tr>
									<th width="10%" style="text-align: center;">No</th>
									<th width="10%">Slip Limbah</th>
									<th width="10%">PIC Limbah</th>
									<th width="20%">Jenis Limbah</th>
									<th width="10%">Kode Limbah</th>
									<th width="10%">Berat</th>
									<th width="20%">Vendor</th>
									<th width="10%">Tanggal Limbah Masuk</th>
								</tr>
							</thead>
							<tbody id="bodyTableDetailApproveDisposal">
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
					<div id="detail_approval_log" style="padding-top: 30px"></div>
				</div>
			</div>
		</div>
	</div>
</div>
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
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		DataList();
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

	function DataList(jenis, bulan){
		$("#loading").show();
		$('#list_disposal').hide();

		$.get('{{ url("fetch/request/disposal") }}', function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', 'Success !');
				$('#TableListDisposal').DataTable().clear();
				$('#TableListDisposal').DataTable().destroy();
				$('#bodyTableListDisposal').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data, function(key, value) {

					tableData += '<tr style="cursor: pointer" onclick="ViewRequest(\''+value.slip_disposal+'\');">';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td>'+value.slip_disposal+'</td>';
					tableData += '<td>'+(value.vendor || 'Tidak Ada Vendor Terpilih')+'</td>';
					tableData += '<td>'+(value.updated_at || '-')+'</td>';
					tableData += '</tr>';

				});
				$('#bodyTableListDisposal').append(tableData);

				var table = $('#TableListDisposal').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[{
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
						}]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'DataListing': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#TableListDisposalLog').DataTable().clear();
				$('#TableListDisposalLog').DataTable().destroy();
				$('#bodyTableListDisposalLog').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data_log, function(key, value) {

					tableData += '<tr style="cursor: pointer" onclick="ViewLog(\''+value.slip_disposal+'\');">';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td>'+value.slip_disposal+'</td>';
					tableData += '<td>'+(value.vendor || 'Tidak Ada Vendor Terpilih')+'</td>';
					tableData += '<td>'+(value.date_disposal || '-')+'</td>';
					tableData += '</tr>';

				});
				$('#bodyTableListDisposalLog').append(tableData);

				var table = $('#TableListDisposalLog').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[{
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
						}]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'DataListing': true	,
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
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function ViewRequest(slip_disposal){
		$('#ModalDetailDisposal').modal('show');
		$('#modal_slip_disposal').html(slip_disposal);

		var data = {
			slip_disposal : slip_disposal
		}

		$.get('{{ url("fetch/detail/request/disposal") }}', data, function(result, status, xhr){
			var a = '';
			var b = '';
			var c = '';
			var d = '';
			var e = '';

			$("#detail_rincian").empty();
			$("#detail_approval").empty();
			var isi = '';
			var index = 1;

			$.each(result.data.resumes, function(key, value) {
				b = value.jenis.split(",");
				c = value.quantity.split(",");
				d = value.slip.split(",");
				e = value.kode_limbah.split(",");

				if (result.data.resumes.length % 2 == 0) {
					isi += '<div class="col-xs-6"';

					isi += '<table class="table table-bordered table-striped table-hover" style="width: 50%; font-family: arial; border-collapse: collapse; text-align: center" cellspacing="0">';
					isi += '<tbody align="center">';
					isi += '<tr>';
					isi += '<th style="width: 1%; vertical-align: top"><table style="border-collapse: collapse; width: 100%;"><thead><tr align="center"><th colspan="4" style="border:1px solid black; font-size: 15px; background-color: #f6d965; height: 20; text-align: center;">Limbah '+b[0]+' ('+e[0]+')</th></tr><tr align="center"><td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">NO. LIMBAH</td><td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">SLIP</td><td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">BERAT</td><td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">JML. JUMBO BAG</td></tr></thead>';
					var p = 1;
					for (var i = 0; i < b.length; i++) {
						isi += '<tr align="center"><td style="border:1px solid black; font-size: 13px; height: 20;">'+ p++ +'</td><td style="border:1px solid black; font-size: 13px; height: 20;">'+d[i]+'</td><td style="border:1px solid black; font-size: 13px; height: 20;">'+c[i]+' KG</td><td style="border:1px solid black; font-size: 13px; height: 20;">1</td></tr>';
					}
					
					isi += '</table>';
					isi += '</th>';
					isi += '</tr>';
					isi += '</tbody>';
					isi += '</table><br>';

					isi += '</div>';
				}else{
					isi += '<div class="col-xs-4"';

					isi += '<table class="table table-bordered table-striped table-hover" style="width: 50%; font-family: arial; border-collapse: collapse; text-align: center" cellspacing="0">';
					isi += '<tbody align="center">';
					isi += '<tr>';
					isi += '<th style="width: 1%; vertical-align: top"><table style="border-collapse: collapse; width: 100%;"><thead><tr align="center"><th colspan="4" style="border:1px solid black; font-size: 15px; background-color: #f6d965; height: 20; text-align: center;">Limbah '+b[0]+' ('+e[0]+')</th></tr><tr align="center"><td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">NO. LIMBAH</td><td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">SLIP</td><td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">BERAT</td><td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">JML. JUMBO BAG</td></tr></thead>';
					var p = 1;
					for (var i = 0; i < b.length; i++) {
						isi += '<tr align="center"><td style="border:1px solid black; font-size: 13px; height: 20;">'+ p++ +'</td><td style="border:1px solid black; font-size: 13px; height: 20;">'+d[i]+'</td><td style="border:1px solid black; font-size: 13px; height: 20;">'+c[i]+' KG</td><td style="border:1px solid black; font-size: 13px; height: 20;">1</td></tr>';
					}
					
					isi += '</table>';
					isi += '</th>';
					isi += '</tr>';
					isi += '</tbody>';
					isi += '</table><br>';

					isi += '</div>';
				}
			});

			var approver_id = result.data.approval[0].approver_id;

			if (approver_id == 'PI1210001') {
				isi += '<div class="col-sm-4 col-sm-offset-4"><center><select class="form-control select2" id="vendor_disposal" name="vendor_disposal" class="supplier_code" data-placeholder="Pilih Vendor" style="width: 100%"><option value="">&nbsp;</option>@foreach($vendor as $ven)<option value="{{$ven->short_name}}">{{$ven->short_name}}</option>@endforeach</select></center></div>';

				isi += '<div class="col-sm-4 col-sm-offset-4" style="padding-top: 10px"><center><div class="input-group"><div class="input-group-addon bg-green" style="border: none;"><i class="fa fa-calendar"></i></div><input type="text" id="tanggal_disposal" name="tanggal_disposal" class="form-control datepicker" style="width: 100%; text-align: center;" placeholder="Tanggal Disposal" required onChange="ChangeDate(\''+slip_disposal+'\', this.value)"></div></center></div>';
			}

			isi += '<table style="width: 100%">';
			isi += '<tr>';
			isi += '<td style="width: 10%; font-weight: bold; color: black; text-align: center"><a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('verivikasi/email/wwt/approve') }}/'+slip_disposal+'/'+approver_id+'">&nbsp; Approve(承認) &nbsp;</a></td>';
			isi += '<td style="width: 10%; font-weight: bold; color: black; text-align: center"><a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;" href=" {{ url('verivikasi/email/wwt/reject') }}/'+slip_disposal+'/'+approver_id+'">&nbsp;&nbsp;&nbsp; Reject(却下) &nbsp;&nbsp;&nbsp;</a></td>';
			isi += '</tr>';

			var detail_approval = '';

			var header_1 = result.data.isi_approval[0].remark;
			var nama_1 = result.data.isi_approval[0].approver_name;
			var status_1 = result.data.isi_approval[0].status;
			var approve_1 = result.data.isi_approval[0].approved_at;

			var header_2 = result.data.isi_approval[1].remark;
			var nama_2 = result.data.isi_approval[1].approver_name;
			var status_2 = result.data.isi_approval[1].status;
			var approve_2 = result.data.isi_approval[1].approved_at;

			var wait = 'Waiting';

			detail_approval += '<center>';
			detail_approval += '<table class="table table-bordered table-striped table-hover" style="width: 40%; font-family: arial; border-collapse: collapse; text-align: center;" cellspacing="0">';
			detail_approval += '<tr>';
			detail_approval += '<th style="border:1px solid black; font-size: 12px; font-weight: bold; height: 40px; background-color:  #e8daef; text-align: center; width: 30%">'+header_1+'</th>';
			detail_approval += '<th style="border:1px solid black; font-size: 12px; font-weight: bold; height: 40px; background-color:  #e8daef; text-align: center; width: 30%">'+header_2+'</th>';
			detail_approval += '</tr>';

			detail_approval += '<tr style="height: 50px">';
			detail_approval += '<td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 50px; width: 30%"><span>'+ (status_1 || wait) +'</span><br>'+ (approve_1 || '') +'</td>';
			detail_approval += '<td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 50px; width: 30%"><span>'+ (status_2 || wait) +'</span><br>'+ (approve_2 || '') +'</td>';
			detail_approval += '</tr>';

			detail_approval += '<tr>';
			detail_approval += '<td style="border:1px solid black; font-size: 10px; font-weight: bold; height: 30px; background-color:  #e8daef; width: 30%">'+nama_1+'</td>';
			detail_approval += '<td style="border:1px solid black; font-size: 10px; font-weight: bold; height: 30px; background-color:  #e8daef; width: 30%">'+nama_2+'</td>';
			detail_approval += '</tr>';
			detail_approval += '</table>';
			detail_approval += '</center>';

			$('#detail_rincian').append(isi);
			$('#detail_approval').append(detail_approval);
			$('#tanggal_disposal').datepicker({
				utoclose: true,
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true
			});

			$('.select2').select2({
				dropdownParent: $('#detail_rincian'),
				allowClear : true,
			});

			$('#detail_data').html("");
			var tableData = "";
			tableData += '<div class="col-xs-12">';
			tableData += '<table style="width: 100%;">';

			tableData += '<tr>';
			tableData += '<th style="width: 20%;">Tanggal Disposal</th>';
			tableData += '<th style="width: 5%;">:</th>';
			tableData += '<th style="width: 75%;"><span style="color:red" id="date_disposal">'+result.data.resumes[0].date_disposal+'</span></th>';
			tableData += '</tr>';

			tableData += '<tr>';
			tableData += '<th style="width: 20%;">Nama Vendor</th>';
			tableData += '<th style="width: 5%;">:</th>';
			tableData += '<th style="width: 75%;"><span style="color:red" id="nama_vendor">'+result.data.resumes[0].vendor+'</span></th>';
			tableData += '</tr>';

			tableData += '</table>';
			tableData += '</div>';
			$('#detail_data').append(tableData);
		});
}

function ViewLog(slip_disposal){
	$('#ModalDetailDisposalLog').modal('show');
	$('#modal_slip_disposal_log').html(slip_disposal);

	var data = {
		slip_disposal : slip_disposal
	}

	$.get('{{ url("fetch/detail/request/disposal") }}', data, function(result, status, xhr){
		$("#detail_rincian_log").empty();
		$("#detail_approval_log").empty();
		var isi = '';
		var index = 1;

		$('#TableDetailApproveDisposal').DataTable().clear();
		$('#TableDetailApproveDisposal').DataTable().destroy();
		$('#bodyTableDetailApproveDisposal').html("");
		var tableData = "";
		var index = 1;
		$.each(result.data.resumes_all, function(key, value) {

			// var slip = value.slip.split(",");
			// var pic = value.pic.split(",");
			// var jenis_limbah = value.jenis.split(",");
			// var berat = value.quantity.split(",");
			// var vendor = value.vendor.split(",");


			tableData += '<tr>';
			tableData += '<td style="text-align: center">'+ index++ +'</td>';
			tableData += '<td>'+value.slip+'</td>';
			tableData += '<td>'+value.pic+'</td>';
			tableData += '<td>'+value.jenis+'</td>';
			tableData += '<td>'+value.kode_limbah+'</td>';
			tableData += '<td>'+value.berat+' KG</td>';
			tableData += '<td>'+value.vendor+'</td>';
			tableData += '<td>'+value.date_in+'</td>';
			tableData += '</tr>';

		});
		$('#bodyTableDetailApproveDisposal').append(tableData);

		var table = $('#TableDetailApproveDisposal').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
			'buttons': {
				buttons:[{
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
				}]
			},
			'paging': true,
			'lengthChange': true,
			'pageLength': 10,
			'DataListing': true	,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});


		var detail_approval = '';

		var header_1 = result.data.isi_approval[0].remark;
		var nama_1 = result.data.isi_approval[0].approver_name;
		var status_1 = result.data.isi_approval[0].status;
		var approve_1 = result.data.isi_approval[0].approved_at;

		var header_2 = result.data.isi_approval[1].remark;
		var nama_2 = result.data.isi_approval[1].approver_name;
		var status_2 = result.data.isi_approval[1].status;
		var approve_2 = result.data.isi_approval[1].approved_at;

		var report = "{{ url('data_file/wwt/disposal/') }}"+'/'+slip_disposal+'.pdf';

		detail_approval += '<center>';
		detail_approval += '<a href="'+report+'" target="_blank" class="btn btn-warning"  data-toggle="tooltip" title="Dokumen PDF"><i class="fa fa-file-pdf-o"></i> Dokumen PDF</a><br><br>';
		detail_approval += '<table style="width: 30%; font-family: arial; border-collapse: collapse; text-align: center;" cellspacing="0">';

		detail_approval += '<tr>';
		detail_approval += '<th style="border:1px solid black; font-size: 12px; font-weight: bold; height: 40px; background-color:  #e8daef; text-align: center; width: 20px">'+header_1+'</th>';
		detail_approval += '<th style="border:1px solid black; font-size: 12px; font-weight: bold; height: 40px; background-color:  #e8daef; text-align: center; width: 20px">'+header_2+'</th>';
		detail_approval += '</tr>';

		detail_approval += '<tr style="height: 50px">';
		detail_approval += '<td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 50px; width: 20px"><span>'+ status_1 +'</span><br>'+ approve_1 +'</td>';
		detail_approval += '<td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 50px; width: 20px"><span>'+ status_2 +'</span><br>'+ approve_2 +'</td>';
		detail_approval += '</tr>';

		detail_approval += '<tr>';
		detail_approval += '<td style="border:1px solid black; font-size: 10px; font-weight: bold; height: 30px; background-color:  #e8daef; width: 20px">'+nama_1+'</td>';
		detail_approval += '<td style="border:1px solid black; font-size: 10px; font-weight: bold; height: 30px; background-color:  #e8daef; width: 20px">'+nama_2+'</td>';
		detail_approval += '</tr>';
		detail_approval += '</table>';
		detail_approval += '</center>';

		$('#detail_rincian_log').append(isi);
		$('#detail_approval_log').append(detail_approval);
	});
}

function ChangeDate(slip_disposal, tanggal){
	var vendor = $('#vendor_disposal').val();

	var data = {
		slip_disposal : slip_disposal,
		tanggal : tanggal,
		vendor : vendor
	}

	$.post('{{ url("insert/date/disposal/limbah") }}', data, function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success','Tanggal Disposal Dipilih!');
			$('#date_disposal').html(result.resumes[0].date_disposal);
			$('#nama_vendor').html(result.resumes[0].vendor);
		}else{
			openErrorGritter('Error!', result.message);
		}
	});
}
</script>
@endsection