@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.gambar {
		width: 200px;
		height: 550px;
		background-color: white;
		border-radius: 15px;
		margin-left: 30px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}

	.gambar img {
		max-width:87%; 
		height:auto;
		display: block;
		margin-left: auto;
		margin-right: auto;
		margin-top: 100px
		/*vertical-align:middle;*/
	}

	.content-wrapper {
		padding: 0px !important;
	}

	.text_stat {
		color: white;
		text-align: center;
		font-weight: bold;
		font-size: 30px;
		vertical-align: top;
	}
</style>
@endsection

@section('content')
<section class="content" style="padding-top: 0px">
	<div class="row">
		<div class="col-xs-12" style="padding: 0 0 15px 15px;">
			<?php $male = 1; $female = 1;  for ($i=5; $i >= 0; $i--) { 
				if ($i == 1) { ?>
					<div class="gambar" style="background-color: transparent; border: 0px"></div>	
				<?php } ?>

				<div class="gambar" id="gambar_<?php echo $i?>" style="background: #3c3c3c">
					<?php 
					if ($i > 1) {
						echo '<img src="'.url("images/Ladies.png").'" id="female_'.$female.'">';
						echo "<p class='text_stat' id='text_".$i."'>NO DATA <br> データ不明</p>";
						$female += 1;
					} else {
						echo '<img src="'.url("images/Gents.png").'" id="male_'.$male.'">';
						echo "<p class='text_stat' id='text_".$i."'>NO DATA <br> データ不明</p>";
						$male += 1;
					}
					?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>

</div>

@stop

@section('scripts')
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

	jQuery(document).ready(function() {
		getToiletStatus();
		setInterval(getToiletStatus, 5000);
	});

	function getToiletStatus() {
		loc = '{{ Request::segment(4) }}';
		data = {
			location : loc
		}
		$.get('{{ url("fetch/room/toilet") }}', data,  function(result, status, xhr){
			$.each(result.datas, function(index, value){
				if (value == 1) {
					$("#gambar_"+index).css('background','rgb(240, 41, 61)');
					$("#text_"+index).html("OCCUPIED<br>使用中");
				} else if (value == 0) {
					$("#gambar_"+index).css('background','rgb(50,205,50)');
					$("#text_"+index).html("VACANT<br>使用可");
				}
			})
		})
	}
</script>
@stop