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
</style>
</head>
<body>
	<div>
		<center>
			<!-- <p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p> -->
			<span style="font-weight: bold; color: purple; font-size: 24px;">Laporan Kecelakaan Lalu Lintas YMPI</span><br>
		</center>
		<br>
		<div style="width: 90%; margin: auto;">
			<table style="width: 100%;">
				<tbody>
					<tr>
						<td style="padding: 0; vertical-align: top; text-align: right;float: right;">
							<table>
								<tbody>
									<tr>
										<td style="font-weight: bold;">No Dok.</td>
										<td style="font-weight: bold;">:</td>
										<td>YMPI/STD/FK3/049</td>
									</tr>
									<tr>
										<td style="font-weight: bold;">No Rev.</td>
										<td>:</td>
										<td>00</td>
									</tr>
									<tr>
										<td style="font-weight: bold;">Tanggal</td>
										<td style="font-weight: bold;">:</td>
										<td>10/12/2019</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table>
				<tbody>
					<tr>
						<td style="font-weight: bold;">NIK - Nama</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data[0]->employee_id }} - {{ $data[0]->employee_name }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Departemen</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data[0]->employee_department }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Bagian</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data[0]->area }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Lokasi Kejadian</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data[0]->location }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Tanggal Kejadian</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ date('d-M-Y', strtotime($data[0]->date_incident))}}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Waktu Kejadian</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ date('H:i', strtotime($data[0]->time_incident))	}} WIB</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black;background-color: #ccc;">Kronologi Kejadian</th>					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= $data[0]->detail_incident ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black;background-color: #ccc;">Poin Penting</th>					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= nl2br($data[0]->illustration_detail) ?></td>
					</tr>
				</tbody>
			</table>

			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black;background-color: #ccc;">Kondisi Korban</th>					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= $data[0]->condition ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black; width: 1%;background-color: #ccc;">Waktu Kerja Hilang</th>

						@if($data[0]->recovery_time != null)
						<th style="border:1px solid black; width: 1%;background-color: #ccc;">Waktu Sembuh</th>
						@endif

						@if($data[0]->loss_cost != null)
						<th style="border:1px solid black; width: 1%;background-color: #ccc;">Kerugian</th>
						@endif
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black;"><?= $data[0]->loss_time ?> Hari</td>

						@if($data[0]->recovery_time != null)
						<td style="border:1px solid black;"><?= $data[0]->recovery_time ?> Hari</td>
						@endif

						@if($data[0]->loss_cost != null)
						<td style="border:1px solid black;"><?= $data[0]->loss_cost ?></td>
						@endif
					</tr>
				</tbody>
			</table>
		<br>
		<table style="border: 1px solid black; border-collapse: collapse; width: 100%;" align="right">
			<?php 
				$data_image = json_decode($data[0]->illustration_image);
				$data_detail = json_decode($data[0]->illustration_detail);
				$jumlah = count($data_image);
			?>
			<thead>
				<tr>
					<th style="border:1px solid black;background-color: #ccc;text-align: center" colspan="{{$jumlah}}">
						Illustrasi Kejadian
					</th>
				</tr>
				<tr>
					<?php
                 		for ($i = 0; $i < $jumlah; $i++) { ?>
                        <td style="border:1px solid black;vertical-align: middle;text-align: center;">
                        	<br>
                        	<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('files/kecelakaan/kecelakaan_lalu_lintas/'.$data_image[$i] )))}}" width="700"> 
                        	<br><?= $data_detail[$i] ?>
                        </td>
                  <?php } ?>      
				</tr>
			</thead>
		</table>

		<br><br>
		<center>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/kecelakaan/sosialisasi/'.$data[0]->id) }}">Tap ID Card Sosialiasi Kehadiran</a>

			<br><br>

			<span style="font-size: 20px">Best Regards,</span>
			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
		</center>
		</div>
	</div>
	</body>
</html>