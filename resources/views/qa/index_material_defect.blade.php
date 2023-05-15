@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		color: black;
	}
	tfoot>tr>th{
		text-align:center;
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
	#tableDetail_info{
		color: black
	}
	#tableDetail_length{
		color: black;
	}
	.content-wrapper{
		color: white;
		font-weight: bold;
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
	    width: 350px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
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
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading. Please Wait. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>	
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="month_from" name="month_from" placeholder="Select Month From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="month_to" name="month_to" placeholder="Select Month To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="form-group">
					<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
						@foreach($vendors as $vendor)
						<option value="{{$vendor->vendor}}">{{$vendor->vendor}}</option>
						@endforeach
					</select>
					<input type="text" name="vendor" id="vendor" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="form-group">
					<select class="form-control select3" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
						<!-- @foreach($materials as $material)
						<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
						@endforeach -->
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="form-group" align="left">
					<select class="form-control select3" data-placeholder="Select Type" id="typeSelect" style="width: 100%;color: black !important;">
						<option value=""></option>
						<option value="By By NG Name">By NG Name (Pareto)</option>
						<option value="By Month" selected>By Month (NG Rate)</option>
					</select>
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<button class="btn btn-success pull-left" onclick="searchStatus()" style="font-weight: bold;">
					Search
				</button>
			</div>
			<!-- <div class="col-xs-2" style="padding-left: 0px">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 11px"></div>
			</div> -->
		</div>
		<div id="by_month">
			<div class="col-xs-8" style="padding-left: 0px;padding-top: 5px">
				<div id="container3" style="width: 100%;height: 500px"></div>
			</div>
			<div class="col-xs-4" style="padding-left: 0px;padding-top: 5px">
				<div id="container4" style="width: 100%;height: 500px"></div>
			</div>
		</div>
		<div id="by_ng_name">
			<div class="col-xs-8" style="padding-left: 0px;padding-top: 5px">
				<div id="container1" style="width: 100%;height: 500px"></div>
			</div>
			<div class="col-xs-4" style="padding-left: 0px;padding-top: 5px">
				<div id="container2" style="width: 100%;height: 500px"></div>
			</div>
		</div>
	</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center><h3 style="font-weight: bold;color:black ;font-size: 20px" id="judul_detail"></h3></center>
					<div class="col-md-12" id="bodyDetail">
			          <table class="table table-bordered" style="font-size:15px" id="tableDetail">
			          	<thead style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:15px">
			          		<tr>
			          			<th>Date</th>
			          			<th>Location</th>
			          			<th>Material</th>
			          			<th>Vendor</th>
			          			<th>Invoice</th>
			          			<th>Qty Rec</th>
			          			<th>Qty Check</th>
			          			<th>Qty NG</th>
			          			<th>Status</th>
			          			<th>Note</th>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/pareto.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script>
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

	var intervalPareto;

	jQuery(document).ready(function(){
		$('.select2').select2();
		$('.select3').select2();
		fetchLotStatus();
		fetchSelectMaterial();
		intervalPareto = setInterval(fetchLotStatus, 30000);
	});

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
		fetchLotStatus();
		fetchSelectMaterial();
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
		fetchLotStatus();
	}

	$('.datepicker').datepicker({
		autoclose: true,
	    format: "yyyy-mm",
	    todayHighlight: true,
	    startView: "months", 
	    minViewMode: "months",
	    autoclose: true,
	    startDate: '<?php echo $firstData[0]->first ?>'
	});

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

	function searchStatus() {
		if ($('#typeSelect').val() == 'By Month') {
			$('#by_month').show();
			$('#by_ng_name').hide();
			$('#container3').html('');
			$('#container4').html('');
		}else{
			$('#by_month').hide();
			$('#by_ng_name').show();
			$('#container1').html('');
			$('#container2').html('');
		}
		fetchLotStatus();
	}

	function fetchLotStatus() {
		var data = {
			month_from:$('#month_from').val(),
			month_to:$('#month_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/material_defect") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					// $('#last_update').html('<span><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</span>');

					var categories = [];
					var series = [];
					var series_ok = [];
					var series_check = [];

					$.each(result.material_defect, function(key,value){
						categories.push(value.ng_name.replace(/(.{14})..+/, "$1&hellip;"));
						series.push({y:parseInt(value.count),key:value.ng_name});
						series_ok.push({y:parseInt(value.count_ok),key:value.ng_name});
						series_check.push({y:parseInt(value.count_check),key:value.ng_name});
					});

					Highcharts.chart('container1', {
					    chart: {
					        renderTo: 'container1',
					        type: 'column'
					    },
					    title: {
					        text: 'QA PARETO DEFECT<br>'+result.firstMonthTitle+' - '+result.lastMonthTitle
					    },
					    tooltip: {
					        shared: true
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize: '10px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
							pareto:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}%',
									style:{
										fontSize: '10px'
									}
								},
								lineWidth: 3,
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
						},credits: {
							enabled: false
						},
					    xAxis: {
					        categories: categories,
					        crosshair: true
					    },
					    yAxis: [{
					        title: {
					            text: ''
					        },
					        
					        labels: {
					            format: "{value}"
					        }
					    }, {
					        title: {
					            text: ''
					        },
					        minPadding: 0,
					        maxPadding: 0,
					        max: 100,
					        min: 0,
					        opposite: true,
					        labels: {
					            format: "{value}%"
					        }
					    },],
					    series: [{
					        type: 'pareto',
					        name: 'Pareto',
					        yAxis: 1,
					        zIndex: 10,
					        baseSeries: 1,
					        tooltip: {
					            valueDecimals: 1,
					            valueSuffix: '%'
					        },
					        colorByPoint:false,
					        color:'#f24444',
					    }, {
					        name: 'Total Defect',
					        type: 'column',
					        zIndex: 2,
					        data: series,
					        colorByPoint:false,
					        color:'#ff3333',
					    },
					    {
					        name: 'Total OK',
					        type: 'column',
					        zIndex: 2,
					        // yAxis: 2,
					        data: series_ok,
					        colorByPoint:false,
					        color:'#8ae379',
					    },
					    {
					        name: 'Total Check',
					        type: 'column',
					        zIndex: 2,
					        // yAxis: 2,
					        data: series_check,
					        colorByPoint:false,
					        color:'#4a92ff',
					    }]
					});

					var categories = [];
					var series = [];
					var series_rate = [];
					var series_ok = [];
					var series_check = [];

					$.each(result.material_defect_month, function(key,value){
						categories.push(value.month_name);
						series.push({y:parseInt(value.count_ng),key:value.month});
						series_rate.push({y:parseFloat(((parseInt(value.count_ng)/parseInt(value.count_check))*100).toFixed(2)),key:value.month});
						series_ok.push({y:parseInt(value.count_ok),key:value.month});
						series_check.push({y:parseInt(value.count_check),key:value.month});
					});


					Highcharts.chart('container3', {
					    chart: {
					        renderTo: 'container3',
					        type: 'column'
					    },
					    title: {
					        text: 'QA NG RATE BY MONTH<br>'+result.firstMonthTitleNg+' - '+result.lastMonthTitleNg
					    },
					    tooltip: {
					        shared: true
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	// showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize: '10px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
							spline:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	// showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}%',
									style:{
										fontSize: '10px'
									}
								},
								lineWidth: 3,
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
						},credits: {
							enabled: false
						},
					    xAxis: {
					        categories: categories,
					        crosshair: true
					    },
					    yAxis: [{
					        title: {
					            text: ''
					        },
					        
					        labels: {
					            format: "{value}"
					        }
					    }, 
					    {
					        title: {
					            text: ''
					        },
					        minPadding: 0,
					        maxPadding: 0,
					        max: 100,
					        min: 0,
					        opposite: true,
					        labels: {
					            format: "{value}%"
					        }
					    },
					    ],
					    series: [
					    
					    {
					        name: 'Total Defect',
					        type: 'column',
					        // zIndex: 2,
					        yAxis: 0,
					        data: series,
					        colorByPoint:false,
					        color:'#ff3333',
					    },
					    {
					        name: 'Total OK',
					        type: 'column',
					        // zIndex: 2,
					        yAxis: 0,
					        data: series_ok,
					        colorByPoint:false,
					        color:'#8ae379',
					    },
					    {
					        name: 'Total Check',
					        type: 'column',
					        // zIndex: 2,
					        yAxis: 0,
					        data: series_check,
					        colorByPoint:false,
					        color:'#4a92ff',
					    },
					    {
					        type: 'spline',
					        name: 'NG Rate',
					        yAxis: 1,
					        // zIndex: 10,
					        // baseSeries: 1,
					        data:series_rate,
					        tooltip: {
					            valueDecimals: 1,
					            valueSuffix: '%'
					        },
					        colorByPoint:false,
					        color:'#f24444',
					    }, ]
					});

					var total = 0;
					var ok = 0;
					var returns = 0;
					var repairs = 0;

					$.each(result.material_status, function(key,value){
						total = value.total;
						returns = parseInt(value.return);
						repairs = parseInt(value.repair);
						ok = parseInt(value.total)-(parseInt(value.return)+parseInt(value.repair));
					});

					Highcharts.chart('container2', {
					    chart: {
					        plotBackgroundColor: null,
					        plotBorderWidth: null,
					        plotShadow: false,
					        type: 'pie'
					    },
					    title: {
					        text: 'MATERIAL STATUS'
					    },
					    tooltip: {
					        pointFormat: '{series.name}<br><b>{point.y} Pc(s)<br>{point.percentage:.1f}%</b>'
					    },
					    accessibility: {
					        point: {
					            valueSuffix: '%',
					            borderColor: '#8ae'
					        }
					    },
					    plotOptions: {
					        pie: {
					            dataLabels: {
					                enabled: true,
					                connectorColor: '#fff',
					                format: '<b>{point.name}</b><br>{point.y} Pc(s)<br>{point.percentage:.1f} %',
					                style:{
					                	fontSize:'13px'
					                }
					            },
					            point: {
					                events: {
					                    click: function () {
					                    }
					                }
					            },
					            cursor: 'pointer',
					            borderWidth: 2,
					            animation:false
					        }
					    },credits: {
							enabled: false
						},
					    series: [{
					        name: 'Qty Material',
					        colorByPoint: true,
					        data: [{
					            name: 'OK',
					            y: ok,
					            sliced: true,
					            selected: true,
					            colorByPoint: false,
								color: '#8ae379',
					        }, {
					            name: 'Repair',
					            y: repairs,
					            colorByPoint: false,
								color: '#ff3333',
					        }, {
					            name: 'Return',
					            y: returns,
					            colorByPoint: false,
								color: '#ff8787',
					        } ]
					    }],
					    responsive: {
					        rules: [{
					            condition: {
					                maxWidth: 500
					            },
					            chartOptions: {
					                plotOptions: {
					                    series: {
					                        dataLabels: {
					                            format: '<b>{point.name}</b>'
					                        }
					                    }
					                }
					            }
					        }]
					    }
					});

					var total = 0;
					var ok = 0;
					var returns = 0;
					var repairs = 0;

					$.each(result.material_status, function(key,value){
						total = value.total;
						returns = parseInt(value.return);
						repairs = parseInt(value.repair);
						ok = parseInt(value.total)-(parseInt(value.return)+parseInt(value.repair));
					});

					Highcharts.chart('container4', {
					    chart: {
					        plotBackgroundColor: null,
					        plotBorderWidth: null,
					        plotShadow: false,
					        type: 'pie'
					    },
					    title: {
					        text: 'MATERIAL STATUS'
					    },
					    tooltip: {
					        pointFormat: '{series.name}<br><b>{point.y} Pc(s)<br>{point.percentage:.1f}%</b>'
					    },
					    accessibility: {
					        point: {
					            valueSuffix: '%',
					            borderColor: '#8ae'
					        }
					    },
					    plotOptions: {
					        pie: {
					            dataLabels: {
					                enabled: true,
					                connectorColor: '#fff',
					                format: '<b>{point.name}</b><br>{point.y} Pc(s)<br>{point.percentage:.1f} %',
					                style:{
					                	fontSize:'13px'
					                }
					            },
					            point: {
					                events: {
					                    click: function () {
					                    }
					                }
					            },
					            cursor: 'pointer',
					            borderWidth: 2,
					            animation:false
					        }
					    },credits: {
							enabled: false
						},
					    series: [{
					        name: 'Qty Material',
					        colorByPoint: true,
					        data: [{
					            name: 'OK',
					            y: ok,
					            sliced: true,
					            selected: true,
					            colorByPoint: false,
								color: '#8ae379',
					        }, {
					            name: 'Repair',
					            y: repairs,
					            colorByPoint: false,
								color: '#ff3333',
					        }, {
					            name: 'Return',
					            y: returns,
					            colorByPoint: false,
								color: '#ff8787',
					        } ]
					    }],
					    responsive: {
					        rules: [{
					            condition: {
					                maxWidth: 500
					            },
					            chartOptions: {
					                plotOptions: {
					                    series: {
					                        dataLabels: {
					                            format: '<b>{point.name}</b>'
					                        }
					                    }
					                }
					            }
					        }]
					    }
					});
				}
			}
		});
	}

	function showModalDetailPareto(categories) {
		$('#loading').show();
		$('#judul_detail').html("");
		var data = {
			month_from:$('#month_from').val(),
			month_to:$('#month_to').val(),
			vendor:$('#vendor').val(),
			material:$('#material').val(),
			categories:categories
		}

		$.get('{{ url("fetch/qa/display/incoming/material_defect/detail") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#judul_detail').html("Detail Pareto of "+categories);
					$('#tableDetail').DataTable().clear();
					$('#tableDetail').DataTable().destroy();
					$('#bodyTableDetail').html("");
					var bodyDetail = "";
					var total_ng = 0;
					$.each(result.detail, function(key,value){
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
						bodyDetail += '<td>'+value.created+'</td>';
						bodyDetail += '<td>'+loc+'</td>';
						bodyDetail += '<td>'+value.material_number+' - '+value.material_description+'</td>';
						bodyDetail += '<td>'+value.vendor+'</td>';
						bodyDetail += '<td>'+value.invoice+'</td>';
						bodyDetail += '<td>'+value.qty_rec+'</td>';
						bodyDetail += '<td>'+value.qty_check+'</td>';
						bodyDetail += '<td>'+value.qty_ng+'</td>';
						bodyDetail += '<td>'+value.status_ng+'</td>';
						bodyDetail += '<td>'+value.note_ng+'</td>';
						bodyDetail += '</tr>';

						total_ng = total_ng + parseInt(value.qty_ng);
					});

					// bodyDetail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;color:black;font-size:15px">';
					// bodyDetail += '<td colspan="7" style="color:black;text-align:right">TOTAL NG</td>';
					// bodyDetail += '<td colspan="3" style="color:black;border-left:3px solid black;text-align:left">'+total_ng+'</td>';
					// bodyDetail += '</tr>';

					$('#bodyTableDetail').append(bodyDetail);

					var table = $('#tableDetail').DataTable({
						// 'dom': '<"pull-left"B><"pull-right"f>rt<"row"<"col-sm-3"l><"col-sm-3"i><"col-sm-6"p>>',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
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

	function getColorPattern(i) {
	    var colors = Highcharts.getOptions().colors,
	        patternColors = [colors[2], colors[0], colors[3], colors[1], colors[4]],
	        patterns = [
	            'M 0 0 L 5 5 M 4.5 -0.5 L 5.5 0.5 M -0.5 4.5 L 0.5 5.5',
	            'M 0 5 L 5 0 M -0.5 0.5 L 0.5 -0.5 M 4.5 5.5 L 5.5 4.5',
	            'M 1.5 0 L 1.5 5 M 4 0 L 4 5',
	            'M 0 1.5 L 5 1.5 M 0 4 L 5 4',
	            'M 0 1.5 L 2.5 1.5 L 2.5 0 M 2.5 5 L 2.5 3.5 L 5 3.5'
	        ];

	    return {
	        pattern: {
	            path: patterns[i],
	            color: patternColors[i],
	            width: 5,
	            height: 5
	        }
	    };
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