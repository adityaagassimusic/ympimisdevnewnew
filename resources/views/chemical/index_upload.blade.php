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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Upload Data Sosialisasi
		<a class="btn btn-info pull-right" style="margin-left: 5px" href="{{url('index/sosialisasi/shedule/sds/')}}">
			Manage Schedule
		</a>
		@if($role_user->role_code == 'S-MIS' || $role_user->role_code == 'C-MIS' || $role_user->role_code == 'MIS' || $role_user->role_code == 'S-CHM' || $role_user->role_code == 'C-CHM')

		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create_modal1">
			Upload Data
		</button>

		@endif

	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12" style="overflow-x: scroll;">
							<table id="example1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										
										<th>Nama Dokumen SDS</th>
										<th>Nama Dokumen</th>
										<th>Bulan</th>
										<th>Periode</th>
										<th>Dokumen <i class="fa fa-paperclip"></i></th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@if(count($documents_sosialisasi) != 0)
									@foreach($documents_sosialisasi as $documents_sosialisasi)
									<tr>
										@if(count($documents_sosialisasi->nama_sds) != 0)
										<td style="width: 15%; text-align: center; font-weight: bold;"><a href="{{ asset('files/chemical/documents') }}/{{$documents_sosialisasi->doc_sds_asli}}" target="_blank">{{$documents_sosialisasi->nama_sds}}</a></td>
										@else
										<td>-</td>
										@endif
										<td>{{$documents_sosialisasi->nama_dokumen}}</td>
										<td>{{$monthTitle = date("F Y", strtotime($documents_sosialisasi->month))}}</td>
										<td>{{$documents_sosialisasi->periode}}</td>
										@if(count($documents_sosialisasi->file_name_pdf) != 0)
										<td style="width: 10%; text-align: center; font-weight: bold;"><a href="{{ asset('files/chemical/documents_sosialisasi') }}/{{$documents_sosialisasi->file_name_pdf}}.pdf" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>
										@else
										<td>-</td>
										@endif
										<td>										
											<center>
												@if($role_user->role_code == 'S-MIS' || $role_user->role_code == 'C-MIS' || $role_user->role_code == 'MIS' || $role_user->role_code == 'S-CHM' || $role_user->role_code == 'C-CHM')

												<button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#edit-modal" onclick="edit_data('{{$documents_sosialisasi->nama_dokumen}}','{{$documents_sosialisasi->month}}','{{$documents_sosialisasi->periode}}','{{$documents_sosialisasi->file_name_pdf}}.pdf','{{$documents_sosialisasi->id}}','{{$documents_sosialisasi->nama_sds}}')">
													Edit
												</button>
												<a href="javascript:void(0)"  class="btn btn-xs btn-danger" onclick="deleteConfirmation('{{ $documents_sosialisasi->id }}');">
													Delete
												</a>
												@endif
												
												<button onclick="ShowChart('{{$documents_sosialisasi->id}}')" class="btn btn-xs btn-primary " style="margin-right:5px;"><i class="fa fa-bar-chart"></i></button>
											</center>
										</td>
									</tr>
									@endforeach
									@endif

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
	</div>
</section>

<div class="modal modal-default fade" id="create_modal1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Create Data Sosialisasi</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="form-group row">
								<label class="col-sm-2">Periode<span class="text-red">*</span></label>
								<div class="col-sm-2" align="left">
									<input type="text" class="form-control" name="inputperiode" id="inputperiode" value="FY199" readonly> 	
								</div> 
							</div>

							<div class="form-group row">
								<label class="col-sm-2">Dokumen SDS<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left" id="selectEmp">
									<select class="form-control selectEmp" data-placeholder="Pilih Dokumen SDS" name="doc_sds" id="doc_sds" style="width: 100%">
										<option value=""></option>
										@foreach($get_dokumen as $doc)
										<option value="{{$doc->title}}-{{$doc->id}}-{{$doc->file_name_sds}}">{{$doc->title}}</option>
										@endforeach
									</select>
								</div>
								
							</div>

							<div class="form-group row">
								<label class="col-sm-2">Nama Dokumen<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" name="inputnama_dokumen" id="inputnama_dokumen" placeholder="Masukkan Nama Dokumen">
								</div> 
							</div>
							<div class="form-group row">
								<label for="editStart" class="col-sm-2 control-label">Bulan<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="inputmonth" name="inputmonth" autocomplete="off" placeholder="Pilih Bulan" required>
									</div>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-2">Upload Materi<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="file" name="uploadData1" id="uploadData1">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-2">Karyawan<span class="text-red">*</span></label>
								<div class="col-sm-3" align="left" style="padding-right: 1px" id="selectPur">
									<select class="form-control selectPur" data-placeholder="Pilih Departemen" name="add_dept" id="add_dept" style="width: 100%" onchange="changeDept(this.value,'add')">
										@foreach($depts as $dept)
										<option value="{{$dept->department}}">{{$dept->department}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-sm-3" align="left" style="padding-right: 1px" id="selectEmp">
									<select class="form-control selectEmp" data-placeholder="Pilih Karyawan" name="add_employees" id="add_employees" style="width: 100%">
										<option value=""></option>
										
									</select>
								</div>
								<div class="col-sm-3" align="left" id="selectArea">
									<select class="form-control selectArea" data-placeholder="Pilih Area" name="add_area" id="add_area" style="width: 100%">
										<option value="KPP (Area cuci)">KPP (Area cuci)</option>
										<option value="Body process (Sax Body)">Body process (Sax Body)</option>
										<option value="Body process (Sax Bell)">Body process (Sax Bell)</option>
										<option value="Body process (Flute)">Body process (Flute)</option>
										<option value="Reedplate">Reedplate</option>
										<option value="SLD (Cuci asam)">SLD (Cuci asam)</option>
										<option value="Handatsuke">Handatsuke</option>
										<option value="Buffing Flute (cuci enthol)">Buffing Flute (cuci enthol)</option>
										<option value="Painting">Painting</option>
										<option value="Plating">Plating</option>
										<option value="WWT">WWT</option>
										<option value="Warehouse">Warehouse</option>
										<option value="Recorder Assy">Recorder Assy</option>
										<option value="Recorder injection">Recorder injection</option>
										<option value="Venova">Venova</option>
										<option value="Mouth piece">Mouth piece</option>
										<option value="Quality Assurance">Quality Assurance</option>
										<option value="Pianika">Pianika</option>
										<option value="Case">Case</option>
										<option value="Molding">Molding</option>
										<option value="Workshop">Workshop</option>
										<option value="KPP (Senban/ Sanding/ Machining/ Press)">KPP (Senban/ Sanding/ Machining/ Press)</option>
										<option value="CL body">CL body</option>
										<option value="SLD (non cuci asam)">SLD (non cuci asam)</option>
										<option value="Buffing (FL key)">Buffing (FL key)</option>
										<option value="Buffing (CL key)">Buffing (CL key)</option>
										<option value="Buffing (SAX key)">Buffing (SAX key)</option>
										<option value="Buffing (SAX body-bell)">Buffing (SAX body-bell)</option>
										<option value="Buffing (FL body)">Buffing (FL body)</option>
										<option value="Assy (FL)"d>Assy (FL)</option>
										<option value="Assy (CL)">Assy (CL)</option>
										<option value="Assy (SAX)">Assy (SAX)</option>
										<option value="Sub Assy (FL)">Sub Assy (FL)</option>
										<option value="Sub Assy (CL)">Sub Assy (CL)</option>
										<option value="Tanpo">Tanpo</option>
										<option value="Sub Assy (SAX)">Sub Assy (SAX)</option>
									</select>
								</div>
								
							</div>

							<div class="form-group row">
								<label class="col-sm-2"></label>
								<div class="col-xs-3" style="padding-right: 2px">
									<button class="btn btn-success" onclick="addEmployee()">
										<i class="fa fa-plus"></i> Tambahkan
									</button>
								</div>
								
							</div>


							<table class="table table-hover table-bordered table-striped" id="tableEmployee">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">ID</th>
										<th style="width: 6%;">Name</th>
										<th style="width: 1%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableEmployeeBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total: </th>
										<th id="countTotal"></th>
										<th></th>
									</tr>
								</tfoot>
							</table>

							
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
				<button class="btn btn-success" onclick="inputDocument()"><i class="fa fa-plus"></i> Buat Data Sosialisasi</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				
				<div class="col-xs-12" style="background-color: #ff851b; padding-right: 1%;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: black;">Update Data Sosialisasi</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<input type="hidden" id="nama_dokumen_edit">
					<input type="hidden" id="month_edit">
					<input type="hidden" id="periode_edit">
					<input type="hidden" id="file_dokumen_edit">
					<input type="hidden" id="dokumen_id">

					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<input type="hidden" class="form-control" name="inputactivity_list_id" id="inputactivity_list_id" placeholder="Masukkan Leader" value="" readonly>
						
						<div class="form-group">
							<label for="">Periode</label>
							<input type="text" class="form-control" name="editperiode" id="editperiode" readonly> 
						</div>

						<div class="form-group">
							<label for="">Bulan</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="editmonth" name="editmonth" autocomplete="off" placeholder="Pilih Bulan" required>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="">Nama Dokumen</label>
							<input type="text" class="form-control" name="editnama_dokumen" id="editnama_dokumen" placeholder="Masukkan Nama Dokumen" >

						</div>

						<div class="form-group" id="selectsdsEdit">
							<label >Dokumen SDS</label>
							<select class="form-control selectsdsEdit" data-placeholder="Pilih Dokumen SDS" name="doc_sds_edit" id="doc_sds_edit" style="width: 100%">
								<option value=""></option>
								@foreach($get_dokumen as $doc)
								<option value="{{$doc->title}}">{{$doc->title}}</option>
								@endforeach
							</select>
						</div>

					</div>
					<div class="col-xs-12">
						<div class="form-group">
							<label for="">Upload Data</label>
							<input type="file" name="edituploadData" id="edituploadData">
						</div>
					</div>
					

					<div class="col-xs-12">
						<div class="col-xs-3" id="selectPur2">
							<label for="">Departemen</label>
							<select class="form-control selectPur2" data-placeholder="Pilih Departemen" name="edit_dept" id="edit_dept" style="width: 100%" onchange="changeDept(this.value,'edit')">
								@foreach($depts as $dept)
								<option value="{{$dept->department}}">{{$dept->department}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-4" id="selectEmp2">
							<label for="">Karyawan</label>
							<select class="form-control selectEmp2" data-placeholder="Pilih Karyawan" name="edit_employees" id="edit_employees" style="width: 100%">
								<option value=""></option>
								
							</select>
						</div>

						<div class="col-sm-3" align="left" id="selectArea1">
							<label class="col-sm-2">Area</label>
							<select class="form-control selectArea1" data-placeholder="Pilih Area" name="edit_area" id="edit_area" style="width: 100%">
								<option value="KPP (Area cuci)">KPP (Area cuci)</option>
								<option value="Body process (Sax Body)">Body process (Sax Body)</option>
								<option value="Body process (Sax Bell)">Body process (Sax Bell)</option>
								<option value="Body process (Flute)">Body process (Flute)</option>
								<option value="Reedplate">Reedplate</option>
								<option value="SLD (Cuci asam)">SLD (Cuci asam)</option>
								<option value="Handatsuke">Handatsuke</option>
								<option value="Buffing Flute (cuci enthol)">Buffing Flute (cuci enthol)</option>
								<option value="Painting">Painting</option>
								<option value="Plating">Plating</option>
								<option value="WWT">WWT</option>
								<option value="Warehouse">Warehouse</option>
								<option value="Recorder Assy">Recorder Assy</option>
								<option value="Recorder injection">Recorder injection</option>
								<option value="Venova">Venova</option>
								<option value="Mouth piece">Mouth piece</option>
								<option value="Quality Assurance">Quality Assurance</option>
								<option value="Pianika">Pianika</option>
								<option value="Case">Case</option>
								<option value="Molding">Molding</option>
								<option value="Workshop">Workshop</option>
								<option value="KPP (Senban/ Sanding/ Machining/ Press)">KPP (Senban/ Sanding/ Machining/ Press)</option>
								<option value="CL body">CL body</option>
								<option value="SLD (non cuci asam)">SLD (non cuci asam)</option>
								<option value="Buffing (FL key)">Buffing (FL key)</option>
								<option value="Buffing (CL key)">Buffing (CL key)</option>
								<option value="Buffing (SAX key)">Buffing (SAX key)</option>
								<option value="Buffing (SAX body-bell)">Buffing (SAX body-bell)</option>
								<option value="Buffing (FL body)">Buffing (FL body)</option>
								<option value="Assy (FL)"d>Assy (FL)</option>
								<option value="Assy (CL)">Assy (CL)</option>
								<option value="Assy (SAX)">Assy (SAX)</option>
								<option value="Sub Assy (FL)">Sub Assy (FL)</option>
								<option value="Sub Assy (CL)">Sub Assy (CL)</option>
								<option value="Tanpo">Tanpo</option>
								<option value="Sub Assy (SAX)">Sub Assy (SAX)</option>
							</select>
						</div>
						<div class="col-xs-2" style="padding-top:25px;">
							<button class="btn btn-success" onclick="addEmployee2()">
								<i class="fa fa-plus"></i> Tambahkan
							</button>
						</div>
					</div>



					<div class="col-xs-12" style="padding-top: 10px;">

						<table class="table table-hover table-bordered table-striped" id="tableEmployee2">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">ID</th>
									<th style="width: 6%;">Name</th>
									<th style="width: 1%;">Action</th>
								</tr>
							</thead>
							<tbody id="tableEmployeeBody2">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<tr>
									<th>Total: </th>
									<th id="countTotal2"></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>


					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="editDocument()">SIMPAN</button>
					</div>

					<div class="col-xs-12" style="padding-top: 10px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 2%;">ID</th>
									<th style="width: 5%;">Name</th>
									<th style="width: 4%;">Dept</th>
									<th style="width: 4%;">Status</th>
									<th style="width: 1%;">Action</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalChart" style="color: black;z-index: 10000;">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>

				<h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_chart"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-7" style="margin-bottom:20px;">
						<div id="container_sosialisasi" style="height: 40vh;"></div>
					</div> 

					<div class="col-xs-4" style="padding-left: 10px;padding-right: 0">
						<div id="pie_grafik" style="height: 40vh;"></div>
					</div>
					<div class="center-block" style="width: 80%;">
						<input type="hidden" id="id_sosialiasi" name="id_sosialiasi">
						<a type="button" class="btn btn-info" style="width:100%" onclick="sosialiasi_kec()"><i class="fa fa-info-circle"></i> Scan Tap RFID</a>
					</div> 


					<div class="col-xs-12" style="margin-top:20px">
						<h4 class="modal-title" id="modalDetailTitleChart"></h4>
						<table class="table table-hover table-bordered table-striped" id="tableDetailChart">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th style="width: 1%;">#</th>
									<th style="width: 3%;">Employee ID</th>
									<th style="width: 9%;">Name</th>
									<th style="width: 9%;">Dept</th>
									<th style="width: 3%;">Status</th>
									<th style="width: 3%;">At</th>
								</tr>
							</thead>
							<tbody id="tableDetailChartBody">
							</tbody>
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	arr_pic = <?php echo json_encode($emp); ?>;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#date').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#inputmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#editmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('.new_periode').select2();
		$('#add_dept').val("").trigger('change');

	});

</script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>

	jQuery(document).ready(function() {

		$("#edituploadData").val("");
		$("#inputnama_dokumen").val("");
		$("#inputmonth").val("");
		$("#uploadData1").val("");
		$('#edit_area').val('').trigger("change");
		$('#editnama_dokumen').val('').trigger("change");
		$('#add_area').val('').trigger("change");
		$('.selectEmp').select2({
			dropdownParent: $('#selectEmp'),
			allowClear:true,
			tags: true

		});
		$('.selectPur').select2({
			dropdownParent: $('#selectPur'),
			allowClear:true
		});

		$('.selectPur2').select2({
			dropdownParent: $('#selectPur2'),
			allowClear:true
		});
		
		$('.selectEmp2').select2({
			dropdownParent: $('#selectEmp2'),
			allowClear:true,
			tags: true
		});
		$('.selectArea').select2({
			dropdownParent: $('#selectArea'),
			allowClear:true
		});
		$('.selectArea1').select2({
			dropdownParent: $('#selectArea1'),
			allowClear:true
		});

		$('.selectsdsEdit').select2();
		$('#doc_sds').val('').trigger('change');

		employees = [];
		employees_edit = [];
		employees_data = [];
		employees_data2 = [];

		count = 0;
		count2 = 0;

		detail_sosil = [];

		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
		} );

		var table = $('#example1').DataTable({
			"order": [],
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
				}
				]
			},
			initComplete: function() {
				this.api()
				.columns([2])
				.every(function(dd) {
					var column = this;
					var theadname = $("#example1 th").eq([dd])
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
						if ($("#example1 th").eq([dd])
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

		table.columns().every( function () {
			var that = this;
			$( '#search', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );

		$('#example1 tfoot tr').appendTo('#example1 thead');

	});




	function sosialiasi_kec(){
		var id = $("#id_sosialiasi").val();
		window.location.href = '{{url("index/sosialisasi/data")}}/'+id;
	}



	function addEmployee() {
		var str = $('#add_employees').val();
		var bags = $('#add_dept').val();
		var areas = $('#add_area').val();
		
		if (str == null || areas == null) {
			audio_error.play();
			openErrorGritter('Error!','Semua Harus Diisi');
			return false;
		}else{
			var employee_id = str.split('-')[0];
			var name = str.split('-')[1];

			if($.inArray(employee_id, employees) != -1){
				audio_error.play();
				openErrorGritter('Error!','Karyawan sudah ada di list.');
				return false;
			}

			var tableEmployee = "";
			tableEmployee += "<tr id='"+employee_id+"' style='position:center;'>";
			tableEmployee += "<td style='margin-bottom:5px;margin-top:5px;'>"+employee_id+"</td>";
			tableEmployee += "<td style='margin-bottom:5px;margin-top:5px;' class='create_data' hidden>"+bags+"_"+employee_id+"_"+name+"</td>";

			tableEmployee += "<td style='margin-bottom:5px;margin-top:5px;'>"+name+"</td>";
			tableEmployee += "<td><a href='javascript:void(0)' onclick='remEmployee(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;margin-bottom:5px;margin-top:5px;'><i class='fa fa-trash'></i></a></td>";
			tableEmployee += "</tr>";

			employees.push(employee_id);
			count += 1;

			$('#countTotal').text(count);
			$('#tableEmployeeBody').append(tableEmployee);
			$('#add_employees').val('').trigger('change');
		}

	}

	function addEmployee2() {
		var str = $('#edit_employees').val();
		var bags = $('#edit_dept').val();
		var edit_areas = $('#edit_area').val();

		
		if (str == null || edit_areas == null) {
			audio_error.play();
			openErrorGritter('Error!','Data tidak boleh kosong');
			return false;
		}else{
			var employee_id = str.split('-')[0];
			var name = str.split('-')[1];
			if($.inArray(employee_id, employees) != -1){
				audio_error.play();
				openErrorGritter('Error!','Karyawan sudah ada di list.');
				return false;
			}

			var tableEmployee = "";

			tableEmployee += "<tr id='"+employee_id+"' style='position:center;'>";
			tableEmployee += "<td style='margin-bottom:5px;margin-top:5px;'>"+employee_id+"</td>";
			tableEmployee += "<td style='margin-bottom:5px;margin-top:5px;' class='edits_data' hidden>"+bags+"_"+employee_id+"_"+name+"</td>";
			tableEmployee += "<td style='margin-bottom:5px;margin-top:5px;'>"+name+"</td>";
			tableEmployee += "<td><a href='javascript:void(0)' onclick='remEmployeeEdit(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;margin-bottom:5px;margin-top:5px;'><i class='fa fa-trash'></i></a></td>";
			tableEmployee += "</tr>";

			employees_edit.push(employee_id);			
			count2 += 1;

			$('#countTotal2').text(count2);
			$('#tableEmployeeBody2').append(tableEmployee);
			$('#edit_employees').val('').trigger('change');
		}
	}

	function remEmployee(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#countTotal').text(count);
		$('#'+id).remove();	
	}

	function remEmployeeEdit(id){
		employees_edit.splice( $.inArray(id), 1 );
		count2 -= 1;
		$('#countTotal2').text(count2);
		$('#'+id).remove();	
	}

	function ShowChart(ids){
		$('#modalDetailTitleChart').html('');
		$('#tableDetailChart').hide();
		$('#tableDetailChart').DataTable().clear();
		$('#tableDetailChart').DataTable().destroy();
		$('#tableDetailChartBody').html('');
		$('#loading').show();

		var data = {
			id:ids
		}

		$.get('{{ url("chart/sosialisasi/sds") }}',data,function(result, status, xhr){
			if(result.status){

				$("#id_sosialiasi").val(ids);
				$('#loading').hide();
				xCategories = [];
				belum = [];
				sudah = [];
				detail_sosil = [];

				var total = 0;
				var total_belum = 0;
				var total_sudah = 0;

				for(var i = 0; i < result.department1.length; i++){
					var count_sudah = 0;
					var count_belum = 0;
					var sosil = [];
					var status_hadir = "";

					for(var j = 0; j < result.sosialisasi.length; j++){

						if (result.sosialisasi[j].status == 0 && result.sosialisasi[j].department == result.department1[i].department_name) {
							count_belum++;
							total_belum++;
							detail_sosil.push({
								id:result.sosialisasi[j].id,
								emp:result.sosialisasi[j].employee_id,
								name:result.sosialisasi[j].name,
								depart:result.sosialisasi[j].department,
								stt:result.sosialisasi[j].status,
								att:result.sosialisasi[j].attend_time,
								status:"Belum Sosialisasi"
							});
							status_hadir = result.sosialisasi[j].status ; 
						}else if (result.sosialisasi[j].status == 1 && result.sosialisasi[j].department == result.department1[i].department_name){
							count_sudah++;
							total_sudah++;
							detail_sosil.push({
								id:result.sosialisasi[j].id,
								emp:result.sosialisasi[j].employee_id,
								name:result.sosialisasi[j].name,
								depart:result.sosialisasi[j].department,
								stt:result.sosialisasi[j].status,
								att:result.sosialisasi[j].attend_time,
								status:"Sudah Sosialisasi"

							});
							status_hadir = result.sosialisasi[j].status ; 

						}

					}

					sudah.push({y:parseInt(count_sudah),key:result.department1[i].department_name,st:status_hadir,depart_short:result.department1[i].department_shortname,departs:result.department1[i].department_name});
					belum.push({y:parseInt(count_belum),key:result.department1[i].department_name,st:status_hadir,depart_short:result.department1[i].department_shortname,departs:result.department1[i].department_name});
					xCategories.push(result.department1[i].department_shortname);

				}



				var total_sudah_hadir = 0;
				var total_belum_hadir = 0;


				total_belum_hadir = parseInt(total_belum);
				total_sudah_hadir = parseInt(total_sudah);

				const chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container_sosialisasi',
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							depth: 50,
							viewDistance: 25
						}
					},
					xAxis: {
						categories: xCategories,
						type: 'category',
						gridLineWidth: 0,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:1,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Total Data',
							style: {
								color: '#eee',
								fontSize: '15px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"15px"
							}
						},
						type: 'linear',
						opposite: true
					}
					],
					tooltip: {
						headerFormat: '<span>{series.name}</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'12px',
						},
						reversed : true
					},	
					title: {
						text: ''
					},
					subtitle: {
						text: ''
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModalDetailChart(this.options.key,this.options.departs,this.options.st,this.series.name,this.options.depart_short);
									}
								}
							},
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: false,
							cursor: 'pointer',
							depth:25
						},
					},
					credits:{
						enabled:false
					},
					series: [{
						type: 'column',
						data: belum,
						name: 'Belum Sosialisasi',
						colorByPoint: false,
						color:'#f44336'
					},{
						type: 'column',
						data: sudah,
						name: 'Sudah Sosialisasi',
						colorByPoint: false,
						color:'#32a852'
					}
					]
				});

				Highcharts.chart('pie_grafik', {
					chart: {
						backgroundColor: 'rgb(255,255,255)',
						type: 'pie',
						options3d: {
							enabled: true,
							alpha: 45,
							beta: 0
						}
					},
					title: {
						text: 'Total Peserta'
					},
					tooltip: {
						pointFormat: '<b>{point.y}</b>'
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					legend: {
						enabled: true,
						symbolRadius: 1,
						borderWidth: 1
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							edgeWidth: 1,
							edgeColor: 'rgb(126,86,134)',
							depth: 35,
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.y}',
								style:{
									fontSize:'0.8vw',
									textOutline:0

								},
								color:'#0f0c0f'
							},
							showInLegend: true
						}
					},
					credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					},
					series: [{
						data: [{
							name: 'Belum Sosialisasi',
							y: total_belum_hadir,
							color: "#d32f2f"
						}, {
							name: 'Sudah Sosialisasi',
							y: total_sudah_hadir,
							color:'#90ee7e'
						}]
					}]
				});
			}

			$('#judul_chart').html('Detail Sosialiasi Safety Data Sheet');
			$('#modalChart').modal('show');



		})
}

function changeDept(dep,st) {
	var pic = '';
	if (st == "add") {
		$("#add_employees").empty();
		$.each(arr_pic, function(key, value) {
			if (value.department == dep) {

				pic += "<option value='"+value.employee_id+"-"+value.name+"'>"+value.employee_id+" - "+value.name+"</option>";
			}
		});
		$("#add_employees").append(pic);

	}else{
		$("#edit_employees").empty();

		$.each(arr_pic, function(key, value) {
			if (value.department == dep) {

				pic += "<option value='"+value.employee_id+"-"+value.name+"'>"+value.employee_id+" - "+value.name+"</option>";
			}
		});
		$("#edit_employees").append(pic);

	}	

}


function ShowModalDetailChart(dept,id,st,depart_short,depart_short) {
	$('#tableDetailChart').hide();

		// $.get('{{ url("chart/kecelakaan/detail") }}', data, function(result, status, xhr) {
		// 	if(result.status){
			$('#tableDetailChartBody').html('');
			$('#tableDetailChart').DataTable().clear();
			$('#tableDetailChart').DataTable().destroy();

			var index = 1;
			var resultData = "";
			var total = 0;

			$.each(detail_sosil, function(key, value) {

				if (value.depart == id && value.stt == st) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.emp +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.depart +'</td>';
					resultData += '<td>'+ value.status +'</td>';
					if (value.att == null) {
						resultData += '<td>-</td>';
					}else{
						resultData += '<td>'+ value.att +'</td>';
					}
					resultData += '</tr>';
					index += 1;
				}
			});
			$('#tableDetailChartBody').append(resultData);
			$('#modalDetailTitleChart').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Operator "+depart_short+" "+st+"</span></center>");

			$('#tableDetailChart').show();
			var table = $('#tableDetailChart').DataTable({
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
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});
			// }
			// else{
			// 	alert('Attempt to retrieve data failed');
			// }
		// });
	}


	function inputDocument(){
		
		var nama_dokumen = $('#inputnama_dokumen').val();
		var month = $('#inputmonth').val();
		var area = $('#add_area').val();
		var doc_sds = $('#doc_sds').val();
		var periode = $('#inputperiode').val();
		var attachment_sds_asli = $('#uploadData1').prop('files')[0];
		var file_sds_asli = $('#uploadData1').val().replace(/C:\\fakepath\\/i, '').split(".");
		var docs = doc_sds.split('-');

		if(nama_dokumen == "" || month == ""){
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
			audio_error.play();
			return false;
		}

			employees_data2 = [];

		$('.create_data').each(function(index, value) {
			employees_data2.push($(value).html());
		});

		var formData = new FormData();
		formData.append('nama_dokumens', nama_dokumen);
		formData.append('months', month);
		formData.append('periodes', periode);
		formData.append('doc_sds', docs[0]);
		formData.append('doc_id', docs[1]);
		formData.append('doc_sds_asli', docs[2]);
		formData.append('employees', employees);
		formData.append('area', area);
		formData.append('attachment_sds_asli', attachment_sds_asli);
		formData.append('extension_asli', file_sds_asli[1]);
		formData.append('employees_data', employees_data2);


		$.ajax({
			url:"{{ url('upload/data/sosialisasi') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#loading').hide();
					openSuccessGritter('Success!', data.message);
					// audio_ok.play();
					$('#create_modal').modal('hide');
					location.reload();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}

			}
		});

	}


	function editDocument(){
		// $('#loading').show();
		var dokumen_id = $('#dokumen_id').val();
		var nama_dokumen_old = $('#nama_dokumen_edit').val();
		var editNama_dokumen = $('#editnama_dokumen').val();
		var editMonth = $('#editmonth').val();
		var editPeriode = $('#editperiode').val();
		var edit_area = $('#edit_area').val();
		var file_old = $('#file_dokumen_edit').val();
		var attachment_data = $('#edituploadData').prop('files')[0];
		var file_data = $('#edituploadData').val().replace(/C:\\fakepath\\/i, '').split(".");
		var doc_sds_edits = $('#doc_sds_edit').val();

		if(editNama_dokumen == "" || editMonth == ""){
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
			audio_error.play();
			return false;
		}

			employees_data = [];

		$('.edits_data').each(function(index, value) {
			employees_data.push($(value).html());
		});
	
		var formData = new FormData();
		formData.append('dokumen_id', dokumen_id);
		formData.append('nama_dokument_old', nama_dokumen_old);
		formData.append('editNama_dokumen', editNama_dokumen);
		formData.append('edit_area', edit_area);
		formData.append('editMonth', editMonth);
		formData.append('editPeriode', editPeriode);
		formData.append('file_old', file_old);
		formData.append('attachment_data', attachment_data);
		formData.append('file_data', file_data[1]);
		formData.append('employees', employees_edit);
		formData.append('doc_sds_edits', doc_sds_edits);
		formData.append('employees_data1', employees_data);

		$.ajax({
			url:"{{ url('edit/upload/sosialisasi') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#loading').hide();
					openSuccessGritter('Success!', data.message);
					// audio_ok.play();
					$('#edit-modal').modal('hide');
					location.reload();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}

			}
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

	function deleteConfirmation(id) {
		if (confirm('Apakah Anda yakin akan menghapus data sosialisasi?')) {
			var data = {
				id:id
			}
			$.get('{{ url("index/documents_sosialisasi/destroy") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success','Success Cancel Data Sosialisasi');
					location.reload();
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function edit_data(name_document,month,periode,file,id,nama_sds) {

		$('#edit_dept').val("").change();
		$('#editnama_dokumen').val(name_document);
		$('#editmonth').val(month);
		$('#editperiode').val(periode);
		$('#doc_sds_edit').val(nama_sds).change();
		$('#nama_dokumen_edit').val(name_document);
		$('#month_edit').val(month);
		$('#periode_edit').val(periode);
		$('#file_dokumen_edit').val(file);
		$('#dokumen_id').val(id);

		var data = {
			id:id
		}

		$.get('{{ url("fetch/edit/sosialisasi") }}', data, function(result, status, xhr) {
			$('#loading').show();
			if(result.status){
				$('#loading').hide();

				var tableData = "";
				var count = 1;
				$('#tableDetailBody').html("");

				$.each(result.get_data_edit, function(key, value) {
					tableData += "<tr id='rowAudience"+value.id+"''>";
					tableData += "<td>"+count+"</td>";
					tableData += "<td>"+value.employee_id+"</td>";
					tableData += "<td>"+value.name+"</td>";
					tableData += "<td>"+value.department+"</td>";
					if(value.status == 0){
						tableData += "<td style='background-color: RGB(255,204,255);'>"+value.status+" - Belum Hadir</td>";
					}
					if(value.status == 1){
						tableData += "<td style='background-color: RGB(204,255,255);'>"+value.status+" - Hadir</td>";
					}
					if ('{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-CHM' || '{{$role_code}}' == 'C-CHM') {
						tableData += "<td><button class='btn btn-danger btn-sm' id='"+value.id+"' onclick='deleteAudience(id)'><i class='fa fa-trash'></i></button></td>";

					}
					tableData += "</tr>";
					count += 1;
				});
				$('#tableDetailBody').append(tableData);
			}
			else{
				audio_error.play();
				$('#loading').hide();
				$('#edit-modal').modal('hide');
				openErrorGritter('Error!', 'Attempt to retrieve data failed');
			}

		});

	}


	function deleteAudience(id){
		var data = {
			id:id
		}
		if(confirm("Are you sure you want to delete this audience?")){
			$.post('{{ url("delete/audience/sosialisasi") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#loadingscreen').hide();
					openSuccessGritter('Success!', result.message);
					$('#rowAudience'+id).remove();
					location.reload();
				}
				else{
					$('#loadingscreen').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$('#loadingscreen').hide();
			return false;
		}
	}





</script>
@endsection