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
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p>This is an automatic notification. Please do not reply to this address. 自動メールです。返信しないでください。</p>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Notifikasi SDS Expaired</span><br><br>
			<table style="border-collapse: collapse; width: 50%">
				<thead>
					<tr align="center">
						<th colspan="7" style="border:1px solid black; font-size: 15px; background-color: #f6d965; height: 20; text-align: center;">Detail Information SDS</th>
					</tr>
					<?php $no = 1 ?>
					<tr align="center"> 
						<td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">No</td>
						<td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">Document ID</td>
						<td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">Item Code</td>
						<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">Judul SDS</td>
						<td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">Valid From</td>
						<td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">Valid To</td>
						<td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">Status</td>
					</tr>
				</thead>
				<tbody id="bodyTableOutstanding">
					<?php for ($i=0; $i < count($data['datas']); $i++) { ?>
						<tr align="center"> 
							<td style="border:1px solid black; font-size: 13px; height: 20;">{{ $no++ }}</td>
							<td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['datas'][$i]->document_id }}</td>
							<td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['datas'][$i]->gmc_material }}</td>
							<td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['datas'][$i]->title }}</td>
							<td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['datas'][$i]->version_date }}</td>
							<td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['datas'][$i]->last_date }}</td>
							<td style="border: 1px solid black; text-align: left; background-color: orange;">{{ $data['datas'][$i]->status }}</td>
						</tr>
					<?php } ?>
				</tbody>
			</table><br>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a style="width: 50px;text-decoration: underline; color: blue;" href="{{ url('https://10.109.52.4/miraidev/public/index/update/sds/'.$data['expa']) }}">Update Data Expaired</a>
		</center>
	</div>
</body>
</html>