@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
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

					<div class="col-xs-2" style="padding-right: 0;">
						<select style="width: 100%" class="form-control select2" data-placeholder="Pilih Part" id="part">
							<option value=""></option>
							<option value="HEAD">HEAD</option>
							<option value="MIDDLE">MIDDLE</option>
							<option value="FOOT">FOOT</option>
							<option value="BLOCK">BLOCK</option>
						</select>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 1vw;color: white"></div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row" id="ALL">
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div style="width: 100%;background-color: white;text-align: center;">
						<span style="font-weight: bold;padding: 2px;font-size: 2vw">TODAY</span>
					</div> -->
					<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <small><span style="color: white">不良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ng">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TARGET % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_target">0.04<sup style="font-size: 30px"> %</sup></h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-area-chart"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container1" class="container1" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;" id="HEAD_1">
				<div class="col-xs-12" style="padding-right: 0;background-color: white;text-align: center">
					<span style="font-size: 20px;padding: 20px;font-weight: bold;">NG RATE HEAD</span>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;" id="HEAD_2">
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div style="width: 100%;background-color: white;text-align: center;">
						<span style="font-weight: bold;padding: 2px;font-size: 2vw">TODAY</span>
					</div> -->
					<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="total_head">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok_head">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <small><span style="color: white">不良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ng_head">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TARGET % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_target_head">0.04<sup style="font-size: 30px"> %</sup></h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-area-chart"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_head">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container_head" class="container" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;" id="MIDDLE_1">
				<div class="col-xs-12" style="padding-right: 0;background-color: white;text-align: center">
					<span style="font-size: 20px;padding: 20px;font-weight: bold;">NG RATE MIDDLE</span>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;"  id="MIDDLE_2">
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div style="width: 100%;background-color: white;text-align: center;">
						<span style="font-weight: bold;padding: 2px;font-size: 2vw">TODAY</span>
					</div> -->
					<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="total_middle">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok_middle">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <small><span style="color: white">不良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ng_middle">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TARGET % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_target_middle">0.04<sup style="font-size: 30px"> %</sup></h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-area-chart"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_middle">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container_middle" class="container" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;" id="FOOT_1">
				<div class="col-xs-12" style="padding-right: 0;background-color: white;text-align: center">
					<span style="font-size: 20px;padding: 20px;font-weight: bold;">NG RATE FOOT</span>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;" id="FOOT_2">
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div style="width: 100%;background-color: white;text-align: center;">
						<span style="font-weight: bold;padding: 2px;font-size: 2vw">TODAY</span>
					</div> -->
					<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="total_foot">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok_foot">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <small><span style="color: white">不良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ng_foot">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TARGET % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_target_foot">0.04<sup style="font-size: 30px"> %</sup></h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-area-chart"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_foot">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container_foot" class="container" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;" id="BLOCK_1">
				<div class="col-xs-12" style="padding-right: 0;background-color: white;text-align: center">
					<span style="font-size: 20px;padding: 20px;font-weight: bold;">NG RATE BLOCK</span>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;" id="BLOCK_2">
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div style="width: 100%;background-color: white;text-align: center;">
						<span style="font-weight: bold;padding: 2px;font-size: 2vw">TODAY</span>
					</div> -->
					<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="total_block">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok_block">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <small><span style="color: white">不良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ng_block">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TARGET % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_target_block">0.04<sup style="font-size: 30px"> %</sup></h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-area-chart"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_block">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container_block" class="container" style="width: 100%;"></div>
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

	jQuery(document).ready(function(){
		$('#date_from').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
		});
		$('#date_to').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart, 300000);
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

	function showAll() {
		$("#ALL").show();
		$("#HEAD_1").show();
		$("#HEAD_2").show();
		$("#MIDDLE_1").show();
		$("#MIDDLE_2").show();
		$("#FOOT_1").show();
		$("#FOOT_2").show();
		$("#BLOCK_1").show();
		$("#BLOCK_2").show();
	}

	function hideAll() {
		$("#ALL").hide();
		$("#HEAD_1").hide();
		$("#HEAD_2").hide();
		$("#MIDDLE_1").hide();
		$("#MIDDLE_2").hide();
		$("#FOOT_1").hide();
		$("#FOOT_2").hide();
		$("#BLOCK_1").hide();
		$("#BLOCK_2").hide();
	}

	function fetchChart(){

		$('#loading').show();

		if ($('#part').val() == '') {
			showAll();
		}else{
			hideAll();
			$('#'+$('#part').val()+'_1').show();
			$('#'+$('#part').val()+'_2').show();
		}

		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/recorder/display/ng_rate") }}', data, function(result, status, xhr) {
			if(result.status){

				var total = 0;
				var title = result.title;
				var title = $('#origin').val();
				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

				var total_ng = 0;
				var total_box = 0;

				for(var i = 0; i < result.ng_rate.length; i++){
					total_ng = total_ng + parseInt(result.ng_rate[i][0].qty_ng);
					total_box = total_box + (parseInt(result.ng_rate[i][0].qty_box)*4);
				}

				$('#total').append().empty();
				$('#total').html(total_box+total_ng+ '');

				$('#ok').append().empty();
				$('#ok').html(total_box + '');

				$('#ng').append().empty();
				$('#ng').html(total_ng + '');

				$('#pctg').append().empty();
				$('#pctg').html(((total_ng/(total_ng+total_box))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				var total_ng_target = 0;
				var total_box_target = 0;

				for(var i = 0; i < result.ng_target.length; i++){
					total_ng_target = total_ng_target + parseInt(result.ng_target[i].qty_ng);
					total_box_target = total_box_target + (parseInt(result.ng_target[i].qty_box)*4);
				}

				$('#pctg_target').append().empty();
				$('#pctg_target').html(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				var categories = [];

				var qty_ng = [], qty_box = [], ng_rate = [],ng_rate_target = [];

				for(var i = 0; i < result.ng_rate.length; i++){
					categories.push(result.ng_rate[i][0].week_date);

					qty_ng.push(parseInt(result.ng_rate[i][0].qty_ng));
					qty_box.push(parseInt(result.ng_rate[i][0].qty_box)*4);
					var total_ngs = parseInt(result.ng_rate[i][0].qty_ng);
					var total_oks = parseInt(result.ng_rate[i][0].qty_ng)+(parseInt(result.ng_rate[i][0].qty_box)*4);
					var percents = (total_ngs/total_oks)*100;
					ng_rate.push(parseFloat(percents.toFixed(3)));
					ng_rate_target.push(parseFloat(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3)));
				};


				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						height: '600',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						
					}
					, { // Secondary yAxis
						title: {
							text: 'Total Check Pc(s)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						opposite: true

					}, { // Secondary yAxis
						title: {
							text: 'NG Rate (%)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						max: 5,
						type: 'linear',
						opposite: true

					}
					],
					tooltip: {
						headerFormat: '<span>Detail</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,result.date,'ng_name');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					
					{
						type: 'column',
						data: qty_ng,
						name: 'Total NG',
						colorByPoint: false,
						color: '#d62d2d',
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: qty_box,
						name: 'Qty Check',
						colorByPoint: false,
						color: '#2064bd',
						yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: ng_rate,
						name: 'NG Rate',
						colorByPoint: false,
						color:'#fff',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						
					},
					{
						type: 'spline',
						data: ng_rate_target,
						name: 'Target NG Rate',
						colorByPoint: false,
						color:'#fcba03',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						dashStyle:'shortdash',
						lineWidth: 4,
						marker: {
			                radius: 4,
			                lineColor: '#fcba03',
			                lineWidth: 1
			            },
						
					},
					]
				});

				var total_ng = 0;
				var total_box = 0;

				for(var i = 0; i < result.ng_rate_head.length; i++){
					total_ng = total_ng + parseInt(result.ng_rate_head[i][0].qty_ng);
					total_box = total_box + (parseInt(result.ng_rate_head[i][0].qty_box)*4);
				}

				$('#total_head').append().empty();
				$('#total_head').html(total_box+total_ng+ '');

				$('#ok_head').append().empty();
				$('#ok_head').html(total_box + '');

				$('#ng_head').append().empty();
				$('#ng_head').html(total_ng + '');

				$('#pctg_head').append().empty();
				$('#pctg_head').html(((total_ng/(total_ng+total_box))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				var total_ng_target = 0;
				var total_box_target = 0;

				for(var i = 0; i < result.ng_target_head.length; i++){
					total_ng_target = total_ng_target + parseInt(result.ng_target_head[i].qty_ng);
					total_box_target = total_box_target + (parseInt(result.ng_target_head[i].qty_box)*4);
				}

				$('#pctg_target_head').append().empty();
				$('#pctg_target_head').html(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');


				var categories_head = [];

				var qty_ng_head = [], qty_box_head = [], ng_rate_head = [],ng_rate_target_head = [];

				for(var i = 0; i < result.ng_rate_head.length; i++){
					categories_head.push(result.ng_rate_head[i][0].week_date);

					qty_ng_head.push(parseInt(result.ng_rate_head[i][0].qty_ng));
					qty_box_head.push(parseInt(result.ng_rate_head[i][0].qty_box)*4);
					var total_ngs = parseInt(result.ng_rate_head[i][0].qty_ng);
					var total_oks = parseInt(result.ng_rate_head[i][0].qty_ng)+(parseInt(result.ng_rate_head[i][0].qty_box)*4);
					var percents = (total_ngs/total_oks)*100;
					ng_rate_head.push(parseFloat(percents.toFixed(3)));
					ng_rate_target_head.push(parseFloat(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3)));
				};


				Highcharts.chart('container_head', {
					chart: {
						type: 'column',
						height: '600',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories_head,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						
					}
					, { // Secondary yAxis
						title: {
							text: 'Total Check Pc(s)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						opposite: true

					}, { // Secondary yAxis
						title: {
							text: 'NG Rate (%)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						max: 5,
						type: 'linear',
						opposite: true

					}
					],
					tooltip: {
						headerFormat: '<span>Detail</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,result.date,'ng_name');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					
					{
						type: 'column',
						data: qty_ng_head,
						name: 'Total NG',
						colorByPoint: false,
						color: '#d62d2d',
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: qty_box_head,
						name: 'Qty Check',
						colorByPoint: false,
						color: '#2064bd',
						yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: ng_rate_head,
						name: 'NG Rate',
						colorByPoint: false,
						color:'#fff',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						
					},
					{
						type: 'spline',
						data: ng_rate_target_head,
						name: 'Target NG Rate',
						colorByPoint: false,
						color:'#fcba03',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						dashStyle:'shortdash',
						lineWidth: 4,
						marker: {
			                radius: 4,
			                lineColor: '#fcba03',
			                lineWidth: 1
			            },
						
					},
					]
				});

				var total_ng = 0;
				var total_box = 0;

				for(var i = 0; i < result.ng_rate_middle.length; i++){
					total_ng = total_ng + parseInt(result.ng_rate_middle[i][0].qty_ng);
					total_box = total_box + (parseInt(result.ng_rate_middle[i][0].qty_box)*4);
				}

				$('#total_middle').append().empty();
				$('#total_middle').html(total_box+total_ng+ '');

				$('#ok_middle').append().empty();
				$('#ok_middle').html(total_box + '');

				$('#ng_middle').append().empty();
				$('#ng_middle').html(total_ng + '');

				$('#pctg_middle').append().empty();
				$('#pctg_middle').html(((total_ng/(total_ng+total_box))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				var total_ng_target = 0;
				var total_box_target = 0;

				for(var i = 0; i < result.ng_target_middle.length; i++){
					total_ng_target = total_ng_target + parseInt(result.ng_target_middle[i].qty_ng);
					total_box_target = total_box_target + (parseInt(result.ng_target_middle[i].qty_box)*4);
				}

				$('#pctg_target_middle').append().empty();
				$('#pctg_target_middle').html(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');


				var categories_middle = [];

				var qty_ng_middle = [], qty_box_middle = [], ng_rate_middle = [],ng_rate_target_middle = [];

				for(var i = 0; i < result.ng_rate_middle.length; i++){
					categories_middle.push(result.ng_rate_middle[i][0].week_date);

					qty_ng_middle.push(parseInt(result.ng_rate_middle[i][0].qty_ng));
					qty_box_middle.push(parseInt(result.ng_rate_middle[i][0].qty_box)*4);
					var total_ngs = parseInt(result.ng_rate_middle[i][0].qty_ng);
					var total_oks = parseInt(result.ng_rate_middle[i][0].qty_ng)+(parseInt(result.ng_rate_middle[i][0].qty_box)*4);
					var percents = (total_ngs/total_oks)*100;
					ng_rate_middle.push(parseFloat(percents.toFixed(3)));
					ng_rate_target_middle.push(parseFloat(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3)));
				};


				Highcharts.chart('container_middle', {
					chart: {
						type: 'column',
						height: '600',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories_middle,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						
					}
					, { // Secondary yAxis
						title: {
							text: 'Total Check Pc(s)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						opposite: true

					}, { // Secondary yAxis
						title: {
							text: 'NG Rate (%)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						max: 5,
						type: 'linear',
						opposite: true

					}
					],
					tooltip: {
						middleerFormat: '<span>Detail</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,result.date,'ng_name');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					
					{
						type: 'column',
						data: qty_ng_middle,
						name: 'Total NG',
						colorByPoint: false,
						color: '#d62d2d',
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: qty_box_middle,
						name: 'Qty Check',
						colorByPoint: false,
						color: '#2064bd',
						yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: ng_rate_middle,
						name: 'NG Rate',
						colorByPoint: false,
						color:'#fff',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						
					},
					{
						type: 'spline',
						data: ng_rate_target_middle,
						name: 'Target NG Rate',
						colorByPoint: false,
						color:'#fcba03',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						dashStyle:'shortdash',
						lineWidth: 4,
						marker: {
			                radius: 4,
			                lineColor: '#fcba03',
			                lineWidth: 1
			            },
						
					},
					]
				});

				var total_ng = 0;
				var total_box = 0;

				for(var i = 0; i < result.ng_rate_foot.length; i++){
					total_ng = total_ng + parseInt(result.ng_rate_foot[i][0].qty_ng);
					total_box = total_box + (parseInt(result.ng_rate_foot[i][0].qty_box)*4);
				}

				$('#total_foot').append().empty();
				$('#total_foot').html(total_box+total_ng+ '');

				$('#ok_foot').append().empty();
				$('#ok_foot').html(total_box + '');

				$('#ng_foot').append().empty();
				$('#ng_foot').html(total_ng + '');

				$('#pctg_foot').append().empty();
				$('#pctg_foot').html(((total_ng/(total_ng+total_box))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				var total_ng_target = 0;
				var total_box_target = 0;

				for(var i = 0; i < result.ng_target_foot.length; i++){
					total_ng_target = total_ng_target + parseInt(result.ng_target_foot[i].qty_ng);
					total_box_target = total_box_target + (parseInt(result.ng_target_foot[i].qty_box)*4);
				}

				$('#pctg_target_foot').append().empty();
				$('#pctg_target_foot').html(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');


				var categories_foot = [];

				var qty_ng_foot = [], qty_box_foot = [], ng_rate_foot = [],ng_rate_target_foot = [];

				for(var i = 0; i < result.ng_rate_foot.length; i++){
					categories_foot.push(result.ng_rate_foot[i][0].week_date);

					qty_ng_foot.push(parseInt(result.ng_rate_foot[i][0].qty_ng));
					qty_box_foot.push(parseInt(result.ng_rate_foot[i][0].qty_box)*4);
					var total_ngs = parseInt(result.ng_rate_foot[i][0].qty_ng);
					var total_oks = parseInt(result.ng_rate_foot[i][0].qty_ng)+(parseInt(result.ng_rate_foot[i][0].qty_box)*4);
					var percents = (total_ngs/total_oks)*100;
					ng_rate_foot.push(parseFloat(percents.toFixed(3)));
					ng_rate_target_foot.push(parseFloat(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3)));
				};


				Highcharts.chart('container_foot', {
					chart: {
						type: 'column',
						height: '600',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories_foot,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						
					}
					, { // Secondary yAxis
						title: {
							text: 'Total Check Pc(s)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						opposite: true

					}, { // Secondary yAxis
						title: {
							text: 'NG Rate (%)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						max: 5,
						type: 'linear',
						opposite: true

					}
					],
					tooltip: {
						footerFormat: '<span>Detail</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,result.date,'ng_name');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					
					{
						type: 'column',
						data: qty_ng_foot,
						name: 'Total NG',
						colorByPoint: false,
						color: '#d62d2d',
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: qty_box_foot,
						name: 'Qty Check',
						colorByPoint: false,
						color: '#2064bd',
						yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: ng_rate_foot,
						name: 'NG Rate',
						colorByPoint: false,
						color:'#fff',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						
					},
					{
						type: 'spline',
						data: ng_rate_target_foot,
						name: 'Target NG Rate',
						colorByPoint: false,
						color:'#fcba03',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						dashStyle:'shortdash',
						lineWidth: 4,
						marker: {
			                radius: 4,
			                lineColor: '#fcba03',
			                lineWidth: 1
			            },
						
					},
					]
				});

				var total_ng = 0;
				var total_box = 0;

				for(var i = 0; i < result.ng_rate_block.length; i++){
					total_ng = total_ng + parseInt(result.ng_rate_block[i][0].qty_ng);
					total_box = total_box + (parseInt(result.ng_rate_block[i][0].qty_box)*4);
				}

				$('#total_block').append().empty();
				$('#total_block').html(total_box+total_ng+ '');

				$('#ok_block').append().empty();
				$('#ok_block').html(total_box + '');

				$('#ng_block').append().empty();
				$('#ng_block').html(total_ng + '');

				$('#pctg_block').append().empty();
				$('#pctg_block').html(((total_ng/(total_ng+total_box))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				var total_ng_target = 0;
				var total_box_target = 0;

				for(var i = 0; i < result.ng_target_block.length; i++){
					total_ng_target = total_ng_target + parseInt(result.ng_target_block[i].qty_ng);
					total_box_target = total_box_target + (parseInt(result.ng_target_block[i].qty_box)*4);
				}

				$('#pctg_target_block').append().empty();
				$('#pctg_target_block').html(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				console.log(total_ng_target);


				var categories_block = [];

				var qty_ng_block = [], qty_box_block = [], ng_rate_block = [],ng_rate_target_block = [];

				for(var i = 0; i < result.ng_rate_block.length; i++){
					categories_block.push(result.ng_rate_block[i][0].week_date);

					qty_ng_block.push(parseInt(result.ng_rate_block[i][0].qty_ng));
					qty_box_block.push(parseInt(result.ng_rate_block[i][0].qty_box)*4);
					var total_ngs = parseInt(result.ng_rate_block[i][0].qty_ng);
					var total_oks = parseInt(result.ng_rate_block[i][0].qty_ng)+(parseInt(result.ng_rate_block[i][0].qty_box)*4);
					var percents = (total_ngs/total_oks)*100;
					ng_rate_block.push(parseFloat(percents.toFixed(3)));
					ng_rate_target_block.push(parseFloat(((total_ng_target/(total_ng_target+total_box_target))*100).toFixed(3)));
				};


				Highcharts.chart('container_block', {
					chart: {
						type: 'column',
						height: '600',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Total NG',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories_block,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						
					}
					, { // Secondary yAxis
						title: {
							text: 'Total Check Pc(s)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						opposite: true

					}, { // Secondary yAxis
						title: {
							text: 'NG Rate (%)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						max: 5,
						type: 'linear',
						opposite: true

					}
					],
					tooltip: {
						blockerFormat: '<span>Detail</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,result.date,'ng_name');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					
					{
						type: 'column',
						data: qty_ng_block,
						name: 'Total NG',
						colorByPoint: false,
						color: '#d62d2d',
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: qty_box_block,
						name: 'Qty Check',
						colorByPoint: false,
						color: '#2064bd',
						yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: ng_rate_block,
						name: 'NG Rate',
						colorByPoint: false,
						color:'#fff',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						
					},
					{
						type: 'spline',
						data: ng_rate_target_block,
						name: 'Target NG Rate',
						colorByPoint: false,
						color:'#fcba03',
						yAxis:2,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						dashStyle:'shortdash',
						lineWidth: 4,
						marker: {
			                radius: 4,
			                lineColor: '#fcba03',
			                lineWidth: 1
			            },
						
					},
					]
				});
				

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
}

function ShowModal(cat,date,type) {
	$("#loading").show();
	var location = $('#location').val();
	var origin = $('#origin').val();
	var data = {
		cat:cat,
		date:date,
		type:type,
		location:location,
		origin:origin,
	}

	$.get('{{ url("fetch/assembly/ng_rate_detail") }}', data, function(result, status, xhr) {
		if(result.status){
			$('#tableDetail').DataTable().clear();
			$('#tableDetail').DataTable().destroy();
			$('#tableDetailBody').html('');
			var tableBody = '';
			var index = 1;
			var total_qty = 0;
			$.each(result.detail, function(key, value) {
				tableBody += '<tr>';
				tableBody += '<td>'+index+'</td>';
				tableBody += '<td>'+value.serial_number+'</td>';
				tableBody += '<td>'+value.model+'</td>';
				tableBody += '<td>'+value.location+'</td>';
				tableBody += '<td>'+value.ng_name+'</td>';
				tableBody += '<td>'+value.ongko+'</td>';
				var qty = 1;
				if (value.value_bawah == null) {
					tableBody += '<td>'+qty+'</td>';
					tableBody += '<td></td>';
					tableBody += '<td></td>';
				}else{
					tableBody += '<td>'+qty+'</td>';
					tableBody += '<td>'+value.value_bawah+'</td>';
					tableBody += '<td>'+value.value_atas+'</td>';
				}
				tableBody += '<td>'+(value.value_lokasi || "")+'</td>';
				tableBody += '<td>'+value.employee_id+'<br>'+value.name+'</td>';
				tableBody += '<td>'+value.created+'</td>';
				tableBody += '</tr>';
				total_qty++;
				index++;
			});
			$('#tableDetailBody').append(tableBody);
			$('#total_all').html(total_qty);

			var table = $('#tableDetail').DataTable({
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
				// "footerCallback": function ( row, data, start, end, display ) {
		  //           var api = this.api(), data;
		 
		  //           var intVal = function ( i ) {
		  //               return typeof i === 'string' ?
		  //                   i.replace(/[\$,]/g, '')*1 :
		  //                   typeof i === 'number' ?
		  //                       i : 0;
		  //           };

		  //           pageTotal = api
		  //               .column( 7, { page: 'current'} )
		  //               .data()
		  //               .reduce( function (a, b) {
		  //                   return intVal(a) + intVal(b);
		  //               }, 0 );
		 
		  //           $( api.column( 7 ).footer() ).html(
		  //               pageTotal
		  //           );
		  //       }
			});
			if (type === 'ng_name') {
				$('#modalDetailTitle').html('Detail NG '+cat+'<br>Tanggal '+date);
			}else{
				$('#modalDetailTitle').html('Detail NG Pada Model '+cat+'<br>Tanggal '+date);
			}
			$("#loading").hide();
			$('#modalDetail').modal('show');
		}else{
			$("#loading").hide();
			openErrorGritter('Error!',result.message);
		}
	});
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