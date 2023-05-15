<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 7pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}
		.centera{
			text-align: center;
			vertical-align: middle !important;
		}
		.square {
			height: 5px;
			width: 5px;
			border: 1px solid black;
			background-color: transparent;
		}
		table {
			page-break-inside: avoid;
		}
	</style>
	<table class="table table-bordered">
		@foreach($cars as $car)
		<thead>
			<tr>
				<td colspan="2" rowspan="3" class="centera">
					<img width="100px" src="{{ public_path() . '/waves.jpg' }}" alt="">
				</td>
				<td colspan="5" rowspan="3" class="centera" style="font-size: 14px;font-weight: bold">CORRECTIVE ACTION REPORT</td>
				<td class="centera">Approved By</td>
				<td class="centera">Approved By</td>
				<td class="centera">Approved By</td>
			</tr>
			<tr>
				<td class="centera">
					@if($car->approved_gm == "Checked")
						{{$car->gmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td class="centera">
					@if($car->approved_dgm == "Checked")
						{{$car->dgmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td class="centera">
					@if($car->checked_manager == "Checked")
						{{$car->managername}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td class="centera">GM</td>
				<td class="centera">DGM</td>
				<td class="centera">Manager</td>
			</tr>
		</thead>
		<tbody>
			 <?php 
	          $tinjauan = $car->tinjauan; 
	          
	          if($tinjauan != NULL){
	            $split = explode(",", $tinjauan);
	            $hitungsplit = count($split);
	          }else{
	            $split = 0;
	          }
	        ?>
			<tr>
				<td colspan="2" width="20%">
					Kategori Komplain : {{ $car->kategori }}
				</td>
				<td colspan="2" width="20%">
					Departemen : {{ $car->department_name }}
				</td>
				<td colspan="2" width="20%">
					Section : {{ $car->section }}
				</td>
				<td colspan="2" width="20%">
					Date : <?php echo date('d F Y', strtotime($car->tgl_permintaan)) ?>
				</td>
				<td colspan="2" width="20%">
					Location : {{ $car->lokasi }}			
				</td>
			</tr>
			<tr>
				<td colspan="2" width="20%">Tinjauan 4M : </td>
				<td colspan="2" width="20%" class="" style="font-size: 10px">Man <input type="checkbox" class="centera" style="font-size: 10px;" 
				@if($split[0]=='1')
					checked @endif>
				</td>
				<td colspan="2" width="20%" class="" style="font-size: 10px">Material <input type="checkbox" class="centera" style="font-size: 10px;" 
				@if($split[1]=='1')
					checked @endif>
				</td>
				<td colspan="2" width="20%" class="" style="font-size: 10px">Machine <input type="checkbox" class="centera" style="font-size: 10px;" 
				@if($split[2]=='1')
					checked @endif</td>
				<td colspan="2" width="20%" class="" style="font-size: 10px">Method <input type="checkbox" class="centera" style="font-size: 10px;" 
				@if($split[3]=='1')
					checked @endif</td>	
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="10"><b style="font-size: 12px">Description</b> : <?= $car->deskripsi ?></td>
				<!-- <td rowspan="2" colspan="3" class="centera" style="font-weight: bold;font-size: 12px">Tinjauan 4M </td> -->
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="10"><b style="font-size: 12px">A. Immediately Action</b> : <?= $car->tindakan ?></td>
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="10"><b style="font-size: 12px">B. Possibility Cause</b> : <?= $car->penyebab ?></td>
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="10"><b style="font-size: 12px">C. Corrective Action</b> : <?= $car->perbaikan ?></td>
			</tr>
			<tr>
				<td class="centera">Prepared</td>
				<td class="centera">Checked</td>
				<td colspan="8"></td>
			</tr>
			<tr>
				<td rowspan="2" class="centera">
					@if($car->pic != null)
						{{$car->picname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" class="centera">
					@if($car->checked_chief == "Checked")
						{{$car->chiefname}}
					@elseif($car->checked_foreman == "Checked")
						{{$car->foremanname}}
					@elseif($car->checked_coordinator == "Checked")
						{{$car->coordinatorname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" colspan="8"></td>
			</tr>
			<tr></tr>
			<tr>
				@if($car->kategori == "Internal")
					<td class="centera">Leader</td>
					<td class="centera">Foreman</td>
				@else
					<td class="centera">Staff</td>
					<td class="centera">Chief</td>				
				@endif
				<td colspan="8"></td>
			</tr>
			<!-- <tr>
				<td colspan="10"></td>
			</tr>
			<tr>
				<td rowspan="2" colspan="6" class="centera" style="font-weight: bold;font-size: 20px">Verification Result</td>
				<td rowspan="2" class="centera">Dept In Charge</td>
				<td colspan="3" class="centera">QA</td>
			</tr>
			<tr>
				<td class="centera">Verified</td>
				<td class="centera">Checked</td>
				<td class="centera">Approved</td>
			</tr>
			<tr>
				<td colspan="2">Date Of Verification:</td>
				<td>Tanggal</td>
				<td colspan="3">Comment</td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">Status</td>
				<td>Open</td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="6"></td>
				<td class="centera">Manager</td>
				<td class="centera">QA Staff</td>
				<td class="centera">QA Chief</td>
				<td class="centera">QA Manager</td>
			</tr> -->
		</tbody>
		@endforeach
	</table>
	<span style="font-size: 8pt">No FM : YMPI/QA/FM/899</span>
</body>
</html>