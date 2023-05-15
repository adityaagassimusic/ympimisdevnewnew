@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
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
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
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
  left: -20px;
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
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }} - {{ $product }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
			<div class="col-xs-5" style="padding-bottom: 12px;padding-left: 0px;">
					<!-- <input type="text" id="operators" placeholder="Operator" > -->
					<div class="row" style="background-color: white;height: 100%;margin-left: 15px;height: 45px;vertical-align: middle;">
						<input type="text" name="operators" id="operators" placeholder="-" style="width: 100%;font-weight: bold;font-size: 20px;height: 45px;text-align: center;" readonly="">
					</div>
			</div>
			<div class="col-xs-2" style="padding-bottom: 12px;text-align:center;">
				<select class="form-control" id="process" name="process" data-placeholder="Pilih Process" style="width: 100%;height: 45px;text-align: center;font-size: 25px;">
					<option value="IN">IN</option>
					<option value="OUT">OUT</option>
				</select>
			</div>
			<div class="col-xs-5" style="padding-bottom: 12px;text-align:center;padding-left: 0px;">
				<input type="text" name="tag" id="tag" placeholder="Scan RFID di Sini . . ." style="width: 100%;font-weight: bold;font-size: 20px;height: 45px;text-align: center;padding-left: 0px;">
			</div>
		<!-- <div class="col-xs-6" style="text-align: center;padding-left: 5px;"> -->
			<!-- <div class="col-xs-12" style="padding-right: 0px;">
				<input type="text" id="tag_product" placeholder="Scan Card Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
			</div> -->
			<!-- <div class="col-xs-2" style="padding-right: 0px;padding-left: 0px">
				<button class="btn btn-danger" onclick="cancel()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
					CANCEL
				</button>
			</div> -->
		<!-- </div> -->
		<!-- <div class="col-xs-12" style="text-align: center;" id="divTray">
			<div style="width: 100%;background-color: lightgreen;padding: 5px;font-weight: bold;">
				DAISHA
			</div>
			<div style="width: 100%;background-color: white;padding: 5px;font-weight: bold;font-size: 50px">
				<span id="daisha_info"></span>
			</div>
		</div> -->
		<div class="col-xs-12" style="text-align: center;padding-top: 10px">
			<div class="box box-solid">
				<div class="box-body">
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
						<input type="hidden" id="operator_id">
						<input type="hidden" id="operator_name">
			            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
			                <tr>
			                  <th style="width: 1%;">#</th>
			                  <th style="width: 2%;text-align: left">Card</th>
			                  <th style="width: 2%;text-align: left">Serial Number</th>
							  <th style="width: 2%;text-align: left">Model</th>
							  <th style="width: 2%;text-align: left">Action</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
		            <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
		            	<button class="btn btn-danger" id="btn_cancel" onclick="cancelSeasoning()" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">CANCEL</button>
		            </div>
		            <div class="col-xs-6" style="padding-right: 0px;padding-left: 5px;">
		            	<button class="btn btn-success" id="btn_finish" onclick="startSeasoning('start')" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">PROSES SEASONING</button>
		            </div>
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
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card / Ketik NIK" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
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

	var counter = 0;
	var arrPart = [];
	var intervalCheck;

	jQuery(document).ready(function() {
		$('#resultScanBody').html("");
		$('#tag').removeAttr('disabled');
		$('#tag').val("");
		$('#daisha_info').html('');
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		// fillResult();
		// setInterval(fillResult,200000);

      $('body').toggleClass("sidebar-collapse");
		
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_id").val("");
		$("#operator_name").val("");
		$("#operators").val("-");
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operators').val(result.employee.employee_id+' - '+result.employee.name);
						$('#operator_id').val(result.employee.employee_id);
						$('#operator_name').val(result.employee.name);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
						$('#tag').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}		
		}
	});

	var packing = null;
	var count_index = 0;

	var serial_numbers = [];
	var serial_numbers_all = [];

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 10){
				$('#loading').show();
				var data = {
					tag : $("#tag").val(),
					origin_group_code:'{{$origin_group_code}}',
					statuses:'PROGRESS',
				}
				
				$.get('{{ url("scan/seasoning") }}', data, function(result, status, xhr){
					if(result.status){
						if (serial_numbers.includes(result.tags.serial_number)) {
							audio_error.play();
							$('#loading').hide();
							openErrorGritter('Error!','Serial Number sudah ada di List');
							$('#tag').removeAttr('disabled');
							$('#tag').val('');
							$('#tag').focus();
							return false;
						}
						var scanBody = '';
						scanBody += '<tr id="'+result.tags.serial_number+'">';
						scanBody += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+(count_index+1)+'</td>';
						scanBody += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+result.tags.remark+'</td>';
						scanBody += '<td style="text-align:center;border:1px solid black;font-size:17px;font-weight:bold;color:red">'+result.tags.serial_number+'</td>';
						scanBody += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+result.tags.model+'</td>';
						scanBody += '<td style="text-align:center;border:1px solid black;padding:2px;">';
						scanBody += '<div class="col-xs-4">';
						scanBody += '<button class="btn btn-danger btn-sm" onClick="removeSerial(\''+result.tags.serial_number+'\')"><i class="fa fa-trash"></i>';
						scanBody += '</button>';
						scanBody += '</div>';
						if ($('#process').val() == 'IN') {
							scanBody += '<div class="col-xs-4">';
								scanBody += '<label class="containers">SET';
								  scanBody += '<input type="radio" name="condition_'+count_index+'" id="condition_'+count_index+'" value="SET">';
								  scanBody += '<span class="checkmark"></span>';
								scanBody += '</label>';
							scanBody += '</div>';
							scanBody += '<div class="col-xs-4">';
								scanBody += '<label class="containers">KANGO';
								  scanBody += '<input type="radio" name="condition_'+count_index+'" id="condition_'+count_index+'" value="KANGO">';
								  scanBody += '<span class="checkmark"></span>';
								scanBody += '</label>';
							scanBody += '</div>';
						}
						scanBody += '</td>';

						scanBody += '</tr>';
						serial_numbers.push(result.tags.serial_number);
						serial_numbers_all.push({
							'tag':$('#tag').val(),
							'remark':result.tags.remark,
							'serial_number':result.tags.serial_number,
							'model':result.tags.model,
						});
						count_index++;
						$('#resultScanBody').append(scanBody);
						openSuccessGritter('Success!', result.message);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
						$('#tag').focus();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
						$('#tag').focus();
					}
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Tag Invalid.');
				audio_error.play();
				$('#tag').removeAttr('disabled');
				$("#tag").val("");
			}		
		}
	});

	function removeSerial(serial_number) {
		serial_numbers_all = serial_numbers_all.filter(tags => tags.serial_number != serial_number);
		serial_numbers.splice( $.inArray(serial_number), 1 );
		$('#'+serial_number).remove();
		$('#tag').val('');
		$('#tag').focus();
	}

	function cancel(){
		$('#tag_product').val("");
		$('#tag_product').focus();
	}

	function cancelTag(){
		$('#modalModel').modal('hide');
		$('#modalLocation').modal('hide');
		$('#tag_product').val("");
		$('#tag_product').focus();
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fillResult() {
		var data = {
			origin_group_code:'{{$origin_group_code}}'
		}
		$.get('{{ url("fetch/seasoning_in") }}',data, function(result, status, xhr){
			if(result.status){
				all_tray = result.all_tray;
				// $('#divTray').html('');

				// var tray = '';

				// for(var i = 0; i < result.tray.length;i++){
				// 	tray += '<div class="col-xs-4" style="padding:5px">';
				// 	tray += '<button class="btn btn-success" style="width:100%;font-weight:bold;font-size:30px;"';
				// 	// tray += 'onClick="selectTray(\''+result.tray[i].location+'\',\''+result.tray[i].seasoning_id+'\')"';
				// 	tray += '>DAISHA '+result.tray[i].location+'<br>';
				// 	tray += '</button>';
				// 	tray += '</div>';
				// }

				trays = result.tray;

				// $('#divTray').append(tray);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function selectTray(tray,seasoning_id) {
		$('#loading').show();
		$('#tray').val("");
		$('#resultScanBody').html('');
		var trays = '';
		var index = 1;
		for(var i = 0; i < all_tray.length;i++){
			var re = new RegExp(all_tray[i].seasoning_id, 'g');
			if (tray == all_tray[i].location && seasoning_id.match(re)) {
				trays += '<tr>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+index+'</td>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].location+'</td>';
				trays += '<td style="text-align:right;padding-left:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[0]+'</td>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[1]+'</td>';
				trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].timestamps+'</td>';
				var packing_status = '';
				for(var j = 0; j < packing.length;j++){
					if (packing[j].serial_number == all_tray[i].material.split(' - ')[2]) {
						packing_status = 'Sudah Packing';
					}
				}
				if (packing_status == '') {
					trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;font-weight:bold;color:red;">'+all_tray[i].material.split(' - ')[2]+'</td>';
					trays += '<td style="text-align:left;padding-left:5px;border:1px solid black;"><label class="container_checkmark" style="color: green;font-size: 13px;padding-left: 35px;" align="center">Check<input type="checkbox" name="check" id="check" class="check" value="'+all_tray[i].id+'"><span class="checkmark_checkmark"></span></label></td>';
				}else{
					trays += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+all_tray[i].material.split(' - ')[2]+'</td>';
					trays += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+packing_status+'</td>';
				}
				trays += '</tr>';
				index++;
			}
		}
		$('#resultScanBody').append(trays);
		$('#tray').val(tray);
	}

	function cancelSeasoning() {
		serial_numbers = [];
		serial_numbers_all = [];
		$("#tray").val('');
		$("#daisha_info").html('');
		$('#tag').removeAttr('disabled');
		$("#tag").val('');
		$('#tag').focus();
		$('#resultScanBody').html('');
	}

	function startSeasoning(empty) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			var condition = [];
			if ($('#process').val() == 'IN') {
				var salah_condition = 0;
				for(var i = 0; i < count_index;i++){
					if ($('#'+serial_numbers[i]).text() != '') {
						var decision_input = '';
						$("input[name='condition_"+i+"']:checked").each(function (i) {
							decision_input = $(this).val();
							condition.push(decision_input);
				        });
				        if (decision_input == '') {
				        	salah_condition++;
				        }
					}
				}
				if (salah_condition > 0) {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!','Pilih SET / KANGO');
					return false;
				}
			}

			var data = {
				serial_numbers_all:serial_numbers_all,
				serial_numbers:serial_numbers,
				condition:condition,
				operator_id:$('#operator_id').val(),
				operator_name:$('#operator_name').val(),
				process:$('#process').val()
			}

			$.post('{{ url("input/seasoning_progress") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success','Success Proses Seasoning');
					fillResult();
					$('#tag').removeAttr('disabled');
					$('#tag').val('');
					$('#tag').focus();
					$('#resultScanBody').html('');
					$('#tray').val('');
					serial_numbers = [];
					serial_numbers_all = [];
					count_index = 0;
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
					return false;
				}
			});
		}
	}

	function timeDifference(date1,date2) {
	    var difference = date1.getTime() - date2.getTime();

	    var daysDifference = Math.floor(difference/1000/60/60/24);
	    difference -= daysDifference*1000*60*60*24

	    var hoursDifference = Math.floor(difference/1000/60/60);
	    difference -= hoursDifference*1000*60*60

	    var minutesDifference = Math.floor(difference/1000/60);
	    difference -= minutesDifference*1000*60

	    var secondsDifference = Math.floor(difference/1000);

	    var day = '';
	    var hour = '';
	    var min = '';
	    var sec = '';
	    if (daysDifference != 0) {
	    	day = addZero(daysDifference);
	    }

	    // if (hoursDifference != 0) {
	    	hour = addZero(hoursDifference);
	    // }

	    // if (minutesDifference != 0) {
	    	min = addZero(minutesDifference);
	    // }

	    // if (secondsDifference != 0) {
	    	sec = addZero(secondsDifference);
	    // }
	    return hour+':' + min+':' + sec;
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
</script>
@endsection