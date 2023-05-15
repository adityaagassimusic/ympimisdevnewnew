<!DOCTYPE html>
<html>
<head>
	<title>Approval Fixed Asset Registration</title>
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
	<?php $asset = $data['assets']; ?>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 20px;"><b>Approval New Asset Registration</b></p>
			This is an automatic notification. Please do not reply to this address.
			<?php if ($data['status'] == 'REJECTED'){ ?>
				<h1 style="color: red">Your Asset Registration has been REJECTED</h1><br>

				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #dd4b39;">Rejected By</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->reject_status }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #dd4b39;">Comment</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->comment }}</td>
					</tr>
				</table>
				<br>
			<?php } else if ($data['status'] == 'HOLD'){ ?>
				<h1 style="color: blue">Your Asset Registration has been HOLDED</h1><br>

				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #dd4b39;">Rejected By</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->reject_status }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #dd4b39;">Comment</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->comment }}</td>
					</tr>
				</table>
			<?php } ?>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold"><center>Asset Identification</center></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Requester</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->name }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Fixed Asset Name</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->asset_name }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Invoice Number</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->invoice_number }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Clasification</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->category_name }} - {{ $asset->clasification_name }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Vendor</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->vendor }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Currency</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->currency }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Original Amount</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->amount }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Amount in USD</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->amount_usd }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">PIC</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->pic }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Location</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->location }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Investment Number</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->investment_number }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Budget Number</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->budget_number }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Created By</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->name }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Created At</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->created_at }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Usage Term</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->usage_term }} - {{ $asset->usage_estimation }}</td>
					</tr>
					@if($data['status'] == 'APPROVAL MANAGER' || $data['status'] == 'APPROVAL MANAGER FA' || $data['status'] == 'REJECTED' || $data['status'] == 'HOLD')
					<tr>
						<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold"><center>Detail Asset Usage</center></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Category</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->category }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Category Code</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->category_code }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">SAP ID</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->sap_id }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Depreciation Key</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->depreciation_key }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #b1ff85;">Usefull Life</td>
						<td style="width: 2%; border:1px solid black;">{{ $asset->life_time }} years</td>
					</tr>
					@endif					
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			@if($data['status'] == 'FA CONTROL')
			<a style="background-color: orange; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/registration_asset_form") }}">&nbsp;&nbsp;&nbsp; Approve & Fill Acounting Section &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/fa_control/hold") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/fa_control/reject") }}">&nbsp; Reject &nbsp;</a><br>
			<br>
			@endif	

			@if($data['status'] == 'APPROVAL MANAGER')
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/manager_user/approve") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/manager_user/hold") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/manager_user/reject") }}">&nbsp; Reject &nbsp;</a><br>
			@endif

			@if($data['status'] == 'APPROVAL MANAGER FA')
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/".$asset->id."/manager_fa/approve") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/manager_fa/hold") }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/".$asset->id."/manager_fa/reject") }}">&nbsp; Reject &nbsp;</a><br>
			@endif

			@if($data['status'] == 'RECEIVE FA')
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas2'][0]['form_number']."/Approved/acc_label") }}">&nbsp;&nbsp;&nbsp; Print Label &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/label/".$data['datas2'][0]['form_number']."/Approved/resend_foreman") }}">&nbsp;&nbsp;&nbsp; Asset Label List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<br>
			@endif

			@if($data['status'] == 'REJECTED' || $data['status'] == 'HOLD')
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/registration_asset_form") }}">&nbsp;&nbsp;&nbsp; Fixed Asset Registration List &nbsp;&nbsp;&nbsp;</a>
			@endif

			<?php 
			if (isset($data['versi'])) {
				if ($data['versi'] == 'web') {
					print_r('<br><br><a style="background-color: #ddd; border: 1px solid orange; width: 50px;text-decoration: none;color: white;font-size: 20px; color: black;" href="'.url("files/fixed_asset/".$data['att']->att).'" target="_blank">&nbsp;&#9993; '.$data['att']->att.'&nbsp;</a>&nbsp;');

					if ($data['status'] == 'APPROVAL MANAGER FA') {
						print_r('&nbsp;<a style="background-color: #ddd; border: 1px solid orange; width: 50px;text-decoration: none;color: white;font-size: 20px; color: black; margin-left: 30px" href="'.url("files/fixed_asset/sap_file/".$asset->sap_file).'" target="_blank">&nbsp;&#9993; SAP_FILE &nbsp;</a>&nbsp;');
					}
				}
			}
			?>
		</center>
	</div>
</body>
</html>