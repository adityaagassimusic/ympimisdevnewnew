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
			<span style="font-weight: bold; color: purple; font-size: 17px;">LAPORAN AUDIT IK - {{strtoupper($data['category'])}}</span><br>
			@if($data['reason'] != null)
			<span style="font-weight: bold;">Reason : </span><br>
			<span style="color: red;font-weight: bold;"><?php echo $data['reason'] ?></span>
			@endif
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;">Document IK</th>
							@if($data['category'] == 'Revisi QC Kouteihyo')
							<th style="font-size: 15px;border: 1px solid black;">Document QC Koteihyo</th>
							@endif
							<th style="font-size: 15px;border: 1px solid black;">Dept</th>
							<th style="font-size: 15px;border: 1px solid black;">Leader</th>
							<th style="font-size: 15px;border: 1px solid black;">Foreman</th>
							<th style="font-size: 15px;border: 1px solid black;">Audit Date</th>
							<th style="font-size: 15px;border: 1px solid black;">Hasil Audit</th>
						</tr>
					</thead>
					<tbody align="center">
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['data']->no_dokumen}} - {{$data['data']->nama_dokumen}}</td>
							@if($data['category'] =='Revisi QC Kouteihyo' || $data['category'] =='Revisi QC Koteihyo')
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">
								@if($data['data']->result_qc_koteihyo != null && count(explode('_',$data['data']->result_qc_koteihyo)) > 1)
								{{explode('_',$data['data']->result_qc_koteihyo)[1]}} - {{explode('_',$data['data']->result_qc_koteihyo)[2]}}
								@endif
							</td>
							@endif
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: left;">{{$data['data']->department_shortname}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: left;">{{$data['data']->leader}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: left;">{{$data['data']->foreman}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: right;">{{$data['data']->date}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: left;">{{$data['category']}}</td>
						</tr>
						@if($data['category'] == 'Revisi QC Kouteihyo')
						<tr>
						<th style="font-size: 15px;border: 1px solid black;background-color: rgb(126,86,134);color: white;text-align: center;" colspan="7">Detail Ketidaksesuaian</th>
						</tr>
						@endif
						@if($data['category'] =='Revisi QC Kouteihyo' || $data['category'] =='Revisi QC Koteihyo')
						<tr>
							<td style="text-align: center;">
							<?php echo $data['data']->kesesuaian_qc_kouteihyo ?>
							</td>
						</tr>
						@endif
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					@if($data['category'] == 'Revisi QC Kouteihyo')
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br>
					<br>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="http://10.109.52.4/mirai/public/index/audit_report_activity/qa_verification/approve/{{$data['data']->id}}">&nbsp;&nbsp;&nbsp; Verifikasi &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="http://10.109.52.4/mirai/public/index/audit_report_activity/qa_verification/reject/{{$data['data']->id}}">&nbsp;&nbsp;&nbsp; Reject &nbsp;&nbsp;&nbsp;</a>
					@endif
					<br>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="http://10.109.52.4/mirai/public/index/audit_ik_monitoring">&nbsp;&nbsp;&nbsp; Audit IK Monitoring &nbsp;&nbsp;&nbsp;</a>
					<br>
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="http://10.109.52.4/mirai/public/index/standardization/document_index">&nbsp;&nbsp;&nbsp; IK DM DL Control &nbsp;&nbsp;&nbsp;</a>
					<br>
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="http://10.109.52.4/mirai/public/index/audit_report_activity/print_audit_report/{{$data['data']->activity_list_id}}/{{$data['audit_guidance']->month}}">&nbsp;&nbsp;&nbsp; Report PDF &nbsp;&nbsp;&nbsp;</a>
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