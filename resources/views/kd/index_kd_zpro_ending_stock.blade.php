@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#reprint" class="btn btn-default btn-sm" style="color:black; background-color: #e7e7e7;">
				&nbsp;<i class="fa fa-print"></i>&nbsp;Direct Print
			</a>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location" value="{{ $location }}">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6">
					<div class="box box-danger">
						<div class="box-body">
							<table class="table table-hover table-bordered table-striped" id="tableList">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 15%;">Due Date</th>
										<th style="width: 10%;">Material</th>
										<th style="width: 50%;">Description</th>
										<th style="width: 10%;">Target</th>
									</tr>					
								</thead>
								<tbody id="tableBodyList">
								</tbody>
								<tfoot style="background-color: rgb(252, 248, 227);">
									<tr>
										<th colspan="3" style="text-align:center;">Total:</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="row">
						<input type="hidden" id="shipment_id">
						<input type="hidden" id="production_id">
						
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Quantity:</span>
						</div>
						<div class="col-xs-6">
							<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<input type="text" id="quantity"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
							<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						{{-- <div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Destination:</span>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Stuffing Date:</span>
						</div>
						<div class="col-xs-6">
							<input type="text" id="destination"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<input type="text" id="shipment_date"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div> --}}
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-6">
									<span style="font-weight: bold; font-size: 16px;">Actual Count:</span>
									<input type="text" id="actual_count" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
								</div>
								<div class="col-xs-6" style="padding-bottom: 10px;">	
									<br>
									<button class="btn btn-primary" onclick="print()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
										CONFIRM
									</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;">PACKED LIST:</span>
							<table class="table table-hover" id="tablePack">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 5%;">No</th>
										{{-- <th style="width: 15%;">Stuffing Date</th> --}}
										<th style="width: 15%;">Material Number</th>
										<th style="width: 35%;">Description</th>
										{{-- <th style="width: 15%;">Destination</th> --}}
										<th style="width: 10%;">Quantity</th>
									</tr>					
								</thead>
								<tbody id="tableBodyPack">
								</tbody>
								<tfoot id="tableFootPack" style="background-color: rgb(252, 248, 227);">
								</tfoot>
							</table>
							<button class="btn btn-success" onclick="showPrint()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
								<i class="fa fa-print"></i> PRINT KDO NUMBER 
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">KDO Detail</a></li>
					<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="kdo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%">KD Number</th>
									<th style="width: 2%">Material Number</th>
									<th style="width: 5%">Material Description</th>
									<th style="width: 2%">Location</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 2%">Stuffing Date</th>
									<th style="width: 2%">Destination</th>
									<th style="width: 3%">Created At</th>
									<th style="width: 1%">Reprint</th>
									<th style="width: 1%">Delete</th>
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
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="tab-pane" id="tab_2">
						<table id="kdo_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">KDO</th>
									<th style="width: 1%">Count Item</th>
									<th style="width: 1%">Location</th>
									<th style="width: 1%">Created At</th>
									<th style="width: 1%">Reprint KDO</th>
									<th style="width: 1%">Cancel</th>
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
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="print_kdo_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Print KDO Number
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<h5>Are you sure print KDO Number ?</h5>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="forcePrint()"><span><i class="fa fa-print"></i> Print</span></button>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="reprint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">            
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Direct Print</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="margin-bottom: 2%;">
					<div class="col-xs-8 col-xs-offset-2">
						<input type="text" placeholder="Input GMC ..." style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 3vw" id="direct_material_number">
						<br><br>
						<input type="number" placeholder="Input Quantity ..." style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 2vw" id="direct_quantity">
						<br><br><br>
						<center>
							<button class="btn btn-lg btn-success" onclick="directPrint();">&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;</button>
						</center>
						<br><br><br>
					</div>
				</div>      
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
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		
		fillTableList();
		fillTablePack();
		fillTable();
		fillTableDetail();
	});

	$('#reprint').on('hidden.bs.modal', function () {
		$('#direct_material_number').val('');
		$("#direct_quantity").val("");
	});

	function directPrint() {
		var material_number = $('#direct_material_number').val();
		var quantity = $('#direct_quantity').val();

		if(material_number == '' || material_number.length != 7){
			openErrorGritter('Error!', 'GMC unmatch');
			$('#direct_material_number').focus();
			return false;
		}

		if(quantity == '' || quantity <= 0){
			openErrorGritter('Error!', 'Quantity unmatch');
			$('#direct_quantity').focus();
			return false;
		}

		window.open('{{ url("index/print_label_zpro_direct") }}'+'/'+material_number+'/'+quantity, '_blank');
		openSuccessGritter('Success!', "Print Success");
	}

	function showPrint() {
		$("#print_kdo_modal").modal('show');
	}

	function deleteKDODetail(id){
		if(confirm("Apa anda yakin akan menghapus data?")){
			$("#loading").show();
			var data = {
				id:id
			}
			$.post('{{ url("delete/kdo_detail") }}', data, function(result, status, xhr){
				if(result.status){
					fillTableList();
					$('#kdo_table').DataTable().ajax.reload();
					$('#kdo_detail').DataTable().ajax.reload();
					$("#loading").hide();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$("#loading").hide();				
		}
	}

	function deleteKDO(id){
		if(confirm("Apa anda yakin akan menghapus data?")){
			$("#loading").show();
			var data = {
				kd_number:id
			}
			$.post('{{ url("delete/kdo") }}', data, function(result, status, xhr){
				if(result.status){
					fillTableList();
					$('#kdo_table').DataTable().ajax.reload();
					$('#kdo_detail').DataTable().ajax.reload();
					$("#loading").hide();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$("#loading").hide();				
		}
	}

	function fillTableDetail(){
		var location = "{{ $location }}";

		var data = {
			status : 1,
			remark : location
		}

		$('#kdo_detail tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table = $('#kdo_detail').DataTable( {
			'paging'        : true,
			'dom': 'Bfrtip',
			'responsive': true,
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
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
				},
				]
			},
			'lengthChange'  : true,
			'searching'     : true,
			'ordering'      : true,
			'info'        : true,
			'order'       : [],
			'autoWidth'   : true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/kdo_detail") }}",
				"data" : data,
			},
			"columns": [
			{ "data": "kd_number" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "location" },
			{ "data": "quantity" },
			{ "data": "st_date" },
			{ "data": "destination_shortname" },
			{ "data": "updated_at" },
			{ "data": "reprintKDO" },
			{ "data": "deleteKDO" }
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

		$('#kdo_detail tfoot tr').appendTo('#kdo_detail thead');
	}

	function fillTable(){
		var location = "{{ $location }}";

		var data = {
			status : 1,
			remark : location
		}
		
		$('#kdo_table tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table = $('#kdo_table').DataTable( {
			'paging'        : true,
			'dom': 'Bfrtip',
			'responsive': true,
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
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
				},
				]
			},
			'lengthChange'  : true,
			'searching'     : true,
			'ordering'      : true,
			'info'        : true,
			'order'       : [],
			'autoWidth'   : true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/kdo") }}",
				"data" : data,
			},
			"columns": [
			{ "data": "kd_number" },
			{ "data": "actual_count" },
			{ "data": "remark" },
			{ "data": "updated_at" },
			{ "data": "reprintKDO" },
			{ "data": "deleteKDO" }
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

		$('#kdo_table tfoot tr').appendTo('#kdo_table thead');
	}

	function reprintKDO(id) {

		var data = {
			kd_number : id
		}

		$("#loading").show();

		if(confirm("Apakah anda ingin mencetak ulang KDO Number dari "+ id +" ?")){
			$.get('{{ url("fetch/kd_reprint_kdo") }}', data,  function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success', result.message);
				}else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}

			});
		}else{
			$("#loading").hide();

		}		
	}


	function reprintKDODetail(id){

		var data = id.split('+');

		var kd_detail = data[0];
		var location = data[1];

		window.open('{{ url("index/print_label_zpro") }}'+'/'+kd_detail, '_blank');
		openSuccessGritter('Success!', "Reprint Success");


	}

	function forcePrint() {
		var location = "{{ $location }}";

		var data = {
			location : location,
		}

		$("#print_kdo_modal").modal('hide');
		$("#loading").show();
		$.post('{{ url("fetch/kd_force_print_zpro") }}', data,  function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$('#actual_count').val(result.actual_count);
				fillTableList();
				fillTablePack();
				$('#kdo_table').DataTable().ajax.reload();
				$('#kdo_detail').DataTable().ajax.reload();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}

		});
	}

	function print() {
		var production_id = $("#production_id").val();
		var material_number = $("#material_number").val();
		var quantity = $("#quantity").val();
		var location = "{{ $location }}";

		var data = {
			production_id : production_id,
			material_number : material_number,
			quantity : quantity,
			location : location,
		}

		if(material_number == ''){
			alert("Material belum dipilih");
			return false;
		}


		$("#loading").show();
		$.post('{{ url("fetch/kd_print_zpro_ending_stock") }}', data,  function(result, status, xhr){
			if(result.status){
				var id = result.knock_down_detail_id;
				window.open('{{ url("index/print_label_zpro") }}'+'/'+id, '_blank');

				$('#actual_count').val('');
				$('#production_id').val('');
				$('#material_number').val('');
				$('#quantity').val('');
				$('#material_description').val('');
				$('#destination').val('');
				$('#shipment_date').val('');

				$('#actual_count').val(result.actual_count);
				fillTableList();
				fillTablePack();
				$('#kdo_table').DataTable().ajax.reload();
				$('#kdo_detail').DataTable().ajax.reload();

				$("#loading").hide();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}

		});


	}

	function fillField(param) {
		//Source Code dibawah hanya fokus mendapatkan actual count
		var location = "{{ $location }}" + '-ending-stock';

		data = {
			id: param,
			location : location,
		}

		$.get('{{ url("fetch/kd_detail") }}', data,  function(result, status, xhr){
			if(result.status){
				$('#actual_count').val(result.actual_count);

				//Fill field
				var data = param.split('_');
				var id = data[0];
				var lot_completion = data[1];

				var due_date = $('#'+param).find('td').eq(0).text();
				var material_number = $('#'+param).find('td').eq(1).text();
				var material_description = $('#'+param).find('td').eq(2).text();
				var quantity = $('#'+param).find('td').eq(3).text();

				var qty_cs = 0;
				if(quantity % lot_completion == 0){
					qty_cs = lot_completion;
				}else{
					if(quantity / lot_completion > 1){
						qty_cs = lot_completion;
					}else{
						qty_cs = quantity;
					}
				}

				$('#production_id').val(id);
				$('#due_date').val(due_date);
				$('#material_number').val(material_number);
				$('#material_description').val(material_description);
				$('#quantity').val(qty_cs);
			}
		});
	}

	function fillTablePack(){
		$.get('{{ url("fetch/kd_pack/".$location) }}',  function(result, status, xhr){
			if(result.status){
				$('#tableBodyPack').append().empty();
				$('#tableFootPack').append().empty();

				var tableData = "";
				var tableFoot = "";

				var total_qty = 0;
				var count = 0;
				$.each(result.pack, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ ++count +'</td>';
					// tableData += '<td>'+ value.st_date +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.destination_shortname +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '</tr>';
					total_qty += value.quantity;
				});
				$('#tableBodyPack').append(tableData);

				tableFoot += '<tr>';
				// tableFoot += '<th colspan="5" style="text-align:center;">Total:</th>';
				tableFoot += '<th colspan="3" style="text-align:center;">Total:</th>';
				tableFoot += '<th>'+ total_qty +'</th>';
				tableFoot += '</tr>';
				$('#tableFootPack').append(tableFoot);
			}

		});
	}


	function fillTableList(){

		$.get('{{ url("fetch/kd/".$location) }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			var total_target = 0;
			$.each(result.target, function(key, value) {
				tableData += '<tr id="'+value.id+'_'+value.lot_completion+'" onclick="fillField(id)">';
				tableData += '<td>'+ value.due_date +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.target +'</td>';
				tableData += '</tr>';
				total_target += value.target;
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
					},
					]
				},
				"footerCallback": function (tfoot, data, start, end, display) {
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$%,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					var api = this.api();
					var totalPlan = api.column(3).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(3).footer()).html(totalPlan);
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