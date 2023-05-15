@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.print.min.css")}}" media="print">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
	tfoot>tr>td{
		text-align:center;
	}
	td:hover {
		overflow: visible;
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
	table.table-bordered > tfoot > tr > td{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	.blink_text {

		animation:1.2s blinker linear infinite;
		-webkit-animation:1.2s blinker linear infinite;
		-moz-animation:1.2s blinker linear infinite;

		color: yellow;
	}

	@-moz-keyframes blinker {  
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}

	@-webkit-keyframes blinker {
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}

	@keyframes blinker {  
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}
	#loading, #error { display: none; }
	.fc-event {
		font-size: 1vw;
		cursor: pointer;
	}

	.fc-event-time, .fc-event-title {
		padding: 0 1px;
		white-space: nowrap;
	}

	.fc-title {
		white-space: normal;
	}
	.fc-content {
	    cursor: pointer;
	}
	.content{
		padding-top: 0px;
		padding-left: 7px;
		padding-right: 7px;
		padding-bottom: 0px;
	}

	* {box-sizing: border-box}
/*body {font-family: Verdana, sans-serif; margin:0}*/
.mySlides {display: none}
img {vertical-align: middle;}

/* Slideshow container */
.slideshow-container {
  max-width: 1000px;
  position: relative;
  margin: auto;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  background-color: black;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 15px;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

/* Fading animation */
.fade {
  animation-name: fade;
  animation-duration: 1.5s;
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		
	</h1>
</section>
@endsection

@section('content')
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-top: 0px;padding-bottom: 10px">
			<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px;">
				<a href="{{url('index/ga_control/gym')}}" class="btn btn-danger" style="width: 100%">
					<i class="fa fa-arrow-left"></i> Back
				</a>
			</div>
			<div class="col-xs-11" style="padding-left: 5px;padding-right: 0px;">
				<input type="text" class="form-control" style="width: 100%;text-align: center;font-size: 20px;" name="tag" id="tag" placeholder="Scan ID Card">
			</div>
		</div>
		<div class="col-xs-4" style="padding-right: 0px;">
			<div class="box box-primary">
				<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;background-color: white;text-align: center;">
					<span style="font-weight: bold;font-size: 60px;color: black !important;width: 100%" id="waktu"></span>
				</div>
				<div class="box-header" style="padding: 0">
					<center><h4 style="font-weight: bold;">SEDANG DI RUANG GYM</h4></center>
				</div>
				<div class="box-body">
					<table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
		              <thead>
			              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
			                <th style="width:1%;">#</th>
							<th style="width:1%">Name</th>
							<th style="width:1%">Start</th>
							<th style="width:1%">End</th>
							<th style="width:1%">Duration</th>
			              </tr>
		              </thead>
		              <tbody id="body-detail">
		                
		              </tbody>
		            </table>
				</div>
			</div>
		</div>
		<div class="col-xs-8" style="padding-left: 5px;">
			<div class="box box-warning">
				<div class="box-header" style="padding: 0">
					<center><h2 style="font-weight: bold;" id="titles">TUTORIAL</h2></center>
				</div>
				<?php $color = ['#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6', 
					  '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
					  '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A', 
					  '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
					  '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC', 
					  '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
					  '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680', 
					  '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
					  '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3', 
					  '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF']; ?>
				<div class="box-body">
					<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
						<table style="width: 100%">
							<tr>
								<td style="padding: 2px;width: 16%">
									<button class="btn btn-primary" style="font-weight: bold;font-size: 22px;width: 100%;border-color: black;" id="btn_warmup" onclick="getVideo('warmup')">PEMANASAN</button>
								</td>
								<td style="padding: 2px;width: 16%">
									<button class="btn btn-primary" style="font-weight: bold;font-size: 22px;width: 100%;border-color: black;" id="btn_dada" onclick="getVideo('dada')">DADA</button>
								</td>
								<td style="padding: 2px;width: 16%">
									<button class="btn btn-primary" style="font-weight: bold;font-size: 22px;width: 100%;border-color: black;" id="btn_punggung" onclick="getVideo('punggung')">PUNGGUNG</button>
								</td>
								<td style="padding: 2px;width: 16%">
									<button class="btn btn-primary" style="font-weight: bold;font-size: 22px;width: 100%;border-color: black;" id="btn_bahu" onclick="getVideo('bahu')">BAHU</button>
								</td>
								<td style="padding: 2px;width: 16%">
									<button class="btn btn-primary" style="font-weight: bold;font-size: 22px;width: 100%;border-color: black;" id="btn_tangan" onclick="getVideo('tangan')">TANGAN</button>
								</td>
								<td style="padding: 2px;width: 16%">
									<button class="btn btn-primary" style="font-weight: bold;font-size: 22px;width: 100%;border-color: black;" id="btn_kaki" onclick="getVideo('kaki')">KAKI</button>
								</td>
							</tr>
						</table>
					</div>
					<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;" id="video">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">BUAT PENDAFTARAN</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" name="addId" id="addId">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">ID<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input class="form-control" type="text" id="addUser" value="<?php if(ISSET($emp)){
										echo $emp->employee_id;
									} ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Nama<span class="text-red"> :</span></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="addUserName" value="<?php if(ISSET($emp)){
										echo $emp->name;
									} ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kategori<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="addCategory" placeholder="Select Date" readonly="">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tanggal<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="addDate" placeholder="Select Date" readonly="">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Jam<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="addStartTime" readonly="">
								</div>
								<div class="col-sm-4" style="padding-left: 0px;">
									<input type="text" class="form-control" id="addEndTime" readonly="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-xs-6" style="padding-left: 0px;">
						<button class="btn btn-danger pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="$('#modalCreate').modal('hide')">BATAL</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="confirmOrder()">DAFTAR</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEnd" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: red; font-weight: bold; padding: 3px; margin-top: 0; color: white;font-size: 30px;">PERINGATAN</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<input type="hidden" name="id" id="id">
					<div class="col-xs-12" style="text-align: center;">
						<span style="color: red;font-size: 60px;font-weight: bold;" id="name">NAME</span><br>
						<span style="color: red;font-size: 60px;font-weight: bold;">WAKTU HABIS</span>
					</div>
					<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
						<button class="btn btn-danger pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="endSession()">KELUAR GYM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalProgress" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">PROGRESS GYM ANDA BULAN INI</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-12" style="padding-bottom: 5px;padding-left: 0px;padding-right: 0px;">
						<table id="data-log-progress" class="table table-striped table-bordered" style="width: 100%;">
			              <thead>
				              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
				                <th style="width:1%;">#</th>
								<th style="width:1%">Date</th>
								<th style="width:1%">Type</th>
								<th style="width:1%">Nilai</th>
				              </tr>
			              </thead>
			              <tbody id="body-detail-progress">
			                
			              </tbody>
			            </table>
					</div>
					<div class="col-md-12" style="padding-bottom: 5px;margin-bottom: 20px;">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Berat Badan<span class="text-red"> :</span></label>
							<div class="col-sm-9">
								<input class="form-control numpad" type="text" id="add_berat_badan" value="" readonly="" placeholder="Input Berat Badan" style="text-align: center;font-size: 20px;">
								<input type="hidden" name="employee_id" id="employee_id">
								<input type="hidden" name="name" id="name">
							</div>
						</div>
					</div>
					<!-- <div class="col-xs-6" style="padding-left: 0px;">
						<button class="btn btn-danger pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="$('#modalProgress').modal('hide')">BATAL</button>
					</div> -->
					<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="confirmProgress()">CONFIRM</button>
					</div>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("bower_components/moment/moment.js")}}"></script>
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

	var myvar = setInterval(waktu,1000);

	var intervalTime;

	jQuery(document).ready(function() {
		$('#tag').val('');
		$('#tag').focus();
		$('body').toggleClass("sidebar-collapse");
		var date = new Date();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: date
		});

		$('#datefrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#dateto').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#menuDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#menuDateRandom').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		
		$('.selectEditEmployee').select2({
			dropdownParent:$('#modalDetail')
		});

		fetchData();

		// clearTimeout(intervalTime);
		setInterval(fetchData,20000);
		getVideo('warmup');

		setInterval(setTime, 1000);
		$('#titles').html('TUTORIAL PEMANASAN');

		setInterval(changeTab,20000);

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	function diff_minutes(dt2, dt1) 
	 {
	 	var diffs =(dt2.getTime() - dt1.getTime()) / 1000;
		diffs /= 60;
 		return Math.abs(Math.round(diffs));
	  
	 }

	var jam = '';

	function waktu() {
		var time = new Date();
		// document.getElementById("jam").style.fontSize = '30em';
		// document.getElementById("jam").style.marginBottom = '-70px';
		// document.getElementById("jam").innerHTML = addZero(time.getHours())+':'+addZero(time.getMinutes());
		jam = addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds());
		$('#waktu').html(jam);
	}

	function getVideo(param) {
		$('#video').html('');
		var video = '';

		if (param == 'dada') {
			var indexParams = 1;
			var count = 6;
		}else if(param == 'punggung'){
			var indexParams = 2;
			var count = 6;
		}else if(param == 'bahu'){
			var indexParams = 3;
			var count = 6;
		}else if(param == 'tangan'){
			var indexParams = 4;
			var count = 5;
		}else if(param == 'kaki'){
			var indexParams = 5;
			var count = 4;
		}else if(param == 'warmup'){
			var indexParams = 0;
			var count = 5;
		}

		video += '<div class="slideshow-container">';

		for(var i = 1; i <= count;i++){
			var url = "{{asset('data_file/gym/')}}/"+param+'_'+i+'.gif';
			video += '<div class="mySlides">';
			  video += '<div class="numbertext">'+i+' / '+count+'</div>';
			  video += '<img style="width:100%" src="'+url+'">';
			video += '</div>';
		}
		video += '<a class="prev" onclick="plusSlides(-1)">❮</a>';
		video += '<a class="next" onclick="plusSlides(1)">❯</a>';

		video += '</div>';
		video += '<br>';

		video += '<div style="text-align:center">';

		for(var i = 1; i <= count;i++){
			video += '<span class="dot" onclick="currentSlide(\''+i+'\')"></span> ';
		}
		video += '</div>';

		var params = [
		'warmup',
		'dada',
		'punggung',
		'bahu',
		'tangan',
		'kaki',];

		$('#titles').html('TUTORIAL '+$('#btn_'+params[indexParams]).text());

		$('#video').append(video);

		showSlides(slideIndex);
		setInterval( function() { plusSlides(1); }, 10000 );
	}

	var indexParams = 0;

	function changeTab() {
		var param = [
		'warmup',
		'dada',
		'punggung',
		'bahu',
		'tangan',
		'kaki',];

		for(var i = 0; i < 6;i++){
			if (indexParams == 5) {
				indexParams = 0;
			}
			document.getElementById("btn_"+param[indexParams]).click();
			$('#titles').html('TUTORIAL '+$('#btn_'+param[indexParams]).text());
			indexParams++;
		}
	}

	var in_time = [];
	function setTime() {
		for (var i = 0; i < in_time.length; i++) {
			var duration = diff_seconds(new Date(), in_time[i]);
			document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
			document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
			document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
		}
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}

	let slideIndex = 1;

		function plusSlides(n) {
		  showSlides(slideIndex += n);
		}

		function currentSlide(n) {
		  showSlides(slideIndex = n);
		}

		function showSlides(n) {
		  let i;
		  let slides = document.getElementsByClassName("mySlides");
		  let dots = document.getElementsByClassName("dot");
		  if (n > slides.length) {slideIndex = 1}    
		  if (n < 1) {slideIndex = slides.length}
		  for (i = 0; i < slides.length; i++) {
		    slides[i].style.display = "none";  
		  }
		  for (i = 0; i < dots.length; i++) {
		    dots[i].className = dots[i].className.replace(" active", "");
		  }
		  slides[slideIndex-1].style.display = "block";  
		  dots[slideIndex-1].className += " active";
		}

	function fetchData(){
		$.get('{{ url("fetch/ga_control/gym/attendance") }}', function(result, status, xhr){
			if(result.status){
				$('#loading').hide();

				$('#body-detail').html('');
				var tableData = '';
				var index = 1;
				in_time = [];
				for(var i = 0; i < result.gym.length;i++){
					var dt1 = new Date(result.datetime);
					var dt2 = new Date('{{date("Y-m-d")}} '+result.gym[i].end_time);
					var diff_time = diff_minutes(dt1, dt2);
					if (result.datetime > '{{date("Y-m-d")}} '+result.gym[i].end_time) {
						diff_time = parseInt(-diff_time);
					}
					if (jam >= result.gym[i].end_time) {
						// audio_error.play();
						// $('#modalEnd').modal('show');
						// $('#id').val(result.gym[i].id);
						// $('#name').html(result.gym[i].name.toUpperCase());
					}
					var color = '#a3ffd0';
					if (result.gym[i].schedule_id == 0) {
						var color = '#a3ffd0';
					}else{
						if (diff_time <=60 && diff_time >= 30) {
							var color = '#a3ffd0';
						}else if(diff_time < 30 && diff_time >= 15){
							var color = '#fff4a3';
						}else if(diff_time < 15 && diff_time > 0){
							var color = '#ffc7c7';
						}else if(diff_time <= 0){
							sendWhatsapp(result.gym[i].id);
							var color = '#ffc7c7';
						}
					}
					tableData += '<tr>';
					tableData += '<td style="background-color: '+color+';text-align:center;">'+index+'</td>';
					tableData += '<td style="background-color: '+color+';text-align:left;padding-left:7px; !important">'+result.gym[i].name+'</td>';
					tableData += '<td style="background-color: '+color+';text-align:right;padding-right:7px; !important">'+(result.gym[i].actual_start_time || result.gym[i].start_time.split(':')[0]+':'+result.gym[i].start_time.split(':')[1])+'</td>';
					tableData += '<td style="background-color: '+color+';text-align:right;padding-right:7px; !important">'+result.gym[i].end_time.split(':')[0]+':'+result.gym[i].end_time.split(':')[1]+'</td>';
					var tanggal_fix = result.gym[i].actual_start_time.replace(/-/g,'/');
					// var started_at = new Date(tanggal_fix);
					in_time.push(new Date(tanggal_fix));
					// countUpFromTime(started_at,i);
					// setTimeout(function(){ countUpFromTime(started_at,i); }, 1000)
					tableData += '<td style="background-color: '+color+';text-align:center;font-weigt:bold;">';
					tableData += '<label id="hours'+ i +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[i]) / 3600)) +'</label>:';
					tableData += '<label id="minutes'+ i +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[i]) % 3600) / 60)) +'</label>:';
					tableData += '<label id="seconds'+ i +'">'+ pad(diff_seconds(new Date(), in_time[i]) % 60) +'</label>';
					// tableData += '<span id="minutes_'+i+'">00</span>:<span id="seconds_'+i+'">00</span>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}

				$('#body-detail').append(tableData);
			}
			else{
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			var data = {
				tag : $("#tag").val(),
				id:''
			}

			$.get('{{ url("scan/ga_control/gym/attendance") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fetchData();
					$('#loading').hide();
					$('#tag').val('');
					$('#tag').focus();
					$('#body-detail-progress').html('');
					var bodyProgress = '';

					var index =1;

					if (result.gym_progress.length > 0) {
						for(var i = 0; i < result.gym_progress.length;i++){
							bodyProgress += '<tr>';
							bodyProgress += '<td style="background-color: #fff;text-align:center;">'+index+'</td>';
							bodyProgress += '<td style="background-color: #fff;text-align:center;">'+result.gym_progress[i].date+'</td>';
							bodyProgress += '<td style="background-color: #fff;text-align:center;">'+result.gym_progress[i].type+'</td>';
							bodyProgress += '<td style="background-color: #fff;text-align:center;">'+result.gym_progress[i].result_check+'</td>';
							bodyProgress += '</tr>';
							index++;
						}
					}
					$('#body-detail-progress').append(bodyProgress);
					$('#add_berat_badan').val('');
					$('#employee_id').val(result.employee_id);
					$('#name').val(result.name);
					$('#modalProgress').modal('show');
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag').removeAttr('disabled');
					$('#tag').val('');
				}
			});
		}
	});

	function confirmProgress() {
		$('#loading').show();
		var data = {
			employee_id:$("#employee_id").val(),
			name:$("#name").val(),
			berat_badan:parseFloat($("#add_berat_badan").val()),
		}

		$.post('{{ url("input/ga_control/gym/progress") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!','Sukses Input Progress Anda Hari Ini');
				$('#loading').hide();
				$('#modalProgress').modal('hide');
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function sendWhatsapp(id) {
		var data = {
			id:id,
			status:'Time Out'
		}

		$.get('{{ url("fetch/ga_control/gym/send_whatsapp") }}', data,function(result, status, xhr){
			if(result.status){
				fetchData();
			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function endSession() {
		var data = {
			id:$('#id').val(),
			tag:''
		}

		$.get('{{ url("scan/ga_control/gym/attendance") }}', data,function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Success Out GYM');
				$("#modalEnd").modal('hide');
				fetchData();
			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	// function countUpFromTime(countFrom,id) {
	//   countFrom = new Date(countFrom).getTime();
	//   var now = new Date(),
	//       countFrom = new Date(countFrom),
	//       timeDifference = (now - countFrom);
	    
	//   var secondsInADay = 60 * 60 * 1000 * 24,
	//       secondsInAHour = 60 * 60 * 1000;
	    
	//   days = Math.floor(timeDifference / (secondsInADay) * 1);
	//   years = Math.floor(days / 365);
	//   if (years > 1){
	//   	days = days - (years * 365) 
	//   }
	//   hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
	//   mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
	//   secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

	//   $('#seconds_'+id).html(addZero(secs));
	//   $('#minutes_'+id).html(addZero(mins));

	//   // clearTimeout(intervalTime);
	//   // intervalTime = ;
	// }

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function formatDate(date) {
		var d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [year, month, day].join('-');
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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