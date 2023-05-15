@extends('layouts.notification')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.eo_number {
		font-size: 4vw;
		font-weight: bold;
		cursor: pointer;
		color: #3c8dbc;
	}

	.status {
		font-size: 3vw;
		font-weight: bold;
	}

	.message {
		font-size: 2vw;
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="row">
		<div class="error" style="text-align: center;">
			<p>
				<h2>
					{{-- Upload PO Berhasil --}}
					@if($code == 1)
					<p class="eo_number" onclick="detailExtraOrder('{{ $eo_number }}')">{{ $eo_number }}</p>
					<p class="message">PO has been submitted suceesfully</p>
					<p class="status"><i style="color : #0bb13d" class="fa fa-check-square"></i>&nbsp;&nbsp;SUCCESS!</p>


					{{-- PO Sudah Pernah Di Upload --}}
					@elseif($code == 2)
					<p class="eo_number" onclick="detailExtraOrder('{{ $eo_number }}')">{{ $eo_number }}</p>
					<p class="status"><i style="color : #dfbb53;" class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;PO Already submitted !</p>
					<p class="message">Contact YMPI Production Control to Change PO</p>
					@endif

				</h2>
			</p>
		</div>
	</div>
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

	});

	function detailExtraOrder(eo_number) {
		window.open('{{ url('index/extra_order/detail') }}' + '/' + eo_number, '_self');
	}


</script>
@endsection