<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;			
		}
		th {
			padding: 2px;
		}
		td {
			padding: 2px;
			text-align: left !important;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 22px; font-weight: bold;">3M Reminder <br> 3M変更リマインダー</p>
			<p>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			自動通知です。返事しないでください。
			<br>
			<p style="font-size: 18px; font-weight: bold">
				This 3M Application has been submited from <b id="req_date" style="color: #9d47ff;">{{ $data['datas']->create_date }}</b><br>
				And Have to Change on  <span style="color: #9d47ff;">{{ $data['datas']->target_date }}</span><br>
				Please Check this 3M Progress
			</p>
			<table style="border-color: black; width: 80%">
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M番号 3M Number</th>
					<td>{{ $data['datas']->form_identity_number }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Reference Number</th>
					<td>{{ $data['datas']->sakurentsu_number }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M変更表題 3M Title</th>
					<td>{{ $data['datas']->title }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">製品名 Product Name</th>
					<td>{{ $data['datas']->product_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">工程名 Proccess Name</th>
					<td>{{ $data['datas']->proccess_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">班名 Unit Name</th>
					<td>{{ $data['datas']->unit }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M変更種類 3M Category</th>
					<td>{{ $data['datas']->category }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">進捗状況 Progress Status</th>
					<td>
						<?php 
						if ($data['datas']->remark == '1') {
							echo 'Translating Form';
						} else if($data['datas']->remark == '2') {
							echo 'Waiting for a Meeting';
						} else if($data['datas']->remark == '3') {
							echo 'Waiting for Document Needs';
						} else if($data['datas']->remark == '4') {
							echo 'Approval Related Department';
						} else if($data['datas']->remark == '5') {
							echo 'Progress Approval';
						} else if($data['datas']->remark == '6') {
							echo 'Waiting for Implementation Form Created';
						} else if($data['datas']->remark == '7') {
							echo 'Waiting for Implementation Form Created';
						} else if($data['datas']->remark == '8') {
							echo 'Approval Implementation Form';
						} else if($data['datas']->remark == '9') {
							echo 'Implementation Form Finish';
						}
						?>
					</td>
				</tr>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
			<a style="color: white; width: 50px;text-decoration: none;font-size:20px; background-color: #4ecc4b;" href="http://10.109.52.4/mirai/public/reminder/sakurentsu/3m/{{ $data['datas']->id }}">&nbsp;&nbsp;&nbsp; Add New Reminder (新催促を追加する)&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="color: white; width: 50px;text-decoration: none;font-size:20px; background-color: #569fe3;" href="http://10.109.52.4/mirai/public/detail/sakurentsu/3m/{{ $data['datas']->id.'/view' }}">&nbsp;&nbsp;&nbsp; 3M Form Report (3M報告)&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<br>
			<a style="color: white; width: 50px;text-decoration: none;font-size:20px; background-color: #6b56e3;" href="http://10.109.52.4/mirai/public/index/sakurentsu/monitoring/3m">&nbsp;&nbsp;&nbsp; Sakurentsu, 3M, Trial Request Monitoring (作連通、３M、試作依頼監視) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</center>
	</div>
</body>
</html>