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
			<span style="font-weight: bold; color: purple; font-size: 17px;">REMINDER PENANGANAN AUDIT PACKING QA<br>梱包監査</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border:1px solid black;">#</th>
							<th style="font-size: 15px;border:1px solid black;">Date</th>
							<th style="font-size: 15px;border:1px solid black;">Product</th>
							<th style="font-size: 15px;border:1px solid black;">Material</th>
							<th style="font-size: 15px;border:1px solid black;">Material Audited</th>
							<th style="font-size: 15px;border:1px solid black;">Serial Number</th>
							<th style="font-size: 15px;border:1px solid black;">Result</th>
							<th style="font-size: 15px;border:1px solid black;">Auditor</th>
							<th style="font-size: 15px;border:1px solid black;">Auditee</th>
							<th style="font-size: 15px;border:1px solid black;">Due Date</th>
							<th style="font-size: 15px;border:1px solid black;">Action</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1; ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: right;">{{($index)}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;">{{$data[$i]->date_audit_name}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data[$i]->product}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">
								<?php $material_number = explode(',',$data[$i]->material_number) ?>
				                <?php $material_description = explode(',',$data[$i]->material_description) ?>
				                <?php $material = []; ?>
				                <?php for($j = 0; $j < count($material_number);$j++){
				                  array_push($material,$material_number[$j].' - '.$material_description[$j]);
				                }
				                echo join('<br>',$material); ?>
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data[$i]->material_audited}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data[$i]->serial_number}}</td>
							@if(str_contains($data[$i]->result_check,'NG'))
							<td style="text-align:center;background-color:#ff8f8f;border:1px solid black; font-size: 15px;width: 2%">NG</td>
							@else
							<td style="text-align:center;background-color:#a2ff8f;border:1px solid black; font-size: 15px;width: 2%">OK</td>
							@endif
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data[$i]->auditor_id}} - {{$data[$i]->auditor_name}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data[$i]->auditee_id}} - {{$data[$i]->auditee_name}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data[$i]->due_date}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: center;">
								<a style="text-decoration: none;color: blue;" href="http://10.109.52.4/mirai/public/index/qa/packing/handling/{{$data[$i]->audit_id}}">Input Penanganan</a>
							</td>
						</tr>
						<?php $index++; ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<br>
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="http://10.109.52.4/mirai/public/index/qa/packing">&nbsp;&nbsp;&nbsp; Monitoring <small>監視</small> &nbsp;&nbsp;&nbsp;</a>
				<br>
				<br>
			</div>
		</center>
	</div>
</body>
</html>