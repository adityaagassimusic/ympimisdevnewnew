@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
	#loading, #error { display: none; }
	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ffbfbf;
			color: black;

		}
		50%, 100% {
			background-color: #ff5757;
			color: black;
		}
	}

	#tableResumeStock > thead > tr > th{
		height: 50px;
		border: 1px solid black;
		text-align: center;
		font-size: 23px;
		font-weight: bold;
	}
	#tableResumeStock > tbody > tr > td{
		height: 120px;
		border: 1px solid black;
		text-align: center;
		font-size: 80px;
		font-weight: bold;
	}

	#tableResumeIdeal > thead > tr > th{
		height: 50px;
		border: 1px solid black;
		text-align: center;
		font-size: 23px;
		font-weight: bold;
	}
	#tableResumeIdeal > tbody > tr > td{
		height: 120px;
		border: 1px solid black;
		text-align: center;
		font-size: 80px;
		font-weight: bold;
	}

	#tableTiming >  tr > th{
		height: 30px;
		border: 1px solid black;
		text-align: center;
		font-size: 20px;
		font-weight: bold;
	}
	#tableTiming >  tr > td{
		height: 30px;
		border: 1px solid black;
		text-align: center;
		font-size: 25px;
		font-weight: bold;
	}

	.headTiming{
		height: 30px;
		border: 1px solid black;
		text-align: center;
		font-size: 15px;
		font-weight: bold;
	}
	.bodyTiming{
		height: 30px;
		border: 1px solid black;
		text-align: center;
		font-size: 20px;
		font-weight: bold;
	}

	#tableTact > thead > tr > th{
		height: 50px;
		border: 1px solid black;
		text-align: center;
		font-size: 30px;
		font-weight: bold;
	}
	#tableTact > tbody > tr > td{
		height: 120px;
		border: 1px solid black;
		text-align: center;
		font-size: 67px;
		font-weight: bold;
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;padding-right: 0px;background-image: url({{url('data_file/pianica/pianica.png')}});background-size: cover;height: 200px;width: 100%">
				<!-- <div id="container" style="height: 65vh;"></div> -->
				<div style="background-color: rgba(130, 222, 255, 0.9);color: black;width: 200px;position: absolute;top: 5px;left: 50px;padding:4px;">
					<center style="padding:0;font-size: 18px;">Material Awal</center>
					<p style="padding-left: 20px;padding-right: 20px;">
					<span class="pull-left">Ideal :</span> <span class="pull-right" id="ideal_material_awal">0</span><br><br>
					<span class="pull-left">P-25 :</span> <span class="pull-right" id="p25_material_awal">0</span><br>
					<span class="pull-left">PS-25F :</span> <span class="pull-right" id="ps25f_material_awal">0</span><br>
					<span class="pull-left">P-32 :</span> <span class="pull-right" id="p32_material_awal">0</span><br>
					<span class="pull-left">P-37 :</span> <span class="pull-right" id="p37_material_awal">0</span><br>
					<span class="pull-left">Total :</span> <span class="pull-right" id="total_material_awal">0</span></p>
				</div>
				<div style="background-color: rgba(255, 196, 3, 0.9);color: black;width: 200px;position: absolute;top: 5px;left: 300px;padding:4px;">
					<center style="padding:0;font-size: 18px;">Tuning</center>
					<p style="padding-left: 20px;padding-right: 20px;">Ideal : <span class="pull-right" id="ideal_tuning">0</span><br><br>
					P-25 : <span class="pull-right" id="p25_tuning">0</span><br>
					PS-25F : <span class="pull-right" id="ps25f_tuning">0</span><br>
					P-32 : <span class="pull-right" id="p32_tuning">0</span><br>
					P-37 : <span class="pull-right" id="p37_tuning">0</span><br>
					Total : <span class="pull-right" id="total_tuning">0</span></p>
				</div>
				<div style="background-color: rgba(255, 156, 255, 0.9);color: black;width: 200px;position: absolute;top: 5px;left: 620px;padding:4px;">
					<center style="padding:0;font-size: 18px;">Kensa Awal</center>
					<p style="padding-left: 20px;padding-right: 20px;">Ideal : <span class="pull-right" id="ideal_kensa_awal">0</span><br><br>
					P-25 : <span class="pull-right" id="p25_kensa_awal">0</span><br>
					PS-25F : <span class="pull-right" id="ps25f_kensa_awal">0</span><br>
					P-32 : <span class="pull-right" id="p32_kensa_awal">0</span><br>
					P-37 : <span class="pull-right" id="p37_kensa_awal">0</span><br>
					Total : <span class="pull-right" id="total_kensa_awal">0</span></p>
				</div>
				<div style="background-color: rgba(189, 138, 255, 0.9);color: black;width: 200px;position: absolute;top: 5px;left: 840px;padding:4px;">
					<center style="padding:0;font-size: 18px;">Assembly</center>
					<p style="padding-left: 20px;padding-right: 20px;">Ideal : <span class="pull-right" id="ideal_assembly">0</span><br><br>
					P-25 : <span class="pull-right" id="p25_assembly">0</span><br>
					PS-25F : <span class="pull-right" id="ps25f_assembly">0</span><br>
					P-32 : <span class="pull-right" id="p32_assembly">0</span><br>
					P-37 : <span class="pull-right" id="p37_assembly">0</span><br>
					Total : <span class="pull-right" id="total_assembly">0</span></p>
				</div>
				<div style="background-color: rgba(255, 138, 138, 0.9);color: black;width: 200px;position: absolute;top: 5px;left: 1050px;padding:4px;">
					<center style="padding:0;font-size: 18px;">Kensa Akhir</center>
					<p style="padding-left: 20px;padding-right: 20px;">Ideal : <span class="pull-right" id="ideal_kensa_akhir">0</span><br><br>
					P-25 : <span class="pull-right" id="p25_kensa_akhir">0</span><br>
					PS-25F : <span class="pull-right" id="ps25f_kensa_akhir">0</span><br>
					P-32 : <span class="pull-right" id="p32_kensa_akhir">0</span><br>
					P-37 : <span class="pull-right" id="p37_kensa_akhir">0</span><br>
					Total : <span class="pull-right" id="total_kensa_akhir">0</span></p>
				</div>

				<div style="background-color: rgba(201, 201, 201, 0.9);color: black;width: 200px;position: absolute;top: 5px;left: 1280px;padding:4px;">
					<center style="padding:0;font-size: 18px;">Total Stock</center>
					<p style="padding-left: 20px;padding-right: 20px;">Ideal : <span class="pull-right" id="ideal_total">0</span><br><br>
					P-25 : <span class="pull-right" id="p25_total">0</span><br>
					PS-25F : <span class="pull-right" id="ps25f_total">0</span><br>
					P-32 : <span class="pull-right" id="p32_total">0</span><br>
					P-37 : <span class="pull-right" id="p37_total">0</span><br>
					Total : <span class="pull-right" id="total_total">0</span></p>
				</div>
				<!-- <div id="image" style="min-height: 17vh;width: 1500px;">
					<image src="{{url('data_file/pianica/pianica.png')}}" style="width:100%">
				</div> -->
			</div>
			<!-- <div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 5px;">
				<div class="box box-solid" style="margin-bottom: 0px;">
					<div class="box-header" style="padding: 0;background-color: lavender;">
						<center><h4 style="font-weight: bold;font-size: 30px;">Stock Ideal (Pcs)</h4></center>
					</div>
					<div class="box-body">
						<table style="width: 100%" id="tableResumeIdeal">
							<thead>
								<tr style="color: black;background-color: #cddc39;">
									<th style="width:19%">Material Awal</th>
									<th style="width:19%">Tuning</th>
									<th style="width:19%">Kensa Awal</th>
									<th style="width:19%">Assembly</th>
									<th style="width:19%">Kensa Akhir</th>
								</tr>
							</thead>
							<tbody id="bodyResumeIdeal">
								<tr style="color: black;background-color: white;">
									<td id="ideal_material_awal2">0</td>
									<td id="ideal_tuning2">0</td>
									<td id="ideal_kensa_awal2">0</td>
									<td id="ideal_assembly2">0</td>
									<td id="ideal_kensa_akhir2">0</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> -->
			<div class="col-xs-12" style="padding: 0px; margin-top: 10px;padding-right: 5px;">
				<div class="box box-solid" style="margin-bottom: 0px;">
					<div class="box-header" style="padding: 0;background-color: lightsalmon;border: 1px solid black;">
						<center><span style="font-weight: bold;font-size: 20px;">Lead Time (Minutes)</span></center>
					</div>
					<div class="box-body" style="padding: 0px">
						<table style="width: 100%" id="tableTiming">
							<tr style="color: black;background-color: #cddc39;">
								<th class="headTiming" style="width:20%">Condition</th>
								<th class="headTiming" style="width:20%">P-25</th>
								<th class="headTiming" style="width:20%">PS-25F</th>
								<th class="headTiming" style="width:20%">P-32</th>
								<th class="headTiming" style="width:20%">P-37</th>
							</tr>
							<tr style="color: black;background-color: white;">
								<th class="headTiming">IDEAL</th>
								<td class="bodyTiming" id="p25_timing_ideal">0</td>
								<td class="bodyTiming" id="ps25f_timing_ideal">0</td>
								<td class="bodyTiming" id="p32_timing_ideal">0</td>
								<td class="bodyTiming" id="p37_timing_ideal">0</td>
							</tr>
							<tr style="color: black;background-color: white;">
								<th class="headTiming">ACTUAL</th>
								<td class="bodyTiming" id="p25_timing">0</td>
								<td class="bodyTiming" id="ps25f_timing">0</td>
								<td class="bodyTiming" id="p32_timing">0</td>
								<td class="bodyTiming" id="p37_timing">0</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 0px;border: 1px solid white;" id="div_con_1">
				<div id="container_1" style="width: 100%">
					
				</div>
			</div>
			<div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 0px;border: 1px solid white;" id="div_con_2">
				<div id="container_2" style="width: 100%">
					
				</div>
			</div>
			<div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 0px;border: 1px solid white;" id="div_con_3">
				<div id="container_3" style="width: 100%">
					
				</div>
			</div>
			<div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 0px;border: 1px solid white;" id="div_con_4">
				<div id="container_4" style="width: 100%">
					
				</div>
			</div>
			<div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 0px;border: 1px solid white;" id="div_con_5">
				<div id="container_5" style="width: 100%">
					
				</div>
			</div>
			<!-- <div class="col-xs-6" style="padding: 0px; margin-top: 10px;padding-right: 5px;">
				<div class="box box-solid" style="margin-bottom: 0px;">
					<div class="box-header" style="padding: 0;background-color: lightskyblue;">
						<center><h4 style="font-weight: bold;font-size: 30px;">Stock Monitoring (Pcs)</h4></center>
					</div>
					<div class="box-body">
						<table style="width: 100%" id="tableResumeStock">
							<thead>
								<tr style="color: black;background-color: #cddc39;">
									<th style="width:19%">Material Awal</th>
									<th style="width:19%">Tuning</th>
									<th style="width:19%">Kensa Awal</th>
									<th style="width:19%">Assembly</th>
									<th style="width:19%">Kensa Akhir</th>
								</tr>
							</thead>
							<tbody id="bodyResumeStock">
								<tr style="color: black;background-color: white;">
									<td id="all_material_awal">0</td>
									<td id="all_tuning">0</td>
									<td id="all_kensa_awal">0</td>
									<td id="all_assembly">0</td>
									<td id="all_kensa_akhir">0</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> -->
			<!-- <div class="col-xs-6" style="padding: 0px; padding-right: 5px;margin-top: 10px;">
				<div class="box box-solid">
					<div class="box-header" style="padding: 0;background-color: lightgreen;">
						<center><h4 style="font-weight: bold;font-size: 30px;">Tact Time ( Minutes / Pcs )</h4></center>
					</div>
					<div class="box-body">
						<table style="width: 100%" id="tableTact">
							<thead>
								<tr style="color: black;background-color: #cddc39;">
									<th style="width:50%">Actual Time</th>
								</tr>
							</thead>
							<tbody id="bodyTact">
								<tr style="color: black;background-color: white;">
									<td id="actual_tact_time">0</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> -->
			<!-- <div class="col-xs-2" style="padding: 0px; margin-top: 0;" style="height: 82vh">
				<div class="small-box" style="background-color: #2064bd;color: white; height: 30vh; margin-bottom: 5px;" id="div_pureto">
					<table style="width: 100%">
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;text-align: center">Pianica Tuning</th>
						</tr>
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;border-bottom:1px solid white;border-top: 1px solid white;text-align: center;">Ideal Stock</th>
						</tr>
						<tr>
							<th style="font-size: 2.9vw;padding-left: 10px;height: 6.6vh;text-align: center;">360</th>
						</tr>
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;border-bottom:1px solid white;border-top: 1px solid white;text-align: center;">Actual Stock</th>
						</tr>
						<tr>
							<th style="font-size: 2.9vw;padding-left: 10px;height: 6.6vh;text-align: center;vertical-align: middle;" id="pn_pureto">0</th>
						</tr>
					</table>
				</div>
				<div class="small-box" style="background-color: #00a65a;color: white; height: 30vh; margin-bottom: 5px;" id="div_kensa_awal">
					<table style="width: 100%">
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;text-align: center">Pianica Kensa Suara Awal</th>
						</tr>
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;border-bottom:1px solid white;border-top: 1px solid white;text-align: center;">Ideal Stock</th>
						</tr>
						<tr>
							<th style="font-size: 2.9vw;padding-left: 10px;height: 6.6vh;text-align: center;">270</th>
						</tr>
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;border-bottom:1px solid white;border-top: 1px solid white;text-align: center;">Actual Stock</th>
						</tr>
						<tr>
							<th style="font-size: 2.9vw;padding-left: 10px;height: 6.6vh;text-align: center;" id="pn_kensa_awal">0</th>
						</tr>
					</table>
				</div>
				<div class="small-box" style="background-color: #7b2bd6;color: white; height: 30vh; margin-bottom: 5px;" id="div_kensa_akhir">
					<table style="width: 100%">
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;text-align: center">Pianica Kensa Suara Akhir</th>
						</tr>
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;border-bottom:1px solid white;border-top: 1px solid white;text-align: center;">Ideal Stock</th>
						</tr>
						<tr>
							<th style="font-size: 2.9vw;padding-left: 10px;height: 6.6vh;text-align: center;">270</th>
						</tr>
						<tr>
							<th style="font-size: 1.2vw;padding-left: 10px;border-bottom:1px solid white;border-top: 1px solid white;text-align: center;">Actual Stock</th>
						</tr>
						<tr>
							<th style="font-size: 2.9vw;padding-left: 10px;height: 6.6vh;text-align: center;" id="pn_kensa_akhir">0</th>
						</tr>
					</table>
				</div>
			</div>
		</div> -->
	</div>

	<div class="modal fade" id="modalTarget" style="color: black;">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header" style="background-color: lightblue">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;">Add Target</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-2" align="right" style="margin-top: 10px;">
             	<label>Line</label>
              </div>
              <div class="col-md-10" style="margin-top: 10px;">
             	<input type="text" class="form-control" style="width: 100%" readonly="" name="line" id="line" placeholder="Input Line">
              </div>
              <div class="col-md-2" align="right" style="margin-top: 10px;">
             	<label>Tact Time (Minutes)</label>
              </div>
              <div class="col-md-10" style="margin-top: 10px;">
             	<input type="text" class="form-control numpad" style="width: 100%" readonly="" name="target" id="target" placeholder="Input Tact Time (Minutes)">
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" style="width: 49%" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
          <button type="button" class="btn btn-success" style="width: 49%" onclick="addTarget()"><i class="fa fa-plus"></i> Add</button>
        </div>
      </div>
    </div>
  </div>

</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url('js/highcharts.js')}}"></script>
<script src="{{ url('js/highcharts-more.js')}}"></script>
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
		fillTable();
		$('.select2').select2({
			allowClear:true
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		setInterval(fillTable, 600000);
	});

	

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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function inputTarget(line) {
		var emp = [
			'PI1910002',
			'PI9805003',
			'PI9812011',
			'PI1110001',
			'PI9906002',
			'PI0104003',
			'PI1710002',
		];

		if (emp.includes('{{$emp}}')) {
			$('#modalTarget').modal('show');
			$('#line').val(line);
			$('#target').val('');
		}
	}

	function addTarget() {
		$('#loading').show();
		var data = {
			line:$('#line').val(),
			target:$('#target').val(),
		}

		$.post('{{ url("input/pn/target") }}',data, function(result, status, xhr) {
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success','Success Add Target');
				$('#modalTarget').modal('hide');
				fillTable();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}

	function fillTable() {
		$('#loading').show();

		$.get('{{ url("fetch/pn/stock_monitoring") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var categories = [];
					var category = [];
					var p_25 = [];
					var p_25f = [];
					var p_32 = [];
					var p_37 = [];
					var target = [];
					var pn_pureto = 0;
					var pn_kensa_awal = 0;
					var pn_assembly = 0;
					var pn_kensa_akhir = 0;

					// for(var j = 0; j < cat.length;j++){
					// 	if (cat[j] == 'PN_Pureto') {
					// 		category.push('Pianica Tuning');
					// 		target.push(parseInt('360'));
					// 	}
					// 	if (cat[j] == 'PN_Kensa_Awal') {
					// 		target.push(parseInt('270'));
					// 		category.push('Pianica Kensa Suara Awal');
					// 	}
					// 	if (cat[j] == 'PN_Kensa_Awal') {
					// 		target.push(parseInt('270'));
					// 		category.push('Pianica Kensa Suara Akhir');
					// 	}
					// 	for(i = 0; i < result.stock.length; i++){
					// 		if (cat[j] == result.stock[i].location) {
					// 			if (result.stock[i].model == 'P-25') {
					// 				p_25.push({y:parseInt(result.stock[i].qty),key:result.stock[i].location});
					// 			}
					// 			if (result.stock[i].model == 'P-32') {
					// 				p_32.push({y:parseInt(result.stock[i].qty),key:result.stock[i].location});
					// 			}
					// 			if (result.stock[i].model == 'P-37') {
					// 				p_37.push({y:parseInt(result.stock[i].qty),key:result.stock[i].location});
					// 			}
					// 			if (cat[j] == 'PN_Pureto') {
					// 				pn_pureto = pn_pureto + parseInt(result.stock[i].qty);
					// 			}
					// 			if (cat[j] == 'PN_Kensa_Awal') {
					// 				pn_kensa = pn_kensa + parseInt(result.stock[i].qty);
					// 			}
					// 		}
					// 	}
					// }

					var schedule_ps25f = 0;
					var schedule_p25 = 0;
					var schedule_p32 = 0;
					var schedule_p37 = 0;
					var schedule_total = 0;

					for(var i = 0; i < result.schedule.length;i++){
						if (result.schedule[i].model == 'PS25F') {
							schedule_ps25f = schedule_ps25f + parseInt(result.schedule[i].quantity);
							schedule_total = schedule_total + parseInt(result.schedule[i].quantity);
						}
						if (result.schedule[i].model == 'P32EP' || result.schedule[i].model == 'P32D') {
							schedule_p32 = schedule_p32 + parseInt(result.schedule[i].quantity);
							schedule_total = schedule_total + parseInt(result.schedule[i].quantity);
						}
						if (result.schedule[i].model == 'P25F') {
							schedule_p32 = schedule_p32 + parseInt(result.schedule[i].quantity);
							schedule_total = schedule_total + parseInt(result.schedule[i].quantity);
						}
						if (result.schedule[i].model == 'P37D' || result.schedule[i].model == 'P37EBR' || result.schedule[i].model == 'P37EBK' || result.schedule[i].model == 'P37ERD') {
							schedule_p37 = schedule_p37 + parseInt(result.schedule[i].quantity);
							schedule_total = schedule_total + parseInt(result.schedule[i].quantity);
						}
					}

					category.push('Pianica Tuning');
					target.push(parseInt('360'));
					target.push(parseInt('270'));
					category.push('Pianica Kensa Suara Awal');
					target.push(parseInt('270'));
					category.push('Pianica Kensa Suara Akhir');

					for(i = 0; i < result.stock.length; i++){
						if (result.stock[i].model == 'P-25') {
							p_25.push({y:parseInt(result.stock[i].pureto),key:'PN_Pureto'});
							pn_pureto = pn_pureto + parseInt(result.stock[i].pureto);
							p_25.push({y:parseInt(result.stock[i].kensa_awal),key:'PN_Kensa_Awal'});
							pn_kensa_awal = pn_kensa_awal + parseInt(result.stock[i].kensa_awal);
							p_25.push({y:parseInt(result.stock[i].assembly),key:'PN_Assembly'});
							pn_assembly = pn_assembly + parseInt(result.stock[i].assembly);
							p_25.push({y:parseInt(result.stock[i].kensa_akhir),key:'PN_Kensa_Akhir'});
							pn_kensa_akhir = pn_kensa_akhir + parseInt(result.stock[i].kensa_akhir);
							$('#p25_tuning').html(result.stock[i].pureto);
							$('#p25_kensa_awal').html(result.stock[i].kensa_awal);
							$('#p25_kensa_akhir').html(result.stock[i].kensa_akhir);
							$('#p25_assembly').html(result.stock[i].assembly);
						}
						if (result.stock[i].model == 'P-32') {
							p_32.push({y:parseInt(result.stock[i].pureto),key:'PN_Pureto'});
							pn_pureto = pn_pureto + parseInt(result.stock[i].pureto);
							p_32.push({y:parseInt(result.stock[i].pureto),key:'PN_Kensa_Awal'});
							pn_kensa_awal = pn_kensa_awal + parseInt(result.stock[i].kensa_awal);
							p_32.push({y:parseInt(result.stock[i].assembly),key:'PN_Assembly'});
							pn_assembly = pn_assembly + parseInt(result.stock[i].assembly);
							p_32.push({y:parseInt(result.stock[i].pureto),key:'PN_Kensa_Akhir'});
							pn_kensa_akhir = pn_kensa_akhir + parseInt(result.stock[i].kensa_akhir);

							$('#p32_tuning').html(result.stock[i].pureto);
							$('#p32_kensa_awal').html(result.stock[i].kensa_awal);
							$('#p32_kensa_akhir').html(result.stock[i].kensa_akhir);
							$('#p32_assembly').html(result.stock[i].assembly);
						}
						if (result.stock[i].model == 'P-37') {
							p_37.push({y:parseInt(result.stock[i].pureto),key:'PN_Pureto'});
							pn_pureto = pn_pureto + parseInt(result.stock[i].pureto);
							p_37.push({y:parseInt(result.stock[i].pureto),key:'PN_Kensa_Awal'});
							pn_kensa_awal = pn_kensa_awal + parseInt(result.stock[i].kensa_awal);
							p_37.push({y:parseInt(result.stock[i].assembly),key:'PN_Assembly'});
							pn_assembly = pn_assembly + parseInt(result.stock[i].assembly);
							p_37.push({y:parseInt(result.stock[i].pureto),key:'PN_Kensa_Akhir'});
							pn_kensa_akhir = pn_kensa_akhir + parseInt(result.stock[i].kensa_akhir);

							$('#p37_tuning').html(result.stock[i].pureto);
							$('#p37_kensa_awal').html(result.stock[i].kensa_awal);
							$('#p37_kensa_akhir').html(result.stock[i].kensa_akhir);
							$('#p37_assembly').html(result.stock[i].assembly);
						}
						if (result.stock[i].model == 'PS-25F') {
							p_25f.push({y:parseInt(result.stock[i].pureto),key:'PN_Pureto'});
							pn_pureto = pn_pureto + parseInt(result.stock[i].pureto);
							p_25f.push({y:parseInt(result.stock[i].pureto),key:'PN_Kensa_Awal'});
							pn_kensa_awal = pn_kensa_awal + parseInt(result.stock[i].kensa_awal);
							p_25f.push({y:parseInt(result.stock[i].assembly),key:'PN_Assembly'});
							pn_assembly = pn_assembly + parseInt(result.stock[i].assembly);
							p_25f.push({y:parseInt(result.stock[i].pureto),key:'PN_Kensa_Akhir'});
							pn_kensa_akhir = pn_kensa_akhir + parseInt(result.stock[i].kensa_akhir);

							$('#ps25f_tuning').html(result.stock[i].pureto);
							$('#ps25f_kensa_awal').html(result.stock[i].kensa_awal);
							$('#ps25f_kensa_akhir').html(result.stock[i].kensa_akhir);
							$('#ps25f_assembly').html(result.stock[i].assembly);
						}
					}

					// $('#div_pureto').prop('class','small-box');
					// if (pn_pureto > 360) {
					// 	$('#div_pureto').removeAttr('class');
					// 	$('#div_pureto').prop('class','small-box sedang');
					// }
					// $('#pn_pureto').html(pn_pureto);

					// $('#div_kensa_awal').prop('class','small-box');
					// if (pn_kensa_awal > 270) {
					// 	$('#div_kensa_awal').removeAttr('class');
					// 	$('#div_kensa_awal').prop('class','small-box sedang');
					// }
					// $('#pn_kensa_awal').html(pn_kensa_awal);

					// $('#div_kensa_akhir').prop('class','small-box');
					// if (pn_kensa_akhir > 270) {
					// 	$('#div_kensa_akhir').removeAttr('class');
					// 	$('#div_kensa_akhir').prop('class','small-box sedang');
					// }
					// $('#pn_kensa_akhir').html(pn_kensa_akhir);

					$('#total_tuning').html(pn_pureto+' ('+(pn_pureto/schedule_total).toFixed(1)+' D)');
					$('#total_kensa_awal').html(pn_kensa_awal+' ('+(pn_kensa_awal/schedule_total).toFixed(1)+' D)');
					$('#total_kensa_akhir').html(pn_kensa_akhir+' ('+(pn_kensa_akhir/schedule_total).toFixed(1)+' D)');
					$('#total_assembly').html(pn_assembly+' ('+(pn_assembly/schedule_total).toFixed(1)+' D)');

					for(var i = 0; i < result.timing.length;i++){
						if (result.timing[i].model == 'P-25') {
							$('#p25_timing').html(result.timing[i].avg);
						}
						if (result.timing[i].model == 'PS-25F') {
							$('#ps25f_timing').html(result.timing[i].avg);
						}
						if (result.timing[i].model == 'P-32') {
							$('#p32_timing').html(result.timing[i].avg);
						}
						if (result.timing[i].model == 'P-37') {
							$('#p37_timing').html(result.timing[i].avg);
						}
					}

					for(var i = 0; i < result.timing_ideal.length;i++){
						if (result.timing_ideal[i].model == 'P-25') {
							$('#p25_timing_ideal').html(result.timing_ideal[i].avg);
						}
						if (result.timing_ideal[i].model == 'PS-25F') {
							$('#ps25f_timing_ideal').html(result.timing_ideal[i].avg);
						}
						if (result.timing_ideal[i].model == 'P-32') {
							$('#p32_timing_ideal').html(result.timing_ideal[i].avg);
						}
						if (result.timing_ideal[i].model == 'P-37') {
							$('#p37_timing_ideal').html(result.timing_ideal[i].avg);
						}
					}

					// $('#actual_tact_time').html(result.tact[0].avg);
					// $('#ideal_tact_time').html((parseInt('{{$target}}')/465).toFixed(2));

					var person_pureto = 0;
					var person_kensa_awal = 0;
					var person_assembly = 0;
					var person_kensa_akhir = 0;

					if(result.person_all.length > 0){
						for(var i = 0; i < result.person_all.length;i++){
							if (result.person_all[i].location == 'PN_Pureto') {
								person_pureto = person_pureto + result.person_all[i].qty;
							}
							if (result.person_all[i].location == 'PN_Assembly') {
								person_assembly = person_assembly + result.person_all[i].qty;
							}
							if (result.person_all[i].location == 'PN_Kensa_Akhir') {
								person_kensa_akhir = person_kensa_akhir + result.person_all[i].qty;
							}
							if (result.person_all[i].location == 'PN_Kensa_Awal') {
								person_kensa_awal = person_kensa_awal + result.person_all[i].qty;
							}
						}
					}
					var ideal_tuning = 0;
					if (result.person_tuning.length > 0) {
						ideal_tuning = 21 * (parseInt(result.person_tuning[0].qty) + person_pureto);
					}else{
						ideal_tuning = 21;
					}

					var ideal_kensa_awal = 0;
					if (person_kensa_awal == 0) {
						ideal_kensa_awal = 22;
					}else{
						ideal_kensa_awal = 22 * person_kensa_awal;
					}

					var ideal_kensa_akhir = 0;
					if (person_kensa_akhir == 0) {
						ideal_kensa_akhir = 10;
					}else{
						ideal_kensa_akhir = 10 * person_kensa_akhir;
					}

					var ideal_assembly = 0;
					if (person_assembly == 0) {
						ideal_assembly = 10;
					}else{
						ideal_assembly = 10 * person_assembly;
					}
					$('#ideal_material_awal').html((parseInt('{{$target}}')/2).toFixed(0)+' ('+((parseInt('{{$target}}')/2)/schedule_total).toFixed(1)+' D)');
					$('#ideal_tuning').html(ideal_tuning+' ('+(ideal_tuning/schedule_total).toFixed(1)+' D)');
					$('#ideal_kensa_awal').html(ideal_kensa_awal+' ('+(ideal_kensa_awal/schedule_total).toFixed(1)+' D)');
					$('#ideal_assembly').html(ideal_assembly+' ('+(ideal_assembly/schedule_total).toFixed(1)+' D)');
					$('#ideal_kensa_akhir').html(ideal_kensa_akhir+' ('+(ideal_kensa_akhir/schedule_total).toFixed(1)+' D)');

					var material_awal = parseInt((parseInt('{{$target}}')/2).toFixed(0));

					$('#ideal_material_awal2').html(material_awal);
					$('#ideal_tuning2').html(ideal_tuning);
					$('#ideal_kensa_awal2').html(ideal_kensa_awal);
					$('#ideal_assembly2').html(ideal_assembly);
					$('#ideal_kensa_akhir2').html(ideal_kensa_akhir);

					var p32_awal = 0,p25_awal = 0,ps25f_awal = 0,p37_awal = 0;

					var all_awal = 0;

					for(var i = 0; i < result.stock_awal.length;i++){
						if (result.stock_awal[i].model == 'P-32') {
							p32_awal = p32_awal + parseInt(result.stock_awal[i].quantity);
						}
						if (result.stock_awal[i].model == 'P-37') {
							p37_awal = p37_awal + parseInt(result.stock_awal[i].quantity);
						}
						if (result.stock_awal[i].model == 'P-25') {
							p25_awal = p25_awal + parseInt(result.stock_awal[i].quantity);
						}
						if (result.stock_awal[i].model == 'PS-25F') {
							ps25f_awal = ps25f_awal + parseInt(result.stock_awal[i].quantity);
						}
						all_awal = all_awal + parseInt(result.stock_awal[i].quantity);
					}

					var total_ideal = 0;
					total_ideal = material_awal + ideal_tuning + ideal_kensa_awal + ideal_assembly + ideal_kensa_akhir;
					$('#ideal_total').html(total_ideal+' ('+(total_ideal/schedule_total).toFixed(1)+' D)');

					$('#total_material_awal').html(all_awal+' ('+(all_awal/schedule_total).toFixed(1)+' D)');
					$('#p32_material_awal').html(p32_awal);
					$('#p25_material_awal').html(p25_awal);
					$('#p37_material_awal').html(p37_awal);
					$('#ps25f_material_awal').html(ps25f_awal);

					var total_ideal_p25 = 0;
					total_ideal_p25 = p25_awal + parseInt($('#p25_tuning').text()) + parseInt($('#p25_kensa_awal').text()) + parseInt($('#p25_assembly').text()) + parseInt($('#p25_kensa_akhir').text());
					$('#p25_total').html(total_ideal_p25);

					var total_ideal_p32 = 0;
					total_ideal_p32 = p32_awal + parseInt($('#p32_tuning').text()) + parseInt($('#p32_kensa_awal').text()) + parseInt($('#p32_assembly').text()) + parseInt($('#p32_kensa_akhir').text());
					$('#p32_total').html(total_ideal_p32);

					var total_ideal_p37 = 0;
					total_ideal_p37 = p37_awal + parseInt($('#p37_tuning').text()) + parseInt($('#p37_kensa_awal').text()) + parseInt($('#p37_assembly').text()) + parseInt($('#p37_kensa_akhir').text());
					$('#p37_total').html(total_ideal_p37);

					var total_ideal_ps25f = 0;
					total_ideal_ps25f = ps25f_awal + parseInt($('#ps25f_tuning').text()) + parseInt($('#ps25f_kensa_awal').text()) + parseInt($('#ps25f_assembly').text()) + parseInt($('#ps25f_kensa_akhir').text());
					$('#ps25f_total').html(total_ideal_ps25f);

					var total_total = total_ideal_p25 + total_ideal_ps25f + total_ideal_p32 + total_ideal_p37;
					$('#total_total').html(total_total+' ('+(total_total/schedule_total).toFixed(1)+' D)');

					series_line_1 = [];
					cat_line_1 = [];
					series_line_2 = [];
					cat_line_2 = [];
					series_line_3 = [];
					cat_line_3 = [];
					series_line_4 = [];
					cat_line_4 = [];
					series_line_5 = [];
					cat_line_5 = [];

					// for(var i = 0; i < result.tact_pureto.length;i++){
					// 	if (result.tact_pureto[i].line == '1') {
					// 		cat_line_1.push('Tuning');
					// 		series_line_1.push(parseFloat(result.tact_pureto[i].diff));
					// 	}
					// 	if (result.tact_pureto[i].line == '2') {
					// 		cat_line_2.push('Tuning');
					// 		series_line_2.push(parseFloat(result.tact_pureto[i].diff));
					// 	}
					// 	if (result.tact_pureto[i].line == '3') {
					// 		cat_line_3.push('Tuning');
					// 		series_line_3.push(parseFloat(result.tact_pureto[i].diff));
					// 	}
					// 	if (result.tact_pureto[i].line == '4') {
					// 		cat_line_4.push('Tuning');
					// 		series_line_4.push(parseFloat(result.tact_pureto[i].diff));
					// 	}
					// 	if (result.tact_pureto[i].line == '5') {
					// 		cat_line_5.push('Tuning');
					// 		series_line_5.push(parseFloat(result.tact_pureto[i].diff));
					// 	}
					// }

					var target1 = [],target2 = [],target3 = [],target4 = [],target5 = [];

					var target1s = 0;
					var target2s = 0;
					var target3s = 0;
					var target4s = 0;
					var target5s = 0;

					for(var i = 0; i < result.target.length;i++){
						if (result.target[i].line == '1') {
							target1s = result.target[i].target;
						}
						if (result.target[i].line == '2') {
							target2s = result.target[i].target;
						}
						if (result.target[i].line == '3') {
							target3s = result.target[i].target;
						}
						if (result.target[i].line == '4') {
							target4s = result.target[i].target;
						}
						if (result.target[i].line == '5') {
							target5s = result.target[i].target;
						}
					}

					for(var i = 0; i < result.tact_kensa_awal.length;i++){
						if (result.tact_kensa_awal[i].line == '1') {
							cat_line_1.push('Kensa Awal');
							series_line_1.push(parseFloat(result.tact_kensa_awal[i].diff));
							target1.push(parseFloat(target1s));
						}
						if (result.tact_kensa_awal[i].line == '2') {
							cat_line_2.push('Kensa Awal');
							series_line_2.push(parseFloat(result.tact_kensa_awal[i].diff));
							target2.push(parseFloat(target2s));
						}
						if (result.tact_kensa_awal[i].line == '3') {
							cat_line_3.push('Kensa Awal');
							series_line_3.push(parseFloat(result.tact_kensa_awal[i].diff));
							target3.push(parseFloat(target3s));
						}
						if (result.tact_kensa_awal[i].line == '4') {
							cat_line_4.push('Kensa Awal');
							series_line_4.push(parseFloat(result.tact_kensa_awal[i].diff));
							target4.push(parseFloat(target4s));
						}
						if (result.tact_kensa_awal[i].line == '5') {
							cat_line_5.push('Kensa Awal');
							series_line_5.push(parseFloat(result.tact_kensa_awal[i].diff));
							target5.push(parseFloat(target5s));
						}
					}

					for(var i = 0; i < result.tact_assembly.length;i++){
						if (result.tact_assembly[i].line == '1') {
							cat_line_1.push('Assembly');
							series_line_1.push(parseFloat(result.tact_assembly[i].diff));
							target1.push(parseFloat(target1s));
						}
						if (result.tact_assembly[i].line == '2') {
							cat_line_2.push('Assembly');
							series_line_2.push(parseFloat(result.tact_assembly[i].diff));
							target2.push(parseFloat(target2s));
						}
						if (result.tact_assembly[i].line == '3') {
							cat_line_3.push('Assembly');
							series_line_3.push(parseFloat(result.tact_assembly[i].diff));
							target3.push(parseFloat(target3s));
						}
						if (result.tact_assembly[i].line == '4') {
							cat_line_4.push('Assembly');
							series_line_4.push(parseFloat(result.tact_assembly[i].diff));
							target4.push(parseFloat(target4s));
						}
						if (result.tact_assembly[i].line == '5') {
							cat_line_5.push('Assembly');
							series_line_5.push(parseFloat(result.tact_assembly[i].diff));
							target5.push(parseFloat(target5s));
						}
					}

					for(var i = 0; i < result.tact_kensa_akhir.length;i++){
						if (result.tact_kensa_akhir[i].line == '1') {
							cat_line_1.push('Kensa Akhir');
							series_line_1.push(parseFloat(result.tact_kensa_akhir[i].diff));
							target1.push(parseFloat(target1s));
						}
						if (result.tact_kensa_akhir[i].line == '2') {
							cat_line_2.push('Kensa Akhir');
							series_line_2.push(parseFloat(result.tact_kensa_akhir[i].diff));
							target2.push(parseFloat(target2s));
						}
						if (result.tact_kensa_akhir[i].line == '3') {
							cat_line_3.push('Kensa Akhir');
							series_line_3.push(parseFloat(result.tact_kensa_akhir[i].diff));
							target3.push(parseFloat(target3s));
						}
						if (result.tact_kensa_akhir[i].line == '4') {
							cat_line_4.push('Kensa Akhir');
							series_line_4.push(parseFloat(result.tact_kensa_akhir[i].diff));
							target4.push(parseFloat(target4s));
						}
						if (result.tact_kensa_akhir[i].line == '5') {
							cat_line_5.push('Kensa Akhir');
							series_line_5.push(parseFloat(result.tact_kensa_akhir[i].diff));
							target5.push(parseFloat(target5s));
						}
					}

					for(var i = 0; i < result.tact.length;i++){
						if (result.tact[i].line == '1') {
							cat_line_1.push('Kakunin Visual');
							series_line_1.push(parseFloat(result.tact[i].diff));
							target1.push(parseFloat(target1s));
						}
						if (result.tact[i].line == '2') {
							cat_line_2.push('Kakunin Visual');
							series_line_2.push(parseFloat(result.tact[i].diff));
							target2.push(parseFloat(target2s));
						}
						if (result.tact[i].line == '3') {
							cat_line_3.push('Kakunin Visual');
							series_line_3.push(parseFloat(result.tact[i].diff));
							target3.push(parseFloat(target3s));
						}
						if (result.tact[i].line == '4') {
							cat_line_4.push('Kakunin Visual');
							series_line_4.push(parseFloat(result.tact[i].diff));
							target4.push(parseFloat(target4s));
						}
						if (result.tact[i].line == '5') {
							cat_line_5.push('Kakunin Visual');
							series_line_5.push(parseFloat(result.tact[i].diff));
							target5.push(parseFloat(target5s));
						}
					}

					Highcharts.chart('container_1', {
						chart: {
							type: 'column',
							height:'200'
						},
						title: {
							useHTML: true,
             				text: '<div style="cursor:pointer" id="tact_1">Tact Time Line 1</div>',
							style: {
								fontSize: '17px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: cat_line_1,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Tact Time (Minutes)',
								style:{
									color:'#fff'
								}
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								// cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Waktu Proses',
							data: series_line_1,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										// fillModalTuning(this.category );
									}
								}
							}
						},
						{
							type: 'line',
							data: target1,
							name: "Tact Time",
							colorByPoint: false,
							color: "#fff",
							animation: false,
							dashStyle:'shortdash',
							lineWidth: 4,
							marker: {
				                radius: 4,
				                lineColor: '#fff',
				                lineWidth: 1
				            },
						},
						]
					});


					Highcharts.chart('container_2', {
						chart: {
							type: 'column',
							height:'200'
						},
						title: {
							useHTML: true,
             				text: '<div style="cursor:pointer" id="tact_2">Tact Time Line 2</div>',
							style: {
								fontSize: '17px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: cat_line_2,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Tact Time (Minutes)',
								style:{
									color:'#fff'
								}
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								// cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Waktu Proses',
							data: series_line_2,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										// fillModalTuning(this.category );
									}
								}
							}
						},
						{
							type: 'line',
							data: target2,
							name: "Tact Time",
							colorByPoint: false,
							color: "#fff",
							animation: false,
							dashStyle:'shortdash',
							lineWidth: 4,
							marker: {
				                radius: 4,
				                lineColor: '#fff',
				                lineWidth: 1
				            },
						},
						]
					});

					Highcharts.chart('container_3', {
						chart: {
							type: 'column',
							height:'200'
						},
						title: {
							useHTML: true,
             				text: '<div style="cursor:pointer" id="tact_3">Tact Time Line 3</div>',
							style: {
								fontSize: '17px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: cat_line_3,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Tact Time (Minutes)',
								style:{
									color:'#fff'
								}
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								// cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Waktu Proses',
							data: series_line_3,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										// fillModalTuning(this.category );
									}
								}
							}
						},
						{
							type: 'line',
							data: target3,
							name: "Tact Time",
							colorByPoint: false,
							color: "#fff",
							animation: false,
							dashStyle:'shortdash',
							lineWidth: 4,
							marker: {
				                radius: 4,
				                lineColor: '#fff',
				                lineWidth: 1
				            },
						},
						]
					});

					Highcharts.chart('container_4', {
						chart: {
							type: 'column',
							height:'200'
						},
						title: {
							useHTML: true,
             				text: '<div style="cursor:pointer" id="tact_4">Tact Time Line 4</div>',
							style: {
								fontSize: '17px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: cat_line_4,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Tact Time (Minutes)',
								style:{
									color:'#fff'
								}
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								// cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Waktu Proses',
							data: series_line_4,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										// fillModalTuning(this.category );
									}
								}
							}
						},
						{
							type: 'line',
							data: target4,
							name: "Tact Time",
							colorByPoint: false,
							color: "#fff",
							animation: false,
							dashStyle:'shortdash',
							lineWidth: 4,
							marker: {
				                radius: 4,
				                lineColor: '#fff',
				                lineWidth: 1
				            },
						},
						]
					});

					Highcharts.chart('container_5', {
						chart: {
							type: 'column',
							height:'200'
						},
						title: {
							useHTML: true,
             				text: '<div style="cursor:pointer" id="tact_5">Tact Time Line 5</div>',
							style: {
								fontSize: '17px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: cat_line_5,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Tact Time (Minutes)',
								style:{
									color:'#fff'
								}
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								// cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Waktu Proses',
							data: series_line_5,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										// fillModalTuning(this.category );
									}
								}
							}
						},
						{
							type: 'line',
							data: target5,
							name: "Tact Time",
							colorByPoint: false,
							color: "#fff",
							animation: false,
							dashStyle:'shortdash',
							lineWidth: 4,
							marker: {
				                radius: 4,
				                lineColor: '#fff',
				                lineWidth: 1
				            },
						},
						]
					});

					document.getElementById("tact_1").onclick = function(){
						inputTarget(1);
					}

					document.getElementById("tact_2").onclick = function(){
						inputTarget(2);
					}

					document.getElementById("tact_3").onclick = function(){
						inputTarget(3);
					}

					document.getElementById("tact_4").onclick = function(){
						inputTarget(4);
					}

					document.getElementById("tact_5").onclick = function(){
						inputTarget(5);
					}

					$('#div_con_1').show();
					$('#div_con_2').show();
					$('#div_con_3').show();
					$('#div_con_4').show();
					$('#div_con_5').show();

					if (series_line_1.length == 0) {
						$('#div_con_1').hide();
					}
					if (series_line_2.length == 0) {
						$('#div_con_2').hide();
					}
					if (series_line_3.length == 0) {
						$('#div_con_3').hide();
					}
					if (series_line_4.length == 0) {
						$('#div_con_4').hide();
					}
					if (series_line_5.length == 0) {
						$('#div_con_5').hide();
					}


					$('#loading').hide();
				}else{
					alert('Fill Data Failed');
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
			[0, '#2a2a2b']
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
		// gridLineColor: '#707073',
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
		gridLineWidth: 0,
        minorGridLineWidth: 0,
		labels: {
			style: {
				color: '#fff'
			}
		},
		// lineColor: '#707073',
		// minorGridLineColor: '#505053',
		// tickColor: '#707073',
		// tickWidth: 1,
		// title: {
		// 	style: {
		// 		color: '#A0A0A3'
		// 	}
		// }
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
			// gridLineColor: '#505053'
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
@stop