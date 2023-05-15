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

.autocomplete {
  position: relative;
  display: inline-block;
}

input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}

input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}

.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
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
		<div class="col-xs-3" style="padding-bottom: 12px;padding-left: 0px;">
			<div class="row" style="background-color: white;height: 100%;margin-left: 15px;height: 45px;vertical-align: middle;">
				<center style="background-color: lightskyblue;font-weight: bold;">
					<span>SENDER</span>
				</center>
				<input type="text" name="operators" id="operators" placeholder="-" style="width: 100%;font-weight: bold;font-size: 17px;height: 45px;text-align: center;" readonly="">
			</div>
		</div>
		<div class="col-xs-3" style="padding-bottom: 12px;text-align:center;padding-right: 0px;">
			<center style="background-color: lightskyblue;font-weight: bold;">
				<span>STATUS</span>
			</center>
			<select class="form-control" id="process" name="process" data-placeholder="Pilih Process" style="width: 100%;height: 45px;text-align: center;font-size: 25px;" onchange="checkStatus()">
				<option value="IN">KIRIM KE MOLDING</option>
				<option value="OUT">KIRIM KE PRESS</option>
			</select>
		</div>
		<!-- <div class="col-xs-4" style="padding-bottom: 12px;text-align:center;">
			<center style="background-color: lightskyblue;font-weight: bold;">
				<span>PART</span>
			</center>
			<select class="form-control" id="part" name="part" data-placeholder="Pilih Process" style="width: 100%;height: 45px;text-align: center;font-size: 25px;">
				<option value="PUNCH">PUNCH</option>
				<option value="DIE">DIE</option>
				<option value="GUIDE PLATE">GUIDE PLATE</option>
			</select>
		</div> -->
		<div class="col-xs-3 autocomplete" style="padding-bottom: 12px;text-align:center;padding-right: 0px;">
			<center style="background-color: lightskyblue;font-weight: bold;">
				<span>PART NUMBER</span>
			</center>
			<input type="text" name="tag" id="tag" placeholder="Input Kanagata Number di Sini . . ." style="width: 100%;font-weight: bold;font-size: 20px;height: 45px;text-align: center;padding-left: 0px;">
		</div>
		<div class="col-xs-3" style="padding-bottom: 12px;text-align:center;">
			<center style="background-color: lightskyblue;font-weight: bold;">
				<span>RECEIVER</span>
			</center>
			<input type="text" name="receive" id="receive" placeholder="Scan ID Card Receiver di Sini" style="width: 100%;font-weight: bold;font-size: 17px;height: 45px;text-align: center;">
		</div>
		<div class="col-xs-12">
			<table class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th style="width: 1%">Material</th>
						<th style="width: 1%">Part</th>
						<th style="width: 2%">Part Number</th>
						<th style="width: 1%">Shot Periodik</th>
						<th style="width: 5%">Note</th>
						<th style="width: 1%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyTemp">
					
				</tbody>
			</table>
		</div>
		<!-- <div class="col-xs-4" style="padding-bottom: 12px;padding-right: 0px;">
			<center style="background-color: lightskyblue;font-weight: bold;">
				<span>NOTE</span>
			</center>
			<textarea id="note" style="width: 100%;height: 45px" placeholder="Input Note"></textarea>
		</div>
		<div class="col-xs-4" style="padding-bottom: 12px;text-align:center;">
			<center style="background-color: lightskyblue;font-weight: bold;">
				<span>RECEIVER</span>
			</center>
			<input type="text" name="receive" id="receive" placeholder="Scan ID Card Receiver di Sini" style="width: 100%;font-weight: bold;font-size: 17px;height: 45px;text-align: center;">
		</div> -->
		<div class="col-xs-12" style="padding-bottom: 12px;text-align:center;">
			<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 25px;" onclick="confirmAll()">CONFIRM</button>
		</div>
		<div class="col-xs-12" style="text-align: center;padding-top: 10px">
			<div class="box box-solid">
				<div class="box-body">
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
						<input type="hidden" id="operator_id">
						<input type="hidden" id="operator_name">
						<input type="hidden" id="receive_id">
						<input type="hidden" id="receive_name">
			            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
			            	<tr>
			            		<th style="width: 1%;text-align: center;" colspan="10">KANAGATA IN PROGRESS</th>
			            	</tr>
			                <tr>
			                  <th style="width: 1%;">#</th>
			                  <th style="width: 2%;text-align: left">ID</th>
			                  <th style="width: 3%;text-align: left">Material</th>
			                  <th style="width: 2%;text-align: left">Process</th>
			                  <th style="width: 2%;text-align: left">Last Counter</th>
							  <th style="width: 2%;text-align: left">Part</th>
							  <th style="width: 2%;text-align: left">Submitted By</th>
							  <th style="width: 2%;text-align: left">Received By</th>
							  <th style="width: 2%;text-align: left">Datetime</th>
							  <th style="width: 2%;text-align: left">Status</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
		            <!-- <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
		            	<button class="btn btn-danger" id="btn_cancel" onclick="cancelSeasoning()" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">CANCEL</button>
		            </div>
		            <div class="col-xs-6" style="padding-right: 0px;padding-left: 5px;">
		            	<button class="btn btn-success" id="btn_finish" onclick="startSeasoning('start')" style="width: 100%;font-weight: bold;font-size: 20px;margin-top: 20px;width: 100%">PROSES SEASONING</button>
		            </div> -->
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
							<label for="exampleInputEmail1">SENDER</label>
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
		cancelAll();
		$('#resultScanBody').html("");
		$('#tag').removeAttr('disabled');
		$('#tag').val("");
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		fillResult();
		setInterval(fillResult,200000);

      $('body').toggleClass("sidebar-collapse");
		
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_id").val("");
		$("#operator_name").val("");
		$("#operators").val("-");
		$("#note").val("");
		$("#receive").val("");
		$("#tag").val("");
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	function checkStatus() {
		if ($("#process").val() == 'IN') {
			if (all_kanagata.length > 0) {
				autocomplete(document.getElementById("tag"), all_kanagata);
			}
		}else{
			if (all_kanagata_trans.length > 0) {
				autocomplete(document.getElementById("tag"), all_kanagata_trans);
			}
		}
	}

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

	$('#receive').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#receive").val().length >= 8){
				var data = {
					employee_id : $("#receive").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#receive').val(result.employee.employee_id+' - '+result.employee.name);
						$('#receive_id').val(result.employee.employee_id);
						$('#receive_name').val(result.employee.name);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#receive').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#receive").val("");
			}		
		}
	});

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 12){
				if($("#tag").val() == ''){
					audio_error.play();
					openErrorGritter('Error!','Pilih Kanagata');
					$('#tag').val('');
					$('#tag').focus();
					return false;
				}
				var material_number = $('#tag').val().split(' - ')[0];
				var part = $('#tag').val().split(' - ')[1];
				var part_number = $('#tag').val().split(' - ')[2];
				var shot_periodik = $('#tag').val().split(' - ')[3];
				if($.inArray((material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-"), kanagata_temp) != -1){
					audio_error.play();
					openErrorGritter('Error!','Material sudah ada di list.');
					$('#tag').val('');
					$('#tag').focus();
					return false;
				}
				kanagata_temp.push((material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-"));
				var datas = '';
				datas += '<tr id="'+(material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-")+'">';
				datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="material_number_'+indexes+'">'+material_number+'</td>';
				datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="part_'+indexes+'">'+part+'</td>';
				datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="part_number_'+indexes+'">'+part_number+'</td>';
				datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="shot_'+indexes+'">'+shot_periodik+'</td>';
				datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;padding:0px;">';
				if ($('#process').val() == 'IN') {
					datas += '<select id="select_note_in_'+indexes+'" style="width: 100%;height: 45px;text-align:center;" data-placeholder="Pilih Note" onclick="selectNote(\''+indexes+'\',this.value)">';
					datas += '<option value="-">Pilih Note</option>';
					datas += '<option value="Aus">Aus</option>';
					datas += '<option value="Periodik Lifetime">Periodik Lifetime</option>';
					datas += '<option value="Nami">Nami</option>';
					datas += '<option value="Kizu">Kizu</option>';
					datas += '<option value="Kake">Kake</option>';
					datas += '</select>';
					datas += '<input id="note_'+indexes+'" style="width: 100%;height: 45px;display:none" placeholder="Input Note Perbaikan">';
				}
				if ($('#process').val() == "OUT") {
					datas += '<select id="select_note_out_'+indexes+'" style="width: 100%;height: 45px;text-align:center;" data-placeholder="Pilih Note" onclick="selectNote(\''+indexes+'\',this.value)">';
					datas += '<option value="-">Pilih Note</option>';
					datas += '<option value="Sudah Periodik">Sudah Periodik</option>';
					datas += '<option value="Sudah Perbaikan">Sudah Perbaikan</option>';
					datas += '</select>';
					datas += '<input id="note_'+indexes+'" style="width: 100%;height: 45px;display:none" placeholder="Input Note Perbaikan">';
				}
				datas += '</td>';
				datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;padding:0px;"><button class="btn btn-danger btn-sm" onclick="deleteKanagata(\''+(material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-")+'\')"><i class="fa fa-trash"></i></button></td>';
				datas += '</tr>';

				$('#bodyTemp').append(datas);

				$('#tag').val('');
				indexes++;
			}
		}
	});

	function deleteKanagata(id) {
		kanagata_temp.splice( $.inArray(id), 1 );
		$('#'+id).remove();
	}

	var indexes = 0;

	function confirmAll() {
		$('#loading').show();
		if (indexes == 0) {
			$('#tag').val('');
			$('#tag').focus();
			$('#loading').hide();
			openErrorGritter('Error!','Masukkan Kanagata');
			return false;
		}
		var salah = 0;
		if ($('#process').val() == 'IN') {
			for(var i = 0; i < indexes;i++){
				if ($('#material_number_'+i).text() != '') {
					if ($('#select_note_in_'+i).val() == '-') {
						salah++;
					}
					if ($('#note_'+i).val() == '') {
						salah++;
					}
				}
			}
		}else{
			for(var i = 0; i < indexes;i++){
				if ($('#material_number_'+i).text() != '') {
					if ($('#select_note_out_'+i).val() == '-') {
						salah++;
					}
					if ($('#note_'+i).val() == '') {
						salah++;
					}
				}
			}
		}
		if (salah > 0 || 
			$('#receive').val() == '' ||
			$('#receive_id').val() == '' ||
			$('#receive_name').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Harus Diisi Semua');
			return false;
		}
		var kanagatas = [];
		for(var i = 0; i < indexes;i++){
			if ($('#material_number_'+i).text() != '') {
				var notess = '';
				if ($('#process').val() == 'IN') {
					notess = $('#select_note_in_'+i).val();
				}else{
					notess = $('#select_note_out_'+i).val();
				}
				kanagatas.push({
					material_number:$('#material_number_'+i).text(),
					part:$('#part_'+i).text(),
					part_number:$('#part_number_'+i).text(),
					shot:$('#shot_'+i).text(),
					note:$('#note_'+i).val(),
					notes:notess,
				});
			}
		}
		var data = {
			process:$('#process').val(),
			submit_id:$('#operator_id').val(),
			submit_name:$('#operator_name').val(),
			receive_id:$('#receive_id').val(),
			receive_name:$('#receive_name').val(),
			kanagatas:kanagatas
		}
		
		$.post('{{ url("input/press/transaction") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#tag').removeAttr('disabled');
				$('#tag').val('');
				$('#tag').focus();
				$('#loading').hide();
				fillResult();
				cancelAll();
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

	function cancelAll() {
		all_kanagata = [];
		all_kanagata_trans = [];
		indexes = 0;
		kanagata_temp = [];
		$('#bodyTemp').html('');
		$('#receive').val('');
		$('#receive_id').val('');
		$('#receive_name').val('');
		$('#tag').val('');
		$('#note').val('');
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	var all_kanagata = [];
	var all_kanagata_trans = [];
	var kanagata_temp = [];

	function fillResult() {
		var data = {
		}
		$.get('{{ url("fetch/press/transaction") }}',data, function(result, status, xhr){
			if(result.status){
				$('#resultScanBody').html('');
				var bodys = '';
				var index = 1;
				for(var i = 0; i < result.transaction.length;i++){
					bodys += '<tr onclick="changeKanagata(\''+result.transaction[i].material_number+'\',\''+result.transaction[i].part+'\',\''+result.transaction[i].part_number+'\',\''+result.transaction[i].last_counter+'\')" style="cursor:pointer">';
					bodys += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+index+'</td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;"><b class="text-red">'+result.transaction[i].transaction_id+'</b></td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;"><b class="text-red">'+result.transaction[i].material_number+'</b> - '+result.transaction[i].material_name+'</td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+result.transaction[i].process+'</td>';
					bodys += '<td style="text-align:right;padding-right:5px;border:1px solid black;">'+result.transaction[i].last_counter+'</td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+result.transaction[i].part+'<br><b class="text-red">'+result.transaction[i].part_number+'</b></td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+result.transaction[i].submit_name+'</td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+result.transaction[i].submit_maintenance_name+'</td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+result.transaction[i].submit_datetime+'</td>';
					bodys += '<td style="text-align:left;padding-left:5px;border:1px solid black;">'+(result.transaction[i].remark || 'Proses Maintenance')+'</td>';
					bodys += '</tr>';
					index++;
				}
				$('#resultScanBody').append(bodys);
				all_kanagata = [];
				all_kanagata_trans = [];

				for(var i = 0; i < result.all_kanagata.length;i++){
					all_kanagata.push(result.all_kanagata[i].material_number+' - '+result.all_kanagata[i].part+' - '+result.all_kanagata[i].punch_die_number+' - '+result.all_kanagata[i].qty_check);
				}

				for(var i = 0; i < result.transaction.length;i++){
					all_kanagata_trans.push(result.transaction[i].material_number+' - '+result.transaction[i].part+' - '+result.transaction[i].part_number+' - '+result.transaction[i].last_counter);
				}

				checkStatus();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function changeKanagata(material_number,part,part_number,shot_periodik) {
		if ($('#process').val() == "OUT") {
			if($.inArray((material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-"), kanagata_temp) != -1){
				audio_error.play();
				openErrorGritter('Error!','Material sudah ada di list.');
				$('#tag').val('');
				$('#tag').focus();
				return false;
			}
			kanagata_temp.push((material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-"));
			var datas = '';
			datas += '<tr id="'+(material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-")+'">';
			datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="material_number_'+indexes+'">'+material_number+'</td>';
			datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="part_'+indexes+'">'+part+'</td>';
			datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="part_number_'+indexes+'">'+part_number+'</td>';
			datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;" id="shot_'+indexes+'">'+shot_periodik+'</td>';
			datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;padding:0px;">';
			// datas += '<select id="select_note_in_'+indexes+'" style="width: 100%;height: 45px" data-placeholder="Pilih Note">';
			// datas += '<option value="Aus">Aus</option>';
			// datas += '<option value="Periodik Lifetime">Periodik Lifetime</option>';
			// datas += '<option value="Nami">Nami</option>';
			// datas += '<option value="Kizu">Kizu</option>';
			// datas += '<option value="Kake">Kake</option>';
			// datas += '</select>';
			datas += '<select id="select_note_out_'+indexes+'" style="width: 100%;height: 45px;text-align:center;" data-placeholder="Pilih Note" onclick="selectNote(\''+indexes+'\',this.value)">';
			datas += '<option value="-">Pilih Note</option>';
			datas += '<option value="Sudah Periodik">Sudah Periodik</option>';
			datas += '<option value="Sudah Perbaikan">Sudah Perbaikan</option>';
			datas += '</select>';
			datas += '<input id="note_'+indexes+'" style="width: 100%;height: 45px;display:none" placeholder="Input Note Perbaikan">';
			datas += '</td>';
			datas += '<td style="background-color:white;font-size:16px;vertical-align:middle;border:1px solid black;padding:0px;"><button class="btn btn-danger btn-sm" onclick="deleteKanagata(\''+(material_number+'_'+part+'_'+part_number+'_'+shot_periodik).replace(/ /g,"-")+'\')"><i class="fa fa-trash"></i></button></td>';
			datas += '</tr>';

			$('#bodyTemp').append(datas);

			$('#tag').val('');
			indexes++;
		}
	}

	function selectNote(param,values) {
		$('#note_'+param).val('testsss');
		if ($('#process').val() == 'IN') {
			$('#note_'+param).val(values);
		}else{
			$('#note_'+param).hide();
			if (values == 'Sudah Periodik' || values == '-') {
				$('#note_'+param).val(values);
			}else{
				$('#note_'+param).val('');
				$('#note_'+param).show();
			}
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

	function autocomplete(inp, arr) {
		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
		      var a, b, i, val = this.value;
		      /*close any already open lists of autocompleted values*/
		      closeAllLists();
		      if (!val) { return false;}
		      currentFocus = -1;
		      /*create a DIV element that will contain the items (values):*/
		      a = document.createElement("DIV");
		      a.setAttribute("id", this.id + "autocomplete-list");
		      a.setAttribute("class", "autocomplete-items");
		      /*append the DIV element as a child of the autocomplete container:*/
		      this.parentNode.appendChild(a);
		      /*for each item in the array...*/
		      for (i = 0; i < arr.length; i++) {
		        /*check if the item starts with the same letters as the text field value:*/
		        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
		          /*create a DIV element for each matching element:*/
		          b = document.createElement("DIV");
		          /*make the matching letters bold:*/
		          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
		          b.innerHTML += arr[i].substr(val.length);
		          /*insert a input field that will hold the current array item's value:*/
		          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
		          /*execute a function when someone clicks on the item value (DIV element):*/
		          b.addEventListener("click", function(e) {
		              /*insert the value for the autocomplete text field:*/
		              inp.value = this.getElementsByTagName("input")[0].value;
		              $("#tag").focus();
		              /*close the list of autocompleted values,
		              (or any other open lists of autocompleted values:*/
		              closeAllLists();
		          });
		          a.appendChild(b);
		        }
		      }
		  });
		  /*execute a function presses a key on the keyboard:*/
		  inp.addEventListener("keydown", function(e) {
		      var x = document.getElementById(this.id + "autocomplete-list");
		      if (x) x = x.getElementsByTagName("div");
		      if (e.keyCode == 40) {
		        /*If the arrow DOWN key is pressed,
		        increase the currentFocus variable:*/
		        currentFocus++;
		        /*and and make the current item more visible:*/
		        addActive(x);
		      } else if (e.keyCode == 38) { //up
		        /*If the arrow UP key is pressed,
		        decrease the currentFocus variable:*/
		        currentFocus--;
		        /*and and make the current item more visible:*/
		        addActive(x);
		      } else if (e.keyCode == 13) {
		        /*If the ENTER key is pressed, prevent the form from being submitted,*/
		        e.preventDefault();
		        if (currentFocus > -1) {
		          /*and simulate a click on the "active" item:*/
		          if (x) x[currentFocus].click();
		        }
		      }
		  });
		  function addActive(x) {
		    /*a function to classify an item as "active":*/
		    if (!x) return false;
		    /*start by removing the "active" class on all items:*/
		    removeActive(x);
		    if (currentFocus >= x.length) currentFocus = 0;
		    if (currentFocus < 0) currentFocus = (x.length - 1);
		    /*add class "autocomplete-active":*/
		    x[currentFocus].classList.add("autocomplete-active");
		  }
		  function removeActive(x) {
		    /*a function to remove the "active" class from all autocomplete items:*/
		    for (var i = 0; i < x.length; i++) {
		      x[i].classList.remove("autocomplete-active");
		    }
		  }
		  function closeAllLists(elmnt) {
		    /*close all autocomplete lists in the document,
		    except the one passed as an argument:*/
		    var x = document.getElementsByClassName("autocomplete-items");
		    for (var i = 0; i < x.length; i++) {
		      if (elmnt != x[i] && elmnt != inp) {
		        x[i].parentNode.removeChild(x[i]);
		      }
		    }
		  }
		  /*execute a function when someone clicks in the document:*/
		  document.addEventListener("click", function (e) {
		      closeAllLists(e.target);
		  });
		}
</script>
@endsection