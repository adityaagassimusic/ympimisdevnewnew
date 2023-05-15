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
			<p style="font-size: 18px;">
			<br>(Last Update: {{ date('d-M-Y H:i:s') }})
			</p>
			This is an automatic notification. Please do not reply to this address.

			<table class="table table-bordered" style="width: 75%">
          		<tr id="show-att">
            		<td>
			              	<table style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
			            	<thead>
					              <tr>
					                <td><br></td>
					              </tr>       
					              <tr>
					                <td colspan="4" style="text-align: center;font-weight: bold;font-size: 20px">FILE APPROVAL CONFIRMATION</td>
					              </tr>
					              <tr>
					                <td><br></td>
					              </tr>
					              <tr>
					                <td style="font-size: 13px;width: 20%; font-weight: bold"></td>
					                <td style="font-size: 13px;width: 18%; font-weight: bold">No Pengajuan</td>
					                <td style="font-size: 13px;width: 5%; font-weight: bold">:</td>
					                <td style="font-size: 13px;">{{ $detail->no_transaction }}</td>
					              </tr> 
					                  <?php
					                    $identitas = explode("/",$detail->nik);
					                  ?> 
					              <tr>
					              	<td style="font-size: 13px;width: 20%; font-weight: bold"></td>
					              	<td style="font-size: 13px;width: 18%; font-weight: bold">Nama</td>
					              	<td style="font-size: 13px;width: 5%; font-weight: bold">:</td>
					              	<td style="font-size: 13px;">{{ $identitas[1] }}</td>
					              </tr>
					              <tr>
					              	<td style="font-size: 13px;width: 20%; font-weight: bold"></td>
					              	<td style="font-size: 13px;width: 18%; font-weight: bold">Jabatan</td>
					              	<td style="font-size: 13px;width: 5%; font-weight: bold">:</td>
					              	<td style="font-size: 13px;">{{ $identitas[2] }}</td>
					              </tr>
					              <tr>
					              	<td style="font-size: 13px;width: 20%; font-weight: bold"></td>
					                <td style="font-size: 13px;width: 18%; font-weight: bold">Department</td>
					                <td style="font-size: 13px;width: 5%; font-weight: bold">:</td>
					                <td style="font-size: 13px;">{{ $detail->department }}</td>
					              </tr>
					              <tr>
					              	<td style="font-size: 13px;width: 20%; font-weight: bold"></td>
					                <td style="font-size: 13px;width: 18%; font-weight: bold">Description</td>
					                <td style="font-size: 13px;width: 5%; font-weight: bold">:</td>
					                <td style="font-size: 13px;">{{ $detail->description }}</td>
					              </tr>
					              <tr>
					              	<td style="font-size: 13px;width: 20%; font-weight: bold"></td>
					                <td style="font-size: 13px;width: 18%; font-weight: bold">Tanggal Pengajuan</td>
					                <td style="font-size: 13px;width: 5%; font-weight: bold">:</td>
					                <td style="font-size: 13px;">{{ $detail->date }}</td>
					              </tr>
					              <tr>
					                <td style="font-size: 13px;width: 20%; font-weight: bold"><br></td>
					              </tr>
			            	</thead>
			        	</table>
			        </td>
			    </tr>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('adagio/data/report/'.$detail->id) }}">Mutasi Verification</a><br>
		</center>
	</div>
</body>
</html>