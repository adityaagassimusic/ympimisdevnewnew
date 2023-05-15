@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-hover > tbody > tr > td:hover, table.table-hover > thead > tr > th:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	input {
		line-height: 24px;
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
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;

	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error {
		display: none;
	}
	.container_{
		margin : 10px;
		padding : 5px;
		border : solid 1px #eee;
	}
	.image_upload > input{
		display:none;
	}

</style>
@stop

@section('header')
<section class="content-header">
	 <ol class="breadcrumb" style="top:2px">
	    <li><a class="btn btn-success" href="{{url('index/ga_control/uniform/attendance')}}" style="color:white"><i class="fa fa-users"></i>&nbsp;&nbsp;Uniform Attendance</a></li>
	  </ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
	<div class="row">

		<div class="col-xs-12" style="margin-top:40px">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;vertical-align: middle;color:white" align="center">
							<span style="font-size: 2vw;color: black;width: 25%;">UNIFORM STOCK DATA</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-1">
			<img src="{{url('images/uniform1.jpg')}}" style="margin-top: 30px;margin-left: 10px;height: 30vh;">
		</div>	

		<div class="col-xs-10" style="padding-left: 20px">
			<div class="row">
				<div class="col-xs-6 col-xs-offset-3" style="padding-right: 0.5%">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="10" style="background-color : #4caf50; padding: 0px;font-size: 2vw;">MAN</th>
							</tr>
							<tr>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">S</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">M</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">L</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">2XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">3XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">4XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">5XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">6XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">7XL</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_s">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_m">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_l">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_2xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_3xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_4xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_5xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_6xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="man_short_7xl">0</td>
							</tr>
						</tbody>
					</table>
				</div>


				

				<div class="col-xs-6" style="padding-right: 0.5%">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="10" style="background-color : #00bcd4; padding: 0px;font-size: 2vw;">WOMAN SHORT SLEEVE</th>
							</tr>
							<tr>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">S</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">M</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">L</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">2XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">3XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">4XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">5XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">6XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">7XL</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_s">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_m">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_l">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_2xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_3xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_4xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_5xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_6xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_short_7xl">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-6">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="10" style="background-color : #039be5; padding: 0px;font-size: 2vw;">WOMAN LONG SLEEVE</th>
							</tr>
							<tr>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">S</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">M</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">L</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">2XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">3XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">4XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">5XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">6XL</th>
								<th width="10%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">7XL</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_s">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_m">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_l">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_2xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_3xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_4xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_5xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_6xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_long_7xl">0</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-xs-6">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="9" style="background-color : #ff7043; padding: 0px;font-size: 2vw;">WOMAN MATERNITY LONG</th>
							</tr>
							<tr>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">S</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">M</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">L</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">2XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">3XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">4XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">5XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">6XL</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_s">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_m">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_l">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_2xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_3xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_4xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_5xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-long_6xl">0</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-6">
					<table class="table table-bordered table-striped table-hover" style="background-color: #ffffff;">
						<thead>
							<tr>
								<th colspan="9" style="background-color : #ff7043; padding: 0px;font-size: 2vw;">WOMAN MATERNITY SHORT</th>
							</tr>
							<tr>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">S</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">M</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">L</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">2XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">3XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">4XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">5XL</th>
								<th width="11%" style="background-color : yellow; padding: 0px;font-size: 1.5vw">6XL</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_s">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_m">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_l">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_2xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_3xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_4xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_5xl">0</td>
								<td style="padding: 10px;font-size: 1.4vw;" onclick="detail(id)" id="woman_maternity-short_6xl">0</td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>
		</div>

				<div class="col-xs-1" style="padding:0">
					<img src="{{url('images/uniform2.jpg')}}"  style="margin-top: 30px;margin-left: -12px;height: 30vh;">
				</div>	

		<div class="col-xs-12">
			<div class="row" style="padding:20px">
				<div class="box-body">
					<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
			    	<table id="listTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>#</th>
								<th>Date</th>
								<th>Employee ID</th>
								<th>Name</th>
								<th>Gender</th>
								<th>Category</th>
								<th>Size</th>
							</tr>
						</thead>
						<tbody id="listTableBody">
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>

	</div>

	<div class="modal modal-default fade" id="detail_modal">
		<div class="modal-dialog modal-lg" style="width: 50%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h2 class="modal-title" id="detail_title"></h2>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6" style="padding-right: 0px; text-align: center;font-size: 2vw;font-weight: bold;">
							<span id="detail_gender">x</span>
							<span id="detail_category">x</span>
							<span id="detail_size">x</span>
						</div>
						<div class="col-xs-6">
				            <input type="text" class="form-control" name="qty_edit" id="qty_edit">
				        </div>
											
					</div>
				</div>
				<div class="modal-footer">
			      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
			      <!-- <input type="hidden" id="id_edit_stock"> -->
			      <button type="button" onclick="edit_stock()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
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
<script src="{{ url("js/dropzone.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2();
			fillTable();
    	setInterval(fillTable, 120000);
    	fetchTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
	});

	function capitalizeFirstLetter(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}

	function detail(id) {

		var data = id.split('_');

		var gender = data[0];
		var category = data[1];
		var size = data[2];

		var data = {
			gender : gender.toUpperCase(),
			category : category.toUpperCase(),
			size : size.toUpperCase()
		}

		$.get('{{ url("edit/ga_control/uniform/stock") }}', data, function(result, status, xhr){
			if(result.status){
	
				if("{{ Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S-GA'}}"){
					$("#detail_title").html('');
					$("#detail_title").html('<center>Edit Stock Seragam</center>');

					$('#detailBody').html("");

					$('#detail_gender').text(gender.toUpperCase());
					$('#detail_category').text(category.toUpperCase());
					$('#detail_size').text(size.toUpperCase());

					// console.log(result.de);

	    			$("#qty_edit").val(result.detail.qty);    
					$("#detail_modal").modal('show');    				
    			}
			}
		});	
	}

	function fillTable(){
		$.get('{{ url("fetch/ga_control/uniform/stock") }}', function(result, status, xhr){
			if(result.status){
				for (var i = 0; i < result.resume.length; i++) {
					var key = result.resume[i].gender + '_' + result.resume[i].category + '_' +result.resume[i].size;
					if (result.resume[i].qty == 0) {
						$('#' + key.toLowerCase()).text(result.resume[i].qty).css('background-color', 'red');
					}else{
						$('#' + key.toLowerCase()).text(result.resume[i].qty);
					}
				}
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
	  
	  return day + '-' + monthNames[month] + '-' + year;
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/ga_control/uniform/log") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.uniform_data, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.date_uniform))+'</td>';
					listTableBody += '<td style="width:1%;">'+value.employee_id+'</td>';
					listTableBody += '<td style="width:2%;">'+value.name+'</td>';
					listTableBody += '<td style="width:1%;">'+value.gender+'</td>';
					listTableBody += '<td style="width:1%;">'+value.category+'</td>';
					listTableBody += '<td style="width:1%;">'+value.size+'</td>';					
					listTableBody += '</tr>';
					count_all += 1;
				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

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
					'pageLength': 20,
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

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

	function edit_stock() {

      var data = {
        gender: $("#detail_gender").text(),
        category: $("#detail_category").text(),
        size: $("#detail_size").text(),
        qty : $("#qty_edit").val()
      };

      $.post('{{ url("update/ga_control/uniform/stock") }}', data, function(result, status, xhr){
        if (result.status == true) {
          openSuccessGritter("Success","Stock has been edited successfully.");
          fillTable();
        } else {
          openErrorGritter("Error","Failed to stock.");
        }
      })
    }

</script>
@endsection

