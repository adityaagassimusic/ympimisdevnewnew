@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
<style>
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.dataTables_filter {
		display: none;
	} 
	.container .checkmark:after {
  top: 9px;
  left: 9px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
}

.container_checkmark {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 20px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
@endsection

@section('content')
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
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
	<p style="position: absolute; color: White; top: 45%; left: 35%;">
		<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>

<div class="row">
	<div class="col-xs-12">
		<h1>
		<center><span style="color: white; font-weight: bold; font-size: 28px; text-align: center;" id="judul">YMPI Visitor Confirmation By Department</span></center>
		</h1>

		<div class="row" style="display: none;padding-left: 20px;padding-right: 20px" id="tabelvisitor">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body">
						<div class="table-responsive">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 10px">
									<a href="javascript:void(0)" data-toggle="modal-default" class="btn btn-md btn-success pull-right" onClick="inputag2()">Sudah Ditemui</a>
									<a href="javascript:void(0)" data-toggle="modal-default" class="btn btn-md btn-danger pull-left"  onClick="inputag3()">Belum Ditemui</a>
								</div>
							</div>
							<table id="visitorList" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 2%">Incoming Date</th>
										<th style="width: 2%">Confirmers</th>
										<th style="width: 1%">Employee</th>
										<th style="width: 2%">Department</th>
										<th style="width: 2%">Company</th>
										<th style="width: 1%">Visitor Name</th>
										<th style="width: 1%">Total</th>
										<th style="width: 1%">Purpose</th>
										<th style="width: 1%">Status</th>
										<th style="width: 2%">Confirmation</th>
									</tr>
								</thead>
								<tbody id="visitorListBody">
								</tbody>
								<!-- <tfoot>
									<tr style="color: black">
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
									</tr>
								</tfoot> -->
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-default">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background-color: rgba(126,86,134,.7)">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Visitor List</h4>
					<div id="header">
				</div>
			</div>
			<div class="modal-body">
				<div  class="form-group">
					<input type="text" name="lop" id="lop" value="1" hidden>

					<div class="col-sm-2" style="padding-right: 0;">
						No. KTP/SIM												
					</div>
					<div class="col-sm-2" style="padding-left: 1; padding-right: 0;">
						Full Name												
					</div>
					<div class="col-sm-2" style="padding-left: 1; padding-right: 0;">
						Status										
					</div>
					<div class="col-sm-2">												
						No Hp	
					</div>
					<div class="col-sm-2">
						Tag Number												
					</div>
					<div class="col-sm-2">
						Remark										
					</div><br>
					<div id="apenlist"><br>		
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" onclick="inputag2()">Confirm</button>
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
	<script src="{{ url("js/jqbtk.js")}}"></script>
	<script >
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});




		jQuery(document).ready(function() { 
			$('#nikkaryawan').keyboard();
			$('#telp').keyboard();
			// $('#nikkaryawan').val('asd');
			// filltelpon();
			fillList();
			$('#tabelvisitor').css({'display':'block'})
			// $('.select2').select2();
			$('.select3').select2({
				dropdownParent: $('#modal-default')
			});
			
		});


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

		function fillList(){
			// var data = {
			// 	process : 'Forging'
			// }
			var department;
			$.get('{{ url("fetch/visitor/fetchVisitorByManager") }}', function(result, status, xhr){
				if(result.status){
					department = result.name;
					$('#visitorList').DataTable().clear();
					$('#visitorList').DataTable().destroy();
					$('#visitorListBody').html("");
					var tableData = "";
					$.each(result.lists, function(key, value) {
						if (value.remark == "") {
							var bg = "background-color: rgb(204, 255, 255);";
						}else{
							var bg = "background-color: rgb(255, 204, 255);";
						}
						tableData += '<tr>';
						tableData += '<td>'+ value.created_at +'</td>';
						var confirmers_name = [];
						if (value.name == 'Budhi Apriyanto') {
							confirmers_name.push('Anton Budi Santoso');
							confirmers_name.push('Agus Yulianto');
						}if (value.name == 'Arief Soekamto') {
							confirmers_name.push('Prawoto');
							confirmers_name.push('Dicky Kurniawan');
						}if (value.name == 'Hiroshi Ura') {
							confirmers_name.push('Ahmad Subhan Hidayat');
						}if (value.name == 'Yukitaka Hayakawa') {
							confirmers_name.push('Ahmad Subhan Hidayat');
						}if (value.name == 'Kyohei Iida') {
							confirmers_name.push('Ahmad Subhan Hidayat');
						}if (value.name == 'Hiromichi Ichimura') {
							confirmers_name.push('Ahmad Subhan Hidayat');
						}else{
							$.each(result.confirmers_all, function(key2, value2) {
								if (value2.department == value.department && value2.name != 'Budhi Apriyanto') {
									confirmers_name.push(value2.name);
								}
							});
						}
						tableData += '<td>'+ confirmers_name.join(' - ') +'</td>';
						tableData += '<td>'+ value.name +'</td>';
						tableData += '<td>'+ (value.department || "") +'</td>';
						tableData += '<td>'+ value.company +'</td>';
						tableData += '<td>'+ value.full_name +'</td>';
						if (value.total1 == null) {
							tableData += '<td>1 Orang</td>';
						}else{
							tableData += '<td>'+ value.total1 +' Orang</td>';
						}
						tableData += '<td>'+ value.purpose +'</td>';
						tableData += '<td>'+ value.status +'</td>';
						if (value.remark == null) {
							tableData += '<td>';
							tableData += '<label class="container_checkmark">Confirm';
							tableData += '<input type="checkbox" name="remark" id="remark" class="remark" value="'+value.id+'">';
							tableData += '<span class="checkmark_checkmark"></span>';
							tableData += '</label>';
							tableData += '</td>';
						}
						else{
							tableData += '<td style="'+bg+'">'+ value.remark +'</td>';
						}
						tableData += '</tr>';
					});
					$('#judul').html('YMPI Visitor Confirmation of '+department);
					$('#visitorListBody').append(tableData);

					// $('#visitorList tfoot th').each(function(){
					// 	var title = $(this).text();
					// 	$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
					// });
						
					var table = $('#visitorList').DataTable({
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
					// table.columns().every( function () {
					// 	var that = this;

					// 	$( 'input', this.footer() ).on( 'keyup change', function () {
					// 		if ( that.search() !== this.value ) {
					// 			that
					// 			.search( this.value )
					// 			.draw();
					// 		}
					// 	} );
					// } );

					// $('#visitorList tfoot tr').appendTo('#visitorList thead');
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function editop(id){
			$('#header').empty();
			$("#apenlist").empty();						
			$('#modal-default').modal({backdrop: 'static', keyboard: false});

			var data = {
				id : id
			}
			$.get('{{ url("visitor_getlist") }}', data, function(result, status, xhr){
				var no =1;
				if(xhr.status == 200){
					if(result.status){
						$('#header').html();
						$('#apenlist').html();

						$.each(result.header_list, function(key, value) { 
							$('#header').append('<b id="idhead" hidden>'+ value.id +'</b><h4 class="modal-title">'+ value.company +'</h4><h4 class="modal-title">'+ value.name +'</h4><h4 class="modal-title">'+ value.department +'</h4>');
						}); 				

						$.each(result.id_list, function(key, value) {
							if (value.remark =="Confirmed") {
								$bg = "background-color: rgb(204, 255, 255);";
							}else{
								$bg = "background-color: rgb(255, 204, 255);";
							}
							$('#apenlist').append('<div id="'+ value.tag +'" style="'+$bg+'height:20px"><div class="col-sm-2" style="padding-right: 0;"><input readonly type="text" class="form-control" id="visitor_id0" name="visitor_id0" placeholder="No. KTP/SIM" required value="'+ value.id_number +'"></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="visitor_name0" name="visitor_name0" placeholder="Full Name" required value="'+ value.full_name +'"></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="status0" name="status0" placeholder="No Hp" value="'+ value.status +'" ></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="telp0" name="telp0" placeholder="No Hp" value="'+ value.telp +'" ></div><div class="col-sm-2"><input readonly type="text" class="form-control" id="'+ value.id +'" placeholder="Tag Number" name="'+no+'" value="'+ value.tag +'"  autofocus " "></div><div class="col-sm-2" style="padding-left:0px"><select class="form-control select3" data-placeholder="Select Remark" name="remark0" id="remark0" style="width: 100%"><option value="Sudah Ditemui">Sudah Ditemui</option><option value="Belum Ditemui">Belum Ditemui</option></select></div></div>	<br><br>');
							no++;
						});


						$("[name='tagvisit']").focus(); 		

					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}
			});
		}

		function inputag(id,name) {

			if (event.keyCode == 13 || event.keyCode == 9) {
				var id = $('#idhead').text();
				var idtag = $('#tagvisit').val();
				// var table = $('#visitorlist').DataTable();
				var data = {
					id:id,
					idtag:idtag                  
				}
				$.post('{{ url("visitor_updateremark") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){							
							openSuccessGritter('Success!', result.message);
							$('#tagvisit').val('');
							$('#'+idtag).css({'background-color':'rgb(204, 255, 255)'})							
						}
						else{
							openErrorGritter('Error!', result.message);
							$('#tagvisit').val('');
						}
					}
					else{
						alert("Disconnected from server");
					}
				});				
			}	
		}

		// update all remark

		function inputag2(id) {
			$('#loading').show();
				var id = [];
				$("input[name='remark']:checked").each(function (i) {
			            id[i] = $(this).val();
		        });
		        if (id.length == 0) {
		        	$('#loading').hide();
		        	openErrorGritter('Error!','Pilih List Visitor. <br>Bisa memilih lebih dari 1.');
		        	return false;
		        }
				var remark = 'Sudah Ditemui';
				var data = {
					id:id,
					remark:remark
				}

				$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							reloadtable();						
							$('#loading').hide();
							openSuccessGritter('Success!', result.message);
												
						}
						else{
							$('#loading').hide();
							openErrorGritter('Error!', result.message);
							
						}
					}
					else{
						$('#loading').hide();
						alert("Disconnected from server");
					}
				});
		}

		function inputag3() {
			$('#loading').show();
				var id = [];
				$("input[name='remark']:checked").each(function (i) {
			            id[i] = $(this).val();
		        });
		        if (id.length == 0) {
		        	$('#loading').hide();
		        	openErrorGritter('Error!','Pilih List Visitor. <br>Bisa memilih lebih dari 1.');
		        	return false;
		        }
				var remark = 'Belum Ditemui';
				var data = {
					id:id,
					remark:remark
				}
				$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							reloadtable();						
							$('#loading').hide();
							openSuccessGritter('Success!', result.message);
												
						}
						else{
							$('#loading').hide();
							openErrorGritter('Error!', result.message);
							
						}
					}
					else{
						$('#loading').hide();
						alert("Disconnected from server");
					}
				});
		}

		function reloadtable() {
			// $('#visitorlist').DataTable().ajax.reload();
			$('#modal-default').modal('hide');
			fillList();
		}

		// $(function () {

	 //    })
	</script>
	@endsection