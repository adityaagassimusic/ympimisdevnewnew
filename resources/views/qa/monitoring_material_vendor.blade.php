@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	input {
		line-height: 22px;
	}
	thead>tr>th{
		/*text-align:center;*/
	}
	tbody>tr>td{
		/*text-align:center;*/
		color: black;
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	.content-wrapper{
		color: white;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.gambar {
	    width: 100%;
	    background-color: none;
	    border-radius: 5px;
	    margin-top: 15px;
	    display: inline-block;
	    /*border: 2px solid white;*/
	  }

	 .dataTables_info{
	 	color: black;
	 	text-align: left;
	 }

	 .dataTables_filter{
	 	color: black;
	 }

	 .select2-search__field {
	  	color: black;
	  }
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-left: 0px;padding-bottom: 0px;margin-bottom: 0px;padding-right: 0px">
			<!-- <div class="col-xs-4" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;">
				<span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode"></span>
			</div> -->
			<div class="col-xs-2" style="padding-right: 5px;padding-left: 0px">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;text-align: left">
				<select id="lot_status" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Pilih Lot Status">
					<option value=""></option>
					<option value="Lot OK">Lot OK</option>
					<option value="Lot Out">Lot Out</option>
				</select>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="form-group">
					<select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-4" style="padding-left: 5px;padding-right: 5px;vertical-align: middle;">
				<button class="btn btn-default pull-left" onclick="fetchAll()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134);padding-left: 5px;padding-right: 5px">
					Search
				</button>
				<button class="btn btn-default pull-left" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134);padding: 0px;padding-left: 2px;padding-right: 2px;margin-left: 2px;margin-left: 10px">
					<span style="font-size: 0.7vw;color: white;width: 100%;font-weight: bold;" id="periode"></span>
				</button>
			</div>
			<!-- <div class="col-xs-1" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:25px;vertical-align: middle;margin-top: 5px;margin-bottom: 5px">
				
			</div> -->
		</div>
		<!-- ARISA -->
		<div class="col-xs-12" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;vertical-align: middle;margin-bottom: 15px;padding-bottom: 0px">
			<span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">ARISA</span>
		</div>
		<div class="col-xs-5" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-left: 0px;padding-right: 0px">
			<div class="gambar" style="margin-top:0px;margin-bottom: 10px;height: 20vh">
				<table style="text-align:center;width:100%;height: 100%">
					<tr>
						<?php $index = 0; ?>
						@foreach($category_arisa1 as $category_arisa)
						<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 1vw;font-weight: bold;">{{$category_arisa->category}}
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_arisa1) ): ?>
							<td style="background-color: transparent;color: white;border: 0px">

							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_arisa2 as $category_arisa)
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OK
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OUT
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_arisa2) ): ?>
							<td style="background-color: transparent;color: white;width: 1%">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_arisa3 as $category_arisa)
						<td style="border: 1px solid #fff;color: white;font-size: 3vw;" id="lot_ok_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_arisa->category)))}}"><span id="lot_ok_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_arisa->category)))}}">99</span>
						</td>
						<td style="border: 1px solid #fff;font-size: 3vw;" id="lot_out_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_arisa->category)))}}"><span id="lot_out_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_arisa->category)))}}">99</span>
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_arisa3) ): ?>
							<td style="background-color: transparent;color: white;">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
				</table>
			</div>
		</div>
		<div class="col-xs-7" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-right: 0px">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;">
				<div class="box-body" style="padding: 2px">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<table id="table_lot_arisa" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
							<thead style="background-color: rgb(126,86,134);text-align: left;">
								<tr>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 2%">Serial Number</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Item Desc.</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty Check (Pcs)</th>
									<!-- <th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Defect</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty NG</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG Ratio (%)</th> -->
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Status</th>
								</tr>
							</thead>
							<tbody id="body_table_lot_arisa" style="text-align:center;">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- TRUE -->
		<div class="col-xs-12" style="background-color: #248517;padding-left: 5px;padding-right: 5px;vertical-align: middle;margin-bottom: 15px;padding-bottom: 0px;margin-top: 7px">
			<span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">TRUE</span>
		</div>
		<div class="col-xs-5" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-left: 0px;padding-right: 0px">
			<div class="gambar" style="margin-top:0px;margin-bottom: 10px;height: 20vh">
				<table style="text-align:center;width:100%;height: 100%">
					<tr>
						<?php $index = 0; ?>
						@foreach($category_true1 as $category_true)
						<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 1vw;font-weight: bold;">{{$category_true->category}}
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_true1) ): ?>
							<td style="background-color: transparent;color: white;border: 0px">

							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_true2 as $category_true)
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OK
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OUT
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_true2) ): ?>
							<td style="background-color: transparent;color: white;width: 1%">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_true3 as $category_true)
						<td style="border: 1px solid #fff;color: white;font-size: 3vw;" id="lot_ok_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_true->category)))}}"><span id="lot_ok_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_true->category)))}}">99</span>
						</td>
						<td style="border: 1px solid #fff;font-size: 3vw;" id="lot_out_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_true->category)))}}"><span id="lot_out_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_true->category)))}}">99</span>
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_true3) ): ?>
							<td style="background-color: transparent;color: white;">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
				</table>
			</div>
		</div>
		<div class="col-xs-7" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-right: 0px">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;">
				<div class="box-body" style="padding: 2px">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<table id="table_lot_true" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
							<thead style="background-color: #248517;text-align: left;">
								<tr>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Item Desc.</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty Check (Pcs)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Defect</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty NG</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG Ratio (%)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Status</th>
								</tr>
							</thead>
							<tbody id="body_table_lot_true" style="text-align:center;">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- KBI -->
		<!-- <div class="col-xs-12" style="background-color: #3d57b8;padding-left: 5px;padding-right: 5px;vertical-align: middle;margin-bottom: 15px;padding-bottom: 0px;margin-top: 7px">
			<span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">KBI</span>
		</div>
		<div class="col-xs-5" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-left: 0px;padding-right: 0px">
			<div class="gambar" style="margin-top:0px;margin-bottom: 10px;height: 20vh">
				<table style="text-align:center;width:100%;height: 100%">
					<tr>
						<?php $index = 0; ?>
						@foreach($category_kbi1 as $category_kbi)
						<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 1vw;font-weight: bold;">{{$category_kbi->category}}
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_kbi1) ): ?>
							<td style="background-color: transparent;color: white;border: 0px">

							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_kbi2 as $category_kbi)
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OK
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OUT
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_kbi2) ): ?>
							<td style="background-color: transparent;color: white;width: 1%">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_kbi3 as $category_kbi)
						<td style="border: 1px solid #fff;color: white;font-size: 3vw;" id="lot_ok_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_kbi->category)))}}"><span id="lot_ok_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_kbi->category)))}}">99</span>
						</td>
						<td style="border: 1px solid #fff;font-size: 3vw;" id="lot_out_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_kbi->category)))}}"><span id="lot_out_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_kbi->category)))}}">99</span>
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_kbi3) ): ?>
							<td style="background-color: transparent;color: white;">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
				</table>
			</div>
		</div>
		<div class="col-xs-7" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-right: 0px">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;">
				<div class="box-body" style="padding: 2px">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<table id="table_lot_kbi" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
							<thead style="background-color: #3d57b8;text-align: left;">
								<tr>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Item Desc.</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty Check (Pcs)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Defect</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty NG</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG Ratio (%)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Status</th>
								</tr>
							</thead>
							<tbody id="body_table_lot_kbi" style="text-align:center;">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> -->

		<!-- KBI -->
		<div class="col-xs-12" style="background-color: #f5a300;padding-left: 5px;padding-right: 5px;vertical-align: middle;margin-bottom: 15px;padding-bottom: 0px;margin-top: 7px">
			<span style="font-size: 20px;color: white;width: 100%;font-weight: bold;">CRESTEC INDONESIA</span>
		</div>
		<div class="col-xs-5" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-left: 0px;padding-right: 0px">
			<div class="gambar" style="margin-top:0px;margin-bottom: 10px;height: 20vh">
				<table style="text-align:center;width:100%;height: 100%">
					<tr>
						<?php $index = 0; ?>
						@foreach($category_crestec1 as $category_crestec)
						<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 1vw;font-weight: bold;">{{$category_crestec->category}}
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_crestec1) ): ?>
							<td style="background-color: transparent;color: white;border: 0px">

							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_crestec2 as $category_crestec)
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OK
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;font-weight: bold;">LOT OUT
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_crestec2) ): ?>
							<td style="background-color: transparent;color: white;width: 1%">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
					<tr>
						<?php $index = 0; ?>
						@foreach($category_crestec3 as $category_crestec)
						<td style="border: 1px solid #fff;color: white;font-size: 3vw;" id="lot_ok_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_crestec->category)))}}"><span id="lot_ok_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_crestec->category)))}}">99</span>
						</td>
						<td style="border: 1px solid #fff;font-size: 3vw;" id="lot_out_td_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_crestec->category)))}}"><span id="lot_out_{{str_replace(' ','_',str_replace('/','_',str_replace(',','_',$category_crestec->category)))}}">99</span>
						</td>
						<?php $index++ ?>
						<?php if ($index < count($category_crestec3) ): ?>
							<td style="background-color: transparent;color: white;">
							</td>
						<?php endif ?>
						@endforeach
					</tr>
				</table>
			</div>
		</div>
		<div class="col-xs-7" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;padding-right: 0px">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;">
				<div class="box-body" style="padding: 2px">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<table id="table_lot_crestec" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
							<thead style="background-color: #f5a300;text-align: left;">
								<tr>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 2%">Job Number</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Item Desc.</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty Check (Pcs)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Status</th>
								</tr>
							</thead>
							<tbody id="body_table_lot_crestec" style="text-align:center;">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/highcharts.js')}}"></script>

<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2({
			allowClear:true
		});
		fetchLotStatus();
		setInterval(fetchLotStatus, 600000);
	});

	function fetchAll() {
		fetchLotStatus();
	}

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
		fetchSelectMaterial();
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	function fetchSelectMaterial() {
		var data = {
			vendor:$('#vendor').val()
		}

		$.get('{{ url("fetch/qa/display/incoming/material_select") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#materialSelect').html('');
					var materialSelect = '';
					$.each(result.material_select, function(key,value){
						materialSelect += '<option value="'+value.material_number+'">'+value.material_number+' - '+value.material_description+'</option>';
					});
					$('#materialSelect').append(materialSelect);
				}
			}
		});
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchLotStatus() {
		$('#loading').show();

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			lot_status:$('#lot_status').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/vendor") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<span>'+result.monthTitle+'</span>');

					$.each(result.lot_count_arisa, function(key,value){
						$('#lot_ok_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_ok);
						$('#lot_out_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_out);

						if (parseInt(value.lot_ok) > 0) {
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","rgb(0, 166, 90)",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}else{
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}

						if (parseInt(value.lot_out) > 0) {
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","#dd4b39",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}else{
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}
					});
					$.each(result.lot_count_true, function(key,value){
						$('#lot_ok_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_ok);
						$('#lot_out_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_out);

						if (parseInt(value.lot_ok) > 0) {
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","rgb(0, 166, 90)",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}else{
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}

						if (parseInt(value.lot_out) > 0) {
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","#dd4b39",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}else{
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}
					});

					// $.each(result.lot_count_kbi, function(key,value){
					// 	$('#lot_ok_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_ok);
					// 	$('#lot_out_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_out);

					// 	if (parseInt(value.lot_ok) > 0) {
					// 		$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","rgb(0, 166, 90)",'important');
					// 		$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
					// 		$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
					// 	}else{
					// 		$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
					// 		$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
					// 		$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
					// 	}

					// 	if (parseInt(value.lot_out) > 0) {
					// 		$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","#dd4b39",'important');
					// 		$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
					// 		$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
					// 	}else{
					// 		$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
					// 		$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
					// 		$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
					// 	}
					// });

					$.each(result.lot_count_crestec, function(key,value){
						$('#lot_ok_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_ok);
						$('#lot_out_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).html(value.lot_out);

						if (parseInt(value.lot_ok) > 0) {
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","rgb(0, 166, 90)",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}else{
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
							$('#lot_ok_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}

						if (parseInt(value.lot_out) > 0) {
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","#dd4b39",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","white",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}else{
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("background-color","white",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("color","black",'important');
							$('#lot_out_td_'+value.material_category.replace(" ", "_").replace("/", "_").replace(",", "_")).css("font-weight","bold",'important');
						}
					});

					$('#body_table_lot_arisa').html("");
					var body_lot_arisa = "";

					$.each(result.lot_detail_arisa, function(key2,value2){
						if (value2.lot_status == 'LOT OUT') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						body_lot_arisa += '<tr>';
						body_lot_arisa += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.serial_number+'</td>';
						body_lot_arisa += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+' - '+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						body_lot_arisa += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
						
						body_lot_arisa += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.lot_status+'</td>';

						body_lot_arisa += '</tr>';
					});

					$('#body_table_lot_arisa').append(body_lot_arisa);

					
					$('#body_table_lot_true').html("");
					var body_lot_true = "";

					$.each(result.lot_detail_true, function(key2,value2){
						if (value2.lot_status == 'LOT OUT') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						body_lot_true += '<tr>';
						body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+' - '+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
						if (value2.ng_name != null) {
							body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.ng_name+'</td>';
						}else{
							body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;"></td>';
						}
						body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.total_ng+'</td>';
						body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.ng_ratio+'</td>';
						body_lot_true += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.lot_status+'</td>';

						body_lot_true += '</tr>';
					});

					$('#body_table_lot_true').append(body_lot_true);

					$('#body_table_lot_crestec').html("");
					var body_lot_crestec = "";

					$.each(result.lot_detail_crestec, function(key2,value2){
						if (value2.lot_status == 'LOT OUT') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						body_lot_crestec += '<tr>';
						body_lot_crestec += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.serial_number+'</td>';
						body_lot_crestec += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+' - '+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						body_lot_crestec += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
						
						body_lot_crestec += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.lot_status+'</td>';

						body_lot_crestec += '</tr>';
					});

					$('#body_table_lot_crestec').append(body_lot_crestec);

					// $('#body_table_lot_kbi').html("");
					// var body_lot_kbi = "";

					// $.each(result.lot_detail_kbi, function(key2,value2){
					// 	if (value2.lot_status == 'LOT OUT') {
					// 		var color = '#ffadad';
					// 	}else{
					// 		var color = '#fff';
					// 	}

					// 	body_lot_kbi += '<tr>';
					// 	body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+' - '+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
					// 	body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
					// 	if (value2.ng_name != null) {
					// 		body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.ng_name+'</td>';
					// 	}else{
					// 		body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;"></td>';
					// 	}
					// 	body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.total_ng+'</td>';
					// 	body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.ng_ratio+'</td>';
					// 	body_lot_kbi += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.lot_status+'</td>';

					// 	body_lot_kbi += '</tr>';
					// });

					// $('#body_table_lot_kbi').append(body_lot_kbi);

					$('#periode').html('Periode On '+result.monthTitle);
					$('#loading').hide();
				}else{
					$('#loading').hide();
				}
			}else{
				$('#loading').hide();
			}
		});
	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection