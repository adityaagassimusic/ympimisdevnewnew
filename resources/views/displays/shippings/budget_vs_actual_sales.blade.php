@extends('layouts.display')
@section('stylesheets')
    <style type="text/css">
        #loading {
            display: none;
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
            <div class="col-xs-3" style="padding-top: 0.25%;">
                <div class="input-group date">
                    <div class="input-group-addon" style="background-color: #ccff90;">
                        <i class="fa fa-calendar-o"></i>
                    </div>
                    <select class="form-control select2" onchange="fetchChart()" name="fy" id='fy'
                        data-placeholder="Select Fiscal Year" style="width: 100%;">
                        <option value="">Select Fiscal Year</option>
                        <option value="FY198">FY198</option>
                        <option value="FY199">FY199</option>
                        <option value="FY200">FY200</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12" id="chart1" style="margin-top: 1%; height: 40vh;"></div>
            <div class="col-xs-12" id="chart2" style="margin-top: 1%; height: 40vh;"></div>
        </div>
    </section>


    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog" style="width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #f2f2f2; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            SALES DETAIL
                        </h2>
                    </center>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; padding-bottom: 25px;">
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                                <li class="vendor-tab active">
                                    <a href="#tab_1" data-toggle="tab" id="tab_header_1">Category</a>
                                </li>
                                <li class="vendor-tab">
                                    <a href="#tab_2" data-toggle="tab" id="tab_header_2">Invoice No.</a>
                                </li>
                                <li class="vendor-tab">
                                    <a href="#tab_3" data-toggle="tab" id="tab_header_3">Detail</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <div class="row">
                                        <div class="col-xs-8 col-xs-offset-2">
                                            <table class="table table-hover table-bordered table-striped" id="tableDetail">
                                                <thead style="background-color: rgba(126,86,134,.7);">
                                                    <tr>
                                                        <th style="width: 3%; text-align: center; vertical-align: middle;">
                                                            Month
                                                        </th>
                                                        <th style="width: 3%; text-align: center; vertical-align: middle;">
                                                            Category
                                                        </th>
                                                        <th style="width: 1%; text-align: center; vertical-align: middle;">
                                                            Quantity
                                                        </th>
                                                        <th style="width: 1%; text-align: center; vertical-align: middle;">
                                                            Amount
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="BodyDetail">
                                                </tbody>
                                                <tfoot id="FootDetail">
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_2">
                                    <div class="row">
                                        <div class="col-xs-8 col-xs-offset-2">
                                            <table class="table table-hover table-bordered table-striped" id="tableInvoice">
                                                <thead style="background-color: rgba(126,86,134,.7);">
                                                    <tr>
                                                        <th style="width: 3%; text-align: center; vertical-align: middle;">
                                                            Month
                                                        </th>
                                                        <th style="width: 3%; text-align: center; vertical-align: middle;">
                                                            Invoice
                                                            No.
                                                        </th>
                                                        <th style="width: 1%; text-align: center; vertical-align: middle;">
                                                            Quantity
                                                        </th>
                                                        <th style="width: 1%; text-align: center; vertical-align: middle;">
                                                            Amount
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="BodyInvoice">
                                                </tbody>
                                                <tfoot id="FootInvoice">
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_3">
                                    <table class="table table-hover table-bordered table-striped" id="tableAll">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                            <tr>
                                                <th style="width: 1%; text-align: center; vertical-align: middle;">Material
                                                </th>
                                                <th style="width: 30%; text-align: center; vertical-align: middle;">
                                                    Description</th>
                                                <th style="width: 1%; text-align: center; vertical-align: middle;">IV No.
                                                </th>
                                                <th style="width: 1%; text-align: center; vertical-align: middle;">Bl Date
                                                </th>
                                                <th style="width: 1%; text-align: center; vertical-align: middle;">Quantity
                                                </th>
                                                <th style="width: 1%; text-align: center; vertical-align: middle;">Price
                                                </th>
                                                <th style="width: 1%; text-align: center; vertical-align: middle;">Amount
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="BodyAll">
                                        </tbody>
                                        <tfoot id="FootAll">
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchChart();

            $('.select2').select2({
                allowClear: true,
            });
        });

        var budget_global = [];
        var sales_global = [];

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

        function fetchChart() {
            var fy = $('#fy').val();
            var data = {
                fy: fy
            }

            $('#loading').show();

            $.get('{{ url('fetch/budget_vs_actual_sales') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#title_text').text('BUDGET VS ACTUAL SALES ON ' + result.fy);
                    var h = $('#chart_title').height();
                    $('.select').css('height', h);

                    var xCategories = [];
                    var forecast = [];
                    var budget = [];
                    var sales = [];

                    var acc_budget = [];
                    var acc_forecast = [];
                    var acc_sales = [];
                    var value_budget = 0;
                    var value_forecast = 0;
                    var value_sales = 0;

                    sales_global = result.resume_sales;

                    for (var i = 0; i < result.months.length; i++) {
                        xCategories.push(result.months[i].text);

                        $.each(result.resume_forecast, function(key, value) {
                            if (value.month == result.months[i].month) {
                                forecast.push(value.amount);
                                value_forecast += value.amount;
                            }
                        });
                        acc_forecast.push(value_forecast);

                        $.each(result.resume_budget, function(key, value) {
                            if (value.month == result.months[i].month) {
                                budget.push(value.amount);
                                value_budget += value.amount;

                            }
                        });
                        acc_budget.push(value_budget);

                        var this_sales = 0;
                        $.each(result.resume_sales, function(key, value) {
                            if (value.bl_date.substr(0, 7) == result.months[i].month) {
                                this_sales += (value.quantity * value.price);
                            }
                        });

                        sales.push(this_sales / 1000);
                        value_sales += this_sales / 1000;
                        acc_sales.push(value_sales);
                    }



                    Highcharts.chart('chart1', {
                        chart: {
                            type: 'column',
                            options3d: {
                                enabled: true,
                                alpha: 0,
                                beta: 0,
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
                        xAxis: {
                            categories: xCategories,
                        },
                        yAxis: {
                            title: {
                                text: 'x 1000 USD'
                            }
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
                            name: 'Budget (USD)',
                            data: budget,
                            color: '#44a9a8'

                        }, {
                            name: 'Forecast (USD)',
                            data: forecast,
                            color: '#ed7d31'

                        }, {
                            name: 'Actual Sales (USD)',
                            data: sales,
                            color: '#a9ff97'
                        }]
                    });

                    Highcharts.chart('chart2', {
                        chart: {
                            type: 'areaspline',
                            backgroundColor: null
                        },
                        title: {
                            text: 'BUDGET VS ACTUAL SALES Accumulation',
                            style: {
                                fontSize: '24px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'x 1000 USD'
                            }
                        },
                        xAxis: {
                            categories: xCategories,
                        },
                        tooltip: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                        },
                        plotOptions: {
                            depth: 100,
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.0f}',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                animation: false,
                                cursor: 'pointer'
                            },
                            areaspline: {
                                fillOpacity: 0.5
                            }
                        },
                        series: [{
                            name: 'Budget (USD)',
                            data: acc_budget,
                            color: '#44a9a8'
                        }, {
                            name: 'Forecast (USD)',
                            data: acc_forecast,
                            color: '#ed7d31'
                        }, {
                            name: 'Actual Sales (USD)',
                            data: acc_sales,
                            color: '#a9ff97'
                        }]

                    });

                    $('#loading').hide();

                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        var convert_month = {
            'Jan': '01',
            'Feb': '02',
            'Mar': '03',
            'Apr': '04',
            'May': '05',
            'Jun': '06',
            'Jul': '07',
            'Aug': '08',
            'Sep': '09',
            'Oct': '10',
            'Nov': '11',
            'Dec': '12',
        }

        function showDetail(category) {

            var year = category.split('-')[1];
            var month = category.split('-')[0];
            var current = year + '-' + convert_month[month];

            console.log(current);

            var tempSales = [];
            var tempInvoice = [];
            var resumeSales = [];
            var resumeInvoice = [];
            var sumSalesQty = 0;
            var sumSalesAmount = 0;
            $.each(sales_global, function(key, value) {
                if (current == value.bl_date.substr(0, 7)) {
                    var amount = value.quantity * value.price;

                    var keySales = value.category;
                    if (!tempSales[keySales]) {
                        tempSales[keySales] = {
                            'month': category,
                            'category': value.category,
                            'quantity': value.quantity,
                            'amount': amount,
                        };
                    } else {
                        tempSales[keySales].quantity += value.quantity;
                        tempSales[keySales].amount += amount;
                    }

                    var keyInvoice = value.invoice_number;
                    if (!tempInvoice[keyInvoice]) {
                        tempInvoice[keyInvoice] = {
                            'month': category,
                            'invoice_number': value.invoice_number,
                            'quantity': value.quantity,
                            'amount': amount,
                        };
                    } else {
                        tempInvoice[keyInvoice].quantity += value.quantity;
                        tempInvoice[keyInvoice].amount += amount;
                    }

                    sumSalesQty += value.quantity;
                    sumSalesAmount += amount;
                }
            });

            for (var keySales in tempSales) {
                resumeSales.push(tempSales[keySales]);
            }

            for (var keyInvoice in tempInvoice) {
                resumeInvoice.push(tempInvoice[keyInvoice]);
            }

            // CATEGORY
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            $('#BodyDetail').html("");
            $('#FootDetail').html("");
            var tableData = '';
            $.each(resumeSales, function(key, value) {
                tableData += '<tr>';
                tableData += '<td style="width: 3%; text-align: center;">' + value.month + '</td>';
                tableData += '<td style="width: 3%;">' + value.category + '</td>';
                tableData += '<td style="width: 1%; text-align: right;">' + value.quantity + '</td>';
                tableData += '<td style="width: 1%; text-align: right;">';
                tableData += value.amount.toLocaleString(undefined, {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                    style: "currency",
                    currency: "USD"
                });
                tableData += '</td>';
            });
            $('#BodyDetail').append(tableData);

            var footData = '';
            footData += '<tr style="background-color: rgb(252, 248, 227); font-weight: bold; font-size: 14pt;">';
            footData += '<td style="width: 3%; text-align: center;" colspan="2">TOTAL</td>';
            footData += '<td style="width: 3%; text-align: right;">' + sumSalesQty + '</td>';
            footData += '<td style="width: 3%; text-align: right;">';
            footData += sumSalesAmount.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
                style: "currency",
                currency: "USD"
            });
            footData += '</td>';
            footData += '</tr>';
            $('#FootDetail').append(footData);

            var tableQty = $('#tableDetail').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [-1],
                    ['Show all']
                ],
                'buttons': {
                    buttons: [{
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }, {
                        extend: 'excel',
                        className: 'btn btn-info',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'paging': false,
                'lengthChange': true,
                'searching': true,
                'info': false,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            // INVOICE
            $('#tableInvoice').DataTable().clear();
            $('#tableInvoice').DataTable().destroy();
            $('#BodyInvoice').html("");
            $('#FootInvoice').html("");
            var tableData = '';
            $.each(resumeInvoice, function(key, value) {
                tableData += '<tr>';
                tableData += '<td style="width: 3%; text-align: center;">' + value.month + '</td>';
                tableData += '<td style="width: 3%;">' + value.invoice_number + '</td>';
                tableData += '<td style="width: 1%; text-align: right;">' + value.quantity + '</td>';
                tableData += '<td style="width: 1%; text-align: right;">';
                tableData += value.amount.toLocaleString(undefined, {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                    style: "currency",
                    currency: "USD"
                });
                tableData += '</td>';
            });
            $('#BodyInvoice').append(tableData);

            var footData = '';
            footData += '<tr style="background-color: rgb(252, 248, 227); font-weight: bold; font-size: 14pt;">';
            footData += '<td style="width: 3%; text-align: center;" colspan="2">TOTAL</td>';
            footData += '<td style="width: 3%; text-align: right;">' + sumSalesQty + '</td>';
            footData += '<td style="width: 3%; text-align: right;">';
            footData += sumSalesAmount.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
                style: "currency",
                currency: "USD"
            });
            footData += '</td>';
            footData += '</tr>';
            $('#FootInvoice').append(footData);

            var tableQty = $('#tableInvoice').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [-1],
                    ['Show all']
                ],
                'buttons': {
                    buttons: [{
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }, {
                        extend: 'excel',
                        className: 'btn btn-info',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'paging': false,
                'lengthChange': true,
                'searching': true,
                'info': false,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });


            // All
            $('#tableAll').DataTable().clear();
            $('#tableAll').DataTable().destroy();
            $('#BodyAll').html("");
            $('#FootAll').html("");
            var tableData = '';
            $.each(sales_global, function(key, value) {
                if (current == value.bl_date.substr(0, 7)) {
                    tableData += '<tr>';
                    tableData += '<td style="width: 1%; text-align: center;">' + value.material_number + '</td>';
                    tableData += '<td style="width: 30%;">' + value.material_description + '</td>';
                    tableData += '<td style="width: 1%; text-align: center;">' + value.invoice_number + '</td>';
                    tableData += '<td style="width: 1%; text-align: center;">' + value.bl_date + '</td>';
                    tableData += '<td style="width: 1%; text-align: right;">' + value.quantity + '</td>';
                    tableData += '<td style="width: 1%; text-align: right;">' + value.price + '</td>';
                    var amount = value.quantity * value.price;
                    tableData += '<td style="width: 1%; text-align: right;">';
                    tableData += amount.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                        style: "currency",
                        currency: "USD"
                    });
                    tableData += '</td>';
                }
            });
            $('#BodyAll').append(tableData);

            var footData = '';
            footData += '<tr style="background-color: rgb(252, 248, 227); font-weight: bold; font-size: 14pt;">';
            footData += '<td style="width: 3%; text-align: center;" colspan="4">TOTAL</td>';
            footData += '<td style="width: 3%; text-align: right;">' + sumSalesQty + '</td>';
            footData += '<td></td>';
            footData += '<td style="width: 3%; text-align: right;">';
            footData += sumSalesAmount.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
                style: "currency",
                currency: "USD"
            });
            footData += '</td>';
            footData += '</tr>';
            $('#FootAll').append(footData);


            var tableQty = $('#tableAll').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [-1],
                    ['Show all']
                ],
                'buttons': {
                    buttons: [{
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }, {
                        extend: 'excel',
                        className: 'btn btn-info',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'paging': false,
                'lengthChange': true,
                'searching': true,
                'info': false,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            $('#modalDetail').modal('show');
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
