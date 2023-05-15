@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #dcdcdc;
		border: 1px solid black !important;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		color: black;
		/*background-color: white;*/
	}
	thead {
		/*background-color: rgb(126,86,134);*/
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
		font-weight: bold;
		font-size: 20px;
	}

	#loading, #error { display: none; }

	.blink{
		animation:blinker 1s linear infinite;
	}
	@keyframes blinker {
		50% {background-color: red},
		50% {background-color: yellow}
	}
</style>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 40%;">
			<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="col-xs-12" style="background-color: #FFC300; text-align: center; margin-bottom: 5px;">
		<div class="col-xs-12">
			<span style="font-weight: bold; font-size: 1.6vw;"><i class="fa fa-file-text-o"></i> Confirmation Loading WWT</span><br>
			<span style="font-weight: bold; font-size: 1.6vw;" id="slip_disposal">{{ $slip_disposal }}</span>
		</div>
	</div>
	<div class="col-xs-12" style="background-color: white; text-align: center; padding-top: 10px;">
		<div class="col-xs-8">
			<center>
				<?php
				$jumlah_all = 0;
				$all = 0;
				for ($i=0; $i < count($data['resumes']); $i++) { 
					$b = explode(',', $data['resumes'][$i]->jenis);
					$c = explode(',', $data['resumes'][$i]->quantity);
					$d = explode(',', $data['resumes'][$i]->slip);
					$e = explode(',', $data['resumes'][$i]->kode_limbah);
					$no = 1;
					if ($i == 0 || ($i+1) % 1 == 0) {
						?>
						<table class="table table-bordered" style="width: 100%; border-collapse: collapse; text-align: center;" cellspacing="0">
							<tbody align="center">
								<?php
							}
							for ($z=0; $z < count($data['resumes']); $z++) {
								if ($z == $i) {
									if ($z == 0 || ($z+1) % 1 == 0) 
										echo '<tr>';
									print_r('<td style="width: 1%; text-align: center">
										<table style="border-collapse: collapse; width: 100%">
										<thead>
										<tr align="center">
										<th colspan="4" style="border:1px solid black; font-size: 15px; background-color: #f6d965; height: 20; text-align: center;">Limbah '.$b[0].' ('.$e[0].')</th>
										</tr>
										<tr align="center"> 
										<td style="border:1px solid black; width: 10%; height: 20;">NO. LIMBAH</td>
										<td style="border:1px solid black; width: 30%; height: 20;">SLIP</td>
										<td style="border:1px solid black; width: 30%; height: 20;">BERAT</td>
										<td style="border:1px solid black; width: 30%; height: 20;">JML. JUMBO BAG</td>
										</tr>
										</thead>
										<tbody id="bodyTableOutstanding">');
										for ($a=0; $a < count($b); $a++) { ?>
											<?php 
											print_r('
												<tr align="center"> 
												<td style="border:1px solid black; height: 20;">'.$no++.'</td>
												<td style="border:1px solid black; height: 20;">'.$d[$a].'</td>
												<td style="border:1px solid black; height: 20;">'.$c[$a].' KG</td>
												<td style="border:1px solid black; height: 20;">1</td>
												</tr>');
											} ?>
											<?php print_r('
												<tr align="center"> 
												<td colspan="2" style="border:1px solid black; height: 20;">JUMLAH</td>
												<td style="border:1px solid black; height: 20;">'.$data['resumes'][$i]->jumlah.' KG</td>
												<td style="border:1px solid black; height: 20;">'.$data['resumes'][$i]->banyak.'</td>
												</tr>
												</tbody>
												</table>
												</td>');
											$jumlah_all += $data['resumes'][$i]->jumlah;
											$all += $data['resumes'][$i]->banyak;
											if (($z+1) == count($data['resumes'])) { echo '</tr>'; }}
										}if (($i+1) % 1 == 0 || ($i+1) == count($data['resumes'])) { ?>
										</tbody>            
									</table>
									<br>
								<?php } } ?>
							</center>
						</div>

					<div class="col-xs-4" style="text-align: center; padding-bottom: 15px">
						<div class="col-xs-12" style="background-color: #DAF7A6; text-align: center;">
							<span style="font-weight: bold; font-size: 1.6vw;">Konfirmasi Disposal</span>
						</div>
						<div class="col-xs-12" style="background-color: white; text-align: center; padding-top: 15px">
							<div class="form-group row" align="center">
								<label for="" class="col-sm-4 control-label" style="color: black;">Vendor<span class="text-red"> :</span></label>
								<div class="col-sm-8">
									<select class="form-control select2" id="nama_vendor" name="nama_vendor" data-placeholder='Pilih' style="width: 100%" required>
										<option value="">&nbsp;</option>
										@foreach($vendor as $vendors)
										<option value="{{$vendors->vendor}}">{{$vendors->vendor}}</option>
										@endforeach
									</select>	
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="background-color: white; text-align: center; padding-top: 15px">
							<div class="form-group row" align="center">
								<label for="" class="col-sm-4 control-label" style="color: black;">Tanggal<span class="text-red"> :</span></label>
								<div class="col-sm-8">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control" id="dari_tanggal" name="dari_tanggal" value="{{ $date_now }}">
									</div>
								</div>
							</div>
						</div>
						<button class="btn btn-success" onclick="Konfirmasi()">Konfirmasi & Download PDF</button>
					</div>
				</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#dari_tanggal').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true,
			autoclose: true
		});

		$('.select2').select2();
	});

	function Konfirmasi(){
		$("#loading").show();
		var slip_disposal = $('#slip_disposal').html();
		var vendor = $('#nama_vendor').val();
		var date = $('#dari_tanggal').val();

		var data = {
			slip_disposal : slip_disposal,
			vendor : vendor,
			date : date
		}
		$.post('{{ url("confirm/save") }}', data, function(result, status, xhr) {
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success!', 'Limbah Di Disposal');
				location.replace("{{url('review/confirm')}}/"+slip_disposal);
			}else{
				openErrorGritter('Error!', result.message);
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
