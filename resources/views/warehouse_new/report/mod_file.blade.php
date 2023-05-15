<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		td {
			padding: 1px;
		}
		.border1 {
			border: 1px solid black;
		}
		body{
			font-size: 10px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		* {
			font-family: arial;
		}
		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		.page-break {
			page-break-after: always;
		}

		@page { }
		.footer { position: fixed; left: 0px; bottom: 100px; right: 0px; height: 130px;text-align: center;}
		.footer .pagenum:before { content: counter(page); }

	</style>
</head>
<body>

	<?php if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "OP-WH-Exim" || Auth::user()->role_code == "F-SPL" || Auth::user()->role_code == "F") { ?>
		<div>
			<center>

				<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left";>
					<tr>
						<td colspan="12" style="font-weight: bold;font-size: 11px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>

					</tr>
					<tr>
						<td colspan="12"><br></td>
					</tr>	
					<tr>
						<td width="100%" style="font-size: 24px; text-align: center;"  colspan="12">
							<b>MATERIAL OUT DELIVERY REPORT</b>
						</td>

					</tr>

					<tr>
						<td colspan="12"><br></td>
					</tr>
					<tr>
						<td colspan="12" >
							<table style="width: 100%;">
								<?php									 
								foreach($ket as $heads) {
									?>
									<tr>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">No. Slip</td>
										<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->loc }}-{{ $heads->kode_request }}</td>
										<td colspan="3"><br></td>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Shift</td>
										@if($heads->shift == "Shift_1" || $heads->shift == "Shift_1_Genba" )
										<td colspan="2" style="font-size: 14px;  font-weight: bold;">: 1</td>
										@else
										<td colspan="2" style="font-size: 14px;  font-weight: bold;">: 2</td>
										@endif
									</tr>
									<tr>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Tanggal</td>
										<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->tanggal }}</td>
										<td colspan="3" style="text-align: center;">
											<img width="100" src="{{ public_path() . '/asli.jpg' }}" alt="" style="padding: 0; top: 55px;left: 400px; position: center;">
										</td>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">From Loc</td>
										<td colspan="2" style="font-size: 14px;  font-weight: bold;">: MSTK</td>
									</tr>
									<tr>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">PIC WH</td>
										<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->names }}</td>
										<td colspan="3"><br></td>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">To Loc</td>
										<td colspan="2" style="font-size: 14px;  font-weight: bold;">: {{ $heads->loc }}</td>
									</tr>
									<tr>
										<td colspan="12"><br></td>
									</tr>	
									<tr>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">PIC Prod</td>
										<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->name_wh }}</td>
										<td colspan="3"><br></td>
										<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Sloc Name</td>
										<td colspan="2" style="font-size: 14px;  font-weight: bold;">: {{ $heads->sloc_name }}</td>
									</tr>
								<?php  } ?>
							</table>
						</td>
						<tr>
							<td colspan="12"><br></td>
						</tr>	
					</tr>	

					<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
						<thead>
							<tr style="font-size: 12px">

								<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
								<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">GMC</td>
								<td colspan="1" style="width:8%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Description</td>
								<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Uom</td>
								<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty Kirim</td>
								<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty Terima</td>
								<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No Hako</td>
								<td colspan="1" style="width:5%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Check</td>
							</tr>
						</thead>
						<tbody>
							<?php $no = 1; 
							foreach($datas as $cek_gmc) {
								?>

								<tr style="font-size: 12px">
									<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
									<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->gmc }} </td>
									<td colspan="1" style="border: 1px solid black;padding-right: 5px">{{ $cek_gmc->description }}</td>
									<td colspan="1" style="border: 1px solid black;">PC</td>
									<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->qty_kirim }}</td>
									<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->qty_kirim }}</td>
									<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->no_hako }}</td>
									<td colspan="1" style="border: 1px solid black; text-align: center">
										<img width="10" src="{{ public_path() . '/Check.png' }}" alt="" style=" top: 55px;position: center;">	
									</td>
								</tr>
								<?php $no++; } ?>

							</tbody>

						</table>




					</table>
					<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
						<tbody>
							<?php $nos = 1; 
							foreach($pt as $pts) {
								?>
								<tr>
									<td colspan="10"><br></td>
									<td colspan="1" style="font-size: 15px; width: 13%; font-weight: bold;">Total Item :</td>
									<td colspan="1" style="font-size: 15px; border: 1px solid black; width: 13.1%; text-align: center; font-weight: bold;">{{ $pts->total }}</td>
								</tr>
								<?php $nos++; } ?>


							</tbody>
						</table>


					</center>
				</div>
				<div class="page-break"></div>
				<div>
					<center>

						<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left";>
							<tr>
								<td colspan="12" style="font-weight: bold;font-size: 11px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>

							</tr>
							<tr>
								<td colspan="12"><br></td>
							</tr>	
							<tr>
								<td width="100%" style="font-size: 24px; text-align: center;"  colspan="12">
									<b>MATERIAL OUT DELIVERY RESUME</b>
								</td>

							</tr>

							<tr>
								<td colspan="12"><br></td>
							</tr>
							<tr>
								<td colspan="12" >
									<table style="width: 100%;">

										<?php									 
										foreach($ket as $heads) {
											?>
											<tr>
												<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">No. Slip</td>
												<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->loc }}-{{ $heads->kode_request }}</td>
												<td colspan="3"><br></td>
												<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Lokasi</td>
												<td colspan="2" style="font-size: 14px;  font-weight: bold;">: {{ $heads->loc }}</td>
											</tr>
											<tr>
												<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Tanggal</td>
												<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->tanggal }}</td>
												<td colspan="3" style="text-align: center;">
													<img width="100" src="{{ public_path() . '/RESUME.jpg' }}" alt="" style="padding: 0; top: 55px;left: 400px; position: center;">
												</td>
												<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Rcvg Loc</td>
												<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->sloc_name }}</td>
											</tr>
											<tr>
												<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Inputor</td>
												<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->names }}</td>

											</tr>

										<?php  } ?>
									</table>
								</td>
								<tr>
									<td colspan="12"><br></td>
								</tr>	
							</tr>	

							<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
								<thead>
									<tr style="font-size: 12px">

										<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
										<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">GMC</td>
										<td colspan="1" style="width:8%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Description</td>			
										<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Quantity</td>
									</tr>
								</thead>
								<tbody>
									<?php $no = 1; 
									foreach($resume as $res) {
										?>

										<tr style="font-size: 12px">
											<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
											<td colspan="1" style="border: 1px solid black;">{{ $res->gmc }} </td>
											<td colspan="1" style="border: 1px solid black;padding-right: 5px">{{ $res->description }}</td>
											<td colspan="1" style="border: 1px solid black;">{{ $res->qty_kirim }}</td>

										</tr>
										<?php $no++; } ?>

									</tbody>
								</table>
							</table>
						</center>
					</div>
					<div class="page-break"></div>
				<?php } ?>

					<div>
						<center>

							
							<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left";>
								<tr>
									<td colspan="12" style="font-weight: bold;font-size: 11px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>

								</tr>
								<tr>
									<td colspan="12"><br></td>
								</tr>	
								<tr>
									<td width="100%" style="font-size: 24px; text-align: center;"  colspan="12">
										<b>MATERIAL OUT DELIVERY REPORT</b>
									</td>
								</tr>
								<tr>
									<td colspan="12"><br></td>
								</tr>

								<tr>
									<td colspan="12" >
										<table style="width: 100%;">
											<?php 
											foreach($ket as $heads) {
												?>
												<tr>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">No. Slip</td>
													<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->loc }}-{{ $heads->kode_request }}</td>
													<td colspan="3"><br></td>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Shift</td>
													@if($heads->shift == "Shift_1" || $heads->shift == "Shift_1_Genba" )
													<td colspan="2" style="font-size: 14px;  font-weight: bold;">: 1</td>
													@else
													<td colspan="2" style="font-size: 14px;  font-weight: bold;">: 2</td>
													@endif
												</tr>
												<tr>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Tanggal</td>
													<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->tanggal }}</td>
													<td colspan="3" style="text-align: center;">
														<img width="100" src="{{ public_path() . '/copy.png' }}" alt="" style="padding: 0; top: 55px;left: 400px; position: center;">
													</td>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">From Loc</td>
													<td colspan="2" style="font-size: 14px;  font-weight: bold;">: MSTK</td>
												</tr>
												<tr>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">PIC WH</td>
													<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->names }}</td>
													<td colspan="3"><br></td>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">To Loc</td>
													<td colspan="2" style="font-size: 14px;  font-weight: bold;">: {{ $heads->loc }}</td>
												</tr>
												<tr>
													<td colspan="12"><br></td>
												</tr>	
												<tr>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">PIC Prod</td>
													<td colspan="3" style="font-size: 14px;  font-weight: bold;">: {{ $heads->name_wh }}</td>
													<td colspan="3"><br></td>
													<td colspan="2" style="font-size: 14px; width: 22%; font-weight: bold;">Sloc Name</td>
													<td colspan="2" style="font-size: 14px;  font-weight: bold;">: {{ $heads->sloc_name }}</td>
												</tr>
											<?php  } ?>
										</table>
									</td>
									<tr>
										<td colspan="12"><br></td>
									</tr>	
								</tr>	

								<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
									<thead>
										<tr style="font-size: 12px">

											<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
											<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">GMC</td>
											<td colspan="1" style="width:8%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Description</td>
											<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Uom</td>
											<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty Kirim</td>
											<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty Terima</td>
											<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No Hako</td>
											<td colspan="1" style="width:5%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Check</td>

										</tr>
									</thead>
									<tbody>
										<?php $no = 1; 
										foreach($datas as $cek_gmc) {
											?>

											<tr style="font-size: 12px">

												<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
												<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->gmc }} </td>
												<td colspan="1" style="border: 1px solid black;padding-right: 5px">{{ $cek_gmc->description }}</td>
												<td colspan="1" style="border: 1px solid black;">PC</td>
												<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->qty_kirim }}</td>
												<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->qty_kirim }}</td>
												<td colspan="1" style="border: 1px solid black;">{{ $cek_gmc->no_hako }}</td>
												<td colspan="1" style="border: 1px solid black; text-align: center">
													<img width="10" src="{{ public_path() . '/Check.png' }}" alt="" style=" top: 55px;position: center;">	
												</td>
											</tr>
											<?php $no++; } ?>

										</tbody>

									</table>


								</table>
								<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
									<tbody>
										<?php $nos = 1; 
										foreach($pt as $pts) {
											?>
											<tr>
												<td colspan="10"><br></td>
												<td colspan="1" style="font-size: 15px; width: 13%; font-weight: bold;">Total Item :</td>
												<td colspan="1" style="font-size: 15px; border: 1px solid black; width: 13.1%; text-align: center; font-weight: bold;">{{ $pts->total }}</td>
											</tr>
											<?php $nos++; } ?>


										</tbody>
									</table>


								</center>
							</div>


					</body>
					</html>