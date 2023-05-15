@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">


<style type="text/css">


	.nmpd-grid {border: none; padding: 20px; top: 100px !important}
	.nmpd-grid>tbody>tr>td {border: none;}

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

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}
	.alert1 {
		-webkit-animation: fade 1s infinite;  /* Safari 4+ */
		-moz-animation: fade 1s infinite;  /* Fx 5+ */
		-o-animation: fade 1s infinite;  /* Opera 12+ */
		animation: fade 1s infinite;  /* IE 10+, Fx 29+ */
	}
	.button5 {border-radius: 50%;}
	.button1 {
		background-color: #e67e22;
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}
	.button2 {
		background-color: #a2ff7d;
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}
	.button3 {
		background-color: #94d8ff;
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}


	@-webkit-keyframes fade {
		0%, 49% {
			background-color: #8cd790;
		}
		50%, 100% {
			background-color: #ed5c64;
		}
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	input[type="radio"] {
	}
	.tombol {
		font-weight: bold;
		font-size: 20px;
	}
	.tombol1 {
		font-weight: bold;
		font-size: 20px;
	}
	
	div.input-box  {
		font-size: 1.5rem;
		border: 1px solid #ccc;
		padding: 4px 8px;
		flex: 1;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Request Produksi <span class="text-purple">制作依頼</span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<div class="input-group col-xs-10 col-md-offset-2"> 
				
				<input type="text" style="text-align: center; font-size: 25 padding-bottom: 10px;" class="form-control" id="sloc_name_material" name="sloc_anme_material" value="" placeholder="Sloc Name" readonly>
				
			</div>
		</li>

	</ol>

</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<center>
						<div class="col-xs-12" style="background-color: #6A5ACD;">
							<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">CREATE REQUEST KANBAN MATERIAL</h1>
						</div>
					</center>
				</div>
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-12">
							<div class=" input-group col-md-8 col-md-offset-2">
								<div class="col-md-10">
									<div class="input-group">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											<i class="glyphicon glyphicon-barcode"></i>
										</div>
										<input type="text" style="text-align: center; font-size: 25" class="form-control" id="scan_qrcode_material" name="scan_qrcode_material" placeholder="Scan Material Request" required>
										<div class="input-group-addon" id="icon-serial">
											<i class="glyphicon glyphicon-ok"></i>
										</div>
										<div class="input-group-btn">
											<button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modalScan"><i class="fa fa-qrcode"></i> Scan Camera </button>
										</div>
									</div>
									<br>
								</div>
								<div class="col-md-2" >
									<input id="toggle" type="checkbox" checked data-toggle="toggle" data-on="Normal" data-off="Urgent" data-onstyle="info" data-offstyle="danger">
								</div>
							</div>
						</div>
						<br>
						<div class="col-md-12">
							<div class="col-md-6 col-md-offset-3">
								<div class="col-md-10">
									<div class="input-group">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											<i class="glyphicon glyphicon-list-alt"></i>
										</div>
										<input type="text" class="form-control" id="reason_urgt" name="reason_urgt" placeholder="Reason Material Urgent" required>
									</div>
									<br>
								</div>
							</div>
						</div>

						<br>
						<div class="col-md-12">
							<table id="req_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgba(118, 154, 227); color: white;">
									<tr>
										<th style="width: 2%">No</th>
										<th style="width: 5%">No Kanban</th>
										<th style="width: 5%">GMC</th>
										<th style="width: 10%">Description</th>
										<th style="width: 5%">Lot</th>
										<th style="width: 5%">Cancel</th>
									</tr>
								</thead>
								<tbody id="tableBodyMaterial">
								</tbody>
							</table>
						</div>
						
						<div class="col-xs-12" style="text-align: center;">
							<button class="btn btn-primary" id="confirm" onclick="final(this.id)" style="font-size: 25px; width: 70%; font-weight: bold; padding: 0;margin-top: 20px; ">
								Save
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-12"  >
							<br>
							<div class="col-md-12">
								<table id="history_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
									<thead style="background-color: rgba(118, 154, 227); color: white;">
										<tr>
											<th style="width: 10%">Date</th>
											<th style="width: 10%">PIC Request</th>
											<th style="width: 18%">Department</th>
											<th style="width: 8%">Kode Request</th>
											<th style="width: 8%">Count Kanban</th>
											<th style="width: 25%">Status Request</th>
											<th style="width: 10%">Action</th>
										</tr>
									</thead>
									<tbody id="tableBodyHistory">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalScan" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<center><h3 style="background-color: #ff851b; font-weight: bold;">SCAN QR CODE HERE</h3></center>
				</div>
				<div class="modal-body table-responsive no-padding">
					<div id='scanner' class="col-xs-12">
						<div class="col-xs-12">
							<center>
								<div id="loadingMessage">
									🎥 Unable to access video stream
									(please make sure you have a webcam enabled)
								</div>
								<video autoplay muted playsinline id="video"></video>
								<div id="output" hidden>
									<div id="outputMessage">No QR code detected.</div>
								</div>
							</center>
						</div>									
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalViewMaterial">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL REQUEST KANBAN MATERIAL</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12" id="test1">
							<table id="detail_material_pel" class="table table-striped table-bordered" style="width: 100%; overflow-y: scroll;"> 
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">No Kanban</th>
										<th style="width: 1%;">SlocName</th>
										<th style="width: 1%;">GMC</th>
										<th style="width: 4%;">Description</th>
										<th style="width: 1%;">lot</th>
										<th style="width: 1%;">uom</th>
										<th style="width: 1%;">Quantity Kirim</th>
										<th id="qty_kurangs" style="width: 2%;">Quantity not appropriate</th>
										<th id="pic_ter" style="width: 2%;">PIC Terima</th>
										<th id="status_1" style="width: 2%;">Status</th>
									</tr>
								</thead>
								<tbody id="detail_material_pel_body">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	<div class="modal modal-danger fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<input type="hidden" id="ids">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Confirm Clear Data</h4>
				</div>
				<div class="modal-body">
					Are you sure you want to Clear Data ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
					<a name="modalbuttoncancel" type="button"  onclick="DeleteForm()" class="btn btn-danger">Yes</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="process" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #757ce8;color: white;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Pilih Lokasi Material </h1>
					</div>
					<div class="col-xs-12" id="table_material" style="padding-bottom: 1%; padding-top: 2%;padding-right: 0;padding-left: 0;">
						<center><h4 id="title_proses" style="font-weight: bold; margin-bottom: 10px;font-size: 30px;"></h4></center>
						<table width="100%">
							<tbody align="center" id='btn_name_material'>
								<button class='btn btn-primary' style='width: 19%; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold; height: 50px;'  onclick="tampilbutton('B-Pro')">B-Pro</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('M-Pro')">M-Pro</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Assembly')">Assembly</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Soldering')">Soldering</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Hts')">HTS</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Case')">Case</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Pianica')">Pianica</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Recorder')">Recorder</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Buffing')">Buffing</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Tanpo')">Tanpo</button>

								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Venova')">Venova</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Reed Plate')">Reed Plate</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Plating')">Plating</button>
								<button class='btn btn-primary' style='width: 19%; height: 50px; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold' onclick="tampilbutton('Laquering')">Laquering</button>
							</tbody>            
						</table>
					</div>
					<div class="row" id="lok_material">
						<div class="col-xs-12">
							<center><span style="font-weight: bold; font-size: 18px;">Pilih Lokasi Material</span></center>
						</div>
						<div class="col-xs-12" style="padding-top: 10px">
							<button class="btn btn-primary" id="material_name" style="width: 100%;font-size: 20px; font-weight: bold;" onclick="changeLokasi()">
							</button>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%; margin-right: 10px; padding-top: 2%;padding-right: 0;padding-left: 0;">

						<table style="width: 100%; margin-top: 5px;">
							<tbody align="center" id='body_button'>
							</tbody>            
						</table>
					</div>

				</div>    
			</div>
		</div>
	</div>


	<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="titleModal">Reprint FLO</h4>
				</div>
				<form class="form-horizontal" role="form" method="post" action="{{url('reprint/flo')}}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body" id="messageModal">
						<label>FLO Number</label>
						<select class="form-control select2" name="flo_number_reprint" style="width: 100%;" data-placeholder="Choose a FLO..." id="flo_number_reprint" required>
							<option value=""></option>
						</select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button id="modalReprintButton" type="submit" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<input type="hidden" id="tag_checkbox">


	<div class="modal fade" id="modalAdd">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">EDIT MATERIAL REQUEST</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-12">
								<div class="form-group">
									<label>Request Material Produksi<span class="text-red">*</span></label>
									<input type="hidden" style="width: 100%" class="form-control" id="id_material">
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12">
									<div class="col-xs-2" style="font-weight: bold;">
										GMC
									</div>
									<div class="col-xs-4"style="font-weight: bold;" >
										Descriptions
									</div>
									<div class="col-xs-2" style="font-weight: bold;">
										Kanban No
									</div>
									<div class="col-xs-2" style="font-weight: bold;">
										Quantity Request
									</div>
									<div class="col-xs-2" style="font-weight: bold;">
										Actions
									</div>
								<!-- <div class="col-xs-3">
									<button class="btn btn-primary btn-sm pull-right" onclick="add_employee()"><i class="fa fa-plus"></i>&nbsp; Add</button>
								</div> -->
							</div>
							<div class="col-xs-12" id="div_process">
							</div>
							<div class="modal-footer">
								<div class="col-xs-12" style="padding-top: 20px">
									<button class="btn btn-success btnNext btn-block" style="font-weight: bold;font-size: 20px" onclick="save_data()">Save</button>
								</div>
							</div>
						</div>
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
				<h4 class="modal-title" id="myModalLabel">Konfirmasi Pengecekan Material</h4>
			</div>
			<div class="modal-body">
				Apakah data sudah sesuai?
			</div>
			<div class="modal-footer">
				<input type="hidden" style="width: 100%" class="form-control" id="category">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button onclick="sumbitPelayanan()" href="#" type="button" class="btn btn-success">Save</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="myModalinfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Informasi Jam Request Pelayanan</h4>
			</div>
			<div class="modal-body">
				<!--  <b>
				Pelayanan Shift 1 </b><br>
				Request Pelayanan 1 pukul 07:00 - 09:00<br>
				Pengantaran 1 pukul 09:00 - 12:00<br>
				Request Pelayanan 2 pukul 12:00 - 14:00<br>
				Pengantaran 2 pukul 14:00 - 16:00
				<br><b>
				Pelayanan Shift 2 </b><br>
				Request Pelayanan pukul 17:00 - 19:00<br>
				Pengantaran pukul 19:00 - 21:00<br>
				Request Pelayanan pukul 21:00 - 22.00<br>
				Pengantaran pukul 22:00 - 23:00<br>
 -->
				<b>
				Permintaan Terkahir OFFF </b><br>
				<b>
				Hubungi Pak NURUL </b>
						

		</div>
		<div class="modal-footer">
			<input type="hidden" style="width: 100%" class="form-control" id="category">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

		</div>
	</div>
</div>
</div>

<div class="modal fade" id="myModalCheck">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">CHECK REQUEST KANBAN MATERIAL</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12" id="test1">
						<table id="detail_material_check" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgb(65, 114, 166); color: white;">
								<tr>
									<th style="width: 1%;">No</th>
									<th style="width: 1%;">PIC Pelayanan</th>
									<th style="width: 1%;">Kode Request</th>
									<th style="width: 1%;">No Kanban</th>
									<th style="width: 4%;">GMC</th>
									<th style="width: 4%;">Description</th>
									<th style="width: 1%;">Quantity Request</th>
									<th style="width: 1%;">Quantity Kirim</th>
									<th style="width: 4%;">Check<input onClick="checkAll(this)" type="checkbox" id="checkAllBox"/> </th>
									

								</tr>
							</thead>
							<tbody id="detail-body-check">
							</tbody>
						</table>
					</div>
					<div class="col-md-12" style="padding-bottom: 10px;">
						<label>Note :</label>
						<div class="col-md-12" style="position:center;">
							<label style="margin-top: 12px; text-align: center;"><button class="button1 button5"></button> Material Kosong</label>
							<label style="margin-top: 12px;"><button class="button2 button5"></button> Material Kurang</label>
							<label style="margin-top: 12px;"><button class="button3 button5"></button> Material Lebih</label>
						</div>
						
					</div>

					<div class="col-md-12">
						<button class="btn btn-info pull-left"  style="font-weight: bold; font-size: 1.3vw; width: 100%;" name="test" id="cob" onclick="RevisiForm(this)">Save </button>
						
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
</section>
@stop

@section('scripts')
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>
<script src="<?php echo e(url("js/bootstrap-toggle.min.js")); ?>"></script>

<script>
	var lot;
	var code1 = [];
	var no = 1;
	var test = [];
	var detail_request = [];
	var nums = 1;
	var datas = [];



	jQuery(document).ready(function() {
		$(function () {
			$('.select2').select2()
		});		

		$('#confirm').hide();
		$('body').toggleClass("sidebar-collapse");
		$("#sloc_name_material").val($("#sloc_name_material").val());
		$('#toggle').prop('checked', true).change();
		$('#lok_material').hide();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%; "></table>';
		$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
		$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
		$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
		$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
		$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};


		fetchRequestProd();
		// setInterval(fetchRequestProd, 10000);
		$("#tag_checkbox").val("");
		fetchAll();
		// setInterval(fetchAll, 11000);
		$("#reason_urgt").val("");

		if ($("#sloc_name_material").val() != "") {	
			
		}else{
			$('#process').modal({
				backdrop: 'static',
				keyboard: false
			});

		}
		
		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		var video;

		

		$('#scan_qrcode_material').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {

				if($("#scan_qrcode_material").val().length > 3){
          // scanFloNumber();
          checkCodeQr(video, $("#scan_qrcode_material").val());
          return false;
      }
      else{
      	openErrorGritter('Error!', 'QR Code Tidak Cocok');
      	$("#scan_qrcode_material").val("");

      	audio_error.play();
      }
  }
});

	});

	$("#toggle").change(function(){
		if($(this).prop("checked") == true){
			$('#category').val("Normal");
			$('#reason_urgt').prop('disabled', true);

		}else{
			$('#category').val("Urgent");
			$('#reason_urgt').prop('disabled', false);
			$('#reason_urgt').val('');
			$('#reason_urgt').focus();
		}
	});
	var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');



	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$( "#modalScan" ).on('shown.bs.modal', function(){
		showCheck();
	});

	$('#modalScan').on('hidden.bs.modal', function () {
		videoOff();
		$("#scanner").hide();
		$("#scan_qrcode_material").val("");
	});

	function changeLokasi() {
		$('#lok_material').hide();
		$('#table_material').show();
		$("#material_name").html("Lokasi");
		$("#body_button").hide();

	}

	function final(id){
		if ($('#category').val() == "Normal") {
			if ($("#req_table > tbody > tr").length == 0) {
				
				alert('data masih kosong');
				$('#myModal').modal('hidden');
			}else {
				$('#myModal').modal('show');

			}

		}else{

			if ($("#req_table > tbody > tr").length == 0 || $("#reason_urgt").val() == "") {

				alert('data masih kosong');
				$('#myModal').modal('hidden');
			}else {
				$('#myModal').modal('show');

			}
		}
	}



	function tampilbutton(names) {
		$('#lok_material').show();
		$("#table_material").hide();
		$("#material_name").html(names);
		$("#body_button").show();
		var data = {
			names : names
		}

		$.get('{{ url("fetch/material_list") }}', data, function(result, status, xhr) {
			$("#body_button").empty();
			$("#process").modal('show');
			var body = "";

			body = "<tr>";
			$.each(result.process, function(key, value) {
				body += "<td class='td_tombol' style='width: 16%; font-size: 16px; margin-right: 7px; margin-bottom: 3px; font-weight: bold; height: 50px;'>";
				body +='<a style="width:95%; padding-bottom:10px;" class="btn btn-success tombol" onclick="save(\''+value.sloc_name+'\')">'+value.sloc_name+'</a>';
				body +="</td>";

				if (key % 5 == 0 && key != 0) {
					body += "</tr>";
					body += "<tr>";
				}

				if (key == result.process.length - 1) {
					body += "</tr>";
				}

			});

			$("#body_button").append(body);
		});

	}

	function videoOff() {
		video.pause();
		video.src = "";
		video.srcObject.getTracks()[0].stop();
	}

	function save(material){
		$("#process").modal('hide');
		$("#sloc_name_material").val(material);
		refresh();

	}

	function delShow(id){
		$("#ids").val(id);
		$('#modaldelete').modal('show');
	}

	function DeleteForm() {

		var ids = $('#ids').val();
		var data = {
			ids:ids
		}
		$.post('{{ url("delete/request/prd") }}', data,  function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				$("#modaldelete").modal('hide');
				// location.reload();
				fetchRequestProd();
				
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}


	function showCheck() {
		// $(".modal-backdrop").add();
		$('#scanner').show();

		var vdo = document.getElementById("video");
		video = vdo;
		var tickDuration = 200;
		video.style.boxSizing = "border-box";
		video.style.position = "absolute";
		video.style.left = "0px";
		video.style.top = "0px";
		video.style.width = "400px";
		video.style.zIndex = 1000;

		var loadingMessage1 = document.getElementById("loadingMessage");
		var outputContainer = document.getElementById("output");
		var outputMessage = document.getElementById("outputMessage");

		navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
			video.srcObject = stream;
			video.play();
			setTimeout(function() {tick();},tickDuration);
		});

		function tick(){
			loadingMessage1.innerText = "⌛ Loading video...";

			loadingMessage1.hidden = true;
			video.style.position = "static";

			var canvasElement = document.createElement("canvas");            
			var canvas = canvasElement.getContext("2d");
			canvasElement.height = video.videoHeight;
			canvasElement.width = video.videoWidth;
			canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
			var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
			var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
			if (code) {
				outputMessage.hidden = true;
				// videoOff();
				checkCodeQr(video, code.data);

			}else{
				outputMessage.hidden = false;
			}

			setTimeout(function() {tick();},tickDuration);
		}
	}

	function UpdatePengecekan(element){
		var qtys = [];
		var id = [];
		var kode_request = [];

		var tag = [];
		var tot = $("#detail_material_pel > tbody > tr").length;

		$('.qty').each(function(){
			qtys.push($(this).html());
		});

		$('.idso').each(function(){
			id.push($(this).val());
		});

		$('.dets').each(function(){
			kode_request.push($(this).html());
		});

		$("input[type=checkbox]:checked").each(function() {
			if (this.id.indexOf("All") >= 0) {
			} else {
				tag.push(this.id);
			}
		});

		if(tag.length < tot){
			alert("Ada Material yang belum di check");
			return false;
		}else{
			$('#tag_checkbox').val(tag);

			var data = {
				id : id,
				qty: qtys,
				kode_request : kode_request
			}
			$.post('{{ url("update/pengecekan") }}',data, function(result, status, xhr){
				if(result.status){    
					openSuccessGritter('Success', result.message);
					$('#modalViewMaterial').modal('hide');
        // $('#history_table').DataTable().destroy();
        // $('#tableBodyHistory').DataTable().ajax.reload();

    }
    else{
    	openErrorGritter('Error!', result.message);
    }

});

		}

	}


	function checkCodeQr(video, param) {
		var material_number = param;
		var cod = $("#coba").val();
		var sloc_name = "";

		var lokasi = $("#sloc_name_material").val();

		var data = {
			tag : param,
			lokasi : lokasi
		}

		var date_now = '{{date("H")}}';
		var stat = 0;


		if (date_now >= "03" && date_now <= "04") {
			stat = 1;
		}
		// else if (date_now >= "11" && date_now <= "13") {
		// 	stat = 1;
		// }else if (date_now >= "16" && date_now <= "19") {
		// 	stat = 1;
		// }else if (date_now >= "21" && date_now <= "22") {
		// 	stat = 1;
		// }
		else{
			stat = 0;
			$('#scan_qrcode_material').val('');
			$("#myModalinfo").modal("show");
			$("#modalScan").modal('hide');
			videoOff();
			return false;
		}

		if (stat = 1) {

			$.get('{{ url("fetch/scan/Qrcode") }}', data, function(result, status, xhr){
				if(jQuery.inArray(material_number, code1) !== -1)
				{
				// videoOff();
				$("#modalScan").modal('hide');
				openErrorGritter('Error','Data sudah ada');
				$("#scan_qrcode_material").val("");

			}else{
				openSuccessGritter('Succes','Data belum ada');
				code1.push(material_number);
				$('#confirm').show();
				if(result.status){

					$("#modalScan").modal('hide');

					datas.push(result)
					renderTable()

					openSuccessGritter('Success!', result.message);
					$("#scan_qrcode_material").val("");

					$('.numpad'+no).numpad({
						hidePlusMinusButton : true,
						decimalSeparator : '.'
					});
					no+=1;
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();

				}
			}
		});

		}


	}

	function renderTable(){
		var tableData = "";
		datas.forEach((item, index)=> {
			tableData += '<tr>';
			tableData += '<th class="tes'+(index+1)+'" style="text-align: center;">'+ (index+1) +'</th>';
			tableData += '<th style="text-align: center;">'+ item.datamaterial.no_hako +'</th>';
			tableData += '<th id="lot" style="text-align: center;">'+ item.datamaterial.gmc_material +' <input type="hidden" class="codematerial" value="'+item.datamaterial.barcode+'"> <input type="hidden" class="locmtrl" value="'+item.datamaterial.rcvg_sloc+'"><input type="hidden" class="no_hako" value="'+item.datamaterial.no_hako+'"><input type="hidden" class="uom" value="'+item.datamaterial.uom+'"></th>';
			tableData += '<th style="text-align: center;">'+ item.datamaterial.description +'</th>';
			if (item.datamaterial.keterangan == "MINIMAL" || item.datamaterial.keterangan == "minimal") {
				tableData += '<th class="quantitycheck numpad'+no+'" id="con" style="text-align: center;" value="">'+ item.datamaterial.lot +'</th>';
			}else{
				tableData += '<th class="quantitycheck" id="con" style="text-align: center;">'+ item.datamaterial.lot +'</th>';
			}
			tableData += '<th style="text-align: center;"><a class="btn btn-sm btn-danger" href="javascript:void(0)" id="'+item.datamaterial.id +'" onclick="cancelConfirmation(id)"><i class="glyphicon glyphicon-remove-sign"></i></a></th>'
			tableData += '</tr>';
		})
		$('#tableBodyMaterial').html(tableData);
	}

	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function cancelConfirmation(id){

		if(confirm("Are you sure you want to cancel this request?")){
			var cob ="";
			code1.splice( $.inArray(id), 1 );
			// $('#'+id).closest("tr").remove();
			// var index = datas.indexOf(id);
			// if (index !== -1) {
			// 	datas.splice(index, 1);
			// renderTable();
			// }
			datas = datas.filter(item=>item.datamaterial.id != id);
			renderTable();
			openSuccessGritter('Success Delete');

		}
		else{
			openErrorGritter('Error!');

			return false;
		}
	}


	function cancelConfirmationRequest(id){

		if(confirm("Are you sure you want to cancel this request?")){

			$("#"+id).remove();

			var data = {
				id:id,
			}

			$("#loading").show();

			$.post('{{ url("delete/material/request") }}', data, function(result, status, xhr){
				if (result.status == true) {
					openSuccessGritter("Success","Data Berhasil Dihapus");
					$("#loading").hide();
					// $('#detail-body-check').DataTable().ajax.reload();
				}
				else{
					$("#loading").hide();

					openErrorGritter("Gagal","Data Gagal Dihapus");
				}
			});


		}
		else{
			openErrorGritter('Error!');

			return false;
		}
	}


	function sumbitPelayanan() {
		$('#loading').show();
		var code_material = [];
		var loc_mtrl = [];
		var no_hako = [];
		var quantity_check = [];
		var uom = [];
		var lok_materials = $("#sloc_name_material").val();
		var categorys = $("#category").val();
		var reason_urgt = $("#reason_urgt").val();


		$('.codematerial').each(function(){
			code_material.push($(this).val());
		});

		$('.locmtrl').each(function(){
			loc_mtrl.push($(this).val());
		});
		$('.no_hako').each(function(){
			no_hako.push($(this).val());
		});

		$('.uom').each(function(){
			uom.push($(this).val());
		});

		$('.quantitycheck').each(function(){
			quantity_check.push($(this).html());
		});


		data = {
			code_material : code_material,
			loc_mtrl : loc_mtrl,
			no_hako : no_hako,
			quantity_check : quantity_check,
			lok_materials : lok_materials,
			uoms : uom,
			categorys : categorys,
			reason_urgt : reason_urgt	
		} 

		$.post('{{ url("confirm/request/produksi") }}', data, function(result, status, xhr){
			if(result.status){  
				$('#loading').hide();
				$('#reason_urgt').val('');
				openSuccessGritter('Success', result.message);
				$('#req_table').DataTable().clear();
				$('#req_table').DataTable().destroy();
				code1 = [];
				$('#confirm').hide();
				location.reload(); 
			}
			else{
				$('#loading').hide();
				$('#req_table').DataTable().clear();
				$('#req_table').DataTable().destroy();
				audio_error.play();
				openErrorGritter('Error Waktu Request Pelayanan Berakhir!', result.message);
			}
		});
	}

	var count_alarm = 0;

	function fetchRequestProd(){
		$.get('{{ url("fetch/history/request/prod") }}', function(result, status, xhr){
			if(result.status){
				
				var status = "";

				var statusku = "Waiting,Providing Materials,Waiting for Delivery,Delivery,Material Checking In Production,Finish";

				var detail = "";
				$('#history_table').DataTable().clear();
				$('#history_table').DataTable().destroy();
				$('#tableBodyHistory').html("");
				var tableData = "";
				var info = "";




				for (var i = 0; i < result.prod.length; i++) {
					tableData += '<tr>';
					if(result.prod[i].remark == "open"){
						
						report = "";
						edit = "<a id ='edit' onclick='openEdit(\""+result.prod[i].kode_request+"\");' class='btn btn-warning btn-xs' target='_blank'><i class='fa fa-edit'></i>Edit</a>";
						detail = "Detail";

					}
					else if (result.prod[i].remark == "close"){
						
						edit = "";
						report = "<a id ='report' href='{{ url('fetch/request/produksi') }}/"+result.prod[i].kode_request+"' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-file-pdf-o'></i> Report MOD</a>";
						detail = "Detail";

					}
					else{
						report = "";
					}
					var check = "";
					var name = statusku.split(',');
					var statusp = result.prod[i].pl_status;
					if (statusp == null) {
						var status = []; 

					}else{
						var status = statusp.split(',');
					}


					if (status.length == 4 && result.prod[i].remark != "not close") {
						info = "alert1";
						check = "<a id ='edit' onclick='openCheck(\""+result.prod[i].kode_request+"\");' class='btn btn-warning btn-xs' target='_blank'><i class='fa fa-edit'></i>Check</a>";
						count_alarm++;

					}else{
						info = "";
					}


					console.log(count_alarm);
					if (count_alarm > 0) {

						var times = 10;
						var loop = setInterval(repeat, 2000);

						function repeat() {
							times--;
							if (times === 0) {
								clearInterval(loop);
							}

							alarm_error.play();
						}

						repeat(); 
					}

					tableData += '<td class="'+info+'">'+ result.prod[i].tanggal +'</td>';
					tableData += '<td class="'+info+'">'+ result.prod[i].name +'</td>';

					tableData += '<td class="'+info+'">'+ result.prod[i].department +'</td>';
					tableData += '<td class="'+info+'">'+ result.prod[i].kode_request +'</td>';
					tableData += '<td class="'+info+'">'+ result.prod[i].total +'</td>';



					tableData += '<td class="'+info+'">';
					for(var j = 0; j < name.length; j++){

						if (status.length < j) {
							if (status[j] == 'proses0') {  
								tableData += '<span class="label" style="color: black; background-color: #aee571; margin-bottom:2%; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';
							}
							else if(status[j] == 'check'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'proses1'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'Pengantaran'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  


							}
							else if(status[j] == 'pengecekan material'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}

							else if(status[j] == 'finish'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> ';  
							}
							else{
								tableData += ' &nbsp;<i class="fa fa-caret-right"></i>&nbsp;<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black;">'+name[j]+'</span> '; 
							}
						} else if (status.length == j){
							if (status[j] == 'proses0') {  
								tableData += '<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';
							}
							else if(status[j] == 'check'){
								tableData += '&nbsp;<i class="fa fa-caret-right"></i>&nbsp;<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'proses1'){
								tableData += '&nbsp;<i class="fa fa-caret-right"></i>&nbsp;<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'Pengantaran'){
								tableData += '<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';


							}
							else if(status[j] == 'pengecekan material'){
								tableData += '<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';

							}

							else if(status[j] == 'finish'){
								tableData += '<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span> ';  

							}
							else{
								tableData += '<span class="label" style="color: black; background-color: #3475c9; border: 1px solid black;">'+name[j]+'</span>';

							}
						} else {
							if (status[j] == 'proses0') {  
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';
							}
							else if(status[j] == 'check'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'proses1'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'Pengantaran'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}
							else if(status[j] == 'pengecekan material'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  

							}

							else if(status[j] == 'finish'){
								tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+name[j]+'</span> ';  

							}
							else{
								tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black;">'+name[j]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp;'; 
							}

						}
					}		

					tableData += '</td>';


					if (status[0] == undefined) {
						del = "<a onclick='delShow(\""+result.prod[i].kode_request+"\");' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-trash'></i>Delete</a>";
					}else{
						del ="";
					}



					tableData += "<td class='"+info+"'><a href='javascript:void(0)' class='btn btn-xs btn-primary' onclick='detail_material("+result.prod[i].kode_request+")' id='kode' data-toggle='tooltip' title="+detail+"><i class='fa fa-eye'></i>Detail</a>  "+report+" "+del+" "+check+" </td>";

					tableData += '</tr>';
				}

				$('#tableBodyHistory').append(tableData);

				var table = $('#history_table').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 25, 50, 100, -1 ],
					[ '25 rows', '50 rows', '100 rows', 'Show all' ]
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
					"aaSorting": [[ 0, "desc" ]]
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
}




function openCheck(kode_request){

	$("#myModalCheck").modal("show");


	var data = {
		kode_request1:kode_request
	}
	$.get('{{ url("fetch/joblist/request") }}',data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){

				$('#detail_material_check').DataTable().clear();
				$('#detail_material_check').DataTable().destroy();
				$('#detail-body-check').html("");

				$('#detail_kur').html('');
				var body_lot = "";
				var no = 1;
				var nos = 1;
				var kosong = "";

				$.each(result.list_check, function(key2,value2){

					// if (value2.quantity_request > value2.quantity_check) {
						
					// 	kosong = 'style="background-color: #a2ff7d;"';

					// }else if (value2.quantity_request < value2.quantity_check) {
						
					// 	kosong = 'style="background-color: #94d8ff;"';
					// }else if (value2.quantity_check = "0") {
					// 	kosong = 'style="background-color: rgb(230, 126, 34);"';
					// }else{
					// 	kosong = "";
					// }


					body_lot += '<tr  id="'+value2.id+'">';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;">'+nos+'</td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;">'+value2.name+'</td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;"><input type="hidden" class="kode_request" value="'+value2.kode_request+'">'+value2.kode_request+'<input type="hidden" class="ids" value="'+value2.id+'"></td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;">'+value2.no_hako+'</td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;">'+value2.gmc+'</td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;">'+value2.description+'</td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;">'+value2.quantity_request+'</td>';
					body_lot += '<td onClick="countPicked(this)" id="'+value2.id+'" style="padding-top: 2px; padding-bottom: 2px;" class="quantitycheck numpad'+no+'" id="con" style="text-align: center;">'+ value2.quantity_check +'</td>';
					body_lot += '<td onClick="countPicked(this)" ><input type="checkbox" name="P" id="print_'+value2.id+'"></td>';
					// body_lot += '<th style="text-align: center;"><a class="btn btn-sm btn-danger" href="javascript:void(0)" id="'+value2.id+'" onclick="cancelConfirmationRequest(id)"><i class="glyphicon glyphicon-remove-sign"></i></a></th>'

					body_lot += '</tr>';
					nos++;
				});

				$('#detail-body-check').append(body_lot);
				$('.numpad'+no).numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
				no+=1;

				$('#detail_material_check').DataTable({

					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bAutoWidth": true,
					"processing": true
				});
			}
		}
	});




	$('#loading').hide();
}

function RevisiForm(op){
	// console.log(op);
	var kd_request = [];
	var id = [];
	var quantitychecks = [];
	var tag = [];

	$("input[type=checkbox]:checked").each(function() {
		if (this.id.indexOf("All") >= 0) {

		} else {
			tag.push(this.id);
		}
	});

	if(tag.length < 1){
		alert("All materials have not been selected");
		return false;
	}else{
		$('.ids').each(function(){
			id.push($(this).val());
		});
	}

	if(id.length == tag.length-1){
		if(confirm("Apakah yakin untuk melakukan save data?")){

			$('.quantitycheck').each(function(){
				quantitychecks.push($(this).html());
			});

			$('.kode_request').each(function(){
				kd_request.push($(this).val());
			});

			data = {
				kode_request : kd_request,
				id : id,
				qty : quantitychecks
			} 
			$("#loading").show();

			$.post('{{ url("update/pengecekan") }}', data, function(result, status, xhr){
				if(result.status){ 
					$("#loading").hide(); 
					openSuccessGritter('Success', result.message);
					$('#myModalkurang').hide();
					location.reload();
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			openErrorGritter('Error!');
			return false;
		}

	}else{
		alert("All materials have not been selecteds");
		return false;
	}

}

var total;

function countPicked(element){

	var id = $(element).attr("id");
	var checkDisabled = $('#print_'+id).prop("disabled");
	if(checkDisabled == undefined){

	}
	else{
		var checkVal = $('#print_'+id).is(":checked");
		if(checkVal) {
			total--;
			$('#print_'+ String(id)).prop('checked', false);

		}else{
			total++;
			$('#print_'+ String(id)).prop('checked', true);
		}
	}
	$("#picked").html(total);
} 


function openEdit(flow_name) {
	var data = {
		flow_name : flow_name
	}

	$("#div_process").empty();
	$.get('{{ url("fetch/internal/edit/request") }}', data, function(result, status, xhr){
		if (result.status) {
			$("#modalAdd").modal('show');
			var no = 1;
			$.each(result.flows, function(index, value){
				var proc = "";
				$("#id_material").val(value.kode_requests);

				proc += '<tr><td><div class="col-xs-2" style="margin-top: 5px">';
				proc += '<input type="text" class="form-control gmc" placeholder="GMC" value="'+value.gmc+'" readonly><input type="hidden" class="area" value="'+value.area+'"><input type="hidden" class="area_code" value="'+value.area_code+'"> <input type="hidden" class="sloc_name" value="'+value.sloc_name+'"><input type="hidden" class="loc" value="'+value.loc+'"><input type="hidden" class="loc" value="'+value.loc+'"><input type="hidden" class="status_aktual" value="'+value.status_aktual+'"><input type="hidden" class="pic_produksi" value="'+value.pic_produksi+'"><input type="hidden" class="kode_request" value="'+value.kode_request+'"><input type="hidden" class="id_no" value="'+value.id+'"><input type="hidden" class="lot" value="'+value.lot+'">';
				proc += '</div>';
				proc += '<div class="col-xs-4" style="margin-top: 5px">';
				proc += '<input type="text" class="form-control description" placeholder="Description" value="'+value.description+'" readonly>';
				proc += '</div>';
				proc += '<div class="col-xs-2" style="margin-top: 5px">';
				proc += '<input type="text" class="form-control no_hako" id="no_hako" placeholder="No Hako" value="'+value.no_hako+'" readonly>';
				proc += '</div>';
				proc += '<div class="col-xs-2" style="margin-top: 5px">';
				proc += '<input type="text" style="font-size:20px; width: 100%; height: 35px;text-align: center;" class="quantitycheck numpad'+no+'" input" value="'+value.quantity_request+'" id="qty'+no+'" name="qty'+no+'">'
				proc += '</div>';
				proc += '<div class="col-xs-2" style="margin-top: 5px">';
				proc += '<button class="btn btn-danger btn-xs" onclick="deleteEmp(this)"><i class="fa fa-close"></i></button>';
				proc += '</div>';
				proc += '</td></tr>';
				
				$("#div_process").append(proc);
			})

			$('.numpad'+no).numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});
			no+=1;

			$('.select2').select2({
				dropdownParent: $('#modalAdd'),
				allowClear: true,
			});
		}
	})
}


function deleteEmp(elem) {
	$(elem).closest('tr').remove();
}

function save_data() {
	$('#loading').show();

	if ($(".quantitycheck").val() == "") {
		openErrorGritter("Save Failed", "Please Check Materialss");
		return false;
	}

	if ($('.id_no').length == 0) {
		openErrorGritter("Save Failed", "Please Check Material");
		return false;	
	}
	var ids = [];
	var gmcs = [];
	var areas = [];
	var area_codes = [];
	var sloc_names = [];
	var locs = [];
	var status_aktuals = [];
	var pic_produksis = [];
	var descriptions = [];
	var quantitychecks = [];
	var no_hakos = [];
	var kode_requests = [];
	var lots = [];


	$('.id_no').each(function() {
		ids.push($(this).val());
	});
	$('.gmc').each(function() {
		gmcs.push($(this).val());
	});
	$('.area').each(function() {
		areas.push($(this).val());
	});
	$('.area_code').each(function() {
		area_codes.push($(this).val());
	});
	$('.sloc_name').each(function() {
		sloc_names.push($(this).val());
	});
	$('.loc').each(function() {
		locs.push($(this).val());
	});
	$('.status_aktual').each(function() {
		status_aktuals.push($(this).val());
	});
	$('.pic_produksi').each(function() {
		pic_produksis.push($(this).val());
	});
	$('.description').each(function() {
		descriptions.push($(this).val());
	});
	$('.quantitycheck').each(function() {
		quantitychecks.push($(this).val());
	});


	$('.no_hako').each(function() {
		no_hakos.push($(this).val());
	});
	$('.kode_request').each(function() {
		kode_requests.push($(this).val());
	});
	$('.lot').each(function() {
		lots.push($(this).val());
	});


	var data = {
		ids : ids,
		gmcs  : gmcs,
		areas : areas,
		area_codes : area_codes,
		sloc_names : sloc_names,
		locs : locs,
		status_aktuals : status_aktuals,
		pic_produksis : pic_produksis,
		descriptions : descriptions,
		quantitychecks : quantitychecks,
		no_hakos : no_hakos,
		kode_requests : kode_requests,
		lots : lots
	}

	$.post('{{ url("post/warehouse/request") }}', data, function(result, status, xhr){
		if (result.status) {
			openSuccessGritter('Success', 'PIC Data Has Been Saved');
			$("#proc_name").val("");
			$("#div_employee").empty();
			$("#modalAdd").modal('hide');
			fetchRequestProd();
		}
	})
}

function fetchAll() {
	$.get('{{ url("fetch/detail/request/prd") }}', function(result, status, xhr){
		if (result.status) {
			detail_request = [];
			for (var i = 0; i < result.request_detail.length; i++) {
				detail_request.push({id: result.request_detail[i].id,kode_request:result.request_detail[i].kode_request, gmc: result.request_detail[i].gmc,description: result.request_detail[i].description,lot: result.request_detail[i].lot,sloc_name: result.request_detail[i].sloc_name,quantity_request1: result.request_detail[i].quantity_request, quantity_check: result.request_detail[i].quantity_check,status_aktual: result.request_detail[i].status_aktual,no_hako: result.request_detail[i].no_hako,uom: result.request_detail[i].uom,status_emp: result.request_detail[i].status_mt,qty_req: result.request_detail[i].qty_req,pic_ter: result.request_detail[i].name});

			}
		}
	});
}


function detail_material(kode_request){
	// var kode_request = kode_request;
	$('#modalViewMaterial').modal('show');

	var tableData = "";
	var num=1;
	var st = "";


	$('#detail_material_pel').DataTable().clear();
	$('#detail_material_pel').DataTable().destroy();
	$('#detail_material_pel_body').html("");
	$('input:checkbox').prop('checked', false);
	$('#total').html(detail_request.length);
	$('#picked').html(0);
	total = 0;  

	$.each(detail_request, function(key, value){
		
		if (value.kode_request == kode_request) {

			tableData += '<tr>';
			tableData += '<td><input type="hidden" class="idso" value="'+value.id+'"> <input type="hidden" class="dets" value="'+value.kode_request+'">'+ value.no_hako +'</td>';
			tableData += '<td>'+ value.sloc_name +'</td>';
			tableData += '<td>'+ value.gmc +'</td>';
			tableData += '<td>'+ value.description +'</td>';
			tableData += '<td>'+ value.lot +'</td>';
			tableData += '<td>'+ value.uom +'</td>';


			if(value.status_aktual == "pengecekan material"){
				$('#qty_kurangs').hide();
				$('#pic_ter').hide();
				st = value.qty_req-value.quantity_request1;
				$("#status_1").show();
				$('#btn_saves').show();


				tableData += '<td>'+ value.quantity_check +'</td>';
				tableData += '<td hidden>'+ value.pic_ter +'</td>';
				tableData += '<td hidden>'+ st +'</td>';

				tableData += '<td style="font-weight: bold; width: 1%; text-align: center;" id="td+'+value.id+'"><span class="label" style="color: black; background-color: #C4E538; border: 1px solid black;">Material Checking In Production</span></td>';


			}else if (value.status_aktual == "finish"){
				$('#qty_kurangs').show();
				$('#pic_ter').show();
				st = "";
				$('#btn_saves').hide();
				$("#status_1").show();
				tableData += '<td>'+ value.quantity_check +'</td>';
				tableData += '<td>-</td>';
				tableData += '<td>'+ value.pic_ter +'</td>';

				tableData += '<td style="font-weight: bold; width: 1%; text-align: center;" id="td+'+value.id+'"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">Finish</span></td>';

			}else if (value.status_aktual == "kurang") {
				$('#qty_kurangs').show();
				$('#pic_ter').show();
				st = value.qty_req-value.quantity_request1;
				$('#btn_saves').hide();
				$("#status_1").show();
				tableData += '<td>'+ value.qty_req +'</td>';
				tableData += '<td>-'+ st +'</td>';
				tableData += '<td>-</td>';
				tableData += '<td style="font-weight: bold;  width: 1%; text-align: center;" id="td+'+value.id+'"><span class="label" style="color: black; background-color: #f1c40f; border: 1px solid black;">Belum Sesuai</span></td>';


			}else if (value.status_aktual == "plus") {
				$('#qty_kurangs').show();
				$('#pic_ter').show();

				st = value.quantity_request1-value.qty_req;

				$('#btn_saves').hide();
				$("#status_1").show();
				tableData += '<td>'+ value.qty_req +'</td>';
				tableData += '<td>-</td>';
				
				tableData += '<td>+'+ st +'</td>';

				tableData += '<td style="font-weight: bold;  width: 1%; text-align: center;" id="td+'+value.id+'"><span class="label" style="color: black; background-color: #f1c40f; border: 1px solid black;">Belum Sesuai</span></td>';

			}else if (value.status_aktual == "1") {
				$('#qty_kurangs').show();
				$('#pic_ter').show();
				st = value.quantity_request1;
				$('#btn_saves').hide();
				$("#status_1").show();
				tableData += '<td>-</td>';
				tableData += '<td>-</td>';
				tableData += '<td>'+ st +'</td>';
				tableData += '<td style="font-weight: bold; width: 1%; text-align: center;" id="td+'+value.id+'"><span class="label" style="color: black; background-color: #EA2027; border: 1px solid black;">Kurang</span></td>';
			}
			else if (value.status_aktual == "2") {
				$('#qty_kurangs').show();
				$('#pic_ter').show();
				st = value.quantity_request1;
				$('#btn_saves').hide();
				$("#status_1").show();
				tableData += '<td>-</td>';
				tableData += '<td>-</td>';
				tableData += '<td>'+ st +'</td>';
				tableData += '<td style="font-weight: bold; width: 1%; text-align: center;" id="td+'+value.id+'"><span class="label" style="color: black; background-color: #03adfc; border: 1px solid black;">Lebih</span></td>';
			}

			else{
				$('#qty_kurangs').show();
				$('#pic_ter').show();
				st = "";
				$("#status_1").show();
				$('#btn_saves').hide();
				tableData += '<td>'+ value.qty_req +'</td>';
				tableData += '<td >-</td>';
				tableData += '<td hidden>'+ st +'</td>';

				tableData += '<td style="background-color: #ffea00;" id="td+'+value.id+'" hidden>Sesuai Request</td>';
			}
			tableData += '</tr>';
			// no++;
			$('#qty_'+value.id).addClass('numpad');


		}

	});

$('#detail_material_pel_body').append(tableData);
$('.numpad').numpad({
	hidePlusMinusButton : true,
	decimalSeparator : '.'
});

var table = $('#detail_material_pel').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'lengthMenu': [
	[ 7, 25, 50, -1 ],
	[ '7 rows', '25 rows', '50 rows', 'Show all' ]
	],
	'buttons': {
		buttons:[
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
		}
		]
	},
	'paging': true,
	'lengthChange': true,
	'pageLength': 7,
	'searching': true ,
	'ordering': true,
	'order': [],
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true
});


}


function checkAll(element){
	var id = $(element).attr("id");
	var checkVal = $('#'+id).is(":checked");


	if(checkVal) {
		total = $('#total').text();
		$('input:checkbox').prop('checked', true);
	}else{
		total = 0;
		$('input:checkbox').prop('checked', false);
	}
	$("#picked").html(total);
}



function fillTableSettlement(){
	var data = {
		status : '2',
	}
	$('#table tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
	});
	var table = $('#flo_table').DataTable( {
		'paging'        : true,
		'dom': 'Bfrtip',
		'responsive': true,
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
		'lengthChange'  : true,
		'searching'     : true,
		'ordering'      : true,
		'info'        : true,
		'order'       : [],
		'autoWidth'   : true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true,
		"serverSide": true,
		"ajax": {
			"type" : "post",
			"url" : "{{ url("index/flo") }}",
			"data" : data,
		},
		"columns": [
		{ "data": "flo_number" },
		{ "data": "destination_shortname" },
		{ "data": "st_date" },
		{ "data": "shipment_condition_name" },
		{ "data": "material_number" },
		{ "data": "material_description" },
		{ "data": "actual" },
		{ "data": "updated_at" },
		{ "data": "action" }
		]
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

	$('#flo_table tfoot tr').appendTo('#flo_table thead');
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

function openInfoGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-info',
		image: '{{ url("images/image-unregistered.png") }}',
		sticky: false,
		time: '2000'
	});
}

function refresh(){
	$('#scan_qrcode_material').val('');
	$('#scan_qrcode_material').focus();
}

</script>

@stop