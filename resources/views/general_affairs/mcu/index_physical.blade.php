@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:left;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:left;
	}
	tfoot>tr>th{
		text-align:left;
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
		/*vertical-align: middle;*/
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	#tableResume > thead > tr > th {
		padding-left: 5px;
		padding-top: 2px;
		padding-bottom: 2px;
	}

	#tableKaryawan > thead > tr > th {
		padding-left: 5px;
		padding-top: 3px;
		padding-bottom: 3px;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	#loading, #error { display: none; }

	.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 16px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  width: 0px;
}

/* Hide the browser's default radio button */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #e8e8e8;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2cb802;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.container .checkmark:after {
  top: 9px;
  left: 9px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
}

.container_checkmark {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 13px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1 style="font-size: 15px">
		{{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>			
	<div class="row">
		<div class="col-xs-4 pull-left" style="padding-right: 5px;">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px;">
				<div class="box-body">
					<input type="hidden" name="mcu_group" id="mcu_group" value="{{json_encode($mcu_group)}}">
					<div style="background-color: #7e5686;color: white;padding: 5px;text-align: center;">
						<span style="font-weight: bold">SCAN ID CARD / KETIK NIK</span>
					</div>
					<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px">
						<input type="text" id="tag" placeholder="Scan ID Card / Ketik NIK" style="width: 100%;font-size: 20px;text-align:center;padding: 5px">
					</div>
					<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;margin-top: 10px">
						<table class="table table-bordered" id="tableKaryawan">
							<thead>
								<tr>
									<th colspan="2" style="background-color: rgb(126,86,134);color: white">DETAIL KARYAWAN</th>
								</tr>
								<tr>
									<th>Tgl</th>
									<th>{{date('d M Y')}} - {{$mcu_periode[0]->periode}}
										<input type="hidden" name="check_date" id="check_date" value="{{date('Y-m-d')}}">
										<input type="hidden" name="schedule_id" id="schedule_id" value="">
									</th>
								</tr>
								<tr>
									<th style="width: 2%">NIK</th>
									<th style="width: 10%" id="employee_id">-</th>
								</tr>
								<tr>
									<th>Nama</th>
									<th id="name">-</th>
								</tr>
								<tr>
									<th>Dept</th>
									<th id="dept">-</th>
								</tr>
								<tr>
									<th>Sect</th>
									<th id="sect">-</th>
								</tr>
								<tr>
									<th>Group</th>
									<th id="groups">-</th>
								</tr>
								<tr>
									<th>Sub Group</th>
									<th id="sub_group">-</th>
								</tr>
								<tr>
									<th>JK</th>
									<th id="gender">-</th>
								</tr>
								<tr>
									<th>Tgl Lahir</th>
									<th id="birth_date">-</th>
								</tr>
								<tr>
									<th>Usia</th>
									<th id="age_th">-</th>
									<input type="hidden" id="age">
								</tr>
								<tr>
									<th>Grade / Penugasan</th>
									<th id="grade_code">-</th>
								</tr>
								<tr>
									<th>MCU Th. Lalu</th>
									<th id="mcu_group_code_before">-</th>
								</tr>
								<tr>
									<th>Audiometri</th>
									<th id="audiometri">-</th>
								</tr>
								<tr>
									<th>Chemical</th>
									<th id="chemical">-</th>
								</tr>
							</thead>
						</table>
						<div class="row">
							<div class="col-xs-12" style="margin-top: 15px">
								<button class="btn btn-danger" style="font-weight: bold;font-size: 20px;width: 100%;" onclick="clearAll()">
									CANCEL
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-8 pull-left" style="padding-left: 5px">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px;">
				<div class="box-body">
					<div style="background-color: #7e5686;color: #FFD700;padding: 5px;text-align: center;">
						<span style="font-weight: bold">DATA CEK FISIK</span>
					</div>
					<div class="row" id="div_clinic">
						<div class="col-xs-6" style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Tinggi Badan</label>
							<div class="input-group">
								<input type="number" name="height" id="height" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold">
									cm
								</div>
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Berat Badan</label>
							<div class="input-group">
								<input type="number" name="weight" id="weight" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly onchange="changeImt(this.value)">
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold">
									Kg
								</div>
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Tekanan Darah</label>
							<div class="input-group">
								<input type="number" name="blood_1" id="blood_1" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold;">
									/
								</div>
								<input type="number" name="blood_2" id="blood_2" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold">
									mm Hg
								</div>
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Nadi</label>
							<div class="input-group">
								<input type="number" name="pulse" id="pulse" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold">
									Kali / Menit
								</div>
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Respirasi</label>
							<div class="input-group">
								<input type="number" name="respiration" id="respiration" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold">
									Kali / Menit
								</div>
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Index Massa Tubuh (IMT)</label>
							<div class="input-group" style="width: 100%">
								<input type="number" name="imt" id="imt" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<!-- <div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold">
									Kali / Menit
								</div> -->
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Visus OD</label>
							<div class="input-group">
								<input type="number" name="visus_od_1" id="visus_od_1" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold;">
									/
								</div>
								<input type="number" name="visus_od_2" id="visus_od_2" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
							</div>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="visus_od" id="visus_od" class="visus_od" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container">Tidak
									  <input type="radio" name="visus_od" id="visus_od" class="visus_od" value="Tidak Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container">Kacamata
									  <input type="radio" name="visus_od" id="visus_od" class="visus_od" value="Kacamata">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-6"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Visus OS</label>
							<div class="input-group">
								<input type="number" name="visus_os_1" id="visus_os_1" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
								<div class="input-group-addon" style="font-size:20px;border: none; background-color: #7e5686; color: #FFD700;font-weight: bold;">
									/
								</div>
								<input type="number" name="visus_os_2" id="visus_os_2" class="form-control numpad" style="height: 50px;text-align: right;font-size: 30px;border: 1px solid #7e5686" readonly>
							</div>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="visus_os" id="visus_os" class="visus_os" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container">Tidak
									  <input type="radio" name="visus_os" id="visus_os" class="visus_os" value="Tidak Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container">Kacamata
									  <input type="radio" name="visus_os" id="visus_os" class="visus_os" value="Kacamata">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12"  style="margin-top: 15px">
							<label style="font-weight: bold;font-size: 16px">Buta Warna</label>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container">Total
									  <input type="radio" checked="true" name="color_blind" id="color_blind" class="color_blind" value="Total">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container">Partial
									  <input type="radio" checked="true" name="color_blind" id="color_blind" class="color_blind" value="Partial">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container">Tidak Buta Warna
									  <input type="radio" checked="true" name="color_blind" id="color_blind" class="color_blind" value="Tidak Buta Warna">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="div_resume" style="margin-top: 5px">
						<div class="col-xs-4" style="margin-top: 0px;padding-right: 5px">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">Resume Cek Fisik</label>
							</center>
							<table class="table table-bordered" style="padding: 0px" id="tableResume">
								<thead>
									<tr>
										<th style="width: 1%">TB</th>
										<th style="width: 10%" id="resume_height">-</th>
									</tr>
									<tr>
										<th>BB</th>
										<th id="resume_weight">-</th>
									</tr>
									<tr>
										<th>T. Darah</th>
										<th id="resume_blood">-</th>
									</tr>
									<tr>
										<th>Nadi</th>
										<th id="resume_pulse">-</th>
									</tr>
									<tr>
										<th>Respirasi</th>
										<th id="resume_respiration">-</th>
									</tr>
									<tr>
										<th>IMT</th>
										<th id="resume_imt">-</th>
									</tr>
									<tr>
										<th>Visus OD</th>
										<th id="resume_visus_od">-</th>
									</tr>
									<tr>
										<th>Visus OS</th>
										<th id="resume_visus_os">-</th>
									</tr>
									<tr>
										<th>Buta Warna</th>
										<th id="resume_color_blind">-</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="col-xs-8" style="margin-top: 0px;padding-left: 5px">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">Keluhan</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark" style="color: green">Tidak Ada
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Tidak Ada">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Pusing
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Pusing">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Nyeri Kepala
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Nyeri Kepala">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Pengelihatan Kabur
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Pengelihatan Kabur">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Nyeri Gigi
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Nyeri Gigi">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Nyeri Dada
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Nyeri Dada">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Nyeri Ulu Hati
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Nyeri Ulu Hati">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Nyeri Sendi
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Nyeri Sendi">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Gatal
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Gatal">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Penurunan Pendengaran
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Penurunan Pendengaran">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Flu
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Flu">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Batuk
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Batuk">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container_checkmark">Hamil
									  <input type="checkbox" name="complaint" id="complaint" class="complaint" value="Hamil" onclick="checkHamil();">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">Riwayat Penyakit</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark" style="color: green">TIDAK ADA
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="TIDAK ADA">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">EPILEPSI
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="EPILEPSI">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">ASMA
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="ASMA">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">DIABETES MELLITUS
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="DIABETES MELLITUS">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">SAKIT JANTUNG
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="SAKIT JANTUNG">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">TBC
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="TBC">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">KEGANASAN
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="KEGANASAN">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">HEPATITIS
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="HEPATITIS">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">OPERASI
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="OPERASI">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-4" style="margin-top: 15px">
									<label class="container_checkmark">ALERGI
									  <input type="checkbox" name="disease_history" id="disease_history" class="disease_history" value="ALERGI">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 pull-left" style="padding-right: 5px;">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px;">
				<div class="box-body">
					<div class="row" id="div_resume2">
						<div class="col-xs-3" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">ASIMETRI WAJAH</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Simetris
									  <input type="radio" checked="true" name="symmetry" id="symmetry" class="symmetry" value="Simetris">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Asimetris
									  <input type="radio" checked="true" name="symmetry" id="symmetry" class="symmetry" value="Asimetris">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">THT</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="tht" id="tht" class="tht" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Tidak Normal
									  <input type="radio" checked="true" name="tht" id="tht" class="tht" value="Tidak Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">GIGI / MULUT</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Carries
									  <input type="radio" checked="true" name="tooth" id="tooth" class="tooth" value="Carries">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Tidak Carries
									  <input type="radio" checked="true" name="tooth" id="tooth" class="tooth" value="Tidak Carries">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">KEPALA LEHER</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Pembesaran Kelenjar
									  <input type="radio" checked="true" name="head" id="head" class="head" value="Pembesaran Kelenjar">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="head" id="head" class="head" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">JANTUNG</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="heart" id="heart" class="heart" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Abnormal
									  <input type="radio" checked="true" name="heart" id="heart" class="heart" value="Abnormal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">PARU-PARU</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="lungs" id="lungs" class="lungs" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Abnormal
									  <input type="radio" checked="true" name="lungs" id="lungs" class="lungs" value="Abnormal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">ABDOMEN</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Nyeri
									  <input type="radio" checked="true" name="abdomen" id="abdomen" class="abdomen" value="Nyeri">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Tidak Nyeri
									  <input type="radio" checked="true" name="abdomen" id="abdomen" class="abdomen" value="Tidak Nyeri">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">HEPAR</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="hepar" id="hepar" class="hepar" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Abnormal
									  <input type="radio" checked="true" name="hepar" id="hepar" class="hepar" value="Abnormal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-4" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">LENGAN</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="arm" id="arm" class="arm" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Abnormal
									  <input type="radio" checked="true" name="arm" id="arm" class="arm" value="Abnormal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-4" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">TUNGKAI</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="limbs" id="limbs" class="limbs" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Abnormal
									  <input type="radio" checked="true" name="limbs" id="limbs" class="limbs" value="Abnormal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-4" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">RUANG GERAK SENDI</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Normal
									  <input type="radio" checked="true" name="joint" id="joint" class="joint" value="Normal">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">Abnormal
									  <input type="radio" checked="true" name="joint" id="joint" class="joint" value="Abnormal">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-6" style="margin-top: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">PENYAKIT KULIT</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container_checkmark">NORMAL
									  <input type="checkbox" name="skin" id="skin" class="skin" value="NORMAL">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container_checkmark">DERMATITIS
									  <input type="checkbox" name="skin" id="skin" class="skin" value="DERMATITIS">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container_checkmark">BERCAK MATI RASA
									  <input type="checkbox" name="skin" id="skin" class="skin" value="BERCAK MATI RASA">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
								<div class="col-xs-3" style="margin-top: 15px">
									<label class="container_checkmark">KULIT MENYELUPAS
									  <input type="checkbox" name="skin" id="skin" class="skin" value="KULIT MENYELUPAS">
									  <span class="checkmark_checkmark"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-6" style="margin-top: 5px;padding-left: 5px;">
							<center style="background-color: #2196F3;color: white">
								<label style="font-weight: bold;font-size: 16px;">RIWAYAT FOTO THORAX TERAKHIR</label>
							</center>
							<div class="col-xs-12" style="border: 1px solid #7e5686">
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">< 6 Bulan
									  <input type="radio" checked="true" name="thorax" id="thorax" class="thorax" value="< 6 Bulan" onclick="checkThorax(this.value);">
									  <span class="checkmark"></span>
									</label>
								</div>
								<div class="col-xs-6" style="margin-top: 15px">
									<label class="container">> 6 Bulan
									  <input type="radio" checked="true" name="thorax" id="thorax" class="thorax" value="> 6 Bulan" onclick="checkThorax(this.value);">
									  <span class="checkmark"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="div_doctor">
						<div class="col-xs-12" style="margin-top: 15px;margin-bottom: 15px">
							<div style="background-color: #7e5686;color: #FFD700;padding: 5px;text-align: center;">
								<span style="font-weight: bold">KATEGORI MEDICAL CHECK UP</span>
							</div>
						</div>
						<div class="col-xs-12">
							<!-- <div class="col-xs-2" style="padding-right: 5px;padding-left: 0px">
								<table class="table table-bordered" style="padding: 0px" id="tableGroup">
									<thead>
										<tr>
											<th style="width: 1%;vertical-align: middle;cursor: pointer;font-size: 20px;background-color: #7e5686;color: #FFD700;">
												PILIH KATEGORI &nbsp;<i style="font-size: 25px" class="fa fa-arrow-right"></i>
											</th>
										</tr>
									</thead>
								</table>
							</div> -->
							<table class="table table-bordered" style="padding: 2px;border: 0px;">
								<tr>
							<?php $index = 0; ?>
							@foreach($mcu_group as $mcu_group)
									<td style="padding-left: 2px;padding-right: 2px;border: 0px;">
										<table class="table table-bordered" style="padding: 2px" id="tableGroup">
											<thead>
												<tr>
													<th style="width: 1%;cursor: pointer;" onclick='$("input[name=mcu_group_code][value={{$mcu_group->code}}]").prop("checked", true)'>
														<label class="container" style="font-size: 20px">{{$mcu_group->code}}
														  <input type="radio" name="mcu_group_code" id="mcu_group_code" class="mcu_group_code" value="{{$mcu_group->code}}">
														  <span class="checkmark"></span>
														</label><br>
														<span class="text-red">{{$mcu_group->remark}}</span>
													</th>
												</tr>
												<?php $category = explode(',',$mcu_group->category); ?>
												<?php for ($i=0; $i < count($category); $i++) { ?>
													<tr>
														<th style="width: 10%;text-align: left;font-size: 13px;padding: 2px">
															{{$category[$i]}}
														</th>
													</tr>
												<?php } ?>
												
											</thead>
										</table>
									</td>
							<?php $index++ ?>
							@endforeach
								</tr>
							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 5px;padding-right: 5px">
			<button class="btn btn-success" style="font-weight: bold;font-size: 30px;width: 100%;" onclick="saveData('{{$inspector}}')">
				SAVE
			</button>
		</div>
	</div>
		<!-- <div class="col-xs-12" style="margin-top: 15px" id="div_category">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px;height: 620px">
				<div class="box-body">
					
			</div>
		</div> -->





</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var employees = [];
	var count = 0;
	var destinations = [];
	var countDestination = 0;

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		// fillList();

		clearAll();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: '<?php echo $tgl_max ?>'
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
	});

	function checkThorax(param) {
		$('input[name=mcu_group_code]').attr('checked',false);
		if (param == '< 6 Bulan') {
			$("input[name='mcu_group_code'][value='G']").prop("checked", true);
		}else{
			$("input[name='mcu_group_code'][value='C']").prop("checked", true);
		}
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			// if($("#tag").val().length >= 8){
				var data = {
					tag : $("#tag").val(),
					inspector : '{{$inspector}}',
				}
				
				$.get('{{ url("scan/ga_control/mcu/physical") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#tag').prop('disabled',true);
						$('#employee_id').html(result.employees.employee_id);
						$('#name').html(result.employees.name);
						$('#dept').html(result.employees.department_shortname);
						$('#sect').html(result.employees.section);
						$('#groups').html(result.employees.group);
						$('#sub_group').html(result.employees.sub_group);
						var birthDate = new Date(result.employees.birth_date);
						$('#birth_date').html(addZero(birthDate.getDate())+'-'+addZero((birthDate.getMonth()+1))+'-'+birthDate.getFullYear());
						var age = getAge(result.employees.birth_date);
						$('#age').val(age);
						$('#age_th').html(age+' Tahun');
						if (result.employees.gender == 'L') {
							$('#gender').html('Pria');
						}else{
							$('#gender').html('Wanita');
						}
						$('#schedule_id').val(result.mcu.id);
						$('#resume_height').html(result.mcu.height+' cm');
						$('#resume_weight').html(result.mcu.weight+' Kg');
						$('#resume_blood').html(result.mcu.blood_pressure+' mm Hg');
						$('#resume_pulse').html(result.mcu.pulse+' Kali / Menit');
						$('#resume_imt').html(result.mcu.imt);
						$('#resume_color_blind').html(result.mcu.color_blind);
						$('#resume_respiration').html(result.mcu.respiration+' Kali / Menit');
						$('#resume_visus_od').html(result.mcu.visus_od+' - '+result.mcu.visus_od_status);
						$('#resume_visus_os').html(result.mcu.visus_os+' - '+result.mcu.visus_os_status);
						$('#audiometri').html(result.mcu.audiometri);
						$('#chemical').html(result.mcu.chemical);

						$('#grade_code').html(result.employees.grade_code.charAt(0)+' / '+result.employees.position);

						if (result.mcu.audiometri == 'YA') {
							document.getElementById('audiometri').style.backgroundColor = '#bfffd2';
						}else{
							document.getElementById('audiometri').style.backgroundColor = '#ffbfbf';
						}

						if (result.mcu_before != null) {
							$('#mcu_group_code_before').html(result.mcu_before.mcu_group_code);
						}
						audio_ok.play();

						if ('{{$inspector}}' == 'clinic') {
							$('#div_resume').hide();
							$('#div_resume2').hide();
							$('#div_clinic').show();
							$('#div_doctor').hide();
							$('#div_category').hide();
						}else{
							$('#div_resume2').show();
							$('#div_resume').show();
							$('#div_clinic').hide();
							$('#div_doctor').show();
							$('#div_category').show();
							if (result.employees.grade_code.match(/L/gi) || result.employees.grade_code.match(/M/gi) || result.employees.grade_code.match(/D/gi)) {
								$("input[name='mcu_group_code']").each(function (i) {
						            if ($('.mcu_group_code')[i].value == 'B') {
						            	$('.mcu_group_code')[i].checked = true;
						            }
						        });
							}else{
								$("input[name='mcu_group_code']").each(function (i) {
						            $('.mcu_group_code')[i].checked = false;
						        });
							}
						}

						if (result.mcu.chemical == 'YA') {
							$("input[name='mcu_group_code']").each(function (i) {
					            if ($('.mcu_group_code')[i].value == 'C') {
					            	$('.mcu_group_code')[i].checked = true;
					            }
					        });
							document.getElementById('chemical').style.backgroundColor = '#bfffd2';
						}else{
							$("input[name='mcu_group_code']").each(function (i) {
					            $('.mcu_group_code')[i].checked = false;
					        });
							document.getElementById('chemical').style.backgroundColor = '#ffbfbf';
						}
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
					}
				});
			// }
			// else{
			// 	openErrorGritter('Error!', 'Employee ID Invalid.');
			// 	audio_error.play();
			// 	$('#tag').removeAttr('disabled');
			// 	$("#tag").val("");
			// }			
		}
	});

	const getAge = (birthDateString) => {
	  const today = new Date();
	  const birthDate = new Date(birthDateString);

	  const yearsDifference = today.getFullYear() - birthDate.getFullYear();

	  if (
	    today.getMonth() < birthDate.getMonth() ||
	    (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())
	  ) {
	    return yearsDifference - 1;
	  }

	  return yearsDifference;
	};


	$(function () {
		// $('.selectPur').select2({
		// 	dropdownParent: $('#selectPur'),
		// 	allowClear:true
		// });
	});

	function changeImt(bb) {
		if ($('#height').val() != '' && $('#weight').val() != '') {
			var bb = parseFloat($('#weight').val());
			var tb = parseFloat(((parseFloat((parseFloat($('#height').val())/100).toFixed(2)))*parseFloat((parseFloat($('#height').val())/100).toFixed(2))).toFixed(2));

			var imt = (bb / tb).toFixed(2);

			$('#imt').val(imt);
		}
	}

	var mcu_group = JSON.parse($('#mcu_group').val());

	function clearAll() {
		$('#tag').removeAttr('disabled');
		$('#tag').val('');
    	$('#tag').focus();

    	$('#schedule_id').val('');
    	$('#employee_id').html('-');
		$('#name').html('-');
		$('#dept').html('-');
		$('#sect').html('-');
		$('#groups').html('-');
		$('#sub_group').html('-');
		$('#schedule').html('-');
		$('#gender').html('-');
		$('#birth_date').html('-');
		$('#age').val('');
		$('#age_th').html('-');
		$('#mcu_group_code_before').html('-');
		$('#audiometri').html('-');
		$('#chemical').html('-');

		$('#height').val('');
		$('#weight').val('');
		$('#blood_1').val('');
		$('#blood_2').val('');
		$('#pulse').val('');
		$('#imt').val('');
		$('#respiration').val('');
		$('#visus_od_1').val('');
		$('#visus_od_2').val('');
		$('#visus_os_1').val('');
		$('#visus_os_2').val('');

		$('input[name=visus_os]').attr('checked',false);
		$('input[name=visus_od]').attr('checked',false);
		$('input[name=complaint]').attr('checked',false);
		$('input[name=symmetry]').attr('checked',false);
		$('input[name=tht]').attr('checked',false);
		$('input[name=tooth]').attr('checked',false);
		$('input[name=head]').attr('checked',false);
		$('input[name=heart]').attr('checked',false);
		$('input[name=lungs]').attr('checked',false);
		$('input[name=abdomen]').attr('checked',false);
		$('input[name=hepar]').attr('checked',false);
		$('input[name=limbs]').attr('checked',false);
		$('input[name=arm]').attr('checked',false);
		$('input[name=joint]').attr('checked',false);
		$('input[name=skin]').attr('checked',false);
		$('input[name=thorax]').attr('checked',false);
		$('input[name=disease_history]').attr('checked',false);
		$('input[name=mcu_group_code]').attr('checked',false);
		$('input[name=color_blind]').attr('checked',false);

		$('#audiometri').removeAttr('style');
		$('#chemical').removeAttr('style');

		// $("input[name='mcu_group_code']").each(function (i) {
  //           $('.mcu_group_code')[i].checked = false;
  //       });

        $("input[name='visus_os']").each(function (i) {
            $('.visus_os')[i].checked = false;
        });

        $("input[name='visus_od']").each(function (i) {
            $('.visus_od')[i].checked = false;
        });

        $("input[name='color_blind']").each(function (i) {
            $('.color_blind')[i].checked = false;
        });

        $("input[name='complaint']").each(function (i) {
            $('.complaint')[i].checked = false;
        });

        $("input[name='disease_history']").each(function (i) {
            $('.disease_history')[i].checked = false;
        });

        $("input[name='symmetry']").each(function (i) {
            $('.symmetry')[i].checked = false;
        });
		$("input[name='tht']").each(function (i) {
            $('.tht')[i].checked = false;
        });
		$("input[name='tooth']").each(function (i) {
            $('.tooth')[i].checked = false;
        });
		$("input[name='head']").each(function (i) {
            $('.head')[i].checked = false;
        });
		$("input[name='heart']").each(function (i) {
            $('.heart')[i].checked = false;
        });
		$("input[name='lungs']").each(function (i) {
            $('.lungs')[i].checked = false;
        });
		$("input[name='abdomen']").each(function (i) {
            $('.abdomen')[i].checked = false;
        });
		$("input[name='hepar']").each(function (i) {
            $('.hepar')[i].checked = false;
        });

        $("input[name='limbs']").each(function (i) {
            $('.limbs')[i].checked = false;
        });
		$("input[name='arm']").each(function (i) {
            $('.arm')[i].checked = false;
        });
		$("input[name='joint']").each(function (i) {
            $('.joint')[i].checked = false;
        });
		$("input[name='skin']").each(function (i) {
            $('.skin')[i].checked = false;
        });
		$("input[name='thorax']").each(function (i) {
            $('.thorax')[i].checked = false;
        });

		$('#div_resume').hide();
		$('#div_resume2').hide();
		$('#div_clinic').hide();
		$('#div_doctor').hide();
		$('#div_category').hide();

	}

	function checkHamil() {
		var complaint = [];
		$("input[name='complaint']:checked").each(function (i) {
	            complaint[i] = $(this).val();
        });
        if (complaint.join().match(/Hamil/gi)) {
        	$('#div_doctor').hide();
        }else{
        	$('#div_doctor').show();
        }
	}

	function saveData(inspector) {
		$("#loading").show();
		if (inspector == 'clinic') {
			var schedule_id = $('#schedule_id').val();
			var height = $('#height').val();
			var weight = $('#weight').val();
			var blood_1 = $('#blood_1').val();
			var blood_2 = $('#blood_2').val();
			var pulse = $('#pulse').val();
			var imt = $('#imt').val();
			var respiration = $('#respiration').val();
			var visus_od_1 = $('#visus_od_1').val();
			var visus_od_2 = $('#visus_od_2').val();
			var visus_os_1 = $('#visus_os_1').val();
			var visus_os_2 = $('#visus_os_2').val();
			var age = $('#age').val();

			var visus_os = '';
			$("input[name='visus_os']:checked").each(function (i) {
		            visus_os = $(this).val();
	        });

	        var color_blind = '';
			$("input[name='color_blind']:checked").each(function (i) {
		            color_blind = $(this).val();
	        });

	        var visus_od = '';
			$("input[name='visus_od']:checked").each(function (i) {
		            visus_od = $(this).val();
	        });

	        if (height == '' || weight == '' || blood_1 == '' || blood_2 == '' || pulse == '' || respiration == '' || visus_od_1 == '' || visus_od_2 == '' || visus_os_1 == '' || visus_os_2 == '' || visus_os == '' || visus_od == '' || color_blind == '' || imt == '') {
	        	openErrorGritter('Error!','Ada data yang belum terisi.');
	        	audio_error.play();
	        	$("#loading").hide();
	        	return false;
	        }

	        var data = {
	        	inspector:'{{$inspector}}',
	        	schedule_id:schedule_id,
				height:height,
				weight:weight,
				blood_1:blood_1,
				blood_2:blood_2,
				pulse:pulse,
				respiration:respiration,
				imt:imt,
				color_blind:color_blind,
				visus_od_1:visus_od_1,
				visus_od_2:visus_od_2,
				visus_os_1:visus_os_1,
				visus_os_2:visus_os_2,
				visus_os:visus_os,
				visus_od:visus_od,
				age:age,
	        }

	        $.post('{{ url("input/ga_control/mcu/physical") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!',result.message);
					audio_ok.play();
					$("#loading").hide();
					clearAll();
				}else{
					openErrorGritter('Error!',result.messsage);
	        		audio_error.play();
	        		$("#loading").hide();
					return false;
				}
			});
		}else{
			var schedule_id = $('#schedule_id').val();

			var complaint = [];
			$("input[name='complaint']:checked").each(function (i) {
		            complaint[i] = $(this).val();
	        });

	        var disease_history = [];
			$("input[name='disease_history']:checked").each(function (i) {
		            disease_history[i] = $(this).val();
	        });

	        var mcu_group_code = '';
			$("input[name='mcu_group_code']:checked").each(function (i) {
		            mcu_group_code = $(this).val();
	        });

	        if (complaint.join().match(/Hamil/gi)) {
	        	mcu_group_code = 'Tidak Perlu';
	        }

	        var symmetry = '';
			$("input[name='symmetry']:checked").each(function (i) {
		            symmetry = $(this).val();
	        });
			var tht = '';
			$("input[name='tht']:checked").each(function (i) {
		            tht = $(this).val();
	        });
			var tooth = '';
			$("input[name='tooth']:checked").each(function (i) {
		            tooth = $(this).val();
	        });
			var head = '';
			$("input[name='head']:checked").each(function (i) {
		            head = $(this).val();
	        });
			var heart = '';
			$("input[name='heart']:checked").each(function (i) {
		            heart = $(this).val();
	        });
			var lungs = '';
			$("input[name='lungs']:checked").each(function (i) {
		            lungs = $(this).val();
	        });
			var abdomen = '';
			$("input[name='abdomen']:checked").each(function (i) {
		            abdomen = $(this).val();
	        });
			var hepar = '';
			$("input[name='hepar']:checked").each(function (i) {
		            hepar = $(this).val();
	        });

	        var limbs = '';
			$("input[name='limbs']:checked").each(function (i) {
		            limbs = $(this).val();
	        });
	        var arm = '';
			$("input[name='arm']:checked").each(function (i) {
		            arm = $(this).val();
	        });
	        var joint = '';
			$("input[name='joint']:checked").each(function (i) {
		            joint = $(this).val();
	        });
	        var skin = [];
			$("input[name='skin']:checked").each(function (i) {
		            skin[i] = $(this).val();
	        });
	        var thorax = '';
			$("input[name='thorax']:checked").each(function (i) {
		            thorax = $(this).val();
	        });

	        if (mcu_group_code == '' || complaint.length == 0 || disease_history.length == 0) {
	        	openErrorGritter('Error!','Ada data yang belum terisi.');
	        	audio_error.play();
	        	$("#loading").hide();
	        	return false;
	        }
	        var data = {
	        	inspector:'{{$inspector}}',
	        	schedule_id:schedule_id,
				complaint:complaint.join('<br>'),
				disease_history:disease_history.join('<br>'),
				mcu_group_code:mcu_group_code,
				symmetry:symmetry,
				tht:tht,
				tooth:tooth,
				head:head,
				heart:heart,
				lungs:lungs,
				abdomen:abdomen,
				hepar:hepar,
				limbs:limbs,
				arm:arm,
				joint:joint,
				skin:skin.join('<br>'),
				thorax:thorax,
	        }

	        $.post('{{ url("input/ga_control/mcu/physical") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!',result.message);
					audio_ok.play();
					$("#loading").hide();
					clearAll();
				}else{
					openErrorGritter('Error!',result.messsage);
	        		audio_error.play();
	        		$("#loading").hide();
					return false;
				}
			});
		}
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

	function getActualFullDate() {
		var today = new Date();

		var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
		return date;
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}


</script>
@endsection