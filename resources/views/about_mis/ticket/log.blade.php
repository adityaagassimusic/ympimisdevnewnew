@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(50,50,50);
		padding: 8px;
		vertical-align: middle;
		/*height: 40px;*/
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(50,50,50);
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
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
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<table style="width: 100%;">
							<tbody>
								<tr style="">
									<td style="text-align: center; width: 0.1%; font-weight: bold;" rowspan="3"><img src="{{ asset("images/logo_mirai.png") }}" style="height: 50px;"></td>
									<td style="text-align: center; width: 5%; font-weight: bold; font-size: 1.7vw;" rowspan="3">MIRAI UPDATES AND UPCOMING UPDATES RECORD</td>
									<td style="text-align: right; width: 0.5%;">Dokumen No</td>
									<td style="text-align: center; width: 0.1%;">:</td>
									<td style="text-align: left; width: 0.5%;">YMPI/IT/FM/020</td>
								</tr>
								<tr style="">
									<td style="text-align: right; width: 0.5%;">Revisi No</td>
									<td style="text-align: center; width: 0.1%;">:</td>
									<td style="text-align: left; width: 0.5%;">00</td>
								</tr>
								<tr style="">
									<td style="text-align: right; width: 0.5%;">Tanggal</td>
									<td style="text-align: center; width: 0.1%;">:</td>
									<td style="text-align: left; width: 0.5%;">22 Juli 2022</td>
								</tr>
							</tbody>
						</table>
						<hr style="margin-top: 10px; margin-bottom: 10px;">
						<div style="background-color: #605ca8; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
							<span style="font-weight: bold; font-size: 30px">NEW UPDATES</span>
						</div>
						<table id="tableUpdates" class="table table-bordered table-hover table-striped">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="width: 1%; text-align: center;">Update Date</th>
									<th style="width: 10%; text-align: left;">Description</th>
									<th style="width: 1%; text-align: center;">Category</th>
									<th style="width: 1%; text-align: center;">Guide
									line</th>
									<th style="width: 4%; text-align: left;">Person In Charge</th>
								</tr>
							</thead>
							<tbody id="tableUpdatesBody">
							</tbody>
						</table>
						<div style="background-color: #f9a825; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
							<span style="font-weight: bold; font-size: 30px">UPCOMING UPDATES</span>
						</div>
						<table id="tableUpcomings" class="table table-bordered table-hover table-striped">
							<thead style="background-color: #f9a825; color: white;">
								<tr>
									<th style="width: 1%; text-align: center;">Update Date</th>
									<th style="width: 10%; text-align: left;">Description</th>
									<th style="width: 1%; text-align: center;">Category</th>
									<th style="width: 1%; text-align: center;">Guidline</th>
									<th style="width: 4%; text-align: left;">Person In Charge</th>
								</tr>
							</thead>
							<tbody id="tableUpcomingsBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalUpload">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Ticket ID<span class="text-red"></span> :</label>
						<div class="col-xs-4">
							<input class="form-control" placeholder="" type="text" id="uploadTicketId" disabled>
						</div>
					</div>
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Guideline<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<input type="file" id="uploadFile">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success pull-right" onclick="uploadFile()" style="font-weight: bold;">UPLOAD</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		fetchTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var tickets = [];

	function modalUpload(ticket_id){
		$('#uploadTicketId').val(ticket_id);
		$('#uploadFile').val("");

		$('#modalUpload').modal('show');
	}

	function uploadFile(){
		var ticket_id = $('#uploadTicketId').val();
		var attachment  = $('#uploadFile').prop('files')[0];

		if(ticket_id == "" || attachment == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Select file to upload.');
			return false;
		}

		var file = $('#uploadFile').val().replace(/C:\\fakepath\\/i, '').split(".");

		var formData = new FormData();
		formData.append('ticket_id', ticket_id);
		formData.append('attachment', attachment);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/mis/guideline') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					fetchTable();
					$('#modalUpload').modal('hide');
					$('#uploadTicketId').val("");
					$('#loading').hide();
					openSuccessGritter('Success!', data.message);
					audio_ok.play();
				}
				else{
					audio_error.play();
					$('#loading').hide();
					openErrorGritter('Error!', data.message);
				}

			}
		});
	}

	function fetchTable(){
		var data = {

		}
		$.get('{{ url("fetch/ticket_log") }}', data, function(result, status, xhr){
			if(result.status){
				tickets = result.tickets;
				$('#tableUpdatesBody').html("");
				$('#tableUpcomingsBody').html("");
				$('#tableUpdates').DataTable().clear();
				$('#tableUpdates').DataTable().destroy();
				$('#tableUpcomings').DataTable().clear();
				$('#tableUpcomings').DataTable().destroy();

				for (var i = 0; i < tickets.length; i++) {
					var tableBody = "";
					tableBody += '<tr>';
					tableBody += '<td style="width: 1%; text-align: center;">'+tickets[i].finished_date+'</td>';
					tableBody += '<td style="width: 10%; text-align: left;"><a href="javascript:void(0)" onclick="detailTicket(\''+tickets[i].ticket_id+'\')">'+tickets[i].ticket_id+'</a><br>'+tickets[i].case_title+'</td>';
					tableBody += '<td style="width: 1%; text-align: center;">'+tickets[i].remark+'</td>';
					if(tickets[i].status == 'Finished'){
						if(tickets[i].guideline_file){
							tableBody += '<td style="width: 1%; text-align: center;"><a href="{{ asset('files/manual') }}/'+tickets[i].guideline_file+'"><i class="fa fa-file-pdf-o"></i> '+tickets[i].guideline_file+'</a></td>';
						}
						else{
							tableBody += '<td style="width: 1%; text-align: center;"><button class="btn btn-primary" onclick="modalUpload(\''+tickets[i].ticket_id+'\')"><i class="fa fa-upload"></i></button></td>';
						}
					}
					else{
						tableBody += '<td style="width: 1%; text-align: center;">-</td>';
					}
					if(tickets[i].pic_id){
						tableBody += '<td style="width: 4%; text-align: left;">'+tickets[i].pic_id+'<br>'+tickets[i].pic_name+'</td>';
					}
					else{
						tableBody += '<td style="width: 4%; text-align: left;">(To Be Decided)</td>';
					}
					tableBody += '</tr>';

					if(tickets[i].status == 'Finished'){
						$('#tableUpdatesBody').append(tableBody);
					}
					else{
						$('#tableUpcomingsBody').append(tableBody);
					}
				}

				$('.table').DataTable({
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
			else{

			}
		});
	}

	function detailTicket(ticket_id){
		window.open('{{ url("index/ticket/detail") }}'+'/'+ticket_id, '_blank');
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
