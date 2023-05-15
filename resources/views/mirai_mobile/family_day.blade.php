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
		{{$title}} <span class="text-purple">{{$title_jp}}</span>
		<!-- <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#importExcel">
			<i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;
			Upload Excel
		</button> -->
		<!-- <a class="buttonclass">
			button
		</a> -->
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
		<div class="col-xs-9 col-md-9 col-lg-9">
			<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px; height: 40vh;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="text-align: left;vertical-align: middle">Status</th>
						<th style="text-align: right;vertical-align: middle">Total Karyawan</th>
						<th style="text-align: right;vertical-align: middle">Sudah Ambil Tiket</th>
						<th style="text-align: right;vertical-align: middle">Karyawan + Keluarga</th>
					</tr>
				</thead>
				<tbody>
					
					<tr>
						<td style="width: 8%; font-weight: bold; font-size: 1.2vw;text-align:left">&nbsp;Jatim Park 1 + Museum Angkut</td>
						<td id="count_opsi1" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_1 ?></td>
						<td id="count_opsi1" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_tiket_1 ?></td>
						<td id="count_opsi1" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_all1 ?></td>
					</tr>
					<tr>
						<td style="width: 8%; font-weight: bold; font-size: 1.2vw;text-align:left">&nbsp;Jatim Park 3 : Dino Park + Fun Tech + Museum Musik Dunia + Circus Magic</td>
						<td id="count_opsi2" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_2 ?></td>
						<td id="count_opsi1" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_tiket_2 ?></td>
						<td id="count_opsi2" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_all2 ?></td>
					</tr>
					<tr>
						<td style="width: 8%; font-weight: bold; color: black; font-size: 1.2vw;text-align:left">&nbsp;Jatim Park 3 : Dino Park + Predator Fun Park</td>
						<td id="count_opsi3" style="width: 1%; text-align: right; font-weight: bold; color: black; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_3 ?></td>
						<td id="count_opsi1" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_tiket_3 ?></td>
						<td id="count_opsi3" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_all3 ?></td>
					</tr>
					<tr>
						<td style="width: 8%;  font-weight: bold; color: black; font-size: 1.2vw;text-align:left">&nbsp;Taman Safari Indonesia II Prigen</td>
						<td id="count_opsi4" style="width: 1%; text-align: right; font-weight: bold;  color: black; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_4 ?></td>
						<td id="count_opsi1" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_tiket_4 ?></td>
						<td id="count_opsi4" style="width: 1%; text-align: right; font-weight: bold;  color: black; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_all4 ?></td>
					</tr>
					<tr>
						<td style="width: 8%;  font-weight: bold; color: black; font-size: 1.2vw;text-align:left">&nbsp;Belum Mengisi</td>
						<td id="count_belum_mengisi" style="width: 1%; text-align: right;  font-weight: bold; color: black; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_belum_mengisi ?></td>
						<td id="count_belum_mengisi" style="width: 1%; text-align: right;  font-weight: bold; color: black; font-size: 1.2vw;">0</td>
						<td id="count_belum_mengisi" style="width: 1%; text-align: right;  font-weight: bold; color: black; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_belum_mengisi_all ?></td>
					</tr>
					<tr>
						<td style="width: 8%; font-weight: bold; font-size: 1.2vw;text-align: left">&nbsp;Total</td>
						<td id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->jumlah ?></td>
						<td id="count_all" style="width: 1%; text-align: right;  font-weight: bold; color: black; font-size: 1.2vw;"><?= $emp_total[0]->destinasi_tiket_all ?></td>
						<td id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->jumlah_all ?></td>
					</tr>	
				</tbody>
			</table>
		</div>

		<div class="col-xs-3 col-md-3 col-lg-3">
			<table id="resumeVacation" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px; height: 40vh;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="text-align: left;vertical-align: middle">Status</th>
						<th style="text-align: right;vertical-align: middle">Count Employee</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 8%; font-weight: bold; font-size: 1.2vw;text-align:left">&nbsp;Sudah Upload Foto</td>
						<td id="count_foto" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->sudah_foto ?></td>
					</tr>

					<tr>
						<td style="width: 8%; font-weight: bold; font-size: 1.2vw;text-align:left">&nbsp;Belum Upload Foto</td>
						<td id="count_foto" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->belum_foto ?></td>
					</tr>

					<tr>
						<td style="width: 8%; font-weight: bold; font-size: 1.2vw;text-align: left">&nbsp;Total</td>
						<td id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"><?= $emp_total[0]->jumlah ?></td>
					</tr>
					
				</tbody>
			</table>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
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
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function initiateTable() {
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th width="1%">Tanggal Mengisi</th>';
		tableData += '<th width="1%">NIK</th>';
		tableData += '<th width="5%">Nama</th>';
		tableData += '<th width="5%">Department</th>';
		tableData += '<th width="1%">Status</th>';
		tableData += '<th width="1%">Jumlah Tiket</th>';
		tableData += '<th width="10%">Destinasi</th>';
		tableData += '<th width="3%">Lomba</th>';
		tableData += '<th width="2%">Foto</th>';
		tableData += '<th width="2%">Foto Lomba</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example1Body">';
		tableData += "</tbody>";
		tableData += "<tfoot>";
		tableData += "<tr>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "<th></th>";
		tableData += "</tr>";
		tableData += "</tfoot>";
		tableData += "</table>";
		$('#divTable').append(tableData);
	}

	function fillList(){
			$('#loading').show();

			$.get('{{ url("fetch/family_day/report") }}', function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";
				
				$.each(result.family, function(key, value) {
					
					tableData += '<tr>';
					if (value.tanggal != null) {
						tableData += '<td>'+ (getFormattedDate(new Date(value.tanggal)) || '') +'</td>';
					}else{
						tableData += '<td></td>';
					}
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td style="text-align:left">&nbsp;&nbsp;'+ value.name +'</td>';
					tableData += '<td style="text-align:left">&nbsp;&nbsp;'+ value.department +'</td>';
					tableData += '<td>'+ value.status +'</td>';
					tableData += '<td>'+ (value.jumlah_tiket || '') +'</td>';
					if (value.destinasi == "Opsi 1") {
						var keterangan = 'Jatim Park 1 + Museum Angkut';
						var color = '#4fff9e';
					} else if (value.destinasi == "Opsi 2") {
						var keterangan = 'Jatim Park 3 : Dino Park + Fun Tech + Museum Musik Dunia + Circus Magic';
						var color = '#4fff9e';
					} else if (value.destinasi == "Opsi 3") {
						var keterangan = 'Jatim Park 3 : Dino Park + Predator Fun Park';
						var color = '#4fff9e';
					} else if (value.destinasi == "Opsi 4") {
						var keterangan = 'Taman Safari Indonesia II Prigen';
						var color = '#4fff9e';
					} else {
						var keterangan = 'Belum Mengisi';
						var color = '#ffadad';
					}
					tableData += '<td style="background-color: '+color+';text-align:left">&nbsp;&nbsp;'+ keterangan +'</td>';
					tableData += '<td>'+ (value.lomba || '') +'</td>';
					if (value.foto != null) {
            	tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.foto);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="https://ympi.co.id/ympicoid/public/images/foto_festival/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
            }
	           tableData += '</td>';
          }else{

            	tableData += '<td style="text-align:center">';
	           tableData += '</td>';
          }

          if (value.foto_lomba != null) {
            	tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.foto_lomba);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="https://ympi.co.id/ympicoid/public/images/foto_festival/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
            }
	           tableData += '</td>';
          }else{

            	tableData += '<td style="text-align:center">';
	           tableData += '</td>';
          }

					// tableData += '<td>'+ (value.foto || '') +'</td>';
					tableData += '</tr>';
				});
				$('#example1Body').append(tableData);

				var table = $('#example1').DataTable({
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
				
				

				$('#example1 tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
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

				$('#example1 tfoot tr').appendTo('#example1 thead');
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
</script>
@endsection