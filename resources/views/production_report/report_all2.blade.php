@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .gambar {
    width: 300px;
    height: 300px;
    background-color: white;
    border-radius: 15px;
    margin-left: 30px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }
</style>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding:0">
  <div class="row" style="padding:0">
    <h1 style="color:white">
      <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      </div>
      <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <center>
          <b style="font-size: 30px">{{$page}} {{$departments}} (職長業務管理)</b>
        </center>
      </div>
      <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green" style="border-color: #00a65a">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker2" id="week_date" onchange="drawChart()" placeholder="Select Month"  style="border-color: #00a65a">
        </div>
      </div>
    </h1>
  </div>
  <div class="row" style="padding: 0" id="containergambar">
    <!-- <div id="container1" class="gambar"></div> -->
  </div>
  <div class="modal fade" id="myModal" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_weekly"><b></b></h4>
          <h5 class="modal-title" style="text-align: center;" id="sub_judul_weekly"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-activity-head-weekly" style="background-color: rgba(126,86,134,.7);">
                </thead>
                <tbody id="data-activity-weekly">
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

  <div class="modal fade" id="myModalMonthly" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_monthly"><b></b></h4>
          <h5 class="modal-title" style="text-align: center;" id="sub_judul_monthly"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-activity-head-monthly" style="background-color: rgba(126,86,134,.7);">
                  <th>No.</th>
                  <th>Activity Name</th>
                  <th>Plan</th>
                  <th>Actual</th>
                </thead>
                <tbody id="data-activity-monthly">
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

  <div class="modal fade" id="myModalDaily" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;" id="judul_daily"><b></b></h4>
          <h5 class="modal-title" style="text-align: center;" id="sub_judul_daily"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-activity-head-daily" style="background-color: rgba(126,86,134,.7);">
                </thead>
                <tbody id="data-activity-daily">
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

  <div class="modal fade" id="myModal2" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Leader Task Monitoring</b></h4>
          <h5 class="modal-title" style="text-align: center;" id="judul"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th colspan="8" style="text-align: center;" id="leader_name"></th>
                  </tr>
                  <tr>
                    <th>Activity Name</th>
                    <th style="width: 13%" colspan="5">W1</th>
                    <th style="width: 13%" colspan="5">W2</th>
                    <th style="width: 13%" colspan="5">W3</th>
                    <th style="width: 13%" colspan="5">W4</th>
                  </tr>
                  <tr>
                    <th>aa</th>
                  </tr>
                </thead>
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

  <div class="modal fade" id="myModal3" style="color: black;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Leader Task Monitoring</b></h4>
          <h5 class="modal-title" style="text-align: center;" id="judul"></h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div id="containerdetail"></div>
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
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>

<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

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
      clearTimeout(interval);
      settimeout();
      statusx = "active";
    })

    function settimeout(){
      interval=setTimeout(function(){
        statusx = "idle";
        drawChart()
      },30000)
    }
  });

  jQuery(document).ready(function() {

    $('.datepicker2').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });
  });

  $(function () {
    $('.select2').select2()
  });

  function renderLabels() {

    var offsetTop = 5,
    offsetLeft = 5;

    if (!this.series[0].label) {
      this.series[0].label = this.renderer
      .label(this.series[0].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
      .css({
        'color': '#FFFFFF',
        'fontWeight':'bold',
        'textAlign': 'center',
        'borderColor': '#EBBA95'
      })
      .add(this.series[0].group);
    }

    this.series[0].label.translate(
      this.chartWidth / 2 - this.series[0].label.width + offsetLeft,
      this.plotHeight / 2 - this.series[0].points[0].shapeArgs.innerR -
      (this.series[0].points[0].shapeArgs.r - this.series[0].points[0].shapeArgs.innerR) / 2 + offsetTop
      );


    if (!this.series[1].label) {
      if(this.series[1].points[0].y != null){
        this.series[1].label = this.renderer
        .label(this.series[1].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
        .css({
          'color': '#FFFFFF',
          'fontWeight':'bold',
          'textAlign': 'center'
        })
        .add(this.series[1].group);
      }
      else{
        this.series[1].label = this.renderer
        .label(' ', 0, 0, 'rect', 0, 0, true, true)
        .css({
          'color': '#FFFFFF',
          'fontWeight':'bold',
          'textAlign': 'center'
        })
        .add(this.series[1].group);
      }
    }

    this.series[1].label.translate(
      this.chartWidth / 2 - this.series[1].label.width + offsetLeft,
      this.plotHeight / 2 - this.series[1].points[0].shapeArgs.innerR -
      (this.series[1].points[0].shapeArgs.r - this.series[1].points[0].shapeArgs.innerR) / 2 + offsetTop
      );

    if (!this.series[2].label) {
      if(this.series[2].points[0].y != null){
        this.series[2].label = this.renderer
        .label(this.series[2].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
        .css({
          'color': '#FFFFFF',
          'fontWeight':'bold',
          'textAlign': 'center'
        })
        .add(this.series[2].group);
      }
      else{
        this.series[2].label = this.renderer
        .label(' ', 0, 0, 'rect', 0, 0, true, true)
        .css({
          'color': '#FFFFFF',
          'fontWeight':'bold',
          'textAlign': 'center'
        })
        .add(this.series[2].group);
      }
    }

    this.series[2].label.translate(
      this.chartWidth / 2 - this.series[2].label.width + offsetLeft,
      this.plotHeight / 2 - this.series[2].points[0].shapeArgs.innerR -
      (this.series[2].points[0].shapeArgs.r - this.series[2].points[0].shapeArgs.innerR) / 2 + offsetTop
      );

    if (!this.series[3].label) {
      if(this.series[3].points[0].y != null){
        this.series[3].label = this.renderer
        .label(this.series[3].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
        .css({
          'color': '#FFFFFF',
          'fontWeight':'bold',
          'textAlign': 'center'
        })
        .add(this.series[3].group);
      }
      else{
        this.series[3].label = this.renderer
        .label(' ', 0, 0, 'rect', 0, 0, true, true)
        .css({
          'color': '#FFFFFF',
          'fontWeight':'bold',
          'textAlign': 'center'
        })
        .add(this.series[3].group);
      }
    }

    this.series[3].label.translate(
      this.chartWidth / 2 - this.series[3].label.width + offsetLeft,
      this.plotHeight / 2 - this.series[3].points[0].shapeArgs.innerR -
      (this.series[3].points[0].shapeArgs.r - this.series[3].points[0].shapeArgs.innerR) / 2 + offsetTop
      );

    // if (!this.series[4].label) {
    //   if(this.series[4].points[0].y != null){
    //     this.series[4].label = this.renderer
    //     .label(this.series[4].points[0].y+'%', 0, 0, 'rect', 0, 0, true, true)
    //     .css({
    //       'color': '#FFFFFF',
    //       'fontWeight':'bold',
    //       'textAlign': 'center'
    //     })
    //     .add(this.series[4].group);
    //   }
    //   else{
    //     this.series[4].label = this.renderer
    //     .label(' ', 0, 0, 'rect', 0, 0, true, true)
    //     .css({
    //       'color': '#FFFFFF',
    //       'fontWeight':'bold',
    //       'textAlign': 'center'
    //     })
    //     .add(this.series[4].group);
    //   }
    // }

    // this.series[4].label.translate(
    //   this.chartWidth / 2 - this.series[4].label.width + offsetLeft,
    //   this.plotHeight / 2 - this.series[4].points[0].shapeArgs.innerR -
    //   (this.series[4].points[0].shapeArgs.r - this.series[4].points[0].shapeArgs.innerR) / 2 + offsetTop
    //   );
  }

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  function drawChart(){
    var week_date = $('#week_date').val();
    var data = {
      week_date: week_date
    };
    $.get('{{ url("index/production_report/fetchReportByLeader/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          // console.table(result.leaderrr);
          $('#containergambar').empty();
          var gambar = "";
          for(var k=1; k< result.datas.length;k++){
            gambar += '<div id="container'+k+'" class="gambar"></div>';
          }
          
          $('#containergambar').append(gambar);
          for(var i=1; i< result.datas.length;i++){
            for(var j=0; j< result.datas[i].length;j++){
              var result_monthly;
              var result_weekly;
              var result_daily;
              var result_prev;
              var cur_day = parseInt(result.datas[i][j].persen_cur_day);
              var cur_week = parseInt(result.datas[i][j].persen_cur_week);
              var result_prev = parseInt(result.datas[i][j].persen_prev);

              // if(parseInt(result.datas[i][j].persen_monthly) == 0){
              //   result_monthly = null;
              //   outerRadiusMonthly= '0%';
              //   innerRadiusMonthly= '0%';
              // }
              // else{
                result_monthly = parseInt(result.datas[i][j].persen_monthly);
                outerRadiusMonthly= '100%';
                innerRadiusMonthly= '80%';
              // }

              // if(parseInt(result.datas[i][j].persen_weekly) == 0){
              //   result_weekly = null;
              //   outerRadiusWeekly= '0%';
              //   innerRadiusWeekly= '0%';
              // }
              // else{
                result_weekly = parseInt(result.datas[i][j].persen_weekly);
                outerRadiusWeekly= '80%';
                innerRadiusWeekly= '60%';
              // }

              // if(parseInt(result.datas[i][j].persen_daily) == 0){
              //   result_daily = null;
              //   outerRadiusDaily= '0%';
              //   innerRadiusDaily= '0%';
              // }
              // else{
                result_daily = parseInt(result.datas[i][j].persen_daily);
                outerRadiusDaily= '60%';
                innerRadiusDaily= '40%';
              // }

              // if(parseInt(result.datas[i][j].persen_monthly) == 0){
              //   result_monthly = null;
              // }
              // else{
              //   result_monthly = parseInt(result.datas[i][j].persen_monthly);
              // }

              // if(parseInt(result.datas[i][j].persen_weekly) == 0){
              //   result_weekly = null;
              // }
              // else{
              //   result_weekly = parseInt(result.datas[i][j].persen_weekly);
              // }

              // if(parseInt(result.datas[i][j].persen_daily) == 0){
              //   result_daily = null;
              // }
              // else{
              //   result_daily = parseInt(result.datas[i][j].persen_daily);
              // }
              var leader_name = result.datas[i][j].leader_name;
              // console.log(result_monthly);

              chart = new Highcharts.Chart({
                chart: {
                  type: 'solidgauge',
                  height: '105%',
                  events: {
                    render: renderLabels
                  },
                  renderTo: 'container'+i
                },
                title: {
                  text: leader_name,
                  style: {
                    fontSize: '14px'
                  }
                },
                tooltip: {
                  enabled:false
                },

                pane: {
                  startAngle: 0,
                  endAngle: 360,
                  center: ['50%', '50%'],
                  size: '100%',
                      background: [{ // Track for Move
                        outerRadius: outerRadiusMonthly,
                        innerRadius: innerRadiusMonthly,
                        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[1])
                        .setOpacity(0.3)
                        .get(),
                        borderWidth: 0
                      }, { // Track for Exercise
                        outerRadius: outerRadiusWeekly,
                        innerRadius: innerRadiusWeekly,
                        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2])
                        .setOpacity(0.3)
                        .get(),
                        borderWidth: 0
                      }, { // Track for Stand
                        outerRadius: outerRadiusDaily,
                        innerRadius: innerRadiusDaily,
                        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[3])
                        .setOpacity(0.3)
                        .get(),
                        borderWidth: 0
                      }]
                    },
                    yAxis: {
                      min: 0,
                      max: 100,
                      lineWidth: 0,
                      tickPositions: []
                    },
                    legend: {
                      itemStyle:{
                        color: "white",
                        fontSize: "12px",
                        fontWeight: "bold",
                      },
                      itemHover: {
                        enabled : false
                      },
                      itemHiddenStyle: {
                        color: '#606063'
                      },
                      labelFormatter: function(e) {
                        return '<span style="text-weight:bold;color:' + this.userOptions.color + ';">' + this.name + '</span>';
                      },
                      symbolWidth: 0,
                      squareSymbol: false
                    },
                    plotOptions: {
                      solidgauge: {
                        dataLabels: {
                          enabled: false
                        },
                        linecap: 'round',
                        stickyTracking: false,
                        rounded: true
                      },
                      series:{
                        cursor: 'pointer',
                        point: {
                          events: {
                            click: function(e) {
                              if(e.point.series.name == 'Prev Month'){
                              }
                              else if(e.point.series.name != 'Current Day'){
                                ShowModalChart(this.options.key,e.point.series.name);
                                  }
                                }
                              },
                            },
                      }
                    },
                    credits: {
                      enabled: false
                    },
                    series: [
                    {
                      name: 'Monthly',
                      color: Highcharts.getOptions().colors[1],
                      data: [{
                        color: Highcharts.getOptions().colors[1],
                        radius: '100%',
                        innerRadius: '80%',
                        y: result_monthly,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true
                    }, {
                      name: 'Weekly',
                      color: Highcharts.getOptions().colors[2],
                      data: [{
                        color: Highcharts.getOptions().colors[2],
                        radius: '80%',
                        innerRadius: '60%',
                        y: result_weekly,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true
                    }, 
                    {
                      name: 'Daily',
                      color: Highcharts.getOptions().colors[3],
                      data: [{
                        color: Highcharts.getOptions().colors[3],
                        radius: '60%',
                        innerRadius: '40%',
                        y: result_daily,
                        key: result.datas[i][j].leader_name
                      }],
                      showInLegend: true
                    },
                    {
                      name: 'Current Day',
                      color: '#ff70f8',
                      data: [{
                        color: '#ff70f8',
                        radius: '40%',
                        innerRadius: '20%',
                        y: cur_day,
                        key: result.datas[i][j].leader_name
                      }],
                      stacking: 'normal',
                      showInLegend: true
                    }]
                  });
}
}
} else{
  alert('Attempt to retrieve data failed');
}
}
});

function ShowModalChart(leader_name,frequency) {
  var week_date = $('#week_date').val();
  var data = {
    leader_name:leader_name,
    frequency:frequency,
    week_date:week_date
  }
  $('#data-activity-weekly').append().empty();
  $('#data-activity-head-weekly').append().empty();
  $('#data-activity-daily').append().empty();
  $('#data-activity-head-daily').append().empty();
  $('#data-activity-monthly').append().empty();
  $('#sub_judul_weekly').append().empty();
  $('#judul_weekly').append().empty();
  $('#sub_judul_monthly').append().empty();
  $('#judul_monthly').append().empty();
  $('#sub_judul_daily').append().empty();
  $('#judul_daily').append().empty();
  var dd = [];
          // var dd = new Object();
          if(frequency == 'Weekly'){
            $('#myModal').modal('show');
            $.get('{{ url("index/production_report/fetchDetailReportWeekly/".$id) }}', data, function(result, status, xhr) {
              if(result.status){

                $('#sub_judul_weekly').append('<b>'+frequency+' Report of '+leader_name+' on '+result.monthTitle+'</b>');
                $('#judul_weekly').append('<b>Leader Task Monitoring of '+leader_name+'</b>');

                var total_plan = 0;
                var presentase = 0;
                var body = '';
                var head = '';
                var jj = [];
                var no = 1;
                var aa = 1;
                var bb = 0;
                var url = '{{ url("") }}';
                head += '<tr>';
                head += '<th rowspan="2" style="vertical-align: middle;">No.</th>';
                head += '<th rowspan="2" style="vertical-align: middle;"><center>Activity Name</center></th>';
                // console.table(result.detail);
                for(var a = 0; a < result.date.length; a++){
                  head += '<th>'+result.date[a].week_name+'</th>';
                  jj.push(result.date[a].week_name);
                }
                head += '</tr>';
                $('#data-activity-head-weekly').append(head);

                var dds = [];
                var dd = "";
                var activity = [];
                var activity_length = 0;
                $.each(result.detail[1], function(index, value){

                  for (var i = 0; i < result.detail[1].length; i++) {
                    if(i == 0){
                      activity.push(result.detail[1][index].activity_name);
                      activity_length++;
                    }else if(i > 0){
                      if(!activity.includes(result.detail[1][index].activity_name)){
                        activity.push(result.detail[1][index].activity_name);
                        activity_length++;
                      }
                    }
                    }
                })
                var nomer = 0;
                var aktual = 0;
                var total_aktual = 0;
                var plan = 1;
                var total_plan =  plan * activity.length;
                // console.table(result.detail[1][1]);
                for (var i = 0; i < activity.length; i++) {
                  dd += "<tr>";
                  dd += "<td>"+ (++nomer) +"</td>";
                  dd += "<td>"+activity[i]+"</td>";
                  
                  for (var j = 0; j < result.detail[1].length; j++) {
                    if(activity[i] == result.detail[1][j].activity_name){
                      for (var k = 1; k < result.detail.length; k++) {
                        for (var l = 0; l < result.detail[k].length; l++) {
                          if(result.detail[k][l].activity_name == activity[i]){
                            if(result.detail[k][l].jumlah_aktual > 0){
                              aktual = aktual + 1;
                              dd += "<td style='background-color: #4aff77'>1</td>";
                            }else{
                              dd += "<td style='background-color: #f7ff59'>0</td>";
                            }
                          }
                        }                      
                      }
                    }
                  }
                  dd += "<tr>";
                }
                total_aktual = aktual;
                total_plan_item = total_plan * 4;
                presentase = (total_aktual/total_plan_item)*100;
                dd += '<tr>';
                dd += '<td colspan="2"><b>Total Plan Activity</b></td>';
                dd += '<td colspan="5"><center><b>'+total_plan+'</b></center></td>';
                dd += '</tr>';
                dd += '<tr>';
                dd += '<td colspan="2"><b>Total Plan Item</b></td>';
                dd += '<td colspan="5"><center><b>'+total_plan_item+'</b></center></td>';
                dd += '</tr>';
                dd += '<tr>';
                dd += '<tr>';
                dd += '<td colspan="2"><b>Total Aktual</b></td>';
                dd += '<td colspan="5"><center><b>'+total_aktual+'</b></center></td>';
                dd += '</tr>';
                dd += '<tr>';
                dd += '<td colspan="2"><b>Presentase</b></td>';
                dd += '<td colspan="5"><center><b>'+parseInt(presentase)+'%</b></center></td>';
                dd += '</tr>';


                //    SAYA
                sd = [];
                // $.each(result.detail, function(index2, value2){
                //   for (var i = 0; i < result.detail[1]; i++) {
                //     if (result.detail[1][i] == 1) {
                //       sd.push([value2.activity_name]);
                //     }
                //   }
                // })

                // console.log(sd);

                // presentase = (total_aktual / total_plan)*100;
                // body += '<tr>';
                // body += '<td colspan="2"><b>Total</b></td>';
                // body += '<td><b>'+total_plan+'</b></td>';
                // body += '<td><b>'+total_aktual+'</b></td>';
                // body += '<td><b>'+parseInt(presentase)+'%</b></td>';
                // body += '<td></td>';
                // body += '</tr>';
                $('#data-activity-weekly').append(dd);

              }

            });
}
else if(frequency == 'Monthly'){
  $('#myModalMonthly').modal('show');
  $.get('{{ url("index/production_report/fetchDetailReportMonthly/".$id) }}', data, function(result, status, xhr) {
    if(result.status){

      $('#sub_judul_monthly').append('<b>'+frequency+' Report of '+leader_name+' on '+result.monthTitle+'</b>');
      $('#judul_monthly').append('<b>Leader Task Monitoring of '+leader_name+'</b>');

      var total_plan = 0;
      var total_aktual = 0;
      var presentase = 0;
      var body = '';
      var url = '{{ url("") }}';
      var no = 1;

      for (var i = 0; i < result.detail.length; i++) {
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+result.detail[i].activity_name+'</td>';
        body += '<td>'+result.detail[i].plan+'</td>';
        if(parseInt(result.detail[i].jumlah_aktual) > 0){
          body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_aktual+'</td>';
        }
        else{
          body += '<td style="background-color: #f7ff59"></td>';
        }
        body += '</tr>';
        total_plan += parseInt(result.detail[i].plan);
        total_aktual += parseInt(result.detail[i].jumlah_aktual);
        no++;
      }
      presentase = (total_aktual / total_plan)*100;
      body += '<tr>';
      body += '<td colspan="2"><b>Total</b></td>';
      body += '<td><b>'+total_plan+'</b></td>';
      body += '<td><b>'+total_aktual+'</b></td>';
      body += '</tr>';
      body += '<tr>';
      body += '<td colspan="2"><b>Presentase</b></td>';
      body += '<td><b>100%</b></td>';
      body += '<td><b>'+parseInt(presentase)+'%</b></td>';
      body += '</tr>';
      $('#data-activity-monthly').append(body);

    }

  });
}
else if(frequency == 'Daily'){
  $('#myModalDaily').modal('show');
  $.get('{{ url("index/production_report/fetchDetailReportDaily/".$id) }}', data, function(result, status, xhr) {
    if(result.status){

      $('#sub_judul_daily').append('<b>'+frequency+' Report of '+leader_name+' on '+result.monthTitle+'</b>');
      $('#judul_daily').append('<b>Leader Task Monitoring of '+leader_name+'</b>');

      var total_plan = 0;
      var presentase = 0;
      var body = '';
      var head = '';
      var jj = [];
      var act_type = [];
      var no = 1;
      var aa = 1;
      var bb = 0;
      var url = '{{ url("") }}';
      head += '<tr>';
      head += '<th rowspan="2" style="vertical-align: middle;">No.</th>';
      head += '<th rowspan="2" style="vertical-align: middle;">Date</th>';
      for(var a = 0; a < result.act_name.length; a++){
        if(result.act_name[a].activity_name != null){
          head += '<th>'+result.act_name[a].activity_name+'</th>';
          bb += 1;
          act_type.push(result.act_name[a].activity_name);
        }
      }
      // console.log(result.act_name);
      head += '</tr>';
      $('#data-activity-head-daily').append(head);
      // var qty = 0;
      var total_aktual = 0;
      for(var h = 0; h < result.date.length;h++){
        body += '<tr>';
        body += '<td>'+no+'</td>';
        body += '<td>'+result.date[h].week_date+'</td>';
        var steam_compressor = 0;
        for(var u = 0; u < result.act_name.length;u++){
          var bodys = '<td style="background-color: #f7ff59"></td>';
          for (var i = 0; i < result.detail.length; i++) {
            if (result.date[h].week_date == result.detail[i].week_date && result.act_name[u].activity_name == 'Cek Kondisi Safety Area Kerja')  {
              if (result.detail[i].jumlah_area_check != 0) {
                bodys = '<td style="background-color: #4aff77">'+result.detail[i].jumlah_area_check+'</td>';
                total_aktual += parseInt(result.detail[i].jumlah_area_check);
              }
            }

            if (result.date[h].week_date == result.detail[i].week_date && (result.act_name[u].activity_name == 'PENGECEKAN PACKING FINISH GOOD HARIAN' || result.act_name[u].activity_name == 'PENGECEKAN PACKING KD HARIAN' || result.act_name[u].activity_name == 'PENGECEKAN KD HARIAN'))  {
              if (result.detail[i].jumlah_daily_check != 0) {
                bodys = '<td style="background-color: #4aff77">'+result.detail[i].jumlah_daily_check+'</td>';
                total_aktual += parseInt(result.detail[i].jumlah_daily_check);
              }
            }

            if (result.date[h].week_date == result.detail[i].week_date && result.act_name[u].activity_name == 'Cek Alat Pelindung Diri (APD)')  {
              if (result.detail[i].jumlah_apd_check != 0) {
                bodys = '<td style="background-color: #4aff77">'+result.detail[i].jumlah_apd_check+'</td>';
                total_aktual += parseInt(result.detail[i].jumlah_apd_check);
              }
            }

            if (result.date[h].week_date == result.detail[i].week_date && result.act_name[u].activity_name == 'Cek Alat Pelindung Diri (APD) Cuci Asam')  {
              if (result.detail[i].jumlah_apd_check_cuci_asam != 0) {
                bodys = '<td style="background-color: #4aff77">'+result.detail[i].jumlah_apd_check_cuci_asam+'</td>';
                total_aktual += parseInt(result.detail[i].jumlah_apd_check_cuci_asam);
              }
            }

            if (result.date[h].week_date == result.detail[i].week_date && (result.act_name[u].activity_name == 'Audit Kanban Harian Leader'))  {
              if (result.detail[i].jumlah_audit_kanban != 0) {
                bodys = '<td style="background-color: #4aff77">'+result.detail[i].jumlah_audit_kanban+'</td>';
                total_aktual += parseInt(result.detail[i].jumlah_audit_kanban);
              }
            }
            var steam = 0;
            if (result.act_name[u].activity_name == 'AUDIT KEBOCORAN STEAM LINE')  {
              if (result.steam.length > 0) {
                for(var j = 0; j < result.steam.length;j++){
                  if (result.steam[j].date == result.date[h].week_date) {
                    steam = 1;
                    steam_compressor++;
                  }
                }
              }
            }

            if (steam != 0) {
              bodys = '<td style="background-color: #4aff77">1</td>';
              // total_aktual += parseInt(steam);
            }

            var compressor = 0;
            if (result.act_name[u].activity_name == 'AUDIT KEBOCORAN PIPING LINE COMPRESSOR')  {
              if (result.compressor.length > 0) {
                for(var j = 0; j < result.compressor.length;j++){
                  if (result.compressor[j].date == result.date[h].week_date) {
                    compressor = 1;
                    steam_compressor++;
                  }
                }
              }
            }

            if (compressor != 0) {
              bodys = '<td style="background-color: #4aff77">1</td>';
            }
          }
          body += bodys;
        }
        if (steam_compressor > 0) {
          total_aktual++;
        }
        // qty = qty + total_aktual;
        body += '</tr>';
        no++;
      }
      // total_aktual = qty;
      plan = parseInt(result.detail[0].plan) * parseInt(result.act_name.length);
      // for(var u = 0; u < result.act_name.length;u++){
      //   for (var i = 0; i < result.detail.length; i++) {
      //     // if (result.act_name[u].activity_name == 'Cek Kondisi Safety Area Kerja') {
      //     //   if(parseInt(result.detail[i].jumlah_area_check) != 0){
      //     //     if (result.date[i].week_date == result.detail[i].week_date) {
      //     //       body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_area_check+'</td>';
      //     //     }
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'PENGECEKAN PACKING FINISH GOOD HARIAN' || result.act_name[u].activity_name == 'PENGECEKAN PACKING KD HARIAN' || result.act_name[u].activity_name == 'PENGECEKAN KD HARIAN') {
      //     //   if(parseInt(result.detail[i].jumlah_daily_check) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_daily_check+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'Cek Alat Pelindung Diri (APD)') {
      //     //   if(parseInt(result.detail[i].jumlah_apd_check) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_apd_check+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'Cek Alat Pelindung Diri (APD) Cuci Asam') {
      //     //   if(parseInt(result.detail[i].jumlah_apd_check_cuci_asam) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_apd_check_cuci_asam+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'PENGECEKAN PACKING FINISH GOOD HARIAN' || result.act_name[u].activity_name == 'PENGECEKAN PACKING KD HARIAN') {
      //     //   if(parseInt(result.detail[i].jumlah_daily_check) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_daily_check+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'Cek Kondisi Safety Area Kerja') {
      //     //   if(parseInt(result.detail[i].jumlah_area_check) != 0){
      //     //     if (result.date[i].week_date == result.detail[i].week_date) {
      //     //       body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_area_check+'</td>';
      //     //     }
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'Cek Alat Pelindung Diri (APD)') {
      //     //   if(parseInt(result.detail[i].jumlah_apd_check) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_apd_check+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'Cek Alat Pelindung Diri (APD) Cuci Asam') {
      //     //   if(parseInt(result.detail[i].jumlah_apd_check_cuci_asam) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_apd_check_cuci_asam+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }
      //     // if (result.act_name[u].activity_name == 'Audit Kanban Harian Leader') {
      //     //   if(parseInt(result.detail[i].jumlah_audit_kanban) != 0){
      //     //     body += '<td style="background-color: #4aff77">'+result.detail[i].jumlah_audit_kanban+'</td>';
      //     //   }else{
      //     //     body += '<td style="background-color: #f7ff59"></td>';
      //     //   }
      //     // }

      //     // var compressor= 0;


      //     //   if (result.act_name[u].activity_name == 'AUDIT KEBOCORAN PIPING LINE COMPRESSOR') {
      //     //     if (result.compressor.length > 0) {
      //     //     for(var j = 0; j < result.compressor.length;j++){
      //     //       if (result.compressor[j].date == result.date[i].week_date) {
      //     //         compressor = 1;
      //     //       }
      //     //     }

      //     //     if (compressor == 0) {
      //     //       body += '<td style="background-color: #f7ff59"></td>';
      //     //     }else{
      //     //       body += '<td style="background-color: #4aff77">1</td>';
      //     //     }
      //     //   }
      //     //   }

      //     // var steam= 0;

      //     //   if (result.act_name[u].activity_name == 'AUDIT KEBOCORAN STEAM LINE') {
      //     //     if (result.steam.length > 0) {
      //     //     for(var j = 0; j < result.steam.length;j++){
      //     //       if (result.steam[j].date == result.date[i].week_date) {
      //     //         steam = 1;
      //     //       }
      //     //     }

      //     //     if (steam == 0) {
      //     //       body += '<td style="background-color: #f7ff59"></td>';
      //     //     }else{
      //     //       body += '<td style="background-color: #4aff77">1</td>';
      //     //     }
      //     //   }
      //     //   }
      //     // total_plan += parseInt(result.detail[i].plan) * bb;
      //     // total_aktual += (parseInt(result.detail[i].jumlah_daily_check) + parseInt(result.detail[i].jumlah_area_check) + parseInt(result.detail[i].jumlah_apd_check) + parseInt(result.detail[i].jumlah_apd_check_cuci_asam)+ parseInt(result.detail[i].jumlah_audit_kanban))+compressor+steam;
      //     // plan = result.detail[i].plan * bb;
      //   }
      // }
      presentase = (total_aktual / plan)*100;
      body += '<tr>';
      body += '<td colspan="2"><b>Total Aktual</b></td>';
      body += '<td colspan="'+result.act_name.length+'"><b>'+total_aktual+'</b></td>';
      // body += '<td></td>';
      body += '</tr>';
      body += '<tr>';
      body += '<td colspan="2"><b>Total Plan</b></td>';
      body += '<td colspan="'+result.act_name.length+'"><b>'+plan+'</b></td>';
      // body += '<td></td>';
      body += '</tr>';
      body += '<tr>';
      body += '<td colspan="2"><b>Presentase</b></td>';
      body += '<td colspan="'+result.act_name.length+'"><b>'+parseInt(presentase)+'%</b></td>';
      // body += '<td></td>';
      body += '</tr>';
      $('#data-activity-daily').append(body);

    }

  });
}
}

function ShowModalChartPrev(leader_name,frequency) {
  $('#myModal').modal('show');
  var week_date = $('#week_date').val();
  var data = {
    leader_name:leader_name,
    week_date:week_date
  }
  $('#data-activity').append().empty();
  $('#judul').append().empty();
  $('#leader_name').append().empty();
  $.get('{{ url("index/production_report/fetchDetailReportPrev/".$id) }}', data, function(result, status, xhr) {
    if(result.status){
              // console.log(result.detail);

              $('#judul').append('<b>Previous Month Report of '+leader_name+' on '+result.monthTitle+'</b>');
              $('#leader_name').append('<b>'+leader_name+'</b>');

              //Middle log
              var total_plan = 0;
              var total_aktual = 0;
              var presentase = 0;
              var body = '';
              var url = '{{ url("") }}';
              for (var i = 0; i < result.detail.length; i++) {
                body += '<tr>';
                body += '<td>'+result.detail[i].activity_name+'</td>';
                body += '<td>'+result.detail[i].activity_type+'</td>';
                body += '<td>'+result.detail[i].plan+'</td>';
                body += '<td>'+parseInt(result.detail[i].jumlah_aktual)+'</td>';
                body += '<td>'+parseInt(result.detail[i].persen)+' %</td>';
                body += '<td><a target="_blank" class="btn btn-primary btn-sm" href="'+url+'/'+result.detail[i].link+'">Details</a></td>';
                // body += '<td>'+result.good[i].quantity+'</td>';
                body += '</tr>';

                total_plan += parseInt(result.detail[i].plan);
                total_aktual += parseInt(result.detail[i].jumlah_aktual);
              }
              presentase = (total_aktual / total_plan)*100;
              body += '<tr>';
              body += '<td colspan="2"><b>Total</b></td>';
              body += '<td><b>'+total_plan+'</b></td>';
              body += '<td><b>'+total_aktual+'</b></td>';
              body += '<td><b>'+parseInt(presentase)+'%</b></td>';
              body += '<td></td>';
              body += '</tr>';
              $('#data-activity').append(body);

            }

          });
}

function ShowModalDetails(leader_name,frequency) {
  $('#myModal3').modal('show');
  var week_date = $('#week_date').val();
  var data = {
    leader_name:leader_name,
    frequency:frequency,
    week_date:week_date
  }
  $('#judul').append().empty();
  $.get('{{ url("index/production_report/fetchDetailReportPrev/".$id) }}', data, function(result, status, xhr) {
    if(result.status){

      $('#judul').append('<b>Previous Month Report of '+leader_name+' on '+result.monthTitle+'</b>');

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
            // itemStyle: {
            //   color: '#E0E0E3'
            // },
            // itemHoverStyle: {
            //   color: '#FFF'
            // },
            // itemHiddenStyle: {
            //   color: '#606063'
            // }
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
      }
    </script>