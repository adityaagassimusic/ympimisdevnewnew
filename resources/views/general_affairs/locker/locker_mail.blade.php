<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		tbody>tr>td{
			padding: 5px 5px 5px 5px;
		}
		thead>tr>th{
			padding: 5px 5px 5px 5px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: purple; font-size: 24px;">{{ $data['title'] }}</span><br>
		</center>
		<br>
		<div style="margin: auto;">
			<center>
				<table style="border: 1px solid black; border-collapse: collapse;">
					<thead style="background-color: #605ca8; color: white;">
						<tr>
							<th style="border: 1px solid black; text-align: center; width: 0.1%;">#</th>
							<th style="border: 1px solid black; text-align: left; width: 0.1%;">Locker ID</th>
							<th style="border: 1px solid black; text-align: left; width: 0.1%;">Employee ID</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Name</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Department</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Section</th>
							<th style="border: 1px solid black; text-align: right; width: 0.5%;">Day(s)</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 0 ?>
						@foreach($data['lockers'] as $col)
						<?php $cnt += 1; ?>
						<tr>
							<td style="border: 1px solid black; text-align: center;">{{ $cnt }}</td>
							<td style="border: 1px solid black; text-align: left;">{{ $col['locker_id'] }}</td>
							<td style="border: 1px solid black; text-align: left;">{{ $col['employee_id'] }}</td>
							<td style="border: 1px solid black; text-align: left;">{{ $col['employee_name'] }}</td>
							<td style="border: 1px solid black; text-align: left;">{{ $col['department_name'] }}</td>
							<td style="border: 1px solid black; text-align: left;">{{ $col['section_name'] }}</td>
							<td style="border: 1px solid black; text-align: right;">{{ $col['since'] }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</center>
		</div>
		<br>
		<br>
		<center>
			Click link below to access Monitoring
			<br>
			<a href="https://10.109.52.4/mirai/public/index/ga_control/locker">Locker Control</a>
		</center>
	</div>
</body>
</html>