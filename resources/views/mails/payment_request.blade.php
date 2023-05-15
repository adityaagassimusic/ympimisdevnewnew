<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
	</style>
</head>
<body>
	<div>
		<center>
		
		@if($data[0]->posisi == "manager" || $data[0]->posisi == "dgm" || $data[0]->posisi == "gm")

		<p style="font-size: 20px;">Payment Request <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Payment Request {{$data[0]->supplier_name}} <?= date('d M y', strtotime($data[0]->payment_due_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Vendor</td>
					<td>: <?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: <?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Term</td>
					<td>: <?= $data[0]->payment_term ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Kind Of</td>
					<td>: <?= $data[0]->kind_of ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Due Date</td>
					<td>: <?= date('d-M-y', strtotime($data[0]->payment_due_date)) ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Attach Document</td>
					<td>: <?= ucwords(str_replace(",",", ",str_replace("_"," ",$data[0]->attach_document))) ?> </td>
				</tr>
				<tr>
					<td style="width: 25%; ">Created By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;color: white;">Invoice</th>
					<th style="width: 3%; border:1px solid black;color: white;">Net Payment</th>
					@if($data[0]->category == "Billing")
					<th style="width: 3%; border:1px solid black;color: white;">PO Amount</th>
					<th style="width: 2%; border:1px solid black;color: white;">Difference</th>
					@elseif($data[0]->category == "General")
					<th style="width: 3%; border:1px solid black;color: white;">PO/DO Amount</th>
					<th style="width: 2%; border:1px solid black;color: white;">Difference</th>
					@endif
					<th style="width: 1%; border:1px solid black;color: white;">Invoice / Kwitansi</th>
					<th style="width: 1%; border:1px solid black;color: white;">Surat Jalan</th>
					<th style="width: 1%; border:1px solid black;color: white;">PO</th>
					<th style="width: 1%; border:1px solid black;color: white;">Faktur Pajak</th>
					<th style="width: 2%; border:1px solid black;color: white;">Lampiran</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: center;"><?= $datas->invoice ?></td></td>
					<td style="border:1px solid black; text-align: right;"><?= $data[0]->currency ?> <?= number_format($datas->amount_detail,2,",",".") ?></td>
					@if($data[0]->category == "Billing")
					<td style="border:1px solid black; text-align: right;"><?= $data[0]->currency ?> <?= number_format($datas->mirai_amount,2,",",".") ?></td>
					<?php 
					$difference = (float) $datas->amount_detail - (float) $datas->mirai_amount;
					?>
					<?php if( $difference == 0) { ?> 
					<td style="border:1px solid black; text-align: right;background-color: green;"><?= $data[0]->currency ?> <?= number_format($difference,2,",",".") ?></td>
					<?php } else { ?>
					<td style="border:1px solid black; text-align: right;background-color: red;color: white;"><?= $data[0]->currency ?> <?= number_format($difference,2,",",".") ?></td>
					<?php } ?>

					
					@elseif($data[0]->category == "General")

					<td style="border:1px solid black; text-align: right;"><?= $data[0]->currency ?> <?= number_format($datas->amount_detail,2,",",".") ?></td>
					<td style="border:1px solid black; text-align: right;background-color: green;"><?= $data[0]->currency ?> 0</td>

					@endif

					@if(str_contains($data[0]->attach_document,'invoice'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@elseif(str_contains($data[0]->attach_document,'receipt'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif

					@if(str_contains($data[0]->attach_document,'surat_jalan'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif

					@if(str_contains($data[0]->attach_document,'purchase_order'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif

					@if(str_contains($data[0]->attach_document,'faktur_pajak'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif
					
					<td style="border:1px solid black; text-align: right;"><a href="{{ url('files/invoice/'.$datas->attach_file) }}" target="_blank" class="fa fa-paperclip"> File</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<br><br>

		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Payment Request?</i></span>
		<br><br>

		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "dgm")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvedgm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "gm")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvegm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

		<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span>
		<br>
		<a href="{{ url('payment_request/verifikasi/'.$data[0]->id) }}">Payment Request Verifikasi</a>
		<br>
		<a href="{{ url('index/payment_request/monitoring') }}">Payment Request Monitoring</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		<!-- @elseif($data[0]->posisi == "gm")

		<p style="font-size: 20px;">Payment Request <br> (支払リクエスト) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Payment Request {{$data[0]->kind_of}} <?= date('d M y', strtotime($data[0]->payment_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Vendor (業者)</td>
					<td>: <?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount (数量)</td>
					<td>: Rp. <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Term (支払条件)</td>
					<td>: <?= $data[0]->payment_term ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Due Date (納期)</td>
					<td>: <?= date('d-M-y', strtotime($data[0]->payment_due_date)) ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>
		<span style="font-weight: bold;"><i>Do you want to Approve This Payment Request?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>

		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvegm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/reject/".$data[0]->id) }}">&nbsp; Reject (却下）&nbsp;</a>

		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""> -->
		

		@elseif($data[0]->posisi == "acc_verif")

		<p style="font-size: 20px;">Payment Request <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Payment Request {{$data[0]->supplier_name}} <?= date('d M y', strtotime($data[0]->payment_due_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Vendor</td>
					<td>: <?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: <?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Term</td>
					<td>: <?= $data[0]->payment_term ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Kind Of</td>
					<td>: <?= $data[0]->kind_of ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Due Date</td>
					<td>: <?= date('d-M-y', strtotime($data[0]->payment_due_date)) ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Attach Document</td>
					<td>: <?= $data[0]->attach_document ?> </td>
				</tr>
				<tr>
					<td style="width: 25%; ">Created By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;color: white;">Invoice</th>
					<th style="width: 3%; border:1px solid black;color: white;">Net Payment</th>

					@if($data[0]->category == "Billing")
					<th style="width: 3%; border:1px solid black;color: white;">PO Amount</th>
					<th style="width: 2%; border:1px solid black;color: white;">Difference</th>
					@elseif($data[0]->category == "General")
					<th style="width: 3%; border:1px solid black;color: white;">PO/DO Amount</th>
					<th style="width: 2%; border:1px solid black;color: white;">Difference</th>
					@endif
					<th style="width: 1%; border:1px solid black;color: white;">Invoice / Kwitansi</th>
					<th style="width: 1%; border:1px solid black;color: white;">Surat Jalan</th>
					<th style="width: 1%; border:1px solid black;color: white;">PO</th>
					<th style="width: 1%; border:1px solid black;color: white;">Faktur Pajak</th>
					<th style="width: 2%; border:1px solid black;color: white;">Lampiran</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: center;"><?= $datas->invoice ?></td></td>
					<td style="border:1px solid black; text-align: right;"><?= $data[0]->currency ?> <?= number_format($datas->amount_detail,2,",",".") ?></td>

					@if($data[0]->category == "Billing")

					<td style="border:1px solid black; text-align: right;"><?= $data[0]->currency ?> <?= number_format($datas->mirai_amount,2,",",".") ?></td>
					<?php 
						$difference = (float) $datas->amount_detail - (float) $datas->mirai_amount;
					?>

					<?php if( $difference == 0) { ?> 
					<td style="border:1px solid black; text-align: right;background-color: green;"><?= $data[0]->currency ?> <?= number_format($difference,2,",",".") ?></td>
					<?php } else { ?>
					<td style="border:1px solid black; text-align: right;background-color: red;color: white;"><?= $data[0]->currency ?> <?= number_format($difference,2,",",".") ?></td>
					<?php } ?>

					@elseif($data[0]->category == "General")

					<td style="border:1px solid black; text-align: right;"><?= $data[0]->currency ?> <?= number_format($datas->amount_detail,2,",",".") ?></td>
					<td style="border:1px solid black; text-align: right;background-color: green;"><?= $data[0]->currency ?> 0</td>

					@endif

					@if(str_contains($data[0]->attach_document,'invoice'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@elseif(str_contains($data[0]->attach_document,'receipt'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif

					@if(str_contains($data[0]->attach_document,'surat_jalan'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif

					@if(str_contains($data[0]->attach_document,'purchase_order'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif


					@if(str_contains($data[0]->attach_document,'faktur_pajak'))
					<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>
					@else
					<td style="border: 1px solid black; text-align: center; background-color: #feccfe; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>
					@endif

					<td style="border:1px solid black; text-align: right;"><a href="{{ url('files/invoice/'.$datas->attach_file) }}" target="_blank" class="fa fa-paperclip"> File</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<br><br>
		
		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span>
		<br>
		<a href="{{ url('payment_request/verifikasi/'.$data[0]->id) }}">Payment Request Verifikasi</a>
		<br>
		<a href="{{ url('index/payment_request/monitoring') }}">Payment Request Monitoring</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

		<p style="font-size: 18px;">Payment Request Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>

		<h2>Payment Request {{$data[0]->supplier_name}} <?= date('d M y', strtotime($data[0]->payment_due_date)) ?> <br><span style="color: red;">Not Approved</span></h2>
		<!-- <h2>Payment Request {{$data[0]->kind_of}} <?= date('d M y', strtotime($data[0]->payment_date)) ?> Not Approved</h2> -->
		
		<?php if ($data[0]->alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $data[0]->alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="{{ url('report/payment_request/'.$data[0]->id) }}">Payment Request Check</a>
		<br>
		<a href="{{url('billing/payment_request/all')}}">Payment Request List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
		</center>
	</div>
</body>
</html>