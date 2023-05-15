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
		* {
			font-family: Sans-serif;
		}
		.eo_number {
			font-size: 4vw;
			font-weight: bold;
		}

		.status {
			font-size: 3vw;
			font-weight: bold;
		}

		.message {
			font-size: 2vw;
		}
		.jp{
			font-weight: normal;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: green; font-size: 24px;">Extra Order Confirmation<br>エキストラオーダー確認</span>
			<div style="width: 90%; margin: auto;">
				<p class="eo_number">{{ $data['approval']['eo_number'] }}</p>
				<p class="message">Commented By : {{ $data['approval']['approver_name'] }}</p>
				<h2 class="message">@php echo $data['approval']['comment']; @endphp</h2>

				@php
				if($data['status'] == 'answer'){
					print_r('<br>');
					print_r('<p class="message">Answer</p>');
					print_r('<h2 class="message">'. $data['approval']['answer'] .'</h2>');
				}
				@endphp
			</div>
		</center>
		<br>
		<hr>
		<center>
			@if($data['status'] == 'answer')
			Do you aggree with this EOC ?
			<br>
			<table style="width: 40%">
				<tr>				
					<th style="width: 30%; font-weight: bold; color: black;">
						<a style="background-color: #ccff90; text-decoration: none; color: black;" href="{{ url('index/extra_order/approval_eoc/?status=Approved&approval_id='.$data['approval']['id']) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Approve&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(承認)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
					</th>
					<th style="width: 5%;">
						&nbsp;
					</th>
					<th style="width: 30%; font-weight: bold; color: black;">
						<a style="background-color: #25b2fd; text-decoration: none; color: black;" href="{{ url('index/extra_order/approval_eoc/?status=Hold&approval_id='.$data['approval']['id']) }}">
							&nbsp;Hold & Comment&nbsp;<br>&nbsp;&nbsp;	(保留・コメント)&nbsp;&nbsp;
						</a>

					</th>
					<th style="width: 5%;">
						&nbsp;
					</th>
					<th style="width: 30%; font-weight: bold; color: black;">
						<a style="background-color: #ff6090; text-decoration: none; color: black;" href="{{ url('index/extra_order/approval_eoc/?status=Rejected&approval_id='.$data['approval']['id']) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reject&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;( 却下 )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
					</th>
				</tr>
			</table>
			@else
			<table style="width: 40%">
				<tr>				
					<th style="width: 30%; font-weight: bold; color: black;">
						<a style="background-color: #fe7c00; text-decoration: none; color: black;" href="{{ url('index/extra_order/approval_eoc/?status=Hold&approval_id='.$data['approval']['id']) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reply this Message&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;(このメッセージに返信する)&nbsp;</a>
					</th>
				</tr>
			</table>
			@endif
		</center>
		<br>
		<br>
		<center>
			<a href="{{ url('index/extra_order/detail/'.$data['approval']['eo_number']) }}">&#10148; Click here to check the request </a>
		</center>
		<br>
		<br>
		<br>
	</div>
</body>
</html>