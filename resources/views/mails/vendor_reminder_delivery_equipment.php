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
			<?php if($data[0]->status_vendor == 'lokal') { ?>
			Dear ,<br>
			Apakah PO dibawah ini sudah dikirimkan? Jika belum akan dikirimkan kapan?
			<?php } else { ?>
			Dear ,<br>
			When this PO will be delivered?
			<?php } ?>
		</p>
		<div style="width: 80%; margin: auto;">
			<table style="border:1px solid black; border-collapse: collapse; width: 100%;">
				<thead>
					<tr>
						<th style="border:1px solid black; background-color: #aee571;">Nomor PO</th>
						<th style="border:1px solid black; background-color: #aee571;">Currency</th>
						<th style="border:1px solid black; background-color: #aee571;">Nama Item</th>
						<th style="border:1px solid black; background-color: #aee571;">Tanggal Pengiriman</th>
						<th style="border:1px solid black; background-color: #aee571;">Jumlah</th>
						<th style="border:1px solid black; background-color: #aee571;">Satuan</th>
						<th style="border:1px solid black; background-color: #aee571;">Harga</th>
						<th style="border:1px solid black; background-color: #aee571;">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < count($data); $i++) { ?>
					<tr>
						<td style="border: 1px solid black; width: 15%; text-align: left;"><?= $data[$i]->no_po ?></td>
						<td style="border: 1px solid black; width: 5%; text-align: left;"><?= $data[0]->currency ?></td>
						<td style="border: 1px solid black; width: 30%; text-align: left;"><?= $data[$i]->nama_item ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= date('d-m-Y', strtotime($data[$i]->delivery_date)) ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= (int) $data[$i]->qty - (int) $data[$i]->qty_receive ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= $data[$i]->uom ?></td>
						<?php
			                if($data[$i]->goods_price != "0" || $data[$i]->goods_price != 0){
			                    $amount = $data[$i]->goods_price * ($data[$i]->qty - $data[$i]->qty_receive);                    
			                }else{
			                    $amount = $data[$i]->service_price * ($data[$i]->qty - $data[$i]->qty_receive); 
			                }
			            ?>
			             <?php
			                if($data[$i]->goods_price != "0" || $data[$i]->goods_price != 0){ ?>
								<td style="border:1px solid black; width: 10%; text-align: right;"><?= number_format($data[$i]->goods_price,2,",",".") ?></td>
			                <?php } else { ?>
								<td style="border:1px solid black; width: 10%; text-align: right;"><?= number_format($data[$i]->service_price,2,",",".") ?></td>
			                <?php } ?>

						<td style="border:1px solid black; width: 10%; text-align: right;"><?= number_format($amount,2,",",".") ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<br>
		<p style="font-weight: bold; margin: 0px;">
			Purchasing Control Department<br>
			PT. Yamaha Musical Products Indonesia<br>
		</p>
		<p style="font-size: 14px; margin: 0px;">
			Jl. Rembang Industri I/36 Kawasan industri PIER Pasuruan<br>
		</p>

		<div style="width: 40%;" style="margin: 0px;">
			<table style="margin-top: 0px;">
				<tr>
					<th style="padding: 0px; border: none; width: 30%; text-align: left; font-weight: normal; font-size: 14px;">Phone</th>
					<th style="padding: 0px; border: none; width: 70%; text-align: left; font-weight: normal; font-size: 14px;">: 0343-740290</th>					
				</tr>
				<tr>
					<th style="padding: 0px; border: none; width: 30%; text-align: left; font-weight: normal; font-size: 14px;">Fax</th>
					<th style="padding: 0px; border: none; width: 70%; text-align: left; font-weight: normal; font-size: 14px;">: 0343-740291</th>					
				</tr>
			</table>
		</div>
	</div>
</body>
</html>