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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 20px;">INFORMASI SCHEDULE AUDIT IK BULAN INI</span><br>
			This is an automatic notification. Please do not reply to this address.<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);color: white">
					<tr>
						<th style="width: 1%; border:1px solid black;">No.</th>
						<th style="width: 6%; border:1px solid black;">Leader</th>
						<th style="width: 6%; border:1px solid black;">Foreman</th>
						<th style="width: 2%; border:1px solid black;">Periode</th>
						<th style="width: 1%; border:1px solid black;">Jumlah IK</th>
					</tr>
				</thead>
				<tbody>
					<?php for($i = 0; $i < count($data);$i++){ ?>
					<tr>
						<td style="border: 1px solid black;text-align: right;padding-right: 4px;">{{$i+1}}</td>
						<td style="border: 1px solid black;text-align: left;padding-left: 4px;">{{$data[$i]->leader}}</td>
						<td style="border: 1px solid black;text-align: left;padding-left: 4px;">{{$data[$i]->foreman}}</td>
						<td style="border: 1px solid black;text-align: left;padding-left: 4px;">{{$data[$i]->month_name}}</td>
						<td style="border: 1px solid black;text-align: right;padding-right: 4px;">{{$data[$i]->qty}}</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/audit_ik_monitoring">Audit IK Monitoring</a>
		</center>
	</div>
</body>
</html>