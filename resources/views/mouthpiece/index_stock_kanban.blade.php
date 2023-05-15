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
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableBodyResume > tr:hover {
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
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-8 pull-left">
			<div style="font-weight: bold; padding: 0%; margin-top: 0%; margin-bottom: 1%; color: black">
				<i class="fa fa-angle-double-down"></i> <span id="last_update"></span> <i class="fa fa-angle-double-down"></i>
			</div>
		</div>

		<div class="col-xs-5" style="padding-top: 80px">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<center>
							<h3 style="background-color: #ffd8b7; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
								<i class="fa fa-angle-double-down"></i> SCAN KANBAN <i class="fa fa-angle-double-down"></i>
							</h3>
						</center>
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-barcode" style="size: 34px"></i>
								</div>
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input pattern="\d*" maxlength="10" type="text" style="text-align: center; font-size: 30px; height: 75px" class="form-control" id="kanban" placeholder="Tap Kanban Disini ..." required onchange="ScanKanban(this.value)">
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-ok"></i>
								</div>
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
								<i class="fa fa-angle-double-down"></i> DETAIL KANBAN <i class="fa fa-angle-double-down"></i>
							</h3>
						</center>
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="row">
									<div class="col-xs-12">
										<label style="font-weight: bold; font-size: 16px;">Tag Kanban : </label>
										<input type="text" id="tag_kanban" style="width: 100%; font-size: 20px; text-align: center; color: black" disabled>
									</div>
									<div class="col-xs-6">
										<label style="font-weight: bold; font-size: 16px; color: red;">GMC : </label>
										<select class="form-control select2" id="gmc" style="width: 100%; font-size: 20px; text-align: center; color: red" required data-placeholder="Pilih GMC" onChange="SelectGMC(this.value)">
											<option value="">&nbsp;</option>
											@foreach($mpdl as $mpdl)
											<option value="{{$mpdl->material_number}}/{{$mpdl->material_description}}/{{$mpdl->storage_location}}/{{$mpdl->bun}}/{{$mpdl->mrpc}}"> {{$mpdl->material_number}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-xs-6">
										<label style="font-weight: bold; font-size: 16px;">Issue Location : </label>
										<input type="text" id="issue" style="width: 100%; font-size: 20px; text-align: center" disabled>
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
											
											<div class="col-xs-3">
												<label style="font-weight: bold; font-size: 16px; color: red;">QTY CS : </label>
												<input class="form-control numpad" type="number" id="quantity" style="width: 100%; font-size: 20px; text-align: center; color: red" value="0">
											</div>

											<div class="col-xs-3">
												<label style="font-weight: bold; font-size: 16px; color: red;">No Kanban : </label>
												<input class="form-control numpad" type="number" id="no_kanban" style="width: 100%; font-size: 20px; text-align: center; color: red" value="0">
											</div>

											<div class="col-xs-3" style="padding-bottom: 10px; padding-top: 28px;">
												<button class="btn btn-primary" onclick="SaveKanban()" style="font-size: 23px; width: 100%; font-weight: bold; padding: 0;"><i class="fa fa-floppy-o" aria-hidden="true"></i> SAVE</button>
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
								<i class="fa fa-angle-double-down"></i> LIST KANBAN MOUTHPIECE <i class="fa fa-angle-double-down"></i>
							</h3>
						</center>
						<div class="row">
							<div class="col-md-12" align="center">
								<table class="table table-hover table-striped table-bordered" id="tableListStock" style="width: 100%;" >
									<thead style="background-color: rgb(126,86,134); color: #FFD700;">
										<tr>
											<th style="width: 10%;">#</th>
											<th style="width: 10%;">GMC</th>
											<th style="width: 40%;">MATERIAL DESCRIPTION</th>
											<th style="width: 10%;">STORAGE LOCATION</th>
											<th style="width: 10%;">QTY CS</th>
											<th style="width: 10%;">UOM</th>
											<th style="width: 10%;">NO KANBAN</th>
										</tr>					
									</thead>
									<tbody id="tableBodyListStock">
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

<div class="modal fade" id="ModalDetailKanban" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #ccff90; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
						<i class="fa fa-angle-double-down"></i> DETAIL KANBAN MOUTHPIECE <i class="fa fa-angle-double-down"></i>
					</h3>
				</center>
				<div class="col-xs-6" align="center">
					<label style="font-weight: bold; font-size: 16px;">No Kanban : </label>
					<input type="text" id="no_kanban_update" style="width: 100%; font-size: 20px; text-align: center; color: black; background-color: #8dcbf3" disabled>
				</div>
				<div class="col-xs-6" align="center">
					<label style="font-weight: bold; font-size: 16px;">Tag Kanban : </label>
					<input type="text" id="tag_kanban_update" style="width: 100%; font-size: 20px; text-align: center; color: black; background-color: #f3d08d" disabled>
				</div>
				<div class="col-xs-6">
					<div class="row" style="margin:0px;">
						<div class="box-body">
							<center>
								<h3 style="background-color: #e88a81; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
									<i class="fa fa-angle-double-down"></i> DETAIL KANBAN BEFORE <i class="fa fa-angle-double-down"></i>
								</h3>
							</center>
							<div class="row">
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;">GMC : </label>
									<input type="text" id="gmc_before" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> Material Description : </label>
									<input type="text" id="desc_before" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> Issue Location : </label>
									<input type="text" id="issue_before" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> QTY CS : </label>
									<input type="text" id="quantity_before" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> UOM : </label>
									<input type="text" id="uom_before" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="row" style="margin:0px;">
						<div class="box-body">
							<center>
								<h3 style="background-color: #ecf176; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
									<i class="fa fa-angle-double-down"></i> DETAIL KANBAN AFTER <i class="fa fa-angle-double-down"></i>
								</h3>
							</center>
							<div class="row">
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;">GMC : </label>
									<select class="form-control select3" id="gmc_after" style="width: 100%; font-size: 20px; text-align: center; color: red" required data-placeholder="Pilih GMC" onChange="SelectGMCUpdate(this.value)">
										<option value="">&nbsp;</option>
										@foreach($mpdl2 as $mpdl2)
										<option value="{{$mpdl2->material_number}}/{{$mpdl2->material_description}}/{{$mpdl2->storage_location}}/{{$mpdl2->bun}}"> {{$mpdl2->material_number}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> Material Description : </label>
									<input type="text" id="desc_after" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> Issue Location : </label>
									<input type="text" id="issue_after" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> QTY CS : </label>
									<input type="text" class="form-control numpad" id="quantity_after" style="width: 100%; font-size: 20px; text-align: center; color: red">
								</div>
								<div class="col-md-12" align="center">
									<label style="font-weight: bold; font-size: 16px;"> UOM : </label>
									<input type="text" id="uom_after" style="width: 100%; font-size: 20px; text-align: center; color: red" disabled>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-4">
						<button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1vw; width: 100%;"> <i class="fa fa-times" aria-hidden="true"></i> CLOSE</button>
					</div>
					<div class="col-xs-4">
						<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="DeleteKanban()" style="font-weight: bold; font-size: 1vw; width: 100%;"><i class="fa fa-trash" aria-hidden="true"></i> DELETE KANBAN</button>
					</div>
					<div class="col-xs-4">
						<button type="button" class="btn btn-success" data-dismiss="modal" onclick="SimpanPerubahan()" style="font-weight: bold; font-size: 1vw; width: 100%;"><i class="fa fa-floppy-o" aria-hidden="true"></i> SAVE</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="alert alert-danger alert-dismissible">
					<h4><i class="icon fa fa-ban"></i> Error!</h4>
					Kanban sudah terdaftar!<br>
					Klik UPDATE untuk merubah detail Kanban, atau klik CLOSE untuk mengabaikan.
				</div>   
			</div>
			<div class="modal-footer">
				<div class="col-xs-12">
					<div class="col-xs-4 pull-left">
						<button type="button" class="btn btn-warning" data-dismiss="modal" style="font-weight: bold; font-size: 1vw; width: 100%;"> <i class="fa fa-times" aria-hidden="true"></i> CLOSE</button>
					</div>
					<div class="col-xs-4 pull-right">
						<button type="button" class="btn btn-success" data-dismiss="modal" style="font-weight: bold; font-size: 1vw; width: 100%;" onclick="EditDetailKanban()"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> UPDATE</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')

<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#kanban").focus();
		$('.select2').select2({
			allowClear : true
		});
		$('.select3').select2({
			allowClear : true,
			dropdownParent: $('#ModalDetailKanban'),
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		UpdatedNow();
		FetchListStock();
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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function DateNow(){
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		return year + "-" + month + "-" + day;
	}

	function UpdatedNow(){
		$('#last_update').html('<i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'');
	}

	function ScanKanban(value){
		$('#tag_kanban_update').val(value.toUpperCase());
		var data = {
			value:value
		}
		$.get('{{ url("cek/stock/kanban") }}', data, function(result, status, xhr){
			if(result.kanban == 0){
				$('#tag_kanban').val(value.toUpperCase());
				confirm("Selanjutnya, pilih GMC, dan lengkapi data detail kanban, lalu tekan save.")
				$('#kanban').val('');
				$("#kanban").focus();
			}else{
				$("#ModalSelect").modal('show');
				$("#kanban").val('');
				$("#kanban").focus();
			}
		});	
	}

	function SelectGMC(value) {
		var data = value.split("/");

		$('#desc').val(data[1]);
		$('#issue').val(data[2]);
		$('#uom').val(data[3]);
		$('#mrpc').val(data[4]);
	}

	function SaveKanban(){
		var data = $('#gmc').val().split("/");
		if (data == '' || $('#quantity').val() == '0') {
			confirm("Data tidak bisa disimpan, lengkapi detail kanban dahulu, lalu klik SAVE.")
		}else{
			$('#loading').show();
			var gmc = data[0];
			var desc = data[1];
			var issue = data[2];
			var uom = data[3];
			var mrpc = data[4];
			var qty = $('#quantity').val();
			var kanban = $('#tag_kanban').val();
			var no_kanban = $('#no_kanban').val();

			var data = {
				gmc:gmc,
				desc:desc,
				issue:issue,
				uom:uom,
				mrpc:mrpc,
				qty:qty,
				kanban:kanban,
				no_kanban:no_kanban
			}
			$.post('{{ url("save/stock/kanban") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#gmc').val('').trigger('change');
					$('#tag_kanban').val('');
					$('#no_kanban').val(0);
					$('#desc').val('');
					$('#issue').val('');
					$('#uom').val('');
					$('#mrpc').val('');
					$('#quantity').val(0);
					$("#kanban").focus();
					openSuccessGritter('Success!', 'Data kanban berhasil disimpan.');
					FetchListStock();
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
	}

	function FetchListStock(){
		$.get('<?php echo e(url("cek/stock/kanban")); ?>', function(result, status, xhr){
			if(result.status){

				$('#tableListStock').DataTable().clear();
				$('#tableListStock').DataTable().destroy();
				var tableData = '';
				$('#tableBodyListStock').html("");
				$('#tableBodyListStock').empty();
				
				var count = 1;
				$.each(result.resume, function(key, value) {

					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.desc +'</td>';
					tableData += '<td>'+ value.issue +'</td>';
					tableData += '<td>'+ value.qty +'</td>';
					tableData += '<td>'+ value.uom +'</td>';
					tableData += '<td> No Kanban '+ value.no_kanban +'</td>';
					tableData += '</tr>';

					count += 1;
				});

				$('#tableBodyListStock').append(tableData);
				var tableList = $('#tableListStock').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	
	function EditDetailKanban(){
		$("#ModalDetailKanban").modal('show');
		var tag_update = $("#tag_kanban_update").val();
		var data = {
			tag:tag_update
		}
		$.get('{{ url("cek/stock/kanban") }}', data, function(result, status, xhr){
			if(result.status){
				$("#no_kanban_update").val(result.detail_kanban.no_kanban);
				$("#gmc_before").val(result.detail_kanban.gmc);
				$("#desc_before").val(result.detail_kanban.desc);
				$("#issue_before").val(result.detail_kanban.issue);
				$("#uom_before").val(result.detail_kanban.uom);
				$("#quantity_before").val(result.detail_kanban.qty);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function SelectGMCUpdate(value){
		var data = value.split('/');
		$("#desc_after").val(data[1]);
		$("#issue_after").val(data[2]);
		$("#uom_after").val(data[3]);
		$("#quantity_after").val(0);
	}

	function SimpanPerubahan(){
		var tag = $("#tag_kanban_update").val()
		var gmc = $("#gmc_after").val();
		var desc = $("#desc_after").val();
		var issue = $("#issue_after").val();
		var uom = $("#uom_after").val();
		var qty = $("#quantity_after").val();

		var data_gmc = gmc.split('/');
		console.log(data_gmc);

		var data = {
			tag:tag,
			gmc:data_gmc[0],
			desc:data_gmc[1],
			issue:data_gmc[2],
			uom:data_gmc[3],
			qty:qty
		}
		$.post('{{ url("update/stock/kanban") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Data kanban berhasil diupdate.');
				FetchListStock();
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function DeleteKanban(){
		var tag = $("#tag_kanban_update").val()
		var data = {
			tag:tag
		}
		$.post('{{ url("delete/stock/kanban") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Data kanban berhasil diupdate.');
				FetchListStock();
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
</script>
@endsection