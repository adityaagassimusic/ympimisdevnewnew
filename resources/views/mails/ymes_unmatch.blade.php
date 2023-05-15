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
		.tittle {
			font-size: 20pt;
			font-weight: bold;
			margin: 0px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			This is an automatic notification. Please do not reply to this address.

			<br>
			<table style="width: 40%" style="border: none;">
				<tr style="border: none;">
					<th style="border: none; width: 70%; text-align: left;">UNMATCH MIRAI & KITTO - INTERFACE DATA</th>
					<th style="border: none; width: 5%;">:</th>
					<th style="border: none; width: 5%; text-align: right;">{{ count($data['unmatch_mirai']) }}</th>
					<th style="border: none; width: 10%;">Item(s)</th>
				</tr>
				<tr style="border: none;">
					<th style="border: none; width: 70%; text-align: left;">UNMATCH INTERFACE DATA - YMES</th>
					<th style="border: none; width: 5%;">:</th>
					<th style="border: none; width: 5%; text-align: right;">{{ count($data['unmatch_ymes']) }}</th>
					<th style="border: none; width: 10%;">Item(s)</th>
				</tr>
			</table>

			@if(count($data['unmatch_mirai']) > 0)
			<p style="font-size: 20px;">ACTUAL vs MIRAI</p>
			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 10%; border:1px solid black;">Category</th>
						<th style="width: 10%; border:1px solid black;">Material</th>
						<th style="width: 30%; border:1px solid black;">Description</th>
						<th style="width: 5%; border:1px solid black;">SLoc</th>
						<th style="width: 5%; border:1px solid black;">ToLoc</th>
						<th style="width: 5%; border:1px solid black;">MIRAI & KITTO</th>
						<th style="width: 5%; border:1px solid black;">IF Data</th>
						<th style="width: 5%; border:1px solid black;">Diff (Abs)</th>
					</tr>
				</thead>
				<tbody>					
					@foreach($data['unmatch_mirai'] as $col)
					<tr>
						<td style="border:1px solid black;">{{ $col['category'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col['material_number'] }}</td>
						<td style="border:1px solid black;">{{ $col['description'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col['issue'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col['receive'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col['old_trx'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col['new_trx'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ abs($col['new_trx'] - $col['old_trx']) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endif
			<br>
			@if(count($data['unmatch_ymes']) > 0)
			<p style="font-size: 20px;">MIRAI vs YMES</p>
			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 10%; border:1px solid black;">Category</th>
						<th style="width: 5%; border:1px solid black;">Material</th>
						<th style="width: 25%; border:1px solid black;">Description</th>
						<th style="width: 5%; border:1px solid black;">SLoc</th>
						<th style="width: 5%; border:1px solid black;">ToLoc</th>
						<th style="width: 5%; border:1px solid black;">IF Data</th>
						<th style="width: 5%; border:1px solid black;">Error</th>
						<th style="width: 5%; border:1px solid black;">YMES</th>
						<th style="width: 5%; border:1px solid black;"> YMES - (IF Data + Error)</th>
					</tr>
				</thead>
				<tbody>					
					@foreach($data['unmatch_ymes'] as $col)
					<tr>
						<td style="border:1px solid black;">{{ $col['category'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col['material_number'] }}</td>
						<td style="border:1px solid black;">{{ $col['description'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col['issue'] }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col['receive'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col['mirai'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col['error'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col['ymes'] }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col['ymes'] - ( $col['mirai'] - $col['error'] ) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endif

		</center>
	</div>
</body>
</html>