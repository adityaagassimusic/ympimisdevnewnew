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
	#listTableBodyOutstanding > tr:hover {
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
		<li><a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;{{ $title }}</a></li>
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
							<span style="font-size: 25px;color: black;width: 25%;">SUSPENSE PAYMENT</span>
							<span style="font-size: 25px;color: black;width: 25%;">サスペンス支払い</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
	    	<div class="col-md-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
	    		<div id="chart2" style="width: 100%"></div>
	    	</div>
	    </div>
	</div>
	<div class="box">
		<div class="box-body">
			<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
	    	<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Date</th>
						<th>Admin</th>
						<th>Purpose</th>
						<!-- <th>Category</th> -->
						<th>Amount</th>
						<th>Document</th>
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
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;color:white" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">List Outstanding Suspense</span>
							<span style="font-size: 25px;color: black;width: 25%;">未払いサスペンス</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6" style="padding:2px">
		<div class="box">
			<div class="box-body">
				<table id="listTableOutstanding" class="table table-bordered table-striped table-hover">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th>#</th>
							<th>Date</th>
							<th>Title</th>
							<th>Amount</th>
							<th>Document</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="listTableBodyOutstanding">
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-6" style="padding:2px">
			<div class="box">
				<div class="box-body">
					<table id="detailTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>#</th>
								<th>Employee Name</th>
								<th>Amount</th>
								<!-- <th>Upload Nota</th> -->
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="detailTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>

</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
					<div class="row">
						<input type="hidden" id="id_edit">
						<div class="col-md-6">
							<div class="col-md-12" style="margin-bottom: 5px;">
								<label for="submission_date" class="col-sm-3 control-label">Req. Date<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<div class="input-group date">
										<div class="input-group-addon">	
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
										<input type="hidden" class="form-control pull-right"  value="{{date('Y-m-d')}}" id="submission_date" name="submission_date">
									</div>
								</div>
							</div>

							<div class="col-md-12" style="margin-bottom: 5px">
								<label for="title" class="col-sm-3 control-label">Requested By<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
									<input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}">
									<input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
									<input type="hidden" id="department" name="department" value="{{$employee->department}}">
								</div>
							</div>												

							<div class="col-md-12" style="margin-bottom: 5px">
								<label for="title" class="col-sm-3 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="title" name="title" placeholder="Reason or Purpose">
								</div>
							</div>


							<!-- <div class="col-md-12" style="margin-bottom: 5px">
								<label for="category" class="col-sm-3 control-label">Category<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control select4" id="category" name="category" data-placeholder='Category' style="width: 100%" onchange="changecategory(this.value)">
										<option value="">&nbsp;</option>
										<option value="Regular">Regular Payment</option>
										<option value="Irregular">Irregular Payment</option>
										<option value="Urgent">Urgent Payment</option>
									</select>
								</div>
							</div> -->

							<div class="col-md-12" style="margin-bottom: 5px">
								<label for="Currency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<select class="form-control select2" id="currency" name="currency" data-placeholder='Currency' style="width: 100%">
										<option value="">&nbsp;</option>
										<option value="USD">USD</option>
										<option value="IDR">IDR</option>
										<option value="JPY">JPY</option>
									</select>
								</div>
							</div>

							<div class="col-md-12" style="margin-bottom: 5px;">
								<label for="amount" class="col-sm-3 control-label">Amount<span class="text-red">*</span></label>
								<div class="col-sm-9">
									<input type="number" class="form-control" id="amount" name="amount" placeholder="Total Amount" readonly="">
								</div>
							</div>

							<!-- <div class="col-md-12" style="margin-bottom: 5px">
								<label for="created_for" class="col-sm-3 control-label">Created For<span class="text-red">*</span></label>
							</div> -->

							<div class="col-md-12" style="margin-bottom: 5px">
								<label for="file" class="col-sm-3 control-label">File Attachment</label>
								<div class="col-sm-9">
									<input type="file" id="file_attach" name="file_attach">
								</div>
							</div>

						</div>

						<div class="col-xs-6" id="regular">
	                       <button class="btn btn-sm btn-success pull-right" onclick="add_amount_regular()"><i class="fa fa-plus"></i>&nbsp; Add</button>

	                       <table class="table">
	                            <thead>
	                                 <tr>
	                                      <th style="width: 60%">Note<span class="text-red">*</span></th>
	                                      <th style="width: 40%">Amount<span class="text-red">*</span></th>
	                                      <th>#</th>
	                                 </tr>
	                            </thead>
	                            <tbody id="body_add_regular">
	                            </tbody>
	                       </table>
	                  	</div>

	                  	<div class="col-xs-6" id="irregular">
	                       <button class="btn btn-sm btn-success pull-right" onclick="add_amount_irregular()"><i class="fa fa-plus"></i>&nbsp; Add</button>

	                       <table class="table">
	                            <thead>
	                                 <tr>
	                                      <th style="width: 30%">No PR<span class="text-red">*</span></th>
	                                      <th style="width: 30%">No Item<span class="text-red">*</span></th>
	                                      <th style="width: 30%">Amount<span class="text-red">*</span></th>
	                                      <th>#</th>
	                                 </tr>
	                            </thead>
	                            <tbody id="body_add_irregular">
	                            </tbody>
	                       </table>
	                  	</div>

					</div>

					<div class="col-md-12">
						<a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
					</div>

					<!-- <div class="col-md-12" style="margin-top:20px">
						<div class="box box-primary" style="border-top-color: orange" id="noteimportant">
					      <div class="callout callout" style="background-color: #fbc02d;border-left: 0;color: black">
					        <h4><i class="fa fa-bullhorn"></i> Catatan!</h4>
					        <p>
					          <b>Regular Payment</b> merupakan Pembayaran Regular
					        </p>
					        <p><b>Irregular Payment</b> merupakan Pembayaran Irregular
					        </p>
					        <p><b>Urgent Payment</b> merupakan Pembayaran Urgent
					        </p>
					    </div>
					  </div>
					</div> -->
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
							<th style="width: 5%">No PR</th>
							<th style="width: 10%">Detail</th>
							<th style="width: 5%">Harga</th>
							<th style="width: 5%">Status</th>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


    var no = 0;

	jQuery(document).ready(function() {

    	$('body').toggleClass("sidebar-collapse");
		fetchTable();
		fetchTableOustanding();
		$('#regular').hide();
		$('#irregular').show();
	    $("#body_add_irregular").html("");
		drawChart();
	});

	// function changecategory(elem){
	// 	if (elem == "Regular" || elem == "Urgent") {
	// 		$('#regular').show();
	// 		$('#irregular').hide();
	//         $("#body_add_regular").html("");
	// 	} else if(elem == "Irregular"){
	// 		$('#regular').hide();
	// 		$('#irregular').show();
	//         $("#body_add_irregular").html("");
	// 	}
	// }

	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});


	// $("#amount").change(function(){
	// 	var output = parseFloat($('#amount').val()); 
	// 	var output2 = output.toLocaleString(undefined,{'minimumFractionDigits':2,'maximumFractionDigits':2});
	// 	$('#amount').val(output2);
 //  	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function newData(id){

		if(id == 'new'){
			$('#modalNewTitle').text('Create Suspense Payment');
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

			$.get('{{ url("detail/suspend") }}', data, function(result, status, xhr){
				if(result.status){

					$('#category').html('');
					$('#currency').html('');

					var category = "";
					var currency = "";

					$('#submission_date').val(result.suspend.submission_date);

					// if(result.suspend.category == "Regular"){
					// 	category += '<option value="Regular" selected>Regular Payment</option>';
					// 	category += '<option value="Irregular">Irregular Payment</option>';
					// 	category += '<option value="Urgent">Urgent Payment</option>';
					// }
					// else if (result.suspend.category == "Irregular"){
					// 	category += '<option value="Regular">Regular Payment</option>';
					// 	category += '<option value="Irregular" selected>Irregular Payment</option>';
					// 	category += '<option value="Urgent">Urgent Payment</option>';
					// }
					// else if(result.suspend.category == "Urgent"){
					// 	category += '<option value="Regular">Regular Payment</option>';
					// 	category += '<option value="Irregular">Irregular Payment</option>';
					// 	category += '<option value="Urgent" selected>Urgent Payment</option>';
					// }

					// $('#category').append(category);

					if(result.suspend.currency == "USD"){
						currency += '<option value="USD" selected>USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.suspend.currency == "IDR"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR" selected>IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.suspend.currency == "JPY"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY" selected>JPY</option>';
					}

					// if (result.suspend.category == "Regular" || result.suspend.category == "Urgent") {
					// 	$('#regular').show();
					// 	$('#irregular').hide();
				 //        $("#body_add_regular").html("");
					// } else if(result.suspend.category == "Irregular"){
					// 	$('#regular').hide();
					// 	$('#irregular').show();
				 //        $("#body_add_irregular").html("");
					// }

					// bodi = "";


					// $.each(result.items, function(key, value) {
					
				 //        bodi += '<tr id="'+(key+1)+'" class="item_regular">';
				 //        bodi += '<td><input type="text" class="form-control" placeholder="Payment Information" id="remark_'+(key+1)+'" value="'+value.detail+'"></td>'
				 //        bodi += '<td><input type="number" class="form-control" id="amount_'+(key+1)+'" onkeyup="cek_amount()" value='+value.amount+'></td>';
				 //        bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+(key+1)+')"><i class="fa fa-trash"></i></button></td>';
				 //        bodi += '</tr>';
					
					// });

				 //    $("#body_add_regular").append(bodi);

					$('#currency').append(currency);
					$('#amount').val(result.suspend.amount);
					$('#title').val(result.suspend.title);
					$('#id_edit').val(result.suspend.id);

					$('#modalNewTitle').text('Update Suspense Payment');
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

	function Save(id){	
		$('#loading').show();

		if(id == 'new'){
			if($("#submission_date").val() == "" || $('#title').val() == "" || $('#currency').val() == "" || $('#amount').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			
			formData.append('submission_date', $("#submission_date").val());
			formData.append('emp_id', $("#emp_id").val());
			formData.append('emp_name', $("#emp_name").val());
			formData.append('department', $("#department").val());
			formData.append('title', $("#title").val());
			// formData.append('category', $("#category").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('file_attach', $('#file_attach').prop('files')[0]);

			var amount_detail = [];

			// if ($("#category").val() == "Regular" || $("#category").val() == "Urgent") {
	
			//        $('.item_regular').each(function(index, value) {
			//            var ido = $(this).attr('id');

			//            if ($("#remark_"+ido).val() != "" && $("#amount_"+ido).val() != "") {
			//                amount_detail.push({ 
			//                	'remark' : $("#remark_"+ido).val(), 
			//                	'amount' : $("#amount_"+ido).val()
			//                });
			//            } else {
			// 			openErrorGritter('Error', "Please Fill field with (*) sign");
			// 			$('#loading').hide();
			//            	return false;
			//            }
			//        });
			// }else{

			// }

			$('.item_irregular').each(function(index, value) {
	            var ido = $(this).attr('id');

	            if ($("#no_pr_"+ido).val() != "" && $("#detail_"+ido).val() != "" && $("#amount_"+ido).val() != "") {
	                amount_detail.push({
	                	'no_pr' : $("#no_pr_"+ido).val(), 
	                	'detail' : $("#detail_"+ido).val(),
	                	'amount' : $("#amount_"+ido).val()
	                });
	            }
	            else {
					openErrorGritter('Error', "Please Fill field with (*) sign");
					$('#loading').hide();
	            	return false;
	            }
	        });
			
			var amount_json = JSON.stringify(amount_detail);
			
			formData.append('amount_detail', amount_json);

			$.ajax({
				url:"{{ url('create/suspend') }}",
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
			// formData.append('category', $("#category").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('file_attach', $("#file_attach").prop('files')[0]);

			$.ajax({
				url:"{{ url('edit/suspend') }}",
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
		// $("#category").val('').trigger('change');
		$('#currency').val('').trigger('change');
		$("#amount").val('');
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
		$.get('{{ url("fetch/suspend") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.suspend, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.submission_date))+'</td>';
					listTableBody += '<td style="width:5%;">'+value.created_name+'</td>';
					listTableBody += '<td style="width:5%;">'+value.title+'</td>';
					// listTableBody += '<td style="width:1%;">'+value.category+'</td>';
					listTableBody += '<td style="width:1%;text-align:right">'+value.currency+' '+value.amount.toLocaleString()+'</td>';

					if (value.file != null) {
						listTableBody += '<td style="width:0.1%;"><a target="_blank" href="{{ url("files/cash_payment/suspend") }}/'+value.file+'"><i class="fa fa-paperclip"></i></td>';
					}
					else{
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;"> - </td>';
					}

					if (value.posisi == 'user') {
						listTableBody += '<td style="width:0.1%;"><span class="label label-danger">Not Sent</span></td>';
					}
					else if (value.posisi == 'manager'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Manager User</span></td>';
					}
					else if (value.posisi == 'staff_acc'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Staff Accounting</span></td>';
					}
					else if (value.posisi == 'manager_acc'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Manager Accounting</span></td>';
					}
					else if (value.posisi == 'direktur'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Direktur</span></td>';
					}
					else if (value.posisi == 'acc'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-success">Received By Accounting</span></td>';
					}

					if (value.posisi == "user")
					{
						listTableBody += '<td style="width:2%;"><center><button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button>  <a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button></center></td>';
					}

					else{
						listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';
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

      if (!confirm("Apakah anda yakin ingin mengirim Suspense Payment ini ke Manager?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("email/suspend") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    // function add_amount_regular() {
    //       no++;
    //       var bodi = "";
    //       bodi += '<tr id="'+no+'" class="item_regular">';
    //       bodi += '<td><input type="text" class="form-control" placeholder="Payment Information" id="remark_'+no+'"></td>'
    //       bodi += '<td><input type="number" class="form-control" id="amount_'+no+'" onkeyup="cek_amount()" placeholder="Price"></td>';
    //       bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';
    //       bodi += '</tr>';
    //       $("#body_add_regular").append(bodi);
    // }

    function add_amount_irregular() {

          getPRList();

          no++;
          var bodi = "";
          bodi += '<tr id="'+no+'" class="item_irregular">';	
          bodi += '<td><select class="form-control select4" data-placeholder="PR" name="no_pr_'+no+'" id="no_pr_'+no+'" style="width: 100% height: 35px;" onchange="pilihPR(this)" required=""></select></td>'
          bodi += '<td><select class="form-control select4" data-placeholder="Detail Item" id="detail_'+no+'" style="width: 100% height: 35px;" onchange="pilihItem(this)" required=""></select></td>'
          bodi += '<td><input type="number" class="form-control" id="amount_'+no+'"></td>';
          bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';
          bodi += '</tr>';
          $("#body_add_irregular").append(bodi);

	          
		$(function () {
			$('.select4').select2({
				allowClear:true,
				dropdownAutoWidth : true,
		        dropdownParent: $('#modalNew')
			});
		})

    }

    function pilihPR(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ url('fetch/suspend/pilih_pr') }}?no_pr="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$("#detail_"+no).html(obj);
				$('#amount_'+no).attr('readonly', true).val("");
			} 
		});
	}

	function pilihItem(elem)
	{
		var no_pr = $("#no_pr_"+no).val();

		$.ajax({
			url: "{{ url('fetch/suspend/get_price') }}?item_desc="+elem.value+"&no_pr="+no_pr,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#amount_'+no).attr('readonly', true).val(obj.item_price);
     			cek_amount();
			}
		});

	}

    function delete_item(no) {
    	$("#"+no).remove();
    	$("#amount_"+no).val("");
     	no--;
     	cek_amount();
    }

    function getPRList() {
		pr_list = "";
		$.get('{{ url("fetch/purchase_order/prlist") }}', function(result, status, xhr) {
			pr_list += "<option></option> ";
			$.each(result.pr, function(index, value){
				pr_list += "<option value="+value.no_pr+">"+value.no_pr+"</option> ";
			});
			$('#no_pr_'+no).append(pr_list);
		})
	}

	function cek_amount(){
		var total = 0;

		// console.log(parseFloat($("#amount_"+i).val()));

		for (var i = 1; i <= no; i++) {
			if(isNaN(parseFloat($("#amount_"+i).val()))) {

			}else{
				total += parseFloat($("#amount_"+i).val());
			}
		}

		$("#amount").val(total);
	}

	function drawChart() {
	$.get('{{ url("fetch/suspend/monitoring") }}', function(result, status, xhr) {
		if(xhr.status == 200){
			if(result.status){
				var bulan = [], jumlah = [];

				$.each(result.datas, function(key, value) {
					bulan.push(value.bulan);
					jumlah.push(parseInt(value.jumlah));
				});

				var date = new Date();

				$('#chart2').highcharts({
					chart: {
						type: 'column',
						height : '250px'
					},
					title: {
						text: ''
					},
					credits : {
						enabled:false
					},
					xAxis: {
						type: 'category',
						categories: bulan
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Total Data Per Bulan'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
                        color: ( 
                        	Highcharts.defaultOptions.title.style &&
                        	Highcharts.defaultOptions.title.style.color
                        	) || 'gray'
                      }
                    },
                  	tickInterval: 1
                  },

                  legend: {
                  	align: 'right',
                  	x: -30,
                  	verticalAlign: 'top',
                  	y: 25,
                  	floating: true,
                  	backgroundColor:
                  	Highcharts.defaultOptions.legend.backgroundColor || 'white',
                  	borderColor: '#CCC',
                  	borderWidth: 1,
                  	shadow: false,
                  	enabled:false
                  },
                  tooltip: {
                  	headerFormat: '<b>{point.x}</b><br/>',
                  	pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                  },
                  plotOptions: {
                  	column: {
                  		stacking: 'normal',
                  		dataLabels: {
                  			enabled: true
                  		}
                  	}
                  },
                  series: [{
                  	name: 'Jumlah',
                  	data: jumlah,
                  	color: '#82e0aa'
                  }]
                })
			} else{
				alert('Attempt to retrieve data failed');
			}
		}
	})
}


function fetchTableOustanding(){
		$('#loading').show();
		$.get('{{ url("fetch/suspend/control") }}', function(result, status, xhr){
			if(result.status){
				$('#listTableOustanding').DataTable().clear();
				$('#listTableOustanding').DataTable().destroy();				
				$('#listTableBodyOustanding').html("");
				var listTableBodyOutstanding = "";

				$.each(result.suspend, function(key, value){
					listTableBodyOutstanding += '<tr>';
					listTableBodyOutstanding += '<td onclick="detail(\''+value.id+'\')" style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBodyOutstanding += '<td onclick="detail(\''+value.id+'\')" style="width:1%;">'+value.submission_date+'</td>';
					listTableBodyOutstanding += '<td onclick="detail(\''+value.id+'\')" style="width:3%;">'+value.title+'</td>';
					listTableBodyOutstanding += '<td onclick="detail(\''+value.id+'\')" style="width:2%;text-align:right">'+value.currency+' '+value.amount.toLocaleString()+'</td>';

					if (value.file != null) {
						listTableBodyOutstanding += '<td onclick="detail(\''+value.id+'\')" style="width:0.1%;"><a target="_blank" href="{{ url("files/cash_payment/suspend") }}/'+value.file+'"><i class="fa fa-paperclip"></i></td>';
					}
					else{
						listTableBodyOutstanding += '<td onclick="detail(\''+value.id+'\')" style="width:0.1%;"> - </td>';
					}

					listTableBodyOutstanding += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';

					listTableBodyOutstanding += '</tr>';

				});

				$('#listTableBodyOutstanding').append(listTableBodyOutstanding);

				$('#listTableOutstanding tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTableOutstanding').DataTable({
					'responsive':true,
					'paging': false,
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

				$('#listTableOutstanding tfoot tr').appendTo('#listTableOutstanding thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

	function detail(id){

		var data = {
			id:id
		}

		$.get('{{ url("fetch/suspend/control/detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#detailTable').DataTable().clear();
				$('#detailTable').DataTable().destroy();				
				$('#detailTableBody').html("");
				var detailTableBody = "";

				$('#judul_suspend').text("List Detail Suspense "+result.suspend[0].title);

				$.each(result.suspend, function(key, value){
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					detailTableBody += '<td style="width:2%;">'+value.emp_name+'</td>';
					detailTableBody += '<td style="width:2%;text-align:right">'+value.item_currency+' '+value.amount.toLocaleString()+'</td>';
					// detailTableBody += '<td style="width:1%;"><input type="file" id="file_attach" name="file_attach"></td>';
					if (value.received_at == null) {
						detailTableBody += '<td style="width:1%;"><button class="btn btn-md btn-warning" data-toggle="tooltip" title="Detail Suspend" style="margin-right:5px;" onclick="detail_suspend(\''+value.id+'\',\''+value.emp_name+'\')"><i class="fa fa-eye"></i></button> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Give Money To Employee" style="margin-right:5px;" onclick="sendEmailMoney(\''+value.id+'\',\''+value.emp_name+'\',\''+value.emp_id+'\')"> <i class="fa fa-check-square-o"></i> <i class="fa fa-user"></i></button></td>';
					}else{

						detailTableBody += '<td style="width:1%;"><button class="btn btn-md btn-warning" data-toggle="tooltip" title="Detail Suspend" style="margin-right:5px;" onclick="detail_suspend(\''+value.id+'\',\''+value.emp_name+'\')"><i class="fa fa-eye"></i></button> <span class="label label-success">Sudah Diterima User</span></td>';
					}
					detailTableBody += '</tr>';
				});

				$('#detailTableBody').append(detailTableBody);

				$('#detailTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#detailTable').DataTable({
					'responsive':true,
					'paging': false,
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

	function detail_suspend(id, name) {
		$('#loading').show();
		var data = {
			id:id,
			name:name
		}

		$.get('{{ url("fetch/suspend/report/detail") }}',data, function(result, status, xhr){
			if(result.status){
				$('#myModalLabel').html("Detail Pembayaran "+name);

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();
				$('#bodyTableDetail').html("");

				var total_point = 0;
				var tableData = "";

				$.each(result.suspend, function(key, value) {
						tableData += '<tr>';
						tableData += '<td  style="width: 1%;border:1px solid black;padding:2px">'+ parseInt(key+1) +'</td>';
						tableData += '<td  style="width: 3%;;border:1px solid black;padding:2px">'+ value.no_pr +'</td>';
						tableData += '<td style="width: 10%;border:1px solid black;padding:2px">'+ value.detail +'</td>';
						tableData += '<td style="width: 5%;text-align:center;border:1px solid black;padding:2px">'+ value.item_currency +' '+ (value.amount/1000).toFixed(3) +'</td>';
						if (value.settle == null) {
							tableData += '<td style="width: 3%;border:1px solid black;padding:2px;background-color:red;color:white">Open</td>';
						}else{
							tableData += '<td style="width: 3%;border:1px solid black;padding:2px;background-color:green;color:white">Close</td>';
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

   function sendEmailMoney(id,name,emp_id) {
      var data = {
        id:id,
		name:name,
		emp_id:emp_id
      };

      if (!confirm("Apakah Anda Memberi Uang Cash Kepada " +name+ " ?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("give/suspend") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Uang Berhasil Diterima");
      	$("#loading").hide();
      	detail(id);
      })
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

