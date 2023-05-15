@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	tfoot>tr>th {
		text-align: center;
	}
	#tableStockMouthpiece > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableBodyStockMouthpiece > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:10px;
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered>tfoot>tr>th {
		border: 1px solid rgb(211, 211, 211);
	}

	table.table-bordered1{
		border:1px solid black;
	}
	table.table-bordered1 > thead > tr > th{
		border:1px solid black;
		font-size: 12px;
		text-align: center;
	}
	table.table-bordered1 > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
		font-size: 12px;
		text-align: center;
		padding:10px;
	}
	table.table-bordered1 > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}


	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	input[type=number] {
		-moz-appearance:textfield;
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	#loading { display: none; }
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-5">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<center>
							<h3 style="background-color: #ffd8b7; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
								<i class="fa fa-angle-double-down"></i> LIST MATERIAL {{$loc}} <i class="fa fa-angle-double-down"></i>
							</h3>
						</center>
						<div class="row">
							<div class="col-md-12" align="center">
								<table class="table table-hover table-striped table-bordered" id="tableStockMouthpiece" style="width: 100%;" >
									<thead style="background-color: rgb(126,86,134); color: #FFD700;">
										<tr>
											<!-- <th style="width: 10%;">#</th> -->
											<th style="width: 10%;">GMC</th>
											<th style="width: 30%;">MATERIAL DESCRIPTION</th>
											<th style="width: 10%;">STORAGE LOCATION</th>
											<th style="width: 5%;">UOM</th>
											<th style="width: 15%;">QTY STOCK</th>
											<!-- <th style="width: 10%;">#</th> -->
										</tr>					
									</thead>
									<tbody id="tableBodyStockMouthpiece" style="background-color: white">
									</tbody>
									<tfoot style="background-color: rgb(252, 248, 227);">
										<tr><th colspan="4" style="text-align:center;" rowspan="1">Total:</th><th rowspan="1" colspan="1" id="total">0</th></tr>
									</tfoot>				
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-7">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<center>
							<h3 style="background-color: #ffd8b7; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
								<i class="fa fa-angle-double-down"></i> DETAIL MATERIAL <i class="fa fa-angle-double-down"></i>
							</h3>
						</center>
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="row">
									<!-- <div class="col-xs-12">
										<label style="font-weight: bold; font-size: 16px;">Tag Kanban : </label>
										<input type="text" id="tag_kanban" style="width: 100%; font-size: 20px; text-align: center; color: black" disabled>
									</div> -->
									<div class="col-xs-6">
										<label style="font-weight: bold; font-size: 16px">GMC : </label>
										<input type="text" id="gmc" style="width: 100%; font-size: 20px; text-align: center" disabled>
									</div>
									<div class="col-xs-6">
										<label style="font-weight: bold; font-size: 16px;">To Location : </label>
										<input type="text" id="loc" style="width: 100%; font-size: 20px; text-align: center" value="{{$loc}}" disabled>
									</div>
									<div class="col-xs-12">
										<label style="font-weight: bold; font-size: 16px;">Material Description : </label>
										<input type="text" id="desc" style="width: 100%; font-size: 20px; text-align: center" disabled>
									</div>

									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-3">
												<label style="font-weight: bold; font-size: 16px;">UOM : </label>
												<input type="text" id="uom" style="width: 100%; font-size: 20px; text-align: center" disabled value="">
												<input type="hidden" id="mrpc">
											</div>
											
											<div class="col-xs-2">
												<label style="font-weight: bold; font-size: 16px; color: red;">QTY Request : </label>
												<input class="form-control numpad" type="number" id="quantity" style="width: 100%; font-size: 20px; text-align: center; color: red" value="0" onchange="CekQty(this.value)">
												<input type="hidden" id="qty_avail">
											</div>

											<div class="col-xs-4">
												<label style="font-weight: bold; font-size: 16px; color: red;">PIC : </label>
												<select class="form-control select2" id="pic" style="width: 100%; font-size: 20px; text-align: center; color: red" required data-placeholder="Pilih Karyawan">
													<option value="">&nbsp;</option>
													@foreach($emp as $emp)
													<option value="{{$emp->employee_id}}/{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
													@endforeach
												</select>
											</div>

											<div class="col-xs-3" style="padding-bottom: 10px; padding-top: 28px;">
												<button class="btn btn-primary" onclick="SaveRequest()" style="font-size: 23px; width: 100%; font-weight: bold; padding: 0;" id="btn_print"><i class="fa fa-print" aria-hidden="true"></i> PRINT SLIP</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<center>
							<h3 style="background-color: #ffd8b7; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
								<i class="fa fa-angle-double-down"></i> LIST STATUS MATERIAL <i class="fa fa-angle-double-down"></i>
							</h3>
						</center>
						<div class="row">
							<div class="col-md-12" align="center">
								<table class="table table-hover table-striped table-bordered" id="tableListStatus" style="width: 100%;" >
									<thead style="background-color: rgb(126,86,134); color: #FFD700;">
										<tr>
											<th style="width: 10%;">NO</th>
											<th style="width: 10%;">GMC</th>
											<th style="width: 30%;">MATERIAL DESCRIPTION</th>
											<th style="width: 10%;">TO LOC</th>
											<th style="width: 10%;">UOM</th>
											<th style="width: 15%;">PIC</th>
											<th style="width: 15%;">STATUS</th>
										</tr>					
									</thead>
									<tbody id="tableBodyListStatus" style="background-color: white">
									</tbody>				
								</table>
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

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	// var location = '{{$loc}}';

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		FetchStock();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('.select2').select2({
			// allowClear : true
		});
		setInterval(FetchStatusList, 5000);
		// $('#btn_print').hide();
	});

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

	function PilihPIC(){
		$('#btn_print').show();
	}

	function CekQty(value){
		var qty_avail = $('#qty_avail').val();
		var qty_request = value;

		if (parseInt(qty_request) > parseInt(qty_avail)) {
			if(confirm("QTY request melebihi stock yang ada.")){
				$('#quantity').val('');
			}
			else{
				return false;
			}
		}
	}

	function FetchStock(){
		var loc = $('#loc').val();

		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/mouthpiece/stock")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableStockMouthpiece').DataTable().clear();
				$('#tableStockMouthpiece').DataTable().destroy();
				var tableData = '';
				$('#tableBodyStockMouthpiece').html("");
				$('#tableBodyStockMouthpiece').empty();
				var jml = 0;
				
				var count = 1;
				$.each(result.resume, function(key, value) {

					tableData += '<tr onclick="DetailRequest(\''+value.gmc+'\', \''+value.desc+'\', \''+value.issue+'\', \''+value.uom+'\', \''+value.qty+'\')">';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.desc +'</td>';
					tableData += '<td>'+ value.issue +'</td>';
					tableData += '<td>'+ value.uom +'</td>';
					tableData += '<td>'+value.qty+'</td>';
					tableData += '</tr>';

					count += 1;
					jml += Number(value.qty);
				});

				$('#total').text(jml);

				$('#tableBodyStockMouthpiece').append(tableData);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function FetchStatusList(){
		var loc = $('#loc').val();

		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/mouthpiece/stock")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableListStatus').DataTable().clear();
				$('#tableListStatus').DataTable().destroy();
				var tableData = '';
				$('#tableBodyListStatus').html("");
				$('#tableBodyListStatus').empty();
				var jml = 0;
				
				var count = 1;
				$.each(result.list, function(key, value) {

					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.desc +'</td>';
					tableData += '<td>'+ value.issue +'</td>';
					tableData += '<td>'+value.qty+' '+ value.uom +'</td>';
					tableData += '<td>'+value.created_by+'</td>';
					if (value.packing == 'onprogress') {
						tableData += '<td style="text-align: center"><span class="label label-warning" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Material Sedang Diproses</span></td>';	
					}else if (value.packing == 'finished') {
						tableData += '<td style="text-align: center"><span class="label label-success" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Material Selesai Diproses</span></td>';	
					}else{
						tableData += '<td><span class="label label-info" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Request Belum DIterima</span></td>';
					}
					tableData += '</tr>';

					count += 1;
				});

				$('#tableBodyListStatus').append(tableData);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function DetailRequest(gmc, desc, issue, uom, qty){
		$('#gmc').val(gmc);
		$('#issue').val(issue);
		$('#desc').val(desc);
		$('#uom').val(uom);
		$('#qty_avail').val(qty);
	}

	function SaveRequest(){
		var gmc = $('#gmc').val();
		var loc = $('#loc').val();
		var desc = $('#desc').val();
		var uom = $('#uom').val();
		var quantity = $('#quantity').val();
		var pic = $('#pic').val();

		var data = {
			gmc:gmc,
			loc:loc,
			desc:desc,
			uom:uom,
			quantity:quantity,
			pic:pic	
		}

		if (gmc == '' || desc == '' || loc == '' || uom == '' || quantity == '' || pic == '') {
			confirm("Isi data dengan lengkap.");
		}else{
			if(confirm("Apakah anda yakin untuk melanjutkan request ini?")){
				$('#loading').show();
				$.post('{{ url("save/request/mouthpiece") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success', 'Slip request berhasil di cetak.');
						location.reload();
						audio_ok.play();
						$('#gmc').val('');
						$('#desc').val('');
						$('#uom').val('');
						$('#quantity').val(0);
						$('#pic').val('').trigger('change');
						FetchStock();
						$('#btn_print').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', 'Gagal melakukan request.	');
					}
				});
			}
		}
	}
</script>
@endsection