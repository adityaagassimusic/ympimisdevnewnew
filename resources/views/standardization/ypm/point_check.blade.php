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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="$('#modalAddPoint').modal('show')"><i class="fa fa-plus"></i> Add Point Check</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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
					<div class="col-xs-12">
						<div class="row">
							<table id="tableYPM" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="" id="headTableYPM">
									<tr>
										<th width="20%" style="background-color: rgb(126,86,134); color: #fff;">Criteria</th>
										<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Point 1</th>
										<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Point 2</th>
										<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Point 3</th>
										<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Point 4</th>
										<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableYPM">
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

	<div class="modal fade" id="modalEditPoint">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="margin-bottom: 20px">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Update YPM Point Check</h3>
					</center>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Criteria<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="hidden" class="form-control" id="id" name="id" placeholder="id">
								<input type="text" class="form-control" id="edit_criteria" name="edit_criteria" placeholder="Criteria">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 1<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="edit_result_1" name="edit_result_1" placeholder="Point 1">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 2<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="edit_result_2" name="edit_result_2" placeholder="Point 2">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 3<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="edit_result_3" name="edit_result_3" placeholder="Point 3">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 4<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="edit_result_4" name="edit_result_4" placeholder="Point 4">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<div class="col-xs-12">
						<div class="row">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button onclick="updatePoint()" class="btn btn-success pull-right"><i class="fa fa-edit"></i> Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAddPoint">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="margin-bottom: 20px">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Add YPM Point Check</h3>
					</center>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Criteria<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="add_criteria" name="add_criteria" placeholder="Criteria">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 1<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="add_result_1" name="add_result_1" placeholder="Point 1">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 2<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="add_result_2" name="add_result_2" placeholder="Point 2">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 3<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="add_result_3" name="add_result_3" placeholder="Point 3">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Point 4<span class="text-red"> *</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="add_result_4" name="add_result_4" placeholder="Point 4">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<div class="col-xs-12">
						<div class="row">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button onclick="addPoint()" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
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
	var teams_all = [];

	jQuery(document).ready(function() {
		all_point = null;
		cancelAll();
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		teams_all = [];
	});

	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function cancelAll() {
		$('#edit_criteria').val('');
		$('#edit_result_1').val('');
		$('#edit_result_2').val('');
		$('#edit_result_3').val('');
		$('#edit_result_4').val('');

		$('#add_criteria').val('');
		$('#add_result_1').val('');
		$('#add_result_2').val('');
		$('#add_result_3').val('');
		$('#add_result_4').val('');
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
	var all_point = null;
	function fillList(){
		$('#loading').show();
		$.get('{{ url("fetch/standardization/ypm/point_check") }}', function(result, status, xhr){
			if(result.status){
				$('#tableYPM').DataTable().clear();
				$('#tableYPM').DataTable().destroy();
				$('#bodyTableYPM').html("");

				var tableDataBody = "";
				var index = 1;

				$.each(result.point_check, function(key, value) {
					tableDataBody += '<tr>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.criteria +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:right">'+ value.result_1 +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:right">'+ value.result_2 +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:right">'+ value.result_3 +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:right">'+ value.result_4 +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:center"><button onclick="editPoint(\''+value.id+'\')" class="btn btn-warning btn-sm">Edit</button><button style="margin-left:10px" class="btn btn-danger btn-sm" onclick="deletePoint(\''+value.id+'\')">Delete</button></td>';
					tableDataBody += '</tr>';
				})
				$('#bodyTableYPM').append(tableDataBody);

				all_point = result.point_check;

				var table = $('#tableYPM').DataTable({
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
					"order": [],
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
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function editPoint(id) {
		$('#id').val(id);
		for(var i = 0; i < all_point.length;i++){
			if (all_point[i].id == id) {
				$('#edit_criteria').val(all_point[i].criteria);
				$('#edit_result_1').val(all_point[i].result_1);
				$('#edit_result_2').val(all_point[i].result_2);
				$('#edit_result_3').val(all_point[i].result_3);
				$('#edit_result_4').val(all_point[i].result_4);
			}
		}
		$('#modalEditPoint').modal('show');
	}

	function updatePoint() {
		$('#loading').show();
		var id = $('#id').val();
		var criteria = $('#edit_criteria').val();
		var result_1 = $('#edit_result_1').val();
		var result_2 = $('#edit_result_2').val();
		var result_3 = $('#edit_result_3').val();
		var result_4 = $('#edit_result_4').val();

		var data = {
			id:id,
			criteria:criteria,
			result_1:result_1,
			result_2:result_2,
			result_3:result_3,
			result_4:result_4,
		}

		$.post('{{ url("update/standardization/ypm/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				fillList();
				cancelAll();
				openSuccessGritter('Success!','Update Succeeded');
				$('#modalEditPoint').modal('hide');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function addPoint() {
		$('#loading').show();
		var criteria = $('#add_criteria').val();
		var result_1 = $('#add_result_1').val();
		var result_2 = $('#add_result_2').val();
		var result_3 = $('#add_result_3').val();
		var result_4 = $('#add_result_4').val();

		var data = {
			criteria:criteria,
			result_1:result_1,
			result_2:result_2,
			result_3:result_3,
			result_4:result_4,
		}

		$.post('{{ url("input/standardization/ypm/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				fillList();
				openSuccessGritter('Success!','Input Succeeded');
				cancelAll();
				$('#modalAddPoint').modal('hide');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function deletePoint(id) {
		if (confirm('Apakah Anda yakin akan menghapus data?')) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.post('{{ url("delete/standardization/ypm/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					cancelAll();
					openSuccessGritter('Success!','Delete Succeeded');
					$('#loading').hide();
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
    }



</script>
@endsection