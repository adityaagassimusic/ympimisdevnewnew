<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 12px;
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

		.page-break {
			page-break-after: always;
		}

		@page { }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
			<thead>
				<tr>
					<td colspan="10" style="font-weight: bold;font-size: 15px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="10" style="text-align: left;font-size: 12px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
				</tr>
				<tr>
					<td colspan="10" style="text-align: left;font-size: 12px">ID : {{$id}}</td>
				</tr>
				<tr>
					<td colspan="10" style="text-align: center;font-size:30px"><b>Form Yokotenkai</b></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="10" style="text-align: left;font-size:20px"><b>Detail kecelakaan Kerja</b></td>
				</tr>
				<tr>
		            <td>
		            	<br>
		              <b>Lokasi Kejadian</b>
		            </td>
		            <td>
		            	<br>
		            	: 
		           	</td>
		            <td>
		            	<br>
		            	{{$accident->location}} - {{$accident->area}}
		            </td>
		      	</tr>
		      	<tr>
		            <td>
		              <b>Tanggal Kejadian</b>
		            </td>
		            <td>
		            	:
		            </td>
		            <td>
		            	<?php echo date('d-M-Y', strtotime($accident->date_incident)) ?> {{$accident->time_incident}}
		            </td>
		      	</tr>
		      	<tr>
		            <td>
		              <b>Kondisi Korban</b>
		            </td>
		            <td>
		            	:
		            </td>
		            <td>
		            	{{$accident->condition}}
		            </td>
		      	</tr>
		      	<tr>
		            <td>
		              <b>Detail Kejadian</b>
		            </td>
		            <td>
		            	: 
		            </td>
		            <td>
		            	{{$accident->detail_incident}}
		            </td>
		      	</tr>
		      	<tr>
		          <?php 
			          $data_image = json_decode($accident->illustration_image);
			          $data_detail = json_decode($accident->illustration_detail);
			          $jumlah = count($data_image);
		          ?>

		            <td>
		            	<b>Illustrasi Kejadian</b>
		            </td>
		            <td>
		            	: 
		            </td>
		            <td> 
		                <?php
		                  for ($i = 0; $i < $jumlah; $i++) { ?>
		                  	  <br>
		                      <?= $i+1 ?>. <?= $data_detail[$i] ?>
		                      <br>
		                      <br>
		                      <div style="display: inline-block;vertical-align: middle;">
		                      <img src="{{ url('files/kecelakaan/kecelakaan_kerja/'.$data_image[$i])}}"  height="100"> 
		                      
		                    </div>
		                  <?php } ?>    
		          	</td>
		          </tr>
		          <tr>
					<td>
		              <b>Info Yokotenkai Dari Standarisasi</b>
		            </td>
		            <td>
		            	: 
		            </td>
		            <td>
		            	<?= $accident->yokotenkai ?>
					</td>
			      </tr>

			      <tr>
			      	<td colspan="10">
			      		<hr>
			      	</td>
			      </tr>			    
			</tbody>
		</table>

		<div class="page-break"></div>

		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;">
			<thead>
			  	<tr>
					<td colspan="8" style="text-align: left;font-size:20px"><b>Detail Yokotenkai</b></td>
				</tr>

				<tr>
					<!-- <td width="2%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Department</td> -->
					<td width="2%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Group</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Pekerjaan Serupa</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Detail Pekerjaan Serupa</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Peralatan Sejenis</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Detail Peralatan Sejenis</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Standar K3</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Kaizen</td>
					<td width="1%" style="border: 1px solid black;font-size: 14px;font-weight: bold;">Detail Kaizen</td>
				</tr>
			</thead>
			<tbody>
				  @foreach($yokotenkai as $yks)
				  <tr>
					<!-- <td style="border: 1px solid black;">{{ $yks->department }}</td> -->
					<td style="border: 1px solid black;">{{ $yks->group }}</td>
					<td style="border: 1px solid black;">
						{{ $yks->pekerjaan_serupa }}

						@if($yks->pekerjaan_serupa_foto != null || $yks->pekerjaan_serupa_foto != "")
		                     <br><br>
		                     <img src="{{ url('files/kecelakaan/yokotenkai/'.$yks->pekerjaan_serupa_foto)}}"  height="150"> 
						@endif
					</td>
					<td style="border: 1px solid black;">
						{{ $yks->pekerjaan_serupa_detail }}
					</td>
					<td style="border: 1px solid black;">
						{{ $yks->peralatan_sejenis }}
					
						@if($yks->peralatan_sejenis_foto != null || $yks->peralatan_sejenis_foto != "")
		                     <br><br>
		                     <img src="{{ url('files/kecelakaan/yokotenkai/'.$yks->peralatan_sejenis_foto)}}"  height="150"> 
						@endif
					</td>
					<td style="border: 1px solid black;">
						{{ $yks->peralatan_sejenis_detail }}
					</td>
					<td style="border: 1px solid black;">{{ $yks->standar_k3 }}</td>
					<td style="border: 1px solid black;">{{ $yks->kaizen }}</td>
						@if($yks->kaizen_sebelum != null || $yks->kaizen_sebelum != "")
		                     <br>Kaizen Sebelum :<br>
		                     <img src="{{ url('files/kecelakaan/yokotenkai/'.$yks->kaizen_sebelum)}}"  height="150"> 
						@endif
						
						@if($yks->kaizen_sesudah != null || $yks->kaizen_sesudah != "")
		                     <br>Kaizen Sesudah :<br>
		                     <img src="{{ url('files/kecelakaan/yokotenkai/'.$yks->kaizen_sesudah)}}"  height="150"> 
						@endif
					<td style="border: 1px solid black;">
						{{ $yks->kaizen_detail }}
					</td>
				  </tr>
				  @endforeach
			</tbody>
		</table>
	</header>
	
</body>
</html>