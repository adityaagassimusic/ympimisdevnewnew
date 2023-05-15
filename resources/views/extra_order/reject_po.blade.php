@extends('layouts.notification')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.eo_number {
		font-size: 3vw;
		font-weight: bold;
	}

	.message {
		font-size: 1.5vw;
	}

</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		Reject & Comment
		<small><span class="text-purple">却下・コメント</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="row">
		<form role="form" method="post" id="formNote" action="{{url('input/extra_order/po_reject')}}">
			<div class="col-xs-12 " style="text-align: center;" id="show">
				<h1 class="eo_number"><i style="color : #59a1b5;" class="fa fa-file-text-o"></i>&nbsp;&nbsp;{{ $data['extra_order']['eo_number'] }}</h1>
				<p>
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<input type="hidden" value="{{$data['extra_order']['eo_number']}}" name="eo_number"/>
					
					<div class="col-xs-12">
						<h2 class="message">Give Reason to Buyer :</h2>
					</div>

					<div class="col-xs-10 col-xs-offset-1">
						<textarea class="form-control" id="message" name="message"></textarea>
					</div>
					<div class="col-xs-12">
						<br>
						<button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
						<a class="btn btn-danger btn-lg" type="button" onclick="reset();">Clear Message</a>
					</div>
				</p>
			</div>
		</form>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

	});


	$("#formNote").submit(function(){
		$("#loading").show();
		this.submit();
	});

	function reset(){
		$("#message").html(CKEDITOR.instances.message.setData(""));
	}

	CKEDITOR.replace('message' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
		height: '250px'
	});
	
</script>
@endsection