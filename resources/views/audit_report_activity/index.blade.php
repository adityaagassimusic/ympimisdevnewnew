@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		text-align:center;
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
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $activity_name }} - {{ $leader }}
		<a class="btn btn-info pull-right" style="margin-left: 5px" href="{{url('index/audit_guidance/index/'.$id)}}">
			Manage Schedule
		</a>
		<a href="{{ url('index/audit_report_activity/create/'.$id) }}" class="btn btn-primary pull-right">Buat Audit IK</a>
	</h1>
	<ol class="breadcrumb">
	</ol>
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
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter {{ $activity_name }}</h3><br>
							<span style="font-weight: bold;color: red">Lakukan Filter untuk melihat data.</span>
						</div>
						<form role="form" method="post" action="{{url('index/audit_report_activity/filter_audit_report/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Group</label>
										<select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Pilih Group">
											<option value=""></option>
											@foreach($subsection as $subsection)
											<option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Bulan Schedule</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="tgl"name="month" placeholder="Pilih Bulan Schedule" autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/audit_report_activity/index/'.$id) }}" class="btn btn-danger">Clear</a>
										<button type="submit" class="btn btn-primary col-sm-14">Search</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak {{ $activity_name }}</h3>
						</div>
						<form target="_blank" role="form" method="post" action="{{url('index/audit_report_activity/print_audit_report/'.$id)}}">
							<!-- <span style="color: red">Mohon Maaf.<br>Fitur ini masih dalam perbaikan.</span> -->
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Bulan Schedule</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl_print" name="month" placeholder="Pilih Bulan Schedule" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<a href="javascript:void(0)" onclick="printPdf('{{$id}}',$('#tgl_print').val())" class="btn btn-primary col-sm-14">Cetak</a>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Kirim Email ke Foreman</h3>
						</div>
						<form role="form" method="post" action="{{url('index/audit_report_activity/send_email/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Group</label>
										<select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Pilih Group" required>
											<option value=""></option>
											@foreach($subsection3 as $subsection3)
											<option value="{{ $subsection3->sub_section_name }}">{{ $subsection3->sub_section_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Bulan Schedule</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl" name="month" placeholder="Pilih Bulan Schedule" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<button type="submit" class="btn btn-primary col-sm-14">Send Email</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="col-md-12">
							<div class="col-md-12">
								<div class="form-group pull-right">
									
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="overflow-x: scroll;">
							<table id="example1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Group</th>
										<th>Date Audited</th>
										<th>Schedule Date</th>
										<th>Nama Dokumen</th>
										<th>No. Dokumen</th>
										<th>Kesesuaian Aktual Proses</th>
										<th>Kelengkapan Point Safety</th>
										<th>Kesesuaian QC Kouteihyo</th>
										<th>Hasil Keseluruhan</th>
										<th>Send Status</th>
										<th>Approval Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (ISSET($audit_report_activity)): ?>
										@foreach($audit_report_activity as $audit_report_activity)
										<tr>
											<td>{{$audit_report_activity->subsection}}</td>
											<td>{{$audit_report_activity->date_audited}}</td>
											<td>{{$audit_report_activity->month}}</td>
											<td>{{$audit_report_activity->nama_dokumen}}</td>
											<td>{{$audit_report_activity->no_dokumen}}</td>
											<td><?php echo $audit_report_activity->kesesuaian_aktual_proses ?></td>
											<td><?php echo $audit_report_activity->kelengkapan_point_safety ?></td>
											<td><?php echo $audit_report_activity->kesesuaian_qc_kouteihyo ?></td>
											<td style="text-align: center;">
												{{$audit_report_activity->handling}}
											</td>
											<td style="text-align: center;">
												@if($audit_report_activity->send_status_audit == "")
							                		<label class="label label-danger">Not Yet Sent</label>
							                	@else
							                		<label class="label label-success">Sent</label>
							                	@endif
											</td>
											<td style="text-align: center;">@if($audit_report_activity->approval == "")
							                		<label class="label label-danger">Not Approved</label>
							                	@else
							                		<label class="label label-success">Approved</label>
							                	@endif</td>
											<td>
												<center>
													@if($audit_report_activity->reminder_status == null)
													<a href="{{url('index/audit_report_activity/edit/'.$id.'/'.$audit_report_activity->audit_id)}}" class="btn btn-warning btn-sm">Edit</a>
													<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/audit_report_activity/destroy") }}', '{{ $audit_report_activity->no_dokumen }} - {{ $audit_report_activity->nama_dokumen }} - {{ $audit_report_activity->date }}','{{ $id }}', '{{ $audit_report_activity->audit_id }}');">
														Delete
													</a>
													@endif
												</center>
											</td>
										</tr>
										@endforeach
									<?php endif ?>
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
		</div>
	</div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
			</div>
			<div class="modal-body">
				Are you sure delete?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		

	});

	$('.datepicker2').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		

	});

	
</script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {
		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );
		var table = $('#example1').DataTable({
			"order": [],
			'dom': 'Bfrtip',
			'responsive': true,
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
			}
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

		$('#example1 tfoot tr').appendTo('#example1 thead');

	});
	$(function () {

		$('#example2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : true,
			'info'        : true,
			'autoWidth'   : false
		})
	})
	function deleteConfirmation(url, name, sampling_check_id,id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+sampling_check_id+'/'+id);
	}

	function printPdf(id,month) {
		if (month == "") {
			alert('Pilih Bulan');
		}else{
			var url = "{{url('index/audit_report_activity/print_audit_report/')}}";
			// console.log(url + '/' + id+ '/' + month);
			window.open(url + '/' + id+ '/' + month,"_blank");
		}
	}
</script>
@endsection