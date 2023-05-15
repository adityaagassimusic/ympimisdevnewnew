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
	max-width: 250px;
}

.jepun {
	font-family: 'ipag';
}

.bok {
	border: 1px solid black;
	width: 10px;
	height: 10px;
	display: inline-block;
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
					<td style="text-align: left; font-size: 10px;">
						Dok No : YMPI / PE / FM / 006
					</td>
					<td>
						<center style="font-size: 10px;">Revisi No. : 01</center>
					</td>
					<td style="text-align: right; font-size: 10px;">
						Date: 20 September 2011
					</td>
				</tr>
				<tr>
					<td width="20%">
						<center>
							<img src="{{ public_path("waves2.png")}}" style="width: 120px"> <br>

							<b style="font-size: 8px;">PT.Yamaha Musical Products Indonesia</b>
						</center>
					</td>
					<td style="font-size: 18px">
						<center>
							<b>ENGINEERING JOB REQUEST</b>
						</center>
					</td>
					<td style="font-size: 16px;" width="23%">
						<center>Production Engineering</center>
					</td>
				</tr>
			</table>
			<table width="100%" border="1" style="margin-top: 5px">
				<tr>
					<td style="font-size: 12px" width="100%">
						<table width="100%">
							<tr>
								<td width="20%">Bagian</td>
								<td width="30%"> : {{$form_data->section}}</td>
								<td width="20%">Target Penyelesaian</td>
								<td width="30%"> : {{$form_data->target_date}}</td>
							</tr>
							<tr>
								<td>Tanggal</td>
								<td style="width: 25%"> : {{$form_data->req_date}}</td>
								<td>Target Aktual</td>
								<td> : </td>
							</tr>
							<tr>
								<td>Prioritas</td>
								<?php $color = ''; if ($form_data->priority == 'Urgent') {
									$color = 'color : #e53935';
								} ?>
								<td style="width: 25%; <?php echo $color ?>"> : {{$form_data->priority}}</td>
							</tr>
							<tr>
								<td>Alasan Prioritas</td>
								<td style="width: 25%"> : {{$form_data->priority_reason}}</td>
							</tr>
							<tr>
								<td>Alasan Pembuatan</td>
								<td style="width: 25%"> : {{$form_data->reason}}</td>
							</tr>
							<tr>
								<td>Tipe Pekerjaan</td>
								<td>
									<table style="width: 100%">
										<tr>
											<?php if ($form_data->job_type == 'Perbaikan') { ?>
												<td>: <div class="bok" style="background-color: #90fa8e"></div> Perbaikan</td>
											<?php }  else { ?>
												<td>: <div class="bok"></div> Perbaikan</td>
											<?php } ?>

											<?php if ($form_data->job_type == 'Desain Baru') { ?>
												<td> <div class="bok" style="background-color: #90fa8e"></div> Desain Baru</td>
											<?php }  else { ?>
												<td> <div class="bok"></div> Desain Baru</td>
											<?php } ?>

											<?php if ($form_data->job_type == 'Trial') { ?>
												<td> <div class="bok" style="background-color: #90fa8e"></div> Trial</td>
											<?php }  else { ?>
												<td> <div class="bok"></div> Trial</td>
											<?php } ?>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="vertical-align: top">Detail Pekerjaan</td>
								<td>
									<table style="width: 100%">
										<tr>
											<?php if ($form_data->job_category == 'Layout') { ?>
												<td>: <div class="bok" style="background-color: #90fa8e"></div> Layout</td>
											<?php }  else { ?>
												<td>: <div class="bok"></div> Layout</td>
											<?php } ?>

											<?php if ($form_data->job_category == 'Jig/Mold') { ?>
												<td> <div class="bok" style="background-color: #90fa8e"></div> Jig/Mold</td>
											<?php }  else { ?>
												<td> <div class="bok"></div> Jig/Mold</td>
											<?php } ?>

											<?php if ($form_data->job_category == 'Mesin') { ?>
												<td> <div class="bok" style="background-color: #90fa8e"></div> Mesin</td>
											<?php }  else { ?>
												<td> <div class="bok"></div> Mesin</td>
											<?php } ?>
										</tr>
										<tr>
											<?php if ($form_data->job_category == 'Equipment') { ?>
												<td>&nbsp; <div class="bok" style="background-color: #90fa8e"></div> Equipment</td>
											<?php }  else { ?>
												<td>&nbsp; <div class="bok"></div> Equipment</td>
											<?php } ?>

											<?php if ($form_data->job_category == 'Tools') { ?>
												<td> <div class="bok" style="background-color: #90fa8e"></div> Tools</td>
											<?php }  else { ?>
												<td> <div class="bok"></div> Tools</td>
											<?php } ?>

											<?php if ($form_data->job_category == 'Proses') { ?>
												<td> <div class="bok" style="background-color: #90fa8e"></div> Proses</td>
											<?php }  else { ?>
												<td> <div class="bok"></div> Proses</td>
											<?php } ?>
										</tr>
										<tr>
											<?php if ($form_data->job_category == 'Lain-lain') { ?>
												<td colspan="3">&nbsp; <div class="bok" style="background-color: #90fa8e"></div>  Lain - lain : <u>{{ $form_data->job_category_note }}</u></td>
											<?php }  else { ?>
												<td colspan="3">&nbsp; <div class="bok"></div>  Lain - lain : </td>
											<?php } ?>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td colspan="2">
									<table style="width: 100%;" border="1">
										<tr>
											<td style="background-color: #f2ccff"><center>Menyetujui</center></td>
											<td style="background-color: #f2ccff"><center>Mengetahui</center></td>
											<td style="background-color: #f2ccff"><center>Pemohon</center></td>
										</tr>
										<tr>
											<?php 
											if ($approval[2]->appr_at) {
												echo '<td><center><b>'.$approval[2]->status.'</b><br/>'.$approval[2]->appr_at.'</center></td>';
											} else {
												echo '<td><center>&nbsp;</center></td>';
											}

											if ($approval[1]->appr_at) {
												echo '<td><center><b>'.$approval[1]->status.'</b><br/>'.$approval[1]->appr_at.'</center></td>';
											} else {
												echo '<td><center>&nbsp;</center></td>';
											}

											if ($approval[0]->appr_at) {
												echo '<td><center><b>'.$approval[0]->status.'</b><br/>'.$approval[0]->appr_at.'</center></td>';
											} else {
												echo '<td><center>&nbsp;</center></td>';
											}
											?>
										</tr>
										<tr>
											<td style="background-color: #f2ccff"><center>{{ $approval[2]->approver_name }}</center></td>
											<td style="background-color: #f2ccff"><center>{{ $approval[1]->approver_name }}</center></td>
											<td style="background-color: #f2ccff"><center>{{ $approval[0]->approver_name }}</center></td>
										</tr>
										<tr>
											<td style="background-color: #f2ccff"><center>Manager</center></td>
											<td style="background-color: #f2ccff"><center>Chief/Foreman</center></td>
											<td style="background-color: #f2ccff"><center>Leader/Staff</center></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table width="100%" border="1" style="margin-top: 5px">
				<tr>
					<td style="padding: 2px; font-size: 12px"><b>Detail Pekerjaan</b></td>
				</tr>
				<tr>
					<td style="padding: 2px; font-size: 12px"><b>Deskripsi Pekerjaan : </b><br/><br/><?php print_r($form_data->description) ?></td>
				</tr>
				<tr>
					<td style="padding: 2px; font-size: 12px"><b>Tujuan Perbaikan : </b><br/><br/><?php print_r($form_data->purpose) ?></td>
				</tr>
				<tr>
					<td style="padding: 2px; font-size: 12px"><b>Kondisi Sekarang : (Foto / Copy Drawing)</b><br/><br/><?php print_r($form_data->condition_before) ?></td>
				</tr>
				<tr>
					<td style="padding: 2px; font-size: 12px"><b>Kondisi Perbaikan : (Sketsa Drawing)</b><br/><br/><?php print_r($form_data->condition_after) ?></td>
				</tr>
			</table>

			<table border="1" align="right" style="margin-right: 5px; font-size: 12px">
				<tr>
					<td style="background-color: #f2ccff"><center>Menyetujui</center></td>
					<td style="background-color: #f2ccff"><center>Mengetahui</center></td>
					<td style="background-color: #f2ccff"><center>Dikerjakan</center></td>
				</tr>
				<tr>
					<?php 
					if ($approval[3]->appr_at) {
						echo '<td><center><b>'.$approval[3]->status.'</b><br/>'.$approval[3]->appr_at.'</center></td>';
					} else {
						echo '<td><center>&nbsp;</center></td>';
					}

					if ($approval[4]->appr_at) {
						echo '<td><center><b>'.$approval[4]->status.'</b><br/>'.$approval[4]->appr_at.'</center></td>';
					} else {
						echo '<td><center>&nbsp;</center></td>';
					}

					if ($form_data->pic_date) {
						echo '<td><center><b>RECEIVED</b><br/>'.$form_data->pic_date.'</center></td>';
					} else {
						echo '<td><center>&nbsp;</center></td>';
					}
					?>
				</tr>
				<tr>
					<td style="background-color: #f2ccff"><center>{{ $approval[3]->approver_name }}</center></td>
					<td style="background-color: #f2ccff"><center>{{ $approval[4]->approver_name }}</center></td>
					@if ($form_data->pic)
					<td style="background-color: #f2ccff"><center>{{ explode('/', $form_data->pic)[1]  }}</center></td>
					@else
					<td style="background-color: #f2ccff"><center></center></td>
					@endif
				</tr>
				<tr>
					<td style="background-color: #f2ccff"><center>Manager PE</center></td>
					<td style="background-color: #f2ccff"><center>Chief PE</center></td>
					<td style="background-color: #f2ccff"><center>Staff PE</center></td>
				</tr>
			</table>
		</center>
	</div>
</body>
</html>