@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}

	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
		color: white;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #e57373;
		}
		50%, 100% {
			background-color: #ffccff;
		}
	}

	.nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
		background-color : red !important;
	}

	.tab-content > .tab-pane,
	.pill-content > .pill-pane {
	    width: 1250px;
	}

	.tab-content > .active,
	.pill-content > .active {
	    width: 1250px;
	}


</style>
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 22%; font-weight: bold">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i> Loading, Please Wait...</span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px;">
			<div class="row">
				<!-- <div class="col-xs-2" style="padding-right: 5px">
					<div class="input-group date">
						<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
							<i class="fa fa-calendar"></i>
						</div>
						<select class="form-control select2" name="period" id="period" data-placeholder="Pilih Periode" style="width: 100%;">
							<option value=""></option>
						</select>
					</div>
				</div>
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px">
					<button class="btn btn-success pull-left" onclick="fetchChart()" style="font-weight: bold;">
						<i class="fa fa-search"></i> Search
					</button>
				</div> -->
			</div>
		</div>


		<div class="col-xs-12">
			<div class="nav-tabs">
				<ul class="nav nav-tabs" style="font-weight: bold;">
					<li class="active"><a style="background-color: #0080FF; color: white" href="#tab_1" data-toggle="tab" class="nav-link">Upload Dokumen BC Barang Modal</a></li>
					<li><a style="background-color: #0080FF; color: white" href="#tab_2" data-toggle="tab" class="nav-link">Pengajuan Disposal Fixed Asset</a></li>
					<li><a style="background-color: #0080FF; color: white" href="#tab_3" data-toggle="tab" class="nav-link">Pengajuan Disposal Non Fixed Asset</a></li>
					<li><a style="background-color: #0080FF; color: white" href="#tab_4" data-toggle="tab" class="nav-link">Kontrol Pengeluaran Fixed Asset</a></li>
				</ul>
				<div class="tab-content" style="background-color: #353537">
					<div class="tab-pane active" id="tab_1">
			            <div class="col-xs-3" style="margin-top:20px">
			                <table id="resumeTable" class="table table-bordered table-striped table-hover"
			                    style="margin-bottom: 5%; height: 17vh;">
			                    <thead style="background-color: rgba(126,86,134,.7);">
			                        <tr>
			                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Status</th>
			                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Jumlah Kedatangan</th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                        <tr>
			                            <td style="width: 1%; font-weight: bold; font-size: 0.9vw;color: black;">All</td>
			                            <td id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw; padding-right: 4%;color: black;">
			                            </td>
			                        </tr> 
			                        <tr>
			                            <td
			                                style="width: 1%; background-color: #ccffff; font-weight: bold; font-size: 0.9vw;color: black;">
			                                BC Uploaded</td>
			                            <td id="count_document"
			                                style="width: 1%; text-align: right; font-weight: bold; background-color: #ccffff;  font-size: 1.2vw; padding-right: 4%;color: black;">
			                            </td>
			                        </tr>
			                        <tr>
			                            <td style="width: 1%; background-color: rgb(254, 204, 254); font-weight: bold; font-size: 0.9vw;color: black;">
			                                BC Not Uploaded</td>
			                            <td id="count_no_document"
			                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(254, 204, 254); font-size: 1.2vw; padding-right: 4%;color: black;">
			                            </td>
			                        </tr>
			                    </tbody>
			                </table>
			            </div>

			            <div class="col-xs-9" style="margin-top:20px">
			                <div id="chart1" style="width: 100%;"></div>
			            </div>

			            <div class="col-xs-12">
			            	<table id="tableModal" class="table table-bordered" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width:5%;">Surat Jalan / Invoice</th>
                                        <th style="width:3%;">Vendor</th>
                                        <th style="width:3%;">Tanggal Kedatangan</th>
                                        <th style="width:3%;">Status</th>
                                        <th style="width:3%;">Upload Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody id="tableModalBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
			            </div>
					</div>
					<div class="tab-pane" id="tab_2">
						

						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Fixed Asset Disposal</h3>
						<div class="col-xs-12" style="margin-top:20px;padding: 0;">
							<div id="main_graph" style="width: 100%"></div>
						</div>

						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_disposal">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>No</th>
									<th>Form Number</th>
									<th>Category</th>
									<th>Section</th>
									<th>User</th>
									<th>Acc Staff</th>
									<th>Manager User</th>
									<th>Manager Disposal</th>
									<th>DGM</th>
									<th>GM</th>
									<th>Manager Acc</th>
									<th>Director</th>
									<th>Presdir</th>
									<th>PIC Label</th>
									<th>Receive Label</th>
								</tr>
							</thead>
							<tbody id="body_disposal" style="color: white">
								<tr>
									<td></td>
								</tr>
							</tbody>
						</table>

					</div>

					<div class="tab-pane" id="tab_3">
						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Disposal Non Fixed Asset </h3>
						<div class="col-xs-12" style="margin-top:20px;padding: 0;">
							<div id="main_graph_non" ></div>
						</div>
						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_non_asset">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>No</th>
									<th>Item Description</th>
									<th>User</th>
									<th>Staff Logistic</th>
									<th>Manager User</th>
									<th>Manager Accounting</th>
									<th>Manager Logistic</th>
								</tr>
							</thead>
							<tbody id="body_non_asset" style="color: white">
								<tr>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="tab-pane" id="tab_4">
						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Kontrol Pengeluaran Fixed Asset</h3>

						<div class="col-xs-12" style="margin-top:20px;padding: 0;">
							<div id="main_graph_pengeluaran" ></div>
						</div>

						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_pengeluaran">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>Registrasi</th>
									<th>Kode Asset</th>
									<th>Deskripsi Barang</th>
									<th>Qty</th>
									<th>Diajukan Oleh</th>
									<th>Pengajuan</th>
									<th>Umur dan Asal FA</th>
									<th>PO Vendor</th>
									<th>Pengajuan Disposal ke BC</th>
									<th>Proses Scrap</th>
									<th>Pengajuan Dokumen BC</th>
									<th>Vendor Transfer</th>
									<th>FA Keluar YMPI</th>
									<th>Dokumen</th>
								</tr>
							</thead>
							<tbody id="body_pengeluaran" style="color: white">
								<tr>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>

				</div>

			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetailAll">
		<div class="modal-dialog modal-lg" style="width: 95%">
			<div class="modal-content">
				<div class="modal-header">
					<center><h4 style="padding-bottom: 15px;color: black;font-weight: bold;" class="modal-title" id="modalDetailTitleAll"></h4></center>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<table class="table table-hover table-bordered table-striped" id="tableDetailAll">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th style="width: 1%;text-align: center;">#</th>
									<th style="width: 3%;text-align: center;">SAP Number</th>
									<th style="width: 3%;text-align: center;">Asset Name</th>
									<th style="width: 4%;text-align: center;">Reference Photo</th>
									<th style="width: 4%;text-align: center;">Existence</th>
									<th style="width: 3%;text-align: center;">Exception Condition</th>
									<th style="width: 3%;text-align: center;">Note</th>
									<th style="width: 4%;text-align: center;">Audit Photo/Video</th>
									<th style="width: 3%;text-align: center;">Status</th>
									<th style="width: 3%;text-align: center;">Auditor</th>
								</tr>
							</thead>
							<tbody id="tableDetailBodyAll">
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
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var summary_list = [];
	var summary_appr = [];

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
        fetchSuratJalan();
	});	


	function fetchSuratJalan() {
            $("#loading").show();
            $.get('{{ url("fetch/kedatangan/dokumen_bc") }}', function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    $('#tableModal').DataTable().clear();
                    $('#tableModal').DataTable().destroy();

                    $('#tableModalBody').html("");

                    var tableModalBody = "";

                    var dokumen = 0;
                    var no_dokumen = 0;

                    $.each(result.surat_jalan, function(key, value) {
                        tableModalBody += '<tr>';
                        tableModalBody += '<td style="width: 10%;text-align: left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += value.surat_jalan;
                        tableModalBody += '</td>';

                        tableModalBody += '<td style="width: 7.5%;text-align: left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += value.supplier_code+ " - "+value.supplier_name;
                        tableModalBody += '</td>';

                        tableModalBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += getFormattedDate(new Date(value.date_receive));
                        tableModalBody += '</td>';

                        if (value.dokumen == null) {
                            tableModalBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;background-color: rgb(254, 204, 254);color:black">';
                            tableModalBody += 'Not Yet Upload';
                            tableModalBody += '</td>';
                            no_dokumen++;
                        }else{
                            tableModalBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;background-color: #ccffff;color:black">';
                            tableModalBody += 'Uploaded';
                            tableModalBody += '</td>';
                            dokumen++;
                        }


                        if (value.dokumen == null) {
                            tableModalBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                            tableModalBody += '<button style="height: 100%;" onclick="penanganan(\''+value.surat_jalan+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-file-pdf-o"></i> Upload File</button>';
                            tableModalBody += '</td>';
                        }else{
                            tableModalBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                            for (var i = 0; i < value.dokumen.split(',').length; i++) {
                                
                            tableModalBody += '<a href="{{url('files/dokumen_bc')}}/'+value.dokumen.split(",")[i]+'" target="_blank" class="fa fa-paperclip"></a>';
                            }

                            tableModalBody += '</td>';
                        }


                        tableModalBody += '</tr>';
                    });

                    $('#count_all').text((dokumen + no_dokumen));
                    $('#count_document').text(dokumen);
                    $('#count_no_document').text(no_dokumen);

                    $('#tableModalBody').append(tableModalBody);

                    $('#tableModal').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: [{
                                extend: 'pageLength',
                                className: 'btn btn-default',
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'ordering': false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    var xCategories = [];
                    var dokumen = [];
                    var no_dokumen = [];


                    for (var i = 0; i < result.calendars.length; i++) {
                        xCategories.push(result.calendars[i].month_text);
                        dokumen.push(0);
                        no_dokumen.push(0);
                    }

                    
                    for (var i = 0; i < result.calendars.length; i++) {
                        for (var j = 0; j < result.surat_jalan.length; j++) {
                            if (result.calendars[i].month == result.surat_jalan[j].date_receive.substr(0, 7)) {
                                var status = '';
                                if (result.surat_jalan[j].dokumen == null) {
                                    dokumen[i] += 1;
                                } else if (result.surat_jalan[j].dokumen != null) {
                                    no_dokumen[i] += 1;
                                } 
                            }
                        }
                    }


                    Highcharts.chart('chart1', {
                        chart: {
                            backgroundColor: null,
                            type: 'column',
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            title: {
                                text: 'Kontrol Dokumen BC Per Bulan',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: xCategories,
                            crosshair: true
                        },
                        yAxis: [{
                            title: {
                                text: 'Jumlah',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '0.8vw'
                                }
                            },
                        }],
                        exporting: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            borderWidth: 1
                        },
                        tooltip: {
                            enabled: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: 'black'
                            },
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.y != 0) ? this.y : "";
                                    },
                                    style: {
                                        textOutline: false
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchModal(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Dokumen',
                            data: dokumen,
                            color: '#feccfe'
                        }, {
                            name: 'Not Yet Upload',
                            data: no_dokumen,
                            color: '#ccffff'
                        }]
                    });


                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }



	function fetchChart(){
		$("#loading").show();

		var data = {
			period:$('#period').val(),
			category:$('#category').val(),
			location:$('#location').val(),
		}

		$.get('{{ url("fetch/barang_modal/monitoring") }}',data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();

				var tb_reg = "";
				$("#body_register").empty();
				var no = 1;

				$.each(result.registrations, function(index, value){
					tb_reg += "<tr>";
					tb_reg += "<td>"+no+"</td>";
					tb_reg += "<td>"+value.form_id+"</td>";

					if (value.asset_name) {
						tb_reg += "<td>"+value.asset_name+"</td>";
						tb_reg += "<td>"+value.pic+"</td>";
					} else {
						tb_reg += "<td></td>";
						tb_reg += "<td>"+value.department+"</td>";
					}

					if (value.asset_name) {
						nm = value.name;
						clr = '#00a65a';
						stts = '('+value.created_at.split(' ')[0]+')';
					} else {
						nm = value.nm;
						clr = '#dd4b39';
						stts = '(Waiting)';
					}


					let firstThree = nm.split(' ').slice(0, 2);
					let nama = firstThree.join(' ');


					tb_reg += "<td style='background-color: "+clr+"'><center>"+nama+"<br>"+stts+"</center></td>";

					if (value.asset_name) {
						if (value.update_fa_at) {
							clr = '#00a65a';
							stts = '('+value.update_fa_at.split(' ')[0]+')';
						} else {
							clr = '#dd4b39';
							stts = '(Waiting)';
						}

						tb_reg += "<td style='background-color: "+clr+"'><center>Ismail Husen<br>"+stts+"</center></td>";


						if (value.manager_app_date) {
							clr = '#00a65a';
							stts = '('+value.manager_app_date.split(' ')[0]+')';
						} else {
							clr = '#dd4b39';
							stts = '(Waiting)';
						}

						var name = value.manager_app.split('/')[1];

						tb_reg += "<td style='background-color: "+clr+"'><center>"+name+"<br>"+stts+"</center></td>";

						if (value.manager_acc_date) {
							clr = '#00a65a';
							stts = '('+value.manager_acc_date.split(' ')[0]+')';
						} else {
							clr = '#dd4b39';
							stts = '(Waiting)';
						}

						var name = value.manager_acc.split('/')[1];

						tb_reg += "<td style='background-color: "+clr+"'><center>"+name+"<br>"+stts+"</center></td>";

						clr = '#dd4b39';
						stts = '(Waiting)';

						$.each(result.labels, function(index2, value2){
							if (value2.remark == value.form_number) {
								if (value2.approval_label_acc_date) {
									clr = '#00a65a';
									stts = '('+value2.approval_label_acc_date.split(' ')[0]+')';
								} else {
									clr = '#dd4b39';
									stts = '(Waiting)';
								}

							}
						})

						tb_reg += "<td style='background-color: "+clr+"'><center>Afifatuz Yulaichah<br>"+stts+"</center></td>";

						clr = '#dd4b39';
						stts = '(Waiting)';
						name = nama;

						$.each(result.labels, function(index2, value2){
							if (value2.remark == value.form_number) {
								if (value2.receive_pic) {
									clr = '#00a65a';
									name = value2.receive_pic.split('/')[1].split(' ')[0];
									stts = '('+value2.receive_pic.split('/')[2]+')';
								} else {
									clr = '#dd4b39';
									stts = '(Waiting)';
								}

							}
						})

						tb_reg += "<td style='background-color: "+clr+"'><center>"+name+"<br>"+stts+"</center></td>";

					} else {
						tb_reg += "<td style='background-color: #dd4b39'><center>Ismail Husen<br>(Waiting)</center></td>";
						$.each(result.managers, function(index3, value3){
							if (value.department == value3.department) {
								tb_reg += "<td style='background-color: #dd4b39'><center>"+value3.approver_name+"<br>(Waiting)</center></td>";
							}
						})

						tb_reg += "<td style='background-color: #dd4b39'><center>Romy Agung Kurniawan<br>(Waiting)</center></td>";

						tb_reg += "<td style='background-color: #dd4b39'><center>Afifatuz Yulaichah<br>(Waiting)</center></td>";

						tb_reg += "<td style='background-color: #dd4b39'><center>Ismail Husen<br>(Waiting)</center></td>";
					}

					tb_reg += "</tr>";

					no++;
				})

				$("#body_register").append(tb_reg);


				var tb_dispo = "";
				$("#body_disposal").empty();
				var no = 1;

				$.each(result.disposals, function(index, value){
					tb_dispo += "<tr>";
					tb_dispo += "<td>"+no+"</td>";
					tb_dispo += "<td>"+value.form_number+"</td>";
					tb_dispo += "<td>"+value.mode+"</td>";
					tb_dispo += "<td>"+value.section_control+"</td>";

					let firstThree = value.name.split(' ').slice(0, 2);
					let nama = firstThree.join(' ');


					tb_dispo += "<td>"+nama+"</td>";

					if (value.fa_app_date) {
						clr = '#00a65a';
						stts = '('+value.fa_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>Ismail Husen<br>"+stts+"</center></td>";

					if (value.manager_app_date) {
						clr = '#00a65a';
						stts = '('+value.manager_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.manager_app.split('/')[1]+"<br>"+stts+"</center></td>";

					if (value.manager_disposal_app_date) {
						clr = '#00a65a';
						stts = '('+value.manager_disposal_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.manager_disposal_app.split('/')[1]+"<br>"+stts+"</center></td>";

					if (value.dgm_app_date) {
						clr = '#00a65a';
						stts = '('+value.dgm_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.dgm_app.split('/')[1]+"<br>"+stts+"</center></td>";					

					if (value.gm_app_date) {
						clr = '#00a65a';
						stts = '('+value.gm_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.gm_app.split('/')[1]+"<br>"+stts+"</center></td>";

					if (value.manager_acc_app_date) {
						clr = '#00a65a';
						stts = '('+value.manager_acc_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.manager_acc_app.split('/')[1]+"<br>"+stts+"</center></td>";

					if (value.director_fin_app_date) {
						clr = '#00a65a';
						stts = '('+value.director_fin_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.director_fin_app.split('/')[1]+"<br>"+stts+"</center></td>";

					if (value.presdir_app_date) {
						clr = '#00a65a';
						stts = '('+value.presdir_app_date.split(' ')[0]+')';
					} else {
						clr = '#dd4b39';
						stts = '(Waiting)';
					}

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+value.presdir_app.split('/')[1]+"<br>"+stts+"</center></td>";

					clr = '#dd4b39';
					stts = '(Waiting)';

					$.each(result.labels, function(index2, value2){
						if (value2.remark == value.form_number) {
							if (value2.approval_label_acc_date) {
								clr = '#00a65a';
								stts = '('+value2.approval_label_acc_date.split(' ')[0]+')';
							} else {
								clr = '#dd4b39';
								stts = '(Waiting)';
							}

						}
					})

					tb_dispo += "<td style='background-color: "+clr+"'><center>Afifatuz Yulaichah<br>"+stts+"</center></td>";

					clr = '#dd4b39';
					stts = '(Waiting)';
					name = nama;

					$.each(result.labels, function(index2, value2){
						if (value2.remark == value.form_number) {
							if (value2.receive_pic) {
								clr = '#00a65a';
								name = value2.receive_pic.split('/')[1];
								stts = '('+value2.receive_pic.split('/')[2].spit(' ')[0]+')';
							} else {
								clr = '#dd4b39';
								stts = '(Waiting)';
							}

						}
					})

					tb_dispo += "<td style='background-color: "+clr+"'><center>"+name+"<br>"+stts+"</center></td>";

					tb_dispo += "</tr>";

					no++;
				})

			$("#body_disposal").append(tb_dispo);


				var tb_non_asset = "";
				$("#body_non_asset").empty();
				var no = 1;
					tb_non_asset += "<tr>";
					tb_non_asset += "<td>1</td>";
					tb_non_asset += "<td>Drawing JIG For FL#1-13 FL-J-17075-1</td>";
					tb_non_asset += "<td>Donni Asri</td>";
					clr = '#dd4b39';
					stts = '(Waiting)';
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Angga Setiawan<br>"+stts+"</center></td>";
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Yudi Aptadipa<br>"+stts+"</center></td>";
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Romy Agung<br>"+stts+"</center></td>";
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Fathur Rozi<br>"+stts+"</center></td>";
					tb_non_asset += "</tr>";

					tb_non_asset += "<tr>";
					tb_non_asset += "<td>2</td>";
					tb_non_asset += "<td>Koma #8, 13 FL-J-17321 (2)</td>";
					tb_non_asset += "<td>Donni Asri</td>";
					clr = '#dd4b39';
					stts = '(Waiting)';
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Angga Setiawan<br>"+stts+"</center></td>";
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Yudi Aptadipa<br>"+stts+"</center></td>";
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Romy Agung<br>"+stts+"</center></td>";
					tb_non_asset += "<td style='background-color: "+clr+"'><center>Fathur Rozi<br>"+stts+"</center></td>";
					tb_non_asset += "</tr>";

				$("#body_non_asset").append(tb_non_asset);


				var tb_pengeluaran = "";
				$("#body_pengeluaran").empty();
				var no = 1;


				$.each(result.disposal_data, function(index, value){
					tb_pengeluaran += '<tr>';
					tb_pengeluaran += '<td>'+ getFormattedDate(new Date(value.registration_date)) +'</td>';
					tb_pengeluaran += '<td>'+ value.fixed_asset_id +'</td>';
					tb_pengeluaran += '<td style="text-align:left">'+ value.fixed_asset_name +'</td>';
					tb_pengeluaran += '<td>1</td>';
					tb_pengeluaran += '<td style="text-align:left">'+ value.pic_app.split('/')[1] +'</td>';
					if (value.status == "created") {
						tb_pengeluaran += '<td style="background-color:red;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(PIC Asset)</td>';
					} 
					else if (value.status == "pic") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Staff Accounting)</td>';
					}
					else if (value.status == "fa_control") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Manager)</td>';
					}
					else if (value.status == "manager") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Manager '+value.pic_incharge+')</td>';
					}
					else if (value.status == "manager_disposal") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(General Manager)</td>';
					}
					else if (value.status == "dgm") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Approval General Manager)</td>';
					}
					else if (value.status == "gm") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Manager Accounting)</td>';
					}
					else if (value.status == "acc_manager") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Director Finance)</td>';
					}
					else if (value.status == "director_fin") {
						tb_pengeluaran += '<td style="background-color:orange;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(President Director)</td>';
					}
					else if (value.status == "presdir") {
						tb_pengeluaran += '<td style="background-color:green;color:white;vertical-align:middle">Received By <br> <span style="font-weight:bold;font-size:10px">(Logistic)</td>';
					}
					else if (value.status == "new_pic") {
						tb_pengeluaran += '<td style="background-color:green;color:white;vertical-align:middle">Completed</td>';
					}
					else{
						tb_pengeluaran += '<td style="background-color:green;color:white;vertical-align:middle">Complete</td>';
					}

					if (value.status == "new_pic") {
						var param = [
						'umur_fa',
						'po_vendor',
						'disposal',
						'scrap',
						'dokumen_bc',
						'transfer',
						'keluar',
						'dokumen_final',
						];
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[0]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[1]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[2]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[3]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[4]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[5]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[6]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
						tb_pengeluaran += '<td onclick="upload_evidence(\''+param[7]+'\',\''+value.id+'\')" style="cursor:pointer"></td>';
					}else{
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
						tb_pengeluaran += '<td></td>';
					}
					tb_pengeluaran += '</tr>';
				});

				$("#body_pengeluaran").append(tb_pengeluaran);

var tb_scrap = "";
$("#body_scrap").empty();
var no = 1;

$.each(result.disposal_scraps, function(index, value){
	tb_scrap += "<tr>";
	tb_scrap += "<td>"+no+"</td>";
	tb_scrap += "</tr>";
})

$("#body_scrap").empty();

var open_reg = [];
var close_reg = [];
var ctg = [];

$.each(result.grafik, function(index, value){
	if(ctg.indexOf(value.mon) === -1){
		ctg[ctg.length] = value.mon;
	}
})

$.each(ctg, function(index, value){
	var open = 0;
	var close = 0;
	$.each(result.grafik, function(index2, value2){
		if (value2.form == 'registrasi' && value2.mon == value && value2.stat == 'open') {
			open +=1;
		} else if (value2.form == 'registrasi' && value2.mon == value && value2.stat == 'close') {
			var stt_label = false;
			$.each(result.labels, function(index3, value3){
				if (value3.remark == value2.form_number) {
					if (value3.receive_pic) {
						stt_label = true;
					} else {
						stt_label = false;
					}
				} else {
					stt_label = false;
				}
			})

			if (stt_label) {
				close += 1;
			} else {
				open += 1;
			}
		}
	})

	open_reg.push(open);
	close_reg.push(close);
})

				// ------------------------------- Chart  --------------------------
				Highcharts.chart('main_graph', {

					chart: {
						type: 'column'
					},

					title: {
						text: 'Approval Progress Fixed Asset'				
					},

					xAxis: {
						categories: ctg
					},

					yAxis: {
						allowDecimals: false,
						min: 0,
						title: {
							text: 'Count Form'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								fontSize: '20px',
								color: '#ddd'
							}
						}
					},

					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},

					legend: {
						labelFormatter: function() {
							return this.name + ' (' + this.userOptions.stack + ')';
						}
					},

					plotOptions: {
						column: {
							stacking: 'normal'
						}
					},

					series: [{
						name: 'Close',
						data: close_reg,
						color: '#90ee7e',
						stack: 'Registration'
					},{
						name: 'Open',
						data: open_reg,
						stack: 'Registration',
						color: '#fc7168'
					}
					]
				});

				Highcharts.chart('main_graph_non', {

					chart: {
						type: 'column'
					},

					title: {
						text: 'Approval Progress Non Fixed Asset'				
					},

					xAxis: {
						categories: ['Mar 2023']
					},

					yAxis: {
						allowDecimals: false,
						min: 0,
						title: {
							text: 'Count Form'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								fontSize: '20px',
								color: '#ddd'
							}
						}
					},

					tooltip: {
						formatter: function () {
							return '<b>tes</b>';
						}
					},

					legend: {
						labelFormatter: function() {
							return this.name + ' (' + this.userOptions.stack + ')';
						}
					},

					plotOptions: {
						column: {
							stacking: 'normal'
						}
					},

					series: [{
						name: 'Close',
						data: [1],
						color: '#90ee7e',
					},{
						name: 'Open',
						data: [2],
						color: '#fc7168'
					}
					]
				});
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
}

function openReportModal() {
	$("#modalSummary").modal('show');

	// tableSummary
	$("#tableDetailSummary").empty();
	body = "";

	tot_asset = 0;
	sum_ada = 0;
	sum_tidak_ada = 0;
	sum_rusak = 0;
	sum_tidak_digunakan = 0;
	sum_label = 0;
	sum_tidak_map = 0;
	sum_tidak_foto = 0;

	$.each(summary_list, function(key, value) {
		body += "<tr>";
		body += "<td style='text-align: right'>"+(key+1)+"</td>";
		body += "<td>"+value.asset_section+"</td>";
		body += "<td style='text-align: right'>"+value.total_asset+"</td>";
		body += "<td style='text-align: right'>"+value.ada_asset+"</td>";

		if (value.tidak_ada_asset == '0') {
			body += "<td style='text-align: right'> - </td>";
		} else {
			body += "<td style='text-align: right'>"+value.tidak_ada_asset+"</td>";
		}

		if (value.rusak_asset == '0') {
			body += "<td style='text-align: right'> - </td>";
		} else {
			body += "<td style='text-align: right'>"+value.rusak_asset+"</td>";
		}

		if (value.tidak_digunakan_asset == '0') {
			body += "<td style='text-align: right'> - </td>";
		} else {
			body += "<td style='text-align: right'>"+value.tidak_digunakan_asset+"</td>";
		}

		if (value.label_asset == '0') {
			body += "<td style='text-align: right'> - </td>";
		} else {
			body += "<td style='text-align: right'>"+value.label_asset+"</td>";
		}

		if (value.tidak_map_asset == '0') {
			body += "<td style='text-align: right'> - </td>";
		} else {
			body += "<td style='text-align: right'>"+value.tidak_map_asset+"</td>";
		}

		if (value.tidak_foto_asset == '0') {
			body += "<td style='text-align: right'> - </td>";
		} else {
			body += "<td style='text-align: right'>"+value.tidak_foto_asset+"</td>";
		}

		body += "</tr>";

		tot_asset += parseInt(value.total_asset);
		sum_ada += parseInt(value.ada_asset);
		sum_tidak_ada += parseInt(value.tidak_ada_asset);
		sum_rusak += parseInt(value.rusak_asset);
		sum_tidak_digunakan += parseInt(value.tidak_digunakan_asset);
		sum_label += parseInt(value.label_asset);
		sum_tidak_map += parseInt(value.tidak_map_asset);
		sum_tidak_foto += parseInt(value.tidak_foto_asset);

	});

	body += "<tr style='background-color: rgba(126,86,134,.3);'>";
	body += "<td></td>";
	body += "<td><b>Total</b></td>";
	body += "<td style='text-align: right'><b>"+tot_asset+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_ada+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_tidak_ada+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_rusak+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_tidak_digunakan+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_label+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_tidak_map+"</b></td>";
	body += "<td style='text-align: right'><b>"+sum_tidak_foto+"</b></td>";
	body += "</tr>";

	$("#tableDetailSummary").append(body);

	$("#tableSummaryAppr").empty();

	if (summary_appr) {
		body2 = "";
		// console.log(summary_appr);

		body2 += '<tr>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">PREPARED</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">CHECKED</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">APPROVED</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">APPROVED</th>';
		body2 += '</tr>';
		body2 += '<tr>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">STAFF</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">MANAGER</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">FINANCE DIRECTOR</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">PRESIDENT DIRECTOR</th>';
		body2 += '</tr>';
		body2 += '<tr>';
		if (summary_appr.prepare_date) {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">APPROVED <br>'+summary_appr.prepare_date+'</th>';
		} else {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;"><br><br></th>';
		}

		if (summary_appr.acc_manager_at) {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">APPROVED <br>'+summary_appr.acc_manager_at+'</th>';
		} else {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;"><br><br></th>';
		}

		if (summary_appr.finance_director_at) {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">APPROVED <br>'+summary_appr.finance_director_at+'</th>';
		} else {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;"><br><br></th>';
		}

		if (summary_appr.president_director_at) {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">APPROVED <br>'+summary_appr.president_director_at+'</th>';
		} else {
			body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;"><br><br></th>';
		}

		body2 += '</tr>';
		body2 += '<tr>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">'+summary_appr.prepared_by.split("/")[1]+'</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">'+summary_appr.acc_manager.split("/")[1]+'</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">'+summary_appr.finance_director.split("/")[1]+'</th>';
		body2 += '<th style="width: 10%; font-size: 1.2vw; text-align: center;">'+summary_appr.president_director.split("/")[1]+'</th>';
		body2 += '</tr>';

		$("#tableSummaryAppr").append(body2);
		$("#appr_send").hide();
	} else {
		$("#appr_send").show();
	}
}

function send_approval() {
	if (confirm('Are You Sure Want to Send Approval this Summary Report ?')) {

		$("#loading").show()
		var formData = new FormData();
		formData.append('period', $("#period").val());
		formData.append('location', 'YMPI');

		$.ajax({
			url: '{{ url("post/fixed_asset/summary") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();


				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);
			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	}
}

function getFormattedDate(date) {
  var year = date.getFullYear();

  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

  var month = date.getMonth();

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  
  return day + '-' + monthNames[month] + '-' + year;
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