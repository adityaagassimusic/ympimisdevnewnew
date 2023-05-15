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
			padding-top: 0px;
			padding-bottom: 0px;
			padding-left: 3px;
			padding-right: 3px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>

			<p style="font-size: 18px;">Safety Shoes</p>
			
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="50%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black; width: 10%;">#</th>
						<th style="width: 1%; border:1px solid black; width: 30%;">Merk</th>
						<th style="width: 1%; border:1px solid black; width: 10%;">Gender</th>
						<th style="width: 1%; border:1px solid black; width: 10%;">Size</th>
						<th style="width: 1%; border:1px solid black; width: 30%;">Status Sepatu</th>
						<th style="width: 1%; border:1px solid black; width: 10%;">Qty</th>
					</tr>
				</thead>
				<tbody>

					@php
					$i=0;
					@endphp
					@foreach($data as $dt)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{ ++$i }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $dt['merk'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $dt['gender'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $dt['size'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $dt['status'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $dt['quantity'] }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ secure_url("index/std_control/safety_shoes") }}">Check Stock</a>
		</center>
	</div>
</body>
</html>