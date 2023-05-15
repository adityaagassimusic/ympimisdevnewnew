<!DOCTYPE html>
<html>
<head>
	<title>DETAIL PENGISIAN SAFETY CHECK</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		@page { margin: 5px; }
		body { margin: 5px; }

		td {
			padding: 3px;
		}
		.border1 {
			border: 1px solid black;
		}

		body {
			font-family: sans-serif;
		}
		thead {
			font-weight: bold;
			text-align: center;
		}
		thead>tr>td {
			background-color: rgba(210, 159, 227, 0.7);
		}

	</style>
</head>
<body>
	<center>
		<table style="width: 90%">
			<tr>
				<td>
					<center><h3 style="font-weight: bold; font-size: 20px">POINT PENGECEKAN SAFETY PT. YMPI PADA SAAT AKAN BERLIBUR</h3></center>
					<b>PIC : {{ explode('/',$data[0]->pic)[0] }} - {{ explode('/',$data[0]->pic)[1] }}</b><br>
					<b>Bagian : {{ $data[0]->location }}</b>
				</td>
				<td>
					No Dok. : YMPI/STD/FK3/040 <br>
					Rev.	: 03 <br>
					Tgl.	: 10/05/2021 <br>
				</td>
			</tr>
		</table>

		<table style="width: 90%" border="1">
			<thead>
				<tr>
					<td style="width: 1%">No</td>
					<td style="width: 60%">Poin Pengecekan</td>
					<td style="width: 5%">Sudah</td>
					<td style="width: 5%">Tidak Ada</td>
					<td style="width: 5%">Foto</td>
					<td>Keterangan</td>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; ?>
				@foreach($data as $sc)
				<?php 
				if(!$sc->remark) {
					?>
					<tr>
						<td><center>{{ $no }}</center></td>
						<td>{{ $sc->check_point }}</td>
						<td>
							<center>
								<?php 
								if ($sc->condition == 'Sudah') {
									echo '<b>V</b>';
								}
								?>
							</center>
						</td>
						<td>
							<center>
								<?php 
								if ($sc->condition == 'Tidak Ada') {
									echo '<b>V</b>';
								}
								?>
							</center>
						</td>
						<td>
							<?php 
							$file = explode(',', $sc->photo);

							for ($i=1; $i <= count($file); $i++) { 
								echo '<a href="'.url("files/safety_holiday/photo/".$file[$i-1]).'" target="_blank"><img src="'.url("files/safety_holiday/photo/".$file[$i-1]).'" style="max-width: 100px"></a><br>';
							}
							?>
						</td>
						<td>{{ $sc->note }}</td>
						<?php 
						$no++; 
					} 
					?>
					@endforeach

					<?php if ($data[0]->category == 'PRD'): ?>
						<tr>
							<td colspan="5" style="font-weight: bold; font-size: 15px;  background-color: rgba(210, 159, 227, 0.7);">Point check tambahan (disesuaikan dengan kondisi area kerja) :</td>
						</tr>
					<?php endif ?>

					<?php $no = 1; ?>
					@foreach($data as $sc)
					<?php 
					if($sc->remark == 'Additional') {
						?>
						<tr>
							<td><center>{{ $no }}</center></td>
							<td>{{ $sc->check_point }}</td>
							<td>
								<center>
									<?php 
									if ($sc->condition == 'Sudah') {
										echo '<b>V</b>';
									}
									?>
								</center>
							</td>
							<td>
								<center>
									<?php 
									if ($sc->condition == 'Tidak Ada') {
										echo '<b>V</b>';
									}
									?>
								</center>
							</td>
							<td>
								<?php 
								$file = explode(',', $sc->photo);

								for ($i=1; $i <= count($file); $i++) { 
									echo '<a href="'.url("files/safety_holiday/photo/".$file[$i-1]).'" target="_blank"><img src="'.url("files/safety_holiday/photo/".$file[$i-1]).'" style="max-width: 100px"></a><br>';
								}
								?>
							</td>
							<td>{{ $sc->note }}</td>
							<?php 
							$no++; 
						} 
						?>
						@endforeach

						<?php if ($data[0]->category == 'PRD'): ?>
							<tr>
								<td colspan="5" style="font-weight: bold; font-size: 15px;  background-color: rgba(210, 159, 227, 0.7);">Fasilitas di area kerja (harus on/standby 24 Jam) :</td>
							</tr>
						<?php endif ?>

						<?php $no = 1; ?>
						@foreach($data as $sc)
						<?php 
						if($sc->remark == 'Standby') {							
							?>
							<tr>
								<td><center>{{ $no }}</center></td>
								<td>{{ $sc->check_point }}</td>
								<td>
									<center>
										<?php 
										if ($sc->condition == 'Sudah') {
											echo '<b>V</b>';
										}
										?>
									</center>
								</td>
								<td>
									<center>
										<?php 
										if ($sc->condition == 'Tidak Ada') {
											echo '<b>V</b>';
										}
										?>
									</center>
								</td>
								<td>
									<?php 
									$file = explode(',', $sc->photo);

									for ($i=1; $i <= count($file); $i++) { 
										echo '<a href="'.url("files/safety_holiday/photo/".$file[$i-1]).'" target="_blank"><img src="'.url("files/safety_holiday/photo/".$file[$i-1]).'" style="max-width: 100px"></a><br>';
									}
									?>
								</td>
								<td>{{ $sc->note }}</td>
								<?php 
								$no++; 
							} 
							?>
							@endforeach
						</tbody>
					</table>

					<table style="font-weight: bold; width: 90%; padding: 0px">
						<tr>
							<td style="width: 70%">
								Note : 
							</td>
						</tr>
						<tr>
							<td>
								*  Isi pada kolom Keterangan jika diperlukan sebagai tambahan informasi.
							</td>
						</tr>
						<tr>
							<td>
								* 	Untuk area ruang ser<b>ver dalam kondisi on/standby 24 jam (tidak boleh dimatika</b>n)
							</td>
						</tr>
					</table>
				</center>
			</body>
			</html>