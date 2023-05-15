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

		@if($data[0]->posisi != "user")

		<p style="font-size: 20px;">Settlement Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Settlement Payment <?= date('d M y', strtotime($data[0]->submission_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Title</td>
					<td>: <?= $data[0]->title ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Requested By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
				<?php $no = 1; 
			
				$totalsuspend = 0;
				$totalsettle = 0;

				foreach($data as $susp) {
					$totalsuspend += $susp->amount_suspend;
					$totalsettle += $susp->amount_settle;
				}

				?>

				<tr>
					<td style="width: 25%;"><b>Total Suspense</b></td>
					<td><b>: <?= $data[0]->currency ?> <?= number_format($totalsuspend,2,",",".") ?></b></td>
				</tr>

				<tr>
					<td style="width: 25%;"><b>Total Settlement</b></td>
					<td><b>: <?= $data[0]->currency ?> <?= number_format($totalsettle,2,",",".") ?></b></td>
				</tr>

				<tr>
					<td style="width: 25%"><b>Difference</b></td>
					
					@if($totalsettle > $totalsuspend)
					<td style="color: red;">: &#8593; <b><?= $data[0]->currency ?> <?= number_format($totalsuspend - $totalsettle,2,",",".") ?></b></td>

					@elseif($totalsettle < $totalsuspend)
					<td style="color: green;">: &#8595; <b><?= $data[0]->currency ?> <?= number_format($totalsuspend - $totalsettle,2,",",".") ?></b></td>
					
					@else
					<td style="padding: 5px"> 0</td>
					@endif
				</tr>


			</tbody>
		</table>

    <br>

    <table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 1%; border:1px solid black;color:white;">No PR</th>
					<th style="width: 3%; border:1px solid black;color:white;">Detail</th>
					<th style="width: 1%; border:1px solid black;color:white;">Suspense</th>
					<th style="width: 1%; border:1px solid black;color:white;">Settlement</th>
					<th style="width: 1%; border:1px solid black;color:white;">Difference</th>
					<th style="width: 1%; border:1px solid black;color:white;">File Lampiran</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->no_pr ?></td></td>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->detail ?></td>
					<td style="border:1px solid black; text-align: left !important;"><?= number_format($datas->amount_suspend,2,",",".") ?></td>
					<td style="border:1px solid black; text-align: left !important;"><?= number_format($datas->amount_settle,2,",",".") ?></td>
					@if($datas->amount_settle > $datas->amount_suspend)
						<td style="border:1px solid black; text-align: left !important;color: red">&#8593; <?= number_format($datas->amount_suspend - $datas->amount_settle,2,",",".") ?></td>
						@elseif($datas->amount_settle < $datas->amount_suspend)
						<td style="border:1px solid black; text-align: left !important;color: green">&#8595;+<?= number_format($datas->amount_suspend - $datas->amount_settle,2,",",".") ?></td>
						@else
						<td style="border:1px solid black;text-align: left !important;">&nbsp; 0</td>
					@endif
					<td style="border:1px solid black; text-align: left !important;"><a target="_blank" class="fa fa-paperclip" href="{{ url("files/cash_payment/settlement/".$datas->nota) }}"> File</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Settlement Payment?</i></span>
		<br><br>



		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("settlement/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "staff_acc")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("settlement/approvestaffacc/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "manager_acc")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("settlement/approvemanageracc/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "direktur")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("settlement/approvedirektur/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif

		@if($data[0]->posisi != "acc")

		<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("settlement/comment/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("settlement/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>
		@endif
		
		<br><br>
<!-- 
		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url('settlement/monitoring') }}">Settlement Payment Monitoring</a>

		<br><br> -->

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

		<p style="font-size: 18px;">Settlement Payment Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>

		<h2>Settlement Payment <?= date('d M y', strtotime($data[0]->submission_date)) ?> Not Approved</h2>
		
		<?php if ($data[0]->alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $data[0]->alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="{{ url('report/settlement/'.$data[0]->id) }}">Settlement Payment Check</a>
		<br>
		<a href="{{url('index/settlement')}}">Settlement Payment List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
		</center>
	</div>
</body>
</html>