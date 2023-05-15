<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding: 3px;
		}
		.cek {
			color: rgb(255, 79, 66) !important;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Top 20 Overtime Information トップ20位の残業情報 <br> (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			<p style="font-weight: bold;">Overtime Period 残業期間: {{ date('F Y', strtotime($data['first'])) }}</p>
			This is an automatic notification. Please do not reply to this address. <br>
			自動通知メールです。返信しないでください。
			<br>
			<br>
			<table style="border-color: black" style="width: 90%;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="7" style="background-color: #9f84a7">Production Overtime 生産残業</th>
					</tr>
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 2%; border:1px solid black; text-align: center;">Period<br>期間</th>
						<th style="width: 2%; border:1px solid black; text-align: center;">Dept<br>部門</th>
						<th style="width: 2%; border:1px solid black; text-align: center;">ID<br>社員番号</th>
						<th style="width: 6%; border:1px solid black; text-align: left;">Name<br>氏名</th>
						<th style="width: 5%; border:1px solid black; text-align: left;">Grade<br>グレード</th>
						<th style="width: 2%; border:1px solid black; text-align: right;">Σ Overtime<br>Σ 残業</th>
						<th style="width: 2%; border:1px solid black; text-align: left;">Remark<br>備考</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['productions']); $i++) { 

						if (round($data['productions'][$i]['overtime'], 2) >= (float) $data['limit']) {							
							$cls = 'class = "cek"';
							$note = 'Need Check <br> 要・否';
						} else {
							$cls = '';
							$note = '';
						}
						print_r ('<tr>
							<td '.$cls.' style="text-align:center">'.$data['productions'][$i]['period'].'</td>
							<td '.$cls.' style="text-align:center">'.$data['productions'][$i]['department'].'</td>
							<td '.$cls.' style="text-align:center">'.$data['productions'][$i]['employee_id'].'</td>
							<td '.$cls.' style="text-align:left">'.$data['productions'][$i]['name'].'</td>
							<td '.$cls.' style="text-align:left">'.$data['productions'][$i]['grade'].'</td>
							<td '.$cls.' style="text-align:right">'.round($data['productions'][$i]['overtime'],2).'</td>
							<td '.$cls.' style="text-align:left">'.$note.'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>

			<br>

			<table style="border-color: black" style="width: 90%;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="7" style="background-color: #9f84a7">Office Overtime 事務所の残業</th>
					</tr>
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 2%; border:1px solid black; text-align: center;">Period <br> 期間</th>
						<th style="width: 2%; border:1px solid black; text-align: center;">Dept <br> 部門</th>
						<th style="width: 2%; border:1px solid black; text-align: center;">ID <br> 社員番号</th>
						<th style="width: 6%; border:1px solid black; text-align: left;">Name <br> 氏名</th>
						<th style="width: 5%; border:1px solid black; text-align: left;">Grade <br> グレード</th>
						<th style="width: 2%; border:1px solid black; text-align: right;">Σ Overtime <br> Σ 残業</th>
						<th style="width: 2%; border:1px solid black; text-align: left;">Remark</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['offices']); $i++) { 
						if (round($data['offices'][$i]['overtime'], 2) >= (float) $data['limit']) {							
							$cls = 'class = "cek"';
							$note = 'Need Check <br> 要・否';
						} else {
							$cls = '';
							$note = '';
						}
						print_r ('<tr>
							<td '.$cls.' style="text-align:center">'.$data['offices'][$i]['period'].'</td>
							<td '.$cls.' style="text-align:center">'.$data['offices'][$i]['department'].'</td>
							<td '.$cls.' style="text-align:center">'.$data['offices'][$i]['employee_id'].'</td>
							<td '.$cls.' style="text-align:left">'.$data['offices'][$i]['name'].'</td>
							<td '.$cls.' style="text-align:left">'.$data['offices'][$i]['grade'].'</td>
							<td '.$cls.' style="text-align:right">'.round($data['offices'][$i]['overtime'], 2).'</td>
							<td '.$cls.' style="text-align:left">'.$note.'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/report/overtime_monthly_fq">Overtime Monitoring ( 残業監視 )</a><br>
		</center>
	</div>
</body>
</html>