<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />	
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">


	<style type="text/css">
		.box {
			width: 24%; 
			display: inline-block;
		}
		img {
			margin: 0px !important;
		}

		/*@page { margin: 20px 20px 10px 20px; }*/

		td {
		  overflow: hidden;
		}

		.cropped {
			width: 90px;
			height: 90px;
			vertical-align: middle;
			text-align: center;
			background-position: center center;
			background-repeat: no-repeat;
		}

		@page { margin: 10px 10px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
		
	</style>
</head>
<body>
	<?php
	include public_path(). "/qr_generator/qrlib.php";
	QRcode::png($data[0]->slip, public_path().'/data_file/wwt/qr_code/'.$data[0]->slip.'.png');
	?>
	<table style="width: 250px;  margin-left: auto; margin-right: auto;text-align: left;">
			<tr>
				<td style="width:250px;text-align: center; font-size: 16px; color: white;background-color: black;border: 1px solid black;" colspan="4">Limbah : {{ $data[0]->waste_category }}</td>
			</tr>	
			<tr>
				<td style="text-align: center;border: 1px solid black; background-color: yellow;" colspan="2">
					@if($data[0]->waste_category == 'Lubricant Oil' || $data[0]->waste_category == 'Painting Liquid Laste' || $data[0]->waste_category == 'Liquid Cleaning Waste')
					<img src="{{ public_path() . '/data_file/wwt/label/mudah_menyala.jpg' }}" width="90" height="90" class="cropped">
					@else
					<img src="{{ public_path() . '/data_file/wwt/label/beracun.jpg' }}" width="90" height="90" class="cropped">
					@endif
				</td>
				<td style="text-align: center;border: 1px solid black; background-color: yellow;" colspan="2">
					<img src="{{ public_path() . '/data_file/wwt/qr_code/'.$data[0]->slip.'.png' }}" width="90" height="90" class="cropped">
				</td>
			</tr>
			<tr>
				<td style="text-align: center;border: 1px solid black; background-color: yellow;" colspan="4"><b>{{$data[0]->slip}}</b></td>
			</tr>
			<tr>
				<td style="text-align: center;border: 1px solid black; background-color: yellow;" colspan="4">Tanggal Pengemasan : {{ $data[0]->date_in }}</td>
			</tr>
		</table>
</body>
</html>