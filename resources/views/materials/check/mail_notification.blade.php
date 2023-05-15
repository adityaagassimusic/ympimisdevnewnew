<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
			border: 1px solid black;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;
			border: 1px solid black;
		}
		table {
			border-collapse: collapse;
			border: 1px solid black;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<span style="font-weight: bold; font-size: 24px;">Informasi Kedatangan Indirect Material</span><br>
			<p style="color: red; font-weight: bold;">Note:<br>Mohon membuat permintaan material indirect dan melakukan pengecekan kesesuaian spesifikasi.</p>
			<div style="width: 90%; margin: auto;">
				<table style="border-color: black" style="width: 90%;">
					<thead style="background-color: rgb(126,86,134);">
						<tr style="color: white; background-color: #7e5686">
							<th style="width: 1%; text-align: center;">Kedatangan</th>
							<th style="width: 1%; text-align: left;">Material</th>
							<th style="width: 4%; text-align: left;">Deskripsi</th>
							<th style="width: 1%; text-align: center;">Jumlah Sample</th>
							<th style="width: 1%; text-align: center;">Uom</th>
							<th style="width: 2%; text-align: center;">Lokasi Pengecekan</th>
						</tr>
					</thead>
					<tbody>
						@foreach($data as $row)
						<tr>
							<td style="width: 1%; text-align: center;">{{ $row['posting_date'] }}</td>
							<td style="width: 1%; text-align: left;">{{ $row['material_number'] }}</td>
							<td style="width: 4%; text-align: left;">{{ $row['material_description'] }}</td>
							<td style="width: 1%; text-align: center;">{{ $row['sample_qty'] }}</td>
							<td style="width: 1%; text-align: center;">{{ $row['uom'] }}</td>
							<td style="width: 2%; text-align: center;">{{ $row['location'] }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<br>
				<br>
				<a href="{{ url('/index/material/check_monitoring') }}">Menuju Monitoring Pengecekan Kedatangan Material Indirect.</a>
			</div>
		</center>
	</div>
</body>
</html>