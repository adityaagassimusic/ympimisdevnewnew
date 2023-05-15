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
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Unverified Kaizen Teian (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border-color: black">
				<thead style="background-color: rgb(126,86,134);">
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 4%; border:1px solid black;">Department</th>
						<th style="width: 3%; border:1px solid black;">Section</th>
						<th style="width: 2%; border:1px solid black;">Unverified Chief / Foreman</th>
						<th style="width: 2%; border:1px solid black;">Unverified Manager</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($data['kaizens'] as $kzn) {
						print_r ('<tr>
							<td>'.$kzn->department.'</td>
							<td>'.$kzn->section.'</td>
							<td>'.$kzn->frm.'</td>
							<td>'.$kzn->mngr.'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>

			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="http://10.109.52.4/mirai/public/index/kaizen">&nbsp;&nbsp;&nbsp; Verify Kaizen Teian &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="http://10.109.52.4/mirai/public/index/kaizen/aproval/grafik/resume">&nbsp;&nbsp;&nbsp; Monitoring Unverified Kaizen Teian &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		
			<br>			
		</center>
	</div>
</body>
</html>