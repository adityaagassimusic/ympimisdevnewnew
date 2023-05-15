@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.highlight-process {
		font-size : 1.3vw;
		font-weight: bold;
		color: green;
		text-shadow: 1px 1px 2px #ccff90;
	}
	.highlight-black {
		font-size : 1.3vw;
		font-weight: bold;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1 style="font-size: 1.5vw">
		Welding Jig Handling&nbsp;<small class="text-purple" style="font-size: 1vw">溶接冶具のハンドリング</small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.5vw; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/jig_data') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;"><span class="highlight-process">Jig Kensa</span> Data</a>
			<a href="{{ url('index/welding/jig_bom') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;"><span class="highlight-process">Jig Kensa</span> BOM</a>
			<a href="{{ url('index/welding/jig_schedule') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;"><span class="highlight-process">Jig Kensa</span> Schedule Check</a>
			<a href="{{ url('index/welding/kensa_point') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;"><span class="highlight-process">Jig Kensa</span> Point Check Kensa</a>
			<a href="{{ url('index/welding/jig_part') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;"><span class="highlight-process">Jig Kensa</span> Spare Part Data</a>

			<a href="{{ url('index/welding/jig_data_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;background-color: #e6e6e6"><span class="highlight-black">Jig Proses</span> Data</a>
			<a href="{{ url('index/welding/jig_bom_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;background-color: #e6e6e6"><span class="highlight-black">Jig Proses</span> BOM</a>
			<a href="{{ url('index/welding/jig_schedule_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;background-color: #e6e6e6"><span class="highlight-black">Jig Proses</span> Schedule Check</a>
			<a href="{{ url('index/welding/kensa_point_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;background-color: #e6e6e6"><span class="highlight-black">Jig Proses</span> Point Check Kensa</a>
			<a href="{{ url('index/welding/jig_part_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: black;background-color: #e6e6e6"><span class="highlight-black">Jig Proses</span> Spare Part Data</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.5vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/kensa_jig') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: green;">Pengecekan <span class="highlight-process">Jig Kensa</span></a>
			<a href="{{ url('index/welding/repair_jig') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: green;">Perbaikan <span class="highlight-process">Jig Kensa</span></a>

			<button class="btn btn-default btn-block" onclick="$('#modalJigProses').modal('show')" style="font-size: 1.2vw; border-color: green;background-color: #ccff90">Pengecekan <span class="highlight-black">Jig Proses</span></button>
			<button class="btn btn-default btn-block" onclick="$('#modalJigProsesRepair').modal('show')" style="font-size: 1.2vw; border-color: green;background-color: #ccff90">Perbaikan <span class="highlight-black">Jig Proses</span></button>
			<br>
			<span style="font-size: 1.5vw; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/monitoring_jig') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: red;">Monitoring <span class="highlight-process">Jig Kensa</span></a>

			<a href="{{ url('index/welding/monitoring_jig_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: red;;background-color: #ff9e9e">Monitoring <span class="highlight-black">Jig Proses</span></a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 1.5vw; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/kensa_jig_report') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: purple;">Laporan Pengecekan <span class="highlight-process">Jig Kensa</span></a>
			<a href="{{ url('index/welding/repair_jig_report') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: purple;">Laporan Perbaikan <span class="highlight-process">Jig Kensa</span></a>

			<a href="{{ url('index/welding/kensa_jig_report_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: purple;background-color: #c4c2ff">Laporan Pengecekan <span class="highlight-black">Jig Proses</span></a>
			<a href="{{ url('index/welding/repair_jig_report_proses') }}" class="btn btn-default btn-block" style="font-size: 1.2vw; border-color: purple;background-color: #c4c2ff">Laporan Perbaikan <span class="highlight-black">Jig Proses</span></a>
		</div>
	</div>

	<div class="modal fade" id="modalJigProses">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						PILIH JIG
					</h4>
				</div>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12">
						<select class="form-control select2" name="jig_proses" id="jig_proses" data-placeholder="Pilih Jig ID" style="width: 100%;">
							<option></option>
							@foreach($jigs as $jigs)
								<option value="{{$jigs->jig_id}}">{{$jigs->jig_id}} - {{$jigs->jig_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-xs-12">
						<div class="modal-footer">
							<div class="row">
								<button style="width: 100%;font-weight: bold;font-size: 20px;" class="btn btn-success" onclick="saveKensaJigProses()">
									CONFIRM
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalJigProsesRepair">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						PILIH JIG REPAIR
					</h4>
				</div>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12">
						<select class="form-control select2" name="jig_repair" id="jig_repair" data-placeholder="Pilih Jig ID" style="width: 100%;">
							<option></option>
							@foreach($jig_repair as $jig_repair)
								<option value="{{$jig_repair->jig_id}}">{{$jig_repair->jig_id}} - {{$jig_repair->jig_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-xs-12">
						<div class="modal-footer">
							<div class="row">
								<button style="width: 100%;font-weight: bold;font-size: 20px;" class="btn btn-success" onclick="saveKensaJigProsesRepair()">
									CONFIRM
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});
		$('body').toggleClass("sidebar-collapse");
		$('#jig_proses').val('');
	});

	function saveKensaJigProses() {
		var url = '{{url("index/welding/kensa_jig_proses/")}}';
		window.location.href = url+'/'+$('#jig_proses').val()+'/home';
	}

	function saveKensaJigProsesRepair() {
		var url = '{{url("index/welding/repair_jig_proses/")}}';
		window.location.href = url+'/'+$('#jig_repair').val()+'/home';
	}
</script>
@endsection