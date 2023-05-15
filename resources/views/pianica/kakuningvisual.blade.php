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

  b{
    font-size: 25px;
  }
  table{
    font-size: 25px;
    font-weight: bold;
  }
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <span class="text-purple"> 外観確認詳細</span>
  </h1>

  <ol class="breadcrumb">
    {{-- <a href="javascript:void(0)"  data-toggle="modal" data-target="#edit" class="btn btn-warning btn-sm">Input</a> --}}
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
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
  <input type="hidden" name="started_at" id="started_at">
  <div class="row">
    <div class="col-xs-6 ">
     <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
        <b>Tag RFID</b> 
        <b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></b><br>
        <input type="text" name="rfid" id="rfid" class="form-control"  autofocus style="text-align: center; font-size: 30px; height: 45px" placeholder="TAP RFID ITEM HERE"><br>

        <input type="text" name="rfid2" id="rfid2" hidden="">
        
        <center><button class="btn btn-lg btn-primary" onclick="rf()"><i class="fa fa-refresh"></i> Change</button></center> <br>
      </div>
    </div>

  </div>
  <div class="col-xs-6">
    <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
       <button class="btn btn-warning btn-lg pull-right" onclick="openmodal()">Change Operator Kakuning Visual</button>         
       <span class="info-box-text" style="font-size: 25px">OPERATOR Kakuning Visual</span>
       <span class="info-box-number" id="p_pureto_nama" style="font-size: 25px">[ ]</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b> 
       <div class="table-responsive">
        <table width="100%"  class="table table-bordered table-striped" border="0" style="margin :0px">
          <tr>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black;" >Total (Pcs)</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black;" >OK (Pcs)</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black;" >NG (Pcs)</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black;" >Qty NG</td>
          </tr>
          <tr>
            <td style="font-size: 45px; background-color: #F0FFF0;border: 1px solid black;" valign="middle" id="total">0</td>
            <td style="font-size: 45px; background-color: #F0FFF0;border: 1px solid black;" valign="middle" id="bagus_total">0</td>
            <td style="font-size: 45px; background-color: pink;border: 1px solid black;" valign="middle" id="ng_total_pcs">0</td>
            <td style="font-size: 45px; background-color: pink;border: 1px solid black;" valign="middle" id="ng_total">0</td>
          </tr>
        </table>
      </div>
    </div>
  </div>   
</div>
</div>

<div class="row">
  <div class="col-xs-12">
    <div class="box box-solid">
      <div class="box box-body"> 
       <!-- Custom Tabs -->
       <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" style="font-size: 25px;">
          <li class="active" style="width: 14%"><center><a href="#tab_1" data-toggle="tab" style="color: black" >Frame Assy</a></center></li>
          <li style="width: 14%;"><center><a style="color: black" href="#tab_2" data-toggle="tab">Cover R/L</a></center></li>
          <li style="width: 14%;"><center><a style="color: black" href="#tab_3" data-toggle="tab">Cover Lower</a></center></li>
          <li style="width: 14%;"><center><a style="color: black" href="#tab_4" data-toggle="tab">Handle</a></center></li>
          <li style="width: 14%;"><center><a style="color: black" href="#tab_5" data-toggle="tab">Button</a></center></li>
          <li style="width: 14%;"><center><a style="color: black" href="#tab_6" data-toggle="tab">Pianica</a></center></li>

          <li ><button class="btn btn-lg btn-success pull-right" onclick="simpan()"><i class="fa fa-save"></i>&nbsp;Save</button></a></li>


        </ul>
        <div class="tab-content">

          <div class="tab-pane active" id="tab_1">
            <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span> -->
            <input id="ng" hidden></input><br>
            <div class="table-responsive">
              <table class="table no-margin table-bordered table-striped" border="0" id="Frame1">
               <tr style="background-color: rgba(126,86,134,.7); color: white;" >
                @foreach($ng_list as $nomor => $ng)
                @if($ng->location =="Frame Assy" && $loop->index <=9)                
                <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$nomor+1}}</th>
                @endif
                @endforeach                                  
              </tr> 
              <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
                @foreach($ng_list as $nomor => $ng)
                @if($ng->location =="Frame Assy" && $loop->index <=9)                
                <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
                @endif
                @endforeach                                  
              </tr>              
              <tr style="background-color: rgba(126,86,134,.7); color: white;" >
                @foreach($ng_list as $nomor => $ng)
                @if($ng->location =="Frame Assy" && $loop->index <=9)                
                <th  align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
                @endif
                @endforeach                                  
              </tr>
              <tr style="background-color: #F0FFF0">
                @foreach($ng_list as $nomor => $ng)
                @if($ng->location =="Frame Assy" && $loop->index <=9)
                <td Style="font-size: 45px" valign="middle" class="f1">0</td>
                @endif
                @endforeach                                  
              </tr>                
            </table> <BR>

            <table class="table no-margin table-bordered table-striped" border="0" id="Frame2">
             <tr style="background-color: rgba(126,86,134,.7); color: white;" >
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index >9)                
              <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$nomor+1}}</th>
              @endif
              @endforeach                                  
            </tr>  
            <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index >9)                
              <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
              @endif
              @endforeach                                  
            </tr>                 
            <tr style="background-color: rgba(126,86,134,.7); color: white;" >
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index >9)                
              <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
              @endif
              @endforeach                                  
            </tr>
            <tr style="background-color: #F0FFF0">
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index >9)
              <td Style="font-size: 45px" valign="middle" class="f1">0</td>
              @endif
              @endforeach                                  
            </tr>                
          </table> <BR> 

        </div>
      </div>

      <!-- /.tab-pane -->
      <div class="tab-pane" id="tab_2">
       <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
       <div class="table-responsive">
        <table class="table no-margin table-bordered table-striped" border="0" id="r/l1">
         <tr style="background-color: rgba(126,86,134,.7); color: white;" >
          @foreach($ng_list as $nomor => $ng)
          @if($ng->location =="Cover R/L" && $loop->index <=28)                
          <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{($nomor-19)+1}}</th>
          @endif
          @endforeach                                  
        </tr>
        <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
          @foreach($ng_list as $nomor => $ng)
          @if($ng->location =="Cover R/L" && $loop->index <=28)                
          <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
          @endif
          @endforeach                                  
        </tr>               
        <tr style="background-color: rgba(126,86,134,.7); color: white;" >
          @foreach($ng_list as $nomor => $ng)
          @if($ng->location =="Cover R/L" && $loop->index <=28)                
          <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
          @endif
          @endforeach                                  
        </tr>
        <tr style="background-color: #F0FFF0">
          @foreach($ng_list as $nomor => $ng)
          @if($ng->location =="Cover R/L" && $loop->index <=28)
          <td Style="font-size: 45px" valign="middle" class="f1">0</td>
          @endif
          @endforeach                                  
        </tr>                
      </table> <BR>

      <table class="table no-margin table-bordered table-striped" border="0" id="r/l2">
       <tr style="background-color: rgba(126,86,134,.7); color: white;" >
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index >28)                
        <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{($nomor-19)+1}}</th>
        @endif
        @endforeach                                  
      </tr>
      <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index >28)                
        <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
        @endif
        @endforeach                                  
      </tr>                   
      <tr style="background-color: rgba(126,86,134,.7); color: white;" >
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index >28)                
        <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
        @endif
        @endforeach                                  
      </tr>
      <tr style="background-color: #F0FFF0">
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index >28)
        <td Style="font-size: 45px" valign="middle" class="f1">0</td>
        @endif
        @endforeach                                  
      </tr>                
    </table> <BR> 

  </div>
</div>

<!-- /.tab-pane -->
<div class="tab-pane" id="tab_3">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="lower1">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)                
      <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{($nomor-39)+2}}</th>
      @endif
      @endforeach                                  
    </tr> 
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)                
      <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>              
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)                
      <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>

  <table class="table no-margin table-bordered table-striped" border="0" id="lower2">
   <tr style="background-color: rgba(126,86,134,.7); color: white;" >
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)                
    <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{($nomor-39)+2}}</th>
    @endif
    @endforeach                                  
  </tr>
  <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)                
    <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
    @endif
    @endforeach                                  
  </tr>                   
  <tr style="background-color: rgba(126,86,134,.7); color: white;" >
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)                
    <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
    @endif
    @endforeach                                  
  </tr>
  <tr style="background-color: #F0FFF0">
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)
    <td Style="font-size: 45px" valign="middle" class="f1">0</td>
    @endif
    @endforeach                                  
  </tr>                
</table> <BR> 

</div>
</div>

<!-- /.tab-pane -->
<div class="tab-pane" id="tab_4">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Handle">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{($nomor-55)+2}}</th>
      @endif
      @endforeach                                  
    </tr>   
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>            
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>
</div>
</div>

<!-- /.tab-pane -->
<div class="tab-pane" id="tab_5">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Button">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{($nomor-61)+2}}</th>
      @endif
      @endforeach                                  
    </tr>    
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>           
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>
</div>
</div>


<!-- /.tab-pane -->
<div class="tab-pane" id="tab_6">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Pianica">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)                
      <th align="center" width="{{(100/3)}}%" style="vertical-align: middle;">{{($nomor-67)+2}}</th>
      @endif
      @endforeach                                  
    </tr>     
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)                
      <th align="center" width="{{(100/3)}}%" style="vertical-align: middle; ">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>          
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)                
      <th align="center" width="{{(100/3)}}%" style="vertical-align: middle; ">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>
</div>
</div>
<!-- /.tab-pane -->
</div>
<!-- /.tab-content -->
</div>
<!-- nav-tabs-custom -->
</div>            
</div>
</div>
</div>

<div class="modal modal-default fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><center>YOUR RFID</center></h4>
      </div>
      <div class="modal-body" >
        <input type="text" name="oppureto" id="oppureto"  class="form-control" autofocus style="text-align: center;  font-size: 30px; height: 45px" placeholder="TAP RFID USER CARD HERE">
        <center><span style="color: red;font-weight: bold;font-size: 20px" id="pesan_skill"></span></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="display: none" id="ubahpureto2">Close</button>
        <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()" ><i class="fa fa-refresh"></i> Change</button>

        {{-- <a id="modalEditButton" href="#" type="button" class="btn btn-outline">Confirm</a> --}}
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="modal_model" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><center style="font-color: red">*NEW LABEL 3 MAN INFORMATION*</center></h4>
      </div>
      <div class="modal-body" >
        <div class="row">
          <div class="col-xs-4">
            <button class="btn btn-primary" onclick="selectSeri(this)" style="width: 100%">P-37EBR</button>
            <br>
          </div>
          <div class="col-xs-4">
            <button class="btn btn-primary" onclick="selectSeri(this)" style="width: 100%">P-37EBK</button>
            <br>
          </div>
          <div class="col-xs-4">
            <button class="btn btn-primary" onclick="selectSeri(this)" style="width: 100%">P-37ERD</button>
            <br>
          </div>

          <div class="col-xs-12" id="three_man_label" style="display: none">
            <div class="form-group">
              <br>
              <label class="col-sm-3 control-label">Product Seri : </label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="seri" placeholder="Product Seri" readonly>
              </div>
            </div>

            <label class="col-sm-3 control-label">Label Position : </label>
            <img src="#" id="img_label" style="max-width: 50%">

            <div>
              <label>Evidence : </label>
              <input type="file" onchange="readURL(this,'');" id="file_label" style="display:none;width: 100%; height: 40px; font-size: 25px; text-align: center;" accept="image/*" capture="user" class="file">
              <br>
              <center><img id="blah_label" src="" style="display: none;width: 40%" alt="your image" /></center>
              <br>
              <button class="btn btn-primary btn-xs" id="btnImage_label" value="Photo" onclick="buttonImage(this)" style="width: 100%; font-size: 25px; text-align: center;"><i class="fa fa-camera"></i> Photo</button>
              <br><br>

              <button id="confirm_eff" href="#" type="button" class="btn btn-success btn-xs" style="width: 100%; font-size: 25px; text-align: center;" onclick="save_eff()"><i class="fa fa-check"></i> Confirm</button>

            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="display: none" id="ubahpureto2">Close</button>
        <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()" >Change</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script >

  var three_man = [];

  jQuery(document).ready(function() {
    $('#started_at').val('');
    gettotalng();
    $('#pesan_skill').html('');
    $('#oppureto').val("");
    $('#rfid').val("");
    $('#ubahpureto').css({'display' : 'none'})
    $('#edit').modal({backdrop: 'static', keyboard: false});
    $('#edit').modal('show');
    $('#edit').on('shown.bs.modal', function() {
      $('#oppureto').focus();
    })
    $('#entrydate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
    });
    $('body').toggleClass("sidebar-collapse");
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  });

  
  var ngar = new Array();
  var Frame1 = document.getElementById("Frame1");   
  if (Frame1 != null) {              
    for (var j = 0; j < 10; j++) {
      Frame1.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Frame1); };  

    }
  } 

  var Frame2 = document.getElementById("Frame2");   
  if (Frame2 != null) {              
    for (var j = 0; j < 9; j++) {
      Frame2.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Frame2); };  

    }
  }

  var rl1 = document.getElementById("r/l1");   
  if (rl1 != null) {              
    for (var j = 0; j < 10; j++) {
      rl1.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,rl1); };  

    }
  }
  var rl2 = document.getElementById("r/l2");   
  if (rl2 != null) {              
    for (var j = 0; j < 9; j++) {
      rl2.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,rl2); };  

    }
  }

  var lower1 = document.getElementById("lower1");   
  if (lower1 != null) {              
    for (var j = 0; j < 8; j++) {
      lower1.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,lower1); };  

    }
  }

  var lower2 = document.getElementById("lower2");   
  if (lower2 != null) {              
    for (var j = 0; j < 8; j++) {
      lower2.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,lower2); };  

    }
  }
  var Handle = document.getElementById("Handle");   
  if (Handle != null) {              
    for (var j = 0; j < 6; j++) {
      Handle.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Handle); };  

    }
  }
  var Button = document.getElementById("Button");   
  if (Button != null) {              
    for (var j = 0; j < 6; j++) {
      Button.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Button); };  

    }
  }
  var Pianica = document.getElementById("Pianica");   
  if (Pianica != null) {              
    for (var j = 0; j < 3; j++) {
      Pianica.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Pianica); };  

    }
  }

  function low(data,nomor,row) {

    var tbl=row;
    
    var row = $(data).closest("tr").index();

    var Akhir = parseInt(data.innerHTML);
    if(Akhir =="0"){
      tbl.rows[3].cells[nomor].innerHTML ="1";
      tbl.rows[3].cells[nomor].style.backgroundColor = "pink";
      var ng = (tbl.rows[1].cells[nomor].innerHTML);                
      ngar.push(ng);
      $('#ng').val(ngar);

    }else{
      tbl.rows[3].cells[nomor].innerHTML ="0";
      tbl.rows[3].cells[nomor].style.backgroundColor = "#F0FFF0";
      var abc = document.getElementById('ng').value.split(",");
      var ng = tbl.rows[1].cells[nomor].innerHTML; 
      var filteredAry = abc.filter(function(e) { return e !== ng })
      ngar = filteredAry;
      $('#ng').val(ngar);

    }           

  }  




  $('#oppureto').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      // if($("#oppureto").val().length == 10){
        pureto(); 
        getpureto();
        return false;
      // }
      // else{
      //   $("#oppureto").val("");
      //   alert('Error!', 'RFID number invalid.');
      // }
    }
  }); 

  $('#rfid').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#rfid").val().length == 10){
        var id = $('#rfid').val();           
        $('#p_rfid').text(id);
        $('#rfid2').val(id);
        getmodel();
               // alert("aa");            
               return false;
             }
             else{
              $("#rfid").val("");
              alert('Error!', 'RFID number invalid.');
            }
          }
        });

  function rf() {            
    $('#rfid').val("");
    $('#rfid').removeAttr('disabled');
    $('#rfid').focus();
    $('#p_rfid').text("[ ]");
  } 


  function openmodal() {
    $('#pesan_skill').html('');
    $('#ubahpureto2').css({'display' : 'block'})
    $('#ubahpureto').css({'display' : 'block'})
    $('#edit').modal('show');
    $('#oppureto').prop('disabled', true);
  }         

  function pureto() {
    var pureto = $('#oppureto').val();
    $('#p_pureto').text(pureto);
  }       

  function openpureto() {
    $('#oppureto').val("");
    $('#oppureto').removeAttr('disabled');
    $('#oppureto').focus();
  }


  function simpan() {
    $('#loading').show();
    var tag = $('#rfid').val();
    var model = $('#modelb').text();
    var op = $('#p_pureto_nik').text();

    var a = "{{Auth::user()->name}}";
    var line = a.substr(a.length - 1);
    var location ="PN_Kakuning_Visual";
    var qty = 1;
    var status = 1;
    var ng = $('#ng').val();

    if(tag == ''){
      $('#loading').hide();
      alert('All field must be filled'); 
      $('#rfid').focus(); 
    }else{
      var data = {
        tag:tag,
        model:model,
        op:op,        
        line:line,
        location:location,
        qty:qty,
        started_at:$('#started_at').val(),
        status:status,
        ng:ng,
      }
      $.post('{{ url("index/SaveKakuningVisual") }}', data, function(result, status, xhr){

        if(xhr.status == 200){
          if(result.status){
           $('#rfid').val("");
           $('#started_at').val('');
           $('#rfid').removeAttr('disabled');
           $('#rfid').focus();
           $('#ng').val('');
           ngar = [];
           var x = document.getElementsByClassName("f1");

           three_man = result.three_man_label;


           for (var i = 0; i < x.length; i++) {
             x[i].innerHTML = "0";
             x[i].style.backgroundColor = "#F0FFF0";
                      // Frame1.rows[3].cells[i].innerHTML ="0";
                      // Frame1.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Frame2.rows[3].cells[i].innerHTML ="0";
                      // Frame2.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // rl1.rows[3].cells[i].innerHTML ="0";
                      // rl1.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // rl2.rows[3].cells[i].innerHTML ="0";
                      // rl2.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // lower1.rows[3].cells[i].innerHTML ="0";
                      // lower1.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // lower2.rows[3].cells[i].innerHTML ="0";
                      // lower2.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Handle.rows[3].cells[i].innerHTML ="0";
                      // Handle.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Button.rows[3].cells[i].innerHTML ="0";
                      // Button.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Pianica.rows[3].cells[i].innerHTML ="0";
                      // Pianica.rows[3].cells[i].style.backgroundColor = "#F0FFF0";
                    }
                    openSuccessGritter('Success!', result.message);
                    gettotalng();
                    deleteInv();

                    if ($("#modelb").text() == 'P-37') {
                      // $("#modal_model").modal('show');
                    }
                    $('#loading').hide();
                  }
                  else{
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                  }
                }
                else{
                  $('#loading').hide();
                  alert("Disconnected from server");
                }
              });
    }        
  }

  function save_eff() {
    if (typeof $('#file_label').prop('files')[0] === 'undefined') {
      openErrorGritter('Error!', 'Please Upload Evidence Label');
      return false;
    }

    var formData = new FormData();

    formData.append('material_number', $("#modelb").text());
    formData.append('material_description',  $("#seri").val());
    formData.append('file_evidence', $('#file_label').prop('files')[0]);

    $("#loading").show();

    $.ajax({
      url: "{{ url('post/label/kakuningVisual') }}",
      method:"POST",
      data:formData,
      dataType:'JSON',
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        if(response.status){
          $('#file_label').hide();
          $('#blah_label').hide();
          // $('#btnImage_label').show();
          $('#three_man_label').hide();

          $("#modal_model").modal('hide');

          $("#loading").hide();
          $('#rfid').focus();
          openSuccessGritter('Success', 'Label Successfully Saved');
        }else{
          $("#loading").hide();
          openErrorGritter('Error!', response.message);
        }
      },
      error: function (response) {
        openErrorGritter('Error!', response.message);
      },
    })
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


  function getpureto() {
   var pureto = $('#oppureto').val();
   var data ={
    pureto:pureto,
    op:'Kakuning Visual',
  }
  $('#loading').show();
  $.get('{{ url("index/op_Pureto") }}', data, function(result, status, xhr){

    if(xhr.status == 200){
      if(result.status){
        $('#p_pureto_nama').text(result.nama);
        $('#p_pureto_nik').text(result.nik);
        $('#edit').modal('hide');
        $('#rfid').focus();
        $('#loading').hide();
        $('#started_at').val('');
            // $('#tag_material').val(result.tag);
            openSuccessGritter('Success!', result.message);
          }
          else{
            $('#started_at').val('');
            if (result.pesan_skill.length > 0) {
              $('#pesan_skill').html(result.pesan_skill);
            }
            $('#loading').hide();
            $('#oppureto').val("");
            // $('#oppureto').removeAttr('disabled');
            $('#oppureto').focus();
            openErrorGritter('Error!', result.message);
          }
        }
        else{
          $('#loading').hide();
          alert("Disconnected from server");
        }
      });

}

function getmodel() {
  $('#loading').show();
 var tag = $('#rfid').val();
 var data ={
  tag:tag,            
}
$.get('{{ url("index/model") }}', data, function(result, status, xhr){

  if(xhr.status == 200){
    if(result.status){
      $('#rfid').prop('disabled', true);
      $('#modelb').text(result.model);
      $('#p_model').text(result.model);
      $('#started_at').val(result.started_at);
      $('#textmodel').css({'color':'black'})        
      openSuccessGritter('Success!', result.message);
      $('#loading').hide();
    }
    else{
      $('#started_at').val('');
      $('#loading').hide();
      $('#rfid').val("");
      $('#rfid').removeAttr('disabled');
      $('#rfid').focus();
      openErrorGritter('Error!', result.message);
    }
  }
  else{

    alert("Disconnected from server");
  }
});

}

function gettotalng() {
 var tag = $('#rfid').val();
 var a = "{{Auth::user()->name}}";
 var line = a.substr(a.length - 1);
 var data ={
  location:'PN_Kakuning_Visual',
  line:line

}
$.get('{{ url("index/TotalNg") }}', data, function(result, status, xhr){

  if(xhr.status == 200){
    if(result.status){
      $('#total').text(result.total[0].total);
      $('#bagus_total').text(result.total[0].total - result.total[0].ng_pcs);
      $('#ng_total_pcs').text(result.total[0].ng_pcs);
      $('#ng_total').text(result.total[0].ng);
              // $('#biri').text(result.model[0].total);
              // $('#oktaf').text(result.model[1].total);
              // $('#rendah').text(result.model[2].total);
              // $('#tinggi').text(result.model[3].total);
                // alert(result.model[0].total)
                // $('#textmodel').css({'color':'black'})        
                openSuccessGritter('Success!', result.message);

              }
              else{                
                openErrorGritter('Error!', result.message);
              }
            }
            else{

              alert("Disconnected from server");
            }
          });

}

function deleteInv() {
 var tag = $('#rfid2').val();
 var data = {
  tag:tag,
}
$.post('{{ url("index/deleteInv") }}', data, function(result, status, xhr){

  if(xhr.status == 200){
    if(result.status){

    }
    else{

    }
  }
  else{

    alert("Disconnected from server");
  }
});
}

function selectSeri(elem) {
  var txt = $(elem).text();
  $("#seri").val(txt);

  $("#three_man_label").show();

  $.each(three_man, function(key, value) {
    if (value.material_description == txt) {
      $("#img_label").attr("src", 'http://10.109.52.1:887/miraidev/public/files/label/three_man/'+value.label_picture);

    }
  })
}

function buttonImage(elem) {
  $(elem).closest("div").find("input").click();
}

function readURL(input,idfile) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      var img = $(input).closest("div").find("img");
      $(img).show();
      $(img).attr('src', e.target.result);
    };

    reader.readAsDataURL(input.files[0]);
  }

  // $('#btnImage_label').hide();
}





</script>
@stop