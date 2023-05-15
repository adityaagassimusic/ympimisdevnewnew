<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 13px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		.line{
			width: 100%; 
			text-align: center; 
			border-bottom: 1px solid #000; 
			line-height: 0.1em;
			margin: 10px 0 20px;  
		}

		.line span{
			background:#fff; 
			padding:0 10px;
		}
		.page-break {
			page-break-after: always;
		}

		@page { }
		.footer { position: fixed; left: 0px;bottom: -130px; right: 0px; height: 200px;text-align: center;}
		.footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
			<thead>
				<tr>
					<td colspan="2" rowspan="5" class="centera" style="padding : 0" width="30%">
						<img width="150" src="{{ public_path() . '/waves2.jpg' }}" alt="" style="padding: 0">
					</td>
					<td colspan="8" style="font-weight: bold;font-size: 18px">MANAGEMENT INFORMATION SYSTEM DEPARTMENT</td>
				</tr>
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="4" style="text-align: left;font-size: 12px"></td>
					<td colspan="2" style="font-size: 12px;">Nomor Tanda Terima</td>
					<td colspan="3" style="font-size: 12px;"><b>: {{ $datas[0]->checklist_id }}</b></td>

				</tr>
				<tr>
					<td colspan="4" style="text-align: left;font-size: 12px"></td>
					<td colspan="2" style="text-align: left;font-size: 12px">Tanggal Terima</td>
					<td colspan="3" style="text-align: left;font-size: 12px"><b>: <?= date('d-M-y', strtotime($datas[0]->receive_date)) ?></b></td>

				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 12px"></td>
					<td colspan="2" style="text-align: left;font-size: 12px">Lokasi</td>
					<td colspan="3" style="text-align: left;font-size: 12px"><b>: {{ $datas2->location }}</b></td>

				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 12px"></td>
					<td colspan="2" style="text-align: left;font-size: 12px">Peruntukan</td>
					<td colspan="3" style="text-align: left;font-size: 14px"><b>: {{ $datas2->peruntukan }}</b></td>

				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>
				
				<tr>
					<td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic;padding-bottom:5px;">
						<div class="line">
							<span>
								TANDA TERIMA
							</span>
							<div>
							</td>
						</tr>

					</thead>
				</table>
			</header>
			<main>
				<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse;" id="isi">
					<thead>
						<tr style="font-size: 12px">
							<td colspan="1" style="padding:10px;height: 15px; width:0.5%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
							<td colspan="1" style="width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No Po</td>
							<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Nama Item</td>
							<td colspan="1" style="width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No. Seri</td>
							<td colspan="1" style="width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty</td>
							<td colspan="1" style="width:2%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Note</td>
							<td colspan="1" style="width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Status</td>

						</tr>
					</thead>
					<tbody>
						<?php $no = 1; 
						$total = 0;
						$total_service = 0;
						?>
						@foreach($datas as $audit)
						<tr>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">{{ $no }}</td>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">{{ $audit->no_po }}</td>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">{{ $audit->nama_item }}</td>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">{{ $audit->no_seri }}</td>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">{{ $audit->qty }}</td>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">{{ $audit->note }}</td>
							<td colspan="1" style="padding:5px; border: 1px solid black;text-align: center;">Diterima</td>

						</tr>
						{{ $no++ }}
						@endforeach
					</tbody>
				</table>	
			</main>

			<div style="padding-top:60px !important">

				<div class="footer">
					<table style="border: 1px solid black; border-collapse: collapse; width: 40%;" align="right">
						<thead align="center">
							<tr>
								<?php
									print_r('<th style="border: 1px solid black; width: 1%;">Penerima</th>');
									print_r('<th style="border: 1px solid black; width: 1%;">PIC MIS</th>');
								?>
							</tr>
							<tr>
								<?php
									print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$datas2->updated_at.'</th>');
									print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$datas2->updated_at.'</th>');

								?>
								
							</tr>
							<tr>
								<?php
									print_r('<th style="border: 1px solid black; width: 1%;">'.$datas2->pic_pengambil_name.'</th>');
									print_r('<th style="border: 1px solid black; width: 1%;">'.$datas2->pic_mis_name.'</th>');
								
								?>
							</tr>

						</thead>
					</table>
				</div>
				</div>
		</body>
		</html>