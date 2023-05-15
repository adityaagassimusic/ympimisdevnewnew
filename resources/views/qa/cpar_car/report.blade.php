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
							<div class="col-xs-6" style="padding-left: 0px;margin-top: 10px;padding-right: 5px;">
								<span style="font-weight: bold;">Audit Title</span>
								<div class="form-group">
									<select class="form-control select2" name="audit_id" id="audit_id" data-placeholder="Pilih Audit Title" style="width: 100%;">
										<option></option>
										@foreach($point as $point)
										<option value="{{$point->audit_id}}">{{$point->audit_title}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0px;margin-top: 10px;padding-right: 5px;">
								<span style="font-weight: bold;">Result Check</span>
								<div class="form-group">
									<select class="form-control select2" name="result_check" id="result_check" data-placeholder="Pilih Result Check" style="width: 100%;">
										<option value=""></option>
										<option value="OK">OK</option>
										<option value="NG">NG</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-3">
							<div class="form-group pull-right">
								<a href="{{ url('index/qa/cpar_car/') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/qa/cpar_car/report') }}" class="btn btn-danger">Clear</a>
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
										<th style="width:1%;">#</th>
										<th style="width:1%">Audit ID</th>
										<th style="width:2%">Audit Title</th>
										<th style="width:1%">Periode</th>
										<th style="width:3%">Dept</th>
										<th style="width:3%">Area</th>
										<th style="width:3%">Product</th>
										<th style="width:1%">Index</th>
										<th style="width:3%">Point Check</th>
										<th style="width:3%">Standard</th>
										<th style="width:1%">Result</th>
										<th style="width:3%">Evidence</th>
										<th style="width:3%">Note</th>
										<th style="width:3%">Auditor</th>
										<th style="width:3%">Auditee</th>
										<th style="width:3%">Penanganan</th>
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
		fillList();
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
			audit_id:$('#audit_id').val(),
			result_check:$('#result_check').val(),
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/qa/cpar_car/report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableReport').DataTable().clear();
				$('#tableReport').DataTable().destroy();
				$('#bodyTableReport').html("");
				var tableData = "";
				var index = 1;
				$.each(result.report, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="background-color: #f0f0ff;text-align:right;padding-right:7px; !important">'+index+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+value.audit_id+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+value.audit_title+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(value.periode || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(value.department || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+value.area+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(value.product || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(value.audit_index || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(value.point_check || '')+'</td>';
					if (value.audit_images == 'null' || value.audit_images == null || value.audit_images == '') {
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff"></td>';
					}else{
						var url = '{{url("data_file/qa/ng_jelas_point")}}/'+value.audit_images;
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff"><img src="'+url+'" style="width:100px;"></td>';
					}
					if (value.result_check.match(/NG/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #ff8f8f;text-align:center;">&#9747;</td>';
					}else if (value.result_check.match(/OK/gi)) {
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">&#9711;</td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #dfffd4;text-align:center;">'+value.result_check+'</td>';
					}
					if (value.result_image != null) {
						var url2 = '{{url("data_file/qa/cpar_car")}}/'+value.result_image;
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff"><img src="'+url2+'" style="width:100px;"></td>';
					}else{
						tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff"></td>';
					}
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+(value.note || '')+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+value.auditor+' - '+value.name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff;text-align:center;">'+value.auditor+' - '+value.name+'</td>';
					tableData += '<td style="padding-left:10px !important;background-color: #f0f0ff">'+(value.handling || '')+'</td>';
					tableData += '</tr>';
					index++;
				});

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