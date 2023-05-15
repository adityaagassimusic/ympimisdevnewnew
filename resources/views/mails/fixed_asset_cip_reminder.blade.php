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
			<p style="font-size: 20px; font-weight: bold;">Reminder Fixed Asset CIP</p>
			<p style="font-size: 20px; font-weight: bold;">Please Create Confirmation Form for Usage of Fixed Asset CIP Below</p>

			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<tr>
					<th style="border:1px solid black; background-color: #c2ff7d;">Fixed Asset Number</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Section</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Location</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Category</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Usage Term</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Usage Estimination</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Investment Number</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">PIC</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Acquisition Date</th>
				</tr>

				<?php  foreach ($data['datas'] as $datas) { ?>
					<tr>
						<td style="border: 1px solid black">{{ $datas->sap_number }}</td>
						<td style="border: 1px solid black">{{ $datas->fixed_asset_name }}</td>
						<td style="border: 1px solid black">{{ $datas->section }}</td>
						<td style="border: 1px solid black">{{ $datas->location }}</td>
						<td style="border: 1px solid black">{{ $datas->classification_category }}</td>
						<td style="border: 1px solid black">{{ $datas->usage_term }}</td>
						<td style="border: 1px solid black">{{ $datas->usage_estimation }}</td>
						<td style="border: 1px solid black">{{ $datas->investment }}</td>
						<td style="border: 1px solid black">{{ $datas->name }}</td>
						<td style="border: 1px solid black">{{ $datas->request_date }}</td>
					</tr>
				<?php } ?>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>

			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="http://10.109.52.1:887/miraidev/public/index/fixed_asset_cip/transfer_cip/form_user/{{ $data['form_number'] }}">&nbsp;&nbsp;&nbsp; Create Confirmation Form &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<!-- <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/fixed_asset/transfer_cip/form_user') }}">&nbsp;&nbsp;&nbsp; Create Confirmation Form &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('index/fixed_asset/transfer_cip') }}">&nbsp; Fixed Asset CIP List &nbsp;</a>
			<br>
		</center>
	</div>
</body>
</html>