<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		td {
			padding: 3px;
		}
		.border1 {
			border: 1px solid black;
		}
		.sign_name {
			font-size: 9px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<table width="100%">
				<tr>
					<td width="65%" style="font-size: 18px" rowspan="2">
						<b>PT.YAMAHA MUSICAL PRODUCTS INDONESIA</b> <br><br>
						<b>FIXED ASSET DISPOSAL SCRAP FORM REPORT</b> <br>
					</td>
					<td width="35%" class="border1" style="font-size: 12px;">
						Dokumen No : YMPI/FA/FM/006 <br>
						Revisi No : 00 <br>	
						Tanggal : 01 Agustus 2014 <br>
					</td>
				</tr>
				<tr>
					<td class="border1">
						Date Of Submission : {{ $datas->create_at }}<b></b><br>
						Reff. Number : {{ $datas->form_number }}<b></b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="80%" style="margin-top: 20px; margin-bottom: 20px" class="border1">
							<tr>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Dispose Officer</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Manager</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">GM</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Acc Manager</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Director Finance</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">FA Control</td>
							</tr>
							<tr>
								<td class="border1 sign_name">
									<?php if ($datas->pic_app_date) {
										echo explode('/', $datas->pic_app)[1];
									} else {
										echo "&nbsp;";
									} ?>									
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->manager_app_date) {
										echo explode('/', $datas->manager_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->gm_app_date) {
										echo explode('/', $datas->gm_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->acc_manager_app_date) {
										echo explode('/', $datas->acc_manager_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->director_app_date) {
										echo explode('/', $datas->director_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->acc_control_app_date) {
										echo explode('/', $datas->acc_control_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
							</tr>
							<tr>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->pic_app_date) {
										echo $datas->pic_app_date;
									} ?>
								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->manager_app_date) {
										echo $datas->manager_app_date;
									} ?>
								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->gm_app_date) {
										echo $datas->gm_app_date;
									} ?>

								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->acc_manager_app_date) {
										echo $datas->acc_manager_app_date;
									} ?>

								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->director_app_date) {
										echo $datas->director_app_date;
									} ?>

								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->acc_control_app_date) {
										echo $datas->acc_control_app_date;
									} ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1" style="background-color: #cccccc">
						<b>ASSET IDENTIFICATION</b>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td width="40%">FA DISPOSAL NUMBER</td>
								<td width="1%">:</td>
								<td>{{ $datas->form_number_disposal }}</td>
							</tr>
							<tr>
								<td>FIXED ASSET NUMBER</td>
								<td>:</td>
								<td>{{ $datas->fixed_asset_id }}</td>
							</tr>
							<tr>
								<td>FIXED ASSET NAME</td>
								<td>:</td>
								<td>{{ $datas->fixed_asset_name }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td colspan="2" class="border1" style="background-color: #cccccc">
						<b>ASSET ACTIVITY</b>
					</td>
				</tr>

				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td width="40%">DISPOSAL DATE</td>
								<td width="1%">:</td>
								<td>{{ $datas->disposal_date }}</td>
							</tr>
							<tr>
								<td>OFFICER DEPARTMENT</td>
								<td>:</td>
								<td>{{ $datas->officer_department }}</td>
							</tr>
							<tr>
								<td>OFFICER NAME</td>
								<td>:</td>
								<td>{{ explode('/', $datas->officer)[1]  }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td colspan="2" class="border1" style="background-color: #cccccc">
						<b>ACTIVITY WHEN SCRAPPING OR DEMOLISHING</b>
					</td>
				</tr>

				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td width="50%">PICTURE BEFORE</td>
								<td width="50%"><img src="{{ url('files/fixed_asset/scrap_picture/'.$datas->picture_before)}}" alt="picture before" style="border: 1px solid black; max-width: 250px; max-height: 170px"></td>
							</tr>
							<tr>
								<td>PICTURE SCRAP PROCESS</td>
								<td><img src="{{ url('files/fixed_asset/scrap_picture/'.$datas->picture_process)}}" alt="picture process" style="border: 1px solid black; max-width: 250px; max-height: 170px"></td>
							</tr>
							<tr>
								<td>PICTURE AFTE SCRAP</td>
								<td><img src="{{ url('files/fixed_asset/scrap_picture/'.$datas->picture_after)}}" alt="picture after" style="border: 1px solid black; max-width: 250px; max-height: 170px"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

		</center>
	</div>
</body>
</html>