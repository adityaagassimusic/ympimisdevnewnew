@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#tableResumeRole tbody>tr>td:hover {
		cursor: pointer;
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(50,50,50);
		padding: 8px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(50,50,50);
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="generateStocktaking()"><i class="fa fa-edit"></i>&nbsp;&nbsp;Generate</button>
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
		<div class="col-xs-7">
			<div class="box box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Accounts Information</h3>
				</div>
				<div class="box-body">
					<div class="col-xs-12">
						<div class="row">
							<div class="form-group" style="width: 30%;">
								<label style="padding-top: 0; padding-left: 0; color: black;" for="" class="col-xs-12 control-label">Period<span class="text-red"></span> :</label>
								<select class="form-control select2" id="fiscal_year" style="width: 100%; height: 100%;" data-placeholder="Select Fiscal Year" onchange="fetchData()" required>
									@foreach($periods as $period)
									<option value="{{ $period->period_date }}">{{ $period->period }}</option>
									@endforeach
								</select>
							</div>
							<table id="tableResume" class="table table-bordered table-hover">
								<thead style="background-color: #605ca8; color: white;">
									<tr>
										<th style="width: 0.1%; text-align: center;">#</th>
										<th style="width: 0.1%; text-align: center;">Period</th>
										<th style="width: 1%; text-align: left;">Category</th>
										<th style="width: 1%; text-align: left;">Data</th>
										<th style="width: 1%; text-align: right;">Active</th>
										<th style="width: 1%; text-align: right;">Inactive</th>
										<th style="width: 1%; text-align: right;">Total</th>
										<th style="width: 1%; text-align: center; background-color: RGB(255,204,255); color: black;">Total<br>Unmatch</th>
										<th style="width: 1%; text-align: center; background-color: RGB(204,255,255); color: black;">Total<br>Handled</th>
									</tr>
								</thead>
								<tbody id="tableResumeBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-5" style="padding-left:0;">
			<div class="box box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Stocktaking Resumes</h3>
				</div>
				<div class="box-body">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableAccount" class="table table-bordered table-hover">
								<thead style="background-color: #605ca8; color: white;">
									<tr>
										<th style="width: 1%; text-align: left;">Accounts Stoktaking Period</th>
										<th style="width: 0.1%; text-align: center;">Adjusted</th>
										<th style="width: 0.1%; text-align: center;">Allowed</th>
										<th style="width: 0.1%; text-align: center;">Deactivated</th>
										<th style="width: 0.1%; text-align: center;">Total</th>
									</tr>
								</thead>
								<tbody id="tableAccountBody">
								</tbody>
							</table>
							<table id="tableRole" class="table table-bordered table-hover">
								<thead style="background-color: #605ca8; color: white;">
									<tr>
										<th style="width: 1%; text-align: left;">Roles Stoktaking Period</th>
										<th style="width: 0.1%; text-align: center;">Added</th>
										<th style="width: 0.1%; text-align: center;">Removed</th>
										<th style="width: 0.1%; text-align: center;">Total</th>
									</tr>
								</thead>
								<tbody id="tableRoleBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Actual Role MIRAI</h3>
				</div>
				<div class="box-body">
					<div class="col-xs-12" id="div_table">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalUnmatch">
	<div class="modal-dialog modal-lg" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table id="tableUnmatch" class="table table-bordered table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 0.1%; text-align: center;">#</th>
								<th style="width: 0.1%; text-align: left;">ID</th>
								<th style="width: 0.5%; text-align: left;">Name</th>
								<th style="width: 0.5%; text-align: left;">Role Code</th>
								<th style="width: 0.5%; text-align: left;">Data</th>
								<th style="width: 0.5%; text-align: left;">Role</th>
								<th style="width: 2%; text-align: left;">Department</th>
								<th style="width: 0.1%; text-align: center;">Status</th>
								<th style="width: 1%; text-align: left;">Action</th>
								<th style="width: 2%; text-align: left;">Remark</th>
							</tr>
						</thead>
						<tbody id="tableUnmatchBody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success pull-right" onclick="updateUnmatch()" style="font-weight: bold;">UPDATE</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalPermission">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;" id="div_table_permission">

				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success pull-right" onclick="updatePermission()" style="font-weight: bold;">UPDATE</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalGithub">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><span style="font-size: 20px;font-weight: bold;">Github Member Evidence</span></center>
			</div>
			<div class="modal-body">
				<span>Upload file</span>
				<input type="file" class="form-control" id="upload_file" name="upload_file">
				<br>
				<img src="{{url('images/github.png')}}" style="width: 100%;">
			</div>
<!-- 			<div class="modal-footer">
				<button class="btn btn-success pull-right" onclick="updatePermission()" style="font-weight: bold;">UPDATE</button>
			</div> -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		fetchData();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var accounts = [];
	var groups = [];
	var actions = [];
	var remarks = [];
	var permissions = [];
	var navigations = [];
	var roles = [];
	var permission_lists = [];
	var navigation_lists = [];
	var role_lists = [];
	var new_permission = [];
	var old_permission = [];
	var update_navigation_name = "";
	var update_position = "";
	var account_resumes = [];
	var role_resumes = [];

	function generateStocktaking(){
		if(confirm("Generate stocktaking for this period?")){
			$('#loading').show();
			var data = {
			}
			$.get('{{ url("generate/mis/stocktaking_account") }}', data, function(result, status, xhr){
				if(result.status){
					fetchData();
					$('#loading').hide();
					openSuccessGritter('Success!', result.message);
					audio_ok.play();
					return false;
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function modalRole(navigation_name, position){
		new_permission = [];
		update_navigation_name = navigation_name;
		update_position = position;

		$('#div_table_permission').html("");
		var div_table_permission = "";

		div_table_permission += '<table id="tablePermission" class="table table-bordered table-striped table-hover">';
		div_table_permission += '<thead style="background-color: rgb(96, 92, 168); color: white;">';
		div_table_permission += '<tr>';
		for (var i = 0; i < navigation_lists.length; i++) {
			if(navigation_lists[i].navigation_name == navigation_name){
				div_table_permission += '<th style="width: 0.1%; text-align: left; font-size: 12px;">'+navigation_lists[i].navigation_code+'</th>';
			}
		}
		div_table_permission += '</tr>';
		div_table_permission += '</thead>';
		div_table_permission += '<tbody>';
		div_table_permission += '<tr>';
		for (var i = 0; i < navigation_lists.length; i++) {
			if(navigation_lists[i].navigation_name == navigation_name){
				div_table_permission += '<td style="width: 0.1%; text-align: left; font-size: 12px; padding-left: 20px;>';
				for (var j = 0; j < role_lists.length; j++) {
					if(role_lists[j].position == position){
						div_table_permission += '<div class="form-group">';
						div_table_permission += '<div class="checkbox">';
						div_table_permission += '<label>';
						var checked = '';
						for (var k = 0; k < permission_lists.length; k++) {
							if(permission_lists[k].position == position && permission_lists[k].navigation_code == navigation_lists[i].navigation_code && permission_lists[k].role_code == role_lists[j].role_code){
								checked = 'checked';
								new_permission.push(permission_lists[k].navigation_code+'+'+permission_lists[k].role_code);
								old_permission.push(permission_lists[k].navigation_code+'+'+permission_lists[k].role_code);
							}
						}
						div_table_permission += '<input type="checkbox" id="'+navigation_lists[i].navigation_code+role_lists[j].role_code+'" onchange="newPermission(\''+navigation_lists[i].navigation_code+'\',\''+role_lists[j].role_code+'\')" '+checked+'>';
						div_table_permission += role_lists[j].role_code;
						div_table_permission += '</label>';
						div_table_permission += '</div>';			
					}
				}
				div_table_permission += '</td>';
			}
		}		
		div_table_permission += '</tr>';
		div_table_permission += '</tbody>';
		div_table_permission += '</table>';

		$('#div_table_permission').append(div_table_permission);
		$('#modalPermission').modal('show');
	}

	function newPermission(navigation_code, role_code){
		var checked = false;
		if ($('#'+navigation_code+role_code).prop('checked') == true){
			checked = true;
		}

		if(checked == true){
			new_permission.push(navigation_code+'+'+role_code);
		}
		else{
			new_permission = jQuery.grep(new_permission, function(value) {
				return value != navigation_code+'+'+role_code;
			});
		}
	}

	function updatePermission(){
		var changed = false;
		for (var i = 0; i < old_permission.length; i++) {
			if(jQuery.inArray(old_permission[i], new_permission) !== -1){

			}
			else{
				var changed = true;					
			}
		}
		for (var i = 0; i < new_permission.length; i++) {
			if(jQuery.inArray(new_permission[i], old_permission) !== -1){

			}
			else{
				var changed = true;					
			}
		}
		if(changed == false){
			openErrorGritter('Error!', 'There is no changes detected');
			audio_error.play();
			return false;
		}
		if(confirm("Are you sure want to update permissions?")){
			var data = {
				permission:new_permission,
				navigation_name:update_navigation_name,
				position:update_position
			}
			$.post('{{ url("update/mis/permission") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success', result.message);
					$('#modalPermission').modal('hide');
					fetchData();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error', result.message);
					audio_error.play();
				}
			});
		}
		else{
			return false;
		}
	}

	function fetchData(){
		$('#loading').show();
		var period = $('#fiscal_year').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/mis/stocktaking_account") }}', data, function(result, status, xhr){
			if(result.status){
				accounts = result.accounts;
				groups = result.groups;
				permissions = result.permissions;
				navigations = result.navigations;
				roles = result.roles;
				permission_lists = result.permission_lists;
				navigation_lists = result.navigation_lists;
				role_lists = result.role_lists;
				account_resumes = result.account_resumes;
				role_resumes = result.role_resumes;

				$('#tableResumeRole').DataTable().clear();
				$('#tableResumeRole').DataTable().destroy();
				$('#tableAccount').DataTable().clear();
				$('#tableAccount').DataTable().destroy();
				$('#tableRole').DataTable().clear();
				$('#tableRole').DataTable().destroy();

				var tableAccountBody = "";
				var tableRoleBody = "";
				$('#tableAccountBody').html("");
				$('#tableRoleBody').html("");

				$.each(result.account_resumes, function(key, value){
					tableAccountBody += '<tr>';
					tableAccountBody += '<td style="width: 1%; text-align: left;">'+value.period+'</td>';
					tableAccountBody += '<td style="width: 0.1%; text-align: center;">'+value.adjusted+'</td>';
					tableAccountBody += '<td style="width: 0.1%; text-align: center;">'+value.allowed+'</td>';
					tableAccountBody += '<td style="width: 0.1%; text-align: center;">'+value.deactivated+'</td>';
					tableAccountBody += '<td style="width: 0.1%; text-align: center;">'+(value.deactivated+value.allowed+value.adjusted)+'</td>';
					tableAccountBody += '</tr>';
				});

				$.each(result.role_resumes, function(key, value){
					tableRoleBody += '<tr>';
					tableRoleBody += '<td style="width: 1%; text-align: left;">'+value.period+'</td>';
					tableRoleBody += '<td style="width: 0.1%; text-align: center;">'+value.added+'</td>';
					tableRoleBody += '<td style="width: 0.1%; text-align: center;">'+value.removed+'</td>';
					tableRoleBody += '<td style="width: 0.1%; text-align: center;">'+(value.removed+value.added)+'</td>';
					tableRoleBody += '</tr>';
				});

				console.table(role_resumes);

				$('#tableAccountBody').append(tableAccountBody);
				$('#tableRoleBody').append(tableRoleBody);

				var tableResumeBody = "";
				$('#tableResumeBody').html("");

				var cnt = 0;
				$.each(result.groups, function(key, value){
					cnt += 1;

					if (cnt % 2 === 0 ) {
						color = '#fffcb7';
					}
					else {
						color = '#ffd8b7';
					}

					tableResumeBody += '<tr>';
					tableResumeBody += '<td style="width: 0.1%; text-align: center; background-color: '+color+'; font-weight: bold;" rowspan="3">'+cnt+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: center; background-color: '+color+'; font-weight: bold;" rowspan="3">'+value.period+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: left; background-color: '+color+'; font-weight: bold;" rowspan="3">'+value.category+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: left;">Sunfish Data</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.employee_active+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.employee_inactive+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.employee_total+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: center; background-color: '+color+'; font-weight: bold; font-size: 2vw; color: red;" rowspan="3"><a href="javascript:void(0)" onclick="unmatch(\''+value.category+'\')">'+value.unmatch+'</a></td>';
					tableResumeBody += '<td style="width: 1%; text-align: center; background-color: '+color+'; font-weight: bold; font-size: 2vw; color: green;" rowspan="3">'+value.handled+'</td>';
					tableResumeBody += '</tr>';
					tableResumeBody += '<tr>';
					tableResumeBody += '<td style="width: 1%; text-align: left;">Actual MIRAI Data</td>';
					if (value.category == "GITHUB") {
						tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.user_active+' (Input Manual)</td>';
					}else{
						tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.user_active+'</td>';
					}
					tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.user_inactive+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right;">'+value.user_total+'</td>';
					tableResumeBody += '</tr>';
					tableResumeBody += '<tr>';
					tableResumeBody += '<td style="width: 1%; text-align: left; background-color: '+color+';">Diff</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right; background-color: '+color+';">'+(value.user_active-value.employee_active)+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right; background-color: '+color+';">'+(value.user_inactive-value.employee_inactive)+'</td>';
					tableResumeBody += '<td style="width: 1%; text-align: right; background-color: '+color+';">'+(value.user_total-value.employee_total)+'</td>';
					tableResumeBody += '</tr>';
				});
				$('#tableResumeBody').append(tableResumeBody);
				$('#loading').hide();

				$('#div_table').html("");
				var div_table = "";

				div_table += '<div class="row">';
				div_table += '<table id="tableResumeRole" class="table table-bordered table-striped table-hover">';
				div_table += '<thead style="background-color: rgb(96, 92, 168); color: white;">';
				div_table += '<tr>';
				div_table += '<th style="width: 0.1%; text-align: center; font-size: 12px;">#</th>';
				div_table += '<th style="width: 2%; text-align: left; font-size: 12px;">Menu</th>';
				$.each(roles, function(key, value){
					div_table += '<th style="width: 0.1%; text-align: center; font-size: 12px;">'+value+'</th>';
				});
				div_table += '</tr>';
				div_table += '</thead>';
				div_table += '<tbody>';
				var cnt = 1;
				for (var i = 0; i < navigations.length; i++) {
					div_table += '<tr>';
					div_table += '<td style="width: 0.1%; text-align: center; font-size: 12px;">'+cnt+'</td>';
					div_table += '<td style="width: 2%; text-align: left; font-size: 12px; font-weight: bold;">'+navigations[i].navigation_name+'</td>';
					for (var j = 0; j < roles.length; j++) {
						var found = false;
						for (var k = 0; k < permissions.length; k++) {
							if(navigations[i].navigation_name == permissions[k].navigation_name && roles[j] == permissions[k].position){
								var role_code = permissions[k].role_code.split(',');
								div_table += '<td style="width: 0.1%; text-align: center; font-size: 12px; cursor: pointer;" onclick="modalRole(\''+navigations[i].navigation_name+'\',\''+roles[j]+'\')">';
								for (var l = 0; l < role_code.length; l++) {
									div_table += role_code[l]+'<br>';									
								}
								div_table += '</td>';
								found = true;	
							}
						}
						if(found == false){
							div_table += '<td style="width: 0.1%; text-align: left; font-size: 12px; cursor: pointer;" onclick="modalRole(\''+navigations[i].navigation_name+'\',\''+roles[j]+'\')"></td>';
						}				
					}
					div_table += '</tr>';
					cnt += 1
				};
				div_table += '</tbody>';
				div_table += '</table>';
				div_table += '</div>';

				$('#div_table').append(div_table);


				$('#tableResumeRole').DataTable({
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

				$('#tableAccount').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 5, 25, 50, -1 ],
					[ '5 rows', '25 rows', '50 rows', 'Show all' ]
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

				$('#tableRole').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 5, 25, 50, -1 ],
					[ '5 rows', '25 rows', '50 rows', 'Show all' ]
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
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				return false;
			}
		});
}

function detail(cat, data){
	console.log(cat);
}

function updateUnmatch(){
	if(confirm("Update stocktaking unmatch?")){
		$('#loading').show();
		update_actions = [];
		update_remarks = [];

		$.each(actions, function(key, value){
			var action = $('#action_'+value).val();
			if(action.length > 0){
				update_actions.push({
					"id": value,
					"action": action
				});
			}
		});

		$.each(remarks, function(key, value){
			var remark = $('#remark_'+value).val();
			if(remark.length > 0){
				update_remarks.push({
					"id": value,
					"remark": remark
				});
			}
		});

		var data = {
			update_actions:update_actions,
			update_remarks:update_remarks
		}
		$.get('{{ url("update/mis/stocktaking_account") }}', data, function(result, status, xhr){
			if(result.status){
				$('#modalUnmatch').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
				audio_ok.play();
				fetchData(); 
				return false;
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				return false;				
			}
		});
	}
	else{
		return false;
	}
}

function unmatch(cat){

	if(cat == 'GITHUB'){
		$('#modalGithub').modal('show');
	}
	else{
		var tableUnmatchBody = "";
		$('#tableUnmatchBody').html("");

		actions = [];
		remarks = [];

		var cnt = 0;
		$.each(accounts, function(key, value){
			if(value.category == cat && value.status == 'Unmatch'){
				cnt += 1;
				if (cnt % 2 === 0 ) {
					color = '#fffcb7';
				}
				else {
					color = '#ffd8b7';
				}
				tableUnmatchBody += '<tr>';
				tableUnmatchBody += '<td style="text-align: center; background-color: '+color+';" rowspan="2">'+cnt+'</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';" rowspan="2">'+value.employee_id+'</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';" rowspan="2">'+value.employee_name+'</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';" rowspan="2">'+value.role_code+'<br>'+value.role_name+'</td>';
				tableUnmatchBody += '<td style="text-align: left;"">Sunfish Data</td>';
				tableUnmatchBody += '<td style="text-align: left; ">'+value.employee_position+'</td>';
				tableUnmatchBody += '<td style="text-align: left; ">'+value.employee_department+'</td>';
				tableUnmatchBody += '<td style="text-align: center; ">'+value.employee_status+'</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';" rowspan="2">';
				if(value.adjusted_by == null || value.adjusted_by == "" ){
					actions.push(value.id);
					tableUnmatchBody += '<select class="form-control select2" id="action_'+value.id+'">';
					tableUnmatchBody += '<option value=""></option>';
					tableUnmatchBody += '<option value="Adjusted">Adjusted</option>';
					tableUnmatchBody += '<option value="Deactivated">Deactivated</option>';
					tableUnmatchBody += '<option value="Allowed">Allowed</option>';
					tableUnmatchBody += '</select>';
				}
				else{
					tableUnmatchBody += '<span style="color: green; font-weight: bold;">'+value.action+' ('+value.updated_at+')</span><br>By '+value.adjusted_by_name;
				}
				tableUnmatchBody += '</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';" rowspan="2">';
				if(value.remark == null || value.remark == "" ){
					remarks.push(value.id);
					tableUnmatchBody += '<textarea class="form-control select2" id="remark_'+value.id+'"></textarea>';
				}
				else{
					tableUnmatchBody += value.remark;
				}
				tableUnmatchBody += '</td>';
				tableUnmatchBody += '</tr>';
				tableUnmatchBody += '<tr>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';">Actual Setting MIRAI</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';">'+value.role_position+'</td>';
				tableUnmatchBody += '<td style="text-align: left; background-color: '+color+';">'+value.role_department+'</td>';
				tableUnmatchBody += '<td style="text-align: center; background-color: '+color+';">'+value.user_status+'</td>';
				tableUnmatchBody += '</tr>';
			}
		});
		$('#tableUnmatchBody').append(tableUnmatchBody); 
		$('#modalUnmatch').modal('show');
	}
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
