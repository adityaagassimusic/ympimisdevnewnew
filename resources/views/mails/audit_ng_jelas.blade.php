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
			<span style="font-weight: bold; color: purple; font-size: 24px;">Temuan Audit NG Jelas QA</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead>
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Detail Temuan</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Claim Title</th>
							<td style="border:1px solid black;">
								{{$data['audit_title']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Area</th>
							<td style="border:1px solid black;">
								{{$data['area']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Product</th>
							<td style="border:1px solid black;">
								{{$data['product']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Point Check</th>
							<td style="border:1px solid black;">
								<?php echo $data['audit_point'] ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Result Check</th>
							<td style="border:1px solid black;">
								<?php if ($data['result_check'] == 'NS'){ ?>
									Observ
								<?php }else{ ?>
									{{$data['result_check']}}
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Image Evidence</th>
							<td style="border:1px solid black;">
								@if($data['remark'] == 'cpar_car')
								@if($data['result_image'] != null)
								<?php if (str_contains($data['result_image'],',')){ ?>
			                      <?php $imagesss = explode(',', $data['result_image']) ?>
			                      <?php for ($i=0; $i < count($imagesss); $i++) { ?>
			                        <img style="width: 100px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/qa/cpar_car/'.$imagesss[$i])))}}" alt="">
			                        <br>
			                      <?php } ?>
			                    <?php }else { ?>
			                      <img style="width: 100px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/qa/cpar_car/'.$data['result_image'])))}}" alt="">
			                    <?php } ?>
								@endif
								@else
								@if($data['result_image'] != null)
								<?php if (str_contains($data['result_image'],',')){ ?>
			                      <?php $imagesss = explode(',', $data['result_image']) ?>
			                      <?php for ($i=0; $i < count($imagesss); $i++) { ?>
			                        <img style="width: 100px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/qa/ng_jelas/'.$imagesss[$i])))}}" alt="">
			                        <br>
			                      <?php } ?>
			                    <?php }else { ?>
			                      <img style="width: 100px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/qa/ng_jelas/'.$data['result_image'])))}}" alt="">
			                    <?php } ?>
								@endif
								@endif
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align:left;">Note</th>
							<td style="border:1px solid black;">
								<?php echo $data['note'] ?>
							</td>
						</tr>
					</thead>
					<!-- <tbody style="text-align: center;">
					</tbody> -->
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
				@if($data['remark'] == 'cpar_car')
				<a class="button" href="http://10.109.52.4/mirai/public/index/qa/cpar_car/handling/{{$data['schedule_id']}}">Input Penanganan</a><br>
				<br>
				<a href="http://10.109.52.4/mirai/public/index/qa/cpar_car">Monitoring</a>
				@else
				<a class="button" href="http://10.109.52.4/mirai/public/index/qa/audit_ng_jelas/handling/{{$data['schedule_id']}}">Input Penanganan</a><br>
				<br>
				<a href="http://10.109.52.4/mirai/public/index/qa/audit_ng_jelas_monitoring">Monitoring</a>
				@endif
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