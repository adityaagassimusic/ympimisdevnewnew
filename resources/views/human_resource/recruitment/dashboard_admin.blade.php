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
		<button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="modalRequest();"><i class="fa fa-list"></i> Buat Request</button>
		<a href="{{ url('index/veteran/employee') }}" class="btn pull-right" style="margin-left: 5px; width: 10%; background-color: #3498db; color: white;"><i class="fa fa-list"></i> Veteran Emplouee</a>
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
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">HUMAN RESOURCE DEPARTMENT</span>
							<span style="font-size: 25px;color: black;width: 25%;">人事部</span>
						</div>
					</div>
				</div>
			</div>
		</div>
    <div class="col-md-12">
    	<div class="col-md-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
    		<div id="chart2" style="width: 100%"></div>
    	</div>
    </div>
    <div class="col-xs-12">
    	<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
    		<div class="box-body" align="center">
    			<table style="text-align:center;width:100%" id="score1">
    				<tr>
    					<td colspan="3" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px">Request Man Power
    					</td>
    				</tr>
    				<tr>
    					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 30%;">Request
    					</td>
    					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 30%;">Recruitment HR</td>
    					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 30%;">Close
    					</td>
    				</tr>
    				<tr>
    					<td style="border: 1px solid #fff;font-size: 80px;">
    						@foreach($jml_reqs1 as $jml_reqs1)
    						<span>{{ $jml_reqs1->jumlah }}</span>
    						@endforeach
    					</td>
    					<td style="border: 1px solid #fff;font-size: 80px;">
    						@foreach($jml_rec as $jml_rec)
    						<span>{{ $jml_rec->jumlah }}</span>
    						@endforeach
    					</td>
    					<td style="border: 1px solid #fff;font-size: 80px;"><span>0</span>
    					</td>
    				</tr>
    			</table>
    		</div>
    	</div>
    </div>   
    <div class="col-xs-12">
    	<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
    		<div class="box-body" align="center">
    			<table class="table table-hover table-striped table-bordered" id="tableResumeReqOff">
			    		<thead style="background-color: rgb(126,86,134); color: white;">
			    			<tr>
			    				<th style="width: 10%">No</th>
									<th style="width: 10%">Request Id</th>
									<th style="width: 10%">Status Karyawan</th>
									<th style="width: 20%">Departemen</th>
									<th style="width: 10%">Pembuat</th>
									<th style="width: 10%">Tanggal Masuk</th>
									<th style="width: 10%">Progress Approval</th>
									<th style="width: 10%">Status</th>
			    			</tr>
			    		</thead>
			    		<tbody id="tableBodyResumeReqOff">
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
			    			</tr>
			    		</tfoot>
			    	</table>
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
  									<select class="form-control select2" id="createDepartment" data-placeholder="Pilih Department" style="width: 100%;">
  										<option></option>
  										@foreach($departments as $department)
  										<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
  										@endforeach
  									</select>
  								</div>
  							</div>
  							{{-- <div class="form-group">
  								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
  								<div class="col-sm-7">
  									<select class="form-control select2" id="createDepartment" data-placeholder="Select Department" style="width: 100%;">
  										<option></option>
  										@foreach($departments as $department)
  										<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
  										@endforeach
  									</select>
  								</div>
  							</div> --}}
  							<div class="form-group">
  								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah<span class="text-red">*</span> :</label>
  								<div class="col-sm-3">
  									<div class="input-group">
  										<!-- <input type="text" class="form-control" placeholder="Qty" value="0" id="createMale" min="0"> -->
  										<input id="createMale" name="createMale" type="number" class="form-control numpad" value="0">
  										<span class="input-group-addon" style="width: 100px;">Laki-laki</span>
  									</div>
  								</div>
  							</div>
  							<div class="form-group">
  								<label style="padding-top: 0;" for="" class="col-sm-3 control-label"><span class="text-red"> </span> </label>
  								<div class="col-sm-3">
  									<div class="input-group">
  										<!-- <input type="text" class="form-control" placeholder="Qty" value="0" id="createFemale" min="0"> -->
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
  									<input type="text" class="form-control datepicker" id="createStartDate" placeholder="   Pilih Tanggal">
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
  										<select class="form-control select2" multiple="" id="createEducationLevel" data-placeholder="Pilih Jenjang" style="width: 100%;">
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
  					<span style="font-weight: bold; font-size: 1.2vw;">Catatan</span>
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

<div class="modal fade" id="modalApproval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #bb8fce;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: black">Detail Request Man Power</h1>
				</div>
				<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
					<!-- <center><h4 id="title_proses" style="font-weight: bold"></h4></center> -->
					<table class="table table-hover table-striped table-bordered" id="tableResumeApproval">
						<thead style="background-color: rgb(126,86,134); color: white;">
							<tr>
								<th>Progress Approval</th>
							</tr>
						</thead>
						<tbody id="tableBodyResumeApproval">
						</tbody>
					</table>
					<table class="table table-hover table-striped table-bordered" id="tableResumeMan1">
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
								<th style="width: 10%">Note</th>
							</tr>
						</thead>
						<tbody id="tableBodyResumeMan1"></tbody>
					</table>
          <input type="hidden" id="request_id">
					<div class="col-xs-5">
						<a class="btn pull-left" style="margin-left: 250px; width: 100%; background-color: #3498db; color: white;" id="btn_list_peserta" style="color:white">List Calon Karyawan</a>
					</div>
					</div>   
				</div>
			</div>
		</div>
	</div>
</div>

	<div class="modal fade" id="modalPeserta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
		<!-- <div class="modal-dialog modal-lg"> -->
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #bb8fce;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: black">List Peserta</h1>
					</div>
					<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
						<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
						<table class="table table-hover table-striped table-bordered" id="tablePeserta">
							<thead style="background-color: rgb(126,86,134); color: white;">
								<tr>
									<th>No</th>
									<th>Nama</th>
									<th>Asal</th>
									<th>Nilai TPA</th>
									<th>Nilai Interview Awal</th>
									<th>Nilai Interview User</th>
									<th>Nilai Psikotest</th>
									<th>Nilai Kesehatan</th>
									<th>Nilai Interview Management</th>
									<th>Nilai Induction Trining</th>
								</tr>
							</thead>
							<tbody id="tableBodyPeserta">
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
					<!-- </div> -->
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
				$('.select2').select2();
				$('#createStartDate').datepicker({
					autoclose: true,
					format: "yyyy-mm-dd"
				});
				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
				$('.datepicker').datepicker({
					autoclose: true,
					format: "yyyy-mm",
					todayHighlight: true,
					startView: "months", 
					minViewMode: "months",
					autoclose: true,
				});
				drawChart();
				drawChart2();
				DetailReq();
			});	

			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
			var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
			var skill_count = 0;
			var skills = [];
			var employees = [];
			var requirement_count = 0;
			var requirements = [];

			function confirmRequest(){
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
					status_at:status_at
				}

				$.get('{{ url("input/hr/request_manpower") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalRequest').modal('hide');
				// $('#score1').reload();
				// reload();
				reset();
				location.reload();
			}
			else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
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
				$('#createMarriageStatus').val("");
				$('#createDomicile').val("");
				$('#createWorkExperience').val(0);
				$('#createEducationLevel').val("");
				$('#createMajor').val("");
				$('#createNote').val("");
			}


			function tambah(id,lop) {
				var id = id;
				var lop = "";
				if (id == "tambah"){
					lop = "lop";
				}else{
					lop = "lop2";
				}
				var divdata = $("<input type='text' name='lop' id='lop' value='"+no+"' hidden><div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-10' style='padding:0;''><select class='form-control select7' id='description"+no+"' name='description"+no+"' data-placeholder='Pilih Nama' style='width: 100%'><option value=''>&nbsp;</option>@foreach($employee as $row)<option value='{{$row->old_nik}}/{{$row->name}}'>{{$row->old_nik}} - {{$row->name}}</option>@endforeach</select></div><div class='col-xs-2' style='padding:0;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");
				$("#"+id).append(divdata);
			// document.getElementById(lop).value = no;
			$('#lop').val(no);
			no+=1;
			$('.select7').select2({
				dropdownParent : $("#modalApproval")
			});

		}

		function kurang(elem,lop) {
			var lop = lop;
			var ids = $(elem).parent('div').parent('div').attr('id');
			var oldid = ids;
			$(elem).parent('div').parent('div').remove();
			var newid = parseInt(ids) + 1;
			jQuery("#"+newid).attr("id",oldid);
			jQuery("#description"+newid).attr("name","description"+oldid);
      // jQuery("#duration"+newid).attr("name","duration"+oldid);

      jQuery("#description"+newid).attr("id","description"+oldid);
      // jQuery("#duration"+newid).attr("id","duration"+oldid);

      no-=1;
      var a = no -1;

      for (var i =  ids; i <= a; i++) { 
      	var newid = parseInt(i) + 1;
      	var oldid = newid - 1;
      	jQuery("#"+newid).attr("id",oldid);
      	jQuery("#description"+newid).attr("name","description"+oldid);
        // jQuery("#duration"+newid).attr("name","duration"+oldid);

        jQuery("#description"+newid).attr("id","description"+oldid);
        // jQuery("#duration"+newid).attr("id","duration"+oldid);

      // alert(i)
    }

    document.getElementById(lop).value = a;
  }

  function modalRequest(){
  	$('#modalRequest').modal('show');
  }

  function modalApproval(){
  	$('#modalApproval').modal('show');
  }

  function modalPeserta(){
  	$('#modalPeserta').modal('show');
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
  			$('#tableResumeReqOff').DataTable().clear();
  			$('#tableResumeReqOff').DataTable().destroy();
  			var tableData = '';
  			$('#tableBodyResumeReqOff').html("");
  			$('#tableBodyResumeReqOff').empty();

  			var count = 1;

  			$.each(result.resumes_off, function(key, value) {
  				var  appr = value.approval.split(',');
  				var  status = value.status.split(',');
  				var urlreport = '{{ url("index/calon/rekontrak") }}';

  				tableData += '<tr>';
  				tableData += '<td>'+ count +'</td>';
  				tableData += '<td>'+ value.request_id +'</td>';
  				tableData += '<td>'+ value.employment_status +'</td>';
  				tableData += '<td>'+ value.department +'</td>';
  				tableData += '<td>'+ value.name +'</td>';
  				tableData += '<td>'+ value.start_date +'</td>';
  				tableData += '<td>';
  				if (value.status_at == 'Process') {
  					tableData += '<button class="btn btn-warning btn-sm" onclick="DetailManPower(\''+value.request_id+'\')">Menunggu Persetujuan</button>';
  				}
  				else if (value.status_at == 'Recruitment HR') {
  					tableData += '<span class="btn btn-success btn-sm" onclick="DetailManPower(\''+value.request_id+'\')">Full Approved</span>';
  				}
  				else if (value.status_at == 'Upload Done') {
  					tableData += '<span class="btn btn-info btn-sm"><a href="'+urlreport+'/'+value.request_id+'" style="color:white">List Peserta</a></span>';
  				}
  				tableData += '</td>';
  				tableData += '<td>'+ value.remark +'</td>';
					tableData += '</tr>';
					count += 1;
				});

  			$('#tableResumeReqOff tfoot th').each( function () {
  				var title = $(this).text();
  				$(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Cari '+title+'" size="20"/>' );
  			} );

  			$('#tableBodyResumeReqOff').append(tableData);
  			var tableResumeReqOff = $('#tableResumeReqOff').DataTable({
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

  			tableResumeReqOff.columns().every( function () {
  				var that = this;
  				$( '#search', this.footer() ).on( 'keyup change', function () {
  					if ( that.search() !== this.value ) {

  						console.log(that.search());

  						console.log(this.value);

  						that
  						.search( this.value )
  						.draw();
  					}
  				} );
  			} );
  			$('#tableResumeReqOff tfoot tr').appendTo('#tableResumeReqOff thead');
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

			$.each(result.resumes, function(key, value) {
				var  appr = value.approval.split(',');
				var  status = value.status.split(',');

				tableData += '<tr>';
				tableData += '<td>';
				for(var i = 0; i < appr.length; i++){
					if (i == appr.length-1){
						if (status[i] == 'Approved') {
							tableData += '<span class="label label-success">'+appr[i]+'</span>';	
						}
						else if(status[i] == 'none'){
							tableData += '';	
						}
						else{
							tableData += '<span class="label label-danger">'+appr[i]+'</span>';	
						}
					}else{
						if (status[i] == 'Approved') {
							tableData += '<span class="label label-success">'+appr[i]+'</span> -> ';	
						}
						else if(status[i] == 'none'){
							tableData += '';	
						}
						else{
							tableData += '<span class="label label-danger">'+appr[i]+'</span> -> ';	
						}
					}
				}
				tableData += '</td>';
				tableData += '</tr>';
			});

			$('#tableBodyResumeApproval').append(tableData);
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function DetailManPower(req_id){
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
      var url = '{{url("index/calon/rekontrak")}}/'+req_id;
      $('#btn_list_peserta').prop('href',url);

			$.each(result.resume_detail, function(key, value) {
				var  appr = value.approval.split(',');
				var  status = value.status.split(',');

				tableData += '<tr>';
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
				tableData += '</tr>';
			});

			$('#tableBodyResumeApproval').append(tableData);

			$('#tableResumeMan1').DataTable().clear();
			$('#tableResumeMan1').DataTable().destroy();
			var tableData = '';
			$('#tableBodyResumeMan1').html("");
			$('#tableBodyResumeMan1').empty();

			var remark = "";
			$.each(result.resume_detail, function(key, value) {
				var  appr = value.approval.split(',');
				var  status = value.status.split(',');
				remark = value.remark;
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
				tableData += '<td>'+ value.note +'</td>';
				tableData += '</tr>';
				$('#req_id').val(value.request_id);
			});

			$('#tableBodyResumeMan1').append(tableData);
			$('#req_id').val(req_id);

			// if (remark == 'Recruitment HR') {
			// 	$('#insert').show();
			// 	$('#New_Employee').hide();
			// 	$('#Veteran_Employee').hide();
			// }
			// else{
			// 	$('#insert').hide();
			// 	$('#New_Employee').hide();
			// 	$('#Veteran_Employee').hide();
			// }

			
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

function ModalVeteran(){
	$('#Veteran_Employee').show();
	$('#New_Employee').hide();
}

function ModalNew(){
	$('#New_Employee').show();
	$('#Veteran_Employee').hide();
}

function drawChart() {
	var dateto = $('#dateto').val();

	var data = {
		dateto: dateto
	};

	$.get('{{ url("fetch/man_power") }}', data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){

				var department = [], outsource = [], permanent = [];

				$.each(result.datas, function(key, value) {
					department.push(value.department);
					outsource.push(parseInt(value.outsource));
					permanent.push(parseInt(value.permanent));
				});

				var date = new Date();

				$('#chart').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Data Karyawan Outsource & Permanent'
					},
					xAxis: {
						type: 'category',
						categories: department
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Total Man Power'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
    		                color: ( // theme
    		                	Highcharts.defaultOptions.title.style &&
    		                	Highcharts.defaultOptions.title.style.color
    		                	) || 'gray'
    		              }
    		            }
    		          },
    		          legend: {
    		          	align: 'right',
    		          	x: -30,
    		          	verticalAlign: 'top',
    		          	y: 25,
    		          	floating: true,
    		          	backgroundColor:
    		          	Highcharts.defaultOptions.legend.backgroundColor || 'white',
    		          	borderColor: '#CCC',
    		          	borderWidth: 1,
    		          	shadow: false
    		          },
    		          tooltip: {
    		          	headerFormat: '<b>{point.x}</b><br/>',
    		          	pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
    		          },
    		          plotOptions: {
    		          	column: {
    		          		stacking: 'normal',
    		          		dataLabels: {
    		          			enabled: true
    		          		}
    		          	}
    		          },
    		          series: [{
    		          	name: 'OUTSOURCING',
    		          	data: outsource
    		          }, {
    		          	name: 'PERMANENT',
    		          	data: permanent
    		          }]
    		        })
			} else{
				alert('Attempt to retrieve data failed');
			}
		}
	})
}

function uploadExcel(){
	$('#loading').show();

	var formData = new FormData();
	var newAttachment  = $('#menuFile').prop('files')[0];
	var file = $('#menuFile').val().replace(/C:\\fakepath\\/i, '').split(".");

	formData.append('newAttachment', newAttachment);

	formData.append('extension', file[1]);
	formData.append('req_id', $('#req_id').val());
	formData.append('file_name', file[0]);

	$.ajax({
		url:"{{ url('import/calon/kryawan') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			if (data.status) {
				openSuccessGritter('Success!',data.message);
				audio_ok.play();
				$('#menuFile').val("");
				$('#modalApproval').modal('hide');
				$('#loading').hide();
				DetailReq();
			}else{
				openErrorGritter('Error!',data.message);
				audio_error.play();
				$('#loading').hide();
			}

		}
	});
}

function SubmitVeteran(){
	var employee = [];
	var req_id = $('#req_id').val();

		// if(employees.length > 0){
			for(var i = 1; i <= $('#lop').val(); i++){
				if($('#description'+i).val() != ""){
					employee.push($('#description'+i).val());
				}
			}
		// }

		var data = {
			req_id:req_id,
			employee:employee
		}

		$.get('{{ url("input/veteran/request") }}', data, function(result, status, xhr){
			if(result.status){
				$('#modalApproval').modal('hide');
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function drawChart2() {
		var dateto = $('#dateto').val();

		var data = {
			dateto: dateto
		};

		$.get('{{ url("fetch/man_power") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var department = [], contract1 = [], contract2 = [], contract3 = [];

					$.each(result.datas, function(key, value) {
						department.push(value.department);
						contract1.push(parseInt(value.contract1));
						contract2.push(parseInt(value.contract2));
						contract3.push(parseInt(value.contract3));
					});

					var date = new Date();

					$('#chart2').highcharts({
						chart: {
							type: 'column'
						},
						title: {
							text: 'Data Karyawan Contract'
						},
						xAxis: {
							type: 'category',
							categories: department
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total Man Power'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
                        color: ( // theme
                        	Highcharts.defaultOptions.title.style &&
                        	Highcharts.defaultOptions.title.style.color
                        	) || 'gray'
                      }
                    }
                  },
                  legend: {
                  	align: 'right',
                  	x: -30,
                  	verticalAlign: 'top',
                  	y: 25,
                  	floating: true,
                  	backgroundColor:
                  	Highcharts.defaultOptions.legend.backgroundColor || 'white',
                  	borderColor: '#CCC',
                  	borderWidth: 1,
                  	shadow: false
                  },
                  tooltip: {
                  	headerFormat: '<b>{point.x}</b><br/>',
                  	pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                  },
                  plotOptions: {
                  	column: {
                  		stacking: 'normal',
                  		dataLabels: {
                  			enabled: true
                  		}
                  	}
                  },
                  series: [{
                  	name: 'CONTRACT 1',
                  	data: contract1
                  }, {
                  	name: 'CONTRACT 2',
                  	data: contract2
                  }, {
                  	name: 'CONTRACT 3',
                  	data: contract3
                  }]
                })
				} else{
					alert('Attempt to retrieve data failed');
				}
			}
		})
	}

	function DetailPeserta(req_id){
		var data = {
			req_id:req_id
		};
		$.get('<?php echo e(url("calon/karyawan")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#tablePeserta').DataTable().clear();
				$('#tablePeserta').DataTable().destroy();
				var tableData = '';
				$('#tableBodyPeserta').html("");
				$('#tableBodyPeserta').empty();
				modalPeserta();

				var urlreport = '{{ url("human_resource/detail_pekerjaan") }}';
        // var approvals = modalApproval();

        var count = 1;
        
        $.each(result.resumes, function(key, value) {
        	tableData += '<tr>';
        	tableData += '<td>'+ count +'</td>';
        	tableData += '<td>'+ value.nama +'</td>';
        	tableData += '<td>'+ value.asal +'</td>';
        	tableData += '<td>'+ value.test_tpa +'</td>';
        	tableData += '<td>'+ value.interview_awal +'</td>';
        	tableData += '<td>'+ value.interview_user +'</td>';
        	tableData += '<td>'+ value.test_psikotest +'</td>';
        	tableData += '<td>'+ value.test_kesehatan +'</td>';
        	tableData += '<td>'+ value.interview_management +'</td>';
        	tableData += '<td>'+ value.induction +'</td>';
        	tableData += '</tr>';
        	count += 1;
        });

        $('#tablePeserta tfoot th').each( function () {
        	var title = $(this).text();
        	$(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
        } );

        $('#tableBodyPeserta').append(tableData);
        var tablePeserta = $('#tablePeserta').DataTable({
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

        tablePeserta.columns().every( function () {
        	var that = this;
        	$( '#search', this.footer() ).on( 'keyup change', function () {
        		if ( that.search() !== this.value ) {
        			that
        			.search( this.value )
        			.draw();
        		}
        	} );
        } );

        $('#tablePeserta tfoot tr').appendTo('#tablePeserta thead');
      }
      else{
      	openErrorGritter('Error!', result.message);
      }
    });
	}



	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#f5b041', '#82e0aa', '#ff5733'],
	};
	Highcharts.setOptions(Highcharts.theme);
</script>

@endsection