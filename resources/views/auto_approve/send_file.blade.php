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
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
th:hover {
  overflow: visible;
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
  border:1px solid black;
  vertical-align: middle;
  padding:0;
  font-size: 13px;
  text-align: center;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid black;
  padding:0;
}
td{
  overflow:hidden;
  text-overflow: ellipsis;
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
  background-color: #ffd8b7;
}

.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
  background-color: #FFD700;
}
#loading, #error { display: none; }

</style>
@endsection

@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
    <button class="btn btn-success pull-right" style="margin-left: 5px; width: 15%;" onclick="openModalCreate();"><i class="fa fa-plus"></i> Create Document Name</button>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@stop

@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Send File, please wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-8">
      <div class="box box-primary">
        <form id ="importForm" name="importForm" method="post" action="{{ url('adagio/send') }}" enctype="multipart/form-data">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box-body">
            <div class="row">
              <input type="text" name="loping" id="loping" value="1" hidden>
              <div class="col-xs-4">
                <span style="font-weight: bold; font-size: 16px;">Application:<span class="text-red">*</span></span>
                <input type="text" class="form-control" id="urut" name="urut" style="width: 100%; height: 30px" value="" readonly="">
              </div>
              <div class="col-xs-4">
                <span style="font-weight: bold; font-size: 16px;">Applicant No:<span class="text-red">*</span></span>
                <input type="text" class="form-control" id="number" name="number" style="width: 100%; height: 30px" value="" readonly="">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-4">
                <span style="font-weight: bold; font-size: 16px;">Applicant:<span class="text-red">*</span></span>
                <input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
                <input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}/{{$employee->name}}/{{$employee->position}}">
                <input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
              </div>
              <div class="col-xs-8">
                <span style="font-weight: bold; font-size: 16px;">Department:<span class="text-red">*</span></span>
                <input type="text" class="form-control" value="{{$employee->department}}" readonly="">
                <input type="hidden" id="department" name="department" value="{{$employee->department}}">
                <input type="hidden" id="section" name="section" value="{{$employee->section}}">
              </div>
            </div>
          <!-- <div class="row">
            <div class="col-xs-12">
              <span style="font-weight: bold; font-size: 16px;">Deskripsi:<span class="text-red">*</span></span>
              <input type="text" class="form-control" id="desc" name="desc" style="width: 100%; height: 30px" value="">
            </div>
          </div> -->
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <span style="font-weight: bold; font-size: 16px;">Document Name:<span class="text-red">*</span></span>
                <select class="form-control select3" name="app" id='app' data-placeholder="Select" style="width: 100%; height: 100px;" onchange="selectType(this.value)">
                  <option value=""></option>
                  @php
                  $category = array();
                  @endphp
                  @foreach($approvals as $approval)
                  @if(!in_array($approval->category, $category))
                  <option value="{{ $approval->category }}">{{ $approval->category }}</option>
                  @php
                  array_push($category, $approval->category);
                  @endphp
                  @endif
                  @endforeach   
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <span style="font-weight: bold; font-size: 16px;">Application Date:<span class="text-red">*</span></span>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control" id="tanggal" name="tanggal" value="<?php echo date("d-m-Y"); ?>" readonly>
                </div>
              </div>
            </div>
            <!-- <div class="col-xs-4">
              @foreach($approvals as $approval)
              <span style="font-weight: bold; font-size: 16px;">No:<span class="text-red">*</span></span>
              <input type="text" class="form-control" id="cat1" name="cat1" style="width: 100%; height: 30px" value="{{ $approval->category }}" readonly="">
              @endforeach  
            </div> -->
            
          </div>
          <!-- <div class="row">
            <div class="col-xs-12">
                <span style="font-weight: bold; font-size: 16px;">Category:<span class="text-red">*</span></span>
                <select class="form-control select3" name="category" id='category' data-placeholder="Select" style="width: 100%; height: 100px;">
                  <option value=""></option>
                  @foreach($categorys as $category)
                  <option value="{{ $category->detail }}">{{ $category->detail }}</option>
                  @endforeach    
                </select>
            </div>
          </div> -->
          <div class="row">
            <div class="col-xs-12">
              <span style="font-weight: bold; font-size: 16px;">Description:<span class="text-red">*</span></span>
              <textarea class="form-control" id="detail" name="detail" required></textarea>
            </div>
          </div>
          <!-- <div class="row">
            <div class="col-xs-12">
                <span style="font-weight: bold; font-size: 16px;">Description:<span class="text-red">*</span></span>
                <br><span style="font-size: 13px;">(JPN)</span></span>
                <textarea class="form-control" id="detail_jpn" name="detail_jpn" required></textarea>
            </div>
          </div> -->
          <div class="row">
            <div class="col-xs-4">
              <span style="font-weight: bold; font-size: 16px;">Attachment<span class="text-red">*</span></span>
              <span style="font-size: 13px;">(PDF Only)</span>
              <input type="file" class="form-control-file" id="file" name="file">
            </div>
          </div>
          <div class="col-md-6 col-md-offset-6">
            <div class="form-group pull-right">
              <!-- <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a> -->
              <!-- <button id="search" class="btn btn-primary">Send</button> -->
            </div>
          </div>
        </div>
        <!-- </form> -->
      </div>
    </div>



    <div class="col-xs-4">
      <div class="box box-primary">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="box-body">
          <div class="box">
            <div class="box-body">
              <!-- <span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span> -->
              <table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
                <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                  <tr>
                    <th style="width: 1%;">No</th>
                    <th style="width: 1%;">Type</th>
                    <th style="width: 7%;">Nama</th>
                    <th style="width: 7%;">Position</th>
                    <!-- <th style="width: 1%;">Spt</th> -->
                  </tr>         
                </thead>
                <tbody id="tableBodyList">
                </tbody>
                <!-- <tfoot>
                  <tr>
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

    <div class="col-xs-4">
      <div class="box-body">
        <div class="col-xs-12" style="padding-left: 80px; padding-right: 80px">
          <div class="row">
            <!-- <div class="col-xs-12" style="padding-bottom: 10px;">
              <button class="btn btn-primary" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;" id="search">Send
              </button>
            </div>
            <div class="col-xs-12" style="padding-bottom: 10px;">
              <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">Clear</a>
            </div> -->
            <div class="panel-heading" style="background-color: #ff7f50">Action : </div>
            <div class="panel-body center-text"  style="padding: 20px; background-color: #ffa07a">
              <button class="btn btn-primary" style="font-size: 25px; width: 100%; font-weight: bold; padding: 0;" id="search">Send
              </button>
                    <!-- <br>
                    <br>
                    <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger" style="font-size: 25px; width: 100%; font-weight: bold; padding: 0;">Clear</a>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>

      <form id ="importForm" name="importForm" method="post" action="{{ url('adagio/home/create') }}">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal fade" id="modalCreate">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <div class="nav-tabs-custom tab-danger">
                  <ul class="nav nav-tabs">
                    <li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Create Document Name</a></li>
                  </ul>
                </div>
                <div class="tab-content">
                  <div class="tab-pane active">
                    <div class="row">
                      <div class="col-md-12" style="margin-bottom : 5px">
                        <div class="col-xs-12" style="padding:0;" align="center">
                          <input type="text" class="form-control" id="category" name="category" placeholder="Document Name" required>
                        </div>                      
                      </div>
                  <!-- <div class="col-md-12" style="margin-bottom : 5px">
                    <input type="text" name="manager" id="manager" value="1" hidden>
                    <div class="col-xs-12" style="padding:0;">
                      <span>Manager : </span>
                      <input type="text" class="form-control" value="{{$manager[0]->employee_id}} - {{$manager[0]->name}}" readonly="">
                      <input type="hidden" id="manager" name="manager" value="{{$manager[0]->employee_id}}/{{$manager[0]->name}}/{{$manager[0]->position}}">
                    </div>  
                  </div> -->
                  <!-- <div class="col-md-12" style="margin-bottom : 5px">
                    <input type="text" name="gm" id="gm" value="1" hidden>
                    <div class="col-xs-12" style="padding:0;">
                      <span>General Manager : </span>
                      <input type="text" class="form-control" value="{{$gm[0]->employee_id}} - {{$gm[0]->name}}" readonly="">
                      <input type="hidden" id="gm" name="gm" value="{{$gm[0]->employee_id}}/{{$gm[0]->name}}/{{$gm[0]->position}}">
                    </div>  
                  </div> -->
                  <!-- <div class="col-md-12" style="margin-bottom : 5px">
                    <div class="col-xs-12" style="padding:0;">
                      <span>Finance Manager</span>
                      <input type="text" class="form-control" value="PI9902017 - Romy Agung Kurniawan" readonly="">
                      <input type="hidden" class="form-control" id="finance_manager" name="finance_manager" value="PI9902017/Romy Agung Kurniawan/Manager" readonly>
                    </div>  
                  </div> -->
                  <!-- <div class="col-md-12" style="margin-bottom : 5px">
                    <div class="col-xs-12" style="padding:0;">
                      <span>Finance Director</span>
                      <input type="text" class="form-control" value="PI1712018 - Kyohei Iida" readonly="">
                      <input type="hidden" class="form-control" id="finance_director" name="finance_director" value="PI1712018/Kyohei Iida/Director" readonly>
                    </div> 
                  </div> -->
                  <!-- <div class="col-md-12" style="margin-bottom : 5px">
                    <div class="col-xs-12" style="padding:0;">
                      <span>Director</span>
                      <input type="text" class="form-control" value="PI1301001 - Hiroshi Ura" readonly="">
                      <input type="hidden" class="form-control" id="director" name="director" value="PI1301001/Hiroshi Ura/Director" readonly>
                    </div>  
                  </div> -->
                  <div class="col-md-12" style="margin-bottom : 5px">
                    <input type="text" name="lop" id="lop" value="1" hidden>
                    <div class="col-xs-10" style="padding:0;">
                      <span>Complation Notify To</span>
                      <select class="form-control select2" id="description1" name="description1" data-placeholder='Select Name' style="width: 100%">
                        <option value="">&nbsp;</option>
                        @foreach($user as $row)
                        <option value="{{$row->employee_id}}/{{$row->name}}/{{$row->position}}">{{$row->employee_id}} - {{$row->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-xs-2" style="padding:0; padding-left: 3px; padding-top: 20px;">
                      <button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
                    </div>  
                  </div>
                  <div id="tambah"></div>
                  <div class="col-md-12">
                    <br>
                    <button class="btn btn-success pull-right" onclick="$('[name=importForm]').submit();">Confirm</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  var no = 2;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('#datefrom').datepicker({
      autoclose: true,
      todayHighlight: true
    });
    // $('#tanggal').datepicker({
    //   autoclose: true,
    //   todayHighlight: true
    // });
    
    $('.select2').select2({
      dropdownParent: $('#modalCreate'),
      allowClear : true
    });

    $('.select3').select2();

  });

  var nomorfile = document.getElementById("number");

  $.ajax({
    url: "{{ url('adagio/nomor_file') }}?dept=<?= $employee->department ?>&sect=<?= $employee->section ?>&group=<?= $employee->group ?>", 
    type : 'GET', 
    success : function(data){
      var obj = jQuery.parseJSON(data);
      var number = obj.no_urut;
      var tahun = obj.tahun;
      var bulan = obj.bulan;
      var dept = obj.dept;

      nomorfile.value = dept+tahun+bulan+number;
    }
  });

  var nomorurut = document.getElementById("urut");

  $.ajax({
    url: "{{ url('adagio/nomor_file') }}?dept=<?= $employee->department ?>&sect=<?= $employee->section ?>&group=<?= $employee->group ?>", 
    type : 'GET', 
    success : function(data){
      var obj = jQuery.parseJSON(data);
      var number = obj.no_urut;
        // var tahun = obj.tahun;
        // var bulan = obj.bulan;
        // var dept = obj.dept;
        var ympi = ': Approval System YMPI';

        nomorurut.value = number+ympi;
      }
    });


  function checkEmp(value) {
    if (value.length == 9) {
      var data = {
        employee_id:$('#employee_id').val()
      }

      $.get('{{ url("adagio/select/user") }}',data, function(result, status, xhr){
        if(result.status){
          $('#name').show();
          $('#department').show();

          $.each(result.employee, function(key, value) {
            $('#name').val(value.name);
            $('#department').val(value.department);
          });
        }
      });
    }else{
      $('#name').show();
      $('#department').show();
    }
  }

  function openModalCreate(){
    $('#modalCreate').modal('show');
  }

  function tambah(id,lop) {
    var id = id;

    var lop = "";

    if (id == "tambah"){
      lop = "lop";
    }else{
      lop = "lop2";
    }

      // <input type='text' class='form-control' id='description"+no+"' name='description"+no+"' placeholder='Enter Approval' required>
      // <select class='form-control select2' id='description"+no+"' name='description"+no+"' data-placeholder='Select Name' style='width: 100%'><option value=''>&nbsp;</option>@foreach($user as $row)<option value='{{$row->employee_id}}'>{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>



      var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-10' style='padding:0;''><select class='form-control select2' id='description"+no+"' name='description"+no+"' data-placeholder='Select Name' style='width: 100%'><option value=''>&nbsp;</option>@foreach($user as $row)<option value='{{$row->employee_id}}/{{$row->name}}/{{$row->position}}'>{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select></div><div class='col-xs-2' style='padding:0;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

      $("#"+id).append(divdata);
      document.getElementById(lop).value = no;
      no+=1;
      $('.select2').select2({
        dropdownParent : $("#modalCreate")
      });

    }

    function kurang(elem,lop) {
      var lop = lop;
      var ids = $(elem).parent('div').parent('div').attr('id');
      var oldid = ids;
      $(elem).parent('div').parent('div').remove();
      var newid = parseInt(ids) + 1;
      jQuery("#"+newid).attr("id",oldid);
      jQuery("#description"+newid).attr("name","description"+oldid);
      // jQuery("#duration"+newid).attr("name","duration"+oldid);

      jQuery("#description"+newid).attr("id","description"+oldid);
      // jQuery("#duration"+newid).attr("id","duration"+oldid);

      no-=1;
      var a = no -1;

      for (var i =  ids; i <= a; i++) { 
        var newid = parseInt(i) + 1;
        var oldid = newid - 1;
        jQuery("#"+newid).attr("id",oldid);
        jQuery("#description"+newid).attr("name","description"+oldid);
        // jQuery("#duration"+newid).attr("name","duration"+oldid);

        jQuery("#description"+newid).attr("id","description"+oldid);
        // jQuery("#duration"+newid).attr("id","duration"+oldid);

      // alert(i)
    }

    document.getElementById(lop).value = a;
  }

  function selectType(type){
    var tipe = type;
    var data = {
      cat:tipe
    }

    $.get('<?php echo e(url("adagio/data/approval")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableList').DataTable().clear();
        $('#tableList').DataTable().destroy();
        $('#tableBodyList').html("");
        $('#tableBodyList').empty();
        
        var tableData = '';
        var count = 1;
        $.each(result.lists, function(key, value) {
          
          var identitas = value.user.split("/");

          tableData += '<tr>';
          tableData += '<td>'+ count +'</td>';
          tableData += '<td>'+ value.category +'</td>';
          tableData += '<td>'+ identitas[1] +'</td>';
          tableData += '<td>'+ identitas[2] +'</td>';
          tableData += '</tr>';

          count += 1;
        });

        $('#tableBodyList').append(tableData);
        // var tableList = $('#tableList').DataTable({
        //   'dom': 'Bfrtip',
        //   'responsive':true,
        //   'lengthMenu': [
        //   [ 10, 25, 50, -1 ],
        //   [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        //   ],
        //   'buttons': {
        //     buttons:[
        //     {
        //       extend: 'pageLength',
        //       className: 'btn btn-default',
        //     }
        //     ]
        //   },
        //   'paging': true,
        //   'lengthChange': true,
        //   'pageLength': 20,
        //   'searching': true,
        //   'ordering': true,
        //   'order': [],
        //   'info': true,
        //   'autoWidth': true,
        //   "sPaginationType": "full_numbers",
        //   "bJQueryUI": true,
        //   "bAutoWidth": false,
        //   "processing": true
        // });

        openSuccessGritter('Success!', result.message);
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });

  }

  function clearConfirmation(){
    // location.reload(true);
    // $('#no').val("");
    // $('#employee_id').val("").trigger('change');
    // $('#department').val("");
    $('#desc').val("");
    $('#app').val("").trigger('change');
    $('#date').val("");
    $('#detail').val("");
    $('#file').val("");   
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '4000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '4000'
    });
  }

</script>

@endsection