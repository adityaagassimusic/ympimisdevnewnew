@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
		vertical-align: middle;
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
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
	}
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		cursor: pointer;
		background-color: #7dfa8c;
		color: black;
		font-weight: bold;
		vertical-align: middle;
	}
	#loading, #error { display: none; }

</style>
@stop

@section('header')
<section class="content-header">

</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="margin-top: 1%;">
			<div class="box">
				<div class="box-body">
					<h2 style="margin-top: 0px;">Resin Request History</h2>
					<table id="table-material" class="table table-bordered table-hover" style="width: 100%;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 10%">Request At</th>
								<th style="width: 20%">Material</th>
								<th style="width: 40%">Material Description</th>
								<th style="width: 10%">Quantity</th>
								<th style="width: 10%">Bag</th>
								<th style="width: 10%">Status</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
						<tbody id="body-material">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="reception" style="color: black;">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h3 style="background-color: orange; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						PUT RESIN INTO STORE
					</h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" id="id" value="delivery">


								<div class="col-xs-6 col-xs-offset-3" style="padding: 0px; margin-bottom: 5%;">
									<div style="background-color: orange; text-align: center;">
										<span style="font-weight: bold; color: white; font-size: 1.5vw;">1. SCAN PIC INJEKSI</span><br>
									</div>
									<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="pic_injection" placeholder="Scan QR Code">
								</div>

								<div class="col-xs-6 col-xs-offset-3" style="padding: 0px; margin-bottom: 5%;">
									<div style="background-color: orange; text-align: center;">
										<span style="font-weight: bold; color: white; font-size: 1.5vw;">2. SCAN PIC WAREHOUSE</span><br>
									</div>
									<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="pic_warehouse" placeholder="Scan QR Code">
								</div>

								<div class="col-xs-6 col-xs-offset-3" style="padding: 0px; margin-bottom: 5%;">
									<div style="background-color: #00c0ef; text-align: center;">
										<span style="font-weight: bold; color: white; font-size: 1.5vw;">3. SCAN RESIN</span><br>
									</div>
									<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="qr_material" placeholder="Scan QR Code">
								</div>


								<div class="col-xs-6 col-xs-offset-3" style="padding: 0px; margin-bottom: 5%;">
									<div style="background-color: green; text-align: center;">
										<span style="font-weight: bold; color: white; font-size: 1.5vw;">4. SCAN STORE</span><br>
									</div>
									<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="qr_store" placeholder="Scan QR Code">
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();


		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		fetchTable();
		
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	
	$("#add_material").on("hidden.bs.modal", function () {
		$('#date').val('');
		$("#material_number").prop('selectedIndex', 0).change();
		$('#quantity').val('');		
	});

	function reception(id) {
		$('#reception').modal('show');

		$('#id').val(id);
		$('#pic_injection').val('');
		$('#pic_warehouse').val('');
		$('#qr_material').val('');
		$('#qr_store').val('');

		$('#pic_injection').focus();

	}

	$('#pic_injection').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var pic = $("#pic_injection").val()

			if(pic.length == 9){

				$('#pic_warehouse').val('');
				$('#pic_warehouse').focus();
			}else{
				openErrorGritter('Error!', 'NIK tidak valid');
				audio_error.play();
				$('#pic_injection').val('');
				$('#pic_injection').focus();
			}			
		}
	});

	$('#pic_warehouse').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var pic = $("#pic_warehouse").val()

			if(pic.length == 9){
				if($("#pic_injection").val() == $("#pic_warehouse").val()){
					openErrorGritter('Error!', 'NIK tidak boleh sama');
					audio_error.play();
					$('#pic_warehouse').val('');
					$('#pic_warehouse').focus();
				}else{
					$('#qr_material').val('');
					$('#qr_material').focus();
				}
			}else{
				openErrorGritter('Error!', 'NIK tidak valid');
				audio_error.play();
				$('#pic_warehouse').val('');
				$('#pic_warehouse').focus();
			}			
		}
	});

	$('#qr_material').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var material_number = $("#qr_material").val()

			if(material_number.length > 7){
				$('#qr_store').val('');
				$('#qr_store').focus();
			}else{
				openErrorGritter('Error!', 'Material tidak valid');
				audio_error.play();
				$('#qr_material').val('');
				$('#qr_material').focus();
			}			
		}
	});

	$('#qr_store').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var store = $("#qr_store").val()

			if(store.length == 7){
				submitReception();
			}else{
				openErrorGritter('Error!', 'Store tidak valid');
				audio_error.play();
				$('#qr_store').val('');
				$('#qr_store').focus();
			}			
		}
	});

	function submitReception() {
		var id = $('#id').val();
		var pic_injection = $('#pic_injection').val();
		var pic_warehouse = $('#pic_warehouse').val();
		var material_number = $('#qr_material').val();
		var store = $('#qr_store').val();

		if (pic_injection == '' || pic_warehouse == '' || material_number == '' || store == '') {
			openErrorGritter('Error!', 'You need to select date');
			return false;
		}

		var data = {
			id : id,
			pic_injection : pic_injection,
			pic_warehouse : pic_warehouse,
			material : material_number,
			store : store,
		}

		$("#loading").show();	

		$.post('{{ url("update/reed/injection_delivery") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);

				$('#reception').modal('hide');				
				$('#table-material').DataTable().ajax.reload();
				$("#loading").hide();

			}else{
				openErrorGritter('Error', result.message);
				$('#qr_material').val();
				$('#qr_store').val();
				$("#loading").hide();

			}
		});

	}

	function fetchTable() {

		$('#table-material').DataTable().destroy();
		$('#table-material tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});

		var table_material = $('#table-material').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/reed/injection_resin_receive") }}"
			},
			"columns": [
			{ "data": "request_at"},
			{ "data": "material_number"},
			{ "data": "material_description"},
			{ "data": "quantity"},
			{ "data": "bag_quantity"},
			{ "data": "label"},
			{ "data": "action"}
			]
		});
		
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

