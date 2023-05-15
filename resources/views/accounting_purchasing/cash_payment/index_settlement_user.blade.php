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
		<div class="col-md-12">
			<div class="form-group">
					<a class="btn btn-success pull-right" style="width: 100%" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Settlement</a>
			</div>
		</div>
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;color:white" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">SETTLEMENT PAYMENT</span>
							<span style="font-size: 25px;color: black;width: 25%;">精算</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4" style="padding:0">

		<div class="col-md-12" style="padding:0">
			<div class="box">
				<div class="box-body" id="setID">
				</div>
			</div>
		</div>

		<div class="col-md-12" style="padding:0">
			<div id="chart2" style="width: 100%"></div>
		</div>

	</div>
	<div class="col-md-8" style="padding:0">
		<div class="box">

			<div class="box-header">

				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<form method="GET" action="{{ url("export/payment_request") }}">
					<div class="col-md-4" style="padding: 0">
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
					<div class="col-md-4">
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
							
							<div class="col-md-6" style="margin-bottom: 5px;">
								<label for="submission_date" class="col-sm-12 control-label">Date<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<div class="input-group date">
										<div class="input-group-addon">	
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
										<input type="hidden" class="form-control pull-right"  value="{{date('Y-m-d')}}" id="submission_date" name="submission_date">
									</div>
								</div>
							</div>

							<div class="col-md-6" style="margin-bottom: 5px">
								<label for="employee_id" class="col-sm-12 control-label">Requested By<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
									<input type="hidden" id="emp_id" class="form-control" value="{{$employee->employee_id}}" readonly="">
									<input type="hidden" id="emp_name" class="form-control" value="{{$employee->name}}" readonly="">
								</div>
							</div>

							<div class="col-md-4" style="margin-bottom: 5px">
								<label for="title" class="col-sm-12 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="title" name="title" placeholder="Reason or Purpose">
								</div>
							</div>

							<div class="col-md-4" style="margin-bottom: 5px;">
								<label for="amount_suspend" class="col-sm-12 control-label">Amount Suspense</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="amount_suspend" name="amount_suspend" placeholder="Total Amount Suspense" readonly="">
								</div>
							</div>

							<div class="col-md-4" style="margin-bottom: 5px;">
								<label for="amount_settle" class="col-sm-12 control-label">Amount Settle</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="amount_settle" name="amount_settle" placeholder="Total Amount Settle" readonly="">
								</div>
							</div>

							<!-- <div class="col-md-4" style="margin-bottom: 5px;">
								<label for="amount_total" class="col-sm-12 control-label">Amount Total</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="amount_total" name="amount_total" placeholder="Total Amount" readonly="">
								</div>
							</div> -->

						</div>

			            	<div class="col-xs-6" id="irregular">
			                 <button class="btn btn-sm btn-success pull-right" onclick="add_suspend()"><i class="fa fa-plus"></i>&nbsp; Add</button>

			                 <table class="table">
			                      <thead>
			                           <tr>
			                                <th style="width: 60%">Suspense<span class="text-red">*</span></th>
			                                <th style="width: 30%">Amount<span class="text-red">*</span></th>
			                                <th>#</th>
			                           </tr>
			                      </thead>
			                      <tbody id="body_suspend">
			                      </tbody>
			                 </table>
			            	</div>

			            	<div class="col-xs-6" id="settle">
			                 <button class="btn btn-sm btn-success pull-right" onclick="settle()"><i class="fa fa-plus"></i>&nbsp; Add</button>

			                 <table class="table">
			                      <thead>
			                           <tr>
			                                <th style="width: 60%">Description<span class="text-red">*</span></th>
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

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;"></h4></center>
			</div>
			<div class="modal-body">
				<table class="table table-bordered" id="tableDetail">
					<thead>
						<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
							<th style="width: 1%">No.</th>
							<th style="width: 5%">Detail</th>
							<th style="width: 3%">Harga</th>
							<th style="width: 10%">Nota</th>
							<th style="width: 3%">Status</th>
						</tr>
					</thead>
					<tbody id="bodyTableDetail">
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
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


    var no_suspend = 0;
    var no_settle = 0;
	suspend_list = "";

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

	// $("#amount").change(function(){
	// 	var output = parseFloat($('#amount').val()); 
	// 	var output2 = output.toLocaleString(undefined,{'minimumFractionDigits':2,'maximumFractionDigits':2});
	// 	$('#amount').val(output2);
 //  	});

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

	function cek_amount_suspend(){
		var total = 0;

		for (var i = 1; i <= no_suspend; i++) {
			if(isNaN(parseFloat($("#amount_suspend"+i).val()))) {

			}else{
				total += parseFloat($("#amount_suspend"+i).val());
			}
		}

		$("#amount_suspend").val(total);
	}

	function cek_amount_settle(){
		var total = 0;

		for (var i = 1; i <= no_settle; i++) {
			if(isNaN(parseFloat($("#amount_settle"+i).val()))) {

			}else{
				total += parseFloat($("#amount_settle"+i).val());
			}
		}

		$("#amount_settle").val(total);
	}

    function add_suspend() {

          getSuspendList();

          no_suspend++;
          var bodi = "";
          bodi += '<tr id="sus_'+no_suspend+'" class="detail_suspend">';	
          bodi += '<td><select class="form-control select4" data-placeholder="Suspense" name="suspend_'+no_suspend+'" id="suspend_'+no_suspend+'" style="width: 100% height: 35px;" onchange="pilihSuspend(this)" required=""></select></td>'
          bodi += '<td><input type="number" class="form-control" id="amount_suspend'+no_suspend+'" placeholder="Amount Suspense"></td>';
          bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no_suspend+')"><i class="fa fa-trash"></i></button></td>';
          bodi += '</tr>';
          $("#body_suspend").append(bodi);

          $('.select4').select2({
						allowClear:true,
						dropdownAutoWidth : true
					});
    }

    function settle() {

          no_settle++;
          var bodi = "";
          bodi += '<tr id="det_'+no_settle+'" class="detail_settle">';	
          bodi += '<td><input type="text" class="form-control" id="description_'+no_settle+'" style="width:100%"></td>'
          bodi += '<td><input type="number" class="form-control" id="amount_settle'+no_settle+'" style="width:100%" onkeyup="cek_amount_settle()" placeholder="Amount Settlement"></td>'
          bodi += '<td><input type="file" id="nota_'+no_settle+'" style="width:100%"></td>';
          bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item_detail('+no_settle+')"><i class="fa fa-trash"></i></button></td>';
          bodi += '</tr>';
          $("#body_settlement").append(bodi);
    }

	function pilihSuspend(elem)
	{
		var ele = elem.value.split("_");

		var suspend = ele[0];
		var detail = ele[1];

		$.ajax({
			url: "{{ url('fetch/suspend/pilih/user') }}?id_suspend="+suspend+"&detail="+detail,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				// $("#detail_"+no).attr('readonly', false).html(obj.item_desc);
				$('#amount_suspend'+no_suspend).attr('readonly', true).val(obj.amount);
				cek_amount_suspend();
			}
		});
	}

    function delete_item(no) {
          $("#sus_"+no).remove();
          no_suspend--;
          cek_amount_suspend();
    }

    function delete_item_detail(no) {
          $("#det_"+no).remove();
          no_settle--;
          cek_amount_settle();
    }

    function getSuspendList() {
		suspend_list = "";
		
		$.get('{{ url("fetch/suspend/list/user") }}', function(result, status, xhr) {
			suspend_list += "<option></option> ";
			$.each(result.suspend_user, function(index, value){
				suspend_list += "<option value='"+value.id+"_"+value.detail+"'>"+value.title+" - "+value.detail+"</option> ";
			});
			$('#suspend_'+no_suspend).append(suspend_list);
		})
	}

	function Save(id){	
		$('#loading').show();

		if(id == 'new'){

			if($("#submission_date").val() == "" || $('#title').val() == "" || $('#amount_settle').val() == ""){
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			
			formData.append('submission_date', $("#submission_date").val());
			formData.append('title', $("#title").val());
			formData.append('amount', $("#amount_settle").val());
			formData.append('emp_id', $("#emp_id").val());
			formData.append('emp_name', $("#emp_name").val());

			var settlement_detail = [];
			// var settlement_detail_file = [];
	    var suspend_detail = [];

			$('.detail_settle').each(function(index, value) {
				
        var idos = $(this).attr('id');
        var ido = idos.split("_");

      	if ($("#description_"+ido[1]).val() != "" && $("#amount_settle"+ido[1]).val() != "") {
           

           if ($('#nota_'+ido[1]).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == "") {
           		openErrorGritter('Error', "Jangan Lupa Nota Harus Diisi ");
							$('#loading').hide();
          	  return false;
           }
           else{
           	formData.append('nota_'+ido[1], $("#nota_"+ido[1]).prop('files')[0]);
						var file = $('#nota_'+ido[1]).val().replace(/C:\\fakepath\\/i, '').split(".");

	           settlement_detail.push({
	          	'description' : $("#description_"+ido[1]).val(), 
	          	'amount_settle' : $("#amount_settle"+ido[1]).val(),
							'extension' : file[1],
							'photo_name' : file[0]
	           });
           }

          

         } 
         else {
						openErrorGritter('Error', "Please Fill field with (*) sign");
						$('#loading').hide();
          	 return false;
          }
        });

				$('.detail_suspend').each(function(index, value) {
	        var idou = $(this).attr('id');
        	var ido = idou.split("_");

           	suspend_detail.push({
          		'suspend' : $("#suspend_"+ido[1]).val(), 
          		'amount_suspend' : $("#amount_suspend"+ido[1]).val()
           	});
	    	});

		  if (settlement_detail.length == 0){
		  	return false;
		  }
			
			var settlement_json = JSON.stringify(settlement_detail);
			var suspend_json = JSON.stringify(suspend_detail);

			formData.append('settlement_detail', settlement_json);
			formData.append('suspend_detail', suspend_json);

			$.ajax({
				url:"{{ url('create/settlement/user') }}",
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
						// fetchTable();
						location.reload();
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
		$('#currency').val('').trigger('change');
		$("#amount").val('');
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/settlement/user") }}', function(result, status, xhr){
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

					if (value.posisi == 'user') {
						listTableBody += '<td style="width:0.1%;"><span class="label label-danger">Not Sent To Admin</span></td>';
					}
					else{
						listTableBody += '<td style="width:0.1%;"><span class="label label-success">Received By Admin</span></td>';
					}

					if (value.posisi == "user")
					{
						listTableBody += '<td style="width:2%;"><center>  <a class="btn btn-md btn-warning" target="_blank" onclick="detail_settle(\''+value.id+'\')"><i class="fa fa-eye"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button></center></td>';
					}
					else{
						listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-warning" target="_blank" onclick="detail_settle(\''+value.id+'\')"><i class="fa fa-eye"></i> </a></center></td>';
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

	function detail_settle(id) {
		
		$('#loading').show();

		var data = {
			id:id
		}

		$.get('{{ url("fetch/settlement/user/detail") }}',data, function(result, status, xhr){
			if(result.status){

				$('#myModalLabel').html("Detail Settlement User");
				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();
				$('#bodyTableDetail').html("");

				var total_point = 0;
				var tableData = "";

				$.each(result.settlement, function(key, value) {
						tableData += '<tr>';
						tableData += '<td style="border:1px solid black;padding:2px">'+ parseInt(key+1) +'</td>';
						tableData += '<td style="border:1px solid black;padding:2px">'+ value.description +'</td>';
						tableData += '<td style="border:1px solid black;padding:2px">'+ value.amount_settle.toLocaleString('de-DE') +'</td>';
						tableData += '<td style="border:1px solid black;padding:2px"><a href="{{ url("files/cash_payment/settlement") }}/'+value.nota+'" target="_blank" class="fa fa-paperclip"></a></td>';
						if (value.sudah_settle == null) {
							tableData += '<td style="border:1px solid black;padding:2px;background-color:red;color:white">Open</td>';
						}else{
							tableData += '<td style="border:1px solid black;padding:2px;background-color:green;color:white">Close</td>';
						}
						tableData += '</tr>';
						key++;
				});
				$("#bodyTableDetail").append(tableData);

				var table = $('#tableDetail').DataTable({
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

				$('#loading').hide();

				$('#modalDetail').modal('show');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!','Failed Get Data');
			}
		});
   }

	function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim settlement ini ke Admin?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("email/settlement/user") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function formatMoney(n) {
	    return "$ " + (Math.round(n * 100) / 100).toLocaleString();
	}

    function fetchMonitoring(){
		var settlement = <?php echo json_encode($settlement) ?>;

		$('#setID').html("");
		var settleData = "";

		$.each(settlement, function(key, value){

			if (value.amount_belum_settle != 0) {

				settleData += '<div class="col-xs-12" style="padding:0">';
				settleData += '<div class="box box-widget widget-user-2" style="border: 1px solid black;margin:0">';
				settleData += '<div class="widget-user-header bg-purple" style="height: 140px;">';
				settleData += '<div class="widget-user-image crop2">';
				settleData += '<img src="{{ url('images/avatar/') }}'+'/'+value.emp_id+'.jpg'+'" alt="">';
				settleData += '<h3 class="widget-user-username">'+value.emp_name+'</h3>';
				settleData += '<h5 class="widget-user-desc">'+value.emp_id+'</h5>';
				settleData += '<h5 class="widget-user-username">'+value.title+'</h5>';
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
			}

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

