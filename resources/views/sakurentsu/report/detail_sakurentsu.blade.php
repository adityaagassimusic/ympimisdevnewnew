@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
    border:1px solid black;
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  #table_trial_1 > tbody > tr > th, #table_trial_2 > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    background-color: #a488aa;
  }

  #table_trial_1 > tbody > tr > td{
    padding: 0px;
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
      <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-6" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-header">
          <h3 class="box-title"><span class="text-purple">Detail Sakurentsu</span></h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <form class="form-horizontal">
                <div class="box-body">
                  <div class="form-group">
                    <label for="sk_number" class="col-sm-4 control-label">Sakuretsu Number</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="sk_number" readonly="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="sk_title" class="col-sm-4 control-label">Title</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="sk_title" readonly="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="sk_target" class="col-sm-4 control-label">Target Date</label>

                    <div class="col-sm-4">
                      <input type="text" class="form-control" id="sk_target" readonly="">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="sk_translate" class="col-sm-4 control-label">Translate Date</label>

                    <div class="col-sm-4">
                      <input type="text" class="form-control" id="sk_translate" readonly="">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-4 control-label">File</label>

                    <div class="col-sm-8" id="sk_file">

                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-4 control-label">Sakurentsu Category<span class="text-red">*</span></label>

                    <div class="col-sm-8">
                      <select class="select2" data-placeholder="Select Category of Sakurentsu" id="select_form" style="width: 100%">
                        <option value=""></option>
                        <option value="Trial">Trial Request</option>
                        <option value="3M">3M</option>
                        <option value="Information">Information</option>
                        <option value="Not Related">Not Related</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group" id="send_item_trial" style="display: none">
                    <label class="col-sm-4 control-label">Send Trial Result<span class="text-red">*</span></label>
                    <div class="col-sm-8">
                      <div class="radio">
                        <label>
                          <input type="radio" name="select_trial_result" value="YES_NEW"> YES, With NEW GMC
                        </label>
                      </div>

                      <div class="radio">
                        <label>
                          <input type="radio" name="select_trial_result" value="YES_EXIST"> YES, With EXISTING GMC
                        </label>
                      </div>

                      <div class="radio">
                        <label>
                          <input type="radio" name="select_trial_result" value="NO"> NO
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group" id="three_m_cat" style="display: none">
                    <label class="col-sm-4 control-label">3M Category<span class="text-red">*</span></label>
                    <div class="col-sm-8">
                      <select class="select2" data-placeholder="Select Category of 3M" id="select_cat_three_m" style="width: 100%">
                        <option value=""></option>
                        <option value="Metode">Metode</option>
                        <option value="Material">Material</option>
                        <option value="Mesin">Mesin</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group" id="pic_dept">
                    <label class="col-sm-4 control-label">PIC Department<span class="text-red">*</span></label>

                    <div class="col-sm-8">
                      <select data-placeholder="Select PIC Department" id="select_dept_form" style="width: 100%" multiple="multiple">
                      </select>
                    </div>
                  </div>

                  <div class="form-group" id="lampiran">
                    <label class="col-sm-4 control-label">Attachment</label>

                    <div class="col-sm-8">
                      <input type="file" name="lampiran_file[]" id="lampiran_file" multiple>
                    </div>
                  </div>

                  <button type="button" class="btn btn-success pull-right" onclick="submit_sk()"><i class="fa fa-check"></i> ACCEPT</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-6" style="padding: 0">
      <div class="col-xs-12">
        <div class="box box-solid">
          <div class="box-header">
            <h3 class="box-title"><span class="text-purple">Detail Sakurentsu (Original Version)</span></h3><br>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-xs-12">
                <form class="form-horizontal">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="sk_number_jp" class="col-sm-4 control-label">Sakuretsu Number</label>

                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="sk_number_jp" readonly="">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="sk_title_jp" class="col-sm-4 control-label">Title</label>

                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="sk_title_jp" readonly="">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="sk_upload" class="col-sm-4 control-label">Upload Date</label>

                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="sk_upload" readonly="">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 control-label">File</label>

                      <div class="col-sm-8" id="sk_file_jp">

                      </div>
                    </div>
                  </div>
                </form>
              </div>

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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");


    $('.select2').select2();
    $('#select_dept_form').select2();

    $("#select_form").val("").trigger("change");
    $("#select_dept_form").val("").trigger("change");

    getDatas();
  });

  
 //  $("#select_form").on("change",function(){
 //    console.log($(this).val());
 //    if ($(this).val() == "3M") {

 //      $("#select_cat_three_m").show();

 //    } else {
 //      $("#select_cat_three_m").hide();
 //        $('#select_cat_three_m').next(".select2-container").hide();
 //    }
 // });

 function getDatas() {
  var id = "{{ Request::segment(4) }}";

  var data = {
    id : id
  }

  $.get('{{ url("fetch/sakurentsu/type") }}', data, function(result, status, xhr){
    var obj = JSON.parse(result.datas.file_translate);
    var app = "";

    $.each(obj, function(key, value) {          
      app += "<a href='"+'{{ url("files/translation/") }}'+"/"+value+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value+"</a><br>";
    })

    var obj2 = JSON.parse(result.datas.file);
    var app2 = "";

    $.each(obj2, function(key, value) {
      app2 += "<a href='"+'{{ url("files/translation/") }}'+"/"+value+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value+"</a><br>";
    })

    $("#sk_number").val(result.datas.sakurentsu_number);
    $("#sk_title").val(result.datas.title);
    $("#sk_target").val(result.datas.target_date);
    $("#sk_upload").val(result.datas.upload_date);
    $("#sk_translate").val(result.datas.translate_date);
    $("#sk_file").append(app);

    $("#sk_number_jp").val(result.datas.sakurentsu_number);
    $("#sk_title_jp").val(result.datas.title_jp);
    $("#select_form").val(result.datas.category).trigger("change");

    if (result.datas.category == 'Trial') {
      $("#send_item_trial").show();
      var $radios = $('input:radio[name=select_trial_result]');
      if($radios.is(':checked') === false) {
        $radios.filter('[value='+result.datas.send_status+']').prop('checked', true);
      }
    }


    $("#sk_file_jp").append(app2);
  })

}

function submit_sk() {
  if (confirm('Are You Sure Want to Accept This Sakurentsu and Send to PIC Department?')) {
    var cat = $("#select_form").val();
    var dept = $("#select_dept_form").val();
    var sort_dept = $('#select_dept_form').select2('data').text;
    var sk_number = $("#sk_number").val();
    var tiga_em_cat = $("#select_cat_three_m").val();
    var send_item = $("input[name='select_trial_result']:checked").val();

    if ($("#select_dept_form").val() == '' && $("#select_form").val() != 'Not Related') {
      openErrorGritter('Error', 'Please Select Department');
      return false;
    }

    if($("#select_form").val() == '3M') {
      if ($("#select_cat_three_m").val() == '') {
        openErrorGritter('Error', '3M Category cannot be empty');
        return false;
      }
    }

    var sk_number = $("#sk_number").val();

    var formData = new FormData();

    var att_count = 0;
    for (var i = 0; i < $('#lampiran_file').prop('files').length; i++) {
      formData.append('lampiran_file_'+i, $('#lampiran_file').prop('files')[i]);
      att_count++;
    }
    formData.append('att_count', att_count);

    formData.append('sk_number', sk_number);
    formData.append('ctg', cat);
    formData.append('dept', dept);
    formData.append('sort_dept', sort_dept);
    formData.append('tiga_em_cat', tiga_em_cat);
    formData.append('send_item_trial', send_item);

    $("#loading").show();

    $.ajax({
      url:"{{ url('post/sakurentsu/type') }}",
      method:"POST",
      data:formData,
      dataType:'JSON',
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
       openSuccessGritter('Success', 'Sakurentsu Successfully Determined');
       $("#select_form").val("").trigger("change");
       $("#select_dept_form").val("").trigger("change");
       window.setTimeout( window.location.replace('{{ url("index/sakurentsu/list_sakurentsu") }}'), 3000 );
       $("#loading").hide();
     },
     error: function (response) {
      openErrorGritter('Error', result.message);
      $("#loading").hide();
    },
  });


    // $.post('{{ url("post/sakurentsu/type") }}', data, function(result, status, xhr){
    //   if (result.status) {
    //     $("#loading").hide();
    //     openSuccessGritter('Success', 'Sakurentsu Successfully Determined');
    //     $("#select_form").val("").trigger("change");
    //     $("#select_dept_form").val("").trigger("change");
    //     window.setTimeout( window.location.replace('{{ url("index/sakurentsu/list_sakurentsu") }}'), 3000 );
    //   } else {
    //     $("#loading").hide();
    //     openErrorGritter('Error', result.message);
    //   }
    // })
  }
  
}

$( "#select_form" ).change(function() {
  var depts = <?php echo json_encode($depts); ?>;

  var dpt = "";
  $("#select_dept_form").empty();

  if ($(this).val() == "3M") {
    $("#three_m_cat").show();
  } else {
    $("#three_m_cat").hide();
    $("#lampiran").hide();
  }

  var cat = $(this).val();

  if(cat == "Not Related") {
    $("#pic_dept").hide();
    $("#send_item_trial").hide();
    $("#lampiran").show();
  } else if(cat == 'Information') {
    $("#pic_dept").show();
    $("#send_item_trial").hide();
    $("#lampiran").show();
  } else {
    $("#pic_dept").show();
    $("#send_item_trial").hide();
  }
  
  $.each(depts, function(key, value) {
    if (cat == "3M") {
      if (jQuery.inArray(value.department_shortname, ['PC','PCH','PE']) !== -1) {
        dpt += "<option value='"+value.department_name+"'>"+value.department_name+"</option>";
      }
    } else if(cat == "Trial") {
      if (jQuery.inArray(value.department_shortname, ['PC']) !== -1) {
        dpt += "<option value='"+value.department_name+"'>"+value.department_name+"</option>";
      }
    } else {
      dpt += "<option value='"+value.department_name+"'>"+value.department_name+"</option>";
    }
  })

  $("#select_dept_form").append(dpt);
});

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

function openSuccessGritter(title, message){
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-success',
    image: '{{ url("images/image-screen.png") }}',
    sticky: false,
    time: '2000'
  });
}

</script>

@stop