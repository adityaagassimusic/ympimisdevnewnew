@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }

	.container_{margin:10px;padding:5px;border:solid 1px #eee;}
	.image_upload > input{display:none;}
	input[type=text]{width:220px;height:auto;}
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 40%;">
			<span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="form-group">
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px">
										<span style="font-weight: bold; font-size: 1.6vw;">{{ $title }}<br><small class="text-purple">{{ $title_jp }}</small></span>
									</div>
									<div class="col-xs-12">
										<table style="border:1px solid black; border-collapse: collapse; width: 70%; padding-top: 0px" align="center">
											<thead style="background-color: #BDD5EA;">
												<tr>
													<th style="width: 40%; border:1px solid black; text-align: center; font-size: 20px">Point</th>
													<th style="width: 60%; border:1px solid black; text-align: center; font-size: 20px">Content</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Judul Dokumen (資料名)</td>
													<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data['appr_sends']->description }}</td>
												</tr>

												<tr>
													<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">No Approval (承認番号)</td>
													<td style="font-size: 15px; border: 1px solid black; padding-left: 20px" id="no_approval">{{ $data['appr_sends']->no_transaction }}</td>
												</tr>
												<tr>
													<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">No Dokumen (資料番号)</td>
													<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data['appr_sends']->no_dokumen }}</td>
												</tr>
											</tr>              
											<?php
											$identitas = explode("/",$data['appr_sends']->nik);
											?> 
											<tr>
												<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Pembuat (作成者)</td>
												<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $identitas[0] }} - {{ $identitas[1] }}</td>
											</tr>
											<tr>
												<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px;">Department (課)</td>
												<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data['appr_sends']->department }}</td>
											</tr>
											<tr>
												<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Tanggal Pembuatan (作成日)</td>
												<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data['appr_sends']->created_at }}</td>
											</tr>
											<tr>
												<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Catatan (備考)</td>
												<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data['appr_sends']->summary }}</td>
											</tr>
											@if($data['appr_sends']->answer != null)
											<tr>
												<td style="width: 1%; border:1px solid black; background-color: yellow">Answer (備考)</td>
												<td style="border:1px solid black; text-align: left !important; background-color: yellow">File {{ $data['appr_sends']->answer }}</td>
											</tr>
											@endif		
										</tbody>
									</table><br><br>
									<form method="post" action="{{ url('simpan/tanggapan') }}" enctype="multipart/form-data" onsubmit="Loading()">
										<input type="hidden" value="{{csrf_token()}}" name="_token" />
										<input type="hidden" value="{{ $data['appr_sends']->no_transaction }}" name="no_approval">
										<table style="border:1px solid black; border-collapse: collapse; width: 70%; padding-top: 0px" align="center">
											<tr>
												<td style="width: 40%; font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Alasan Hold</td>
												<td style="width: 60%; font-size: 15px; border: 1px solid black; padding-left: 20px">
													<?php print_r(nl2br($data['appr_sends']->comment)) ?>
												</td>
											</tr>
											<tr>
												<td style="width: 40%; font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Upload Dokumen Perbaikan</td>
												<td style="width: 60%; font-size: 15px; border: 1px solid black; padding-left: 20px; text-align: center">
													<input type="file" name="file_gambar" id="file_gambar" onchange="Test()" accept="application/pdf" style="padding-top: 10px; padding-bottom: 10px">
												</td>
											</tr>
										</table><br><br>
									</div>
									<iframe id="muncul" width=70% height=600></iframe>
									<br>
									<button id="tombol_muncul" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 100%;" type="submit">Kirim Pengajuan<br>提出物を提出する</button>
								</form>
							</div>
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

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#muncul").hide();
		$("#tombol_muncul").hide();
	});

	function Loading(){
		$("#loading").show();
	}

	function Test() {
		$("#muncul").show();
		$("#tombol_muncul").show();
		document.getElementById("muncul").style.display = "block";
		var oFReader = new FileReader();
		oFReader.readAsDataURL(document.getElementById("file_gambar").files[0]);
		oFReader.onload = function(oFREvent) {
			document.getElementById("muncul").src = oFREvent.target.result;
		};
	};

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