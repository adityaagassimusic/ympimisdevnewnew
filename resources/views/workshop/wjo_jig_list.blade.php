@extends('layouts.display')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
		color: #ffd700;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		background-color: #fffcb7;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> </span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button data-toggle="modal" data-target="#modalAdd" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i>&nbsp; Add Jig Data</button>
		</li>
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
			<table id="masterTable" class="table table-bordered">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">No</th>
						<th style="width: 2%;">Jig Code</th>
						<th style="width: 3%;">Process</th>
						<th style="width: 5%;">Jig Name</th>
						<th style="width: 1%;">Qty Max</th>
						<th style="width: 1%;">Qty Bal</th>
						<th style="width: 1%;">Qty Order</th>
						<th style="width: 1%;">Qty Finish</th>
						<th style="width: 1%;">Action</th>
					</tr>
				</thead>
				<tbody id="masterBody">
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal fade" id="OrderModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #00a65a">
					<center><h2 style="margin: 0px; color: white"><b>Workshop Job Order</b></h2></center>
				</div>
				<div class="modal-body">
					<input type="hidden" id="jig_id">
					<center style="font-size: 18px">Apakah anda yakin mengajukan WJO Jig <br>"<b><span id="jig_name"></span></b>" Ini?</center>
					<br>
					<div class="form-group">
						<label for="notes" class="col-sm-2 control-label">Catatan :</label>

						<div class="col-sm-10">
							<textarea class="form-control" id="notes" placeholder="Tulisakan catatan (Bila diperlukan)"></textarea>
							<br>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success pull-left" onclick="orderWJO()"><i class="fa fa-check"></i> YES</button>
					<button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
				</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillMasterTable();

		$('.select2').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true,
		});
	});

	function fillMasterTable(){
		var data = {

		}

		$.get('{{ url("fetch/workshop/jig") }}', data, function(result, status, xhr){
			$("#masterBody").empty();
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			var body = "";
			no = 1;
			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.jig_code+"</td>";
				body += "<td>"+value.process+"</td>";
				body += "<td>"+value.jig_name+"</td>";
				body += "<td>"+value.quantity+"</td>";
				body += "<td>"+value.quantity_actual+"</td>";

				var style = '';
				if (value.status_order) {
					style = 'style="background-color:#bfffa6"';
				}

				body += "<td "+style+" >"+(value.quantity - value.quantity_actual)+"</b></td>";

				var style = '';
				if (value.quantity_finish > 0) {
					style = 'style="background-color:#91ffa7"';
				} else if (value.quantity - value.quantity_actual > 0) {
					style = 'style="background-color:#ff9191"';
				}

				body += "<td "+style+">"+value.quantity_finish+"</td>";

				body += "<td style='vertical-align:middle'>";
				if (value.quantity_actual > 0) {
					body += "<button class='btn btn-success' onclick='openOrder(\""+value.jig_code+"\", \""+value.jig_name+"\" )'><i class='fa fa-send'></i>&nbsp; Order WJO</button>";
				}
				body += "</td>";
				
				body += "</tr>";
				no++;
			});
			$("#masterBody").append(body);

			$('#masterTable').DataTable({
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
				'pageLength': 25,
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
		})

	}


	function orderWJO() {
		$("#loading").show();
		var id_jig = $("#jig_id").val();
		var jig_name = $("#jig_name").text();
		var notes = $("#notes").val();

		var data = {
			id_jig : id_jig,
			jig_name : jig_name,
			notes : notes
		}

		$.post('{{ url("post/workshop/jig/order") }}', data, function(result, status, xhr){
			$("#loading").hide();
			if (result.status) {
				$("#OrderModal").modal('hide');
				$("#notes").val('');
				openSuccessGritter('Success', 'WJO Order Sukses');
				audio_ok.play();
				fillMasterTable();
			} else {
				openErrorGritter('Error', result.message);
				audio_error.play();
			}
		})
	}

	function openOrder(jig_id, name) {
		$('#OrderModal').modal({
			backdrop: 'static',
			keyboard: false
		});

		$("#jig_id").val(jig_id);
		$("#jig_name").text(name);
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection