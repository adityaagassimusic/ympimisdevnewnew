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
			<span style="font-weight: bold; color: purple; font-size: 24px;">YPM Contest Approval<br>YPMコンテストの承認</span><br>
			@if($data['statuses'] == 'Final')
			<br>
			<p style="font-size: 18px;font-weight: bold;color: red">YPM Contest Telah Direject oleh {{$data['employee_id']}}.</p>
			@endif
			<p>
				<span style="font-weight: bold;">*Note 備考:</span><br>
				<span style="background-color: #c3e157;">&nbsp;&nbsp;Top 6 (Menjadi Juara) <small>上位６（優勝）</small>&nbsp;&nbsp;</span>
			</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">Juara<br><small>優勝</small></th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">Team Dept.<br><small>チーム部</small></th>
							<th style="font-size: 15px;width: 4%;border: 1px solid black">Team Name<br><small>チーム名</small></th>
							<th style="font-size: 15px;width: 10%;border: 1px solid black">Team Title<br><small>件名</small></th>
							<th style="font-size: 15px;width: 3%;border: 1px solid black">Hadiah<br><small>報酬</small></th>
							<!-- <?php $judges_id = []; ?>
							<?php for($i = 0; $i < count($data['judges_all']);$i++){ ?>
								<?php if (count(explode(' ',$data['judges_all'][$i]->judges_name)) > 1){ ?>
									<th style="font-size: 15px;width: 2%;border: 1px solid black">{{explode(' ',$data['judges_all'][$i]->judges_name)[0]}} {{explode(' ',$data['judges_all'][$i]->judges_name)[1]}}</th>
								<?php }else{ ?>
									<th style="font-size: 15px;width: 2%;border: 1px solid black">{{explode(' ',$data['judges_all'][$i]->judges_name)[0]}}</th>
								<?php } ?>
							<?php array_push($judges_id,$data['judges_all'][$i]->judges_id) ?>
							<?php } ?>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">Total Nilai<br><small>審査合計</small></th>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">File PDF<br><small>ファイルPDF</small></th> -->
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1 ?>
						<?php for($i = 0; $i < count($data['teams']);$i++){ ?>
							<?php if ($index < 7) {
								$bgcolor = '#c3e157'; ?>
								<tr style="background-color: {{$bgcolor}}">
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: center;">{{$index}}</td>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_dept}}</td>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_name}}</td>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_title}}</td>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;"><span style="float: left">Rp.</span><span style="float: right">{{number_format($data['teams'][$i]->hadiah,2,',','.')}}</span></td>
									<!-- <?php
									$total = 0;
									for($k = 0; $k < count($judges_id);$k++){
										for($j = 0; $j < count($data['results']);$j++){
											if ($data['results'][$j]->asesor_id == $judges_id[$k] && $data['results'][$j]->team_id == $data['teams'][$i]->team_id) { ?>
												<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['results'][$j]->nilai}}</td>
												
											<?php $total = $total + $data['results'][$j]->nilai;
											}
										}
									}
									 ?>
									 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$total}}</td>
									 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: center;">
									 	@if($data['teams'][$i]->file_pdf_contest != null)
									 	<a style="text-decoration: none;" href="{{url('data_file/ypm/pdf/'.$data['teams'][$i]->file_pdf_contest)}}">ファイルPDF</a>
									 	@endif
									 </td> -->
								</tr>
							<?php }else{
								$bgcolor = 'none';
							} ?>
						
						<?php $index++; ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<!-- <br>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br> -->
					<!-- <a style="width: 50px;text-decoration: underline;" href="{{ url('index/qa/certificate/code') }}">Monitoring 監視</a> -->
				<br>
			</div>
		</center>
	</div>
</body>
</html>