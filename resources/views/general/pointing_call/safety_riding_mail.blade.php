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
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: purple; font-size: 24px;">{{ $data['year'] }}年 {{ $data['mon'] }}月 Catatan Record Penerapan 『Janji Safety Riding』</span><br>
		</center>
		<br>
		<div style="width: 90%; margin: auto;">
			<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="95%">
					<thead>
						<tr>
							<th style="text-align: left; border:1px solid black;" rowspan="2">Location</th>
							<th style="text-align: left; border:1px solid black;" rowspan="2">Department</th>
							<th style="text-align: right; border:1px solid black;" rowspan="2">Safety Riding</th>
							<th style="text-align: right; border:1px solid black;" rowspan="2">Check Progress</th>
							<th style="text-align: center; border:1px solid black;" colspan="2">Check Sebelum</th>
							<th style="text-align: center; border:1px solid black;" colspan="2">Check Sesudah</th>
						</tr>
						<tr>
							<th style="text-align: center; border:1px solid black;">Chief</th>
							<th style="text-align: center; border:1px solid black;">Manager</th>
							<th style="text-align: center; border:1px solid black;">Chief</th>
							<th style="text-align: center; border:1px solid black;">Manager</th>
						</tr>
					</thead>
					<tbody>
						@foreach($data['safety_ridings'] as $col)
						<tr>
							<td style="text-align: left; width: 0.1%; border:1px solid black;">{{ $col['location'] }}</td>
							<td style="text-align: left; width: 0.1%; border:1px solid black;">{{ $col['department_shortname'] }}</td>
							<td style="text-align: right; width: 1%; border:1px solid black;">{{ $col['total_sr'] }} of {{ $col['total_emp'] }} ({{ round(($col['total_sr']/$col['total_emp'])*100, 0)}}%)</td>
							<td style="text-align: right; width: 2%; border:1px solid black;">({{ $col['maru'] }} Maru) ({{ $col['batsu'] }} Batsu) ({{ round((($col['maru']+$col['batsu'])/($col['total_emp']*$col['total_date']))*100, 0) }}%)</td>
							<td style="text-align: center; width: 1%; border:1px solid black;">{{ $col['cb_name'] }}<br>{{ $col['cb_at'] }}</td>
							<td style="text-align: center; width: 1%; border:1px solid black;">{{ $col['mb_name'] }}<br>{{ $col['mb_at'] }}</td>
							<td style="text-align: center; width: 1%; border:1px solid black;">{{ $col['ca_name'] }}<br>{{ $col['ca_at'] }}</td>
							<td style="text-align: center; width: 1%; border:1px solid black;">{{ $col['ma_name'] }}<br>{{ $col['ma_at'] }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</center>
			<center>
				<br>
				Klik link dibawah untuk menuju halaman 
				<br>
				<a href="http://10.109.52.4/mirai/public/index/safety_riding">Catatan Record Penerapan 『Janji Safety Riding』</a>
			</center>
		</div>
	</div>
</body>
</html>