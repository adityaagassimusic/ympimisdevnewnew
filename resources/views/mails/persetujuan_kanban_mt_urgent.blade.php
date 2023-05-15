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
		.button_reject {
			background-color: #fa3939; /* Green */
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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			
			<?php if ($data['status'] == 'REJECTED'){ ?>
				<span style="font-weight: bold; color: purple; font-size: 24px;">APPROVAL REQUEST KANBAN MATERIAL URGENT <br></span>
				<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
				<h2 style="color: red">Your Urgent Material Kanban Request has been REJECTED</h2>
				<h2 style="color: red">Reason Rejected</h2>
				@foreach($data['information'] as $info)
				<h3 style="color: black">{{$info->reason_urgent}}</h3>
				@endforeach
			<?php } else if ($data['status'] == 'Approval leaderWH'){ ?>
				<span style="font-weight: bold; color: purple; font-size: 24px;">APPROVAL REQUEST KANBAN MATERIAL URGENT <br></span>
				<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
				<h2 style="color: red">There is Urgent Material Kanban Request</h2>
				<?php } else if ($data['status'] == 'Detail Kirim Users'){ ?><span style="font-weight: bold; color: purple; font-size: 24px;">INFORMATION REQUEST KANBAN MATERIAL URGENT <br></span>
				<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<?php } else if ($data['status'] == 'APPROVAL'){ ?>
				<span style="font-weight: bold; color: purple; font-size: 24px;">APPROVAL REQUEST KANBAN MATERIAL URGENT <br></span>
				<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
				<h2 style="color: green">Your Urgent Material Kanban Request has been Approval
				<?php } ?>
			</center>
		</div>			
		<div>
			<center>
				<div style="width: 80%">
					<table style="border:1px solid black; border-collapse: collapse;">
						<tbody align="center">
							@foreach($data['information'] as $infor)
							<tr>
								<td colspan="2" style="border:1px solid black; font-size: 15px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Information</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
									Date Request
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;color: red">
									{{$infor->created_at}}
								</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
									PIC Request
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
									{{$infor->name}}
								</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
									Department
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
									{{$infor->department}}
								</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
									Section
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
									{{$infor->section}}
								</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
									Reason Urgent
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
									{{$infor->reason_urgent_in}}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<br><br>

				</div>
				<div style="width: 100%">
					<table style="border:1px solid black; border-collapse: collapse;width: 90%">

						<tbody align="center">
							<tr>
								<td colspan="9" style="border:1px solid black; font-size: 15px; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align:center;">Detail Request Kanban Material</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15; font-weight: bold;background-color: #d4e157;text-align:center;">
									NO
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 15%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									Kode Request
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 2%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									GMC
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 30%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									Description
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									Lot
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									UOM
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 10%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									No Hako
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 20%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									Sloc Name
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 25%; height: 15;font-weight: bold;background-color: #d4e157;text-align:center;">
									Quantity Request
								</td>
							</tr>
							<?php $index = 1; ?>
							@foreach($data['rqst_kanban'] as $kanban)
							<tr>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$index}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->kode_request}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->gmc}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->description}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->lot}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->uom}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->no_hako}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->sloc_name}}
								</td>
								<td style="border:1px solid black; font-size: 13px; height: 15;text-align:center;">
									{{$kanban->quantity_request}}
								</td>

							</tr>
							<?php $index++ ?>
							@endforeach
						</tbody>
					</table>
				</div>
				<br>
				@if($data['status'] == 'Approval leaderWH')
				@foreach($data['information'] as $urgt)
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/request/kanbanurgent/".$urgt->kode_request."/leaderWH/approve") }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/request/kanbanurgent/".$urgt->kode_request."/leaderWH/reject") }}">&nbsp; Reject &nbsp;</a><br>
				@endforeach
				@endif
			</center>
		</div>
	</body>
	</html>