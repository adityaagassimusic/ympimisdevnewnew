@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: black;
	}
	table.table-bordered > tbody > tr > td{
		color: black;
		background-color: white;
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
	}

</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		Patrol S-Up GM & Presdir
	</h1>
	<ol class="breadcrumb">
		<?php $user = STRTOUPPER(Auth::user()->username) ?>

		<button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Ganti Lokasi
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
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<div class="col-xs-12" style="padding: 0" id="poin_head">
					<?php $no = 1; ?>
					<div class="col-xs-12">
						<center><span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> S Up Target <i class="fa fa-angle-double-down"></i></span></center>
					</div>
					@foreach($poin as $point)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							<button class="btn btn-success" id="point_{{$no}}" style="width: 99%;font-size: 1.3vw;font-weight: bold;white-space: normal;height: 100px;" onclick="getData(this.id)">
								{{$point}}
							</button>
						</center>
					</div>
					<?php $no++ ?>
					@endforeach
				</div>
				<div class="col-xs-12" id="poin_fix" style="padding-top: 5px;padding: 0">
					<div class="col-xs-12" style="padding: 0">
						<button class="btn btn-success" id="poin_fix2" style="width: 100%;font-size: 1.5vw;font-weight: bold;" onclick="changePoin()">#0</button>
					</div>
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_1">
					<?php $no_sup = 1; ?>
					@foreach($point_1 as $poin_1)

					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_1}}_1" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_1}}
							</button>
						</center>
					</div>

					<?php $no_sup++ ?>
					@endforeach
				</div>
				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_2">
					<?php $no_sup = 1; ?>
					@foreach($point_2 as $poin_2)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_2}}_2" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_2}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_3">
					<?php $no_sup = 1; ?>
					@foreach($point_3 as $poin_3)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_2}}_3" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_3}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_4">
					<?php $no_sup = 1; ?>
					@foreach($point_4 as $poin_4)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_4}}_4" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_4}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_5">
					<?php $no_sup = 1; ?>
					@foreach($point_5 as $poin_5)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_5}}_5" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_5}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_6">
					<?php $no_sup = 1; ?>
					@foreach($point_6 as $poin_6)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_6}}_6" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_6}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_7">
					<?php $no_sup = 1; ?>
					@foreach($point_7 as $poin_7)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_7}}_7" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_7}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" style="padding: 0" id="isi_poin_head_8">
					<?php $no_sup = 1; ?>
					@foreach($point_8 as $poin_8)
					<div class="col-xs-3" style="padding-top: 5px;margin: 0;padding-right: 0; padding-left: 0;">
						<center>
							@if($no_sup <= 4)
							<center><span style="font-size: 30px; color: blue;"><i class="fa fa-angle-double-down"></i> Step {{$no_sup}} <i class="fa fa-angle-double-down"></i></span></center>
							@endif
							<button class="btn btn-warning" id="{{$poin_8}}_8" style="width: 99%;font-size: 1.1vw;white-space: normal;height: 100px;" onclick="getDataIsi(this.id)">
								{{$poin_8}}
							</button>
						</center>
					</div>
					<?php $no_sup++ ?>
					@endforeach
				</div>

				<div class="col-xs-12" id="isi_poin_fix" style="padding: 0;padding-top: 5px;padding-bottom: 5px;">
					<div class="col-xs-12" style="padding: 0">
						<button class="btn btn-warning" id="isi_poin_fix2" style="width: 100%;font-size: 1vw;font-weight: bold;" onclick="changePoinIsi()">
							#1
						</button>
						<input type="hidden" id="isi_isi">
					</div>
				</div>


				<div class="col-xs-12" style="padding: 0;padding-top: 5px;">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 0px;">
						<thead>
							<tr>
								<th style="width:15%; background-color: #0e92eb; color: white; text-align: center; padding:0;font-size: 18px;border: 1px solid black" colspan="3">General Information</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Patrol Date</td>
								<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black"><?= date("d F Y") ?></td>
							</tr>
							<tr>
								<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Auditor</td>
								<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="employee_name"></td>
							</tr>
							<tr>
								<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Category</td>
								<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="category"></td>
							</tr>
							<tr>
								<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Location</td>
								<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="location"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<input type="hidden" id="employee_id">

			<div class="col-xs-12" style="overflow-x: scroll;padding: 0">

				<!-- <div class="pull-right">
					<a type="button" class="btn btn-success btn-lg" onclick='tambah();'><i class='fa fa-plus' ></i> Tambah Data</a>
				</div> -->

				<!-- <br><br><br> -->

				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; color: white; background-color: #000000;">
						<tr>
							<th style="color: white;border-right: 1px solid white">Patrol Detail / Topic</th>
							<th style="color: white;border-right: 1px solid white">Photo</th>
							<th style="color: white;border-right: 1px solid white">Problem</th>
							<th style="color: white;border-right: 1px solid white">PIC</th>
							<!-- <th style="color: white;border-right: 1px solid white;width: 1%">Act</th> -->
						</tr>
					</thead>
					<tbody id="body_cek">
						<tr class="member">
							<td width="10%">
								<!-- <input type="text" class="form-control patrol_detail" id="patrol_detail" name="patrol_detail" placeholder="Patrol Detail" required=""> -->
								<select class="form-control select3 patrol_detail" id="patrol_detail" data-placeholder="Patrol Topic" style="width: 100%; font-size: 20px;">
									<option></option>
									<option value="S-Up and 5S">S-Up and 5S</option>
									<option value="Environment">Environment</option>
									<option value="Health">Health</option>
									<option value="Safety">Safety</option>
								</select>
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURL(this,'');" id="file" style="display:none" class="file">
									<button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

									<img width="150px" id="blah" src="" style="display: none" alt="your image" />
									<br><span class="text-red">*Disarankan Upload Foto Dalam Bentuk Landscape</span>
								</center>
							</td>
							<td width="10%">
								<textarea id="patrol_note" height="100%" class="form-control note"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2 patrol_pic" id="patrol_pic" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
								<!-- <input type="hidden" id="patrol_pic_name"> -->
							</td>
							<!-- <td><button class="btn btn-danger" onclick="delete_confirmation(this)"><i class="fa fa-close"></i></button></td> -->
						</tr>
					</tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%;font-size: 25px" onclick="cek()"><i class="fa fa-check"></i> Submit</button>
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
						<label>Pilih Kategori</label>
						<select class="form-control select3" id="selectCategory" data-placeholder="Pilih Kategori..." style="width: 100%; font-size: 20px;">
							<option value=""></option>
							<option value="S-Up And EHS Patrol Presdir">S-Up And EHS Patrol Presdir</option>
							<option value="5S Patrol GM">5S Patrol GM Production</option>
							<option value="5S Patrol GM PS">5S Patrol GM Production Support</option>
						</select>
					</div>

					<div class="form-group">
						<label>Pilih Lokasi</label>
						<select class="form-control select3" id="selectLocation" data-placeholder="Pilih Lokasi..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($location as $loc)
							<option value="{{ $loc }}">{{ $loc }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-success pull-right" onclick="selectData()" style="width: 33%">Submit</button>
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
				<a  class="btn btn-danger" href="{{ url('') }}">Tutup</button>
					<!-- <a href="{{ url("index/audit_iso/cek_report/")}}" class="btn btn-success btn-sm" target="_blank" style="color:white;margin-right: 5px"><i class="fa fa-file-pdf-o"></i> Cek Laporan Hasil {{ $page }} </a> -->
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-success">Lihat Report Hasil Audit</a>
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


		var counter = 2;
		var add_point = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
			$('.select2').prop('selectedIndex', 0).change();
			$('.select2').select2({
				dropdownAutoWidth : true,
				allowClear:true
			});

			$('.select3').select2({
				minimumResultsForSearch : -1,
				dropdownAutoWidth : true,
				allowClear:true
			});

			$('#modalFirst').modal({
				backdrop: 'static',
				keyboard: false
			});


			$('#poin_fix').hide();
			$('#isi_poin_head_1').hide();
			$('#isi_poin_head_2').hide();
			$('#isi_poin_head_3').hide();
			$('#isi_poin_head_4').hide();
			$('#isi_poin_head_5').hide();
			$('#isi_poin_head_6').hide();
			$('#isi_poin_head_7').hide();
			$('#isi_poin_head_8').hide();
			$('#isi_poin_fix').hide();
			$('#isi_poin_fix2').hide();
		})

		function selectData(id){

			var kategori = $('#selectCategory').val();
			var lokasi = $('#selectLocation').val();

			if(kategori == ""){
				$("#loading").hide();
				alert("Kolom Kategori Harap diisi");
				$("html").scrollTop(0);
				return false;
			}
			else if(kategori == "S-Up And EHS Patrol Presdir"){
				$('#employee_name').html("Hiromichi Ichimura");
				$('#employee_id').val("PI2111044");
			}
			else if(kategori == "5S Patrol GM"){
				$('#employee_name').html("Yukitaka Hayakawa");
				$('#employee_id').val("PI1206001");
			}
			else if(kategori == "5S Patrol GM PS"){
				$('#employee_name').html("Budhi Apriyanto");
				$('#employee_id').val("PI0109004");
			}
			else{
				$('#employee_name').html("{{$employee->name}}");
				$('#employee_id').val("{{$employee->employee_id}}");
			}


			if(lokasi == ""){
				$("#loading").hide();
				alert("Kolom Lokasi Harap diisi");
				$("html").scrollTop(0);
				return false;
			}

			$('#modalFirst').modal('hide');

			$('#category').html(kategori);
			$('#location').html(lokasi);

		}

		function get_check() {

			var category = $('#category').text();

			var data = {
				category:category
			}

			$("#loading").show();

			$.get('{{ url("fetch/audit_patrol") }}', data, function(result, status, xhr){
				$("#loading").hide();
				openSuccessGritter("Success","Data Has Been Load");
				var body = "";

				$('#tableResult').DataTable().clear();
				$('#tableResult').DataTable().destroy();
			// $('#body_cek').html("");

			count = 1;

			$.each(result.lists, function(index, value){
				body += "<tr>";
				body += "<td width='1%'>"+count+"</td>";
				body += "<td width='5%' id='klausul_"+count+"'>"+value.klausul+"<input type='hidden' id='id_point_"+count+"' value='"+value.id+"'><input type='hidden' id='jumlah_point_"+count+"' value='"+result.lists.length+"'></td>";
				body += "<td width='15%' id='point_judul_"+count+"'>"+value.point_judul+"</td>";
				body += "<td width='20%' id='point_question_"+count+"'>"+value.point_question+"</td>";
				body += "<td width='20%'><label class='radio' style='margin-top: 5px;margin-left: 5px'>Good<input onclick='goodchoice(this.id)' type='radio' id='status_"+count+"' name='status_"+count+"' value='Good'><span class='checkmark'></span></label><label class='radio' style='margin-top: 5px;margin-left: 5px'>Not Good<input type='radio' id='status_"+count+"' name='status_"+count+"' value='Not Good' onclick='notgoodchoice(this.id)'><span class='checkmark'></span></label></td>";
				body += "<td width='20%'><textarea id='note_"+count+"' height='50%' style='display:none'></textarea></td>";
				var idid = '#file_'+count;
				body += '<td width="15%"><input type="file" style="display:none" onchange="readURL(this,\''+count+'\');" id="file_'+count+'"><button class="btn btn-primary btn-lg" id="btnImage_'+count+'" value="Photo" style="display:none" onclick="buttonImage(this)">Photo</button><img width="150px" id="blah_'+count+'" src="" style="display: none" alt="your image" /></td>';
				body += "</tr>";
				count++;
			})

			$("#body_cek").append(body);

			var table = $('#tableResult').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false
			} );
		})
		}

		function tambah(){

			body = "";

			body += '<tr class="member">';
			// body += '<td width="10%"><select class="form-control select3 patrol_lokasi" id="patrol_lokasi" data-placeholder="Pilih Lokasi..." style="width: 100%; font-size: 20px;" required><option></option>@foreach($location as $loc)<option value="{{ $loc }}">{{ $loc }}</option>@endforeach</select></td>'; 
			body += '<td width="10%"><select class="form-control select3 patrol_detail" id="patrol_detail" data-placeholder="Patrol Topic" style="width: 100%; font-size: 20px;"><option></option><option value="S-Up and 5S">S-Up and 5S</option><option value="Environment">Environment</option><option value="Health">Health</option><option value="Safety">Safety</option></select></td>';
			body += '<td width="10%"><center><input type="file" onchange="readURL(this,\'\');" id="file" style="display:none" class="file"><button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button><img width="150px" id="blah" src="" style="display: none" alt="your image" /></center></td>';
			body += '<td width="10%"><textarea id="patrol_note" height="50%" class="form-control note"></textarea></td>';
			body += '<td width="10%"><select class="form-control select3 patrol_pic" id="patrol_pic"  data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;"><option></option>@foreach($auditee as $audite)<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>@endforeach</select></td>';
			body +=	'<td><button class="btn btn-danger" onclick="delete_confirmation(this)"><i class="fa fa-close"></i></button></td>';

			body += '</tr>';

			$("#body_cek").append(body);

			$(function () {
				$('.select3').select2({
					dropdownAutoWidth : true,
					allowClear:true
				});
			});

		}

		function buttonImage(elem) {
			$(elem).closest("td").find("input").click();
			// console.log(input);
		}

		const compressImage = async (file, {
            quality = 1,
            type = file.type
        }) => {

            const imageBitmap = await createImageBitmap(file);

            const canvas = document.createElement('canvas');
            canvas.width = imageBitmap.width;
            canvas.height = imageBitmap.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(imageBitmap, 0, 0);

            const blob = await new Promise((resolve) =>
                canvas.toBlob(resolve, type, quality)
            );

            return new File([blob], file.name, {
                type: blob.type,
            });
        };

        const input = document.querySelector('.file');
        input.addEventListener('change', async (e) => {            
            const {
                files
            } = e.target;
            
            if (!files.length) return;
            
            const dataTransfer = new DataTransfer();
            
            for (const file of files) {

                if (!file.type.startsWith('image')) {                    
                    dataTransfer.items.add(file);
                    continue;
                }
                
                const compressedFile = await compressImage(file, {
                    quality: 0.3,
                    type: 'image/jpeg',
                });
                
                dataTransfer.items.add(compressedFile);
                
                readURL(compressedFile);
            }

            // Set value of the file input to our new files list
            e.target.files = dataTransfer.files;
        });

        function readURL(compressedFile) {
            var reader = new FileReader();
            var img = $(input).closest("td").find("img");
            reader.onload = function(e) {
                $(img).show();
                $(img).attr('src', e.target.result);
            };
                reader.readAsDataURL(input.files[0]);
            $(input).closest("td").find("button").hide();
        }

		// function readURL(input,idfile) {
		// 	if (input.files && input.files[0]) {
		// 		var reader = new FileReader();

		// 		reader.onload = function (e) {
		// 			var img = $(input).closest("td").find("img");
		// 			$(img).show();
		// 			$(img)
		// 			.attr('src', e.target.result);
		// 		};

		// 		reader.readAsDataURL(input.files[0]);
		// 	}

		// 	$(input).closest("td").find("button").hide();
		// 	// $('#btnImage'+idfile).hide();
		// }

		function cek() {
			if (confirm('Apakah Anda yakin?')) {
				$('#loading').show();

				var audit_data = [];
				// var patrol_lokasi = [];
				var patrol_detail = [];
				var note = [];
				var patrol_pic = [];
				var file = [];

				// $('.patrol_lokasi').each(function(i, obj) {
				// 	patrol_lokasi.push($(this).val());
				// })

				// $('.patrol_detail').each(function(i, obj) {
				// 	patrol_detail.push($(this).val());
				// })

				// $('.note').each(function(i, obj) {
				// 	note.push($(this).val());
				// })

				// $('.patrol_pic').each(function(i, obj) {
				// 	patrol_pic.push($(this).val());
				// })	

				// $('.file').each(function(i, obj) {
				// 	formData.append('file_datas_'+i, $(this).prop('files')[0]);
				// })	
				

				// $('.member').each(function(index, value) {
				// 	var ids = $(this).attr('id');


				// 	var auditor_id =  '{{$employee->employee_id}}';
				// 	var auditor_name =  '{{$employee->name}}';


				// 	audit_data.push({
				// 		'category': $('#category').text(),
				// 		'auditor_id': $('#employee_id').text(),
				// 		'auditor_name' : $('#employee_name').text(),
				// 		'lokasi': $('#patrol_lokasi').val(),
				// 		'detail': $('#patrol_detail').val(),
				// 		'note': $('#patrol_note').val(),
				// 		'pic': $('#patrol_pic').val()
				// 	});
				// });w

				// var data = {
				// 	patrol_lokasi : patrol_lokasi,
				// 	patrol_detail : patrol_detail,
				// 	note : note,
				// 	patrol_pic : patrol_pic,
				// 	category : $('#category').text(),
				// 	auditor_id : $('#employee_id').text(),
				// 	auditor_name  : $('#employee_name').text()
				// }

				// $.post('{{ url("post/audit_patrol") }}', data,  function(result, status, xhr){
				// 	openSuccessGritter("Success", "Audit Berhasil Dilaksanakan");
				// 	location.reload();
				// })

				var len = $('.member').length;

				var formData = new FormData();

				var category_patrol = $('#category').text();
				if (category_patrol == "5S Patrol GM PS") {
					category_patrol = "5S Patrol GM";
				}

				formData.append('jumlah', len);				
				formData.append('category', category_patrol);
				formData.append('location', $('#location').text());
				formData.append('auditor_id', $('#employee_id').val());
				formData.append('auditor_name',  $('#employee_name').text());
				formData.append('poin_fix',  $.trim($('#poin_fix2').text()));
				formData.append('isi_poin_fix',  $.trim($('#isi_poin_fix2').text()));

				$('.file').each(function(i, obj) {
					formData.append('file_datas_'+i, $(this).prop('files')[0]);
					var file=$(this).val().replace(/C:\\fakepath\\/i, '').split(".");

					formData.append('extension_'+i, file[1]);
					formData.append('foto_name_'+i, file[0]);
				})	

				// $('.patrol_lokasi').each(function(i, obj) {
				// 	formData.append('patrol_lokasi_'+i, $(this).val());
				// })

				$('.patrol_detail').each(function(i, obj) {
					formData.append('patrol_detail_'+i, $(this).val());
				})

				$('.note').each(function(i, obj) {
					formData.append('note_'+i, $(this).val());
				})

				$('.patrol_pic').each(function(i, obj) {
					formData.append('patrol_pic_'+i, $(this).val());
				})	


				$.ajax({
					url:"{{ url('post/audit_patrol_file') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success: function (response) {
						$("#loading").hide();
						$("#patrol_detail").val("").trigger("change");
						$("#patrol_note").val("");
						$("#patrol_pic").val("").trigger("change");
						$("#blah").hide();
						$("#btnImage").show();
						openSuccessGritter("Success", "Audit Berhasil Disimpan");
						// location.reload();
					},
					error: function (response) {
						console.log(response.message);
					},
				})
			}
		}

	// function selectemployee(){
	//          var pic = document.getElementById("patrol_pic").value;

	//          $.ajax({
	//           url: "{{ url('index/audit_iso/get_nama') }}?auditee=" +pic, 
	//           type : 'GET', 
	//           success : function(data){
	//            var obj = jQuery.parseJSON(data);
	//            $('#patrol_pic_nama').val(obj[0].name);
	//          }
	//        });      
	//    }

	function delete_confirmation(elem) {
		$(elem).closest('tr').remove();
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

	function getData(poin) {

		$('#poin_head').hide();
		$('#poin_fix').show();
		$('#poin_fix2').html($('#'+poin).text());

		var point = poin.split('_');

		var length = '{{count($poin)}}';
		for (var i = 0; i <= length; i++) {
			if (point[1] == i) {
				$('#isi_poin_head_'+point[1]).show();
				$('#isi_poin_fix').hide();
				$('#isi_poin_fix2').hide();
				$('#nomor_fix').val(point[1]);
			}else{
				$('#isi_poin_head_'+i).hide();
				$('#isi_poin_fix').hide();
				$('#isi_poin_fix2').hide();
				$('#nomor_fix').val("");
			}
		}
	}

	function changePoin() {
		$('#poin_head').show();
		$('#poin_fix').hide();
		$('#poin_fix2').html('#0');

		var length = '{{count($poin)}}';
		for (var i = 0; i <= length; i++) {
			$('#isi_poin_head_'+i).hide();
		}
		$('#isi_poin_fix').hide();
	}

	function getDataIsi(detail_poin) {

		var isi = detail_poin.split('_');

		var length = '{{count($poin)}}';
		for (var i = 0; i <= length; i++) {
			$('#isi_poin_head_'+i).hide();
		}
		// $('#isi_poin_head').hide();
		$('#isi_poin_fix').show();
		$('#isi_poin_fix2').show();
		$('#isi_poin_fix2').html(isi[0]);
		$('#isi_isi').val(isi[1]);
	}

	function changePoinIsi(value) {

		var i = $('#isi_isi').val();

		var length = '{{count($poin)}}';

		$('#isi_poin_head_'+i).show();

		$('#isi_poin_fix').hide();
		$('#isi_poin_fix2').html('#1');
	}

</script>
@endsection