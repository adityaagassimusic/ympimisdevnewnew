@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .gambar {
    width: 400px;
    height: 420px;
    background-color: white;
    border-radius: 15px;
    margin-left: 30px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }
  .content-wrapper{
    padding-top: 0px;
    margin-top: 0px
  }
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    overflow:hidden;
  }
  tbody>tr>td{
    /*text-align:center;*/
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
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
    vertical-align: middle;
    padding:0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
    border:1px solid black;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ecf0f5;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #dedeff;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <div class="row" style="padding-left: 20px;padding-right: 20px;padding-top: 0px;margin-top: 0px">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="background-color: rgb(126,86,134);text-align: center;height: 35px;padding-right: 5px">
      <span style="color: white;font-size: 25px;font-weight: bold;" id="title_periode">
      </span>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px;padding-right: 5px">
      <select class="form-control select2" data-placeholder="Pilih Fiscal Year" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="fiscal_year">
        <option value=""></option>
        @foreach($fiscal as $fiscal)
          <option value="{{$fiscal->fiscal_year}}">{{$fiscal->fiscal_year}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 0px;padding-right: 5px">
      <select class="form-control select2" data-placeholder="Pilih Tipe Audit" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="type">
        <option value=""></option>
        <option value="Production">Production</option>
        <option value="QA">QA</option>
      </select>
    </div>
    
    <!-- <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px;padding-right: 5px">
      <div class="input-group date">
        <div class="input-group-addon" style="border-color: rgb(126,86,134);background-color: rgb(126,86,134);color: white">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker2" id="month_from" onchange="drawChart()" placeholder="Select Month From" style="border-color: #00a65a;height: 35px">
      </div>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 0px">
      <div class="input-group date">
        <div class="input-group-addon" style="border-color: rgb(126,86,134);background-color: rgb(126,86,134);color: white">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker2" id="month_to" onchange="drawChart()" placeholder="Select Month To" style="border-color: #00a65a;height: 35px">
      </div>
    </div> -->
    <div class="col-xs-10" style="padding-top: 10px;padding-left: 0px;" id="div_con1">
        <div id="container" style="height: 82vh"></div>
    </div>
    <div class="col-xs-2" style="padding-right: 0;padding-left:5px;padding-top: 10px;" id="div_res_qa_1">
      <div class="small-box" style="background: #f1f2ee; height: 20.5vh; margin-bottom: 5px;" onclick="ShowModalAll('Sudah Vaksin')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.2vw;"><b>TOTAL CLAIM</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>クレーム件数の合計</b></h3>
          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_claim">0</h5>
          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="pres">0 %</h4> -->
        </div>
        <div class="icon" style="padding-top: 12vh;font-size:8vh">
          <i class="fa fa-history" ></i>
        </div>
      </div>

      <div class="small-box" style="background: #8f8f8f; height: 20.5vh; margin-bottom: 5px;" onclick="ShowModalAll('Belum Vaksin All')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: white;"><b>FREKUENSI AUDIT</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査の頻度</b></h3>
          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white;" id="total_frek_claim">0</h5>
          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum_vaksin">0 %</h4> -->
        </div>
        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
          <i class="fa fa-list"></i>
        </div>
      </div>
      <div class="small-box" style="background: #42a5f5; height: 20.5vh; margin-bottom: 5px;" onclick="ShowModalAll('Belum Vaksin All')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: white"><b>BELUM AUDIT</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査未実施み</b></h3>
          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="total_belum">0</h5>
          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum_vaksin">0 %</h4> -->
        </div>
        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
          <i class="fa fa-remove"></i>
        </div>
      </div>
      <div class="small-box" style="background: #00ff73; height: 20.5vh; margin-bottom: 5px;" onclick="ShowModalAll('Belum Vaksin All')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>SUDAH AUDIT</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査実施済</b></h3>
          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_sudah">0</h5>
          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: red;" id="persen_belum_vaksin">0 %</h4> -->
        </div>
        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
          <i class="fa fa-check"></i>
        </div>
      </div>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;">
        <div id="container2" style="height: 82vh"></div>
    </div>
    <div class="col-xs-10" style="padding-top: 10px;padding-left: 0px;">
        <div id="container3" style="height: 82vh"></div>
    </div>
    <div class="col-xs-2" style="padding-right: 0;padding-top: 10px;padding-left: 5px;" id="div_res_qa_2">
      <div class="small-box" style="background: #00af50; height: 27.6vh; margin-bottom: 5px;" onclick="ShowModalAll('Sudah Vaksin')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>TIDAK ADA TEMUAN</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: #0d47a1;"><b>指摘なし</b></h3>
          <h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_ok">0</h5>
          <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="pres_ok">0 %</h4>
        </div>
        <div class="icon" style="padding-top: 17vh;font-size:8vh">
          <i class="fa fa-check" ></i>
        </div>
      </div>

      <div class="small-box" style="background: #ffff01; height: 27.6vh; margin-bottom: 5px;" onclick="ShowModalAll('Belum Vaksin All')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.2vw;"><b>TEMUAN BELUM SEMPURNA</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: #0d47a1;"><b>指摘対応中</b></h3>
          <h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="total_ns">0</h5>
          <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="pres_ns">0 %</h4>
        </div>
        <div class="icon" style="padding-top: 17vh;font-size:8vh;">
          <i class="fa fa-minus-square-o"></i>
        </div>
      </div>

       <div class="small-box" style="background: #fe0000; height: 27.6vh; margin-bottom: 5px;" onclick="ShowModalAll('Belum Vaksin All')">
        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
          <h3 style="margin-bottom: 0px;font-size: 1.2vw;color: white"><b>TEMUAN TIDAK DILAKUKAN</b></h3>
          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: #0d47a1;text-shadow: 1px 1px 9px #fff;"><b>指摘未対応</b></h3>
          <h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="total_ng">0</h5>
          <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: white" id="pres_ng">0 %</h4>
        </div>
        <div class="icon" style="padding-top: 17vh;font-size:8vh;">
          <i class="fa fa-remove"></i>
        </div>
      </div>
    </div>
    <div class="col-xs-12" style="padding-left: 0px;padding-right: 5px" id="div_resume_qa">

    </div>
    <div class="col-xs-12" style="padding-left: 0px;padding-right: 5px" id="div_resume_claim">

    </div>
    <div class="col-xs-12" style="padding-left: 0px;padding-right: 5px" id="div_resume">

    </div>
  </div>
    <div class="modal fade" id="modalDetailSudah" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <h3 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_sudah"><b></b></h3>
            <h5 class="modal-title" style="text-align: center;" id="sub_judul_sudah"></h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12" id="data-activity-sudah">
            </div>
            <div class="col-xs-12" id="data-detail-sudah">
              <hr style="border: 3px solid red">
              <center style="background-color: orange">
                <span style="font-weight: bold;font-size: 20px;">Detail Audit NG Jelas</span>
              </center>
              <table class="table table-striped table-bordered" style="width: 100%;">
                <tr>
                  <td style="text-align: center;width: 3%;border-bottom: 2px solid black;font-weight: bold">Claim Title</td>
                  <td style="text-align: center;width: 3%;border-bottom: 2px solid black;font-weight: bold">Auditor</td>
                  <td style="text-align: center;width: 3%;border-bottom: 2px solid black;font-weight: bold">Audited At</td>
                </tr>
                <tr>
                  <td style="text-align: center;" id="claim_title"></td>
                  <td style="text-align: center;" id="auditor"></td>
                  <td style="text-align: center;" id="audited_at"></td>
                </tr>
              </table>
              <table id="table-detail-sudah" class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                  <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#73c4ff">
                  <th style="width: 1%">#</th>
                  <th style="width: 3%">Point Check</th>
                  <th style="width: 2%">Image Reference</th>
                  <th style="width: 1%">Condition</th>
                  <th style="width: 2%">Image Evidence</th>
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <th style="width: 2%">Action</th>
                  </tr>
                </thead>
                <tbody style="border:1px solid black" id="body-detail-sudah">
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

  <div class="modal fade" id="modalDetailBelum" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_belum"><b></b></h3>
            <h5 class="modal-title" style="text-align: center;" id="sub_judul_belum"></h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12" id="data-activity-belum">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

    <div class="modal fade" id="modalDetailTemuan" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1400px">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_temuan"></h3>
            <h5 class="modal-title" style="text-align: center;" id="sub_judul_temuan"></h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12" id="data-detail-temuan">
                <table id="table-detail-temuan" class="table table-striped table-bordered" style="width: 100%;">
                  <thead>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#73c4ff">
                    <th style="width: 1%">#</th>
                    <th style="width: 2%">Judul Claim</th>
                    <th style="width: 3%">Point Check</th>
                    <th style="width: 2%">Foto Referensi</th>
                    <th style="width: 1%">Kondisi</th>
                    <th style="width: 2%">Foto Hasil Audit</th>
                    <th style="width: 2%">Note</th>
                    <th style="width: 2%">Auditor</th>
                    <th style="width: 2%">Penanganan</th>
                    <th style="width: 2%">Ditangani Oleh</th>
                    </tr>
                  </thead>
                  <tbody style="border:1px solid black" id="body-detail-temuan">
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


@endsection

@section('scripts')
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<!-- <script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script> -->
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    drawChart();

    $('.datepicker').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy",
      startView: "years", 
      minViewMode: "years",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });

    var interval;
    var statusx = "idle";

    setInterval(drawChart,300000);

    // $(document).on('mousemove keyup keypress',function(){
    //   clearTimeout(interval);
    //   settimeout();
    //   statusx = "active";
    // })

    // function settimeout(){
    //   interval=setTimeout(function(){
    //     statusx = "idle";
    //     drawChart()
    //   },5000)
    // }

    if ('{{$loc}}' != 'All') {
      $('#type').val('{{$loc}}').trigger('change');
    }else{
      $('#type').val('').trigger('change');
    }
  });

  jQuery(document).ready(function() {

    $('.datepicker2').datepicker({
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });
  });

  $(function () {
    $('.select2').select2({
      allowClear:true
    })
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  function drawChart(){
    var fiscal_year = $('#fiscal_year').val();
    var type = $('#type').val();
    var data = {
      fiscal_year: fiscal_year,
      type: type,
    };
    $.get('{{ url("fetch/audit_ng_jelas_monitoring2") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          $('#title_periode').html('Periode '+result.fiscalTitle);
          var categories = [];
          var not_yet = [];
          var done_ok = [];
          var done_ns = [];
          var done_ng = [];

          $('#total_claim').html('0');
          $('#total_frek_claim').html('0');
          $('#total_sudah').html('0');
          $('#total_belum').html('0');
          $('#total_ok').html('0');
          $('#total_ns').html('0');
          $('#total_ng').html('0');

          var total_frek_claim = 0;
          var total_sudah = 0;
          var total_belum = 0;
          var total_ok = 0;
          var total_ns = 0;
          var total_ng = 0;

          for(var i = 0; i< result.month.length;i++){
            categories.push(result.month[i].month_name);
            for(var j = 0; j< result.audits.length;j++){
              var done_total = 0;
              var not_yet_total = 0;
              if (result.audits[j][0].month == result.month[i].months) {
                total_frek_claim = total_frek_claim + parseInt(result.audits[i][0].point_claim);
                total_ok = total_ok + parseInt(result.audits[i][0].done_claim_ok);
                total_ng = total_ng + parseInt(result.audits[i][0].done_claim_ng);
                total_ns = total_ns + parseInt(result.audits[i][0].done_claim_ns);
                total_belum = total_belum + (parseInt(result.audits[i][0].point_claim) - (parseInt(result.audits[i][0].done_claim_ok)+parseInt(result.audits[i][0].done_claim_ns)+parseInt(result.audits[i][0].done_claim_ng)));
                total_sudah = total_sudah + ((parseInt(result.audits[i][0].done_claim_ok)+parseInt(result.audits[i][0].done_claim_ns)+parseInt(result.audits[i][0].done_claim_ng)));
                done_ok.push({y:parseInt(result.audits[i][0].done_claim_ok),key:result.audits[i][0].month});
                done_ns.push({y:parseInt(result.audits[i][0].done_claim_ns),key:result.audits[i][0].month});
                done_ng.push({y:parseInt(result.audits[i][0].done_claim_ng),key:result.audits[i][0].month});
                not_yet.push({y:(parseInt(result.audits[i][0].point_claim) - (parseInt(result.audits[i][0].done_claim_ok)+parseInt(result.audits[i][0].done_claim_ns)+parseInt(result.audits[i][0].done_claim_ng))),key:result.audits[i][0].month});
              }
            }
          }

          $('#total_claim').html(result.titles.length);
          $('#total_frek_claim').html(total_frek_claim);
          $('#total_sudah').html(total_sudah);
          $('#total_belum').html(total_belum);
          $('#total_ok').html(total_ok);
          $('#total_ns').html(total_ns);
          $('#total_ng').html(total_ng);
          $('#pres_ok').html(((total_ok/total_frek_claim)*100).toFixed(1)+' %');
          $('#pres_ng').html(((total_ng/total_frek_claim)*100).toFixed(1)+' %');
          $('#pres_ns').html(((total_ns/total_frek_claim)*100).toFixed(1)+' %');

          Highcharts.chart('container', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME SCHEDULE AUDIT NG JELAS BY CLAIM",
              style: {
                fontSize: '20px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: categories,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return this.value;
                },
                style: {
                  fontSize:"15px",
                }
              }
            },
            yAxis: {
              allowDecimals: false,
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Audit'
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  fontSize:"15px",
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
              },
              labels:{
                style:{
                  fontSize:"13px"
                }
              }
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              }
            },
            tooltip: {
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                    fontSize: '13px'
                  }
                },
                labels:{
                  style: {
                    fontSize: '13px'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                stacking: 'normal',
                pointPadding: 0.8,
                groupPadding: 0.93,
                borderWidth: 1,
                dataLabels: {
                  enabled: true,
                  style: {
                    fontSize: '13px'
                  }
                },
                animation: false,
                point: {
                  events: {
                    click: function () {
                      showModalClaim(this.options.key,this.series.name);
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Belum Dilakukan Audit',
              data: not_yet,
              color:'#3d53ff',
            },
            {
              name: 'Sudah Dilakukan Audit (OK)',
              data: done_ok,
              color:'#00a65a',
            },
            {
              name: 'Sudah Dilakukan Audit (Temuan Belum Sempurna)',
              data: done_ns,
              color:'#ffed29',
            },
            {
              name: 'Sudah Dilakukan Audit (Temuan Tidak Dilakukan)',
              data: done_ng,
              color:'#a60000',
            }]
          });

          var categories2 = [];
          var not_yet2 = [];
          var done2 = [];

          for(var i = 0; i< result.month.length;i++){
            categories2.push(result.month[i].month_name);
            for(var j = 0; j< result.audits.length;j++){
              var done_total = 0;
              var not_yet_total = 0;
              if (result.audits[j][0].month == result.month[i].months) {
                done2.push({y:parseInt(result.audits[i][0].done_audit),key:result.audits[i][0].month});
                not_yet2.push({y:(parseInt(result.audits[i][0].point_audit) - parseInt(result.audits[i][0].done_audit)),key:result.audits[i][0].month});
              }
            }
          }

          Highcharts.chart('container2', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME AUDIT NG JELAS BY POINT AUDIT",
              style: {
                fontSize: '20px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: categories2,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return this.value;
                },
                style: {
                  fontSize:"15px",
                }
              }
            },
            yAxis: {
              allowDecimals: false,
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Audit'
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  fontSize:"15px",
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
              },
              labels:{
                style:{
                  fontSize:"13px"
                }
              }
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              }
            },
            tooltip: {
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                    fontSize: '13px'
                  }
                },
                labels:{
                  style: {
                    fontSize: '13px'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                stacking: 'normal',
                pointPadding: 0.8 ,
                groupPadding: 0.93,
                borderWidth: 1,
                dataLabels: {
                  enabled: true,
                  style: {
                    fontSize: '13px'
                  }
                },
                animation: false,
                point: {
                  events: {
                    click: function () {
                      // showModal(this.options.key,this.series.name,'point');
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Belum Dilakukan Audit',
              data: not_yet2,
              color:'#3d53ff',
            },
            {
              name: 'Sudah Dilakukan Audit',
              data: done2,
              color:'#00a65a',
            }]
          });

          var categories3 = [];
          var ns = [];
          var ng = [];
          var handling = [];

            for(var j = 0; j< result.temuan.length;j++){
              categories3.push(result.temuan[j].month_name);
              ns.push({y:parseInt(result.temuan[j].ns),key:result.temuan[j].months});
              ng.push({y:parseInt(result.temuan[j].ng),key:result.temuan[j].months});
              handling.push({y:parseInt(result.temuan[j].handling),key:result.temuan[j].months});
            }

          Highcharts.chart('container3', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME TEMUAN AUDIT NG JELAS",
              style: {
                fontSize: '20px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: categories3,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                formatter: function (e) {
                  return this.value;
                },
                style: {
                  fontSize:"15px",
                }
              }
            },
            yAxis: {
              allowDecimals: false,
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Audit'
              },
              stackLabels: {
                enabled: true,
                style: {
                  fontWeight: 'bold',
                  fontSize:"15px",
                  color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
              },
              labels:{
                style:{
                  fontSize:"13px"
                }
              }
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              }
            },
            tooltip: {
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                    fontSize: '13px'
                  }
                },
                labels:{
                  style: {
                    fontSize: '13px'
                  }
                }
              },
              column: {
                color:  Highcharts.ColorString,
                pointPadding: 0.8 ,
                groupPadding: 0.93,
                borderWidth: 1,
                dataLabels: {
                  enabled: true,
                  style: {
                    fontSize: '13px'
                  }
                },
                animation: false,
                point: {
                  events: {
                    click: function () {
                      showModalTemuan(this.options.key,this.series.name);
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Temuan Belum Sempurna',
              data: ns,
              color:'#ffed29',
              stacking:'normal'
            },
            {
              name: 'Temuan Tidak Dilakukan',
              data: ng,
              color:'#a60000',
              stacking:'normal'
            },
            {
              name: 'Temuan Sudah Ditangani',
              data: handling,
              color:'#00a65a',
            }]
          });

          $("#div_resume_qa").html('');
          var tableresumeqa = "";
          tableresumeqa += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #ffeb3b;color:black;text-align: center;height: 35px;margin-top:20px">';
          tableresumeqa += '<span style="font-size: 25px;font-weight: bold;">DETAIL AUDIT NG JELAS QA BY MONTH</span>';
          tableresumeqa += '</div>';
          // tableresumeqa += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="height: 35px;margin-top:20px;padding-left: 5px;padding-right: 0px">';
          //   tableresumeqa += '<select class="form-control select3" data-placeholder="Pilih Audit Title" style="width: 100%;padding-right: 0px" size="2" onchange="filterTable()" id="title">';
          //     tableresumeqa += '<option value=""></option>';
          //     for(var i = 0; i < result.month.length;i++){
          //       tableresumeqa += '<option value="'+result.titles[i].audit_title+'">'+result.titles[i].audit_title+'</option>';
          //     }
          //   tableresumeqa += '</select>';
          // tableresumeqa += '</div>';
          tableresumeqa += '<table id="tableresumeqa" style="background-color: #f0f0ff;color: black;font-size: 1vw;" class="table table-bordered table-hover tableresumeqas">'
          tableresumeqa += '<thead>';
          tableresumeqa += '<tr>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:3%">Periode</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Claim</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Auditor</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Plan</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Act</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Hasil</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Penanganan</th>';
          tableresumeqa += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Action</th>';
          tableresumeqa += '</tr>';
          tableresumeqa += '</thead>';
          tableresumeqa += '<tbody id="bodyResumeQa">';
          var sss = [];
          for(var j = 0; j < result.month.length;j++){
            tableresumeqa += '<tr>';
            tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center" id="month_'+j+'">'+result.month[j].month_name+'</td>';
            var title_0 = [];
            for(var k = 0; k < result.schedules.length;k++){
              if (result.schedules[k].month == result.month[j].months) {
                tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center" id="schedule_'+j+'_0">'+result.schedules[k].audit_title+'</td>';
                tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.schedules[k].employee_id+'<br>'+result.schedules[k].name+'</td>';
                tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.schedules[k].plan+'</td>';
                if (result.on_schedule.length > 0) {
                  for(var o = 0; o < result.on_schedule.length;o++){
                    if (result.on_schedule[o].month == result.schedules[k].month && result.on_schedule[o].audit_id == result.schedules[k].audit_id && result.on_schedule[o].condition != null) {
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#e0ffe4">1</td>';
                      if (result.on_schedule[o].condition == 'OK') {
                        var icon = '&#9711;';
                        var color = '#e0ffe4';
                      }else if (result.on_schedule[o].condition == 'NG') {
                        var color = '#ffd4d4';
                        var icon = '&#9747;';
                      }else{
                        var color = '#ffffab';
                        var icon = '&#8420;';
                      }
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:'+color+'">'+icon+'</td>';
                      if (result.on_schedule[o].condition != 'OK') {
                        if (result.on_schedule[o].handling == null) {
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#ffd4d4">Belum Ada Penanganan</td>';
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"><a href="{{url("index/qa/audit_ng_jelas/handling/")}}/'+result.on_schedule[o].schedule_id+'" class="btn btn-sm btn-danger">Penanganan</a></td>';
                        }else{
                          var url = '{{url("print/pdf/audit_ng_jelas")}}';
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#e0ffe4">Sudah Ditangani</td>';
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"><a class="btn btn-sm btn-primary" target="_blank" href="'+url+'/'+result.schedules[k].audit_title+'/'+result.on_schedule[o].month+'/QA">Report</a></td>';
                        }
                      }else{
                        var url = '{{url("print/pdf/audit_ng_jelas")}}';
                        tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#e0ffe4">Tidak Perlu Penanganan</td>';
                        tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"><a class="btn btn-sm btn-primary" target="_blank" href="'+url+'/'+result.schedules[k].audit_title+'/'+result.on_schedule[o].month+'/QA">Report</a></td>';
                      }
                    }else if(result.on_schedule[o].month == result.schedules[k].month && result.on_schedule[o].audit_id == result.schedules[k].audit_id && result.on_schedule[o].condition == null){
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;">0</td>';
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                    }
                  }
                }else{
                  tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                  tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                  tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                  tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                }
                title_0.push(result.schedules[k].audit_title+'_'+result.schedules[k].month);
                break;
              }
            }
            tableresumeqa += '</tr>';
            // for(var j = 0; j < result.month.length;j++){
              // tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
              var ss = 0;
              for(var k = 0; k < result.schedules.length;k++){
                if (result.schedules[k].month == result.month[j].months) {
                  if (title_0.indexOf(result.schedules[k].audit_title+'_'+result.schedules[k].month) !== -1) {

                  }else{
                    tableresumeqa += '<tr>';
                    tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.schedules[k].audit_title+'</td>';
                    tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.schedules[k].employee_id+'<br>'+result.schedules[k].name+'</td>';
                    tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center" id="schedule_'+j+'_0">'+result.schedules[k].plan+'</td>';
                    if (result.on_schedule.length > 0) {
                      for(var o = 0; o < result.on_schedule.length;o++){
                        if (result.on_schedule[o].month == result.schedules[k].month && result.on_schedule[o].audit_id == result.schedules[k].audit_id && result.on_schedule[o].condition != null) {
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#e0ffe4">1</td>';
                          if (result.on_schedule[o].condition == 'OK') {
                            var icon = '&#9711;';
                            var color = '#e0ffe4';
                          }else if (result.on_schedule[o].condition == 'NG') {
                            var color = '#ffd4d4';
                            var icon = '&#9747;';
                          }else{
                            var color = '#ffffab';
                            var icon = '&#8420;';
                          }
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:'+color+'">'+icon+'</td>';
                          if (result.on_schedule[o].condition != 'OK') {
                            if (result.on_schedule[o].handling == null) {
                              tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#ffd4d4">Belum Ada Penanganan</td>';
                              tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"><a href="{{url("index/qa/audit_ng_jelas/handling/")}}/'+result.on_schedule[o].schedule_id+'" class="btn btn-sm btn-danger">Penanganan</a></td>';
                            }else{
                              var url = '{{url("print/pdf/audit_ng_jelas")}}';
                              tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#e0ffe4">Sudah Ditangani</td>';
                              tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"><a class="btn btn-sm btn-primary" target="_blank" href="'+url+'/'+result.schedules[k].audit_title+'/'+result.on_schedule[o].month+'/QA">Report</a></td>';
                            }
                          }else{
                            var url = '{{url("print/pdf/audit_ng_jelas")}}';
                            tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;background-color:#e0ffe4">Tidak Perlu Penanganan</td>';
                            tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"><a class="btn btn-sm btn-primary" target="_blank" href="'+url+'/'+result.schedules[k].audit_title+'/'+result.on_schedule[o].month+'/QA">Report</a></td>';
                          }
                        }else if(result.on_schedule[o].month == result.schedules[k].month && result.on_schedule[o].audit_id == result.schedules[k].audit_id && result.on_schedule[o].condition == null){
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center;">0</td>';
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                          tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                        }
                      }
                    }else{
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                      tableresumeqa += '<td style="border: 1px solid black;padding:2px;text-align:center"></td>';
                    }
                    tableresumeqa += '</tr>';
                  }
                  ss++;
                }
              }
              sss.push(ss);
            // }
          }


          tableresumeqa += '</tbody>';
          $("#div_resume_qa").append(tableresumeqa);
          var div_con1 = document.getElementById("div_con1");

          if ($('#type').val() == 'Production') {
            $('#div_resume_qa').hide();
            $('#container2').show();
            $('#container3').hide();
            $('#div_res_qa_1').hide();
            $('#div_res_qa_2').hide();
            div_con1.classList.remove("col-xs-10");
            div_con1.classList.add("col-xs-12");
          }else if($('#type').val() == 'QA'){
            $('#container2').hide();
            $('#container3').show();
            $('#div_resume_qa').show();
            div_con1.classList.remove("col-xs-12");
            div_con1.classList.add("col-xs-10");
            $('#div_res_qa_1').show();
            $('#div_res_qa_2').show();
          }else{
            $('#container2').show();
            $('#container3').show();
            $('#div_resume_qa').show();
            $('#div_res_qa_1').show();
            $('#div_res_qa_2').show();
            div_con1.classList.remove("col-xs-12");
            div_con1.classList.add("col-xs-10");
          }

          // console.log(sss);

          for(var j = 0; j < sss.length;j++){
            $('#month_'+j).attr('rowspan', sss[j]);
          }

          $("#div_resume").html('');
          var tableresume = "";

          tableresume += '<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="background-color: #ffeb3b;color:black;text-align: center;height: 35px;margin-top:20px">';
          tableresume += '<span style="font-size: 25px;font-weight: bold;">DETAIL AUDIT NG JELAS BY POINT</span>';
          tableresume += '</div>';
          tableresume += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="height: 35px;margin-top:20px;padding-left: 5px;padding-right: 0px">';
            tableresume += '<select class="form-control select3" data-placeholder="Pilih Audit Title" style="width: 100%;padding-right: 0px" size="2" onchange="filterTable()" id="title">';
              tableresume += '<option value=""></option>';
              for(var i = 0; i < result.titles.length;i++){
                tableresume += '<option value="'+result.titles[i].audit_title+'">'+result.titles[i].audit_title+'</option>';
              }
            tableresume += '</select>';
          tableresume += '</div>';
          tableresume += '<table id="tableResume" style="background-color: #f0f0ff;color: black;font-size: 1vw;" class="table table-bordered table-hover tableResumes">'
          tableresume += '<thead>';
          tableresume += '<tr>';
          tableresume += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:3%">Claim</th>';
          tableresume += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Prd</th>';
          tableresume += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Area</th>';
          tableresume += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">#</th>';
          for(var j = 0; j < result.month.length;j++){
            tableresume += '<th style="border: 1px solid black;background-color: #0073b7;color:white;width:1%">'+result.month[j].month_name+'</th>';
          }
          tableresume += '</tr>';
          tableresume += '</thead>';


          tableresume += '<tbody id="bodyResume">';
          for(var i = 0; i < result.titles.length;i++){
            tableresume += '<tr id="'+result.titles[i].audit_title+'_0">';
            tableresume += '<td rowspan="2" style="border: 1px solid black;padding:2px;text-align:center">'+result.titles[i].audit_title+'</td>';
            tableresume += '<td rowspan="2" style="border: 1px solid black;padding:2px;text-align:center">'+result.titles[i].periode+'</td>';
            tableresume += '<td rowspan="2" style="border: 1px solid black;padding:2px;text-align:center">'+result.titles[i].department_shortname+'<br>'+result.titles[i].area+'</td>';
            tableresume += '<td style="border: 1px solid black;background-color: #dd4b39;color:white;width:1%;text-align:center">Plan</td>';
            for(var j = 0; j < result.month.length;j++){
              for(var k = 0; k < result.resumes.length;k++){
                if (result.resumes[k][0].month == result.month[j].months) {
                  if (result.resumes[k][0].audit_title == result.titles[i].audit_title) {
                    tableresume += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.resumes[k][0].point_audit+'</td>';
                  }
                }
              }
            }
            tableresume += '</tr>';
            tableresume += '<tr id="'+result.titles[i].audit_title+'_1">';
            tableresume += '<td style="border: 1px solid black;background-color: rgb(0, 166, 90);color:white;width:1%;text-align:center">Act</td>';
            for(var j = 0; j < result.month.length;j++){
              for(var k = 0; k < result.resumes.length;k++){
                if (result.resumes[k][0].month == result.month[j].months) {
                  if (result.resumes[k][0].audit_title == result.titles[i].audit_title) {
                    tableresume += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.resumes[k][0].done_audit+'</td>';
                  }
                }
              }
            }
            tableresume += '</tr>';
          }
          tableresume += '</tbody>';
          tableresume += '</table>';

          $("#div_resume").append(tableresume);

          $('.select3').select2({
            allowClear:true
          });


          $("#div_resume_claim").html('');
          var tableresumeClaim = "";

          tableresumeClaim += '<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="background-color: #ffeb3b;color:black;text-align: center;height: 35px;margin-top:20px">';
          tableresumeClaim += '<span style="font-size: 25px;font-weight: bold;">DETAIL AUDIT NG JELAS BY CLAIM</span>';
          tableresumeClaim += '</div>';
          tableresumeClaim += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="height: 35px;margin-top:20px;padding-left: 5px;padding-right: 0px">';
            tableresumeClaim += '<select class="form-control select4" data-placeholder="Pilih Audit Title" style="width: 100%;padding-right: 0px" size="2" onchange="filterTableClaim()" id="title_claim">';
              tableresumeClaim += '<option value=""></option>';
              for(var i = 0; i < result.titles.length;i++){
                tableresumeClaim += '<option value="'+result.titles[i].audit_title+'">'+result.titles[i].audit_title+'</option>';
              }
            tableresumeClaim += '</select>';
          tableresumeClaim += '</div>';
          tableresumeClaim += '<table id="tableResumeClaim" style="background-color: #f0f0ff;color: black;font-size: 1vw;" class="table table-bordered table-hover tableResumeClaims">'
          tableresumeClaim += '<thead>';
          tableresumeClaim += '<tr>';
          tableresumeClaim += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:3%">Claim</th>';
          tableresumeClaim += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Prd</th>';
          tableresumeClaim += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">Area</th>';
          tableresumeClaim += '<th style="border: 1px solid black;vertical-align:middle;background-color: #0073b7;color:white;width:1%">#</th>';
          for(var j = 0; j < result.month.length;j++){
            tableresumeClaim += '<th style="border: 1px solid black;background-color: #0073b7;color:white;width:1%">'+result.month[j].month_name+'</th>';
          }
          tableresumeClaim += '</tr>';
          tableresumeClaim += '</thead>';


          tableresumeClaim += '<tbody id="bodyResumeClaim">';
          for(var i = 0; i < result.titles.length;i++){
            tableresumeClaim += '<tr id="'+result.titles[i].audit_title+'_0">';
            tableresumeClaim += '<td rowspan="2" style="border: 1px solid black;padding:2px;text-align:center">'+result.titles[i].audit_title+'</td>';
            tableresumeClaim += '<td rowspan="2" style="border: 1px solid black;padding:2px;text-align:center">'+result.titles[i].periode+'</td>';
            tableresumeClaim += '<td rowspan="2" style="border: 1px solid black;padding:2px;text-align:center">'+result.titles[i].department_shortname+'<br>'+result.titles[i].area+'</td>';
            tableresumeClaim += '<td style="border: 1px solid black;background-color: #dd4b39;color:white;width:1%;text-align:center">Plan</td>';
            for(var j = 0; j < result.month.length;j++){
              for(var k = 0; k < result.resumes_claim.length;k++){
                if (result.resumes_claim[k][0].month == result.month[j].months) {
                  if (result.resumes_claim[k][0].audit_title == result.titles[i].audit_title) {
                    tableresumeClaim += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.resumes_claim[k][0].point_audit+'</td>';
                  }
                }
              }
            }
            tableresumeClaim += '</tr>';
            tableresumeClaim += '<tr id="'+result.titles[i].audit_title+'_1">';
            tableresumeClaim += '<td style="border: 1px solid black;background-color: rgb(0, 166, 90);color:white;width:1%;text-align:center">Act</td>';
            for(var j = 0; j < result.month.length;j++){
              for(var k = 0; k < result.resumes_claim.length;k++){
                if (result.resumes_claim[k][0].month == result.month[j].months) {
                  if (result.resumes_claim[k][0].audit_title == result.titles[i].audit_title) {
                    tableresumeClaim += '<td style="border: 1px solid black;padding:2px;text-align:center">'+result.resumes_claim[k][0].done_audit+'</td>';
                  }
                }
              }
            }
            tableresumeClaim += '</tr>';
          }
          tableresumeClaim += '</tbody>';
          tableresumeClaim += '</table>';

          $("#div_resume_claim").append(tableresumeClaim);

          $('.select4').select2({
            allowClear:true
          });

        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    });
  }

  function filterTable() {
    var input, filter, table,tbody, tr, td, i, txtValue;
      input = document.getElementById("title");
      filter = input.value;
      if (filter == null) {
        table = document.getElementById("bodyResume");
        tr = table.getElementsByTagName("tr");
        console.log(tr);
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
          if (td) {
              tr[i].style.display = "";
          }
        }
      }else{
        table = document.getElementById("bodyResume");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          // td = tr[i].getElementsByTagName("td")[0];
          // console.log(td);
          if (tr[i].getAttribute("id").indexOf(filter) > -1) {
            // txtValue = td.textContent || td.innerText;
            // if (txtValue.indexOf(filter) > -1) {
              tr[i].style.display = "";
            // } else {
              
            // }
          }else{
            tr[i].style.display = "none";
          }
        }
      }
  }

  function filterTableClaim() {
    var input, filter, table,tbody, tr, td, i, txtValue;
      input = document.getElementById("title_claim");
      filter = input.value;
      if (filter == null) {
        table = document.getElementById("bodyResumeClaim");
        tr = table.getElementsByTagName("tr");
        console.log(tr);
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
          if (td) {
              tr[i].style.display = "";
          }
        }
      }else{
        table = document.getElementById("bodyResumeClaim");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          // td = tr[i].getElementsByTagName("td")[0];
          // console.log(td);
          if (tr[i].getAttribute("id").indexOf(filter) > -1) {
            // txtValue = td.textContent || td.innerText;
            // if (txtValue.indexOf(filter) > -1) {
              tr[i].style.display = "";
            // } else {
              
            // }
          }else{
            tr[i].style.display = "none";
          }
        }
      }
  }

  function showModalClaim(date,kondisi) {
    $('#loading').show();
    var data = {
      date:date,
      kondisi:kondisi,
      audit_type:$('#type').val(),
    }

    $.get('{{ url("fetch/detail_audit_ng_jelas_monitoring/claim") }}', data, function(result, status, xhr) {
      if(result.status){

        if (kondisi === 'Sudah Dilakukan Audit (OK)' || kondisi === 'Sudah Dilakukan Audit (Temuan Belum Sempurna)' || kondisi === 'Sudah Dilakukan Audit (Temuan Tidak Dilakukan)') {
          $('#data-detail-sudah').hide();
          $('#data-activity-sudah').html('');
          var datatable = "";

          datatable += '<table id="data-sudah" class="table table-striped table-bordered" style="width: 100%;">';
          datatable += '<thead>'
          datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
          datatable += '<th>#</th>';
          datatable += '<th>Claim</th>';
          datatable += '<th>Process</th>';
          datatable += '<th>Report</th>';
          datatable += '<th>PDF</th>';
          datatable += '</tr>';
          datatable += '</thead>';
          datatable += '<tbody style="border:1px solid black">';
          var index_sudah = 1;
          for(var i = 0; i < result.title.length;i++){
            datatable += '<tr style="border:1px solid black">';
            datatable += '<td style="text-align:center">'+index_sudah+'</td>';
            datatable += '<td style="text-align:center">'+result.title[i].audit_title+'</td>';
            datatable += '<td style="text-align:center">'+result.title[i].proses+'</td>';
            datatable += '<td style="text-align:center"><button class="btn btn-success btn-sm" onclick="detailClaim(\''+result.title[i].audit_title+'\',\''+date+'\',\''+result.title[i].type+'\')">Report</button></td>';
            var url = '{{url("print/pdf/audit_ng_jelas")}}';
            datatable += '<td style="text-align:center"><a target="_blank" class="btn btn-danger btn-sm" href="'+url+'/'+result.title[i].audit_title+'/'+date+'/'+result.title[i].type+'"><i class="fa fa-file-pdf-o"></i>  Report PDF</a></td>';
            datatable += '</tr>';
            index_sudah++;
          }
          datatable += '</tbody>';
          datatable += '</table>';

          $('#data-activity-sudah').append(datatable);

          $('#judul_sudah').html('');
          $('#judul_sudah').html('<b>Audit NG Jelas '+kondisi+' <br>Bulan '+result.monthTitle+'<b>');
          $('#loading').hide();
          $('#modalDetailSudah').modal('show');

        }else{
          $('#data-detail-belum').hide();
          $('#data-activity-belum').html('');

          var datatable_belum = "";

          datatable_belum += '<table id="data-belum" class="table table-striped table-bordered" style="width: 100%;">';
          datatable_belum += '<table id="data-belum" class="table table-striped table-bordered" style="width: 100%;">';
          datatable_belum += '<thead>'
          datatable_belum += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
          datatable_belum += '<th>#</th>';
          datatable_belum += '<th>Claim</th>';
          datatable_belum += '<th>Audit By</th>';
          datatable_belum += '<th>Auditor</th>';
          datatable_belum += '</tr>';
          datatable_belum += '</thead>';
          datatable_belum += '<tbody style="border:1px solid black">';
          var index_belum = 1;
          for(var i = 0; i < result.title.length;i++){
            datatable_belum += '<tr style="border:1px solid black">';
            datatable_belum += '<td style="text-align:center">'+index_belum+'</td>';
            datatable_belum += '<td style="text-align:center">'+result.title[i].audit_title+'</td>';
            datatable_belum += '<td style="text-align:center">'+result.title[i].type+'</td>';
            datatable_belum += '<td style="text-align:center">'+result.title[i].auditor+'</td>';
            datatable_belum += '</tr>';
            index_belum++;
          }
          datatable_belum += '</tbody>';
          datatable_belum += '</table>';

          $('#data-activity-belum').append(datatable_belum);

          $('#judul_belum').html('');
          $('#judul_belum').html('<b>Audit NG Jelas Belum Dilakukan Audit Pada Bulan '+result.monthTitle+'<b>');
          $('#loading').hide();
          $('#modalDetailBelum').modal('show');
        }
      }
    });
  }

  function detailClaim(audit_title,date,audit_type) {
    $('#loading').show();
    var data = {
      audit_title:audit_title,
      date:date,
      audit_type:audit_type,
    }
    $.get('{{ url("fetch/detail_audit_ng_jelas_monitoring/claim/detail") }}', data, function(result, status, xhr) {
      if(result.status){
        $('#data-detail-sudah').show();
        $('#table-detail-sudah').DataTable().clear();
        $('#table-detail-sudah').DataTable().destroy();
        $('#body-detail-sudah').html('');
        var detailsudah = '';
        var index = 1;
        for(var j = 0; j < result.details.length;j++){
            detailsudah += '<tr style="border:1px solid black;text-align:center;font-size:15px">';
            detailsudah += '<td>'+index+'</td>';
            detailsudah += '<td>'+result.details[j].point_check+'</td>';
            if (result.details[j].audit_images != 'null') {
              detailsudah += '<td><img width="150px" src="{{ url("/data_file/qa/ng_jelas_point") }}/'+result.details[j].audit_images+'"></td>';
            }else{
              detailsudah += '<td></td>';
            }
            if (audit_type === 'QA') {
              if (result.details[j].kondision == 'OK') {
                detailsudah += '<td style="background-color:#a2ff8f">&#9711;</td>';
              }else if (result.details[j].kondision == 'NG') {
                detailsudah += '<td style="background-color:#ff8f8f">&#9747;</td>';
              }else if (result.details[j].kondision == 'NS'){
                detailsudah += '<td style="background-color:#fff68f">&#8420;</td>';
              }else{
                detailsudah += '<td>-</td>';
              }
              detailsudah += '<td>';
              if (result.details[j].images != 'null' || result.details[j].images != null) {
                if (result.details[j].images.match(/,/gi)) {
                  var images = result.details[j].images.split(',');
                  for(var i = 0; i < images.length;i++){
                    var url_result = "{{ url('data_file/qa/ng_jelas/') }}"+'/'+images[i];
                    detailsudah += '<img width="150px" src="'+url_result+'">';
                  }
                }else{
                  var url_result = "{{ url('data_file/qa/ng_jelas/') }}"+'/'+result.details[j].images;
                  detailsudah += '<img width="150px" src="'+url_result+'">';
                }
              }
              detailsudah += '</td>';
              // detailsudah += '<td><img width="150px" src="{{ url("/data_file/qa/ng_jelas/") }}/'+result.details[j].images+'"></td>';
            }else{
              if (result.details[j].kondision == 'Good') {
                detailsudah += '<td style="background-color:#a2ff8f">&#9711;</td>';
              }else if (result.details[j].kondision == 'Not Good') {
                detailsudah += '<td style="background-color:#ff8f8f">&#9747;</td>';
              }else{
                detailsudah += '<td>-</td>';
              }
              if (result.details[j].images != 'null' || result.details[j].images != null) {
                detailsudah += '<td><img width="150px" src="{{ url("/data_file/") }}/'+result.details[j].images+'"></td>';
              }else{
                detailsudah += '<td></td>';
              }
            }
            detailsudah += '<td>';
            if ((result.details[j].kondision == 'NG' && result.details[j].send_status == null) || (result.details[j].kondision == 'NS' && result.details[j].send_status == null)) {
              detailsudah += '<button class="btn btn-sm btn-info" onclick="sendEmail(\''+result.details[j].id+'\',\''+result.details[j].chief_foreman+'\',\''+result.details[j].manager+'\')">Send Email</button>';
            }
            detailsudah += '</td>';
            detailsudah += '</tr>';
            $('#claim_title').html(result.details[j].audit_title);
            $('#auditor').html(result.details[j].auditor+' - '+result.details[j].name);
            $('#audited_at').html(result.details[j].created_at);
            index++;
        }
        $('#body-detail-sudah').append(detailsudah);

        var table = $('#table-detail-sudah').DataTable({
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
            }
            ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 5,
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
        $('#loading').hide();
      }else{
        $('#loading').hide();
        alert('Failed');
      }
    })
  }

  function showModalTemuan(month,kondisi) {
    $('#loading').show();
    var data = {
      month:month,
      kondisi:kondisi,
      audit_type:$('#type').val(),
    }

    $.get('{{ url("fetch/audit_ng_jelas/detail_temuan") }}', data, function(result, status, xhr) {
      if(result.status){
        $('#table-detail-temuan').DataTable().clear();
        $('#table-detail-temuan').DataTable().destroy();
        $('#body-detail-temuan').html('');
        var body_temuan = '';
        var index = 1;
        for(var i = 0; i < result.temuan.length;i++){
          body_temuan += '<tr style="border:1px solid black;text-align:center;font-size:15px">';
          body_temuan += '<td>'+index+'</td>';
          body_temuan += '<td>'+result.temuan[i].audit_title+'</td>';
          body_temuan += '<td>'+result.temuan[i].audit_point+'</td>';
          if (result.temuan[i].audit_images != 'null') {
            body_temuan += '<td><img width="200px" src="{{ url("/data_file/qa/ng_jelas_point") }}/'+result.temuan[i].audit_images+'"></td>';
          }else{
            body_temuan += '<td></td>';
          }
          
          if (result.temuan[i].result_check == 'OK') {
            body_temuan += '<td style="background-color:#a2ff8f">&#9711;</td>';
          }else if (result.temuan[i].result_check == 'NG') {
            body_temuan += '<td style="background-color:#ff8f8f">&#9747;</td>';
          }else if (result.temuan[i].result_check == 'NS'){
            body_temuan += '<td style="background-color:#fff68f">&#8420;</td>';
          }else{
            body_temuan += '<td>-</td>';
          }
          body_temuan += '<td>';
          if (result.temuan[i].result_image != 'null' || result.temuan[i].result_image != null) {
            if (result.temuan[i].result_image.match(/,/gi)) {
              var images = result.temuan[i].result_image.split(',');
              for(var j = 0; j < images.length;j++){
                body_temuan += '<img width="150px" src="{{ url("/data_file/qa/ng_jelas/") }}/'+images[j]+'">';
              }
            }else{
              body_temuan += '<img width="150px" src="{{ url("/data_file/qa/ng_jelas/") }}/'+result.temuan[i].result_image+'">';
            }
          }
          body_temuan += '</td>';
          body_temuan += '<td>'+(result.temuan[i].note || '')+'</td>';
          body_temuan += '<td>'+(result.temuan[i].auditor || '')+'</td>';
          body_temuan += '<td>'+(result.temuan[i].handling || '')+'</td>';
          body_temuan += '<td>'+(result.temuan[i].handled_by || '')+'</td>';
          body_temuan += '</tr>';
          index++;
        }
        $('#body-detail-temuan').append(body_temuan);
        $('#judul_temuan').html('Resume Temuan Audit NG Jelas Bulan '+result.monthTitle);
        var table = $('#table-detail-temuan').DataTable({
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
            }
            ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 5,
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
        $('#loading').hide();
        $('#modalDetailTemuan').modal('show');
      }else{
        $('#loading').hide();
        alert('Failed');
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
      time: '2000'
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
  function sendEmail(id,chief_foreman,manager) {
    if (confirm('Apakah Anda yakin akan mengirim Email?')) {
      $('#loading').show();
      var data = {
        id:id,
        chief_foreman:chief_foreman,
        manager:manager
      }
      $.get('{{ url("send_email/qa/audit_ng_jelas/") }}',data,  function(result, status, xhr){
        if(result.status){
          $('#loading').hide();
          location.reload();
          openSuccessGritter('Success!','Send Email Success');
        } else {
          $('#loading').hide();
          audio_error.play();
          openErrorGritter('Error!',result.message);
        }
      })
    }
  }
  Highcharts.createElement('link', {
    href: '{{ url("fonts/UnicaOne.css")}}',
    rel: 'stylesheet',
    type: 'text/css'
  }, null, document.getElementsByTagName('head')[0]);

  Highcharts.theme = {
    colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
    '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
    chart: {
      backgroundColor: {
        linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
        stops: [
        [0, '#2a2a2b']
        ]
      },
      style: {
        fontFamily: 'sans-serif'
      },
      plotBorderColor: '#606063'
    },
    title: {
      style: {
        color: '#E0E0E3',
        textTransform: 'uppercase',
        fontSize: '20px'
      }
    },
    subtitle: {
      style: {
        color: '#E0E0E3',
        textTransform: 'uppercase'
      }
    },
    xAxis: {
      gridLineColor: '#707073',
      labels: {
        style: {
          color: '#E0E0E3'
        }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      title: {
        style: {
          color: '#A0A0A3'

        }
      }
    },
    yAxis: {
      gridLineColor: '#707073',
      labels: {
        style: {
          color: '#E0E0E3'
        }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      tickWidth: 1,
      title: {
        style: {
          color: '#A0A0A3'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.85)',
      style: {
        color: '#F0F0F0'
      }
    },
    plotOptions: {
      series: {
        dataLabels: {
          color: 'white'
        },
        marker: {
          lineColor: '#333'
        }
      },
      boxplot: {
        fillColor: '#505053'
      },
      candlestick: {
        lineColor: 'white'
      },
      errorbar: {
        color: 'white'
      }
    },
    legend: {
    },
    credits: {
      style: {
        color: '#666'
      }
    },
    labels: {
      style: {
        color: '#707073'
      }
    },

    drilldown: {
      activeAxisLabelStyle: {
        color: '#F0F0F3'
      },
      activeDataLabelStyle: {
        color: '#F0F0F3'
      }
    },

    navigation: {
      buttonOptions: {
        symbolStroke: '#DDDDDD',
        theme: {
          fill: '#505053'
        }
      }
    },

    rangeSelector: {
      buttonTheme: {
        fill: '#505053',
        stroke: '#000000',
        style: {
          color: '#CCC'
        },
        states: {
          hover: {
            fill: '#707073',
            stroke: '#000000',
            style: {
              color: 'white'
            }
          },
          select: {
            fill: '#000003',
            stroke: '#000000',
            style: {
              color: 'white'
            }
          }
        }
      },
      inputBoxBorderColor: '#505053',
      inputStyle: {
        backgroundColor: '#333',
        color: 'silver'
      },
      labelStyle: {
        color: 'silver'
      }
    },

    navigator: {
      handles: {
        backgroundColor: '#666',
        borderColor: '#AAA'
      },
      outlineColor: '#CCC',
      maskFill: 'rgba(255,255,255,0.1)',
      series: {
        color: '#7798BF',
        lineColor: '#A6C7ED'
      },
      xAxis: {
        gridLineColor: '#505053'
      }
    },

    scrollbar: {
      barBackgroundColor: '#808083',
      barBorderColor: '#808083',
      buttonArrowColor: '#CCC',
      buttonBackgroundColor: '#606063',
      buttonBorderColor: '#606063',
      rifleColor: '#FFF',
      trackBackgroundColor: '#404043',
      trackBorderColor: '#404043'
    },

    legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
    background2: '#505053',
    dataLabelsColor: '#B0B0B3',
    textColor: '#C0C0C0',
    contrastTextColor: '#F0F0F3',
    maskColor: 'rgba(255,255,255,0.3)'
  };
  Highcharts.setOptions(Highcharts.theme);

</script>