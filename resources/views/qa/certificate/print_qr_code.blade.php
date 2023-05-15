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
	
	for ($i=0; $i < count($cer_array2); $i++) { 
		 ?>
	

		<!-- <div class="column"> -->
		<table style="width: 250px;  margin-left: auto; margin-right: auto;text-align: left;">
			<tr>
				<?php for ($j=0; $j < count($cer_array2[$i]); $j++) { 
					QRcode::png(url('print/qa/certificate/'.$cer_array2[$i][$j][0]->certificate_id), public_path().'/data_file/qa/certificate_qr/'.$cer_array2[$i][$j][0]->certificate_id.'.png');
					?>
				<td style="width:250px;text-align: center; font-size: 16px; color: white;background-color: black;border: 1px solid black;">YMPI-QA-{{$cer_array2[$i][$j][0]->code}}-{{$cer_array2[$i][$j][0]->code_number}}{{$cer_array2[$i][$j][0]->number}}
				</td>
				<td style="width:10px">&nbsp;</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for ($j=0; $j < count($cer_array2[$i]); $j++) { ?>
					<td style="width:250px;text-align: center; font-weight: bold;border: 1px solid black;font-size: 12px;"><?php echo $cer_array2[$i][$j][0]->employee_id ?> - <?php echo $cer_array2[$i][$j][0]->name ?></td>
					<td style="width:10px">&nbsp;</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for ($j=0; $j < count($cer_array2[$i]); $j++) { ?>
				<td style="text-align: center;border: 1px solid black; width: 120px;font-size: 8px;"><br>
					<img src="{{ public_path() . '/data_file/qa/certificate_qr/'.$cer_array2[$i][$j][0]->certificate_id.'.png' }}" class="cropped">
					<br>
					{{url('print/qa/certificate/')}}/{{$cer_array2[$i][$j][0]->certificate_id}}
				</td>
				<td>&nbsp;</td>
			<?php } ?>
			</tr>
		</table>
		<!-- </div> -->
		<br>
	<?php } ?>
</body>
</html>