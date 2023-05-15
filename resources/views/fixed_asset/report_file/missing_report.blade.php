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
		.sign {
			font-size: 8px;
		}
		.sign_name {
			font-size: 9px;
		}
		.bg_bold {
			color: blue;
			text-decoration: underline;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<table width="100%">
				<tr>

					<td width="70%" style="font-size: 13px" rowspan="2">
						<b>PT.YAMAHA MUSICAL PRODUCTS INDONESIA</b> <br><br>
						<b>FIXED ASSET MISSING FORM REPORT</b> <br>
					</td>
					<td width="25%" class="border1" style="font-size: 11px;">
						Dokumen No : YMPI/FA/FM/006 <br>
						Revisi No : 00 <br>	
						Tanggal : 01 Agustus 2014 <br>
					</td>
				</tr>
				<tr>
					<td class="border1" style="font-size: 11px;">
						Date Of Submission : {{ $datas->create_at }}<b></b><br>
						Reff. Number : <b>{{ $datas->form_number }}</b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="100%" style="margin-top: 20px; margin-bottom: 20px" class="border1">
							<tr>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Applicant</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">FA Control</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Manager Applicant</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">GM</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Acc Manager</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Director</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Presdir</td>
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
									<?php if ($datas->fa_app_date) {
										echo explode('/', $datas->fa_app)[1];
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
									<?php if ($datas->presdir_app_date) {
										echo explode('/', $datas->presdir_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->upload_doc_date) {
										echo explode('/', $datas->fa_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
							</tr>
							<tr>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->pic_app_date) {
										echo $datas->pic_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->fa_app_date) {
										echo $datas->fa_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->manager_app_date) {
										echo $datas->manager_app_date;
									} ?>

								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->gm_app_date) {
										echo $datas->gm_app_date;
									} ?>

								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->acc_manager_app_date) {
										echo $datas->acc_manager_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->director_app_date) {
										echo $datas->director_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->presdir_app_date) {
										echo $datas->presdir_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->upload_doc_date) {
										echo $datas->upload_doc_date;
									} ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1" style="background-color: #cccccc">
						SECTION 1 : FILL BY APPLICANT <br>
						<b>ASSET IDENTIFICATION</b>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td colspan="2"></td>
								<td><b>PICTURE</b></td>
							</tr>
							<tr>
								<td style="padding: 5px 0px 5px 20px;" colspan="2">
									<b>FIXED ASSET CLASSIFICATION</b> <br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->clasification == 'Building' || $datas->clasification == 'Construction in Prog' || $datas->clasification == 'Land' || $datas->clasification == 'Land right') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Building</span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->clasification == 'Machinery & Equipment' || $datas->clasification == 'Machinery') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Machine & Equipment</span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->clasification == 'Tools, Furniture') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Tools, Furniture, Fixture</span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->clasification == 'Molding') {											
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Molding</span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->clasification == 'Vehicle') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Vehicle</span>
									</label> 
									<br>
								</td>
								<td>
									<!-- <figcaption>Fixed Asset Picture</figcaption> -->
									<img src="{{ url('files/fixed_asset/missing_picture/'.$datas->new_picture)}}" alt="img asset" style="border: 1px solid black; max-width: 250px; max-height: 100px">
								</td>
							</tr>
							<tr>
								<td width="40%">FIXED ASSET NAME</td>
								<td colspan="2">: {{ $datas->fixed_asset_name }}</td>
							</tr>
							<tr>
								<td>FIXED ASSET NO</td>
								<td colspan="2">: {{ $datas->fixed_asset_id }}</td>
							</tr>
							<tr>
								<td>SECTION CONTROL</td>
								<td colspan="2">: {{ $datas->section_control }}</td>
							</tr>
							<tr>
								<td>DISPOSAL REASON</td>
								<td colspan="2">: {{ $datas->reason }}</td>
							</tr>
						</table>
					</tr>
					<tr>
						<td colspan="2" class="border1" style="background-color: #cccccc">
							SECTION 2 : FILL BY APPLICANT FOR MISSING ITEM <br>
							<b>IMPROVEMENT STATEMENT</b>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1">
							<table width="100%">
								<tr>
									<td colspan="2">We apologize for  loss(missing) of mentioned fixed asset, to avoid the condition improvement plan is undertaken</td>
								</tr>
								<tr>
									<td width="40%">REASON FOR MISSING</td>
									<td>: {{ $datas->missing_reason }}</td>
								</tr>
								<tr>
									<td>IMPROVEMENT PLAN</td>
									<td>: {{ $datas->improvement_plan }}</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1" style="background-color: #cccccc">
							SECTION 3 : FILL BY ACCOUNTING <br>
							<b>ASSET VALUATION</b>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1">
							<table width="100%">
								<tr>
									<td width="40%">FA NO. (SAP ID)</td>
									<td>: {{ $datas->fixed_asset_id }}</td>
								</tr>
								<tr>
									<td>ACQUISITION COST</td>
									<td>: $ {{ $datas->acquisition_cost }}</td>
								</tr>
								<tr>
									<td>ACQUISITION DATE</td>
									<td>: {{ $datas->acquisition_date }}</td>
								</tr>
								<tr>
									<td>NET BOOK VALUE</td>
									<td>: $ {{ $datas->book_value }}</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

			</center>
		</div>
	</body>
	</html>