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
		text-align: center;
	}

	.container {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 16px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default checkbox */
	.container input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	}

	/* On mouse-over, add a grey background color */
	.container:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.container input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.container input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.container .checkmark:after {
	  left: 10px;
	  top: 5px;
	  width: 5px;
	  height: 12px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Cash Payment <span class="text-purple">現金支払い</span>
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
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>

	<div class="col-md-12" style="padding:0">
		<div class="box">
			<div class="box-body" id="setID">
			
			</div>
		</div>
	</div>


	<div class="col-md-12" style="padding:0">

		<div class="box">
			<div class="box-header">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<form method="GET" action="{{ url("export/payment_request") }}">
					<div class="col-md-2" style="padding: 0">
						<div class="form-group">
							<label>Date From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom">
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label>Date To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<!-- <div class="form-group">
							<div class="col-md-4" style="padding-right: 0;">
								<label style="color: white;"> x</label>
								<button type="submit" class="btn btn-primary form-control"><i class="fa fa-download"></i> Export Suspense Payment</button>
							</div>
						</div> -->
					</div>

					<div class="col-md-2" style="padding-right: 0;">
						<div class="form-group">
								<label style="color: white;"> x</label>
								<a class="btn btn-success pull-right" style="width: 100%" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Settlement</a>
						</div>
					</div>
				</form>
			</div>
			<div class="box-body">
				<div class="row">
					
				</div>

				<table id="listTable" class="table table-bordered table-striped table-hover">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th>#</th>
							<th>Submission Date</th>
							<th>Title</th>
							<th>Amount</th>
							<th>Document Attach</th>
							<th>Status</th>
							<th>Action</th>
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
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog modal-lg" style="width: 1250px">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
					<div class="row">
						<input type="hidden" id="id_edit">
						<div class="col-md-12">
							<div class="col-md-12" style="margin-bottom: 5px;">
								<label for="submission_date" class="col-sm-3 control-label">Date<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<div class="input-group date">
										<div class="input-group-addon">	
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
										<input type="hidden" class="form-control pull-right"  value="{{date('Y-m-d')}}" id="submission_date" name="submission_date">
										<input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}">
										<input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
										<input type="hidden" id="department" name="department" value="{{$employee->department}}">
									</div>
								</div>
							</div>

							<div class="col-md-12" style="margin-bottom: 5px">
								<label for="title" class="col-sm-3 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="title" name="title" placeholder="Title Settlement">
								</div>
							</div>

							<div class="col-md-12" style="margin-bottom: 5px;">
								<label for="amount" class="col-sm-3 control-label">Amount<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="number" class="form-control" id="amount" name="amount" placeholder="Total Amount" readonly="">
								</div>
							</div>
						</div>

          	<div class="col-xs-12" id="settle">
               <button class="btn btn-sm btn-success pull-right" onclick="add_settlement()"><i class="fa fa-plus"></i>&nbsp; Add</button>

               <table class="table">
                    <thead>
                         <tr>
                              <th style="width: 30%">Settlement<span class="text-red">*</span></th>
                              <th style="width: 30%">Description<span class="text-red">*</span></th>
                              <th style="width: 20%">Amount<span class="text-red">*</span></th>
                              <th style="width: 20%">Nota<span class="text-red">*</span></th>
                              <th>#</th>
                         </tr>
                    </thead>
                    <tbody id="body_settlement">
                    </tbody>
               </table>
          	</div>

					</div>

					<div class="col-md-12">
						<a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


  	var no = 0;
	settle_list = "";

	jQuery(document).ready(function() {

    	$('body').toggleClass("sidebar-collapse");
		fetchTable();
		fetchMonitoring();
	});


	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	$(function () {
		$('.select4').select2({
			allowClear:true,
			dropdownAutoWidth : true,
			tags: true,
	        dropdownParent: $('#modalNew')
		});
	})

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function newData(id){

		if(id == 'new'){
			$('#modalNewTitle').text('Create Settlement');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');
		}
		else{
			$('#newButton').hide();
			$('#updateButton').show();
			var data = {
				id:id
			}
			$.get('{{ url("detail/settlement") }}', data, function(result, status, xhr){
				if(result.status){

					$('#category').html('');
					$('#currency').html('');

					var category = "";
					var currency = "";

					$('#submission_date').val(result.suspend.submission_date);
					$('#currency').append(currency);
					$('#amount').val(result.suspend.amount);
					$('#title').val(result.suspend.title);
					$('#id_edit').val(result.suspend.id);

					$('#modalNewTitle').text('Update Settlement');
					$('#loading').hide();
					$('#modalNew').modal('show');
				}
				else{
					openErrorGritter('Error', result.message);
					$('#loading').hide();
					audio_error.play();
				}
			});
		}
	}

    function add_settlement() {
      getSettlementList();

      no++;
      var bodi = "";

      bodi += '<tr id="det_'+no+'" class="detail_settlement">';	
      bodi += '<td><select class="form-control select2" data-placeholder="Settlement" name="settle_'+no+'" id="settle_'+no+'" style="width: 100% height: 35px;" onchange="PilihSettle(this)" required=""></select></td>';
      bodi += '<td><select class="form-control select2" data-placeholder="Description" name="description_'+no+'" id="description_'+no+'" style="width: 100% height: 35px;" onchange="pilihItem(this)" required=""></select></td>';
      bodi += '<td><input type="number" class="form-control" id="amount_'+no+'"><input type="hidden" class="form-control" id="nota_'+no+'"></td>';
      bodi += '<td id="file_'+no+'"></td>';
	// $('#amount_'+no).attr('readonly', true).val(obj.amount);
      bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item_detail('+no+')"><i class="fa fa-trash"></i></button></td>';
      bodi += '</tr>';
      $("#body_settlement").append(bodi);
    }

    function cek_amount(){
		var total = 0;

		for (var i = 1; i <= no; i++) {
			if(isNaN(parseFloat($("#amount_"+i).val()))) {

			} else{
				total += parseFloat($("#amount_"+i).val());
			}
		}

		$("#amount").val(total);
	}

  function PilihSettle(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ url('fetch/settlement/pilih') }}?id_settlement="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$("#description_"+no).html(obj);
			} 
		});
	}

	function pilihItem(elem)
	{
		var no_settle = $("#settle_"+no).val();

		$.ajax({
			url: "{{ url('fetch/settlement/pilih_item') }}?description="+elem.value+"&id="+no_settle,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#amount_'+no).attr('readonly', true).val(obj.amount);
				$('#nota_'+no).val(obj.nota);
				$('#file_'+no).html('<a href="{{ url("files/cash_payment/settlement") }}/'+obj.nota+'" target="_blank" class="fa fa-paperclip"></a>');
				cek_amount();
			}
		});
	}

    function delete_item_detail(no) {
          $("#det_"+no).remove();
          no--;
          cek_amount();
    }

    function getSettlementList() {
		settle_list = "";
		
		$.get('{{ url("fetch/settlement/list") }}', function(result, status, xhr) {
			settle_list += "<option></option> ";
			$.each(result.settle, function(index, value){
				settle_list += "<option value="+value.id_set+">"+value.title+" - "+value.submission_date+" - "+value.created_name+"</option> ";
			});
			$('#settle_'+no).append(settle_list);
		})
	}

	function Save(id){	
		$('#loading').show();

		if(id == 'new'){
			if($("#submission_date").val() == "" || $('#title').val() == "" || $('#amount').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			
			formData.append('submission_date', $("#submission_date").val());
			formData.append('title', $("#title").val());
			formData.append('amount', $("#amount").val());
			formData.append('emp_id', $("#emp_id").val());
			formData.append('emp_name', $("#emp_name").val());
			formData.append('department', $("#department").val());

			var settlement_detail = [];

			$('.detail_settlement').each(function(index, value) {

		        var idos = $(this).attr('id');
		        var ido = idos.split("_");

		        // && $("#nota_"+ido[1]).val() != ""

	            if ($("#description_"+ido[1]).val() != "" && $("#amount_"+ido[1]).val() != "") {
	                settlement_detail.push({
	                	'id_settle' : $("#settle_"+ido[1]).val(), 
	                	'description' : $("#description_"+ido[1]).val(), 
	                	'amount' : $("#amount_"+ido[1]).val(),
	                	'nota' : $("#nota_"+ido[1]).val()
	                });
	            }
	            else {
				openErrorGritter('Error', "Please Fill field with (*) sign");
				$('#loading').hide();
	            	return false;
	            }
	        });
			
			var settlement_json = JSON.stringify(settlement_detail);
			formData.append('settlement_detail', settlement_json);

			$.ajax({
				url:"{{ url('create/settlement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#submission_date").val() == "" || $('#title').val() == "" || $('#currency').val() == "" || $('#amount').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign");
				return false;
			}
			var formData = new FormData();
			
			formData.append('id_edit', $("#id_edit").val());
			formData.append('title', $("#title").val());
			formData.append('submission_date', $("#submission_date").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('file_attach', $("#file_attach").prop('files')[0]);

			$.ajax({
				url:"{{ url('edit/settlement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}
				}
			});
		}
	}

	function clearNew(){
		$('#id_edit').val('');
		$('#title').val('');
		$("#amount").val('');
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/settlement") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.settlement, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+value.submission_date+'</td>';
					listTableBody += '<td style="width:2%;">'+value.title+'</td>';
					listTableBody += '<td style="width:2%;">'+value.amount.toLocaleString()+'</td>';

					if (value.file != null) {
						listTableBody += '<td style="width:0.1%;"><a target="_blank" href="{{ url("files/settlement") }}/'+value.file+'"><i class="fa fa-paperclip"></i></td>';
					}
					else{
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;"> - </td>';
					}

					if (value.posisi == 'user') {
						listTableBody += '<td style="width:0.1%;"><span class="label label-danger">Not Sent</span></td>';
					}
					else if (value.posisi == 'manager'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Manager</span></td>';
					}
					else if (value.posisi == 'direktur'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Director</span></td>';
					}
					else{
						listTableBody += '<td style="width:0.1%;"><span class="label label-success">Diterima Accounting</span></td>';
					}

					if (value.posisi == "user")
					{
						listTableBody += '<td style="width:2%;"><center><button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button>  <a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/settlement") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button></center></td>';
					}

					else{
						listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/settlement") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';
					}

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

	function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Settlement ini ke Manager?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("email/settlement") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function fetchMonitoring(){
		var settlement = <?php echo json_encode($settlement) ?>;

		$('#setID').html("");
		var settleData = "";

		$.each(settlement, function(key, value){
			settleData += '<div class="col-xs-3">';
			settleData += '<div class="box box-widget widget-user-2" style="border: 1px solid black;margin:0">';
			settleData += '<div class="widget-user-header bg-purple" style="height: 140px;">';
			settleData += '<div class="widget-user-image crop2">';
			settleData += '<img src="{{ url('images/avatar/') }}'+'/'+value.emp_id+'.jpg'+'" alt="">';
			settleData += '<h3 class="widget-user-username">'+value.emp_name+'</h3>';
			settleData += '<h5 class="widget-user-desc">'+value.emp_id+'</h5>';
			settleData += '<h5 class="widget-user-username" style="font-size:15px">'+value.title+'</h5>';
			settleData += '</div>';
			settleData += '</div>';
			settleData += '<div class="box-footer no-padding">';
			settleData += '<ul class="nav nav-stacked">';
			settleData += '<li>';
			settleData += '<table style="width: 100%;margin-bottom:0px !important" class="table table-bordered" >';
			settleData += '<tbody>';
			settleData += '<tr>';
			settleData += '<td style="width: 2%; font-weight: bold; font-size: 1.1vw;color:black;text-align:left">Saldo</td><td style="width: 1%;border-right:0;">:</td><td style="width: 5%; font-weight: bold; font-size: 1.1vw;color:black;text-align:left"> '+value.item_currency+' '+parseInt(value.amount_belum_settle + value.amount_sudah_settle).toLocaleString('de-DE')+'</td>';
			settleData += '</tr>';
			settleData += '<tr>';
			settleData += '<td style="width: 2%; font-weight: bold; font-size: 1.1vw;color:red;text-align:left">Suspense </td><td style="width: 1%;border-right:0;">:</td><td style="width: 5%; font-weight: bold; font-size: 1.1vw;color:red;text-align:left"> '+value.item_currency+' '+value.amount_belum_settle.toLocaleString('de-DE')+'</td>';
			settleData += '</tr>';
			settleData += '<tr>';
			settleData += '<td style="width: 2%; font-weight: bold; font-size: 1.1vw;color:green;text-align:left">Settlement </td><td style="width: 1%;border-right:0;">:</td><td style="width: 5%; font-weight: bold; font-size: 1.1vw;color:green;text-align:left"> '+value.item_currency+' '+value.amount_sudah_settle.toLocaleString('de-DE')+'</td>';
			settleData += '</tr>';
			settleData += '</tbody>';
			settleData += '</table>';
			settleData += '</li>';
			settleData += '</ul>';
			settleData += '</div>';
			settleData += '</div>';
			settleData += '</div>';
		});
		$('#setID').append(settleData);
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

