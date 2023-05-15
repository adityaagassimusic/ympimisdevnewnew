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
	#historyLocationUpper{
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
	<div class="row" style="padding-left: 10px;padding-right: 10px">
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					@if($location == 'kensa-process')
						<div class="col-xs-4">
					@else
						<div class="col-xs-8">
					@endif
						<div class="row">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 0;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 16px;" colspan="2">Operator Kensa</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:16px; width: 30%;" id="op">-</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;" id="op2"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
						<div class="col-xs-4" style="padding-left: 5px;padding-right: 0px;">
							<div class="input-group">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;background-color: bisque">
									LOWER
								</div>
								<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan RFID Card LOWER..." required>
							</div>
						</div>
					@if($location == 'kensa-process')
							<div class="col-xs-4" style="padding-left: 5px;padding-right: 0px;" id="divUpper">
								<div class="input-group">
									<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag_upper" name="tag_upper" placeholder="Scan RFID Card UPPER..." required>
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;background-color: bisque">
										UPPER
									</div>
								</div>
							</div>
					@endif
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
					<div id="historyLocationUpper">
						<table class="table table-bordered" style="width: 100%;padding-top: 5px;">
							<tbody id="detailsUpper">
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
										PENENTUAN SE
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
									<select class="form-control select2" id="remark_process" style="width: 100%" data-placeholder="Penentuan SE">
										<option value=""></option>
										<option value="Normal">Normal</option>
										<option value="SP">SE</option>
									</select>
									</td>
									<td>
									<select class="form-control select2" id="remark_qa" style="width: 100%" data-placeholder="Penentuan SE">
										<option value=""></option>
										<option value="Normal">Normal</option>
										<option value="SP">SE</option>
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
								<th colspan="7" style="width: 40%; background-color: darkorange; padding:0;font-size: 15px;" >Temporary NG</th>
							</tr>
							<tr>
								<th style="width: 40%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Nama NG</th>
								<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Value / Jumlah</th>
								<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Onko</th>
								<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Oleh</th>
								<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Ganti Kunci</th>
								<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Repair</th>
								<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Action</th>
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
									<th colspan=4 style="width: 3%; background-color: chartreuse; padding:0;font-size: 15px;" >QA Audit Detail</th>
								</tr>
								<tr>
									<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
									<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Serial Number</th>
									<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Model</th>
									<th style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Time</th>
								</tr>
							</thead>
							<tbody id="bodyTableQaAudit">
							</tbody>
						</table>
					</div>
				</div>
			<?php endif ?>
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
				<div style="text-align: center;background-color: white;">
					<span style="font-weight: bold;">Pilih Operator QA yang Diaudit</span>
				</div>
				<select class="form-control select2" id="operator_qa" data-placeholder="Pilih Operator QA">
					<option value=""></option>
					@foreach($operator_qa as $operator_qa)
					<option value="{{$operator_qa->employee_id}}">{{$operator_qa->employee_id}} - {{$operator_qa->name}}</option>
					@endforeach
				</select>
			</div>
			<div>
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 49%" onclick="cancelAll()" class="btn btn-danger">CANCEL</button>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 49%" onclick="confirmAll()" class="btn btn-success">CONFIRM</button>

					<!-- <button id="conf1" style="width: 100%; margin-top: 30px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="clearUpper()" class="btn btn-warning">CLEAR UPPER</button> -->
				</center>
			</div>
		</div>
	</div>

</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
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
					<div id="ngTanpoawase" class="row" style="display: none">
						<div class="col-xs-12">
							<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #d199ff;padding: 5px">Pilih Indikator Jam untuk NG Tanpo Awase</h4>
						</div>
						<div class="col-xs-6" style="padding-right: 5px;" id="divAawal" align="center">
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
						<div class="col-xs-6" style="padding-left: 5px;" id="divAkhir" align="center">
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
							<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #e196ff;padding: 5px">Pilih Lokasi Tanpo Awase</h4>
						</div>
						<div class="col-xs-12" id="divLokasi" align="center">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_lokasi" data-placeholder="Pilih Lokasi">
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
							<h4 style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #b8ffd1;padding: 5px">Pilih Kunci Fleksibel</h4>
						</div>
						<div class="col-xs-6" style="padding-right: 5px;" id="divAawalFlek" align="center">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_atas_flek" data-placeholder="Pilih Kunci">
								<option value=""></option>
								<option value="1A">1A</option>
								<option value="1B">1B</option>
								<option value="2B">2B</option>
								<option value="2A">2A</option>
								<option value="3A">3A</option>
								<option value="3B">3B</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="7">7</option>
								<option value="10">10</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="21">21</option>
								<option value="20">20</option>
								<option value="24">24</option>
								<option value="12">12</option>
								<option value="17">17</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>

							</select>
						</div>
						<div class="col-xs-6" style="padding-left: 5px;" id="divAkhirFlek" align="center">
							<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="value_bawah_flek" data-placeholder="Pilih Kunci">
								<option value=""></option>
								<option value="1A">1A</option>
								<option value="1B">1B</option>
								<option value="2B">2B</option>
								<option value="2A">2A</option>
								<option value="3A">3A</option>
								<option value="3B">3B</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="7">7</option>
								<option value="10">10</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="21">21</option>
								<option value="20">20</option>
								<option value="24">24</option>
								<option value="12">12</option>
								<option value="17">17</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
							</select>
						</div>
					</div>
					<div id="divOperator" align="center">
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

<div class="modal fade" id="modalSerialConfirm">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body no-padding">
					<h4 id="judul_ng" style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">Pilih NG</h4>
					<div class="row">
						<div class="col-xs-12">
							<label style="font-size: 20px">Serial Number</label>
							<input type="text" readonly="true" name="serial_number_confirm" style="text-align: center;font-size: 30px;">
						</div>
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
							<label style="font-size: 20px">Model</label>
							<input type="text" readonly="true" name="model_confirm" style="text-align: center;font-size: 30px;">
						</div>
					</div>
					<div style="padding-top: 10px">
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
							<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confirmSerial()" class="btn btn-success">CONFIRM</button>
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
	var onko = null;
	var ng_lists = null;
	var operator = null;
	var operators = [];
	jQuery(document).ready(function() {
		$("#operator_qa").val('').trigger('change');
		$('#div_qa_audit').hide();
		if ('{{$location}}' == 'qa-audit') {
			$('#div_qa_audit').show();
		}
		if ('{{$location}}' == 'qa-audit') {
			fetchNgTemp();
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
			dropdownParent: $('#divLokasi'),
		});

		$('#value_atas_flek').select2({
			allowClear:true,
			dropdownParent: $('#divAawalFlek'),
		});
		$('#value_bawah_flek').select2({
			allowClear:true,
			dropdownParent: $('#divAkhirFlek'),
		});

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').removeAttr('disabled');
		$('#operator').val('');
		$('#tag').val('');
		$('#tag_upper').val('');
		onko = null;
		ng_lists = null;
		operator = null;
		operators = [];
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
				var data = {
					employee_id : $("#operator").val(),
					location:'{{$location}}'
				}

				$.get('{{ url("scan/assembly/operator_kensa/cl") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						if (result.employee.name.split(' ').length > 1) {
							$('#op2').html(result.employee.name.split(' ')[0]+' '+result.employee.name.split(' ')[1]);
						}else{
							$('#op2').html(result.employee.name.split(' ')[0]);
						}
						$('#employee_id').val(result.employee.employee_id);
						$('#operator').prop('disabled',true);
						$('#tag').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').removeAttr('disabled');
						$('#operator').val('');
					}
				});
		}
	});

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			fetchAll();
		}
	});

	$('#tag_upper').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').hide();
			$('#tag_upper').prop('disabled',true);
			fetchAllUpper();
		}
	});

	function fetchAll() {
		var data = {
			tag : $("#tag").val(),
			location : $("#location_now").text(),
			employee_id:$("#operator").val()
		}

		$.get('{{ url("scan/assembly/kensa/cl") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#modalOperator').modal('hide');
				$('#serial_number').html(result.tag.serial_number);
				$('#model').html(result.tag.model);
				$('#tag').prop('disabled',true);

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
					operators.push(result.details[i].employee_id);
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

				$('#note_history').val(notes.join(','));
				details += '</tr>';

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
					if (result.history_ng[i].name.split(' ').length > 1) {
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].name.split(' ')[0]+' '+result.history_ng[i].name.split(' ')[1]+'</td>';
					}else{
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].name.split(' ')[0]+'</td>';
					}
					if (result.history_ng[i].decision == null) {
						if ('{{$location}}' == 'kensa-process') {
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
						if ('{{$location}}' == 'kensa-process') {
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

				if (result.inventory != null) {
					if ('{{$location}}' == 'kensa-process') {
						$('#remark_qa').prop('disabled',true);
					}
					if ('{{$location}}' == 'qa-kensa') {
						$('#remark_process').prop('disabled',true);
					}
					if ('{{$location}}' == 'qa-audit') {
						$('#remark_process').prop('disabled',true);
						$('#remark_qa').prop('disabled',true);
					}
					if (result.inventory.remark != null) {
						// if (result.inventory.remark.split('_')[0] != '') {
							$('#remark_process').val(result.inventory.remark.split('_')[0]).trigger('change');
							if ('{{$location}}' == 'kensa-process') {
								$('#remark_qa').prop('disabled',true);
							}
						// }

						// if (result.inventory.remark.split('_')[1] != '') {
							$('#remark_qa').val(result.inventory.remark.split('_')[1]).trigger('change');
							if ('{{$location}}' == 'qa-kensa') {
								$('#remark_process').prop('disabled',true);
							}
						// }
					}
				}

				onko = result.onko;
				ng_lists = result.ng_lists;
				operator = result.emp;
				$('#loading').hide();
				if (result.tag.model == 'YCL450' || result.tag.model == 'YCL400AD') {
					$('#divUpper').hide();
				}
				if ('{{$location}}' == 'kensa-process') {
					$('#tag_upper').focus();
				}
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#tag').val('');
			}
		});
	}

	function fetchAllUpper() {
		var data = {
			tag : $("#tag_upper").val(),
			location : $("#location_now").text(),
			employee_id:$("#operator").val()
		}

		$.get('{{ url("scan/assembly/kensa/cl/upper") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#modalOperator').modal('hide');
				// $('#serial_number').html(result.tag.serial_number);
				// $('#model').html(result.tag.model);
				$('#tag_upper').prop('disabled',true);

				//Detail
				$('#detailsUpper').html('');
				var details = '';
				details += '<tr>';
				for(var i = 0; i < result.details.length;i++){
					if (i % 2 == 0) {
						var color = 'lightgreen';
					}else{
						var color = 'lightskyblue';
					}
					details += '<td style="background-color:'+color+';font-weight:bold;font-size:12px;text-align:left;padding-left:5px;">'+result.details[i].location.toUpperCase()+' UPPER</td>';
				}
				details += '</tr>';
				details += '<tr>';
				for(var i = 0; i < result.details.length;i++){
					if (result.details[i].name.split(' ').length > 1) {
						details += '<td style="background-color:white;font-size:12px;text-align:left;padding-left:5px;">'+result.details[i].name.split(' ')[0]+'<br>'+result.details[i].name.split(' ')[1]+'</td>';
					}else{
						details += '<td style="background-color:white;font-size:12px;text-align:left;padding-left:5px;">'+result.details[i].name.split(' ')[0]+'</td>';
					}
					operators.push(result.details[i].employee_id);
				}
				details += '</tr>';

				$('#detailsUpper').append(details);

				//History NG
				// $('#ngHistoryBody').html('');
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
					if (result.history_ng[i].name.split(' ').length > 1) {
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].name.split(' ')[0]+' '+result.history_ng[i].name.split(' ')[1]+'</td>';
					}else{
						history_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.history_ng[i].name.split(' ')[0]+'</td>';
					}
					if (result.history_ng[i].decision == null) {
						if ('{{$location}}' == 'kensa-process') {
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
						if ('{{$location}}' == 'kensa-process') {
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
				// $("#started_at").val('{{date("Y-m-d H:i:s")}}');

				// if (result.inventory != null) {
				// 	if ('{{$location}}' == 'kensa-process') {
				// 		$('#remark_qa').prop('disabled',true);
				// 	}
				// 	if ('{{$location}}' == 'qa-kensa') {
				// 		$('#remark_process').prop('disabled',true);
				// 	}
				// 	if (result.inventory.remark != null) {
				// 		// if (result.inventory.remark.split('_')[0] != '') {
				// 			$('#remark_process').val(result.inventory.remark.split('_')[0]).trigger('change');
				// 			if ('{{$location}}' == 'kensa-process') {
				// 				$('#remark_qa').prop('disabled',true);
				// 			}
				// 		// }

				// 		// if (result.inventory.remark.split('_')[1] != '') {
				// 			$('#remark_qa').val(result.inventory.remark.split('_')[1]).trigger('change');
				// 			if ('{{$location}}' == 'qa-kensa') {
				// 				$('#remark_process').prop('disabled',true);
				// 			}
				// 		// }
				// 	}
				// }

				onko = result.onko;
				ng_lists = result.ng_lists;
				// operator = result.operator;
				$('#loading').hide();
				if ('{{$location}}' == 'kensa-process') {
					$('#tag_upper').focus();
				}
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#tag_upper').val('');
				$('#tag_upper').removeAttr('disabled');
				$('#tag_upper').focus();
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

		$.post('{{ url("input/assembly/repair/cl") }}', data, function(result, status, xhr){
			if(result.status){
				fetchAll();
				fetchNgTemp();
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

		$.post('{{ url("input/assembly/changekey/cl") }}', data, function(result, status, xhr){
			if(result.status){
				fetchAll();
				fetchNgTemp();
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
		$("#value_atas_flek").val('').trigger('change');
		$("#value_lokasi").val('').trigger('change');
		$("#value_bawah_flek").val('').trigger('change');
		$("#operator_id_before_select").val('').trigger('change');
		$('#modalNg').modal('hide')
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}


	function showNgDetail(ng_name) {
		getOnkoChange();
		$('#loading').show();
		$("#ngTanpoawase").hide();
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
		if (ng_name == 'Renraku' || ng_name == 'Kagi Atari' || ng_name == 'Renraku (PR)' || ng_name == 'Kagi Atari (PR)' || ng_name == 'Susunan' || ng_name == 'Celah') {
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

			var op_unik = operators.filter(onlyUnique);

			var opbfsel = "";
			opbfsel += '<option value="">Pilih Operator</option>';
			for(var j = 0; j < op_unik.length;j++){
				var name = '';
				for(var i = 0; i < operator.length;i++){
					if (operator[i].employee_id == op_unik[j]) {
						name = operator[i].name;
					}
				}
				opbfsel += '<option value="'+op_unik[j]+'">'+op_unik[j]+' - '+name+'</option>';
			}

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

			var op_unik = operators.filter(onlyUnique);

			var opbfsel = "";
			opbfsel += '<option value="">Pilih Operator</option>';
			for(var j = 0; j < op_unik.length;j++){
				var name = '';
				for(var i = 0; i < operator.length;i++){
					if (operator[i].employee_id == op_unik[j]) {
						name = operator[i].name;
					}
				}
				opbfsel += '<option value="'+op_unik[j]+'">'+op_unik[j]+' - '+name+'</option>';
			}

			$('#ngDetail').append(bodyDetail);
			$('#onkoBody').append(bodyNgOnko);
			$('#operator_id_before_select').append(opbfsel);

			$('#operator_id_before_select').select2({
				allowClear:true,
				dropdownParent: $('#divOperator'),
			});

			if (ng_name == 'Tanpo Awase') {
				$("#ngTanpoawase").show();
			}

			// if (ng_name == 'Renraku' || ng_name == 'Kagi Atari' || ng_name == 'Zure') {
			// 	$("#ngMilihKunci").show();
			// }

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

		$.get('{{ url("fetch/assembly/ng_temp/cl") }}', data, function(result, status, xhr){
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
						if (result.temp_ng[i].name.split(' ').length > 1) {
							temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.temp_ng[i].name.split(' ')[0]+' '+result.temp_ng[i].name.split(' ')[1]+'</td>';
						}else{
							temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+result.temp_ng[i].name.split(' ')[0]+'</td>';
						}
						if (result.temp_ng[i].decision == null) {
							if ('{{$location}}' == 'kensa-process') {
								temp_ng += '<td style="background-color:white;font-size:13px;text-align:center;">';
								temp_ng += '<button class="btn btn-warning btn-sm" onclick="gantiKunci(\''+result.temp_ng[i].id+'\')">Ganti Kunci</button>';
								temp_ng += '</td>';
							}else{
								temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.temp_ng[i].decision || '')+'</td>';
							}
						}else{
							temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.temp_ng[i].decision || '')+'</td>';
						}
						if (result.temp_ng[i].repair_status == null) {
							if ('{{$location}}' == 'kensa-process') {
								temp_ng += '<td style="background-color:white;font-size:13px;text-align:center;">';
								temp_ng += '<button class="btn btn-success btn-sm" onclick="repair(\''+result.temp_ng[i].id+'\')">Repair</button>';
								temp_ng += '</td>';
							}else{
								temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.temp_ng[i].repair_status || '')+'</td>';
							}
						}else{
							temp_ng += '<td style="background-color:white;font-size:13px;padding-left:5px;text-align:left;">'+(result.temp_ng[i].repair_status || '')+'</td>';
						}
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

	function cancelNgTemp(id) {
		$('#loading').show();
		var data = {
			id:id,
		}

		$.post('{{ url("delete/assembly/ng_temp/cl") }}', data, function(result, status, xhr){
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

		if (ng_name == 'Tanpo Awase') {
			value_atas = $('#value_atas').val();
			value_bawah = $('#value_bawah').val();
			value_lokasi = $('#value_lokasi').val();

			if (value_atas == "" || value_bawah == "" || value_lokasi == "") {
				$("#loading").hide();
				openErrorGritter('Error!','Pilih NG dan Kunci');
				return false;
			}
		}else if(ng_name == 'Renraku' || ng_name == 'Kagi Atari' || ng_name == 'Renraku (PR)' || ng_name == 'Kagi Atari (PR)' || ng_name == 'Susunan' || ng_name == 'Celah'){
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
			value_lokasi:value_lokasi,
			value_bawah:value_bawah,
			operator_id:$('#operator_id_before_select').val(),
			started_at:$('#started_at').val(),
		}

		$.post('{{ url("input/assembly/ng_temp/cl") }}', data, function(result, status, xhr){
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

	function confirmAll(){
		$('#loading').show();

		var location = [];
		var point = [];
		var detail = [];
		var how_to_check = [];
		var results = [];
		var remark_process = '';
		var remark_qa = '';

		var operator_qa = '';

		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");
			$('#loading').hide();
			return false;
		}

		if ('{{$location}}' == 'qa-kensa') {

			if ($('#remark_qa').val() == '') {
				openErrorGritter('Error!', 'Pilih Penentuan SE');
				audio_error.play();
				$('#loading').hide();
				return false;
			}

			if($('#tag').val() == ""){
				openErrorGritter('Error!', 'Tag is empty');
				audio_error.play();
				$("#tag").val("");
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
		}
		remark_qa = $('#remark_qa').val();
		remark_process = $('#remark_process').val();

		// var made_in = '';
		// var body = '';
		// var bell = '';
		// var side_cover = '';
		// var f_4 = '';
		// var j_3 = '';

		if ('{{$location}}' == 'kensa-process') {
			// if($('#tag').val() == "" || $('#tag_upper').val() == ""){
			// 	openErrorGritter('Error!', 'Tag is empty');
			// 	audio_error.play();
			// 	$("#tag").val("");
			// 	$('#loading').hide();
			// 	return false;
			// }
		// 	made_in = $('#made_in').val();
		// 	body = $('#body').val();
		// 	bell = $('#bell').val();
		// 	side_cover = $('#side_cover').val();
		// 	f_4 = $('#f_4').val();
		// 	j_3 = $('#j_3').val();

		// 	if (made_in == '' ||
		// 		body == '' ||
		// 		bell == '' ||
		// 		side_cover == '' ||
		// 		f_4 == '' ||
		// 		j_3 == '') {
		// 		$('#loading').hide();
		// 		audio_error.play();
		// 		openErrorGritter('Error!', 'Input Semua Spec Kensa Process');
		// 		return false;
			if ($('#remark_process').val() == '') {
				openErrorGritter('Error!', 'Pilih Penentuan SE');
				audio_error.play();
				$('#loading').hide();
				return false;
			}
		}


		// }
		var data = {
			tag : $('#tag').val(),
			tag_upper : $('#tag_upper').val(),
			employee_id : $('#employee_id').val(),
			serial_number : $('#serial_number').text(),
			model : $('#model').text(),
			location : $('#location_now').text(),
			started_at : $('#started_at').val(),
			note : $('#note').val(),
			origin_group_code : '042',
			remark_process:remark_process,
			remark_qa:remark_qa,
			operator_qa:operator_qa,
			// process_made_in:made_in,
			// process_body:body,
			// process_bell:bell,
			// process_side_cover:side_cover,
			// process_f_4:f_4,
			// process_j_3:j_3,
		}

		$.post('{{ url("input/assembly/kensa/cl") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!','Input Kensa Sukses');
				$('#loading').hide();
				cancelAll();
				getOnkoChange();
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

	// function clearUpper(){
	// 	$('#loading').show();
	// 	if($('#tag').val() == ""){
	// 		openErrorGritter('Error!', 'Tag is empty');
	// 		audio_error.play();
	// 		$("#tag").val("");
	// 		$('#loading').hide();
	// 		return false;
	// 	}

	// 	var location = [];
	// 	var point = [];
	// 	var detail = [];
	// 	var how_to_check = [];
	// 	var results = [];
	// 	var remark_process = '';
	// 	var remark_qa = '';

	// 	if ('{{$location}}' == 'qa-kensa') {

	// 		if ($('#remark_qa').val() == '') {
	// 			openErrorGritter('Error!', 'Pilih Penentuan SE');
	// 			audio_error.play();
	// 			$('#loading').hide();
	// 			return false;
	// 		}
	// 	}
	// 	remark_qa = $('#remark_qa').val();
	// 	remark_process = $('#remark_process').val();

	// 	// var made_in = '';
	// 	// var body = '';
	// 	// var bell = '';
	// 	// var side_cover = '';
	// 	// var f_4 = '';
	// 	// var j_3 = '';

	// 	if ('{{$location}}' == 'kensa-process') {
	// 		if ($('#remark_process').val() == '') {
	// 			openErrorGritter('Error!', 'Pilih Penentuan SE');
	// 			audio_error.play();
	// 			$('#loading').hide();
	// 			return false;
	// 		}
	// 	}

	// 	var data = {
	// 		tag : $('#tag').val(),
	// 		employee_id : $('#employee_id').val(),
	// 		serial_number : $('#serial_number').text(),
	// 		model : $('#model').text(),
	// 		location : $('#location_now').text(),
	// 		started_at : $('#started_at').val(),
	// 		origin_group_code : '042',
	// 		remark_process:remark_process,
	// 		remark_qa:remark_qa,
	// 		// process_made_in:made_in,
	// 		// process_body:body,
	// 		// process_bell:bell,
	// 		// process_side_cover:side_cover,
	// 		// process_f_4:f_4,
	// 		// process_j_3:j_3,
	// 	}

	// 	$.post('{{ url("input/assembly/kensa/cl") }}', data, function(result, status, xhr){
	// 		if(result.status){
	// 			openSuccessGritter('Success!','Input Kensa Sukses');
	// 			$('#loading').hide();
	// 			cancelAll();
	// 			$('#tag').focus();
	// 		}
	// 		else{
	// 			$('#loading').hide();
	// 			audio_error.play();
	// 			openErrorGritter('Error', result.message);
	// 			$('#tag').val('');
	// 		}
	// 	});
	// }

	function cancelAll() {
		$('#divUpper').show();
		$('#tag_upper').val('');
		$('#tag_upper').removeAttr('disabled');
		$("#ngTanpoawase").hide();
		$("#ngMilihKunci").hide();
		$("#started_at").val('');
		onko = null;
		ng_lists = null;
		operator = null;
		$("#operator_qa").val('').trigger('change');
		$("#value_atas").val('').trigger('change');
		$("#value_bawah").val('').trigger('change');
		$("#remark_process").val('').trigger('change');
		$("#remark_qa").val('').trigger('change');
		// $('#made_in').val('').trigger('change');
		// $('#body').val('').trigger('change');
		// $('#bell').val('').trigger('change');
		// $('#side_cover').val('').trigger('change');
		// $('#f_4').val('').trigger('change');
		// $('#j_3').val('').trigger('change');
		$("#tag").removeAttr('disabled');
		$("#tag").val('');
		$("#serial_number").html('');
		$("#model").html('');
		$("#details").html('');
		$("#note_history").val('');
		$("#note").val('');
		$("#detailsUpper").html('');
		$("#ngHistoryBody").html('');
		$("#ngTempBody").html('');
		$('#tag').focus();
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
