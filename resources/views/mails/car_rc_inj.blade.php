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
		.button_blue {
		  background-color: #4c4faf; /* Blue */
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
			<span style="font-weight: bold; color: purple; font-size: 24px;">CAR Audit QA Recorder Injection</span><br>
			<?php if (ISSET($data['reason'])): ?>
				<span style="font-weight: bold;color: red;font-size: 20px">
					CAR Anda telah ditolak oleh Manager.
				</span>
				<br>
				<span style="color: red">
					Reason:<br>
					<?php echo $data['reason'] ?>
				</span>
			<?php endif ?>

			<?php if (ISSET($data['edit'])): ?>
				<span style="font-weight: bold;font-size: 20px">
					CAR ini telah diubah. Silahkan cek kembali.
				</span>
			<?php endif ?>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>			
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="60%">
					<tbody>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Item</td>
							<td style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Detail</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">Product</td>
							<td style="border:1px solid black;">{{$data['data_email_inj']->product}} - {{$data['data_email_inj']->part_name}}</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Auditor
							</td>
							<td style="border:1px solid black;">
								{{$data['data_email_inj']->auditor}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								PIC Assy
							</td>
							<td style="border:1px solid black;">
								{{$data['data_email_inj']->auditee}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								PIC Injeksi
							</td>
							<td style="border:1px solid black;">
								{{$data['data_email_inj']->pic_injection}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Defect
							</td>
							<td style="border:1px solid black;">
								{{$data['data_email_inj']->defect}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Area
							</td>
							<td style="border:1px solid black;">
								{{$data['data_email_inj']->area}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Category
							</td>
							<td style="border:1px solid black;">
								{{$data['data_email_inj']->category}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Evidence
							</td>
							<td style="border:1px solid black;">
								<img style="width: 150px" src="data:image/png;base64,{{base64_encode(file_get_contents('http://10.109.52.4/mirai/public/data_file/recorder/qa_audit/'.$data['data_email_inj']->image))}}" alt="">
							</td>
						</tr>
					</tbody>
				</table>
		</center>
	</div>
	<br>
	<br>
	<div>
		<center>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
				<!-- <a class="button" href="{{url('approve/recorder/qa_audit/injeksi/'.$data['remark'].'/'.$data['data_email_inj']->id)}}" style="font-weight:bold;font-size:20px">Approve</a> -->
				<a class="button_blue" href="{{url('print/recorder/qa_audit/injeksi/'.$data['data_email_inj']->id)}}" style="font-weight:bold;font-size:20px;">Hasil Training & Konseling</a>
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