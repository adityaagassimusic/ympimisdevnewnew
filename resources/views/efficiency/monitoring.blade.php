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
            border: 0.5px solid black;
            height: 40px !important;
        }

        tbody>tr>td {
            padding-right: 3px;
            padding-left: 3px;
            border: 0.5px solid black;
            height: 40px !important;
        }

        tfoot>tr>th {
            padding-right: 3px;
            padding-left: 3px;
            border: 0.5px solid black;
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
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-5" id="eff_monitoring_monthly" style="padding: 0;">
                    </div>
                    <div class="col-xs-7" id="eff_monitoring_daily" style="padding: 0;">
                    </div>
                </div>
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
                    {{-- <div class="col-xs-6" style="margin-bottom: 10px;">
                        <table id="tableDetailResume"
                            style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="width: 1%; text-align: center;">Remark</th>
                                    <th style="width: 1%; text-align: center;">Tanggal</th>
                                    <th style="width: 1%; text-align: center;">Input</th>
                                    <th style="width: 1%; text-align: center;">Output</th>
                                    <th style="width: 1%; text-align: center;">Persentase</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailResumeBody">
                            </tbody>
                        </table>
                    </div> --}}
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-6">
                                <table id="tableDetailInput"
                                    style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
                                    <thead style="background-color: #2b908f; color: white;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 3%; text-align: center;">Remark</th>
                                            <th style="width: 1%; text-align: center;">ID</th>
                                            <th style="width: 4%; text-align: left;">Nama</th>
                                            <th style="width: 1%; text-align: right;">Tanggal</th>
                                            <th style="width: 1%; text-align: right;">Kehadiran<br>(Jam)</th>
                                            <th style="width: 1%; text-align: right;">Lembur<br>(Jam)</th>
                                            <th style="width: 1%; text-align: right;">Pengalihan<br>(Jam)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableDetailInputBody">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="background-color: #fffcb7; text-align: center;" colspan="5">Total
                                            </th>
                                            <th style="background-color: #fffcb7; text-align: right;"></th>
                                            <th style="background-color: #fffcb7; text-align: right;"></th>
                                            <th style="background-color: #fffcb7; text-align: right;"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-xs-6">
                                <table id="tableDetailOutput"
                                    style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
                                    <thead style="background-color: #90ee7e; color: black;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 3%; text-align: center;">Remark</th>
                                            <th style="width: 1%; text-align: center;">Material</th>
                                            <th style="width: 4%; text-align: left;">Deskripsi</th>
                                            <th style="width: 1%; text-align: right;">Std Time<br>(Menit)</th>
                                            <th style="width: 1%; text-align: right;">Tanggal</th>
                                            <th style="width: 1%; text-align: right;">Jumlah</th>
                                            <th style="width: 1%; text-align: right;">Output<br>(Jam)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableDetailOutputBody">
                                    </tbody>
                                    <tfoot>
                                        <th style="background-color: #fffcb7; text-align: center;" colspan="6">Total
                                        </th>
                                        <th style="background-color: #fffcb7; text-align: right;"></th>
                                        <th style="background-color: #fffcb7; text-align: right;"></th>
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
        var resume_yearly = [];
        var resume_monthly = [];
        var resume_daily = [];
        var remarks = [];

        function modalDetail(type, remark, date) {
            // $('#loading').show();
            var period = date;
            var data = {
                type: type,
                remark: remark,
                date: date,
                period: period,
            }
            $.get('{{ url('fetch/efficiency/monitoring_modal') }}', data, function(result, status, xhr) {
                if (result.status) {
                    // var tableDetailResumeBody = "";
                    var tableDetailInputBody = "";
                    var tableDetailOutputBody = "";
                    var cnt_input = 0;
                    var cnt_output = 0;
                    // $('#tableDetailResumeBody').html("");
                    $('#tableDetailInputBody').html("");
                    $('#tableDetailOutputBody').html("");

                    // $('#tableDetailResume').DataTable().clear();
                    // $('#tableDetailResume').DataTable().destroy();
                    $('#tableDetailInput').DataTable().clear();
                    $('#tableDetailInput').DataTable().destroy();
                    $('#tableDetailOutput').DataTable().clear();
                    $('#tableDetailOutput').DataTable().destroy();

                    $.each(result.inputs, function(key, value) {
                        cnt_input += 1;
                        tableDetailInputBody += '<tr>';
                        tableDetailInputBody += '<td style="width: 0.1%; text-align: center;">' +
                            cnt_input +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 3%; text-align: center;">' + value
                            .remark +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 1%; text-align: center;">' + value
                            .employee_id +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 4%; text-align: left;">' + value
                            .employee_name +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 1%; text-align: right;">' + value
                            .result_date +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 1%; text-align: right;">' + value
                            .attendance.toFixed(2) +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 1%; text-align: right;">' + value
                            .overtime.toFixed(2) +
                            '</td>';
                        tableDetailInputBody += '<td style="width: 1%; text-align: right;">' + value
                            .diversion.toFixed(2) +
                            '</td>';
                        tableDetailInputBody += '</tr>';
                    });

                    $.each(result.outputs, function(key, value) {
                        cnt_output += 1;
                        tableDetailOutputBody += '<tr>';
                        tableDetailOutputBody += '<td style="width: 0.1%; text-align: center;">' +
                            cnt_output +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 3%; text-align: center;">' + value
                            .remark +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 1%; text-align: center;">' + value
                            .material_number +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 4%; text-align: left;">' + value
                            .material_description +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 1%; text-align: right;">' + value
                            .standard_time +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 1%; text-align: right;">' + value
                            .result_date +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 1%; text-align: right;">' + value
                            .quantity +
                            '</td>';
                        tableDetailOutputBody += '<td style="width: 1%; text-align: right;">' + value
                            .output.toFixed(2) +
                            '</td>';
                        tableDetailOutputBody += '</tr>';
                    });

                    $('#tableDetailInputBody').append(tableDetailInputBody);
                    $('#tableDetailOutputBody').append(tableDetailOutputBody);

                    $('#tableDetailInput').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, 100, -1],
                            ['25 rows', '50 rows', '100 rows', 'Show all']
                        ],
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
                        "bAutoWidth": true,
                        "processing": true,
                        "ordering": true,
                        "paging": true,
                        "footerCallback": function(tfoot, data, start, end, display) {
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };
                            var api = this.api();

                            var sisa_sub_assy = api.column(5).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(5).footer()).html(sisa_sub_assy.toLocaleString());

                            var stamp = api.column(6).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(6).footer()).html(stamp.toLocaleString());

                            var stamp_kd = api.column(7).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(7).footer()).html(stamp_kd.toLocaleString());
                        }
                    });

                    $('#tableDetailOutput').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, 100, -1],
                            ['25 rows', '50 rows', '100 rows', 'Show all']
                        ],
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
                        "bAutoWidth": true,
                        "processing": true,
                        "ordering": true,
                        "paging": true,
                        "footerCallback": function(tfoot, data, start, end, display) {
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };
                            var api = this.api();

                            var stamp = api.column(6).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(6).footer()).html(stamp.toLocaleString());

                            var stamp_kd = api.column(7).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(7).footer()).html(stamp_kd.toLocaleString());
                        }
                    });

                    $('#loading').hide();
                    $('#modalDetail').modal('show');
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }
            });
        }

        function fetchData() {
            $('#loading').show();
            var period = $('#filterPeriod').val();
            var data = {
                period: period
            }
            $.get('{{ url('fetch/efficiency/monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#title_text').text('Efficiency Data ' + result.fiscal_year + ' (' + result
                        .period_title +
                        ')');
                    resume_yearly = result.resume_yearly;
                    resume_monthly = result.resume_monthly;
                    resume_daily = result.resume_daily;
                    remarks = result.remarks;
                    $('#eff_monitoring_monthly').html("");
                    $('#eff_monitoring_daily').html("");

                    $.each(remarks, function(key, value) {
                        var eff_div_monthly = "";
                        eff_div_monthly =
                            '<div class="col-xs-12" style="padding:0; height:250px; color: white;" id="eff_month_' +
                            value + '">' + value + '</div><hr>';

                        $('#eff_monitoring_monthly').append(eff_div_monthly);

                        var max_height_percentage = 120;
                        var categories = [];
                        var series_input = [];
                        var series_output = [];
                        var series_percentage = [];

                        for (var i = 0; i < resume_monthly.length; i++) {
                            if (resume_monthly[i].remark == value) {

                                categories.push(resume_monthly[i].categories);
                                series_input.push(resume_monthly[i].total_input);
                                series_output.push(resume_monthly[i].total_output);
                                series_percentage.push(resume_monthly[i].percentage);

                                if (resume_monthly[i].percentage >= 120 && resume_monthly[i]
                                    .percentage <=
                                    200) {
                                    max_height_percentage = Math.round(resume_monthly[i]
                                        .percentage);
                                }
                            }
                        }

                        Highcharts.chart('eff_month_' + value, {
                            chart: {
                                type: 'column',
                                backgroundColor: null
                            },
                            title: {
                                text: '<a href="{{ url('index/efficiency/monitoring_detail') }}/' +
                                    value + '" target="blank_">' +
                                    value + '</a>'
                            },
                            credits: {
                                enabled: false
                            },
                            xAxis: {
                                tickInterval: 1,
                                gridLineWidth: 1,
                                categories: categories,
                                crosshair: true,
                            },
                            yAxis: [{
                                min: 0,
                                title: {
                                    text: null
                                }
                            }, {
                                min: 0,
                                max: max_height_percentage,
                                title: {
                                    text: null
                                },
                                labels: {
                                    format: '{value}%',
                                },
                                opposite: true
                            }],
                            tooltip: {
                                shared: true,
                                useHTML: true
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.05,
                                    groupPadding: 0.1,
                                    borderWidth: 0
                                },
                                series: {
                                    minPointLength: 0,
                                    cursor: 'pointer',
                                    point: {
                                        events: {
                                            click: function() {
                                                modalDetail('monthly', value, this
                                                    .category);
                                            }
                                        }
                                    },
                                },
                            },
                            series: [{
                                name: 'Input Hour(s)',
                                data: series_input

                            }, {
                                name: 'Output Hour(s)',
                                data: series_output
                            }, {
                                name: 'Efficiency %',
                                type: 'spline',
                                yAxis: 1,
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return Highcharts.numberFormat(this
                                            .y, 1) + '%';
                                    }
                                },
                                data: series_percentage
                            }]
                        });

                        var eff_div_daily = "";
                        eff_div_daily =
                            '<div class="col-xs-12" style="padding:0; height:250px; color: white;" id="eff_daily_' +
                            value + '">' + value + '</div><hr>';
                        $('#eff_monitoring_daily').append(eff_div_daily);

                        var max_height_percentage = 120;
                        var categories = [];
                        var series_input = [];
                        var series_output = [];
                        var series_percentage = [];

                        for (var i = 0; i < resume_daily.length; i++) {
                            if (resume_daily[i].remark == value) {

                                categories.push(resume_daily[i].categories);
                                series_input.push(resume_daily[i].total_input);
                                series_output.push(resume_daily[i].total_output);
                                series_percentage.push(resume_daily[i].percentage);

                                if (resume_daily[i].percentage >= 120 && resume_daily[i]
                                    .percentage <=
                                    200) {
                                    max_height_percentage = Math.round(resume_daily[i]
                                        .percentage);
                                }
                            }
                        }

                        Highcharts.chart('eff_daily_' + value, {
                            chart: {
                                type: 'column',
                                backgroundColor: null
                            },
                            title: {
                                text: '<a href="{{ url('index/efficiency/monitoring_detail') }}/' +
                                    value + '" target="blank_">' +
                                    value + '</a>'
                            },
                            credits: {
                                enabled: false
                            },
                            xAxis: {
                                tickInterval: 1,
                                gridLineWidth: 1,
                                categories: categories,
                                crosshair: true,
                            },
                            yAxis: [{
                                min: 0,
                                title: {
                                    text: null
                                }
                            }, {
                                min: 0,
                                max: max_height_percentage,
                                title: {
                                    text: null
                                },
                                labels: {
                                    format: '{value}%',
                                },
                                opposite: true
                            }],
                            tooltip: {
                                shared: true,
                                useHTML: true
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.05,
                                    groupPadding: 0.1,
                                    borderWidth: 0
                                },
                                series: {
                                    minPointLength: 0,
                                    cursor: 'pointer',
                                    point: {
                                        events: {
                                            click: function() {
                                                modalDetail('daily', value, this
                                                    .category);
                                            }
                                        }
                                    },
                                },
                            },
                            series: [{
                                name: 'Input Hour(s)',
                                data: series_input

                            }, {
                                name: 'Output Hour(s)',
                                data: series_output
                            }, {
                                name: 'Efficiency %',
                                type: 'spline',
                                yAxis: 1,
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return Highcharts.numberFormat(this
                                            .y, 1) + '%';
                                    }
                                },
                                data: series_percentage
                            }]
                        });
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
