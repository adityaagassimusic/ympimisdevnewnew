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
		<input type="hidden" id="location" value="injection">
		<input type="hidden" id="proses" value="injection">
		<input type="hidden" id="employee_id" value="{{ $employee->employee_id }}">

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
								</tbody>
							</table>
							<div class="col-xs-6">
								<button class="btn btn-primary" onclick="showSample(10)" style="font-weight: bold; font-size: 2vw; width: 100%;">10 SAMPEL</button>
							</div>
							<div class="col-xs-6">
								<button class="btn btn-primary" onclick="showSample(30)" style="font-weight: bold; font-size: 2vw; width: 100%;">30 SAMPEL</button>
							</div>
						</div>
					</div>
					<div class="box" id="measurement">
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

							<div class="col-xs-6 col-xs-offset-3" style="margin-top: 2%;">
								<button id="submit_approval" onclick="finishApproval()" class="btn btn-success" style="font-weight: bold; font-size: 2vw; width: 100%;"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;&nbsp;SUBMIT</button>
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

	function clearAll(){
		sample_global = 0;

		$("#resin").val('');
		$("#lot").val('');

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
	}

	function showSample(sample) {
		sample_global = sample;
		$('#measurement').hide();
		$('#body_sample').html("");
		stat = [];

		var css = "font-size:16px; width: 100%; height: 30px;";

		var body = '';
		body += '<tr>';
		body += '<td style="vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold; background-color: rgb(252, 248, 227);">Awal</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="panjang_awal">';
		body += '</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="diameter_awal">';
		body += '</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="tebal_awal">';
		body += '</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="berat_awal">';
		body += '</td>';
		body += '</tr>';
		body += '<tr>';
		body += '<td style="vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold; background-color: rgb(252, 248, 227);">Tengah</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="panjang_tengah">';
		body += '</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="diameter_tengah">';
		body += '</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="tebal_tengah">';
		body += '</td>';
		body += '<td style="background-color: rgb(252, 248, 227);">';
		body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="berat_tengah">';
		body += '</td>';
		body += '</tr>';
		for (var i = 1; i <= sample; i++) {
			body += '<tr>';
			body += '<td style="vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold;">Akhir '+i+'</td>';
			body += '<td>';
			body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="panjang_akhir'+i+'">';
			body += '</td>';
			body += '<td>';
			body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="diameter_akhir'+i+'">';
			body += '</td>';
			body += '<td>';
			body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="tebal_akhir'+i+'">';
			body += '</td>';
			body += '<td>';
			body += '<input type="text" style="'+css+'" onchange="changeVal(id)" class="numpad input" id="berat_akhir'+i+'">';
			body += '</td>';
			body += '</tr>';
		}

		var css = 'style="border: 1px solid; text-align: center; vertical-align: middle; padding: 0px; font-size: 16px; font-weight; bold; background-color: rgb(252, 248, 227);"'; 
		body += '<tr>';
		body += '<th '+css+'>Rata-rata</th>';
		body += '<th '+css+' id="panjang_mean"></th>';
		body += '<th '+css+' id="diameter_mean"></th>';
		body += '<th '+css+' id="tebal_mean"></th>';
		body += '<th '+css+' id="berat_mean"></th>';
		body += '</tr>';

		$('#body_sample').append(body);
		$('#panjang_awal').addClass('numpad');
		$('#diameter_awal').addClass('numpad');
		$('#tebal_awal').addClass('numpad');
		$('#berat_awal').addClass('numpad');

		$('#panjang_awal').css('background-color', 'white');
		$('#diameter_awal').css('background-color', 'white');
		$('#tebal_awal').css('background-color', 'white');
		$('#berat_awal').css('background-color', 'white');

		$('#panjang_tengah').addClass('numpad');
		$('#diameter_tengah').addClass('numpad');
		$('#tebal_tengah').addClass('numpad');
		$('#berat_tengah').addClass('numpad');

		$('#panjang_tengah').css('background-color', 'white');
		$('#diameter_tengah').css('background-color', 'white');
		$('#tebal_tengah').css('background-color', 'white');
		$('#berat_tengah').css('background-color', 'white');

		for (var i = 1; i <= sample; i++) {
			$('#panjang_akhir'+i).addClass('numpad');
			$('#diameter_akhir'+i).addClass('numpad');
			$('#tebal_akhir'+i).addClass('numpad');
			$('#berat_akhir'+i).addClass('numpad');

			$('#panjang_akhir'+i).css('background-color', 'white');
			$('#diameter_akhir'+i).css('background-color', 'white');
			$('#tebal_akhir'+i).css('background-color', 'white');
			$('#berat_akhir'+i).css('background-color', 'white');
		}

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('#measurement').show();

	}

	function changeVal(id) {
		var param = id.split('_');
		var index = param[0];
		var row = param[1];

		var total = 0;
		var awal = parseFloat($('#'+index+'_awal').val() || 0);
		var tengah = parseFloat($('#'+index+'_tengah').val() || 0);
		var numbers = [];
		var number_std = [];

		for (var i = 1; i <= sample_global; i++) {
			var quantity = $('#'+index+'_akhir'+i).val();
			if(quantity == ''){
				quantity = 0;
				number_std.push(parseFloat(quantity));
			}else{
				number_std.push(parseFloat(quantity));
				numbers.push(parseFloat(quantity));
			}

			total += parseFloat(quantity);
		}

		var mean = (total+awal+tengah)/(sample_global+2);
		
		$('#'+index+'_mean').text(parseFloat(mean).toFixed(2));
		
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

		if(resin == '' || lot == ''){
			openErrorGritter('Error!', 'Semua field harus diisi');
			return false;
		}



		var val_panjang = $('#panjang_awal').val();
		var val_diameter = $('#diameter_awal').val();
		var val_tebal = $('#tebal_awal').val();
		var val_berat = $('#berat_awal').val();
		if(val_panjang == '' || val_diameter == '' || val_tebal == '' || val_berat == ''){
			openErrorGritter('Error!', 'Semua poin ukur harus diisi');
			return false;
		}
		var awal = [];
		awal.push({
			'length' : val_panjang,
			'diameter' : val_diameter,
			'thickness' : val_tebal,
			'weight' : val_berat
		});




		var val_panjang = $('#panjang_tengah').val();
		var val_diameter = $('#diameter_tengah').val();
		var val_tebal = $('#tebal_tengah').val();
		var val_berat = $('#berat_tengah').val();
		if(val_panjang == '' || val_diameter == '' || val_tebal == '' || val_berat == ''){
			openErrorGritter('Error!', 'Semua poin ukur harus diisi');
			return false;
		}
		var tengah = [];
		tengah.push({
			'length' : val_panjang,
			'diameter' : val_diameter,
			'thickness' : val_tebal,
			'weight' : val_berat
		});



		var panjang = [];
		var diameter = [];
		var tebal = [];
		var berat = [];

		for (var i = 1; i <= sample_global; i++) {
			var val_panjang = $('#panjang_akhir'+i).val();
			var val_diameter = $('#diameter_akhir'+i).val();
			var val_tebal = $('#tebal_akhir'+i).val();
			var val_berat = $('#berat_akhir'+i).val();

			if(val_panjang == '' || val_diameter == '' || val_tebal == '' || val_berat == ''){
				openErrorGritter('Error!', 'Semua poin ukur harus diisi');
				return false;
			}

			panjang.push(parseFloat(val_panjang));
			diameter.push(parseFloat(val_diameter));
			tebal.push(parseFloat(val_tebal));
			berat.push(parseFloat(val_berat));
		}

		

		
		var data = {
			location : location,
			process : proses,
			date : date,
			sample : sample,
			order_id : order_id,
			employee_id : employee_id,
			machine : machine,
			resin : resin,
			lot : lot,
			awal : awal,
			tengah : tengah,
			length : panjang,
			diameter : diameter,
			thickness : tebal,
			weight : berat
		}

		$.post('{{ url("fetch/reed/submit_cdm") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter("Success", result.message);

				window.open('{{ url("index/reed")}}');

			}else{
				openErrorGritter("Error!", result.message);
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

