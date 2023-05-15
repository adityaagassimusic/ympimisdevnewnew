@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { 
		display: none;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	.urgent{
		background-color: red;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 2500; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Mohon tunggu sebentar ...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">					
					<form method="GET" action="{{ url("export/maintenance/list_spk") }}">
						<div class="col-md-4">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-6">
										<div class="form-group">
											<label>SPK Masuk Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="reqFrom" id="reqFrom">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>SPK Masuk Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="reqTo" id="reqTo">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="targetFrom" id="targetFrom">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="targetTo" id="targetTo">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Selesai Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="finFrom" id="finFrom">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Selesai Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="finTo" id="finTo">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Order No</label>
													<input type="text" class="form-control" name="orderNo" id="orderNo" placeholder="Masukkan Order No">
												</div>
											</div>
											<div class="col-md-8">
												<div class="form-group">
													<label>Bagian Pemohon</label>
													<select class="form-control select2" data-placeholder="Pilih Bagian" name="section" id="section" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														@php
														$group = array();
														@endphp
														@foreach($employees as $employee)
														@if(!in_array($employee->section.'-'.$employee->group, $group))
														<option value="{{ $employee->section }}_{{ $employee->group }}">{{ $employee->section }}-{{ $employee->group }}</option>
														@php
														array_push($group, $employee->section.'-'.$employee->group);
														@endphp
														@endif
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="row">

											<div class="col-md-4">
												<div class="form-group">
													<label>Prioritas</label>
													<select class="form-control select2" data-placeholder="Pilih Prioritas" name="priority" id="priority" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="normal">Normal</option>
														<option value="urgent">Urgent</option>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Jenis Pekerjaan</label>
													<select class="form-control select2" data-placeholder="Pilih Jenis Pekerjaan" name="workType" id="workType" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="Perbaikan">Perbaikan</option>
														<option value="Pemasangan">Pemasangan</option>
														<option value="Pelepasan">Pelepasan</option>
														<option value="Penggantian">Penggantian</option>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>PIC</label>
													<select class="form-control select2" data-placeholder="Pilih PIC" name="pic" id="pic" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														@foreach($mt_employees as $pic)
														<option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Kategori</label>
													<select class="form-control select2" data-placeholder="Pilih kategori" name="categoryStatus" id="categoryStatus" style="width: 100% height: 35px; font-size: 15px;">
														<option></option>
														<option value="all">All</option>
														@foreach($category as $ctg)
														<option value="{{ $ctg }}">{{ $ctg }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Progres</label>
													<select class="form-control select2" data-placeholder="Pilih Progres" name="remark" id="remark" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="all">All</option>
														@foreach($statuses as $status)
														<option value="{{ $status->process_code }}">{{ $status->process_name }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Status</label>
													<select class="form-control select2" data-placeholder="Pilih Status Pending" name="status" id="status" style="width: 100% height: 35px; font-size: 15px;">
														<option value=""></option>
														<option value="-">-</option>
														<option value="Part Tidak Ada">Part Tidak Ada</option>
														<option value="Vendor">Proyek Vendor</option>
														<option value="WJO">Menunggu WJO</option>
														<option value="Call Friend">Call Friend</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="location.reload()" class="btn btn-danger">Clear</a>
								<button type="submit" class="btn btn-success"><i class="fa fa-download"></i> Excel</button>
								<a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
							</div>
						</div>
					</form>
					<div class="col-md-12" style="overflow-x: auto;">
						<table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">SPK</th>
									<th style="width: 1%;">Tanggal Masuk</th>
									<th style="width: 1%;">Prioritas</th>
									<th style="width: 1%;">Nama Mesin</th>
									<th style="width: 1%;">Pemohon</th>
									<th style="width: 1%;">Bagian</th>
									<th style="width: 1%;">Kategori</th>
									<th style="width: 1%;">Deskripsi</th>
									<th style="width: 1%;">Progress</th>
									<th style="width: 1%;">Target Selesai</th>
									<th style="width: 1%;">PIC</th>
								</tr>
							</thead>
							<tbody id="tableBodyList">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="detailModal" style="color: black;" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Detail SPK</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="spk_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Nama Pengaju</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="pengaju_detail" readonly>
								</div>
							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="tanggal_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="bagian_detail" readonly>
								</div>
							</div>
						</div>
						<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px"></div>
						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
								<div class="col-xs-7" align="left">
									<span style="font-size: 13px;" class="label" id="prioritas_detail"></span>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Jenis Pekerjaan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="workType_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Reason Urgent</label>
								<div class="col-xs-7" align="left">
									<textarea class="form-control" id="urgent_reason_detail" rows="1" readonly></textarea>
								</div>
							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Kategori</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="kategori_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Kondisi Mesin</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="mesin_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Potensi Bahaya</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="bahaya_detail" readonly>
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Nama Mesin</label>
								<div class="col-xs-10" align="left">
									<input type="text" class="form-control" id="nama_mesin" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Uraian Permintaan</label>
								<div class="col-xs-10" align="left">
									<textarea class="form-control" id="uraian_detail"></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Lampiran</label>
								<div class="col-xs-10" align="left" id="lampiran_detail">
									
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Catatan Keamanan</label>
								<div class="col-xs-8" align="left">
									<textarea class="form-control" id="keamanan_detail" rows="1" readonly></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Tanggal Target</label>
								<div class="col-xs-3" align="left">
									<div class="input-group date">
										<div class="input-group-addon bg-default">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control" id="target_detail" readonly>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="form-group row" align="right" id="prog" style="display: none"></div>
							<div class="form-group row" align="right" id="pending" style="display: none"></div>
						</div>

						<div class="col-xs-12" id="open_session" style="display: none">
							<!-- <div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Reason Open<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" placeholder="Isikan Catatan SPK Open" id="reason_open">
								</div>
							</div> -->

							<div class="col-xs-4 col-xs-offset-4 class_pic_ubah">
								<center><h2>UBAH PIC</h2></center>
								<select class="form-control input-lg" data-placeholder="Pilih PIC"  id="pic_ubah" style="width: 100%">
									<option value=""></option>
									@foreach($mt_employees as $pic)
									<option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="col-xs-12" style="padding-top: 1%">
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th width="6%">ID PIC</th>
											<th>Nama PIC</th>
											<th width="25%">Plan Mulai</th>
											<th width="25%">Plan Selesai</th>
											<th width="1%">Opsi</th>
										</tr>
									</thead>
									<tbody id="pic_member2"></tbody>
								</table>
							</div>
							<div class="col-xs-12">
								<button type="button" class="btn btn-success pull-right" onclick="replace_member()"><i class="fa fa-save"></i> Simpan</button>
							</div>

							<!-- <button class="btn btn-success pull-right" id="btn_open" onclick="open_spk()">OPEN SPK</button> -->
						</div>

						<div class="col-xs-12" style="display: none" id="pending_vendor">
							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Nama Vendor<span class="text-red">*</span></label>
								<div class="col-xs-6">
									<input type="text" class="form-control" placeholder="Isikan Nama Vendor Terkait" id="vendor_name">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Nomor PO<span class="text-red">*</span></label>
								<div class="col-xs-3">
									<input type="text" class="form-control" placeholder="Isikan Nomor PO Vendor" id="vendor_po">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Plan<span class="text-red">*</span></label>
								<div class="col-xs-2">
									<input type="text" class="form-control" placeholder="Plan Start Kerja" id="vendor_start">
								</div>

								<div class="col-xs-1" style="padding: 0px; text-align: center;">&rarr;</div>

								<div class="col-xs-2">
									<input type="text" class="form-control" placeholder="Plan Finish Kerja" id="vendor_finish">
								</div>
							</div>

							<div class="form-group row" align="right">
								<div class="col-xs-12">
									<button class="btn btn-success pull-right" id="vendor_close" style="margin-right: 20px" onclick="close_vendor()"><i class="fa fa-check"></i> Close SPK</button>
								</div>
							</div>
						</div>

						<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px"></div>
						<div id="approval">
							<div class="col-xs-12">
								<button type="button" class="btn btn-danger pull-left" onclick="approval('0')"><i class="fa fa-close"></i> Reject</button>

								<button type="close" class="btn btn-success pull-right" onclick="approval('1')"><i class="fa fa-check"></i> Approve</button>
							</div>
						</div>

						<div id="pilih_pic">
							<div class="col-xs-12">
								<div class="col-xs-4 col-xs-offset-4 class_pic_detail">
									<center><h2>PILIH PIC</h2></center>
									<select class="form-control input-lg" data-placeholder="Pilih PIC"  id="pic_detail" style="width: 100%">
										<option value=""></option>
										@foreach($mt_employees as $pic)
										<option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-xs-12" style="padding-top: 1%">
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th width="6%">ID PIC</th>
											<th>Nama PIC</th>
											<th width="25%">Plan Mulai</th>
											<th width="25%">Plan Selesai</th>
											<th width="1%">Opsi</th>
										</tr>
									</thead>
									<tbody id="pic_member"></tbody>
								</table>
							</div>
							<div class="col-xs-12">
								<button type="button" class="btn btn-danger pull-left" onclick="reject()"><i class="fa fa-close"></i> Reject</button>
								<textarea class="form-control pull-left" placeholder='Isikan Alasan Reject' id="reject_reason" style="width: 200px; height: 40px; margin-left: 5px"></textarea>
								<button type="button" class="btn btn-success pull-right" onclick="job_ok()"><i class="fa fa-save"></i> Simpan</button>
							</div>
						</div>

						<div id="report_list">
							<div class="col-xs-12">
								<div class="col-xs-4 col-xs-offset-4">
									<center><h2>Report</h2></center>
								</div>

								<div class="col-xs-12">
									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Penyebab</label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="penyebab_detail" rows="1" readonly></textarea>
										</div>
										<div class="col-xs-1"><img src="#" width="100%" alt="photo" id="penyebab_photo_1"></div>
										<div class="col-xs-1"><img src="#" width="100%" alt="photo" id="penyebab_photo_2"></div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Penanganan</label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="penanganan_detail" rows="1" readonly></textarea>
										</div>
										<div class="col-xs-1"><img src="#" width="100%" alt="photo" id="penanganan_photo_1"></div>
										<div class="col-xs-1"><img src="#" width="100%" alt="photo" id="penanganan_photo_2"></div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Pencegahan</label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="pencegahan_detail" rows="1" readonly></textarea>
										</div>
										<div class="col-xs-1"><img src="#" width="100%" alt="photo" id="pencegahan_photo_1"></div>
										<div class="col-xs-1"><img src="#" width="100%" alt="photo" id="pencegahan_photo_2"></div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Sparepart</label>
										<div class="col-xs-8" align="left" id="spare_detail">
											<center>
												<table class="table">
													<thead>
														<tr>
															<th>Item</th>
															<th>Qty</th>
														</tr>
													</thead>
													<tbody id="part_detail">
													</tbody>
												</table>
											</center>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Foto</label>
										<div class="col-xs-8" align="left" id="img_detail">
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="detail">
							<div class="col-xs-12">
								<button type="button" class="btn btn-primary pull-right" onclick="update_uraian()"><i class="fa fa-save"></i> Update</button>
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

	var counter = 1;
	var pic_member = [];
	var pic_ubah_member = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		var opt = $("#sub_section option").sort(function (a,b) { return a.value.toUpperCase().localeCompare(b.value.toUpperCase()) });
		$("#sub_section").append(opt);
		$('#sub_section').prop('selectedIndex', 0).change();

		fillTable();

		$('.select2').select2({
			allowClear: true
		});
		
	});

	$(function () {
		$('#pic_detail').select2({
			// dropdownAutoWidth : true,
			dropdownParent: $(".class_pic_detail")
		});

		$('#pic_ubah').select2({
			// dropdownAutoWidth : true,
			dropdownParent: $(".class_pic_ubah")
		});

		$('#vendor_start').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		
		$('#vendor_finish').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#reqFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#reqTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	})



	function fillTable() {
		var reqFrom = $('#reqFrom').val();
		var reqTo = $('#reqTo').val();
		var targetFrom = $('#targetFrom').val();
		var targetTo = $('#targetTo').val();
		var finFrom = $('#finFrom').val();
		var finTo = $('#finTo').val();
		var orderNo = $('#orderNo').val();
		var section = $('#section').val();
		var workType = $('#workType').val();
		var remark = $('#remark').val(); 
		var status = $('#status').val();
		var ctg = $('#categoryStatus').val();
		var pic = $('#pic').val();

		var data = {
			reqFrom:reqFrom,
			reqTo:reqTo,
			targetFrom:targetFrom,
			targetTo:targetTo,
			finFrom:finFrom,
			finTo:finTo,
			orderNo:orderNo,
			section:section,
			workType:workType,
			remark:remark,
			status:status,
			category:ctg,
			pic:pic
		}

		$.get('{{ url("fetch/maintenance/list_spk") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				$.each(result.tableData ,function(index, value){
					click = "";
					if (value.remark == "0" || value.remark == "2" || value.remark == "3" || value.remark == "4" || value.remark == "5" || value.remark == "6") {
						click = "onclick='showJobModal(\""+value.order_no+"\")'";
					}

					tableData += "<tr "+click+">";
					tableData += "<td>"+value.order_no+"</td>";
					tableData += "<td>"+value.date+"</td>";
					
					if(value.priority == 'Urgent'){
						var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
					}else{
						var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
					}
					tableData += "<td>"+priority+"</td>";

					tableData += "<td>"+(value.machine_desc || '')+"</td>";
					tableData += "<td>"+value.requester+"</td>";
					tableData += "<td>"+value.section+"</td>";
					tableData += "<td>"+(value.category || '-')+"</td>";
					tableData += "<td>"+value.description+"</td>";
					tableData += "<td>"+value.process_name+"</td>";
					tableData += "<td>"+(value.target_date || '-')+"</td>";
					tableData += "<td>"+(value.operator || '-')+"</td>";
					tableData += "</tr>";
				})

				$('#tableBodyList').append(tableData);

				var table = $('#tableList').DataTable({
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
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					"order": [[ 2, 'desc' ]]
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	$('#pic_detail').on('change', function() {
		var val = $('#pic_detail').val();
		var stat = 0;

		if (val != "") {
			if (pic_member.length > 0) {
				$.each(pic_member, function(index, value){
					if (value == val) {
						stat = 1;
					}
				})
			}

			if (stat == 0) {
				pic_member.push(val);
				pilih();
			} else {
				openErrorGritter('Error!', "PIC Sudah berada pada list");
			}
			$("#pic_detail").prop('selectedIndex', 0).change();
		}
	});

	function pilih() {
		var employee_id = $("#pic_detail").val();
		var employee_name = $("#pic_detail option:selected").html();
		body = "";

		body += "<tr id='"+employee_id+"' class = 'member'>";
		body += "<td id='operator_"+employee_id+"'>"+employee_id+"</td>";
		body += "<td>"+employee_name+"</td>";

		body += "<td>";
		body += "<div class='input-group'>";
		body += "<input type='text' class='form-control datepicker' id='start_date_"+employee_id+"' placeholder='Start Date' style='width:60%'>";
		body += "<input type='text' class='form-control timepicker' id='start_time_"+employee_id+"' placeholder='Start Time' style='width:40%'>";
		body += "</div>";
		body += "</td>";

		body += "<td>";
		body += "<div class='input-group'>";
		body += "<input type='text' class='form-control datepicker' id='finish_date_"+employee_id+"' placeholder='Finish Date' style='width:60%'>";
		body += "<input type='text' class='form-control timepicker' id='finish_time_"+employee_id+"' placeholder='Finish Time' style='width:40%'>";
		body += "</div>";
		body += "</td>";
		body += "<td><button class='btn btn-danger' onClick='delete2(this)'><i class='fa fa-close'></i></button></td>";
		body += "</tr>";

		counter++;

		$("#pic_member").append(body);

		var date = new Date();

		var year  = pad(date.getFullYear());
		var month = pad(date.getMonth() + 1);
		var day   = pad(date.getDate());

		var yyyymmdd = year +"-"+ month +"-"+ day;

		$('.datepicker').val(yyyymmdd);


		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});


		$('.timepicker').timepicker({
			use24hours: true,
			showInputs: false,
			showMeridian: false,
			minuteStep: 5,
			defaultTime: '00:00',
			timeFormat: 'hh:mm'
		})
	}

	function delete2(elem) {
		$(elem).closest('tr').remove();

		var id = $(elem).closest('tr').attr('id');;
		pic_member = $.grep(pic_member, function(value) {
			return value != id;
		});
	}

	function showJobModal(order_no) {
		$("#pic_member").empty();
		$("#pic_member2").empty();
		counter = 1;
		pic_member = [];
		pic_ubah_member = [];

		var data = {
			order_no : order_no
		}

		$.get('{{ url("fetch/maintenance/detail") }}', data,  function(result, status, xhr){
			$("#detailModal").modal("show");
			$("#spk_detail").val(result.detail[0].order_no);
			$("#pengaju_detail").val(result.detail[0].name);
			$("#tanggal_detail").val(result.detail[0].date);
			$("#bagian_detail").val(result.detail[0].section);

			if (result.detail[0].priority == "Normal") {
				$("#prioritas_detail").removeClass("label-danger");
				$("#prioritas_detail").addClass("label-default");
			} else {
				$("#prioritas_detail").removeClass("label-default");
				$("#prioritas_detail").addClass("label-danger");
			}
			$("#prioritas_detail").text(result.detail[0].priority);

			$("#workType_detail").val(result.detail[0].type);
			$("#urgent_reason_detail").val(result.detail[0].note);
			$("#kategori_detail").val(result.detail[0].category);
			$("#mesin_detail").val(result.detail[0].machine_condition);
			$("#bahaya_detail").val(result.detail[0].danger);

			if (result.detail[0].machine_name == 'Lain - lain') {
				$("#nama_mesin").val(result.detail[0].machine_name+"  _  "+result.detail[0].machine_remark);
			} else {
				$("#nama_mesin").val(result.detail[0].machine_desc);
			}

			$("#uraian_detail").val(result.detail[0].description);
			$("#keamanan_detail").val(result.detail[0].safety_note);
			$("#target_detail").val(result.detail[0].target_date);
			$("#lampiran_detail").empty();
			$("#lampiran_detail").append("<a href='{{ url('maintenance/spk_att/') }}/"+result.detail[0].att+"' target='_blank'>"+result.detail[0].att+"</a>");

			$("#penyebab_detail").val((result.detail[0].cause || ""));
			$("#penanganan_detail").val((result.detail[0].handling || ""));
			$("#pencegahan_detail").val((result.detail[0].prevention || ""));


			if (result.detail[0].cause_photo) {
				penyebab_foto = result.detail[0].cause_photo.split(', ');
				$("#penyebab_photo_1").attr("src", "{{url('maintenance/spk_report')}}/"+penyebab_foto[0]);
				if (penyebab_foto[1]) {
					$("#penyebab_photo_2").attr("src", "{{url('maintenance/spk_report')}}/"+penyebab_foto[0]);
				}
			}

			if (result.detail[0].handling_photo) {
				penanganan_foto = result.detail[0].handling_photo.split(', ');
				$("#penanganan_photo_1").attr("src", "{{url('maintenance/spk_report')}}/"+penanganan_foto[0]);
				if (penanganan_foto[1]) {
					$("#penyebab_photo_2").attr("src", "{{url('maintenance/spk_report')}}/"+penanganan_foto[1]);
				}
			}

			if (result.detail[0].prevention_photo) {
				pencegahan_foto = result.detail[0].prevention_photo.split(', ');
				$("#pencegahan_photo_1").attr("src", "{{url('maintenance/spk_report')}}/"+pencegahan_foto[0]);
				if (pencegahan_foto[1]) {
					$("#penyebab_photo_2").attr("src", "{{url('maintenance/spk_report')}}/"+pencegahan_foto[1]);
				}
			}

			$("#prog").empty();
			$("#pending").empty();

			if (result.detail[0].process_name == "Pending" && result.detail[0].status == "Vendor") {
				$("#open_session").hide();
				$("#pending_vendor").show();
			} else if(result.detail[0].process_name == "Pending" && result.detail[0].status != "Vendor") {
				$("#open_session").show();
			} else {
				$("#open_session").hide();
			}

			if (result.detail[0].process_name == "InProgress" || result.detail[0].process_name == "Finished" || result.detail[0].process_name == "Pending") {
				$("#prog").show();

				var progress = "";
				var pending = "";

				progress += '<label class="col-xs-2" style="margin-top: 1%;">Progress</label>';
				pending += '<label class="col-xs-2" style="margin-top: 1%;">Pending</label>';

				$("#img_detail").empty();
				
				$.each(result.detail ,function(index, value){
					col = "";
					var photo = "";
					arr_photo = [];
					var img = "";

					if (index > 0) {
						col = "col-xs-offset-2";
					}

					progress += '<div class="col-xs-3 '+col+'" align="left"><input type="text" name="prog_name_'+index+'" id="prog_name_'+index+'" class="form-control" value="'+value.name_op+'" readonly></div>';
					progress += '<div class="col-xs-2" align="left" style="padding-right: 0px"><input type="text" name="prog_start_'+index+'" id="prog_start_'+index+'"class="form-control" value="'+value.start_actual+'" readonly></div>';
					progress += '<div class="col-xs-1" style="padding: 0px"><center><b> &rarr; </b></center></div>';
					progress += '<div class="col-xs-2" align="left" style="padding-left: 0px"><input type="text" name="prog_finish_'+index+'" name="prog_finish_'+index+'" class="form-control" value="'+(value.finish_actual || '' )+'" readonly></div>';

					photo = value.photo;


					if (photo != undefined) {

						arr_photo = photo.split(', ');

						$.each(arr_photo,function(index2, value2){
							if (value2.substring(11, 20).toLowerCase() == value.id_op.toLowerCase()) {
								img += '<img src="{{ url("maintenance/spk_report") }}/'+value2+'" id="img_detail_'+(index2+1)+'" style="width: 30%; margin-right: 10px">';
							}
						})
					}

					$("#img_detail").append(img);
				})

				var part = "";
				$("#part_detail").empty();

				if (result.part) {
					$.each(result.part, function(index, value){
						part += "<tr>";
						part += "<td>"+value.part_name+" - "+value.specification+"</td>";
						part += "<td>"+value.quantity+"</td>";
						part += "</tr>";
					})

					$("#part_detail").append(part);
				}


				$("#prog").append(progress);

				if (result.detail[0].process_name == "Pending") {
					$("#pilih_pic").hide();
					pending += '<div class="col-xs-2" align="left"><input type="text" name="pending_status" id="pending_status" class="form-control" value="'+result.detail[0].status+'" readonly></div>';
					pending += '<div class="col-xs-6" align="left" style="padding-right: 0px"><input type="text" name="pending_desc" id="pending_desc" class="form-control" value="'+result.detail[0].pending_desc+" | "+result.detail[0].pending_remark+'" readonly></div>';

					$("#pending").show();
					$("#report_list").hide();
					$("#approval").hide();
					$("#pending").append(pending);
				} else if (result.detail[0].process_name == "Finished") {
					$("#approval").hide();
					$("#pilih_pic").hide();
					$("#report_list").show();
				} else if (result.detail[0].process_name == "InProgress") {
					$("#prog").show();
					$("#approval").hide();
					$("#report_list").hide();
					$("#pilih_pic").hide();
					$("#pending").hide();
				} else {
					$("#approval").hide();
					$("#pilih_pic").hide();
					$("#report_list").hide();
					$("#prog").hide();
				}
			} else if (result.detail[0].process_name == "Received") {
				$("#prog").hide();
				$("#pilih_pic").show();
				$("#report_list").hide();
				$("#approval").hide();
			}
			else if (result.detail[0].process_name == "Requested") {
				$("#approval").show();
				$("#prog").hide();
				$("#pilih_pic").hide();
				$("#report_list").hide();
			} else {
				$("#prog").hide();
				$("#report_list").hide();
				$("#approval").hide();
			}

		})
}

function job_ok() {
	var order_no = $("#spk_detail").val();
	var arr_member = [];

	$('.member').each(function(index, value) {
		var ids = $(this).attr('id');

		arr_member.push({
			'operator': $('#operator_'+ids).text(),
			'start_date': $('#start_date_'+ids).val(),
			'start_time': format_two_digits($('#start_time_'+ids).val().split(':')[0])+":"+ $('#start_time_'+ids).val().split(':')[1],
			'finish_date': $('#finish_date_'+ids).val(),
			'finish_time': format_two_digits($('#finish_time_'+ids).val().split(':')[0])+":"+ $('#finish_time_'+ids).val().split(':')[1]
		});
	});

	var data = {
		order_no : order_no,
		member : arr_member
	}

	$("#loading").show();
	$.post('{{ url("post/maintenance/member") }}', data,  function(result, status, xhr){
		$("#pic_member").empty();
		$("#detailModal").modal('hide');
		fillTable();

		$("#loading").hide();
		openSuccessGritter("Success", "SPK Berhasil Ditugaskan");
	})
}

function replace_member() {
	var order_no = $("#spk_detail").val();
	var arr_member = [];

	$('.member2').each(function(index, value) {
		var ids = $(this).attr('id');

		arr_member.push({
			'operator': $('#ubah_operator_'+ids).text(),
			'start_date': $('#ubah_start_date_'+ids).val(),
			'start_time': format_two_digits($('#ubah_start_time_'+ids).val().split(':')[0])+":"+ $('#ubah_start_time_'+ids).val().split(':')[1],
			'finish_date': $('#ubah_finish_date_'+ids).val(),
			'finish_time': format_two_digits($('#ubah_finish_time_'+ids).val().split(':')[0])+":"+ $('#ubah_finish_time_'+ids).val().split(':')[1],	
		});
	});

	var data = {
		order_no : order_no,
		member : arr_member,
		uraian : $("#uraian_detail").val()
	}

	$.post('{{ url("post/maintenance/member/change") }}', data,  function(result, status, xhr){
		$("#pic_member2").empty();
		$("#detailModal").modal('hide');
		fillTable();
		openSuccessGritter("Success", "SPK Berhasil Ditugaskan");
	})
}

// function open_spk() {
// 	if ($("#reason_open").val() == "") {
// 		openErrorGritter('Fail', 'Kolom Catatan Harus diisi');
// 		return false;
// 	}

// 	var data = {
// 		order_no : $("#spk_detail").val(),
// 		reason : $("#reason_open").val()
// 	}

// 	$.post('{{ url("post/maintenance/spk/open") }}', data,  function(result, status, xhr){
// 		openSuccessGritter("Success", "SPK Berhasil Ditugaskan");
// 	})
// }

function approval(param) {
	var data = {
		stat : param,
		order_no : $("#spk_detail").val()
	}
	
	$.get('{{ url("verify/maintenance/spk/approve_urgent") }}', data,  function(result, status, xhr){
		if (result.status) {
			openSuccessGritter("Success", "SPK Berhasil Disetujui");
			$("#detailModal").modal('hide');
			fillTable();
		} else {
			openErrorGritter("Error", result.message);
		}
	})
}

$('#pic_ubah').on('change', function() {
	var val = $('#pic_ubah').val();
	var stat = 0;

	if (val != "") {
		if (pic_ubah_member.length > 0) {
			$.each(pic_ubah_member, function(index, value){
				if (value == val) {
					stat = 1;
				}
			})
		}

		if (stat == 0) {
			pic_ubah_member.push(val);
			pilih_ubah();
		} else {
			openErrorGritter('Error!', "PIC Sudah berada pada list");
		}
		$("#pic_ubah").prop('selectedIndex', 0).change();
	}
});

function pilih_ubah() {
	var employee_id = $("#pic_ubah").val();
	var employee_name = $("#pic_ubah option:selected").html();
	body = "";

	body += "<tr id='"+employee_id+"' class = 'member2'>";
	body += "<td id='ubah_operator_"+employee_id+"'>"+employee_id+"</td>";
	body += "<td>"+employee_name+"</td>";

	body += "<td>";
	body += "<div class='input-group'>";
	body += "<input type='text' class='form-control datepicker' id='ubah_start_date_"+employee_id+"' placeholder='Start Date' style='width:60%'>";
	body += "<input type='text' class='form-control timepicker' id='ubah_start_time_"+employee_id+"' placeholder='Start Time' style='width:40%'>";
	body += "</div>";
	body += "</td>";

	body += "<td>";
	body += "<div class='input-group'>";
	body += "<input type='text' class='form-control datepicker' id='ubah_finish_date_"+employee_id+"' placeholder='Finish Date' style='width:60%'>";
	body += "<input type='text' class='form-control timepicker' id='ubah_finish_time_"+employee_id+"' placeholder='Finish Time' style='width:40%'>";
	body += "</div>";
	body += "</td>";
	body += "<td><button class='btn btn-danger' onClick='delete3(this)'><i class='fa fa-close'></i></button></td>";
	body += "</tr>";

	counter++;

	$("#pic_member2").append(body);

	var date = new Date();

	var year  = pad(date.getFullYear());
	var month = pad(date.getMonth() + 1);
	var day   = pad(date.getDate());

	var yyyymmdd = year +"-"+ month +"-"+ day;

	$('.datepicker').val(yyyymmdd);


	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true
	});

	$('.timepicker').timepicker({
		use24hours: true,
		showInputs: false,
		showMeridian: false,
		minuteStep: 5,
		defaultTime: '00:00',
		timeFormat: 'hh:mm'
	})
}

function delete3(elem) {
	$(elem).closest('tr').remove();

	var id = $(elem).closest('tr').attr('id');;
	pic_ubah_member = $.grep(pic_ubah_member, function(value) {
		return value != id;
	});
}

function format_two_digits(n) {
	return n < 10 ? '0' + n : n;
}

function pad(numb) {
	return (numb < 10 ? '0' : '') + numb;
}

function close_vendor() {
	var spk_number = $("#spk_detail").val();
	var vendor_name = $("#vendor_name").val();
	var vendor_po = $("#vendor_po").val();

	var vendor_start = $("#vendor_start").val();
	var vendor_finish = $("#vendor_finish").val();

	var data = {
		spk_number : spk_number,
		vendor_name : vendor_name,
		vendor_po : vendor_po,
		vendor_start : vendor_start,
		vendor_finish : vendor_finish
	}

	$.post('{{ url("post/maintenance/spk/pending/vendor/action") }}', data,  function(result, status, xhr){
		if (result.status) {
			openSuccessGritter('Success', 'Success Close SPK by Vendor');
			$("#detailModal").modal('hide');
			fillTable();
		} else {
			openErrorGritter('Error','');
		}
	})
}

function reject() {
	if ($("#reject_reason").val() == '') {
		openErrorGritter('Error', 'Lengkapi Alasan Reject');
		return false;
	}

	var data = {
		order_no : $("#spk_detail").val(),
		reason : $("#reject_reason").val()
	}

	$.post('{{ url("post/maintenance/spk/reject") }}', data,  function(result, status, xhr){
		if (result.status) {
			openSuccessGritter('Success', 'Success Reject SPK');
			$("#detailModal").modal('hide');
			fillTable();
		} else {
			openErrorGritter('Error', '');
		}
	})
}

function update_uraian() {
	$("#loading").show();
	var data = {
		uraian : $("#uraian_detail").val(),
		order_no : $("#spk_detail").val()
	}

	$.post('{{ url("update/maintenance/spk/") }}', data,  function(result, status, xhr){
		if (result.status) {
			$("#loading").hide();
			openSuccessGritter('Success', 'Success Update SPK');
			$("#detailModal").modal('hide');
			fillTable();
		} else {
			$("#loading").hide();
			openErrorGritter('Error', '');
		}
	})
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
