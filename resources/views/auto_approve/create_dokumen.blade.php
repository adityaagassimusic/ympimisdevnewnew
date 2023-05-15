@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    vertical-align: middle;
    text-align: center;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(100, 100, 100);
    padding: 3px;
    vertical-align: middle;
    height: 45px;
    text-align: center;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(100, 100, 100);
    vertical-align: middle;
  }
  .dataTables_info,
  .dataTables_length {
    color: white;
  }

  div.dataTables_filter label, 
  div.dataTables_wrapper div.dataTables_info {
    color: white;
  }
  #loading, #error { display: none; }

  .container_{margin:10px;padding:5px;border:solid 1px #eee;}
  .image_upload > input{display:none;}
  input[type=text]{width:220px;height:auto;}

</style>
@endsection

@section('header')
<section class="content-header">
  <h1 class="pull-left" style="padding: 0px; margin: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h1>
</section>
@stop

@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 40%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12" id="form_input" style="display: none">
      <form id ="importForm" name="importForm" method="post" action="{{ url('adagio/send') }}" enctype="multipart/form-data" onsubmit="Loading()">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
          <div class="box-body">
            <div class="col-xs-7">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="box-body">
                <div class="row">
                  <input type="text" name="loping" id="loping" value="1" hidden>
                  <div class="col-md-8">
                    <div class="form-group">
                      <span style="font-weight: bold; font-size: 16px;">Kategori Dokumen (資料類) :<span class="text-red">*</span></span>
                      <select class="form-control select3" name="cat_app" id='cat_app' data-placeholder="Pilih" style="width: 100%; height: 100px;" onchange="selectType(this.value)" required>
                        <option value=""></option>
                        @php
                        $judul = array();
                        @endphp
                        @foreach($approvals as $approval)
                        @if(!in_array($approval->judul, $judul))
                        <option value="{{ $approval->judul }}/{{ $approval->created_by }}">{{ $approval->judul }}</option>
                        @php
                        array_push($judul, $approval->judul);
                        @endphp
                        @endif
                        @endforeach   
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <span style="font-weight: bold; font-size: 16px;">No Approval (承認番号) :<span class="text-red">*</span></span>
                    <input type="text" class="form-control" id="number" name="number" style="width: 100%; height: 35px" value="" readonly="" required>
                  </div>
                  <div class="col-xs-12">
                    <span style="font-weight: bold; font-size: 16px;">Judul Dokumen (資料名) :<span class="text-red">*</span></span>
                    <input type="text" class="form-control" id="detail" name="detail" style="width: 100%; height: 35px" value="" required>
                  </div>
                  <div class="col-xs-12" style="padding-top: 15px;">
                    <span style="font-weight: bold; font-size: 16px;">Judul Dokumen (Japanese) (資料名（日本語) :<span class="text-red">*</span></span>
                    <input type="text" class="form-control" id="jd_japan" name="jd_japan" style="width: 100%; height: 35px" value="" required>
                  </div>
                  <div class="col-xs-6" style="padding-top: 15px;">
                    <span style="font-weight: bold; font-size: 16px;">Departemen (課) :<span class="text-red">*</span></span>
                    <input type="text" class="form-control" readonly="" name="dept" id="dept" style="width: 100%; height: 35px">
                    <input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}/{{$employee->name}}/{{$employee->position}}" required>
                  </div>
                  <div class="col-md-6" style="padding-top: 15px;">
                    <div class="form-group">
                      <span style="font-weight: bold; font-size: 16px;">Tanggal Pembuatan (作成日) :<span class="text-red">*</span></span>
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" id="tanggal" name="tanggal" value="<?php echo date("d-m-Y"); ?>" readonly required>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <span style="font-weight: bold; font-size: 16px;">No Dokumen (資料番号) :</span>
                    <input type="text" class="form-control" id="no_dok" name="no_dok" style="width: 100%; height: 35px" value="">
                  </div>
                  <div class="col-xs-6">
                    <span style="font-weight: bold; font-size: 16px;">Lampiran (添付資料)<span class="text-red">*</span></span>
                    <span style="font-size: 13px;">(Hanya PDF (PDFのみ))</span>
                    <p class="image_upload">
                      <label for="file">
                        <a class="btn btn-warning" rel="nofollow"><span class='glyphicon glyphicon-paperclip'></span> Upload Dokumen (資料アップロード)</a>
                      </label>
                      <input type="file" name="file" id="file" accept="application/pdf" required onchange="DokumenMuncul()">
                      <label class="custom-file-label" for="file"></label>
                    </p>
                  </div>
                  <div class="col-xs-12">
                    <iframe id="dokumen_muncul" width=100% height=700></iframe>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12">
                    <!-- <span style="font-weight: bold; font-size: 16px;">Catatan (備考) :</span><span class="text-red">*</span> -->
                    <span style="font-weight: bold; font-size: 16px;" id="reason_note"></span><span class="text-red">*</span>
                    <textarea class="form-control" id="summary" name="summary" style="height: 100px" placeholder="Berikan Penjelasan singkat mengenai dokumen tersebut." required></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12" style="padding-top: 15px;">
                    <p>
                      <b>(<span class="text-red">*</span>)Wajib Diketahui (注意事項) :</b> <br>
                      1. Pilih kategori Dokumen sesuai dengan kebutuhan. (必要に応じて資料を選んでください。)<br>
                      2. Isikan no dokumen sesuai dengan dokumen existing. (現行資料に沿って資料番号をご記入ください。)<br>
                      3. Upload dokumen (Hanya dengan Format PDF). (PDF資料をアップロードしてください。)<br>
                      4. Tambahkan catatan jika ada note yang dirasa perlu untuk approver ketahui mengenai dokumen. (資料に関して承認者が事前に知る必要のある情報があれば備考欄にご記入ください。)<br>
                      5. Pastikan kembali semua isian sebelum klik tombol (Kirim Pengajuan). (送信ボタンを押す前に全ての欄が記入されたかご確認ください。)
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xs-5" style="padding-left: 0;">
              <div class="box box-primary">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <div class="box-body"  align="center">
                  <span id="header_list" style="font-weight: bold; font-size: 16px;"></span>
                  <div class="box-body">
                    <table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
                      <thead style="background-color: #605ca8; color: white;">
                        <tr>
                          <th style="width: 2%;">Urutan (順番)</th>
                          <th style="width: 4%;">Nama (名前)</th>
                          <th style="width: 2%;">Jabatan (役職)</th>
                          <th style="width: 2%;">Ket. (備考)</th>
                        </tr>         
                      </thead>
                      <tbody id="tableBodyList">
                      </tbody>
                    </table>
                  </div>
                 <!--  <span id="header_list_cc" style="font-weight: bold; font-size: 16px;"></span>
                  <div class="box-body">
                    <table class="table table-hover table-striped table-bordered" id="tableListCC" style="width: 100%;" >
                      <thead style="background-color: #605ca8; color: white;">
                        <tr>
                          <th style="width: 2%;">Urutan (順番)</th>
                          <th style="width: 4%;">Nama (名前)</th>
                          <th style="width: 2%;">Jabatan (役職)</th>
                          <th style="width: 2%;">Ket. (備考)</th>
                        </tr>         
                      </thead>
                      <tbody id="tableBodyListCC">
                      </tbody>
                    </table>
                  </div> -->
                </div>
              </div>
            </div>
            <div class="col-xs-5" style="padding-left: 0;">
              <center>
                <button id="kirim_us" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 50%;" type="submit">Kirim Pengajuan<br>提出物を提出する</button>
              </center>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  var no = 2;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $("#dokumen_muncul").hide();
    $('#datefrom').datepicker({
      autoclose: true,
      todayHighlight: true
    });
    $('.select2').select2({
      dropdownParent: $('#modalCreate'),
      allowClear : true
    });

    $('.select3').select2();
    selectResume();
    BuatDokumen();
    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
      autoclose: true,
    });
    $('#dept').show();
    $('#app_cat').show();

    $('#file').on('change',function(){
      var fileName = $('input[type=file]').val().split('\\').pop();
      $(this).next('.custom-file-label').html(fileName);
    })
  });

  function BuatDokumen(){
    $("#form_input").show();
  }

  function DokumenMuncul() {
    $("#dokumen_muncul").show();
    document.getElementById("dokumen_muncul").style.display = "block";
    var oFReader = new FileReader();
    oFReader.readAsDataURL(document.getElementById("file").files[0]);
    oFReader.onload = function(oFREvent) {
      document.getElementById("dokumen_muncul").src = oFREvent.target.result;
    };
  };

  function Loading(){
    $("#loading").show();
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '4000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '4000'
    });
  }

  function generateNoApproval(){
    var no = $('#number').val();
    var dept = $('#dept').val();
    var data = {
      no:no,
      dept:dept
    }
    $.get('<?php echo e(url("adagio/cek/nomor_file")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#number').val(result.no_appr);
        openSuccessGritter('Success!', result.message);
        $("#generate_no").hide();
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function CobakTest(){
    $.get('<?php echo e(url("adagio/cobak/watermark")); ?>', function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function selectType(type){
    var tipe = type;
    var data = {
      cat_app:tipe
    }

    $.get('<?php echo e(url("adagio/data/approval")); ?>', data, function(result, status, xhr){
      if(result.status){

        let lower = type.toLowerCase();
        if(lower.indexOf('special reason') >= 0) {
          $("#reason_note").html('Alasan (理由) : ');
        }else{
          $("#reason_note").html('Catatan (備考) : ');
        }

      $('#tableList').DataTable().clear();
      $('#tableList').DataTable().destroy();
      $('#tableBodyList').html("");
      $('#tableBodyList').empty();
      $("#generate_no").show();


      var tableData = '';
      var count = 1;
      $.each(result.lists, function(key, value) {

        $('#header_list').html("List Approval : "+value.judul);

        var identitas = value.user.split("/");
        $('#dept').val(value.department);
        $('#app_cat').val(value.category);
        $('#detail').val(value.judul);
        $('#jd_japan').val(value.jd_japan);
        $('#number').val(result.no_approval);


        tableData += '<tr>';
        tableData += '<td>Approver Ke '+ count +'</td>';
        tableData += '<td>'+ identitas[1] +'</td>';
        tableData += '<td>'+ identitas[2] +'</td>';
        tableData += '<td>'+ identitas[3] +'</td>';
        tableData += '</tr>';

        count += 1;
      });

      $('#tableBodyList').append(tableData);

      $('#tableListCC').DataTable().clear();
      $('#tableListCC').DataTable().destroy();
      $('#tableBodyListCC').html("");
      $('#tableBodyListCC').empty();
      $("#generate_no").show();


      var tableData = '';
      var count = 1;
      $.each(result.cc, function(key, value) {

        $('#header_list_cc').html("List CC : "+value.judul);

        var identitas = value.user.split("/");


        tableData += '<tr>';
        tableData += '<td>'+ identitas[1] +'</td>';
        tableData += '<td>'+ identitas[2] +'</td>';
        tableData += '<td>'+ identitas[3] +'</td>';
        tableData += '</tr>';

        count += 1;
      });

      $('#tableBodyListCC').append(tableData);
      openSuccessGritter('Success!', result.message);
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });
  }

  function generateNewSernum() {
    var nomorfile = document.getElementById("number");

    var dept = $("#dept").val();

    $.ajax({
      url: "{{ url('adagio/nomor_file') }}?dept="+dept, 
      type : 'GET', 
      success : function(data){
        var obj = jQuery.parseJSON(data);
        var number = obj.no_urut;
        var tahun = obj.tahun;
        var bulan = obj.bulan;
        var dept = obj.dept;

        nomorfile.value = dept+tahun+bulan+number;
      }
    });
  }

  function selectResume(id){
    var data = {
      status : 2,
      id:id,
      date_from:$('#date_from').val(),
      date_to:$('#date_to').val()
    }

    $.get('<?php echo e(url("adagio/data/resume")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableResume').DataTable().clear();
        $('#tableResume').DataTable().destroy();
        var tableData = '';
        $('#tableBodyResume').html("");
        $('#tableBodyResume').empty();

        var count = 1;

        $.each(result.resumes, function(key, value) {

          var identitas = value.nik.split("/");
          var sendmail = '{{ url("adagio/data/sendmail/") }}';
          var report = '{{ url("adagio/done/report")}}';
          var urldelete = '{{ url("adagio/data/delete/") }}';

          tableData += '<tr>';
          tableData += '<td>'+ count +'</td>';
          tableData += '<td>'+ value.no_transaction +'</td>';
          tableData += '<td>'+ identitas[0] +'</td>';
          tableData += '<td>'+ identitas[1] +'</td>';
          tableData += '<td>'+ identitas[2] +'</td>';
          tableData += '<td>'+ value.department +'</td>';
          tableData += '<td>'+ value.date +'</td>';
          if (value.remark == 0) {
            tableData += '<td><a onclick="sendMail('+value.id+')" target="_blank" class="btn btn-success btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Send Mail</a><a href="'+report+'/'+value.id+'" target="_blank" class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Report"><i class="fa fa-file-pdf-o"></i> Report</a><a onclick="deleteList('+value.id+')" target="_blank" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i> Delete</a></td>';  
          }else if(value.remark == 'All Approved'){
            tableData += '<td><a href="'+report+'/'+value.id+'" target="_blank" data-toggle="tooltip" title="Report"><img width="30" src="'+"{{ asset('files/file_approval/centang.png') }}"+'" alt="" style="padding: 0"></a></td>';
          }
          else if(value.remark == 'Rejected'){
            tableData += '<td><a href="'+report+'/'+value.id+'" target="_blank" data-toggle="tooltip" title="Report"><img width="30" src="'+"{{ asset('files/file_approval/silang.png') }}"+'" alt="" style="padding: 0"></a></td>';
          }
          else{
            tableData += '<td><a href="'+report+'/'+value.id+'" target="_blank" data-toggle="tooltip" title="Report"><img width="50" src="'+"{{ asset('files/file_approval/progress.png') }}"+'" alt="" style="padding: 0"></a></td>';
          }

          tableData += '</tr>';

          count += 1;
        });

        $('#tableBodyResume').append(tableData);
        var tableResume = $('#tableResume').DataTable({
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
        $('#modalLocation').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function clearConfirmation(){
    location.reload(true);    
  }
</script>

@endsection