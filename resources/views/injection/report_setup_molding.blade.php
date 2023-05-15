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

	.btn_less_more {
	  background: none!important;
	  border: none;
	  padding: 0!important;
	  font-size: 12px;
	  /*optional*/
	  font-family: arial, sans-serif;
	  /*input has OS specific font-family*/
	  color: #069;
	  cursor: pointer;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
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
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
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
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/injection/report_setup_molding') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="row" style="overflow-x: scroll;">
							<table id="tableSetupMolding" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">Type</th>
										<th width="3%">PIC</th>
										<th width="1%">Mesin</th>
										<th width="2%">Part</th>
										<th width="2%">Last Shot</th>
										<th width="2%">Start</th>
										<th width="2%">End</th>
										<th width="2%">Dandori Duration</th>
										<th width="2%">Purging</th>
										<th width="2%">Setting Robot & Camera</th>
										<th width="2%">First Inject & Approval QA Duration</th>
										<th width="2%">Dimension Check Duration</th>
										<th width="2%">Pause</th>
										<th width="2%">Total Duration</th>
										<th width="2%">Reason Pause</th>
										<th width="2%">Note</th>
										<th width="2%">Decision</th>
										<th width="2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableSetupMolding">
								</tbody>
								<!-- <tfoot> -->
									<!-- <tr style="color: black">
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
										<th></th>
										<th></th>
										<th></th>
									</tr> -->
								<!-- </tfoot> -->
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalTimline" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="text-align: center;background-color: orange;color: white;margin-bottom: 20px;">
					<h4 class="modal-title" style="font-size: 20px;font-weight: bold;">Detail Timeline</h4>
				</div>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12">
						<table id="tableSetupMoldingTimeline" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
							<thead style="background-color: rgb(126,86,134); color: #FFD700;">
								<tr>
									<th width="1%">#</th>
									<th width="1%">Mesin</th>
									<th width="1%">Process</th>
									<th width="1%">PIC</th>
									<th width="2%">Part</th>
									<th width="2%">Start</th>
									<th width="2%">End</th>
									<th width="10%">Reason</th>
								</tr>
							</thead>
							<tbody id="bodyTableSetupMoldingTimeline">
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
                    <button class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 0.95vw; width: 20%;">Close</button>
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
			tanggal_from:$('#tanggal_from').val(),
			tanggal_to:$('#tanggal_to').val(),
		}
		$.get('{{ url("fetch/injection/report_setup_molding") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableSetupMolding').DataTable().clear();
				$('#tableSetupMolding').DataTable().destroy();
				$('#bodyTableSetupMolding').html("");
				var tableData = "";
				$.each(result.datas, function(key, value) {
					var tablePause = "";
					var duration_dandori = parseFloat(value.duration);
					var duration_approval = 0;
					var duration_pause = 0;
					var duration_check = 0;
					var duration_purging = 0;
					var duration_setting = 0;
					var reason_pause = [];
					var duration_total = parseFloat(value.duration);
					for(var i = 0; i < result.dataworkall.length;i++){
						var dataone = result.dataworkall[i].split("+");
						if (dataone[0] == value.molding_code) {
							if (dataone[1] == 'PAUSE') {
								duration_total = duration_total - parseFloat(dataone[4]);
								duration_pause = duration_pause + parseFloat(dataone[4]);
								reason_pause.push(dataone[5]+" "+dataone[4]+" Menit");
							}else if(dataone[1] == "APPROVAL QA"){
								if (dataone[4].length > 0) {
									duration_total = duration_total - parseFloat(dataone[4]);
									duration_approval = duration_approval + parseFloat(dataone[4]);
								}else{
									duration_approval = 0;
								}
							}else if(dataone[1] == "CEK VISUAL & DIMENSI"){
								if (dataone[4].length > 0) {
									duration_total = duration_total - parseFloat(dataone[4]);
									duration_check = duration_check + parseFloat(dataone[4]);
								}else{
									duration_check = 0;
								}
							}else if(dataone[1] == "PURGING"){
								if (dataone[4].length > 0) {
									duration_total = duration_total - parseFloat(dataone[4]);
									duration_purging = duration_purging + parseFloat(dataone[4]);
								}else{
									duration_purging = 0;
								}
							}else if(dataone[1] == "SETTING ROBOT & CAMERA"){
								if (dataone[4].length > 0) {
									duration_total = duration_total - parseFloat(dataone[4]);
									duration_setting = duration_setting + parseFloat(dataone[4]);
								}else{
									duration_setting = 0;
								}
							}
						}
					}
					tableData += '<tr>';
					tableData += '<td>'+ value.type +'</td>';
					tableData += '<td>'+ value.pic +'</td>';
					tableData += '<td>'+ value.mesin +'</td>';
					tableData += '<td>'+ value.color +' - '+ value.part +'</td>';
					tableData += '<td>'+ value.last_shot +'</td>';
					tableData += '<td>'+ value.start_time +'</td>';
					tableData += '<td>'+ value.end_time +'</td>';
					tableData += '<td>'+ duration_total.toFixed(2) +'</td>';
					tableData += '<td>'+ duration_purging.toFixed(2) +'</td>';
					tableData += '<td>'+ duration_setting.toFixed(2) +'</td>';
					tableData += '<td>'+ duration_approval.toFixed(2) +'</td>';
					tableData += '<td>'+ duration_check.toFixed(2) +'</td>';
					tableData += '<td>'+ duration_pause.toFixed(2) +'</td>';
					tableData += '<td>'+ parseFloat(value.duration).toFixed(2) +'</td>';
					tableData += '<td>'+ reason_pause.join(', ') +'</td>';
					if (value.note != null) {
						if (value.note.length > 15) {
							tableData += '<td>';
							tableData += '<span id="less_'+key+'">'+ value.note.split(' ')[0]+' '+value.note.split(' ')[1]+'... <button class="btn_less_more" onclick="readMore(\''+key+'\')">Read More</button></span>';
							tableData += '<span style="display:none" id="more_'+key+'">'+ value.note+' <button class="btn_less_more" onclick="readLess(\''+key+'\')">Read Less</button></span>';
							tableData += '</td>';
						}else{
							tableData += '<td>'+ value.note +'</td>';
						}
					}else{
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.decision || "") +'</td>';
					tableData += '<td><button class="btn btn-primary btn-sm" onclick="timelineDetail(\''+value.molding_code+'\')">Timeline</button></td>';
					tableData += '</tr>';
				});
				$('#bodyTableSetupMolding').append(tableData);

				// $('#tableSetupMolding tfoot th').each(function(){
				// 	var title = $(this).text();
				// 	$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				// });
				
				var table = $('#tableSetupMolding').DataTable({
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

				// table.columns().every( function () {
				// 	var that = this;

				// 	$( 'input', this.footer() ).on( 'keyup change', function () {
				// 		if ( that.search() !== this.value ) {
				// 			that
				// 			.search( this.value )
				// 			.draw();
				// 		}
				// 	} );
				// } );

				// $('#tableSetupMolding tfoot tr').appendTo('#tableSetupMolding thead');
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function timelineDetail(molding_code) {
		$('#loading').show();
		var data = {
			molding_code:molding_code
		}

		$.get('{{ url("fetch/injection/report_setup_molding/timeline") }}',data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableSetupMoldingTimeline').html('');
				var timeline = '';

				for(var i = 0; i < result.timeline.length;i++){
					timeline += '<tr>';
					timeline += '<td style="text-align:right;padding-right:7px;">'+(i+1)+'</td>';
					timeline += '<td style="text-align:left;padding-left:7px;">'+result.timeline[i].mesin+'</td>';
					timeline += '<td style="text-align:left;padding-left:7px;">'+result.timeline[i].status+'</td>';
					timeline += '<td style="text-align:left;padding-left:7px;">'+result.timeline[i].pic+'</td>';
					timeline += '<td style="text-align:left;padding-left:7px;">'+result.timeline[i].part+'</td>';
					timeline += '<td style="text-align:right;padding-right:7px;">'+result.timeline[i].start_time+'</td>';
					timeline += '<td style="text-align:right;padding-right:7px;">'+result.timeline[i].end_time+'</td>';
					timeline += '<td style="text-align:left;padding-left:7px;">'+result.timeline[i].reason+'</td>';
					timeline += '</tr>';
				}

				$('#bodyTableSetupMoldingTimeline').append(timeline);

				$('#modalTimline').modal('show');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})

	}

	function readMore(index) {
		$('#less_'+index).hide();
		$('#more_'+index).show();
	}

	function readLess(index) {
		$('#less_'+index).show();
		$('#more_'+index).hide();
	}


</script>
@endsection