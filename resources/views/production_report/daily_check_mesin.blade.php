@extends('layouts.display')
@section('stylesheets')
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
  .pointers:hover{
    background-color: #7dfa8c !important;
    color: black !important;
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
      <select class="form-control select2" data-placeholder="Pilih Department" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="department_all">
        <option value=""></option>
        @foreach($department_all as $dept)
          <option value="{{$dept->id}}">{{$dept->department_shortname}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="padding-left: 5px;padding-right: 5px">
      <select class="form-control select2" data-placeholder="Pilih Fiscal Year" style="height: 40px;width: 100%;padding-right: 0px" size="2" onchange="drawChart()" id="fiscal_year">
        <option value=""></option>
        @foreach($fiscal as $fiscal)
          <option value="{{$fiscal->fiscal_year}}">{{$fiscal->fiscal_year}}</option>
        @endforeach
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
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;">
        <div id="container" style="height: 500px"></div>
    </div>
    <!-- <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;">
        <div id="container2" style="height: 500px"></div>
    </div> -->
    <div class="col-xs-12" style="padding-top: 10px;padding-left: 0px" id="div_resume">

    </div>
  </div>
    <div class="modal fade" id="modalDetail" style="color: black;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
          </div>
          <div class="modal-body">
            <div class="row">
            <div class="col-md-12" id="data-activity">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;">
              <thead>
              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                <th style="width: 1%;">#</th>
                <th style="width: 1%;">Mesin</th>
                <th style="width: 5%;">Aktual</th>
                <th style="width: 1%;">PIC</th>
                <th style="width: 1%;">Leader</th>
                <th style="width: 1%;">Foreman</th>
                <th style="width: 1%;">Status</th>
              </tr>
              </thead>
              <tbody id="body-detail">
                
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
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<!-- <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script> -->
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var detail = null;

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

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

    $(document).on('mousemove keyup keypress',function(){
      // clearTimeout(interval);
      // settimeout();
      statusx = "active";
    })

    detail = null;

    // function settimeout(){
    //   interval=setTimeout(function(){
    //     statusx = "idle";
    //     drawChart()
    //   },600000)
    // }
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
      });
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
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
    $.get('{{ url("fetch/daily_check_mesin") }}', data, function(result, status, xhr) {
        if(result.status){
          $('#title_periode').html('Periode '+result.fiscal_year);
          var categories = [];
          var plan = [];
          var done = [];
          var not_yet = [];

          for(var i = 0; i< result.dailycheck.length;i++){
            categories.push(result.dailycheck[i].month_name);
            plan.push(parseInt(result.dailycheck[i].point));
            done.push({y:parseInt(result.dailycheck[i].actual),key:result.dailycheck[i].month});
            var not_yets = parseInt(result.dailycheck[i].point)-parseInt(result.dailycheck[i].actual);
            not_yet.push({y:not_yets,key:result.dailycheck[i].month});
          }

          Highcharts.chart('container', {
            chart: {
              type: 'column',
              backgroundColor: null
            },
            title: {
              floating: false,
              text: "RESUME DAILY CHECK MESIN",
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

          $('#div_resume').html('');
          var div_resume = '';

          if (department != '') {
            div_resume += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #8557cf;color:white;text-align: center;height: 35px;padding-right: 5px;margin-top:20px">';
              div_resume += '<span style="font-size: 25px;font-weight: bold;">Resume All YMPI</span>';
              div_resume += '</div>';
              div_resume += '<table id="div_resume" style="background-color: black;color: white;font-size: 15px;" class="table table-bordered div_resumes">'
              div_resume += '<tr>';
              div_resume += '<td style="border: 1px solid black;background-color:white;color:black;text-align:left;padding-left:10px;">Item</td>';
              var month = [];
              for(var i = 0; i < result.dailycheck.length;i++){
                month.push({month:result.dailycheck[i].month,month_name:result.dailycheck[i].month_name});
              }

              var all = [];
              var sudah = [];
              var belum = [];

              for(var i = 0; i < month.length;i++){
                div_resume += '<td style="border: 1px solid black;background-color:white;color:black;padding-left: 5px;">'+month[i].month_name+'</td>';
              }
              div_resume += '</tr>';

              for(var k = 0; k < month.length;k++){
                var alls = 0;
                var sudahs = 0;
                var belums = 0;
                for(var i = 0; i < result.resumes.length;i++){
                  for(var j = 0; j < result.resumes[i].resume.length;j++){
                    if (result.resumes[i].resume[j].month == month[k].month) {
                      alls = alls + parseInt(result.resumes[i].resume[j].point);
                      sudahs = sudahs + parseInt(result.resumes[i].resume[j].actual);
                      belums = belums + (parseInt(result.resumes[i].resume[j].point) - parseInt(result.resumes[i].resume[j].actual));
                    }
                  }
                }
                all.push({count:alls,month:month[k].month});
                sudah.push({count:sudahs,month:month[k].month});
                belum.push({count:belums,month:month[k].month});
              }

              div_resume += '<tr>';
                div_resume += '<td style="border: 1px solid white;text-align:left;padding-left:10px;">Jumlah Mesin</td>';
                for(var i = 0; i < all.length;i++){
                  var status = 'All';
                  var dept = '';
                  div_resume += '<td class="pointers" style="border: 1px solid white;cursor:pointer;text-align:right;padding-right:5px;" onclick="showModal(\''+all[i].month+'\',\''+status+'\',\''+dept+'\')">'+all[i].count+'</td>';
                }
              div_resume += '</tr>';

              div_resume += '<tr>';
                div_resume += '<td style="border: 1px solid white;text-align:left;padding-left:10px;">Sudah Dilakukan Daily Check</td>';
                for(var i = 0; i < sudah.length;i++){
                  var status = 'Sudah Dikerjakan';
                  var dept = '';
                  div_resume += '<td class="pointers" style="border: 1px solid white;cursor:pointer;text-align:right;padding-right:5px;" onclick="showModal(\''+sudah[i].month+'\',\''+status+'\',\''+dept+'\')">'+sudah[i].count+'</td>';
                }
              div_resume += '</tr>';

              div_resume += '<tr>';
                div_resume += '<td style="border: 1px solid white;text-align:left;padding-left:10px;">Belum Dilakukan Daily Check</td>';
                for(var i = 0; i < belum.length;i++){
                  var status = 'Belum Dikerjakan';
                  var dept = '';
                  div_resume += '<td class="pointers" style="border: 1px solid white;cursor:pointer;text-align:right;padding-right:5px;" onclick="showModal(\''+belum[i].month+'\',\''+status+'\',\''+dept+'\')">'+belum[i].count+'</td>';
                }
              div_resume += '</tr>';

            div_resume += '<table>';
          }

          for(var i = 0; i < result.resumes.length;i++){
            div_resume += '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #00a65a;color:white;text-align: center;height: 35px;padding-right: 5px;margin-top:20px">';
              div_resume += '<span style="font-size: 25px;font-weight: bold;">Resume '+result.resumes[i].department_name+'</span>';
              div_resume += '</div>';
              div_resume += '<table id="div_resume" style="background-color: black;color: white;font-size: 15px;" class="table table-bordered div_resumes">'
              div_resume += '<tr>';
              div_resume += '<td style="border: 1px solid black;background-color:white;color:black;text-align:left;padding-left:10px;">Item</td>';
              for(var j = 0; j < result.resumes[i].resume.length;j++){
                div_resume += '<td style="border: 1px solid black;background-color:white;color:black;padding-left:5px;">'+result.resumes[i].resume[j].month_name+'</td>';
              }
              div_resume += '</tr>';

              div_resume += '<tr>';
                div_resume += '<td style="border: 1px solid white;text-align:left;padding-left:10px;">Jumlah Mesin</td>';
                for(var j = 0; j < result.resumes[i].resume.length;j++){
                  var status = 'All';
                  div_resume += '<td class="pointers" style="border: 1px solid white;cursor:pointer;text-align:right;padding-right:5px;" onclick="showModal(\''+result.resumes[i].resume[j].month+'\',\''+status+'\',\''+result.resumes[i].department_id+'\')">'+result.resumes[i].resume[j].point+'</td>';
                }
              div_resume += '</tr>';

              div_resume += '<tr>';
                div_resume += '<td style="border: 1px solid white;text-align:left;padding-left:10px;">Sudah Dilakukan Daily Check</td>';
                for(var j = 0; j < result.resumes[i].resume.length;j++){
                  var status = 'Sudah Dikerjakan';
                  div_resume += '<td class="pointers" style="border: 1px solid white;cursor:pointer;text-align:right;padding-right:5px;" onclick="showModal(\''+result.resumes[i].resume[j].month+'\',\''+status+'\',\''+result.resumes[i].department_id+'\')">'+result.resumes[i].resume[j].actual+'</td>';
                }
              div_resume += '</tr>';

              div_resume += '<tr>';
                div_resume += '<td style="border: 1px solid white;text-align:left;padding-left:10px;">Belum Dilakukan Daily Check</td>';
                for(var j = 0; j < result.resumes[i].resume.length;j++){
                  var status = 'Belum Dikerjakan';
                  div_resume += '<td class="pointers" style="border: 1px solid white;cursor:pointer;text-align:right;padding-right:5px;" onclick="showModal(\''+result.resumes[i].resume[j].month+'\',\''+status+'\',\''+result.resumes[i].department_id+'\')">'+(parseInt(result.resumes[i].resume[j].point) - parseInt(result.resumes[i].resume[j].actual))+'</td>';
                }
              div_resume += '</tr>';

            div_resume += '<table>';
          }
          $('#div_resume').append(div_resume);

          $('#loading').hide();
        }else{
          $('#loading').hide();
          openErrorGritter('Error!',result.message);
        }
      });
  }

  function showModal(month,statuses,department) {
    $('#loading').show();
    var data = {
      month:month,
      department:department,
      statuses:statuses
    }

    $.get('{{ url("fetch/daily_check_mesin/detail") }}', data, function(result, status, xhr) {
        if(result.status){
          $('#data-log').DataTable().clear();
          $('#data-log').DataTable().destroy();
          $('#body-detail').html('');
          var datatable = '';
          for(var i = 0; i < result.detail.length;i++){
              if (statuses == 'Belum Dikerjakan') {
                if (result.detail[i].pic == null) {
                  datatable += '<tr style="border:1px solid black">';
                  datatable += '<td style="text-align:right;padding-right:5px;">'+(i+1)+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].nama_pengecekan+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+(result.detail[i].aktual || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+(result.detail[i].pic || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].leader+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].foreman+'</td>';
                  if (result.detail[i].pic == null) {
                    datatable += '<td style="background-color:#ffadad">Belum Dikerjakan</td>';
                  }else{
                    datatable += '<td style="background-color:#bfffad">Sudah Dikerjakan</td>';
                  }
                  datatable += '</tr>';
                }
              }else if (statuses == 'Sudah Dikerjakan'){
                if (result.detail[i].pic != null) {
                  datatable += '<tr style="border:1px solid black">';
                  datatable += '<td style="text-align:right;padding-right:5px;">'+(i+1)+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].nama_pengecekan+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+(result.detail[i].aktual || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+(result.detail[i].pic || '')+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].leader+'</td>';
                  datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].foreman+'</td>';
                  if (result.detail[i].pic == null) {
                    datatable += '<td style="background-color:#ffadad">Belum Dikerjakan</td>';
                  }else{
                    datatable += '<td style="background-color:#bfffad">Sudah Dikerjakan</td>';
                  }
                  datatable += '</tr>';
                }
              }else{
                datatable += '<tr style="border:1px solid black">';
                datatable += '<td style="text-align:right;padding-right:5px;">'+(i+1)+'</td>';
                datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].nama_pengecekan+'</td>';
                datatable += '<td style="text-align:left;padding-left:5px;">'+(result.detail[i].aktual || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:5px;">'+(result.detail[i].pic || '')+'</td>';
                datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].leader+'</td>';
                datatable += '<td style="text-align:left;padding-left:5px;">'+result.detail[i].foreman+'</td>';
                if (result.detail[i].pic == null) {
                  datatable += '<td style="background-color:#ffadad">Belum Dikerjakan</td>';
                }else{
                  datatable += '<td style="background-color:#bfffad">Sudah Dikerjakan</td>';
                }
                datatable += '</tr>';
              }
          }
          $('#body-detail').append(datatable);

          var table = $('#data-log').DataTable({
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
              }
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
          $('#modalDetail').modal('show');

          $('#judul_weekly').html('<b>Daily Check Mesin <br>Bulan '+result.monthTitle);

          $('#loading').hide();
        }else{
          openErrorGritter('Error!',result.message);
          $('#loading').hide();
        }
    });
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