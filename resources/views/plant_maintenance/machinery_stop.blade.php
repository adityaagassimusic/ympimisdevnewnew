@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.text-yellow{
		font-size: 40px !important;
		font-weight: bold;
	}
	.table {
		width: 100%;
		max-width: 100%;
		/*		margin-bottom: 20px;*/
	}
	table {
		background-color: transparent;
	}

	.label-tbl {padding-left: 20px !important;}
	.text-red {color: #ff7564 !important;}
	.desc-number {color: white; text-shadow: -1px -1px 0 #0F0}

	.summary-tbl{
		border-top: 0;
		border-spacing: 5px;
		border-collapse: separate;
	}
	.summary-tbl > tbody > tr > td{
		font-size: 36px;
		background: transparent;
		color: #FFF;
		vertical-align: middle;
	}
	td:hover{
		cursor: pointer;
	}
	.summary-tbl > thead > tr > th{
		border:1px solid #8b8c8d;
		background-color: #518469;
		color: white;
		font-size: 12px;
		border-bottom: 7px solid #797979;
		vertical-align: middle;
	}
	.tbl-header{
		border:1px solid #8b8c8d !important;
		background-color: #518469 !important;
		color: white !important;
		font-size: 16px !important;
		border-bottom: 7px solid #797979 !important;
		vertical-align: middle !important;
	}
	.summary-tbl > tfoot > tr > td{
		border:1px solid #777474;
		font-size: 20px;
		background: transparent;
		color: yellow;
		vertical-align: middle;
		padding: 20px 10px;
		letter-spacing: 1.1px;
	}

	.alarm {
		-webkit-animation: alarm_ani 2s infinite;  /* Safari 4+ */
		-moz-animation: alarm_ani 2s infinite;  /* Fx 5+ */
		-o-animation: alarm_ani 2s infinite;  /* Opera 12+ */
		animation: alarm_ani 2s infinite;  /* IE 10+, Fx 29+ */
		font-weight: bold;
	}

	@-webkit-keyframes alarm_ani {
		0%, 30% {
			color: rgba(55, 255, 0, 255);
		}
		31%, 60% {
			color: rgba(55, 255, 0, 100);
		}
		61%, 100% {
			color: rgba(55, 255, 0, 0);
		}
	}

</style>
@stop
@section('header')
@endsection
<style type="text/css">
</style>
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<span style="color: white; font-size: 2vw">Machine Status :</span>
			<div class="small-box" style="background: #00af50; height: 15vh; margin-bottom: 5px;cursor: pointer;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw; color: white"><b>InProcess</b></h3>
					<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px; color: #fff" id="total_process">0 Machine</h5>
				</div>
			</div>

			<div class="small-box" style="background: #3366cc; height: 15vh; margin-bottom: 5px;cursor: pointer;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw; color: #fff"><b>Setup</b></h3>
					<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px; color: #fff" id="total_setup">0 Machine</h5>
				</div>
			</div>

			<div class="small-box" style="background: #fca83a; height: 15vh; margin-bottom: 5px;cursor: pointer;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw; color: black"><b>Idle</b></h3>
					<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px; color: black" id="total_idle1">0 Machine</h5>
				</div>
			</div>

		<!-- 	<div class="small-box" style="background: #ffffff; height: 15vh; margin-bottom: 5px;cursor: pointer;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw; color: #000"><b>Idle 2</b></h3>
					<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_idle2">0 Machine</h5>
				</div>
			</div> -->

			<div class="small-box" style="background: #000; height: 15vh; margin-bottom: 5px;cursor: pointer;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw; color: #fff"><b>Off</b></h3>
					<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px; color: #fff" id="total_off">0 Machine</h5>
				</div>
			</div>
			<div class="small-box" style="background: #f24b4b; height: 15vh; margin-bottom: 5px;cursor: pointer;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw; color: #fff"><b>Trouble</b></h3>
					<h5 style="font-size: 2vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px; color: #fff" id="total_trouble">0 Machine</h5>
				</div>
			</div>
		</div>
		<div class="col-xs-10">
			<div id="master_col" class="row">
				<table class="table summary-tbl">
					<tbody id="tbody-id">
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-Edit">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 id="modal-title"></h4> 
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="form-group">
							<label class="col-sm-3 control-label" style="margin-top: 3px">Kode Mesin</label>
							<div class="col-sm-9" style="margin-top: 3px">
								<input type="hidden" id="id">
								<input type="text" class="form-control" id="machine_code" readonly placeholder="Nomor Mesin">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" style="margin-top: 3px">Nama Mesin</label>
							<div class="col-sm-9" style="margin-top: 3px">
								<input type="text" class="form-control" id="machine_name" readonly>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" style="margin-top: 3px">Lokasi Mesin</label>
							<div class="col-sm-9" style="margin-top: 3px">
								<input type="text" class="form-control" id="location" readonly>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" style="margin-top: 3px">Nomor SPK</label>
							<div class="col-sm-9" style="margin-top: 3px">
								<input type="text" class="form-control" id="spk" readonly placeholder="Nomor SPK">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" style="margin-top: 3px">Trouble Status</label>
							<div class="col-sm-9" style="margin-top: 3px">
								<input type="text" class="form-control" id="trouble_stat" placeholder="Status Trouble">
							</div>
						</div>
					</div>
					
					
				</div>
				<div class="modal-footer">
					<button type="button" style="margin-top: 5px" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
					<button type="button" style="margin-top: 5px" class="btn btn-success pull-right" onclick="save_trouble()"><i class="fa fa-check"></i> Simpan</button>
				</div>
			</div>
		</div>
	</div>
</section>
@stop
@section('scripts')
<script src="{{ url("js/jquery.marquee.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>

	var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

	var error_machine = [];
	var trouble_machine_list = [];
	var proses = 0;
	var trouble = 0;
	var idle1 = 0;
	var idle2 = 0;
	var setup = 0;
	var off = 0;

	jQuery(document).ready(function(){
		$('.select2').select2();

		var i = 0;

		setInterval(function() {
			i++;
			if(i%2 == 0){
				$(".machine-stop").css("background-color", "red");
				$(".machine-stop").css("color", "white");
			} else {
				$(".machine-stop").css("background-color", "white");
				$(".machine-stop").css("color", "red");
			}
		}, 1000);

		showData();
		setInterval(showData, 1000 * 60);

	});	

	function display(minutes)
	{
	// var days = Math.floor(minutes / 60 / 24);    
	var hours = Math.floor(minutes / 60);          
	var minutes = minutes % 60;

	// var hours = (minutes / 60 | 0);
	// var minutes = (minutes | 0);
	// var days = minutes / 60 / 24 | 0;
	return ' '+hours+ ' H '+minutes+' M';

}

function showData() {
	error_machine = [];
	proses = 0;
	setup = 0;
	idle1 = 0;
	idle2 = 0;
	off = 0;
	trouble = 0;

	var cols = 0;

	$("#total_process").text(proses+' Machine(s)');
	$("#total_setup").text(setup+' Machine(s)');
	$("#total_idle1").text((idle1 + idle2)+' Machine(s)');
	$("#total_off").text(off+' Machine(s)');
	$("#total_trouble").text(trouble+' Machine(s)');

	var body = '';

	$('#master_col').empty();

	var data = {

	}

	$.get('{{ url("fetch/machinery_stop") }}', data, function(result, status, xhr){
		if (result.machine_trouble.length > 0) {
			var master_col = '';
			var machine_red = [];				


			var num = 0;

			$.each(result.machine_trouble, function(key, value) {
				var today = new Date();
				var date_trouble = new Date(value.started_at);

				var t = today - date_trouble;
				var z = parseInt(t / 1000 / 60);

				var days = z / 60 / 24 | 0;
				var hours = Math.floor(z / 60);          
				var minutes = z % 60;

				if (hours >= 24) {
					machine_red.push({'id' : value.id, 'machine_code' : value.machine_code, 'machine_name' : value.machine_name, 'location' : value.machine_location, 'status' : value.status_machine, 'error_status' : value.trouble_status, 'form_number' : value.form_number, 'stat' : value.remark, 'time' : '<b><span> '+days+'<span style="font-size: 3vw;"> Days</span><span> '+hours % 24+'<span style="font-size: 3vw;"> Hour</span></b>'});
				}else{
					machine_red.push({'id' : value.id, 'machine_code' : value.machine_code, 'machine_name' : value.machine_name, 'location' : value.machine_location, 'status' : value.status_machine, 'error_status' : value.trouble_status, 'form_number' : value.form_number, 'stat' : value.remark, 'time' : '<b><span> '+hours+'<span style="font-size: 3vw;"> Hour</span><span> '+minutes+'<span style="font-size: 3vw;"> Min</span></b>'});
				}								

			});

			body += '<div class="col-xs-'+cols+'" style="padding-left: 0px">';

			$.each(machine_red, function(key, value) {
				body += '<hr style="margin-top: 5px; margin-bottom: 5px">';
				body += '<table>';
				body += '<tr onclick="openEdit(\''+value.id+'\', \''+value.machine_code+'\', \''+value.machine_name+'\', \''+value.location+'\', \''+value.error_status+'\', \''+value.status+'\', \''+value.stat+'\', \''+value.form_number+'\')">';
				body += '<td width="1%" style="text-align:right;border-top:0; color: white; padding-right: 5px; font-size: 2vw">';
				body += '<i class="fa fa-map-marker"></i>';
				body += '</td>';
				body += '<td width="55%" style="color: white; font-size: 3vw">&nbsp;';			
				body += value.location;
				body += '</td>';
				body += '<td rowspan="3" class="machine-stop text-center" style="background-color: red; border-radius: 10px; letter-spacing: 2px; font-size: 5.5vw; color: white;">';

				body += '<i class="glyphicon glyphicon-time" style="font-size: 0.75em;"></i> ';
				body += value.time;
				body += '</td>';
				body += '</tr>';
				body += '<tr onclick="openEdit(\''+value.id+'\', \''+value.machine_code+'\', \''+value.machine_name+'\', \''+value.location+'\', \''+value.error_status+'\', \''+value.status+'\', \''+value.stat+'\', \''+value.form_number+'\')">';		
				body += '<td style="text-align:right; color: white; padding-right: 5px; font-size: 2vw;">';
				body += '<i class="fa fa-gears"></i> ';
				body += '</td>';
				body += '<td style="color: white; font-size: 3vw">&nbsp;';

				if (!value.error_status) {
					body += value.machine_name;
				} else {
					body += value.machine_name+'&nbsp; ( '+value.error_status+' )';
				}

				body += '</td>';
				body += '</tr>';
				body += '<tr onclick="openEdit(\''+value.id+'\', \''+value.machine_code+'\', \''+value.machine_name+'\', \''+value.location+'\', \''+value.error_status+'\', \''+value.status+'\', \''+value.stat+'\', \''+value.form_number+'\')">';	
				body += '<td style="text-align:right;border-top:0; color: white; padding-right: 5px; font-size: 2vw">';
				body += '<small><i class="fa fa-info-circle" style=""></i>';
				body += '</td>';
				body += '<td style="color: black; font-size: 2vw; padding-right: 1vw;">';
				body += '<div style="background-color:#faacf3;border-radius:2px; width: 100%">'+(value.status || '')+'</div>';
				body += '</small></td>';
				body += '</tr>';
				body += '</table>';

				if (key % 3 === 0 || (machine_red.length-1) != key) {
					body += '</div>';
					body += '<div class="col-xs-'+cols+'" style="padding-left: 0px">';
				} else if ((machine_red.length-1) == key) {
					body += '</div>';
				}
			})

			$('#master_col').append(body);
		} else {
			body += '<div class="col-xs-12" style="padding-left: 0px">';
			body += '<hr style="margin-top: 5px; margin-bottom: 5px">';
			body += '<table style="width: 100%">';
			body += '<tr>';
			body += '<td width="100%" style="border-top:0; color: #37ff00; padding-right: 5px; font-size: 5vw; padding-top: 10vh">';
			body += '<center><i class="fa fa-check-circle" style="font-size: 10vw"></i><br>ALL MACHINES ARE RUNNING<br> 機械故障停止はありません</center>';
			body += '</td>';
			body += '</tr>';
			body += '</table>';

			body += '</div>';

			$('#master_col').append(body);
		}

		var m_stop = 0;
		var m_progress = 0;
		var m_setup = 0;
		var m_idle = 0;
		var m_off = 0;

		$.each(result.oee, function(key, value) {
			if (value.status_mesin == 'error') {
				m_stop += 1;
			} else if (value.status_mesin == 'off') {
				m_off += 1;
			} else if (value.status_mesin == 'inprogress') {
				m_progress += 1;
			} else if (value.status_mesin == 'setup') {
				m_setup += 1;
			} else if (value.status_mesin == 'iddle' || value.status_mesin == 'iddle2') {
				m_idle += 1;
			}
		})

		$("#total_process").text(m_progress+" Machine(s)");
		$("#total_setup").text(m_setup+" Machine(s)");
		$("#total_idle1").text(m_idle+" Machine(s)");
		$("#total_off").text(m_off+" Machine(s)");
		$("#total_trouble").text(m_stop+" Machine(s)");
	})
}

function openEdit(id, code, mesin_name, loc, error_status, status, stat, form_number) {
	$("#modal-Edit").modal('show');

	if (code != 'null') {
		$("#machine_code").val(code);
	}

	if (form_number != 'null') {
		$("#spk").val(form_number);
	}

	$("#machine_name").val(mesin_name);
	$("#id").val(id);
	$("#location").val(loc);
	$("#trouble_stat").val(status);
}

function save_trouble() {
	$("#loading").show();
	var data = {
		id : $("#id").val(),
		trouble_status : $("#trouble_stat").val(),
	}

	$.post('{{ url("edit/machinery_stop") }}', data, function(result, status, xhr){
		if (result.status) {
			$("#loading").hide();
			openSuccessGritter('Success', 'Update Data Sukses');

			$("#machine_name").val('');
			$("#id").val('');
			$("#location").val('');
			$("#trouble_stat").val('');
			showData();
			
			$("#modal-Edit").modal('hide');
		} else {
			$("#loading").hide();
			openErrorGritter('Error', result.message);
		}
	})
}

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

function secondsToDhms(seconds) {
	seconds = Number(seconds);
	var d = Math.floor(seconds / (3600*24));
	var h = Math.floor(seconds % (3600*24) / 3600);
	var m = Math.floor(seconds % 3600 / 60);
	var s = Math.floor(seconds % 60);

	var dDisplay = d > 0 ? d + (d == 1 ? " day, " : " days, ") : "";
	var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
	var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
	var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
	return dDisplay + hDisplay + mDisplay + sDisplay;
}

</script>
@endsection