@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    input {
        line-height: 22px;
    }
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
        vertical-align: middle;
    }

    table.table-bordered > thead > tr > th{
        border:1px solid rgb(54, 59, 56) !important;
        text-align: center;
        background-color: #f0f0ff;  
        color:black;
    }

    table.table-bordered > tbody > tr > td{
        border-collapse: collapse !important;
        border:1px solid rgb(54, 59, 56)!important;
        background-color: #f0f0ff;
        color: black;
        vertical-align: middle;
        text-align: center;
        padding:3px;
    }

    table.table-bordered > tfoot > tr > th{
        border:1px solid black;
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: white; top: 45%; left: 50%;">
            <span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>

    <div class="row">
        <div class="col-xs-12" style="padding-bottom: 5px;">
            <div class="row">
                <div class="col-xs-2" style="padding-right: 0;">
                    <div class="input-group date">
                        <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From">
                    </div>
                </div>
                <div class="col-xs-2" style="padding-right: 0;">
                    <div class="input-group date">
                        <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" id="tanggal_to" name="tanggal_to" placeholder="Select Date To">
                    </div>
                </div>

                <div class="col-xs-2">
                    <button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
                </div>
                <div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">

                <div class="col-xs-6">
                    <div id="container1" class="container1" style="width: 100%;"></div>
                </div>
                <div class="col-xs-6">
                    <div id="container2" class="container2" style="width: 100%;"></div>
                </div>

                <div class="col-xs-12" style="padding-top: 10px;" id="resume_pics">
                </div>
            </div>
        </div>


        <div class="col-md-12" style="margin-top: 5px;">
            <center><span style="font-weight: bold; color: white; font-size: 2.0vw;">GS JOBLIST</span></center>

            <table id="tableFinishs" class="table table-bordered" style="margin-top: 5px; width: 100%">
              <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
                <tr>
                  <th style="width: 5%; padding: 5;vertical-align: middle;font-size: 16px;color: black;background-color: #f57f17">No</th>
                  <th style="width: 12%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;color: black; background-color: #f57f17">Nama</th>
                  <th style="width: 7%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;color: black; background-color: #f57f17">Lokasi</th>
                  <th style="width: 15%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;color: black; background-color: #f57f17">Pekerjaan</th>
                  <th style="width: 7%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;color: black; background-color: #f57f17">Start</th>
                  <th style="width: 7%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;color: black; background-color: #f57f17">Finish</th>
                  <th style="width: 5%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;color: black; background-color: #f57f17">Load Time</th>
              </tr>
          </thead>
          <tbody id="bodyFinishs">
          </tbody>
          <tfoot>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tfoot>
    </table>
</div>

</div>
</section>



<div class="modal fade" id="modalDetail" style="z-index: 10000;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #34c3eb;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold; color: white" id="modalDetailTitle"></h1>
                </div>
            </div>
            <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                <center>
                    <i class="fa fa-spinner fa-spin" id="loading2" style="font-size: 80px;"></i>
                </center>
                <div class="col-xs-12">
                    <table class="table table-bordered table-striped table-hover" id="DetailWr">
                        <thead style="background-color: rgba(252, 215, 3) !important;">
                            <tr>

                             <th>#</th>
                             <th>Nama</th>
                             <th>Lokasi</th>
                             <th>Pekerjaan</th>
                             <th>Start</th>
                             <th>Finish</th>
                             <th>Load Time</th>
                         </tr>
                     </thead>
                     <tbody id="modalDetailBody">

                     </tbody>
                     <tfoot style="background-color: rgba(126,86,134,.7);">
                        <tr>
                            <th colspan="4" style="color: white">TOTAL</th>
                            <th colspan="3"style="text-align: right;color: white" id="modalDetailTotal4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>
</div>


<div class="modal modal-default fade" id="modalFinish">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="background-color: #BA55D3; text-align: center; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                    FOTO PEKERJAAN
                </h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Before</label>
                                : <div name="img_foto_before" id="img_foto_before"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label >Foto After</label>
                                : <div name="img_foto_after" id="img_foto_after"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalImage">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-group">
                    <div  name="image_show" id="image_show"></div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var translations = [];
    var temps = [];

    var pics = <?php echo json_encode($pics); ?>;

    jQuery(document).ready(function(){
        $('#tanggal_from').datepicker({
            <?php $tgl_max = date('Y-m-d') ?>
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,   
            endDate: '<?php echo $tgl_max ?>'
        });
        $('#tanggal_to').datepicker({
            <?php $tgl_max = date('Y-m-d') ?>
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,   
            endDate: '<?php echo $tgl_max ?>'
        });
        $('.select2').select2({
            allowClear:true
        });
        fetchChart();
        // setInterval(fetchChart, 300000);
    });

    




    function fetchChart(){
        $('#loading').show();

        var dateFrom = $('#tanggal_from').val();
        var dateTo = $('#tanggal_to').val();

        var data = {
            date_from:dateFrom,
            date_to:dateTo
        }

        $.get('{{ url("fetch/resume/gs") }}', data, function(result, status, xhr) {
            if(result.status){

                var array = result.translations;
                temps = result.dataworkall;

                translations = result.translations;

                var weekly_calendars = result.weekly_calendars;
                var dates1 = result.dateTitleFirst;
                var dates2 = result.dateTitleLast;
                var result = [];
                var picCategories = [];
                var picCategories2 = [];
                var series_translation = [];
                var series_tot_jobs= [];
                var daily_pics = [];
                var dateCategories = [];
                var result_pic = [];
                var resume_pics = "";
                $('#resume_pics').html("");

                array.reduce(function(res, value) {
                    if(value.status == 2){
                        if (!res[value.nik_gs]) {
                            res[value.nik_gs] = { nik_gs: value.nik_gs, load_translation: 0, tot_jobs: 0 };
                            result.push(res[value.nik_gs])
                        }
                        
                        var duration_total = parseFloat(parseFloat(value.times).toFixed(2)/60);
                        for(var i = 0; i < temps.length;i++){
                            var dataone = temps[i].split("+");
                            if(value.id == dataone[0]){
                                duration_total = duration_total - parseFloat(dataone[5]);
                            }
                        }

                        if(value.finished_at != null){
                            res[value.nik_gs].load_translation += parseFloat(parseFloat(duration_total).toFixed(2));
                            res[value.nik_gs].tot_jobs += value.jum;
                        }
                    }
                    return res;
                }, {});

                array.reduce(function(res, value) {
                   var pic_workload = 0;
                   var pic_workload2 = 0;
                   if(value.status == 2){
                    if (!res[value.nik_gs+value.date_finish]) {
                        res[value.nik_gs+value.date_finish] = { nik_gs: value.nik_gs, finished_at: value.date_finish, load_workload: 0, load_job: 0};
                        result_pic.push(res[value.nik_gs+value.date_finish])
                    }

                    if(value.finished_at != null){

                       var duration_total = parseFloat(parseFloat(value.times).toFixed(2)/60);
                       for(var i = 0; i < temps.length;i++){
                        var dataone = temps[i].split("+");
                        if(value.list_job == dataone[1]  && value.id == dataone[0]){
                            duration_total = duration_total - parseFloat(dataone[5]);
                        }
                    }

                    pic_workload = parseFloat(parseFloat(duration_total).toFixed(2));
                    pic_workload2 = parseFloat(parseFloat(pic_workload).toFixed(2));
                    res[value.nik_gs+value.date_finish].load_workload +=  pic_workload2;
                    res[value.nik_gs+value.date_finish].load_job +=  value.jum;

                }
            }
            return res;
        },{});

                $.each(pics, function(key, value){
                    var translation = 0;
                    var tot_job = 0;

                    for(var i = 0; i < result.length; i++){
                        if(result[i].nik_gs == value.employee_id){
                            translation = result[i].load_translation;
                            tot_job = result[i].tot_jobs;
                        }
                    }


                    translationk = parseFloat(parseFloat(translation).toFixed(2));
                    picCategories.push(value.employee_name.split(' ').slice(0,2).join(' ')); 
                    picCategories2.push(value.employee_name.split(' ').slice(0,2).join(' ')); 

                    series_translation.push(translationk);
                    series_tot_jobs.push(tot_job);
                    resume_pics = '<div style="height: 40vh;" id="container_'+value.employee_id+'">'+value.employee_id+'</div>';
                    $('#resume_pics').append(resume_pics);

                    for(var j = 0; j < weekly_calendars.length; j++){
                        daily_pics.push({nik_gs: value.employee_id, finished_at: weekly_calendars[j].week_date}); 
                        if(jQuery.inArray(weekly_calendars[j].day_date, dateCategories) === -1) {
                            dateCategories.push(weekly_calendars[j].week_date);
                        }
                    }
                });


                // var seriesCategory = [];
                // var xscategories = [];

                // var area = ['Area GS 1','Area GS 2','Area GS 3','Lain-Lain'];
                // for(var i = 0; i < area.length;i++){
                //     var count = 0;
                //     for(var j = 0; j < translations.length;j++){
                //         var re = new RegExp(area[i], 'g');
                //         if(translations[j].category.match(re)){
                //             count++;
                //         }
                //     }
                //     seriesCategory.push(parseInt(count));
                //     xscategories.push(area[i]);
                // }


                var daily_result = [];

                $.each(daily_pics, function(key, value){
                    var daily_loads = 0;
                    var load_jobs_daily = 0;


                    for(var i = 0; i < result_pic.length; i++){
                        if(result_pic[i].nik_gs == value.nik_gs && result_pic[i].finished_at == value.finished_at){
                            daily_loads = result_pic[i].load_workload;
                            load_jobs_daily = result_pic[i].load_job;

                        }
                    }
                    daily_result.push({nik_gs: value.nik_gs, finished_at: value.finished_at, translation_load: daily_loads, job_load:load_jobs_daily});
                });

                Highcharts.chart('container1', {
                    chart: {
                        type: 'column',
                        height: '330',
                        backgroundColor: "rgba(0,0,0,0)"
                    },
                    title: {
                        text: 'Total Workload',
                        style: {
                            fontSize: '25px',
                            fontWeight: 'bold'
                        }
                    },
                    subtitle: {
                        text: 'on '+dates1+' - '+dates2,
                        style: {
                            fontSize: '1vw',
                            fontWeight: 'bold'
                        }
                    },
                    xAxis: {
                        categories: picCategories,
                        type: 'category',
                        gridLineWidth: 1,
                        gridLineColor: 'RGB(204,255,255)',
                        lineWidth:2,
                        lineColor:'#9e9e9e',
                        labels: {
                            style: {
                                fontSize: '14px',
                                fontWeight: 'bold'
                            }
                        },
                    },
                    yAxis: [{
                        title: {
                            text: 'Minutes',
                            style: {
                                color: '#eee',
                                fontSize: '14px',
                                fontWeight: 'bold',
                                fill: '#6d869f'
                            }
                        },
                        labels:{
                            style:{
                                fontSize:"14px"
                            }
                        },
                        type: 'linear',

                    }

                    ],
                    tooltip: {
                        headerFormat: '<span>Workload</span><br/>',
                        pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
                    },
                    legend: {
                        layout: 'horizontal',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -90,
                        y: 20,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
                        shadow: true,
                        itemStyle: {
                            fontSize:'16px',
                        },
                    },  
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        series:{
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function () {
                                        fillModal(this.category,result.date,'workload');
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}',
                                style:{
                                    fontSize: '1vw'
                                }
                            },
                            animation: {
                                enabled: true,
                                duration: 800
                            },
                            pointPadding: 0.93,
                            groupPadding: 0.93,
                            borderWidth: 0.93,
                            cursor: 'pointer'
                        },
                    },
                    series: [

                    {
                        type: 'column',
                        data: series_translation,
                        name: 'Workload',
                        colorByPoint: false,
                        color: '#3f51b5',
                        animation: false,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}' ,
                            style:{
                                fontSize: '1vw',
                                textShadow: false
                            },
                        },
                    }
                    ]
                });


Highcharts.chart('container2', {
    chart: {
        type: 'column',
        height: '330',
        backgroundColor: "rgba(0,0,0,0)"
    },
    title: {
        text: 'Total Job',
        style: {
            fontSize: '25px',
            fontWeight: 'bold'
        }
    },
    subtitle: {
        text: 'on '+dates1+' - '+dates2,
        style: {
            fontSize: '1vw',
            fontWeight: 'bold'
        }
    },
    xAxis: {
        categories: picCategories2,
        type: 'category',
        gridLineWidth: 1,
        gridLineColor: 'RGB(204,255,255)',
        lineWidth:2,
        lineColor:'#9e9e9e',
        labels: {
            style: {
                fontSize: '14px',
                fontWeight: 'bold'
            }
        },
    },
    yAxis: [{
        title: {
            text: 'Job',
            style: {
                color: '#eee',
                fontSize: '18px',
                fontWeight: 'bold',
                fill: '#6d869f'
            }
        },
        labels:{
            style:{
                fontSize:"14px"
            }
        },
        type: 'linear',
    }
    ],
    tooltip: {
        headerFormat: '<span>Workload</span><br/>',
        pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
    },
    legend: {
        layout: 'horizontal',
        align: 'right',
        verticalAlign: 'top',
        x: -90,
        y: 20,
        floating: true,
        borderWidth: 1,
        backgroundColor:
        Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
        shadow: true,
        itemStyle: {
            fontSize:'16px',
        },
    },  
    credits: {
        enabled: false
    },
    plotOptions: {
        series:{
            cursor: 'pointer',
            point: {
                events: {
                    click: function () {
                        fillModal(this.category,result.date,'categ');
                    }
                }
            },
            dataLabels: {
                enabled: true,
                format: '{point.y}',
                style:{
                    fontSize: '1vw'
                }
            },
            animation: {
                enabled: true,
                duration: 800
            },
            pointPadding: 0.93,
            groupPadding: 0.93,
            borderWidth: 0.93,
            cursor: 'pointer'
        },
    },
    series: [

    {
        type: 'column',
        data: series_tot_jobs,
        name: 'Total Job',
        colorByPoint: false,
        color: '#ffc44f',
        animation: false,
        dataLabels: {
            enabled: true,
            format: '{point.y}' ,
            style:{
                fontSize: '1vw',
                textShadow: false
            },
        },
    }
    ]
});

$.each(pics, function(key, value){
    var series_daily = [];
    var trend = [];
    var load_job1 = [];


    for(var i = 0; i < daily_result.length; i++){
        if(daily_result[i].nik_gs == value.employee_id){

            var coun = parseFloat(parseFloat(daily_result[i].translation_load).toFixed(2));
            series_daily.push(parseFloat(parseFloat(coun).toFixed(2)));
            trend.push(parseFloat(parseFloat(coun).toFixed(2)));
            load_job1.push(daily_result[i].job_load);
        }
    }


    Highcharts.chart('container_'+value.employee_id, {
        chart: {
            type: 'column',
            backgroundColor: "rgba(0,0,0,0)"
        },
        title: {
            text: '<b>'+value.employee_name+'</b>',
            style: {
                fontSize: '25px',
                fontWeight: 'bold'
            }
        },
        xAxis: {
            categories: dateCategories,
            type: 'category',
            gridLineWidth: 1,
            gridLineColor: 'RGB(204,255,255)',
            lineWidth:2,
            lineColor:'#9e9e9e',
            labels: {
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold'
                }
            },
        },
        yAxis: [{
            // lineWidth:2,
            lineColor:'#fff',
            type: 'linear',
            title: {
                text: 'Minutes'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
            },
            plotLines: [{
                color: '#FF0000',
                value: 480,
                dashStyle: 'shortdash',
                width: 2,
                zIndex: 5,
                label: {
                    align:'right',
                    text: 'Target 480 Minutes',
                    x:-7,
                    style: {
                        fontSize: '13px',
                        color: '#FF0000',
                        fontWeight: 'bold'
                    }
                }
            }],
        },{ // Secondary yAxis
            title: {
                text: 'Total Job',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        tooltip: {
            headerFormat: '<span>Detail</span><br/>',
            pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
        },
        legend: {
            layout: 'horizontal',
            align: 'right',
            verticalAlign: 'top',
            x: -10,
            y: 7,
            floating: true,
            borderWidth: 1,
            backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
            shadow: true,
            itemStyle: {
                fontSize:'16px',
            },
        },  
        credits: {
            enabled: false
        },
        plotOptions: {
            series:{
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            fillModal(this.category,value.employee_name,'operators');
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.y}',
                    style:{
                        fontSize: '1vw'
                    }
                },
                animation: {
                    enabled: true,
                    duration: 800
                },
                pointPadding: 0.93,
                groupPadding: 0.93,
                borderWidth: 0.93,
                cursor: 'pointer'
            },
        },
        series: [
        {
            type: 'column',
            data: series_daily,
            name: 'Total Workload',    
            colorByPoint: false,
            color: '#8a3fb5',
            animation: false,
            dataLabels: {
                enabled: true,
                format: '{point.y}',
                style:{
                    fontSize: '1vw',
                    textShadow: false
                },
            },
            stack:'GG'

        },
        
        {
            type: 'line',
            data: trend,
            name: "Trendline Total Workload",
            colorByPoint: false,
            color: "#a0c013",
            animation: false,
            dashStyle:'shortdash',
            lineWidth: 2,
            marker: {
                radius: 4,
                lineColor: '#fff',
                lineWidth: 1
            },
        },
        
        {
                        type: 'spline',
                        data: load_job1,
                        name: 'Total Job',
                        colorByPoint: false,
                        color:'#fff',
                        yAxis:1,
                        animation: false,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}' ,
                            style:{
                                fontSize: '1vw',
                                textShadow: false
                            },
                        },
                        
                    }


        ]
    });

});
fetchTable();




$('#loading').hide();
}
else{
    $('#loading').hide();
    openErrorGritter('Error!',result.message);
}

});

}


function fetchTable(){
    var tableGSBody = "";
    $('#bodyFinishs').html("");
    $('#tableFinishs').DataTable().clear();
    $('#tableFinishs').DataTable().destroy();
    var cnt_translation = 0;
    var cnt_time = 0;
    var cnt_time2 = 0;



    $.each(translations, function(key, value){
        cnt_translation += 1;
        var duration_total = parseFloat(parseFloat(value.times).toFixed(2)/60);
        for(var i = 0; i < temps.length;i++){
            var dataone = temps[i].split("+");
            if(value.list_job == dataone[1] && value.id == dataone[0]){
                duration_total = duration_total - parseFloat(dataone[5]);
            }
        }
        cnt_time2 = parseFloat(parseFloat(duration_total).toFixed(2));
        tableGSBody += '<tr onclick= "modalFinishImg(\''+value.img_before+'\',\''+value.img_after+'\')" style="cursor:pointer">';
        tableGSBody += '<td style="width: 0.1%; text-align: center;">'+cnt_translation+'</td>';
        tableGSBody += '<td style="width: 0.4%; text-align: left;">'+value.name_gs+'</td>';
        tableGSBody += '<td style="width: 0.2%; text-align: left;">'+value.category+'</td>';
        tableGSBody += '<td style="width: 0.6%; text-align: left;">'+value.list_job+'</td>';
        tableGSBody += '<td style="width: 0.1%; text-align: left;">'+value.request_at+'</td>';
        tableGSBody += '<td style="width: 0.1%; text-align: right;">'+value.finished_at+'</td>';
        tableGSBody += '<td style="width: 0.1%; text-align: right;">'+cnt_time2+'</td>';
        tableGSBody += '</tr>';
    });
    $('#bodyFinishs').append(tableGSBody);
    $('#tableFinishs tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
    } );

    var table = $('#tableFinishs').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 15, 25, -1 ],
        [ '15 rows', '25 rows', 'Show all' ]
        ],
        'buttons': {
            buttons:[
            {
                extend: 'pageLength',
                className: 'btn btn-default',
            },
            {
                extend: 'excel',
                className: 'btn btn-info',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                exportOptions: {
                    columns: ':not(.notexport)'
                }
            }
            ]
        },
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        'ordering' :false,
        "order": [[ 0, 'asc' ]],
        initComplete: function() {
            this.api()
            .columns([1, 2])
            .every(function(dd) {
                var column = this;
                var theadname = $("#tableFinishs th").eq([dd]).text();
                var select = $(
                    '<select><option value="" style="font-size:11px;">All</option></select>'
                    )
                .appendTo($(column.footer()).empty())
                .on('change', function() {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column.search(val ? '^' + val + '$' : '', true, false)
                    .draw();
                });
                column
                .data()
                .unique()
                .sort()
                .each(function(d, j) {
                    var vals = d;
                    if ($("#tableFinishs th").eq([dd]).text() == 'Category') {
                        vals = d.split(' ')[0];
                    }
                    select.append('<option style="font-size:11px;" value="' +
                        d + '">' + vals + '</option>');
                });
            });
        },
    });

    table.columns().every( function () {
        var that = this;
        $( '#search', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                .search( this.value )
                .draw();
            }
        } );
    } );



    // $('#tableResume tfoot tr').appendTo('#tableResume thead');

}


function modalFinishImg(before,after){
    var images_gs = "";
    var images_gs_after = "";
    $("#img_foto_after").html("");
    $("#img_foto_before").html("");
    $('#img_foto_before').show();
    $('#img_foto_after').show();

    if (before.length == 4) {
        $('#img_foto_before').hide();
    }else{
        images_gs += '<img src="{{ url("images/ga/gs_control") }}/'+before+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+before+'\')">';
        $("#img_foto_before").append(images_gs);
    }

    if (after.length == 4) {
        $('#img_foto_after').hide();
    }else{
        images_gs_after += '<img src="{{ url("images/ga/gs_control") }}/'+after+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+after+'\')">';
        $("#img_foto_after").append(images_gs_after);
    }
    $('#modalFinish').modal('show');

}

function showImage(imgs) {
    $('#modalImage').modal('show');
    var images_show = "";
    $("#image_show").html("");
    images_show += '<img style="cursor:zoom-in" src="{{ url("images/ga/gs_control") }}/'+imgs+'" width="100%" >';
    $("#image_show").append(images_show);
}



function fillModal(cat, name,st){

   $('#loading2').show();
   $('#DetailWr').hide();
   $('#modalDetailBody').html('');
   $('#DetailWr').DataTable().clear();
   $('#DetailWr').DataTable().destroy();
   var resultData = '';
   var no = 1;
   var nos = 1;

   var resultTotal4 = 0;
   var cnt_times = 0;
   var cnt_times2 = 0;
   var resultText = "";
   if (st == "workload") {
    resultText = 'Detail Total Workload GS';          
}else if (st == "categ") {
    resultText = 'Detail Total Category';          
}
else{
    resultText = 'Detail Total Workload GS';          
}


$.each(translations, function(key, value) {

  var duration_total = parseFloat(parseFloat(value.times).toFixed(2)/60);
  for(var i = 0; i < temps.length;i++){
    var dataone = temps[i].split("+");
    if(value.list_job == dataone[1] && value.id == dataone[0]){
        duration_total = duration_total - parseFloat(dataone[5]);
    }
}

cnt_times2 = parseFloat(parseFloat(duration_total).toFixed(2));

var names = value.name_gs.split(' ').slice(0,2).join(' ');


if (st == "workload") {

   if(names == cat){
    resultData += '<tr style="background-color: #fcba03;">';
    resultData += '<td style="width: 1%">'+ no +'</td>';
    resultData += '<td style="width: 5%">'+ value.name_gs +'</td>';
    resultData += '<td style="width: 1%">'+ value.category +'</td>';
    resultData += '<td style="width: 5%">'+ value.list_job +'</td>';
    resultData += '<td style="width: 2%">'+ value.request_at +'</td>';
    resultData += '<td style="width: 2%">'+ value.finished_at +'</td>';
    resultData += '<td style="width: 1%; font-weight: bold;">'+ cnt_times2 +'</td>';
    resultData += '</tr>';
    resultTotal4 += Math.abs(cnt_times2);
    no++;
}
}else if (st == "categ") {
    if(names == cat){
        resultData += '<tr style="background-color: rgb(204, 255, 255);">';
        resultData += '<td style="width: 1%">'+ nos +'</td>';
        resultData += '<td style="width: 5%">'+ value.name_gs +'</td>';
        resultData += '<td style="width: 1%">'+ value.category +'</td>';
        resultData += '<td style="width: 5%">'+ value.list_job +'</td>';
        resultData += '<td style="width: 2%">'+ value.request_at +'</td>';
        resultData += '<td style="width: 2%">'+ value.finished_at +'</td>';
        resultData += '<td style="width: 1%;font-weight: bold;">'+ cnt_times2 +'</td>';
        resultData += '</tr>';
        resultTotal4++;        
        nos++;
    }

}else{
    if(value.name_gs == name && value.date_finish == cat){
        resultData += '<tr style="background-color: rgb(204, 255, 255);">';
        resultData += '<td style="width: 1%">'+ nos +'</td>';
        resultData += '<td style="width: 5%">'+ value.name_gs +'</td>';
        resultData += '<td style="width: 1%">'+ value.category +'</td>';
        resultData += '<td style="width: 5%">'+ value.list_job +'</td>';
        resultData += '<td style="width: 2%">'+ value.request_at +'</td>';
        resultData += '<td style="width: 2%">'+ value.finished_at +'</td>';
        resultData += '<td style="width: 1%; font-weight: bold;">'+ cnt_times2 +'</td>';
        resultData += '</tr>';
        resultTotal4 += Math.abs(cnt_times2);     
        nos++;
    }
}

});
$('#modalDetailBody').append(resultData);

$('#modalDetailTotal4').html('');
$('#modalDetailTotal4').append(resultTotal4.toLocaleString());

$('#loading2').hide();
$('#DetailWr').show();

$('#modalDetailTitle').html(resultText);

$('#DetailWr').DataTable( {
    'dom': 'Bfrtip',
    'responsive':true,
    'lengthMenu': [
    [ 20, 50, -1 ],
    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
    ],
    'buttons': {
      buttons:[
      {
        extend: 'pageLength',
        className: 'btn btn-default',
    },

    {
        extend: 'excel',
        className: 'btn btn-info',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        exportOptions: {
          columns: ':not(.notexport)'
      }
  },
  ]
},
'paging': true,
'lengthChange': true,
'searching': true,
'ordering': true,
'info': true,
'autoWidth': true,
"sPaginationType": "full_numbers",
"bJQueryUI": true,
"bAutoWidth": false,
"processing": false,
"order": [[ 0, 'asc' ]]

} );
$('#modalDetail').modal('show');


}



$.date = function(dateObject) {
    var d = new Date(dateObject);
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    if (day < 10) {
        day = "0" + day;
    }
    if (month < 10) {
        month = "0" + month;
    }
    var date = year + "-" + month + "-" + day;

    return date;
};

function changeLocation(){
    $("#location").val($("#locationSelect").val());
}


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

Highcharts.createElement('link', {
        href: '{{ url("fonts/UnicaOne.css")}}',
        rel: 'stylesheet',
        type: 'text/css'
    }, null, document.getElementsByTagName('head')[0]);

    Highcharts.theme = {
        colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
        '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
        chart: {
            backgroundColor: null,
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

</script>
@endsection