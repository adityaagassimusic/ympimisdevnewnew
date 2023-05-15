@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">

<style type="text/css">

  .reqerror {color: #FF0000;}

  table.table-bordered{
    border:1px solid rgb(150,150,150);
  }
  table.table-bordered > thead > tr > th{
    border:1px solid rgb(54, 59, 56) !important;
    text-align: center;
    background-color: #212121;  
    color:white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(54, 59, 56);
    background-color: #eeeeee;
    /*color: white;*/
    vertical-align: middle;
    text-align: center;
    padding:3px;
  }
  table.table-condensed > thead > tr > th{   
    color: black
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(150,150,150);
    padding:0;
  }
  table.table-bordered > tbody > tr > td > p{
    color: #abfbff;
  }

  table.table-striped > thead > tr > th{
    border:1px solid black !important;
    text-align: center;
    background-color: rgba(126,86,134,.7) !important;  
  }

  table.table-striped > tbody > tr > td{
    border: 1px solid #eeeeee !important;
    border-collapse: collapse;
    color: black;
    padding: 3px;
    vertical-align: middle;
    text-align: center;
    background-color: white;
  }

  .radio {
    display: inline-block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 16px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  /* Hide the browser's default radio button */

  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  td:hover {
    overflow: visible;
  }
  table > thead > tr > th{
    border:2px solid #f4f4f4;
    color: white;
  }
  td>span{
    color: black !important;
  }

  td>p{
    color: black !important;
  }
  #tabelmonitor{
    font-size: 0.9vw;
  }

  .zoom{
   -webkit-user-select: none;
   -moz-user-select: none;
   -ms-user-select: none;
   -webkit-animation: zoomin 5s ease-in infinite;
   animation: zoomin 5s ease-in infinite;
   transition: all .5s ease-in-out;
   overflow: hidden;
 }
 @-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
} /*End of Zoom in Keyframes */

/* Zoom out Keyframes */
@-webkit-keyframes zoomout {
  0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}
@keyframes zoomout {
  0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}/*End of Zoom out Keyframes */


.label{
  padding:0 ;
}

hr { background-color: red; height: 1px; border: 0; }
#loading, #error { display: none; }
   /* #container1 {
      height: 400px;
      }*/

    </style>
    @endsection
    @section('header')
    <section class="content-header">
      <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 40%;">
          <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
        </p>
      </div>
      @if (session('error'))
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Error!</h4>
        {{ session('error') }}
      </div>   
      @endif
      @if (session('status'))
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Success!</h4>
        {{ session('status') }}
      </div>   
      @endif
      <h1 class="pull-left" style="padding: 0px; margin: 0px;">Pelaporan Kanagata Retak Control & Monitoring<span class="text-purple"> (金型割れの報告、管理、モニタリング)</span></h1>

      <h1>
        <a data-toggle="modal" onclick="createModal()" class="btn btn-success pull-right" style="margin-left: 10px;"><i class="fa fa-pencil-square-o"></i> Buat Pelaporan<span</span></a>
          <a data-toggle="modal" href="{{ url("history/lifeshoot")}}" class="btn btn-info pull-right" style="margin-left: 10px;"><i class="fa fa-pencil-square-o"></i> Resume Life Time<span</span></a>
          </h1>
          <br>

          <br>


        </section>
        @endsection

        @section('content')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <section class="content" style="padding-top: 0; padding-bottom: 0">
          <div class="row">
            <div class="col-md-12">
             <div class="col-xs-2" style="padding-bottom: 8px">
              <div class="input-group date">
                <div class="input-group-addon bg-green" style="border: none;">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datepicker" id="dateto" placeholder="Pilih Bulan" onchange="fillChartdraf()">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="col-md-12" style="margin-top: 5px;">
             <div id="container1" style="width: 100%"></div>
             <div class="col-xs-2" style="padding-left: 0; padding-bottom: 10px; padding-top: 10px">
              <select class="form-control select4" id="pic_progress" name="pic_progress" data-placeholder="PIC On Progress" onchange="fillList()">
                <option value="">&nbsp;</option>
                @foreach($pic as $pic)
                <option value="{{$pic->approver_name}}">{{$pic->approver_name}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-xs-12 pull-left">
            <div style="background-color: #3f51b5;color: white;padding: 5px;text-align: center;margin-bottom: 8px">
              <span style="font-weight: bold;font-size: 26px">IN PROGRESS</span>
            </div>
          </div>

          <div class="col-md-12" style="padding-top: 10px;">
            <table id="tableLeave" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;font-size: 0.9vw;">
              <thead style="background-color: rgb(126,86,134); color: #fff;">
                <tr>
                  <th width="1%">Request ID</th>
                  <th width="1%">Req Date</th>
                  <th width="5%">Title</th>
                  <th width="0.1%">Retak Ke</th>
                  <th width="1%">Status</th>
                  <th width="2%" style="background-color: #3064db">Applicant</th>
                  <th width="2%" style="background-color: #3064db">Staff Prod</th>
                  <th width="2%" style="background-color: #3064db">Staff PE</th>
                  <th width="2%" style="background-color: #3064db">Foreman</th>
                  <th width="2%" style="background-color: #3064db">Manager Prod</th>
                  <th width="2%" style="background-color: #3064db">Chief PE</th>
                  <th width="2%" style="background-color: #3064db">Manager PE</th>
                  <th width="3%" style="background-color: #3064db">Manager Japanese Speacialist PE</th>
                  <th width="2%" style="background-color: #e0ba46">Reason</th>
                  <th width="3%">Action</th>
                </tr>
              </thead>
              <tbody id="bodyTableLeave">
              </tbody>
            </table>
          </div>

          <div class="col-xs-12 pull-left">
            <div style="background-color: #32a852;color: white;padding: 5px;text-align: center;margin-bottom: 8px; margin-top: 10px;">
              <span style="font-weight: bold;font-size: 26px">COMPLETED</span>
            </div>
          </div>
          <div class="col-md-12" style="padding-top: 10px; overflow-x: scroll;">
            <table id="tableLeaveFinish" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;font-size: 0.9vw;">
              <thead style="background-color: rgb(126,86,134); color: #fff;">
                <tr>
                  <th width="1%">Request ID</th>
                  <th width="1%">Req Date</th>
                  <th width="5%">Title</th>
                  <th width="0.1%">Retak Ke</th>
                  <th width="1%">Status</th>
                  <th width="2%" style="background-color: #3064db">Applicant</th>
                  <th width="2%" style="background-color: #3064db">Staff Prod</th>
                  <th width="2%" style="background-color: #3064db">Staff PE</th>
                  <th width="2%" style="background-color: #3064db">Foreman</th>
                  <th width="2%" style="background-color: #3064db">Manager Prod</th>
                  <th width="2%" style="background-color: #3064db">Chief PE</th>
                  <th width="2%" style="background-color: #3064db">Manager PE</th>
                  <th width="3%" style="background-color: #3064db">Manager Japanese Speacialist PE</th>
                  <th width="2%" style="background-color: #e0ba46">Decision</th>
                  <th width="2%" style="background-color: #e0ba46">Reason</th>
                  <th width="3%">Action</th>
                </tr>
              </thead>
              <tbody id="bodyTableLeaveFinish">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Reff Number</th>
                    <th>Submission Date</th>
                    <th>Department</th>
                    <th>Applicant</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Vendor</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalInv">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table2"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tabelInv" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Nomor Investment</th>
                    <th>Tanggal Pengajuan</th>
                    <th>No Item</th>
                    <th>Detail</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Dollar</th> 
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalActual">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tabelActual" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Reff Number</th>
                    <th>Nomor PO</th>
                    <th>Tanggal PO</th>
                    <th>Item</th>
                    <th>Supplier</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Budget No</th>
                    <th>Qty</th>
                    <th>Total Receive</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

</section>

<form id ="importForm" name="importForm" method="post" action="{{ url('create/pelaporan/kanagata') }}" enctype="multipart/form-data">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Buat Pelaporan Kanagata Retak</h4>
          <br>
          <div class="nav-tabs-custom tab-danger">
            <ul class="nav nav-tabs">
              <li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Informasi Kanagata</a></li>
              <li class="vendor-tab disabledTab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Photo Kanagata & Material</a></li>
              <li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Repair Informasi</a></li>
            </ul>
          </div>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                   <div class="form-group">
                    <label id="label_section">Applicant<span class="text-red">*</span></label>
                    <input type="text" id="form_identitas" name="form_identitas" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}} " readonly>
                    <input type="hidden" id="emp_id" name="emp_id" class="form-control" value="{{$employee->employee_id}}" readonly>
                  </div>

                  <div class="form-group" id="new_deskripsi">
                    <label id="label_group">Problem Decription<span class="text-red">*</span></label>
                    <select class="form-control new_deskripsi" id="problem_desc" name="problem_desc" data-placeholder='Pilih Problem Decription' style="width: 100%">
                      <option value="">&nbsp;</option>
                      <option value="Kanagata Retak">Kanagata Retak</option>
                      <option value="Kanagata Pecah">Kanagata Pecah</option>
                      <option value="Kanagata Aus">Kanagata Aus</option>
                    </select>
                  </div>

                  <div class="form-group" id="selectProcess">
                    <label id="label_group">Type Process<span class="text-red">*</span></label>
                    <select class="form-control selectProcess" id="type_proses" name="type_proses"  data-placeholder='Pilih Type Process' style="width: 100%" onchange="getTypes('creates')">
                      <option value="">&nbsp;</option>
                      <option value="Forging">Forging</option>
                      <option value="Bending">Bending</option>
                      <option value="Hiraoshi">Hiraoshi</option>
                      <option value="Triming">Triming</option>
                      <option value="Blank Nuki">Blank Nuki</option>
                      <option value="Nukishibori">Nukishibori</option>
                    </select>
                  </div>


                  <div class="form-group" id="selectGmc">
                    <label>GMC Material<span class="text-red">*</span></label>
                    <select class="form-control selectGmc" id="gmc_material" name="gmc_material" data-placeholder='Pilih GMC' style="width: 100%" onchange="checkGMC(this.value,'creates')">
                      <option value="">&nbsp;</option>
                      @foreach($gmc as $row)
                      <option value="{{$row->gmc_material}}">{{$row->gmc_material}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label id="label_section">Description Material<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="desc_material" name="desc_material" placeholder="Description Material" readonly>
                    <!-- <input type="hidden" class="form-control" id="lifetimes" name="lifetimes" placeholder="lifetimes" readonly> -->
                  </div>
                  <div class="form-group">
                    <label id="label_section">Description Product<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="desc_product" name="desc_product" placeholder="Description Product" readonly>
                  </div>
                  
                  <div class="form-group" id="selectDie">
                    <label id="label_group">Type Die<span class="text-red">*</span></label>
                    <select class="form-control selectDie" id="type_die" name="type_die" data-placeholder='Pilih Type Die' style="width: 100%">
                      <option value="">&nbsp;</option>
                      <option value="DIE">DIE</option>
                      <option value="PUNCH">PUNCH</option>
                      <option value="GUIDE PLATE">GUIDE PLATE</option>
                      <option value="DIE PLATE">DIE PLATE</option>
                      <option value="PUNCH PLATE">PUNCH PLATE</option>
                      <option value="STRIPPER PLATE">STRIPPER PLATE</option>
                      <option value="KNOCK OUT">KNOCK OUT</option>
                      <option value="DRAWING PUNCH">DRAWING PUNCH</option>
                      <option value="DRAWING DIE">DRAWING DIE</option>
                      <option value="SNAP RING">SNAP RING</option>
                      <option value="LOWER KNOCK OUT">LOWER KNOCK OUT</option>
                      <option value="UPPER KNOCK OUT">UPPER KNOCK OUT</option>
                    </select>
                  </div>


                  <div class="form-group">
                    <label id="label_group">No Die<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="no_die" name="no_die" placeholder="No Die" onkeyup="myFunction('create')">
                  </div>
                  <div class="form-group" id="making_dates">
                    <label id="label_section">Molding Production Date<span class="text-red">*</span></label>
                    <input type="text" class="form-control datepicker2" id="making_date" name="making_date" placeholder="Pilih Molding Production Date">
                  </div>
                  <div class="form-group">
                    <label id="labeldept">Total Shoot<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad' id="total_shoot" name='total_shoot' style="background-color: white;" onchange="checkLifetime('create')" placeholder='0'>
                  </div>
                  <div class="form-group" id="time_life">
                    <label id="labeldept">Target Total Shoot</label>
                    <input type='text' class='form-control' id="lifetimes" name='lifetimes' placeholder='0' readonly>
                    <input type='hidden' class='form-control' id="request_status_lifetime" name='request_status_lifetime' placeholder='0' readonly>
                  </div>
                  <span class="reqerror" id="request_status"></span>
                  

                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Incident Date<span class="text-red">*</span></label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <!-- value="<?= date('Y-m-d') ?>" -->
                      <input type="text" class="form-control pull-right datepicker2" id="tanggal_kejadian" name="tanggal_kejadian"  placeholder="Incident Date" value="<?= date('Y-m-d') ?>" >
                    </div>
                  </div>

                  <div class="form-group">
                    <label id="labelposition">Spare Die<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad1' id="spare_die" name='spare_die' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group" id="forging_kes">
                    <label id="labelposition">Forging Ke<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad2' id="forging_ke" name='forging_ke' style="background-color: white;" placeholder='0' required=''>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Die High<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad3' id="die_high" name='die_high' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group" id="limitpeak">
                    <label id="labelposition">Limit Preasure / Peak<span class="text-red">*</span></label>
                    <div class="col-md-12">
                      <div class="col-md-5">
                        <input type="text" class="form-control numpad5" id="limit_preasure" name="limit_preasure" style="background-color: white;" placeholder="0">
                      </div>
                      <div class="col-md-1">
                        <h4>/</h4>
                      </div>
                      <div class="col-md-5">
                        <input type="text" class="form-control numpad6" id="peak" name="peak" style="background-color: white;" placeholder="0">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Cavity<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad4' id="cavity" name='cavity' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Retak Ke : <span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad8' id="retak_ke" name='retak_ke' style="background-color: white;" placeholder='0'>
                  </div>
                <!--   <div class="form-group">
                    <label id="labelposition">Comment : <span class="text-red">*</span></label>
                    <textarea class="form-control" id="comment_create" name="comment_create" placeholder="Enter Comment" style="width: 100%"> </textarea>
                  </div> -->
                  <div class="form-group" >
                    <label id="labelposition">Comment : <span class="text-red">*</span></label>
                    <textarea class="form-control" id="comment_create" name="comment_create" placeholder="Enter Comment"></textarea>
                  </div>

                </div>
              </div>
              <div class="col-md-12">
                <a class="btn btn-info btnNext pull-right">Lanjut</a>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="tab_2">
            <div class="row">
              <div class="col-md-12">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label class="col-sm-4" id="labelposition">NG Pada Area Proses Sanding Normal<span class="text-red">*</span></label>
                    &nbsp;
                    <div class="col-sm-5">
                      <input  type="radio" name="status" id="status" value="Ya" checked='checked' onchange="changeSanding(this.value)" >
                      <label class="form-check-label" for="exampleRadios1">
                        Ya
                      </label>
                      &nbsp;
                      <input class="form-check-input" type="radio" name="status" id="status" value="Tidak" onchange="changeSanding(this.value)">
                      <span class="checkmark"></span>
                      <label class="form-check-label" for="exampleRadios1">
                        Tidak
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                    <div class="col-sm-6">
                      <label>Condition Material Has Been Repaired<span class="text-red"></span></label>
                      <input type="file" id="condition_material" name="condition_material" onchange="readURL5(this,'');">
                      <br>
                      <img width="200px" height="180px" id="blah" src="" style="display: none; padding-bottom: 5px;" alt="your image" />
                      <br>
                    </div>
                    <div class="col-sm-6">
                      <label>Detail Material Has Been Repaired<span class="text-red"></span></label>
                      <input type="file" id="detail_condition_material" name="detail_condition_material" onchange="readURL5(this,'');">
                      <br>
                      <img width="200px" height="180px" id="blah" src="" style="display: none; padding-bottom: 5px;" alt="your image" />
                      <br>
                    </div>
                  </div>
                </div>


                <div class="col-sm-12" id="repair_data">
                  <div class="form-group">
                    <label class="col-sm-4" id="labelposition">Repair<span class="text-red">*</span></label>
                    &nbsp;
                    <div class="col-sm-5">

                      <input class="form-check-input" type="radio" name="repair" id="repair" value="Bisa" checked>
                      <label class="form-check-label" for="exampleRadios1">
                        Bisa
                      </label>
                      &nbsp;
                      <input class="form-check-input" type="radio" name="repair" id="repair" value="Tidak" onchange="timeRepair()" >
                      <label class="form-check-label" for="exampleRadios1">
                        Tidak
                      </label>
                    </div>
                  </div>
                </div>


                <div class="col-sm-12" id="time_repair_data">

                  <div class="form-group">
                    <div class="col-sm-6">
                      <label  id="labelposition">Time Repair<span class="text-red">*</span>(Detik)</label>
                      <input type="text" class="form-control numpad7" id="waktu_repair" name="waktu_repair" style="background-color: white;" placeholder="waktu repair">
                    </div>
                  </div>
                </div>
              </div>


              <div class="col-md-12">
                <br>
                <button class="btn btn-success konfirm pull-right">Konfirmasi</button>
                <span class="pull-right">&nbsp;</span>
                <a class="btn btn-primary btnPrevious pull-right">Sebelum</a>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="tab_3">
            <div class="row">
              <div class="col-md-12" style="margin-bottom : 5px">
                <div class="col-md-6">
                 <div class="form-group">
                  <label>Photo Kanagata<span class="text-red">*</span></label>
                  <input type="file" id="foto_kanagata" name="foto_kanagata" onchange="readURL2(this,'');">
                  <br>
                  <img width="200px" height="180px" id="blah" src="" style="display: none" alt="your image" />
                </div>

                <div class="form-group">
                  <label>Photo Defect Material<span class="text-red"></span></label>
                  <input type="file" id="foto_defect_material" name="foto_defect_material" onchange="readURL2(this,'');">
                  <br>
                  <img width="200px" height="180px" id="blah" src="" style="display: none" alt="your image" />
                </div>

              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Detail Photo Kanagata<span class="text-red">*</span></label>
                  <input type="file" id="foto_detail_kanagata" name="foto_detail_kanagata" onchange="readURL1(this,'');">
                  <br>
                  <img width="200px" height="180px" id="blah" src="" style="display: none" alt="your image" />
                </div>
                <div class="form-group">
                  <label>Detail Photo Defect Material<span class="text-red"></span></label>
                  <input type="file" id="foto_detail_defect" name="foto_detail_defect" onchange="readURL3(this,'');">
                  <br>
                  <img width="200px" height="180px" id="blah" src="" style="display: none" alt="your image" />
                </div>
              </div>
              <div id="tambah"></div>
              <div class="col-md-12">
                <a class="btn btn-info btnNext3 pull-right">Lanjut</a>
                <span class="pull-right">&nbsp;</span>
                <a class="btn btn-primary btnPrevious pull-right">Sebelum</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</form>

<form id ="editForm" name="editForm" method="post" action="{{ url('update/pelaporan/kanagata') }}" enctype="multipart/form-data">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Edit Pelaporan Kanagata Retak</h4>
          <br>
          <div class="nav-tabs-custom tab-danger">
            <ul class="nav nav-tabs">
              <li class="vendor-tab active disabledTab"><a href="#tab_1_e" data-toggle="tab" id="tab_header_1_e">Informasi Kanagata</a></li>
              <li class="vendor-tab disabledTab"><a href="#tab_3_e" data-toggle="tab" id="tab_header_3_e">Photo Kanagata & Material</a></li>
              <li class="vendor-tab disabledTab"><a href="#tab_2_e" data-toggle="tab" id="tab_header_2_e">Repair Informasi</a></li>
            </ul>
          </div>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1_e">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                   <div class="form-group">
                    <label id="label_section">Request ID<span class="text-red">*</span></label>
                    <input type="text" id="request_id_edit" name="request_id_edit" class="form-control"readonly>
                  </div>

                  <div class="form-group" id="new_deskripsi">
                    <label id="label_group">Problem Decription<span class="text-red">*</span></label>
                    <select class="form-control new_deskripsi" id="problem_desc_edit" name="problem_desc_edit" data-placeholder='Pilih Problem Decription' style="width: 100%">
                      <option value="">&nbsp;</option>
                      <option value="Kanagata Retak">Kanagata Retak</option>
                      <option value="Kanagata Pecah">Kanagata Pecah</option>
                      <option value="Kanagata Aus">Kanagata Aus</option>
                    </select>
                  </div>

                  <div class="form-group" id="selectProcess">
                    <label id="label_group">Type Process<span class="text-red">*</span></label>
                    <select class="form-control selectProcess" id="type_proses_edit" name="type_proses_edit"  data-placeholder='Pilih Type Process' style="width: 100%" onchange="getTypes('edit')">
                      <option value="">&nbsp;</option>
                      <option value="Forging">Forging</option>
                      <option value="Bending">Bending</option>
                      <option value="Hiraoshi">Hiraoshi</option>
                      <option value="Triming">Triming</option>
                      <option value="Blank Nuki">Blank Nuki</option>
                      <option value="Nukishibori">Nukishibori</option>
                    </select>
                  </div>

                  <div class="form-group" id="selectGmcEdit">
                    <label>GMC Material<span class="text-red">*</span></label>
                    <select class="form-control selectGmcEdit" id="gmc_material_edit" name="gmc_material_edit" data-placeholder='Pilih GMC' style="width: 100%" onchange="checkGMC(this.value,'edit')">
                      <option value="">&nbsp;</option>
                      @foreach($gmc as $row)
                      <option value="{{$row->gmc_material}}">{{$row->gmc_material}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label id="label_section">Description Material<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="desc_material_edit" name="desc_material_edit" placeholder="Description Material" readonly>
                    <!-- <input type="hidden" class="form-control" id="lifetimes" name="lifetimes" placeholder="lifetimes" readonly> -->
                  </div>

                  <div class="form-group">
                    <label id="label_section">Description Product<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="desc_product_edit" name="desc_product_edit" placeholder="Description Product" readonly>
                  </div>
                  
                  <div class="form-group" id="selectDieEdit">
                    <label id="label_group">Type Die<span class="text-red">*</span></label>
                    <select class="form-control selectDieEdit" id="type_die_edit" name="type_die_edit" data-placeholder='Pilih Type Die' style="width: 100%">
                      <option value="">&nbsp;</option>
                      <option value="DIE">DIE</option>
                      <option value="PUNCH">PUNCH</option>
                      <option value="GUIDE PLATE">GUIDE PLATE</option>
                      <option value="DIE PLATE">DIE PLATE</option>
                      <option value="PUNCH PLATE">PUNCH PLATE</option>
                      <option value="STRIPPER PLATE">STRIPPER PLATE</option>
                      <option value="KNOCK OUT">KNOCK OUT</option>
                      <option value="DRAWING PUNCH">DRAWING PUNCH</option>
                      <option value="DRAWING DIE">DRAWING DIE</option>
                      <option value="SNAP RING">SNAP RING</option>
                      <option value="LOWER KNOCK OUT">LOWER KNOCK OUT</option>
                      <option value="UPPER KNOCK OUT">UPPER KNOCK OUT</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label id="label_group">No Die<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="no_die_edit" name="no_die_edit" placeholder="No Die" onkeyup="myFunction('edit')">
                  </div>
                  <div class="form-group" id="making_dates_edit">
                    <label id="label_section">Molding Production Date<span class="text-red">*</span></label>
                    <input type="text" class="form-control molds" id="making_date_edit" name="making_date_edit" placeholder="Pilih Molding Production Date">
                  </div>
                  <div class="form-group">
                    <label id="labeldept">Total Shoot<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad' id="total_shoot_edit" name='total_shoot_edit' style="background-color: white;" onchange="checkLifetime('edit')" placeholder='0'>
                  </div>
                  <div class="form-group" id="time_life_edit">
                    <label id="labeldept">Target Total Shoot</label>
                    <input type='text' class='form-control' id="lifetimes_edit" name='lifetimes_edit' placeholder='0' readonly>
                    <input type='hidden' class='form-control' id="request_status_lifetime_edit" name='request_status_lifetime_edit' placeholder='0' readonly>
                  </div>
                  <span class="reqerror" id="request_status_edit"></span>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Incident Date<span class="text-red">*</span></label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right datepicker2" id="tanggal_kejadian_edit" name="tanggal_kejadian_edit" placeholder="Incident Date">
                    </div>
                  </div>

                  <div class="form-group">
                    <label id="labelposition">Spare Die<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad1' id="spare_die_edit" name='spare_die_edit' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group" id="forging_kes_edit">
                    <label id="labelposition">Forging Ke<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad2' id="forging_ke_edit" name='forging_ke_edit' style="background-color: white;" placeholder='0' required=''>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Die High<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad3' id="die_high_edit" name='die_high_edit' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group" id="limitpeak_edit">
                    <label id="labelposition">Limit Preasure / Peak<span class="text-red">*</span></label>
                    <div class="col-md-12">
                      <div class="col-md-5">
                        <input type="text" class="form-control numpad5" id="limit_preasure_edit" name="limit_preasure_edit" style="background-color: white;" placeholder="0">
                      </div>
                      <div class="col-md-1">
                        <h4>/</h4>
                      </div>
                      <div class="col-md-5">
                        <input type="text" class="form-control numpad6" id="peak_edit" name="peak_edit" style="background-color: white;" placeholder="0">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Cavity<span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad4' id="cavity_edit" name='cavity_edit' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Retak Ke : <span class="text-red">*</span></label>
                    <input type='text' class='form-control numpad8' id="retak_ke_edit" name='retak_ke_edit' style="background-color: white;" placeholder='0'>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Comment : <span class="text-red">*</span></label>
                    <textarea class="form-control" id="comment_edit" name="comment_edit" placeholder="Enter Comment" style="width: 100%"> </textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <a class="btn btn-info btnNextEdit pull-right">Lanjut</a>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="tab_2_e">
            <div class="row">
              <div class="col-md-12">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label class="col-sm-4" id="labelposition">NG Pada Area Proses Sanding Normal<span class="text-red">*</span></label>
                    &nbsp;
                    <div class="col-sm-5">
                      <input class="form-check-input" type="radio" name="status_edit" id="status_edit" onchange="changeSandingEdit(this.value)" value="Ya" checked>
                      <span class="checkmark"></span>
                      <label class="form-check-label" for="exampleRadios1">
                        Ya
                      </label>
                      &nbsp;
                      <input class="form-check-input" type="radio" name="status_edit" id="status_edit" onchange="changeSandingEdit(this.value)" value="Tidak">
                      <span class="checkmark"></span>
                      <label class="form-check-label" for="exampleRadios1">
                        Tidak
                      </label>
                    </div>
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="form-group">
                    <div class="col-sm-6">
                      <div class="form-group" id="img_repair">
                       <label for="image">Material Has Been Repaired Old</label>
                       : <div name="img_edit_repair" id="img_edit_repair"></div>
                     </div>
                     <label>Condition Material Has Been Repaired<span class="text-red"></span></label>
                     <input type="file" id="condition_material_edit" name="condition_material_edit" onchange="readURL5(this,'');">
                     <br>
                     <img width="200px" height="180px" id="blah" src="" style="display: none; padding-bottom: 5px;" alt="your image" />
                     <br>
                   </div>
                   <div class="col-sm-6">
                    <div class="form-group" id="img_repair_detail">
                     <label for="image">Detail Material Has Been Repaired Old</label>
                     : <div name="img_edit_detail_repair" id="img_edit_detail_repair"></div>
                   </div>
                   <label>Detail Material Has Been Repaired<span class="text-red"></span></label>
                   <input type="file" id="detail_condition_material_edit" name="detail_condition_material_edit" onchange="readURL5(this,'');">
                   <br>
                   <img width="200px" height="180px" id="blah" src="" style="display: none; padding-bottom: 5px;" alt="your image" />
                   <br>
                 </div>
               </div>
             </div>

             <div class="col-sm-12" id="repair_data_edit">
              <div class="form-group">
                <label class="col-sm-4" id="labelposition">Repair<span class="text-red">*</span></label>
                &nbsp;
                <div class="col-sm-5">
                  <input class="form-check-input" type="radio" name="repair_edit" id="repair_edit" value="Bisa" checked>
                  <label class="form-check-label" for="exampleRadios1">
                    Bisa
                  </label>
                  &nbsp;
                  <input class="form-check-input" type="radio" name="repair_edit" id="repair_edit" value="Tidak" onchange="timeRepair()">
                  <label class="form-check-label" for="exampleRadios1">
                    Tidak
                  </label>
                </div>
              </div>
            </div>

            <div class="col-sm-12" id="time_repair_edit">
              <div class="form-group">
                <div class="col-sm-6">
                  <label  id="labelposition">Time Repair<span class="text-red">*</span>(Detik)</label>
                  <input type="text" class="form-control numpad7" id="waktu_repair_edit" name="waktu_repair_edit" style="background-color: white;" value="0" placeholder="waktu repair">
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <br>
            <button class="btn btn-success konfirmEdit pull-right">Konfirmasi</button>
            <span class="pull-right">&nbsp;</span>
            <a class="btn btn-primary btnPrevious pull-right">Sebelum</a>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="tab_3_e">
        <div class="row">
          <div class="col-md-12" style="margin-bottom : 5px">
            <div class="col-md-6">
             <div class="form-group" id="edit_kanagatas">
               <label for="image">Photo Kanagata Old</label>
               : <div name="img_edit_kanagata" id="img_edit_kanagata"></div>
             </div>
             <div class="form-group">
               <label>Photo Kanagata<span class="text-red">*</span></label>
               <input type="file" id="foto_kanagata_edit" name="foto_kanagata_edit" onchange="readURL2(this,'');">
               <br>
               <img width="250px" height="180px" id="blah" src="" style="display: none" alt="your image" />
             </div>
             <div class="form-group" id="edit_defects">
               <label for="image">Photo Defect Material Old</label>
               : <div name="img_edit_defect" id="img_edit_defect"></div>
             </div>
             <div class="form-group">
              <label>Photo Defect Material<span class="text-red"></span></label>
              <input type="file" id="foto_defect_material_edit" name="foto_defect_material_edit" onchange="readURL2(this,'');">
              <br>
              <img width="250px" height="180px" id="blah" src="" style="display: none" alt="your image" />
            </div>

          </div>
          <div class="col-md-6">
            <div class="form-group" id="detail_kanagatas">
             <label for="image">Detail Photo Kanagata Old</label>
             : <div name="img_edit_detail_kanagata" id="img_edit_detail_kanagata"></div>
           </div>
           <div class="form-group">
            <label>Detail Photo Kanagata<span class="text-red">*</span></label>
            <input type="file" id="foto_detail_kanagata_edit" name="foto_detail_kanagata_edit" onchange="readURL1(this,'');">
            <br>
            <img width="250px" height="180px" id="blah" src="" style="display: none" alt="your image" />
          </div>
          <div class="form-group" id="detail_defects">
           <label for="image">Detail Photo Defect Material Old</label>
           : <div name="img_edit_detail_defect" id="img_edit_detail_defect"></div>
         </div>
         <div class="form-group">
          <label>Detail Photo Defect Material<span class="text-red"></span></label>
          <input type="file" id="foto_detail_defect_edit" name="foto_detail_defect_edit" onchange="readURL3(this,'');">
          <br>
          <img width="250px" height="180px" id="blah" src="" style="display: none" alt="your image" />
        </div>

      </div>
      <div id="tambah"></div>
      <div class="col-md-12">
        <a class="btn btn-info btnNext2Edit pull-right">Lanjut</a>
        <span class="pull-right">&nbsp;</span>
        <a class="btn btn-primary btnPrevious pull-right">Sebelum</a>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
</div>
</form>

<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <center>
          <h3 style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
            Perbaharui Data Dokumen Pelaporan Kanagata Retak<br>
          </h3>
        </center>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
          <form class="form-horizontal">
            <div class="col-md-12">
              <div class="form-group">
                <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
                <div class="col-sm-7">
                  <select class="form-control select2" id="editDepartment" data-placeholder="Select Department" style="width: 100%;">
                    <option value=""></option>
                    @foreach($gmc as $department)
                    <option value="{{$department->gmc_material}}">{{ $department->gmc_material }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Kategori<span class="text-red">*</span> :</label>
                <div class="col-sm-7">
                  <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_edit_IK" onclick="btnCategory('IK')" class="btn btn-sm">IK - Instruksi Kerja</a>
                  <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_edit_DM" onclick="btnCategory('DM')" class="btn btn-sm">DM - Dokumen Mutu</a>
                  <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_edit_DL" onclick="btnCategory('DL')" class="btn btn-sm">DL - Dokumen Lingkungan</a>
                </div>
                <input type="hidden" id="editCategory">
              </div>
              <div class="form-group">
                <label style="padding-top: 0;" for="" class="col-sm-3 control-label">No. Dokumen<span class="text-red">*</span> :</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" placeholder="Enter Document Number" id="editDocumentNumber">
                </div>
              </div>
              <div class="form-group">
                <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul Dokumen<span class="text-red">*</span> :</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" placeholder="Enter Document Title" id="editTitle">
                </div>
              </div>
            </div>
          </form>
          <div class="col-md-12" style="padding-bottom: 10px;">
            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
            <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="editDocument()">SIMPAN</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalImage">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <div class="form-group">
          <div  name="image_show" id="image_show"></div>
        </div>
      </div>
    </div>
  </div>
</div>




@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<!-- <script src="{{ url("js/drilldown.js")}}"></script> -->

<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("ckeditor/ckeditor.js") }}"></script>


<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
  var datadetail = [];
  
  jQuery(document).ready(function() {
   $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%; "></table>';
   $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
   $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
   $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
   $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
   $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};
   $('body').toggleClass("sidebar-collapse");

   $('#myModal').on('hidden.bs.modal', function () {
    $('#example2').DataTable().clear();
  });

   $('.select2').select2();
   $('.select3').select2({
    dropdownParent: $('#modalCreate'),
    allowClear : true
  });
   $('.selectProcess').select2();
   $('.new_deskripsi').select2();
   
   $('.selectDie').select2({
    dropdownParent: $('#selectDie'),
    allowClear:true,
    tags: true
  });

   $('.selectGmc').select2({
    dropdownParent: $('#selectGmc'),
    allowClear:true,
    tags: true
  });

   $('.select10').select2({
    dropdownParent: $('#modalCreate'),
    allowClear : true
  });
   $('.select11').select2({
    allowClear : true
  });

   $('.selectDieEdit').select2({
    dropdownParent: $('#selectDieEdit'),
    allowClear:true,
    tags: true
  });
   $('.selectGmcEdit').select2({
    dropdownParent: $('#selectGmcEdit'),
    allowClear:true,
    tags: true
  });

   CKEDITOR.replace('comment_create' ,{
    filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
    toolbar: [
    { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },
    [ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ],
    { name: 'tools', items: [ 'Maximize' ] }
    ],
    height:100
  });

   CKEDITOR.replace('comment_edit' ,{
    filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
    toolbar: [
    { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },
    [ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ],
    { name: 'tools', items: [ 'Maximize' ] }
    ],
    height:100
  });

   


   $('.hideselect').next(".select2-container").hide();

   $('#time_life').hide();
   $('#forging_kes').hide();
   $('#making_dates').hide();
   $('#gmc_material').val('');
   $('#gmc_material').val('').trigger('change');
   $('#desc_material').val('');
   $('#type_die').val('').trigger('change');
   $('#dateto').val('');
   $('#desc_product').val('');
   $('#problem_desc').val('').trigger('change');
   $('#retak_ke').val('');
   $('#foto_kanagata').val('');
   $('#foto_defect_material').val('');
   $('#foto_detail_kanagata').val('');
   $('#foto_detail_defect').val('');
   $('#condition_material').val('');
   $('#detail_condition_material').val('');
   $('#no_die').val('');
   $('#making_date').val('');
   $('#total_shoot').val('');
   $('#spare_die').val('');
   $('#forging_ke').val('');
   $('#tanggal_kejadian').val('');
   $('#die_high').val('');
   $('#limit_preasure').val('');
   $('#peak').val('');
   $('#cavity').val('');
   $('#waktu_repair').val('');
   $('#comment_create').val('');
   $('input[type="radio"]').prop('checked', false);
   $('#type_proses').val('').trigger('change');

   $('#foto_kanagata_edit').val('');
   $('#foto_detail_kanagata_edit').val('');
   $('#foto_defect_material_edit').val('');
   $('#foto_detail_defect_edit').val('');
   $('#condition_material_edit').val('');
   $('#detail_condition_material_edit').val('');

   fillChartdraf();
   $('.select4').select2({
    allowClear : true,
  });

   $('.datepicker2').datepicker({
    autoclose: true,
    format: "yyyy-mm-dd",
    todayHighlight: true,
  });

   $('.molds').datepicker({
     autoclose: true,
     format: "yyyy-mm-dd",
     todayHighlight: true,
   });

   $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });

   $('.numpad').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });

   $('.numpad1').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad2').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad3').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad4').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad5').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad6').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad7').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
   $('.numpad8').numpad({
    hidePlusMinusButton : true,
    decimalSeparator : '.',
  });
 });


  // $('.datepicker').datepicker({
  //   autoclose: true,
  //   format: "dd-mm-yyyy",
  //   todayHighlight: true,
  // });






  function modalEdit(document_id){
    var images = "";
    var images_defect = "";
    var images_detail_kanagata = "";
    var images_detail_defect = "";
    var images_condition_repair_detail = "";
    var images_condition_repair = "";

    $("#img_edit_kanagata").html("");
    $("#img_edit_defect").html("");
    $("#img_edit_detail_kanagata").html("");
    $("#img_edit_detail_defect").html("");
    $("#img_edit_repair").html("");
    $("#img_edit_detail_repair").html("");

    for (var i = 0; i < datadetail[0].length; i++) {
     if(datadetail[0][i].request_id == document_id){
      $('#request_id_edit').val(datadetail[0][i].request_id);
      $('#problem_desc_edit').val(datadetail[0][i].problem_desc).change();
      $('#type_proses_edit').val(datadetail[0][i].process_type).change();
      $('#gmc_material_edit').val(datadetail[0][i].gmc_material).change();
      $('#desc_material_edit').val(datadetail[0][i].desc_material);
      $('#desc_product_edit').val(datadetail[0][i].part_name);
      $('#type_die_edit').val(datadetail[0][i].type_die).change();
      $('#no_die_edit').val(datadetail[0][i].no_die);
      $('#total_shoot_edit').val(datadetail[0][i].total_shoot);
      $('#lifetimes_edit').val(datadetail[0][i].lifetime);
      $('#total_shoot_edit').val("22");
      $('#tanggal_kejadian_edit').val(datadetail[0][i].tanggal_kejadian);
      $('#spare_die_edit').val(datadetail[0][i].spare_die);
      $('#die_high_edit').val(datadetail[0][i].die_high);
      $('#limit_preasure_edit').val(datadetail[0][i].limit_preasure);
      $('#peak_edit').val(datadetail[0][i].peak);
      $('#cavity_edit').val(datadetail[0][i].cavity);
      $('#retak_ke_edit').val(datadetail[0][i].retak_ke);
      // var strCopy = datadetail[0][i].comment_users.split();
      // obj.split(/<p>|<\/p>|¤/)
      // console.log(datadetail[0][i].comment_users.split(/<span>|<\/span><br>|¤/));
      // $('#comment_edit').val(datadetail[0][i].comment_users.split(/<span>|<\/span><br>|¤/));
        $('#comment_edit').html(CKEDITOR.instances.comment_edit.setData(datadetail[0][i].comment_users));
      


      if (datadetail[0][i].making_date == null) {
        $('#making_dates_edit').hide();
        $('#forging_kes_edit').hide();
      }else{
        $('#making_date_edit').val(datadetail[0][i].making_date);

        $('#forging_ke_edit').val(datadetail[0][i].forging_ke);
      }

      if (datadetail[0][i].foto_kanagata == null || datadetail[0][i].foto_kanagata == '') {
        $('#edit_kanagatas').hide();
      }else{
        $("#img_edit_kanagata").html("");

        images += '<img src="{{ url("images/pelaporan_kanagata") }}/'+datadetail[0][i].foto_kanagata+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+datadetail[0][i].foto_kanagata+'\')">';
        $("#img_edit_kanagata").append(images);

      }

      if (datadetail[0][i].foto_defect_material == null || datadetail[0][i].foto_defect_material == '') {
        $('#edit_defects').hide();
      }else{
        images_defect += '<img src="{{ url("images/pelaporan_kanagata") }}/'+datadetail[0][i].foto_defect_material+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+datadetail[0][i].foto_defect_material+'\')">';
        $("#img_edit_defect").append(images_defect);
      }

      if (datadetail[0][i].detail_foto_kanagata == null || datadetail[0][i].detail_foto_kanagata == '') {
        $('#detail_kanagatas').hide();
      }else{
        images_detail_kanagata += '<img src="{{ url("images/pelaporan_kanagata") }}/'+datadetail[0][i].detail_foto_kanagata+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+datadetail[0][i].detail_foto_kanagata+'\')">';
        $("#img_edit_detail_kanagata").append(images_detail_kanagata);
      }

      if (datadetail[0][i].detail_foto_defect_material == null || datadetail[0][i].detail_foto_defect_material == "") {
        $('#detail_defects').hide();
      }else{
        images_detail_defect += '<img src="{{ url("images/pelaporan_kanagata") }}/'+datadetail[0][i].detail_foto_defect_material+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+datadetail[0][i].detail_foto_defect_material+'\')">';
        $("#img_edit_detail_defect").append(images_detail_defect);
      }

      if (datadetail[0][i].condition_material_repair == null || datadetail[0][i].condition_material_repair == '') {
        $('#img_repair').hide();
        $('#img_repair_detail').hide();
      }else{
        $('#img_repair').show();
        $('#img_repair_detail').show();

        images_condition_repair += '<img src="{{ url("images/pelaporan_kanagata") }}/'+datadetail[0][i].condition_material_repair+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+datadetail[0][i].condition_material_repair+'\')">';
        $("#img_edit_detail_repair").append(images_condition_repair);

        images_condition_repair_detail += '<img src="{{ url("images/pelaporan_kanagata") }}/'+datadetail[0][i].detail_condition_material_repair+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+datadetail[0][i].detail_condition_material_repair+'\')">';
        $("#img_edit_repair").append(images_condition_repair_detail);

      }

      if (datadetail[0][i].ng_sanding == 'Ya') {
        $("input[id=status_edit][value=Ya]").prop('checked', true);
        $('#repair_data_edit').hide();
        $('#time_repair_edit').hide();
      }else{
        $("input[id=status_edit][value=Tidak]").prop('checked', true);
        if (datadetail[0][i].repair == 'Bisa') {
          $("input[id=repair_edit][value=Bisa]").prop('checked', true);
        }else{
          $("input[id=repair_edit][value=Tidak]").prop('checked', true);
        }
        $('#waktu_repair_edit').val(datadetail[0][i].waktu_repair);
      }
    }
  }

  $.each(datadetail, function(key, value){
  });
  $('#modalEdit').modal('show');
}

$('.btnPrevious').click(function(){
  $('.nav-tabs > .active').prev('li').find('a').trigger('click');
});

$('.btnNext').click(function(){
  var gmc_material = $('#gmc_material').val();
  var desc_material = $('#desc_material').val();
  var no_die = $('#no_die').val();
  var type_proses = $('#type_proses').val();
  var making_date = $('#making_date').val();
  var total_shoot = $('#total_shoot').val();
  var spare_die = $('#spare_die').val();
  var tanggal = $('#tanggal_kejadian').val();
  var forging_ke = $('#forging_ke').val();
  var die_high = $('#die_high').val();
  var limit_preasure = $('#limit_preasure').val();
  var peak = $('#peak').val();
  var cavity = $('#cavity').val();
  if(gmc_material == '' || no_die == '' ||  total_shoot == '' || spare_die == '' || tanggal == '' || die_high == '' || cavity == '' || type_proses == ''){
    alert('Semua Data Harus Diisi Dahulu.');  
  }
  else{
    $('.nav-tabs > .active').next('li').find('a').trigger('click');
  }
});

$('.btnNext3').click(function(){
  var foto_kanagata = $('#foto_kanagata').val();
  var foto_defect_material = $('#foto_defect_material').val();
  var foto_detail_kanagata = $('#foto_detail_kanagata').val();
  var foto_defect_material = $('#foto_defect_material').val();

  if(foto_kanagata == '' || foto_detail_kanagata == ''){
    alert('Semua Data Harus Diisi Dahulu.');  
  }
  else{
    $('.nav-tabs > .active').next('li').find('a').trigger('click');
  }
});


$('.konfirm').click(function(){
  var status = $('input[id="status"]:checked').val();
  if(status == null){
    alert('Semua Data Harus Diisi Dahulu.');
    return false;  
  }else{
    $("#importForm").submit(function(){
      if (!confirm("Apakah Anda Yakin Ingin Membuat Pelaporan Kanagata Retak??")) {
        return false;
      } else {
        // setInterval($("#loading").show(), 5000);
        this.submit();
      }
    });

  }
});

$('.btnNextEdit').click(function(){
  var gmc_material_edit = $('#gmc_material_edit').val();
  var desc_material_edit = $('#desc_material_edit').val();
  var no_die_edit = $('#no_die_edit').val();
  var type_proses_edit = $('#type_proses_edit').val();
  var making_date_edit = $('#making_date_edit').val();
  var total_shoot_edit = $('#total_shoot_edit').val();
  var spare_die_edit = $('#spare_die_edit').val();
  var tanggal_edit =  $('#tanggal_kejadian_edit').val();
  var forging_ke_edit = $('#forging_ke_edit').val();
  var die_high_edit = $('#die_high_edit').val();
  var limit_preasure_edit = $('#limit_preasure_edit').val();
  var peak_edit = $('#peak_edit').val();
  var cavity_edit = $('#cavity_edit').val();
  if(gmc_material_edit == '' || no_die_edit == '' ||  total_shoot_edit == '' || spare_die_edit == '' || tanggal_edit == '' || die_high_edit == '' || cavity_edit == '' || type_proses_edit == ''){
    alert('Semua Data Harus Diisi Dahulu.');  
  }
  else{
    $('.nav-tabs > .active').next('li').find('a').trigger('click');
  }
});

$('.btnNext2Edit').click(function(){
  $('.nav-tabs > .active').next('li').find('a').trigger('click');
});

$('.konfirmEdit').click(function(){
  var status = $('input[id="status_edit"]:checked').val();
  if(status == null){
    alert('Semua Data Harus Diisi Dahulu.');  
    return false;
  }else{
    $("#editForm").submit(function(){
      if (!confirm("Apakah Anda Yakin Ingin Edit Pelaporan Kanagata Retak??")) {
        return false;
      } else {
        setInterval($("#loading").show(), 5000);
        this.submit();
      }
    });

  }
});

function myFunction(st) {
  if (st == "create") {
    var x = document.getElementById("no_die");
    x.value = x.value.toUpperCase();  
  }else if (st == "edit") {
    var x = document.getElementById("no_die_edit");
    x.value = x.value.toUpperCase();
  }
}

function getTypes(st) {

  if (st == 'creates') {
    if ($('#type_proses').val() == "Forging") {
      $('#making_dates').show();
      $('#forging_kes').show();
    }else{
      $('#making_dates').hide();
      $('#forging_kes').hide();
    }
  }

  if (st == 'edit') {
    if ($('#type_proses_edit').val() == "Forging") {
      $('#making_dates_edit').show();
      $('#forging_kes_edit').show();
    }else{
      $('#making_dates_edit').hide();
      $('#forging_kes_edit').hide();
    }
  }

}

function timeRepair() {
  var qty = 0 ;
  $('#waktu_repair').val(qty);
}

function changeSanding(param) {
  if (param == "Ya") {
    $('#repair_data').hide();
    $('#time_repair_data').hide();
  }else{
    $('#repair_data').show();
    $('#time_repair_data').show();
  }
}

function changeSandingEdit(param) {
  if (param == "Ya") {
    $('#repair_data_edit').hide();
    $('#time_repair_edit').hide();
  }else{
    $('#repair_data_edit').show();
    $('#time_repair_edit').show();
  }
}

function createModal(){
  $('#modalCreate').modal('show');
  $('input[name="repair"][value="Bisa"]').prop('checked',true);
  $('#repair_data').hide();
  $('#time_repair_data').hide();
}

function readURL(input,idfile) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      var img = $(input).closest("div").find("img");
      $(img).show();
      $(img)
      .attr('src', e.target.result);
    };

    reader.readAsDataURL(input.files[0]);
  }

  $(input).closest("td").find("button").hide();
      // $('#btnImage'+idfile).hide();
    }

    function readURL1(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          var img = $(input).closest("div").find("img");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      $(input).closest("td").find("button").hide();
      // $('#btnImage'+idfile).hide();
    }
    function readURL2(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          var img = $(input).closest("div").find("img");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      $(input).closest("td").find("button").hide();
      // $('#btnImage'+idfile).hide();
    }

    function readURL3(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          var img = $(input).closest("div").find("img");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      $(input).closest("td").find("button").hide();
      // $('#btnImage'+idfile).hide();
    }

    function readURL5(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          var img = $(input).closest("div").find("img");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
      }
      $(input).closest("td").find("button").hide();
    }

    function checkGMC(value,st) {
      var data = {
        gmc:value
      }
      var result = value.substring(2, 0);
      if (result == "ZE") {
        $('#limitpeak').hide();
      }else{
        $('#limitpeak').show();
      }

      if (result == "ZE") {
        $('#limitpeak_edit').hide();
      }else{
        $('#limitpeak_edit').show();
      }

      $.get('{{ url("kanagata/gmc") }}',data, function(result, status, xhr){
        if(result.status){
          if (st == 'creates') {
            $.each(result.lifetime, function(key, value) {
              $('#desc_material').val(value.desc_material);
              $('#desc_product').val(value.part_name);
            });

            if ($('#type_proses').val() == "Forging") {
              $.each(result.lifetime, function(key, value) {
                $('#lifetimes').val(value.lifetime);
                $('#time_life').show();
              });
            }else{
              $('#time_life').show();
              $('#lifetimes').val("-");
            }
          }else if (st == 'edit'){
           $.each(result.lifetime, function(key, value) {
            $('#desc_material_edit').val(value.desc_material);
            $('#desc_product_edit').val(value.part_name);
          });

           if ($('#type_proses_edit').val() == "Forging") {
            $.each(result.lifetime, function(key, value) {
              $('#lifetimes_edit').val(value.lifetime);
              $('#time_life_edit').show();
            });
          }else{
            $('#time_life_edit').show();
            $('#lifetimes_edit').val("-");
          }
        }
      }
    });
    }

    function checkLifetime(st) {
      if (st == "create") {
        $num =  parseInt($('#total_shoot').val());
        $num1 =  parseInt($('#lifetimes').val());
        if ($('#type_proses').val() == "Forging") {
          if ($num < $num1) {
            $('#request_status').html('* Total shoot kanagata kurang dari Target Total Shoot');
            $('#request_status_lifetime').val('Total Shoot Kanagata Kurang dari Target Total Shoot');
          }else{
            $('#request_status').html('* Total shoot kanagata melebihi Target Total Shoot');
            $('#request_status_lifetime').val('Total Shoot Kanagata Melebihi Target Total Shoot');
          } 
        }else {
          $('#request_status').html('');
          $('#request_status_lifetime').val('');
          $('#request_status').hide();
          $('#request_status_lifetime').hide();
        }
      }else if (st == "edit"){
       $num =  parseInt($('#total_shoot_edit').val());
       $num1 =  parseInt($('#lifetimes_edit').val());
       if ($('#type_proses_edit').val() == "Forging") {
        if ($num < $num1) {
          $('#request_status_edit').html('* Total shoot kanagata kurang dari Target Total Shoot');
          $('#request_status_lifetime_edit').val('Total Shoot Kanagata Kurang dari Target Total Shoot');
        }else{
          $('#request_status_edit').html('* Total shoot kanagata melebihi Target Total Shoot');
          $('#request_status_lifetime_edit').val('Total Shoot Kanagata Melebihi Target Total Shoot');
        } 
      }else {
        $('#request_status_edit').html('');
        $('#request_status_lifetime_edit').val('');
        $('#request_status_edit').hide();
        $('#request_status_lifetime_edit').hide();
      }

    }

  }

  function fillList(st,namest){
    $("#loading").show();
    var select = 'AllResume';

    if (namest == "Proses") {
      var nam = "Partially Approved";
    }else if (namest == "finish") {
      var nam = "Fully Approved";
    }else{
      var nam = "Rejected";
    }

    var data = {
      pic_progress:$('#pic_progress').val(),
      select:select,
      dates:$('#dateto').val(),
      st:st,
      names:nam
    }

    $.get('{{ url("kanagata/approval/table") }}',data, function(result, status, xhr){
      if(result.status){
        $("#loading").hide();
        $('#tableLeave').DataTable().clear();
        $('#tableLeave').DataTable().destroy();
        $('#bodyTableLeave').html("");
        $('#tableLeaveFinish').DataTable().clear();
        $('#tableLeaveFinish').DataTable().destroy();
        $('#bodyTableLeaveFinish').html("");
        var tableData = "";
        var tableDataComplete = "";

        datadetail.push(result.leave_request);

        $.each(result.leave_request, function(key, value) {

          tableData += '<tr>';
          if ('{{$user->employee_id}}' == value.created_by || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
            tableData += '<td style="font-weight:bold;color:red"><a href="javascript:void(0)" onclick="modalEdit(\''+value.request_id+'\')">'+value.request_id+'</a></td>';
          }else{
            tableData += '<td style="font-weight:bold;color:black">'+value.request_id+'</td>';
          }
          tableData += '<td>'+ value.tanggal +'</td>';
          tableData += '<td style="color:black;font-weight:bold;font-size:11px">'+value.part_name+'<br>'+value.type_die+'<br>'+value.no_die+'</td>';
          tableData += '<td>'+ value.retak_ke +'</td>';

          if (value.remark == 'Requested') {
            tableData += '<td style="background-color:#3f51b5 !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
          }else if (value.remark == 'Partially Approved') {
            tableData += '<td style="background-color:#f39c12 !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
          }else if (value.remark == 'Fully Approved') {
            tableData += '<td style="background-color:#00a65a !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
          }else if (value.remark == 'Rejected') {
            tableData += '<td style="background-color:#dd4b39 !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
          }
          var last_approval = '';
          var last_approval_status = '';
          var last_approval_comment = '';

          var approval_remark = [];
          var approval_remarks = [];
          for(var i = 0; i < result.leave_approvals.length;i++){
            if (result.leave_approvals[i][0].request_id == value.request_id) {
              for(var j = 0; j < result.leave_approvals[i].length;j++){
                approval_remark.push(result.leave_approvals[i][j].remark);
                approval_remarks.push({remark:result.leave_approvals[i][j].remark,keutamaan:result.leave_approvals[i][j].keutamaan});
              }
            }
          }
          if (approval_remark.indexOf("Applicant") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Applicant') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Applicant') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Applicant') {
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else{
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                      }
                    }
                  }
                }
              }
            }

          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }



          if (approval_remark.indexOf("Staff Prod") != -1) {

            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Staff Prod') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Staff Prod') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Staff Prod') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else if (result.leave_approvals[i][j].status == 'Rejected') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }
                          else{
                            var url = "{{ url('approval/comment/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Staff Prod">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }

          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }

          if (approval_remark.indexOf("Staff PE") != -1) {


            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Staff PE') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Staff PE') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Staff PE') {
                     if (value.remark == "Rejected") {
                      tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                    }else{
                      if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                        if (result.leave_approvals[i][j].keutamaan == 'belum') {
                          tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                        }else if (result.leave_approvals[i][j].status == 'Rejected') {
                          tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                        }
                        else{
                          var url = "{{ url('approval/comment/') }}";
                          tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Staff PE">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                        }
                      }else{
                        if (result.leave_approvals[i][j].keutamaan == 'belum') {
                          tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                        }else{
                          tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                        }
                      }
                    }
                  }
                }
              }
            }
          }


        }else{
          tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
        }


        if (approval_remark.indexOf("Foreman") != -1) {
          for(var i = 0; i < result.leave_approvals.length;i++){
            if (result.leave_approvals[i][0].request_id == value.request_id) {
              for(var j = 0; j < result.leave_approvals[i].length;j++){
                if (result.leave_approvals[i][j].status == 'Approved') {
                  if (result.leave_approvals[i][j].remark == 'Foreman') {
                    tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                  }
                }else if(result.leave_approvals[i][j].status == 'Rejected'){
                  if (result.leave_approvals[i][j].remark == 'Foreman') {
                    tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                  }
                }else if(result.leave_approvals[i][j].status == null){
                  if (result.leave_approvals[i][j].remark == 'Foreman') {
                    if (value.remark == "Rejected") {
                      tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                    }else{
                      if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                        if (result.leave_approvals[i][j].keutamaan == 'belum') {
                          tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                        }else if (result.leave_approvals[i][j].status == 'Rejected') {
                          tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                        }
                        else{
                          var url = "{{ url('approval/comment/') }}";
                          tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Foreman">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                        }
                      }else{
                        if (result.leave_approvals[i][j].keutamaan == 'belum') {
                          tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                        }else{
                          tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }else{
          tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
        }

        if (approval_remark.indexOf("Manager Prod") != -1) {

          for(var i = 0; i < result.leave_approvals.length;i++){
            if (result.leave_approvals[i][0].request_id == value.request_id) {
              for(var j = 0; j < result.leave_approvals[i].length;j++){
                if (result.leave_approvals[i][j].status == 'Approved') {
                  if (result.leave_approvals[i][j].remark == 'Manager Prod') {
                    tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                  }
                }else if(result.leave_approvals[i][j].status == 'Rejected'){
                  if (result.leave_approvals[i][j].remark == 'Manager Prod') {
                    tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                  }
                }else if(result.leave_approvals[i][j].status == null){
                  if (result.leave_approvals[i][j].remark == 'Manager Prod') {
                   if (value.remark == "Rejected") {
                    tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                  }else{
                    if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else if (result.leave_approvals[i][j].status == 'Rejected') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }
                      else{
                        var url = "{{ url('approval/comment/') }}";
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Manager Prod">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                      }
                    }else{
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else{
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                      }
                    }
                  }
                }
              }
            }
          }
        }

      }else{
        tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
      }

      if (approval_remark.indexOf("Chief PE") != -1) {

        for(var i = 0; i < result.leave_approvals.length;i++){
          if (result.leave_approvals[i][0].request_id == value.request_id) {
            for(var j = 0; j < result.leave_approvals[i].length;j++){
              if (result.leave_approvals[i][j].status == 'Approved') {
                if (result.leave_approvals[i][j].remark == 'Chief PE') {
                  tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                }
              }else if(result.leave_approvals[i][j].status == 'Rejected'){
                if (result.leave_approvals[i][j].remark == 'Chief PE') {
                  tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                }
              }else if(result.leave_approvals[i][j].status == null){
                if (result.leave_approvals[i][j].remark == 'Chief PE') {
                  if (value.remark == "Rejected") {
                    tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                  }else{
                    if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else if (result.leave_approvals[i][j].status == 'Rejected') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }
                      else{
                        var url = "{{ url('approval/comment/') }}";
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Chief PE">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                      }
                    }else{
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else{
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                      }
                    }
                  }
                }
              }
            }
          }
        }

      }else{
        tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
      }
      if (approval_remark.indexOf("Manager PE") != -1) {
        for(var i = 0; i < result.leave_approvals.length;i++){
          if (result.leave_approvals[i][0].request_id == value.request_id) {
            for(var j = 0; j < result.leave_approvals[i].length;j++){
              if (result.leave_approvals[i][j].status == 'Approved') {
                if (result.leave_approvals[i][j].remark == 'Manager PE') {
                  tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                }
              }else if(result.leave_approvals[i][j].status == 'Rejected'){
                if (result.leave_approvals[i][j].remark == 'Manager PE') {
                  tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                }
              }else if(result.leave_approvals[i][j].status == null){
                if (result.leave_approvals[i][j].remark == 'Manager PE') {
                  if (value.remark == "Rejected") {
                    tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                  }else{
                    if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else if (result.leave_approvals[i][j].status == 'Rejected') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }
                      else{
                        var url = "{{ url('approval/comment/') }}";
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Manager PE">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                      }
                    }else{
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else{
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }else{
        tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
      }

      if (approval_remark.indexOf("Manager Japanese Speacialist PE") != -1) {

        for(var i = 0; i < result.leave_approvals.length;i++){
          if (result.leave_approvals[i][0].request_id == value.request_id) {
            for(var j = 0; j < result.leave_approvals[i].length;j++){
              if (result.leave_approvals[i][j].status == 'Approved') {
                if (result.leave_approvals[i][j].remark == 'Manager Japanese Speacialist PE') {
                  tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                }
              }else if(result.leave_approvals[i][j].status == 'Rejected'){
                if (result.leave_approvals[i][j].remark == 'Manager Japanese Speacialist PE') {
                  tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                }
              }else if(result.leave_approvals[i][j].status == null){
                if (result.leave_approvals[i][j].remark == 'Manager Japanese Speacialist PE') {
                  if (value.remark == "Rejected") {
                    tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                  }else{
                    if ('{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else if (result.leave_approvals[i][j].status == 'Rejected') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }
                      else{
                        var url = "{{ url('decision/kanagata/approval/') }}";
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Manager Japanese Speacialist PE">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                      }
                    }else{
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else{
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                      }
                    }
                  }
                }
              }
            }
          }
        }

      }else{
        tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
      }

      for(var i = 0; i < result.leave_approvals.length;i++){
        if (result.leave_approvals[i][0].request_id == value.request_id) {
          for(var j = 0; j < result.leave_approvals[i].length;j++){
            if (result.leave_approvals[i][j].status != null) {
              last_approval = result.leave_approvals[i][j].remark;
              last_approval_status = result.leave_approvals[i][j].status;
              last_approval_comment = result.leave_approvals[i][j].comment;
            }
          }
        }
      }

      if (value.comment != null) {
       tableData += '<td style="font-size:12px;text-align: center;color:#00000;"><p>'+value.comment+'</p></td>';

     }else if (last_approval_status == "Rejected") {
       tableData += '<td style="color:black;font-size:12px;text-align: center;">'+last_approval_comment+'</td>';
     }else if (value.comment == "<span></span> ") {
      tableData += '<td style="color:black;font-size:12px;text-align: center;">-</td>';

    }
    else{
      tableData += '<td style="color:black;font-size:11px;text-align: center;">-</td>';
    }

    tableData += '<td>';
    tableData += '<button style="margin-right:2px" class="btn btn-xs btn-info" onclick="detailInformation(\''+value.request_id+'\')">Detail</button>';

    if (value.created_by == '{!! auth()->user()->username !!}' || result.role == "MIS" || result.role == "S-MIS" || result.role == "C-MIS") {
      tableData += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-danger"  onclick="cancelRequest(\''+value.request_id+'\');">Cancel</a>';
    }
    if (value.remark == 'Fully Approved' || value.remark == 'Rejected') {

    }else{
      if (last_approval != '') {
        tableData += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-primary"  onclick="resendEmail(\''+value.id+'\',\''+last_approval+'\');">Resend Email</a>';
      }
    }
    tableData += '</td>';
    tableData += '</tr>';
  });
$('#bodyTableLeave').append(tableData);

var table = $('#tableLeave').DataTable({
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



$.each(result.get_data_complete, function(key, value) {
  tableDataComplete += '<tr>';
  tableDataComplete += '<td style="font-weight:bold;color:black">'+value.request_id+'</td>';
  tableDataComplete += '<td>'+ value.tanggal +'</td>';
  tableDataComplete += '<td style="color:black;font-weight:bold;font-size:12px">'+value.part_name+'<br>'+value.type_die+'<br>'+value.no_die+'</td>';
  tableDataComplete += '<td>'+ value.retak_ke +'</td>';

  if (value.remark == 'Requested') {
    tableDataComplete += '<td style="background-color:#3f51b5 !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
  }else if (value.remark == 'Partially Approved') {
    tableDataComplete += '<td style="background-color:#f39c12 !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
  }else if (value.remark == 'Fully Approved') {
    tableDataComplete += '<td style="background-color:#00a65a !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
  }else if (value.remark == 'Rejected') {
    tableDataComplete += '<td style="background-color:#dd4b39 !important;color:white;font-size:11px;font-weight:bold;">'+value.remark+'</td>';
  }
  var last_approval = '';
  var last_approval_status = '';
  var last_approval_comment = '';

  var approval_remark = [];
  var approval_remarks = [];
  for(var i = 0; i < result.leave_approvals_complete.length;i++){
    if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
      for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
        approval_remark.push(result.leave_approvals_complete[i][j].remark);
        approval_remarks.push({remark:result.leave_approvals_complete[i][j].remark,keutamaan:result.leave_approvals_complete[i][j].keutamaan});
      }
    }
  }
  if (approval_remark.indexOf("Applicant") != -1) {

    for(var i = 0; i < result.leave_approvals_complete.length;i++){
      if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
        for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
          if (result.leave_approvals_complete[i][j].status == 'Approved') {
            if (result.leave_approvals_complete[i][j].remark == 'Applicant') {
              tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
            }
          }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
            if (result.leave_approvals_complete[i][j].remark == 'Applicant') {
              tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
            }
          }else if(result.leave_approvals_complete[i][j].status == null){
            if (result.leave_approvals_complete[i][j].remark == 'Applicant') {
              if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
              }else{
                tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
              }
            }
          }
        }
      }
    }

  }else{
    tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
  }

  

  if (approval_remark.indexOf("Staff Prod") != -1) {

    for(var i = 0; i < result.leave_approvals_complete.length;i++){
      if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
        for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
          if (result.leave_approvals_complete[i][j].status == 'Approved') {
            if (result.leave_approvals_complete[i][j].remark == 'Staff Prod') {
              tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
            }
          }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
            if (result.leave_approvals_complete[i][j].remark == 'Staff Prod') {
              tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
            }
          }else if(result.leave_approvals_complete[i][j].status == null){
            if (result.leave_approvals_complete[i][j].remark == 'Staff Prod') {
              if (value.remark == "Rejected") {
                tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
              }else{
                if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                  if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                    tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                  }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                    tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                  }
                  else{
                    var url = "{{ url('approval/comment/') }}";
                    tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Staff Prod">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                  }
                }else{
                  if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                    tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                  }else{
                    tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                  }
                }
              }
            }
          }
        }
      }
    }

  }else{
    tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
  }

  if (approval_remark.indexOf("Staff PE") != -1) {


    for(var i = 0; i < result.leave_approvals_complete.length;i++){
      if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
        for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
          if (result.leave_approvals_complete[i][j].status == 'Approved') {
            if (result.leave_approvals_complete[i][j].remark == 'Staff PE') {
              tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
            }
          }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
            if (result.leave_approvals_complete[i][j].remark == 'Staff PE') {
              tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
            }
          }else if(result.leave_approvals_complete[i][j].status == null){
            if (result.leave_approvals_complete[i][j].remark == 'Staff PE') {
             if (value.remark == "Rejected") {
              tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
            }else{
              if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }
                else{
                  var url = "{{ url('approval/comment/') }}";
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Staff PE">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                }
              }else{
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else{
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                }
              }
            }
          }
        }
      }
    }
  }


}else{
  tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
}

if (approval_remark.indexOf("Foreman") != -1) {
  for(var i = 0; i < result.leave_approvals_complete.length;i++){
    if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
      for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
        if (result.leave_approvals_complete[i][j].status == 'Approved') {
          if (result.leave_approvals_complete[i][j].remark == 'Foreman') {
            tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
          if (result.leave_approvals_complete[i][j].remark == 'Foreman') {
            tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == null){
          if (result.leave_approvals_complete[i][j].remark == 'Foreman') {
            if (value.remark == "Rejected") {
              tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
            }else{
              if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }
                else{
                  var url = "{{ url('approval/comment/') }}";
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Foreman">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                }
              }else{
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else{
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                }
              }
            }
          }
        }
      }
    }
  }
}else{
  tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
}

if (approval_remark.indexOf("Manager Prod") != -1) {

  for(var i = 0; i < result.leave_approvals_complete.length;i++){
    if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
      for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
        if (result.leave_approvals_complete[i][j].status == 'Approved') {
          if (result.leave_approvals_complete[i][j].remark == 'Manager Prod') {
            tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
          if (result.leave_approvals_complete[i][j].remark == 'Manager Prod') {
            tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == null){
          if (result.leave_approvals_complete[i][j].remark == 'Manager Prod') {
           if (value.remark == "Rejected") {
            tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
          }else{
            if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
              if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
              }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
              }
              else{
                var url = "{{ url('approval/comment/') }}";
                tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Manager Prod">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
              }
            }else{
              if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
              }else{
                tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
              }
            }
          }
        }
      }
    }
  }
}

}else{
  tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
}

if (approval_remark.indexOf("Chief PE") != -1) {

  for(var i = 0; i < result.leave_approvals_complete.length;i++){
    if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
      for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
        if (result.leave_approvals_complete[i][j].status == 'Approved') {
          if (result.leave_approvals_complete[i][j].remark == 'Chief PE') {
            tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
          if (result.leave_approvals_complete[i][j].remark == 'Chief PE') {
            tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == null){
          if (result.leave_approvals_complete[i][j].remark == 'Chief PE') {
            if (value.remark == "Rejected") {
              tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
            }else{
              if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }
                else{
                  var url = "{{ url('approval/comment/') }}";
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Chief PE">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                }
              }else{
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else{
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                }
              }
            }
          }
        }
      }
    }
  }

}else{
  tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
}
if (approval_remark.indexOf("Manager PE") != -1) {
  for(var i = 0; i < result.leave_approvals_complete.length;i++){
    if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
      for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
        if (result.leave_approvals_complete[i][j].status == 'Approved') {
          if (result.leave_approvals_complete[i][j].remark == 'Manager PE') {
            tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
          if (result.leave_approvals_complete[i][j].remark == 'Manager PE') {
            tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == null){
          if (result.leave_approvals_complete[i][j].remark == 'Manager PE') {
            if (value.remark == "Rejected") {
              tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
            }else{
              if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }
                else{
                  var url = "{{ url('approval/comment/') }}";
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Manager PE">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                }
              }else{
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else{
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                }
              }
            }
          }
        }
      }
    }
  }
}else{
  tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
}

if (approval_remark.indexOf("Manager Japanese Speacialist PE") != -1) {

  for(var i = 0; i < result.leave_approvals_complete.length;i++){
    if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
      for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
        if (result.leave_approvals_complete[i][j].status == 'Approved') {
          if (result.leave_approvals_complete[i][j].remark == 'Manager Japanese Speacialist PE') {
            tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
          if (result.leave_approvals_complete[i][j].remark == 'Manager Japanese Speacialist PE') {
            tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
          }
        }else if(result.leave_approvals_complete[i][j].status == null){
          if (result.leave_approvals_complete[i][j].remark == 'Manager Japanese Speacialist PE') {
            if (value.remark == "Rejected") {
              tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';               
            }else{
              if ('{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'C-MIS' || '{{$role_code}}' == 'S-MIS') {
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else if (result.leave_approvals_complete[i][j].status == 'Rejected') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }
                else{
                  var url = "{{ url('decision/kanagata/approval/') }}";
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+value.id+'/Manager Japanese Speacialist PE">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                }
              }else{
                if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
                  tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
                }else{
                  tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                }
              }
            }
          }
        }
      }
    }
  }

}else{
  tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
}

for(var i = 0; i < result.leave_approvals_complete.length;i++){
  if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
    for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
      if (result.leave_approvals_complete[i][j].status != null) {
        last_approval = result.leave_approvals_complete[i][j].remark;
        last_approval_status = result.leave_approvals_complete[i][j].status;
        last_approval_comment = result.leave_approvals_complete[i][j].comment;
      }
    }
  }
}

if(value.decision == null){
  tableDataComplete += '<td style="font-weight: bold; width: 1%; text-align: center;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black; "></span></td>';

}else{
  if (value.decision == "Ganti Cavity") {
    tableDataComplete += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: rgb(243, 156, 18); border: 1px solid black;">'+value.decision+'</span></td>';
  }else if (value.decision == "Lanjut"){
    tableDataComplete += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #23d613; border: 1px solid black;">'+value.decision+'</span></td>';
  }else{
    tableDataComplete += '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #dd4b39; border: 1px solid black;">'+value.decision+'</span></td>dd4b39'; 
  }
}

if (value.comment != null) {
 tableDataComplete += '<td style="font-size:12px;text-align: center;color:#black !important;"><p>'+value.comment+'</p></td>';

}else if (last_approval_status == "Rejected") {
 tableDataComplete += '<td style="color:black;font-size:12px;text-align: center;">'+last_approval_comment+'</td>';
}else if (value.comment == "<span></span> ") {
  tableDataComplete += '<td style="color:black;font-size:12px;text-align: center;">-</td>';
}
else{
  tableDataComplete += '<td style="color:black;font-size:12px;text-align: center;">-</td>';
}

tableDataComplete += '<td>';
tableDataComplete += '<button style="margin-right:2px" class="btn btn-xs btn-info" onclick="detailInformation(\''+value.request_id+'\')">Detail</button>';

tableDataComplete += '</td>';
tableDataComplete += '</tr>';
});
$('#bodyTableLeaveFinish').append(tableDataComplete);

var table = $('#tableLeaveFinish').DataTable({
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


}
else{
}
});
}


var kata_confirm = 'Apakah Anda Yakin?';

function detailInformation(request_id){
  window.open('{{ url("detail/kanagata") }}'+'/'+request_id, '_blank');
}


function cancelRequest(request_id) {
  if (confirm('Apakah Anda yakin akan membatalkan pelaporan?')) {
    $('#loading').show();
    var data = {
      request_id:request_id
    }
    
    $.get('{{ url("cancel/pelaporan/kanagata") }}', data, function(result, status, xhr){
      if(result.status){
        fillList();
        $('#loading').hide();
        audio_ok.play();
        openSuccessGritter('Success','Success Cancel Request');
      } else {
        fillList();
        $('#loading').hide();
        audio_error.play();
        openErrorGritter('Error!',result.message);
      }
    })
  }
}

function resendEmail(request_id,remark) {
  if (confirm('Apakah Anda yakin akan mengirim ulang Email?')) {
    $('#loading').show();
    $.get('{{ url("resend/kanagata/") }}/'+request_id+'/'+remark,  function(result, status, xhr){
      if(result.status){
        fillList();
        $('#loading').hide();
        audio_ok.play();
        openSuccessGritter('Success','Success Resend Email');
      } else {
        fillList();
        $('#loading').hide();
        audio_error.play();
        openErrorGritter('Error!',result.message);
      }
    })
  }
}

function showImage(imgs) {
  $('#modalImage').modal('show');
  var images_show = "";
  $("#image_show").html("");
  images_show += '<img style="cursor:zoom-in" src="{{ url("images/pelaporan_kanagata") }}/'+imgs+'" width="100%" >';
  $("#image_show").append(images_show);
}


function fillChartdraf() {
  $("#loading").show();
  fillList();
  var dateto = $('#dateto').val();

  var data = {
    dateto:dateto
  }

  $.get('{{ url("fetch/kanagata/control") }}',data, function(result, status, xhr) {
    if(xhr.status == 200){
      if(result.status){
        $("#loading").hide();

        var months = [];
        var jml_reject = [];
        var jml_belum = [];
        var jml_finish = [];

        for (var i = 0; i < result.datas.length; i++) {
          months.push(result.datas[i].bulan);
          jml_belum.push({y:parseInt(result.datas[i].Signed),key:result.datas[i].bulans});
          jml_reject.push({y:parseInt(result.datas[i].NotSigned),key:result.datas[i].bulans});
          jml_finish.push({y:parseInt(result.datas[i].finish),key:result.datas[i].bulans});

        }

        var colors = ['#32a852', '#a83232'];

        Highcharts.chart('container1', {
          chart: {
            type: 'column',
            options3d: {
              enabled: true,
              alpha: 15,
              beta: 0,
              depth: 50,
              viewDistance: 50
            }
          },
          title: {
            text: 'Pelaporan Kanagata Retak Monitoring & Control'
          },
          xAxis: {
            categories: months,
            type: 'category',
            gridLineWidth: 1,
            gridLineColor: 'RGB(204,255,255)',
            lineWidth:2,
            lineColor:'#9e9e9e',

            labels: {
              style: {
                fontSize: '13px'
              }
            },
          },yAxis: [{
            title: {
              text: 'Total',
              style: {
                color: '#eee',
                fontSize: '15px',
                fontWeight: 'bold',
                fill: '#6d869f'
              }
            },
            labels:{
              style:{
                fontSize:"15px"
              }
            },
            max: 15,
            type: 'linear',
            opposite: true
          },
          ],
          tooltip: {
            headerFormat: '<span>{series.name}</span><br/>',
            pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
          },
          legend: {
            layout: 'horizontal',
            backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
            itemStyle: {
              fontSize:'10px',
            },
            enabled: true,
            reversed: true
          },  
          plotOptions: {
            series:{
              cursor: 'pointer',
              animation: false,
              dataLabels: {
                enabled: true,
                format: '{point.y}',
                style:{
                  fontSize: '1vw'
                }
              },
              point: {
                events: {
                  click: function () {
                    fetchFilter(this.category,this.options.key,this.series.name);
                  }
                }
              },
              animation: false,
              pointPadding: 0.93,
              groupPadding: 0.93,
              borderWidth: 0.93,
              cursor: 'pointer'
            },
          },credits: {
            enabled: false
          },
          colors:colors,
          series: [{
            data: jml_finish,
            name: 'finish',
            showInLegend: false

          },{
            data: jml_belum,
            name: 'Proses',
            showInLegend: false,
            colorByPoint: false,
            color:'#599bd9',
          },
          {
            data: jml_reject,
            name: 'Rejected',
            showInLegend: false
          }],
        });

      }
    }
  });
}

function fetchFilter(categ,st,names){
  // console.log(name);
  fillList(st,names);
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