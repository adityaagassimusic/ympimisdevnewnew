@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    .table > tbody > tr:hover {
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
        height: 40px;
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
    <div class="row">
        <div class="col-xs-8">
            <div class="box box-solid" style="height: 80vh;">
                <div class="box-body">
                    <div id="container1" style="height: 75vh;"></div>
                </div>
            </div>
        </div>
        <div class="col-xs-4" style="padding-left: 0;">
            <div class="box box-solid" style="height: 80vh;">
                <div class="box-body">
                    <span style="font-weight: bold; font-size: 1.2vw;">CMS Response Table</span>
                    <table id="tableResume" class="table table-bordered table-striped table-hover">
                        <thead style="background-color: #605ca8; color: white;">
                            <tr>
                                <th style="width: 0.1%; text-align: center;">Period</th>
                                <th style="width: 0.1%; text-align: center;">Answer Yes</th>
                                <th style="width: 0.1%; text-align: center;">Answer No</th>
                                <th style="width: 0.1%; text-align: center;">No Response</th>
                            </tr>
                        </thead>
                        <tbody id="tableResumeBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalChart">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                    <center><span id="modalTitle" style="font-weight: bold; font-size: 1.3vw;"></span></center>
                    <table id="tableModal" class="table table-bordered table-striped table-hover">
                        <thead style="background-color: #605ca8; color: white;">
                            <tr>
                                <th style="width: 1%; text-align: center;">#</th>
                                <th style="width: 3%; text-align: left;">PIC</th>
                                <th style="width: 1%; text-align: left;">Code</th>
                                <th style="width: 4%; text-align: left;">Vendor</th>
                                <th style="width: 1%; text-align: left;">Response</th>
                                <th style="width: 2%; text-align: right;">At</th>
                            </tr>
                        </thead>
                        <tbody id="tableModalBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
        $('body').toggleClass("sidebar-collapse");
        fetchChart();
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
    var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
    var responses = [];
    var logs = [];

    function modalDetail(cat){
        $('#tableModal').DataTable().clear();
        $('#tableModal').DataTable().destroy();
        var tableModalBody = "";
        $('#tableModalBody').html("");
        var cnt = 0;

        $.each(responses, function(key, value){
            if(cat == value.q){
                cnt += 1;
                tableModalBody += '<tr>';
                tableModalBody += '<td style="width: 1%; text-align: center;">'+cnt+'</td>';
                tableModalBody += '<td style="width: 3%; text-align: left;">'+value.pic_name+'</td>';
                tableModalBody += '<td style="width: 1%; text-align: left;">'+value.vendor_code+'</td>';
                tableModalBody += '<td style="width: 4%; text-align: left;">'+value.vendor_name+'</td>';
                if(value.answer == 'Yes'){
                    tableModalBody += '<td style="width: 1%; text-align: left; background-color: #605ca8;">'+value.answer+'</td>';
                }
                else if(value.answer == 'No'){
                    tableModalBody += '<td style="width: 1%; text-align: left; background-color: #90ee7e;">'+value.answer+'</td>';
                }
                else{
                    tableModalBody += '<td style="width: 1%; text-align: left; background-color: #d32f2f;">No Response</td>';
                }
                tableModalBody += '<td style="width: 2%; text-align: right;">'+value.created_at+'</td>';
                tableModalBody += '</tr>';                
            }
        });

        $('#tableModalBody').append(tableModalBody);

        $('#tableModal').DataTable({
            'dom': 'Bfrtip',
            'responsive':true,
            'lengthMenu': [
            [-1 ],
            ['Show all' ]
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
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
        });

        $('#modalTitle').text("Detail Response of "+cat);
        $('#modalChart').modal('show');
    }

    function fetchChart(){
        var data = {

        }

        $.get('{{ url("fetch/trade_agreement/monitoring_cms") }}', data, function(result, status, xhr){
            if(result.status){
                responses = result.responses;
                logs = result.logs;
                var array = result.charts;
                var result = [];

                array.reduce(function(res, value) {
                    if (!res[value.q]) {
                        res[value.q] = { q: value.q, answer_yes: 0, answer_no: 0, no_response: 0 };
                        result.push(res[value.q])
                    }

                    if(value.answer == 'Yes'){
                        res[value.q].answer_yes += 1;
                    }
                    if(value.answer == 'No'){
                        res[value.q].answer_no += 1;                        
                    }
                    if(value.answer == ''){
                        res[value.q].no_response += 1;                        
                    }
                    return res;
                }, {});

                var xCategories = [];
                var series_yes = [];
                var series_no = [];
                var series_no_response = [];

                var tableResumeBody = "";
                $('#tableResumeBody').html("");

                $.each(result, function(key, value){
                    xCategories.push(value.q);
                    series_yes.push(value.answer_yes);
                    series_no.push(value.answer_no);
                    series_no_response.push(value.no_response);

                    tableResumeBody += '<tr onclick="modalDetail(\''+value.q+'\')" style="cursor: pointer;">';
                    tableResumeBody += '<td style="width: 1%; font-weight: bold; font-size: 1.2vw; text-align: center;">'+value.q+'</td>';
                    tableResumeBody += '<td style="width: 1%; font-weight: bold; font-size: 1.2vw; text-align: center;">'+value.answer_yes+'</td>';
                    tableResumeBody += '<td style="width: 1%; font-weight: bold; font-size: 1.2vw; text-align: center;">'+value.answer_no+'</td>';
                    tableResumeBody += '<td style="width: 1%; font-weight: bold; font-size: 1.2vw; text-align: center;">'+value.no_response+'</td>';
                    tableResumeBody += '</tr>';
                });
                $('#tableResumeBody').append(tableResumeBody);

                Highcharts.chart('container1', {
                    chart: {
                        backgroundColor: null,
                        type: 'column'
                    },
                    title: {
                        text: '<b>CMS Reponse Resume</b>'
                    },
                    xAxis: {
                        categories: xCategories
                    },
                    credits: {
                        enabled: false
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total Vendor'
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
                        shared: true,
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>'
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
                            },
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function () {
                                        modalDetail(this.category);
                                    }
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Answer Yes',
                        data: series_yes,
                        color: '#605ca8'
                    }, {
                        name: 'Answer No',
                        data: series_no,
                        color: '#90ee7e'
                    }, {
                        name: 'No Response',
                        data: series_no_response,
                        color: '#d32f2f'
                    }]
                });

            }
            else{
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
