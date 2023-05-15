@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
<style type="text/css">
	#tableDetail>tbody>tr:hover {
		background-color: #7dfa8c !important;
	}

	tbody>tr>td {
		padding: 10px 5px 10px 5px;
	}

	table.table-bordered {
		border: 1px solid black;
		vertical-align: middle;
	}

	table.table-bordered>thead>tr>th {
		border: 1px solid black;
		vertical-align: middle;
	}

	table.table-bordered>tbody>tr>td {
		border: 1px solid black;
		vertical-align: middle;
		height: 40px;
		padding: 2px 5px 2px 5px;
	}

	.contr #loading {
		display: none;
	}

	.label-status {
		color: black;
		font-size: 0.8vw;
		border-radius: 4px;
		padding: 3px 10px 5px 10px;
		border: 1.5px solid black;
		vertical-align: middle;
	}
</style>
@endsection

@section('content')
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

	<div id="loading"
	style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	<p style="position: absolute; color: White; top: 45%; left: 45%;">
		<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
	</p>
</div>
<input id="role_code" value="{{ Auth::user()->role_code }}" hidden>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-solid">
			<div class="box-header">
				<h3 class="box-title">Filter<span class="text-purple"></span></h3>
			</div>
			<div class="box-body">
				<div class="col-xs-6 col-xs-offset-3" style="padding: 0px;">
					<div class="box box-primary box-solid" style="margin: 0px;">
						<div class="box-body">
							<div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
								<div class="form-group">
									<label>Date From</label>
									<div class="input-group date" style="width: 100%;">
										<input type="text" placeholder="Select Date"
										class="form-control datepicker pull-right" id="check_in_from">
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
								<div class="form-group">
									<label>Date To</label>
									<div class="input-group date" style="width: 100%;">
										<input type="text" placeholder="Select Date"
										class="form-control datepicker pull-right" id="check_in_to">
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>

				<div class="col-xs-6 col-xs-offset-2" style="margin-top: 0.75%;">
					<div class="form-group pull-right" style="margin: 0px;">
						<button onclick="clearConfirmation()"
						class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
						<button onclick="showTable()" class="btn btn-primary"><span class="fa fa-search"></span>
						Search</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-12">
	<div class="form-group pull-right">
		<a href="{{ url('index/inventory_mis') }}" class="btn btn-primary" style="color:white">
			&nbsp;<i class="fa fa-sign-in"></i>&nbsp;&nbsp;&nbsp;MIS Inventory
		</a>
	</div>
</div>

<div class="col-xs-12">
	<table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%"
	style="font-size: 0.85vw;">
	<thead style="background-color: #605ca8; color: white;">
		<tr>
			<th style="text-align: center;">Date</th>
			<th style="text-align: center;">Kode ID</th>
			<th style="text-align: center;">PIC Pengambil</th>
			<th style="text-align: center;">PIC Penerima</th>
			<th style="text-align: center;">Count Item</th>
			<th style="text-align: center;">Aksi</th>
		</tr>
	</thead>
	<tbody id="tableDetailBody">
	</tbody>
	<tfoot style="background-color: RGB(252, 248, 227);">
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>

		</tr>
	</tfoot>
</table>
</div>

</section>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Data</h1>
				</div>
				<div class="col-xs-12" style="padding-top: 10px" >
					<input type="hidden" name="form_number_edit" id="form_number_edit">
					<table style="width: 100%" class="table">
						<thead>
							<tr>
								<th>No PO<span class="text-red">*</span></th>
								<th>Nama Item<span class="text-red">*</span></th>
								<th>No Seri<span class="text-red">*</span></th>
								<th>Qty<span class="text-red">*</span></th>
							</tr>
						</thead>
						<tbody id="body_asset_edit">
						</tbody>
					</table>

					<div class="form-group">
						<center><button class="btn btn-success" id="create_btn_edit" onclick="save_edit_form()"><i class="fa fa-check"></i> Save </button></center>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url('js/icheck.min.js') }}"></script>
<script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.select2').select2();

		showTable();
	});





	function openEditModal(form_id) {
		$("#editModal").modal('show');
		$("#body_asset_edit").empty();
		$("#form_number_edit").val("");

		var data = {
			form_id : form_id
		}
		$.get('{{ url("fetch/inventory/mis/edit") }}',data, function(result, status, xhr){
			if (result.status) {
				$("#form_number_edit").val(form_id);
				var body = '';
				$.each(result.datas, function(index2, value2){
					body += '<tr style="margin-bottom: 3px">';
					body += '<td><input type="hidden" class="ids" value="'+value2.id+'"><input type="text" class="form-control asset_no_edit" placeholder="Fixed Asset Number" value="'+value2.no_po+'" readonly></td>';
					body += '<td><input type="text" class="form-control asset_no_edit" placeholder="Fixed Asset Number" value="'+value2.nama_item+'" readonly></td>';
					body += '<td><input type="text" class="form-control asset_no_edit" placeholder="Fixed Asset Number" value="'+value2.no_seri+'" readonly></td>';
					body += '<td><input type="text" class="form-control asset_no_edit" placeholder="Fixed Asset Number" value="'+value2.qty+'" readonly></td>';
					body += '<td><button type="button" class="btn btn-danger" onclick="del(this)"><i class="fa fa-minus"></i></td>';
					body += '</tr>';
				});

				$("#body_asset_edit").append(body);

				$(".select2").select2();
			}
		})
	}


	function del(elem) {
		$(elem).closest('tr').remove();
	}


	function save_edit_form() {
		var arr_id = [];


		$('.ids').each(function(index, value) {
			arr_id.push($(this).val());
		});


		// if(jQuery.inArray("", arr_id) !== -1) {
		// 	openErrorGritter('Error', 'there is empty field');
		// 	return false;
		// }

		// $("#loading").show();

		var data = {
			form_number : $("#form_number_edit").val(),
			asset_id : arr_id,
		}

		$.post('{{ url("update/inventory/mis1") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#loading").hide();
				openSuccessGritter('Success', 'Success Update Data');
				$("#editModal").modal('hide');
				$("#body_asset_edit").empty();
				showTable();
			} else {
				openErrorGritter('Error', result.message);
				$("#loading").hide();
			}
		})
	}

	function showTable() {

		var check_in_from = $('#check_in_from').val();
		var check_in_to = $('#check_in_to').val();
		var category = $('#category').val();

		var data = {
			check_in_from: check_in_from,
			check_in_to: check_in_to,
			category: category,
		}


		$('#loading').show();
		$.get('{{ url("fetch/history/inventory/mis") }}', data, function(
			result, status, xhr) {
			if (result.status) {
				$('#loading').hide();

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				$('#tableDetail thead').html("");
				var head = '';
				head += '<tr>';
				head += '<th style="text-align: center;">Kode ID</th>';
				head += '<th style="text-align: center;">Tanggal Terima</th>';
				head += '<th style="text-align: center;">Total Item</th>';
				head += '<th style="text-align: center;">Pengambil</th>';
				head += '<th style="text-align: center;">Tanda Terima</th>';
				head += '<th style="text-align: center;">Action</th>';
				head += '</tr>';
				$('#tableDetail thead').append(head);

				$('#tableDetail tfoot').html("");
				var foot = '';
				foot += '<tr>'
				foot += '<th></th>';
				foot += '<th></th>';
				foot += '<th></th>';
				foot += '<th></th>';
				foot += '<th></th>';
				foot += '<th></th>';
				foot += '</tr>';
				$('#tableDetail tfoot').append(foot);

				$('#tableDetailBody').html("");
				var body = '';
				for (var i = 0; i < result.checklist.length; i++) {

					body += '<tr>';

					body += '<td style="width:3%; text-align:center;">' +
					result.checklist[i].checklist_id +
					'</td>';

					body += '<td style="width:3%; text-align:center;">' +
					result.checklist[i].receive_date +
					'</td>';

					body += '<td style="width:2%; text-align:center;">' +
					result.checklist[i].total_item +
					'</td>';

					body += '<td style="width:5%; text-align:center;">' +
					result.checklist[i].pic_pengambil_name+
					'</td>';

					body += '<td style="width:5%; text-align:center;">';
					body += '<a class="btn btn-info btn-md" ';
					body += 'onclick="showCheckOut(\'' + result.checklist[i].checklist_id + '\')" ';
					body += 'style="padding: 6px 12px 6px 12px;" >';
					body += '&nbsp;<i class="fa fa-paperclip"></i>&nbsp;&nbsp;&nbsp;Tanda Terima</a>';
					body += '</td>';

					if ('{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
						body += '<td style="width:3%; text-align:center;">';
						body += '<a class="btn btn-warning btn-md" ';
						body += 'onclick="openEditModal(\'' + result.checklist[i].checklist_id + '\')" ';
						body += 'style="padding: 6px 12px 6px 12px;" >';
						body += '&nbsp;<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Edit</a>';
						body += '<a class="btn btn-danger btn-md" style="margin-left:5px" ';
						body += 'onclick="deleteItem(\'' + result.checklist[i].checklist_id + '\')" ';
						body += 'style="padding: 6px 12px 6px 12px;">';
						body += '&nbsp;<i class="fa fa-close"></i>&nbsp;&nbsp;&nbsp;Delete</a>';
						body += '</td>';

					}else{
						body += '<td style="width:3%; text-align:center;">-';
						body += '</td>';
					}

					body += '</tr>';
				}
				$('#tableDetailBody').append(body);

				$('#tableDetail tfoot th').each(function() {
					var title = $(this).text();
					$(this).html(
						'<input style="text-align: center; width: 100%; color: grey;" type="text" placeholder="Search ' +
						title + '" size="3"/>');
				});

				var table = $('#tableDetail').DataTable({
					'dom': 'Bfrtip',
					'responsive': true,
					'lengthMenu': [
					[10, 25, 50, -1],
					['10 rows', '25 rows', '50 rows',
					'Show all'
					]
					],
					"pageLength": 25,
					'buttons': {
						buttons: [{
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
					"bAutoWidth": false
				});

				table.columns().every(function() {
					var that = this;
					$('input', this.footer()).on('keyup change',
						function() {
							if (that.search() !== this.value) {
								that
								.search(this.value)
								.draw();
							}
						});
				});
				$('#tableDetail tfoot tr').prependTo('#tableDetail thead');

				$('#loading').hide();
			}

		});

}

function deleteItem(id) {
        if (confirm('Apakah Anda yakin akan menghapus list item?')) {

            var data = {
                id:id
            }
            $.get('{{ url("delete/history/item/mis") }}', data, function(result, status, xhr){
                if(result.status){
                    $('#loading').hide();
                    audio_ok.play();
                    openSuccessGritter('Success','Success dihapus');
                    location.reload();

                } else {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!',result.message);
                }
            })
        }
    }

function showCheckOut(checklist) {
	window.open('{{ url("inventory/report") }}' + '/' + checklist, '_blank');
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}')

function openSuccessGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: "growl-success",
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '5000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '5000'
	});
}
</script>
@endsection
