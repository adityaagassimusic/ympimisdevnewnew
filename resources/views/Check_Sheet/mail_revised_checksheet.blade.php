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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: black; font-size: 24px;">Revised Checksheet Information</span><br>

			@php
			$order = 'th';

			if($data['checksheet']->rev == 1){
				$order = 'st';
			}else if($data['checksheet']->rev == 2){
				$order = 'nd';
			}else if($data['checksheet']->rev == 3){
				$order = 'rd';
			}
			@endphp

			<span style="font-weight: bold; color: black; font-size: 18px;">{{ $data['checksheet']->rev . $order }} revision at {{ $data['checksheet']->updated_at }}</span><br>

		</center>
		<br>
		<div style="width: 50%; margin: auto;">
			<span style="font-weight: bold;">Checksheet Data:</span>
			<table style="border:1px solid black; border-collapse: collapse; width: 100%;">
				<tbody>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Checksheet No.</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->id_checkSheet }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Carier</th>
						<td style="border:1px solid black;">{{ $data['carier'][$data['checksheet']->carier] }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Container No.</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->countainer_number }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Seal No.</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->seal_number }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Vehicle Reg No.</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->no_pol }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Destination</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->destination }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Invoice</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->invoice }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">DO Number</th>
						<td style="border:1px solid black;">{{ $data['checksheet']->do_number }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">Stuffing Date</th>
						<td style="border:1px solid black;">{{ date('l, d M Y', strtotime($data['checksheet']->Stuffing_date)) }}</td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #ffcd51;">On Or About</th>
						<td style="border:1px solid black;">{{ date('l, d M Y', strtotime($data['checksheet']->etd_sub)) }}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<br>
		<br>
		<center>
			<span style="font-weight: bold;">&#8650; <i>Click Here For</i> &#8650;</span><br>

			{{-- <a style="background-color: #ccff90; text-decoration: none; color: black;" href="{{ url('show/CheckSheet/'.$data['checksheet']->id) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Approve&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> --}}
			<a style="background-color: #25b2fd; text-decoration: none; color: black;" href="{{ url('show/CheckSheet/'.$data['checksheet']->id) }}">&nbsp;Checksheet Detail&nbsp;</a>
		</center>
		<br>
		<br>
	</div>
</body>
</html>