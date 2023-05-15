@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">	
	.head {
		text-align: center;
		font-weight: bold;
	}
	.label-pad{
		padding-top: 0.25%;
	}
	.header-tab{
		font-size: 20px;
		font-weight: bold;
	}
	.isi{
		background-color: #f5f5f5;
		color: black;
		padding: 10px;
	}

	#signArea{
		width: 504px;
		margin: 15px auto;
	}
	.sign-container {
		width: 90%;
		margin: auto;
	}
	.sign-preview {
		width: 150px;
		height: 50px;
		border: solid 1px #CFCFCF;
		margin: 10px 5px;
	}
	.tag-ingo {
		font-family: cursive;
		font-size: 12px;
		text-align: left;
		font-style: oblique;
	}
	.center-text {
		text-align: center;
	}
	.centera{
      text-align: center;
      vertical-align: middle !important;
    }
    .square {
      height: 5px;
      width: 5px;
      border: 1px solid black;
      background-color: transparent;
    }
	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
	}

	#loading { display: none; }
	

</style>
@stop
@section('header')
<h1>
	<span class="text-yellow">
		{{ $title }}
	</span>
	<small>
		<span style="color: #FFD700;"> {{ $title_jp }}</span>
	</small>
</h1>
<br>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%; text-align: center;">
			<span style="font-size: 5vw; "><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<h2 class="head"><?php echo "EDIT DATA CALON KARYAWAN PT. YMPI " . date("Y");?></h2>

					<form id="form_update" method="post" action="upload" enctype="multipart/form-data">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />

						<a href="{{ url('calon/karyawan') }}" class="btn pull-right" style="margin-left: 5px; width: 15%; background-color: #3498db; color: white;"><i class="fa fa-list"></i> Insert Calon Karyawan</a>

						<div class="form-group row" style="margin-top: 3%; padding-left: 30px;">
							<label class="col-xs-4 label-pad" style="font-weight: bold; text-align: left;"><span class="text-red">*) : Harus Diisi</span></label>
						</div>

						<div class="form-group row" style="margin-top: 3%;">
							<label class="col-xs-6 col-xs-offset-1 header-tab">A. Data Pribadi</label>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-2 label-pad">Nomor KTP<span class="text-red">*</span></label>
							<div class="col-xs-4" align="left">
								<input type="number" class="form-control" id="nik" name="nik" placeholder="Nomor KTP" onchange ="checkEmp(this.value)" required>
							</div>
						</div>

						<div id="data_pribadi">
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Nama Lengkap<span class="text-red">*</span></label>
								<div class="col-xs-4" align="left">
									<input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap (Sesuai KTP)" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Jenis Kelamin<span class="text-red">*</span></label>
								<div class="col-xs-2" align="left">
									<select class="form-control select5" data-placeholder="Jenis Kelamin" id="gender" name="gender" style="width: 100%" required>
										<option style="color:grey;" value="">L/P</option>
										<option value="Male">L</option>
										<option value="Female">P</option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Nomor NPWP</label>
								<div class="col-xs-4" align="left">
									<input type="text" class="form-control" id="npwp" name="npwp" placeholder="Nomor NPWP">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Tempat Lahir<span class="text-red">*</span></label>
								<div class="col-xs-4" align="left">
									<input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Tempat Lahir" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Tanggal Lahir<span class="text-red">*</span></label>
								<div class="col-xs-3" align="left">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir" name="tanggal_lahir" placeholder="Pilih Tanggal Lahir" required>
									</div>
								</div>
							</div>					

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Agama<span class="text-red">*</span></label>
								<div class="col-xs-3" align="left">
									<select class="form-control select2" data-placeholder="Pilih Status" id="agama" name="agama" style="width: 100%" required>
										<option style="color:grey;" value="">Pilih Agama</option>
										<option value="ISLAM">ISLAM</option>
										<option value="PROTESTAN">PROTESTAN</option>
										<option value="KATHOLIK">KATHOLIK</option>
										<option value="HINDU">HINDU</option>
										<option value="BUDHA">BUDHA</option>
										<option value="KONGHUCU">KONGHUCU</option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Status Perkawinan<span class="text-red">*</span></label>
								<div class="col-xs-3" align="left">
									<select class="form-control select2" data-placeholder="Pilih Status" id="status_perkawinan" name="status_perkawinan" style="width: 100%" required>
										<option style="color:grey;" value="">Pilih Status</option>
										<option value="Single">LAJANG</option>
										<option value="Married">KAWIN</option>
										<option value="Divoread">CERAI</option>

									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Alamat Asal (Sesuai KTP)<span class="text-red">*</span></label>
								<div class="col-xs-6" align="left">
									<input type="text" class="form-control" id="alamat_asal" name="alamat_asal" placeholder="Alamat" required>
								</div>
								<div class="col-xs-1">
									<input type="number" class="form-control" id="rt_asal" name="rt_asal" placeholder="RT" required>
								</div>
								<div class="col-xs-1" align="left">
									<input type="number" class="form-control" id="rw_asal" name="rw_asal" placeholder="RW" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad"></label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="kelurahan_asal" name="kelurahan_asal" placeholder="Kelurahan" required>
								</div>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="kecamatan_asal" name="kecamatan_asal" placeholder="Kecamatan" required>
								</div>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="kota_asal" name="kota_asal" placeholder="Kota / Kabupaten" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Alamat Domisili (Sekarang)<span class="text-red">*</span></label>
								<div class="col-xs-6" align="left">
									<input type="text" class="form-control" id="alamat_domisili" name="alamat_domisili" placeholder="Alamat" required>
								</div>
								<div class="col-xs-1">
									<input type="number" class="form-control" id="rt_domisili" name="rt_domisili" placeholder="RT" required>
								</div>
								<div class="col-xs-1" align="left">
									<input type="number" class="form-control" id="rw_domisili" name="rw_domisili" placeholder="RW" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad"></label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="kelurahan_domisili" name="kelurahan_domisili" placeholder="Kelurahan" required>
								</div>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="kecamatan_domisili" name="kecamatan_domisili" placeholder="Kecamatan" required>
								</div>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="kota_domisili" name="kota_domisili" placeholder="Kota / Kabupaten" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Telepon Rumah</label>
								<div class="col-xs-3" align="left">
									<input type="number" class="form-control" id="telepon_rumah" name="telepon_rumah" placeholder="Telepon Rumah">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Hand Phone<span class="text-red">*</span></label>
								<div class="col-xs-3" align="left">
									<input type="number" class="form-control" id="hp" name="hp" placeholder="Nomor Hand Phone (Wajib Diisi)" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Email<span class="text-red">*</span></label>
								<div class="col-xs-3" align="left">
									<input type="email" class="form-control" id="email" name="email" placeholder="Alamat Email (Wajib Diisi)" required>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Nomor BPJSKES</label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="bpjskes" name="bpjskes" placeholder="Nomor BPJS Kesehatan">
								</div>
							</div>


							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">FASKES</label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="faskes" name="faskes" placeholder="Fasilitas Kesehatan">
								</div>
							</div>


							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Nomor BPJSTK</label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="bpjstk" name="bpjstk" placeholder="Nomor BPJS Ketenagakerjaan">
								</div>
							</div>
						</div>

						<div id="susunan_keluarga">
							<div class="form-group row" style="margin-top: 3%;">
								<label class="col-xs-6 col-xs-offset-1 header-tab">B. Susunan Anggota Keluarga Anda (Termasuk diri anda sendiri)</label>
							</div>
							{{-- Ayah --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Ayah<span class="text-red">*</span></label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_ayah" name="nama_ayah" placeholder="Nama Ayah" required>
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_ayah" name="kelamin_ayah" style="width: 100%" required>
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_ayah" name="tempat_lahir_ayah" placeholder="Tempat Lahir Ayah">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_ayah" name="tanggal_lahir_ayah" placeholder="Tanggal Lahir Ayah">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah" placeholder="Pekerjaan Ayah">
								</div>
							</div>

							{{-- Ibu --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Ibu<span class="text-red">*</span></label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_ibu" name="nama_ibu" placeholder="Nama Ibu" required>
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_ibu" name="kelamin_ibu" style="width: 100%" required>
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_ibu" name="tempat_lahir_ibu" placeholder="Tempat Lahir Ibu">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_ibu" name="tanggal_lahir_ibu" placeholder="Tanggal Lahir Ibu" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu" placeholder="Pekerjaan Ibu">
								</div>
							</div>

							{{-- Saudara1 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 1</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara1" name="nama_saudara1" placeholder="Nama Saudara 1">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara1" name="kelamin_saudara1" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara1" name="tempat_lahir_saudara1" placeholder="Tempat Lahir Saudara 1">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara1" name="tanggal_lahir_saudara1" placeholder="Tanggal Lahir Saudara 1">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara1" name="pekerjaan_saudara1" placeholder="Pekerjaan Saudara 1">
								</div>
							</div>

							{{-- Saudara2 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 2</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara2" name="nama_saudara2" placeholder="Nama Saudara 2">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara2" name="kelamin_saudara2" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara2" name="tempat_lahir_saudara2" placeholder="Tempat Lahir Saudara 2">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara2" name="tanggal_lahir_saudara2" placeholder="Tanggal Lahir Saudara 2">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara2" name="pekerjaan_saudara2" placeholder="Pekerjaan Saudara 2">
								</div>
							</div>

							{{-- Saudara3 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 3</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara3" name="nama_saudara3" placeholder="Nama Saudara 3">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara3" name="kelamin_saudara3" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara3" name="tempat_lahir_saudara3" placeholder="Tempat Lahir Saudara 3">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara3" name="tanggal_lahir_saudara3" placeholder="Tanggal Lahir Saudara 3">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara3" name="pekerjaan_saudara3" placeholder="Pekerjaan Saudara 3">
								</div>
							</div>

							{{-- Saudara4 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 4</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara4" name="nama_saudara4" placeholder="Nama Saudara 4">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara4" name="kelamin_saudara4" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara4" name="tempat_lahir_saudara4" placeholder="Tempat Lahir Saudara 4">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara4" name="tanggal_lahir_saudara4" placeholder="Tanggal Lahir Saudara 4">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara4" name="pekerjaan_saudara4" placeholder="Pekerjaan Saudara 4">
								</div>
							</div>

							{{-- Saudara5 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 5</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara5" name="nama_saudara5" placeholder="Nama Saudara 5">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara5" name="kelamin_saudara5" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara5" name="tempat_lahir_saudara5" placeholder="Tempat Lahir Saudara 5">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara5" name="tanggal_lahir_saudara5" placeholder="Tanggal Lahir Saudara 5">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara5" name="pekerjaan_saudara5" placeholder="Pekerjaan Saudara 5">
								</div>
							</div>

							{{-- Saudara6 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 6</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara6" name="nama_saudara6" placeholder="Nama Saudara 6">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara6" name="kelamin_saudara6" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara6" name="tempat_lahir_saudara6" placeholder="Tempat Lahir Saudara 6">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara6" name="tanggal_lahir_saudara6" placeholder="Tanggal Lahir Saudara 6">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara6" name="pekerjaan_saudara6" placeholder="Pekerjaan Saudara 6">
								</div>
							</div>

							{{-- Saudara7 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 7</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara7" name="nama_saudara7" placeholder="Nama Saudara 7">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara7" name="kelamin_saudara7" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara7" name="tempat_lahir_saudara7" placeholder="Tempat Lahir Saudara 7">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara7" name="tanggal_lahir_saudara7" placeholder="Tanggal Lahir Saudara 7">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara7" name="pekerjaan_saudara7" placeholder="Pekerjaan Saudara 7">
								</div>
							</div>

							{{-- Saudara8 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 8</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara8" name="nama_saudara8" placeholder="Nama Saudara 8">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara8" name="kelamin_saudara8" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara8" name="tempat_lahir_saudara8" placeholder="Tempat Lahir Saudara 8">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara8" name="tanggal_lahir_saudara8" placeholder="Tanggal Lahir Saudara 8">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara8" name="pekerjaan_saudara8" placeholder="Pekerjaan Saudara 8">
								</div>
							</div>

							{{-- Saudara9 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 9</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara9" name="nama_saudara9" placeholder="Nama Saudara 9">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara9" name="kelamin_saudara9" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara9" name="tempat_lahir_saudara9" placeholder="Tempat Lahir Saudara 9">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara9" name="tanggal_lahir_saudara9" placeholder="Tanggal Lahir Saudara 9">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara9" name="pekerjaan_saudara9" placeholder="Pekerjaan Saudara 9">
								</div>
							</div>

							{{-- Saudara10 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 10</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara10" name="nama_saudara10" placeholder="Nama Saudara 10">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara10" name="kelamin_saudara10" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara10" name="tempat_lahir_saudara10" placeholder="Tempat Lahir Saudara 10">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara10" name="tanggal_lahir_saudara10" placeholder="Tanggal Lahir Saudara 10">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara10" name="pekerjaan_saudara10" placeholder="Pekerjaan Saudara 10">
								</div>
							</div>

							{{-- Saudara11 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 11</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara11" name="nama_saudara11" placeholder="Nama Saudara 11">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara11" name="kelamin_saudara11" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara11" name="tempat_lahir_saudara11" placeholder="Tempat Lahir Saudara 11">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara11" name="tanggal_lahir_saudara11" placeholder="Tanggal Lahir Saudara 11">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara11" name="pekerjaan_saudara11" placeholder="Pekerjaan Saudara 11">
								</div>
							</div>

							{{-- Saudara12 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Saudara 12</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_saudara12" name="nama_saudara12" placeholder="Nama Saudara 12">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_saudara12" name="kelamin_saudara12" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_saudara12" name="tempat_lahir_saudara12" placeholder="Tempat Lahir Saudara 12">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_saudara12" name="tanggal_lahir_saudara12" placeholder="Tanggal Lahir Saudara 12">
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_saudara12" name="pekerjaan_saudara12" placeholder="Pekerjaan Saudara 12">
								</div>
							</div>
						</div>

						<div id="susunan_keluarga_inti">
							<div class="form-group row" style="margin-top: 3%;">
								<label class="col-xs-6 col-xs-offset-1 header-tab">C. Susunan Anggota Keluarga Inti</label>
							</div>
							{{-- Suami/Istri --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Suami/Istri</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_pasangan" name="nama_pasangan" placeholder="Nama Pasangan">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_pasangan" name="kelamin_pasangan" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_pasangan" name="tempat_lahir_pasangan" placeholder="Tempat Lahir Pasangan">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_pasangan" name="tanggal_lahir_pasangan" placeholder="Tanggal Lahir Pasangan" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_pasangan" name="pekerjaan_pasangan" placeholder="Pekerjaan Pasangan">
								</div>
							</div>


							{{-- Anak1 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 1</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak1" name="nama_anak1" placeholder="Nama Anak 1">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak1" name="kelamin_anak1" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak1" name="tempat_lahir_anak1" placeholder="Tempat Lahir Anak 1">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak1" name="tanggal_lahir_anak1" placeholder="Tanggal Lahir Anak 1" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak1" name="pekerjaan_anak1" placeholder="Pekerjaan Anak 1">
								</div>
							</div>

							{{-- Anak2 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 2</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak2" name="nama_anak2" placeholder="Nama Anak 2">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak2" name="kelamin_anak2" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak2" name="tempat_lahir_anak2" placeholder="Tempat Lahir Anak 2">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak2" name="tanggal_lahir_anak2" placeholder="Tanggal Lahir Anak 2" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak2" name="pekerjaan_anak2" placeholder="Pekerjaan Anak 2">
								</div>
							</div>

							{{-- Anak3 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 3</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak3" name="nama_anak3" placeholder="Nama Anak 3">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak3" name="kelamin_anak3" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak3" name="tempat_lahir_anak3" placeholder="Tempat Lahir Anak 3">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak3" name="tanggal_lahir_anak3" placeholder="Tanggal Lahir Anak 3" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak3" name="pekerjaan_anak3" placeholder="Pekerjaan Anak 3">
								</div>
							</div>

							{{-- Anak4 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 4</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak4" name="nama_anak4" placeholder="Nama Anak 4">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak4" name="kelamin_anak4" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak4" name="tempat_lahir_anak4" placeholder="Tempat Lahir Anak 4">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak4" name="tanggal_lahir_anak4" placeholder="Tanggal Lahir Anak 4" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak4" name="pekerjaan_anak4" placeholder="Pekerjaan Anak 4">
								</div>
							</div>

							{{-- Anak5 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 5</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak5" name="nama_anak5" placeholder="Nama Anak 5">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak5" name="kelamin_anak5" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak5" name="tempat_lahir_anak5" placeholder="Tempat Lahir Anak 5">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak5" name="tanggal_lahir_anak5" placeholder="Tanggal Lahir Anak 5" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak5" name="pekerjaan_anak5" placeholder="Pekerjaan Anak 5">
								</div>
							</div>

							{{-- Anak6 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 6</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak6" name="nama_anak6" placeholder="Nama Anak 6">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak6" name="kelamin_anak6" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak6" name="tempat_lahir_anak6" placeholder="Tempat Lahir Anak 6">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak6" name="tanggal_lahir_anak6" placeholder="Tanggal Lahir Anak 6" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak6" name="pekerjaan_anak6" placeholder="Pekerjaan Anak 6">
								</div>
							</div>

							{{-- Anak7 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">Anak 7</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="nama_anak7" name="nama_anak7" placeholder="Nama Anak 7">
								</div>
								<div class="col-xs-1" align="left" style="padding-left: 0px;">
									<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak7" name="kelamin_anak7" style="width: 100%">
										<option style="color:grey;" value="">L/P</option>
										<option value="L">L</option>
										<option value="P">P</option>
									</select>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="tempat_lahir_anak7" name="tempat_lahir_anak7" placeholder="Tempat Lahir Anak 7">
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tanggal_lahir_anak7" name="tanggal_lahir_anak7" placeholder="Tanggal Lahir Anak 7" >
									</div>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_anak7" name="pekerjaan_anak7" placeholder="Pekerjaan Anak 7">
								</div>
							</div>
						</div>


						<div id="pendidikan_formal">
							<div class="form-group row" style="margin-top: 3%;">
								<label class="col-xs-6 col-xs-offset-1 header-tab">D. Pendidikan Formal</label>
							</div>
							{{-- SD --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">SD</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="sd" name="sd" placeholder="Nama Lembaga Pendidikan">
								</div>

								<div class="col-xs-2" align="left">

								</div>

								<div class="col-xs-2" align="left">
									<input type="number" class="form-control yearpicker" id="sd_masuk" name="sd_masuk" placeholder="Tahun Masuk">
								</div>

								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="number" class="form-control yearpicker" id="sd_lulus" name="sd_lulus" placeholder="Tahun Lulus">
								</div>
							</div>

							{{-- SLTP --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">SLTP</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="smp" name="smp" placeholder="Nama Lembaga Pendidikan">
								</div>

								<div class="col-xs-2" align="left">

								</div>

								<div class="col-xs-2" align="left">
									<input type="number" class="form-control yearpicker" id="smp_masuk" name="smp_masuk" placeholder="Tahun Masuk">
								</div>

								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="number" class="form-control yearpicker" id="smp_lulus" name="smp_lulus" placeholder="Tahun Lulus">
								</div>
							</div>

							{{-- SLTA --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">SLTA</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="sma" name="sma" placeholder="Nama Lembaga Pendidikan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="sma_jurusan" name="sma_jurusan" placeholder="Jurusan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="number" class="form-control yearpicker" id="sma_masuk" name="sma_masuk" placeholder="Tahun Masuk">
								</div>

								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="number" class="form-control yearpicker" id="sma_lulus" name="sma_lulus" placeholder="Tahun Lulus">
								</div>
							</div>

							{{-- S1 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">S1</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="s1" name="s1" placeholder="Nama Lembaga Pendidikan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="s1_jurusan" name="s1_jurusan" placeholder="Jurusan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="number" class="form-control yearpicker" id="s1_masuk" name="s1_masuk" placeholder="Tahun Masuk">
								</div>

								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="number" class="form-control yearpicker" id="s1_lulus" name="s1_lulus" placeholder="Tahun Lulus">
								</div>
							</div>

							{{-- S2 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">S2</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="s2" name="s2" placeholder="Nama Lembaga Pendidikan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="s2_jurusan" name="s2_jurusan" placeholder="Jurusan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="number" class="form-control yearpicker" id="s2_masuk" name="s2_masuk" placeholder="Tahun Masuk">
								</div>

								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="number" class="form-control yearpicker" id="s2_lulus" name="s2_lulus" placeholder="Tahun Lulus">
								</div>
							</div>

							{{-- S3 --}}
							<div class="form-group row" align="right">
								<label class="col-xs-2 label-pad">S3</label>
								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="s3" name="s3" placeholder="Nama Lembaga Pendidikan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="text" class="form-control" id="s3_jurusan" name="s3_jurusan" placeholder="Jurusan">
								</div>

								<div class="col-xs-2" align="left">
									<input type="number" class="form-control yearpicker" id="s3_masuk" name="s3_masuk" placeholder="Tahun Masuk">
								</div>

								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="number" class="form-control yearpicker" id="s3_lulus" name="s3_lulus" placeholder="Tahun Lulus">
								</div>
							</div>
						</div>


						
						<div id="emergency">
							<div class="form-group row" style="margin-top: 3%;">
								<label class="col-xs-9 col-xs-offset-1 header-tab">E. Kondisi Darurat Yang Bisa Dihubungi (Wajib Diisi)</label>
								<label class="col-xs-9 col-xs-offset-1 header-tab">&nbsp;&nbsp;&nbsp;&nbsp; Sebutkan nama-nama orang yang dapat dihubungi ketika ada kondisi emergency</label>
							</div>

							{{-- Darurat1 --}}
							<div class="form-group row" align="right">
								<div class="col-xs-2 col-xs-offset-2" align="left">
									<input type="text" class="form-control" id="nama_darurat1" name="nama_darurat1" placeholder="Nama" required>
								</div>
								<div class="col-xs-2" align="left">
									<input type="number" class="form-control" id="telp_darurat1" name="telp_darurat1" placeholder="Nomor Telepon" required>
								</div>						
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_darurat1" name="pekerjaan_darurat1" placeholder="Pekerjaan" required>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<!-- <input type="text" class="form-control" id="hubungan_darurat1" name="hubungan_darurat1"  placeholder="Hubungan" required> -->
									<select id="hubungan_darurat1" name="hubungan_darurat1" class="form-control select2" data-placeholder="Hubungan Keluarga" style="width: 100%; font-size: 20px;" required>
										<option></option>
										@foreach($darurats as $darurat)
										<option value="{{ $darurat }}">{{ $darurat }}</option>
										@endforeach
									</select>
								</div>
							</div>

							{{-- Darurat2 --}}
							<div class="form-group row" align="right">
								<div class="col-xs-2 col-xs-offset-2" align="left">
									<input type="text" class="form-control" id="nama_darurat2" name="nama_darurat2" placeholder="Nama" required>
								</div>
								<div class="col-xs-2" align="left">
									<input type="number" class="form-control" id="telp_darurat2" name="telp_darurat2" placeholder="Nomor Telepon" required>
								</div>						
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_darurat2" name="pekerjaan_darurat2" placeholder="Pekerjaan" required>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<!-- <input type="text" class="form-control" id="hubungan_darurat2" name="hubungan_darurat2" placeholder="Hubungan" required> -->
									<select id="hubungan_darurat2" name="hubungan_darurat2" class="form-control select2" data-placeholder="Hubungan Keluarga" style="width: 100%; font-size: 20px;" required>
										<option></option>
										@foreach($darurats as $darurat)
										<option value="{{ $darurat }}">{{ $darurat }}</option>
										@endforeach
									</select>
								</div>
							</div>

							{{-- Darurat3 --}}
							<div class="form-group row" align="right">
								<div class="col-xs-2 col-xs-offset-2" align="left">
									<input type="text" class="form-control" id="nama_darurat3" name="nama_darurat3" placeholder="Nama" required>
								</div>
								<div class="col-xs-2" align="left">
									<input type="number" class="form-control" id="telp_darurat3" name="telp_darurat3" placeholder="Nomor Telepon" required>
								</div>						
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<input type="text" class="form-control" id="pekerjaan_darurat3" name="pekerjaan_darurat3" placeholder="Pekerjaan" required>
								</div>
								<div class="col-xs-2" align="left" style="padding-left: 0px;">
									<!-- <input type="text" class="form-control" id="hubungan_darurat3" name="hubungan_darurat3" placeholder="Hubungan" required> -->
									<select id="hubungan_darurat3" name="hubungan_darurat3" class="form-control select2" data-placeholder="Hubungan Keluarga" style="width: 100%; font-size: 20px;" required>
										<option></option>
										@foreach($darurats as $darurat)
										<option value="{{ $darurat }}">{{ $darurat }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>

						<div class="form-group row" style="margin-top: 3%;" id="lain_lain">
							<label class="col-xs-9 col-xs-offset-1 header-tab">F. Lain-Lain</label>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>1. Bersediakah anda bekerja lembur bila diperlukan ? Jelaskan alasannya <span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-2" style="padding-left: 50px">
									<input type="radio" id="1" name="1" value="BERSEDIA"> BERSEDIA
								</div>
								<div class="col-xs-2">
									<input type="radio" id="1" name="1" value="TIDAK BERSEDIA"> TIDAK BERSEDIA
								</div>
								<div class="col-xs-7 pull-right">
									<textarea id="no_1" name="no_1" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>2. Besediakah anda dipindah bagian bila diperlukan ? Jelaskan alasannya<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-2" style="padding-left: 50px">
									<input type="radio" id="2" name="2" value="BERSEDIA"> BERSEDIA
								</div>
								<div class="col-xs-2">
									<input type="radio" id="2" name="2" value="TIDAK BERSEDIA"> TIDAK BERSEDIA
								</div>
								<div class="col-xs-7 pull-right">
									<textarea id="no_2" name="no_2" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>3. Bersediakah anda dipindah ke daerah lain, dinas luar kota bila diperlukan ? Jelaskan alasanya<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-2" style="padding-left: 50px">
									<input type="radio" id="3" name="3" value="BERSEDIA"> BERSEDIA
								</div>
								<div class="col-xs-2">
									<input type="radio" id="3" name="3" value="TIDAK BERSEDIA"> TIDAK BERSEDIA
								</div>
								<div class="col-xs-7 pull-right">
									<textarea id="no_3" name="no_3" class="form-control" rows="3"required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>4. Sebutkan secara berurut, apa yang anda prioritaskan : Gaji, Suasana Kerja, Kedudukan    <span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_4" name="no_4" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>5. Jenis pekerjaan apa yang sebenarnya anda sukai : Kantor, Lapangan, Dinas Luar, dsb Mengapa ?<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_5" name="no_5" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>6. Apakah anda memiliki hubungan keluarga / teman baik dengan seseorang (karyawan) Perusahaan ini ? Sebutkan dan jelaskan<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-2" style="padding-left: 50px">
									<input type="radio" id="6" name="6" value="BERSEDIA" >BERSEDIAi
								</div>
								<div class="col-xs-2">
									<input type="radio" id="6" name="6" value="TIDAK BERSEDIA"> TIDAK BERSEDIA
								</div>
								<div class="col-xs-7 pull-right">
									<textarea id="no_6" name="no_6" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>7. Sebutkan nama, alamat, No. Telp, orang yang dapat kami hubungi untuk memperoleh referensi tentang diri anda ?<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_7" name="no_7" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>8. SIM apa yang anda miliki ?<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_8" name="no_8" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>9. Jika anda dinyatakan bisa bergabung dengan Perusahaan ini, kapan anda bersedia mulai bekerja ?<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_9" name="no_9" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>10. Apa  yang anda harapkan bilamana anda bergabung dengan Perusahaan ini ? Mengapa<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_10" name="no_10" class="form-control" rows="3" placecholder="Alamat Domisili (Sekarang)" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>11. Jika anda diterima di PT. Yamaha Musical Products Indonesia, berapa gaji yang anda harapkan ?<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_11" name="no_11" class="form-control" rows="3" required></textarea>
								</div>
							</div>

							<div class="col-xs-9 col-xs-offset-1">
								<div class="col-xs-12">
									<label>12. Tuliskan nomor Ijazah Pendidikan terakhir Anda<span class="text-red">*</span></label>
								</div>	
								<div class="col-xs-7 pull-right">
									<textarea id="no_12" name="no_12" class="form-control" rows="3" required></textarea>
								</div>
							</div>
						</div>
						<div class="form-group row" align="center" style="margin-top: 5%; margin-bottom: 10%;" id="tombol">
							<div class="col-xs-6 col-xs-offset-6">
								<a style="margin-right: 3%;" class="btn btn-danger" href="{{ route('emp_service', ['id' =>'1']) }}"><i class="fa fa-close"></i> CANCEL</a>
								<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url("js/html2canvas.js")}}"></script>
<link rel="stylesheet" href="{{ url("css/jquery.signaturepad.css")}}">
<script src="{{ url("js/numeric-1.2.6.min.js")}}"></script>
<script src="{{ url("js/bezier.js")}}"></script>
<script src="{{ url("js/jquery.signaturepad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});			

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	
	jQuery(document).ready(function() {
	$('body').toggleClass("sidebar-collapse");

	$('.select3').select2({
		allowClear: true
	});
	$('.select2').select2({
		allowClear: true
	});
	$('.select5').select2({
		allowClear: true
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: 'dd-mm-yyyy',
		endDate: '<?php echo $tgl_max ?>'

	})

	$('.yearpicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: 'yyyy',
		minViewMode: 'decade',
		orientation: 'top',
		endDate: '<?php echo $tgl_max ?>'
	})

	
	$("#data_pribadi").hide();	
	$("#susunan_keluarga").hide();	
	$("#susunan_keluarga_inti").hide();	
	$("#pendidikan_formal").hide();	
	$("#emergency").hide();	
	$("#lain_lain").hide();	
	$("#tombol").hide();	

	});
function fillForm() {
	var emp_id = $("#emp_id").val();
	$("#employee_id").val(emp_id);

	var data = {
		emp_id : emp_id
	}

	$.get('{{ url("fetch/fill_emp_data") }}', data, function(result, status, xhr){
		if(result.status){
			if(result.data != null){
				$("#nama_lengkap").val(result.data.name);
				$("#employee_id").val(result.data.employee_id);
				$("#nik").val(result.data.nik);
				$("#npwp").val(result.data.npwp);
				$("#tempat_lahir").val(result.data.birth_place);
				$("#tanggal_lahir").val(result.data.tgl_lahir);
				$('#agama').val(result.data.religion).trigger('change.select2');
				$('#status_perkawinan').val(result.data.mariage_status).trigger('change.select2');
				$("#alamat_asal").val(result.data.address);
				$("#alamat_domisili").val(result.data.current_address);
				$("#telepon_rumah").val(result.data.telephone);
				$("#hp").val(result.data.handphone);
				$("#email").val(result.data.email);
				$("#bpjskes").val(result.data.bpjskes);
				$("#faskes").val(result.data.faskes);
				$("#bpjstk").val(result.data.bpjstk);

				var ayah = result.data.f_ayah.split('_');
				$("#nama_ayah").val(ayah[0]);
				$('#kelamin_ayah').val(ayah[1]).trigger('change.select2');
				$("#tempat_lahir_ayah").val(ayah[2]);
				$("#tanggal_lahir_ayah").val(ayah[3]);
				$("#pekerjaan_ayah").val(ayah[4]);

				var ibu = result.data.f_ibu.split('_');
				$("#nama_ibu").val(ibu[0]);
				$('#kelamin_ibu').val(ibu[1]).trigger('change.select2');
				$("#tempat_lahir_ibu").val(ibu[2]);
				$("#tanggal_lahir_ibu").val(ibu[3]);
				$("#pekerjaan_ibu").val(ibu[4]);

				var saudara1 = result.data.f_saudara1.split('_');
				$("#nama_saudara1").val(saudara1[0]);
				$('#kelamin_saudara1').val(saudara1[1]).trigger('change.select2');
				$("#tempat_lahir_saudara1").val(saudara1[2]);
				$("#tanggal_lahir_saudara1").val(saudara1[3]);
				$("#pekerjaan_saudara1").val(saudara1[4]);

				var saudara2 = result.data.f_saudara2.split('_');
				$("#nama_saudara2").val(saudara2[0]);
				$('#kelamin_saudara2').val(saudara2[1]).trigger('change.select2');
				$("#tempat_lahir_saudara2").val(saudara2[2]);
				$("#tanggal_lahir_saudara2").val(saudara2[3]);
				$("#pekerjaan_saudara2").val(saudara2[4]);

				var saudara3 = result.data.f_saudara3.split('_');
				$("#nama_saudara3").val(saudara3[0]);
				$('#kelamin_saudara3').val(saudara3[1]).trigger('change.select2');
				$("#tempat_lahir_saudara3").val(saudara3[2]);
				$("#tanggal_lahir_saudara3").val(saudara3[3]);
				$("#pekerjaan_saudara3").val(saudara3[4]);

				var saudara4 = result.data.f_saudara4.split('_');
				$("#nama_saudara4").val(saudara4[0]);
				$('#kelamin_saudara4').val(saudara4[1]).trigger('change.select2');
				$("#tempat_lahir_saudara4").val(saudara4[2]);
				$("#tanggal_lahir_saudara4").val(saudara4[3]);
				$("#pekerjaan_saudara4").val(saudara4[4]);

				var saudara5 = result.data.f_saudara5.split('_');
				$("#nama_saudara5").val(saudara5[0]);
				$('#kelamin_saudara5').val(saudara5[1]).trigger('change.select2');
				$("#tempat_lahir_saudara5").val(saudara5[2]);
				$("#tanggal_lahir_saudara5").val(saudara5[3]);
				$("#pekerjaan_saudara5").val(saudara5[4]);

				var saudara6 = result.data.f_saudara6.split('_');
				$("#nama_saudara6").val(saudara6[0]);
				$('#kelamin_saudara6').val(saudara6[1]).trigger('change.select2');
				$("#tempat_lahir_saudara6").val(saudara6[2]);
				$("#tanggal_lahir_saudara6").val(saudara6[3]);
				$("#pekerjaan_saudara6").val(saudara6[4]);

				var saudara7 = result.data.f_saudara7.split('_');
				$("#nama_saudara7").val(saudara7[0]);
				$('#kelamin_saudara7').val(saudara7[1]).trigger('change.select2');
				$("#tempat_lahir_saudara7").val(saudara7[2]);
				$("#tanggal_lahir_saudara7").val(saudara7[3]);
				$("#pekerjaan_saudara7").val(saudara7[4]);

				var saudara8 = result.data.f_saudara8.split('_');
				$("#nama_saudara8").val(saudara8[0]);
				$('#kelamin_saudara8').val(saudara8[1]).trigger('change.select2');
				$("#tempat_lahir_saudara8").val(saudara8[2]);
				$("#tanggal_lahir_saudara8").val(saudara8[3]);
				$("#pekerjaan_saudara8").val(saudara8[4]);

				var saudara9 = result.data.f_saudara9.split('_');
				$("#nama_saudara9").val(saudara9[0]);
				$('#kelamin_saudara9').val(saudara9[1]).trigger('change.select2');
				$("#tempat_lahir_saudara9").val(saudara9[2]);
				$("#tanggal_lahir_saudara9").val(saudara9[3]);
				$("#pekerjaan_saudara9").val(saudara9[4]);

				var saudara10 = result.data.f_saudara10.split('_');
				$("#nama_saudara10").val(saudara10[0]);
				$('#kelamin_saudara10').val(saudara10[1]).trigger('change.select2');
				$("#tempat_lahir_saudara10").val(saudara10[2]);
				$("#tanggal_lahir_saudara10").val(saudara10[3]);
				$("#pekerjaan_saudara10").val(saudara10[4]);

				var saudara11 = result.data.f_saudara11.split('_');
				$("#nama_saudara11").val(saudara11[0]);
				$('#kelamin_saudara11').val(saudara11[1]).trigger('change.select2');
				$("#tempat_lahir_saudara11").val(saudara11[2]);
				$("#tanggal_lahir_saudara11").val(saudara11[3]);
				$("#pekerjaan_saudara11").val(saudara11[4]);

				var saudara12 = result.data.f_saudara12.split('_');
				$("#nama_saudara12").val(saudara12[0]);
				$('#kelamin_saudara12').val(saudara12[1]).trigger('change.select2');
				$("#tempat_lahir_saudara12").val(saudara12[2]);
				$("#tanggal_lahir_saudara12").val(saudara12[3]);
				$("#pekerjaan_saudara12").val(saudara12[4]);		

				var pasangan = result.data.m_pasangan.split('_');
				$("#nama_pasangan").val(pasangan[0]);
				$('#kelamin_pasangan').val(pasangan[1]).trigger('change.select2');
				$("#tempat_lahir_pasangan").val(pasangan[2]);
				$("#tanggal_lahir_pasangan").val(pasangan[3]);
				$("#pekerjaan_pasangan").val(pasangan[4]);	

				var anak1 = result.data.m_anak1.split('_');
				$("#nama_anak1").val(anak1[0]);
				$('#kelamin_anak1').val(anak1[1]).trigger('change.select2');
				$("#tempat_lahir_anak1").val(anak1[2]);
				$("#tanggal_lahir_anak1").val(anak1[3]);
				$("#pekerjaan_anak1").val(anak1[4]);

				var anak2 = result.data.m_anak2.split('_');
				$("#nama_anak2").val(anak2[0]);
				$('#kelamin_anak2').val(anak2[1]).trigger('change.select2');
				$("#tempat_lahir_anak2").val(anak2[2]);
				$("#tanggal_lahir_anak2").val(anak2[3]);
				$("#pekerjaan_anak2").val(anak2[4]);	

				var anak3 = result.data.m_anak3.split('_');
				$("#nama_anak3").val(anak3[0]);
				$('#kelamin_anak3').val(anak3[1]).trigger('change.select2');
				$("#tempat_lahir_anak3").val(anak3[2]);
				$("#tanggal_lahir_anak3").val(anak3[3]);
				$("#pekerjaan_anak3").val(anak3[4]);	

				var anak4 = result.data.m_anak4.split('_');
				$("#nama_anak4").val(anak4[0]);
				$('#kelamin_anak4').val(anak4[1]).trigger('change.select2');
				$("#tempat_lahir_anak4").val(anak4[2]);
				$("#tanggal_lahir_anak4").val(anak4[3]);
				$("#pekerjaan_anak4").val(anak4[4]);	

				var anak5 = result.data.m_anak5.split('_');
				$("#nama_anak5").val(anak5[0]);
				$('#kelamin_anak5').val(anak5[1]).trigger('change.select2');
				$("#tempat_lahir_anak5").val(anak5[2]);
				$("#tanggal_lahir_anak5").val(anak5[3]);
				$("#pekerjaan_anak5").val(anak5[4]);	

				var anak6 = result.data.m_anak6.split('_');
				$("#nama_anak6").val(anak6[0]);
				$('#kelamin_anak6').val(anak6[1]).trigger('change.select2');
				$("#tempat_lahir_anak6").val(anak6[2]);
				$("#tanggal_lahir_anak6").val(anak6[3]);
				$("#pekerjaan_anak6").val(anak6[4]);	

				var anak7 = result.data.m_anak7.split('_');
				$("#nama_anak7").val(anak7[0]);
				$('#kelamin_anak7').val(anak7[1]).trigger('change.select2');
				$("#tempat_lahir_anak7").val(anak7[2]);
				$("#tanggal_lahir_anak7").val(anak7[3]);
				$("#pekerjaan_anak7").val(anak7[4]);	

				var sd = result.data.sd.split('_');
				$("#sd").val(sd[0]);
				$("#sd_masuk").val(sd[2]);
				$("#sd_lulus").val(sd[3]);

				var smp = result.data.smp.split('_');
				$("#smp").val(smp[0]);
				$("#smp_masuk").val(smp[2]);
				$("#smp_lulus").val(smp[3]);

				var sma = result.data.sma.split('_');
				$("#sma").val(sma[0]);
				$("#sma_jurusan").val(sma[1]);
				$("#sma_masuk").val(sma[2]);
				$("#sma_lulus").val(sma[3]);

				var s1 = result.data.s1.split('_');
				$("#s1").val(s1[0]);
				$("#s1_jurusan").val(s1[1]);
				$("#s1_masuk").val(s1[2]);
				$("#s1_lulus").val(s1[3]);

				var s2 = result.data.s2.split('_');
				$("#s2").val(s2[0]);
				$("#s2_jurusan").val(s2[1]);
				$("#s2_masuk").val(s2[2]);
				$("#s2_lulus").val(s2[3]);

				var s3 = result.data.s3.split('_');
				$("#s3").val(s3[0]);
				$("#s3_jurusan").val(s3[1]);
				$("#s3_masuk").val(s3[2]);
				$("#s3_lulus").val(s3[3]);

				var darurat1 = result.data.emergency1.split('_');
				$("#nama_darurat1").val(darurat1[0]);
				$("#telp_darurat1").val(darurat1[1]);
				$("#pekerjaan_darurat1").val(darurat1[2]);
				$("#hubungan_darurat1").val(darurat1[3]);

				var darurat2 = result.data.emergency2.split('_');
				$("#nama_darurat2").val(darurat2[0]);
				$("#telp_darurat2").val(darurat2[1]);
				$("#pekerjaan_darurat2").val(darurat2[2]);
				$("#hubungan_darurat2").val(darurat2[3]);

				var darurat3 = result.data.emergency3.split('_');
				$("#nama_darurat3").val(darurat3[0]);
				$("#telp_darurat3").val(darurat3[1]);
				$("#pekerjaan_darurat3").val(darurat3[2]);
				$("#hubungan_darurat3").val(darurat3[3]);
			}	
		}
	});
}

$('#form_update').on('submit', function(event){
	if(!validateForm()){
		return false
	}

	if(confirm("Data yang anda input akan disimpan oleh sistem.\nApakah anda yakin ?")){
		event.preventDefault();
		var formdata = new FormData(this);

		$("#loading").show();

		$.ajax({
			url:"{{url('update/data/calon/karyawan')}}",
			method:'post',
			data:formdata,
			dataType:"json",
			processData: false,
			contentType: false,
			cache: false,
			success:function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					openHRq();

				}else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();				
				openErrorGritter('Error!', 'Calon Karyawan Gagal Ditambahkan');
			}
		});
	}

	
});

function checkEmp(value) {
		var data = {
			nik:$('#nik').val()
		}

		$.get('{{ url("get/calon/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				if(result.employee != null){

					var address = result.employee[0].address.split('/');
					var current_address = result.employee[0].current_address.split('/');

					openSuccessGritter('Success!', result.message);
					$("#data_pribadi").show();	
					$("#susunan_keluarga").show();	
					$("#susunan_keluarga_inti").show();	
					$("#pendidikan_formal").show();	
					$("#emergency").show();	
					$("#lain_lain").show();	
					$("#tombol").show();


					$("#nama_lengkap").val(result.employee[0].name);
					$("#employee_id").val(result.employee[0].employee_id);
					$("#nik").val(result.employee[0].nik);
					$("#npwp").val(result.employee[0].npwp);
					$("#tempat_lahir").val(result.employee[0].birth_place);
					$("#tanggal_lahir").val(result.employee[0].birth_date);
					$('#gender').val(result.employee[0].gender).trigger('change.select2');
					$('#agama').val(result.employee[0].religion).trigger('change.select2');
					$('#status_perkawinan').val(result.employee[0].mariage_status).trigger('change.select2');
					$("#alamat_asal").val(address[0]);
					$("#rt_asal").val(address[1]);
					$("#rw_asal").val(address[2]);
					$("#kelurahan_asal").val(address[3]);
					$("#kecamatan_asal").val(address[4]);
					$("#kota_asal").val(address[5]);
					$("#alamat_domisili").val(current_address[0]);
					$("#rt_domisili").val(current_address[1]);
					$("#rw_domisili").val(current_address[2]);
					$("#kelurahan_domisili").val(current_address[3]);
					$("#kecamatan_domisili").val(current_address[4]);
					$("#kota_domisili").val(current_address[5]);
					$("#telepon_rumah").val(result.employee[0].telephone);
					$("#hp").val(result.employee[0].handphone);
					$("#email").val(result.employee[0].email);
					$("#bpjskes").val(result.employee[0].bpjskes);
					$("#faskes").val(result.employee[0].faskes);
					$("#bpjstk").val(result.employee[0].bpjstk);

					var ayah = result.employee[0].f_ayah.split('_');
					$("#nama_ayah").val(ayah[0]);
					$('#kelamin_ayah').val(ayah[1]).trigger('change.select2');
					$("#tempat_lahir_ayah").val(ayah[2]);
					$("#tanggal_lahir_ayah").val(ayah[3]);
					$("#pekerjaan_ayah").val(ayah[4]);

					var ibu = result.employee[0].f_ibu.split('_');
					$("#nama_ibu").val(ibu[0]);
					$('#kelamin_ibu').val(ibu[1]).trigger('change.select2');
					$("#tempat_lahir_ibu").val(ibu[2]);
					$("#tanggal_lahir_ibu").val(ibu[3]);
					$("#pekerjaan_ibu").val(ibu[4]);

					var saudara1 = result.employee[0].f_saudara1.split('_');
					$("#nama_saudara1").val(saudara1[0]);
					$('#kelamin_saudara1').val(saudara1[1]).trigger('change.select2');
					$("#tempat_lahir_saudara1").val(saudara1[2]);
					$("#tanggal_lahir_saudara1").val(saudara1[3]);
					$("#pekerjaan_saudara1").val(saudara1[4]);

					var saudara2 = result.employee[0].f_saudara2.split('_');
					$("#nama_saudara2").val(saudara2[0]);
					$('#kelamin_saudara2').val(saudara2[1]).trigger('change.select2');
					$("#tempat_lahir_saudara2").val(saudara2[2]);
					$("#tanggal_lahir_saudara2").val(saudara2[3]);
					$("#pekerjaan_saudara2").val(saudara2[4]);

					var saudara3 = result.employee[0].f_saudara3.split('_');
					$("#nama_saudara3").val(saudara3[0]);
					$('#kelamin_saudara3').val(saudara3[1]).trigger('change.select2');
					$("#tempat_lahir_saudara3").val(saudara3[2]);
					$("#tanggal_lahir_saudara3").val(saudara3[3]);
					$("#pekerjaan_saudara3").val(saudara3[4]);

					var saudara4 = result.employee[0].f_saudara4.split('_');
					$("#nama_saudara4").val(saudara4[0]);
					$('#kelamin_saudara4').val(saudara4[1]).trigger('change.select2');
					$("#tempat_lahir_saudara4").val(saudara4[2]);
					$("#tanggal_lahir_saudara4").val(saudara4[3]);
					$("#pekerjaan_saudara4").val(saudara4[4]);

					var saudara5 = result.employee[0].f_saudara5.split('_');
					$("#nama_saudara5").val(saudara5[0]);
					$('#kelamin_saudara5').val(saudara5[1]).trigger('change.select2');
					$("#tempat_lahir_saudara5").val(saudara5[2]);
					$("#tanggal_lahir_saudara5").val(saudara5[3]);
					$("#pekerjaan_saudara5").val(saudara5[4]);

					var saudara6 = result.employee[0].f_saudara6.split('_');
					$("#nama_saudara6").val(saudara6[0]);
					$('#kelamin_saudara6').val(saudara6[1]).trigger('change.select2');
					$("#tempat_lahir_saudara6").val(saudara6[2]);
					$("#tanggal_lahir_saudara6").val(saudara6[3]);
					$("#pekerjaan_saudara6").val(saudara6[4]);

					var saudara7 = result.employee[0].f_saudara7.split('_');
					$("#nama_saudara7").val(saudara7[0]);
					$('#kelamin_saudara7').val(saudara7[1]).trigger('change.select2');
					$("#tempat_lahir_saudara7").val(saudara7[2]);
					$("#tanggal_lahir_saudara7").val(saudara7[3]);
					$("#pekerjaan_saudara7").val(saudara7[4]);

					var saudara8 = result.employee[0].f_saudara8.split('_');
					$("#nama_saudara8").val(saudara8[0]);
					$('#kelamin_saudara8').val(saudara8[1]).trigger('change.select2');
					$("#tempat_lahir_saudara8").val(saudara8[2]);
					$("#tanggal_lahir_saudara8").val(saudara8[3]);
					$("#pekerjaan_saudara8").val(saudara8[4]);

					var saudara9 = result.employee[0].f_saudara9.split('_');
					$("#nama_saudara9").val(saudara9[0]);
					$('#kelamin_saudara9').val(saudara9[1]).trigger('change.select2');
					$("#tempat_lahir_saudara9").val(saudara9[2]);
					$("#tanggal_lahir_saudara9").val(saudara9[3]);
					$("#pekerjaan_saudara9").val(saudara9[4]);

					var saudara10 = result.employee[0].f_saudara10.split('_');
					$("#nama_saudara10").val(saudara10[0]);
					$('#kelamin_saudara10').val(saudara10[1]).trigger('change.select2');
					$("#tempat_lahir_saudara10").val(saudara10[2]);
					$("#tanggal_lahir_saudara10").val(saudara10[3]);
					$("#pekerjaan_saudara10").val(saudara10[4]);

					var saudara11 = result.employee[0].f_saudara11.split('_');
					$("#nama_saudara11").val(saudara11[0]);
					$('#kelamin_saudara11').val(saudara11[1]).trigger('change.select2');
					$("#tempat_lahir_saudara11").val(saudara11[2]);
					$("#tanggal_lahir_saudara11").val(saudara11[3]);
					$("#pekerjaan_saudara11").val(saudara11[4]);

					var saudara12 = result.employee[0].f_saudara12.split('_');
					$("#nama_saudara12").val(saudara12[0]);
					$('#kelamin_saudara12').val(saudara12[1]).trigger('change.select2');
					$("#tempat_lahir_saudara12").val(saudara12[2]);
					$("#tanggal_lahir_saudara12").val(saudara12[3]);
					$("#pekerjaan_saudara12").val(saudara12[4]);		

					var pasangan = result.employee[0].m_pasangan.split('_');
					$("#nama_pasangan").val(pasangan[0]);
					$('#kelamin_pasangan').val(pasangan[1]).trigger('change.select2');
					$("#tempat_lahir_pasangan").val(pasangan[2]);
					$("#tanggal_lahir_pasangan").val(pasangan[3]);
					$("#pekerjaan_pasangan").val(pasangan[4]);	

					var anak1 = result.employee[0].m_anak1.split('_');
					$("#nama_anak1").val(anak1[0]);
					$('#kelamin_anak1').val(anak1[1]).trigger('change.select2');
					$("#tempat_lahir_anak1").val(anak1[2]);
					$("#tanggal_lahir_anak1").val(anak1[3]);
					$("#pekerjaan_anak1").val(anak1[4]);

					var anak2 = result.employee[0].m_anak2.split('_');
					$("#nama_anak2").val(anak2[0]);
					$('#kelamin_anak2').val(anak2[1]).trigger('change.select2');
					$("#tempat_lahir_anak2").val(anak2[2]);
					$("#tanggal_lahir_anak2").val(anak2[3]);
					$("#pekerjaan_anak2").val(anak2[4]);	

					var anak3 = result.employee[0].m_anak3.split('_');
					$("#nama_anak3").val(anak3[0]);
					$('#kelamin_anak3').val(anak3[1]).trigger('change.select2');
					$("#tempat_lahir_anak3").val(anak3[2]);
					$("#tanggal_lahir_anak3").val(anak3[3]);
					$("#pekerjaan_anak3").val(anak3[4]);	

					var anak4 = result.employee[0].m_anak4.split('_');
					$("#nama_anak4").val(anak4[0]);
					$('#kelamin_anak4').val(anak4[1]).trigger('change.select2');
					$("#tempat_lahir_anak4").val(anak4[2]);
					$("#tanggal_lahir_anak4").val(anak4[3]);
					$("#pekerjaan_anak4").val(anak4[4]);	

					var anak5 = result.employee[0].m_anak5.split('_');
					$("#nama_anak5").val(anak5[0]);
					$('#kelamin_anak5').val(anak5[1]).trigger('change.select2');
					$("#tempat_lahir_anak5").val(anak5[2]);
					$("#tanggal_lahir_anak5").val(anak5[3]);
					$("#pekerjaan_anak5").val(anak5[4]);	

					var anak6 = result.employee[0].m_anak6.split('_');
					$("#nama_anak6").val(anak6[0]);
					$('#kelamin_anak6').val(anak6[1]).trigger('change.select2');
					$("#tempat_lahir_anak6").val(anak6[2]);
					$("#tanggal_lahir_anak6").val(anak6[3]);
					$("#pekerjaan_anak6").val(anak6[4]);	

					var anak7 = result.employee[0].m_anak7.split('_');
					$("#nama_anak7").val(anak7[0]);
					$('#kelamin_anak7').val(anak7[1]).trigger('change.select2');
					$("#tempat_lahir_anak7").val(anak7[2]);
					$("#tanggal_lahir_anak7").val(anak7[3]);
					$("#pekerjaan_anak7").val(anak7[4]);	

					var sd = result.employee[0].sd.split('_');
					$("#sd").val(sd[0]);
					$("#sd_masuk").val(sd[2]);
					$("#sd_lulus").val(sd[3]);

					var smp = result.employee[0].smp.split('_');
					$("#smp").val(smp[0]);
					$("#smp_masuk").val(smp[2]);
					$("#smp_lulus").val(smp[3]);

					var sma = result.employee[0].sma.split('_');
					$("#sma").val(sma[0]);
					$("#sma_jurusan").val(sma[1]);
					$("#sma_masuk").val(sma[2]);
					$("#sma_lulus").val(sma[3]);

					var s1 = result.employee[0].s1.split('_');
					$("#s1").val(s1[0]);
					$("#s1_jurusan").val(s1[1]);
					$("#s1_masuk").val(s1[2]);
					$("#s1_lulus").val(s1[3]);

					var s2 = result.employee[0].s2.split('_');
					$("#s2").val(s2[0]);
					$("#s2_jurusan").val(s2[1]);
					$("#s2_masuk").val(s2[2]);
					$("#s2_lulus").val(s2[3]);

					var s3 = result.employee[0].s3.split('_');
					$("#s3").val(s3[0]);
					$("#s3_jurusan").val(s3[1]);
					$("#s3_masuk").val(s3[2]);
					$("#s3_lulus").val(s3[3]);

					var darurat1 = result.employee[0].emergency1.split('_');
					$("#nama_darurat1").val(darurat1[0]);
					$("#telp_darurat1").val(darurat1[1]);
					$("#pekerjaan_darurat1").val(darurat1[2]);
					$("#hubungan_darurat1").val(darurat1[3]).trigger('change');

					var darurat2 = result.employee[0].emergency2.split('_');
					$("#nama_darurat2").val(darurat2[0]);
					$("#telp_darurat2").val(darurat2[1]);
					$("#pekerjaan_darurat2").val(darurat2[2]);
					$("#hubungan_darurat2").val(darurat2[3]).trigger('change');

					var darurat3 = result.employee[0].emergency3.split('_');
					$("#nama_darurat3").val(darurat3[0]);
					$("#telp_darurat3").val(darurat3[1]);
					$("#pekerjaan_darurat3").val(darurat3[2]);
					$("#hubungan_darurat3").val(darurat3[3]).trigger('change');

					var answer1 = result.answer[0].answer1.split('/');
					// $("#1").val(answer1[0]);
					$("#no_1").val(answer1[1]);

					var answer2 = result.answer[0].answer2.split('/');
					// $("#2").val(answer2[0]);
					$("#no_2").val(answer2[1]);

					var answer3 = result.answer[0].answer3.split('/');
					// $("#3").val(answer3[0]);
					$("#no_3").val(answer3[1]);

					$("#no_4").val(result.answer[0].answer4);

					$("#no_5").val(result.answer[0].answer5);

					var answer6 = result.answer[0].answer6.split('/');
					// $("#6").val(answer6[0]);
					$("#no_6").val(answer6[1]);

					$("#no_7").val(result.answer[0].answer7);
					$("#no_8").val(result.answer[0].answer8);
					$("#no_9").val(result.answer[0].answer9);
					$("#no_10").val(result.answer[0].answer10);
					$("#no_11").val(result.answer[0].answer11);
					$("#no_12").val(result.answer[0].answer12);

					// if (answer1[0] == 'BERSEDIA') {
					// 	 document.getElementById('1').checked = true;
					// }else if(answer1[0] == 'TIDAK BERSEDIA'){
					// 	document.getElementById('1').checked = true;
					// }else{
					// 	document.getElementById('1').checked = false;
					// }

					// $('input[name="1"][value="' + answer1[0] + '"]').prop('checked', true);
				}	
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

function checkLength(value){
	if (value.length == 16) {
		openSuccessGritter('Success!', 'NIK Ditemukan, Silahkan Edit Data, Dan Simpan Kembali.');
	}else{
		openErrorGritter('Error!', 'NIK Tidak Ditemukan, Silahkan Isi Data Secara Lengkap Dulu.');
	}
}

function validateForm() {

	if($("#nama_lengkap").val() == ''){
		openErrorGritter('Error!', 'Nama Lengkap Harus Diisi');
		$("#nama_lengkap").focus();
		return false;
	}

	if($("#tempat_lahir").val() == ''){
		openErrorGritter('Error!', 'Tempat Lahir Harus Diisi');
		$("#tempat_lahir").focus();
		return false;
	}

	if($("#tanggal_lahir").val() == ''){
		openErrorGritter('Error!', 'Tanggal Lahir Harus Diisi');
		$("#tanggal_lahir").focus();
		return false;
	}

	if($("#agama").val() == ''){
		openErrorGritter('Error!', 'Agama Harus Diisi');
		$("#agama").focus();
		return false;
	}

	if($("#status_perkawinan").val() == ''){
		openErrorGritter('Error!', 'Status Perkawinan Harus Diisi');
		$("#status_perkawinan").focus();
		return false;
	}

	if($("#alamat_asal").val() == ''){
		openErrorGritter('Error!', 'Alamat Asal Harus Diisi');
		$("#alamat_asal").focus();
		return false;
	}

	if($("#alamat_domisili").val() == ''){
		openErrorGritter('Error!', 'Alamat Domisili Harus Diisi');
		$("#alamat_domisili").focus();
		return false;
	}

	if($("#hp").val() == ''){
		openErrorGritter('Error!', 'Nomor Handphone Harus Diisi');
		$("#hp").focus();
		return false;
	}

	if($("#email").val() == ''){
		openErrorGritter('Error!', 'Email Harus Diisi');
		$("#email").focus();
		return false;
	}else{
		if(!validateEmail($("#email").val())){
			openErrorGritter('Error!', 'Email tidak valid');
			$("#email").focus();
			return false;
		}
	}

	if($("#nama_ayah").val() == ''){
		openErrorGritter('Error!', 'Indentitas Ayah Harus Diisi');
		$("#nama_ayah").focus();
		return false;
	}

	if($("#nama_ibu").val() == ''){
		openErrorGritter('Error!', 'Indentitas Ibu Harus Diisi');
		$("#nama_ibu").focus();
		return false;
	}

	if($("#nama_darurat1").val() == '' || $("#telp_darurat1").val() == '' || $("#hubungan_darurat1").val() == ''){
		openErrorGritter('Error!', 'Kontak Kondisi Emergency Harus Diisi Semua');
		$("#nama_darurat1").focus();
		return false;
	}

	if($("#nama_darurat2").val() == '' || $("#telp_darurat2").val() == '' || $("#hubungan_darurat2").val() == ''){
		openErrorGritter('Error!', 'Kontak Kondisi Emergency Harus Diisi Semua');
		$("#nama_darurat2").focus();
		return false;
	}

	if($("#nama_darurat3").val() == '' || $("#telp_darurat3").val() == '' || $("#hubungan_darurat3").val() == ''){
		openErrorGritter('Error!', 'Kontak Kondisi Emergency Harus Diisi Semua');
		$("#nama_darurat3").focus();
		return false;
	}

	return true;		
}

function openHRq() {
	window.location.replace('{{ url("/") }}');
}

function validateEmail(email) {
	const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
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
</script>
@endsection