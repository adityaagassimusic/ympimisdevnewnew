@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(100, 100, 100);
		vertical-align: middle;
	}
	input[type=checkbox] {
		transform: scale(1.7);
		cursor: pointer;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
{{-- 	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1> --}}
</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="form-group">
					<label style="padding-top: 0; color: white;" for="" class="col-xs-12 control-label">Karyawan<span class="text-red">*</span> :</label>
					<div class="col-xs-5">
						<select class="form-control select2" style="width: 100%; text-align: center;" type="text" id="createEmployee" data-placeholder="Pilih Karyawan" onchange="checkData(this.value)">
							<option value=""></option>
							@foreach($employee_syncs as $employee_sync)
							<option value="{{ $employee_sync->employee_id }}">{{ $employee_sync->employee_id }} - {{ $employee_sync->name }} ({{ $employee_sync->department_shortname }})</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="row">
				<div class="form-group">

					<?php if($category == "roda_2") { ?>

					<!-- <label style="padding-top: 0; color: white;" for="" class="col-xs-3 control-label">No. Polisi Kendaraan<span class="text-red">*</span> :</label>
					<label style="padding-top: 0; color: white;" for="" class="col-xs-9 control-label">No. Polisi Terdaftar :</label> -->

					<label style="padding-top: 0; color: white;" for="" class="col-xs-12 control-label">No. Polisi Kendaraan<span class="text-red">*</span> :</label>
					
					<?php } else if($category == "roda_4") { ?>

					<label style="padding-top: 0; color: white;" for="" class="col-xs-12 control-label">No. Polisi Kendaraan<span class="text-red">*</span> :</label>
					<?php } ?>

					<?php if($category == "roda_4") { ?>

						<div class="col-xs-3">
							<input class="form-control" placeholder="Masukkan Nomor Polisi Kendaraan" type="text" id="createNumber">
						</div>

					<?php } else if($category == "roda_2") { ?>
						<div class="col-xs-3">
							<select class="form-control select3" style="width: 100%; text-align: center;" type="text" id="createNumber" data-placeholder="Pilih Nopol"></select>
					
						</div>
					<?php } ?>
					<!-- <?php if($category == "roda_2") { ?>

					<div class="col-xs-2">
						<input class="form-control" placeholder="Nomor Polisi Kendaraan Terdaftar" type="text" id="createNumberRegistration" readonly>
					</div>

					<?php } ?> -->
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 15px;">
			<div class="box box-solid" style="margin-bottom: 0;">
				<div class="box-body">
					@foreach($vehicle_inspections as $vehicle_inspection)
					<div class="col-xs-3" style="padding-bottom: 10px; padding-top: 10px;">
						<input type="checkbox" value="{{ $vehicle_inspection['description'] }}" id="{{ $vehicle_inspection['code'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $vehicle_inspection['description'] }}
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="row">
				<div class="form-group">
					<label style="padding-top: 0; color: white;" for="" class="col-xs-12 control-label">Catatan<span class="text-red">*</span> :</label>
					<div class="col-xs-12">
						<textarea rows="3" id="createRemark" style="width: 100%;"></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="row">
				<div class="form-group">
					<label style="padding-top: 0; color: white;" for="" class="col-xs-12 control-label">Bukti Foto<span class="text-red"></span> :</label>
					<div class="col-xs-12">
						<div class="box box-solid" style="margin-bottom: 0;">
							<div class="box-body" id="uploadImage">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="row">
				<div class="col-xs-4">
					<button class="btn btn-danger" style="font-weight: bold; font-size: 2vw; width: 100%;" onclick="resetAll();">BATAL</button>
				</div>
				<div class="col-xs-8">
					<button class="btn btn-success" style="font-weight: bold; font-size: 2vw; width: 100%;" onclick="inputInspection();">SIMPAN</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<table id="tableInspection" class="table table-bordered table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 1%; text-align: center;">Kategori</th>
						<th style="width: 1%; text-align: center;">Departemen</th>
						<th style="width: 10%; text-align: left;">Karyawan</th>
						<th style="width: 5%; text-align: center;">Jumlah Pelanggaran</th>
						<th style="width: 10%; text-align: left;">Catatan</th>
						<th style="width: 5%; text-align: center;">Foto</th>
						<th style="width: 5%; text-align: right;">Waktu Inspeksi</th>
					</tr>
				</thead>
				<tbody style="background-color: #fcf8e3;" id="tableInspectionBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		resetAll();
		$('.select2').select2({
			allowClear:true,
			dropdownAutoWidth : true
		});
		$('.select3').select2({
			allowClear:true,
			dropdownAutoWidth : true,
			tags:true
		});
		fetchTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var vehicle_inspections = <?php echo json_encode($vehicle_inspections); ?>;
    var employee_vehicle = <?php echo json_encode($employee_vehicle); ?>;
	var category = "<?php echo $category; ?>";

	function fetchTable(){
		$('#loading').show();
		var data = {

		}
		$.get('{{ url("fetch/standardization/form") }}', data, function(result, status, xhr){
			if(result.status){
				var tableInspectionBody = "";
				$('#tableInspectionBody').html("");
				var url = "{{ url("files/vehicle_inspection") }}";

				$.each(result.vehicle_inspections, function(keyt, value){
					tableInspectionBody += '<tr>';
					tableInspectionBody += '<td style="text-align: center;">'+value.category+'</td>';
					tableInspectionBody += '<td style="text-align: center;">'+value.department_shortname+'</td>';
					tableInspectionBody += '<td style="text-align: left;">'+value.employee_id+'<br>'+value.employee_name+'</td>';
					tableInspectionBody += '<td style="text-align: center; font-weight: bold; font-size: 2vw; color: red;">'+value.jumlah+'</td>';
					tableInspectionBody += '<td style="text-align: left;">'+value.remark+'</td>';
					if(value.upload_image){
						tableInspectionBody += '<td style="text-align: center;"><img style="height: 100px;" src="'+url+'/'+value.upload_image+'"></td>';
					}
					else{
						tableInspectionBody += '<td style="text-align: center;">-</td>';						
					}
					tableInspectionBody += '<td style="text-align: right;">'+value.created_at+'</td>';
					tableInspectionBody += '</tr>';
				});

				$('#tableInspectionBody').append(tableInspectionBody);
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', result.message);
				return false;				
			}
		});
	}

	function inputInspection(){
		if(confirm("Apakah anda yakin akan meyimpan temuan pemeriksaan ini?")){
			$('#loading').show();
			var employee_id = $('#createEmployee').val();
			var vehicle_number = $('#createNumber').val();
			var inspections = [];
			var remark = $('#createRemark').val();

			for (var i = 0; i < vehicle_inspections.length; i++) {
				if($('#'+vehicle_inspections[i]['code']).prop('checked') == true){
					inspections.push($('#'+vehicle_inspections[i]['code']).val());
				}
			}

			if(inspections.length <= 0 || employee_id == "" || vehicle_number == ""){
				audio_error.play();
				openErrorGritter('Error!', 'Semua data harus diisi terlebih dahulu.');
				$('#loading').hide();
				return false;
			}

			var formData = new FormData();
			formData.append('category', category);
			formData.append('employee_id', employee_id);
			formData.append('vehicle_number', vehicle_number);
			formData.append('remark', remark);
			formData.append('inspections', inspections);

			formData.append('attachment', $('#createImage').prop('files')[0]);
			var file = $('#createImage').val().replace(/C:\\fakepath\\/i, '').split(".");
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('input/standardization/form') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data){
					if(data.status){
						var tableInspectionBody = "";
						var url = "{{ url("files/vehicle_inspection") }}";

						tableInspectionBody += '<tr>';
						tableInspectionBody += '<td style="text-align: center;">'+data.category+'</td>';
						tableInspectionBody += '<td style="text-align: center;">'+data.employee_sync.department_shortname+'</td>';
						tableInspectionBody += '<td style="text-align: left;">'+data.employee_sync.employee_id+'<br>'+data.employee_sync.name+'</td>';
						tableInspectionBody += '<td style="text-align: center; font-weight: bold; font-size: 2vw; color: red;">'+data.jumlah+'</td>';
						tableInspectionBody += '<td style="text-align: left;">'+data.remark+'</td>';
						if(data.upload_image){
							tableInspectionBody += '<td style="text-align: center;"><img style="height: 100px;" src="'+url+'/'+data.upload_iamge+'"></td>';
						}
						else{
							tableInspectionBody += '<td style="text-align: center;">-</td>';							
						}
						tableInspectionBody += '<td style="text-align: right;">'+data.created_at+'</td>';
						tableInspectionBody += '</tr>';

						$('#tableInspectionBody').append(tableInspectionBody);

						resetAll();
						audio_ok.play();
						openSuccessGritter('Success!', data.message);
						$('#loading').hide();
					}
					else{
						audio_error.play();
						openErrorGritter('Error!', data.message);
						$('#loading').hide();
						return false;
					}
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function checkData(value){
        var stat = 0;
        // $.each(employee_vehicle, function(key2, value2) {
        //     if (value == value2.employee_id) {
        //     	if (value2.nopol_2 != null) {
        //     		$('#createNumberRegistration').val(value2.nopol+ ' / '+value2.nopol_2);
        //     	}else{
        //     		$('#createNumberRegistration').val(value2.nopol);
        //     	}
                
        //         stat = 1;
        //     }
        // });

        // if (stat == 0) {
        //     $('#createNumberRegistration').val("Tidak Terdaftar");
        // }

        var stat = 0;

        list = "";
        list += "<option></option> ";

        $.each(employee_vehicle, function(key2, value2) {
            if (value == value2.employee_id) {
        		if (value2.nopol_2 != null) {
          			list += "<option value='"+value2.nopol+"'>"+value2.nopol+"</option>";
          			list += "<option value='"+value2.nopol_2+"'>"+value2.nopol_2+"</option>";
            	}else{
          			list += "<option value='"+value2.nopol+"'>"+value2.nopol+"</option>";
            	}
                
                stat = 1;
            }
        });

        if (stat == 0) {
            $('#createNumber').val("");
        }
        $('#createNumber').html(list);
	}

	function resetAll(){
		$('#createEmployee').prop('selectedIndex', 0).change();
		$('#createNumber').val('');
		$('#createRemark').val('');

		for (var i = 0; i < vehicle_inspections.length; i++) {
			$('#'+vehicle_inspections[i]['code']).prop('checked', false);
		}

		$('#uploadImage').html("");
		var uploadImage = "";

		uploadImage += '<center>';
		uploadImage += '<input accept="image/*" capture="environment" type="file" class="file" onchange="readURL(this);" style="display:none" onchange="readURL(this);" id="createImage">';
		uploadImage += '<button class="btn btn-primary btn-lg" value="Photo" onclick="buttonImage(this)" style="font-size: 1.5vw;"><i class="fa fa-file-image-o"></i></button>';
		uploadImage += '<img src="" onclick="buttonImage(this)" style="display: none; height: 200px;" alt="your image"/>';
		uploadImage += '</center>';

		$('#uploadImage').append(uploadImage);
	}
	function buttonImage(elem) {
		$(elem).closest("div").find("input").click();
	}

	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				var img = $(input).closest("div").find("img");
				$(img).show();
				$(img).attr('src', e.target.result);
			};

			reader.readAsDataURL(input.files[0]);

		}

		$(input).closest("div").find("button").hide();
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

</script>
@endsection

