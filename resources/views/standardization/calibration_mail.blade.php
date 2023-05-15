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
			<span style="font-weight: bold; color: purple; font-size: 24px;">Calibration Reminder</span><br>
		</center>
		<br>
		<div style="margin: auto;">
			<center>
				<table style="border: 1px solid black; border-collapse: collapse;">
					<thead style="background-color: #605ca8; color: white;">
						<tr>
							<th style="border: 1px solid black; text-align: center; width: 0.1%;">#</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">ID</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Kategori</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Merk<br>Nama</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Lokasi</th>
							<th style="border: 1px solid black; text-align: right; width: 1%;">Tanggal Kalibrasi</th>
							<th style="border: 1px solid black; text-align: right; width: 1%;">Kalibrasi Berikutnya</th>
							<th style="border: 1px solid black; text-align: right; width: 1%;">Sisa Hari</th>
							<th style="border: 1px solid black; text-align: left; width: 1%;">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 0 ?>
						@foreach($data['calibrations'] as $col)
						<?php $cnt += 1; ?>
						<tr>
							<td style="width: 1%; border: 1px solid black; text-align: center;">{{ $cnt }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col->calibration_id }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col->category }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col->instrument_brand }}<br>{{ $col->instrument_name }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: left;">{{ $col->location }}<br>{{ $col->department }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: right;">{{ $col->valid_from }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: right;">{{ $col->valid_to }}</td>
							<td style="width: 1%; border: 1px solid black; text-align: right;">{{ $col->date_diff }}</td>
							@if($col->status == 'Akan Kalibrasi')
							<td style="width: 1%; border: 1px solid black; text-align: left; background-color: orange;">{{ $col->status }}</td>
							@elseif($col->status == 'Harus Kalibrasi')
							<td style="width: 1%; border: 1px solid black; text-align: left; background-color: RGB(255,204,255);">{{ $col->status }}</td>
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
			<a href="http://10.109.52.4/mirai/public/index/standardization/calibration">Calibration Control </a>
		</center>
	</div>
</body>
</html>