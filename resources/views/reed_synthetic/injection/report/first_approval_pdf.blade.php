<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<title>First Approval {{ $approval->material_description }}</title>
</head>
<body >
	<style type="text/css">
		table tr td{
			border-collapse: collapse;
			vertical-align: middle;
		}

		table.table > tbody > tr > td {
			padding-top: 0px;
			padding-bottom: 0px;
			border: 1px solid black;
			font-size: 10px;
		}

		@page {
			margin-top: 5%; 
			margin-bottom: 0px; 
			vertical-align: middle;
		}

		#yamaha-logo {
			height: 50px;
			position: fixed;
			left: 5px;
			top: 5px;
		}

		.blank-header {
			width: 10px;
			border-right: 1px solid white !important;
			border-bottom: 1px solid white !important;			
		}

		.image {
			max-height: 50px;
			border-right: 1px solid white !important;
			vertical-align: bottom !important;
			border-bottom: 1px solid white !important;			
		}

		.titte {
			border-left: 1px solid white !important;
			text-align: center !important;
			vertical-align: middle !important;
			font-size: 13px !important;
			border-bottom: 1px solid white !important;
			font-weight: bold;		
		}

		.blank {
			height: 5px;
			border-top: 1px solid white !important;
		}

		.header-column {
			width: 5%;
			text-align: center;
			vertical-align: middle !important;
			border: 1px solid black !important;
		}

		.header-value {
			width: 25%;
			text-align: center;
			vertical-align: middle !important; 
		}

		.header-second {
			text-align: center;
			font-weight: bold;
		}

		.dok-level {
			font-size: 28px !important;
			text-align: center !important;
			vertical-align: middle !important;
			font-weight: bold !important;
		}

		.no-dok {
			font-size: 12px !important;
			text-align: center !important;
			vertical-align: middle !important;
			font-weight: bold !important;
		}

		.rev {
			font-size: 12px !important;
			text-align: center !important;
			vertical-align: middle !important;
			font-weight: bold !important;
		}

		.dok-info {
			width: 20%;
			border-right: 1px solid white !important;
		}

		.thin-border-bottom {
			border-bottom: 0.55px solid grey !important;
		}

		.thin-border-top {
			border-top: 0.55px solid grey !important;
		}

		.no-border-top {
			border-top: 1px solid white !important;
		}

		.no-border-bottom {
			border-bottom: 1px solid white !important;
		}

		.poin-kontrol {
			text-align: center !important;
			vertical-align: middle !important;
			font-weight: bold;
		}

		.content-kontrol {
			border-top: 1px double black !important;
			text-align: left !important;
			vertical-align: middle !important;
		}

		.red {
			color: red !important;;
		}

		.content-kontrol-center {
			text-align: center !important;
			vertical-align: middle !important;
		}

		.text-border {
			border: 1px solid black;
			border-radius: 2px;
			color: black;
			padding-left: 10px;
			padding-right: 5px;
		}

		.title-image-bottom {
			border-bottom: 1px solid white !important;
		}

		.image-bottom {
			border-top: 1px solid white !important;
			height: 280px;
		}

		#metode {
			height: 270px;
			position: fixed;
			left: 15px;
			top: 395px;
		}

		#drawing {
			height: 210px;
			position: fixed;
			left: 400px;
			top: 400px;
		}

		.note {
			text-align:left !important;
			vertical-align: middle !important;
			font-weight: bold;
		}

		.title-info-bottom {
			border-bottom: 1px solid white !important;
		}

		.content-info-bottom {
			border-top: 1px solid white !important;
		}

		.ttd{
			height: 70px;
			width: 16%;
		}

		.table-ttd {
			padding-top: 0px !important;
			padding-left: 0px !important;
			padding-right: 0px !important;
			border-left: 1px solid white !important;
		}

		.blank-bottom {
			border-bottom: 1px solid white !important;
		}

		.right-blank {
			border-right: 1px solid white !important;
		}

		.footer {
			text-align: center;
			font-family: sans-serif;
		}

		.coret {
			-webkit-text-decoration: line-through !important;
			text-decoration: line-through !important;
			text-decoration-style: double !important;
		}

		#manager_pe {
			height: 45px;
			position: fixed;
			left: 150px;
			top: 910px;
		}

		#manager_me {
			height: 50px;
			position: fixed;
			left: 247px;
			top: 907px;
		}

		#chief_pe {
			height: 45px;
			position: fixed;
			left: 340px;
			top: 910px;
		}

		#foreman_pe {
			height: 45px;
			position: fixed;
			left: 430px;
			top: 910px;
		}

		#leader_pe {
			height: 45px;
			position: fixed;
			left: 530px;
			top: 910px;
		}

		#staff_pe {
			height: 45px;
			position: fixed;
			left: 619px;
			top: 910px;
		}


	</style>

	<img id="yamaha-logo" src="{{ public_path() . '/files/reed/yamaha.png' }}">
	<img id="metode" src="{{ public_path() . '/files/reed/point_check.png' }}">
	<img id="drawing" src="{{ public_path() . '/files/reed/drawing/'. $approval->material_number .'.png' }}">

	<img id="manager_pe" src="{{ public_path() . '/files/reed/sign/manager_pe.png' }}">
	<img id="manager_me" src="{{ public_path() . '/files/reed/sign/manager_me.png' }}">
		<img id="chief_pe" src="{{ public_path() . '/files/reed/sign/chief_pe.png' }}">
	<img id="foreman_pe" src="{{ public_path() . '/files/reed/sign/foreman_pe.png' }}">
	<img id="leader_pe" src="{{ public_path() . '/files/reed/sign/leader_pe.png' }}">
	<img id="staff_pe" src="{{ public_path() . '/files/reed/sign/staff_pe.png' }}">


	@php

	$desc = explode(' ', $approval->material_description);
	$product = $desc[0];

	@endphp

	<table class="table table-bordered" style="margin-bottom: 0px !important;">
		<tbody>
			<tr>
				<td rowspan="2" colspan="1" class="blank-header"></td>
				<td rowspan="2" colspan="2" class="image"></td>
				<td rowspan="2" colspan="5" class="titte" >
					CDM (CEK DIMENSI MATERIAL)<br>
					REED SYNTHETIC {{ $product }}<br>
					FIRST APPROVAL
				</td>
				<td colspan="1" class="header-column">
					Tanggal
				</td>
				<td colspan="3" class="header-value">
					{{ date('d-M-y', strtotime($approval->date)) }}
				</td>
			</tr>

			<tr>
				<td colspan="1" class="header-column">
					PIC
				</td>
				<td colspan="3" class="header-value">
					{{ $approval->name }} <br> ({{ $approval->operator_id }})
				</td>
			</tr>
			
			<tr>
				<td colspan="12" class="blank">
				</td>
			</tr>

			<tr>
				<td colspan="2" class="header-second">
					Dok. Level
				</td>
				<td colspan="6" class="header-second">
					Judul Dokumen
				</td>
				<td colspan="3" class="header-second">
					No. Dok
				</td>
				<td colspan="1" class="header-second">
					Rev.
				</td>
			</tr>

			<tr>
				<td colspan="2" rowspan="4" class="dok-level">
					4
				</td>
				<td colspan="4" class="no-border-bottom dok-info">PART NAME </td>
				<td colspan="2" class="no-border-bottom dok-value">: Synthetic Reed {{ $product }}</td>
				<td colspan="3" rowspan="4" class="no-dok">
					@if($product == 'CLR')
					YMPI/RCD/FM/225
					@elseif($product == 'ASR')
					YMPI/RCD/FM/226
					@elseif($product == 'TSR')
					YMPI/RCD/FM/227
					@endif
				</td>
				<td colspan="1" rowspan="4" class="rev">
					00	
				</td>
			</tr>

			<tr>
				<td colspan="4" class="no-border-bottom no-border-top dok-info">DESC. MATERIAL </td>
				<td colspan="2" class="no-border-bottom no-border-top dok-value">: Synthetic Reed {{ $product }}</td>
			</tr>

			<tr>
				<td colspan="4" class="no-border-bottom no-border-top dok-info">GMC </td>
				<td colspan="2" class="no-border-bottom no-border-top dok-value">: {{ $approval->material_number }}</td>
			</tr>

			<tr>
				<td colspan="4" class="no-border-top dok-info">PROCESS </td>
				<td colspan="2" class="no-border-top dok-value">: Injection </td>
			</tr>

			<tr>
				<td colspan="12" class="thin-border-bottom" style="height: 1.25px;"></td>
			</tr>

			<tr>
				<td colspan="12" class="poin-kontrol thin-border-top">
					Poin Kontrol
				</td>
			</tr>

			<tr>
				<td colspan="1" class="poin-kontrol">
					No.
				</td>
				<td colspan="4" class="poin-kontrol">
					Poin Kontrol
				</td>
				<td colspan="3" class="poin-kontrol">
					Standart
				</td>
				<td colspan="2" class="poin-kontrol">
					Metode Kontrol
				</td>
				<td colspan="2" class="poin-kontrol">
					Periode
				</td>
			</tr>

			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-bottom">
					1
				</td>
				<td colspan="4" class="content-control thin-border-bottom">
					Panjang
				</td>
				<td colspan="3" class="content-control thin-border-bottom">
					@if($product == 'CLR')
					STD 69.80 mm +0.4/-0 mm
					@elseif($product == 'ASR')
					STD 73.0 mm +2.0/-0.1 mm																		
					@elseif($product == 'TSR')
					STD 81.70 mm +4.8/-0mm																		
					@endif
				</td>
				<td colspan="2" rowspan="3" class="content-kontrol-center thin-border-bottom">
					Nogisu
				</td>
				<td colspan="2" rowspan="5" class="content-kontrol-center thin-border-bottom">
					First Approval
				</td>
			</tr>

			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top thin-border-bottom">
					2
				</td>
				<td colspan="4" class="content-control thin-border-top thin-border-bottom">
					Diameter
				</td>
				<td colspan="3" class="content-control thin-border-top thin-border-bottom">
					@if($product == 'CLR')
					STD Ø 6mm +0.01/-0.02 mm
					@elseif($product == 'ASR')
					STD Ø 6mm +0.02/-0.01 mm
					@elseif($product == 'TSR')
					STD Ø 6mm +0.02/-0.01 mm																	
					@endif
				</td>
			</tr>

			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top thin-border-bottom">
					3
				</td>
				<td colspan="4" class="content-control thin-border-top thin-border-bottom">
					Tebal
				</td>
				<td colspan="3" class="content-control thin-border-top thin-border-bottom">
					@if($product == 'CLR')
					STD 3.2 mm ± 0.02mm
					@elseif($product == 'ASR')
					STD 3.5 mm ± 0.02mm																		
					@elseif($product == 'TSR')
					STD 3.5 mm ± 0.02mm
					@endif
				</td>
			</tr>

			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top thin-border-bottom">
					4
				</td>
				<td colspan="4" class="content-control thin-border-top thin-border-bottom">
					Berat
				</td>
				<td colspan="3" class="content-control thin-border-top thin-border-bottom">
					@if($product == 'CLR')
					STD 3.47 gram ± 0.05 gram
					@elseif($product == 'ASR')
					STD 4.02 gram ± 0.05 gram																
					@elseif($product == 'TSR')
					STD  4.45 gram ± 0.05 gram
					@endif
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					Digital Scale
				</td>
			</tr>

			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top">
					5
				</td>
				<td colspan="4" class="content-control thin-border-top">
					Visual Produk
				</td>
				<td colspan="3" class="content-control thin-border-top">
					
				</td>
				<td colspan="2" class="content-control thin-border-top">
					
				</td>
			</tr>

			<tr>
				<td colspan="12" class="thin-border-bottom" style="height: 1.25px;"></td>
			</tr>

			<tr>
				<td colspan="12" class="poin-kontrol thin-border-top">
					Poin Pengecekan																										
				</td>
			</tr>
			<tr>
				<td colspan="1" class="poin-kontrol">
					No.
				</td>
				<td colspan="4" class="poin-kontrol">
					Poin Kontrol
				</td>
				<td colspan="3" class="poin-kontrol">
					Isi Input Hasil Pengecekan
				</td>
				<td colspan="2" class="poin-kontrol">
					Rata-Rata Hasil Pengukuran
				</td>
				<td colspan="2" class="poin-kontrol">
					Penilaian
				</td>
			</tr>
			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-bottom">
					1
				</td>
				<td colspan="4" class="content-control thin-border-bottom">
					@if($product == 'CLR')
					Panjang (69.80 - 70.20 mm)															
					@elseif($product == 'ASR')
					Panjang (72.90 - 75.00 mm)																													
					@elseif($product == 'TSR')
					Panjang (81.70 - 86.50mm)															
					@endif
				</td>
				<td colspan="3" class="content-control thin-border-bottom">
					Hasil pengukuran
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-bottom">
					{{ round($detail[0]->length, 2) }}
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-bottom">
					@if($detail[0]->length >= $measurement->min_length)
					<span style="font-weight: bold;">OK</span> / <span class="coret">NG</span>
					@else
					<span class="coret">OK</span> / <span style="font-weight: bold;">NG</span>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top thin-border-bottom">
					2
				</td>
				<td colspan="4" class="content-control thin-border-top thin-border-bottom">
					@if($product == 'CLR')
					Diameter (Ø5.98 - Ø 6.01 mm)
					@elseif($product == 'ASR')
					Diameter (Ø5.99 - Ø 6.02 mm)								
					@elseif($product == 'TSR')
					Diameter (Ø5.99 - Ø 6.02 mm)															
					@endif
				</td>
				<td colspan="3" class="content-control thin-border-top thin-border-bottom">
					Hasil pengukuran
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					{{ round($detail[0]->diameter, 2) }}
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					@if(($detail[0]->diameter >= $measurement->min_diameter) && ($detail[0]->diameter <= $measurement->max_diameter))
					<span style="font-weight: bold;">OK</span> / <span class="coret">NG</span>
					@else
					<span class="coret">OK</span> / <span style="font-weight: bold;">NG</span>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top thin-border-bottom">
					3
				</td>
				<td colspan="4" class="content-control thin-border-top thin-border-bottom">
					@if($product == 'CLR')
					Tebal (3.18 - 3.22 mm)
					@elseif($product == 'ASR')
					Tebal (3.48 - 3.52 mm)																						
					@elseif($product == 'TSR')
					Tebal (3.48 - 3.52 mm)
					@endif
				</td>
				<td colspan="3" class="content-control thin-border-top thin-border-bottom">
					Hasil pengukuran
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					{{ round($detail[0]->thickness, 2) }}
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					@if(($detail[0]->thickness >= $measurement->min_thickness) && ($detail[0]->thickness <= $measurement->max_thickness))
					<span style="font-weight: bold;">OK</span> / <span class="coret">NG</span>
					@else
					<span class="coret">OK</span> / <span style="font-weight: bold;">NG</span>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top thin-border-bottom">
					4
				</td>
				<td colspan="4" class="content-control thin-border-top thin-border-bottom">
					@if($product == 'CLR')
					Berat (3.42-3.52 gr)
					@elseif($product == 'ASR')
					Berat (3.97 - 4.07 gr)
					@elseif($product == 'TSR')
					Berat (4.40 - 4.50 gr)
					@endif
				</td>
				<td colspan="3" class="content-control thin-border-top thin-border-bottom">
					Hasil pengukuran
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					{{ round($detail[0]->weight, 2) }}
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top thin-border-bottom">
					@if($detail[0]->weight >= $measurement->min_weight)
					<span style="font-weight: bold;">OK</span> / <span class="coret">NG</span>
					@else
					<span class="coret">OK</span> / <span style="font-weight: bold;">NG</span>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="1" class="content-kontrol-center thin-border-top">
					5
				</td>
				<td colspan="4" class="content-control thin-border-top">
					Visual Produk
				</td>
				<td colspan="3" class="content-control thin-border-top">
					
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top">
					
				</td>
				<td colspan="2" class="content-kontrol-center thin-border-top">
					OK / NG
				</td>
			</tr>

			<tr>
				<td colspan="12" class="thin-border-bottom" style="height: 1.25px;"></td>
			</tr>

			<tr>
				<td colspan="7" class="title-image-bottom thin-border-top">
					<span style="text-decoration: underline;">Metode</span>
				</td>
				<td colspan="5" class="title-image-bottom thin-border-top ">
					<span style="text-decoration: underline;">Drawing</span>
				</td>
			</tr>
			<tr>
				<td colspan="7" class="image-bottom">
					
				</td>
				<td colspan="5" class="image-bottom">
					
				</td>
			</tr>
			<tr>
				<td colspan="12" class="note">
					Bila terdapat ketidaksesuaian diluar check poin, lokasi, detail, dan jumlah timbulnya NG diisikan di kolom penangan kondisi sample abnormal, kumpulkan dengan melampirkan 1 sample
				</td>
			</tr>

			<tr>
				<td colspan="12" class="thin-border-bottom" style="height: 1.25px;"></td>
			</tr>

			<tr>
				<td colspan="7" class="title-info-bottom thin-border-top">
					<span style="text-decoration: underline;">Poin perhatian khusus</span>
				</td>
				<td colspan="5" class="title-info-bottom thin-border-top">
					<span style="text-decoration: underline;">Keterangan</span>
				</td>
			</tr>
			<tr>
				<td colspan="7" class="content-info-bottom">
					<ul style="margin-left: 0px; padding-left: 10px;">
						<li class="content-control red">
							Bila dalam kensa sampling ditemukan NG,langsung lakukan kensa 100% pada material hingga hasil kensa yang sebelumnya.
						</li>
						<li class="content-control">
							Untuk barang yang sulit diputuskan,mengikuti instruksi para Leader di area kerja,PIC mutu,PIC teknik.
						</li>
						<li class="content-control">
							Supaya tidak terkena yogore, kizu,sewaktu handling harap hati-hati
						</li>
					</ul>
				</td>
				<td colspan="5" class="content-info-bottom">
					
				</td>
			</tr>

			<tr>
				<td colspan="2" class="poin-kontrol">
					Tanggal
				</td>
				<td colspan="1" class="poin-kontrol">
					Rev.
				</td>
				<td colspan="9" class="poin-kontrol">
					Alasan dan Spesifikasi Perubahan
				</td>
			</tr>

			<tr>
				<td colspan="2" class="thin-border-bottom content-kontrol-center">
					17-Jun-21
				</td>
				<td colspan="1" class="thin-border-bottom content-kontrol-center">
					00
				</td>
				<td colspan="9" class="thin-border-bottom content-control">
					Pembuatan Baru
				</td>
			</tr>

			<tr>
				<td colspan="2" class="thin-border-top thin-border-bottom content-kontrol-center">&nbsp;</td>
				<td colspan="1" class="thin-border-top thin-border-bottom content-kontrol-center">&nbsp;</td>
				<td colspan="9" class="thin-border-top thin-border-bottom content-control">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="2" class="thin-border-top content-kontrol-center">&nbsp;</td>
				<td colspan="1" class="thin-border-top content-kontrol-center">&nbsp;</td>
				<td colspan="9" class="thin-border-top content-control">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="12" class="blank-bottom">
					&nbsp;
				</td>
			</tr>


			<tr>
				<td colspan="2" class="right-blank">
					
				</td>
				<td colspan="10" class="table-ttd">
					<table class="table table-bordered">
						<tbody>
							<tr>
								<td colspan="2" class="poin-kontrol">
									Disetujui Oleh																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Disetujui Oleh																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Dicek Oleh																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Dicek Oleh																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Dicek Oleh																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Dibuat Oleh																			
								</td>
							</tr>
							<tr>
								<td colspan="2" class="ttd">
									&nbsp;																		
								</td>
								<td colspan="2" class="ttd">
									&nbsp;																		
								</td>
								<td colspan="2" class="ttd">
									&nbsp;																	
								</td>
								<td colspan="2" class="ttd">
									&nbsp;																	
								</td>
								<td colspan="2" class="ttd">
									&nbsp;																
								</td>
								<td colspan="2" class="ttd">
									&nbsp;																
								</td>
							</tr>
							<tr>
								<td colspan="2" class="poin-kontrol">
									Manager PE																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Manager ME																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Chief PE																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Foreman PE																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Leader																			
								</td>
								<td colspan="2" class="poin-kontrol">
									Staff PE																			
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<h6 class="footer" style="margin-top: 0px;">YAMAHA Corp.</h6>
</body>
</html>