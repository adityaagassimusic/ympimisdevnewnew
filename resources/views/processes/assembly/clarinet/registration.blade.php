@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:  2px 5px 2px 5px;
	}

	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
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

	.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

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
  color: white;
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
  left: -10px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
  color: white;
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

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-4" style="padding: 0 0 0 10px;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #3d9970;background-color: white">
					<i class="fa fa-arrow-down"></i> ➀ PILIH MODEL <i class="fa fa-arrow-down"></i>
				</div>
				<div>
					<div class="row" style="padding-right: 10px">
						<button id="YCL255J" onclick="selectModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 45%; font-size: 2vw; font-weight: bold;">YCL255J</button>
						@foreach($models as $model)
						@if($model->model != 'YCL255E')
						<button id="{{ $model->model }}" onclick="selectModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 45%; font-size: 2vw; font-weight: bold;">{{ $model->model }}</button>
						@endif
						@endforeach
						<div class="col-xs-12" style="margin-top: 10px;">
							<div class="col-xs-12 text-center" style="background-color: lightgreen;padding: 10px;font-weight: bold;font-size: 20px;margin-bottom: 7px;">
								<span>PILIH MODE REGISTRASI</span>
							</div>
							<div class="col-xs-4">
								<label class="containers">ALL
								  <input type="radio" name="condition" checked="true" id="condition" value="ALL" onchange="changeMode(this.value)">
								  <span class="checkmark"></span>
								</label>
							</div>
							<div class="col-xs-4">
								<label class="containers">UPPER
								  <input type="radio" name="condition" id="condition" value="UPPER" onchange="changeMode(this.value)">
								  <span class="checkmark"></span>
								</label>
							</div>
							<div class="col-xs-4">
								<label class="containers">LOWER
								  <input type="radio" name="condition" id="condition" value="LOWER" onchange="changeMode(this.value)">
								  <span class="checkmark"></span>
								</label>
							</div>
						</div>
						<div class="col-xs-12" style="margin-top: 10px;" id="4xx">
							<div class="col-xs-12 text-center" style="background-color: darkturquoise;padding: 10px;font-weight: bold;font-size: 20px;margin-bottom: 7px;">
								<span>MENU KHUSUS YCL4XX</span>
							</div>
							<div class="col-xs-3" style="background-color: white;padding-left: 0px;padding-right: 0px;">
								<label>Upper</label>
								<select style="width: 100%" id="cites_upper" class="form-control select2" data-placeholder="Pilih Upper">
									<option value=""></option>
									<option value="-">Tidak Ada</option>
									<option value="Q2">Q2</option>
									<option value="J2">J2</option>
									<option value="R2">R2</option>
									<option value="I2">I2</option>
									<option value="D2">D2</option>
									<option value="I1">I1</option>
									<option value="H2">H2</option>
									<option value="L2">L2</option>
								</select>
							</div>
							<div class="col-xs-3" style="background-color: white;padding-left: 0px;padding-right: 0px;">
								<label>Lower</label>
								<select style="width: 100%" id="cites_lower" class="form-control select2" data-placeholder="Pilih Lower">
									<option value=""></option>
									<option value="-">Tidak Ada</option>
									<option value="Q2">Q2</option>
									<option value="J2">J2</option>
									<option value="R2">R2</option>
									<option value="I2">I2</option>
									<option value="D2">D2</option>
									<option value="I1">I1</option>
									<option value="H2">H2</option>
									<option value="L2">L2</option>
								</select>
							</div>
							<div class="col-xs-3" style="background-color: white;padding-left: 0px;padding-right: 0px;">
								<label>Bell</label>
								<select style="width: 100%" id="cites_gekan" class="form-control select2" data-placeholder="Pilih Bell">
									<option value=""></option>
									<option value="-">Tidak Ada</option>
									<option value="Q2">Q2</option>
									<option value="J2">J2</option>
									<option value="R2">R2</option>
									<option value="I2">I2</option>
									<option value="D2">D2</option>
									<option value="I1">I1</option>
									<option value="H2">H2</option>
									<option value="L2">L2</option>
								</select>
							</div>
							<div class="col-xs-3" style="background-color: white;padding-left: 0px;padding-right: 0px;">
								<label>Barrel</label>
								<select style="width: 100%" id="cites_asagaokan" class="form-control select2" data-placeholder="Pilih Barrel">
									<option value=""></option>
									<option value="-">Tidak Ada</option>
									<option value="Q2">Q2</option>
									<option value="J2">J2</option>
									<option value="R2">R2</option>
									<option value="I2">I2</option>
									<option value="D2">D2</option>
									<option value="I1">I1</option>
									<option value="H2">H2</option>
									<option value="L2">L2</option>
								</select>
							</div>
							<div class="col-xs-12" style="background-color: white;padding-left: 0px;padding-right: 0px;">
								<div class="col-xs-12">
									<label>Pilih Daisha</label>
								</div>
								<?php $index_daisha = 1; ?>
								<?php for ($i=1; $i < 10; $i++) { ?>
									<?php if (!str_contains($daisha[0]->daisha,$i)): ?>
										<div class="col-xs-3" style="padding: 5px;">
											<button class="btn btn-default" onclick="changeTray('{{$i}}')" id="tray_{{$i}}" style="width: 99%;border-color: black;">Daisha {{$i}}</button>
										</div>
										<?php $index_daisha++; ?>
									<?php endif ?>
								<?php } ?>
								<!-- <div class="col-xs-3" style="padding: 5px;">
									<button class="btn btn-default" onclick="changeTray(2)" id="tray_2" style="width: 99%;border-color: black;">Daisha 2</button>
								</div>
								<div class="col-xs-3" style="padding: 5px;">
									<button class="btn btn-default" onclick="changeTray(3)" id="tray_3" style="width: 99%;border-color: black;">Daisha 3</button>
								</div>
								<div class="col-xs-3" style="padding: 5px;">
									<button class="btn btn-default" onclick="changeTray(4)" id="tray_4" style="width: 99%;border-color: black;">Daisha 4</button>
								</div>
								<div class="col-xs-3" style="padding: 5px;">
									<button class="btn btn-default" onclick="changeTray(5)" id="tray_5" style="width: 99%;border-color: black;">Daisha 5</button>
								</div>
								<div class="col-xs-3" style="padding: 5px;">
									<button class="btn btn-default" onclick="changeTray(6)" id="tray_6" style="width: 99%;border-color: black;">Daisha 6</button>
								</div>
								<div class="col-xs-3" style="padding: 5px;">
									<button class="btn btn-default" onclick="changeTray(7)" id="tray_7" style="width: 99%;border-color: black;">Daisha 7</button>
								</div> -->
							</div>
						</div>
						<div class="col-xs-6" style="padding-right: 5px;">
							<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-top: 20px;">
								<thead style="background-color: yellow;">
									<tr>
										<th colspan="2" style="text-align: center; font-size: 1.5vw;background-color: #85bcff">LOWER</th>
									</tr>
									<tr>
										<th style="text-align: left; font-size: 1.5vw;">Model</th>
										<th style="text-align: right; font-size: 1.5vw;">Qty</th>
									</tr>
								</thead>
								<tbody id="resumeTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th style="text-align: left; font-size: 1.5vw;">Total</th>
									<th style="text-align: right; font-size: 1.5vw;" id="totalResume"></th>
								</tfoot>						
							</table>
						</div>
						<div class="col-xs-6" style="padding-left: 0px;">
							<table id="resumeTableUpper" class="table table-bordered table-striped table-hover" style="margin-top: 20px;">
								<thead style="background-color: yellow;">
									<tr>
										<th colspan="2" style="text-align: center; font-size: 1.5vw;background-color: #ff9e9e;">UPPER</th>
									</tr>
									<tr>
										<th style="text-align: left; font-size: 1.5vw;background-color: #1cd622">Model</th>
										<th style="text-align: right; font-size: 1.5vw;background-color: #1cd622">Qty</th>
									</tr>
								</thead>
								<tbody id="resumeTableBodyUpper">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th style="text-align: left; font-size: 1.5vw;">Total</th>
									<th style="text-align: right; font-size: 1.5vw;" id="totalResumeUpper"></th>
								</tfoot>						
							</table>
						</div>
						<!-- <div class="col-xs-12">
							<div class="col-xs-4" style="background-color: white;padding: 0px;">
								<span style="width: 100%;padding: 0px;font-size: 22px;">REGISTRATION</span>
							</div>
							<div class="col-xs-4" style="padding: 0px;">
								<label class="switch">
								  <input type="checkbox" id="return_mode" value="OFF" onchange="returnMode(this.id)">
								  <span class="slider round"></span>
								</label>
							</div>
							<div class="col-xs-4" style="background-color: white;padding: 0px;">
								<span style="width: 100%;padding: 0px;font-size: 22px;">RETURN</span>
							</div>
						</div>
						<div class="col-xs-12">
							<span style="font-size: 1.5vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID Return</span>
							<input id="tagBodyReturn" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 3vw; width: 100%;" disabled="true">
						</div> -->
					</div>
				</div>
			</center>
		</div>
		<div class="col-xs-4" style="padding: 0 0 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #ffa500; background-color: white;">
					<i class="fa fa-arrow-down"></i> RECORD <i class="fa fa-arrow-down"></i>
				</div>
				<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw; margin-top: 5px" border="1">
					<tbody>
						<tr>
							<td style="width: 2%;" id="op_id">-</td>
							<td style="width: 8%;" id="op_name">-</td>
						</tr>
					</tbody>
				</table>
				<div class="col-xs-9" style="padding-left: 0; padding-right: 0;">
					<span style="font-size: 1.5vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID LOWER</span>
					<input id="tagBody" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 3vw; width: 100%;">
					<span style="font-size: 1.5vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID UPPER</span>
					<input id="tagBody2" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 3vw; width: 100%;">
				</div>
				<div class="col-xs-3" style="padding-right: 0;">
					<span style="font-size: 1.5vw; font-weight: bold; color: rgb(255,255,150);">&nbsp;</span>
					<button class="btn btn-danger" id="resetTag" onclick="clearAll()" style="width: 100%; height: 22.5vh; font-size: 3.4vw"><i class="fa fa-refresh"></i></button>
				</div>
				<div class="col-xs-12">
					<span style="font-size: 2vw; font-weight: bold; color: rgb(255,127,80);">Serial Number</i></span>
				</div>
				<input id="serialNumber" type="text" style="border:0; font-weight: bold; background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 4vw" onkeyup="fetchModel();">
				<input id="model" type="text" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" value="Not Found">
				<div class="col-xs-4" style="padding-left: 0;">
					<button class="btn btn-danger" id="btnChange" onclick="modalChange()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-wrench"></i></button>
				</div>
				<div class="col-xs-8" style="padding-left: 0; padding-right: 0;">
					<button class="btn btn-success" id="print" onclick="print()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-print"></i> PRINT</button>
				</div>
			</center>
		</div>
		<div class="col-xs-4" style="padding: 0 10px 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; text-align: center; color: #3c3c3c;background-color: white">
					<i class="fa fa-arrow-down"></i> REGISTRATION LOG <i class="fa fa-arrow-down"></i>
				</div>
				<div style="padding-left: 10px">
					<button class="btn btn-danger btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 40%; font-size: 1.3vw" data-toggle="modal" data-target="#modalReprint">
						<i class="fa fa-print"></i>&nbsp;Reprint
					</button>
					<a href="{{ url("/index/assembly/stamp_record/042") }}" class="btn btn-primary btn-sm" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 40%; font-size: 1.3vw"><i class="fa fa-calendar-check-o"></i>&nbsp;Record</a>
					<table id="logTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(240,240,240);">
							<tr>
								<th style="width: 1%">Serial</th>
								<th style="width: 1%">Model</th>
								<th style="width: 2%">By</th>
								<th style="width: 1%">At</th>
							</tr>
						</thead>
						<tbody id="logTableBody">
						</tbody>
						<tfoot>
						</tfoot>	
					</table>
				</div>
			</center>
		</div>
	</div>
	<input type="hidden" id="employee_id">
	<input type="hidden" id="started_at">
</section>

<div class="modal modal-default fade" id="modalReprint" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Reprint Serial Number</h4>
			</div>
			<div class="modal-body" id="divReprint">
				<label>Serial Number</label>
				<select class="form-control select2" name="serial_number_reprint" style="width: 100%;" data-placeholder="Pilih Serial Number ..." id="serial_number_reprint" required>
					<option value=""></option>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				<button onclick="reprintStamp()" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
			</div>
		</div>
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

<div class="modal fade" id="modalChange">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h4 class="modal-title">Change Model</h4>
				</center>
			</div>
			<div class="modal-body">
				<center>
					<input id="newModel" type="text" style="border:0; font-weight: bold; background-color: #dd4b39; width: 100%; text-align: center; font-size: 4vw; color: yellow;" disabled>
					@foreach($models as $model)
					<button id="{{ $model->model }}" onclick="putModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 30%; font-size: 1.3vw;">{{ $model->model }}</button>
					@endforeach
				</center>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Close</button>
				<button type="button" onclick="updateModel()" class="btn btn-success" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Change Model</button>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
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

	jQuery(document).ready(function() {
		$('#4xx').hide();
		$('#serial_number_reprint').select2({
			allowClear:true,
			dropdownParent: $('#divReprint'),
		});
		$('.select2').select2({
			allowClear:true,
		});
		$("input[name=condition][value=ALL]").prop('checked', true);
		// $("#return_mode").val('OFF');
		// $("#return_mode").prop('checked',false);
		// $("#tagBodyReturn").prop('disabled',true);
		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').val("");
			$('#operator').focus();
		});
		fetchResult();
	});

	function changeTray(id) {
		for(var i = 1; i < 10;i++){
			$('#tray_'+i).css('background-color', 'white');
		}
		$('#tray_'+id).css('background-color', '#a3ffaf');
	}

	function changeMode(vals) {
		if (vals == 'ALL') {
			clearAll();
		}else if(vals == 'UPPER'){
			$('#tagBody').prop('disabled',true);
			$('#tagBody2').removeAttr('disabled');
			$('#tagBody2').focus();
		}else if (vals == 'LOWER') {
			$('#tagBody').prop('disabled',false);
			$('#tagBody2').prop('disabled',true);
			$('#tagBody').focus();
		}
	}

	// function returnMode(id) {
	// 	if ($('#'+id).val() == 'OFF') {
	// 		$("#tagBody").prop('disabled',true);
	// 		$('#tagBody').val('');
	// 		$('#tagBody2').val('');
	// 		$("#tagBodyReturn").removeAttr('disabled');
	// 		$('#tagBodyReturn').val('');
	// 		$('#tagBodyReturn').focus();
	// 		$('#'+id).val('ON');
	// 	}else{
	// 		$('#'+id).val('OFF');
	// 		$('#tagBodyReturn').val('');
	// 		$("#tagBodyReturn").prop('disabled',true);
	// 		$("#tagBody").removeAttr('disabled');
	// 		$('#tagBody').val('');
	// 		$('#tagBody2').val('');
	// 		$('#tagBody').focus();
	// 	}
	// }

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var models = <?php echo json_encode($models); ?>;
	var logs = [];

	function print(){
		var origin_group_code = '042';
		var model = $('#model').val();
		var serial_number = $('#serialNumber').val();
		var tagName = $('#tagBody').val();
		var tagName2 = $('#tagBody2').val();
		var op_id = $('#employee_id').val();
		var started_at = $('#started_at').val();
		var location = 'registration-process';

		var vals = '';
		$("input[name='condition']:checked").each(function (i) {
            vals = $(this).val();
        });

        var cites = '';
        var tray = '';
        if (model.match(/450/gi) || model == 'YCL400AD') {
        	if ($("#cites_gekan").val() == '' || $("#cites_upper").val() == '' || $("#cites_lower").val() == '' || $("#cites_asagaokan").val() == '') {
        		openErrorGritter('Error!','Pilih Semua Cites');
        		audio_error.play();
        		$('#loading').hide();
        		return false;
        	}
        	cites += $("#cites_upper").val();
			cites += '/'+$("#cites_lower").val();
			cites += '/'+$("#cites_gekan").val();
			cites += '/'+$("#cites_asagaokan").val();

			var tray_salah = 0;
			for(var i = 1; i < 10;i++){
				if (document.getElementById('tray_'+i) != null) {
					if (document.getElementById('tray_'+i).style.backgroundColor == 'rgb(163, 255, 175)') {
						tray_salah++;
						tray = i;
					}
				}
			}

			// if (tray_salah == 0) {
			// 	openErrorGritter('Error!','Pilih Daisha');
	  //   		audio_error.play();
	  //   		$('#loading').hide();
	  //   		return false;
			// }

			cites += '/'+tray;
        }

		var data = {
			origin_group_code: origin_group_code,
			model:model,
			serial_number:serial_number,
			tagName:tagName,
			tagName2:tagName2,
			op_id:op_id,
			started_at:started_at,
			vals:vals,
			cites:cites,
			location:location
		}

		$.post('{{ url("input/assembly/registration_process") }}', data, function(result, status, xhr){
			if(result.status){
				fetchResult();
				audio_ok.play();
				openSuccessGritter('Success!', result.message);
				clearAll();
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
				return false;
			}
		});
	}

	function reprintStamp() {
		var serial_number = $('#serial_number_reprint').val();
		if (serial_number == '') {
			alert('Pilih Serial Number');
		}else{
			var data = {
				serial_number:serial_number,
				origin_group_code:'042'
			}

			$.get('{{ url("reprint/assembly/stamp") }}', data, function(result, status, xhr){
				if(result.status){
					$('#serial_number_reprint').val("").trigger('change');
					$('#reprintModal').modal('hide');
					openSuccessGritter('Success!', result.message);
					fetchResult();
				}
				else{
					audio_error.play();
					openErrorGritter(result.message);
					return false;
				}
			});
		}
	}

	function fetchResult(){
		var data = {
			origin_group_code : '042',
			location : 'registration-process'
		}
		$.get('{{ url("fetch/assembly/stamp_result") }}', data, function(result, status, xhr){
			if(result.status){
				$('#logTable').DataTable().clear();
				$('#logTable').DataTable().destroy();
				$('#logTableBody').html('');

				logs = result.logs;
				var tableData = '';
				var no = 1

				var serial_numbers = [];
				var serial_numbers_upper = [];
				var todays = [];
				var todays_upper = [];

				$.each(logs, function(key, value){
					if (no % 2 === 0 ) {
						color = 'style="background-color: #fffcb7"';
					} else {
						color = 'style="background-color: #ffd8b7"';
					}
					tableData += '<tr '+color+'>';
					tableData += '<td style="vertical-align:middle">'+value.serial_number+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.model+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.name+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.created_at+'</td>';
					tableData += '</tr>';
					no += 1;					

					var re = new RegExp('{{date("Y-m-d")}}', 'g');
					if (value.created_at.match(re)) {
						if (!value.serial_number.match(/_U/gi)) {
							serial_numbers.push(value.model);
							todays.push(value.model);
						}
						if (value.serial_number.match(/_U/gi)) {
							serial_numbers_upper.push(value.model);
							todays_upper.push(value.model);
						}
					}
				});

				var sn_unik = serial_numbers.filter(onlyUnique);
				var sn_unik_upper = serial_numbers_upper.filter(onlyUnique);

				$('#resumeTableBody').html('');
				var resumes = '';

				$('#resumeTableBodyUpper').html('');
				var resumes_upper = '';

				var count = 0;
				var count_upper = 0;

				for(var i = 0; i < sn_unik.length;i++){
					resumes += '<tr style="background-color: #fffcb7">';
					resumes += '<td style="text-align: left; font-size: 1.2vw;">'+sn_unik[i]+'</td>';
					var qty = 0;
					for(var j = 0; j < todays.length;j++){
						if (todays[j] == sn_unik[i]) {
							qty++;
						}
					}
					resumes += '<td style="text-align: right; font-size: 1.2vw;">'+qty+'</td>';
					resumes += '</tr>';
					count = count+qty;
				}

				for(var i = 0; i < sn_unik_upper.length;i++){
					resumes_upper += '<tr style="background-color: #fffcb7">';
					resumes_upper += '<td style="text-align: left; font-size: 1.2vw;">'+sn_unik_upper[i]+'</td>';
					var qty = 0;
					for(var j = 0; j < todays_upper.length;j++){
						if (todays_upper[j] == sn_unik_upper[i]) {
							qty++;
						}
					}
					resumes_upper += '<td style="text-align: right; font-size: 1.2vw;">'+qty+'</td>';
					resumes_upper += '</tr>';
					count_upper = count_upper+qty;
				}

				$('#resumeTableBody').append(resumes);
				$('#totalResume').html(count);

				$('#resumeTableBodyUpper').append(resumes_upper);
				$('#totalResumeUpper').html(count_upper);

				$('#logTableBody').append(tableData);

				$('#logTable').DataTable({
					"sDom": '<"top"i>rt<"bottom"flp><"clear">',
					'paging'      	: true,
					'lengthChange'	: false,
					'searching'   	: true,
					'ordering'		: false,
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"infoCallback": function( settings, start, end, max, total, pre ) {
						return "<b>Total "+ total +" pc(s)</b>";
					}
				});

				$('#serial_number_reprint').html("");
				var reprint = "";
				reprint += '<option value=""></option>';
				$.each(result.logsall, function(key, value){
					reprint += '<option value="'+value.serial_number+'">'+value.serial_number+'</option>';
				});
				$('#serial_number_reprint').append(reprint);
			}
			else{
				audio_error.play();
				openErrorGritter(result.message);
				return false;			
			}
		});
	}

	function selectModel(model){
		$('#model').val(model);
		$('#btnChange').prop("disabled", false);
		$('#print').prop("disabled", false);
		$('#4xx').hide();
		if (model.match(/450/gi) || model == 'YCL400AD') {
			$('#4xx').show();
		}
	}

	function fetchModel(){
		var serial_number = $("#serialNumber").val();
		var data = {
			serial_number : serial_number,
			process_code : '1',
			origin_group_code : '042'
		}
		if(serial_number.length == 8) {
			$.get('{{ url("fetch/assembly/model") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.stamp_inventory != null) {
						$('#model').val(result.stamp_inventory.model);
						if(result.stamp_inventory.model.length <= 3 || result.stamp_inventory.model == 'INDONESIA' || result.stamp_inventory.model == 'CHINA'){
							$('#btnChange').prop("disabled", false);
							$('#print').prop("disabled", true);
						}
						else{
							$('#btnChange').prop("disabled", false);
							$('#print').prop("disabled", false);
						}
					}else{
						$('#model').val('Tidak Terdaftar');
					}
				}
				else{
					audio_error.play();
					openErrorGritter(result.message);
					$('#model').val('Not Found');
					$('#serialNumber').prop('disabled', false);
					$('#serialNumber').val('21');
					$('#serialNumber').focus();
					return false;
				}
			});
		}
		else{
			if ($("#model").val().match(/450/gi) || $("#model").val() == 'YCL400AD') {
				$('#print').removeAttr("disabled");
			}else{
				$("#model").val('Not Found');
				$('#print').prop("disabled", true);
			}
			$('#btnChange').prop("disabled", true);
		}
	}

	$('#tagBody').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagBody").val().length == 10){
				var data = {
					tag : $("#tagBody").val(),
					origin_group_code : '042'
				}
				$.get('{{ url("scan/assembly/tag_stamp") }}', data, function(result, status, xhr){
					if(result.status){
						if (result.tag.remark.indexOf('_U') > -1){
							audio_error.play();
							openErrorGritter('Error', 'Anda menggunakan RFID untuk LOWER');
							$('#tagBody').val('');
							$('#tagBody').focus();
							return false;
						}

						$('#tagBody').val(result.tag.remark);
						$('#started_at').val(result.started_at);
						$('#tagBody').prop('disabled', true);
						var vals = '';
						$("input[name='condition']:checked").each(function (i) {
				            vals = $(this).val();
				        });
						if (vals == 'ALL') {
							$('#tagBody2').prop('disabled', false);
							$('#tagBody2').focus();
						}

						if (vals == 'LOWER') {
							$('#serialNumber').prop('disabled', false);
							$('#serialNumber').val('21');
							$('#serialNumber').focus();
						}
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tagBody').val('');
						$('#tagBody').focus();
						return false;
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'RFID tidak valid periksa kembali RFID anda');
				$('#tagBody').val('');
				$('#tagBody').focus();
				return false;
			}
		}
	});

	$('#tagBody2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagBody2").val().length == 10){
				var data = {
					tag : $("#tagBody2").val(),
					origin_group_code : '042'
				}
				$.get('{{ url("scan/assembly/tag_stamp") }}', data, function(result, status, xhr){
					if(result.status){
						if (result.tag.remark.indexOf('_U') < 0){
							audio_error.play();
							openErrorGritter('Error', 'Anda menggunakan RFID untuk UPPER');
							$('#tagBody2').val('');
							$('#tagBody2').focus();
							return false;
						}
						$('#tagBody2').val(result.tag.remark);
						$('#tagBody2').prop('disabled', true);
						$('#serialNumber').prop('disabled', false);
						$('#serialNumber').val('21');
						$('#serialNumber').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tagBody2').val('');
						$('#tagBody2').focus();
						return false;
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'RFID tidak valid periksa kembali RFID anda');
				$('#tagBody2').val('');
				$('#tagBody2').focus();	
				return false;
			}
		}
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				$.get('{{ url("scan/assembly/operator") }}', data, function(result, status, xhr){
					if(result.status){
						audio_ok.play();
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op_id').html(result.employee.employee_id);
						$('#op_name').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						clearAll();
						// intervalTag = setInterval(focusTag, 1000);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
						return false;
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				$("#operator").val("");
				return false;
			}			
		}
	});

	function clearAll(){
		for(var i = 1; i < 10;i++){
			$('#tray_'+i).css('background-color', 'white');
		}
		$("#cites_gekan").val('').trigger('change');
		$("#cites_upper").val('').trigger('change');
		$("#cites_lower").val('').trigger('change');
		$("#cites_asagaokan").val('').trigger('change');
		$('#newModel').val("");
		$('#started_at').val("");
		$('#serialNumber').val("21");
		$('#serialNumber').prop("disabled", true);
		$('#print').prop("disabled", true);
		$('#btnChange').prop("disabled", true);
		$('#model').val("Not Found");
		$('#model').prop("disabled", true);
		var vals = '';
		$("input[name='condition']:checked").each(function (i) {
            vals = $(this).val();
        });

		if (vals == 'ALL') {
			$('#tagBody2').val("");
			$('#tagBody2').prop("disabled", true);
			$('#tagBody').val("");
			$('#tagBody').prop("disabled", false);
			$('#tagBody').focus();
		}else if(vals == 'LOWER'){
			$('#tagBody2').val("");
			$('#tagBody2').prop("disabled", true);
			$('#tagBody').val("");
			$('#tagBody').prop("disabled", false);
			$('#tagBody').focus();
		}else{
			$('#tagBody2').removeAttr("disabled");
			$('#tagBody2').val("");
			$('#tagBody').val("");
			$('#tagBody').prop("disabled", true);
			$('#tagBody2').focus();
		}
	}

	function updateModel(){
		var model = $('#newModel').val();
		var serial_number = $('#serialNumber').val();
		var origin_group_code = '042';
		var location = 'registration-process';
		var op_id = $('#employee_id').val();
		var started_at = $('#started_at').val();

		if(model == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Pilih model terlebih dahulu.');
			return false;
		}

		var data = {
			model : model,
			serial_number : serial_number,
			origin_group_code : origin_group_code,
			location : location,
			op_id : op_id,
			started_at : started_at
		}

		$.post('{{ url("edit/assembly/model") }}', data, function(result, status, xhr){
			if(result.status){
				$('#model').val(result.model);
				$('#newModel').val('');
				$('#print').prop('disabled', false);

				audio_ok.play();
				openSuccessGritter('Success!', result.message);

				$('#modalChange').modal('hide');
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
				return false;
			}
		});
	}

	function modalChange(){
		$('#modalChange').modal('show');
	}

	function putModel(id){
		$('#newModel').val(id);
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

</script>
@endsection

