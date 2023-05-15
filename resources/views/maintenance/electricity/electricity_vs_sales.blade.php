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
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="box box-primary box-solid" style="background-color: #f2f2f2;">
                    <div class="box-header text-center">
                        <h4 style="margin: 0px;"><b>Total KWh <span class="month_tittle"></span></b></h4>
                    </div>
                    <div class="box-body text-center">
                        <h2 style="margin: 0px;font-size: 2vw; font-weight: bold; cursor: pointer;" id='total_kwh'
                            onclick="showUpdate()">0
                            <span></span>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="box box-primary box-solid">
                    <div class="box-header text-center">
                        <h4 style="margin: 0px;"><b>Total Sales <span class="month_tittle"></span></b></h4>
                    </div>
                    <div class="box-body text-center">
                        <h2 style="margin: 0px;font-size: 2vw; font-weight: bold;" id='total_sales'>0</h2>
                    </div>
                </div>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="box box-success box-solid">
                    <div class="box-header text-center">
                        <h4 style="margin: 0px;"><b>Target KPI</b></h4>
                    </div>
                    <div class="box-body text-center">
                        <h2 style="margin: 0px;font-size: 2vw; font-weight: bold;" id='target_kpi'>0%</h2>
                    </div>
                </div>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="box box-success box-solid">
                    <div class="box-header text-center">
                        <h4 style="margin: 0px;"><b>Actual KPI <span class="month_tittle"></span></b></h4>
                    </div>
                    <div class="box-body text-center">
                        <h2 style="margin: 0px;font-size: 2vw; font-weight: bold;" id='actual_kpi'>0</h2>
                    </div>
                </div>
            </div>
            <div class="col-xs-2 col-xs-offset-2">
                <div class="col-xs-12" id="last_update" style="padding: 0%; color: white;"></div>
                <div class="col-xs-12" style="float: right; vertical-align: top; padding-right: 0px;">
                    <div class="input-group date">
                        <div class="input-group-addon" style="background-color: #ccff90;">
                            <i class="fa fa-calendar-o"></i>
                        </div>
                        <input type="text" onchange="fetchChart()" class="form-control monthpicker" name="month"
                            id="month" placeholder="Select Month">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" id="monthly" style="height: 36vh;"></div>
            <div class="col-xs-12" id="yearly" style="height: 36vh;"></div>
        </div>

        <p style="color: white; text-align: left; font-style: italic;"><b>* <sup>)</sup> Note :</b><br> % Ratio = [
            Electricity kWh / (Sales
            <i class="fa fa-yen"></i>/
            10<sup>8</sup>) x 100% ] -
            Ratio FY194
        </p>
    </section>

    <div class="modal fade" id="modal_update">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="title"
                        style="text-align: center; font-weight: bold; padding-top: 1%; padding-bottom: 1%;">
                        UPDATE ELECTRICITY BILL & EXCHANGE RATE
                    </h3>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; margin-top: 1%;">
                    <div class="col-xs-12">
                        <table class="table table-hover table-bordered table-striped" id="tableDetail"
                            style="margin-top: 1%;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 3%; text-align: center; vertical-align: middle;">Month</th>
                                    <th style="width: 3%; text-align: center; vertical-align: middle;">Electricity
                                        Consumption</th>
                                    <th style="width: 3%; text-align: center; vertical-align: middle;">Sales (USD)</th>
                                    <th style="width: 3%; text-align: center; vertical-align: middle;">Rate (USD to JPY)
                                    </th>
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
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            setInterval(fetchChart, 1000 * 60 * 60 * 3);

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

            fetchChart();


        });

        var months = [];

        function updateResume() {
            var update = [];
            for (var i = 0; i < months.length; i++) {
                update.push({
                    'month': months[i],
                    'electricity_consumption': $('#electricity_consumption_' + months[i]).val(),
                    'sales': $('#sales_' + months[i]).val(),
                    'usd_to_jpy': $('#rate_' + months[i]).val()
                });
            }

            var data = {
                update: update
            }

            $('#loading').show();
            $.post('{{ url('update/maintenance/electricity_pln') }}', data, function(result, status, xhr) {
                if (result.status) {
                    fetchChart();

                    $('#modal_update').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);

                }
            });

        }

        function showUpdate() {
            var month = $('#month').val();
            var data = {
                month: month
            }

            $('#loading').show();
            $.get('{{ url('fetch/maintenance/electricity_pln') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#BodyDetail').html("");
                    var tableData = "";
                    months = [];

                    for (var i = 0; i < result.data.length; i++) {

                        months.push(result.data[i].month);
                        tableData += '<tr>';
                        tableData += '<td style="padding: 0px; text-align: center; width: 16%;">';
                        tableData += result.data[i].month;
                        tableData += '</td>';

                        tableData += '<td style="padding: 0px; text-align: center; width: 28%;">';
                        tableData += '<input style="text-align: right; padding: 0% 2% 0% 2%; width: 50%;" ';
                        tableData += 'type="number" id="electricity_consumption_' + result.data[i].month + '" ';
                        tableData += 'value="' + result.data[i].electricity_consumption + '">';
                        tableData += ' kWh</td>';

                        tableData += '<td style="padding: 0px; text-align: center; width: 28%;">';
                        tableData += '<input style="text-align: right; padding: 0% 2% 0% 2%; width: 50%;" ';
                        tableData += 'type="number" id="sales_' + result.data[i].month + '" ';
                        tableData += 'value="' + result.data[i].sales + '">';
                        tableData += ' kWh</td>';

                        tableData += '<td style="padding: 0px; text-align: center; width: 28%;">';
                        tableData += '<input style="text-align: right; padding: 0% 2% 0% 2%; width: 50%;" ';
                        tableData += 'type="number" id="rate_' + result.data[i].month + '" ';
                        tableData += 'value="' + result.data[i].usd_to_jpy + '">';
                        tableData += '</td>';

                        tableData += '</tr>';
                    }
                    $('#BodyDetail').append(tableData);
                    $('#modal_update').modal('show');

                    $('#loading').hide();
                }

            });
        }

        function fetchChart() {
            var month = $('#month').val();
            var data = {
                month: month
            }

            $('#loading').show();

            $.get('{{ url('fetch/maintenance/electricity/saving_monitor') }}', data, function(result, status, xhr) {
                if (result.status) {

                    var fy_sales = 0;
                    var fy_elec = 0;

                    $('#last_update').html(
                        '<p class="pull-right" style="margin: 0px; font-size: 10pt;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: ' +
                        result.last_update + '</p>');

                    var total_sales = 0;
                    $.each(result.this_month_sales, function(key, value) {
                        total_sales += (value.quantity * value.price);
                    });
                    var total_sales_txt = total_sales.toLocaleString(undefined, {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0,
                        style: "currency",
                        currency: "USD"
                    });
                    $('#total_sales').text(total_sales_txt);

                    var total_kwh = 0;
                    $.each(result.this_month_electricity, function(key, value) {
                        total_kwh += (value.consumption_outgoing_i + value.consumption_outgoing_ii + value
                            .consumption_outgoing_iii + value.consumption_outgoing_iv);
                    });
                    var total_kwh_txt = total_kwh.toLocaleString(undefined, {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                    $('#total_kwh').text(total_kwh_txt + ' KWh');
                    $('.month_tittle').text(result.month_tittle);


                    var categories = [];
                    var series_data = [];

                    var ichi_oku = 100000000;
                    var exchange_mtc = 102;

                    var base_data = result.base_data.electricity_consumption / (result.base_data.sales / ichi_oku);
                    for (var i = 0; i < result.months.length; i++) {
                        var ratio = null;
                        var kwh_sales = 0;

                        if (result.months[i].month == result.month) {
                            for (var x = 0; x < result.curr.length; x++) {
                                kwh_sales = result.curr[x].elec / (result.curr[x].amount * result.curr[x]
                                    .usd_to_jpy / ichi_oku);
                                fy_sales += result.curr[x].amount * exchange_mtc / ichi_oku;
                                fy_elec += result.curr[x].elec;
                                ratio = (kwh_sales / base_data - 1) * 100;

                                $('#actual_kpi').text(ratio.toFixed(2) + '%');

                            }

                        } else {
                            for (var j = 0; j < result.monthly_data.length; j++) {
                                if (result.monthly_data[j].month == result.months[i].month) {
                                    for (var k = 0; k < result.exchanges.length; k++) {
                                        if (result.exchanges[k].period == result.months[i].month + '-01') {
                                            kwh_sales = result.monthly_data[j].electricity_consumption / (result
                                                .monthly_data[j].sales * result.exchanges[k].rate / ichi_oku);
                                            fy_sales += result.monthly_data[j].sales * result.exchanges[k].rate /
                                                ichi_oku;
                                            fy_elec += result.monthly_data[j].electricity_consumption;
                                            ratio = (kwh_sales / base_data - 1) * 100;
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                        }

                        var color = '';
                        if (ratio > 0) {
                            color = '#fe1515';
                        } else {
                            color = '#00a600';
                        }

                        categories.push(result.months[i].month_text);
                        series_data.push({
                            y: ratio,
                            color: color
                        });
                    }


                    Highcharts.chart('monthly', {
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
                            text: 'Monthly KPI',
                            style: {
                                color: '#ffffff',
                                fontWeight: 'bold',
                                fontSize: '25pt'
                            },
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
                            categories: categories,
                        },
                        yAxis: {
                            title: {
                                text: '% Ratio'
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
                                    format: '{point.y:.1f}%',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                cursor: 'pointer',
                            },
                        },
                        series: [{
                            name: 'Electricity Ratio',
                            data: series_data
                        }]
                    });


                    categories = [];
                    series_data = [];
                    for (var i = 0; i < result.yearly_data.length; i++) {
                        var kwh_sales = 0;
                        var ratio = 0;

                        if (result.yearly_data[i].fiscal_year == result.fy) {
                            // kwh_sales = fy_elec / fy_sales;
                            // ratio = (kwh_sales / base_data - 1) * 100;
                            ratio = -2.9;
                        } else {
                            kwh_sales = result.yearly_data[i].electricity_consumption / (result.yearly_data[i]
                                .sales / ichi_oku);
                            ratio = (kwh_sales / base_data - 1) * 100;
                        }


                        var color = '';
                        if (ratio > 0) {
                            color = '#fe1515';
                        } else {
                            color = '#00a600';
                        }

                        categories.push(result.yearly_data[i].fiscal_year);
                        series_data.push({
                            y: ratio,
                            color: color
                        });
                    }

                    Highcharts.chart('yearly', {
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
                            text: 'yearly KPI',
                            style: {
                                color: '#ffffff',
                                fontWeight: 'bold',
                                fontSize: '25pt'
                            },
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
                            categories: categories,
                        },
                        yAxis: {
                            title: {
                                text: '% Ratio'
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
                                    format: '{point.y:.1f}%',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                cursor: 'pointer',
                            },
                        },
                        series: [{
                            name: 'Electricity Ratio',
                            data: series_data
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
