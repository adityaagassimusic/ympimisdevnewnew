@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">

	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: black;
	}
	table.table-bordered > tbody > tr > td{
		color: black;
	}

	#loading { display: none; }


	.radio {
		display: inline-block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 16px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	/* Hide the browser's default radio button */
	.radio input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}

	/* Create a custom radio button */
	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 25px;
		width: 25px;
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
		background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}

	#tableResult > thead > tr > th {
		border: 1px solid black;
	}

	#tableResult > tbody > tr > td {
		border: 1px solid #b0bec5;
		background-color: white;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		Audit AEO
	</h1>
	<ol class="breadcrumb">
		<?php $user = STRTOUPPER(Auth::user()->username) ?>

		<button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
			<i class="fa fa-edit"></i>&nbsp;&nbsp;Ganti Lokasi
		</button>

	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if ($errors->has('password'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Alert!</h4>
		{{ $errors->first() }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>	
	@endif

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-4" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: #673ab7; text-align: center; color: white; padding:0;font-size: 18px;border: 0" colspan="3">General Information</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: left; color: white; font-size:20px; width: 30%;border: 1px solid black">&nbsp;Audit Date</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;border: 1px solid black">&nbsp;<?= date("d-m-Y") ?></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: left; color: white; font-size:20px; width: 30%;border: 1px solid black">&nbsp;Auditor</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;border: 1px solid black" id="employee_id">&nbsp;{{ $employee->employee_id }} - {{ $employee->name }}</td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: left; color: white; font-size:20px; width: 30%;border: 1px solid black">&nbsp;PIC</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;border: 1px solid black" id="location"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: left; color: white; font-size:20px; width: 30%;border: 1px solid black">&nbsp;Auditee</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;border: 1px solid black" id="auditee_name"></td>
							<input type="hidden" id="auditee_id_fix">
							<input type="hidden" id="auditee_name_fix">
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-12" style="overflow-x: scroll;padding: 0">
				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; color: black; background-color: #cddc39;">
						<tr>
							<th>No.</th>
							<!-- <th>Klausul</th> -->
							<th>Kriteria</th>
							<th>Pertanyaan</th>
							<th>Jawaban</th>
							<!-- <th>Remark</th> -->
							<th>Evidence</th>
						</tr>
					</thead>
					<tbody id="body_cek">
						
					</tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%" onclick="cek()"><i class="fa fa-check"></i>Check</button>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalFirst">
	<div class="modal-dialog modal-sm" style="width: 400px">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label>Pilih Lokasi</label>
						<select class="form-control select2" id="selectLocation" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
							<option></option>
		                    <option value="Human Resources">Human Resources</option>
		                    <option value="General Affairs">General Affairs</option>
		                    <option value="Accounting">Accounting</option>
		                    <option value="Maintenance">Maintenance</option>
		                    <option value="MIS">MIS</option>
		                    <option value="Logistic">Logistic</option>
		                    <option value="Standardization">Standardization</option>
		                    <option value="Production Control">Production Control</option>
		                    <option value="Purchasing">Purchasing</option>
		                    <option value="Production">Production</option>
		                    <option value="Production Engineering">Production Engineering</option>
		                    <option value="QA">QA</option>
		                    <option value="QA Production">QA Production</option>
						</select>
					</div>
					<div class="form-group">
						<label>Pilih Auditee</label>
						<select class="form-control select3" id="auditee" onchange="selectemployee(this)" data-placeholder="Pilih Auditee" style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($auditee as $audite)
							<option value="{{ $audite->employee_id }}_{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-success pull-right" onclick="selectData()">Submit</button>


						@if(Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "S-LOG" || Auth::user()->role_code == "C-LOG")
						<br><br>
						<a class="btn btn-primary btn-sm" style="width: 100%" href="{{ url("/index/audit_aeo/point_check") }}">
							<i class="fa fa-plus"></i>&nbsp;&nbsp;Point Check & Hasil Audit
						</a>
						@endif

					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Input Confirmation</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<a  class="btn btn-danger" href="{{ url('index/audit_aeo') }}">Tutup</a>
				<!-- <a href="{{ url("index/audit_iso/cek_report/")}}" class="btn btn-success btn-sm" target="_blank" style="color:white;margin-right: 5px"><i class="fa fa-file-pdf-o"></i> Cek Laporan Hasil {{ $page }} </a> -->
				<!-- <a id="modalDeleteButton" href="#" type="button" class="btn btn-success">Buat Laporan Audit ISO</a> -->
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').prop('selectedIndex', 0).change();
		$('.select2').select2({
			minimumResultsForSearch : -1
		});

		$('.select3').select2();

		$('#modalFirst').modal({
			backdrop: 'static',
			keyboard: false
		});
	})

	function selectData(id){

		var kategori = $('#selectCategory').val();
		var lokasi = $('#selectLocation').val();
		var auditee = $('#auditee_id_fix').val();
		var auditee_name = $('#auditee_name_fix').val();

		if(kategori == ""){
			$("#loading").hide();
			alert("Kolom Kategori Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		if(lokasi == ""){
			$("#loading").hide();
			alert("Kolom Lokasi Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		if(auditee == ""){
			$("#loading").hide();
			alert("Kolom Auditee Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		$('#modalFirst').modal('hide');

		$('#location').html(lokasi);
		$('#auditee_name').html(auditee_name);

		get_check();
	}

	function get_check() {

		var location = $('#location').text();

		var data = {
			location:location
		}

		$("#loading").show();

		$.get('{{ url("fetch/audit_aeo") }}', data, function(result, status, xhr){
			$("#loading").hide();
			openSuccessGritter("Success","Data Has Been Load");
			var body = "";
			$('#tableResult').DataTable().clear();
			$('#tableResult').DataTable().destroy();
			$('#body_cek').html("");

			count = 1;

			$.each(result.lists, function(index, value){
				body += "<tr>";
				body += "<td width='1%'>"+count+" <input type='hidden' id='jumlah_point_"+count+"' value='"+result.lists.length+"'></td>";
				// body += "<td width='5%' id='klausul_"+count+"'>"+value.klausul+"<input type='hidden' id='id_point_"+count+"' value='"+value.id+"'></td>";
				body += "<td width='10%' id='point_judul_"+count+"'>"+value.point_judul+"</td>";
				body += "<td width='20%' id='point_question_"+count+"'>"+value.point_question+"</td>";
				body += "<td width='40%'><textarea id='note_"+count+"'></textarea></td>";
				// body += "<td ></td>";
				

				// var idid = '#file_'+count;
				body += '<td width="20%"><input type="file" id="file_'+count+'"></td>';
				body += "</tr>";

				count++;
			})

			$("#body_cek").append(body);		

			var count = 1;

			$.each(result.lists, function(index, value){	
				CKEDITOR.replace('note_'+count ,{
			        filebrowserImageBrowseUrl : "{{ url('kcfinder_master') }}",
			        height: '200px'
			    });
				count++;
			});			

			var table = $('#tableResult').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false
			} );
		})
	}

	// function buttonImage(idfile) {
	// 	$(idfile).click();
	// }

	// function goodchoice(id) {
	// 	var idid = id.split('_');
	// 	$('#note_'+idid[1]).hide();
	// 	$('#btnImage_'+idid[1]).hide();
	// }

	// function notgoodchoice(id) {
	// 	var idid = id.split('_');
	// 	$('#note_'+idid[1]).show();
	// 	$('#btnImage_'+idid[1]).show();
	// }

	// function readURL(input,idfile) {
	// 	if (input.files && input.files[0]) {
	// 		var reader = new FileReader();

	// 		reader.onload = function (e) {
	// 			$('#blah_'+idfile).show();
	// 			$('#blah_'+idfile)
	// 			.attr('src', e.target.result);
	// 		};

	// 		reader.readAsDataURL(input.files[0]);
	// 	}
	// 	$('#btnImage_'+idfile).hide();
	// }

	function cek() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var audit = [];
			var countpoint = parseInt($('#jumlah_point_1').val());
			var stat = 0;
			for (var z = 1; z <= countpoint; z++) {
				if(CKEDITOR.instances['note_'+z].getData() == "" || $('#file_'+z).prop('files')[0] == undefined){
					$('#loading').hide();
					alert('Mohon Lengkapi Jawaban dan Evidence');
					return false;
				}
			}

			for(var i = 0; i < countpoint; i++){
				var a = i+1;

				var tanggal = '{{date("Y-m-d")}}';
				var kategori =  'AEO';
				var lokasi =  $('#location').text();
				var auditor_id =  '{{$employee->employee_id}}';
				var auditor_name =  '{{$employee->name}}';
				var auditee =  $('#auditee_id_fix').val();
				var auditee_name =  $('#auditee_name_fix').val();
				// var klausul =  $('#klausul_'+a).text();
				var point_judul =  $('#point_judul_'+a).text();
				var point_question =  $('#point_question_'+a).text();
				// var note =  $('#note_'+a).val();
				var note = CKEDITOR.instances['note_'+a].getData();
				// var idstatus = 'input[id="status_'+a+'"]:checked';
				// var status = $(idstatus).val();
				// var id_point = $('#id_point_'+a).val();
				var fileData  = $('#file_'+a).prop('files')[0];

				var file=$('#file_'+a).val().replace(/C:\\fakepath\\/i, '').split(".");

				var formData = new FormData();
				formData.append('fileData', fileData);
				formData.append('tanggal', tanggal);
				formData.append('kategori', kategori);
				formData.append('lokasi', lokasi);
				formData.append('auditor_id', auditor_id);
				formData.append('auditor_name', auditor_name);
				formData.append('auditee', auditee);
				formData.append('auditee_name', auditee_name);
				// formData.append('klausul', klausul);
				formData.append('point_judul', point_judul);
				formData.append('point_question', point_question);
				// formData.append('status', status);
				formData.append('note', note);
				// formData.append('id_point', id_point);
				formData.append('extension', file[1]);
				formData.append('foto_name', file[0]);


				$.ajax({
					url:"{{ url('input/audit_aeo') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success:function(data)
					{
						stat += 1;
						// openSuccessGritter('Success','Input Data Audit Berhasil');
						if (stat == countpoint) {
							$('#loading').hide();
							$('#myModal').modal('show');
							var url = 
							// jQuery('#myModal').attr("href", url+'/'+id+'/'+weekly_report_id);
							$('.modal-body').html("Terima Kasih telah mengisi Audit Internal AEO.<br>Data sudah berhasil disimpan ke database sistem.");
						}
				}
			})
			}
			

		}
	}

	function selectemployee(elem){
		var auditee = elem.value.split("_");
		var auditee_id_fix = auditee[0];
		var auditee_name_fix = auditee[1];
         
        $('#auditee_name').val(auditee_name_fix);
        $('#auditee_id_fix').val(auditee_id_fix);
        $('#auditee_name_fix').val(auditee_name_fix);
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