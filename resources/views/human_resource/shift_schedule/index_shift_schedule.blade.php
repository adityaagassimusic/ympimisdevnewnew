@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
		height: 45px;
		text-align: center;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(100, 100, 100);
		vertical-align: middle;
	}
	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}
	#loading, #error { display: none; }
</style>

@section('header')
<section class="content-header" style="padding-bottom: 40px">
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
	<h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h1>
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<center style="padding-top: 350px;">
			<span style="font-size: 50px; color: white">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</center>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="border: 1px solid grey;">
				@if($user->employee_id == 'PI2101044')
				<div class="box-body">
					<div class="col-md-4">
						<div class="form-group">
							<label>Attend. Date From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="datefrom">
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Attend. Date To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="dateto">
							</div>
						</div>
					</div>
					<div class="col-md-4" style="padding-top: 27px">
						<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
						<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
						<button onClick="SelectKaryawan()" class="btn btn-info pull-right">Select Karyawan</button>
					</div>
				</div>
				@endif
				<div class="box-body">
					<table id="TableResume" class="table table-bordered table-hover" style="margin-bottom: 0;">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="text-align: center;">No</th>
								<th style="text-align: center;">NIK</th>
								<th style="text-align: center;">Nama</th>
								<th style="text-align: center;">Dept</th>
								<th style="text-align: center;">Sect.</th>
								<th style="text-align: center;">Shift Schedule</th>
								<th style="text-align: center;">Keterangan</th>
								<th style="text-align: center;">#</th>
								<th style="text-align: center;">#</th>
							</tr>
						</thead>
						<tbody id="bodyTableResume" style="background-color: #fcf8e3;">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
<script src="{{ url('js/highcharts.js')}}"></script>
<script src="{{ url('js/exporting.js')}}"></script>
<script src="{{ url('js/export-data.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		DataResume();
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function DataResume(){
		$('#loading').show();
		$('#TableResume').DataTable().clear();
		$('#TableResume').DataTable().destroy();
		var tableData = "";

		$.get('{{ url("fetch/data/shift_schedule") }}', function(result, status, xhr){
			if (result.status) {
				$.each(result.data_resumes, function(key, value) {
					var karyawan = value.karyawan.split('/');
					var bagian = value.bagian.split('/');
					tableData += '<tr>';
					tableData += '<td>'+ key +'</td>';
					tableData += '<td>'+ karyawan[0] +'</td>';
					tableData += '<td>'+ karyawan[1] +'</td>';
					tableData += '<td>'+ (bagian[0] || "") +'</td>';
					tableData += '<td>'+ (bagian[1] || "") +'</td>';
					tableData += '<td>'+ (value.shift_sf || "") +'</td>';
					tableData += '<td>Tidak Sesuai</td>';
					tableData += '<td><span class="label label-primary">Check In '+value.jam_in+'</span></td>';
					tableData += '<td><span class="label label-primary">Check Out '+value.jam_out+'</span></td>';
					tableData += '</tr>';
				});
				$('#bodyTableResume').append(tableData);
				
				var table = $('#TableResume').DataTable({
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
			}else{
				$('#loading').hide();
				alert('Get Data Failed');
			}
		});
	}

	function SelectKaryawan(){
		$('#loading').show();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto
		}

		$.post('<?php echo e(url("select/match/data")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!', 'OK');
				$('#datefrom').val();
				$('#dateto').val();
				DataResume()
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Pokok Salah');
				$('#datefrom').val();
				$('#dateto').val();
			}
		});
	}
</script>
@endsection