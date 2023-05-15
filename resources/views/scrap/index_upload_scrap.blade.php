@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
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
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(54, 59, 56) !important;
		text-align: center;
		background-color: #212121;  
		color:white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(54, 59, 56);
		background-color: #212121;
		color: white;
		vertical-align: middle;
		text-align: center;
		padding:3px;
	}
	table.table-condensed > thead > tr > th{   
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}

	table.table-striped > thead > tr > th{
		border:1px solid black !important;
		text-align: center;
		background-color: rgba(126,86,134,.7) !important;  
	}

	table.table-striped > tbody > tr > td{
		border-collapse: collapse;
		color: black;
		padding: 3px;
		vertical-align: middle;
		text-align: center;
		background-color: white;
	}
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
	</h1>
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
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="box-body">
						<div class="row">
							<div>
								<div class="col-md-12" style="padding-bottom: 10px">
									<div class="row" style="margin:0px;">
										<div>
											<button class="btn pull-right" style="margin-left: 5px; width: 12%; color: white; padding-bottom; background-color: #3c9ce7" onclick="InputMp();"><i class="fa fa-list"></i> Monthly Upload</button>
											<button class="btn pull-right" style="margin-left: 5px; width: 12%; color: white; padding-bottom; background-color: #ff3200" onclick="InputMp2();"><i class="fa fa-list"></i> Daily Upload</button>
											<a class="btn pull-right" style="margin-left: 5px; width: 12%; color: white; padding-bottom; background-color: #ffc300" href="{{ url('index/display/eff_scrap') }}"><i class="fa fa-list"></i> Monitoring Scrap</a>
										</div>
									</div>
								</div>
								<div class="col-md-12" align="center">
									<div class="form-group">
										<table id="tableKaryawanKontrak" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
											<thead style="background-color: rgb(126,86,134); color: #FFD700;">
												<tr>
													<th width="1%">No</th>
													<th width="1%">Entry Date</th>
													<th width="1%">Posting Date</th>
													<th width="1%">MvT</th>
													<th width="2%">GMC</th>
													<th width="1%">Qty</th>
													<th width="2%">SLoc</th>
													<th width="2%">Rec Loc</th>
													<th width="2%">Reference</th>
													<th width="5%">Desc</th>
												</tr>
											</thead>
											<tbody id="bodyTableKaryawanKontrak">
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

		<div class="modal fade" id="modalCreate">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<center><h3 style="background-color: #3c9ce7; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Monthly Upload Scrap</h3>
								</center>
							</ul>
						</div>
						<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label for="" class="col-sm-4 control-label" style="color: black;">Masukkan File<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
									</div>
								</div>
								<div class="form-group row">
									<label for="" class="col-sm-4 control-label" style="color: black;">Contoh Template Upload<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<a href="{{ url('uploads/upload_scrap/Template_Upload_Scrap.xlsx') }}">Template_Upload_Scrap.xlsx</a>
									</div>
								</div>
								<div class="modal-footer">
									<center>
										<button type="button" id="button_submit" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #3c9ce7;" onclick="UploadMp()">Upload</button>
									</center>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalCreate2">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<center><h3 style="background-color: #ff3200; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Daily Upload Scrap</h3>
								</center>
							</ul>
						</div>
						<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label for="" class="col-sm-4 control-label" style="color: black;">Bulan<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<div class="input-group date">
											<div class="input-group-addon bg-green" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="date" name="date" placeholder="Pilih Tanggal">
										</div>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label for="" class="col-sm-4 control-label" style="color: black;">Masukkan File<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<input type="file" name="upload_file2" id="upload_file2" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
									</div>
								</div>
								<div class="form-group row">
									<label for="" class="col-sm-4 control-label" style="color: black;">Contoh Template Upload<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<a href="{{ url('uploads/upload_scrap/Template_Upload_Scrap.xlsx') }}">Template_Upload_Scrap.xlsx</a>
									</div>
								</div>
								<div class="modal-footer">
									<center>
										<button type="button" id="button_submit" class="btn btn-succes" style="font-weight: bold; font-size: 1.3vw; width: 68%; color: white; background-color: #ff3200;" onclick="UploadMp2()">Upload</button>
									</center>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>



	</section>
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
		var arr = [];
		var arr2 = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			$('#date').datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true
			});

			$('.select2').select2({
				allowClear:true
			});
			Search();
		});

		function InputMp(){
			$('#modalCreate').modal('show');
		}

		function InputMp2(){
			$('#modalCreate2').modal('show');
		}

		function DownloadResumeExcel(){
			var tnj = $('#select_tunj').val()
			var on_month = $('#datefrom').val()
			var until_month = $('#dateto').val()

			var data = {
				tnj:tnj,
				on_month:on_month,
				until_month:until_month
			}

			$.post('{{ url("download/resume/tunjangan") }}',data, function(result, status, xhr){
				if(result.status){
					$('#section_tp').show();
					$('#section_tp').html("");
					var sections = "";
					sections += '<option value="">&nbsp;</option>';
					$.each(result.section, function(key, value) {
						sections += '<option value="'+value.section+'">'+value.section+'</option>';
					});

					$('#section_tp').append(sections);
				}
			});
		}

		function SubmitUpload(){
			$('#loading').show();

			var formData = new FormData();
			var newAttachment  = $('#upload_file').prop('files')[0];
			var file = $('#upload_file').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment', newAttachment);
			formData.append('bulan', $("#bulan").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('upload/update/karyawan') }}",
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
						$('#bulan').val("");
						$('#upload_file').val("");
						$('#modalUpdate').modal('hide');
						$('#loading').hide();
						Search();
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});
		}

		function UploadMp(){
			$('#loading').show();

			var formData = new FormData();
			var newAttachment  = $('#upload_file').prop('files')[0];
			var file = $('#upload_file').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment', newAttachment);
			formData.append('bulan', $("#bulan").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('upload/scrap/mirai') }}",
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
						$('#upload_file').val("");
						$('#modalCreate').modal('hide');
						$('#loading').hide();
						// Search();
						location.reload(true); 
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}
				}
			});
		}


		function UploadMp2(){
			$('#loading').show();

			var formData = new FormData();
			var newAttachment  = $('#upload_file2').prop('files')[0];
			var file = $('#upload_file2').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment', newAttachment);
			formData.append('date', $("#date").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('upload/daily/scrap/mirai') }}",
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
						$('#upload_file').val("");
						$('#modalCreate2').modal('hide');
						$('#loading').hide();
						// Search();
						location.reload(true); 
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}
				}
			});
		}

		function NextContrac(){
			$("#jangka_waktu").show();
			$("#option").hide();
		}

		function UpdateKaryawanContrac(){
			var data = {
				month_next:$('#month_next').val(),
				employee_id:$('#nik_update').text(),
			}
			$.get('{{ url("update/karyawan/contract") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#ModalDetailUpdate').modal('hide');
					Search();
				}
				else{
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function Download(){
			var data = {
				dateto:$('#dateto').val(),
				datefrom:$('#datefrom').val()
			}
			$.get('{{ url("download/data/calon/karyawan") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#ModalDetailUpdate').modal('hide');
				}
				else{
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function CreatePdf(nik){
			var data = {
				nik:nik
			}
			$.get('{{ url("create/pdf/calon/karyawan") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					Search();
				}
				else{
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function Search(){
			var data = {
				dateto:$('#dateto').val(),
				datefrom:$('#datefrom').val(),
				tunjangan:$('#select_tunj').val()
			}
			$.get('{{ url("fetch/upload/scrap/mirai") }}',data, function(result, status, xhr){
				if(result.status){
					$('#tableKaryawanKontrak').DataTable().clear();
					$('#tableKaryawanKontrak').DataTable().destroy();
					$('#bodyTableKaryawanKontrak').html("");
					var tableData = "";
					var index = 1;
					$.each(result.resumes, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ index +'</td>';
						tableData += '<td>'+ value.entry_date +'</td>';
						tableData += '<td>'+ value.posting_date +'</td>';
						tableData += '<td>'+ value.movement_type +'</td>';
						tableData += '<td>'+ value.material_number+'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.storage_location +'</td>';
						tableData += '<td>'+ value.receive_location +'</td>';
						tableData += '<td>'+ value.reference +'</td>';
						tableData += '<td>'+ value.remark +'</td>';
						index++;
					});
					$('#bodyTableKaryawanKontrak').append(tableData);

					var table = $('#tableKaryawanKontrak').DataTable({
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 100,
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
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function ModalKaryawan(){
			$('#ModalDetailUpdate').modal('show');
			$("#jangka_waktu").hide();
		}

		function ModalKaryawanKontrak(employee_id){
			var data = {
				employee_id:employee_id
			};
			$.get('<?php echo e(url("fetch/karyawan/kontrak")); ?>', data, function(result, status, xhr){
				if(result.status){
					ModalKaryawan();
					$('#DetailKaryawanKontrak').DataTable().clear();
					$('#DetailKaryawanKontrak').DataTable().destroy();
					var tableData = '';
					$('#BodyDetailKaryawanKontrak').html("");
					$('#BodyDetailKaryawanKontrak').empty();
					$.each(result.data, function(key, value) {
						tableData += '<tr>';
						tableData += '<td id="nik_update">'+ value.employee_id +'</td>';
						tableData += '<td>'+ value.name +'</td>';
						tableData += '<td>'+ value.department +'</td>';
						tableData += '<td>'+ value.section +'</td>';
						tableData += '<td>'+ value.group +'</td>';
						tableData += '<td>'+ value.sub_group +'</td>';
						tableData += '</tr>';
					});
					$('#BodyDetailKaryawanKontrak').append(tableData);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}

		function clearConfirmation(){
			location.reload(true);		
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