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
			This is an automatic notification. Please do not reply to this address.

			<h1>Machine Error Information (設備エラー情報)</h1>

			<h2>An error has occurred for {{$data['time']}} for the {{$data['machine']}} Machine. Thank you</h2>
			<?php if (isset($data['jepang'])) { ?>
				<h2>本機械 "{{$data['machine']}}" ２時間ほどエラーが発生した。　ありがとうございます</h2>
			<?php } ?>
		</center>
	</div>
</body>
</html>