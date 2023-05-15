<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		@font-face {
		    font-family: 'ipag';
		    src: url({{ public_path('fonts/ipag.ttf') }}) format("truetype");
		}


		table {
			border-collapse: collapse;
		}
		td {
			padding: 3px;
		}
		.border1 {
			border: 1px solid black;
		}

		img {
			max-width: 350px;
		}

		.jepun {
			font-family: 'ipag';
		}

		@page { margin: 5px; }
		body { margin: 5px; }
	</style>
</head>
<body>
	<div>
		<center>
			<table width="100%" border="1">
				<tr>
					<td width="20%">
						<center>
							<img src="{{ public_path("waves2.png")}}" style="width: 120px"> <br>

							<b style="font-size: 8px;">PT.Yamaha Musical Products Indonesia</b>
						</center>
					</td>
					<td style="font-size: 18px">
						<center>
							<b>FORM PENGAJUAN TRIAL</b>
						</center>
					</td>
					<td style="font-size: 10px;" width="23%">
						Dokumen No : YMPI/STD/FK3/029 <br>
						Revisi No : 07 <br>	
						Tanggal : 02 Maret 2022 <br>
					</td>
				</tr>
			</table>
			<table width="100%" border="1" style="margin-top: 5px">
				<tr>
					<td colspan="2" style="background-color: yellow">
						<b style="font-size: 13px">I. PENGAJUAN TRIAL</b>
						<sup style="font-size: 10px">*di isi oleh bagian yang mengajukan trial</sup>
					</td>
				</tr>
				<tr>
					<td style="font-size: 12px" width="100%" colspan="2">
						<b>Kepada Yth.</b> <br>
						<table width="100%">
							<tr>
								<td>Nama</td>
								<td> : {{$data_trial->trial_to_name}}</td>
								<td>Tanggal Pengajuan</td>
								<td> : {{$data_trial->submission_date}}</td>
								<td>No. Referensi</td>
								<td> : {{$data_trial->sakurentsu_number}}</td>
							</tr>
							<tr>
								<td>Departemen</td>
								<td style="width: 25%"> : {{$data_trial->department}}</td>
								<td>Tanggal Trial</td>
								<td> : {{$data_trial->trial_date}}</td>
								<td>Total APD/Material</td>
								<td> : {{ $data_trial->apd_material}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="padding: 0px" colspan="2">
						<table width="100%" border="1" style="text-align: center; font-size: 11px;">
							<tr style="font-weight: bold; background-color: #d9d9d9">
								<td colspan="2">Kondisi</td>
							</tr>
							<tr style="font-weight: bold; background-color: #d9d9d9">
								<td style="border-bottom: 2px solid black">Sebelumnya</td>
								<td style="border-bottom: 2px solid black">Trial</td>
							</tr>
							<tr>
								<td style="text-align: left;" class="jepun"><?php print_r($data_trial->trial_before) ?></td>
								<td style="text-align: left;" class="jepun"><?php print_r($data_trial->trial_detail) ?></td>
							</tr>
							<tr>
								<td colspan="2" style="font-weight: bold; border-bottom: 2px solid black; background-color: #d9d9d9">Tujuan Trial</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: left" class="jepun"><?php print_r($data_trial->trial_purpose) ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="padding: 0px" colspan="2">
						<table width="100%" border="1" style="text-align: center; font-size: 11px; margin-top: 5px">
							<thead>
								<tr style="font-weight: bold; background-color: #d9d9d9">
									<td style="border-bottom: 2px solid black" width="1%">No</td>
									<td style="border-bottom: 2px solid black" width="30%">Nama Material</td>
									<td style="border-bottom: 2px solid black" width="10%">Jumlah</td>
									<td style="border-bottom: 2px solid black" width="10%">Lokasi/Area Trial</td>
									<td style="border-bottom: 2px solid black">Keterangan / Spesifikasi</td>
								</tr>
							</thead>
							<tbody>
								<?php $no2=count($data_material); $no=1; foreach ($data_material as $mat): ?>
								<tr>
									<td>{{$no}}</td>
									<td class="jepun">{{$mat->material_name}}</td>
									<td>{{$mat->quantity}}</td>
									<td>{{$data_trial->trial_location}}</td>
									<?php if ($no == 1): ?>
										<td rowspan="{{$no2}}" style="text-align: left" class="jepun"><?php print_r(nl2br($data_trial->trial_info)) ?></td>
									<?php endif ?>
								</tr>	
								<?php $no++; endforeach ?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td style="padding: 0px;" width="5%"></td>
					<td style="padding: 0px;" width="95%">
						<table width="100%" border="1" style="text-align: center; font-size: 10px; margin-top: 5px;">
							<tr>
								<?php if ($data_trial->pic_dept == 'Production Engineering Department') $col = 6; else $col = 5;?>
								<td colspan="<?php echo $col ?>">Bagian yang Mengajukan Trial</td>
								<td colspan="2">Bagian Terkait</td>
							</tr>
							<tr>
								<td>Dibuat Oleh,</td>
								<td>Diperiksa Oleh,</td>
								<td>Disetujui Oleh,</td>
								<?php if ($data_trial->pic_dept == 'Production Engineering Department') { ?>
									<td>Disetujui Oleh,</td>
								<?php } ?>
								<td>Disetujui Oleh,</td>
								<td>Disetujui Oleh,</td>
								<td>Disetujui Oleh,</td>
								<td>Disetujui Oleh,</td>
							</tr>
							<tr>
								<td>{{$data_trial->requester_name}} </td>
								<td>@if(isset($data_trial->chief)) {{ explode('/',$data_trial->chief)[1] }} @endif</td>
								<td>@if(isset($data_trial->manager)) {{ explode('/',$data_trial->manager)[1] }} @endif</td>

								<?php if ($data_trial->pic_dept == 'Production Engineering Department') { ?>
									<td>@if(isset($data_trial->manager_mechanical)) {{ explode('/',$data_trial->manager_mechanical)[1] }} </span> @else &nbsp;<br>&nbsp; @endif</td>
								<?php } ?>

								<td>@if(isset($data_trial->dgm)) {{ explode('/',$data_trial->dgm)[1] }} @endif</td>
								<td>@if(isset($data_trial->gm)) {{ explode('/',$data_trial->gm)[1] }} @endif</td>
								<td>@if(isset($data_trial->dgm2)) {{ explode('/',$data_trial->dgm2)[1] }} @endif</td>
								<td>@if(isset($data_trial->gm2)) {{ explode('/',$data_trial->gm2)[1] }} @endif</td>
							</tr>
							<tr>
								<td style="background-color: #84fc74;"><span style="font-size: 8px;">{{$data_trial->submission_date}}</span></td>

								@if(isset($data_trial->chief_date))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->chief_date}}</span></td>
								@else
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@endif

								@if(isset($data_trial->manager_date))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->manager_date}}</span></td>
								@else
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@endif

								@if($data_trial->pic_dept == 'Production Engineering Department')
								@if(isset($data_trial->manager_mechanical_date))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->manager_mechanical_date}}</span></td>
								@else
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@endif
								@endif

								@if(isset($data_trial->dgm_date))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->dgm_date}}</span></td>
								@else
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@endif

								@if(isset($data_trial->gm_date))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->gm_date}}</span></td>
								@else
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@endif

								@if(isset($data_trial->dgm_date2))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->dgm_date2}}</span></td>
								@elseif(isset($data_trial->dgm2))
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@else
								<td style="background-color: #000;"><span style="font-size: 8px">&nbsp;</span></td>
								@endif

								@if(isset($data_trial->gm_date2))
								<td style="background-color: #84fc74;"><span style="font-size: 8px">{{$data_trial->gm_date2}}</span></td>
								@elseif(isset($data_trial->gm2))
								<td style="background-color: #fa7878;"><span style="font-size: 8px">Not Approve</span></td>
								@else
								<td style="background-color: #000;"><span style="font-size: 8px">&nbsp;</span></td>
								@endif
							</tr>
							<tr>
								<td>Staff</td>
								<td>Chief</td>
								<td>Manager</td>
								<?php if ($data_trial->pic_dept == 'Production Engineering Department') { ?>
									<td>Manager Mechanical</td>
								<?php } ?>
								<td>DGM</td>
								<td>GM</td>
								<td>DGM</td>
								<td>GM</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<table width="100%" border="1" style="margin-top: 5px">
				<thead>
					<tr>
						<td colspan="6" style="background-color: yellow">
							<b style="font-size: 13px">II. PENERIMA TRIAL</b>
							<sup style="font-size: 10px">*di isi oleh bagian yang menerima trial dan bagian terkait</sup>
						</td>
					</tr>
					<tr style="text-align: center; font-size: 10px; font-weight: bold">
						<td style="width: 2%">No</td>
						<td style="width: 20%">Departemen</td>
						<td style="width: 20%">Section</td>
						<td>Rencana Perbaikan / Rekomendasi</td>
						<td style="width: 15%">TTD Manager</td>
						<td style="width: 15%">TTD Chief/Foreman</td>
					</tr>
				</thead>
				<tbody style="font-size: 10px;">
					<?php $no = 1; foreach ($data_receiver as $receive): ?>
					<tr>
						<td>{{$no}}</td>
						<td>{{$receive->trial_receive_department}}</td>
						<td>{{$receive->trial_receive_section}}</td>
						<td>{{$receive->perbaikan}}</td>
						<td>{{ explode('/',$receive->manager)[1] }} <br> <span style="font-size: 8px">Tgl. {{$receive->manager_date}} </span></td>
						<td>{{ explode('/',$receive->chief)[1] }} <br> <span style="font-size: 8px">Tgl. {{$receive->chief_date}} </span></td>
					</tr>
					<?php $no++; endforeach ?>
				</tbody>
			</table>

			<table width="100%" border="1" style="margin-top: 5px">
				<tr>
					<td colspan="3" style="background-color: yellow">
						<b style="font-size: 13px">III. HASIL TRIAL</b>
						<sup style="font-size: 10px">*di isi oleh bagian yang menerima trial</sup>
					</td>
				</tr>
				<tr style="font-size: 10px; font-weight: bold">
					<td style="text-align: center;">Metode Trial</td>
					<td style="width: 30%" colspan="2">Tanggal Trial : @if($data_trial->trial_date) {{$data_trial->trial_date}} @endif</td>
				</tr>
				<tr style="font-size: 10px; font-weight: bold">
					<td colspan="3">
						@if(count($data_result) > 0)  
						<table>
							<?php foreach ($data_result as $result): 
								if ($result->trial_method) {
									echo '<tr>';
									echo '<td style="padding-top : 0px; padding-bottom : 0px">'.$result->fill_by.' : </td>';
									echo '<td>'.$result->trial_method.'</td>';
									echo '</tr>';
								}
							endforeach ?>
						</table>
						@endif
					</td>
				</tr>

				<tr style="font-size: 10px; font-weight: bold">
					<td style="text-align: center;">Hasil Trial</td>
					<td style="width: 30%" colspan="2">Tanggal Penyelesaian : @if(count($data_result) > 0) {{$data_result[0]->trial_date_finish}} @endif</td>
				</tr>
				<tr style="font-size: 10px; font-weight: bold">
					<td colspan="3">
						@if(count($data_result) > 0) 
						<table>

							<?php foreach ($data_result as $result):
								if ($result->trial_method) {
									echo '<tr>';
									echo '<td style="padding-top : 0px; padding-bottom : 0px">'.$result->fill_by.' : </td>';
									echo '<td>'.$result->trial_result.'</td>';
									echo '</tr>';
								} 
							endforeach ?>
						</table>
						@endif
					</td>
				</tr>

				<tr style="font-size: 10px; font-weight: bold">
					<td style="text-align: center">Komentar dan Rekomendasi</td>
					<td style="width: 30%; text-align: center" colspan="2">Kesimpulan</td>
				</tr>
				<tr style="font-size: 10px; font-weight: bold">
					<td rowspan="2">@if(count($data_result) > 0) {{$data_result[0]->comment}} @endif</td>
					<td style="text-align: center">OK</td>
					<td style="text-align: center">Not OK</td>
				</tr>
				<tr>
					<td style="font-weight: bold; text-align: center;"><?php if($data_trial->qc_report_status) if ($data_trial->qc_report_status == 'OK') echo 'V'; else echo '&nbsp;'; ?></td>
					<td style="font-weight: bold; text-align: center;"><?php if($data_trial->qc_report_status) if ($data_trial->qc_report_status == 'Not OK') echo 'V'; else echo '&nbsp;'; ?></td>
				</tr>
			</table>

			<!-- <table width="80%" border="1" style="margin-top: 5px; text-align: center; font-size: 9px;">
				<tr>
					<td colspan="3">Penerima Trial,</td>
					<td colspan="3">Pengusul Trial,</td>
					<td rowspan="2">Disetujui Oleh,</td>
					<td rowspan="2">Disetujui Oleh,</td>
					<td rowspan="2">Disetujui Oleh,</td>
				</tr>
				<tr>
					<td>Dibuat Oleh,</td>
					<td>Diperiksa Oleh,</td>
					<td>Disetujui Oleh,</td>
					<td>Diterima Oleh,</td>
					<td>Diperiksa Oleh,</td>
					<td>Disetujui Oleh,</td>
				</tr>
				<tr>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
					<td>&nbsp;<br>&nbsp;</td>
				</tr>
				<tr>
					<td>Staff</td>
					<td>Chief / Foreman</td>
					<td>Manager</td>
					<td>Staff</td>
					<td>Chief / Foreman</td>
					<td>Manager</td>
					<td>DGM Produksi</td>
					<td>GM</td>
					<td>GM</td>
				</tr>
			</table> -->

			<table width="100%" border="1">
				<tr>
					<td colspan="2" style="font-size: 9px">*) Diperiksa Oleh bagian yang menerima Trial</td>
				</tr>
				<tr>
					<td style="font-size: 11px; width: 10%">Rev. 05</td>
					<td style="font-size: 11px">Perubahan penyebutan KK menjadi Leader</td>
				</tr>
				<tr>
					<td style="font-size: 11px">Rev. 06</td>
					<td style="font-size: 11px">Penambahan kolom sign DGM Produksi</td>
				</tr>
				<tr>
					<td style="font-size: 11px">Rev. 07</td>
					<td style="font-size: 11px">Penambahan kolom sign Bagian Terkait saat pengajuan trial</td>
				</tr>
			</table>
		</center>
	</div>
</body>
</html>