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
			<?php $pattern = "/Final/i";  ?>
			<span style="font-weight: bold; color: purple; font-size: 24px;">SMALL GROUP ACTIVITY (SGA) REJECTION<br>??</span><br>
			@if(ISSET($data['finish']))
			<p style="font-size: 18px;font-weight: bold;color: green">Pengajuan Seleksi SGA Telah Disetujui.</p>
			@endif
			@if(ISSET($data['reject']))
			<p style="font-size: 18px;font-weight: bold;color: red">Pengajuan Seleksi SGA Tidak Disetujui.</p>
			<p style="font-size: 18px;font-weight: bold;color: red">Reason<br>{{$data['reason']}}</p>
			@endif
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;width: 1%">Juara</th>
							<th style="font-size: 15px;width: 2%">Team No.</th>
							<th style="font-size: 15px;width: 4%">Bagian</th>
							<!-- <th style="font-size: 15px;width: 2%">Hasil Penilaian Seleksi</th>
							<th style="font-size: 15px;width: 2%">Hasil Penilaian Final</th>
							<th style="font-size: 15px;width: 2%">40% Seleksi</th>
							<th style="font-size: 15px;width: 2%">60% Final</th>
							<th style="font-size: 15px;width: 2%">Total Penilaian</th> -->
							<th style="font-size: 15px;width: 2%">Hadiah</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1 ?>
						<?php $total_all = 0; ?>
						<?php for($i = 0; $i < count($data['teams']);$i++){ ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$index}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_no}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_name}}</td>
							<!-- <td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->total_nilai_seleksi}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->total_nilai_final}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;"><?php 
								$seleksi = (40/100)*$data['teams'][$i]->total_nilai_seleksi;
								echo $seleksi;
							 ?></td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;"><?php 
								$final = (60/100)*$data['teams'][$i]->total_nilai_final;
								echo $final;
							 ?></td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;"><?php 
								$total = $seleksi+$final;
								echo number_format($total,1);
							 ?></td> -->
							 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;"><span style="float: left">Rp.</span><span style="float: right">{{$data['teams'][$i]->hadiah}},00</span></td>
							 <?php $hadiah_new = str_replace(',', '', $data['teams'][$i]->hadiah);
							 $total_all = $total_all + $hadiah_new; ?>
						</tr>
						<?php $index++; ?>
						<?php } ?>
						<tr>
							<td colspan="3" style="border:1px solid black; font-size: 15px; height: 20;text-align: right;font-weight: bold;">TOTAL</td>
							<td colspan="" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;font-weight: bold;"><span style="float: left">Rp.</span><span style="float: right"><?php echo number_format($total_all,2,",",".") ?></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					
					<!-- <br>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br> -->
					<!-- <a style="width: 50px;text-decoration: underline;" href="{{ url('index/qa/certificate/code') }}">Monitoring 監視</a> -->
				<br>
			</div>
		</center>
	</div>
</body>
</html>