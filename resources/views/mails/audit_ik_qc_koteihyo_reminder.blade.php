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
			<span style="font-weight: bold; color: purple; font-size: 17px;">REMINIDER AUDIT IK LEADER DENGAN TEMUAN REVISI QC KOTEIHYO</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;">Document</th>
							<th style="font-size: 15px;border: 1px solid black;">Dept</th>
							<th style="font-size: 15px;border: 1px solid black;">Leader</th>
							<th style="font-size: 15px;border: 1px solid black;">Foreman</th>
							<th style="font-size: 15px;border: 1px solid black;">Temuan</th>
							<th style="font-size: 15px;border: 1px solid black;">Status</th>
							<th style="font-size: 15px;border: 1px solid black;">Verifikasi</th>
							<th style="font-size: 15px;border: 1px solid black;">Penanganan</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php for ($i=0; $i < count($data); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">{{$data[$i]->no_dokumen}} - {{$data[$i]->nama_dokumen}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">
								{{$data[$i]->department_shortname}}
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">
								{{$data[$i]->leader}}
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">
								{{$data[$i]->foreman}}
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;"><?php echo $data[$i]->kesesuaian_qc_kouteihyo ?></td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">
							@if($data[$i]->qa_verification == null && $data[$i]->handling_status == null)
							Belum Diverifikasi QA
							@elseif($data[$i]->qa_verification != null && $data[$i]->handling_status == null)
							Belum Ditangani QA
							@else
							Sudah Ditangani QA
							@endif
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: center;">
								@if($data[$i]->qa_verification == null)
								<a style="text-decoration: none;color: green;" href="10.109.52.4/mirai/public/index/audit_report_activity/qa_verification/approve/{{$data[$i]->id}}">Approve</a>
								<br>
								<a style="text-decoration: none;color: red;" href="10.109.52.4/mirai/public/index/audit_report_activity/qa_verification/reject/{{$data[$i]->id}}">Reject</a>
								@else
								{{explode('_',$data[$i]->qa_verification)[0]}} by {{explode('_',$data[$i]->qa_verification)[2]}}
								@endif
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: center;">
								@if($data[$i]->handling_status == null)
								<a style="text-decoration: none;color: blue;" href="10.109.52.4/mirai/public/index/audit_ik_monitoring/handling/{{$data[$i]->id}}">Input Penanganan</a>
								@else
								Sudah Ditangani QA
								@endif
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<br>
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="10.109.52.4/mirai/public/index/audit_ik_monitoring">&nbsp;&nbsp;&nbsp; Monitoring <small>監視</small> &nbsp;&nbsp;&nbsp;</a>
				<br>
				<br>
				<p>
					<b>Thanks & Regards,</b>
				</p>
				<p>PT. Yamaha Musical Products Indonesia<br>
					Jl. Rembang Industri I / 36<br>
					Kawasan Industri PIER - Pasuruan<br>
					Phone   : 0343 – 740290<br>
					Fax.    : 0343 - 740291
				</p>
			</div>
		</center>
	</div>
</body>
</html>