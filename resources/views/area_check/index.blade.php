@extends('layouts.master')
@section('stylesheets')
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: -20px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}

#tableCheck > tbody > tr > td > p > img {
	width: 200px !important;
}

.content-wrapper{
	padding-top: 0px !important;
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $activity_name }} - {{ $leader }}
		<a href="{{ url('index/area_check_point/index/'.$id) }}" class="btn btn-primary pull-right">Point Check</a>&nbsp;
		<button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#create-modal" style="margin-right: 5px;">
	        Buat Audit
	    </button>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter {{ $activity_name }}</h3>
						</div>
						<form role="form" method="post" action="{{url('index/area_check/filter_area_check/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Pilih Bulan</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="tgl"name="month" placeholder="Pilih Bulan" autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Lokasi</label>
										<select class="form-control select2" name="location" id="location" style="width: 100%;" data-placeholder="Pilih Lokasi" required>
											<option value=""></option>
											@foreach($area_code as $area_code)
												<option value="{{$area_code->area}}">{{$area_code->area}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/area_check/index/'.$id) }}" class="btn btn-danger">Clear</a>
										<button type="submit" class="btn btn-primary col-sm-14">Search</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak {{ $activity_name }}</h3>
						</div>
						<!-- <form target="_blank" role="form" method="post" action="{{url('index/area_check/print_area_check/'.$id)}}"> -->
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Pilih Bulan</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl_print" name="month" placeholder="Pilih Bulan" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Lokasi</label>
										<select class="form-control select2" name="location_print" id="location_print" style="width: 100%;" data-placeholder="Pilih Lokasi" required>
											<option value=""></option>
											@foreach($area_code2 as $area_code2)
												<option value="{{$area_code2->area}}">{{$area_code2->area}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<button onclick="print_pdf('{{$id}}',$('#tgl_print').val())" class="btn btn-primary col-sm-14">Print</button>
									</div>
								</div>
							</div>
						<!-- </form> -->
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Kirim Email ke Foreman</h3>
						</div>
						<form role="form" method="post" action="{{url('index/area_check/sendemail/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<label>Pilih Bulan</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl" name="month" placeholder="Pilih Tanggal" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<button type="submit" class="btn btn-primary col-sm-14">Kirim Email</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<table id="example1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Group</th>
										<th>Location</th>
										<th>Point Check</th>
										<th>Date</th>
										<th>Condition</th>
										<th>PIC</th>
										<th>Image</th>
										<th>Send Status</th>
										<th>Approval Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@if(count($area_check) != 0)
									@foreach($area_check as $area_check)
									<tr>
										<td>{{$area_check->subsection}}</td>
										<td>{{$area_check->location}}</td>
										<td>{{$area_check->point_check}}</td>
										<td>{{$area_check->date}}</td>
										<td>{{$area_check->condition}}</td>
										<td>{{$area_check->pic}}</td>
										<td><img width="100px" src="{{url('data_file/cek_area/'.$area_check->image_evidence)}}" alt="your image" /></td>
										<td>
											@if($area_check->send_status == "")
						                		<label class="label label-danger">Not Yet Sent</label>
						                	@else
						                		<label class="label label-success">Sent</label>
						                	@endif
										</td>
										<td>@if($area_check->approval == "")
						                		<label class="label label-danger">Not Approved</label>
						                	@else
						                		<label class="label label-success">Approved</label>
						                	@endif</td>
										<td>
											<center>
												{{-- <a class="btn btn-info btn-sm" href="{{url('index/area_check/show/'.$id.'/'.$area_check->id_area)}}">View</a> --}}
												{{-- <a href="{{url('index/daily_check_fg/edit/'.$id.'/'.$daily_check->id_area)}}" class="btn btn-warning btn-xs">Edit</a> --}}
												<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_area_check('{{ url("index/area_check/update") }}','{{ $area_check->id_area }}');">
									               Edit
									            </button>
												<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/area_check/destroy") }}', '{{ $area_check->point_check }} - {{ $area_check->date }}','{{ $id }}', '{{ $area_check->id_area }}');">
													Delete
												</a>
											</center>
										</td>
									</tr>
									@endforeach
									@endif
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
										<th></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
			</div>
			<div class="modal-body delete-body">
				Are you sure delete?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Cancel</button>
				<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: orange;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <center>
        	<h4 class="modal-title"><b>Buat Audit</b></h4>
        </center>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="department" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Group</label>
	                <select class="form-control select2" name="inputsubsection" id="inputsubsection" style="width: 100%;" data-placeholder="Pilih Group..." required>
	                  <option value=""></option>
	                  @foreach($subsection as $subsection)
	                  <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <!-- <input type="text" name="inputdate" id="inputdate" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title=""> -->
				  <input type="text" name="inputdate" id="inputdate" class="form-control" value="{{ date('Y-m-d') }}" placeholder="Pilih Tanggal" required="required" title="">
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
            		<?php $no = 1 ?>
	              <label>Point Check</label>
	                <select class="form-control select2" name="inputpoint_check" id="inputpoint_check" style="width: 100%;" data-placeholder="Pilih Point Check..." required>
	                	<option value=""></option>
	                  @foreach($point_check as $point_check)
	                    <option value="{{ $point_check->id }}">{{ $no }}. {{ $point_check->location }} - {{ $point_check->point_check }}</option>
	                    <?php $no++ ?>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Condition</label>
	              <div class="radio" style="margin-left: 20px;">
					<label class="containers">&#9711;
					  <input type="radio" name="condition" id="inputcondition" value="OK">
					  <span class="checkmark"></span>
					</label>
				  </div>
				  <div class="radio" style="margin-left: 20px;">
					<label class="containers">&#9747;
					  <input type="radio" name="condition" id="inputcondition" value="NG">
					  <span class="checkmark"></span>
					</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label>PIC</label>
	                <select class="form-control select2" name="inputpic" id="inputpic" style="width: 100%;" data-placeholder="Pilih PIC..." required>
	                  <option value=""></option>
	                  @foreach($pic as $pic)
	                    <option value="{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
	                  @endforeach
	                </select>
	            </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<div class="form-group">
	              <label for="">Image Evidence</label>
	              <input type="file" name="inputfile" accept="image/*" capture="environment" id="inputfile" onchange="readURL(this);">
	              <br>
	              <img width="100px" id="inputfileimage" src="" style="display: none" alt="your image" />
	            </div>
            </div>
		      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding">
		      	<div class="modal-footer">
		          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
		          <input type="submit" value="Submit" onclick="create()" class="btn btn-primary">
		        </div>
		      </div>
          </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: lightgreen">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <center>
        	<h4 class="modal-title" align="center"><b>Edit Audit</b></h4>
        </center>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
	              <input type="hidden" name="url" id="url_edit" class="form-control" value="">
				  <input type="text" name="department" id="editdepartment" class="form-control" value="" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Group</label>
	                <select class="form-control select2" name="editsubsection" id="editsubsection" style="width: 100%;" data-placeholder="Pilih Group..." required>
	                  <option value=""></option>
	                  @foreach($subsection2 as $subsection2)
	                  <option value="{{ $subsection2->sub_section_name }}">{{ $subsection2->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="editdate" id="editdate" class="form-control" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label>Point Check</label>
	              <?php $no = 1 ?>
	                <select class="form-control select2" name="editpoint_check" id="editpoint_check" style="width: 100%;" data-placeholder="Pilih Point Check..." required>
	                  <option value=""></option>
	                  @foreach($point_check2 as $point_check2)
	                    <option value="{{ $point_check2->id }}">{{ $no }}. {{ $point_check2->location }} - {{ $point_check2->point_check }}</option>
	                    <?php $no++ ?>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Condition</label>
				  <div class="radio" style="margin-left: 20px;">
					<label class="containers">&#9711;
					  <input type="radio" name="condition" id="editcondition" value="OK">
					  <span class="checkmark"></span>
					</label>
				  </div>
				  <div class="radio" style="margin-left: 20px;">
					<label class="containers">&#9747;
					  <input type="radio" name="condition" id="editcondition" value="NG">
					  <span class="checkmark"></span>
					</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label>PIC</label>
	                <select class="form-control select2" name="editpic" id="editpic" style="width: 100%;" data-placeholder="Pilih PIC..." required>
	                  <option value=""></option>
	                  @foreach($pic2 as $pic2)
	                    <option value="{{ $pic2->name }}">{{ $pic2->employee_id }} - {{ $pic2->name }}</option>
	                  @endforeach
	                </select>
	            </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<div class="form-group">
	              <label for="">Image Evidence</label>
	              <input type="file" name="editfile" accept="image/*" capture="environment" id="editfile" onchange="readURLEdit(this);">
	              <br>
	              <img width="100px" id="editfileimage" src="" style="display: none" alt="your image" />
	            </div>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding">
          	<div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
	            <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
	        </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#inputfileimage').hide();
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
	});

	function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#inputfileimage').show();
              $('#inputfileimage')
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

    function readURLEdit(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#editfileimage').show();
              $('#editfileimage')
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		

	});

	$('.datepicker2').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});

	$('#inputdate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
    
    $('#editProductionDate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

	
</script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<!-- <script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script> -->
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
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
	jQuery(document).ready(function() {
		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );
		var table = $('#example1').DataTable({
			"order": [],
			'dom': 'Bfrtip',
			'responsive': true,
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
			}
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

	});
	$(function () {

		$('#example2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : true,
			'info'        : true,
			'autoWidth'   : false
		})
	})
	function deleteConfirmation(url, name,id,area_check_id) {
		jQuery('.delete-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+area_check_id);
	}

	function create(){
		$('#loading').show();
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#inputdepartment').val();
		var subsection = $('#inputsubsection').val();
		var point_check = $('#inputpoint_check').val();
		var date = $('#inputdate').val();
		var condition = $('input[id="inputcondition"]:checked').val();
		var pic = $('#inputpic').val();

		if ($('#inputfile').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Masukkan File');
			audio_error.play();
			return false;
		}

		var formData = new FormData();
		var newAttachment  = $('#inputfile').prop('files')[0];
		var file = $('#inputfile').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('leader',leader);
		formData.append('foreman',foreman);
		formData.append('department',department);
		formData.append('subsection',subsection);
		formData.append('point_check',point_check);
		formData.append('pic',pic);
		formData.append('date',date);
		formData.append('condition',condition);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('index/area_check/store/'.$id) }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					$('#inputfile').val("");
					$('#inputfileimage').hide();
					openSuccessGritter('Success','Input Audit Berhasil');
					window.location.reload();
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function edit_area_check(url,id) {
    	$.ajax({
                url: "{{ route('area_check.getareacheck') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var data = data.data;
                  $("#url_edit").val(url+'/'+id);
                  $("#editdepartment").val(data.department);
                  $("#editsubsection").val(data.subsection).trigger('change.select2');
                  $("#editpoint_check").val(data.area_check_point_id).trigger('change.select2');
                  $("#editdate").val(data.date);
                  $('input[id="editcondition"][value="'+data.condition+'"]').prop('checked',true);
                  $("#editpic").val(data.pic).trigger('change.select2');
                  var url_iamge = '{{url("data_file/cek_area")}}'+'/'+data.image_evidence;
                  document.getElementById("editfileimage").src=url_iamge;
                  $('#editfileimage').show()
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){

    	$('#loading').show();
		var department = $('#editdepartment').val();
		var subsection = $('#editsubsection').val();
		var area_check_point_id = $('#editpoint_check').val();
		var date = $('#editdate').val();
		var condition = $('input[id="editcondition"]:checked').val();
		var pic = $('#editpic').val();
		var url = $('#url_edit').val();

		var formData = new FormData();
		var newAttachment  = $('#editfile').prop('files')[0];
		var file = $('#editfile').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('department',department);
		formData.append('subsection',subsection);
		formData.append('area_check_point_id',area_check_point_id);
		formData.append('pic',pic);
		formData.append('date',date);
		formData.append('condition',condition);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:url,
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#editfile').val("");
					$('#editfileimage').hide();
					openSuccessGritter('Success','Update Audit Berhasil');
					window.location.reload();
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function print_pdf(id,month) {
		if (month == "" || $('#location_print').val() == "") {
			alert('Masukkan Bulan & Lokasi');
		}else{
			var url = "{{url('index/area_check/print_area_check/')}}";
			// console.log(url + '/' + id+ '/' + month);
			window.open(url + '/' + id+ '/' + month + '/' +$('#location_print').val(),"_blank");
		}
	}
</script>
@endsection