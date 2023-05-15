@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
    #detailTable > tbody > tr:hover{
        /*cursor: pointer;*/
        background-color: #7dfa8c !important;
    }
    #resumeTable > tbody > tr > td:hover{
        cursor: pointer;
        background-color: #7dfa8c !important;
    }
    #resumeCategoryTable > tbody > tr:hover{
        cursor: pointer;
        background-color: #7dfa8c !important;
    }
    table.table-bordered{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid black;
        vertical-align: middle;
        padding:  2px 5px 2px 5px;
    }
    #detailTable > tbody > tr > td{
        height: 40px;
    }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance:textfield;
    }

    .nmpd-grid {
        border: none; padding: 20px;
    }
    .nmpd-grid>tbody>tr>td {
        border: none;
    }
    #loading { display: none; }
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
    .radio input {
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
        background-color: #ccc;
        border-radius: 50%;
    }

    /* On mouse-over, add a grey background color */
    .radio:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .radio input:checked ~ .checkmark {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .discussion > span > p > img {
        width: 300px;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio .checkmark:after {
        top: 9px;
        left: 9px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
    }


    .radio_box {
        /*display: inline-block;*/
        position: relative;
        padding-left: 35px;
        padding-top: 4px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 14px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid rgba(0,0,0,0.1);
        /*height: 30px;*/
        background-color: #f2f2f2;
        min-height: 50px;
        width: 100% !important;
    }

    /* Hide the browser's default radio button */
    .radio_box input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark_box {
        position: absolute;
        top: 3px;
        left: 8px;
        height: 20px;
        width: 20px;
        background-color: #ccc;
    }


    /* On mouse-over, add a grey background color */
    .radio_box:hover input ~ .checkmark_box {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .radio_box input:checked ~ .checkmark_box {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark_box:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio_box input:checked ~ .checkmark_box:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio_box .checkmark_box:after {
        top: 6px;
        left: 6px;
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
        font-size: 16px;
        font-weight: bold;
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
        background-color: #eee;
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

    html {background-color: #fefefe;} 
</style>
@endsection

<!-- @section('header')
<section class="content-header">

</section>
@endsection
-->
@section('content')
<section class="content" style="font-size: 0.9vw">
   <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 20px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
</div>
<div class="container" style="background-color: #f2f2fe !important;" >
    <div class="row">
        <div class="col-lg-12" style="margin-top:5px;">
            <h2  style="background-color:#955ddf;color:white;border-top-left-radius: 5px;border-top-right-radius: 5px;font-size: 25px; text-align:center; border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;">TRAINING FILOSOFI YAMAHA</h2>
            <input type="hidden" name="employee_id" id="employee_id" value="{{$empsync[0]->employee_id}}">
            <input type="hidden" name="name" id="name" value="{{$empsync[0]->name}}">

            <div class="contact-form" style="background-color: white;padding: 15px;border-radius: 10px;margin-top: 20px;margin-bottom: 20px;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                {{ csrf_field() }}
                <input type="hidden" name="periode" id="periode" value="">
                <center id="title_pertanyaan" style="display: none;">
                    <span class="center-heading" style="padding-bottom: 15px;text-align: center;font-weight: bold;font-size: 18px">
                        PERTANYAAN
                    </span>
                </center>

                <div class="row">
                    <?php $tr_filos_question_total = count($tr_filos_question) ?>
                    <?php if (count($tr_filos_question) > 0): ?>
                        <?php $no = 0; ?>

                        @foreach($tr_filos_question as $tr_filos_question)

                        <div id="div_tr_question_<?= $no ?>" style="display: none;width: 100%;padding: 15px;" class="col-xs-12">
                           <?php if ($no > 2): ?>
                            <span style="font-weight: bold;font-size: 14px">Pilihlah jawaban “BENAR” atau “SALAH” pada setiap pertanyaan berikut</span><br>
                        <?php endif ?>
                        <label class="label-input1002" style="color: purple;margin-top: 0px;font-size: 14px"><span id="TrFilos_question_<?= $no ?>">{{$no+1}}. {{ $tr_filos_question->question }}</span></label>


                        <?php $tr_filos_answer = explode('_', $tr_filos_question->answer) ?>

                        <?php for ($i=0; $i < count($tr_filos_answer); $i++) { ?>
                            <div class="validate-input" style="position: relative; width: 100%">
                                <label class="radio_box" style="margin-top: 5px;font-size: 12px">{{$tr_filos_answer[$i]}}
                                    <input type="radio" id="TrFilos_answer_{{$no}}" name="TrFilos_answer_{{$no}}" value="{{$tr_filos_answer[$i]}}">
                                    <span class="checkmark_box"></span>
                                </label>
                            </div>
                        <?php } ?>
                        <input type="hidden" name="Tr_Filos_right_answer_{{$no}}" id="Tr_Filos_right_answer_{{$no}}" value="{{$tr_filos_question->right_answer}}">

                        <br>
                    </div>
                    <div id="div_tr_discussion_<?= $no ?>" style="display: none;width: 100%;padding: 15px;" class="col-xs-12 discussion">
                        <center><span id="tr_status_<?= $no ?>" style="font-weight: bold;font-size: 20px"></span></center>
                        <br>

                        <center><span id="images_<?= $no ?>" style="display: none;"><img src='../public/images/kode_etik/yess_answer.png' width='18%'></span>
                        </center>
                        <br>
                        <center><span id="images_not_<?= $no ?>" style="display: none;"><img src='../public/images/kode_etik/sad.png' width='18%'></span>
                        </center>
                        <br>
                        <?php if ($no < 3): ?>
                           <center><span style="font-weight: bold;font-size: 20px">Jawaban yang benar adalah</span><br>
                            <span><?php echo $tr_filos_question->right_answer ?></span>
                        </center>
                    <?php endif ?>

                    <?php if ($no == 3): ?>
                        <center><span style="font-weight: bold;font-size: 20px">Jawaban yang benar adalah</span><br>
                            <span><?php echo $tr_filos_question->right_answer ?></span><br>
                            <span id="st_answer_4">3 divisi yang kuat yaitu: Teknik, Produksi, Penjualan</span>
                        </center>
                    <?php endif ?>
                    <?php if ($no == 4): ?>
                        <center><span style="font-weight: bold;font-size: 20px">Jawaban yang benar adalah</span><br>
                            <span><?php echo $tr_filos_question->right_answer ?></span><br>
                            <span id="st_answer_5">You Have the Power to Change Yamaha</span>
                        </center>
                    <?php endif ?>


                </div>


                <button class="btn btn-success" type="button" id="btn_tr_submit_<?= $no ?>" onclick="submitTrFilosQuestion('{{$no}}')" style="display: inline-block;float: right;display: none;margin: 20px">
                    <span>
                        Submit
                        <i class="fa fa-arrow-right"></i>
                    </span>
                </button>
                <button class="btn btn-info" type="button" id="btn_tr_back_<?= $no ?>" onclick="backTrFilosQuestion('{{$no}}')" style="display: inline-block; float: left;display: none; background-color: red;margin: 20px">
                    <span>
                        <i class="fa fa-arrow-left"></i>
                        Back
                    </span>
                </button>
                <button class="btn btn-info" type="button" id="btn_tr_filos_next_<?= $no ?>" onclick="nextTrFilosQuestion('{{$no}}')" style="display: inline-block;float: right;display: none;margin: 20px">
                    <span>
                        Next
                        <i class="fa fa-arrow-right"></i>
                    </span>
                </button>
                <?php $no++ ?>
                @endforeach
            <?php endif ?>

            <div id="title_sambutan" style="width: 100%; display:none;">

                <div class="col-xs-12 col-md-12">
                   <a class="pull-left" style="margin-left: 15px; font-size: 15px; border-color: black;" href="{{ url("index/employee/service") }}"> <i class="fa fa-arrow-circle-left"></i> Back </a>
                   <br>
                   <center><span id="images_nova_<?= $no ?>"><img src='../public/images/filosofi_n.png' style="width: 40vh;"></span>
                   </center>
                   <br>

                   <center style="font-size:16px">Terima kasih sudah mengikuti Training Filosofi Yamaha yang dilakukan oleh HR Dept.<br>Sebagai feed back atas training tersebut, mohon untuk mengisi daftar pertanyaan berikut : </center>
                   <br>
                   <button class="btn btn-primary" type="button" id="btn_tr_next_awal" onclick="nextTrFilosAwal()" style="display: inline-block;float: right;display: none;margin: 20px">
                    <span>
                        Next
                        <i class="fa fa-arrow-right"></i>
                    </span>
                </button>
            </div>
        </div>
        <div id="status_open" style="width: 100%; display:none;">


            <a class="pull-left" style="margin-left: 20px; font-size: 15px; border-color: black;" href="{{ url("index/employee/service") }}"> <i class="fa fa-arrow-circle-left"></i> Back </a>
            <br> <br>
            <br>
            <div class="col-xs-12 col-md-12">
                <center><span id="images_close"><img src='../public/images/maintenances.png' style="width: 30vh;"></span>
                </center>

                <center><span id="images_open"><img src='../public/images/kode_etik/training.png' style="width: 30vh;"></span>
                </center>
                <br>
                <center id="pernyataan_status" style="font-size:16px">Hai <?= Auth::user()->name ?><br>Training Filosofi Yamaha Masih Belum Dibuka</center>

                <center id="pernyataan_status_open" style="font-size:16px;display: none;">Hai <?= Auth::user()->name ?><br>Training Filosofi Yamaha Sudah Dibuka</center>


                <?php if ($empsync[0]->employee_id == "PI2101043" || $empsync[0]->employee_id == "PI1110002"): ?>
                    <center>


                     <button class="btn btn-info" type="button" id="btn_open_back" onclick="TrFilosOpen('close')" style="display: inline-block;display: none;margin: 20px;">
                        <span>
                            <i class="fa fa-arrow-circle-left"></i>
                            Close
                        </span>
                    </button>

                    <button class="btn btn-primary" type="button" id="btn_open" onclick="TrFilosOpen('open')" style="display: inline-block;display: none;margin: 20px">
                        <span>
                            Open
                            <i class="fa fa-arrow-right"></i>
                        </span>
                    </button>

                    <button class="btn btn-success" type="button" id="btn_open_next" onclick="TrFilosNext()" style="display: inline-block;display: none;margin: 20px">
                        <span>
                            Next
                            <i class="fa fa-arrow-right"></i>
                        </span>
                    </button>


                </center>

            <?php endif ?>


        </div>
    </div>
</div>
<div id="div_detail_belum" style="margin-top: 20px;display:none">
    <center><span style="font-size: 18px;text-align: center;font-weight: bold;">
        <?php date_default_timezone_set('Asia/Jakarta'); ?>
        Maaf, fitur ini masih dalam pengembangan.
    </span></center>
</div>

<div id="div_detail_sudah" style="display: none;">
    <?php if ($empsync[0]->employee_id == "PI2101043" || $empsync[0]->employee_id == "PI1110002"){ ?>
        <a class="pull-left" style="font-size: 15px; border-color: black;" onclick="TrFilosBacks()"> <i class="fa fa-arrow-circle-left"></i> Back </a>
        <br> <br>
    <?php }else{ ?>
        <a class="pull-left" style="font-size: 15px; border-color: black;" href="{{ url("index/employee/service") }}"> <i class="fa fa-arrow-circle-left"></i> Back </a>
        <br> <br>
    <?php } ?>

    <center>
        <div><img src='../public/images/kode_etik/Thanks_veno.png' width='35%'></div> 
    </center>
    <br>
    <center><span style="font-size: 18px;text-align: center;color: black;">
        <?php date_default_timezone_set('Asia/Jakarta'); ?>
        Terimakasih <span style="color: blue;">{{$empsync[0]->name}}</span><span style="color: black;"> telah melakukan Training Filosofi Yamaha</span><br> pada<br>
        <?php if (ISSET($check_emp_tr) && $check_emp_tr != null){ ?>
            <span id="tanggal_tr_filos" style="font-weight: bold;color: red;">{{$check_emp_tr->created_at}}</span> 
        <?php }else{ ?>
            <span id="tanggal_tr_filos" style="font-weight: bold;color: red;"></span>
        <?php } ?>
    </span></center>
</div>



</div>
</div>

</div>
</div>





</section>


@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        if (parseInt('{{count($tr_status)}}') > 0) {

         if ('{{$empsync[0]->employee_id}}' == "PI2101043" || '{{$empsync[0]->employee_id}}' == "PI1110002") {
            $('#title_pertanyaan').hide();
            $('#div_detail_belum').hide();
            $('#status_open').show();
            $('#btn_open').hide();
            $('#btn_open_back').show();
            $('#btn_open_next').show();
            $('#pernyataan_status').hide();
            $('#pernyataan_status_open').show();
            $('#images_close').hide();
            $('#images_open').show();

        }else{
            for(var i = 0; i < parseInt('{{$tr_filos_question_total}}');i++){
                var name= 'TrFilos_answer_'+i;
                $('#'+name).prop('checked', false);
            }
            $('#div_tr_question_0').hide();
            $('#btn_tr_next_awal').show();
            $('#status_open').hide();
            $('#btn_open_next').hide();
            $('#title_sambutan').show();
            $('#title_pertanyaan').hide();
            $('#surat_pernyataan').hide();
            $('#div_detail_belum').hide();
            $('#sudah_kode_etik').hide();
            $('#nama').html('{{$empsync[0]->name}}');
            $('#nama_bawah').html('{{$empsync[0]->name}}');
            $('#nik').html('{{$empsync[0]->employee_id}}');
            $('#grade').html('{{$empsync[0]->grade_code}}');
            $('#jabatan').html('{{$empsync[0]->position}}');
            $('#images_close').hide();
            $('#images_open').hide();

            if (parseInt('{{count($check_emp_tr)}}') > 0) {
                $('#status_open').hide();
                $('#title_sambutan').hide();
                $('#div_detail_sudah').show();
                $('#surat_pernyataan').hide();
                $('#div_tr_question_0').hide();
                $('#btn_tr_submit_0').hide();
                $('#title_pertanyaan').hide();

            }
        }

    }else{



        if ('{{$empsync[0]->employee_id}}' == "PI2101043" || '{{$empsync[0]->employee_id}}' == "PI1110002") {
            $('#title_pertanyaan').hide();
            $('#div_detail_belum').hide();
            $('#status_open').show();
            $('#btn_open').show();
            $('#btn_open_back').hide();
            $('#btn_open_next').hide();
            $('#images_close').show();
            $('#images_open').hide();

        }else{
            if (parseInt('{{count($check_emp_tr)}}') > 0) {
                $('#status_open').hide();
                $('#title_sambutan').hide();
                $('#div_detail_sudah').show();
                $('#surat_pernyataan').hide();
                $('#div_tr_question_0').hide();
                $('#btn_tr_submit_0').hide();
                $('#title_pertanyaan').hide();

            }else{
               $('#title_pertanyaan').hide();
               $('#div_detail_belum').hide();
               $('#status_open').show();
               $('#btn_open').show();
               $('#btn_open_back').hide();
               $('#btn_open_next').hide();
               $('#images_close').show();
               $('#images_open').hide();
           }

       }





   }

});

    function TrFilosBacks() {

        $('#div_detail_sudah').hide();
        $('#title_pertanyaan').hide();
        $('#div_detail_belum').hide();
        $('#status_open').show();
        $('#btn_open').hide();
        $('#btn_open_back').show();
        $('#btn_open_next').show();
        $('#pernyataan_status').hide();
        $('#pernyataan_status_open').show();
        $('#images_close').hide();
        $('#images_open').show();


    }

    function TrFilosNext() {
       $('#div_tr_question_0').hide();
       $('#btn_tr_next_awal').show();
       $('#status_open').hide();
       $('#btn_open_next').hide();
       $('#title_sambutan').show();
       $('#title_pertanyaan').hide();
       $('#surat_pernyataan').hide();
       $('#div_detail_belum').hide();
       $('#sudah_kode_etik').hide();
       $('#nama').html('{{$empsync[0]->name}}');
       $('#nama_bawah').html('{{$empsync[0]->name}}');
       $('#nik').html('{{$empsync[0]->employee_id}}');
       $('#grade').html('{{$empsync[0]->grade_code}}');
       $('#jabatan').html('{{$empsync[0]->position}}');

       if (parseInt('{{count($check_emp_tr)}}') > 0) {
        $('#status_open').hide();
        $('#title_sambutan').hide();
        $('#div_detail_sudah').show();
        $('#surat_pernyataan').hide();
        $('#div_tr_question_0').hide();
        $('#btn_tr_submit_0').hide();
        $('#title_pertanyaan').hide();
    }

}

function submitTrFilosQuestion(no) {

    var answer = '';
    $("input[name='TrFilos_answer_"+no+"']:checked").each(function (i) {
        answer = $(this).val();
    });
    if (answer != $("#Tr_Filos_right_answer_"+no).val()) {
        $('#tr_status_'+no).html('Jawaban Anda Salah!<br>Silahkan baca jawaban benar.');
        $('#tr_status_'+no).css("color", "red");
        $('#tr_status_'+no).css("fontWeight", "bold");
        $('#div_tr_discussion_'+no).show();
        $('#btn_tr_back_'+no).show();
        $('#images_'+no).hide();
        $('#images_not_'+no).show();
        $('#btn_tr_submit_'+no).hide();
        $('#div_tr_question_'+no).hide();
        $('#st_answer_4').show();
        $('#st_answer_5').show();

    }else{
        $('#tr_status_'+no).html('Jawaban Anda Benar!');
        $('#tr_status_'+no).css("color", "green");
        $('#tr_status_'+no).css("fontWeight", "bold");
        $('#div_tr_discussion_'+no).show();
        $('#images_not_'+no).hide();
        $('#images_'+no).show();
        $('#btn_tr_filos_next_'+no).show();
        $('#btn_tr_submit_'+no).hide();
        $('#div_tr_question_'+no).hide();
        $('#st_answer_4').hide();
        $('#st_answer_5').hide();
    }
}

function backTrFilosQuestion(no) {
    $('#tr_status_'+no).html('');
    $('#tr_status_'+no).css("color", "white");
    $('#btn_tr_back_'+no).hide();
    $('#div_tr_discussion_'+no).hide();
    $('#btn_tr_submit_'+no).show();
    $('#div_tr_question_'+no).show();
}


function nextTrFilosAwal() {
    $('#btn_tr_next_awal').hide();
    $('#title_sambutan').hide();
    $('#div_tr_question_0').show();
    $('#btn_tr_submit_0').show();
}

function nextTrFilosQuestion(no) {
    $('#tr_status_'+no).html('');
    $('#tr_status_'+no).css("color", "white");
    $('#btn_tr_back_'+no).hide();
    $('#btn_tr_filos_next_'+no).hide();
    $('#div_tr_discussion_'+no).hide();
    $('#btn_tr_submit_'+no).hide();
    $('#div_tr_question_'+no).hide();

    $('#btn_tr_submit_'+(parseInt(no)+1)).show();
    $('#div_tr_question_'+(parseInt(no)+1)).show();

    if ((parseInt(no)+1) == '{{$tr_filos_question_total}}') {

        $('#div_pertanyaan').hide();
        $('#surat_pernyataan').hide();
        $('#title_pertanyaan').hide();

        var question = [];
        var answers = [];

        for(var i = 0; i < parseInt('{{$tr_filos_question_total}}');i++){
            var answer = '';
            $("input[name='TrFilos_answer_"+i+"']:checked").each(function (i) {
                answer = $(this).val();
            });
            answers.push(answer);
            question.push($('#TrFilos_question_'+i).html());
        }

        var data = {
            employee_id : $('#employee_id').val(),
            question:question,
            answer:answers
        }

        $.get('{{ url("training_filosofi/input") }}', data, function(result, status, xhr){
            if(result.status == true){    
                $("#loading").hide();
                openSuccessGritter("Success","Berhasil Disimpan");
                $("#tanggal_tr_filos").html(result.datetime);
                $('#div_detail_sudah').show();
            }
            else {
                $("#loading").hide();
                openErrorGritter('Error!', result.datas);
            }
        })

    }
}

function TrFilosOpen(st) {    
    var data = {
        status:st
    }

    $.get('{{ url("update/status") }}', data, function(result, status, xhr){
        if(result.status == true){    
            openSuccessGritter("Success","Berhasil Diupdate");
            location.reload();
        }
        else {
            $("#loading").hide();
            openErrorGritter('Error!', "Error");
        }
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

</script>

@endsection
