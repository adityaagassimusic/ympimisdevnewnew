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
		tableData += '<th width="1%">Kategori</th>';
		tableData += '<th width="1%">NIK</th>';
		tableData += '<th width="3%">Nama</th>';
		tableData += '<th width="1%">Tanggal SIM</th>';
		tableData += '<th width="1%">Foto SIM</th>';
		tableData += '<th width="1%">Nomor Polisi</th>';
		tableData += '<th width="1%">Tanggal STNK</th>';
		tableData += '<th width="1%">Foto STNK</th>';
		tableData += '<th width="2%">Foto Kendaraan</th>';
		tableData += '<th width="1%">Nomor Polisi 2</th>';
		tableData += '<th width="1%">Tanggal STNK 2</th>';
		tableData += '<th width="1%">Foto STNK 2</th>';
		tableData += '<th width="2%">Foto Kendaraan 2</th>';
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

			$.get('{{ url("fetch/vehicle/report") }}', function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";
				
				$.each(result.vehicle, function(key, value) {
					
					tableData += '<tr>';
					tableData += '<td>'+ value.category +'</td>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td style="text-align:left">&nbsp;&nbsp;'+ value.name +'</td>';
					if (value.date_sim == null) {
						tableData += '<td></td>';
					}else{
						tableData += '<td>'+ (getFormattedDate(new Date(value.date_sim)) || '') +'</td>';
					}
					if (value.file_sim != null) {
            tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.file_sim);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="http://10.109.33.10/ympicoid/public/images/kendaraan/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
            }
	           tableData += '</td>';
          }else{
            tableData += '<td style="text-align:center">';
	          tableData += '</td>';
          }
					tableData += '<td style="text-align:center">'+ value.nopol +'</td>';
					tableData += '<td>'+ (getFormattedDate(new Date(value.date_stnk)) || '') +'</td>';
					if (value.file_stnk != null) {
            tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.file_stnk);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="http://10.109.33.10/ympicoid/public/images/kendaraan/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
            }
	           tableData += '</td>';
          }else{
            tableData += '<td style="text-align:center">';
	          tableData += '</td>';
          }

          if (value.file_kendaraan != null) {
            tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.file_kendaraan);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="http://10.109.33.10/ympicoid/public/images/kendaraan/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
            }
	           tableData += '</td>';
          }else{

            	tableData += '<td style="text-align:center">';
	           tableData += '</td>';
          }

          tableData += '<td style="text-align:center">'+ (value.nopol_2 || '') +'</td>';
          if (value.date_stnk_2 == null) {
						tableData += '<td></td>';
          }else{
						tableData += '<td>'+ (getFormattedDate(new Date(value.date_stnk_2)) || '') +'</td>';
          }
					if (value.file_stnk_2 != null) {
            tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.file_stnk_2);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="http://10.109.33.10/ympicoid/public/images/kendaraan/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
            }
	           tableData += '</td>';
          }else{
            tableData += '<td style="text-align:center">';
	          tableData += '</td>';
          }

          if (value.file_kendaraan_2 != null) {
            tableData += '<td style="text-align:center">' ;
            var data = JSON.parse(value.file_kendaraan_2);
            for (var i = 0; i < data.length; i++) {
	            tableData += '<a href="http://10.109.33.10/ympicoid/public/images/kendaraan/'+data[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
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