@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
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
		padding: 5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}

	input.currency {
		text-align: left;
		padding-right: 15px;
	}

	.input-group-addon {
		padding: 6px 6px;
	}

	.table > tbody > tr > th {
		background-color: rgba(126,86,134,.7);
		border-color: rgba(126,86,134,.7);
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
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Buat {{ $page }}</a>
		</li>
	</ol>
</section>
@endsection

@section('content')
<section class="content" style="margin-top: 20px">
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

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	    <p style="position: absolute; color: White; top: 45%; left: 35%;">
	      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
	    </p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Filter <span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="col-xs-12">
						<div class="col-md-3">
							<div class="form-group">
								<label>Tanggal Pengajuan</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Tanggal Pengajuan Sampai</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-header">
							</div>
							<div class="box-body" style="padding-top: 0;">
								<table id="prTable" class="table table-bordered table-striped table-hover">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%">Nomor PR</th>
											<th style="width: 1%">Tanggal</th>
											<th style="width: 2%">User</th>
											<th style="width: 1%">Att</th>
											<th style="width: 6%">Note</th>
											<th style="width: 1%">Status</th>
											<th style="width: 3%">Action</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<form id="importForm" name="importForm" method="post" action="{{ url('canteen/create/purchase_requisition') }}" enctype="multipart/form-data">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal fade" id="modalCreate">
			<div class="modal-dialog modal-lg" style="width: 1300px">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Create Purchase Requisition</h4>
						<br>
						<div class="nav-tabs-custom tab-danger">
							<ul class="nav nav-tabs">
								<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Informasi PR</a></li>
								<li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Informasi Budget PR</a></li>
								<li class="vendor-tab disabledTab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Detail Item PR</a></li>
							</ul>
						</div>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-6">
											<div class="form-group">
												<label>Identitas<span class="text-red">*</span></label>
												<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
												<input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}">
												<input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
											</div>
											<div class="form-group">
												<label>Departemen<span class="text-red">*</span></label>
												<input type="text" class="form-control" value="{{$employee->department}} {{$employee->section}}" readonly="">
												<input type="hidden" id="department" name="department" value="{{$employee->department}}">
												<input type="hidden" id="section" name="section" value="{{$employee->section}}">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Tanggal Pengajuan<span class="text-red">*</span></label>
												<div class="input-group date">
													<div class="input-group-addon">	
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" class="form-control pull-right" id="sd" name="sd" value="<?= date('d F Y')?>" disabled="">
													<input type="hidden" class="form-control pull-right" id="submission_date" name="submission_date" value="<?= date('Y-m-d')?>" readonly="">
												</div>
											</div>
											<div class="form-group">
												<label>Nomor PR<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="no_pr" name="no_pr" readonly="">
											</div>											
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label>Catatan / Keterangan Pendukung PR (Optional)</label>
												<textarea class="form-control pull-right" id="note" name="note"></textarea>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label>File Terlampir (Optional)</label>
												<input type="file" id="reportAttachment" name="reportAttachment[]" multiple="">
											</div>
										</div>

									</div>
									<div class="col-md-12" style="padding-right: 30px;padding-top: 10px">
										<a class="btn btn-primary btnNext pull-right">Selanjutnya</a>
									</div>

								</div>
							</div>
							<div class="tab-pane" id="tab_2">
								<div class="row">
									<div class="col-md-12">
										<div id="budgetket">
											<div class="col-xs-12">
												<div class="form-group">
													<b>Detail Budget</b> 
												</div>
											</div>

											<div class="col-xs-12">

												<table class="table" style="border:none">
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Nomor Budget</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_no_text" name="budget_no_text"></label>
															<input type="hidden" id="budget_no" name="budget_no">
														</td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Deskripsi</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_description" name="budget_description"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Nama Akun</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_account" name="budget_account"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Kategori</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_category" name="budget_category"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Budget Awal</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_amount" name="budget_amount"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Sisa Budget Tahunan <span class="periode"></td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_sisa_tahun" name="budget_sisa_tahun"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 18%">Sisa Budget Bulanan <span class="periode"></td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 80%"><label id="budget_sisa" name="budget_sisa"></label></td>
													</tr>
												</table>

											</div>

										</div>
									</div>
									<div class="col-md-12">
										<a class="btn btn-primary btnNext2 pull-right">Selanjutnya</a>
										<span class="pull-right">&nbsp;</span>
										<a class="btn btn-info btnPrevious pull-right">Kembali</a>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="tab_3">
								<div class="row">
									<div class="col-md-12" style="margin-bottom : 5px">
										<div class="col-xs-1" style="padding:5px;">
											<b>Kode Item</b>
										</div>
										<div class="col-xs-4" style="padding:5px;">
											<b>Deskripsi</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Tgl Dtg</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Mata Uang</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Harga</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Jumlah</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>UOM</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Total</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Aksi</b>
										</div>

										<input type="text" name="lop" id="lop" value="1" hidden>
										<div class="col-xs-1" style="padding:5px;">
											<select class="form-control select4" data-placeholder="Choose Item" name="item_code1" id="item_code1" style="width: 100% height: 35px;" onchange="pilihItem(this)">
											</select>
										</div>
										<div class="col-xs-4" style="padding:5px;">
											<input type="text" class="form-control" id="item_desc1" name="item_desc1" placeholder="Description" required="">
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar" style="font-size: 10px"></i>
												</div>
												<input type="text" class="form-control pull-right datepicker" id="req_date1" name="req_date1" placeholder="Tanggal" required="">
											</div>
										</div>

										<div class="col-xs-1" style="padding: 5px">
											<select class="form-control select2" id="item_currency1" name="item_currency1" data-placeholder='Currency' style="width: 100%" onchange="currency(this)">
												<option value="">&nbsp;</option>
												<option value="IDR">IDR</option>											</select>
											<input type="text" class="form-control" id="item_currency_text1" name="item_currency_text1" style="display:none">
										</div>

										<div class="col-xs-1" style="padding:5px;">

											<div class="input-group"> 
												<span class="input-group-addon" id="ket_harga1" name="ket_harga1" style="padding:3px">?</span>
												<input type="text" class="form-control currency" id="item_price1" name="item_price1" placeholder="Harga" data-number-to-fixed="2" data-number-stepfactor="100"required="" style="padding: 6px 6px">
											</div>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<input type="text" class="form-control" id="qty1" name="qty1" placeholder="Qty" required="" onkeyup='getTotal(this.id)'>
											<input type="hidden" class="form-control" id="moq1" name="moq1" placeholder="Moq">
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<select class="form-control select7" id="uom1" name="uom1" data-placeholder="UOM" style="width: 100%;">
												<option></option>
												@foreach($uom as $um)
												<option value="{{ $um }}">{{ $um }}</option>
												@endforeach
											</select>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<input type="text" class="form-control" id="amount1" name="amount1" placeholder="Total" required="" readonly="">
											<input type="hidden" class="form-control" id="konversi_dollar1" name="konversi_dollar1" placeholder="Total" required="" readonly="">
										</div>						          		
										<div class="col-xs-1" style="padding:5px;">
											<a type="button" class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></a>
										</div>	
									</div>

									<div id="tambah"></div>

									<div class="col-md-2 col-md-offset-6">
										<p><b>Total Rupiah</b></p>
										<div class="input-group">
											<span class="input-group-addon">Rp. </span><input type="text" id="total_id" class="form-control" readonly>
										</div>
									</div>

									<div class="col-md-3">
										<p><b>Total Keseluruhan</b></p>
										<div class="input-group">
											<span class="input-group-addon">$ </span><input type="text" id="total_keseluruhan" class="form-control" readonly>
										</div>
									</div>

									<div class="col-md-11" style="margin-top: 20px">
										<p><b>Informasi Budget</b></p>
										<table class="table table-striped text-center">
											<tr>
												<th>Bulan</th>
												<th>Saldo Awal</th>
												<th>Total Pembelian</th>
												<th>Saldo Akhir</th>
											</tr>
											<tr>
												<td>
													<label id="bulanbudget" name="bulanbudget"></label>
												</td>
												<td>
													<label id="budgetLabel" name="budgetLabel"></label>
													<input type="hidden" id="budget" name="budget">
												</td>
												<td>
													<label id="TotalPembelianLabel" name="TotalPembelianLabel"></label>
													<input type="hidden" id="TotalPembelian" name="TotalPembelian">
												</td>
												<td>
													<label id="SisaBudgetLabel" name="SisaBudgetLabel"></label>
												</td>
											</tr>
										</table>
									</div>
									<div class="col-md-12">
										<br>
										<button type="submit" class="btn btn-success pull-right">Konfirmasi</button> 
										<span class="pull-right">&nbsp;</span>
										<a class="btn btn-info btnPrevious pull-right">Kembali</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="modal fade in" id="modalEdit">
		<form id="importFormEdit" name="importFormEdit" method="post" action="{{ url('canteen/update/purchase_requisition') }}">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-dialog modal-lg" style="width: 1300px">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Edit Purchase Requisition</h4>
						<br>
						<div class="nav-tabs-custom tab-danger">
							<ul class="nav nav-tabs" id="navv">
								<li class="vendor-tab active disabledTab"><a href="#tab_1_edit" data-toggle="tab" id="tab_header_1">Informasi & Detail PR</a></li>
							</ul>
						</div>

						<br>
						<h4 class="modal-title" id="modalDetailTitle"></h4>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1_edit">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-4">
											<div class="form-group">
												<label>Identitas</label>
												<input type="text" class="form-control" id="identitas_edit" name="identitas_edit" placeholder="Identitas">
											</div>
											<div class="form-group">
												<label>No PR</label>
												<input type="text" class="form-control" id="no_pr_edit" name="no_pr_edit" placeholder="PR Number">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Departemen</label>
												<input type="text" class="form-control" id="departemen_edit" name="departemen_edit" placeholder="Departemen">
											</div>
											<div class="form-group">
												<label>No Budget</label>
												<input type="text" class="form-control" id="no_budget_edit" name="no_budget_edit" placeholder="No Budget">
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group">
												<label>Tanggal Pengajuan</label>
												<input type="text" class="form-control" id="tgl_pengajuan_edit" name="tgl_pengajuan_edit" placeholder="Tanggal Pengajuan">
											</div>
										</div>
									</div>

									<div class="col-md-12" style="margin-bottom : 5px">
										<div class="col-xs-1" style="padding:5px;">
											<b>Kode Item</b>
										</div>
										<div class="col-xs-4" style="padding:5px;">
											<b>Deskripsi</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Tgl Kedatangan</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Mata Uang</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Harga</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Jumlah</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>UOM</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Total</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Aksi</b>
										</div>
									</div>

									<div id="modalDetailBodyEdit">
									</div>


									<br>
									<div id="tambah2">
										<input type="text" name="lop2" id="lop2" value="1" hidden="">
										<input type="text" name="looping" id="looping" hidden="">
									</div>

									<div class="col-md-11" style="margin-top: 20px">
										<p><b>Informasi Budget Yang Telah Masukkan</b></p>
										<table class="table table-striped text-center">
											<tr>
												<th>Bulan</th>
												<th>Budget Bulanan</th>
												<th>Total Pembelian</th>
												<th>Sisa Budget</th>
											</tr>
											<tr>
												<td>
													<label id="bulanbudgetedit" name="bulanbudgetedit"></label>
												</td>
												<td>
													<label id="budgetLabelEdit" name="budgetLabelEdit"></label>
												</td>
												<td>
													<label id="TotalPembelianEditLabel" name="TotalPembelianEditLabel"></label>
													<input type="hidden" id="TotalPembelianEdit" name="TotalPembelianEdit">
												</td>
												<td>
													<label id="SisaBudgetLabelEdit" name="SisaBudgetLabelEdit"></label>
													<input type="hidden" id="SisaBudgetEdit" name="SisaBudgetEdit">
												</td>
											</tr>
										</table>
									</div>
								</div>
								<div class="col-md-12">
									<input type="hidden" class="form-control" id="id_edit_pr" name="id_edit_pr" placeholder="ID">
									<button type="submit" class="btn btn-warning pull-right">Update</button>
									<span class="pull-right">&nbsp;</span>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="modal modal-danger fade in" id="modaldanger">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<h4 class="modal-title">Hapus Item</h4>
				</div>
				<div class="modal-body" id="modalDeleteBody">
					<p></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
					<a id="a" name="modalDeleteButton" href="" type="button" onclick="delete_item(this.id)" class="btn btn-danger">Delete</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-danger fade" id="modalDeletePR" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
	      </div>
	      <div class="modal-body">
	        Apakah anda yakin ingin menghapus Form PR Ini ?
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <a id="a" name="modalButton" href="" type="button"  onclick="deletePR(this.id)" class="btn btn-danger">Delete</a>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal modal fade" id="modaltracing" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width: 1000px">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Tracing PR <span id="nomor_pr_tracing"></span></h4>
				</div>
				<div class="modal-body">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Item Detail</th>
								<th>Qty</th>
								<th>Currency</th>
								<th>Unit Price</th>
								<th>Amount</th>
								<th>Status</th>
								<th>PO Number</th>
								<th>Received</th>
							</tr>
						</thead>
						<tbody id="modalDetailBodyTracing">							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	@endsection

	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
	<!-- <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script> -->
	<script>



		var no = 2;
		var limitdate = "";
		hasil_konversi_id = 0;
		item = [];
		item_list = "";
		total_id = 0;
		exchange_rate = [];

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {

    		$('.select10').select2({
    			dropdownAutoWidth : true,
    			dropdownParent: $("#budget_data"),
    			allowClear:true,
    		});

		// data table
		fillTable();		

        //Get Detail Item Berdasarkan code_item
        getItemList();

        getExchangeRate();

        id = document.getElementById('total_id');
        id.value = 0;

        limitdate = new Date();
        limitdate.setDate(limitdate.getDate());
        // limitdate.setDate(limitdate.getDate() + 21);

        $('#datefrom').datepicker({
        	autoclose: true,
        	todayHighlight: true
        });

        $('#dateto').datepicker({
        	autoclose: true,
        	todayHighlight: true,
        });

        $('#sd').datepicker({
        	autoclose: true,
        	format: 'yyyy-mm-dd',
        	todayHighlight: true
        });

        $('.datepicker').datepicker({
        	autoclose: true,
        	format: 'yyyy-mm-dd',
        	startDate: limitdate,
        	todayHighlight: true
        });

        $('.btnNext').click(function(){
        	var emp_id = $('#emp_id').val();
        	var no_pr = $('#no_pr').val();
        	var staff = $('#staff').val();
        	var catatan = $('#note').val();
        	// var catatan = CKEDITOR.instances.note.getData();
        	// || catatan == ''
        	if(emp_id == '' || no_pr == '' || staff == ''){ 
        		alert('Semua Kolom Harus Diisi');	
        	}
        	else{
        		$('.nav-tabs > .active').next('li').find('a').trigger('click');
        	}
        });

        $('.btnNext2').click(function(){

        	var status = 0;  		
			var len = $('#budget_sisa').text();
			if (len != "") {
				var sisa = parseFloat(len.substr(1));
				if (sisa > 0) {
					
				} else {
					status++;
				}			        	
			}

			if (status > 0) {
				openErrorGritter("Error","Budget Tidak Ada");
				return false;
			}

        	var budget_no = $('#budget_no').val();
        	if(budget_no == ""){
        		alert('Budget field must be filled');	
        	}
        	else{
        		$('.nav-tabs > .active').next('li').find('a').trigger('click');
        	}

        	var nomorpr = document.getElementById("no_pr");
			
			$.ajax({
				url: "{{ url('canteen_purchase_requisition/get_nomor_pr') }}", 
				type : 'GET', 
				success : function(data){
					var obj = jQuery.parseJSON(data);
					var no = obj.no_urut;
					var tahun = obj.tahun;
					var bulan = obj.bulan;
					var dept = obj.dept;

					nomorpr.value = dept+tahun+bulan+no;
				}
			});
        });

        $('.btnNextEdit').click(function(){
        	$('#navv > .active').next('li').find('a').trigger('click');
        });

        $('.btnPrevious').click(function(){
        	$('.nav-tabs > .active').prev('li').find('a').trigger('click');
        });
    });


	$("#importForm").submit(function(){
		if (!confirm("Apakah Anda Yakin Ingin Membuat PR Ini??")) {
			return false;
		} else {
			var status = 0;  		
			var len = $('#SisaBudgetLabel').text();
			if (len != "") {
				var sisa = parseFloat(len.substr(1));
				if (sisa > 0) {
					
				} else {
					status++;
				}			        	
			}

			if (status > 0) {
				openErrorGritter("Error","Tidak Boleh Melebihi Budget");
				return false;
			}else{
				this.submit();
			}
		}
	});

	//Datatable 

	function fillTable(){
		$('#prTable').DataTable().clear();
		$('#prTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto
		}

		var table = $('#prTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
				},
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'print',
					className: 'btn btn-warning',
					text: '<i class="fa fa-print"></i> Print',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/canteen/purchase_requisition") }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_pr" },
			{ "data": "submission_date" },
			{ "data": "emp_name" },
			{ "data": "file" },
			{ "data": "note" },
			{ "data": "status" },
			{ "data": "action" },
			],
		});

		$('#prTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
		});

		table.columns().every( function () {
			var that = this;
			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});

		$('#prTable tfoot tr').appendTo('#prTable thead');
	}


	//Open Modal + Get Nomor PR

	function openModalCreate(){
		$('#modalCreate').modal('show');
		//nomor PR auto generate
		var nomorpr = document.getElementById("no_pr");

		$.ajax({
			url: "{{ url('canteen_purchase_requisition/get_nomor_pr') }}", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				var no = obj.no_urut;
				var tahun = obj.tahun;
				var bulan = obj.bulan;
				var dept = obj.dept;

				nomorpr.value = dept+tahun+bulan+no;
			}
		});

		pilihBudget('ADM23085');
	}

	function clearConfirmation(){
		location.reload(true);		
	}

	//Get Item + Pilih Item

	function pilihItem(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ route('getcanteenitem') }}?kode_item="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#item_desc'+no).val(obj.deskripsi).attr('readonly', true);
				$('#item_price'+no).val(obj.price).attr('readonly', true);
				$('#uom'+no).val(obj.uom).change().attr('readonly', true);
				$('#qty'+no).val("0");
				$('#moq'+no).val(obj.moq);
				$('#amount'+no).val("0");
				$('#item_currency'+no).next(".select2-container").hide();
				$('#item_currency'+no).hide();
				$('#item_currency_text'+no).show();
				$('#item_currency_text'+no).val(obj.currency).show().attr('readonly', true);

				if (obj.currency == "IDR"){
					$('#ket_harga'+no).text("Rp.");
				}

				var $datepicker = $('#req_date'+no).attr('readonly', false);
				$datepicker.datepicker();
				$datepicker.datepicker('setDate', limitdate);
			} 
		});

	    // alert(sel.value);
	}

	function pilihItemEdit(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ route('admin.prgetitemdesc') }}?kode_item="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#item_desc_edit'+no).val(obj.deskripsi).attr('readonly', true);
				$('#item_price_edit'+no).val(obj.price).attr('readonly', true);
				$('#uom_edit'+no).val(obj.uom).change();
				// $('#qty_edit'+no).val("0");
				// $('#amount_edit'+no).val("0");

				$('#item_currency_edit'+no).next(".select2-container").hide();
				$('#item_currency_edit'+no).hide();
				$('#item_currency_text_edit'+no).show();
				$('#item_currency_text_edit'+no).val(obj.currency).show().attr('readonly', true);
				
				if (obj.currency == "IDR"){
					$('#ket_harga_edit'+no).text("Rp.");
				}


			} 
		});

	    // alert(sel.value);
	}

	function getItemList() {
		$.get('{{ url("fetch/canteen/purchase_requisition/itemlist") }}', function(result, status, xhr) {
			item_list += "<option></option> ";
			$.each(result.item, function(index, value){
				item_list += "<option value="+value.kode_item+">"+value.kode_item+ " - " +value.deskripsi+"</option> ";
			});
			$('#item_code1').append(item_list);
		})
	}

	function getExchangeRate(){
		$.ajax({
			url: "{{ url('purchase_requisition/get_exchange_rate') }}", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				for (var i = 0; i < obj.length; i++) {
            		var currency = obj[i].currency; // currency
	            	var rate = obj[i].rate; //nilai tukar

	            	exchange_rate.push({
	            		'currency' :  obj[i].currency, 
	            		'rate' :  obj[i].rate,
	            	});
	            }
	        }
	    });
	}

	//get Budget + Pilih budget
	
	// function getBudget() {

	// 	data = {
	// 		department : "{{ $employee->department }}",
	// 	}
		
	// 	$.get('{{ url("fetch/purchase_requisition/budgetlist") }}', data, function(result, status, xhr) {
	//   		budget_list = "";
	//   		budget_list = "<option value=''></option>";
	// 		$.each(result.budget, function(index, value){
	// 			budget_list += "<option value="+value.budget_no+">"+value.budget_no+" - "+value.description+"</option> ";
	// 		});
	// 		// console.log($('#budget_no').val());


	// 		if ($('#budget_no').val() == "" || $('#budget_no').val() == null) {
	//   			$('#budget_no').html('');
	// 			$('#budget_no').append(budget_list);				
	// 		}

	// 	})
	// }

	function pilihBudget(elem)
	{
		$('#budgetket').show();

		$.ajax({

			url: "{{ route('admin.prgetbudgetdesc') }}?budget_no="+elem,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#budget_no_text').text(elem);
				$('#budget_no').val(elem);
				$('#budget_description').text(obj.description);
				$('#budget_account').text(obj.account);
				$('#budget_category').text(obj.category);
				$('.periode').text(obj.periode);
				
				var total_tahun = obj.apr + obj.may + obj.jun + obj.jul + obj.aug + obj.sep + obj.oct + obj.nov + obj.dec + obj.jan + obj.feb + obj.mar;
				// console.log(total_tahun);

				$('#budget_amount').text("$"+obj.amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
				$('#budget_sisa_tahun').text("$"+total_tahun.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
				$('#budget_sisa').text("$"+obj.budget_now.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

				$('#budgetLabel').text("$"+obj.budget_now.toFixed(2));
				$('#budget').val(obj.budget_now.toFixed(2));
				$('#bulanbudget').text(obj.namabulan);

			} 
		});
	}

	//Get total amount + Per Currency

	function getTotal(id) {
		// console.log(id);
		var num = id.match(/\d/g);
		num = num.join("");

		var price = document.getElementById("item_price"+num).value;
	    // var prc = price.replace(/\D/g, ""); get angka saja

	    var qty = document.getElementById("qty"+num).value;
      	// var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty
      	var hasil = parseFloat(qty) * parseFloat(price);

      	var moq = document.getElementById("moq"+num).value;

      	if (parseFloat(qty) < parseFloat(moq) && parseFloat(qty) > 0) {
      		openErrorGritter("Error","Jumlah Kurang Dari Minimum Order. Minimum order = "+moq);
      		return false;
      	}

      	if (!isNaN(hasil)) {

    		//isi amount + amount dirubah
    		var amount = document.getElementById('amount'+num);
    		// amount.value = rubah(hasil);
    		amount.value = hasil.toFixed(2);

    		total_id = 0;
    		total_id_all = 0;
    		total_id_konversi = 0;
    		total_beli = 0;

        	//mata uang

        	for (var i = 1; i < no; i++) {
        		var req_date = $('#submission_date').val();
        		date_js = new Date(req_date);
        		req_bulan = date_js.getMonth()+1;

        		var mata_uang = $('#item_currency'+i).val();
        		var mata_uang_text = $('#item_currency_text'+i).val();
        		var dollar = document.getElementById('konversi_dollar'+i);

        		tot_id = document.getElementById('amount'+i).value;

        		if (mata_uang == "IDR" || mata_uang_text == "IDR"){
        			total_id += parseFloat(tot_id);	

        			total_konversi = parseFloat(tot_id);
        			dollar.value = konversi("IDR","USD",total_konversi);   	    			

        			total_id_konversi = parseFloat(konversi("IDR","USD",total_id));
        		}

        		document.getElementById('total_id').value = rubah(total_id);

        		total_beli =  total_id_konversi;
        		budget = $('#budget').val();

        		if (total_beli > 0) {
        			$('#TotalPembelianLabel').text("$"+total_beli.toFixed(2));
        			$('#TotalPembelian').val(total_beli.toFixed(2));
        		}else{
        			$('#TotalPembelianLabel').text("");
        		}

        		var sisa = parseFloat(budget) - parseFloat(total_beli);

        		if (total_beli > 0) {
        			if (sisa < 0) {
        				$('#SisaBudgetLabel').text("$"+sisa.toFixed(2)).css("color", "red");   	    		
        			}else if(sisa > 0){
        				$('#SisaBudgetLabel').text("$"+sisa.toFixed(2)).css("color", "green");
        			}
        			else{
        				$('#SisaBudgetLabel').text("$"+sisa.toFixed(2));
        			}
        		}
        		else{
        			$('#SisaBudgetLabel').text("");
        		}

        		var curr;
        		if (mata_uang != "") {
        			curr = mata_uang;
        		}
        		else if(mata_uang_text != ""){
        			curr = mata_uang_text;
        		}

        		document.getElementById('total_keseluruhan').value = konversiToUSD(curr,'USD');
        	}
        }
    }

    function getTotalEdit(id) {
		// console.log(id);
		var num = id.match(/\d/g);
		num = num.join("");

		var price = $("#item_price_edit"+num).val();
	    // var prc = price.replace(/\D/g, ""); get angka saja

	    var qty = $("#qty_edit"+num).val();
      	// var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty
      	var hasil = parseFloat(qty) * parseFloat(price);

      	// var moq = document.getElementById("moq"+num).value;

      	// if (moq != null && qty < moq && qty > 0) {
      	// 	openErrorGritter("Error","Jumlah Kurang Dari Minimum Order. Minimum order = "+moq);
      	// 	return false;
      	// }

      	if (!isNaN(hasil)) {
      		$("#amount_edit"+num).val(hasil.toFixed(2));
    		total_id = 0;
    		total_id_all = 0;
    		total_id_konversi = 0;
    		total_beli = 0;


      		var req_date = $('#req_date_edit'+num).val();
      		date_js = new Date(req_date);
      		req_bulan = date_js.getMonth()+1;

      		var mata_uang = $('#item_currency_edit'+num).val();
      		var mata_uang_text = $('#item_currency_text_edit'+num).val();
      		var dollar = document.getElementById('konversi_dollar'+num);

    		tot_id = document.getElementById('amount_edit'+num).value;

    		if (mata_uang == "IDR" || mata_uang_text == "IDR"){
    			total_id += parseFloat(tot_id);	

    			total_konversi = parseFloat(tot_id);
    			dollar.value = konversi("IDR","USD",total_konversi);   	    			

    			total_id_konversi = parseFloat(konversi("IDR","USD",total_id));
    		}
      		

    		document.getElementById('total_id').value = rubah(total_id);

    		total_beli = total_id_konversi;
    		budget = $('#budget').val();

    		if (total_beli > 0) {
    			$('#TotalPembelianEditLabel').text("$"+total_beli.toFixed(2));
    			$('#TotalPembelianEdit').val(total_beli.toFixed(2));
    		}else{
    			$('#TotalPembelianEditLabel').text("");
    		}

    		var sisa = parseFloat(budget) - parseFloat(total_beli);

    		if (total_beli > 0) {
    			if (sisa < 0) {
    				$('#SisaBudgetLabelEdit').text("$"+sisa.toFixed(2)).css("color", "red");   	    		
    			}else if(sisa > 0){
    				$('#SisaBudgetLabelEdit').text("$"+sisa.toFixed(2)).css("color", "green");
    			}
    			else{
    				$('#SisaBudgetLabelEdit').text("$"+sisa.toFixed(2));
    			}
    		}
    		else{
    			$('#SisaBudgetLabelEdit').text("");
    		}

    		var curr;
    		if (mata_uang != "") {
    			curr = mata_uang;
    		}
    		else if(mata_uang_text != ""){
    			curr = mata_uang_text;
    		}

    		document.getElementById('total_keseluruhan').value = konversiToUSD(curr,'USD');
      	}
      }

      function konversi(from, to, amount){

      	var obj = exchange_rate;

      	for (var i = 0; i < obj.length; i++) {
    		var currency = obj[i].currency; // currency
        	var rate = obj[i].rate; //nilai tukar

        	if (from == currency) {
        		fromrate = rate;
        	}

        	if (to == currency) {
        		torate = rate;
        	}
        }
        hasil_konversi = (amount / fromrate) * torate;
        return hasil_konversi.toFixed(2);		    
    }

    function konversiToUSD(from, to){

    	var obj = exchange_rate;
    	var fromrate = 0;
    	var torate = 0;

    	for (var i = 0; i < obj.length; i++) {
    		var currency = obj[i].currency; // currency
        	var rate = obj[i].rate; //nilai tukar

        	if (from == currency) {
        		fromrate = rate;
        	}

        	if (to == currency) {
        		torate = rate;
        	}
        }
        if (from == "IDR") {
        	hasil_konversi_id = (total_id / fromrate) * torate;
        }

        var hasil = parseFloat(hasil_konversi_id.toFixed(2));
        return hasil.toFixed(2);

    }

    function tambah(id,lop) {
    	var id = id;

    	var lop = "";

    	if (id == "tambah"){
    		lop = "lop";
    	}else{
    		lop = "lop2";
    	}

    	var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Choose Item' name='item_code"+no+"' id='item_code"+no+"' onchange='pilihItem(this)'><option></option></select></div><div class='col-xs-4' style='padding:5px;'><input type='text' class='form-control' id='item_desc"+no+"' name='item_desc"+no+"' placeholder='Description' required=''></div><div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date"+no+"' name='req_date"+no+"' placeholder='Tanggal' required=''></div></div> <div class='col-xs-1' style='padding: 5px'><select class='form-control select2' id='item_currency"+no+"' name='item_currency"+no+"'data-placeholder='Currency' style='width: 100%' onchange='currency(this)'><option value=''></option><option value='IDR'>IDR</option></select><input type='text' class='form-control' id='item_currency_text"+no+"' name='item_currency_text"+no+"' style='display:none'></div> <div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"' name='ket_harga"+no+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price"+no+"' name='item_price"+no+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' style='padding:6px 6px'></div></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' onkeyup='getTotal(this.id)' required=''><input type='hidden' class='form-control' id='moq"+no+"' name='moq"+no+"' placeholder='Moq'></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select6' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' placeholder='Total' required='' readonly><input type='hidden' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Total' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

    	$("#"+id).append(divdata);
    	$("#item_code"+no).append(item_list);


    	$('.datepicker').datepicker({
    		autoclose: true,
    		format: 'yyyy-mm-dd',
    		startDate: limitdate,
    		todayHighlight:true
    	});

    	$(function () {
    		$('.select3').select2({
    			dropdownAutoWidth : true,
    			dropdownParent: $("#"+id),
    			allowClear:true
    		});

		  	$('.select6').select2({
    			dropdownParent: $("#"+id),
		  		allowClear:true,
		  		dropdownAutoWidth : true,
		  	});
    	})

		// $("#"+id).select2().trigger('change');
		document.getElementById(lop).value = no;
		no+=1;
	}

	//Fungsi Kurang

	function kurang(elem,lop) {

		var lop = lop;
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;

		$("#"+newid).attr("id",oldid);
		$("#item_code"+newid).attr("name","item_code"+oldid);
		$("#item_desc"+newid).attr("name","item_desc"+oldid);
		$("#item_price"+newid).attr("name","item_price"+oldid);
		$("#qty"+newid).attr("name","qty"+oldid);
		$("#moq"+newid).attr("name","moq"+oldid);
		$("#uom"+newid).attr("name","uom"+oldid);
		$("#ket_harga"+newid).attr("name","ket_harga"+oldid);
		$("#amount"+newid).attr("name","amount"+oldid);
		$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
		$("#item_currency"+newid).attr("name","item_currency"+oldid);
		$("#item_currency_text"+newid).attr("name","item_currency_text"+oldid);
		$("#req_date"+newid).attr("name","req_date"+oldid);

		$("#item_code"+newid).attr("id","item_code"+oldid);
		$("#item_desc"+newid).attr("id","item_desc"+oldid);
		$("#item_price"+newid).attr("id","item_price"+oldid);
		$("#qty"+newid).attr("id","qty"+oldid);
		$("#moq"+newid).attr("id","moq"+oldid);
		$("#uom"+newid).attr("id","uom"+oldid);
		$("#ket_harga"+newid).attr("id","ket_harga"+oldid);
		$("#amount"+newid).attr("id","amount"+oldid);
		$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);
		$("#item_currency"+newid).attr("id","item_currency"+oldid);
		$("#item_currency_text"+newid).attr("id","item_currency_text"+oldid);
		$("#req_date"+newid).attr("id","req_date"+oldid);

		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {	
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			$("#"+newid).attr("id",oldid);
			$("#item_code"+newid).attr("name","item_code"+oldid);
			$("#item_desc"+newid).attr("name","item_desc"+oldid);
			$("#item_price"+newid).attr("name","item_price"+oldid);
			$("#qty"+newid).attr("name","qty"+oldid);
			$("#moq"+newid).attr("name","moq"+oldid);
			$("#uom"+newid).attr("name","uom"+oldid);
			$("#ket_harga"+newid).attr("name","ket_harga"+oldid);
			$("#amount"+newid).attr("name","amount"+oldid);
			$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
			$("#item_currency"+newid).attr("name","item_currency"+oldid);
			$("#item_currency_text"+newid).attr("name","item_currency_text"+oldid);
			$("#req_date"+newid).attr("name","req_date"+oldid);

			$("#item_code"+newid).attr("id","item_code"+oldid);
			$("#item_desc"+newid).attr("id","item_desc"+oldid);
			$("#item_price"+newid).attr("id","item_price"+oldid);
			$("#qty"+newid).attr("id","qty"+oldid);
			$("#moq"+newid).attr("id","moq"+oldid);
			$("#uom"+newid).attr("id","uom"+oldid);
			$("#ket_harga"+newid).attr("id","ket_harga"+oldid);
			$("#amount"+newid).attr("id","amount"+oldid);
			$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);
			$("#item_currency"+newid).attr("id","item_currency"+oldid);
			$("#item_currency_text"+newid).attr("id","item_currency_text"+oldid);
			$("#req_date"+newid).attr("id","req_date"+oldid);


		}
		document.getElementById(lop).value = a;

		getTotal("qty"+a);	

	}

	//Change to format uang

	function currency(elem){

		var no = elem.id.match(/\d/g);
		no = no.join("");

		var mata_uang = $('#item_currency'+no).val();
		var mata_uang_text = $('#item_currency_text'+no).val();

		if (mata_uang == "IDR") {
			$('#ket_harga'+no).text("Rp. ");
		}
	}

	/* Fungsi formatUang */
	function formatUang(angka, prefix) {
		var number_string = angka.replace(/[^,\d]/g, "").toString(),
		split = number_string.split(","),
		sisa = split[0].length % 3,
		rupiah = split[0].substr(0, sisa),
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if (ribuan) {
      	separator = sisa ? "." : "";
      	rupiah += separator + ribuan.join(".");
      }

      rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
      return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
  }

  function rubah(angka){
  	var reverse = angka.toString().split('').reverse().join(''),
  	ribuan = reverse.match(/\d{1,3}/g);
  	ribuan = ribuan.join('.').split('').reverse().join('');
  	return ribuan;
  }

  $('.select2').select2({
  	allowClear: true,
  	dropdownAutoWidth : true
  });

  $(function () {
  	$('.select4').select2({
  		dropdownParent: $("#tab_3"),
  		allowClear:true,
  		dropdownAutoWidth : true
  	});
  	$('.select7').select2({
  		dropdownParent: $("#tab_3"),
  		allowClear:true,
  		dropdownAutoWidth : true,
  	});
  })



  function editPR(id){
  	var isi = "";
  	var isi_other = "";
  	$('#modalEdit').modal("show");
  	
  	var data = {
  		id:id
  	};
  	
  	$.get('{{ url("canteen/edit/purchase_requisition") }}', data, function(result, status, xhr){	

  		$("#identitas_edit").val(result.purchase_requisition.emp_id+' - '+result.purchase_requisition.emp_name).attr('readonly', true);
  		$("#departemen_edit").val(result.purchase_requisition.department).attr('readonly', true);
  		$("#no_pr_edit").val(result.purchase_requisition.no_pr).attr('readonly', true);
  		$("#no_budget_edit").val(result.purchase_requisition.no_budget).attr('readonly', true);
  		$("#tgl_pengajuan_edit").val(result.purchase_requisition.submission_date).attr('readonly', true);
  		$("#id_edit_pr").val(result.purchase_requisition.id).attr('readonly', true);

  		$('#modalDetailBodyEdit').html('');
  		
  		var ids = [];
  		var total_amount = 0;

  		$.ajax({
  			url: "{{ route('admin.prgetbudgetdesc') }}?budget_no="+result.purchase_requisition.no_budget,
  			method: 'GET',
  			success: function(data) {
  				var json = data,
  				obj = JSON.parse(json);
  				$('#bulanbudgetedit').text(obj.namabulan);
  			}
  		});


  		$.each(result.purchase_requisition_item, function(key, value) {
  			
  			var tambah2 = "tambah2";
  			var	lop2 = "lop2";

  			isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";
  			if (value.item_code != null) {

  				isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_code_edit"+value.id+"' name='item_code_edit"+value.id+"' value="+value.item_code+" readonly=''></div>";


  			} else{
  				isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_code_edit"+value.id+"' name='item_code_edit"+value.id+"' readonly=''></div>";
  			}
  			
  			isi += "<div class='col-xs-4' style='padding:5px;'><input type='text' class='form-control' id='item_desc_edit"+value.id+"' name='item_desc_edit"+value.id+"' placeholder='Description' required='' value='"+value.item_desc+"'></div>";
  			isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date_edit"+value.id+"' name='req_date_edit"+value.id+"' placeholder='Tanggal' required='' value='"+value.item_request_date+"'' readonly=''></div></div>";
  			isi += "<div class='col-xs-1' style='padding: 5px'><input type='text' class='form-control' id='item_currency_edit"+value.id+"' name='item_currency_edit"+value.id+"' value='"+value.item_currency+"' readonly=''><input type='text' class='form-control' id='item_currency_text_edit"+value.id+"' name='item_currency_text_edit"+value.id+"' readonly='' style='display:none'></div>";
  			isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit"+value.id+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price_edit"+value.id+"' name='item_price_edit"+value.id+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' readonly='' value="+value.item_price+" style='padding: 6px 6px'></div></div>";
  			isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty_edit"+value.id+"' name='qty_edit"+value.id+"' placeholder='Qty' required='' value='"+value.item_qty+"' readonly=''></div>";
  			isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='uomhide"+value.id+"' id='uomhide"+value.id+"' value='"+value.item_uom+"'><select class='form-control select5' id='uom_edit"+value.id+"' name='uom_edit"+value.id+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div>";
  			isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount_edit"+value.id+"' name='amount_edit"+value.id+"' placeholder='Total' required='' value='"+value.item_amount+"' readonly=''><input type='hidden' class='form-control' id='konversi_dollar"+value.id+"' name='konversi_dollar"+value.id+"' placeholder='Total' required='' readonly='' value="+value.amount+"></div>";
  			isi += "<div class='col-xs-1' style='padding:5px;'><a href='javascript:void(0);' id='b"+ value.id +"' onclick='deleteConfirmation(\""+ value.item_desc +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button></div>";
  			isi += "</div>";


  			ids.push(value.id);

  			$('#modalDetailBodyEdit').append(isi);

  			$('.item_code_edit').append(item_list);

  			$('.datepicker').datepicker({
  				autoclose: true,
  				format: 'yyyy-mm-dd'
  			});

  			$(function () {
  				$('.select5').select2({
  					dropdownAutoWidth : true,
  					dropdownParent: $("#"+id),
  					allowClear: true
  				});
  			})

  			if (value.item_currency == "IDR"){
  				$('#ket_harga_edit'+value.id).text("Rp.");
  			}

  			var uom = $('#uomhide'+value.id).val();
  			$("#uom_edit"+value.id).val(uom).trigger("change");

  			// var item_code = $('#item_code_edit_hide'+value.id).val();
  			// $("#item_code_edit"+value.id).val(item_code).trigger("change");

  			$("#looping").val(ids);

  			date_budget = new Date(value.budget_date);
  			budget_bulan = date_budget.getMonth()+1;
  			total_amount = total_amount + value.amount;
  			total_sisa = value.beg_bal - total_amount;

  			$("#budgetLabelEdit").text("$"+value.beg_bal).attr('readonly', true);
  			
  			$("#TotalPembelianEdit").val(total_amount.toFixed(2)).attr('readonly', true);
  			$("#TotalPembelianEditLabel").text("$"+total_amount.toFixed(2)).attr('readonly', true);

  			$('#SisaBudgetLabelEdit').text("$"+total_sisa.toFixed(2));
  			$('#SisaBudgetEdit').val(total_sisa.toFixed(2));

  		});

		if (result.purchase_requisition_item.length == 0) {

  			isi_other = "<div class='col-md-12' style='margin-bottom : 5px'>";

	  		isi_other += "<button type='button' class='btn btn-success' onclick='tambah(\"tambah2\",\"lop2\");'><i class='fa fa-plus' ></i></button>";

  			isi_other += "</div>";


  			$('#modalDetailBodyEdit').append(isi_other);

		}

	});
}

function deleteConfirmation(name, id) {
	$('#modalDeleteBody').text("Are you sure want to delete ' " + name + " '");
	$('[name=modalDeleteButton]').attr("id",id);
}

function deleteConfirmationPR(id) {
	$('[name=modalButton]').attr("id",id);
}

function delete_item(id) {

	var data = {
		id:id,
	}

	$("#loading").show();

	$.post('{{ url("canteen/delete/purchase_requisition_item") }}', data, function(result, status, xhr){
		if (result.status == true) {
        	openSuccessGritter("Success","Data Berhasil Dihapus");
        	$("#loading").hide();
			setTimeout(function(){  window.location.reload() }, 2500);
		}
		else{
			openErrorGritter("Gagal","Data Gagal Dihapus");
		}
	});

	$('#modaldanger').modal('hide');
	$('#'+id).css("display","none");
}

function deletePR(id){

	var data = {
		id:id,
	}

	$("#loading").show();

	$.post('{{ url("canteen/delete/purchase_requisition") }}', data, function(result, status, xhr){
		if (result.status == true) {
        	openSuccessGritter("Success","Data Berhasil Dihapus");
        	$("#loading").hide();
        	setTimeout(function(){  window.location.reload() }, 2500);
		}
		else{
			openErrorGritter("Success","Data Gagal Dihapus");
		}
	});
}

function sendEmail(id) {
    var data = {
        id:id
    };

    if (!confirm("Apakah anda yakin ingin mengirim PR ini ke Manager?")) {
        return false;
    }
    else{
      	$("#loading").show();
    }

    $.get('{{ url("canteen/purchase_requisition/sendemail") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
    })
}


function ResendEmail(id) {
	var data = {
		id:id
	};

	if (!confirm("Gunakan Fitur ini untuk mengirim email reminder ke pihak approver. Mohon untuk tidak melakukan spam. Apakah anda yakin ingin mengirim email reminder ini ke approver?")) {
		return false;
	}
	else{
		$("#loading").show();
	}

	$.get('{{ url("canteen/purchase_requisition/resendemail") }}', data, function(result, status, xhr){
		openSuccessGritter("Success","Email Resend Berhasil Terkirim");
		$("#loading").hide();
		// setTimeout(function(){  window.location.reload() }, 2500);
	})
}

function tracing(id){
  	var isi = "";
  	$('#modaltracing').modal("show");
  	
  	var data = {
  		id:id
  	};
  	
  	$.get('{{ url("canteen/tracing/purchase_requisition") }}', data, function(result, status, xhr){	

  		$('#modalDetailBodyTracing').html('');

		$.each(result.purchase_requisition_item, function(key, value) {
  			
  			$("#nomor_pr_tracing").text(value.no_pr);

  			isi = "<tr>";
  			isi += "<td style='text-align:left'>"+value.item_desc+"</td>";
  			isi += "<td>"+value.item_qty+" "+value.item_uom+"</td>";
  			isi += "<td>"+value.item_currency+"</td>";
  			isi += "<td style='text-align:right'>"+value.item_price.toLocaleString('de-DE')+"</td>";
  			isi += "<td style='text-align:right'>"+value.item_amount.toLocaleString('de-DE')+"</td>";

  			if(value.tanggal_diterima != null){
  				isi += "<td style='background-color:green;color:white'>Sudah Diterima</td>";
  			}
  			else if(value.status_pr == "PR") {
  				isi += "<td style='background-color:red;color:white'>Belum PO</td>";
  			}
  			else if(value.status_pr == "PO"){
  				isi += "<td style='background-color:blue;color:white'>Sudah PO</td>";
  			}
  			else if(value.status_pr == "Actual"){
  				isi += "<td style='background-color:green;color:white'>Sudah Diterima</td>";
  			}

  			if(value.tanggal_diterima != null){
  				isi += "<td>"+value.po_number+"</td>";
  			}
  			else if(value.status_pr == "PR") {
  				isi += "<td>-</td>";
  			}
  			else if(value.status_pr == "PO"){
  				isi += "<td>"+value.po_number+"</td>";
  			}
  			else if(value.status_pr == "Actual"){
  				isi += "<td>"+value.po_number+"</td>";
  			}

  			if(value.tanggal_diterima != null){
  				isi += "<td>"+getFormattedDate(new Date(value.tanggal_diterima))+"</td>";
  			}
  			else if(value.status_pr == "PR") {
  				isi += "<td>-</td>";
  			}
  			else if(value.status_pr == "PO"){
  				isi += "<td>-</td>";
  			}
  			else if(value.status_pr == "Actual"){
  				isi += "<td>Closed</td>";
  			}

  			isi += "<tr>";

  			$('#modalDetailBodyTracing').append(isi);
  		});
  	});
  }

  function getFormattedDate(date) {
	  var year = date.getFullYear();

	  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
		  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
		];

	  var month = date.getMonth();

	  var day = date.getDate().toString();
	  day = day.length > 1 ? day : '0' + day;
	  
	  return day + '-' + monthNames[month] + '-' + year;
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
		time: '2000'
	});
}



</script>

@endsection




