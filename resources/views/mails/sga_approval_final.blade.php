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
			<span style="font-weight: bold; color: purple; font-size: 24px;">SMALL GROUP ACTIVITY (SGA)<br>スモールグループ活動</span><br>
			@if(ISSET($data['finish']))
			<p style="font-size: 18px;font-weight: bold;color: green">Pengajuan Final SGA Telah Disetujui.</p>
			@endif
			@if(ISSET($data['reject']))
			<p style="font-size: 18px;font-weight: bold;color: red">Pengajuan Final SGA Tidak Disetujui.</p>
			<p style="font-size: 18px;font-weight: bold;color: red">Reason<br>{{$data['reason']}}</p>
			@endif
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;width: 1%;border: 1px solid black">Juara<br><small>優勝</small></th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">Team No.<br><small>チーム番号</small></th>
							<th style="font-size: 15px;width: 4%;border: 1px solid black">Bagian<br><small>職場</small></th>
							<!-- <th style="font-size: 15px;width: 2%;border: 1px solid black">Hasil Penilaian Seleksi<br><small>予選審査結果</small></th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">Hasil Penilaian Final<br><small>決勝審査結果</small></th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">40% Seleksi<br><small>40％予選</small></th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">60% Final<br><small>60%決勝</small></th>
							<th style="font-size: 15px;width: 2%;border: 1px solid black">Total Penilaian<br><small>審査合計</small></th> -->
							<th style="font-size: 15px;width: 3%;border: 1px solid black">Preview PDF<br><small>プレビューPDF</small></th>
							<th style="font-size: 15px;width: 3%;border: 1px solid black">Hadiah<br><small>報酬</small></th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1 ?>
						<?php $total_all = 0; ?>
						<?php for($i = 0; $i < count($data['teams']);$i++){ ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: center;">{{$index}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_no}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data['teams'][$i]->team_name}}</td>
							<!-- <td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->total_nilai_seleksi}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->total_nilai_final}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->persen_seleksi}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->persen_final}}</td>
							<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data['teams'][$i]->totals}}</td> -->
							 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: center;">
							 	@if($data['teams'][$i]->file_pdf != null)
							 	<a style="text-decoration: none;" href="{{url('data_file/sga/pdf/'.$data['teams'][$i]->file_pdf)}}">File PDF <small>プレビューPDF</small></a>
							 	@endif
							 </td>
							 <td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;"><span style="float: left">Rp.</span><span style="float: right">{{$data['teams'][$i]->hadiah}},00</span></td>
							 <?php $hadiah_new = str_replace(',', '', $data['teams'][$i]->hadiah);
							 $total_all = $total_all + $hadiah_new; ?>
						</tr>
						<?php $index++; ?>
						<?php } ?>
						<tr>
							<td colspan="4" style="border:1px solid black; font-size: 15px; height: 20;text-align: right;font-weight: bold;">TOTAL 合計</td>
							<td colspan="" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;font-weight: bold;"><span style="float: left">Rp.</span><span style="float: right"><?php echo number_format($total_all,2,",",".") ?></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					@if(!ISSET($data['finish']) && !ISSET($data['reject']))
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
					(下をクリックしてください)<br>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sga/report/'.$data['periode'].'/'.$data['next_remark']) }}">&nbsp;&nbsp;&nbsp; Approve <small>(承認)</small> &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/sga/report/'.$data['periode'].'/'.$data['next_remark']) }}">&nbsp;&nbsp;&nbsp; Reject <small>(却下)</small> &nbsp;&nbsp;&nbsp;</a>
					@endif
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