@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:left;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #dcdcdc;
		border: 1px solid black !important;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		color: black;
		/*background-color: white;*/
	}
	thead {
		/*background-color: rgb(126,86,134);*/
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
		font-weight: bold;
		font-size: 20px;
	}

	#loading, #error, #finish { display: none; }

	.blink{
		animation:blinker 1s linear infinite;
	}
	@keyframes blinker {
		50% {background-color: red},
		50% {background-color: yellow}
	}

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

	label {
		font-size: 20px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $title }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div id="finish" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0, 255, 115); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: black; top: 45%; left: 35%; text-align: center;">
				<i class="fa fa-check-square-o" style="font-size: 55px"></i><br>
				<span style="font-size: 40px">PENGECEKAN BERHASIL TERSIMPAN</span>
			</p>
		</div>		

		<div class="col-xs-10">
			<h2>POIN PENGECEKAN SAFETY PT. YMPI PADA SAAT AKAN BERLIBUR</h2>
		</div>
		<div class="col-xs-2 pull-right">
			No Dok. : YMPI/STD/FK3/040 <br>
			Rev : 03 <br>
			Tgl. : 10/05/2021 <br>
		</div>

		<form method="POST" id="main_form" autocomplete="off" enctype="multipart/form-data">
			<div class="col-xs-12">
				<div class="form-group">
					<label class="col-sm-2 control-label">PIC Pengecekan</label>
					<div class="col-sm-5"> : 
						<select class="form-control select2" id="pic" data-placeholder="Pilih PIC" onchange="pilih_pic(this)" style="width: 90%">
							<option value=""></option>
							@foreach($list_user as $usr)
							<option value="{{ $usr['emp_id'] }}">{{ $usr['emp_id'] }} - {{ $usr['name'] }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<div class="col-xs-12" id="dept_div" style="display: none">
				<div class="form-group">
					<label class="col-sm-2 control-label" style="margin-top: 10px">Departemen</label>
					<div class="col-sm-6" style="margin-top: 10px"> : 
						<select class="form-control select2" id="department" data-placeholder="Pilih Departemen" onchange="pilih_bagian(this)" style="width: 90%">
							<option value=""></option>
						</select>
					</div>
				</div>
			</div>

			<div class="col-xs-12">
				<div class="form-group">
					<label class="col-sm-2 control-label" style="margin-top: 10px">BAGIAN</label>
					<div class="col-sm-5" style="margin-top: 10px"> : 
						<select class="form-control select2" id="bagian" data-placeholder="Pilih Bagian" style="width: 90%">
							<option value=""></option>
						</select>
					</div>
				</div>
			</div>

			<div class="col-xs-12" style="margin-top: 10px" style="overflow-x: auto;">
				<table class="table table-bordered" style="width: 100%;" id="tableForm">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th style="width: 5%">No</th>
							<th style="width: 30%">Poin Pengecekan</th>
							<th style="width: 20%"><span class="text-red">*</span>Kondisi</th>
							<th style="width: 25%"><span class="text-red">*</span>Foto</th>
							<th style="width: 20%">Keterangan</th>
						</tr>
					</thead>
					<tbody id="bodyForm">
					</tbody>
				</table>
				<div id="div_prod">
					<h3 style="font-weight: bold; display: none" id="txtAdd">Point check tambahan (disesuaikan dengan kondisi area kerja) : <button class="btn btn-success" onclick="addNew()" type="button"> <i class="fa fa-plus"></i> Tambah</button></h3>
					<table class="table table-bordered" style="width: 100%; display: none" id="tableAddForm">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<!-- <th style="width: 5%">No</th> -->
								<th style="width: 30%"><span class="text-red">*</span>Poin Pengecekan</th>
								<th style="width: 20%"><span class="text-red">*</span>Kondisi</th>
								<th style="width: 25%">Foto</th>
								<th style="width: 20%">Keterangan</th>
								<th style="width: 1%">#</th>
							</tr>
						</thead>
						<tbody id="bodyAddForm">
						</tbody>
					</table>
					<h3 style="font-weight: bold; display: none" id="txtStandBy">Fasilitas di area kerja (harus on/standby 24 Jam) : <button class="btn btn-success" onclick="addStandBy()" type="button"> <i class="fa fa-plus"></i> Tambah</button></h3>
					<table class="table table-bordered" style="width: 100%; display: none" id="tableStandByForm">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<!-- <th style="width: 5%">No</th> -->
								<th style="width: 30%"><span class="text-red">*</span>Poin Pengecekan</th>
								<th style="width: 20%"><span class="text-red">*</span>Kondisi</th>
								<th style="width: 25%">Foto</th>
								<th style="width: 20%">Keterangan</th>
								<th style="width: 1%">#</th>
							</tr>
						</thead>
						<tbody id="bodyStandByForm">
						</tbody>
					</table>
				</div>
				<button type="submit" class="btn btn-success btn-lg" style="width: 100%"><i class="fa fa-check"></i> Simpan dan Kirim ke Atasan</button>
				<table style="font-weight: bold; font-size: 20px; width: 100%">
					<tr>
						<td style="width: 70%">
							Note : 
						</td>
					</tr>
					<tr>
						<td>
							*  Isi pada kolom Keterangan jika diperlukan sebagai tambahan informasi.
						</td>
					</tr>
					<tr>
						<td>
							* 	Untuk area ruang server dalam kondisi on/standby 24 jam (tidak boleh dimatikan)
						</td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</section>

@endsection
@section('scripts')
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

	var no_add = 0;
	var no_safety = 0;
	var usr = <?php echo json_encode($list_user); ?>;
	var bagian = <?php echo json_encode($list_bagian); ?>;
	var kategori_form = '';
	var dpt = '';
	var jml_form = 0;
	var jml_form_new = 0;
	var jml_form_safe = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		// fetchForm();
	})

	$(function () {
		$('.select2').select2()
	})

	function fetchForm(){
		var form = <?php echo json_encode($form); ?>;

		$("#bodyForm").empty();

		var body = '';
		var no_ofc = 0;
		var no_prd = 0;
		$.each(form, function(index, value){
			if (kategori_form == 'OFC' && value.area == 'Office') {
				jml_form +=1;

				body += '<tr>';
				body += '<td style="font-size: 15px" class="point_number" id="point_'+no_ofc+'"><center>'+value.point_number+'</center></td>';
				body += '<td style="font-size: 15px" class="point_check" id="check_'+no_ofc+'">'+value.point_check+'</td>';
				body += '<td>';
				body += '<label class="radio" style="margin-top: 5px">Sudah <input class="kodisi" type="radio" id="kondisi_sudah_'+no_ofc+'" name="kondisi_'+no_ofc+'" value="Sudah"><span class="checkmark"></span></label>';
				body += '&nbsp;&nbsp;';
				body += '<label class="radio" style="margin-top: 5px">Tidak Ada<input class="kodisi" type="radio" id="kondisi_belum_'+no_ofc+'" name="kondisi_'+no_ofc+'" value="Tidak Ada"><span class="checkmark"></span></label>';
				body += '</td>';
				body += '<td>';
				body += '<center><button class="btn btn-success btn-xs" onclick="add_photo(\''+no_ofc+'\')" type="button"><i class="fa fa-plus"></i> Tambah</button><div id="foto_'+no_ofc+'"></div></center>';
				body += '</td>';
				body += '<td><textarea class="form-control keterangan" id="note_'+no_ofc+'" placeholder="Isikan keterangan"></textarea></td>';
				body += '</tr>';

				no_ofc ++;
			} else if(kategori_form == 'PRD' && value.area == 'Production') {
				jml_form +=1;

				body += '<tr>';
				body += '<td style="font-size: 15px" class="point_number" id="point_'+no_prd+'"><center>'+value.point_number+'</center></td>';
				body += '<td style="font-size: 15px" class="point_check" id="check_'+no_prd+'">'+value.point_check+'</td>';
				body += '<td>';
				body += '<label class="radio" style="margin-top: 5px">Sudah <input class="kodisi" type="radio" id="kondisi_sudah_'+no_prd+'" name="kondisi_'+no_prd+'" value="Sudah"><span class="checkmark"></span></label>';
				body += '&nbsp;&nbsp;';
				body += '<label class="radio" style="margin-top: 5px">Tidak Ada<input class="kodisi" type="radio" id="kondisi_belum_'+no_prd+'" name="kondisi_'+no_prd+'" value="Tidak Ada"><span class="checkmark"></span></label>';
				body += '</td>';
				body += '<td>';
				body += '<center><button class="btn btn-success btn-xs" onclick="add_photo(\''+no_prd+'\')" type="button"><i class="fa fa-plus"></i> Tambah</button><div id="foto_'+no_prd+'"></div></center>';
				body += '</td>';
				body += '<td><textarea class="form-control keterangan" id="note_'+no_prd+'" placeholder="Isikan keterangan"></textarea></td>';	
				body += '</tr>';

				no_prd ++;


			}
		})

		$("#bodyForm").append(body);

		if (kategori_form == 'OFC') {
			$("#txtAdd").hide();
			$("#tableAddForm").hide();
			$("#txtStandBy").hide();
			$("#tableStandByForm").hide();
		} else {
			$("#txtAdd").show();
			$("#tableAddForm").show();
			$("#txtStandBy").show();
			$("#tableStandByForm").show();
		}
	}

	function pilih_pic(elem) {
		var val = $(elem).val();
		var remark = '';
		var option = '';
		var option2 = '';
		var dpt = '';

		$.each(usr, function(index2, value2){
			if (val == value2.emp_id) {
				remark = value2.remark;
				dpt = value2.department;
			}
		})

		$("#bagian").empty();
		$("#department").empty();
		option = '<option></option>';

		$.each(bagian, function(index, value){
			if (remark == 'OFC' && value.category == 'department') {
				if (val != 'PI0109004' && val != 'PI9905001' && val != 'PI9709001') {
					if (dpt == value.bagian) {
						option += '<option value="'+value.bagian+'" selected>'+value.bagian+'</option>';
					} else {
						option += '<option value="'+value.bagian+'">'+value.bagian+'</option>';
					}
				} 
			} else if ((val == 'PI0109004' || val == 'PI9905001' || val == 'PI9709001') && value.category == 'division') {
				if (dpt == value.bagian) {
					option += '<option value="'+value.bagian+'" selected>'+value.bagian+'</option>';
				} else {
					option += '<option value="'+value.bagian+'">'+value.bagian+'</option>';
				}
			}
			// else if (remark != 'OFC' && value.category == 'section') {
			// 	option += '<option value="'+value.bagian+'">'+value.bagian+'</option>';
			// }
		})

		if (remark != 'OFC') {
			$("#dept_div").show();

			var dept = [];

			$.each(bagian, function(index, value){
				if (value.category == 'group') {
					if(dept.indexOf(value.department) === -1){
						dept[dept.length] = value.department;
					}
				}
			})


			option2 = '<option></option>';
			$.each(dept, function(index, value){
				if (dpt == value) {
					option2 += "<option value='"+value+"' selected>"+value+"</option>";
				} else {
					option2 += "<option value='"+value+"'>"+value+"</option>";
				}
			})


			$("#department").append(option2);
			$("#department").select2();
			$("#department").trigger('change');
		} else {
			$("#dept_div").hide();
		}

		$("#bagian").append(option);		

		kategori_form = remark;

		fetchForm();
	}

	function pilih_bagian(elem) {
		var val = $(elem).val();

		$("#bagian").empty();
		option = '<option></option>';
		$.each(bagian, function(index, value){
			if (value.category == 'group' && value.department == val) {
				option += '<option value="'+value.bagian+'">'+value.bagian+'</option>';
			}
		})
		$("#bagian").append(option);
	}



	function save() {
		if(confirm('Anda yakin akan menyimpan form dan mengirim ke Atasan?')) {
			var point_check = [];
			var kondisi = [];
			var keterangan = [];
			
			var point_check_new = [];
			var kondisi_new = [];
			var keterangan_new = [];

			var point_check_sb = [];
			var kondisi_sb = [];
			var keterangan_sb = [];
			var validator = '';

			$('.point_check').each(function(i, obj) {
				point_check.push($(obj).text());

				if($('input[name="kondisi_'+i+'"]:checked').val() == undefined){
					// openErrorGritter('Error', 'Mohon Isi Semua Kolom Kondisi');
					validator = 'Mohon Isi Semua Kolom Kondisi';
					return false;
				}
				kondisi.push($('input[name="kondisi_'+i+'"]:checked').val());

				// if( document.getElementById("foto_"+i).files.length == 0 ){
				// 	validator = 'Harap melengkapi semua Foto';
				// 	return false;
				// }
			});

			$('.keterangan').each(function(i, obj) {
				keterangan.push($(obj).val());
			});

			// ---------- NEW -----------
			$('.check_new').each(function(i, obj) {
				point_check_new.push($(obj).val());

				ids = $(obj).attr('id');
				ids = ids.split('_')[2];

				if($(obj).val() == '') {
					// openErrorGritter('Error', 'Mohon Isi Semua Poin Pengecekan Tambahan');
					validator = 'Mohon Isi Semua Poin Pengecekan Tambahan';
					return false;
				}

				if($('input[name="kondisi_new_'+ids+'"]:checked').val() == undefined){
					// openErrorGritter('Error', 'Mohon Isi Semua Kolom Kondisi');
					validator = 'Mohon Isi Semua Kolom Kondisi Tambahan';
					return false;
				}
				kondisi_new.push($('input[name="kondisi_new_'+ids+'"]:checked').val());

				// if( document.getElementById("foto_new_"+i).files.length == 0 ){
				// 	validator = 'Harap melengkapi semua Foto';
				// 	return false;
				// }
			});

			$('.keterangan_new').each(function(i, obj) {
				keterangan_new.push($(obj).val());
			});

			// ---------- Stand by 24 Jam --------
			$('.check_safe').each(function(i, obj) {
				point_check_sb.push($(obj).val());

				ids = $(obj).attr('id');
				ids = ids.split('_')[2];

				if($(obj).val() == '') {
					// openErrorGritter('Error', 'Mohon Isi Semua Poin Pengecekan Fasilitas StandBy 24 Jam');
					validator = 'Mohon Isi Semua Poin Pengecekan Fasilitas StandBy 24 Jam';
					return false;
				}

				if($('input[name="kondisi_safe_'+ids+'"]:checked').val() == undefined){
					// openErrorGritter('Error', 'Mohon Isi Semua Kolom Kondisi');
					validator = 'Mohon Isi Semua Kolom Kondisi Standby';
					return false;
				}
				kondisi_sb.push($('input[name="kondisi_safe_'+ids+'"]:checked').val());

				// if( document.getElementById("foto_new_"+i).files.length == 0 ){
				// 	validator = 'Harap melengkapi semua Foto';
				// 	return false;
				// }
			});

			$('.keterangan_safe').each(function(i, obj) {
				keterangan_sb.push($(obj).val());
			});

			if (validator != '') {
				openErrorGritter('Error', validator);
				return false;
			}

			$("#loading").show();

			var formData = new FormData();
			formData.append('point_check', JSON.stringify(point_check));
			formData.append('condition', JSON.stringify(kondisi));
			formData.append('note', JSON.stringify(keterangan));

			formData.append('point_check_new', JSON.stringify(point_check_new));
			formData.append('condition_new', JSON.stringify(kondisi_new));
			formData.append('note_new', JSON.stringify(keterangan_new));

			formData.append('point_check_sb', JSON.stringify(point_check_sb));
			formData.append('condition_sb', JSON.stringify(kondisi_sb));
			formData.append('note_sb', JSON.stringify(keterangan_sb));

			formData.append('bagian', $("#bagian").val());
			formData.append('employee_id', $("#pic").val());
			formData.append('kategori', kategori_form);
			$.ajax({
				url: '{{ url("post/safety_check/form") }}',
				type: 'POST',
				data: formData,
				success: function (result, status, xhr) {
					$("#loading").hide();

					// $("#pic").val('').trigger('change');
					// $("#bagian").empty();
					$('input[type="radio"]').prop('checked', false);

					$("#bodyAddForm").val();
					$("#bodyStandByForm").val();

					$('.keterangan').each(function(){
						$(this).val('');
					})

					$("#txtAdd").hide();
					$("#tableAddForm").hide();
					$("#txtStandBy").hide();
					$("#tableStandByForm").hide();

					openSuccessGritter('Success', result.message);

				},
				error: function(result, status, xhr){
					$("#loading").hide();

					openErrorGritter('Error!', result.message);
				},
				cache: false,
				contentType: false,
				processData: false
			});
		}
	}

	function add_photo(id) {
		$("#foto_"+id).append('<tr><td><input type="file" class="upload_foto_'+id+' form-control" accept="image/*" required></td><td><button class="btn btn-xs btn-danger type="button" pull-right" onclick="delete_foto(this)"><i class="fa fa-minus"></i></button></td></tr>');
	}

	function add_photo_new(id) {
		$("#foto_new_"+id).append('<tr><td><input type="file" class="upload_foto_new_'+id+' form-control" accept="image/*" required></td><td><button class="btn btn-xs btn-danger pull-right" type="button" onclick="delete_foto(this)"><i class="fa fa-minus"></i></button></td></tr>');
	}

	function add_photo_safe(id) {
		$("#foto_safe_"+id).append('<tr><td><input type="file" class="upload_foto_safe_'+id+' form-control" accept="image/*" required></td><td><button class="btn btn-xs btn-danger pull-right" type="button" onclick="delete_foto(this)"><i class="fa fa-minus"></i></button></td></tr>');
	}

	function delete_foto(elem) {
		$(elem).closest('tr').remove();
		// $(elem).siblings('input:file').remove();
		// $(elem).remove();
	}

	$("form#main_form").submit(function(e){
		var jml_cek = 0;
		var cek = 0;

		var point_check = [];
		var kondisi = [];
		var keterangan = [];

		var point_check_new = [];
		var kondisi_new = [];
		var keterangan_new = [];

		var point_check_sb = [];
		var kondisi_sb = [];
		var keterangan_sb = [];
		var validator = '';

		$('.point_check').each(function(i, obj) {
			jml_cek += 1;
			point_check.push($(obj).text());

			if($('input[name="kondisi_'+i+'"]:checked').val() == undefined){
					// openErrorGritter('Error', 'Mohon Isi Semua Kolom Kondisi');
				validator = 'Mohon Isi Semua Kolom Kondisi';
				return false;
			}
			kondisi.push($('input[name="kondisi_'+i+'"]:checked').val());
		});

		$('.keterangan').each(function(i, obj) {
			keterangan.push($(obj).val());
		});

		// ---------- NEW -----------
		$('.check_new').each(function(i, obj) {
			jml_cek += 1;
			point_check_new.push($(obj).val());

			ids = $(obj).attr('id');
			ids = ids.split('_')[2];

			if($(obj).val() == '') {
					// openErrorGritter('Error', 'Mohon Isi Semua Poin Pengecekan Tambahan');
				validator = 'Mohon Isi Semua Poin Pengecekan Tambahan';
				return false;
			}

			if($('input[name="kondisi_new_'+ids+'"]:checked').val() == undefined){
					// openErrorGritter('Error', 'Mohon Isi Semua Kolom Kondisi');
				validator = 'Mohon Isi Semua Kolom Kondisi Tambahan';
				return false;
			}
			kondisi_new.push($('input[name="kondisi_new_'+ids+'"]:checked').val());
		});

		$('.keterangan_new').each(function(i, obj) {
			keterangan_new.push($(obj).val());
		});

		// ---------- Stand by 24 Jam --------
		$('.check_safe').each(function(i, obj) {
			jml_cek += 1;
			point_check_sb.push($(obj).val());

			ids = $(obj).attr('id');
			ids = ids.split('_')[2];

			if($(obj).val() == '') {
					// openErrorGritter('Error', 'Mohon Isi Semua Poin Pengecekan Fasilitas StandBy 24 Jam');
				validator = 'Mohon Isi Semua Poin Pengecekan Fasilitas StandBy 24 Jam';
				return false;
			}

			if($('input[name="kondisi_safe_'+ids+'"]:checked').val() == undefined){
					// openErrorGritter('Error', 'Mohon Isi Semua Kolom Kondisi');
				validator = 'Mohon Isi Semua Kolom Kondisi Standby';
				return false;
			}
			kondisi_sb.push($('input[name="kondisi_safe_'+ids+'"]:checked').val());
		});

		$('.keterangan_safe').each(function(i, obj) {
			keterangan_sb.push($(obj).val());
		});

			// ---- FOTO ------

		$.each(point_check, function(index, value){
			if($("#foto_"+index).children().length == 0) {
				validator = 'Mohon Lengkapi Foto Pengecekan';
				openErrorGritter('Error', validator);
				return false;
			}
		})


		$.each(point_check_new, function(index, value){
			if($("#foto_new_"+index).children().length == 0) {
				validator = 'Mohon Lengkapi Foto Pengecekan';
				openErrorGritter('Error', validator);
				return false;
			}
		})


		$.each(point_check_sb, function(index, value){
			if($("#foto_safe_"+index).children().length == 0) {
				validator = 'Mohon Lengkapi Foto Pengecekan';
				openErrorGritter('Error', validator);
				return false;
			}
		})

		if ($("#bagian").val() == '') {
			openErrorGritter('Error', 'Lengkapi Bagian');
			return false;
		}

		if (validator != '') {
			openErrorGritter('Error', validator);
			return false;
		}

		e.preventDefault();
		$.each(point_check, function(index, value){
			cek += 1;
			var formData = new FormData();
			formData.append('point_check', value);
			formData.append('condition', kondisi[index]);
			formData.append('note', keterangan[index]);

			formData.append('bagian', $("#bagian").val());
			formData.append('employee_id', $("#pic").val());

			formData.append('kategori', kategori_form);
			formData.append('total_check', jml_cek);
			formData.append('cek', cek);

			if($("#foto_"+index).children().length == 0) {
				validator = 'Mohon Lengkapi Foto Pengecekan';
				openErrorGritter('Error', validator);
				return false;
			}

			$('.upload_foto_'+index).each(function(i, obj) {
				formData.append('photo['+i+']', $(obj).prop('files')[0]);
			});


			$("#loading").show();


			$.ajax({
				url: '{{ url("post/safety_check/form2") }}',
				type: 'POST',
				enctype: 'multipart/form-data',
				data: formData,
				processData: false,
				contentType: false,
				cache: false,
				success: function (result, status, xhr) {
					if(result.status) {
						if(result.cek == result.cek_all) {
							console.log(cek+' '+jml_cek);
							openSuccessGritter("Success", result.message);
							$("#loading").hide();
							$("#finish").show();
						}
					} else {
						if(result.cek == result.cek_all) {
							$("#loading").hide();
							openErrorGritter("Error", result.message);
						}
					}
				},
				function (xhr, ajaxOptions, thrownError) {

					openErrorGritter(xhr.status, thrownError);
				}
			})
		})

		cek = 1;

		$.each(point_check_new, function(index, value){
			var formData = new FormData();
			formData.append('point_check', value);
			formData.append('condition', kondisi_new[index]);
			formData.append('note', keterangan_new[index]);
			formData.append('cat', 'Additional');

			formData.append('bagian', $("#bagian").val());
			formData.append('employee_id', $("#pic").val());

			formData.append('kategori', kategori_form);
			formData.append('total_check', jml_cek);
			formData.append('cek', cek);

			if($("#foto_new_"+index).children().length == 0) {
				validator = 'Mohon Lengkapi Foto Pengecekan ADD';
				openErrorGritter('Error', validator);
				return false;
			}

			$('.upload_foto_new_'+index).each(function(i, obj) {

				formData.append('photo['+i+']', $(obj).prop('files')[0]);
			});


			$("#loading").show();


			$.ajax({
				url: '{{ url("post/safety_check/form2") }}',
				type: 'POST',
				enctype: 'multipart/form-data',
				data: formData,
				processData: false,
				contentType: false,
				cache: false,
				success: function (result, status, xhr) {
					if(result.status) {
						cek += 1;
						if(result.cek == result.cek_all) {
							console.log(cek+' '+jml_cek);
							openSuccessGritter("Success", result.message);
							$("#loading").hide();
						}
					} else {
						if(result.cek == result.cek_all) {
							$("#loading").hide();
							openErrorGritter("Error", result.message);
						}
					}
				},
				function (xhr, ajaxOptions, thrownError) {

					openErrorGritter(xhr.status, thrownError);
				}
			})

		})

		$.each(point_check_sb, function(index, value){
			cek += 1;
			var formData = new FormData();
			formData.append('point_check', value);
			formData.append('condition', kondisi_sb[index]);
			formData.append('note', keterangan_sb[index]);
			formData.append('cat', 'Standby');

			formData.append('bagian', $("#bagian").val());
			formData.append('employee_id', $("#pic").val());

			formData.append('kategori', kategori_form);
			formData.append('total_check', jml_cek);
			formData.append('cek', cek);

			if($("#foto_safe_"+index).children().length == 0) {
				validator = 'Mohon Lengkapi Foto Pengecekan 24 JAM';
				openErrorGritter('Error', validator);
				return false;
			}

			$('.upload_foto_safe_'+index).each(function(i, obj) {

				formData.append('photo['+i+']', $(obj).prop('files')[0]);
			});


			$("#loading").show();


			$.ajax({
				url: '{{ url("post/safety_check/form2") }}',
				type: 'POST',
				enctype: 'multipart/form-data',
				data: formData,
				processData: false,
				contentType: false,
				cache: false,
				success: function (result, status, xhr) {
					if(result.status) {
						if(result.cek == result.cek_all) {
							console.log(cek+' '+jml_cek);
							openSuccessGritter("Success", result.message);
							$("#loading").hide();
						}
					} else {
						if(result.cek == result.cek_all) {
							$("#loading").hide();
							openErrorGritter("Error", result.message);
						}
					}
				},
				function (xhr, ajaxOptions, thrownError) {

					openErrorGritter(xhr.status, thrownError);
				}
			})

		})

			// formData.append('point_check_new', JSON.stringify(point_check_new));
			// formData.append('condition_new', JSON.stringify(kondisi_new));
			// formData.append('note_new', JSON.stringify(keterangan_new));

			// formData.append('point_check_sb', JSON.stringify(point_check_sb));
			// formData.append('condition_sb', JSON.stringify(kondisi_sb));
			// formData.append('note_sb', JSON.stringify(keterangan_sb));


			// for (var j = 0; j < jml_form; j++) {
			// 	$.each($(".upload_foto_"+j)[0].files, function(i, file) {
			// 		ajaxData.append('photo_'+j+'['+i+']', file);
			// 	});
			// }

			// for (var j = 0; j < jml_form; j++) {
			// 	if($("#foto_"+j).children().length == 0) {
			// 		validator = 'Mohon Lengkapi Foto Pengecekan';
			// 		openErrorGritter('Error', validator);
			// 		return false;
			// 	}

			// 	$('.upload_foto_'+j).each(function(i, obj) {

			// 		formData.append('photo_'+j+'['+i+']', $(obj).prop('files')[0]);
			// 	});
			// }

			// for (var j = 0; j < jml_form_new; j++) {
			// 	if($("#foto_new_"+j).children().length <= 0) {
			// 		validator = 'Mohon Lengkapi Foto Pengecekan NEW';
			// 		openErrorGritter('Error', validator);
			// 		return false;
			// 	}

			// 	$('.upload_foto_new_'+j).each(function(i, obj) {

			// 		formData.append('photo_'+j+'['+i+']', $(obj).prop('files')[0]);
			// 	});
			// }

			// for (var j = 0; j < jml_form_safe; j++) {
			// 	if($("#foto_safe_"+j).children().length <= 0) {
			// 		validator = 'Mohon Lengkapi Foto Pengecekan SAFE';
			// 		openErrorGritter('Error', validator);
			// 		return false;
			// 	}

			// 	$('.upload_foto_safe_'+j).each(function(i, obj) {

			// 		formData.append('photo_'+j+'['+i+']', $(obj).prop('files')[0]);
			// 	});
			// }

	});


function addNew() {
	var body = '';

	body += '<tr>';
	body += '<td><textarea class="form-control check_new" placeholder="Isikan Poin Pengecekan" id="check_new_'+no_add+'"></textarea></td>';
	body += '<td>';
	body += '<label class="radio" style="margin-top: 5px">Sudah <input class="kodisi" type="radio" id="kondisi_new_sudah_'+no_add+'" name="kondisi_new_'+no_add+'" value="Sudah"><span class="checkmark"></span></label>';
	body += '&nbsp;&nbsp;';
	body += '<label class="radio" style="margin-top: 5px">Tidak Ada<input class="kodisi" type="radio" id="kondisi_new_belum_'+no_add+'" name="kondisi_new_'+no_add+'" value="Tidak Ada"><span class="checkmark"></span></label>';
	body += '</td>';
	body += '<td><center><button type="button" class="btn btn-success btn-xs" onclick="add_photo_new(\''+no_add+'\')"><i class="fa fa-plus"></i> Tambah</button><div id="foto_new_'+no_add+'"></div></center></td>';
	body += '<td><textarea class="form-control keterangan_new" id="note_new_'+no_add+'" placeholder="Isikan keterangan"></textarea></td>';
	body += '<td><button class="btn btn-xs btn-danger" onclick="del(this)" id="del_new"><i class="fa fa-minus"></i></button></td>';
	body += '</tr>';

	$("#bodyAddForm").append(body);

	no_add += 1;
	jml_form_new += 1;
}

function addStandBy() {
	var body = '';

	body += '<tr>';
	body += '<td><textarea class="form-control check_safe" placeholder="Isikan Poin Pengecekan" id="check_safe_'+no_safety+'"></textarea></td>';
	body += '<td>';
	body += '<label class="radio" style="margin-top: 5px">Sudah <input class="kodisi" type="radio" id="kondisi_safe_sudah_'+no_safety+'" name="kondisi_safe_'+no_safety+'" value="Sudah"><span class="checkmark"></span></label>';
	body += '&nbsp;&nbsp;';
	body += '<label class="radio" style="margin-top: 5px">Tidak Ada<input class="kodisi" type="radio" id="kondisi_safe_belum_'+no_safety+'" name="kondisi_safe_'+no_safety+'" value="Tidak Ada"><span class="checkmark"></span></label>';
	body += '</td>';
	body += '<td><center><button type="button" class="btn btn-success btn-xs" onclick="add_photo_safe(\''+no_safety+'\')"><i class="fa fa-plus"></i> Tambah</button><div id="foto_safe_'+no_safety+'"></div></center></td>';
	body += '<td><textarea class="form-control keterangan_safe" id="note_safe_'+no_safety+'" placeholder="Isikan keterangan"></textarea></td>';
	body += '<td><button class="btn btn-xs btn-danger" onclick="del(this)" id="del_safe"><i class="fa fa-minus"></i></button></td>';
	body += '</tr>';

	$("#bodyStandByForm").append(body);

	no_safety += 1;
	jml_form_safe += 1;
}

function del(elem) {
	var ids = $(elem).attr('id');
	if (ids == 'del_new') {
		jml_form_new -= 1;
	} else if (ids == 'del_safe') {
		jml_form_safe -= 1;
	}

	$( elem ).closest( "tr" ).remove();
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
		time: '4000'
	});
	audio_ok.play();
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
	audio_error.play();
}

</script>
@endsection