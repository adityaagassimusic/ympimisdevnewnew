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
    List of {{ $page }}
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
        <!-- <div class="box-body">
          <div class="col-xs-2">
            <div class="row">
             <select class="form-control select2" id="location" data-placeholder='Location' style="width: 100%">
                <option value="">&nbsp;</option>
                <option value="Saxophone">Saxophone</option>
                <option value="Flute">Flute</option>
                <option value="Clarinet">Clarinet</option>
              </select>
            </div>
          </div>
          <div class="col-xs-2">
            <button class="btn btn-success" onclick="fillTable()">Search</button>
          </div>
        </div> -->

        <div class="box-body">
          <div class="col-xs-12" style="padding:0">
            <table id="tableResult" class="table table-bordered table-striped table-hover" >
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th style="width: 2%;">Tanggal</th>
                  <th style="width: 1%;">HPL</th>
                  <th style="width: 1%;">Jumlah Audit</th>
                  <th style="width: 2%;">Action</th>
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
                <th style="width: 1%">No</th>
                <th style="width: 2%">Material Number</th>
                <th style="width: 5%">Material Description</th>
                <th style="width: 1%">Qty</th>
                <th style="width: 1%">Qty Audit</th>
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
  });


  function clearConfirmation(){
    location.reload(true);
  }

  function modalAudit(tanggal,hpl) {
    $('#loading').show();

    var data = {
      hpl:hpl,
      tanggal:tanggal
    }

    $.get('{{ url("fetch/report/case/audit/detail") }}',data, function(result, status, xhr){
      if(result.status){
        $('#myModalLabel').html("Detail Audit "+hpl+" Pada Tanggal "+tanggal+"");

        $('#tableDetail').DataTable().clear();
        $('#tableDetail').DataTable().destroy();
        $('#bodyTableDetail').html("");

        var tableData = "";
        var index = 1;
        $.each(result.stock, function(key2, value2) {
          var stat = 0;
          $.each(result.detail, function(key, value) {

            if (value.material_number == value2.material_number) {
              tableData += '<tr>';
              tableData += '<td style="width: 1%;border:1px solid black;padding:2px">'+ index +'</td>';
              tableData += '<td style="width: 2%;border:1px solid black;padding:2px;text-align:left;">'+ value.material_number +'</td>';
              tableData += '<td style="width: 5%;border:1px solid black;padding:2px">'+ value.material_description +'</td>';
              if (value.qty > value.qty_audit) {
                tableData += '<td style="width: 1%;border:1px solid black;padding:2px;background:red;color:white">'+ value.qty +'</td>';
                tableData += '<td style="width: 1%;border:1px solid black;padding:2px;background:red;color:white">'+ value.qty_audit +'</td>';
              }else if(value.qty < value.qty_audit){
                tableData += '<td style="width: 1%;border:1px solid black;padding:2px;background:red;color:white">'+ value.qty +'</td>';
                tableData += '<td style="width: 1%;border:1px solid black;padding:2px;background:red;color:white">'+ value.qty_audit +'</td>';
              }
              else{
                tableData += '<td style="width: 1%;border:1px solid black;padding:2px;background:green;color:white">'+ value.qty +'</td>';
                tableData += '<td style="width: 1%;border:1px solid black;padding:2px;background:green;color:white">'+ value.qty_audit +'</td>';
              }
              tableData += '</tr>';
              index++;
              stat = 1;
            }


          });

           if (stat == 0) {
              tableData += '<tr>';
              tableData += '<td  style="width: 1%;border:1px solid black;padding:2px">'+ index +'</td>';
              tableData += '<td  style="width: 2%;border:1px solid black;padding:2px;text-align:left;">'+ value2.material_number +'</td>';
              tableData += '<td  style="width: 5%;border:1px solid black;padding:2px">'+ value2.material_description +'</td>';
              tableData += '<td  style="width: 1%;border:1px solid black;padding:2px">-</td>';
              tableData += '<td  style="width: 1%;border:1px solid black;padding:2px">-</td>';
              tableData += '</tr>';
              index++;
              stat = 1;
            }

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
          'searching': true ,
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
        openErrorGritter('Error!',result.message);
      }
    });

  }

  function fillTable(){
    $('#loading').show();

    // var location = $('#location').val();
    
    // var data = {
    //   location:location
    // }

    // data,

    $.get('{{ url("fetch/report/case/audit") }}',  function(result, status, xhr){

      $('#tableResult').DataTable().clear();
      $('#tableResult').DataTable().destroy();
      $('#tableBodyResult').html("");

      var tableData = "";

      $.each(result.lists, function(key, value) {
         tableData += '<tr>';     
         tableData += '<td>'+ value.tanggal+'</td>';
         tableData += '<td>'+ value.hpl+'</td>';
         tableData += '<td>'+ value.jumlah_audit +'</td>';     
         tableData += '<td><a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="modalAudit(\''+value.tanggal+'\',\''+value.hpl+'\')" data-toggle="modal" data-target="#modalAudit"  title="Detail Data"><i class="fa fa-pencil"></i> Detail</a></td>';
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