@extends('layouts.display_2')

@section('stylesheets')
<style type="text/css">
	content, html, body {
		height: 100%;
	}
	body {
		margin: 0px;
		background-color: #333333;
	}
	.navbare {
		overflow: hidden;
		position: fixed;
		top: 0;
		width: 100%;
		background-color: #605ca8;
		z-index: 100;
		padding: 5px 0 5px 0;
	}

	#schedule {
		overflow-y: scroll;
		height: 750px;
		width: 1700px;
		/*margin-top: -60px;*/
		/*zoom: 2;
		-moz-transform: scale(2);
		-moz-transform-origin: 0 0;*/
		position:absolute; 
		/*clip:rect(0px,1110px,800px,50px);*/
		top:0px; left:-280px;
	}
	
</style>
@endsection

@section('content')
<div class="navbare">
	<center><span style="color: white; font-size: 4vw; font-weight: bold;">TRAINING ROOM 2</span></center>
</div>
<center>
	<iframe src="https://outlook.office365.com/calendar/view/day/" width="100%" id="schedule"></iframe>
	<!-- <object type="text/html" data="https://outlook.office365.com/calendar/view/day/" width="800px" height="600px" style="overflow:auto;border:5px ridge blue"> -->
	</object>
</center>
@endsection

<script type="text/javascript">
	window.setInterval("reloadIFrame();", 1800000);

	function reloadIFrame() {
		document.getElementById("schedule").src="https://outlook.office365.com/calendar/view/day/";	}

	</script>