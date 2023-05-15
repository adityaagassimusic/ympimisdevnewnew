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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $activity_name }} - {{ $leader_dept }} <small><span class="text-purple">教育報告</span></small>
		<a href="{{ url('index/training_report/create/'.$id) }}" class="btn btn-primary pull-right"><b>Buat {{ $activity_alias }}</b></a>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="box-header">
							<h3 class="box-title">Filter {{ $activity_name }}</h3>
						</div>
						<form role="form" method="post" action="{{url('index/training_report/filter_training/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group">
									<label>Pilih Bulan</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date" name="date" autocomplete="off" placeholder="Pilih Bulan">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group pull-right">
									<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/training_report/index/'.$id) }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="form-group pull-right">
							
						</div>
					</div>
				  <div class="row">
				    <div class="col-xs-12">
				      
				        <div style="overflow-x: scroll;">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				              	<th>Judul Training</th>
				                <th>Department</th>
				                <th>Product</th>
				                <th>Periode</th>
				                <th>Date</th>
				                <th>Trainer</th>
				                <th>Send Status</th>
				                <th>Approval Status</th>
				                <th>Details</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              <?php if (ISSET($training_report)): ?>
				              	@if(count($training_report) != 0)
					              @foreach($training_report as $training_report)
					              <tr>
					              	<td>{{$training_report->training_title}}</td>
					                <td>{{$training_report->department_shortname}}</td>
					                <td>{{$training_report->product}}</td>
					                <td>{{$training_report->periode}}</td>
					                <td>{{$training_report->date}}</td>
					                <td>{{$training_report->trainer}}</td>
					                <td>@if($training_report->send_status == "")
					                		<label class="label label-danger">Belum Dikirim Email</label>
					                	@else
					                		<label class="label label-success">Terkirim</label>
					                	@endif
					        		</td>
					                <td>@if($training_report->approval == "")
					                		<label class="label label-danger">Not Approved</label>
					                	@else
					                		<label class="label label-success">Approved</label>
					                	@endif</td>
					                <td>
					                  <center>
					                    <a class="btn btn-primary btn-sm" href="{{url('index/training_report/details/'.$training_report->id.'/view')}}">Isi Peserta</a>
					                    <a class="btn btn-success btn-sm" href="{{url('index/training_report/print/'.$training_report->id)}}" target="_blank">Cetak</a>
					                    @if($training_report->send_status == "")
					                		<a class="btn btn-info btn-sm" href="{{url('index/training_report/sendemail/'.$training_report->id)}}">Kirim Email</a>
					                	@endif
					                  </center>
					                </td>
					                <td>
					                  <center>
					                    <!-- <a class="btn btn-info btn-sm" href="{{url('index/training_report/show/'.$id.'/'.$training_report->id)}}">View</a> -->
					                    <a href="{{url('index/training_report/edit/'.$id.'/'.$training_report->id)}}" class="btn btn-warning btn-sm">Edit</a>
					                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/training_report/destroy") }}', '{{ $training_report->activity_lists->activity_name }} - {{ $training_report->product }} - {{ $training_report->date }}','{{ $id }}', '{{ $training_report->id }}');">
					                      Delete
					                    </a>
					                  </center>
					                </td>
					              </tr>
					              @endforeach
					              @endif
				              <?php endif ?>
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
	</div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <div class="modal-body">
          Are you sure delete?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
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
		$('#date').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#date2').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('body').toggleClass("sidebar-collapse");
	});

	
</script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
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
    function deleteConfirmation(url, name, audit_id,id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+audit_id+'/'+id);
    }
  </script>
@endsection