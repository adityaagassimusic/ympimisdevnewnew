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
			<p style="font-size: 20px; font-weight: bold;">Approval Asset Disposal Scrap Report</p>
			This is an automatic notification. Please do not reply to this address.
			<?php if ($data['datas']['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Asset Disposal has been REJECTED</h1>
			<?php } else if ($data['datas']['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Asset Disposal has been HOLDED</h1>
			<?php } ?>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tr>
					<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold"><center>Asset Identification</center></td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Disposal No</th>
					<td style="width: 50%">{{ $data['datas']['form_number_disposal'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Number 固定資産番号</th>
					<td style="width: 50%; text-align: right;">{{ $data['datas']['fixed_asset_id'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name 固定資産名</th>
					<td style="width: 50%">{{ $data['datas']['fixed_asset_name'] }}</td>
				</tr>

				<tr>
					<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold"><center>Disposal Activity</center></td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Disposal Date</th>
					<td style="width: 50%">{{ $data['datas']['disposal_date'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Officer Department</th>
					<td style="width: 50%">{{ $data['datas']['officer_department'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Officer Name</th>
					<td style="width: 50%">{{ $data['datas']['officer'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Picture Scrap Before</th>
					<td style="width: 50%; padding-top: 2px; padding-bottom: 2px">
					<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('files/fixed_asset/scrap_picture/'.$data["datas"]["picture_before"])))}}" alt="" style="max-height: 100px;"><br>
				</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Picture Scrap Process</th>
					<td style="width: 50%; padding-top: 2px; padding-bottom: 2px">
						<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('files/fixed_asset/scrap_picture/'.$data["datas"]["picture_process"])))}}" alt="" style="max-height: 100px;">
					</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Picture Scrap After</th>
					<td style="width: 50%; padding-top: 2px; padding-bottom: 2px">
						<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('files/fixed_asset/scrap_picture/'.$data["datas"]["picture_after"])))}}" alt="" style="max-height: 100px;">
					</td>
				</tr>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>
			<?php if ($data['datas']['status'] == 'created') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Approved/pic") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Reject/pic") }}">&nbsp;  Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'pic') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Approved/manager") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Hold/manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Reject/manager") }}">&nbsp;  Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'manager') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Approved/gm") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Hold/gm") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Reject/gm") }}">&nbsp;  Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'gm') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Approved/acc_manager") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Hold/acc_manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Reject/acc_manager") }}">&nbsp;  Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'acc_manager') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Approved/director") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Hold/director") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Reject/director") }}">&nbsp;  Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'director') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Approved/fa_control") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Hold/fa_control") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/scrap/".$data['datas']['id']."/Reject/fa_control") }}">&nbsp;  Reject (却下) &nbsp;</a>
				<br>
			<?php } ?>

			@if($data['datas']['status'] == 'reject' || $data['datas']['status'] == 'hold')
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/disposal/scrap") }}">&nbsp;&nbsp;&nbsp; Fixed Asset Disposal Scrap List &nbsp;&nbsp;&nbsp;</a>
			@endif	
		</center>
	</div>
</body>
</html>