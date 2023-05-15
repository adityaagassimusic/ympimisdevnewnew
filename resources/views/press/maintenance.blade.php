@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
<style type="text/css">
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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#moldingMaster > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#molding {
		height:410px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	<input type="hidden" id="maintenance_code" value="{{ $title }} {{$title_jp}} }">
	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 10px; padding-left: 0">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">PIC Molding <span style="color: red" id="counter"></span></th>
							
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size:16px; width: 1%;" id="op_0">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;width: 1%" id="op_1">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;width: 1%" id="op_2">-</td>
						</tr>
						<!-- <tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 15px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 15px;"><b>Molding List</b></td>
						</tr> -->
					</tbody>
				</table>
			</div>
			<table style="width: 100%;border: '1'">
				<tbody>
						<tr>
							<td colspan="4" style="width: 1%; font-weight: bold; font-size: 15px; background-color: rgb(220,220,220);display: none">Status</td>
						</tr>
						<tr>
							<td colspan="4" id="status" style="border:1px solid black;width: 4%; font-size: 2vw; font-weight: bold; background-color: rgb(50, 50, 50); color: yellow;display: none">-</td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);border: 1px solid black;">Transaction ID</td>
							<td id="transaction_id_new" style="border:1px solid black;width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);border: 1px solid black;">Part</td>
							<td id="part" style="border:1px solid black; width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);border: 1px solid black;">Part Number</td>
							<td id="part_number" style="border:1px solid black; width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);border: 1px solid black;">Last Counter</td>
							<td id="last_counter" style="border:1px solid black; width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;">-</td>
						</tr>
						<tr id="perbaikantime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: black; width: 23%;background-color: rgb(204,255,255);"><div class="timerperbaikan">
					            <span class="hourperbaikan" id="hourperbaikan">00</span> h : <span class="minuteperbaikan" id="minuteperbaikan">00</span> m : <span class="secondperbaikan" id="secondperbaikan">00</span> s
					            <input type="hidden" id="perbaikan" class="timepicker" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_perbaikan" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
				</tbody>
			</table>
			<button id="start_perbaikan" style="width: 100%; margin-top: 5px; font-size: 30px; padding-top:5px;padding-bottom: 5px; font-weight: bold; border-color: black; color: white; width: 100%" onclick="startPerbaikan()" class="btn btn-success">MULAI PERBAIKAN</button>
				<!-- <button id="pause" style="width: 100%; margin-top: 10px; font-size: 30px; padding-top: 5px;padding-bottom: 5px; font-weight: bold; border-color: black; color: white; width: 100%" onclick="pause()" class="btn btn-warning">PAUSE</button> -->
				<button id="change_operator" style="width: 100%; margin-top: 10px; font-size: 30px; padding-top: 5px;padding-bottom: 5px; font-weight: bold; border-color: black; color: white; width: 100%" onclick="location.reload()" class="btn btn-info">GANTI OPERATOR</button>
		</div>

		<div class="col-xs-6" style="padding-right: 0; padding-left: 10px">
			<table style="width: 100%;" border="1">
				<tbody>
					<tr>
						<td style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
							<span>Note</span>
				    	</td>
					</tr>
					<tr>
						<td style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
							<textarea name="noteperbaikan" id="noteperbaikan" style="width:100%;height:230px;font-size: 1.2vw;text-align: center;vertical-align: middle;" placeholder="TULISKAN CATATAN DI SINI"></textarea>
						</td>
					</tr>
				</tbody>
			</table>

			<div style="padding-top: 5px;">
				<button id="finish_perbaikan" style="width: 100%; margin-top: 5px; font-size: 30px;padding-top: 5px padding-bottom: 5px;font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishPerbaikan()" class="btn btn-danger">SELESAI PERBAIKAN</button>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID 1</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_0" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_0" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 2</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_1" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_1" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 3</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_2" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_2" placeholder="Scan ID Card">

					</div>
					<div class="col-xs-12">
						<div class="row">
							<button id="btn_operator" onclick="saveOperator()" class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMolding" style="overflow-y: auto;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12" id="molding_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12" style="background-color: lightskyblue">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Molding</span></center>
							</div>
							<div class="col-xs-12" id="molding_btn">
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="molding_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12" style="background-color: lightskyblue">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Molding</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button id="molding_fix2" class="btn btn-danger" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMolding2()">
									MOLDING
								</button>
							</div>
						</div>
					</div>
					<input type="hidden" id="transaction_id">
					<input type="hidden" id="status_temp">
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer row">
							<button onclick="saveMolding()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center> <b style="font-size: 2vw">PAUSE</b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<center><label for="">Reason</label></center>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="reasonPause" placeholder="Reason" required><br>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<button class="btn btn-danger btn-block" style="font-weight: bold;font-size: 20px" data-dismiss="modal">Cancel</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="saveStatus()">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/jqbtk.js") }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	var intervalTime;

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

	    $('#noteperbaikan').keyboard();
	    cancelAll();
	});

	function cancelAll() {
		temp = null;
		$('#transaction_id').val('');
		$('#status_temp').val('');
		$('#operator_0').val('');
		$('#operator_1').val('');
		$('#operator_2').val('');
		$('#perbaikannote').hide();
		$('#perbaikannote2').hide();
		$("#start_perbaikan").show();
		$("#finish_perbaikan").hide();
		$('#molding_fix').hide();
	}

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator_0').focus();
	});

	$('#operator_0').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_0").val().length >= 8){
				var data = {
					employee_id : $("#operator_0").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_0').val(result.employee.name);
						$('#op_0').html(result.employee.employee_id+' - '+result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_0').val(result.employee.employee_id);
						$('#operator_0').prop('disabled',true);
						$('#operator_1').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_0').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_0").val("");
			}			
		}
	});

	$('#operator_1').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_1").val().length >= 8){
				var data = {
					employee_id : $("#operator_1").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_1').val(result.employee.name);
						$('#op_1').html(result.employee.employee_id+' - '+result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_1').val(result.employee.employee_id);
						$('#operator_1').prop('disabled',true);
						$('#operator_2').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_1').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_1").val("");
			}			
		}
	});

	$('#operator_2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_2").val().length >= 8){
				var data = {
					employee_id : $("#operator_2").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_2').val(result.employee.name);
						$('#op_2').html(result.employee.employee_id+' - '+result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_2').val(result.employee.employee_id);
						$('#operator_2').prop('disabled',true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_2').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_2").val("");
			}			
		}
	});

	var temp = null;

	function getMoldingMaster() {
		$('#loading').show();
		$.get('{{ url("fetch/press/maintenance") }}',  function(result, status, xhr){
			if(result.status){

				$('#molding_fix').hide();

				var moldingMaster = '';
				$('#molding_btn').html("");
				var no = 1;
				var color ="";
				// $('#modalMolding').modal('show');
				$('#modalMolding').modal({
					backdrop: 'static',
					keyboard: false
				});
				temp = result.temp;
				$.each(result.maintenance, function(key, value) {
					var statuses = '';
					for(var i = 0; i < result.temp.length;i++){
						if (result.temp[i].transaction_id == value.transaction_id) {
							statuses = 'Continue';
						}
					}
					if (statuses == '') {
						moldingMaster += '<div class="col-xs-3" style="padding-top: 5px">';
						moldingMaster += '<center>';
						moldingMaster += '<button class="btn btn-danger" id="'+value.transaction_id+'" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMolding(this.id,\''+value.last_counter+'\',\''+value.part+'\',\''+value.part_number+'\',\''+statuses+'\')">'+value.transaction_id+' - '+value.part+'<br>'+value.part_number+' <br>Shot : '+value.last_counter+'<br>MULAI PERIODIK</button>';
						moldingMaster += '</center>';
						moldingMaster += '</div>';
					}else{
						moldingMaster += '<div class="col-xs-3" style="padding-top: 5px">';
						moldingMaster += '<center>';
						moldingMaster += '<button class="btn btn-warning" id="'+value.transaction_id+'" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMolding(this.id,\''+value.last_counter+'\',\''+value.part+'\',\''+value.part_number+'\',\''+statuses+'\')">'+value.transaction_id+' - '+value.part+'<br>'+value.part_number+' <br>Shot : '+value.last_counter+'<br>SEDANG PERIODIK</button>';
						moldingMaster += '</center>';
						moldingMaster += '</div>';
					}
				});
				$('#molding_btn').append(moldingMaster);
				openSuccessGritter('Success!', 'Success Get Data');
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function saveOperator() {
		$('#modalOperator').modal('hide');
		getMoldingMaster();
	}

	function saveMolding() {
		$('#loading').show();
		if ($('#transaction_id').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error','Pilih Molding');
			return false;
		}
		$('#loading').hide();

		$('#transaction_id_new').html($('#molding_fix2').text().split(' - ')[0]);
		$('#part').html($('#molding_fix2').text().split(' - ')[1]);
		$('#part_number').html($('#molding_fix2').text().split(' - ')[2]);
		$('#last_counter').html($('#molding_fix2').text().split(' - ')[3]);

		$('#modalMolding').modal('hide');

		if ($('#status_temp').val() != '') {
			for(var i = 0; i < temp.length;i++){
				if (temp[i].transaction_id == $('#transaction_id').val()) {
					$('#start_time_perbaikan').val(temp[i].start_time);
					startPerbaikan();
					var employee_id = temp[i].employee_id.split(',');
					var name = temp[i].name.split(',');
					var employee_id_0 = '-';
					var employee_id_1 = '-';
					var employee_id_2 = '-';
					if (employee_id.length == 1) {
						employee_id_0 = employee_id[0]+' - '+name[0];
					}
					if (employee_id.length == 2) {
						employee_id_0 = employee_id[0]+' - '+name[0];
						employee_id_1 = employee_id[1]+' - '+name[1];
					}
					if (employee_id.length == 3) {
						employee_id_0 = employee_id[0]+' - '+name[0];
						employee_id_1 = employee_id[1]+' - '+name[1];
						employee_id_2 = employee_id[2]+' - '+name[2];
					}
					$('#op_0').html(employee_id_0);
					$('#op_1').html(employee_id_1);
					$('#op_2').html(employee_id_2);
				}
			}
		}
	}

	function getMolding(transaction_id,last_counter,part,part_number,statuses) {
		$('#molding_fix').show();
		$('#molding_choice').hide();
		$('#molding_fix2').html(transaction_id+' - '+part+' - '+part_number+' - '+last_counter);
		$('#transaction_id').val(transaction_id);
		$('#status_temp').val(statuses);
		if (statuses == '') {
			$('#molding_fix2').prop('class','btn btn-danger');
		}else{
			$('#molding_fix2').prop('class','btn btn-warning');
		}
	}

	function changeMolding2() {
		$('#molding_fix').hide();
		$('#molding_choice').show();
		$('#molding_fix2').html("MOLDING");
		$('#transaction_id').val('');
		$('#status_temp').val('');
	}

	function startPerbaikan() {
		if ($('#status_temp').val() == '') {
			$('#start_time_perbaikan').val(getActualFullDate());
			inputTemp();
		}
		var tanggal_fix = $('#start_time_perbaikan').val().replace(/-/g,'/');
		started_at = new Date(tanggal_fix);
		countUpFromTime(started_at);

		$("#start_perbaikan").hide();
		$("#finish_perbaikan").show();
	}

	function inputTemp() {
		var employee_id = [];
		var name = [];
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		if (pic_1 != "-") {
			employee_id.push(pic_1.split(' - ')[0]);
			name.push(pic_1.split(' - ')[1]);
		}
		if (pic_2 != "-") {
			employee_id.push(pic_2.split(' - ')[0]);
			name.push(pic_2.split(' - ')[1]);
		}
		if (pic_3 != "-") {
			employee_id.push(pic_3.split(' - ')[0]);
			name.push(pic_3.split(' - ')[1]);
		}

		var data = {
			transaction_id:$('#transaction_id').val(),
			start_time:$('#start_time_perbaikan').val(),
			employee_id:employee_id.join(','),
			name:name.join(','),
		}
		$('#loading').show();
		$.post('{{ url("input/press/maintenance/temp") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success','Success Input Temp');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function finishPerbaikan() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				transaction_id:$('#transaction_id').val(),
				note:$('#noteperbaikan').val(),
			}

			$.post('{{ url("input/press/maintenance") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					cancelAll();
					alert('Selesai Melakukan Perbaikan');
					location.reload();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function countUpFromTime(countFrom) {
	  countFrom = new Date(countFrom).getTime();
	  var now = new Date(),
	      countFrom = new Date(countFrom),
	      timeDifference = (now - countFrom);
	    
	  var secondsInADay = 60 * 60 * 1000 * 24,
	      secondsInAHour = 60 * 60 * 1000;
	    
	  days = Math.floor(timeDifference / (secondsInADay) * 1);
	  years = Math.floor(days / 365);
	  if (years > 1){
	  	days = days - (years * 365) 
	  }
	  hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
	  mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
	  secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

	  $('div.timerperbaikan span.secondperbaikan').html(addZero(secs));
	  $('div.timerperbaikan span.minuteperbaikan').html(addZero(mins));
	  $('div.timerperbaikan span.hourperbaikan').html(addZero(hours));

	  clearTimeout(intervalTime);
	  intervalTime = setTimeout(function(){ countUpFromTime(countFrom); }, 1000);
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
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
</script>
@endsection


