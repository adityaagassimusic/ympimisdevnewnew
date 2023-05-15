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
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
			<center>
				<h1 style="background-color: #a1887f; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: white; border: 1px solid darkgrey; border-radius: 5px;">
					{{ $title }}
				</h1>
			</center>
			<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
				<span style="font-size: 26px; color: purple; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> MUTASI <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/initial/stock_monitoring/mpro" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-id-badge"></i>
					Report Satu Departemen
				</a>

				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/initial/stock_trend/mpro" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-id-badge"></i>
					Report Antar Departemen
				</a>

				<!-- <span style="font-size: 26px; color: red; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> Antrian <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/lathe_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Lathe Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/mc_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">MC Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/annealing_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Annealing Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/press_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Press Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/sanding_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Sanding Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/cuci_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Cuci Process</span>
				</a> -->
			</div>

			<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
				<span style="font-size: 26px; color: purple; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> TUNJANGAN <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="{{ url('resume/tunjangan/karyawan') }}" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-american-sign-language-interpreting"></i>
					Report Tunjangan
				</a>

				<!-- <span style="font-size: 26px; color: red; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> Antrian <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/lathe_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Lathe Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/mc_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">MC Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/annealing_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Annealing Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/press_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Press Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/sanding_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Sanding Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/cuci_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Cuci Process</span>
				</a> -->
			</div>

			<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
				<span style="font-size: 26px; color: purple; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> CALON KARYAWAN <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="{{ url('fetch/calon/karyawan') }}" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-address-card"></i>
					Report Calon Karyawan
				</a>

				<!-- <span style="font-size: 26px; color: red; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> Antrian <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/lathe_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Lathe Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/mc_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">MC Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/annealing_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Annealing Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/press_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Press Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/sanding_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Sanding Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/cuci_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Cuci Process</span>
				</a> -->
			</div>

			<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
				<span style="font-size: 26px; color: purple; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> DATA KARYAWAN <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="{{ url('index/karyawan_bermasalah') }}" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-user-times"></i>
					Report Pelanggaran Kehadiran
				</a>

				<a target="_blank" href="{{ url('index/employee_end_contract') }}" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-user"></i>
					Report Karyawan Habis Kontrak
				</a>

				<a target="_blank" href="{{ url('index/emp_data') }}" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-user"></i>
					Report Data Karyawan
				</a>

				<a target="_blank" href="{{ url('index/report/bpjs') }}" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-users"></i>
					Report Data Keluarga BPJS
				</a>

				<!-- <span style="font-size: 26px; color: red; font-weight :bold;">
					<i class="fa fa-angle-double-down"></i> Antrian <i class="fa fa-angle-double-down"></i>
				</span>

				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/lathe_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Lathe Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/mc_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">MC Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/annealing_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Annealing Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/press_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Press Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/sanding_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Sanding Process</span>
				</a>
				<a target="_blank" href="http://10.109.33.30/miraidev/public/index/kpp_board/cuci_process" class="btn btn-default btn-social" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
					<i style="font-size: 16px;" class="fa fa-television"></i>
					Kanban Queue <span class="highlight-display">Cuci Process</span>
				</a> -->
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
	});

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