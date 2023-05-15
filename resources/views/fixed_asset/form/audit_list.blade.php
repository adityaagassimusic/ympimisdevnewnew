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
			<a data-toggle="modal" data-target="#mapModal" class="btn btn-primary" style="color:white"><i class="fa fa-upload"></i><i class="fa fa-map"></i>Upload Map</a>
		</li>
		<li>
			<a data-toggle="modal" data-target="#uploadModal" class="btn btn-danger" style="color:white"><i class="fa fa-upload"></i>Upload Check Vendor</a>
		</li>
		<li>
			<a data-toggle="modal" data-target="#exportModal" class="btn btn-danger" style="color:white"><i class="fa fa-download"></i>Download Data Vendor</a>
		</li>
		<?php if (str_contains(Auth::user()->role_code, 'MIS') || strtoupper(Auth::user()->username) == 'PI0905001') { ?>
			<li>
				<a data-toggle="modal" data-target="#generateModal" class="btn btn-success" style="color:white"><i class="fa fa-refresh"></i>Generate Schedule</a>
			</li>
		<?php } ?>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="box no-border">
				<div class="box-body">
					<div class="row" style="overflow-x: scroll;">
						<div class="col-xs-4">
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-4 control-label">Select Period</label>
								<div class="col-sm-8">
									<select class="form-control select2" id="period" data-placeholder="Select Period">
										<option value=""></option>
										@foreach($period as $per)
										<option value="{{$per->period}}">{{$per->period}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-2">
							<div class="form-group">
								<button class="btn btn-primary" onclick="getData()"><i class="fa fa-search"></i> Filter</button>
							</div>
						</div>

						<div class="col-xs-12">
							<br>
							<table id="AuditAssetTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Period</th>
										<th>FA Number</th>
										<th>Fixed Asset Name</th>
										<th>Section</th>
										<th>Location</th>
										<th>PIC</th>
										<th>Auditor</th>
										<th>Image</th>
										<th>Status</th>
										<th>Report</th>
										<th>Action</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
										<th style="text-align:center"></th>
									</tr>
								</tfoot>
								<tbody id="AuditAssetBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel"><center><b>Generate Schedule Audit Asset</b></center></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="col-xs-12">
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-2 control-label">Select Location :</label>

								<div class="col-sm-5">
									<select class="select2" style="width: 100%" data-placeholder="Select Location" id="loc_audit">
										<option value=""></option>
										<option value="All">All</option>
										<option value="YMPI">YMPI</option>
										<option value="Vendor">Vendor</option>
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="margin-top: 10px">
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-2 control-label">Audit Month :</label>

								<div class="col-sm-5">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control monthpicker" name="date" id="date" placeholder="Select Audit Month">
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="col-sm-7">
								<button class="btn btn-primary btn-sm pull-right" onclick="filter();" style="margin-top: 10px"><i class="fa fa-search"></i> filter</button>
							</div>
						</div>

						<div class="col-xs-12" style="margin-top: 2%;">
							<label>Select Auditor:<span class="text-red">*</span></label>
						</div>
						<div class="col-xs-12">
							<table class="table table-bordered" style="width: 100%">
								<thead style="background-color: #a488aa">
									<tr>
										<th>Section</th>
										<th>Location</th>
										<th>Qty Asset</th>
										<th>Auditor<span class="text-red">*</span></th>
										<th>Remote Audit</th>
									</tr>
								</thead>
								<tbody id="body_auditor">
								</tbody>
							</table>
						</div>
					</div>
				</div>    
			</div>
			<div class="modal-footer">
				<div class="row" style="margin-left: 2%; margin-right: 2%;">
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
					<button onclick="generate()" class="btn btn-success"><i class="fa fa-check"></i> Generate</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalImage">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b style="font-size: 2vw">Asset Image</b> </center>
				<div class="modal-body no-padding">
					<div class="col-xs-12">
						<form action="{{ url("update/fixed_asset/photo")}}" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to update image?');">
							<div class="form-group">
								<label for="new_asset_image">Update Asset Image</label>
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="file" id="new_asset_image" name="new_asset_image">
								<input type="hidden" id="sap_number" name="sap_number">
								<input type="hidden" id="period_img" name="period_img">

								<p class="help-block">Upload a New Image if Needed</p>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Update Image</button>
							</div>
						</form>
					</div>
					<div class="col-xs-12" id="images" style="padding-top: 20px">

					</div>
				</div>
				<div class="modal-footer">
					<div class="row">
						<button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 20px;font-weight: bold;">
							CLOSE
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="upload_map_form">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><center><b>Generate Schedule Audit Asset</b></center></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-12">
								<div class="form-group">
									<label for="inputEmail3" class="col-sm-2 control-label">Select Department :</label>

									<div class="col-sm-5">
										<select class="select4" style="width: 100%" data-placeholder="Select Department" id="dept_map">
											<option value=""></option>
											@foreach($dept as $dpt)
											<option value="{{$dpt->department}}">{{$dpt->department}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-sm-3">
										<button type="button" class="btn btn-primary btn-sm" onclick="filter_map()"><i class="fa fa-search"></i> filter</button>
									</div>
								</div>
							</div>

							<div class="col-xs-12">
								<table class="table table-bordered" style="width: 100%">
									<thead style="background-color: #a488aa">
										<tr>
											<th>Section</th>
											<th>Location</th>
											<th>Map</th>
											<th>Upload Map</th>
										</tr>
									</thead>
									<tbody id="body_map">
									</tbody>
								</table>
							</div>
						</div>
					</div>    
				</div>
				<div class="modal-footer">
					<div class="row" style="margin-left: 2%; margin-right: 2%;">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
						<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Update Map</button>
					</div>
				</div>
			</form>
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
												<!-- <option value=""></option>						 -->
												<option value="Check" selected >Check by Vendor</option>											
												<!-- <option value="Audit">Audit by YMPI</option>											 -->
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
										<i class="fa fa-spin fa-refresh" id="loading3" style="display: none"></i>
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
										<i class="fa fa-spin fa-refresh" id="loading2" style="display: none"></i>
									</div>
								</div>
							</div>
							<div class="col-xs-12">
								<div class="col-xs-12" style="font-weight: bold; background-color: yellow; margin-top: 5px">
									Sample Pengisian Cek Vendor : <a href='{{ url("files/fixed_asset/Sample Check Fixed Asset.xlsx")}}'>Sample_Check_Vendor.xlsx</a> <br>
									Sample Pengisian Audit oleh YMPI : <a href='{{ url("files/fixed_asset/Sample Audit YMPI.xlsx")}}'>Sample_Audit_YMPI.xlsx</a>
									
								</div>
								<div class="col-xs-12" style="font-weight: bold; background-color: #98fc86; margin-top: 30px; color: red">
									Form Berita Acara : <a href='{{ url("files/fixed_asset/BERITA ACARA AUDIT FIXED ASSET VENDOR.xlsb")}}' style="color: red; text-decoration: underline;">Form Berita Acara.xlsx</a> <br>
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

	(function($) {

		var Defaults = $.fn.select2.amd.require('select2/defaults');

		$.extend(Defaults.defaults, {
			dropdownPosition: 'auto'
		});

		var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');

		var _positionDropdown = AttachBody.prototype._positionDropdown;

		AttachBody.prototype._positionDropdown = function() {

			var $window = $(window);

			var isCurrentlyAbove = this.$dropdown.hasClass('select2-dropdown--above');
			var isCurrentlyBelow = this.$dropdown.hasClass('select2-dropdown--below');

			var newDirection = null;

			var offset = this.$container.offset();

			offset.bottom = offset.top + this.$container.outerHeight(false);

			var container = {
				height: this.$container.outerHeight(false)
			};

			container.top = offset.top;
			container.bottom = offset.top + container.height;

			var dropdown = {
				height: this.$dropdown.outerHeight(false)
			};

			var viewport = {
				top: $window.scrollTop(),
				bottom: $window.scrollTop() + $window.height()
			};

			var enoughRoomAbove = viewport.top < (offset.top - dropdown.height);
			var enoughRoomBelow = viewport.bottom > (offset.bottom + dropdown.height);

			var css = {
				left: offset.left,
				top: container.bottom
			};

    // Determine what the parent element is to use for calciulating the offset
    var $offsetParent = this.$dropdownParent;

    // For statically positoned elements, we need to get the element
    // that is determining the offset
    if ($offsetParent.css('position') === 'static') {
    	$offsetParent = $offsetParent.offsetParent();
    }

    var parentOffset = $offsetParent.offset();

    css.top -= parentOffset.top
    css.left -= parentOffset.left;

    var dropdownPositionOption = this.options.get('dropdownPosition');

    if (dropdownPositionOption === 'above' || dropdownPositionOption === 'below') {
    	newDirection = dropdownPositionOption;
    } else {

    	if (!isCurrentlyAbove && !isCurrentlyBelow) {
    		newDirection = 'below';
    	}

    	if (!enoughRoomBelow && enoughRoomAbove && !isCurrentlyAbove) {
    		newDirection = 'above';
    	} else if (!enoughRoomAbove && enoughRoomBelow && isCurrentlyAbove) {
    		newDirection = 'below';
    	}

    }

    if (newDirection == 'above' ||
    	(isCurrentlyAbove && newDirection !== 'below')) {
    	css.top = container.top - parentOffset.top - dropdown.height;
}

if (newDirection != null) {
	this.$dropdown
	.removeClass('select2-dropdown--below select2-dropdown--above')
	.addClass('select2-dropdown--' + newDirection);
	this.$container
	.removeClass('select2-container--below select2-container--above')
	.addClass('select2-container--' + newDirection);
}

this.$dropdownContainer.css(css);

};

})(window.jQuery);


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
		// dropdownParent: $("#generateModal")
		dropdownPosition: 'below'
	});

	$('.select3').select2({
		dropdownParent: $("#generateModal")
		// dropdownPosition: 'below'
	});

	$('.select4').select2({
		dropdownParent: $("#mapModal")
		// dropdownPosition: 'below'
	});

	getData();
});

function getData() {
	$("#loading").show();
	var data = {
		period : $("#period").val()
	}

	$.get('{{ url("fetch/fixed_asset/audit/list") }}', data, function(result, status, xhr) {
		$("#loading").hide();
		$('#AuditAssetTable').DataTable().clear();
		$('#AuditAssetTable').DataTable().destroy();
		$("#AuditAssetBody").empty();
		body = "";

		$.each(result.assets, function(index, value){
			body += "<tr>";
			body += "<td>"+value.period+"</td>";
			body += "<td>"+value.sap_number+"</td>";
			body += "<td>"+value.asset_name+"</td>";
			body += "<td>"+value.asset_section+"</td>";
			body += "<td>"+value.location+"</td>";
			body += "<td>"+value.name+"</td>";
			body += "<td>";
			body += value.checked_by.split('/')[1];
			if (value.audit_type == 'Remote') {
				body += "<br><label class='label label-info'><i class='fa fa-globe'></i> Remote Audit</label>";
			}
			body += "</td>";
			var url = "{{ url('files/fixed_asset/asset_picture') }}/"+value.asset_images;
			body += "<td><img src='"+url+"' style='max-width: 100px; max-height: 100px; cursor:pointer' onclick='modalImage(\""+url+"\", \""+value.sap_number+"\", \""+value.period+"\")' Alt='Image Not Found'></td>";
			body += "<td>";
			body += value.status;
			if (value.remark) {
				var nm = '1';

				if (~value.remark.indexOf("2")) {
					nm = '2';
				}

				body += "<label class='btn btn-warning btn-xs'><i class='fa fa-check'></i> Saved Temporary "+nm+"</label>";
			}
			body += "</td>";

			body += "<td>";
			// if(value.appr_manager_at){
				body += "<a class='btn btn-danger btn-xs' target='_blank' href='{{ url('report/fixed_asset/asset_check/pdf') }}/"+value.period+"/"+value.asset_section+"/"+value.location+"'><i class='fa fa-file-pdf-o'></i> Report</a>";
			// }
			body += "<a class='btn btn-primary btn-xs' target='_blank' href='{{ url('files/fixed_asset/map/') }}/"+value.asset_section+"_"+value.location+".pdf'><i class='fa fa-map'></i> Map</a>";
			body += "</td>";
			body += "<td>";

			if (value.status && value.status == 'Check 1') {
				if(jQuery.inArray(value.asset_section+'_'+value.location, result.img_not) != -1) {
					
				} else {
					body += "<a class='btn btn-warning btn-xs' href='{{ url('index/check/fixed_asset/check2') }}/"+value.asset_section+"/"+value.location+"/"+value.period+"'><i class='fa fa-pencil'></i> Cek 2</a>";
				}

				// }
			} else if (value.status && value.status == 'Check 2' && !value.appr_manager_at && value.category == 'YMPI') {
				body += "<button class='btn btn-primary btn-xs' onclick='send_mail(\""+value.asset_section+"\",\""+value.period+"\",\""+value.category+"\")'><i class='fa fa-send'></i> Send Approval</button>";
			} else if (value.status && value.status == 'Check 2' && !value.appr_manager_at && value.category == 'Vendor') {
				body += "<button class='btn btn-primary btn-xs' onclick='send_mail(\""+value.location+"\",\""+value.period+"\",\""+value.category+"\")'><i class='fa fa-send'></i> Send Approval</button>";
			} else if (value.status && value.status == 'Not Checked') {
				if(jQuery.inArray(value.asset_section+'_'+value.location, result.img_not) != -1) {
					
				} else {
					body += "<a class='btn btn-primary btn-xs' href='{{ url('index/check/fixed_asset/check1') }}/"+value.asset_section+"/"+value.location+"/"+value.period+"'><i class='fa fa-pencil'></i> Cek 1</a>";
					if (value.remark) {
						body += "<a class='btn btn-primary btn-xs' onclick='cekdua_check(\""+value.check_one_by+"\",\""+value.asset_section+"\",\""+value.location+"\",\""+value.period+"\")'><i class='fa fa-pencil'></i> Cek 2</a>";
					}
				}
			} else if(value.appr_manager_at){
				body += "<label class='btn btn-success btn-xs'><i class='fa fa-check'></i> Fully Approved</label>";
			}

			body += "<a class='btn btn-default btn-xs' href='{{ url('index/detail/fixed_asset/check') }}/"+value.asset_section+"/"+value.location+"/"+value.period+"'><i class='fa fa-check-square-o'></i> Detail Check</a>";


			body += "</td>";
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

		$('#AuditAssetTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
		} );

		table.columns().every( function () {
			var that = this;
			$( '#search', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );

		$('#AuditAssetTable tfoot tr').appendTo('#AuditAssetTable thead');
	})
}

function modalImage(url, sap_number, period) {
	$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
	$("#sap_number").val(sap_number);
	$("#period_img").val(period);
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
			body += '<td>'+value.jml_asset+'</td>';
			body += '<td style="width: 45%">';
			body += '<select class="form-control select3 auditor" style="width: 100%" data-placeholder="Select Auditor">';
			body += '<option value=""></option>';

			$.each(auditor_list, function(index2, value2){
				body += '<option value="'+value2.employee_id+'/'+value2.name+'">'+value2.employee_id+' - '+value2.name+'</option>'
			})
			body += '</select>';
			body += '</td>';

			if (value.remark == 'Vendor') {
				body += '<td><div class="checkbox" id="'+value.location+'"><label><input type="checkbox" class="checkbox_btn"> Remote</label></div></td>';
			} else {
				body += '<td><div class="checkbox" id="'+value.location+'"><label><input type="checkbox" class="checkbox_btn" disabled></label></div></td>';
			}

			body += '</tr>';
		} else if($("#loc_audit").val() != "All" && $("#loc_audit").val() == value.remark) {
			body += '<tr>';
			body += '<td class="section">'+value.section+'</td>';
			body += '<td class="location">'+value.location+'</td>';
			body += '<td>'+value.jml_asset+'</td>';
			body += '<td style="width: 45%">';
			body += '<select class="form-control select2 auditor" style="width: 100%" data-placeholder="Select Auditor">';
			body += '<option value=""></option>';

			$.each(auditor_list, function(index2, value2){
				body += '<option value="'+value2.employee_id+'/'+value2.name+'">'+value2.employee_id+' - '+value2.name+'</option>'
			})
			body += '</select>';
			body += '</td>';

			if (value.remark == 'Vendor') {
				body += '<td><div class="checkbox" id="'+value.location+'"><label><input type="checkbox" class="checkbox_btn"> Remote</label></div></td>';
			} else {
				body += '<td><div class="checkbox" id="'+value.location+'"><label><input type="checkbox" class="checkbox_btn" disabled></label></div></td>';
			}

			body += '</tr>';
		}
	})

	$("#body_auditor").append(body);

	$('.select3').select2({
		dropdownParent: $("#generateModal")
		// dropdownPosition: 'below'
	});

}

function generate() {
	$("#loading").show();

	if ($('#date').val() == '') {
		$("#loading").hide();
		audio_error.play();
		openErrorGritter('Error', 'Please Add Month Audit');
		return false;
	}

	var auditor = [];
	var stat = true;
	$('.auditor').each(function(index, value) {
		if($(this).val() == ''){
			stat = false;
		}
		auditor.push($(this).val());
	});

	if (stat == false) {
		$("#loading").hide();
		audio_error.play();
		openErrorGritter('Error', 'Please Select All Auditor Field');
		return false;
	}

	var section = [];
	$('.section').each(function(index, value) {
		section.push($(this).text());
	});

	var location = [];
	$('.location').each(function(index, value) {
		location.push($(this).text());
	});

	var remote = [];
	$('.checkbox_btn').each(function(index, value) {
		if($(this).is(":checked")) {
			remote.push('Remote');
		} else {
			remote.push('Normal');
		}
	});

	var data = {
		mon : $("#date").val(),
		section : section,
		location : location,
		auditor : auditor,
		remote : remote
	}

	$.post('{{ url("post/fixed_asset/audit/generate") }}', data, function(result, status, xhr) {
		if (result.status) {
			$("#loading").hide();
			openSuccessGritter('Success', 'Generate Schedule Successfully');
			$("#generateModal").modal('hide');
			$("#date").val("");
			$('.select2').val("").trigger("change");
		} else {
			$("#loading").hide();
			audio_error.play();
			openErrorGritter('Error', result.message);
		}
	})
}

function send_mail(section, period, category) {
	if(confirm('Send Fixed Asset Audit Approval in "'+section+'" ?')){
		var data = {
			section : section,
			period : period,
			category : category
		}
		$("#loading").show();

		$.post('{{ url("approval/fixed_asset/check") }}', data, function(result, status, xhr) {
			$("#loading").hide();
			if (result.status) {
				openSuccessGritter('Success', 'Approval berhasil terkirim');
			} else {
				openErrorGritter('Error', result.message);

			}
		})

	}
}

function filter_map(){
	var data = {
		dept : $("#dept_map").val()
	}

	$.get('{{ url("fetch/fixed_asset/section/location") }}', data, function(result, status, xhr) {
		if (result.status) {
			$("#body_map").empty();
			var body = '';

			$.each(result.asset_maps, function(index, value){
				body += '<tr>';
				body += '<td>'+value.asset_section+'</td>';
				body += '<td>'+value.location+'</td>';

				if (value.asset_map) {
					body += '<td><a class="btn btn-primary btn-xs" target="_blank" href="{{ url("files/fixed_asset/map") }}/'+value.asset_map+'"><i class="fa fa-map"></i> Map</a></td>';
				} else {
					body += '<td></td>';
				}
				body += '<td><input type="hidden" class="sec" value="'+value.asset_section+'"><input type="hidden" class="loc" value="'+value.location+'"><input type="file" id="map_file" name="map_file" class="map"></td>';

				body += '</tr>';
			});

			$("#body_map").append(body);
		}
	})
}


$("form#upload_map_form").submit(function(e) {
	$("#loading").show();

	e.preventDefault();

	var arr_sec = [];
	var arr_loc = [];
	var arr_map = [];

	$('.map').each(function() {
		arr_map.push($(this).prop('files')[0]);
	});

	$('.sec').each(function() {
		arr_sec.push($(this).val());
	});

	$('.loc').each(function() {
		arr_loc.push($(this).val());
	});	

	
	var formData = new FormData();
	formData.append('section', arr_sec);
	formData.append('location', arr_loc);

	$('.map').each(function(index, value) {
		formData.append('map_' + index, $(this).prop('files')[0]);
	});

	$.ajax({
		url: '{{ url("upload/fixed_asset/map") }}',
		type: 'POST',
		data: formData,
		success: function (result, status, xhr) {
			$("#loading").hide();

			openSuccessGritter('Success', 'Upload Map Successfully');
			location.reload(); 
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

		$("#loading2").show();
		$("#loading3").show();
		var data = {
			period : $(elem).val()
		}

		$.get('{{ url("fetch/fixed_asset/vendor_type") }}', data, function(result, status, xhr) {
			$("#loading2").hide();
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

$('#uploadModal').on('shown.bs.modal', function () {
	$("#period_up").val('').trigger('change');
	$("#loc_up").val(null);	
	$("#up_file").val(null);
})

$('#exportModal').on('shown.bs.modal', function () {
	$('#period_exp').val('').trigger('change');
	$("#download_button").removeAttr('disabled');
})

function cekdua_check(cek_one, section, location, period) {
	var str = "{{ Auth::user()->username }}";
	if (~cek_one.indexOf(str)) {
		openErrorGritter('Error', 'Anda sudah melakukan cek 1, Harap melakukan cek 2 dengan orang yang berbeda');
	} else {
		window.location.replace("{{ url('index/check/fixed_asset/check2') }}/"+section+"/"+location+"/"+period);
	}
}

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
	audio_error.play();
}

</script>
@endsection