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
  overflow:hidden;
  padding: 3px;
}
tbody>tr>td{
  text-align:left;
  vertical-align:middle !important;
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
td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List of {{ $page }}s 
  </h1>
  <ol class="breadcrumb">
    <li><a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-sm" style="width: 100%;color:white;font-weight: bold; ">Tambahkan Data</a></li>
  </ol>
</section>
@endsection


@section('content')

<section class="content">

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <div>
      <center>
        <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
      </center>
    </div>
  </div>

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
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box-body" style="overflow-x: scroll;">
          <table id="example1" class="table table-bordered table-striped table-hover" >
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Section</th>
                <th>No CPAR</th>
                <th>CPAR Date</th>
                <th>HPL</th>    
                <th>Type</th>
                <th>Subsidiary</th> 
                <th>PIC</th>
                <th>Defect</th>
                <th>Location</th>
                <th>Category</th>
                <th>Jumlah</th>
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
</section>


  <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1100px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"><center>Input Market Claim</b></center></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
           <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jenis Market Claim<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select3" id="section" name="section" style="width: 100%;" data-placeholder="Pilih Jenis" required>
                <option value=""></option>
                <option value="wi">WI</option>
                <option value="edin">EDIN</option>
              </select>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">No CPAR<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select3" id="cpar_no" name="cpar_no" style="width: 100%;" data-placeholder="Pilih No CPAR" required>
              <option value=""></option>
                @foreach($no_cpar as $no_cpar)
                <option value="{{ $no_cpar->cpar_no }}">{{ $no_cpar->cpar_no }}</option>
                @endforeach
            </select>
           </div>
          </div>
          <div class="form-group row" align="left" id="desc">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">CPAR Date<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="tgl_cpar" placeholder="Tanggal Komplain" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">HPL<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="hpl" placeholder="Contoh : SAX, FL, P-32E, P37D" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Type<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="type" placeholder="Contoh : YTS-280, YAS-480, P-32EP, P-32E" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Subsidiary</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="subsidiary" placeholder="Contoh : YCJ, YMJ, YME">
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">PIC</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="pic" placeholder="Contoh : Assy FL, WLD, BP">
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Defect<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="defect" placeholder="Contoh : Kizu, Bari, Seri, Kake, Suara tidak keluar" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Location NG</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="location" placeholder="Contoh : Bass Doremi, Case, La tinggi, Reed D42">
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Kategori NG<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select3" id="category" name="category" style="width: 100%;" data-placeholder="Pilih Kategori NG" required>
                <option value=""></option>
                <option value="Visual">Visual</option>
                <option value="Fungsi">Fungsi</option>
                <option value="NG Jelas">NG Jelas</option>
              </select>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Qty<span class="text-red">*</span></label>
            <div class="col-sm-8">

              <div class="input-group">
                <input type="number" class="form-control" id="defect_qty" placeholder="Jumlah Defect" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
        <button type="button" onclick="create()" class="btn btn-primary" ><i class="fa fa-plus"></i> Create</button>
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
<script>

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    fillDetail();
    $("#navbar-collapse").text('');
      $('.select2').select2({
        language : {
          noResults : function(params) {
            return "There is no cpar with status 'close'";
          }
        }
      });
    });

    $('#tgl_cpar').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true
    });

    $(function () {
      $('.select3').select2({
        dropdownParent: $('#createModal')
      });
    })


  function clearConfirmation(){
    location.reload(true);
  }

  function fillDetail(){
    $('#example1').DataTable().destroy();
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    } );
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
        // "serverSide": true,
        "ajax": {
          "type" : "post",
          "url" : "{{ url("index/market_claim/filter") }}"
        },
        "columns": [
          { "data": "sec" , "width": "2%"},
          { "data": "cpar_no" , "width": "10%"},
          { "data": "cpar_date" , "width": "5%"},
          { "data": "hpl" , "width": "4%"},
          { "data": "type" , "width": "4%"},
          { "data": "subsidiary" , "width": "3%"},
          { "data": "pic" , "width": "3%"},
          { "data": "defect", "width": "10%"},
          { "data": "location", "width": "3%"},
          { "data": "category", "width": "3%"},
          { "data": "qty", "width": "3%"},
        ]
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
  }


  function create() {
    $('#loading').show();

    if($("#section").val() == "" || $('#cpar_no').val() == null || $('#tgl_cpar').val() == "" || $('#hpl').val() == "" || $('#type').val() == "" || $('#defect').val() == "" || $('#category').val() == "" || $('#defect_qty').val() == ""){
        $('#loading').hide();
      openErrorGritter('Error', "Please fill field with (*) sign.");
      return false;
    }

    var data = {
      section: $("#section").val(),
      cpar_no: $("#cpar_no").val(),
      tgl_cpar: $("#tgl_cpar").val(),
      hpl: $("#hpl").val(),
      type : $("#type").val(),
      subsidiary : $("#subsidiary").val(),
      pic : $("#pic").val(),
      defect : $("#defect").val(),
      location : $("#location").val(),
      category : $("#category").val(),
      defect_qty : $("#defect_qty").val()
    };

    $.post('{{ url("index/market_claim/create") }}', data, function(result, status, xhr){
      if (result.status == true) {
        $('#loading').hide();
        $('#createModal').modal('hide');
        $('#example1').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","New Market Claim Data has been Created");
        cleardata();
      } else {
        openErrorGritter("Error",result.message);
      }
    })
  }

  function cleardata(){
    $("#section").val("").trigger('change');
    $("#cpar_no").val("").trigger('change');
    $("#tgl_cpar").val("");
    $("#hpl").val("");
    $("#type").val("");
    $("#subsidiary").val("");
    $("#pic").val("");
    $("#defect").val("");
    $("#location").val("");
    $("#category").val("").trigger('change');
    $("#defect_qty").val("");
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
    time: '2000'
  });
}

  

</script>

@stop