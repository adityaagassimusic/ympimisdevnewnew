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
		/*.button {
		  background-color: #4CAF50;
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
		}*/
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Temuan Audit QC Koteihyo (QC工程表 監査)</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Detail Temuan</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Document</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->document_number}} - {{$data->title}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Area</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php echo $data->area ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Proses</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php echo $data->process ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Object Audit</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php echo $data->document_number_finding ?> - <?php echo $data->document_name_finding ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Hasil</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php if ($data->condition == 'NS'){ ?>
									&#8420; &nbsp;(Need Observation)
								<?php }else if($data->condition == 'NG'){ ?>
									&#9747; &nbsp;(Not Good)
								<?php }else{ ?>
									&#9711; 
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Temuan</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php echo $data->finding ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Evidence</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								<?php echo $data->evidence ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left: 10px">Due Date</th>
							<td style="border:1px solid black;text-align: left;">
								<?php echo $data->due_date ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left: 10px">Employee</th>
							<td style="border:1px solid black;text-align: left;">
								<?php echo $data->employee_id ?> - <?php echo $data->employee_name ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left: 10px">File Temuan</th>
							<td style="border:1px solid black;text-align: left;">
								<a class="btn btn-danger btn-xs" target="_blank" href='{{url("pdf/qa/qc_koteihyo/")}}/{{$data->schedule_id}}'>File PDF</a>
							</td>
						</tr>
						
					</thead>
					<!-- <tbody style="text-align: center;">
					</tbody> -->
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a href="http://10.109.52.4/mirai/public/handling/qa/qc_koteihyo/{{$data->schedule_id}}/{{$data->employee_id}}">Input Penanganan</a><br>
					<br>
					<a href="http://10.109.52.4/mirai/public/index/qa/qc_koteihyo">Monitoring</a>
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
		</center>
	</div>
</body>
</html>