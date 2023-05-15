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
			This is an automatic notification. Please do not reply to this address.<br>(Last Update: {{ date('d-M-Y H:i:s') }})
			<br>

			<table style="width: 95%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
				<thead>
					<tr>
						<td colspan="9" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
						<td style="font-weight: bold;font-size: 13px; text-align: right">Perihal : Pemberitahuan Tim Belum Mengerjakan KY</td>
					</tr>
					<tr>
						<td colspan="9" style="text-align: left;font-size: 13px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
					</tr>
					<tr>
						<td colspan="9" style="text-align: left;font-size: 13px">Phone : (0343) 740290 Fax : (0343) 740291</td>
					</tr>
					<tr>
						<td colspan="10" style="text-align: left;font-size: 13px">Jawa Timur Indonesia</td>
					</tr>
				</thead>
			</table><br>
			<table style="border-collapse: collapse;" width="95%">
				<tbody>
					<tr>
						<td style="text-align: left">Dear STD Team, Ditemukan ada beberapa tim KY yang belum melaksanakan KY secara teratur, berikut list nya :</td>
					</tr>
				</tbody>
			</table><br>
			<table style="border:1px solid black; border-collapse: collapse; width: 80%; padding-top: 0px">
				<thead style="background-color: #BDD5EA; color: black;">
					<tr>
						<th width="10%" style="border:1px solid black; text-align: center; font-size: 12px">No</th>
						<th width="20%" style="border:1px solid black; text-align: center; font-size: 12px">Nama Tim</th>
						<th width="40%" style="border:1px solid black; text-align: center; font-size: 12px">Nama Ketua Tim</th>
						<th width="20%" style="border:1px solid black; text-align: center; font-size: 12px">Tanggungan</th>
					</tr>
				</thead>
				<tbody>
					<?php
                    $index = 1;
                    foreach ($data['datas'] as $row) {
                        print_r('<tr>');
                        print_r('<td style="border:1px solid black; font-size: 12px; height: 25px; text-align: center;">' . $index . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; height: 25px; text-align: left;">' . $row->nama_tim . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; height: 25px; text-align: left;">' . $row->nama . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; height: 25px; text-align: left;">' . $row->bulan . '</td>');
                        print_r('</tr>');
                        $index++;
                    }
                    ?>
				</tbody>
				<tfoot>
				</tfoot>
			</table><br>
			<table style="border-collapse: collapse;" width="95%">
				<tbody>
					<tr>
						<td style="text-align: left">
							<p>
								Thanks & Regards,<br>
								Management Information System Department
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>