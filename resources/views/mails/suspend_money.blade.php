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

		<p style="font-size: 20px;">Suspend Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Suspend Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Requested By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Requested For</td>
					<td>: <?= $data[0]->emp_id ?> - <?= $data[0]->emp_name ?></td></td>
				</tr>
			</tbody>
		</table>

		<br>

		<table class="table table-bordered" style="font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0" width="80%">
			<thead>
				<tr>
					<td><span style="text-align: left; font-size: 17px;">Mengajukan Suspend Dengan Keterangan : </span> </td>
				</tr>
			</thead>
        <tbody align="center">
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 20px; font-weight: bold; width: 50%; height: 30; background-color: #d4e157"><?= $data[0]->title ?></td>
            
          </tr>
          <tr>
          	<?php 
            $total = 0;
            foreach($data as $datas) {
            	$total = $total + $datas->amount_detail;
            }
            ?>
            <td colspan="2" style="border:1px solid black; font-size: 20px; font-weight: bold; width: 50%; height: 40; background-color: #a2cf6e">Total Cash : <?= $data[0]->currency ?> <?= number_format($total,2,',','.') ?> </td>
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
		<span style="font-weight: bold;font-size: 18px"><i>Please Check Your Back Account For Transfer Money</i></span>
		<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For Upload Bill</i> &#8650;</span><br>
		<a href="{{ url('index/settlement/user') }}">Upload Settlement</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		</center>
	</div>
</body>
</html>