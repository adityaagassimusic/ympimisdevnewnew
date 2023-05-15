@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">

<style type="text/css">
	/*Start CSS Numpad*/
	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	/*End CSS Numpad*/

	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	input[type=number] {
		-moz-appearance:textfield;
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
	#master:hover {
		cursor: pointer;
	}
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	#qr_code {
		text-align: center;
		font-weight: bold;
	}
	.input {
		text-align: center;
		font-weight: bold;
	}
	#progress-text {
		text-align: center;
		font-weight: bold;
		font-size: 20px;
		color: #fff;
	}

</style>
@stop
@section('header')
@endsection
@section('content')

<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
	<p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
		<span style="font-size: 50px;">Please wait ... </span><br>
		<span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>

<section class="content" style="padding-top: 0;">
	<div class="row" style="margin-left: 1%; margin-right: 1%;" id="main">
		<div class="col-xs-12" style="padding-left: 0px;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<p id="operator_name" style="font-size:18px; text-align: center; color: yellow; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;"></p>
				<p style="font-size:18px; text-align: center; color: yellow; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;">Tanggal : <?= date('d-m-Y') ?></p>
				<p id="location_name" style="font-size:18px; text-align: center; color: yellow; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;"></p>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;overflow-x: scroll;">
			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;" colspan="10" id='audit_title'>Audit Case Assy</th>
					</tr>
					<tr>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">RACK CODE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">ITEM CODE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">ITEM DESCRIPTION</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">STOCK</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">STOCK ACTUAL</th>
						<!-- <th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">KANBAN EDAR</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">KANBAN EDAR ACTUAL</th> -->
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">ACTION</th>
					</tr>
				</thead>
				<tbody id="audit_body">
				</tbody>
			</table>
		</div>

		<!-- <div class="col-xs-12" style="padding: 0px;">
			<button type="button" style="font-size:20px; height: 40px; font-weight: bold; margin-right: 1%; padding: 9.5%; padding-top: 0px; padding-bottom: 0px;" onclick="canc()" id="cancel" class="btn btn-danger">&nbsp;CANCEL&nbsp;</button>
			<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 9.5%; padding-top: 0px; padding-bottom: 0px;" onclick="conf()" id="confirm" class="btn btn-success" disabled>CONFIRM</button>

		</div> -->
	</div>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Lokasi</label>
							<select class="form-control select2" name="lokasi" id='lokasi' data-placeholder="Pilih Lokasi" style="width: 100%;">
								<option value="">Pilih Lokasi</option>

								@foreach($location as $loc)
								<option value="{{ $loc->location }}">{{ $loc->location }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label for="exampleInputEmail1">Operator</label>
							<select class="form-control select2" name="operator" id='operator' data-placeholder="Pilih Operator" style="width: 100%;">
								<option value="">Pilih Operator</option>
								@foreach($employees as $employee)
								<option value="{{ $employee->employee_id }} - {{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<button class="btn btn-success pull-right" onclick="selectData()" style="width: 33%">Submit</button>
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
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 37.5%; z-index: 10000000; border: 2px solid grey;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){
		$(this).find('.del').addClass('btn-default');
		$(this).find('.clear').addClass('btn-default');
		$(this).find('.cancel').addClass('btn-default');		
		$(this).find('.done').addClass('btn-success');
		$(this).find('.neg').addClass('btn-default');
		$('.neg').css('display', 'block');
	};

	jQuery(document).ready(function() {

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});


		$('.select2').select2({
			dropdownAutoWidth : true,
			allowClear:true
		});


		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

	});

	function selectData(){

		var operator = $('#operator').val(); 
		var lokasi = $('#lokasi').val(); 

		if(lokasi == ""){
			$("#loading").hide();
			alert("Kolom Lokasi Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		if(operator == ""){
			$("#loading").hide();
			alert("Kolom Operator Harap diisi");
			$("html").scrollTop(0);
			return false;
		}
			
		$('#modalOperator').modal('hide');

		$('#operator_name').text('');
		$('#operator_name').text('Operator : ' + operator);

		$('#location_name').text('');
		$('#location_name').text('Lokasi : ' + lokasi);

		fillStore(lokasi);
	}


	function fillStore(loc){
		var data = {
			loc : loc
		}

		$.get('{{ url("fetch/tools/audit") }}', data, function(result, status, xhr){
			if (result.status) {

				// if(result.status){
				$('#confirm').prop('disabled', false);
				// }else{
					// $('#confirm').prop('disabled', true);
				// }

				$("#audit_body").empty();
				$("#audit_body").html("");
				$("#audit_title").text("");
				$("#audit_title").text("Audit Tools");

				$('#confirm').hide();			

				var body = '';
				var num = '';
				for (var i = 0; i < result.lists.length; i++) {
					body += '<tr>';
					body += '<td style="width:15%;padding: 0px; background-color: rgb(250,250,210); text-align: left; color: #000000; font-size: 20px;padding-left:5px" id="number_'+result.lists[i].id+'">'+result.lists[i].rack_code+'</td>';
					body += '<td style="width:15%;padding: 0px; background-color: rgb(250,250,210); text-align: left; color: #000000; font-size: 20px;padding-left:5px" id="code_'+result.lists[i].id+'">'+result.lists[i].item_code+'</td>';
					body += '<td style="width:50%;padding: 0px; background-color: rgb(250,250,210); text-align: left; color: #000000; font-size: 20px;padding-left:5px" id="description_'+result.lists[i].id+'">'+result.lists[i].description+'</td>';
					body += '<td style="width:10%;padding: 0px; background-color: rgb(250,250,210); text-align: center; color: #000000; font-size: 20px;padding-left:5px" id="qty_'+result.lists[i].id+'">'+result.lists[i].stock_kanban+'</td>';
					body += '<td style="width:10%;padding: 0px; background-color: rgb(250,250,210); text-align: center; color: #000000; font-size: 20px;"> <input type="number" class="form-control numpad"  id="audit_qty_'+result.lists[i].id+'" name="audit_'+result.lists[i].id+'"></td>';
					body += '<td style="width: 5%;padding: 0px;"><button id="save_button" type="button" style="font-size:14px; font-weight: bold;padding:1px 4px;" onclick="save('+result.lists[i].id+')" class="btn btn-success"> <i class="fa fa-save"></i></button></td>';

					// body += '<td style="width:10%;padding: 0px; background-color: rgb(250,250,210); text-align: center; color: #000000; font-size: 20px;padding-left:5px" id="qty_'+result.lists[i].id+'">'+result.lists[i].stock_kanban+'</td>';

					// var stat = 0;
					// for (var z = 0; z < result.audits.length;z++) {
					// 	if (result.audits[z].material_number == result.lists[i].material_number) {
					// 		body += '<td style="width:10%;padding: 0px; background-color: rgb(0,300,0); text-align: center; color: #000000; font-size: 20px;">'+result.audits[z].qty_audit+'</td>';

					// 		body += '<td style="width: 5%;padding: 0px;"><button type="button" style="font-size:14px; font-weight: bold;padding:1px 4px;" onclick="cancelInput('+result.lists[i].id+')" class="btn btn-danger"><i class="fa fa-close"></i></button></td>';
					// 		var stat = 1;
					// 	}
					// }

					// if (stat == 0) {
					// 	body += '<td style="width:10%;padding: 0px; background-color: rgb(250,250,210); text-align: center; color: #000000; font-size: 20px;"> <input type="number" class="form-control numpad"  id="audit_'+result.lists[i].id+'" name="audit_'+result.lists[i].id+'"></td>';

					// 	body += '<td style="width: 5%;padding: 0px;"><button id="save_button" type="button" style="font-size:14px; font-weight: bold;padding:1px 4px;" onclick="save('+result.lists[i].id+')" class="btn btn-success"> <i class="fa fa-save"></i></button></td>';
					// }
					
					body += '</tr>';
				}
				
				$("#audit_body").append(body);
				$('#confirm').show();

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

			}else {
				if(result.message){
					openErrorGritter('Error', result.message);
				}else{
					openErrorGritter('Error', 'Not Found');		
				}
			}
		});
	}

	

	function save(id){
		var material_number = $("#number_"+id).text();
		var material_description = $("#description_"+id).text();
		var qty = $("#qty_"+id).text();
		var qty_audit = $("#audit_"+id).val();
		var lokasi = $('#lokasi').val(); 
		
		var data = {
			id : id,
			material_number : material_number,
			material_description : material_description,
			qty : qty,
			qty_audit : qty_audit
		}

		$.post('{{ url("fetch/case/audit/confirm") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', result.message);
				fillStore(lokasi);
				// cancInput();

				audio_ok.play();

			}else{
				openErrorGritter('Error', result.message);
				audio_error.play();

			}
		});
	}

	function save(id){
		var material_number = $("#number_"+id).text();
		var material_description = $("#description_"+id).text();
		var qty = $("#qty_"+id).text();
		var qty_audit = $("#audit_"+id).val();
		var lokasi = $('#lokasi').val(); 
		
		var data = {
			id : id,
			material_number : material_number,
			material_description : material_description,
			qty : qty,
			qty_audit : qty_audit
		}

		$.post('{{ url("fetch/case/audit/confirm") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', result.message);
				fillStore(lokasi);
				// cancInput();

				audio_ok.play();

			}else{
				openErrorGritter('Error', result.message);
				audio_error.play();

			}
		});
	}

	function cancelInput(id){
		var material_number = $("#number_"+id).text();
		var material_description = $("#description_"+id).text();
		var qty = $("#qty_"+id).text();
		var qty_audit = $("#audit_"+id).text();
		var lokasi = $('#lokasi').val(); 
		
		var data = {
			id : id,
			material_number : material_number,
			material_description : material_description,
			qty : qty,
			qty_audit : qty_audit
		}

		$.post('{{ url("fetch/case/audit/delete") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', result.message);
				fillStore(lokasi);
				// cancInput();

				audio_ok.play();

			}else{
				openErrorGritter('Error', result.message);
				audio_error.play();

			}
		});
	}

	// function conf() {
	// 	$("#loading").show();

	// 	var str = $("#audit_title").text();

	// 	var lokasi = $('#lokasi').val(); 
	// 	var data = {
	// 		store : store	
	// 	}

	// 	if(confirm("Data akan disimpan oleh sistem.\nData tidak dapat dikembalikan.")){

	// 		$.post('{{ url("fetch/stocktaking/update_process_new/audit1") }}', data, function(result, status, xhr){
	// 			if (result.status) {
	// 				openSuccessGritter('Success', result.message);
	// 				$("#loading").hide();

	// 				var store = $("#qr_code").val();

	// 				fillStore(lokasi);

	// 				audio_ok.play();
					
	// 			}else{
	// 				$("#loading").hide();
	// 				openErrorGritter('Error', result.message);

	// 				audio_error.play();

	// 			}
	// 		});
	// 	}else{
	// 		$("#loading").hide();
	// 	}
	// }

	var audio_error = new Audio('{{ url("sounds/error_suara.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection