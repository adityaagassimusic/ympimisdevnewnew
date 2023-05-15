@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-right: 7.5px;">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<form role="form">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-8 col-xs-offset-2">
									
									<div class="col-xs-4">
										<div class="form-group">
											<label for="exampleInputEmail1">Issue Location</label>
											<select class="form-control select2" multiple="multiple" id="filterIssue" data-placeholder="Select Location" style="width: 100%;">
												<option></option>
												@foreach($locations as $location)
												<option value="{{ $location->storage_location }}">{{ $location->storage_location }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-8">
										<div class="form-group">
											<label for="exampleInputEmail1">Material Number</label>
											<select class="form-control select2" multiple="multiple" id="filterMaterial" data-placeholder="Select Material" style="width: 100%;">
												<option></option>
												@foreach($materials as $material)
												<option value="{{ $material->item_code }}">{{ $material->item_code }} - {{ $material->item_name }}</option>
												@endforeach
											</select>
										</div>
									</div>

								</div>
							</div>
						</div>
					</form>
					<button class="btn btn-primary pull-right" style="width: 10%; margin: 5px;" onclick="fetchSearch()">Search</button>
					<button class="btn btn-danger pull-right" style="width: 10%; margin: 5px;" onclick="clearAll()">Clear</button>
					<button class="btn btn-danger pull-left" style="margin: 5px;" onclick="syncAll()"><i class="fa fa-close"></i>&nbsp;Remove All Filtered</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<table id="tableResult" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #90ed7d;">
							<tr>
								<th style="width: 1%; text-align: center;">Material</th>
								<th style="width: 5%; text-align: center;">Description</th>
								<th style="width: 0.5%; text-align: center;">SLoc</th>
								<th style="width: 0.5%; text-align: center;">MStation</th>
								<th style="width: 1%; text-align: center;">Quantity</th>
								<th style="width: 1%; text-align: center;">IF at</th>
								<th style="width: 1%; text-align: center;">Action</th>
							</tr>
						</thead>
						<tbody id="tableResultBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		fetchSearch();
	});

	function syncAll(){
		if(confirm("Do you want to sync all this transaction data?")){

			var material_number = $('#filterMaterial').val();
			var issue_location = $('#filterIssue').val();

			var data = {
				material_number:material_number,
				issue_location:issue_location,
			}

			$.post('{{ url("sync/ymes/all_production_result_temporary") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableResult').DataTable().ajax.reload(null, false);
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
					$('#loading').hide();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function sync(id) {
		if(confirm("Do you want to sync this transaction data?")){
			var data = {
				id:id
			}
			$.post('{{ url("sync/ymes/production_result_temporary") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableResult').DataTable().ajax.reload(null, false);
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
					$('#loading').hide();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#loading').hide();
				}
			});
		}
	}

	function clearAll(){
		location.reload(true);
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function fetchSearch(){
		var material_number = $('#filterMaterial').val();
		var issue_location = $('#filterIssue').val();

		$('#tableResult').DataTable().clear();
		$('#tableResult').DataTable().destroy();

		var data = {
			material_number:material_number,
			issue_location:issue_location,
		}

		var table = $('#tableResult').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default'
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
			"serverSide": false,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/ymes/production_result_temporary") }}",
				"data" : data
			},
			"columns": [
			{ "data": "material_number" },
			{ "data": "material_description" },	
			{ "data": "issue_location" },
			{ "data": "mstation" },
			{ "data": "quantity" },
			{ "data": "created_at" },
			{ "data": "sync" }
			],
			"columnDefs": [
			{ "className": 'text-left', "targets": [1] },
			{ "className": 'text-center', "targets": [0, 2, 3, 5, 6] },
			{ "className": 'text-right', "targets": [4] },
			{ "width": "40%", "targets": 1 },
			{ "width": "20%", "targets": 5 },
			]
		});
	}


	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

</script>

@endsection
