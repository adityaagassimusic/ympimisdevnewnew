@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        table {
            border: 1px solid black !important;
        }

        thead>tr>th {
            vertical-align: middle !important;
            text-align: center !important;
            border: 1px solid black !important;
        }

        tbody>tr>td {
            border: 1px solid black !important;
            padding: 3px 3px 3px 3px !important;
        }

        tfoot>tr>th {
            border: 1px solid black !important;
            padding: 3px 3px 3px 3px !important;
        }

        #loading {
            display: none;
        }
    </style>
@stop
@section('header')

@stop
@section('content')
    <section class="content" style="padding-top: 0">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-2" style="margin-top: 0; margin-bottom: 0;">
                <div class="input-group date">
                    <div class="input-group-addon bg-olive">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="filterDate" onchange="fetchChart()">
                </div>
            </div>
            <div id="chart_title" class="col-xs-10" style="background-color: rgb(96, 92, 168);">
                <center>
                    <span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text"></span>
                </center>
            </div>
            <div class="col-xs-12" style="margin-top: 1%;">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="col-xs-6" style="padding-left: 0;">
                            <div id="container1" style="height: 45vh;"></div>
                            <center>
                                <span style="font-weight: bold; font-size: 1.2vw;">Finished Goods Resume Table</span>
                            </center>
                            <table class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgb(96, 92, 168); color: white;">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">HPL</th>
                                        <th style="width: 0.1%; text-align: right;">Plan</th>
                                        <th style="width: 0.1%; text-align: right;">Actual</th>
                                        <th style="width: 0.1%; text-align: right;">Diff</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (%)</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (Day)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="width: 0.1%; text-align: center;">BI</td>
                                        <td style="width: 0.1%; text-align: right;" id="total_bi_plan"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_bi_actual"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_bi_diff"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_bi_percentage"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_bi_day"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0.1%; text-align: center;">EI</td>
                                        <td style="width: 0.1%; text-align: right;" id="total_ei_plan"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_ei_actual"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_ei_diff"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_ei_percentage"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_ei_day"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="tableResumeFG" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgb(96, 92, 168); color: white;">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">HPL</th>
                                        <th style="width: 0.1%; text-align: right;">Plan</th>
                                        <th style="width: 0.1%; text-align: right;">Actual</th>
                                        <th style="width: 0.1%; text-align: right;">Diff</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (%)</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (Day)</th>
                                    </tr>
                                </thead>
                                <tbody id="tableResumeFGBody">
                                </tbody>
                                <tfoot id="tableResumeFGFoot" style="background-color: rgb(252, 248, 227);">
                                    <tr>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: right;" id="fg_total_plan"></th>
                                        <th style="text-align: right;" id="fg_total_actual"></th>
                                        <th style="text-align: right;" id="fg_total_diff"></th>
                                        <th style="text-align: right;" id="fg_total_percentage"></th>
                                        <th style="text-align: right;" id="fg_total_days"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-xs-6" style="padding-right: 0;">
                            <div id="container2" style="height: 45vh;"></div>
                            <center>
                                <span style="font-weight: bold; font-size: 1.2vw;">KD Parts Resume Table</span>
                            </center>
                            <table class="table table-bordered table-striped table-hover">
                                <thead style="background-color: #00a65a; color: white;">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">HPL</th>
                                        <th style="width: 0.1%; text-align: right;">Plan</th>
                                        <th style="width: 0.1%; text-align: right;">Actual</th>
                                        <th style="width: 0.1%; text-align: right;">Diff</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (%)</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (Day)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="width: 0.1%; text-align: center;">Assy</td>
                                        <td style="width: 0.1%; text-align: right;" id="total_fa_plan"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_fa_actual"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_fa_diff"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_fa_percentage"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_fa_day"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0.1%; text-align: center;">Case</td>
                                        <td style="width: 0.1%; text-align: right;" id="total_cs_plan"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_cs_actual"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_cs_diff"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_cs_percentage"></td>
                                        <td style="width: 0.1%; text-align: right;" id="total_cs_day"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="tableResumeKD" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: #00a65a; color: white;">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">HPL</th>
                                        <th style="width: 0.1%; text-align: right;">Plan</th>
                                        <th style="width: 0.1%; text-align: right;">Actual</th>
                                        <th style="width: 0.1%; text-align: right;">Diff</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (%)</th>
                                        <th style="width: 0.1%; text-align: right;">Shortage (Day)</th>
                                    </tr>
                                </thead>
                                <tbody id="tableResumeKDBody">
                                </tbody>
                                <tfoot id="tableResumeKDFoot" style="background-color: rgb(252, 248, 227);">
                                    <tr>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: right;" id="kd_total_plan"></th>
                                        <th style="text-align: right;" id="kd_total_actual"></th>
                                        <th style="text-align: right;" id="kd_total_diff"></th>
                                        <th style="text-align: right;" id="kd_total_percentage"></th>
                                        <th style="text-align: right;" id="kd_total_days"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Production Resume Detail<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <table id="tableDetail" class="table table-bordered table-striped table-hover">
                            <thead style="">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 0.1%; text-align: right;">Material</th>
                                    <th style="width: 0.1%; text-align: center;">Plan</th>
                                    <th style="width: 0.1%; text-align: center;">Actual</th>
                                    <th style="width: 0.1%; text-align: center;">Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailBody">
                            </tbody>
                            <tbody id="tableDetailFoot">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('#filterDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('body').toggleClass("sidebar-collapse");
            fetchChart();
        });

        var finished_goods = [];
        var knock_downs = [];

        function fetchChart() {
            var filter_date = $('#filterDate').val();
            var data = {
                filter_date: filter_date
            }

            $('#loading').show();

            $.get('{{ url('fetch/production_resume') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#title_text').text('PERIODE ' + result.title_date.toUpperCase());
                    var h = $('#chart_title').height();
                    $('#filterDate').css('height', h);


                    finished_goods = result.finished_goods;
                    knock_downs = result.knock_downs;

                    var work_days = result.work_days.length;
                    if (work_days <= 0) {
                        work_days = 1;
                    }

                    var fg_categories = [];
                    var kd_categories = [];

                    var fg_percentage_series = [];
                    var kd_percentage_series = [];

                    var fg_day_series = [];
                    var kd_day_series = [];

                    var tableResumeFGBody = "";
                    var tableResumeKDBody = "";

                    $('#tableResumeFGBody').html("");
                    $('#tableResumeKDBody').html("");

                    var total_bi_plan = 0;
                    var total_bi_actual = 0;
                    var total_bi_daily = 0;
                    var total_ei_plan = 0;
                    var total_ei_actual = 0;
                    var total_ei_daily = 0;

                    var total_fa_plan = 0;
                    var total_fa_actual = 0;
                    var total_fa_percentage = 0;
                    var total_fa_day = 0;
                    var total_cs_plan = 0;
                    var total_cs_actual = 0;
                    var total_cs_percentage = 0;
                    var total_cs_day = 0;

                    var fg_total_plan = 0;
                    var fg_total_actual = 0;
                    var kd_total_plan = 0;
                    var kd_total_actual = 0;

                    $.each(result.finished_goods, function(key, value) {
                        fg_categories.push(value.hpl);
                        fg_percentage_series.push(parseFloat(((value.plan / value.actual) * 100).toFixed(
                            2)));
                        fg_day_series.push(parseFloat(((value.plan - value.actual) / (value.plan /
                            work_days)).toFixed(2)));

                        tableResumeFGBody += '<tr>';
                        tableResumeFGBody += '<td style="text-align: center;">' + value.hpl + '</td>';
                        tableResumeFGBody += '<td style="text-align: right;">' + value.plan + '</td>';
                        tableResumeFGBody += '<td style="text-align: right;">' + value.actual + '</td>';
                        tableResumeFGBody += '<td style="text-align: right;">' + (value.actual - value
                            .plan) + '</td>';
                        tableResumeFGBody += '<td style="text-align: right;">' + (((value.plan - value
                            .actual) / value.plan) * 100).toFixed(2) + ' %</td>';
                        tableResumeFGBody += '<td style="text-align: right;">' + ((value.plan - value
                            .actual) / (value.plan / work_days)).toFixed(2) + ' Day(s)</td>';
                        tableResumeFGBody += '</tr>';

                        fg_total_plan += value.plan;
                        fg_total_actual += value.actual;

                        if (value.hpl == 'ASFG' || value.hpl == 'TSFG' || value.hpl == 'FLFG' || value
                            .hpl == 'CLFG') {
                            total_bi_plan += value.plan;
                            total_bi_actual += value.actual;
                        }
                        if (value.hpl == 'PN' || value.hpl == 'RC' || value.hpl == 'VN') {
                            total_ei_plan += value.plan;
                            total_ei_actual += value.actual;
                        }
                    });

                    $('#total_bi_plan').text(total_bi_plan);
                    $('#total_bi_actual').text(total_bi_actual);
                    $('#total_bi_diff').text(total_bi_actual - total_bi_plan);
                    $('#total_bi_percentage').text((((total_bi_plan - total_bi_actual) / total_bi_plan) * 100)
                        .toFixed(2) + ' %');
                    $('#total_bi_day').text(((total_bi_plan - total_bi_actual) / (total_bi_plan / work_days))
                        .toFixed(2) + ' Day(s)');

                    $('#total_ei_plan').text(total_ei_plan);
                    $('#total_ei_actual').text(total_ei_actual);
                    $('#total_ei_diff').text(total_ei_actual - total_ei_plan);
                    $('#total_ei_percentage').text((((total_ei_plan - total_ei_actual) / total_ei_plan) * 100)
                        .toFixed(2) + ' %');
                    $('#total_ei_day').text(((total_ei_plan - total_ei_actual) / (total_ei_plan / work_days))
                        .toFixed(2) + ' Day(s)');

                    $('#fg_total_plan').text(fg_total_plan);
                    $('#fg_total_actual').text(fg_total_actual);
                    $('#fg_total_diff').text(fg_total_actual - fg_total_plan);
                    $('#fg_total_percentage').text((((fg_total_plan - fg_total_actual) / fg_total_plan) * 100)
                        .toFixed(2) + ' %');
                    $('#fg_total_days').text(((fg_total_plan - fg_total_actual) / (fg_total_plan / work_days))
                        .toFixed(2) + ' Day(s)');

                    $('#tableResumeFGBody').append(tableResumeFGBody);

                    $.each(result.knock_downs, function(key, value) {
                        kd_categories.push(value.hpl);
                        kd_percentage_series.push(parseFloat(((value.plan / value.actual) * 100).toFixed(
                            2)));
                        kd_day_series.push(parseFloat(((value.plan - value.actual) / (value.plan /
                            work_days)).toFixed(2)));

                        tableResumeKDBody += '<tr>';
                        tableResumeKDBody += '<td style="text-align: center;">' + value.hpl + '</td>';
                        tableResumeKDBody += '<td style="text-align: right;">' + value.plan + '</td>';
                        tableResumeKDBody += '<td style="text-align: right;">' + value.actual + '</td>';
                        tableResumeKDBody += '<td style="text-align: right;">' + (value.actual - value
                            .plan) + '</td>';
                        tableResumeKDBody += '<td style="text-align: right;">' + (((value.plan - value
                            .actual) / value.plan) * 100).toFixed(2) + ' %</td>';
                        tableResumeKDBody += '<td style="text-align: right;">' + ((value.plan - value
                            .actual) / (value.plan / work_days)).toFixed(2) + ' Day(s)</td>';
                        tableResumeKDBody += '</tr>';

                        kd_total_plan += value.plan;
                        kd_total_actual += value.actual;

                        if (value.hpl == 'ASSY-SX' || value.hpl == 'SUBASSY-SX' || value.hpl ==
                            'SUBASSY-CL' || value.hpl == 'SUBASSY-FL' || value.hpl == 'CL-BODY') {
                            total_fa_plan += value.plan;
                            total_fa_actual += value.actual;
                        }
                        if (value.hpl == 'CASE') {
                            total_cs_plan += value.plan;
                            total_cs_actual += value.actual;
                        }

                    });

                    $('#total_fa_plan').text(total_fa_plan);
                    $('#total_fa_actual').text(total_fa_actual);
                    $('#total_fa_diff').text(total_fa_actual - total_fa_plan);
                    $('#total_fa_percentage').text((((total_fa_plan - total_fa_actual) / total_fa_plan) * 100)
                        .toFixed(2) + ' %');
                    $('#total_fa_day').text(((total_fa_plan - total_fa_actual) / (total_fa_plan / work_days))
                        .toFixed(2) + ' Day(s)');

                    $('#total_cs_plan').text(total_cs_plan);
                    $('#total_cs_actual').text(total_cs_actual);
                    $('#total_cs_diff').text(total_cs_actual - total_cs_plan);
                    $('#total_cs_percentage').text((((total_cs_plan - total_cs_actual) / total_cs_plan) * 100)
                        .toFixed(2) + ' %');
                    $('#total_cs_day').text(((total_cs_plan - total_cs_actual) / (total_cs_plan / work_days))
                        .toFixed(2) + ' Day(s)');

                    $('#kd_total_plan').text(kd_total_plan);
                    $('#kd_total_actual').text(kd_total_actual);
                    $('#kd_total_diff').text(kd_total_actual - kd_total_plan);
                    $('#kd_total_percentage').text((((kd_total_plan - kd_total_actual) / kd_total_plan) * 100)
                        .toFixed(2) + ' %');
                    $('#kd_total_days').text(((kd_total_plan - kd_total_actual) / (kd_total_plan / work_days))
                        .toFixed(2) + ' Day(s)');
                    $('#tableResumeKDBody').append(tableResumeKDBody);

                    Highcharts.chart('container1', {
                        chart: {
                            backgroundColor: null,
                            type: 'column'
                        },
                        title: {
                            text: '<b>Finished Goods Shortage Summary</b>'
                        },
                        xAxis: {
                            categories: fg_categories
                        },
                        credits: {
                            enabled: false
                        },
                        yAxis: [
                            // {
                            // 	labels: {
                            // 		format: '{value}%',
                            // 		style: {
                            // 			color: Highcharts.getOptions().colors[1]
                            // 		}
                            // 	},
                            // 	title: {
                            // 		text: 'Percentage',
                            // 		style: {
                            // 			color: Highcharts.getOptions().colors[1]
                            // 		}
                            // 	}
                            // }, 
                            {
                                labels: {
                                    format: '{value}',
                                    style: {
                                        color: Highcharts.getOptions().colors[1]
                                    }
                                },
                                title: {
                                    text: null,
                                    style: {
                                        color: Highcharts.getOptions().colors[1]
                                    }
                                },
                                opposite: true
                            }
                        ],
                        legend: {
                            enabled: false,
                            align: 'right',
                            x: -30,
                            verticalAlign: 'top',
                            y: 25,
                            floating: true,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || null,
                            borderColor: null,
                            borderWidth: 1,
                            shadow: false
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{point.y}'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.2f} Day(s)',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                animation: {
                                    duration: 0
                                }
                            }
                        },
                        series: [{
                                name: 'Day(s)',
                                data: fg_day_series,
                                color: '#605ca8',
                                // yAxis: 1,
                            }
                            // ,{
                            // 	name: 'Percentage',
                            // 	data: fg_percentage_series,
                            // 	color: '#90ed7d',
                            // 	tooltip: {
                            // 		valueSuffix: ' %'
                            // 	}
                            // }
                        ]
                    });

                    Highcharts.chart('container2', {
                        chart: {
                            backgroundColor: null,
                            type: 'column'
                        },
                        title: {
                            text: '<b>KD Parts Shortage Summary</b>'
                        },
                        xAxis: {
                            categories: kd_categories
                        },
                        credits: {
                            enabled: false
                        },
                        yAxis: [
                            // {
                            // 	labels: {
                            // 		format: '{value}%',
                            // 		style: {
                            // 			color: Highcharts.getOptions().colors[1]
                            // 		}
                            // 	},
                            // 	title: {
                            // 		text: 'Percentage',
                            // 		style: {
                            // 			color: Highcharts.getOptions().colors[1]
                            // 		}
                            // 	}
                            // }, 
                            {
                                labels: {
                                    format: '{value}',
                                    style: {
                                        color: Highcharts.getOptions().colors[1]
                                    }
                                },
                                title: {
                                    text: null,
                                    style: {
                                        color: Highcharts.getOptions().colors[1]
                                    }
                                },
                                opposite: true
                            }
                        ],
                        legend: {
                            enabled: false,
                            align: 'right',
                            x: -30,
                            verticalAlign: 'top',
                            y: 25,
                            floating: true,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || null,
                            borderColor: null,
                            borderWidth: 1,
                            shadow: false
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{point.y}'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.2f} Day(s)',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                animation: {
                                    duration: 0
                                }
                            }
                        },
                        series: [{
                                name: 'Day(s)',
                                data: kd_day_series,
                                color: '#00a65a',
                                // yAxis: 1,		
                            }
                            // ,{
                            // 	name: 'Percentage',
                            // 	data: kd_percentage_series,
                            // 	color: '#90ed7d',
                            // 	tooltip: {
                            // 		valueSuffix: ' %'
                            // 	}
                            // }
                        ]
                    });

                    $('#loading').hide();

                } else {
                    alert('Attempt to retrieve data failed.');
                }
            });
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '3000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '3000'
            });
        }
    </script>
@endsection
