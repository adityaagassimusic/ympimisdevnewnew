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
			<span style="font-weight: bold; color: purple; font-size: 24px;">QA KENSA CERTIFICATE INPROCESS APPROVAL<br>品質保証検査認定承認</span><br>
			<?php if (ISSET($data[0]['complete']) && $data[0]['complete'] == 'complete'): ?>
				<p style="font-size: 18px;font-weight: bold;color: green">This Certificate has been Fully Approved.</p>
			<?php endif ?>
			<?php if (ISSET($data[0]['reject']) && $data[0]['reject'] == 'reject'): ?>
				<p style="font-size: 18px;font-weight: bold;color: red">This Certificate has been Rejected by<br>
				<?php if (ISSET($data[0]['reject_by'])): ?>
					{{$data[0]['reject_by']->approver_name}}
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
			<?php $certificate_id = []; ?>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;width: 3%;border:1px solid black;">Certificate No.</th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Certificate Name</th>
							<th style="font-size: 15px;width: 5%;border:1px solid black;">Employee</th>
							<th style="font-size: 15px;width: 5%;border:1px solid black;">Category</th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Result</th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Decision</th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Preview</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<?php $result = explode(',',$data[$i]['datas_nilai'][0]->result) ?>
						<tr>
							<td rowspan="{{count($result)}}" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas']->certificate_code}}</td>
							<td rowspan="{{count($result)}}" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas']->certificate_name}}</td>
							<td rowspan="{{count($result)}}" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas']->employee_id}} - {{$data[$i]['datas']->name}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{explode('_',$result[0])[0]}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{explode('_',$result[0])[1]}}</td>
							<td rowspan="{{count($result)}}" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas_nilai'][0]->decision}}</td>
							<td rowspan="{{count($result)}}" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
								<a style="text-decoration: none;color: blue;" href="{{ url('print/qa/certificate/inprocess/'.$data[$i]['datas']->certificate_id) }}">Preview</a>
							</td>
							<?php array_push($certificate_id, $data[$i]['datas']->certificate_id) ?>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{explode('_',$result[1])[0]}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{explode('_',$result[1])[1]}}</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					@if(!ISSET($data[0]['complete']) && !ISSET($data[0]['reject']))
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
					(下をクリックしてください)<br>
					<?php if ($data[0]['next_remark'] == 'Staff QA') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/qa/certificate/inprocess/'.$data[0]['next_remark']) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/qa/certificate/inprocess/'.$data[0]['next_remark'].'/'.join(',',$certificate_id)) }}">&nbsp;&nbsp;&nbsp; Reject &nbsp;&nbsp;&nbsp;</a>
					<?php }else{ ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval_all/qa/certificate/inprocess/'.$data[0]['next_remark'].'/'.join(',',$certificate_id)) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/qa/certificate/inprocess/'.$data[0]['next_remark'].'/'.join(',',$certificate_id)) }}">&nbsp;&nbsp;&nbsp; Reject &nbsp;&nbsp;&nbsp;</a>
					<?php } ?>
					<br>
					<br>
					@endif
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a style="width: 50px;text-decoration: underline;" href="{{ url('index/qa/certificate/code/inprocess') }}">Monitoring</a>
				<br>
			</div>
		</center>
	</div>
</body>
</html>