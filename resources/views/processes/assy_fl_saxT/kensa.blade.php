@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	#ngList {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }

</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>

	<input type="hidden" id="loc" value="{{ $loc }}">
	<input type="hidden" id="started_at">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag" name="tag" placeholder="Scan ID Slip..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<thead>
						<tr>
							<td colspan="2" style="text-align: center; width: 2%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">OP Kensa</td>
							<td colspan="4" id="op_kensa" style="text-align: center; width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 2%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 2%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Key</td>
							<td id="key" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 2%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Surface</td>
							<td id="surface" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							<input type="hidden" id="material_tag">
							<input type="hidden" id="material_number">
							<input type="hidden" id="material_quantity">
							<input type="hidden" id="employee_id">
							<input type="hidden" id="operator_id">
						</tr>
						
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator I.C.</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="op">-</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="op2">-</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-10 col-xs-offset-1" style="padding-top: 30px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="4" style="background-color: #ffff66; text-align: center; color: black; font-weight: bold; font-size:2vw;">LACQUERING</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">#</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Check</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">NG</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody id = "alto-body">
						<tr>
							<td style="background-color: #323232; text-align: center; color: #ffff07; font-size: 2vw;">ALTO</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="lcq_as_result">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="lcq_as_ng">0</td>
							<td style="background-color: #9dff69; text-align: center; color: #000000; font-size: 2vw;" id="lcq_as_rate">0%</td>
						</tr>
						<tr>
							<td style="background-color: #323232; text-align: center; color: #ffff07; font-size: 2vw;">TENOR</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="lcq_ts_result">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="lcq_ts_ng">0</td>
							<td style="background-color: #9dff69; text-align: center; color: #000000; font-size: 2vw;" id="lcq_ts_rate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-10 col-xs-offset-1" style="padding-top: 15px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="4" style="background-color: #f2f2f2; text-align: center; color: black; font-weight: bold; font-size:2vw;">PLATING</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">#</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Check</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">NG</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: #323232; text-align: center; color: #ffff07; font-size: 2vw;">ALTO</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="plt_as_result">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="plt_as_ng">0</td>
							<td style="background-color: #9dff69; text-align: center; color: #000000; font-size: 2vw;" id="plt_as_rate">0%</td>
						</tr>
						<tr>
							<td style="background-color: #323232; text-align: center; color: #ffff07; font-size: 2vw;">TENOR</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="plt_ts_result">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="plt_ts_ng">0</td>
							<td style="background-color: #9dff69; text-align: center; color: #000000; font-size: 2vw;" id="plt_ts_rate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-xs-6" style="padding-right: 0;">
			<div id="ngList">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Count</th>
						</tr>
					</thead>
					<tbody id="ng_lists">
					</tbody>
				</table>
			</div>
			<div>
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="canc()" class="btn btn-danger">CANCEL</button>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="conf()" class="btn btn-success">CONFIRM</button>
				</center>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#tag').val('');
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
				
				$.get('{{ url("scan/middle/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						fillResult(result.employee.employee_id);
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

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 11){
				scanTag($("#tag").val());
			}
			else{
				openErrorGritter('Error!', 'ID Slip Invalid');
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}			
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function fillResult(emp_id){
		var data = {
			location: $('#loc').val(),
			employee_id : emp_id,
		}
		$.get('{{ url("fetch/middle/kensa") }}', data, function(result, status, xhr){
			var lcq_as_qty = 0;
			var lcq_ts_qty = 0;
			var plt_as_qty = 0;
			var plt_ts_qty = 0;

			var lcq_as_ng = 0;
			var lcq_ts_ng = 0;
			var plt_as_ng = 0;
			var plt_ts_ng = 0;


			$.each(result.result, function(index, value) {
				if (value.surface.includes('LCQ')) {
					if (value.hpl == 'ASKEY') {
						lcq_as_qty += value.quantity;
					} else if (value.hpl == 'TSKEY'){
						lcq_ts_qty += value.quantity;
					}
				} else if (value.surface.includes('PLT')) {
					if (value.hpl == 'ASKEY') {
						plt_as_qty += value.quantity;
					} else if (value.hpl == 'TSKEY'){
						plt_ts_qty += value.quantity;
					}
				}
			});

			$('#lcq_as_result').text(lcq_as_qty);
			$('#lcq_ts_result').text(lcq_ts_qty);
			$('#plt_as_result').text(plt_as_qty);
			$('#plt_ts_result').text(plt_ts_qty);

			$.each(result.ng, function(index, value) {
				if (value.surface.includes('LCQ')) {
					if (value.hpl == 'ASKEY') {
						lcq_as_ng += value.quantity;
					} else if (value.hpl == 'TSKEY'){
						lcq_ts_ng += value.quantity;
					}
				} else if (value.surface.includes('PLT')) {
					if (value.hpl == 'ASKEY') {
						plt_as_ng += value.quantity;
					} else if (value.hpl == 'TSKEY'){
						plt_ts_ng += value.quantity;
					}
				}
			});

			$('#lcq_as_ng').text(lcq_as_ng);
			$('#lcq_ts_ng').text(lcq_ts_ng);
			$('#plt_as_ng').text(plt_as_ng);
			$('#plt_ts_ng').text(plt_ts_ng);


			if (!isNaN((lcq_as_ng/lcq_as_qty))) $('#lcq_as_rate').text(Math.round((lcq_as_ng/lcq_as_qty)*100, 2)+'%');
			if (!isNaN((lcq_ts_ng/lcq_ts_qty))) $('#lcq_ts_rate').text(Math.round((lcq_ts_ng/lcq_ts_qty)*100, 2)+'%');
			if (!isNaN((plt_as_ng/plt_as_qty))) $('#plt_as_rate').text(Math.round((plt_as_ng/plt_as_qty)*100, 2)+'%');
			if (!isNaN((plt_ts_ng/plt_ts_qty))) $('#plt_ts_rate').text(Math.round((plt_ts_ng/plt_ts_qty)*100, 2)+'%');

		});
	}

	function disabledButton() {
		if($('#tag').val() != ""){
			var btn = document.getElementById('conf1');
			btn.disabled = true;
			btn.innerText = 'Posting...'	
			return false;
		}
	}

	function conf(){
		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");
			$("#tag").focus();

			return false;
		}

		var tag = $('#tag_material').val();
		var loop = $('#loop').val();
		// var total = 0;
		var count_ng = 0;
		var ng = [];
		var count_text = [];
		for (var i = 1; i <= loop; i++) {
			if($('#count'+i).text() > 0){
				ng.push([$('#ng'+i).text(), $('#count'+i).text()]);
				count_text.push('#count'+i);
				// total += parseInt($('#count'+i).text());
				count_ng += 1;
			}
		}

		var data = {
			loc: $('#loc').val(),
			tag: $('#material_tag').val(),
			material_number: $('#material_number').val(),
			quantity: $('#material_quantity').val(),
			employee_id: $('#employee_id').val(),
			started_at: $('#started_at').val(),
			operator_id: $('#operator_id').val(),
			ng: ng,
			count_text: count_text,
			// total_ng: total,
		}
		disabledButton();

		$.post('{{ url("input/process_assembly_kensa/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text(0);
				}
				$('#op_kensa').text("");
				$('#operator_id').val("");
				$('#model').text("");
				$('#key').text("");
				$('#surface').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				fillResult($('#employee_id').val());
				$('#tag').focus();				
			}
			else{
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				audio_error.play();
				openErrorGritter('Error!', result.message);
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function canc(){
		var loop = $('#loop').val();
		for (var i = 1; i <= loop; i++) {
			$('#count'+i).text(0);
		};
		$('#op_kensa').text("");
		$('#model').text("");
		$('#key').text("");
		$('#surface').text("");
		$('#material_tag').val("");
		$('#material_number').val("");
		$('#material_quantity').val("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();
		$('#ng_lists').html('');


	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != ""){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != ""){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
			$('#tag').blur();
		}
	}

	function scanTag(tag){
		$('#tag').prop('disabled', true);
		var data = {
			tag:tag,
			loc:$('#loc').val()
		}
		$("#loading").show();

		$.get('{{ url("scan/process_assembly_kensa/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#model').text(result.middle_inventory.model);
				$('#key').text(result.middle_inventory.key);
				$('#surface').text(result.middle_inventory.surface);
				$('#material_tag').val(result.middle_inventory.tag);
				$('#material_number').val(result.middle_inventory.material_number);
				$('#material_quantity').val(result.middle_inventory.quantity);
				$('#started_at').val(result.started_at);
				$('#operator_id').val(result.middle_inventory.employee_id);
				$('#op_kensa').text(result.middle_inventory.name);
				$('#ng_lists').html('');

				var body = '';
				var loop = 0;
				for (var i = 0; i < result.ng_lists.length; i++) {
					loop++;

					var color = '';
					if (loop % 2 == 0 ) {
						color = 'style="background-color: #fffcb7"';
					} else {
						color = 'style="background-color: #ffd8b7"';
					}

					body += '<tr '+color+'>';
					body += '<td id="minus" onclick="minus('+ loop +')" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>';
					body += '<td id="ng'+ loop +'" style="font-size: 20px;">' + result.ng_lists[i].ng_name + '</td>';
					body += '<td id="plus" onclick="plus('+ loop +')" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>'
					body += '<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count'+ loop +'">0</span></td>'
					body += '</tr>';
				}
				body += '<input type="hidden" id="loop" value="'+result.ng_lists.length+'">'

				$('#ng_lists').append(body);
				$("#loading").hide();

			}
			else{
				$('#tag').prop('disabled', false);
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
				$("#loading").hide();
				
			}
		});
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