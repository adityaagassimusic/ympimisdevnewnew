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
		<div class="col-xs-6" style="padding: 0 0 0 10px;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #3d9970;background-color: white">
					<i class="fa fa-arrow-down"></i> ➀ PILIH MODEL <i class="fa fa-arrow-down"></i>
				</div>
				<div>
					<div class="row" style="padding-right: 10px">
						@foreach($models as $model)
						@if($model->material_description != 'YCL-255E//ID')
						<button id="{{ $model->material_number }}_{{ $model->material_description }}" onclick="selectModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 45%; font-size: 2vw; font-weight: bold;">{{ $model->material_description }}</button>
						@endif
						@endforeach
					</div>
				</div>
				<div>
					<div class="row" style="padding-right: 10px">
						<button id="J" onclick="selectJapan(id)" type="button" class="btn btn-info btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 45%; font-size: 2vw; font-weight: bold;">JAPAN</button>
						<button id="NJ" onclick="selectJapan(id)" type="button" class="btn btn-info btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 45%; font-size: 2vw; font-weight: bold;">NOT JAPAN</button>
					</div>
				</div>
			</center>
		</div>
		<div class="col-xs-6" style="padding: 0 0 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #ffa500; background-color: white;">
					<i class="fa fa-arrow-down"></i> PILIH TANGGAL <i class="fa fa-arrow-down"></i>
				</div>
				<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw; margin-top: 5px" border="1">
					<tbody>
						<tr>
							<td style="width: 2%;" id="op_id">-</td>
							<td style="width: 8%;" id="op_name">-</td>
						</tr>
					</tbody>
				</table>
				<div class="col-xs-12" style="padding-left: 0; padding-right: 0;">
					<span style="font-size: 1.5vw; font-weight: bold; color: rgb(255,255,150);">Pilih Tanggal</span>
					<input id="date" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 3vw; width: 100%;" readonly>
				</div>
				<div class="col-xs-12">
					<span style="font-size: 2vw; font-weight: bold; color: white">Model</i></span>
				</div>
				<input id="gmc" type="hidden" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" value="">
				<input id="model" type="text" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" value="Not Found" readonly="">
				<input id="japan" type="text" style="border:0; font-weight: bold; background-color: lightblue; width: 100%; text-align: center; font-size: 4vw" value="" readonly="">
				<div class="col-xs-4" style="padding-left: 0;">
					<button class="btn btn-danger" id="btnChange" onclick="modalChange()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-wrench"></i></button>
				</div>
				<div class="col-xs-8" style="padding-left: 0; padding-right: 0;">
					<button class="btn btn-success" id="print" onclick="print()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-print"></i> PRINT</button>
				</div>
			</center>
		</div>
	</div>
	<input type="hidden" id="employee_id">
	<input type="hidden" id="started_at">
</section>

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

	function selectModel(model){
		$('#model').val(model.split('_')[1]);
		$('#gmc').val(model.split('_')[0]);
	}

	function selectJapan(japan){
		$('#japan').val(japan);
	}

	jQuery(document).ready(function() {
		$('#date').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').val("");
			$('#operator').focus();
		});
	});

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

	function print() {
		if ($('#date').val() == '' || $('#model').val() == '' || $('#model').val() == 'Not Found' || $('#japan').val() == '') {
			openErrorGritter('Error!','Pilih Tanggal dan Model');
			audio_error.play();
			return false;
		}
		var date = $('#date').val();
		var model = $('#gmc').val();
		var japan = $('#japan').val();

		window.open('{{ url("index/assembly/clarinet/label_outer_alone") }}'+'/'+date+'/'+model+'/'+japan, '_blank');
		
	}

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
		$('#date').val("");
		$('#date').focus();
		$('#started_at').val("");
		$('#model').val("Not Found");
		$('#gmc').val("");
		$('#japan').val("");
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

