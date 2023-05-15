@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  tr>th{
    text-align:center;
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
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  .ng:hover {
    background-color: #9ef598;
    cursor: pointer;
  }

  .reed:hover {
    background-color: #9ef598;
    cursor: pointer;
  }
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    INCOMING CHECK REED PLATE
    
  </h1>

  <ol class="breadcrumb">

  </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: white; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="row">
   <div class="col-xs-5 ">
    <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
        <span style="font-weight: bold; font-size: 1.5vw">Op Bensuki </span><br>
        <div class="table-responsive">
          <table>

            <tr>
              <td rowspan="2" style="padding: 2px">LOW</td>
              @foreach($lows  as $nomor => $lows)
              @if($lows->warna =="M" )
              <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $lows->nama}}" name="{{ $lows->nik}}" onclick="opben('LOW',this.id,this.name,this)">
                {{$a = explode('-', trim($lows->kode))[0]}}</button></td>
                @endif                
                @endforeach
              </tr>
              <tr>
                @foreach($low  as $nomor => $low)
                @if($low->warna =="H" )
                <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $low->nama}}" name="{{ $lows->nik}}" onclick="opben('LOW',this.id,this.name,this)">{{$a = explode('-', trim($low->kode))[0]}}</button></td>
                @endif                
                @endforeach                
              </tr>
              <tr>
                <td rowspan="2" style="padding: 2px">MIDDLE</td>
                @foreach($middles  as $nomor => $middles)
                @if($middles->warna =="M" )
                <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $middles->nama}}" name="{{ $middles->nik}}"  onclick="opben('MIDDLE',this.id,this.name,this)">
                  {{$a = explode('-', trim($middles->kode))[0]}}</button></td>
                  @endif                
                  @endforeach
                </tr>
                <tr>
                  @foreach($middle  as $nomor => $middle)
                  @if($middle->warna =="H" )
                  <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $middle->nama}}" name="{{ $middle->nik}}"onclick="opben('MIDDLE',this.id,this.name,this)">{{$a = explode('-', trim($middle->kode))[0]}}</button></td>
                  @endif                
                  @endforeach                
                </tr>

                <tr>
                  <td rowspan="2" style="padding: 2px">HIGH</td>
                  @foreach($highs  as $nomor => $highs)
                  @if($highs->warna =="M" )
                  <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $highs->nama}}" name="{{ $highs->nik}}"onclick="opben('HIGH',this.id,this.name,this)">
                    {{$a = explode('-', trim($highs->kode))[0]}}</button></td>
                    @endif                
                    @endforeach
                  </tr>
                  <tr>
                    @foreach($high  as $nomor => $high)
                    @if($high->warna =="H" )
                    <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $high->nama}}" name="{{ $highs->nik}}"onclick="opben('HIGH',this.id,this.name,this)">{{$a = explode('-', trim($high->kode))[0]}}</button></td>
                    @endif                
                    @endforeach                
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xs-7 ">
          <div class="box box-solid">        
            <div class="box-body" style="margin:0px">
              <div class="col-xs-2" style="padding: 0px; font-weight:  bold; vertical-align: middle; font-size: 1.2vw" >Model :</div>
              <div class="col-xs-10" style="padding: 0px">
                <!-- <span >Model  </span><br> -->


                @foreach($models as $model) 
                @if($model == "P-37")                
                <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  #800000; color: white">{{$model}}</button></div>
                @elseif($model == "P-32")
                <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  rgb(135,206,250); color: black">{{$model}}</button></div>
                @else
                <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  rgb(240,230,140); color: black">{{$model}}</button></div>
                @endif
                @endforeach
              </div>

              <!-- Op Reed Plate  <br> -->
              <div class="col-xs-2" style="padding: 0px; font-weight:  bold; vertical-align: middle; font-size: 1.2vw" >Op Reed Plate :</div>

              <div class="col-xs-10" style="padding: 0px 0px 5px 0px">
                @foreach($bennukis  as $nomor => $bennukis)
                <div class="col-xs-2"style="padding: 5px 5px 0px 5px; " ><button class="btn btn-LG btn-primary" onclick="opred(this.id,this,this.name)" id="{{ $bennukis->nama}}" name="{{ $bennukis->nik}}" style="width:100%; background-color: #8A2BE2">{{ $bennukis->kode}}</button></div>
                @endforeach
              </div>

              <!-- Shift<br> -->
              <div class="col-xs-2" style="padding: 0px; font-weight:  bold; vertical-align: middle; font-size: 1.2vw" >Shift :</div>

              <div class="col-xs-10" style="padding: 0px 0px 5px 0px;">
               @foreach($shifts as $shifts) 
               @if($shifts =="B")
               <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ></div>
               @else
               <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-info" onclick="shift(this.id,this)" id="{{$shifts}}" style="width:100%;">{{$shifts}}</button></div>
               @endif
               @endforeach
             </div>

             <!-- Mesin<br> -->
             <div class="col-xs-2" style="padding: 0px; font-weight:  bold; vertical-align: middle; font-size: 1.2vw" >Mesin :</div>

             <div class="col-xs-10" style="padding: 0px">
               @foreach($mesins as $mesins) 
               <div class="col-xs-2"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-success" onclick="mesin(this.id,this)" id="{{$mesins}}" style="width:100%;">{{$mesins}}</button></div>
               @endforeach
             </div>

           </div>
         </div>

       </div>
     </div>
     <input type="text" name="ng" id="ng" value="" hidden="">
     <div class="nav-tabs-custom col-xs-8">
      <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
        <li class="active" style="width: 100%"><a href="#low" data-toggle="tab"><i class="fa fa-music"></i><b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></b><b id="opbentetx" style="color:red"> [ Op Bensuki ] - </b><b class="destroy" id="posisi"></b><b class="destroy" id="opben"></b><b><b id="opredtext" style="color:red"> [ Op Reed Plate ] - </b></b><b class="destroy" id="opred"></b><b class="destroy" id="shift" style="color:red">[Shift]</b> <b class="destroy" id="mesin" style="color:red">-[Mesin]</b> <b class="destroy" id="nikbensuki" hidden></b>  <b class="destroy" id="nikplate" hidden></b> <b  class="destroy" id="kode" hidden></b>  <b class="destroy" id="kode2" hidden></b><b class="destroy" id="kodemesin" hidden></b><b class="destroy" id="kodeshift" hidden></b></a></li>
        <!-- <li style="width: 8%"><button class="btn btn-success" style="width: 100%" onclick="save();">Save</button></li>  -->
      </ul>
      <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="low" style="position: relative; ">
          <div class="box-body">

            <!--     <div class="col-xs-6" style="padding: 0px 2px 2px 0px">
                <button class="btn" style="width: 100%; background-color: #fcc55d;" onclick="select_ng('type','LOW')">LOW</button>
              </div>
              <div class="col-xs-6" style="padding: 0px 0px 2px 2px">
                <button class="btn" style="width: 100%; background-color: #fcc55d;" onclick="select_ng('type','HIGH')">HIGH</button>
              </div>  -->
              <div class="col-xs-12" style="padding: 0px; margin-bottom: 3px">
                <table class="table no-margin table-striped" border="0" style="padding: 4px">
                  <thead style="background-color: rgba(126,86,134,.7); color: white;">
                    <tr>
                      <th align="center" id="head_ng_num" style="padding: 4px">REED</th>
                    </tr>
                  </thead>
                  <tbody id="body_ng_num">
                  </tbody>
                </table>  
              </div>
              <div class="col-xs-10" style="padding: 2px 2px 2px 0px; overflow-y:hidden; overflow-x:scroll;">
                <table class="table no-margin table-striped" border="0" style="padding: 4px">
                  <thead style="background-color: rgba(126,86,134,.7); color: white;">
                    <tr>
                      <th align="center" colspan="16" style="padding: 4px">NG</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Lepas')">Lepas</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Longgar')">Longgar</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Pangkal Menempel')">Pangkal Menempel</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Panjang')">Panjang</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Melekat')">Melekat</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Ujung Menempel')">Ujung Menempel</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Lengkung')">Lengkung</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Terbalik')">Terbalik</td>
                    </tr>
                    <tr>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Celah Lebar')">Celah Lebar</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Salah Posisi')">Salah Posisi</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Kepala Rusak')">Kepala Rusak</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Patah')">Patah</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Lekukan')">Lekukan</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Kotor')">Kotor</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Celah Sempit')">Celah Sempit</td>
                      <td style="border: 1px solid black; padding: 5px 2px 5px 2px; vertical-align: middle;" class="ng" align="center" width="6.6%" onclick="select_ng('ng','Double')">Double</td>
                    </tr>
                  </tbody>
                </table>  
              </div>
              <div class="col-xs-2" style="padding: 2px 0px 2px 2px; border: 1px solid black">
                <center>
                  <!-- <b id="ng_type" style="color:red"> [ High/Low ] - </b><br> -->
                  <b id="ng_reed" style="color:red"> [ Reed ] </b><br>
                  <b id="ng_ng" style="color:red"> [ NG ] </b><br><br>
                  <button class="btn btn-primary" style="width: 100%" onclick="add()"><i class="fa fa-share-square-o"></i> Create NG</button> 
                </center>
              </div>
            </div>
          </div>
        </div>  
      </div>
      <div class="col-xs-4">
        <div class="box">
          <div class="box-header">
            <center><b>LOG NG</b></center>
          </div>
          <div class="box-body">
            <div class="col-xs-12" style="padding-right: 0px; padding-left: 5px; overflow-x:hidden; overflow-y:scroll; height: 220px">
             <table class="table no-margin table-striped" border="0" style="padding: 4px">
              <thead style="background-color: rgba(126,86,134,.7); color: white;">
                <tr>
                  <th align="center" style="padding: 4px; border: 1px solid black">REED</th>
                  <th align="center" style="padding: 4px; border: 1px solid black">NG</th>
                  <th align="center" style="padding: 4px; border: 1px solid black">Qty</th>
                </tr>
              </thead>
              <tbody id="body_log">
              </tbody>
            </table> 
          </div>
          <div class="col-xs-12">
            <button class="btn btn-success btn-md" style="width: 100%" onclick="save()"><i class="fa fa-check"></i> Save</button>
          </div>
        </div>
      </div>
    </div>



    <div class="modal modal-warning fade" id="edit">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Input</h4>
            </div>
            <div class="modal-body" >
              <input type="text" name="" class="form-control">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
              <a id="modalEditButton" href="#" type="button" class="btn btn-outline">Confirm</a>
            </div>
          </div>
        </div>
      </div>

      @endsection

      @section('scripts')
      <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
      <script >

        var ngs = [];
        var reeds = [];
        var qtys = [];

        jQuery(document).ready(function() {
          $('#entrydate').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true, 
          });
          $('body').toggleClass("sidebar-collapse");
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
        });
        // var ngar = new Array();
        // var tbl = document.getElementById("tblMain");
        // if (tbl != null) {              
        //   for (var j = 0; j < 16; j++) {
        //     tbl.rows[2].cells[j].onclick = function () { low(this,this.cellIndex); };  
        //     tbl.rows[4].cells[j].onclick = function () { low(this,this.cellIndex); };             
        //   }
        // }

        // function low(data,nomor,row) {
        //   var row = $(data).closest("tr").index();
        //   if (row == "2"){
        //     var awal = parseInt(data.innerHTML);
        //     if(awal =="0"){
        //       tbl.rows[2].cells[nomor].innerHTML ="1";
        //       tbl.rows[2].cells[nomor].style.backgroundColor = "pink";
        //       var ng = (tbl.rows[0].cells[nomor].innerHTML);                
        //       ngar.push(ng+"-"+"LOW");
        //       $('#ng').val(ngar);
        //     }else{
        //       tbl.rows[2].cells[nomor].innerHTML ="0";
        //       tbl.rows[2].cells[nomor].style.backgroundColor = "#F0FFF0";
        //       var abc = document.getElementById('ng').value.split(",");
        //       var ng = ((tbl.rows[0].cells[nomor].innerHTML)+"-"+"LOW"); 
        //       var filteredAry = abc.filter(function(e) { return e !== ng })
        //       ngar = filteredAry;
        //       $('#ng').val(ngar);
        //     }
        //   }
        //   else if (row == "4"){
        //     var awal = parseInt(data.innerHTML);
        //     if(awal =="0"){
        //       tbl.rows[4].cells[nomor].innerHTML ="1";
        //       tbl.rows[4].cells[nomor].style.backgroundColor = "pink";
        //       var ng = (tbl.rows[0].cells[nomor].innerHTML);                
        //       ngar.push(ng+"-"+"HIGH");
        //       $('#ng').val(ngar);
        //     }else{
        //       tbl.rows[4].cells[nomor].innerHTML ="0";
        //       tbl.rows[4].cells[nomor].style.backgroundColor = "#F0FFF0";
        //       var abc = document.getElementById('ng').value.split(",");
        //       var ng = ((tbl.rows[0].cells[nomor].innerHTML)+"-"+"HIGH"); 
        //       var filteredAry = abc.filter(function(e) { return e !== ng })
        //       ngar = filteredAry;
        //       $('#ng').val(ngar);
        //     }
        //   }
        // } 


        function model(id) {
          $('#modelb').text(""+id+" ");
          $('#textmodel').css({'color':'black'});

          var num = 0;
          if (id == 'P-25') {
            $("#body_ng_num").empty();
            $("#head_ng_num").attr("colspan", 9);
            $("#body_ng_num").append("<tr>");

            for (var i = 0; i < 9; i++) {
              $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

              num++;
            }

            $("#body_ng_num").append("</tr><tr>");

            for (var i = 0; i < 9; i++) {
              $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

              num++;
            }

            $("#body_ng_num").append("</tr><tr>");


            for (var i = 0; i < 7; i++) {
              $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

              num++;
            }

            $("#body_ng_num").append("</tr>");
          } else if (id == 'P-32') {
            $("#body_ng_num").empty();
            $("#head_ng_num").attr("colspan", 11);
            $("#body_ng_num").append("<tr>");

            for (var i = 0; i < 11; i++) {
              $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

              num++;
            }

            $("#body_ng_num").append("</tr><tr>");

            for (var i = 0; i < 11; i++) {
              $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

              num++;
            }

            $("#body_ng_num").append("</tr><tr>");


            for (var i = 0; i < 10; i++) {
              $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

              num++;
            }

            $("#body_ng_num").append("</tr>");
          } else if (id == 'P-37') {
           $("#body_ng_num").empty();
           $("#head_ng_num").attr("colspan", 13);
           $("#body_ng_num").append("<tr>");

           for (var i = 0; i < 13; i++) {
            $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

            num++;
          }

          $("#body_ng_num").append("</tr><tr>");

          for (var i = 0; i < 13; i++) {
            $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

            num++;
          }

          $("#body_ng_num").append("</tr><tr>");


          for (var i = 0; i < 11; i++) {
            $("#body_ng_num").append('<td class="reed" style="border: 1px solid black; padding-top: 5px; padding-bottom: 5px; width: 1%; text-align: center; font-weight: bold; font-size: 20px" onclick="select_ng(\'reed\',\'Reed '+(num+1)+'\')">'+(num+1)+'</td>');

            num++;
          }

          $("#body_ng_num").append("</tr>");
        }

      }

      function model2(id) {
        $('#modelb2').text(id);
      }

      function opben(group,id,nik,kode) {
        var code = $(kode).text().trim()+"-"+group;
        $('#opben').text(" "+id+"");
        $('#posisi').text(" "+group+"-");
        $('#nikbensuki').text(nik);
        $('#kode').text(code); 
        $('#opbentetx').css({'color':'black'});
      }

      function shift(id,kode) {

       var code = $(kode).text().trim();
       $('#shift').text("-"+id+"-");
       $('#shift').css({'color':'black'})
       $('#kodeshift').text(code);  
     }

     function mesin(id,kode) {
       var code = $(kode).text().trim();
       $('#mesin').text(" "+id+"");
       $('#mesin').css({'color':'black'})
       $('#kodemesin').text(code);  
     }

     function opred(id,kode,nik) {
      var code = $(kode).text().trim();
      $('#nikplate').text(nik);
      $('#opred').text(" "+id+"");
      $('#kode2').text(code); 
      $('#opredtext').css({'color':'black'}) 
    }

    function openSuccessGritter(title,text){
      jQuery.gritter.add({
        title: title,
        text: text,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '3000'
      });
    }

    function destroy() {
      ngar = [];
      $('#ng').val(''); 
      $('.destroy').text('');
        // for (var i = 0; i < 16; i++) {
        //   tbl.rows[4].cells[i].innerHTML ="0";
        //   tbl.rows[4].cells[i].style.backgroundColor = "#F0FFF0";
        //   tbl.rows[2].cells[i].innerHTML ="0";
        //   tbl.rows[2].cells[i].style.backgroundColor = "#F0FFF0";
        // }
      }

      function destroy2() {
        $('#modelb2').text('');
        $('#qty').val(''); 
        $('#entrydate').val('');
        // for (var i = 0; i < 16; i++) {
        //   tbl.rows[4].cells[i].innerHTML ="0";
        //   tbl.rows[4].cells[i].style.backgroundColor = "#F0FFF0";
        //   tbl.rows[2].cells[i].innerHTML ="0";
        //   tbl.rows[2].cells[i].style.backgroundColor = "#F0FFF0";
        // }

      }


      function save() {
        if ('Anda Yakin Menyimpan Data ?') {
          var model = $('#modelb').text();
          var kodebensuki = $('#kode').text(); 
          var nikbensuki = $('#nikbensuki').text();
          var kodeplate = $('#kode2').text();
          var nikplate = $('#nikplate').text(); 
          var shift = $('#kodeshift').text();
          var mesin = $('#kodemesin').text();
          var posisi =  $('#posisi').text().replace('-','');
          var a = "{{Auth::user()->name}}";
          var line = a.substr(a.length - 1); 

          var enji = [];

          $.each(reeds, function(key, value) {
            enji.push({'reed' : value, 'ng' : ngs[key], 'qty' : qtys[key]});
          });

          // if (enji.length < 1) {
          //   openErrorGritter('Error', 'Pi');
          //   return false;
          // }


          if(model == '' || kodebensuki == '' || nikbensuki == '' || kodeplate == '' || nikplate == '' || shift == '' || mesin == ''){
            alert('Semua Kolom Harap Diisi'); 
          }else{
            $("#loading").show();
            var data = {
              model:model,
              kodebensuki:kodebensuki,
              nikbensuki:nikbensuki,
              kodeplate:kodeplate,
              nikplate:nikplate,
              shift:shift,
              mesin:mesin,
              ng:enji,
              line:line,
              posisi:posisi
            }
            $.post('{{ url("index/Save") }}', data, function(result, status, xhr){
              $("#loading").hide();
              if(xhr.status == 200){
                if(result.status="oke"){                
                  openSuccessGritter('Success!','Input NG-Rate Success');
                  destroy();
                  $('#shift').text("[Shift]-");
                  $('#mesin').text("[Mesin]");
                  $('#textmodel').css({'color':'red'});
                  $('#opbentetx').css({'color':'red'});
                  $('#opredtext').css({'color':'red'});
                  $('#shift').css({'color':'red'});
                  $('#mesin').css({'color':'red'});

                  $("#body_log").empty();
                  $("#body_ng_num").empty();

                  $("#ng_reed").text(' [ Reed ] ');
                  $("#ng_ng").text(' [ NG ] ');


                  reeds = [];
                  ngs = [];
                  qtys = [];
                }
                else{
                  alert('Attempt to retrieve data failed');
                }
              }
              else{
              }
            });
          }        
        }
      }

      // function incoming() {
      //   var model = $('#modelb2').text();
      //   var qty = $('#qty').val(); 
      //   var entrydate = $('#entrydate').val();

      //   if(model == '' || qty == '' || entrydate == '' ){
      //     alert('Semua Kolom Harap Diisi');  
      //   }else{
      //     var data = {
      //       model:model,
      //       entrydate:entrydate,
      //       qty:qty,

      //     }
      //     $.post('{{ url("index/Incoming") }}', data, function(result, status, xhr){
      //       if(xhr.status == 200){
      //         if(result.status="oke"){                
      //           openSuccessGritter('Success!','Input Incoming Success');
      //           destroy2();
      //         }
      //         else{
      //           alert('Attempt to retrieve data failed');
      //         }
      //       }
      //       else{
      //       }
      //     });
      //   }           

      // }

      function select_ng(category, value) {
        if (category == 'type') {
          $("#ng_type").text("[ "+value+" ]");
        } else if (category == 'reed') {
          $("#ng_reed").text("[ "+value+" ]");
        } else if (category == 'ng') {
          $("#ng_ng").text("[ "+value+" ]");
        }
      }

      function add() {
        var txt_reed = $("#ng_reed").text();
        var txt_ng = $("#ng_ng").text();

        txt_reed = txt_reed.split('[ ')[1];
        txt_reed = txt_reed.split(' ]')[0];

        txt_ng = txt_ng.split('[ ')[1];
        txt_ng = txt_ng.split(' ]')[0];
        if (txt_reed == 'Reed' || txt_ng == 'NG') {
          openErrorGritter('Error', 'Pilih Nomor Reed atau NG');
          return false;
        }

        var ava = 0;

        for (var i = 0; i < reeds.length; i++) {
          if (reeds[i] == txt_reed && ngs[i] == txt_ng) {
            qtys[i] += 1;
            ava = 1;
          }
        }

        if (ava == 0) {
          reeds.push(txt_reed);
          ngs.push(txt_ng);
          qtys.push(1);
        }

        var txt = "";
        $("#body_log").empty();

        $.each(reeds, function(key, value) {
          txt += '<tr>';
          txt += '<td style="border: 1px solid black; padding: 2px">'+reeds[key]+'</td>';
          txt += '<td style="border: 1px solid black; padding: 2px">'+ngs[key]+'</td>';
          txt += '<td style="border: 1px solid black; padding: 2px">'+qtys[key]+'</td>';
          txt += '</tr>';
        });

        $("#body_log").append(txt);
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