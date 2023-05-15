<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 10px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		#isi2 > thead > tr > td {
			text-align: center;
		}

		#isi2 > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		.line{
		   width: 100%; 
		   text-align: center; 
		   border-bottom: 1px solid #000; 
		   line-height: 0.1em;
		   margin: 10px 0 20px;  
		}

		.line span{
		   background:#fff; 
		   padding:0 10px;
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
					<td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic">
						<div class="line">
							&nbsp;
						<div>
					</td>
				</tr>
				<tr>
					<td colspan="10" style="font-weight: bold;font-size: 16px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA (PT. YMPI)</td>
				</tr>
				<tr>
					<td colspan="5" style="font-weight: bold;font-size: 15px">Detail Jurnal Payment</td>
					<td colspan="5" style="text-align: left;font-size: 13px">Phone : (0343) 740290</td>
				</tr>
				<tr>
					<td colspan="5" style="text-align: left;font-size: 13px">Kawasan Industri PIER - Pasuruan (Jawa Timur Indonesia)</td>
					<td colspan="5" style="text-align: left;font-size: 13px">Fax : (0343) 740291</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic">
						<div class="line">
							&nbsp;
						<div>
					</td>
				</tr>
				<tr>
					<td colspan="1" style="font-size: 14px">Supplier Name</td>
					<td colspan="9" style="font-size: 14px;font-weight: bold;">: {{$jurnal->supplier_name}}</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 14px;">Jurnal Date</td>
					<td colspan="9" style="font-size: 14px;font-weight: bold;">: <?= date('d-M-y', strtotime($jurnal->jurnal_date)) ?></td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 14px;">Bank Information</td>
					<td colspan="9" style="font-size: 14px;font-weight: bold;">: {{$jurnal->bank_branch}} - {{$jurnal->bank_beneficiary_no }} - {{$jurnal->bank_beneficiary_name }}</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 14px;">Amount</td>
					<td colspan="9" style="font-size: 14px;font-weight: bold;">: {{$jurnal->currency}} {{$jurnal->amount}}</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 14px;">ID Payment</td>
					<td colspan="9" style="font-size: 14px;font-weight: bold;">: {{$jurnal->id_payment}}</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>
			</thead>
		</table>
	</header>
	<main>
		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; " id="isi">
			<thead>
				<tr style="font-size: 12px">
					<td colspan="10" style="padding:10px;height: 15px; width:1%; background-color:#6B717E; font-weight: bold; border: 1px solid black;font-size: 14px;color: white;">Invoice Section</td>
				</tr>

				<tr style="font-size: 12px">
					<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">No</td>
					<td colspan="3" style="width:7%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Invoice No</td>
					<td colspan="2" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Amount</td>
					<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">PPN</td>
					<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">PPH</td>
					<td colspan="2" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Net Payment</td>
				</tr>
			</thead>
			<tbody>
				<?php 
				$no = 1; 
				$total_amount = 0;
				$total_ppn = 0;
				$total_pph = 0;
				$total_net = 0;

				?>
				@foreach($jurnal_invoice as $pay)
				<tr>
					<td colspan="1" style="font-size: 12px;height: 30px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
					<td colspan="3" style="font-size: 12px;border: 1px solid black;">{{ $pay->invoice_no }}</td>
					<td colspan="2" style="font-size: 12px;border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($pay->amount,2,",","."); ?></td>
					<td colspan="1" style="font-size: 12px;border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($pay->ppn,2,",","."); ?></td>
					<td colspan="1" style="font-size: 12px;border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($pay->pph,2,",","."); ?></td>
					<td colspan="2" style="font-size: 12px;border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($pay->net_payment,2,",","."); ?></td>
				</tr>
				<?php 
					$total_amount += $pay->amount;
					$total_ppn += $pay->ppn;
					$total_pph += $pay->pph;
					$total_net += $pay->net_payment;
				?>
				<?php $no++; ?>

				@endforeach

				<tr>
					<td colspan="4" style="font-size: 14px;height: 40px;text-align: right;padding: 0;;font-weight: bold;">Total :</td>
					<td colspan="2" style="font-size: 14px;text-align: right;padding-right: 5px;font-weight: bold;"><?= number_format($total_amount,2,",","."); ?></td>
					<td colspan="1" style="font-size: 14px;text-align: right;padding-right: 5px;font-weight: bold;"><?= number_format($total_ppn,2,",","."); ?></td>
					<td colspan="1" style="font-size: 14px;text-align: right;padding-right: 5px;font-weight: bold;"><?= number_format($total_pph,2,",","."); ?></td>
					<td colspan="2" style="font-size: 14px;text-align: right;padding-right: 5px;font-weight: bold;"><?= number_format($total_net,2,",","."); ?></td>
				</tr>
			</tbody>
		</table>	


		<table style="width: 100%; font-family: TimesNewRoman; margin-top: 20px; border-collapse: collapse;" id="isi2" >
			<thead>
				<tr style="font-size: 12px">
					<td colspan="10" style="padding:10px;height: 15px; width:1%; background-color:#FFE8E1; font-weight: bold; border: 1px solid black;font-size: 14px;color: black;">Jurnal Section</td>
				</tr>

				<tr style="font-size: 12px">
					<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Seq</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Reference</td>
					<td colspan="1" style="width:2%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Cost Center</td>
					<td colspan="1" style="width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Type</td>
					<td colspan="2" style="width:5%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">GL Account</td>
					<td colspan="2" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Amount</td>
					<td colspan="2" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;font-size: 14px;">Note</td>
				</tr>
			</thead>
			<tbody>
				@foreach($jurnal_detail as $detail)
				<tr>
					<td colspan="1" style="font-size: 12px;height: 30px; border: 1px solid black;text-align: center;padding: 0">{{ $detail->seq_id }}</td>
					<td colspan="1" style="font-size: 12px;border: 1px solid black;">{{ $detail->reference }}</td>
					<td colspan="1" style="font-size: 12px;border: 1px solid black;">{{ $detail->cost_center }}</td>
					<td colspan="1" style="font-size: 12px;border: 1px solid black;">{{ $detail->type }}</td>
					<td colspan="2" style="font-size: 12px;border: 1px solid black;">{{ $detail->gl_account }} - {{ $detail->gl_desc }}</td>
					<td colspan="2" style="font-size: 12px;border: 1px solid black;text-align: right;padding-right: 5px"><?= $detail->currency ?> <?= $detail->amount ?></td>
					<td colspan="2" style="font-size: 12px;border: 1px solid black;text-align: left">{{ $detail->note }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>	
	</main>

	
	<!-- <footer>
		<div class="footer">
			<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: center;" border="1">
				<thead>
					<tr>
						<td colspan="1" style="width:15%;height: 10px; border: 1px solid black;text-align: center;padding: 0">Created By</td>
						<td colspan="1" style="width:15%;">Approved By</td>
						<td colspan="1" style="width:15%;">Approved By</td>
						<td colspan="1" style="width:15%;">Approved By</td>
					</tr>

				</thead>
				<tbody>
					<tr>
						<td colspan="1" style="height: 30px">
							{{$jurnal->created_name}}
						</td>
						<td colspan="1" style="height: 30px">
							
						</td>
						<td colspan="1" style="height: 30px">
							
						</td>
						<td colspan="1" style="height: 30px">
							
						</td>
					</tr>
					<tr>
						<td colspan="1"></td>
						<td colspan="1"></td>
						<td colspan="1"></td>
						<td colspan="1"></td>
					</tr>
				</tbody>
			</table>
	    </div>
	</footer> -->


</body>
</html>