<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body >
	<style type="text/css">
		@page  
		{ 
			size: auto;   /* auto is the initial value */ 

			/* this affects the margin in the printer settings */ 
			margin: 0mm 0mm 0mm 0mm;
		} 

		table {
			font-family: 'calibri';
			border-collapse: collapse;
			padding: 0px;
			border: 2px solid black;
		}

		table, th, td {
			padding: 0px;
		}

		p {
			padding: 0px;
			margin: 0px;
		}

		.gmc {
			font-size: 12pt;
		}

		.desc {
			font-size: 10pt;
		}

		.qr {
			font-size: 8pt;
		}

		#kd_number {
			font-size: 11pt;
			font-weight: normal;
			padding-bottom: 3pt;			
		}

		#barcode {
			padding-top: 5pt;
		}

		#dept {
			font-size: 10pt;
		}

	</style>
	<?php 
	include public_path(). "/qr_generator/qrlib.php"; 

	QRcode::png($data_asset->fixed_asset_id, public_path().'/fa-qr.png');
	?>

	<table style="width: 330px; margin-left: 20px">
		<tr height="">
			<th rowspan="4" style=" width: 1%;">
				<img src="{{ url("fa-qr.png")}}" style="width: 65px; height: 65px; object-fit: cover;"> 
				<br>
				<!-- <p id="ids" style="font-size: 8pt">{{ $dept->department_shortname }}</p> -->
			</th>
		</tr>
		<tr>
			<th colspan="3" style="padding-top: 0px; padding-bottom: 0px;" width="100px" class="desc" id="fixed_asset_name">{{ $data_asset->fixed_asset_name }}</th>
		</tr>
		<tr>
			<th style="width: 20%; font-size: 11pt">{{ $data_asset->fixed_asset_id }}</th>
			<th style="padding-top: 0px; padding-bottom: 0px;; font-size: 11pt" width="100px">&nbsp;</th>
			<th style="width: 20%; font-size: 11pt">{{ $data_asset->req_date }}</th>
		</tr>
		<!-- <tr>
			<th colspan="3" style="font-size: 11pt">{{ $data_asset->section }}</th>		
		</tr> -->
		<tr>
			<th colspan="3" style="padding-top: 0px; padding-bottom: 0px; font-size: 12px; border-top: 2px solid black" width="80px">ASSET PT YAMAHA MUSICAL PRODUCTS INDONESIA</th>
		</tr>
	</table>



</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		window.print(); 
		// initialize();
		// defineCustomPaperSize();

		// setTimeout(function() {
		// 	printWindow(window, 'Label Kecil');
		// }, 5000)

	});


</script>