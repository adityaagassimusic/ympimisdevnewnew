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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchPoint();
		setInterval(slide, 1000*60*2);
		$(document).bind("contextmenu",function(e){
			return false;
		});
	});

	var curr = 1;
	var count = 1;

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

	function fetchPoint(){
		$('.content').html('');

		var image_data = '';

		image_data += '<div onclick="next()" class="row" name="'+count+'">';
		image_data += '<img src="{{ asset('images/aeo_landscape.png') }}" style="width: 100vw;">';
		// image_data += '<span class="dot" style="left: 20px; top 20px; font-weight: bold; font-size: 1vw;">'+count+'</span>';
		image_data += '</div>';
		count += 1;
		image_data += '<div onclick="next()" class="row" name="'+count+'">';
		image_data += '<img src="{{ asset('images/aeo_slide_1.png') }}" style="width: 100vw;">';
		// image_data += '<span class="dot" style="left: 20px; top 20px; font-weight: bold; font-size: 1vw;">'+count+'</span>';
		image_data += '</div>';
		count += 1;
		image_data += '<div onclick="next()" class="row" name="'+count+'">';
		image_data += '<img src="{{ asset('images/aeo_slide_2.png') }}" style="width: 100vw;">';
		// image_data += '<span class="dot" style="left: 20px; top 20px; font-weight: bold; font-size: 1vw;">'+count+'</span>';
		image_data += '</div>';
		count += 1;
		$('.content').append(image_data);
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