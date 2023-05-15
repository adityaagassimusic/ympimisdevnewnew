@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#recordTableBody > tr:hover {
		background-color: #7dfa8c;
	}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 0;
		padding-right: 0;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	#loading, #error { display: none; }
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
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Employee Resume Data Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<!-- <form method="GET" action="{{ url('excel/general/online_transportation') }}"> -->
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold;">Month</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="monthfrom" name="monthfrom">
							</div>
						</div>
						<div class="col-xs-4">
							<a onClick="fetchTable()" class="btn btn-primary" href="javascript:void(0)">Search</a>
							<!-- <button class="btn btn-success" type="submit"><i class="fa fa-download"></i> Export Excel</button> -->
						</div>
					</div>
					<!-- </form> -->

					<div class="row" style="padding-top: 10px;">
						<div class="col-md-12">
							<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">BELUM DI KONFIRMASI (<span id="periode1"></span>)</span>
							</div>
							<div id="divConfirmTable">
								
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-xs-12" style="background-color: orange; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">RESUME (<span id="periode4"></span>)</span>
							</div>
							<table id="resumeTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">ID</th>
										<th style="width: 3%">Nama</th>
										<th style="width: 1%">Grade</th>
										<th style="width: 1%">Mobil</th>
										<th style="width: 1%">Shuttle</th>
										<th style="width: 1%">Lainnya</th>
										<th style="width: 1%">Total Kehadiran</th>
										<th style="width: 1%">Total Amount</th>
									</tr>
								</thead>
								<tbody id="resumeTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
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
						<div class="col-md-12">
							<div class="col-xs-12" style="background-color: yellow; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">DETAIL (<span id="periode3"></span>)</span>
							</div>
							<table id="detailTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">ID</th>
										<th style="width: 10%">Nama</th>
										<th style="width: 1%">Tanggal</th>
										<th style="width: 1%">Kehadiran</th>
										<th style="width: 1%">Kendaraan</th>
										<th style="width: 1%">Asal</th>
										<th style="width: 1%">Tujuan</th>
										<th style="width: 1%">Tol (IDR)</th>
										<th style="width: 1%">Jarak (Km)</th>
										<th style="width: 1%">Bensin</th>
										<th style="width: 1%">Total</th>
										<!-- <th style="width: 2%">Lampiran</th> -->
									</tr>
								</thead>
								<tbody id="detailTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
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
										<th></th>
										<!-- <th></th> -->
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="col-md-12">
							<div class="col-xs-12" style="background-color: #78d09a; text-align: center; margin-bottom: 5px;">
								<span style="font-weight: bold; font-size: 1.6vw;">RESUME EXPORT (<span id="periode2"></span>)</span>
							</div>
							<table id="resumeTableExport" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">EMPNO</th>
										<th style="width: 3%">EMPNAME</th>
										<th style="width: 1%">ALTRANSPORTYMPI</th>
									</tr>
								</thead>
								<tbody id="resumeTableBodyExport">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px;">Edit Transportation Data</h3></center>
				<div class="row">
					<div class="col-md-11 col-md-offset-2">
						<form class="form-horizontal">
							<input type="hidden" id="newId">
							<div class="form-group">
								<input type="hidden" id="id_transport">
								<label for="newAttend" class="col-sm-2 control-label">Kehadiran<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<select class="form-control select2" name="editAttend" id="editAttend" data-placeholder="Pilih Kehadiran" style="width: 100%;" disabled>
										<option value=""></option>
										<option value="in">Masuk</option>
										<option value="out">Pulang</option>
										<option value="cuti">Cuti</option>
										<option value="izin">Izin</option>
										<option value="sakit">Sakit</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="editDate" class="col-sm-2 control-label">Tanggal<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control pull-right" id="editDate" name="editDate" value="{{date('Y-m-d')}}">
								</div>
							</div>
							<div class="form-group">
								<label for="editVehicle" class="col-sm-2 control-label">Kendaraan<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<select class="form-control select2" name="editVehicle" id="editVehicle" data-placeholder="Pilih Kendaraan" style="width: 100%;" disabled>
										<option value=""></option>
										<option value="car">Mobil</option>
										<option value="shuttle">Shuttle</option>
										<option value="lainnya">Lainnya</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="editOrigin" class="col-sm-2 control-label">Asal<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="editOrigin" name="editOrigin" placeholder="Asal">
								</div>
							</div>
							<div class="form-group">
								<label for="editDestination" class="col-sm-2 control-label">Tujuan<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="editDestination" name="editDestination" placeholder="Tujuan">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Jarak<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<div class="input-group">
										<input type="number" style="width: 100%" class="form-control" id="editDistance" name="editDistance" placeholder="Jarak Tempuh">
										<div class="input-group-addon">
											Km
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="editHighwayAmount" class="col-sm-2 control-label">Biaya Tol<span class="text-red">*</span></label>
								<div class="col-sm-6">
									<input type="text" style="width: 100%" class="form-control" id="editHighwayAmount" name="editHighwayAmount" placeholder="Biaya Tol">
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a class="btn btn-success pull-right" onclick="updateData()" id="newButton">Update</a>
					</div>
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#monthfrom').val("");
		$('#monthfrom').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true

		});
		$('.select2').select2();
		fetchTable();
		// appendConfirmTable();
		// fetchConfirmTable();
		// fetchResumeTable();

		$('#editDate').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
	});

	function fetchTable(){
		fetchConfirmTable();
		fetchResumeTable();
	}

	function confirmRecord(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.post('{{ url("confirm/general/online_transportation_report") }}', data, function(result, status, xhr){
			if(result.status){
				fetchTable();
				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function fetchResumeTable(){
		$('#loading').show();
		var month_from = $('#monthfrom').val();
		var data = {
			month_from:month_from
		}
		$.get('{{ url("fetch/general/online_transportation_resume_report") }}', data, function(result, status, xhr){
			if(result.status){
				$('#detailTable').DataTable().clear();
				$('#detailTable').DataTable().destroy();
				$('#resumeTable').DataTable().clear();
				$('#resumeTable').DataTable().destroy();
				$('#resumeTableExport').DataTable().clear();
				$('#resumeTableExport').DataTable().destroy();
				$('#detailTableBody').html("");
				$('#resumeTableBody').html("");
				$('#resumeTableBodyExport').html("");
				var detailTable = "";
				var resumeTable = "";
				var resumeTableExport = "";

				$.each(result.transportations, function (key, value){
					detailTable += '<tr>';
					detailTable += '<td style="width: 1%;">'+value.employee_id+'</td>';
					detailTable += '<td style="width: 10%;">'+value.name+'</td>';
					detailTable += '<td style="width: 1%;">'+value.check_date+'</td>';
					detailTable += '<td style="width: 1%;">'+value.attend_code.toUpperCase()+'</td>';
					detailTable += '<td style="width: 1%;">'+value.vehicle.toUpperCase()+'</td>';
					detailTable += '<td style="width: 1%;">(IN = '+value.origin_in+') - (OUT = '+value.origin_out+')</td>';
					detailTable += '<td style="width: 1%;">(IN = '+value.destination_in+') - (OUT = '+value.destination_out+')</td>';
					detailTable += '<td style="width: 1%;">'+value.highway_amount_total+'</td>';
					detailTable += '<td style="width: 1%;">'+value.distance_total+'</td>';
					detailTable += '<td style="width: 1%;">'+value.fuel.toFixed(0)+'</td>';
					detailTable += '<td style="width: 1%;">'+value.total_amount.toFixed(0)+'</td>';
					// detailTable += '<td style="width: 1%;">';
					// if(value.att_in != '{{ url("files/general_transportation/0") }}'){
					// 	detailTable += '<a href="javascript:void(0)" id="'+ value.att_in +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> in</a>';
					// }
					// if(value.att_out != '{{ url("files/general_transportation/0") }}'){
					// 	detailTable += '&nbsp;<a href="javascript:void(0)" id="'+ value.att_out +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> out</a>';
					// }
					// detailTable += '</td>';
					detailTable += '</tr>';					
				});

				$('#detailTableBody').append(detailTable);


				var data = result.transportations;
				var result = [];

				data.reduce(function (res, value) {
					if (!res[value.employee_id]) {
						res[value.employee_id] = {
							total_amount: 0,
							attend_count: 0,
							car: 0,
							shuttle: 0,
							lainnya: 0,
							employee_id: value.employee_id,
							name: value.name,
							grade: value.grade,
						};
						result.push(res[value.employee_id])
					}
					res[value.employee_id].total_amount += value.total_amount
					res[value.employee_id].attend_count += value.attend_count
					if (value.vehicle == 'car') {
						res[value.employee_id].car += 1
					}
					if (value.vehicle == 'shuttle') {
						res[value.employee_id].shuttle += 1
					}
					if (value.vehicle == 'lainnya') {
						res[value.employee_id].lainnya += 1
					}
					if (true) {}
					return res;
				}, {});

				$.each(result, function (key, value){
					resumeTable += '<tr>';
					resumeTable += '<td>'+value.employee_id+'</td>';
					resumeTable += '<td>'+value.name+'</td>';
					resumeTable += '<td>'+value.grade+'</td>';
					resumeTable += '<td>'+value.car+'</td>';
					resumeTable += '<td>'+value.shuttle+'</td>';
					resumeTable += '<td>'+value.lainnya+'</td>';
					resumeTable += '<td>'+value.attend_count+'</td>';
					resumeTable += '<td>'+value.total_amount.toFixed(0)+'</td>';
					resumeTable += '</tr>';
				});
				$('#resumeTableBody').append(resumeTable);

				$.each(result, function (key, value){
					resumeTableExport += '<tr>';
					resumeTableExport += '<td>'+value.employee_id+'</td>';
					resumeTableExport += '<td>'+value.name+'</td>';
					resumeTableExport += '<td>'+value.total_amount.toFixed(0)+'</td>';
					resumeTableExport += '</tr>';
				});
				$('#resumeTableBodyExport').append(resumeTableExport);

				$('#detailTable tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var table = $('#detailTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
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
					'paging': false,
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

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#detailTable tfoot tr').appendTo('#detailTable thead');

				$('#resumeTable tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var table2 = $('#resumeTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
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
					'paging': false,
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

				table2.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#resumeTable tfoot tr').appendTo('#resumeTable thead');

				var table3 = $('#resumeTableExport').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
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
							title:'',
							sheetName: 'Payroll Component Upload',
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
					'paging': false,
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
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});
}

function appendConfirmTable() {
	var divConfirmTable = "";
	$('#divConfirmTable').html('');

	divConfirmTable += '<table id="confirmTable" class="table table-bordered table-striped table-hover"></table>';

	$('#divConfirmTable').append(divConfirmTable);

	var confirmTable = "";
	$('#confirmTable').html('');

	confirmTable += '<thead style="background-color: rgba(126,86,134,.7);" id="confirmTableHead">';
	confirmTable += '<tr>';
	confirmTable += '<th style="width: 1%">ID</th>';
	confirmTable += '<th style="width: 5%">Nama</th>';
	confirmTable += '<th style="width: 1%">Grade</th>';
	confirmTable += '<th style="width: 1%">Tanggal</th>';
	confirmTable += '<th style="width: 1%">Kehadiran</th>';
	confirmTable += '<th style="width: 1%">Kendaraan</th>';
	confirmTable += '<th style="width: 1%">Asal</th>';
	confirmTable += '<th style="width: 1%">Tujuan</th>';
	confirmTable += '<th style="width: 1%">Tol</th>';
	confirmTable += '<th style="width: 1%">Jarak</th>';
	confirmTable += '<th style="width: 1%">Confirm</th>';
	confirmTable += '<th style="width: 1%">Action</th>';
	confirmTable += '</tr>';
	confirmTable += '</thead>';

	confirmTable += '<tbody id="confirmTableBody"></tbody>';

	confirmTable += '<tfoot id="confirmTableFoot" style="background-color: RGB(252, 248, 227);">';
	confirmTable += '<tr>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '<th></th>';
	confirmTable += '</tr>';
	confirmTable += '</tfoot>';
	$('#confirmTable').append(confirmTable);
}

function fetchConfirmTable(){
	$('#loading').show();
	var month_from = $('#monthfrom').val();
	var data = {
		month_from:month_from
	}

	$.get('{{ url("fetch/general/online_transportation_report") }}', data, function(result, status, xhr){
		if(result.status){
			$('#periode1').text(result.period);
			$('#periode2').text(result.period);
			$('#periode3').text(result.period);
			$('#periode4').text(result.period);

			appendConfirmTable();

			var confirmTableBody = "";
			$('#confirmTableBody').html('');

			$.each(result.transportations, function(key, value){
				confirmTableBody += '<tr id="confirm_'+value.id+'">';
				confirmTableBody += '<td>'+value.employee_id+'</td>';
				confirmTableBody += '<td>'+value.name+'</td>';
				confirmTableBody += '<td>'+value.grade_code+'</td>';
				confirmTableBody += '<td>'+value.check_date+'</td>';
				confirmTableBody += '<td>'+value.attend_code.toUpperCase()+'</td>';
				if (value.vehicle == null) {
					confirmTableBody += '<td></td>';
				}else{
					confirmTableBody += '<td>'+value.vehicle.toUpperCase()+'</td>';
				}
				if (value.vehicle == null) {
					confirmTableBody += '<td></td>';
				}else{
					confirmTableBody += '<td>'+value.origin+'</td>';
				}
				if (value.vehicle == null) {
					confirmTableBody += '<td></td>';
				}else{
					confirmTableBody += '<td>'+value.destination+'</td>';
				}
				if (value.highway_amount == null) {
					confirmTableBody += '<td>0</td>';
				}else{
					confirmTableBody += '<td>'+value.highway_amount+'</td>';
				}
				confirmTableBody += '<td>'+value.distance+'</td>';
				// confirmTableBody += '<td><a href="javascript:void(0)" id="'+ value.highway_attachment +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> Struk</a></td>';
				confirmTableBody += '<td><button class="btn btn-success btn-xs" onclick="confirmRecord(\''+value.id+'\')">Confirm</button></td>';
				confirmTableBody += '<td><button class="btn btn-warning btn-xs" onclick="openModalEdit(\''+value.id+'\')">Edit</button><br><button class="btn btn-danger btn-xs" onclick="openModalDelete(\''+value.id+'\')">Delete</button></td>';
				confirmTableBody += '</tr>';
			});

			$('#confirmTableBody').append(confirmTableBody);

			var table = $('#confirmTable').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
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
				'paging': false,
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

			$('#confirmTable tfoot th').each(function(){
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
			});

			table.columns().every( function () {
				var that = this;

				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
						.search( this.value )
						.draw();
					}
				} );
			} );

			$('#confirmTable tfoot tr').appendTo('#confirmTable thead');

			$('#loading').hide();
		}
		else{
			$('#loading').hide();
			openErrorGritter('Error', result.message);
		}

	});

}

function openModalEdit(id) {
	var data = {
		id:id
	}
	$.get('{{ url("fetch/general/edit_online_transportation") }}', data,function(result, status, xhr){
		if(result.status){
			$('#editAttend').val(result.datas.attend_code).trigger('change');
			$('#editVehicle').val(result.datas.vehicle).trigger('change');
			$('#editDate').val(result.datas.check_date);
			$('#editDistance').val(result.datas.distance);
			$('#editOrigin').val(result.datas.origin);
			$('#editDestination').val(result.datas.destination);
			$('#editHighwayAmount').val(result.datas.highway_amount);
			$('#id_transport').val(id);

			$('#edit_modal').modal('show');
		}
	});
}

function updateData() {
	$('#loading').show();
	var editDate = $('#editDate').val();
	var editDistance = $('#editDistance').val();
	var editOrigin = $('#editOrigin').val();
	var editDestination = $('#editDestination').val();
	var editHighwayAmount = $('#editHighwayAmount').val();
	var id_transport = $('#id_transport').val();
	var data = {
		editDate:editDate,
		editDistance:editDistance,
		editOrigin:editOrigin,
		editDestination:editDestination,
		editHighwayAmount:editHighwayAmount,
		id_transport:id_transport,
	}

	var url = '{{ url("update/general/online_transportation") }}';

	$.post(url, data,function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success','Update Data Berhasil.');
			$('#edit_modal').modal('hide');
			$('#loading').hide();
			fetchTable();
		}else{
			openErrorGritter('Error!','Update Data Gagal.');
			$('#loading').hide();
		}
	});
}

function openModalDelete(id) {
	if (confirm('Apakah Anda yakin akan menghapus data?')) {
		$('#loading').show();
		var data = {
			id:id
		}

		$.post('{{ url("delete/general/online_transportation") }}', data,function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Delete Data Berhasil.');
				$('#loading').hide();
				fetchTable();
			}else{
				openErrorGritter('Error!','Delete Data Gagal.');
				$('#loading').hide();
			}
		});
	}
}



function downloadAtt(id){
	window.open(id, '_blank');
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