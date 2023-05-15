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
			<span style="font-weight: bold; color: purple; font-size: 24px;">KENSA CERTIFICATE NON-ACTIVE REQUEST<br></span><br>
			<br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<?php if (ISSET($data['complete']) && $data['complete'] == 'complete'){ ?>
				<p style="font-size: 18px;font-weight: bold;color: green">Permintaan Anda telah disetujui oleh Staff QA.<br>Sertifikat Non-Aktif akan segera diproses.</p>
			<?php } ?>
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
								Request Date
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;text-align: left;">
								{{$data['request_date']}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;text-align: left;">
								Certificate Name
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;text-align: left;">
								{{$data['certificate_name']}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;text-align: left;">
								Reason
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;text-align: left;">
								{{$data['reason']}}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 60%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="background-color: rgb(126,86,134);color: white;border:1px solid black;font-size: 15px;width: 2%">Request ID</th>
							<th style="background-color: rgb(126,86,134);color: white;border:1px solid black;font-size: 15px;width: 2%">Employee ID</th>
							<th style="background-color: rgb(126,86,134);color: white;border:1px solid black;font-size: 15px;width: 5%">Name</th>
							<th style="background-color: rgb(126,86,134);color: white;border:1px solid black;font-size: 15px;width: 2%">Certificate</th>
							<th style="background-color: rgb(126,86,134);color: white;font-size: 15px;border:1px solid black;width: 2%">Dept - Sect</th>
							<th style="background-color: rgb(126,86,134);color: white;font-size: 15px;border:1px solid black;width: 5%">Group - Sub Group</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $request_id = []; ?>
						<?php for ($i=0; $i < count($data['nons']); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['nons'][$i]->request_id}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
							{{$data['nons'][$i]->employee_id}}
							</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
							{{$data['nons'][$i]->name}}
							</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
							{{$data['nons'][$i]->certificate_code}}
							</td>
							<?php $dept = ''; $sect = ''; $group = ''; $sub_group = ''; ?>
							<?php for ($j=0; $j < count($data['emp']); $j++) { 
								if ($data['nons'][$i]->employee_id == $data['emp'][$j]->employee_id) {
								 	$dept = $data['emp'][$j]->department_shortname;
								 	$sect = $data['emp'][$j]->section;
								 	$group = $data['emp'][$j]->group;
								 	$sub_group = $data['emp'][$j]->sub_group;
								}
							} ?>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">{{$dept}} - {{$sect}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">
							{{$group}} - {{$sub_group}}
							</td>
						</tr>
						<?php array_push($request_id, $data['nons'][$i]->request_id) ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div style="width: 80%">
				<br>
					<?php if (!ISSET($data['complete']) && !ISSET($data['reject'])){ ?>
						<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
						(下をクリックしてください)
						<br>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/submission/qa/certificate/non/'.$data['next_remark'].'/'.join(',',$request_id)) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/submission/qa/certificate/non/'.$data['next_remark'].'/'.join(',',$request_id)) }}">&nbsp;&nbsp;&nbsp; Reject (却下) &nbsp;&nbsp;&nbsp;</a>
					<?php } ?>
					<br><br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a style="text-decoration: underline;" href="{{ url('index/submission/qa/certificate') }}">Monitoring (監視)</a>
				<br>
			</div>
		</center>
	</div>
</body>
</html>