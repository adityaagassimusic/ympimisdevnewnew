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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="loc" value="{{ $loc }}">
	<input type="hidden" id="started_at">
	<input type="hidden" id="welding_time">
	<div class="row" style="margin-left: 0px; margin-right: 0px;">
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-8">
						<div class="row">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 0;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 16px;" colspan="2">Operator Kensa</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:16px; width: 30%;" id="op">-</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;" id="op2"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-4" style="padding-left: 5px;padding-right: 10cpx;">
							<div class="input-group">
								<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan RFID Card..." required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
									<i class="glyphicon glyphicon-credit-card"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<button class="btn btn-success btn-lg" style="width: 100%;font-weight: bold;" onclick="fetchResult()">KLIK UNTUK MELIHAT HASIL KENSA</button>
				<table style="width: 100%; margin-top: 5px;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 2%; font-size: 16px; font-weight: bold; background-color: rgb(100,100,100); color: yellow; border: 1px solid black" colspan="2"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Key</td>
							<td id="key" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">OP</td>
							<td id="opwelding" style="width: 5%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<input type="hidden" id="material_tag">
							<input type="hidden" id="barcode_number">
							<input type="hidden" id="material_number">
							<input type="hidden" id="material_quantity">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px;">
				<div class="col-xs-12" style="text-align: center; padding: 0;" id="check_point_dimensi">
				</div>
			</div>
			<div style="padding-top: 5px;">
				<div class="col-xs-6" style="text-align: center; padding: 0;" id="attention_point">
				</div>
				<div class="col-xs-6" style="text-align: center; padding: 0;" id="check_point">
				</div>
			</div>
		</div>
		<div class="col-xs-5" style="padding-right: 0;">
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
						<?php $nomor = 0 ?>
						<input type="hidden" id="loop" value="{{count($ng_lists)}}">
						@foreach($ng_lists as $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
					?>
					<tr <?php echo $color ?>>
						<td id="minus" onclick="minus({{$nomor}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>
						<td id="ng{{$nomor}}" style="font-size: 20px;">{{ $ng_list->ng_name }} </td>
						<td id="plus" onclick="plus({{$nomor}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>
						<td style=""><input style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;width: 100%;text-align: center;" id="count{{$nomor}}" value="0" readonly=""></td>
					</tr>
					<?php $no+=1; ?>
					<?php $nomor++; ?>
					@endforeach
				</tbody>
			</table>
		</div>
		<div>
			<center>
				<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 33%" onclick="canc()" class="btn btn-danger">CANCEL</button>
				<button id="rework" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 33%" onclick="rework()" class="btn btn-warning">REWORK</button>
				<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 33%" onclick="conf()" class="btn btn-success">CONFIRM</button>
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

<div class="modal fade" id="modalResult">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; padding:10px; font-weight: bold;color: white;">Production Result</h3></center>
			</div>
			<div class="modal-body" style="padding-top: 0px;">
				<div class="row">
					<div class="col-xs-12">
						<table style="width: 100%;" border="1" id="tablesx">
							<tbody>
								<tr>
									<td colspan="9" style="width: 1%; font-weight: bold; font-size: 16px; background-color: orange;">SAXOPHONE</td>
								</tr>
								<tr>
									<td colspan="3" style="width: 1%; font-weight: bold; font-size: 16px; background-color: #ffff66;">Alto</td>
									<td colspan="3" style="width: 1%; font-weight: bold; font-size: 16px; background-color: #1565c0;color: white;">Tenor</td>
									<td colspan="3" style="width: 1%; font-weight: bold; font-size: 16px; background-color: lightgreen;">82Z</td>
								</tr>
								<tr>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">OK</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG RATIO</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">OK</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG RATIO</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">OK</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG RATIO</td>
								</tr>
								<tr>
									<td id="result1" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black">999</td>
									<td id="result2" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ffccff; border: 1px solid black">999</td>
									<td id="result3" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">999</td>
									<td id="result4" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black">999</td>
									<td id="result5" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ffccff; border: 1px solid black">999</td>
									<td id="result6" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">999</td>
									<td id="result7" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black">999</td>
									<td id="result8" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ffccff; border: 1px solid black">999</td>
									<td id="result9" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">999</td>
								</tr>
							</tbody>
						</table>

						<table style="width: 100%;" border="1" id="tablecl">
							<tbody>
								<tr>
									<td colspan="3" style="width: 1%; font-weight: bold; font-size: 16px; background-color: lightgreen;">CLARINET</td>
								</tr>
								<tr>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">OK</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG RATIO</td>
								</tr>
								<tr>
									<td id="result1cl" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black">999</td>
									<td id="result2cl" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ffccff; border: 1px solid black">999</td>
									<td id="result3cl" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">999</td>
								</tr>
							</tbody>
						</table>

						<table style="width: 100%;" border="1" id="tablefl">
							<tbody>
								<tr>
									<td colspan="3" style="width: 1%; font-weight: bold; font-size: 16px; background-color: greenyellow;">FLUTE</td>
								</tr>
								<tr>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">OK</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG</td>
									<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: #cccccc; border: 1px solid black">NG RATIO</td>
								</tr>
								<tr>
									<td id="result1fl" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black">999</td>
									<td id="result2fl" style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ffccff; border: 1px solid black">999</td>
									<td id="result3fl" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">999</td>
								</tr>
							</tbody>
						</table>
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
		$('#tablecl').hide();
		$('#tablesx').hide();
		$('#tablefl').hide();
		$('#table'+$('#loc').val().split('-')[2]).show();
		for (var i = 0; i < loop; i++) {
			$('#count'+i).val(0);
		}
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#tag').val('');
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var emp_id = "";

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			getHeader(this.value);
		}
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			// if($('#operator').val().length > 9 ){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/welding/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// fetchResult(result.employee.employee_id);
						emp_id = result.employee.employee_id;
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						$('#tag').focus();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			// }else{
			// 	audio_error.play();
			// 	openErrorGritter('Error', 'Tag Invalid');
			// 	$('#operator').val('');
			// }
		}
	});

	function fetchResult(){
		$('#loading').show();
		var location = $('#loc').val();
		var employee_id = emp_id;
		var data = {
			employee_id:employee_id,
			location:location
		}
		$.get('{{ url("fetch/welding/kensa_result") }}', data, function(result, status, xhr){
			if (result.status) {
				var pctgAS = (result.ngs[0].askey/result.oks[0].askey)*100;
				var pctgTS = (result.ngs[0].tskey/result.oks[0].tskey)*100;
				var pctgZ = (result.ngs[0].z/result.oks[0].z)*100;

				var pctgcl = (result.ngs[0].cl/result.oks[0].cl)*100;
				var pctgfl = (result.ngs[0].fl/result.oks[0].fl)*100;

				$('#result1').text(result.oks[0].askey);
				$('#result2').text(result.ngs[0].askey);
				$('#result3').text(pctgAS.toFixed(1)+'%');
				$('#result4').text(result.oks[0].tskey);
				$('#result5').text(result.ngs[0].tskey);
				$('#result6').text(pctgTS.toFixed(1)+'%');
				$('#result7').text(result.oks[0].z);
				$('#result8').text(result.ngs[0].z);
				$('#result9').text(pctgZ.toFixed(1)+'%');
				$('#result1cl').text(result.oks[0].cl);
				$('#result2cl').text(result.ngs[0].cl);
				$('#result3cl').text(pctgcl.toFixed(1)+'%');
				$('#result1fl').text(result.oks[0].fl);
				$('#result2fl').text(result.ngs[0].fl);
				$('#result3fl').text(pctgfl.toFixed(1)+'%');

				$('#modalResult').modal('show');

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}

	function getHeader(tag) {
		$('#loading').show();
		$('#attention_point').html("");
		$('#check_point').html("");
		$('#check_point_dimensi').html("");
		var location = $('#loc').val();
		var data = {
			tag : tag,
			location : location
		}

		$.get('{{ url("scan/welding/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				$("#model").text((result.material.model || "")+" "+result.material.key+" "+result.material.surface);
				$("#key").text(result.material.key);
				if(result.opwelding){
					if(result.opwelding.last_check){
						if(result.opwelding.last_check.includes('PI')){
							// if($('#loc').val() == 'phs-visual-sx'){
							// 	$('#barcode_number').val(result.opwelding.phs_kartu_barcode);
							// }
							// else{
							// 	$('#barcode_number').val(result.opwelding.hsa_kartu_barcode);
							// }
							var name = result.opwelding.name;
							if (result.opwelding.name == null) {
								for(var i = 0; i < result.emp.length;i++){
									if (result.emp[i].employee_id == result.opwelding.last_check) {
										name = result.emp[i].name;
									}
								}
							}
							$("#opwelding").text(result.opwelding.last_check+" - "+(name || ''));	
							$('#welding_time').val(result.opwelding.created_at);
						}else{
							$("#opwelding").text("Operator Not Found");
							$('#welding_time').val("");
						}	
					}else{
						$("#opwelding").text("Operator Not Found");
						$('#welding_time').val("");
					}					
				}
				else{
					$("#opwelding").text("Operator Not Found");
					$('#welding_time').val("");		
				}
				
				if(location == 'hsa-visual-sx'){
					$('#attention_point').append('<img style="width: 100%; height: 445px;" src="'+result.attention_point+'">');
					$('#check_point').append('<img style="width: 100%; height: 445px;" src="'+result.check_point+'">');	
				}else if(location == 'hsa-dimensi-sx'){
					$('#check_point_dimensi').append('<img style="width: 100%;" src="'+result.check_point_dimensi+'">');	
				}


				$('#started_at').val(result.started_at);
				$('#material_tag').val(tag);
				$('#material_number').val(result.material.material_number);
				$('#material_quantity').val(result.material.lot_completion);

				// fetchResult($('#op').text());
				$("#tag").prop('disabled', true);
				openSuccessGritter('Success', result.message);
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#model').text("");
				$('#key').text("");
				$("#tag").val("");
				$("#tag").focus();
			}
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

		var tag = $('#tag').val();
		var loop = $('#loop').val();
		// var total = 0;
		var count_ng = 0;
		var ng = [];
		for (var i = 0; i < loop; i++) {
			if(parseInt($('#count'+i).val()) > 0){
				ng.push([$('#ng'+i).text(), $('#count'+i).val()]);
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
			welding_time: $('#welding_time').val(),
			started_at: $('#started_at').val(),
			ng: ng,
			operator_id:$('#opwelding').text()
			// total_ng: total,
		}
		disabledButtonRework();

		$.post('{{ url("input/welding/rework") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('rework');
				btn.disabled = false;
				btn.innerText = 'REWORK';
				openSuccessGritter('Success!', result.message);
				for (var i = 0; i < loop; i++) {
					$('#count'+i).val(0);
				}
				$('#attention_point').html("");
				$('#check_point').html("");
				$('#model').text("");
				$('#key').text("");
				$('#opwelding').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#welding_time').val("");
				$('#opwelding').val("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				$('#tag').focus();	

				// fetchResult($('#op').text());

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
		if($('#model').text() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#model").text("");

			return false;
		}

		var loop = $('#loop').val();
		// var total = 0;
		var count_ng = 0;
		var ng = [];
		var count_text = [];
		for (var i = 0; i < loop; i++) {
			if(parseInt($('#count'+i).val()) > 0){
				ng.push([$('#ng'+i).text(), $('#count'+i).val()]);
				// total += parseInt($('#count'+i).text());
				count_ng += 1;
			}
		}

		var data = {
			loc: $('#loc').val(),
			tag: $('#material_tag').val(),
			barcode_number: $('#barcode_number').val(),
			material_number: $('#material_number').val(),
			quantity: $('#material_quantity').val(),
			employee_id: $('#op').text(),
			operator_id: $('#opwelding').text().split(' - ')[0],
			started_at: $('#started_at').val(),
			welding_time: $('#welding_time').val(),
			cek: $('#material_quantity').val(),
			ng: ng,
			// total_ng: total,
		}
		disabledButton();

		$.post('{{ url("input/welding/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				for (var i = 0; i < loop; i++) {
					$('#count'+i).val(0);
				}
				$('#attention_point').html("");
				$('#check_point').html("");
				$('#model').text("");
				$('#key').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#welding_time').val("");
				$('#opwelding').text("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				$('#tag').focus();

				// fetchResult($('#op').text());

			}
			else{
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function canc(){
		var loop = $('#loop').val();
		for (var i = 0; i < loop; i++) {
			$('#count'+i).val(0);
		};
		$('#attention_point').html("");
		$('#check_point').html("");
		$('#model').text("");
		$('#key').text("");
		$('#opwelding').text("");
		$('#material_tag').val("");
		$('#material_number').val("");
		$('#material_quantity').val("");
		$('#employee_id').val("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();

	}

	function plus(id){
		var count = $('#count'+id).val();
		if($('#key').text() != ""){
			$('#count'+id).val(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).val();
		if($('#key').text() != ""){
			if(count > 0)
			{
				$('#count'+id).val(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
		}
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
</script>
@endsection
