@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;		
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
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.dt-button-background {
		display: none !important;
		position: :static !important;
    }

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	#loading, #error { display: none; }

	.containers {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 15px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	  padding-top: 6px;
	}

	/* Hide the browser's default checkbox */
	.containers input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	  margin-top: 4px;
	}

	/* On mouse-over, add a grey background color */
	.containers:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.containers input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.containers input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.containers .checkmark:after {
	  left: 9px;
	  top: 5px;
	  width: 5px;
	  height: 10px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}

	#tableReport tr th {
		color: #333 !important;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>
		<a class="btn btn-success btn-sm pull-right" href="{{route('indexApprovalPresidentDirector')}}">
			<i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Buat Pengajuan
		</a>
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>			
	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12" style="margin-bottom: 10px">
							<center style="background-color: rgb(126,86,134); color: #fff;">
								<span style="font-size: 17px;font-weight: bold;padding: 5px">Filter</span>
							</center>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Date From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Date To</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Department</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Department" id="department">
									<option value=""></option>
									@foreach ($departments as $dept)
									<option value="{{ $dept->department_name }}">{{ $dept->department_name }}</option>										
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Status</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Status" id="status">
									<option value=""></option>
									<option value="Requested">Requested</option>									
									<option value="Completed">Completed</option>									
								</select>
							</div>
						</div>
						{{-- <div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Category</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Category" id="category">
									<option value=""></option>
									
								</select>
							</div>
						</div> --}}
						<div class="col-md-5 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ route('indexApprovalPresidentDirector') }}" class="btn btn-warning">Back</a>
									<button onclick="clearAll()" class="btn btn-danger">Clear</button>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br />			
	</div>	
	<div class="row box box-solid dropShadow" style="margin: 0;">		
		<div class="col-xs-12 pull-left" style="padding:10px 10px">
			<div id="tableReportContainer">
			
			</div>				
		</div>
	</div>

	<div class="modal modal-default fade" id="detail_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail Pengajuan Approval</h1>
					</div>
				</div>
				<div class="modal-body" style="padding-top: 0px">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body" style="padding-top: 0px;margin-top: 0px">                                    
								<center style="padding-top: 0px">
									<div style="width: 60%" style="padding-top: 0px">
										<table style="border:1px solid black; border-collapse: collapse;">
											<tbody align="center">
												<tr>
													<td colspan="2" style="border:1px solid black; font-size: 20px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Informasi Pengajuan</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Request ID
														
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_request_id" >
																													
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Penerima
														
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_recipient" >
																													
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Status
														
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; padding: 0 0 5px 0" id="detail_status" >

													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Department
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_department">

													</td>
												</tr>                                                    
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Keperluan
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 50;" id="detail_purpose">

													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Attachment
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 50px;" id="detail_attachment">

													</td>
												</tr>
											</tbody>
										</table>                                                                                         

									</div>                                        
								</center>                                    
							</div>
						</div>
					</div>
				</div>
				<div id="detail_modal_footer" class="modal-footer">                        
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
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
	var employees = [];
	var count = 0;
	var destinations = [];
	var countDestination = 0;

	var storedDataRequest = [];
    var storedDataComplete = [];
    var storedDataApproval = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		// fillList();

		clearAll();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function tableInit() {
            $('#tableReportContainer').html('');

            var tableReportInit = '';
            tableReportInit += '<table id="tableReport" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">';
            tableReportInit += '<thead style="background-color: rgb(126,86,134); color: #fff;">';
            tableReportInit += '<tr>';
            tableReportInit += '<th width="1%" style="color:#fff !important;">Status</th>';
            tableReportInit += '<th width="1%" style="color:#fff !important;">Req</th>';
            tableReportInit += '<th width="0.1%" style="color:#fff !important;">Dept</th>';
            tableReportInit += '<th width="1%" style="background-color: #3064db; color:#fff !important;">Aplikator</th>';
            tableReportInit += '<th width="0.1%" style="background-color: #3064db; color:#fff !important;">GM Dept Pengaju</th>';
            tableReportInit += '<th width="2%" style="color:#fff !important;">Nama Dokumen</th>';
            tableReportInit += '<th width="0.2%" style="font-size:14px; color:#fff !important;"">Jumlah Salinan</th>';
            tableReportInit += '<th width="1%" style="background-color: #3064db; color:#fff !important;">Tujuan Pengiriman</th>';
            tableReportInit += '<th width="2%" style="color:#fff !important;">Maksud dan Tujuan</th>';
            tableReportInit += '<th width="1%" style="color:#fff !important;">#</th>';
            tableReportInit += '</tr>';
            tableReportInit += '</thead>';
            tableReportInit += '<tbody id="bodyTableReport">';
            tableReportInit += '</tbody>';
			tableReportInit += '<tfoot>';
			tableReportInit += '<tr>';
			tableReportInit += '<th>Status</th>';
			tableReportInit += '<th>Req</th>';
			tableReportInit += '<th>Dept</th>';
			tableReportInit += '<th style="background-color: #3064db">Aplikator</th>';
			tableReportInit += '<th style="background-color: #3064db">GM Dept Pengaju</th>';
			tableReportInit += '<th>Nama Dokumen</th>';
			tableReportInit += '<th>Jumlah Salinan</th>';
			tableReportInit += '<th style="background-color: #3064db">Tujuan Pengiriman</th>';
			tableReportInit += '<th>Maksud dan Tujuan</th>';
			tableReportInit += '<th>#</th>';
			tableReportInit += '</tr>';
			tableReportInit += '</tfoot>';						
            tableReportInit += '</table>';

            $('#tableReportContainer').append(tableReportInit);
		}

	function clearAll() {
		$('#tanggal_from').val('');
		$('#tanggal_to').val('');
		// $('#tanggal_from').datepicker('setDate', new Date());
		// $('#tanggal_to').datepicker('setDate', new Date());
		$('#tableReportContainer').html('');
		$('#department').val('').trigger('change');
		$('#status').val('').trigger('change');
		
	}

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
			date_from:$('#tanggal_from').val(),
			date_to:$('#tanggal_to').val(),			
			department:$('#department').val(),
			status:$('#status').val(),
		}
		$.get('{{ url("fetch/ga_secretary/president_director/approval/report") }}',data, function(result, status, xhr){
			if(result.status){
				tableInit();

				var tableDataReport = '';
				storedDataRequest = result.presdir_request;
                storedDataApproval = result.presdir_approvals;
                storedDataCompleted = result.presdir_request_completed;

				$.each(result.presdir_request, function(key, value) {
					tableDataReport += '<tr>';
						
					let newValueUploadDate = new Date(value.created_at);
					let todayUploadDate = new Date();
					let differenceInDaysUploadDate = Math.round((todayUploadDate - newValueUploadDate) / (1000 * 60 * 60 * 24));

					if (differenceInDaysUploadDate == 0) {
						differenceInDaysUploadDate = 'Today';
					} else {
						differenceInDaysUploadDate = differenceInDaysUploadDate + ' days ago';
					}					

					tableDataReport += '<td>';
					
					if(value.status == 'Requested') {
						tableDataReport += '<span class="label label-primary" style="font-size: 12px; padding: 2px 5px;">Requested</span>';
					}
					else if (value.status == 'Completed') {
						tableDataReport += '<span class="label label-success" style="font-size: 12px; padding: 2px 5px;">Completed</span>';
					}
					else if (value.status == 'Cancelled') {
						tableDataReport += '<span class="label label-danger" style="font-size: 12px; padding: 2px 5px;">Cancelled</span>';
					}
					tableDataReport += '<br>';
					tableDataReport += '<span class="approver_column_label">' + value.date + '</span><br>';                            
					tableDataReport += '<span class="approver_column_label">(' + differenceInDaysUploadDate + ')</span>';
					tableDataReport += '</td>';

					tableDataReport += '<td><span style="font-weight:bold;color:red;">' + value.request_id + '</span><br>';
					
					tableDataReport += '<td>' + value.department_shortname + '</td>';                                                          

					$.each(result.presdir_approvals, function(key, val) {
						if (val.request_id == value.request_id) {
							tableDataReport += '<td class="approver_column"';												

							tableDataReport += '<span class="approver_column_label" style="text-align:left; padding:0px 5px;">' + val.person_name + '</span><br>';                                                                                                

							tableDataReport += '</td>';
						}
					});                        

					tableDataReport += '<td style="text-align:left; padding:0px 5px;">' + value.document_name + '</td>';             
					tableDataReport += '<td>' + value.hardcopy_total + '</td>';
					tableDataReport += '<td>' + value.recipient + '</td>';             
					tableDataReport += '<td style="text-align:left; padding:0px 5px;">' + value.purpose + '</td>';             

					tableDataReport += '<td class="btn-action">';
					tableDataReport += '<button class="btn btn-info btn-xs" data-toggle="modal" data-target="#detail_modal" onclick="showDetail(\'' + value.request_id + '\',\'' + value.department + '\',\'' + value.purpose + '\',\'' + value.document_name + '\',\'' + value.recipient + '\',\'' + value.status + '\')" style="margin-right:5px;"><i class="fa fa-eye"></i> Detail</button>';
					tableDataReport += '</td>';

					tableDataReport += '</tr>';
				});

				$('#bodyTableReport').append(tableDataReport);

				
				$('#tableReport tfoot th').each(function(index) {
					if (index == 2) { 
						var title = $(this).text();
						$(this).html('<select><option value="">All</option>@foreach ($departments as $dept)<option value="{{ $dept->department_shortname }}">{{ $dept->department_shortname }}</option>@endforeach</select>');
					}
					else if(index == 4) {
						var title = $(this).text();
						$(this).html('<select style="width:100%;"><option value="">All</option>@foreach ($divisions as $divs)<option value="{{ $divs->division_name }}">{{ $divs->division_name }}</option>@endforeach</select>');
					}
					else if(index == 0){
						$(this).html('<select style="width:100%"><option value="">All</option><option value="Requested">Requested</option><option value="Completed">Completed</option><option value="Cancelled">Cancelled</option></select>');
					}
					else if(index == 9) {						
						$(this).html(' ');
					}
					else {
						var title = $(this).text();
						$(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title + '" />');						
					}
				});

				var table = $('#tableReport').DataTable({
					'dom': 'Bfrtip',					
					'responsive': false,
					'lengthMenu': [
						[10, 25, 50, -1],
						['10 rows', '25 rows', '50 rows', 'Show all']
					],
					'buttons': {
						buttons: [{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
								exportOptions: {
									columns: ':not(.notexport)',									
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)',
									columns: [1,2,3,4,5,6,7,8],
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)',
									columns: [1,2,3,4,5,6,7,8],
								}
							}
						]
					},					
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
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
					$( 'input', this.footer() ).on( 'keyup change clear', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );

					$( 'select', this.footer() ).on( 'change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				});				
				
				// append tfoot to below thead
				$('#tableReport tfoot tr').appendTo('#tableReport thead');
				
				$('#loading').hide();

			} else {

				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}			

		});
	}
	
	function showDetail(request_id, department, purpose, document_name, recipient, status) {              
            $('#detail_request_id').html(request_id);
            $('#detail_department').html(department);      
            $('#detail_recipient').html(recipient);
            $('#detail_document_name').html(document_name);
            $('#detail_purpose').html(purpose);            
            $('#detail_status').css('font-size', '16px');
            if (status == 'Approved') {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-success">' + status + '</span>');
            } else if (status == 'Requesting') {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-warning">' + status + '</span>');
            } else if (status == 'Rejected') {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-danger">' + status + '</span>');
            } else {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-primary">' + status + '</span>');
            }            

            if(status == 'Requesting'){
                $('#detail_modal_footer').html('<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button><button class="btn btn-success" onclick="completeRequest()"><i class="fa fa-check"></i> Complete</button>');
            } else {
                $('#detail_modal_footer').html('<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>');
            }
            
            storedDataRequest.forEach(function(value, index) {
                if (value.request_id == request_id) {
                    if(value.docs_name){
                        var docs_name = value.docs_name.split("|");
                        var url = value.url.split("|");
                        var html = '';
                        for (var i = 0; i < docs_name.length; i++) {
                            html += '<a href="{{ url('') }}' + url[i] + '" target="_blank">' + docs_name[i] + '</a><br>';
                        }
                        $('#detail_attachment').html(html);
                    }else{
                        $('#detail_attachment').html('Tidak Ada Lampiran');
                    }
                }
            });                        
        }

	function getActualFullDate() {
		var today = new Date();

		var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
		return date;
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}


</script>
@endsection