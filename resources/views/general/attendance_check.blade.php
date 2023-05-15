@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
	.blink {
		animation-duration: 1s;
		animation-name: blink;
		animation-iteration-count: infinite;
		animation-direction: alternate;
		animation-timing-function: ease-in-out;
	}
	@keyframes blink {
		50% {
			opacity: 1;
		}
		100% {
			opacity: 0;
		}
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-5 pull-right" style="text-align: right; height: 1px;">
			<input id="tag" type="text" style="border:0; height: 1px; background-color: #3c3c3c; width: 50px; text-align: center; font-size: 1vw">
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6">
					<span style="color: yellow; font-weight: bold; font-size: 2vw;" id="counter"></span>
				</div>
				<div class="col-xs-6">
					<span style="color: yellow; font-weight: bold; font-size: 2vw;" id="counter2"></span>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6" id="container">
				</div>
				<div class="col-xs-6" id="container2">
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="purpose_code"> 
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#tag').val('');
		$('.select2').prop('selectedIndex', 0).change();
		$('.select2').select2();
		fetchAttendanceList();
		$('#modalPurpose').modal({
			backdrop: 'static',
			keyboard: false
		});
	});

	var myvar = setInterval(waktu,1000);
	var timeref;
	var istirahat = null;
	var visitor = null;
	var x;
	var countDownDate;
	var times_now = new Date();
	timernow = addZero(times_now.getHours())+':'+addZero(times_now.getMinutes())+':'+addZero(times_now.getSeconds());

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function waktu() {
		var time = new Date();
		timeref = addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds());
		if (timeref == '09:00:00') {
			location.reload();
		}
		if (timeref == '11:00:00') {
			location.reload();
		}
	}

	function focusTag(){
		$('#tag').focus();
	}

	function scanAttendance(id){
		var data = {
			tag:id
		}
		$.post('{{ url("scan/general/attendance_check") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				fetchAttendanceList();
				audio_ok.play();
				$('#tag').val('');
				$('#tag').focus();
			}
			else{
				$('#tag').val('');
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 9){
				scanAttendance($("#tag").val());
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Panjang tag tidak sesuai');
				$("#tag").val('');
			}
		}
	});

	function fetchAttendanceList(){
		$.get('{{ url("fetch/general/attendance_check") }}', function(result, status, xhr){
			if(result.status){
				var count_bento = 0;
				var count_live = 0;
				var count_extra = 0;
				var count_overtime = 0;

				var count_all_bento = 0;
				var count_all_live = 0;
				var count_all_extra = 0;
				var count_all_overtime = 0;

				setInterval(focusTag, 1000);
				var data_bento = "";
				var data_live = "";
				var data_extra = "";
				var data_overtime = "";

				$('#container').html("");
				$('#container2').html("");
				$('#counter').html("");

				var empid_bento = '-';
				var name_bento = '-';
				var empid_live = '-';
				var name_live = '-';
				var empid_extra = '-';
				var name_extra = '-';
				var empid_overtime = '-';
				var name_overtime = '-';

				if (result.attendance_lists_bento.length > 0) {
					$.each(result.attendance_lists_bento, function(key, value){
						empid_bento = value.employee_id;
						name_bento = value.name;
					});
				}

				if (result.attendance_lists_live.length > 0) {
					$.each(result.attendance_lists_live, function(key, value){
						empid_live = value.employee_id;
						name_live = value.name;
					});
				}
				if (result.attendance_lists_extra.length > 0) {
					$.each(result.attendance_lists_extra, function(key, value){
						empid_extra = value.employee_id;
						name_extra = value.name;
					});
				}

				if (result.attendance_lists_overtime.length > 0) {
					$.each(result.attendance_lists_overtime, function(key, value){
						empid_overtime = value.employee_id;
						name_overtime = value.name;
					});
				}

				data_bento += '<div class="col-xs-12" style="padding:2px;padding-top:0px">';
				if(empid_bento == '-'){
					data_bento += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				else{
					// count_bento += 1;
					data_bento += '<table class="table table-bordered blink" style="background-color: #ccff90; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				data_bento += '<tbody>';
				data_bento += '<tr>';
				data_bento += '<td style="padding: 0; font-size: 3vw;">'+empid_bento+'</td>';
				data_bento += '</tr>';
				data_bento += '<tr>';
				data_bento += '<td style="padding: 0; font-size: 2.5vw;">'+name_bento+'</td>';
				data_bento += '</tr>';
				data_bento += '</tbody>';
				data_bento += '</table>';
				
				data_bento += '</div>';

				data_live += '<div class="col-xs-12" style="padding:2px;padding-top:0px">';
				if(empid_live == '-'){
					data_live += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				else{
					// count_live += 1;
					data_live += '<table class="table table-bordered blink" style="background-color: #ccff90; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				data_live += '<tbody>';
				data_live += '<tr>';
				data_live += '<td style="padding: 0; font-size: 3vw;">'+empid_live+'</td>';
				data_live += '</tr>';
				data_live += '<tr>';
				data_live += '<td style="padding: 0; font-size: 2.5vw;">'+name_live+'</td>';
				data_live += '</tr>';
				data_live += '</tbody>';
				data_live += '</table>';
				
				data_live += '</div>';

				data_extra += '<div class="col-xs-12" style="padding:2px;padding-top:0px">';
				if(empid_extra == '-'){
					data_extra += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				else{
					// count_live += 1;
					data_extra += '<table class="table table-bordered blink" style="background-color: #ccff90; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				data_extra += '<tbody>';
				data_extra += '<tr>';
				data_extra += '<td style="padding: 0; font-size: 3vw;">'+empid_extra+'</td>';
				data_extra += '</tr>';
				data_extra += '<tr>';
				data_extra += '<td style="padding: 0; font-size: 2.5vw;">'+name_extra+'</td>';
				data_extra += '</tr>';
				data_extra += '</tbody>';
				data_extra += '</table>';
				data_extra += '</div>';


				data_overtime += '<div class="col-xs-12" style="padding:2px;padding-top:0px">';
				if(empid_overtime == '-'){
					data_overtime += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				else{
					// count_live += 1;
					data_overtime += '<table class="table table-bordered blink" style="background-color: #ccff90; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
				}
				data_overtime += '<tbody>';
				data_overtime += '<tr>';
				data_overtime += '<td style="padding: 0; font-size: 3vw;">'+empid_overtime+'</td>';
				data_overtime += '</tr>';
				data_overtime += '<tr>';
				data_overtime += '<td style="padding: 0; font-size: 2.5vw;">'+name_overtime+'</td>';
				data_overtime += '</tr>';
				data_overtime += '</tbody>';
				data_overtime += '</table>';
				data_overtime += '</div>';

				$.each(result.attendance_lists, function(key, value){
					if(value.purpose_code == 'Bento' || value.purpose_code == 'bento'){
						// if(count_all_bento == 0){

						// }
						// else{
							data_bento += '<div class="col-xs-6" style="padding:2px;padding-top:0px">';
							if(value.attend_date == null){
								data_bento += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
							}
							else{
								count_bento += 1;
								data_bento += '<table class="table table-bordered" style="background-color: #ccffff;; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
							}
							data_bento += '<tbody>';
							data_bento += '<tr>';
							data_bento += '<td style="padding: 0; font-size: 2vw;">'+value.employee_id+'</td>';
							data_bento += '</tr>';
							data_bento += '<tr>';
							data_bento += '<td style="padding: 0; font-size: 1.5vw;">'+value.name+'</td>';
							data_bento += '</tr>';
							data_bento += '</tbody>';
							data_bento += '</table>';
							data_bento += '</div>';
						// }
						count_all_bento += 1;
					}else if (value.purpose_code == 'Extra' || value.purpose_code == 'extra') {
						data_extra += '<div class="col-xs-6" style="padding:2px;padding-top:0px">';
						if(value.attend_date == null){
							data_extra += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}
						else{
							count_extra += 1;
							data_extra += '<table class="table table-bordered" style="background-color: #ccffff;; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}
						data_extra += '<tbody>';
						data_extra += '<tr>';
						data_extra += '<td style="padding: 0; font-size: 2vw;">'+value.employee_id+'</td>';
						data_extra += '</tr>';
						data_extra += '<tr>';
						data_extra += '<td style="padding: 0; font-size: 1.5vw;">'+value.name+'</td>';
						data_extra += '</tr>';
						data_extra += '</tbody>';
						data_extra += '</table>';
						data_extra += '</div>';
						
						count_all_extra += 1;


					}else if (value.purpose_code == 'Overtime' || value.purpose_code == 'overtime') {
						data_overtime += '<div class="col-xs-6" style="padding:2px;padding-top:0px">';
						if(value.attend_date == null){
							data_overtime += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}
						else{
							count_overtime += 1;
							data_overtime += '<table class="table table-bordered" style="background-color: #ccffff;; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}
						data_overtime += '<tbody>';
						data_overtime += '<tr>';
						data_overtime += '<td style="padding: 0; font-size: 2vw;">'+value.employee_id+'</td>';
						data_overtime += '</tr>';
						data_overtime += '<tr>';
						data_overtime += '<td style="padding: 0; font-size: 1.5vw;">'+value.name+'</td>';
						data_overtime += '</tr>';
						data_overtime += '</tbody>';
						data_overtime += '</table>';
						data_overtime += '</div>';
						
						count_all_overtime += 1;
					}
					else{
						// if(count_all_live == 0){
							
						// }
						// else{
							data_live += '<div class="col-xs-6" style="padding:2px;padding-top:0px">';
							if(value.attend_date == null){
								data_live += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
							}
							else{
								count_live += 1;
								data_live += '<table class="table table-bordered" style="background-color: #ccffff;; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
							}
							data_live += '<tbody>';
							data_live += '<tr>';
							data_live += '<td style="padding: 0; font-size: 2vw;">'+value.employee_id+'</td>';
							data_live += '</tr>';
							data_live += '<tr>';
							data_live += '<td style="padding: 0; font-size: 1.5vw;">'+value.name+'</td>';
							data_live += '</tr>';
							data_live += '</tbody>';
							data_live += '</table>';
							data_live += '</div>';
						// }

						count_all_live += 1;
					}
				});
			$('#container').append(data_bento);
			$('#counter').html('<center><span style="color: white; font-weight: bold; font-size: 3vw;">BENTO '+count_bento+' of '+count_all_bento+'</span></center>');

				// $('#container2').append(data_overtime);
				// 	$('#counter2').html('<center><span style="color: white; font-weight: bold; font-size: 3vw;">Overtime '+count_overtime+' of '+count_all_overtime+'</span></center>');

				if (timernow >= '07:00:00' && timernow <= '14:00:00') {
					$('#container2').append(data_live);
					$('#counter2').html('<center><span style="color: white; font-weight: bold; font-size: 3vw;">LIVE COOKING '+count_live+' of '+count_all_live+'</span></center>');

				}else{
					$('#container2').append(data_overtime);
					$('#counter2').html('<center><span style="color: white; font-weight: bold; font-size: 3vw;">Overtime '+count_overtime+' of '+count_all_overtime+'</span></center>');
				}
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);				
			}
		});
}

	// function fetchAttendanceList(){
	// 	$.get('{{ url("fetch/general/attendance_check") }}', function(result, status, xhr){
	// 		if(result.status){
	// 			var count_ok = 0;
	// 			var count_all = 0;
	// 			setInterval(focusTag, 1000);
	// 			var attendance_data = "";
	// 			$('#container').html("");
	// 			$.each(result.attendance_lists, function(key, value){
	// 				var name = value.NAME;

	// 				if(key == 0){
	// 					attendance_data += '<div class="col-xs-12" style="padding:2px;padding-top:0px">';
	// 					if(value.attend_date == null){
	// 						attendance_data += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
	// 					}else{
	// 						count_ok += 1;
	// 						attendance_data += '<table class="table table-bordered blink" style="background-color: #ccff90; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
	// 					}
	// 					attendance_data += '<tbody>';
	// 					attendance_data += '<tr>';
	// 					attendance_data += '<td style="padding: 0; width:70%; font-size:3vw;">'+value.employee_id+'</td>';
	// 					attendance_data += '<td style="padding: 0; width:30%; font-size:3vw;">'+value.department+'</td>';
	// 					attendance_data += '</tr>';
	// 					attendance_data += '<tr>';
	// 					attendance_data += '<td colspan="2" style="padding: 0; font-size:5vw;">'+name.substring(0,30).toUpperCase()+'</td>';
	// 					attendance_data += '</tr>';
	// 					attendance_data += '</tbody>';
	// 					attendance_data += '</table>';
	// 					attendance_data += '</div>';
	// 				}
	// 				else{						
	// 					attendance_data += '<div class="col-xs-2" style="padding:2px;padding-top:0px">';
	// 					if(value.attend_date == null){
	// 						attendance_data += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
	// 					}else{
	// 						count_ok += 1;
	// 						attendance_data += '<table class="table table-bordered" style="background-color: #ccffff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
	// 					}
	// 					attendance_data += '<tbody>';
	// 					attendance_data += '<tr>';
	// 					attendance_data += '<td style="padding: 0; width:70%;">'+value.employee_id+'</td>';
	// 					attendance_data += '<td style="padding: 0; width:30%;">'+value.department+'</td>';
	// 					attendance_data += '</tr>';
	// 					attendance_data += '<tr>';
	// 					attendance_data += '<td colspan="2" style="padding: 0;">'+name.substring(0,30)+'</td>';
	// 					attendance_data += '</tr>';
	// 					attendance_data += '</tbody>';
	// 					attendance_data += '</table>';
	// 					attendance_data += '</div>';
	// 				}

	// 				count_all += 1;

	// 			});
	// 			$('#purpose_code').text(result.attendance_lists[0].purpose_code);

	// 			console.log(result.attendance_lists[0].purpose_code);
	// 			$('#counter').text(' ('+count_ok+' of '+count_all+')');
	// 			$('#container').append(attendance_data);
	// 		}
	// 		else{
	// 			audio_error.play();
	// 			openErrorGritter('Error!', result.message);
	// 		}
	// 	});
	// }
	
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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