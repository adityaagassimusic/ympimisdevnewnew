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
			width: 80px;
			height: 80px;
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
	
	for ($i=0; $i < count($tools_array2); $i++) { 
		 ?>
	

		<!-- <div class="column"> -->
		<table style="width: 175px;  margin-left: auto; margin-right: auto;text-align: left;">
			<tr>

				<?php for ($j=0; $j < count($tools_array2[$i]); $j++) { 

				QRcode::png($tools_array2[$i][$j][0]->rack_code.'_'.$tools_array2[$i][$j][0]->item_code.'_'.$tools_array2[$i][$j][0]->no_kanban, public_path().'/kanban_tools_qr/'.$tools_array2[$i][$j][0]->rack_code.'_'.$tools_array2[$i][$j][0]->item_code.'_'.$tools_array2[$i][$j][0]->no_kanban.'.png');
				?>

				<?php } ?>

				<?php for ($j=0; $j < count($tools_array2[$i]); $j++) { ?>

				<td style="text-align: center;border: 1px solid black; width: 90px;font-size: 8px;" colspan="3" rowspan="5">
					<img src="{{ public_path() . '/kanban_tools_qr/'.$tools_array2[$i][$j][0]->rack_code.'_'.$tools_array2[$i][$j][0]->item_code.'_'.$tools_array2[$i][$j][0]->no_kanban.'.png' }}" class="cropped">

					<?php echo $tools_array2[$i][$j][0]->rack_code."_".$tools_array2[$i][$j][0]->item_code."_".$tools_array2[$i][$j][0]->no_kanban ?>
				</td>
				<td style="width:175px;text-align: center; font-size: 12px; color: black;background-color: #ccc;border: 1px solid black;" colspan="3">{{ $tools_array2[$i][$j][0]->rack_code }} - {{ $tools_array2[$i][$j][0]->location }}
				</td>
				<td style="width:10px">&nbsp;</td>
				<?php } ?>
			</tr>	
			<tr>
				<?php for ($j=0; $j < count($tools_array2[$i]); $j++) { ?>
				<td style="text-align: center; font-size: 12px;border: 1px solid black" colspan="3"><?php echo $tools_array2[$i][$j][0]->group ?> </td>
				<td>&nbsp;</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for ($j=0; $j < count($tools_array2[$i]); $j++) { ?>
					<td style="width:175px;text-align: center; vertical-align: middle; font-weight: bold;border: 1px solid black;font-weight: bold;" colspan="3"><?php echo $tools_array2[$i][$j][0]->item_code ?> | <?php echo $tools_array2[$i][$j][0]->description ?> </td>
					<td style="width:10px">&nbsp;</td>
				<?php } ?>
			</tr>

			<tr>
				<?php for ($j=0; $j < count($tools_array2[$i]); $j++) { ?>
				<th style="font-size: 10px; background-color: #ccc; text-align: center; color:black;border: 1px solid black" colspan="2">Qty Order</th>
				<!-- <th style="background-color: black; text-align: center; color:white;border: 1px solid black">Uom</th> -->
				<th style="font-size: 10px; background-color: #ccc; text-align: center; color:black;border: 1px solid black">Kanban No</th>
				<td>&nbsp;</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for ($j=0; $j < count($tools_array2[$i]); $j++) { ?>
				<td style="text-align: center;color:black;border: 1px solid black;font-size: 14px;font-weight: bold;" colspan="2"><?= $tools_array2[$i][$j][0]->moq ?> <?= $tools_array2[$i][$j][0]->uom ?></td>
				<!-- <td style="text-align: center;color:black;border: 1px solid black;"></td> -->
				<td style="text-align: center;color:black;border: 1px solid black;font-size: 14px;font-weight: bold;"><?= $tools_array2[$i][$j][0]->no_kanban?></td>
				<td>&nbsp;</td>
				<?php } ?>
			</tr>
		</table>
		<!-- </div> -->
		<br>
	<?php } ?>
</body>
</html>