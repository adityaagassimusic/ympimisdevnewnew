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
		/*.button {
		  background-color: #4CAF50;
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
		}*/
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Cek Efektifitas Temuan Audit IK Leader</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Detail Temuan</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Document</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['data']->no_dokumen}} - {{$data['data']->nama_dokumen}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Dept</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['data']->department_shortname}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Leader</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['data']->leader}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Foreman</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['data']->foreman}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Audit Date</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['data']->date}}
							</td>
						</tr>

						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Temuan</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['data']->handling}}
							</td>
						</tr>

						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Penanganan</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php echo $data['data']->handling_result ?>
							</td>
						</tr>

						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">PIC Cek Efektifitas</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data['activity']->auditor_effectivity_id}} - {{$data['activity']->auditor_effectivity_name}}
							</td>
						</tr>
						
					</thead>
					<!-- <tbody style="text-align: center;">
					</tbody> -->
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a href="{{url('index/audit_ik_monitoring/cek_efektifitas/'.$data['data']->id)}}">Cek Efektifitas</a><br>
					<br>
					<a href="{{url('index/audit_ik_monitoring')}}">Monitoring</a>
		</center>
	</div>
</body>
</html>