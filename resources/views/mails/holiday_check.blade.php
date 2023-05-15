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
		.button {
			background-color: #4CAF50; /* Green */
			border: none;
			color: white;
			padding: 10px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			margin: 4px 2px;
			cursor: pointer;
			border-radius: 4px;
			cursor: pointer;
		}
		.button_reject {
			background-color: #fa3939; /* Green */
			border: none;
			color: white;
			padding: 10px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			margin: 4px 2px;
			cursor: pointer;
			border-radius: 4px;
			cursor: pointer;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Resume Pengecekan Safety Saat Akan Libur</span><br>
			<p>This is an automatic notification. Please do not reply to this address. </p>		
		</center>
	</div>			
	<div>
		<center>
			<table style="border:1px solid black; border-collapse: collapse; width: 60%" border="1">
				<thead>
					<tr>
						<th style="font-size: 15px; background-color: rgba(210, 159, 227, 0.7)" colspan="4">Yang Sudah Mengisi</th>
					</tr>
					<tr>
						<th style="font-size: 15px;width: 1%; background-color: rgba(210, 159, 227, 0.7)">No</th>
						<th style="font-size: 15px;width: 20%; background-color: rgba(210, 159, 227, 0.7)">Dibuat Oleh</th>
						<th style="font-size: 15px;width: 20%; background-color: rgba(210, 159, 227, 0.7)">Bagian</th>
						<th style="font-size: 15px;width: 10%; background-color: rgba(210, 159, 227, 0.7)">Dibuat Pada</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; foreach ($data['resume_check'] as $rsm) { ?>
						<tr>
							<td>{{ $no }}</td>
							<td>{{ explode('/',$rsm->pic)[1] }}</td>
							<td>{{ $rsm->location }}</td>
							<td>{{ explode(',',$rsm->create_at)[0] }}</td>
						</tr>
						<?php $no++; } ?>
					</tbody>
				</table>
				<br>
				<table style="border:1px solid black; border-collapse: collapse; width: 80%" border="1">
					<thead>
						<tr>
							<th style="font-size: 15px; background-color: rgba(210, 159, 227, 0.7)" colspan="6">Pengecekan dengan kondisi 'Tidak Ada'</th>
						</tr>
						<tr>
							<th style="font-size: 15px;width: 1%; background-color: rgba(210, 159, 227, 0.7)">No</th>
							<th style="font-size: 15px;width: 20%; background-color: rgba(210, 159, 227, 0.7)">Dibuat Oleh</th>
							<th style="font-size: 15px;width: 20%; background-color: rgba(210, 159, 227, 0.7)">Bagian</th>
							<th style="font-size: 15px;width: 20%; background-color: rgba(210, 159, 227, 0.7)">Cek Poin</th>
							<th style="font-size: 15px;width: 20%; background-color: rgba(210, 159, 227, 0.7)">Keterangan</th>
							<th style="font-size: 15px;width: 10%; background-color: rgba(210, 159, 227, 0.7)">Dibuat Pada</th>
						</tr>
					</thead>
					<tbody>
						<?php $nos = 1; foreach ($data['not_safe'] as $no_safe) { ?>
							<tr>					
								<td>{{ $nos }}</td>
								<td>{{ explode('/',$no_safe->pic)[1] }}</td>
								<td>{{ $no_safe->location }}</td>
								<td>{{ $no_safe->check_point }}</td>
								<td>{{ $no_safe->note }}</td>
								<td>{{ $no_safe->created_at }}</td>
							</tr>
							<?php $nos++; } ?>
						</tbody>
					</table>					
					<br>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
					<br>

					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/safety_check/monitoring") }}">&nbsp;&nbsp;&nbsp; Monitoring Pengisian &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</center>
			</div>
		</body>
		</html>