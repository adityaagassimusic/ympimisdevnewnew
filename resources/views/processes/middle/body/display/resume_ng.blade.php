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
		<div class="col-xs-5" style="background-color: lightpink;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_ng_rate">TREND NG RATIO</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Location" onchange="changeLocation()" style="width: 100%;color: black !important">
					@foreach($location as $locations)
					@if(str_contains($locations,$product))
					<option value="{{$locations}}">{{strtoupper($locations)}}</option>
					@endif
					@endforeach
				</select>
				<input type="text" name="location" id="location" style="color: black !important" hidden>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_from" name="month_from" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_to" name="month_to" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillListNgRate()">
        		Search
        	</button>
        </div>
		<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;margin-bottom: 20px;">
			<div style="height: 43vh" id="container_ng_rate">
				
			</div>
		</div>

		<div class="col-xs-5" style="background-color: lightgreen;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_pareto_head">TREND NG RATIO</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" multiple="multiple" id="locationSelectHead" data-placeholder="Select Location" onchange="changeLocationHead()" style="width: 100%;color: black !important">
					@foreach($location2 as $locations)
					@if(str_contains($locations,$product))
					<option value="{{$locations}}">{{strtoupper($locations)}}</option>
					@endif
					@endforeach
				</select>
				<input type="text" name="location_head" id="location_head" style="color: black !important" hidden>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_from_head" name="month_from_head" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_to_head" name="month_to_head" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillParetoHead()">
        		Search
        	</button>
        </div>
		<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;margin-bottom: 20px;">
			<div style="height: 43vh" id="container_head">
				
			</div>
		</div>

		<div class="col-xs-5" style="background-color: lightskyblue;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_pareto_body">TREND NG RATIO</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" multiple="multiple" id="locationSelectBody" data-placeholder="Select Location" onchange="changeLocationBody()" style="width: 100%;color: black !important">
					@foreach($location3 as $locations)
					@if(str_contains($locations,$product))
					<option value="{{$locations}}">{{strtoupper($locations)}}</option>
					@endif
					@endforeach
				</select>
				<input type="text" name="location_body" id="location_body" style="color: black !important" hidden>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_from_body" name="month_from_body" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_to_body" name="month_to_body" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillParetoBody()">
        		Search
        	</button>
        </div>
		<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;margin-bottom: 20px;">
			<div style="height: 43vh" id="container_body">
				
			</div>
		</div>

		<div class="col-xs-5" style="background-color: lightsalmon;color:black;text-align: center;height: 35px;">
        	<span style="font-size: 25px;font-weight: bold;" id="title_pareto_foot">TREND NG RATIO</span>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="form-group">
				<select class="form-control select2" multiple="multiple" id="locationSelectFoot" data-placeholder="Select Location" onchange="changeLocationFoot()" style="width: 100%;color: black !important">
					@foreach($location4 as $locations)
					@if(str_contains($locations,$product))
					<option value="{{$locations}}">{{strtoupper($locations)}}</option>
					@endif
					@endforeach
				</select>
				<input type="text" name="location_foot" id="location_foot" style="color: black !important" hidden>
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_from_foot" name="month_from_foot" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-2" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control" id="month_to_foot" name="month_to_foot" placeholder="Select Month">
			</div>
        </div>
        <div class="col-xs-1" style="color:black;text-align: center;height: 35px;padding-right: 0px;padding-left: 5px;text-align: left;">
        	<button class="btn btn-success" style="width: 100%" onclick="fillParetoFoot()">
        		Search
        	</button>
        </div>
		<div class="col-xs-12" style="padding-right: 0px;padding-left: 0px;margin-bottom: 20px;">
			<div style="height: 43vh" id="container_foot">
				
			</div>
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
<script src="{{ url("js/pareto.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var kataconfirm = 'Apakah Anda yakin?';

	var locations = <?php echo json_encode($location5) ?>;
	var location_new = [];

	jQuery(document).ready(function() {
		location_new = [];
		var re = new RegExp('{{$product}}', 'g');
		for(var i = 0;i < locations.length;i++){
			if (locations[i].match(re)) {
				location_new.push(locations[i]);
			}
		}

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

		$('#month_from_head').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});

		$('#month_to_head').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		$('#month_from_body').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});

		$('#month_to_body').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		$('#month_from_foot').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});

		$('#month_to_foot').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		$('body').toggleClass("sidebar-collapse");

		fillListNgRate();
		fillParetoHead();
		fillParetoBody();
		fillParetoFoot();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
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

	function changeLocation() {
		$("#location").val($("#locationSelect").val());
	}

	function changeLocationHead() {
		$("#location_head").val($("#locationSelectHead").val());
	}

	function changeLocationBody() {
		$("#location_body").val($("#locationSelectBody").val());
	}

	function changeLocationFoot() {
		$("#location_foot").val($("#locationSelectFoot").val());
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fillListNgRate(){
		$('#loading').show();
		var data = {
			location:$('#location').val(),
			location_all:location_new.join(','),
			month_from:$('#month_from').val(),
			month_to:$('#month_to').val(),
		}
		$.get('{{ url("fetch/body/resume_ng") }}',data, function(result, status, xhr){
			if(result.status){
				var months = [];
				var categories = [];
				var persen_head = [];
				var persen_body = [];
				var persen_foot = [];

				$.each(result.ng_rate_head, function(key,value){
					months.push(value.months);
				});

				$.each(result.ng_rate_body, function(key,value){
					months.push(value.months);
				});

				$.each(result.ng_rate_foot, function(key,value){
					months.push(value.months);
				});

				var month_unik = months.filter(onlyUnique);

				for(var i = 0; i < month_unik.length;i++){
					var month_name = '';
					var ng_body = 0;
					var ng_head = 0;
					var ng_foot = 0;

					var check_body = 0;
					var check_head = 0;
					var check_foot = 0;
					for(var j = 0; j < result.ng_rate_head.length;j++){
						if (month_unik[i] == result.ng_rate_head[j].months) {
							check_head = check_head + parseInt(result.ng_rate_head[j].check);
							ng_head = ng_head + parseInt(result.ng_rate_head[j].ng);
							month_name = result.ng_rate_head[j].month_name;
						}
					}
					for(var k = 0; k < result.ng_rate_body.length;k++){
						if (month_unik[i] == result.ng_rate_body[k].months) {
							check_body = check_body + parseInt(result.ng_rate_body[k].check);
							ng_body = ng_body + parseInt(result.ng_rate_body[k].ng);
							month_name = result.ng_rate_body[k].month_name;
						}
					}
					for(var l = 0; l < result.ng_rate_foot.length;l++){
						if (month_unik[i] == result.ng_rate_foot[l].months) {
							check_foot = check_foot + parseInt(result.ng_rate_foot[l].check);
							ng_foot = ng_foot + parseInt(result.ng_rate_foot[l].ng);
							month_name = result.ng_rate_foot[l].month_name;
						}
					}
					persen_body.push(parseFloat(((ng_body/check_body)*100).toFixed(1)));
					persen_head.push(parseFloat(((ng_head/check_head)*100).toFixed(1)));
					persen_foot.push(parseFloat(((ng_foot/check_foot)*100).toFixed(1)));
					categories.push(month_name);
				}

				Highcharts.chart('container_ng_rate', {
				    chart: {
				        zoomType: 'xy'
				    },
				    title: {
				        text: ""
				    },
				    subtitle: {
				        text: ''
				    },
				    xAxis: [{
				        categories: categories,
				        crosshair: true,
				        labels:{
				        	style:{
				        		fontSize: '15px'
				        	}
				        }
				    }],
				    yAxis: { 
				        title: {
				            text: 'NG Ratio',
				            style: {
				                color: '#fff',
				                fontSize: '15px'
				            }
				        },
				        labels: {
				            format: '{value}%',
				            style: {
				                color: '#fff',
				                fontSize: '15px'
				            }
				        },
				    },
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
						spline:{
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
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							// cursor: 'pointer',
							borderColor: 'black',
						},
					},
				    series: [
				    {
				        name: 'NG Ratio Head',
				        type: 'spline',
				        data: persen_head,
				        color: '#45a12b',
				        tooltip: {
				            valueSuffix: '%'
				        }
				    },
				    {
				        name: 'NG Ratio Body',
				        type: 'spline',
				        data: persen_body,
				        color: '#3d48db',
				        tooltip: {
				            valueSuffix: '%'
				        }
				    },
				    {
				        name: 'NG Ratio Foot',
				        type: 'spline',
				        data: persen_foot,
				        color: '#eb7617',
				        tooltip: {
				            valueSuffix: '%'
				        }
				    }]
				});

				$('#title_ng_rate').html('TREND NG RATIO {{strtoupper($product)}} ON '+result.monthTitle.toUpperCase());
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function fillParetoHead(){
		$('#loading').show();
		var data = {
			location:$('#location_head').val(),
			location_all:location_new.join(','),
			month_from:$('#month_from_head').val(),
			month_to:$('#month_to_head').val(),
			cat:'HEAD'
		}
		$.get('{{ url("fetch/body/pareto") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var pareto = [];

				var others = 0;

				for(var i = 0; i < result.pareto.length;i++){
					if (i < 10) {
						pareto.push(parseInt(result.pareto[i].qty));
						categories.push(result.pareto[i].ng_name);
					}else{
						others = others + parseInt(result.pareto[i].qty);
					}
				}
				pareto.push(others);
				categories.push('Other');

				Highcharts.chart('container_head', {
				    chart: {
				        renderTo: 'container_head',
				        type: 'column'
				    },
				    title: {
				        text: '',
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
									fontSize: '15px'
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
			                    	// showModalDetailPareto(this.options.key);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y:,.0f}%',
								style:{
									fontSize: '14px'
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
				        crosshair: true,
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'15px',
				            }
				        }
				    },
				    yAxis: [{
				        title: {
				            text: 'Total Defect',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'13px',
				            }
				        }
				    }, {
				        title: {
				            text: 'Pareto',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        minPadding: 0,
				        maxPadding: 0,
				        max: 100,
				        min: 0,
				        opposite: true,
				        labels: {
				            format: "{value}%",
				            style:{
				            	fontSize:'13px',
				            }
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
				        color:'#fff',
				    }, {
				        name: 'Total Defect',
				        type: 'column',
				        zIndex: 2,
				        data: pareto,
				        colorByPoint:false,
				        color:'#45a12b',
				    },
				    ]
				});

				$('#title_pareto_head').html('PARETO DEFECT {{strtoupper($product)}} HEAD ON '+result.monthTitle.toUpperCase());
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function fillParetoBody(){
		$('#loading').show();
		var data = {
			location:$('#location_body').val(),
			location_all:location_new.join(','),
			month_from:$('#month_from_body').val(),
			month_to:$('#month_to_body').val(),
			cat:'BODY'
		}
		$.get('{{ url("fetch/body/pareto") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var pareto = [];
				var others = 0;

				for(var i = 0; i < result.pareto.length;i++){
					if (i < 10) {
						pareto.push(parseInt(result.pareto[i].qty));
						categories.push(result.pareto[i].ng_name);
					}else{
						others = others + parseInt(result.pareto[i].qty);
					}
				}
				pareto.push(others);
				categories.push('Other');

				Highcharts.chart('container_body', {
				    chart: {
				        renderTo: 'container_body',
				        type: 'column'
				    },
				    title: {
				        text: '',
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
									fontSize: '15px'
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
			                    	// showModalDetailPareto(this.options.key);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y:,.0f}%',
								style:{
									fontSize: '14px'
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
				        crosshair: true,
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'15px',
				            }
				        }
				    },
				    yAxis: [{
				        title: {
				            text: 'Total Defect',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'13px',
				            }
				        }
				    }, {
				        title: {
				            text: 'Pareto',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        minPadding: 0,
				        maxPadding: 0,
				        max: 100,
				        min: 0,
				        opposite: true,
				        labels: {
				            format: "{value}%",
				            style:{
				            	fontSize:'13px',
				            }
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
				        color:'#fff',
				    }, {
				        name: 'Total Defect',
				        type: 'column',
				        zIndex: 2,
				        data: pareto,
				        colorByPoint:false,
				        color:'#3d48db',
				    },
				    ]
				});

				$('#title_pareto_body').html('PARETO DEFECT {{strtoupper($product)}} BODY ON '+result.monthTitle.toUpperCase());
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function fillParetoFoot(){
		$('#loading').show();
		var data = {
			location:$('#location_foot').val(),
			location_all:location_new.join(','),
			month_from:$('#month_from_foot').val(),
			month_to:$('#month_to_foot').val(),
			cat:'FOOT'
		}
		$.get('{{ url("fetch/body/pareto") }}',data, function(result, status, xhr){
			if(result.status){
				var categories = [];
				var pareto = [];
				var others = 0;

				for(var i = 0; i < result.pareto.length;i++){
					if (i < 10) {
						pareto.push(parseInt(result.pareto[i].qty));
						categories.push(result.pareto[i].ng_name);
					}else{
						others = others + parseInt(result.pareto[i].qty);
					}
				}
				pareto.push(others);
				categories.push('Other');

				Highcharts.chart('container_foot', {
				    chart: {
				        renderTo: 'container_foot',
				        type: 'column'
				    },
				    title: {
				        text: '',
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
									fontSize: '15px'
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
			                    	// showModalDetailPareto(this.options.key);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y:,.0f}%',
								style:{
									fontSize: '14px'
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
				        crosshair: true,
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'15px',
				            }
				        }
				    },
				    yAxis: [{
				        title: {
				            text: 'Total Defect',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        labels: {
				            format: "{value}",
				            style:{
				            	fontSize:'13px',
				            }
				        }
				    }, {
				        title: {
				            text: 'Pareto',
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold',
				            	color:'#fff'
				            }
				        },
				        minPadding: 0,
				        maxPadding: 0,
				        max: 100,
				        min: 0,
				        opposite: true,
				        labels: {
				            format: "{value}%",
				            style:{
				            	fontSize:'13px',
				            }
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
				        color:'#fff',
				    }, {
				        name: 'Total Defect',
				        type: 'column',
				        zIndex: 2,
				        data: pareto,
				        colorByPoint:false,
				        color:'#eb7617',
				    },
				    ]
				});

				$('#title_pareto_foot').html('PARETO DEFECT {{strtoupper($product)}} FOOT ON '+result.monthTitle.toUpperCase());
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