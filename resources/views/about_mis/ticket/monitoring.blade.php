@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
	.table-pic tbody>tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	tbody>tr>td{
		padding: 10px 5px 10px 5px;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 40px;
		padding:  2px 5px 2px 5px;
	}
	.crop2 {
		overflow: hidden;
	}
	.crop2 img {
		height: 70px;
		margin: -5% 0 0 0 !important;
	}
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>

		<a data-toggle="modal" data-target="#modalCreate" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-pencil-square-o"></i> Buat Ticket</a>

		<a href="{{ url("/index/ticket_log") }}" class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-list"></i> Update Logs</a>

		@if(in_array('A7', $navs))
		<a href="{{ url("/index/member/mis") }}" class="btn btn-warning pull-right" style="margin-left: 5px; width: 10%;width: 170px;"><i class="fa fa-line-chart"></i> <span>Resume Ticket Result</span></a>

		<a href="{{ url("/index/ticket_monitoring/category") }}" class="btn btn-primary pull-right" style="margin-left: 5px; width: 170px;"><i class="fa fa-list"></i> <span>Ticket By Category</span></a>

		<a href="{{ url("/index/daily_report") }}" class="btn btn-danger pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-file-code-o"></i> <span>Daily Report</span></a>
		@endif
	</h1>
</section>
@endsection

@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<!-- <div class="col-xs-12">
			<div class="row">
				
			</div>
		</div> -->
		<!-- <div class="col-xs-3" id="ticket_pics" style="padding-right: 7px;">

		</div> -->
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12" style="padding:0">
		          <div class="col-xs-2" style="padding-right: 5px;">
		            <div class="input-group date">
		              <div class="input-group-addon bg-green" style="border: none;">
		                <i class="fa fa-calendar"></i>
		              </div>
		              <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" required="" onchange="fetchMonitoring()">
		            </div>
		          </div>

		          <div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
		            <div class="input-group date">
		              <div class="input-group-addon bg-green" style="border: none;">
		                <i class="fa fa-calendar"></i>
		              </div>
		              <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" onchange="fetchMonitoring()">
		            </div>
		          </div>

		          <div class="col-xs-2" style="padding-left: 0px;">
		            <div class="input-group date">
		              <div class="input-group-addon bg-green" style="border: none;">
		                <i class="fa fa-calendar"></i>
		              </div>
		              <select class="form-control select3" onchange="fetchMonitoring()" id="priority" name="priority" data-placeholder="Filter By Priority" style="width: 100%">
		                <option value=""></option>
		                <option value="Very High">Very High</option>
		                <option value="High">High</option>
		                <option value="Normal">Normal</option>
		                <!-- <option value="Low">Low</option> -->
		            </select>
		            </div>
		          </div>
		        </div>
				<div class="col-xs-12"  style="padding-top: 20px">
					<div class="col-xs-10" style="padding-right: 0;padding-left: 0px;">
						<div id="container" style="width: 100%; height: 50vh; margin-bottom: 10px; border: 1px solid black;"></div>
					</div>
					<!-- <div class="col-xs-6" style="padding-right: 0;">
						<div id="containerSoftware" style="width: 100%; height: 40vh; margin-bottom: 10px; border: 1px solid black;"></div>
					</div> -->
					<div class="col-xs-2" style="padding-right: 0px;height: 50vh;">
						<p class="text-center">
						<strong>Resume Tickets</strong>
						</p>
						<!-- <div class="progress-group">
						<span class="progress-text">Add Products to Cart</span>
						<span class="progress-number"><b>160</b>/200</span>
						<div class="progress sm">
							<div class="progress-bar progress-bar-aqua" style="width: 80%"></div>
							</div>
						</div>

						<div class="progress-group">
							<span class="progress-text">Complete Purchase</span>
							<span class="progress-number"><b>310</b>/400</span>
							<div class="progress sm">
								<div class="progress-bar progress-bar-red" style="width: 80%"></div>
							</div>
						</div> -->

						<div class="progress-group">
							<span class="progress-text">Total Ticket Open</span>
							<span class="progress-number"><b><span id="totalWaiting"> </span></b></span>
							<div class="progress sm" id="progress-open">
							</div>
						</div>

						<div class="progress-group">
							<span class="progress-text">Total Ticket Progress</span>
							<span class="progress-number"><b><span id="totalProgress"></span></b>/<span id="totalWaitingProgress"></span></span>
							<div class="progress sm" id="progress-progress">
							</div>
						</div>

						<div class="progress-group">
							<span class="progress-text">Total Ticket Close</span>
							<span class="progress-number"><b><span id="totalFinish"></span></b>/<span id="totalAllTicket"> </span></span>
							<div class="progress sm" id="progress-total">
							</div>
						</div>

						<!-- <div class="small-box" style="margin-bottom: 5px; height: 13vh; background-color: #FFFFFF; border: 1px solid black;">
							<div class="inner">
								<h3 id="totalWaitingSoftware" style="font-size: 2vw;">0</h3>
								<p style="font-weight: bold; font-size: 1.2vw;">Waiting</p>
							</div>
							<div class="icon">
								<i class="ion ion-android-alarm-clock" style="font-size: 4.5vw;"></i>
							</div>
						</div>
						<div class="small-box" style="margin-bottom: 5px; height: 13vh; background-color: #f9a825; border: 1px solid black;">
							<div class="inner">
								<h3 id="totalProgressSoftware" style="font-size: 2vw;">0</h3>
								<p style="font-weight: bold; font-size: 1.2vw;">InProgress</p>
							</div>
							<div class="icon">
								<i class="ion ion-android-settings" style="font-size: 4.5vw;"></i>
							</div>
						</div>
						<div class="small-box" style="margin-bottom: 5px; height: 13vh; background-color: #aee571; border: 1px solid black;">
							<div class="inner">
								<h3 id="totalFinishSoftware" style="font-size: 2vw;">0</h3>
								<p style="font-weight: bold; font-size: 1.2vw;">Finished</p>
							</div>
							<div class="icon">
								<i class="ion ion-android-star-outline" style="font-size: 4.5vw;"></i>
							</div>
						</div> -->
					</div>
					
					<!-- <div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 10px;">
						<span style="font-weight: bold; font-size: 1.6vw;">Outstanding Ticket</span>
					</div> -->

					<table id="tableProgress" class="table table-bordered table-hover">
						<thead style="">
							<tr>
								<th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">ID</th>
								<th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Dept</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Prioritas<br>Status</th>
								<th style="width: 6%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Judul</th>
								<!-- <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" colspan="2">Tanggal Permintaan</th> -->
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;" colspan="3">Approval</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Status</th>
								<th style="width: 3%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">PIC</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Progress</th>
								<!-- <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Aksi</th> -->
							</tr>
							<tr>
								<!-- <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Pengajuan</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Selesai</th> -->
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;">Manager</th>
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;">Chief MIS</th>
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;border-right: 1px solid black;">Manager MIS</th>
							</tr>
						</thead>

						<tbody id="tableProgressBody">
						</tbody>
						<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
					</table>
					<div style="background-color: #00a65a; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
						<span style="font-weight: bold; font-size: 20px">TICKET COMPLETE</span>
					</div>
					<table id="tableFinished" class="table table-bordered table-hover">
						<thead style="">
							<tr>
								<th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">ID</th>
								<th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Dept</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Prioritas<br>Status</th>
								<th style="width: 6%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Judul</th>
								<!-- <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" colspan="2">Tanggal Permintaan</th> -->
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;" colspan="3">Approval</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Status</th>
								<th style="width: 3%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">PIC</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Progress</th>
								<!-- <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;" rowspan="2">Aksi</th> -->
							</tr>
							<tr>
								<!-- <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Pengajuan</th>
								<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Selesai</th> -->
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;">Manager</th>
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;">Chief MIS</th>
								<th style="width: 1%; text-align: center; background-color: #f9a825; color: black;border-right: 1px solid black;">Manager MIS</th>
							</tr>
						</thead>
						<tbody id="tableFinishedBody">
						</tbody>
						<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- modal -->
	<div class="modal fade" id="ModalDetail" style="z-index: 10000;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="nav-tabs-custom tab-danger" align="center">
						<span style="font-weight: bold; font-size: 2vw;" class="text-purple" id="title-detail"></span>
					</div>
					<div class="col-md-12" style="padding-top: 10px">
						<table id="detailModal" class="table table-bordered table-striped table-hover">
							<thead style="background-color: #605ca8; color: white;">
								<tr>
									<th style="width: 10%;">ID</th>
									<th style="width: 30%;">Dept.</th>
									<th style="width: 40%;">Title</th>
									<th style="width: 10%;">Status</th>
									<th style="width: 10%;">PIC</th>
								</tr>
							</thead>
							<tbody id="detailModalBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
						<form class="form-horizontal">

							<input type="hidden" value="{{csrf_token()}}" name="_token" />

							<div class="col-md-12" style="padding-top: 20px;">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
									<div class="col-sm-7">
										<select class="form-control select2" name="createDepartment" id="createDepartment" data-placeholder="Select Department" style="width: 100%;">
											@foreach($departments as $department)
											@if($department->department_name == Auth::user()->employee_sync->department)
											<option value="{{ $department->department_name }}" selected>{{ $department->department_name }}</option>
											@else
											<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
											@endif
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kategori<span class="text-red">*</span> :</label>
									<div class="col-sm-4">
										<select class="form-control select2" name="createCategory" id="createCategory" data-placeholder="Select Category" style="width: 100%;">
											<option></option>
											@foreach($categories as $category)
											<option value="{{ $category }}">{{ $category }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Prioritas<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<select class="form-control select2" id="createPriority" data-placeholder="Select Priority" style="width: 30%;" onchange="changePriority()">
											<option></option>
											@foreach($priorities as $priority)
											<option value="{{ $priority }}">{{ $priority }}</option>
											@endforeach
										</select><br>
										<span style="padding-bottom: 0; font-size: 0.7vw; color: red;">High: Ada dampak internal YMPI (Stop line, Overtime, dsb) Atau eksternal (Costumer, Auditor, dsb)</span>
									</div>
								</div>
								<div class="form-group" id="createGroupReason">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Alasan<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="2" placeholder="Enter Reason" id="createReason">Kerugian jika tidak terimplementasi.&#013;Internal: ...&#013;Eksternal: ...</textarea>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul (Max 200 char.)<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" placeholder="Enter Title" id="createTitle">
									</div>
								</div>

								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Deskripsi<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="3" placeholder="Enter Description" id="createDescription"></textarea>
									</div>
								</div>
								<!-- <div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Grup Ticket<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<select class="form-control select2" id="createGroup" data-placeholder="Select Group" style="width: 30%;">
											<option></option>
											<option value="Digital Form">Digital Form</option>
											<option value="Digital Approval">Digital Approval</option>
											<option value="Digital Kanban">Digital Kanban</option>
											<option value="Monitoring">Monitoring</option>
											<option value="IOT">IOT</option>
											<option value="Transaction">Transaksi</option>
											<option value="ympi.co.id">ympi.co.id</option>
											<option value="Network">Network</option>
											<option value="Hardware">Hardware</option>
										</select>
									</div>
								</div> -->
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kondisi Sebelum<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="3" placeholder="Enter Description" id="createBefore">-</textarea>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kondisi Diharapkan<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="3" placeholder="Enter Description" id="createAfter">-</textarea>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal Permintaan Selesai<span class="text-red">*</span> :</label>
									<div class="col-sm-3">
										<input type="text" class="form-control datepicker" id="createDueDate" placeholder="Select Date">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Flow Lama<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="file" id="createAttachmentFlowOld" multiple="">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Flow Baru<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="file" id="createAttachmentFlowNew" multiple="">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Gambaran Tampilan Sistem<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="file" id="createAttachment" multiple="">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nomor Dokumen<span class="text-red"></span> :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="2" placeholder="Enter Document Number" id="createDocument"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Target<span class="text-red">*</span> :</label>
									<div class="col-sm-3">
										<a class="btn btn-primary" style="font-weight: bold;" onclick="addCostDown()"><i class="fa fa-plus"></i> Target</a>
									</div>
								</div>
								<div class="col-md-12">
									<span style="font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> List Target</span>
									<table class="table table-bordered table-striped" id="tableCostDown">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 4%;">Category<span class="text-red">*</span></th>
												<th style="width: 8%;">Description<span class="text-red">*</span></th>
												<th style="width: 4%;">Nilai<span class="text-red">*</span></th>
												<th style="width: 4%;">Satuan<span class="text-red">*</span></th>
												<th style="width: 1%;">Action</th>
											</tr>
										</thead>
										<tbody id="tableCostDownBody">
										</tbody>
									</table>
								</div>
							</div>
						</form>
						<div class="col-md-5" style="margin-top: 20px;">
							<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 100%;">BATAL</button>
						</div>
						<div class="col-md-7" style="margin-top: 20px;">
							<button class="btn btn-success pull-left" onclick="createTicket()" style="font-weight: bold; font-size: 1.3vw; width: 100%;">BUAT</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModalBulan">
	    <div class="modal-dialog modal-lg" style="width:1250px;">
	      <div class="modal-content">
	        <div class="modal-header">
	          <h4 style="float: right;" id="modal-title"></h4>
	          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
	          <br><h4 class="modal-title" id="title_month"></h4>
	        </div>
	        <div class="modal-body">
	          <div class="row">
	            <div class="col-md-12">
	              <table id="example4" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
	                <thead style="background-color: rgba(126,86,134,.7);">
	                  <tr>
	                    <th>ID</th>
	                    <th>Status</th>
	                    <th>Priority</th>
	                    <th>Case Title</th>
	                    <th>PIC Name</th>
	                  </tr>
	                </thead>
	                <tbody>
	                </tbody>
	              </table>
	            </div>
	          </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
	      </div>
	    </div>
	  </div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ asset("ckeditor/ckeditor.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$(document).keydown(function(e) {

		});
		$('body').toggleClass("sidebar-collapse");
		$('#createGroupReason').hide();	
		fetchMonitoring();
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%; z-index: 9999;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var costdowns = [];
	var costdown_count = 0;

	$(function () {
		$('.select3').select2({
		    dropdownAutoWidth : true,
		    allowClear: true
		  });

		$('.datepicker').datepicker({
		    autoclose: true,
		    format: "dd-mm-yyyy",
		    todayHighlight: true,
		});

		$('#createDepartment').select2({
			dropdownParent: $('#modalCreate')
		});
		$('#createCategory').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
		$('#createPriority').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
		$('#createGroup').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
		$('#createDueDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
	});


	$('#modalCreate').on('hidden.bs.modal', function(){
		// $('#createCategory').val('');
		// $('#createPriority').prop('selectedIndex', 0).change();
		// $('#createTitle').val('');
		// $('#createDescription').val('');
		// $('#createBefore').html(CKEDITOR.instances.createTranslationRequest.setData(""));
		// $('#createAfter').html(CKEDITOR.instances.createTranslationRequest.setData(""));
		// $('#createDueDate').val('');
		// $('#createAttachment').val('');
		// $('#createDocument').val('');
		// $('#createGroupReason').hide();
	});


	function detailTicket(ticket_id){
		window.open('{{ url("index/ticket/detail") }}'+'/'+ticket_id, '_blank');
	}

	function fetchMonitoring(){

		var date_from = $('#date_from').val();
	    var date_to = $('#date_to').val();
	    var priority = $('#priority').val();

	    var data = {
	      date_from: date_from,
	      date_to: date_to,
	      priority: priority
	    };

		$.get('{{ url("fetch/ticket/monitoring/new") }}', data, function(result, status, xhr){
			if(result.status){
				
				$('#tableProgress').DataTable().clear();
				$('#tableProgress').DataTable().destroy();

				$('#tableFinished').DataTable().clear();
				$('#tableFinished').DataTable().destroy();

				$('#tableProgressBody').html('');
				$('#tableFinishedBody').html('');

				var totalWaiting = 0;
				var totalWaitingProgress = 0;
				var totalProgress = 0;
				var totalFinish = 0;
				var totalAll = 0;

				var totalOpenHigh = 0;
				var totalOpenNormal = 0;

				var ticketTableBody = "";
				var ticketTableFinish = "";

				$.each(result.tickets, function(key, value){
					var cnt = 0;
					if (value.status == "Approval") {
						ticketTableBody += '<tr>';
						ticketTableBody += '<td style="width: 0.1%; font-weight: bold;"><a href="javascript:void(0)" onclick="detailTicket(\''+value.ticket_id+'\')">'+value.ticket_id+'</a></td>';
						ticketTableBody += '<td style="width: 0.1%;">'+value.department_shortname+'</td>';
						ticketTableBody += '<td style="width: 0.1%;">'+value.priority+'</td>';
						if(value.priority == 'High' || value.priority == 'Very High'){
							ticketTableBody += '<td style="width: 7%;"><span style="color: #e53935;">('+value.priority+' '+value.created_at+')</span></br>'+value.case_title+'</td>';
						}
						else{
							ticketTableBody += '<td style="width: 7%;">('+value.priority+' '+value.created_at+')</br>'+value.case_title+'</td>';
						}

						$rejected = false;

						for(var i = 0; i < result.ticket_approvers.length; i++){
							if(result.ticket_approvers[i].ticket_id == value.ticket_id){
								cnt += 1;
								if(value.category == 'Pembuatan Aplikasi Baru' || value.category == 'Pengembangan Aplikasi Lama' || value.priority == 'High' || value.priority == 'Very High'){
									
									if(result.ticket_approvers[i].status == 'Rejected'){
										$rejected = true;
									}

									if(!$rejected){
										if(result.ticket_approvers[i].status == 'Approved'){
											ticketTableBody += '<td style="width: 3%; color: black; background-color: #aee571;">'+result.ticket_approvers[i].approver_name+' (Approved)<br>'+result.ticket_approvers[i].approved_at+'</td>';
										}
										else if(result.ticket_approvers[i].status == null){
											ticketTableBody += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('approval/ticket') }}?ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'"><div style="height:100%;width:100%">'+result.ticket_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
										}
									}else{
										ticketTableBody += '<td style="width: 3%; color: white; background-color: black;">'+result.ticket_approvers[i].approver_name+' (Rejected)<br>'+(result.ticket_approvers[i].approved_at|| '')+'</td>';
									}

								}
								else{
									ticketTableBody += '<td style="width: 3%; color: black; background-color: black;"></td>';
									if(result.ticket_approvers[i].status == 'Approved'){
										ticketTableBody += '<td style="width: 3%; color: black; background-color: #aee571;">'+result.ticket_approvers[i].approver_name+' (Approved)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									else if(result.ticket_approvers[i].status == null){
										ticketTableBody += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('approval/ticket') }}?ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'"><div style="height:100%;width:100%">'+result.ticket_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
									}
									else if(result.ticket_approvers[i].status == 'Rejected'){
										ticketTableBody += '<td style="width: 3%; color: white; background-color: black;">'+result.ticket_approvers[i].approver_name+' (Rejected)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									ticketTableBody += '<td style="width: 3%; color: black; background-color: black;"></td>';
									
								}
							}
						}
						if(value.status == 'Approval'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: white; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Waiting'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'InProgress'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #f9a825; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'OnHold'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Finished'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else{
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}

						var pic_name = "-";
						if(value.pic_shortname !== null){
							pic_name = value.pic_shortname;
						}

						ticketTableBody += '<td style="width: 0.1%; font-weight: bold;">'+pic_name+'</td>';
						ticketTableBody += '<td style="width: 1%; text-align: right;">';
						ticketTableBody += '<div class="progress progress-sm active" style="border: 1px solid black;">';
						ticketTableBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="'+value.progress+'" aria-valuemin="0" aria-valuemax="100" style="width: '+value.progress+'%;">';
						ticketTableBody += '</div>';
						ticketTableBody += '</div>';
						ticketTableBody += '<span style="font-weight: bold;">'+value.progress+'%</span>';
						ticketTableBody += '</td>';
						ticketTableBody += '</tr>';	
					}
					
				});

				$.each(result.tickets, function(key, value){

					if(value.status == 'Waiting' || value.status == 'Approval'){
						totalWaiting += 1;
						totalWaitingProgress += 1;
						totalAll += 1;

						if (value.priority == "High" || value.priority == "Very High") {
							totalOpenHigh += 1;
						}else{
							totalOpenNormal += 1;
						}
					}
					if(value.status == 'InProgress' || value.status == 'OnHold'){
						totalProgress += 1;
						totalWaitingProgress += 1;
						totalAll += 1;
					}
					if(value.status == 'Finished'){
						totalFinish += 1;
						totalAll += 1;
					}

					$('#totalWaiting').text(totalWaiting);
					$('#totalWaitingProgress').text(totalWaitingProgress);
					$('#totalProgress').text(totalProgress);
					$('#totalFinish').text(totalFinish);
					$('#totalAll').text(totalAll);
					$('#totalAllTicket').text(totalAll);

					// persen_open = parseFloat(totalWaiting) / parseFloat(totalAll) * 100;


					persen_open_high = parseFloat(totalOpenHigh) / parseFloat(totalWaiting) * 100;
					persen_open_normal = parseFloat(totalOpenNormal) / parseFloat(totalWaiting) * 100;					
					persen_progress = parseFloat(totalProgress) / parseFloat(totalWaitingProgress) * 100;
					persen_total = parseFloat(totalFinish) / parseFloat(totalAll) * 100;

					// $('#progress-open').html('<div class="progress-bar progress-bar-red" style="width: '+persen_open+'%"></div>');

					$('#progress-open').html('<div class="progress-bar progress-bar-red" style="width: '+persen_open_high+'%;background-color:#b71c1c"></div><div class="progress-bar progress-bar-red" style="width: '+persen_open_normal+'%;background-color:#ef5350"></div>');

					$('#progress-progress').html('<div class="progress-bar progress-bar-yellow" style="width: '+persen_progress+'%"></div>');
					$('#progress-total').html('<div class="progress-bar progress-bar-green" style="width: '+persen_total+'%"></div>');


					var cnt = 0;
					 if (value.status != "Approval") {
					if (value.status != "Finished" && value.status != "Rejected") {
						ticketTableBody += '<tr>';
						ticketTableBody += '<td style="width: 0.1%; font-weight: bold;"><a href="javascript:void(0)" onclick="detailTicket(\''+value.ticket_id+'\')">'+value.ticket_id+'</a></td>';
						ticketTableBody += '<td style="width: 0.1%;">'+value.department_shortname+'</td>';
						ticketTableBody += '<td style="width: 0.1%;">'+value.priority+'</td>';
						if(value.priority == 'High' || value.priority == 'Very High'){
							ticketTableBody += '<td style="width: 7%;"><span style="color: #e53935;">('+value.priority+' '+value.created_at+')</span></br>'+value.case_title+'</td>';
						}
						else{
							ticketTableBody += '<td style="width: 7%;">('+value.priority+' '+value.created_at+')</br>'+value.case_title+'</td>';
						}

						$rejected = false;

						for(var i = 0; i < result.ticket_approvers.length; i++){
							if(result.ticket_approvers[i].ticket_id == value.ticket_id){
								cnt += 1;
								if(value.category == 'Pembuatan Aplikasi Baru' || value.category == 'Pengembangan Aplikasi Lama' || value.priority == 'High' || value.priority == 'Very High'){
									
									if(result.ticket_approvers[i].status == 'Rejected'){
										$rejected = true;
									}

									if(!$rejected){
										if(result.ticket_approvers[i].status == 'Approved'){
											ticketTableBody += '<td style="width: 3%; color: black; background-color: #aee571;">'+result.ticket_approvers[i].approver_name+' (Approved)<br>'+result.ticket_approvers[i].approved_at+'</td>';
										}
										else if(result.ticket_approvers[i].status == null){
											ticketTableBody += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('approval/ticket') }}?ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'"><div style="height:100%;width:100%">'+result.ticket_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
										}
									}else{
										ticketTableBody += '<td style="width: 3%; color: white; background-color: black;">'+result.ticket_approvers[i].approver_name+' (Rejected)<br>'+(result.ticket_approvers[i].approved_at|| '')+'</td>';
									}

								}
								else{
									ticketTableBody += '<td style="width: 3%; color: black; background-color: black;"></td>';
									if(result.ticket_approvers[i].status == 'Approved'){
										ticketTableBody += '<td style="width: 3%; color: black; background-color: #aee571;">'+result.ticket_approvers[i].approver_name+' (Approved)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									else if(result.ticket_approvers[i].status == null){
										ticketTableBody += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('approval/ticket') }}?ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'"><div style="height:100%;width:100%">'+result.ticket_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
									}
									else if(result.ticket_approvers[i].status == 'Rejected'){
										ticketTableBody += '<td style="width: 3%; color: white; background-color: black;">'+result.ticket_approvers[i].approver_name+' (Rejected)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									ticketTableBody += '<td style="width: 3%; color: black; background-color: black;"></td>';
									
								}
							}
						}
						if(value.status == 'Approval'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: white; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Waiting'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'InProgress'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #f9a825; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'OnHold'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Finished'){
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else{
							ticketTableBody += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}

						var pic_name = "-";
						if(value.pic_shortname !== null){
							pic_name = value.pic_shortname;
						}

						ticketTableBody += '<td style="width: 0.1%; font-weight: bold;">'+pic_name+'</td>';
						ticketTableBody += '<td style="width: 1%; text-align: right;">';
						ticketTableBody += '<div class="progress progress-sm active" style="border: 1px solid black;">';
						ticketTableBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="'+value.progress+'" aria-valuemin="0" aria-valuemax="100" style="width: '+value.progress+'%;">';
						ticketTableBody += '</div>';
						ticketTableBody += '</div>';
						ticketTableBody += '<span style="font-weight: bold;">'+value.progress+'%</span>';
						ticketTableBody += '</td>';
						ticketTableBody += '</tr>';	
					} else{
						ticketTableFinish += '<tr>';
						ticketTableFinish += '<td style="width: 0.1%; font-weight: bold;"><a href="javascript:void(0)" onclick="detailTicket(\''+value.ticket_id+'\')">'+value.ticket_id+'</a></td>';
						ticketTableFinish += '<td style="width: 0.1%;">'+value.department_shortname+'</td>';
						ticketTableFinish += '<td style="width: 0.1%;">'+value.priority+'</td>';
						if(value.priority == 'High' || value.priority == 'Very High'){
							ticketTableFinish += '<td style="width: 7%;"><span style="color: #e53935;">('+value.priority+' '+value.created_at+')</span></br>'+value.case_title+'</td>';
						}
						else{
							ticketTableFinish += '<td style="width: 7%;">('+value.priority+' '+value.created_at+')</br>'+value.case_title+'</td>';
						}
						for(var i = 0; i < result.ticket_approvers.length; i++){
							if(result.ticket_approvers[i].ticket_id == value.ticket_id){
								cnt += 1;
								if(value.category == 'Pembuatan Aplikasi Baru' || value.category == 'Pengembangan Aplikasi Lama' || value.priority == 'High' || value.priority == 'Very High'){
									if(result.ticket_approvers[i].status == 'Approved'){
										ticketTableFinish += '<td style="width: 3%; color: black; background-color: #aee571;">'+result.ticket_approvers[i].approver_name+' (Approved)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									else if(result.ticket_approvers[i].status == null){
										ticketTableFinish += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('approval/ticket') }}?ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'"><div style="height:100%;width:100%">'+result.ticket_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
									}
									else if(result.ticket_approvers[i].status == 'Rejected'){
										ticketTableFinish += '<td style="width: 3%; color: white; background-color: black;">'+result.ticket_approvers[i].approver_name+' (Rejected)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
								}
								else{
									ticketTableFinish += '<td style="width: 3%; color: black; background-color: black;"></td>';
									if(result.ticket_approvers[i].status == 'Approved'){
										ticketTableFinish += '<td style="width: 3%; color: black; background-color: #aee571;">'+result.ticket_approvers[i].approver_name+' (Approved)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									else if(result.ticket_approvers[i].status == null){
										ticketTableFinish += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('approval/ticket') }}?ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'"><div style="height:100%;width:100%">'+result.ticket_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
									}
									else if(result.ticket_approvers[i].status == 'Rejected'){
										ticketTableFinish += '<td style="width: 3%; color: white; background-color: black;">'+result.ticket_approvers[i].approver_name+' (Rejected)<br>'+result.ticket_approvers[i].approved_at+'</td>';
									}
									ticketTableFinish += '<td style="width: 3%; color: black; background-color: black;"></td>';
								}
							}
						}
						if(value.status == 'Approval'){
							ticketTableFinish += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: white; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Waiting'){
							ticketTableFinish += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'InProgress'){
							ticketTableFinish += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #f9a825; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'OnHold'){
							ticketTableFinish += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Finished'){
							ticketTableFinish += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else{
							ticketTableFinish += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}

						var pic_name = "-";
						if(value.pic_shortname !== null){
							pic_name = value.pic_shortname;
						}

						ticketTableFinish += '<td style="width: 0.1%; font-weight: bold;">'+pic_name+'</td>';
						ticketTableFinish += '<td style="width: 1%; text-align: right;">';
						ticketTableFinish += '<div class="progress progress-sm active" style="border: 1px solid black;">';
						ticketTableFinish += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="'+value.progress+'" aria-valuemin="0" aria-valuemax="100" style="width: '+value.progress+'%;">';
						ticketTableFinish += '</div>';
						ticketTableFinish += '</div>';
						ticketTableFinish += '<span style="font-weight: bold;">'+value.progress+'%</span>';
						ticketTableFinish += '</td>';
						ticketTableFinish += '</tr>';	
					}
				}	
				});

				$('#tableProgressBody').append(ticketTableBody);
				$('#tableFinishedBody').append(ticketTableFinish);

				$('#tableProgress tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
				} );

				var table = $('#tableProgress').DataTable({
				'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 15, 25, -1 ],
					[ '15 rows', '25 rows', 'Show all' ]
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
					"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				'ordering' :false,
					initComplete: function() {
                    this.api()
                        .columns([1, 2])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tableFinished th").eq([dd]).text();
                            var select = $(
                                    '<select><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#tableFinished th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                	},
				});

				table.columns().every( function () {
					var that = this;
					$( '#search', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#tableProgress tfoot tr').appendTo('#tableProgress thead');


				$('#tableFinished tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
				} );

				var table2 = $('#tableFinished').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 15, 25, -1 ],
					[ '15 rows', '25 rows', 'Show all' ]
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
					"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				'ordering' :false,
					initComplete: function() {
                    this.api()
                        .columns([1, 2])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tableFinished th").eq([dd]).text();
                            var select = $(
                                    '<select><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#tableFinished th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                	},
				});


				table2.columns().every( function () {
					var that = this;
					$( '#search', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#tableFinished tfoot tr').appendTo('#tableFinished thead');

				var bulan = [];
				var tahun = [];
				var belum_ditangani_bulan = [];
				var progress_ditangani_bulan = [];
				var sudah_ditangani_bulan = [];
				
				$.each(result.month_data, function(key, value) {
		          	bulan.push(value.bulan);
		          	tahun.push(value.tahun);
		          	belum_ditangani_bulan.push({y: parseInt(value.jumlah_belum),key:value.tahun});
		          	progress_ditangani_bulan.push({y: parseInt(value.jumlah_progress),key:value.tahun});
		          	sudah_ditangani_bulan.push({y: parseInt(value.jumlah_sudah),key:value.tahun});
		        });

				$('#container').highcharts({
		          chart: {
		            type: 'column',
		            backgroundColor: null
		          },
		          title: {
		            text: "Outstanding Tickets By Month",
		          },
		          xAxis: {
		            type: 'category',
		            categories: bulan,
		            lineWidth:2,
		            lineColor:'#000',
		            gridLineWidth: 1,
		            labels: {
		              formatter: function (e) {
		                return ''+ this.value +' '+tahun[(this.pos)];
		              }
		            }
		          },
		          yAxis: {
		            lineWidth:2,
		            lineColor:'#000',
		            type: 'linear',
		            title: {
		              text: 'Total Ticket'
		            },
		            stackLabels: {
		              enabled: true,
		              style: {
		                fontWeight: 'bold',
		                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
		              }
		            }
		          },
		          legend: {
		            itemStyle:{
		              color: "black",
		              fontSize: "12px",
		              fontWeight: "bold",

		            }
		          },
		          plotOptions: {
		            series: {
		              cursor: 'pointer',
		              point: {
		                events: {
		                  click: function () {
		                    ShowModalBulan(this.category,this.series.name,this.key);
		                  }
		                }
		              },
		              dataLabels: {
		                enabled: false,
		                format: '{point.y}'
		              }
		            },
		            column: {
		              color:  Highcharts.ColorString,
		              stacking: 'normal',
		              pointPadding: 0.93,
		              groupPadding: 0.93,
		              borderWidth: 1,
		              dataLabels: {
		                enabled: true
		              }
		            }
		          },
		          credits: {
		            enabled: false
		          },

		          tooltip: {
		            formatter:function(){
		              return this.series.name+' : ' + this.y;
		            }
		          },
		          series: [
		          {
		            name: 'Ticket Open',
		            data: belum_ditangani_bulan,
              		color: '#ff2824'
		          },{
		            name: 'Ticket Progress',
		            data: progress_ditangani_bulan,
		            color: '#f39c12'
		          },
		          {
		            name: 'Ticket Close',
		            data: sudah_ditangani_bulan,
              		color: 'rgb(34, 204, 125)'
		          }
		          ]
		        })
			}
			else{
				alert('Unidentified Error '+result.message);
				audio_error.play();
				return false;
			}
		});
	}


	  function ShowModalBulan(bulan, status, tahun) {


	  	var priority = $("#priority").val();

	    tabel = $('#example4').DataTable();
	    tabel.destroy();

	    $("#myModalBulan").modal("show");

	    var table = $('#example4').DataTable({
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
	      'searching': false,
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
	        "url" : "{{ url('fetch/ticket/monitoring/detail') }}",
	        "data" : {
	          bulan : bulan,
	          status : status,
	          remark : priority,
	          tahun : tahun
	        }
	      },
	      "columns": [
	      {"data": "ticket_id", "width": "5%"},
	      {"data": "status" , "width": "10%"},
	      {"data": "priority", "width": "10%"},
	      {"data": "case_title", "width": "30%"},
	      {"data": "pic_name", "width": "10%"}
	      ]    
	    });

	    $('#title_month').append().empty();
	    $('#title_month').append('<center><b> '+status+' '+bulan+'-'+tahun+' '+priority+'</b></center>'); 
	  }

	function DetailGrafikPriority(month, category, ket){
		$('#loading').show();
		var data = {
			month:month,
			category:category,
			ket:ket
		}

		$.get('{{ url("fetch/detail/tiket") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#ModalDetail').modal('show');
				$('#title-detail').html('Detail Ticket '+category+'<br>'+month);

				$('#detailModal').DataTable().clear();
				$('#detailModal').DataTable().destroy();
				$('#detailModalBody').html("");
				var tableData = "";
				$.each(result.data, function(key, value) {

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ value.ticket_id +'</td>';
					tableData += '<td style="text-align: center">'+ value.department +'</td>';
					tableData += '<td style="text-align: center">'+ value.case_title +'</td>';
					tableData += '<td style="text-align: center">'+ value.status +'</td>';
					tableData += '<td style="text-align: center">'+ value.priority +'</td>';
					tableData += '</tr>';

				});
				$('#detailModalBody').append(tableData);

				var table = $('#detailModal').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[{
						extend: 'pageLength',
						className: 'btn btn-default',
					}]
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 5,
				'DataListing': true	,
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
		});
	}

	function compare( a, b ) {
		if ( a.created_at < b.created_at ){
			return -1;
		}
		if ( a.created_at > b.created_at ){
			return 1;
		}
		return 0;
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function addCostDown(){
		var costdown_list = <?php echo json_encode($costdowns); ?>;

		if(costdown_count > 0){
			if($('#costdown_category_'+costdown_count).val() == ""){
				openErrorGritter('Error!', 'Isikan costdown terlebih dahulu.');
				return false;
			}
			if($('#costdown_description_'+costdown_count).val() == ""){
				openErrorGritter('Error!', 'Isikan costdown terlebih dahulu.');
				return false;
			}
			if($('#costdown_amount_'+costdown_count).val() <= 0){
				openErrorGritter('Error!', 'Isikan costdown terlebih dahulu.');
				return false;
			}
			if($('#costdown_uom_'+costdown_count).val() == ""){
				openErrorGritter('Error!', 'Isikan costdown terlebih dahulu.');
				return false;
			}
		}

		var tableCostDownBody = "";
		costdown_count += 1;

		tableCostDownBody += '<tr id="costdown_'+costdown_count+'">';
		tableCostDownBody += '<td>';
		tableCostDownBody += '<select style="width: 100%;" class="select2" id="costdown_category_'+costdown_count+'">';
		tableCostDownBody += '<option></option>';
		$.each(costdown_list, function(key, value){
			tableCostDownBody += '<option value="'+value+'">'+value+'</option>';
		});
		tableCostDownBody += '</select>';
		tableCostDownBody += '</td>';
		tableCostDownBody += '<td><textarea class="form-control" rows="2" id="costdown_description_'+costdown_count+'"></textarea></td>';
		tableCostDownBody += '<td><input type="text" class="form-control numpad" id="costdown_amount_'+costdown_count+'" value="0"></td>';
		// tableCostDownBody += '<td><input type="text" class="form-control" id="costdown_uom_'+costdown_count+'"></td>';
		tableCostDownBody += '<td>';
		tableCostDownBody += '<select style="width: 100%;" class="select2" id="costdown_uom_'+costdown_count+'">';
		tableCostDownBody += '<option></option>';
		tableCostDownBody += '<option>Rupiah</option>';
		tableCostDownBody += '<option>Dollar</option>';
		tableCostDownBody += '<option>Minutes</option>';
		tableCostDownBody += '<option>Person</option>';
		tableCostDownBody += '</select>';
		tableCostDownBody += '</td>';
		tableCostDownBody += '<td><center><a href="javascript:void(0)" onclick="remCostDown(id)" id="'+costdown_count+'" class="btn btn-danger btn-sm" style="margin-right:5px;"><i class="fa fa-trash"></i></a></center></td>';
		tableCostDownBody += '</tr>';

		$('#tableCostDownBody').append(tableCostDownBody);
		costdowns.push(costdown_count);

		$('#costdown_amount_'+costdown_count).numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		// $('#costdown_category_'+costdown_count).select2({
		// 	dropdownParent: $('#modalCreate'),
		// 	minimumResultsForSearch: -1
		// });

		$('.select2').select2();
	}
	
	function remCostDown(id){
		costdowns = jQuery.grep(costdowns, function(value) {
			return value != id;
		});
		$('#costdown_'+id).remove();
	}

	function changePriority(){
		if($('#createPriority').val() == 'High' || $('#createPriority').val() == 'Very High'){
			$('#createGroupReason').show();
			return false;
		}
		else{
			$('#createGroupReason').hide();	
			return false;		
		}
	}

	CKEDITOR.replace('createBefore' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
	});

	CKEDITOR.replace('createAfter' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
	});


	function createTicket(){
		if(confirm("Apakah anda yakin akan mengajukan tiket ini?")){
			$('#loading').show();

			var category = $('#createCategory').val();
			var department = $('#createDepartment').val();
			var priority = $('#createPriority').val();
			var reason = $('#createReason').val();
			var title = $('#createTitle').val();
			var description = $('#createDescription').val();
			var doc = $('#createDocument').val();
			var before = CKEDITOR.instances.createBefore.getData();
			var after = CKEDITOR.instances.createAfter.getData();
			// var due_from = $('#createDueFrom').val();
			var due_to = $('#createDueDate').val();
			var costdown_length = costdowns.length;
			var status = true;
			var message = "";

			if(priority == 'High' || priority == 'Very High'){
				if(reason == ''){
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', 'Untuk prioritas tinggi harus menyertakan alasan.');
					$('#createReason').focus();
					return false;
				}
			}

			if(category == '' || priority == '' || title == '' || description == '' || before == '' || after == '' || due_to == ''){
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
				return false;
			}


			if ($('#createAttachment').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == "" || $('#createAttachmentFlowOld').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == "" || $('#createAttachmentFlowNew').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == "") {
				openErrorGritter('Error', "Semua File Harus diisi");
				$('#loading').hide();
				return false;
			}
			
			var formData = new FormData();

			var attachment  = $('#createAttachment').prop('files')[0];
			var file = $('#createAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			var attachment_flow_before = $('#createAttachmentFlowOld').prop('files')[0];
			var file_flow_before = $('#createAttachmentFlowOld').val().replace(/C:\\fakepath\\/i, '').split(".");

			var attachment_flow_after = $('#createAttachmentFlowNew').prop('files')[0];
			var file_flow_after = $('#createAttachmentFlowNew').val().replace(/C:\\fakepath\\/i, '').split(".");

			total_costdown = 0;

			$.each(costdowns, function(key, value){
				var costdown_category = $('#costdown_category_'+value).val();
				var costdown_description = $('#costdown_description_'+value).val();
				var costdown_amount =  $('#costdown_amount_'+value).val();
				var costdown_uom =  $('#costdown_uom_'+value).val();

				total_costdown += costdown_amount;

				if(costdown_category == '' || costdown_description == '' || costdown_amount == '' || costdown_uom == ''){
					message = 'Semua kolom costdown harus terisi.';
					status = false;
				}
				if(!$.isNumeric(costdown_amount)){
					message = 'Amount costdown harus angka.';
					status = false;
				}
				if(costdown_category == 'Manpower' && costdown_amount == 0){
					message = 'Amount costdown manpower harus memiliki nilai.';
					status = false;				
				}
				if(costdown_category == 'Efficiency' && costdown_amount == 0){
					message = 'Amount costdown efficiency harus memiliki nilai.';
					status = false;				
				}
				if(costdown_category == 'Material' && costdown_amount == 0){
					message = 'Amount costdown material harus memiliki nilai.';
					status = false;				
				}
				if(costdown_category == 'Overtime' && costdown_amount == 0){
					message = 'Amount costdown overtime harus memiliki nilai.';
					status = false;			
				}
				formData.append('costdown['+key+']', costdown_category+'~'+costdown_description+'~'+costdown_amount+'~'+costdown_uom);
			});

			if(status == false){
				$('#loading').hide();
				costdown = [];
				audio_error.play();
				openErrorGritter('Error!', message);
				return false;
			}

			if(category == 'Pembuatan Aplikasi Baru' || category == 'Pengembangan Aplikasi Lama'){
				if(costdown_length <= 0){
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', 'Harus ada costdown untuk kategori (Pembuatan & Pengembangan Aplikasi).');
					return false;
				}
				if(total_costdown <= 0){
					$('#loading').hide();
					costdown = [];
					audio_error.play();
					openErrorGritter('Error!', 'Harus memiliki amount costdown.');
					return false;	
				}
			}

			formData.append('doc', doc);
			formData.append('department', department);
			formData.append('category', category);
			formData.append('priority', priority);
			formData.append('reason', reason);
			formData.append('title', title);
			formData.append('description', description);
			// formData.append('due_from', due_from);
			formData.append('due_to', due_to);
			formData.append('before', before);
			formData.append('after', after);
			
			formData.append('attachment', attachment);
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			formData.append('attachment_flow_before', attachment_flow_before);
			formData.append('extension_flow_before', file_flow_before[1]);
			formData.append('file_name_flow_before', file_flow_before[0]);

			formData.append('attachment_flow_after', attachment_flow_after);
			formData.append('extension_flow_after', file_flow_after[1]);
			formData.append('file_name_flow_after', file_flow_after[0]);

			$.ajax({
				url:"{{ url('input/ticket') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						$('#loading').hide();
						clearAll();
						openSuccessGritter('Success!',data.message);
						audio_ok.play();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!',data.message);
						audio_error.play();
					}

				}
			});
		}
		else{
			return false;
		}
	}

	
function clearAll(){
	$('#loading').hide();
	$('#modalCreate').modal('hide');
	$('#createGroupReason').hide();
	$("#createCategory").prop('selectedIndex', 0).change();
	$("#createPriority").prop('selectedIndex', 0).change();
	$('#createTitle').val("");
	$('#createDescription').val("");
	$('#createBefore').val("");
	$('#createAfter').val("");
	// $('#createDueFrom').val("");
	$('#createDueTo').val("");
	$('#createAttachment').val("");
	$('#createDocument').val("");
	$('#tableCostDownBody').html("");
	costdowns = [];
	costdown_count = 0;
}

</script>

@endsection