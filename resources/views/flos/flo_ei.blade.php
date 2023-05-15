@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style>
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	td:hover {
		overflow: visible;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	input[type=number]::-webkit-outer-spin-button,`
	input[type=number]::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>Educational Instrument <span class="text-purple">教育管楽器</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li>
	</ol>
</section>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">Fulfillment <span class="text-purple">FLO充足</span></h3>
				</div>
				<!-- /.box-header -->
				<form class="form-horizontal" role="form" method="post" action="{{url('print/flo')}}">
					<div class="box-body">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="box-body">
							<div class="row">
								<div class="col-md-3">
									<div class="input-group col-md-12">
										<label id="labelYMJ">
											<input type="checkbox" class="flat-red" id="ymj">
											<span class="fa fa-caret-left text-red">&nbsp; Check if product for YMJ.</span>
										</label>
									</div>
									&nbsp;
									<div class="input-group col-md-12">
										<div class="input-group-addon" id="icon-material">
											<i class="glyphicon glyphicon-barcode"></i>
										</div>
										<input type="text" style="text-align: center" class="form-control" id="material_number" name="material_number" placeholder="Material Number" required>
									</div>
									{{-- &nbsp;
									<div class="input-group col-md-12">
										<div class="input-group-addon" id="icon-serial">
											Qty
										</div>
										<input type="number" style="text-align: center" class="form-control" id="quantity" name="quantity" placeholder="Quantity" required>
									</div> --}}
								</div>
								<div class="col-md-9">
									<div class="input-group col-md-8 col-md-offset-2">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											FLO
										</div>
										<input type="text" style="text-align: center; font-size: 22" class="form-control" id="flo_number" name="flo_number" placeholder="Not Available" required>
										<div class="input-group-addon" id="icon-serial">
											<i class="glyphicon glyphicon-lock"></i>
										</div>
									</div>
									&nbsp;
									<table id="flo_detail_table" class="table table-bordered table-striped">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												{{-- <th>#</th> --}}
												<th>Serial</th>
												<th>Material</th>
												<th>Description</th>
												<th>Qty</th>
												<th>Del.</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<!-- /.box-body -->
					</div>
				</form>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>

		<div class="col-xs-12">
			<div class="box box-success">
				<div class="box-header">
					<h3 class="box-title">Closure <span class="text-purple">FLO完了</span></h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-12">
							<div class="input-group col-md-8 col-md-offset-2">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="flo_number_settlement" name="flo_number_settlement" placeholder="Scan FLO Here..." required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-ok"></i>
								</div>
							</div>
							<br>
							<table id="flo_table" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 5%">FLO</th>
										<th style="width: 10%">Dest.</th>
										<th style="width: 10%">Ship. Date</th>
										<th style="width: 5%">By</th>
										<th style="width: 5%">Material</th>
										<th style="width: 35%">Description</th>
										<th style="width: 10%">Qty</th>
										<th style="width: 5%">Cancel</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
	<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="titleModal">Reprint FLO</h4>
				</div>
				<form class="form-horizontal" role="form" method="post" action="{{url('reprint/flo')}}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body" id="messageModal">
						<label>FLO Number</label>
						<select class="form-control select2" name="flo_number_reprint" style="width: 100%;" data-placeholder="Choose a FLO..." id="flo_number_reprint" required>
							<option value=""></option>
							@foreach($flos as $flo)
							<option value="{{ $flo->flo_number }}">{{ $flo->flo_number }} || {{ $flo->shipmentschedule->material_number }} || {{ $flo->shipmentschedule->material->material_description }}</option>
							@endforeach
						</select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="modalReprintButton" type="submit" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</section>


@stop
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>


	jQuery(document).ready(function() {
		$(function () {
			$('.select2').select2()
		});

		$(document).on("wheel", "input[type=number]", function (e) {
			$(this).blur();
		})

		$("#ymj").prop('checked', false);
		$('input[type="checkbox"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-red',
			radioClass   : 'iradio_flat-red'
		});

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		// if($('#flo_number').val() != ""){
		// 	$('#flo_detail_table').DataTable().destroy();
		// 	fillFloTable($("#flo_number").val());
		// }

		$('#flo_table').DataTable().destroy();
		fillFloTableSettlement();

		refresh();

		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();

		$("#material_number").on("input", function() {
			delay(function(){
				if ($("#material_number").val().length < 7) {
					$("#material_number").val("");
				}
			}, 200 );
		});

		$("#flo_number_settlement").on("input", function() {
			delay(function(){
				if ($("#flo_number_settlement").val().length < 7) {
					$("#flo_number_settlement").val("");
				}
			}, 200 );
		});

		$('#material_number').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#material_number").val().length == 7){
					scanMaterialNumber();
					return false;
				}
				else{
					openErrorGritter('Error!', 'Material number invalid.');
					audio_error.play();
					$("#material_number").val("");
				}
			}
		});

		$('#flo_number_settlement').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#flo_number_settlement").val().length > 7){
					scanFloNumber();
					return false;
				}
				else{
					openErrorGritter('Error!', 'FLO number invalid.');
					audio_error.play();
					$("#flo_number_settlement").val("");
				}
			}
		});

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function scanMaterialNumber(){
		$('#material_number').prop('disabled', true);
		var material_number = $("#material_number").val();
		var data = {
			material_number : material_number,
		}
		$.post('{{ url("scan/educational_instrument") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$("#material_number").val("");
					$("#flo_number").val(result.flo_number);
					$('#flo_detail_table').DataTable().destroy();
					fillFloTable(result.flo_number);
					$('#material_number').prop('disabled', false);
					$("#material_number").focus();
					
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$('#material_number').prop('disabled', false);
					$("#material_number").val("");
				}
			}
			else{
				openErrorGritter('Error!', 'Disconnected from server');
				audio_error.play();
				$('#material_number').prop('disabled', false);
				$("#material_number").val("");
			}
		});
	}

	function scanFloNumber(){
		$("#flo_number_settlement").prop("disabled", true);
		var flo_number = $("#flo_number_settlement").val();
		var data = {
			flo_number : flo_number,
			status : '1',
		}
		$.post('{{ url("scan/flo_settlement") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#flo_table').DataTable().ajax.reload();
					$("#flo_number_settlement").val("");
					$('#flo_detail_table').DataTable().destroy();
					fillFloTable($("#flo_number").val());
					$('#flo_number').val("");
					refresh();
					$("#flo_number_settlement").prop("disabled", false);
					$("#flo_number_settlement").focus();
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#flo_number_settlement").prop("disabled", false);
					$("#flo_number_settlement").val("");
				}
			}
			else{
				openErrorGritter('Error!', 'Disconnected from server');
				audio_error.play();
				$("#flo_number_settlement").prop("disabled", false);
				$("#flo_number_settlement").val("");
			}
		});
	}

	function fillFloTable(flo_number){
		var index_flo_number = flo_number;
		var data_flo = {
			flo_number : index_flo_number
		}
		$('#flo_detail_table').DataTable( {
			"sDom": '<"top"i>rt<"bottom"flp><"clear">',
			'paging'      	: false,
			'lengthChange'	: false,
			'searching'   	: false,
			'ordering'    	: false,
			'info'       	: true,
			'autoWidth'		: false,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"infoCallback": function( settings, start, end, max, total, pre ) {
				return "<b>Total "+ total +" pc(s)</b>";
			},
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "post",
				"url" : "{{ url("index/flo_detail") }}",
				"data": data_flo
			},
			"columns": [
			// { "data": "id",
			// render: function (data, type, row, meta) {
			// 	return meta.row + meta.settings._iDisplayStart + 1;
			// }, "sWidth": "2%" },
			{ "data": "serial_number", "sWidth": "14%" },
			{ "data": "material_number", "sWidth": "12%" },
			{ "data": "material_description", "sWidth": "62%" },
			{ "data": "quantity", "sWidth": "5%" },
			{ "data": "action", "sWidth": "4%" }
			]
		});
	}

	function fillFloTableSettlement(){
		var data = {
			status : '1',
			originGroup : ['027','072','073'],
		}

		$('#flo_table tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table = $('#flo_table').DataTable( {
			'paging'      	: true,
			'lengthChange'	: true,
			'searching'   	: true,
			'order'       : [],
			'ordering'    	: true,
			'info'       	: true,
			'autoWidth'		: true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "post",
				"url" : "{{ url("index/flo") }}",
				"data" : data,
			},
			"columns": [
			{ "data": "flo_number" },
			{ "data": "destination_shortname" },
			{ "data": "st_date" },
			{ "data": "shipment_condition_name" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "actual" },
			{ "data": "action" }
			]
		});
		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});

		$('#flo_table tfoot tr').appendTo('#flo_table thead');
	}

	function cancelConfirmation(id){
		var flo_number = $("#flo_number_settlement").val(); 
		var data = {
			id: id,
			flo_number : flo_number,
			status : '1',
		};
		if(confirm("Are you sure you want to cancel this settlement?")){
			$.post('{{ url("cancel/flo_settlement") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#flo_table').DataTable().ajax.reload();
						$("#flo_number_settlement").val("");
						$("#flo_number_settlement").focus();					
					}
					else{
						openErrorGritter('Error!', result.message);
						audio_error.play();
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected from server');
					audio_error.play();
				}
			});
		}
		else{
			return false;
		}
	}

	function deleteConfirmation(id){
		alert("Delete harus konfirmasi inputor/admin");
		return false;
		var flo_number = $("#flo_number").val(); 
		var data = {
			id: id,
			flo_number : flo_number
		};
		if(confirm("Are you sure you want to delete this data?")){
			$.post('{{ url("destroy/serial_number") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);

				if(xhr.status == 200){
					if(result.status){
						$('#flo_detail_table').DataTable().ajax.reload();
						$("#serial_number").prop('disabled', true);
						$("#material_number").prop('disabled', false);
						$("#serial_number").val("");
						$("#material_number").val("");
						$("#material_number").focus();
						openSuccessGritter('Success!', result.message);
					}
					else{
						openErrorGritter('Error!', result.message);
						audio_error.play();
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected from server');
					audio_error.play();
				}
			});
		}
		else{
			return false;
		}
	}

	function refresh(){
		$('#labelYMJ').hide();
		$("#flo_number_reprint").val("").change();
		$('#quantity').prop('disabled', true);
		$('#flo_number').prop('disabled', true);
		// $('#quantity').val('');
		$('#flo_number').val('');
		$('#material_number').val('');
		$('#flo_number_settlement').val('');
		$("#material_number").prop('disabled', false);
		$("#material_number").focus();
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}


</script>
@stop