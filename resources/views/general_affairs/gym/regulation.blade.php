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
		$(document).bind("contextmenu",function(e){
			return false;
		});
		setInterval(changeTab,20000);
	});

	function fetchPoint(){
		$('.content').html('');

		var image_data = '';

		for(var i = 1; i < 9;i++){
			var url = "{{ asset('data_file/gym_regulation/regulation_') }}"+i+'.jpg';
			image_data += '<div class="row" style="text-align:center;" id="regulation_'+i+'">';
			image_data += '<img src="'+url+'" style="width: 80%">';
			image_data += '</div>';
		}
		index = 1;
		$('.content').append(image_data);
	}

	var index = 1;

	function changeTab() {
		if (index == 8) {
			index = 1;
		}
		location.href='#regulation_'+index;
		index++;
	}


</script>
@endsection