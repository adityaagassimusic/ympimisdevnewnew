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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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

#tableMaster > tbody > tr > td > p > img {
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
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th style="text-align: center;width: 2%">Gender</th>
										<th style="text-align: center;width: 1%">Start Time</th>
										<th style="text-align: center;width: 1%">End Time</th>
										<th style="text-align: center;width: 1%">Capacity</th>
				                        <th style="text-align: center;width: 10%">Day</th>
				                        <th style="text-align: center;width: 2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableMaster">
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

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Schedule</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-3">Gender<span class="text-red">*</span></label>
									<div class="col-sm-3" align="left">
										<select class="form-control select2" data-placeholder="Select Gender" name="edit_gender" id="edit_gender" style="width: 100%">
											<option value=""></option>
											<option value="L">L</option>
											<option value="P">P</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Start Time<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" id="edit_start_time" name="edit_start_time" class="form-control timepicker" value="0:00">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">End Time<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" id="edit_end_time" name="edit_end_time" class="form-control timepicker" value="0:00">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Capacity</label>
									<div class="col-sm-5">
										<input type="text" class="form-control numpad" id="edit_capacity" readonly="" placeholder="Input Capacity" required readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Day</label>
									<div class="col-sm-9" align="left">
										<button class="btn btn-default" style="margin-left: 2px;border-color: grey" onclick="setDay('Monday')" id="Monday">Monday</button>
										<button class="btn btn-default" style="margin-left: 2px;border-color: grey" onclick="setDay('Tuesday')" id="Tuesday">Tuesday</button>
										<button class="btn btn-default" style="margin-left: 2px;border-color: grey" onclick="setDay('Wednesday')" id="Wednesday">Wednesday</button>
										<button class="btn btn-default" style="margin-left: 2px;border-color: grey" onclick="setDay('Thursday')" id="Thursday">Thursday</button>
										<button class="btn btn-default" style="margin-left: 2px;border-color: grey" onclick="setDay('Friday')" id="Friday">Friday</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
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
		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillList();

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.select2').select2({
			allowClear:true,
		});
	});

	function setDay(day) {
		if (document.getElementById(day).style.backgroundColor == 'rgb(163, 255, 175)') {
			$('#'+day).css('background-color', 'white');
		}else{
			$('#'+day).css('background-color', '#a3ffaf');
		}
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
		$.get('{{ url("fetch/ga_control/gym/schedule") }}', function(result, status, xhr){
			if(result.status){
				$('#tableMaster').DataTable().clear();
				$('#tableMaster').DataTable().destroy();
				$('#bodyTableMaster').html("");
				var tableData = "";
				var index = 1;
				$.each(result.schedule, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.gender +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.start_time +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.end_time +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.capacity +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.remark +'</td>';
					tableData += '<td style="text-align:center;">';
					tableData += '<button class="btn btn-xs btn-warning" onclick="editSchedule(\''+value.id+'\',\''+value.gender+'\',\''+value.start_time+'\',\''+value.end_time+'\',\''+value.capacity+'\',\''+value.remark+'\')"><i class="fa fa-edit"></i></button>';
					// tableData += '<button class="btn btn-xs btn-danger" style="margin-left:5px;" onclick="deleteSchedule(\''+value.id+'\')"><i class="fa fa-trash"></i></button>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				});

				safety = result.safety;
				$('#bodyTableMaster').append(tableData);

				var table = $('#tableMaster').DataTable({
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

	function editSchedule(id,gender,start_time,end_time,capacity,remark) {
		$('#id').val(id);
		$('#edit_gender').val(gender).trigger('change');
		var starts = new Date('{{date("Y-m-d")}} '+start_time);
		var hour = addZero(starts.getHours());
		var minute = addZero(starts.getMinutes());
		$('#edit_start_time').val(hour+':'+minute);

		var ends = new Date('{{date("Y-m-d")}} '+end_time);
		var hour = addZero(ends.getHours());
		var minute = addZero(ends.getMinutes());
		$('#edit_end_time').val(hour+':'+minute);

		$('#edit_capacity').val(capacity);
		var remarks = remark.split(',');
		$('#Monday').css('background-color', 'white');
		$('#Tuesday').css('background-color', 'white');
		$('#Wednesday').css('background-color', 'white');
		$('#Thursday').css('background-color', 'white');
		$('#Friday').css('background-color', 'white');
		for(var i = 0; i < remarks.length;i++){
			$('#'+remarks[i]).css('background-color', '#a3ffaf');
		}
		$('#edit-modal').modal('show');
	}

	function cancelAll() {
		$('#id').val('');
		$('#edit_material').val('').trigger('change');
		$('#edit_tag').val('');
		$('#edit_no_kanban').val('');
		$('#edit_barcode').val('');

		$('#id').val('');
		$('#add_material').val('').trigger('change');
		$('#add_tag').val('');
		$('#add_no_kanban').val('');
		$('#add_barcode').val('');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			if ($('#edit_gender').val() == '' || $('#edit_start_time').val() == '' || $('#edit_end_time').val() == '' || $('#edit_capacity').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!',"Isi Semua Data");
				return false;
			}

			var day = [];
			if (document.getElementById('Monday').style.backgroundColor == 'rgb(163, 255, 175)') {
				day.push('Monday');
			}
			if (document.getElementById('Tuesday').style.backgroundColor == 'rgb(163, 255, 175)') {
				day.push('Tuesday');
			}
			if (document.getElementById('Wednesday').style.backgroundColor == 'rgb(163, 255, 175)') {
				day.push('Wednesday');
			}
			if (document.getElementById('Thursday').style.backgroundColor == 'rgb(163, 255, 175)') {
				day.push('Thursday');
			}
			if (document.getElementById('Friday').style.backgroundColor == 'rgb(163, 255, 175)') {
				day.push('Friday');
			}
			var data = {
				id:$('#id').val(),
				gender:$('#edit_gender').val(),
				start_time:$('#edit_start_time').val(),
				end_time:$('#edit_end_time').val(),
				capacity:$('#edit_capacity').val(),
				remark:day.join(','),
			}

			$.post('{{ url("update/ga_control/gym/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Edit Data');
					$('#loading').hide();
					$('#edit-modal').modal('hide');
					fillList();
					cancelAll();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}



</script>
@endsection