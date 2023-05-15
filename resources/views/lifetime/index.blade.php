@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		background-color: rgba(126,86,134,.7);
		vertical-align: middle;
		color: black;
		font-size: 1vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		padding:0;
		font-size: 1vw;
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		color: black;
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
	<div class="row">
		<!-- <div class="col-xs-12" style="padding-bottom: 10px">
			<div class="col-xs-5 pull-right" style="padding-left: 5px;padding-right: 5px">
				
			</div>
		</div> -->
		<div class="col-xs-12" style="padding-right: 5px;">
			<!-- <div class="box box-solid" style="height: 35vh">
				<div class="box-body">
					<center style="background-color: #605ca8;color: white"><h4 style="font-weight: bold;padding: 5px;margin-top: 0px">Filter</h4></center>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Kode Sertifikat</span>
							<div class="form-group">
								
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Status</span>
							<div class="form-group">
								<select class="form-control select2" name="status" id="status" data-placeholder="Pilih Status" style="width: 100%;">
									<option></option>
									<option value="1">Active</option>
									<option value="0">Inactive</option>
									<option value="3">Expired</option>
									<option value="2">Renewal</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/certificate') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/certificate/code') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> -->
			<div class="nav-tabs-custom">
				<div class="tab-content">
					<div class="tab-pane active">
						<div class="row">
							<div class="col-xs-12">
								<div class="col-xs-5" style="vertical-align: bottom;">
									<div class="col-xs-6" style="padding-left: 0px;padding-right: 0px;">
										<a class="btn btn-primary" style="margin-bottom: 10px;width: 100%" href="{{url('index/master/lifetime/'.$category.'/'.$location)}}">
											<i class="fa fa-book"></i> Master Data
										</a>
									</div>
									<div class="col-xs-6" style="padding-left: 4px;padding-right: 0px;">
										@if($category == 'jig')
										<a class="btn btn-success" style="margin-right: 5px;margin-bottom: 10px;width: 100%" href="{{url('index/record/lifetime/'.$category.'/'.$location)}}">
											<i class="fa fa-pencil"></i> Recording
										</a>
										@endif
									</div>
									@if($category == 'jig')
									<div class="col-xs-6" style="padding-left: 0px;padding-right: 0px;">
										<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih Product" id="selectProduct" onchange="selectProduct(this.value)">
											<option value=""></option>
											<option value="All">All</option>
											@foreach($products as $products)
											<option value="{{$products->product}}">{{$products->product}}</option>
											@endforeach
										</select>
									</div>
									@endif
									<div class="col-xs-6" style="padding-left: 4px;padding-right: 0px;">
										<select style="width: 100%;" class="form-control select2" data-placeholder="Pilih Item" id="selectItem" onchange="fillList()">
											<option value=""></option>
										</select>
									</div>
									@if($category == 'screwdriver')
									<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
										<button class="btn btn-success pull-right"style="width: 100%;font-size: 17px;" onclick="useModal()"><b>Mulai Pakai</b></button>
									</div>
									@endif
									<div class="col-xs-12">
										<div id="container_pie"></div>
									</div>
									<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
										<div style="margin-bottom: 10px;">
											<select class="form-control select2" data-placeholder="Select Avail" name="select_availability" id="select_availability" style="width: 100%;margin-bottom: 10px;" onchange="selectAvailability()">
												<option value=""></option>
												<option value="Used">Used</option>
												<option value="Repair">Repair</option>
												<option value="Not Use">Not Use</option>
											</select>
										</div>
										<table id="tableLifetime" class="table table-bordered" style="margin:0;margin-top: 10px;">
											<thead id="headLifetime">
												<tr>
													<th style="padding: 0px;width: 5%;text-align:left;padding-left:7px;font-size: 15px;">Item Code</th>
													<th style="padding: 0px;width: 8%;text-align:left;padding-left:7px;font-size: 15px;">Item</th>
													<th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Made In</th>
													<!-- <th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Limit</th>
													<th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Unit</th> -->
													@if($category == 'screwdriver')
													<th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Used (Days)</th>
													<th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Rep</th>
													@else
													<th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Lifetime</th>
													<th style="padding: 0px;width: 1%;text-align:left;padding-left:7px;font-size: 15px;">Rep</th>
													@endif
													<th style="padding: 0px;width: 5%;text-align:left;padding-left:7px;font-size: 15px;">Act</th>
												</tr>
											</thead>
											<tbody id="bodyLifetime">
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-xs-7">
									<div id="container" style="width: 99%;"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<div class="modal modal-default fade" id="use-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white" id="title_use">Mulai Pakai {{ucwords($category)}}</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Scan ID Card Karyawan<span class="text-red">*</span></label>
									<input type="hidden" name="id_use" id="id_use">
									<input type="hidden" name="tag_use" id="tag_use">
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="tag_employee_use" placeholder="Scan ID Card Karyawan" required>
									</div>
									<div class="col-sm-2" align="left">
										<button class="btn btn-danger" onclick="cancelScan('tag_employee_use')"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Scan {{ucwords($category)}}<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="tag_item_use" placeholder="Scan {{ucwords($category)}}" required>
									</div>
									<div class="col-sm-2" align="left">
										<button class="btn btn-danger" onclick="cancelScan('tag_item_use')"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#use-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="use()"><i class="fa fa-pencil"></i> Confirm</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="un-use-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white" id="title_un_use">Selesai Pakai {{ucwords($category)}}</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Scan ID Card Karyawan<span class="text-red">*</span></label>
									<input type="hidden" name="id_un_use" id="id_un_use">
									<input type="hidden" name="tag_un_use" id="tag_un_use">
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="tag_employee_un_use" placeholder="Scan ID Card Karyawan" required>
									</div>
									<div class="col-sm-2" align="left">
										<button class="btn btn-danger" onclick="cancelScan('tag_employee_un_use')"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Scan ID Card Sub Leader<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="tag_subleader_un_use" placeholder="Scan ID Sub Leader" required>
									</div>
									<div class="col-sm-2" align="left">
										<button class="btn btn-danger" onclick="cancelScan('tag_subleader_un_use')"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Scan {{ucwords($category)}}<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="tag_item_un_use" placeholder="Scan {{ucwords($category)}}" required>
									</div>
									<div class="col-sm-2" align="left">
										<button class="btn btn-danger" onclick="cancelScan('tag_item_un_use')"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#un-use-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="unUse()"><i class="fa fa-pencil"></i> Confirm</button>
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
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var items = null;
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		items = null;

		setInterval(fillList,1200000);
	});

	var availability = ['Empty','Used','Repair','Not Use'];

	function selectAvailability() {
	    var input, filter, table,tbody, tr, td, i, txtValue;
	      input = document.getElementById("select_availability");
	      filter = input.value;
	      if (filter == null) {
	        table = document.getElementById("bodyLifetime");
	        tr = table.getElementsByTagName("tr");
	        for (i = 0; i < tr.length; i++) {
	          td = tr[i].getElementsByTagName("td")[0];
	          if (td) {
	              tr[i].style.display = "";
	          }
	        }
	      }else{
	        table = document.getElementById("bodyLifetime");
	        tr = table.getElementsByTagName("tr");
	        for (i = 0; i < tr.length; i++) {
	          // td = tr[i].getElementsByTagName("td")[0];
	          // console.log(td);
	          if (tr[i].getAttribute("id").indexOf(filter) > -1) {
	            // txtValue = td.textContent || td.innerText;
	            // if (txtValue.indexOf(filter) > -1) {
	              tr[i].style.display = "";
	            // } else {
	              
	            // }
	          }else{
	            tr[i].style.display = "none";
	          }
	        }
	      }
	  }

	function fillList(){
		$('#loading').show();

		var data = {
			product:$("#selectProduct").val(),
			item_name:$("#selectItem").val(),
		}
		
		$.get('{{ url("fetch/lifetime/".$category."/".$location) }}', data,function(result, status, xhr){
			if(result.status){
				if (result.lifetime != null) {
					$('#tableLifetime').DataTable().clear();
					$('#tableLifetime').DataTable().destroy();
					var categories = [];
					var series = [];
					var target = [];
					items = null;

					$('#bodyLifetime').html('');
					var bodyLifetime = '';
					var item_code = [];
					for(var i = 0; i < result.lifetime.length;i++){
						item_code.push(result.lifetime[i].item_code);
						if (parseInt(result.lifetime[i].repair) > 0) {
							if (result.lifetime[i].availability == 2) {
								categories.push('<div id="'+result.lifetime[i].item_code+'" style="color:#e88113">'+result.lifetime[i].item_alias+'</div>');
							}else{
								categories.push('<div id="'+result.lifetime[i].item_code+'" style="color:red">'+result.lifetime[i].item_alias+'</div>');
							}
						}else{
							if (result.lifetime[i].availability == 2) {
								categories.push('<div id="'+result.lifetime[i].item_code+'" style="color:#e88113">'+result.lifetime[i].item_alias+'</div>');
							}else{
								categories.push('<div id="'+result.lifetime[i].item_code+'" style="color:#000">'+result.lifetime[i].item_alias+'</div>');
							}
						}

						var diff = parseInt(result.lifetime[i].lifetime_limit) - parseInt(result.lifetime[i].lifetime);
						var color = '';
						var colors = '#50c42d';

						if ('{{$category}}' == 'screwdriver') {
							if (diff <= 0) {
								var colors = '#ff9c9c';
								var color = 'background-color:#ff9c9c';
							}
							target.push(parseInt(result.lifetime[i].lifetime_limit));
							var lifetimes = result.lifetime[i].days;
						}else{
							var lifetimes = result.lifetime[i].lifetime;
							if (result.lifetime[i].availability == 2) {
								var colors = '#ff9c24';
								var color = 'background-color:#ffc885';
							}
						}

						series.push({y:parseInt(lifetimes),color:colors});

						var url = '{{url("index/repair/lifetime/".$category."/".$location)}}/'+result.lifetime[i].id;

						bodyLifetime += '<tr id="'+availability[result.lifetime[i].availability]+'">';
						bodyLifetime += '<td style="font-size:13px;text-align:left;padding-left:7px;'+color+'">'+result.lifetime[i].item_code+'</td>';
						bodyLifetime += '<td style="font-size:13px;text-align:left;padding-left:7px;'+color+'">'+result.lifetime[i].item_alias+'</td>';
						bodyLifetime += '<td style="font-size:13px;text-align:left;padding-left:7px;'+color+'">'+result.lifetime[i].item_made_in+'</td>';
						// bodyLifetime += '<td style="font-size:13px;text-align:right;padding-right:7px;'+color+'">'+parseInt(result.lifetime[i].lifetime_limit)+'</td>';
						// bodyLifetime += '<td style="font-size:13px;text-align:left;padding-left:7px;'+color+'">'+result.lifetime[i].limit_unit+'</td>';
						bodyLifetime += '<td style="font-size:13px;text-align:right;padding-right:7px;'+color+'">'+parseInt(lifetimes)+'</td>';
						bodyLifetime += '<td style="font-size:13px;text-align:right;padding-right:7px;'+color+'">'+parseInt(result.lifetime[i].repair)+'</td>';
						bodyLifetime += '<td style="font-size:13px;text-align:left;padding-left:7px;'+color+'">';
						if (result.lifetime[i].availability == 1) {
							if ('{{$category}}' == 'jig') {
								if ('{{$role}}'.match('L-') || '{{$role}}'.match('S-')) {
									bodyLifetime += '<a href="'+url+'" class="btn btn-xs btn-success">Mulai Repair</a>';
								}
							}
							if ('{{$category}}' == 'screwdriver') {
								if (result.lifetime[i].employee_id == null) {
								}else{
									bodyLifetime += '<button class="btn btn-warning btn-xs" onclick="unUseModal(\''+result.lifetime[i].id+'\',\''+result.lifetime[i].tag+'\',\''+result.lifetime[i].item_alias+'\');">Selesai Pakai</button>';
								}
								if ('{{$role}}'.match('L-') || '{{$role}}'.match('S-')) {
									bodyLifetime += '<a href="'+url+'" class="btn btn-xs btn-success">Mulai Repair</a>';
								}
							}
						}else if(result.lifetime[i].availability == 2){
							bodyLifetime += 'Repair';
						}else if(result.lifetime[i].availability == 3){
							bodyLifetime += 'Not Use<br>';
							if ('{{$role}}'.match('L-') || '{{$role}}'.match('S-')) {
								bodyLifetime += '<a href="'+url+'" class="btn btn-xs btn-success">Mulai Repair</a>';
							}
						}
						bodyLifetime += '</td>';
						bodyLifetime += '</tr>';
					}

					$('#bodyLifetime').append(bodyLifetime);

					Highcharts.chart('container', {
					    chart: {
					        type: 'bar',
					        height:'2500px'
					    },
					    title: {
							text: '<span style="font-size: 18pt;">Lifetime {{ucwords($category)}} {{ucwords($location)}}</span>',
							useHTML: true,
							style:{
								fontWeight:'bold'
							}
						},
					    xAxis: {
					       categories: categories,
						   labels: {
						      useHTML: true,
						      style: {
						        color: 'black',
						      }
						    }
					    },
					    yAxis: {
					        title: {
								text: 'Lifetime'
							},
							stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '0.8vw'
                                }
                            },
					    },
					    plotOptions: {
							column: {
								cursor: 'pointer',
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									// formatter: function () {
									// 	return Highcharts.numberFormat(this.y,2)+'%';
									// }
								}
							},
							line: {
								marker: {
									enabled: false
								},
								dashStyle: 'ShortDash'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Lifetime',
							data: series
						},
						{
							name: 'Target',
							type: 'line',
							data: target,
							color: '#FF0000',
						}
						]
					});

					// Highcharts.chart('container_pie', {
					//     chart: {
					//         plotBackgroundColor: null,
					//         plotBorderWidth: null,
					//         plotShadow: false,
					//         type: 'column'
					//     },
					//     title: {
					//         text: ''
					//     },
					//     tooltip: {
					//         pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					//     },
					//     accessibility: {
					//         point: {
					//             valueSuffix: '%'
					//         }
					//     },
					//     plotOptions: {
					//         series: {
					//             allowPointSelect: true,
					//             cursor: 'pointer',
					//             dataLabels: {
					//                 enabled: false
					//             },
					//             showInLegend: true,
					//             point: {
					// 				events: {
					// 					click: function () {
					// 						filterTable(this.name);
					// 					}
					// 				}
					// 			},
					//         }
					//     },
					//     legend: {
					// 		symbolRadius: 1,
					// 		borderWidth: 1,
					// 		labelFormat: '<span style="color:{color}">{name}</span>: <b>{y}</b> ({percentage:.1f}%)<br/>',
					// 	},
					// 	credits:{
					// 		enabled:false
					// 	},
					//     series: [{
					//         name: '{{ucwords($category)}}',
					//         colorByPoint: true,
					//         data: [{
					// 			name: 'Used',
					// 			y: parseInt(result.pie_charts[0].used),
					// 			color: '#32a852'
					// 		},{
					// 			name: 'Repair',
					// 			y: parseInt(result.pie_charts[0].repair),
					// 			color: '#d3a72f'
					// 		},{
					// 			name: 'Not Use',
					// 			y: parseInt(result.pie_charts[0].not_use),
					// 			color: '#d32f2f'
					// 		}]
					//     }]
					// });

					Highcharts.chart('container_pie', {
						chart: {
							type: 'column',
							height:'300'
						},
						title: {
							text: '',
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: ['Used','Repair','Not Use'],
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '13px',
									fontWeight:'bold'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Qty {{ucwords($category)}}',
								style:{
									fontSize: '13px',
									fontWeight:'bold'
								}
							},
							labels:{
								style:{
									fontSize: '13px',
									fontWeight:'bold'
								}
							}
						},
						legend: {
							enabled:false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>Qty {{ucwords($category)}} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											filterTable(this.category);
										}
									}
								},
								dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.y != 0) ? this.y : "";
                                    },
                                    style: {
                                        textOutline: false
                                    }
                                },
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Quantity',
							data: [
							{y:parseInt(result.pie_charts[0].used),color:'#0fa628'},
							{y:parseInt(result.pie_charts[0].repair),color:'#f09c35'},
							{y:parseInt(result.pie_charts[0].not_use),color:'#858585'}
							],
							color: '#e88113',
						},
						]
					});

					var table = $('#tableLifetime').DataTable({
						'dom': 'Bfrtip',
						// 'lengthMenu': [
						// [ 20, 50, 100, -1 ],
						// [ '20 rows', '50 rows', '100 rows', 'Show all' ]
						// ],
						'buttons': {
							buttons:[
							// {
							// 	extend: 'pageLength',
							// 	className: 'btn btn-default',
							// },
							// {
							// 	extend: 'excel',
							// 	className: 'btn btn-info',
							// 	text: '<i class="fa fa-file-excel-o"></i> Excel',
							// 	exportOptions: {
							// 		columns: ':not(.notexport)'
							// 	}
							// },
							]
						},
						'paging': false,
						'lengthChange': false,
						// 'pageLength': 20,
						'searching'   	: true,
						'ordering'		: false,
						'order': [],
						'info'       	: true,
						'autoWidth'		: false,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						// "infoCallback": function( settings, start, end, max, total, pre ) {
						// 	return "<b>Total "+ total +" pc(s)</b>";
						// }
					});

					items = result.item;
					selectProduct('screwdriver');
				}

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function filterTable(availability) {
		$('#select_availability').val(availability).trigger('change');
	}

	function useModal() {
		// $('#id_use').val(id);
		// $('#tag_use').val(tag);
		// $("#title_use").html('Mulai Pakai '+alias);
		$('#tag_item_use').val('');
		$('#title_use').html('Mulai Pakai '+'{{ucwords($category)}}');
		$('#tag_employee_use').val('');
		$('#use-modal').modal('show');
		$('#tag_employee_use').focus();
	}

	function unUseModal(id,tag,alias) {
		$('#tag_item_un_use').val('');
		$('#tag_employee_un_use').val('');
		$('#tag_subleader_un_use').val('');
		$('#title_un_use').html('Selesai Pakai '+alias);
		$('#tag_un_use').val(tag);
		$('#id_un_use').val(id);
		$('#tag_subleader_un_use').focus();
		$('#un-use-modal').modal('show');
	}

	function cancelScan(id) {
		$('#'+id).removeAttr('disabled');
		$('#'+id).val('');
		$('#'+id).focus();
	}

	$('#tag_employee_use').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var data = {
				employee_id : $("#tag_employee_use").val(),
			}

			$.get('{{ url("scan/operator/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#tag_employee_use').val(result.employee.employee_id+' - '+result.employee.name);
					$('#tag_item_use').val('');
					$('#tag_item_use').focus();
					$('#tag_employee_use').prop('disabled',true);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_employee_use').removeAttr('disabled');
					$('#tag_employee_use').val('');
					$('#tag_employee_use').focus();
				}
			});
		}
	});

	$('#tag_employee_un_use').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var data = {
				employee_id : $("#tag_employee_un_use").val(),
			}

			$.get('{{ url("scan/operator/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#tag_employee_un_use').val(result.employee.employee_id+' - '+result.employee.name);
					$('#tag_subleader_un_use').val('');
					$('#tag_subleader_un_use').focus();
					$('#tag_employee_un_use').prop('disabled',true);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_employee_un_use').removeAttr('disabled');
					$('#tag_employee_un_use').val('');
					$('#tag_employee_un_use').focus();
				}
			});
		}
	});

	$('#tag_subleader_un_use').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var data = {
				employee_id : $("#tag_subleader_un_use").val(),
			}

			$.get('{{ url("scan/operator/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#tag_subleader_un_use').val(result.employee.employee_id+' - '+result.employee.name);
					$('#tag_item_un_use').val('');
					$('#tag_item_un_use').focus();
					$('#tag_subleader_un_use').prop('disabled',true);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_subleader_un_use').removeAttr('disabled');
					$('#tag_subleader_un_use').val('');
					$('#tag_subleader_un_use').focus();
				}
			});
		}
	});

	$('#tag_item_use').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var data = {
				tag : $("#tag_item_use").val(),
			}

			$.get('{{ url("scan/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', 'Scan Success');
					$('#id_use').val(result.lifetime.id);
					$('#tag_use').val(result.lifetime.tag);
					$('#title_use').html('Mulai Pakai '+result.lifetime.item_alias);
					$('#tag_item_use').prop('disabled',true);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_item_use').removeAttr('disabled');
					$('#tag_item_use').val('');
					$('#tag_item_use').focus();
				}
			});
		}
	});

	$('#tag_item_un_use').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var data = {
				tag : $("#tag_item_un_use").val(),
			}

			$.get('{{ url("scan/record/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', 'Scan Success');
					$('#id_un_use').val(result.lifetime.id);
					$('#tag_un_use').val(result.lifetime.tag);
					$('#title_un_use').html('Selesai Pakai '+result.lifetime.item_alias);
					$('#tag_item_un_use').prop('disabled',true);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_item_un_use').removeAttr('disabled');
					$('#tag_item_un_use').val('');
					$('#tag_item_un_use').focus();
				}
			});
		}
	});

	function use() {
		$('#loading').show();
		var employee_id = $('#tag_employee_use').val().split(' - ')[0];
		var employee_name = $('#tag_employee_use').val().split(' - ')[1];
		var tag = $('#tag_item_use').val();
		var id = $('#id_use').val();

		var data = {
			id:id,
			tag:tag,
			employee_id:employee_id,
			employee_name:employee_name
		}

		$.post('{{ url("input/use/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Item Used From Now');
				fillList();
				$('#use-modal').modal('hide');
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function unUse() {
		$('#loading').show();
		if ($('#tag_employee_un_use').val() == '' || $('#tag_subleader_un_use').val() == '' || $('#tag_item_un_use').val() == '') {
			audio_error.play();
			openErrorGritter("Error!",'Semua Harus Diisi.');
			$('#loading').hide();
			return false;
		}
		var employee_id = $('#tag_employee_un_use').val().split(' - ')[0];
		var employee_name = $('#tag_employee_un_use').val().split(' - ')[1];
		var subleader_id = $('#tag_subleader_un_use').val().split(' - ')[0];
		var subleader_name = $('#tag_subleader_un_use').val().split(' - ')[1];
		var tag = $('#tag_item_un_use').val();
		var id = $('#id_un_use').val();

		var data = {
			id:id,
			tag:tag,
			employee_id:employee_id,
			employee_name:employee_name,
			subleader_id:subleader_id,
			subleader_name:subleader_name
		}

		$.post('{{ url("input/unuse/lifetime/".$category."/".$location) }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Item Not Use From Now');
				fillList();
				$('#un-use-modal').modal('hide');
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function selectProduct(value) {
		if (value == 'screwdriver') {
			$("#selectItem").html('');
			var itemSelect = '';
			itemSelect += '<option value=""></option>';
			itemSelect += '<option value="All">All</option>';
			for(var i = 0; i < items.length;i++){
				itemSelect += '<option value="'+items[i].item_name+'">'+items[i].item_name+'</option>';
			}
			$("#selectItem").append(itemSelect);
		}else{
			$("#selectItem").html('');
			var itemSelect = '';
			itemSelect += '<option value=""></option>';
			itemSelect += '<option value="All">All</option>';
			if (value == '') {
				for(var i = 0; i < items.length;i++){
					itemSelect += '<option value="'+items[i].item_name+'">'+items[i].item_name+'</option>';
				}
			}else{
				for(var i = 0; i < items.length;i++){
					if (value == items[i].product) {
						itemSelect += '<option value="'+items[i].item_name+'">'+items[i].item_name+'</option>';
					}
				}
			}
			$("#selectItem").append(itemSelect);

			fillList();
		}
	}


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

</script>
@endsection