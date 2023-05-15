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
							<th style="border: 1px solid black; text-align: left; width: 1%;">ID</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Category</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Employee</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Location</th>
							<th style="border: 1px solid black; text-align: right; width: 1%;">Valid From</th>
							<th style="border: 1px solid black; text-align: right; width: 1%;">Valid To</th>
							<th style="border: 1px solid black; text-align: right; width: 1%;">Valid Left</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 0 ?>
						@foreach($data['licenses'] as $col)
						<?php $cnt += 1; ?>
						<tr>
							<td style="width: 1%; border: 1px solid black; text-align: center;">{{ $cnt }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col['license_id'] }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col['license_category'] }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col['employee_id'] }}<br>{{ $col['employee_name'] }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col['location'] }}<br>{{ $col['department'] }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: right;">{{ $col['valid_from'] }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: right;">{{ $col['valid_to'] }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: right;">{{ $col['diff'] }}</td>
							@if($col['status'] == 'AtRisk')
							<td style="width: 1%; border: 1px solid black; text-align: left; background-color: orange;">{{ $col['status'] }}</td>
							@elseif($col['status'] == 'Expired')
							<td style="width: 1%; border: 1px solid black; text-align: left; background-color: RGB(255,204,255);">{{ $col['status'] }}</td>
							@endif
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
			<a href="http://10.109.52.4/mirai/public/index/license/{{ $data['link'] }}">License Monitoring</a>
		</center>
	</div>
</body>
</html>