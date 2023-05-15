@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            padding-right: 3px;
            padding-left: 3px;
            border: 1px solid black;
            height: 40px !important;
        }

        tbody>tr>td {
            padding-right: 3px;
            padding-left: 3px;
            border: 1px solid black;
        }

        tfoot>tr>th {
            padding-right: 3px;
            padding-left: 3px;
            border: 1px solid black;
            height: 40px !important;
        }

        #loading,
        #error {
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
                    <br><br><br>
                    <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
                </center>
            </div>
        </div>
        <div class="row">
            <div id="period_title" class="col-xs-5" style="background-color: #ccff90;">
                <center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
            </div>
            <div class="col-xs-2 pull-right">
                <select class="form-control select2" id="filterPeriod" style="width: 100%;" data-placeholder="Pilih Periode"
                    onchange="fetchData()" required>
                    <option value=""></option>
                    @foreach ($weeks as $week)
                        <option value="{{ $week->indek }}">{{ $week->fiscal_year }} {{ $week->bulan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-12" style="padding-top: 10px;">
                <div id="monitoring_st" style="height: 42vh;"></div>
            </div>
            <div class="col-xs-12">
                <div id="monitoring_bl" style="height: 42vh;"></div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <table id="tableDetail"
                                    style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
                                    <thead style="background-color: #90ee7e; color: black;">
                                        <tr>
                                            <th style="width: 1%; text-align: center;">Period</th>
                                            <th style="width: 1%; text-align: center;">Dest.</th>
                                            <th style="width: 1%; text-align: center;">Material</th>
                                            <th style="width: 10%; text-align: left;">Description</th>
                                            <th style="width: 1%; text-align: center;">HPL</th>
                                            <th style="width: 1%; text-align: right;">Qty<br>Request</th>
                                            <th style="width: 1%; text-align: right;">Amt<br>Request</th>
                                            <th style="width: 1%; text-align: right;">Qty<br>Backorder</th>
                                            <th style="width: 1%; text-align: right;">Amt<br>Backorder</th>
                                            <th style="width: 1%; text-align: right;">Qty<br>Shipment</th>
                                            <th style="width: 1%; text-align: right;">Amt<br>Shipment</th>
                                            <th style="width: 1%; text-align: right;">Diff Amt<br>Shipment</th>
                                            <th style="width: 1%; text-align: right;">Qty<br>Sales</th>
                                            <th style="width: 1%; text-align: right;">Amt<br>Sales</th>
                                            <th style="width: 1%; text-align: right;">Diff Amt<br>Shipment</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableDetailBody">
                                    </tbody>
                                    <tfoot style="background-color: #fffcb7;">
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                            <th style="text-align: right;"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/bootstrap-toggle.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchData();
            $('.select2').select2();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var datas = [];

        function modalDetail(category) {
            var tableDetailBody = "";
            $('#tableDetailBody').html("");
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            $.each(datas, function(key, value) {
                if (value.destination_shortname == category) {
                    var bg_st = "";
                    var bg_bl = "";
                    if (parseFloat(value.target_quantity_sales) + parseFloat(value.target_quantity_backorder) >
                        parseFloat(value.actual_quantity_st)) {
                        bg_st = "background-color: #ffccff;";
                    }
                    if (parseFloat(value.target_quantity_sales) + parseFloat(value.target_quantity_backorder) >
                        parseFloat(value.actual_quantity_bl)) {
                        bg_bl = "background-color: #ffccff;";
                    }
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 1%; text-align: center;">' + value.st_month + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: center;">' + value.destination_shortname +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: center;">' + value.material_number +
                        '</td>';
                    tableDetailBody += '<td style="width: 10%; text-align: left;">' + value.material_description +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: center;">' + value.hpl + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;">' + value.target_quantity_sales +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;">' + value.target_sales + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;">' + value
                        .target_quantity_backorder + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;">' + value.target_backorder +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;' + bg_st + '">' + value
                        .actual_quantity_st +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;' + bg_st + '">' + value
                        .actual_sales_st +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;' + bg_st + '">' + (value
                            .actual_sales_st - (value
                                .target_sales + value.target_backorder)) +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;' + bg_bl + '">' + value
                        .actual_quantity_bl +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;' + bg_bl + '">' + value
                        .actual_sales_bl +
                        '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: right;' + bg_bl + '">' + (value
                            .actual_sales_bl - (value
                                .target_sales + value.target_backorder)) +
                        '</td>';
                    tableDetailBody += '</tr>';
                }
            });
            $('#tableDetailBody').append(tableDetailBody);

            $('#tableDetail').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'buttons': {
                    buttons: [{
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
                "searching": true,
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "ordering": true,
                "paging": false,
                "footerCallback": function(tfoot, data, start, end, display) {
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                    var api = this.api();

                    var Packing = api.column(5).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(5).footer()).html(Packing.toLocaleString());

                    var act = api.column(6).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(6).footer()).html(act.toLocaleString());

                    var act = api.column(7).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(7).footer()).html(act.toLocaleString());

                    var act = api.column(8).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(8).footer()).html(act.toLocaleString());

                    var act = api.column(9).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(9).footer()).html(act.toLocaleString());

                    var act = api.column(10).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(10).footer()).html(act.toLocaleString());

                    var act = api.column(11).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(11).footer()).html(act.toLocaleString());

                    var act = api.column(12).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(12).footer()).html(act.toLocaleString());

                    var act = api.column(13).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(13).footer()).html(act.toLocaleString());

                    var act = api.column(14).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(14).footer()).html(act.toLocaleString());
                }
            });

            $('#modalDetail').modal('show');
        }

        function fetchData() {
            $('#loading').show();
            var period = $('#filterPeriod').val();
            var data = {
                period: period
            }
            $.get('{{ url('fetch/sales_by_destination') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#title_text').text('Sales Data ' + result.fiscal_year + ' - ' + result
                        .period_title +
                        '');
                    datas = result.datas;
                    resumes = [];

                    var array_by_destination = result.datas;
                    var result_by_destination = [];

                    array_by_destination.reduce(function(res, value) {
                        if (!res[value.destination_shortname]) {
                            res[value.destination_shortname] = {
                                destination_shortname: value.destination_shortname,
                                target_quantity_sales: 0,
                                target_sales: 0,
                                target_quantity_backorder: 0,
                                target_backorder: 0,
                                actual_quantity_st: 0,
                                actual_sales_st: 0,
                                actual_quantity_bl: 0,
                                actual_sales_bl: 0,
                            };
                            result_by_destination.push(res[value.destination_shortname]);
                        }
                        res[value.destination_shortname].target_quantity_sales += parseFloat(value
                            .target_quantity_sales);
                        res[value.destination_shortname].target_sales += parseFloat(value.target_sales);
                        res[value.destination_shortname].target_quantity_backorder += parseFloat(value
                            .target_quantity_backorder);
                        res[value.destination_shortname].target_backorder += parseFloat(value
                            .target_backorder);
                        res[value.destination_shortname].actual_quantity_st += parseFloat(value
                            .actual_quantity_st);
                        res[value.destination_shortname].actual_sales_st += parseFloat(value
                            .actual_sales_st);
                        res[value.destination_shortname].actual_quantity_bl += parseFloat(value
                            .actual_quantity_bl);
                        res[value.destination_shortname].actual_sales_bl += parseFloat(value
                            .actual_sales_bl);
                        return res;
                    }, {});

                    categories_by_destination = [];
                    series_by_destination_target = [];
                    series_by_destination_backorder = [];
                    series_by_destination_actual = [];
                    series_by_destination_percentage = [];

                    $.each(result_by_destination, function(key, value) {
                        categories_by_destination.push(value.destination_shortname);
                        series_by_destination_target.push(value.target_sales);
                        series_by_destination_backorder.push(value.target_backorder);
                        series_by_destination_actual.push(value.actual_sales_st);
                        series_by_destination_percentage.push((value.actual_sales_st / (value.target_sales +
                            value
                            .target_backorder)) * 100);
                    });

                    Highcharts.chart('monitoring_st', {
                        chart: {
                            type: 'column',
                            backgroundColor: null,
                        },
                        credits: {
                            enabled: false
                        },
                        title: {
                            text: 'Shipment Achievement Per Destination',
                        },
                        xAxis: {
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: categories_by_destination,
                            crosshair: true,
                            labels: {
                                style: {
                                    fontSize: '14px',
                                }
                            }
                        },
                        yAxis: [{
                                min: 0,
                                title: {
                                    text: 'Amount'
                                },
                                stackLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.total / 1000).toFixed(0) + 'K';
                                    },
                                    style: {
                                        color: 'white',
                                        fontSize: '14px',
                                    }
                                }
                            },
                            {
                                title: {
                                    text: 'Percentage',
                                },
                                min: 0,
                                max: 100,
                                opposite: true
                            }
                        ],
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            pointFormat: '{series.name}: <b>{point.y:.1f} </b><br>'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.05,
                                groupPadding: 0.1,
                                borderWidth: 0,
                                stacking: 'normal',
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalDetail(this.category);
                                        }
                                    }
                                }
                            },
                            line: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.2f}%',
                                    style: {
                                        color: 'white',
                                        fontSize: '14px',
                                    }
                                },
                            },
                        },
                        series: [{
                            name: 'Target Sales',
                            data: series_by_destination_target,
                            yAxis: 0,
                            stack: 'Target',
                            color: '#605ca8',
                            minPointLength: 5,
                        }, {
                            name: 'Backorder',
                            data: series_by_destination_backorder,
                            yAxis: 0,
                            stack: 'Target',
                            color: '#f29b12',
                        }, {
                            name: 'Actual Sales',
                            data: series_by_destination_actual,
                            yAxis: 0,
                            stack: 'Actual',
                            color: '#ccff90',
                            minPointLength: 5,
                        }, {
                            type: 'line',
                            name: 'Percentage',
                            data: series_by_destination_percentage,
                            yAxis: 1,
                            color: '#f45b5b',
                            marker: {
                                lineWidth: 2,
                                lineColor: '#f45b5b',
                                fillColor: 'white'
                            }
                        }]
                    });

                    categories_by_destination = [];
                    series_by_destination_target = [];
                    series_by_destination_backorder = [];
                    series_by_destination_actual = [];
                    series_by_destination_percentage = [];

                    $.each(result_by_destination, function(key, value) {
                        categories_by_destination.push(value.destination_shortname);
                        series_by_destination_target.push(value.target_sales);
                        series_by_destination_backorder.push(value.target_backorder);
                        series_by_destination_actual.push(value.actual_sales_bl);
                        series_by_destination_percentage.push((value.actual_sales_bl / (value.target_sales +
                            value
                            .target_backorder)) * 100);
                    });

                    Highcharts.chart('monitoring_bl', {
                        chart: {
                            type: 'column',
                            backgroundColor: null,
                        },
                        credits: {
                            enabled: false
                        },
                        title: {
                            text: 'Sales Achievement Per Destination',
                        },
                        xAxis: {
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: categories_by_destination,
                            crosshair: true,
                            labels: {
                                style: {
                                    fontSize: '14px',
                                }
                            }
                        },
                        yAxis: [{
                                min: 0,
                                title: {
                                    text: 'Amount'
                                },
                                stackLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.total / 1000).toFixed(0) + 'K';
                                    },
                                    style: {
                                        color: 'white',
                                        fontSize: '14px',
                                    }
                                }
                            },
                            {
                                title: {
                                    text: 'Percentage',
                                },
                                min: 0,
                                max: 100,
                                opposite: true
                            }
                        ],
                        tooltip: {
                            shared: true,
                            useHTML: true,
                            pointFormat: '{series.name}: <b>{point.y:.1f} </b><br>'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.05,
                                groupPadding: 0.1,
                                borderWidth: 0,
                                stacking: 'normal',
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalDetail(this.category);
                                        }
                                    }
                                }
                            },
                            line: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.2f}%',
                                    style: {
                                        color: 'white',
                                        fontSize: '14px',
                                    }
                                }
                            },
                        },
                        series: [{
                            name: 'Target Sales',
                            data: series_by_destination_target,
                            yAxis: 0,
                            stack: 'Target',
                            color: '#605ca8',
                            minPointLength: 5,
                        }, {
                            name: 'Backorder',
                            data: series_by_destination_backorder,
                            yAxis: 0,
                            stack: 'Target',
                            color: '#f29b12',
                        }, {
                            name: 'Actual Sales',
                            data: series_by_destination_actual,
                            yAxis: 0,
                            stack: 'Actual',
                            color: '#ccff90',
                            minPointLength: 5,
                        }, {
                            type: 'line',
                            name: 'Percentage',
                            data: series_by_destination_percentage,
                            yAxis: 1,
                            color: '#f45b5b',
                            marker: {
                                lineWidth: 2,
                                lineColor: '#f45b5b',
                                fillColor: 'white'
                            }
                        }]
                    });

                    $('#loading').hide();
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
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
