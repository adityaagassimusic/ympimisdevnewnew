@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }

	.content-wrapper{
		background-color: #ecf0f5 !important;
	}

	.control-label{
		text-align: left !important;
	}

	.content-wrapper{
		background-color: ;
	}

</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
		<div class="col-xs-2" style="padding-bottom: 10px">
			<select class="form-control select2" name="fiscal_year" id='fiscal_year' style="width: 100%;" onchange="Chart()">
				@foreach($fy as $fy)
				<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
				@endforeach
			</select>
		</div>
		<div class="col-xs-10" style="padding-bottom: 10px" align="right">
			<p style="font-size: 15px; color: white">Last Update: {{ date('d-M-Y H:i:s') }}</p>
		</div>
		<div class="col-xs-12">
			<div id="chart" style="height: 60vh"></div>
		</div>
		<div class="col-xs-12">
			<div id="chart2" style="height: 60vh;margin-top: 10px;"></div>
		</div>
	</div>
</section>


<div class="modal fade" id="ModalDetailPIC" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<span style="font-weight: bold; font-size: 2vw;" class="text-purple" id="title-detail-pic"></span>
				</div>
				<div class="col-md-12" style="padding-top: 10px">
					<table id="detailModalPIC" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 10%;">ID</th>
								<th style="width: 10%;">Dept.</th>
								<th style="width: 50%;">Title</th>
								<th style="width: 10%;">Status</th>
								<th style="width: 20%;">PIC</th>
							</tr>
						</thead>
						<tbody id="detailModalPICBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="DetailPerolehan" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<span style="font-weight: bold; font-size: 2vw;" class="text-purple" id="title-detail-perolehan"></span>
				</div>
				<div class="col-md-12" style="padding-top: 10px">
					<table id="detailModalPerolehan" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 10%;">ID</th>
								<th style="width: 10%;">Dept.</th>
								<th style="width: 50%;">Title</th>
								<th style="width: 10%;">Status</th>
								<th style="width: 20%;">PIC</th>
							</tr>
						</thead>
						<tbody id="detailModalPerolehanBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


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
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			allowClear : true,
		});
		Chart();
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

	function onlyUnique(value, index, self) {
		return self.indexOf(value) === index;
	}

	function Chart() {
		$("#loading").show();

    	var fy = $('#fiscal_year').val();

    	var data = {
	      fy:fy
	    };

		$.get('{{ url("fetch/member/mis") }}',data ,function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$("#loading").hide();
					var categori = [];
					var series_fin = [];
					var series_op = [];


					var fy = result.p;

					$.each(result.wc, function(key, value){
						var date = new Date(value.bulan);
						var nama_bulan = date.getMonth();
						switch(nama_bulan) {
							case 0: nama_bulan = "January"; break;
							case 1: nama_bulan = "February"; break;
							case 2: nama_bulan = "March"; break;
							case 3: nama_bulan = "April"; break;
							case 4: nama_bulan = "May"; break;
							case 5: nama_bulan = "June"; break;
							case 6: nama_bulan = "July"; break;
							case 7: nama_bulan = "August"; break;
							case 8: nama_bulan = "September"; break;
							case 9: nama_bulan = "October"; break;
							case 10: nama_bulan = "November"; break;
							case 11: nama_bulan = "December"; break;
						}
						var isi = 0;
						categori.push(nama_bulan);
						$.each(result.result_month_finish, function(key2, value2){
							if (value.bulan == value2.bulan) {
								series_fin.push(value2.jumlah);
								isi = 1;
							}
						});
						$.each(result.result_month_open, function(key2, value2){
							if (value.bulan == value2.bulan) {
								series_op.push(value2.jumlah);
								isi = 1;
							}
						});
						if (isi == 0) {
							series_fin.push(0);
							series_op.push(0);
						}
					});

					


					// $.each(result.wc, function(key, value){

					// 	var date = new Date(value.bulan);
					// 	var nama_bulan = date.getMonth();

					// 	switch(nama_bulan) {
					// 		case 0: nama_bulan = "January"; break;
					// 		case 1: nama_bulan = "February"; break;
					// 		case 2: nama_bulan = "March"; break;
					// 		case 3: nama_bulan = "April"; break;
					// 		case 4: nama_bulan = "May"; break;
					// 		case 5: nama_bulan = "June"; break;
					// 		case 6: nama_bulan = "July"; break;
					// 		case 7: nama_bulan = "August"; break;
					// 		case 8: nama_bulan = "September"; break;
					// 		case 9: nama_bulan = "October"; break;
					// 		case 10: nama_bulan = "November"; break;
					// 		case 11: nama_bulan = "December"; break;
					// 	}


					// 	var isi2 = 0;
					// 	var isi3 = 0;

					// 	categori_pic.push(nama_bulan);

					// 	$.each(result.software_member, function(key2, value2){
					// 		$.each(result.result_member, function(key3, value3){
					// 			if (value2.employee_id == value3.pic_id && value.bulan == value3.bulan) {
					// 				pic.push(value2.name);
					// 				series_finish.push(parseInt(value3.jumlah));
					// 				isi2 += 1;
					// 			}
					// 		});
					// 		if (isi2 == 0) {
					// 			series_finish.push(0);
					// 		}
					// 	});
					// });

					var pic = [];
					var series_finish = [];
					var series_progress = [];
					var categori_pic = [];

					var bulans = [];
					var bulan_name = [];
					for(var i = 0; i < result.result_member.length;i++){
						bulans.push(result.result_member[i].bulan);
						bulan_name.push(result.result_member[i].bulan2);
					}

					var bulans_unik = bulans.filter(onlyUnique);
					var bulan_name_unik = bulan_name.filter(onlyUnique);

					var seriesss = [];
					var sr = [];
					
					for(var i = 0; i < bulans_unik.length;i++){
						categori_pic.push(bulan_name_unik[i]);
						for(var j = 0; j < result.software_member.length;j++){
							var seriess = [];
							var qty = 0;
							for(var k = 0; k < result.result_member.length;k++){
								if (result.software_member[j].employee_id == result.result_member[k].pic_id && result.result_member[k].bulan == bulans_unik[i]) {
									if (parseInt(result.result_member[k].jumlah) != 0) {
										qty = parseInt(result.result_member[k].jumlah);
										pic = result.software_member[j].employee_id;
										bulan = result.result_member[k].bulan;

										seriess.push(qty);
										seriess.push(pic);
										seriess.push(bulan);
									}
								}
							}
							seriesss.push(seriess);
						}
					}

					new_sr = [];
					ctg_mon = [];

					$.each(result.software_member, function(key, value) {
						sr.push({'employee_id' : value.employee_id, 'name' : value.name, 'data' : [0]})
					})

					$.each(sr, function(key, value) {
						var tmp_sr = [];
						$.each(result.result_member, function(key2, value2) {
							if (value.employee_id == value2.pic_id) {
								tmp_sr.push(value2.jumlah);
							}

							if(ctg_mon.indexOf(value2.bulan2) === -1){
								ctg_mon[ctg_mon.length] = value2.bulan2;
							}
						})

						new_sr.push({'name' : value.name, 'data' : tmp_sr});
					})
					
					Highcharts.chart('chart', {
						chart: {
							type: 'line',
							backgroundColor: '#fff',
							options3d: {
								enabled: true,
								alpha: 15,
								beta: 0,
								depth: 0,
								viewDistance: 50
							}
						},
						title: {
							text: 'MONITORING TOTAL PEROLEHAN TIKET PER BULAN '+fy,
							style: {
								fontSize: '16px',
								fontWeight: 'bold',
								color:'#000'
							}
						},
						xAxis: {
							categories: categori,
							type: 'category',
							gridLineColor: '#000',
							labels: {
								style: {
									color: '#000'
								}
							},
							lineColor: '#000',
							minorGridLineColor: '#000',
							tickColor: '#000',
							title: {
								style: {
									color: '#000'

								}
							}
						},yAxis: [{
							title: {
								text: 'Total Ticket',
								style: {
									color: '#000',
									fontSize: '15px',
									fontWeight: 'bold',
									fill: '#000'
								}
							},
							labels:{
								style:{
									fontSize:"15px",
									color: '#000'
								}
							}
						},
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							itemStyle: {
								color: '#000',
								fontWeight:'bold'
							},
							itemHoverStyle: {
								color: '#000'
							},
							itemHiddenStyle: {
								color: '#000'
							}
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											DetailPerolehan(this.category);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							name:'Perolehan Ticket Finish',
							data: series_fin,
							color : 'green'
						}
						// ,
						// {
						// 	name:'Open',
						// 	data: series_op,
						// 	color : '#ff6666'
						// }
						]
					});

					Highcharts.chart('chart2', {
						chart: {
							type: 'column',
							backgroundColor: '#fff',
						},
						title: {
							text: 'MONITORING TICKET BY PIC '+fy,
							style: {
								fontSize: '16px',
								fontWeight: 'bold',
								color:'#000'
							}
						},
						xAxis: {
							categories: ctg_mon,
							crosshair: true
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Ticket'
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y} Ticket</b></td></tr>',
							footerFormat: '</table>',
							shared: true,
							useHTML: true
						},
						plotOptions: {
							column: {
								pointPadding: 0.2,
								borderWidth: 0
							}
						},
						series: new_sr,
						credits: {
							enabled: false
						}
					});

				}
			}
		});
}


function DetailPerolehan(category){
	$('#loading').show();

    var fy = $('#fiscal_year').val();

	var data = {
		category:category,
		fy:fy
	}

	$.get('{{ url("fetch/detail/tiket_perolehan") }}', data, function(result, status, xhr){
		if(result.status){
			$('#loading').hide();
			$('#DetailPerolehan').modal('show');
			$('#title-detail-perolehan').html('Detail Perolehan '+category+'');

			$('#detailModalPerolehan').DataTable().clear();
			$('#detailModalPerolehan').DataTable().destroy();
			$('#detailModalPerolehanBody').html("");
			var tableData = "";

			$.each(result.data, function(key, value) {
				tableData += '<tr>';
				tableData += '<td style="text-align: center">'+ value.ticket_id +'</td>';
				tableData += '<td style="text-align: center">'+ value.department_shortname +'</td>';
				tableData += '<td style="text-align: left">'+ value.case_title +'</td>';
				tableData += '<td style="text-align: center">'+ value.status +'</td>';
				tableData += '<td style="text-align: left">'+ (value.pic_name || '') +'</td>';
				tableData += '</tr>';
			});
			$('#detailModalPerolehanBody').append(tableData);

			var table = $('#detailModalPerolehan').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[{
						extend: 'pageLength',
						className: 'btn btn-default',
					}]
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 10,
				'DataListing': true	,
				'ordering': true,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});
		}
	});
}

function DetailMPic(pic,category){
	$('#loading').show();

	var data = {
		pic:pic,
		category:category
	}

	$.get('{{ url("fetch/detail/tiket_pic") }}', data, function(result, status, xhr){
		if(result.status){
			$('#loading').hide();
			$('#ModalDetailPIC').modal('show');
			$('#title-detail-pic').html('Detail '+category+' PIC '+pic);

			$('#detailModalPIC').DataTable().clear();
			$('#detailModalPIC').DataTable().destroy();
			$('#detailModalPICBody').html("");
			var tableData = "";

			$.each(result.data, function(key, value) {
				tableData += '<tr>';
				tableData += '<td style="text-align: center">'+ value.ticket_id +'</td>';
				tableData += '<td style="text-align: center">'+ value.department_shortname +'</td>';
				tableData += '<td style="text-align: left">'+ value.case_title +'</td>';
				tableData += '<td style="text-align: center">'+ value.status +'</td>';
				tableData += '<td style="text-align: left">'+ (value.pic_name || '') +'</td>';
				tableData += '</tr>';
			});
			$('#detailModalPICBody').append(tableData);

			var table = $('#detailModalPIC').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[{
						extend: 'pageLength',
						className: 'btn btn-default',
					}]
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 10,
				'DataListing': true	,
				'ordering': true,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});
		}
	});
}


Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
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
	}
};
Highcharts.setOptions(Highcharts.theme);
</script>
@endsection