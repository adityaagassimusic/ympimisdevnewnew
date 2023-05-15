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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
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

	.dataTables_filter {
		display: none;
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
				<div class="col-xs-2" style="padding-right: 5px">
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
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div id="main_graph"></div>
			<br>
		</div>

		<div class="col-xs-12">
			<div class="nav-tabs">
				<ul class="nav nav-tabs" style="font-weight: bold;">
					<li <?php if(Request::segment(4) !== null) { if(Request::segment(4) == 'registration') echo 'class="active"'; } else echo 'class="active"'; ?>><a style="background-color: #0080FF; color: white" href="#tab_1" data-toggle="tab" class="nav-link">Registrasi</a></li>
					<li <?php if(Request::segment(4) !== null) { if(Request::segment(4) == 'disposal') echo 'class="active"'; } ?>><a style="background-color: #0080FF; color: white" href="#tab_2" data-toggle="tab" class="nav-link">Disposal</a></li>
					<li <?php if(Request::segment(4) !== null) { if(Request::segment(4) == 'transfer') echo 'class="active"'; } ?>><a style="background-color: #0080FF; color: white" href="#tab_3" data-toggle="tab" class="nav-link">transfer Lokasi</a></li>
					<li><a style="background-color: #0080FF; color: white" href="#tab_4" data-toggle="tab" class="nav-link">Other</a></li>
				</ul>
				<div class="tab-content" style="background-color: #353537">
					<div class="tab-pane <?php if(Request::segment(4) !== null) { if(Request::segment(4) == 'registration') echo 'active'; } else echo 'active'; ?>" id="tab_1">
						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Fixed Asset Registration</h3>
						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_register">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>No</th>
									<th>Form Number</th>
									<th>FA Name</th>
									<th>Section</th>
									<th>User</th>
									<th>Acc Staff</th>
									<th>Manager User</th>
									<th>Manager Acc</th>
									<th>PIC Label</th>
									<th>Receive Label</th>
								</tr>
							</thead>
							<tbody id="body_register" style="color: white">
								<tr>
									<td></td>
								</tr>
							</tbody>
							<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tfoot>
						</table>
					</div>

					<div class="tab-pane <?php if(Request::segment(4) !== null) { if(Request::segment(4) == 'disposal') echo 'active'; } ?>" id="tab_2">
						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Fixed Asset Disposal</h3>
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
							<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tfoot>
						</table>

						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Fixed Asset Scrap Report</h3>
						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_scrap">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>No</th>
									<th>Form Number</th>
									<th>Section</th>
									<th>User</th>
									<th>Manager</th>
									<th>DGM</th>
									<th>GM</th>
									<th>Director</th>
									<th>Acc Staff</th>
									<th>Manager Acc</th>
								</tr>
							</thead>
							<tbody id="body_scrap" style="color: white">
								<tr>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="tab-pane <?php if(Request::segment(4) !== null) { if(Request::segment(4) == 'transfer') echo 'active'; } ?>" id="tab_3">
						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Fixed Asset Transfer</h3>
						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_transfer">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>No</th>
									<th>Form Number</th>
									<th>Section</th>
									<th>User</th>
									<th>Manager User</th>
									<th>User Transfer</th>
									<th>Manager Transfer</th>
									<th>Acc Staff</th>
									<th>Manager Acc</th>
									<th>PIC Label</th>
									<th>Receive Label</th>
								</tr>
							</thead>
							<tbody id="body_transfer" style="color: white">
								<tr>
									<td></td>
								</tr>
							</tbody>
							<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tfoot>
						</table>
					</div>
					<div class="tab-pane" id="tab_4">
						<h3 style="color: white; text-align: center; background-color: #7e5686; padding-top: 3px; padding-bottom: 3px">Outstanding Fixed Asset Label Request</h3>
						<table style="width: 100%" class="table table-bordered table-stripped table-responsive" id="table_label">
							<thead style="background-color: #df9ded;">
								<tr>
									<th>No</th>
									<th>Form Number</th>
									<th>Section</th>
									<th>User</th>
									<th>Acc Staff</th>
									<th>PIC Label</th>
									<th>Receive Label</th>
								</tr>
							</thead>
							<tbody id="body_label" style="color: white">
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
<!-- <script src="{{ url("js/dataTables.buttons.min.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.flash.min.js")}}"></script> -->
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<!-- <script src="{{ url("js/buttons.html5.min.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.print.min.js")}}"></script> -->
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
	});	


	function fetchChart(){
		$("#loading").show();

		var data = {
			period:$('#period').val(),
			category:$('#category').val(),
			location:$('#location').val(),
			status:"{{ Request::segment(4) }}"
		}

		$.get('{{ url("fetch/fixed_asset/monitoring_approval") }}',data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();

				// $('#table_register').DataTable().clear();
				// $('#table_register').DataTable().destroy();

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

					if ("{{ Auth::user()->name }}" == value.nm) {
						href = "href='{{ url('index/fixed_asset/registration_asset_form') }}'";
					} else {
						href = "href='javascript:void(0)'";
					}


					tb_reg += "<td style='cursor:pointer; background-color: "+clr+"'><center><a style='color:white' "+href+">"+nama+"<br>"+stts+"</a></center></td>";

					if (value.asset_name) {
						var sts_app = false;
						if (value.update_fa_at) {
							clr = '#00a65a';
							stts = '('+value.update_fa_at.split(' ')[0]+')';
						} else {
							clr = '#dd4b39';
							stts = '(Waiting)';

							if (value.created_by) {
								sts_app = true;
							}
						}

						if ("{{ Auth::user()->name }}" == 'Ismail Husen' && sts_app) {
							href = "href='{{ url('index/approval/fixed_asset') }}/"+value.id+"/FA CONTROL'";
						} else {
							href = "href='javascript:void(0)'";
						}

						tb_reg += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">Ismail Husen<br>"+stts+"</a></center></td>";


						var sts_app = false;
						if (value.manager_app_date) {
							clr = '#00a65a';
							stts = '('+value.manager_app_date.split(' ')[0]+')';
						} else {
							clr = '#dd4b39';
							stts = '(Waiting)';

							if (value.update_fa_at) {
								sts_app = true;
							}
						}

						var name = value.manager_app.split('/')[1];						

						if ("{{ Auth::user()->name }}" == name && sts_app) {
							href = "href='{{ url('index/approval/fixed_asset') }}/"+value.id+"/APPROVAL MANAGER'";
						} else {
							href = "href='javascript:void(0)'";
						}

						tb_reg += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+name+"<br>"+stts+"</a></center></td>";

						var sts_app = false;
						if (value.manager_acc_date) {
							clr = '#00a65a';
							stts = '('+value.manager_acc_date.split(' ')[0]+')';
						} else {
							clr = '#dd4b39';
							stts = '(Waiting)';

							if (value.manager_app_date) {
								sts_app = true;
							}
						}

						var name = value.manager_acc.split('/')[1];

						if ("{{ Auth::user()->name }}" == name && sts_app) {
							href = "href='{{ url('index/approval/fixed_asset') }}/"+value.id+"/APPROVAL MANAGER FA'";
						} else {
							href = "href='javascript:void(0)'";
						}

						tb_reg += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+name+"<br>"+stts+"</a></center></td>";

						clr = '#dd4b39';
						stts = '(Waiting)';

						var sts_app = false;
						$.each(result.labels, function(index2, value2){
							if (value2.remark == value.form_id) {
								if (value2.approval_label_acc_date) {
									clr = '#00a65a';
									stts = '('+value2.approval_label_acc_date.split(' ')[0]+')';
								} else {
									sts_app = true;
									clr = '#dd4b39';
									stts = '(Waiting)';
								}
							} else {
								sts_app = true;
							}
						})						

						if ("{{ Auth::user()->name }}" == 'Afifatuz Yulaichah' && sts_app && value.manager_acc_date) {
							href = "href='{{ url('index/approval/fixed_asset_label') }}/"+value.form_id+"'";
						} else {
							href = "href='javascript:void(0)'";
						}

						tb_reg += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">Afifatuz Yulaichah<br>"+stts+"</a></center></td>";


						clr = '#dd4b39';
						stts = '(Waiting)';
						name = nama;

						$.each(result.labels, function(index2, value2){
							if (value2.remark == value.form_id) {
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

$('#table_register tfoot th').each(function(){
	var title = $(this).text();
	$(this).html( '<input style="text-align: center; width: 100%" type="text" placeholder="Search '+title+'"/>' );
});

var table = $('#table_register').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'paging': false,
	'lengthChange': false,
	'searching': true,
	'ordering': true,
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true,
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

$('#table_register tfoot tr').appendTo('#table_register thead');


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

	sts_app = false;

	if (value.pic_app_date) {
		clr = '#00a65a';
		stts = '('+value.pic_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.name && sts_app) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+nama+"<br>"+stts+"</a></center></td>";

	sts_app = false;

	if (value.fa_app_date) {
		clr = '#00a65a';
		stts = '('+value.fa_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == 'Ismail Husen' && sts_app && value.pic_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">Ismail Husen<br>"+stts+"</a></center></td>";

	if (value.manager_app_date) {
		clr = '#00a65a';
		stts = '('+value.manager_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.manager_app.split('/')[1] && sts_app && value.fa_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.manager_app.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.manager_disposal_app_date) {
		clr = '#00a65a';
		stts = '('+value.manager_disposal_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.manager_disposal_app.split('/')[1] && sts_app && value.manager_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.manager_disposal_app.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.dgm_app_date) {
		clr = '#00a65a';
		stts = '('+value.dgm_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.dgm_app.split('/')[1] && sts_app && value.manager_disposal_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.dgm_app.split('/')[1]+"<br>"+stts+"</a></center></td>";					

	if (value.gm_app_date) {
		clr = '#00a65a';
		stts = '('+value.gm_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.gm_app.split('/')[1] && sts_app && value.dgm_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.gm_app.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.manager_acc_app_date) {
		clr = '#00a65a';
		stts = '('+value.manager_acc_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.manager_acc_app.split('/')[1] && sts_app && value.gm_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.manager_acc_app.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.director_fin_app_date) {
		clr = '#00a65a';
		stts = '('+value.director_fin_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.director_fin_app.split('/')[1] && sts_app && value.manager_acc_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.director_fin_app.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.presdir_app_date) {
		clr = '#00a65a';
		stts = '('+value.presdir_app_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.presdir_app.split('/')[1] && sts_app && value.director_fin_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.presdir_app.split('/')[1]+"<br>"+stts+"</a></center></td>";

	clr = '#dd4b39';
	stts = '(Waiting)';

	var sts_app = false;
	$.each(result.labels, function(index2, value2){
		if (value2.remark == value.form_number) {
			if (value2.approval_label_acc_date) {
				clr = '#00a65a';
				stts = '('+value2.approval_label_acc_date.split(' ')[0]+')';
			} else {
				sts_app = true;
				clr = '#dd4b39';
				stts = '(Waiting)';
			}
		} else {
			sts_app = true;
		}
	})	

	if ("{{ Auth::user()->name }}" == 'Afifatuz Yulaichah' && sts_app && value.presdir_app_date) {
		href = "href='{{ url('index/approval/fixed_asset_label') }}/"+value.form_number+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_dispo += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">Afifatuz Yulaichah<br>"+stts+"</a></center></td>";

	clr = '#dd4b39';
	stts = '(Waiting)';
	name = nama;

	$.each(result.labels, function(index2, value2){
		if (value2.remark == value.form_number) {
			if (value2.receive_pic) {
				clr = '#00a65a';
				name = value2.receive_pic.split('/')[1];
				stts = '('+value2.receive_pic.split('/')[2].split(' ')[0]+')';
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

$('#table_disposal tfoot th').each(function(){
	var title = $(this).text();
	$(this).html( '<input style="text-align: center; width: 100%" type="text" placeholder="Search '+title+'"/>' );
});

var table = $('#table_disposal').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'paging': false,
	'lengthChange': false,
	'searching': true,
	'ordering': true,
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true,
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

$('#table_disposal tfoot tr').appendTo('#table_disposal thead');

var tb_scrap = "";
$("#body_scrap").empty();
var no = 1;

$.each(result.disposal_scraps, function(index, value){
	tb_scrap += "<tr>";
	tb_scrap += "<td>"+no+"</td>";
	tb_scrap += "</tr>";
})

$("#body_scrap").empty();


var tb_trans = "";
$("#body_transfer").empty();
var no = 1;
$.each(result.transfer, function(index, value){
	tb_trans += "<tr>";
	tb_trans += "<td>"+no+"</td>";
	tb_trans += "<td>"+value.form_number+"</td>";
	tb_trans += "<td>"+value.old_section+"</td>";

	sts_app = false;

	if (value.approval_pic_date) {
		clr = '#00a65a';
		stts = '('+value.approval_pic.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.name && sts_app) {
		href = "href='{{ url('index/approval/fixed_asset_disposal') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.name+"<br>"+stts+"</a></center></td>";

	if (value.approval_manager_date) {
		clr = '#00a65a';
		stts = '('+value.approval_manager_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.approval_manager.split('/')[1] && sts_app && value.approval_pic_date) {
		href = "href='{{ url('index/approval/fixed_asset_transfer') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.approval_manager.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.approval_new_pic_date) {
		clr = '#00a65a';
		stts = '('+value.approval_new_pic_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.approval_new_pic.split('/')[1] && sts_app && value.approval_manager_date) {
		href = "href='{{ url('index/approval/fixed_asset_transfer') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.approval_new_pic.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.approval_new_manager_date) {
		clr = '#00a65a';
		stts = '('+value.approval_new_manager_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.approval_new_manager.split('/')[1] && sts_app && value.approval_new_pic_date) {
		href = "href='{{ url('index/approval/fixed_asset_transfer') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.approval_new_manager.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.receive_acc_date) {
		clr = '#00a65a';
		stts = '('+value.receive_acc_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.receive_acc.split('/')[1] && sts_app && value.approval_new_manager_date) {
		href = "href='{{ url('index/approval/fixed_asset_transfer') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.receive_acc.split('/')[1]+"<br>"+stts+"</a></center></td>";

	if (value.approval_acc_manager_date) {
		clr = '#00a65a';
		stts = '('+value.approval_acc_manager_date.split(' ')[0]+')';
		sts_app = false;
	} else {
		clr = '#dd4b39';
		stts = '(Waiting)';
		sts_app = true;
	}

	if ("{{ Auth::user()->name }}" == value.approval_acc_manager.split('/')[1] && sts_app && value.receive_acc_date) {
		href = "href='{{ url('index/approval/fixed_asset_transfer') }}/"+value.id+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">"+value.approval_acc_manager.split('/')[1]+"<br>"+stts+"</a></center></td>";

	var sts_app = false;
	$.each(result.labels, function(index2, value2){
		if (value2.remark == value.form_number) {
			if (value2.approval_label_acc_date) {
				clr = '#00a65a';
				stts = '('+value2.approval_label_acc_date.split(' ')[0]+')';
			} else {
				sts_app = true;
				clr = '#dd4b39';
				stts = '(Waiting)';
			}
		} else {
			sts_app = true;
		}
	})	

	if ("{{ Auth::user()->name }}" == 'Afifatuz Yulaichah' && sts_app && value.approval_acc_manager_date) {
		href = "href='{{ url('index/approval/fixed_asset_label') }}/"+value.form_number+"'";
	} else {
		href = "href='javascript:void(0)'";
	}

	tb_trans += "<td style='background-color: "+clr+"'><center><a style='color:white' "+href+">Afifatuz Yulaichah<br>"+stts+"</a></center></td>";

	clr = '#dd4b39';
	stts = '(Waiting)';
	name = value.name;

	$.each(result.labels, function(index2, value2){
		if (value2.remark == value.form_number) {
			if (value2.receive_pic) {
				clr = '#00a65a';
				name = value2.receive_pic.split('/')[1];
				stts = '('+value2.receive_pic.split('/')[2].split(' ')[0]+')';
			} else {
				clr = '#dd4b39';
				stts = '(Waiting)';
			}

		}
	})

	tb_trans += "<td style='background-color: "+clr+"'><center>"+name+"<br>"+stts+"</center></td>";
	tb_trans += "</tr>";
	no++;
})

$("#body_transfer").append(tb_trans);

$('#table_transfer tfoot th').each(function(){
	var title = $(this).text();
	$(this).html( '<input style="text-align: center; width: 100%" type="text" placeholder="Search '+title+'"/>' );
});

var table = $('#table_transfer').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'paging': false,
	'lengthChange': false,
	'searching': true,
	'ordering': true,
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true,
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

$('#table_transfer tfoot tr').appendTo('#table_transfer thead');

var open_reg = [];
var open_dis = [];
var open_trans = [];
var close_reg = [];
var close_dis = [];
var close_trans = [];

var ctg = [];

$.each(result.grafik, function(index, value){
	if(ctg.indexOf(value.mon) === -1){
		ctg[ctg.length] = value.mon;
	}
})

$.each(ctg, function(index, value){
	var open = 0;
	var open_dispo = 0;
	var open_tran = 0;
	var close = 0;
	var close_dispo = 0;
	var close_tran = 0;

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
				}

				else if (!stt_label){
					stt_label = false;
				}
			})

			if (stt_label) {
				close += 1;
			} else {
				open += 1;
			}
		} else if (value2.form == 'disposal' && value2.mon == value && value2.stat == 'open') {
			open_dispo +=1;
		} else if (value2.form == 'disposal' && value2.mon == value && value2.stat == 'close') {
			var stt_label = false;
			$.each(result.labels, function(index3, value3){
				if (value3.remark == value2.form_number) {
					if (value3.receive_pic) {
						stt_label = true;
					} else {
						stt_label = false;
					}
				}

				else if (!stt_label){
					stt_label = false;
				}
			})

			if (stt_label) {
				close_dispo += 1;
			} else {
				open_dispo += 1;
			}
		} else if (value2.form == 'transfer' && value2.mon == value && value2.stat == 'open') {
			open_tran +=1;
		} else if (value2.form == 'transfer' && value2.mon == value && value2.stat == 'close') {
			var stt_label = false;
			$.each(result.labels, function(index3, value3){
				if (value3.remark == value2.form_number) {
					if (value3.receive_pic) {
						stt_label = true;
					} else {
						stt_label = false;
					}
				}

				else if (!stt_label){
					stt_label = false;
				}
			})

			if (stt_label) {
				close_tran += 1;
			} else {
				open_tran += 1;
			}
		}
	})

	open_reg.push(open);
	open_dis.push(open_dispo);
	open_trans.push(open_tran);
	close_reg.push(close);
	close_dis.push(close_dispo);
	close_trans.push(close_tran);
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
			stacking: 'normal',
			dataLabels: {
				enabled: true
			}
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
	},{
		name: 'Close',
		data: close_dis,
		color: '#90ee7e',
		stack: 'Disposal'
	},{
		name: 'Open',
		data: open_dis,
		stack: 'Disposal',
		color: '#fced62'
	},{
		name: 'Close',
		data: close_trans,
		color: '#90ee7e',
		stack: 'Transfer'
	},{
		name: 'Open',
		data: open_trans,
		stack: 'Transfer',
		color: '#4287f5'
	}
					// ,{
					// 	name: 'United States',
					// 	data: [113, 122, 95],
					// 	stack: 'Disposal'
					// },{
					// 	name: 'Canada',
					// 	data: [77, 72, 80],
					// 	stack: 'Disposal'
					// },{
					// 	name: 'Indonesia',
					// 	data: [77, 72, 80],
					// 	stack: 'Label'
					// },{
					// 	name: 'England',
					// 	data: [77, 72, 80],
					// 	stack: 'Label'
					// },{
					// 	name: 'Germany',
					// 	data: [102, 98, 65],
					// 	stack: 'Transfer'
					// },{
					// 	name: 'United States',
					// 	data: [113, 122, 95],
					// 	stack: 'Transfer'
					// },{
					// 	name: 'Germany',
					// 	data: [102, 98, 65],
					// 	stack: 'Scrap Report'
					// }, {
					// 	name: 'United States',
					// 	data: [113, 122, 95],
					// 	stack: 'Scrap Report'
					// }
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