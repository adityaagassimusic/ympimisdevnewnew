@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
    /*text-align:center;*/
    overflow:hidden;
  }
  tbody>tr>td{
    /*text-align:center;*/
  }
  tfoot>tr>th{
    /*text-align:center;*/
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
    background-color: #e8e8e8;
  }

  #tableResume tr>td{
    text-align:left;
    padding-left: 7px;
  }
  .tableResumes tr td {
    cursor: pointer;
  }
  .td_hover:hover{
    background-color: #7dfa8c !important;
    color: black !important;
  }
  #loading, #error { display: none; }
  #data-log > thead > tr > th{
    font-size: 15px;
  }
  #data-log > tbody > tr > td{
    font-size: 14px;
  }

  #data-log > tbody > tr > td > p > img{
    width: 100px !important;
    height: auto !important;
  }


</style>
@endsection
@section('header')
@endsection
@section('content')
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
  <p style="position: absolute; color: White; top: 45%; left: 27%;">
    <span style="font-size: 40px">Loading, please wait a moment . . . <i class="fa fa-spin fa-refresh"></i></span>
  </p>
</div>
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
      <select class="form-control select2" data-placeholder="Pilih Department" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="department_all">
        <option value=""></option>
        @foreach($department_all as $dept)
          <option value="{{$dept->id}}">{{$dept->department_shortname}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 0px;padding-right: 5px">
      <select class="form-control select2" data-placeholder="Pilih Fiscal Year" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="fiscal_year">
        <option value=""></option>
        @foreach($fiscal as $fiscal)
          <option value="{{$fiscal->fiscal_year}}">{{$fiscal->fiscal_year}}</option>
        @endforeach
      </select>
    </div>
    
    <div class="col-xs-6" style="padding-top: 10px;padding-left: 0px;">
        <div id="container" style="height: 500px"></div>
    </div>
    <div class="col-xs-6" style="padding-top: 10px;padding-left: 0px;">
        <div id="container2" style="height: 500px"></div>
    </div>
    <div class="col-xs-12" style="padding-left: 0px;">
    @if(str_contains($role_code,'MIS') || str_contains($role_code,'QA') || str_contains($role_code,'STD'))
      @if(str_contains($role_code,'MIS') || str_contains($role_code,'QA'))
      <a class="btn btn-primary pull-right" href="{{url('index/audit_report_activity/document')}}">
        <b>Manage Document</b>
      </a>
      @endif
    <a class="btn btn-success pull-right" style="margin-right: 5px;" href="{{url('index/audit_report_activity/unmatch')}}">
      <b>Unmatch Document</b>
    </a>
    @endif
    </div>
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px" id="div_resume">

    </div>
    </div>
      <div class="modal fade" id="modalDetail" style="color: black;">
        <div class="modal-dialog modal-lg" style="width: 1300px">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
            </div>
            <div class="modal-body">
              <div class="row parent">
                <div class="col-md-12 child" id="data-activity" style="overflow-x: scroll;">
                </div>
                <div class="col-md-12" id="data-handling" style="display: none;">
                  <center><span style="font-size: 20px;font-weight: bold;">INPUT PENANGANAN</span></center>
                  <input type="hidden" name="id_detail" id="id_detail">
                  <table class="table table-striped table-bordered" style="width: 100%;">
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Dokumen</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="document_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Temuan</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="handling_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Date</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="date_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Leader</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="leader_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Foreman</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="foreman_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Dept</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="department_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Kesesuaian Aktual Proses</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="kesesuaian_aktual_proses_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Kelengkapan Point Safety</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="kelengkapan_point_safety_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Kesesuaian QC Koteihyo</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="kesesuaian_qc_kouteihyo_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Tindakan Perbaikan</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="tindakan_perbaikan_detail"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Target</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="target_detail"></td>
                    </tr>
                    <tr>
                      <th style="border: 1px solid black;background-color:#cddc39">EVIDENCE</th>
                      <th style="border: 1px solid black;background-color:#cddc39">PENANGANAN</th>
                    </tr>
                    <tr>
                      <td style="border: 1px solid black;">
                        <input type="file" name="handling_evidence" id="handling_evidence">
                      </td>
                      <td style="border: 1px solid black;">
                        <textarea id="handling_result"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="border: 1px solid black;">
                        <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
                          <button class="btn btn-danger" style="width: 100%;font-weight: bold;font-size: 20px;" onclick="$('#data-handling').hide();$('#data-activity').show();">CANCEL</button>
                        </div>
                        <div class="col-xs-6" style="padding-right: 0px;padding-left: 5px;">
                          <button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px;" onclick="confirmPenanganan()">CONFIRM</button>
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>

                <div class="col-md-12" id="data-audit" style="display: none;">
                  <center><span style="font-size: 20px;font-weight: bold;">INPUT AUDIT QA</span></center>
                  <input type="hidden" name="id_audit" id="id_audit">
                  <table class="table table-striped table-bordered" style="width: 100%;">
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Dokumen</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="document_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Temuan</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="handling_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Date</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="date_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Leader</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="leader_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Foreman</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="foreman_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Dept</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="department_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Kesesuaian Aktual Proses</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="kesesuaian_aktual_proses_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Kelengkapan Point Safety</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="kelengkapan_point_safety_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Kesesuaian QC Koteihyo</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="kesesuaian_qc_kouteihyo_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Tindakan Perbaikan</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="tindakan_perbaikan_audit"></td>
                    </tr>
                    <tr style="border-bottom:3px solid black;border-top:3px solid black;">
                      <th style="border: 1px solid black;background-color:#cddc39;">Target</th>
                      <td style="border: 1px solid black;text-align: left;padding-left: 7px;" id="target_audit"></td>
                    </tr>
                    <tr>
                      <th style="border: 1px solid black;background-color:#cddc39;text-align: center;">EVIDENCE</th>
                      <th style="border: 1px solid black;background-color:#cddc39;text-align: center;">HASIL AUDIT</th>
                    </tr>
                    <tr>
                      <td style="border: 1px solid black;">
                        <input type="file" name="audit_evidence" id="audit_evidence">
                      </td>
                      <td style="border: 1px solid black;">
                        <textarea id="audit_result"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="border: 1px solid black;">
                        <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
                          <button class="btn btn-danger" style="width: 100%;font-weight: bold;font-size: 20px;" onclick="$('#data-audit').hide();$('#data-activity').show();">CANCEL</button>
                        </div>
                        <div class="col-xs-6" style="padding-right: 0px;padding-left: 5px;">
                          <button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px;" onclick="confirmAudit()">CONFIRM</button>
                        </div>
                      </td>
                    </tr>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  var detail = null;
  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  jQuery(document).ready(function(){
    detail = null;
      CKEDITOR.replace('handling_result' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
      });
      // CKEDITOR.replace('audit_result' ,{
      //   filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
      // });
      $('body').toggleClass("sidebar-collapse");

      drawChart();

      $('.datepicker').datepicker({
        format: "yyyy",
        startView: "years", 
        minViewMode: "years",
        autoclose: true,
      });
      $('.datepicker2').datepicker({
        format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
        autoclose: true,
      });
      $('.select2').select2({
        allowClear:true
      });
  });

  function drawChart(){
  $('#loading').show();
    // var month_from = $('#month_from').val();
    // var month_to = $('#month_to').val();
    var department = $('#department_all').val();
    var fiscal_year = $('#fiscal_year').val();
    var data = {
      // month_from: month_from,
      // month_to: month_to,
      department: department,
      fiscal_year: fiscal_year,
    }
    $.get('{{ url("fetch/audit_ik_monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          $('#title_periode').html('Periode '+result.fiscalTitle);
          var categories = [];
          var plan = [];
          var done = [];
          var not_yet = [];

          var training_ulang = [];
          var revisi_ik = [];
          var revisi_qc = [];
          var jig = [];
          var obsolete = [];

          var training_ulang_sudah = [];
          var revisi_ik_sudah = [];
          var revisi_qc_sudah = [];
          var jig_sudah = [];
          var obsolete_sudah = [];

          var all_sudah_ditangani = [];
          var all_belum_ditangani = [];

          for(var i = 0; i < result.month_name.length;i++){
            categories.push(result.month_name[i].month_name);
            var plans = 0;
            var dones = 0;
            var not_yets = 0;

            var training_ulangs = 0;
            var revisi_iks = 0;
            var revisi_qcs = 0;
            var jigs = 0;
            var obsoletes = 0;

            var training_ulangs_sudah = 0;
            var revisi_iks_sudah = 0;
            var revisi_qcs_sudah = 0;
            var jigs_sudah = 0;
            var obsoletes_sudah = 0;

            for(var j = 0; j < result.audit_ik.length;j++){
              if (result.audit_ik[j].month == result.month_name[i].month) {
                plans = plans+(parseInt(result.audit_ik[j].sudah)+parseInt(result.audit_ik[j].belum));
                dones = dones+(parseInt(result.audit_ik[j].sudah));
                not_yets = not_yets+(parseInt(result.audit_ik[j].belum));

                training_ulangs = training_ulangs + (parseInt(result.audit_ik[j].training_ulang_ik));
                revisi_iks = revisi_iks + (parseInt(result.audit_ik[j].revisi_ik));
                revisi_qcs = revisi_qcs + (parseInt(result.audit_ik[j].revisi_qc));
                jigs = jigs + (parseInt(result.audit_ik[j].revisi_jig));
                obsoletes = obsoletes + (parseInt(result.audit_ik[j].ik_obsolete));

                training_ulangs_sudah = training_ulangs_sudah + (parseInt(result.audit_ik[j].training_ulang_ik_sudah));
                revisi_iks_sudah = revisi_iks_sudah + (parseInt(result.audit_ik[j].revisi_ik_sudah));
                revisi_qcs_sudah = revisi_qcs_sudah + (parseInt(result.audit_ik[j].revisi_qc_sudah));
                jigs_sudah = jigs_sudah + (parseInt(result.audit_ik[j].revisi_jig_sudah));
                obsoletes_sudah = obsoletes_sudah + (parseInt(result.audit_ik[j].ik_obsolete_sudah));
              }
            }
            plan.push(plans);
            done.push({y:dones,key:result.month_name[i].month});
            not_yet.push({y:not_yets,key:result.month_name[i].month});

            training_ulang.push({y:(training_ulangs-training_ulangs_sudah),key:result.month_name[i].month});
            revisi_ik.push({y:(revisi_iks-revisi_iks_sudah),key:result.month_name[i].month});
            revisi_qc.push({y:(revisi_qcs-revisi_qcs_sudah),key:result.month_name[i].month});
            jig.push({y:(jigs-jigs_sudah),key:result.month_name[i].month});
            obsolete.push({y:(obsoletes-obsoletes_sudah),key:result.month_name[i].month});

            var belum_ditangani = 0;
            
            // belum_ditangani = belum_ditangani + (training_ulangs-training_ulangs_sudah);
            belum_ditangani = belum_ditangani + (revisi_iks-revisi_iks_sudah);
            belum_ditangani = belum_ditangani + (revisi_qcs-revisi_qcs_sudah);
            belum_ditangani = belum_ditangani + (jigs-jigs_sudah);
            belum_ditangani = belum_ditangani + (obsoletes-obsoletes_sudah);

            all_belum_ditangani.push({y:belum_ditangani,key:result.month_name[i].month});

            training_ulang_sudah.push({y:training_ulangs_sudah,key:result.month_name[i].month});
            revisi_ik_sudah.push({y:revisi_iks_sudah,key:result.month_name[i].month});
            revisi_qc_sudah.push({y:revisi_qcs_sudah,key:result.month_name[i].month});
            jig_sudah.push({y:jigs_sudah,key:result.month_name[i].month});
            obsolete_sudah.push({y:obsoletes_sudah,key:result.month_name[i].month});

            var sudah_ditangani = 0;
            
            // sudah_ditangani = sudah_ditangani + training_ulangs_sudah;
            sudah_ditangani = sudah_ditangani + revisi_iks_sudah;
            sudah_ditangani = sudah_ditangani + revisi_qcs_sudah;
            sudah_ditangani = sudah_ditangani + jigs_sudah;
            sudah_ditangani = sudah_ditangani + obsoletes_sudah;

            all_sudah_ditangani.push({y:sudah_ditangani,key:result.month_name[i].month});
          }

          Highcharts.chart('container', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME HASIL AUDIT IK",
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
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total IK'
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
                pointPadding: 0.93,
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
                      showModal(this.options.key,this.series.name,"");
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            {
              name: 'Belum Dikerjakan',
              data: not_yet,
              color:'#a60000',
            },
            {
              name: 'Sudah Dikerjakan',
              data: done,
              color:'#00a65a',
            }]
          });

          Highcharts.chart('container2', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME TEMUAN AUDIT IK",
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
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total NG'
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
                pointPadding: 0.93,
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
                      showModal(this.options.key,this.series.name,"");
                    }
                  }
                },
              }
            },credits: {
              enabled: false
            },
            series: [
            // {
            //   name: 'Training Ulang IK',
            //   data: training_ulang,
            //   color:'#8000b3',
            //   stack:'GG2'
            // },
            // {
            //   name: 'Training Ulang IK (Close)',
            //   data: training_ulang_sudah,
            //   color:'#da7dff',
            //   stack:'GG2'
            // },
            {
              name: 'Revisi IK',
              data: revisi_ik,
              color:'#0061a6',
              stack:'GG2'
            },
            {
              name: 'Revisi IK (Close)',
              data: revisi_ik_sudah,
              color:'#61baff',
              stack:'GG2'
            },
            {
              name: 'Revisi QC Kouteihyo',
              data: revisi_qc,
              color:'#a19d30',
              stack:'GG2'
            },
            {
              name: 'Revisi QC Kouteihyo (Close)',
              data: revisi_qc_sudah,
              color:'#f7f145',
              stack:'GG2'
            },
            {
              name: 'Pembuatan Jig / Repair Jig',
              data: jig,
              color:'#a6007d',
              stack:'GG2'
            },
            {
              name: 'Pembuatan Jig / Repair Jig (Close)',
              data: jig_sudah,
              color:'#ff7ade',
              stack:'GG2'
            },
            {
              name: 'IK Tidak Digunakan',
              data: obsolete,
              color:'#b36100',
              stack:'GG2'
            },
            {
              name: 'IK Tidak Digunakan (Close)',
              data: obsolete_sudah,
              color:'#ed860c',
              stack:'GG2'
            },
            {
              name: 'Temuan Belum Ditangani',
              data: all_belum_ditangani,
              color:'#a60000',
              stack:'GG'
            },
            {
              name: 'Temuan Sudah Ditangani',
              data: all_sudah_ditangani,
              color:'#00a65a',
              stack:'GG'
            }]
          });

          var departments = [];
          for(var l = 0; l < result.audit_ik.length;l++){
            departments.push(result.audit_ik[l].department_id+'_'+result.audit_ik[l].department_name);
          }
          var depts = departments.filter(onlyUnique);

          $('#div_resume').html('');
          var tableresume = '';

          tableresume += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #00a65a;color:white;text-align: center;height: 35px;padding-right: 5px;margin-top:20px">';
          tableresume += '<span style="font-size: 25px;font-weight: bold;">RESUME ALL YMPI</span>';
          tableresume += '</div>';
          tableresume += '<table id="tableResume" style="background-color: black;color: white;font-size: 15px;" class="table table-bordered tableResumes">'
            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;">課</td>';
              tableresume += '<td style="border: 1px solid white">Item</td>';
              for(var j = 0; j< result.month_name.length;j++){
                tableresume += '<td style="border: 1px solid white;text-align:center">'+result.month_name[j].month_name+'</td>';
              }
              lengthspan = result.month_name.length;
              var lengthspanfix = lengthspan+3;
              tableresume += '<td style="border: 1px solid white;text-align:center">Total 額</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;">作業手順書数</td>';
              tableresume += '<td style="border: 1px solid white">Jumlah IK</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'All';
                    var dept = 'All';
                    var jumlah_ik = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        jumlah_ik = jumlah_ik + (parseInt(result.audit_ik_all[l].sudah)+parseInt(result.audit_ik_all[l].belum));
                      }
                    }
                    totals = totals + jumlah_ik;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+jumlah_ik+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;">監査が実施されました</td>';
              tableresume += '<td style="border: 1px solid white">Sudah Dilakukan Audit</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Sudah Dikerjakan';
                    var dept = 'All';
                    var sudah = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        sudah = sudah + parseInt(result.audit_ik_all[l].sudah);
                      }
                    }
                    totals = totals + sudah;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+sudah+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;">監査が実施されません</td>';
              tableresume += '<td style="border: 1px solid white">Belum Dilakukan Audit</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Belum Dikerjakan';
                    var dept = 'All';
                    var belum = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        belum = belum + parseInt(result.audit_ik_all[l].belum);
                      }
                    }
                    totals = totals + belum;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+belum+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white">IKの内容はQC工程表の内容と違います</td>';
                tableresume += '<td style="border: 1px solid white">Jumlah Proses yang Tidak Sesuai QC Koteihyo</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Tidak Sesuai';
                    var dept = 'All';
                    var tidak_sesuai = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        tidak_sesuai = tidak_sesuai + parseInt(result.audit_ik_all[l].revisi_qc);
                      }
                    }
                    totals = totals + tidak_sesuai;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+tidak_sesuai+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr style="background-color: white;background-color: #ffd154">';
                tableresume += '<td colspan="'+lengthspanfix+'" style="border: 1px solid black;color: black;text-align: center;font-weight: bold;">Penanganan :</td>';
              tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;color: #8abbff">作業仕様書再教育</td>';
                tableresume += '<td style="border: 1px solid white">Traning Ulang IK</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Training Ulang IK';
                    var dept = 'All';
                    var training_ulang = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        training_ulang = training_ulang + parseInt(result.audit_ik_all[l].training_ulang_ik);
                      }
                    }
                    totals = totals + training_ulang;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+training_ulang+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;color: #8abbff">作業仕様書修正→再教育（作業仕様書の内容が不適切だった場合）</td>';
                tableresume += '<td style="border: 1px solid white">Revisi IK, Training Ulang (Jika Ada Isi IK yang Tidak Sesuai)</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Revisi IK';
                    var dept = 'All';
                    var revisi_ik = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        revisi_ik = revisi_ik + parseInt(result.audit_ik_all[l].revisi_ik);
                      }
                    }
                    totals = totals + revisi_ik;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+revisi_ik+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;color: #8abbff">QC工程表の改定</td>';
                tableresume += '<td style="border: 1px solid white">Revisi QC Kouteihyo</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Revisi QC Kouteihyo';
                    var dept = 'All';
                    var revisi_qc = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        revisi_qc = revisi_qc + parseInt(result.audit_ik_all[l].revisi_qc);
                      }
                    }
                    totals = totals + revisi_qc;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+revisi_qc+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;color: #8abbff">治具修正・作成等（治具摩耗、適切な治具を使用していなかった等の場合</td>';
                tableresume += '<td style="border: 1px solid white">Pembuatan Jig, Repair Jig (Jika Jig Aus atau Tidak Menggunakan Jig yang Benar)</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'Pembuatan Jig / Repair Jig';
                    var dept = 'All';
                    var revisi_jig = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        revisi_jig = revisi_jig + parseInt(result.audit_ik_all[l].revisi_jig);
                      }
                    }
                    totals = totals + revisi_jig;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+revisi_jig+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;color: #ff6b6b;font-weight: bold;">使用されなくなったIK</td>';
                tableresume += '<td style="border: 1px solid white">IK Obsolete</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'IK Tidak Digunakan';
                    var dept = 'All';
                    var ik_obsolete = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        ik_obsolete = ik_obsolete + parseInt(result.audit_ik_all[l].ik_obsolete);
                      }
                    }
                    totals = totals + ik_obsolete;
                    tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+ik_obsolete+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
            tableresume += '</tr>';

            tableresume += '<tr>';
              tableresume += '<td style="border: 1px solid white;color: #ff6b6b;font-weight: bold;background-color: yellow;border-top: 3px solid white;border-bottom:3px solid red">うち仕様書通り行われていなかった工程数</td>';
                tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;font-size:20px">Total Action</td>';
                var totals = 0;
                for(var j = 0; j< result.month_name.length;j++){
                    var kondision = 'All';
                    var dept = 'All';
                    var total_penanganan = 0;
                    for(var l = 0; l < result.audit_ik_all.length;l++){
                      if (result.month_name[j].month == result.audit_ik_all[l].month) {
                        total_penanganan = total_penanganan + (parseInt(result.audit_ik_all[l].training_ulang_ik)+parseInt(result.audit_ik_all[l].revisi_ik)+parseInt(result.audit_ik_all[l].revisi_qc)+parseInt(result.audit_ik_all[l].revisi_jig)+parseInt(result.audit_ik_all[l].ik_obsolete));
                      }
                    }
                    totals = totals + total_penanganan;
                    tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;border-left:2px solid black;text-align:center;font-size:20px">'+total_penanganan+'</td>';
                }
              tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;border-left:2px solid black;text-align:center;font-size:20px">'+totals+'</td>';
            tableresume += '</tr>';

          tableresume += '</table>';

          for(var u = 0; u < depts.length;u++){
            tableresume += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #e7ffb8;color:black;text-align: center;height: 35px;padding-right: 5px;margin-top:20px">';
            tableresume += '<span style="font-size: 25px;font-weight: bold;">'+depts[u].split('_')[1]+'</span>';
            tableresume += '</div>';
            tableresume += '<table style="background-color: black;color: white;font-size: 15px;" class="table table-bordered tableResumes">'
              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;">課</td>';
                tableresume += '<td style="border: 1px solid white">Item</td>';
                for(var j = 0; j< result.month_name.length;j++){
                  tableresume += '<td style="border: 1px solid white;text-align:center">'+result.month_name[j].month_name+'</td>';
                }
                lengthspan = result.month_name.length;
                var lengthspanfix = lengthspan+3;
                tableresume += '<td style="border: 1px solid white;text-align:center">Total 額</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;">作業手順書数</td>';
                tableresume += '<td style="border: 1px solid white">Jumlah IK</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'All';
                      var dept = depts[u].split('_')[0];
                      var jumlah_ik = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          jumlah_ik = jumlah_ik + (parseInt(result.audit_ik[l].sudah)+parseInt(result.audit_ik[l].belum));
                        }
                      }
                      totals = totals + jumlah_ik;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+jumlah_ik+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;">監査が実施されました</td>';
                tableresume += '<td style="border: 1px solid white">Sudah Dilakukan Audit</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Sudah Dikerjakan';
                      var dept = depts[u].split('_')[0];
                      var sudah = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          sudah = sudah + parseInt(result.audit_ik[l].sudah);
                        }
                      }
                      totals = totals + sudah;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+sudah+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;">監査が実施されません</td>';
                tableresume += '<td style="border: 1px solid white">Belum Dilakukan Audit</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Belum Dikerjakan';
                      var dept = depts[u].split('_')[0];
                      var belum = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          belum = belum + parseInt(result.audit_ik[l].belum);
                        }
                      }
                      totals = totals + belum;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+belum+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white">IKの内容はQC工程表の内容と違います</td>';
                  tableresume += '<td style="border: 1px solid white">Jumlah Proses yang Tidak Sesuai QC Koteihyo</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Tidak Sesuai';
                      var dept = depts[u].split('_')[0];
                      var tidak_sesuai = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          tidak_sesuai = tidak_sesuai + parseInt(result.audit_ik[l].revisi_qc);
                        }
                      }
                      totals = totals + tidak_sesuai;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+tidak_sesuai+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr style="background-color: white;background-color: #ffd154">';
                  tableresume += '<td colspan="'+lengthspanfix+'" style="border: 1px solid black;color: black;text-align: center;font-weight: bold;">Penanganan :</td>';
                tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">作業仕様書再教育</td>';
                  tableresume += '<td style="border: 1px solid white">Traning Ulang IK</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Training Ulang IK';
                      var dept = depts[u].split('_')[0];
                      var training_ulang = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          training_ulang = training_ulang + parseInt(result.audit_ik[l].training_ulang_ik);
                        }
                      }
                      totals = totals + training_ulang;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+training_ulang+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">作業仕様書修正→再教育（作業仕様書の内容が不適切だった場合）</td>';
                  tableresume += '<td style="border: 1px solid white">Revisi IK, Training Ulang (Jika Ada Isi IK yang Tidak Sesuai)</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Revisi IK';
                      var dept = depts[u].split('_')[0];
                      var revisi_ik = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          revisi_ik = revisi_ik + parseInt(result.audit_ik[l].revisi_ik);
                        }
                      }
                      totals = totals + revisi_ik;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+revisi_ik+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">QC工程表の改定</td>';
                  tableresume += '<td style="border: 1px solid white">Revisi QC Kouteihyo</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Revisi QC Kouteihyo';
                      var dept = depts[u].split('_')[0];
                      var revisi_qc = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          revisi_qc = revisi_qc + parseInt(result.audit_ik[l].revisi_qc);
                        }
                      }
                      totals = totals + revisi_qc;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+revisi_qc+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #8abbff">治具修正・作成等（治具摩耗、適切な治具を使用していなかった等の場合</td>';
                  tableresume += '<td style="border: 1px solid white">Pembuatan Jig, Repair Jig (Jika Jig Aus atau Tidak Menggunakan Jig yang Benar)</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'Pembuatan Jig / Repair Jig';
                      var dept = depts[u].split('_')[0];
                      var revisi_jig = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          revisi_jig = revisi_jig + parseInt(result.audit_ik[l].revisi_jig);
                        }
                      }
                      totals = totals + revisi_jig;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+revisi_jig+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #ff6b6b;font-weight: bold;">使用されなくなったIK</td>';
                  tableresume += '<td style="border: 1px solid white">IK Obsolete</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'IK Tidak Digunakan';
                      var dept = depts[u].split('_')[0];
                      var ik_obsolete = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          ik_obsolete = ik_obsolete + parseInt(result.audit_ik[l].ik_obsolete);
                        }
                      }
                      totals = totals + ik_obsolete;
                      tableresume += '<td style="border: 1px solid white;text-align:center" class="td_hover" onclick="showModal(\''+result.month_name[j].month+'\',\''+kondision+'\',\''+dept+'\')">'+ik_obsolete+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;text-align:center">'+totals+'</td>';
              tableresume += '</tr>';

              tableresume += '<tr>';
                tableresume += '<td style="border: 1px solid white;color: #ff6b6b;font-weight: bold;background-color: yellow;border-top: 3px solid white;border-bottom:3px solid red">うち仕様書通り行われていなかった工程数</td>';
                  tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;font-size:20px">Total Action</td>';
                  var totals = 0;
                  for(var j = 0; j< result.month_name.length;j++){
                      var kondision = 'All';
                      var dept = depts[u].split('_')[0];
                      var total_penanganan = 0;
                      for(var l = 0; l < result.audit_ik.length;l++){
                        if (result.month_name[j].month == result.audit_ik[l].month && result.audit_ik[l].department_id == depts[u].split('_')[0]) {
                          total_penanganan = total_penanganan + (parseInt(result.audit_ik[l].training_ulang_ik)+parseInt(result.audit_ik[l].revisi_ik)+parseInt(result.audit_ik[l].revisi_qc)+parseInt(result.audit_ik[l].revisi_jig)+parseInt(result.audit_ik[l].ik_obsolete));
                        }
                      }
                      totals = totals + total_penanganan;
                      tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;border-left:2px solid black;text-align:center;font-size:20px">'+total_penanganan+'</td>';
                  }
                tableresume += '<td style="border: 1px solid white;background-color: yellow;color: black;font-weight: bold;border-top: 3px solid white;border-bottom:3px solid red;border-left:2px solid black;text-align:center;font-size:20px">'+totals+'</td>';
              tableresume += '</tr>';

            tableresume += '</table>';
          }


          $('#div_resume').append(tableresume);
          $('#loading').hide();
        } else{
          $('#loading').hide();
          alert(result.message);
        }
      }
    });
  }

  function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
  }

  function closeHandling(id) {
    $('#id_detail').val(id);
    for(var i = 0; i < detail.length;i++){
      if (detail[i].id_audit == id) {
        $('#document_detail').html(detail[i].no_dokumen+' '+detail[i].nama_dokumen);
        $('#handling_detail').html(detail[i].handling);
        $('#date_detail').html(detail[i].date_audit);
        $('#leader_detail').html(detail[i].leader);
        $('#foreman_detail').html(detail[i].foreman);
        $('#department_detail').html(detail[i].department_shortname);
        $('#kesesuaian_aktual_proses_detail').html(detail[i].kesesuaian_aktual_proses);
        $('#kelengkapan_point_safety_detail').html(detail[i].kelengkapan_point_safety);
        $('#kesesuaian_qc_kouteihyo_detail').html(detail[i].kesesuaian_qc_kouteihyo);
        $('#tindakan_perbaikan_detail').html(detail[i].tindakan_perbaikan);
        $('#target_detail').html(detail[i].target);
      }
    }
    $("#data-handling").show();
    $("#data-activity").hide();
  }

  function auditQA(id) {
    $('#id_audit').val(id);
    for(var i = 0; i < detail.length;i++){
      if (detail[i].id_audit == id) {
        $('#document_audit').html(detail[i].no_dokumen+' '+detail[i].nama_dokumen);
        $('#handling_audit').html(detail[i].handling);
        $('#date_audit').html(detail[i].date_audit);
        $('#leader_audit').html(detail[i].leader);
        $('#foreman_audit').html(detail[i].foreman);
        $('#department_audit').html(detail[i].department_shortname);
        $('#kesesuaian_aktual_proses_audit').html(detail[i].kesesuaian_aktual_proses);
        $('#kelengkapan_point_safety_audit').html(detail[i].kelengkapan_point_safety);
        $('#kesesuaian_qc_kouteihyo_audit').html(detail[i].kesesuaian_qc_kouteihyo);
        $('#tindakan_perbaikan_audit').html(detail[i].tindakan_perbaikan);
        $('#target_audit').html(detail[i].target);
      }
    }
    $("#data-audit").show();
    $("#data-activity").hide();
  }

  function confirmPenanganan() {
    $("#loading").show();

    var file = $('#handling_evidence').prop('files')[0];
    var filename = $('#handling_evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[0];
    var extension = $('#handling_evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[1];

    var formData = new FormData();
    formData.append('handling_result',CKEDITOR.instances['handling_result'].getData());
    formData.append('id',$('#id_detail').val());
    formData.append('file',file);
    formData.append('filename',filename);
    formData.append('extension',extension);

    $.ajax({
        url:"{{ url('input/audit_ik_monitoring/handling') }}",
        method:"POST",
        data:formData,
        dataType:'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success:function(data)
        {
          if (data.status) {
            $('#modalDetail').modal('hide');
            $('#data-activity').show();
            $('#data-handling').hide();
            sendEmailCek($('#id_detail').val());
            drawChart();
            $("#handling_result").html(CKEDITOR.instances.handling_result.setData(''));
            $("#handling_evidence").val('');
            $("#loading").hide();
            alert('Sukses Input Penanganan');
          }else{
            $("#loading").hide();
            alert(data.message);
            return false;
          }

        }
      });
  }

  function confirmAudit() {
    $("#loading").show();

    var file = $('#audit_evidence').prop('files')[0];
    var filename = $('#audit_evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[0];
    var extension = $('#audit_evidence').val().replace(/C:\\fakepath\\/i, '').split(".")[1];

    var formData = new FormData();
    formData.append('qa_audit_result',CKEDITOR.instances['audit_result'].getData());
    formData.append('id',$('#id_audit').val());
    formData.append('file',file);
    formData.append('filename',filename);
    formData.append('extension',extension);

    $.ajax({
        url:"{{ url('input/audit_ik_monitoring/audit_qa') }}",
        method:"POST",
        data:formData,
        dataType:'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success:function(data)
        {
          if (data.status) {
            $('#modalDetail').modal('hide');
            $('#data-activity').show();
            $('#data-audit').hide();
            drawChart();
            $("#loading").hide();
            alert('Sukses Input Audit QA');
          }else{
            $("#loading").hide();
            alert(data.message);
            return false;
          }

        }
      });
  }

  function showModal(month,kondisi,department) {
    $('#loading').show();
    if (department === '') {
      var departments = $('#department_all').val();
    }else if (department === 'All') {
      var departments = '';
    }else{
      var departments = department;
    }
    var data = {
      month: month,
      department: departments,
      kondisi: kondisi,
    }

    $.get('{{ url("fetch/detail_audit_ik_monitoring") }}', data, function(result, status, xhr) {
      if(result.status){
        // if ($('#data-log').text() != '') {
          $('#data-log').DataTable().clear();
          $('#data-log').DataTable().destroy();
        // }
        $('#data-activity').html('');
        var datatable = "";

        datatable += '<table id="data-log" class="table table-striped table-bordered" style="width: 100%;padding-right:20px;">';
        datatable += '<thead>'
        datatable += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
        datatable += '<th>Dokumen</th>';
        datatable += '<th>Periode</th>';
        datatable += '<th>Date</th>';
        datatable += '<th>Kesesuaian Proses</th>';
        datatable += '<th>Kelengkapan Safety</th>';
        datatable += '<th>Kesesuaian QC Kouteihyo</th>';
        datatable += '<th>Perbaikan</th>';
        datatable += '<th>Target</th>';
        datatable += '<th>Temuan</th>';
        datatable += '<th>Operator</th>';
        datatable += '<th>Leader</th>';
        datatable += '<th>Foreman</th>';
        datatable += '<th>Dept</th>';
        // datatable += '<th>Action</th>';
        datatable += '<th style="background-color:#dcc139">Verifikasi QA</th>';
        datatable += '<th style="background-color:#dcc139">Evidence Penanganan</th>';
        datatable += '<th style="background-color:#dcc139">Penanganan</th>';
        datatable += '<th style="background-color:#dcc139">By</th>';
        datatable += '<th style="background-color:#dcc139">At</th>';
        datatable += '<th style="background-color:#85bcff">Cek Efektifitas</th>';
        datatable += '<th style="background-color:#85bcff">Note Cek Efektifitas</th>';
        datatable += '<th style="background-color:#85bcff">PIC Cek</th>';
        datatable += '<th style="background-color:#85bcff">At</th>';
        datatable += '</tr>';
        datatable += '</thead>';
        datatable += '<tbody style="border:1px solid black">';
        detail = result.datas;

        for(var j = 0; j < result.datas.length;j++){
          if (kondisi == 'Belum Dikerjakan') {
            if (result.datas[j].status == 'Belum Dikerjakan') {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '</tr>';
            }
          }else if (kondisi == 'Sudah Dikerjakan') {
            if (result.datas[j].status == 'Sudah Dikerjakan') {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              if(result.datas[j].result_qc_koteihyo != null){
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '')+'<br>Doukmen '+(result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2] || '')+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '')+'</td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              if (result.datas[j].handling != 'Tidak Ada Penanganan') {
                if(result.datas[j].handling_status != null){
                  if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                    datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                  }else{
                    datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                  }
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                    datatable += '<td style="text-align:left;padding-left:7px;">';
                    // datatable += '<button class="btn btn-success btn-sm" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button>';
                    var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                  datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                    datatable += '</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }

                if(result.datas[j].audit_effectivity != null && result.datas[j].handling_status != null){
                  if(result.datas[j].audit_effectivity == 'OK'){
                    var color = '#b8ffcb';
                  }else{
                    var color = '#ffd1d1';
                  }
                  datatable += '<td style="text-align:center;background-color:'+color+';">'+result.datas[j].audit_effectivity+'</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_note+'</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_at+'</td>';
                }else{
                  var url = "{{url('index/audit_ik_monitoring/cek_efektifitas')}}/"+result.datas[j].id_audit;
                  datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-sm" onclick="sendEmailCek(\''+result.datas[j].id_audit+'\')">Send Email</button><br><a href="'+url+'" class="btn btn-primary btn-sm" style="margin-top:2px;">Cek Efektifitas</a></td>';
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                  datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '</tr>';
            }
          }else if (kondisi == 'Training Ulang IK') {
            if (result.datas[j].handling == 'Training Ulang IK' && result.datas[j].handling_status == null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/C-PE/gi) || '{{$role_code}}' == 'M') {
                  datatable += '<td style="text-align:left;padding-left:7px;">';
                  // datatable += '<button class="btn btn-success btn-sm" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button>';
                  var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                  datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                  datatable += '</td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';

              
              datatable += '</tr>';
            }
          }else if (kondisi == 'Training Ulang IK (Close)') {
            if (result.datas[j].handling == 'Training Ulang IK' && result.datas[j].handling_status != null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '</tr>';
            }
          }else if (kondisi == 'Revisi IK') {
            if (result.datas[j].handling == 'Revisi IK' && result.datas[j].handling_status == null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              if(result.datas[j].result_qc_koteihyo != null){
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '')+'<br>Doukmen '+(result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2] || '')+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '')+'</td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // if (result.datas[j].handling_status == null) {
              //   datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-xs" onclick="sendEmail(\''+result.datas[j].id_audit+'\',\''+result.datas[j].handling+'\')">Re-Send Email</button></td>';
              // }else{
              //   datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              // }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                // if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;">';
                  // datatable += '<button class="btn btn-success btn-sm" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button>';
                  var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                  datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                  datatable += '</td>';
                // }else{
                //   datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                // }
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';

              
              datatable += '</tr>';
            }
          }else if (kondisi == 'Revisi IK (Close)') {
            if (result.datas[j].handling == 'Revisi IK' && result.datas[j].handling_status != null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              if(result.datas[j].result_qc_koteihyo != null){
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '')+'<br>Doukmen '+(result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2] || '')+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '')+'</td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              if(result.datas[j].audit_effectivity != null && result.datas[j].handling_status != null){
                if(result.datas[j].audit_effectivity == 'OK'){
                  var color = '#b8ffcb';
                }else{
                  var color = '#ffd1d1';
                }
                datatable += '<td style="text-align:center;background-color:'+color+';">'+result.datas[j].audit_effectivity+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_note+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_at+'</td>';
              }else{
                var url = "{{url('index/audit_ik_monitoring/cek_efektifitas')}}/"+result.datas[j].id_audit;
                datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-sm" onclick="sendEmailCek(\''+result.datas[j].id_audit+'\')">Send Email</button><br><a href="'+url+'" class="btn btn-primary btn-sm" style="margin-top:2px;">Cek Efektifitas</a></td>';
                // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '</tr>';
            }
          }else if (kondisi == 'Revisi QC Kouteihyo' || kondisi == 'Tidak Sesuai') {
            if (result.datas[j].handling == 'Revisi QC Kouteihyo' && result.datas[j].handling_status == null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // if (result.datas[j].handling_status == null) {
              //   datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-xs" onclick="sendEmail(\''+result.datas[j].id_audit+'\',\''+result.datas[j].handling+'\')">Re-Send Email</button></td>';
              // }else{
              //   datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              // }
              datatable += '<td style="text-align:center;">';
              if ('{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/C-PE/gi) || '{{$role_code}}' == 'M') {
                if(result.datas[j].qa_verification == null){
                  var urls = '{{url("index/audit_report_activity/qa_verification/approve/")}}/'+result.datas[j].id_audit;
                  datatable += '<a class="btn btn-success btn-sm" style="margin-bottom:10px;" target="_blank" href="'+urls+'">Verifikasi</a><br>';
                  var urls = '{{url("index/audit_report_activity/qa_verification/reject/")}}/'+result.datas[j].id_audit;
                  datatable += '<a class="btn btn-danger btn-sm" target="_blank" href="'+urls+'">Reject</a>';
                }else{
                  datatable += result.datas[j].qa_verification.split('_')[0]+'<br>'+result.datas[j].qa_verification.split('_')[1]+'<br>'+result.datas[j].qa_verification.split('_')[2]+'<br>'+result.datas[j].qa_verification.split('_')[3];
                  if (result.datas[j].qa_verification.split('_')[0] == 'Rejected') {
                    datatable += '<br>'+result.datas[j].qa_verification_reason;
                  }
                }
              }
              datatable += '</td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                if (result.datas[j].department_shortname.match(/PP/gi)) {
                  if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/PE/gi) || '{{$role_code}}' == 'M') {
                      datatable += '<td style="text-align:left;padding-left:7px;">';
                      var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                      datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                      datatable += '</td>';
                    }else{
                      datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                    }
                }else{
                  if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}' == 'M') {
                      datatable += '<td style="text-align:left;padding-left:7px;">';
                      var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                      datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                      datatable += '</td>';
                    }else{
                      datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                    }
                }
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }

              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';

              
              datatable += '</tr>';
            }
          }else if (kondisi == 'Revisi QC Kouteihyo (Close)') {
            if (result.datas[j].handling == 'Revisi QC Kouteihyo' && result.datas[j].handling_status != null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:center;">';
              if(result.datas[j].qa_verification != null){
                datatable += result.datas[j].qa_verification.split('_')[0]+'<br>'+result.datas[j].qa_verification.split('_')[1]+'<br>'+result.datas[j].qa_verification.split('_')[2]+'<br>'+result.datas[j].qa_verification.split('_')[3];
                if (result.datas[j].qa_verification.split('_')[0] == 'Rejected') {
                  datatable += '<br>'+result.datas[j].qa_verification_reason;
                }
              }
              datatable += '</td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              if(result.datas[j].audit_effectivity != null && result.datas[j].handling_status != null){
                if(result.datas[j].audit_effectivity == 'OK'){
                  var color = '#b8ffcb';
                }else{
                  var color = '#ffd1d1';
                }
                datatable += '<td style="text-align:center;background-color:'+color+';">'+result.datas[j].audit_effectivity+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_note+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_at+'</td>';
              }else{
                var url = "{{url('index/audit_ik_monitoring/cek_efektifitas')}}/"+result.datas[j].id_audit;
                datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-sm" onclick="sendEmailCek(\''+result.datas[j].id_audit+'\')">Send Email</button><br><a href="'+url+'" class="btn btn-primary btn-sm" style="margin-top:2px;">Cek Efektifitas</a></td>';
                // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '</tr>';
            }
          }else if (kondisi == 'Pembuatan Jig / Repair Jig') {
            if (result.datas[j].handling == 'Pembuatan Jig / Repair Jig' && result.datas[j].handling_status == null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/C-PE/gi) || '{{$role_code}}' == 'M') {
                  datatable += '<td style="text-align:left;padding-left:7px;">';
                  // datatable += '<button class="btn btn-success btn-sm" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button>';
                  var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                  datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                  datatable += '</td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '</tr>';
            }
          }else if (kondisi == 'Pembuatan Jig / Repair Jig (Close)') {
            if (result.datas[j].handling == 'Pembuatan Jig / Repair Jig' && result.datas[j].handling_status != null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/C-PE/gi) || '{{$role_code}}' == 'M') {
                  datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }
              }
              if(result.datas[j].audit_effectivity != null && result.datas[j].handling_status != null){
                if(result.datas[j].audit_effectivity == 'OK'){
                  var color = '#b8ffcb';
                }else{
                  var color = '#ffd1d1';
                }
                datatable += '<td style="text-align:center;background-color:'+color+';">'+result.datas[j].audit_effectivity+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_note+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_at+'</td>';
              }else{
                var url = "{{url('index/audit_ik_monitoring/cek_efektifitas')}}/"+result.datas[j].id_audit;
                datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-sm" onclick="sendEmailCek(\''+result.datas[j].id_audit+'\')">Send Email</button><br><a href="'+url+'" class="btn btn-primary btn-sm" style="margin-top:2px;">Cek Efektifitas</a></td>';
                // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '</tr>';
            }
          }else if (kondisi == 'IK Tidak Digunakan') {
            if (result.datas[j].handling == 'IK Tidak Digunakan' && result.datas[j].handling_status == null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // if (result.datas[j].handling_status == null) {
              //   datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-xs" onclick="sendEmail(\''+result.datas[j].id_audit+'\',\''+result.datas[j].handling+'\')">Re-Send Email</button></td>';
              // }else{
              //   datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              // }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/C-PE/gi) || '{{$role_code}}' == 'M') {
                  datatable += '<td style="text-align:left;padding-left:7px;">';
                  // datatable += '<button class="btn btn-success btn-sm" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button>';
                  var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                  datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                  datatable += '</td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';

              
              datatable += '</tr>';
            }
          }else if (kondisi == 'IK Tidak Digunakan (Close)') {
            if (result.datas[j].handling == 'IK Tidak Digunakan' && result.datas[j].handling_status != null) {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '</tr>';
            }
          }else if (kondisi == 'Temuan Sudah Ditangani') {
            if (result.datas[j].handling_status != null && result.datas[j].handling != 'Tidak Ada Penanganan' && result.datas[j].status == 'Sudah Dikerjakan') {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:center;">';
              if(result.datas[j].qa_verification != null){
                datatable += result.datas[j].qa_verification.split('_')[0]+'<br>'+result.datas[j].qa_verification.split('_')[1]+'<br>'+result.datas[j].qa_verification.split('_')[2]+'<br>'+result.datas[j].qa_verification.split('_')[3];
                if (result.datas[j].qa_verification.split('_')[0] == 'Rejected') {
                  datatable += '<br>'+result.datas[j].qa_verification_reason;
                }
              }
              datatable += '</td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              if(result.datas[j].audit_effectivity != null && result.datas[j].handling_status != null){
                if(result.datas[j].audit_effectivity == 'OK'){
                  var color = '#b8ffcb';
                }else{
                  var color = '#ffd1d1';
                }
                datatable += '<td style="text-align:center;background-color:'+color+';">'+result.datas[j].audit_effectivity+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_note+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].audit_effectivity_at+'</td>';
              }else{
                var url = "{{url('index/audit_ik_monitoring/cek_efektifitas')}}/"+result.datas[j].id_audit;
                datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-sm" onclick="sendEmailCek(\''+result.datas[j].id_audit+'\')">Send Email</button><br><a href="'+url+'" class="btn btn-primary btn-sm" style="margin-top:2px;">Cek Efektifitas</a></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].auditor_effectivity_id || '')+' - '+(result.datas[j].auditor_effectivity_name || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '</tr>';
            }
          }else if (kondisi == 'Temuan Belum Ditangani') {
            if (result.datas[j].handling_status == null && result.datas[j].handling != 'Tidak Ada Penanganan' && result.datas[j].status == 'Sudah Dikerjakan') {
              datatable += '<tr style="border:1px solid black">';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
              datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
              datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
              // if (result.datas[j].handling_status == null) {
              //   datatable += '<td style="text-align:left;padding-left:7px;"><button class="btn btn-success btn-xs" onclick="sendEmail(\''+result.datas[j].id_audit+'\',\''+result.datas[j].handling+'\')">Re-Send Email</button></td>';
              // }else{
              //   datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              // }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              datatable += '</tr>';
            }
          }else{
            datatable += '<tr style="border:1px solid black">';
            datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].no_dokumen+'<br>'+result.datas[j].nama_dokumen+'</td>';
            datatable += '<td style="text-align:right;padding-right:7px;">'+result.datas[j].month+'</td>';
            datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].date_audit || '')+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_aktual_proses || '')+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kelengkapan_point_safety || '')+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].kesesuaian_qc_kouteihyo || '');
              if (result.datas[j].result_qc_koteihyo != null) {
                if (result.datas[j].result_qc_koteihyo.split('_').length > 2) {
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1]+' - '+result.datas[j].result_qc_koteihyo.split('_')[2];
                }else{
                  datatable += '<br>Doukmen '+result.datas[j].result_qc_koteihyo.split('_')[0]+' - '+result.datas[j].result_qc_koteihyo.split('_')[1];
                }
              }
              datatable += '</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].tindakan_perbaikan || '')+'</td>';
            datatable += '<td style="text-align:right;padding-right:7px;">'+(result.datas[j].target || '')+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling || '')+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].operator || '')+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].leader+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].foreman+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].department_shortname+'</td>';
            datatable += '<td style="text-align:left;padding-left:7px;"></td>';
            datatable += '<td style="text-align:center;">';
              if(result.datas[j].qa_verification != null){
                datatable += result.datas[j].qa_verification.split('_')[0]+'<br>'+result.datas[j].qa_verification.split('_')[1]+'<br>'+result.datas[j].qa_verification.split('_')[2]+'<br>'+result.datas[j].qa_verification.split('_')[3];
                if (result.datas[j].qa_verification.split('_')[0] == 'Rejected') {
                  datatable += '<br>'+result.datas[j].qa_verification_reason;
                }
              }
              datatable += '</td>';
            if(result.datas[j].handling_status != null){
                var url_handling = '{{url("data_file/qa/audit_ik/handling")}}/'+result.datas[j].handling_evidence;
                if (result.datas[j].handling_evidence.match(/.pdf/gi)) {
                  datatable += '<td style="text-align:left;padding-left:7px;"><a href="'+url_handling+'" target="_blank"><i class="fa fa-file"></i></a></td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"><img src="'+url_handling+'" style="width:150px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;">'+(result.datas[j].handling_result || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_id+'<br>'+result.datas[j].handled_name+'</td>';
                datatable += '<td style="text-align:left;padding-left:7px;">'+result.datas[j].handled_at+'</td>';
              }else{
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                if ('{{$role_code}}'.match(/STD/gi) || '{{$role_code}}'.match(/QA/gi) || '{{$role_code}}'.match(/MIS/gi) || '{{$role_code}}'.match(/C-PE/gi) || '{{$role_code}}' == 'M') {
                  datatable += '<td style="text-align:left;padding-left:7px;">';
                  // datatable += '<button class="btn btn-success btn-sm" onclick="closeHandling(\''+result.datas[j].id_audit+'\')">Penanganan</button>';
                  var url = "{{url('index/audit_ik_monitoring/handling')}}/"+result.datas[j].id_audit;
                  datatable += '<a href="'+url+'" class="btn btn-success btn-sm" style="margin-top:2px;">Penanganan</a>';
                  datatable += '</td>';
                }else{
                  datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                }
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
                datatable += '<td style="text-align:left;padding-left:7px;"></td>';
              }

            datatable += '<td style="text-align:left;padding-left:7px;"></td>';
            datatable += '<td style="text-align:left;padding-left:7px;"></td>';
            datatable += '<td style="text-align:left;padding-left:7px;"></td>';
            datatable += '<td style="text-align:left;padding-left:7px;"></td>';

            datatable += '</tr>';
          }
        }
        datatable += '</tbody>';
        datatable += '</table>';
        
        $('#data-activity').append(datatable);

        $('#data-log').DataTable({
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
          'pageLength': 10,
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

        $('#judul_weekly').html('<b>Audit IK <br>Bulan '+result.monthTitle+' <br>dengan Kondisi '+kondisi+'<b>');

        $('#loading').hide();
        $('#modalDetail').modal('show');
      }else{
        $('#loading').hide();
        alert(result.message);
      }
    });
  }

  function sendEmail(id,handling) {
    $('#loading').show();
    var data = {
      id:id,
      handling:handling
    }

    $.get('{{ url("input/audit_ik_monitoring/send_email") }}', data, function(result, status, xhr) {
      if(result.status){
        $('#loading').hide();
        openSuccessGritter('Success!','Success Send Email');
      }else{
        openErrorGritter('Error!',result.message);
      }
    });
  }

  function sendEmailCek(id) {
    $('#loading').show();
    var data = {
      id:id
    }

    $.get('{{ url("input/audit_ik_monitoring/send_email_cek") }}', data, function(result, status, xhr) {
      if(result.status){
        $('#loading').hide();
        openSuccessGritter('Success!','Success Send Email Cek Efektifitas');
      }else{
        openErrorGritter('Error!',result.message);
      }
    });
  }

  Highcharts.createElement('link', {
    href: '{{ url("fonts/UnicaOne.css")}}',
    rel: 'stylesheet',
    type: 'text/css'
  }, null, document.getElementsByTagName('head')[0]);

  Highcharts.theme = {
    colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
    '#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
    chart: {
      backgroundColor: {
        linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
        stops: [
        [0, '#2a2a2b'],
        [1, '#2a2a2b']
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
      itemStyle: {
        color: '#E0E0E3'
      },
      itemHoverStyle: {
        color: '#FFF'
      },
      itemHiddenStyle: {
        color: '#606063'
      }
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

  function addZero(i) {
    if (i < 10) {
      i = "0" + i;
    }
    return i;
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '5000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '5000'
    });
  }


</script>
@endsection