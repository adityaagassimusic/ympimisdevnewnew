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
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>			
			<p style="font-size: 20px; font-weight: bold;">Confirmation Form Fixed Asset CIP</p>

			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<tr>
					<th style="border:1px solid black; background-color: #c2ff7d;">Fixed Asset Number</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Acquisition Date</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Location</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">OLD Usage Term</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">OLD Usage Est</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">NEW Usage Term</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">NEW Usage Est</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Created By</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Remark</th>
				</tr>

				<?php  foreach ($data['data'] as $datas) { ?>
					<tr>
						<td>{{ $datas['sap_number'] }}</td>
						<td>{{ $datas['fixed_asset_name'] }}</td>
						<td>{{ $datas['acquisition_date'] }}</td>
						<td>{{ $datas['location'] }}</td>
						<td>{{ $datas['usage_term_old'] }}</td>
						<td>{{ $datas['usage_estimation_old'] }}</td>
						<td>{{ $datas['usage_term'] }}</td>
						<td>{{ $datas['usage_estimation'] }}</td>
						<td>{{ $datas['name'] }}</td>
						<td></td>
					</tr>
				<?php } ?>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/cip/".$data['data'][0]['form_number']."/Approved/".$data['position']) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/cip/".$data['data'][0]['form_number']."/Hold/".$data['position']) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/cip/".$data['data'][0]['form_number']."/Reject/".$data['position']) }}">&nbsp; Reject &nbsp;</a>
		</center>
	</div>
</body>
</html>