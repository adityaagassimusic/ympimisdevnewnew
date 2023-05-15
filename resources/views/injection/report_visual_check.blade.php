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
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
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
									<input type="text" class="form-control datepicker" id="tanggal_to"name="tanggal_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Mesin</span>
							<div class="form-group">
								<select class="form-control select2" name="machine" id="machine" data-placeholder="Pilih Mesin" style="width: 100%;">
									<option></option>
									@foreach($mesin as $mesin)
										<option value="{{$mesin}}">{{$mesin}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Jam Pengecekan</span>
							<div class="form-group">
								<select class="form-control select2" name="hour_check" id="hour_check" data-placeholder="Pilih Jam Pengecekan" style="width: 100%;">
									<option></option>
									@foreach($hour as $hour)
										<option value="{{$hour}}">{{$hour}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/injection/report_visual_check') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- <div class="col-xs-6" style="padding-left: 5px">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Print PDF</h4>
					<div class="row">
						<div class="col-md-6 col-md-offset-2">
							<span style="font-weight: bold;">Date</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_pdf" name="tanggal_pdf" placeholder="Select Date" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<span style="font-weight: bold;">Part</span>
							<div class="form-group">
								<select class="form-control select2" name="part_type" id="part_type" data-placeholder="Pilih Part" style="width: 100%;">
									<option></option>
									@foreach($part as $part)
										<option value="{{$part}}">{{$part}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12" style="padding-left: 0px">
								<div class="form-group">
									<button class="btn btn-info col-sm-14" onclick="printPdf()">Print PDF</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->

		
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableVisual" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">Machine</th>
										<th width="1%">Hour</th>
										<th width="2%">Material</th>
										<th width="1%">Part</th>
										<th width="1%">Color</th>
										<th width="1%">Cavity</th>
										<th width="1%">Molding</th>
										<th width="1%">Dryer</th>
										<th width="1%">Cav Detail</th>
										<th width="3%">Point Check</th>
										<th width="1%">Result</th>
										<th width="2%">Note</th>
										<th width="2%">PIC</th>
										<th width="2%">At</th>
										<th width="2%">CAR Desc</th>
										<th width="2%">CAR Action Immediately</th>
										<th width="2%">CAR Possible Cause</th>
										<th width="2%">CAR Action</th>
										<th width="2%">CAR Approver</th>
										<th width="2%">CAR Approved At</th>
									</tr>
								</thead>
								<tbody id="bodyTableVisual">
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
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

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

		var data = {
			tanggal_from:$('#tanggal_from').val(),
			tanggal_to:$('#tanggal_to').val(),
			machine:$('#machine').val(),
			hour_check:$('#hour_check').val(),
		}
		$.get('{{ url("fetch/injection/report_visual") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableVisual').DataTable().clear();
				$('#tableVisual').DataTable().destroy();
				$('#bodyTableVisual').html("");
				var tableData = "";
				$.each(result.visual_check, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.machine +'</td>';
					tableData += '<td>'+ value.hour_check +'</td>';
					tableData += '<td>'+ value.material_number +'<br>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.part_type +'</td>';
					tableData += '<td>'+ value.color +'</td>';
					tableData += '<td>'+ value.cavity +'</td>';
					tableData += '<td>'+ value.molding +'</td>';
					tableData += '<td>'+ value.dryer +'<br>'+ value.lot_number +'</td>';
					tableData += '<td>'+ value.cav_detail +'</td>';
					tableData += '<td>'+ value.point_check +'</td>';
					if (value.result_check == 'OK') {
						tableData += '<td style="background-color:#a2ff8f">&#9711;</td>';
					}else if (value.result_check == 'NG') {
						tableData += '<td style="background-color:#ff8f8f">&#9747;</td>';
					}else{
						tableData += '<td style="background-color:#fff68f">&#8420;</td>';
					}
					tableData += '<td>'+ (value.note || "") +'</td>';
					tableData += '<td>'+ value.pic_check +'<br>'+ value.name.replace(/(.{14})..+/, "$1&hellip;") +'</td>';
					tableData += '<td>'+ value.created_at +'</td>';
					if (value.result_check != 'OK') {
						tableData += '<td>'+ (value.car_description || "") +'</td>';
						tableData += '<td>'+ (value.car_action_now || "") +'</td>';
						tableData += '<td>'+ (value.car_cause || "") +'</td>';
						tableData += '<td>'+ (value.car_action || "") +'</td>';
						tableData += '<td>'+ (value.car_approver_id || "")+' '+(value.car_approver_name || "") +'</td>';
						tableData += '<td>'+ (value.car_approved_at || "") +'</td>';
					}else{
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
					}
					tableData += '</tr>';
				});
				$('#bodyTableVisual').append(tableData);

				var table = $('#tableVisual').DataTable({
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
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function printPdf() {
		var date = $('#tanggal_pdf').val();
		var part_type = $('#part_type').val();

		if (date == "" || part_type == "") {
			openErrorGritter('Error!','Pilih tanggal dan part');
		}else{
			window.open('{{url("pdf/injection/report_visual")}}'+'/'+date+'/'+part_type, '_blank');
		}
	}


</script>
@endsection