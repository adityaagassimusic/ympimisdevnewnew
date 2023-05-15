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
			<?php $pattern = "/Final/i";  ?>
			@if(!preg_match($pattern,$data['periode']))
			<span style="font-weight: bold; color: purple; font-size: 24px;">SMALL GROUP ACTIVITY (SGA)<br>スモールグループ活動</span><br>
			@else
			<span style="font-weight: bold; color: purple; font-size: 24px;">SMALL GROUP ACTIVITY (SGA)<br>スモールグループ活動</span><br>
			@endif
			@if(ISSET($data['finish']))
			<p style="font-size: 18px;font-weight: bold;color: green">Pengajuan Seleksi SGA Telah Disetujui.</p>
			@endif
			@if(ISSET($data['reject']))
			<p style="font-size: 18px;font-weight: bold;color: red">Pengajuan Seleksi SGA Tidak Disetujui.</p>
			<p style="font-size: 18px;font-weight: bold;color: red">Reason<br>{{$data['reason']}}</p>
			@endif
			<p>
				<span style="font-weight: bold;">*Note 備考:</span><br>
				<span style="background-color: #c3e157;">&nbsp;&nbsp;Top 5 (Maju ke Final) 上位５（決勝進出）&nbsp;&nbsp;</span>
			</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">#</th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">Team No.<br><small>チーム番号</small></th>
							<th style="font-size: 15px;width: 4%;border: 1px solid black">Team Name<br><small>チーム名</small></th>
							<th style="font-size: 15px;width: 10%;border: 1px solid black">Team Title<br><small>件名</small></th>
							<?php $asesor_id = []; ?>
							<?php for($i = 0; $i < count($data['sga_asesor']);$i++){ ?>
								<?php if (count(explode(' ',$data['sga_asesor'][$i]->asesor_name)) > 1){ ?>
									<th style="font-size: 15px;width: 2%;border: 1px solid black">{{explode(' ',$data['sga_asesor'][$i]->asesor_name)[0]}} {{explode(' ',$data['sga_asesor'][$i]->asesor_name)[1]}}</th>
								<?php }else{ ?>
									<th style="font-size: 15px;width: 2%;border: 1px solid black">{{explode(' ',$data['sga_asesor'][$i]->asesor_name)[0]}}</th>
								<?php } ?>
							<?php array_push($asesor_id,$data['sga_asesor'][$i]->asesor_id) ?>
							<?php } ?>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">Total Nilai<br><small>審査合計</small></th>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">File PDF<br><small>ファイルPDF</small></th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1 ?>
						<?php for($i = 0; $i < count($data['teams']);$i++){ ?>
						@if(!preg_match($pattern,$data['periode']))
							<?php if ($index < 6) {
								$bgcolor = '#c3e157';
							}else{
								$bgcolor = 'none';
							} ?>
						@else
							<?php $bgcolor = 'none'; ?>
						@endif
						<tr style="background-color: {{$bgcolor}}">
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$index}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_no}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_name}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_title}}</td>
							<?php
							$total = 0;
							for($k = 0; $k < count($asesor_id);$k++){
								for($j = 0; $j < count($data['sga_result']);$j++){
									if ($data['sga_result'][$j]->asesor_id == $asesor_id[$k] && $data['sga_result'][$j]->team_no == $data['teams'][$i]->team_no) { ?>
										<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['sga_result'][$j]->total_nilai}}</td>
										
									<?php $total = $total + $data['sga_result'][$j]->total_nilai;
									}
								}
							}
							 ?>
							 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$total}}</td>
							 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: center;">
							 	@if($data['teams'][$i]->file_pdf != null)
							 	<a style="text-decoration: none;" href="{{url('data_file/sga/pdf/'.$data['teams'][$i]->file_pdf)}}">File Presentation</a>
							 	@endif
							 </td>
						</tr>
						<?php $index++; ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					@if(!preg_match($pattern,$data['periode']))
						@if(!ISSET($data['finish']) && !ISSET($data['reject']))
						<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
						(下をクリックしてください)<br>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sga/report/'.$data['periode'].'/'.$data['next_remark']) }}">&nbsp;&nbsp;&nbsp; Approve <small>(承認)</small> &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/sga/report/'.$data['periode'].'/'.$data['next_remark']) }}">&nbsp;&nbsp;&nbsp; Reject <small>(却下)</small> &nbsp;&nbsp;&nbsp;</a>
						@endif
					@else
						@if(!ISSET($data['finish']) && !ISSET($data['reject']))
						<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
						(下をクリックしてください)<br>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sga/report/'.$data['periode'].'/'.$data['next_remark']) }}">&nbsp;&nbsp;&nbsp; Approve <small>(承認)</small> &nbsp;&nbsp;&nbsp;</a>
						@endif
					@endif
					<br>
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