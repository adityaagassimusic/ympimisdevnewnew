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
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: purple; font-size: 24px;">TRANSLATION REQUEST</span><br>
			<span style="font-weight: bold; font-size: 26px;">{{ $data['translation']->translation_id }}</span>
			<span style="font-weight: bold; font-size: 26px; color: green;">
			</span>
			<br>
			<span style="font-weight: bold;">ID:</span><br>
			<span>{{ $data['translation']->requester_id }}</span><br>
			<span style="font-weight: bold;">Name:</span><br>
			<span>{{ $data['translation']->requester_name }}</span><br>
			<span style="font-weight: bold;">Department:</span><br>
			<span>{{ $data['translation']->department_name }}</span><br>
			<br>
			<span style="font-weight: bold;">Category:</span><br>
			<span>{{ $data['translation']->document_type }}</span><br>
			<span style="font-weight: bold;">Due Date:</span><br>
			<span>{{ date('d F Y', strtotime($data['translation']->request_date)) }}</span><br>
			<span style="font-weight: bold;">Number of Pages:</span><br>
			<span>{{ $data['translation']->number_page }} Page(s)</span><br>
			<span style="font-weight: bold;">Number of Attachments:</span><br>
			<span>{{ count($data['filenames']) }} Att(s)</span><br>
			<span style="font-weight: bold;">Note:</span><br>
			<span>{{ $data['translation']->remark }}</span><br>
			<span style="font-weight: bold;">Text to Translate:</span><br>
			<span><?= $data['translation']->translation_request ?></span><br>
			<br>
			<br>
			Silahkan menentukan PIC untuk menerjemahkan permintaan.
			<br>
			<table style="width: 30%">
				<tr>
					<th style="width: 1%; font-weight: bold; color: black;">
						<a style="background-color: #ccff90; text-decoration: none; color: black;" href="{{ url('approval/translation?status=Approved&translation_id='.$data['translation']->translation_id) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Assign PIC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
					</th>
					<th style="width: 1%;">
						&nbsp;&nbsp;&nbsp;
					</th>
					<th style="width: 1%; font-weight: bold; color: black;">
						<a style="background-color: #ff6090; text-decoration: none; color: black;" href="{{ url('approval/translation?status=Rejected&translation_id='.$data['translation']->translation_id) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reject&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
					</th>
				</tr>
			</table>
		</center>
	</div>
</body>
</html>