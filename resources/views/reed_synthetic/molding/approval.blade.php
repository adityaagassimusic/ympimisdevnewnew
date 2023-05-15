@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 1vw;
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error {
		display: none;
	}
	.input {
		text-align: center;
		font-weight: bold;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="location" value="molding">
		<input type="hidden" id="proses" value="injection">
		<input type="hidden" id="employee_id" value="{{ $employee->employee_id }}">
		<input type="hidden" id="panjang_max" value="{{ $material_stardard->max_length }}">
		<input type="hidden" id="panjang_min" value="{{ $material_stardard->min_length }}">
		<input type="hidden" id="diameter_max" value="{{ $material_stardard->max_diameter }}">
		<input type="hidden" id="diameter_min" value="{{ $material_stardard->min_diameter }}">
		<input type="hidden" id="tebal_max" value="{{ $material_stardard->max_thickness }}">
		<input type="hidden" id="tebal_min" value="{{ $material_stardard->min_thickness }}">
		<input type="hidden" id="berat_max" value="{{ $material_stardard->max_weight }}">
		<input type="hidden" id="berat_min" value="{{ $material_stardard->min_weight }}">

		<div class="col-xs-12">
			<div class="row" style="margin-top: 1%">
				<div class="col-xs-7" style="">
					<div class="box">
						<div class="box-body">
							<table class="table table-bordered">
								<thead style="background-color: orange;">
									<tr>
										<th colspan="2" style="font-size: 2vw;">{{ $order->material_description }}</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th style="vertical-align: middle;">ORDER ID</th>
										<th>
											<input type="text" name="order_id" id="order_id" class="form-control" value="{{ $order->order_id }}" readonly>
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">TANGGAL</th>
										<th>
											<input type="text" name="date" id="date" class="form-control" value="{{ date('Y-m-d', strtotime($order->created_at)) }}" readonly>
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">OP MTC Molding</th>
										<th>
											<input type="text" name="operator" id="operator" class="form-control" value="{{ $employee->employee_id }} - {{ $employee->name }}" readonly>
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">MESIN</th>
										<th>
											<input type="text" name="machine" id="machine" class="form-control" value="{{ $list->remark }}" readonly>
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">RESIN</th>
										<th>
											<input type="text" name="resin" id="resin" class="form-control">
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">NO. LOT RESIN</th>
										<th>
											<input type="text" name="lot" id="lot" class="form-control">
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">PARAMETER LS 4</th>
										<th>
											<input type="text" name="parameter" id="parameter" class="form-control">
										</th>
									</tr>
									<tr>
										<th style="vertical-align: middle;">PARAMETER INJEKSI</th>
										<th>
											<input accept="image/*" capture="environment" type="file" class="file" style="display:none" onchange="readURL(this);" id="input_photo">
											<button class="btn btn-default btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)" style="font-size: 1.5vw; width: 300px; height: 200px;"><i class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Foto Parameter</button>
											<img width="150px" id="photo" src="" onclick="buttonImage(this)" style="display: none; width: 300px; height: 200px;" alt=""/>
										</th>
									</tr>
								</tbody>
							</table>
							<div class="col-xs-4">
								<button class="btn btn-danger" onclick="submitng()" style="font-weight: bold; font-size: 2vw; width: 100%;">NG</button>
							</div>
							<div class="col-xs-4">
								<button class="btn btn-primary" onclick="showSample(10)" style="font-weight: bold; font-size: 2vw; width: 100%;">10 SAMPEL</button>
							</div>
							<div class="col-xs-4">
								<button class="btn btn-primary" onclick="showSample(30)" style="font-weight: bold; font-size: 2vw; width: 100%;">30 SAMPEL</button>
							</div>
						</div>
					</div>

				</div>
				<div class="col-xs-5" style="padding-left: 0px;">
					<div class="box">
						<div class="box-body">
							<h3 style="margin-top: 0px;">STANDAR PANJANG & JUMLAH SAMPEL</h3>
							<table class="table table-bordered table-stripped">
								<thead style="background-color: orange;">
									<tr>
										<th style="width: 30%; font-size: 1vw;">Produk</th>
										<th style="width: 40%; font-size: 1vw;">Panjang Keseluruhan (mm)</th>
										<th style="width: 30%; font-size: 1vw;">Jumlah Sampel</th>
									</tr>
								</thead>
								<tbody id="pickingTableBody" style="background-color: #f5f5f5;">
									<tr>
										<td style="vertical-align: middle;" rowspan="2">CLR</td>
										<td style="text-align: left;">≥ 70.2 mm</td>
										<td>10 PC(s)</td>
									</tr>
									<tr>
										<td style="text-align: left;">≥ 69.8 mm & < 70.2 mm</td>
										<td>30 PC(s)</td>
									</tr>
									<tr>
										<td style="vertical-align: middle;" rowspan="2">ASR</td>
										<td style="text-align: left;">≥ 73.3 mm</td>
										<td>10 PC(s)</td>
									</tr>
									<tr>
										<td style="text-align: left;">≥ 72.9 mm & < 73.3 mm</td>
										<td>30 PC(s)</td>
									</tr>
									<tr>
										<td style="vertical-align: middle;" rowspan="2">TSR</td>
										<td style="text-align: left;">≥ 82.2 mm</td>
										<td>10 PC(s)</td>
									</tr>
									<tr>
										<td style="text-align: left;">≥ 81.7 mm & < 82.2 mm</td>
										<td>30 PC(s)</td>
									</tr>
								</tbody>
							</table>
							<h3 style="margin-top: 30px; margin-bottom: 0px;">CEK POIN</h3>
							<div class="col-xs-12">
								<img src="{{ $point_check }}" style="width: 100%; height: 30%; text-align: center;" alt="your image"/>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row" id="measurement">
				<div class="col-xs-7" style="">
					<div class="box">
						<div class="box-body">
							<table class="table table-bordered table-stripped">
								<thead style="background-color: #cdcdcd">
									<tr>
										<th style="width: 20%; vertical-align: middle; padding: 0px;" rowspan="3">No Shot</th>
										<th colspan="4" style="padding: 0px;">Poin Ukur</th>
									</tr>
									<tr>
										<th style="width: 20%; padding: 0px;">1</th>
										<th style="width: 20%; padding: 0px;">2</th>
										<th style="width: 20%; padding: 0px;">3</th>
										<th style="width: 20%; padding: 0px;">4</th>
									</tr>
									<tr>
										<th style="vertical-align: middle; padding: 0px;">Panjang (mm)</th>
										<th style="vertical-align: middle; padding: 0px;">Diameter Cold Slug Well (mm)</th>
										<th style="vertical-align: middle; padding: 0px;">Tebal (mm)</th>
										<th style="vertical-align: middle; padding: 0px;">Berat (gr)</th>
									</tr>
								</thead>
								<tbody id="body_sample">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-5" style="padding-left: 0px;">
					<div class="box">
						<div class="box-body">
							<div class="row">
								<div class="col-xs-12">
									<h3 style="margin-top: 0px;">STANDAR POIN UKUR</h3>
									<table class="table table-bordered table-stripped">
										<thead style="background-color: #cdcdcd">
											<tr>
												<th style="width: 20%; padding: 0px; vertical-align: middle;">Produk</th>
												<th style="width: 20%; padding: 0px; vertical-align: middle;">Panjang</th>
												<th style="width: 20%; padding: 0px; vertical-align: middle;">Diamater Cold Slug Well</th>
												<th style="width: 20%; padding: 0px; vertical-align: middle;">Tebal</th>
												<th style="width: 20%; padding: 0px; vertical-align: middle;">Berat</th>
											</tr>
										</thead>
										<tbody>
											@foreach($stardard as $row)
											<tr>
												<td>{{ $row->material_description }}</td>

												@if(is_null($row->max_length))
												<td style="vertical-align: middle;">≥ {{ $row->min_length }}</td>
												@else
												<td style="vertical-align: middle;">≥ {{ $row->min_length }} & ≤ {{ $row->max_length }}</td>
												@endif

												@if(is_null($row->max_diameter))
												<td style="vertical-align: middle;">≥ {{ $row->min_diameter }}</td>
												@else
												<td style="vertical-align: middle;">≥ {{ $row->min_diameter }} & ≤ {{ $row->max_diameter }}</td>
												@endif

												@if(is_null($row->max_thickness))
												<td style="vertical-align: middle;">≥ {{ $row->min_thickness }}</td>
												@else
												<td style="vertical-align: middle;">≥ {{ $row->min_thickness }} & ≤ {{ $row->max_thickness }}</td>
												@endif

												@if(is_null($row->max_length))
												<td style="vertical-align: middle;">≥ {{ $row->min_weight }}</td>
												@else
												<td style="vertical-align: middle;">≥ {{ $row->min_weight }} & ≤ {{ $row->max_weight }}</td>
												@endif
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="col-xs-12">
									<h5 style="font-weight: bold;">Standart Panjang : Rata-rata keseluruhan - (4 x σ) ≥ Dimensi Trimming Maksimal + Trim Allowance</h5>
								</div>
								
								<div class="col-xs-8 col-xs-offset-2" style="padding-left: 0px;">
									<table class="table table-bordered table-stripped">
										<thead style="background-color: #cdcdcd">
											<tr>
												<th style="padding: 0px;">Poin Ukur</th>
												<th style="padding: 0px;">Status</th>
												<th style="padding: 0px;">Ket.</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="padding: 0px;">Panjang</td>
												<td style="padding: 0px;" id="panjang_ket"></td>
												<td style="padding: 0px;" id="panjang_value"></td>
											</tr>
											<tr>
												<td style="padding: 0px; font-size: 14px;">Diameter Cold Slug Well</td>
												<td style="padding: 0px;" id="diameter_ket"></td>
												<td style="padding: 0px;" id="diameter_value"></td>
											</tr>
											<tr>
												<td style="padding: 0px;">Tebal</td>
												<td style="padding: 0px;" id="tebal_ket"></td>
												<td style="padding: 0px;" id="tebal_value"></td>
											</tr>
											<tr>
												<td style="padding: 0px;">Berat</td>
												<td style="padding: 0px;" id="berat_ket"></td>
												<td style="padding: 0px;" id="berat_value"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							
							
							<div class="col-xs-8 col-xs-offset-2">
								<button id="submit_approval" onclick="finishApproval()" class="btn btn-success" style="font-weight: bold; font-size: 2vw; width: 100%;"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;&nbsp;SUBMIT</button>
								<button id="approval_again" onclick="clearAll()" class="btn btn-primary" style="font-weight: bold; font-size: 2vw; width: 100%;"><i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;ISI APPROVAL LAGI</button>
							</div>
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
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:20px; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:20px; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		clearAll();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	var sample_global = 0;
	var stat = [];


	function clearAll(){
		sample_global = 0;
		stat = [];

		$("#resin").val('');
		$("#lot").val('');
		$("#parameter").val('');
		$("#input_photo").val('');
		$("#photo").hide();
		$("#btnImage").show();

		$('#measurement').hide();
		$('#body_sample').html('');

		$('#panjang_value').html('');
		$('#diameter_value').html('');
		$('#tebal_value').html('');
		$('#berat_value').html('');
		$('#panjang_value').css('background-color', 'none');
		$('#diameter_value').css('background-color', 'none');
		$('#tebal_value').css('background-color', 'none');
		$('#berat_value').css('background-color', 'none');

		$('#panjang_ket').html('');
		$('#diameter_ket').html('');
		$('#tebal_ket').html('');
		$('#berat_ket').html('');
		$('#panjang_ket').css('background-color', 'none');
		$('#diameter_ket').css('background-color', 'none');
		$('#tebal_ket').css('background-color', 'none');
		$('#berat_ket').css('background-color', 'none');

		$('#submit_approval').show();
		$('#submit_approval').prop('disabled', false);

		$('#approval_again').hide();
		$('#approval_again').prop('disabled', true);
	}

	function buttonImage(elem) {
		$(elem).closest("th").find("input").click();
	}

	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				var img = $(input).closest("th").find("img");
				$(img).show();
				$(img).attr('src', e.target.result);
			};

			reader.readAsDataURL(input.files[0]);

		}

		$("#photo").show();
		$(input).closest("th").find("button").hide();
	}

	function showSample(sample) {
		sample_global = sample;
		$('#measurement').hide();
		$('#body_sample').html("");
		stat = [];

		var body = '';
		for (var i = 1; i <= sample; i++) {
			body += '<tr>';
			body += '<td style="vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold;">'+i+'</td>';
			body += '<td>';
			body += '<input type="text" style="font-size:16px; width: 100%; height: 30px;" onchange="changeVal(id)" class="numpad input" id="panjang_'+i+'">';
			body += '</td>';
			body += '<td>';
			body += '<input type="text" style="font-size:16px; width: 100%; height: 30px;" onchange="changeVal(id)" class="numpad input" id="diameter_'+i+'">';
			body += '</td>';
			body += '<td>';
			body += '<input type="text" style="font-size:16px; width: 100%; height: 30px;" onchange="changeVal(id)" class="numpad input" id="tebal_'+i+'">';
			body += '</td>';
			body += '<td>';
			body += '<input type="text" style="font-size:16px; width: 100%; height: 30px;" onchange="changeVal(id)" class="numpad input" id="berat_'+i+'">';
			body += '</td>';
			body += '</tr>';
		}

		var css = 'style="border: 1px solid; text-align: center; vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold; background-color: rgb(252, 248, 227);"'; 
		body += '<tr>';
		body += '<th '+css+'>STD Deviasi (σ)</th>';
		body += '<th '+css+' id="panjang_deviasi"></th>';
		body += '<th '+css+' id="diameter_deviasi"></th>';
		body += '<th '+css+' id="tebal_deviasi"></th>';
		body += '<th '+css+' id="berat_deviasi"></th>';
		body += '</tr>';

		body += '<tr>';
		body += '<th '+css+'>Rata-rata</th>';
		body += '<th '+css+' id="panjang_mean"></th>';
		body += '<th '+css+' id="diameter_mean"></th>';
		body += '<th '+css+' id="tebal_mean"></th>';
		body += '<th '+css+' id="berat_mean"></th>';
		body += '</tr>';

		body += '<tr>';
		body += '<th '+css+'>Nilai Maksimum</th>';
		body += '<th '+css+' id="panjang_value_max"></th>';
		body += '<th '+css+' id="diameter_value_max"></th>';
		body += '<th '+css+' id="tebal_value_max"></th>';
		body += '<th '+css+' id="berat_value_max"></th>';
		body += '</tr>';

		body += '<tr>';
		body += '<th '+css+'>Nilai Minimum</th>';
		body += '<th '+css+' id="panjang_value_min"></th>';
		body += '<th '+css+' id="diameter_value_min"></th>';
		body += '<th '+css+' id="tebal_value_min"></th>';
		body += '<th '+css+' id="berat_value_min"></th>';
		body += '</tr>';

		$('#body_sample').append(body);			

		for (var i = 1; i <= sample; i++) {
			$('#panjang_'+i).addClass('numpad');
			$('#diameter_'+i).addClass('numpad');
			$('#tebal_'+i).addClass('numpad');
			$('#berat_'+i).addClass('numpad');

			$('#panjang_'+i).css('background-color', 'white');
			$('#diameter_'+i).css('background-color', 'white');
			$('#tebal_'+i).css('background-color', 'white');
			$('#berat_'+i).css('background-color', 'white');
		}

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('#measurement').show();

		for (var i = 0; i < sample; i++) {
			stat.push({
				'diameter' : 'OK',
				'tebal' : 'OK',
				'berat' : 'OK'
			});
		}

	}

	function changeVal(id) {
		var param = id.split('_');
		var index = param[0];
		var row = param[1];

		var total = 0;
		var numbers = [];
		var number_std = [];

		for (var i = 1; i <= sample_global; i++) {
			var quantity = $('#'+index+'_'+i).val();
			if(quantity == ''){
				quantity = 0;
				number_std.push(parseFloat(quantity));
			}else{
				number_std.push(parseFloat(quantity));
				numbers.push(parseFloat(quantity));
			}

			total += parseFloat(quantity);
		}

		var mean = total/sample_global;
		var max = Math.max.apply(null, numbers);
		var min = Math.min.apply(null, numbers);

		var sigma = 0;
		for (var x = 0; x < number_std.length; x++) {
			var v1 =  Math.pow(parseFloat(number_std[x] - mean), 2);
			sigma += v1;
		}

		var std = Math.sqrt(sigma/number_std.length);

		$('#'+index+'_deviasi').text(parseFloat(std).toFixed(4));
		$('#'+index+'_mean').text(parseFloat(mean).toFixed(2));
		$('#'+index+'_value_max').text(max);
		$('#'+index+'_value_min').text(min);

		var value = mean - (4 * std);

		if(index == 'panjang'){
			$('#'+index+'_value').text(parseFloat(value).toFixed(2));

			var max = parseFloat($('#'+index+'_max').val());
			var min = parseFloat($('#'+index+'_min').val());
			if(max > 0){
				if(value >= min && value <= max){
					$('#'+index+'_ket').text('OK');
					$('#'+index+'_ket').css('background-color', 'rgb(204, 255, 255)');
					$('#'+index+'_value').css('background-color', 'rgb(204, 255, 255)');
				}else{
					$('#'+index+'_ket').text('NG');
					$('#'+index+'_ket').css('background-color', 'rgb(255, 204, 255)');
					$('#'+index+'_value').css('background-color', 'rgb(255, 204, 255)');
				}
			}else{
				if(value >= min){
					$('#'+index+'_ket').text('OK');
					$('#'+index+'_ket').css('background-color', 'rgb(204, 255, 255)');
					$('#'+index+'_value').css('background-color', 'rgb(204, 255, 255)');
				}else{
					$('#'+index+'_ket').text('NG');
					$('#'+index+'_ket').css('background-color', 'rgb(255, 204, 255)');
					$('#'+index+'_value').css('background-color', 'rgb(255, 204, 255)');
				}
			}

		}else{
			$('#'+index+'_value').text('-');
			value = parseFloat($('#'+id).val());
			var max = parseFloat($('#'+index+'_max').val());
			var min = parseFloat($('#'+index+'_min').val());

			if(max > 0){
				if(value >= min && value <= max){
					stat[row-1][index] = 'OK'
					$('#'+id).css('background-color', 'white');
				}else{
					stat[row-1][index] = 'NG'
					$('#'+id).css('background-color', 'rgb(255, 204, 255)');
				}
			}else{
				if(value >= min){
					stat[row-1][index] = 'OK'
					$('#'+id).css('background-color', 'white');
				}else{
					stat[row-1][index] = 'NG'
					$('#'+id).css('background-color', 'rgb(255, 204, 255)');
				}
			}

			var stat_ket = 'OK'
			for (var i = 0; i < stat.length; i++) {
				if(stat[i][index] == 'NG'){
					stat_ket = 'NG'
				}
			}

			$('#'+index+'_ket').text(stat_ket);
			if(stat_ket == 'OK'){
				$('#'+index+'_value').css('background-color', 'rgb(204, 255, 255)');
				$('#'+index+'_ket').css('background-color', 'rgb(204, 255, 255)');
			}else{
				$('#'+index+'_value').css('background-color', 'rgb(255, 204, 255)');
				$('#'+index+'_ket').css('background-color', 'rgb(255, 204, 255)');
			}
		}
	}

	function submitng() {
		var location = $("#location").val();
		var proses = $("#proses").val();
		var date = $("#date").val();
		var sample = sample_global;
		var order_id = $("#order_id").val();
		var employee_id = $("#employee_id").val();
		var machine = $("#machine").val();
		var resin = $("#resin").val();
		var lot = $("#lot").val();
		var parameter = $("#parameter").val();

		if(resin == '' || lot == '' || parameter == ''){
			openErrorGritter('Error!', 'Semua field harus diisi');
			return false;
		}

		if($('#input_photo').val() == ''){
			openErrorGritter('Error!', 'Foto parameter kosong');
			return false;
		}

		var formData = new FormData();
		formData.append('location', location);
		formData.append('process', proses);
		formData.append('date', date);
		formData.append('sample', sample);
		formData.append('order_id', order_id);
		formData.append('employee_id', employee_id);
		formData.append('machine', machine);
		formData.append('resin', resin);
		formData.append('lot', lot);
		formData.append('parameter', parameter)

		formData.append('file_datas', $('#input_photo').prop('files')[0]);
		var file = $('#input_photo').val().replace(/C:\\fakepath\\/i, '').split(".");
		formData.append('extension', file[1]);
		formData.append('photo_name', file[0]);

		$.ajax({
			url:"{{ url('fetch/reed/submit_approval_ng') }}",
			method:"POST",
			data:formData,
			contentType: false,
			cache: false,
			processData: false,
			success: function (result, status, xhr) {
				if(result.status){
					openSuccessGritter("Success", result.message);
					$('#submit_approval').hide();
					$('#submit_approval').prop('disabled', true);

					$('#approval_again').show();
					$('#approval_again').prop('disabled', false);

					clearAll();			
				}else{
					openErrorGritter("Error!", result.message);
				}
			},
			error: function (result, status, xhr) {
				openErrorGritter("Error!", result.message);

			},
		})
	}

	function finishApproval() {
		var location = $("#location").val();
		var proses = $("#proses").val();
		var date = $("#date").val();
		var sample = sample_global;
		var order_id = $("#order_id").val();
		var employee_id = $("#employee_id").val();
		var machine = $("#machine").val();
		var resin = $("#resin").val();
		var lot = $("#lot").val();
		var parameter = $("#parameter").val();

		if(resin == '' || lot == '' || parameter == ''){
			openErrorGritter('Error!', 'Semua field harus diisi');
			return false;
		}

		if($('#input_photo').val() == ''){
			openErrorGritter('Error!', 'Foto parameter kosong');
			return false;
		}

		var panjang = [];
		var diameter = [];
		var tebal = [];
		var berat = [];
		var status = [];

		status.push($('#panjang_ket').text());
		status.push($('#diameter_ket').text());
		status.push($('#tebal_ket').text());
		status.push($('#berat_ket').text());

		

		for (var i = 1; i <= sample_global; i++) {
			var val_panjang = $('#panjang_'+i).val();
			var val_diameter = $('#diameter_'+i).val();
			var val_tebal = $('#tebal_'+i).val();
			var val_berat = $('#berat_'+i).val();

			if(val_panjang == '' || val_diameter == '' || val_tebal == '' || val_berat == ''){
				openErrorGritter('Error!', 'Semua poin ukur harus diisi');
				return false;
			}

			panjang.push(parseFloat(val_panjang));
			diameter.push(parseFloat(val_diameter));
			tebal.push(parseFloat(val_tebal));
			berat.push(parseFloat(val_berat));
		}

		var formData = new FormData();
		formData.append('location', location);
		formData.append('process', proses);
		formData.append('date', date);
		formData.append('sample', sample);
		formData.append('order_id', order_id);
		formData.append('employee_id', employee_id);
		formData.append('machine', machine);
		formData.append('resin', resin);
		formData.append('lot', lot);
		formData.append('length', panjang);
		formData.append('diameter', diameter);
		formData.append('thickness', tebal);
		formData.append('weight', berat);
		formData.append('parameter', parameter);
		formData.append('status', status);

		formData.append('file_datas', $('#input_photo').prop('files')[0]);
		var file = $('#input_photo').val().replace(/C:\\fakepath\\/i, '').split(".");
		formData.append('extension', file[1]);
		formData.append('photo_name', file[0]);

		$('#loading').show();
		$.ajax({
			url:"{{ url('fetch/reed/submit_approval') }}",
			method:"POST",
			data:formData,
			contentType: false,
			cache: false,
			processData: false,
			success: function (result, status, xhr) {
				if(result.status){
					$('#submit_approval').hide();
					$('#submit_approval').prop('disabled', true);

					$('#approval_again').show();
					$('#approval_again').prop('disabled', false);

					$('#loading').hide();
					openSuccessGritter("Success", result.message);
				}else{
					$('#loading').hide();					
					openErrorGritter("Error!", result.message);
				}
			},
			error: function (result, status, xhr) {
				console.log(result.message);
				$('#loading').hide();					
				openErrorGritter("Error!", result.message);

			},
		})
	}


	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '7000'
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

