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
	</style>
</head>
<body>
	<div>
		<center>
			<table width="100%">
				<tr>
					<td width="65%" style="font-size: 18px" rowspan="2">
						<b>PT.YAMAHA MUSICAL PRODUCTS INDONESIA</b> <br><br>
						<b>FIXED ASSET LABEL REQUEST FORM</b> <br>
					</td>
					<td width="35%" class="border1" style="font-size: 12px;">
						Dokumen No : YMPI/FA/FM/006 <br>
						Revisi No : 00 <br>	
						Tanggal : 01 Agustus 2014 <br>
					</td>
				</tr>
				<tr>
					<td class="border1">
						Date Of Submission : {{$datas->submission_date}}<b></b><br>
						Reff. Number : <b></b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="80%" style="margin-top: 20px; margin-bottom: 20px" class="border1">
							<tr>
								<td class="border1"><center>PIC</center></td>
								<td class="border1"><center>Manager</center></td>
								<td class="border1"><center>FA Controller</center></td>
								<td class="border1"><center>Manager Acc</center></td>
								<td class="border1"><center>Receive PIC</center></td>
							</tr>
							<tr>
								<td class="border1">
									FA SIGN
								</td>
								<td class="border1">
									<?php if ($datas->approval_manager) {
										echo "Budhi Apriyanto";
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1">
									<?php if ($datas->approval_acc) {
										echo "Romy Agung Kurniawan";
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1">
									<?php if ($datas->approval_manager_acc) {
										echo "Romy Agung Kurniawan";
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1">
									<?php if ($datas->receive_pic) {
										echo "Romy Agung Kurniawan";
									} else {
										echo "&nbsp;";
									} ?>
								</td>
							</tr>
							<tr>
								<td class="border1" style="font-size: 11px">Date : 
									<?php echo $datas->app_at; ?>
								</td>
								<td class="border1" style="font-size: 11px">Date : 
									<?php if ($datas->approval_manager) {
										echo $datas->approval_manager;
									} ?>
								</td>
								<td class="border1" style="font-size: 11px">Date : 
									<?php if ($datas->approval_acc) {
										echo $datas->approval_acc;
									} ?>

								</td>
								<td class="border1" style="font-size: 11px">Date : 
									<?php if ($datas->approval_manager_acc) {
										echo $datas->approval_manager_acc;
									} ?>

								</td>
								<td class="border1" style="font-size: 11px">Date : 
									<?php if ($datas->receive_pic) {
										echo $datas->receive_pic;
									} ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						SECTION 1 : FILL BY PIC <br>
						<b>ASSET LABEL IDENTIFICATION</b>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td width="40%">FIXED ASSET NAME</td>
								<td width="1%">:</td>
								<td>{{ $datas->fixed_asset_name }}</td>
							</tr>
							<tr>
								<td>LABEL FIXED ASSET NO (SAP ID)</td>
								<td>:</td>
								<td>{{ $datas->fixed_asset_no }}</td>
							</tr>
							<tr>
								<td>SECTION CONTROL</td>
								<td>:</td>
								<td>{{ $datas->section }}</td>
							</tr>
							<tr>
								<td>LOCATION</td>
								<td>:</td>
								<td>{{ $datas->location }}</td>
							</tr>
							<tr>
								<td>PIC</td>
								<td>:</td>
								<td>{{ $datas->pic }} - {{ $datas->name }}</td>
							</tr>

							<tr>
								<td>REASON</td>
								<td>:</td>
								<td>{{ $datas->reason }}</td>
							</tr>
						</table>
					</tr>
				</table>

			</center>
		</div>
	</body>
	</html>