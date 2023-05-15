@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#body_asset > tr > td {
		background-color: #fff;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		List of Asset 
	</h1>
	<ol class="breadcrumb">
		<li>
			<div class="input-group date">
				<div class="input-group-addon bg-purple" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<select class="form-control select2" id="period" data-placeholder="Select Period" onchange="getData()">
					<option value=""></option>
					@foreach($period as $per)
					<option value="{{$per->period}}">{{$per->period}}</option>
					@endforeach
				</select>
			</div>
			<!-- <a data-toggle="modal" data-target="#generateModal" class="btn btn-success" style="color:white"><i class="fa fa-refresh"></i>Generate Schedule</a> -->
		</li>
	</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="box no-border">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<!-- <button class="btn btn-primary btn-sm pull-right" id="download_foto" onclick="getFotoVendor()"><i class="fa fa-download"></i> Update Foto Vendor</button> -->
							<a data-toggle="modal" data-target="#uploadModal" class="btn btn-danger btn-sm pull-right" style="color:white; margin-right: 5px"><i class="fa fa-upload"></i> Upload Audit Vendor</a>
							<a data-toggle="modal" data-target="#exportModal" class="btn btn-danger btn-sm pull-right" style="color:white; margin-right: 5px"><i class="fa fa-download"></i> Download Data Audit</a>
							<br>
							<br>
							<table id="AuditAssetTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Period</th>
										<th>Section</th>
										<th>Location</th>
										<th>PIC</th>
										<th>Auditor</th>
										<th>Report</th>
										<th>Map</th>
										<th>Qty Asset</th>
										<th>Qty Audit</th>
										<th>Actual Audit</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="AuditAssetBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="upload_vendor_asset">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><center><b>Upload Asset Vendor</b></center></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">Upload File Audit : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<div class="form-group">
												<select class="select2" style="width: 100%" data-placeholder="Select Upload Type" id="type" disabled>
													<option value="Audit" selected>Audit by YMPI</option>
												</select>
											</div>
										</div>
									</div>
								</div>


								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">Select Periode : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<div class="form-group">

												<select class="select2" style="width: 100%" data-placeholder="Select Period" id="period_up" onchange="getVendor(this,'loc_up')">
													<option value=""></option>
													@foreach($period as $per)
													<option value="{{$per->period}}">{{$per->period}}</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">Select Vendor : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<div class="form-group">

												<select class="select2" style="width: 100%" data-placeholder="Select Vendor" id="loc_up" onchange="getType()">
													<option value=""></option>
												</select>
											</div>
										</div>
										<div class="col-sm-1">
											<i class="fa fa-spin fa-refresh" id="loading2" style="display: none"></i>
										</div>
									</div>
								</div>

								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">File Asset : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<input type="file" name="up_file" id="up_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
										</div>
									</div>
								</div>

								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">File Photo : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<input type="file" name="up_img" id="up_img" accept="image/*" multiple>
										</div>
									</div>
								</div>

								<div class="col-xs-12" id="bap_div" style="display: none">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">Berita Acara : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<input type="file" name="up_bap" id="up_bap" accept="application/pdf">
											<input type="hidden" name="audit_type" id="audit_type">
										</div>
									</div>
								</div>


							</div>
						</div>    
					</div>
					<div class="modal-footer">
						<div class="row" style="margin-left: 2%; margin-right: 2%;">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="export_vendor_asset" method="post" action="{{ url('download/fixed_asset/vendor')}}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><center><b>Download Asset Vendor</b></center></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">Select Periode : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<select class="select2" style="width: 100%" data-placeholder="Select Period" id="period_exp" name="period_exp" onchange="getVendor(this,'loc_exp')">
												<option value=""></option>
												@foreach($period as $per)
												<option value="{{$per->period}}">{{$per->period}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="form-group">
										<label for="inputEmail3" class="col-sm-4 control-label">Select Vendor : <span class="text-red">*</span></label>

										<div class="col-sm-6">
											<select class="select2" style="width: 100%" data-placeholder="Select Vendor" name="loc_exp" id="loc_exp">
												<option value=""></option>
											</select>
										</div>
										<div class="col-sm-1">
											<i class="fa fa-spin fa-refresh" id="loading3" style="display: none"></i>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="col-xs-12" style="font-weight: bold; background-color: yellow; margin-top: 5px">
										Sample Pengisian Cek Vendor: <a href='{{ url("files/fixed_asset/Sample Check Fixed Asset.xlsx")}}'>Sample_Check_Vendor.xlsx</a> <br>
										Sample Pengisian Audit oleh YMPI: <a href='{{ url("files/fixed_asset/Sample Audit Fixed Asset.xlsx")}}'>Sample_Audit_YMPI.xlsx</a>
									</div>

								</div>

							</div>
						</div>    
					</div>
					<div class="modal-footer">
						<div class="row" style="margin-left: 2%; margin-right: 2%;">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button type="submit" onclick="loading(this)" id="download_button" style="margin-top: 5px" class="btn btn-success btn-sm pull-right"><i class="fa fa-download"></i> Download</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jsQR.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var location_list = <?php echo json_encode($location); ?>;
	var auditor_list = <?php echo json_encode($auditor_list); ?>;
	var vendor_arr = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});

		$('.select2').select2({
			allowClear: true,

		});

		$("#period").val("").trigger('change');
	});

	function getData() {
		var data = {
			period : $("#period").val()
		}

		$.get('{{ url("fetch/fixed_asset/auditor_audit/list") }}', data, function(result, status, xhr) {
			$('#AuditAssetTable').DataTable().clear();
			$('#AuditAssetTable').DataTable().destroy();
			$("#AuditAssetBody").empty();
			body = "";

			$.each(result.assets, function(index, value){
				body += "<tr>";
				body += "<td>"+value.period+"</td>";
				body += "<td>"+value.asset_section+"</td>";
				body += "<td>";
				body += value.location;

				if (value.audit_type == 'Remote') {
					body += "<br><label class='label label-info'><i class='fa fa-globe'></i> Remote Audit</label>";
				}

				body += "</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.checked_by.split('/')[1]+"</td>";
				body += "<td><a class='btn btn-danger btn-xs' target='_blank' href='{{ url('report/fixed_asset/asset_audit/pdf') }}/"+value.period+"/"+value.asset_section+"/"+value.location+"'><i class='fa fa-file-pdf-o'></i> Report</a></td>";
				body += "<td><a class='btn btn-primary btn-xs' target='_blank' href='{{ url('files/fixed_asset/map/') }}/"+value.asset_section+"_"+value.location+".pdf'><i class='fa fa-map'></i> Map</a></td>";
				body += "<td>"+value.jml_asset+"</td>";

				audit = Math.ceil(parseInt(value.jml_asset) / 100 * 10);
				if (audit == 0) audit = 1;

				body += "<td>"+ audit +"</td>";
				body += "<td>"+value.audited+"</td>";
				body += "<td>";
				if (value.status == 'Check 2' && value.appr_manager_at && value.status_audit == 'Open') {
					body += "<a class='btn btn-xs btn-primary' target='_blank' href='{{ url('index/fixed_asset/audit') }}/"+value.asset_section+"/"+value.location+"/"+value.period+"'><i class='fa fa-check-square-o'></i> Audit</a>";
				} else if (value.status_audit == 'Close') {
					body += "<span class='btn btn-xs btn-success'><i class='fa fa-check'></i> Audited</span>";
				}
				body +="</td>";

				body += "</tr>";
			})

			$("#AuditAssetBody").append(body);

			var table = $('#AuditAssetTable').DataTable({
				'dom': 'Bfrtip',
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
					]
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});
		})
	}

	function modalImage(url, sap_number, period) {
		$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
		$("#sap_number").val(sap_number);
		$("#period").val(period);
		$('#modalImage').modal('show');
	}

	function filter() {
		if($("#loc_audit").val() == '') {
			openErrorGritter('Error', 'Please Select Location');
		}

		var body = "";
		$("#body_auditor").empty();

		$.each(location_list, function(index, value){
			if ($("#loc_audit").val() == "All") {
				body += '<tr>';
				body += '<td class="section">'+value.section+'</td>';
				body += '<td class="location">'+value.location+'</td>';
				body += '<td style="width: 45%">';
				body += '<select class="form-control select2 auditor" style="width: 100%" data-placeholder="Select Auditor">';
				body += '<option value=""></option>';

				$.each(auditor_list, function(index2, value2){
					body += '<option value="'+value2.employee_id+'/'+value2.name+'">'+value2.employee_id+' - '+value2.name+'</option>'
				})
				body += '</select>';
				body += '</td>';
				body += '</tr>';
			} else if($("#loc_audit").val() != "All" && $("#loc_audit").val() == value.remark) {
				body += '<tr>';
				body += '<td class="section">'+value.section+'</td>';
				body += '<td class="location">'+value.location+'</td>';
				body += '<td style="width: 45%">';
				body += '<select class="form-control select2 auditor" style="width: 100%" data-placeholder="Select Auditor">';
				body += '<option value=""></option>';

				$.each(auditor_list, function(index2, value2){
					body += '<option value="'+value2.employee_id+'/'+value2.name+'">'+value2.employee_id+' - '+value2.name+'</option>'
				})
				body += '</select>';
				body += '</td>';
				body += '</tr>';
			}
		})

		$("#body_auditor").append(body);

		$('.select2').select2({
		// dropdownParent: $("#generateModal")
		dropdownPosition: 'below'
	});

	}

	function getFotoVendor() {
		$("#loading").show();

		var data = {
			period : $("#period").val()
		}

		$.get('{{ url("get/fixed_asset/photo_vendor") }}', data, function(result, status, xhr) {
			if (result.status) {
				openSuccessGritter('Success', 'Data Successfully updated');
				$("#loading").hide();
			} else {
				$("#loading").hide();
				openErrorGritter('Error', result.message);
			}
		})
	}

	$('#uploadModal').on('shown.bs.modal', function () {
		$("#period_up").val('').trigger('change');
		$("#loc_up").val(null);	
		$("#up_file").val(null);
	})


	$("form#upload_vendor_asset").submit(function(e) {

		if ($("#type").val() == '') {
			openErrorGritter('Error!','Isi Semua Data');
			return false;
		}

		if ($("#audit_type").val() == 'Remote') {
			if ($('#up_bap').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
				openErrorGritter('Error!','Isi Semua Data');
				return false;
			}
		}

		if ($("#period_up").val() == '') {
			openErrorGritter('Error!','Isi Semua Data');
			return false;
		}

		if ($("#loc_up").val() == '') {
			openErrorGritter('Error!','Isi Semua Data');
			return false;
		}

		if ($('#up_file').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
			openErrorGritter('Error!','Isi Semua Data');
			return false;
		}

		$("#loading").show();

		e.preventDefault();

		var formData = new FormData();
		formData.append('period', $("#period_up").val());
		formData.append('type', $("#type").val());
		formData.append('location', $("#loc_up").val());
		formData.append('asset', $('#up_file').prop('files')[0]);
		formData.append('bap', $('#up_bap').prop('files')[0]);

		var photo = $("#up_img").get(0).files;

		for (var i = 0; i < photo.length; i++) {
			formData.append("photo_"+i, photo[i]);
		}

		formData.append('len', photo.length);

		$.ajax({
			url: '{{ url("upload/fixed_asset/vendor") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				if (result.status) {
					openSuccessGritter('Success', 'Upload Asset Successfully');
					setTimeout(function(){ location.reload(); }, 2000);
				} else {
					openErrorGritter('Error', result.message);
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
				audio_error.play();
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function getVendor(elem, ids) {
		$("#"+ids).empty();
		$("#"+ids).append("<option value=''></option>");

		if ($(elem).val() != '') {
			$("#loading3").show();
			var data = {
				period : $(elem).val()
			}

			$.get('{{ url("fetch/fixed_asset/vendor_type") }}', data, function(result, status, xhr) {
				$("#loading3").hide();
				$.each(result.assets, function(index, value){
					$("#"+ids).append("<option value='"+value.location+"'>"+value.location+"</option>");
					vendor_arr.push({'vendor' : value.location, 'type' : value.audit_type});
				})
			});
		}


	}


	function getType() {
		var val = $("#loc_up").val();
		$.each(vendor_arr, function(index, value){
			if (val == value.vendor ){
				if (value.type == 'Remote' && $("#type").val() == 'Check') {
					$("#bap_div").show();
					$('#audit_type').val('Remote');
				} else {
					$("#bap_div").hide();
					$('#audit_type').val('Normal');
				}
			}
		})
	}

	$('#exportModal').on('shown.bs.modal', function () {
		$('#period_exp').val('').trigger('change');
		$("#download_button").removeAttr('disabled');
	})

	function loading() {
		$("#download_button").attr('disabled', true);
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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