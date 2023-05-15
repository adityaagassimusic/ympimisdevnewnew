
@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Edit {{ $page }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <div class="box box-primary">
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/role', $role->id)}}">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Role Code</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="role_code" placeholder="Enter Role Code" value="{{$role->role_code}}" disabled>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Role Name</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="role_name" placeholder="Enter Role Name" value="{{$role->role_name}}">
          </div>
          <div class="col-xs-6 col-xs-offset-3" style="margin-top: 10px;">
            <table class="table table-bordered table-striped table-hover">
              <thead style="background-color: #9993fa;">
                <tr>
                  <th colspan="4" style="border: 1px solid black;">USER LOG ROLE</th>
                </tr>
                <tr>
                  <th style="border: 1px solid black;">Menu Before</th>
                  <th style="border: 1px solid black;">Menu After</th>
                  <th style="border: 1px solid black;">Created By</th>
                  <th style="border: 1px solid black;">Created At</th>
                </tr>
              </thead>
              <tbody>
                @if(count($nav_log) > 0)
                <?php for ($i=0; $i < count($nav_log); $i++) { ?>
                <tr style="cursor: pointer;" onclick="detailRole('{{$nav_log[$i]->created_at}}','{{$nav_log[$i]->befores}}','{{$nav_log[$i]->afters}}')">
                  <td style="border: 1px solid black;text-align: right;">{{$nav_log[$i]->befores}}</td>
                  <td style="border: 1px solid black;text-align: right;">{{$nav_log[$i]->afters}}</td>
                  <?php 
                  $name = '';
                  for ($j=0; $j < count($emp); $j++) { 
                    if ($emp[$j]->employee_id == $nav_log[$i]->created_by) {
                      $name = $emp[$j]->name;
                    }
                  } ?>
                  <td style="border: 1px solid black;">{{$nav_log[$i]->created_by}} - {{$name}}</td>
                  <td style="border: 1px solid black;text-align: right;">{{$nav_log[$i]->created_at}}</td>
                </tr>
                <?php } ?>
                @endif
              </tbody>
            </table>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4" style="text-align: right;">Role Permissions</label>
          <br>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-2" style="padding: 0;">
              @foreach($nav_admins as $nav_admin)
              @if(in_array($nav_admin->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_admin->navigation_code }}" checked> {{ $nav_admin->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_admin->navigation_code }}"> {{ $nav_admin->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-2" style="padding: 0;">
              @foreach($nav_masters as $nav_master)
              @if(in_array($nav_master->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_master->navigation_code }}" checked> {{ $nav_master->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_master->navigation_code }}"> {{ $nav_master->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-2" style="padding: 0;">
              @foreach($nav_services as $nav_service)
              @if(in_array($nav_service->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_service->navigation_code }}" checked> {{ $nav_service->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_service->navigation_code }}"> {{ $nav_service->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-2" style="padding: 0;">
              @foreach($nav_reports as $nav_report)
              @if(in_array($nav_report->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_report->navigation_code }}" checked> {{ $nav_report->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_report->navigation_code }}"> {{ $nav_report->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <div class="col-md-2" style="padding: 0;">
              @foreach($nav_transactions as $nav_transaction)
              @if(in_array($nav_transaction->navigation_code, $permissions))
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_transaction->navigation_code }}" checked> {{ $nav_transaction->navigation_name }}</label><br>
              @else
              <label><input type="checkbox" name="navigation_code[]" class="minimal-red" value="{{ $nav_transaction->navigation_code }}"> {{ $nav_transaction->navigation_name }}</label><br>
              @endif
              @endforeach
            </div>
            <!-- <div class="col-md-2" style="padding: 0">
              
            </div> -->
          </div>
        </div>
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/role') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="modal modal-default fade" id="detail-modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">
                &times;
              </span>
            </button>
            <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail User Log Role</h1>
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="box-body">
                <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
                  <table class="table table-bordered table-striped table-hover" id="tableDetailBefore">
                    <thead style="background-color: #9993fa;">
                      <tr>
                        <th style="border: 1px solid black;">Menu Before (<span id="qty_before"></span>)</th>
                      </tr>
                    </thead>
                    <tbody id="bodyDetailBefore">
                    </tbody>
                  </table>
                </div>
                <div class="col-xs-6" style="padding-left: 5px;padding-right: 0px;">
                  <table class="table table-bordered table-striped table-hover" id="tableDetailAfter">
                    <thead style="background-color: #9993fa;">
                      <tr>
                        <th style="border: 1px solid black;">Menu After (<span id="qty_after"></span>)</th>
                      </tr>
                    </thead>
                    <tbody id="bodyDetailAfter">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger pull-left" onclick="$('#detail-modal').modal('hide')"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  var nav_log_all = jQuery.parseJSON ( '<?php echo json_encode($nav_log_all) ?>' );
  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
  });
  $(function () {
    $('.select2').select2()
  })

  function detailRole(created_at,before,after) {
    $('#tableDetailBefore').DataTable().clear();
    $('#tableDetailBefore').DataTable().destroy();

    $('#tableDetailAfter').DataTable().clear();
    $('#tableDetailAfter').DataTable().destroy();

    $('#bodyDetailBefore').html('');
    var bodyDetailBefore = '';

    $('#bodyDetailAfter').html('');
    var bodyDetailAfter = '';

    var qty_before = 0;
    var qty_after = 0;
    if (parseInt(before) < parseInt(after)) {
      var allbefore = [];

      for(var i = 0; i < nav_log_all.length;i++){
        if (nav_log_all[i].condition == 'before' && nav_log_all[i].created_at == created_at) {
          qty_before++;
          bodyDetailBefore += '<tr>';
          bodyDetailBefore += '<td>'+nav_log_all[i].navigation_code+'</td>';
          bodyDetailBefore += '</tr>';
          allbefore.push(nav_log_all[i].navigation_code);
        }
      }
      var after_highlight = [];
      var after_common = [];
      for(var i = 0; i < nav_log_all.length;i++){
        if (nav_log_all[i].condition == 'after' && nav_log_all[i].created_at == created_at) {
          if (!allbefore.includes(nav_log_all[i].navigation_code)) {
            after_highlight.push(nav_log_all[i].navigation_code);
          }else{
            after_common.push(nav_log_all[i].navigation_code);
          }
        }
      }

      for(var i = 0; i < after_highlight.length;i++){
          qty_after++;
          bodyDetailAfter += '<tr>';
          bodyDetailAfter += '<td style="background-color:#9effb8">'+after_highlight[i]+'</td>';
          bodyDetailAfter += '</tr>';
      }
      for(var i = 0; i < after_common.length;i++){
          qty_after++;
          bodyDetailAfter += '<tr>';
          bodyDetailAfter += '<td>'+after_common[i]+'</td>';
          bodyDetailAfter += '</tr>';
      }
    }else{
      var allafter = [];

      for(var i = 0; i < nav_log_all.length;i++){
        if (nav_log_all[i].condition == 'after' && nav_log_all[i].created_at == created_at) {
          qty_after++;
          bodyDetailAfter += '<tr>';
          bodyDetailAfter += '<td>'+nav_log_all[i].navigation_code+'</td>';
          bodyDetailAfter += '</tr>';
          allafter.push(nav_log_all[i].navigation_code);
        }
      }
      var before_highlight = [];
      var before_common = [];
      for(var i = 0; i < nav_log_all.length;i++){
        if (nav_log_all[i].condition == 'before' && nav_log_all[i].created_at == created_at) {
          if (!allafter.includes(nav_log_all[i].navigation_code)) {
            before_highlight.push(nav_log_all[i].navigation_code);
          }else{
            before_common.push(nav_log_all[i].navigation_code);
          }
        }
      }

      for(var i = 0; i < before_highlight.length;i++){
          qty_before++;
          bodyDetailBefore += '<tr>';
          bodyDetailBefore += '<td style="background-color:#ffabab">'+before_highlight[i]+'</td>';
          bodyDetailBefore += '</tr>';
      }
      for(var i = 0; i < before_common.length;i++){
          qty_before++;
          bodyDetailBefore += '<tr>';
          bodyDetailBefore += '<td>'+before_common[i]+'</td>';
          bodyDetailBefore += '</tr>';
      }
    }
    $('#qty_before').html(qty_before);
    $('#qty_after').html(qty_after);
    $('#bodyDetailBefore').append(bodyDetailBefore);
    $('#bodyDetailAfter').append(bodyDetailAfter);

    var table = $('#tableDetailBefore').DataTable({
            'dom': 'Bfrtip',
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
            'searching'     : true,
            'ordering'    : true,
            'order': [],
            'info'        : true,
            'autoWidth'   : false,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            // "infoCallback": function( settings, start, end, max, total, pre ) {
            //  return "<b>Total "+ total +" pc(s)</b>";
            // }
          });

    var table = $('#tableDetailAfter').DataTable({
            'dom': 'Bfrtip',
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
            'searching'     : true,
            'ordering'    : true,
            'order': [],
            'info'        : true,
            'autoWidth'   : false,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            // "infoCallback": function( settings, start, end, max, total, pre ) {
            //  return "<b>Total "+ total +" pc(s)</b>";
            // }
          });
    $('#detail-modal').modal('show');
  }

  $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
    checkboxClass: 'icheckbox_minimal-red',
    radioClass   : 'iradio_minimal-red'
  })
</script>
@stop

