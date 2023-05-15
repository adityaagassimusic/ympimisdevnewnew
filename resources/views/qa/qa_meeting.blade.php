@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	html {
	  scroll-behavior: smooth;
	}


	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}

	#tableCode > tbody > tr > td:hover{
		background-color: #7dfa8c !important;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif						
	<div class="row" style="padding-left: 10px;padding-right: 10px;">
		<div class="col-xs-5" style="background-color: lightskyblue;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_worst">TOP 10 WORST VENDOR</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
					@foreach($vendor as $vendor)
					<option value="{{$vendor->vendor_shortname}}">{{$vendor->vendor_shortname}}</option>
					@endforeach
				</select>
				<input type="text" name="vendor" id="vendor" style="color: black !important" hidden>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" id="vendorOrigin" data-placeholder="Select Origin" style="width: 100%;color: black !important"> 
					<option value=""></option>
					<option value="INCOMING CHECK">INCOMING CHECK</option>
					<option value="PRODUCTION FINDING">PRODUCTION FINDING</option>
				</select>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month" name="month" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillListWorstVendor()">
        		Search
        	</button>
        </div>
		<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px;margin-bottom: 20px;height: 85vh">
			<table id="tableWorstMaterial" class="table table-striped table-bordered" style="width: 100%;margin-bottom: 0px;display: none">
            	<thead>
            		<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39;">
			            <th style="width: 1%;padding-top: 0px;padding-bottom: 0px;">Rank</th>
			            <th style="width: 14%;padding-top: 0px;padding-bottom: 0px;">Vendor</th>
			            <!-- <th style="width: 2%;padding-top: 0px;padding-bottom: 0px;">Qty Rec</th> -->
			            <th style="width: 2%;padding-top: 0px;padding-bottom: 0px;">Qty Check</th>
			            <th style="width: 2%;padding-top: 0px;padding-bottom: 0px;">Repair</th>
			            <th style="width: 2%;padding-top: 0px;padding-bottom: 0px;">Return</th>
			            <th style="width: 2%;padding-top: 0px;padding-bottom: 0px;">Total NG</th>
			            <th style="width: 2%;padding-top: 0px;padding-bottom: 0px;">% NG</th>
            		</tr>
            	</thead>
            	<tbody id="bodyWorstMaterial">
                
            	</tbody>
            </table>
		</div>
		<div class="col-xs-6" style="padding-right: 0px;padding-left: 5px;margin-bottom: 20px;">
			<div style="height: 85vh" id="container_worst_material">
				
			</div>
		</div>

		<div class="col-xs-5" style="background-color: darkorange;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_ng_rate"></span>
        </div>
		<div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
					<select class="form-control select2" multiple="multiple" id="vendorSelect2" data-placeholder="Select Vendors" onchange="changeVendor2()" style="width: 100%;color: black !important"> 
						@foreach($vendor2 as $vendor)
						<option value="{{$vendor->vendor_shortname}}">{{$vendor->vendor_shortname}}</option>
						@endforeach
					</select>
					<input type="text" name="vendor2" id="vendor2" style="color: black !important" hidden>
				</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" id="vendorOrigin2" data-placeholder="Select Origin" style="width: 100%;color: black !important"> 
					<option value=""></option>
					<option value="INCOMING CHECK">INCOMING CHECK</option>
					<option value="PRODUCTION FINDING">PRODUCTION FINDING</option>
				</select>
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_from" name="month_from" placeholder="Select Month From">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_to" name="month_to" placeholder="Select Month To">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillListNgRate()">
        		Search
        	</button>
        </div>
        <div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;">
			<div style="height: 85vh" id="container_ng_rate">
				
			</div>
		</div>
		<div class="col-xs-3" style="background-color: lightpink;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_worst">TOP 10 WORST MATERIAL</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
					<select class="form-control select2" multiple="multiple" id="vendorSelect3" data-placeholder="Select Vendors" onchange="changeVendor3()" style="width: 100%;color: black !important"> 
						@foreach($vendor3 as $vendor)
						<option value="{{$vendor->vendor_shortname}}">{{$vendor->vendor_shortname}}</option>
						@endforeach
					</select>
					<input type="text" name="vendor3" id="vendor3" style="color: black !important" hidden>
				</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
					<select class="form-control select2" multiple="multiple" id="materialSelect" data-placeholder="Select Materials" onchange="changeMaterial()" style="width: 100%;color: black !important"> 
						@foreach($material as $material)
						<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
						@endforeach
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" id="vendorOrigin3" data-placeholder="Select Origin" style="width: 100%;color: black !important"> 
					<option value=""></option>
					<option value="INCOMING CHECK">INCOMING CHECK</option>
					<option value="PRODUCTION FINDING">PRODUCTION FINDING</option>
				</select>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month2" name="month2" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillListWorstMaterial()">
        		Search
        	</button>
        </div>
		<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;margin-bottom: 20px;">
			<table id="tableWorstMtrl" class="table table-striped table-bordered" style="width: 100%;margin-bottom: 0px;display: none">
            	<thead>
            		<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
            			<th style="width: 1%;">#</th>
			            <th style="width: 10%;">Material</th>
			            <!-- <th style="width: 2%;">Qty Rec</th> -->
			            <th style="width: 2%;">Qty Check</th>
			            <th style="width: 2%;">Repair</th>
			            <th style="width: 2%;">Return</th>
			            <th style="width: 2%;">Total NG</th>
			            <th style="width: 2%;">% NG</th>
			            <th style="width: 10%;">NG Detail</th>
            		</tr>
            	</thead>
            	<tbody id="bodyWorstMtrl">
                
            	</tbody>
            </table>
		</div>
	</div>

	<div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
          </div>
          <div class="modal-body">
            <div class="row">
            <div class="col-md-12" id="data-activity">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
              <thead>
              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                <th style="width: 1%;">#</th>
                <th style="width: 4%;">Document</th>
                <th style="width: 3%;">Auditee</th>
                <th style="width: 3%;">Auditor</th>
                <th style="width: 1%;">Schedule Date</th>
                <th style="width: 1%;">Status</th>
              </tr>
              </thead>
              <tbody id="body-detail">
                
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

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr_sudah = null;
	var arr_belum = null;
	var kataconfirm = 'Apakah Anda yakin?';

	jQuery(document).ready(function() {
		$('#month').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});

		$('#month2').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});

		$('#month_from').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});

		$('#month_to').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		$('body').toggleClass("sidebar-collapse");

		fillListWorstVendor();
		fillListNgRate();
		fillListWorstMaterial();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		arr_sudah = null;
		arr_belum = null;
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
	}

	function changeVendor2() {
		$("#vendor2").val($("#vendorSelect2").val());
	}
	function changeVendor3() {
		$("#vendor3").val($("#vendorSelect3").val());
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	function fillListWorstVendor(){
		$('#loading').show();
		var data = {
			vendor:$('#vendor').val(),
			month:$('#month').val(),
			vendor_origin:$('#vendorOrigin').val(),
		}
		$.get('{{ url("fetch/qa_meeting/worst_vendor") }}',data, function(result, status, xhr){
			if(result.status){
				$("#tableWorstMaterial").hide();
				$('#tableWorstMaterial').DataTable().clear();
				$('#tableWorstMaterial').DataTable().destroy();
				$('#bodyWorstMaterial').html('');
				var bodyWorstMaterial = '';

				if (result.worst_vendor.length == 10) {
					$('#bodyWorstMaterial').css('height','79.5vh');
				}else{
					$('#bodyWorstMaterial').css('height','auto');
				}

				for(var i = 0; i < result.worst_vendor.length;i++){
					bodyWorstMaterial += '<tr style="background-color:white;">';
					bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+(i+1)+'</td>';
					bodyWorstMaterial += '<td style="text-align:left;padding-left:7px !important;">'+result.worst_vendor[i].vendor+'</td>';
					// bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_vendor[i].qty_rec+'</td>';
					bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_vendor[i].qty_check+'</td>';
					bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_vendor[i].repair+'</td>';
					bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_vendor[i].return+'</td>';
					bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_vendor[i].total_ng+'</td>';
					bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+parseFloat(result.worst_vendor[i].ratio).toFixed(2)+' %</td>';
					bodyWorstMaterial += '</tr>';
				}

				$('#bodyWorstMaterial').append(bodyWorstMaterial);

				var categories = [];
				var checkes = [];
				var persen = [];

				$.each(result.worst_vendor, function(key,value){
					categories.push(value.vendor_shortname);
					checkes.push(parseInt(value.qty_check));
					persen.push(parseFloat(value.ratio));
				});

				Highcharts.chart('container_worst_material', {
				    chart: {
				        zoomType: 'xy'
				    },
				    title: {
				        text: "TOP 10 WORST VENDOR",
				        style:{
				        	fontWeight:'bold',
				        	fontSize:'16px'
				        }
				    },
				    subtitle: {
				        text: ''
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
				            text: 'Qty Check',
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
				    credits:{
				    	enabled:false
				    },
				    tooltip: {
				        shared: true
				    },
				    legend: {
				        enabled:true
				    },
				    plotOptions: {
						series:{
							// cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                    	// showModalDetail(this.category);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '11px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							// cursor: 'pointer',
						},
						spline:{
							// cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                    	// showModalDetail(this.category);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y}%',
								style:{
									fontSize: '11px'
								}
							},
							states: {
						      hover: {
						        lineWidthPlus: 0
						     }
						    },
							lineWidth:0,
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							// cursor: 'pointer',
							borderColor: 'black',
						},
					},
				    series: [{
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

				$('#title_worst').html('TOP 10 WORST VENDOR ON '+result.monthTitle.toUpperCase());

				$("#tableWorstMaterial").show();
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function fillListNgRate(){
		$('#loading').show();
		var data = {
			vendor:$('#vendor2').val(),
			month_from:$('#month_from').val(),
			month_to:$('#month_to').val(),
			vendor_origin:$('#vendorOrigin2').val(),
		}
		$.get('{{ url("fetch/qa_meeting/ng_rate") }}',data, function(result, status, xhr){
			if(result.status){

				var categories = [];
				var checkes = [];
				var returnes = [];
				var repaires = [];
				var persen = [];

				$.each(result.ng_rate, function(key,value){
					categories.push(value.month_name);
					checkes.push(parseInt(value.qty_check));
					returnes.push(parseInt(value.return));
					repaires.push(parseInt(value.repair));
					persen.push(parseFloat(value.ratio));
				});

				Highcharts.chart('container_ng_rate', {
				    chart: {
				        zoomType: 'xy'
				    },
				    title: {
				        text: result.vendorTitle,
				        style:{
				        	fontWeight:'bold',
				        	fontSize:'18px'
				        }
				    },
				    subtitle: {
				        text: result.firstTitle + ' - '+result.lastTitle,
				        style:{
				        	fontSize:'13px'
				        }
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
				            text: 'Qty Check',
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
				    credits:{
				    	enabled:false
				    },
				    tooltip: {
				        shared: true
				    },
				    legend: {
				        enabled:true
				    },
				    plotOptions: {
						series:{
							// cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                    	// showModalDetail(this.category);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '11px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							// cursor: 'pointer',
						},
						spline:{
							// cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                    	// showModalDetail(this.category);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y}%',
								style:{
									fontSize: '11px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							// cursor: 'pointer',
							borderColor: 'black',
						},
					},
				    series: [{
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

				$('#title_ng_rate').html('TREND NG RATE VENDOR (ALL ITEM)');
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function fillListWorstMaterial() {
		$('#loading').show();
		var data = {
			vendor:$('#vendor3').val(),
			month:$('#month2').val(),
			material:$('#material').val(),
			vendor_origin:$('#vendorOrigin3').val(),
		}
		$.get('{{ url("fetch/qa_meeting/worst_material") }}',data, function(result, status, xhr){
			if(result.status){
				$("#tableWorstMtrl").hide();
				$('#tableWorstMtrl').DataTable().clear();
				$('#tableWorstMtrl').DataTable().destroy();
				$('#bodyWorstMtrl').html('');
				var bodyWorstMaterial = '';

				// if (result.worst_vendor.length == 10) {
				// 	$('#bodyWorstMaterial').css('height','79.5vh');
				// }else{
				// 	$('#bodyWorstMaterial').css('height','auto');
				// }

				var index = 0;
				for(var i = 0; i < result.worst_material.length;i++){
					if (index < 10) {
						bodyWorstMaterial += '<tr style="background-color:white;">';
						bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+(i+1)+'</td>';
						bodyWorstMaterial += '<td style="text-align:left;padding-left:7px !important;">'+result.worst_material[i].material_number+' - '+result.worst_material[i].material_description+'</td>';
						// bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_material[i].qty_rec+'</td>';
						bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_material[i].qty_check+'</td>';
						bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_material[i].repair+'</td>';
						bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_material[i].return+'</td>';
						bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+result.worst_material[i].total_ng+'</td>';
						bodyWorstMaterial += '<td style="text-align:right;padding-right:7px !important;">'+parseFloat(result.worst_material[i].ratio).toFixed(2)+' %</td>';
						bodyWorstMaterial += '<td style="text-align:left;padding-left:7px !important;">'+result.worst_material[i].ng+'</td>';
						bodyWorstMaterial += '</tr>';
						index++;
					}
				}

				$('#bodyWorstMtrl').append(bodyWorstMaterial);

				$('#title_worst').html('TOP 10 WORST VENDOR ON '+result.monthTitle.toUpperCase());

				$("#tableWorstMtrl").show();
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}
	Highcharts.createElement('link', {
			href: '{{ url("fonts/UnicaOne.css")}}',
			rel: 'stylesheet',
			type: 'text/css'
		}, null, document.getElementsByTagName('head')[0]);

		Highcharts.theme = {
			colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
			'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
			chart: {
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					stops: [
					[0, '#2a2a2b'],
					[1, '#3e3e40']
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



</script>
@endsection