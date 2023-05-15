@extends('layouts.display')
@section('stylesheets')
<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
  border:1px solid rgb(211,211,211);
  padding-top: 0px;
  padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }

.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
  top: 9px;
  left: 9px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
}

.container_checkmark {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 13px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #999999;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}

#tableEdit > thead > tr > th{
  padding: 2px;
}

#tableEdit > tbody > tr > td,{
  padding: 2px;
  border: 1px solid black;
}

input[type=number] {
    -moz-appearance:textfield; /* Firefox */
  }

</style>
@stop
@section('header')
<section class="content-header" >
  <h1>
    Review Certificate<span class="text-purple"> </span>
  </h1>
</section>
@stop
@section('content')
<section class="content" >
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
      <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
  </div>
  <input type="hidden" id="check_time">
  <!-- <div class="row"> -->
    <div class="box box-solid">
      <div class="box-body">
        <div class="col-xs-12" style="padding-bottom: 20px">
          <h1 style="font-weight: bold;font-size: 25px">
            Preview QA Kensa Certificate <small><span class="text-purple">品質保証検査認定プレビュー</span></small>
            <?php if ($approval_now->approver_status == null && $approval_now->remark == 'Leader QA'){ ?>
              <a href="{{url('index/qa/certificate/code')}}" class="btn btn-primary pull-right" style="font-weight: bold;margin-left: 10px"><i class="fa fa-bar-chart"></i> Monitoring</a>
              <button class="btn btn-warning pull-right" style="font-weight: bold" onclick="openModalEdit()"><i class="fa fa-edit"></i> Edit</button>
            <?php }else if($approval_now->approver_status != null && $approval_now->remark == 'Leader QA'){ ?>
              
            <?php }else{ ?>
              <!-- <a class="btn btn-danger pull-right" style="font-weight: bold" href="{{url('approval/qa/certificate/'.$approval_now->remark)}}"><i class="fa fa-arrow-left"></i> Back 戻る</a> -->
              <!-- <button class="btn btn-danger pull-right" style="font-weight: bold" onclick="window.close();"><i class="fa fa-arrow-left"></i> Back 戻る</button> -->
            <?php } ?>
          </h1>
        </div>
        <div class="col-xs-12">
          <table class="table table-bordered">
            <tr id="show-att">
              <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;" colspan="2" id="attach_pdf">
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  <!-- </div> -->

  <div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-lg" style="width: 1100px">
      <div class="modal-content">
        <div class="modal-header no-padding">
          <h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 1%; padding-bottom: 1%;" class="modal-title">
            EDIT CERTIFICATE
          </h4>
        </div>
        <div class="modal-body table-responsive">
          <div class="col-xs-12" style="overflow-x: scroll;">
            <center>
              <span style="color: red;font-weight: bold">
                PERHATIAN !!!
              </span><br>
              <span style="color: red;">
                Pastikan perhitungan <b style="background-color: yellow">Nilai Total</b>, <b style="background-color: yellow">Status Kelulusan</b>, dan <b style="background-color: yellow">Note</b> benar saat mengubah data.
              </span>
            </center>
            <table id="tableEdit" class="table table-responsive" style="margin-top: 10px">
              <thead>
                <tr>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">#</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:4%;padding:3px">Subject</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:5%;padding:3px">Jenis Tes</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Kategori</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Standard</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Jumlah Soal</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Bobot Nilai</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Jumlah Soal Benar</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Presentase Nilai A</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Presentase Nilai Total</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Kelulusan</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:3%;padding:3px">Note</th>
                </tr>
              </thead>
              <tbody id="bodyEdit">
                
              </tbody>
            </table>
          </div>

          <div class="col-xs-12" style="padding-top: 20px">
            <div class="modal-footer">
              <div class="row">
                <button onclick="confirmEdit()" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
                  CONFIRM
                </button>
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
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };

    var count_point = 0;
    var data_subject = [];

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
  $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
  $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
  $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
  $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
  $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

  jQuery(document).ready(function() {
    // $('.select2').select2({
    //   allowClear:true,
    //   ropdownParent: $('#modalCode')
    // });


    $('.numpad').numpad({
      hidePlusMinusButton : true,
      decimalSeparator : '.'
    });

      $('body').toggleClass("sidebar-collapse");
      var path = "{{$file}}";
      $('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
  });

  function approve(certificate_id,remark) {
    if (confirm('Apakah Anda yakin?')) {
      $('#loading').show();
      var data = {
        certificate_id:certificate_id,
        remark:remark
      }

      $.get('{{ url("approve/qa/certificate") }}', data, function(result, status, xhr){
        if(result.status){
          $('#loading').hide();
          openSuccessGritter('Success!',result.message);
          location.reload();
        }else{
          $('#loading').hide();
          openErrorGritter('Error!',result.message);
          return false;
        }
      })
    }
  }
  var total_question;

  function openModalEdit() {
    var data = {
      certificate_id:'{{$certificate_id}}'
    }
    $.get('{{ url("edit/qa/certificate") }}', data, function(result, status, xhr){
        if(result.status){
          $('#bodyEdit').html('');
          var editData = '';
          var index = 0;
          total_question = 0;

          for(var i = 0; i < result.certificate.length;i++){
            editData += '<tr>';
            editData += '<td style="border:1px solid black"><input type="hidden" id="id_'+index+'" value="'+result.certificate[i].id+'">'+(index+1)+'</td>';
            editData += '<td style="border:1px solid black">'+result.certificate[i].subject+'</td>';
            editData += '<td style="border:1px solid black">'+(result.certificate[i].test_type || '')+'</td>';
            editData += '<td style="border:1px solid black">'+result.certificate[i].category+'</td>';
            editData += '<td style="border:1px solid black">'+result.certificate[i].standard+' %</td>';
            if (result.certificate[i].category == 'Kesesuaian proses kerja berdasarkan IK') {
              editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control numpad" readonly id="question_'+index+'" value="'+(result.certificate[i].question || '')+'"></td>';
              editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control numpad" id="weight_'+index+'" value="'+(result.certificate[i].weight || '')+'"></td>';
            }else{
              editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control numpad" id="question_'+index+'" value="'+(result.certificate[i].question || '')+'"></td>';
              editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control" readonly id="weight_'+index+'" value="'+(result.certificate[i].weight || '')+'"></td>';
            }
            editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control numpad" id="question_result_'+index+'" value="'+(result.certificate[i].question_result || '')+'"></td>';
            editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control numpad" id="presentase_result_'+index+'" value="'+(result.certificate[i].presentase_result || '')+'"></td>';
            editData += '<td style="border:1px solid black"><input type="text" style="text-align:right" class="form-control numpad" id="presentase_a_'+index+'" value="'+(result.certificate[i].presentase_a || '')+'"></td>';
            editData += '<td style="border:1px solid black"><select class="form-control" style="width:100%" id="result_grade_'+index+'">';
            if (result.certificate[i].note == '-') {
              editData += '<option value="LULUS" selected="selected">LULUS</option>';
              editData += '<option value=""></option>';
            }else if(result.certificate[i].note == 'Tidak Sertifikasi'){
              editData += '<option value="LULUS">LULUS</option>';
              editData += '<option value="" selected="selected"></option>';
            }
            editData += '</select></td>';
            editData += '<td style="border:1px solid black"><select class="form-control" style="width:100%" id="note_'+index+'">';
            if (result.certificate[i].note == '-') {
              editData += '<option value="-" selected="selected">-</option>';
              editData += '<option value="Tidak Sertifikasi">Tidak Sertifikasi</option>';
            }else if(result.certificate[i].note == 'Tidak Sertifikasi'){
              editData += '<option value="-">-</option>';
              editData += '<option value="Tidak Sertifikasi" selected="selected">Tidak Sertifikasi</option>';
            }
            editData += '</tr>';
            index++;
          }
          total_question = result.certificate.length;
          $('#bodyEdit').append(editData);

          $('.numpad').numpad({
            hidePlusMinusButton : true,
            decimalSeparator : '.'
          });

          $('#modalEdit').modal('show');
        }else{
          $('#loading').hide();
          openErrorGritter('Error!',result.message);
          return false;
        }
      })
  }

  function confirmEdit() {
    if (confirm('Apakah Anda yakin akan mengubah data?')) {
      $('#loading').show();
      var question = [];
      var weight = [];
      var question_result = [];
      var result_grade = [];
      var note = [];
      var presentase_result = [];
      var presentase_a = [];
      var id = [];
      for(var i = 0; i < total_question;i++){
        id.push($('#id_'+i).val());
        question.push($('#question_'+i).val());
        weight.push($('#weight_'+i).val());
        question_result.push($('#question_result_'+i).val());
        presentase_result.push($('#presentase_result_'+i).val());
        presentase_a.push($('#presentase_a_'+i).val());
        result_grade.push($('#result_grade_'+i).val());
        note.push($('#note_'+i).val());
      }
      var data = {
        id:id,
        question:question,
        weight:weight,
        question_result:question_result,
        presentase_result:presentase_result,
        presentase_a:presentase_a,
        result_grade:result_grade,
        note:note,
        certificate_id:'{{$certificate_id}}',
      }

      $.post('{{ url("update/qa/certificate") }}', data, function(result, status, xhr){
          if(result.status){
            $('#loading').hide();
            openSuccessGritter('Success!','Success Update Data');
            location.reload();
          }else{
            $('#loading').hide();
            openErrorGritter('Error!',result.message);
            return false;
          }
      })
    }
  }
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
@endsection
