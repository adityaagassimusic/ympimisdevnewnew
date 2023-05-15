@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
#loading, #error { display: none; }
thead input {
	width: 100%;
	padding: 3px;
	box-sizing: border-box;
}
thead>tr>th{
	text-align:center;
	overflow:hidden;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
}
th:hover {
	overflow: visible;
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
	border:1px solid black;
	vertical-align: middle;
	padding:0;
	font-size: 13px;
	text-align: center;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid black;
	padding:0;
}
td{
	overflow:hidden;
	text-overflow: ellipsis;
}

input[type=number] {
-moz-appearance:textfield; /* Firefox */
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
	background-color: #ffd8b7;
}

.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
	background-color: #FFD700;
}
#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="modalRequest();"><i class="fa fa-list"></i> Request Manpower</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">HUMAN RESOURCE DEPARTMENT</span>
							<span style="font-size: 25px;color: black;width: 25%;">人事部</span>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 10px">
						<div id="container1"></div>
					</div>
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%">
						<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
						<table class="table table-hover table-striped table-bordered" id="tableResumeReq">
							<thead style="background-color: rgb(126,86,134); color: white;">
								<tr>
									<th style="width: 5%">No</th>
									<th style="width: 10%">Request Id</th>
									<th style="width: 10%">Status Karyawan</th>
									<th style="width: 15%">Departemen</th>
									<th style="width: 10%">Pembuat</th>
									<th style="width: 5%">Tanggal Pengajuan</th>
									<th style="width: 5%">Tanggal Masuk</th>
									<th style="width: 30%">Progress Approval</th>
									<th style="width: 10%">Status</th>
								</tr>
							</thead>
							<tbody id="tableBodyResumeReq"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalRequest" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<span style="font-weight: bold; font-size: 1.2vw;">Permintaan Manpower</span>
					<hr style="margin-top: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Posisi<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" id="createPosition" data-placeholder="Pilih Posisi" style="width: 100%;">
										<option></option>
										@foreach($positions as $position)
										<option value="{{ $position }}">{{ $position }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
								<div class="col-sm-7">
									<select class="form-control select2" id="createDepartment" data-placeholder="Pilih Department" onchange="SelectEmployee(this.value)" style="width: 100%;">
										<option></option>
										@foreach($departments as $department)
										<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input id="createMale" name="createMale" type="number" class="form-control numpad" value="0">
										<span class="input-group-addon" style="width: 100px;">Laki-laki</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label"><span class="text-red"> </span> </label>
								<div class="col-sm-3">
									<div class="input-group">
										<input id="createFemale" name="createFemale" type="number" class="form-control numpad" value="0">
										<span class="input-group-addon" style="width: 100px;">Perempuan</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Alasan Penambahan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Masukkan Alasan Penambahan" id="createReason"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Perkiraan Tanggal Masuk<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="createStartDate" value="{{ $date }}" readonly>
								</div>
								<div class="col-sm-6">
									<span class="text-red">*</span><span>Maksimal kurang dari 3 minggu</span>
								</div>
							</div>
						</div>				
					</form>
					<span style="font-weight: bold; font-size: 1.2vw;">Kualifikasi Umum</span>
					<hr style="margin-top: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12">
							<div class="col-xs-6">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Usia<span class="text-red"></span> :</label>
									<div class="col-sm-5">
										<div class="input-group">
											<span class="input-group-addon" style="width: 50px;">Min</span>
											<input type="number" class="form-control numpad" placeholder="Usia" value="0" id="createMinAge" min="0">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label"><span class="text-red"></span> </label>
									<div class="col-sm-5">
										<div class="input-group">
											<span class="input-group-addon" style="width: 50px;">Max</span>
											<input type="number" class="form-control numpad" placeholder="Usia" value="0" id="createMaxAge" min="0">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Status<span class="text-red"></span> :</label>
									<div class="col-sm-7">
										<select class="form-control select2" id="createMarriageStatus" data-placeholder="Pilih Status" style="width: 100%;">
											<option></option>
											<option value="Belum Menikah">Belum Menikah</option>
											<option value="Sudah Menikah">Sudah Menikah</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Domisili<span class="text-red"></span> :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" placeholder="Masukkan Domisili" id="createDomicile">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-4 control-label">Pengalaman<span class="text-red"></span> :</label>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="number" class="form-control numpad" placeholder="Qty" value="0" id="createWorkExperience" min="0">
											<span class="input-group-addon" style="width: 60px;">Tahun</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jenjang<span class="text-red">*</span> :</label>
									<div class="col-sm-7">
										<select class="form-control select3" multiple="" id="createEducationLevel" data-placeholder="Pilih Jenjang" style="width: 100%;">
											<option value="SMA">SMA</option>
											<option value="SMK">SMK</option>
											<option value="SMA">D1</option>
											<option value="SMK">D2</option>
											<option value="SMA">D3</option>
											<option value="SMK">D4</option>
											<option value="SMK">S1</option>
											<option value="SMK">S2</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jurusan<span class="text-red"></span> :</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" placeholder="Masukkan Jurusan" id="createMajor">
									</div>
								</div>
							</div>
						</div>
					</form>
					<span style="font-weight: bold; font-size: 1.2vw;">Kualifikasi Khusus</span>
					<hr style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="col-xs-6">
							<div style="margin-bottom: 10px;">
								<span style="font-weight: bold; font-size: 14px;">Keahlian/ketrampilan yang diutamakan<span class="text-red">*</span></span>
								<button class="btn btn-success btn-xs pull-right" onclick="addSkill()"><i class="fa fa-plus"></i></button>
							</div>
							<div id="skill">
							</div>
						</div>
						<div class="col-xs-6">
							<div style="margin-bottom: 10px;">
								<span style="font-weight: bold; font-size: 14px;">Persyaratan Lainnya </span>
								<button class="btn btn-success btn-xs pull-right" onclick="addRequirement()"><i class="fa fa-plus"></i></button>
							</div>
							<div id="requirement">
							</div>
						</div>
					</div>

					<div class="col-md-12" style="margin-bottom : 5px;" id="select">
						<span style="font-weight: bold; font-size: 1.2vw;">Employee</span>
						<hr style="margin-top: 5px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="VeteranEmployee()">Rekontrak Employee</button>
						<button class="btn btn-success pull-left" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="NewEmployee()">New Employee</button>
					</div>

					<div class="col-md-12" style="margin-bottom : 5px;" id="btn-new">
						<hr style="margin-top: 5px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="NewEmployee()">New Employee</button>
					</div>

					<div class="col-md-12" style="margin-bottom : 5px;" id="btn-veteran">
						<hr style="margin-top: 5px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="VeteranEmployee()">Rekontrak Employee</button>
					</div>

					<div id="new">
					<span style="font-weight: bold; font-size: 1.2vw;">Request Karyawan Baru</span>
					<hr style="margin-top: 5px;">
					<div class="col-md-12" style="margin-bottom : 5px;">
						<div class="col-xs-6" style="padding:0">
							<input type="checkbox" name="new_employee" id="new_employee" value="Request Karyawan Baru"> Request Karyawan Baru
						</div>
						<div class="col-xs-3" style="padding:0">
							<select class="form-control select9" id="loc_penempatan" name="loc_penempatan" data-placeholder='Sub Group' style="width: 100%" required>
							</select>
						</div>
						<div class="col-xs-3" style="padding:0; padding-left: 5px">
							<input type="text" class="form-control" name="process_penempatan" id="process_penempatan" placeholder='Process' required>
						</div>
					</div>
					</div>

					<div id="veteran">
						<span style="font-weight: bold; font-size: 1.2vw;">Request Rekontrak Employee</span>
						<hr style="margin-top: 5px;">
						<input type="hidden" name="lop" id="lop" value="1">
						<input type="hidden" name="req_id" id="req_id">
						<div id="1" class="col-md-12" style="margin-bottom : 5px;">
							<div class="col-xs-3" style="padding:0;">
								<select class="form-control select5" id="description1" name="description1" data-placeholder='Pilih Nama' style="width: 100%" required>
								</select>
							</div>
							<div class="col-xs-3" style="padding:0; padding-left: 5px">
								<select class="form-control select6" id="penempatan1" name="penempatan1" data-placeholder='Sub Group' style="width: 100%" required>
								</select>
							</div>
							<div class="col-xs-3" style="padding:0; padding-left: 5px">
								<input type="text" class="form-control" name="process1" id="process1" placeholder='Process' required>
							</div>
							<div class="col-xs-1" style="padding:0; padding-left: 5px">
								<input type="text" class="form-control" placeholder="/Bulan" name="durasi1" id="durasi1">
							</div>
							<div class="col-xs-2" style="padding:0; padding-left: 5px;">
								<button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
							</div>  
							<div id="tambah"></div>
						</div>
					</div>

					<span style="font-weight: bold; font-size: 1.2vw;">Catatan</span><br>
					<span style="font-size: 95%;">(Cantumkan NIK, Nama, Departemen Rekomendasi)</span>
					<hr style="margin-top: 5px;">
					<div class="col-xs-12" style="margin-bottom: 15px;">
						<textarea class="form-control" rows="3" placeholder="Masukkan Catatan" id="createNote"></textarea>
					</div>
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Kembali</button>
					<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="confirmRequest()">Konfirmasi</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalApproval">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #bb8fce;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: black">Detail Request Man Power</h1>
				</div>
				<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%">
					<!-- <center><h2 style="font-weight: bold">Detail Request Man Power</h2></center> -->
					<table class="table table-hover table-striped table-bordered" id="tableResumeApproval">
						<thead style="background-color: rgb(126,86,134); color: white;">
							<tr>
								<th style="width: 10%">Request ID</th>
								<th style="width: 10%">Posisi</th>
								<th style="width: 20%">Departemen</th>
								<th style="width: 10%">Status Karyawan</th>
								<th style="width: 5%">L</th>
								<th style="width: 5%">P</th>
								<th style="width: 10%">Alasan</th>
								<th style="width: 10%">Tanggal Masuk</th>
								<th style="width: 10%">Status</th>
								<th style="width: 10%">Domisili</th>
							</tr>
						</thead>
						<tbody id="tableBodyResumeApproval"></tbody>
					</table>
				</div>    
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	var no = 2;
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			dropdownParent:$('#modalRequest')
		});
		$('.select3').select2({
			dropdownParent:$('#modalRequest')
		});
		$('.select9').select2({
			dropdownParent:$('#new')
		});
		$('.select5').select2({
			dropdownParent:$('#veteran')
		});
		$('.select6').select2({
			dropdownParent:$('#veteran')
		});
		// $('#createStartDate').datepicker({
		// 	autoclose: true,
		// 	todayHighlight: true,
		// 	format: "yyyy-mm-dd"
		// });
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		DetailReq();
		$('#createDepartment').val('{{$emp_dept->department}}').trigger('change');
		$('#select').show();
		$('#btn-new').hide();
		$('#btn-veteran').hide();
		$('#new').hide();
		$('#veteran').hide();
	});	

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var skill_count = 0;
	var skills = [];
	var requirement_count = 0;
	var requirements = [];
	var emps = 	'';
	var sec = '';
	var new_karyawan = '';

	function VeteranEmployee(){
		$('#select').hide();
		$('#veteran').show();
		$('#new').hide();
		$('#btn-new').show();
		$('#btn-veteran').hide();
	}

	function NewEmployee(){
		$('#select').hide();
		$('#veteran').hide();
		$('#new').show();
		$('#btn-new').hide();
		$('#btn-veteran').show();
	}

	function SelectEmployee(value) {
		var data = {
			createDepartment:$('#createDepartment').val()
		}
		$.get('{{ url("select/veteran/employee") }}',data, function(result, status, xhr){
			if(result.status){
				$('#description1').show();
				$('#description1').html('');
				emps = '';

				emps += '<option value=""></option>';

				$.each(result.employee, function(key, value) {
					emps += '<option value="'+value.old_nik+'/'+value.name+'">'+value.old_nik+' - '+value.name+'</option>';
				});

				$('#penempatan1').show();
				$('#penempatan1').html('');
				sec = '';
				
				sec += '<option value=""></option>';

				$.each(result.sub_grp, function(key, value) {
					sec += '<option value="'+value.sub_group+'">'+value.sub_group+'</option>';
				});

				$('#loc_penempatan').show();
				$('#loc_penempatan').html('');
				loc_penempatan = '';
				
				loc_penempatan += '<option value=""></option>';

				$.each(result.sub_grp, function(key, value) {
					loc_penempatan += '<option value="'+value.sub_group+'">'+value.sub_group+'</option>';
				});

				$('#description1').append(emps);
				$('#description1').val('').trigger('change');
				$('#penempatan1').append(sec);
				$('#penempatan1').val('').trigger('change');
				$('#loc_penempatan').append(loc_penempatan);
				$('#loc_penempatan').val('').trigger('change');
			}
		});
        // if (value.length > 0 ) {
            
        // }else{
        //     openErrorGritter('Error!','Data Tidak Ditemukan.');
        // }
    }

	function tambah(id,lop) {
			var id = id;
			var lop = "";
			if (id == "tambah"){
				lop = "lop";
			}else{
				lop = "lop2";
			}
			var divdata = $("<input type='text' name='lop' id='lop' value='"+no+"' hidden><div id='"+no+"' class='col-md-12' style='padding: 0; padding-top: 5px'><div class='col-xs-3' style='padding:0;'><select class='form-control select7' id='description"+no+"' name='description"+no+"' data-placeholder='Pilih Nama' style='width: 100%'></select></div><div class='col-xs-3' style='padding:0; padding-left: 5px'><select class='form-control select8' id='penempatan"+no+"' name='penempatan"+no+"' data-placeholder='Penempatan' style='width: 100%' required></select></div><div class='col-xs-3' style='padding:0; padding-left: 5px'><input type='text' class='form-control' name='process"+no+"' id='process"+no+"' placeholder='Process' required></div><div class='col-xs-1' style='padding:0; padding-left: 5px'><input type='text' class='form-control' placeholder='/Bulan' name='durasi"+no+"' id='durasi"+no+"''></div><div class='col-xs-2' style='padding:0; padding-left: 5px'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i></button>&nbsp;<button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");
			$("#"+id).append(divdata);

			$('#description'+no+'').append(emps);
			$('#description'+no+'').val('').trigger('change');
			$('#penempatan'+no+'').append(sec);
			$('#penempatan'+no+'').val('').trigger('change');

		$('#lop').val(no);
		$('.select7').select2({
			dropdownParent:$('#veteran')
		});
		$('.select8').select2({
			dropdownParent:$('#veteran')
		});
		no+=1;
	}

	function kurang(elem,lop) {
		var lop = lop;
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#description"+newid).attr("name","description"+oldid);

		jQuery("#description"+newid).attr("id","description"+oldid);
		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) { 
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			jQuery("#"+newid).attr("id",oldid);
			jQuery("#description"+newid).attr("name","description"+oldid);
			jQuery("#description"+newid).attr("id","description"+oldid);
		}
		document.getElementById(lop).value = a;
	}

	function confirmRequest(){
		$("#loading").show();

		var position = $('#createPosition').val();
		var department = $('#createDepartment').val();
		var quantity_male = $('#createMale').val();
		var quantity_female = $('#createFemale').val();
		var reason = $('#createReason').val();
		var start_date = $('#createStartDate').val();
		var min_age = $('#createMinAge').val();
		var max_age = $('#createMaxAge').val();			
		var marriage_status = $('#createMarriageStatus').val();
		var domicile = $('#createDomicile').val();
		var work_experience = $('#createWorkExperience').val();
		var education_level = $('#createEducationLevel').val();
		var major = $('#createMajor').val();
		var note = $('#createNote').val();
		// var new_employee = $('#new_employee').val();
		// var new_employee = $("input[name='new_employee']:checked");
		var new_employee = '';
		$("input[name='new_employee']:checked").each(function (i) {
            new_employee = $(this).val();
        });
		var loc_penempatan = $('#loc_penempatan').val();
		var process_penempatan = $('#process_penempatan').val();

		var skill = "";
		var requirement = "";
		var status_at = "";

		if(skills.length > 0){
			$.each(skills, function(key, value){
				if($('#skill_'+value).val() != ""){
					skill += $('#skill_'+value).val();
					skill += ";";					
				}
			});
		}

		if(requirements.length > 0){
			$.each(requirements, function(key, value){
				if($('#requirement_'+value).val() != ""){
					requirement += $('#requirement_'+value).val();
					requirement += ";";
				}
			});
		}

		var employee = [];
		for(var i = 1; i <= $('#lop').val(); i++){
			if($('#description'+i).val() != ""){
				employee.push($('#description'+i).val());
			}
		}

		var penempatan = [];
		for(var i = 1; i <= $('#lop').val(); i++){
			if($('#penempatan'+i).val() != ""){
				penempatan.push($('#penempatan'+i).val());
			}
		}

		var prc_penempatan = [];
		for(var i = 1; i <= $('#lop').val(); i++){
			if($('#process'+i).val() != ""){
				prc_penempatan.push($('#process'+i).val());
			}
		}

		var durasi = [];
		for(var i = 1; i <= $('#lop').val(); i++){
			if($('#durasi'+i).val() != ""){
				durasi.push($('#durasi'+i).val());
			}
		}

		var data = {
			position:position,
			department:department,
			quantity_male:quantity_male,
			quantity_female:quantity_female,
			reason:reason,
			start_date:start_date,
			min_age:min_age,
			max_age:max_age,
			marriage_status:marriage_status,
			domicile:domicile,
			work_experience:work_experience,
			education_level:education_level,
			major:major,
			note:note,
			skill:skill,
			requirement:requirement,
			status_at:status_at,
			employee:employee,
			penempatan:penempatan,
			durasi:durasi,
			new_employee:new_employee,
			loc_penempatan:loc_penempatan,
			process_penempatan:process_penempatan,
			prc_penempatan:prc_penempatan
		}
			// console.log(new_employee);

		$.get('{{ url("input/hr/request_manpower") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				DetailReq();
				reset();
				$('#modalRequest').modal('hide');
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function reset(){
		$('#createPosition').val("").trigger('change');
		$('#createDepartment').val("").trigger('change');
		$('#createMale').val(0);
		$('#createFemale').val(0);
		$('#createReason').val("");
		$('#createStartDate').val("");
		$('#createMinAge').val(0);
		$('#createMaxAge').val(0);
		$('#createMarriageStatus').val("").trigger('change');
		$('#createDomicile').val("");
		$('#createWorkExperience').val(0);
		// $('#createEducationLevel').val("");
		$('#createMajor').val("");
		$('#createNote').val("");
		$("input[name='new_employee']").each(function (i) {
            $('#new_employee')[i].checked = false;
        });
        $('#createDepartment').val('{{$emp_dept->department}}').trigger('change');
		$('#select').show();
		$('#btn-new').hide();
		$('#btn-veteran').hide();
		$('#new').hide();
		$('#veteran').hide();
		$('#loc_penempatan').val("").trigger('change');
		$('#process_penempatan').val("");
		$('#process1').val("");
		$('#durasi').val("");
	}

	function modalRequest(){
		$('#modalRequest').modal('show');
	}

	function modalApproval(){
		$('#modalApproval').modal('show');
	}

	function addRequirement(){
		var requirement = "";
		requirement_count += 1;

		requirement += '<div class="form-group">';
		requirement += '<input type="text" class="form-control" id="requirement_'+requirement_count+'">';
		requirement += '<div>';

		requirements.push(requirement_count);
		$('#requirement').append(requirement);
	}

	function addSkill(){
		var skillData = "";
		skill_count += 1;

		skillData += '<div class="form-group">';
		skillData += '<input type="text" class="form-control" id="skill_'+skill_count+'">';
		skillData += '<div>';

		skills.push(skill_count);
		$('#skill').append(skillData);
	}

	function truncate(str, n){
		return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
	};

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function DetailReq(req_id){
		var data = {
			req_id:req_id
		};
		$.get('<?php echo e(url("human_resource/resume_request")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#tableResumeReq').DataTable().clear();
				$('#tableResumeReq').DataTable().destroy();
				var tableData = '';
				$('#tableBodyResumeReq').html("");
				$('#tableBodyResumeReq').empty();
				fillChart();

				var urlreport = '{{ url("human_resource/detail_pekerjaan") }}';
        // var approvals = modalApproval();

        var count = 1;
        
        $.each(result.resumes, function(key, value) {
        	var  appr = value.approval.split(',');
        	var  status = value.status.split(',');

        	tableData += '<tr onclick="DetailApproval(\''+value.request_id+'\')">';
        	tableData += '<td>'+ count +'</td>';
        	tableData += '<td>'+ value.request_id +'</td>';
        	tableData += '<td>'+ value.employment_status +'</td>';
        	tableData += '<td>'+ value.department +'</td>';
        	tableData += '<td>'+ value.name +'</td>';
        	tableData += '<td>'+ value.created_at +'</td>';
        	tableData += '<td>'+ value.start_date +'</td>';
        	tableData += '<td>';
        	for(var i = 0; i < appr.length; i++){
        		if (i == appr.length-1){
        			if (status[i] == 'Approved') {
        				tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+appr[i]+'</span>';  
        			}
        			else if(status[i] == 'none'){
        				tableData += '';  
        			}
        			else{
        				tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black;">'+appr[i]+'</span>'; 
        			}
        		}else{
        			if (status[i] == 'Approved') {
        				tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+appr[i]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  
        			}
        			else if(status[i] == 'none'){
        				tableData += '';  
        			}
        			else{
        				tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black;">'+appr[i]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; '; 
        			}
        		}
        	}
        	tableData += '</td>';
        	tableData += '<td>'+ value.remark +'</td>';
          tableData += '</tr>';
          count += 1;
        });
        $('#tableBodyResumeReq').append(tableData);
        var tableResumeReq = $('#tableResumeReq').DataTable({
        	'dom': 'Bfrtip',
        	'responsive':true,
        	'lengthMenu': [
        	[10, 25, 50, -1], [10, 25, 50, "All"]
        	],
        	'buttons': {
        		buttons:[
        		{
        			extend: 'pageLength',
        			className: 'btn btn-default',
        		}
        		]
        	},
        	'paging': true,
        	'lengthChange': false,
        	'pageLength': 10,
        	'searching': true,
        	'ordering': true,
          // 'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        $('#tableResumeReq tfoot tr').appendTo('#tableResumeReq thead');
      }
      else{
      	openErrorGritter('Error!', result.message);
      }
    });
}

function DetailApproval(req_id){
	var data = {
		req_id:req_id
	};
	$.get('<?php echo e(url("human_resource/resume_request")); ?>', data, function(result, status, xhr){
		if(result.status){
			$('#tableResumeApproval').DataTable().clear();
			$('#tableResumeApproval').DataTable().destroy();
			var tableData = '';
			$('#tableBodyResumeApproval').html("");
			$('#tableBodyResumeApproval').empty();
			modalApproval();
			$.each(result.resume_detail, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ value.request_id +'</td>';
				tableData += '<td>'+ value.position +'</td>';
				tableData += '<td>'+ value.department +'</td>';
				tableData += '<td>'+ value.employment_status +'</td>';
				tableData += '<td>'+ value.quantity_male +'</td>';
				tableData += '<td>'+ value.quantity_female +'</td>';
				tableData += '<td>'+ value.reason +'</td>';
				tableData += '<td>'+ value.start_date +'</td>';
				tableData += '<td>'+ value.marriage_status +'</td>';
				tableData += '<td>'+ value.domicile +'</td>';
				tableData += '</tr>';
			});
			$('#tableBodyResumeApproval').append(tableData);
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function fillChart() {
	$("#loading").show();

	var data = {
		status : 2,
		dpt:$('#dpt').val(),
		stt:$('#stt').val(),
		nm:$('#nm').val(),
		date_to:$('#date_to').val()
	}

	$.get('{{ url("human_resource/resume_request") }}',data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){


				$("#loading").hide();
				var dept = [];

				var jml_sudah = [];
				var jml_belum = [];

				var sudah = 0;
				var belum = 0;

				var series = []
				var series2 = [];

				var jml_rendah = 0;
				var jml_sedang = 0;
				var jml_tinggi = 0;

				for (var i = 0; i < result.grafik.length; i++) {
					dept.push(result.grafik[i].department_shortname);

					sudah = sudah+parseInt(result.grafik[i].sudah);
					belum = belum+parseInt(result.grafik[i].belum);

					jml_sudah.push(parseInt(result.grafik[i].sudah));
					jml_belum.push(parseInt(result.grafik[i].belum));

					series.push([dept[i], jml_sudah[i]]);
					series2.push([dept[i], jml_belum[i]]);
				}

				Highcharts.chart('container1', {
					chart: {
						type: 'column'
						// backgroundColor: "#403e40"
					},
					title: {
						text: 'Resume Request Man Power',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: dept,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: '',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						tickInterval: 1

					}
          , { // Secondary yAxis
          	title: {
          		text: '',
          		style: {
          			color: '#eee',
          			fontSize: '18px',
          			fontWeight: 'bold',
          			fill: '#6d869f'
          		}
          	},
          	labels:{
          		style:{
          			fontSize:"14px"
          		}
          	},
          	type: 'linear',
          	opposite: true,
          	tickInterval: 1

          }
          ],
          tooltip: {
          	headerFormat: '<span>Detail</span><br/>',
          	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
          },
          legend: {
          	layout: 'horizontal',
          	align: 'right',
          	verticalAlign: 'top',
          	x: -90,
          	y: 20,
          	floating: true,
          	borderWidth: 1,
          	// backgroundColor:
          	// Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
          	shadow: true,
          	itemStyle: {
          		fontSize:'16px',
          	},
          },  
          credits: {
          	enabled: false
          },
          plotOptions: {
          	series:{
          		cursor: 'pointer',
          		dataLabels: {
          			enabled: true,
          			format: '{point.y}',
          			style:{
          				fontSize: '1vw'
          			}
          		},
          		animation: {
          			enabled: true,
          			duration: 800
          		},
          		pointPadding: 0.93,
          		groupPadding: 0.93,
          		borderWidth: 0.93,
          		cursor: 'pointer'
          	},
          },
          series: [
          
          
          {
          	type: 'column',
          	data: series2,
          	name: 'Menunggu Persetujuan',
          	colorByPoint: false,
          	color:'#a83232',
          	yAxis:1,
          	animation: false,
          	dataLabels: {
          		enabled: true,
          		format: '{point.y}' ,
          		style:{
          			fontSize: '1vw',
          			textShadow: false
          		},
          	},
          },
          {
          	type: 'column',
          	data: series,
          	name: 'Recruitment HR',
          	colorByPoint: false,
          	color: "#32a852",
          	animation: false,
          	dataLabels: {
          		enabled: true,
          		format: '{point.y}' ,
          		style:{
          			fontSize: '1vw',
          			textShadow: false
          		},
          	},
          }

          ]
        });
			}
		}
	});
}
</script>

@endsection