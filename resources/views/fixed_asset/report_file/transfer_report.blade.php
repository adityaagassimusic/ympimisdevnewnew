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
						<b>FIXED ASSET TRANSFER FORM</b> <br>
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
						Reff. Number : {{$datas->form_number}}<b></b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="80%" style="margin-top: 20px; margin-bottom: 20px" class="border1">
							<tr>
								<td class="border1 sign_name" style="background-color: #cccccc">Old PIC</td>
								<td class="border1 sign_name" style="background-color: #cccccc">Manager</td>
								<td class="border1 sign_name" style="background-color: #cccccc">New PIC</td>
								<td class="border1 sign_name" style="background-color: #cccccc">New Manager</td>
								<td class="border1 sign_name" style="background-color: #cccccc">FA Controller</td>
								<td class="border1 sign_name" style="background-color: #cccccc">Acc Manager</td>
							</tr>
							<tr>
								<td class="border1 sign_name">
									<?php if ($datas->approval_pic_date) {
										echo explode('/', $datas->approval_pic)[1];
									} else {
										echo "&nbsp;";
									} ?>									
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->approval_manager_date) {
										echo explode('/', $datas->approval_manager)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->approval_new_pic_date) {
										echo explode('/', $datas->approval_new_pic)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->approval_new_manager_date) {
										echo explode('/', $datas->approval_new_manager)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->approval_receive_acc_date) {
										echo explode('/', $datas->receive_acc)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->approval_acc_manager_date) {
										echo explode('/', $datas->approval_acc_manager)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
							</tr>
							<tr>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->approval_pic_date) {
										echo $datas->approval_pic_date;
									} ?>
								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->approval_manager_date) {
										echo $datas->approval_manager_date;
									} ?>
								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->approval_new_pic_date) {
										echo $datas->approval_new_pic_date;
									} ?>

								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->approval_new_manager_date) {
										echo $datas->approval_new_manager_date;
									} ?>

								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->approval_receive_acc_date) {
										echo $datas->approval_receive_acc_date;
									} ?>

								</td>
								<td class="border1 sign_name">Date : 
									<?php if ($datas->approval_acc_manager_date) {
										echo $datas->approval_acc_manager_date;
									} ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1" style="background-color: #cccccc">
						SECTION 1 : FILL BY OLD PIC <br>
						<b>ASSET IDENTIFICATION</b>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td style="padding: 5px 0px 5px 20px;" colspan="2">
									<b>FIXED ASSET CLASSIFICATION</b> <br>
									<label class="checkbox-inline">
										<?php if ($datas->classification_category == 'Building' || $datas->classification_category == 'Construction in Prog' || $datas->classification_category == 'Land' || $datas->classification_category == 'Land right') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>
										
										Building
									</label> 
									<br>
									<label class="checkbox-inline">
										<?php if ($datas->classification_category == 'Machinery & Equipment') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										Machine & Equipment
									</label> 
									<br>
									<label class="checkbox-inline">
										<?php if ($datas->classification_category == 'Tools, Furniture') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										Tools, Furniture, Fixture
									</label> 
									<br>
									<label class="checkbox-inline">
										<?php if ($datas->classification_category == 'Molding') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										Molding
									</label> 
									<br>
									<label class="checkbox-inline">
										<?php if ($datas->classification_category == 'Vehicle') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										Vehicle
									</label> 
									<br>
								</td>
								<td>
									<figcaption>Fixed Asset Picture</figcaption>
									<img src="{{ url('files/fixed_asset/transfer_location/'.$datas->new_picture)}}" alt="img asset" style="border: 1px solid black; max-width: 250px; max-height: 300px">
								</td>
							</tr>
							<tr>
								<td width="40%" style="font-weight: bold">FIXED ASSET NAME</td>
								<td width="1%">:</td>
								<td>{{ $datas->fixed_asset_name }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">LABEL FIXED ASSET NO (SAP ID)</td>
								<td>:</td>
								<td>{{ explode(".",$datas->new_picture)[0]  }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">OLD SECTION CONTROL</td>
								<td>:</td>
								<td>{{ $datas->old_section }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">OLD LOCATION</td>
								<td>:</td>
								<td>{{ $datas->old_location }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">OLD PIC</td>
								<td>:</td>
								<td>{{ $datas->old_pic }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">NEW SECTION CONTROL</td>
								<td>:</td>
								<td>{{ $datas->new_section }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">NEW LOCATION</td>
								<td>:</td>
								<td>{{ $datas->new_location }}</td>
							</tr>
							<tr>
								<td style="font-weight: bold">NEW PIC</td>
								<td>:</td>
								<td>{{ $datas->new_pic }}</td>
							</tr>

							<tr>
								<td style="font-weight: bold">TRANSFER REASON</td>
								<td>:</td>
								<td>{{ $datas->transfer_reason }}</td>
							</tr>
						</table>
					</tr>
				</table>

			</center>
		</div>
	</body>
	</html>