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
					<tbody>
						<tr>

							@if( (str_contains($loc, 'incoming') || str_contains($loc, 'kensa')) && !str_contains($loc, 'acc'))
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Key</td>
							<td id="key" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Quantity</td>
							<td id="qty_lot" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							@else
							<td style="width: 10%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Desc.</td>
							<td id="description" style="width: 65%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 10%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Qty</td>
							<td id="qty_lot" style="width: 15%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							@endif
							
							<input type="hidden" id="material_tag">
							<input type="hidden" id="material_number">
							<input type="hidden" id="material_quantity">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator</th>
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


			@if((str_contains($loc, 'incoming') || str_contains($loc, 'kensa')) && !str_contains($loc, 'acc') && !str_contains($loc, 'cl') && !str_contains($loc, 'fl'))
			<div style="padding-top: 15px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="3" style="background-color: #ffff66; text-align: center; color: black; font-weight: bold; font-size:2vw;">ALTO</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Result</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Not Good</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="ASresult">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="ASnotGood">0</td>
							<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;" id="ASngRate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="3" style="background-color: rgb(157, 255, 105); text-align: center; color: black; font-weight: bold; font-size:2vw;">TENOR</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Result</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Not Good</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="TSresult">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="TSnotGood">0</td>
							<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;" id="TSngRate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>
			@else
			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="3" style="background-color: rgb(120, 146, 240); text-align: center; color: black; font-weight: bold; font-size:2vw;">RESUME</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Result</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Not Good</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="CLresult">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="CLnotGood">0</td>
							<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;" id="CLngRate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>
			@endif

			@if($loc == 'plt-kensa-acc')
			<button style="width: 100%;font-weight: bold;font-size: 30px;" class="btn btn-danger" onclick="openModalCuciEnthol($('#op').text(),'selesai')">
				FINISH CUCI ENTHOLE
			</button>
			@endif

			

			
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
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loop" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minus" onclick="minus({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>
							<td id="ng{{$nomor+1}}" style="font-size: 20px;">{{ $ng_list->ng_name }} </td>
							<td id="plus" onclick="plus({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>
			<div>
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="canc()" class="btn btn-danger">CANCEL</button>
					<button id="rework" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="rework()" class="btn btn-warning">REWORK</button>
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

<div class="modal fade" id="modalCuciEnthol"  data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body">
					<div style="padding-top: 5px;">
						<input type="hidden" id="enthol_type">
						<div class="col-xs-10" style="background-color: green;padding: 10px;text-align: center;font-size: 20px;">
							<span style="color: white;text-align: center;font-weight: bold;" id="title">CUCI ENTHOLE</span>
						</div>
						<div class="col-xs-2" style="padding-left: 0px;padding-right: 0px;">
							<button id="close" onclick="$('#modalCuciEnthol').modal('hide');$('#tag').removeAttr('disabled');$('#tag').focus()" style="font-weight: bold;font-size: 20px;width: 100%;height: 48px;" class="btn btn-danger pull-right">CLOSE</button>
						</div>
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
							<div class="input-group">
								<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag_enthol" name="tag_enthol" placeholder="Scan Kanban ..." required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
									<i class="glyphicon glyphicon-credit-card"></i>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;margin-top: 10px">
							<button onclick="fetchEntholLog();" style="font-weight: bold;margin-bottom: 10px;" class="btn btn-primary"><i class="fa fa-refresh"></i> REFRESH</button>
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;margin-top: 10px;" id="tableEnthol">
								<thead>
									<tr>
										<th style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">#</th>
										<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">Material</th>
										<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">Loc</th>
										<th style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">Qty</th>
										<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">At</th>
									</tr>
								</thead>
								<tbody id="bodyEntholLog">
								</tbody>
							</table>
						</div>
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
			var asQty = 0, tsQty = 0;

			$.each(result.result, function(index, value) {
				if (value.hpl == 'ASKEY') {
					$('#ASresult').text(value.qty);
					asQty = value.qty;
				} else if (value.hpl == 'TSKEY') {
					$('#TSresult').text(value.qty);
					tsQty = value.qty;
				}else {
					$('#CLresult').text(value.qty);
					clQty = value.qty;
				}
			})

			$.each(result.ng, function(index, value) {
				if (value.hpl == 'ASKEY') {
					$('#ASnotGood').text(value.qty);
					$('#ASngRate').text(Math.round((value.qty/asQty)*100, 2)+'%');
				} else if (value.hpl == 'TSKEY') {
					$('#TSnotGood').text(value.qty);
					$('#TSngRate').text(Math.round((value.qty/tsQty)*100, 2)+'%');
				} else {
					$('#CLnotGood').text(value.qty);
					$('#CLngRate').text(Math.round((value.qty/clQty)*100, 2)+'%');
				}
			})
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

	function disabledButtonRework() {
		if($('#tag').val() != ""){
			var btn = document.getElementById('rework');
			btn.disabled = true;
			btn.innerText = 'Posting...'	
			return false;
		}
	}

	function rework(){
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
			ng: ng,
			count_text: count_text,
			// total_ng: total,
		}
		disabledButtonRework();

		$.post('{{ url("input/middle/rework") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('rework');
				btn.disabled = false;
				btn.innerText = 'REWORK';
				openSuccessGritter('Success!', result.message);
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text(0);
				}
				$('#description').text("");
				$('#model').text("");
				$('#key').text("");
				$('#qty_lot').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				fillResult($('#employee_id').val());
				$('#tag').focus();				
			}
			else{
				var btn = document.getElementById('rework');
				btn.disabled = false;
				btn.innerText = 'REWORK';
				audio_error.play();
				openErrorGritter('Error!', result.message);
				$("#tag").val("");
				$("#tag").focus();
			}
		});
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

		// console.log($('#material_tag').val());

		var data = {
			loc: $('#loc').val(),
			tag: $('#material_tag').val(),
			material_number: $('#material_number').val(),
			quantity: $('#material_quantity').val(),
			employee_id: $('#employee_id').val(),
			started_at: $('#started_at').val(),
			ng: ng,
			count_text: count_text,
			// total_ng: total,
		}
		disabledButton();

		$.post('{{ url("input/middle/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text(0);
				}
				$('#description').text("");
				$('#model').text("");
				$('#key').text("");
				$('#qty_lot').text("");
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
		$('#description').text("");
		$('#model').text("");
		$('#key').text("");
		$('#qty_lot').text("");
		$('#material_tag').val("");
		$('#material_number').val("");
		$('#material_quantity').val("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();

	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#material_number').val() != ""){
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
		if($('#material_number').val() != ""){
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
		$.get('{{ url("scan/middle/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#description').text(result.middle_inventory.material_description);
				$('#model').text(result.middle_inventory.model);
				$('#key').text(result.middle_inventory.key);
				$('#qty_lot').text(result.middle_inventory.quantity);
				$('#material_tag').val(result.middle_inventory.tag);
				$('#material_number').val(result.middle_inventory.material_number);
				$('#material_quantity').val(result.middle_inventory.quantity);
				$('#started_at').val(result.started_at);
			}
			else{
				$('#tag').prop('disabled', false);
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
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

	// ENTHOLE
	function openModalCuciEnthol(employee_id,type) {
		$('#modalCuciEnthol').modal('show')
		$('#tag').prop('disabled',true);
		$('#modalCuciEnthol').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#enthol_type').val('');
		$('#enthol_type').val(type);
		$('#title').html(type.toUpperCase()+' CUCI ENTHOL');

		$('#modalCuciEnthol').on('shown.bs.modal', function () {
			$('#tag_enthol').focus();
		});
		fetchEntholLog();
	}

	function fetchEntholLog() {
		$('#loading').show();
		var data = {
			location : $("#loc").val(),
		}
		$.get('{{ url("fetch/enthol/log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableEnthol').DataTable().clear();
				$('#tableEnthol').DataTable().destroy()
				$('#bodyEntholLog').html('');
				var bodyEnthol = '';

				for(var i = 0; i < result.enthol.length;i++){
					bodyEnthol += '<tr>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+(i+1)+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].material_number+' - '+result.descs[i]+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].location+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].quantity+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].created_at+'</td>';
					bodyEnthol += '</tr>';
				}
				$('#bodyEntholLog').append(bodyEnthol);
				$('#loading').hide();

				var table = $('#tableEnthol').DataTable({
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
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	$('#tag_enthol').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_enthol").val().length >= 8){
				var data = {
					tag : $("#tag_enthol").val(),
					loc : $("#loc").val(),
					employee_id : $('#op').text(),
					type : $('#enthol_type').val(),
				}
				
				$.get('{{ url("scan/enthol/kanban") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#tag_enthol').val('');
						$('#tag_enthol').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_enthol').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid.');
				audio_error.play();
				$('#tag').removeAttr('disabled');
				$('#tag').val('');
			}			
		}
	});


	// END ENTHOLE





</script>
@endsection