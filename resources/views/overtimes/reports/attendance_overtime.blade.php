@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<meta name="mobile-web-app-capable" content="yes">
<meta name="viewport" content="initial-scale=1">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		vertical-align: middle;

	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
		vertical-align: middle;
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		color: black;
	}

	#loading,
	#error {
		display: none;
	}

	#slip {
		text-align: center;
		font-weight: bold;
	}

	.input {
		text-align: center;
		font-weight: bold;
	}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading"
	style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
	<p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
		<span style="font-size: 50px;">Please wait ... </span><br>
		<span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>

<div class="row">
	<div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px;">
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<div class="box">
				<div class="box-body">
					<div style="padding-bottom: 10px;">
						<div class="col-xs-10">
							<center style="background-color: #33d6ff;border-top:0px;border-left:0px;border-right:0px;">
								<span style="font-size: 25px;text-align: center;font-weight: bold;">
									LIST ATTENDANCE ORDER EXTRA FOOD
								</span>
							</center>

						</div>
						<div class="col-xs-2">
							<button class="btn btn-default btn-sm pull-right" onclick="getHistory()">
								&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
							</button>
						</div>
					</div>
					<div class="panel-body">
						<div class="col-xs-12" id="last_update" style="padding: 0%;margin-top: 10px;">
							<p class="pull-right" style="margin: 0px; font-size: 10pt;">
								<i class="fa fa-fw fa-clock-o"></i>Last Updated:
								<span id="last_updated"></span>
							</p>
						</div>

						<div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
							<button class="btn btn-default" style="width: 100%; font-weight: bold;"
							onclick="getHistoryFilter('IN')">
							<span><i class="fa fa-arrow-circle-o-down"></i>&nbsp;&nbsp;IN</span>
							<br>
							<span id="countExtraFoodIn" style="font-size: 4vw;">0</span>
						</button>
					</div>
					<div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
						<button class="btn btn-default" style="width: 100%; font-weight: bold;"
						onclick="getHistoryFilter('OUT')">
						<span><i class="fa fa-arrow-circle-o-up "></i></i>&nbsp;&nbsp;OUT</span>
						<br>
						<span id="countExtraFoodOut" style="font-size: 4vw;">0</span>
					</button>
				</div>
				<div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
					<button class="btn btn-default" style="width: 100%; font-weight: bold;"
					onclick="getHistoryFilter('STOCK')">
					<span><i class="fa fa-credit-card-alt"></i></i>&nbsp;&nbsp;STOCK</span>
					<br>
					<span id="countExtraFoodSisa" style="font-size: 4vw;">0</span>
				</button>
			</div>
		</div>

		<div class="input-group input-group-lg" style="border: 1px solid black;">
			<div class="input-group-addon" id="icon-serial"
			style="font-weight: bold; border-color: none; font-size: 18px;">
			<i class="fa fa-qrcode"></i>
		</div>
		<input type="text" class="form-control" style="text-align:center;" placeholder="SCAN TAP RFID CARD / KETIKAN NIK" id="tag">
		<div class="input-group-addon" id="icon-serial"
		style="font-weight: bold; border-color: none; font-size: 18px;">
		<i class="fa fa-barcode"></i>
	</div>
</div>

<div style="padding-top: 10px;">
	<div class="col-xs-12 col-md-6 col-lg-6">
		<div class="row">
			<center style="background-color: #ffd333;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NIK</span></center>
			<center style="border: 1px solid black">
				<span style="font-size: 30px;font-weight: bold;" id="nik">-</span>
			</center>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-lg-6">
		<div class="row">
			<center style="background-color: #ffd333;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NAME</span></center>
			<center style="border: 1px solid black">
				<span style="font-size: 30px;font-weight: bold;" id="name">-</span>
			</center>
		</div>
	</div>
</div>

<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll !important;">
	<table id="tableAttendance" class="table table-bordered table-striped table-hover" style="width:100%;">
		<thead style="background-color: rgb(126,86,134);" id="headTableAttendance">
			<tr>
				<th style="color:white;width:1%">ID</th>
				<th style="color:white;width:1%">Date</th>
				<th style="color:white;width:1%">Employee ID</th>
				<th style="color:white;width:3%">Name</th>
				<th style="color:white;width:4%">Section</th>
				<th style="color:white;width:4%">Shift</th>
				<th style="color:white;width:1%">Keterangan</th>
				<th style="color:white;width:2%">Time At</th>
			</tr>
		</thead>
		<tbody id="bodyTableAttendance">
		</tbody>
		<tfoot id="footTableAttendance">
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
	
</div>
</div>
</div>
</div>
</div>


<div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px;" >
	<div class="nav-tabs-custom" style="margin-top: 1%;">
		<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
			<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Extra Food NO LIST</a>
			</li>
			<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">OVERTIME MAKAN</a>
			</li>

		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
				<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
					<div class="box">
						<div class="box-body">
							<div style="padding-bottom: 10px;">
								<div class="col-xs-10">
									<center style="background-color: #a16eac;border-top:0px;border-left:0px;border-right:0px;">
										<span style="font-size: 25px;text-align: center;font-weight: bold;">
											ATTENDANCE EXTRA FOOD NO LIST DATA 
										</span>
									</center>
								</div>
								<div class="col-xs-2">
									<button class="btn btn-default btn-sm pull-right" onclick="getTambahan('put')">
										&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
									</button>
								</div>
							</div>
							<div class="panel-body">
								<div class="col-xs-12" id="last_update" style="padding: 0%;margin-top: 10px;">
									<p class="pull-right" style="margin: 0px; font-size: 10pt;">
										<i class="fa fa-fw fa-clock-o"></i>Last Updated:
										<span id="last_updated"></span>
									</p>
								</div>
								<div class="col-xs-4" style="padding: 10px 5px 10px 5px;pointer-events: none; ">
									<button class="btn btn-default" style="width: 100%; font-weight: bold;">
										<span><i class="fa fa-arrow-circle-o-up "></i></i>&nbsp;&nbsp;OUT</span>
										<br>
										<span id="countExtraTamOut" style="font-size: 4vw;">0</span>
									</button>
								</div>

							</div>
<!-- 
							<div class="input-group input-group-lg" style="border: 1px solid black;">
								<div class="input-group-addon" id="icon-serial"
								style="font-weight: bold; border-color: none; font-size: 18px;">
								<i class="fa fa-qrcode"></i>
							</div>
							<input type="text" class="form-control" style="text-align:center;" placeholder="SCAN TAP RFID CARD / KETIKAN NIK" id="tag5">
							<div class="input-group-addon" id="icon-serial"
							style="font-weight: bold; border-color: none; font-size: 18px;">
							<i class="fa fa-barcode"></i>
						</div>
					</div> -->

					<div style="margin-top:10px;" id="select3">
						<select class="form-control select3" data-placeholder="Select NIK" id="select_emp" name="select_emp" style="width: 100%;margin-bottom: 10px;" onChange="selectEmpa(this.value)">
							<option value=""></option>
							 @foreach($data_emp as $row)
                      <option value="{{$row->employee_id}}__{{$row->name}}">{{$row->employee_id}} - {{$row->name}}</option>
                      @endforeach
							
						</select>
					</div>

					<div style="padding-top: 10px;">
						<div class="col-xs-12 col-md-6 col-lg-6">
							<div class="row">
								<center style="background-color: #169cdc;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NIK</span></center>
								<center style="border: 1px solid black">
									<span style="font-size: 30px;font-weight: bold;" id="nik5">-</span>
								</center>
							</div>
						</div>
						<div class="col-xs-12 col-md-6 col-lg-6">
							<div class="row">
								<center style="background-color: #169cdc;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NAME</span></center>
								<center style="border: 1px solid black">
									<span style="font-size: 30px;font-weight: bold;" id="name5">-</span>
								</center>
							</div>
							<span style="font-size: 30px;font-weight: bold; display: none;" id="shift5">-</span>

						</div>
					</div>

					<div class="col-md-12" style="padding-top:10px" id="tabInput" hidden>
						<button class="btn btn-danger pull-left" onclick="cancelRequest1()" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="createRequest1()">Save</button>
					</div>

					<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll !important;">
						<table id="tableAttendance5" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgb(126,86,134,.7);" id="headTableAttendance3">
								<tr>
									<th style="color:white;width:1%">ID</th>
									<th style="color:white;width:1%">Date</th>
									<th style="color:white;width:1%">Employee ID</th>
									<th style="color:white;width:3%">Name</th>
									<th style="color:white;width:4%">Section</th>
									<th style="color:white;width:4%">Shift</th>
									<th style="color:white;width:1%">Keterangan</th>
									<th style="color:white;width:1%">Time At</th>
									<th style="color:white;width:1%">Action</th>
								</tr>
							</thead>
							<tbody id="bodyTableAttendance5">
							</tbody>
							<tfoot id="footTableAttendance5">
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="tab-pane" id="tab_2" style="overflow-x: auto;">
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<div class="box">
				<div class="box-body">
					<div style="padding-bottom: 10px;">
						<div class="col-xs-10">
							<center style="background-color: #a16eac;border-top:0px;border-left:0px;border-right:0px;">
								<span style="font-size: 25px;text-align: center;font-weight: bold;">
									LIST ATTENDANCE ORDER MAKAN OVERTIME
								</span>
							</center>
						</div>
						<div class="col-xs-2">
							<button class="btn btn-default btn-sm pull-right" onclick="getHistory3()">
								&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
							</button>
						</div>
					</div>
					<div class="panel-body">
						<div class="col-xs-12" id="last_update" style="padding: 0%;margin-top: 10px;">
							<p class="pull-right" style="margin: 0px; font-size: 10pt;">
								<i class="fa fa-fw fa-clock-o"></i>Last Updated:
								<span id="last_updated"></span>
							</p>
						</div>

						<div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
							<button class="btn btn-default" style="width: 100%; font-weight: bold;"
							onclick="getHistoryFilter3('IN')">
							<span><i class="fa fa-arrow-circle-o-down"></i>&nbsp;&nbsp;IN</span>
							<br>
							<span id="countOvertimeIn" style="font-size: 4vw;">0</span>
						</button>
					</div>
					<div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
						<button class="btn btn-default" style="width: 100%; font-weight: bold;"
						onclick="getHistoryFilter3('OUT')">
						<span><i class="fa fa-arrow-circle-o-up "></i></i>&nbsp;&nbsp;OUT</span>
						<br>
						<span id="countOvertimeOut" style="font-size: 4vw;">0</span>
					</button>
				</div>
				<div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
					<button class="btn btn-default" style="width: 100%; font-weight: bold;"
					onclick="getHistoryFilter3('STOCK')">
					<span><i class="fa fa-credit-card-alt"></i></i>&nbsp;&nbsp;STOCK</span>
					<br>
					<span id="countOvertimeSisa" style="font-size: 4vw;">0</span>
				</button>
			</div>
		</div>

		<div class="input-group input-group-lg" style="border: 1px solid black;">
			<div class="input-group-addon" id="icon-serial"
			style="font-weight: bold; border-color: none; font-size: 18px;">
			<i class="fa fa-qrcode"></i>
		</div>
		<input type="text" class="form-control" style="text-align:center;" placeholder="SCAN TAP RFID CARD / KETIKAN NIK" id="tag3">
		<div class="input-group-addon" id="icon-serial"
		style="font-weight: bold; border-color: none; font-size: 18px;">
		<i class="fa fa-barcode"></i>
	</div>
</div>
<div style="padding-top: 10px;">
	<div class="col-xs-12 col-md-6 col-lg-6">
		<div class="row">
			<center style="background-color: #169cdc;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NIK</span></center>
			<center style="border: 1px solid black">
				<span style="font-size: 30px;font-weight: bold;" id="nik3">-</span>
			</center>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-lg-6">
		<div class="row">
			<center style="background-color: #169cdc;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NAME</span></center>
			<center style="border: 1px solid black">
				<span style="font-size: 30px;font-weight: bold;" id="name3">-</span>
			</center>
		</div>
	</div>
</div>

<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll !important;">
	<table id="tableAttendance3" class="table table-bordered table-striped table-hover">
		<thead style="background-color: rgb(126,86,134,.7);" id="headTableAttendance3">
			<tr>
				<th style="color:white;width:1%">ID</th>
				<th style="color:white;width:1%">Date</th>
				<th style="color:white;width:1%">Employee ID</th>
				<th style="color:white;width:3%">Name</th>
				<th style="color:white;width:4%">Section</th>
				<th style="color:white;width:4%">Shift</th>
				<th style="color:white;width:1%">Keterangan</th>
				<th style="color:white;width:1%">Time At</th>
			</tr>
		</thead>
		<tbody id="bodyTableAttendance3">
		</tbody>
		<tfoot id="footTableAttendance3">
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<!-- <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px;">
	<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
		<div class="box">
			<div class="box-body">
				<div style="padding-bottom: 10px;">
					<center style="background-color: #a16eac;border-top:0px;border-left:0px;border-right:0px;">
						<span style="font-size: 25px;text-align: center;font-weight: bold;">
							LIST ATTENDANCE ORDER MAKAN RAMADHAN
						</span>
					</center>
				</div>

				<div class="input-group input-group-lg" style="border: 1px solid black;">
					<div class="input-group-addon" id="icon-serial"
					style="font-weight: bold; border-color: none; font-size: 18px;">
					<i class="fa fa-qrcode"></i>
				</div>
				<input type="text" class="form-control" style="text-align:center;" placeholder="SCAN TAP RFID CARD" id="tag2">
				<div class="input-group-addon" id="icon-serial"
				style="font-weight: bold; border-color: none; font-size: 18px;">
				<i class="fa fa-barcode"></i>
			</div>
		</div>
		<div style="padding-top: 10px;">
			<div class="col-xs-12 col-md-6 col-lg-6">
				<div class="row">
					<center style="background-color: #169cdc;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NIK</span></center>
					<center style="border: 1px solid black">
						<span style="font-size: 30px;font-weight: bold;" id="nik2">-</span>
					</center>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-lg-6">
				<div class="row">
					<center style="background-color: #169cdc;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NAME</span></center>
					<center style="border: 1px solid black">
						<span style="font-size: 30px;font-weight: bold;" id="name2">-</span>
					</center>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-top: 20px;">
			<div class="row">
				<table id="tableAttendance2" class="table table-bordered table-striped table-hover">
					<thead style="background-color: rgb(126,86,134,.7);" id="headTableAttendance">
						<tr>
							<th style="color:white;width:1%">ID</th>
							<th style="color:white;width:1%">Employee ID</th>
							<th style="color:white;width:4%">Name</th>
							<th style="color:white;width:7%">Section</th>
							<th style="color:white;width:7%">Shift</th>
							<th style="color:white;width:2%">Keterangan</th>
							<th style="color:white;width:2%">Time At</th>
						</tr>
					</thead>
					<tbody id="bodyTableAttendance2">
					</tbody>
					<tfoot id="footTableAttendance2">
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
</div>
</div>
</div>
</div> -->
</div>

</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var data_rmds = [];
	var datas_rovs = [];
	var datas_detail_extra = [];
	var datas_detail_extra_tam = [];
	var datas_detail_overtime = [];



	jQuery(document).ready(function() {

		$('.select2').select2({
			minimumInputLength: 3,
			allowClear: 'true'
		});

		$('.select3').select2({
			dropdownParent: $('#select3'),
			allowClear:true
		});

		$('#tag').focus();
		getHistory();
		getTambahan();

		$("#select_emp").val("");

	});

	var audio_error = new Audio('{{ url('sounds/error_suara.mp3') }}');
	var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

	var mirai_pi = '';
	var ymes_pi = '';
	var sum_ymes_qty = 0;
	var sum_ymes_book = 0;
	var sum_pi_qty = 0;
	var sum_pi_book = 0;

	$('#tag').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			if($("#tag").val().length >= 7){
				var data = {
					tag : $("#tag").val(),
				}
				
				$.get('{{ url("fetch/scan/overtime/attendance") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Scan dan Data Berhasil Disimpan');
						$('#loading').hide();
						$('#nik').html(result.employee.employee_id);
						$('#name').html(result.employee.name);
						audio_ok.play();
						$('#tag').removeAttr('disabled');
						$('#tag').val("");
						$('#tag').focus();
						getHistory();
					}else{
						$('#loading').hide();
						$('#tag').removeAttr('disabled');
						$('#tag').val("");
						$('#tag').focus();
						getHistory();
						audio_error.play();
						openErrorGritter('Error!',result.message);
					}
				})
			}else{
				$('#loading').hide();
				$('#tag').removeAttr('disabled');
				$('#tag').val("");
				$('#tag').focus();
				getHistory();
				audio_error.play();
				openErrorGritter('Error!','Masukkan ID Card Dengan Benar');
			}
		}
	});

	
	$('#tag3').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			if($("#tag3").val().length >= 7){
				var data = {
					tag2 : $("#tag3").val(),
				}
				
				$.get('{{ url("fetch/scan/overtime/attendance2") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Scan dan Data Berhasil Disimpan');
						$('#loading').hide();
						$('#nik3').html(result.employee.employee_id);
						$('#name3').html(result.employee.name);
						audio_ok.play();
						$('#tag3').removeAttr('disabled');
						$('#tag3').val("");
						$('#tag3').focus();
						getHistory();
						
					}else{
						$('#loading').hide();
						$('#tag3').removeAttr('disabled');
						$('#tag3').val("");
						$('#tag3').focus();
						getHistory3();
						audio_error.play();
						openErrorGritter('Error!',result.message);
					}
				})
			}else{
				$('#loading').hide();
				$('#tag3').removeAttr('disabled');
				$('#tag3').val("");
				$('#tag3').focus();
				getHistory3();
				audio_error.play();
				openErrorGritter('Error!','Masukkan ID Card Dengan Benar');
			}
		}
	});


	// $('#tag5').keyup(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		$('#loading').show();
	// 		if($("#tag5").val().length >= 7){
	// 			var data = {
	// 				tag5 : $("#tag5").val(),
	// 			}
				
	// 			$.get('{{ url("fetch/scan/overtime/attendance5") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success','Scan dan Data Berhasil Disimpan');
	// 					$('#loading').hide();
	// 					$('#nik5').html(result.employee.employee_id);
	// 					$('#name5').html(result.employee.name);
	// 					audio_ok.play();
	// 					$('#tag5').removeAttr('disabled');
	// 					$('#tag5').val("");
	// 					$('#tag5').focus();
	// 					getTambahan();
	// 				}else{
	// 					$('#loading').hide();
	// 					$('#tag5').removeAttr('disabled');
	// 					$('#tag5').val("");
	// 					$('#tag5').focus();
	// 					getTambahan();
	// 					audio_error.play();
	// 					openErrorGritter('Error!',result.message);
	// 				}
	// 			})
	// 		}else{
	// 			$('#loading').hide();
	// 			$('#tag5').removeAttr('disabled');
	// 			$('#tag5').val("");
	// 			$('#tag5').focus();
	// 			getTambahan();
	// 			audio_error.play();
	// 			openErrorGritter('Error!','Masukkan ID Card Dengan Benar');
	// 		}
	// 	}
	// });


	

	function getHistory() {
		$('#loading').show();


		$.get('{{ url("fetch/list/overtime/attendance") }}', function(result, status, xhr){
			$('#loading').hide();
			$("#bodyTableAttendance").empty();
			$('#tableAttendance').DataTable().clear();
			$('#tableAttendance').DataTable().destroy();
			var body = "";
			var no = 1;
			var nos = 0;
			var out1 = 0;

			data_rmds = [];
			data_rmds = result.datas_rmd;

			datas_rovs = [];
			datas_rovs = result.datas_rov;


			$.each(result.datas, function(index, value){
				if (value.remark == "Order Extra Food") {

					datas_detail_extra.push({
						time_in: value.time_in,
						employee_id: value.employee_id,
						name: value.name,
						section: value.section,
						shift: value.shift+" | "+value.shiftdaily_code,
						attend_date: value.attend_date  
					});

					body += "<tr>";
					body += "<td>"+no+"</td>";
					body += "<td>"+value.time_in+"</td>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+value.section+"</td>";
					if (value.shiftdaily_code != null) {
						body += "<td>"+value.shift+" | "+value.shiftdaily_code+"</td>";

					}else{

						body += "<td>"+value.shift+"</td>";

					}
					if (value.attend_date == null) {
						body += "<td><label class='label label-info'>Belum Hadir</label></td>";
						body += "<td>-</td>";
					}else{
						body += "<td><label class='label label-success'>Hadir</label></td>";
						body += "<td>"+value.attend_date+"</td>";
						out1++;
					}
					body += "</tr>";
					no++;
					nos++;
				}

			})
			$("#countExtraFoodOut").html(out1);
			$("#countExtraFoodIn").html(nos);			
			$("#countExtraFoodSisa").html(nos-out1);

			$("#bodyTableAttendance").append(body);
			$('#tableAttendance tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
			} );

			var table = $('#tableAttendance').DataTable({
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
				},initComplete: function() {
					this.api()
					.columns([1,5])
					.every(function(dd) {
						var column = this;
						var theadname = $("#tableAttendance th").eq([dd])
						.text();
						var select = $(
							'<select><option value="" style="font-size:11px;">All</option></select>'
							)
						.appendTo($(column.footer()).empty())
						.on('change', function() {
							var val = $.fn.dataTable.util
							.escapeRegex($(this)
								.val());

							column.search(val ? '^' + val + '$' :
								'', true,
								false)
							.draw();
						});
						column
						.data()
						.unique()
						.sort()
						.each(function(d, j) {
							var vals = d;
							if ($("#tableAttendance th").eq([dd])
								.text() ==
								'Category') {
								vals = d.split(' ')[0];
						}
						select.append(
							'<option style="font-size:12px;"  value="' +
							d + '">' + vals + '</option>');
					});
					});
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": false,
				"aaSorting": [[ 0, "desc" ]]
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

			$('#tableAttendance tfoot tr').appendTo('#tableAttendance thead');
			
			getHistory3('no');
		})
}

function getHistoryFilter(st) {
	$('#loading').show();

	var timeoutID = setTimeout(function () {

		$('#loading').hide();
	}, 1000);

	$("#bodyTableAttendance").empty();
	$('#tableAttendance').DataTable().clear();
	$('#tableAttendance').DataTable().destroy();
	var body = "";
	var no = 1;


	$.each(datas_detail_extra, function(index, value){
		if (st == "OUT") {
			if (value.attend_date != null) {

				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.time_in+"</td>";
				body += "<td>"+value.employee_id+"</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.section+"</td>";
				body += "<td>"+value.shift+"</td>";

				if (value.attend_date == null) {
					body += "<td><label class='label label-info'>Belum Hadir</label></td>";
					body += "<td>-</td>";
				}else{
					body += "<td><label class='label label-success'>Hadir</label></td>";
					body += "<td>"+value.attend_date+"</td>";

				}
				body += "</tr>";
				no++;
			}

		}else if (st == "IN") {
			body += "<tr>";
			body += "<td>"+no+"</td>";
			body += "<td>"+value.time_in+"</td>";
			body += "<td>"+value.employee_id+"</td>";
			body += "<td>"+value.name+"</td>";
			body += "<td>"+value.section+"</td>";
			body += "<td>"+value.shift+"</td>";

			if (value.attend_date == null) {
				body += "<td><label class='label label-info'>Belum Hadir</label></td>";
				body += "<td>-</td>";
			}else{
				body += "<td><label class='label label-success'>Hadir</label></td>";
				body += "<td>"+value.attend_date+"</td>";

			}
			body += "</tr>";
			no++;
		}else{
			if (value.attend_date == null) {
				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.time_in+"</td>";
				body += "<td>"+value.employee_id+"</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.section+"</td>";
				body += "<td>"+value.shift+"</td>";
				if (value.attend_date == null) {
					body += "<td><label class='label label-info'>Belum Hadir</label></td>";
					body += "<td>-</td>";
				}else{
					body += "<td><label class='label label-success'>Hadir</label></td>";
					body += "<td>"+value.attend_date+"</td>";

				}
				body += "</tr>";
				no++;
			}
		}

	})

	$("#bodyTableAttendance").append(body);
	$('#tableAttendance tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
	} );

	var table = $('#tableAttendance').DataTable({
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
		},initComplete: function() {
			this.api()
			.columns([1,5])
			.every(function(dd) {
				var column = this;
				var theadname = $("#tableAttendance th").eq([dd])
				.text();
				var select = $(
					'<select><option value="" style="font-size:11px;">All</option></select>'
					)
				.appendTo($(column.footer()).empty())
				.on('change', function() {
					var val = $.fn.dataTable.util
					.escapeRegex($(this)
						.val());

					column.search(val ? '^' + val + '$' :
						'', true,
						false)
					.draw();
				});
				column
				.data()
				.unique()
				.sort()
				.each(function(d, j) {
					var vals = d;
					if ($("#tableAttendance th").eq([dd])
						.text() ==
						'Category') {
						vals = d.split(' ')[0];
				}
				select.append(
					'<option style="font-size:12px;"  value="' +
					d + '">' + vals + '</option>');
			});
			});
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': false,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": false,
		"aaSorting": [[ 0, "desc" ]]
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

	$('#tableAttendance tfoot tr').appendTo('#tableAttendance thead');

	
}


function selectEmpa(st) {
	var data_name = st.split('__');

	var names = [];
	var names1 = [];

	if (st != "") {
		for (var i = 0; i < datas_detail_extra.length; i++) {
			if (datas_detail_extra[i].employee_id == data_name[0]) {	
				names = [];
				names.push(datas_detail_extra[i].employee_id);
			}
		}

		for (var i = 0; i < datas_detail_extra_tam.length; i++) {
			if (datas_detail_extra_tam[i].employee_id == data_name[0]) {	
				names1 = [];
				names1.push(datas_detail_extra_tam[i].employee_id);
			}
		}

		if (names == data_name[0]) {
			$('#tabInput').hide();
			$('#nik5').html("-");
			$('#name5').html("-");
			names = [];
			$("#select_emp").val("").trigger('change');
			alert('Data Sudah Ada di List.Gunakan NIK untuk Scan Atau Periksa Name Tag Anda Ke Bagian HR Jika Tidak Bisa');
			return false;

		}else if (names1 == data_name[0]) {
			$('#tabInput').hide();
			$('#nik5').html("-");
			$('#name5').html("-");
			names1 = [];
			$("#select_emp").val("").trigger('change');
			alert('Data Sudah Ada di List. Gunakan NIK untuk Scan Atau Periksa Name Tag Anda Ke Bagian HR Jika Tidak Bisa');
			return false;
		}
		else{
			$('#nik5').html(data_name[0]);
			$('#name5').html(data_name[1].split(' ').slice(0,2).join(' '));
			$('#shift5').html(data_name[0]);
			$('#tabInput').show();
		}
	}
}


function cancelRequest1() {
	$('#tabInput').hide();
	$('#nik5').html("-");
	$('#name5').html("-");
	$("#select_emp").val("").trigger('change');
}

function createRequest1() {
	$('#loading').show();

	var emp = $('#select_emp').val().split('__');
	
	var data = {
		employees:emp[0],
		name:emp[1],
		shift:emp[2],
		section:emp[3]
	}

	$.post('{{ url("create/extra/food") }}', data, function(result, status, xhr){
		if(result.status){
			$('#loading').hide();
			$('#nik5').html("-");
			$('#tabInput').hide();
			$('#name5').html("-");
			$("#select_emp").val("").trigger('change');
			openSuccessGritter('Success','Data Sudah Masuk.');
			audio_ok.play();
			getTambahan();
		} else {
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!',result.message);
		}
	})
}

function getTambahan(st) {

	var role_code = '{{ $role_user->role_code }}'
	var group = '{{ $user->group }}'


	if (st =! "put") {

	}else{
		$('#loading').show();
		var timeoutID = setTimeout(function () {

			$('#loading').hide();
		}, 1000);
	}

	$.get('{{ url("fetch/list/extra/tambahan") }}', function(result, status, xhr){
		$('#loading').hide();
		$("#bodyTableAttendance5").empty();
		$('#tableAttendance5').DataTable().clear();
		$('#tableAttendance5').DataTable().destroy();
		var body = "";
		var no = 1;
		var nos = 0;


		$.each(result.datas_tam, function(index, value){
			if (value.remark == "Order Extra Food") {

				datas_detail_extra_tam.push({
					time_in: value.time_in,
					employee_id: value.employee_id,
					name: value.name,
					section: value.section,
					shift: value.shift+" | "+value.shiftdaily_code,
					attend_date: value.attend_date  
				});

				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.time_in+"</td>";
				body += "<td>"+value.employee_id+"</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.section+"</td>";
				if (value.shiftdaily_code != null) {
					body += "<td>"+value.shift+" | "+value.shiftdaily_code+"</td>";

				}else{

					body += "<td>"+value.shift+"</td>";

				}
				if (value.attend_date == null) {
					body += "<td><label class='label label-info'>Belum Hadir</label></td>";
					body += "<td>-</td>";
				}else{
					body += "<td><label class='label label-success'>Hadir</label></td>";
					body += "<td>"+value.attend_date+"</td>";

				}

				if (role_code == "S-MIS" || role_code == "MIS" || role_code == "S-GA" || group == 'Security Group') {

					body += "<td><button class='btn btn-danger btn-sm' onclick='deleteJob(\""+value.id+"\")' style='margin-right: 5px'><i class='fa fa-trash-o'></i>&nbsp;&nbsp;Delete</button></td>";
				}else{
					body += "<td>-</td>";
				}
				body += "</tr>";
				no++;
				nos++;
			}

		})

		$("#countExtraTamOut").html(nos);			
		$("#bodyTableAttendance5").append(body);
		$('#tableAttendance5 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
		} );

		var table = $('#tableAttendance5').DataTable({
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
			},initComplete: function() {
				this.api()
				.columns([1,5])
				.every(function(dd) {
					var column = this;
					var theadname = $("#tableAttendance5 th").eq([dd])
					.text();
					var select = $(
						'<select><option value="" style="font-size:11px;">All</option></select>'
						)
					.appendTo($(column.footer()).empty())
					.on('change', function() {
						var val = $.fn.dataTable.util
						.escapeRegex($(this)
							.val());

						column.search(val ? '^' + val + '$' :
							'', true,
							false)
						.draw();
					});
					column
					.data()
					.unique()
					.sort()
					.each(function(d, j) {
						var vals = d;
						if ($("#tableAttendance5 th").eq([dd])
							.text() ==
							'Category') {
							vals = d.split(' ')[0];
					}
					select.append(
						'<option style="font-size:12px;"  value="' +
						d + '">' + vals + '</option>');
				});
				});
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": false,
			"aaSorting": [[ 0, "desc" ]]
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

		$('#tableAttendance5 tfoot tr').appendTo('#tableAttendance5 thead');

	})
}

function deleteJob(id) {
	if (confirm('Apakah Anda yakin akan menghapus data?')) {

		var data = {
			id:id
		}
		$.post('{{ url("delete/extra/tam") }}', data, function(result, status, xhr){
			if(result.status){
				audio_ok.play();
				openSuccessGritter('Success','Success dihapus');
				getTambahan();

			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		})
	}
}



	function compareValues(key, order = 'asc') {
		return function innerSort(a, b) {
			if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
      // property doesn't exist on either object
      return 0;
  }

  const varA = (typeof a[key] === 'date')
  ? a[key].toUpperCase() : a[key];
  const varB = (typeof b[key] === 'date')
  ? b[key].toUpperCase() : b[key];

  let comparison = 0;
  if (varA > varB) {
  	comparison = 1;
  } else if (varA < varB) {
  	comparison = -1;
  }
  return (
  	(order === 'desc') ? (comparison * -1) : comparison
  	);
};
}



function getHistory3(st) {
	if (st == "no") {

	}else{

		$('#loading').show();
		var timeoutID = setTimeout(function () {

			$('#loading').hide();
		}, 1000);
	}


	$("#bodyTableAttendance3").empty();
	$('#tableAttendance3').DataTable().clear();
	$('#tableAttendance3').DataTable().destroy();
	var body3 = "";
	var no = 1;
	var nos2 = 0;
	var out2 = 0;

	$.each(datas_rovs.sort(compareValues('attend_date', 'desc')), function(index, value){
		body3 += "<tr>";
		body3 += "<td>"+no+"</td>";
		body3 += "<td>"+value.time_in+"</td>";
		body3 += "<td>"+value.employee_id+"</td>";
		body3 += "<td>"+value.name+"</td>";
		body3 += "<td>"+value.section+"</td>";
		body3 += "<td>"+value.shift+"</td>";
		if (value.attend_date == null) {
			body3 += "<td><label class='label label-info'>Belum Hadir</label></td>";
			body3 += "<td>-</td>";
		}else{
			body3 += "<td><label class='label label-success'>Hadir</label></td>";
			body3 += "<td>"+value.attend_date+"</td>";
			out2++;
		}
		body3 += "</tr>";
		nos2++;
		no++;
	})

	$("#countOvertimeOut").html(out2);
	$("#countOvertimeIn").html(nos2);			
	$("#countOvertimeSisa").html(nos2-out2);

	$("#bodyTableAttendance3").append(body3);
	$('#tableAttendance3 tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
	} );

	var table = $('#tableAttendance3').DataTable({
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
		},initComplete: function() {
			this.api()
			.columns([1,5])
			.every(function(dd) {
				var column = this;
				var theadname = $("#tableAttendance3 th").eq([dd])
				.text();
				var select = $(
					'<select><option value="" style="font-size:11px;">All</option></select>'
					)
				.appendTo($(column.footer()).empty())
				.on('change', function() {
					var val = $.fn.dataTable.util
					.escapeRegex($(this)
						.val());

					column.search(val ? '^' + val + '$' :
						'', true,
						false)
					.draw();
				});
				column
				.data()
				.unique()
				.sort()
				.each(function(d, j) {
					var vals = d;
					if ($("#tableAttendance3 th").eq([dd])
						.text() ==
						'Category') {
						vals = d.split(' ')[0];
				}
				select.append(
					'<option style="font-size:12px;"  value="' +
					d + '">' + vals + '</option>');
			});
			});
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': false,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": false,
		"aaSorting": [[ 0, "desc" ]]
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

	$('#tableAttendance3 tfoot tr').appendTo('#tableAttendance3 thead');
}


function getHistoryFilter3(st) {
	$('#loading').show();
	var timeoutID = setTimeout(function () {

		$('#loading').hide();
	}, 1000);



	$("#bodyTableAttendance3").empty();
	$('#tableAttendance3').DataTable().clear();
	$('#tableAttendance3').DataTable().destroy();
	var body3 = "";
	var no = 1;

	$.each(datas_rovs, function(index, value){
		if (st == "IN") {
			body3 += "<tr>";
			body3 += "<td>"+no+"</td>";
			body3 += "<td>"+value.time_in+"</td>";
			body3 += "<td>"+value.employee_id+"</td>";
			body3 += "<td>"+value.name+"</td>";
			body3 += "<td>"+value.section+"</td>";
			body3 += "<td>"+value.shift+"</td>";
			if (value.attend_date == null) {
				body3 += "<td><label class='label label-info'>Belum Hadir</label></td>";
				body3 += "<td>-</td>";
			}else{
				body3 += "<td><label class='label label-success'>Hadir</label></td>";
				body3 += "<td>"+value.attend_date+"</td>";

			}
			body3 += "</tr>";
			no++;

		}else if (st == "OUT") {
			if (value.attend_date != null) { 
				body3 += "<tr>";
				body3 += "<td>"+no+"</td>";
				body3 += "<td>"+value.time_in+"</td>";
				body3 += "<td>"+value.employee_id+"</td>";
				body3 += "<td>"+value.name+"</td>";
				body3 += "<td>"+value.section+"</td>";
				body3 += "<td>"+value.shift+"</td>";
				if (value.attend_date == null) {
					body3 += "<td><label class='label label-info'>Belum Hadir</label></td>";
					body3 += "<td>-</td>";
				}else{
					body3 += "<td><label class='label label-success'>Hadir</label></td>";
					body3 += "<td>"+value.attend_date+"</td>";

				}
				body3 += "</tr>";
				no++;
			}

		}else{
			if (value.attend_date == null) { 
				body3 += "<tr>";
				body3 += "<td>"+no+"</td>";
				body3 += "<td>"+value.time_in+"</td>";
				body3 += "<td>"+value.employee_id+"</td>";
				body3 += "<td>"+value.name+"</td>";
				body3 += "<td>"+value.section+"</td>";
				body3 += "<td>"+value.shift+"</td>";
				if (value.attend_date == null) {
					body3 += "<td><label class='label label-info'>Belum Hadir</label></td>";
					body3 += "<td>-</td>";
				}else{
					body3 += "<td><label class='label label-success'>Hadir</label></td>";
					body3 += "<td>"+value.attend_date+"</td>";

				}
				body3 += "</tr>";
				no++;
			}
		}

	})

	$("#bodyTableAttendance3").append(body3);
	$('#tableAttendance3 tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
	} );

	var table = $('#tableAttendance3').DataTable({
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
		},initComplete: function() {
			this.api()
			.columns([1,5])
			.every(function(dd) {
				var column = this;
				var theadname = $("#tableAttendance3 th").eq([dd])
				.text();
				var select = $(
					'<select><option value="" style="font-size:11px;">All</option></select>'
					)
				.appendTo($(column.footer()).empty())
				.on('change', function() {
					var val = $.fn.dataTable.util
					.escapeRegex($(this)
						.val());

					column.search(val ? '^' + val + '$' :
						'', true,
						false)
					.draw();
				});
				column
				.data()
				.unique()
				.sort()
				.each(function(d, j) {
					var vals = d;
					if ($("#tableAttendance3 th").eq([dd])
						.text() ==
						'Category') {
						vals = d.split(' ')[0];
				}
				select.append(
					'<option style="font-size:12px;"  value="' +
					d + '">' + vals + '</option>');
			});
			});
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': false,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": false,
		"aaSorting": [[ 0, "desc" ]]
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

	$('#tableAttendance3 tfoot tr').appendTo('#tableAttendance3 thead');
}



function openSuccessGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '4000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '4000'
	});
}
</script>
@endsection
