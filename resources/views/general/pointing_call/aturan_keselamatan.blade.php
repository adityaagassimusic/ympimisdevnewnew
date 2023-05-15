@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.dot {
		height: 5%;
		width: 5%;
		position: absolute;
		z-index: 10;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	.content-wrapper {
		background-color: white !important;
		padding-top: 0px !important;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="default_language" value="{{ $default_language }}">
<section class="content" style="padding-top: 0;padding-bottom: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
</section>

<div class="modal fade" id="modalImage">
	<div class="modal-dialog" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">x</span>
				</button>
			</div>
			<div class="modal-body" id="modalImageBody">

			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url('js/jquery.marquee.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchPoint();
		$('.marquee').marquee({
            duration: 4000,
            gap: 1,
            delayBeforeStart: 0,
            direction: 'up',
            duplicated: true
        });
		setInterval(slide, 1000*60*2);
		$(document).bind("contextmenu",function(e){
			return false;
		});
	});
	var curr = 1;
	var count = 1;
	var timeref;

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function waktu() {
		var time = new Date();
		document.getElementById("time").innerHTML = addZero(time.getHours())+':'+addZero(time.getMinutes());
		timeref = addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds());
	}

	function slide(){

		var dt = new Date();
		var hour = dt.getHours();

		if(hour >= 7 && hour < 16){
			var c;

			if(curr == count-1){
				curr = 1;
				c = 1;
			}else{
				c = curr++;
			}

			for (var i = 1; i <= count; i++) {
				$("[name='"+i+"']").hide();	
			}

			$("[name='"+curr+"']").show();

		}
	}

	function clearConfirmation(){
		location.reload(true);
	}

	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location: location
		}

		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('.content').html('');

				var image_data = '';

				$.each(result.pointing_calls, function(key, value){
						image_data += '<div onclick="next()" class="row" id="'+value.point_title+'" name="'+count+'">';
						if (value.point_title == 'template') {
							image_data += '<marquee style="position:absolute;text-align:center;left:5vh;top:57vh;right:5vh;" direction="up" height="530vh">';
							image_data += '<span style="color:yellow;font-size:70px;font-weight:bold;">PENINJAUAN LAPANGAN AEO</span>';
							image_data += '<br>';
							image_data += '<br>';
							image_data += '<span style="color:yellow;font-size:60px;font-weight:bold;text-decoration:underline;">Bea Cukai Pusat : </span><br>';
							image_data += '<span style="color:white;font-size:60px;font-weight:bold;">Mr. Yuwono Sutiasmaji<br>Mr. Eko Agus Budiyono<br>Mr. Nofianzah Kurniyawan</span><br>';
							image_data += '<br>';
							image_data += '<br>';
							image_data += '<span style="color:yellow;font-size:60px;font-weight:bold;text-decoration:underline;">CM AEO BC Pasuruan : </span><br>';
							image_data += '<br>';
							image_data += '<span style="color:white;font-size:60px;font-weight:bold;padding-top:60px;">Mr. Joko Wurianto</span><br>';
							image_data += '<br>';
							image_data += '<br>';
							image_data += '<span style="color:yellow;font-size:60px;font-weight:bold;text-decoration:underline;">CM AEO BC Juanda : </span><br>';
							image_data += '<span style="color:white;font-size:60px;font-weight:bold;">Mr. Kupang Luqman</span><br>';
							image_data += '<span style="color:white;font-size:60px;font-weight:bold;">Mr. Joko Wurianto</span><br>';
							image_data += '<br>';
							image_data += '<br>';
							image_data += '<span style="color:yellow;font-size:60px;font-weight:bold;text-decoration:underline;">CM AEO BC Tj. Perak : </span><br>';
							image_data += '<span style="color:white;font-size:60px;font-weight:bold;">Mr. Satria Yudhatama</span><br>';
							image_data += '</marquee>';

							image_data += '<div style="position:absolute;text-align:center;left:20vh;top:91vh;right:20vh;">';
							image_data += '<span style="color:white;font-size:60px;font-weight:bold;" id="time">10:00</span><br>';
							image_data += '<span style="color:white;font-size:40px;font-weight:bold;" id="date">14-Sep-2022</span><br>';
							image_data += '</div>';
						}
						image_data += '<img src="{{ asset('images/aturan_keselamatan') }}/'+value.point_title+'_'+value.point_no+'.png" style="width: 100%;">';
						// image_data += '<span class="dot" style="left: 20px; top 20px; font-weight: bold; font-size: 1vw;">'+count+'</span>';
						image_data += '</div>';

					count += 1;
				});
				$('.content').append(image_data);

				var myvar = setInterval(waktu,1000);

				$('#date').html('{{date("d-M-Y", strtotime(date("Y-m-d")))}}');

}
else{
	$('#loading').show();
}
});


}

$(function() {
	$(document).keydown(function(e) {
		switch(e.which) {
			case 39:
			var c;

			if(curr == count-1){
				curr = 1;
				c = 1;
			}else{
				c = curr++;
			}

			for (var i = 1; i <= count; i++) {
				$("[name='"+i+"']").hide();	
			}

			$("[name='"+curr+"']").show();

		
			break;



			case 37:
			if(curr == 1){
				curr = count;
			}

			if(curr <= 0){
				curr = 1;
			}
			
			var c = curr--;

			for (var i = 1; i <= count; i++) {
				$("[name='"+i+"']").hide();	
			}

			$("[name='"+curr+"']").show();


			curr = curr--;

			break;

		}
	});
});

function next() {
	var c;

	if(curr == count-1){
		curr = 1;
		c = 1;
	}else{
		c = curr++;
	}

	for (var i = 1; i <= count; i++) {
		$("[name='"+i+"']").hide();	
	}

	$("[name='"+curr+"']").show();
}

</script>
@endsection