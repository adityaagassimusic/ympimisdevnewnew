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
            Preview QA Kensa Certificate In Process <small><span class="text-purple">品質保証検査認定プレビュー</span></small>
            <?php if ($approval_now->approver_status == null && $approval_now->remark == 'Leader QA'){ ?>
              <a href="{{url('index/qa/certificate/code/inprocess')}}" class="btn btn-primary pull-right" style="font-weight: bold;margin-left: 10px"><i class="fa fa-bar-chart"></i> Monitoring</a>
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
                Pastikan perhitungan <b style="background-color: yellow">Nilai Total</b> dan <b style="background-color: yellow">Status Kelulusan</b> benar saat mengubah data.
              </span>
            </center>
            <table id="tableEditIk" class="table table-responsive" style="margin-top: 10px">
              <thead>
                <tr>
                  <th colspan="2" style="text-align: left;"> Standart kelulusan : Penguasaan 100% point IK </th>
                  <th colspan="2" style="text-align: right;"><span id="error_ik" style="color: red"></span></th>
                </tr>
                <tr>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">Total Point IK</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">OK</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">NG</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">% Nilai</th>
                </tr>
              </thead>
              <tbody id="bodyEditIk">
                <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="number" id="total_ik" class="td_isi numpad" style="width: 100%;text-align: center;"></td>
                <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="number" id="ok_ik" onchange="checkIk()" class="td_isi numpad" style="width: 100%;text-align: center;"></td>
                <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="number" id="ng_ik" readonly="" style="width: 100%;text-align: center;"></td>
                <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;text-align: center;"><input type="text" id="presentase_ik" readonly="" style="width: 100%;text-align: center;;background-color: #ffffc2"></td>
              </tbody>
            </table>

            <table id="tableEditKomposisi" class="table table-responsive" style="margin-top: 10px">
              <thead>
                <tr>
                  <th colspan="6" style="border-bottom: 2px solid rgb(60, 60, 60);text-align: left;">Nilai Aktual</th>
                </tr>
                <tr>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: right;">Grade Soal</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">A</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">B</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">C</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">D</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;" rowspan="2">Total Kesalahan</th>
                </tr>
                <tr>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: right;">Skor tiap Grade</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">4</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">3</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">2</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px">1</th>
                </tr>
              </thead>
              <tbody id="bodyEditKomposisi">
                <?php $index = 1; ?>
                @foreach($composition as $com)
                <tr>
                  <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">{{$com->composition}}</td>
                  <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_a">{{$com->com_a}}</td>
                  <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_b">{{$com->com_b}}</td>
                  <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_c">{{$com->com_c}}</td>
                  <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_d">{{$com->com_d}}</td>
                  <td style="background-color:#fff;color:#000;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px" id="com_{{$index}}_fault">{{$com->total_fault}}</td>
                </tr>
                <?php $index++ ?>
                @endforeach
              </tbody>
            </table>

            <table id="tableEditInprocess" class="table table-responsive" style="margin-top: 10px">
              <thead>
                <tr>
                  <th colspan="6" style="border-bottom: 2px solid rgb(60, 60, 60);text-align: left;">Nilai Aktual</th>
                </tr>
                <tr>
                  <th colspan="6" style="border-bottom: 2px solid rgb(60, 60, 60)"><span style="color: red;" id="error_standard"></span></th>
                </tr>
                <tr>
                  <th rowspan="2" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;width: 2%">Keterangan</th>
                  <th colspan="4" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;width: 5%">Grade Soal</th>
                  <th rowspan="2" style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;vertical-align: middle;">Total</th>
                </tr>
                <tr>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">A</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">B</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">C</th>
                  <th style="background-color:rgb(126,86,134);color:#FFD700;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">D</th>
                </tr>
              </thead>
              <tbody id="bodyEditInprocess">
               <tr>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">1. Skor</td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_1">4</td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_2">3</td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_3">2</td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="bobot_4">1</td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;">-</td>
                </tr>
                <tr>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">2. Jumlah Soal</th>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_1" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value="2"></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_2" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=1></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_3" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=3></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="question_4" class="td_isi numpad" onchange="totalQuestion()" style="width: 100%;text-align: center;" value=5></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="total_question"></td>
                </tr>
                <tr>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;">3. Jumlah Soal Benar</td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_1" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_2" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_3" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:0px;cursor: pointer;"><input type="number" id="answer_4" class="td_isi numpad" onchange="totalAnswer()" style="width: 100%;text-align: center;" value=""></td>
                  <td style="background-color:white;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;" id="total_answer"></td>
                </tr>
                <tr>
                  <td style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;font-weight: bold;">4. Presentase Nilai Grade A</td>
                  <td colspan="5" style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;font-weight: bold;" id="presentase_a"></td>
                </tr>
                <tr>
                  <td style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: left;font-weight: bold;">5. Presentase Nilai Total</td>
                  <td colspan="5" style="background-color:#ffffc2;color:black;font-size:15px;border:2px solid rgb(60, 60, 60);width:1%;padding:3px;text-align: center;font-weight: bold;" id="presentase_total"></td>
                </tr>
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

  function checkIk() {
    var total_ik = $('#total_ik').val();
    var ok_ik = $('#ok_ik').val();

    if (ok_ik != total_ik) {
      audio_error.play();
      openErrorGritter('Error!','Jumlah OK HARUS SAMA DENGAN Total Point IK');
      $('#ok_ik').val('');
      return false;
    }

    $('#error_ik').html('');

    var presentase_ik = ((parseInt(ok_ik)/parseInt(total_ik))*100).toFixed(2);

    $('#ng_ik').val(parseInt(total_ik)-parseInt(ok_ik));
    if (presentase_ik < 100) {
      $('#error_ik').html('PESERTA TIDAK LULUS POINT IK');
    }
    $('#presentase_ik').val(presentase_ik+' %');
  }

  function totalQuestion() {
    var total = 0;
    total = total + (parseInt($('#question_1').val() || 0)*parseInt($('#bobot_1').text())) + (parseInt($('#question_2').val() || 0)*parseInt($('#bobot_2').text())) + (parseInt($('#question_3').val() || 0)*parseInt($('#bobot_3').text())) + (parseInt($('#question_4').val() || 0)*parseInt($('#bobot_4').text()));
    $('#total_question').html(total);
  }

  function totalAnswer() {
    var total = 0;
    total = total + (parseInt($('#answer_1').val() || 0)*parseInt($('#bobot_1').text())) + (parseInt($('#answer_2').val() || 0)*parseInt($('#bobot_2').text())) + (parseInt($('#answer_3').val() || 0)*parseInt($('#bobot_3').text())) + (parseInt($('#answer_4').val() || 0)*parseInt($('#bobot_4').text()));
    $('#total_answer').html(total);

    $('#error_standard').html('');

    var status = 0;

    var q_1 = parseInt($('#question_1').val() || 0);
    var a_1 = parseInt($('#answer_1').val() || 0);
    var q_2 = parseInt($('#question_2').val() || 0);
    var a_2 = parseInt($('#answer_2').val() || 0);
    var q_3 = parseInt($('#question_3').val() || 0);
    var a_3 = parseInt($('#answer_3').val() || 0);
    var q_4 = parseInt($('#question_4').val() || 0);
    var a_4 = parseInt($('#answer_4').val() || 0);

    if (checkComposition1() == 0) {
      
    }else if (checkComposition2() == 0) {
      
    }else if (checkComposition3() == 0) {
      
    }else if (checkComposition4() == 0) {
      
    }else{
      $('#error_standard').html('PESERTA TIDAK LULUS NILAI STANDART');
    }

    var presentase_a = a_1 / q_1;
    var presentase_total = total / parseInt($('#total_question').text());
    var presentase_as = (presentase_a.toFixed(2))*100;
    var presentase_totals = (presentase_total.toFixed(2))*100;
    if (presentase_as < 100) {
      $('#error_standard').html('PESERTA TIDAK LULUS NILAI STANDART');
    }
    if (presentase_totals < 90) {
      $('#error_standard').html('PESERTA TIDAK LULUS NILAI STANDART');
    }
    $('#presentase_a').html(presentase_as+' %');
    $('#presentase_total').html(presentase_totals+' %');
  }

  function checkComposition1() {
    var q_1 = parseInt($('#question_1').val() || 0);
    var a_1 = parseInt($('#answer_1').val() || 0);
    var q_2 = parseInt($('#question_2').val() || 0);
    var a_2 = parseInt($('#answer_2').val() || 0);
    var q_3 = parseInt($('#question_3').val() || 0);
    var a_3 = parseInt($('#answer_3').val() || 0);
    var q_4 = parseInt($('#question_4').val() || 0);
    var a_4 = parseInt($('#answer_4').val() || 0);

    var status_1 = 0;

    if ((q_1 - a_1) > parseInt($('#com_1_a').text())) {
      status_1++;
    }
    if ((q_2 - a_2) > parseInt($('#com_1_b').text())) {
      status_1++;
    }
    if ((q_3 - a_3) > parseInt($('#com_1_c').text())) {
      status_1++;
    }
    if ((q_4 - a_4) > parseInt($('#com_1_d').text())) {
      status_1++;
    }
    return status_1;
  }

  function checkComposition2() {
    var q_1 = parseInt($('#question_1').val() || 0);
    var a_1 = parseInt($('#answer_1').val() || 0);
    var q_2 = parseInt($('#question_2').val() || 0);
    var a_2 = parseInt($('#answer_2').val() || 0);
    var q_3 = parseInt($('#question_3').val() || 0);
    var a_3 = parseInt($('#answer_3').val() || 0);
    var q_4 = parseInt($('#question_4').val() || 0);
    var a_4 = parseInt($('#answer_4').val() || 0);

    var status_1 = 0;

    if ((q_1 - a_1) > parseInt($('#com_2_a').text())) {
      status_1++;
    }
    if ((q_2 - a_2) > parseInt($('#com_2_b').text())) {
      status_1++;
    }
    if ((q_3 - a_3) > parseInt($('#com_2_c').text())) {
      status_1++;
    }
    if ((q_4 - a_4) > parseInt($('#com_2_d').text())) {
      status_1++;
    }
    return status_1;
  }

  function checkComposition3() {
    var q_1 = parseInt($('#question_1').val() || 0);
    var a_1 = parseInt($('#answer_1').val() || 0);
    var q_2 = parseInt($('#question_2').val() || 0);
    var a_2 = parseInt($('#answer_2').val() || 0);
    var q_3 = parseInt($('#question_3').val() || 0);
    var a_3 = parseInt($('#answer_3').val() || 0);
    var q_4 = parseInt($('#question_4').val() || 0);
    var a_4 = parseInt($('#answer_4').val() || 0);

    var status_1 = 0;

    if ((q_1 - a_1) > parseInt($('#com_3_a').text())) {
      status_1++;
    }
    if ((q_2 - a_2) > parseInt($('#com_3_b').text())) {
      status_1++;
    }
    if ((q_3 - a_3) > parseInt($('#com_3_c').text())) {
      status_1++;
    }
    if ((q_4 - a_4) > parseInt($('#com_3_d').text())) {
      status_1++;
    }
    return status_1;
  }

  function checkComposition4() {
    var q_1 = parseInt($('#question_1').val() || 0);
    var a_1 = parseInt($('#answer_1').val() || 0);
    var q_2 = parseInt($('#question_2').val() || 0);
    var a_2 = parseInt($('#answer_2').val() || 0);
    var q_3 = parseInt($('#question_3').val() || 0);
    var a_3 = parseInt($('#answer_3').val() || 0);
    var q_4 = parseInt($('#question_4').val() || 0);
    var a_4 = parseInt($('#answer_4').val() || 0);

    var status_1 = 0;

    if ((q_1 - a_1) > parseInt($('#com_4_a').text())) {
      status_1++;
    }
    if ((q_2 - a_2) > parseInt($('#com_4_b').text())) {
      status_1++;
    }
    if ((q_3 - a_3) > parseInt($('#com_4_c').text())) {
      status_1++;
    }
    if ((q_4 - a_4) > parseInt($('#com_4_d').text())) {
      status_1++;
    }
    return status_1;
  }

  function openModalEdit() {
    cancelAll();
    var data = {
      certificate_id:'{{$certificate_id}}'
    }
    $.get('{{ url("edit/qa/certificate/inprocess") }}', data, function(result, status, xhr){
        if(result.status){
          $('#total_ik').val(result.certificate[0].question);
          $('#ok_ik').val(result.certificate[0].answer);
          $('#ng_ik').val(result.certificate[0].total_answer);
          $('#presentase_ik').val(result.certificate[0].presentase_total);

          for(var i = 1; i < result.certificate.length;i++){
            $('#question_'+i).val(result.certificate[i].question);
            $('#answer_'+i).val(result.certificate[i].answer);
          }
          $('#total_answer').html(result.certificate[1].total_answer);
          $('#total_question').html(result.certificate[1].total_question);
          $('#presentase_a').html(result.certificate[1].presentase_a);
          $('#presentase_total').html(result.certificate[1].presentase_total);

          // $('.numpad').numpad({
          //   hidePlusMinusButton : true,
          //   decimalSeparator : '.'
          // });

          $('#modalEdit').modal('show');
        }else{
          $('#loading').hide();
          openErrorGritter('Error!',result.message);
          return false;
        }
      })
  }

  function cancelAll() {
    $('#total_ik').val('');
    $('#ok_ik').val('');
    $('#ng_ik').val('');
    $('#presentase_ik').val('');

    for(var i = 1; i <= 4;i++){
      $('#question_'+i).val('');
      $('#answer_'+i).val('');
    }
    $('#total_answer').html('');
    $('#total_question').html('');
    $('#presentase_a').html('');
    $('#presentase_total').html('');
  }

  function confirmEdit() {
    if (confirm('Apakah Anda yakin untuk menyelesaikan proses?')) {
      $('#loading').show();

      if ($('#error_ik').val() != '' || $('#error_standard').val() != '') {
        openErrorGritter('Error!','Peserta TIDAK LULUS. Pastikan nilai sudah benar.');
        $('#loading').hide();
        audio_error.play();
        return false;
      }

      var q_1 = $('#question_1').val();
      var a_1 = $('#answer_1').val();
      var q_2 = $('#question_2').val();
      var a_2 = $('#answer_2').val();
      var q_3 = $('#question_3').val();
      var a_3 = $('#answer_3').val();
      var q_4 = $('#question_4').val();
      var a_4 = $('#answer_4').val();

      var total_question = $('#total_question').text();
      var total_answer = $('#total_answer').text();

      var presentase_a = $('#presentase_a').text();
      var presentase_total = $('#presentase_total').text();

      var total_ik = $('#total_ik').val();
      var ok_ik = $('#ok_ik').val();
      var ng_ik = $('#ng_ik').val();
      var presentase_ik = $('#presentase_ik').val();

      var data = {
        q_1:q_1,
        a_1:a_1,
        q_2:q_2,
        a_2:a_2,
        q_3:q_3,
        a_3:a_3,
        q_4:q_4,
        a_4:a_4,

        total_question:total_question,
        total_answer:total_answer,

        presentase_a:presentase_a,
        presentase_total:presentase_total,

        total_ik:total_ik,
        ok_ik:ok_ik,
        ng_ik:ng_ik,
        presentase_ik:presentase_ik,
        certificate_id:'{{$certificate_id}}'
      }
      $.post('{{ url("update/qa/certificate/inprocess") }}', data, function(result, status, xhr){
        if(result.status){
          $('#loading').hide();
          openSuccessGritter('Success!',result.message);
          location.reload();
        }else{
          $('#loading').hide();
          openErrorGritter('Error!',result.message);
        }
      });
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
