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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;font-weight: bold;">Your employees have already met visitors.</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead>
					<tr>
						<th style="background-color: rgb(56, 181, 14);border: 1px solid black;color: white">Employee</th>
						<th style="background-color: rgb(56, 181, 14);border: 1px solid black;color: white">Dept</th>
						<th style="background-color: rgb(56, 181, 14);border: 1px solid black;color: white">Visitor Company</th>
						<th style="background-color: rgb(56, 181, 14);border: 1px solid black;color: white">Visitor Name</th>
						<th style="background-color: rgb(56, 181, 14);border: 1px solid black;color: white">Confirmed At</th>
						<th style="background-color: rgb(56, 181, 14);border: 1px solid black;color: white">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < count($data); $i++) {  ?>
						<tr>
							<td style="width: 2%; border:1px solid black;">{{$data[$i]['employees']}}</td>
							<td style="width: 2%; border:1px solid black;">{{$data[$i]['department']}}</td>
							<td style="width: 3%; border:1px solid black;">{{$data[$i]['company']}}</td>
							<td style="width: 3%; border:1px solid black;">{{$data[$i]['nama']}}</td>
							<td style="width: 2%; border:1px solid black;">{{$data[$i]['confirmed_at']}}</td>
							<td style="width: 2%; border:1px solid black;">{{$data[$i]['remark']}}</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>