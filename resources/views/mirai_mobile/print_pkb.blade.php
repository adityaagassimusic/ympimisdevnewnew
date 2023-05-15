<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />	
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">


	<style type="text/css">
		.box {
			width: 24%; 
			display: inline-block;
		}
		img {
			margin: 0px !important;
		}

		/*@page { margin: 20px 20px 10px 20px; }*/

		td {
		  overflow: hidden;
		}

		.cropped {
			width: 90px;
			height: 90px;
			vertical-align: middle;
			text-align: center;
			background-position: center center;
			background-repeat: no-repeat;
		}

		@page { margin: 10px 10px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
		
	</style>
</head>
<body>
	

		<?php for($i = 0; $i < count($pkb); $i++){ ?>
		<table style="width:270px;  margin-left: auto; margin-right: auto;text-align: left;padding-bottom: 70px">
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="width:270px;text-align: left; font-size: 7pt;padding-left: 20px;padding-right: 20px;border-right: 1px solid black;border-left: 1px solid black;border-top: 1px solid black;margin-top:40px;background-color: yellow;">
					<span style="padding: 5px;font-weight: bold;">PKB Tahun {{$pkb[$i][$j][0]->periode}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="padding: 5px;font-weight: bold;"> Yamaha Musical Products Indonesia</span>
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="width:270px;text-align: center; font-size: 16px;text-decoration: underline;padding-left: 50px;padding-right: 50px;border-right: 1px solid black;border-left: 1px solid black;padding-top:30px">
					SURAT PERNYATAAN
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="width:270px;text-align: left; font-size: 8pt;padding-top: 20px;border-right: 1px solid black;border-left: 1px solid black;padding-left: 10px;padding-right: 10px;">
					Saya yang bertandatangan di bawah ini :
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="text-align: left; font-size: 8pt;padding-top: 20px;width: 100px;border-right: 1px solid black;border-left: 1px solid black;padding-left: 10px;padding-right: 10px;">
					<table>
						<tr>
							<td>Nama</td>
							<td style="padding-left: 10px">: {{$pkb[$i][$j][0]->name}}</td>
						</tr>
						<tr>
							<td>NIK</td>
							<td style="padding-left: 10px">: {{$pkb[$i][$j][0]->employee_id}}</td>
						</tr>
						<tr>
							<td>Grade / Jabatan</td>
							<td style="padding-left: 10px">: {{$pkb[$i][$j][0]->grade_code}} / {{$pkb[$i][$j][0]->position}}</td>
						</tr>
						<tr>
							<td>Bagian</td>
							<td style="padding-left: 10px">: {{$pkb[$i][$j][0]->department}}</td>
						</tr>
					</table>
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="text-align: left; font-size: 8pt;padding-top: 20px;width: 100px;border-right: 1px solid black;border-left: 1px solid black;padding-left: 10px;padding-right: 10px;">
					Menyatakan dengan sebenarnya bahwa saya telah menerima buku, membaca dan mengerti isi Perjanjian Kerja Bersama ini. <br><br>
					Demikian pernyataan ini saya buat dengan sebenar-benarnya dan tanpa ada paksaan dari pihak manapun.
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="text-align: left; font-size: 8pt;padding-top: 40px;width: 100px;border-right: 1px solid black;border-left: 1px solid black;padding-left: 10px;padding-right: 10px;">
					Pasuruan, {{date('d F Y',strtotime($pkb[$i][$j][0]->created_at))}}
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="text-align: left; font-size: 12pt;padding-top: 20px;width: 100px;border-right: 1px solid black;border-left: 1px solid black;padding-left: 10px;padding-right: 10px;color: green;font-weight: bold">
					MENYETUJUI
				</td>
				<?php } ?>
			</tr>
			<tr>
				<?php for($j = 0; $j < count($pkb[$i]); $j++){ ?>
				<td style="text-align: left; font-size: 8pt;padding-top: 20px;width: 100px;border-right: 1px solid black;border-left: 1px solid black;padding-left: 10px;border-bottom: 1px solid black;padding-bottom: 40px;text-decoration: underline;">
					{{$pkb[$i][$j][0]->name}}
				</td>
				<?php } ?>
			</tr>
		</table>
		<!-- <div class="column">
			<span class="contact100-form-title" style="padding-bottom: 15px;text-align: center;font-weight: bold;font-size: 18px">
				PERJANJIAN KERJA BERSAMA<br>(PKB)<br>
			</span>
			<span class="contact100-form-title" style="padding-bottom: 15px;text-align: center;font-size: 18px;text-decoration: underline;">
				SURAT PERNYATAAN
			</span>
			<table>
				<tr>
					<td colspan="3">Saya yang bertandatangan di bawah ini :</td>
				</tr>
				<tr>
					<td style="width: 3%">Nama</td>
					<td style="width: 1%">:</td>
					<td style="width: 20%"><span id="nama"></span></td>
				</tr>
				<tr>
					<td>NIK</td>
					<td>:</td>
					<td><span id="nik"></span></td>
				</tr>
				<tr>
					<td>Grade /<br>Jabatan</td>
					<td>:</td>
					<td><span id="grade"></span> / <span id="jabatan"></span></td>
				</tr>
				<tr>
					<td>Bagian</td>
					<td>:</td>
					<td><span id="department_pkb"></span></td>
				</tr>
			</table>
			<br>	
			Menyatakan dengan sebenarnya bahwa saya telah menerima buku, membaca <br>
			dan mengerti isi Perjanjian Kerja Bersama ini. <br>
			Demikian pernyataan ini saya buat dengan sebenar-benarnya dan tanpa ada paksaan <br>
			dari pihak manapun. <br>
			<br>
			<br>
			Pasuruan, {{date('d F Y')}}
			<br>

			<span id="nama_bawah" style="text-decoration: underline;"></span>
		</div> -->
		<?php } ?>
</body>
</html>