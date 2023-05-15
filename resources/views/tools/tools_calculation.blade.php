@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
	  width: 100%;
	  padding: 3px;
	  box-sizing: border-box;
	}
	thead>tr>th{
	  text-align:center;
	  overflow:hidden;
	  padding: 3px;
	}
	tbody>tr>td{
	  text-align:center;
	}
	tfoot>tr>th{
	  text-align:center;
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
	  border:1px solid rgb(211,211,211);
	  padding-top: 0;
	  padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
	  border:1px solid rgb(211,211,211);
	}
	td{
	    overflow:hidden;
	    text-overflow: ellipsis;
	  }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Tools Kanban Calculation <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-8" style="padding-right: 5px;">
			<div class="box box-solid" style="height: 26vh">
				<div class="box-body">
					<center style="background-color: #605ca8;color: white"><h4 style="font-weight: bold;padding: 5px;margin-top: 0px">Filter</h4></center>
					<div class="row">
						<div class="col-md-4 col-md-offset-1">
							<span style="font-weight: bold;">Location</span>
							<div class="form-group">
								<select class="form-control select2" name="location" id="location" data-placeholder="Pilih Lokasi" style="width: 100%;">
									<option></option>
									<option value="Welding Process">Welding Process</option>
									<option value="Final Assy">Final Assy</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Destination</span>
							<div class="form-group">
								<select class="form-control select2" name="destination" id="destination" data-placeholder="Pilih Kategori" style="width: 100%;">
									<option></option>
									<option value="Local">Local</option>
									<option value="Import">Import</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<span style="font-weight: bold;color:white">x</span>
							<div class="col-md-12">
								<div class="form-group">
									<a href="{{ url('index/injection/report_cleaning') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillTable()">Search</button>
								</div>
							</div>
						</div>
						 @if(Auth::user()->role->role_code == 'MIS' || Auth::user()->role->role_code == 'PROD')
                        <div class="col-xs-2">
							<div class="form-group">
	                            <a data-toggle="modal" data-target="#calculateData" class="btn btn-warning btn-md" style="color:white;display:none"><span class="fa fa-refresh"></span>&nbsp;&nbsp;&nbsp;Calculate Data</a>
	                        </div>
                        </div>
                        @endif
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4" style="margin-bottom: 0px;padding-bottom: 0px;padding-left: 5px">
			<div class="box box-solid" style="height: 26vh;margin-bottom: 0px;padding-bottom: 0px">
				<div class="box-body">
					<center style="background-color: #605ca8;color: white"><h4 style="font-weight: bold;padding: 5px;margin-top: 0px">Resume</h4></center>
					<table class="table table-bordered" id="tableResume" style="height: 15vh">
						<thead>
							<tr>
								<th style="width: 50%;color: red">Need Order</th>
								<th style="color: red" id="need_order">0</th>
							</tr>
							<tr>
								<th style="width: 50%;color: blue">Ordered</th>
								<th style="color: blue" id="ordered">0</th>
							</tr>
							<tr>
								<th style="width: 50%;color: black">No Need Order</th>
								<th style="color: black" id="no_need">0</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-12">			
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body" >
							<table id="toolTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:20%;">Tools</th>
										<!-- <th style="width:5%;">Month</th> -->
										<th style="width:5%;">Lifetime</th>
										<!-- <th style="width:5%;">Qty Target</th> -->
										<!-- <th style="width:5%;">Tools/Month</th> -->
										<!-- <th style="width:5%;">Need/Day</th> -->
										<th style="width:5%;">MOQ (Kanban)</th>
										<th style="width:5%;">Destination</th>
										<th style="width:5%;">Lot Kanban</th>
										<th style="width:5%;">Stock Kanban</th>
										<th style="width:5%;">Minimum Stock</th>
										<th style="width:5%;">Status</th>
										<th style="width:5%;">No PR/Inv</th>
										<th style="width:5%;">Forecast <?= date('M') ?></th>
									</tr>
								</thead>
								<tbody id="tableBodyResult">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalPR" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-sm">
	    	<div class="modal-content">
	      		<div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        	<h4 class="modal-title" id="myModalLabel">Edit Nomor PR</h4>
	      		</div>
		      	<div class="modal-body">
			        <div class="box-body">
			          <input type="hidden" value="{{csrf_token()}}" name="_token" />
			          <div class="row">
				          <div class="col-xs-12">
				            <label for="po_sap">Nomor PR<span class="text-red">*</span></label>
				            <input type="text" class="form-control" name="no_pr" id="no_pr">
				           </div>
			          	</div>
			        </div>
		     	</div>
			    <div class="modal-footer">
			      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
			      <input type="hidden" id="id_edit">
			      <button type="button" onclick="edit_pr()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
			    </div>
		  	</div>
		</div>
	</div>

	<div class="modal fade" id="calculateData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <input type="hidden" value="{{csrf_token()}}" name="_token" />
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                <h4 class="modal-title" id="myModalLabel">Generate Tools Calculation Data</h4>
	                Generate Calculation akan mengahapus yang telah ada, dan mengganti dengan yang baru<br>
	            </div>
	            <div class="modal-body">
	                <div class="row">
	                    <div class="col-xs-6 col-xs-offset-3">
	                        <div class="col-xs-12">
	                            <label>Select Month</label>
	                            <div class="input-group date pull-right" style="text-align: center;">
	                                <div class="input-group-addon bg-green">
	                                    <i class="fa fa-calendar"></i>
	                                </div>
	                                <input type="text" class="form-control monthpicker" name="generate_month" id="generate_month" placeholder="Select Month">  
	                            </div>

	                        </div>
	                        <div class="col-xs-12" style="margin-top: 3%;">
	                            <label>Select Location</label>
	                            <select class="form-control select2" multiple="multiple" id='location' id='location' data-placeholder="Select Location" style="width: 100%;">
	                                @foreach($location as $loc)
	                                <option value="{{ $loc->location }}">{{ $loc->location }}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>    
	                </div>
	            </div>
	            <div class="modal-footer">
	                <div class="row" style="margin-top: 7%; margin-right: 2%;">
	                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	                    <button onclick="generate()" class="btn btn-primary">Generate </button>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

</section>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
		$('body').toggleClass("sidebar-collapse");
		fillTable();

		$('.monthpicker').datepicker({
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose: true,
            todayHighlight: true
        });
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function showDetail(tgl, nama) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$('#myModal').modal('show');
		$('#welding-log-body').append().empty();
				$('#welding-ng-log-body').append().empty();
				$('#welding-cek-body').append().empty();
				$('#ng_rate').append().empty();
				$('#posh_rate').append().empty();
				$('#judul').append().empty();


				$.get('{{ url("fetch/welding/op_ng_detail") }}', data, function(result, status, xhr) {
					if(result.status){

						$('#judul').append('<b>'+result.nik+' - '+result.nama+' on '+tgl+'</b>');

						//Welding log
						var total_good = 0;
						var body = '';
						for (var i = 0; i < result.good.length; i++) {
							body += '<tr>';
							body += '<td>'+result.good[i].welding_time+'</td>';
							body += '<td>'+result.good[i].model+'</td>';
							body += '<td>'+result.good[i].key+'</td>';
							body += '<td>'+result.good[i].op_kensa+'</td>';
							body += '<td>'+result.good[i].quantity+'</td>';
							body += '</tr>';

							total_good += parseInt(result.good[i].quantity);
						}
						body += '<tr>';
						body += '<td  colspan="4" style="text-align: center;">Total</td>';
						body += '<td>'+total_good+'</td>';
						body += '</tr>';
						$('#welding-log-body').append(body);


						//Welding NG log
						var total_ng = 0;
						var body = '';
						for (var i = 0; i < result.ng_ng.length; i++) {
							body += '<tr>';
							body += '<td>'+result.ng_ng[i].welding_time+'</td>';
							body += '<td>'+result.ng_ng[i].model+'</td>';
							body += '<td>'+result.ng_ng[i].key+'</td>';
							body += '<td>'+result.ng_ng[i].op_kensa+'</td>';
							body += '<td>'+result.ng_ng[i].ng_name+'</td>';
							body += '<td>'+result.ng_ng[i].quantity+'</td>';
							body += '</tr>';

							total_ng += parseInt(result.ng_ng[i].quantity);
						}
						body += '<tr>';
						body += '<td colspan="5" style="text-align: center;">Total</td>';
						body += '<td>'+total_ng+'</td>';
						body += '</tr>';
						$('#welding-ng-log-body').append(body);

						//Welding cek
						var total_cek = 0;
						var body = '';
						for (var i = 0; i < result.cek.length; i++) {
							body += '<tr>';
							body += '<td>'+result.cek[i].welding_time+'</td>';
							body += '<td>'+result.cek[i].model+'</td>';
							body += '<td>'+result.cek[i].key+'</td>';
							body += '<td>'+result.cek[i].op_kensa+'</td>';
							body += '<td>'+result.cek[i].quantity+'</td>';
							body += '</tr>';

							total_cek += parseInt(result.cek[i].quantity);
						}
						body += '<tr>';
						body += '<td colspan="4" style="text-align: center;">Total</td>';
						body += '<td>'+total_cek+'</td>';
						body += '</tr>';
						$('#welding-cek-body').append(body);


						//Resume
						var ng_rate = total_ng / total_cek * 100;
						var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Cek</sub> x 100%';
						text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_cek +'</sub> x 100%';
						text_ng_rate += '<br>= <b>'+ ng_rate.toFixed(2) +'%</b>';
						$('#ng_rate').append(text_ng_rate);


						//Chart NG
						var data = [];
						var ng_name = [];
						var qty = [];
						for (var i = 0; i < result.ng_qty.length; i++) {

							ng_name.push(result.ng_qty[i].ng_name);
							qty.push(result.ng_qty[i].qty);

							if(i == 0){
								data.push([ng_name[i], qty[i], true, false]);
							}else{
								data.push([ng_name[i], qty[i], false, false]);
							}

						}

						Highcharts.chart('modal_ng', {
							chart: {
								styledMode: true,
								backgroundColor: null,
								borderWidth: null,
								plotBackgroundColor: null,
								plotShadow: null,
								plotBorderWidth: null,
								plotBackgroundImage: null
							},
							title: {
								text: '',
								style: {
									display: 'none'
								}
							},
							exporting: {
								enabled: false 
							},
							tooltip: {
								enabled: false
							},
							plotOptions: {
								pie: {
									animation: false,
									dataLabels: {
										useHTML: true,
										enabled: true,
										format: '<span style="color:#121212"><b>{point.name}</b>:</span><br><span style="color:#121212">total = {point.y} PC(s)</span>',
										style:{
											textOutline: true,
										}
									}
								}
							},
							credits: {
								enabled:false
							},
							series: [{
								type: 'pie',
								allowPointSelect: true,
								keys: ['name', 'y', 'selected', 'sliced'],
								data: data,
							}]
						});

					}

				});
			}

			function getFormattedDate(date) {
			    var year = date.getFullYear();

			    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
			      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
			    ];

			    var month = date.getMonth();

			    var day = date.getDate().toString();
			    day = day.length > 1 ? day : '0' + day;
			    
			    // return day + ' ' + monthNames[month] + ' ' + year;
			    return monthNames[month] + '-' + year;

			  }

			function fillTable(){

				var data = {
					location : $('#location').val(),
					destination:$('#destination').val()
				}

			    $('#loading').show();

			    $.get('{{ url("fetch/calculation") }}', data, function(result, status, xhr){
			      $('#toolTable').DataTable().clear();
			      $('#toolTable').DataTable().destroy();
			      $('#tableBodyResult').html("");
			      var tableData = "";

				  var no_need = 0, 
				  need_order = 0, 
				  need_adjustment = 0;
				  ordered = 0;

			      $.each(result.resume, function(key, value) {
			       tableData += '<tr>';     
			       tableData += '<td style="text-align:left">'+ value.item_code+' - '+ value.description+'</td>';
			       // tableData += '<td>'+ getFormattedDate(new Date(value.due_date)) +'</td>';
			       tableData += '<td>'+ value.lifetime +' </td>';
			       // tableData += '<td>'+ value.qty_target +' pcs </td>';
			       // var lifetarget = parseInt(value.qty_target) / parseInt(value.lifetime);
			       // tableData += '<td>'+ lifetarget.toFixed(0) +' Tools / Month</td>';
			       // tableData += '<td>'+ (lifetarget/22).toLocaleString() +' Tools / Day</td>';
			       tableData += '<td>'+ value.moq +'</td>';
			       if (value.remark == "Lokal") {
			       		tableData += '<td>'+ value.remark +'</td>';
			       		//(Minimum 14 Days)
			       }
			       else if(value.remark == "Import"){
			       		tableData += '<td>'+ value.remark +'</td>';
			       		//(Minimum 3 Month)
			       }

			       var stock_kanban = parseInt(value.stock_kanban);
			       var need_kanban = parseInt(value.need_kanban);


			       	tableData += '<td>'+value.lot_kanban+'</td>';

			       if (stock_kanban > need_kanban) {
			      		tableData += '<td style="background-color:blue;color:white">'+value.stock_kanban+'</td>';
			       }else if(stock_kanban < need_kanban){
			       		tableData += '<td style="background-color:red;color:white">'+value.stock_kanban+'</td>';
			       }else{
			       		tableData += '<td>'+value.stock_kanban+'</td>';
			       }

			       tableData += '<td style="background-color:orange;">'+value.need_kanban+'</td>';
			       
			       if (stock_kanban > need_kanban) {
			      		// tableData += '<td style="background-color:blue;color:white">Need Adjustment</td>';
			      		// need_adjustment++;
			       }else if(stock_kanban < need_kanban){
			       		if (value.status == "waiting_order") {
				       		tableData += '<td style="background-color:red;color:white">Need Order</td>';
				       		need_order++;
			       		}
			       		else if(value.status == "pr_approval"){
			       			tableData += '<td style="background-color:blue;color:white">Ordered</td>';
				       		ordered++;
			       		}
			       }else{
			       		tableData += '<td>No Need Order</td>';
			       		no_need++;
			       }

			        if (value.status == "waiting_order") {
						tableData += '<td><a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-md" onClick="editNoPR('+value.id+')"><i class="fa fa-edit"></i></a></td>';
					}else if(value.status == "order"){
						tableData += '<td>'+value.no_pr+'</td>';
					}
					else{
						tableData += '<td></td>';
					}


                    var inserted = false;
                    for (var k = 0; k < result.forecast.length; k++) {
                        if(result.forecast[k].tools_item == value.item_code){
                            
                            tableData += '<td style="vertical-align: middle; text-align: center;">'+Math.ceil(result.forecast[k].total_need)+'</td>';

                            inserted = true;
                        }
                    }
                    if(!inserted){
                        tableData += '<td style="vertical-align: middle; text-align: center;">0</td>';
                    }

			       tableData += '</tr>';     
			     });


			      	$('#tableBodyResult').append(tableData);


					$('#no_need').html(no_need);
					$('#need_order').html(need_order);
					$('#need_adjustment').html(need_adjustment);
					$('#ordered').html(ordered);

			     
			      var table = $('#toolTable').DataTable({
			        'dom': 'Bfrtip',
			        'responsive':true,
			        'lengthMenu': [
			        [ 5, 10, 25, -1 ],
			        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
			          },
			          ]
			        },
			        'paging': true,
			        'lengthChange': true,
			        'pageLength': 15,
			        'searching': true,
			        'ordering': true,
			        'order': [],
			        'info': true,
			        'autoWidth': true,
			        "sPaginationType": "full_numbers",
			        "bJQueryUI": true,
			        "bAutoWidth": false,
			        "processing": true
			      });


			      // table.columns().every( function () {
			      //   var that = this;

			      //   $('input', this.footer() ).on( 'keyup change', function () {
			      //     if ( that.search() !== this.value ) {
			      //       that
			      //       .search( this.value )
			      //       .draw();
			      //     }
			      //   } );
			      // } );

			      $('#loading').hide();
			    })
			    
			}

	function fetchTable(){
		$('#toolTable').DataTable().destroy();
		
		var tools = $('#tools').val();
		var data = {
			tools:tools
		}

		var table = $('#toolTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
			'buttons': {
				// dom: {
				// 	button: {
				// 		tag:'button',
				// 		className:''
				// 	}
				// },
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url('fetch/calculation') }}",
				"data" : data
			},
			"columns": [
				{ "data": "item_code"},
				{ "data": "description"},
				{ "data": "lifetime"},
				{ "data": "qty_target"},
				{ "data": "due_date"},
				{ "data": "life_target"},
				{ "data": "daily_need"},
				{ "data": "moq"},
				{ "data": "remark"},
				{ "data": ""},
				{ "data": ""},
				{ "data": ""}
			]
		});

		table.columns().every( function () {
	        var that = this;

	        $( 'input', this.footer() ).on( 'keyup change', function () {
	          if ( that.search() !== this.value ) {
	            that
	            .search( this.value )
	            .draw();
	          }
	        } );
	      } );
		
	}

	function editNoPR(id,nomor){
    	$('#modalPR').modal("show");
    	$("#id_edit").val(id);
    }

    function edit_pr() {

      var data = {
        id: $("#id_edit").val(),
        no_pr : $("#no_pr").val()
      };

      $.post('{{ url("tools/edit_pr") }}', data, function(result, status, xhr){
        if (result.status == true) {
           openSuccessGritter("Success","Nomor PR Berhasil Di Edit");
           fillTable();
        } else {
          openErrorGritter("Error","Failed to edit.");
        }
      })
    }


	function openSuccessGritter(title, message){
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '3000'
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

