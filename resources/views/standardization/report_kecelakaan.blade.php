<!DOCTYPE html>
<html>
<head>
	<title>Report Kecelakaan Yamaha Group</title>
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
	@page {
		margin: 40px 0 0 0;
	}
</style>
</head>
<body>
	<div style="width: 90%; margin: auto;">
		<header>
			<center>
				@if($data->category == "Kerja")
					<span style="font-weight: bold; color: purple; font-size: 18px;">LAPORAN KECELAKAAN KERJA YAMAHA GROUP</span><br>
				<!-- <span style="font-weight: bold; font-size: 16px;">{{ $data->id }}</span> -->
				@elseif($data->category == "Lalu Lintas")
					<span style="font-weight: bold; color: purple; font-size: 18px;">LAPORAN KECELAKAAN LALU LINTAS</span>
				@endif
			</center>
			<br>
			<table style="width: 100%;">
				<tbody>
					<tr>
						<td style="padding: 0; vertical-align: top; width: 2%;">
							<table>
								<tbody>
								</tbody>
							</table>
						</td>
						<td style="padding: 0; vertical-align: top; text-align: right; width: 1%;">
							<table>
								<tbody>
									<tr>
										<td style="font-weight: bold;">No Dok.</td>
										<td style="font-weight: bold;">:</td>
										@if($data->category == "Kerja")
										<td>YMPI/STD/FK3/051</td>
										@elseif($data->category == "Lalu Lintas")
										<td>YMPI/STD/FK3/049</td>
										@endif
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
			<table>
				<tbody>
					@if($data->category == "Lalu Lintas")
						<tr>
							<td style="font-weight: bold;">NIK - Nama</td>
							<td style="font-weight: bold;">:</td>
							<td width="100">{{ $data->employee_id }} - {{ $data->employee_name }}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Departemen</td>
							<td style="font-weight: bold;">:</td>
							<td width="100">{{ $data->employee_department }}</td>
						</tr>
					@else

					@endif

					@if($data->category == "Lalu Lintas")
						<tr>
							<td style="font-weight: bold;">Bagian</td>
							<td style="font-weight: bold;">:</td>
							<td width="100">{{ $data->area }}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Lokasi Kejadian</td>
							<td style="font-weight: bold;">:</td>
							<td width="100">{{ $data->location }}</td>
						</tr>
					@elseif($data->category == "Kerja")
						<tr>
							<td style="font-weight: bold;">Lokasi Kejadian</td>
							<td style="font-weight: bold;">:</td>
							<td width="100">{{ $data->location }}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Bagian / Area</td>
							<td style="font-weight: bold;">:</td>
							<td width="100">
								{{ $data->area }}
							</td>
						</tr>
					@endif

					
					<tr>
						<td style="font-weight: bold;">Tanggal Kejadian</td>
						<td style="font-weight: bold;">:</td>
						<td width="100">{{ date('d-M-Y', strtotime($data->date_incident))}}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Waktu Kejadian</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ date('H:i', strtotime($data->time_incident))	}} WIB</td>
					</tr>
					
					@if($data->category == "Lalu Lintas")
					<tr>
						<td style="font-weight: bold;">Kategori</td>
						<td style="font-weight: bold;">:</td>
						<td width="100">{{ $data->accident_number }}</td>
					</tr>
					@elseif($data->category == "Kerja")
					<tr>
						<td style="font-weight: bold;">Korban</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data->position }} </td>
					</tr>
					@endif
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black; width: 30%;background-color: #ccc;">Kronologi Kejadian</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= $data->detail_incident ?></td>
					</tr>
				</tbody>
			</table>
			@if($data->category == "Lalu Lintas")
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black; width: 30%;background-color: #ccc;">Poin Penting</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= nl2br($data->illustration_detail) ?></td>
					</tr>
				</tbody>
			</table>
			@endif
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%" >
				<thead>
					
					<tr>
						<th style="border:1px solid black; width: 30%;background-color: #ccc;">Kondisi Korban</th>					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= $data->condition ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black;background-color: #ccc;">
							Waktu Kerja Hilang
						</th>
						<th style="border:1px solid black;background-color: #ccc;">
							Waktu Sembuh
						</th>
						<th style="border:1px solid black;background-color: #ccc;">
							Kerugian
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black;"><?= $data->loss_time ?> Hari</td>
						<td style="border:1px solid black;"><?= $data->recovery_time ?> Hari</td>
						@if($data->loss_cost != null)
						<td style="border:1px solid black;"><?= $data->loss_cost ?></td>
						@else
						<td style="border:1px solid black;"></td>
						@endif
					</tr>
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>

					<?php 
					$data_image = json_decode($data->illustration_image);
					$data_detail = json_decode($data->illustration_detail);
					$jumlah = count($data_image);
					?>

					<tr>
						<th style="border:1px solid black;background-color: #ccc;text-align: center" colspan="{{$jumlah}}">
							Illustrasi Kejadian
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php
	                 	for ($i = 0; $i < $jumlah; $i++) { ?>

	                        <td style="border:1px solid black;vertical-align: middle;text-align: center;">
	                        	<br>
	                        	@if($data->category == "Lalu Lintas")
	                        		<img src="{{ url('files/kecelakaan/kecelakaan_lalu_lintas/'.$data_image[$i])}}" width="700"> 
	                        	@elseif($data->category == "Kerja")
		                        	<img src="{{ url('files/kecelakaan/kecelakaan_kerja/'.$data_image[$i])}}" width="200"> 
		                        	<br><center><?= $data_detail[$i] ?></center>
	                        	@endif
	                        </td>
	                  <?php } ?>              
						
					</tr>
				</tbody>
			</table>

		</header>
		
			
	</div>
</body>
</html>