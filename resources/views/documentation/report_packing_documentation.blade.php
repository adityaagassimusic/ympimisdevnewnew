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
    vertical-align: middle;
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
    List of {{ $page }} {{ $location }}
  </h1>
  <ol class="breadcrumb">
    <!-- <li><a href="{{ url("index/form_experience/create")}}" class="btn btn-success btn-sm" style="color:white"><i class="fa fa-plus"></i>Buat {{ $page }}</a></li> -->
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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
      <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-xs-2">
            <div class="row">
             <div class="form-group">
                <label>Tanggal Dari</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                    <input type="text" class="form-control pull-right" id="tanggal" name="tanggal" >
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-2" style="margin-left:10px">
            <div class="row">
             <div class="form-group">
                <label>Tanggal Ke</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                    <input type="text" class="form-control pull-right" id="tanggal_ke" name="tanggal_ke" >
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-2">
              <div class="form-group">
                  <label style="color: white;">Action</label>
                  <input type="button" id="search" onClick="fillTable()" class="form-control btn btn-success" value="Search"></button>
              </div>
          </div>
        </div>

        <div class="box-body">
          <div class="col-xs-12" style="padding:0">
            <table id="tableResult" class="table table-bordered table-striped table-hover" >
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <!-- <th>Nama</th> -->
                  <th style="width: 1%;">Location</th>
                  <th style="width: 1%;">Employee ID</th>
                  <th style="width: 3%;">Name</th>
                  <th style="width: 2%;">Serial Number</th>
                  <th style="width: 2%;">Model</th>
                  <th style="width: 3%;">Photo</th>
                  <th style="width: 2%;">Date</th>
                  <th style="width: 1%;">Action</th>
                </tr>
              </thead>
              <tbody id="tableBodyResult">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-danger fade" id="modalDeleteData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
        </div>
        <div class="modal-body">
          Apakah anda yakin ingin delete data ini ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          <a id="a" name="modalbutton" type="button"  onclick="delete_data_packing(this.id)" class="btn btn-danger">Yes</a>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modalImage">
    <div class="modal-dialog modal-lg" style="width:1150px">
      <div class="modal-content">
        <div class="modal-header"><center> <b style="font-size: 2vw"></b> </center>
          <div class="modal-body table-responsive no-padding">
            <div class="col-xs-12" style="padding-top: 20px">
              <div class="modal-footer">
                <div class="row">
                  <button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 20px;font-weight: bold;">
                    CLOSE
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-12" id="images" style="padding-top: 20px">
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


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
<script>

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    
    fillTable();
    $('body').toggleClass("sidebar-collapse");

    $("#navbar-collapse").text('');

    $('.select2').select2({
      dropdownAutoWidth : true,
      allowClear:true
    });

    $('#tanggal').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
      });

    $('#tanggal_ke').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
      });
  });


  function clearConfirmation(){
    location.reload(true);
  }

  function deleteData(id) {
    $('[name=modalbutton]').attr("id",id);
  }

  function modalImage(data) {

    // var photo = data.split(",");
    // var photos = '';
    // var url = '{{ url("images/packing") }}';
    // for (var i = 0; i < photo.length; i++) {
    //   photos += '<img style="width:100%" src="'+url+'/'+photo[i]+'" class="user-image" alt="User image" >';
    //   photos += '&nbsp;&nbsp;&nbsp;';
    // }

    // $('#images').html(photos);

    // $('#modalImage').modal('show');

    var photo = data.split(",");
    var photos = '';
    // var url = '{{ url("images/packing") }}';
    for (var i = 0; i < photo.length; i++) {

      var result = doesFileExist("http://10.109.52.6/mirai/public/images/packing/"+ photo[i]);
      
      if (result == true) {
        photos += '<img style="width:100%" src="http://10.109.52.6/mirai/public/images/packing/'+photo[i]+'" class="user-image" alt="User image" >';
        photos += '<br>';
      } else {
        photos += '<img style="width:100%" src="https://10.109.52.4/mirai/public/images/packing/'+photo[i]+'" class="user-image" alt="User image" >';
        photos += '<br>';
      }
      
    }

    $('#images').html(photos);
    $('#modalImage').modal('show');
  }


  function doesFileExist(urlToFile) {
      var xhr = new XMLHttpRequest();
      xhr.open('HEAD', urlToFile, false);
      xhr.send();
       
      if (xhr.responseURL.includes('404')) {
          return false;
      } else {
          return true;
      }
  }

  function fillTable(){
    $('#loading').show();

    var location = "{{$location}}";
    var tanggal = $("#tanggal").val();
    var tanggal_ke = $("#tanggal_ke").val();
    
    var data = {
      location:location,
      tanggal:tanggal,
      tanggal_ke:tanggal_ke
    }

    $.get('{{ url("fetch/report/packing_documentation") }}', data, function(result, status, xhr){

      $('#tableResult').DataTable().clear();
      $('#tableResult').DataTable().destroy();
      $('#tableBodyResult').html("");
      var tableData = "";

        $.each(result.data, function(key, value) {
           tableData += '<tr>';     
           tableData += '<td>'+ value.location+'</td>';
           tableData += '<td>'+ value.employee_id+'</td>';
           tableData += '<td>'+ value.employee_name +'</td>';     
           tableData += '<td>'+ (value.serial_number || "") +'</td>';
            tableData += '<td>'+ value.model +'</td>';
        
          if (value.photo != null) {
            var data = JSON.parse(value.photo);
            // for (var i = 0; i < data.length; i++) {
            // }

            tableData += '<td style="text-align:center">' 
            tableData += '<a class="btn btn-success" onclick="modalImage(\''+data+'\')"><i class="fa fa-camera"> Photo</a>';
            tableData += '</td>'
          }
         tableData += '<td>'+ value.created_at +'</td>';   
         tableData += '<td><a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="deleteData('+value.id+')" data-toggle="modal" data-target="#modalDeleteData"  title="Delete Data"><i class="fa fa-trash"></i> Delete</a></td>';     
         tableData += '</tr>'; 
      });

      $('#tableBodyResult').append(tableData);

      var table = $('#tableResult').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 5, 10, 25, -1 ],
        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
        'pageLength': 15,
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

      $('#loading').hide();
    })

    }

    function deleteConfirmation(id) {
      jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
    }

    function delete_data_packing(id){

      var data = {
        id:id,
      }

      $("#loading").show();

      $.post('{{ url("delete/packing_documentation") }}', data, function(result, status, xhr){
        if (result.status == true) {
              openSuccessGritter("Success","Data Berhasil Diupdate");
              $("#loading").hide();
              setTimeout(function(){  window.location.reload() }, 2500);
        }
        else{
          openErrorGritter("Success","Data Gagal Diupdate");
        }
      });
    }

    function openSuccessGritter(title, message){
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '2000'
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