@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
		<button class="btn btn-info pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Tambahkan Data
		</button>
	</h1>
	<ol class="breadcrumb">
		<li>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
				</div>
				<div class="box-body" style="padding-top: 0;">
					<table id="jobTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 1%">Employee ID</th>
								<th style="width: 3%">Name</th>
								<th style="width: 1%">Total Job</th>
								<th style="width: 3%">Action</th>
							</tr>
						</thead>
						<tbody id="bodydataJob">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Tambah OP GS</h1>
					</div>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="box-body">
							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
										<div class="form-group" id="selectEmp">
											<label>Karyawan<span class="text-red">*</span></label>
											<select class="form-control selectEmp" id="emp_gs" name="emp_gs" data-placeholder='Pilih Karyawan' style="width: 100%" onchange="checkEmp(this.value,'creates')">
												<option value="">&nbsp;</option>
												@foreach($emps as $row)
												<option value="{{$row->employee_id}}-{{$row->name}}">{{$row->employee_id}} - {{$row->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label id="label_section">Name<span class="text-red">*</span></label>
										<input type="text" id="emp_name" name="emp_name" class="form-control" placeholder="name" readonly>
									</div>
								</div>
								<div style="padding-bottom:10px;" align="right">
									<button class="btn btn-success" onclick="modaldata()">
										<i class="fa fa-plus"></i>
									</button>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableEmployee">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%; text-align: center;">kategori</th>
											<th style="width: 2%; text-align: center;">lokasi</th>
											<th style="width: 6%; text-align: center;">Job</th>
											<th style="width: 1%; text-align: center;">Action</th>
										</tr>
									</thead>
									<tbody id="tableEmployeeBody">
									</tbody>
									<tfoot style="background-color: RGB(252, 248, 227);">
										<tr>
											<th>Total: </th>
											<th colspan="3" id="countTotal"></th>
										</tr>
									</tfoot>
								</table>
							</div>	
							
						 <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="addJob()">SIMPAN</button>
                        </div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalloc" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body no-padding">
					<h4 id="judul_ng" style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">Pilih Kategori</h4>
					<div class="row">
						<div class="col-xs-12" id="AreaDetailFix" style="display: none;">
							<center><button class="btn btn-primary" style="width: 99%;font-size: x-large;height:50px;" id="point_area_fix" onclick="getArea()">
							</button></center>
						</div>

						<div class="col-xs-12" id="AreaDetail">
							@foreach($category as $cat)
							<div class="col-xs-4" id="ngDetailFix1" style="padding-top: 10px;padding-left:0px;padding-right:0px">
								<center><button class="btn btn-primary" style="width: 99%;font-size: x-large;height:50px;" id="{{$cat->category}}" onclick="getAreaChange(this.id)" >{{$cat->category}}	
								</button></center>
							</div>
							<input type="hidden" id="ddd" value="{{$cat->category}}">
							@endforeach	
						</div>
					</div>
					
					<h4 id="judul_loc" style="display: none; padding-top: 10px;font-weight: bold;text-align:center;background-color: #ffd375;padding: 5px">Pilih Lokasi</h4>
					<div class="row">
						<div class="col-xs-12" id="LocBody" style="display: none;">
							<center><button class="btn btn-warning" id="point_lokasi_fix" style="width: 99%;font-size: 25px;" onclick="getLokasi(this.id)"></button></center>
						</div>
						<div class="col-xs-12" id="LocBodyFix" style="display: none;padding-top: 5px">
						</div>
					</div>

					<div class="form-group" id="divPekerjaan" style="display: none;">
						<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #75ff9f;padding: 5px">Pilih List Pekerjaan</h4>
						<select class="form-control selecJob" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="job_id_before_select" data-placeholder="Pilih List Pekerjaan"></select>
					</div>

					<div style="padding-top: 10px">
						<div class="col-xs-6" style="padding-left: 0px;padding-right: 10px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white;" data-dismiss="modal" aria-label="Close"  class="btn btn-danger">CANCEL</button>
						</div>
						<div class="col-xs-6" style="padding-left: 10px;padding-right: 0px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confInput()" class="btn btn-success">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetail" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL JOBLIST GS</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="detail_job" class="table table-hover table-striped table-bordered" style="font-weight: bold;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>ID</th>
									<th>Employee Id</th>
									<th>Kategori</th>
									<th>Lokasi</th>
									<th>Job</th>
								</tr>
							</thead>
							<tbody id="detail_job_body">
							</tbody>
							<tfoot id="footDetail">
								<tr>
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
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Job GS</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="box-body">
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="form-group">
									<div class="form-group" id="selectEmp">
										<label>Karyawan<span class="text-red">*</span></label>
										<input type="text" id="emp_gs_edit" name="emp_gs_edit" class="form-control" placeholder="employee_id" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label id="label_section">Name<span class="text-red">*</span></label>
									<input type="text" id="emp_name_edit" name="emp_name_edit" class="form-control" placeholder="name" readonly>
								</div>
							</div>
							<!-- <div style="padding-bottom:10px;" align="right">
								<button class="btn btn-success" onclick="modaldata()">
									<i class="fa fa-plus"></i>
								</button>
							</div>
							<table class="table table-hover table-bordered table-striped" id="tableEmployeeEdit">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%; text-align: center;">Kategori</th>
										<th style="width: 2%; text-align: center;">Lokasi</th>
										<th style="width: 6%; text-align: center;">Job</th>
										<th style="width: 1%; text-align: center;">Action</th>
									</tr>
								</thead>
								<tbody id="tableEmployeeBodyEdit">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total: </th>
										<th colspan="3" id="countTotalEdit"></th>
									</tr>
								</tfoot>
							</table> -->
							<!-- <div class="form-group row" style="border-top:2px solid red;margin-top: 20px">
							</div> -->
							<table class="table table-hover table-bordered" id="tableEmployeeEditDetail">
								<thead style="background-color: #9932CC; color: black;">
									<tr>
										<th style="width: 1%; text-align: center;">Kategori</th>
										<th style="width: 2%; text-align: center;">Lokasi</th>
										<th style="width: 6%; text-align: center;">Job</th>
										<th style="width: 1%; text-align: center;">Action</th>
									</tr>
								</thead>
								<tbody id="tableEmployeeBodyEditDetail">
								</tbody>
								<tfoot id="footDetailEdit">
									<tr>
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
			<!-- <div class="modal-footer">
				<button class="btn btn-success" onclick="editJob()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Simpan</button>
			</div> -->
		</div>
	</div>
</div>
</div>

@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/highcharts.js') }}"></script>
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url('js/icheck.min.js') }}"></script>
<script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var pics = <?php echo json_encode($emps); ?>;
	var data_loc = <?php echo json_encode($area); ?>;
	var data_job = <?php echo json_encode($job); ?>;

	var data_master = [];
	var data_area = [];
	var countDestination = 0;
	var data_gs = [];
	var daily_jobs = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getData();

		$('#emp_name').val('');
		$('#emp_gs').val('');


		$('.selectEmp').select2({
			dropdownParent: $('#selectEmp'),
			allowClear:true,
			tags: true
		});
	});

	function emptyAll() {
		$('#category').val('').trigger('change');
		$('#jig_parent').val('');
		$('#jig_id').val('');
		$('#usage').val('');
		$('#jig_index').val('');
		$('#jig_alias').val('');
		$('#jig_name').val('');
		$('#jig_tag').val('');
		$('#check_period').val('');
		$('#drawing').val('');

		$('#category_edit').val('').trigger('change');
		$('#jig_parent_edit').val('');
		$('#jig_id_edit').val('');
		$('#usage_edit').val('');
		$('#jig_index_edit').val('');
		$('#jig_alias_edit').val('');
		$('#jig_name_edit').val('');
		$('#jig_tag_edit').val('');
		$('#check_period_edit').val('');
		$('#drawing_edit').val('');
	}

	function checkEmp(value) {
		var emp = value.split('-');
		$.each(pics, function(key, value) {
			if (value.employee_id == emp[0]) {
				$('#emp_name').val();
				$('#emp_name').val(value.name);
			}
		});
	}

	function changeCategory(value) {
		if (value === 'KENSA') {
			$('#tagjig').show();
			$('#periodcheck').show();
			$('#type').val('JIG');
			$('#jigusage').hide();
		}else{
			$('#tagjig').hide();
			$('#periodcheck').hide();
			$('#jigusage').show();
			$('#type').val('PART');
		}
	}


	function getArea() {
		$('#LocBody').hide();
		$('#judul_loc').hide();
		$('#LocBodyFix').hide();
		$('#divPekerjaan').hide();
		$('#AreaDetail').show();
		$('#AreaDetailFix').hide();
		$('#point_area_fix').html('');
	}


	function getAreaChange(id) {
		$('#AreaDetail').hide();
		$('#AreaDetailFix').show();
		$('#point_area_fix').html(id);

		$('#LocBodyFix').html('');

		var bodyDetail = "";
		$('#judul_loc').show();
		$('#LocBodyFix').show();

		$.each(data_loc, function(key, value) {
			if (value.category == id) {
				bodyDetail += '<div class="col-xs-3" style="padding-top: 5px;padding-left:0px;padding-right:0px">';
				bodyDetail += '<center><button class="btn btn-warning" id="'+value.area+'" style="width: 99%;font-size: 3vh;" onclick="getLokasiFix(this.id)">'+value.area;
				bodyDetail += '</button></center></div>';
			}
		});

		$('#LocBodyFix').append(bodyDetail);

	}

	function getLokasiFix(loc) {
		$('#LocBody').show();
		$('#LocBodyFix').hide();
		$('#point_lokasi_fix').html(loc);
		$('#divPekerjaan').show();
		$('#job_id_before_select').html('');

		var opbfsel = "";
		opbfsel += '<option value="">Pilih Pekerjaan</option>';
		for (var i = 0; i < data_job.length; i++) {
			if (data_job[i].area == loc) {
				opbfsel += '<option value="'+data_job[i].list_job+'">'+data_job[i].list_job+'</option>';
			}
		}

		$('#job_id_before_select').append(opbfsel);
		$('#job_id_before_select').select2({
			allowClear:true,
			dropdownParent: $('#modalloc'),
			tags: true
		});
	}

	function getLokasi(locs) {
		$('#LocBody').hide();
		$('#LocBodyFix').show();
		$('#point_lokasi_fix').html('');
	}

	function modaldata(){
		$('#LocBody').hide();
		$('#judul_loc').hide();
		$('#LocBodyFix').hide();
		$('#divPekerjaan').hide();
		$('#AreaDetail').show();
		$('#AreaDetailFix').hide();
		$('#point_area_fix').html('');
		$('#modalloc').modal('show');

	}

	function confInput(){
		if(confirm("Apakah Anda Yakin membuat List Pekerjaan?")){
			var list_job = $('#job_id_before_select').val();
			var location = $('#point_lokasi_fix').html();
			var areas = $('#point_area_fix').html();	
			if($.inArray(list_job, data_gs) != -1){
				audio_error.play();
				openErrorGritter('Error!','Pekerjaan sudah ada di list.');
				return false;
			}
			$('#modalloc').modal('hide');

			var tableDestination = "";
			tableDestination += "<tr id='"+countDestination+"'>";
			tableDestination += "<td class='area1'>"+areas+"</td>";  
			tableDestination += "<td class='locs'>"+location+"</td>";
			tableDestination += "<td class='listjob'>"+list_job+"</td>";
			tableDestination += "<td><a href='javascript:void(0)' onclick='remDestination(id,\""+list_job+"\")' id='"+countDestination+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			tableDestination += "</tr>";
			countDestination++;
			data_gs.push(list_job);
			$('#countTotal').text(countDestination);
			$('#tableEmployeeBody').append(tableDestination);

		}
	}


	function remDestination(id,list){
		if(confirm("Are you sure you want to cancel?")){
			data_gs.splice( $.inArray(list), 1 );
			countDestination -= 1;
			$('#countTotal').text(countDestination);
			$('#'+id).remove();
		}

	}

	function getData() {
		$.get('{{ url("fetch/gs/job/daily") }}', function(result, status, xhr){
			if(result.status){
				$('#jobTable').DataTable().clear();
				$('#jobTable').DataTable().destroy();
				$('#bodydataJob').empty();
				var jobTable = '';
				var index = 1;

				daily_jobs.push(result.gs_job_daily);

				$.each(result.gs_jobs, function(key, value) {
					jobTable += '<tr>';
					jobTable += '<td>'+index+'</td>';
					jobTable += '<td>'+value.operator_gs+'</td>';
					jobTable += '<td>'+value.names+'</td>';
					jobTable += '<td>'+value.total_list+'</td>';
					jobTable += '<td><button class="btn btn-info btn-sm" onclick="detailJobData(\''+value.operator_gs+'\')" style="margin-right: 5px"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat</button><button class="btn btn-warning btn-sm" onclick="editJobData(\''+value.operator_gs+'\',\''+value.names+'\')" style="margin-right: 5px"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edit</button></td>';
					jobTable += '</tr>';
					index++;
				});

				$('#bodydataJob').append(jobTable);

				var table = $('#jobTable').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'processing': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}else{
				alert('Retireve Data Failed');
			}
		});
	}




	function addJob() {
		var joblist = [];
		var locs1 = [];
		var areas1 = [];

		if ($('#emp_gs').val() == "") {
			alert('Semua Data Harus Diisi');
			$('#loading').hide();
		}else{
				// $('#loading').show();
			if (confirm('Apakah Anda yakin akan membuat pekerjaan?')) {

				var op = $('#emp_gs').val().split('-');
				var emp_nik = op[0];
				var emp_name = op[1];
				console.log(emp_name);

				$('.locs').each(function(){
					locs1.push($(this).html());
				});
				$('.area1').each(function(){
					areas1.push($(this).html());
				});
				$('.listjob').each(function(){
					joblist.push($(this).html());
				});

				var formData = new FormData();
				formData.append('emp_name', emp_name);
				formData.append('op_nik', emp_nik);
				formData.append('joblist', joblist);
				formData.append('locs1', locs1);
				formData.append('areas1', areas1);

				$.ajax({
					url:"{{ url('create/gs/joblist') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success:function(data)
					{
						$('#loading').hide();
						$('#create_modal').modal('hide');
						// emptyAll();
						// getData();
						openSuccessGritter('Success!','Success Input Data');
						location.reload();
					}
				});
			}
		}
		}

		function detailJobData(id) {
			$('#detail_job').DataTable().clear();
			$('#detail_job').DataTable().destroy();
			$('#detail_job_body').empty();
			var detailjob = "";
			var index = 1;

			for (var i = 0; i < daily_jobs[0].length; i++) {	
				if (daily_jobs[0][i].operator_gs == id) {
					detailjob += '<tr>';
					detailjob += '<td>'+index+'</td>';
					detailjob += '<td>'+daily_jobs[0][i].operator_gs+'</td>';
					detailjob += '<td>'+daily_jobs[0][i].category+'</td>';
					detailjob += '<td>'+daily_jobs[0][i].area+'</td>';
					detailjob += '<td>'+daily_jobs[0][i].list_job+'</td>';
					detailjob += '</tr>';
					index++;
				}
			}

			$('#detail_job_body').append(detailjob);


			$('#detail_job tfoot th').each(function() {
				var title = $(this).text();
				$(this).html(
					'<input style="text-align: center;" type="text" placeholder="Search ' +
					title +
					'" size="4"/>');
			});

			var table = $('#detail_job').DataTable({
				"order": [],
				'dom': 'Bfrtip',
				'responsive': true,
				'lengthMenu': [
				[10, 25, 50, -1],
				['10 rows', '25 rows', '50 rows', 'Show all']
				],
				"columnDefs": [{
					"targets": [4],
					"className": "text-left"
				}],
				'buttons': {
					buttons: [{
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
					]
				},
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				'ordering' :false,
				initComplete: function() {
					this.api()
					.columns([2, 3])
					.every(function(dd) {
						var column = this;
						var theadname = $("#detail_job th").eq([dd])
						.text();
						var select = $(
							'<select><option value="" style="font-size:11px;">All</option></select>'
							)
						.appendTo($(column.footer()).empty())
						.on('change', function() {
							var val = $.fn.dataTable.util
							.escapeRegex($(this)
								.val());

							column.search(val ? '^' + val + '$' :
								'', true,
								false)
							.draw();
						});
						column
						.data()
						.unique()
						.sort()
						.each(function(d, j) {
							var vals = d;
							if ($("#detail_job th").eq([dd])
								.text() ==
								'Category') {
								vals = d.split(' ')[0];
						}
						select.append(
							'<option style="font-size:11px;" value="' +
							d + '">' + vals + '</option>');
					});
					});
				},
			});

			table.columns().every(function() {
				var that = this;

				$('input', this.footer()).on('keyup change', function() {
					if (that.search() !== this.value) {
						that
						.search(this.value)
						.draw();
					}
				});
			});

			$('#detail_job tfoot tr').appendTo('#detail_job thead');

			$('#modalDetail').modal('show');
		}

		function editJobData(nik,name) {

			$('#emp_gs_edit').val(nik);
			$('#emp_name_edit').val(name);

			$('#tableEmployeeEditDetail').DataTable().clear();
			$('#tableEmployeeEditDetail').DataTable().destroy();
			$('#tableEmployeeBodyEditDetail').empty();
			var detailjobedit = "";

			for (var i = 0; i < daily_jobs[0].length; i++) {	
				if (daily_jobs[0][i].operator_gs == nik) {
					detailjobedit += '<tr>';
					detailjobedit += '<td style="width:1%;">'+daily_jobs[0][i].category+'</td>';
					detailjobedit += '<td style="width:1%;">'+daily_jobs[0][i].area+'</td>';
					detailjobedit += '<td style="width:4%;">'+daily_jobs[0][i].list_job+'</td>';
					detailjobedit += '<td style="width:1%;"><button class="btn btn-danger btn-sm" onclick="deleteJob(\''+daily_jobs[0][i].list_job+'\')" style="margin-right: 5px"><i class="fa fa-trash-o"></i>&nbsp;&nbsp;Delete</button></td>';
					detailjobedit += '</tr>';
				}
			}

			$('#tableEmployeeBodyEditDetail').append(detailjobedit);

			$('#tableEmployeeEditDetail tfoot th').each(function() {
				var title = $(this).text();
				$(this).html(
					'<input style="text-align: center;" type="text" placeholder="Search ' +
					title +
					'" size="4"/>');
			});

			var table = $('#tableEmployeeEditDetail').DataTable({
				"order": [],
				'dom': 'Bfrtip',
				'responsive': true,
				'lengthMenu': [
				[10, 25, 50, -1],
				['10 rows', '25 rows', '50 rows', 'Show all']
				],
				"columnDefs": [{
					"targets": [2],
					"className": "text-left"
				}],
				'buttons': {
					buttons: [{
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
					]
				},
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				'ordering' :false,
				initComplete: function() {
					this.api()
					.columns([0, 1])
					.every(function(dd) {
						var column = this;
						var theadname = $("#tableEmployeeEditDetail th").eq([dd])
						.text();
						var select = $(
							'<select><option value="" style="font-size:11px;">All</option></select>'
							)
						.appendTo($(column.footer()).empty())
						.on('change', function() {
							var val = $.fn.dataTable.util
							.escapeRegex($(this)
								.val());

							column.search(val ? '^' + val + '$' :
								'', true,
								false)
							.draw();
						});
						column
						.data()
						.unique()
						.sort()
						.each(function(d, j) {
							var vals = d;
							if ($("#tableEmployeeEditDetail th").eq([dd])
								.text() ==
								'Category') {
								vals = d.split(' ')[0];
						}
						select.append(
							'<option style="font-size:11px;" value="' +
							d + '">' + vals + '</option>');
					});
					});
				},
			});

			table.columns().every(function() {
				var that = this;

				$('input', this.footer()).on('keyup change', function() {
					if (that.search() !== this.value) {
						that
						.search(this.value)
						.draw();
					}
				});
			});

			$('#tableEmployeeEditDetail tfoot tr').appendTo('#tableEmployeeEditDetail thead');

			$('#edit_modal').modal('show');
		}


		function deleteJob(id) {
			if (confirm('Apakah Anda yakin akan menghapus pekerjaan?')) {
    // $('#loading').show();

    var emps = $('#emp_gs_edit').val();
    var data = {
    	id:id,
    	emps:emps
    }
    $.get('{{ url("delete/joblist/gs") }}', data, function(result, status, xhr){
    	if(result.status){
    		$('#loading').hide();
    		audio_ok.play();
    		openSuccessGritter('Success','Success dihapus');
    		$('#edit_modal').modal('hide');
    		getData();
    		location.reload();

    	} else {
    		$('#loading').hide();
    		audio_error.play();
    		openErrorGritter('Error!',result.message);
    	}
    })
}
}


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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