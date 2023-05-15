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
		.button {
		  background-color: #4CAF50; /* Green */
		  border: none;
		  color: white;
		  padding: 10px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  cursor: pointer;
		  border-radius: 4px;
		  cursor: pointer;
		}
		.button_reject {
		  background-color: #fa3939; /* Green */
		  border: none;
		  color: white;
		  padding: 10px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  cursor: pointer;
		  border-radius: 4px;
		  cursor: pointer;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">SURAT IZIN KELUAR PERUSAHAAN DITOLAK<br>外出申請書</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<p style="font-size: 25px;font-weight: bold;">Permintaan Anda dengan Requesi ID : <span style="color: red">{{$data['leave_request']->request_id}}</span> telah ditolak oleh Manager.<br>Reason : {{$data['reason']}}</p>
			<!-- <span style="font-weight: bold; font-size: 26px;">Request ID<br>{{$data['leave_request']->request_id}}</span> -->
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 60%">
				<!-- <p>
					<b>Thanks & Regards,</b>
				</p>
				<p>PT. Yamaha Musical Products Indonesia<br>
					Jl. Rembang Industri I / 36<br>
					Kawasan Industri PIER - Pasuruan<br>
					Phone   : 0343 – 740290<br>
					Fax.    : 0343 - 740291
				</p> -->
			</div>
		</center>
	</div>
</body>
</html>