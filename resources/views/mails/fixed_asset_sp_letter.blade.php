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
			<?php if ($data['position'] == 'Reminder') { ?>
				<p style="font-size: 20px; font-weight: bold;">Reminder Fixed Asset CIP - More Than 1 Year</p>
			<?php } else if($data['position'] == 'Approval') { ?>
				<p style="font-size: 20px; font-weight: bold;">Approval Fixed Asset Special Letter</p>
			<?php } ?>
			<p style="font-size: 20px; font-weight: bold;">Period : {{ $data['period'] }}</p>

			This is an automatic notification. Please do not reply to this address.
			<?php if ($data['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Fixed Asset Special Letter has been REJECTED</h1>
			<?php } else if ($data['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Fixed Asset Special Letter has been HOLDED</h1>
			<?php } else if ($data['status'] == 'complete'){ ?>
				<h1 style="color: blue">Your Fixed Asset Special Letter has been Fully Approved</h1>
			<?php } ?>			

			<br><br>
			<label style="text-align: left !important">I apply to inform reason  for Long Outstanding Contruction On Progres (CIP) as bellow :</label>
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tr>
					<td style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>No</center></td>
					<td style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Fixed Asset Number</center></td>
					<td style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Fixed Asset Name</center></td>
					<td style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Date Acquisition</center></td>
					<td style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Amount (USD)</center></td>
					<td style="border: 1px solid black; font-weight: bold; background-color: #f7da88;"><center>Plan Use</center></td>
				</tr>

				<?php $no = 1; $arr_fa = []; foreach ($data['data'] as $datas) { ?>
					<tr>
						<td style="border: 1px solid black; font-weight: bold">{{ $no }}</td>
						<td style="text-align: right; border: 1px solid black">{{ $datas['fixed_asset_number'] }}</td>
						<td style="border: 1px solid black">{{ $datas['fixed_asset_name'] }}</td>
						<td style="border: 1px solid black">{{ $datas['acquisition_date'] }}</td>
						<td style="text-align: right; border: 1px solid black">{{ $datas['amount'] }}</td>
						<td style="border: 1px solid black">{{ $datas['plan_use'] }}</td>
					</tr>
					<?php $no++; array_push($arr_fa, $datas['fixed_asset_number']); } ?>
				</table>
				<br>
				<table width="80%" style="border:1px solid black; border-collapse: collapse; border: 1px solid black">
					<!-- <tr>
						<th width="30%" style="background-color: #f7da88; border:1px solid black;">件名 Subject</th>
						<td style="border:1px solid black;">{{ $data['data'][0]->subject_jp }} <br> {{ $data['data'][0]->subject }}</td>
					</tr> -->
					<tr>
						<th style="background-color: #f7da88; border:1px solid black;" width="30%">特命理由 Specific Reason</th>
						<td style="border:1px solid black;">{{ $data['data'][0]->reason_jp }} <br> {{ $data['data'][0]->reason }}</td>
					</tr>
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
				<br>
				<?php 
				if($data['position'] == 'Reminder') { 
					$string_fa = implode(',', $arr_fa);
					?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/cip/".$string_fa) }}">&nbsp;&nbsp;&nbsp; Disposal Asset &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset_sp_letter/create/".$data['data'][0]['form_number']) }}">&nbsp;&nbsp;&nbsp; Create Special Reason Letter &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php } else { ?>	
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset_sp_letter/Approved/".$data['position']."/".$data['data'][0]['form_number']) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset_sp_letter/Hold/".$data['position']."/".$data['data'][0]['form_number']) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset_sp_letter/Reject/".$data['position']."/".$data['data'][0]['form_number']) }}">&nbsp; Reject &nbsp;</a>
					<br>
				<?php } ?>

				@if($data['status'] == 'reject' || $data['status'] == 'hold')
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/audit/list") }}">&nbsp;&nbsp;&nbsp; Fixed Asset Missing List &nbsp;&nbsp;&nbsp;</a>
				@endif	

			</center>
		</div>
	</body>
	</html>