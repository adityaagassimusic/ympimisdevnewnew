@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table > tr:hover {
		background-color: #7dfa8c;
	}
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 2px;
		padding-right: 2px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td:hover{
		cursor: pointer;
		background-color: #7dfa8c;
	}
	.column-table {
		flex: 50%;
		padding: 5px;
	}
	.row-table {
		display: flex;
		margin-left:-5px;
		margin-right:-5px;
	}
	#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<a style="color: white;" class="btn btn-info pull-right" href="{{ url('fetch/general/safety_riding_pdf') }}/{{ $param }}" target="_blank"><i class="fa fa-file-pdf-o"></i> Generate PDF</a>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					{{-- <div class="input-group">
						<span class="input-group-addon" style="background-color: yellow; font-weight: bold;">Periode</span>
						<input type="text" class="form-control datepicker" id="selectMonth" placeholder="Select Month" onchange="fetchRecord();" value="{{$mon}}" style="width: 10%;">
					</div> --}}
					<input type="hidden" id="selectMonth" value="{{ $mon }}">
					<input type="hidden" id="department" value="{{ $department }}">
					<input type="hidden" id="location" value="{{ $location }}">
						{{-- <center>
							<span style="font-weight: bold; font-size: 2vw;" id="period"></span>
						</center> --}}
					</div>
					<div class="box-body">

						<div>
							<center>
								<span style="font-weight: bold; font-size: 20px;" id="period">
									
								</span>
							</center>
							<br>
							<table class="table" style="margin-bottom: 20px;">
								<tr>
									<th style="text-align: left; border: 0px;">
										<span>
											① Perkirakan waktu untuk tiba dengan selamat di tempat tujuan. (Mari berangkat kerja lebih awal.)
											<br>
											② Marilah patuhi aturan berlalu lintas demi orang-orang tercinta kita.
										</span>
									</th>
									<th style="text-align: right; border: 0px;">
										<span>
											No Dok. : YMPI/STD/FK3/054<br>
											Rev		: 00<br>
											Tanggal	: 01 April 2015
										</span>
									</th>
								</tr>
							</table>
						</div>
						<div class="row-table">
							<div class="column-table">
								<table class="table table-hover table-bordered" style="margin-bottom: 20px; width: 100%;">
									<thead>
										<tr>
											<th rowspan="3" style="width: 3%;" id="department_name"></th>
											<th colspan="2" style="width: 1%;">Sebelum Mulai</th>
										</tr>
										<tr>
											<th style="width: 1%;">② Manager</th>
											<th style="width: 1%;">① Chief</th>
										</tr>
										<tr>
											<th style="height: 40px; width: 1%;" id="manager_name_before"></th>
											<th style="height: 40px; width: 1%;" id="chief_name_before"></th>
										</tr>
									</thead>
								</table>
							</div>
							<div class="column-table">
								<table class="table table-hover table-bordered pull-right" style="margin-bottom: 20px; width: 50%;">
									<thead>
										<tr>
											<th colspan="2" style="width: 1%;">Sesudah Selesai</th>
										</tr>
										<tr>
											<th style="width: 1%;">④ Manager</th>
											<th style="width: 1%;">③ Chief</th>
										</tr>
										<tr>
											<th style="height: 40px; width: 1%;" id="manager_name_after"></th>
											<th style="height: 40px; width: 1%;" id="chief_name_after"></th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
						<br>
					</div>
					<table class="table table-hover table-bordered" id="tableRecord" style="margin-bottom: 20px;">
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOpt">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-6">
					<button style="border: 1px solid black; height: 170px; font-size: 3vw; font-weight: bold; width: 100%; background-color: #e53935;" onclick="checkIn(id)" id="batsu">BATSU<br>&#9747;</button>
				</div>
				<div class="col-xs-6">
					<button class="btn" style="border: 1px solid black; height: 170px; font-size: 3vw; font-weight: bold; width: 100%; background-color: #7dfa8c;" onclick="checkIn(id)" id="maru">MARU<br>&#9711;</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fetchRecord();
		$('#selectMonth').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var checkEmployeeId = "";
	var checkDueDate = "";
	var checkId = "";

	function checkIn(rem){
		var department = $('#department_name').text();
		var location = 'OFC';
		var data = {
			employee_id:checkEmployeeId,
			department:department,
			due_date:checkDueDate,
			remark:rem,
			location:location
		}
		$.post('{{ url("input/general/safety_riding_record") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				if(rem == 'batsu'){
					$('#'+checkId).text("☓");
				}
				else{
					$('#'+checkId).text("◯");
				}
				$('#modalOpt').modal('hide');
			}
			else{
				$('#modalOpt').modal('hide');
				openErrorGritter('Error!', result.message);
				$('#loading').hide();
				audio_error.play();
				return false;
			}
		});
	}

	function checkOpt(value, id){
		str = value.split('~');
		checkEmployeeId = str[0];
		checkDueDate = str[1];
		checkId = id;

		$('#modalOpt').modal('show');
	}

	function approve(remark){
		var month = $('#selectMonth').val();
		var department = $('#department_name').text();
		var location = 'OFC';
		var data = {
			month:month,
			department:department,
			remark:remark,
			location:location
		}
		if(confirm("Apakah anda yakin akan melakukan konfirmasi?")){
			$.post('{{ url("approve/general/safety_riding_record") }}', data, function(result, status, xhr){
				if(result.status){
					fetchRecord();
					openSuccessGritter('Success!', result.message);
					$('#loading').hide();
					audio_ok.play();
				}
				else{
					openErrorGritter('Error!', result.message);
					$('#loading').hide();
					audio_error.play();
					return false;
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function fetchRecord(){
		var month = $('#selectMonth').val();
		var location = $('#location').val();
		var department = $('#department').val();
		var data = {
			month:month,
			location:location,
			department:department
		}
		$.get('{{ url("fetch/general/safety_riding_record") }}', data, function(result, status, xhr){
			if(result.status){

				$('#period').text(result.year+'年 '+result.mon+'月 Catatan Record Penerapan 『Janji Safety Riding』');
				$('#department_name').html("");
				$('#department_name').append(result.safety_ridings[0].department);
				$('#manager_name_before').html("");
				$('#chief_name_before').html("");
				$('#manager_name_after').html("");
				$('#chief_name_after').html("");
				$('#manager_name_before').append('<button class="btn btn-success" id="manager-before" onclick="approve(id)">Confirm</button>');
				$('#chief_name_before').append('<button class="btn btn-success" id="chief-before" onclick="approve(id)">Confirm</button>');

				$('#manager_name_after').append('<button class="btn btn-success" id="manager-after" onclick="approve(id)">Confirm</button>');
				$('#chief_name_after').append('<button class="btn btn-success" id="chief-after" onclick="approve(id)">Confirm</button>');

				$.each(result.safety_riding_approvers, function(key, value){
					if(value.remark == 'manager-before'){
						var manager_name_before = "";
						$('#manager_name_before').html("");

						manager_name_before += value.employee_name+'<br>';
						manager_name_before += value.created_at;
						$('#manager_name_before').append(manager_name_before);
					}
					if(value.remark == 'chief-before'){
						var chief_name_before = "";
						$('#chief_name_before').html("");

						chief_name_before += value.employee_name+'<br>';
						chief_name_before += value.created_at;
						$('#chief_name_before').append(chief_name_before);
					}
					if(value.remark == 'manager-after'){
						var manager_name_after = "";
						$('#manager_name_after').html("");

						manager_name_after += value.employee_name+'<br>';
						manager_name_after += value.created_at;
						$('#manager_name_after').append(manager_name_after);
					}
					if(value.remark == 'chief-after'){
						var chief_name_after = "";
						$('#chief_name_after').html("");

						chief_name_after += value.employee_name+'<br>';
						chief_name_after += value.created_at;
						$('#chief_name_after').append(chief_name_after);
					}
				});
				$('#tableRecord').html("");
				var tableRecord = "";
				var ins = false;
				var cnt = 0;

				tableRecord += '<thead style="background-color: #63ccff;">';
				tableRecord += '<tr>';
				tableRecord += '<th style="width: 0.1%; font-size: 1vw;">#</th>';
				tableRecord += '<th style="width: 7%; font-size: 1vw;">Nama</th>';
				tableRecord += '<th style="width: 12%; font-size: 1vw;">Janji</th>';
				$.each(result.weekly_calendars, function(key, value){
					if(value.remark == 'H'){
						tableRecord += '<th style="vertical-align: top; width: 0.1%; background-color: grey; font-size: 1vw;">'+value.header+'</th>';
					}
					else{
						tableRecord += '<th style="vertical-align: top; width: 0.1%; font-size: 1vw;">'+value.header+'</th>';
					}
				});
				tableRecord += '</tr>';
				tableRecord += '</thead>';
				tableRecord += '<tbody>';
				$.each(result.safety_ridings, function(key, value){
					tableRecord += '<tr style="height: 45px;">';
					tableRecord += '<td style="width: 0.1%;">'+(key+1)+'</td>';
					tableRecord += '<td style="width: 7%; text-align: left;">'+value.employee_name+'</td>';
					tableRecord += '<td style="width: 12%; text-align: left;">'+value.safety_riding+'</td>';
					for(var i = 0; i < result.weekly_calendars.length; i++){
						ins = false;
						for(var j = 0; j < result.safety_riding_records.length; j++){
							if(result.weekly_calendars[i].week_date == result.safety_riding_records[j].due_date && value.employee_id == result.safety_riding_records[j].employee_id){
								if(result.safety_riding_records[j].remark == 'maru'){
									cnt += 1;
									tableRecord += '<td id="check_'+cnt+'" style="width: 0.1%; text-align: center;" onclick="checkOpt(\''+value.employee_id+'~'+result.weekly_calendars[i].week_date+'\',id);">&#9711;</td>';
								}
								else{
									cnt += 1;
									tableRecord += '<td id="check_'+cnt+'" style="width: 0.1%; text-align: center;" onclick="checkOpt(\''+value.employee_id+'~'+result.weekly_calendars[i].week_date+'\',id);">&#9747;</td>';
								}
								ins = true;
							}
						}
						if(ins == false){
							cnt += 1;
							tableRecord += '<td id="check_'+cnt+'" style="width: 0.1%; text-align: center;" onclick="checkOpt(\''+value.employee_id+'~'+result.weekly_calendars[i].week_date+'\',id);"></td>';
						}
					}
				});
				tableRecord += '</tr>';
				tableRecord += '</tbody>';

				$('#tableRecord').append(tableRecord);
			}
			else{
				openErrorGritter('Error!', result.message);
				$('#loading').hide();
				audio_error.play();
				return false;
			}
		});
}

function truncate(str, n){
	return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
};

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
</script>

@endsection