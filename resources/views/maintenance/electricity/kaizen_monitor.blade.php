@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
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
            <div class="col-xs-10">
                <div class="box box-solid" style="margin-bottom: 0px;">
                    <div class="box-body">
                        <div id="chart" style="height: 74vh;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-2" style="padding-left: 0px;">
                <div class="col-xs-12" style="padding: 0px; margin-bottom: 2%;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <div class="input-group date" style="margin-bottom: 1.5%;">
                                <div class="input-group-addon" style="background-color: #a488aa;">
                                    <i class="fa fa-calendar-o"></i>
                                </div>
                                <input type="text" onchange="fetchChart()" class="form-control datepicker" name="date"
                                    id="date" placeholder="Select Date">
                            </div>
                            <div class="input-group date" style="margin-bottom: 1.5%;">
                                <div class="input-group-addon" style="background-color: #a488aa;">
                                    &nbsp;<i class="fa fa-filter"></i>
                                </div>
                                <select class="form-control select2" name="data_kind" id="data_kind" onchange="fetchChart()"
                                    placeholder="Data Kind">
                                    <option value="Hour">Hour</option>
                                    <option value="Day">Day</option>
                                    <option value="Month">Month</option>
                                </select>
                            </div>
                            <div class="input-group date">
                                <div class="input-group-addon" style="background-color: #a488aa;">
                                    &nbsp;<i class="fa fa-map-marker"></i>&nbsp;
                                </div>
                                <select class="form-control select2" name="location" id="location" onchange="fetchChart()"
                                    placeholder="Select Location">
                                    <option value="WWT">WWT</option>
                                    <option value="CHILLER 2 LCQ">CHILLER 2 LCQ</option>
                                    <option value="DC SND BPRO">DC SND BPRO</option>
                                    <option value="COMPRESSOR 1020 MPRO">COMPRESSOR 1020 MPRO</option>
                                    <option value="COMPRESSOR 1070 MPRO">COMPRESSOR 1070 MPRO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="padding: 0px; margin-bottom: 2%;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <h5 style="margin-top: 0%; font-weight: bold;">Total integrated value [kWh]</h5>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">
                                            <div style="vertical-align: middle;">
                                                <span class="label"
                                                    style="padding-bottom: 0px; background-color: #3635ea; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                            </div>
                                        </th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #c2fdc3">
                                            <span id="before_total"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">
                                            <div style="vertical-align: middle;">
                                                <span class="label"
                                                    style="padding-bottom: 0px; background-color: #4ffe53; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                            </div>
                                        </th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #c2fdc3">
                                            <span id="after_total"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">&nbsp;</th>
                                        <th style="border: none !important; text-align: left; width: 90%;">
                                            <span style="font-weight: bold; font-size: 12px;">Before - After</span>
                                        </th>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">&nbsp;</th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #f4f4f4">
                                            <span id="diff_total"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="padding: 0px; margin-bottom: 2%;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <h5 style="margin-top: 0%; font-weight: bold;">Total CO<sub>2</sub> exhaust [kg/kWh]</h5>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">
                                            <div style="vertical-align: middle;">
                                                <span class="label"
                                                    style="padding-bottom: 0px; background-color: #3635ea; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                            </div>
                                        </th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #c2fdc3">
                                            <span id="before_co2"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">
                                            <div style="vertical-align: middle;">
                                                <span class="label"
                                                    style="padding-bottom: 0px; background-color: #4ffe53; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                            </div>
                                        </th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #c2fdc3">
                                            <span id="after_co2"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">&nbsp;</th>
                                        <th style="border: none !important; text-align: left; width: 90%;">
                                            <span style="font-weight: bold; font-size: 12px;">Before - After</span>
                                        </th>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">&nbsp;</th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #f4f4f4">
                                            <span id="diff_co2"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="padding: 0px; margin-bottom: 2%;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <h5 style="margin-top: 0%; font-weight: bold;">Total charge [$]</h5>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">
                                            <div style="vertical-align: middle;">
                                                <span class="label"
                                                    style="padding-bottom: 0px; background-color: #3635ea; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                            </div>
                                        </th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #c2fdc3">
                                            <span id="before_charge"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">
                                            <div style="vertical-align: middle;">
                                                <span class="label"
                                                    style="padding-bottom: 0px; background-color: #4ffe53; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                            </div>
                                        </th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #c2fdc3">
                                            <span id="after_charge"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <table style="width: 100%; border: none !important; margin-bottom: 1%;">
                                <thead style="border: none !important;">
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">&nbsp;</th>
                                        <th style="border: none !important; text-align: left; width: 90%;">
                                            <span style="font-weight: bold; font-size: 12px;">Before - After</span>
                                        </th>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <th style="border: none !important; text-align: center; width: 10%;">&nbsp;</th>
                                        <th
                                            style="border: none !important; text-align: right; width: 90%; background-color: #f4f4f4">
                                            <span id="diff_charge"></span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xs-12" style="margin-top: 0.5%;">
                <div class="box box-solid" style="margin-bottom: 0px;">
                    <div class="box-body">
                        <table id="tableList" class="table table-bordered" style="width: 100%; font-size: 16px;">
                            <thead id="tableHeadList">
                            </thead>
                            <tbody id="tableBodyList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    {{-- <script src="{{ url('js/exporting.js') }}"></script> --}}
    {{-- <script src="{{ url('js/export-data.js') }}"></script> --}}
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            setInterval(fetchChart, 1000 * 60 * 60 * 3);

            $('.select2').select2();

            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });

            fetchChart();

        });


        function fetchChart() {
            var date = $('#date').val();
            var location = $('#location').val();
            var type = $('#data_kind').val();

            var data = {
                date: date,
                location: location,
                type: type
            }

            $('#loading').show();

            $.get('{{ url('fetch/maintenance/electricity/kaizen_monitor') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#tableHeadList').html('');
                    $('#tableBodyList').html('');

                    if (result.date != $('#date').val()) {
                        $('#date').val(result.date);
                    }
                    var date = result.date;
                    var location = result.location;

                    var usage_before = [];
                    var usage_after = [];
                    var categories = [];

                    var data_kind = $('#data_kind').val();
                    var data_tittle = '';
                    if (data_kind == 'Hour') {
                        for (let i = 0; i <= 23; i++) {
                            categories.push(i);
                        }
                        data_tittle = 'Hourly';
                    } else if (data_kind == 'Day') {
                        for (let i = 1; i <= 31; i++) {
                            categories.push(i);
                        }
                        data_tittle = 'Daily';
                    } else if (data_kind == 'Month') {
                        for (let i = 0; i < result.calendar.length; i++) {
                            categories.push(result.calendar[i].month_dt);
                        }
                        data_tittle = 'Monthly';
                    }


                    var total_usage_before = 0;
                    var total_usage_after = 0;
                    for (let i = 0; i < categories.length; i++) {

                        var usage = 0;
                        for (let j = 0; j < result.before.length; j++) {
                            if (data_kind == 'Hour') {
                                if (location == 'WWT') {
                                    if (parseInt(result.before[j].datetime.substr(8, 2)) == 21) {
                                        if (categories[i] == parseInt(result.before[j].datetime.substr(11, 2))) {
                                            usage += result.before[j].usage;
                                        }
                                    }
                                } else if (location == 'DC SND BPRO') {
                                    if (parseInt(result.before[j].datetime.substr(8, 2)) == 13) {
                                        if (categories[i] == parseInt(result.before[j].datetime.substr(11, 2))) {
                                            usage += result.before[j].usage;
                                        }
                                    }
                                }
                            } else if (data_kind == 'Day') {
                                if (categories[i] == parseInt(result.before[j].datetime.substr(8, 2))) {
                                    usage += result.before[j].usage;
                                }
                            } else if (data_kind == 'Month') {
                                if (categories[i] == result.before[j].datetime.substr(0, 7)) {
                                    usage += result.before[j].usage;
                                }
                            }
                        }
                        usage_before.push(usage);
                        total_usage_before += usage;


                        var usage = 0;
                        for (let j = 0; j < result.after.length; j++) {
                            if (data_kind == 'Hour') {
                                if (parseInt(result.after[j].datetime.substr(8, 2)) ==
                                    parseInt(date.substr(8, 2))) {
                                    if (result.date == result.after[j].datetime.substr(0, 10) &&
                                        categories[i] == parseInt(result.after[j].datetime.substr(11, 2))) {
                                        usage += result.after[j].usage;
                                    }
                                }
                            } else if (data_kind == 'Day') {
                                if (result.date.substr(0, 7) == result.after[j].datetime.substr(0, 7) &&
                                    categories[i] == parseInt(result.after[j].datetime.substr(8, 2))) {
                                    usage += result.after[j].usage;
                                }
                            } else if (data_kind == 'Month') {
                                if (categories[i] == result.after[j].datetime.substr(0, 7)) {
                                    usage += result.after[j].usage;
                                }
                            }
                        }
                        usage_after.push(usage);
                        total_usage_after += usage;
                    }


                    var css = "padding-top: 0px; padding-bottom: 0px; font-size: 14px;";
                    var tableHead = '<tr>';
                    tableHead += '<th style="background-color: #a488aa; ' + css + '">' + data_kind + '</th>';
                    for (let i = 0; i < categories.length; i++) {
                        tableHead += '<th style="background-color: #fcf8e3; text-align: center; ' + css + '">' +
                            categories[i] +
                            '</th>';
                    }
                    tableHead += '</tr>';
                    $('#tableHeadList').html(tableHead);


                    var tableBody = '<tr>';
                    tableBody += '<th style="background-color: #a488aa; ' + css + '">Before Kaizen</th>';
                    for (let i = 0; i < usage_before.length; i++) {
                        tableBody += '<td style="text-align: center; ' + css + '">' + usage_before[i]
                            .toLocaleString(
                                undefined, {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }) + '</td>';
                    }
                    tableBody += '</tr>';
                    tableBody += '<tr>';
                    tableBody += '<th style="background-color: #a488aa; ' + css + '">After Kaizen</th>';
                    for (let i = 0; i < usage_after.length; i++) {
                        tableBody += '<td style="text-align: center; ' + css + '">' + usage_after[i].toLocaleString(
                            undefined, {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }) + '</td>';
                    }
                    tableBody += '</tr>';
                    $('#tableBodyList').html(tableBody);




                    var txt = total_usage_before.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#before_total').text(txt);
                    var txt = total_usage_after.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#after_total').text(txt);
                    var txt = (total_usage_before - total_usage_after).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#diff_total').text(txt);




                    var txt = (total_usage_before * 0.8).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#before_co2').text(txt);
                    var txt = (total_usage_after * 0.8).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#after_co2').text(txt);
                    var txt = ((total_usage_before - total_usage_after) * 0.8).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#diff_co2').text(txt);



                    var txt = (total_usage_before * 1270).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#before_charge').text(txt);
                    var txt = (total_usage_after * 1270).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#after_charge').text(txt);
                    var txt = ((total_usage_before - total_usage_after) * 1270 / 14700).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#diff_charge').text(txt);




                    Highcharts.chart('chart', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: location + ' ' + data_tittle + ' Electricity',
                            style: {
                                color: '#3c3c3c;',
                                fontWeight: 'bold'
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            itemStyle: {
                                color: '#3c3c3c;'
                            },
                        },
                        exporting: {
                            enabled: false
                        },
                        xAxis: {
                            categories: categories,
                            gridLineWidth: 1,
                            labels: {
                                style: {
                                    color: '#3c3c3c;'
                                },
                            },
                            title: {
                                text: data_kind,
                                style: {
                                    color: '#3c3c3c;'
                                },
                            }
                        },
                        yAxis: {
                            labels: {
                                style: {
                                    color: '#3c3c3c;'
                                },
                            },
                            title: {
                                text: 'kWh',
                                style: {
                                    color: '#3c3c3c;'
                                }
                            },
                            gridLineWidth: 0
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
                                    enabled: false,
                                },
                            },
                        },
                        series: [{
                                name: 'Before Kaizen',
                                data: usage_before,
                                color: '#3635ea'
                            },
                            {
                                name: 'After Kaizen',
                                data: usage_after,
                                color: '#4ffe53'
                            }
                        ]
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

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@endsection
