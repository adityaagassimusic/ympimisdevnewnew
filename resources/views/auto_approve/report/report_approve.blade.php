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
  <h1>
    Check & Verifikasi {{ $page }}
  </h1>
  <ol class="breadcrumb">
 </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
      </p>
  </div>
    <?php $user = STRTOUPPER(Auth::user()->username)?>
    <div class="box box-primary">
    <div class="box-body"> 
        <table class="table table-bordered">
          <tr id="show-att">
            <td>
              <table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
            <thead>
              <tr>
                <td colspan="8" style="font-weight: bold;font-size: 12px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
                @if(($detail->remark == "Rejected"))
                <td colspan="5" style="font-weight: bold;font-size: 12px" align="right"><img width="50" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0;position: absolute;top: 55px;left: 840px"></td>
                @endif
              </tr>
              <tr>
                <td colspan="13"><br></td>
              </tr>       
              <tr>
                <td colspan="13" style="text-align: center;font-weight: bold;font-size: 20px">FILE APPROVAL CONFIRMATION</td>
              </tr>
              <tr>
                <td colspan="5"><br></td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px;">Application</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->aplication }}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Application No</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->no_transaction }}</td>
              </tr> 
                  <?php
                    $identitas = explode("/",$detail->nik);
                  ?> 
              <!-- <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold">NIK</td>
              <td colspan="10" style="font-size: 15px;">: {{ $identitas[0] }}</td>
              </tr> -->
              <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Applicant</td>
              <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $identitas[1] }}</td>
              </tr>
              <!-- <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold">Jabatan</td>
              <td colspan="10" style="font-size: 15px;">: {{ $identitas[2] }}</td>
              </tr> -->
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Department</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->department }}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Applicant Date</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->date }}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Subject</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->judul }}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Category</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->category }}</td>
              </tr>
              
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Description</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->summary }}</td>
              </tr>
              <!-- <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold">Download</td>
                <td colspan="10" style="font-size: 15px; padding-left: 10px"><a href="{{url('../public/adagio')}}/{{ $detail->file }}"><button class="btn btn-success btn-xs">Download</button></a></td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold">Upload</td>
                <td colspan="10" style="font-size: 15px; padding-left: 5px"><input type="file" class="form-control-file" id="file" name="file"></td>
              </tr> -->
            </thead>
        </table>
    <br>
        <table style="width: 100%; font-family: arial; border-collapse: collapse; ">
            <tr style="background-color: rgb(126,86,134);" id="tableChecked">
            </tr><tr style="background-color: rgb(126,86,134);" id="tableHeaderResume">
            </tr>
            <tr style="background-color: rgb(126,86,134);" id="tableHeaderResume1">
            </tr>
            <tr style="background-color: rgb(126,86,134);" id="tableHeaderResume2">
            </tr>
        </table>
            </td>


            @if(($detail->remark != "All Approved") && ($detail->remark != "Rejected"))
            <!-- (($user == $resumes[0]->chief_or_foreman_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ca == null && $resumes[0]->posisi == "chf_asal") -->
            @if(($user == $detail->appove1[0]) || ($user == $detail->appove2[0]) || ($user == $detail->appove3[0]) || ($user == $detail->appove4[0]) || ($user == $detail->appove5[0]) || ($user == $detail->appove6[0]) || ($user == $detail->appove7[0]) || ($user == $detail->appove8[0]) || ($user == $detail->appove9[0])  || ($user == $detail->appove10[0]) || (Auth::user()->role_code == "MIS"))
            <td colspan="3" style="font-size: 16px;width: 25%;">
              <div class="col-md-12">
                <div class="panel-default" style="padding-top: 20px">
                  <input type="hidden"  name="approve" id="approve" value="1" />
                  <div class="panel-heading" style="background-color: #ff7f50">Approval : </div>
                  <div class="panel-body center-text"  style="padding: 20px; background-color: #ffa07a">
                    <a href="{{url('adagio/verivikasi/'.$detail->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px" onclick="loading()">Verifikasi</button></a>
                    <br>
                    <br>
                    <a href="{{url('adagio/rejected/'.$detail->id)}}" style="color: white"><button class="btn btn-danger col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px" onclick="loading()">Rejected</button></a>
                  </div>
                </div>
              </div>
            </td>
            @endif
            @endif
          </tr>
        </table>
    </div>
  </div>

  <div class="box box-primary">
    <div class="box-body"> 
       <table class="table table-bordered">
          <tr id="show-att">
            <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;" colspan="2" id="attach_pdf">
            </td>
          </tr>
        </table>
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

      var showAtt = "{{$detail->file}}";
      var path = "{{$file_path}}";
      if(showAtt.includes('.pdf')){
        $('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
      }
      var id = "{{ $detail->id }}";
      selectType();
    });

    function loading(){
      $("#loading").show();
    }
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function selectType(){
    //   var data = {
    //   id:'{{ Request::segment(4) }}'
    // }
    id = '{{ Request::segment(4) }}';

    $.get('<?php echo e(url("adagio/data/fetch/")); ?>/'+id, function(result, status, xhr){
      if(result.status){
        var tableData = '';
        $('#tableChecked').html("");
        $('#tableChecked').empty();
          if(result.detail.approve1 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow; border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }
          if(result.detail.approve2 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }
          if(result.detail.approve3 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }
           if(result.detail.approve4 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }
           if(result.detail.approve5 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }
           if(result.detail.approve6 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }
           if(result.detail.approve7 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          } 
           if(result.detail.approve8 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          } 
           if(result.detail.approve9 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          } 
           if(result.detail.approve10 == null) {
             tableData += '';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 10px">Checked By</th>';
          }

        $('#tableChecked').append(tableData);

        var tableData = '';
        $('#tableHeaderResume').html("");
        $('#tableHeaderResume').empty();
          if(result.detail.approve1 == null) {
             tableData += '';
          }else{
            var approve1 = result.detail.approve1.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve1[1] +'</th>';
          }
          if(result.detail.approve2 == null) {
             tableData += '';
          }else{
            var approve2 = result.detail.approve2.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve2[1] +'</th>';
          }
          if(result.detail.approve3 == null) {
             tableData += '';
          }else{
            var approve3 = result.detail.approve3.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve3[1] +'</th>';
          }
           if(result.detail.approve4 == null) {
             tableData += '';
          }else{
            var approve4 = result.detail.approve4.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve4[1] +'</th>';
          }
           if(result.detail.approve5 == null) {
             tableData += '';
          }else{
            var approve5 = result.detail.approve5.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve5[1] +'</th>';
          }
           if(result.detail.approve6 == null) {
             tableData += '';
          }else{
            var approve6 = result.detail.approve6.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve6[1] +'</th>';
          }
           if(result.detail.approve7 == null) {
             tableData += '';
          }else{
            var approve7 = result.detail.approve7.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve7[1] +'</th>';
          } 
           if(result.detail.approve8 == null) {
             tableData += '';
          }else{
            var approve8 = result.detail.approve8.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve8[1] +'</th>';
          } 
           if(result.detail.approve9 == null) {
             tableData += '';
          }else{
            var approve9 = result.detail.approve9.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve9[1] +'</th>';
          } 
           if(result.detail.approve10 == null) {
             tableData += '';
          }else{
            var approve10 = result.detail.approve10.split("/");
            tableData += '<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">'+ approve10[1] +'</th>';
          }

        $('#tableHeaderResume').append(tableData);

        var tableData = '';
        $('#tableHeaderResume1').html("");
        $('#tableHeaderResume1').empty();
          if(result.detail.date1 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve2 == null) {
             tableData += '';
          }
          else if(result.detail.date2 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve3 == null) {
             tableData += '';
          }
          else if(result.detail.date3 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve4 == null) {
             tableData += '';
          }
           else if(result.detail.date4 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve5 == null) {
             tableData += '';
          }
           else if(result.detail.date5 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve6 == null) {
             tableData += '';
          }
           else if(result.detail.date6 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve7 == null) {
             tableData += '';
          }
           else if(result.detail.date7 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }
          if(result.detail.approve8 == null) {
             tableData += '';
          } 
           else if(result.detail.date8 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          } 
           if(result.detail.approve9 == null) {
             tableData += '';
          }
           else if(result.detail.date9 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          } 
          if(result.detail.approve10 == null) {
             tableData += '';
          }
           else if(result.detail.date10 == null) {
             tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"></th>';
          }else{
            if (result.detail.remark == 'Rejected') {
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/rejected.jpg') }}"+'" alt="" style="padding: 0"></th>';  
            }
            else{
            tableData += '<th colspan="1" style="height:40px; width:8%; background-color: white; font-weight: bold; border: 1px solid black; text-align: center;"><img width="70" src="'+"{{ asset('files/file_approval/approved.jpg') }}"+'" alt="" style="padding: 0"></th>';
            }
          }

        $('#tableHeaderResume1').append(tableData);

        var tableData = '';
        $('#tableHeaderResume2').html("");
        $('#tableHeaderResume2').empty();
          if(result.detail.date1 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date1 +'</th>';
          }
          if(result.detail.approve2 == null) {
             tableData += '';
          }
          else if(result.detail.date2 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date2 +'</th>';
          }
          if(result.detail.approve3 == null) {
             tableData += '';
          }
          else if(result.detail.date3 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date3 +'</th>';
          }
          if(result.detail.approve4 == null) {
             tableData += '';
          }
           else if(result.detail.date4 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date4 +'</th>';
          }
          if(result.detail.approve5 == null) {
             tableData += '';
          }
           else if(result.detail.date5 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date5 +'</th>';
          }
          if(result.detail.approve6 == null) {
             tableData += '';
          }
           else if(result.detail.date6 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date6 +'</th>';
          }
          if(result.detail.approve7 == null) {
             tableData += '';
          }
           else if(result.detail.date7 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date7 +'</th>';
          }
          if(result.detail.approve8 == null) {
             tableData += '';
          } 
           else if(result.detail.date8 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date8 +'</th>';
          } 
           if(result.detail.approve9 == null) {
             tableData += '';
          }
           else if(result.detail.date9 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date9 +'</th>';
          } 
          if(result.detail.approve10 == null) {
             tableData += '';
          }
           else if(result.detail.date10 == null) {
             tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px""></th>';
          }else{
            tableData += '<th colspan="1" style="background-color: yellow;border: 1px solid black; text-align: center; font-size : 12px"">'+ result.detail.date10 +'</th>';
          }

        $('#tableHeaderResume2').append(tableData);
      }
      else{
        openErrorGritter('Error!', result.message);
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