@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
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
		vertical-align: middle;
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
	html {
	  scroll-behavior: smooth;
	}


	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}*/
	.td_hover:hover {
		background-color: #fff4a1 !important;
		cursor: pointer;
	}
	.td_biasa:hover {
		cursor: pointer;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}

	#tableCode > tbody > tr > td:hover{
		background-color: #7dfa8c !important;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
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
	<div class="row" style="padding-left: 0px;padding-right: 0px;">
		<input type="hidden" name="category" id="category">
		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="col-xs-12" style="padding-right: 0px;margin-bottom: 10px;">
				@if($mp_ut == 'maintenance-ut')
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 15px;margin-bottom: 5px;background-color: #d391ff;text-align: center;font-size: 25px;cursor: pointer;" onclick="changeTbm('all')">
					<span style="font-weight: bold;padding: 15px;">
						TBM UTILITY
					</span>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('v-belt')">
						Changing V-Belt
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('cleaning-ac')">
						Cleaning AC
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('cooling-tower')">
						Cleaning Cooling Tower
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('water-cooler')">
						Cleaning AC Water Cooler
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('chiller')">
						Cleaning Chiller
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('water-scrubber')">
						Cleaning Water Scrubber
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('grease-trap')">
						Cleaning Grease Trap
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('strainer-st')">
						Cleaning Strainer ST
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('greasing-dc')">
						Greasing Dust Collector
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('piping-gas')">
						Check Piping Gas Leak
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('travo')">
						Check Travo
					</button>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('emergency-lamp')">
						Check Emergency Lamp
					</button>
				</div>
				@elseif($mp_ut == 'maintenance-mp')
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 15px;margin-bottom: 5px;background-color: ivory;text-align: center;font-size: 25px;cursor: pointer;" onclick="changeTbm('all');hideDetail()">
					<span style="font-weight: bold;padding: 15px;">
						TBM MESIN PRODUKSI
					</span>
				</div>
				<table style="width: 100%" class="mp-utama">
					<tr>
						<td style="width: 20%">
							<button class="btn btn-primary" style="width: 100%;font-weight: bold;" onclick="showDetail('soldering')">
								Soldering
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-primary" style="width: 100%;font-weight: bold;" onclick="showDetail('machining1')">
								Machining 1st
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-primary" style="width: 100%;font-weight: bold;" onclick="showDetail('machining2')">
								Machining 2nd
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-primary" style="width: 100%;font-weight: bold;" onclick="showDetail('barrel')">
								Barrel
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-primary" style="width: 100%;font-weight: bold;" onclick="showDetail('buffing')">
								Buffing
							</button>
						</td>
					</tr>
				</table>

				<div class="col-xs-2 soldering" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('air-strainer')">
						Saluran Air & Strainer (Sold Inverter)
					</button>
				</div>
				<div class="col-xs-2 soldering" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('meja-soldering')">
						Cleaning Meja Soldering
					</button>
				</div>
				<div class="col-xs-2 soldering" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('coil-holder')">
						Cleaning Coil Holder
					</button>
				</div>
				<div class="col-xs-2 soldering" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('nipple')">
						Change Nipple
					</button>
				</div>
				<div class="col-xs-2 soldering" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('hose-bening')">
						Change Hose Bening
					</button>
				</div>
				<div class="col-xs-2 soldering" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('hose-triode')">
						Change Hose Triode
					</button>
				</div>

				<div class="col-xs-3 machining1" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('overhaul-sc')">
						Overhaul SC
					</button>
				</div>
				<div class="col-xs-3 machining1" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('oilmist-1')">
						Cleaning Filter Oilmist MC1
					</button>
				</div>
				<div class="col-xs-3 machining1" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('check-megger')">
						Check Megger
					</button>
				</div>
				<div class="col-xs-3 machining1" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('daya-hisap-1')">
						Check Daya Hisap
					</button>
				</div>

				<div class="col-xs-4 machining2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('cleaning-spindle')">
						Cleaning Spindle & Cooling Fan
					</button>
				</div>
				<div class="col-xs-4 machining2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('daya-hisap-2')">
						Check Daya Hisap
					</button>
				</div>
				<div class="col-xs-4 machining2" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('oilmist-2')">
						Cleaning Filter Oilmist MC2
					</button>
				</div>

				<div class="col-xs-3 barrel" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('rantai-baut')">
						Check Rantai & Baut Getar
					</button>
				</div>
				<div class="col-xs-3 barrel" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('rantai-baut-daily')">
						Check Rantai & Baut Getar Harian
					</button>
				</div>
				<div class="col-xs-3 barrel" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('main-shaft')">
						Check Main Shaft
					</button>
				</div>
				<div class="col-xs-3 barrel" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('felt-ring')">
						Change Felt Ring
					</button>
				</div>

				<div class="col-xs-3 buffing" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('bearing-motor')">
						Penggantian Bearing Motor
					</button>
				</div>
				<div class="col-xs-3 buffing" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('bearing-shaft')">
						Penggantian Bearing Shaft
					</button>
				</div>
				<div class="col-xs-3 buffing" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('v-belt-buffing')">
						Penggantian V-Belt
					</button>
				</div>
				<div class="col-xs-3 buffing" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('v-belt-buffing-daily')">
						Penggantian V-Belt Daily
					</button>
				</div>

				<table style="width: 100%">
					<tr>
						<td style="width: 20%">
							<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('senban')">
								Senban
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('oiling-machine')">
								Oiling Machine
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('sanding-3-machine')">
								Sanding 3 Step Machine
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('grease-up')">
								Grease-Up Machine
							</button>
						</td>
						<td style="width: 20%">
							<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('jishu-hozen')">
								Jishu Hozen SC
							</button>
						</td>
					</tr>
				</table>
				@elseif($mp_ut == 'maintenance-vendor-mp')
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 15px;margin-bottom: 5px;background-color: lightskyblue;text-align: center;font-size: 25px;cursor: pointer;" onclick="changeTbm('all');hideDetail()">
					<span style="font-weight: bold;padding: 15px;">
						TBM VENDOR MESIN PRODUKSI
					</span>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('robodrill')">
						Robodrill
					</button>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('machining')">
						Machining
					</button>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('lathe')">
						Lathe
					</button>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('press')">
						Press
					</button>
				</div>
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-warning" style="width: 100%;font-weight: bold;" onclick="changeTbm('other')">
						Other
					</button>
				</div>
				@elseif($mp_ut == 'maintenance-vendor-ut')
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 15px;margin-bottom: 5px;background-color: lightgreen;text-align: center;font-size: 25px;cursor: pointer;">
					<span style="font-weight: bold;padding: 15px;">
						TBM VENDOR UTILITY
					</span>
				</div>
				@elseif($mp_ut == 'injection')
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 15px;margin-bottom: 5px;background-color: #d391ff;text-align: center;font-size: 25px;cursor: pointer;" onclick="changeTbm('all')">
					<span style="font-weight: bold;padding: 15px;">
						TBM MACHINE INJECTION
					</span>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('injection-filter')">
						Filter
					</button>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('injection-hose')">
						Selang
					</button>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('greas-up-injection')">
						Grease Up Mesin Injeksi
					</button>
				</div>
				<div class="col-xs-3" style="padding-left: 0px;padding-right: 5px;margin-bottom: 5px;">
					<button class="btn btn-success" style="width: 100%;font-weight: bold;" onclick="changeTbm('greas-up-robot')">
						Grease Up Robot
					</button>
				</div>
				@endif
			</div>
			<!-- <div class="col-xs-2" style="padding-right: 5px;">
				<select class="form-control select2" data-placeholder="Pilih Category" id="select_category" onchange="changeTbm(this.value)">
					<option value=""></option>
					<option value="v-belt">V-Belt</option>
					<option value="ac-cleaner">AC Cleaner</option>
				</select>
			</div> -->
			<div class="col-xs-2" style="padding-right: 5px;">
				<select class="form-control select2" data-placeholder="Pilih Fiscal Year" id="fiscal_year" onchange="fillData()">
					<option value=""></option>
					@foreach($fy_all as $fy)
					<option value="{{$fy->fiscal_year}}">{{$fy->fiscal_year}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
				<!-- <select class="form-control select2" data-placeholder="Pilih Point Check" id="point_id" onchange="fillData()">
					<option value=""></option>
				</select> -->
				<div class="input-group date">
					<div class="input-group-addon bg-white">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="month" name="month" placeholder="Select Month" autocomplete="off" onchange="fillData()">
				</div>
			</div>
			<div class="col-xs-5 pull-right" style="padding-left: 0px;">
				<a class="btn btn-warning pull-right" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);display: none;" id="btn_point" href="">
					<i class="fa fa-list"></i>&nbsp;&nbsp;Point Check
				</a>
			</div>
		</div>
		<div class="col-xs-12">
			<div id="container"></div>
		</div>
		<div class="col-md-12" style="overflow-x: scroll;">
			<table id="tableSchedule" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);" id="headTableSchedule">
				</thead>
				<tbody id="bodyTableSchedule">
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal modal-default fade" id="add-schedule-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Schedule</h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" class="form-control" id="add_id_schedule" placeholder="id" required>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_location" placeholder="Location" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Machine<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_point_check" placeholder="Machine" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Note<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_scan_index" placeholder="Note" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule Date<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_schedule_date" placeholder="Schedule Date" required readonly>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="do-schedule-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white">Doing Schedule</h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" class="form-control" id="do_id_schedule" placeholder="id" required>
								<table class="table table-bordered table-striped table-hover">
									<tr>
										<th style="border:1px solid black;background-color: #deffa1">Loc</th>
										<th style="border:1px solid black;background-color: #deffa1">Mesin</th>
										<th style="border:1px solid black;background-color: #deffa1">Note</th>
										<th style="border:1px solid black;background-color: #deffa1">Spec</th>
										<th style="border:1px solid black;background-color: #deffa1">Periode</th>
										<th style="border:1px solid black;background-color: #deffa1">Schedule Date / Month</th>
									</tr>
									<tr>
										<td id="do_location"></td>
										<td id="do_point_check"></td>
										<td><span id="do_scan_index"></span></td>
										<td><span id="do_specification"></span></td>
										<td><span id="do_image_reference"></span></td>
										<td id="do_schedule_date"></td>
									</tr>
									<tr>
										<th colspan="2" style="border:1px solid black;background-color: #deffa1">Evidence</th>
										<th style="border:1px solid black;background-color: #deffa1">Report PDF</th>
										<th style="border:1px solid black;background-color: #deffa1" id="th_values_1">Hasil Pengukuran</th>
										<th style="border:1px solid black;background-color: #deffa1" id="th_values_2">Hasil Pengukuran Sub Clamp</th>
										<th style="border:1px solid black;background-color: #deffa1" id="th_values_3">Hasil Pengukuran Main Clamp</th>
										<th colspan="2" style="border:1px solid black;background-color: #deffa1">Note</th>
									</tr>
									<tr>
										<td colspan="2">
											<input class="form-control" type="file" name="evidence" id="evidence">
										</td>
										<td>
											<input class="form-control" type="file" name="report" id="report">
										</td>
										<td id="td_values1">
											<input class="form-control numpad" type="text" readonly="" name="values" id="values" placeholder="Pengukuran">
										</td>
										<td id="td_values2">
											<input class="form-control numpad" type="text" readonly="" name="values2" id="values2" placeholder="Pengukuran">
										</td>
										<td colspan="2">
											<textarea id="note" style="width: 100%"></textarea>
											<script type="text/javascript">
											CKEDITOR.replace('note' ,{
										        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
										        height: '100px',
										        toolbar:'MA'
										    });
										    CKEDITOR.instances.note.config.readOnly = false;
											</script>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<?php if (str_contains($role,'S-') || str_contains($role,'L-') || str_contains($role,'C-')): ?>
						<button class="btn btn-danger pull-left" onclick="deleteSchedule()"><i class="fa fa-close"></i> DELETE</button>
						<button class="btn btn-success pull-left" id="btn_add"><i class="fa fa-plus"></i> ADD SCHEDULE</button>
					<?php endif ?>
					<button class="btn btn-success" onclick="doing()"><i class="fa fa-check"></i> CONFIRM</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="all-schedule-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white" id="title_schedule"></h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12" id="tab_schedule">
							<!-- <div class="box-body" id="tab_schedule">
								
							</div> -->
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#all-schedule-modal').modal('hide')"><i class="fa fa-close"></i> CLOSE</button>
					<?php if (str_contains($role,'S-') || str_contains($role,'L-') || str_contains($role,'C-')): ?>
						<button class="btn btn-success pull-left" id="btn_add2"><i class="fa fa-plus"></i> ADD SCHEDULE</button>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="all-schedule-done-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white" id="title_schedule_done"></h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12" id="tab_schedule_done">
							<!-- <div class="box-body" id="tab_schedule">
								
							</div> -->
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#all-schedule-done-modal').modal('hide')"><i class="fa fa-close"></i> CLOSE</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="detail-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail</h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<table class="table table-bordered table-striped table-hover">
									<tr>
										<th style="border:1px solid black;background-color: #deffa1">Loc</th>
										<th style="border:1px solid black;background-color: #deffa1">Mesin</th>
										<th style="border:1px solid black;background-color: #deffa1">Note</th>
										<th style="border:1px solid black;background-color: #deffa1">Spec</th>
										<th style="border:1px solid black;background-color: #deffa1">Periode</th>
										<th style="border:1px solid black;background-color: #deffa1">Schedule Date / Month</th>
										<th style="border:1px solid black;background-color: #deffa1">Audited At</th>
										<th style="border:1px solid black;background-color: #deffa1">Auditor</th>
									</tr>
									<tr>
										<td id="detail_location"></td>
										<td id="detail_point_check"></td>
										<td><span id="detail_scan_index"></span></td>
										<td><span id="detail_specification"></span></td>
										<td><span id="detail_image_reference"></span></td>
										<td id="detail_schedule_date"></td>
										<td id="detail_audited_at"></td>
										<td id="detail_auditor"></td>
									</tr>
									<tr>
										<th colspan="2" style="border:1px solid black;background-color: #deffa1">Evidence</th>
										<th style="border:1px solid black;background-color: #deffa1">Report PDF</th>
										<th style="border:1px solid black;background-color: #deffa1">Hasil Pengukuran</th>
										<th colspan="4" style="border:1px solid black;background-color: #deffa1">Note</th>
									</tr>
									<tr>
										<td colspan="2" id="detail_evidence">
											
										</td>
										<td id="detail_report">
											
										</td>
										<td id="detail_values" style="text-align: center;font-size: 20px;">
											
										</td>
										<td colspan="4">
											<textarea id="detail_note" style="width: 100%"></textarea>
											<script type="text/javascript">
											// $('#detail_note').attr('disabled', 'disabled');
											CKEDITOR.replace('detail_note' ,{
										        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
										        height: '100px',
										        toolbar:'MA'
										    });
										    CKEDITOR.instances.detail_note.config.readOnly = true;
											</script>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#detail-modal').modal('hide')"><i class="fa fa-close"></i> CLOSE</button>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr_sudah = null;
	var arr_belum = null;
	var kataconfirm = 'Apakah Anda yakin?';

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];
		$('#th_values_1').show();
		$('#th_values_2').hide();
		$('#th_values_3').hide();
		$('#td_values2').hide();
		$('.soldering').hide();
		$('.machining1').hide();
		$('.machining2').hide();
		$('.barrel').hide();
		$('.buffing').hide();
		if ('{{$mp_ut}}' == 'maintenance-vendor-ut') {
			$('#category').val('maintenance-vendor-ut-all');
		}else{
			$('#category').val('all');
		}
		// document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/v-belt')}}");
		$('#btn_point').hide();
		$('body').toggleClass("sidebar-collapse");

		fillData();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		arr_sudah = null;
		arr_belum = null;

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	function showDetail(classes) {
		$('.soldering').hide();
		$('.machining1').hide();
		$('.machining2').hide();
		$('.barrel').hide();
		$('.buffing').hide();
		$('.'+classes).show();
		$('.mp-utama').hide();
	}

	function hideDetail() {
		$('.soldering').hide();
		$('.machining1').hide();
		$('.machining2').hide();
		$('.barrel').hide();
		$('.buffing').hide();
		$('.mp-utama').show();
	}

	function changeTbm(category) {
		if (category == '') {
			$("#category").val('all');
			$('#btn_point').hide();
			document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/')}}"+'/all/'+'{{$mp_ut}}');
		}else{
			$('#btn_point').show();
			document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/')}}"+'/'+category+'/'+'{{$mp_ut}}');
			$("#category").val(category);
		}
		// $("#titles").html(category.toUpperCase());
		$('#th_values_1').show();
		$('#th_values_2').hide();
		$('#th_values_3').hide();
		$('#td_values2').hide();
		if ($('#category').val().match(/daya-hisap/gi)) {
			$('#th_values_1').hide();
			$('#th_values_2').show();
			$('#th_values_3').show();
			$('#td_values2').show();
		}else{
			$('#th_values_1').show();
			$('#th_values_2').hide();
			$('#th_values_3').hide();
			$('#td_values2').hide();
		}
		fillData();
	}

	function fillData(){
		$('#loading').show();
		var category = 'all';
		if ($('#category').val() != '') {
			category = $('#category').val();
		}

		var data = {
			category:category,
			fy:$('#fiscal_year').val(),
			// point_id:$('#point_id').val(),
			month:$('#month').val(),
			mp_ut:'{{$mp_ut}}',
		}
		
		$.get('{{ url("fetch/maintenance/tbm/") }}', data,function(result, status, xhr){
			if(result.status){
				if (!category.match(/daily/gi)) {
					//MONTHLY
					$('#headTableSchedule').html("");
					$('#bodyTableSchedule').html("");
					var tableScheduleBody = "";
					var tableScheduleHead = "";

					var total_sudah = [];
					var total_belum = [];
					var categories = [];
					
					for(var i = 0; i < result.resume.length;i++){
						categories.push((result.resume[i].location || result.resume[i].point_check)+' - '+result.resume[i].scan_index);
						total_sudah.push({y:parseInt(result.resume[i].sudah),key:result.resume[i].point_id});
						total_belum.push({y:parseInt(result.resume[i].belum),key:result.resume[i].point_id});
					}
					var index = 1;
						tableScheduleHead += '<tr>';
						tableScheduleHead += '<th style="color:white">#</th>';
						tableScheduleHead += '<th style="color:white">Cat</th>';
						tableScheduleHead += '<th style="color:white">Loc</th>';
						tableScheduleHead += '<th style="color:white">Machine</th>';
						tableScheduleHead += '<th style="color:white">Note</th>';
						tableScheduleHead += '<th style="color:white">Spec</th>';
						tableScheduleHead += '<th style="color:white">Periode</th>';
						tableScheduleHead += '<th style="color:white">Activity</th>';
						$.each(result.month, function(key, value) {
							tableScheduleHead += '<th style="color:white">'+value.month_name+'</th>';
						});
						tableScheduleHead += '</tr>';

					$('#headTableSchedule').append(tableScheduleHead);

					var points = [];
					for(var i = 0; i < result.schedule.length;i++){
						points.push(result.schedule[i].point_id);
					}

					var index = 1;
					for(var i = 0; i < result.point_check.length;i++){
						if (category == 'all') {
							if (points.includes(result.point_check[i].id)) {
								tableScheduleBody += '<tr id="'+result.point_check[i].id+'">';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;text-align:right;padding-right4px;">'+index+'</td>';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+result.point_check[i].category.toUpperCase()+'</td>';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].location || '')+'</td>';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].point_check || '')+'</td>';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].scan_index || '')+'</td>';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].specification || '')+'</td>';
								tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].image_reference || '')+'</td>';
								tableScheduleBody += '<td style="border:1px solid black;background-color:white;">Plan</td>';
								for(var j = 0; j < result.month.length;j++){
									var scheduled = 'white';
									var classes = 'td_hover';
									var values = null;
									var measurement = [];
									var sudah = [];
									var id_schedule = [];
									var schedule_status = [];
									for(var k = 0; k < result.schedule.length;k++){
										if (result.month[j].month == result.schedule[k].schedule_month && result.schedule[k].point_id == result.point_check[i].id) {
											scheduled = '#ffd9d9';
											classes = 'td_biasa';
											values += 1;
											id_schedule.push(result.schedule[k].id);
											schedule_status.push(result.schedule[k].schedule_status);
											if (result.schedule[k].schedule_status == 'Sudah Dikerjakan') {
												sudah.push('Sudah');
											}else{
												sudah.push('Belum');
											}
										}
									}
									if (values == null) {
										if ('{{$role}}'.match(/L-/gi) || '{{$role}}'.match(/MIS/gi) || '{{$role}}'.match(/C-/gi)) {
											tableScheduleBody += '<td onclick="addSchedule(\''+result.point_check[i].id+'\',\''+result.month[j].month+'\',\''+(result.point_check[i].location || '')+'\',\''+(result.point_check[i].point_check || '')+'\',\''+(result.point_check[i].scan_index || '')+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}else{
											tableScheduleBody += '<td class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}
									}else{
										if (sudah.join().match(/Belum/gi)) {
											if (values > 1) {
												tableScheduleBody += '<td onclick="allSchedule(\''+id_schedule.join()+'\',\''+result.month[j].month+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].location+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
											}else{
												tableScheduleBody += '<td onclick="doSchedule(\''+id_schedule[0]+'\',\''+result.month[j].month+'\',\''+(result.point_check[i].location || '')+'\',\''+(result.point_check[i].point_check || '')+'\',\''+(result.point_check[i].scan_index || '')+'\',\''+(result.point_check[i].image_reference || '')+'\',\''+(result.point_check[i].specification || '')+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].category+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
											}
										}else{
											tableScheduleBody += '<td onclick="allSchedule(\''+id_schedule.join()+'\',\''+result.month[j].month+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].location+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}
									}
								}
								tableScheduleBody += '</tr>';
								tableScheduleBody += '<tr>';
								tableScheduleBody += '<td style="border:1px solid black;background-color:white;">Actual</td>';
								for(var j = 0; j < result.month.length;j++){
									var scheduled = 'white';
									var classes = 'td_hover';
									var values = null;
									var id_schedule = [];
									var schedule_status = [];
									var measurement = [];
									for(var k = 0; k < result.schedule.length;k++){
										if (result.month[j].month == result.schedule[k].schedule_month && result.schedule[k].point_id == result.point_check[i].id && result.schedule[k].schedule_status == 'Sudah Dikerjakan') {
											scheduled = '#d9ffdd';
											classes = 'td_biasa';
											id_schedule.push(result.schedule[k].id);
											schedule_status.push(result.schedule[k].schedule_status);
											values += 1;
											if (result.schedule[k].values != null) {
												measurement.push(result.schedule[k].values);
											}
											if (result.schedule[k].values2 != null) {
												measurement.push(result.schedule[k].values2);
											}
										}
									}
									var meas = null;
									if (measurement.length > 0) {
										if (result.point_check[i].category.match(/megger/gi)) {
											if (measurement.length > 1) {
												meas = 'Check 1 : '+measurement[0]+'<br>'+'Check 2 : '+measurement[1];
											}else{
												meas = 'Check 1 : '+measurement.join(',');
											}
										}else if (result.point_check[i].category.match(/daya-hisap/gi)) {
											if (measurement.length == 2) {
												meas = 'Sub Clamp : '+measurement[0]+'<br>'+'Main Clamp : '+measurement[1];
											}else{
												meas = 'Sub Clamp : '+measurement[2]+'<br>'+'Main Clamp : '+measurement[3];
											}
										}else{
											meas = 'Hasil : '+measurement.join(',');
										}
									}
									if (values != null) {
										if (values > 1) {
											tableScheduleBody += '<td onclick="allScheduleDone(\''+id_schedule.join()+'\',\''+result.month[j].month+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
										}else{
											tableScheduleBody += '<td onclick="details(\''+id_schedule[0]+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
										}
									}else{
										tableScheduleBody += '<td class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
									}
								}
								tableScheduleBody += '</tr>';
								index++;
							}
						}else{
							tableScheduleBody += '<tr id="'+result.point_check[i].id+'">';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;text-align:right;padding-right4px;">'+index+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+result.point_check[i].category.toUpperCase()+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].location || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].point_check || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].scan_index || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].specification || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].image_reference || '')+'</td>';
							tableScheduleBody += '<td style="border:1px solid black;background-color:white;">Plan</td>';
							for(var j = 0; j < result.month.length;j++){
								var scheduled = 'white';
								var classes = 'td_hover';
								var values = null;
								var sudah = [];
								var id_schedule = [];
								var schedule_status = [];
								for(var k = 0; k < result.schedule.length;k++){
									if (result.month[j].month == result.schedule[k].schedule_month && result.schedule[k].point_id == result.point_check[i].id) {
										scheduled = '#ffd9d9';
										classes = 'td_biasa';
										values += 1;
										id_schedule.push(result.schedule[k].id);
										schedule_status.push(result.schedule[k].schedule_status);
										if (result.schedule[k].schedule_status == 'Sudah Dikerjakan') {
											sudah.push('Sudah');
										}else{
											sudah.push('Belum');
										}
									}
								}
								if (values == null) {
									if ('{{$role}}'.match(/L-/gi) || '{{$role}}'.match(/MIS/gi) || '{{$role}}'.match(/C-/gi)) {
										tableScheduleBody += '<td onclick="addSchedule(\''+result.point_check[i].id+'\',\''+result.month[j].month+'\',\''+(result.point_check[i].location || '')+'\',\''+(result.point_check[i].point_check || '')+'\',\''+(result.point_check[i].scan_index || '')+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
									}else{
										tableScheduleBody += '<td class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
									}
								}else{
									if (sudah.join().match(/Belum/gi)) {
										if (values > 1) {
											tableScheduleBody += '<td onclick="allSchedule(\''+id_schedule.join()+'\',\''+result.month[j].month+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].location+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}else{
											tableScheduleBody += '<td onclick="doSchedule(\''+id_schedule[0]+'\',\''+result.month[j].month+'\',\''+(result.point_check[i].location || '')+'\',\''+(result.point_check[i].point_check || '')+'\',\''+(result.point_check[i].scan_index || '')+'\',\''+(result.point_check[i].image_reference || '')+'\',\''+(result.point_check[i].specification || '')+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].category+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}
									}else{
										tableScheduleBody += '<td onclick="allSchedule(\''+id_schedule.join()+'\',\''+result.month[j].month+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].location+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
									}
								}
							}
							tableScheduleBody += '</tr>';
							tableScheduleBody += '<tr>';
							tableScheduleBody += '<td style="border:1px solid black;background-color:white;">Actual</td>';
							for(var j = 0; j < result.month.length;j++){
								var scheduled = 'white';
								var classes = 'td_hover';
								var values = null;
								var measurement = [];
								var id_schedule = [];
								var schedule_status = [];
								for(var k = 0; k < result.schedule.length;k++){
									if (result.month[j].month == result.schedule[k].schedule_month && result.schedule[k].point_id == result.point_check[i].id && result.schedule[k].schedule_status == 'Sudah Dikerjakan') {
										scheduled = '#d9ffdd';
										classes = 'td_biasa';
										id_schedule.push(result.schedule[k].id);
										schedule_status.push(result.schedule[k].schedule_status);
										values += 1;
										if (result.schedule[k].values != null) {
											measurement.push(result.schedule[k].values);
										}
										if (result.schedule[k].values2 != null) {
											measurement.push(result.schedule[k].values2);
										}
									}
								}
								var meas = null;
								if (measurement.length > 0) {
									if (result.point_check[i].category.match(/megger/gi)) {
										if (measurement.length > 1) {
											meas = 'Check 1 : '+measurement[0]+'<br>'+'Check 2 : '+measurement[1];
										}else{
											meas = 'Check 1 : '+measurement.join(',');
										}
									}else if (result.point_check[i].category.match(/daya-hisap/gi)) {
										if (measurement.length == 2) {
											meas = 'Sub Clamp : '+measurement[0]+'<br>'+'Main Clamp : '+measurement[1];
										}else{
											meas = 'Sub Clamp : '+measurement[2]+'<br>'+'Main Clamp : '+measurement[3];
										}
									}else{
										meas = 'Hasil : '+measurement.join(',');
									}
								}
								if (values != null) {
									if (values > 1) {
										tableScheduleBody += '<td onclick="allScheduleDone(\''+id_schedule.join()+'\',\''+result.month[j].month+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
									}else{
										tableScheduleBody += '<td onclick="details(\''+id_schedule[0]+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
									}
								}else{
									tableScheduleBody += '<td class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
								}
							}
							tableScheduleBody += '</tr>';
							index++;
						}
					}


					$('#bodyTableSchedule').append(tableScheduleBody);

					var cats = '';

					if (category == 'all') {
						cats = 'ALL POINT ('+result.monthTitle+')';
					}else{
						cats = category.toUpperCase()+' ('+result.fy+')';
					}

					const chart = new Highcharts.Chart({
					    chart: {
					        renderTo: 'container',
					        type: 'column',
					        backgroundColor:'none',
					        options3d: {
					            enabled: true,
					            alpha: 0,
					            beta: 0,
					            depth: 50,
					            viewDistance: 25
					        }
					    },
					    xAxis: {
							categories: categories,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Data <small>トータルデータ</small>',
								style: {
									color: '#eee',
									fontSize: '12px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							allowDecimals: false,
							labels:{
								style:{
									fontSize:"12px"
								}
							},
							type: 'linear',
						}
						],
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#1c1c1c',
							itemStyle: {
								fontSize:'12px',
							},
						},	
					    title: {
					        text: '<b>TBM SCHEDULE - '+cats+'</b>',
					    },
					    subtitle: {
					        text: ''
					    },
					    plotOptions: {
					        series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category,this.series.name,this.options.key);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '0.9vw'
									}
								},
								animation: false,
								cursor: 'pointer',
								depth:25
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [{
							type: 'column',
							data: total_sudah,
							name: 'Sudah Dikerjakan',
							colorByPoint: false,
							color:'#32a852'
						},{
							type: 'column',
							data: total_belum,
							name: 'Belum Dikerjakan',
							colorByPoint: false,
							color:'#f44336'
						}
						]
					});

					if (category == '' || category == 'all') {
						$('#btn_point').hide();
						document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/')}}"+'/all/'+'{{$mp_ut}}');
					}else{
						$('#btn_point').show();
						document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/')}}"+'/'+category+'/'+'{{$mp_ut}}');
					}

					$('#loading').hide();
				}else{
					//DAILY
					$('#headTableSchedule').html("");
					$('#bodyTableSchedule').html("");
					var tableScheduleBody = "";
					var tableScheduleHead = "";

					var total_sudah = [];
					var total_belum = [];
					var categories = [];
					
					for(var i = 0; i < result.resume_daily.length;i++){
						categories.push((result.resume_daily[i].location || result.resume_daily[i].point_check)+' - '+result.resume_daily[i].scan_index);
						total_sudah.push({y:parseInt(result.resume_daily[i].sudah),key:result.resume_daily[i].point_id});
						total_belum.push({y:parseInt(result.resume_daily[i].belum),key:result.resume_daily[i].point_id});
					}

					var cats = '';

					if (category == 'all') {
						cats = 'ALL POINT ('+result.monthTitle+')';
					}else{
						cats = category.toUpperCase()+' ('+result.monthTitle+')';
					}

					const chart = new Highcharts.Chart({
					    chart: {
					        renderTo: 'container',
					        type: 'column',
					        backgroundColor:'none',
					        options3d: {
					            enabled: true,
					            alpha: 0,
					            beta: 0,
					            depth: 50,
					            viewDistance: 25
					        }
					    },
					    xAxis: {
							categories: categories,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Data <small>トータルデータ</small>',
								style: {
									color: '#eee',
									fontSize: '12px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							allowDecimals: false,
							labels:{
								style:{
									fontSize:"12px"
								}
							},
							type: 'linear',
						}
						],
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#1c1c1c',
							itemStyle: {
								fontSize:'12px',
							},
						},	
					    title: {
					        text: '<b>TBM SCHEDULE - '+cats+'</b>',
					    },
					    subtitle: {
					        text: ''
					    },
					    plotOptions: {
					        series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category,this.series.name,this.options.key);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '0.9vw'
									}
								},
								animation: false,
								cursor: 'pointer',
								depth:25
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [{
							type: 'column',
							data: total_sudah,
							name: 'Sudah Dikerjakan',
							colorByPoint: false,
							color:'#32a852'
						},{
							type: 'column',
							data: total_belum,
							name: 'Belum Dikerjakan',
							colorByPoint: false,
							color:'#f44336'
						}
						]
					});

					var index = 1;
						tableScheduleHead += '<tr>';
						tableScheduleHead += '<th style="color:white">#</th>';
						tableScheduleHead += '<th style="color:white">Cat</th>';
						tableScheduleHead += '<th style="color:white">Loc</th>';
						tableScheduleHead += '<th style="color:white">Machine</th>';
						tableScheduleHead += '<th style="color:white">Note</th>';
						tableScheduleHead += '<th style="color:white">Spec</th>';
						tableScheduleHead += '<th style="color:white">Periode</th>';
						tableScheduleHead += '<th style="color:white">Activity</th>';
						$.each(result.calendar, function(key, value) {
							if (value.remark.match(/H/gi)) {
								tableScheduleHead += '<th style="background-color:black;color:white">'+value.dates+'</th>';
							}else{
								tableScheduleHead += '<th style="color:white">'+value.dates+'</th>';
							}
						});
						tableScheduleHead += '</tr>';

					$('#headTableSchedule').append(tableScheduleHead);

					var points = [];
					for(var i = 0; i < result.schedule_daily.length;i++){
						points.push(result.schedule_daily[i].point_id);
					}

					var index = 1;
					for(var i = 0; i < result.point_check.length;i++){
							tableScheduleBody += '<tr id="'+result.point_check[i].id+'">';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;text-align:right;padding-right4px;">'+index+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+result.point_check[i].category.toUpperCase()+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].location || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].point_check || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].scan_index || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].specification || '')+'</td>';
							tableScheduleBody += '<td rowspan="2" style="border:1px solid black;">'+(result.point_check[i].image_reference || '')+'</td>';
							tableScheduleBody += '<td style="border:1px solid black;background-color:white;">Plan</td>';
							for(var j = 0; j < result.calendar.length;j++){
								var scheduled = 'white';
								var classes = 'td_hover';
								var values = null;
								var sudah = [];
								var id_schedule = [];
								var schedule_status = [];
								for(var k = 0; k < result.schedule_daily.length;k++){
									if (result.calendar[j].week_date == result.schedule_daily[k].schedule_date && result.schedule_daily[k].point_id == result.point_check[i].id) {
										scheduled = '#ffd9d9';
										classes = 'td_biasa';
										values += 1;
										id_schedule.push(result.schedule_daily[k].id);
										schedule_status.push(result.schedule_daily[k].schedule_status);
										if (result.schedule_daily[k].schedule_status == 'Sudah Dikerjakan') {
											sudah.push('Sudah');
										}else{
											sudah.push('Belum');
										}
									}
								}
								if (values == null) {
									if ('{{$role}}'.match(/L-/gi) || '{{$role}}'.match(/MIS/gi) || '{{$role}}'.match(/C-/gi)) {
										tableScheduleBody += '<td onclick="addSchedule(\''+result.point_check[i].id+'\',\''+result.calendar[j].week_date+'\',\''+(result.point_check[i].location || '')+'\',\''+(result.point_check[i].point_check || '')+'\',\''+(result.point_check[i].scan_index || '')+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
									}else{
										tableScheduleBody += '<td class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
									}
								}else{
									if (sudah.join().match(/Belum/gi)) {
										if (values > 1) {
											tableScheduleBody += '<td onclick="allSchedule(\''+id_schedule.join()+'\',\''+result.calendar[j].week_date+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].location+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}else{
											tableScheduleBody += '<td onclick="doSchedule(\''+id_schedule[0]+'\',\''+result.calendar[j].week_date+'\',\''+(result.point_check[i].location || '')+'\',\''+(result.point_check[i].point_check || '')+'\',\''+(result.point_check[i].scan_index || '')+'\',\''+(result.point_check[i].image_reference || '')+'\',\''+(result.point_check[i].specification || '')+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].category+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
										}
									}else{
										tableScheduleBody += '<td onclick="allSchedule(\''+id_schedule.join()+'\',\''+result.calendar[j].week_date+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].location+'\',\''+result.point_check[i].id+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'</td>';
									}
								}
							}
							tableScheduleBody += '</tr>';
							tableScheduleBody += '<tr>';
							tableScheduleBody += '<td style="border:1px solid black;background-color:white;">Actual</td>';
							for(var j = 0; j < result.calendar.length;j++){
								var scheduled = 'white';
								var classes = 'td_hover';
								var values = null;
								var measurement = [];
								var id_schedule = [];
								var schedule_status = [];
								for(var k = 0; k < result.schedule_daily.length;k++){
									if (result.calendar[j].week_date == result.schedule_daily[k].schedule_date && result.schedule_daily[k].point_id == result.point_check[i].id && result.schedule_daily[k].schedule_status == 'Sudah Dikerjakan') {
										scheduled = '#d9ffdd';
										classes = 'td_biasa';
										id_schedule.push(result.schedule_daily[k].id);
										schedule_status.push(result.schedule_daily[k].schedule_status);
										values += 1;
										if (result.schedule_daily[k].values != null) {
											measurement.push(result.schedule_daily[k].values);
										}
										if (result.schedule_daily[k].values2 != null) {
											measurement.push(result.schedule_daily[k].values2);
										}
									}
								}
								var meas = null;
								if (measurement.length > 0) {
									if (result.point_check[i].category.match(/megger/gi)) {
										if (measurement.length > 1) {
											meas = 'Check 1 : '+measurement[0]+'<br>'+'Check 2 : '+measurement[1];
										}else{
											meas = 'Check 1 : '+measurement.join(',');
										}
									}else if (result.point_check[i].category.match(/daya-hisap/gi)) {
										if (measurement.length == 2) {
											meas = 'Sub Clamp : '+measurement[0]+'<br>'+'Main Clamp : '+measurement[1];
										}else{
											meas = 'Sub Clamp : '+measurement[2]+'<br>'+'Main Clamp : '+measurement[3];
										}
									}else{
										meas = 'Hasil : '+measurement.join(',');
									}
								}
								if (values != null) {
									if (values > 1) {
										tableScheduleBody += '<td onclick="allScheduleDone(\''+id_schedule.join()+'\',\''+result.calendar[j].week_date+'\',\''+schedule_status.join()+'\',\''+result.point_check[i].point_check+'\',\''+result.point_check[i].scan_index+'\',\''+result.point_check[i].schedule_category+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
									}else{
										tableScheduleBody += '<td onclick="details(\''+id_schedule[0]+'\')" class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
									}
								}else{
									tableScheduleBody += '<td class="'+classes+'" style="border:1px solid black;text-align:center;background-color:'+scheduled+'">'+(values || '')+'<br>'+(meas || '')+'</td>';
								}
							}
							tableScheduleBody += '</tr>';
							index++;
					}


					$('#bodyTableSchedule').append(tableScheduleBody);

					if (category == '' || category == 'all') {
						$('#btn_point').hide();
						document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/')}}"+'/all/'+'{{$mp_ut}}');
					}else{
						$('#btn_point').show();
						document.getElementById('btn_point').setAttribute('href', "{{url('index/maintenance/point_check/tbm/')}}"+'/'+category+'/'+'{{$mp_ut}}');
					}
					$('#loading').hide();
				}
			}else{
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
			}
		});
	}

	function allSchedule(id,month,schedule_status,point_check,scan_index,location,point_id,schedule_category) {
		$('#loading').show();
		$('#tab_schedule').html('');
		$('#title_schedule').html('Schedule '+point_check+' - '+scan_index+' At '+month)
		var ids = id.split(',');
		var schedule_statuses = schedule_status.split(',');
		var index = 1;
		for(var i = 0; i < ids.length;i++){
			if (schedule_statuses[i] == 'Belum Dikerjakan') {
				$('#loading').show();
				var schedules = null;
				var data = {
					id:ids[i]
				}
				$.get('{{ url("fetch/maintenance/tbm/schedule") }}', data,function(result, status, xhr){
					if(result.status){
						schedules = result.schedule;
						var all_schedule = '';
						all_schedule += '<div class="col-xs-3" style="padding-left:5px;padding-right:5px;">';
						all_schedule += '<button style="width:100%;font-weight:bold;" onclick="doSchedule(\''+schedules.id+'\',\''+month+'\',\''+(schedules.location || '')+'\',\''+(schedules.point_check || '')+'\',\''+(schedules.scan_index || '')+'\',\''+(schedules.image_reference || '')+'\',\''+(schedules.specification || '')+'\',\''+schedules.point_id+'\',\''+schedules.category+'\',\''+schedule_category+'\')" class="btn btn-primary">Schedule '+index+'</button>';
						all_schedule += '</div>';
						$('#tab_schedule').append(all_schedule);
						$('#loading').hide();
						index++;
					}else{
						alert('Attempt to retrieve data failed');
						$('#loading').hide();
					}
				});
			}
		}
		$('#loading').hide();
		document.getElementById( "btn_add2" ).setAttribute( "onClick", 'javascript: addSchedule(\''+point_id+'\',\''+month+'\',\''+location+'\',\''+point_check+'\',\''+scan_index+'\');' );
		$('#all-schedule-modal').modal('show');
	}

	function allScheduleDone(id,month,schedule_status,point_check,scan_index,location,point_id,schedule_category) {
		$('#loading').show();
		$('#tab_schedule_done').html('');
		$('#title_schedule_done').html('Schedule '+point_check+' - '+scan_index+' At '+month)
		var ids = id.split(',');
		var schedule_statuses = schedule_status.split(',');
		var index = 1;
		for(var i = 0; i < ids.length;i++){
			if (schedule_statuses[i] == 'Sudah Dikerjakan') {
				$('#loading').show();
				var schedules = null;
				var data = {
					id:ids[i]
				}
				$.get('{{ url("fetch/maintenance/tbm/schedule") }}', data,function(result, status, xhr){
					if(result.status){
						schedules = result.schedule;
						var all_schedule = '';
						all_schedule += '<div class="col-xs-3" style="padding-left:5px;padding-right:5px;">';
						all_schedule += '<button style="width:100%;font-weight:bold;" onclick="details(\''+schedules.id+'\')" class="btn btn-primary">Schedule '+index+'</button>';
						all_schedule += '</div>';
						$('#tab_schedule_done').append(all_schedule);
						$('#loading').hide();
						index++;
					}else{
						alert('Attempt to retrieve data failed');
						$('#loading').hide();
					}
				});
			}
		}
		$('#loading').hide();
		$('#all-schedule-done-modal').modal('show');
	}

	function ShowModal(category,name,point_id) {
		$('#point_id').val(point_id).trigger('change');
	}

	function doSchedule(id,month,location,point_check,scan_index,image_reference,specification,id_point,category,schedule_category) {
		if (schedule_category == null || schedule_category == 'null') {
			$('#do_schedule_date').html(month.split('-')[0]+'-'+month.split('-')[1]);
		}else{
			$('#do_schedule_date').html(month);
		}
		$('#do_id_schedule').val(id);
		$('#do_location').html(location);
		$('#do_point_check').html(point_check);
		$('#do_scan_index').html(scan_index);
		$('#do_image_reference').html(image_reference);
		$('#do_specification').html(specification);
		$('#do-schedule-modal').modal('show');
		$('#values').val('');
		$('#values2').val('');
		$('#all-schedule-modal').hide();
		$('#all-schedule-modal').modal('hide');
		document.getElementById( "btn_add" ).setAttribute( "onClick", 'javascript: addSchedule(\''+id_point+'\',\''+month+'\',\''+location+'\',\''+point_check+'\',\''+scan_index+'\');' );

		$('#th_values_1').show();
		$('#th_values_2').hide();
		$('#th_values_3').hide();
		$('#td_values2').hide();
		if (category.match(/daya-hisap/gi)) {
			$('#th_values_1').hide();
			$('#th_values_2').show();
			$('#th_values_3').show();
			$('#td_values2').show();
		}else{
			$('#th_values_1').show();
			$('#th_values_2').hide();
			$('#th_values_3').hide();
			$('#td_values2').hide();
		}
		// document.getElementById('btn_add').onclick = addSchedule(id,month,location,point_check,scan_index);
	}

	function details(id) {
		$('#loading').show();
		var data = {
			id:id
		}

		$.get('{{ url("fetch/maintenance/tbm/schedule") }}', data,function(result, status, xhr){
			if(result.status){
				$('#detail_location').html(result.schedule.location);
				$('#detail_point_check').html(result.schedule.point_check);
				$('#detail_scan_index').html(result.schedule.scan_index);
				$('#detail_specification').html(result.schedule.specification);
				$('#detail_image_reference').html(result.schedule.image_reference);
				if (result.point_check.schedule_category == null) {
					$('#detail_schedule_date').html(result.schedule.schedule_date.split('-')[0]+'-'+result.schedule.schedule_date.split('-')[1]);
				}else{
					$('#detail_schedule_date').html(result.schedule.schedule_date);
				}
				$('#detail_audited_at').html(result.schedule.audited_at);
				$('#detail_auditor').html(result.schedule.auditor_id+' - '+result.schedule.auditor_name);
				var url = '{{url("data_file/maintenance/tbm")}}/'+result.schedule.evidence;
				$('#detail_evidence').html("<img src='"+url+"' style='width:200px;'>");
				if (result.schedule.report != null) {
					var url = '{{url("data_file/maintenance/tbm")}}/'+result.schedule.report;
					$('#detail_report').html("<a href='"+url+"' target='_blank'><i class='fa fa-file-pdf-o'></i></a>");
				}else{
					$('#detail_report').html("");
				}
				$("#detail_note").html(CKEDITOR.instances.detail_note.setData(result.schedule.note));
				if (result.schedule.values2 != null) {
					$('#detail_values').html(result.schedule.values +' - '+result.schedule.values2);
				}else{
					$('#detail_values').html(result.schedule.values);
				}
				// $("#detail_note").prop('disabled',true);
				$('#all-schedule-done-modal').hide();
				$('#all-schedule-done-modal').modal('hide');
				$("#detail-modal").modal('show');
				$('#loading').hide();
			}else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function deleteSchedule() {
		if (confirm(kataconfirm)) {
			$('#loading').show();
			var id = $('#do_id_schedule').val();
			var data = {
				id:id
			}

			$.get('{{ url("delete/maintenance/tbm/schedule") }}', data,function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Delete Schedule');
					$('#do-schedule-modal').modal('hide');
					$('#loading').hide();
					fillData();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
	}

	function addSchedule(id,month,location,point_check,scan_index) {
		$("#add_location").val(location);
		$("#add_point_check").val(point_check);
		$("#add_scan_index").val(scan_index);
		$("#add_id_schedule").val(id);
		$("#add_schedule_date").val(month);
		$('#do-schedule-modal').hide();
		$('#do-schedule-modal').modal('hide');
		$('#all-schedule-modal').hide();
		$('#all-schedule-modal').modal('hide');
		$('#add-schedule-modal').modal('show');
	}

	function add() {
		$('#loading').show();
		var location = $("#add_location").val();
		var point_check = $("#add_point_check").val();
		var scan_index = $("#add_scan_index").val();
		var id = $("#add_id_schedule").val();
		var schedule_date = $("#add_schedule_date").val();
		var category = $("#category").val();

		var data = {
			location:location,
			point_check:point_check,
			scan_index:scan_index,
			id:id,
			schedule_date:schedule_date,
			category:category,
		}

		$.post('{{ url("input/maintenance/tbm/schedule") }}', data,function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!','Success Add Schedule');
				$('#loading').hide();
				$('#add-schedule-modal').modal('hide');
				fillData();
			}else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

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
	
	function doing() {
		$('#loading').show();
		var id = $("#do_id_schedule").val();
		var note = CKEDITOR.instances['note'].getData();
		var valueses = $("#values").val();
		var valueses2 = $("#values2").val();

		if ($('#evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Upload Foto Evidence');
			return false;
		}

		var file = $('#evidence').prop('files')[0];
		var filename = $('#evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[0];
		var extension = $('#evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[1];

		var file2 = $('#report').prop('files')[0];
		var filename2 = $('#report').val().replace(/C:\\fakepath\\/i, '').split(".")[0];
		var extension2 = $('#report').val().replace(/C:\\fakepath\\/i, '').split(".")[1];

		var formData = new FormData();
		formData.append('id',id);
		formData.append('note',note);
		formData.append('file',file);
		formData.append('filename',filename);
		formData.append('extension',extension);
		formData.append('values',valueses);
		formData.append('values2',valueses2);
		formData.append('file2',file2);
		formData.append('filename2',filename2);
		formData.append('extension2',extension2);

		$.ajax({
			url:"{{ url('input/maintenance/tbm/doing') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					$('#loading').hide();
					$('#do-schedule-modal').modal('hide');
					fillData();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	Highcharts.createElement('link', {
			href: '{{ url("fonts/UnicaOne.css")}}',
			rel: 'stylesheet',
			type: 'text/css'
		}, null, document.getElementsByTagName('head')[0]);

		Highcharts.theme = {
			colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
			'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
			chart: {
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					stops: [
					[0, '#2a2a2b'],
					[1, '#3e3e40']
					]
				},
				style: {
					fontFamily: 'sans-serif'
				},
				plotBorderColor: '#606063'
			},
			title: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase',
					fontSize: '20px'
				}
			},
			subtitle: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase'
				}
			},
			xAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				title: {
					style: {
						color: '#A0A0A3'

					}
				}
			},
			yAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				tickWidth: 1,
				title: {
					style: {
						color: '#A0A0A3'
					}
				}
			},
			tooltip: {
				backgroundColor: 'rgba(0, 0, 0, 0.85)',
				style: {
					color: '#F0F0F0'
				}
			},
			plotOptions: {
				series: {
					dataLabels: {
						color: 'white'
					},
					marker: {
						lineColor: '#333'
					}
				},
				boxplot: {
					fillColor: '#505053'
				},
				candlestick: {
					lineColor: 'white'
				},
				errorbar: {
					color: 'white'
				}
			},
			legend: {
				itemStyle: {
					color: '#E0E0E3'
				},
				itemHoverStyle: {
					color: '#FFF'
				},
				itemHiddenStyle: {
					color: '#606063'
				}
			},
			credits: {
				style: {
					color: '#666'
				}
			},
			labels: {
				style: {
					color: '#707073'
				}
			},

			drilldown: {
				activeAxisLabelStyle: {
					color: '#F0F0F3'
				},
				activeDataLabelStyle: {
					color: '#F0F0F3'
				}
			},

			navigation: {
				buttonOptions: {
					symbolStroke: '#DDDDDD',
					theme: {
						fill: '#505053'
					}
				}
			},

			rangeSelector: {
				buttonTheme: {
					fill: '#505053',
					stroke: '#000000',
					style: {
						color: '#CCC'
					},
					states: {
						hover: {
							fill: '#707073',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						},
						select: {
							fill: '#000003',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						}
					}
				},
				inputBoxBorderColor: '#505053',
				inputStyle: {
					backgroundColor: '#333',
					color: 'silver'
				},
				labelStyle: {
					color: 'silver'
				}
			},

			navigator: {
				handles: {
					backgroundColor: '#666',
					borderColor: '#AAA'
				},
				outlineColor: '#CCC',
				maskFill: 'rgba(255,255,255,0.1)',
				series: {
					color: '#7798BF',
					lineColor: '#A6C7ED'
				},
				xAxis: {
					gridLineColor: '#505053'
				}
			},

			scrollbar: {
				barBackgroundColor: '#808083',
				barBorderColor: '#808083',
				buttonArrowColor: '#CCC',
				buttonBackgroundColor: '#606063',
				buttonBorderColor: '#606063',
				rifleColor: '#FFF',
				trackBackgroundColor: '#404043',
				trackBorderColor: '#404043'
			},

			legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
			background2: '#505053',
			dataLabelsColor: '#B0B0B3',
			textColor: '#C0C0C0',
			contrastTextColor: '#F0F0F3',
			maskColor: 'rgba(255,255,255,0.3)'
		};
		Highcharts.setOptions(Highcharts.theme);



</script>
@endsection