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
			<?php if ( $data['datas']['status'] == 'acc_manager') { ?>
				<p style="font-size: 20px; font-weight: bold;">Asset Transfer Location</p>
				<p style="font-size: 20px; font-weight: bold;">A New Transfer Location Has Been Fully Approved, Please Print The Label</p>
			<?php } else { ?>
				<p style="font-size: 20px; font-weight: bold;">Approval Asset Transfer Location</p>
			<?php } ?>
			This is an automatic notification. Please do not reply to this address. <br><br>
			<?php if ($data['datas']['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Asset Transfer has been REJECTED</h1>
			<?php } else if ($data['datas']['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Asset Transfer has been HOLDED</h1>
			<?php } ?>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Number</th>
					<td style="width: 50%">{{ $data['datas']['fixed_asset_no'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name</th>
					<td style="width: 50%">{{ $data['datas']['fixed_asset_name'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Old Section Control</th>
					<td style="width: 50%">{{ $data['datas']['old_section'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Old Location</th>
					<td style="width: 50%">{{ $data['datas']['old_location'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Old PIC</th>
					<td style="width: 50%">{{ $data['datas']['old_pic'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">New Section Control</th>
					<td style="width: 50%">{{ $data['datas']['new_section'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">New Location</th>
					<td style="width: 50%">{{ $data['datas']['new_location'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">New PIC</th>
					<td style="width: 50%">{{ $data['datas']['new_pic'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Transfer Reason</th>
					<td style="width: 50%">{{ $data['datas']['transfer_reason'] }}</td>
				</tr>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>
			<?php if ($data['datas']['status'] == 'created') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Approved/old_pic") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Reject/old_pic") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'pic_old') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Approved/old_manager") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Hold/old_manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Reject/old_manager") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'manager_old') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Approved/new_pic") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Hold/new_pic") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Reject/new_pic") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'pic_new') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Approved/new_manager") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Hold/new_manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Reject/new_manager") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'manager_new') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Approved/acc_control") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Hold/acc_control") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Reject/acc_control") }}">&nbsp;&nbsp;&nbsp; Reject &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<br>
			<?php } else if($data['datas']['status'] == 'acc_control') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Approved/acc_manager") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Hold/acc_manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/transfer/".$data['datas']['id']."/Reject/acc_manager") }}">&nbsp; Reject &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'acc_manager') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas2'][0]['form_number']."/Approved/acc_label") }}">&nbsp;&nbsp;&nbsp; Print Label &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas2'][0]['form_number']."/Approved/resend_foreman") }}">&nbsp;&nbsp;&nbsp; Asset Label List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<br>
			<?php } else if($data['datas']['status'] == 'rejected' || $data['datas']['status'] == 'hold') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/transfer_asset") }}">&nbsp;&nbsp;&nbsp; Transfer Asset List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<br>
			<?php } ?>
		</center>
	</div>
</body>
</html>