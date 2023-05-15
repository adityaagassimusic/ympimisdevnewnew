@extends('layouts.display')
@section('stylesheets')
    <style type="text/css">
        .box-resume {
            font-size: 30px;
            font-weight: bold;
            height: 21vh;
            color: #3c3c3c;
            cursor: pointer;
        }

        .box-resume:hover {
            font-weight: bold;
            color: black;
        }

        #loading {
            display: none;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@stop
@section('header')
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <div>
                <center>
                    <span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i
                            class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
                </center>
            </div>
        </div>
        <div class="row">
            <div id="chart_title" class="col-xs-9" style="background-color: #ccff90;">
                <center>
                    <span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span>
                </center>
            </div>
            <div class="col-xs-3">
                <div class="input-group date">
                    <div class="input-group-addon" style="background-color: #ccff90;">
                        <i class="fa fa-calendar-o"></i>
                    </div>
                    <input type="text" onchange="fetchChart()" class="form-control monthpicker" name="month"
                        id="month" placeholder="Select Month">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3" style="margin-top: 2%;">
                <div class="small-box box-resume" style="background-color: #f2675b;" onclick="showResume()">
                    <div class="inner" style="padding-bottom: 0px; padding-top: 15px;">
                        <h3 style="margin-bottom: 0px;font-size: 2vw;"><b>MONTHLY TARGET</b></h3>
                        <h2 style="margin: 0px;font-size: 3vw; font-weight: bold;" id='target_monthly'>0</h2>
                    </div>
                    <div class="icon" style="padding-top: 30px;">
                        <i class="glyphicon glyphicon-flash"></i>
                    </div>
                </div>

                <div class="small-box box-resume" style="background-color: #a9ff97;">
                    <div class="inner" style="padding-bottom: 0px; padding-top: 15px;">
                        <h3 style="margin-bottom: 0px;font-size: 2vw;"><b>MONTHLY ACTUAL</b></h3>
                        <h2 style="margin: 0px;font-size: 3vw; font-weight: bold;" id='actual_monthly'>0</h2>
                    </div>
                    <div class="icon" style="padding-top: 30px;">
                        <i class="glyphicon glyphicon-flash"></i>
                    </div>
                </div>

                <div class="small-box box-resume" style="background-color: #ffab85;">
                    <div class="inner" style="padding-bottom: 0px; padding-top: 15px;">
                        <h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CO<sup>2</sup> EMISSION</b></h3>
                        <h2 style="margin: 0px;font-size: 3vw; font-weight: bold;" id='emission'>0</h2>
                    </div>
                    <div class="icon" style="padding-top: 10px;">
                        <i class="fa fa-industry"></i>
                    </div>
                </div>
            </div>
            <div class="col-xs-9" style="margin-top: 2%;">
                <div class="col-xs-12" id="last_update" style="padding-right: 3%; color: white; font-size: 1vw;"></div>
                <div class="col-xs-12" id="chart1" style="padding: 0px; height: 80vh;"></div>
            </div>
            <div class="col-xs-12" style="margin-top: 2%;">
                <div class="col-xs-12" id="chart_monthly" style="padding: 0px; height: 80vh;"></div>
            </div>
        </div>
        <div class="row" id="daily_usage"></div>
        <div class="row" id="daily_average"></div>
    </section>

    <div class="modal fade" id="target_modal">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="title"
                        style="text-align: center; font-weight: bold; padding-top: 1%; padding-bottom: 1%;">
                        UPDATE TARGET
                    </h3>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; margin-top: 1%;">
                    <div class="col-xs-6 col-xs-offset-3">
                        <div class="input-group date">
                            <div class="input-group-addon" style="background-color: #ccff90;">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                            <select class="form-control select2" onchange="changeYear()" name="year" id='year'
                                data-placeholder="Select Year" style="width: 100%;">
                                <option value="">Select Year</option>
                                <option value=""></option>
                                @foreach ($years as $row)
                                    <option value="{{ $row->year }}">{{ $row->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <table class="table table-hover table-bordered table-striped" id="tableDetail"
                            style="margin-top: 1%;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 16%; text-align: center; vertical-align: middle;">Month</th>
                                    <th style="width: 28%; text-align: center; vertical-align: middle;">Daily Target</th>
                                    <th style="width: 28%; text-align: center; vertical-align: middle;">Monthly Target</th>
                                    <th style="width: 28%; text-align: center; vertical-align: middle;">Yearly Target</th>
                                </tr>
                            </thead>
                            <tbody id="BodyDetail">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="updateResume()" class="btn btn-success">Update</button>
                    </center>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/highcharts-3d.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchChart();
            setInterval(fetchChart, 1000 * 60 * 60);


            $('.select2').select2({
                allowClear: true,
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

        });

        var months = [];

        function updateResume() {
            var update = [];

            for (var i = 0; i < months.length; i++) {
                update.push({
                    'month': months[i],
                    'daily': $('#daily_' + months[i]).val(),
                    'monthly': $('#monthly_' + months[i]).val(),
                    'yearly': $('#yearly_' + months[i]).val()
                });
            }

            var data = {
                update: update
            }

            $('#loading').show();
            $.post('{{ url('update/maintenance/electricity/target') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#target_modal').modal('hide');
                    $('#loading').hide();
                    fetchChart();
                }
            });
        }

        function changeYear() {
            var year = $('#year').val();
            var data = {
                year: year
            }

            $('#loading').show();
            $.get('{{ url('fetch/maintenance/electricity/target') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#BodyDetail').html("");
                    var tableData = "";
                    months = [];

                    for (var i = 0; i < result.target.length; i++) {

                        months.push(result.target[i].month);
                        tableData += '<tr>';
                        tableData += '<td style="padding: 0px; text-align: center; width: 16%;">' + result.target[i]
                            .month_name + '</td>';
                        tableData +=
                            '<td style="padding: 0px; text-align: center; width: 28%; font-weight: bold;">';
                        tableData +=
                            '<input style="text-align: right; padding-left: 2%; padding-right: 2%; width: 70%;" type="number" id="daily_' +
                            result.target[i].month + '" value="' + result.target[i].daily_target + '">';
                        tableData += ' kWh</td>';

                        tableData +=
                            '<td style="padding: 0px; text-align: center; width: 28%; font-weight: bold;">';
                        tableData +=
                            '<input style="text-align: right; padding-left: 2%; padding-right: 2%; width: 70%;" type="number" id="monthly_' +
                            result.target[i].month + '" value="' + result.target[i].monthly_target + '">';
                        tableData += ' kWh</td>';

                        tableData +=
                            '<td style="padding: 0px; text-align: center; width: 28%; font-weight: bold;">';
                        tableData +=
                            '<input style="text-align: right; padding-left: 2%; padding-right: 2%; width: 70%;" type="number" id="yearly_' +
                            result.target[i].month + '" value="' + result.target[i].yearly_target + '">';
                        tableData += ' kWh</td>';
                        tableData += '</tr>';
                    }
                    $('#BodyDetail').append(tableData);

                    $('#loading').hide();
                }

            });
        }

        function showResume() {
            $('#target_modal').modal('show');

            var background_color = '#f2f2f2';

            $('#title').css({
                'background-color': background_color
            });

            changeYear();

        }

        function fetchChart() {
            var month = $('#month').val();
            var data = {
                month: month
            }

            $('#loading').show();

            $.get('{{ url('fetch/maintenance/electricity/daily_consumption_ratio') }}', data, function(result, status,
                xhr) {
                if (result.status) {

                    $('#last_update').html(
                        '<p class="pull-right" style="margin: 0px;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: ' +
                        result.last_update + '</p>');
                    $('#title_text').text('DAILY ELECTRICITY CONSUMPTION ON ' + result.month_name.toUpperCase());
                    var h = $('#chart_title').height();
                    $('#month').css('height', h);

                    var xCategories = [];
                    var acc_target = [];
                    var acc_consump = [];

                    var value_target = 0;
                    var value_consump = 0;


                    for (var i = 0; i < result.calendar.length; i++) {
                        xCategories.push(result.calendar[i].date_name);

                        if (result.calendar[i].remark == 'H') {
                            value_target += 0;
                        } else {
                            value_target += result.target.daily_target / 1000;
                        }
                        acc_target.push(value_target);


                        var is_inserted = true;
                        for (var j = 0; j < result.consumption.length; j++) {
                            if (result.calendar[i].week_date == result.consumption[j].date) {
                                value_consump += (result.consumption[j].consumption_outgoing_i + result.consumption[
                                        j].consumption_outgoing_ii + result.consumption[j]
                                    .consumption_outgoing_iii + result.consumption[j].consumption_outgoing_iv);


                                if ((result.consumption[j].consumption_outgoing_i == null) && (result.consumption[j]
                                        .consumption_outgoing_ii == null) && (result.consumption[j]
                                        .consumption_outgoing_iii == null) && (result.consumption[j]
                                        .consumption_outgoing_iv == null)) {
                                    is_inserted = false;
                                }

                                break;
                            }
                        }

                        // console.log(result.last_update.substr(0,10) + '_' +result.calendar[i].week_date);

                        if (is_inserted && (result.calendar[i].week_date < result.last_update.substr(0, 10))) {
                            acc_consump.push(value_consump / 1000);
                        } else {
                            acc_consump.push(null);
                        }

                    }

                    $('#target_daily').html((result.target.daily_target / 1000).toFixed(2) +
                        '<sup style="font-size: 2vw">MWh</sup>');
                    $('#target_monthly').html((result.target.monthly_target / 1000).toFixed(2) +
                        '<sup style="font-size: 2vw">MWh</sup>');
                    $('#actual_monthly').html((value_consump / 1000).toFixed(2) +
                        '<sup style="font-size: 2vw">MWh</sup>');
                    $('#emission').html((value_consump * 0.8 / 1000).toFixed(2) +
                        '<sup style="font-size: 2vw">Ton</sup>');

                    var max = value_target;
                    if ((value_consump / 1000) > value_target) max = (value_consump / 1000);


                    Highcharts.chart('chart1', {
                        chart: {
                            type: 'column',
                            options3d: {
                                enabled: true,
                                alpha: 3,
                                beta: 3,
                                viewDistance: 20,
                                depth: 80
                            },
                            backgroundColor: null
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true
                        },
                        exporting: {
                            enabled: false
                        },
                        xAxis: {
                            categories: xCategories,
                        },
                        yAxis: {
                            title: {
                                text: 'MWh (x 1000 kWh)'
                            },
                            max: max,
                            min: 0,
                        },
                        tooltip: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.05,
                                groupPadding: 0.1,
                                borderWidth: 0
                            },
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.0f}',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function(event) {
                                            showDetail(event.point.category);

                                        }
                                    }
                                },
                            },
                        },
                        series: [{
                            name: 'Target',
                            data: acc_target,
                            type: 'spline',
                            lineWidth: 4,
                            color: '#ff2e2c',
                            zIndex: 1000,
                            marker: {
                                enabled: false
                            },
                            dataLabels: {
                                enabled: false
                            },
                        }, {
                            name: 'Electricity Consumption Accumulative',
                            color: '#a9ff97',
                            zIndex: 1,
                            data: acc_consump
                        }]
                    });

                    var daily_outgoing_i = [];
                    var daily_outgoing_ii = [];
                    var daily_outgoing_iii = [];
                    var daily_outgoing_iv = [];

                    var avg_outgoing_i = [];
                    var avg_outgoing_ii = [];
                    var avg_outgoing_iii = [];
                    var avg_outgoing_iv = [];


                    for (var i = 0; i < result.calendar.length; i++) {

                        var consumption_outgoing_i = null;
                        var consumption_outgoing_ii = null;
                        var consumption_outgoing_iii = null;
                        var consumption_outgoing_iv = null;

                        for (var j = 0; j < result.consumption.length; j++) {
                            if (result.calendar[i].week_date == result.consumption[j].date) {
                                consumption_outgoing_i = result.consumption[j].consumption_outgoing_i;
                                consumption_outgoing_ii = result.consumption[j].consumption_outgoing_ii;
                                consumption_outgoing_iii = result.consumption[j].consumption_outgoing_iii;
                                consumption_outgoing_iv = result.consumption[j].consumption_outgoing_iv;

                                break;
                            }
                        }

                        daily_outgoing_i.push(consumption_outgoing_i);
                        daily_outgoing_ii.push(consumption_outgoing_ii);
                        daily_outgoing_iii.push(consumption_outgoing_iii);
                        daily_outgoing_iv.push(consumption_outgoing_iv);

                        if (consumption_outgoing_i == null) {
                            avg_outgoing_i.push(null);
                        } else {
                            avg_outgoing_i.push(consumption_outgoing_i / 24);
                        }

                        if (consumption_outgoing_ii == null) {
                            avg_outgoing_ii.push(null);
                        } else {
                            avg_outgoing_ii.push(consumption_outgoing_ii / 24);
                        }

                        if (consumption_outgoing_iii == null) {
                            avg_outgoing_iii.push(null);
                        } else {
                            avg_outgoing_iii.push(consumption_outgoing_iii / 24);
                        }

                        if (consumption_outgoing_iv == null) {
                            avg_outgoing_iv.push(null);
                        } else {
                            avg_outgoing_iv.push(consumption_outgoing_iv / 24);
                        }
                    }

                    var daily = [];
                    daily.push(daily_outgoing_i);
                    daily.push(daily_outgoing_ii);
                    daily.push(daily_outgoing_iii);
                    daily.push(daily_outgoing_iv);

                    var avg = [];
                    avg.push(avg_outgoing_i);
                    avg.push(avg_outgoing_ii);
                    avg.push(avg_outgoing_iii);
                    avg.push(avg_outgoing_iv);

                    var name = ['I', 'II', 'III', 'IV'];

                    $('#daily_usage').html('');
                    $('#daily_average').html('');

                    for (var i = 0; i < 4; i++) {
                        var container = '<div class="col-xs-6" id="daily_' + i + '" style="height: 47vh;"></div>';
                        $('#daily_usage').append(container);

                        Highcharts.chart('daily_' + i, {
                            chart: {
                                type: 'column',
                                options3d: {
                                    enabled: true,
                                    alpha: 3,
                                    beta: 3,
                                    viewDistance: 20,
                                    depth: 80
                                },
                                backgroundColor: null
                            },
                            title: {
                                text: 'DAILY CONSUMPTION OUTGOING ' + name[i] + ' (kWh)'
                            },
                            credits: {
                                enabled: false
                            },
                            legend: {
                                enabled: true
                            },
                            exporting: {
                                enabled: false
                            },
                            xAxis: {
                                categories: xCategories,
                            },
                            yAxis: {
                                title: {
                                    text: 'kWh'
                                },
                            },
                            tooltip: {
                                enabled: false
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.05,
                                    groupPadding: 0.1,
                                    borderWidth: 0
                                },
                                series: {
                                    dataLabels: {
                                        enabled: true,
                                        format: '{point.y:.0f}',
                                        style: {
                                            fontSize: '12px;'
                                        }
                                    },
                                    cursor: 'pointer',
                                    point: {
                                        events: {
                                            click: function(event) {
                                                showDetail(event.point.category);

                                            }
                                        }
                                    },
                                },
                            },
                            series: [{
                                name: 'Electricity Consumption',
                                color: '#61A4BC',
                                zIndex: 1,
                                data: daily[i]
                            }]
                        });

                        var container = '<div class="col-xs-6" id="average_' + i + '" style="height: 47vh;"></div>';
                        $('#daily_average').append(container);

                        Highcharts.chart('average_' + i, {
                            chart: {
                                type: 'column',
                                options3d: {
                                    enabled: true,
                                    alpha: 3,
                                    beta: 3,
                                    viewDistance: 20,
                                    depth: 80
                                },
                                backgroundColor: null
                            },
                            title: {
                                text: 'DAILY AVERAGE CONSUMPTION OUTGOING ' + name[i] + ' PER HOUR (kWh)'
                            },
                            credits: {
                                enabled: false
                            },
                            legend: {
                                enabled: true
                            },
                            exporting: {
                                enabled: false
                            },
                            xAxis: {
                                categories: xCategories,
                            },
                            yAxis: {
                                title: {
                                    text: 'kWh'
                                },
                            },
                            tooltip: {
                                enabled: false
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.05,
                                    groupPadding: 0.1,
                                    borderWidth: 0
                                },
                                series: {
                                    dataLabels: {
                                        enabled: true,
                                        format: '{point.y:.0f}',
                                        style: {
                                            fontSize: '12px;'
                                        }
                                    },
                                    cursor: 'pointer',
                                    point: {
                                        events: {
                                            click: function(event) {
                                                showDetail(event.point.category);

                                            }
                                        }
                                    },
                                },
                            },
                            series: [{
                                name: 'Average Consumption / Hour',
                                color: '#F7E2E2',
                                zIndex: 1,
                                data: avg[i]
                            }]
                        });
                    }

                    var bulan = [];
                    var series1 = [];
                    var series2 = [];
                    var series3 = [];
                    var series4 = [];
                    var fy = [];

                    fy.push(result.fy[0].fiscal_year);

                    $.each(result.weekly_calendar, function(key, value) {
                        var isi = 0;
                        bulan.push(value.bulan);
                        $.each(result.month_consumption, function(key2, value2) {
                            if (value.bulan == value2.date) {
                                series1.push(parseFloat(value2.consumption_outgoing_i.toFixed(2)));
                                series2.push(parseFloat(value2.consumption_outgoing_ii.toFixed(2)));
                                series3.push(parseFloat(value2.consumption_outgoing_iii.toFixed(
                                    2)));
                                series4.push(parseFloat(value2.consumption_outgoing_iv.toFixed(2)));
                                isi = 1;
                            }
                        });
                        if (isi == 0) {
                            series1.push(0);
                            series2.push(0);
                            series3.push(0);
                            series4.push(0);
                        }
                    });

                    Highcharts.chart('chart_monthly', {
                        chart: {
                            type: 'column',
                            options3d: {
                                enabled: true,
                                alpha: 3,
                                beta: 3,
                                viewDistance: 20,
                                depth: 80
                            },
                            backgroundColor: null
                        },
                        title: {
                            text: 'Resume Monthly Consumption ' + fy
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true
                        },
                        exporting: {
                            enabled: false
                        },
                        xAxis: {
                            categories: bulan,
                            type: 'category',
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
                            title: {
                                text: 'kWh'
                            },
                        },
                        tooltip: {
                            headerFormat: '<span>{series.name}</span><br/>',
                            pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
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
                        plotOptions: {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        // click: function () {
                                        // 	detailLimbah(this.category, this.series.name);
                                        // }
                                    }
                                },
                                animation: false,
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        fontSize: '1vw'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer'
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            data: series1,
                            name: 'Outgoing I',
                            zIndex: 0,
                            color: '#C1E1DC'
                        }, {
                            data: series2,
                            name: 'Outgoing II',
                            zIndex: 0,
                            color: '#FFCCAC'
                        }, {
                            data: series3,
                            name: 'Outgoing III',
                            zIndex: 0,
                            color: '#FFEB94'
                        }, {
                            data: series4,
                            name: 'Outgoing IV',
                            zIndex: 0,
                            color: '#FDD475'
                        }]
                    });

                    $('#loading').hide();

                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        Highcharts.createElement('link', {
            href: '{{ url('fonts/UnicaOne.css') }}',
            rel: 'stylesheet',
            type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
            colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
                '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'
            ],
            chart: {
                backgroundColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 1,
                        y2: 1
                    },
                    stops: [
                        [0, '#2a2a2b'],
                        [1, '#3e3e40']
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
    </script>
@endsection
