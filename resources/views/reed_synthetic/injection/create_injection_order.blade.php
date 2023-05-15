@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
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
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	input[type=number] {
		-moz-appearance:textfield;
	}

	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="location" value="injection">
		<input type="hidden" id="proses" value="order">
		<div class="col-xs-4" style="padding-right: 0px;">
			<div class="box">
				<div class="box-body">
					<div class="col-xs-12">
						<h3>LIST MATERIALS :</h3>
						<table class="table table-hover table-bordered table-striped" id="tableList">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 10%;">MATERIAL</th>
									<th style="width: 50%;">DESCRIPTION</th>
								</tr>
							</thead>
							<tbody id="tableBodyList">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-8">
			<div class="box">
				<div class="box-body">
					<div class="col-xs-12" style="padding-top: 20px;">
						<table id="checksheetTable" class="table table-bordered table-hover">
							<thead style="background-color: orange;">
								<tr>
									<th style="width: 10%; vertical-align: middle;">ORDER ID</th>
									<th style="width: 10%; vertical-align: middle;">DUE DATE</th>
									<th style="width: 10%; vertical-align: middle;">MATERIAL</th>
									<th style="width: 30%; vertical-align: middle;">DESCRIPTION</th>
									<th style="width: 10%; vertical-align: middle;">QUANTITY</th>
									<th style="width: 10%; vertical-align: middle;">STATUS</th>
									<th style="width: 15%; vertical-align: middle;">PRINT ORDER</th>
									<th style="width: 10%; vertical-align: middle;">PRINT LABEL</th>
								</tr>
							</thead>
							<tbody id="checksheetTableBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="checksheetModal">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="padding: 0px;">
				<h2 style="background-color: #00a65a; text-align: center; font-weight: bold; padding-top: 1%; padding-bottom: 1%;" class="modal-title">
					CREATE INJECTION ORDER
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-2">
						<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
						<input type="text" id="material_number" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
					</div>
					<div class="col-xs-8 col-xs-offset-2">
						<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
						<input type="text" id="material_description" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
					</div>
					<div class="col-xs-8 col-xs-offset-2">
						<span style="font-weight: bold; font-size: 16px;">Due Date</span>
						<input type="text" id="due_date" placeholder="Select Due Date ..." style="width: 100%; height: 40px; font-size: 25px; text-align: center;">
					</div>
					<div class="col-xs-8 col-xs-offset-2">
						<span style="font-weight: bold; font-size: 16px;">Quantity</span>
						<input type="text" id="quantity" placeholder="Input Quantity ..." style="width: 100%; height: 40px; font-size: 25px; text-align: center;">

					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-lg btn-success" onclick="createOrder()">Submit</button>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#due_date').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('body').toggleClass("sidebar-collapse");

		clearItem();
		fetchChecksheet();
		fillTableList();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function clearItem(){
		$('#material_number').val("");
		$('#material_description').val("");
		$('#due_date').val("");
		$('#quantity').val("");
	}


	function createOrder(){

		var material_number = $('#material_number').val();
		var due_date = $('#due_date').val();
		var quantity = $('#quantity').val();
		var location = $('#location').val();


		if(due_date == '' || quantity == ''){
			openErrorGritter('Error', "Fill the blank field !");
			audio_error.play();
			return false;			
		}

		if((quantity % 96) > 0){
			openErrorGritter('Error', "Quantity are not multiples of 96 !");
			audio_error.play();
			return false;			
		}

		var data = {
			material_number : material_number,			 
			due_date : due_date,
			quantity : quantity,
			location : location 
		}

		$('#loading').show();
		$.post('{{ url("create/reed/injection_order") }}', data, function(result, status, xhr){
			if(result.status){
				
				fetchChecksheet();
				$('#loading').hide();

				clearItem();
				$('#checksheetModal').modal('hide');
				openSuccessGritter('Success', result.message);

			}
			else{
				openErrorGritter('Error', result.message);
				audio_error.play();
				$('#loading').hide();
				return false;
			}
		});
	}

	function reprintChecksheet(order_id) {
		var data = {
			order_id:order_id
		}

		if(confirm("Apakah anda yakin akan mencetak Packing Order nomor "+order_id+"?")){

			$.get('{{ url("reprint/reed/packing_order") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);	
				}else{
					openErrorGritter('Error!', result.message);	
				}
			});
		}
	}

	function fetchChecksheet(){
		$.get('{{ url("fetch/reed/injection_order") }}', function(result, status, xhr){
			if(result.status){

				var checksheetTable = "";
				$('#checksheetTable').DataTable().clear();
				$('#checksheetTable').DataTable().destroy();
				$('#checksheetTableBody').html("");

				$.each(result.checksheets, function(key, value){
					checksheetTable += '<tr>';
					checksheetTable += '<td>'+value.order_id+'</td>';
					checksheetTable += '<td>'+value.due_date+'</td>';
					checksheetTable += '<td>'+value.material_number+'</td>';
					checksheetTable += '<td>'+value.material_description+'</td>';
					checksheetTable += '<td>'+value.quantity+'</td>';
					if(value.remark == 0){
						if(value.setup_molding == 1){
							checksheetTable += '<td>';
							checksheetTable += '<span class="label label-info">Approval OK</span>';
							checksheetTable += '</td>';
						}else{
							checksheetTable += '<td>';
							checksheetTable += '<span class="label label-default">Order Created</span>';
							checksheetTable += '</td>';
						}
					}else if(value.remark == 1){
						checksheetTable += '<td>';
						checksheetTable += '<span class="label label-success">Ijection Finished</span>';
						checksheetTable += '</td>';
					}

					if(value.print != 1){
						checksheetTable += '<td>';
						checksheetTable += '<button style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-primary btn-sm" id="'+value.order_id+'" onclick="reprintChecksheet(id)"><i class="fa fa-ticket"></i>&nbsp;&nbsp;Print</button>';
						checksheetTable += '&nbsp;';
						checksheetTable += '</td>';
					}else{
						checksheetTable += '<td>';
						checksheetTable += '<button style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-info btn-sm" id="'+value.order_id+'" onclick="reprintChecksheet(id)"><i class="fa fa-ticket"></i>&nbsp;&nbsp;Re-Print</button>';
						checksheetTable += '&nbsp;';
						checksheetTable += '</td>';					
					}

					checksheetTable += '<td>';
					checksheetTable += '<button style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-danger btn-sm" id="'+value.order_id+'" onclick="printLabel(id)">';
					checksheetTable += '<i class="fa fa-tags"></i>&nbsp;&nbsp;Print';
					checksheetTable += '</button>';
					checksheetTable += '</td>';
					
					checksheetTable += '</tr>';
				});

				$('#checksheetTableBody').append(checksheetTable);

				$('#checksheetTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ -1 ],
					[ 'Show all' ]
					],
					'buttons': {
						buttons:[
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
						},
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
					"processing": true

				});


			}
			else{
				openErrorGritter('Error', result.message);
				audio_error.play();
				return false;				
			}
		});
	}

	function printLabel(order_id) {
		var data = {
			order_id:order_id
		}

		$.get('{{ url("index/reed/print_label_injection") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function reprintChecksheet(order_id) {
		var data = {
			order_id:order_id
		}

		$.get('{{ url("index/reed/print_work_order") }}', data, function(result, status, xhr){
			if(result.status){
				fetchChecksheet();
				openSuccessGritter('Success', result.message);
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
		
	}

	function fillField(material_number, description){
		clearItem();
		$('#material_number').val(material_number);
		$('#material_description').val(description);

		$('#checksheetModal').modal('show');

	}

	function fillTableList(){

		$.get('{{ url("fetch/reed/injection_material") }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			$.each(result.target, function(key, value) {
				tableData += '<tr onclick="fillField(\''+value.material_number+'\''+','+'\''+value.material_description+'\')">';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '</tr>';
			});
			$('#tableBodyList').append(tableData);


			$('#tableList').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[]
				},
				'paging': false,
				'lengthChange': true,
				'searching': false,
				'ordering': true,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true

			});

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