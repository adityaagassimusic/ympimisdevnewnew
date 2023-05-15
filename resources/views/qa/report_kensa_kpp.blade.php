@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.buttonclass {
		top: 0;
		left: 0;
		transition: all 0.15s linear 0s;
		position: relative;
		display: inline-block;
		padding: 15px 25px;
		background-color: #ffe800;
		text-transform: uppercase;
		color: #404040;
		font-family: arial;
		letter-spacing: 1px;
		box-shadow: -6px 6px 0 #404040;
		text-decoration: none;
		cursor: pointer;
	}
	.buttonclass:hover {
		top: 3px;
		left: -3px;
		box-shadow: -3px 3px 0 #404040;
		color: white
	}
	.buttonclass:hover::after {
		top: 1px;
		left: -2px;
		width: 4px;
		height: 4px;
	}
	.buttonclass:hover::before {
		bottom: -2px;
		right: 1px;
		width: 4px;
		height: 4px;
	}
	.buttonclass::after {
		transition: all 0.15s linear 0s;
		content: "";
		position: absolute;
		top: 2px;
		left: -4px;
		width: 8px;
		height: 8px;
		background-color: #404040;
		transform: rotate(45deg);
		z-index: -1;
	}
	.buttonclass::before {
		transition: all 0.15s linear 0s !important;
		content: "";
		position: absolute;
		bottom: -4px;
		right: 2px;
		width: 8px;
		height: 8px;
		background-color: #404040;
		transform: rotate(45deg) !important;
		z-index: -1 !important;
	}

	a.buttonclass {
		position: relative;
	}

	a:active.buttonclass {
		top: 6px;
		left: -6px;
		box-shadow: none;
	}
	a:active.buttonclass:before {
		bottom: 1px;
		right: 1px;
	}
	a:active.buttonclass:after {
		top: 1px;
		left: 1px;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>

</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-warning alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4> Warning!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<form method="GET" action="{{ url('excel/qa/report/kensa') }}">
						<h4>Filter</h4>
						<div class="row">
							<div class="col-md-4 col-md-offset-2">
								<span style="font-weight: bold;">Date From</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<span style="font-weight: bold;">Date To</span>
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="date_to"name="date_to" placeholder="Select Date To" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="row">					
							<div class="col-md-4 col-md-offset-2">
								<span style="font-weight: bold;">Inspection Level</span>
								<div class="form-group">
									<select class="form-control select2" multiple="multiple" id='inspectionLevelSelect' onchange="changeInspectionLevel()" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important">
										@foreach($inspection_levels as $inspection_level)
										<option value="{{$inspection_level->inspection_level}}">{{$inspection_level->inspection_level}}</option>
										@endforeach
									</select>
									<input type="text" name="inspection_level" id="inspection_level" style="color: black !important" hidden>
								</div>
							</div>

							<div class="col-md-4">
								<span style="font-weight: bold;">Material</span>
								<div class="form-group">
									<select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
										@foreach($materials as $material)
										<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
										@endforeach
									</select>
									<input type="text" name="material" id="material" style="color: black !important" hidden>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 ">
								<a href="{{ url('index/qa') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
								<a href="{{ url('index/qa/report/kensa') }}" class="btn btn-danger"><i class="fa  fa-refresh"></i> Clear</a>
							</div>

							<div class="col-md-6">
								<a class="btn btn-primary col-sm-14" href="javascript:void(0)" onclick="fillList()"><i class="fa  fa-search"></i> Search</a>
								<button class="btn btn-success" type="submit" name="publish" value="Export Excel Without Merge"><i class="fa fa-file-excel-o"></i> Export Excel Without Merge</button>
								<button class="btn btn-warning" type="submit" name="save"><i class="fa  fa-file-excel-o"></i> Export Excel With Merge</button>
							</div>
						</div>
					</div>

				</div>
			</form>
			<div class="col-xs-12" style="direction: rtl;transform: rotate(180deg);overflow-y: hidden;overflow-x: scroll;">
				<div class="row" id="divTable" style="direction: ltr;transform: rotate(-180deg);">
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div class="modal modal-default fade" id="edit_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Report Incoming Check</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />

							<div class="form-group row" align="right">
								<label class="col-sm-4">Loc<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="location_edit" class="form-control" id="location_edit" placeholder="Location" required readonly>
									<input type="hidden" class="form-control" id="incoming_check_code_edit" placeholder="Check Code" required>
									<input type="hidden" class="form-control" id="id_log_edit" placeholder="Check Code" required>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Date<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="date_edit" class="form-control" id="date_edit" placeholder="Date" required readonly>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Material<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="hidden" id="material_desc_edit">
									<select class="form-control select3" onchange="change_material()" id='material_edit' data-placeholder="Select Material" style="width: 100%;color: black !important">
										<option value=""></option>
										@foreach($materials as $material)
										<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Inspection Level<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<select class="form-control select3" id="inspection_level_edit" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important">
										<option value=""></option>
										@foreach($inspection_levels as $inspection_level)
										<option value="{{$inspection_level->inspection_level}}">{{$inspection_level->inspection_level}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-4">Qty Recieve<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="number" class="form-control" id="qty_rec_edit" placeholder="Qty Recieve" required>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" onclick="updateKensaReport()"><i class="fa fa-pencil"></i> Update</button>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('.select3').select2({
			dropdownParent: $('#edit_modal')
		});
		cancelAll();

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function cancelAll() {
		$('#location_edit').val('');
		$('#date_edit').val('');
		$('#material_edit').val('').trigger('change');
		$('#material_desc_edit').val('');
		$('#inspection_level_edit').val('').trigger('change');
		$('#qty_rec_edit').val('');
		$('#incoming_check_code_edit').val('');
		$('#id_log_edit').val('');
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	function changeInspectionLevel() {
		$("#inspection_level").val($("#inspectionLevelSelect").val());
	}

	function initiateTable() {
		$('#example1').DataTable().clear();
		$('#example1').DataTable().destroy();
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th>ID</th>';
		tableData += '<th>Loc</th>';
		tableData += '<th>Date</th>';
		tableData += '<th>Inspector</th>';
		tableData += '<th>Inspection Level</th>';
		tableData += '<th>Material</th>';
		tableData += '<th>Desc</th>';
		tableData += '<th>HPL</th>';
		tableData += '<th>Qty Production</th>';
		tableData += '<th>Qty Check</th>';
		tableData += '<th>Defect</th>';
		tableData += '<th>Repair</th>';
		tableData += '<th>Scrap</th>';
		tableData += '<th>Note</th>';
		tableData += '<th>NG Ratio</th>';
		tableData += '<th>Action</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example1Body">';
		tableData += "</tbody>";
		tableData += "</table>";
		$('#divTable').append(tableData);

		
	}

	function fillList(){
		$('#loading').show();
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();
		var material = $('#material').val();
		var inspection_level = $('#inspection_level').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
			material:material,
			inspection_level:inspection_level,
		}
		$.get('{{ url("fetch/qa/report/kensa") }}',data, function(result, status, xhr){
			if(result.status){
				initiateTable();			
				
				var tableData = "";
				var index = 0;
				
				$.each(result.datas, function(key, value) {
					var jumlah = 0;

					if (value.ng_name != null) {
						var ng_name = value.ng_name.split('_');
						var ng_qty = value.ng_qty.split('_');
						var status_ng = value.status_ng.split('_');
						if (value.note_ng != null) {
							var note_ng = value.note_ng.split('_');
						}else{
							var note_ng = "";
						}
						jumlah = ng_name.length;
					}else{
						jumlah = 1;
					}

					
					for (var i = 0; i < jumlah; i++) {
						tableData += '<tr>';
						tableData += '<td style="vertical-align:middle;text-align:right">'+ value.id_log +'</td>';
						tableData += '<td style="vertical-align:middle">'+ value.location +'</td>';
						tableData += '<td style="vertical-align:middle">'+ value.created +'</td>';
						tableData += '<td style="vertical-align:middle">'+ value.employee_id +'<br>'+ value.name +'</td>';
						tableData += '<td style="vertical-align:middle">'+ value.inspection_level +'</td>';
						tableData += '<td style="vertical-align:middle;text-align:right">'+ value.material_number +'</td>';
						tableData += '<td style="vertical-align:middle">'+ value.material_description +'</td>';
						tableData += '<td style="vertical-align:middle">'+ value.hpl +'</td>';
						tableData += '<td style="vertical-align:middle;text-align:right">'+ value.qty_production +'</td>';
						tableData += '<td style="vertical-align:middle;text-align:right">'+ value.qty_check +'</td>';
						if (value.ng_name != null) {
							tableData += '<td style="vertical-align:middle">';
							tableData += '<span class="label label-danger">'+ng_name[i]+'</span><br>';
							tableData += '</td>';
							if (status_ng[i] == 'Repair') {
								tableData += '<td style="vertical-align:middle;text-align:right">';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
								tableData += '<td style="vertical-align:middle">';
								tableData += '</td>';
							}else if (status_ng[i] == 'Scrap') {
								tableData += '<td style="vertical-align:middle">';
								tableData += '</td>';
								tableData += '<td style="vertical-align:middle;text-align:right">';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
							}
							if (note_ng.length > 0) {
								tableData += '<td style="vertical-align:middle">';
								tableData += ''+note_ng[i]+'';
								tableData += '</td>';
							}else{
								tableData += '<td style="vertical-align:middle">';
								tableData += '</td>';
							}
						}else{
							tableData += '<td style="vertical-align:middle">';
							tableData += '</td>';
							tableData += '<td style="vertical-align:middle">';
							tableData += '</td>';
							tableData += '<td style="vertical-align:middle">';
							tableData += '</td>';
							tableData += '<td style="vertical-align:middle">';
							tableData += ''+value.note_all+'';
							tableData += '</td>';
						}
						tableData += '<td style="vertical-align:middle;text-align:right">'+ value.ng_ratio.toFixed(2) +'</td>';
						tableData += '<td style="vertical-align:middle"><button class="btn btn-warning btn-xs" onclick="editKensaReport(\''+value.id_log+'\')"><i class="fa fa-pencil"></i> Edit</button><button class="btn btn-danger btn-xs	" onclick="deleteKensaReport(\''+value.id_log+'\')"><i class="fa fa-trash"></i> Delete</button></td>';
						tableData += '</tr>';
					}

					index++;
				});
				$('#example1Body').append(tableData);

				$('#example1').DataTable({
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
				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
}

function editKensaReport(id) {
	$('#loading').show();
	cancelAll();
	var data = {
		id:id
	}
	$.get('{{ url("fetch/qa/report/kensa/edit") }}',data, function(result, status, xhr){
		if (result.status) {
			$.each(result.datas, function(key, value) {
				if (value.location == 'KPP') {
					var loc = 'Key Part Proccess';
				}

				$('#location_edit').val(loc);
				$('#date_edit').val(value.date);
				$('#material_edit').val(value.material_number).trigger('change');
				$('#material_desc_edit').val(value.material_description);
				$('#inspection_level_edit').val(value.inspection_level).trigger('change');
				$('#qty_rec_edit').val(value.qty_production);
				$('#incoming_check_code_edit').val(value.incoming_check_code);
				$('#id_log_edit').val(value.id_log);
			});
			$('#edit_modal').modal('show');
			$('#loading').hide();
		}else{
			$('#loading').hide();
			openErrorGritter('Error!',result.message);
		}
	});
}

function deleteKensaReport(id) {
	if (confirm('Apakah Anda yakin akan menghapus data?')) {
		$('#loading').show();
		var data = {
			id:id
		}
		$.get('{{ url("fetch/qa/report/kensa/delete") }}',data, function(result, status, xhr){
			if (result.status) {
				fillList();
				$('#loading').hide();
				openSuccessGritter('Success',result.message);
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}
}

function updateKensaReport() {
	$('#loading').show();
	var material = $('#material_edit').val();
	var material_desc = $('#material_desc_edit').val();
	var inspection_level = $('#inspection_level_edit').val();
	var lot_number = $('#lot_number_edit').val();
	var qty_rec = $('#qty_rec_edit').val();
	var incoming_check_code = $('#incoming_check_code_edit').val();
	var id_log = $('#id_log_edit').val();

	var data = {
		material:material,
		material_desc:material_desc,
		inspection_level:inspection_level,
		qty_production:qty_rec,		
		incoming_check_code:incoming_check_code,
		id_log:id_log,
	}

	$.post('{{ url("update/qa/report/kensa") }}',data, function(result, status, xhr){
		if (result.status) {
			fillList();
			$('#loading').hide();
			$('#edit_modal').modal('hide');
			cancelAll();
			openSuccessGritter('Success',result.message);
		}else{
			$('#loading').hide();
			openErrorGritter('Error!',result.message);
		}
	});
}

function change_material() {
	$("#material_desc_edit").val($( "#material_edit option:selected").text().split(' - ')[1]);
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