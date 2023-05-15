<!DOCTYPE html>
<html>
<head>
	<title>Approval Fixed Asset Label</title>
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
			<?php if ( $data['datas'][0]['status'] == 'acc_label') { ?>
				<p style="font-size: 20px; font-weight: bold;">Asset Label Request</p>
				<p style="font-size: 20px; font-weight: bold;">An Asset Request Label Has Been Fully Approved, Please Print The Label</p>
			<?php } else if ( $data['datas'][0]['status'] == 'printed') { ?>
				<p style="font-size: 20px; font-weight: bold;">Asset Label Request</p>
				<p style="font-size: 20px; font-weight: bold;">An Asset Request Label Has Been Printed, Please Take The Label</p>
			<?php } else { ?>
				<p style="font-size: 20px; font-weight: bold;">Approval Asset Label Request</p>
			<?php } ?>
			This is an automatic notification. Please do not reply to this address.
			<?php if ($data['datas'][0]['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Asset Request Label has been REJECTED</h1>
			<?php } else if ($data['datas'][0]['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Asset Request Label has been HOLDED</h1>
			<?php } ?>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tr>
					<th style="border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">SAP Number</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Section</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">Location</th>
					<th style="border:1px solid black; background-color: #c2ff7d;">PIC</th>
				</tr>

				<?php  foreach ($data['datas'] as $datas) { ?>
					<tr>
						<td>{{ $datas['fixed_asset_name'] }}</td>
						<td>{{ $datas['fixed_asset_no'] }}</td>
						<td>{{ $datas['section'] }}</td>
						<td>{{ $datas['location'] }}</td>
						<td>{{ $datas['name'] }}</td>
					</tr>
				<?php } ?>
			</table>
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="50%">
				<tr>
					<th style="border:1px solid black; background-color: #c2ff7d;">Reason</th>
				</tr>
				<tr>
					<td>{{ $data['datas'][0]['reason'] }}</td>
				</tr>
			</table>
			<br><br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>
			<?php if ($data['datas'][0]['status'] == 'created') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Approved/pic") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Reject/pic") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas'][0]['status'] == 'acc_control') { ?>				
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Approved/acc_control") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Hold/acc_control") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Reject/acc_control") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas'][0]['status'] == 'acc_label') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Approved/acc_label") }}">&nbsp;&nbsp;&nbsp; Print Label &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas'][0]['form_number']."/Approved/resend_foreman") }}">&nbsp;&nbsp;&nbsp; Asset Label List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<br>
			<?php } else if($data['datas'][0]['status'] == 'reject' || $data['datas'][0]['status'] == 'hold') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/label_asset") }}">&nbsp;&nbsp;&nbsp; Label Request Asset List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<br>
			<?php } ?>			
		</center>
	</div>
</body>
</html>