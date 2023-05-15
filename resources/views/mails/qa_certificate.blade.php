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
			<span style="font-weight: bold; color: purple; font-size: 24px;">QA KENSA CERTIFICATE APPROVAL<br>品質保証検査認定承認</span><br>
			<br>
			<?php if (ISSET($data['complete']) && $data['complete'] == 'complete'): ?>
				<p style="font-size: 18px;font-weight: bold;color: green">This Certificate has been Fully Approved.</p>
			<?php endif ?>
			<?php if (ISSET($data['reject']) && $data['reject'] == 'reject'): ?>
				<p style="font-size: 18px;font-weight: bold;color: red">This Certificate has been Rejected by<br>
				<?php if (ISSET($data['reject_by'])): ?>
					{{$data['reject_by']->approver_name}}
				<?php endif ?>
				<br>
				Please inform this rejection to Leader QA.
				</p>
			<?php endif ?>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 60%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<!-- <thead style="background-color: rgb(126,86,134);color: white">
					</thead> -->
					<tbody align="center">
						<tr>
							<td colspan="2" style="border:1px solid black; font-size: 15px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Details</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;text-align: left;">
								Certificate No. <small>認定番号</small>
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;text-align: left;">
								{{$data['datas']->certificate_code}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;text-align: left;">
								Certificate Name 認定名
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;text-align: left;">
								{{$data['datas']->certificate_name}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;text-align: left;">
								Employee 従業員
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;text-align: left;">
								{{$data['datas']->employee_id}} - {{$data['datas']->name}}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<?php if (!ISSET($data['complete']) && !ISSET($data['reject'])){ ?>
						<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
						(下をクリックしてください)
						<br>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/qa/certificate/'.$data['next_remark']) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/qa/certificate/'.$data['next_remark'].'/'.$data['datas']->certificate_id) }}">&nbsp;&nbsp;&nbsp; Reject (却下) &nbsp;&nbsp;&nbsp;</a>
					<?php } ?>
					<br>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a style="text-decoration: underline;" href="{{ url('print/qa/certificate/'.$data['certificate_id']) }}">Preview Certificate (プレビュー)</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a style="text-decoration: underline;margin-left: 50px" href="{{ url('index/qa/certificate/code') }}">Monitoring (監視)</a>
				<br>
			</div>
		</center>
	</div>
</body>
</html>