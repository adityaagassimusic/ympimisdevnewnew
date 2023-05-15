@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.buttonclass {
	  top: 0;
	  left: 0;
	  transition: all 0.15s linear 0s;
	  position: relative;
	  display: inline-block;
	  padding: 15px 25px;
	  background-color: #ffe800;
	  text-transform: uppercase;
	  color: #404040;
	  font-family: arial;
	  letter-spacing: 1px;
	  box-shadow: -6px 6px 0 #404040;
	  text-decoration: none;
	  cursor: pointer;
	}
	.buttonclass:hover {
	  top: 3px;
	  left: -3px;
	  box-shadow: -3px 3px 0 #404040;
	  color: white
	}
	.buttonclass:hover::after {
	  top: 1px;
	  left: -2px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass:hover::before {
	  bottom: -2px;
	  right: 1px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass::after {
	  transition: all 0.15s linear 0s;
	  content: "";
	  position: absolute;
	  top: 2px;
	  left: -4px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg);
	  z-index: -1;
	}
	.buttonclass::before {
	  transition: all 0.15s linear 0s !important;
	  content: "";
	  position: absolute;
	  bottom: -4px;
	  right: 2px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg) !important;
	  z-index: -1 !important;
	}

	a.buttonclass {
	  position: relative;
	}

	.hover_td:hover{
		background-color: #ffffb0 !important;
	}

	a:active.buttonclass {
	  top: 6px;
	  left: -6px;
	  box-shadow: none;
	}
	a:active.buttonclass:before {
	  bottom: 1px;
	  right: 1px;
	}
	a:active.buttonclass:after {
	  top: 1px;
	  left: 1px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-4">
							<label>Cabang Kompetisi</label>
							<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Cabang Kompetisi" id="category">
								<option value=""></option>
								<option value="Mobile Legends">Mobile Legends</option>
								<option value="Penalti Putra">Penalti Putra</option>
								<option value="Penalti Putri">Penalti Putri</option>
								<option value="Indiaka">Indiaka</option>
								<option value="Karaoke">Karaoke</option>
								<option value="Music Acoustic">Music Acoustic</option>
								<option value="Senam Taiso">Senam Taiso</option>
							</select>
						</div>
						<div class="col-xs-12" style="margin-top: 10px;">
							<button class="btn btn-success" onclick="fillList();">
								<b>Search</b>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="row">
						<div class="col-xs-12">
							<table class='table table-bordered table-striped table-hover' style="width: 100%">
								<thead>
									<tr>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Mobile Legends</th>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Penalti Putra</th>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Penalti Putri</th>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Indiaka</th>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Karaoke</th>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Music Acoustic</th>
										<th colspan="2" style="background-color: rgba(126,86,134,.7);width: 15;color: white;">Senam Taiso</th>
									</tr>
									<tr>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
										<th style="background-color: #ffabab;width: 15;">Limit</th>
										<th style="background-color: #98ff96;width: 15;">Actual</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ml_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ml">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ppa_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ppa">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ppi_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ppi">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="in_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="in">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ka_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ka">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ba_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ba">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ta_limit">0</td>
										<td class="hover_td" style="border: 1px solid black;font-size: 30px;font-weight: bold;" id="ta">0</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-xs-12">
							<div class="row" id="divTable">
							</div>
						</div>
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
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});

	function initiateTable() {
		$('#tableCompetition').DataTable().clear();
		$('#tableCompetition').DataTable().destroy();
		$('#divTable').html('');
		var thead = '';
		thead += "<div class='col-xs-12'>";
		thead += "<table id='tableCompetition' class='table table-bordered table-striped table-hover'>";
			thead += '<thead style="background-color: rgba(126,86,134,.7);">';
				thead += '<tr>';
					thead += '<th style="width: 1%">No.</th>';
					thead += '<th style="width: 3%">Category</th>';
					thead += '<th style="width: 3%">Team</th>';
					thead += '<th style="width: 1%">Reg. Number</th>';
					thead += '<th style="width: 2%">Phone No</th>';
					thead += '<th style="width: 2%">Player</th>';
					thead += '<th style="width: 4%">Employee ID</th>';
					thead += '<th style="width: 4%">Name</th>';
					thead += '<th style="width: 4%">Photo</th>';
					thead += '<th style="width: 4%">Dept</th>';
					thead += '<th style="width: 4%">Sect</th>';
					thead += '<th style="width: 4%">Group</th>';
					thead += '<th style="width: 4%">Sub Group</th>';
					thead += '<th style="width: 3%">Song</th>';
					thead += '<th style="width: 3%">Singer</th>';
					thead += '<th style="width: 3%">Location</th>';
					thead += '<th style="width: 3%">Attribute</th>';
				thead += '</tr>';
			thead += '</thead>';
			thead += '<tbody id="bodyTableCompetition">';
			thead += '</tbody>';
			thead += '<tfoot>';
			thead += '<tr>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '<th></th>';
			thead += '</tr>';
			thead += '</tfoot>';
		thead += '</table>';
		thead += '</div>';

		$("#divTable").append(thead);

		$("#bodyTableCompetition").html('');
	}

	function fillList(){
		$('#loading').show();
		var data = {
			category:$('#category').val()
		}
		$.get('{{ url("fetch/competition/registration/report") }}', data,function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";
				var index = 1;
				$.each(result.competition, function(key, value) {
						tableData += '<tr>';
						tableData += '<td style="text-align:right;padding-right:5px;">'+ index +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.category +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.team_name +'</td>';
						tableData += '<td style="text-align:right;padding-right:5px;">'+ value.no_urut +'</td>';
						tableData += '<td style="text-align:right;padding-right:5px;">'+ value.phone_no +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.player_name +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.employee_id +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.name +'</td>';
						var url = '{{url("images/avatar/")}}/'+value.employee_id+'.jpg';
						tableData += '<td style="text-align:center;padding:4px;"><img src="'+ url +'" style="width:100px;"></td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.department +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ value.section +'</td>';
						var group = '';
						var sub_group = '';
						for(var i = 0; i < result.emp.length;i++){
							if (result.emp[i].employee_id == value.employee_id) {
								group = result.emp[i].group;
								sub_group = result.emp[i].sub_group;
							}
						}
						tableData += '<td style="text-align:left;padding-left:5px;">'+ (group || '') +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ (sub_group || '') +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ (value.song || '') +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ (value.singer || '') +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ (value.location || '') +'</td>';
						tableData += '<td style="text-align:left;padding-left:5px;">'+ (value.attribute || '') +'</td>';
						tableData += '</tr>';
						index++;
				});
				$('#bodyTableCompetition').append(tableData);

				for(var i = 0; i < result.all.length;i++){
					if (result.all[i].category == 'Mobile Legends') {
						$('#ml').html(result.all[i].qty);
					}
					if (result.all[i].category == 'Penalti Putra') {
						$('#ppa').html(result.all[i].qty);
					}
					if (result.all[i].category == 'Penalti Putri') {
						$('#ppi').html(result.all[i].qty);
					}
					if (result.all[i].category == 'Indiaka') {
						$('#in').html(result.all[i].qty);
					}
					if (result.all[i].category == 'Karaoke') {
						$('#ka').html(result.all[i].qty);
					}
					if (result.all[i].category == 'Music Acoustic') {
						$('#ba').html(result.all[i].qty);
					}
					if (result.all[i].category == 'Senam Taiso') {
						$('#ta').html(result.all[i].qty);
					}
				}

				for(var i = 0; i < result.limit.length;i++){
					if (result.limit[i].category == 'Mobile Legends') {
						$('#ml_limit').html(result.limit[i].limit);
					}
					if (result.limit[i].category == 'Penalti Putra') {
						$('#ppa_limit').html(result.limit[i].limit);
					}
					if (result.limit[i].category == 'Penalti Putri') {
						$('#ppi_limit').html(result.limit[i].limit);
					}
					if (result.limit[i].category == 'Indiaka') {
						$('#in_limit').html(result.limit[i].limit);
					}
					if (result.limit[i].category == 'Karaoke') {
						$('#ka_limit').html(result.limit[i].limit);
					}
					if (result.limit[i].category == 'Music Acoustic') {
						$('#ba_limit').html(result.limit[i].limit);
					}
					if (result.limit[i].category == 'Senam Taiso') {
						$('#ta_limit').html(result.limit[i].limit);
					}
				}

				$('#tableCompetition tfoot th').each( function () {
			        var title = $(this).text();
			        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="10"/>' );
			      } );

				var table = $('#tableCompetition').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
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

		      $('#tableCompetition tfoot tr').appendTo('#tableCompetition thead');
				
				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
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

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}
</script>
@endsection