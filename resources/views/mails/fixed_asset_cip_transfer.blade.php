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
			<p style="font-size: 20px; font-weight: bold;">Transfer Form Fixed Asset CIP</p>

			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<tr>
					<th style="border:1px solid black; background-color: #c2ff7d;">No</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">CIP Number</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">CIP Name</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">FA Number</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">FA Name</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Currency</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">CIP Amount</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Allocation CIP Amount</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">FA Amount</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Registration Date</th>
				</tr>

				<?php $no =1; for ($i=0; $i < count($data['data']); $i++) { ?>
					<tr>
						<td>{{ $no }}</td>
						<td>{{ $data['data'][$i]['cip_sap_number'] }}</td>
						<td>{{ $data['data'][$i]['cip_asset_name'] }}</td>
						<?php 
						$con = 1;

						foreach ($data['count_fa'] as $c_fa) {
							if ($c_fa->new_sap_number == $data['data'][$i]['new_sap_number']) {
								$con = $c_fa->con;
							}
						}
						// dd($con);

						if (isset($data['data'][$i-1])) {
							if ($data['data'][$i-1]['new_sap_number'] != $data['data'][$i]['new_sap_number']) {
								echo "<td rowspan='".$con."'>".$data['data'][$i]['new_sap_number']."</td>";
							}
						} else {
							echo "<td rowspan='".$con."'>".$data['data'][$i]['new_sap_number']."</td>";
						} 

						if (isset($data['data'][$i-1])) {
							if ($data['data'][$i-1]['new_sap_number'] != $data['data'][$i]['new_sap_number']) {
								echo "<td rowspan='".$con."'>".$data['data'][$i]['new_asset_name']."</td>";
							}
						} else {
							echo "<td rowspan='".$con."'>".$data['data'][$i]['new_asset_name']."</td>";
						} 

						if (isset($data['data'][$i-1])) {
							if ($data['data'][$i-1]['new_sap_number'] != $data['data'][$i]['new_sap_number']) {
								echo "<td rowspan='".$con."'>".$data['data'][$i]['currency']."</td>";
							}
						} else {
							echo "<td rowspan='".$con."'>".$data['data'][$i]['currency']."</td>";
						} 

						?>
						
						<td>{{ $data['data'][$i]['cip_amount_usd'] }}</td>
						<td>{{ $data['data'][$i]['amount_use'] }}</td>

						<?php 
						if (isset($data['data'][$i-1])) {
							if ($data['data'][$i-1]['new_sap_number'] != $data['data'][$i]['new_sap_number']) {
								echo "<td rowspan='".$con."'>".$data['data'][$i]['amount_usd']."</td>";
							}
						} else {
							echo "<td rowspan='".$con."'>".$data['data'][$i]['amount_usd']."</td>";
						} 

						if (isset($data['data'][$i-1])) {
							if ($data['data'][$i-1]['new_sap_number'] != $data['data'][$i]['new_sap_number']) {
								echo "<td rowspan='".$con."'>".$data['data'][$i]['registration_date']."</td>";
							}
						} else {
							echo "<td rowspan='".$con."'>".$data['data'][$i]['registration_date']."</td>";
						} 
						?>
					</tr>
					<?php $no++; } ?>
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
				<br>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer_cip/".$data['data'][0]['cip_form_number']."/Approved/".$data['position']) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer_cip/".$data['data'][0]['cip_form_number']."/Hold/".$data['position']) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer_cip/".$data['data'][0]['cip_form_number']."/Reject/".$data['position']) }}">&nbsp; Reject &nbsp;</a>
			</center>
		</div>
	</body>
	</html>