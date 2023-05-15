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
<section class="content" style="padding-top: 0;padding-bottom: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
</section>


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
		var data = {
			code: '{{$code}}'
		}

		$.get('{{ url("fetch/general/xibo/display") }}', data, function(result, status, xhr){
			if(result.status){
				$('.content').html('');

				var image_data = '';

				image_data += '<div onclick="next()" class="row" id="'+result.xibo[0].category+'" name="'+count+'">';

				if (result.xibo[0].category == 'template-1') {
					image_data += '<marquee style="position:absolute;text-align:center;left:5vh;top:77.5vh;right:5vh;" direction="up" height="260vh">';
					var font_size_title = '130px';
				}else{
					image_data += '<marquee style="position:absolute;text-align:center;left:5vh;top:57vh;right:5vh;" direction="up" height="530vh">';
					var font_size_title = '70px';
				}
				image_data += '<span style="color:'+result.xibo[0].color_title+';font-size:'+font_size_title+';font-weight:bold;">'+result.xibo[0].title+'</span>';

				var additional = null;

				$.each(result.xibo, function(key, value){
					if (value.additional != null) {
						additional = value.additional.split(',');
					}
					if (value.sub_title != null) {
						image_data += '<br>';
						image_data += '<br>';
						image_data += '<span style="color:'+value.color_sub_title+';font-size:60px;font-weight:bold;text-decoration:underline;">'+value.sub_title+'</span><br>';
					}
					if (value.content != null) {
						image_data += '<span style="color:'+value.color_content+';font-size:'+value.content_font_size+'px;font-weight:bold;">';
						if (value.content.match(/_/gi)) {
							var contents = value.content.split('_');
							for(var i = 0; i < contents.length;i++){
								image_data += contents[i]+'<br>';
							}
						}else{
							image_data += value.content+'<br>';
						}
						image_data += '</span><br>';
					}
				});
				image_data += '</marquee>';
				image_data += '<div style="position:absolute;text-align:center;left:20vh;top:91vh;right:20vh;">';
				image_data += '<span style="color:white;font-size:60px;font-weight:bold;" id="time">10:00</span><br>';
				image_data += '<span style="color:white;font-size:40px;font-weight:bold;" id="date">14-Sep-2022</span><br>';
				image_data += '</div>';
				image_data += '<img src="{{ asset('images/xibo') }}/'+result.xibo[0].category+'.png" style="width: 100%;">';
				image_data += '</div>';
				count += 1;

				if (additional != null) {
					for(var i = 0; i < additional.length;i++){
						image_data += '<div onclick="next()" class="row" id="'+result.xibo[0].category+'" name="'+count+'">';
						image_data += '<img src="{{ asset('images/xibo') }}/'+additional[i]+'" style="width: 100%;">';
						image_data += '</div>';
						count += 1;
					}
				}

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