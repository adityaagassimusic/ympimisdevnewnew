@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">

<style type="text/css">
    .content-header {
        background-color: #61258e !important;
        padding: 10px;
        color: white;
    }

    .content-header > h1 {
        margin: 0;
        font-size: 80px;
        text-align: center;
        font-weight: bold;
    }

    .content-header > h2 {
        margin: 0;
        font-size: 3vw;
        text-align: center;
        font-weight: bold;
    }

    .content-header .isi {
        margin: 0;
        font-size: 150px;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .content-header .keterangan {
        margin: 0;
        font-size: 3vw;
        text-align: center;
        vertical-align: middle;
    }

    .content-wrapper{
        padding: 0 !important;
    }

    .table1 {
        cursor: pointer;
    }

    .table2 {
        cursor: pointer;
    }



    .text-yellow{
        font-size: 35px !important;
        font-weight: bold;
    }
    table.table-bordered{
        border:1px solid black;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid black;
        vertical-align: middle;
    }
    .disp {
        width: 15%;
        background-color: none;
        border-radius: 5px;
        margin-left: 15px;
        margin-top: 15px;
        display: inline-block;
        border: 2px solid white;
        cursor: pointer;

    }
    .patient-duration{
        margin: 0px;
        padding: 0px;
    }
    .gambar {
        width: 100%;
        background-color: none;
        border-radius: 5px;
        margin-top: 15px;

        display: inline-block;
        border: 2px solid white;
    }
    .gambar1 {
        width: 100%;
        background-color: none;
        border-radius: 5px;
        margin-top: 15px;

        display: inline-block;
        border: 2px solid white;
    }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
          <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
      </p>
  </div>
    <!-- <div class="col-xs-12" style="padding-top: 5px; padding-left: 5px;">
        <div class="col-xs-2" style="padding-right: 0;">
            <div class="input-group date">
                <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date" onchange="drawChart()">
            </div>
        </div>      
    </div> -->
    <div class="row" id="dispss" style="text-align: center; padding-top: 20px; padding-right:20px; padding-bottom: 0px">
    </div>
    
    <!-- <div class="col-xs-6" style="padding-right: 0; padding-top: 10px;">
        <div id="chart_kategori" style="width: 99%; height: 300px;"></div>
    </div> -->
    <!-- <div class="col-xs-6" style="padding-right: 0; padding-top: 10px;">
        <div id="chart_pelayanan" style="width: 99%; height: 300px;"></div>
    </div> -->
    <div class="col-xs-6" style="padding-top: 20px;">

        <div class="gambar" style="margin-top:20px" id="">
            <table style="text-align:center;width:100%">
                <tr>
                    <td colspan="2" style="border: 1px solid #fff !important;background-color: #267778; font-weight: bold;color: white;font-size: 24px">PENERIMAAN MATERIAL IMPORT
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 20%;">Request Masuk
                    </td>

                    <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 20%;">Pengecekan Material
                    </td>

                </tr>
                <tr>
                    <td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_request_import" ><span id="total_request_import" style="color: white;">0</span></td>
                    <td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_check_import" ><span id="total_check_import" style="color: white;">0</span></td>
                    
                    
                </tr>
                <tr>
                    <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 16%;">Penataan Material
                    </td>
                    <td  colspan="2" style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 16%;">Finish
                    </td>
                </tr>
                <tr>
                    <td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_penataan_import" ><span id="total_penataan_import" style="color: white;">0</span></td>  
                    <td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_finih_import" ><span id="total_finih_import" style="color: white;">0</span></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-xs-6" style="padding-top: 20px;">
     <div class="gambar1" style="margin-top:20px" id="">
        <table style="text-align:center;width:100%">
            <tr>
                <td colspan="3" style="border: 1px solid #fff !important;background-color: #255e79; font-weight: bold;color: white;font-size: 24px">REQUEST  KANBAN MATERIAL
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 20%;">Request Masuk
                </td>

                <td  style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 20%;">Pengambilan Material
                </td>
                <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 20%;">Menunggu Pengantaran
                </td>

            </tr>
            <tr>
                <td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_request_masuk" ><span id="total_request_masuk" style="color: white;">0</span></td>
                <td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center; vertical-align:middle" id="total_pengecekan" ><span id="total_pengecekan" style="color: white;">0</span></td>
                <td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center; vertical-align:middle" id="total_wait_delivery" ><span id="total_wait_delivery" style="color: white;">0</span></td>

            </tr>
            <tr>
                <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 16%;">Pengantaran
                </td>
                <td  style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 16%;">Pengecekan Material di Produksi
                </td>
                <td style="border: 1px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #cccccc;color: black;font-size: 20px;width: 16%;">Finish
                </td>
            </tr>
            <tr>
                <td class="table2" style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_delivery" ><span id="total_delivery" style="color: white;">0</span></td>  
                <td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_check_prd" ><span id="total_check_prd" style="color: white;">0</span></td>
                <td style="font-weight: bold; font-size: 80px; border: 1px solid #fff; text-align: center;vertical-align:middle" id="total_finish_delivery" ><span id="total_finish_delivery" style="color: white;">0</span></td>
            </tr>
        </table>
    </div>
</div>
<!-- 
    <div class="col-xs-6" style="padding-top: 20px;">
        <div class="box box-solid">
            <table id="tableResume" class="table table-bordered" style="width: 100%; font-size: 16px;">
                <thead style="background-color: rgb(126,86,134);">
                    <tr>
                        <th style="width: 1%; text-align: center; color: white; ">Penerimaan Import<br></th>
                        <th style="width: 1%; text-align: center; color: white;">Count Request<br></th>
                    </tr>
                </thead>
                <tbody id="tableBodyResume" style="background-color: #a6e3ac;">
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="">Request Masuk</td>
                        <td class="table1" style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_check_belum_pen"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="">Pengecekan Material</td>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_pengecekan_material"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="">Penataan Material</td>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_penataan"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="">Finish</td>
                        <td class="table1" style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_check_finish_pen"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

        

    <div class="col-xs-6" style="padding-top: 20px">
        <div class="box box-solid">
            <table id="tableResume2" class="table table-bordered" style="width: 100%; font-size: 16px;">
                <thead style="background-color: rgb(90, 101, 105);">
                    <tr>
                        <th style="width: 1%; text-align: center; color: white;">Pelayanan Produksi<br></th>
                        <th style="width: 1%; text-align: center; color: white;">Count Request Material<br></th>
                    </tr>
                </thead>
                <tbody id="tableBodyResume2" style="background-color: rgb(184, 194, 79);">
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" >Request Masuk</td>
                        <td class="table2" style="font-size: 1.7vw; text-align: center;font-weight: bold;color: black;" id="total_check_belum_pel"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" >Persiapan Pelayanan</td>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_check_progress_pel"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" >Pengantaran</td>
                        <td class="table2" style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_pengantaran"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" >Proses Pengantaran</td>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_proses_pengantaran"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" >Finish</td>
                        <td class="table2" style="font-size: 1.7vw; text-align: center; font-weight: bold;color: black;" id="total_check_finish_pel"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> -->

    <div class="modal fade" id="myModalType">
        <div class="modal-dialog modal-lg" style="width:1250px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="float: right;" id="modal-title"></h4>
                    <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
                    <br><h4 class="modal-title" id="judul_table_type"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="example4" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>Vendor</th>
                                        <th>No Pallet</th>
                                        <th>Total</th>                                        
                                        <th>PIC Internal</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-body-penerimaan">
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

    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #3c8dbc;">
                        <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">JOBLIST MATERIAL</h1>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="detail_material" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                                <thead style="background-color: rgba(126,86,134,.7);">

                                    <tr>
                                        <th>Vendor</th>
                                        <th>No Pallet</th>
                                        <th>Gmc</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_material_body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_finish_import">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center><h3 style="background-color: #1da12e; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Finish Request Material Import</h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <div class="col-md-3">
                        </div>
                        <div class="col-xs-12">
                            <table class="table table-hover table-bordered table-striped" id="example6">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                 <tr>
                                    <th>PIC Pelayanan</th> 
                                    <th>No Pallet</th>
                                    <th>Total Material</th>
                                    <th>Perkiraan Waktu</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="detail_body_import">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="myModalType2">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <center><h3 style="background-color: #1da12e; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Finish Request Material Produksi</h3>
                </center>
                <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                    <div class="col-md-3">
                    </div>
                    <div class="col-xs-12">
                        <table class="table table-hover table-bordered table-striped" id="example5">
                            <thead style="background-color: rgba(126,86,134,.7);">
                               <tr>
                                <th>PIC Pelayanan</th> 
                                <th>Kode Request</th>
                                <th>GMC</th>
                                <th>Lot</th>
                                <th>Perkiraan Waktu</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="detail-body-pelayanan">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

   <!--  <div class="modal fade" id="myModalType2">
        <div class="modal-dialog " style="width:1250px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="float: right;" id="modal-title2"></h4>
                    <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
                    <br><h4 class="modal-title" id="judul_table_type2"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="example5" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>PIC Pelayanan</th> 
                                        <th>Kode Request</th>
                                        <th>GMC</th>
                                        <th>Lot</th>
                                        <th>Perkiraan Waktu</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-body-pelayanan">

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
-->

  <!--   <div class="modal fade" id="modal_finish_import">
        <div class="modal-dialog modal-md" style="width:1250px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="float: right;" id="modal-title2"></h4>
                    <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
                    <br><h4 class="modal-title" id="judul_table_type3"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="example6" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>PIC Pelayanan</th> 
                                        <th>No Pallet</th>
                                        <th>Total Material</th>
                                        <th>Perkiraan Waktu</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_body_import">
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
    </div> -->

    <div class="modal fade" id="modalDetailPel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #3c8dbc;">
                        <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL MATERIAL</h1>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="detail_material_pel" class="table table-striped table-bordered" style="width: 100%;"> 
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <tr>
                                            <th>Kode Request</th>
                                            <th>No Kanban</th>
                                            <th>GMC</th>
                                            <th>Description</th>
                                            <th>Lot</th>
                                            <th>Uom</th>
                                            <th>Status</th>
                                        </tr>
                                    </tr>
                                </thead>
                                <tbody id="detail_material_body_pel">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_but">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                   <div class="col-xs-12" style="background-color: #757ce8;color: white;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold;">Request Materials Production</h1>
                </div>
                <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;padding-right: 0;padding-left: 0;">
                    <center><h4 id="title_proses" style="font-weight: bold; margin-bottom: 10px;font-size: 30px;"></h4></center>
                    <table width="100%">
                      <tbody align="center" id='aktivitas'>
                      </tbody>            
                  </table>
              </div>   

          </div>
      </div>
  </div>
</div>


<div class="modal fade" id="modal_import">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
               <div class="col-xs-12" style="background-color: #757ce8;color: white;">
                <h1 style="text-align: center; margin:5px; font-weight: bold;">Request Masuk Material Import</h1>
            </div>
            <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;padding-right: 0;padding-left: 0;">
                <center><h4 id="title_proses_import" style="font-weight: bold; margin-bottom: 10px;font-size: 30px;"></h4></center>
                <table width="100%">
                  <tbody align="center" id='aktivitas_import'>
                  </tbody>            
              </table>
          </div>   

      </div>
  </div>
</div>
</div>
<div class="modal fade" id="modalDetailMaterial">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #3c8dbc;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL MATERIAL</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="detail_material_job" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                            <thead style="background-color: rgba(126,86,134,.7);">

                                <tr>
                                    <th>Gmc</th>
                                    <th>Description</th>
                                    <th>Lot</th>
                                    <th>Sloc Name</th>
                                    <th>Perkiraan Waktu</th>
                                </tr>
                                
                            </thead>
                            <tbody id="detail_material_body_job">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="startJob()">Kerjakan</button>
            </div>
        </div>
    </div>
</div>

</div><div class="modal fade" id="modalDetailImport">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #3c8dbc;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL MATERIAL</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="detail_material_import" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                            <thead style="background-color: rgba(126,86,134,.7);">

                                <tr>
                                    <th id="as">No Invoice</th>
                                    <th id="bs">No Surat Jalan</th>
                                    <th>No Case</th>
                                    <th>Vendor</th>
                                    <th>Gmc</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Perkiraan Waktu</th>
                                </tr>
                                
                            </thead>
                            <tbody id="detail_material_body_import">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" onclick="startImport()">Kerjakan</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetailFinish">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #3c8dbc;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL MATERIAL</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="detail_material_finish" class="table table-striped table-bordered" style="width: 100%;"> 
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <tr>
                                        <th>GMC</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody id="detail_material_body_finish">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGetInfo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #3c8dbc;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL JOBLIST OPERATOR</h1>
                </div>
            </div>

            <div class="col-xs-12" style="margin-bottom: 10px">
                <center style="background-color: #3f51b5; color: #fff;">
                    <span style="font-size: 17px;font-weight: bold;padding: 5px">IN PROGRESS</span>
                </center>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="detail_material_history" class="table table-striped table-bordered" style="width: 100%;"> 
                            <thead style="background-color: rgb(126,86,134);color: white;">
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Joblist</th>
                                    <th>Start_time</th>
                                </tr>
                            </thead>
                            <tbody id="detail_material_body_history">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xs-12" style="margin-bottom: 10px">
                <center style="background-color: #32a852; color: #fff;">
                    <span style="font-size: 17px;font-weight: bold;padding: 5px">COMPLETED</span>
                </center>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="detail_material_complete" class="table table-striped table-bordered" style="width: 100%;"> 
                            <thead style="background-color: rgb(126,86,134);color: white;">
                                <tr>
                                    <th>Name</th>
                                    <th>Kode Request</th>
                                    <th>Status</th>
                                    <th>Joblist</th>
                                    <th>Start_time</th>
                                </tr>
                            </thead>
                            <tbody id="detail_material_body_complete">
                            </tbody>
                        </table>
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
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // penerimaan Import
    var req_masuk_import = [];
    var req_progress_import = [];
    var req_penataan = [];
    var req_finish_import = [];
    var detail_request_import = [];

    //Pelayanan Produksi
    var req_masuk_pelayanan = [];
    var req_progress_pelayanan = [];
    var req_proses_pengantaran = [];
    var req_finish_pelayanan = [];
    var detail_request1 = [];
    var detail_request = [];
    var fetch_opwh = [];


    jQuery(document).ready(function(){
        fetchPelayanan();
        drawChart();
        // updateOp();
        fetchOp();
        joblist();

        // setInterval(updateOp, 4000);
        setInterval(joblist, 2000);
        // fetchAll();
        // setInterval(fetchAll, 18000);
        // fetchImport();
        // setInterval(fetchImport, 25000);
        // fetchDetail();
        // setInterval(fetchDetail, 30000);
        // setInterval(drawChart, 15000);

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,   
            endDate: new Date()
        });
    });

    var in_time = [];
    var test = []
    function pad(val) {
        var valString = val + "";
        if (valString.length < 2) {
            return "0" + valString;
        } else {
            return valString;
        }
    }
    // function tes(){
    //     // var a = new Date();
    //     // var b = a.getTime() / 1000;
    //     // var c = new Date('Jul 07 2021 07:00:00');
    //     // var d = c.getTime() / 1000
    //     // alert(d);

    //     // var cob =  new Date()
    //     alert(diff_seconds(new Date(), new Date("Jul 07 2021 07:00:00")) % 60);
    // }


    function diff_seconds(dt2,dt1){

        var time2 = dt2.getTime() / 1000; 
        var time3 = dt1.getTime() / 1000;
        var diff = (time2 - time3);
        // return diff;
        return Math.abs(Math.round(diff));
    }

    function joblist(){
        // $.get('{{ url("fetch/status") }}',function(result, status, xhr){

            // if(result.status){  
                $('#dispss').html("");
                var tableData = "";
                var status= "";
                var background= "";
                var lokasi= "";

                num = 1;
                in_time = [];
                test = [];
                var patient = 0;

                $.each(fetch_opwh, function(key, value){
                    tableData += '<div class="disp" onclick="getInfo(\''+value.employee_id+'\')"; style="margin-top:0px">';
                    tableData += '<table class="konten" style="text-align:center;width:100%">';
                    tableData += '<tr>';
                    tableData += '<td colspan="2" style="border: 1px solid #fff !important;background-color: #605ca8;color: white;font-size: 20px"><b>OPERATOR '+num+'</b></td>';
                    tableData += '</tr>';
                    tableData += '<tr>';

                    var names = value.nama.split(" ");
                    tableData += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #605ca8;color: orange;font-size: 18px;width: 100%; font-weight: bold;"><span id="name" style="color: orange;">'+names[0]+" "+(names[1] || "")+'</span></td>';
                    tableData += '</tr>';

                    if (value.shift == "Shift_1" || value.shift == "Shift_1_Genba" || value.shift == "Shift_1_Jumat") {
                        shift = "SHIFT 1";
                    }else if (value.shift == "OFF") {
                        shift = "-";

                    }
                    else{
                        shift = "SHIFT 2";
                    }

                    tableData += '<tr>';
                    tableData += '<td style="font-weight: bold;font-size: 1.2vw;border-top: 1px solid #111; text-align: center; background-color: #e0ba46; vertical-align:middle;" ><span id="status_emp" style="color: black;">'+shift+'</span></td>';
                    tableData += '</tr>';


                    if(value.status1 == "idle" && value.kategori == " CK1" || value.kategori == " CK10" || value.kategori == " CK11" || value.kategori == " CK12" || value.kategori == " CK2" || value.kategori == " CK3" || value.kategori == " CK4" || value.kategori == " CK5"|| value.kategori == " CK13" || value.kategori == " CK15" || value.kategori == " CK6" || value.kategori == " CK7" || value.kategori == " CK8" || value.kategori == " CK9" || value.kategori == " CUTI" || value.kategori == " IMP" || value.kategori == " IPU" || value.kategori == " PC" || value.kategori == " SAKIT" || value.kategori == " TELAT" || value.kategori == " UPL") {
                        status = "Off";
                        background = "#c4001d";
                    }else if (value.status1 == "idle") {
                        status = "Idle";
                        background = "#3f9e4d";
                    }
                    else if (value.status1 == "work"){
                        status = "Work";
                        background = "#1aab32";
                    }else if (value.status1 == "off") {
                        status = "Off";
                        background = "#c4001d";
                    }else if (value.shift == "OFF") {
                        status = "Off";
                        background = "#c4001d";
                    }
                    else{
                        status = "-";
                        background = "";
                    }
                    tableData += '<tr>';
                    tableData += '<td style="font-weight: bold;font-size: 2vw;border-top: 1px solid #111; text-align: center;background-color:'+background+';vertical-align:middle" ><span id="status_emp" style="color: white;">'+status+'</span></td>';
                    tableData += '</tr>';

                    if (value.status_aktual_pekerjaan == "pengecekan import") {
                        status_jo = "Pengecekan Import";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "penataan import") {
                        status_jo = "Penataan Import";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "persiapan pelayanan") {
                        status_jo = "Persiapan Pelayanan";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "pengantaran") {
                        status_jo = "Pengantaran";
                        lokasi = "Produksi";
                    }else if (value.status_aktual_pekerjaan == "pengecekan material") {
                        status_jo = "Pengecekan Material";
                        lokasi = "Produksi";
                    }else if (value.status_aktual_pekerjaan == "Bongkar Import") {
                        status_jo = "Bongkar Import";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Kirim Material Incoming Check Qa") {
                        status_jo = "Kirim Material Incoming Check Qa";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Bagi Material") {
                        status_jo = "Bagi Material";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Bongkar Peti") {
                        status_jo = "Bongkar Peti";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Pelayanan Larutan") {
                        status_jo = "Pelayanan Larutan";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Terima Material Vendor") {
                        status_jo = "Kirim Material Incoming Check Qa";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Cuci Asam") {
                        status_jo = "Cuci Asam";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Penerimaan Scrap") {
                        status_jo = "Penerimaan Scrap";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "Chorei") {
                        status_jo = "Chorei";
                        lokasi = "Internal";
                    }else if (value.status_aktual_pekerjaan == "5S") {
                        status_jo = "5S";
                        lokasi = "Internal";
                    }
                    else if (value.status_aktual_pekerjaan == "Pengambilan Material QA") {
                        status_jo = "Pengambilan Material QA";
                        lokasi = "Internal";
                    }
                    else if (value.status_aktual_pekerjaan == "Kembali dari Pengiriman") {
                        status_jo = "Kembali dari Pengiriman";
                        lokasi = "Internal";
                    }
                     else if (value.status_aktual_pekerjaan == "Persiapan Stoktaking") {
                        status_jo = "Persiapan Stoktaking";
                        lokasi = "Internal";
                    }
                     else if (value.status_aktual_pekerjaan == "Stoktaking Hari H") {
                        status_jo = "Stoktaking Hari H";
                        lokasi = "Internal";
                    }

                    else {
                        status_jo = "-";
                        if (value.status1 == "off") {
                            lokasi = "-";    
                        }else if (value.status1 == "idle" && value.kategori == " CK1" || value.kategori == " CK10" || value.kategori == " CK11" || value.kategori == " CK12" || value.kategori == " CK2" || value.kategori == " CK3" || value.kategori == " CK4" || value.kategori == " CK5"|| value.kategori == " CK13" || value.kategori == " CK15" || value.kategori == " CK6" || value.kategori == " CK7" || value.kategori == " CK8" || value.kategori == " CK9" || value.kategori == " CUTI" || value.kategori == " IMP" || value.kategori == " IPU" || value.kategori == " PC" || value.kategori == " SAKIT" || value.kategori == " TELAT" || value.kategori == " UPL") {
                            lokasi = "-";    
                        }
                        else{
                            lokasi = "Internal";    

                        }

                    }

                    tableData += '<tr>';
                    tableData += '<td style="font-weight: bold;font-size: 1.2vw;border-top: 1px solid #111; text-align: center; background-color: #cad16d; vertical-align:middle;" ><span id="status_emp" style="color: black;">'+lokasi+'</span></td>';
                    tableData += '</tr>';

                    tableData += '<tr>';
                    tableData += '<td style="font-weight: bold;font-size: 1.2vw;border-top: 1px solid #111; text-align: center;vertical-align:middle;" ><span id="status_emp" style="color: white;">'+status_jo+'</span></td>';
                    tableData += '</tr>';

                    tableData += '<tr>';
                    tableData += '<td style="font-weight: bold;font-size: 1.2vw;border-top: 1px solid #111; text-align: center;vertical-align:middle; color:white;"><p class="patient-duration">';

                    if (value.status1 == "idle" && value.kategori == " CK1" || value.kategori == " CK10" || value.kategori == " CK11" || value.kategori == " CK12" || value.kategori == " CK2" || value.kategori == " CK3" || value.kategori == " CK4" || value.kategori == " CK5"|| value.kategori == " CK13" || value.kategori == " CK15" || value.kategori == " CK6" || value.kategori == " CK7" || value.kategori == " CK8" || value.kategori == " CK9" || value.kategori == " CUTI" || value.kategori == " IMP" || value.kategori == " IPU" || value.kategori == " PC" || value.kategori == " SAKIT" || value.kategori == " TELAT" || value.kategori == " UPL" ) {
                        in_time.push(new Date(value.start_time_status));
                        tableData += '<label>null</label>';

                    }else if (value.status1 == "off") {
                        in_time.push(new Date(value.start_time_status));
                        tableData += '<label>null</label>';
                    }else{
                        in_time.push(new Date(value.start_time_status));
                        tableData += '<label id="hours'+ patient +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[patient]) / 3600)) +'</label>:';

                        tableData += '<label id="minutes'+ patient +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[patient]) % 3600) / 60)) +'</label>:';
                        tableData += '<label id="seconds'+ patient +'">'+ pad(diff_seconds(new Date(), in_time[patient]) % 60) +'</label>';
                    }
                    tableData += '</p></td>';
                    tableData += '</tr>';
                    tableData += '</table>';
                    tableData += '</div>';
                    num += 1;
                    ++patient;

                });


            // });

            $('#dispss').append(tableData);
        // }else{
        //     openErrorGritter('Error!', result.message);
        // }

    // });
}

function getInfo(employee_id) {
    $("#modalGetInfo").modal("show");
    data = {
      employee_id : employee_id
  } 

  $.get('{{ url("get/operator/joblist") }}', data, function(result, status, xhr){
      if(result.status){ 
        $("#loading").hide(); 
        $('#detail_material_history').DataTable().clear();
        $('#detail_material_history').DataTable().destroy();
        $('#detail_material_body_history').html('');
        var tableData = "";
        var tableData2 = "";
        var st_job = "";
        $('#detail_material_complete').DataTable().clear();
        $('#detail_material_complete').DataTable().destroy();
        $('#detail_material_body_complete').html('');

        $.each(result.get_op_work, function(key, value) {

            tableData += '<tr>';
            tableData += '<td>'+value.NAME+'</td>';
            tableData += '<td>'+value.status+'</td>';
            if (value.joblist != null) {
                tableData += '<td>'+value.joblist+'</td>';
            }else{
                tableData += '<td>-</td>';
            }
            tableData += '<td>'+value.start_job+'</td>';
            tableData += '</tr>';

        });
        $('#detail_material_body_history').append(tableData);

        var kd_mt = "";

        $.each(result.get_op_history, function(key, value) {

            tableData2 += '<tr>';
            tableData2 += '<td>'+value.name+'</td>';
            if (value.request_desc == null) {
                kd_mt = "-";
            }else{
                kd_mt = value.request_desc;
            }
            tableData2 += '<td>'+kd_mt+'</td>';
            tableData2 += '<td>'+value.STATUS+'</td>';
            tableData2 += '<td>'+value.start_job+'</td>';
            tableData2 += '<td>'+value.end_job+'</td>';
            tableData2 += '</tr>';

        });
        $('#detail_material_body_complete').append(tableData2);

        var tableList = $('#detail_material_complete').DataTable({
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
                }
                ]
            },
            'paging': true,
            'lengthChange': true,
            'pageLength': 20,
            'searching': true,
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true,
            "aaSorting": [[ 0, "desc" ]]

        });

    }
    else{
        $("#loading").hide();
        audio_error.play();
        openErrorGritter('Error', result.message);
    }
});

}

function fetchOp() {
    $.get('{{ url("fetch/status") }}', function(result, status, xhr){
        if (result.status) {
            fetch_opwh = [];
            for (var i = 0; i < result.namas.length; i++) {
                fetch_opwh.push({employee_id:result.namas[i].employee_id, status1: result.namas[i].status1,nama: result.namas[i].nama,shift: result.namas[i].shift,status_aktual_pekerjaan: result.namas[i].status_aktual_pekerjaan, kategori: result.namas[i].kategori,start_time_status: result.namas[i].start_time_status});

            }
            console.log(fetch_opwh);
        }
    });
}

function fetchAll() {
    $.get('{{ url("fetch/count/request") }}', function(result, status, xhr){
      if (result.status) {
        detail_request1 = [];
        for (var i = 0; i < result.detail_request.length; i++) {
          detail_request1.push({id: result.detail_request[i].id,kode_request:result.detail_request[i].kode_request, gmc: result.detail_request[i].gmc,description: result.detail_request[i].description,lot: result.detail_request[i].lot,sloc_name: result.detail_request[i].sloc_name});
      }
  }
});
}
function fetchImport() {
    $.get('{{ url("fetch/count/import") }}', function(result, status, xhr){
      if (result.status) {
        detail_request_import = [];
        for (var i = 0; i < result.detail_import.length; i++) {
          detail_request_import.push({no_case: result.detail_import[i].no_case,vendor:result.detail_import[i].vendor, gmc: result.detail_import[i].gmc,description: result.detail_import[i].description,quantity: result.detail_import[i].quantity,no_invoice: result.detail_import[i].no_invoice,no_surat_jalan: result.detail_import[i].no_surat_jalan});
      }
      console.log(detail_request_import);
  }
});
}


function fetchDetail() {
    $.get('{{ url("fetch/detail/pelayanan/internal") }}', function(result, status, xhr){
        if (result.status) {
            detail_request = [];
            for (var i = 0; i < result.det_gmc_finish.length; i++) {
                detail_request.push({kode_request:result.det_gmc_finish[i].kode_request, gmc: result.det_gmc_finish[i].gmc,description: result.det_gmc_finish[i].description,lot: result.det_gmc_finish[i].lot,status_aktual: result.det_gmc_finish[i].status_aktual, status_pel: result.det_gmc_finish[i].status_pel, no_hako: result.det_gmc_finish[i].no_hako, uom: result.det_gmc_finish[i].uom});

            }
        }
    });
}




function requestMaterial() {
    $('#modal_but').show();
        // var url = '{{ url("index/activity_list/filter/") }}';
        // var urlnew = url + '/' + id + '/' + no + '/' + frequency;
        $.get('{{ url("fetch/count/request") }}', function(result, status, xhr){
            if(result.status){
                $('#aktivitas').empty();
                var aktivitas = "";

                $.each(result.count_material, function(key, value) {
                  aktivitas += '<div class="col-xs-4">';
                  if (key == 0) {
                      aktivitas += "<button class='btn btn-success' onclick='detail(\""+value.kode_request+"\")' style='margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px'>Kode Request : "+value.kode_request+" <br><b style='font-size: 15px'>Sloc Name : "+value.sloc_name+"</b><br><b style='font-size: 15px'>Total Material : "+value.total+"</b> </button>";
                  }else{
                    aktivitas += "<button disabled class='btn btn-success' onclick='detail(\""+value.kode_request+"\")' style='margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px'>Kode Request : "+value.kode_request+" <br><b style='font-size: 15px'>Sloc Name : "+value.sloc_name+"</b><br><b style='font-size: 15px'>Total Material : "+value.total+"</b> </button>";
                }
                aktivitas += '</div>';
            });
                
                $('#aktivitas').append(aktivitas);
                $('#loading').hide();
                $("#modal_but").modal('show');
            } else {
                audio_error.play();
                $('#loading').show();
            }
        });
    }

    function requestImport() {
        $('#modal_import').show();
        // var url = '{{ url("index/activity_list/filter/") }}';
        // var urlnew = url + '/' + id + '/' + no + '/' + frequency;
        $.get('{{ url("fetch/count/import") }}', function(result, status, xhr){
            if(result.status){
                $('#aktivitas_import').empty();
                var aktivitas_import = "";
                var status = "";
                var ex = "";

                $.each(result.material_import, function(key, value) {
                  aktivitas_import += '<div class="col-xs-4">';
                  if (value.no_invoice == null) {
                    status = value.no_surat_jalan;
                    ex = "No Surat Jalan";
                }else{
                    status = value.no_invoice;
                    ex = "No Invoice";
                }

                if (key == 0) {

                    aktivitas_import += "<button class='btn btn-success' onclick='det_import(\""+value.no_case+"\")' style='margin-bottom: 10px;white-space: normal;width: 100%;font-size: 14px'>"+ex+" : "+status+" / "+value.no_case+" <br> Vendor : "+value.vendor+" <br><b style='font-size: 15px'>Total Material : "+value.total+"</b> </button>";
                    aktivitas_import += '</div>';
                }else{
                    aktivitas_import += "<button disabled class='btn btn-success' onclick='det_import(\""+value.no_case+"\")' style='margin-bottom: 10px;white-space: normal;width: 100%;font-size: 14px'>"+ex+" : "+status+" / "+value.no_case+" <br> Vendor : "+value.vendor+" <br><b style='font-size: 15px'>Total Material : "+value.total+"</b> </button>";
                    aktivitas_import += '</div>';
                }

            });
                
                $('#aktivitas_import').append(aktivitas_import);
                $('#loading').hide();
                $("#modal_import").modal('show');
            } else {
                audio_error.play();
                $('#loading').show();
            }
        });
    }

    function det_import(id) {
        $("#modalDetailImport").modal("show");
        $('#detail_material_import').DataTable().clear();
        $('#detail_material_import').DataTable().destroy();
        $('#detail_material_body_import').html('');
        var tableLogBody = "";
        var no = 1;
        var status = "";
        $.each(detail_request_import, function(key, value){
            if (value.no_case == id) {
                tableLogBody += '<tr>';
                if (value.no_invoice == null) {
                    $('#as').hide();
                    $('#bs').show();
                    status = value.no_surat_jalan;
                    tableLogBody += '<td hidden>'+status+'</td>';
                    tableLogBody += '<td>'+status+'</td>';


                }else{
                    $('#as').show();
                    $('#bs').hide();
                    status = value.no_invoice;
                    tableLogBody += '<td>'+status+'</td>';
                    tableLogBody += '<td hidden>'+status+'</td>';
                }
                tableLogBody += '<td>'+value.no_case+'</td>';
                tableLogBody += '<td>'+value.vendor+'</td>';
                tableLogBody += '<td>'+value.gmc+'<input type="hidden" class="nocase" value="'+value.no_case+'"></td>';
                tableLogBody += '<td>'+value.description+'</td>';
                tableLogBody += '<td>'+value.quantity+'</td>';
                tableLogBody += '<td>0</td>';
                tableLogBody += '</tr>';
                no++;
            }
        });
        
        $('#detail_material_body_import').append(tableLogBody);

        $('#detail_material_import').DataTable({
            'dom': 'Bfrtip',
            'responsive':true,
            'buttons': {
                buttons:[
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
    }




    function detail(id) {
        $("#modalDetailMaterial").modal("show");
        $('#detail_material_job').DataTable().clear();
        $('#detail_material_job').DataTable().destroy();
        var data = {
            id:id
        }
        
        // $.get('{{ url("fetch/detail/job") }}', data, function(result, status, xhr){
        //     if(result.status){
            $('#detail_material_job').DataTable().destroy();
            $('#detail_material_body_job').html('');
            var tableLogBody = "";
            var no = 1;

            $.each(detail_request1, function(key, value){
                if (value.kode_request == id) {
                    tableLogBody += '<tr>';
                    tableLogBody += '<td>'+value.gmc+'<input type="hidden" class="koderequset" value="'+value.kode_request+'"></td>';
                    tableLogBody += '<td>'+value.description+'</td>';
                    tableLogBody += '<td>'+value.lot+'</td>';
                    tableLogBody += '<td>'+value.sloc_name+'</td>';
                    tableLogBody += '<td>0</td>';
                    tableLogBody += '</tr>';
                    no++;
                }
            });
            $('#detail_material_body_job').append(tableLogBody);

            $('#detail_material_job').DataTable({
                'dom': 'Bfrtip',
                'responsive':true,
                'lengthMenu': [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                'buttons': {
                    buttons:[
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
            // }
            // else{
            //     $('#loading').hide();
            //     alert('Unidentified Error');
            //     // audio_error.play();
            //     return false;
            // }
        // });

    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


    function startJob() {

        var kode_request = [];

        $('.koderequset').each(function(){
          kode_request.push($(this).val());
      });

        data = {
          kode_request : kode_request
      } 
      $("#loading").show();
      $.post('{{ url("update/pelayanan/proses1") }}', data, function(result, status, xhr){
          if(result.status){ 
            $("#loading").hide(); 
            openSuccessGritter('Success', result.message);
            $('#modalDetailPel').hide();
            window.location.href = "{{secure_url('index/detail')}}/"+kode_request[0];
        }
        else{
            $("#loading").hide();
            audio_error.play();
            openErrorGritter('Error', result.message);
        }
    });
  }


  function startImport() {

    var no_case = [];

    $('.nocase').each(function(){
      no_case.push($(this).val());
  });

    data = {
      no_case : no_case
  } 

  $.post('{{ url("update/pengecekan/import") }}', data, function(result, status, xhr){
      if(result.status){  
        openSuccessGritter('Success', result.message);
        $("#loading").show();

        $('#modalDetailImport').hide();
        window.location.href = "{{secure_url('warehouse/internal')}}/"+no_case[0];
    }
    else{
        $("#loading").hide();
        audio_error.play();
        openErrorGritter('Error', result.message);
    }
});
}

function updateOp(){
    var dat = new Date();
    chors = dat.getHours();
    min = dat.getMinutes();
    var status= "";
    var status2= "";
    var status3= "";
    var status4= "";
    

    $.get('{{ url("fetch/status") }}', function(result, status, xhr){
        if(result.status){
            $.each(result.namas, function(key, value) {
                if (chors >= 7 && chors < 16 ) {
                    if (value.shift == "Shift_1" || value.shift == "Shift_1_Genba" || value.shift == "Shift_1_Jumat") {
                        status = "idle";
                    }else{
                        status = "off";
                    }
                    data = {
                        shift : value.shift,
                        emp : value.employee_id,
                        status : status
                    }

                }else if (chors >= 16 && chors < 2) {
                    if (value.shift == "Shift_2_Genba") {
                        status2 = "idle";
                    }else{
                        status2 = "off";
                    }
                    data = {
                        shift : value.shift,
                        emp : value.employee_id,
                        status : status2
                    }


                }else if (chors >= 16) {
                    if (value.shift == "Shift_2_Genba") {
                        status2 = "idle";
                    }else{
                        status2 = "off";
                    }
                    data = {
                        shift : value.shift,
                        emp : value.employee_id,
                        status : status2
                    }
                }
                else if (chors == 1) {
                    if (value.shift == "Shift_2" || value.shift == "Shift_2") {
                        status3 = "off";
                    }else{
                        status3 = "off"
                    }
                    data = {
                        shift : value.shift,
                        emp : value.employee_id,
                        status : status3
                    }

                }else if (chors == 0) {
                    if (value.shift == "Shift_2" || value.shift == "Shift_2_Genba") {
                        status4 = "idle";
                    }else{
                        status4 = "off"
                    }
                    data = {
                        shift : value.shift,
                        emp : value.employee_id,
                        status : status4
                    }

                }else if (value.shift == "OFF") {
                    status4 = "off"
                    data = {
                        shift : value.shift,
                        emp : value.employee_id,
                        status : status4
                    }

                }
                else{
                    alert("melewati waktu");
                }

                $.post('{{ url("update/status/operator") }}', data, function(result, status, xhr){
                    if(result.status){
                    }else{
                        openErrorGritter('Error!', result.message);
                    }
                });
            });
        } else {
            audio_error.play();
        }
    });

    // if (chors == 16) {
    //     $.post('{{ url("update/status/operator") }}', function(result, status, xhr){
    //         if(result.status){
    //             openSuccessGritter('Success', result.message);
    //         }else{
    //             openErrorGritter('Error!', result.message);
    //         }
    //     });
    // }else{
    //     alert("salah");
    // }

}


function detail_gmc(kode){
    $('#modalDetail').modal('show');
    var tanggal = $('#tanggal_from').val();

    var data = {
        no_case : kode,
        tanggals : tanggal
    }

    $.get('{{ url("fetch/detail/gmc") }}',data, function(result, status, xhr){
        if(result.status){
            $('#detail_material').DataTable().clear();
            $('#detail_material').DataTable().destroy();
            $('#detail_material_body').html("");
            var tableData = "";
            var num=1;
            for (var i = 0; i < result.gmcs.length; i++) {
                tableData += '<tr>';
                tableData += '<td>'+ result.gmcs[i].vendor +'</td>';
                tableData += '<td>'+ result.gmcs[i].no_case +'</td>';
                tableData += '<td>'+ result.gmcs[i].gmc +'</td>';
                tableData += '<td>'+ result.gmcs[i].description +'</td>';
                tableData += '<td>'+ result.gmcs[i].quantity +'</td>';
                if (result.gmcs[i].status_job == "not done") {
                    status_peks = '-';
                }else if (result.gmcs[i].status_job == "progress") {
                    status_peks = result.gmcs[i].status_aktual;
                }else{
                    status_peks = result.gmcs[i].status_aktual;
                }

                tableData += '<td>'+ status_peks +'</td>';
                tableData += '</tr>';
            }

            $('#detail_material_body').append(tableData);

            var table = $('#detail_material').DataTable({
                'dom': 'Bfrtip',
                'responsive':true,
                'buttons': {
                    buttons:[
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
                'pageLength': 7,
                'searching': true   ,
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
            openErrorGritter('Error!', result.message);
        }
    });

}


function ShowDetailPelayanan(kategori1, status_pekl) {
    $("#myModalType2").modal("show");
    var tanggals = $('#tanggal_from').val();
    data = {
        kategori1 : kategori1,
        tanggals : tanggals
    }

    $.get('{{ url("fetch/detail/pelayanan/internal") }}', data, function(result, status, xhr){
        if(result.status){

            $('#example5').DataTable().clear();
            $('#example5').DataTable().destroy();
            $('#detail-body-pelayanan').html("");
            $('#judul_table_type2').append().empty();
            $('#judul_table_type2').append('<center><b>Detail Material "'+status_pekl+'"</b> </center>'); 

            var body = '';
            count = 1;
            for (var i = 0; i < result.detail_pels.length; i++) {

                // var number = 1;

                // var std_time = number  * 1.5; 
                body += '<tr>';
                body += '<td>'+ result.detail_pels[i].name +'</td>';
                body += '<td>'+ result.detail_pels[i].kode_request +'</td>';
                body += '<td>'+ result.detail_pels[i].gmc +'</td>';
                body += '<td>'+ result.detail_pels[i].lot +'</td>';
                body += '<td>0</td>';

                body += '<td style="text-align:center;" id="butview_'+count+'"> <a class="btn btn-info" id="kode" onclick="detail_gmc_pel('+result.detail_pels[i].kode_request+')" style="border-color: green;"><i class="fa fa-eye"></i></a></td>';
                body += '</tr>';
            }
            $('#detail-body-pelayanan').append(body);

            var tableList = $('#example5').DataTable({
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
                'pageLength': 5,
                'searching': false,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            tableList.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                        .search( this.value )
                        .draw();
                    }
                } );
            } );
                        // $('#operator-head').sho
                    }
                });
}

function ShowDetailFinish(status_finish) {
    $("#modal_finish_import").modal("show");

    $.get('{{ url("fetch/detail/import") }}', function(result, status, xhr){
        if(result.status){
            $('#example6').DataTable().clear();
            $('#example6').DataTable().destroy();
            $('#detail_body_import').html("");
            var body = '';
            count = 1;
            var status ="";
            for (var i = 0; i < result.imports.length; i++) {
                // var number = 1;
                // var std_time = number  * 1.5; 
                body += '<tr>';
                body += '<td>'+ result.imports[i].name +'</td>';
                body += '<td>'+ result.imports[i].no_case+ '</td>';
                body += '<td>'+ result.imports[i].total +'</td>';
                body += '<td>0</td>';
                if (result.imports[i].status_aktual == "Finish Penataan ") {
                    status = "Finish";
                }else{
                    status = "Belum";
                }
                body += '<td>'+ status +'</td>';
                body += '<td style="text-align:center;" id="butview_'+count+'"> <a class="btn btn-info" id="kode" onclick="detail_gmc_finish('+result.imports[i].no_case+')" style="border-color: green;"><i class="fa fa-eye"></i></a></td>';
                body += '</tr>';
            }
            $('#detail_body_import').append(body);

            var tableList = $('#example6').DataTable({
                'dom': 'Bfrtip',
                'responsive':true,
                'lengthMenu': [
                [ 5, 10, 25, -1 ],
                [ '5 rows', '10 rows', '25 rows', 'Show all' ]
                ],
                'buttons': {
                    buttons:[
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
                'pageLength': 5,
                'searching': false,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            tableList.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                        .search( this.value )
                        .draw();
                    }
                } );
            } );
        }
    });
}


function detail_gmc_pel(kode){
    $('#modalDetailPel').modal('show');
    var tanggal = $('#tanggal_from').val();

    $('#detail_material_pel').DataTable().clear();
    $('#detail_material_pel').DataTable().destroy();
    $('#detail_material_body_pel').html("");
    var tableData = "";
    var num=1;

    $.each(detail_request, function(key, value){
        if (value.kode_request == kode) {
            tableData += '<tr>';
            tableData += '<td>'+value.kode_request+'</td>';
            tableData += '<td>'+value.no_hako+'</td>';
            tableData += '<td>'+value.gmc+'</td>';
            tableData += '<td>'+value.description+'</td>';
            tableData += '<td>'+value.lot+'</td>';
            tableData += '<td>'+value.uom+'</td>';
            if (value.status_pel == "not yet") {
                status_pels = '-';
            }else if (value.status_pel == "progress") {
                status_pels = value.status_aktual;
            }else{
                status_pels = value.status_aktual;
            }

            tableData += '<td>'+ status_pels +'</td>';
            tableData += '</tr>';
            num++;
        }
    });

    $('#detail_material_body_pel').append(tableData);

    var table = $('#detail_material_pel').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 7, 25, 50, -1 ],
        [ '7 rows', '25 rows', '50 rows', 'Show all' ]
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
        'pageLength': 7,
        'searching': true   ,
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

function detail_gmc_finish(kode){
    data = {
        kode : kode    
    }

    $.get('{{ url("fetch/detail/import") }}',data, function(result, status, xhr){
        if(result.status){
            $('#modalDetailFinish').modal('show');

            $('#detail_material_finish').DataTable().clear();
            $('#detail_material_finish').DataTable().destroy();
            $('#detail_material_body_finish').html("");
            var tableData = "";
            var num=1;
            for (var i = 0; i < result.detail_finish.length; i++) {
                tableData += '<tr>';
                tableData += '<td>'+ result.detail_finish[i].gmc +'</td>';
                tableData += '<td>'+ result.detail_finish[i].description+ '</td>';
                tableData += '<td>'+ result.detail_finish[i].quantity +'</td>';
                tableData += '</tr>';
            }

            $('#detail_material_body_finish').append(tableData);

            var table = $('#detail_material_finish').DataTable({
                'dom': 'Bfrtip',
                'responsive':true,
                'lengthMenu': [
                [ 7, 25, 50, -1 ],
                [ '7 rows', '25 rows', '50 rows', 'Show all' ]
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
                'pageLength': 7,
                'searching': true   ,
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
    });
}

function drawChart() {

    var tanggal = $('#tanggal_from').val();
    var data1 = {
        tanggal : tanggal
    }    

    $.get('{{ url("fetch/display_internal") }}',data1, function(result, status, xhr) {
        if(result.status){
            //Pelayanan Produksi
            var belum_pel = [];
            var pel_masuk = result.data_belum_pel.length;
            belum_pel.push(pel_masuk);
            $("#total_request_masuk").empty();


            if (belum_pel > 0) {
                $('#total_request_masuk').text(belum_pel).css("background-color","#f39c12",'important');
                $("#total_request_masuk").text(belum_pel).css("color", "white",'important');

            }
            else{
                $("#total_request_masuk").text(belum_pel).css("background-color", "#f7f7f7",'important');
                $("#total_request_masuk").text(belum_pel).css("color", "black",'important');

            }
            req_masuk_pelayanan = result.data_belum_pel;

            var progress_pel = [];
            var pel_progress = result.data_progress_pel.length;
            progress_pel.push(pel_progress);
            $("#total_pengecekan").empty();
            if (pel_progress > 0) {
                $('#total_pengecekan').text(pel_progress).css("background-color","rgb(48, 100, 219)",'important');
                $("#total_pengecekan").text(pel_progress).css("color", "white",'important');
            }
            else{   
                $('#total_pengecekan').text(pel_progress).css("background-color","#f7f7f7",'important');
                $("#total_pengecekan").text(pel_progress).css("color", "black",'important');          
            }
            req_progress_pelayanan = result.data_progress_pel;

            var pengantaran_pel = [];
            var pel_progress = result.data_pengantaran.length;
            pengantaran_pel.push(pel_progress);
            $("#total_wait_delivery").empty();
            if (pel_progress > 0) {
                $('#total_wait_delivery').text(pengantaran_pel).css("background-color","rgb(48, 100, 219)",'important');
                $("#total_wait_delivery").text(pengantaran_pel).css("color", "white",'important');
            }
            else{
             $('#total_wait_delivery').text(pengantaran_pel).css("background-color","#f7f7f7",'important');
             $("#total_wait_delivery").text(pengantaran_pel).css("color", "black",'important');

         }
         req_progress_pelayanan = result.data_progress_pel;

         var pengantaran_procces = [];
         var process_pen = result.proccess_pengantaran.length;
         pengantaran_procces.push(process_pen);
         $("#total_delivery").empty();

         if (process_pen > 0) {
            $('#total_delivery').text(pengantaran_procces).css("background-color","rgb(48, 100, 219)",'important');
            $("#total_delivery").text(pengantaran_procces).css("color", "white",'important');
        }
        else{
            $('#total_delivery').text(pengantaran_procces).css("background-color","#f7f7f7",'important');
            $("#total_delivery").text(pengantaran_procces).css("color", "black",'important');

        }

        req_progress_pelayanan = result.data_progress_pel;

        var finish_pel = [];
        var pel_finish = result.data_finish_pel.length;
        finish_pel.push(pel_finish);
        $("#total_finish_delivery").empty();
        if (pel_finish > 0) {
            $('#total_finish_delivery').text(finish_pel).css("background-color","rgb(63, 158, 77)",'important');
            $("#total_finish_delivery").text(finish_pel).css("color", "white",'important');
        }
        else{
            $('#total_finish_delivery').text(finish_pel).css("background-color","#f7f7f7",'important');
            $("#total_finish_delivery").text(finish_pel).css("color", "black",'important');

        }

        req_finish_pelayanan = result.data_finish_pel;

        var check_pel= [];
        var pel_check = result.data_checks_pelayanan.length;
        check_pel.push(pel_check);
        $("#total_check_prd").empty();
        if (pel_check > 0) {
         $('#total_check_prd').text(check_pel).css("background-color","rgb(48, 100, 219)",'important');
         $("#total_check_prd").text(check_pel).css("color", "white",'important');
     }
     else{
       $('#total_check_prd').text(check_pel).css("background-color","#f7f7f7",'important');
       $("#total_check_prd").text(check_pel).css("color", "black",'important');

   }

   var req_import= [];
   var imp_req = result.data_progress.length;
   req_import.push(imp_req);
   $("#total_request_import").empty();
   if (pel_check > 0) {
     $('#total_request_import').text(req_import).css("background-color","#f39c12",'important');
     $("#total_request_import").text(req_import).css("color", "white",'important');
 }
 else{
   $('#total_request_import').text(req_import).css("background-color","#f7f7f7",'important');
   $("#total_request_import").text(req_import).css("color", "black",'important');
}


var req_import_check= [];
var imp_req_check = result.data_belum.length;
req_import_check.push(imp_req_check);
$("#total_reques").empty();
if (pel_check > 0) {
 $('#total_check_import').text(req_import_check).css("background-color","rgb(48, 100, 219)",'important');
 $("#total_check_import").text(req_import_check).css("color", "white",'important');
}
else{
   $('#total_check_import').text(req_import_check).css("background-color","#f7f7f7",'important');
   $("#total_check_import").text(req_import_check).css("color", "black",'important');
}

var req_import_pen= [];
var imp_req_pen = result.data_penataan.length;
req_import_pen.push(imp_req_pen);
$("#total_penataan_import").empty();
if (pel_check > 0) {
 $('#total_penataan_import').text(req_import_pen).css("background-color","rgb(48, 100, 219)",'important');
 $("#total_penataan_import").text(req_import_pen).css("color", "white",'important');
}
else{
   $('#total_penataan_import').text(req_import_pen).css("background-color","#f7f7f7",'important');
   $("#total_penataan_import").text(req_import_pen).css("color", "black",'important');
}

var fin_import= [];
var imp_finish = result.data_finish.length;
fin_import.push(imp_finish);
$("#total_finih_import").empty();
if (pel_check > 0) {
 $('#total_finih_import').text(fin_import).css("background-color","rgb(63, 158, 77)",'important');
 $("#total_finih_import").text(fin_import).css("color", "white",'important');
}
else{
   $('#total_finih_import').text(fin_import).css("background-color","#f7f7f7",'important');
   $("#total_finih_import").text(fin_import).css("color", "black",'important');
}



            // var elem_total_check_pel_masuk = document.getElementById('total_check_belum_pel');

            // elem_total_check_pel_masuk.addEventListener('click', function(){
            //     requestMaterial();
            // });

            

        } else{
            alert('Gagal');
        }
    })
}



function drawChart1() {

    var tanggal = $('#tanggal_from').val();
    var data1 = {
        tanggal : tanggal
    }    

    $.get('{{ url("fetch/display_internal") }}',data1, function(result, status, xhr) {
        if(result.status){
            //Pelayanan Produksi
            var belum_pel = [];
            var pel_masuk = result.data_belum_pel.length;
            belum_pel.push(pel_masuk);
            $("#total_request_masuk").empty();


            if (belum_pel > 0) {
                $('#total_request_masuk').text(belum_pel).css("background-color","#f39c12",'important');
                $("#total_request_masuk").text(belum_pel).css("color", "white",'important');

            }
            else{
                $("#total_request_masuk").text(belum_pel).css("background-color", "#f7f7f7",'important');
                $("#total_request_masuk").text(belum_pel).css("color", "black",'important');

            }
            req_masuk_pelayanan = result.data_belum_pel;

            var progress_pel = [];
            var pel_progress = result.data_progress_pel.length;
            progress_pel.push(pel_progress);
            $("#total_pengecekan").empty();
            if (pel_progress > 0) {
                $('#total_pengecekan').text(pel_progress).css("background-color","rgb(48, 100, 219)",'important');
                $("#total_pengecekan").text(pel_progress).css("color", "white",'important');
            }
            else{   
                $('#total_pengecekan').text(pel_progress).css("background-color","#f7f7f7",'important');
                $("#total_pengecekan").text(pel_progress).css("color", "black",'important');          
            }
            req_progress_pelayanan = result.data_progress_pel;

            var pengantaran_pel = [];
            var pel_progress = result.data_pengantaran.length;
            pengantaran_pel.push(pel_progress);
            $("#total_wait_delivery").empty();
            if (pel_progress > 0) {
                $('#total_wait_delivery').text(pengantaran_pel).css("background-color","rgb(48, 100, 219)",'important');
                $("#total_wait_delivery").text(pengantaran_pel).css("color", "white",'important');
            }
            else{
             $('#total_wait_delivery').text(pengantaran_pel).css("background-color","#f7f7f7",'important');
             $("#total_wait_delivery").text(pengantaran_pel).css("color", "black",'important');

         }
         req_progress_pelayanan = result.data_progress_pel;

         var pengantaran_procces = [];
         var process_pen = result.proccess_pengantaran.length;
         pengantaran_procces.push(process_pen);
         $("#total_delivery").empty();

         if (process_pen > 0) {
            $('#total_delivery').text(pengantaran_procces).css("background-color","rgb(48, 100, 219)",'important');
            $("#total_delivery").text(pengantaran_procces).css("color", "white",'important');
        }
        else{
            $('#total_delivery').text(pengantaran_procces).css("background-color","#f7f7f7",'important');
            $("#total_delivery").text(pengantaran_procces).css("color", "black",'important');

        }

        req_progress_pelayanan = result.data_progress_pel;

        var finish_pel = [];
        var pel_finish = result.data_finish_pel.length;
        finish_pel.push(pel_finish);
        $("#total_finish_delivery").empty();
        if (pel_finish > 0) {
            $('#total_finish_delivery').text(finish_pel).css("background-color","rgb(63, 158, 77)",'important');
            $("#total_finish_delivery").text(finish_pel).css("color", "white",'important');
        }
        else{
            $('#total_finish_delivery').text(finish_pel).css("background-color","#f7f7f7",'important');
            $("#total_finish_delivery").text(finish_pel).css("color", "black",'important');

        }

        req_finish_pelayanan = result.data_finish_pel;

        var check_pel= [];
        var pel_check = result.data_checks_pelayanan.length;
        check_pel.push(pel_check);
        $("#total_check_prd").empty();
        if (pel_check > 0) {
         $('#total_check_prd').text(check_pel).css("background-color","rgb(48, 100, 219)",'important');
         $("#total_check_prd").text(check_pel).css("color", "white",'important');
     }
     else{
       $('#total_check_prd').text(check_pel).css("background-color","#f7f7f7",'important');
       $("#total_check_prd").text(check_pel).css("color", "black",'important');

   }

   var req_import= [];
   var imp_req = result.data_belum.length;
   req_import.push(imp_req);
   $("#total_check_import").empty();
   if (imp_req > 0) {
     $('#total_check_import').text(req_import).css("background-color","rgb(48, 100, 219)",'important');
     $("#total_check_import").text(req_import).css("color", "white",'important');
 }
 else{
   $('#total_check_import').text(req_import).css("background-color","#f7f7f7",'important');
   $("#total_check_import").text(req_import).css("color", "black",'important');

}

var req_import= [];
var imp_req = result.data_belum.length;
req_import.push(imp_req);
$("#total_check_import").empty();
if (imp_req > 0) {
 $('#total_check_import').text(req_import).css("background-color","rgb(48, 100, 219)",'important');
 $("#total_check_import").text(req_import).css("color", "white",'important');
}
else{
   $('#total_check_import').text(req_import).css("background-color","#f7f7f7",'important');
   $("#total_check_import").text(req_import).css("color", "black",'important');

}


            // var elem_total_check_pel_masuk = document.getElementById('total_check_belum_pel');

            // elem_total_check_pel_masuk.addEventListener('click', function(){
            //     requestMaterial();
            // });

            // var elem_total_pengantaran = document.getElementById('total_pengantaran');

            // elem_total_pengantaran.addEventListener('click', function(){
            //     window.location.href = "{{secure_url('index/pengantaran')}}";

            // });

            // var elem_total_check_pel_finish = document.getElementById('total_check_finish_pel');

            // elem_total_check_pel_finish.addEventListener('click', function(){
            //     ShowDetailPelayanan('req_finish_pelayanan', 'Finish Pelayanan');
            // });



        } else{
            alert('Gagal');
        }
    })
}


// function fetchResume(){
//  $.get('{{ url("fetch/Resume") }}',function(result, status, xhr){
//      if(result.status){  
//          $('#tableBodyResume').html("");         
//          var tableData = "";
//          var belum_ditangani = [];
//          var not_permintaan = result.data_belum.length;
//          belum_ditangani.push(not_permintaan);
//          var progress_ditangani = [];
//          var progress_pernerimaan = result.data_progress.length;
//          progress_ditangani.push(progress_pernerimaan);
//          var finish_ditangani = [];
//          var finish_pernerimaan = result.data_finish.length;
//          finish_ditangani.push(finish_pernerimaan);
//          var tanggal_kedatangan = result.tanggal;
//          var belum_pel2 = [];
//          var pel_masuk = result.data_belum_pel.length;
//          belum_pel2.push(pel_masuk);
//          var progress_pel2 = [];
//          var pel_progress = result.data_progress_pel.length;
//          progress_pel2.push(pel_progress);

//          var finish_pel2 = [];
//          var pel_finish = result.data_finish_pel.length;
//          finish_pel2.push(pel_finish);

//          tableData += '<tr>';
//          tableData += '<td>'sddssds'</td>';
//          tableData += '<td>'+ belum_ditangani +'</td>';
//          tableData += '<td>'+ progress_ditangani +'</td>';
//          tableData += '<td>'+ finish_ditangani +'</td>';
//          tableData += '</tr>';
//          $('#tableBodyResume').append(tableData);
//      }else{
//          openErrorGritter('Error!', result.message);
//      }
//  });
// }


function fetchPelayanan(){

    $.get('{{ url("fetch/pelayanan/job") }}',function(result, status, xhr){
        if(result.status){
            var tableData = "";
            $('#tableDutyBody').html('');
            var color = "";
            var no = 1;

            $.each(result.pel, function(key, value){

                if (result.pel.length > 0) {

                }

                if (no % 2 === 0 ) {
                    color = 'style="background-color: #ffd8b7"';
                } else {
                    color = 'style="background-color: #fffcb7"';
                }
                tableData += '<tr '+color+'>';
                tableData += '<td class= "kode_request"style="padding:0; text-align:center;">'+value.kode_request+'</td>';
                tableData += '<td style="padding:0; text-align:center;">'+value.area+'</td>';
                tableData += '<td style="padding:0; text-align:center;">'+value.total+'</td>';
                tableData += '</tr>';
                no ++;
            });

            $('#tableDuty').append(tableData);
        }
        else{
            // openErrorGritter('Error!', 'Attempt to retrieve data failed');
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
@endsection