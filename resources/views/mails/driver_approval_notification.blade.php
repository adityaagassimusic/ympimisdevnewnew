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
	<?php
	$id = $data['driver']['id'];
	?>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Driver Request Approval</p>
			This is an automatic notification. Please do not reply to this address.<br>
			<span style="font-weight: bold;color: red;font-size: 18px"><i>Permohonan ini sudah disetujui atasan pihak terkait.</i>
			</span><br>
			<?php if ($data['driver']['name'] != null): ?>
				<p style="font-size: 18px;font-weight: bold;">Driver Anda : {{$data['driver']['name']}}
				<br>No. HP : {{$data['driver']['phone_no']}}</p>
			<?php endif ?>
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">ID</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['id'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Pemohon</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['created_by'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Keperluan</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['purpose'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Kota Tujuan</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['destination_city'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Mulai</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['date_from'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Sampai</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['date_to'] }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/ga_control/driver') }}">Driver Monitoring System</a><br>
		</center>
	</div>
</body>
</html>