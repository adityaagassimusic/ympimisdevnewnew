@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		List of {{$page}}
	</h1>
	<ol class="breadcrumb">
		<!-- <li>
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Fixed Asset Scrap.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Asset Disposal Scrap</a> &nbsp;
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Disposal Fixed Asset Sale.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Asset Disposal Sale</a> &nbsp;
		</li> -->
		<li>
			<!-- <a class="btn btn-danger btn-md" style="color:white" href="{{ url('monitoring/fixed_asset/disposal/scrap') }}"><i class="fa fa-tv"></i>Monitoring Disposal</a>&nbsp; -->
			<!-- <a class="btn btn-primary btn-md" style="color:white" href="{{ url('index/fixed_asset/disposal/scrap') }}"><i class="fa fa-gavel"></i>Disposal Scrap List</a>&nbsp; -->
			<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Request Disposal Non FA</a>
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 5%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 5%">Fixed Asset No.</th>
								<th style="width: 10%">Section Control</th>
								<th style="width: 10%">PIC InCharge</th>
								<th style="width: 10%">Mode</th>
								<th style="width: 10%">Status</th>
								<th style="width: 5%">PDF</th>
								<th style="width: 5%">Action</th>
							</tr>
						</thead>
						<tbody id="masterBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	
	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: orange;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Form Disposal Non Fixed Asset</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master">
							<div class="row">

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Non Asset Category : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<select class="form-control select2" data-placeholder="Select Category" style="width: 100%" name="category" id="category">
											<option value=""></option>
											<option value="Machine and Equipment">Machine and Equipment</option>
											<option value="Moulding">Moulding</option>
											<option value="Spare Part">Spare Part</option>
											<option value="Factory Tool">Factory Tool / Vehicle</option>
											<option value="Office Tool">Office Tool</option>
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<select class="form-control select2" data-placeholder="Select Section" style="width: 100%" name="section" id="section">
											<option value=""></option>
											@foreach($section as $sec)
											<option value="{{$sec->section}}">{{$sec->section}}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-3" align="left" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Non Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-9">
										<input type="hidden" id="asset_name" name="asset_name">
										<select class="form-control select2" id="asset_id" name="asset_id" data-placeholder="Select Non Asset" style="width: 100%" onchange="pilihAsset(this.value)">
											<option value=""></option>
											@foreach($list_non_asset as $asset)
											<option value="{{$asset->no_po}}_{{$asset->no_pr}}_{{$asset->nama_item}}_{{$asset->surat_jalan}}">{{$asset->no_po}} - {{$asset->no_pr}} - {{$asset->nama_item}} - {{$asset->date_receive}} - {{$asset->surat_jalan}}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-3" align="left" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Non Asset Picture : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-9">
										<input type="file" id="asset_picture" name="asset_picture" accept="image/*">
									</div>
								</div>

								<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Non Asset Number : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<input type="text" class="form-control" id="asset_no" name="asset_no" placeholder="fixed asset number" readonly>
									</div>
								</div> -->

								<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Non Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<input type="text" class="form-control" id="asset_cls" name="asset_cls" placeholder="fixed asset Clasification" readonly>
									</div>
								</div> -->
								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<textarea class="form-control" id="disposal_reason" name="disposal_reason" placeholder="input reason"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason (Japanese): <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<textarea class="form-control" id="disposal_reason_jp" name="disposal_reason_jp" placeholder="input reason (Japanese)"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Disposal Mode : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<select class="form-control select2" data-placeholder="Choose mode" onchange="pilihMode(this)" style="width: 100%" name="mode" id="mode">
											<option value=""></option>
											<option value="SCRAP">SCRAP</option>
											<option value="OVER-HANDLE">OVER-HANDLE</option>
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%; display: none" id="div_quot">
									<div class="col-xs-3" style="padding: 0px;" align="left">
										<span style="font-weight: bold; font-size: 16px;">Quotation : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-9">
										<input type="file" name="quotation_file" id="quotation_file" accept="application/pdf">
									</div>
								</div>
							</div>

							<div class="col-xs-12" style="margin-top: 3%;">
								<p style="font-size: 1.2vw;">List Disposal Non Fixed Asset</p>
		                        <div class="box box-primary">
		                            <div class="box-body">
		                                <table class="table table-hover table-bordered table-striped" id="tableListNonAsset">
		                                    <thead style="background-color: rgba(126,86,134,.7);">
		                                        <tr>
		                                            <th style="width: 1%; text-align: center;">No</th>
		                                            <th style="width: 7%; text-align: center;">No PO</th>
		                                            <th style="width: 7%; text-align: center;">No PR</th>
		                                            <th style="width: 7%; text-align: center;">Nama Item</th>
		                                            <th style="width: 7%; text-align: center;">Qty</th>
		                                            <th style="width: 7%; text-align: center;">Tanggal Diterima</th>
		                                            <th style="width: 7%; text-align: center;">Surat Jalan</th>
		                                            <th style="width: 2%; text-align: center;">Aksi</th>
		                                        </tr>
		                                    </thead>
		                                    <tbody id="tableBodyNonAsset">
		                                    </tbody>
		                                </table>
		                            </div>
		                        </div>
		                    </div>

							<div class="row">
								<div class="col-xs-12 pull-right">
									<button class="btn btn-success pull-right" type="submit" id="create_btn"><i class="fa fa-check"></i> Request Disposal </button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalFill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #f39c12;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Form Disposal Asset</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master_fill">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="hidden" id="asset_name_fill" name="asset_name_fill">
										<input type="hidden" id="id_fill" name="id_fill">
										<input type="text" class="form-control" id="asset_id_fill" name="asset_id_fill" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Picture : </label>
									</div>
									<div class="col-xs-6">
										<a href="#" id="asset_picture_fill" target="_blank"><i class="fa fa-file-image-o"></i> Asset Picture</a>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no_fill" name="asset_no_fill" placeholder="fixed asset number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_cls_fill" name="asset_cls_fill" placeholder="fixed asset Clasification" readonly>
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="section_fill" name="section_fill" placeholder="Section Control" >
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason_fill" name="disposal_reason_fill" readonly></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason (Japanese): <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason_jp_fill" name="disposal_reason_jp_fill" placeholder="input reason (Japanese)" readonly></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Mode : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" name="mode_fill" id="mode_fill" class="form-control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%; display: none" id="quot_div">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Quotation : </span>
									</div>
									<div class="col-xs-6">
										<a href="#" id="quotation_file_fill"><i class="fa fa-file-pdf-o"></i> quotation file</a>
									</div>
								</div>
							</div>

							<div class="col-sx-12" style="padding-bottom: 1%">
								<center><label>- - - - - - - - - - - - &nbsp;  FA SECTION &nbsp; - - - - - - - - - - - -</label></center>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Registration Amount : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="amount_fill" name="amount_fill" placeholder="Registration Amount">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Registration Date : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control datepicker" id="date_fill" name="date_fill" placeholder="Registration Date" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Vendor : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="vendor_fill" name="vendor_fill" placeholder="Vendor">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Net Book Value : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="net_fill" name="net_fill" placeholder="Net Book Value">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Invoice Number : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="invoice_fill" name="invoice_fill" placeholder="Invoice Number">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-4 pull-right">
									<center><button class="btn btn-warning" type="submit" id="update_btn"><i class="fa fa-check"></i> Update Disposal </button></center>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Form Disposal Asset</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master_edit">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="hidden" id="asset_name_fill" name="asset_name_edit">
										<input type="hidden" id="id_edit" name="id_edit">
										<input type="text" id="asset_id_edit" name="asset_id_edit" class="form-control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Picture : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="file" id="asset_picture_edit" name="asset_picture_edit">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no_edit" name="asset_no_edit" placeholder="fixed asset number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_cls_edit" name="asset_cls_edit" placeholder="fixed asset Clasification" readonly>
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="section_edit" name="section_edit" placeholder="Section Control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason_edit" name="disposal_reason_edit"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason (Japanese): <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason_jp_edit" name="disposal_reason_jp_edit" placeholder="input reason (Japanese)"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Mode : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<select class="form-control select4" data-placeholder="select mode" onchange="pilihModeEdit(this)" style="width: 100%" name="mode_edit" id="mode_edit">
											<option value=""></option>
											<option value="SALE">SALE</option>
											<option value="SCRAP">SCRAP</option>
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%; display: none" id="div_quot_edit">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Quotation : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="quotation_file_edit" id="quotation_file_edit">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-4 pull-right">
									<center><button class="btn btn-primary" type="submit" id="edit_btn"><i class="fa fa-check"></i> Save </button></center>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #00a65a">
					<center><h2 style="margin: 0px"><b>Send Mail</b></h2></center>
				</div>
				<div class="modal-body">
					<input type="hidden" id="email_form_id">
					<center style="font-size: 18px">Are you sure want to Send Mail to "<b><span id="approval_name"></span></b>" Again?</center>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success pull-left" onclick="sendMail()"><i class="fa fa-check"></i> YES</button>
					<button class="btn btn-danger" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"></i> NO</button>
				</div>
			</div>
		</div>
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

	var no = 0;
	var nomor_receive = 1;
	var investment_list = [];
	var pic_list = [];
	var list_receive = null;
    var selected_receive = [];


	jQuery(document).ready(function() {
		list_receive = null;
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.select2').select2({
			dropdownParent: $('#createModal'),
		})

		$('.select3').select2({
			dropdownParent: $('#modalFill'),
		})

		$('.select4').select2({
			dropdownParent: $('#modalEdit'),
		})

		drawData();

	});

	function drawData() {
		$.get('{{ url("fetch/non_fixed_asset/disposal") }}', function(result, status, xhr){
			$("#masterBody").empty();
			var body = "";

			list_receive = result.list_receive;

			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+value.id+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.create_at+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.fixed_asset_id+"</td>";
				body += "<td>"+value.section_control+"</td>";
				body += "<td>"+value.pic_incharge+"</td>";
				body += "<td>"+(value.mode || '-')+"</td>";
				if(value.status == "created"){
					body += '<td><label class="label label-danger">Sent To PIC Asset</label></td>';
				} 
				else if(value.status == "pic"){
					body += '<td><label class="label label-warning">Accounting Staff</label></td>';
				}
				else if(value.status == "fa_control"){
					body += '<td><label class="label label-warning">Manager</label></td>';
				}
				else if(value.status == "manager"){
					body += '<td><label class="label label-warning">Manager '+value.pic_incharge+'</label></td>';
				}
				else if(value.status == "manager_disposal"){
					body += '<td><label class="label label-warning">General Manager</label></td>';
				}
				else if(value.status == "gm"){
					body += '<td><label class="label label-warning">Manager Accounting</label></td>';
				}
				else if(value.status == "acc_manager"){
					body += '<td><label class="label label-warning">Director Accounting</label></td>';
				}
				else if(value.status == "director_fin"){
					body += '<td><label class="label label-warning">President Director</label></td>';
				}
				else if(value.status == "presdir"){
					body += '<td><label class="label label-success">Logistic Verification</label></td>';
				}
				else if(value.status == "new_pic"){
					body += '<td><label class="label label-success">Completed</label></td>';
				}

				else if(value.status == "reject"){
					body += '<td><label class="label label-danger"><i class="fa fa-close"></i> Rejected</label></td>';
				}

				else if(value.status == "hold"){
					body += '<td><label class="label label-primary"><i class="fa fa-hand-stop-o"></i> Hold</label></td>';
				}

				else{
					body += '<td></td>';
				}

				body += "<td>";
				body += "<a class='btn btn-danger btn-xs' href='{{ url('files/fixed_asset/report_disposal') }}/Disposal_"+value.form_number+".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp; Report</a>";
				body += "</td>";
				body += "<td>";
				if (value.status == 'pic' && value.last_status == 'pic') {
					if ('{{ strtoupper(Auth::user()->username) }}' == 'PI0905001' || '{{ Auth::user()->role }}'.indexOf("MIS") >= 0) {
						body += "<button class='btn btn-warning btn-xs' onclick='openFillModal("+value.id+")'><i class='fa fa-pencil'></i> Fill</button>&nbsp;";
					}
				}
				
				body += "<button class='btn btn-primary btn-xs' onclick='openEditModal("+value.id+")'><i class='fa fa-pencil'></i> Edit</button>&nbsp;";

				if (value.status != 'hold' && value.status != 'reject' && value.status != 'new_pic') {
					body += "<button class='btn btn-success btn-xs' onclick='openSendMail(\""+value.form_number+"\",\""+value.status+"\")'><i class='fa fa-send'></i> Send Mail</button>&nbsp;";
				}
				body += "</td>";
				body += "</tr>";
			})

			$("#masterBody").append(body);

			var table = $('#masterTable').DataTable({
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
			});

		})
	}

	// function pilihAsset(elem) {
		// var id_asset = $(elem).val();

		// $.each(asset_list, function(index, value){
		// 	if (value.sap_number == id_asset) {
		// 		$("#asset_no").val(id_asset);
		// 		$("#asset_name").val(value.fixed_asset_name);
		// 		$("#asset_cls").val(value.classification_category);
		// 		$("#section").val(value.section);
		// 	}

		// })
	// }

	$("form#form_master").submit(function(e) {
		if( document.getElementById("asset_picture").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Asset Photo');
			return false;
		}

		if( $("#disposal_reason").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason');
			return false;
		}

		if( $("#disposal_reason_jp").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason Japanese');
			return false;
		}

		if( $("#mode").val() == ""){
			openErrorGritter('Error!', 'Please Select Mode');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("post/non_fixed_asset/disposal") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();
				$('#createModal').modal('hide');
				openSuccessGritter('Success', result.message);

				location.reload(true);

			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function pilihMode(elem) {
		if ($(elem).val() == "SCRAP") {
			$("#div_quot").hide();
		} else if($(elem).val() == "SALE") {
			$("#div_quot").show();
		}
	}

	function pilihModeEdit(elem) {
		if ($(elem).val() == "SCRAP") {
			$("#div_quot_edit").hide();
		} else if($(elem).val() == "SALE") {
			$("#div_quot_edit").show();
		}
	}

	function openFillModal(id) {
		$("#modalFill").modal('show');

		var data = {
			id : id
		}

		$.get('{{ url("fetch/fixed_asset/disposal/byId") }}', data, function(result, status, xhr){
			$("#id_fill").val(result.disposal.id);
			$("#asset_name_fill").val(result.disposal.fixed_asset_name);
			$("#asset_id_fill").val(result.disposal.fixed_asset_id+" - "+result.disposal.fixed_asset_name);
			$("#asset_picture_fill").attr('href', "{{ url('files/fixed_asset/disposal') }}/"+result.disposal.new_picture);
			$("#asset_no_fill").val(result.disposal.fixed_asset_id);
			$("#asset_cls_fill").val(result.disposal.clasification_id);
			$("#section_fill").val(result.disposal.section_control);
			$("#disposal_reason_fill").val(result.disposal.reason);
			$("#disposal_reason_jp_fill").val(result.disposal.reason_jp);
			$("#mode_fill").val(result.disposal.mode);
			
			if (result.disposal.mode == 'SALE') {
				$("#quot_div").show();
				$("#quotation_file_fill").attr('href', "{{ url('files/fixed_asset/disposal_quotation') }}/"+result.disposal.quotation_file);
			}


			if (result.data_reg) {
				$("#amount_fill").val(result.data_reg.amount);
				$("#date_fill").val(result.data_reg.request_date);
				$("#vendor_fill").val(result.data_reg.vendor);
				$("#invoice_fill").val(result.data_reg.invoice_number);


				// $('.datepicker').datepicker({
				// 	autoclose: true,
				// 	format: "yyyy-mm-dd",
				// 	todayHighlight: true
				// });
			}
		})
	}

	function openEditModal(id) {
		$("#modalEdit").modal('show');

		var data = {
			id : id
		}

		$.get('{{ url("fetch/fixed_asset/disposal/byId") }}', data, function(result, status, xhr){
			$("#id_edit").val(result.disposal.id);
			$("#asset_name_edit").val(result.disposal.fixed_asset_name);
			$("#asset_id_edit").val(result.disposal.fixed_asset_id+" - "+result.disposal.fixed_asset_name);
			$("#asset_no_edit").val(result.disposal.fixed_asset_id);
			$("#asset_cls_edit").val(result.disposal.clasification_id);
			$("#section_edit").val(result.disposal.section_control);
			$("#disposal_reason_edit").val(result.disposal.reason);
			$("#disposal_reason_jp_edit").val(result.disposal.reason_jp);
			$("#mode_edit").val(result.disposal.mode).trigger('change');
		})
	}

	$("form#form_master_fill").submit(function(e) {
		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("fill/fixed_asset/disposal") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();


				$('#modalFill').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);

			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$("form#form_master_edit").submit(function(e) {
		if ($("#mode_edit").val() == "SALE") {
			if(document.getElementById("quotation_file_edit").files.length == 0 ){
				openErrorGritter('Error!', 'Please Add Quotation File');
				return false;
			}
		}

		if( $("#mode_edit").val() == ""){
			openErrorGritter('Error!', 'Please Select Mode');
			return false;
		}

		if( document.getElementById("asset_picture_edit").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Asset Photo');
			return false;
		}

		if( $("#disposal_reason_edit").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason');
			return false;
		}

		if( $("#disposal_reason_jp_edit").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason Japanese');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("edit/fixed_asset/disposal") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();


				$('#modalEdit').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);

			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function openSendMail(id, approval) {
		var appr = "";

		if (approval == 'created') {
			appr = 'PIC Asset';
		} else if (approval == 'pic') {
			appr = 'Fixed Asset Control';
		} else if (approval == 'fa_control') {
			appr = 'PIC Manager';
		} else if (approval == 'manager') {
			appr = 'Manager PIC InCharge';
		} else if (approval == 'manager_disposal') {
			appr = 'General Manager';
		} else if (approval == 'gm') {
			appr = 'Accounting Manager';
		} else if (approval == 'acc_manager') {
			appr = 'Finance Director';
		} else if (approval == 'director_fin') {
			appr = 'President Director';
		} else if (approval == 'presdir') {
			appr = 'PIC Disposal';
		}


		$("#sendMailModal").modal('show');
		$("#approval_name").text(appr);
		$("#email_form_id").val(id);
	}

	function sendMail() {
		$("#loading").show();
		data = {
			id : $("#email_form_id").val(),
			form : 'Disposal Form'
		}

		$.post('{{ url("send/mail/fixed_asset") }}', data, function(result, status, xhr) {
			$("#loading").hide();
			openSuccessGritter('Success', 'Send Email Successfully');
			$("#sendMailModal").modal("hide");
		})
	}

	function pilihAsset(sj) {
		// console.log(sj);
        if (sj.length > 0) {
            if (!selected_receive.includes(sj)) {
                var tableData = '';

               	var no_po = sj.split('_')[0];
               	var no_pr = sj.split('_')[1];
               	var nama_item = sj.split('_')[2];
               	var surat_jalan = sj.split('_')[3];


                for (var i = 0; i < list_receive.length; i++) {

                    if (list_receive[i].no_po == no_po && list_receive[i].no_pr == no_pr && list_receive[i].nama_item == nama_item && list_receive[i].surat_jalan == surat_jalan) {


                        tableData += '<tr id="stock_'+list_receive[i].no_pr+'">';

                        tableData += '<td style="text-align: center;">';
                        tableData += nomor_receive++;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += list_receive[i].no_po;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += list_receive[i].no_pr;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += list_receive[i].nama_item;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += list_receive[i].qty_receive;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += list_receive[i].date_receive;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += list_receive[i].surat_jalan;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<button id="tes" class="btn btn-danger btn-xs" ';
                        tableData += 'onclick="delete_data(\''+list_receive[i].no_pr+'\')">';
                        tableData += '<i class="fa fa-trash"></i></button></td>';
                        tableData += '</tr>';

                		selected_receive.push(list_receive[i].no_pr);
                    }
                }

                $('#tableBodyNonAsset').append(tableData);
                $("#asset_id").prop('selectedIndex', 0).change();

            } else {
                $("#asset_id").prop('selectedIndex', 0).change();
                openErrorGritter('Error!', 'Data Already selected');
                return false;
            }

            if (selected_receive.length > 0) {
                $('#tableListNonAsset').show();
            } else {
                $('#tableListNonAsset').hide();
            }

        }
    }

    function delete_data(no_pr) {

            for (var i = (selected_receive.length - 1); i >= 0; i--) {
                if (selected_receive[i] == no_pr) {
                    selected_receive.splice(i, 1);
                }
            }

            $("#stock_" +no_pr).remove();
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