@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #dcdcdc;
		border: 1px solid black !important;
		font-weight: bold;
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
		color: yellow;
		/*background-color: white;*/
	}
	thead {
		/*background-color: rgb(126,86,134);*/
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
		font-weight: bold;
		font-size: 20px;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Informasi Umum</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op"></td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2"></td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">KATEGORI</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<label class="radio-inline" style="font-size: 20px">
								<input type="radio" name="category_input" value="IN"><b>IN</b>
							</label>
							<label class="radio-inline" style="font-size: 20px; margin-left: 100px">
								<input type="radio" name="category_input" value="OUT"><b>OUT</b>
							</label>
						</td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">PIC</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<select class="select2" data-placeholder="Pilih PIC" style="width: 50%" id="pic_input">
								<option value=""></option>
								@foreach($pics as $pic)
								<option value="{{ $pic->employee_id }}/{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
								@endforeach
							</select>
						</td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">TANGGAL</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<center>
								<div class="input-group" style="margin-left: 40%">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" id="tanggal_input" class="form-control datepicker" style="width: 30%; text-align: center;" placeholder="Pilih Tanggal Input" value="{{ date('Y-m-d') }}">
								</div>
							</center>
						</td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">JENIS LIMBAH</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<select class="select2" data-placeholder="Pilih Jenis Limbah" style="width: 50%" id="limbah_input" onchange="check_balance()">
								<option value=""></option>
								@foreach($limbah as $waste)
								<option value="{{ $waste }}">{{ $waste }}</option>
								@endforeach
							</select>
						</td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">SISA STOK</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-weight: bold; font-size: 25px" id="stok">
							0
						</td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">JUMLAH</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<center>
								<div class="input-group" style="margin-left: 30%; margin-right: 30%">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-keyboard-o"></i>
									</div>
									<input type="text" class="form-control numpad" id="qty_input" placeholder="Input Jumlah" style="text-align: center; font-weight: bold; font-size: 22px">
									<div class="input-group-addon bg-purple" style="border: none;">
										<i class="fa fa-keyboard-o"></i>
									</div>
								</div>
							</center>
						</td>
					</tr>
				</tbody>
			</table>

			<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 25px" id="btn_check" onclick="check()"><i class="fa fa-check"></i> SIMPAN</button>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<br>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="table_history">
				<thead style="background-color: rgb(126,86,134)">
					<tr>
						<th>No</th>
						<th>Tanggal</th>
						<th>Jenis Limbah</th>
						<th>Ketegori</th>
						<th>Jumlah</th>
						<th>Sisa Stok</th>
					</tr>
				</thead>
				<tbody id="body_hostory">
				</tbody>
			</table>
		</div>
	</div>
</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var arr_item = [];
	var item_ctg = [];
	var machine_check_list = [];
	var arr_ids = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.select2').select2();
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
		});

		// if ($("input[type=radio][name=category_input]").val() == 'IN') {
		// 	$("#tanggal_input").prop('disabled', false);
		// }
		// else if ($("input[type=radio][name=category_input]").val() == 'OUT') {
		// 	$("#tanggal_input").val('{{ date("Y-m-d") }}').trigger('change');
		// 	$("#tanggal_input").prop('disabled', true)
		// }
	})

	function check_balance() {
		var elem = $("#limbah_input").val();
		if (elem == "") {
			return false;
		}

		$("#stok").text("0");

		var data = {
			limbah : elem
		}

		$.get('{{ url("fetch/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {
			if (result.history_data.length > 0) {
				$("#stok").text(result.history_data[0].remaining_stock);
			}

			$('#table_history').DataTable().clear();
			$('#table_history').DataTable().destroy();
			$("#body_hostory").empty();
			var body = '';

			$.each(result.history_data, function(index, value){
				body += '<tr>';
				body += '<td>'+(index+1)+'</td>';
				body += '<td>'+value.due_date+'</td>';
				body += '<td>'+value.waste_category+'</td>';
				body += '<td>'+value.category+'</td>';
				body += '<td>'+value.quantity+'</td>';
				body += '<td>'+value.remaining_stock+'</td>';
				body += '</tr>';
			})

			$("#body_hostory").append(body);

			var table = $('#table_history').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					{
						extend: 'pageLength',
						className: 'btn btn-default',
					},
					]
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": false,
			});
		})
	}

	$('input[type=radio][name=category_input]').change(function() {
		if (this.value == 'IN') {
			$("#tanggal_input").prop('disabled', false);
		}
		else if (this.value == 'OUT') {
			$("#tanggal_input").val('{{ date("Y-m-d") }}').trigger('change');
			$("#tanggal_input").prop('disabled', true)
		}
	});

	function check() {
		if ($('input[name=category_input]:checked').val() == 'IN') {
			var sisa_stok = parseFloat($("#stok").text()) + parseFloat($("#qty_input").val());
		} else if($('input[name=category_input]:checked').val() == 'OUT') {
			var sisa_stok = parseFloat($("#stok").text()) - parseFloat($("#qty_input").val());
		}		

		var data = {
			kategori : $('input[name=category_input]:checked').val(),
			pic : $("#pic_input").val(),
			tanggal : $("#tanggal_input").val(),
			jenis_limbah : $("#limbah_input").val(),
			jumlah : $("#qty_input").val(),
			sisa_stok : sisa_stok
		}

		$.post('{{ url("post/maintenance/wwt/waste_control") }}', data, function(result, status, xhr) {
			check_balance();
		})
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

</script>
@endsection