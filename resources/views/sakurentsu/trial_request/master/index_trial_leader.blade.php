@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
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
		border:1px solid rgb(150,150,150);
		vertical-align: middle;

	}

	.btn { margin: 2px; }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center style="position: absolute; top: 45%; left: 35%;">
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-refresh"></i> &nbsp; Please Wait ...</span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<table id="listTable" class="table table-bordered table-striped table-hover" style="width: 100%">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>#</th>
								<th>Submission Date</th>
								<th>Form Number</th>
								<th>Subject</th>
								<th>Requester</th>
								<th>Trial To</th>
								<th>Purpose</th>
								<th>Trial Date</th>
								<th>Reference No</th>
								<th>Leader</th>
								<th>Action</th>
								<th>Report</th>
							</tr>
						</thead>
						<tbody id="listTableBody">
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalFile">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="sk_num"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableFile">
						<tbody id='bodyFile'></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #00a65a">
				<center><h2 style="margin: 0px"><b>Send Result Notification</b></h2></center>
			</div>
			<div class="modal-body">
				<input type="hidden" id="email_form_id">
				<input type="hidden" id="trial_sakurentsu_number">
				<center style="font-size: 18px"><b><i class="fa fa-book"></i> <span id="trial_number_mail"></span></b><br>Select PIC to fill Trial Result</center>
				<button class="btn btn-success" id="add_pic" onclick="add_pic()"><i class="fa fa-plus"></i> Add PIC</button>
				<table class="table table-striped" id="table_pic">
					<thead>
						<tr>
							<th><center>PIC</center></th>
							<th style="width: 10%"><center>#</center></th>
						</tr>
					</thead>
					<tbody id="body_pic"></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" onclick="sendMail()"><i class="fa fa-check"></i> SAVE</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="uploadQCMOdal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_qc">
				<div class="modal-header" style="background-color: #00a65a">
					<center><h2 style="margin: 0px"><b>Upload QC Report</b></h2></center>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12">
							<input type="hidden" id="qc_form_id" name="qc_form_id">
							<center style="font-size: 18px"><b><i class="fa fa-book"></i> <span id="trial_number_qc"></span></b></center>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-4" style="padding: 0px;" align="right">
								<span style="font-weight: bold; font-size: 16px;">QC Report File : <span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6">
								<input type="file" id="qc_report_file" name="qc_report_file" accept="application/pdf">
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-4" style="padding: 0px;" align="right">
								<span style="font-weight: bold; font-size: 16px;">QC Report Status : <span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6">
								<select class="form-control select3" id="qc_report_status" name="qc_report_status" data-placeholder='Select Report Status' style="width: 100%">
									<option value=""></option>
									<option value="Approval">Approval</option>
									<option value="OK">OK</option>
									<option value="Not OK">Not OK</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" type="submit"><i class="fa fa-upload"></i> Upload</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("ckeditor/ckeditor.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var file = [];

	var no_penerima = 1;

	jQuery(document).ready(function() {

		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

		fetchTable();
	});

	$('.select4').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#modalNew'),
	});

	$('.select5').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#modalNew'),
	});

	$('.select3').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#uploadQCMOdal'),
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


	function fetchTable(){
		$('#loading').show();

		$.get('{{ url("fetch/trial_request/leader") }}', function(result, status, xhr){
			if(result.status){
				// ----------- INTERNAL TRIAL REQUEST

				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";

				$.each(result.trial, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+value.submission_date+'</td>';
					listTableBody += '<td style="width:1%;">'+value.form_number+'</td>';
					listTableBody += '<td style="width:3%;">'+value.subject+'</td>';
					listTableBody += '<td style="width:2%;">'+value.requester_name+'</td>';
					listTableBody += '<td style="width:1%;">'+value.department+'</td>';
					listTableBody += '<td style="width:1%;">'+value.trial_purpose+'</td>';
					listTableBody += '<td style="width:3%;">'+value.trial_date+'</td>';
					listTableBody += '<td style="width:2%;">'+(value.sakurentsu_number || '')+'</td>';
					listTableBody += '<td style="width:3%;">'+value.fill_by+'</td>';

					listTableBody += '<td style="width:2%;"><center>';

					if (value.status == 'resulting' && value.fill_by.indexOf("{{ Auth::user()->name }}") >= 0) {
						listTableBody += '<a class="btn btn-xs btn-primary" target="_blank" href="{{ url("result/sakurentsu/trial_request") }}/'+value.form_number+'/'+value.section+'"><i class="fa fa-pencil"></i> Tulis Report Trial</a>';
					}

					listTableBody += '</center></td>';

					listTableBody += '<td style="width:2%;"><center>';
					listTableBody += '  <a class="btn btn-xs btn-danger" target="_blank" href="{{ url("uploads/sakurentsu/trial_req/report") }}/Report_'+value.form_number+'.pdf"><i class="fa fa-file-pdf-o"></i> Report</a>';
					listTableBody += '</center></td>';
					listTableBody += '</tr>';
				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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
					'pageLength': 20,
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

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}


	function getFileInfo(num, sk_num) {
		$("#sk_num").text(sk_num+" File(s)");

		$("#bodyFile").empty();

		body_file = "";
		$.each(file, function(key, value) {  
			if (sk_num == value.sk_number) {
				var obj = JSON.parse(value.file);
				var app = "";

				if (obj) {
					for (var i = 0; i < obj.length; i++) {
						body_file += "<tr>";
						body_file += "<td>";
						body_file += "<a href='../../uploads/sakurentsu/translated/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
						body_file += "</td>";
						body_file += "</tr>";
					}
				}
			}
		});

		$("#bodyFile").append(body_file);

		$("#modalFile").modal('show');
	}


	function add_mat() {
		var body = "";

		body += '<tr>';
		body += '<td style="padding-right: 10px"><input type="text" class="form-control mat" placeholder="Material"></td>';
		body += '<td style="padding-left: 10px"><input type="text" class="form-control qty" placeholder="Quantity"></td>';
		body += '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
		body += '</tr>';

		$("#body_mat").append(body);
	}

	function deleteMat(elem) {
		$(elem).closest('tr').remove();
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

