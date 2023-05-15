@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		border-top: 2px solid white;
		vertical-align: middle;
		text-align: center;
		padding:1px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}
	.content{
		color: white;
		font-weight: bold;
	}

	hr {
		margin: 0px;
	}

	.akan {
		color:black;background-color:#fff833;
		cursor: pointer;
	}

	.idle {
		color:#fcff38;background-color:#575c57;
	}
	.complete {
		cursor: pointer;
		color:white;background-color:#575c57;
	}
	
	/*@-webkit-keyframes akan {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			opacity: 0;
		}
		50%, 100% {
			background-color: rgb(243, 156, 18);
		}
	}*/

	.sedang {
		background-color: #4ff05a;
		color: black;
		cursor: pointer;
	}

	/*@-webkit-keyframes sedang {
		0%, 49% {
			background: #575c57;
			color: #fcff38;
		}
		50%, 100% {
			background-color: #4ff05a;
			color: black;
		}
	}*/
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding: 0px;">
	<!-- <span style="padding-top: 0px">
		<center><h1><b>{{ $page }}</b></h1></center>
	</span> -->
	<div class="row">
		<div class="col-xs-12">
			<table id="driverTable" class="table table-bordered">
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">Driver</th>
						<th style="width: 0.66%; padding: 0;">#1</th>
						<th style="width: 0.66%; padding: 0;">#2</th>
						<th style="width: 0.66%; padding: 0;">#3</th>
						<th style="width: 0.66%; padding: 0;">#4</th>
						<th style="width: 0.66%; padding: 0;">#5</th>
						<th style="width: 0.66%; padding: 0;">#6</th>
						<th style="width: 0.66%; padding: 0;">#7</th>
						<th style="width: 0.66%; padding: 0;">#8</th>
						<th style="width: 0.66%; padding: 0;">#9</th>
						<th style="width: 0.66%; padding: 0;">#10</th>
					</tr>
				</thead>
				<tbody id="driverTableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="modal fade" id="myModal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="color: black; padding-bottom: : 0px;">
					<!-- <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4> -->
					<center style="background-color: orange"><span style="font-weight: bold;font-size: 30px" class="modal-title" id="judul_table"></span></center>
				</div>
				<div class="modal-body" style="padding-top: 0px;">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered" style="width: 100%;">
								<!-- <thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 15%;">No.</th>
										<th style="width: 15%;">WS</th>
										<th style="width: 25%;">Material Number</th>
										<th style="width: 45%;">Material Description</th> 
									</tr>
								</thead> -->
								<tbody style="color: black">
									<tr>
										<td width="20%" style="background-color: #AFECE7; text-align:left;padding-left:7px;border: 1px solid black;font-size:18px;">Order By:</td>
										<td width="70%" style="border: 1px solid black;font-size:18px;text-align: left;padding-left: 7px;" id="by"></td>
									</tr>
									<tr>
										<td width="20%" style="background-color: #AFECE7; text-align:left;padding-left:7px;border: 1px solid black;font-size:18px;">City:</td>
										<td width="70%" style="border: 1px solid black;font-size:18px;text-align: left;padding-left: 7px;" id="city"></td>
									</tr>
									<tr>
										<td width="20%" style="background-color: #AFECE7; text-align:left;padding-left:7px;border: 1px solid black;font-size:18px;">Destination:</td>
										<td width="70%" style="border: 1px solid black;font-size:18px;text-align: left;padding-left: 7px;" id="destination"></td>
									</tr>
									<tr>
										<td width="20%" style="background-color: #AFECE7; text-align:left;padding-left:7px;border: 1px solid black;font-size:18px;">Time:</td>
										<td width="70%" style="border: 1px solid black;font-size:18px;text-align: left;padding-left: 7px;" id="time"></td>
									</tr>
									<tr>
										<td width="20%" style="background-color: #AFECE7; text-align:left;padding-left:7px;border: 1px solid black;font-size:18px;">Passenger:</td>
										<td width="70%" style="border: 1px solid black;font-size:18px;text-align: left;padding-left: 7px;" id="passenger"></td>
									</tr>
									<tr>
										<td width="20%" style="background-color: #AFECE7; text-align:left;padding-left:7px;border: 1px solid black;font-size:18px;">Car:</td>
										<td width="70%" style="border: 1px solid black;font-size:18px;text-align: left;padding-left: 7px;" id="car"></td>
									</tr>
								</tbody>
							</table>

						<center><span style="width: 100%;color: black">
							Scan ID Card Driver
						</span></center>
						<input type="text" placeholder="Scan ID Card Here" class="form-control" name="tag" id="tag" style="width: 100%">
						<input type="hidden" placeholder="Scan ID Card Here" class="form-control" name="driver_id" id="driver_id" style="width: 100%">

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
				</div>
			</div>
		</div>
	</div>
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
		fetchTable();
		$('#tag').val('');
		setInterval(fetchTable, 10000);
		$('.selectPur').select2({
	      allowClear:true
	    });
	});
	var time_awal = '';

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	$('#myModal').on('shown.bs.modal', function () {
	    $('#tag').focus();
	})  

	function fetchTable(){

		$.get('{{ url("fetch/ga_control/driver_monitoring") }}',  function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					akan_wld = [];
					akan_wld_kosong = [];
					sedang = [];
					sedang_kosong = [];

					result.duty_time_alarm.sort(dynamicSort('time'));

					if (result.duty_time_alarm.length >0) {
						time_awal = result.duty_time_alarm[0].time;
					}else{
						time_awal = '1990-12-20 00:00:01';
					}
					waktu();

					$('#driverTableBody').html("");
					var driverTableBody = "";
					var i = 0;
					var driver = [];
					$.each(result.orders, function(index, value){
						
						if (i % 2 === 0 ) {
							color = 'style="background-color: #8ab3ff;color:rgb(0,0,0);font-size:16px"';
						} else {
							color = 'style="background-color: #1c4ca6;color:white;font-size:16px"';
						}

						var duty_1 = "";
						var duty_status = 0;
						if (value.queue_1.split('_')[1] == 'akan' || value.queue_1.split('_')[1] == 'sedang') {
							duty_1 = 'onclick="dutyAction(\''+value.queue_1.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_1.split('_')[0].replace("'","")+'\',\''+value.queue_1.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_1 = '';
						}
						if (value.queue_1.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_2 = "";
						if (value.queue_2.split('_')[1] == 'akan' || value.queue_2.split('_')[1] == 'sedang') {
							duty_2 = 'onclick="dutyAction(\''+value.queue_2.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_2.split('_')[0].replace("'","")+'\',\''+value.queue_2.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_2 = '';
						}
						if (value.queue_2.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_2 = "";
						if (value.queue_2.split('_')[1] == 'akan' || value.queue_2.split('_')[1] == 'sedang') {
							duty_2 = 'onclick="dutyAction(\''+value.queue_2.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_2.split('_')[0].replace("'","")+'\',\''+value.queue_2.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_2 = '';
						}
						if (value.queue_2.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_3 = "";
						if (value.queue_3.split('_')[1] == 'akan' || value.queue_3.split('_')[1] == 'sedang') {
							duty_3 = 'onclick="dutyAction(\''+value.queue_3.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_3.split('_')[0].replace("'","")+'\',\''+value.queue_3.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_3 = '';
						}
						if (value.queue_3.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_4 = "";
						if (value.queue_4.split('_')[1] == 'akan' || value.queue_4.split('_')[1] == 'sedang') {
							duty_4 = 'onclick="dutyAction(\''+value.queue_4.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_4.split('_')[0].replace("'","")+'\',\''+value.queue_4.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_4 = '';
						}
						if (value.queue_4.split('_')[1] == 'sedang') {
							duty_status++;
						}


						var duty_5 = "";
						if (value.queue_5.split('_')[1] == 'akan' || value.queue_5.split('_')[1] == 'sedang') {
							duty_5 = 'onclick="dutyAction(\''+value.queue_5.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_5.split('_')[0].replace("'","")+'\',\''+value.queue_5.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_5 = '';
						}
						if (value.queue_5.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_6 = "";
						if (value.queue_6.split('_')[1] == 'akan' || value.queue_6.split('_')[1] == 'sedang') {
							duty_6 = 'onclick="dutyAction(\''+value.queue_6.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_6.split('_')[0].replace("'","")+'\',\''+value.queue_6.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_6 = '';
						}
						if (value.queue_6.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_7 = "";
						if (value.queue_7.split('_')[1] == 'akan' || value.queue_7.split('_')[1] == 'sedang') {
							duty_7 = 'onclick="dutyAction(\''+value.queue_7.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_7.split('_')[0].replace("'","")+'\',\''+value.queue_7.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_7 = '';
						}
						if (value.queue_7.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_8 = "";
						if (value.queue_8.split('_')[1] == 'akan' || value.queue_8.split('_')[1] == 'sedang') {
							duty_8 = 'onclick="dutyAction(\''+value.queue_8.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_8.split('_')[0].replace("'","")+'\',\''+value.queue_8.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_8 = '';
						}
						if (value.queue_8.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_9 = "";
						if (value.queue_9.split('_')[1] == 'akan' || value.queue_9.split('_')[1] == 'sedang') {
							duty_9 = 'onclick="dutyAction(\''+value.queue_9.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_9.split('_')[0].replace("'","")+'\',\''+value.queue_9.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_9 = '';
						}
						if (value.queue_9.split('_')[1] == 'sedang') {
							duty_status++;
						}

						var duty_10 = "";
						if (value.queue_10.split('_')[1] == 'akan' || value.queue_10.split('_')[1] == 'sedang') {
							duty_10 = 'onclick="dutyAction(\''+value.queue_10.split('_')[2].replace("'","")+'\',\''+result.orders2[i].queue_10.split('_')[0].replace("'","")+'\',\''+value.queue_10.split('_')[3].replace("'","")+'\')"';
						}else{
							duty_10 = '';
						}
						if (value.queue_10.split('_')[1] == 'sedang') {
							duty_status++;
						}

						if (duty_status > 0) {
							color = 'style="background-color: #575c57;color:white;font-size:16px"';
							duty_statuses = 'Bertugas';
						}else{
							duty_statuses = '';
						}

						var colorJumlah = 'style="background-color: #f73939;font-size:30px"';
						driverTableBody += '<tr style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">';
						driverTableBody += '<td height="5%" '+color+' id="'+value.employee_id+'">'+value.employee_id+'<br>'+value.employee_name+'<br>Posisi : '+value.position+'<br><span style="color:#ff5c5c">'+duty_statuses+'</span></td>';
						driverTableBody += '<td '+duty_1+' class="'+value.queue_1.split('_')[1]+'">'+value.queue_1.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_2+' class="'+value.queue_2.split('_')[1]+'">'+value.queue_2.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_3+' class="'+value.queue_3.split('_')[1]+'">'+value.queue_3.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_4+' class="'+value.queue_4.split('_')[1]+'">'+value.queue_4.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_5+' class="'+value.queue_5.split('_')[1]+'">'+value.queue_5.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_6+' class="'+value.queue_6.split('_')[1]+'">'+value.queue_6.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_7+' class="'+value.queue_7.split('_')[1]+'">'+value.queue_7.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_8+' class="'+value.queue_8.split('_')[1]+'">'+value.queue_8.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_9+' class="'+value.queue_9.split('_')[1]+'">'+value.queue_9.split('_')[0]+'</td>';
						driverTableBody += '<td '+duty_10+' class="'+value.queue_10.split('_')[1]+'">'+value.queue_10.split('_')[0]+'</td>';
						driverTableBody += '</tr>';
							
						i += 1;
					});

$('#driverTableBody').append(driverTableBody);
}
else{
	alert('Attempt to retrieve data failed.');
}
}
});
}

	function waktu() {
		var time = new Date();
		timeref = addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds());

		var d1 = new Date (time_awal),
		    d2 = new Date ( time_awal );
		d2.setMinutes ( d2.getMinutes() - 15 );
		timeref_awal = addZero(d2.getHours())+':'+addZero(d2.getMinutes())+':'+addZero(d2.getSeconds());
		timeref_akhir = addZero(d1.getHours())+':'+addZero(d1.getMinutes())+':'+addZero(d1.getSeconds());
		if (timeref >= timeref_awal && timeref <= timeref_akhir) {
			alarm_error.play();
		}
	}

function dutyAction(id,title,passenger) {
	$('#driver_id').val(id);
	$('#by').html(title.split('<br>')[0].split(': ')[1]);
	$('#city').html(title.split('<br>')[1].split(': ')[1]);
	$('#destination').html(title.split('<br>')[2].replace('(','').replace(')',''));
	$('#time').html(title.split('<br>')[3]);
	$('#car').html(title.split('<br>')[4]);
	$('#passenger').html(passenger.replace(',','<br>').replace('-',' - '));
	$('#myModal').modal('show');
	$('#tag').val('').trigger('change');
	$('#judul_table').html('Duty Details');
	$('#tag').val('');
	$('#tag').focus();
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

function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

function scanAttendance(tag) {

	if ($('#tag').val() == '') {
		audio_error.play();
		openErrorGritter('Error!','Scan ID Card.');
		$('#tag').val('');
		$('#tag').focus();
		return false;
	}
	var data = {
		tag:tag,
		id:$("#driver_id").val(),
	}
	$.post('{{ url("scan/ga_control/driver_monitoring") }}', data, function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success', result.message);
			fetchTable();
			audio_ok.play();
			$('#myModal').modal('hide');
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

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
	return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
}

function getActualTime() {
	var d = new Date();
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return h + ":" + m + ":" + s;
}

$.date = function(dateObject) {
	var d = new Date(dateObject);
	var day = d.getDate();
	var month = d.getMonth() + 1;
	var year = d.getFullYear();
	if (day < 10) {
		day = "0" + day;
	}
	if (month < 10) {
		month = "0" + month;
	}
	var date = day + "/" + month + "/" + year;

	return date;
};
</script>
@endsection