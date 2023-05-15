@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
		height: 45px;
		text-align: center;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(100, 100, 100);
		vertical-align: middle;
	}
	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}
	#loading, #error { display: none; }
</style>

@section('header')
<section class="content-header" style="padding-bottom: 40px">
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
	<h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h1>
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<center style="padding-top: 350px;">
			<span style="font-size: 50px; color: white">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</center>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body" style="background-color: #78a1d0; text-align: center">
					<span style="font-weight: bold; font-size: 1.6vw;"><span id="id_kode_soal"></span></span>
					<input type="hidden" id="nama_tim" value="{{ $kode }}">
					<input type="hidden" id="kode_soal">
				</div>
			</div>
		</div>

		<div class="col-xs-6">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<table class="table table-bordered table-hover" style="margin-bottom: 0;">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="text-align: center;">Gambar Ilustrasi</th>
							</tr>
						</thead>
					</table><br>
					<div id="attach_pdf" align="center"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="box box-solid" style="border: 1px solid grey;">
				<div class="box-body">
					<table id="TableResume" class="table table-bordered table-hover" style="margin-bottom: 0;">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="text-align: center;">Penjelasan</th>
							</tr>
						</thead>
						<tbody id="bodyTableResume" style="background-color: #fcf8e3;">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
				<div class="box-body">
					<table id="TableResumeKunci" class="table table-bordered table-hover" style="margin-bottom: 0;">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="text-align: center;">Kata Kunci</th>
							</tr>
						</thead>
						<tbody id="bodyTableResumeKunci" style="background-color: #fcf8e3;">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
<script src="{{ url('js/highcharts.js')}}"></script>
<script src="{{ url('js/exporting.js')}}"></script>
<script src="{{ url('js/export-data.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		// DataResume();
		ViewSoal();
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function ViewSoal(){
		$.get('{{ url("fetch/soal/ky") }}', function(result, status, xhr){
			if(result.status){
				$('#loading').show();
				$('#TableResume').DataTable().clear();
				$('#TableResume').DataTable().destroy();
				var tableData = "";
				$.each(result.resumes, function(key, value) {

					var nomer = key+1;

					var judul = value.kode_soal+' - '+value.remark;
					$('#id_kode_soal').html(judul);
					$('#kode_soal').html(value.kode_soal);

					$('#attach_pdf').html('');
					var pdf = "{{ url('data_file/std/ky') }}"+'/'+value.nama_gambar;
					$('#attach_pdf').append('<img src="'+pdf+'" style="width: 40vw; height: auto">');

					tableData += '<tr>';
					tableData += '<td>'+ value.soal +'</td>';
					tableData += '</tr>';		
				});

				$('#bodyTableResume').append(tableData);

				$('#TableResumeKunci').DataTable().clear();
				$('#TableResumeKunci').DataTable().destroy();
				var tableData = "";
				$.each(result.a, function(key, value) {

					var nomer = key+1;

					var kunci_jawaban = value.jawaban.split('/');;

					tableData += '<tr>';
					tableData += '<td>'+ kunci_jawaban[key] +'</td>';
					tableData += '</tr>';		
				});

				$('#bodyTableResumeKunci').append(tableData);

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function SelectKaryawan(){
		$('#loading').show();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto
		}

		$.post('<?php echo e(url("select/match/data")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!', 'OK');
				$('#datefrom').val();
				$('#dateto').val();
				DataResume()
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Pokok Salah');
				$('#datefrom').val();
				$('#dateto').val();
			}
		});
	}
</script>
@endsection