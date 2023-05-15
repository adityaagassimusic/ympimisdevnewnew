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
			<p style="font-weight: bold; color: purple; font-size: 18px;">AUDIT SCREW PIANICA - <?php if($data['statuses'] == 'Unbalance'){
				echo 'SCREW TIDAK SESUAI';
			}else{
				echo 'AUDIT TIDAK DILAKSANAKAN';
			} ?></p>
			This is an automatic notification. Please do not reply to this address.
			<?php if ($data['statuses'] == 'Not Implemented'): ?>
				<br>
				<br>
				<p style="font-weight: bold; color: red; font-size: 20px;">Audit Screw Pianica Tidak Dilaksanakan pada jam {{$data['hour']}}</p>
			<?php endif ?>
			<?php if ($data['audit'] != null): ?>
				<table style="border:1px solid black; border-collapse: collapse;" width="60%">
					<tbody>
						<tr>
							<td colspan="2" style=" border:1px solid black; background-color: #d4e157;font-weight: bold;text-align: center;">Details</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Line</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['audit']['line'] }}</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Screw MIRAI</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['audit']['screw_system'] }}</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Screw Counter</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['audit']['screw_counter'] }}</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Screw NG</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['audit']['screw_ng'] }}</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Selisih</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['diff'] }}</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Auditor</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['audit']['auditor_id'] }} - {{$data['audit']['auditor_name']}}</td>
						</tr>
						<tr>
							<td style="width: 2%; border:1px solid black; background-color: rgb(126,86,134);color: white;">Audited At</td>
							<td style="width: 4%; border:1px solid black;text-align: left;padding-left: 10px;">{{ $data['audit']['created_at'] }}</td>
						</tr>
					</tbody>
				</table>
			<?php endif ?>
			<br>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a style="width: 50px;text-decoration: none;font-size:16px;" href="http://10.109.52.4/mirai/public/index/pn/audit_screw/report">&nbsp;&nbsp;&nbsp; Report Audit Screw &nbsp;&nbsp;&nbsp;</a>			
		</center>
	</div>
</body>
</html>