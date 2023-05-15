@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
	<style type="text/css">
		thead>tr>th{
			font-size: 16px;
		}
		#tableBodyList > tr:hover {
			cursor: pointer;
			background-color: #7dfa8c;
		}

		#tableBodyResume > tr:hover {
			cursor: pointer;
			background-color: #7dfa8c;
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
		input[type="radio"] {
		}

		#loading { display: none; }


		.radio {
			display: inline-block;
			position: relative;
			padding-left: 35px;
			margin-bottom: 12px;
			cursor: pointer;
			font-size: 16px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		/* Hide the browser's default radio button */
		.radio input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		/* Create a custom radio button */
		.checkmark {
			position: absolute;
			top: 0;
			left: 0;
			height: 25px;
			width: 25px;
			background-color: #eee;
			border-radius: 50%;
		}

		/* On mouse-over, add a grey background color */
		.radio:hover input ~ .checkmark {
			background-color: #ccc;
		}

		/* When the radio button is checked, add a blue background */
		.radio input:checked ~ .checkmark {
			background-color: #2196F3;
		}

		/* Create the indicator (the dot/circle - hidden when not checked) */
		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the indicator (dot/circle) when checked */
		.radio input:checked ~ .checkmark:after {
			display: block;
		}

		/* Style the indicator (dot/circle) */
		.radio .checkmark:after {
			top: 9px;
			left: 9px;
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: white;
		}

	</style>
	@stop
	@section('header')
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<section class="content-header">
		<h1>
			{{ $page }}
			<small><span class="text-purple">{{$title_jp}}</span></small>
			<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#trouble-modal" onclick="troubleMaker()">
				<b>TROUBLE</b>
			</button>
		</h1>
	</section>
	@stop
	@section('content')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<section class="content">
		<input type="hidden" id="data" value="data">
		<div class="row">
			<div class="col-xs-5">
				<div style="padding: 0;">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
						<tbody>
							<tr>
								<th style="width:70%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;" colspan="7">Operator</th>
								<th style="width:30%; background-color: #beffa6; text-align: center; color: black; padding:0;font-size: 15px;" colspan="3">Shift List</th>
							</tr>
							<tr>
								<td style="background-color: #6e81ff; text-align: center; color: white; font-size:1vw; padding:0;width: 30%;vertical-align: middle;" colspan="3" id="op">-</td>
								<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; padding:0;font-size: 1vw;vertical-align: middle;" colspan="4" id="op2">-</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getDataShift('Shift 1')">
									<center>
										<p class="btn btn-success" id="shift_1" style="font-size: 1vw;">1</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getDataShift('Shift 2')">
									<center>
										<p class="btn btn-success" id="shift_2" style="font-size: 1vw;">2</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getDataShift('Shift 3')">
									<center>
										<p class="btn btn-success" id="shift_3" style="font-size: 1vw;">3</p>
									</center>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
						<tbody>
							<tr>
								<th style="width:70%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">PILIH PROSES</th>
							</tr>
							<tr>
								<td style="background-color: #6e81ff; text-align: center; color: white; font-size:1vw; padding:0;width: 30%;vertical-align: middle;">
									<select class="form-control" style="width: 100%; height: 40px; font-size: 15px; text-align: center;" id="process_desc" name="process_desc" data-placeholder="Choose Process Desc" required onchange="itemList()">
										<option value="-">Pilih Proses</option>
										<option value="Forging">Forging</option>
										<option value="Forging 1st">Forging 1st</option>
										<option value="Forging 2nd">Forging 2nd</option>
										<option value="Forging 3rd">Forging 3rd</option>
										<option value="Bending">Bending</option>
										<option value="Bending 1st">Bending 1st</option>
										<option value="Bending 2nd">Bending 2nd</option>
										<option value="Bending 3rd">Bending 3rd</option>
										<option value="Bending 4th">Bending 4th</option>
										<option value="Trimming">Trimming</option>
										<option value="Blank Nuki">Blank Nuki</option>
										<option value="Nukishibori">Nukishibori</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<!-- <div class="col-md-12" id="process_desc_select">
						<center style="width:100%; background-color: rgb(220,220,220); text-align: center; color: black;font-weight: bold;">
							<span>PILIH PROSES</span>
						</center>
						
					</div> -->
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
						<tbody>
							<tr>
								<th style="background-color: #ffcccc; text-align: center; color: black; padding:0;font-size: 15px;" colspan="7">Machine List (Amada)</th>
							</tr>
							<tr>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 1')">
									<center>
										<p class="btn btn-danger" id="amada_1" style="font-size: 1vw;">#1</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 2')">
									<center>
										<p class="btn btn-danger" id="amada_2" style="font-size: 1vw;">#2</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 3')">
									<center>
										<p class="btn btn-danger" id="amada_3" style="font-size: 1vw;">#3</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 4')">
									<center>
										<p class="btn btn-danger" id="amada_4" style="font-size: 1vw;">#4</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 5')">
									<center>
										<p class="btn btn-danger" id="amada_5" style="font-size: 1vw;">#5</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 6')">
									<center>
										<p class="btn btn-danger" id="amada_6" style="font-size: 1vw;">#6</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 7')">
									<center>
										<p class="btn btn-danger" id="amada_7" style="font-size: 1vw;">#7</p>
									</center>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
						<tbody>
							<tr>
								<th style="width:15%; background-color: #ffcb96; text-align: center; color: black; padding:0;font-size: 15px;" colspan="6" width="50%">Machine List (Komatsu)</th>
							</tr>
							<tr>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Komatsu 1')">
									<center>
										<p class="btn btn-default bg-orange" id="komatsu_1" style="font-size: 1vw;">#1</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Komatsu 2')">
									<center>
										<p class="btn btn-default bg-orange" id="komatsu_2" style="font-size: 1vw;">#2</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Komatsu 3')">
									<center>
										<p class="btn btn-default bg-orange" id="komatsu_3" style="font-size: 1vw;">#3</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Komatsu 4')">
									<center>
										<p class="btn btn-default bg-orange" id="komatsu_4" style="font-size: 1vw;">#4</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Komatsu 5')">
									<center>
										<p class="btn btn-default bg-orange" id="komatsu_5" style="font-size: 1vw;">#5</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Komatsu 6')">
									<center>
										<p class="btn btn-default bg-orange" id="komatsu_6" style="font-size: 1vw;">#6</p>
									</center>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="box" style="padding: 0;">
					<div class="box-body">
						<span style="font-size: 15px; font-weight: bold;">ITEM LIST:</span>
						<table class="table table-hover table-striped" id="tableList">
							<thead>
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 2%;">Material</th>
									<th style="width: 5%;">Part</th>
									<th style="width: 5%;">Desc</th>
									<!-- <th style="width: 5%;">Part Num</th> -->
									<th style="width: 5%;">Process</th>
									<!-- <th style="width: 5%;">Kanagata Number</th> -->
								</tr>					
							</thead>
							<tbody id="tableBodyList">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-xs-7">
				<div class="row">
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Shift:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="shift" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Material:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="material_number" style="width: 100%; height: 30px; font-size:15px; text-align: center;" disabled>
							</div>
						</div>						
					</div>
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Machine:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="machine" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Part:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="part_name" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
						</div>
					</div>
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Product:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="product" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Desc:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="material_description" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
						</div>						
					</div>
					<div class="col-xs-12" style="padding-top: 5px;">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
								<tr id="nukishibori1">
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">PUNCH PLATE</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">STRIPPER PLATE</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">DRAWING PUNCH</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">DRAWING DIE</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">DIE PLATE</th>
								</tr>
								<tr id="nukishibori2">
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="ppl2" id="ppl2" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="splate" id="splate" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dp" id="dp" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dd" id="dd" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dies2" id="dies2" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
								</tr>
								<tr id="nukishibori3">
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="ppl2_total" id="ppl2_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="splate_total" id="splate_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dp_total" id="dp_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dd_total" id="dd_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="die2_total" id="die2_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
								</tr>
								<tr id="nukishibori4">
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">SNAP RING</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">LOWER K.O</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">UPPER K.O</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">HALF NUKI PUNCH</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">DIE INSERT</th>
								</tr>
								<tr id="nukishibori5">
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="snap" id="snap" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="lower" id="lower" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="upper" id="upper" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="half" id="half" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dinsert" id="dinsert" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
								</tr>
								<tr id="nukishibori6">
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="snap_total" id="snap_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="lower_total" id="lower_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="upper_total" id="upper_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="half_total" id="half_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dinsert_total" id="dinsert_total" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
								</tr>
								<tr id="all1">
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">PUNCH</th>
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">PUNCH PLATE</th>
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">DIES</th>
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">PLATE</th>
								</tr>
								<tr id="all2">
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="punch" id="punch" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="ppl" id="ppl" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="dies" id="dies" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
									<td style=" text-align: center; color: black; font-size:1vw;padding: 0px;">
										<input type="text" name="plate" id="plate" readonly="" style="width: 100%;height: 40px;font-size: 15px;text-align: center;">
									</td>
								</tr>
								<tr id="all3">
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">RUNNING PUNCH</th>
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">RUNNING PUNCH PLATE</th>
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">RUNNING DIES</th>
									<th style="width:25%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">RUNNING PLATE</th>
								</tr>
								<tr id="all4">
									<td style=" text-align: center; color: black;padding: 0px;">
										<input type="text" id="punch_total" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
									</td>
									<td style=" text-align: center; color: black;padding: 0px;">
										<input type="text" id="ppl_total" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
									</td>
									<td style=" text-align: center; color: black;padding: 0px;">
										<input type="text" id="die_total" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
									</td>
									<td style=" text-align: center; color: black;padding: 0px;">
										<input type="text" id="plate_total" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
									</td>
								</tr>
						</table>
					</div>
				</div>
				<button class="btn btn-success" onclick="start()" id="start_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;margin-top: 20px;">
					TEKAN UNTUK MULAI PROSES
				</button>
				<div class="row" id="processtime_picker">
					<div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Start Time</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="background-color: #fffcb7; text-align: center; color: black; font-size:25px; width: 30%;" id="start_time"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" id="downtime_picker">
					<div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<tbody>
								<tr>
									
								</tr>
								<tr>
									
								</tr>
							</tbody>
						</table>
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="background-color: #6e81ff; color: white;text-align: center; padding:0;font-size: 15px; display: none;" id="th_molding">SETUP KANAGATA</th>
									<th rowspan="2" style="width:15%; background-color: #6e81ff;color: white; text-align: center; padding:0;font-size: 15px;vertical-align: middle;" colspan="2">PRODUCTION TIME</th>
								</tr>
								<tr>
									<th style="background-color: lightcoral; color: white;text-align: center; padding:0;font-size: 15px;" id="status_mesin"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px; display: none;" id="th_pasang">Pasang Kanagata</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px; display: none;" id="th_lepas">Lepas Kanagata</th>
									<!-- <th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Process Time</th> -->
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Kensa Time</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Electric Supply Time</th>
								</tr>
								<tr>
									<td style="text-align: center; color: black; font-size:2vw;display: none;" id="td_pasang">
							        <button class="btn btn-md btn-success" id="startpasmod" onClick="mulaiPasang()"><b>MULAI PASANG KANAGATA</b></button> 
							        <button class="btn btn-md btn-danger" id="stoppasmod" onClick="selesaiPasang()"><b>SELESAI PASANG</b></button>
									<div class="timerpasmod">
							            <span class="hourpasmod">00</span>:<span class="minutepasmod">00</span>:<span class="secondpasmod">10</span>
							        </div>
							        <input type="hidden" id="pasang_molding" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
							    	</td>

									<td style=" text-align: center; color: black; font-size:2vw;display: none; " id="td_lepas">
									<button class="btn btn-md btn-success" id="startlepmod" onClick="mulaiLepas()"><b>MULAI LEPAS KANAGATA</b></button> 
							        <button class="btn btn-md btn-danger" id="stoplepmod" onClick="selesaiLepas()"><b>SELESAI LEPAS</b></button>
									<div class="timerlepmod">
							            <span class="hourlepmod">00</span>:<span class="minutelepmod">00</span>:<span class="secondlepmod">10</span>
							        </div>
									<input type="hidden" id="lepas_molding" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
									</td>

									<!-- <td style=" text-align: center; color: black; font-size:2vw;padding: 0px;">
									<input type="text" id="process_time" class="form-control timepicker" style="width: 100%; height: 60px; font-size: 35px; text-align: center;" placeholder="0:00:00" required readonly></td> -->
									<td style="text-align: center; color: black; font-size:2vw; ">
									<button class="btn btn-lg btn-success" id="startkensatime" onClick="timerkensatime.start(1000)">Start</button> 
							        <button class="btn btn-lg btn-danger" id="stopkensatime" onClick="timerkensatime.stop()">Stop</button>
									<div class="timerkensatime">
							            <span class="hourkensatime">00</span>:<span class="minutekensatime">00</span>:<span class="secondkensatime">00</span>
							        </div>
									<input type="hidden" id="kensa_time" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required disabled></td>
									<td style="text-align: center; color: black; font-size:2vw; ">
									<button class="btn btn-lg btn-success" id="startelectime" onClick="timerelectime.start(1000)">Start</button> 
							        <button class="btn btn-lg btn-danger" id="stopelectime" onClick="timerelectime.stop()">Stop</button>
									<div class="timerelectime">
							            <span class="hourelectime">00</span>:<span class="minuteelectime">00</span>:<span class="secondelectime">00</span>
							        </div>
									<input type="hidden" id="electric_time" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" id="production_data">
					<div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;" id="tableAll">
							<thead>
								<tr>
									<th style="width:15%; background-color: #6e81ff; color: white; text-align: center; padding:0;font-size: 15px;" colspan="5">PRODUCTION DATA</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Actual Shot</th>
									<!-- <th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Punch</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Punch Plate</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Dies</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Plate</th> -->
								</tr>
								<tr>
									<td style=" text-align: center; color: black; font-size:2vw;padding: 0px;"><input type="number" class="form-control numpad" id="data_ok" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Actual Shot" onchange="dataOkKeyUp()"></td>
								</tr>
								<!-- <tr>
									<td style=" text-align: center; color: black; font-size:2vw;padding: 0px;"><input type="number" class="form-control numpad" id="data_ok" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Actual Shot" onchange="dataOkKeyUp()"></td>
									<td style="text-align: center; color: black; font-size:2vw;padding: 0px;"><input type="number" class="form-control" id="jumlah_punch" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Punch" disabled></td>
									<td style="text-align: center; color: black; font-size:2vw;padding: 0px;"><input type="number" class="form-control" id="jumlah_ppl" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Punch Plate" disabled></td>
									<td style="text-align: center; color: black; font-size:2vw;padding: 0px;"><input type="number" class="form-control" id="jumlah_dies" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Dies" disabled></td>
									<td style="text-align: center; color: black; font-size:2vw;padding: 0px;"><input type="number" class="form-control" id="jumlah_plate" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled></td>
								</tr> -->
								<input type="hidden" class="form-control" id="jumlah_punch" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Punch" disabled>
								<input type="hidden" class="form-control" id="jumlah_ppl" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Punch Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_dies" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Dies" disabled>
								<input type="hidden" class="form-control" id="jumlah_plate" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_ppl2" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_dies2" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_splate" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_dp" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_dd" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_snap" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_lower" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_upper" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_half" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
								<input type="hidden" class="form-control" id="jumlah_dinsert" style="width: 100%; height: 60px; font-size: 30px; text-align: center;" placeholder="Jumlah Plate" disabled>
							</tbody>
						</table>
					</div>
				</div>
				<button class="btn btn-danger" onclick="end()" id="end_button" style="font-size:40px; width: 100%; font-weight: bold; padding: 0;margin-top: 30px;">
					TEKAN UNTUK SELESAI PROSES
				</button>
			</div>
		</div>
		<div class="modal fade" id="modalOperator">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<div class="modal-body table-responsive no-padding">
							<div class="form-group">
								<label for="exampleInputEmail1">Employee ID</label>
								<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Masukkan NIK" required>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="trouble-modal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header" style="background-color: lightskyblue">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" align="center"><b>Input Data Trouble</b></h4>
					</div>
					<div class="modal-body">
						<div class="box-body">
							<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="">Trouble Start Time</label>
									<input type="text" class="form-control" name="trouble_start" id="trouble_start" placeholder="Enter Leader" readonly>
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="">Reason</label>
									<textarea name="reason" id="reason" class="form-control" rows="2" required="required"></textarea>
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<span style="font-size: 15px; font-weight: bold;">TROUBLE HISTORY:</span>
								<table class="table table-hover table-striped" id="tableTrouble">
									<thead>
										<tr>
											<th style="width: 1%;">#</th>
											<th style="width: 2%;">Start Time</th>
											<th style="width: 5%;">Reason</th>
											<th style="width: 2%;">End Time</th>
											<th style="width: 2%;">Action</th>
										</tr>					
									</thead>
									<tbody id="tableBodyTrouble">
									</tbody>
								</table>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
								<button onclick="createTrouble()" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	@endsection
	@section('scripts')
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

		$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
		$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
		$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
		$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
		$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
		$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

		jQuery(document).ready(function() {
			$('#operator').focus();
			$('body').toggleClass("sidebar-collapse");
			$('.select2').select2({
				allowClear:true
			});

			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});

			cancelAll();

			$('#modalOperator').modal({
				backdrop: 'static',
				keyboard: false
			});
			$('#operator').val('');
			$('#tag').val('');

			$("#downtime_picker").hide();
			$("#processtime_picker").hide();
			$("#productiontime_picker").hide();
			$("#production_data").hide();
			$("#end_button").hide();
			$("#reset_button").hide();
			// $("#process_desc_select").hide();

			$('#stoppasmod').hide();
			$('#stoplepmod').hide();
			// $('#stopproctime').hide();
			$('#stopelectime').hide();
			$('#stopkensatime').hide();

			$('.timepicker').timepicker({
				// timeFormat: 'HH:mm:ss',
				showInputs: false,
				showMeridian: false,
				timeFormat: 'HH:mm:ss',
				showSeconds:true
				// defaultTime: '00:00:00',
			});
		});

		function mulaiPasang() {
			if ($('#process_desc').val().match(/Nukishibori/gi)) {
				if ($('#machine').val() == '' || $('#ppl2').val() == '') {
					openErrorGritter('Error!','Pilih Mesin dan Kanagata');
					return false;
				}
			}else{
				if ($('#machine').val() == '' || $('#punch').val() == '') {
					openErrorGritter('Error!','Pilih Mesin dan Kanagata');
					return false;
				}
			}
			timerpasmod.start(1000);
		}

		function selesaiPasang() {
			if (confirm('Apakah Anda yakin?')) {
				if ($('#process_desc').val().match(/Nukishibori/gi)) {
					timerpasmod.stop();
					var data = {
						machine : $("#machine").val(),
						molding : $('#ppl2').val(),
						status:'PASANG'
					}

					$.post('{{ url("input/press/setup_molding") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', 'Success Pasang Kanagata');
							$('#status_mesin').html('KANAGATA : '+$('#ppl2').val());
							$('#th_molding').show();
							$('#th_pasang').hide();
							$('#td_pasang').hide();
							$('#th_lepas').show();
							$('#td_lepas').show();
							$('#loading').hide();
						}
						else{
							$('#loading').hide();
							openErrorGritter('Error', result.message);
						}
					});
				}else{
					timerpasmod.stop();
					var data = {
						machine : $("#machine").val(),
						molding : $('#punch').val(),
						status:'PASANG'
					}

					$.post('{{ url("input/press/setup_molding") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', 'Success Pasang Kanagata');
							$('#status_mesin').html('KANAGATA : '+$('#punch').val());
							$('#th_molding').show();
							$('#th_pasang').hide();
							$('#td_pasang').hide();
							$('#th_lepas').show();
							$('#td_lepas').show();
							$('#loading').hide();
						}
						else{
							$('#loading').hide();
							openErrorGritter('Error', result.message);
						}
					});
				}
			}
		}

		function mulaiLepas() {
			if ($('#process_desc').val().match(/Nukishibori/gi)) {
				if ($('#machine').val() == '' || $('#ppl2').val() == '') {
					openErrorGritter('Error!','Pilih Mesin dan Kanagata');
					return false;
				}
			}else{
				if ($('#machine').val() == '' || $('#punch').val() == '') {
					openErrorGritter('Error!','Pilih Mesin dan Kanagata');
					return false;
				}
			}
			timerlepmod.start(1000);
		}

		function selesaiLepas() {
			if (confirm('Apakah Anda yakin?')) {
				timerlepmod.stop();
				var data = {
					machine : $("#machine").val(),
					status:'LEPAS'
				}

				$.post('{{ url("input/press/setup_molding") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Success Lepas Kanagata');
						$('#status_mesin').html('MESIN KOSONG');
						$('#th_molding').show();
						$('#th_pasang').show();
						$('#td_pasang').show();
						$('#th_lepas').hide();
						$('#td_lepas').hide();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error', result.message);
					}
				});
			}
		}

		function dataOkKeyUp() {
			var x = document.getElementById("data_ok").value;
			document.getElementById("jumlah_punch").value = x;
			document.getElementById("jumlah_dies").value = x;
			document.getElementById("jumlah_plate").value = x;
			document.getElementById("jumlah_ppl").value = x;
			document.getElementById("jumlah_ppl2").value = x;
			document.getElementById("jumlah_dies2").value = x;
			document.getElementById("jumlah_splate").value = x;
			document.getElementById("jumlah_dp").value = x;
			document.getElementById("jumlah_dd").value = x;
			document.getElementById("jumlah_snap").value = x;
			document.getElementById("jumlah_lower").value = x;
			document.getElementById("jumlah_upper").value = x;
			document.getElementById("jumlah_half").value = x;
			document.getElementById("jumlah_dinsert").value = x;
		}

		function getData(nama_mesin){
			for(var i = 1; i < 8;i++){
				$('#amada_'+i).css('background-color', '#d73925');
				$('#amada_'+i).css('border-color', '#ac2925');
				$('#amada_'+i).css('color', '#fff');
			}
			for(var i = 1; i < 7;i++){
				$('#komatsu_'+i).css('background-color', '#ff851b');
				$('#komatsu_'+i).css('border-color', '#ff851b');
				$('#komatsu_'+i).css('color', '#fff');
			}
			if (nama_mesin.match(/Amada/gi)) {
				$('#amada_'+nama_mesin.split(' ')[1]).css('background-color', '#fff');
				$('#amada_'+nama_mesin.split(' ')[1]).css('border-color', '#000');
				$('#amada_'+nama_mesin.split(' ')[1]).css('color', '#000');
			}
			if (nama_mesin.match(/Komatsu/gi)) {
				$('#komatsu_'+nama_mesin.split(' ')[1]).css('cssText', 'background-color: #fff !important;color: #000 !important');
				$('#komatsu_'+nama_mesin.split(' ')[1]).css('border-color', '#000');
			}
			$("#machine").val(nama_mesin);
		}

		function getDataShift(shift){
			for(var i = 1; i < 4;i++){
				$('#shift_'+i).css('background-color', '#00a65a');
				$('#shift_'+i).css('border-color', '#008d4c');
				$('#shift_'+i).css('color', '#fff');
			}
			$('#shift_'+shift.split(' ')[1]).css('background-color', '#fff');
			$('#shift_'+shift.split(' ')[1]).css('border-color', '#000');
			$('#shift_'+shift.split(' ')[1]).css('color', '#000');
			$("#shift").val(shift);
		}

		function troubleMaker(){
			$("#trouble_start").val(getActualFullDate());
			troubleList();
		}

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

		$('#operator').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#operator").val().length >= 8){
					$('#loading').show();
					var data = {
						employee_id : $("#operator").val()
					}

					$.get('{{ url("scan/press/operator") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							$('#modalOperator').modal('hide');
							$('#op').html(result.employee.employee_id);
							$('#op2').html(result.employee.name);
							$('#employee_id').val(result.employee.employee_id);
							$('#tag').focus();
							// itemList();
							$('#loading').hide();
						}
						else{
							$('#loading').hide();
							// audio_error.play();
							openErrorGritter('Error', result.message);
							$('#operator').val('');
							$("#operator").focus();
						}
					});
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', 'Employee ID Invalid.');
					// audio_error.play();
					$("#operator").val("");
					$("#operator").focus();
				}
			}
		});

		function cancelAll() {
			$('#all1').hide();
			$('#all2').hide();
			$('#all3').hide();
			$('#all4').hide();
			$('#nukishibori1').hide();
			$('#nukishibori2').hide();
			$('#nukishibori3').hide();
			$('#nukishibori4').hide();
			$('#nukishibori5').hide();
			$('#nukishibori6').hide();
			$("#process_desc").val('-');
			for(var i = 1; i < 4;i++){
				$('#shift_'+i).css('background-color', '#00a65a');
				$('#shift_'+i).css('border-color', '#008d4c');
				$('#shift_'+i).css('color', '#fff');
			}
			for(var i = 1; i < 8;i++){
				$('#amada_'+i).css('background-color', '#d73925');
				$('#amada_'+i).css('border-color', '#ac2925');
				$('#amada_'+i).css('color', '#fff');
			}
			for(var i = 1; i < 7;i++){
				$('#komatsu_'+i).css('background-color', '#ff851b');
				$('#komatsu_'+i).css('border-color', '#ff851b');
				$('#komatsu_'+i).css('color', '#fff');
			}
			$('#punch_total').val('');
			$('#die_total').val('');
			$('#plate_total').val('');
			$('#ppl_total').val('');
			$('#ppl2_total').val('');
			$('#dp_total').val('');
			$('#dd_total').val('');
			$('#splate_total').val('');
			$('#die2_total').val('');
			$('#snap_total').val('');
			$('#lower_total').val('');
			$('#upper_total').val('');
			$('#half_total').val('');
			$('#dinsert_total').val('');

			$('#jumlah_punch').val('');
			$('#jumlah_dies').val('');
			$('#jumlah_plate').val('');
			$('#jumlah_ppl').val('');
			$('#jumlah_ppl2').val('');
			$('#jumlah_dies2').val('');
			$('#jumlah_splate').val('');
			$('#jumlah_dp').val('');
			$('#jumlah_dd').val('');
			$('#jumlah_snap').val('');
			$('#jumlah_lower').val('');
			$('#jumlah_upper').val('');
			$('#jumlah_half').val('');
			$('#jumlah_dinsert').val('');

			$('#data_ok').val('');
			$('#shift').val('');
			$('#material_number').val('');
			$('#machine').val('');
			$('#part_name').val('');
			$('#product').val('');
			$('#material_description').val('');

			$('#punch').val('');
			$('#dies').val('');
			$('#plate').val('');
			$('#ppl').val('');
			$('#ppl2').val('');
			$('#dies2').val('');
			$('#dp').val('');
			$('#dd').val('');
			$('#splate').val('');
			$('#snap').val('');
			$('#lower').val('');
			$('#upper').val('');
			$('#half').val('');
			$('#dinsert').val('');
			// $('#process_time').val('00:00:00');
		}

		function itemList(){
			$('#loading').show();
			if ($('#process_desc').val().match(/Forging/)) {
				var proc = $('#process_desc').val().split(' ')[0];
			}else{
				var proc = $('#process_desc').val();
			}
			$('#all1').hide();
			$('#all2').hide();
			$('#all3').hide();
			$('#all4').hide();
			$('#nukishibori1').hide();
			$('#nukishibori2').hide();
			$('#nukishibori3').hide();
			$('#nukishibori4').hide();
			$('#nukishibori5').hide();
			$('#nukishibori6').hide();
			if ($('#process_desc').val().match(/Nukishibori/)) {
				$('#all1').hide();
				$('#all2').hide();
				$('#all3').hide();
				$('#nukishibori1').show();
				$('#nukishibori2').show();
				$('#nukishibori3').show();
				$('#nukishibori4').show();
				$('#nukishibori5').show();
				$('#nukishibori6').show();
			}else{
				$('#all1').show();
				$('#all2').show();
				$('#all3').show();
				$('#all4').show();
				$('#nukishibori1').hide();
				$('#nukishibori2').hide();
				$('#nukishibori3').hide();
				$('#nukishibori4').hide();
				$('#nukishibori5').hide();
				$('#nukishibori6').hide();
			}
			var data = {
				process : proc
			}

			// $.get('{{ url("fetch/press/fetchProcess") }}', data, function(result, status, xhr){
			// 	if(result.status){
			// 		$("#process_desc_select").show();
			// 		$('#process_desc').html(result.process_desc);
			// 	}
			// 	else{
			// 		openErrorGritter('Error!',result.message);
			// 	}
			// });

			clearKanan();

			$.get('{{ url("fetch/press/press_list") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableList').DataTable().clear();
					$('#tableList').DataTable().destroy();
					$('#tableBodyList').html("");
					var tableData = "";
					var count = 1;
					$.each(result.lists, function(key, value) {
						tableData += '<tr onclick="fetchCount(\''+value.material_number+'\',\''+value.material_name+'\',\''+value.process+'\',\''+value.product+'\',\''+value.punch_die_number+'\')">';
						tableData += '<td>'+ count +'</td>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_name +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						// tableData += '<td>'+ value.punch_die_number +'</td>';
						tableData += '<td>'+ value.process +'</td>';
						tableData += '</tr>';
						count += 1;
					});
					$('#tableBodyList').append(tableData);
					$('#tableList').DataTable({
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
						"processing": true
					});
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function createTrouble(){
			$('#loading').show();
			var date = '{{ date("Y-m-d") }}';
			var pic = $("#op").text();
			var product = $("#product").val();
			var machine = $("#machine").val();
			var shift = $("#shift").val();
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var trouble_start = $("#trouble_start").val();
			var reason = $("#reason").val();

			var data = {
				date : date,
				pic : pic,
				product : product,
				machine : machine,
				shift : shift,
				material_number : material_number,
				process : process,
				start_time : trouble_start,
				reason : reason,
			}

			$.post('{{ url("index/press/store_trouble") }}', data, function(result, status, xhr){
				if(result.status){
					// $("#trouble-modal").modal('hide');
					openSuccessGritter('Success','New Trouble Data has been created');
					$("#reason").val('');
					troubleList();
					$('#loading').hide();
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error',result.message);
				}
			});
		}

		function troubleList(){
			var date = '{{ date("Y-m-d") }}';
			var pic = $("#op").text();
			var product = $("#product").val();
			var machine = $("#machine").val();
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var data = {
				date : date,
				pic : pic,
				product : product,
				machine : machine,
				material_number : material_number,
				process : process
			}
			$.get('{{ url("fetch/press/trouble_list") }}', data, function(result, status, xhr){
				if(result.status){
					var tableData = "";
					$('#tableBodyTrouble').html("");
					var count = 1;
					$.each(result.lists, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ count +'</td>';
						tableData += '<td>'+ value.start_time +'</td>';
						tableData += '<td>'+ value.reason +'</td>';
						tableData += '<td>'+ (value.end_time || '') +'</td>';
						if(value.end_time == null){
							tableData += '<td><button type="button" class="btn btn-danger pull-right" onclick="finishTrouble('+ value.id +')"><b>FINISH</b></button></td>';
						}else{
							tableData += '<td></td>';
						}
						tableData += '</tr>';

						count += 1;
					});
					$('#tableBodyTrouble').append(tableData);
				}
				else{
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function finishTrouble(id){

			var data = {
				id : id
			}

			$.post('{{ url("index/press/finish_trouble") }}', data, function(result, status, xhr){
				if(result.status){
					// $("#trouble-modal").modal('hide');
					openSuccessGritter('Success','The Trouble has been finished');
					troubleList();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Trouble Data Failed');
				}
			});
		}

		function clearKanan() {
			$('#punch_total').val('');
			$('#die_total').val('');
			$('#plate_total').val('');
			$('#ppl_total').val('');
			$('#ppl2_total').val('');
			$('#dp_total').val('');
			$('#dd_total').val('');
			$('#splate_total').val('');
			$('#die2_total').val('');
			$('#snap_total').val('');
			$('#lower_total').val('');
			$('#upper_total').val('');
			$('#half_total').val('');
			$('#dinsert_total').val('');

			$('#jumlah_punch').val('');
			$('#jumlah_dies').val('');
			$('#jumlah_plate').val('');
			$('#jumlah_ppl').val('');
			$('#jumlah_ppl2').val('');
			$('#jumlah_dies2').val('');
			$('#jumlah_splate').val('');
			$('#jumlah_dp').val('');
			$('#jumlah_dd').val('');
			$('#jumlah_snap').val('');
			$('#jumlah_lower').val('');
			$('#jumlah_upper').val('');
			$('#jumlah_half').val('');
			$('#jumlah_dinsert').val('');

			$('#punch').val('');
			$('#dies').val('');
			$('#plate').val('');
			$('#ppl').val('');
			$('#ppl2').val('');
			$('#dies2').val('');
			$('#dp').val('');
			$('#dd').val('');
			$('#splate').val('');
			$('#snap').val('');
			$('#lower').val('');
			$('#upper').val('');
			$('#half').val('');
			$('#dinsert').val('');
		}

		function fetchCount(material_number,material_name,processes,product,punch_die_number){
			$("#loading").show();

			var data = {
				material_number : material_number,
				material_name : material_name,
				product : product,
				process : processes,
				// punch_die_number : punch_die_number,
			}
			$.get('{{ url("fetch/press/fetchMaterialList") }}', data, function(result, status, xhr){
				if(result.status){
					// $('#process_desc').html("");
					clearKanan();

					var processes = "";
					$('#material_number').val(result.count.material_number);
					$('#material_description').val(result.count.material_description);
					$('#part_name').val(result.count.material_name);
					$('#product').val(result.count.product);
					$('#punch').val(result.punch_data);
					$('#dies').val(result.dies_data);
					$('#dies2').val(result.dies_data);
					$('#plate').val(result.plate_data);
					$('#splate').val(result.plate_data);
					$('#ppl').val(result.ppl_data);
					$('#ppl2').val(result.ppl_data);
					$('#dp').val(result.dp_data);
					$('#dd').val(result.dd_data);
					$('#snap').val(result.snap_data);
					$('#lower').val(result.lower_data);
					$('#upper').val(result.upper_data);
					$('#half').val(result.half_data);
					$('#dinsert').val(result.dinsert_data);
					if (result.punch_first != null) {
						fetchTotalPunch(result.punch_first.punch_die_number);
					}
					if (result.dies_first != null) {
						fetchTotalDie(result.dies_first.punch_die_number);
					}
					if (result.plate_first != null) {
						fetchTotalPlate(result.plate_first.punch_die_number);
					}
					if (result.ppl_first != null) {
						fetchTotalPpl(result.ppl_first.punch_die_number);
					}
					if (result.dp_first != null) {
						fetchTotalDp(result.dp_first.punch_die_number);
					}
					if (result.dd_first != null) {
						fetchTotalDd(result.dd_first.punch_die_number);
					}
					if (result.snap_first != null) {
						fetchTotalSnap(result.snap_first.punch_die_number);
					}
					if (result.lower_first != null) {
						fetchTotalLower(result.lower_first.punch_die_number);
					}
					if (result.upper_first != null) {
						fetchTotalUpper(result.upper_first.punch_die_number);
					}
					if (result.half_first != null) {
						fetchTotalHalf(result.half_first.punch_die_number);
					}
					if (result.dinsert_first != null) {
						fetchTotalDinsert(result.dinsert_first.punch_die_number);
					}
					$('#addCount').val("0");

					$("#loading").hide();
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!',result.message);
				}
			});

			var data = {
				machine : $("#machine").val()
			}

			$.get('{{ url("fetch/press/fetchMachine") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', 'Success Get Data Machine');
					if (result.machine.kanagata_status == null) {
						$('#status_mesin').html('MESIN KOSONG');
						$('#th_molding').show();
						$('#th_pasang').show();
						$('#td_pasang').show();
						$('#th_lepas').hide();
						$('#td_lepas').hide();
					}else{
						var sesuai = '';
						if ($('#process_desc').val().match(/Nukishibori/gi)) {
							if ($('#ppl2').val() != result.machine.kanagata_status) {
								sesuai = 'TIDAK SESUAI';
							}
						}else{
							if ($('#punch').val() != result.machine.kanagata_status) {
								sesuai = 'TIDAK SESUAI';
							}
						}
						$('#status_mesin').html('KANAGATA : '+result.machine.kanagata_status+' '+sesuai);
						$('#th_molding').show();
						$('#th_pasang').hide();
						$('#td_pasang').hide();
						$('#th_lepas').show();
						$('#td_lepas').show();
					}
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error', result.message);
				}
			});
		}

		function fetchTotalPunch(punch_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				punch_number : punch_number,
			}
			$.get('{{ url("fetch/press/fetchPunch") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#punch_total').val(result.total_punch);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalDie(die_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				die_number : die_number,
			}
			$.get('{{ url("fetch/press/fetchDie") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#die_total').val(result.total_die);
					$('#die2_total').val(result.total_die);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalPlate(plate_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				plate_number : plate_number,
			}
			$.get('{{ url("fetch/press/fetchPlate") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#plate_total').val(result.total_plate);
					$('#splate_total').val(result.total_plate);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalPpl(ppl_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				ppl_number : ppl_number,
			}
			$.get('{{ url("fetch/press/fetchPpl") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#ppl_total').val(result.total_ppl);
					$('#ppl2_total').val(result.total_ppl);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalDp(dp_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				dp_number : dp_number,
			}
			$.get('{{ url("fetch/press/fetchDp") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#dp_total').val(result.total_dp);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalDd(dd_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				dd_number : dd_number,
			}
			$.get('{{ url("fetch/press/fetchDd") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#dd_total').val(result.total_dd);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalSnap(snap_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				snap_number : snap_number,
			}
			$.get('{{ url("fetch/press/fetchSnap") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#snap_total').val(result.total_snap);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalLower(lower_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				lower_number : lower_number,
			}
			$.get('{{ url("fetch/press/fetchLower") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#lower_total').val(result.total_lower);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalUpper(upper_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				upper_number : upper_number,
			}
			$.get('{{ url("fetch/press/fetchUpper") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#upper_total').val(result.total_upper);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalHalf(half_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				half_number : half_number,
			}
			$.get('{{ url("fetch/press/fetchHalf") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#half_total').val(result.total_half);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}

		function fetchTotalDinsert(dinsert_number){
			$('#loading').show();
			var material_number = $("#material_number").val();
			// var process = 'Forging';
			var data = {
				material_number : material_number,
				// process : process,
				dinsert_number : dinsert_number,
			}
			$.get('{{ url("fetch/press/fetchdinsert") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.ada_reminder != '') {
						openErrorGritter('Error!',result.ada_reminder);
					}
					$('#dinsert_total').val(result.total_dinsert);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
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

		function start(){
			$('#loading').show();
			if ($('#shift').val() == '' || $('#machine').val() == '' || $('#material_number').val() == '' || $('#process_desc').val() == '-') {
				$('#loading').hide();
				openErrorGritter('Error!','Isi Semua Data');
				return false;
			}
			$("#start_time").html(getActualFullDate());
			$("#start_button").hide();
			$("#end_button").show();
			$("#downtime_picker").show();
			$("#processtime_picker").show();
			$("#productiontime_picker").show();
			$("#production_data").show();
			$('#loading').hide();
		}

		function reset(){
			window.location = "{{ url('index/press/create') }}";
		}

		function end(){
			$('#loading').show();

			var date = '{{ date("Y-m-d") }}';
			var pic = $("#op").text();
			var product = $("#product").val();
			var machine = $("#machine").val();
			var shift = $("#shift").val();
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var punch_number = $("#punch").val();
			var die_number = $("#dies").val();
			var plate_number = $("#plate").val();
			var ppl_number = $("#ppl").val();
			if ($("#process_desc").val().match(/Nukishibori/gi)) {
				ppl_number = $('#ppl2').val();
				dies_number = $('#dies2').val();
				plate_number = $('#splate').val();
			}
			var dp_number = $("#dp").val();
			var dd_number = $("#dd").val();
			var snap_number = $("#snap").val();
			var lower_number = $("#lower").val();
			var upper_number = $("#upper").val();
			var half_number = $("#half").val();
			var dinsert_number = $("#dinsert").val();
			var start_time = $("#start_time").text();
			var lepas_molding = $("#lepas_molding").val();
			var pasang_molding = $("#pasang_molding").val();
			// var process_time = $("#process_time").val();
			var kensa_time = $("#kensa_time").val();
			var electric_supply_time = $("#electric_time").val();
			var data_ok = $("#data_ok").val();

			var ppl_value = $("#jumlah_ppl").val();
			var die_value = $("#jumlah_dies").val();
			var plate_value = $("#jumlah_plate").val();

			if (!process.match(/Blank Nuki/gi)) {
				ppl_value = null;
			}

			if ($("#process_desc").val().match(/Nukishibori/gi)) {
				ppl_value = $('#jumlah_ppl2').val();
				dies_value = $('#jumlah_dies2').val();
				plate_value = $('#jumlah_splate').val();
			}

			var punch_value = $("#jumlah_punch").val();
			var dp_value = $('#jumlah_dp').val();
			var dd_value = $('#jumlah_dd').val();
			var snap_value = $('#jumlah_snap').val();
			var lower_value = $('#jumlah_lower').val();
			var upper_value = $('#jumlah_upper').val();
			var half_value = $('#jumlah_half').val();
			var dinsert_value = $('#jumlah_dinsert').val();

			if (process.match(/Forging/gi)) {
				plate_value = null;
			}

			if (process.match(/Nukishibori/gi)) {
				punch_value = null;
			}

			var end_time = getActualFullDate();

			var process_time = timeDifference(new Date(end_time.replace(/-/g,'/')),new Date(start_time.replace(/-/g,'/')));

			if(process == '' || machine == '' || data_ok == '' || lepas_molding == '' || pasang_molding == '' || process_time == '' || electric_supply_time == '' || shift == '' || material_number == ''){
				$('#loading').hide();
				openErrorGritter('Error!','Semua Harus Diisi');
			}
			else{
				var data = {
					date : date,
					pic : pic,
					product : product,
					machine : machine,
					shift : shift,
					material_number : material_number,
					process : process,
					punch_number : punch_number,
					die_number : die_number,
					plate_number : plate_number,
					ppl_number : ppl_number,
					dp_number:dp_number,
					dd_number:dd_number,
					snap_number:snap_number,
					lower_number:lower_number,
					upper_number:upper_number,
					half_number:half_number,
					dinsert_number:dinsert_number,
					start_time : start_time,
					end_time : end_time,
					lepas_molding : lepas_molding,
					pasang_molding : pasang_molding,
					process_time : process_time,
					kensa_time : kensa_time,
					electric_supply_time : electric_supply_time,
					data_ok : data_ok,
					punch_value : punch_value,
					die_value : die_value,
					plate_value : plate_value,
					ppl_value : ppl_value,
					dp_value:dp_value,
					dd_value:dd_value,
					snap_value:snap_value,
					lower_value:lower_value,
					upper_value:upper_value,
					half_value:half_value,
					dinsert_value:dinsert_value,
				}
				// $("#end_button").hide();
				// $("#reset_button").show();

				$.post('{{ url("index/press/store") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						alert('Success Save Data');
						location.reload();
					} else {
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error',result.message);
					}
				});
			}
		}

		function timeDifference(date1,date2) {
		    var difference = date1.getTime() - date2.getTime();

		    var daysDifference = Math.floor(difference/1000/60/60/24);
		    difference -= daysDifference*1000*60*60*24

		    var hoursDifference = Math.floor(difference/1000/60/60);
		    difference -= hoursDifference*1000*60*60

		    var minutesDifference = Math.floor(difference/1000/60);
		    difference -= minutesDifference*1000*60

		    var secondsDifference = Math.floor(difference/1000);

		    return addZero(hoursDifference)+':'+addZero(minutesDifference)+':'+addZero(secondsDifference);
		}

		function _timerpasmod(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startpasmod').hide();
				$('#stoppasmod').show();
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerpasmod span.secondpasmod').text();
			        var menit = $('div.timerpasmod span.minutepasmod').text();
			        var jam = $('div.timerpasmod span.hourpasmod').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#pasang_molding').val(waktu);
			        $('#stoppasmod').hide();
			        $('#startpasmod').show();
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerpasmod span.secondpasmod').html(second);
		        $('div.timerpasmod span.minutepasmod').html(minute);
		        $('div.timerpasmod span.hourpasmod').html(hour);
		    }
		}
		 
		var timerpasmod;
		$(document).ready(function(e) 
		{
		    timerpasmod = new _timerpasmod
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerpasmod.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerpasmod.reset(0);
		});

		function _timerlepmod(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startlepmod').hide();
		    	$('#stoplepmod').show();
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerlepmod span.secondlepmod').text();
			        var menit = $('div.timerlepmod span.minutelepmod').text();
			        var jam = $('div.timerlepmod span.hourlepmod').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#stoplepmod').hide();
			        $('#startlepmod').show();
			        $('#lepas_molding').val(waktu);
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerlepmod span.secondlepmod').html(second);
		        $('div.timerlepmod span.minutelepmod').html(minute);
		        $('div.timerlepmod span.hourlepmod').html(hour);
		    }
		}
		 
		var timerlepmod;
		$(document).ready(function(e) 
		{
		    timerlepmod = new _timerlepmod
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerlepmod.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerlepmod.reset(0);
		});

		function _timerelectime(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startelectime').hide();
				$('#stopelectime').show();
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerelectime span.secondelectime').text();
			        var menit = $('div.timerelectime span.minuteelectime').text();
			        var jam = $('div.timerelectime span.hourelectime').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#electric_time').val(waktu);
			        $('#stopelectime').hide();
			        $('#startelectime').show();
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerelectime span.secondelectime').html(second);
		        $('div.timerelectime span.minuteelectime').html(minute);
		        $('div.timerelectime span.hourelectime').html(hour);
		    }
		}
		 
		var timerelectime;
		$(document).ready(function(e) 
		{
		    timerelectime = new _timerelectime
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerelectime.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerelectime.reset(0);
		});

		function _timerkensatime(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startkensatime').hide();
				$('#stopkensatime').show();
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerkensatime span.secondkensatime').text();
			        var menit = $('div.timerkensatime span.minutekensatime').text();
			        var jam = $('div.timerkensatime span.hourkensatime').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#kensa_time').val(waktu);
			        $('#stopkensatime').hide();
			        $('#startkensatime').show();
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerkensatime span.secondkensatime').html(second);
		        $('div.timerkensatime span.minutekensatime').html(minute);
		        $('div.timerkensatime span.hourkensatime').html(hour);
		    }
		}
		 
		var timerkensatime;
		$(document).ready(function(e) 
		{
		    timerkensatime = new _timerkensatime
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerkensatime.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerkensatime.reset(0);
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

	</script>
	@endsection