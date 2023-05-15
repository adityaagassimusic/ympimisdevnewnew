@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
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
		height: 30px;
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
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
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
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<center>
				<h1 style="background-color: #3f51b5; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: white; border: 1px solid darkgrey; border-radius: 5px;">
					IN PROGRESS
				</h1>
			</center>
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="col-xs-12" style="text-align: center;">
									<table id="TableDetailCustomFieldWaiting" class="table table-bordered table-striped table-hover">
										<thead style="background-color: #BDD5EA; color: black;">
											<tr>
												<th width="5%" style="text-align: center;">No</th>
												<th width="10%" style="text-align: center;">Nama Karyawan</th>
												<th width="10%" style="text-align: center;">Nama</th>
												<th width="10%" style="text-align: center;">No BPJS</th>
												<th width="10%" style="text-align: center;">No KTP</th>
												<th width="10%" style="text-align: center;">Hubungan</th>
												<th width="10%" style="text-align: center;">TTL</th>
												<th width="5%" style="text-align: center;">Jenis Kelamin</th>
												<th width="15%" style="text-align: center;">Alamat</th>
												<th width="2%" style="text-align: center;">Nama Faskes</th>
												<th width="2%" style="text-align: center;">Kelas Rawat</th>
												<th width="11%" style="text-align: center;">#</th>
											</tr>
										</thead>
										<tbody id="bodyTableDetailCustomFieldWaiting">
										</tbody>
										<tfoot>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<center>
				<h1 style="background-color: #32a852; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: white; border: 1px solid darkgrey; border-radius: 5px;">
					COMPLETED
				</h1>
			</center>
			<div class="row" style="margin:0px;">
				<div class="box box-success">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<form method="GET" action="{{ url("download/report/karyawan/bpjs") }}">
								<div class="col-xs-3" style="padding-bottom: 10px" align="center">
									<label>Dari Bulan</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="dari_bulan" name="dari_bulan" data-placeholder="Select Month">
									</div>
								</div>
								<div class="col-xs-3" style="padding-bottom: 10px" align="center">
									<label>Sampai Bulan</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="sampai_bulan" name="sampai_bulan" data-placeholder="Select Month">
									</div>
								</div>
								<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
									<select class="form-control select2" id="kategori" name="kategori" required data-placeholder="Kategori">
										<option value="">&nbsp;</option>
										<option value="1">Approved</option>
										<option value="3">Rejected</option>
									</select>
								</div>
								<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
									<!-- <button class="btn btn-primary" style="width: 100%" onclick="DownloadReport()"><i class="fa fa-download" aria-hidden="true"> Download Report</i></button> -->
									<button class="btn btn-primary" style="width: 100%" type="submit"><i class="fa fa-download" aria-hidden="true"> Download Report</i></button>
								</div>
								</form>
								<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
									<button class="btn btn-warning" style="width: 100%" onclick="Clear()"><i class="fa fa-eraser" aria-hidden="true"> Clear</i></button>
								</div>
								<div class="col-xs-12" style="text-align: center;">
									<table id="TableDetailCustomFieldDone" class="table table-bordered table-striped table-hover">
										<thead style="background-color: #BDD5EA; color: black;">
											<tr>
												<th width="5%" style="text-align: center;">No</th>
												<th width="10%" style="text-align: center;">Nama Karyawan</th>
												<th width="10%" style="text-align: center;">Nama</th>
												<th width="10%" style="text-align: center;">No BPJS</th>
												<th width="10%" style="text-align: center;">No KTP</th>
												<th width="10%" style="text-align: center;">Hubungan</th>
												<th width="10%" style="text-align: center;">TTL</th>
												<th width="5%" style="text-align: center;">Jenis Kelamin</th>
												<th width="15%" style="text-align: center;">Alamat</th>
												<th width="2%" style="text-align: center;">Nama Faskes</th>
												<th width="2%" style="text-align: center;">Kelas Rawat</th>
												<th width="11%" style="text-align: center;">#</th>
											</tr>
										</thead>
										<tbody id="bodyTableDetailCustomFieldDone">
										</tbody>
										<tfoot>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="ModalKartuKeluarga" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="judul_kk">
					</h3>
					<div id="gambar_kk"></div><br>
					<button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1vw; width: 40%;">Kembali</button>
				</center>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
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
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		DataCustomField();
		$('#dari_bulan').datepicker({
			format: 'yyyy-mm',
			viewMode: "months",
			minViewMode: "months",
			todayHighlight: true,
			autoclose: true,
		});
		$('#sampai_bulan').datepicker({
			format: 'yyyy-mm',
			viewMode: "months",
			minViewMode: "months",
			todayHighlight: true,
			autoclose: true,
		});
		$('.select2').select2({
			allowClear : true
		});
	});

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

	// function DownloadReport(){
	// 	var dari_bulan = $("#dari_bulan").val();
	// 	var sampai_bulan = $("#sampai_bulan").val();

	// 	var formData = new FormData();
	// 	formData.append('dari_bulan', $("#dari_bulan").val());
	// 	formData.append('sampai_bulan', $("#sampai_bulan").val());

	// 	$.ajax({
	// 		url:"{{ url('download/report/karyawan/bpjs') }}",
	// 		method:"POST",
	// 		data:formData,
	// 		dataType:'JSON',
	// 		contentType: false,
	// 		cache: false,
	// 		processData: false,
	// 		success: function (response) {
	// 			openSuccessGritter("Success","Report berhasil di download.");
	// 			location.reload();
	// 		},
	// 		error: function (response) {
	// 			openErrorGritter("Error", 'Silahkan coba lagi.');
	// 		},
	// 	});
	// }

	function Clear(){
		location.reload();
		$("#dari_bulan").val('');
		$("#sampai_bulan").val('');
	}

	function LihatKK(id, upload_kk){
		var data = {
			id : id
		}
		$.get('{{ url("open/kk") }}', data, function(result, status, xhr){
			if(result.status){
				// console.log(upload_kk);
				var url = "{{ url('hr_bpjs') }}/"+upload_kk;
				$("#ModalKartuKeluarga").modal('show');
				$("#judul_kk").html('KARTU KELUARGA');
				$("#gambar_kk").html('<img src="'+url+'" style="width: 85%">');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function DokumenPdf(id){
		var data = {
			id : id
		}
		$.get('{{ url("berkas/tanda_tangan") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				var nama = 'Data Fix '+result.judul+'.pdf';
				var qq = '{{ url("hr_bpjs_fix/") }}/'+nama+'';
				window.open(qq, '_blank');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function DataCustomField(){
		$.get('{{ url("fetch/data/bpjs/all") }}', function(result, status, xhr){
			if(result.status){
				$('#TableDetailCustomFieldWaiting').DataTable().clear();
				$('#TableDetailCustomFieldWaiting').DataTable().destroy();
				$('#bodyTableDetailCustomFieldWaiting').html("");
				var tableData = "";
				var index = 1;

				$.each(result.report_waiting, function(key, value){
					// var report = "{{ url('index/confirmation/report') }}"+'/'+value.id_reg+'';
					var report = "{{ url('index/report/bpjs') }}"+'/'+value.id_reg+'';


					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td style="text-align: center">'+value.karyawan+'</td>';
					tableData += '<td style="text-align: center">'+value.name+'</td>';
					tableData += '<td style="text-align: center">'+value.bpjs_number+'</td>';
					// tableData += '<td style="text-align: center">'+value.no_ktp+'</td>';
					tableData += '<td style="text-align: center">';
					tableData += ''+value.no_ktp+'<br>';
					tableData += '<button type="button" class="btn btn-success btn-xs" onclick="LihatKK(\''+value.id+'\', \''+value.upload_kk+'\')">Lihat KK</button>';
					tableData += '</td>';
					
					tableData += '<td style="text-align: center">'+value.hubungan+'</td>';
					tableData += '<td style="text-align: center">'+value.tempat_lahir+', '+value.tanggal_lahir+'</td>';
					tableData += '<td style="text-align: center">'+value.jenis_kelamin+'</td>';
					if (value.rt == null) {
						tableData += '<td style="text-align: center">'+value.alamat+'</td>';
					}else{
						tableData += '<td style="text-align: center">'+value.alamat+', '+value.rt+'-'+value.rw+', '+value.kelurahan+', '+value.kecamatan+'</td>';
					}
					tableData += '<td style="text-align: center">'+value.nama_faskes+'</td>';
					tableData += '<td style="text-align: center">'+value.kelas_rawat+'</td>';
					if (value.status == 0) {
						tableData += '<td style="text-align: center">';
						tableData += '<a href="'+report+'" target="_blank" data-toggle="tooltip" title="Konfirmasi"><span class="label label-danger">Menunggu Konfirmasi HR</span></a>';
						tableData += '</td>';
					}else{
						tableData += '<td style="text-align: center">';
						tableData += '<span class="label label-success">Disetujui HR</span>';
						tableData += '</td>';
					}
					tableData += '</tr>';

				});
				$('#bodyTableDetailCustomFieldWaiting').append(tableData);

				var table = $('#TableDetailCustomFieldWaiting').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[{
							extend: 'pageLength',
							className: 'btn btn-default',
						}]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'DataListing': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#TableDetailCustomFieldDone').DataTable().clear();
				$('#TableDetailCustomFieldDone').DataTable().destroy();
				$('#bodyTableDetailCustomFieldDone').html("");
				var tableData = "";
				var index = 1;

				$.each(result.report_done, function(key, value){
					var report = "{{ url('index/report/bpjs') }}"+'/'+value.id_reg+'';

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td style="text-align: center">'+value.karyawan+'</td>';
					tableData += '<td style="text-align: center">'+value.name+'</td>';
					tableData += '<td style="text-align: center">'+value.bpjs_number+'</td>';
					// tableData += '<td style="text-align: center">'+value.no_ktp+'</td>';

					tableData += '<td style="text-align: center">';
					tableData += ''+value.no_ktp+'<br>';
					tableData += '<button type="button" class="btn btn-success btn-xs" onclick="LihatKK(\''+value.id+'\', \''+value.upload_kk+'\')">Lihat KK</button>';
					tableData += '</td>';

					tableData += '<td style="text-align: center">'+value.hubungan+'</td>';
					tableData += '<td style="text-align: center">'+value.tempat_lahir+', '+value.tanggal_lahir+'</td>';
					tableData += '<td style="text-align: center">'+value.jenis_kelamin+'</td>';
					if (value.rt == null) {
						tableData += '<td style="text-align: center">'+value.alamat+'</td>';
					}else{
						tableData += '<td style="text-align: center">'+value.alamat+', '+value.rt+'-'+value.rw+', '+value.kelurahan+', '+value.kecamatan+'</td>';
					}
					tableData += '<td style="text-align: center">'+value.nama_faskes+'</td>';
					tableData += '<td style="text-align: center">'+value.kelas_rawat+'</td>';
					if (value.status == 0) {
						tableData += '<td style="text-align: center">';
						tableData += '<span class="label label-danger">Menunggu Konfirmasi HR</span>';
						tableData += '</td>';
					}else{
						tableData += '<td style="text-align: center">';
						tableData += '<span class="label label-success">Disetujui HR</span>';
						tableData += '<br><br><button onclick="DokumenPdf(\''+value.id_reg+'\')" type="button" class="btn btn-info btn-xs">Report PDF</button>';
						tableData += '</td>';
					}
					tableData += '</tr>';

				});
				$('#bodyTableDetailCustomFieldDone').append(tableData);

				var table = $('#TableDetailCustomFieldDone').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[{
							extend: 'pageLength',
							className: 'btn btn-default',
						}]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'DataListing': true	,
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
				alert('Attempt to retrieve data failed');
			}
		});
}
</script>
@endsection