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
			<span style="font-weight: bold; color: purple; font-size: 17px;">Data SIM dan STNK Expired dalam 30 Hari</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;width: 1%">#</th>
							<th style="font-size: 15px;border: 1px solid black;width: 3%;">NIK</th>
							<th style="font-size: 15px;border: 1px solid black;width: 5%;">Nama</th>
							<th style="font-size: 15px;border: 1px solid black;width: 5%;">Keterangan</th>
							<th style="font-size: 15px;border: 1px solid black;width: 5%;">Masa Berlaku</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1; ?>
						<?php for ($i=0; $i < count($data['get_all_data']); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: right;">{{$index}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{$data['get_all_data'][$i]->employee_id}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['get_all_data'][$i]->name}}</td>

							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">
							@if($data['get_all_data'][$i]->validity_sim == 30)
							SIM
							@elseif($data['get_all_data'][$i]->validity_stnk == 30)
							STNK<br>
							NOPOL {{$data['get_all_data'][$i]->nopol}}
							@elseif($data['get_all_data'][$i]->validity_stnk_2 == 30)
							STNK<br>
							NOPOL {{$data['get_all_data'][$i]->nopol_2}}
							@endif
							</td>

							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">
							@if($data['get_all_data'][$i]->validity_sim == 30)
							<?= date('d-M-y', strtotime($data['get_all_data'][$i]->date_sim)) ?>
							@elseif($data['get_all_data'][$i]->validity_stnk == 30)
							<?= date('d-M-y', strtotime($data['get_all_data'][$i]->date_stnk)) ?>
							@elseif($data['get_all_data'][$i]->validity_stnk_2 == 30)
							<?= date('d-M-y', strtotime($data['get_all_data'][$i]->date_stnk_2)) ?>
							@endif
							</td>
						</tr>
						<?php
							$index++;
						 } ?>
					</tbody>
				</table>
			</div>
		</center>
	</div>
</body>
</html>