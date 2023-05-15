@extends('layouts.display')
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
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
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

	#resultScan_info, #resultScan_filter{
		color: white !important;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
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
<section class="content" style="padding-top: 0px">
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
		<div class="col-xs-12" style="margin-bottom: 10px;">
			<div class="col-xs-3 col-xs-offset-2" style="padding-right: 0px;">
				<input type="text" id="employee_id" placeholder="NIK" style="width: 100%;font-size: 20px;text-align:center;" readonly="">
			</div>
			<div class="col-xs-5" style="padding-right: 5px;padding-left: 0px;">
				<input type="text" id="name" placeholder="Nama" style="width: 100%;font-size: 20px;text-align:center;" readonly="">
			</div>
			<div class="col-xs-2 pull-right" style="padding-left: 5px;padding-right: 5px">
				<a class="btn btn-primary pull-right" href="{{url('index/lifetime/'.$category.'/'.$location)}}">
					<i class="fa fa-bar-chart"></i> Monitoring
				</a>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 10px">
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
				<input type="text" id="tag_kanban" placeholder="Scan Kanban / Jig di Sini" style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
			</div>
			<!-- <div class="col-xs-6" style="padding-left: 0px;">
				<input type="text" id="tag" placeholder="Scan Jig di Sini" style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
			</div> -->
		</div>
		<div class="col-xs-6" style="padding-bottom: 10px">
			<table id="resultKanban" class="table table-bordered table-striped table-hover" style="width: 100%;">
	            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
	            	<tr>
	            		<th colspan="4" style="font-size: 25px;text-align: center;">KANBAN</th>
	            		<th colspan="1" style="font-size: 25px;text-align: center;" id="count">0</th>
	            	</tr>
	                <tr>
	                  <th style="width: 1%;">Barcode</th>
	                  <th style="width: 4%;">Material</th>
	                  <th style="width: 1%;">No. Kanban</th>
	                  <th style="width: 1%;">Cat</th>
	                  <th style="width: 1%;">Action</th>
	                </tr>
	            </thead >
	            <tbody id="resultScanKanban">
				</tbody>
            </table>
		</div>
		<div class="col-xs-6" style="padding-bottom: 10px;padding-left: 0px;">
			<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
	            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
	            	<tr>
	            		<th colspan="9" style="font-size: 25px;text-align: center;">HISTORY</th>
	            	</tr>
	                <tr>
	                  <th style="width: 1%;">Product</th>
	                  <th style="width: 4%;">Item</th>
	                  <th style="width: 1%;">Lifetime</th>
	                  <th style="width: 4%;">By</th>
	                  <th style="width: 2%;">Start</th>
	                  <th style="width: 2%;">End</th>
	                </tr>
	            </thead >
	            <tbody id="resultScanBody">
				</tbody>
            </table>
		</div>
	</div>

		<div class="modal fade" id="modalOperator">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<div class="modal-body table-responsive no-padding">
							<div class="form-group">
								<label for="exampleInputEmail1">Employee ID</label>
								<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
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

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').removeAttr('disabled');
		$('#employee_id').val('');
		$('#name').val('');
		$('#operator').val('');
		// $('#tag').val('');
		$('#tag_kanban').val('');
		kanban = [];
		count = 0;
	});

	var kanban = [];
	var count = 0;

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
				$('#loading').show();
				var data = {
					employee_id : $("#operator").val(),
					location:'{{$location}}'
				}

				$.get('{{ url("scan/operator/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#employee_id').val(result.employee.employee_id);
						$('#name').val(result.employee.name);
						$('#operator').prop('disabled',true);
						$('#tag_kanban').val('');
						$('#tag_kanban').focus();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').removeAttr('disabled');
						$('#operator').val('');
					}
				});
		}
	});

	// $('#tag').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		// if (count > 0) {
	// 			$('#loading').show();
	// 			var data = {
	// 				tag : $("#tag").val(),
	// 				kanban:kanban
	// 			}

	// 			$.get('{{ url("scan/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', result.message);
	// 					fillList();
	// 					$('#tag').val('');
	// 					$('#tag_kanban').val('');
	// 					$('#tag_kanban').focus();
	// 					count = 0;
	// 					$('#resultScanKanban').html('');
	// 					$('#count').html('0');
	// 					kanban = [];
	// 					$('#loading').hide();
	// 				}
	// 				else{
	// 					$('#loading').hide();
	// 					audio_error.play();
	// 					openErrorGritter('Error', result.message);
	// 					$('#tag').removeAttr('disabled');
	// 					$('#tag').val('');
	// 				}
	// 			});
	// 		// }else{
	// 		// 	$('#loading').hide();
	// 		// 	audio_error.play();
	// 		// 	openErrorGritter('Error', 'Scan Kanban Dulu.');
	// 		// 	$('#tag').val('');
	// 		// 	$('#tag_kanban').val('');
	// 		// 	$('#tag_kanban').focus();
	// 		// }
	// 	}
	// });

	$('#tag_kanban').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if (!isNaN($("#tag_kanban").val())) {
				$('#loading').show();
				var data = {
					tag : $("#tag_kanban").val(),
					kanban:kanban
				}

				$.get('{{ url("scan/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						fillList();
						// $('#tag').val('');
						$('#tag_kanban').val('');
						$('#tag_kanban').focus();
						count = 0;
						$('#resultScanKanban').html('');
						$('#count').html('0');
						kanban = [];
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_kanban').val('');
						$('#tag_kanban').focus();
						$('#tag_kanban').removeAttr('disabled');
						// $('#tag').val('');
					}
				});
			}else{
				$('#loading').show();
				var data = {
					tag : $("#tag_kanban").val(),
				}

				$.get('{{ url("scan/kanban/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
					if(result.status){
						var tags = '';
						var surface = '';
						if (result.materials != null) {
							if (result.materials.surface.match(/N./gi)) {
								surface = 'Nikel';
							}else{
								surface = 'Silver';
							}
						}

						if($.inArray($("#tag_kanban").val(), kanban) != -1){
							$('#loading').hide();
							audio_error.play();
							openErrorGritter('Error!','Kanban sudah ada di list.');
							$('#tag_kanban').val('');
							$('#tag_kanban').focus();
							return false;
						}

						tags += '<tr id="'+$("#tag_kanban").val()+'">';
						tags += '<td style="background-color:white;">'+$("#tag_kanban").val()+'</td>';
						tags += '<td style="background-color:white;">'+result.tags.material_number+' - '+result.tags.material_description+'</td>';					
						tags += '<td style="background-color:white;text-align:center;">'+$("#tag_kanban").val().substr(11)+'</td>';
						tags += '<td style="background-color:white;">'+surface+'</td>';
						tags += '<td style="text-align:center;background-color:white;">';
						tags += '<button onclick="deleteKanban(\''+$("#tag_kanban").val()+'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>'
						tags += '</td>';
						tags += '</tr>';

						$('#resultScanKanban').append(tags);
						kanban.push($("#tag_kanban").val());

						count++;
						$('#count').html(count);

						$('#tag_kanban').val('');
						$('#tag_kanban').focus();
						$('#loading').hide();
						openSuccessGritter('Success!', 'Scan Success');
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_kanban').removeAttr('disabled');
						$('#tag_kanban').val('');
					}
				});
			}
		}
	});

	function deleteKanban(id){
		kanban.splice( $.inArray(id), 1 );
		$('#'+id).remove();
		count--;
		$('#count').html(count);
	}

	function fillList(){
		$('#loading').show();
		
		$.get('{{ url("fetch/record/lifetime/".$category."/".$location) }}', function(result, status, xhr){
			if(result.status){
				if (result.lifetime != null) {
					$('#resultScan').DataTable().clear();
					$('#resultScan').DataTable().destroy();
					$('#resultScanBody').html('');
					var scanBody = '';

					for(var i = 0; i < result.lifetime.length;i++){
						scanBody += '<tr>';
						scanBody += '<td style="background-color:white;">'+result.lifetime[i].product+'</td>';
						scanBody += '<td style="background-color:white;">'+result.lifetime[i].item_name+' - '+result.lifetime[i].item_type+' - '+result.lifetime[i].item_index+'</td>';
						scanBody += '<td style="background-color:white;text-align:right;padding-right:10px !important;">'+result.lifetime[i].lifetime+'</td>';
						var employee_id = '';
						var name = '';
						for(var j = 0; j < result.employee.length;j++){
							if (result.employee[j].employee_id == result.lifetime[i].created_by) {
								employee_id = result.employee[j].employee_id;
								name = result.employee[j].name;
							}
						}
						scanBody += '<td style="background-color:white;">'+employee_id+' - '+name+'</td>';
						if (result.lifetime[i].created_at == result.lifetime[i].updated_at) {
							scanBody += '<td style="background-color:white;text-align:right;padding-right:7px;">'+result.lifetime[i].updated_at+'</td>';
							scanBody += '<td style="background-color:white;text-align:right;padding-right:7px;"></td>';
						}else{
							scanBody += '<td style="background-color:white;text-align:right;padding-right:7px;">'+result.lifetime[i].created_at+'</td>';
							scanBody += '<td style="background-color:white;text-align:right;padding-right:7px;">'+result.lifetime[i].updated_at+'</td>';
						}
						scanBody += '</tr>';
					}

					$('#resultScanBody').append(scanBody);

					var table = $('#resultScan').DataTable({
						'dom': 'Bfrtip',
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
						'searching'   	: true,
						'ordering'		: true,
						'order': [],
						'info'       	: true,
						'autoWidth'		: false,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						// "infoCallback": function( settings, start, end, max, total, pre ) {
						// 	return "<b>Total "+ total +" pc(s)</b>";
						// }
					});
				}

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}


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

</script>
@endsection