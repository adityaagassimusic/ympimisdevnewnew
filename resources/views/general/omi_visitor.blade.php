@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input:focus, textarea:focus {
		outline: none;
	}
	.content-wrapper{
		background-color: white !important;
		background-image:url({{url('images/omi/covid_bg.jpg')}});
	}
	.clapclap {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: clapclap 1s infinite;  /* Safari 4+ */
		-moz-animation: clapclap 1s infinite;  /* Fx 5+ */
		-o-animation: clapclap 1s infinite;  /* Opera 12+ */
		animation: clapclap 1s infinite;  /* IE 10+, Fx 29+ */
	}
	
	@-webkit-keyframes clapclap {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			/opacity: 0;/
		}
		50%, 100% {
			background-color: rgb(255,255,0);
		}
	}

	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
	
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="text" id="employeeTag" style="width: 100%; background: transparent; border: none; height: 0; padding: 0; margin: 0; position: absolute;">
	<div class="row">
		<div class="col-xs-7">
			<div class="row">
				<center>
					<div class="col-xs-12" id="visitor_appeal" style="font-size: 7vw; padding-right: 0; font-weight: bold;"></div>
					<div class="col-xs-12" id="count_detail" style="color: white; font-weight: bold; font-size: 2vw;"></div>
					<div class="col-xs-12" id="visitor_count" style="font-size: 24vw; font-weight: bold;"></div>
				</center>
			</div>
		</div>
		<div class="col-xs-5" id="police_image">
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
		fetchVisitorCount();
		tagFocus();
		setInterval(tagFocus, 1000*60);
		setInterval(fetchVisitorCount, 1000*60*5);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var dilarang_masuk = new Audio('{{ url("sounds/dilarang_masuk.mp3") }}');
	var silahkan_masuk = new Audio('{{ url("sounds/silahkan_masuk.mp3") }}');
	var count = 0;

	$('#employeeTag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#employeeTag").val().length == 10){
				scanEmployeeTag($("#employeeTag").val());
				return false;
			}
			else{
				$('#employeeTag').val("");
				$('#employeeTag').focus();
				openErrorGritter('Error!', 'Employee Tag Invalid.');
				audio_error.play();
			}
		}
	});

	function tagFocus(){
		$('#employeeTag').focus();
	}

	function scanEmployeeTag(tag){
		var data = {
			tag:tag
		};
		$.post('{{ url("input/general/omi_visitor") }}', data, function(result, status, xhr){
			if(result.status){
				$('#employeeTag').val("");
				$('#employeeTag').focus();

				if(result.message == 'silahkan_masuk'){
					openSuccessGritter('Success!', 'Silahkan Masuk');
					silahkan_masuk.play();
				}
				if(result.message == 'berhasil_keluar'){
					openSuccessGritter('Success');
					audio_ok.play();
				}
				if(result.message == 'dilarang_masuk'){
					openErrorGritter('Error', 'Dilarang Masuk');
					dilarang_masuk.play();
				}

				fetchVisitorCount();
			}
			else{
				$('#employeeTag').val("");
				$('#employeeTag').focus();
				openErrorGritter('Error!', result.message);
				audio_error.play();		
			}
		});
	}

	function fetchVisitorCount(){
		$.get('{{ url("fetch/general/omi_visitor") }}', function(result, status, xhr){
			if(result.status){
				var visitor_count = "";
				var count_detail = "";
				var visitor_appeal = "";
				var police_image = "";
				$('#visitor_count').html("");
				$('#count_detail').html("");
				$('#visitor_appeal').html("");
				$('#police_image').html("");

				count = result.visitors.length;

				if(count >= 14){
					visitor_count = '<span style="background-color: #ff1744;">&nbsp;'+count+'&nbsp;</span>';
					count_detail = '<span style="color: #ff1744; font-weight:bold;"><i class="fa fa-arrow-up"></i> Jumlah Pengunjung Melebihi Ketentuan <i class="fa fa-arrow-up"></i></span>';
					visitor_appeal = '<span style="color: #ff1744; font-weight:bold;" class="clapclap">DILARANG MASUK</span>';
					police_image = '<center><img style="height: 700px;" src="{{ url('images/omi/police01_b_09.png') }}"></center>';
				}
				else{
					visitor_count = '<span style="background-color: #7cb342;">&nbsp;'+count+'&nbsp;</span>';
					count_detail = '<span style="color:#7cb342; font-weight:bold;"><i class="fa fa-arrow-down"></i> Jumlah Pengunjung Dibawah Ketentuan <i class="fa fa-arrow-down"></i></span>';
					visitor_appeal = '<span style="color: #7cb342; font-weight:bold;">BOLEH MASUK</span>';
					police_image = '<center><img style="height: 700px;" src="{{ url('images/omi/police01_b_10.png') }}"></center>';
				}

				$('#visitor_count').append(visitor_count);
				$('#count_detail').append(count_detail);
				$('#visitor_appeal').append(visitor_appeal);
				$('#police_image').append(police_image);
			}
			else{
				openErrorGritter('Error', 'Attempt to retrieve data failed.');
			}
		});
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
</script>
@endsection