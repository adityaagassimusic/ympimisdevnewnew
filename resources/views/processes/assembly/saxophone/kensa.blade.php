@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link href="{{ url('bower_components/roundslider/dist/roundslider.min.css') }}" rel="stylesheet" />
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
	#ngTemp {
		height:200px;
		overflow-y: scroll;
	}
	#ngHistory {
		height:150px;
		overflow-y: scroll;
	}
	#historyLocation{
		overflow-x: scroll;
	}
	#ngAll {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }

	.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 14px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left:-10px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
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
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="started_at">
	<input type="hidden" id="confirm_status">
	<div class="row" style="padding-left: 10px;padding-right: 10px">
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-8">
						<div class="row">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 0;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 16px;" colspan="3">Operator Kensa</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:16px; width: 30%;" id="op">-</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;" id="op2"></td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:16px; width: 30%;" id="line">-</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-4">
							<div class="input-group">
								<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan RFID Card..." required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
									<i class="glyphicon glyphicon-credit-card"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%; margin-top: 5px;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 2%; font-size: 16px; font-weight: bold; background-color: rgb(100,100,100); color: yellow; border: 1px solid black" colspan="2"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">SN</td>
							<td id="serial_number" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Loc</td>
							<td id="location_now" style="width: 5%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">{{$location}}</td>
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px">
				@if($location == 'repair-process')
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 10px;">
				@else
				<div class="col-xs-8" style="padding-left: 0px;padding-right: 10px;">
				@endif
					<div id="historyLocation">
						<table class="table table-bordered" style="width: 100%;padding-top: 5px;">
							<tbody id="details">
							</tbody>
						</table>
					</div>
				</div>
				<?php if ($location != 'repair-process'): ?>
					<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px;">
						<table style="width: 100%">
							<thead>
								<tr style="color: white">
									<th colspan="2" style="border-bottom: 2px solid white">
										PENENTUAN SP
									</th>
								</tr>
								<tr style="color: white">
									<th style="border-right: 2px solid white">
										Process
									</th>
									<th>
										QA
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
									<select class="form-control select2" id="remark_process" style="width: 100%" data-placeholder="Penentuan SP">
										<option value=""></option>
										<option value="Normal">Normal</option>
										<option value="SP">SP</option>
									</select>
									</td>
									<td>
									<select class="form-control select2" id="remark_qa" style="width: 100%" data-placeholder="Penentuan SP">
										<option value=""></option>
										<option value="Normal">Normal</option>
										<option value="SP">SP</option>
									</select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				<?php endif ?>
			</div>
			<div style="padding-top: 5px">
				<div id="ngTemp" class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
					<table id="ngTempTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<thead>
							<tr>
								<th colspan="5" style="width: 40%; background-color: darkorange; padding:0;font-size: 15px;" >Temporary NG</th>
							</tr>
							<tr>
								<th style="width: 40%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Nama NG</th>
								<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Value / Jumlah</th>
								<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Onko</th>
								<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Oleh</th>
								<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Action</th>
							</tr>
						</thead>
						<tbody id="ngTempBody">
						</tbody>
					</table>
				</div>
			</div>
			<div style="padding-top: 5px">
				<div id="ngHistory" class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
					<table id="ngHistoryTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<thead>
							<tr>
								<th colspan=7 style="width: 3%; background-color: darkturquoise; padding:0;font-size: 15px;" >History NG</th>
							</tr>
							<tr>
								<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Nama NG</th>
								<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Value / Jumlah</th>
								<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Onko</th>
								<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Loc</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Oleh</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Ganti Kunci</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Repair</th>
							</tr>
						</thead>
						<tbody id="ngHistoryBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
					<div style="width: 100%;background-color: lightgray;text-align: center;">
						<span style="font-weight: bold;font-size: 18px;">
							Note
						</span>
					</div>
					<textarea style="width: 100%" readonly="true" class="form-control" placeholder="Note History" id="note_history"></textarea>
					<textarea style="width: 100%" class="form-control" placeholder="Note" id="note"></textarea>
				</div>
			</div>

			<?php if ($location == 'qa-audit'): ?>
				<div style="padding-top: 5px">
					<div id="detailQaAudit" class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
						<table id="tableQaAudit" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
							<thead>
								<tr>
									<th colspan=6 style="width: 3%; background-color: chartreuse; padding:0;font-size: 15px;" >QA Audit Detail</th>
								</tr>
								<tr>
									<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
									<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Serial Number</th>
									<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Model</th>
									<th style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Time</th>
									<th style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >PIC Visual / Tenor</th>
									<th style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >PIC Fungsi</th>
								</tr>
							</thead>
							<tbody id="bodyTableQaAudit">
							</tbody>
						</table>
					</div>
				</div>
			<?php endif ?>

			<div style="padding-top: 5px">
				<div id="spec_process_table" style="display: none">
					<table id="specProcessTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<thead>
							<tr>
								<th colspan="6" style="width: 3%; background-color: dodgerblue; color: white; padding:0;font-size: 15px;" >Spec Kensa Process</th>
							</tr>
							<tr>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Made In</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Body</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Bell</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Side Cover</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >F-4</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >J-3</th>
							</tr>
						</thead>
						<tbody id="specProcessBody">
							<tr>
								<td>
									<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih Made In" id="made_in">
										<option value=""></option>
										<option value="Indonesia">Indonesia</option>
										<option value="China">China</option>
										<option value="Japan">Japan</option>
									</select>
								</td>
								<td>
									<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih Body" id="body">
										<option value=""></option>
										<option value="480">480</option>
										<option value="480S">480S</option>
										<option value="280">280</option>
										<option value="280S">280S</option>
										<option value="380">380</option>
										<option value="26">26</option>
										<option value="26S">26S</option>
										<option value="200AD">200AD</option>
										<option value="200ADS">200ADS</option>
										<option value="300AD">300AD</option>
										<option value="580AL">580AL</option>
										<option value="VDHM">VDHM</option>
										<option value="PLU1II">PLU1II</option>
									</select>
								</td>
								<td>
									<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih Bell" id="bell">
										<option value=""></option>
										<option value="480">480</option>
										<option value="480S">480S</option>
										<option value="280">280</option>
										<option value="280S">280S</option>
										<option value="380">380</option>
										<option value="26">26</option>
										<option value="26S">26S</option>
										<option value="200AD">200AD</option>
										<option value="200ADS">200ADS</option>
										<option value="300AD">300AD</option>
										<option value="580AL">580AL</option>
										<option value="VDHM">VDHM</option>
										<option value="PLU1II">PLU1II</option>
									</select>
								</td>
								<td>
									<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih Side Cover" id="side_cover">
										<option value=""></option>
										<option value="480">480</option>
										<option value="480S">480S</option>
										<option value="280">280</option>
										<option value="280S">280S</option>
										<option value="380">380</option>
										<option value="26">26</option>
										<option value="26S">26S</option>
										<option value="200AD">200AD</option>
										<option value="200ADS">200ADS</option>
										<option value="300AD">300AD</option>
										<option value="580AL">580AL</option>
										<option value="VDHM">VDHM</option>
										<option value="PLU1II">PLU1II</option>
									</select>
								</td>
								<td>
									<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih F-4" id="f_4">
										<option value=""></option>
										<option value="480">480</option>
										<option value="480S">480S</option>
										<option value="280">280</option>
										<option value="280S">280S</option>
										<option value="380">380</option>
										<option value="26">26</option>
										<option value="26S">26S</option>
										<option value="200AD">200AD</option>
										<option value="200ADS">200ADS</option>
										<option value="300AD">300AD</option>
										<option value="580AL">580AL</option>
										<option value="VDHM">VDHM</option>
										<option value="PLU1II">PLU1II</option>
									</select>
								</td>
								<td>
									<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih J-3" id="j_3">
										<option value=""></option>
										<option value="480">480</option>
										<option value="480S">480S</option>
										<option value="280">280</option>
										<option value="280S">280S</option>
										<option value="380">380</option>
										<option value="26">26</option>
										<option value="26S">26S</option>
										<option value="200AD">200AD</option>
										<option value="200ADS">200ADS</option>
										<option value="300AD">300AD</option>
										<option value="580AL">580AL</option>
										<option value="VDHM">VDHM</option>
										<option value="PLU1II">PLU1II</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-5" style="padding-right: 0;">
			<?php if (count($ng_lists) > 0){ ?>
				<div id="ngAll">
			<?php }else{ ?>
				<div id="ngAll" style="height: 0px;">
			<?php } ?>
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >List NG</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<tr <?php echo $color ?>>
							<td onclick="showNgDetail('{{ $ng_list->ng_name }}')" style="font-size: 35px;cursor: pointer;">{{ $ng_list->ng_name }} </td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="col-xs-12" style="padding: 0px;margin-top: 10px;" id="div_qa_audit">
				<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
					<div style="text-align: center;background-color: white;">
						<span style="font-weight: bold;">Pilih Operator QA Visual / Tenor</span>
					</div>
					<select class="form-control select2" id="operator_qa" data-placeholder="Pilih Operator QA Visual / Tenor">
						<option value=""></option>
						@foreach($operator_qa as $operator_qa)
						<option value="{{$operator_qa->employee_id}}">{{$operator_qa->employee_id}} - {{$operator_qa->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
					<div style="text-align: center;background-color: white;">
						<span style="font-weight: bold;">Pilih Operator QA Fungsi</span>
					</div>
					<select class="form-control select2" id="operator_qa2" data-placeholder="Pilih Operator QA Fungsi">
						<option value=""></option>
						@foreach($operator_qa2 as $operator_qa2)
						<option value="{{$operator_qa2->employee_id}}">{{$operator_qa2->employee_id}} - {{$operator_qa2->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			@if($location == 'repair-process')
			<div style="margin-top: 100px;">
			@else
			<div>
			@endif
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 49%" onclick="cancelAll()" class="btn btn-danger">CANCEL</button>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 49%" onclick="confirmAll()" class="btn btn-success">CONFIRM</button>
					<!-- <button style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white;" onclick="showModalSpec()" class="btn btn-primary">SPEC PRODUCT</button> -->
				</center>
			</div>
			<div class="col-xs-12" style="padding: 0px;margin-top: 10px;">
				<div id="spec_product">
					
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0">
			<div style="padding-top: 5px">
				<div id="spec_product_table">
					<table id="specProductTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<!-- <thead> -->
							<!-- <tr>
								<th colspan="5" style="width: 3%; background-color: dodgerblue; color: white; padding:0;font-size: 15px;" >Spec Product</th>
							</tr>
							<tr>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >BELL AND BOW</th> -->
								<!-- <th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Point</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Detail</th>
								<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Cara Cek</th>
								<th style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Result</th> -->
								<!-- <th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Kptsn</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Repair</th> -->
							<!-- </tr>
							<tr>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >BODY</th>
							</tr>
							<tr>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >NECK</th>
							</tr>
						</thead> -->
						<!-- <tbody id="specProductBody">
						</tbody> -->
					</table>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Pilih Line</label>
						<table style="width: 100%" id="tableLine">
							<tr>
								<td style="padding-right: 2px;">
									<button class="btn btn-primary" onclick="changeLine('1')" style="width: 100%;font-size: 18px;">
										<b>Line 1</b>
									</button>
								</td>
								<td style="padding-right: 2px;">
									<button class="btn btn-primary" onclick="changeLine('2')" style="width: 100%;font-size: 18px;">
										<b>Line 2</b>
									</button>
								</td>
								<td style="padding-right: 2px;">
									<button class="btn btn-primary" onclick="changeLine('3')" style="width: 100%;font-size: 18px;">
										<b>Line 3</b>
									</button>
								</td>
								<td style="padding-right: 2px;">
									<button class="btn btn-primary" onclick="changeLine('4')" style="width: 100%;font-size: 18px;">
										<b>Line 4</b>
									</button>
								</td>
								<td style="padding-right: 0px;">
									<button class="btn btn-primary" onclick="changeLine('5')" style="width: 100%;font-size: 18px;">
										<b>Line 5</b>
									</button>
								</td>
							</tr>
						</table>
						<table style="width: 100%;display: none;" id="tableLineFix">
							<tr>
								<td style="padding-right: 2px;">
									<button class="btn btn-warning" onclick="changeLineBack()" style="width: 100%;font-size: 18px;font-weight: bold;" id="line_fix">
										LINE
									</button>
								</td>
							</tr>
						</table>
					</div>
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
					<div class="form-group" style="margin-top: 30px;">
						<button class="btn btn-success" onclick="confirmLine()" style="width: 100%;font-size: 18px;">
							<b>CONFIRM</b>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="modalNg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body no-padding">
					<h4 id="judul_ng" style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">Pilih NG</h4>
					<div class="row">
						<div class="col-xs-12" id="ngDetail">
						</div>
						<div class="col-xs-12" id="ngDetailFix" style="display: none;padding-top: 5px">
							<center><button class="btn btn-primary" style="width:100%;font-size: 25px;font-weight: bold;" onclick="getNgChange()" id="ngFix">NG
							</button></center>
							<input type="hidden" id="ngFix2" value="NG">
						</div>
					</div>

					<h4 id="judul_onko" style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #ffd375;padding: 5px">Pilih Lokasi NG</h4>
					<div class="row">
						<div class="col-xs-12" id="onkoBody">
						</div>
						<div class="col-xs-12" id="onkoBodyFix" style="display: none;padding-top: 5px">
							<center><button class="btn btn-warning" style="width:100%;font-size: 25px;font-weight: bold" onclick="getOnkoChange()" id="onkoFix">ONKO
							</button></center>
							<input type="hidden" id="onkoFix2" value="ONKO">
						</div>
					</div>
					<div id="ngNari" class="row" style="display: none">
						<div class="col-xs-12">
							<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: darkorchid;color:white;padding: 5px">Pilih Indikator Jam untuk NG Nari</h4>
						</div>
						<div class="col-xs-6" style="padding-right: 5px;" id="divAawal">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_atas" data-placeholder="Pilih Jam Awal">
								<option value=""></option>
								<option value="0">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
							</select>
						</div>
						<div class="col-xs-6" style="padding-left: 5px;" id="divAkhir">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_bawah" data-placeholder="Pilih Jam Akhir">
								<option value=""></option>
								<option value="0">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
							</select>
						</div>
						<div class="col-xs-12">
							<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: greenyellow;padding: 5px">Pilih Level Nari</h4>
						</div>
						<div class="col-xs-12" id="divAkhirLokasi">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_lokasi" data-placeholder="Pilih Level Nari">
								<option value=""></option>
								<option value="A">A</option>
								<option value="B">B</option>
								<option value="C">C</option>
								<option value="D">D</option>
							</select>
						</div>
					</div>

					<div id="ngMilihKunci" class="row" style="display: none">
						<div class="col-xs-12">
							<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #ffd375;padding: 5px">Pilih Kunci Fleksibel</h4>
						</div>
						<div class="col-xs-6" style="padding-right: 5px;" id="divAawalFlek">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_atas_flek" data-placeholder="Pilih Kunci">
								<option value=""></option>
								<option value="H1">H1</option>
								<option value="H2">H2</option>
								<option value="H3">H3</option>
								<option value="H4">H4</option>
								<option value="H5">H5</option>
								<option value="G1">G1</option>
								<option value="G2">G2</option>
								<option value="C1">C1</option>
								<option value="C2">C2</option>
								<option value="C3">C3</option>
								<option value="C4">C4</option>
								<option value="C5">C5</option>
								<option value="D1">D1</option>
								<option value="D2">D2</option>
								<option value="D3">D3</option>
								<option value="D4">D4</option>
								<option value="D5">D5</option>
								<option value="E1">E1</option>
								<option value="E2">E2</option>
								<option value="E3">E3</option>
								<option value="E4">E4</option>
								<option value="E5">E5</option>
								<option value="E7">E7</option>
								<option value="E8">E8</option>
								<option value="E6">E6</option>
								<option value="F1">F1</option>
								<option value="F2">F2</option>
								<option value="F3">F3</option>
								<option value="F4">F4</option>
								<option value="J1">J1</option>
								<option value="J2">J2</option>
								<option value="J3">J3</option>
								<option value="J5">J5</option>
								<option value="J6">J6</option>
							</select>
						</div>
						<div class="col-xs-6" style="padding-left: 5px;" id="divAkhirFlek">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_bawah_flek" data-placeholder="Pilih Kunci">
								<option value=""></option>
								<option value="H1">H1</option>
								<option value="H2">H2</option>
								<option value="H3">H3</option>
								<option value="H4">H4</option>
								<option value="H5">H5</option>
								<option value="G1">G1</option>
								<option value="G2">G2</option>
								<option value="C1">C1</option>
								<option value="C2">C2</option>
								<option value="C3">C3</option>
								<option value="C4">C4</option>
								<option value="C5">C5</option>
								<option value="D1">D1</option>
								<option value="D2">D2</option>
								<option value="D3">D3</option>
								<option value="D4">D4</option>
								<option value="D5">D5</option>
								<option value="E1">E1</option>
								<option value="E2">E2</option>
								<option value="E3">E3</option>
								<option value="E4">E4</option>
								<option value="E5">E5</option>
								<option value="E7">E7</option>
								<option value="E8">E8</option>
								<option value="E6">E6</option>
								<option value="F1">F1</option>
								<option value="F2">F2</option>
								<option value="F3">F3</option>
								<option value="F4">F4</option>
								<option value="J1">J1</option>
								<option value="J2">J2</option>
								<option value="J3">J3</option>
								<option value="J5">J5</option>
								<option value="J6">J6</option>
							</select>
						</div>
					</div>

					<div id="divOperator">
						<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #75ff9f;padding: 5px">Pilih Operator Asal NG</h4>
						<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="operator_id_before_select" data-placeholder="Pilih Operator"></select>
					</div>
					<div style="padding-top: 10px">
						<div class="col-xs-6" style="padding-left: 0px;padding-right: 10px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="cancelNg()" class="btn btn-danger">CANCEL</button>
						</div>
						<div class="col-xs-6" style="padding-left: 10px;padding-right: 0px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalSerialConfirm" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body no-padding">
					<h4 id="judul_ng" style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">KONFIRMASI MATERIAL</h4>
					<div class="row">
						<div class="col-xs-12">
							<center><label style="font-size: 20px">Serial Number</label></center>
							<input type="text" readonly id="serial_number_confirm" style="text-align: center;font-size: 40px; width: 100%">
						</div>
						<div class="col-xs-12" style="margin-top: 10px;">
							<center><label style="font-size: 20px">Model</label></center>
							<input type="text" readonly id="model_confirm" style="text-align: center;font-size: 40px; width: 100%">
						</div>
					</div>
					<div style="padding-top: 10px">
						<div class="col-xs-6" style="padding-left: 0px;padding-right: 0px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold;" class="btn btn-danger" onclick="cancelAll()" class="btn btn-success">TIDAK SESUAI</button>
						</div>
						<div class="col-xs-6" style="padding-left: 5px;padding-right: 0px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold;" class="btn btn-success" onclick="confirmSerial()" class="btn btn-success">SESUAI</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('bower_components/roundslider/dist/roundslider.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var count_spec = 0;
	var onko = null;
	var ng_lists = null;
	var operator = null;
	jQuery(document).ready(function() {
		$('#div_qa_audit').hide();
		if ('{{$location}}' == 'qa-audit') {
			fetchNgTemp();
			$('#div_qa_audit').show();
		}
		if ('{{$location}}' == 'repair-process') {
			document.getElementById("ngHistory").style.height = "300px";
		}else{
			document.getElementById("ngHistory").style.height = "150px";
		}
		cancelAll();
		$('.select2').select2({
			allowClear:true
		});
		$('#value_atas').select2({
			allowClear:true,
			dropdownParent: $('#divAawal'),
		});
		$('#value_bawah').select2({
			allowClear:true,
			dropdownParent: $('#divAkhir'),
		});
		$('#value_lokasi').select2({
			allowClear:true,
			dropdownParent: $('#divAkhirLokasi'),
		});
		$('#value_atas_flek').select2({
			allowClear:true,
			dropdownParent: $('#divAawalFlek'),
		});
		$('#value_bawah_flek').select2({
			allowClear:true,
			dropdownParent: $('#divAkhirFlek'),
		});
		$('#spec_product_table').hide();
		if ('{{$location}}' == 'qa-kensa' || '{{$location}}' == 'qa-visual') {
			$('#spec_product_table').show();
		}
		$('#spec_process_table').hide();
		if ('{{$location}}' == 'kensa-process') {
			$('#spec_process_table').show();
		}
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').removeAttr('disabled');
		$('#operator').val('');
		$('#tableLine').show();
		$('#tableLineFix').hide();
		$('#line_fix').html('LINE');
		$('#tag').val('');
		count_spec = 0;
		onko = null;
		ng_lists = null;
		operator = null;
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	function changeLine(line) {
		$('#line_fix').html('Line '+line);
		$('#tableLine').hide();
		$('#tableLineFix').show();
		$('#operator').focus();
	}

	function changeLineBack() {
		$('#line_fix').html('LINE');
		$('#tableLine').show();
		$('#tableLineFix').hide();
	}

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#line_fix').text() == 'LINE') {
				openErrorGritter('Error!','Pilih Line Dulu');
				$('#operator').removeAttr('disabled');
				$('#operator').val('');
				audio_error.play();
				return false;
			}else{
				var data = {
					employee_id : $("#operator").val(),
					location: '{{$location}}',
					line:$('#line_fix').text().split(' ')[1]
				}

				$.get('{{ url("scan/assembly/operator_kensa/sax") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// $('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						$('#operator').prop('disabled',true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').removeAttr('disabled');
						$('#operator').val('');
					}
				});
			}
		}
	});

	function confirmLine() {
		if ($('#line_fix').text() == 'LINE') {
			openErrorGritter('Error!','Pilih Line');
			audio_error.play();
			return false;
		}

		if ($('#operator').val() == '' || $('#op2').text() == '') {
			openErrorGritter('Error!','Scan ID Card');
			audio_error.play();
			return false;
		}

		$('#modalOperator').modal('hide');
		$('#line').html($('#line_fix').text());
		$('#tag').focus();
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			fetchAll();
		}
	});

	function fetchAll() {
		var data = {
			tag : $("#tag").val(),
			location : $("#location_now").text(),
			location_number : $("#line").text().split(' ')[1],
			employee_id:$("#operator").val()
		}

		$.get('{{ url("scan/assembly/kensa/sax") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);

				$('#modalOperator').modal('hide');
				$('#serial_number').html(result.tag.serial_number);
				$('#model').html(result.tag.model);
				$('#tag').prop('disabled',true);

				if ($('#confirm_status').val() == '') {
					$('#modalSerialConfirm').modal('show');
				}
				$("#serial_number_confirm").val(result.tag.serial_number);
				$("#model_confirm").val(result.tag.model);

				//Detail
				$('#details').html('');
				var details = '';
				details += '<tr>';
				for(var i = 0; i < result.details.length;i++){
					if (i % 2 == 0) {
						var color = 'lightgreen';
					}else{
						var color = 'lightskyblue';
					}
					details += '<td style="background-color:'+color+';font-weight:bold;font-size:12px;text-align:left;padding-left:5px;">'+result.details[i].location.toUpperCase()+'</td>';
				}
				details += '</tr>';
				details += '<tr>';
				var notes = [];
				for(var i = 0; i < result.details.length;i++){
					if (result.details[i].name.split(' ').length > 1) {
						details += '<td style="background-color:white;font-size:12px;text-align:left;padding-left:5px;">'+result.details[i].name.split(' ')[0]+'<br>'+result.details[i].name.split(' ')[1]+'</td>';
					}else{
						details += '<td style="background-color:white;font-size:12px;text-align:left;padding-left:5px;">'+result.details[i].name.split(' ')[0]+'</td>';
					}
					if (result.details[i].location.match(/{{$location}}/gi)) {
						if (result.details[i].note != null) {
							notes.push(result.details[i].note);
						}
					}
				}
				details += '</tr>';

				$('#note_history').val(notes.join(','));

				$('#details').append(details);

				//History NG
				$('#ngHistoryBody').html('');
				var history_ng = '';
				for(var i = 0; i < result.history_ng.length;i++){
					history_ng += '<tr>';
					history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].ng_name+'</td>';
					history_ng += '<td style="background-color:white;font-size:13px;padding-right:5px;text-align:right;">';
					history_ng += result.history_ng[i].value_atas;
					if (result.history_ng[i].value_bawah != null) {
						history_ng += ' - '+result.history_ng[i].value_bawah;
					}
					history_ng += '</td>';
					if (result.history_ng[i].value_lokasi != null) {
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].ongko+' - '+result.history_ng[i].value_lokasi+'</td>';
					}else{
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].ongko+'</td>';
					}
					history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.history_ng[i].location.toUpperCase() || '')+'</td>';
					history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].name.split(' ')[0]+' '+result.history_ng[i].name.split(' ')[1]+'</td>';
					if (result.history_ng[i].decision == null) {
						if ('{{$location}}' == 'repair-process') {
							history_ng += '<td style="background-color:white;font-size:13px;text-align:center;">';
							history_ng += '<button class="btn btn-warning btn-sm" onclick="gantiKunci(\''+result.history_ng[i].id+'\')">Ganti Kunci</button>';
							history_ng += '</td>';
						}else{
							history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.history_ng[i].decision || '')+'</td>';
						}
					}else{
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.history_ng[i].decision || '')+'</td>';
					}
					if (result.history_ng[i].repair_status == null) {
						if ('{{$location}}' == 'repair-process') {
							history_ng += '<td style="background-color:white;font-size:13px;text-align:center;">';
							history_ng += '<button class="btn btn-success btn-sm" onclick="repair(\''+result.history_ng[i].id+'\')">Repair</button>';
							history_ng += '</td>';
						}else{
							history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.history_ng[i].repair_status || '')+'</td>';
						}
					}else{
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.history_ng[i].repair_status || '')+'</td>';
					}
					history_ng += '</tr>';
				}
				$('#ngHistoryBody').append(history_ng);

				fetchNgTemp();
				$("#started_at").val(result.started_at);

				$('#specProductTable').html('');
				if (('{{$location}}' == 'qa-kensa' && result.spec != null) || ('{{$location}}' == 'qa-visual' && result.spec != null)) {
					var tableSpec = '';
					tableSpec += '<thead>';
					tableSpec += '<tr>';
					tableSpec += '<th colspan="5" style="width: 3%; background-color: dodgerblue; color: white; padding:0;font-size: 15px;" >Spec Product</th>';
					tableSpec += '</tr>';
					tableSpec += '</thead>';
					tableSpec += '<tbody>';
					tableSpec += '<tr>';
					tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;padding-left:7px;font-weight:bold;" >BELL AND BOW</td>';
					var index = 1;
					for(var i = 0; i < result.spec.length;i++){
						if (result.spec[i].location == 'BELL AND BOW') {
							tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;text-align:left;padding-left:7px;"><input type="hidden" value="'+result.spec[i].location+'" id="location_'+i+'"><input type="hidden" value="'+result.spec[i].point+'" id="point_'+i+'"><input type="hidden" value="'+result.spec[i].detail+'" id="detail_'+i+'"><input type="hidden" value="'+result.spec[i].how_to_check+'" id="how_to_check_'+i+'">';
							tableSpec += '<table style="text-align:left;">';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+index+'. '+result.spec[i].point+'</td>';
							tableSpec += '</tr>';
							var url = '{{url("data_file/checksheet/sax/")}}/'+result.tag.model+'_BELL AND BOW_'+index+'.png';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;"><img src="'+url+'" style="width:200px;"></td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;">'+result.spec[i].detail+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+result.spec[i].how_to_check+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="">';
								tableSpec += '<div class="col-xs-6">';
								tableSpec += '<label class="containers">OK';
								if (result.spec_now.length > 0) {
									if (result.spec_now[i].results == 'OK') {
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="OK">';
									}else{
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
									}
								}else{
									tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
								}
								tableSpec += '<span class="checkmark"></span>';
								tableSpec += '</label>';
								tableSpec += '</div>';
								tableSpec += '<div class="col-xs-6" style="border-left:2px solid black">';
								tableSpec += '<label class="containers">NG';
								if (result.spec_now.length > 0) {
									if (result.spec_now[i].results == 'NG') {
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="NG">';
									}else{
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
									}
								}else{
									tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
								}
								tableSpec += '<span class="checkmark"></span>';
								tableSpec += '</label>';
								tableSpec += '</div>';
							tableSpec += '</td>';
							tableSpec += '</tr>';
							tableSpec += '</table>';
							tableSpec += '</td>';
							index++;
						}
					}
					tableSpec += '</tr>';
					tableSpec += '<tr>';
					tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;font-weight:bold;" >BODY</td>';
					var index = 1;
					for(var i = 0; i < result.spec.length;i++){
						if (result.spec[i].location == 'BODY') {
							tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;text-align:left;padding-left:7px;"><input type="hidden" value="'+result.spec[i].location+'" id="location_'+i+'"><input type="hidden" value="'+result.spec[i].point+'" id="point_'+i+'"><input type="hidden" value="'+result.spec[i].detail+'" id="detail_'+i+'"><input type="hidden" value="'+result.spec[i].how_to_check+'" id="how_to_check_'+i+'">';
							tableSpec += '<table style="text-align:left;">';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+index+'. '+result.spec[i].point;
							if (result.spec[i].point == 'No. Seri') {
								tableSpec += ' = <span style="font-weight:bold;color:red;font-size:18px">'+result.tag.serial_number+'</span>';
							}
							tableSpec += '</td>';
							tableSpec += '</tr>';
							var url = '{{url("data_file/checksheet/sax/")}}/'+result.tag.model+'_BODY_'+index+'.png';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;"><img src="'+url+'" style="width:200px;"></td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;">'+result.spec[i].detail+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+result.spec[i].how_to_check+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="">';
								tableSpec += '<div class="col-xs-6">';
								tableSpec += '<label class="containers">OK';
								if (result.spec_now.length > 0) {
									if (result.spec_now[i].results == 'OK') {
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="OK">';
									}else{
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
									}
								}else{
									tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
								}
								tableSpec += '<span class="checkmark"></span>';
								tableSpec += '</label>';
								tableSpec += '</div>';
								tableSpec += '<div class="col-xs-6" style="border-left:2px solid black">';
								tableSpec += '<label class="containers">NG';
								if (result.spec_now.length > 0) {
									if (result.spec_now[i].results == 'NG') {
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="NG">';
									}else{
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
									}
								}else{
									tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
								}
								tableSpec += '<span class="checkmark"></span>';
								tableSpec += '</label>';
								tableSpec += '</div>';
							tableSpec += '</td>';
							tableSpec += '</tr>';
							tableSpec += '</table>';
							tableSpec += '</td>';
							index++;
						}
					}
					tableSpec += '</tr>';
					tableSpec += '<tr>';
					tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;font-weight:bold;" >NECK</td>';
					var index = 1;
					for(var i = 0; i < result.spec.length;i++){
						if (result.spec[i].location == 'NECK') {
							tableSpec += '<td style="width: 3%; background-color: white; padding:0;font-size: 15px;text-align:left;padding-left:7px;"><input type="hidden" value="'+result.spec[i].location+'" id="location_'+i+'"><input type="hidden" value="'+result.spec[i].point+'" id="point_'+i+'"><input type="hidden" value="'+result.spec[i].detail+'" id="detail_'+i+'"><input type="hidden" value="'+result.spec[i].how_to_check+'" id="how_to_check_'+i+'">';
							tableSpec += '<table style="text-align:left;">';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+index+'. '+result.spec[i].point+'</td>';
							tableSpec += '</tr>';
							var url = '{{url("data_file/checksheet/sax/")}}/'+result.tag.model+'_NECK_'+index+'.png';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;"><img src="'+url+'" style="width:200px;"></td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:center;border:1px solid black;">'+result.spec[i].detail+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="text-align:left;">'+result.spec[i].how_to_check+'</td>';
							tableSpec += '</tr>';
							tableSpec += '<tr>';
							tableSpec += '<td style="">';
								tableSpec += '<div class="col-xs-6">';
								tableSpec += '<label class="containers">OK';
								if (result.spec_now.length > 0) {
									if (result.spec_now[i].results == 'OK') {
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="OK">';
									}else{
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
									}
								}else{
									tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="OK">';
								}
								tableSpec += '<span class="checkmark"></span>';
								tableSpec += '</label>';
								tableSpec += '</div>';
								tableSpec += '<div class="col-xs-6" style="border-left:2px solid black">';
								tableSpec += '<label class="containers">NG';
								if (result.spec_now.length > 0) {
									if (result.spec_now[i].results == 'NG') {
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" checked value="NG">';
									}else{
										tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
									}
								}else{
									tableSpec += '<input type="radio" name="condition_'+i+'" id="condition_'+i+'" value="NG">';
								}
								tableSpec += '<span class="checkmark"></span>';
								tableSpec += '</label>';
								tableSpec += '</div>';
							tableSpec += '</td>';
							tableSpec += '</tr>';
							tableSpec += '</table>';
							tableSpec += '</td>';
							index++;
						}
					}
					var point = [];
					var detail = [];
					for(var i = 0; i < result.spec.length;i++){
						if (result.spec[i].location == '') {
							point.push(result.spec[i].point);
							detail.push(result.spec[i].detail);
						}
					}
					var point_unik = point.filter(onlyUnique);
					var detail_unik = detail.filter(onlyUnique);
					tableSpec += '<td colspan="2" style="width: 3%; background-color: white; padding:0;font-size: 15px;">';
					tableSpec += '<table style="text-align:left;height:200px;width:100%">';
					tableSpec += '<tr>';
					tableSpec += '<td style="text-align:center;border:1px solid black;"></td>';
					for(var j = 0; j < detail_unik.length;j++){
						tableSpec += '<td style="text-align:left;padding-left:7px;border:1px solid black;font-weight:bold;">'+detail_unik[j]+'</td>';
					}
					tableSpec += '</tr>';
					for(var j = 0; j < point_unik.length;j++){
						tableSpec += '<tr>';
						tableSpec += '<td style="text-align:left;padding-left:7px;border:1px solid black;font-weight:bold;">'+point_unik[j]+'</td>';
						for(var i = 0; i < detail_unik.length;i++){
							for(var k = 0; k < result.spec.length;k++){
								if (result.spec[k].detail == detail_unik[i] && result.spec[k].point == point_unik[j]) {
									tableSpec += '<input type="hidden" id="location_'+k+'" value="'+result.spec[k].location+'">';
									tableSpec += '<input type="hidden" id="point_'+k+'" value="'+result.spec[k].point+'">';
									tableSpec += '<input type="hidden" id="detail_'+k+'" value="'+result.spec[k].detail+'">';
									tableSpec += '<input type="hidden" id="how_to_check_'+k+'" value="'+result.spec[k].how_to_check+'">';
									tableSpec += '<td style="text-align:right;border:1px solid black;">';
									tableSpec += '<div class="col-xs-6">';
									tableSpec += '<label class="containers">IYA';
									if (result.spec_now.length > 0) {
										if (result.spec_now[k].results == 'IYA') {
											tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" checked value="IYA">';
										}else{
											tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="IYA">';
										}
									}else{
										tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="IYA">';
									}
									  tableSpec += '<span class="checkmark"></span>';
									tableSpec += '</label>';
									tableSpec += '</div>';
									tableSpec += '<div class="col-xs-6" style="border-left:2px solid black;">';
									tableSpec += '<label class="containers" style="margin-left:10px;">TIDAK';
									if (result.spec_now.length > 0) {
										if (result.spec_now[k].results == 'TIDAK') {
											tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" checked value="TIDAK">';
										}else{
											tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="TIDAK">';
										}
									}else{
										tableSpec += '<input type="radio" name="condition_'+k+'" id="condition_'+k+'" value="TIDAK">';
									}
									  tableSpec += '<span class="checkmark"></span>';
									tableSpec += '</label>';
									tableSpec += '</div>';
									tableSpec += '</td>';
								}
							}
						}
						tableSpec += '</tr>';
					}
					tableSpec += '</table>';
					tableSpec += '</td>';
					tableSpec += '</tr>';
					tableSpec += '</tbody>';

					$('#specProductTable').append(tableSpec);

					count_spec = result.spec.length;
				}

				if (result.inventory != null) {
					if ('{{$location}}' == 'kensa-process') {
						$('#remark_qa').prop('disabled',true);
					}
					if ('{{$location}}' == 'qa-kensa' || '{{$location}}' == 'qa-visual' || '{{$location}}' == 'qa-fungsi') {
						$('#remark_process').prop('disabled',true);
					}
					if ('{{$location}}' == 'qa-audit') {
						$('#remark_qa').prop('disabled',true);
						$('#remark_process').prop('disabled',true);
					}
					if (result.inventory.remark != null) {
						// if (result.inventory.remark.split('_')[0] != '') {
							$('#remark_process').val(result.inventory.remark.split('_')[0]).trigger('change');
							if ('{{$location}}' == 'kensa-process' ) {
								$('#remark_qa').prop('disabled',true);
							}
						// }

						// if (result.inventory.remark.split('_')[1] != '') {
							$('#remark_qa').val(result.inventory.remark.split('_')[1]).trigger('change');
							if ('{{$location}}' == 'qa-kensa' || '{{$location}}' == 'qa-visual' || '{{$location}}' == 'qa-fungsi') {
								$('#remark_process').prop('disabled',true);
							}
						// }
					}
				}

				if ('{{$location}}' == 'kensa-process' && result.spec_process != null) {
					for(var i = 0; i < result.spec_process.length;i++){
						if (result.spec_process[i].category == 'Made In') {
							$('#made_in').val(result.spec_process[i].results).trigger('change');
						}
						if (result.spec_process[i].category == 'Body') {
							$('#body').val(result.spec_process[i].results).trigger('change');
						}
						if (result.spec_process[i].category == 'Bell') {
							$('#bell').val(result.spec_process[i].results).trigger('change');
						}
						if (result.spec_process[i].category == 'Side Cover') {
							$('#side_cover').val(result.spec_process[i].results).trigger('change');
						}
						if (result.spec_process[i].category == 'F-4') {
							$('#f_4').val(result.spec_process[i].results).trigger('change');
						}
						if (result.spec_process[i].category == 'J-3') {
							$('#j_3').val(result.spec_process[i].results).trigger('change');
						}
					}
				}

				// $('#specProductBody').append(specBody);

				onko = result.onko;
				ng_lists = result.ng_lists;
				operator = result.operator;
				// console.log(result.spec.length);
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#tag').val('');
			}
		});
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function repair(id) {
		$('#loading').show();
		var data = {
			repaired_by:$("#employee_id").val(),
			id:id
		}

		$.post('{{ url("input/assembly/repair/sax") }}', data, function(result, status, xhr){
			if(result.status){
				fetchAll();
				openSuccessGritter('Success',result.message);
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}

	function gantiKunci(id) {
		$('#loading').show();
		var data = {
			repaired_by:$("#employee_id").val(),
			id:id
		}

		$.post('{{ url("input/assembly/changekey/sax") }}', data, function(result, status, xhr){
			if(result.status){
				fetchAll();
				$('#loading').hide();
				openSuccessGritter('Success',result.message);
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}
	function cancelNg() {
		getNgChange();
		getOnkoChange();
		$("#value_atas").val('').trigger('change');
		$("#value_bawah").val('').trigger('change');
		$("#operator_id_before_select").val('').trigger('change');
		$('#modalNg').modal('hide')
	}


	function showNgDetail(ng_name) {
		getOnkoChange();
		$('#loading').show();
		$("#ngNari").hide();
		$("#ngMilihKunci").hide();
		if ($("#tag").val() == '') {
			audio_error.play();
			openErrorGritter('Error!','Scan Kartu RFID Dulu.');
			$('#loading').hide();
			$('#tag').focus();
			return false;
		}
		$('#ngDetail').html('');
		$('#onkoBody').html('');
		$('#operator_id_before_select').html('');
		var bodyDetail = '';
		var bodyNgOnko = '';
		if (ng_name == 'Renraku' || ng_name == 'Kagi Atari' || ng_name == 'Jarak Ken') {
			var index = 0;
			$.each(ng_lists, function(key, value) {
				if (value.ng_name == ng_name) {
					bodyDetail += '<div class="col-xs-4" style="padding-top: 10px;padding-left:0px;padding-right:0px">';
					bodyDetail += '<center><button class="btn btn-primary" id="'+value.ng_name+' - '+value.ng_detail+'" style="width: 99%;font-size: x-large;height:50px;" onclick="getNg(this.id)">'+value.ng_name+' - '+value.ng_detail;
					bodyDetail += '</button></center></div>';
					index++;
				}
			});

			if (index == 1) {
				getNg(ng_name);
			}else{
				getNgChange();
			}

			// $.each(onko, function(key, value) {
			// 	if (value.location == 'renraku') {
			// 		bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px;padding-left:0px;padding-right:0px">';
			// 		bodyNgOnko += '<center><button class="btn btn-warning" id="'+value.keynomor+'" style="width: 99%;font-size: large" onclick="getOnko(this.id)">'+value.keynomor;
			// 		bodyNgOnko += '</button></center></div>';
			// 	}
			// });

			bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px;padding-left:0px;padding-right:0px">';
			bodyNgOnko += '<center><button class="btn btn-warning" id="Lain-lain" style="width: 99%;font-size: large" onclick="getOnko(this.id)">Lain-lain';
			bodyNgOnko += '</button></center></div>';

			getOnko('Lain-lain');

			var opbfsel = "";
			opbfsel += '<option value="">Pilih Operator</option>';
			$.each(operator, function(key, value) {
				opbfsel += '<option value="'+value.employee_id+'">'+value.employee_id+' - '+value.name+' - '+value.location+'</option>';
			});

			$('#ngDetail').append(bodyDetail);
			$('#onkoBody').append(bodyNgOnko);
			$('#operator_id_before_select').append(opbfsel);

			$('#operator_id_before_select').select2({
				allowClear:true,
				dropdownParent: $('#modalNg'),
			});

			$("#ngMilihKunci").show();

			$('#modalNg').modal('show');
			$('#loading').hide();
		}else{
			var index = 0;
			$.each(ng_lists, function(key, value) {
				if (value.ng_name == ng_name) {
					bodyDetail += '<div class="col-xs-4" style="padding-top: 10px;padding-left:0px;padding-right:0px">';
					bodyDetail += '<center><button class="btn btn-primary" id="'+value.ng_name+' - '+value.ng_detail+'" style="width: 99%;font-size: x-large;height:50px;" onclick="getNg(this.id)">'+value.ng_name+' - '+value.ng_detail;
					bodyDetail += '</button></center></div>';
					index++;
				}
			});

			if (index == 1) {
				getNg(ng_name);
			}else{
				getNgChange();
			}

			$.each(onko, function(key, value) {
				if (value.location == 'all') {
					bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px;padding-left:0px;padding-right:0px">';
					bodyNgOnko += '<center><button class="btn btn-warning" id="'+value.keynomor+'" style="width: 99%;font-size: large" onclick="getOnko(this.id)">'+value.keynomor;
					bodyNgOnko += '</button></center></div>';
				}
			});

			var opbfsel = "";
			opbfsel += '<option value="">Pilih Operator</option>';
			$.each(operator, function(key, value) {
				opbfsel += '<option value="'+value.employee_id+'">'+value.employee_id+' - '+value.name+' - '+value.location+'</option>';
			});

			$('#ngDetail').append(bodyDetail);
			$('#onkoBody').append(bodyNgOnko);
			$('#operator_id_before_select').append(opbfsel);

			$('#operator_id_before_select').select2({
				allowClear:true,
				dropdownParent: $('#divOperator'),
			});

			if (ng_name == 'Nari') {
				$("#ngNari").show();
			}

			$('#modalNg').modal('show');
			$('#loading').hide();
		}
	}

	function getNg(value) {
		$('#ngDetail').hide();
		$('#ngDetailFix').show();
		$('#ngFix').html(value);
		$('#ngFix2').val(value);
	}

	function getNgChange() {
		$('#ngDetail').show();
		$('#ngDetailFix').hide();
		$('#ngFix').html("NG");
		$('#ngFix2').val("NG");
	}

	function getOnko(value) {
		$('#onkoBody').hide();
		$('#onkoBodyFix').show();
		$('#onkoFix').html(value);
		$('#onkoFix2').val(value);
	}

	function getOnkoChange() {
		$('#onkoBody').show();
		$('#onkoBodyFix').hide();
		$('#onkoFix').html("ONKO");
		$('#onkoFix2').val("ONKO");
	}

	function doesFileExist(urlToFile) {
	    var xhr = new XMLHttpRequest();
	    xhr.open('HEAD', urlToFile, false);
	    xhr.send();
	     
	    if (xhr.responseURL.includes('404')) {
	        return false;
	    } else {
	        return true;
	    }
	}

	function fetchNgTemp() {
		$('#loading').show();
		var data = {
			tag : $("#tag").val(),
			location : $("#location_now").text(),
		}

		$.get('{{ url("fetch/assembly/ng_temp/sax") }}', data, function(result, status, xhr){
			if(result.status){
				$('#ngTempBody').html('');
				var temp_ng = '';
				if (result.temp_ng != null && result.temp_ng.length > 0) {
					for(var i = 0; i < result.temp_ng.length;i++){
						temp_ng += '<tr>';
						temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.temp_ng[i].ng_name+'</td>';
						temp_ng += '<td style="background-color:white;font-size:13px;padding-right:5px;text-align:right;">';
						temp_ng += result.temp_ng[i].value_atas;
						if (result.temp_ng[i].value_bawah != null) {
							temp_ng += ' - '+result.temp_ng[i].value_bawah;
						}
						temp_ng += '</td>';
						if (result.temp_ng[i].value_lokasi != null) {
							temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.temp_ng[i].ongko+' - '+result.temp_ng[i].value_lokasi+'</td>';
						}else{
							temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.temp_ng[i].ongko+'</td>';
						}
						temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.temp_ng[i].name.split(' ')[0]+' '+result.temp_ng[i].name.split(' ')[1]+'</td>';
						temp_ng += '<td style="background-color:white;font-size:13px;text-align:center;"><button class="btn btn-danger" onclick="cancelNgTemp(\''+result.temp_ng[i].id_ng+'\')">Cancel</button></td>';
						temp_ng += '</tr>';
					}
					$('#ngTempBody').append(temp_ng);
				}

				$('#bodyTableQaAudit').html('');
				var qa_audit = '';

				if (result.qa_audit != null && result.qa_audit.length > 0) {
					for(var i = 0; i < result.qa_audit.length;i++){
						qa_audit += '<tr>';
						qa_audit += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:right;padding-right:7px;">'+(i+1)+'</td>';
						qa_audit += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.qa_audit[i].serial_number+'</td>';
						qa_audit += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.qa_audit[i].model+'</td>';
						qa_audit += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:right;padding-right:7px;">'+result.qa_audit[i].sedang_start_date+'</td>';
						qa_audit += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;padding-left:7px;">'+(result.qa_audit[i].name_fungsi || '')+'</td>';
						qa_audit += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;padding-left:7px;">'+(result.qa_audit[i].name_visual || '')+'</td>';

						qa_audit += '</tr>';
					}
				}

				$('#bodyTableQaAudit').append(qa_audit);

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#tag').val('');
			}
		});
	}

	function confNgTemp() {
		$("#loading").show();
		if ($('#ngFix2').val() == "NG" || $('#onkoFix2').val() == "ONKO") {
			$("#loading").hide();
			openErrorGritter('Error!','Pilih NG dan Kunci');
			return false;
		}

		var ng_name = $("#ngFix2").val();
		var onko = $("#onkoFix2").val();
		var operator = $("#operator_id_before_select").val();
		var value_atas = 1;
		var value_bawah = null;
		var value_lokasi = null;

		if (ng_name == 'Nari') {
			value_atas = $('#value_atas').val();
			value_bawah = $('#value_bawah').val();
			value_lokasi = $('#value_lokasi').val();

			if (value_atas == "" || value_bawah == "" || value_lokasi == "") {
				$("#loading").hide();
				openErrorGritter('Error!','Pilih NG dan Kunci');
				return false;
			}
		}else if(ng_name == 'Renraku' || ng_name == 'Kagi Atari' || ng_name == 'Jarak Ken'){
			value_atas = $('#value_atas_flek').val();
			value_bawah = $('#value_bawah_flek').val();

			if (value_atas == "" || value_bawah == "") {
				$("#loading").hide();
				openErrorGritter('Error!','Pilih NG dan Kunci');
				return false;
			}
		}

		if (!'{{$location}}'.match(/qa/gi)) {
			if (ng_name.match(/Kizu/gi) || ng_name.match(/kizu/gi)) {
				if (operator == '') {
					$("#loading").hide();
					openErrorGritter('Error!','Pilih Operator Penghasil NG');
					return false;
				}
			}
		}

		var data = {
			employee_id:$('#employee_id').val(),
			tag:$('#tag').val(),
			serial_number:$('#serial_number').text(),
			model:$('#model').text(),
			location:$('#location_now').text(),
			ng_name:$('#ngFix2').val(),
			ongko:$('#onkoFix2').val(),
			value_atas:value_atas,
			value_bawah:value_bawah,
			value_lokasi:value_lokasi,
			operator_id:$('#operator_id_before_select').val(),
			started_at:$('#started_at').val(),
		}

		$.post('{{ url("input/assembly/ng_temp/sax") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
				cancelNg();
				fetchNgTemp();
				$("#modalNg").modal('hide');
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		})
	}

	function cancelNgTemp(id) {
		$('#loading').show();
		var data = {
			id:id,
		}

		$.post('{{ url("delete/assembly/ng_temp/sax") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
				fetchNgTemp();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		})
	}

	function confirmAll(){
		$('#loading').show();
		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");
			$('#loading').hide();
			return false;
		}

		var location = [];
		var point = [];
		var detail = [];
		var how_to_check = [];
		var results = [];
		var remark_process = '';
		var remark_qa = '';
		var operator_qa = '';
		var operator_qa2 = '';

		if ('{{$location}}' == 'qa-visual' || '{{$location}}' == 'qa-kensa') {
			for(var i = 0; i < count_spec;i++){
				var decision = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            decision = $(this).val();
		        });
				if (decision == '') {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', 'Input Semua Spec Product');
					return false;
				}
			}
			for(var i = 0; i < count_spec;i++){
				var result_spec = '';
				$("input[name='condition_"+i+"']:checked").each(function (i) {
		            result_spec = $(this).val();
		        });
				
				location.push($('#location_'+i).val());
				point.push($('#point_'+i).val());
				detail.push($('#detail_'+i).val());
				how_to_check.push($('#how_to_check_'+i).val());
				results.push(result_spec);
			}
		}

		if ('{{$location}}' == 'qa-kensa' || '{{$location}}' == 'qa-visual' || '{{$location}}' == 'qa-fungsi') {
			if ($('#remark_qa').val() == '') {
				openErrorGritter('Error!', 'Pilih Penentuan SP');
				audio_error.play();
				$('#loading').hide();
				return false;
			}
		}

		if ('{{$location}}' == 'qa-audit') {
			if ($('#operator_qa').val() == '') {
				openErrorGritter('Error!', 'Pilih Operator yang Diaudit');
				audio_error.play();
				$('#loading').hide();
				return false;
			}
			operator_qa = $('#operator_qa').val();
			operator_qa2 = $('#operator_qa2').val();
		}
		remark_qa = $('#remark_qa').val();
		remark_process = $('#remark_process').val();

		var made_in = '';
		var body = '';
		var bell = '';
		var side_cover = '';
		var f_4 = '';
		var j_3 = '';

		if ('{{$location}}' == 'kensa-process') {
			made_in = $('#made_in').val();
			body = $('#body').val();
			bell = $('#bell').val();
			side_cover = $('#side_cover').val();
			f_4 = $('#f_4').val();
			j_3 = $('#j_3').val();

			if (made_in == '' ||
				body == '' ||
				bell == '' ||
				side_cover == '' ||
				f_4 == '' ||
				j_3 == '') {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', 'Input Semua Spec Kensa Process');
					return false;
			}
			if ($('#remark_process').val() == '') {
				openErrorGritter('Error!', 'Pilih Penentuan SP');
				audio_error.play();
				$('#loading').hide();
				return false;
			}
		}

		var data = {
			tag : $('#tag').val(),
			employee_id : $('#employee_id').val(),
			serial_number : $('#serial_number').text(),
			model : $('#model').text(),
			location : $('#location_now').text(),
			location_number : $('#line').text().split(' ')[1],
			started_at : $('#started_at').val(),
			origin_group_code : '043',
			spec_location:location,
			spec_point:point,
			spec_detail:detail,
			spec_how_to_check:how_to_check,
			spec_results:results,
			remark_process:remark_process,
			operator_qa:operator_qa,
			operator_qa2:operator_qa2,
			remark_qa:remark_qa,
			process_made_in:made_in,
			process_body:body,
			process_bell:bell,
			process_side_cover:side_cover,
			process_f_4:f_4,
			process_j_3:j_3,
			note : $('#note').val(),
		}

		$.post('{{ url("input/assembly/kensa/sax") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!','Input Kensa Sukses');
				$('#loading').hide();
				cancelAll();
				fetchNgTemp();
				$('#tag').focus();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#tag').val('');
			}
		});
	}

	function cancelAll() {
		getOnkoChange();
		$("#ngNari").hide();
		// $('#specProductBody').html('')
		$("#ngMilihKunci").hide();
		$('#specProductTable').html('');
		count_spec = 0;
		onko = null;
		ng_lists = null;
		operator = null;
		$('#operator_qa').val('');
		$('#operator_qa2').val('');
		$("#value_atas").val('').trigger('change');
		$("#value_bawah").val('').trigger('change');
		$("#remark_process").val('').trigger('change');
		$("#remark_qa").val('').trigger('change');
		$('#made_in').val('').trigger('change');
		$('#body').val('').trigger('change');
		$('#bell').val('').trigger('change');
		$('#side_cover').val('').trigger('change');
		$('#f_4').val('').trigger('change');
		$('#j_3').val('').trigger('change');
		$('#spec_product').html('');
		$("#tag").removeAttr('disabled');
		$("#tag").val('');
		$("#serial_number").html('');
		$("#model").html('');
		$("#details").html('');
		$("#ngHistoryBody").html('');
		$("#ngTempBody").html('');
		$('#started_at').val('');
		$('#confirm_status').val('');
		$("#serial_number_confirm").val('');
		$("#model_confirm").val('');
		$("#note").val('');
		$("#note_history").val('');
		$("#modalSerialConfirm").modal('hide');
		$('#tag').focus();
	}

	function confirmSerial() {
		$('#loading').show();
		var data = {
			serial_number:$('#serial_number_confirm').val(),
			model:$('#model_confirm').val(),
			tag:$('#tag').val(),
			location:$('#location_now').text(),
			location_number : $("#line").text().split(' ')[1],
			employee_id:$("#employee_id").val(),
			origin_group_code : '043',
		}

		$.post('{{ url("input/assembly/kensa/confirmation") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!','Sukses Konfirmasi Material');
				$('#loading').hide();
				$('#modalSerialConfirm').modal('hide');
				$('#confirm_status').val('confirmed');
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#serial_number').text() != ""){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#serial_number').text() != ""){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
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
</script>
@endsection
