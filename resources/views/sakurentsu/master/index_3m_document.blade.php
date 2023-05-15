@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
	}

	.datepicker {
		padding: 6px 12px 6px 12px;
	}

	.btn { margin: 2px; }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center style="position: absolute; top: 45%; left: 35%;">
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-refresh"></i> &nbsp; Please Wait ...</span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<table id="listTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 3%">EO Number</th>
								<th style="width: 1%">Material Number</th>
								<th style="width: 1%">Material Buyer Number</th>
								<th>Description</th>
								<th style="width: 5%">Uom</th>
								<th style="width: 5%">Storage Location</th>
								<th style="width: 5%">Price</th>
								<th style="width: 10%">Price Valid Date</th>
								<th style="width: 5%">Att</th>
								<th style="width: 5%">Reference</th>
								<th style="width: 5%">Status</th>
								<th style="width: 5%">Action</th>
							</tr>
						</thead>
						<tbody id="listTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="modal_upload">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="sk_num3"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="form-group">
						<label>3M Need : </label><br>
						<label class="radio-inline">
							<input type="radio" name="tiga_em_need" value="Need">Need 3M
						</label>
						<label class="radio-inline">
							<input type="radio" name="tiga_em_need" value="No Need">No Need 3M
						</label>
					</div>

					<button class="btn btn-success" style="width: 100%" id="upload-trial"><i class="fa fa-check"></i>&nbsp; Upload Trial File(s)</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalSales">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" style="background-color: #3c8dbc; padding-top: 5px; padding-bottom: 5px; color: white "><center><b>Upload Sales Price</b></center></h4>
				<div class="modal-body">
					<div class="col-xs-12">
						<center><h4 id="sales_id_form" style="font-weight: bold"></h4></center>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label class="col-sm-6 control-label">Material Description<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="sales_item_desc" placeholder="Material Desc" readonly>
							</div>
						</div>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label class="col-sm-6 control-label">GMC Material<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="sales_gmc" placeholder="GMC Material" readonly>
								<input type="hidden" id="sales_id">
								<input type="hidden" id="sales_material_id">
							</div>
						</div>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label class="col-sm-6 control-label">Sales Price Form<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<input type="file" name="sales_file" id="sales_file" accept="application/pdf">
							</div>
						</div>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label class="col-sm-6 control-label">Sales Price<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="sales_price" id="sales_price" placeholder="Sales Price">
							</div>
						</div>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label class="col-sm-6 control-label">Valid Date<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control datepicker" name="sales_valid_date" id="sales_valid_date" placeholder="Valid Date">
							</div>
						</div>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label class="col-sm-6 control-label">Status<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<select class="select3 form-control" name="sales_status" id="sales_status" data-placeholder="Form Status" style="width: 100%">
									<option value=""></option>
									<option value="Approval Price">Approval</option>
									<option value="Complete">Complete</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<div class="col-sm-12">
								<button class="btn btn-success pull-right" onclick="saveSales()"><i class="fa fa-check"></i> Save</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
<script src="{{ url("ckeditor/ckeditor.js") }}"></script>
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

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

		$('.select2').select2({
			dropdownAutoWidth : true,
			allowClear: true,
			dropdownParent: $('#modalBom'),
		});


		$('.select4').select2({
			dropdownAutoWidth : true,
			allowClear: true,
			dropdownParent: $('#modalTrial'),
		});

		$('.select3').select2({
			dropdownAutoWidth : true,
			allowClear: true,
			dropdownParent: $('#modalSales'),
		});

		CKEDITOR.replace('kondisi_sebelum' ,{
			filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
		});

		CKEDITOR.replace('trial' ,{
			filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
		});

		get_data();
	});

	function get_data() {
		$.get('{{ url("fetch/sakurentsu/list_material") }}', function(result, status, xhr){
			$('#listTable').DataTable().clear();
			$('#listTable').DataTable().destroy();
			$("#listTableBody").empty();
			body = "";

			$.each(result.datas, function(key, value) {
				body += "<tr>";
				body += "<td>"+(value.eo_number || '')+"</td>";

				var style = '';
				if (value.material_number == 'NEW') {
					style = 'style="background-color: #ff8c98"';
				}

				body += "<td "+style+">"+value.material_number+"</td>";
				body += "<td>"+value.material_number_buyer+"</td>";
				body += "<td>"+value.description+"</td>";
				body += "<td>"+(value.uom || '-')+"</td>";
				body += "<td>"+(value.storage_location || '-')+"</td>";

				var style = '';
				if (value.sales_price == null) {
					style = 'background-color: #ff8c98';
				} else if (value.status_price == 'Approval Price') {
					style = 'background-color: #ffde85';
				}

				body += "<td style='text-align: right; "+style+"'>"+(value.sales_price || '')+"</td>";
				body += "<td style='"+style+"'>"+(value.valid_date || '')+"</td>";

				if (value.attachment) {
					body += "<td><a href='#' class='btn btn-xs btn-primary'><i class='fa fa-file-archive-o'></i> Attachment</a></td>";
				} else {
					body += "<td> - </td>";
				}
				body += "<td>";
				if (value.reference_form_number) {
					body += "<span class='label label-danger'>Trial : "+value.reference_form_number+"</span>";
				}

				if (value.remark) {
					body += "<span class='label label-primary'>Approval : "+value.remark+"</span>";
				}
				body += "</td>";

				var status = '';
				if (value.status_price == null) {
					status = (value.status || '');
				} else {
					status = value.status_price;
				}

				body += "<td>"+status+"</td>";
				body += "<td>";

				if (value.material_number == 'NEW') {
					body += "<button class='btn btn-danger btn-xs' onclick='openTrialModal(\""+value.description+"\",\""+value.id+"\")' disabled>Trial Request</button><br>";
					body += "<button class='btn btn-primary btn-xs' onclick='openBOMModal(\""+value.description+"\",\""+value.eo_number+"\",\""+value.status+"\", "+value.id+")'>Upload BOM & Std Time</button><br>";
				}
				
				if ((value.status_price == null || value.status_price == 'Approval Price') && value.material_number != 'NEW') {
					if ('{{Auth::user()->role_code}}' == 'MIS' || '{{Auth::user()->username}}' == 'PI1106001' || '{{Auth::user()->username}}' == 'PI1111001') {
						body += "<button class='btn btn-success btn-xs' onclick='openSalesModal(\""+value.description+"\",\""+value.eo_number+"\",\""+value.material_number+"\", "+value.id+")'>Upload Price</button><br>";
					}
				}
				body += "</td>";
			})
			$("#listTableBody").append(body);

			var table = $('#listTable').DataTable({
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
				"order": [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});   
		})
	}

	function openBOMModal(item_desc, form_number, remark, id) {
		$("#modalBom").modal('show');
		$("#bom_id").val(form_number);
		$("#item_desc").val(item_desc);
		$("#material_id").val(id);

		$("#id_form").html('Extra Order Form Number : <i class="fa fa-book"></i> '+form_number);

		$.get('{{ url("adagio/cek/nomor_file/eo") }}', function(result, status, xhr){
			$('#no_approval').val(result.no_appr);
		});

		
	}

	function saveSales() {
		if (! $("#sales_file").val()) {
			openErrorGritter("Failed", "Please Select File");
			return false;
		}

		if ($("#sales_price").val() == '' || $("#sales_valid_date").val() == '' || $("#sales_status").val() == '') {
			openErrorGritter("Failed", "Please complete all fields");
			return false;
		}

		$('#loading').show();

		var myFormData = new FormData();
		myFormData.append('sales_file', $("#sales_file").prop('files')[0]);
		myFormData.append('gmc_material', $("#sales_gmc").val());
		myFormData.append('price', $("#sales_price").val());
		myFormData.append('valid_date', $("#sales_valid_date").val());
		myFormData.append('status', $("#sales_status").val());

		$.ajax({
			url: '{{ url("post/sakurentsu/trial_request/sales_price") }}',
			type: 'POST',
			processData: false,
			contentType: false,
			dataType : 'json',
			data: myFormData,
			success: function(jsonData){
				$('#loading').hide();
				openSuccessGritter('Success', 'Successfully Upload Sales Price');
				$("#modalSales").modal('hide');
				get_data();
			}
		});
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
			time: '3000'
		});
	}

</script>
@endsection

