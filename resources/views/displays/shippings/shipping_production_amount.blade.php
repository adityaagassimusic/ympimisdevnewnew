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
            <div class="col-xs-12" style="margin-top: 1%;">
                <div class="col-xs-6" id="last_update"
                    style="padding-left: 4%; margin-top: 2%; color: white; font-size: 1vw; vertical-align: bottom;"></div>

                <div class="col-xs-6" style="padding-right: 3%; color: white; font-size: 1vw;">
                    <table style="float: right;">
                        <tbody>
                            <tr>
                                <th style="text-align: right;">Budget Sales 売上予算 :</th>
                                <th style="text-align: left; color: #fdfb17;">&nbsp;USD&nbsp;</th>
                                <th style="text-align: right; color: #fdfb17;" id="target_val"></th>
                            </tr>
                            <tr>
                                <th style="text-align: right;">Shipping Acc. 出荷累計 :</th>
                                <th style="text-align: left; color: #fdfb17;">&nbsp;USD&nbsp;</th>
                                <th style="text-align: right; color: #fdfb17;" id="sales_val"></th>
                            </tr>
                            <tr>
                                <th style="text-align: right;">Production Acc. 生産累計 :</th>
                                <th style="text-align: left; color: #fdfb17;">&nbsp;USD&nbsp;</th>
                                <th style="text-align: right; color: #fdfb17;" id="prod_val"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12" id="chart1" style="padding: 0px; height: 70vh;"></div>

                <center>
                    <table style="border: none !important;">
                        <tr style="border: none !important;">
                            <thead style="border: none !important;">
                                <th style="border: none !important; vertical-align: middle !important;">
                                    <div style="vertical-align: middle;">
                                        <span class="label"
                                            style="padding-bottom: 0px; background-color: #ffc3ff; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                        <span style="color: white;"> Actual Sales (USD)</span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span class="label"
                                            style="padding-bottom: 0px; background-color: #a9ff97; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                        <span style="color: white;"> Actual Production (USD)</span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span
                                            style="padding-bottom: 0px; color: #ffc3ff; font-size: 20px; padding-top: 5px;">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                        <span style="color: white;"> Accumulative Sales (USD)</span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span style="padding-bottom: 0px; color: #a9ff97; font-size: 20px;">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                        <span style="color: white;"> Accumulative Production (USD)</span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span style="padding-bottom: 0px; color: #ff0000; font-size: 20px;">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                        <span style="color: white;"> Target Sales (USD)</span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span style="padding-bottom: 0px; color: #097ea4; font-size: 20px; ">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                        <span style="color: white;"> Target Production (USD)</span>
                                    </div>
                                </th>
                            </thead>
                        </tr>
                    </table>
                </center>

                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                    <div class="box box-solid">
                        <div class="box-body">
                            <table id="tableList" class="table table-bordered" style="width: 100%; font-size: 16px;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 20%; text-align: center;">WEEK OF MONTH<br><span
                                                style="color: purple;">第何週</span></th>
                                        <th style="width: 20%; text-align: center;">START DATE<br><span
                                                style="color: purple;">開始日</span></th>
                                        <th style="width: 20%; text-align: center;">END DATE<br><span
                                                style="color: purple;">終了日</span></th>
                                        <th style="width: 20%; text-align: center;">SHIPPING AMOUNT<br><span
                                                style="color: purple;">出荷金額</span></th>
                                        <th style="width: 20%; text-align: center;">PRODUCTION AMOUNT<br><span
                                                style="color: purple;">生産金額</span></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/highcharts-3d.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchChart();
            setInterval(fetchChart, 1000 * 60 * 60);

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

        });

        var allProduction = [];
        var resumeProduction = [];

        var resumeCalendar = [];
        var dailyData = [];
        var resumeWeekly = [];

        function resumeProd() {

            resumeProduction = [];
            var temp = [];
            for (var i = 0; i < allProduction.length; i++) {
                var key = allProduction[i].date;

                if (!temp[key]) {
                    temp[key] = {
                        'date': allProduction[i].date,
                        'amount': allProduction[i].amount,
                    };
                } else {
                    temp[key].amount += allProduction[i].amount;
                }
            }


            for (var key in temp) {
                resumeProduction.push(temp[key]);
            }
        }

        function resumeWeeklyData() {
            resumeWeekly = [];

            for (var i = 0; i < resumeCalendar.length; i++) {
                var sum_sales = 0;
                var sum_production = 0;
                for (var j = 0; j < dailyData.length; j++) {
                    if ((dailyData[j].date >= resumeCalendar[i].start) && (dailyData[j].date <= resumeCalendar[i].end)) {
                        sum_sales += dailyData[j].sales;
                        sum_production += dailyData[j].production;
                    }
                }
                var week = i + 1;

                resumeWeekly[i] = {
                    'week': 'W' + week,
                    'sales': sum_sales,
                    'production': sum_production
                };
            }

            dailyData = [];

        }


        function fetchChart() {
            var month = $('#month').val();
            var data = {
                month: month
            }

            $('#loading').show();

            $.get('{{ url('fetch/shipping_production_amount') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#last_update').html(
                        '<p style="margin: 0px; vertical-align: bottom;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: ' +
                        result.last_update + '</p>');
                    $('#title_text').text('DAILY SHIPPING & PRODUCTION AMOUNT ON ' + result.month_name
                        .toUpperCase());
                    var h = $('#chart_title').height();
                    $('#month').css('height', h);

                    resumeCalendar = result.resume_calendar;
                    allProduction = result.production;
                    resumeProd();

                    var xCategories = [];
                    var xPlotLines = [];
                    var target = [];
                    var budget = [];
                    var forecast = [];

                    var sales = [];
                    var acc_sales = [];
                    var value_sales = 0;

                    var production = [];
                    var acc_production = [];
                    var value_production = 0;

                    for (var i = 0; i < result.calendar.length; i++) {
                        var daily_sales = 0;
                        var daily_prod = 0;
                        var value_target = 0;

                        xCategories.push(result.calendar[i].date_name);

                        //xPlotLines
                        for (var j = 0; j < result.resume_calendar.length; j++) {
                            if (result.calendar[i].week_date == result.resume_calendar[j].end) {
                                var week = j + 1;
                                var plotValue = i + 0.5;

                                xPlotLines.push({
                                    dashStyle: 'ShortDashDot',
                                    color: '#ffff00',
                                    width: 2,
                                    value: plotValue,
                                    label: {
                                        style: {
                                            color: '#ffffff',

                                        },
                                        rotation: 0,
                                        text: 'W' + week
                                    }
                                });
                            }
                        }


                        //SALES
                        var this_sales = 0;
                        for (var j = 0; j < result.sales.length; j++) {
                            if (result.calendar[i].week_date == result.sales[j].bl_date) {
                                this_sales += result.sales[j].price * result.sales[j].quantity / 1000;

                                daily_sales += result.sales[j].price * result.sales[j].quantity;
                            }
                        }

                        sales.push(this_sales);
                        value_sales += this_sales;

                        if (result.calendar[i].week_date > result.now) {
                            acc_sales.push(null);
                        } else {
                            acc_sales.push(value_sales);
                        }


                        //PRODUCTION
                        var this_production = 0;
                        for (var j = 0; j < resumeProduction.length; j++) {
                            if (result.calendar[i].week_date == resumeProduction[j].date) {
                                this_production += resumeProduction[j].amount / 1000;

                                daily_prod += resumeProduction[j].amount;
                            }
                        }

                        production.push(this_production);
                        value_production += this_production;

                        if (result.calendar[i].week_date > result.now) {
                            acc_production.push(null);
                        } else {
                            acc_production.push(value_production);
                        }


                        dailyData.push({
                            'date': result.calendar[i].week_date,
                            'sales': daily_sales,
                            'production': daily_prod
                        });
                    }


                    resumeWeeklyData();
                    $('#tableBodyList').html("");
                    var tableData = "";
                    var total_sales = 0;
                    var total_prod = 0;
                    for (var h = 0; h < result.resume_calendar.length; h++) {
                        for (var i = 0; i < resumeWeekly.length; i++) {
                            var week = h + 1;
                            if (resumeWeekly[i].week == 'W' + week) {
                                tableData += '<tr>';
                                tableData += '<td style="text-align: center;">' + resumeWeekly[i].week + '</td>';
                                tableData += '<td style="text-align: center;">' + result.resume_calendar[h].start +
                                    '</td>';
                                tableData += '<td style="text-align: center;">' + result.resume_calendar[h].end +
                                    '</td>';
                                tableData += '<td style="text-align: right;">' + Math.round(resumeWeekly[i].sales)
                                    .toLocaleString() + '</td>';
                                tableData += '<td style="text-align: right;">' + Math.round(resumeWeekly[i]
                                    .production).toLocaleString() + '</td>';
                                tableData += '</tr>';

                                total_sales += resumeWeekly[i].sales;
                                total_prod += resumeWeekly[i].production;
                            }
                        }
                    }
                    $('#tableBodyList').append(tableData);

                    $('#target_val').text(result.budget.amount.toLocaleString() + ' K');
                    $('#sales_val').text(Math.round(total_sales / 1000).toLocaleString() + ' K');
                    $('#prod_val').text(Math.round(total_prod / 1000).toLocaleString() + ' K');

                    var max = result.budget.amount;
                    if (max < Math.round(total_sales / 1000)) {
                        max = Math.round(total_sales / 1000);
                    }
                    max += 500;



                    var series = [];
                    series.push({
                        name: 'Actual Sales (USD)',
                        color: '#ffc3ff',
                        edgeColor: '#fcfcfc',
                        edgeWidth: 1.10,
                        data: sales,
                    });
                    series.push({
                        name: 'Actual Production (USD)',
                        color: '#a9ff97',
                        edgeColor: '#fcfcfc',
                        edgeWidth: 1.10,
                        data: production
                    });
                    series.push({
                        name: 'Accumulative Sales (USD)',
                        color: '#ffc3ff',
                        data: acc_sales,
                        type: 'spline',
                        dataLabels: {
                            enabled: false
                        },
                    });
                    series.push({
                        name: 'Accumulative Production (USD)',
                        color: '#a9ff97',
                        data: acc_production,
                        type: 'spline',
                        dataLabels: {
                            enabled: false
                        },
                    });

                    var sales_target = [];
                    var acc_target = 0;
                    var point = 0;
                    var index = 0;
                    $.each(result.sales_target, function(key, value) {
                        var target = [];
                        var value_target = 0
                        point++;

                        for (var i = 0; i < result.calendar.length; i++) {
                            if (result.calendar[i].week_name == value.week_name) {
                                value_target = acc_target + value.amount / 1000;
                                target.push(value_target);
                            } else {
                                target.push(null);
                            }
                        }

                        acc_target = value_target;

                        sales_target.push(target);
                        series.push({
                            'showInLegend': false,
                            'name': 'Target Sales W' + point,
                            'type': 'line',
                            'step': 'left',
                            'lineWidth': 3,
                            'zIndex': 100,
                            'color': '#ff0000',
                            'data': sales_target[index],
                            'marker': {
                                enabled: false
                            },
                        });

                        index++;
                    });

                    var prod_target = [];
                    var acc_target = 0;
                    var point = 0;
                    var index = 0;
                    $.each(result.production_target, function(key, production_target) {
                        var target = [];
                        var value_target = 0
                        point++;

                        for (var i = 0; i < result.calendar.length; i++) {
                            if (result.calendar[i].week_name == production_target.week_name) {
                                value_target = acc_target + production_target.amount / 1000;
                                target.push(value_target);
                            } else {
                                target.push(null);
                            }
                        }

                        acc_target = value_target;
                        prod_target.push(target);
                        series.push({
                            'showInLegend': false,
                            'name': 'Target Production W' + point,
                            'type': 'line',
                            'step': 'left',
                            'lineWidth': 3,
                            'zIndex': 100,
                            'color': '#097ea4',
                            'data': prod_target[index],
                            'marker': {
                                enabled: false
                            },
                        });

                        index++;
                    });

                    console.log(series);


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
                            enabled: false
                        },
                        exporting: {
                            enabled: false
                        },
                        xAxis: {
                            categories: xCategories,
                            plotLines: xPlotLines
                        },
                        yAxis: {
                            title: {
                                text: 'x 1000 USD'
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
                                    enabled: false
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
                        series: series
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
