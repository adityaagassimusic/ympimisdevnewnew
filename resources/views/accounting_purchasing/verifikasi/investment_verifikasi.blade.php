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
    <small>Verifikasi Form Investment</small>
  </h1>
  <ol class="breadcrumb">

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
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
      </p>
  </div>
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-body">
      <?php 
        $user = STRTOUPPER(Auth::user()->username);
        $user_manager = explode("/", $investment->approval_manager);
        $user_dgm = explode("/", $investment->approval_dgm);
        $user_gm = explode("/", $investment->approval_gm);
        $user_manager_acc = explode("/", $investment->approval_manager_acc);
        $user_direktur_acc = explode("/", $investment->approval_dir_acc);
        $user_presdir = explode("/", $investment->approval_presdir);
      ?>


      @if((($user == $user_manager[0] || str_contains(Auth::user()->role_code, 'S-MIS')) && count($user_manager) != 4 && $investment->posisi == "manager") 
      || (($user == $user_dgm[0] || str_contains(Auth::user()->role_code, 'S-MIS')) && count($user_dgm) != 4 && $investment->posisi == "dgm")
      || (($user == $user_gm[0]) && count($user_gm) != 4 && $investment->posisi == "gm")
      || (($user == $user_manager_acc[0] || str_contains(Auth::user()->role_code, 'S-MIS')) && count($user_manager_acc) != 4 && $investment->posisi == "manager_acc")
      || (($user == $user_direktur_acc[0]) && count($user_direktur_acc) != 4 && $investment->posisi == "direktur_acc")
      || (($user == $user_presdir[0]) && count($user_presdir) != 4 && $investment->posisi == "presdir")
      )

      <form role="form" id="myForm" method="post" action="{{url('investment/approval/'.$investment->id)}}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />  
        <table class="table table-bordered">
          <tr>
            <td colspan="12" style="font-size: 16px;padding: 0">
              <div class="col-md-4" style="padding: 10px">
                  <a data-toggle="modal" data-target="#notapproved" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject</a>
              </div>
              <div class="col-md-4" style="padding: 10px">
                  <a target="_blank" class="btn btn-info col-sm-12" href="{{ url('investment/comment/'.$investment->id) }}" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Hold & Comment</a>
              </div>
              <div class="col-md-4" style="padding: 10px">
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <input type="hidden"  name="approve" id="approve" value="1" />
                  <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Verifikasi</button>
              </div>
            </td>
          </tr>
          <br><br>
          <tr id="show-att">
            <td colspan="12" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" colspan="2" id="text_attach_1">
            </td>
          </tr>
        </table>
      </form>

    @else
      <table class="table table-bordered">
        <tr id="show-att">
          <td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" colspan="2" id="text_attach_1">
          </td>
        </tr>
      </table>
    @endif
    </div>
  </div>

  <div class="modal modal-danger fade" id="notapproved" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('investment/notapprove/'.$investment->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Give Reason Not Approve Investment</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Form Will Go Back To User
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-outline">Not Approved</a>
          </div>
        </form>
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

      var showAtt = "{{$investment->pdf}}";
      var path = "{{$file_path}}";


      if(showAtt.includes('.pdf')){
        $('#text_attach_1').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
      }

      if(showAtt.includes('.png') || showAtt.includes('.PNG')){
        $('#text_attach_1').append("<embed src='"+ path +"' width='100%' height='800px'>");
      }

      if(showAtt.includes('.jp') || showAtt.includes('.JP')){
        $('#text_attach_1').append("<embed src='"+ path +"' width='100%' height='800px'>");
      }
    });

    CKEDITOR.replace('alasan' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '250px'
    });


    function loading(){
      $("#loading").show();
    }


    document.getElementById("myForm").addEventListener("submit", loading);
    
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