<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
	td{
		padding-right: 5px;
		padding-left: 5px;
		padding-top: 0px;
		padding-bottom: 0px;
	}
	th{
		padding-right: 5px;
		padding-left: 5px;			
	}
</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: #ff6090; font-size: 24px;">Your Live Cooking Order Has Been Rejected by YMPI </span>
			<br>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="95%">
				<thead style="background-color: #63ccff">
					<tr>
						<th style="border:1px solid black; width: 9%; font-size: 11px; padding: 0px 0px 0px 0px;">Name</th>
						<?php
						for ($i=0; $i < count($data['calendar']); $i++) {
							print_r('<th style="border:1px solid black; width: 1%; font-size:11px; padding: 0px 0px 0px 0px;">'.date('d M', strtotime($data['calendar'][$i])).'</th>');
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php for ($i=0; $i < count($data['datas']); $i++) { ?>
					<tr>
						<td style="border: 1px solid black; text-align: left; background-color: #fff; padding: 0px 0px 0px 7px; font-size:16px;">{{$data['datas'][$i]['name']}}</td>
						<?php for ($j=0; $j < count($data['calendar']); $j++) { 
							if ($data['calendar'][$j] == $data['datas'][$i]['date']) { ?>
								<td style="border: 1px solid black; text-align: center; background-color: #ff6090; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
							<?php }else{ ?>
								<td style="border: 1px solid black; text-align: center; background-color: #fff; padding: 0px 0px 0px 0px; font-size:16px;"></td>
							<?php }
						} ?>
					</tr>
				<?php } ?>
		</tbody>
	</table>
	<br>
	<br>
	Last order & last change one day before.
	<br>
	<a href="http://10.109.52.4/mirai/public/index/ga_control/live_cooking">&#10148; Click this link if you want to change or create order.</a>
</center>
</div>
</body>
</html>