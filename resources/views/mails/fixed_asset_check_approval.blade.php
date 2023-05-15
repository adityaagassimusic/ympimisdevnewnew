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
			text-align: left;		
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<?php if ($data['position'] == 'Auditor' || $data['position'] == 'Chief Foreman' || $data['position'] == "Manager") { ?>
				<p style="font-size: 20px; font-weight: bold;">Approval Audit Check Asset</p>
			<?php } else if($data['position'] == 'Audit Report') { ?>
				<p style="font-size: 20px; font-weight: bold;">Report Audit</p>
			<?php } ?>
			<p style="font-size: 20px; font-weight: bold;">Period : {{ $data['period'] }}</p>

			<?php if ($data['position'] == 'Auditor') { ?>
				<p style="font-size: 18px; font-weight: bold;">Asset Has Been Checked and Fully Approved <br> Please Do an Audit</p>
			<?php } ?>

			This is an automatic notification. Please do not reply to this address.
			<?php if ($data['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Audit Check Asset has been REJECTED</h1>
			<?php } else if ($data['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Audit Check Asset has been HOLDED</h1>
			<?php } else if ($data['status'] == 'complete'){ ?>
				<h1 style="color: blue">Your Audit Check Asset has been Fully Approved</h1>
			<?php } ?>			

			<br><br>
			<?php if ($data['position'] == 'Chief Foreman' || $data['position'] == "Manager") { ?>				
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<tr>
						<td rowspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Section</center></td>
						<td rowspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Qty Fixed Asset</center></td>
						<td colspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Audit Result</center></td>
						<td colspan="5" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Condition</center></td>
					</tr>
					<tr>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Available</center></td>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Not Available</center></td>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Broken</center></td>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Not Use</center></td>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Label Not Update</center></td>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Map Not Update</center></td>
						<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Photo Not Update</center></td>
					</tr>
					<?php foreach ($data['data_details'] as $datas) { ?>
						<tr>
							<td style="border: 1px solid black; font-weight: bold">{{ $data['datas'][0]->asset_section }}</td>
							<td style="text-align: right;">{{ $data['datas'][0]->total_asset }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->ada }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->tidak_ada }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->rusak }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->tidak_digunakan }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->label_rusak }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->map_rusak }}</td>
							<td style="text-align: right; border: 1px solid black">{{ $datas->image_rusak }}</td>
						</tr>
					<?php } ?>
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
				<br>
				<?php if ($data['position'] == 'Chief Foreman') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/audit/approval/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period."/Approved/chief") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/audit/approval/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period."/Hold/chief") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/audit/approval/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period."/Reject/chief") }}">&nbsp; Reject &nbsp;</a>
					<br><br>
					<a style="background-color: #f7a540; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/check_report/fixed_asset/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period) }}">&nbsp;&nbsp;&nbsp; Detail Check Asset &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<?php } else if($data['position'] == 'Manager') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/audit/approval/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period."/Approved/manager") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/audit/approval/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period."/Hold/manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/audit/approval/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period."/Reject/manager") }}">&nbsp; Reject &nbsp;</a>
					<br><br>
					<a style="background-color: #f7a540; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/check_report/fixed_asset/".$data['datas'][0]->asset_section."/".$data['datas'][0]->period) }}">&nbsp;&nbsp;&nbsp; Detail Check Asset &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br>
					
				<?php } ?>	

				@if($data['status'] == 'reject' || $data['status'] == 'hold')
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/audit/list") }}">&nbsp;&nbsp;&nbsp; Fixed Asset Missing List &nbsp;&nbsp;&nbsp;</a>
				@endif	
			<?php } else if($data['position'] == 'Auditor') { ?>
				<table style="border:1px solid black; border-collapse: collapse;" width="70%">
					<tr>
						<td colspan="4" style="border: 1px solid black; background-color: #f7da88; font-weight: bold"><center>Asset Summary</center></td>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Asset Section</th>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Asset Location</th>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Number of Asset</th>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Quantity Audit</th>
					</tr>

					<?php $adt = 0; $audit = 0; foreach ($data['datas'] as $dt) { ?>
						<tr>
							<td style="border: 1px solid black">{{ $dt->asset_section }}</td>
							<td style="border: 1px solid black;">{{ $dt->location }}</td>
							<td style="border: 1px solid black; text-align: right;">{{ $dt->qty_asset }}</td>
							<?php $tmp_adt = ceil(10 / 100 * (int) $dt->qty_asset); ?>
							<?php if ($tmp_adt == 0) $tmp_adt = 1;  ?>
							<td style="border: 1px solid black; text-align: right;"><?php echo $tmp_adt; ?>  </td>
						</tr>
						<?php $audit += (int) $dt->qty_asset;  ?>
						<?php $adt += $tmp_adt; } ?>
						<tr>
							<td colspan="2" style="font-weight: bold">Total Asset</td>
							<td style="border: 1px solid black; text-align: right; font-weight: bold">{{ $audit }}</td>
							<td style="border: 1px solid black; text-align: right; font-weight: bold">{{ $adt }}</td>
						</tr>
					</table>

					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
					<br>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/audit/") }}">&nbsp;&nbsp;&nbsp; Audit Form &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;						
				<?php } else if($data['position'] == 'Audit Report') { ?>
					<table style="border:1px solid black; border-collapse: collapse;" width="80%">
						<tr>
							<td rowspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Section</center></td>
							<td rowspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Location</center></td>
							<td rowspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Qty Fixed Asset</center></td>
							<td rowspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Qty Audited Asset</center></td>
							<td colspan="2" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Audit Result</center></td>
							<td colspan="5" style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Condition</center></td>
						</tr>
						<tr>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Available</center></td>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Not Available</center></td>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Broken</center></td>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Not Use</center></td>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Label Not Update</center></td>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Map Not Update</center></td>
							<td style="border: 1px solid black; font-weight: bold; background-color: #ffedba;"><center>Photo Not Update</center></td>
						</tr>
						<?php foreach ($data['data_details'] as $datas) { ?>
							<tr>
								<td style="border: 1px solid black; font-weight: bold">{{ $datas->asset_section }}</td>
								<td style="border: 1px solid black; font-weight: bold">{{ $datas->location }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->total_asset }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->audit_asset }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->ada }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->tidak_ada }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->rusak }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->tidak_digunakan }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->label_rusak }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->map_rusak }}</td>
								<td style="text-align: right; border: 1px solid black">{{ $datas->image_rusak }}</td>
							</tr>
						<?php } ?>
					</table>
				<?php } ?>
			</center>
		</div>
	</body>
	</html>