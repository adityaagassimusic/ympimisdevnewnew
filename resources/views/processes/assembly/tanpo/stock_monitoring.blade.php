@extends('layouts.display')
@section('stylesheets')
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding: 2px 5px 2px 5px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 2px 5px 2px 5px;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        #loading {
            display: none;
        }

        .sedang {
            -webkit-animation: sedang 1s infinite;
            -moz-animation: sedang 1s infinite;
            -o-animation: sedang 1s infinite;
            animation: sedang 1s infinite;
        }

        @-webkit-keyframes sedang {

            0%,
            49% {
                background: #e57373;
            }

            50%,
            100% {
                background-color: #ffccff;
            }
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
                    <br><br><br>
                    <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <div class="row">
                    <div class="col-xs-12" style="padding-right:0;">
                        <table id="temperature" style="background-color: #7dfa8c; width: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="4" style="text-align: center; font-size: 1.8vw; font-weight: bold;">
                                        TEMPERATURE</td>
                                </tr>
                                <tr>
                                    <td style="min-width: 0.1%; font-size: 1.5vw;">MIN:</td>
                                    <td style="min-width: 1%; font-size: 1.5vw; font-weight: bold;" id="min_temp">99&#8451;
                                    </td>
                                    <td style="min-width: 0.1%; font-size: 1.5vw;">MAX:</td>
                                    <td style="min-width: 1%; font-size: 1.5vw; font-weight: bold;" id="max_temp">99&#8451;
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: center; font-size: 5vw; font-weight: bold;"
                                        id="temp">99&#8451;</td>
                                </tr>
                            </tbody>
                        </table>

                        <table id="humidity" style="background-color: #eef132; width: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="4" style="text-align: center; font-size: 1.8vw; font-weight: bold;">
                                        HUMIDITY</td>
                                </tr>
                                <tr>
                                    <td style="min-width: 0.1%; font-size: 1.5vw;">MIN:</td>
                                    <td style="min-width: 1%; font-size: 1.5vw; font-weight: bold;" id="min_hum">99</td>
                                    <td style="min-width: 0.1%; font-size: 1.5vw;">MAX:</td>
                                    <td style="min-width: 1%; font-size: 1.5vw; font-weight: bold;" id="max_hum">99</td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: center; font-size: 5vw; font-weight: bold;"
                                        id="hum">99</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-9">
                <div class="row">
                    <div class="col-xs-3">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control datepicker" name="filterDate" id="filterDate"
                                placeholder="Pilih Periode" style="width: 100%;">
                        </div>
                    </div>
                    <div class="col-xs-3" style="padding-left: 0;">
                        <button class="btn btn-success" onclick="fetchChart()"><i
                                class="fa fa-search"></i>&nbsp;&nbsp;&nbsp;&nbsp;Cari&nbsp;&nbsp;</button>
                    </div>
                </div>
                <div id="container" style="height: 95vh;"></div>
            </div>
    </section>


@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('.datepicker').datepicker({
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            fetchChart();
            setInterval(fetchChart, 1000 * 60 * 5);
        });

        var alarm_error = new Audio('{{ url('sounds/alarm_error.mp3') }}');
        var safety_stocks = [];

        function fetchChart() {
            var date = $('#filterDate').val();
            var data = {
                date: date,
            }
            $.get('{{ url('fetch/process/tanpo_stock_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {

                    safety_stocks = result.safety_stocks;
                    var stocks = [];

                    $.each(result.temps, function(key, value) {
                        if (value.remark == "temperature") {
                            $('#min_temp').text(value.lower_limit + " °C");
                            $('#max_temp').text(value.upper_limit + " °C");
                        } else if (value.remark == "humidity") {
                            $('#min_hum').text(value.lower_limit + " %");
                            $('#max_hum').text(value.upper_limit + " %");
                        }

                        if (value.value > value.upper_limit || value.value < value.lower_limit) {
                            if (value.remark == "temperature") {
                                document.getElementById('temperature').style.backgroundColor = "#e57373";
                                $('#temperature').prop('class', 'sedang table table-bordered');
                                $('#temp').text(value.value + " °C");
                                alarm_error.play();

                            } else if (value.remark == "humidity") {
                                document.getElementById('humidity').style.backgroundColor = "#e57373";
                                $('#humidity').prop('class', 'sedang table table-bordered');
                                $('#hum').text(value.value + " %");
                                alarm_error.play();
                            }
                        } else {
                            if (value.remark == "temperature") {
                                document.getElementById('temperature').style.backgroundColor = "#7dfa8c";
                                $('#temperature').prop('class', 'table table-bordered');
                                $('#temp').text(value.value + " °C");
                            } else if (value.remark == "humidity") {
                                document.getElementById('humidity').style.backgroundColor = "#eef132";
                                $('#humidity').prop('class', 'table table-bordered');
                                $('#hum').text(value.value + " %");
                            }
                        }

                    });

                    $.each(result.actual_stocks, function(key, value) {
                        var remark = value.remark;
                        var actual_stock = parseInt(value.stock);
                        var safety_stock = 0;
                        for (var i = 0; i < safety_stocks.length; i++) {
                            if (safety_stocks[i].remark == remark) {
                                safety_stock = safety_stocks[i].safety_stock;
                            }
                        }

                        obj = {};
                        obj['remark'] = remark;
                        obj['actual'] = actual_stock;
                        obj['safety'] = safety_stock;
                        stocks.push(obj);
                    });

                    xCategories = [];
                    plan = [];
                    actual = [];

                    $.each(stocks, function(key, value) {
                        xCategories.push(value.remark);
                        plan.push(value.safety);
                        actual.push(value.actual);
                    });

                    Highcharts.chart('container', {
                        colors: ['#fffb00', '#ff2d00'],
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'Realtime Tanpo Stock Condition</span>'
                        },
                        xAxis: {
                            tickInterval: 1,
                            overflow: true,
                            categories: xCategories,
                            min: 0,
                            gridLineWidth: 1,
                            labels: {
                                style: {
                                    fontSize: '20px',
                                    color: 'white'
                                }
                            }
                        },
                        yAxis: {
                            tickInterval: 500,
                            min: 0,
                            title: {
                                text: 'PC(s)'
                            },
                            labels: {
                                style: {
                                    fontSize: '30px',
                                    color: 'white'
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'right',
                            verticalAlign: 'top',
                            x: -30,
                            y: 50,
                            floating: true,
                            borderWidth: 1,
                            backgroundColor: null,
                            itemStyle: {
                                fontSize: '20px',
                                color: 'white'
                            }
                        },
                        tooltip: {
                            shared: true
                        },
                        plotOptions: {
                            series: {
                                minPointLength: 0,
                                pointPadding: 0,
                                groupPadding: 0,
                                animation: {
                                    duration: 0
                                }
                            },
                            column: {
                                grouping: false,
                                shadow: false,
                                borderWidth: 0,
                            }
                        },
                        series: [{
                            name: 'Target Stock',
                            data: plan,
                            pointPadding: 0.05
                        }, {
                            name: 'Actual Stock',
                            data: actual,
                            pointPadding: 0.2
                        }]
                    });
                } else {
                    alert('Attempt to retrieve data failed.');
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
