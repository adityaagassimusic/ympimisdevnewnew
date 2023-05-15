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
                <div class="small-box box-resume" style="background-color: #f2f2f2;" id="budget_resume"
                    onclick="showResume(id)">
                    <div class="inner" style="padding-bottom: 0px; padding-top: 20px;">
                        <h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TOTAL BUDGET</b></h3>
                        <h2 style="margin: 0px;font-size: 4vw; font-weight: bold;" id='total_budget'>0</h2>
                    </div>
                    <div class="icon" style="padding-top: 40px;">
                        <i class="glyphicon glyphicon-usd"></i>
                    </div>
                </div>

                <div class="small-box box-resume" style="background-color: #ff964a;" id="forecast_resume"
                    onclick="showResume(id)">
                    <div class="inner" style="padding-bottom: 0px; padding-top: 20px;">
                        <h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TOTAL FORECAST</b></h3>
                        <h2 style="margin: 0px;font-size: 4vw; font-weight: bold;" id='total_forecast'>0</h2>
                    </div>
                    <div class="icon" style="padding-top: 40px;">
                        <i class="glyphicon glyphicon-usd"></i>
                    </div>
                </div>

                <div class="small-box box-resume" style="background-color: #a9ff97;">
                    <div class="inner" style="padding-bottom: 0px; padding-top: 20px;">
                        <h3 style="margin-bottom: 0px; font-size: 2vw; font-weight: bold;"><b>SALES ACCUMULATION</b></h3>
                        <h2 style="margin: 0px ;font-size: 4vw; font-weight: bold;" id='total_sales'>0</h2>
                    </div>
                    <div class="icon" style="padding-top: 40px;">
                        <i class="glyphicon glyphicon-usd"></i>
                    </div>
                </div>
            </div>
            <div class="col-xs-9" style="margin-top: 2%;">
                <div class="col-xs-12" id="last_update" style="padding-right: 3%; color: white; font-size: 1vw;"></div>
                <div class="col-xs-12" id="chart1" style="padding: 0px; height: 78vh;"></div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="budget_resume_modal">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="title"
                        style="text-align: center; font-weight: bold; padding-top: 1%; padding-bottom: 1%;">
                        UPDATE BUDGET RESUME
                    </h3>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; margin-top: 1%;">
                    <div class="col-xs-6 col-xs-offset-3">
                        <div class="input-group date">
                            <div class="input-group-addon" style="background-color: #ccff90;">
                                <i class="fa fa-calendar-o"></i>
                            </div>
                            <select class="form-control select2" onchange="changeFy()" name="fy" id='fy'
                                data-placeholder="Select Fiscal Year" style="width: 100%;">
                                <option value="">Select Fiscal Year</option>
                                <option value="FY198">FY198</option>
                                <option value="FY199">FY199</option>
                                <option value="FY200">FY200</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <table class="table table-hover table-bordered table-striped" id="tableDetail"
                            style="margin-top: 1%;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 3%; text-align: center; vertical-align: middle;">Month</th>
                                    <th style="width: 3%; text-align: center; vertical-align: middle;" id="table_title">
                                        Amount Budget</th>
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

        var budget_global = [];
        var sales_global = [];
        var modal_category = [];
        var month = [];

        function showResume(id) {
            $('#budget_resume_modal').modal('show');

            var background_color = '';

            if (id.includes("budget")) {
                modal_category = 'budget';
                table_category = 'Budget';
                background_color += '#f2f2f2';

            } else if (id.includes("forecast")) {
                modal_category = 'forecast';
                table_category = 'Forecast';
                background_color += '#ff964a';
            }

            $('#title').css({
                'background-color': background_color
            });

            $('#title').text('UPDATE ' + modal_category.toUpperCase() + ' RESUME');
            $('#table_title').text('Amount ' + table_category);

            changeFy();

        }

        function changeFy() {
            var fy = $('#fy').val();
            var data = {
                category: modal_category,
                fy: fy
            }

            $('#loading').show();
            $.get('{{ url('fetch/shipping_amount_resume') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#BodyDetail').html("");
                    var tableData = "";
                    month = [];
                    for (var i = 0; i < result.data.length; i++) {
                        month.push(result.data[i].month);
                        tableData += '<tr>';
                        tableData += '<td style="padding: 0px; text-align: center; width: 50%;">' + result.data[i]
                            .text + '</td>';
                        tableData +=
                            '<td style="padding: 0px; text-align: center; width: 50%; font-weight: bold;">';
                        tableData +=
                            '<input style="text-align: right; padding-left: 2%; padding-right: 2%; width: 50%;" type="number" id="' +
                            result.data[i].month + '" value="' + result.data[i].amount + '">';
                        tableData += ' K USD</td>';
                        tableData += '</tr>';
                    }
                    $('#BodyDetail').append(tableData);

                    $('#loading').hide();
                }

            });
        }

        function updateResume() {
            var update = [];

            for (var i = 0; i < month.length; i++) {
                update.push({
                    'month': month[i],
                    'amount': $('#' + month[i]).val()
                });
            }

            var data = {
                category: modal_category,
                update: update
            }

            $('#loading').show();
            $.post('{{ url('update/shipping_amount_resume') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#budget_resume_modal').modal('hide');
                    $('#loading').hide();
                    fetchChart();
                }
            });
        }

        function fetchChart() {
            var month = $('#month').val();
            var data = {
                month: month
            }

            $('#loading').show();

            $.get('{{ url('fetch/shipping_amount') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#last_update').html(
                        '<p class="pull-right" style="margin: 0px;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: ' +
                        result.last_update + '</p>');
                    $('#title_text').text('DAILY SHIPPING AMOUNT ON ' + result.month_name.toUpperCase());
                    var h = $('#chart_title').height();
                    $('#month').css('height', h);

                    var xCategories = [];
                    var budget = [];
                    var forecast = [];
                    var sales = [];
                    var acc_sales = [];
                    var value_sales = 0;

                    for (var i = 0; i < result.calendar.length; i++) {
                        xCategories.push(result.calendar[i].date_name);

                        budget.push(result.budget.amount);
                        forecast.push(result.forecast.amount);

                        var this_sales = 0;
                        for (var j = 0; j < result.sales.length; j++) {
                            if (result.calendar[i].week_date == result.sales[j].bl_date) {
                                this_sales += result.sales[j].quantity * result.sales[j].price / 1000;
                            }
                        }

                        sales.push(this_sales);
                        value_sales += this_sales;

                        if (result.calendar[i].week_date > result.now) {
                            acc_sales.push(null);
                        } else {
                            acc_sales.push(value_sales);
                        }
                    }

                    $('#total_budget').html(result.budget.amount + '<sup style="font-size: 2vw">K USD</sup>');
                    $('#total_forecast').html(result.forecast.amount + '<sup style="font-size: 2vw">K USD</sup>');
                    $('#total_sales').html(Math.round(value_sales) + '<sup style="font-size: 2vw">K USD</sup>');

                    var max = 0;
                    if (result.budget.amount > max) max = result.budget.amount;
                    if (result.forecast.amount > max) max = result.forecast.amount;
                    if (value_sales > max) max = value_sales;
                    max += 100;

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
                                text: 'x 1000 USD'
                            },
                            max: max,
                            min: 0,
                            plotBands: [{
                                value: result.budget.amount,
                                width: 2,
                                color: '#f2f2f2',
                                dashStyle: 'Solid',
                                label: {
                                    text: 'Budget : ' + result.budget.amount,
                                    style: {
                                        color: '#f2f2f2',
                                        fontWeight: 'bold'
                                    }
                                },
                                zIndex: 3
                            }, {
                                value: result.forecast.amount,
                                width: 2,
                                color: '#ff964a',
                                dashStyle: 'Solid',
                                label: {
                                    text: 'Forecast : ' + result.forecast.amount,
                                    style: {
                                        color: '#ff964a',
                                        fontWeight: 'bold'
                                    }
                                },
                                zIndex: 3
                            }]
                        },
                        tooltip: {
                            // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            // pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            // '<td style="padding:0"><b>{point.y:.0f} K</b></td></tr>',
                            // footerFormat: '</table>',
                            // shared: true,
                            // useHTML: true
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
                            name: 'Accumulative Sales (USD)',
                            data: acc_sales,
                            type: 'spline',
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:.0f}'
                            },
                        }, {
                            name: 'Actual Sales (USD)',
                            color: '#a9ff97',
                            data: sales
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
