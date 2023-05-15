@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
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
    Detail {{ $page }}
    <small>Detail Corrective Action Report</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
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
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
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
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New CPAR</h3> --}}
    </div>  
      
    <form role="form" method="post" action="{{url('index/qc_car/detail_action', $cars->id)}}" enctype="multipart/form-data">
      <div class="box-body">
         <?php if ($cars->pic == NULL) { ?>
           <a class="btn btn-danger" data-toggle="modal" href="#modalkaryawan">Pilih Karyawan</a>
         <?php } ?>
         <input type="hidden" name="checkpic" id="checkpic" value="{{$cars->pic}}">

         <a href="{{url('index/qc_report/print_cpar', $cpar[0]->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain"  target="_blank">Preview CPAR Report</a>

         <a href="{{url('index/qc_car/print_car_new', $cars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank">Preview CAR Report</a>

         <a data-toggle="modal" data-target="#statusmodal{{$cars->id}}" class="btn btn-primary btn-md" style="color:white;margin-right: 5px">Cek Status Verifikasi</a>

         <!-- <a href="{{url('index/qc_car/sendemail/'.$cars['id'].'/'.$cars['posisi'])}}" class="btn btn-sm ">Email </a> -->
         @if($cars->deskripsi != null && $cars->tindakan != null && $cars->penyebab != null && $cars->perbaikan != null )
           @if(($cars->email_status == "SentStaff" && $cars->posisi == "staff") || ($cars->email_status == "SentForeman" && $cars->posisi == "foreman"))
              <a class="btn btn-md btn-default" data-toggle="tooltip" title="Send Email" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email</a>
              <!-- <a class="btn btn-success" data-toggle="modal" data-target="#email{{$cars->id}}">Send Email</a> -->
           @else
               <label class="label label-success" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
           @endif
          @endif
         
         <br/><br/>

         <?php if ($cars->pic != NULL) { ?>
            <b>PIC</b> : <label class="label label-success"> {{$cars->employee_pic->name}} </label>
            <br><br>

        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">No CPAR<span class="text-red">*</span></label>

          <div class="col-sm-5">
            <input type="text" class="form-control" name="cpar_no" placeholder="Nasukkan Nomor CPAR" value="{{ $cars->cpar_no }}" readonly="">
          </div>

          <label class="col-sm-2">Tinjauan 4M<span class="text-red">*</span></label>      
          <div class="col-sm-4">
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" 
              <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 1) {
                  echo 'checked';
                }
              }?> name="tinjauan4m[]" value="1" id="tinjauan4m">Man
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" name="tinjauan4m[]" value="2" id="tinjauan4m" <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 2) {
                  echo 'checked';
                }
              }?>>Machine
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" name="tinjauan4m[]" value="3" id="tinjauan4m" <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 3) {
                  echo 'checked';
                }
              }?>>Material
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" name="tinjauan4m[]" value="4" id="tinjauan4m" <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 4) {
                  echo 'checked';
                }
              }?>>Method
            </label>
          </div>
        </div>

        <div class="col-sm-12" style="padding:0">              

              <div class="col-sm-6" style="margin: 0">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">
                  Deskripsi Masalah
                  <!-- <br><span style="font-size: 14px;font-weight: normal;">Merupakan sebuah</span> -->
                  <br><span style="font-size: 14px;font-weight: normal;">Contoh : <a onclick="modalImage('deskripsi')"> Gambar</a></span>
                </label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="deskripsi" placeholder="Masukkan Deskripsi">{{ $cars->deskripsi }}</textarea>
                </div>
              </div>


              <div class="col-sm-6" style="margin: 0;">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Analisa Akar Penyebab Permasalahan (Root Of Cause)
                  <!-- <br><span style="font-size: 14px;font-weight: normal;">Merupakan</span> -->
                  <br><span style="font-size: 14px;font-weight: normal;">Contoh : <a onclick="modalImage('penyebab')"> Gambar</a></span>
                </label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="penyebab" placeholder="Masukkan Penyebab">{{ $cars->penyebab }}</textarea>
                </div>
              </div>

              <div class="col-sm-6" style="margin: 0;margin-top: 20px;">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Penanganan Segera (Correction)
                  <br><span style="font-size: 14px;font-weight: normal;"><b>Merupakan Tindakan penanganan saat terjadi komplain</b><br>
                  *Contoh kategori Correction :
                    <br>1. Pengecekan stok WIP dan atau FSTK
                    <br>2. Repair material NG
                    <br>3. Sosialisasi permasalahan/komplain
                  </span>
                  <br><span style="font-size: 14px;font-weight: normal;">Contoh : <a onclick="modalImage('penanganan')"> Gambar</a></span> 
                </label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="tindakan" placeholder="Tindakan Perbaikan" style="height: 100%">{{ $cars->tindakan }}</textarea>
                </div>
              </div>



              <div class="col-sm-6" style="margin: 0;margin-top: 20px;">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Tindakan Perbaikan (Correction)
                  <br><span style="font-size: 14px;font-weight: normal;"><b>Merupakan tindakan penanganan saat terjadi permasalahan. Tindakan tersebut bersifat tangible (nyata/bisa dilihat).</b>

                  <br>*Contoh kategori Correction :
                  <br>1. Repair dan atau penggantian tools, jig atau mesin yang rusak
                  <br>2. Pemberian peringatan kepada Operator
                  <br>3. Refresh training IK
                  <br>4. Lain-lain, perubahan metode kerja yang dapat dilihat nyata
                  
                  </span>
                  <br><span style="font-size: 14px;font-weight: normal;">Contoh : <a onclick="modalImage('perbaikan')"> Gambar</a></span>
                </label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="perbaikan" placeholder="Masukkan Perbaikan">{{ $cars->perbaikan }}</textarea>
                </div>
              </div>

              <div class="col-sm-6 col-md-offset-3" style="margin-top: 20px;">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Tindakan Perbaikan (Preventive Action)
                  <br><span style="font-size: 14px;font-weight: normal;"><b>Merupakan tindakan untuk mencegah permasalahan berulang di kemudian hari. Tindakan tsb bersifat intangible (tidak terlihat nyata).</b>

                  <br>*Contoh tindakan Preventive Action  :
                  <br>1. Pembuatan Prosedur, DM, IK, cek list
                  <br>2. Penambahan point pengecekan di IK, Cek list, Form
                  <br>3. Penambahan cek list jishu hozen mesin, tools, jig
                  <br>4. Program training rutin operator 


                  </span>
                  <br><span style="font-size: 14px;font-weight: normal;">Contoh : <a onclick="modalImage('pencegahan')"> Gambar</a></span>
                </label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="pencegahan" placeholder="Masukkan Pencegahan">{{ $cars->pencegahan }}</textarea>
                </div>
              </div>
            </div>
              <!-- 
        <div class="form-group row" align="left">
          <label class="col-sm-1">Deskripsi<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="deskripsi" placeholder="Masukkan Deskripsi">{{ $cars->deskripsi }}</textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Penanganan Segera<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="tindakan" placeholder="Masukkan Tindakan Segera">{{ $cars->tindakan }}</textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Analisa Penyebab Permasalahan<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="penyebab" placeholder="Masukkan Penyebab">{{ $cars->penyebab }}</textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tindakan Perbaikan<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="perbaikan" placeholder="Masukkan perbaikan">{{ $cars->perbaikan }}</textarea>
          </div>
        </div> -->
        <div class="form-group row increment" align="left" >
          <label class="col-sm-1" style="margin-top: 20px;">File</label>
          <div class="col-sm-5" style="margin-top: 20px;">
            <input type="file" name="files[]" multiple>
            <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button>
            <!-- {{ $cars->file }} -->
            <!-- <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button> -->
          </div>
        </div>
        <div class="clone hide">
          <div class="form-group row control-group" style="margin-top:10px">
            <label class="col-sm-1">File</label>
            <div class="col-sm-6">
              <input type="file" name="files[]">
              <div class="input-group-btn"> 
                <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
              </div>
            </div>
          </div>
        </div>

         <?php if ($cars->file != null){ ?>
            <br><br>
              <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">File Yang Telah Diupload</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i> </button>
                  </div>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <?php $data = json_decode($cars->file);
                    for ($i = 0; $i < count($data); $i++) { ?>
                    <div class="col-md-12">
                      <div class="col-md-3">
                        <div class="isi">
                          <?= $data[$i] ?>
                        </div>
                      </div>
                      <div  class="col-md-2">
                          <a href="{{ url('/files/car/'.$data[$i]) }}" class="btn btn-primary pull-right">Download / Preview</a>
                      </div>
                      <div class="col-md-1">
                          <a href="javascript:void(0)" onclick="hapus('{{$data[$i]}}','{{$cars->id}}')" class="btn btn-danger pull-left">
                            <i class="fa fa-trash"></i></a>
                      </div>                      
                    </div>
                    <br><br><br>
                  <?php } ?>                       
                </div>
              </div>    
          <?php } ?>

        <div class="col-md-12">
          <div style="background-color:green;padding: 10px;text-align: center;font-size: 20px;color: white;">Detail Tindakan Perbaikan (Isikan semua tindakan dan tanggal perbaikan)</div>

          <!--  @if($cars->perubahan_dokumen == "" || $cars->perubahan_dokumen == null)
            <div class="col-md-2" style="margin-top: 20px;">
                <label id="label_group">Detail Perubahan Dokumen<span class="text-red">*</span></label>
            </div>

            <div class="col-md-2" style="margin-top: 20px;">
              <div class="form-group">
                <select class="form-control select2" id="perubahan_dokumen" name="perubahan_dokumen" data-placeholder="Perubahan Dokumen" style="width: 100%;" onchange="perubahanDokumen(this)" required="">
                  <option value="">&nbsp;</option>
                  <option value="Ada">Ada</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
            </div>

            @else
            <div class="col-md-2" style="margin-top: 20px;">
                <label id="label_group">Detail Perubahan Dokumen<span class="text-red">*</span></label>
            </div>

            <div class="col-md-2" style="margin-top: 20px;">
              <div class="form-group">

                <select class="form-control select2" id="perubahan_dokumen" name="perubahan_dokumen" data-placeholder="Perubahan Dokumen" style="width: 100%;" onchange="perubahanDokumen(this)">
                    @if($cars->perubahan_dokumen == "Ada")
                    <option value="">&nbsp;</option>
                    <option value="Ada" selected>Ada</option>
                    <option value="Tidak">Tidak</option>
                    @else
                    <option value="">&nbsp;</option>
                    <option value="Ada">Ada</option>
                    <option value="Tidak" selected>Tidak</option>
                    @endif
                </select>
              </div>
            </div>
        @endif -->
        <div id="change_dokumen" class="col-md-12" style="padding: 0;">
          <br>
            <b style="color:red">
            Tuliskan semua action dari Tindakan Penanganan Segera, Tindakan Perbaikan (Correction) dan Tindakan pencegahan (Preventive Action).
            <br> 
            Contoh : Bila ada 2 tindakan Penanganan segera, 3 Tindakan Perbaikan (Correction) dan 1 Tindakan Pencegahan (Preventive Action), 
            <br>Maka tuliskan total 6 action pada kolom "Detail Tindakan Perbaikan"

<!--             Contoh : Bila ada 3 tindakan perbaikan, maka isikan 3 detail tindakan perbaikan tersebut dan tanggal perbaikannya. 
            <br>
            Bila tidak ada dokumen yang perlu dirubah pada kolom dokumen, isikan "tidak ada perubahan dokumen". 
            <br>Bila ada Perubahan Dokumen, Isikan "Ada" kemudian upload dokumen tersebut. -->
            <br>
            <i class="fa fa-arrow-down" style="font-size:30px;position: absolute;top: 100px;left:200px"></i>
            </b>
            <a class="btn btn-sm btn-success pull-right" style="margin-top: 10px" onclick="add_item()"><i class="fa fa-plus"></i>&nbsp; Add</a>
            <table class="table">
              <thead>
               <tr>
                <th style="width: 20%">Detail Tindakan Perbaikan</th>
                <th style="width: 10%">Tanggal Tindakan Perbaikan</th>
                <th style="width: 5%">Dokumen yang berubah</th>
                <th style="width: 5%">Jenis Dokumen</th>
                <!-- <th style="width: 20%">Dokumen / Metode Proses Perlu Revisi</th> -->
                <th style="width: 5%">File</th>
                <th style="width: 5%">#</th>
              </tr>
              <?php  
              if (count($documents) > 0) { 
                $index = 1;  ?>
                  @foreach($documents as $doc)
                  <tr id="{{$index}}">
                    <input type="hidden" class="form-control" value="{{$index}}" id="index" name="index">
                    
                    <th>
                      <input type="hidden" class="form-control" value="{{$doc->detail}}" id="detail_<?= $index ?>" name="detail_<?= $index ?>">
                      {{$doc->detail}}
                    </th>
                    <th>
                      <input type="hidden" class="form-control" value="{{$doc->due_date}}" id="due_date_<?= $index ?>" name="due_date_<?= $index ?>">
                      <?= date('d F Y', strtotime($doc->due_date))  ?>
                    </th> 
                    <th>
                      <input type="hidden" class="form-control" value="{{$doc->nomor_dokumen}}" id="dokumen_<?= $index ?>" name="dokumen_<?= $index ?>">
                      {{$doc->nomor_dokumen}}
                    </th>
                     <th>
                      <input type="hidden" class="form-control" value="{{$doc->dokumen}}" id="jenis_dokumen_<?= $index ?>" name="jenis_dokumen_<?= $index ?>">
                      {{$doc->dokumen}}
                    </th>
                    <th>
                    <?php if($doc->file != null){ ?> 
                        <a href="{{ url('/files/car/document/'.$doc->file) }}" target="_blank" class="fa fa-paperclip"></a> &nbsp;</th>
                      <?php } else { ?>
                        <?php if($doc->nomor_dokumen == "Tidak Ada") { ?>
                        <?php } else { ?>
                        <input type="file" class="form-control" id="file_<?= $index ?>" name="file_<?= $index ?>">
                        <?php } ?>
                      <?php } ?>
                    <th><a class="btn btn-sm btn-danger" onclick="delete_doc('{{$doc->id}}','{{$index}}')"><i class="fa fa-trash"></i></a>
                    </th>
                  </tr>
                <?php $index++; ?>
              @endforeach
            <?php }
            ?>
          
          </thead>
          <tbody id="body_add">

              <?php  
              if (count($documents) == 0) { ?>
            <tr id="1" class="item">
            <td>
            <input type="text" name="lop" id="lop" value=1 hidden>
            <input type="text" name="loop_tp_1" id="loop_tp_1" value="1" hidden>
            <input type="text" name="detail_1" id="detail_1" class="form-control" placeholder="Detail Aksi" required="">
            </td>

            <td>
            <div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker2" id="due_date_1" name="due_date_1" placeholder="Due Date" required=""></div>
            </td>
            <td>
              <select class="form-control select2" id="dokumen_1" name="dokumen_1" data-placeholder="Keterangan Dokumen" style="width: 100%" required="" onchange="withDokumen(this)">
                <option value="">&nbsp;</option>
                <option value="Ada">Ada</option>
                <option value="Tidak Ada">Tidak Ada</option>
              </select>
            </td>

            <td>
            <!-- <input type="text" name="nomor_dokumen_1" id="nomor_dokumen_1" class="form-control" placeholder="Nomor Dokumen"> -->
            <select class="form-control select2" id="jenis_dokumen_1" name="jenis_dokumen_1" data-placeholder="Jenis Dokumen" style="width: 100%;display: none"><option value="">&nbsp;</option><option value="IK">IK</option><option value="Jishuhozen">Jishuhozen</option><option value="CDM">CDM</option><option value="Pengecekan Produk Pertama">Pengecekan Produk Pertama</option><option value="Dokumen Mutu">Dokumen Mutu</option><option value="Form Mutu">Form Mutu</option><option value="Daftar Hadir Sosialisasi">Daftar Hadir Sosialisasi</option><option value="Pembuatan Tools, Jig, dan Alat Ukur">Pembuatan Tools, Jig, dan Alat Ukur</option><option value="Lain-lain">Lain-lain</option><option value="None">None</option> </select>
            </td>

            <td>
            <input type="file" class="form-control" id="file_1" name="file_1" style="display:none">
            </td>
          </tbody>

          <?php  } ?>
        </table>
            </div>
            
      </div>

      <div class="col-md-12">
          <div style="background-color:orange;padding: 10px;text-align: center;font-size: 20px;color: white;margin-bottom: 20px;">Yokotenkai Proses Serupa
          </div>
          <div class="col-md-12">
              <a id="tombol_proses_serupa_div" class="btn btn-sm btn-success pull-right" style="display: none" onclick="add_item_proses()"><i class="fa fa-plus"></i>&nbsp; Add Proses Serupa</a>
          </div>

          <div class="col-md-2" style="margin-top: 20px;">
              <label id="label_group">Proses Yang Serupa<span class="text-red">*</span></label>
          </div>

          <div class="col-md-2" style="margin-top: 20px;">
            <div class="form-group">

              @if($cars->proses_serupa == "" || $cars->proses_serupa == null)
              <select class="form-control select2" id="proses_serupa" name="proses_serupa" data-placeholder="Proses Serupa" style="width: 100%;" onchange="prosesSerupa(this)" required="">
                <option value="">&nbsp;</option>
                <option value="Ada">Ada</option>
                <option value="Tidak">Tidak</option>
              </select>

              @else
              <select class="form-control select2" id="proses_serupa" name="proses_serupa" data-placeholder="Proses Serupa" style="width: 100%;" onchange="prosesSerupa(this)">
                @if($cars->proses_serupa == "Ada")
                <option value="">&nbsp;</option>
                <option value="Ada" selected>Ada</option>
                <option value="Tidak">Tidak</option>
                @else
                <option value="">&nbsp;</option>
                <option value="Ada">Ada</option>
                <option value="Tidak" selected>Tidak</option>
                @endif
              </select>

              @endif
            </div>
          </div>

          <div class="col-md-8" id="proses_serupa_div" style="display: none;margin-top: 20px;">
            <div class="col-md-6" style="padding:0;">
              <input type="text" name="lop_proses" id="lop_proses" value="1" hidden>

              <textarea class="form-control" id="proses_serupa_detail_1" name="proses_serupa_detail_1" placeholder="Detail Yokotenkai"></textarea></td>
            </div>


            <div class="col-md-4" style="text-align:center;">
               <div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker2" id="due_date_yokotenkai_1" name="due_date_yokotenkai_1" placeholder="Tanggal Pelaksanaan Yokotenkai"></div>
            </div>
            <div class="col-md-2" style="text-align:center;">
              <input type="file" onchange="readURL(this,'');" id="proses_serupa_file_1" name="proses_serupa_file_1" style="display:none" class="file" accept="image/*" capture="environment">
              <button type="button" class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>
              <img width="150px" id="blah" src="" style="display: none" alt="your image" />

              <!-- <div id="proses_serupa_foto"></div> -->
            </div>
          </div>

          <div id="body_add_serupa"></div>
         
          @if($cars->proses_serupa_detail != null || $cars->proses_serupa_detail != "")
          <div class="col-md-12" >

          <?php 
            $data = json_decode($cars->proses_serupa_detail);
            $foto = json_decode($cars->proses_serupa_foto);

                for ($i = 0; $i < count($data); $i++) { ?>
                  <div class="col-md-6" style="margin-top:20px">
                    <div class="isi">
                      <?= $data[$i] ?>
                    </div>
                  </div>
                  <div  class="col-md-6" style="margin-top:20px">
                      <img src="{{ url('/files/car/yokotenkai/'.$foto[$i]) }}" width="150px">
                  </div>
                  <!-- <div class="col-md-1">
                      <a href="javascript:void(0)" onclick="hapus('{{$data[$i]}}','{{$cars->id}}')" class="btn btn-danger pull-left">
                        <i class="fa fa-trash"></i></a>
                  </div> -->      
                <br>
              <?php } ?> 

          </div>
          @endif

        </table>
      </div>

       
        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-5" style="margin-top: 20px">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qc_car') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
        <?php } ?>
      </div>
    </form>
  </div>
  
  <?php foreach ($cpar as $cpars){ ?>
  <div class="modal fade" id="modalkaryawan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Pilih PIC Yang Mengerjakan CPAR {{$cpars->cpar_no}}</h4>
        </div>
        <div class="modal-body">

          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <input type="hidden" id="cars" value="{{ $cars->id }}">
            <center><h4><b> Komplain : {{ $cpars->judul_komplain }} </b></h4></center>
            <center><h5>Sumber : {{ $cpars->sumber_komplain }} </h5></center>
            <br>

            <center><a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain"  target="_blank">Preview CPAR Report</a></center><br><br>

            <!-- Kategori : {{$cpars->kategori}}
            Lokasi : {{$cpars->lokasi}} -->
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">
                  Staff / Foreman
                  <span class="text-red">*</span></label>
              <div class="col-sm-8">
                <select class="form-control select3" id="pic" name="pic" style="width: 100%;" data-placeholder="Pilih PIC" required>
                  <option value=""></option>
                  @foreach($pic as $pic)
                    <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
            <button type="button" onclick="create_pic()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-send"></i>    Confirm And Send</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="statusmodal{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Status CAR</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <table class="table table-hover">
              <tbody>
                <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                  <tr style="background-color: #4caf50;color: white">
                      <td colspan="2" style="width: 33%"><b>Position</b></td>
                      <td colspan="2" style="width: 33%"><b>Action</b></td>
                      <td colspan="2" style="width: 33%"><b>Email</b></td>
                  </tr>
                  <tr>
                      <td colspan="2"><b>
                        @if($cars->car_cpar->kategori == "Internal") 
                              Staff / Leader
                          @elseif($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") 
                              Staff
                          @endif
                      </b></td>
                      @if(($cars->email_status == "SentStaff" && $cars->posisi == "staff") || ($cars->email_status == "SentForeman" && $cars->posisi == "foreman")) 
                      <td colspan="2"><b><span class="label label-success">On Progress</span></b></td>
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @else
                      <td colspan="2"><b><span class="label label-warning">Verification</span></b></td>
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2">
                        <b>
                          @if($cars->car_cpar->kategori == "Internal") 
                              Chief / Foreman
                          @elseif($cars->car_cpar->kategori == "Eksternal") 
                              Chief
                          @elseif($cars->car_cpar->kategori == "Supplier")
                              Coordinator
                          @endif
                        </b>
                      </td>
                      <td colspan="2"><b>
                        @if($cars->checked_chief == "Checked" || $cars->checked_foreman == "Checked" || $cars->checked_coordinator == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cars->email_status == "SentManager" || $cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "manager" || $cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "qa"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>Manager</b></td>
                      <td colspan="2"><b>
                        @if($cars->checked_manager == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "qa"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>DGM</b></td>
                      <td colspan="2"><b>
                        @if($cars->approved_dgm == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "gm" || $cars->posisi == "qa"))
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>GM</b></td>
                      <td colspan="2"><b>
                        @if($cars->approved_gm == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if($cars->email_status == "SentQA" && $cars->posisi == "qa")
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
              </tbody>
          </table>
          </div>    
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

<!-- <div class="modal fade" id="email{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Pilih Verifikator yang dituju</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
           <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <label class="col-sm-3">Chief / Foreman / Manager<span class="text-red"> *</span></label>
              <div class="col-sm-8">
                <select class="form-control select2" id="cf" name="cf" style="width: 100%;" data-placeholder="Pilih Chief / Foreman / Manager" required>
                  <option value=""></option>
                  @foreach($cfm as $cfm)
                  <option value="{{ $cfm->employee_id }}">{{ $cfm->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div> -->

<div class="modal fade" id="modalImage">
    <div class="modal-dialog modal-lg">
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

  <?php } ?>
  
  @endsection-
  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script type="text/javascript">


  // jQuery(document).ready(function() {
  //     add_item();      
  // });

    var no = 2;
    var no_proses = 2;
    var count_document = '{{count($documents)}}';
    
    $('body').toggleClass("sidebar-collapse");

    $(".plusdata").click(function(){ 
        var html = $(".clone").html();
        $(".increment").after(html);
    });

     $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });

     if ("{{$cars->proses_serupa}}" == "Ada") {
        $('#proses_serupa_div').show();  
        $('#tombol_proses_serupa_div').show();  
     };

    var checkpic = $("#checkpic").val()

    if(checkpic == "") {
      $(window).on('load',function(){
          $('#modalkaryawan').modal('show');
      });
    }
  </script>

  <script>
    
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(function () {
      $('.select2').select2()
    })

    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm",
      todayHighlight: true,
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });

    $(".datepicker2").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
      });

    $(function () {
      $('.select3').select2({
        dropdownParent: $('#modalkaryawan')
      });
    })

    $(function(){

        // if ("{{$cars->perubahan_dokumen}}" == "Ada") {
        //   $('#change_dokumen').show(); 
        // }
  

        $('#man').click(function() {
            if($(this).is(':checked'))
                $("#manhidden").remove();
            else
                document.getElementById("man").value = "0";
        });
        
        $('#machine').click(function() {
            if($(this).is(':checked'))
                $("#machinehidden").remove();
            else
                document.getElementById("machine").value = "0";
        });

        $('#material').click(function() {
            if($(this).is(':checked'))
                $("#materialhidden").remove();
            else
                document.getElementById("material").value = "0";
        });

        $('#method').click(function() {
            if($(this).is(':checked'))
                $("#methodhidden").remove();
            else
                document.getElementById("method").value = "0";
        });
    });

    function create_pic() {

      var data = {
        pic: $("#pic").val()
      };

      // console.log(data);

      $.post('{{ url("index/qc_car/create_pic/".$cars->id) }}', data, function(result, status, xhr){
        // console.log(result.status);
        if (result.status == true) {
          openSuccessGritter("Success","Pic has been choosen");
          window.location.reload();
        } else {
          openErrorGritter("Error","Cannot Create PIC");
        }
      })
    }

    CKEDITOR.replace('deskripsi' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
    });

    CKEDITOR.replace('tindakan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
    });

    CKEDITOR.replace('penyebab' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
    });

    CKEDITOR.replace('perbaikan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
    });

    CKEDITOR.replace('pencegahan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
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

    function hapus(nama_file,idcar){
        var data = {
          nama_file : nama_file,
          idcar : idcar
        };
        $.post('{{ url("index/qc_car/deletefiles") }}', data, function(result, status, xhr){
          if(result.status){
            openSuccessGritter('Success Hapus File', result.message);
            location.reload();
          }
          else{
            openErrorGritter('Error!', result.message);
          }
        })
      }
    
    function sendemail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim CAR ini?")) {
        return false;
      }

      $("#loading").show();

      $.get('{{ url("index/qc_car/sendemail/$cars->id/$cars->posisi") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

    function add_item() {

      if (parseInt(count_document) > 0) {
        no = parseInt(count_document) + 1; 
      }

      var bodi = "";
      var loop_tp = "";
      var detail_tp = "";
      var jenis_dokumen_tp = "";
      var due_date_tp = "";
      var keterangan_tp = "";

      // employee_id_tp += "<option value=''></option>";
      // in_out_tp += "<option value=''></option>";
      keterangan_tp += "<option value=''></option>";

      bodi += '<tr id="'+no+'" class="item">';
      bodi += '<td>';
      bodi += '<input type="text" name="lop" id="lop" value='+no+' hidden>';
      bodi += '<input type="text" name="loop_tp_'+no+'" id="loop_tp_'+no+'" value="'+no+'" hidden>';
      bodi += '<input type="text" name="detail_'+no+'" id="detail_'+no+'" class="form-control" placeholder="Detail Aksi" required="">';
      bodi += '</td>';


      bodi += '<td>';
      bodi += '<div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker" id="due_date_'+no+'" name="due_date_'+no+'" placeholder="Due Date" required=""></div>';
      bodi += '</td>';

      bodi += '<td>';
      bodi += '<select class="form-control select2" id="dokumen_'+no+'" name="dokumen_'+no+'" data-placeholder="Keterangan Dokumen" style="width: 100%" required="" onchange="withDokumen(this)"><option value="">&nbsp;</option><option value="Ada">Ada</option><option value="Tidak Ada">Tidak Ada</option></select></td>';
      bodi += '<td>';
      // bodi += '<input type="text" name="nomor_dokumen_'+no+'" id="nomor_dokumen_'+no+'" class="form-control" placeholder="Nomor Dokumen">';
      bodi += '<select class="form-control select2" id="jenis_dokumen_'+no+'" name="jenis_dokumen_'+no+'" data-placeholder="Jenis Dokumen" style="width: 100%;display:none"><option value="">&nbsp;</option><option value="IK">IK</option><option value="Jishuhozen">Jishuhozen</option><option value="CDM">CDM</option><option value="Pengecekan Produk Pertama">Pengecekan Produk Pertama</option><option value="Dokumen Mutu">Dokumen Mutu</option><option value="Form Mutu">Form Mutu</option><option value="Daftar Hadir Sosialisasi">Daftar Hadir Sosialisasi</option><option value="Pembuatan Tools, Jig, dan Alat Ukur">Pembuatan Tools, Jig, dan Alat Ukur</option><option value="Lain-lain">Lain-lain</option><option value="None">None</option> </select>';
      bodi += '</td>';

      // bodi += '<td>';
      // bodi += '</td>';


      bodi += '<td>';
      bodi += '<input type="file" class="form-control" id="file_'+no+'" name="file_'+no+'" style="display:none">'
      bodi += '</td>';

      bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';
      bodi += '</tr>';

      $("#body_add").append(bodi);

      no++;
      count_document++;
      $('.select2').select2({
        allowClear : true
      });

      $(".datepicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
      });
    }

    function delete_item(nomor) {
      $("#"+nomor).remove();
      no--;  
    }

    function add_item_proses() {
      var bodi_proses = "";
      bodi_proses += '<div class="col-md-8 col-md-offset-3" id="proses_serupa_div_'+no_proses+'" style="margin-top:10px">';
      bodi_proses += '<div class="col-md-1 " style="padding:0;"><button class="btn btn-sm btn-danger" onclick="delete_item_body('+no_proses+')"><i class="fa fa-trash"></i></button></div>';
      bodi_proses += '<div class="col-md-5" style="padding:0;"><textarea class="form-control" id="proses_serupa_detail_'+no_proses+'" name="proses_serupa_detail_'+no_proses+'" placeholder="Detail Yokotenkai"></textarea></td></div>';bodi_proses += ' <div class="col-md-4"><div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker2" id="due_date_yokotenkai_'+no_proses+'" name="due_date_yokotenkai_'+no_proses+'" placeholder="Tanggal Pelaksanaan Yokotenkai" ></div></div>';
      bodi_proses += '<div class="col-md-2" style="padding:0;text-align:center;"><input type="file" onchange="readURL(this,\'\');" id="proses_serupa_file_'+no_proses+'" name="proses_serupa_file_'+no_proses+'" style="display:none" class="file" accept="image/*" capture="environment"><button type="button" class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button><img width="150px" id="blah" src="" style="display: none" alt="your image" /></div>';
      bodi_proses += '';
      bodi_proses += '<div>';

      $("#body_add_serupa").append(bodi_proses);

      $("#lop_proses").val(no_proses);
      no_proses++;

      $(".datepicker2").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
      });
    }

    function delete_item_body(nomor) {
      $("#proses_serupa_div_"+nomor).remove();
      $("#lop_proses").val(no_proses-1);
      no_proses--;  
    }

    function delete_doc(id,index) {
      var data = {
        id: id
      };

      if (!confirm("Apakah anda yakin ingin menghapus document ini?")) {
        return false;
      }

      $.post('{{ url("index/qc_car/delete_document") }}', data, function(result, status, xhr){
        // $('#example1').DataTable().ajax.reload(null, false);
        $('#'+index).remove(); 
        openSuccessGritter("Success","Delete Material");
      })
    }

    function prosesSerupa(elem){
      if (elem.value == "Ada") {
        $('#proses_serupa_div').show();  
        $('#tombol_proses_serupa_div').show();      
      }else{
        $('#proses_serupa_div').hide();  
        $('#tombol_proses_serupa_div').hide();   
      }
    }

    function perubahanDokumen(elem){
      if (elem.value == "Ada") {
        $('#change_dokumen').show();        
      }else{
        $('#change_dokumen').hide();    
      }
    }


  function buttonImage(elem) {
    $(elem).closest("div").find("input").click();
  }

  function readURL(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          var img = $(input).closest("div").find("img#blah");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      $(input).closest("div").find("button").hide();
    }

    function modalImage(title) {
      var photos = '';
      if (title == "deskripsi") {
        photos += '<img style="width:800px" src="{{url("images/car/deskripsi_car.png")}}" class="user-image" alt="User image" >';
      } else if (title == "penanganan") {
        photos += '<img style="width:800px" src="{{url("images/car/penanganan_car.png")}}" class="user-image" alt="User image" >';
      } else if (title == "penyebab") {
        photos += '<img style="width:800px" src="{{url("images/car/penyebab_car.png")}}" class="user-image" alt="User image" >';
      } else if (title == "perbaikan") {
        photos += '<img style="width:800px" src="{{url("images/car/perbaikan_car.png")}}" class="user-image" alt="User image" >';
      } else if (title == "pencegahan") {
        photos += '<img style="width:800px" src="{{url("images/car/pencegahan_car.png")}}" class="user-image" alt="User image" >';
      }
      $('#images').html(photos);
      $('#modalImage').modal('show');
    }

    function withDokumen(elem){
      var id = elem.id;
      var num = id.split("_")[1];

      // console.log(num);

      if (elem.value == "Ada") {
        $("#jenis_dokumen_"+num).next(".select2-container").show();
        $("#file_"+num).show();
      } else if (elem.value == "Tidak Ada") {
        $("#jenis_dokumen_"+num).next(".select2-container").hide();
        $("#file_"+num).hide();
      }
    }

  </script>
@stop