@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

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
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 20px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}

#tableReport > tbody > tr > td > p > img {
      width: 100px !important;
    }
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
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div style="text-align: center;background-color: lightgreen;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: black;font-size: 20px">
							REPORT
						</span>
					</div>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
								<span style="font-weight: bold;">Date From</span>
								<div class="form-group">
					                <input type="text" class="form-control pull-right datepicker" id="date_from" name="date_from" placeholder="Date From">
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 5px;padding-right: 0px;">
								<span style="font-weight: bold;">Date To</span>
								<div class="form-group">
					                <input type="text" class="form-control pull-right datepicker" id="date_to" name="date_to" placeholder="Date To">
								</div>
							</div>
							<div class="col-xs-12" style="padding: 0px;margin-top: 10px;">
								<span style="font-weight: bold;">Dokumen</span>
								<div class="form-group">
									<select class="form-control select2" name="document_number" id="document_number" data-placeholder="Pilih Document" style="width: 100%;">
										<option></option>
										@foreach($process as $process)
										<option value="{{$process->document_number}}">{{$process->document_number}} - {{$process->document_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-3">
							<div class="form-group pull-right">
								<a href="{{ url('index/qa/special_process/') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/qa/special_process/report') }}" class="btn btn-danger">Clear</a>
								<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableReport" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th style="text-align: center;width: 2%">Document No.</th>
										<th style="text-align: center;width: 2%">Title</th>
										<th style="text-align: center;width: 2%">Date</th>
										<th style="text-align: center;width: 3%">Proses Pekerjaan</th>
				                        <th style="text-align: center;width: 3%">Point Pekerjaan</th>
				                        <th style="text-align: center;width: 3%">Point Safety</th>
				                        <th style="text-align: center;width: 1%">Kondisi</th>
				                        <th style="text-align: center;width: 3%">Note</th>
				                        <th style="text-align: center;width: 3%">Penanganan Dari Genba</th>
				                        <th style="text-align: center;width: 2%">Auditor</th>
				                        <th style="text-align: center;width: 2%">Auditee</th>
				                        <th style="text-align: center;width: 1%">Alat</th>
				                        <th style="text-align: center;width: 1%">Mesin</th>
									</tr>
								</thead>
								<tbody id="bodyTableReport">
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
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

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
			document_number:$('#document_number').val(),
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/qa/special_process/report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableReport').DataTable().clear();
				$('#tableReport').DataTable().destroy();
				$('#bodyTableReport').html("");
				var tableData = "";
				var index = 1;
				$.each(result.report, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.document_number +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.document_name +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.date_audit +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.work_process || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.work_point || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.work_safety || '') +'</td>';
					if (value.decision == 'NG') {
						tableData += '<td style="text-align:center;background-color: #ff8f8f">&#9747;</td>';
					}else{
						tableData += '<td style="text-align:center;background-color: #dfffd4">&#9711;</td>';
					}
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.note || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.handling || '') +'<br><span style="font-weight: bold;">'+ (value.handling_revision || '') +'</span>';
					if (value.handling_revision == 'Revisi IK') {
						var url = "{{url('data_file/qa/special_process/handling')}}/"+value.handling_revision_document;
						tableData += '<br><a class="btn btn-success" target="_blank" href="'+url+'"><i class="fa fa-file"></i></a>';
					}
					tableData += '</td>';
					if (value.auditor_id.match(/,/gi)) {
						tableData += '<td style="text-align:left;padding-left:7px;">';
						var auditor_id = value.auditor_id.split(',');
						var auditor_name = value.auditor_name.split(',');
						for(var i = 0; i < auditor_id.length;i++){
							tableData += auditor_id[i] +' - '+auditor_name[i]+'<br>';
						}
						tableData += '</td>';
					}else{
						tableData += '<td style="text-align:left;padding-left:7px;">'+ value.auditor_id +' - '+value.auditor_name+'</td>';
					}
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.auditee_id +' - '+value.auditee_name+'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.alat || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.mesin || '') +'</td>';
					tableData += '</tr>';
					index++;
				});

				safety = result.safety;
				$('#bodyTableReport').append(tableData);

				var table = $('#tableReport').DataTable({
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

				$('#tablePointApd').DataTable().clear();
				$('#tablePointApd').DataTable().destroy();

				$('#bodyTableApd').html("");
				var tableData = "";
				var index = 1;
				$.each(result.safety, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.document_number +'</td>';
					tableData += '<td>'+ value.document_name +'</td>';
					tableData += '<td>'+ value.category_safety +'</td>';
					if (value.category_safety == 'Alat Kelengkapan Diri') {
						tableData += '<td>'+ (value.point_safety || '') +'</td>';
						tableData += '<td></td>';
					}else{
						tableData += '<td></td>';
						tableData += '<td>'+ (value.point_safety || '') +'</td>';
					}
					tableData += '<td><button class="btn btn-sm btn-warning" onclick="editPointSafety(\''+value.id+'\',\''+value.document_number+'\',\''+value.document_name+'\',\''+value.category_safety+'\',\''+value.point_safety+'\')">Edit</button></td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableApd').append(tableData);

				var table = $('#tablePointApd').DataTable({
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
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}



</script>
@endsection