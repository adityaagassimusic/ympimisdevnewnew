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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="margin: 0px;">Ini adalah pesan otomatis dari sistem MIRAI YMPI<br>Mohon untuk tidak membalas email ke alamat ini.</p>
		</center>
		<br>
		
		<p>
			Dear <?= $data[0]->name ?>,<br>
			Sistem MIRAI telah merilis PO <?= $data[0]->no_po ?> baru untuk vendor : <?= $data[0]->supplier_name ?>
		</p>
		<div style="width: 80%; margin: auto;">
			<table style="border:1px solid black; border-collapse: collapse; width: 100%;">
				<thead>
					<tr>
						<th style="border:1px solid black; background-color: #aee571;">Nama Item</th>
						<th style="border:1px solid black; background-color: #aee571;">Tanggal Pengiriman</th>
						<th style="border:1px solid black; background-color: #aee571;">Jumlah</th>
						<th style="border:1px solid black; background-color: #aee571;">Satuan</th>
						<th style="border:1px solid black; background-color: #aee571;">Harga</th>
						<th style="border:1px solid black; background-color: #aee571;">Total</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $datas)
					<tr>
						<td style="border: 1px solid black; width: 40%; text-align: left;"><?= $datas->nama_item ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= date('d-m-Y', strtotime($datas->delivery_date)) ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= $datas->qty ?></td>
						<td style="border: 1px solid black; width: 10%; text-align: center;"><?= $datas->uom ?></td>
						<?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){
			                    $amount = $datas->goods_price * $datas->qty;                    
			                }else{
			                    $amount = $datas->service_price * $datas->qty; 
			                }
			            ?>
			             <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){ ?>
								<td style="border:1px solid black; width: 10%; text-align: right;"><?= number_format($datas->goods_price,2,",",".") ?></td>
			                <?php } else { ?>
								<td style="border:1px solid black; width: 10%; text-align: right;"><?= number_format($datas->service_price,2,",",".") ?></td>
			                <?php } ?>

						<td style="border:1px solid black; width: 10%; text-align: right;"><?= number_format($amount,2,",",".") ?></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<br>
		<p style="margin: 0px;">
			Untuk konfirmasi lebih lanjut silahkan langsung menghubungi Buyer (PIC) kami :<br>
		</p>
		<div style="width: 50%;" style="margin-top: 0px;">
			<table style="margin-top: 0px;">
				<tr>
					<th style="padding: 0px; border: none; width: 20%; text-align: left; font-weight: normal;">&nbsp;</th>
					<th style="padding: 0px; border: none; width: 80%; text-align: left; font-weight: normal;">:
						<a href="mailto:amelia.novrinta@music.yamaha.com">Amelia Novrinta</a>
					</th>			
				</tr>
				<tr>
					<th style="padding: 0px; border: none; width: 20%; text-align: left; font-weight: normal;">Buyer</th>
					<th style="padding: 0px; border: none; width: 80%; text-align: left; font-weight: normal;">:
						<a href="mailto:m.hamzah@music.yamaha.com">m.hamzah@music.yamaha.com</a>
					</th>					
				</tr>
				<tr>
					<th style="padding: 0px; border: none; width: 20%; text-align: left; font-weight: normal;">&nbsp;</th>
					<th style="padding: 0px; border: none; width: 80%; text-align: left; font-weight: normal;">:
						<a href="mailto:shega.erik.wicaksono@music.yamaha.com">shega.erik.wicaksono@music.yamaha.com</a>
					</th>			
				</tr>
			</table>
		</div>
		<br>
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