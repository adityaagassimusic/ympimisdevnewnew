@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">


<style type="text/css">
	.nmpd-grid {border: none; padding: 20px; top: 100px !important}
	.nmpd-grid>tbody>tr>td {border: none;}

	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;

	}

	.type_slct {
		color: #17b3cc
	}
	tbody>tr>td{
		text-align:center;
		color: black;
	}
	tfoot>tr>th{
		text-align:center;
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

	.content-wrapper{
		/*color: white;*/
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.gambar {
		width: 100%;
		background-color: none;
		border-radius: 5px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}
	.dataTables_info{
		color: black !important;
	}
	.table1 {
		cursor: pointer;
	}

	.table2 {
		cursor: pointer;
	}


</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>


	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;padding-left: 0px">
			<div class="col-xs-4" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;">
				<span style="font-size: 20px;color: white;width: 100%;" id="periode"></span>
			</div>

			<!-- <div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div> -->


			<div class="pull-right" style="margin: 0px;padding-top: 0px;padding-right: 0px;">
				<button class="btn btn-success btn-sm pull-left" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
					<i class="fa fa-plus"></i>&nbsp;&nbsp;Buat Pekerjaan Lain
				</button>
			</div>
		</div>



		<div style="margin-bottom: 15px;margin-left: 0px;margin-right: 0px;margin-top: 40px; ">
			<div class="box-body">
				<div class="col-xs-12" >
					<div class="col-xs-3" style="padding-left: 10px">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #605ca8;">
								<center>
									<span style="font-size: 22px; font-weight: bold; color: white;"><b>INFORMATION</b></span>
								</center>
							</div>
							<table class="table table-responsive" style="height: 250px;border: 0;background:#2a2a2b">
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Operator</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.0vw;border-top: 1px solid #111"><span class="pull-right" id="isp" style="color: orange;"></span>
									</th>
									<th hidden style="vertical-align: bottom;font-weight: bold;font-size: 1.2vw;border-top: 1px solid #111"><span class="pull-right" id="employee_id" style="color: orange;"></span>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Status Pekerjaan</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.0vw;border-top: 1px solid #111"><span class="pull-right" id="place" style="color: orange;"></span></th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Start</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.0vw;border-top: 1px solid #111"><span class="pull-right" id="address" style="color: orange;"></span></th>
								</tr>
							</table>
						</div>
						<div style="background-color: #605ca8;"> 
							<center>
								<a class="btn btn-info" style="font-weight: bold; font-size: 1.2vw; width: 100%;" onclick="EndJoblist(this)">Finish</a>
							</center>
						</div>
						
					</div>
					<div class="col-xs-9">
						<div class="box-header">
							<center>
								<div class="col-xs-12" style="background-color: #6A5ACD;">
									<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">History Pekerjaan Lain-Lain</h1>
								</div>
							</center>
						</div>
						<div class="box-body">
							<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
								<table id="history_job" class="table table-bordered" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
									<thead style="background-color: rgb(126,86,134);">
										<tr>
											<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No</th>
											<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Name</th>
											<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Joblist</th>
											<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Start Job</th>
											<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">End Job</th>
										</tr>
									</thead>
									<tbody id="body_history_job" style="background-color: rgb(168, 237, 188);">
									</tbody>
								</table>
							</div>
						</div>
					</div>

				</div>

				
			</div>
		</div>

<!-- 
		<div class="box box-solid" style="margin-bottom: 15px;margin-left: 0px;margin-right: 0px;margin-top: 40px; ">
			<div class="box-header">
				<center>
					<div class="col-xs-12" style="background-color: #6A5ACD;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">REQUEST KANBAN MATERIAL PLUS </h1>
					</div>
				</center>
			</div>
			<div class="box-body">
				<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
					<table id="table_plus" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Date</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">PIC Produksi</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Department</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Kode Request</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Count Kanban</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 2%">Status</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 2%">Action</th>
							</tr>
						</thead>
						<tbody id="body_table_plus" style="text-align:center;">

						</tbody>
					</table>
				</div>
			</div>
		</div> -->

		<!-- <div class="box box-solid" style="margin-bottom: 15px;margin-left: 0px;margin-right: 0px;margin-top: 40px; ">
			<div class="box-header">
				<center>
					<div class="col-xs-12" style="background-color: rgb(231, 76, 60)">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">REQUEST KANBAN MATERIAL MINUS</h1>
					</div>
				</center>
			</div>
			<div class="box-body">
				<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
					<table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Date</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">PIC Produksi</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Department</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Kode Request</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 3%">Count Kanban</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 2%">Status</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 2%">Action</th>
							</tr>
						</thead>
						<tbody id="body_table_lot" style="text-align:center;">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	-->

	<div class="gambar" style="margin-top:0px" id="">
		<table style="text-align:center;width:100%">

			<tr>
				<td colspan="6" style="border: 1px solid #fff !important;background-color: #605ca8; font-weight: bold;color: white;font-size: 24px">PELAYANAN PRODUKSI
				</td>
			</tr>
			<tr>
				<td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #e0ba46;color: black;font-size: 20px;width: 16%;">Request Masuk
				</td>
				<td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #e0ba46;color: black;font-size: 20px;width: 16%;">Providing Materials
				</td>
				<td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #e0ba46;color: black;font-size: 20px;width: 16%;">Waiting for Delivery
				</td>
				<td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #e0ba46;color: black;font-size: 20px;width: 16%;">Delivery
				</td>
				<td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #e0ba46;color: black;font-size: 20px;width: 16%;">Checking Material In Production
				</td>
				<td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #e0ba46;color: black;font-size: 20px;width: 16%;">Finish
				</td>
			</tr>
			<tr>
				<td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_check_belum_pel" ><span id="total_check_belum_pel" style="color: white;">0</span></td>
				<td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center; vertical-align:middle" id="total_check_progress_pel" ><span id="total_check_progress_pel" style="color: white;">0</span></td>
				<td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_pengantaran" ><span id="total_pengantaran" style="color: white;">0</span></td>
				<td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_proses_pengantaran" ><span id="total_proses_pengantaran" style="color: white;">0</span></td>
				<td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_check_material_pel" ><span id="total_check_material_pel" style="color: white;">0</span></td>
				<td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_check_finish_pel" ><span id="total_check_finish_pel" style="color: white;">0</span></td>

			</tr>
		</table>
	</div>

</div>

<div class="modal fade" id="modal_but">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #757ce8;color: white;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Request Kanban Materials Production</h1>
				</div>
				<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;padding-right: 0;padding-left: 0;">
					<center><h4 id="title_proses" style="font-weight: bold; margin-bottom: 10px;font-size: 30px;"></h4></center>
					<table width="100%">
						<tbody align="center" id='aktivitas'>
						</tbody>            
					</table>
				</div>   

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailMaterial">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3c8dbc;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL REQUEST KANBAN MATERIAL</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="detail_material_job" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">

								<tr>
									<th>NO</th>
									<th>Kode Request</th>
									<th>PIC Request</th>
									<th>No Kanban</th>
									<th>Gmc</th>
									<th>Description</th>
									<th>Lot</th>
									<th>Sloc Name</th>
									<th>Uom</th>
									<th>Perkiraan Waktu</th>
								</tr>

							</thead>
							<tbody id="detail_material_body_job">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal" onclick="startJob()" style="font-weight: bold; font-size: 1.3vw; width: 100%;">KERJAKAN</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="myModalType2" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3f9e4d;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Finish Request Kanban Material Produksi</h1>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
				<div class="col-md-3">
				</div>
				<div class="col-xs-12">
					<table class="table table-bordered table-striped table-hover" id="example5">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Tanggal</th> 
								<th>PIC Pengambilan Material</th>
								<th>PIC Pengantaran</th> 
								<th>Kode Request</th>
								<th>PIC Produksi</th>
								<th>Status</th>
								<th>Perkiraan Waktu</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="detail-body-pelayanan">

						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="myModalPengambilanMt" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #34c3eb;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Information Request Kanban Material</h1>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
				<div class="col-md-3">
				</div>
				<div class="col-xs-12">
					<table class="table table-bordered table-striped table-hover" id="pengambilan_mt">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>

								<th>Tanggal</th> 
								<th>Kode Request</th>
								<th>PIC Pelayanan</th> 
								<th>PIC Produksi</th>
								<th>Sloc Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="detail_body_pengambilan">

						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="myModalDeliv" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #34c3eb;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Information Material Delivery</h1>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
				<div class="col-md-3">
				</div>
				<div class="col-xs-12">
					<table class="table table-bordered table-striped table-hover" id="delivery_mt">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>

								<th>Date</th> 
								<th>Kode Request</th>
								<th>PIC Pengambilan Material</th> 
								<th>PIC Pengantaran</th> 
								<th>PIC Produksi</th>
								<th>Sloc Name</th>
								<th>Location</th>
							</tr>
						</thead>
						<tbody id="detail_body_delivery">

						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="myModalCheck" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #34c3eb;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Information Material Check Material</h1>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
				<div class="col-md-3">
				</div>
				<div class="col-xs-12">
					<table class="table table-bordered table-striped table-hover" id="check_mt">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>

								<th>Date</th> 
								<th>Kode Request</th>
								<th>PIC Pengambilan Material</th> 
								<th>PIC Pengantaran</th> 
								<th>PIC Produksi</th>
								<th>Location</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody id="detail_body_check">

						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>


<div class="modal fade" id="myModalkurang">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3f9e4d;">
					<h3 style="text-align: center; margin:5px; font-weight: bold; color: white">Detail Request Kanban Material Minus</h3>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="detail_material_pel" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Tanggal</th> 
									<th>PIC Pelayanan</th> 
									<th>Kode Request</th>
									<th>Gmc</th>
									<th>Description</th>
									<th>Lot</th>
									<th>Quantity Kurang</th>
								</tr>
							</thead>
							<tbody id="detail-body-kurang">
							</tbody>
						</table>
					</div>
					<div class="col-md-12">
						<button class="btn btn-info pull-left"  style="font-weight: bold; font-size: 1.3vw; width: 100%;" onclick="RevisiForm(this)">Save </button>
						<!-- <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 49%;" onclick="MaterialAda(this)">Material Ada </button> -->
					</div>

				</div>
			</div>


		</div>
	</div>
</div>

<div class="modal fade" id="myModalplus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3f9e4d;">
					<h3 style="text-align: center; margin:5px; font-weight: bold; color: white">Detail Request Kanban Material Plus</h3>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="detail_material_pluss" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Tanggal</th> 
									<th>PIC Pelayanan</th> 
									<th>Kode Request</th>
									<th>Gmc</th>
									<th>Description</th>
									<th>Lot</th>
									<th>Quantity Plus</th>
								</tr>
							</thead>
							<tbody id="detail-body-plus">
							</tbody>
						</table>
					</div>
					<div class="col-md-12">
						<button class="btn btn-info pull-left"  style="font-weight: bold; font-size: 1.3vw; width: 100%;" onclick="RevisiFormPlus(this)">Save </button>
					</div>

				</div>
			</div>


		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailPel" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #3f9e4d;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Detail Request Kanban Material</h1>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
				<div class="col-md-3">
				</div>
				<div class="col-xs-12">
					<table class="table table-bordered table-striped table-hover" id="detail_material_pel1">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Kode Request</th>
								<th>No Kanban</th>
								<th>GMC</th>
								<th>Description</th>
								<th>Lot</th>
								<th>Quantity Kirim</th>
								<th>Uom</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody id="detail_material_body_pel">
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="modal modal-default fade" id="create_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">BUAT PEKERJAAN LAIN</h1>
				</div>
			</div>
			<input type="text" name="lop" id="lop" value="0" hidden>
			<input type="text" name="tot_ammount" id="tot_ammount" value="" hidden>


			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12" style="padding-bottom: 10px; padding-top: 4px;">
						<div class="col-md-12" style="padding-top: 10px;">
							<div class="col-xs-6">
								<div class="form-group row">
									<label style="color: black;">Date<span class="text-red">*</span></label>
									<div class="input-group date">
										<div class="input-group-addon"> 
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date" name="date" value="<?= date('d F Y')?>" readonly="">
										<input type="hidden" class="form-control pull-right" id="tgl_po" name="tgl_po" value="<?= date('Y-m-d')?>" readonly="">
									</div>
								</div>

							</div>
							<div class="col-xs-6">
								<div class="form-group" id="selectPur">
									<label style="color: black;">Pilih Pekerjaan<span class="text-red">*</span></label>
									<select class="form-control selectPur"  data-placeholder="Choose Freight" name="lain" id="lain" style="width: 100%; color: black;" >
										<option value=""></option>
										<option class="type_slct" value="Cuci Asam"><b style="color:black;">Cuci Asam</b> 
										</option>
										<option value="Bongkar Import">Bongkar Import</option>
										<option value="Kirim Material Incoming Check Qa">Kirim material Incoming Check Qa
										</option>
										<option value="Chorei">Chorei</option>
										<option value="5S">5S</option>
										<option value="Bagi Material">Bagi Material</option>
										<option value="Bongkar Peti">Bongkar Peti</option>
										<option value="Pelayanan Larutan">Pelayanan Larutan</option>
										<option value="Terima Material Vendor">Terima Material Vendor</option>
										<option value="Penerimaan Scrap">Penerimaan Scrap</option>
										<option value="Pengambilan Material QA">Pengambilan Material QA</option>
										<option value="Kembali dari Pengiriman">Kembali dari Pengiriman</option>
										<option value="Persiapan Stoktaking">Persiapan Stoktaking</option>
										<option value="Stoktaking Hari H">Stoktaking Hari H</option>

									</select>
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" style="font-weight: bold; font-size: 1.3vw;" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
				<button class="btn btn-success pull-right"  style="font-weight: bold; font-size: 1.3vw;" name="test" id="cob" onclick="SaveJoblist(this)">Buat Pekerjaan</button>
			</div>
		</div>
	</div>
</div>


</section>
@endsection
@section('scripts')

<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>


<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var detail_request1 = [];
	var detail_request = [];
	var detail_kurang = [];
	var detail_pengambilan_mt = [];
	var detail_delivery_mt = [];
	var detail_check_mt = [];




	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%; "></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};



	jQuery(document).ready(function(){
		$('.select2').select2();
		getOperator();
		detail_material_job_lain();
		// fetchLotStatus();
		drawChart();
		fetchAll();
		fetchDetail();
		// fetchPlusStatus();
		fetchPengambilan();
		fetchDelivery();
		$('.selectPur').select2({
			dropdownParent: $('#selectPur'),
			allowClear:true
		});
	});



	$('.numpad').numpad({
		hidePlusMinusButton : true,
		decimalSeparator : '.'
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchAll() {
		$.get('{{ url("fetch/count/request") }}', function(result, status, xhr){
			if (result.status) {
				detail_request1 = [];
				for (var i = 0; i < result.detail_request.length; i++) {
					detail_request1.push({id: result.detail_request[i].id,kode_request:result.detail_request[i].kode_request, gmc: result.detail_request[i].gmc,description: result.detail_request[i].description,lot: result.detail_request[i].lot,sloc_name: result.detail_request[i].sloc_name,no_hako: result.detail_request[i].no_hako,name: result.detail_request[i].name,uom: result.detail_request[i].uom});
				}
			}
		});
	}

	function fetchPengambilan() {
		$.get('{{ url("fetch/count/pengambilan_mt") }}', function(result, status, xhr){
			if (result.status) {
				detail_pengambilan_mt = [];
				for (var i = 0; i < result.pengambilan_mts.length; i++) {
					detail_pengambilan_mt.push({id: result.pengambilan_mts[i].id,kode_request:result.pengambilan_mts[i].kode_request, name_prd: result.pengambilan_mts[i].NAME,name_wh: result.pengambilan_mts[i].name,sloc_name: result.pengambilan_mts[i].sloc_name,tanggal: result.pengambilan_mts[i].tanggal,pic_pelayanan: result.pengambilan_mts[i].pic_pelayanan});
				}
			}
		});
	}
	function fetchDelivery() {
		$.get('{{ url("fetch/count/pengambilan_mt") }}', function(result, status, xhr){
			if (result.status) {
				detail_delivery_mt = [];
				for (var i = 0; i < result.delivery.length; i++) {
					detail_delivery_mt.push({id: result.delivery[i].id,kode_request:result.delivery[i].kode_request, name_prd: result.delivery[i].NAME,name_wh: result.delivery[i].name,sloc_name: result.delivery[i].sloc_name,tanggal: result.delivery[i].tanggal,pic_pelayanan: result.delivery[i].pic_pelayanan,area: result.delivery[i].area,op_pengantaran: result.delivery[i].names});
				}
				detail_check_mt = [];
				for (var i = 0; i < result.check.length; i++) {
					detail_check_mt.push({id: result.check[i].id,kode_request:result.check[i].kode_request, name_prd: result.check[i].NAME,name_wh: result.check[i].name,sloc_name: result.check[i].sloc_name,tanggal: result.check[i].tanggal,pic_pelayanan: result.check[i].pic_pelayanan,area: result.check[i].area,op_pengantaran: result.check[i].names});
				}


			}
		});
	}



	

	function fetchMtKurang() {
		$.get('{{ url("fetch/count/request") }}', function(result, status, xhr){
			if (result.status) {
				detail_kurang = [];
				for (var i = 0; i < result.detail_request.length; i++) {
					detail_kurang.push({id: result.detail_request[i].id,kode_request:result.detail_request[i].kode_request, gmc: result.detail_request[i].gmc,description: result.detail_request[i].description,lot: result.detail_request[i].lot,sloc_name: result.detail_request[i].sloc_name,no_hako: result.detail_request[i].no_hako,name: result.detail_request[i].name,uom: result.detail_request[i].uom});
				}
			}
		});
	}

	function fetchDetail() {
		$.get('{{ url("fetch/detail/pelayanan/internal") }}', function(result, status, xhr){
			if (result.status) {
				detail_request = [];
				for (var i = 0; i < result.det_gmc_finish.length; i++) {
					detail_request.push({kode_request:result.det_gmc_finish[i].kode_request, gmc: result.det_gmc_finish[i].gmc,description: result.det_gmc_finish[i].description,lot: result.det_gmc_finish[i].lot, remark: result.det_gmc_finish[i].remark, no_hako: result.det_gmc_finish[i].no_hako, uom: result.det_gmc_finish[i].uom,qty_kirim: result.det_gmc_finish[i].qty_kirim});

				}
			}
		});
	}

	

	function SaveJoblist(){
		var joblist =  $('#lain').val();

		if ($('#lain').val() == '') {
			openErrorGritter('Error!', 'Data Tidak Boleh Kosong');
			return false;
		}


		data = {
			joblist : joblist,
		} 

		$("#loading").show();
		$.post('{{ url("post/create/joblist") }}', data, function(result, status, xhr){
			if(result.status){ 
				$("#loading").hide();
				$('#create_modal').hide();
				openSuccessGritter('Success', result.message);
				location.reload();
			}
			else{
				$("#loading").hide();
				openErrorGritter('Error', result.message);
			}



		});

	}

	function getOperator() {

		$.get('{{ url("fetch/create/joblist") }}', function(result, status, xhr){
			if(result.status){ 
				$.each(result.check, function(index, value){
					$('#isp').append().empty();
					$('#isp').html(value.name);

					$('#place').append().empty();
					$('#place').html(value.joblist);

					$('#address').append().empty();
					$('#address').html(value.start_job);

					$('#employee_id').append().empty();
					$('#employee_id').html(value.employee_id);

				});
				$('#periode').html('Date On '+result.monthTitle);

			}
			else{
				$("#loading").hide();
				openErrorGritter('Error', result.message);
			}
		});
	}


	function drawChart() {

		var tanggal = $('#tanggal_from').val();
		var data1 = {
			tanggal : tanggal
		}    

		$.get('{{ url("fetch/display_internal") }}',data1, function(result, status, xhr) {
			if(result.status){
            //Pelayanan Produksi
            var belum_pel = [];
            var pel_masuk = result.data_belum_pel.length;
            belum_pel.push(pel_masuk);
            $("#total_check_belum_pel").empty();


            if (belum_pel > 0) {
            	$('#total_check_belum_pel').text(belum_pel).css("background-color","rgb(170, 171, 97)",'important');
            	$("#total_check_belum_pel").text(belum_pel).css("color", "white",'important');

            }
            else{
            	$("#total_check_belum_pel").text(belum_pel).css("background-color", "white",'important');
            	$("#total_check_belum_pel").text(belum_pel).css("color", "black",'important');

            }
            req_masuk_pelayanan = result.data_belum_pel;

            var progress_pel = [];
            var pel_progress = result.data_progress_pel.length;
            progress_pel.push(pel_progress);
            $("#total_check_progress_pel").empty();
            if (pel_progress > 0) {
            	$('#total_check_progress_pel').text(pel_progress).css("background-color","#04b521",'important');
            	$("#total_check_progress_pel").text(pel_progress).css("color", "white",'important');
            }
            else{   
            	$("#total_check_progress_pel").text(pel_progress).css("background-color", "white",'important');
            	$("#total_check_progress_pel").text(pel_progress).css("color", "black",'important');            
            }
            req_progress_pelayanan = result.data_progress_pel;

            var pengantaran_pel = [];
            var pel_progress = result.data_pengantaran.length;
            pengantaran_pel.push(pel_progress);
            $("#total_pengantaran").empty();
            if (pel_progress > 0) {
            	$('#total_pengantaran').text(pengantaran_pel).css("background-color","#04b521",'important');
            	$("#total_pengantaran").text(pengantaran_pel).css("color", "white",'important');
            }
            else{
            	$('#total_pengantaran').text(pengantaran_pel).css("background-color","white",'important');
            	$("#total_pengantaran").text(pengantaran_pel).css("color", "black",'important');

            }
            req_progress_pelayanan = result.data_progress_pel;

            var pengantaran_procces = [];
            var process_pen = result.proccess_pengantaran.length;
            pengantaran_procces.push(process_pen);
            $("#total_proses_pengantaran").empty();

            if (process_pen > 0) {
            	$('#total_proses_pengantaran').text(pengantaran_procces).css("background-color","#04b521",'important');
            	$("#total_proses_pengantaran").text(pengantaran_procces).css("color", "white",'important');
            }
            else{
            	$('#total_proses_pengantaran').text(pengantaran_procces).css("background-color","white",'important');
            	$("#total_proses_pengantaran").text(pengantaran_procces).css("color", "black",'important');

            }

            req_progress_pelayanan = result.data_progress_pel;

            var finish_pel = [];
            var pel_finish = result.data_finish_pel.length;
            finish_pel.push(pel_finish);
            $("#total_check_finish_pel").empty();
            if (pel_finish > 0) {
            	$('#total_check_finish_pel').text(finish_pel).css("background-color","#3f9e4d",'important');
            	$("#total_check_finish_pel").text(finish_pel).css("color", "white",'important');
            }
            else{
            	$('#total_check_finish_pel').text(finish_pel).css("background-color","white",'important');
            	$("#total_check_finish_pel").text(finish_pel).css("color", "black",'important');

            }

            req_finish_pelayanan = result.data_finish_pel;

            var check_pel= [];
            var pel_check = result.data_checks_pelayanan.length;
            check_pel.push(pel_check);
            $("#total_check_material_pel").empty();
            if (pel_check > 0) {
            	$('#total_check_material_pel').text(check_pel).css("background-color","#e67e22",'important');
            	$("#total_check_material_pel").text(check_pel).css("color", "white",'important');
            }
            else{
            	$('#total_check_material_pel').text(check_pel).css("background-color","white",'important');
            	$("#total_check_material_pel").text(check_pel).css("color", "black",'important');

            }

            
            var elem_total_check_pel_masuk = document.getElementById('total_check_belum_pel');

            elem_total_check_pel_masuk.addEventListener('click', function(){
            	requestMaterial();
            });

            var elem_total_check_providing = document.getElementById('total_check_progress_pel');

            elem_total_check_providing.addEventListener('click', function(){
            	ShowDetailPengambilanMt();
            });

            var elem_total_pengantaran = document.getElementById('total_pengantaran');

            elem_total_pengantaran.addEventListener('click', function(){
            	window.location.href = "{{secure_url('index/pengantaran')}}";

            });


            var elem_total_check_deliv = document.getElementById('total_proses_pengantaran');

            elem_total_check_deliv.addEventListener('click', function(){
            	ShowDetailDeliv();
            });

            

            var elem_total_check_mt_prd = document.getElementById('total_check_material_pel');

            elem_total_check_mt_prd.addEventListener('click', function(){
            	ShowDetailCheck();
            });

            var elem_total_check_pel_finish = document.getElementById('total_check_finish_pel');

            elem_total_check_pel_finish.addEventListener('click', function(){
            	ShowDetailPelayanan('req_finish_pelayanan', 'Finish Pelayanan');
            });



        } else{
        	alert('Gagal');
        }
    })
}

function fetchLotStatus() {
	$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

	var data = {
		date_from:$('#date_from').val(),
		date_to:$('#date_to').val(),
	}
	$.get('{{ url("fetch/joblist/request") }}',data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){

				// $('#body_table_lot').html("");
				// var body_lot = "";
				// var num = 1; 

				// $.each(result.list_request, function(key2,value2){

				// 	body_lot += '<tr>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+num+'</td>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.tanggal+'</td>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.name+'</td>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.department+'</td>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.kode_request+'</td>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.total+'</td>';
				// 	body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.status+'</td>';
				// 	body_lot += '<td ><a href="javascript:void(0)" class="btn btn-xs btn-primary" onclick="detail_material_kurang(\''+value2.kode_request+'\')" id="kode" data-toggle="tooltip" title="Check"><i class="fa fa-eye"></i>Check</a></td>';

				// 	body_lot += '</tr>';
				// 	num++;
				// });

				// $('#body_table_lot').append(body_lot);

				$('#periode').html('Date On '+result.monthTitle);
			}
		}
	});
}


function fetchPlusStatus() {
	// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

	var data = {
		date_from:$('#date_from').val(),
		date_to:$('#date_to').val(),
	}
	$.get('{{ url("fetch/joblist/request") }}',data, function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){

				$('#body_table_plus').html("");
				var body_lot = "";
				var num = 1; 

				$.each(result.list_request_plus, function(key2,value2){

					body_lot += '<tr>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+num+'</td>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.tanggal+'</td>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.name+'</td>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.department+'</td>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.kode_request+'</td>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.total+'</td>';
					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.status+'</td>';
					body_lot += '<td ><a href="javascript:void(0)" class="btn btn-xs btn-primary" onclick="detail_material_plus(\''+value2.kode_request+'\')" id="kode" data-toggle="tooltip" title="Check"><i class="fa fa-eye"></i>Check</a></td>';

					body_lot += '</tr>';
					num++;
				});

				$('#body_table_plus').append(body_lot);
				// $('#periode').html('Date On '+result.monthTitle);
			}
		}
	});
}

function requestMaterial() {
	$('#modal_but').show();
        // var url = '{{ url("index/activity_list/filter/") }}';
        // var urlnew = url + '/' + id + '/' + no + '/' + frequency;
        $.get('{{ url("fetch/count/request") }}', function(result, status, xhr){
        	if(result.status){
        		$('#aktivitas').empty();
        		var aktivitas = "";
        		var color = "";
        		var stat_mt = "";



        		$.each(result.count_material, function(key, value) {
        			aktivitas += '<div class="col-xs-3">';
        			if (value.remark == "URGENT") {
        				color = "btn-danger";
        				stat_mt = "URGENT";
        			}else{
        				color = "btn-success"
        				stat_mt = "NORMAL";
        			}
        			// if (key == 0) {
        				aktivitas += "<button class='btn "+color+"' onclick='detail(\""+value.kode_request+"\")' style='margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px'>Kode Request : "+value.kode_request+" <br><b style='font-size: 12px'>Sloc Name : "+value.sloc_name+"</b><br><b style='font-size: 15px'>Total Material : "+value.total+"</b><br><b style='font-size: 15px'>"+stat_mt+"</b> </button>";
        			// }else{
        			// 	aktivitas += "<button disabled class='btn "+color+"' onclick='detail(\""+value.kode_request+"\")' style='margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px'>Kode Request : "+value.kode_request+" <br><b style='font-size: 15px'>Sloc Name : "+value.sloc_name+"</b><br><b style='font-size: 15px'>Total Material : "+value.total+"</b> </button>";
        			// }
        			aktivitas += '</div>';
        		});

        		$('#aktivitas').append(aktivitas);
        		$('#loading').hide();
        		$("#modal_but").modal('show');
        	} else {
        		audio_error.play();
        		$('#loading').show();
        	}
        });
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
   


    function detail_material_job_lain() {
    	

    	$.get('{{ url("fetch/create/joblist/lain") }}', function(result, status, xhr) {
    		if(xhr.status == 200){
    			if(result.status){

    				$('#history_job').DataTable().clear();
    				$('#history_job').DataTable().destroy();
    				$('#body_history_job').html("");

    				var body_lot = "";
    				var no = 1;

    				$.each(result.joblist_lain, function(key2,value2){

    					body_lot += '<tr>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+no+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.name+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.joblist+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.start_job+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.end_job+'</td>';
    					body_lot += '</tr>';
    					no++;
    				});

    				$('#body_history_job').append(body_lot);
    				no+=1;

    				var table = $('#history_job').DataTable({
    					'dom': 'Bfrtip',
    					'lengthMenu': [
    					[ 5, 10, 25, 50, -1 ],
    					[ '5 rows', '10 rows', '25 rows', '50 rows', 'Show all' ]
    					],
    					'buttons': {
    						buttons:[
    						{
    							extend: 'pageLength',
    							className: 'btn btn-default',
    						}
    						]
    					},
    					'paging': true,
    					'lengthChange': true,
    					'pageLength': 10,
    					'searching'     : true,
    					'ordering'      : true,
    					'order': [],
    					'info'          : true,
    					'autoWidth'     : true,
    					"sPaginationType": "full_numbers",
    					"bJQueryUI": true,
    					"bAutoWidth": false,
    				});
    			}
    		}
    	});



    }
    function detail_material_plus(kode_request) {
    	$("#myModalplus").modal("show");

    	var data = {
    		kode_request:kode_request
    	}
    	$.get('{{ url("fetch/joblist/request") }}',data, function(result, status, xhr) {
    		if(xhr.status == 200){
    			if(result.status){

    				$('#detail_material_pluss').DataTable().clear();
    				$('#detail_material_pluss').DataTable().destroy();
    				$('#detail-body-plus').html("");

    				var body_lot = "";
    				var no = 1;

    				$.each(result.list_mt_plus, function(key2,value2){

    					body_lot += '<tr>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.tanggal+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.name+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;"><input type="hidden" class="kode_request" value="'+value2.kode_request+'">'+value2.kode_request+'<input type="hidden" class="ids" value="'+value2.id+'"></td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.gmc+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.description+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.lot+'</td>';
    					body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;" class="quantitycheck numpad'+no+'" id="con" style="text-align: center;">'+ value2.quantity_request +'</td>';
    					body_lot += '</tr>';
    					// num++;?
    				});

    				$('#detail-body-plus').append(body_lot);
    				$('.numpad'+no).numpad({
    					hidePlusMinusButton : true,
    					decimalSeparator : '.'
    				});
    				no+=1;
    			}
    		}
    	});



    	$('#detail_material_pluss').DataTable({
    		'dom': 'Bfrtip',
    		'responsive':true,
    		'lengthMenu': [
    		[ 10, 25, 50, -1 ],
    		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
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
    		"processing": true
    	});
    	$('#loading').hide();
    }

    function EndJoblist(){
    	if(confirm("Apakah anda sudah yakin?")){


    		if ($("#isp").html() == "" || $("#place").html() == "" || $("#address").html() == "") {
        		audio_error.play();
    		    openErrorGritter('Error', 'Anda Belum Memilih Pekerjaan');
    			return false;
    		}

    		data = {
    			employee_id : $('#employee_id').html(),
    			joblist : $('#place').html()

    		} 

    		$("#loading").show();
    		$.post('{{ url("post/update/operator") }}', data, function(result, status, xhr){
    			if(result.status){ 
    				$("#loading").hide(); 
    				location.reload();
    				openSuccessGritter('Success', result.message);
    			}
    			else{
    				$("#loading").hide();
    				openErrorGritter('Error', 'Update Gagal');
    			}
    		});

    	}else{
    		return false;
    	}
    }


    function RevisiForm(){
    	if(confirm("Apakah anda sudah yakin?")){
    		var kd_request = [];
    		var id = [];
    		var quantitychecks = [];

    		$('.quantitycheck').each(function(){
    			quantitychecks.push($(this).html());
    		});


    		$('.kode_request').each(function(){
    			kd_request.push($(this).val());
    		});
    		$('.ids').each(function(){
    			id.push($(this).val());
    		});

    		data = {
    			kode_request : kd_request,
    			id : id,
    			quantitychecks : quantitychecks
    		} 
    		$("#loading").show();
    		$.post('{{ url("post/revisi/mod") }}', data, function(result, status, xhr){
    			if(result.status){ 
    				$("#loading").hide(); 
    				location.reload();
    				openSuccessGritter('Success', result.message);
    				$('#myModalkurang').hide();
    			}
    			else{
    				$("#loading").hide();
    				openErrorGritter('Error', result.message);
    			}
    		});
    	}else{
    		return false;
    	}
    }

    function RevisiFormPlus(){
    	if(confirm("Apakah anda sudah yakin?")){
    		var kd_request = [];
    		var id = [];
    		var quantitychecks = [];

    		$('.quantitycheck').each(function(){
    			quantitychecks.push($(this).html());
    		});

    		console.log(quantitychecks);

    		$('.kode_request').each(function(){
    			kd_request.push($(this).val());
    		});
    		$('.ids').each(function(){
    			id.push($(this).val());
    		});

    		data = {
    			kode_request : kd_request,
    			id : id,
    			quantitychecks : quantitychecks
    		} 
    		$("#loading").show();
    		$.post('{{ url("post/revisi/mod/plus") }}', data, function(result, status, xhr){
    			if(result.status){ 
    				$("#loading").hide(); 
    				openSuccessGritter('Success', result.message);
    				$('#myModalkurang').hide();
    				location.reload();
    			}
    			else{
    				$("#loading").hide();
    				openErrorGritter('Error', result.message);
    				location.reload();

    			}
    		});
    	}else{
    		return false;
    	}
    }
    function detail(id) {
    	$("#modalDetailMaterial").modal("show");
    	$('#detail_material_job').DataTable().clear();
    	$('#detail_material_job').DataTable().destroy();
    	var data = {
    		id:id
    	}

    	$('#detail_material_job').DataTable().destroy();
    	$('#detail_material_body_job').html('');
    	var tableLogBody = "";
    	var no = 1;

    	$.each(detail_request1, function(key, value){
    		if (value.kode_request == id) {
    			tableLogBody += '<tr>';
    			tableLogBody += '<td>'+no+'</td>';
    			tableLogBody += '<td>'+value.kode_request+'</td>';
    			tableLogBody += '<td>'+value.name+'</td>';
    			tableLogBody += '<td>'+value.no_hako+'</td>';
    			tableLogBody += '<td>'+value.gmc+'<input type="hidden" class="koderequset" value="'+value.kode_request+'"></td>';
    			tableLogBody += '<td>'+value.description+'</td>';
    			tableLogBody += '<td>'+value.lot+'</td>';
    			tableLogBody += '<td>'+value.sloc_name+'</td>';
    			tableLogBody += '<td>'+value.uom+'</td>';
    			tableLogBody += '<td>0</td>';
    			tableLogBody += '</tr>';
    			no++;
    		}
    	});
    	$('#detail_material_body_job').append(tableLogBody);

    	$('#detail_material_job').DataTable({

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
    	$('#loading').hide();
    }

    function startJob() {

    	var kode_request = [];
    	$("#loading").show(); 
    	$('.koderequset').each(function(){
    		kode_request.push($(this).val());
    	});

    	data = {
    		kode_request : kode_request
    	} 
    	$.post('{{ url("update/pelayanan/proses1") }}', data, function(result, status, xhr){
    		if(result.status){ 
    			$("#loading").hide(); 
    			openSuccessGritter('Success', result.message);
    			$('#modalDetailPel').hide();
    			window.location.href = "{{secure_url('index/detail')}}/"+kode_request[0];
    		}
    		else{
    			$("#loading").hide();
    			audio_error.play();
    			openErrorGritter('Error', result.message);
    		}
    	});
    }
    function ShowDetailCheck(kategori1, status_pekl) {
    	$("#myModalCheck").modal("show");
    	var tanggals = $('#tanggal_from').val();

    	$('#check_mt').DataTable().clear();
    	$('#check_mt').DataTable().destroy();
    	$('#detail_body_check').html("");

    	var tableLogBody = '';

    	$.each(detail_check_mt, function(key, value){
    		tableLogBody += '<tr>';
    		tableLogBody += '<td>'+value.tanggal+'</td>';
    		tableLogBody += '<td>'+value.kode_request+'</td>';
    		tableLogBody += '<td>'+value.name_wh+'</td>';
    		tableLogBody += '<td>'+value.op_pengantaran+'</td>';
    		tableLogBody += '<td>'+value.name_prd+'</td>';
    		tableLogBody += '<td>'+value.area+'</td>';
    		tableLogBody += '<td style="font-weight: bold; width: 1%; text-align: center;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">Belum di Check</span></td>';
    		tableLogBody += '</tr>';


    	});
    	$('#detail_body_check').append(tableLogBody);

    	var tableList = $('#check_mt').DataTable({
    		'dom': 'Bfrtip',
    		'responsive':true,
    		'lengthMenu': [
    		[ 5, 10, 25, -1 ],
    		[ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
    		'paging': true,
    		'lengthChange': true,
    		'pageLength': 5,
    		'searching': true,
    		'ordering': true,
    		'order': [],
    		'info': true,
    		'autoWidth': true,
    		"sPaginationType": "full_numbers",
    		"bJQueryUI": true,
    		"bAutoWidth": false,
    		"processing": true,
    		"aaSorting": [[ 0, "desc" ]]

    	});
    	
    }



    function ShowDetailDeliv(kategori1, status_pekl) {
    	$("#myModalDeliv").modal("show");
    	var tanggals = $('#tanggal_from').val();

    	data = {
    		kategori1 : kategori1,
    		tanggals : tanggals
    	}

    	$('#delivery_mt').DataTable().clear();
    	$('#delivery_mt').DataTable().destroy();
    	$('#detail_body_delivery').html("");

    	var tableLogBody = '';

    	$.each(detail_delivery_mt, function(key, value){
    		tableLogBody += '<tr>';
    		tableLogBody += '<td>'+value.tanggal+'</td>';
    		tableLogBody += '<td>'+value.kode_request+'</td>';
    		tableLogBody += '<td>'+value.name_wh+'</td>';
    		tableLogBody += '<td>'+value.op_pengantaran+'</td>';
    		tableLogBody += '<td>'+value.name_prd+'</td>';
    		tableLogBody += '<td>'+value.sloc_name+'</td>';
    		tableLogBody += '<td>'+value.area+'</td>';    		
    		tableLogBody += '</tr>';


    	});
    	$('#detail_body_delivery').append(tableLogBody);

    	var tableList = $('#delivery_mt').DataTable({
    		'dom': 'Bfrtip',
    		'responsive':true,
    		'lengthMenu': [
    		[ 5, 10, 25, -1 ],
    		[ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
    		'paging': true,
    		'lengthChange': true,
    		'pageLength': 5,
    		'searching': true,
    		'ordering': true,
    		'order': [],
    		'info': true,
    		'autoWidth': true,
    		"sPaginationType": "full_numbers",
    		"bJQueryUI": true,
    		"bAutoWidth": false,
    		"processing": true,
    		"aaSorting": [[ 0, "desc" ]]

    	});
    	
    }

    function ShowDetailPengambilanMt(kategori1, status_pekl) {
    	$("#myModalPengambilanMt").modal("show");
    	var tanggals = $('#tanggal_from').val();

    	data = {
    		kategori1 : kategori1,
    		tanggals : tanggals
    	}

    	$('#pengambilan_mt').DataTable().clear();
    	$('#pengambilan_mt').DataTable().destroy();
    	$('#detail_body_pengambilan').html("");

    	var tableLogBody = '';
    	console.log(window.user = {!! auth()->user()->id !!});

    	$.each(detail_pengambilan_mt, function(key, value){
    		tableLogBody += '<tr>';
    		tableLogBody += '<td>'+value.tanggal+'</td>';
    		tableLogBody += '<td>'+value.kode_request+'</td>';
    		tableLogBody += '<td>'+value.name_wh+'</td>';
    		tableLogBody += '<td>'+value.name_prd+'</td>';
    		tableLogBody += '<td>'+value.sloc_name+'</td>';
    		if (value.id == {!! auth()->user()->id !!}) {
    			tableLogBody += '<td style="text-align:center;" id="butview_"> <a class="btn btn-warning" id="kode" href="{{ secure_url("index/detail/") }}/'+value.kode_request+'" style="border-color: green;">Lanjutkan</a></td>';

    		}else{
    			tableLogBody += '<td style="text-align:center;" id="butview_">-</td>';	
    		}
    		
    		tableLogBody += '</tr>';


    	});
    	$('#detail_body_pengambilan').append(tableLogBody);

    	var tableList = $('#pengambilan_mt').DataTable({
    		'dom': 'Bfrtip',
    		'responsive':true,
    		'lengthMenu': [
    		[ 10, 25, -1 ],
    		[ '10 rows', '25 rows', 'Show all' ]
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
    		'paging': true,
    		'lengthChange': true,
    		'pageLength': 25,
    		'searching': true,
    		'ordering': true,
    		'order': [],
    		'info': true,
    		'autoWidth': true,
    		"sPaginationType": "full_numbers",
    		"bJQueryUI": true,
    		"bAutoWidth": false,
    		"processing": true,
    		"aaSorting": [[ 0, "desc" ]]

    	});



    	
    }

    function ShowDetailPelayanan(kategori1, status_pekl) {
    	$("#myModalType2").modal("show");
    	var tanggals = $('#tanggal_from').val();

    	data = {
    		kategori1 : kategori1,
    		tanggals : tanggals
    	}

    	$.get('{{ url("fetch/detail/pelayanan/internal") }}', data, function(result, status, xhr){
    		if(result.status){

    			$('#example5').DataTable().clear();
    			$('#example5').DataTable().destroy();
    			$('#detail-body-pelayanan').html("");
    			$('#judul_table_type2').append().empty();
    			$('#judul_table_type2').append('<center><b>Detail Material "'+status_pekl+'"</b> </center>'); 

    			var body = '';
    			count = 1;
    			for (var i = 0; i < result.detail_pels.length; i++) { 
    				body += '<tr>';
    				body += '<td>'+ result.detail_pels[i].tanggal +'</td>';
    				body += '<td>'+ result.detail_pels[i].name +'</td>';
    				body += '<td>'+ result.detail_pels[i].name_peng +'</td>';
    				body += '<td>'+ result.detail_pels[i].kode_request +'</td>';
    				body += '<td>'+ result.detail_pels[i].namess +'</td>';
    				body += '<td>'+ result.detail_pels[i].total +'</td>';
    				body += '<td>0</td>';

    				body += '<td style="text-align:center;" id="butview_'+count+'"> <a class="btn btn-info" id="kode" onclick="detail_gmc_pel('+result.detail_pels[i].kode_request+')" style="border-color: green;"><i class="fa fa-eye"></i></a></td>';
    				body += '</tr>';
    			}
    			$('#detail-body-pelayanan').append(body);

    			var tableList = $('#example5').DataTable({
    				'dom': 'Bfrtip',
    				'responsive':true,
    				'lengthMenu': [
    				[ 5, 10, 25, -1 ],
    				[ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
    				'paging': true,
    				'lengthChange': true,
    				'pageLength': 5,
    				'searching': true,
    				'ordering': true,
    				'order': [],
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

    function detail_gmc_pel(kode){
    	$('#modalDetailPel').modal('show');
    	var tanggal = $('#tanggal_from').val();

    	$('#detail_material_pel1').DataTable().clear();
    	$('#detail_material_pel1').DataTable().destroy();
    	$('#detail_material_body_pel').html("");
    	var tableData = "";
    	var num=1;

    	$.each(detail_request, function(key, value){
    		if (value.kode_request == kode) {
    			tableData += '<tr>';
    			tableData += '<td>'+value.kode_request+'</td>';
    			tableData += '<td>'+value.no_hako+'</td>';
    			tableData += '<td>'+value.gmc+'</td>';
    			tableData += '<td>'+value.description+'</td>';
    			tableData += '<td>'+value.lot+'</td>';
    			tableData += '<td>'+value.qty_kirim+'</td>';
    			tableData += '<td>'+value.uom+'</td>';
    			tableData += '<td>'+ value.remark +'</td>';
    			tableData += '</tr>';
    			num++;
    		}
    	});

    	$('#detail_material_body_pel').append(tableData);


    	var tableList = $('#detail_material_pel1').DataTable({
    		'dom': 'Bfrtip',
    		'responsive':true,
    		'lengthMenu': [
    		[ 5, 10, 25, -1 ],
    		[ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
    			]
    		},
    		'paging': true,
    		'lengthChange': true,
    		'pageLength': 5,
    		'searching': true,
    		'ordering': true,
    		'order': [],
    		'info': true,
    		'autoWidth': true,
    		"sPaginationType": "full_numbers",
    		"bJQueryUI": true,
    		"bAutoWidth": false,
    		"processing": true,
    		"aaSorting": [[ 0, "desc" ]]

    	});

    }

    function addZero(i) {
    	if (i < 10) {
    		i = "0" + i;
    	}
    	return i;
    }

    function getActualFullDate() {
    	var d = new Date();
    	var day = addZero(d.getDate());
    	var month = addZero(d.getMonth()+1);
    	var year = addZero(d.getFullYear());
    	var h = addZero(d.getHours());
    	var m = addZero(d.getMinutes());
    	var s = addZero(d.getSeconds());
    	return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
    }

    function getActualDate() {
    	var d = new Date();
    	var day = addZero(d.getDate());
    	var month = addZero(d.getMonth()+1);
    	var year = addZero(d.getFullYear());
    	var h = addZero(d.getHours());
    	var m = addZero(d.getMinutes());
    	var s = addZero(d.getSeconds());
    	return day + "-" + month + "-" + year;
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