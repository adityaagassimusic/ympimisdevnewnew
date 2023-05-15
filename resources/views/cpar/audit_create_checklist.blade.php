@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .col-xs-2{
    padding-top: 5px;
  }
  .col-xs-10{
    padding-top: 5px;
  }
  .col-xs-3{
    padding-top: 5px;
  }
  .col-xs-5{
    padding-top: 5px;
  }
  .col-xs-6{
    padding-top: 5px;
  }
  .col-xs-7{
    padding-top: 5px;
  }
  .col-xs-8{
    padding-top: 5px;
  }
  input[type=checkbox] {
    transform: scale(1.5);
  }
  
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Buat {{ $page }}
    <small><b>E</b>lectronic-<b>I</b>nternal <b>R</b>equest <b>C</b>orrective <b>A</b>ction</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}
 </ol>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
      {{-- <h3 class="box-title">Create New</h3> --}}
    </div>  
    <form role="form">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="form_identitas">Auditor / Penemu Masalah</label>
            <input type="text" id="form_identitas" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}} ({{$employee->department}})" readonly>
            <input type="hidden" id="auditor" class="form-control" value="{{$employee->employee_id}}" readonly>
            <input type="hidden" id="auditor_name" class="form-control" value="{{$employee->name}}" readonly>
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
            <label for="auditor_tgl">Tanggal Terbit</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control datepicker" id="auditor_tgl" placeholder="Masukkan Tanggal Kejadian" value="<?php echo date('d F Y'); ?>" disabled="">
              <input type="hidden" class="form-control" id="auditor_date" value="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
            <label for="auditor_jenis">Jenis</label>
            <input type="text" class="form-control" id="auditor_jenis" placeholder="Masukkan Kategori" value="Audit Internal" readonly="">          
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
            <label for="auditor_lokasi">Lokasi</label>
             <input type="text" class="form-control" id="auditor_lokasi" placeholder="Masukkan Lokasi" value="{{ $audit->lokasi }}" readonly="">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-2">
            <label for="auditor_kategori">Kategori</label>
            <select class="form-control select2" name="auditor_kategori" id="auditor_kategori" style="width: 100%;" data-placeholder="Pilih Kategori" >
              <option value="{{$audit->kategori}}">{{$audit->kategori}}</option>
            </select>
          </div>
          <div id="syarat">
            <div class="col-xs-2">
              <label for="auditor_persyaratan">Persyaratan</label>
              <div class="input-group" style="margin-top: 5px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="1" id="auditor_persyaratan">Prosedur (Manual)
                </label>
              </div>
            </div>
            <div class="col-xs-2">
              <label for="auditor_persyaratan"></label>
              <div class="input-group" style="margin-top: 10px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="2" id="auditor_persyaratan">Standart Produk/Spesifikasi
                </label>
              </div>
            </div>
            <div class="col-xs-2">
              <label for="auditor_persyaratan"></label>
              <div class="input-group" style="margin-top: 10px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="3" id="auditor_persyaratan">Persyaratan Pelanggan
                </div>
              </div>
              <div class="col-xs-2">
                <label for="auditor_persyaratan"></label>
                <div class="input-group" style="margin-top: 10px">
                  <label class="checkbox-inline">
                    <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="4" id="auditor_persyaratan">Keputusan Top Manajemen
                  </div>
                </div>
                <div class="col-xs-2">
                  <label for="auditor_persyaratan"></label>
                  <div class="input-group" style="margin-top: 10px">
                    <label class="checkbox-inline">
                      <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="5" id="auditor_persyaratan">Peraturan
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6">
                  <label for="auditor_permasalahan">Uraian Permasalahan</label>
                  <textarea class="form-control" id="auditor_permasalahan"><?= $audit->note ?></textarea>
                </div>
                <div class="col-xs-6">
                  <label for="auditor_bukti">Bukti Temuan (Yang Mendukung Uraian Permasalahan)</label>
                  <textarea class="form-control" id="auditor_bukti"><img src="{{url('files/audit_iso/'.$audit->foto)}}" width="300"></textarea>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <label for="auditee">Pilih Auditee</label>
                  <input type="text" class="form-control" id="auditee_all" placeholder="Auditee" readonly value="{{$audit->auditee}} - {{$audit->auditee_name}}">
                  <input type="hidden" class="form-control" name="auditee" id="auditee" placeholder="auditee" value="{{$audit->auditee}}">
                  <input type="hidden" class="form-control" name="auditee_name" id="auditee_name" placeholder="auditee_name" value="{{$audit->auditee_name}}">
                </div>
                <div class="col-xs-6">

                  <input type="hidden" class="form-control" id="auditee_due_date" placeholder="Masukkan Due Date" value="<?= date('Y-m-d', strtotime(date('Y-m-d') .'+ 1 month'));?>">
                  <input type="hidden" class="form-control" id="id_checklist" value="{{ $audit->id }}">
                  <label for="auditee">Nomor Audit CPAR</label>
                  <input type="text" class="form-control" id="audit_no" placeholder="Audit CPAR" required readonly>
                </div>

              </div>
            </div>
            <div class="row">
              <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
                <div class="btn-group">
                  <a class="btn btn-danger" href="{{ url('index/audit_iso') }}">Cancel</a>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-primary pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      @endsection

      @section('scripts')
      <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
      <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
      <script type="text/javascript">
        $(document).ready(function() {
          $("body").on("click",".btn-danger",function(){ 
            $(this).parents(".control-group").remove();
          });

          var kat = document.getElementById("auditor_kategori");
          var getkat = kat.options[kat.selectedIndex].value;

          var auditno = document.getElementById("audit_no");

          if (getkat == "ISO 9001") {
            kategori = "Q";
          }
          else if (getkat == "ISO 14001"){
            kategori = "E";
          }
          else if (getkat == "ISO 45001"){
            kategori = "S";
          }

          $.ajax({
            url: "{{ url('index/audit_iso/get_nomor_depan') }}?kategori=" + getkat, 
            type : 'GET', 
            success : function(data){
              var obj = jQuery.parseJSON(data);
              var nomordepan = obj;
            // console.log(nomordepan);
            if (obj != null) {
              if (nomordepan == 0) {
                var lastnum = 0;
              }
              if (nomordepan.length == 2) {
                var lastnum = nomordepan.substr(nomordepan.length - 1);              
              }
              else if (nomordepan.length == 3) {
                var lastnum = nomordepan.substr(nomordepan.length - 2);
              }              
              else if (nomordepan.length == 4) {
                var lastnum = nomordepan.substr(nomordepan.length - 3);
              }
            }

            no = parseInt(lastnum) + 1;
            auditno.value = kategori+no;
          }
        });
        });

      </script>
      <script>

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        }); 

        jQuery(document).ready(function() {
          $('body').toggleClass("sidebar-collapse");
          $("#navbar-collapse").text('');
          $('.select2').select2({
            language : {
              noResults : function(params) {
            // return "There is no cpar with status 'close'";
          }
        }
      });


        });

        $(function () {
          $('.select2').select2()
        });

        function selectemployee(){
          var auditee = document.getElementById("auditee").value;

          $.ajax({
           url: "{{ url('index/audit_iso/get_nama') }}?auditee=" +auditee, 
           type : 'GET', 
           success : function(data){
            var obj = jQuery.parseJSON(data);
            $('#auditee_name').val(obj[0].name);
          }
        });      
        }

        $('.datepicker').datepicker({
          format: "dd/mm/yyyy",
          autoclose: true,
          todayHighlight: true
        });

        $('.datepicker2').datepicker({
          format: "yyyy-mm-dd",
          autoclose: true,
          todayHighlight: true
        });

        $("#form_submit").click( function() {
          $("#loading").show();

          if ($("#auditor_lokasi").val() == "") {
            $("#loading").hide();
            alert("Kolom Lokasi Harap diisi");
            $("html").scrollTop(0);
            return false;
          }
          check_list = [];

          $(".auditor_persyaratanCheckbox").each(function( i ) {
            if ($(this).is(':checked')) {
              check_list.push(1);
            } else {
              check_list.push(0);
            }
          });


          if(jQuery.inArray(1, check_list) !== -1) {
            
          } else {
            $("#loading").hide();
            alert("Kolom Persyaratan Harap diisi");
            $("html").scrollTop(0);
            return false;
          }

          if ($("#auditor_kategori").val() == "") {
            $("#loading").hide();
            alert("Kolom Kategori Harap diisi");
            $("html").scrollTop(0);
            return false;
          }

          if (CKEDITOR.instances.auditor_permasalahan.getData() == "") {
            $("#loading").hide();
            alert("Kolom Permasalahan Harap diisi");
            $("html").scrollTop(0);
            return false;
          }

          if (CKEDITOR.instances.auditor_bukti.getData() == "") {
            $("#loading").hide();
            alert("Kolom Bukti Harap diisi");
            $("html").scrollTop(0);
            return false;
          }

          if ($("#auditee").val() == "") {
            $("#loading").hide();
            alert("Kolom Auditee Harap diisi");
            $("html").scrollTop(0);
            return false;
          }

          if ($("#auditee_due_date").val() == "") {
            $("#loading").hide();
            alert("Kolom Auditee Due Date Harap diisi");
            $("html").scrollTop(0);
            return false;
          }

          var type = [];
          var tinjauan;
          $("input[name='auditor_persyaratan']:checked").each(function (i) {
            type[i] = $(this).val();
          });

          var data = {
            auditor: $("#auditor").val(),
            auditor_name: $("#auditor_name").val(),
            auditor_date: $("#auditor_date").val(),
            auditor_jenis: $("#auditor_jenis").val(),
            auditor_lokasi: $("#auditor_lokasi").val(),
            auditor_kategori: $("#auditor_kategori").val(),
            audit_no: $("#audit_no").val(),
            auditor_persyaratan: type.join(),
            auditor_permasalahan: CKEDITOR.instances.auditor_permasalahan.getData(),
            auditor_bukti: CKEDITOR.instances.auditor_bukti.getData(),
            auditee: $("#auditee").val(),
            auditee_name: $("#auditee_name").val(),
            auditee_due_date: $("#auditee_due_date").val(),
            id_checklist: $("#id_checklist").val()
          };

          $.post('{{ url("post/audit_iso_checklist/create") }}', data, function(result, status, xhr){
            if(result.status == true){    
              $("#loading").hide();
              openSuccessGritter("Success","Data Berhasil Dibuat & Dikirim Ke Standarisasi");
              setTimeout(function(){  window.location = "{{url('index/audit_iso')}}"; }, 1000);
            }
            else {
              $("#loading").hide();
              openErrorGritter('Error!', result.datas);
            }

          });

        });

        CKEDITOR.replace('auditor_permasalahan' ,{
          filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
          height: '200px'
        });

        CKEDITOR.replace('auditor_bukti' ,{
          filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
          height: '200px'
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
            time: '2000'
          });
        }

      </script>
      @stop

