@extends('layouts.display')
@section('stylesheets')
<meta name="format-detection" content="telephone=no">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#moldingLog > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#moldingLogPasang > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#molding {
		height:150px;
		overflow-y: scroll;
	}

	#molding_pasang {
		height:150px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	input[type=number]:hover {
		background-color: #7dfa8c !important;
	}

	#tableParameter > thead > tr > th{
		color: white;
	}

	#tableParameter > tbody > tr > td:hover,{
		background-color: #7dfa8c !important;
	}

	#tableParameter2 > thead > tr > th{
		color: white;
	}

	#tableParameter2 > tbody > tr > td:hover,{
		background-color: #7dfa8c !important;
	}

	#tableParameter3 > thead > tr > th{
		color: white;
	}

	#tableParameter3 > tbody > tr > td > input:hover,{
		background-color: #7dfa8c !important;
	}

	@media screen and (max-width: 1080px) {
	  #tableParameter {
	    width: 83% !important;
	  }
	  #tableParameter2 {
	    width: 83% !important;
	  }
	  #tableParameter3 {
	    width: 83% !important;
	  }
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	<input type="hidden" id="molding_code" value="">
	<input type="hidden" id="push_block_id_gen" value="">
	
	<div class="row" style="padding-left: 5px;padding-right: 5px">
		<div class="col-xs-6" style="padding-right: 5px; padding-left: 0">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">LEPAS MOLDING<span style="color: red" id="counter"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size:1vw; width: 1%;" id="op_0">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;width: 1%" id="op_1">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;width: 1%" id="op_2">-</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 15px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;"><b>Molding List</b></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								PIC Pasang
							</th>
							<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
							<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Part
							</th>
							<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Shot
							</th>
						</tr>
					</thead>
					<tbody id="moldingLog">
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin_lepas" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part_lepas" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Product</td>
							<td id="color_lepas" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Total Shot</td>
							<td id="total_shot_lepas" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr id="lepastime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"><div class="timerlepas">
					            <span class="hourlepas" id="hourlepas">00</span> h : <span class="minutelepas" id="minutelepas">00</span> m : <span class="secondlepas" id="secondlepas">00</span> s
					            <input type="hidden" id="lepas" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_lepas" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
						<tr id="reasonlepas">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);" id="reason">-
					    	</td>
						</tr>
						<!-- <tr>
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"> KEPUTUSAN
					    	</td>
						</tr> -->
						<tr>
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px" id="div_keputusan">
									KEPUTUSAN
								</div>
								<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px" id="div_maintenance">
									<button id="btn_maintenance" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeDecision('MAINTENANCE')" class="btn btn-warning">MAINTENANCE</button>
								</div>
								<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px" id="div_ok">
									<button id="btn_ok" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeDecision('MASIH OK')" class="btn btn-success">MASIH OK</button>
								</div>
								<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;display: none;" id="div_decision">
									<button id="btn_decision" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelDecision()" class="btn btn-info">TIDAK TAHU</button>
								</div>
							</td>
						</tr>
						<tr id="lepasnote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span class="" id="">Note</span>
					    	</td>
						</tr>
						<tr id="lepasnote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="notelepas" id="notelepas" cols="35" rows="2" style="font-size: 1.2vw;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="start_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="startLepas()" class="btn btn-success">MULAI LEPAS</button>
			</div>
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px">
				<button id="pause_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="pause('LEPAS','PAUSE')" class="btn btn-warning">PAUSE</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="batal_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelLepas()" class="btn btn-danger">BATAL</button>	
			</div>
			<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px">
				<button id="finish_lepas" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishLepas()" class="btn btn-success">SELESAI LEPAS</button>
			</div>
		</div>

		<div class="col-xs-6" style="padding-right: 0; padding-left: 5px">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">PASANG MOLDING <span style="color: red" id="counter"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<!-- <td id="mesin_pasang_pilihan" style="padding:0;background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;">
								<button class="btn btn-danger" onclick="getDataMesin(1);" id="#1">#1</button>
								<button class="btn btn-danger" onclick="getDataMesin(2);" id="#2">#2</button>
								<button class="btn btn-danger" onclick="getDataMesin(3);" id="#3">#3</button>
								<button class="btn btn-danger" onclick="getDataMesin(4);" id="#4">#4</button>
								<button class="btn btn-danger" onclick="getDataMesin(5);" id="#5">#5</button>
								<button class="btn btn-danger" onclick="getDataMesin(6);" id="#6">#6</button>
								<button class="btn btn-danger" onclick="getDataMesin(7);" id="#7">#7</button>
								<button class="btn btn-danger" onclick="getDataMesin(8);" id="#8">#8</button>
								<button class="btn btn-danger" onclick="getDataMesin(9);" id="#9">#9</button>
								<button class="btn btn-danger" onclick="getDataMesin(11);" id="#11">#11</button>
							</td> -->
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="mesin_pasang">-</td>
							<td style="padding: 0px">
								<select id="product" class="" style="width: 100%;font-size: 1.3vw;height: 40px;text-align: center;" data-placeholder="Pilih Product">
									<option value="">Pilih Product</option>
									@foreach($product as $product)
									<option value="{{$product}}">{{$product}}</option>
									@endforeach
								</select>
							</td>
							<td>
								<select id="part" class="" style="width: 100%;font-size: 1.3vw;height: 40px;text-align: center;" data-placeholder="Pilih Part">
									<option value="">Pilih Part</option>
									@foreach($part as $part)
									<option value="{{$part}}">{{$part}}</option>
									@endforeach
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;">
							<span style="color: red"><i id="pesan_pasang" ></i></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding_pasang">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Product
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Part
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Last Counter
							</th>
						</tr>
					</thead>
					<tbody id="moldingLogPasang">
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Product</td>
							<td id="product_pasang" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part_pasang" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin_pasang_list" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Last Counter</td>
							<td id="last_counter_pasang" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr id="pasangtime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"><div class="timerpasang">
					            <span id="hourpasang" class="hourpasang">00</span> h : <span class="minutepasang" id="minutepasang">00</span> m : <span class="secondpasang" id="secondpasang">00</span> s
					            <input type="hidden" id="pasang" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_pasang" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
						<tr id="pasangnote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span>Note</span>
					    	</td>
						</tr>
						<tr id="pasangnote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="notepasang" id="notepasang" cols="35" rows="2" style="font-size: 1.2vw;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="start_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="startPasang()" class="btn btn-success">MULAI PASANG</button>
				<!-- <input type="hidden" id="start_time_pasang"> -->
			</div>
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px">
				<button id="pause_pasang" style="width: 100%; margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="pause('PASANG','PAUSE')" class="btn btn-warning">PAUSE</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="purging_pasang" style="width: 100%; margin-top: 10px; font-size: 25px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="approvalcek('Purging','PURGING')" class="btn btn-default">PURGING</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="setting_robot" style="width: 100%; margin-top: 10px; font-size: 25px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="approvalcek('Setting Robot & Camera','SETTING ROBOT & CAMERA')" class="btn btn-default">SET ROBOT & CAMERA</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="approval_pasang" style="width: 100%;  margin-top: 10px; font-size: 25px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="approvalcek('Approval QA','APPROVAL QA')" class="btn btn-default">APPROVAL QA</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="cek_visual_pasang" style="width: 100%; margin-top: 10px; font-size: 25px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="approvalcek('Cek Visual & Dimensi','CEK VISUAL & DIMENSI')" class="btn btn-default">CEK DIMENSI</button>
			</div>
			<div class="col-xs-12" style="padding-left: 0px;padding-right: 5px">
				<button id="parameter_pasang" disabled="true" style="width: 100%; padding-top: 10px;padding-bottom: 12px; margin-top: 10px; font-size: 25px;  font-weight: bold; border-color: black; color: black; width: 100%" onclick="$('#modalParameter').modal('show');getMesinParameter()" class="btn btn-default">INPUT PARAMETER</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="batal_pasang" style="width: 100%; padding-top: 10px;margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelPasang()" class="btn btn-danger">BATAL</button>
			</div>
			<div class="col-xs-6" style="padding-right: 0px;padding-left: 5px">
				<button id="finish_pasang" style="width: 100%; padding-top: 10px;margin-top: 10px; font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishPasang()" class="btn btn-success">SELESAI PASANG</button>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6" style="padding-right: 5px;padding-left: 0px">
					<!-- <div class="row"> -->
						<button id="change_operator" style="width: 100%;  font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeOperator()" class="btn btn-info">GANTI OPERATOR</button>
					<!-- </div> -->
				</div>
				<div class="col-xs-6" style="padding-left: 5px;padding-right: 0px">
					<!-- <div class="row"> -->
						<button id="change_mesin" style="width: 100%;  font-size: 30px;  font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeMesin()" class="btn btn-primary">GANTI MESIN</button>
					<!-- </div> -->
				</div>
			</div>
		</div>
		<div class="col-xs-6">
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID 1</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_0" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_0" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 2</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_1" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_1" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 3</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_2" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_2" placeholder="Scan ID Card">

					</div>
					<div class="col-xs-12">
						<div class="row">
							<button id="btn_operator" onclick="saveOperator()" class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMesin" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12" id="mesin_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin PASANG Molding</span></center>
							</div>
							<div class="col-xs-12" id="mesin_btn" style="padding-left: 0px;">
								@foreach($mesin as $mesin)
								<div class="col-xs-3" style="padding-top: 5px">
									<center>
										<button class="btn btn-primary" id="{{$mesin}}" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMesin(this.id,'PASANG')">{{$mesin}}</button>
									</center>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="mesin_choice_lepas" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Mesin LEPAS Molding</span></center>
							</div>
							<div class="col-xs-12" id="mesin_btn" style="padding-left: 0px;">
								@foreach($mesin_lepas as $mesin)
								<div class="col-xs-3" style="padding-top: 5px">
									<center>
										<button class="btn btn-warning" id="{{$mesin}}" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMesin(this.id,'LEPAS')">{{$mesin}}</button>
									</center>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="mesin_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;" id="title_mesin">Pilih Mesin</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-primary" id="mesin_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesin2()">
									MESIN
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="row">
							<div class="modal-footer">
								<button onclick="saveMesin()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
									CONFIRM
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalScanApproval" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center> <b style="font-size: 2vw" id="">SCAN ID CARD PE MOLDING & QA</b> </center>
				<input type="hidden" id="typePause">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<center><label for="">PIC PE MOLDING</label></center>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="tag_molding" placeholder="Scan ID Card PIC PE Molding" required><br>
					</div>

					<div class="form-group">
						<center><label for="">Approver QA</label></center>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="tag_qa" placeholder="ID Card Approver QA" required><br>
					</div>

					<div class="col-xs-6" style="padding-left: 0px">
						<button class="btn btn-danger btn-block" style="font-weight: bold;font-size: 20px" data-dismiss="modal">Cancel</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="saveApproval('Approval QA','APPROVAL QA')">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalStatus" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center> <b style="font-size: 2vw" id="statusReason">PAUSE</b> </center>
				<input type="hidden" id="typePause">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<center><label for="">Reason</label></center>
						<!-- <input class="form-control" style="width: 100%; text-align: center;" type="text" id="reasonPause" placeholder="Reason" required><br> -->
						<select class="form-control select2" id="reasonPause" data-placeholder="Pilih Reason" style="width: 100%;text-align: center;">
							<option value="-">Pilih Reason</option>
							<option value="Istirahat">Istirahat</option>
							<option value="Ganti Shift">Ganti Shift</option>
							<option value="Approval Tunggu QA">Approval Tunggu QA</option>
							<option value="Trouble">Trouble</option>
							<option value="No Production">No Production</option>
							<!-- <option value="Cek Visual & Dimensi">Cek Visual & Dimensi</option>
							<option value="Approval QA">Approval QA</option> -->
						</select>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<button class="btn btn-danger btn-block" style="font-weight: bold;font-size: 20px" data-dismiss="modal">Cancel</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="saveStatus()">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalParameter" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" style="width: 1300px">
			<div class="modal-content">
				<div class="modal-header">
					<center style="background-color: green"><h2 style="font-weight: bold;font-size: 25px;color: white;padding: 10px">
						Input Machine Parameter
					</h2></center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<span style="font-weight: bold;font-size: 20px;">Pilih BEST Parameter di bawah</span>
							<select class="form-control" style="width: 100%" data-placeholder="Pilih BEST Parameter" id="param_select" onchange="changeParameter(this.value)">
								
							</select>
							<div class="col-xs-3" style="padding-left: 0px;padding-right: 0px;text-align: center;border: 1px solid black;margin-top: 4px;">
								<span style="font-weight: bold;font-size: 15px;display: inline-block;width:100%;background-color: #e9ffb5">Date</span>
								<br>
								<span style="font-weight: bold;font-size: 18px;" id="date_param"></span>
							</div>
							<div class="col-xs-3" style="padding-left: 0px;padding-right: 0px;text-align: center;border: 1px solid black;margin-top: 4px;">
								<span style="font-weight: bold;font-size: 15px;display: inline-block;width:100%;background-color: #ffd1b5">Product</span>
								<br>
								<span style="font-weight: bold;font-size: 18px;" id="product_param"></span>
							</div>
							<div class="col-xs-3" style="padding-left: 0px;padding-right: 0px;text-align: center;border: 1px solid black;margin-top: 4px;">
								<span style="font-weight: bold;font-size: 15px;display: inline-block;width:100%;background-color: #b5fffb">Molding</span>
								<br>
								<span style="font-weight: bold;font-size: 18px;" id="molding_param"></span>
							</div>
							<div class="col-xs-3" style="padding-left: 0px;padding-right: 0px;text-align: center;border: 1px solid black;margin-top: 4px;">
								<span style="font-weight: bold;font-size: 15px;display: inline-block;width:100%;background-color: #f3b5ff">Person</span>
								<br>
								<span style="font-weight: bold;font-size: 18px;" id="person_param"></span>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll;" id="lastParameter">
							<table class="table table-bordered" id="tableParameter">
								<thead>
									<tr>
										<th colspan="14" style="background-color: orange">PARAMETER INJEKSI</th>
									</tr>
									<tr>
										<th id="tabledesign">NH</th>
										<th id="tabledesign">H1</th>
										<th id="tabledesign">H2</th>
										<th id="tabledesign">H3</th>
										<th id="tabledesign">Dryer</th>
										<th id="tabledesign" colspan="2">MTC</th>
										<th id="tabledesign" colspan="2">Chiller</th>
										<th id="tabledesign">Clamp</th>
										<th id="tabledesign">PH4</th>
										<th id="tabledesign">PH3</th>
										<th id="tabledesign">PH2</th>
										<th id="tabledesign">PH1</th>
										
									</tr>
									<tr>
										<th id="tabledesign" colspan="4">Header / ヒーター</th>
										<th id="tabledesign">ドライヤ</th>
										<th id="tabledesign" colspan="2">温調器</th>
										<th id="tabledesign" colspan="2">チラー</th>
										<th id="tabledesign">クランプ圧</th>
										<th id="tabledesign" colspan="4">Pressure Hold / 保圧</th>
									</tr>
									<tr>
										<th id="tabledesign" colspan="4">°C</th>
										<th id="tabledesign">°C</th>
										<th id="tabledesign">°C</th>
										<th id="tabledesign">MPa / bar</th>
										<th id="tabledesign">°C</th>
										<th id="tabledesign">MPa / bar</th>
										<th id="tabledesign">kN</th>
										<th id="tabledesign" colspan="4">% / MPa</th>
									</tr>
								</thead>
								<tbody id="bodyLastParameter">
								</tbody>
							</table>
						</div>
						<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll;" id="lastParameter">
							<table class="table table-bordered" id="tableParameter2">
								<thead>
									<tr>
										<th id="tabledesign">TRH3</th>
										<th id="tabledesign">TRH2</th>
										<th id="tabledesign">TRH1</th>
										<th id="tabledesign">VH</th>
										<th id="tabledesign">PI</th>
										<th id="tabledesign">LS10 BB</th>
										<th id="tabledesign">VI5</th>
										<th id="tabledesign">VI4</th>
										<th id="tabledesign">VI3</th>
										<th id="tabledesign">VI2</th>
										<th id="tabledesign">VI1</th>
										<th id="tabledesign">LS4</th>
										<th id="tabledesign">LS4D</th>
										<th id="tabledesign">LS4C</th>
										<th id="tabledesign">LS4B</th>
										<th id="tabledesign">LS4A</th>
										<th id="tabledesign">LS5</th>
									</tr>
									<tr>
										<th id="tabledesign" colspan="3">Pressure Hold Time / 保圧時間</th>
										<th id="tabledesign">Velocity PH / 保圧速度</th>
										<th id="tabledesign">射出圧</th>
										<th id="tabledesign">サックバック</th>
										<th id="tabledesign" colspan="5">Velocity Injection / 射出速度</th>
										<th id="tabledesign" colspan="6">Length of Stroke / ストローク</th>
									</tr>
									<tr>
										<th id="tabledesign" colspan="3">Sec</th>
										<th id="tabledesign">mm/sec</th>
										<th id="tabledesign">MPa</th>
										<th id="tabledesign">mm</th>
										<th id="tabledesign" colspan="5">% / mm / sec</th>
										<th id="tabledesign" colspan="6">mm</th>
									</tr>
								</thead>
								<tbody id="bodyLastParameter2">
								</tbody>
							</table>
						</div>
						<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll;" id="lastParameter">
							<table class="table table-bordered" id="tableParameter3">
								<thead>
									<tr>
										<th id="tabledesign">VE1</th>
										<th id="tabledesign">VE2</th>
										<th id="tabledesign">VR</th>
										<th id="tabledesign">LS31A</th>
										<th id="tabledesign">LS31</th>
										<th id="tabledesign">SRN</th>
										<th id="tabledesign">RPM</th>
										<th id="tabledesign">BP</th>
										<th id="tabledesign">TR1 INJ</th>
										<th id="tabledesign">TR3 COOL</th>
										<th id="tabledesign">TR4 INT</th>
										<th id="tabledesign">Min. Cush</th>
										<th id="tabledesign">FILL</th>
										<th id="tabledesign">Circle Time</th>
									</tr>
									<tr>
										<th id="tabledesign" colspan="3">エジェクタ押し/戻し速度</th>
										<th id="tabledesign" colspan="2">エジェクタ押しストローク</th>
										<th id="tabledesign" colspan="2">Screw / スクリュー</th>
										<th id="tabledesign">背圧</th>
										<th id="tabledesign" colspan="3">Timer / タイマー</th>
										<th id="tabledesign">クッション</th>
										<th id="tabledesign"></th>
										<th id="tabledesign">サイクルタイム</th>
									</tr>
									<tr>
										<th id="tabledesign" colspan="3">mm / sec</th>
										<th id="tabledesign" colspan="2">mm</th>
										<th id="tabledesign" colspan="2">% / min<sup>-1</sup></th>
										<th id="tabledesign">MPa</th>
										<th id="tabledesign" colspan="3">Sec</th>
										<th id="tabledesign">mm</th>
										<th id="tabledesign">Sec</th>
										<th id="tabledesign">Sec</th>
									</tr>
								</thead>
								<tbody id="bodyLastParameter3">
								</tbody>
							</table>
						</div>
						<!-- <div class="col-xs-12" style="padding-top: 20px;height: 25vw;">
							<div id="container" style="width: 100%;height: 25vw;">
								
							</div>
						</div> -->
					</div>
					<div class="modal-footer" style="padding: 0px;margin-top: 20px">
						<div class="col-xs-6">
							<button onclick="cancel_parameter()" class="btn btn-danger" style="width: 100%;font-size: 30px;font-weight: bold;">
								CANCEL
							</button>
						</div>
						<div class="col-xs-6">
							<button onclick="create_parameter()" class="btn btn-success" style="width: 100%;font-size: 30px;font-weight: bold;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jqbtk.js") }}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var intervalUpdate;

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var status_cek_visual = null;
	var status_approval_qa = null;
	var status_purging = null;
	var status_setting_robot = null;
	var status_parameter = null;

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#reasonlepas').hide();
		$('#lepasnote').hide();
		$('#lepasnote2').hide();
		$('#lepastime').hide();
		$('#finish_lepas').hide();
		$('#div_decision').hide();
		$('#div_ok').hide();
		$('#div_maintenance').hide();
		$('#div_keputusan').hide();

		// $('#mesin_pasang').hide();
		$('#pasangnote').hide();
		$('#pasangnote2').hide();
		$('#pasangtime').hide();
		$('#finish_pasang').hide();
		$('#batal_pasang').hide();
		$('#batal_lepas').hide();

		$('#mesin_fix').hide();

		$('#operator_0').val('');
		$('#operator_1').val('');
		$('#operator_2').val('');

		$('#product').val('').trigger('change');
		$('#part').val('').trigger('change');

		status_cek_visual = null;
		status_approval_qa = null;
		status_purging = null;
		status_setting_robot = null;
		status_parameter = null;

		$('#approval_pasang').prop('class','btn btn-default');

		$('#molding_code').val('');
		setInterval(setTime, 1000);
		$('#notelepas').keyboard();
		$('#notepasang').keyboard();
		$('#parameter_pasang').prop('disabled',true);

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator_0').focus();
	});

	$('#operator_0').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_0").val().length >= 8){
				$('#loading').show();
				var data = {
					employee_id : $("#operator_0").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_0').val(result.employee.name);
						$('#op_0').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_0').val(result.employee.employee_id);
						$('#operator_0').prop('disabled',true);
						$('#operator_1').focus();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_0').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_0").val("");
			}			
		}
	});

	$('#tag_molding').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_molding").val().length >= 8){
				$('#loading').show();
				var data = {
					employee_id : $("#tag_molding").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Scan Berhasil');
						$('#tag_molding').val(result.employee.employee_id+'-'+result.employee.name);
						$('#tag_qa').focus();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_molding').val('');
					}
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#tag_molding").val("");
			}			
		}
	});

	$('#tag_qa').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_qa").val().length >= 8){
				$('#loading').show();
				var data = {
					employee_id : $("#tag_qa").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success!', 'Scan Berhasil');
						$('#tag_qa').val(result.employee.employee_id+'-'+result.employee.name);
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_qa').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#tag_qa").val("");
			}			
		}
	});

	$('#operator_1').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_1").val().length >= 8){
				$('#loading').show();
				var data = {
					employee_id : $("#operator_1").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success!', result.message);
						$('#operator_1').val(result.employee.name);
						$('#op_1').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_1').val(result.employee.employee_id);
						$('#operator_1').prop('disabled',true);
						$('#operator_2').focus();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_1').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_1").val("");
			}			
		}
	});

	$('#operator_2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_2").val().length >= 8){
				$('#loading').show();
				var data = {
					employee_id : $("#operator_2").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success!', result.message);
						$('#operator_2').val(result.employee.name);
						$('#op_2').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_2').val(result.employee.employee_id);
						$('#operator_2').prop('disabled',true);
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_2').val('');
					}
				});
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_2").val("");
			}			
		}
	});

	function changeParameter(values) {
		$('#loading').show();
		var tableData = "";
		var tableData2 = "";
		var tableData3 = "";
		$('#bodyLastParameter').append().empty();
		$('#bodyLastParameter2').append().empty();
		$('#bodyLastParameter3').append().empty();

		for(var i = 0; i< param_all.length;i++){
			if (param_all[i].date == values.split('_')[0] && 
				param_all[i].part == values.split('_')[1] && 
				param_all[i].molding == values.split('_')[2] && 
				param_all[i].person == values.split('_')[3]) {

				if (param_all[i].detail != null) {
					tableData += '<tr>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control numpad" readonly value="'+param_all[i].detail.nh+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control numpad" readonly value="'+param_all[i].detail.h1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control numpad" readonly value="'+param_all[i].detail.h2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control numpad" readonly value="'+param_all[i].detail.h3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control numpad" readonly value="'+param_all[i].detail.dryer+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control numpad" readonly value="'+param_all[i].detail.mtc_temp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control numpad" readonly value="'+param_all[i].detail.mtc_press+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control numpad" readonly value="'+param_all[i].detail.chiller_temp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control numpad" readonly value="'+param_all[i].detail.chiller_press+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control numpad" readonly value="'+param_all[i].detail.clamp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control numpad" readonly value="'+param_all[i].detail.ph4+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control numpad" readonly value="'+param_all[i].detail.ph3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control numpad" readonly value="'+param_all[i].detail.ph2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control numpad" readonly value="'+param_all[i].detail.ph1+'"></td>';
					tableData += '</tr>';

					tableData2 += '<tr>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control numpad" readonly value="'+param_all[i].detail.trh3+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control numpad" readonly value="'+param_all[i].detail.trh2+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control numpad" readonly value="'+param_all[i].detail.trh1+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control numpad" readonly value="'+param_all[i].detail.vh+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control numpad" readonly value="'+param_all[i].detail.pi+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control numpad" readonly value="'+param_all[i].detail.ls10+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control numpad" readonly value="'+param_all[i].detail.vi5+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control numpad" readonly value="'+param_all[i].detail.vi4+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control numpad" readonly value="'+param_all[i].detail.vi3+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control numpad" readonly value="'+param_all[i].detail.vi2+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control numpad" readonly value="'+param_all[i].detail.vi1+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control numpad" readonly value="'+param_all[i].detail.ls4+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control numpad" readonly value="'+param_all[i].detail.ls4d+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control numpad" readonly value="'+param_all[i].detail.ls4c+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control numpad" readonly value="'+param_all[i].detail.ls4b+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control numpad" readonly value="'+param_all[i].detail.ls4a+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control numpad" readonly value="'+param_all[i].detail.ls5+'"></td>';
					tableData2 += '</tr>';

					tableData3 += '<tr>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control numpad" readonly value="'+param_all[i].detail.ve1+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control numpad" readonly value="'+param_all[i].detail.ve2+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control numpad" readonly value="'+param_all[i].detail.vr+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control numpad" readonly value="'+param_all[i].detail.ls31a+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control numpad" readonly value="'+param_all[i].detail.ls31+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control numpad" readonly value="'+param_all[i].detail.srn+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control numpad" readonly value="'+param_all[i].detail.rpm+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control numpad" readonly value="'+param_all[i].detail.bp+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control numpad" readonly value="'+param_all[i].detail.tr1inj+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control numpad" readonly value="'+param_all[i].detail.tr3cool+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control numpad" readonly value="'+param_all[i].detail.tr4int+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control numpad" readonly value="'+param_all[i].detail.mincush+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control numpad" readonly value="'+param_all[i].detail.fill+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control numpad" readonly value="'+param_all[i].detail.circletime+'"></td>';
					tableData3 += '</tr>';
				}else{
					tableData += '<tr>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control numpad" readonly value=""></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control numpad" readonly value=""></td>';
					tableData += '</tr>';

					tableData2 += '<tr>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control numpad" readonly value=""></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control numpad" readonly value=""></td>';
					tableData2 += '</tr>';

					tableData3 += '<tr>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control numpad" readonly value=""></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control numpad" readonly value=""></td>';
					tableData3 += '</tr>';
				}
				
				$('#bodyLastParameter').append(tableData);
				$('#bodyLastParameter2').append(tableData2);
				$('#bodyLastParameter3').append(tableData3);

				$('#date_param').html(param_all[i].date);
				$('#product_param').html(param_all[i].part);
				$('#molding_param').html(param_all[i].molding);
				$('#person_param').html(param_all[i].person);

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
			}
		}
		$('#loading').hide();
	}

	var param_all;

	function getMesinParameter() {
		// $('#mesin_parameter_fix').show();
		// $('#mesin_parameter').hide();
		// $('#mesin_parameter_fix2').html(mesin_parameter);

		data = {
			mesin : '#'+$('#mesin_pasang').text().split(' ')[1],
			part_name: $('#product').val(),
			part_type: $('#part').val(),
			remark: 'First Shot Approval'
		}

		$.get('{{ url("index/fetch_mesin_parameter_new") }}', data, function(result, status, xhr){
			if(result.status){
				var tableData = "";
				var tableData2 = "";
				var tableData3 = "";
				$('#bodyLastParameter').append().empty();
				$('#bodyLastParameter2').append().empty();
				$('#bodyLastParameter3').append().empty();
				var option_param = '';

				var molding = [];
				var part = [];
				var ng = [];

				var nh = [];
				var h1 = [];
				var h2 = [];
				var h3 = [];
				var dryer = [];
				var mtc_temp = [];
				var mtc_press = [];
				var chiller_temp = [];
				var chiller_press = [];
				var clamp = [];
				var ph4 = [];
				var ph3 = [];
				var ph2 = [];
				var ph1 = [];
				var trh3 = [];
				var trh2 = [];
				var trh1 = [];
				var vh = [];
				var pi = [];
				var ls10 = [];
				var vi5 = [];
				var vi4 = [];
				var vi3 = [];
				var vi2 = [];
				var vi1 = [];
				var ls4 = [];
				var ls4d = [];
				var ls4c = [];
				var ls4b = [];
				var ls4a = [];
				var ls5 = [];
				var ve1 = [];
				var ve2 = [];
				var vr = [];
				var ls31a = [];
				var ls31 = [];
				var srn = [];
				var rpm = [];
				var bp = [];
				var tr1inj = [];
				var tr3cool = [];
				var tr4int = [];
				var mincush = [];
				var fill = [];
				var circletime = [];

				var series = [];
				var categories = [];

				$('#param_select').html('');
				if (result.detail.length == 0 || result.detail[0] == null) {
					tableData += '<tr>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control numpad"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control numpad"></td>';
					tableData += '</tr>';

					tableData2 += '<tr>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control numpad"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control numpad"></td>';
					tableData2 += '</tr>';

					tableData3 += '<tr>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control numpad"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control numpad"></td>';
					tableData3 += '</tr>';

				}else{
					tableData += '<tr>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control numpad" value="'+result.detail[0]['detail'].nh+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control numpad" value="'+result.detail[0]['detail'].h1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control numpad" value="'+result.detail[0]['detail'].h2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control numpad" value="'+result.detail[0]['detail'].h3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control numpad" value="'+result.detail[0]['detail'].dryer+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control numpad" value="'+result.detail[0]['detail'].mtc_temp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control numpad" value="'+result.detail[0]['detail'].mtc_press+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control numpad" value="'+result.detail[0]['detail'].chiller_temp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control numpad" value="'+result.detail[0]['detail'].chiller_press+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control numpad" value="'+result.detail[0]['detail'].clamp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control numpad" value="'+result.detail[0]['detail'].ph4+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control numpad" value="'+result.detail[0]['detail'].ph3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control numpad" value="'+result.detail[0]['detail'].ph2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control numpad" value="'+result.detail[0]['detail'].ph1+'"></td>';
					tableData += '</tr>';

					tableData2 += '<tr>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control numpad" value="'+result.detail[0]['detail'].trh3+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control numpad" value="'+result.detail[0]['detail'].trh2+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control numpad" value="'+result.detail[0]['detail'].trh1+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control numpad" value="'+result.detail[0]['detail'].vh+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control numpad" value="'+result.detail[0]['detail'].pi+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control numpad" value="'+result.detail[0]['detail'].ls10+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control numpad" value="'+result.detail[0]['detail'].vi5+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control numpad" value="'+result.detail[0]['detail'].vi4+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control numpad" value="'+result.detail[0]['detail'].vi3+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control numpad" value="'+result.detail[0]['detail'].vi2+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control numpad" value="'+result.detail[0]['detail'].vi1+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control numpad" value="'+result.detail[0]['detail'].ls4+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control numpad" value="'+result.detail[0]['detail'].ls4d+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control numpad" value="'+result.detail[0]['detail'].ls4c+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control numpad" value="'+result.detail[0]['detail'].ls4b+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control numpad" value="'+result.detail[0]['detail'].ls4a+'"></td>';
					tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control numpad" value="'+result.detail[0]['detail'].ls5+'"></td>';
					tableData2 += '</tr>';

					tableData3 += '<tr>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control numpad" value="'+result.detail[0]['detail'].ve1+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control numpad" value="'+result.detail[0]['detail'].ve2+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control numpad" value="'+result.detail[0]['detail'].vr+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control numpad" value="'+result.detail[0]['detail'].ls31a+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control numpad" value="'+result.detail[0]['detail'].ls31+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control numpad" value="'+result.detail[0]['detail'].srn+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control numpad" value="'+result.detail[0]['detail'].rpm+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control numpad" value="'+result.detail[0]['detail'].bp+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control numpad" value="'+result.detail[0]['detail'].tr1inj+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control numpad" value="'+result.detail[0]['detail'].tr3cool+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control numpad" value="'+result.detail[0]['detail'].tr4int+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control numpad" value="'+result.detail[0]['detail'].mincush+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control numpad" value="'+result.detail[0]['detail'].fill+'"></td>';
					tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control numpad" value="'+result.detail[0]['detail'].circletime+'"></td>';
					tableData3 += '</tr>';

					option_param += '<option value="">Pilih BEST Parameter</option>';
					for(var i = 0; i < result.detail.length;i++){
						var datas = [];
						option_param += '<option value="'+result.detail[i]['date']+'_'+result.detail[i]['part']+'_'+result.detail[i]['molding']+'_'+result.detail[i]['person']+'_'+result.detail[i]['qty_ng']+'">'+result.detail[i]['date']+' - '+result.detail[i]['part']+' - '+result.detail[i]['molding']+' - '+result.detail[i]['person']+' - NG = '+result.detail[i]['qty_ng']+'</option>';
						if (result.detail[i]['detail'] != null) {
							datas.push((parseFloat(result.detail[i]['detail'].nh) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].h1) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].h2) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].h3) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].dryer) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].mtc_temp) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].mtc_press) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].chiller_temp) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].chiller_press) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].clamp) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ph4) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ph3) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ph2) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ph1) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].trh3) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].trh2) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].trh1) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vh) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].pi) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls10) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vi5) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vi4) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vi3) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vi2) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vi1) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls4) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls4d) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls4c) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls4b) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls4a) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls5) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ve1) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ve2) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].vr) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls31a) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].ls31) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].srn) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].rpm) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].bp) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].tr1inj) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].tr3cool) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].tr4int) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].mincush) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].fill) || 0));
							datas.push((parseFloat(result.detail[i]['detail'].circletime) || 0));

							series.push({
						        name: result.detail[i]['date']+'-NG='+result.detail[i]['qty_ng'],
						        data: datas
						    });
						}
					}
				}

				categories.push('Header NH');
				categories.push('Header H1');
				categories.push('Header H2');
				categories.push('Header H3');
				categories.push('Dryer');
				categories.push('Thermostat Temp');
				categories.push('Thermostat Pressure');
				categories.push('Chiller Temp');
				categories.push('Chiller Pressure');
				categories.push('Clamp Pressure');
				categories.push('Pressure Hold 4');
				categories.push('Pressure Hold 3');
				categories.push('Pressure Hold 2');
				categories.push('Pressure Hold 1');
				categories.push('Pressure Hold Time 3');
				categories.push('Pressure Hold Time 2');
				categories.push('Pressure Hold Time 1');
				categories.push('Velocity PH');
				categories.push('Injection Pressure');
				categories.push('Sackback LS10 BB');
				categories.push('Velocity Injection 5');
				categories.push('Velocity Injection 4');
				categories.push('Velocity Injection 3');
				categories.push('Velocity Injection 2');
				categories.push('Velocity Injection 1');
				categories.push('Length of Stroke 4');
				categories.push('Length of Stroke 4D');
				categories.push('Length of Stroke 4C');
				categories.push('Length of Stroke 4B');
				categories.push('Length of Stroke 4A');
				categories.push('Length of Stroke 5');
				categories.push('Ejector Push VE1');
				categories.push('Ejector Push VE2');
				categories.push('Ejector Push VR');
				categories.push('Ejector Push Stroke LS31A');
				categories.push('Ejector Push Stroke LS31');
				categories.push('Screw SRN');
				categories.push('Screw RPM');
				categories.push('Back Pressure');
				categories.push('Timer TR1 INJ');
				categories.push('Timer TR3 COOL');
				categories.push('Timer TR4 INT');
				categories.push('Min. Cushion');
				categories.push('FILL');
				categories.push('Circle Time');


				$('#param_select').append(option_param);
				$('#bodyLastParameter').append(tableData);
				$('#bodyLastParameter2').append(tableData2);
				$('#bodyLastParameter3').append(tableData3);

				// if (series.length > 0) {
					// Highcharts.chart('container', {
					//     chart: {
					//         zoomType: 'xy',
					//     },
					//     title: {
					//         text: 'Injection Prameter',
					//         style:{
					//         	fontWeight:'bold',
					//         	fontSize:'13px'
					//         }
					//     },
					//     subtitle: {
					//         text: $('#mesin_pasang').text()+' - '+$('#product').val()+' '+$('#part').val()
					//     },
					//     xAxis: [{
					//         categories: categories,
					//         crosshair: true,
					//     }],
					//     yAxis: [{ 
					//         labels: {
					//             format: '{value}',
					//             style: {
					//                 color: '#fff'
					//             }
					//         },
					//         title: {
					//             text: 'Parameter Value',
					//             style: {
					//                 color: '#fff'
					//             }
					//         }
					//     },],
					//     credits:{
					//     	enabled:false
					//     },
					//     tooltip: {
					//         shared: true
					//     },
					//     legend: {
					//         enabled:true
					//     },
					//     plotOptions: {
					// 		series:{
					// 			cursor: 'pointer',
				 //                point: {
				 //                  events: {
				 //                    click: function () {
				 //                    	// showModalDetail(this.category);
				 //                    }
				 //                  }
				 //                },
					// 			dataLabels: {
					// 				enabled: true,
					// 				format: '{point.y:.2f}',
					// 				style:{
					// 					fontSize: '11px'
					// 				}
					// 			},
					// 			animation: false,
					// 			pointPadding: 0.93,
					// 			groupPadding: 0.93,
					// 			cursor: 'pointer',
					// 			lineWidth: 1
					// 		}
					// 	},
					//     series: series
					// });
				// }
				if (result.detail.length > 0 && result.detail[0] != null) {
					$('#date_param').html(result.detail[0]['date']);
					$('#product_param').html(result.detail[0]['product']);
					$('#molding_param').html(result.detail[0]['molding']);
					$('#person_param').html(result.detail[0]['person']);
				}

				param_all = result.detail;

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
			}
			else{
				tableData += '<tr>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control numpad" value=""></td>';
				tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control numpad" value=""></td>';
				tableData += '</tr>';

				tableData2 += '<tr>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control numpad" value=""></td>';
				tableData2 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control numpad" value=""></td>';
				tableData2 += '</tr>';

				tableData3 += '<tr>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control numpad" value=""></td>';
				tableData3 += '<td id="tabledesign" style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control numpad" value=""></td>';
				tableData3 += '</tr>';

				$('#bodyLastParameter').append(tableData);
				$('#bodyLastParameter2').append(tableData2);
				$('#bodyLastParameter3').append(tableData3);

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});
			}
		});
	}

	function saveOperator() {
		$('#modalOperator').modal('hide');
		$('#modalMesin').modal('show');
	}

	function saveMesin() {
		if ($('#mesin_fix2').text() == 'MESIN') {
			alert('Pilih Mesin');
		}else{
			$('#mesin_pasang').html($('#mesin_fix2').text());
			getMoldingLogPasang($('#mesin_fix2').text());
			$('#modalMesin').modal('hide');
			getMoldingLog($('#mesin_fix2').text());
			get_history_temp($('#mesin_fix2').text());
		}
	}

	function getMesin(value,type) {
		$('#mesin_fix').show();
		$('#mesin_fix2').html(value);
		if (type == 'PASANG') {
			$('#title_mesin').html('ANDA AKAN MEMASANG MOLDING');
			$('#mesin_fix2').removeClass();
			$('#mesin_fix2').addClass('btn btn-primary');
		}else{
			$('#title_mesin').html('ANDA AKAN MELEPAS MOLDING');
			$('#mesin_fix2').removeClass();
			$('#mesin_fix2').addClass('btn btn-warning');
		}
		$('#mesin_choice').hide();
		$('#mesin_choice_lepas').hide();
	}

	function changeMesin2() {
		$('#title_mesin').html('MOLDING');
		$('#mesin_fix').hide();
		$('#mesin_choice').show();
		$('#mesin_choice_lepas').show();
		$('#mesin_fix2').html("MESIN");
	}

	function changeDecision(value) {
		$('#div_maintenance').hide();
		$('#div_ok').hide();
		$('#div_decision').show();
		$('#btn_decision').html(value);
	}

	function cancelDecision() {
		$('#div_maintenance').show();
		$('#div_ok').show();
		$('#div_decision').hide();
		$('#btn_decision').html("TIDAK TAHU");
	}

	function getDataMesin(nomor_mesin) {
		$('#mesin_pasang').html('Mesin ' + nomor_mesin);
		$('#mesin_pasang_pilihan').hide();
		$('#mesin_pasang').show();
		getMoldingLogPasang('Mesin ' + nomor_mesin);
	}

	function changeMesin() {
		$('#mesin_pasang').html('-');
		// $('#mesin_pasang').hide();
		// $('#moldingLogPasang').html("");
		$('#modalMesin').modal("show");
		$('#mesin_choice').show();
		$('#mesin_choice_lepas').show();
		$('#mesin_fix').hide();
		cancelAll();
	}

	function changeOperator() {
		location.reload();
	}

	function create_parameter() {
		$('#loading').show();
		if ($('#input_circletime').val() == '') {
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!','Parameter harus diisi.');
			return false;
		}
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		var pic = [];

		if (pic_1 != "-") {
			pic.push(pic_1);
		}

		if (pic_2 != "-") {
			pic.push(pic_2);
		}

		if (pic_3 != "-") {
			pic.push(pic_3);
		}

		var data = {
			molding_code:$('#molding_code').val(),
			pic_check:pic.join(),
			push_block_code : 'First Shot Approval',
			push_block_id_gen : 'FSA_'+'{{date("Y-m-d H:i:s")}}'+'_'+$('#product_pasang').text()+'_'+pic,
			reason : 'MOLD CHANGE',
			check_date : '{{date("Y-m-d H:i:s")}}',
			product_type : $('#product').val()+' '+$('#part').val(),
			mesin : '#'+$("#mesin_pasang").text().split(' ')[1],
			molding : $('#part_pasang').text(),
			nh : $('#input_nh').val(),
			h1 : $('#input_h1').val(),
			h2 : $('#input_h2').val(),
			h3 : $('#input_h3').val(),
			dryer : $('#input_dryer').val(),
			mtc_temp : $('#input_mtc_temp').val(),
			mtc_press : $('#input_mtc_press').val(),
			chiller_temp : $('#input_chiller_temp').val(),
			chiller_press : $('#input_chiller_press').val(),
			clamp : $('#input_clamp').val(),
			ph4 : $('#input_ph4').val(),
			ph3 : $('#input_ph3').val(),
			ph2 : $('#input_ph2').val(),
			ph1 : $('#input_ph1').val(),
			trh3 : $('#input_trh3').val(),
			trh2 : $('#input_trh2').val(),
			trh1 : $('#input_trh1').val(),
			vh : $('#input_vh').val(),
			pi : $('#input_pi').val(),
			ls10 : $('#input_ls10').val(),
			vi5 : $('#input_vi5').val(),
			vi4 : $('#input_vi4').val(),
			vi3 : $('#input_vi3').val(),
			vi2 : $('#input_vi2').val(),
			vi1 : $('#input_vi1').val(),
			ls4 : $('#input_ls4').val(),
			ls4d : $('#input_ls4d').val(),
			ls4c : $('#input_ls4c').val(),
			ls4b : $('#input_ls4b').val(),
			ls4a : $('#input_ls4a').val(),
			ls5 : $('#input_ls5').val(),
			ve1 : $('#input_ve1').val(),
			ve2 : $('#input_ve2').val(),
			vr : $('#input_vr').val(),
			ls31a : $('#input_ls31a').val(),
			ls31 : $('#input_ls31').val(),
			srn : $('#input_srn').val(),
			rpm : $('#input_rpm').val(),
			bp : $('#input_bp').val(),
			tr1inj : $('#input_tr1inj').val(),
			tr3cool : $('#input_tr3cool').val(),
			tr4int : $('#input_tr4int').val(),
			mincush : $('#input_mincush').val(),
			fill : $('#input_fill').val(),
			circletime : $('#input_circletime').val()
		}

		// console.log(data);

		$.post('{{ url("index/push_block_recorder/create_parameter") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#modalParameter').modal('hide');
				openSuccessGritter('Success', result.message);
				status_parameter = '{{date("Y-m-d H:i:s")}}';
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function cancel_parameter() {
		$("#modalParameter").modal('hide');
	}

	function getMoldingLog(mesin){
		$('#loading').show();
		var data = {
			mesin:mesin
		}
		$.get('{{ url("get/injeksi/get_molding") }}', data, function(result, status, xhr){
			if(result.status){

				var moldingLog = '';
				$('#moldingLog').html("");
				var no = 1;
				var color ="";
				$.each(result.datas, function(key, value) {
					if (no % 2 === 0 ) {
							color = 'style="background-color: #fffcb7;font-size: 20px;"';
						} else {
							color = 'style="background-color: #ffd8b7;font-size: 20px;"';
						}
					if (value.shot >= 15000) {
						color = 'style="background-color: #ff3030;font-size: 20px;color:white"';
					}
					moldingLog += '<tr onclick="fetchCount(\''+value.mesin+'\',\''+value.part+'\',\''+value.product+'\',\''+value.shot+'\')" style="padding-top:5px;padding-bottom:5px;">';
					moldingLog += '<td '+color+'>'+value.pic+'</td>';
					moldingLog += '<td '+color+'>'+value.mesin+'</td>';
					moldingLog += '<td '+color+'>'+value.part+'</td>';
					moldingLog += '<td '+color+'>'+value.shot+'</td>';
					// moldingLog += '<td '+color+'>'+value.end_time+'</td>';
					
					moldingLog += '</tr>';				
				no++;
				});
				$('#moldingLog').append(moldingLog);

				// $('#statusLog').text(result.log[0].status);
				

				openSuccessGritter('Success!', result.message);
				$('#loading').hide();
				
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function getMoldingLogPasang(mesin){
		$('#loading').show();
		var data = {
			mesin : mesin,
		}
		$.get('{{ url("get/injeksi/get_molding_pasang") }}', data, function(result, status, xhr){
			if(result.status){
				$('#pesan_pasang').html(result.pesan);

				if (result.pesan.length == 0) {
					var moldingLogPasang = '';
					// $('#moldingLogPasang').html("");
					var no = 1;
					var color ="";
					$.each(result.datas, function(key, value) {
						if (no % 2 === 0 ) {
								color = 'style="background-color: #fffcb7;font-size: 25px;padding-top:5px;padding-bottom:5px;"';
							} else {
								color = 'style="background-color: #ffd8b7;font-size: 25px;padding-top:5px;padding-bottom:5px;"';
							}
						moldingLogPasang += '<tr onclick="fetchCountPasang(\''+value.id+'\',\''+value.product+'\',\''+value.part+'\')">';
						moldingLogPasang += '<td '+color+'>'+value.product+'</td>';
						moldingLogPasang += '<td '+color+'>'+value.part+'</td>';
						moldingLogPasang += '<td '+color+'>'+value.mesin+'</td>';
						moldingLogPasang += '<td '+color+'>'+value.last_counter+'</td>';
						// moldingLogPasang += '<td '+color+'>'+value.end_time+'</td>';
						
						moldingLogPasang += '</tr>';				
					no++;
					});
					$('#moldingLogPasang').append(moldingLogPasang);
					$('#loading').hide();
				}
				$('#loading').hide();

				// $('#statusLog').text(result.log[0].status);
				

				// openSuccessGritter('Success!', result.message);
				
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function fetchCount(mesin,part,product,shot){
		// var data = {
		// 	id : id,
		// }
		// $.get('{{ url("fetch/injeksi/fetch_molding") }}', data, function(result, status, xhr){
		// 	if(result.status){
				$('#mesin_lepas').html(mesin);
				$('#part_lepas').html(part);
				$('#color_lepas').html(product);
				$('#total_shot_lepas').html(shot);
		// 	}
		// 	else{
		// 		alert('Attempt to retrieve data failed');
		// 	}
		// });
	}

	function fetchCountPasang(id,product,part){
		var data = {
			id : id,
		}
		$.get('{{ url("fetch/injeksi/fetch_molding_pasang") }}', data, function(result, status, xhr){
			if(result.status){
				$('#product_pasang').html(result.datas.product);
				$('#part_pasang').html(result.datas.part);
				$('#mesin_pasang_list').html(result.datas.mesin);
				$('#last_counter_pasang').html(result.datas.last_counter);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
		// $('#product').val(product).trigger('change');
		$('#part').val(product).trigger('change');

		if ($('#product').val() == null) {
			$('#product').val('').trigger('change');
		}

		if ($('#part').val() == null) {
			$('#part').val('').trigger('change');
		}
	}

	function startLepas() {
		if ($('#mesin_lepas').text() == '') {
			alert('Pilih Data Molding Yang Akan Dilepas.');
		}else{
			// $('#reasonlepas').show();
			$('#lepasnote').show();
			$('#lepasnote2').show();
			$('#lepastime').show();
			$('#finish_lepas').show();
			$('#batal_lepas').show();
			$('#start_lepas').hide();
			$('#div_ok').show();
			$('#div_maintenance').show();
			$('#div_keputusan').show();
			intervalUpdate = setInterval(update_history_temp,60000);
			store_history_temp('LEPAS');
		}
	}

	function cancelAll() {
		$('#finish_lepas').hide();
		$('#lepasnote').hide();
		$('#lepasnote2').hide();
		$('#batal_lepas').hide();
		$('#lepastime').hide();
		$('#start_lepas').show();
		$('#secondlepas').html("00");
        $('#minutelepas').html("00");
        $('#hourlepas').html("00");
        $('#part_lepas').html("");
        $('#total_shot_lepas').html("");
        $('#color_lepas').html("");
        $('#mesin_lepas').html("");
        $('#reasonlepas').hide();
        $('#div_ok').hide();
        $('#div_maintenance').hide();
        $('#div_keputusan').hide();
        $('#div_decision').hide();

        $('#finish_pasang').hide();
		$('#pasangnote').hide();
		$('#pasangnote2').hide();
		$('#batal_pasang').hide();
		$('#pasangtime').hide();
		$('#start_pasang').show();
		$('#secondpasang').html("00");
        $('#minutepasang').html("00");
        $('#hourpasang').html("00");
        $('#product_pasang').html("");
        $('#part_pasang').html("");
        $('#mesin_pasang_list').html("");
        $('#last_counter_pasang').html("");
        $('#moldingLogPasang').html("");
        $('#mesin_pasang_pilihan').show();

        $('#molding_code').val('');
        // $('#mesin_pasang').hide();
	}

	function cancelLepas() {
		$('#loading').show();
		var pic = $('#op2').text();
		var mesin = $('#mesin_lepas').text();
		var part = $('#part_lepas').text();
		var data = {
			pic : pic,
			mesin : mesin,
			part : part,
			type : 'LEPAS',
		}

		$.post('{{ url("index/injeksi/cancel_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Setup Molding Canceled');
				getMoldingLog($('#mesin_fix2').text());
				$('#finish_lepas').hide();
				$('#lepasnote').hide();
				$('#lepasnote2').hide();
				$('#batal_lepas').hide();
				$('#lepastime').hide();
				$('#start_lepas').show();
				$('#secondlepas').html("00");
		        $('#minutelepas').html("00");
		        $('#hourlepas').html("00");
		        $('#part_lepas').html("");
		        $('#total_shot_lepas').html("");
		        $('#color_lepas').html("");
		        $('#mesin_lepas').html("");
		        $('#reasonlepas').hide();
		        $('#div_ok').hide();
		        $('#div_maintenance').hide();
		        $('#div_keputusan').hide();
		        $('#div_decision').hide();
		        $('#loading').hide();
			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error','Cancel Failed');
			}
		});
	}

	function finishLepas() {
		$('#loading').show();
		clearInterval(intervalUpdate);
		count = false;
		var detik = $('div.timerlepas span.secondlepas').text();
        var menit = $('div.timerlepas span.minutelepas').text();
        var jam = $('div.timerlepas span.hourlepas').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#lepas').val(waktu);

		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();
		var mesin = $('#mesin_lepas').text();
		var part = $('#part_lepas').text();
		var color = $('#color_lepas').text();
		var total_shot = $('#total_shot_lepas').text();
		var start_time = $('#start_time_lepas').val();
		var end_time = getActualFullDate();
		var running_time = $('#lepas').val();
		var notelepas = $('#notelepas').val();
		var reason = $('#reason').text();
		var decision = $('#btn_decision').text();
		var molding_code = $('#molding_code').val();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		if (reason == '-' || decision == 'TIDAK TAHU') {
			alert('Semua Data Harus Diisi');
			$('#loading').hide();
		}else{
			var data = {
				mesin : mesin,
				type : 'LEPAS',
				pic_1 : pic_1,
				pic_2 : pic_2,
				pic_3 : pic_3,
				reason : reason,
				part : part,
				color : color,
				total_shot : total_shot,
				start_time : start_time,
				end_time : end_time,
				running_time : running_time,
				notelepas : notelepas,
				decision : decision,
				molding_code : molding_code,
			}

			$.post('{{ url("index/injeksi/store_history_molding") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','History Molding has been created');
					// reset();
					$('#finish_lepas').hide();
					$('#lepastime').hide();
					$('#lepasnote').hide();
					$('#lepasnote2').hide();
					$('#batal_lepas').hide();
					$('#start_lepas').show();
					$('#secondlepas').html("00");
			        $('#minutelepas').html("00");
			        $('#hourlepas').html("00");
			        $('#part_lepas').html("");
			        $('#total_shot_lepas').html("");
			        $('#color_lepas').html("");
			        $('#mesin_lepas').html("");
			        $('#reasonlepas').hide();
			        $('#div_ok').hide();
			        $('#div_maintenance').hide();
			        $('#div_keputusan').hide();
			        $('#div_decision').hide();
					getMoldingLog($('#mesin_fix2').text());
					getMoldingLogPasang($('#mesin_fix2').text());
					$('#loading').hide();
					location.reload();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create History Molding Temp Failed');
					$('#loading').hide();
				}
			});
		}
	}

	function startPasang() {
		console.log($('#product').val());
		if ($('#mesin_pasang_list').text() == '' || $('#mesin_pasang').text() == '-') {
			alert('Pilih Data Molding Yang Akan Dipasang.');
		}else if ($('#pesan_pasang').text() != '') {
			alert('Mesin Sudah Terpasang Molding. Silahkan Pilih Mesin Lain.');
		}else if ($('#product').val() == '' || $('#part').val() == '' || $('#product').val() == null || $('#part').val() == null) {
			alert('Pilih Product dan Part');
		}
		else{
			$('#pasangnote').show();
			$('#pasangnote2').show();
			$('#pasangtime').show();
			$('#finish_pasang').show();
			$('#start_pasang').hide();
			$('#batal_pasang').show();
			intervalUpdate = setInterval(update_history_temp,60000);
			store_history_temp('PASANG');
		}
	}

	function cancelPasang() {
		$('#loading').show();
		var pic = $('#op2').text();
		var mesin = $('#mesin_pasang').text();
		var data = {
			pic : pic,
			mesin : mesin,
			type : 'PASANG',
		}

		$.post('{{ url("index/injeksi/cancel_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Setup Molding Canceled');
				$('#finish_pasang').hide();
				$('#pasangnote').hide();
				$('#pasangnote2').hide();
				$('#batal_pasang').hide();
				$('#pasangtime').hide();
				$('#start_pasang').show();
				$('#secondpasang').html("00");
		        $('#minutepasang').html("00");
		        $('#hourpasang').html("00");
		        $('#product_pasang').html("");
		        $('#part_pasang').html("");
		        $('#mesin_pasang_list').html("");
		        $('#last_counter_pasang').html("");
		        // $('#moldingLogPasang').html("");
		        $('#mesin_pasang_pilihan').show();
		        // $('#mesin_pasang').hide();
		        $('#loading').hide();
			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error','Cancel Failed');
			}
		});
	}

	function finishPasang() {
		$('#loading').show();
		if (status_approval_qa == null || status_parameter == null || status_cek_visual == null || status_purging == null || status_setting_robot == null) {
			openErrorGritter('Error!','Semua Proses Harus Dilalui');
			$('#loading').hide();
			return false;
		}
		$('#loading').show();
		clearInterval(intervalUpdate);
		count_pasang = false;
		var detik = $('div.timerpasang span.secondpasang').text();
        var menit = $('div.timerpasang span.minutepasang').text();
        var jam = $('div.timerpasang span.hourpasang').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#pasang').val(waktu);

		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();
		var mesin = $('#mesin_pasang').text();
		var part = $('#part_pasang').text();
		var color = $('#product_pasang').text();
		var total_shot = $('#last_counter_pasang').text();
		var start_time = $('#start_time_pasang').val();
		var end_time = getActualFullDate();
		var running_time = $('#pasang').val();
		var notepasang = $('#notepasang').val();
		var molding_code = $('#molding_code').val();
		var part_name = $('#product').val();
		var part_type = $('#part').val();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		var data = {
			mesin : mesin,
			type : 'PASANG',
			pic_1 : pic_1,
			pic_2 : pic_2,
			pic_3 : pic_3,
			part : part,
			color : color,
			total_shot : total_shot,
			start_time : start_time,
			end_time : end_time,
			running_time : running_time,
			notelepas : notepasang,
			molding_code : molding_code,
			part_name : part_name,
			part_type : part_type,
		}

		$.post('{{ url("index/injeksi/store_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','History Molding has been created');
				// reset();
				$('#finish_pasang').hide();
				$('#pasangnote').hide();
				$('#pasangnote2').hide();
				$('#batal_pasang').hide();
				$('#pasangtime').hide();
				$('#start_pasang').show();
				$('#secondpasang').html("00");
		        $('#minutepasang').html("00");
		        $('#hourpasang').html("00");
		        $('#product_pasang').html("");
		        $('#part_pasang').html("");
		        $('#mesin_pasang_list').html("");
		        $('#last_counter_pasang').html("");
		        // $('#moldingLogPasang').html("");
		        $('#mesin_pasang_pilihan').show();
		        // $('#mesin_pasang').hide();
		        getMoldingLog($('#mesin_fix2').text());
		        getMoldingLogPasang($('#mesin_fix2').text());
		        $('#loading').hide();
		        location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create History Molding Failed');
				$('#loading').hide();
			}
		});
	}

	function store_history_temp(type) {
		$('#loading').show();
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		var start_time = getActualFullDate();
		if (type === 'LEPAS') {
			var mesin = $('#mesin_lepas').text();
			var part = $('#part_lepas').text();
			var color = $('#color_lepas').text();
			var part_name = null;
			var part_type = null;
			var total_shot = $('#total_shot_lepas').text();
			if (parseInt(total_shot) < 15000) {
				$('#reason').html('LEPAS');
			}else if (parseInt(total_shot) >= 15000){
				$('#reason').html('MAINTENANCE');
			}
			$('#start_time_lepas').val(start_time);
			duration = 0;
			count = true;
			var tanggal_fix = start_time.replace(/-/g,'/');
			started_at = new Date(tanggal_fix);
			getMoldingLog($('#mesin_fix2').text());
			$('#loading').hide();
		}
		else if (type === 'PASANG') {
			var mesin = $('#mesin_pasang').text();
			var color = $('#part_pasang').text();
			var part = $('#product_pasang').text();
			var part_name = $('#product').val();
			var part_type = $('#part').val();
			var total_shot = $('#last_counter_pasang').text();
			$('#start_time_pasang').val(start_time);
			duration = 0;
			count_pasang = true;
			var tanggal_fix = start_time.replace(/-/g,'/');
			started_at = new Date(tanggal_fix);
			getMoldingLogPasang(mesin);
			$('#loading').hide();
		}

		var pic = [];

		if (pic_1 != "-") {
			pic.push(pic_1);
		}

		if (pic_2 != "-") {
			pic.push(pic_2);
		}

		if (pic_3 != "-") {
			pic.push(pic_3);
		}

		if (mesin == '-' || mesin == null) {
			$('#loading').hide();
			alert('Semua Data Harus Diisi');
		}else{
			var data = {
				molding_code:type+'_'+pic+'_'+mesin+'_'+color+'_'+getActualFullDate(),
				mesin : mesin,
				type : type,
				pic : pic.join(', '),
				part : part,
				color : color,
				total_shot : total_shot,
				start_time : start_time,
				part_name : part_name,
				part_type : part_type,
			}

			$.post('{{ url("index/injeksi/store_history_temp") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success','History Molding Temp has been created');
					// reset();
					getMoldingLogPasang($('#mesin_fix2').text());
					$('#molding_code').val(result.molding_code);
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error','Create History Molding Temp Failed');
				}
			});
		}
	}

	function get_history_temp(mesin) {
		$('#loading').show();
		var data = {
			mesin : mesin
		}
		$.get('{{ url("index/injeksi/get_history_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$('#moldingLogPasang').html('');
					$.each(result.datas, function(key, value) {
						var pic = value.pic.split(', ');

						if (pic.length == 1) {
							$('#op_0').html(pic[0]);
						}else if(pic.length == 2){
							$('#op_0').html(pic[0]);
							$('#op_1').html(pic[1]);
						}else{
							$('#op_0').html(pic[0]);
							$('#op_1').html(pic[1]);
							$('#op_2').html(pic[2]);
						}
						if (value.remark != null) {
							if (confirm('Pekerjaan dalam proses '+value.remark+'. Apakah Anda ingin melanjutkan?')) {
								$('#molding_code').val(value.molding_code);
								// changeStatus(value.molding_code);
								if (value.type == "LEPAS") {
									$('#mesin_lepas').html(value.mesin);
									$('#part_lepas').html(value.part);
									$('#color_lepas').html(value.product);
									$('#total_shot_lepas').html(value.total_shot);
									$('#notelepas').val(value.note);
									$('#start_time_lepas').val(value.start_time);
									$('#push_block_id_gen').val(value.push_block_id_gen);
									if (parseInt(value.total_shot) < 15000) {
										$('#reason').html('LEPAS');
									}else if (parseInt(value.total_shot) >= 15000){
										$('#reason').html('MAINTENANCE');
									}
									duration = 0;
									count = true;
									var tanggal_fix = value.start_time.replace(/-/g,'/');
									started_at = new Date(tanggal_fix);
									$('#start_lepas').hide();
									$('#finish_lepas').show();
									$('#lepastime').show();
									$('#lepasnote').show();
									// $('#reasonlepas').show();
									$('#lepasnote2').show();
									$('#batal_lepas').show();
									$('#div_ok').show();
									$('#div_maintenance').show();
									$('#div_keputusan').show();
								}
								else if(value.type == 'PASANG'){
									$('#mesin_pasang_pilihan').hide();
									$('#mesin_pasang').show();
									$('#mesin_pasang').html(value.mesin);
									$('#mesin_pasang_list').html(value.mesin);
									$('#part_pasang').html(value.color);
									$('#product_pasang').html(value.part);
									$('#last_counter_pasang').html(value.total_shot);
									$('#notepasang').val(value.note);
									$('#start_time_pasang').val(value.start_time);
									$('#push_block_id_gen').val(value.push_block_id_gen);
									duration = 0;
									count_pasang = true;
									var tanggal_fix = value.start_time.replace(/-/g,'/');
									started_at = new Date(tanggal_fix);
									$('#start_pasang').hide();
									$('#finish_pasang').show();
									$('#pasangtime').show();
									$('#pasangnote').show();
									$('#pasangnote2').show();
									$('#batal_pasang').show();
									$('#parameter_pasang').prop('disabled',true);

									if (value.status_approval_qa != null && value.status_cek_visual != null && value.status_purging != null && value.status_setting_robot != null) {
										$('#parameter_pasang').removeAttr('disabled');
									}
									if (value.status_parameter != null) {
										status_parameter = value.status_parameter;
									}
									status_approval_qa = value.status_approval_qa;
									status_cek_visual = value.status_cek_visual;
									status_purging = value.status_purging;
									status_setting_robot = value.status_setting_robot;
									$('#product').val(value.part_name).trigger('change');
									$('#part').val(value.part_type).trigger('change');
								}								
								intervalUpdate = setInterval(update_history_temp,60000);
								if (value.remark == 'APPROVAL QA') {
									$('#approval_pasang').prop('class','btn btn-info');
								}else if(value.remark == 'CEK VISUAL & DIMENSI'){
									$('#cek_visual_pasang').prop('class','btn btn-info');
								}else if(value.remark == 'PURGING'){
									$('#purging_pasang').prop('class','btn btn-info');
								}else if(value.remark == 'SETTING ROBOT & CAMERA'){
									$('#setting_robot').prop('class','btn btn-info');
								}else{
									changeStatus(value.molding_code);
								}
								$('#loading').hide();
							}else{
								$('#loading').hide();
								changeMesin();
							}
						}else{
							$('#molding_code').val(value.molding_code);
							if (value.type == "LEPAS") {
								$('#mesin_lepas').html(value.mesin);
								$('#part_lepas').html(value.part);
								$('#color_lepas').html(value.product);
								$('#total_shot_lepas').html(value.total_shot);
								$('#notelepas').val(value.note);
								$('#start_time_lepas').val(value.start_time);
								$('#push_block_id_gen').val(value.push_block_id_gen);
								if (parseInt(value.total_shot) < 15000) {
									$('#reason').html('LEPAS');
								}else if (parseInt(value.total_shot) >= 15000){
									$('#reason').html('MAINTENANCE');
								}
								duration = 0;
								count = true;
								var tanggal_fix = value.start_time.replace(/-/g,'/');
								started_at = new Date(tanggal_fix);
								$('#start_lepas').hide();
								$('#finish_lepas').show();
								$('#lepastime').show();
								$('#lepasnote').show();
								// $('#reasonlepas').show();
								$('#lepasnote2').show();
								$('#batal_lepas').show();
								$('#div_ok').show();
								$('#div_maintenance').show();
								$('#div_keputusan').show();
								$('#loading').hide();
							}
							else if(value.type == 'PASANG'){
								$('#mesin_pasang_pilihan').hide();
								$('#mesin_pasang').show();
								$('#mesin_pasang').html(value.mesin);
								$('#mesin_pasang_list').html(value.mesin);
								$('#part_pasang').html(value.color);
								$('#product_pasang').html(value.part);
								$('#last_counter_pasang').html(value.total_shot);
								$('#notepasang').val(value.note);
								$('#start_time_pasang').val(value.start_time);
								$('#push_block_id_gen').val(value.push_block_id_gen);
								duration = 0;
								count_pasang = true;
								var tanggal_fix = value.start_time.replace(/-/g,'/');
								started_at = new Date(tanggal_fix);
								$('#start_pasang').hide();
								$('#finish_pasang').show();
								$('#pasangtime').show();
								$('#pasangnote').show();
								$('#pasangnote2').show();
								$('#batal_pasang').show();
								$('#parameter_pasang').prop('disabled',true);
								if (value.status_approval_qa != null && value.status_cek_visual != null && value.status_purging != null && value.status_setting_robot != null) {
									$('#parameter_pasang').removeAttr('disabled');
								}
								if (value.status_parameter != null) {
									status_parameter = value.status_parameter;
								}
								status_approval_qa = value.status_approval_qa;
								status_cek_visual = value.status_cek_visual;
								status_purging = value.status_purging;
								status_setting_robot = value.status_setting_robot;
								$('#product').val(value.part_name).trigger('change');
								$('#part').val(value.part_type).trigger('change');
								$('#loading').hide();
							}
							intervalUpdate = setInterval(update_history_temp,60000);
						}
					});
					$('#loading').hide();
				}
				// $('#loading').hide();
				openSuccessGritter('Success!', result.message);
				
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function update_history_temp() {
		$('#loading').showw();
		var mesin = $('#mesin_fix2').text();
		var notelepas = $('#notelepas').val();
		var notepasang = $('#notepasang').val();

		var data = {
			mesin : mesin,
			note : notelepas,
			type : 'LEPAS'
		}

		$.post('{{ url("index/injeksi/update_history_temp") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				// openSuccessGritter('Success','History Molding Temp has been updated');
				// reset();
			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error','Update History Molding Temp Failed');
			}
		});

		var data2 = {
			mesin : mesin,
			note : notepasang,
			type : 'PASANG'
		}

		$.post('{{ url("index/injeksi/update_history_temp") }}', data2, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				// openSuccessGritter('Success','History Molding Temp has been updated');
				// reset();
			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error','Update History Molding Temp Failed');
			}
		});
	}

	function pause(type,status) {
		$('#modalStatus').modal('show');
		$('#typePause').val(type);
		$('#statusReason').html(status);
		// if (status === 'CEK VISUAL & DIMENSI') {
		// 	$('#reasonPause').val('Cek Visual & Dimensi').trigger('change');
		// }else if(status === 'APPROVAL QA') {
		// 	$('#reasonPause').val('Approval QA').trigger('change');
		// }else{
		// 	$('#reasonPause').val('-').trigger('change');
		// }
	}

	function saveStatus() {
		$('#loading').show();
		var reason = $('#reasonPause').val();

		if (reason == '-') {
			$('#loading').hide();
			alert('Pilih Reason');
		}else{
			$('#loading').show();
			var pic_1 = $('#op_0').text();
			var pic_2 = $('#op_1').text();
			var pic_3 = $('#op_2').text();

			var pic = [];

			if (pic_1 != "-") {
				pic.push(pic_1);
			}

			if (pic_2 != "-") {
				pic.push(pic_2);
			}

			if (pic_3 != "-") {
				pic.push(pic_3);
			}

			var type=$('#typePause').val();

			if (type == 'PASANG') {
				var mesin = $('#mesin_pasang').text();
				var part = $('#part_pasang').text();
			}else{
				var mesin = $('#mesin_lepas').text();
				var part = $('#part_lepas').text();
			}

			var data = {
				type:$('#typePause').val(),
				molding_code:$('#molding_code').val(),
				status:$('#statusReason').text(),
				pic:pic.join(),
				mesin:mesin,
				part:part,
				reason:reason,
				start_time:getActualFullDate(),
			}

			$.get('{{ url("input/reason_pause") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					alert('Pemasangan / Pelepasan Molding Dalam Proses '+$('#statusReason').text());
					location.reload();
					$('#reasonPause').val('-').trigger('change');
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function changeStatus(molding_code) {
		$('#loading').show();
		var data = {
			molding_code:molding_code
		}
		$.get('{{ url("change/reason_pause") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				alert('Pemasangan / Pelepasan Molding Dilanjutkan.');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function approvalcek(reason,remark) {
		$('#loading').show();
		var reason = reason;
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		var pic = [];

		if (pic_1 != "-") {
			pic.push(pic_1);
		}

		if (pic_2 != "-") {
			pic.push(pic_2);
		}

		if (pic_3 != "-") {
			pic.push(pic_3);
		}

		var type="PASANG";

		if (type == 'PASANG') {
			var mesin = $('#mesin_pasang').text();
			var part = $('#part_pasang').text();
		}

		var data = {
			type:"PASANG",
			molding_code:$('#molding_code').val(),
			status:remark,
			pic:pic.join(),
			mesin:mesin,
			part:part,
			reason:reason,
			start_time:getActualFullDate(),
			status_qa:null
		}

		$.get('{{ url("input/approval_cek") }}', data, function(result, status, xhr){
			if(result.status){
				if (result.statusApprovalCek == 'Mulai') {
					if (remark == 'APPROVAL QA') {
						$('#approval_pasang').prop('class','btn btn-info');
						openSuccessGritter('Success','Approval QA Dimulai');
					}else if (remark == 'CEK VISUAL & DIMENSI') {
						$('#cek_visual_pasang').prop('class','btn btn-info');
						openSuccessGritter('Success','Cek Dimensi Dimulai');
					}else if (remark == 'PURGING') {
						$('#purging_pasang').prop('class','btn btn-info');
						openSuccessGritter('Success','Purging Dimulai');
					}else if (remark == 'SETTING ROBOT & CAMERA') {
						$('#setting_robot').prop('class','btn btn-info');
						openSuccessGritter('Success','Setting Robot & Camera Dimulai');
					}
				}else if(result.statusApprovalCek == 'Selesai'){
					if (remark == 'APPROVAL QA') {
						// $('#approval_pasang').prop('class','btn btn-default');
						// openSuccessGritter('Success','Approval QA Selesai');
						$('#modalScanApproval').modal('show');
						$('#tag_molding').val('');
						$('#tag_qa').val('');
						$('#tag_molding').focus();
						if (result.datawork.status_approval_qa != null && result.datawork.status_cek_visual != null && result.datawork.status_purging != null && result.datawork.status_setting_robot != null) {
							$('#parameter_pasang').removeAttr('disabled');
						}
					}else if (remark == 'CEK VISUAL & DIMENSI') {
						$('#cek_visual_pasang').prop('class','btn btn-default');
						openSuccessGritter('Success','Cek Visual & Dimensi Selesai');
						if (result.datawork.status_approval_qa != null && result.datawork.status_cek_visual != null && result.datawork.status_purging != null && result.datawork.status_setting_robot != null) {
							$('#parameter_pasang').removeAttr('disabled');
						}
						status_cek_visual = '{{date("Y-m-d H:i:s")}}';
					}else if (remark == 'PURGING') {
						$('#purging_pasang').prop('class','btn btn-default');
						openSuccessGritter('Success','Purging Selesai');
						if (result.datawork.status_approval_qa != null && result.datawork.status_cek_visual != null && result.datawork.status_purging != null && result.datawork.status_setting_robot != null) {
							$('#parameter_pasang').removeAttr('disabled');
						}
						status_purging = '{{date("Y-m-d H:i:s")}}';
					}else if (remark == 'SETTING ROBOT & CAMERA') {
						$('#setting_robot').prop('class','btn btn-default');
						openSuccessGritter('Success','Setting Robot & Camera Selesai');
						if (result.datawork.status_approval_qa != null && result.datawork.status_cek_visual != null && result.datawork.status_purging != null && result.datawork.status_setting_robot != null) {
							$('#parameter_pasang').removeAttr('disabled');
						}
						status_setting_robot = '{{date("Y-m-d H:i:s")}}';
					}
				}
				$('#reasonPause').val('-').trigger('change');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function saveApproval(reason,remark) {
		$('#loading').show();
		if ($("#tag_molding").val() == '' || $("#tag_qa").val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','PIC Molding & QA harus diisi.');
			audio_error.play();
			return false;
		}
		var reason = reason;
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		var pic = [];

		if (pic_1 != "-") {
			pic.push(pic_1);
		}

		if (pic_2 != "-") {
			pic.push(pic_2);
		}

		if (pic_3 != "-") {
			pic.push(pic_3);
		}

		var type="PASANG";

		if (type == 'PASANG') {
			var mesin = $('#mesin_pasang').text();
			var part = $('#part_pasang').text();
		}
		var data = {
			type:"PASANG",
			molding_code:$('#molding_code').val(),
			status:remark,
			pic:pic.join(),
			mesin:mesin,
			part:part,
			reason:reason,
			start_time:getActualFullDate(),
			status_qa:'selesai',
			pic_molding:$("#tag_molding").val(),
			pic_qa:$("#tag_qa").val(),
		}
		$.get('{{ url("input/approval_cek") }}', data, function(result, status, xhr){
			if(result.status){
				if(result.statusApprovalCek == 'Selesai'){
					if (remark == 'APPROVAL QA') {
						$('#approval_pasang').prop('class','btn btn-default');
						openSuccessGritter('Success','Approval QA Selesai');
						if (result.datawork.status_approval_qa != null && result.datawork.status_cek_visual != null && result.datawork.status_purging != null && result.datawork.status_setting_robot != null) {
							$('#parameter_pasang').removeAttr('disabled');
						}
						status_approval_qa = '{{date("Y-m-d H:i:s")}}';
						$('#modalScanApproval').modal('hide');
						$('#tag_molding').val('');
						$('#tag_qa').val('');
					}
				}
				$('#loading').hide();
				$('#reasonPause').val('-').trigger('change');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}


	var duration = 0;
	var count = false;
	var count_pasang = false;
	var started_at;
	function setTime() {
		if(count){
			$('#secondlepas').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minutelepas').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourlepas').html((pad(parseInt(diff_seconds(new Date(), started_at) / 3600))).toString());
		}
		if(count_pasang){
			$('#secondpasang').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minutepasang').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourpasang').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
		}
	}

	function formatTime(time) {
	   let d = new Date(((time + timeConversion) * 1000));
	   return
	      ('0' + d.getUTCDate()).slice(-2)
	      + '/' +
	      ('0' + (d.getUTCMonth() + 1) ).slice(-2)
	      + '/' +
	      d.getUTCFullYear().toString().substr(2, 4)
	      + ' ' + 
	      ('0' + d.getUTCHours()).slice(-2)
	      + ':' +
	      ('0' + d.getUTCMinutes()).slice(-2)
	      ;
	};

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
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
		var date = day + "/" + month + "/" + year;

		return date;
	};

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

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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

