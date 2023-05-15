@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
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
  border:1px solid green;
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
 <h1>
  Sakurentsu <span class="text-purple"> {{ $title_jp }}</span>
</h1>
<ol class="breadcrumb">
</ol>
</section>
@endsection

@section('content')
<section class="content">
 @if (session('success'))
 <div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
  {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h4><i class="icon fa fa-ban"></i> Error!</h4>
  {{ session('error') }}
</div>
@endif

<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
  <p style="position: absolute; color: White; top: 45%; left: 35%;">
   <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i><br>少々お待ちください</span>
 </p>
</div>

<div class="row">
  <div class="col-xs-5" style="padding-right: 0">
   <div class="box box-solid">
    <div class="box-header">
      <h3 class="box-title">Transfer Sakurentsu Information<span class="text-purple">作連通情報共有</span></h3>
    </div>
    <div class="box-body">
     <div class="row">
      <div class="col-xs-12">
       <div class="form-group">

        <form action="{{ url('index/sakurentsu/upload_sakurentsu') }}" enctype="multipart/form-data" method="POST" id="upload_form">
          <div class="col-xs-12" style="padding: 0">

            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <input type="hidden" id="applicant" name="applicant" class="form-control" value="{{$employee->name}}" readonly>
            
            <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="input"><span class="text-red">*</span>Sakuretsu Number <span class="text-purple">作連通番号</span></label>              
                <input type="text" name="sakurentsu_number" id="sakurentsu_number" placeholder="Input Sakurentsu Number or Reff Number" class="form-control">
              </div>
            </div>
            <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="input"><span class="text-red">*</span>Sakurentsu Title <span class="text-purple">作連通の表題</span></label>
                <input type="text" name="title_jp" id="title_jp" placeholder="Input title here" class="form-control">
              </div>
            </div>

            <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="sakurentsu_category"><span class="text-red">*</span>Sakurentsu Category <span class="text-purple">作連通のカテゴリ</span></label><br>
                <select class="select2" name="sakurentsu_category" id="sakurentsu_category" data-placeholder="Select Category" style="width: 100%" onchange="category_change(this)">
                  <option value=""></option>
                  <option value="3M">3M</option>
                  <option value="Trial">Trial Request 試作依頼</option>
                  <option value="Information">Information 情報</option>
                </select>
              </div>
            </div>

          <!--   <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="sakurentsu_category"><span class="text-red">*</span>Sakurentsu Category <span class="text-purple">作連通のカテゴリ</span></label><br>
                <select class="select2" name="sakurentsu_category" id="sakurentsu_category" data-placeholder="Select Category" style="width: 100%">
                  <option value=""></option>
                  <option value="3M">3M</option>
                  <option value="Trial">Trial Request 試作依頼</option>
                  <option value="Information">Information 情報</option>
                </select>
              </div>
            </div> -->
          </div>
          <div class="col-xs-12" style="padding: 0">
            <div class="form-group">
              <label><span class="text-red">*</span>Target Date <span class="text-purple">締切</span></label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="target_date" name="target_date" placeholder="Select Target Date">
              </div>
            </div>

            <div class="form-group" id="send_div" style="display: none">
              <label><span class="text-red">*</span>Send Trial Results <span class="text-purple">試作結果を送る</span></label> <br>
              <label class="radio-inline">
                <input type="radio" name="send_item" value="YES_NEW"><b>YES</b>, with New GMC
              </label>
              <label class="radio-inline">
                <input type="radio" name="send_item" value="YES_EXIST"><b>YES</b>, with Existing GMC
              </label>
              <label class="radio-inline">
                <input type="radio" name="send_item" value="NO"><b>NO</b>
              </label>
            </div>

            <div class="form-group" id="pss_div" style="display: none">
              <label><span class="text-red">*</span>PSS Requirement <span class="text-purple">PSS 要求</span></label> <br>
              <label class="radio-inline">
                <input type="radio" name="pss_req" value="PSS">Need PSS
              </label>
              <label class="radio-inline">
                <input type="radio" name="pss_req" value="">No Need
              </label>
            </div>

            <div class="form-group">
              <label><span class="text-red">*</span>Sakurentsu File <span class="text-purple">作連通ファイル</span></label>
              <input type="file" name="file[]" multiple="">
              <!-- <div class="dropzone" id="my-dropzone" name="mainFileUploader">
               <div class="fallback">
                 <input name="file" type="file" multiple />
               </div>
             </div> -->
           </div>
           <button type="submit" id="submit-all" class="btn btn-success pull-right" style="margin-top: 10px" onclick="loading()"><i class="fa fa-send"></i> Send</button>
         </div>
       </form>
       <div>
         <!-- <button type="submit" id="submit-all" class="btn btn-success pull-right" style="margin-top: 10px" onclick="location.reload()">Upload</button> -->
       </div>
     </div>
   </div>

 </div>
</div>
</div>
</div>
<div class="col-xs-7" style="padding: 0">
 <div class="col-xs-12">
  <div class="box box-solid">
    <div class="box-header">
      <h3 class="box-title">Sakurentsu <span class="text-purple">作連通</span></h3><br>
    </div>
    <div class="box-body">
     <div class="col-xs-12">
      <table id="sakurentsuTable" class="table table-bordered" style="width: 100%">
        <thead style="background-color: rgba(126,86,134,.7);">
         <tr>
          <th width="1%">Applicant <br> 申請者</th>
          <th width="1%">Number <br> 作連通の番号</th>
          <th width="1%">Target Date <br> 締切</th>
          <th width="1%">File <br> ファイル</th>
          <th width="1%">Category <br> カテゴリー</th>
          <th width="1%">Status <br> ステイタス</th>
          <th width="1%">Act <br> ??</th>
        </tr>
      </thead>
      <tbody id="tableSakurentsu">
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="modalFile">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="sk_num"></h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <b>Japanese</b>
          <table class="table table-hover table-bordered table-striped" id="tableFileJp">
            <tbody id='bodyFileJp'></tbody>
          </table>
          <b>Translate</b>
          <table class="table table-hover table-bordered table-striped" id="tableFile">
            <tbody id='bodyFile'></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><center><b id="title_modal">UPDATE SAKURENTSU</b></center></h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <div class="col-xs-12">
            <form action="{{ url('index/sakurentsu/update_sakurentsu') }}" enctype="multipart/form-data" method="POST" id="upload_form">
              <div class="col-xs-12" style="padding: 0">

                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <input type="hidden" id="applicant_edit" name="applicant_edit" class="form-control" value="{{$employee->name}}" readonly>
                <input type="hidden" id="id_edit" name="id_edit">

                <div class="col-xs-12" style="padding: 1px">
                  <div class="form-group">
                    <label for="input"><span class="text-red">*</span>Sakuretsu Number <span class="text-purple">作連通番号</span></label>              
                    <input type="text" name="sakurentsu_number_edit" id="sakurentsu_number_edit" placeholder="Input Sakurentsu Number or Reff Number" class="form-control">
                  </div>
                </div>
                <div class="col-xs-12" style="padding: 1px">
                  <div class="form-group">
                    <label for="input"><span class="text-red">*</span>Sakurentsu Title <span class="text-purple">作連通の表題</span></label>
                    <input type="text" name="title_jp_edit" id="title_jp_edit" placeholder="Input title here" class="form-control">
                  </div>
                </div>

                <div class="col-xs-12" style="padding: 1px">
                  <div class="form-group">
                    <label for="sakurentsu_category"><span class="text-red">*</span>Sakurentsu Category <span class="text-purple">作連通のカテゴリ</span></label><br>
                    <select class="select2" name="sakurentsu_category_edit" id="sakurentsu_category_edit" data-placeholder="Select Category" style="width: 100%" onchange="category_change_edit(this)">
                      <option value=""></option>
                      <option value="3M">3M</option>
                      <option value="Trial">Trial Request 試作依頼</option>
                      <option value="Information">Information 情報</option>
                    </select>
                  </div>
                </div>

              </div>
              <div class="col-xs-12" style="padding: 0">
                <div class="form-group">
                  <label><span class="text-red">*</span>Target Date <span class="text-purple">締切</span></label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="target_date_edit" name="target_date_edit" placeholder="Select Target Date">
                  </div>
                </div>

                <div class="form-group" id="send_div_edit" style="display: none">
                  <label><span class="text-red">*</span>Send Trial Results <span class="text-purple">試作結果を送る</span></label> <br>
                  <label class="radio-inline">
                    <input type="radio" name="send_item_edit" value="YES_NEW"><b>YES</b>, with New GMC
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="send_item_edit" value="YES_EXIST"><b>YES</b>, with Existing GMC
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="send_item_edit" value="NO"><b>NO</b>
                  </label>
                </div>

                <div class="form-group" id="pss_div_edit" style="display: none">
                  <label><span class="text-red">*</span>PSS Requirement <span class="text-purple">PSS 要求</span></label> <br>
                  <label class="radio-inline">
                    <input type="radio" name="pss_req_edit" value="PSS">Need PSS
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="pss_req_edit" value="">No Need
                  </label>
                </div>

                <div class="form-group">
                  <label><span class="text-red">*</span>Sakurentsu File <span class="text-purple">作連通ファイル</span></label>
                  <!-- <input type="file" name="file_edit[]" multiple=""> -->
                  <div id="file_edit"></div>
                </div>
                <button type="submit" id="submit-all_edit" class="btn btn-primary pull-right" style="margin-top: 10px" onclick="loading()"><i class="fa fa-check"></i> update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="delete_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><center>DELETE SAKURENTSU "<span id="sakurentsu_number_delete"></span>"</center></h4>
        <div class="modal-body table-responsive no-padding">
          <div class="col-xs-12">
            <center><label>Please Add Delete Notes</label></center>
          </div>
          <div class="col-xs-12">
            <input type="hidden" name="id_delete" id="id_delete">
            <textarea class="form-control" id="delete_notes" placeholder="Write delete notes"></textarea>
            <br>
          </div>
          <div class="col-xs-12">
            <button class="btn btn-danger pull-left"><i class="fa fa-close"></i> Cancel</button>
            <button class="btn btn-success pull-right" onclick="delete_sk()"><i class="fa fa-trash"></i> Delete</button>
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
<script src="{{ url("js/dropzone.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
 $.ajaxSetup({
  headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});

 var file = [];


 jQuery(document).ready(function() {
  fetchTable();  

  $('#target_date').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd',
    orientation: "bottom auto"
  });

  $('#target_date_edit').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd',
    orientation: "bottom auto"
  });

  $(".select2").select2();

});


 var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

 function loading() {
  $("#loading").show();
}

function fetchTable(){

  var data = {
  }

  $.get('{{ url("fetch/sakurentsu") }}', data, function(result, status, xhr){
   if(xhr.status == 200){
     if(result.status){

       $('#sakurentsuTable').DataTable().clear();
       $('#sakurentsuTable').DataTable().destroy();

       $("#tableSakurentsu").find("td").remove();  
       $('#tableSakurentsu').html("");

       var table = "";


       $.each(result.datas, function(key, value) {

         table += '<tr>';
         table += '<td width="1%">'+value.applicant+'</td>';
         table += '<td width="1%">'+value.sakurentsu_number+'</td>';
         table += '<td width="1%">'+value.target_date+'</td>';
         table += "<td width='1%'>"+('<button class="btn btn-xs" onclick="getFileInfo('+key+',\''+value.sakurentsu_number+'\')"><i class="fa fa-paperclip"> File(s)</i></button>' || '')+"</td>";
         table += '<td width="1%">'+value.category+'</td>';

         if (value.status == "translate") {
           table += '<td width="1%" style="background-color:yellow">Translating</td>';                    
         }else{
           table += '<td width="1%" style="background-color:green;color:white">Finish Translating</td>';
         }

         table += '<td width="1%"><button class="btn btn-primary btn-xs" onclick="openEditModal('+value.id+')"><i class="fa fa-pencil"></i> Edit</button><button class="btn btn-danger btn-xs" onclick="delete_modal('+value.id+', \''+value.sakurentsu_number+'\')"><i class="fa fa-trash"></i> Delete</button></td>';

         table += '</tr>';

         file.push({'sk_number' : value.sakurentsu_number, 'file' : value.file, 'file_translate' : value.file_translate});

       })

       $('#tableSakurentsu').append(table);

       var table = $('#sakurentsuTable').DataTable({
         'responsive':true,
         'lengthMenu': [
         [ 10, 25, 50, -1 ],
         [ '10 rows', '25 rows', '50 rows', 'Show all' ]
         ],
         'paging': true,
         'searching': true,
         'ordering': true,
         'order': [[ 2, "desc" ]],
         'info': true,
         'autoWidth': true,
         "sPaginationType": "numbers",
         "bJQueryUI": true,
         "bAutoWidth": false,
         "processing": true
       });
     }
     else{
      alert('Attempt to retrieve data failed');
    }
  }
});
}

function category_change(elem) {
  if ($(elem).val() == "Trial") {
    $("#pss_div").show();
    $("#send_div").show();
  } else {
    $("#pss_div").hide();
    $("#send_div").hide();
  }
}

function category_change_edit(elem) {
  if ($(elem).val() == "Trial") {
    $("#pss_div_edit").show();
    $("#send_div_edit").show();
  } else {
    $("#pss_div_edit").hide();
    $("#send_div_edit").hide();
  }
}

function getFileInfo(num, sk_num) {
  $("#sk_num").text(sk_num+" File(s)");

  $("#bodyFile").empty();

  body_file = "";
  $.each(file, function(key, value) {  
    if (sk_num == value.sk_number) {
      var obj = JSON.parse(value.file_translate);
      var app = "";

      console.log(obj);

      if (obj) {
        for (var i = 0; i < obj.length; i++) {
          body_file += "<tr>";
          body_file += "<td>";
          body_file += "<a href='"+"{{ url('files/translation/') }}/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
          body_file += "</td>";
          body_file += "</tr>";
        }
      }
    }
  });

  $("#bodyFile").append(body_file);

  $("#bodyFileJp").empty();

  body_file_jp = "";
  $.each(file, function(key, value) {  
    if (sk_num == value.sk_number) {
      var obj = JSON.parse(value.file);
      var app = "";

      if (obj) {
        for (var i = 0; i < obj.length; i++) {
         body_file_jp += "<tr>";
         body_file_jp += "<td>";
         body_file_jp += "<a href='"+"{{ url('files/translation/') }}/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
         body_file_jp += "</td>";
         body_file_jp += "</tr>";
       }
     }
   }
 });

  $("#bodyFileJp").append(body_file_jp);

  $("#modalFile").modal('show');
}

function openEditModal(id) {
  $("#edit_modal").modal('show');

  var data = {
    id : id
  }

  $.get('{{ url("fetch/sakurentsu/type") }}', data, function(result, status, xhr){
    $("#id_edit").val(result.datas.id);
    $("#sakurentsu_number_edit").val(result.datas.sakurentsu_number);
    $("#title_jp_edit").val(result.datas.title_jp);
    $("#sakurentsu_category_edit").val(result.datas.category).trigger('change');
    $("#target_date_edit").val(result.datas.target_date);
    $("input[name=send_item_edit][value=" + result.datas.send_status + "]").prop('checked', true);
    $("input[name=pss_req_edit][value=" + result.datas.remark + "]").prop('checked', true);

    $("#file_edit").empty();
    
    var obj = JSON.parse(result.datas.file);
    
    $.each(obj, function(key, value) {  
      $("#file_edit").append("<a href='"+"{{ url('files/translation/') }}/"+value+"'  target='_blank' class='btn btn-xs btn-primary'><i class='fa fa-file-pdf-o'></i> "+value+"</a>&nbsp;");
    });
  })
}

function delete_modal(id, sk_num) {
  $("#delete_modal").modal('show');

  $("#id_delete").val(id);
  $("#sakurentsu_number_delete").text(sk_num);
}

function delete_sk() {
  var id = $("#id_delete").val();
  var sk_num = $("#sakurentsu_number_delete").text();

  if (confirm('Are you sure want to delete this Sakurentsu "'+sk_num+'" ?')) {

    if ($("#delete_notes").val() == '') {
      openErrorGritter('Error', 'Please Add notes');
      return false;
    }

    var data = {
      id : id,
      notes : $("#delete_notes").val()
    }

    $.post('{{ url("delete/sakurentsu") }}', data, function(result, status, xhr){
      if (result.status) {
        $("#delete_notes").val('');
        openSuccessGritter('Success', 'Delete Successfully');
        setTimeout(function(){window.location.reload();},2000); 
      } else {
        openErrorGritter('Error', result.message);
      }
    })
  }
}

var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


function openErrorGritter(title, message) {
  jQuery.gritter.add({
   title: title,
   text: message,
   class_name: 'growl-danger',
   image: '{{ url("images/image-stop.png") }}',
   sticky: false,
   time: '2000'
 });
  audio_error.play();
}

function openSuccessGritter(title, message){
  jQuery.gritter.add({
   title: title,
   text: message,
   class_name: 'growl-success',
   image: '{{ url("images/image-screen.png") }}',
   sticky: false,
   time: '2000'
 });
  audio_ok.play();

}

</script>

@stop