@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content-wrapper {
		background-color: white !important;
		padding-top: 0 !important;
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
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="default_language" value="{{ $default_language }}">
{{-- <div id="pic"></div> --}}
<section class="content" style="padding-top: 10px;" id="coba">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<input type="hidden" id="location" value="{{ $location }}">
	<div id="container" style="padding-top: 50px;"></div>	
</section>
@endsection
@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').addClass('fixed');
		fetchPoint();
		// slide();
		setInterval(slide, 1000*60*15);
	});

	function slide(){

		var dt = new Date();
		var hour = dt.getHours();

		if(hour >= 8 && hour < 15){
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

			if($("[name='inp_"+curr+"']").val() > 0.5){
				window.scrollTo(0, document.body.scrollHeight);	
			}
			if($("[name='inp_"+curr+"']").val() <= 0.5){
				window.scrollTo(0, 0);
			}

			console.log('tampil = '+curr+'; jml = '+count);
		}
	}

	var curr = 1;
	var count = 1;

	$(function() {
		$(document).keydown(function(e) {
			switch(e.which) {
				case 40:
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

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;


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

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;


				case 38:

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

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
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

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;

			}
		});
	});



	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location:location
		}
		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('#container').html("");
				var image_data = "";
				var h = "";

				if(window.innerHeight > window.innerWidth){
					h = "100%";
				}
				else{
					h = "150vh";
				}

				$.each(result.pointing_calls, function(key, value){
					image_data += '<div class="row" id="'+value.point_title+'" name="'+count+'" tabindex="1" style="height: 100%;">';
					image_data += '<input type="hidden" name="inp_'+count+'" value="'+value.point_no/value.point_max+'">';
					if(value.point_title == 'slogan_mutu'){
						image_data += '<div style="font-weight:bold; font-size: 30px; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';
						image_data += '<center><img src="{{ asset('images/pointing_calls/national') }}/'+value.point_title+'_'+value.point_no+'.gif" style="max-width: 95%;"></center>';
						count += 1;						
					}
					else if(value.point_title == 'diamond'){
						image_data += '<div style="font-weight:bold; font-size: 30px; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls/national') }}/'+value.point_title+'_'+value.point_no+'.gif" style="width: 100%;"></center>';
						count += 1;	
					}
					else if(value.point_title == 'k3'){
						image_data += '<div style="font-weight:bold; font-size: 30px; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls/national') }}/'+value.point_title+'_'+value.point_no+'.gif" style="width: 100%;"></center>';
						count += 1;	
					}

					else if(value.point_title == 'janji_safety'){
						if('{{Auth::user()->username}}' == 'Display' || '{{Auth::user()->username}}' == 'display'){
							
						}
						else{
							image_data += '<div id="'+value.point_title+'" name="'+count+'" tabindex="1" style="padding-left: 10px; padding-right: 10px; min-height:100vh;">';
							image_data += '<center><span style="font-weight: bold; font-size: 2vw;">{{ date('Y') }}年 {{ date('m') }}月 Catatan Record Penerapan 『Janji Safety Riding』</span></center><br>';
							image_data += '<span style="font-weight: bold; font-size: 1.3vw;">① Perkirakan waktu untuk tiba dengan selamat di tempat tujuan. (Mari berangkat kerja lebih awal.)</span><br>';
							image_data += '<span style="font-weight: bold; font-size: 1.3vw;">② Marilah patuhi aturan berlalu lintas demi orang-orang tercinta kita.</span><br><br>';
							if(result.safety_ridings.length > 0){
								image_data += '<span style="font-weight: bold; font-size: 1.5vw;">'+result.department+'</span>';
							}
							else{		
								image_data += '<span style="font-weight: bold; font-size: 1.5vw; background-color:red;">Belum input "Janji Safety Riding" periode ini</span><br>';
							}
							image_data += '<span style="font-weight: bold; font-size: 1.5vw;"></span><br>';
							image_data += '<table class="table table-bordered">';
							image_data += '<thead>';
							image_data += '<tr style="background-color: rgba(126,86,134,.7); font-size:1.3vw;">';
							image_data += '<th style="width: 1%; text-align: center;">#</th>';
							image_data += '<th style="width: 10%;">Nama</th>';
							image_data += '<th style="width: 30%;">Janji Safety Riding</th>';
							image_data += '</tr>';
							image_data += '</thead>';
							image_data += '<tbody id="safetyTableBody">';
							image_data += '</tbody>';
							image_data += '</table>';
							image_data += '</div>';	
							count += 1;						
						}
					}
					else if(value.point_no > 0){
						image_data += '<div style="font-weight:bold; font-size: 2.5vw; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls/national') }}/'+value.point_title+'_'+value.point_no+'.gif" style="height: '+h+'; max-width: 100%;"></center>';
						count += 1;
					}
					else{
						image_data += '<div style="font-weight:bold; font-size: 2.5vw; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls') }}/'+value.point_title+'_id.jpg" style="height: '+h+'; max-width: 100%;"></center>';
						count += 1;
					}
					image_data += '</div>';
				});

if('{{Auth::user()->username}}' == 'Display' || '{{Auth::user()->username}}' == 'display'){
	// $('#pic').append('<div style="font-weight:bold; font-size: 2.5vw; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; top:50px; text-align:center;"><span style="color: red;">'+result.pic.employee_name+' - '+result.pic.department_shortname+'</span><br>Next PIC: '+result.pic_next.employee_name+' - '+result.pic_next.department_shortname+'</div>');
	$('#container').append(image_data);
}
else{
	var safety_data = "";
	var safety_count = 1;
	$('#safetyTableBody').html("");

	$.each(result.safety_ridings, function(key, value){
		safety_data += '<tr>';
		safety_data += '<td style="font-size: 1.3vw; text-align: center;">'+safety_count+'</td>';
		safety_data += '<td style="font-size: 1.3vw;">'+value.employee_name+'</td>';
		safety_data += '<td style="font-size: 1.3vw;">'+value.safety_riding.toUpperCase()+'</td>';
		safety_data += '</tr>';	
		safety_count += 1;
	});
	$('#container').append(image_data);
	$('#safetyTableBody').append(safety_data);
}

for(var i = 2; i <= count; i++){
	$("[name='"+i+"']").hide();	
}


$('.row').click(function() {
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

	if($("[name='inp_"+curr+"']").val() > 0.5){
		window.scrollTo(0, document.body.scrollHeight);	
	}
	if($("[name='inp_"+curr+"']").val() <= 0.5){
		window.scrollTo(0, 0);
	}
	return false;
}); 


}
else{
	alert('Unidentified ERROR!')
}
});


}

</script>
@endsection