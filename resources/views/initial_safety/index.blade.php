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
@endsection
@section('header')
<section class="content-header">
  <h1>
    List of {{ $page }}s
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" style="color:white">Delete {{ $page }}s</a>
      &nbsp;
      <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import {{ $page }}s</a>
      &nbsp;
      <a data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a>
    </li>
  </ol>
</section>
@endsection


@section('content')

<section class="content">
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
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Material Number</th>
                <th>Material Description</th>
                <th>Origin group</th>
                <th>Valid Date</th>
                <th>Qty</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
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
  </div>
</section>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id ="importForm" method="get" action="{{ url('destroy/safety_stock') }}">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Valid Date</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="valid_date2" name="valid_date2" required>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Origin Group</label>
                <select class="form-control select2" multiple="multiple" name="origin_group2[]" id='origin_group2' data-placeholder="Select Origin Group" style="width: 100%;" required>
                  <option></option>
                  @foreach($origin_groups as $origin_group)
                  <option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button id="modalImportButton" type="submit" onclick="return confirm('Are you sure you want to delete this Initial Safety Stock?');" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body" id="modalDeleteBody">
        Are you sure delete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Create {{$page}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="right">
            <label class="col-sm-4">Material<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="material_number" style="width: 100%;" data-placeholder="Choose a Material Number..." required>
                <option value=""></option>
                @foreach($materials as $material)
                <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Valid Date<span class="text-red">*</span></label>
            <div class="col-sm-6">
             <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="valid_date" name="valid_date">
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
          <div class="col-sm-6">
            <div class="input-group">
              <input min="1" type="number" class="form-control" id="quantity" placeholder="Enter Quantity" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
      </div>    
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-plus"></i> Create</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Edit {{$page}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="right">
            <label class="col-sm-4">Material<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="material_number_edit" style="width: 100%;" data-placeholder="Choose a Material Number..." required disabled>
                <option value=""></option>
                @foreach($materials as $material)
                <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Valid Date<span class="text-red">*</span></label>
            <div class="col-sm-6">
             <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="valid_date_edit" name="valid_date" readonly>
            </div>
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
          <div class="col-sm-6">
            <div class="input-group">
              <input min="1" type="number" class="form-control" id="quantity_edit" placeholder="Enter Quantity" required>
              <input type="hidden" id="id_edit">
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
      </div>    
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      <button type="button" onclick="edit()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Detail {{$page}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="right">
            <label class="col-sm-6">Material Number</label>
            <div class="col-sm-6" align="left" id="material_number_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Material Description</label>
            <div class="col-sm-6" align="left" id="material_description_view"></div>
          </div>          
          <div class="form-group row" align="right">
            <label class="col-sm-6">Origin Group</label>
            <div class="col-sm-6" align="left" id="origin_group_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Valid Date</label>
            <div class="col-sm-6" align="left" id="valid_date_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Quantity</label>
            <div class="col-sm-6" align="left" id="quantity_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Created By</label>
            <div class="col-sm-6" align="left" id="created_by_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Last Update</label>
            <div class="col-sm-6" align="left" id="last_updated_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Created At</label>
            <div class="col-sm-6" align="left" id="created_at_view"></div>
          </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id ="importForm" method="post" action="{{ url('import/safety_stock') }}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
          Format: [Material Number][Valid Date][Quantity]<br>
          Rule: One file one date <br>
          Sample: <a href="{{ url('download/manual/import_initial_safety_stock.txt') }}">1910_import_initial_safety_stock.txt</a>
        </div>
        <div class="">
          <div class="modal-body">
            <center><input type="file" name="intial_stock" id="InputFile" accept="text/plain"></center>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
            <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@stop

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

  jQuery(document).ready(function() {
    draw_table();

    $('#valid_date').datepicker({
      autoclose: true,
      format: "dd/mm/yyyy",
      todayHighlight: true
    });

    $('.select2').select2();
  });

  function draw_table() {
    var table = $('#example1').DataTable({
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
          // text: '<i class="fa fa-print"></i> Show',
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
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("fetch/safety_stock") }}"
      },
      "columns": [
      { "data": "material_number" },
      { "data": "material_description"},
      { "data": "origin_group_name" },
      { "data": "valid_date" },
      { "data": "quantity" },
      { "data": "action" }
      ],
    });

    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
    });

    table.columns().every( function () {
      var that = this;
      $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
          that
          .search( this.value )
          .draw();
        }
      });
    });
    $('#example1 tfoot tr').appendTo('#example1 thead');
  }

  function create() {
    var data = {
      material_number: $("#material_number").val(),
      valid_date: $("#valid_date").val(),
      quantity: $("#quantity").val()
    };

    $.post('{{ url("create/safety_stock") }}', data, function(result, status, xhr){
      if (result.status == true) {
        $('#example1').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","New Initial Safety Stock has been created.");
      } else {
        openErrorGritter("Error","Initial safety stock not created.");
      }
    })
  }

  function modalEdit(id) {
    $('#EditModal').modal("show");

    var data  = {
      id:id
    };

    $.get('{{ url("edit/safety_stock") }}', data, function(result, status, xhr){
      $("#id_edit").val(id);
      $('#material_number_edit').val(result.datas.material_number).trigger('change.select2');
      $("#valid_date_edit").val(result.datas.valid_date);
      $("#quantity_edit").val(result.datas.quantity);
    })
  }

  function edit() {
   var data = {
    id: $("#id_edit").val(),
    quantity: $("#quantity_edit").val()
  };

  $.post('{{ url("edit/safety_stock") }}', data, function(result, status, xhr){
    if (result.status == true) {
      $('#example1').DataTable().ajax.reload(null, false);
      openSuccessGritter("Success","Initial Safety Stock has been edited.");
    } else {
      openErrorGritter("Error","Failed to edit initial safety stock.");
    }
  })
}

function modalView(id) {
  $("#ViewModal").modal("show");
  var data = {
    id:id
  };

  $.get('{{ url("view/safety_stock") }}', data, function(result, status, xhr){
    $("#material_number_view").text(result.datas[0].material_number);
    $("#material_description_view").text(result.datas[0].material_description);
    $("#origin_group_view").text(result.datas[0].origin_group_name);
    $("#valid_date_view").text(result.datas[0].valid_date);
    $("#quantity_view").text(result.datas[0].quantity);
    $("#created_by_view").text(result.datas[0].name);
    $("#last_updated_view").text(result.datas[0].updated_at);
    $("#created_at_view").text(result.datas[0].created_at);
  })
}

function modalDelete(id, material_number, valid_date) {
  var data = {
    id: id
  };

  if (!confirm("Are you sure want to delete Material ' "+material_number+" ' in "+valid_date+" ?")) {
    return false;
  }

  $.post('{{ url("delete/safety_stock") }}', data, function(result, status, xhr){
    $('#example1').DataTable().ajax.reload(null, false);
    openSuccessGritter("Success","Material ' "+material_number+" ' in "+valid_date+" has been deleted.");
  })
}

$(function () {
  $('#valid_date2').datepicker({
    autoclose: true,
    format: "mm-yyyy",
    startView: "months", 
    minViewMode: "months"
  });
})

function deleteConfirmation(url, name, id) {
  jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
  jQuery('#modalDeleteButton').attr("href", url+'/'+id);
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

@stop