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
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	#loading {
		display: none;
	}
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body" style="padding-top: 30px; padding-bottom: 30px;">
					<div class="row">
						<div class="col-xs-12">
							<div class="input-group col-xs-8 col-xs-offset-2">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="eo_number_sequence" placeholder="Scan EO Number Here..." required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="margin-bottom: 1%;">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Extra Order Detail</a></li>
					{{-- <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO</a></li> --}}
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="eo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%">EO No. Seq.</th>
									<th style="width: 2%">Material</th>
									<th style="width: 5%">Description</th>
									<th style="width: 2%">Location</th>
									<th style="width: 2%">Destination</th>
									<th style="width: 2%">St. Date</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 3%">Received At</th>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillTableDetail();

		$("#eo_number_sequence").focus();

	});

	var audio_error = new Audio('{{ url("sounds/error_suara.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$('#eo_number_sequence').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#eo_number_sequence").val().length > 11){
				scanDelivery();

			}else{
				openErrorGritter('Error!', "EO Number doesn't match.");
				$("#eo_number_sequence").val("");
				audio_error.play();
			}
		}
	});


	function scanDelivery(){
		var eo_number_sequence = $("#eo_number_sequence").val();
		var data = {
			eo_number_sequence : eo_number_sequence,
			status : 2
		}

		$("#loading").show();
		$.post('{{ url("input/extra_order/delivery") }}', data, function(result, status, xhr){
			if(result.status){

				$('#eo_detail').DataTable().ajax.reload();
				$("#eo_number_sequence").val("");
				$("#eo_number_sequence").focus();

				audio_ok.play();
				openSuccessGritter('Success!', result.message);
				$("#loading").hide();

			}else{
				$("#eo_number_sequence").val("");
				$("#eo_number_sequence").focus();

				audio_error.play();
				openErrorGritter('Error!', result.message);
				
				$("#loading").hide();
			}
		});
	}

	function fillTableDetail(){

		var data = {
			storage_location : $("#storage_location").val(),
			status : 2
		}

		$('#eo_detail tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table = $('#eo_detail').DataTable( {
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
				"url" : "{{ url("fetch/extra_order_detail") }}",
				"data" : data,
			},
			"columns": [
			{ "data": "eo_number_sequence" },
			{ "data": "material_number" },
			{ "data": "description" },
			{ "data": "location" },
			{ "data": "destination_shortname" },
			{ "data": "request_date" },
			{ "data": "quantity" },
			{ "data": "updated_at" },
			{ "data": "reprint" },
			{ "data": "delete" }
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

		$('#eo_detail tfoot tr').appendTo('#eo_detail thead');
	}

	function printLabel(eo_number_sequence) {
		newwindow = window.open('{{ url("index/label_extra_order/")}}'+'/'+eo_number_sequence, eo_number_sequence, 'height=550,width=650');

		if (window.focus) {
			newwindow.focus();
		}

		return false;
	}

	function reprintDetail(eo_number_sequence) {
		if(confirm("Apakah anda ingin mencetak ulang Extra Order Label dari "+ eo_number_sequence +" ?")){
			printLabel(eo_number_sequence);
		}		
	}	

	function deleteDetail(sequence){

		var message = 'List item ' + sequence + ' :\n';
		$('#eo_detail tbody tr').each(function(){
			if(sequence == $(this).find('td').eq(0).text()){
				var material_number = $(this).find('td').eq(1).text();
				var description = $(this).find('td').eq(2).text();
				var quantity = $(this).find('td').eq(6).text();
				message += material_number +  ' _ ' + description + ' _ ' + quantity +'\n';
			}
		});
		message += '\nApa anda yakin akan melakukan cancel delivery data tersebut ?';

		if(confirm(message)){
			$("#loading").show();
			var data = {
				sequence:sequence,
				status:2
			}
			$.post('{{ url("input/extra_order/cancel") }}', data, function(result, status, xhr){
				if(result.status){
					$('#eo_detail').DataTable().ajax.reload();

					$("#loading").hide();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}

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