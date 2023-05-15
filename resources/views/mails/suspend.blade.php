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

		<p style="font-size: 20px;">Suspense Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>
			Suspense Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?>
			
			@if($data[0]->posisi == "acc")

			Fully Approved

			@endif

		</h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Requested By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: <b><?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </b></td>	
				</tr>
			</tbody>
		</table>

		<br>

		<table class="table table-bordered" style="font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0" width="80%">
			<thead>
				<tr>
					<td><span style="text-align: left; font-size: 17px;">Mengajukan Suspense Dengan Keterangan : </span> </td>
				</tr>
			</thead>
        <tbody align="center">
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 20px; font-weight: bold; width: 50%; height: 70; background-color: #d4e157"><?= $data[0]->title ?></td>
          </tr>
        </tbody>            
    </table>

    <br>

    <table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 1%; border:1px solid black;color:white;">No PR</th>
					<th style="width: 3%; border:1px solid black;color:white;">Detail</th>
					<th style="width: 1%; border:1px solid black;color:white;">Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->no_pr ?></td></td>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->detail ?></td>
					<td style="border:1px solid black; text-align: right;"><?= $datas->currency ?> <?= number_format($datas->amount_detail,2,',','.') ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>


		@if($data[0]->posisi != "acc")

		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Suspense Payment?</i></span>
		<br><br>

		@endif



		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "staff_acc")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvestaffacc/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "manager_acc")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvemanageracc/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "direktur")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvedirektur/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif

		@if($data[0]->posisi != "acc")

		<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/comment/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>
		@endif
		
		<br><br>

		<!-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url('suspend/monitoring') }}">Suspense Payment Monitoring</a>

		<br><br> -->

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

		<p style="font-size: 18px;">Suspense Payment Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>

		<h2>Suspense Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?> Not Approved</h2>
		
		<?php if ($data[0]->alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $data[0]->alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="{{ url('report/suspend/'.$data[0]->id) }}">Suspense Payment Check</a>
		<br>
		<a href="{{url('index/suspend')}}">Suspense Payment List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
		</center>
	</div>
</body>
</html>