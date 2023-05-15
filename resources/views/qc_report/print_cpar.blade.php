<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
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

		/*table {
			page-break-inside: auto;
		}*/

		/*.page-break {
		    page-break-after: always;
		}*/

		@page { margin: 100px 50px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

	</style>
	
        <div class="footer">

            Page <span class="pagenum"></span>
        </div>
 	
	<table class="table table-bordered" style="table-layout: fixed;">
		<thead>
			<tr>
				<td colspan="3" class="centera" >
					<img width="120" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important">
				</td>
				<td colspan="5" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">CORRECTIVE & PREVENTIVE ACTION REQUEST</td>
				<td colspan="2" style="font-size: 9px;">
					No Dokumen : YMPI/QA/FM/988 <br>
					Revisi : 01<br>
					Tanggal : 08 Oktober 2019<br>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php $i=1;
			$jumlahparts = count($parts);

			if($jumlahparts < 2)
				$jumlah = 0;
			else if($jumlahparts == 2)
				$jumlah = 2;
			else if($jumlahparts == 3)
				$jumlah = 4;
			else if($jumlahparts == 4)
				$jumlah = 6;
			else if($jumlahparts == 5)
				$jumlah = 8;
			else if($jumlahparts == 6)
				$jumlah = 10;
			else if($jumlahparts == 7)
				$jumlah = 12;
			else if($jumlahparts == 8)
				$jumlah = 14;
			?>

			@foreach($cpars as $cpar)
			<tr>
				<td rowspan="{{ 12 + $jumlah }}" style="width: 5%">{{ $i++ }}</td>
				<td colspan="5" style="border: none !important">To : <b>{{$cpar->name}}</b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">CPAR No : <b>{{$cpar->cpar_no}}</b></td>
			</tr>
			<tr>
				<td colspan="5" style="border: none !important">Location : <b>{{$cpar->lokasi}}</b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Source Of Complaint : <b>{{$cpar->sumber_komplain}} - {{$cpar->kategori_komplain}}</b></td>
			</tr>
			<tr>
				<td colspan="5" style="border: none !important">Issue Date : <b><?php echo date('d F Y', strtotime($cpar->tgl_permintaan)) ?></b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Department : <b><?= ucwords($cpar->department_name)?></b></td>
			</tr>
			<tr>
				<td colspan="5" style="border: none !important">Request Due Date : <b><?php echo date('d F Y', strtotime($cpar->tgl_balas)) ?></b><br>(CPAR Return to QA)</td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">
					@if($cpar->destination_code != null)
						Customer : <b>{{$cpar->destination_name}}</b>
					@elseif($cpar->vendor != null)
						Vendor : <b>{{$cpar->vendorname}}</b>
					@elseif($cpar->penemu_ng != null)
						Penemu NG : <b>{{$cpar->penemu_ng}}</b>
					@endif
				</td>
			</tr>
			<tr>
				<td>Part Item</td>
				<td colspan="2">Part Description</td>
				<td>Invoice / Lot No</td>
				<td>Sample / Check Qty</td>
				<td>Defect Qty</td>
				<td>% Defect</td>
				<td colspan="2"></td>
			</tr>

			<?php 
			$jumlahparts = count($parts);
			if($jumlahparts != 0) { 

			?>
			@foreach($parts as $part)
			<tr>
				<td rowspan="2">{{$part->part_item}}</td>
				<td rowspan="2" colspan="2">{{$part->material_description}}</td>
				<td rowspan="2">{{$part->no_invoice}}</td>
				<td rowspan="2">{{$part->sample_qty}}</td>
				<td rowspan="2">{{$part->defect_qty}}</td>
				<td rowspan="2">{{$part->defect_presentase}}</td>
				<td rowspan="2" colspan="2"></td>
			</tr>
			<tr></tr>
			@endforeach
			<?php }
			else { 
			?>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2" colspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2" colspan="2"></td>
			</tr>
			<tr></tr>
			<?php } ?>
			<?php if($jumlahparts != 0) { ?> 
			<tr>
				<td colspan="9"><p style="font-size: 12px">Detail Problem : </p><?= $part->detail_problem ?></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>	
			<?php } ?>
			<!-- <tr><td colspan="8"></td></tr> -->
			<tr>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Checked By</td>
				<?php if ($cpar->kategori_approval == "CPAR DGM Produksi" || $cpar->kategori_approval == "CPAR GM Produksi") { ?>
					<td>Known By</td>
				<?php } else {  ?>
					
				<?php } ?>

				<?php if ($cpar->kategori_approval == "CPAR GM Produksi") { ?>
					<td>Known By</td>
				<?php } else {  ?>
					
				<?php } ?>
				<td>Received By</td>
				<?php if ($cpar->kategori_approval == "CPAR DGM Produksi") { ?>
					<td colspan="4" rowspan="4">&nbsp;</td>
				<?php } else if ($cpar->kategori_approval == "CPAR GM Produksi") {  ?>
					<td colspan="3" rowspan="4">&nbsp;</td>
				<?php } else {  ?>
					<td colspan="5" rowspan="4">&nbsp;</td>
				<?php } ?>	
				
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->staff != null)
						{{$cpar->staffname}}
					@elseif($cpar->leader != null)
						{{$cpar->leadername}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->checked_chief == "Checked")
						{{$cpar->chiefname}}
					@elseif($cpar->checked_foreman == "Checked")
						{{$cpar->foremanname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->checked_manager == "Checked")
						{{$cpar->managername}}
					@else
						&nbsp;
					@endif
				</td>
				<?php if ($cpar->kategori_approval == "CPAR DGM Produksi" || $cpar->kategori_approval == "CPAR GM Produksi") { ?>
				<td rowspan="2" style="vertical-align: middle;">
					<?php if ($cpar->kategori_approval == "CPAR DGM Produksi" || $cpar->kategori_approval == "CPAR GM Produksi") { ?>
						@if($cpar->approved_dgm == "Checked")
							{{$cpar->dgmname}}
						@else
							&nbsp;
						@endif
					<?php } ?>
				</td>
				<?php } else {  ?>

				<?php } ?>



				<?php if ($cpar->kategori_approval == "CPAR GM Produksi") { ?>
				<td rowspan="2" style="vertical-align: middle;text-align: center">
					<?php if ($cpar->kategori_approval == "CPAR GM Produksi") { ?>
						@if($cpar->approved_gm == "Checked")
							@if(count($cparss[0]->ttd) > 0)
							<!-- <img width="50" src="{{url($cparss[0]->ttd)}}" alt="" style="vertical-align: middle !important"> -->
							@endif
							{{$cpar->gmname}}
						@else
							&nbsp;
						@endif
					<?php } ?>
				</td>
				<?php } else {  ?>

				<?php } ?>

				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->received_manager == "Received")
						{{$cpar->name}}
					@else
						&nbsp;
					@endif
				</td>
				<!-- <td colspan="2" rowspan="2" style="vertical-align: middle;">&nbsp;</td> -->
					
				</td>
			</tr>
			<tr></tr>
			<tr>
				@if($cpar->kategori == "Internal")
				<td>Staff / Leader</td>
				<td>Chief / Foreman</td>
				@else
				<td>Staff</td>
				<td>Chief</td>				
				@endif
				<td>Manager</td>
				<?php if ($cpar->kategori_approval == "CPAR DGM Produksi" || $cpar->kategori_approval == "CPAR GM Produksi") { ?>
					<td>DGM</td>
				<?php } else {  ?>

				<?php } ?>
				<?php if ($cpar->kategori_approval == "CPAR GM Produksi") { ?>
					<td>GM</td>
				<?php } else {  ?>

				<?php } ?>
				<td>Manager</td>
				<!-- <td colspan="2"></td> -->
			</tr>
			<tr>
				<td rowspan="2">2</td>
				<td colspan="9">Immediate Action (Filled By QA)</td>
			</tr>
			<tr>
				<td colspan="9"><?= $cpar->tindakan ?></td>
			</tr>

			<tr>
				<td rowspan="2">3</td>
				<td colspan="9">Verification Status</td>
			</tr>
			<tr>
				<td colspan="9">{{$cpar->status_name}}</td>
			</tr>
			<tr>
				<td rowspan="5">4</td>
				<td colspan="9"></td>
			</tr>
			<!-- 
			<tr>
				<td colspan="9">Cost Estimation</td>
			</tr> -->
<!-- 			<tr>
				<td colspan="9"><?= $cpar->cost ?> </td>
			</tr> -->
			<tr>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Known By</td>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td rowspan="2">
					@if($cpar->posisi == "QA" || $cpar->posisi == "QA2" || $cpar->posisi == "QAmanager" || $cpar->posisi == "QAFIX")
						@if($cpar->staff != null)
							{{$cpar->staffname}}
						@elseif($cpar->leader != null)
							{{$cpar->leadername}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($cpar->posisi == "QA2" || $cpar->posisi == "QAmanager" || $cpar->posisi == "QAFIX")
						@if($cpar->staff != null)
							{{$cpar->chiefname}}
						@elseif($cpar->leader != null)
							{{$cpar->foremanname}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($cpar->posisi == "QAmanager" || $cpar->posisi == "QAFIX")
						{{$cpar->managername}}
					@else
						&nbsp;
					@endif
				</td>
				<td colspan="6" rowspan="2"></td>
			</tr>
			<tr></tr>
			<tr>
				<td>Staff</td>
				<td>Chief</td>
				<td>Manager</td>
				<td colspan="6"></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>