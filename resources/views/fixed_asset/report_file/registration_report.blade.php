<!DOCTYPE html>
<html>
<head>
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
						<b>FIXED ASSET REGISTER APPLICATION</b> <br>
					</td>
					<td width="35%" class="border1" style="font-size: 12px;">
						Dokumen No : YMPI/FA/FM/005 <br>
						Revisi No : 00 <br>	
						Tanggal : 01 Juni 2014 <br>
					</td>
				</tr>
				<tr>
					<td class="border1">
						Date Of Submission : <b>{{ $datas->request_date }}</b><br>
						Reff. Number : <b>{{ $datas->form_number }}</b>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="80%" style="margin-top: 20px; margin-bottom: 20px" class="border1">
							<tr>
								<td class="border1" style="background-color: #ddd; width: 25%">Applicant</td>
								<td class="border1" style="background-color: #ddd; width: 25%">FA Control</td>
								<td class="border1" style="background-color: #ddd; width: 25%">Manager</td>
								<td class="border1" style="background-color: #ddd; width: 25%">Manager ACC</td>
							</tr>
							<tr>
								<td class="border1">
									{{ $datas->name }}
								</td>
								<td class="border1">
									<?php if ($datas->update_fa_at) {
										echo "Ismail Husen";
									} ?>									
								</td>
								<td class="border1">
									<?php if ($datas->manager_app_date) {
										echo explode('/', $datas->manager_app)[1];
									} else {
										echo "&nbsp;";
									} ?>
								</td>
								<td class="border1">
									<?php if ($datas->manager_acc_date) {
										echo "Romy Agung Kurniawan";
									} else {
										echo "&nbsp;";
									} ?>
								</td>
							</tr>
							<tr>
								<td class="border1">Date : <br>{{ explode(' ',$datas->created_at)[0] }}</td>
								<td class="border1">Date : <br>
									<?php if ($datas->update_fa_at) {
										echo explode(' ', $datas->update_fa_at)[0];
									} ?>
								</td>
								<td class="border1">Date :  <br>
									<?php if ($datas->manager_app_date) {
										echo explode(' ', $datas->manager_app_date)[0];
									} ?>
								</td>
								<td class="border1">Date :  <br>
									<?php if ($datas->manager_acc_date) {
										echo explode(' ', $datas->manager_acc_date)[0];
									} ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr style="background-color: #ddd">
					<td colspan="2" class="border1">
						SECTION 1 : FILL BY APPLICANT <br>
						<b>ASSET IDENTIFICATION</b>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="border1">
						<table width="100%">
							<tr>
								<td width="40%">NAME ON INVOICE</td>
								<td width="2%">:</td>
								<td>{{ $datas->asset_name }}</td>
							</tr>
							<tr>
								<td>FIXED ASSET NAME</td>
								<td>:</td>
								<td>{{ $datas->asset_name }}</td>
							</tr>
							<tr>
								<td>INVOICE NUMBER</td>
								<td>:</td>
								<td>{{ $datas->invoice_number }}</td>
							</tr>
							<tr>
								<td>CLASIFICATION CODE</td>
								<td>:</td>
								<td>{{ $datas->clasification_id }}</td>
							</tr>
							<tr>
								<td>CLASIFICATION DESC</td>
								<td>:</td>
								<td>{{ $datas->clasification_name }}</td>
							</tr>
							<tr>
								<td>VENDOR</td>
								<td>:</td>
								<td>{{ $datas->vendor }}</td>
							</tr>
							<tr>
								<td>CURENCY</td>
								<td>:</td>
								<td>{{ $datas->currency }}</td>
							</tr>
							<tr>
								<td>ORIGINAL AMOUNT</td>
								<td>:</td>
								<td>{{ $datas->amount }}</td>
							</tr>

							<tr>
								<td>AMOUNT IN  USD</td>
								<td>:</td>
								<td>{{ $datas->amount_usd }}</td>
							</tr>

							<tr>
								<td>SECTION CONTROL/LOKASI</td>
								<td>:</td>
								<td>{{ $datas->location }}</td>
							</tr>
							<tr>
								<td>PIC</td>
								<td>:</td>
								<td>{{ $datas->pic }}</td>
							</tr>
							<tr>
								<td>INVESTMENT NUMBER</td>
								<td>:</td>
								<td>{{ $datas->investment_number }}</td>
							</tr>
							<tr>
								<td>BUDGET NUMBER</td>
								<td>:</td>
								<td>{{ $datas->budget_number }}</td>
							</tr>
							<tr>
								<td>USAGE TERM</td>
								<td>:</td>
								<td>
									<?php if ($datas->usage_term == 'soon') {
										echo '<span>Not Use Yet (YYYY-MM-DD)</span><br>';
										echo '<span style="color:blue; font-weight: bold">Soon</span>';
									} else {
										echo '<span style="color:blue; font-weight: bold">Not Use Yet ('.$datas->usage_estimation.')</span><br>';
										echo '<span>Soon</span>';
									}?>
									
								</td>
							</tr>
						</table>
					</tr>
					<tr style="background-color: #ddd">
						<td colspan="2" class="border1">
							SECTION 2 : FILL BY ACCOUNTING <br>
							<b>DETAIL ASSET USAGE</b>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="border1">
							<table width="100%">
								<tr>
									<td width="40%">CATEGORIES</td>
									<td width="2%">:</td>
									<td>{{ $datas->category }}</td>
								</tr>
								<tr>
									<td>CATEGORIES CODE</td>
									<td>:</td>
									<td>{{ $datas->category_code }}</td>
								</tr>
								<tr>
									<td>SAP ID</td>
									<td>:</td>
									<td>{{ $datas->sap_id }}</td>
								</tr>
								<tr>
									<td>DEPRECIATION KEY</td>
									<td>:</td>
									<td>{{ $datas->depreciation_key }}</td>
								</tr>
								<tr>
									<td>USEFULL LIFE (YEAR)</td>
									<td>:</td>
									<td>{{ $datas->life_time }} Year</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

			</center>
		</div>
	</body>
	</html>