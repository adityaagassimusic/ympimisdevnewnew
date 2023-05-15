@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    table>tbody>tr:hover {
        /*cursor: pointer;*/
        background-color: #7dfa8c !important;
    }
    table{
        border:1px solid black !important;
    }
    thead>tr>th{
        vertical-align: middle !important;
        /*border:1px solid black !important;*/
    }
    tbody>tr>td{
        /*border:1px solid black !important;*/
    }
    #loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
    <h1>
        {{ $title }}
        <small><span class="text-purple">{{ $title_jp }}</span></small>
    </h1>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="font-size: 0.8vw;">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-2">
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: yellow; font-weight: bold;"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control datepicker" id="dateFrom" placeholder="Date From">
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: yellow; font-weight: bold;"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control datepicker" id="dateTo" placeholder="Date To">
                    </div>
                </div>
                <div class="col-xs-2">
                    <button class="btn btn-success" onclick="fetchResume();">Search</button>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div id="container1" style="height: 40vh;"></div>
        </div>
        <div class="col-xs-6">
            <div id="container2" style="height: 40vh;"></div>
        </div>
        <div class="col-xs-12" style="padding-top: 10px;" id="resume_pics">
        </div>
        <div class="col-xs-12" style="padding-top: 10px;">
            <center><span style="font-weight: bold; font-size: 1.2vw;">Translation List (あ <i class="fa fa-exchange"></i> A)</span></center>
            <table id="tableTranslation" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #605ca8; color: white;">
                    <tr>
                        <th style="width: 0.1%; text-align: center;">#</th>
                        <th style="width: 0.1%; text-align: left;">ID</th>
                        <th style="width: 0.1%; text-align: left;">Title</th>
                        <th style="width: 0.1%; text-align: left;">Requested By</th>
                        <th style="width: 0.1%; text-align: left;">Dept</th>
                        <th style="width: 0.1%; text-align: right;">Due Date</th>
                        <th style="width: 0.1%; text-align: right;">Finished At</th>
                        <th style="width: 0.1%; text-align: right;">Load Time</th>
                        <th style="width: 0.1%; text-align: left;">PIC ID</th>
                        <th style="width: 0.1%; text-align: left;">PIC Name</th>
                    </tr>
                </thead>
                <tbody id="tableTranslationBody">
                </tbody>
            </table>
        </div>
        <div class="col-xs-12">
            <center><span style="font-weight: bold; font-size: 1.2vw;">Meeting List (<i class="fa fa-users"></i>)</span></center>
            <table id="tableMeeting" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #90ed7d; color: black;">
                    <tr>
                        <th style="width: 0.1%; text-align: center;">#</th>
                        <th style="width: 0.1%; text-align: left;">ID</th>
                        <th style="width: 0.1%; text-align: left;">Title</th>
                        <th style="width: 0.1%; text-align: left;">Dept</th>
                        <th style="width: 0.1%; text-align: right;">Date</th>
                        <th style="width: 0.1%; text-align: right;">From</th>
                        <th style="width: 0.1%; text-align: right;">To</th>
                        <th style="width: 0.1%; text-align: right;">Duration</th>
                        <th style="width: 0.1%; text-align: left;">PIC ID</th>
                        <th style="width: 0.1%; text-align: left;">PIC Name</th>
                    </tr>
                </thead>
                <tbody id="tableMeetingBody">
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
    var no = 2;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery(document).ready(function() {
        $('#dateFrom').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        $('#dateTo').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        // fetchResume();
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
    var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
    var pics = <?php echo json_encode($pics); ?>;
    var departments = <?php echo json_encode($departments); ?>;
    var translations = [];

    function fetchTable(){
        var tableMeetingBody = "";
        var tableTranslationBody = "";
        $('#tableMeetingBody').html("");
        $('#tableTranslationBody').html("");
        $('#tableTranslation').DataTable().clear();
        $('#tableTranslation').DataTable().destroy();
        $('#tableMeeting').DataTable().clear();
        $('#tableMeeting').DataTable().destroy();
        var cnt_translation = 0;
        var cnt_meeting = 0;

        $.each(translations, function(key, value){
            if(value.category == 'translation'){
                cnt_translation += 1;
                tableTranslationBody += '<tr>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: center;">'+cnt_translation+'</td>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: left;">'+value.translation_id+'</td>';
                tableTranslationBody += '<td style="width: 0.7%; text-align: left;">'+value.title+'</td>';
                tableTranslationBody += '<td style="width: 0.6%; text-align: left;">'+value.requester_name+'</td>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: right;">'+value.request_date+'</td>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: right;">'+value.finished_at+'</td>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: right;">'+value.load_time+'</td>';
                tableTranslationBody += '<td style="width: 0.1%; text-align: left;">'+value.pic_id+'</td>';
                tableTranslationBody += '<td style="width: 0.6%; text-align: left;">'+value.pic_name+'</td>';
                tableTranslationBody += '</tr>';
            }
            if(value.category == 'meeting'){
                cnt_meeting += 1;

                tableMeetingBody += '<tr>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: center;">'+cnt_meeting+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: left;">'+value.translation_id+'</td>';
                tableMeetingBody += '<td style="width: 0.7%; text-align: left;">'+value.document_type+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: left;">'+value.department_shortname+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: right;">'+value.request_date+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: right;">'+value.request_time_from+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: right;">'+value.request_time_to+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: right;">'+value.load_time+'</td>';
                tableMeetingBody += '<td style="width: 0.1%; text-align: left;">'+value.pic_id+'</td>';
                tableMeetingBody += '<td style="width: 0.6%; text-align: left;">'+value.pic_name+'</td>';
                tableMeetingBody += '</tr>';
            }
        });
        $('#tableMeetingBody').append(tableMeetingBody);
        $('#tableTranslationBody').append(tableTranslationBody);

        $('#tableTranslation').DataTable({
            'dom': 'Bfrtip',
            'responsive':true,
            'lengthMenu': [
            [ 10, 25, 50, -1 ],
            [ '25 rows', '50 rows', 'Show all' ]
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
            'searching': true,
            'ordering': false,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
        });

        $('#tableMeeting').DataTable({
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
            'searching': true,
            'ordering': false,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
        });
    }

    function fetchResume(){
        $('#loading').show();
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        if(dateFrom == "" || dateTo == ""){
            openErrorGritter('Error', 'All date must be filled.');
            $('#loading').hide();
            return false;
        }
        var cat = 'resume';
        var data = {
            cat:cat,
            date_from:dateFrom,
            date_to:dateTo
        }
        $.get('{{ url("fetch/translation") }}', data, function(result, status, xhr){
            if(result.status){
                var weekly_calendars = result.weekly_calendars;
                var array = result.translations;
                translations = result.translations;
                var result = [];
                var result_pic = [];

                array.reduce(function(res, value) {
                    if(value.status = 'Finished'){
                        if (!res[value.pic_id]) {
                            res[value.pic_id] = { pic_id: value.pic_id, load_translation: 0, load_meeting: 0 };
                            result.push(res[value.pic_id])
                        }
                        if(value.category == 'translation'){
                            res[value.pic_id].load_translation += value.load_time;
                        }
                        if(value.category == 'meeting'){
                            res[value.pic_id].load_meeting += value.load_time;                        
                        }
                    }
                    return res;
                }, {});

                array.reduce(function(res, value) {
                    if(value.status = 'Finished'){
                        if (!res[value.pic_id+value.finished_at]) {
                            res[value.pic_id+value.finished_at] = { pic_id: value.pic_id, finished_at: value.finished_at, load_translation: 0, load_meeting: 0 };
                            result_pic.push(res[value.pic_id+value.finished_at])
                        }
                        if(value.category == 'translation'){
                            res[value.pic_id+value.finished_at].load_translation += value.load_time;
                        }
                        if(value.category == 'meeting'){
                            res[value.pic_id+value.finished_at].load_meeting += value.load_time;                        
                        }
                    }
                    return res;
                }, {});

                var picCategories = [];
                var series_meeting = [];
                var series_translation = [];
                var daily_pics = [];
                var dateCategories = [];
                var resume_pics = "";
                $('#resume_pics').html("");

                $.each(pics, function(key, value){
                    var meeting = 0;
                    var translation = 0;
                    for(var i = 0; i < result.length; i++){
                        if(result[i].pic_id == value.employee_id){
                            meeting = result[i].load_meeting;
                            translation = result[i].load_translation;
                        }
                    }
                    picCategories.push(value.employee_name); 
                    series_meeting.push(meeting);
                    series_translation.push(translation);
                    resume_pics = '<div style="height: 40vh;" id="container_'+value.employee_id+'">'+value.employee_id+'</div>';
                    $('#resume_pics').append(resume_pics);

                    for(var j = 0; j < weekly_calendars.length; j++){
                        daily_pics.push({pic_id: value.employee_id, finished_at: weekly_calendars[j].week_date}); 
                        if(jQuery.inArray(weekly_calendars[j].day_date, dateCategories) === -1) {
                            dateCategories.push(weekly_calendars[j].day_date);
                        }
                    }
                });

                var daily_result = [];

                $.each(daily_pics, function(key, value){
                    var daily_load_meeting = 0;
                    var daily_load_translation = 0;
                    for(var i = 0; i < result_pic.length; i++){
                        if(result_pic[i].pic_id == value.pic_id && result_pic[i].finished_at == value.finished_at){
                            daily_load_meeting = result_pic[i].load_meeting;
                            daily_load_translation = result_pic[i].load_translation;
                        }
                    }
                    daily_result.push({pic_id: value.pic_id, finished_at: value.finished_at, meeting_load: daily_load_meeting, translation_load: daily_load_translation});
                });


                $.each(pics, function(key, value){
                    var series_meeting_daily = [];
                    var series_translation_daily = [];

                    for(var i = 0; i < daily_result.length; i++){
                        if(daily_result[i].pic_id == value.employee_id){
                            series_meeting_daily.push(daily_result[i].meeting_load);
                            series_translation_daily.push(daily_result[i].translation_load);
                        }
                    }

                    Highcharts.chart('container_'+value.employee_id, {
                        chart: {
                            backgroundColor: null,
                            type: 'column'
                        },
                        title: {
                            text: '<b>'+value.employee_name+'</b>'
                        },
                        xAxis: {
                            categories: dateCategories
                        },
                        credits: {
                            enabled: false
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Minute(s)'
                            },
                            stackLabels: {
                                enabled: true
                            }
                        },
                        legend: {
                            align: 'right',
                            x: -30,
                            verticalAlign: 'top',
                            y: 25,
                            floating: true,
                            backgroundColor:
                            Highcharts.defaultOptions.legend.backgroundColor || null,
                            borderColor: null,
                            borderWidth: 1,
                            shadow: false
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Meeting',
                            data: series_meeting_daily,
                            color: '#90ed7d'
                        }, {
                            name: 'Translation',
                            data: series_translation_daily,
                            color: '#605ca8'
                        }]
                    });
                });

                var result2 = [];

                array.reduce(function(res, value) {
                    if (!res[value.department_shortname]) {
                        res[value.department_shortname] = { department_shortname: value.department_shortname, load_translation: 0, load_meeting: 0 };
                        result2.push(res[value.department_shortname])
                    }
                    if(value.category == 'translation'){
                        res[value.department_shortname].load_translation += value.load_time;
                    }
                    if(value.category == 'meeting'){
                        res[value.department_shortname].load_meeting += value.load_time;                        
                    }
                    return res;
                }, {});

                var deptCategories = [];
                var series_meeting_dept = [];
                var series_translation_dept = [];

                $.each(departments, function(key, value){
                    var meeting = 0;
                    var translation = 0;
                    for(var i = 0; i < result2.length; i++){
                        if(result2[i].department_shortname == value.department_shortname){
                            meeting = result2[i].load_meeting;
                            translation = result2[i].load_translation;
                        }
                    }
                    deptCategories.push(value.department_shortname); 
                    series_meeting_dept.push(meeting);
                    series_translation_dept.push(translation);                   
                });

                Highcharts.chart('container1', {
                    chart: {
                        backgroundColor: null,
                        type: 'column'
                    },
                    title: {
                        text: '<b>Interpreter Resume</b>'
                    },
                    xAxis: {
                        categories: picCategories
                    },
                    credits: {
                        enabled: false
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Minute(s)'
                        },
                        stackLabels: {
                            enabled: true
                        }
                    },
                    legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || null,
                        borderColor: null,
                        borderWidth: 1,
                        shadow: false
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            pointPadding: 0.93,
                            groupPadding: 0.93,
                            borderWidth: 0.8,
                            borderColor: '#212121',
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{
                        name: 'Meeting',
                        data: series_meeting,
                        color: '#90ed7d'
                    }, {
                        name: 'Translation',
                        data: series_translation,
                        color: '#605ca8'
                    }]
                });

                Highcharts.chart('container2', {
                    chart: {
                        backgroundColor: null,
                        type: 'column'
                    },
                    title: {
                        text: '<b>Deptartment Resume</b>'
                    },
                    xAxis: {
                        categories: deptCategories
                    },
                    credits: {
                        enabled: false
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total Request'
                        },
                        stackLabels: {
                            enabled: true
                        }
                    },
                    legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || null,
                        borderColor: null,
                        borderWidth: 1,
                        shadow: false
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            pointPadding: 0.93,
                            groupPadding: 0.93,
                            borderWidth: 0.8,
                            borderColor: '#212121',
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{
                        name: 'Meeting',
                        data: series_meeting_dept,
                        color: '#90ed7d'
                    }, {
                        name: 'Translation',
                        data: series_translation_dept,
                        color: '#605ca8'
                    }]
                });
                fetchTable();
                $('#loading').hide();
            }
            else{
                $('#loading').hide();
                alert('Attempt to retrieve data failed');
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
