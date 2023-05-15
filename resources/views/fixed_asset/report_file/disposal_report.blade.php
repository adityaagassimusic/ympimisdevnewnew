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
			text-align: center;
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
						<b>FIXED ASSET DISPOSAL APPLICATION</b> <br>
						<img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_0.png') }}" width="150">
					</td>
					<td width="30%" class="border1" style="font-size: 11px;">
						Dokumen No : YMPI/FA/FM/006 <br>
						Revisi No : 00 <br>	
						Tanggal : 01 Agustus 2014 <br>
					</td>
				</tr>
				<tr>
					<td class="border1" style="font-size: 11px;">
						Submission Date <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_1.png') }}" width="30"> : {{ $datas->create_at }}<b></b><br>
						Reff. Number <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_2.png') }}" width="40"> : <b>{{ $datas->form_number }}</b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="100%" style="margin-top: 20px; margin-bottom: 20px" class="border1">
							<tr>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Applicant</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">FA Control</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Manager Applicant</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Manager Disposal</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">DGM</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">GM</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Acc Manager</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Director</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">Presdir</td>
								<td class="border1 sign_name" style="width: 1%; background-color: #cccccc">PIC Disposal</td>
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
									<?php if ($datas->manager_disposal_app_date) {
										echo explode('/', $datas->manager_disposal_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->dgm_app_date) {
										echo explode('/', $datas->dgm_app)[1];
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
									<?php if ($datas->manager_acc_app_date) {
										echo explode('/', $datas->manager_acc_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1 sign_name">
									<?php if ($datas->director_fin_app_date) {
										echo explode('/', $datas->director_fin_app)[1];
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
									<?php if ($datas->new_pic_app_date) {
										echo explode('/', $datas->new_pic_app)[1];
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
									<?php if ($datas->manager_disposal_app_date) {
										echo $datas->manager_disposal_app_date;
									} ?>

								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->dgm_app_date) {
										echo $datas->dgm_app_date;
									} ?>

								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->gm_app_date) {
										echo $datas->gm_app_date;
									} ?>

								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->manager_acc_app_date) {
										echo $datas->manager_acc_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->director_fin_app_date) {
										echo $datas->director_fin_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->presdir_app_date) {
										echo $datas->presdir_app_date;
									} ?>
								</td>
								<td class="border1 sign">Date : <br>
									<?php if ($datas->new_pic_app_date) {
										echo $datas->new_pic_app_date;
									} ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1" style="background-color: #cccccc">
						SECTION 1 : FILL BY APPLICANT <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_3.png') }}" width="90"><br>
						<b>ASSET IDENTIFICATION <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_4.png') }}" width="90"></b>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td style="padding: 5px 0px 5px 20px;" colspan="2">
									<b>FIXED ASSET CLASSIFICATION <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_5.png') }}" width="100"></b> <br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->category == 'Building' || $datas->category == 'Construction in Prog' || $datas->category == 'Land' || $datas->category == 'Land right') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Building <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_6.png') }}" width="50"></span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->category == 'Machinery & Equipment') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Machine & Equipment <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_7.png') }}" width="70"></span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->category == 'Tools, Furniture') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Tools, Furniture, Fixture <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_8.png') }}" width="120"></span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->category == 'Molding') {											
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Molding <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_9.png') }}" width="30"></span>
									</label> 
									<br>
									<label class="checkbox-inline">
										<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if ($datas->category == 'Vehicle') {
											echo "<b>V</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										} else {
											echo "<b>&nbsp;</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}?>

										<span>Vehicle <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_10.png') }}" width="30"></span>
									</label> 
									<br>
								</td>
								<td>
									<!-- <figcaption>Fixed Asset Picture</figcaption> -->
									<img src="{{ url('files/fixed_asset/disposal/'.$datas->new_picture)}}" alt="img asset" style="border: 1px solid black; max-width: 250px; max-height: 100px">
								</td>
							</tr>
							<tr>
								<td width="40%">FIXED ASSET NAME <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_11.png') }}" width="80"></td>
								<td colspan="2">: {{ $datas->fixed_asset_name }}</td>
							</tr>
							<tr>
								<td>FIXED ASSET NO <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_12.png') }}" width="70"></td>
								<td colspan="2">: {{ $datas->fixed_asset_id }}</td>
							</tr>
							<tr>
								<td>SECTION CONTROL <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_13.png') }}" width="50"></td>
								<td colspan="2">: {{ $datas->section_control }}</td>
							</tr>
							<tr>
								<td>DISPOSAL REASON <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_14.png') }}" width="50"></td>
								<td colspan="2">: {{ $datas->reason }}</td>
							</tr>
						</table>
					</tr>
					<tr>
						<td colspan="2" class="border1" style="background-color: #cccccc">
							SECTION 2 : FILL BY ACCOUNTING <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_15.png') }}" width="60"><br>
							<b>ASSET VALUATION <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_16.png') }}" width="60"></b>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1">
							<table width="100%">
								<tr>
									<td width="40%">REGISTRATION AMOUNT <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_17.png') }}" width="60"></td>
									<td>: $ {{ $datas->registration_amount }}</td>
								</tr>
								<tr>
									<td>REGISTRATION DATE <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_18.png') }}" width="50"></td>
									<td>: {{ $datas->registration_date }}</td>
								</tr>
								<tr>
									<td>VENDOR <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_19.png') }}" width="40"></td>
									<td>: {{ $datas->vendor }}</td>
								</tr>
								<tr>
									<td>NET BOOK VALUE <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_20.png') }}" width="60"></td>
									<td>: $ {{ $datas->book_value }}</td>
								</tr>
								<tr>
									<td>NO INVOICE <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_21.png') }}" width="90"></td>
									<td>: {{ $datas->invoice_number }}</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1" style="background-color: #cccccc">
							SECTION 3 : FILL BY DISPOSAL OFFICER <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_22.png') }}" width="90"><br>
							<b>ASSET DISPOSAL IN CHARGE <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_23.png') }}" width="60"></b>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1">
							<table width="100%">
								<tr>
									<td width="40%">PIC IN CHARGE <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_24.png') }}" width="60"></td>
									<td>: {{ $datas->pic_incharge }}</td>
								</tr>
								<tr>
									<td>DISPOSAL MODE <img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_25.png') }}" width="60"></td>
									<td>: 
										<?php if ($datas->mode == 'SALE') {
											echo '<span class="bg_bold">SALE <img src="'.public_path('files/fixed_asset/disposal/jpg_font/FAD_27.png').'" width="40"></span>';
										} else {
											echo '<span>SALE <img src="'.public_path('files/fixed_asset/disposal/jpg_font/FAD_27.png').'" width="40"></span>';
										} ?>

										<?php if ($datas->mode == 'SCRAP') {
											echo '<span class="bg_bold">SCRAP <img src="'.public_path('files/fixed_asset/disposal/jpg_font/FAD_28.png').'" width="40"></span>';
										} else {
											echo '<span>SCRAP <img src="'.public_path('files/fixed_asset/disposal/jpg_font/FAD_28.png').'" width="40"></span>';
										} ?>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										Maximum disposal date is 30 days after President Director signed or stated
										below for imported asset :  ______/_______/________ <br>
										<img src="{{ public_path('files/fixed_asset/disposal/jpg_font/FAD_26.png') }}" width="280">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

			</center>
		</div>
	</body>
	</html>