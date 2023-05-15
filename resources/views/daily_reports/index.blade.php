@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
table{
	border:1px solid black !important;
}
thead>tr>th{
	vertical-align: middle !important;
	text-align:center !important;
	border:1px solid black !important;
}
tbody>tr>td{
	border:1px solid black !important;
}
tfoot>tr>th{
	border:1px solid black !important;
}
.select2-container.select2-container--default.select2-container--open  {
	z-index: 5000;
}
.crop2 {
	overflow: hidden;
}
.crop2 img {
	height: 70px;
	margin: -2.7% 0 0 0 !important;
}
#tableTimelineBody > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
#loading { display: none; }
#alert { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Daily Report
		<small><span class="text-purple">??</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalTimeline();"><i class="fa fa-plus-square"></i> Create Timeline</button>
	</h1>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<table class="table table-bordered table-responsive" width="100%" id="tableTimeline">
						<thead>
							<tr>
								<th>Ticket</th>
								<th>Project Name</th>
								{{-- <th>ID</th> --}}
								<th>Name</th>
								<th>Date</th>
								<th>Category</th>
								<th>Description</th>
								<th>Duration</th>
								<th>Att</th>
								<th>Progress</th>
							</tr>
						</thead>
						<tbody id="tableTimelineBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalTimeline" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Add Timeline<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC ID<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control" placeholder="Enter PIC ID" id="addPicId" disabled="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC Name<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter PIC Name" id="addPicName" disabled="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ticket ID<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<select class="form-control select2" id="addTicketId" data-placeholder="Select ID" style="width: 100%;" onchange="checkTicket()">
										<option value="0"></option>
										<option value="MIS00000">MIS00000 - Non Project/Ticket</option>
										@foreach($tickets as $ticket)
										<option value="{{ $ticket->ticket_id }}">{{ $ticket->ticket_id }} - {{ $ticket->case_title }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Date<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="addDate" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" id="addCategory" data-placeholder="Select Category" style="width: 100%;" onchange="checkTicket()">
										<option></option>
										@foreach($timeline_categories as $timeline_category)
										<option value="{{ $timeline_category }}">{{ $timeline_category }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Description<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="addDescription"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Duration<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<input type="text" id="addDuration" class="form-control timepicker" value="00:15">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Progress %<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" placeholder="Enter Progress %" id="addProgress">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Progress Category%:</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" placeholder="Enter Progress %" id="addProgressCategory">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Attachment<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="addAttachment" multiple="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL </button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="addTimeline()">ADD TIMELINE </i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	var no = 2;
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		fetchTimeline();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function checkTicket(){

		var ticket = $("#addTicketId").val();
		var ticket_category = $("#addCategory").val();

		var data = {
			ticket:ticket,
			ticket_category:ticket_category
		}

		if(ticket == 'MIS00000'){
			$('#addProgress').val(100);
			$('#addProgressCategory').val(0);
		}
		else if(ticket == "0" || ticket == ""){
			return false;
		}
		else{
			$.get('{{ url("fetch/ticket/timeline") }}',data, function(result, status, xhr){
				if(result.status){
					$('#addProgress').val(result.ticket_timelines.progress);
					if (result.ticket_category != null) {
						$('#addProgressCategory').val(result.ticket_category.progress_category);
					}else{
						$('#addProgressCategory').val();
					}
				}
				else{
					alert('Attempt to retrieve data failed.');
				}

			});
		}
	}


	function modalTimeline(){
		clearAll();
		$('#addPicId').val('{{ Auth::user()->username }}');
		$('#addPicName').val('{{ Auth::user()->name }}');
		$('#modalTimeline').modal('show');

		$('#addDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#addCategory').select2({
			dropdownParent: $('#modalTimeline')
		});

		$('#addTicketId').select2({
			dropdownParent: $('#modalTimeline')
		});

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
	}

	function addTimeline(){
		var ticket_id = $('#addTicketId').val();
		var pic_id = $('#addPicId').val();
		var pic_name = $('#addPicName').val();
		var category = $('#addCategory').val();
		var date = $('#addDate').val();
		var description = $('#addDescription').val();
		var duration = $('#addDuration').val();
		var progress = $('#addProgress').val();
		var progress_category = $('#addProgressCategory').val();
		var attachment = $('#addAttachment').prop('files')[0];
		var file = $('#addAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

		if(date == '' || description == '' || duration == '00:00' || progress == ''){
			audio_error.play();
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus terisi.');
			return false;
		}

		var formData = new FormData();

		formData.append('ticket_id', ticket_id);
		formData.append('pic_id', pic_id);
		formData.append('pic_name', pic_name);
		formData.append('category', category);
		formData.append('date', date);
		formData.append('description', description);
		formData.append('duration', duration);
		formData.append('progress', progress);
		formData.append('progress_category', progress_category);
		formData.append('attachment', attachment);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('input/ticket/timeline') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					clearAll();
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
					$('#alert').show();
					$('#modalTimeline').modal('hide');
					fetchTimeline();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}
			}
		});
	}

	function fetchTimeline(){
		// $.get('{{ url("fetch/ticket/timeline/-") }}', function(result, status, xhr){
			// if(result.status){
				// var tableTimelineBody = "";
				// $('#tableTimelineBody').html("");
				$('#tableTimeline').DataTable().clear();
				$('#tableTimeline').DataTable().destroy();

				// $.each(result.ticket_timelines, function(key, value){
				// 	tableTimelineBody += '<tr>';
				// 	tableTimelineBody += '<td style="width: 0.1%;">'+value.ticket_id+'</td>';
				// 	tableTimelineBody += '<td style="width: 3%;">'+(value.project_name || 'Non Project/Ticket')+'</td>';
				// 	// tableTimelineBody += '<td style="width: 0.1%;">'+value.pic_id+'</td>';
				// 	tableTimelineBody += '<td style="width: 5%;">'+value.pic_name+'</td>';
				// 	tableTimelineBody += '<td style="text-align: right; width: 1%;">'+value.timeline_date+'</td>';
				// 	tableTimelineBody += '<td style="width: 0.1%;">'+value.timeline_category+'</td>';
				// 	tableTimelineBody += '<td style="width: 10%;">'+value.timeline_description+'</td>';
				// 	tableTimelineBody += '<td style="text-align: right; width: 0.1%;">'+value.duration+'</td>';
				// 	if(value.timeline_attachment != ""){
				// 		tableTimelineBody += '<td style="text-align: center; width: 0.1%;"><a href="javascript:void(0)" id="'+ value.id +'" onClick="downloadAtt(id)">Open</a></td>';

				// 	}
				// 	else{
				// 		tableTimelineBody += '<td style="text-align: center; width: 0.1%;">-</td>';
				// 	}
				// 	tableTimelineBody += '<td style="text-align: right; width: 0.1%;">'+value.progress_update+'%</td>';
				// 	tableTimelineBody += '</tr>';
				// });

				// $('#tableTimelineBody').append(tableTimelineBody);



				var table = $('#tableTimeline').DataTable({
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
					"processing": true,
					"serverSide": true,
					"ajax": {
						"type" : "get",
						"url" : "{{ url('fetch/ticket/timeline/new') }}",
					},
					"columns": [
					{ "data": "ticket_id", "width": "2%" },
					{ "data": "project_name", "width": "10%" },
					{ "data": "pic_name", "width": "10%" },
					{ "data": "timeline_date", "width": "8%" },
					{ "data": "timeline_category", "width": "5%" },
					{ "data": "timeline_description", "width": "20%" },
					{ "data": "duration", "width": "2%" },
					{ "data": "timeline_attachment", "width": "1%" },
					{ "data": "progress_update", "width": "1%" },
					],
				});


				// $('#tableTimeline tfoot th').each( function () {
				// 	var title = $(this).text();
				// 	$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
				// });

				// table.columns().every( function () {
				// 	var that = this;
				// 	$( 'input', this.footer() ).on( 'keyup change', function () {
				// 		if ( that.search() !== this.value ) {
				// 			that
				// 			.search( this.value )
				// 			.draw();
				// 		}
				// 	});
				// });
				// $('#tableTimeline tfoot tr').appendTo('#tableTimeline thead');
			}
			// else{
			// 	alert('Attempt to retrieve data failed.');
			// 	return false;
			// }
	// 	});
	// }

	function clearAll(){
		$('#loading').hide();
		$('#addTicketId').prop('selectedIndex', 0).change();
		$('#addPicId').val('');
		$('#addPicName').val('');
		$('#addDate').val('');
		$('#addDescription').val('');
		$('#addDuration').val('00:00');
		$('#addProgress').val('');
		$('#addProgressCategory').val('');
		$('#addCategory').prop('selectedIndex', 0).change();
		$('#addAttachment').val('');
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>
@endsection