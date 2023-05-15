@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		/*text-align:center;*/
		vertical-align: middle;
		padding: 2px;
	}
	tbody>tr>td{
		/*text-align:center;*/
		vertical-align: middle;
		padding: 2px;
	}
	tfoot>tr>th{
		/*text-align:center;*/
		vertical-align: middle;
		padding: 2px;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(173, 173, 173);
		padding: 0;
		vertical-align: middle;
		padding: 2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(173, 173, 173);
		vertical-align: middle;
		padding: 2px;
	}
	#loading, #error { display: none; }
	.gambar {
	    width: 100%;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 0px;
	    margin-top: 10px;
	    display: inline-block;
	    border: 2px solid white;
	  }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 1vw;color: white"></div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-responsive" style="border:2px solid white;margin-bottom:0px" width="200px">';
						<tr style="background-color:#159925">';
							<th rowspan="2" style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;vertical-align: bottom;">DETAIL</th>
							<th rowspan="2" style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;vertical-align: bottom;">PRODUK</th>
							<th rowspan="2" style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;vertical-align: bottom;">MACHINE</th>
							<th colspan="2" style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw">MAN</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw">MATERIAL</th>
							<th rowspan="2" style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;vertical-align: bottom;">DRYER</th>
						</tr>
						<tr>
							<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;background-color: #cf9b0c">MP MOLDING</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;background-color: #cf9b0c">MP INJEKSI</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;background-color: #cf9b0c">LOT RESIN</th>
							<!-- <th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;background-color: #cf9b0c">MESIN</th> -->
							<!-- <th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:1.5vw;background-color: #cf9b0c">DRYER</th> -->
						</tr>
						<tr>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;">EXISTING</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="produk_existing">PRODUK</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="mesin_existing">MESIN</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="molding_existing">MP MOLDING</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="injeksi_existing">MP INJEKSI</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="resin_existing">LOT RESIN</th>
							<!-- <th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="mesin_existing">MESIN</th> -->
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="dryer_existing">DRYER</th>
						</tr>
						<tr>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;">BEST CONDITION</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="produk_best">PRODUK</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="mesin_best">MESIN</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="molding_best">MP MOLDING</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="injeksi_best">MP INJEKSI</th>
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="resin_best">LOT RESIN</th>
							<!-- <th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="mesin_best">MESIN</th> -->
							<th style="border:2px solid white;padding:0px;color:white;text-align:left;font-size:1.5vw;padding-left: 5px;vertical-align: middle;" id="dryer_best">DRYER</th>
						</tr>
					</table>
				</div>
				<div class="col-xs-12" style="margin-top: 20px">
					<div style="text-align: center;background-color: white;" class="col-xs-12">
						<span style="width: 100%;padding: 20px;font-weight: bold;font-size: 20px;">
							PARAMETER
						</span>
					</div>
					<table id="example1" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th rowspan="3">Cat</th>
								<th>NH</th>
								<th>H1</th>
								<th>H2</th>
								<th>H3</th>
								<th>Dryer</th>
								<th colspan="2">MTC</th>
								<th colspan="2">Chiller</th>
								<th>Clamp</th>
								<th>PH4</th>
								<th>PH3</th>
								<th>PH2</th>
								<th>PH1</th>
								<th>TRH3</th>
								<th>TRH2</th>
								<th>TRH1</th>
								<th>VH</th>
								<th>PI</th>
								<th>LS10 BB</th>
								<th>VI5</th>
								<th>VI4</th>
								<th>VI3</th>
								<th>VI2</th>
								<th>VI1</th>
								<th>LS4</th>
								<th>LS4D</th>
								<th>LS4C</th>
								<th>LS4B</th>
								<th>LS4A</th>
								<th>LS5</th>
								<th>VE1</th>
								<th>VE2</th>
								<th>VR</th>
								<th>LS31A</th>
								<th>LS31</th>
								<th>SRN</th>
								<th>RPM</th>
								<th>BP</th>
								<th>TR1 INJ</th>
								<th>TR3 COOL</th>
								<th>TR4 INT</th>
								<th>Min. Cush</th>
								<th>FILL</th>
								<th>Circle Time</th>
							</tr>
							<tr>
								<th colspan="4">Header</th>
								<th></th>
								<th colspan="2"></th>
								<th colspan="2"></th>
								<th></th>
								<th colspan="4">Pressure Hold</th>
								<th colspan="3">Pressure Hold Time</th>
								<th>Velocity PH</th>
								<th></th>
								<th></th>
								<th colspan="5">Velocity Injection</th>
								<th colspan="6">Length of Stroke</th>
								<th colspan="3"></th>
								<th colspan="2"></th>
								<th colspan="2">Screw</th>
								<th></th>
								<th colspan="3">Timer</th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
							<tr>
								<th colspan="4">°C</th>
								<th>°C</th>
								<th>°C</th>
								<th>MPa / bar</th>
								<th>°C</th>
								<th>MPa / bar</th>
								<th>kN</th>
								<th colspan="4">% / MPa</th>
								<th colspan="3">Sec</th>
								<th>mm/sec</th>
								<th>MPa</th>
								<th>mm</th>
								<th colspan="5">% / mm / sec</th>
								<th colspan="6">mm</th>
								<th colspan="3">mm / sec</th>
								<th colspan="2">mm</th>
								<th colspan="2">% / min<sup>-1</sup></th>
								<th>MPa</th>
								<th colspan="3">Sec</th>
								<th>mm</th>
								<th>Sec</th>
								<th>Sec</th>
							</tr>
						</thead>
						<tbody id="bodyParameter">
							<tr>
								<td style="padding:0;text-align:right"><b>EXIST</b></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_nh" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_h1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_h2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_h3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_dryer" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_mtc_temp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_mtc_press" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_chiller_temp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_chiller_press" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_clamp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph4" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_trh3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_trh2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_trh1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vh" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_pi" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls10" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi5" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi4" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4d" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4c" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4b" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4a" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls5" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ve1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ve2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vr" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls31a" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls31" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_srn" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_rpm" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_bp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_tr1inj" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_tr3cool" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_tr4int" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_mincush" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_fill" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_circletime" class="form-control"></td>
							</tr>

							<tr>
								<td style="padding:0;text-align:right;background-color: white"><b>BEST</b></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_nh" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_h1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_h2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_h3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_dryer" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_mtc_temp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_mtc_press" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_chiller_temp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_chiller_press" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_clamp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ph4" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ph3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ph2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ph1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_trh3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_trh2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_trh1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vh" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_pi" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls10" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vi5" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vi4" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vi3" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vi2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vi1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls4" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls4d" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls4c" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls4b" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls4a" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls5" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ve1" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ve2" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_vr" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls31a" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_ls31" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_srn" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_rpm" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_bp" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_tr1inj" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_tr3cool" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_tr4int" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_mincush" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_fill" class="form-control"></td>
								<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="best_circletime" class="form-control"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<!-- <center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center> -->
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Serial Number</th>
								<th style="width: 2%;">Model</th>
								<th style="width: 3%;">Loc</th>
								<th style="width: 3%;">NG Name</th>
								<th style="width: 3%;">Onko</th>
								<th style="width: 3%;">Qty</th>
								<th style="width: 3%;">Value Atas</th>
								<th style="width: 3%;">Value Bawan</th>
								<th style="width: 3%;">NG Loc</th>
								<th style="width: 3%;">Emp Kensa</th>
								<th style="width: 3%;">At</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th colspan="6">TOTAL</th>
								<th colspan="6" style="text-align: left;" id="total_all"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };

	jQuery(document).ready(function(){
		$('#date_from').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('#date_to').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		// setInterval(fetchChart, 600000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: null,
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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	var parameter_existing;
	var parameter_best;

	function fetchChart(){

		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/recorder/display/traceability") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
				$('#produk_existing').html(result.existing[0].part);
				$('#molding_existing').html(result.existing[0].person.replace(',','<br>'));
				$('#injeksi_existing').html(result.existing[0].person_injeksi.split('_')[0]);
				$('#resin_existing').html(result.existing[0].resin);
				$('#mesin_existing').html(result.existing[0].mesin);
				$('#dryer_existing').html(result.existing[0].dryer);

				$('#produk_best').html(result.best[0].part);
				$('#molding_best').html(result.best[0].person.replace(',','<br>'));
				$('#injeksi_best').html(result.best[0].person_injeksi.split('_')[0]);
				$('#resin_best').html(result.best[0].resin);
				$('#mesin_best').html(result.best[0].mesin);
				$('#dryer_best').html(result.best[0].dryer);

				parameter_existing = result.parameter_existing;
				parameter_best = result.parameter_best;

				parameter();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function parameter() {
		$('#edit_nh').val('');
		$('#edit_h1').val('');
		$('#edit_h2').val('');
		$('#edit_h3').val('');
		$('#edit_dryer').val('');
		$('#edit_mtc_temp').val('');
		$('#edit_mtc_press').val('');
		$('#edit_chiller_temp').val('');
		$('#edit_chiller_press').val('');
		$('#edit_clamp').val('');
		$('#edit_ph4').val('');
		$('#edit_ph3').val('');
		$('#edit_ph2').val('');
		$('#edit_ph1').val('');
		$('#edit_trh3').val('');
		$('#edit_trh2').val('');
		$('#edit_trh1').val('');
		$('#edit_vh').val('');
		$('#edit_pi').val('');
		$('#edit_ls10').val('');
		$('#edit_vi5').val('');
		$('#edit_vi4').val('');
		$('#edit_vi3').val('');
		$('#edit_vi2').val('');
		$('#edit_vi1').val('');
		$('#edit_ls4').val('');
		$('#edit_ls4d').val('');
		$('#edit_ls4c').val('');
		$('#edit_ls4b').val('');
		$('#edit_ls4a').val('');
		$('#edit_ls5').val('');
		$('#edit_ve1').val('');
		$('#edit_ve2').val('');
		$('#edit_vr').val('');
		$('#edit_ls31a').val('');
		$('#edit_ls31').val('');
		$('#edit_srn').val('');
		$('#edit_rpm').val('');
		$('#edit_bp').val('');
		$('#edit_tr1inj').val('');
		$('#edit_tr3cool').val('');
		$('#edit_tr4int').val('');
		$('#edit_mincush').val('');
		$('#edit_fill').val('');
		$('#edit_circletime').val('');

		$('#best_nh').val('');
		$('#best_h1').val('');
		$('#best_h2').val('');
		$('#best_h3').val('');
		$('#best_dryer').val('');
		$('#best_mtc_temp').val('');
		$('#best_mtc_press').val('');
		$('#best_chiller_temp').val('');
		$('#best_chiller_press').val('');
		$('#best_clamp').val('');
		$('#best_ph4').val('');
		$('#best_ph3').val('');
		$('#best_ph2').val('');
		$('#best_ph1').val('');
		$('#best_trh3').val('');
		$('#best_trh2').val('');
		$('#best_trh1').val('');
		$('#best_vh').val('');
		$('#best_pi').val('');
		$('#best_ls10').val('');
		$('#best_vi5').val('');
		$('#best_vi4').val('');
		$('#best_vi3').val('');
		$('#best_vi2').val('');
		$('#best_vi1').val('');
		$('#best_ls4').val('');
		$('#best_ls4d').val('');
		$('#best_ls4c').val('');
		$('#best_ls4b').val('');
		$('#best_ls4a').val('');
		$('#best_ls5').val('');
		$('#best_ve1').val('');
		$('#best_ve2').val('');
		$('#best_vr').val('');
		$('#best_ls31a').val('');
		$('#best_ls31').val('');
		$('#best_srn').val('');
		$('#best_rpm').val('');
		$('#best_bp').val('');
		$('#best_tr1inj').val('');
		$('#best_tr3cool').val('');
		$('#best_tr4int').val('');
		$('#best_mincush').val('');
		$('#best_fill').val('');
		$('#best_circletime').val('');

		if (parameter_existing.length > 0) {
			if (parameter_existing[0].nh != parameter_best[0].nh) {
				var color_nh = 'red';
			}else{
				var color_nh = 'transparent';
			}
			if(parameter_existing[0].h1 != parameter_best[0].h1){
				var color_h1 = 'red';
			}else{
				var color_h1 = 'transparent';
			}
			if(parameter_existing[0].h2 != parameter_best[0].h2){
				var color_h2 = 'red';
			}else{
				var color_h2 = 'transparent';
			}
			if(parameter_existing[0].h3 != parameter_best[0].h3){
				var color_h3 = 'red';
			}else{
				var color_h3 = 'transparent';
			}
			if(parameter_existing[0].dryer != parameter_best[0].dryer){
				var color_dryer = 'red';
			}else{
				var color_dryer = 'transparent';
			}
			if(parameter_existing[0].mtc_temp != parameter_best[0].mtc_temp){
				var color_mtc_temp = 'red';
			}else{
				var color_mtc_temp = 'transparent';
			}
			if(parameter_existing[0].mtc_press != parameter_best[0].mtc_press){
				var color_mtc_press = 'red';
			}else{
				var color_mtc_press = 'transparent';
			}
			if(parameter_existing[0].chiller_temp != parameter_best[0].chiller_temp){
				var color_chiller_temp = 'red';
			}else{
				var color_chiller_temp = 'transparent';
			}
			if(parameter_existing[0].chiller_press != parameter_best[0].chiller_press){
				var color_chiller_press = 'red';
			}else{
				var color_chiller_press = 'transparent';
			}
			if(parameter_existing[0].clamp != parameter_best[0].clamp){
				var color_clamp = 'red';
			}else{
				var color_clamp = 'transparent';
			}
			if(parameter_existing[0].ph4 != parameter_best[0].ph4){
				var color_ph4 = 'red';
			}else{
				var color_ph4 = 'transparent';
			}
			if(parameter_existing[0].ph3 != parameter_best[0].ph3){
				var color_ph3 = 'red';
			}else{
				var color_ph3 = 'transparent';
			}
			if(parameter_existing[0].ph2 != parameter_best[0].ph2){
				var color_ph2 = 'red';
			}else{
				var color_ph2 = 'transparent';
			}
			if(parameter_existing[0].ph1 != parameter_best[0].ph1){
				var color_ph1 = 'red';
			}else{
				var color_ph1 = 'transparent';
			}
			if(parameter_existing[0].trh3 != parameter_best[0].trh3){
				var color_trh3 = 'red';
			}else{
				var color_trh3 = 'transparent';
			}
			if(parameter_existing[0].trh2 != parameter_best[0].trh2){
				var color_trh2 = 'red';
			}else{
				var color_trh2 = 'transparent';
			}
			if(parameter_existing[0].trh1 != parameter_best[0].trh1){
				var color_trh1 = 'red';
			}else{
				var color_trh1 = 'transparent';
			}
			if(parameter_existing[0].vh != parameter_best[0].vh){
				var color_vh = 'red';
			}else{
				var color_vh = 'transparent';
			}
			if(parameter_existing[0].pi != parameter_best[0].pi){
				var color_pi = 'red';
			}else{
				var color_pi = 'transparent';
			}
			if(parameter_existing[0].ls10 != parameter_best[0].ls10){
				var color_ls10 = 'red';
			}else{
				var color_ls10 = 'transparent';
			}
			if(parameter_existing[0].vi5 != parameter_best[0].vi5){
				var color_vi5 = 'red';
			}else{
				var color_vi5 = 'transparent';
			}
			if(parameter_existing[0].vi4 != parameter_best[0].vi4){
				var color_vi4 = 'red';
			}else{
				var color_vi4 = 'transparent';
			}
			if(parameter_existing[0].vi3 != parameter_best[0].vi3){
				var color_vi3 = 'red';
			}else{
				var color_vi3 = 'transparent';
			}
			if(parameter_existing[0].vi2 != parameter_best[0].vi2){
				var color_vi2 = 'red';
			}else{
				var color_vi2 = 'transparent';
			}
			if(parameter_existing[0].vi1 != parameter_best[0].vi1){
				var color_vi1 = 'red';
			}else{
				var color_vi1 = 'transparent';
			}
			if(parameter_existing[0].ls4 != parameter_best[0].ls4){
				var color_ls4 = 'red';
			}else{
				var color_ls4 = 'transparent';
			}
			if(parameter_existing[0].ls4d != parameter_best[0].ls4d){
				var color_ls4d = 'red';
			}else{
				var color_ls4d = 'transparent';
			}
			if(parameter_existing[0].ls4c != parameter_best[0].ls4c){
				var color_ls4c = 'red';
			}else{
				var color_ls4c = 'transparent';
			}
			if(parameter_existing[0].ls4b != parameter_best[0].ls4b){
				var color_ls4b = 'red';
			}else{
				var color_ls4b = 'transparent';
			}
			if(parameter_existing[0].ls4a != parameter_best[0].ls4a){
				var color_ls4a = 'red';
			}else{
				var color_ls4a = 'transparent';
			}
			if(parameter_existing[0].ls5 != parameter_best[0].ls5){
				var color_ls5 = 'red';
			}else{
				var color_ls5 = 'transparent';
			}
			if(parameter_existing[0].ve1 != parameter_best[0].ve1){
				var color_ve1 = 'red';
			}else{
				var color_ve1 = 'transparent';
			}
			if(parameter_existing[0].ve2 != parameter_best[0].ve2){
				var color_ve2 = 'red';
			}else{
				var color_ve2 = 'transparent';
			}
			if(parameter_existing[0].vr != parameter_best[0].vr){
				var color_vr = 'red';
			}else{
				var color_vr = 'transparent';
			}
			if(parameter_existing[0].ls31a != parameter_best[0].ls31a){
				var color_ls31a = 'red';
			}else{
				var color_ls31a = 'transparent';
			}
			if(parameter_existing[0].ls31 != parameter_best[0].ls31){
				var color_ls31 = 'red';
			}else{
				var color_ls31 = 'transparent';
			}
			if(parameter_existing[0].srn != parameter_best[0].srn){
				var color_srn = 'red';
			}else{
				var color_srn = 'transparent';
			}
			if(parameter_existing[0].rpm != parameter_best[0].rpm){
				var color_rpm = 'red';
			}else{
				var color_rpm = 'transparent';
			}
			if(parameter_existing[0].bp != parameter_best[0].bp){
				var color_bp = 'red';
			}else{
				var color_bp = 'transparent';
			}
			if(parameter_existing[0].tr1inj != parameter_best[0].tr1inj){
				var color_tr1inj = 'red';
			}else{
				var color_tr1inj = 'transparent';
			}
			if(parameter_existing[0].tr3cool != parameter_best[0].tr3cool){
				var color_tr3cool = 'red';
			}else{
				var color_tr3cool = 'transparent';
			}
			if(parameter_existing[0].tr4int != parameter_best[0].tr4int){
				var color_tr4int = 'red';
			}else{
				var color_tr4int = 'transparent';
			}
			if(parameter_existing[0].mincush != parameter_best[0].mincush){
				var color_mincush = 'red';
			}else{
				var color_mincush = 'transparent';
			}
			if(parameter_existing[0].fill != parameter_best[0].fill){
				var color_fill = 'red';
			}else{
				var color_fill = 'transparent';
			}
			if(parameter_existing[0].circletime != parameter_best[0].circletime){
				var color_circletime = 'red';
			}else{
				var color_circletime = 'transparent';
			}
			$('#edit_nh').val(parameter_existing[0].nh);
			$('#edit_h1').val(parameter_existing[0].h1);
			$('#edit_h2').val(parameter_existing[0].h2);
			$('#edit_h3').val(parameter_existing[0].h3);
			$('#edit_dryer').val(parameter_existing[0].dryer);
			$('#edit_mtc_temp').val(parameter_existing[0].mtc_temp);
			$('#edit_mtc_press').val(parameter_existing[0].mtc_press);
			$('#edit_chiller_temp').val(parameter_existing[0].chiller_temp);
			$('#edit_chiller_press').val(parameter_existing[0].chiller_press);
			$('#edit_clamp').val(parameter_existing[0].clamp);
			$('#edit_ph4').val(parameter_existing[0].ph4);
			$('#edit_ph3').val(parameter_existing[0].ph3);
			$('#edit_ph2').val(parameter_existing[0].ph2);
			$('#edit_ph1').val(parameter_existing[0].ph1);
			$('#edit_trh3').val(parameter_existing[0].trh3);
			$('#edit_trh2').val(parameter_existing[0].trh2);
			$('#edit_trh1').val(parameter_existing[0].trh1);
			$('#edit_vh').val(parameter_existing[0].vh);
			$('#edit_pi').val(parameter_existing[0].pi);
			$('#edit_ls10').val(parameter_existing[0].ls10);
			$('#edit_vi5').val(parameter_existing[0].vi5);
			$('#edit_vi4').val(parameter_existing[0].vi4);
			$('#edit_vi3').val(parameter_existing[0].vi3);
			$('#edit_vi2').val(parameter_existing[0].vi2);
			$('#edit_vi1').val(parameter_existing[0].vi1);
			$('#edit_ls4').val(parameter_existing[0].ls4);
			$('#edit_ls4d').val(parameter_existing[0].ls4d);
			$('#edit_ls4c').val(parameter_existing[0].ls4c);
			$('#edit_ls4b').val(parameter_existing[0].ls4b);
			$('#edit_ls4a').val(parameter_existing[0].ls4a);
			$('#edit_ls5').val(parameter_existing[0].ls5);
			$('#edit_ve1').val(parameter_existing[0].ve1);
			$('#edit_ve2').val(parameter_existing[0].ve2);
			$('#edit_vr').val(parameter_existing[0].vr);
			$('#edit_ls31a').val(parameter_existing[0].ls31a);
			$('#edit_ls31').val(parameter_existing[0].ls31);
			$('#edit_srn').val(parameter_existing[0].srn);
			$('#edit_rpm').val(parameter_existing[0].rpm);
			$('#edit_bp').val(parameter_existing[0].bp);
			$('#edit_tr1inj').val(parameter_existing[0].tr1inj);
			$('#edit_tr3cool').val(parameter_existing[0].tr3cool);
			$('#edit_tr4int').val(parameter_existing[0].tr4int);
			$('#edit_mincush').val(parameter_existing[0].mincush);
			$('#edit_fill').val(parameter_existing[0].fill);
			$('#edit_circletime').val(parameter_existing[0].circletime);

			document.getElementById('edit_nh').style.backgroundColor = color_nh;
			document.getElementById('edit_h1').style.backgroundColor = color_h1;
			document.getElementById('edit_h2').style.backgroundColor = color_h2;
			document.getElementById('edit_h3').style.backgroundColor = color_h3;
			document.getElementById('edit_dryer').style.backgroundColor = color_dryer;
			document.getElementById('edit_mtc_temp').style.backgroundColor = color_mtc_temp;
			document.getElementById('edit_mtc_press').style.backgroundColor = color_mtc_press;
			document.getElementById('edit_chiller_temp').style.backgroundColor = color_chiller_temp;
			document.getElementById('edit_chiller_press').style.backgroundColor = color_chiller_press;
			document.getElementById('edit_clamp').style.backgroundColor = color_clamp;
			document.getElementById('edit_ph4').style.backgroundColor = color_ph4;
			document.getElementById('edit_ph3').style.backgroundColor = color_ph3;
			document.getElementById('edit_ph2').style.backgroundColor = color_ph2;
			document.getElementById('edit_ph1').style.backgroundColor = color_ph1;
			document.getElementById('edit_trh3').style.backgroundColor = color_trh3;
			document.getElementById('edit_trh2').style.backgroundColor = color_trh2;
			document.getElementById('edit_trh1').style.backgroundColor = color_trh1;
			document.getElementById('edit_vh').style.backgroundColor = color_vh;
			document.getElementById('edit_pi').style.backgroundColor = color_pi;
			document.getElementById('edit_ls10').style.backgroundColor = color_ls10;
			document.getElementById('edit_vi5').style.backgroundColor = color_vi5;
			document.getElementById('edit_vi4').style.backgroundColor = color_vi4;
			document.getElementById('edit_vi3').style.backgroundColor = color_vi3;
			document.getElementById('edit_vi2').style.backgroundColor = color_vi2;
			document.getElementById('edit_vi1').style.backgroundColor = color_vi1;
			document.getElementById('edit_ls4').style.backgroundColor = color_ls4;
			document.getElementById('edit_ls4d').style.backgroundColor = color_ls4d;
			document.getElementById('edit_ls4c').style.backgroundColor = color_ls4c;
			document.getElementById('edit_ls4b').style.backgroundColor = color_ls4b;
			document.getElementById('edit_ls4a').style.backgroundColor = color_ls4a;
			document.getElementById('edit_ls5').style.backgroundColor = color_ls5;
			document.getElementById('edit_ve1').style.backgroundColor = color_ve1;
			document.getElementById('edit_ve2').style.backgroundColor = color_ve2;
			document.getElementById('edit_vr').style.backgroundColor = color_vr;
			document.getElementById('edit_ls31a').style.backgroundColor = color_ls31a;
			document.getElementById('edit_ls31').style.backgroundColor = color_ls31;
			document.getElementById('edit_srn').style.backgroundColor = color_srn;
			document.getElementById('edit_rpm').style.backgroundColor = color_rpm;
			document.getElementById('edit_bp').style.backgroundColor = color_bp;
			document.getElementById('edit_tr1inj').style.backgroundColor = color_tr1inj;
			document.getElementById('edit_tr3cool').style.backgroundColor = color_tr3cool;
			document.getElementById('edit_tr4int').style.backgroundColor = color_tr4int;
			document.getElementById('edit_mincush').style.backgroundColor = color_mincush;
			document.getElementById('edit_fill').style.backgroundColor = color_fill;
			document.getElementById('edit_circletime').style.backgroundColor = color_circletime;
		}else{
			$('#edit_nh').val('');
			$('#edit_h1').val('');
			$('#edit_h2').val('');
			$('#edit_h3').val('');
			$('#edit_dryer').val('');
			$('#edit_mtc_temp').val('');
			$('#edit_mtc_press').val('');
			$('#edit_chiller_temp').val('');
			$('#edit_chiller_press').val('');
			$('#edit_clamp').val('');
			$('#edit_ph4').val('');
			$('#edit_ph3').val('');
			$('#edit_ph2').val('');
			$('#edit_ph1').val('');
			$('#edit_trh3').val('');
			$('#edit_trh2').val('');
			$('#edit_trh1').val('');
			$('#edit_vh').val('');
			$('#edit_pi').val('');
			$('#edit_ls10').val('');
			$('#edit_vi5').val('');
			$('#edit_vi4').val('');
			$('#edit_vi3').val('');
			$('#edit_vi2').val('');
			$('#edit_vi1').val('');
			$('#edit_ls4').val('');
			$('#edit_ls4d').val('');
			$('#edit_ls4c').val('');
			$('#edit_ls4b').val('');
			$('#edit_ls4a').val('');
			$('#edit_ls5').val('');
			$('#edit_ve1').val('');
			$('#edit_ve2').val('');
			$('#edit_vr').val('');
			$('#edit_ls31a').val('');
			$('#edit_ls31').val('');
			$('#edit_srn').val('');
			$('#edit_rpm').val('');
			$('#edit_bp').val('');
			$('#edit_tr1inj').val('');
			$('#edit_tr3cool').val('');
			$('#edit_tr4int').val('');
			$('#edit_mincush').val('');
			$('#edit_fill').val('');
			$('#edit_circletime').val('');
		}
		if (parameter_best.length > 0) {
			$('#best_nh').val(parameter_best[0].nh);
			$('#best_h1').val(parameter_best[0].h1);
			$('#best_h2').val(parameter_best[0].h2);
			$('#best_h3').val(parameter_best[0].h3);
			$('#best_dryer').val(parameter_best[0].dryer);
			$('#best_mtc_temp').val(parameter_best[0].mtc_temp);
			$('#best_mtc_press').val(parameter_best[0].mtc_press);
			$('#best_chiller_temp').val(parameter_best[0].chiller_temp);
			$('#best_chiller_press').val(parameter_best[0].chiller_press);
			$('#best_clamp').val(parameter_best[0].clamp);
			$('#best_ph4').val(parameter_best[0].ph4);
			$('#best_ph3').val(parameter_best[0].ph3);
			$('#best_ph2').val(parameter_best[0].ph2);
			$('#best_ph1').val(parameter_best[0].ph1);
			$('#best_trh3').val(parameter_best[0].trh3);
			$('#best_trh2').val(parameter_best[0].trh2);
			$('#best_trh1').val(parameter_best[0].trh1);
			$('#best_vh').val(parameter_best[0].vh);
			$('#best_pi').val(parameter_best[0].pi);
			$('#best_ls10').val(parameter_best[0].ls10);
			$('#best_vi5').val(parameter_best[0].vi5);
			$('#best_vi4').val(parameter_best[0].vi4);
			$('#best_vi3').val(parameter_best[0].vi3);
			$('#best_vi2').val(parameter_best[0].vi2);
			$('#best_vi1').val(parameter_best[0].vi1);
			$('#best_ls4').val(parameter_best[0].ls4);
			$('#best_ls4d').val(parameter_best[0].ls4d);
			$('#best_ls4c').val(parameter_best[0].ls4c);
			$('#best_ls4b').val(parameter_best[0].ls4b);
			$('#best_ls4a').val(parameter_best[0].ls4a);
			$('#best_ls5').val(parameter_best[0].ls5);
			$('#best_ve1').val(parameter_best[0].ve1);
			$('#best_ve2').val(parameter_best[0].ve2);
			$('#best_vr').val(parameter_best[0].vr);
			$('#best_ls31a').val(parameter_best[0].ls31a);
			$('#best_ls31').val(parameter_best[0].ls31);
			$('#best_srn').val(parameter_best[0].srn);
			$('#best_rpm').val(parameter_best[0].rpm);
			$('#best_bp').val(parameter_best[0].bp);
			$('#best_tr1inj').val(parameter_best[0].tr1inj);
			$('#best_tr3cool').val(parameter_best[0].tr3cool);
			$('#best_tr4int').val(parameter_best[0].tr4int);
			$('#best_mincush').val(parameter_best[0].mincush);
			$('#best_fill').val(parameter_best[0].fill);
			$('#best_circletime').val(parameter_best[0].circletime);
		}else{
			$('#best_nh').val('');
			$('#best_h1').val('');
			$('#best_h2').val('');
			$('#best_h3').val('');
			$('#best_dryer').val('');
			$('#best_mtc_temp').val('');
			$('#best_mtc_press').val('');
			$('#best_chiller_temp').val('');
			$('#best_chiller_press').val('');
			$('#best_clamp').val('');
			$('#best_ph4').val('');
			$('#best_ph3').val('');
			$('#best_ph2').val('');
			$('#best_ph1').val('');
			$('#best_trh3').val('');
			$('#best_trh2').val('');
			$('#best_trh1').val('');
			$('#best_vh').val('');
			$('#best_pi').val('');
			$('#best_ls10').val('');
			$('#best_vi5').val('');
			$('#best_vi4').val('');
			$('#best_vi3').val('');
			$('#best_vi2').val('');
			$('#best_vi1').val('');
			$('#best_ls4').val('');
			$('#best_ls4d').val('');
			$('#best_ls4c').val('');
			$('#best_ls4b').val('');
			$('#best_ls4a').val('');
			$('#best_ls5').val('');
			$('#best_ve1').val('');
			$('#best_ve2').val('');
			$('#best_vr').val('');
			$('#best_ls31a').val('');
			$('#best_ls31').val('');
			$('#best_srn').val('');
			$('#best_rpm').val('');
			$('#best_bp').val('');
			$('#best_tr1inj').val('');
			$('#best_tr3cool').val('');
			$('#best_tr4int').val('');
			$('#best_mincush').val('');
			$('#best_fill').val('');
			$('#best_circletime').val('');
		}
	}


	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};

	function changeLocation(){
		$("#location").val($("#locationSelect").val());
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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