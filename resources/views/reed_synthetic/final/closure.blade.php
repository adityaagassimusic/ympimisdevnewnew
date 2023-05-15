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
		/*display: inline-block;*/
		overflow: block;
		text-overflow: ellipsis;
		white-space: nowrap;

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
				<div class="col-xs-12" style="text-align: center;">
					<div class="input-group col-md-8 col-md-offset-2">
						<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: green;">
							<i class="glyphicon glyphicon-qrcode"></i>
						</div>
						<input type="text" style="text-align: center; border-color: green; font-size: 3vw; height: 70px" class="form-control" id="kdo_number_closure" name="kdo_number_closure" placeholder="Scan KDO Number" required>
						<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: green;">
							<i class="glyphicon glyphicon-qrcode"></i>
						</div>
					</div>
					<br>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Detail</a></li>
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
									<th style="width: 3%">Created At</th>
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
		$("#kdo_number_closure").focus();
		$('#export_plan').css({"background-color":"inherit"});

		fillTableDetail();

	});

	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$('#kdo_number_closure').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#kdo_number_closure").val().length >= 10){
				scanKdoClosure();
			}else{
				openErrorGritter('Error!', 'Nomor KDO tidak sesuai.');
				$("#kdo_number_closure").val("");
				$("#kdo_number_closure").focus();
				audio_error.play();
			}
		}
	});


	function scanKdoClosure() {
		var kd_number = $("#kdo_number_closure").val();
		var location = '{{ $location }}';

		data = {
			kd_number : kd_number,
			location : location
		}

		$("#loading").show();

		$.post('{{ url("scan/reed/closure") }}', data, function(result, status, xhr){
			if(result.status){
				$("#kdo_number_closure").val("");
				$("#kdo_number_closure").focus();

				$('#kdo_detail').DataTable().ajax.reload();

				audio_ok.play();
				$("#loading").hide();
				openSuccessGritter('Success!', result.message);


			}else{
				audio_error.play();
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
				
				$("#kdo_number_closure").val("");
				$("#kdo_number_closure").focus();
			}
		});
		
	}



	function printLabelSubassy(kd_detail,windowName) {
		newwindow = window.open('{{ url("index/print_label_subassy/") }}'+'/'+kd_detail, windowName, 'height=250,width=450');

		if (window.focus) {
			newwindow.focus();
		}

		return false;
	}

	function reprintKDODetail(id){
		var data = id.split('+');
		var kd_detail = data[0];
		var location = "{{ $location }}";

		printLabelSubassy(kd_detail, ('reprint'+kd_detail));
		openSuccessGritter('Success!', "Reprint Success");
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
					$('#kdo_detail').DataTable().ajax.reload();
					$('#kdo_closure').DataTable().ajax.reload();
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

		var data = {
			status : 1,
			remark : "{{ $location }}"
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
			{ "data": "updated_at" }
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