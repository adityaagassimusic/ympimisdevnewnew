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
  <?php $user = STRTOUPPER(Auth::user()->username)?>
  <div class="box box-primary" class="col-xs-6">
    <div class="box-body"> 
      <table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
        <thead>
          <tr>
            <td colspan="13"><br></td>
          </tr>       
          <tr>
            <td colspan="13" style="text-align: center;font-weight: bold;font-size: 20px">REPORT MIRAI APPROVAL<br>(MIRAIの資料承認進捗の報告)</td>
          </tr>
          <tr>
            <td colspan="5"><br></td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Judul Dokumen (資料名)</td>
            <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->judul }}</td>
          </tr>

          <tr>
            <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">No Approval (承認番号)</td>
            <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->no_transaction }}</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">No Dokumen (資料番号)</td>
            <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->no_dokumen }}</td>
          </tr>
              <!-- <tr>
                <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px;">Judul Dokumen (資料名)</td>
                <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->judul }}</td>
              </tr> -->

            </tr>              
            <?php
            $identitas = explode("/",$detail->nik);
            ?> 
            <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Pembuat (作成者)</td>
              <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $identitas[0] }} - {{ $identitas[1] }}</td>
            </tr>
            <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px;">Department (課)</td>
              <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->department }}</td>
            </tr>
            <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Tanggal Pembuatan (作成日)</td>
              <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->created_at }}</td>
            </tr>
            <tr>
              @if(preg_match("/special reason/i", $detail->judul))
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Alasan (理由)</td>
              @else
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Catatan (備考)</td>
              @endif
              <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $detail->summary }}</td>
            </tr>
            @if($detail->remark == 'Rejected')
            <tr>
              <td colspan="2" style="font-size: 15px;width: 22%; font-weight: bold; border: 1px solid black; padding-left: 20px">Alasan</td>
              <td colspan="10" style="font-size: 15px; border: 1px solid black; padding-left: 20px"><?php print_r(nl2br($detail->comment)) ?></td>
            </tr>
            @endif
          </thead>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-6">
        <div class="row" style="margin:0px;">
          <div class="box box-primary">
            <div class="box-body">
              <!-- <span style="">Dokumen Pengajuan</span> -->
              <div class="row">
                <div class="col-md-12" align="center">
                  <table class="table table-bordered">
                    <tr id="show-att">
                      <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;" colspan="2" id="attach_pdf">
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="row" style="margin:0px;">
          <div class="box box-primary">
            <div class="box-body">
              <div class="row">
                <div class="col-md-12" align="center">
                  <table class="table table-bordered">
                    <tr id="show-att">
                      <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;" colspan="2" id="ttd_pdf">
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="box box-primary">
      <div class="box-body"> 
       <table class="table table-bordered">
        <tr id="show-att">
          <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;" colspan="2" id="attach_pdf">
          </td>
        </tr>
      </table>
    </div>
  </div>
  <div class="box box-primary">
    <div class="box-body"> 
     <table class="table table-bordered">
      <tr id="show-att">
        <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;" colspan="2" id="ttd_pdf">
        </td>
      </tr>
    </table>
  </div>
</div> -->
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
    var path_ttd = "{{$file_path_ttd}}";
    var ttd = "";
    if(showAtt.includes('.pdf')){
      $('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
      $('#ttd_pdf').append("<embed src='"+ path_ttd +"' type='application/pdf' width='100%' height='800px'>");
      $('#resume').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
    }
    var id = "{{ $detail->id }}";
      // selectType();
  });

  function loading(){
    $("#loading").show();
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
</script>
@stop