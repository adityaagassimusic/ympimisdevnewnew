<html>
<head>
	<title>YMPI ??????</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />

	<style type="text/css">
		.mstr {
			width: 370px; 
			display: inline-block;
			border: 3px solid black;
		}
		img {
			margin: 0px !important;
		}

		@page { margin: 110px 0 -70px 15px }


		/*@page { margin: 50px 0; }
		body { margin: 40px; }
		*/

		.cropped {
			width: 100px;
			height: 100px;
			vertical-align: middle;
			text-align: center;
			background-position: center center;
			background-repeat: no-repeat;
		}

	</style>
</head>
<body>
	<?php
	include public_path(). "/qr_generator/qrlib.php"; 
	for ($i=1; $i <= count($lists); $i++) { 

	QRcode::png($lists[$i-1]->barcode, public_path().'/kanban_mt_qr/'.$lists[$i-1]->barcode.'.png');

	if ($i % 2 == 0) $margin = 'style="margin-left:1px;margin-bottom:27px;"'; else $margin = 'style="margin-right:1px;margin-bottom:27px;"'?>
<?php
	if ($lists[$i-1]->lot == 0) $tot = 'Z1'; else $tot = $lists[$i-1]->lot
	?>
	<div class='mstr' <?php echo $margin; ?>>
		<table style="width: 95%;  margin-left: auto; margin-right: auto; margin-bottom: 3px;">
			<tr>
				<th style="width: 50%; text-align: center; color:black;"> <?php echo $lists[$i-1]->updated_at ?></th>
				<th style="width: 20%; font-size: 20px; text-align: center; border: 1px solid black; color:black;">No Kanban : <?php echo $lists[$i-1]->no_hako ?></th>
			</tr>

			<tr><td style="text-align: center; padding-bottom: 15px; font-size: 27px; padding-top: 18px; text-decoration: underline; color: black" colspan="2"><b>KANBAN MATERIAL</b></td></tr>

			<tr>
				<th style="width: 33%; background-color: black; text-align: center; color:white">GMC</th>
				<th style="width: 60%; background-color: black; text-align: center; color:white">DESC</th>
			</tr>
			<tr>


				<th style="width: 33%; text-align: center; color:black; border: 1px solid black; font-size: 30px;"> <?php echo $lists[$i-1]->gmc_material ?></th>
				<th style="width: 60%; height: 40px; font-size: 12px; text-align: center; border: 1px solid black; color:black;"> <?php echo $lists[$i-1]->description ?></th>
			</tr>

			<tr>
				<th style="width: 33%; background-color: black; text-align: center; color:white">LOT</th>
				<th style="width: 60%; background-color: black; text-align: center; color:white">BUYER CODE</th>
			</tr>
			<tr>
				<th style="width: 33%; height: 40px; font-size: 30px; text-align: center; color:black; border: 1px solid black; "><?php echo $tot; ?></th>
				<th style="width: 60%; text-align: center; font-size: 20px; border: 1px solid black; color:black;"> <?php echo $lists[$i-1]->buyer ?></th>
			</tr>

			<tr>
				<th style="width: 33%; background-color: black; text-align: center; color:white">PROCESS</th>
				<th style="width: 60%; background-color: black; text-align: center; color:white">TO LOC</th>
			</tr>
			<tr>
				<th style="width: 33%; height: 40px; text-align: center; font-size: 20px; color:black; border: 1px solid black; "> <?php echo $lists[$i-1]->sloc_name ?></th>
				<th style="width: 60%; text-align: center; border: 1px solid black; font-size: 20px; color:black;"> <?php echo $lists[$i-1]->rcvg_sloc ?></th>
			</tr>

			<tr><td colspan="2" style="padding-bottom: 6px;"></td></tr>

			<tr style=" width: 100px;"><td style="text-align: center; background-color: white; border: 3px solid black;" colspan="2"><img src="{{ public_path() . '/kanban_mt_qr/'.$lists[$i-1]->barcode.'.png' }}" class="cropped"></td>
			</tr>

			<tr><td style="text-align: center; font-weight: bold; background-color: white; border: 3px solid black; font-size: 15px;" colspan="2"><?php echo $lists[$i-1]->barcode ?></td></tr>
			
		</table>
	</div>
	<?php if ($i % 2 == 0) echo '<br><br>'; ?>
	<?php } ?>	
</body>
</html>