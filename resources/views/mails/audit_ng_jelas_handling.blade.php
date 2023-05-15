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
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Penanganan Audit NG Jelas QA</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Detail Temuan</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Claim Title</th>
							<td style="border:1px solid black;">
								{{$data['audit_title']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Area</th>
							<td style="border:1px solid black;">
								{{$data['area']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Product</th>
							<td style="border:1px solid black;">
								{{$data['product']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Point Check</th>
							<td style="border:1px solid black;">
								{{$data['audit_point']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Result Check</th>
							<td style="border:1px solid black;">
								<?php if ($data['result_check'] == 'NS'){ ?>
									Observ
								<?php }else{ ?>
									{{$data['result_check']}}
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Image Evidence</th>
							<td style="border:1px solid black;">
								<?php if (str_contains($data['result_image'],',')){ ?>
			                      <?php $imagesss = explode(',', $data['result_image']) ?>
			                      <?php for ($i=0; $i < count($imagesss); $i++) { ?>
			                        <img style="width: 300px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/qa/ng_jelas/'.$imagesss[$i])))}}" alt="">
			                        <br>
			                      <?php } ?>
			                    <?php }else { ?>
			                      <img style="width: 300px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/qa/ng_jelas/'.$data['result_image'])))}}" alt="">
			                    <?php } ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Note</th>
							<td style="border:1px solid black;">
								{{$data['note']}}
							</td>
						</tr>
					</thead>
					<!-- <tbody style="text-align: center;">
					</tbody> -->
				</table>
				<br>
				<br>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Detail Penanganan</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Penanganan</th>
							<td style="border:1px solid black;">
								<?php echo $data['handling'] ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Ditangani Oleh</th>
							<td style="border:1px solid black;">
								{{$data['handled_by']}} - {{$data['handled_name']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Waktu Penanganan</th>
							<td style="border:1px solid black;">
								{{$data['handled_at']}}
							</td>
						</tr>
					</thead>
					<!-- <tbody style="text-align: center;">
					</tbody> -->
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a class="button" href="http://10.109.52.4/mirai/public/index/qa/audit_ng_jelas_monitoring">Monitoring</a>
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