@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  input[type=checkbox] {
    transform: scale(1.25);
  }
  thead>tr>th{
    /*text-align:center;*/
    background-color: #7e5686;
    color: white;
    border: none;
    border:1px solid black;
    border-bottom: 1px solid black !important;
  }
  tbody>tr>td{
    /*text-align:center;*/
    border: 1px solid black;
  }
  tfoot>tr>th{
    /*text-align:center;*/
  }
  td:hover {
    overflow: visible;
  }
  table.table-hover > tbody > tr > td{
    border:1px solid #eeeeee;
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
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
<h3 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3><br>
</section>

@endsection
@section('content')
<section class="content">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
          <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>
    <div class="box box-primary">
    <div class="box-body">
    <div class="col-xs-12">
      <center><h2 style="background-color: #ff9800; width: 70%;"> Approval お見舞いのお金 </h2></center>
    </div> 
    <div class="col-xs-8">
     <table class="table table-bordered">
          <tr id="show-att">
            <td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" id="attach_pdf">
            </td>
          </tr>
        </table>
    </div>
    <div class="col-xs-4">
      <div class="panel panel-default">
          <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
            <tbody align="center">
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; width: 30%; font-weight: bold; background-color:  #e8daef ; height: 30;">No Approval</td>
                      <td colspan="5"style="border:1px solid black; font-size: 12px; width: 80%">{{ $resumes[0]->no_transaction }}</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 30">No Dokumen</td>
                      <td colspan="5"style="border:1px solid black; font-size: 12px;">{{ $resumes[0]->no_dokumen }}</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 30">Kategori Dokumen</td>
                      <td colspan="5"style="border:1px solid black; font-size: 12px;">{{ $resumes[0]->judul }}</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 30">Nama Dokumen</td>
                      <td colspan="5"style="border:1px solid black; font-size: 12px;">{{ $resumes[0]->description }}</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 30">Pembuat</td>
                      <?php
                        $nama = explode("/", $resumes[0]->nik);
                      ?>
                      <td colspan="2"style="border:1px solid black; font-size: 12px;">{{ $nama[0] }} - {{ $nama[1] }}</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 30">Departemen</td>
                      <td colspan="5"style="border:1px solid black; font-size: 12px;">{{ $resumes[0]->department }}</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 30">Tanggal</td>
                      <td colspan="5"style="border:1px solid black; font-size: 12px;">{{ $resumes[0]->created_at }}</td>
                    </tr>
            </tbody>            
            </table>
        </div>
        @if(($approvers->status == null))
        <div class="panel panel-default">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <input type="hidden"  name="approve" id="approve" value="1" />
          <div class="panel-heading">Approval : </div>
          <div class="panel-body center-text"  style="padding: 20px">
            <a href="{{ url('adagio/verivikasi/'.$resumes[0]->no_transaction) }}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px" onclick="loading()">Verifikasi</button></a>
            <br>
            <br>
            <br>
            <a href="{{ url('adagio/rejected/'.$resumes[0]->no_transaction) }}" style="color: white"><button class="btn btn-danger col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px" onclick="loading()">Rejected</button></a>
          </div>
        </div>
        @elseif(($emp->employee_id == $approvers->approver_id)&&($approvers->status != null))
        <div class="panel panel-default">
          <div class="panel-heading">Sudah Di Verifikasi</div>
        </div>
        @endif
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
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
      $(this).parents(".control-group").remove();
      });
      $('body').toggleClass("sidebar-collapse");

      var showAtt = "{{$file->file}}";
      var path = "{{$file_path}}";

      if(showAtt.includes('.pdf')){
        $('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
      }
    });

    function loading(){
      $("#loading").show();
    }
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

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