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
		<p>
			<center><b>LIST PENERIMAAN BARANG GUDANG <br>Tanggal <?= date('d-M-Y') ?></b></center>
		</p>
		<div style="width: 80%; margin: auto;">
			<table style="border:1px solid black; border-collapse: collapse; width: 100%;">
				<thead>
					<tr>
						<th style="border:1px solid black; background-color: #aee571;">Nomor PO</th>
						<th style="border:1px solid black; background-color: #aee571;">Nomor PR</th>
						<th style="border:1px solid black; background-color: #aee571;">Nama Item</th>
						<th style="border:1px solid black; background-color: #aee571;">Tanggal Penerimaan</th>
						<th style="border:1px solid black; background-color: #aee571;">Jumlah</th>
						<th style="border:1px solid black; background-color: #aee571;">Surat Jalan</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < count($data); $i++) { ?>
					<tr>
						<td style="border: 1px solid black; width: 15%; text-align: left;"><?= $data[$i]->no_po ?></td>
						<td style="border: 1px solid black; width: 5%; text-align: left;"><?= $data[$i]->no_pr ?></td>
						<td style="border: 1px solid black; width: 30%; text-align: left;"><?= $data[$i]->nama_item ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= date('d-m-Y', strtotime($data[$i]->date_receive)) ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= $data[$i]->qty_receive ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= $data[$i]->surat_jalan ?></td></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<br>
		<p style="font-weight: bold; margin: 0px;text-align: center;">
			This item was ordered by Purchasing Control Department<br>
			PT. Yamaha Musical Products Indonesia<br>
		</p>
		<p style="font-size: 14px; margin: 0px;text-align: center;">
			Jl. Rembang Industri I/36 Kawasan industri PIER Pasuruan<br>
		</p>

		<p style="font-size: 14px; margin: 0px;text-align: center;">
			Phone : 0343-740290
		</p>

		<p style="font-size: 14px; margin: 0px;text-align: center;">
			Fax : 0343-740291
		</p>
	</div>
</body>
</html>