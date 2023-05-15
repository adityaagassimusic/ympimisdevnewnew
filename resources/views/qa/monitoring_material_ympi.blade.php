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
	    /*border-radius: 5px;*/
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
					<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
						@foreach($vendors as $vendor)
						<option value="{{$vendor->vendor}}">{{$vendor->vendor}}</option>
						@endforeach
					</select>
					<input type="text" name="vendor" id="vendor" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="form-group">
					<select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;vertical-align: middle;">
				<button class="btn btn-default pull-left" onclick="fetchAll()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134);padding-left: 5px;padding-right: 5px">
					Search
				</button>
				<button class="btn btn-default" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134);padding: 0px;padding-left: 2px;padding-right: 2px;margin-left: 2px">
					<span style="font-size: 0.7vw;color: white;width: 100%;font-weight: bold;" id="periode"></span>
				</button>
			</div>
			<!-- <div class="col-xs-1" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:25px;vertical-align: middle;margin-top: 5px;margin-bottom: 5px">
				
			</div> -->
		</div>
		<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
			<div id="container" style="width: 100%;height: 43vh"></div>
			<hr style="border: 3px solid white;padding: 0px;margin: 0px">
			<div id="container2" style="width: 100%;height: 43vh"></div>
		</div>
		<div class="col-xs-6" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;">
			<div class="gambar" style="margin-top:0px;height: 20vh;margin-bottom: 10px">
				<table style="text-align:center;width:100%;height: 100%">
					<tr>
						<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 1vw;font-weight: bold;">INCOMING YMMJ
						</td>
						<td style="color: transparent;">
							
						</td>
						<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 1vw;font-weight: bold;">INCOMING NON-YMMJ
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;width: 24%;font-weight: bold;">LOT OK
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;width: 24%;font-weight: bold;">LOT OUT
						</td>
						<td style="color: transparent;width: 4%;border:0px">
							
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;width: 24%;font-weight: bold;">LOT OK
						</td>
						<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 1vw;width: 24%;font-weight: bold;">LOT OUT
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid #fff;color: white;font-size: 5vw;" id="lot_ok_td_YMMJ"><span id="lot_ok_YMMJ">0</span>
						</td>
						<td style="border: 1px solid #fff;font-size: 5vw;" id="lot_out_td_YMMJ"><span id="lot_out_YMMJ">0</span>
						</td>
						<td style="color: transparent;">
							
						</td>
						<td style="border: 1px solid #fff;color: white;font-size: 5vw;" id="lot_ok_td_NYMMJ"><span id="lot_ok_NYMMJ">0</span>
						</td>
						<td style="border: 1px solid #fff;font-size: 5vw;" id="lot_out_td_NYMMJ"><span id="lot_out_NYMMJ">0</span>
						</td>
					</tr>
				</table>
			</div>
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
							<thead style="background-color: rgb(126,86,134);text-align: left;">
								<tr>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Item Desc.</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty Check (Pcs)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Defect</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Repair</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Return</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG Ratio (%)</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Vendor</th>
									<th style="border: 1px solid black; font-size: 0.8vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Status</th>
								</tr>
							</thead>
							<tbody id="body_table_lot" style="text-align:center;">
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center><h3 style="font-weight: bold;color:black ;font-size: 20px" id="judul_detail"></h3></center>
					<div class="col-md-12" id="bodyDetail">
			          <table class="table table-bordered table-striped" style="font-size:15px" id="tableDetail">
			          	<thead style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:15px">
			          		<tr>
			          			<th>Loc</th>
			          			<th>Material</th>
			          			<th>Vendor</th>
			          			<th>Invoice</th>
			          			<th>Inspection Level</th>
			          			<th>Qty Rec</th>
			          			<th>Qty Check</th>
			          			<th>Qty Repair</th>
			          			<th>Qty Return</th>
			          			<th>Total NG</th>
			          			<th>Status</th>
			          		</tr>
			          	</thead>
			          	<tbody id="bodyTableDetail">
			          		
			          	</tbody>
			          </table>
			        </div>
				</div>
			</div>
			<div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
		</div>
	</div>
</div>
</section>
@endsection
@section('scripts')
<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
<script src="{{ url('js/highcharts.js')}}"></script>
<!-- <script src="{{ url("js/pareto.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script> -->
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
		fetchNgRate();
		setInterval(fetchLotStatus, 600000);
	});

	function fetchAll() {
		fetchLotStatus();
		fetchNgRate();
	}

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
		fetchSelectMaterial();
		// fetchLotStatus();
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
		// fetchLotStatus();
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

	function fetchNgRate() {
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/ng_rate") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var categories = [];
					var checkes = [];
					var returnes = [];
					var repaires = [];
					var persen = [];

					$.each(result.ng_rate, function(key,value){
						categories.push(value.months);
						checkes.push(parseInt(value.checkes));
						returnes.push(parseInt(value.returnes));
						repaires.push(parseInt(value.repaires));
						persen.push(parseFloat(value.persen));
					});

					Highcharts.chart('container', {
					    chart: {
					        zoomType: 'xy'
					    },
					    title: {
					        text: 'NG RATE INCOMING CHECK QA'
					    },
					    subtitle: {
					        text: result.firstDateTitle+' - '+result.lastDateTitle
					    },
					    xAxis: [{
					        categories: categories,
					        crosshair: true
					    }],
					    yAxis: [{ 
					        labels: {
					            format: '{value}',
					            style: {
					                color: '#fff'
					            }
					        },
					        title: {
					            text: 'Qty',
					            style: {
					                color: '#fff'
					            }
					        }
					    }, { 
					        title: {
					            text: 'NG Rate',
					            style: {
					                color: '#fff'
					            }
					        },
					        labels: {
					            format: '{value}%',
					            style: {
					                color: '#fff'
					            }
					        },
					        opposite: true
					    }],
					    tooltip: {
					        shared: true
					    },
					    legend: {
					        enabled:true
					    },credits: {
							enabled: false
						},
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
							},
							spline:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y}%',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
							},
						},
					    series: [
					    {
					        name: 'Repair',
					        type: 'column',
					        data: repaires,
					        color: '#a5a5a5'

					    },{
					        name: 'Return',
					        type: 'column',
					        data: returnes,
					        color: '#f46fbb'

					    },{
					        name: 'Total Check',
					        type: 'column',
					        data: checkes,
					        color: '#2c5394'

					    }, {
					        name: 'NG Rate',
					        type: 'spline',
					        data: persen,
					        color: '#ed151d',
					        yAxis: 1,
					        tooltip: {
					            valueSuffix: '%'
					        }
					    }]
					});

					var categories = [];
					var rate = [];

					var index = 1;

					$.each(result.highest, function(key,value){
						if(index < 6){
							categories.push(value.vendor_shortname);
							rate.push(parseInt(value.ratio));
							index++;
						}
					});

					Highcharts.chart('container2', {
					    chart: {
					        zoomType: 'xy'
					    },
					    title: {
					        text: 'NG RATE INCOMING CHECK BY VENDOR'
					    },
					    subtitle: {
					        text: result.firstDateTitle+' - '+result.lastDateTitle
					    },
					    xAxis: [{
					        categories: categories,
					        crosshair: true
					    }],
					    yAxis: [{ 
					        title: {
					            text: 'NG Rate',
					            style: {
					                color: '#fff'
					            }
					        },
					        labels: {
					            format: '{value}%',
					            style: {
					                color: '#fff'
					            }
					        }
					    }],
					    tooltip: {
					        shared: true
					    },
					    legend: {
					        enabled:true
					    },credits: {
							enabled: false
						},
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetailByVendor(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y} %',
									style:{
										fontSize: '13px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
							},
						},
					    series: [
					    {
					        name: 'Ratio',
					        type: 'column',
					        data: rate,
					        color: '#7a0000'

					    }]
					});
				}
			}
		});
	}

	function showModalDetail(categories) {
		$('#loading').show();
		$('#judul_detail').html("");
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
			categories:categories
		}

		$.get('{{ url("fetch/qa/display/incoming/ng_rate/detail") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#tableDetail').DataTable().clear();
					$('#tableDetail').DataTable().destroy();
					$('#judul_detail').html("Detail NG Rate Incoming Check QA on "+categories);
					$('#bodyTableDetail').html("");
					var bodyDetail = "";
					var total_ng = 0;
					var total_check= 0;
					$.each(result.detail, function(key,value){
						if (value.status_lot == 'Lot Out') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}
						if (value.location == 'wi1') {
				  			var loc = 'Woodwind Instrument (WI) 1';
				  		}else if (value.location == 'wi2') {
				  			var loc = 'Woodwind Instrument (WI) 2';
				  		}else if(value.location == 'ei'){
				  			var loc = 'Educational Instrument (EI)';
				  		}else if(value.location == 'sx'){
				  			var loc = 'Saxophone Body';
				  		}else if (value.location == 'cs'){
				  			var loc = 'Case';
				  		}else if(value.location == 'ps'){
				  			var loc = 'Pipe Silver';
				  		}
						bodyDetail += '<tr>';
						bodyDetail += '<td style="background-color:'+color+';">'+loc+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.material_number+' - '+value.material_description+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.vendor_shortname+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.invoice+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.inspection_level+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.qty_rec+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.qty_check+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.repair+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.return+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.total_ng+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.status_lot+'</td>';
						bodyDetail += '</tr>';

						total_ng = total_ng + parseInt(value.total_ng);
						total_check = total_check + parseInt(value.qty_check);
					});

					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL NG</td>';
					// bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+total_ng+'</td>';
					// bodyDetail += '</tr>';
					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL CHECK</td>';
					// bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+total_check+'</td>';
					// bodyDetail += '</tr>';
					// bodyDetail += '</tr>';
					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="10" style="color:black;text-align:right">NG RATE</td>';
					// bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+Math.round((total_ng / total_check) * 100)+' %</td>';
					// bodyDetail += '</tr>';

					$('#bodyTableDetail').append(bodyDetail);

					var table = $('#tableDetail').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 100, -1 ],
						[ '10 rows', '25 rows', '100 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
									exportOptions: {
										columns: ':not(.notexport)'
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					$('#modalDetail').modal('show');
					$('#loading').hide();
				}
			}
		});
	}

	function showModalDetailByVendor(categories) {
		$('#loading').show();
		$('#judul_detail').html("");
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			vendor:categories,
		}

		$.get('{{ url("fetch/qa/display/incoming/ng_rate/detail/vendor") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#judul_detail').html("Detail NG Rate Incoming Check QA on "+categories);
					$('#tableDetail').DataTable().clear();
					$('#tableDetail').DataTable().destroy();
					$('#bodyTableDetail').html("");
					var bodyDetail = "";
					var total_ng = 0;
					var total_check= 0;
					$.each(result.detail, function(key,value){
						if (value.status_lot == 'Lot Out') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						if (value.location == 'wi1') {
				  			var loc = 'Woodwind Instrument (WI) 1';
				  		}else if (value.location == 'wi2') {
				  			var loc = 'Woodwind Instrument (WI) 2';
				  		}else if(value.location == 'ei'){
				  			var loc = 'Educational Instrument (EI)';
				  		}else if(value.location == 'sx'){
				  			var loc = 'Saxophone Body';
				  		}else if (value.location == 'cs'){
				  			var loc = 'Case';
				  		}else if(value.location == 'ps'){
				  			var loc = 'Pipe Silver';
				  		}
						bodyDetail += '<tr>';
						bodyDetail += '<td style="background-color:'+color+';">'+loc+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.material_number+' - '+value.material_description+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.vendor_shortname+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.invoice+'</td>';
						bodyDetail += '<td style="background-color:'+color+';">'+value.inspection_level+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.qty_rec+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.qty_check+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.repair+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.return+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.total_ng+'</td>';
						bodyDetail += '<td style="background-color:'+color+';text-align:right;padding-right:7px;">'+value.status_lot+'</td>';
						bodyDetail += '</tr>';

						total_ng = total_ng + parseInt(value.total_ng);
						total_check = total_check + parseInt(value.qty_check);
					});

					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL NG</td>';
					// bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+total_ng+'</td>';
					// bodyDetail += '</tr>';
					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="10" style="color:black;text-align:right">TOTAL CHECK</td>';
					// bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+total_check+'</td>';
					// bodyDetail += '</tr>';
					// bodyDetail += '</tr>';
					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="10" style="color:black;text-align:right">NG RATE</td>';
					// bodyDetail += '<td colspan="2" style="color:black;border-left:3px solid black;text-align:left">'+Math.round((total_ng / total_check) * 100)+' %</td>';
					// bodyDetail += '</tr>';

					$('#bodyTableDetail').append(bodyDetail);

					var table = $('#tableDetail').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 100, -1 ],
						[ '10 rows', '25 rows', '100 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
									exportOptions: {
										columns: ':not(.notexport)'
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					$('#modalDetail').modal('show');
					$('#loading').hide();
				}
			}
		});
	}

	function fetchLotStatus() {
		$('#loading').show();

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			lot_status:$('#lot_status').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/ympi") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<span>'+result.monthTitle+'</span>');
					var lot_ok_ymmj = 0;
					var lot_out_ymmj = 0;

					var lot_ok_nymmj = 0;
					var lot_out_nymmj = 0;

					$.each(result.lot_count, function(key,value){
						if (value.vendor_shortname == 'YMMJ') {
							lot_ok_ymmj = lot_ok_ymmj + parseInt(value.lot_ok);
							lot_out_ymmj = lot_out_ymmj + parseInt(value.lot_out);
						}
						if (value.vendor_shortname != 'YMMJ') {
							lot_ok_nymmj = lot_ok_nymmj + parseInt(value.lot_ok);
							lot_out_nymmj = lot_out_nymmj + parseInt(value.lot_out);
						}
					});

					$('#lot_ok_YMMJ').html(lot_ok_ymmj);
					$('#lot_out_YMMJ').html(lot_out_ymmj);

					$('#lot_ok_NYMMJ').html(lot_ok_nymmj);
					$('#lot_out_NYMMJ').html(lot_out_nymmj);

					if (parseInt(lot_ok_ymmj) > 0) {
						$('#lot_ok_td_YMMJ').css("background-color","rgb(0, 166, 90)",'important');
						$('#lot_ok_td_YMMJ').css("color","white",'important');
						$('#lot_ok_td_YMMJ').css("font-weight","bold",'important');
					}else{
						$('#lot_ok_td_YMMJ').css("background-color","white",'important');
						$('#lot_ok_td_YMMJ').css("color","black",'important');
						$('#lot_ok_td_YMMJ').css("font-weight","bold",'important');
					}

					if (parseInt(lot_out_ymmj) > 0) {
						$('#lot_out_td_YMMJ').css("background-color","#dd4b39",'important');
						$('#lot_out_td_YMMJ').css("color","white",'important');
						$('#lot_out_td_YMMJ').css("font-weight","bold",'important');
					}else{
						$('#lot_out_td_YMMJ').css("background-color","white",'important');
						$('#lot_out_td_YMMJ').css("color","black",'important');
						$('#lot_out_td_YMMJ').css("font-weight","bold",'important');
					}

					if (parseInt(lot_ok_nymmj) > 0) {
						$('#lot_ok_td_NYMMJ').css("background-color","rgb(0, 166, 90)",'important');
						$('#lot_ok_td_NYMMJ').css("color","white",'important');
						$('#lot_ok_td_NYMMJ').css("font-weight","bold",'important');
					}else{
						$('#lot_ok_td_NYMMJ').css("background-color","white",'important');
						$('#lot_ok_td_NYMMJ').css("color","black",'important');
						$('#lot_ok_td_NYMMJ').css("font-weight","bold",'important');
					}

					if (parseInt(lot_out_nymmj) > 0) {
						$('#lot_out_td_NYMMJ').css("background-color","#dd4b39",'important');
						$('#lot_out_td_NYMMJ').css("color","white",'important');
						$('#lot_out_td_NYMMJ').css("font-weight","bold",'important');
					}else{
						$('#lot_out_td_NYMMJ').css("background-color","white",'important');
						$('#lot_out_td_NYMMJ').css("color","black",'important');
						$('#lot_out_td_NYMMJ').css("font-weight","bold",'important');
					}

					$('#table_lot').DataTable().clear();
					$('#table_lot').DataTable().destroy();
					$('#body_table_lot').html("");
					var body_lot = "";

					$.each(result.lot_detail, function(key2,value2){
						if (value2.status_lot == 'Lot Out') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						body_lot += '<tr>';
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+' - '+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
						if (value2.ng_name != null) {
							body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.ng_name+'</td>';
						}else{
							body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;"></td>';
						}
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.repair+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.return+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.ng_ratio.toFixed(2)+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.vendor_shortname+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 0.9vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.status_lot+'</td>';

						body_lot += '</tr>';
					});

					$('#body_table_lot').append(body_lot);

					var table = $('#table_lot').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 100, -1 ],
						[ '10 rows', '25 rows', '100 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
									exportOptions: {
										columns: ':not(.notexport)'
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

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